<?php
// Start a new session or resume the existing session
session_start();

// Include the file that connects to the database
include('config/db_conn.php');
// Use the PHPMailer classes for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the PHPMailer files needed for sending emails
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Check if the logout button was clicked
if (isset($_POST['logout_btn'])) {
    // Remove the authentication session variables
    unset($_SESSION['auth']);
    unset($_SESSION['auth_user']);
    // Set a status message indicating successful logout
    $_SESSION['status'] = "Logged out successfully";
    // Redirect the user to the login page
    header('Location: login.php');
    // Stops
    exit(0);
}



// Check if the request method is POST
if (isset($_POST['AddUser'])) {
    function validate($data)// Define a function to clean and sanitize input data
    {
        $data = trim($data); // Remove extra spaces from the beginning and end of the input
        $data = stripslashes($data); // Remove backslashes from the input
        $data = htmlspecialchars($data); // Convert special characters to HTML entities to prevent XSS attacks
        return $data; // Return the cleaned and sanitized input
    }

    // Sanitize input data obtained from the POST request
    $full_name = validate($_POST['full_name']); // Clean and get the full name from the submitted form data
    $email = validate($_POST['email']); // Clean and get the email address from the submitted form data
    $phone_number = validate($_POST['phone_number']); // Clean and get the phone number from the submitted form data
    $address = validate($_POST['address']); // Clean and get the address from the submitted form data
    $password = validate($_POST['password']); // Clean and get the password from the submitted form data
    $confirm_password = validate($_POST['confirm_password']); // Clean and get the confirm password from the submitted form data
    if ($password == $confirm_password) { // Check if the password matches the confirm password

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Check if the email is in a valid format
            $_SESSION['error'] = "Invalid email format"; // Set error message for invalid email format
            header("Location: registration.php"); // Redirect to the registration page
            exit(); // Stop further execution
        }
    
        // Check if email already exists in the database
        $email_check_query = "SELECT * FROM user_profile WHERE email='$email' LIMIT 1"; // Query to check if the email exists
        $result = mysqli_query($conn, $email_check_query); // Execute the query
        $user = mysqli_fetch_assoc($result); // Fetch the result as an associative array
    
        // If email already exists
        if ($user) { // Check if a user with the given email was found
            $_SESSION['error'] = "Email already exists"; // Set error message for existing email
            header("Location: registration.php"); // Redirect to the registration page
            exit(); // Stop further execution
        }
    
    

        // Define default values for status and verify fields
        $status = "active";
        $verify = "not verified";

     // SQL query to insert user data into the database
    $sql = "INSERT INTO user_profile (full_name,email,phone_number,address,password,status,verify)
        VALUES ('$full_name','$email','$phone_number','$address','$password','$status','$verify')";

    if (mysqli_query($conn, $sql)) { // Check if the query executed successfully
        $_SESSION['status'] = "User Added Successfully"; // Set success message

// Send email notification
        $mail = new PHPMailer(true); // Create a new PHPMailer instance
        $mail->isSMTP(); // Use SMTP for sending email
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'uchihareikata@gmail.com'; // Your Gmail email address
        $mail->Password = 'qyki jszw moov wvhz'; // Your Gmail password
        $mail->SMTPSecure = 'ssl'; // Enable SSL encryption
        $mail->Port = 465; // TCP port to connect to
        $mail->setFrom('uchihareikata@gmail.com', 'Russell Osias'); // Set sender email and name
        $mail->addAddress($email); // Add recipient email address
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Registration Confirmation'; // Set email subject
        $mail->Body = 'Dear ' . $full_name . ',<br><br>Thank you for registering on our website.<br><br>Sincerely,<br>Your Name'; // Set email body

    if ($mail->send()) { // Check if the email was sent successfully
         $_SESSION['status'] .= ". Email sent successfully"; // Append success message for email
    } else {
         $_SESSION['status'] .= ". Email sending failed. Error: " . $mail->ErrorInfo; // Append error message for email
    }

                  header("Location: registration.php"); // Redirect to the registration page
    } else {
          $_SESSION['status'] = "User Registration Failed"; // Set error message if query fails
                 header("Location: registration.php"); // Redirect to the registration page
    }
    } else {
         $_SESSION['status'] = "Password and Confirm Password do not match.!"; // Set error message if passwords don't match
                   header("Location: registration.php"); // Redirect to the registration page
    }
}

// Check if the request method is POST
if (isset($_POST['UpdateUser'])) {
function validate($data)
{
    $data = trim($data); // Remove extra spaces from the beginning and end
    $data = stripslashes($data); // Remove backslashes
    $data = htmlspecialchars($data); // Convert special characters to HTML entities
    return $data; // Return the cleaned data
}

    // Ensure fields are initialized
    $user_id = $_POST['user_id']; // Get user ID from the form
    $full_name = ($_POST['full_name']); // Get full name from the form
    $email = ($_POST['email']); // Get email from the form
    $phone_number = ($_POST['phone_number']); // Get phone number from the form
    $address = ($_POST['address']); // Get address from the form
    $password = ($_POST['password']); // Get password from the form

// Construct SQL query for updating user profile
$sql = "UPDATE user_profile SET full_name = '$full_name', email = '$email', phone_number = '$phone_number', address = '$address', password = '$password' WHERE user_id = '$user_id' ";

if (mysqli_query($conn, $sql)) { // Check if the query executed successfully
    $_SESSION['status'] = "User Update Successfully"; // Set success message
        header("Location: registration.php"); // Redirect to the registration page
} else {
    $_SESSION['status'] = "User Updating Failed"; // Set error message if query fails
    header("Location: registration.php"); // Redirect to the registration page
    }
}


// Check if the request method is POST
if (isset($_POST['DeleteUserbtn'])) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $user_id = $_POST['delete_id'];

    $sql = "DELETE FROM user_profile WHERE user_id = '$user_id' ";
    if (mysqli_query($conn, $sql)) {
        // Redirect with a success message
        $_SESSION['status'] = "User Deleted Successfully";
        header("Location: registration.php");
    } else {
        // Display an error message if the query fails
        $_SESSION['status'] = "User Deleting Failed";
        header("Location: registration.php");
    }
}
?>
