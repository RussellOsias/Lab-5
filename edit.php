<?php
// Include authentication check to ensure user is logged in
include('authentication.php');

// Include header, topbar, sidebar, and database connection files
include('includes/header.php');
include('config/db_conn.php');

// Use PHPMailer library for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes required for email functionality
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Check if the form is submitted for updating user details
if (isset($_POST['UpdateUser'])) {
    // Function to sanitize input data to prevent XSS and other attacks
    function validate($data)
    {
        $data = trim($data); // Remove whitespace from both sides of a string
        $data = stripslashes($data); // Unquote a quoted string
        $data = htmlspecialchars($data); // Convert special characters to HTML entities
        return $data;
    }

    // Sanitize input data obtained from the POST request
    // Get the user ID from the submitted form data
    $user_id = $_POST['user_id'];
    // Clean and get the full name from the submitted form data
    $full_name = validate($_POST['full_name']);
    // Clean and get the email address from the submitted form data
    $email = validate($_POST['email']);
    // Clean and get the phone number from the submitted form data
    $phone_number = validate($_POST['phone_number']);
    // Clean and get the address from the submitted form data
    $address = validate($_POST['address']);
    // Clean and get the password from the submitted form data
    $password = validate($_POST['password']);


    // Construct SQL query for updating user profile in the database
    $sql = "UPDATE user_profile SET full_name = '$full_name', email = '$email', phone_number = '$phone_number', address = '$address', password = '$password' WHERE user_id = '$user_id' ";

    // Execute the query and check if the update was successful
    if (mysqli_query($conn, $sql)) {
        // Redirect with a success message if the update was successful
        $_SESSION['status'] = "User Updated Successfully";

        // Create a new PHPMailer instance for sending email notifications
        $mail = new PHPMailer(true);
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'reikatauchiha@gmail.com'; // Your Gmail email address
        $mail->Password = 'rhlt zyks rwyc mzpf'; // Your Gmail password
        $mail->SMTPSecure = 'ssl'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465; // TCP port to connect to
        $mail->setFrom('reikatauchiha@gmail.com', 'Russell Osias'); // Your email address and name
        $mail->addAddress($email); // Add a recipient (user's email address)
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Email Address Update Confirmation'; // Email subject
        $mail->Body = 'Dear ' . $full_name . ',<br><br>Your email address has been successfully updated to: ' . $email . '.<br><br>Sincerely,<br>Russells Website'; // Email body content

        // Check if the email was sent successfully
        if ($mail->send()) {
            // Append success message if email was sent successfully
            $_SESSION['status'] .= ". Email sent successfully";
        } else {
            // Append error message if email sending failed
            $_SESSION['status'] .= ". Email sending failed. Error: " . $mail->ErrorInfo;
        }

        // Redirect to the registration page
        header("Location: registration.php");
        exit(); // Ensure that no further code is executed after redirection
    } else {
        // Display an error message if the query fails
        $_SESSION['status'] = "User Updating Failed";
        header("Location: registration.php");
        exit(); // Ensure that no further code is executed after redirection
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Edit - Registered User</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit - Registered User</h3>
                            <a href="registration.php" class="btn btn-danger btn-sm float-right">Back</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="edit.php" method="POST">
                                        <div class="modal-body">
                                            <?php
                                                // Check if user_id is set in the URL
                                                if(isset($_GET['user_id']))
                                                {
                                                    // Retrieve user_id from URL
                                                    $user_id = $_GET['user_id'];
                                                    
                                                    // Query the database to get user details
                                                    $query = "SELECT * FROM user_profile WHERE user_id='$user_id' LIMIT 1";
                                                    $query_run = mysqli_query($conn, $query);

                                                    // Check if user details exist
                                                    if(mysqli_num_rows($query_run) > 0)
                                                    {
                                                        // Loop through user details
                                                        foreach($query_run as $row)
                                                        {
                                                            ?>
                                                                <!-- Display user details in input fields -->
                                                                <input type="hidden" name="user_id" value="<?php echo $row['user_id'] ?>">
                                                                <div class="form-group">
                                                                    <label for="">Full Name</label>
                                                                    <input type="text" name="full_name" value="<?php echo $row['full_name'] ?>" class="form-control" placeholder="Full Name">
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="">Email</label>
                                                                    <input type="text" name="email" value="<?php echo $row['email'] ?>" class="form-control" placeholder="Email">
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="">Phone Number</label>
                                                                    <input type="text" name="phone_number" value="<?php echo $row['phone_number'] ?>" class="form-control" placeholder="Phone Number">
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="">Address</label>
                                                                    <input type="text" name="address" value="<?php echo $row['address'] ?>" class="form-control" placeholder="Address">
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="">Password</label>
                                                                    <input type="password" name="password" value="<?php echo $row['password'] ?>" class="form-control" placeholder="Password">
                                                                </div>
                                                            <?php
                                                        }
                                                    } 
                                                    else
                                                    {
                                                        // Display message if no record found
                                                        echo "<h4>No Record Found.!</h4>";
                                                    } 
                                                }
                                            ?>
                                            
                                        </div>
                                        <div class="modal-footer">
                                            <!-- Button to submit form for updating user -->
                                            <button type="submit" name="UpdateUser" class="btn btn-info">Update</button>
                                        </div>
                                    </form> 
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<!-- Include script files -->
<?php include('includes/script.php'); ?>
<!-- Include footer -->
<?php include('includes/footer.php'); ?>
