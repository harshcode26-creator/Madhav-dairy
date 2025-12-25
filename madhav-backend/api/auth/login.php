<?php
header("Content-Type: application/json");
session_start();
require_once "../../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";

if ($email === "" || $password === "") {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Email and password are required"
    ]);
    exit;
}

$sql = "
    SELECT users.id, users.name, users.email, users.password, roles.role_name
    FROM users
    JOIN roles ON users.role_id = roles.id
    WHERE users.email = ?
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid credentials"
    ]);
    exit;
}

$user = mysqli_fetch_assoc($result);

if (!password_verify($password, $user["password"])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid credentials"
    ]);
    exit;
}

/* Security: regenerate session */
session_regenerate_id(true);

$_SESSION["user_id"] = $user["id"];
$_SESSION["role"] = $user["role_name"];

echo json_encode([
    "status" => "success",
    "message" => "Login successful",
    "user" => [
        "id" => $user["id"],
        "name" => $user["name"],
        "email" => $user["email"],
        "role" => $user["role_name"]
    ]
]);
