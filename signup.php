<?php
// Start the session
session_start();
?>

<!-- Include the header file -->
<?php include('includes/header.php'); ?>

<!-- HTML section -->
<div class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 my-5">
                <div class="card my-5">
                    <div class="card-header bg-light">
                        <!-- Signup Form Header -->
                        <h5>Sign Up</h5>
                    </div>
                    <div class="card-body">
                        <!-- Display session status message if set -->
                        <?php
                        if (isset($_SESSION['status'])) {
                            echo "<div class='alert alert-success'>" . $_SESSION['status'] . "</div>";
                            unset($_SESSION['status']); // Clear the status message after displaying it
                        }
                        ?>

                        <!-- Display error message if set -->
                        <?php
                        if (isset($_SESSION['error'])) {
                            echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
                            unset($_SESSION['error']); // Clear the error message after displaying it
                        }
                        ?>

                        <!-- Signup Form -->
                        <form action="signupcode.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                            <div class="form-group">
                                <label for="">Full Name</label>
                                <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                            </div>

                            <div class="form-group">
                                <label for="">Email</label>
                                <input type="text" name="email" class="form-control" placeholder="Email" required>
                            </div>

                            <div class="form-group">
                                <label for="">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" placeholder="Phone Number" required>
                            </div>
                            <div class="form-group">
                                <label for="birthdate">Birthdate</label>
                                <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo $userProfile['birthdate']; ?>" max="<?php echo date('Y-m-d', strtotime('-13 years')); ?>" required>
                                <small class="form-text text-muted">You must be at least 13 years old.</small>
                            </div>
                            <div class="form-group">
                                <label for="">Address</label>
                                <input type="text" name="address" class="form-control" placeholder="Address" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Password</label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Confirm Password</label>
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
                                        <span id="password_message" class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <button type="submit" name="signup_btn" class="btn btn-primary btn-block">Sign Up</button>
                            </div>
                        </form>

                        <!-- Link to the login page -->
                        <div class="text-center">
                            <p>Already have an account? <a href="login.php">Log in</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include footer and script files -->
<?php include('includes/script.php'); ?>
<?php include('includes/footer.php'); ?>

<!-- JavaScript for password confirmation validation -->
<script>
    function validateForm() {
        var password = document.getElementById("password").value;
        var confirm_password = document.getElementById("confirm_password").value;

        if (password != confirm_password) {
            document.getElementById("password_message").innerHTML = "Passwords do not match";
            return false;
        } else {
            document.getElementById("password_message").innerHTML = "";
            return true;
        }
    }
</script>
