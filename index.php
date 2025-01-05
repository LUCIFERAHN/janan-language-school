<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌟 آموزشگاه زبان جانان</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* ✅ تنظیمات عمومی */
        body {
            direction: rtl;
            text-align: right;
        }

        /* ✅ Navbar */
        .navbar {
            background: linear-gradient(to right, #17a2b8, #117a8b);
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
            color: #f8f9fa !important;
        }

        /* ✅ Header */
        .header {
            background: url('assets/images/background3.jpg') no-repeat center center/cover;
            height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            background-color: #17a2b8;
        }

        /* ✅ متن هدر */
        .header-content {
            margin-top: -100px;
            /* تغییر این مقدار برای جابجایی بیشتر یا کمتر */
        }


        .header h1 {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .header p {
            font-size: 1.2rem;
        }

        /* ✅ درباره ما */
        .about-section {
            color: #fff;
            /* رنگ متن سفید برای خوانایی بهتر */
            padding: 50px 20px;
            text-align: center;
            padding: 50px 0;
            background: linear-gradient(to right, rgba(68, 0, 255, 0.8), rgba(166, 14, 113, 0.8));
        }

        .about-section h2 {
            color: #17a2b8;
        }

        /* ✅ استایل بخش ویژگی‌ها */
        .features-section {
            background: linear-gradient(to right, rgba(0, 123, 255, 0.8), rgba(23, 162, 184, 0.8));
            color: #fff;
            padding: 50px 20px;
            text-align: center;
        }

        .feature-card {
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #17a2b8;
        }

        /* ✅ Footer */
        .footer {
            background: #343a40;
            color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
        }

        .footer a {
            color: #17a2b8;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* ✅ دکمه‌ها */
        .btn-custom {
            background-color: #17a2b8;
            color: white;
            border-radius: 20px;
        }

        .btn-custom:hover {
            background-color: #117a8b;
        }
    </style>
</head>

<body>

    <!-- ✅ Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">🌟 آموزشگاه زبان جانان</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="contact.php">تماس با ما</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">ویژگی‌ها</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">ورود</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ✅ Header -->
    <header class="header">
        <div class="header-content">
            <h1>🌟 به آموزشگاه زبان جانان خوش آمدید!</h1>
            <p>آموزش زبان انگلیسی با بهترین کیفیت و اساتید مجرب</p>
            <!-- <a href="login.php" class="btn btn-custom mt-3">ورود به حساب کاربری</a> -->
        </div>
    </header>


    <!-- ✅ درباره ما -->
    <section id="about" class="about-section text-center">
        <div class="container">
            <h2>📚 درباره آموزشگاه ما</h2>
            <p>آموزشگاه زبان جانان با بهره‌گیری از بهترین متدهای آموزشی و اساتید مجرب، تجربه‌ای متفاوت از یادگیری زبان انگلیسی را برای شما فراهم می‌آورد.</p>
        </div>
    </section>

    <!-- ✅ ویژگی‌ها -->
    <section id="features" class="features-section text-center">
        <div class="container">
            <h2>✨ ویژگی‌های ما</h2>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card feature-card p-4">
                        <div class="feature-icon">📅</div>
                        <h5>برنامه‌های منظم</h5>
                        <p>کلاس‌ها و برنامه‌های منظم آموزشی برای بهبود مهارت‌های زبان‌آموزان.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card p-4">
                        <div class="feature-icon">👩‍🏫</div>
                        <h5>اساتید مجرب</h5>
                        <p>اساتید حرفه‌ای و کارآزموده برای هر دوره آموزشی.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card p-4">
                        <div class="feature-icon">💻</div>
                        <h5>پشتیبانی آنلاین</h5>
                        <p>پشتیبانی آنلاین برای پاسخگویی به سوالات زبان‌آموزان.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ✅ Footer -->
    <footer class="footer">
        <p>© 2024 آموزشگاه زبان جانان | طراحی‌شده با ❤️</p>
        <p><a href="contact.php">تماس با ما</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>