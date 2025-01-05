<?php
// ✅ شروع جلسه به‌صورت امن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';
// بررسی دسترسی کاربر
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// مقداردهی اولیه
$message = '';
$courses_result = null;
// ✅ ثبت دوره جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_course'])) {
    $course_name = trim($_POST['course_name']);
    $teacher_id = intval($_POST['teacher_id']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // اعتبارسنجی داده‌ها
    if (empty($course_name) || empty($teacher_id) || empty($start_date) || empty($end_date)) {
        $message = '<div class="alert alert-danger">❌ لطفاً همه فیلدها را پر کنید.</div>';
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $message = '<div class="alert alert-danger">❌ تاریخ شروع نمی‌تواند بعد از تاریخ پایان باشد.</div>';
    } else {
        // افزودن دوره به پایگاه داده
        $stmt = $conn->prepare("
            INSERT INTO courses (course_name, teacher_id, start_date, end_date) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('siss', $course_name, $teacher_id, $start_date, $end_date);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">✅ دوره جدید با موفقیت ثبت شد.</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در ثبت دوره: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    }
}
// ✅ حذف دوره
if (isset($_GET['delete_id'])) {
    $course_id = intval($_GET['delete_id']);

    // بررسی وجود دوره
    $stmt = $conn->prepare("SELECT id FROM courses WHERE id = ?");
    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $message = '<div class="alert alert-danger">❌ دوره مورد نظر یافت نشد.</div>';
    } else {
        // حذف دوره
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param('i', $course_id);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">✅ دوره با موفقیت حذف شد.</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در حذف دوره: ' . $stmt->error . '</div>';
        }
    }
    $stmt->close();
}

// ✅ دریافت لیست دوره‌ها
$stmt = $conn->prepare("SELECT courses.id, courses.course_name, teachers.full_name AS teacher_name, courses.start_date, courses.end_date 
                        FROM courses 
                        LEFT JOIN teachers ON courses.teacher_id = teachers.id");
$stmt->execute();
$courses_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📚 مدیریت دوره‌ها</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
   /* ✅ تنظیمات کلی */
   body {
            direction: rtl;
            text-align: right;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: black;
            font-family: 'Tahoma', sans-serif;
        }

        .container {
            margin-top: 30px;
        }

        h1 {
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-section,
        .table-section {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
        }

        .form-section h2,
        .table-section h2 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: #555;
        }

        .btn-custom {
            border-radius: 4px;
        }

        table thead {
            background-color: #f8f9fa;
        }

        table th,
        table td {
            text-align: center;
            vertical-align: middle;
        }

        .alert {
            margin-bottom: 20px;
        }

        .delete-btn {
            color: red;
            cursor: pointer;
        }
    </style>
    <script>
        function confirmDelete(courseId) {
            if (confirm('❗ آیا مطمئن هستید که می‌خواهید این دوره را حذف کنید؟')) {
                window.location.href = '?delete_id=' + courseId;
            }
        }
    </script>
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

    <div class="container">
        <h1>📚 مدیریت دوره‌ها</h1>
        <?= $message ?>

        <!-- ✅ فرم ثبت دوره جدید -->
        <div class="form-section">
            <h2>➕ افزودن دوره جدید</h2>
            <form method="POST" action="courses.php">
                <div class="mb-3">
                    <label class="form-label">📚 نام دوره:</label>
                    <input type="text" name="course_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">👩‍🏫 استاد دوره:</label>
                    <select name="teacher_id" class="form-select" required>
                        <option value="">-- انتخاب استاد --</option>
                        <?php
                        $teacher_result = $conn->query("SELECT id, full_name FROM teachers");
                        while ($teacher = $teacher_result->fetch_assoc()) {
                            echo "<option value='{$teacher['id']}'>{$teacher['full_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">📅 تاریخ شروع:</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">📅 تاریخ پایان:</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
                <button type="submit" name="submit_course" class="btn btn-primary btn-custom">📥 ثبت دوره</button>
            </form>
        </div>

        <!-- ✅ نمایش لیست دوره‌ها -->
        <div class="table-section">
            <h2>📋 لیست دوره‌ها</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>📚 نام دوره</th>
                        <th>👩‍🏫 استاد</th>
                        <th>📅 تاریخ شروع</th>
                        <th>📅 تاریخ پایان</th>
                        <th>🛠️ عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($course = $courses_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $course['id'] ?></td>
                            <td><?= $course['course_name'] ?></td>
                            <td><?= $course['teacher_name'] ?: '❌ بدون استاد' ?></td>
                            <td><?= $course['start_date'] ?></td>
                            <td><?= $course['end_date'] ?></td>
                            <td>
                                <a href="course_students.php?course_id=<?= $course['id'] ?>" class="btn btn-info btn-sm">👥 لیست دانشجویان</a>
                                <button onclick="confirmDelete(<?= $course['id'] ?>)" class="btn btn-sm btn-danger">🗑️ حذف</button>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
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