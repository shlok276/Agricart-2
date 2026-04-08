<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Retrieve form data
        $product_id = $_POST['product_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $mrp = $_POST['mrp'] ?? $_POST['price']; // Fallback if missing
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];

        // File upload handling helper
        function handleUpload($file_key, $product_id, $column_name, $conn) {
            if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == UPLOAD_ERR_OK) {
                $filename = basename($_FILES[$file_key]["name"]);
                $target_file = "../images/" . $filename;
                
                if (move_uploaded_file($_FILES[$file_key]["tmp_name"], $target_file)) {
                    $stmt = $conn->prepare("UPDATE product_details SET $column_name = :file WHERE product_id = :id");
                    $stmt->execute(['file' => $filename, 'id' => $product_id]);
                    return true;
                }
            }
            return false;
        }

        // Handle images 1, 2, and 3
        handleUpload('image', $product_id, 'photo', $conn);
        handleUpload('image2', $product_id, 'photo2', $conn);
        handleUpload('image3', $product_id, 'photo3', $conn);

        // Update the database with other product details
        $update_sql = "UPDATE product_details SET name = :name, description = :desc, mrp = :mrp, price = :price, quantity = :qty WHERE product_id = :id";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->execute([
            'name' => $name,
            'desc' => $description,
            'mrp' => $mrp,
            'price' => $price,
            'qty' => $quantity,
            'id' => $product_id
        ]);

        // Redirect back to the product page after updating
        header("Location: products.php?alert=update_success");
        exit;
    } catch (PDOException $e) {
        die("Update failed: " . $e->getMessage());
    }
}
?>
