<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

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
    <title>Add Shop</title>
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
                        class="fa-solid fa-bars"></i> Add Shop</span>
                <span style="font-size:30px;cursor:pointer; color: rgb(0, 0, 0); display:none;" class="nav2"><i
                        class="fa-solid fa-bars"></i> Add Shop</span>
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
                    <h1>Shop Details</h1>
                    <br />
                    <form action="add_shop_process.php" method="POST" enctype="multipart/form-data">
                        <div class="product-details">
                        <label for="shop_name">Shop Name:</label><br>
                        <input type="text" id="shop_name" name="shop_name" required><br>
                        
                        <label for="description">Shop Address:</label><br>
                        <textarea id="description" name="description" required></textarea><br>
                        
                        <label for="shop_city">Shop City:</label><br>
                        <input type="text" id="shop_city" name="shop_city" required><br>
                        
                        <label for="shop_email">Shop E-mail:</label><br>
                        <input type="email" id="shop_email" name="shop_email" required><br>
                        
                        <label for="contact_number">Contact Number:</label><br>
                        <input type="text" id="contact_number" name="contact_number" required><br>
                        
                        <label for="shop_timing">Shop Timing:</label><br>
                        <input type="text" id="shop_timing" name="shop_timing" required><br>
                        (eg: Monday-Friday 8:30AM To 7:00PM)
                        <br>
                        <br>
                        
                        <label for="contact_person">Contact Person:</label><br>
                        <input type="text" id="contact_person" name="contact_person" required><br>
                        
                        <label for="location_link">Shop Location link:</label><br>
                        <input type="text" id="location_link" name="location_link" required><br>
                        
                        <label for="image1">Upload Shop Image:</label><br>
                        <input type="file" id="image1" name="image1" required><br>

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
                    $alert_message = 'Shop Details Inserted Successfully!';
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
            $(".nav").css('display', 'none');
            $(".nav2").css('display', 'block');
        });

        $(".nav2").click(function () {
            $("#mySidenav").css('width', '300px');
            $("#main").css('margin-left', '300px');
            $(".nav").css('display', 'block');
            $(".nav2").css('display', 'none');
        });
    </script>
</body>
</html>
