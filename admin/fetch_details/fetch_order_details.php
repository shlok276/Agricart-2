<?php
include ("../../database/connection.php");

try {
    $query = "SELECT o.order_no, p.name AS product_name, bd.full_name AS buyer_name, 
              sd.first_name AS seller_name, o.payment, o.price, o.quantity, o.status, o.order_date, o.tracking_no 
              FROM order_details o 
              JOIN product_details p ON o.product_id = CAST(p.product_id AS VARCHAR)
              JOIN buyer_details bd ON o.buyer_id = bd.buyer_id 
              JOIN seller_details sd ON o.seller_id = sd.seller_id";
    $stmt = $conn->query($query);
    $orders = $stmt->fetchAll();

    $csvContent = "Order Number,Tracking Number,Product Name,Buyer Name,Seller Name,Price,Quantity,Payment,Order Status,Order Date\n";

    foreach ($orders as $row) {
        $paymentMethod = ($row['payment'] == 0) ? "Cash" : "Online";
        $orderStatus = ($row['status'] == 0) ? "Pending" : "Shipped";
        
        $product_name = str_replace('"', '""', $row['product_name'] ?? '');
        $buyer_name = str_replace('"', '""', $row['buyer_name'] ?? '');
        $seller_name = str_replace('"', '""', $row['seller_name'] ?? '');

        $csvContent .= "{$row['order_no']},{$row['tracking_no']},\"{$product_name}\",\"{$buyer_name}\",\"{$seller_name}\",{$row['price']},{$row['quantity']},{$paymentMethod},{$orderStatus},{$row['order_date']}\n";
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="order_details.csv"');
    echo $csvContent;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

