<?php
// Include necessary files
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve order ID and tracking ID from the form
    $order_id = $_POST['order_id'];
    $tracking_no = $_POST['tracking_no'];

    try {
        // Update the database with the tracking ID
        $stmt_update = $conn->prepare("UPDATE order_details SET status = '1', tracking_no = :tracking_no WHERE order_id = :order_id");
        $update_result = $stmt_update->execute(['tracking_no' => $tracking_no, 'order_id' => $order_id]);

        // Check if the update was successful
        if($update_result) {
            $message = 'Tracking ID updated successfully';
        } else {
            $message = 'Failed to update Tracking ID';
        }
    } catch (PDOException $e) {
        $message = 'Error: ' . addslashes($e->getMessage());
    }

    // Provide feedback to the user
    echo "<script>alert('$message'); window.location.href='order.php';</script>";
} else {
    header("Location: order.php");
}
?>
