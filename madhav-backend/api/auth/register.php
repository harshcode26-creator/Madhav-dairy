<?php
header("Content-Type: application/json");

require_once "../../config/db.php";

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

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Password must be at least 6 characters"
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
$roleId = $role["id"];

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

/* Insert user */
$insertSql = "INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)";
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, "sssi", $name, $email, $hashedPassword, $roleId);

if (mysqli_stmt_execute($insertStmt)) {
    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "message" => "User registered successfully"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Registration failed"
    ]);
}
