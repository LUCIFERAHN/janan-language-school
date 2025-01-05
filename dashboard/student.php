<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل دانشجو</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <!-- <link rel="stylesheet" href="../assets/css/style.css"> -->
    <style>
        /* استایل کلی برای پنل دانشجو */
        body {
            font-family: 'Tahoma', sans-serif;
            background: linear-gradient(rgb(116, 229, 241), rgb(175, 152, 240));
            margin: 0;
            padding: 0;
        }

        /* ✅ Header */
        .header {
            background: url('../assets/images/background1.jpg') no-repeat center center/cover;
            height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            background-color: #17a2b8;
            /* opacity: 0.8; */
        }

        /* header:hover {
            opacity: 1;
            -webkit-filter:blur(3px);
            filter:blur(3px);
        } */

        /* ✅ متن هدر */
        .header-content {
            opacity: 1;
            font-size: 50px;
            margin-top: -240px;
            /* تغییر این مقدار برای جابجایی بیشتر یا کمتر */
        }

        .header-p {
            opacity: 1;
            font-size: 23px;
        }


        /* ✅ Navbar */
        .navbar {
            background: linear-gradient(to right, rgba(74, 33, 222, 0.8), rgba(23, 162, 184, 0.8));
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white !important;
        }

        .nav-link {
            color: white !important;
        }

        .nav-link:hover {
            color: red !important;
        }

        .row {
            background: linear-gradient(to right, rgba(74, 33, 222, 0.8), rgba(23, 162, 184, 0.8));
            color: #fff;
            padding: 50px 20px;
            text-align: center;
            border-radius: 50px;
        }

        .student-panel h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
        }

        .student-panel p {
            font-size: 1rem;
            color: white;
        }

        .card {
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #34495e;
        }

        .card-text {
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        .btn-primary {
            background-color: #4a69bd;
            border: none;
        }

        .btn-primary:hover {
            background-color: #3c6382;
        }

        .btn-success {
            background-color: #78e08f;
            border: none;
        }

        .btn-success:hover {
            background-color: #60a3bc;
        }

        .btn-warning {
            background-color: #f6b93b;
            border: none;
        }

        .btn-warning:hover {
            background-color: #e58e26;
        }

        footer {
            margin-top: 30px;
            background-color: #2c3e50;
            color: white;
            padding: 10px 0;
        }

        footer p {
            margin: 0;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="#">آموزشگاه زبان جانان</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="تغییر وضعیت ناوبری">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                            <a class="nav-link" href="announcements_view.php">📢 اطلاعیه‌ها</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="chat.php">💬گفتگو</a>
                        </li>
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



    <!-- ✅ Header -->
    <header class="header">
        <div class="header-content">
            <h1>🌟 دانشجو عزیز !</h1>
            <p class="header-p">به آموزشگاه زبان جانان خوش امدید </p>
        </div>
    </header>

    <div class="row mt-5 text-center">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">📚 برنامه هفتگی</h5>
                    <p class="card-text">برنامه کلاس‌های خود را مشاهده کنید.</p>
                    <a href="student_schedule.php" class="btn btn-primary">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">📝 نمرات کلاسی</h5>
                    <p class="card-text">نمرات ثبت‌شده توسط اساتید را ببینید.</p>
                    <a href="student_scores.php" class="btn btn-primary">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">📄 سوابق تحصیلی </h5>
                    <p class="card-text"> کارنامه و مدارک خود را بررسی کنید.</p>
                    <a href="student_records.php" class="btn btn-primary">مشاهده</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5 text-center">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">💳 پرداخت آنلاین</h5>
                    <p class="card-text">پرداخت‌های خود را انجام دهید و وضعیت را بررسی کنید.</p>
                    <a href="student_payments.php" class="btn btn-success">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">📝 حضور غیاب </h5>
                    <p class="card-text">وضعیت حضور و غیاب خود را میتوانید مشاهده کنید.</p>
                    <a href="student_attendance.php" class="btn btn-primary">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">🔒 تغییر رمز عبور</h5>
                    <p class="card-text">اطلاعات ورود خود را تغییر دهید.</p>
                    <a href="user_profile.php" class="btn btn-warning">تغییر</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- footer.php -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>