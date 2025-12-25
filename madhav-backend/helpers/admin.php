<?php
require_once __DIR__ . "/auth.php";

function requireAdmin() {
    requireAuth();

    $allowedRoles = ["admin", "superadmin"];

    if (!in_array($_SESSION["role"], $allowedRoles)) {
        http_response_code(403);
        echo json_encode([
            "status" => "error",
            "message" => "Admin access required"
        ]);
        exit;
    }
}
