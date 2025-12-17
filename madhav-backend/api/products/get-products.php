<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once "../../config/db.php";

$sql = "
    SELECT 
        p.id,
        p.name,
        p.price,
        p.category_id,
        c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
";

$result = mysqli_query($conn, $sql);

$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

echo json_encode([
    "status" => "success",
    "products" => $products
]);
