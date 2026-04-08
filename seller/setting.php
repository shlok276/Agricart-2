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
<html>

<head>
    <title>Settings</title>
    <link rel="icon" href="../images/titlelogo.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
            .table-container input{
                width: 25%;
            }
        </style>
</head>

<body>
    <?php include("navigation.php"); ?>

    <div id="main">
        <div class="head">
            <div class="col-div-6">
                <span style="font-size:30px;cursor:pointer; color: #000e04;" class="nav"><i
                        class="fa-solid fa-bars"></i> Setting</span>
                <span style="font-size:30px;cursor:pointer; color: rgb(0, 0, 0); display: none;" class="nav2"><i
                        class="fa-solid fa-bars"></i> Setting</span>
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
                    <h1>Change Password</h1>
                    <br />
                    <div class="table-container">
                        <table>
                            <form action="change_password.php" method="POST">
                                <tr><td>Current Password</td></tr><br>
                                <input type="password" name="current_password" required><br>
                                <tr><td>New Password</td></tr><br>
                                <input type="password" name="new_password" required><br>
                                <tr><td>Confirm Password</td></tr><br>
                                <input type="password" name="confirm_password" required><br><br>
                                <button type="submit" name="save_password">Submit</button>
                            </form>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    window.onload = function() {
        <?php
        if(isset($_GET['alert'])) {
            $alert_message = '';
            switch($_GET['alert']) {
                case 'password_changed_successfully':
                    $alert_message = 'Password changed successfully!';
                    break;
                case 'update_error':
                    $alert_message = 'Error occurred while updating password!';
                    break;
                case 'password_mismatch':
                    $alert_message = 'New password and confirm password do not match!';
                    break;
                case 'incorrect_current_password':
                    $alert_message = 'Current password is incorrect!';
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
    $(document).ready(function () {
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
    });
</script>
</html>
