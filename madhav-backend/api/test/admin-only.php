<?php
require_once "../../helpers/admin.php";
requireAdmin();

echo json_encode([
    "status" => "success",
    "message" => "Admin access granted"
]);
