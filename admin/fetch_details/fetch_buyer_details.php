<?php
include ("../../database/connection.php");

try {
    $stmt = $conn->query("SELECT * FROM buyer_details");
    $buyers = $stmt->fetchAll();

    $csvContent = "Full Name,Email,Contact,Address,State,Pin code,Created On\n";

    foreach ($buyers as $row) {
        $full_name = str_replace('"', '""', $row['full_name']);
        $address = str_replace('"', '""', $row['address']);
        $state = str_replace('"', '""', $row['state']);
        
        $csvContent .= "\"{$full_name}\",{$row['email']},{$row['contact_no']},\"{$address}\",\"{$state}\",{$row['pin_code']},{$row['created_on']}\n";
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="buyer_details.csv"');
    echo $csvContent;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
