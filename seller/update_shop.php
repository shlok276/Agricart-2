<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shop_id = $_POST['shop_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $email = $_POST['email'];
    $contact_no = $_POST['contact_no'];
    $location = $_POST['location'];
    $time = $_POST['timing'] ?? $_POST['timming']; // Handle both spellings
    $contact_person = $_POST['contact_person'];

    try {
        // Check if a new image file is uploaded
        if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../images/";
            $filename = uniqid() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $stmt_img = $conn->prepare("UPDATE shop_details SET photo = :photo WHERE shop_id = :id");
                $stmt_img->execute(['photo' => $filename, 'id' => $shop_id]);
            }
        }

        // Update the database with other shop details
        $sql = "UPDATE shop_details SET name = :name, address = :addr, city = :city, email = :email, 
                contact_no = :cno, time = :time, contact_person = :cperson, location = :loc 
                WHERE shop_id = :id";
        
        $stmt_update = $conn->prepare($sql);
        $stmt_update->execute([
            'name' => $name,
            'addr' => $address,
            'city' => $city,
            'email' => $email,
            'cno' => $contact_no,
            'time' => $time,
            'cperson' => $contact_person,
            'loc' => $location,
            'id' => $shop_id
        ]);

        header("Location: shop.php?alert=update_success");
        exit;
    } catch (PDOException $e) {
        die("Update failed: " . $e->getMessage());
    }
}
?>
