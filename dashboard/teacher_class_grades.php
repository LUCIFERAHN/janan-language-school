<?php
session_start();
include '../config.php';

// ✅ بررسی نقش استاد
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}

$teacher_id = $_SESSION['user_id'];
$message = '';

// ✅ دریافت دوره‌های مرتبط با استاد
$stmt = $conn->prepare("
    SELECT id, course_name 
    FROM courses 
    WHERE teacher_id = ?
");
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$courses_result = $stmt->get_result();
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
// ✅ دریافت لیست دوره‌ها برای نمایش در Dropdown
$courses_result = $conn->query("SELECT id, course_name FROM courses");
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);

// ✅ دریافت دانشجویان مرتبط با دوره انتخاب‌شده
$students = [];
$selected_course_id = $_POST['course_id'] ?? null;
if ($selected_course_id) {
    $stmt = $conn->prepare("
        SELECT u.id, u.full_name 
        FROM users u
        WHERE u.role = 'student' AND u.course_id = ?
    ");
    $stmt->bind_param('i', $selected_course_id);
    $stmt->execute();
    $students_result = $stmt->get_result();
    $students = $students_result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}


// ✅ ثبت نمرات
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grades'])) {
    $grades = $_POST['grades'];
    $comments = $_POST['comments'];
    $session_dates = $_POST['session_dates'];

    foreach ($grades as $student_id => $grade) {
        $comment = $comments[$student_id] ?? null;
        $session_date = $session_dates[$student_id] ?? null;

        if ($session_date) {
            // بررسی وجود student_id در جدول users
            $stmt_check = $conn->prepare("
                SELECT id FROM users WHERE id = ? AND role = 'student'
            ");
            $stmt_check->bind_param('i', $student_id);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $stmt = $conn->prepare("
                    INSERT INTO grades (student_id, course_id, teacher_id, session_date, grade, comment)
                    VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        grade = VALUES(grade), 
                        comment = VALUES(comment), 
                        session_date = VALUES(session_date)
                ");
                $stmt->bind_param('iiisds', $student_id, $selected_course_id, $teacher_id, $session_date, $grade, $comment);
                $stmt->execute();
                $stmt->close();
            } else {
                $message = '<div class="alert alert-danger">❌ دانشجو با این شناسه یافت نشد.</div>';
            }

            $stmt_check->close();
        }
    }
    $message = '<div class="alert alert-success">✅ نمرات و تاریخ جلسات با موفقیت ثبت شدند.</div>';
}


?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 نمرات کلاسی</title>
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
        .grades-container {
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
        .date-input {
            width: 150px;
        }
    </style>
</head>

<body>
<header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="teacher.php">پنل استاد</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="تغییر وضعیت منو">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php" onclick="return confirmNavigation('آیا مطمئن هستید که می‌خواهید به صفحه اصلی بروید؟')">🏠 صفحه اصلی</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php" onclick="return confirmNavigation('آیا مطمئن هستید که می‌خواهید خارج شوید؟')">🚪 خروج</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

        <h2 class="text-center my-4">📊 مدیریت نمرات کلاسی</h2>
        <?= $message ?>

        <!-- ✅ فرم انتخاب دوره -->
        <div class="card card-custom mb-4">
            <div class="card-header card-header-custom">📚 انتخاب دوره</div>
            <div class="card-body">
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
            <!-- ✅ جدول ثبت نمرات -->
            <form method="POST">
                <input type="hidden" name="course_id" value="<?= $selected_course_id ?>">
                <div class="card card-custom">
                    <div class="card-header card-header-custom">📝 ثبت نمرات</div>
                    <div class="card-body">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>👤 نام دانشجو</th>
                                    <th>📅 تاریخ جلسه</th>
                                    <th>📊 نمره</th>
                                    <th>📝 توضیحات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                                        <td>
                                            <input type="date" name="session_dates[<?= $student['id'] ?>]" class="form-control date-input" required>
                                        </td>
                                        <td><input type="number" step="0.1" name="grades[<?= $student['id'] ?>]" class="form-control"></td>
                                        <td><input type="text" name="comments[<?= $student['id'] ?>]" class="form-control"></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="submit" name="save_grades" class="btn btn-success w-100">💾 ثبت نمرات</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>