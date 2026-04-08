<?php
include("../session/session_start.php");
include("../database/connection.php");

$totalPrice = 0;
$shippingCharges = 0;
$cart_items = [];

// Check if 'username' session variable is set
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    try {
        $stmt_buyer = $conn->prepare("SELECT buyer_id FROM buyer_details WHERE email = :email");
        $stmt_buyer->execute(['email' => $username]);
        $buyer_id_row = $stmt_buyer->fetch();
        
        if ($buyer_id_row) {
            $buyer_id = $buyer_id_row['buyer_id'];

            // Handle product removal
            if(isset($_GET['remove_product'])) {
                $product_id = $_GET['remove_product'];
                $stmt_remove = $conn->prepare("DELETE FROM cart_details WHERE buyer_id = :buyer_id AND product_id = :product_id");
                $run_remove = $stmt_remove->execute(['buyer_id' => $buyer_id, 'product_id' => $product_id]);

                if($run_remove) {
                    header("Location: cart.php"); 
                    exit();
                } else {
                    echo "Error removing product from cart";
                }
            }

            // Fetch cart items
            $query = "SELECT cart_details.*, product_details.name, product_details.price, product_details.photo FROM cart_details 
                      INNER JOIN product_details ON cart_details.product_id::integer = product_details.product_id
                      WHERE cart_details.buyer_id = :buyer_id";
            // Note: PostgreSQL cast ::integer used here, matching seller/index.php fix
            if ($db_type !== 'pgsql') {
                $query = str_replace('::integer', '', $query);
            }

            $stmt_cart = $conn->prepare($query);
            $stmt_cart->execute(['buyer_id' => $buyer_id]);
            $cart_items = $stmt_cart->fetchAll();

            foreach ($cart_items as $row) {
                $product_price = $row['price'];
                $quantity = $row['quantity'];
                $subtotal = $product_price * $quantity;
                $totalPrice += $subtotal;
                
                if ($subtotal < 150) {
                    $shippingCharges += 20;
                }
            }
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

$totalWithShipping = $totalPrice + $shippingCharges;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="home.css">
    <link rel="icon" href="../images/titlelogo.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <script>
        function reloadPage() {
            location.reload();
        }
        
        function prepareCheckout() {
            var products = [];
            var rows = document.querySelectorAll("#cart tbody tr");
            rows.forEach(function(row) {
                if (row.querySelector(".product_id")) {
                    var product = {
                        product_id: row.querySelector(".product_id").value,
                        price: row.querySelector(".price").textContent.replace("₹", "").trim(),
                        quantity: row.querySelector(".quantity").textContent.trim(),
                    };
                    products.push(product);
                }
            });
            document.getElementById("product_details").value = JSON.stringify(products);
        }
    </script>
</head>
<body>
    <section id="header">
        <a onclick="reloadPage()"><img src="../images/homelogo.png" class="logo"></a>
        <div>
            <ul id="navbar">
                <li class="module"><a href="index.php">Home</a></li>
                <li class="module"><a href="product.php">Products</a></li>
                <li class="module"><a href="shop.php">Shop</a></li>
                <li class="module"><a href="about.php">About</a></li>
                <li class="module"><a href="contact.php">Contact</a></li>
                <li class="icon"><a href="cart.php"><ion-icon name="cart-outline"></ion-icon></a></li>
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

    <section id="page-hadder" class="contct-hadder">
        <h2>Cart</h2>
        <p>Purchase products at best price</p>
    </section>

    <section id="cart" class="section-p1">
        <table width="100%">
            <thead>
                <tr>
                    <td>Remove</td>
                    <td>Images</td>
                    <td>Product</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td>Subtotal</td>
                </tr>
            </thead>
            <tbody>
                <?php
                if(!empty($cart_items)) {
                    foreach ($cart_items as $row) {
                        $product_name = $row['name'];
                        $product_price = $row['price'];
                        $quantity = $row['quantity'];
                        $subtotal = $product_price * $quantity;
                ?>
                        <tr>
                            <td><a href="javascript:void(0)" onclick="removeProduct(<?php echo $row['product_id']; ?>)"><ion-icon name="close-circle-outline"></ion-icon></a></td>
                            <td><img src="../images/<?php echo empty($row['photo']) ? 'abc2.jpeg' : $row['photo']; ?>" alt="Product Image"></td>
                            <td><?php echo htmlspecialchars($product_name); ?></td>
                            <td class="price">₹<?php echo $product_price; ?></td>
                            <td class="quantity"><?php echo $quantity; ?></td>
                            <td>₹<?php echo $subtotal; ?></td>
                            <input type="hidden" class="product_id" value="<?php echo $row['product_id']; ?>">
                        </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='6'>No items in the cart</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <?php if($totalPrice > 0): ?>
    <section id="cart-parts" class="section-p1"> 
        <div id="subtotal">
            <h3>Cart Total</h3>
            <table>
                <tr>
                    <td>Cart Subtotal</td>
                    <td>₹<?php echo number_format($totalPrice, 2); ?></td>
                </tr>
                <tr>
                    <td>Shipping Charges</td>
                    <td><?php echo ($shippingCharges == 0) ? 'Free' : "₹" . number_format($shippingCharges, 2); ?></td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td>₹<?php echo number_format($totalWithShipping, 2);?></td>
                </tr>
            </table>
            <button type="button" class="normal" onclick="openPopup()">Proceed To Checkout</button>
                
                    <div class="overlay" id="overlay">
                        <div class="popup">
                            <span class="close-btn" onclick="closePopup()">×</span>
                            <div class="pay_main">
                                <div class="pay_1">
                                    <center>
                                        <h3>Online Payment</h3>
                                        <img src="../images/payment-badge.png" class="logo-pay"><br>
                                        <a href="online.php"><button type="button" class="normal" >Pay Online</button></a>
                                    </center>
                                </div>
                                <div class="pay_2">
                                    <center>
                                        <h3>Cash on Delivery</h3>
                                        <img src="../images/cod.jpg" class="logo-cod"><br>
                                        <form method="post" action="checkout_process.php" id="checkout_form">
                                            <input type="hidden" name="product_details" id="product_details">
                                            <input type="hidden" name="shipping_charges" value="<?php echo $shippingCharges; ?>">
                                            <button type="submit" name="proceed_to_checkout" class="normal" onclick="prepareCheckout()">Pay With Cash</button>
                                        </form>
                                    </center>
                                </div>
                        </div>     
                    </div>
                </div>
        </div>
    </section>
    <?php endif; ?>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        function openPopup() {
            document.getElementById("overlay").style.display = "flex";
        }
        function closePopup() {
            document.getElementById("overlay").style.display = "none";
        }
        function removeProduct(productId) {
            var confirmation = confirm('Are you sure you want to remove the product?');
            if (confirmation) {
                window.location.href = 'cart.php?remove_product=' + productId;
            }
        }
    </script>
</body>
<?php include ("footer.php"); ?>
</html>
