<?php
include ("../database/connection.php");
include ("../session/session_start.php");
// include ("../session/session_check.php");

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'login':
        $email = $_POST['e-mail'];
        $password = $_POST['password'];
        
        try {
            // Check admin
            $stmt_admin = $conn->prepare("SELECT * FROM admin WHERE email = :email");
            $stmt_admin->execute(['email' => $email]);
            $row_admin = $stmt_admin->fetch();

            // Check seller
            $stmt_seller = $conn->prepare("SELECT * FROM seller_details WHERE email = :email");
            $stmt_seller->execute(['email' => $email]);
            $row_seller = $stmt_seller->fetch();

            if ($row_seller) {
                $storedPasswordHash = $row_seller['password'];
                if (password_verify($password, $storedPasswordHash)) {
                    if ($row_seller['status'] == 0){
                        if ($row_seller['verify'] == 0){
                            header("location:../seller/verify_account.php");
                        }else{
                            $_SESSION['username'] = $email;
                            header("location:../seller/index.php");
                            exit();
                        }
                    } else {
                        header("location:../seller/account_deactivate.php");
                        exit();
                    }
                } else {
                    $_SESSION['login_error'] = "Invalid email or password.";
                    header("location:s_login.php");
                    exit();
                }
            } elseif ($row_admin) {
                $storedPasswordHash = $row_admin['password'];
                if (password_verify($password, $storedPasswordHash)) {
                    $_SESSION['username'] = $email;
                    header("location:../admin/index.php");
                    exit();
                } else {
                    $_SESSION['login_error'] = "Invalid email or password.";
                    header("location:s_login.php");
                    exit();
                }
            } else {
                $_SESSION['login_error'] = "User not found.";
                header("location:s_login.php");
                exit();
            }
        } catch (PDOException $e) {
            die("Login failed: " . $e->getMessage());
        }
        break;

    case 'registration':
        $email = $_POST['e-mail'];
        $number = !empty($_POST['number']) ? (int)$_POST['number'] : 0;
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $created_at = date('Y-m-d H:i:s');

        try {
            // Check if the email is already registered
            $stmt_check = $conn->prepare("SELECT * FROM seller_details WHERE email = :email");
            $stmt_check->execute(['email' => $email]);
            
            if ($stmt_check->rowCount() > 0) {
                // Email already registered
                $_SESSION['register_error'] = "E-mail already registered. You can login directly";
                header("location:login.php");
                exit();
            } else {
                // Email not registered, proceed with registration
                // Adding required fields for PostgreSQL schema
                $sql = "INSERT INTO seller_details(email, contact_no, password, created_on, first_name, last_name, photo, government_id, gst_no, status, otp, verify) 
                        VALUES(:email, :number, :password, :created_on, '', '', '', '', 0, 0, 0, 0)";
                $stmt_insert = $conn->prepare($sql);
                $run_query = $stmt_insert->execute([
                    'email' => $email,
                    'number' => $number,
                    'password' => $password,
                    'created_on' => $created_at
                ]);
            
                if ($run_query) {
                    $_SESSION['username'] = $email;
                    header("location:../seller/verify_account.php");
                    exit();
                } else {
                    echo "Registration failed.";
                }
            }
        } catch (PDOException $e) {
            die("Registration failed: " . $e->getMessage());
        }
        
        header("location:s_login.php");
        exit();
        break;
}
?>