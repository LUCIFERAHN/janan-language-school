<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // ✅ تنظیمات SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jananzabansara@gmail.com'; // ایمیل شما
    $mail->Password = 'fvos odte agrl yzag'; // رمز App Password
    $mail->SMTPSecure = 'tls'; 
    $mail->Port = 587; 

    // ✅ تنظیمات کاراکتر
    $mail->CharSet = 'UTF-8'; // تنظیم کدگذاری کاراکتر به UTF-8
    $mail->Encoding = 'base64'; // کدگذاری ایمیل

    // ✅ اطلاعات فرستنده و گیرنده
    $mail->setFrom('jananzabansara@gmail.com', 'آموزشگاه زبان جانان');
    $mail->addAddress('luciferahnclay@gmail.com'); // ایمیل گیرنده

    // ✅ محتوای ایمیل
    $mail->isHTML(true); // استفاده از HTML در ایمیل
    $mail->Subject = '📧 تست ارسال ایمیل با PHPMailer';
    $mail->Body = '<h3>سلام!</h3><p>این یک ایمیل تستی از <b>PHPMailer</b> است.</p>';
    $mail->AltBody = 'سلام! این یک ایمیل تستی از PHPMailer است.'; // محتوای متنی در صورت عدم پشتیبانی از HTML

    // ✅ ارسال ایمیل
    $mail->send();
    echo '✅ ایمیل با موفقیت ارسال شد.';
} catch (Exception $e) {
    echo "❌ خطا در ارسال ایمیل: {$mail->ErrorInfo}";
}
?>
