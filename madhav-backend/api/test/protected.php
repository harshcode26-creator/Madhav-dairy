<?php
require_once "../../helpers/auth.php";
requireAuth();

echo json_encode([
    "status" => "success",
    "message" => "You are authenticated"
]);
