<?php
header("Content-Type: application/json");

require_once "../../helpers/admin.php";
require_once "../../config/db.php";

$query = "
SELECT 
    orders.id,
    users.name AS customer_name,
    users.email AS customer_email,
    products.name AS product_name,
    orders.quantity,
    orders.total_price,
    orders.status,
    orders.created_at
FROM orders
JOIN users ON orders.user_id = users.id
JOIN products ON orders.product_id = products.id
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
