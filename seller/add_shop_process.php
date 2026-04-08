<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seller_username = $_SESSION['username'];
    
    try {
        $stmt_id = $conn->prepare("SELECT seller_id FROM seller_details WHERE email = :email");
        $stmt_id->execute(['email' => $seller_username]);
        $seller_row = $stmt_id->fetch();
        
        if (!$seller_row) {
            die("Seller not found.");
        }
        $seller_id = $seller_row['seller_id'];

        $shop_name = $_POST['shop_name'];
        $shop_address = $_POST['description'];
        $shop_city = $_POST['shop_city'];
        $shop_email = $_POST['shop_email'];
        $contact_number = !empty($_POST['contact_number']) ? (int)$_POST['contact_number'] : 0;
        $shop_timing = $_POST['shop_timing']; // "time" column in DB
        $contact_person = $_POST['contact_person'];
        $location_link = $_POST['location_link'];

        // File upload handling for shop image
        $shop_image = $_FILES['image1']['name'] ?? '';
        if ($shop_image) {
            $target_dir = "../images/";
            $target_file = $target_dir . basename($shop_image);
            move_uploaded_file($_FILES["image1"]["tmp_name"], $target_file);
        }

        // SQL query to insert data into the database
        $sql = "INSERT INTO shop_details (seller_id, name, address, city, email, contact_no, time, contact_person, location, photo)
                VALUES (:sid, :name, :addr, :city, :email, :cno, :time, :cperson, :loc, :photo)";

        $stmt_insert = $conn->prepare($sql);
        $run = $stmt_insert->execute([
            'sid' => $seller_id,
            'name' => $shop_name,
            'addr' => $shop_address,
            'city' => $shop_city,
            'email' => $shop_email,
            'cno' => $contact_number,
            'time' => $shop_timing,
            'cperson' => $contact_person,
            'loc' => $location_link,
            'photo' => $shop_image
        ]);

        if ($run) {
            header("Location: add_shop.php?alert=success");
        } else {
            header("Location: add_shop.php?alert=error");
        }
    } catch (PDOException $e) {
        die("Error adding shop: " . $e->getMessage());
    }
}
?>
