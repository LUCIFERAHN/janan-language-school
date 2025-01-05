<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// ✅ بررسی ورود کاربر
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

include '../config.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$message = '';
$errors = [];

// ✅ تغییر نام کاربری و رمز عبور
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = trim($_POST['new_username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 🔑 بررسی نام کاربری تکراری
    if ($new_username) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param('si', $new_username, $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = '❗ این نام کاربری قبلاً استفاده شده است. لطفاً نام کاربری دیگری انتخاب کنید.';
        }
        $stmt->close();
    }

    // 🔒 بررسی رمز عبور
    if ($new_password || $confirm_password) {
        if ($new_password !== $confirm_password) {
            $errors[] = '❌ رمز عبور و تأیید آن مطابقت ندارند.';
        }
    }

    // ✅ اگر هیچ خطایی وجود نداشته باشد
    if (empty($errors)) {
        if ($new_username) {
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->bind_param('si', $new_username, $user_id);
            $stmt->execute();
            $stmt->close();
            $message = '<div class="alert alert-success">✅ نام کاربری با موفقیت تغییر کرد.</div>';
        }

        if ($new_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param('si', $hashed_password, $user_id);
            $stmt->execute();
            $stmt->close();
            $message .= '<div class="alert alert-success">✅ رمز عبور با موفقیت تغییر کرد.</div>';
        }
    }
}

// ✅ دریافت اطلاعات کاربر
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($current_username);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 تغییر اطلاعات حساب کاربری</title>
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

        .profile-container {
            margin-top: 30px;
            max-width: 600px;
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

        .btn-secondary-custom {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary-custom:hover {
            background-color: #5a6268;
        }

        .alert ul {
            margin: 0;
            padding: 0 15px;
        }

        .alert ul li {
            list-style-type: none;
        }
    </style>
</head>

<body>
    <div class="container profile-container">
        <h2 class="text-center my-4">🔐 تغییر اطلاعات حساب کاربری</h2>

        <!-- ✅ نمایش پیام‌ها و خطاها -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>❌ خطاها:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?= $message ?>

        <!-- ✅ فرم تغییر اطلاعات -->
        <div class="card card-custom">
            <div class="card-header card-header-custom">✏️ تغییر نام کاربری و رمز عبور</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">📛 نام کاربری فعلی:</label>
                        <input type="text" value="<?= htmlspecialchars($current_username) ?>" class="form-control" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">📝 نام کاربری جدید:</label>
                        <input type="text" name="new_username" class="form-control" placeholder="نام کاربری جدید">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">🔑 رمز عبور جدید:</label>
                        <input type="password" name="new_password" class="form-control" placeholder="رمز عبور جدید">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">🔑 تأیید رمز عبور جدید:</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="تأیید رمز عبور جدید">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary-custom w-100">📥 ذخیره تغییرات</button>
                </form>
                <a href="<?= $role ?>.php" class="btn btn-secondary-custom w-100 mt-3">⬅️ بازگشت به داشبورد</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>