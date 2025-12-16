<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "madhav_dairy";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit;
}
