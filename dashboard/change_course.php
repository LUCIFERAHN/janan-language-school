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
// ✅ دریافت لیست دوره‌ها برای نمایش در Dropdown
$courses_result = $conn->query("SELECT id, course_name FROM courses");
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);
// ✅ دریافت اطلاعات دانشجو
$stmt = $conn->prepare("SELECT id, full_name, course_id FROM users WHERE id = ? AND role = 'student'");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('❌ دانشجو یافت نشد.');
}

$student = $result->fetch_assoc();

// ✅ تغییر دوره دانشجو
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_course'])) {
    $new_course_id = intval($_POST['new_course_id']);

    if ($new_course_id > 0) {
        $update_stmt = $conn->prepare("
            UPDATE users 
            SET course_id = ? 
            WHERE id = ?
        ");
        $update_stmt->bind_param('ii', $new_course_id, $student_id);

        if ($update_stmt->execute()) {
            $message = '<div class="alert alert-success">✅ دوره با موفقیت تغییر یافت.</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در تغییر دوره.</div>';
        }
        $update_stmt->close();
    } else {
        $message = '<div class="alert alert-danger">❗ شناسه دوره معتبر نیست.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>🔄 تغییر دوره دانشجو</title>
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
        <h2 class="text-center">🔄 تغییر دوره دانشجو</h2>
        <?= $message ?>
        <form method="POST">
        <select name="course_id" class="form-select mb-2" required>
                    <option value="">انتخاب دوره</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            <button type="submit" name="change_course" class="btn btn-primary w-100">🔄 تغییر دوره</button>
        </form>
        <a href="students.php" class="btn btn-secondary mt-3 w-100">🔙 بازگشت به مدیریت دانشجویان</a>
    </div>
</body>
</html>
