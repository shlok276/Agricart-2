<?php 
include ("../session/session_start.php");
include("../session/session_check.php");
include("../database/connection.php");

try {
    // Total orders
    $stmt = $conn->query("SELECT COUNT(*) AS totalOrders FROM order_details");
    $totalOrders = $stmt->fetchColumn();

    // Total buyers
    $stmt = $conn->query("SELECT COUNT(*) AS totalBuyers FROM buyer_details");
    $totalBuyers = $stmt->fetchColumn();

    // Total sellers
    $stmt = $conn->query("SELECT COUNT(*) AS totalSeller FROM seller_details");
    $totalSeller = $stmt->fetchColumn();

    // Total products
    $stmt = $conn->query("SELECT COUNT(*) AS totalProduct FROM product_details");
    $totalProduct = $stmt->fetchColumn();

    // Calculate total income
    $stmt = $conn->query("SELECT SUM(price) AS totalIncome FROM order_details");
    $totalIncome = $stmt->fetchColumn() ?? 0;

    // Monthly income — use compatible syntax for MySQL/PostgreSQL
    // PostgreSQL uses TO_CHAR, MySQL uses DATE_FORMAT
    if ($db_type === 'pgsql') {
        $monthlySQL = "SELECT TO_CHAR(od.order_date, 'Mon YYYY') AS month_year, SUM(od.price * od.quantity) AS monthlyIncome
                       FROM order_details od
                       GROUP BY TO_CHAR(od.order_date, 'YYYY-MM'), TO_CHAR(od.order_date, 'Mon YYYY')
                       ORDER BY MIN(od.order_date)";
    } else {
        $monthlySQL = "SELECT DATE_FORMAT(od.order_date, '%b %Y') AS month_year, SUM(od.price * od.quantity) AS monthlyIncome
                       FROM order_details od
                       GROUP BY DATE_FORMAT(od.order_date, '%Y-%m')
                       ORDER BY od.order_date";
    }

    $stmt_monthly = $conn->query($monthlySQL);
    $months = [];
    $incomeData = [];
    foreach ($stmt_monthly->fetchAll() as $row) {
        $months[] = $row['month_year'];
        $incomeData[] = $row['monthlyincome'] ?? $row['monthlyIncome'];
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
    <title>Admin Dashboard</title>
    <link rel="icon" href="../images/titlelogo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
   
<?php include ("navbar.php"); ?>
    <div class="main-content">
        <header>
            <div class="header-title-wrapper">
                <div class="header-title">
                    <h1>Dashboard</h1>
                    <p>Display Analytics About Website <span class="las la-chart-lin"></span></p>
                </div>
            </div>
        </header>

        <main>
            <section>
                <h3 class="section-head">Overview</h3>
                <div class="analytics">
                    <div class="analytic">
                    <i class="fa-solid fa-cart-shopping"></i>
                        <div class="analytic-info">
                            <h4>Sales</h4>
                            <h1><?php echo $totalOrders; ?></h1>
                        </div>
                    </div>
                    <div class="analytic">
                    <i class="fa-solid fa-users"></i>
                        <div class="analytic-info">
                            <h4>Buyer</h4>
                            <h1><?php echo $totalBuyers; ?></h1>
                        </div>
                    </div>
                    <div class="analytic">
                    <i class="fa-solid fa-users"></i>
                        <div class="analytic-info">
                            <h4>Seller</h4>
                            <h1><?php echo $totalSeller; ?></h1>
                        </div>
                    </div>
                    <div class="analytic">
                    <i class="fa-solid fa-barcode"></i>
                        <div class="analytic-info">
                            <h4>Products</h4>
                            <h1><?php echo $totalProduct; ?></h1>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="block-grid">
                    <div class="revenue-card">
                        <h3 class="section-head">Total Revenue</h3>
                       <div class="rev-content">
                        <img src="../images/adminrevenue.jpeg" alt="">
                        <div class="rev-info">
                            <h3>Admin</h3>
                            <h1><?php echo $totalBuyers; ?> <small> Buyers</small></h1>
                        </div>
                        <div class="rev-sum">
                            <h4>Total Income</h4>
                            <h2>₹<?php echo number_format($totalIncome, 2); ?></h2>
                        </div>
                       </div>
                    </div>

                    <div class="graph-card">
                        <h3 class="section-head">Graph</h3>
                        <div class="graph-content">
                            <div class="graph-board" id="d">
                                <button onclick="downloadGraph()"><i class="fa-solid fa-file-export"></i></button>
                                <canvas id="revenueChart" weight="100%" height="50px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let ctx = document.querySelector("#revenueChart");
        ctx.height = 150;

        let revChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [
                    {
                        label: "Monthly Income",
                        borderColor: "green",
                        borderWidth: "3",
                        backgroundColor: "rgba(235, 247, 245, 0.7)",
                        data: <?php echo json_encode($incomeData); ?>
                    },
                ]
            },
            options: {
                responsive: true,
                tooltips: {
                    intersect: false,
                    node: "index",
                }
            }
        });
       
    function downloadGraph() {
        var dataURL = revChart.toBase64Image();
        var link = document.createElement("a");
        link.href = dataURL;
        link.download = "revenue_chart.png";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
</body>
</html>