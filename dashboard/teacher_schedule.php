<?php
session_start();
include '../config.php';

// ✅ بررسی نقش استاد
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}

$teacher_id = $_SESSION['user_id'];
$message = '';
// ✅ دریافت لیست دوره‌ها برای نمایش در Dropdown
$courses_result = $conn->query("SELECT id, course_name FROM courses");
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);

// ✅ ثبت برنامه هفتگی
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $course_id = intval($_POST['course_id']);
    $day_of_week = trim($_POST['day_of_week']);
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);

    if ($course_id > 0 && !empty($day_of_week) && !empty($start_time) && !empty($end_time)) {
        $stmt = $conn->prepare("
            INSERT INTO schedules (teacher_id, course_id, day_of_week, start_time, end_time)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('iisss', $teacher_id, $course_id, $day_of_week, $start_time, $end_time);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">✅ برنامه با موفقیت ثبت شد.</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در ثبت برنامه: ' . $stmt->error . '</div>';
        }

        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger">❗ همه فیلدها باید پر شوند.</div>';
    }
}

// ✅ دریافت برنامه‌های ثبت‌شده
$schedule_result = $conn->prepare("
    SELECT s.id, c.course_name, s.day_of_week, s.start_time, s.end_time, s.created_at
    FROM schedules s
    JOIN courses c ON s.course_id = c.id
    WHERE s.teacher_id = ?
");
$schedule_result->bind_param('i', $teacher_id);
$schedule_result->execute();
$schedules = $schedule_result->get_result()->fetch_all(MYSQLI_ASSOC);
$schedule_result->close();
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>📅 برنامه هفتگی استاد</title>
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

        .form-container,
        table {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        table thead {
            background-color: #4a69bd;
            color: #ffffff;
        }

        table tbody tr:hover {
            background-color: #f1f2f6;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="teacher.php">پنل استاد</a>
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
        <h2 class="text-center mb-4">📅 مدیریت برنامه هفتگی</h2>
        <?= $message ?>

        <!-- ✅ فرم ثبت برنامه هفتگی -->
        <div class="form-container mb-4">
            <h4>➕ افزودن برنامه جدید</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">📚 انتخاب دوره</label>
                    <select name="course_id" class="form-select mb-2">
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>"><?= $course['course_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">📅 روز هفته</label>
                    <select name="day_of_week" class="form-select" required>
                        <option value="">انتخاب روز</option>
                        <option value="شنبه">شنبه</option>
                        <option value="یکشنبه">یکشنبه</option>
                        <option value="دوشنبه">دوشنبه</option>
                        <option value="سه‌شنبه">سه‌شنبه</option>
                        <option value="چهارشنبه">چهارشنبه</option>
                        <option value="پنج‌شنبه">پنج‌شنبه</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">🕒 ساعت شروع</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">🕒 ساعت پایان</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
                <button type="submit" name="add_schedule" class="btn btn-primary w-100">➕ ثبت برنامه</button>
            </form>
        </div>

        <!-- ✅ نمایش برنامه‌های ثبت‌شده -->
        <h4>📋 لیست برنامه‌های هفتگی</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>📚 دوره</th>
                    <th>📅 روز هفته</th>
                    <th>🕒 ساعت شروع</th>
                    <th>🕒 ساعت پایان</th>
                    <th>📅 تاریخ ثبت</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><?= htmlspecialchars($schedule['course_name']) ?></td>
                        <td><?= htmlspecialchars($schedule['day_of_week']) ?></td>
                        <td><?= htmlspecialchars($schedule['start_time']) ?></td>
                        <td><?= htmlspecialchars($schedule['end_time']) ?></td>
                        <td><?= htmlspecialchars($schedule['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>