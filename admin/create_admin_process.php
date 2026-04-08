<?php 
include("../database/connection.php");

if(isset($_POST['create_admin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $contact_no = $_POST['contact_no'];

    try {
        // Check if the username already exists
        $stmt_check = $conn->prepare("SELECT * FROM admin WHERE email = :email");
        $stmt_check->execute(['email' => $email]);

        if($stmt_check->rowCount() > 0) {
            // Username already exists, show alert
            header("Location: create_admin.php?alert=useralreadyexist");  
            exit();
        } else {
            // Username doesn't exist, proceed with creating the admin account
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new admin data into the database
            $stmt_insert = $conn->prepare("INSERT INTO admin (email, password, contact_no) VALUES (:email, :password, :contact_no)");
            $result = $stmt_insert->execute([
                'email' => $email,
                'password' => $hashed_password,
                'contact_no' => $contact_no
            ]);

            if($result) {
                // Admin account created successfully, show alert
                header("Location: create_admin.php?alert=admincreated");
                exit();
            } else {
                header("Location: create_admin.php?alert=error");
                exit();
            }
        }
    } catch (PDOException $e) {
        // In case of error, redirect to error alert
        header("Location: create_admin.php?alert=error");
        exit();
    }
}
?>
