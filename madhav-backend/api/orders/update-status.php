<?php
header("Content-Type: application/json");

require_once "../../helpers/admin.php";
require_once "../../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$orderId = $data["order_id"] ?? "";
$status = $data["status"] ?? "";

$allowed = ["pending", "confirmed", "delivered", "cancelled"];

if ($orderId === "" || !in_array($status, $allowed)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

$query = "UPDATE orders SET status='$status' WHERE id='$orderId'";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        "status" => "success",
        "message" => "Order status updated"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update status"
    ]);
}