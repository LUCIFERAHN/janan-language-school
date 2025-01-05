<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';

// بررسی دسترسی دانشجو
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

$student_id = $_SESSION['user_id'];
$message = '';

// ✅ دریافت مدارک ثبت‌شده توسط ادمین
$stmt = $conn->prepare("
    SELECT id, file_name, created_at 
    FROM certificates 
    WHERE student_id = ?
");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$certificates_result = $stmt->get_result();
$certificates = $certificates_result->fetch_all(MYSQLI_ASSOC);

// ✅ دریافت کارنامه‌های ثبت‌شده توسط استاد
$stmt = $conn->prepare("
    SELECT t.file_name, t.file_path, c.course_name, t.created_at 
    FROM transcripts t
    JOIN courses c ON t.course_id = c.id
    WHERE t.student_id = ?
");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$transcripts_result = $stmt->get_result();
$transcripts = $transcripts_result->fetch_all(MYSQLI_ASSOC);
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

        .container {
            margin-top: 30px;
        }

        .table th, .table td {
            text-align: center;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #4a69bd;
            display: inline-block;
        }

        .card-custom {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .btn-download {
            font-size: 0.9rem;
            padding: 5px 10px;
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
        <h1 class="text-center mb-4">📄 سوابق تحصیلی</h1>

        <!-- ✅ بخش مدارک صادرشده توسط ادمین -->
        <div class="card card-custom">
            <div class="card-header bg-primary text-white">🎓 مدارک صادرشده  </div>
            <div class="card-body">
                <?php if (!empty($certificates)): ?>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>📄 نام مدرک</th>
                                <th>📅 تاریخ صدور</th>
                                <th>⬇️ دانلود</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($certificates as $index => $certificate): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($certificate['file_name']) ?></td>
                                    <td><?= htmlspecialchars($certificate['created_at']) ?></td>
                                    <td>
                                        <a href="../uploads/certificates/<?= htmlspecialchars($certificate['file_name']) ?>" 
                                           class="btn btn-success btn-download" download>دانلود</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center">❗ مدرکی برای شما صادر نشده است.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ✅ بخش کارنامه‌های ثبت‌شده توسط استاد -->
        <div class="card card-custom">
            <div class="card-header bg-info text-white">📝 کارنامه‌های ثبت‌شده  </div>
            <div class="card-body">
                <?php if (!empty($transcripts)): ?>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>📚 نام دوره</th>
                                <th>📄 نام کارنامه</th>
                                <th>📅 تاریخ ثبت</th>
                                <th>⬇️ دانلود</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transcripts as $index => $transcript): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($transcript['course_name']) ?></td>
                                    <td><?= htmlspecialchars($transcript['file_name']) ?></td>
                                    <td><?= htmlspecialchars($transcript['created_at']) ?></td>
                                    <td>
                                        <a href="<?= htmlspecialchars($transcript['file_path']) ?>" 
                                           class="btn btn-primary btn-download" download>دانلود</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center">❗ کارنامه‌ای برای شما ثبت نشده است.</div>
                <?php endif; ?>
            </div>
        </div>

        <a href="student.php" class="btn btn-secondary mt-4">↩️ بازگشت به داشبورد</a>
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
