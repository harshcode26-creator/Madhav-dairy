<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once "../../config/db.php";

$result = mysqli_query($conn, "SELECT id, name, price FROM products");

$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

echo json_encode([
    "status" => "success",
    "products" => $products
]);
