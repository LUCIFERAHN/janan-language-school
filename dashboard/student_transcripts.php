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

// ✅ دریافت اطلاعات کارنامه‌های دانشجو
$student_id = $_SESSION['user_id'];
$transcripts_query = "
    SELECT transcripts.file_name, transcripts.file_path, transcripts.uploaded_at,
           courses.course_name
    FROM transcripts
    JOIN courses ON transcripts.course_id = courses.id
    WHERE transcripts.student_id = '$student_id'
    ORDER BY transcripts.uploaded_at DESC
";

$result = mysqli_query($conn, $transcripts_query);

if (!$result) {
    die('❌ خطا در دریافت کارنامه‌ها: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📄 سوابق تحصیلی</title>
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

        .transcripts-container {
            margin-top: 20px;
        }

        .transcripts-table th {
            background-color: #17a2b8;
            color: white;
        }

        .download-btn {
            color: white;
            background-color: #28a745;
        }

        .download-btn:hover {
            background-color: #218838;
        }

        .no-transcripts {
            text-align: center;
            color: #dc3545;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <div class="container transcripts-container">
        <!-- ✅ دکمه بازگشت به صفحه قبل -->
        <div class="mb-3">
            <button onclick="history.back()" class="btn btn-outline-danger">⬅️ بازگشت به صفحه قبل</button>
        </div>

        <h2 class="text-center my-4">📄 سوابق تحصیلی شما</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead>
                        <tr>
                            <th>📚 عنوان درس</th>
                            <th>📅 تاریخ ثبت</th>
                            <th>📥 دانلود کارنامه</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['course_name']) ?></td>
                                <td><?= htmlspecialchars($row['uploaded_at']) ?></td>
                                <td>
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>"
                                        class="btn download-btn" download="<?= htmlspecialchars($row['file_name']) ?>">
                                        📥 دانلود
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center no-transcripts">
                ⛔ هنوز هیچ کارنامه‌ای برای شما ثبت نشده است.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>