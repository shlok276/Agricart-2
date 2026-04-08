<?php
include ("../database/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    // Validate data
    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            // Insert data into database with current timestamp
            $sql = "INSERT INTO contact_details (buyer_name, email, message, created_on) VALUES (:name, :email, :message, NOW())";
            $stmt = $conn->prepare($sql);
            $run = $stmt->execute([
                'name' => $name,
                'email' => $email,
                'message' => $message
            ]);

            if ($run) {
                // Redirect after successful insertion
                header("Location: contact.php");
                exit();
            } else {
                echo "Failed to send message. Please try again.";
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    } else {
        echo "Please fill in all fields.";
    }
}
?>
