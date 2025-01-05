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

// ✅ ثبت آزمون جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_exam'])) {
    $exam_name = $_POST['exam_name'];
    $course_id = $_POST['course_id'];
    $exam_date = $_POST['exam_date'];

    if ($exam_name && $course_id && $exam_date) {
        $stmt = $conn->prepare("INSERT INTO exams (exam_name, course_id, exam_date) VALUES (?, ?, ?)");
        $stmt->bind_param('sis', $exam_name, $course_id, $exam_date);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">✅ آزمون جدید با موفقیت ثبت شد.</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در ثبت آزمون.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-warning">❗ لطفاً تمام فیلدها را پر کنید.</div>';
    }
}

// ✅ دریافت لیست آزمون‌ها
$exams_result = mysqli_query($conn, "
    SELECT exams.id, exams.exam_name, exams.exam_date, courses.course_name 
    FROM exams 
    JOIN courses ON exams.course_id = courses.id
    ORDER BY exams.exam_date DESC
");
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📝 مدیریت آزمون‌ها</title>
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

        .exam-container {
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
            background: linear-gradient(to right, #17a2b8, #117a8b);
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
    <div class="container exam-container">
        <!-- ✅ دکمه بازگشت به صفحه قبل -->
        <div class="mb-3">
            <button onclick="history.back()" class="btn btn-outline-danger">⬅️ بازگشت به صفحه قبل</button>
        </div>
        <h2 class="text-center my-4">📝 مدیریت آزمون‌ها</h2>
        <?= $message ?>

        <!-- ✅ فرم افزودن آزمون جدید -->
        <div class="form-container">
            <h4>➕ افزودن آزمون جدید</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">📚 نام آزمون:</label>
                    <input type="text" name="exam_name" class="form-control" placeholder="نام آزمون" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">📅 تاریخ آزمون:</label>
                    <input type="date" name="exam_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">📘 دوره مربوطه:</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">انتخاب دوره</option>
                        <?php
                        $courses_result = mysqli_query($conn, "SELECT id, course_name FROM courses");
                        while ($course = mysqli_fetch_assoc($courses_result)) {
                            echo "<option value='{$course['id']}'>{$course['course_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="add_exam" class="btn btn-primary-custom w-100">📥 ثبت آزمون</button>
            </form>
        </div>

        <!-- ✅ نمایش لیست آزمون‌ها -->
        <h4 class="mb-3">📊 لیست آزمون‌ها</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center">
                <thead>
                    <tr>
                        <th>🔢 شناسه</th>
                        <th>📚 نام آزمون</th>
                        <th>📘 دوره</th>
                        <th>📅 تاریخ برگزاری</th>
                        <th>⚙️ عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($exam = mysqli_fetch_assoc($exams_result)): ?>
                        <tr>
                            <td><?= $exam['id'] ?></td>
                            <td><?= htmlspecialchars($exam['exam_name']) ?></td>
                            <td><?= htmlspecialchars($exam['course_name']) ?></td>
                            <td><?= htmlspecialchars($exam['exam_date']) ?></td>
                            <td class="action-btns">
                                <a href="edit_exam.php?id=<?= $exam['id'] ?>" class="btn btn-sm btn-warning">✏️ ویرایش</a>
                                <a href="delete_exam.php?id=<?= $exam['id'] ?>" class="btn btn-sm btn-danger">🗑️ حذف</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>