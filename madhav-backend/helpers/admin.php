<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    echo json_encode([
        "status" => "error",
        "message" => "Admin access required"
    ]);
    exit;
}
