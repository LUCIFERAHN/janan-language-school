<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

$message = '';

// ✅ ذخیره نظر در پایگاه داده
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message_text = trim($_POST['message']);

    if ($name && $email && $message_text) {
        $stmt = $conn->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $message_text);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">✅ نظر شما با موفقیت ثبت شد. از بازخورد شما سپاسگزاریم!</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در ثبت نظر. لطفاً دوباره تلاش کنید.</div>';
        }

        $stmt->close();
    } else {
        $message = '<div class="alert alert-warning">❗ لطفاً تمام فیلدها را پر کنید.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📞 تماس با ما</title>
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

        /* ✅ بخش تماس */
        .contact-container {
            margin-top: 30px;
        }

        .card-custom {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header-custom {
            background: linear-gradient(to right, #17a2b8, #117a8b);
            color: white;
            font-size: 1.1rem;
        }

        .btn-primary-custom {
            background-color: #17a2b8;
            border: none;
        }

        .btn-primary-custom:hover {
            background-color: #117a8b;
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
    </style>
</head>
<body>

    <!-- ✅ Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🌟 آموزشگاه زبان جانان</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">صفحه اصلی</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php"> ورود</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contact.php">تماس با ما</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ✅ بخش تماس -->
    <div class="container contact-container">
        <h2 class="text-center my-4">📞 تماس با ما</h2>
        <?= $message ?>

        <div class="row">
            <!-- اطلاعات تماس -->
            <div class="col-md-6">
                <div class="card card-custom mb-4">
                    <div class="card-header card-header-custom">📍 اطلاعات تماس</div>
                    <div class="card-body">
                        <p><strong>📞 شماره تماس:</strong> 09961108170</p>
                        <p><strong>📧 ایمیل:</strong> <a href="mailto:scottmccall466@yahoo.com">scottmccall466@yahoo.com</a></p>
                        <p><strong>📍 آدرس:</strong> میناب، دانشگاه ملی مهارت</p>
                    </div>
                </div>
            </div>

            <!-- فرم نظر سنجی -->
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header card-header-custom">📝 ارسال نظر</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">👤 نام:</label>
                                <input type="text" name="name" class="form-control" placeholder="نام شما" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">📧 ایمیل:</label>
                                <input type="email" name="email" class="form-control" placeholder="ایمیل شما" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">💬 نظر:</label>
                                <textarea name="message" class="form-control" rows="4" placeholder="نظر خود را بنویسید" required></textarea>
                            </div>
                            <button type="submit" name="submit_feedback" class="btn btn-primary-custom w-100">📥 ارسال نظر</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Footer -->
    <footer class="footer">
        <p>© 2024 آموزشگاه زبان جانان | طراحی‌شده با ❤️</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
