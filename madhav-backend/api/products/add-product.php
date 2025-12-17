<?php
header("Content-Type: application/json");

require_once "../../helpers/admin.php";
require_once "../../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data["name"] ?? "");
$price = $data["price"] ?? "";
$category_id = $data["category_id"] ?? "";

if ($name === "" || $price === "" || $category_id === "") {
    echo json_encode([
        "status" => "error",
        "message" => "Name, price and category are required"
    ]);
    exit;
}

$query = "INSERT INTO products (name, price, category_id)
          VALUES ('$name', '$price', '$category_id')";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        "status" => "success",
        "message" => "Product added successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to add product"
    ]);
}
