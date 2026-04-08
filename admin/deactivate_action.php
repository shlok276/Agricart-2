<?php
include ("../database/connection.php");

if(isset($_GET['seller_id']))
{
    deactivate($conn);
}

function deactivate($conn)
{
    $id = $_GET['seller_id'];
    try {
        $query = "UPDATE seller_details SET status = 1 WHERE seller_id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        header("location:seller.php");
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>