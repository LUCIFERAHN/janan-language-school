<?php
session_start();
include '../config.php';

// ✅ بررسی دسترسی دانشجو
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

$student_id = $_SESSION['user_id'];

// ✅ دریافت دوره دانشجو
$stmt = $conn->prepare("
    SELECT course_id 
    FROM users 
    WHERE id = ? AND role = 'student'
");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$course_id = $student['course_id'] ?? null;
$stmt->close();

// ✅ دریافت برنامه هفتگی مرتبط با دوره دانشجو
$schedules = [];
if ($course_id) {
    $stmt = $conn->prepare("
        SELECT s.day_of_week, s.start_time, s.end_time, c.course_name, u.full_name AS teacher_name
        FROM schedules s
        JOIN courses c ON s.course_id = c.id
        JOIN users u ON s.teacher_id = u.id
        WHERE s.course_id = ?
    ");
    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $schedules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>📅 برنامه هفتگی</title>
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

        .schedule-container {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table thead {
            background-color: #4a69bd;
            color: #ffffff;
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
        <h2 class="text-center mb-4">📅 برنامه هفتگی شما</h2>

        <?php if ($course_id && !empty($schedules)): ?>
            <div class="schedule-container">
                <h4 class="mb-3">📚 دوره: <?= htmlspecialchars($schedules[0]['course_name'] ?? 'نامشخص') ?></h4>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>📅 روز هفته</th>
                            <th>🕒 ساعت شروع</th>
                            <th>🕒 ساعت پایان</th>
                            <th>👨‍🏫 استاد</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?= htmlspecialchars($schedule['day_of_week']) ?></td>
                                <td><?= htmlspecialchars($schedule['start_time']) ?></td>
                                <td><?= htmlspecialchars($schedule['end_time']) ?></td>
                                <td><?= htmlspecialchars($schedule['teacher_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">⛔ هنوز برنامه‌ای برای دوره شما ثبت نشده است.</div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>