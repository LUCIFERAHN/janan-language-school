
<?php
session_start(); // شروع جلسه

// حذف تمام نشست‌ها
session_unset(); // حذف متغیرهای نشست
session_destroy(); // نابود کردن نشست

// هدایت به صفحه ورود
header('Location: index.php');
exit();
?>
