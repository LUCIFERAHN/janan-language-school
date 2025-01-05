<?php
session_start();
include '../config.php';

// ✅ بررسی نقش ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// ✅ دریافت آمار
// تعداد کاربران با نقش‌های مختلف
$user_counts = [
    'students' => 0,
    'teachers' => 0,
    'admins' => 0
];
$stmt = $conn->prepare("SELECT role, COUNT(*) as count FROM users GROUP BY role");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if ($row['role'] === 'student') $user_counts['students'] = $row['count'];
    if ($row['role'] === 'teacher') $user_counts['teachers'] = $row['count'];
    if ($row['role'] === 'admin') $user_counts['admins'] = $row['count'];
}
$stmt->close();

// تعداد دوره‌ها
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM courses");
$stmt->execute();
$result = $stmt->get_result();
$courses_count = $result->fetch_assoc()['count'];
$stmt->close();

// تعداد نمرات ثبت‌شده
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM grades");
$stmt->execute();
$result = $stmt->get_result();
$grades_count = $result->fetch_assoc()['count'];
$stmt->close();

?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 داشبورد ادمین</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            direction: rtl;
            text-align: right;
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: #fff;
        }
        .card {
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .chart-container {
            margin-top: 50px;
        }
    </style>
</head>
<body>
<header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="admin.php">پنل ادمین</a>
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
<div class="container mt-5">
    <h1 class="text-center mb-5">📊 داشبورد مدیریت</h1>

    <div class="row text-center">
        <div class="col-md-3">
            <div class="card bg-primary text-white p-4">
                <h5>👩‍🎓 تعداد دانشجویان</h5>
                <h3><?= $user_counts['students'] ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white p-4">
                <h5>👨‍🏫 تعداد اساتید</h5>
                <h3><?= $user_counts['teachers'] ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white p-4">
                <h5>🛡️ تعداد مدیران</h5>
                <h3><?= $user_counts['admins'] ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white p-4">
                <h5>📚 تعداد دوره‌ها</h5>
                <h3><?= $courses_count ?></h3>
            </div>
        </div>
    </div>

    <div class="row chart-container">
        <div class="col-md-6">
            <div class="card p-4">
                <h5 class="text-center">📊 نمودار دایره‌ای کاربران</h5>
                <canvas id="userChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4">
                <h5 class="text-center">📈 نمودار میله‌ای آمار</h5>
                <canvas id="statsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// ✅ نمودار دایره‌ای کاربران
const userChartCtx = document.getElementById('userChart').getContext('2d');
const userChart = new Chart(userChartCtx, {
    type: 'pie',
    data: {
        labels: ['دانشجویان', 'اساتید', 'مدیران'],
        datasets: [{
            data: [<?= $user_counts['students'] ?>, <?= $user_counts['teachers'] ?>, <?= $user_counts['admins'] ?>],
            backgroundColor: ['#3498db', '#2ecc71', '#f39c12']
        }]
    },
    options: {
        responsive: true
    }
});

// ✅ نمودار میله‌ای آمار
const statsChartCtx = document.getElementById('statsChart').getContext('2d');
const statsChart = new Chart(statsChartCtx, {
    type: 'bar',
    data: {
        labels: ['دانشجویان', 'اساتید', 'مدیران', 'دوره‌ها', 'نمرات'],
        datasets: [{
            label: 'تعداد',
            data: [<?= $user_counts['students'] ?>, <?= $user_counts['teachers'] ?>, <?= $user_counts['admins'] ?>, <?= $courses_count ?>, <?= $grades_count ?>],
            backgroundColor: ['#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6']
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>
