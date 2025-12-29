<?php
header("Content-Type: application/json");
session_start();
require_once "../../config/db.php";
require_once "../../helpers/sendVerificationMail.php";


$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data["name"] ?? "");
$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";

if ($name === "" || $email === "" || $password === "") {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required"
    ]);
    exit;
}

/* Strong password validation */
if (
    strlen($password) < 8 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[0-9]/', $password) ||
    !preg_match('/[\W]/', $password)
) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" =>
            "Password must be at least 8 characters and include uppercase, lowercase, number, and special character"
    ]);
    exit;
}

/* Check if email already exists */
$checkSql = "SELECT id FROM users WHERE email = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "s", $email);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) > 0) {
    http_response_code(409);
    echo json_encode([
        "status" => "error",
        "message" => "Email already registered"
    ]);
    exit;
}

/* Get USER role id */
$roleSql = "SELECT id FROM roles WHERE role_name = 'user' LIMIT 1";
$roleResult = mysqli_query($conn, $roleSql);

if (!$roleResult || mysqli_num_rows($roleResult) === 0) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "User role not configured"
    ]);
    exit;
}

$role = mysqli_fetch_assoc($roleResult);
$roleId = (int) $role["id"];

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

/* Generate email verification token */
$verifyToken = bin2hex(random_bytes(32));

/* Insert user as unverified */
$insertSql = "
    INSERT INTO users (name, email, password, role_id, email_verified, email_verify_token)
    VALUES (?, ?, ?, ?, 0, ?)
";

$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param(
    $insertStmt,
    "sssis",
    $name,
    $email,
    $hashedPassword,
    $roleId,
    $verifyToken
);

if (!mysqli_stmt_execute($insertStmt)) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Registration failed"
    ]);
    exit;
}

/* Auto login (but unverified) */
$userId = mysqli_insert_id($conn);
session_regenerate_id(true);

$_SESSION["user_id"] = $userId;
$_SESSION["role"] = "user";
$_SESSION["email_verified"] = false;
$_SESSION["email"] = $email;
$_SESSION["name"] = $name;

$mailSent = sendVerificationMail($email, $name, $verifyToken);

if (!$mailSent) {
    // rollback user creation
    $deleteSql = "DELETE FROM users WHERE email = ?";
    $deleteStmt = mysqli_prepare($conn, $deleteSql);
    mysqli_stmt_bind_param($deleteStmt, "s", $email);
    mysqli_stmt_execute($deleteStmt);

    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Unable to send verification email. Please try again later."
    ]);
    exit;
}


http_response_code(201);
echo json_encode([
    "status" => "success",
    "message" => "Account created. Please check your email to verify your account."
]);
