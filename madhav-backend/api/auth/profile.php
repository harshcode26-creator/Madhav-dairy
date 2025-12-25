<?php
header("Content-Type: application/json");
session_start();
require_once "../../config/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Not logged in"
    ]);
    exit;
}

$userId = $_SESSION["user_id"];

$sql = "
    SELECT users.id, users.name, users.email, roles.role_name
    FROM users
    JOIN roles ON users.role_id = roles.id
    WHERE users.id = ?
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

echo json_encode([
    "status" => "success",
    "user" => [
        "id" => $user["id"],
        "name" => $user["name"],
        "email" => $user["email"],
        "role" => $user["role_name"]
    ]
]);
