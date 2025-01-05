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

// ✅ حذف نظر
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">✅ نظر با موفقیت حذف شد.</div>';
    } else {
        $message = '<div class="alert alert-danger">❌ خطا در حذف نظر.</div>';
    }
}

// ✅ دریافت لیست نظرات
$result = $conn->query("SELECT id, name, email, message, created_at FROM feedback ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💬 مدیریت نظرات و پیشنهادات</title>
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

        .table th,
        .table td {
            text-align: center;
        }

        .btn-delete {
            color: white;
            background-color: #dc3545;
            border: none;
            padding: 5px 10px;
        }

        .btn-delete:hover {
            background-color: #c82333;
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
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1 class="text-center mb-4">💬 مدیریت نظرات و پیشنهادات</h1>
        <?= isset($message) ? $message : '' ?>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>👤 نام</th>
                        <th>📧 ایمیل</th>
                        <th>💬 پیام</th>
                        <th>📅 تاریخ ارسال</th>
                        <th>🗑️ حذف</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $index++ ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td>
                                <a href="?delete_id=<?= $row['id'] ?>"
                                    onclick="return confirm('❗ آیا مطمئن هستید که می‌خواهید این نظر را حذف کنید؟')"
                                    class="btn btn-danger btn-sm">🗑️ حذف</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning text-center">❗ هیچ نظری برای نمایش وجود ندارد.</div>
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