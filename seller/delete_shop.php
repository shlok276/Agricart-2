<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['shop_id'])) {
    $shop_id = $_POST['shop_id'];

    try {
        // Delete the shop from the database
        $stmt_delete = $conn->prepare("DELETE FROM shop_details WHERE shop_id = :id");
        $run = $stmt_delete->execute(['id' => $shop_id]);

        if ($run) {
            // Shop successfully deleted
            header("Location: shop.php?alert=delete_success");
            exit;
        } else {
            echo "Error deleting the shop. Please try again.";
        }
    } catch (PDOException $e) {
        die("Delete failed: " . $e->getMessage());
    }
} else {
    header("Location: shop.php");
    exit;
}
?>
