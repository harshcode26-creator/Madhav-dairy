<?php
header("Content-Type: application/json");
session_start();
require_once "../../config/db.php";
require_once "../../helpers/sendVerificationMail.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Not authenticated"
    ]);
    exit;
}

$userId = $_SESSION["user_id"];

/* Fetch user */
$sql = "SELECT email, name, email_verified FROM users WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "User not found"
    ]);
    exit;
}

$user = mysqli_fetch_assoc($result);

/* Already verified */
if ((int)$user["email_verified"] === 1) {
    echo json_encode([
        "status" => "success",
        "message" => "Email already verified"
    ]);
    exit;
}

/* Generate new OTP */
$otp = random_int(100000, 999999);
$hashedOtp = password_hash((string)$otp, PASSWORD_DEFAULT);
$otpExpiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

/* Update OTP */
$updateSql = "
    UPDATE users
    SET email_otp = ?, email_otp_expires = ?
    WHERE id = ?
";
$updateStmt = mysqli_prepare($conn, $updateSql);
mysqli_stmt_bind_param($updateStmt, "ssi", $hashedOtp, $otpExpiry, $userId);
mysqli_stmt_execute($updateStmt);

/* Send OTP */
$mailSent = sendVerificationMail($user["email"], $user["name"], $otp);

if (!$mailSent) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Unable to resend OTP. Please try again."
    ]);
    exit;
}

echo json_encode([
    "status" => "success",
    "message" => "OTP resent successfully"
]);
