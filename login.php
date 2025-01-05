<?php
session_start();
include 'config.php';

$error = '';

// بررسی ارسال فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = '❗ لطفاً نام کاربری و رمز عبور را وارد کنید.';
    } else {
        // بررسی کاربر در پایگاه داده
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                
                // هدایت بر اساس نقش کاربر
                switch ($user['role']) {
                    case 'admin':
                        header('Location: dashboard/admin.php');
                        break;
                    case 'teacher':
                        header('Location: dashboard/teacher.php');
                        break;
                    case 'student':
                        header('Location: dashboard/student.php');
                        break;
                    default:
                        $error = '❗ نقش کاربر نامعتبر است.';
                }
                exit();
            } else {
                $error = '❗ رمز عبور اشتباه است.';
            }
        } else {
            $error = '❗ نام کاربری یافت نشد.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 ورود به سیستم</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            direction: rtl;
            text-align: right;
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4a69bd;
        }

        .error-alert {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔐 ورود به سیستم</h2>
        
        <!-- ✅ نمایش خطاها -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger error-alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">👤 نام کاربری:</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="نام کاربری خود را وارد کنید" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">🔑 رمز عبور:</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="رمز عبور خود را وارد کنید" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">🚀 ورود</button>
        </form>
        <p class="mt-3 text-center">بازگشت به <a href="index.php">  صفحه اصلی</a></p>
    </div>
</body>
</html>
