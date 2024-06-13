<?php


// Include database connection file
include('config/db_conn.php');

// Check if user ID is set in session
if (isset($_SESSION['user_id'])) {
    // Retrieve user ID from session
    $user_id = $_SESSION['user_id'];

    // Fetch user profile data based on user ID
    $query = "SELECT * FROM user_profile WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $user_profile = mysqli_fetch_assoc($result);
} else {
    // Redirect user to login page if user ID is not set
    header("Location: login.php");
    exit(); // Stop further execution
}
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .50; width: 50px; height: 50px;">
        <span class="brand-text font-weight-light" style="font-size: 14px;">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image" style="width: 50px; height: 50px;">
                <?php if (!empty($user_profile['photo'])) : ?>
                    <img src="<?php echo $user_profile['photo']; ?>" class="img-circle elevation-2" alt="User Image" style="width: 150%; height: 100%;">
                <?php else : ?>
                    <img src="assets/dist/img/default-avatar.jpg" class="img-circle elevation-2" alt="Default Avatar" style="width: 150%; height: 100%;">
                <?php endif; ?>
            </div>
            <div class="info" style="margin-left: 20px;">
                <a href="registration.php" class="d-block" style="font-size: 14px;"><?php echo $user_profile['full_name']; ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add "Friends" link here -->
                <li class="nav-item">
                    <a href="friendlist.php" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Friends
                        </p>
                    </a>
                </li>
                <!-- Other sidebar links go here -->
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
