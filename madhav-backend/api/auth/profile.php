<?php
header("Content-Type: application/json");

require_once "../../helpers/auth.php";
require_once "../../config/db.php";

$userId = $_SESSION["user_id"];

$result = mysqli_query($conn, "SELECT id, name, email, role FROM users WHERE id='$userId'");
$user = mysqli_fetch_assoc($result);

echo json_encode([
    "status" => "success",
    "user" => $user
]);
