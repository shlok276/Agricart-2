<?php
include ("../database/connection.php");
include ("../session/session_start.php");
include("../session/session_check.php");

if(isset($_POST['verifySeller']) && isset($_POST['seller_id'])) {
    $seller_id = $_POST['seller_id'];
    
    try {
        $stmt = $conn->prepare("UPDATE seller_details SET verify = 1 WHERE seller_id = :id");
        $result = $stmt->execute(['id' => $seller_id]);
        
        if($result) {
            header("Location: new_seller.php?alert=verified");
        } else {
            header("Location: new_seller.php?alert=error");
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
