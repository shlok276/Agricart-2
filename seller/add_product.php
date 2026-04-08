<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

// Fetch seller details for the specific user from the database
$seller_username = $_SESSION['username'];
$seller_id = null;
$seller_photo = null;

try {
    $stmt_seller = $conn->prepare("SELECT seller_id, photo FROM seller_details WHERE email = :email");
    $stmt_seller->execute(['email' => $seller_username]);
    $row = $stmt_seller->fetch();
    
    if ($row) {
        $seller_id = $row['seller_id'];
        $seller_photo = $row['photo'];
    } else {
        die("Seller details not found.");
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
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>
    <?php include("navigation.php"); ?>

    <div id="main">
        <div class="head">
            <div class="col-div-6">
                <span style="font-size:30px;cursor:pointer; color: #000e04;" class="nav"><i
                        class="fa-solid fa-bars"></i> Add Product</span>
                <span style="font-size:30px;cursor:pointer; color: rgb(0, 0, 0);" class="nav2"><i
                        class="fa-solid fa-bars"></i> Add Product</span> 
            </div>
            <div class="col-div-6">
            <div class="profile">
                <?php
                    $image = empty($seller_photo) ? '../images/profile.jpg' : '../images/' . $seller_photo;
                    echo "<td><img src='$image' class='pro-img'></td>";
                ?>
                    <p><?php echo htmlspecialchars($seller_username); ?></p>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="clearfix"></div>
        <br />
        <div class="clearfix"></div>
        <br /><br />
        <div class="col-div-8">
            <div class="box-8">
                <div class="content">
                    <h1>Product Details</h1>
                    <br />
                    <form action="add_product_process.php" method="POST" enctype="multipart/form-data">
                        <div class="product-details">
                            <label for="product_name">Product Name:</label><br>
                            <input type="text" id="product_name" name="product_name" required><br>

                            <label for="mrp">MRP:</label><br>
                            <input type="number" min="0" id="mrp" name="mrp" required><br>

                            <label for="price">Selling Price:</label><br>
                            <input type="number" min="0" id="price" name="price" required><br>

                            <label for="quantity">Quantity:</label><br>
                            <input type="number" min="0" id="quantity" name="quantity" required><br>

                            <label for="description">Description:</label><br>
                            <textarea id="description" name="description" required></textarea><br>

                            <label for="image1">Upload Image 1:</label><br>
                            <input type="file" id="image1" name="image1" required><br>

                            <label for="image2">Upload Image 2:</label><br>
                            <input type="file" id="image2" name="image2" required><br>

                            <label for="image3">Upload Image 3:</label><br>
                            <input type="file" id="image3" name="image3" required><br><br>
                        
                            <div class="submit">
                                <input type="submit" value="Submit">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    window.onload = function() {
        <?php
        if(isset($_GET['alert'])) {
            $alert_message = '';
            switch($_GET['alert']) {
                case 'success':
                    $alert_message = 'Product Details Inserted Successfully';
                    break;
                case 'error':
                    $alert_message = 'Error while inserting data!';
                    break;
                default:
                    $alert_message = '';
                    break;
            }
            if($alert_message) {
                echo 'alert("' . $alert_message . '");';
            }
        }
        ?>
    };
</script>
    <script>
        $(".nav").click(function () {
            $("#mySidenav").css('width', '70px');
            $("#main").css('margin-left', '70px');
            $(".logo").css('visibility', 'visible');
            $(".logo span").css('visibility', 'visible');
            $(".logo span").css('margin-left', '-10px');
            $(".icon-a").css('visibility', 'visible');
            $(".icons").css('visibility', 'visible');
            $(".icons").css('margin-left', '-8px');
            $(".nav").css('display', 'none');
            $(".nav2").css('display', 'block');
            $(".img").css('width', '60px');
            $(".img").css('height', '45px');
            $(".white").css('color', 'white');
        });

        $(".nav2").click(function () {
            // Updated expanded width to match other pages
            $("#mySidenav").css('width', '300px');
            $("#main").css('margin-left', '300px');
            $(".logo").css('visibility', 'visible');
            $(".icon-a").css('visibility', 'visible');
            $(".icons").css('visibility', 'visible');
            $(".nav").css('display', 'block');
            $(".nav2").css('display', 'none');
            $(".img").css('width', '160px');
            $(".img").css('height', '110px');
            $(".white").css('color', '#818181');
        });
    </script>
</body>
</html>
