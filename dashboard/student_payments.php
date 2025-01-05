<?php
session_start();
include '../config.php';

// ✅ بررسی نقش دانشجو
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

$student_id = $_SESSION['user_id'];
$message = '';

// ✅ ثبت پرداخت جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_payment'])) {
    if (!isset($student_id) || empty($student_id)) {
        $message = '<div class="alert alert-danger">❌ خطا: شناسه دانشجو نامعتبر است.</div>';
    } else {
        // دریافت و تبدیل مبلغ
        $amount = intval($_POST['amount']);

        // دیباگ مقدار مبلغ
        echo "مبلغ وارد شده: $amount<br>";

        // تعیین وضعیت پرداخت
        if ($amount >= 200000) {
            $status = 'موفق';
        } else {
            $status = 'ناموفق';
        }



        // ثبت پرداخت در پایگاه داده
        $stmt = $conn->prepare("
            INSERT INTO payments (student_id, amount, status)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param('iis', $student_id, $amount, $status);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">✅ پرداخت با موفقیت ثبت شد.</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در ثبت پرداخت: ' . $stmt->error . '</div>';
        }

        $stmt->close();
    }
}

// ✅ نمایش پرداخت‌های قبلی
$stmt = $conn->prepare("
    SELECT id, amount, status, created_at 
    FROM payments 
    WHERE student_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>💳 پرداخت آنلاین</title>
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
            margin-top: 50px;
        }

        /* ✅ کارت پرداخت */
        .payment-card {
            background: #ffffff;
            color: #333;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.3s ease-in-out;
        }

        .payment-card:hover {
            transform: translateY(-5px);
        }

        .payment-card h3 {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .payment-card p {
            font-size: 1rem;
        }

        .payment-card form input[type="number"] {
            border-radius: 10px;
        }

        .payment-card .btn-primary {
            background: linear-gradient(to right, #4a90e2, #7b5de8);
            border: none;
            border-radius: 25px;
            font-size: 1rem;
        }

        .payment-card .btn-primary:hover {
            background: linear-gradient(to right, #7b5de8, #4a90e2);
        }

        /* ✅ جدول پرداخت‌ها */
        .payment-history {
            background: #fff;
            color: #333;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .payment-history table thead {
            background-color: #4a90e2;
            color: #fff;
        }

        .payment-history table tbody tr:hover {
            background-color: #f1f2f6;
        }

        /* ✅ انیمیشن ورودی */
        .animate__fadeIn {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="student.php">پنل دانشجو</a>
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
        <h2 class="text-center mb-4">💳 پرداخت آنلاین</h2>
        <?= $message ?>

        <!-- ✅ فرم پرداخت آنلاین -->
        <div class="payment-card animate__fadeIn">
            <h3>💵 فرم پرداخت</h3>
            <p>لطفاً مبلغ موردنظر خود را برای پرداخت وارد کنید:</p>
            <form method="POST">
                <div class="mb-3">
                    <label for="amount" class="form-label">💰 مبلغ (تومان)</label>
                    <input type="number" name="amount" id="amount" class="form-control" placeholder="مثلاً 200000" min="1000" required>
                </div>
                <button type="submit" name="make_payment" class="btn btn-primary w-100">💳 پرداخت</button>
            </form>
        </div>

        <!-- ✅ لیست پرداخت‌های قبلی -->
        <div class="payment-history animate__fadeIn">
            <h4>📋 سوابق پرداخت</h4>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>🆔 شناسه</th>
                        <th>💰 مبلغ (تومان)</th>
                        <th>✅ وضعیت</th>
                        <th>📅 تاریخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= $payment['id'] ?></td>
                            <td><?= number_format($payment['amount']) ?></td>
                            <td>
                                <?php if ($payment['status'] === 'موفق'): ?>
                                    <span class="badge bg-success">✅ موفق</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">❌ ناموفق</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $payment['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>