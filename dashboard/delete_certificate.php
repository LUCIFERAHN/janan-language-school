<?php
session_start();
include '../config.php';

// بررسی نقش ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
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
// ✅ حذف مدرک
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // دریافت اطلاعات فایل مدرک
    $stmt = $conn->prepare("SELECT file_name FROM certificates WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $certificate = $result->fetch_assoc();

    if ($certificate) {
        $file_path = "../uploads/certificates/" . $certificate['file_name'];

        // حذف از پایگاه داده
        $stmt = $conn->prepare("DELETE FROM certificates WHERE id = ?");
        $stmt->bind_param('i', $delete_id);
        if ($stmt->execute()) {
            // حذف فایل از سرور
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $message = '<div class="alert alert-success">✅ مدرک با موفقیت حذف شد.</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ خطا در حذف مدرک.</div>';
        }
    } else {
        $message = '<div class="alert alert-warning">❗ مدرک موردنظر یافت نشد.</div>';
    }
}

// دریافت لیست مدارک
$result = $conn->query("
    SELECT c.id, c.file_name, c.created_at, u.full_name 
    FROM certificates c
    JOIN users u ON c.student_id = u.id
    ORDER BY c.created_at DESC
");
?>
<html>
<td>
    <a href="?delete_id=<?= $row['id'] ?>" 
       onclick="return confirm('❗ آیا مطمئن هستید که می‌خواهید این مدرک را حذف کنید؟')"
       class="btn btn-danger btn-sm">🗑️ حذف</a>
</td>

</html>
