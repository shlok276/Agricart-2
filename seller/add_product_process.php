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

        // Get form data
        $product_name = $_POST['product_name'];
        $mrp = !empty($_POST['mrp']) ? (int)$_POST['mrp'] : 0;
        $price = !empty($_POST['price']) ? (int)$_POST['price'] : 0;
        $quantity = !empty($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        $description = $_POST['description'];

        // Handle file uploads for images
        $image1 = $_FILES['image1']['name'];
        $image2 = $_FILES['image2']['name'];
        $image3 = $_FILES['image3']['name'];

        $image1_tmp = $_FILES['image1']['tmp_name'];
        $image2_tmp = $_FILES['image2']['tmp_name'];
        $image3_tmp = $_FILES['image3']['tmp_name'];

        // Move uploaded images to desired directory
        if ($image1) move_uploaded_file($image1_tmp, "../images/" . $image1);
        if ($image2) move_uploaded_file($image2_tmp, "../images/" . $image2);
        if ($image3) move_uploaded_file($image3_tmp, "../images/" . $image3);

        // Insert data into database
        $sql = "INSERT INTO product_details (seller_id, name, mrp, price, quantity, description, photo, photo2, photo3) 
                VALUES (:sid, :name, :mrp, :price, :qty, :desc, :p1, :p2, :p3)";
        
        $stmt_insert = $conn->prepare($sql);
        $run = $stmt_insert->execute([
            'sid' => $seller_id,
            'name' => $product_name,
            'mrp' => $mrp,
            'price' => $price,
            'qty' => $quantity,
            'desc' => $description,
            'p1' => $image1,
            'p2' => $image2,
            'p3' => $image3
        ]);

        if ($run) {
            header("Location: add_product.php?alert=success");
        } else {
            header("Location: add_product.php?alert=error");
        }
    } catch (PDOException $e) {
        die("Error adding product: " . $e->getMessage());
    }
}
?>
