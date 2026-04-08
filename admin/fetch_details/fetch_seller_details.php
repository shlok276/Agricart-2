<?php
include ("../../database/connection.php");

try {
    $stmt = $conn->query("SELECT * FROM seller_details");
    $sellers = $stmt->fetchAll();

    $csvContent = "First Name,Last Name,Email,Contact,Government ID,GST Number,Created On,Account Status,Verification Status\n";

    foreach ($sellers as $row) {
        $first_name = str_replace('"', '""', $row['first_name'] ?? '');
        $last_name = str_replace('"', '""', $row['last_name'] ?? '');
        $status = ($row['status'] == 0) ? "Active" : "Inactive";
        $verify = ($row['verify'] == 0) ? "Unverified" : "Verified";
        $gst = ($row['gst_no'] == 0) ? "-" : $row['gst_no'];
        
        $csvContent .= "\"{$first_name}\",\"{$last_name}\",{$row['email']},{$row['contact_no']},\"{$row['government_id']}\",{$gst},{$row['created_on']},{$status},{$verify}\n";
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="seller_details.csv"');
    echo $csvContent;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>



