<?php
include("../database/connection.php");
include("../session/session_start.php");
include("../session/session_check.php");

if(isset($_POST['save_password'])) {
    $username = $_SESSION['username'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    try {
        // Fetch the hashed password from the database
        $stmt = $conn->prepare("SELECT password FROM seller_details WHERE email = :email");
        $stmt->execute(['email' => $username]);
        $row = $stmt->fetch();
        $stored_hashed_password = $row['password'] ?? '';

        // Verify the current password
        if(password_verify($current_password, $stored_hashed_password)) {
            if($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt_update = $conn->prepare("UPDATE seller_details SET password = :pwd WHERE email = :email");
                $run = $stmt_update->execute(['pwd' => $hashed_password, 'email' => $username]);
                
                if($run) {
                    header("Location: setting.php?alert=password_changed_successfully");
                    exit();
                } else {
                    header("Location: setting.php?alert=update_error");
                    exit();
                }
            } else {
                header("Location: setting.php?alert=password_mismatch");
                exit();
            }
        } else {
            header("Location: setting.php?alert=incorrect_current_password");
            exit();
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>
