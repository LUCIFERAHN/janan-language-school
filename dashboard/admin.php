<?php
session_start();

// ✅ بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// مسیر داشبورد بر اساس نقش کاربری
$dashboard_link = match ($_SESSION['role']) {
    'admin' => 'admin.php',
    'teacher' => 'teacher.php',
    'student' => 'student.php',
    default => '../index.php'
};

include '../config.php';
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛡️ داشبورد ادمین</title>
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

        /* ✅ نوار منو (Sidebar) */
        .sidebar {
            background: linear-gradient(to bottom, #4e73df, #224abe);
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            right: 0;
            width: 260px;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: white;
            margin: 5px 0;
            font-weight: bold;
        }

        .sidebar .nav-link:hover {
            background-color: #2e59d9;
            border-radius: 5px;
        }

        .sidebar .nav-link.active {
            background-color: #2e59d9;
        }

        .sidebar .sidebar-header {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }

        /* ✅ محتوای اصلی (Main Content) */
        .main-content {
            margin-right: 260px;
            padding: 20px;
        }

        .main-content .card-custom {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .main-content .card-header-custom {
            background: linear-gradient(to right, #6f42c1, #4e2a84);
            color: white;
        }

        /* ✅ فوتر */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <!-- ✅ نوار منو (Sidebar) -->
    <div class="sidebar">
        <div class="sidebar-header">
            🛡️ پنل مدیریت
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="admin_dashboard.php">🏠 داشبورد</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_create_user.php">👤 ایجاد کاربر</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="courses.php">📚 مدیریت دوره‌ها</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="exams.php">📝 مدیریت آزمون‌ها</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="certificates.php">📜 صدور مدارک</a>
            </li>
            <li>
                <a href="announcements.php" class="nav-link">📢 مدیریت اطلاعیه‌ها</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="students.php">👤 مدیریت دانشجویان</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="chat.php">💬 پشتیبانی</a>
            </li>
            <li>
                <a href="feedback.php" class="nav-link">💬 مدیریت نظرات</a>
            </li>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php" onclick="return confirmNavigation('آیا مطمئن هستید که می‌خواهید خارج شوید؟')">🚪 خروج</a>
            </li>
        </ul>
    </div>

    <!-- ✅ محتوای اصلی (Main Content) -->
    <div class="main-content">
        <h2 class="mb-4">🏠 داشبورد مدیریت</h2>

        <!-- ✅ پیام خوش‌آمدگویی -->
        <div class="alert alert-info text-center">
            🎯 به پنل مدیریت خوش آمدید. از منوی سمت راست برای مدیریت بخش‌های مختلف استفاده کنید.
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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>