<?php 
include ("../database/connection.php");
include ("../session/session_start.php");
include("../session/session_check.php");

$buyers = [];
try {
    $stmt = $conn->query("SELECT * FROM buyer_details");
    $buyers = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer List</title>
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
                    <h1>Buyer</h1>
                    <p>Display Information About Buyers<span class="las la-chart-lin"></span></p>
                </div>
            </div>
        </header>

        <main>
            <div class="table-data">
                <div class="order">
                <div class="head">
            <h3>Total Buyers</h3>
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
                                            <th>Name</th>
                                            <th>E-mail</th>
                                            <th>Contact</th>
                                            <th>Created on</th>
                                            <th>Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $counter = 1;
                                        if (!empty($buyers)) {
                                            foreach ($buyers as $row) { 
                                                $buyer_id = $row['buyer_id'];
                                        ?>
                                            <tr>
                                                <td><?php echo $counter++;?></td>
                                                <td><?php echo htmlspecialchars($row['full_name'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td><?php echo htmlspecialchars($row['contact_no'] ?? ''); ?></td>
                                                <td><?php echo $row['created_on'] ?? ''; ?></td>
                                                <td>
                                                    <button onclick="openPopup(<?php echo $buyer_id; ?>)"><i class="fa-solid fa-magnifying-glass"></i> Views</button>
                                                    <div class="overlay" id="overlay_<?php echo $buyer_id; ?>">
                                                        <div class="popup">
                                                            <span class="close-btn" onclick="closePopup(<?php echo $buyer_id; ?>)">×</span>
                                                            <h2>Buyer Details</h2>
                                                            <form>
                                                            <div style="max-height: 400px; overflow-y: auto;">
                                                                <table>
                                                                    <tr>
                                                                        <td>Photo</td>
                                                                        <td>
                                                                        <?php
                                                                            $image = empty($row['photo']) ? '../images/profile.jpg' : '../images/' . $row['photo'];
                                                                            echo "<img src='$image' alt='Buyer Photo' style='width: 50px; height: 50px; border-radius: 50%;'>";
                                                                        ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Full Name</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['full_name'] ?? '');?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>E-mail</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['email']);?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Contact</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['contact_no'] ?? '');?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Created On</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo $row['created_on'] ?? '';?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Address</td>
                                                                        <td>
                                                                        <textarea style="border: 1px solid #ccc; padding: 5px; width: 700px; height: 50px;" readonly><?php echo htmlspecialchars($row['address'] ?? ''); ?></textarea>
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
                                        } else { ?>
                                            <tr>
                                                <td colspan="6">
                                                    <p class='no-data-found'>No buyer data found.</p>
                                                </td>
                                            </tr>
                                        <?php } ?>
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
        function openPopup(buyerId) {
            document.getElementById("overlay_" + buyerId).style.display = "flex";
        }

        function closePopup(buyerId) {
            document.getElementById("overlay_" + buyerId).style.display = "none";
        }

        function downloadCSV() {
            var downloadWindow = window.open('fetch_details/fetch_buyer_details.php', '_blank');
            downloadWindow.focus();
        }

        function filterSellers() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("buyerSearch");
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
