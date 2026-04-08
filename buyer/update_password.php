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
        $stmt_check = $conn->prepare("SELECT password FROM buyer_details WHERE email = :email");
        $stmt_check->execute(['email' => $username]);
        $row = $stmt_check->fetch();

        if ($row) {
            $stored_hashed_password = $row['password'];

            // Verify the current password
            if(password_verify($current_password, $stored_hashed_password)) {
                // Current password is correct
                if($new_password === $confirm_password) {
                    // New password and confirm password match
                    // Hash the new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    // Update the password in the database
                    $stmt_update = $conn->prepare("UPDATE buyer_details SET password = :password WHERE email = :email");
                    $run_update = $stmt_update->execute([
                        'password' => $hashed_password,
                        'email' => $username
                    ]);

                    if($run_update) {
                        header("Location: profile.php?alert=password_changed_successfully");
                        exit();
                    } else {
                        header("Location: profile.php?alert=update_error");
                        exit();
                    }
                } else {
                    header("Location: profile.php?alert=password_mismatch");
                    exit();
                }
            } else {
                header("Location: profile.php?alert=incorrect_current_password");
                exit();
            }
        }
    } catch (PDOException $e) {
        die("Password update failed: " . $e->getMessage());
    }
}
?>
