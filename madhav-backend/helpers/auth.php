<?php
header("Content-Type: application/json");
session_start();

function requireAuth() {
    if (!isset($_SESSION["user_id"])) {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Unauthorized access"
        ]);
        exit;
    }
}

function getAuthUser() {
    if (!isset($_SESSION["user_id"])) {
        return null;
    }

    return [
        "user_id" => $_SESSION["user_id"],
        "role" => $_SESSION["role"] ?? null
    ];
}
