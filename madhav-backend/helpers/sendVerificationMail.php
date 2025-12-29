<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/../vendor/PHPMailer/PHPMailer.php";
require_once __DIR__ . "/../vendor/PHPMailer/SMTP.php";
require_once __DIR__ . "/../vendor/PHPMailer/Exception.php";

function sendVerificationMail($toEmail, $toName, $token) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;

        // IMPORTANT: use correct credentials
        $mail->Username = "madhavdairy.noreply@gmail.com";
        $mail->Password = "mueb laxg aezf gjpf";

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom("madhavdairy.noreply@gmail.com", "Madhav Dairy");
        $mail->addAddress($toEmail, $toName);

        $verifyLink =
            "http://localhost/madhav-dairy/madhav-backend/api/auth/verify-email.php?token=" . $token;

        $mail->isHTML(true);
        $mail->Subject = "Verify your email - Madhav Dairy";
        $mail->Body = "
            <h2>Welcome to Madhav Dairy</h2>
            <p>Please verify your email:</p>
            <p><a href='$verifyLink'>Verify Email</a></p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("MAIL ERROR: " . $mail->ErrorInfo);
        return false;
    }
}
