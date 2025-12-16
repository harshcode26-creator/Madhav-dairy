<?php
header("Content-Type: application/json");

require_once "../../helpers/auth.php";
require_once "../../config/db.php";

$userId = $_SESSION["user_id"];

$query = "
SELECT 
    orders.id,
    products.name AS product_name,
    orders.quantity,
    orders.total_price,
    orders.status,
    orders.created_at
FROM orders
JOIN products ON orders.product_id = products.id
WHERE orders.user_id = '$userId'
ORDER BY orders.created_at DESC
";

$result = mysqli_query($conn, $query);

$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

echo json_encode([
    "status" => "success",
    "orders" => $orders
]);
