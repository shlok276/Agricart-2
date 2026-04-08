<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

// Fetch seller ID for the specific user from the database
$seller_username = $_SESSION['username'];
$seller_id = null;
$orders = [];
$seller_photo = null;

try {
    $stmt_seller = $conn->prepare("SELECT seller_id, photo FROM seller_details WHERE email = :email");
    $stmt_seller->execute(['email' => $seller_username]);
    $seller_row = $stmt_seller->fetch();
    
    if ($seller_row) {
        $seller_id = $seller_row['seller_id'];
        $seller_photo = $seller_row['photo'];

        // Fetch order details for the seller including product details
        // Renamed 'by' alias to 'bd' for PostgreSQL compatibility
        $order_query = "SELECT o.*, p.name, p.photo, p.description, bd.full_name, bd.address, bd.state 
                        FROM order_details o
                        INNER JOIN product_details p ON o.product_id = p.product_id
                        INNER JOIN buyer_details bd ON o.buyer_id = bd.buyer_id
                        WHERE o.seller_id = :sid
                        AND (o.tracking_no IS NULL OR o.tracking_no = '')
                        ORDER BY o.order_id DESC";
        
        $stmt_orders = $conn->prepare($order_query);
        $stmt_orders->execute(['sid' => $seller_id]);
        $orders = $stmt_orders->fetchAll();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>New Orders</title>
    <link rel="icon" href="../images/titlelogo.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>
    <?php
    include("navigation.php");
    ?>

    <div id="main">
        <div class="head">
            <div class="col-div-6">
                <span style="font-size:30px;cursor:pointer; color: #000e04;" class="nav"><i
                        class="fa-solid fa-bars"></i> New Order</span>
                <span style="font-size:30px;cursor:pointer; color: rgb(0, 0, 0); display:none;" class="nav2"><i
                        class="fa-solid fa-bars"></i> New Order</span>
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
                    <p>
                        <h1>New Orders</h1>
                    </p>
                    <br />
                    <table>
                        <tr>
                            <th>SR No.</th>
                            <th>Order Number</th>
                            <th>Photo</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Tools</th>
                        </tr>
                        <?php
                        $sr_no = 1;
                        if (!empty($orders)) {
                            foreach ($orders as $row) {?>
                                <tr>
                                <td><?php echo $sr_no++; ?></td>
                                <td><?php echo htmlspecialchars($row['order_no']); ?></td>
                                <?php
                                    $image = empty($row['photo']) ? '../images/profile.jpg' : '../images/' . $row['photo'];
                                    echo "<td><img src='$image' class='pro-img'></td>";
                                    ?>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td>
                                    <div class="view-button">
                                        <button onclick="openPopup(<?php echo $row['order_id']; ?>)">
                                            <i class='fa-solid fa-magnifying-glass'></i> View
                                        </button>
                                        <div class="overlay" id="overlay_<?php echo $row['order_id'];?>">
                                            <div class="popup">
                                            <span class="close-btn" onclick="closePopup('<?php echo $row['order_id']; ?>')">×</span>
                                                <h2>Order Details</h2>
                                                <form method="POST" action="update_trackingid.php" enctype="multipart/form-data">
                                                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                                    <div style="max-height: 400px; overflow-y: auto;">
                                                        <table>
                                                            <tr>
                                                                <td>Order Number</td>
                                                                <td><?php echo htmlspecialchars($row['order_no']); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Product Name</td>
                                                                <td>
                                                                    <?php echo htmlspecialchars($row['name']); ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Description</td>
                                                                <td>
                                                                    <?php echo htmlspecialchars($row['description']); ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Price</td>
                                                                <td>
                                                                   <?php echo $row['price']; ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Quantity</td>
                                                                <td>
                                                                    <?php echo $row['quantity']; ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Total</td>
                                                                <td>
                                                                    <?php
                                                                        $totalPrice = $row['price'] * $row['quantity'];
                                                                        if ($totalPrice < 150) {
                                                                            $totalPrice += 20; // Add shipping charge if applicable
                                                                        }
                                                                        echo $totalPrice;
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Buyer Name</td>
                                                                <td>
                                                                    <?php echo htmlspecialchars($row['full_name']); ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Buyer Address</td>
                                                                <td>
                                                                    <?php echo htmlspecialchars($row['address']); ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Buyer State</td>
                                                                <td>
                                                                    <?php echo htmlspecialchars($row['state']); ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Tracking Id</td>
                                                                <td>
                                                                    <input type="text" name="tracking_no" value="<?php echo htmlspecialchars($row['tracking_no'] ?? ''); ?>">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <button type="submit" name="save_changes">Save Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                     </div>
                                </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6'>No new orders found.</td></tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    function openPopup(orderId) {
        document.getElementById("overlay_" + orderId).style.display = "flex";
    }

    function closePopup(orderId) {
        document.getElementById("overlay_" + orderId).style.display = "none";
    }

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
