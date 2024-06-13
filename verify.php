<?php
// Start the session to manage user authentication
session_start();

// Include the header file
include('includes/header.php');

// Include file for database connection
include('config/db_conn.php');

// Include PHPMailer classes for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the required PHPMailer files
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Check if the verification code is submitted
if (isset($_POST['verify_btn'])) {
    // Validate the entered verification code
    $entered_code = $_POST['verify_code']; // Retrieve entered verification code
    $expected_code = $_SESSION['verification_code']; // Retrieve expected verification code from session

    if ($entered_code == $expected_code) { // If verification code matches
        // Update user status in the database
        $user_id = $_SESSION['user_id']; // Retrieve user ID from session
        $update_query = "UPDATE user_profile SET verify = 'verified' WHERE user_id = '$user_id'"; // SQL query to update user status

        if (mysqli_query($conn, $update_query)) { // If update query is successful
            // Successful verification, redirect to registration.php
            $_SESSION['auth'] = true; // Set authentication status in the session to indicate successful verification
            header("Location: registration.php"); // Redirect to index page
            exit(); // Stop further execution
        } else {
            $_SESSION['status'] = "Error updating verification status: " . mysqli_error($conn); // Set error message
            header("Location: verify.php"); // Redirect to verification page
            exit(); // Stop further execution
        }
    } else {
        $_SESSION['status'] = "Invalid verification code"; // Set error message
        header("Location: verify.php"); // Redirect to verification page
        exit(); // Stop further execution
    }
}
?>

<!-- HTML section -->
<div class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 my-5">
                <div class="card my-5">
                    <div class="card-header bg-light">
                        <!-- Verify Email Form Header -->
                        <h5>Verify Email</h5>
                    </div>
                    <div class="card-body">
                        <!-- Display status messages -->
                        <?php
                        if (isset($_SESSION['status'])) {
                            echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>" . $_SESSION['status'] . "
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                            </div>";
                            unset($_SESSION['status']); // Clear the status message after displaying it
                        }
                        ?>

                        <!-- Verification Code Form -->
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="form-group">
                                <label for="">Enter Verification Code</label>
                                <input type="text" name="verify_code" class="form-control" placeholder="Verification Code" required>
                            </div>

                            <!-- Submit button for verification code -->
                            <button type="submit" name="verify_btn" class="btn btn-primary btn-block">Verify</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include footer and script files -->
<?php include('includes/script.php'); ?>
<?php include('includes/footer.php'); ?>
