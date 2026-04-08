<?php 
include ("../database/connection.php");
include ("../session/session_start.php");
include("../session/session_check.php");

$messages = [];
try {
    $stmt = $conn->query("SELECT * FROM contact_details ORDER BY created_on DESC");
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message List</title>
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
                    <h1>Messages</h1>
                    <p>Display Messages From Sellers and Buyers <span class="las la-chart-lin"></span></p>
                </div>
            </div>
        </header>

        <main>
            <div class="table-data">
                <div class="order">
                <div class="head">
            <h3>Total Message</h3>
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
                                            <th>Status</th>
                                            <th>Created On</th>
                                            <th>Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $counter = 1;
                                        if (!empty($messages)) {
                                            foreach ($messages as $row) {
                                        ?>
                                            <tr>
                                                <td><?php echo $counter++;?></td>
                                                <td><?php echo htmlspecialchars($row['buyer_name'] ?? '');?></td>
                                                <td class="status">
                                                    <div class="status_inner_div" style="background-color: <?php echo ($row['status'] == 0) ? 'red' : 'green'; ?>;">
                                                        <span><?php echo ($row['status'] == 0) ? 'New' : 'Viewed'; ?></span>
                                                    </div>
                                                </td>
                                                <td><?php echo $row['created_on'] ?? '';?></td>
                                                <td>
                                                    <button onclick="openPopup('<?php echo $row['contact_id']; ?>')"><i class="fa-solid fa-magnifying-glass"></i> View</button>
                                                    <div class="overlay" id="overlay_<?php echo $row['contact_id']; ?>">
                                                        <div class="popup">
                                                            <span class="close-btn" onclick="closePopup('<?php echo $row['contact_id']; ?>')">×</span>
                                                            <h2>Message Details</h2>
                                                            <form>
                                                            <div style="max-height: 400px; overflow-y: auto;">
                                                                <table id="a">
                                                                    <tr>
                                                                        <td>Name</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['buyer_name'] ?? ''); ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Email</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo htmlspecialchars($row['email'] ?? ''); ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Message</td>
                                                                        <td>
                                                                            <div class="details-display" style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 100px; white-space: pre-wrap;">
                                                                                <?php echo htmlspecialchars($row['message'] ?? ''); ?>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Status</td>
                                                                        <td class="status">
                                                                            <div class="status_inner_div" style="background-color: <?php echo ($row['status'] == 0) ? 'red' : 'green'; ?>; display: inline-block;">
                                                                                <span><?php echo ($row['status'] == 0) ? 'New' : 'Viewed'; ?></span>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Created On</td>
                                                                        <td>
                                                                            <div style="border: 1px solid #ccc; padding: 5px; width: 700px; min-height: 50px;"><?php echo $row['created_on'] ?? ''; ?></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td></td>
                                                                        <td>
                                                                        <?php
                                                                            if ($row['status'] == 0) {
                                                                                echo '<a href="mark_read_message.php?contact_id=' . $row['contact_id'] . '" class="mark-as-read" style="text-decoration: none; background: #007bff; color: white; padding: 8px 12px; border-radius: 4px;"> Mark as read</a>';
                                                                            }
                                                                        ?> 
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
                                                <td colspan="5">
                                                    <p class='no-data-found'>No message data found.</p>
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
        function openPopup(contactId) {
            document.getElementById("overlay_" + contactId).style.display = "flex";
        }

        function closePopup(contactId) {
            document.getElementById("overlay_" + contactId).style.display = "none";
        }
        function downloadCSV() {
            var downloadWindow = window.open('fetch_details/fetch_message_details.php', '_blank');
            downloadWindow.focus();
        }
    </script>
</body>
</html>
