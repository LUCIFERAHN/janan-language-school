<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header('Location: index.php?error=لطفاً تمام فیلدها را پر کنید.');
        exit();
    }

    // بررسی نام کاربری
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // بررسی رمز عبور
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // هدایت براساس نقش
            switch ($user['role']) {
                case 'student':
                    header('Location: dashboard/student.php');
                    break;
                case 'teacher':
                    header('Location: dashboard/teacher.php');
                    break;
                case 'admin':
                    header('Location: dashboard/admin.php');
                    break;
                default:
                    header('Location: index.php?error=نقش کاربر نامعتبر است.');
            }
            exit();
        } else {
            header('Location: index.php?error=رمز عبور اشتباه است.');
            exit();
        }
    } else {
        header('Location: index.php?error=نام کاربری یافت نشد.');
        exit();
    }
}
?>
