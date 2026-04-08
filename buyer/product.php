<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

try {
    // Ordering randomly
    $random_func = ($db_type === 'pgsql') ? 'RANDOM()' : 'RAND()';
    $query = "SELECT * FROM product_details ORDER BY $random_func"; 
    $stmt_products = $conn->query($query);
    $products = $stmt_products->fetchAll();

    $username = $_SESSION['username'] ?? null;
    $buyer_id = null;
    $cart_row = ['product_count' => 0];

    if ($username) {
        $stmt_buyer = $conn->prepare("SELECT buyer_id FROM buyer_details WHERE email = :email");
        $stmt_buyer->execute(['email' => $username]);
        $buyer_id_row = $stmt_buyer->fetch();
        
        if ($buyer_id_row) {
            $buyer_id = $buyer_id_row['buyer_id'];
            
            // Fetch cart count
            $cartcount_query = "SELECT COUNT(*) AS product_count FROM cart_details WHERE buyer_id = :id";
            $stmt_cartcount = $conn->prepare($cartcount_query);
            $stmt_cartcount->execute(['id' => $buyer_id]);
            $cart_row = $stmt_cartcount->fetch();
        }
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
            $existing_item = $stmt_check->fetch();
            
            if($existing_item) {
                // If the product already exists, update the quantity
                $stmt_update = $conn->prepare("UPDATE cart_details SET quantity = quantity + :quantity WHERE product_id = :product_id AND buyer_id = :buyer_id");
                $stmt_update->execute([
                    'quantity' => $quantity,
                    'product_id' => $product_id,
                    'buyer_id' => $buyer_id
                ]);
                echo "<script>alert('Quantity updated successfully.')</script>";
            } else {
                // If the product does not exist, insert a new entry
                $stmt_insert = $conn->prepare("INSERT INTO cart_details (product_id, buyer_id, quantity) VALUES (:product_id, :buyer_id, :quantity)");
                $stmt_insert->execute([
                    'product_id' => $product_id,
                    'buyer_id' => $buyer_id,
                    'quantity' => $quantity
                ]);
                echo "<script>alert('Product added to cart successfully.')</script>";
            }
        }

        // Redirect to prevent form resubmission
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
    <title>Products</title>
    <link rel="stylesheet" href="home.css">
    <link rel="icon" href="../images/titlelogo.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <script>
    function reloadPage() {
        location.reload();
    }
    </script>
</head>
<body>

<section id="header">
    <a onclick="reloadPage()"><img src="../images/homelogo.png" class="logo"></a>
    <div>
        <ul id="navbar">
            <li class="module"><a href="index.php">Home</a></li>
            <li class="module"><a class="active" href="product.php">Products</a></li>
            <li class="module"><a href="shop.php">Shop</a></li>
            <li class="module"><a href="about.php">About</a></li>
            <li class="module"><a href="contact.php">Contact</a></li>
            <li class="icon">
                <div class="cart">
                    <a href="cart.php"><ion-icon name="cart-outline"></ion-icon></a>
                    <?php if (isset($cart_row['product_count']) && $cart_row['product_count'] > 0): ?>
                        <sup><?php echo $cart_row['product_count']; ?></sup>
                    <?php endif; ?>
                </div>
            </li>
            <li class="dropdown"><a href="#" class="dropbtn"><ion-icon name="person-outline"></ion-icon></a>
                <div class="dropdown-content">
                      <a href="profile.php"><ion-icon name="person-circle-outline"></ion-icon> Profile</a>
                      <a href="order.php"><ion-icon name="cube-outline"></ion-icon> Orders</a>
                      <a href="../login/logout.php"><ion-icon name="log-out-outline"></ion-icon> Log Out</a>
                </div>
            </li>
        </ul>
    </div>
</section>

<section id="page-hadder">
    <h2>Grow your own</h2>
</section><br><br>

<section id="product1" class="section-p1">
    <h2>Featured Products</h2>
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

            <div class="pro" onclick="window.location.href='product_detail.php?product_id=<?php echo $product_id; ?>'">
            <img src="<?php echo $image; ?>" alt="">
            <div class="des">
                <h5><?php echo htmlspecialchars($name); ?></h5>
                
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
            <form method="post" class="cart_form" action="" onclick="event.stopPropagation();">
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

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
<?php include ("footer.php"); ?>
</html>