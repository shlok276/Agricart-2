<?php
include("database/connection.php");

try {
    $stmt = $conn->query("SELECT COUNT(*) FROM buyer_details");
    $count = $stmt->fetchColumn();
    echo "<h1>Database Connected Successfully!</h1>";
    echo "Total buyers in Supabase: " . $count;
} catch (PDOException $e) {
    echo "<h1>Connection Failed!</h1>";
    echo $e->getMessage();
}
?>
