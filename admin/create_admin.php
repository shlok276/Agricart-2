<?php 
include ("../database/connection.php");
include ("../session/session_start.php");
include("../session/session_check.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin</title>
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
                    <h1>
                        Create Admin
                    </h1>
                    <p>
                        Create a new admin account <span class="las la-chart-lin"></span>
                    </p>
                </div>
            </div>

        </header>

        <main>
            <div class="table-data">
                <div class="order">
                <div class="head">
                <h3>Create Admin</h3>
            </div>
                    <section>
                    <div class="profile-3">
                        <form action="create_admin_process.php" method="post">
                            <div class="name">
                                User Name (Email)<br>
                                <input type="email" name="email" required>
                            </div>
                            <div class="name">
                                Password<br>
                                <input type="password" name="password" required>
                            </div>
                            <div class="name">
                                Contact Number<br>
                                <input type="text" name="contact_no" required>
                            </div>
                            <center><button class="profile-button" type="submit" name="create_admin">Create Admin</button></center>
                        </form>
                    </div>
              
                    </section>

                </div>
            </div>

        </main>
    </div>
    <script>
    window.onload = function() {
        <?php
        if(isset($_GET['alert'])) {
            $alert_message = '';
            switch($_GET['alert']) {
                case 'useralreadyexist':
                    $alert_message = 'Admin user already exists!';
                    break;
                case 'admincreated':
                    $alert_message = 'Admin Created Successfully!';
                    break;
                case 'error':
                    $alert_message = 'Error occurred while creating admin account!';
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
</body>

</html>
