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
            // Check buyer_details
            $stmt_buyer = $conn->prepare("SELECT * FROM buyer_details WHERE email = :email");
            $stmt_buyer->execute(['email' => $email]);
            $row_buyer = $stmt_buyer->fetch();

            if ($row_buyer) {
                $storedPasswordHash = $row_buyer['password'];
                if (password_verify($password, $storedPasswordHash)) {
                    $_SESSION['username'] = $email;
                    header("location:../buyer/index.php");
                    exit();
                } else {
                    $_SESSION['login_error'] = "Invalid email or password.";
                    header("location:login.php");
                    exit();
                }
            } else {
                $_SESSION['login_error'] = "User not found.";
                header("location:login.php");
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
            $stmt_check = $conn->prepare("SELECT * FROM buyer_details WHERE email = :email");
            $stmt_check->execute(['email' => $email]);
            
            if ($stmt_check->rowCount() > 0) {
                // Email already registered
                $_SESSION['register_error'] = "E-mail already registered. You can login directly";
                header("location:login.php");
                exit();
            } else {
                // Email not registered, proceed with registration
                // Note: PostgreSQL photo field is not null in our schema, giving it a default empty string if not provided
                $sql = "INSERT INTO buyer_details(email, contact_no, password, created_on, photo, full_name, address, pin_code, state, otp) 
                        VALUES(:email, :number, :password, :created_on, '', '', '', 0, '', 0)";
                $stmt_insert = $conn->prepare($sql);
                $run_query = $stmt_insert->execute([
                    'email' => $email,
                    'number' => $number,
                    'password' => $password,
                    'created_on' => $created_at
                ]);
            
                if ($run_query) {
                    $_SESSION['username'] = $email;
                    header("location:../buyer/index.php");
                    exit();
                } else {
                    echo "Registration failed.";
                }
            }
        } catch (PDOException $e) {
            die("Registration failed: " . $e->getMessage());
        }
        
        header("location:login.php");
        exit();
        break;
}
?>