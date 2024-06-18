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

// Google Client configuration
require_once 'vendor/autoload.php';

// Initialize Google Client
$clientID = '849244603760-94fcqfgipelmg79nqlssatfhjebr4k2u.apps.googleusercontent.com'; // Set the Google OAuth client ID.
$clientSecret = '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'; // Set the Google OAuth client secret.
$redirectUri = 'http://localhost:3000/login.php'; // Set the redirect URI to match your setup.

$client = new Google_Client(); // Create a new Google client object.
$client->setClientId($clientID); // Set the client ID for the Google client.
$client->setClientSecret($clientSecret); // Set the client secret for the Google client.
$client->setRedirectUri($redirectUri); // Set the redirect URI for the Google client.
$client->addScope("email"); // Request access to the user's email.
$client->addScope("profile"); // Request access to the user's profile.

// Check if the user is already authenticated and redirect to index page if so
if (isset($_SESSION['auth'])) { // Check if the user is already authenticated.
    $_SESSION['status'] = "You are already logged In"; // Set a status message indicating the user is already logged in.
    header('Location: registration.php'); // Redirect to the registration page.
    exit; // Stop further script execution.
}

// Handle Google OAuth 2.0 callback
if (isset($_GET['code'])) { // Check if the OAuth 2.0 callback code is set.
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']); // Exchange the authorization code for an access token.

        if (isset($token['error'])) { // Check if there was an error fetching the access token.
            $_SESSION['status'] = "Error fetching access token: " . $token['error_description']; // Set an error message.
            header('Location: login.php'); // Redirect to the login page.
            exit; // Stop further script execution.
        }


        $client->setAccessToken($token);

        // Check if access token is valid
        if ($client->isAccessTokenExpired()) {
            $_SESSION['status'] = "Access token expired"; // Set error message
            header('Location: login.php'); // Redirect to login page
            exit; // Stop further script execution
        }

     // Get profile information
$google_oauth = new Google_Service_Oauth2($client); // Create a new Google OAuth2 service object.
$google_account_info = $google_oauth->userinfo->get(); // Get the user's profile information from Google.

// Extract user info
$userinfo = [ // Extract the user's email and full name.
    'email' => $google_account_info->getEmail(), // Get the user's email from the Google account info.
    'full_name' => $google_account_info->getName(), // Get the user's full name from the Google account info.
];

// Check if user exists in database
$sql = "SELECT * FROM user_profile WHERE email = '{$userinfo['email']}' LIMIT 1"; // Prepare an SQL query to check if the user already exists in the database.
$result = mysqli_query($conn, $sql); // Execute the query.

if (mysqli_num_rows($result) > 0) { // Check if the user exists in the database.
    // User exists, fetch user data
    $userinfo_db = mysqli_fetch_assoc($result); // Fetch the user's data from the database.
    $_SESSION['auth'] = true; // Set a session variable to indicate the user is authenticated.
    $_SESSION['user_id'] = $userinfo_db['user_id']; // Set the user's ID in the session.
    $_SESSION['full_name'] = $userinfo_db['full_name']; // Set the user's full name in the session.
    $_SESSION['email'] = $userinfo_db['email']; // Set the user's email in the session.
} else {
    // User does not exist, insert into database
    $sql = "INSERT INTO user_profile (email, full_name, verify) VALUES ('{$userinfo['email']}', '{$userinfo['full_name']}', 'pending')"; // Prepare an SQL query to insert the new user into the database.
    $insert_result = mysqli_query($conn, $sql); // Execute the insert query.
   
    if ($insert_result) { // Check if the user insertion into the database was successful.
        $_SESSION['auth'] = true; // Set a session variable to indicate the user is authenticated.
        $_SESSION['user_id'] = mysqli_insert_id($conn); // Get the auto-generated user ID from the database insertion.
        $_SESSION['full_name'] = $userinfo['full_name']; // Set the user's full name in the session.
        $_SESSION['email'] = $userinfo['email']; // Set the user's email in the session.
    

                // Generate verification code
                $verification_code = rand(100000, 999999); // Generate a random 6-digit verification code

                // Store verification code in session
                $_SESSION['verification_code'] = $verification_code;

                // Send verification email
                $mail = new PHPMailer(true); // Create a new PHPMailer instance
                $mail->isSMTP(); // Set mailer to use SMTP
                $mail->Host = 'smtp.gmail.com'; // SMTP server address
                $mail->SMTPAuth = true; // Enable SMTP authentication
                $mail->Username = 'reikatauchiha@gmail.com'; // SMTP username (your Gmail address)
                $mail->Password = 'rhlt zyks rwyc mzpf';  // Your Gmail password
                $mail->SMTPSecure = 'tls'; // Enable TLS encryption
                $mail->Port = 587; // TCP port to connect to
                $mail->setFrom('reikatauchiha@gmail.com', 'Russell Osias'); // Set sender's email address and name
                $mail->addAddress($userinfo['email']); // Add recipient's email address
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Email Verification'; // Set email subject
                $mail->Body = 'Your verification code is: ' . $verification_code; // Set email body

                if ($mail->send()) { // If email sent successfully
                    $_SESSION['status'] = "Please check your email for the verification code"; // Set status message
                    header('Location: verify.php'); // Redirect to verify page
                    exit; // Stop further script execution
                } else {
                    $_SESSION['status'] = "Failed to send verification email"; // Set error message
                    header('Location: login.php'); // Redirect to login page
                    exit; // Stop further script execution
                }
            } else {
                $_SESSION['status'] = "Failed to create user"; // Set error message
                header('Location: login.php'); // Redirect to login page
                exit; // Stop further script execution
            }
        }

        // Redirect to a protected page
        header("Location: registration.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['status'] = "Error: " . $e->getMessage(); // Set error message
        header('Location: login.php'); // Redirect to login page
        exit; // Stop further script execution
    }
}

// Check if the login button is clicked
if (isset($_POST['login_btn'])) {
    // Sanitize input data
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Sanitize username and password
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    // Query to fetch user from database
    $sql = "SELECT * FROM user_profile WHERE email='$email' AND password='$password' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Check if user found with matching credentials
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // Check if user is verified
            if ($row['verify'] !== 'verified') {
                // Handle verification process
                // Code for sending verification email and processing verification

                // Generate verification code
                $verification_code = rand(100000, 999999); // Generate a random 6-digit verification code

                // Store verification code in session
                $_SESSION['verification_code'] = $verification_code;

                // Send verification email
                $mail = new PHPMailer(true); // Create a new PHPMailer instance
                $mail->isSMTP(); // Set mailer to use SMTP
                $mail->Host = 'smtp.gmail.com'; // SMTP server address
                $mail->SMTPAuth = true; // Enable SMTP authentication
                $mail->Username = 'reikatauchiha@gmail.com'; // SMTP username (your Gmail address)
                $mail->Password = 'rhlt zyks rwyc mzpf';  // Your Gmail password
                $mail->SMTPSecure = 'tls'; // Enable TLS encryption
                $mail->Port = 587; // TCP port to connect to
                $mail->setFrom('reikatauchiha@gmail.com', 'Russell Osias'); // Set sender's email address and name
                $mail->addAddress($email); // Add recipient's email address
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Email Verification'; // Set email subject
                $mail->Body = 'Your verification code is: ' . $verification_code; // Set email body

                if ($mail->send()) { // If email sent successfully
                    $_SESSION['status'] = "Please check your email for the verification code"; // Set status message
                    header('Location: verify.php'); // Redirect to verify page
                    exit; // Stop further script execution
                } else {
                    $_SESSION['status'] = "Failed to send verification email"; // Set error message
                    header('Location: login.php'); // Redirect to login page
                    exit; // Stop further script execution
                }
            }

            // Set session variables
            $_SESSION['auth'] = true;
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['email'] = $row['email'];

            // Update user status to "active"
            $user_id = $row['user_id'];
            $update_status_query = "UPDATE user_profile SET status = 'active' WHERE user_id = '$user_id'";
            mysqli_query($conn, $update_status_query);

            // Redirect to the home page after successful login
            header("Location: registration.php");
            exit;
        } else {
            // If no matching credentials found, set error message and redirect to login page
            $_SESSION['status'] = "Invalid email or password";
            header("Location: login.php");
            exit;
        }
    } else {
        // If database query fails, set error message with error details and redirect to login page
        $_SESSION['status'] = "Error: " . mysqli_error($conn);
        header("Location: login.php");
        exit;
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
                        <h5>Login Form</h5>
                    </div>
                    <div class="card-body">
                        <!-- Display authentication status message if set -->
                        <?php
                        if (isset($_SESSION['status'])) {
                            ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong></strong> <?php echo $_SESSION['status']; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php
                            unset($_SESSION['status']); // Unset the status message after displaying
                        }
                        ?>
                                                <!-- Login Form -->
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input type="text" name="email" class="form-control" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <label for="">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="login_btn" class="btn btn-primary btn-block" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; background-color: #007bff; border-color: #007bff;">
                                    <img src="assets/dist/img/login.png" alt="Login Icon" style="width: 20px; height: 20px; margin-right: 10px;">
                                    Login
                                </button>
                            </div>
                        </form>
                                        <!-- Google Sign-In Button -->
                        <div class="form-group text-center">
                            <?php
                            // Render Google Sign-In button with Google colors and logo
                            $authUrl = $client->createAuthUrl();
                            echo "<a href='$authUrl' class='btn btn-danger'>"; // Change 'btn-primary' to 'btn-danger' for a different color
                            ?>
                            <img src="assets/dist/img/google.svg" alt="Google Logo" style="width: 20px; height: 20px; margin-right: 10px;">
                            Login with Google
                            </a>
                        </div>

                        <!-- Additional HTML -->
                        <div class="text-center">
                            <p>Don't have an account? <a href="signup.php" class="btn-sm">Sign Up</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
