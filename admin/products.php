<?php 
include ("../database/connection.php");
include ("../session/session_start.php");
include("../session/session_check.php");

$products = [];
try {
    $query = "SELECT product_details.*, seller_details.first_name AS seller_name FROM product_details
              LEFT JOIN seller_details ON product_details.seller_id = seller_details.seller_id";
    $stmt = $conn->query($query);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
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
                    <h1>Products</h1>
                    <p>Display Information About Products <span class="las la-chart-lin"></span></p>
                </div>
            </div>
        </header>

        <main>
            <div class="table-data">
                <div class="order">
                <div class="head">
            <h3>Total Products</h3>
            
            <form id="download">
                <button type="button" onclick="downloadCSV()"><i class="fa-solid fa-file-export"></i></button>
            </form>
        </div>
                    <section>
                        <div class="table-data">
                            <div class="order">
                                <table id="table">
                                    <thead>
                                        <tr>
                                            <th>Sr.no</th>
                                            <th>Photo</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $counter = 1;
                                        if (!empty($products)) {
                                            foreach ($products as $row) {
                                        ?>
                                            <tr>
                                                <td><?php echo $counter++;?></td>
                                                <td>
                                                    <?php
                                                    $image = empty($row['photo']) ? '../images/profile.jpg' : '../images/' . $row['photo'];
                                                    echo "<img src='$image' alt='Product Photo' style='width: 50px; height: 50px; border-radius: 50%;'>";
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
                                                <td>₹<?php echo number_format($row['price'] ?? 0); ?></td>
                                                <td><?php echo $row['quantity'] ?? 0; ?></td>
                                                <td>
                                                    <button onclick="openPopup('<?php echo $row['product_id']; ?>')"><i class="fa-solid fa-magnifying-glass"></i> Views</button>
                                                    <div class="overlay" id="overlay_<?php echo $row['product_id']; ?>">
                                                        <div class="popup">
                                                            <span class="close-btn" onclick="closePopup('<?php echo $row['product_id']; ?>')">×</span>
                                                            <h2>Product Details</h2>
                                                            <form>
                                                            <div style="max-height: 400px; overflow-y: auto;">
                                                                <table>
                                                                    <tr>
                                                                        <td>Photo 1</td>
                                                                        <td>
                                                                            <?php
                                                                            $img1 = empty($row['photo']) ? '../images/profile.jpg' : '../images/' . $row['photo'];
                                                                            echo "<img src='$img1' alt='Photo 1' style='width: 100px; height: 100px; border-radius: 8px;'>";
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Photo 2</td>
                                                                        <td>
                                                                            <?php
                                                                            $img2 = empty($row['photo2']) ? '../images/profile.jpg' : '../images/' . $row['photo2'];
                                                                            echo "<img src='$img2' alt='Photo 2' style='width: 100px; height: 100px; border-radius: 8px;'>";
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Photo 3</td>
                                                                        <td>
                                                                            <?php
                                                                            $img3 = empty($row['photo3']) ? '../images/profile.jpg' : '../images/' . $row['photo3'];
                                                                            echo "<img src='$img3' alt='Photo 3' style='width: 100px; height: 100px; border-radius: 8px;'>";
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Name</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['name'] ?? ''); ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Seller Name</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['seller_name'] ?? '-'); ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>MRP</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;">₹<?php echo number_format($row['mrp'] ?? 0); ?></div>
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
                                                                        <td>Description</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 100px; overflow: auto;">
                                                                                <?php echo htmlspecialchars($row['description'] ?? ''); ?>
                                                                            </div>
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
                                                    <p class='no-data-found'>No product data found.</p>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                       ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <script>
        function openPopup(productId) {
            document.getElementById("overlay_" + productId).style.display = "flex";
        }

        function closePopup(productId) {
            document.getElementById("overlay_" + productId).style.display = "none";
        }

        function downloadCSV() {
            var downloadWindow = window.open('fetch_details/fetch_product_details.php', '_blank');
            downloadWindow.focus();
        }

        function filterProducts() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("productSearch");
            filter = input.value.toUpperCase();
            table = document.getElementById("table");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                tr[i].style.display = "";
                for (j = 0; j < tr[i].cells.length; j++) {
                    td = tr[i].cells[j];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            break;
                        }
                    }
                }
                if (j === tr[i].cells.length) {
                    tr[i].style.display = "none";
                }
            }
        }
    </script>
</body>

</html>
