<?php
include("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

// Fetch seller's username from session
$seller_username = $_SESSION['username'];

try {
    // Query to fetch seller ID using the username
    $stmt_id = $conn->prepare("SELECT seller_id FROM seller_details WHERE email = :email");
    $stmt_id->execute(['email' => $seller_username]);
    $seller_id_row = $stmt_id->fetch();
    
    if (!$seller_id_row) {
        die("Seller not found.");
    }
    $seller_id = $seller_id_row['seller_id'];

    // Query to fetch total sales
    $stmt_sales = $conn->prepare("SELECT SUM(price * quantity) AS total_sales FROM order_details WHERE seller_id = :id");
    $stmt_sales->execute(['id' => $seller_id]);
    $total_sales_row = $stmt_sales->fetch();
    $total_sales = $total_sales_row['total_sales'] ?? 0;

    // Query to fetch total orders
    $stmt_orders = $conn->prepare("SELECT COUNT(*) AS total_orders FROM order_details WHERE seller_id = :id");
    $stmt_orders->execute(['id' => $seller_id]);
    $total_orders_row = $stmt_orders->fetch();
    $total_orders = $total_orders_row['total_orders'] ?? 0;

    // Query to fetch total products
    $stmt_products = $conn->prepare("SELECT COUNT(*) AS total_products FROM product_details WHERE seller_id = :id");
    $stmt_products->execute(['id' => $seller_id]);
    $total_products_row = $stmt_products->fetch();
    $total_products = $total_products_row['total_products'] ?? 0;

    // Query to fetch top 5 selling products
    // Using explicit table aliases for clarity
    // Note: MySQL uses CAST(field AS SIGNED), PostgreSQL uses field::integer
    $cast_sql = ($db_type === 'pgsql') ? "od.product_id::integer" : "CAST(od.product_id AS SIGNED)";
    
    $sql_top = "SELECT pd.name, 
                       SUM(od.quantity) AS total_sold, 
                       SUM(od.quantity * od.price) AS total_revenue
                FROM order_details od
                INNER JOIN product_details pd ON $cast_sql = pd.product_id
                WHERE pd.seller_id = :id
                GROUP BY pd.name, pd.product_id
                ORDER BY total_sold DESC
                LIMIT 5";
    // Force cast to integer if product_id is varchar in table (as seen in some schema versions)
    
    $stmt_top = $conn->prepare($sql_top);
    $stmt_top->execute(['id' => $seller_id]);
    $top_products = $stmt_top->fetchAll();

    // Query to fetch seller photo
    $stmt_photo = $conn->prepare("SELECT photo FROM seller_details WHERE seller_id = :id");
    $stmt_photo->execute(['id' => $seller_id]);
    $row_img = $stmt_photo->fetch();

} catch (PDOException $e) {
    die("Error fetching dashboard data: " . $e->getMessage());
}

?>

<!DOCTYPE HTML>
<html>

<head>
    <title>Dashboard</title>
    <link rel="icon" href="../images/titlelogo.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>
    <?php
    include("navigation.php");
    ?>


    </div>
    <div id="main">

        <div class="head">
            <div class="col-div-6">
                <span style="font-size:30px;cursor:pointer; color: #000e04;" class="nav"><i
                        class="fa-solid fa-bars"></i> Dashboard</span>
                <span style="font-size:30px;cursor:pointer; color: rgb(0, 0, 0);" class="nav2"><i
                        class="fa-solid fa-bars"></i> Dashboard</span>
            </div>
			
            <div class="col-div-6">
                <div class="profile">
                <?php
                    $image = (!empty($row_img) && !empty($row_img['photo'])) ? '../images/' . $row_img['photo'] : '../images/profile.jpg';
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
                <p><?php echo $total_orders; ?><br /><span>Order</span></p>
                <i class="fa fa-list box-icon"></i>
            </div>
        </div>
        <div class="col-div-3">
            <div class="box">
                <p><?php echo $total_products; ?><br /><span>Product</span></p>
                <i class="fa fa-tasks box-icon"></i>
            </div>
        </div>
        <div class="clearfix"></div>
        <br /><br />
        <div class="col-div-8-dash">
            <div class="box-8">
                <div class="content">
                    <p>Top selling products of the month</p>
                    <br />
                    <table>
        <tr>
            <th>Product Name</th>
            <th>Total Quantity Sold</th>
            <th>Total Revenue</th>
        </tr>
        <?php
        if (count($top_products) > 0) {
            foreach($top_products as $row) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["name"]) . "</td>
                        <td>" . $row["total_sold"] . "</td>
                        <td>" . number_format($row["total_revenue"]) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No results found</td></tr>";
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
