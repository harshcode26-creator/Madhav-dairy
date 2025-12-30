<?php
header("Content-Type: application/json");
session_start();
require_once "../../config/db.php";

/* User must be logged in */
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Not authenticated"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$otp = trim($data["otp"] ?? "");

if ($otp === "") {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "OTP is required"
    ]);
    exit;
}

$userId = $_SESSION["user_id"];

/* Fetch OTP data */
$sql = "
    SELECT email_verified, email_otp, email_otp_expires
    FROM users
    WHERE id = ?
    LIMIT 1
";

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

/* OTP expired */
if ($user["email_otp_expires"] === null || strtotime($user["email_otp_expires"]) < time()) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "OTP expired. Please request a new one."
    ]);
    exit;
}

/* OTP mismatch */
if (!password_verify($otp, $user["email_otp"])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid OTP"
    ]);
    exit;
}

/* OTP valid → verify email */
$updateSql = "
    UPDATE users
    SET email_verified = 1,
        email_otp = NULL,
        email_otp_expires = NULL
    WHERE id = ?
";

$updateStmt = mysqli_prepare($conn, $updateSql);
mysqli_stmt_bind_param($updateStmt, "i", $userId);
mysqli_stmt_execute($updateStmt);

/* Update session */
$_SESSION["email_verified"] = true;

echo json_encode([
    "status" => "success",
    "message" => "Email verified successfully"
]);
