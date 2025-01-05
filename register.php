<?php
session_start();
include 'config.php';

// پردازش ثبت‌نام
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);
    $role = mysqli_real_escape_string($conn, trim($_POST['role']));

    if (empty($username) || empty($password) || empty($role)) {
        $error = "لطفاً تمام فیلدها را پر کنید.";
    } else {
        // هش کردن رمز عبور
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // افزودن کاربر به جدول users
        $query = "INSERT INTO users (username, password, role) 
                  VALUES ('$username', '$hashed_password', '$role')";

        if (mysqli_query($conn, $query)) {
            $success = "ثبت‌نام با موفقیت انجام شد.";
        } else {
            $error = "خطا در ثبت‌نام: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت‌نام کاربران</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            direction: rtl;
            text-align: right;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: #fff;
            font-family: 'Tahoma', sans-serif;
        
        }
        .card {
            width: 400px;
        }
    </style>
</head>
<body>
    <div class="card shadow p-4">
        <h3 class="text-center mb-4">ثبت‌نام کاربر جدید</h3>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success text-center"><?= $success ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">نام کاربری</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">رمز عبور</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">نقش کاربر</label>
                <select name="role" class="form-select" required>
                    <option value="">-- انتخاب نقش --</option>
                    <option value="student">🎓 دانشجو</option>
                    <option value="teacher">🧑‍🏫 استاد</option>
                    <option value="admin">🛡️ ادمین</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">ثبت‌نام</button>
        </form>
        <p class="mt-3 text-center">حساب کاربری دارید؟ <a href="index.php">وارد شوید</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
