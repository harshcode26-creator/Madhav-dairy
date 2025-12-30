<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/../vendor/PHPMailer/PHPMailer.php";
require_once __DIR__ . "/../vendor/PHPMailer/SMTP.php";
require_once __DIR__ . "/../vendor/PHPMailer/Exception.php";

function sendVerificationMail($toEmail, $toName, $otp) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;

        // sender email credentials
        $mail->Username = "madhavdairy.noreply@gmail.com";
        $mail->Password = "mueb laxg aezf gjpf";

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom("madhavdairy.noreply@gmail.com", "Madhav Dairy");
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = "Your OTP for Madhav Dairy";

        $mail->Body = "
            <h2>Email Verification</h2>
            <p>Hello <strong>$toName</strong>,</p>
            <p>Your One Time Password (OTP) is:</p>
            <h1 style='letter-spacing:4px;'>$otp</h1>
            <p>This OTP is valid for <strong>10 minutes</strong>.</p>
            <p>If you did not create this account, please ignore this email.</p>
        ";

        $mail->AltBody =
            "Your OTP is $otp. It is valid for 10 minutes.";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("MAIL ERROR: " . $mail->ErrorInfo);
        return false;
    }
}
