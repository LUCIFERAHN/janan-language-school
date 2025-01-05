<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';

// ✅ بررسی دسترسی کاربر (ادمین یا استاد)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'teacher'])) {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$message = '';

// ✅ ارسال اطلاعیه جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_announcement'])) {
    $title = trim($_POST['title']);
    $message_text = trim($_POST['message']);

    if (!empty($title) && !empty($message_text)) {
        $stmt = $conn->prepare("
            INSERT INTO announcements (title, message, sender_role, sender_id) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('sssi', $title, $message_text, $role, $user_id);
        $stmt->execute();
        $message = '<div class="alert alert-success">✅ اطلاعیه با موفقیت ارسال شد.</div>';
    } else {
        $message = '<div class="alert alert-danger">❗ عنوان و متن اطلاعیه را وارد کنید.</div>';
    }
}

// ✅ دریافت لیست اطلاعیه‌ها
$result = $conn->query("
    SELECT id, title, message, created_at 
    FROM announcements 
    ORDER BY created_at DESC
");
// مسیر داشبورد بر اساس نقش کاربری
$dashboard_link = match ($_SESSION['role']) {
    'admin' => 'admin.php',
    'teacher' => 'teacher.php',
    'student' => 'student.php',
    default => '../index.php'
};
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📢 مدیریت اطلاعیه‌ها</title>
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
        .announcement-form {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="<?= $dashboard_link ?>">داشبورد </a>
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
        <h1 class="text-center mb-4">📢 مدیریت اطلاعیه‌ها</h1>
        <?= $message ?>

        <!-- ✅ فرم ارسال اطلاعیه -->
        <div class="card announcement-form">
            <div class="card-header bg-primary text-white">✍️ ارسال اطلاعیه جدید</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">📄 عنوان اطلاعیه:</label>
                        <input type="text" name="title" class="form-control" placeholder="عنوان اطلاعیه را وارد کنید" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">📝 متن اطلاعیه:</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="متن اطلاعیه را وارد کنید" required></textarea>
                    </div>
                    <button type="submit" name="send_announcement" class="btn btn-success w-100">📤 ارسال اطلاعیه</button>
                </form>
            </div>
        </div>

        <!-- ✅ لیست اطلاعیه‌ها -->
        <h3 class="text-center mb-3">📋 لیست اطلاعیه‌ها</h3>
        <?php if ($result->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <strong><?= htmlspecialchars($row['title']) ?></strong>
                        <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                        <small class="text-muted">📅 <?= $row['created_at'] ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-warning text-center">❗ هیچ اطلاعیه‌ای وجود ندارد.</div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <script>
            function confirmNavigation(message) {
                return confirm(message);
            }
        </script>
        <div class="container">
            <p>&copy; 2024 آموزشگاه زبان جانان. تمامی حقوق محفوظ است.</p>
        </div>
        <!-- JavaScript (Bootstrap) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Custom JS -->
        <script src="../assets/js/script.js"></script>
        <!-- Confirmation Script -->


    </footer>
</body>
</html>
