<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header('Content-Type: application/json');

// Get form values (sanitize for security)
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

$mail = new PHPMailer(true);

try {
    // SMTP Debug (for development)
    $mail->SMTPDebug = 2; // Set to 0 in production
    $mail->Debugoutput = function ($str, $level) {
        error_log("SMTP Debug [$level]: $str");
    };

    // Server settings
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Host = 'smtp-relay.brevo.com';
    $mail->SMTPAuth = true;
    $mail->Username = '8a4897003@smtp-brevo.com'; // Replace with actual Brevo SMTP login
    $mail->Password = '1tvads5kcgmhJHpr';        // Replace with Brevo SMTP password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->AltBody = "الاسم: $name\nالبريد الإلكتروني: $email\nالرسالة:\n$message \nرقم الهاتف: $phone";


    // Sender & Recipient
    $mail->setFrom('info@madinet-masr.dev', 'Mountain View Website'); // Must match verified domain
    $mail->addAddress('ads@theglobalhub.net', 'Recipient Name');     // Change to actual recipient

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'رسالة جديدة من نموذج الاتصال';
    $mail->Body    = "
        <h3>معلومات الرسالة:</h3>
        <p><strong>الاسم:</strong> {$name}</p>
        <p><strong>رقم الهاتف:</strong> {$phone}</p>
        <p><strong>البريد الإلكتروني:</strong> {$email}</p>
        <p><strong>الرسالة:</strong><br>{$message}</p>
    ";

    // Send email
    if ($mail->send()) {
        echo json_encode([
            "status" => "success",
            "message" => "تم إرسال رسالتك بنجاح!"
        ]);
    } else {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        echo json_encode([
            "status" => "error",
            "message" => "تعذر إرسال الرسالة، يرجى المحاولة لاحقًا."
        ]);
    }

} catch (Exception $e) {
    error_log("PHPMailer Exception: " . $mail->ErrorInfo);
    echo json_encode([
        "status" => "error",
        "message" => "حدث خطأ أثناء إرسال الرسالة: " . $mail->ErrorInfo
    ]);
}