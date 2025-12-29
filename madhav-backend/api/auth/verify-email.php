<?php
header("Content-Type: application/json");
session_start();
require_once "../../config/db.php";

$token = $_GET["token"] ?? "";

if ($token === "") {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Verification token is missing"
    ]);
    exit;
}

/* Find user with this token */
$sql = "
    SELECT id, email_verified
    FROM users
    WHERE email_verify_token = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $token);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid or expired verification link"
    ]);
    exit;
}

$user = mysqli_fetch_assoc($result);

/* Already verified */
if ((int) $user["email_verified"] === 1) {
    echo json_encode([
        "status" => "success",
        "message" => "Email already verified"
    ]);
    exit;
}

/* Verify email */
$updateSql = "
    UPDATE users
    SET email_verified = 1,
        email_verify_token = NULL
    WHERE id = ?
";

$updateStmt = mysqli_prepare($conn, $updateSql);
mysqli_stmt_bind_param($updateStmt, "i", $user["id"]);
mysqli_stmt_execute($updateStmt);

/* Update session if same user is logged in */
if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $user["id"]) {
    $_SESSION["email_verified"] = true;
}

echo json_encode([
    "status" => "success",
    "message" => "Email verified successfully"
]);
