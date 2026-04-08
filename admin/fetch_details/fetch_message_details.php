<?php
include ("../../database/connection.php");

try {
    $stmt = $conn->query("SELECT * FROM contact_details ORDER BY created_on DESC");
    $messages = $stmt->fetchAll();

    $csvContent = "Buyer Name,E-mail,Description,Status,Created On\n";

    foreach ($messages as $row) {
        $status = ($row['status'] == 0) ? "New" : "Viewed";
        $description = str_replace('"', '""', $row['message'] ?? '');
        $buyer_name = str_replace('"', '""', $row['buyer_name'] ?? '');
        
        $csvContent .= "\"{$buyer_name}\",{$row['email']},\"{$description}\",{$status},{$row['created_on']}\n";
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="message_details.csv"');
    echo $csvContent;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
