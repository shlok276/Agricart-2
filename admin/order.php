<?php 
include ("../database/connection.php");
include ("../session/session_start.php");
include("../session/session_check.php");

$orders = [];
$totalOrders = 0;
$totalIncome = 0;

try {
    // Main query to fetch details of all orders
    $query = "SELECT o.order_id, o.order_no, p.name AS product_name, bd.full_name AS buyer_name, 
              sd.first_name AS seller_name, o.payment, o.price, o.quantity, o.status, o.order_date, o.tracking_no
              FROM order_details o 
              JOIN product_details p ON o.product_id = CAST(p.product_id AS VARCHAR) -- Handle potential type mismatch if needed
              JOIN buyer_details bd ON o.buyer_id = bd.buyer_id 
              JOIN seller_details sd ON o.seller_id = sd.seller_id";
    
    // Note: product_id in order_details might be VARCHAR in some schemas, while serial in product_details.
    // In our schema check, order_details.product_id is VARCHAR(50).
    
    $stmt = $conn->query($query);
    $orders = $stmt->fetchAll();

    // Total orders
    $stmt_count = $conn->query("SELECT COUNT(*) FROM order_details");
    $totalOrders = $stmt_count->fetchColumn();

    // Total income
    $stmt_income = $conn->query("SELECT SUM(price * quantity) FROM order_details");
    $totalIncome = $stmt_income->fetchColumn() ?? 0;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List</title>
    <link rel="icon" href="../images/titlelogo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <?php include ("navbar.php"); ?>

    <div class="main-content">
        <header>
            <div class="header-title-wrapper">
                <div class="header-title">
                    <h1>Orders</h1>
                    <p>Display Information About Website Orders <span class="las la-chart-lin"></span></p>
                </div>
            </div>
        </header>

        <main>
            <section>
                <h3 class="section-head">Overview</h3>
                <div class="analytics">
                    <div class="analytic">
                        <div class="analytic-icon">
                            <span class="las la-eye"></span>
                        </div>
                        <div class="analytic-info">
                            <h4>Total Sales</h4>
                            <h1>₹<?php echo number_format($totalIncome, 2); ?></h1>
                        </div>
                    </div>
                    <div class="analytic">
                        <div class="analytic-icon">
                            <span class="las la-store"></span>
                        </div>
                        <div class="analytic-info">
                            <h4>Orders</h4>
                            <h1><?php echo $totalOrders; ?></h1>
                        </div>
                    </div>
                </div>
            </section>

            <div class="table-data">
                <div class="order">
                <div class="head">
                <h3>Total Order</h3>
                <form id="download">
                    <button type="button" class="d" onclick="downloadCSV()"><i class="fa-solid fa-file-export"></i></button>
                 </form>
            </div>
                    <div class="table-data">
                        <div class="order">
                            <table id="table">
                                <thead>
                                    <tr>
                                        <th>Order No.</th>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Payment</th>
                                        <th>Order Status</th>
                                        <th>Tools</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (!empty($orders)) {
                                        foreach ($orders as $row) {
                                    ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['order_no'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($row['product_name'] ?? ''); ?></td>
                                                <td>₹<?php echo number_format($row['price'] ?? 0); ?></td>
                                                <td><?php echo ($row['payment'] == 0) ? 'Cash' : 'Online'; ?></td>
                                                <td><?php echo ($row['status'] == 0) ? 'Pending' : 'Shipped'; ?></td>
                                                <td>
                                                    <button class="a" onclick="openPopup(<?php echo $row['order_id']; ?>)"><i class="fa-solid fa-magnifying-glass"></i> Views</button>
                                                    <div class="overlay" id="overlay_<?php echo $row['order_id']; ?>">
                                                        <div class="popup">
                                                            <span class="close-btn" onclick="closePopup(<?php echo $row['order_id']; ?>)">×</span>
                                                            <h2>Order Details</h2>
                                                            <form>
                                                            <div style="max-height: 400px; overflow-y: auto;">
                                                                <table>
                                                                    <tr>
                                                                        <td>Order Number</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['order_no'] ?? ''); ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tracking Number</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['tracking_no'] ?? '-'); ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Product Name</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['product_name'] ?? ''); ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Buyer Name</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['buyer_name'] ?? ''); ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Seller Name</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['seller_name'] ?? ''); ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Price</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;">₹<?php echo number_format($row['price'] ?? 0); ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Quantity</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo $row['quantity'] ?? 0; ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Payment</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo ($row['payment'] == 0) ? 'Cash' : 'Online'; ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Order Status</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo ($row['status'] == 0) ? 'Pending' : 'Shipped'; ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Order Date</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo $row['order_date'] ?? ''; ?></div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {?>
                                        <tr>
                                            <td colspan="6">
                                                <p class='no-data-found'>No order data found.</p>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                   ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function openPopup(orderId) {
            document.getElementById("overlay_" + orderId).style.display = "flex";
        }

        function closePopup(orderId) {
            document.getElementById("overlay_" + orderId).style.display = "none";
        }
        function downloadCSV() {
            var downloadWindow = window.open('fetch_details/fetch_order_details.php', '_blank');
            downloadWindow.focus();
        }
    </script>
    
</body>

</html>