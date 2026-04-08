<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Fetch form data
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $image = $_FILES['image']['name'];
        $gov_id = $_FILES['gov_id']['name'];
        $gst_no = !empty($_POST['gst_no']) ? (int)$_POST['gst_no'] : 0;

        // Move uploaded files to the destination directory
        $target_directory = "../images/";
        $image_target = $target_directory . basename($image);
        $gov_id_target = $target_directory . basename($gov_id);

        if (
            move_uploaded_file($_FILES["image"]["tmp_name"], $image_target) &&
            move_uploaded_file($_FILES["gov_id"]["tmp_name"], $gov_id_target)
        ) {
            // Fetch seller's ID based on the email stored in the session
            $seller_username = $_SESSION['username'];
            $stmt_seller = $conn->prepare("SELECT seller_id FROM seller_details WHERE email = :email");
            $stmt_seller->execute(['email' => $seller_username]);
            $seller_row = $stmt_seller->fetch();
            
            if ($seller_row) {
                $seller_id = $seller_row['seller_id'];

                // Insert data into database
                $insert_query = "UPDATE seller_details SET first_name = :fname, last_name = :lname, photo = :photo, government_id = :gov_id, gst_no = :gst_no WHERE seller_id = :sid";
                $stmt_insert = $conn->prepare($insert_query);
                $run = $stmt_insert->execute([
                    'fname' => $first_name,
                    'lname' => $last_name,
                    'photo' => $image,
                    'gov_id' => $gov_id,
                    'gst_no' => $gst_no,
                    'sid' => $seller_id
                ]);

                if ($run) {
                    header("Location: ../login/s_login.php?alert=inserted");
                    exit();
                } else {
                    header("Location: ../login/s_login.php?alert=error");
                    exit();
                }
            }
        }
        header("Location: ../login/s_login.php?alert=Shop Details Inserted Successfully");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
