<?php
session_start();
include '../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// ✅ بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$message = '';

// مخفی کردن تمام خطاها
error_reporting(0);

// ✅ دریافت لیست دوره‌ها برای نمایش در Dropdown
$courses_result = $conn->query("SELECT id, course_name FROM courses");
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);

// ✅ تابع تبدیل نام فارسی به انگلیسی برای نام کاربری
function transliterate($string)
{
    $persian = ['آ', 'ا', 'ب', 'پ', 'ت', 'ث', 'ج', 'چ', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'ژ', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ک', 'گ', 'ل', 'م', 'ن', 'و', 'ه', 'ی'];
    $english = ['a', 'a', 'b', 'p', 't', 's', 'j', 'ch', 'h', 'kh', 'd', 'z', 'r', 'z', 'zh', 's', 'sh', 's', 'z', 't', 'z', 'a', 'gh', 'f', 'gh', 'k', 'g', 'l', 'm', 'n', 'v', 'h', 'y'];
    return str_replace($persian, $english, $string);
}

// ✅ افزودن دانشجوی جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $course_id = intval($_POST['course_id']);

    // تولید نام کاربری و رمز عبور
    $transliterated_name = transliterate($full_name);
    $username = strtolower(preg_replace('/[^a-z0-9]/', '_', $transliterated_name)) . rand(100, 999);
    $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if (!empty($full_name) && !empty($email) && !empty($phone) && $course_id > 0) {
        $stmt = $conn->prepare("
            INSERT INTO users (full_name, email, phone, username, password, role, course_id)
            VALUES (?, ?, ?, ?, ?, 'student', ?)
        ");
        $stmt->bind_param('sssssi', $full_name, $email, $phone, $username, $hashed_password, $course_id);
    
        if ($stmt->execute()) {
            // اطمینان از اینکه $full_name خالی نیست
            $full_name = isset($full_name) ? $full_name : 'دانشجو';

    
            // ✅ ارسال ایمیل به دانشجو
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'jananzabansara@gmail.com';
                $mail->Password = 'fvos odte agrl yzag';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
    
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
    
                $mail->setFrom('jananzabansara@gmail.com', 'آموزشگاه زبان جانان');
                $mail->addAddress($email, $full_name);
    
                $mail->isHTML(true); // ارسال ایمیل HTML
                $mail->Subject = '📧 اطلاعات ورود به سامانه آموزشگاه زبان جانان';
                $mail->Body = '
                    <div style="direction: rtl; text-align: right; font-family: Tahoma, Arial, sans-serif; font-size: 14px; line-height: 1.6;">
                        <h3 style="color: #4a69bd;">سلام ' . htmlspecialchars($full_name) . '،</h3>
                        <p>✅ <strong>اطلاعات ورود شما:</strong></p>
                        <hr>
                        <p><strong>📌 نام کاربری:</strong> ' . htmlspecialchars($username) . '</p>
                        <p><strong>🔑 رمز عبور:</strong> ' . htmlspecialchars($password) . '</p>
                        <hr>
                        <p>لطفاً پس از ورود به سامانه رمز عبور خود را تغییر دهید.</p>
                        <p>با احترام،<br>تیم پشتیبانی آموزشگاه زبان جانان</p>
                    </div>
                ';
                $mail->AltBody = "سلام $full_name،\n\nنام کاربری: $username\nرمز عبور: $password\nلطفاً پس از ورود به سامانه رمز عبور خود را تغییر دهید.";
    
                $mail->send();
                $message = '<div class="alert alert-success">✅ دانشجو ثبت شد و اطلاعات ورود به ایمیل ارسال شد.</div>';
            } catch (Exception $e) {
                $message = '<div class="alert alert-warning">⚠️ دانشجو ثبت شد اما ایمیل ارسال نشد: ' . $mail->ErrorInfo . '</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در ثبت دانشجو.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger">❗ همه فیلدها باید پر شوند.</div>';
    }
}    

// ✅ دریافت لیست دانشجویان
$result = $conn->query("
    SELECT u.id, u.full_name, u.email, u.phone, u.username, c.course_name, u.created_at 
    FROM users u
    LEFT JOIN courses c ON u.course_id = c.id
    WHERE u.role = 'student'
");
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>🎓 مدیریت دانشجویان</title>
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

        .container {
            margin-top: 30px;
        }

        .form-container,
        table {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-edit {
            background: #ffc107;
        }

        .btn-course {
            background: #17a2b8;
        }

        .btn-add {
            background: #28a745;
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

    <div class="container">
        <h2>🎓 مدیریت دانشجویان</h2>
        <?= $message ?>
        <form method="POST">
            <input type="text" name="full_name" class="form-control mb-2" placeholder="نام کامل">
            <input type="email" name="email" class="form-control mb-2" placeholder="ایمیل">
            <input type="text" name="phone" class="form-control mb-2" placeholder="شماره تماس">
            <select name="course_id" class="form-select mb-2">
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>"><?= $course['course_name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="add_student" class="btn btn-add w-100">➕ ثبت دانشجو</button>
        </form>
        <table class="table table-hover">
            <tr>
                <th>نام</th>
                <th>ایمیل</th>
                <th>دوره</th>
                <th>عملیات</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['full_name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['course_name'] ?></td>
                    <td>
                        <a href="edit_student.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️ ویرایش</a>
                        <a href="change_course.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">🔄 تغییر دوره</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>