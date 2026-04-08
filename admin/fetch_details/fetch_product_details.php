<?php
include ("../../database/connection.php");

try {
    $query = "SELECT product_details.*, seller_details.first_name AS seller_name FROM product_details
              LEFT JOIN seller_details ON product_details.seller_id = seller_details.seller_id";
    $stmt = $conn->query($query);
    $products = $stmt->fetchAll();

    $csvContent = "Product Name,Seller Name,Price,Quantity,Description,Photo1,Photo2,Photo3\n";

    foreach ($products as $row) {
        $name = str_replace('"', '""', $row['name'] ?? '');
        $seller_name = str_replace('"', '""', $row['seller_name'] ?? '');
        $description = str_replace('"', '""', $row['description'] ?? '');
        
        $csvContent .= "\"{$name}\",\"{$seller_name}\",{$row['price']},{$row['quantity']},\"{$description}\",{$row['photo']},{$row['photo2']},{$row['photo3']}\n";
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="product_details.csv"');
    echo $csvContent;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
