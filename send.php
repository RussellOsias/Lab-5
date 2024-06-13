<?php
use PHPMailer\PHPMailer\PHPMailer; // Import PHPMailer class
use PHPMailer\PHPMailer\Exception; // Import Exception class

require 'phpmailer/src/Exception.php'; // Include Exception class file
require 'phpmailer/src/PHPMailer.php'; // Include PHPMailer class file
require 'phpmailer/src/SMTP.php'; // Include SMTP class file

if(isset($_POST["send"])){ // Check if the form is submitted
    $mail = new PHPMailer(true); // Create a new instance of PHPMailer class
    $mail->isSMTP(); // Set the mailer to use SMTP
    $mail->Host = 'smtp.gmail.com'; // Set the SMTP server hostname
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'reikatauchiha@gmail.com'; // SMTP username (your Gmail address)
    $mail->Password = 'rhlt zyks rwyc mzpf'; // SMTP password (your Gmail password)
    $mail->SMTPSecure = 'ssl'; // Enable SSL encryption for SMTP secure connection
    $mail->Port = 587; // Set the SMTP port for Gmail

    // Set sender email address
    $mail->setFrom('reikatauchiha@gmail.com', 'Email Verification');

    // Add recipient dynamically from form data
    $recipient_email = $_POST['recipient_email'];
    $mail->addAddress($recipient_email);

    $mail->isHTML(true); // Set email format to HTML

    // Set the subject and message content
    $mail->Subject = $_POST["subject"]; // Set the subject of the email
    $mail->Body = $_POST["message"]; // Set the body of the email

    try {
        $mail->send(); // Send the email
        echo "
        <script>
        alert('Sent Successfully'); // Display success message
        document.location.href = 'index.php'; // Redirect to index.php
        </script>
        ";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"; // Display error message if mailer fails
    }
}
?>
