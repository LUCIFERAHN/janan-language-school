<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// ✅ بررسی ورود دانشجو
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

include '../config.php';

// ✅ دریافت اطلاعات مدارک و گواهی‌های دانشجو
$student_id = $_SESSION['user_id'];
$certificates_query = "
    SELECT certificates.file_name, certificates.file_path, certificates.issued_date,
           courses.course_name
    FROM certificates
    JOIN courses ON certificates.course_id = courses.id
    WHERE certificates.student_id = '$student_id'
    ORDER BY certificates.issued_date DESC
";

$result = mysqli_query($conn, $certificates_query);

if (!$result) {
    die('❌ خطا در دریافت اطلاعات مدارک و گواهی‌ها: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📜 مدارک و گواهی‌ها</title>
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

        .certificates-container {
            margin-top: 20px;
        }

        .certificate-card {
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .certificate-card .card-header {
            background: linear-gradient(to right, #6f42c1, #4e2a84);
            color: white;
        }

        .certificate-card .card-body {
            background-color: #f8f9fc;
        }

        .download-btn {
            background-color: #28a745;
            color: white;
        }

        .download-btn:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container certificates-container">
        <!-- ✅ دکمه بازگشت به صفحه قبل -->
        <div class="mb-3">
            <button onclick="history.back()" class="btn btn-outline-danger">⬅️ بازگشت به صفحه قبل</button>
        </div>
        <h2 class="text-center my-4">📜 مدارک و گواهی‌های شما</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="row">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-6">
                        <div class="card certificate-card">
                            <div class="card-header">
                                🎓 <?= htmlspecialchars($row['course_name']) ?>
                            </div>
                            <div class="card-body">
                                <p><strong>📅 تاریخ صدور:</strong> <?= htmlspecialchars($row['issued_date']) ?></p>
                                <a href="<?= htmlspecialchars($row['file_path']) ?>"
                                    class="btn download-btn w-100" download="<?= htmlspecialchars($row['file_name']) ?>">
                                    📥 دانلود مدرک
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                ⛔ هنوز هیچ مدرکی برای شما ثبت نشده است.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>