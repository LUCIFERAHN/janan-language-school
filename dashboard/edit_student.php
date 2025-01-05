<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';

// ✅ بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// ✅ بررسی شناسه دانشجو
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('❌ شناسه دانشجو نامعتبر است.');
}

$student_id = intval($_GET['id']);
$message = '';

// ✅ دریافت اطلاعات دانشجو
$stmt = $conn->prepare("SELECT id, full_name, email, phone, course_id FROM users WHERE id = ? AND role = 'student'");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('❌ دانشجو یافت نشد.');
}

$student = $result->fetch_assoc();

// ✅ به‌روزرسانی اطلاعات دانشجو
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $course_id = intval($_POST['course_id']);

    if (!empty($full_name) && !empty($email) && !empty($phone) && $course_id > 0) {
        $update_stmt = $conn->prepare("
            UPDATE users 
            SET full_name = ?, email = ?, phone = ?, course_id = ? 
            WHERE id = ?
        ");
        $update_stmt->bind_param('sssii', $full_name, $email, $phone, $course_id, $student_id);

        if ($update_stmt->execute()) {
            $message = '<div class="alert alert-success">✅ اطلاعات دانشجو با موفقیت به‌روزرسانی شد.</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در به‌روزرسانی اطلاعات.</div>';
        }
        $update_stmt->close();
    } else {
        $message = '<div class="alert alert-danger">❗ همه فیلدها باید پر شوند.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>✏️ ویرایش اطلاعات دانشجو</title>
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">✏️ ویرایش اطلاعات دانشجو</h2>
        <?= $message ?>
        <form method="POST">
            <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" class="form-control mb-2" placeholder="نام کامل" required>
            <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" class="form-control mb-2" placeholder="ایمیل" required>
            <input type="text" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" class="form-control mb-2" placeholder="شماره تماس" required>
            <input type="number" name="course_id" value="<?= htmlspecialchars($student['course_id']) ?>" class="form-control mb-2" placeholder="شناسه دوره" required>
            <button type="submit" name="update_student" class="btn btn-primary w-100">💾 ذخیره تغییرات</button>
        </form>
        <a href="students.php" class="btn btn-secondary mt-3 w-100">🔙 بازگشت به مدیریت دانشجویان</a>
    </div>
</body>
</html>
