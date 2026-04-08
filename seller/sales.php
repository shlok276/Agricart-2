<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

// Fetch seller ID for the specific user from the database
$seller_username = $_SESSION['username'];
$seller_id = null;
$total_sales = 0;
$total_orders = 0;
$orders = [];
$seller_photo = null;

try {
    $stmt_seller = $conn->prepare("SELECT seller_id, photo FROM seller_details WHERE email = :email");
    $stmt_seller->execute(['email' => $seller_username]);
    $seller_row = $stmt_seller->fetch();
    
    if ($seller_row) {
        $seller_id = $seller_row['seller_id'];
        $seller_photo = $seller_row['photo'];

        // Total sales
        $stmt_total_sales = $conn->prepare("SELECT SUM(price * quantity) AS total_sales FROM order_details WHERE seller_id = :sid");
        $stmt_total_sales->execute(['sid' => $seller_id]);
        $total_sales_row = $stmt_total_sales->fetch();
        $total_sales = $total_sales_row['total_sales'] ?? 0;

        // Total orders
        $stmt_total_orders = $conn->prepare("SELECT COUNT(*) AS total_orders FROM order_details WHERE seller_id = :sid");
        $stmt_total_orders->execute(['sid' => $seller_id]);
        $total_orders_row = $stmt_total_orders->fetch();
        $total_orders = $total_orders_row['total_orders'] ?? 0;

        // Detailed sales info
        $order_query = "SELECT od.quantity, od.price, (od.quantity * od.price) AS total, pd.name AS product_name, bd.full_name AS buyer_name
                        FROM order_details od
                        INNER JOIN product_details pd ON od.product_id = pd.product_id
                        INNER JOIN buyer_details bd ON od.buyer_id = bd.buyer_id
                        WHERE od.seller_id = :sid";
        $stmt_detail = $conn->prepare($order_query);
        $stmt_detail->execute(['sid' => $seller_id]);
        $orders = $stmt_detail->fetchAll();
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
    <title>Sales</title>
    <link rel="icon" href="../images/titlelogo.png" type="image/x-icon">
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
                        class="fa-solid fa-bars"></i> Sales</span>
                <span style="font-size:30px;cursor:pointer; color: rgb(0, 0, 0); display:none;" class="nav2"><i
                        class="fa-solid fa-bars"></i> Sales</span>
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
        <div class="col-div-3">
            <div class="box">
                <p><?php echo number_format($total_sales); ?><br /><span>Total Sales</span></p>
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
        </div>

        <div class="col-div-3">
            <div class="box">
                <p><?php echo $total_orders; ?><br /><span>Orders</span></p>
                <i class="fa fa-list box-icon"></i>
            </div>
        </div>

        <div class="clearfix"></div>
        <br /><br />
        <div class="col-div-8">
            <div class="box-8">
                <div class="content">
                    <h1>Total Sales</h1>
                    <br />
                    <table>
                        <tr>
                            <th>SR. no</th>
                            <th>Product Name</th>
                            <th>Buyer Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                        <?php
                        $sr_no = 1;
                        if (!empty($orders)) {
                            foreach ($orders as $row) {?>
                                <tr>
                                <td><?php echo $sr_no++; ?></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo number_format($row['total']); ?></td>
                            </tr>
                            <?php
                            }
                        } else {
                            echo "<tr><td colspan='6'>No sales data found.</td></tr>";
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
