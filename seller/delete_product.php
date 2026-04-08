<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    try {
        // Delete the product from the database
        $stmt_delete = $conn->prepare("DELETE FROM product_details WHERE product_id = :id");
        $run = $stmt_delete->execute(['id' => $product_id]);

        if ($run) {
            header("Location: products.php?alert=delete_success");
            exit;
        } else {
            echo "Error deleting the product. Please try again.";
        }
    } catch (PDOException $e) {
        die("Delete failed: " . $e->getMessage());
    }
} else {
    header("Location: products.php");
    exit;
}
?>
