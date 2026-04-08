<?php
include("../database/connection.php");
include("../session/session_start.php");

$username = $_SESSION['username'] ?? null;
$buyer_id = null;
$name = $mrp = $price = $quantity = $description = $image1 = $image2 = $image3 = null;
$cart_row = ['product_count' => 0];

try {
    if ($username) {
        $stmt_buyer = $conn->prepare("SELECT buyer_id FROM buyer_details WHERE email = :email");
        $stmt_buyer->execute(['email' => $username]);
        $buyer_id_row = $stmt_buyer->fetch();
        $buyer_id = $buyer_id_row['buyer_id'] ?? null;
    }

    if(isset($_GET['product_id'])) {
        $product_id = $_GET['product_id'];
        $stmt_prod = $conn->prepare("SELECT * FROM product_details WHERE product_id = :id");
        $stmt_prod->execute(['id' => $product_id]);
        $row = $stmt_prod->fetch();

        if($row) {
            $name = $row['name'];
            $mrp = $row['mrp'];
            $price = $row['price'];
            $quantity = $row['quantity'];
            $description = $row['description'];
            $image1 = empty($row['photo']) ? 'xyz.png' : $row['photo'];
            $image2 = empty($row['photo2']) ? 'xyz.png' : $row['photo2'];
            $image3 = empty($row['photo3']) ? 'xyz.png' : $row['photo3'];
        }
    }

    if(isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id'];
        $requested_quantity = (int)$_POST['quantity'];

        if (!$buyer_id) {
            echo "<script>alert('Please login to add products to cart.')</script>";
        } elseif($requested_quantity > $quantity) {
            echo "<script>alert('Not enough quantity available.')</script>";
        } else {
            $stmt_check = $conn->prepare("SELECT * FROM cart_details WHERE product_id = :pid AND buyer_id = :bid");
            $stmt_check->execute(['pid' => $product_id, 'bid' => $buyer_id]);
            $existing_cart = $stmt_check->fetch();
            
            if($existing_cart) {
                $existing_quantity = (int)$existing_cart['quantity'];
                $new_quantity = $existing_quantity + $requested_quantity;

                if($new_quantity > $quantity) {
                    echo "<script>alert('Not enough quantity available.')</script>";
                } else {
                    $stmt_update = $conn->prepare("UPDATE cart_details SET quantity = :new_qty WHERE product_id = :pid AND buyer_id = :bid");
                    $stmt_update->execute(['new_qty' => $new_quantity, 'pid' => $product_id, 'bid' => $buyer_id]);
                    echo "<script>alert('Quantity updated successfully.')</script>";
                }
            } else {
                $stmt_insert = $conn->prepare("INSERT INTO cart_details (product_id, buyer_id, quantity) VALUES (:pid, :bid, :qty)");
                $stmt_insert->execute(['pid' => $product_id, 'bid' => $buyer_id, 'qty' => $requested_quantity]);
                echo "<script>alert('Product added to cart successfully.')</script>";
            }
        }
        header("Location: ".$_SERVER['PHP_SELF']."?product_id=$product_id");
        exit();
    }

    if ($buyer_id) {
        $stmt_cartcount = $conn->prepare("SELECT COUNT(*) AS product_count FROM cart_details WHERE buyer_id = :bid");
        $stmt_cartcount->execute(['bid' => $buyer_id]);
        $cart_row = $stmt_cartcount->fetch();
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
    <title>Products Details</title>
    <link rel="stylesheet" href="home.css">
    <link rel="icon" href="../images/titlelogo.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <style>
        .out-of-stock {
            color: red;
        }
    </style>
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

<section id="productdetails" class="section-p1">
    <?php if(isset($name)): ?>
    <div class="single-product-image">
        <img src="../images/<?php echo htmlspecialchars($image1); ?>" width="100%" id="MainImg" alt="Main Image">

        <div class="small-img-group">
            <div class="small-img-col">
                <img src="../images/<?php echo htmlspecialchars($image1); ?>" width="100%" class="small-img" alt="Small Image 1">
            </div>
            <div class="small-img-col">
                <img src="../images/<?php echo htmlspecialchars($image2); ?>" width="100%" class="small-img" alt="Small Image 2">
            </div>
            <div class="small-img-col">
                <img src="../images/<?php echo htmlspecialchars($image3); ?>" width="100%" class="small-img" alt="Small Image 3">
            </div>
        </div>
    </div>

    <div class="single-product-details">
        <h1><?php echo htmlspecialchars($name); ?></h1>
        <br>
        <?php if($quantity > 0): ?>
            <div class="mrp">
                <b><strike>₹<?php echo $mrp; ?></strike></b>
                <h2>₹<?php echo $price; ?></h2>
            </div>
            <form method="post" action="">
                <br>
                <input type="number" name="quantity" value="1" min="1" max="<?php echo $quantity; ?>">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <button type="submit" name="add_to_cart" class="normal">Add To Cart</button>
            </form>
        <?php else: ?>
            <h2 class="out-of-stock">Out of Stock</h2>
        <?php endif; ?>
        <h4>Product Details</h4>
        <span><?php echo nl2br(htmlspecialchars($description)); ?></span>
    </div>
    <?php else: ?>
    <center><p>No product found.</p></center>
    <?php endif; ?>
</section>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<script>
   var MainImg = document.getElementById("MainImg");
   var smallimg = document.getElementsByClassName("small-img");

   for(let i=0; i<smallimg.length; i++) {
       smallimg[i].onclick = function() {
            MainImg.src = smallimg[i].src;
       }
   }
</script>

</body>
<?php
include ("8_product.php");
include ("footer.php");
?>
</html>
