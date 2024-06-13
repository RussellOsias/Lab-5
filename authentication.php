<?php
// Check if session is not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session
}

// Check if the user is not authenticated and redirect to login page
if (!isset($_SESSION['auth'])) {
    $_SESSION['status'] = "Please log in to access this page"; // Set error message
    header('Location: login.php'); // Redirect to login page
    exit(); // Stop further execution
}

?>
