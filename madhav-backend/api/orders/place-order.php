<?php
header("Content-Type: application/json");

require_once "../../helpers/auth.php";
require_once "../../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$userId = $_SESSION["user_id"];
$productId = $data["product_id"] ?? "";
$quantity = $data["quantity"] ?? "";

if ($productId === "" || $quantity === "" || $quantity <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid order data"
    ]);
    exit;
}

$productRes = mysqli_query($conn, "SELECT price FROM products WHERE id='$productId' AND is_available=1");

if (mysqli_num_rows($productRes) === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Product not available"
    ]);
    exit;
}

$product = mysqli_fetch_assoc($productRes);
$totalPrice = $product["price"] * $quantity;

$query = "INSERT INTO orders (user_id, product_id, quantity, total_price)
          VALUES ('$userId', '$productId', '$quantity', '$totalPrice')";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        "status" => "success",
        "message" => "Order placed successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to place order"
    ]);
}
