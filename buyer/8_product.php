<?php
include("../database/connection.php");


try {
    // Ordering randomly and limiting to 8 products
    // Note: MySQL uses RAND(), PostgreSQL uses RANDOM()
    $random_func = ($db_type === 'pgsql') ? 'RANDOM()' : 'RAND()';
    $query = "SELECT * FROM product_details ORDER BY $random_func LIMIT 8"; 
    $stmt_products = $conn->query($query);
    $products = $stmt_products->fetchAll();

    $username = $_SESSION['username'] ?? null;
    $buyer_id = null;

    if ($username) {
        $stmt_buyer = $conn->prepare("SELECT buyer_id FROM buyer_details WHERE email = :email");
        $stmt_buyer->execute(['email' => $username]);
        $buyer_id_row = $stmt_buyer->fetch();
        $buyer_id = $buyer_id_row['buyer_id'] ?? null;
    }

    // Check if add to cart button is clicked
    if(isset($_POST['add_to_cart_short_cut'])) {
        $product_id = $_POST['product_id'];
        $quantity = 1; 

        if (!$buyer_id) {
            echo "<script>alert('Please login to add products to cart.')</script>";
        } else {
            // Check if the product already exists in the cart
            $stmt_check = $conn->prepare("SELECT * FROM cart_details WHERE product_id = :product_id AND buyer_id = :buyer_id");
            $stmt_check->execute(['product_id' => $product_id, 'buyer_id' => $buyer_id]);
            $existing_cart_item = $stmt_check->fetch();
            
            if($existing_cart_item) {
                // If the product already exists, update the quantity
                $stmt_update = $conn->prepare("UPDATE cart_details SET quantity = quantity + :quantity WHERE product_id = :product_id AND buyer_id = :buyer_id");
                $run_update = $stmt_update->execute([
                    'quantity' => $quantity,
                    'product_id' => $product_id,
                    'buyer_id' => $buyer_id
                ]);

                if($run_update) {
                    echo "<script>alert('Quantity updated successfully.')</script>";
                } else {
                    echo "<script>alert('Failed to update quantity. Please try again.')</script>";
                }
            } else {
                // If the product does not exist, insert a new entry
                $stmt_insert = $conn->prepare("INSERT INTO cart_details (product_id, buyer_id, quantity) VALUES (:product_id, :buyer_id, :quantity)");
                $run_insert = $stmt_insert->execute([
                    'product_id' => $product_id,
                    'buyer_id' => $buyer_id,
                    'quantity' => $quantity
                ]);

                if($run_insert) {
                    echo "<script>alert('Product added to cart successfully.')</script>";
                } else {
                    echo "<script>alert('Failed to add product to cart. Please try again.')</script>";
                }
            }
        }

        // Redirect to the same page to prevent form resubmission
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agricart</title>
    <link rel="stylesheet" href="home.css">
    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <section id="product1" class="section-p1">
        <h2>Similar Products</h2>
        <p>Specially for Organic Farming</p>
        <div class="pro-container">
        <?php
            // Loop through each product fetched from the database
            foreach ($products as $row) {
                $image = empty($row['photo']) ? '../images/xyz.png' : '../images/' . $row['photo'];
                $product_id = $row['product_id']; 
                $name = $row['name'];
                $price = $row['price'];
            ?>
                <div class="pro">
                <a href="product_detail.php?product_id=<?php echo $product_id; ?>">
                    <img src="<?php echo $image; ?>" alt="">
                </a>
                <div class="des">
                    <span><h5><?php echo htmlspecialchars($name); ?></h5></span>
                    
                    <?php 
                        if ($row['quantity'] > 0) {
                            ?>
                            <h4>₹<?php echo $price; ?></h4>
                        <?php } else {
                            ?>
                            <h2 class="out-of-stock" style="color: red;">Out of Stock</h2>
                            <?php
                        } 
                    ?>
                </div>

                <form method="post" class="cart_form" action="">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <?php if ($row['quantity'] > 0): ?>
                        <button class="cart" type="submit" name="add_to_cart_short_cut">
                            <a><ion-icon name="cart-outline"></ion-icon></a>
                        </button>
                    <?php endif; ?>
                </form>

            </div>
            <?php
            }
        ?>
        </div>
    </section>
</body>
</html>