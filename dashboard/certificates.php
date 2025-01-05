<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// ✅ بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

include '../config.php';

$message = '';

// ✅ ثبت مدرک جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_certificate'])) {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $file = $_FILES['certificate_file'];

    if ($student_id && $course_id && $file['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($file['name']);
        $file_path = "../uploads/certificates/" . time() . "_" . $file_name;
        move_uploaded_file($file['tmp_name'], $file_path);

        $stmt = $conn->prepare("
            INSERT INTO certificates (student_id, course_id, file_name, file_path)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('iiss', $student_id, $course_id, $file_name, $file_path);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">✅ مدرک جدید با موفقیت ثبت شد.</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در ثبت مدرک.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-warning">❗ لطفاً تمام فیلدها را پر کنید و یک فایل معتبر آپلود کنید.</div>';
    }
}

// ✅ دریافت لیست مدارک
$certificates_result = mysqli_query($conn, "
    SELECT certificates.id, certificates.file_name, certificates.file_path, certificates.issued_date, 
           users.username AS student_name, courses.course_name
    FROM certificates
    JOIN users ON certificates.student_id = users.id
    JOIN courses ON certificates.course_id = courses.id
    ORDER BY certificates.issued_date DESC
");

// ✅ دریافت لیست دانشجویان و دوره‌ها
$students_result = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'student'");
$students = mysqli_fetch_all($students_result, MYSQLI_ASSOC);

$courses_result = mysqli_query($conn, "SELECT id, course_name FROM courses");
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📜 مدیریت صدور مدارک</title>
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

        /* ✅ کارت‌ها */
        .card-custom {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header-custom {
            background: linear-gradient(to right, #6f42c1, #4e2a84);
            color: white;
            font-size: 1.1rem;
        }

        /* ✅ جدول‌ها */
        .table th {
            background-color: #007bff;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }

        .action-btns .btn {
            margin: 2px;
        }

        /* ✅ فرم‌ها */
        .form-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .form-container h4 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #495057;
        }

        .btn-primary-custom {
            background-color: #17a2b8;
            border: none;
        }

        .btn-primary-custom:hover {
            background-color: #117a8b;
        }

        .alert-custom {
            margin-bottom: 20px;
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
        <h2 class="text-center my-4">📜 مدیریت صدور مدارک</h2>
        <?= $message ?>

        <!-- ✅ فرم افزودن مدرک جدید -->
        <div class="form-container">
            <h4>➕ صدور مدرک جدید</h4>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">👤 دانشجو:</label>
                    <select name="student_id" class="form-select" required>
                        <option value="">انتخاب دانشجو</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">📚 دوره:</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">انتخاب دوره</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">📤 فایل مدرک:</label>
                    <input type="file" name="certificate_file" class="form-control" required>
                </div>
                <button type="submit" name="add_certificate" class="btn btn-primary-custom w-100">📥 ثبت مدرک</button>
            </form>
        </div>

        <!-- ✅ نمایش لیست مدارک -->
        <h4 class="mb-3">📊 لیست مدارک</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center">
                <thead>
                    <tr>
                        <th>🔢 شناسه</th>
                        <th>👤 دانشجو</th>
                        <th>📚 دوره</th>
                        <th>📅 تاریخ صدور</th>
                        <th>⚙️ عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($certificate = mysqli_fetch_assoc($certificates_result)): ?>
                        <tr>
                            <td><?= $certificate['id'] ?></td>
                            <td><?= htmlspecialchars($certificate['student_name']) ?></td>
                            <td><?= htmlspecialchars($certificate['course_name']) ?></td>
                            <td><?= htmlspecialchars($certificate['issued_date']) ?></td>
                            <td class="action-btns">
                                <a href="<?= $certificate['file_path'] ?>" class="btn btn-sm btn-success" download>📥 دانلود</a>
                                <a href="delete_certificate.php?id=<?= $certificate['id'] ?>" class="btn btn-sm btn-danger">🗑️ حذف</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>