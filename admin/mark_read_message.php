<?php
include ("../database/connection.php");

if(isset($_GET['contact_id']))
{
    mark_read($conn);
}

function mark_read($conn)
{
    $id = $_GET['contact_id'];
    try {
        $query = "UPDATE contact_details SET status = 1 WHERE contact_id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        header("location:message.php");
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>