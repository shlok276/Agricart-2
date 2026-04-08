<?php
include("../session/session_start.php");
include("../database/connection.php");

/**
 * Function to reduce the quantity of a product in the product_details table
 */
function reduceProductQuantity($product_id, $quantity) {
    global $conn;

    try {
        // Retrieve the current quantity of the product
        $stmt_get = $conn->prepare("SELECT quantity FROM product_details WHERE product_id = :id");
        $stmt_get->execute(['id' => $product_id]);
        $row = $stmt_get->fetch();

        if (!$row) return false;

        // Calculate the new quantity after deduction
        $current_quantity = (int)$row['quantity'];
        $new_quantity = $current_quantity - (int)$quantity;

        // Update the quantity of the product in the database
        $stmt_update = $conn->prepare("UPDATE product_details SET quantity = :qty WHERE product_id = :id");
        return $stmt_update->execute(['qty' => $new_quantity, 'id' => $product_id]);
    } catch (PDOException $e) {
        error_log("Error in reduceProductQuantity: " . $e->getMessage());
        return false;
    }
}

// Check if required session variables are set
if (isset($_SESSION['username'])) {
    try {
        $username = $_SESSION['username'];
        $stmt_buyer = $conn->prepare("SELECT buyer_id, full_name, address, state, pin_code FROM buyer_details WHERE email = :email");
        $stmt_buyer->execute(['email' => $username]);
        $buyer_details = $stmt_buyer->fetch();

        if (!$buyer_details) {
            die("Error: Buyer details not found.");
        }

        // Check if any required details are empty
        if (empty($buyer_details['full_name']) || empty($buyer_details['address']) || empty($buyer_details['state']) || empty($buyer_details['pin_code'])) {
            header("Location: profile.php");
            exit();
        }

        $buyer_id = $buyer_details['buyer_id'];

        // Retrieve product details from the hidden input field
        $product_details_json = $_POST['product_details'] ?? '[]';
        $product_details = json_decode($product_details_json, true);

        if (empty($product_details)) {
            die("Error: No products in checkout.");
        }

        // Generate a random 10-digit order number
        $order_no = mt_rand(1000000000, 9999999999);
        $order_date = date("Y-m-d H:i:s");

        // Prepare statements for the loop
        $stmt_seller = $conn->prepare("SELECT seller_id FROM product_details WHERE product_id = :id");
        $stmt_insert = $conn->prepare("INSERT INTO order_details (order_no, product_id, buyer_id, seller_id, price, quantity, order_date) VALUES (:order_no, :pid, :bid, :sid, :price, :qty, :date)");

        // Start transaction (recommended for multiple related inserts)
        $conn->beginTransaction();

        foreach($product_details as $product) {
            $product_id = $product['product_id'];
            $price = $product['price'];
            $quantity = $product['quantity'];

            // Retrieve the seller ID for the product
            $stmt_seller->execute(['id' => $product_id]);
            $seller_row = $stmt_seller->fetch();
            $seller_id = $seller_row['seller_id'] ?? null;

            if (!$seller_id) {
                throw new Exception("Seller not found for product ID: $product_id");
            }

            // Insert product details into the order_details table
            $stmt_insert->execute([
                'order_no' => $order_no,
                'pid' => $product_id,
                'bid' => $buyer_id,
                'sid' => $seller_id,
                'price' => $price,
                'qty' => $quantity,
                'date' => $order_date
            ]);

            // Reduce the quantity of the product
            if (!reduceProductQuantity($product_id, $quantity)) {
                throw new Exception("Error reducing product quantity for product ID: $product_id");
            }
        }

        // Remove all products from the cart_details table for the current buyer
        $stmt_del = $conn->prepare("DELETE FROM cart_details WHERE buyer_id = :bid");
        $stmt_del->execute(['bid' => $buyer_id]);

        // Commit transaction
        $conn->commit();

        $_SESSION['order_no'] = $order_no;
        header("Location: checkout.php");
        exit();

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        die("Error processing checkout: " . $e->getMessage());
    }
} else {
    echo "Error: User is not logged in";
}
?>
