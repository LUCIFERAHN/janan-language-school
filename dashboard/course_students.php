<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';

// بررسی دسترسی کاربر
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// بررسی دریافت شناسه دوره
if (!isset($_GET['course_id'])) {
    header("Location: courses.php");
    exit();
}

$course_id = intval($_GET['course_id']);
$message = '';

// دریافت اطلاعات دوره
$stmt = $conn->prepare("SELECT course_name FROM courses WHERE id = ?");
$stmt->bind_param('i', $course_id);
$stmt->execute();
$course_result = $stmt->get_result();

if ($course_result->num_rows === 0) {
    $message = '<div class="alert alert-danger">❌ دوره مورد نظر یافت نشد.</div>';
} else {
    $course = $course_result->fetch_assoc();
}

// دریافت لیست دانشجویان این دوره
$stmt = $conn->prepare("
    SELECT full_name, phone 
    FROM users 
    WHERE course_id = ? AND role = 'student'
    ORDER BY full_name COLLATE utf8mb4_persian_ci ASC
");

$stmt->bind_param('i', $course_id);
$stmt->execute();
$students_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👥 لیست دانشجویان دوره</title>
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
        h1 {
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .student-list {
            margin-top: 30px;
        }
        .student-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .student-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
<header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="student.php">پنل دانشجو</a>
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
        <h1>👥 لیست دانشجویان دوره: <?= htmlspecialchars($course['course_name']) ?></h1>
        <?= $message ?>

        <?php if ($students_result->num_rows > 0): ?>
            <div class="student-list">
                <?php while ($student = $students_result->fetch_assoc()): ?>
                    <div class="student-item">
                        <strong>👤 نام دانشجو:</strong> <?= htmlspecialchars($student['full_name']) ?><br>
                        <strong>📞 شماره تماس:</strong> <?= htmlspecialchars($student['phone']) ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">❗ هیچ دانشجویی در این دوره ثبت‌نام نکرده است.</div>
        <?php endif; ?>

        <a href="courses.php" class="btn btn-secondary mt-4">↩️ بازگشت به مدیریت دوره‌ها</a>
    </div>
    <footer class="bg-dark text-white text-center py-3 mt-4">
        <script>
            function confirmNavigation(message) {
                return confirm(message);
            }
        </script>
        <div class="container">
            <p>&copy; 2024 آموزشگاه زبان جانان. تمامی حقوق محفوظ است.</p>
        </div>
        <!-- JavaScript (Bootstrap) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Custom JS -->
        <script src="../assets/js/script.js"></script>
        <!-- Confirmation Script -->

    </footer>
</body>
</html>
