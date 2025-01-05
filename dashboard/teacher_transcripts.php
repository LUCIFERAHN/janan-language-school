<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ بررسی ورود استاد
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}

include '../config.php';

$teacher_id = $_SESSION['user_id'];
$message = '';

// ✅ دریافت شناسه استاد از جدول users
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'teacher'");
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    die('<div class="alert alert-danger">❌ خطا: اطلاعات استاد یافت نشد.</div>');
}

$teacher_id_db = $teacher['id'];
// ✅ دریافت لیست دوره‌ها برای نمایش در Dropdown
$courses_result = $conn->query("SELECT id, course_name FROM courses");
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);

// ✅ دریافت دوره‌های مرتبط با استاد
$stmt = $conn->prepare("SELECT id, course_name FROM courses WHERE teacher_id = ?");
$stmt->bind_param('i', $teacher_id_db);
$stmt->execute();
$courses_result = $stmt->get_result();
$courses = [];
while ($row = $courses_result->fetch_assoc()) {
    $courses[] = $row;
}
// ✅ دریافت لیست دوره‌ها برای نمایش در Dropdown
$courses_result = $conn->query("SELECT id, course_name FROM courses");
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);
// ✅ دریافت دانشجویان دوره انتخاب‌شده
$students = [];
$selected_course_id = $_POST['course_id'] ?? null;
if ($selected_course_id) {
    $stmt = $conn->prepare("
        SELECT id, full_name 
        FROM users 
        WHERE role = 'student' AND course_id = ?
    ");
    $stmt->bind_param('i', $selected_course_id);
    $stmt->execute();
    $students_result = $stmt->get_result();
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }
}

// ✅ آپلود فایل کارنامه
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_transcript'])) {
    $student_id = intval($_POST['student_id']);
    $course_id = intval($_POST['course_id']);
    $file = $_FILES['transcript_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($file['name']);
        $file_path = "../uploads/transcripts/" . time() . "_" . $file_name;
        move_uploaded_file($file['tmp_name'], $file_path);

        $stmt = $conn->prepare("
            INSERT INTO transcripts (teacher_id, student_id, course_id, file_name, file_path)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('iiiss', $teacher_id_db, $student_id, $course_id, $file_name, $file_path);
        $stmt->execute();
        $stmt->close();

        $message = '<div class="alert alert-success">✅ کارنامه با موفقیت آپلود شد.</div>';
    } else {
        $message = '<div class="alert alert-danger">❌ خطا در آپلود فایل.</div>';
    }
}
?>


<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📥 ثبت کارنامه</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
   /* ✅ تنظیمات کلی */
   body {
            direction: rtl;
            text-align: right;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: #fff;
            font-family: 'Tahoma', sans-serif;
        }

        .transcripts-container {
            margin-top: 20px;
        }

        .card-custom {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header-custom {
            background: linear-gradient(to right, #6f42c1, #4e2a84);
            color: white;
        }
    </style>
</head>

<body>
    <div class="container transcripts-container">
        <!-- ✅ دکمه بازگشت به صفحه قبل -->
        <div class="mb-3">
            <button onclick="history.back()" class="btn btn-outline-danger">⬅️ بازگشت به صفحه قبل</button>
        </div>
        <h2 class="text-center my-4">📥 ثبت کارنامه</h2>
        <?= $message ?>

        <!-- ✅ دانلود فرم نیمه‌آماده -->
        <div class="card card-custom mb-4">
            <div class="card-header card-header-custom">📄 دانلود فرم کارنامه</div>
            <div class="card-body">
                <a href="../templates/template_transcript.pdf" class="btn btn-primary" download>
                    📥 دانلود فرم نیمه‌آماده
                </a>
            </div>
        </div>

        <!-- ✅ فرم انتخاب دوره -->
        <div class="card card-custom mb-4">
            <div class="card-header card-header-custom">📚 انتخاب دوره</div>
            <div class="card-body">
               <!-- ✅ فرم انتخاب دوره -->
               <form method="POST" class="form-container mb-4">
            <select name="course_id" class="form-select mb-3" required onchange="this.form.submit()">
                <option value="">یک دوره انتخاب کنید</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= ($selected_course_id == $course['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($course['course_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
            </div>
        </div>

        <?php if ($selected_course_id && !empty($students)): ?>
            <!-- ✅ فرم آپلود کارنامه -->
            <div class="card card-custom mb-4">
                <div class="card-header card-header-custom">📤 آپلود کارنامه</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="course_id" value="<?= $selected_course_id ?>">
                        <div class="mb-3">
                            <label class="form-label">👤 دانشجو:</label>
                            <select name="student_id" class="form-select" required>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student['id'] ?>">
                                        <?= htmlspecialchars($student['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">📤 فایل کارنامه:</label>
                            <input type="file" name="transcript_file" class="form-control" required>
                        </div>
                        <button type="submit" name="upload_transcript" class="btn btn-primary w-100">📥 آپلود کارنامه</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
