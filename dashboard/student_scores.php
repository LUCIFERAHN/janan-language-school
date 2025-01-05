<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// ✅ بررسی ورود دانشجو
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

include '../config.php';

// ✅ دریافت نمرات دانشجو
$student_id = $_SESSION['user_id'];
$grades_query = "
    SELECT grades.grade, grades.comment, grades.created_at,
           courses.course_name, users.full_name AS teacher_name
    FROM grades
    JOIN courses ON grades.course_id = courses.id
    JOIN users ON grades.teacher_id = users.id
    WHERE grades.student_id = '$student_id'
    ORDER BY grades.created_at DESC
";

$result = mysqli_query($conn, $grades_query);

if (!$result) {
    die('❌ خطا در دریافت نمرات کلاسی: ' . mysqli_error($conn));
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

        .grade-card {
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .grade-card .card-header {
            background: linear-gradient(to right, #28a745, #218838);
            color: white;
        }

        .grade-card .card-body {
            background-color: #f8f9fc;
        }

        .grade-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
        }

        .comment-text {
            font-size: 0.9rem;
            color: #6c757d;
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

        <h2 class="text-center my-4">📊 نمرات کلاسی شما</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="row">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-6">
                        <div class="card grade-card">
                            <div class="card-header">
                                🏫 <?= htmlspecialchars($row['course_name']) ?>
                            </div>
                            <div class="card-body">
                                <p><strong>👨‍🏫 استاد:</strong> <?= htmlspecialchars($row['teacher_name']) ?></p>
                                <p><strong>📝 نمره:</strong> <span class="grade-value"><?= htmlspecialchars($row['grade']) ?></span></p>
                                <?php if ($row['comment']): ?>
                                    <p class="comment-text">💬 توضیحات: <?= htmlspecialchars($row['comment']) ?></p>
                                <?php endif; ?>
                                <p><strong>📅 تاریخ ثبت:</strong> <?= htmlspecialchars($row['created_at']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                ⛔ هنوز نمره‌ای برای شما ثبت نشده است.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>