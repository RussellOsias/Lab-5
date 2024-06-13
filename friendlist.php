<?php
// Start the session
session_start();

// Include database connection
include('config/db_conn.php');
include('includes/header.php');

include('includes/sidebar.php');
include('config/db_conn.php');
// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch all user profiles with profile pictures and bios except the logged-in user
$query = "SELECT user_id, full_name, photo, bio, address, phone_number, birthdate, gender, citizenship, nationality, religion, marital_status FROM user_profile WHERE photo IS NOT NULL AND user_id != '$user_id'";
$result = mysqli_query($conn, $query);

// Check if query was successful
if ($result) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Friend List</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <!-- AdminLTE CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
        <!-- Custom styles -->
        <style>
            .profile-card {
                width: 200px;
                margin: 20px;
            }
            .profile-img {
                width: 100%;
                height: auto;
            }
            .card-title {
                font-weight: bold;
            }
            .collapse-content {
                margin-top: 10px;
            }
        </style>
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                </ul>
            </nav>

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="#" class="brand-link">
                    <img src="assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                    <span class="brand-text font-weight-light">AdminLTE 3</span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <li class="nav-item">
                                <a href="registration.php" class="nav-link">
                                    <i class="nav-icon fas fa-home"></i>
                                    <p>Go Back Home</p>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">Friend List</h1>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div>
                <!-- /.content-header -->

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php
                            // Loop through each user profile
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <div class="col-md-3">
                                    <div class="card profile-card">
                                        <!-- Display profile picture from database -->
                                        <?php if (!empty($row['photo'])) : ?>
                                            <img src="<?php echo $row['photo']; ?>" class="card-img-top profile-img" alt="Profile Picture">
                                        <?php else : ?>
                                            <img src="assets/dist/img/default-avatar.jpg" class="card-img-top profile-img" alt="Default Avatar">
                                        <?php endif; ?>
                                             <div class="card-body">
                                                <h5 class="card-title"><?php echo $row['full_name']; ?></h5>
                                                <p class="card-text"><strong>Bio:</strong> <?php echo $row['bio']; ?></p>
                                                <!-- Drop-down button for additional information -->
                                                <div class="row">
                                                    <div class="col">
                                                        <button class="btn btn-primary btn-block" type="button" data-toggle="collapse" data-target="#collapseInfo<?php echo $row['user_id']; ?>" aria-expanded="false" aria-controls="collapseInfo<?php echo $row['user_id']; ?>">
                                                            View More
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="collapse collapse-content" id="collapseInfo<?php echo $row['user_id']; ?>">
                                                    <p><strong>Address:</strong> <?php echo $row['address']; ?></p>
                                                    <p><strong>Phone Number:</strong> <?php echo $row['phone_number']; ?></p>
                                                    <p><strong>Birthdate:</strong> <?php echo date("F j, Y", strtotime($row['birthdate'])); ?></p>
                                                    <p><strong>Gender:</strong> <?php echo $row['gender']; ?></p>
                                                    <p><strong>Citizenship:</strong> <?php echo $row['citizenship']; ?></p>
                                                    <p><strong>Nationality:</strong> <?php echo $row['nationality']; ?></p>
                                                    <p><strong>Religion:</strong> <?php echo $row['religion']; ?></p>
                                                    <p><strong>Marital Status:</strong> <?php echo $row['marital_status']; ?></p>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->

            <!-- Main Footer -->
        
                <?php include('includes/footer.php'); ?>
            
        </div>
        <!-- ./wrapper -->

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
        <!-- AdminLTE App -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>
    </body>
    </html>
    <?php
} else {
    // Error fetching user profiles
    echo "Error: " . mysqli_error($conn);
}

// Close database connection
mysqli_close($conn);
?>
