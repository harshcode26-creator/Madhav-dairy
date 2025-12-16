<?php
header("Content-Type: application/json");

require_once "../../config/db.php";

echo json_encode([
    "status" => "success",
    "message" => "Backend connected successfully"
]);
