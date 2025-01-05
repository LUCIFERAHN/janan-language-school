<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}

include '../config.php';

// مسیر داشبورد بر اساس نقش کاربری
$dashboard_link = match ($_SESSION['role']) {
    'admin' => 'admin.php',
    'teacher' => 'teacher.php',
    'student' => 'student.php',
    default => '../index.php'
};



?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎓 پنل استاد</title>
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

        .background {
            background: url('../assets/images/background7.png') no-repeat center center/cover;
            /* background-repeat: no-repeat; */
            /* background-position: left; */

        }

        /* 🎨 نوار منو (Sidebar) */
        .sidebar {
            background: linear-gradient(to bottom, #4e73df, #224abe);
            color: white;
            height: 100vh;
            padding: 20px 10px;
            border-radius: 10px;
        }

        .sidebar a {
            color: white;
            margin: 5px 0;
            font-weight: bold;
        }

        .sidebar a:hover {
            background-color: #2e59d9;
            border-radius: 5px;
        }

        /* 📊 محتوای اصلی (Main Content) */
        .main-content {
            padding: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="background">
        <div class="container-fluid">
            <div class="row">
                <!-- 📚 نوار منو (Sidebar) -->
                <div class="col-md-3 sidebar">
                    <h3 class="text-center mb-4">🎓 پنل استاد</h3>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $dashboard_link ?>">🏠 داشبورد</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="teacher_schedule.php">📅 برنامه هفتگی</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="teacher_active_courses.php">📚 دوره‌های فعال</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="teacher_attendance.php">📋 حضور و غیاب</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="teacher_class_grades.php">📝 نمرات کلاسی</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="teacher_transcripts.php">📥 ثبت کارنامه</a>
                        </li>
                        <li class="nav-item">
                            <a href="user_profile.php" class="nav-link">🔐 تغییر اطلاعات کاربری</a>
                        </li>
                        <li>
                            <a href="announcements.php" class="nav-link">📢 مدیریت اطلاعیه‌ها</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="chat.php">💬 گفتگو</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php" onclick="return confirmNavigation('آیا مطمئن هستید که می‌خواهید خارج شوید؟')">🚪 خروج</a>
                        </li>
                    </ul>
                </div>

                <!-- 📊 محتوای اصلی (Main Content) -->
                <div class="col-md-9 main-content">
                    <h2 class="mb-4">🎓 خوش آمدید، استاد عزیز!</h2>
                    <div class="alert alert-info">
                        از منوی سمت راست برای دسترسی به بخش‌های مختلف استفاده کنید.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>