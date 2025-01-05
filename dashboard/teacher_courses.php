<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';

// بررسی دسترسی کاربر
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$message = '';

// دریافت لیست دوره‌های استاد
$stmt = $conn->prepare("
    SELECT id, course_name, start_date, end_date 
    FROM courses 
    WHERE teacher_id = (SELECT id FROM teachers WHERE user_id = ?)
");
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📚 لیست دوره‌ها</title>
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
        table th, table td {
            text-align: center;
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
        <h1 class="text-center">📚 لیست دوره‌های من</h1>
        <?= $message ?>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered table-striped mt-4">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>📚 نام دوره</th>
                        <th>📅 تاریخ شروع</th>
                        <th>📅 تاریخ پایان</th>
                        <th>👥 لیست دانشجویان</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($course = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $course['id'] ?></td>
                            <td><?= htmlspecialchars($course['course_name']) ?></td>
                            <td><?= htmlspecialchars($course['start_date']) ?></td>
                            <td><?= htmlspecialchars($course['end_date']) ?></td>
                            <td>
                                <a href="teacher_course_students.php?course_id=<?= $course['id'] ?>" 
                                   class="btn btn-info btn-sm">👥 مشاهده دانشجویان</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning text-center mt-4">❗ شما هنوز دوره‌ای برای تدریس ندارید.</div>
        <?php endif; ?>
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

    </footer></body>
</html>
