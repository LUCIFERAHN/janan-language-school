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

// ✅ بررسی مقداردهی `username`
$username = $_SESSION['username'] ?? 'کاربر ناشناس'; // مقدار پیش‌فرض در صورت عدم وجود

include '../config.php';

// مسیر داشبورد بر اساس نقش کاربری
$dashboard_link = match ($_SESSION['role'] ?? '') {
    'admin' => 'admin.php',
    'teacher' => 'teacher.php',
    'student' => 'student.php',
    default => '../index.php'
};

// ✅ اطلاعات کاربر جاری
$user_id = $_SESSION['user_id'];

// ✅ دریافت لیست کاربران
$stmt = $conn->prepare("
    SELECT u.id, u.username, u.role,
           (SELECT COUNT(*) 
            FROM chats 
            WHERE sender_id = u.id AND receiver_id = ? AND is_read = '0') AS unread_count
    FROM users u
    WHERE u.id != ?
");
$stmt->bind_param('ii', $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ✅ دریافت پیام‌ها
$receiver_id = intval($_GET['receiver_id'] ?? 0);
$receiver_info = null;
$messages = [];

if ($receiver_id) {
    $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id = ?");
    $stmt->bind_param('i', $receiver_id);
    $stmt->execute();
    $receiver_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // ✅ به‌روزرسانی وضعیت پیام‌ها به عنوان خوانده‌شده
    $stmt = $conn->prepare("
        UPDATE chats 
        SET is_read = '1' 
        WHERE sender_id = ? AND receiver_id = ?
    ");
    $stmt->bind_param('ii', $receiver_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // ✅ دریافت پیام‌ها
    $stmt = $conn->prepare("
        SELECT chats.*, sender.username AS sender_name, receiver.username AS receiver_name, chats.created_at
        FROM chats 
        JOIN users AS sender ON chats.sender_id = sender.id
        JOIN users AS receiver ON chats.receiver_id = receiver.id
        WHERE (chats.sender_id = ? AND chats.receiver_id = ?)
           OR (chats.sender_id = ? AND chats.receiver_id = ?)
        ORDER BY chats.created_at ASC
    ");
    $stmt->bind_param('iiii', $user_id, $receiver_id, $receiver_id, $user_id);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
// ✅ ارسال پیام جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message']) && $receiver_id) {
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));
    if (!empty($message)) {
        $stmt = $conn->prepare("
            INSERT INTO chats (sender_id, receiver_id, message, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        if ($stmt === false) {
            die("❌ خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
        }
        
        $stmt->bind_param('iis', $user_id, $receiver_id, $message);
        if ($stmt->execute()) {
            header("Location: chat.php?receiver_id=$receiver_id");
            exit();
        } else {
            die("❌ خطا در ارسال پیام: " . $stmt->error);
        }
        
    } else {
        echo '<div class="alert alert-warning">⚠️ لطفاً یک پیام وارد کنید.</div>';
    }
}

?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💬 سیستم چت</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* ✅ تنظیمات کلی */
        body {
            direction: rtl;
            text-align: right;
            background: #f0f2f5;
            font-family: 'Tahoma', sans-serif;
        }

        /* ✅ کانتینر اصلی چت */
        .chat-container {
            display: flex;
            height: 90vh;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* ✅ لیست کاربران */
        .user-list {
            width: 30%;
            height: 100vh;
            overflow-y: auto;
            background: #f8f9fa;
            border-left: 1px solid #ddd;
        }

        .user-list a {
            display: block;
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-decoration: none;
            color: #333;
        }

        .user-list a:hover {
            background: #e2e6ea;
        }

        .user-list .active-user {
            background: #d1e7dd;
            font-weight: bold;
        }

        .user-list .unread {
            color: red;
            font-weight: bold;
        }

        /* ✅ بخش چت */
        .chat-box {
            width: 70%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-left: 1px solid #ddd;
            background: #fff;
        }

        .chat-header {
            background: #4a90e2;
            color: white;
            padding: 10px 20px;
            font-size: 1.2rem;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            background: #f9fafc;
        }

        .chat-message {
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            max-width: 70%;
        }

        .chat-message.sent {
            background: #d1e7dd;
            margin-left: auto;
        }

        .chat-message.received {
            background: #f8d7da;
        }

        .chat-footer {
            padding: 10px;
            border-top: 1px solid #ddd;
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
    <div class="chat-container mt-4">
        <!-- ✅ لیست کاربران -->
        <div class="user-list">
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <a href="chat.php?receiver_id=<?= $user['id'] ?>"
                        class="list-group-item <?= $receiver_id == $user['id'] ? 'active-user' : '' ?>">
                        <?= htmlspecialchars($user['username']) ?>
                        <small class="text-muted">(<?= htmlspecialchars($user['role']) ?>)</small>
                        <?php if ($user['unread_count'] > 0): ?>
                            <span class="badge bg-danger ms-2">📩 <?= $user['unread_count'] ?> پیام جدید</span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted">❗ کاربری برای چت در دسترس نیست.</p>
            <?php endif; ?>
        </div>


        <!-- ✅ بخش چت -->
        <div class="chat-box">
            <div class="chat-header">
                <?= $receiver_info ? "چت با " . htmlspecialchars($receiver_info['username']) : "یک مخاطب انتخاب کنید" ?>
            </div>
            <div class="chat-messages">
                <?php foreach ($messages as $msg): ?>
                    <div class="chat-message <?= $msg['sender_id'] == $user_id ? 'sent' : 'received' ?>">
                        <p><?= htmlspecialchars($msg['message']) ?></p>
                        <small><?= htmlspecialchars($msg['created_at']) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ($receiver_info): ?>
                <div class="chat-footer">
                    <form method="POST">
                        <textarea name="message" rows="3" class="form-control mb-2" placeholder="پیام خود را وارد کنید..." required></textarea>
                        <button type="submit" name="send_message" class="btn btn-primary w-100">✉️ ارسال پیام</button>
                    </form>

                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>