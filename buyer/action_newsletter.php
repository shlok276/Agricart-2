<?php
// Retrieve the email from the form submission
if(isset($_POST['email'])) {
    $email = $_POST['email'];

    include ("../database/connection.php");

    try {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO newsletter (email) VALUES (:email)");
        $run = $stmt->execute(['email' => $email]);

        // Execute SQL statement
        if ($run) {
            header("location:index.php");
            exit();
        } else {
            echo "Failed to subscribe to newsletter.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>