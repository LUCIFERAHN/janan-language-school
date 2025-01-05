<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// ✅ بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

include '../config.php';

$message = '';

// ✅ ثبت کاربر جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // بررسی اینکه فیلدها خالی نباشند
    if ($full_name && $username && $_POST['password'] && $role) {
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $full_name, $username, $password, $role);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">✅ کاربر جدید با موفقیت ایجاد شد.</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در ایجاد کاربر: ' . $stmt->error . '</div>';
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
    <title>👤 ایجاد کاربر جدید</title>
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

        .create-user-container {
            margin-top: 20px;
        }

        .card-custom {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header-custom {
            background: linear-gradient(to right, #6f42c1, #4e2a84);
            color: white;
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
        <h2 class="text-center my-4">👤 ایجاد کاربر جدید</h2>
        <?= $message ?>

        <!-- ✅ فرم ایجاد کاربر جدید -->
        <div class="card card-custom mb-4">
            <div class="card-header card-header-custom">📝 اطلاعات کاربر جدید</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">👤 نام کامل:</label>
                        <input type="text" name="full_name" class="form-control" placeholder="نام کامل کاربر" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">📛 نام کاربری:</label>
                        <input type="text" name="username" class="form-control" placeholder="نام کاربری" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">🔑 رمز عبور:</label>
                        <input type="password" name="password" class="form-control" placeholder="رمز عبور" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">🎓 نقش کاربر:</label>
                        <select name="role" class="form-control" required>
                            <option value="student">🎓 دانشجو</option>
                            <option value="teacher">👨‍🏫 استاد</option>
                            <option value="admin">🛡️ ادمین</option>
                        </select>
                    </div>
                    <button type="submit" name="create_user" class="btn btn-primary w-100">✅ ایجاد کاربر</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>