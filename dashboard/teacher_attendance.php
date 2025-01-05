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

// ✅ دریافت لیست دوره‌ها برای نمایش در Dropdown
$courses_result = $conn->query("SELECT id, course_name FROM courses");
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);

// ✅ دریافت دانشجویان دوره انتخاب‌شده
$students = [];
$selected_course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : null;

if ($selected_course_id) {
    $stmt = $conn->prepare("
        SELECT u.id, u.full_name 
        FROM users u
        WHERE u.role = 'student' AND u.course_id = ?
    ");
    $stmt->bind_param('i', $selected_course_id);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// ✅ ثبت حضور و غیاب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_attendance'])) {
    $attendance_date = $_POST['attendance_date'];

    if (!empty($attendance_date)) {
        foreach ($_POST['attendance'] as $student_id => $status) {
            $stmt = $conn->prepare("
                INSERT INTO attendance (student_id, course_id, teacher_id, attendance_date, status)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE status = VALUES(status), attendance_date = VALUES(attendance_date)
            ");
            $stmt->bind_param('iiiss', $student_id, $selected_course_id, $teacher_id, $attendance_date, $status);
            $stmt->execute();
            $stmt->close();
        }
        $message = '<div class="alert alert-success">✅ وضعیت حضور و غیاب با موفقیت ثبت شد.</div>';
    } else {
        $message = '<div class="alert alert-danger">❗ تاریخ جلسه نمی‌تواند خالی باشد.</div>';
    }
}
?>


<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>📋 حضور و غیاب</title>
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
        .container {
            margin-top: 30px;
        }

        .form-container {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table thead {
            background-color: #4a69bd;
            color: #fff;
        }

        table tbody tr:hover {
            background-color: #f1f2f6;
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

    <div class="container">
        <h2 class="text-center mb-4">📋 ثبت حضور و غیاب</h2>
        <?= $message ?>

        <!-- ✅ فرم انتخاب دوره -->
        <form method="POST" class="form-container mb-4">
            <h5>📚 انتخاب دوره</h5>
            <select name="course_id" class="form-select mb-3" required onchange="this.form.submit()">
                <option value="">یک دوره انتخاب کنید</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= ($selected_course_id == $course['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($course['course_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- ✅ فرم حضور و غیاب -->
        <?php if ($selected_course_id && !empty($students)): ?>
            <form method="POST" class="form-container">
                <input type="hidden" name="course_id" value="<?= $selected_course_id ?>">
                <div class="mb-3">
                    <label class="form-label">📅 تاریخ جلسه</label>
                    <input type="date" name="attendance_date" class="form-control" required>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>👤 نام دانشجو</th>
                            <th>✅ وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['full_name']) ?></td>
                                <td>
                                    <div>
                                        <label>
                                            <input type="radio" name="attendance[<?= $student['id'] ?>]" value="present" required> حاضر
                                        </label>
                                        <label class="ms-3">
                                            <input type="radio" name="attendance[<?= $student['id'] ?>]" value="absent"> غایب
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="save_attendance" class="btn btn-success w-100">💾 ثبت حضور و غیاب</button>
            </form>
        <?php elseif ($selected_course_id): ?>
            <div class="alert alert-warning">⛔ دانشجویی برای این دوره ثبت نشده است.</div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>