<?php
include("../database/connection.php");
include("../session/session_start.php");
include("../session/session_check.php");

if(isset($_POST['save_profile'])) {
    $username = $_SESSION['username'];

    $full_name = $_POST['first_name'];
    $contact_no = !empty($_POST['contact_no']) ? (int)$_POST['contact_no'] : 0;
    $email = $_POST['email']; // Note: This is readonly in the form but sent anyway
    $address = $_POST['address'];
    $state = $_POST['state'];
    $pincode = !empty($_POST['pin_code']) ? (int)$_POST['pin_code'] : 0;

    try {
        $stmt_update = $conn->prepare("UPDATE buyer_details SET full_name = :full_name, contact_no = :contact_no, address = :address, pin_code = :pincode, state = :state WHERE email = :email");
        $run_update = $stmt_update->execute([
            'full_name' => $full_name,
            'contact_no' => $contact_no,
            'address' => $address,
            'pincode' => $pincode,
            'state' => $state,
            'email' => $username
        ]);

        if($run_update) {
            header("Location: profile.php?alert=profile_update");
            exit();
        } else {
            header("Location: profile.php?alert=update_error");
            exit();
        }
    } catch (PDOException $e) {
        die("Update failed: " . $e->getMessage());
    }
}
?>
