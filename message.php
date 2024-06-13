<?php
// Check if status message is set in session
if(isset($_SESSION['status']))
{
    ?>
    <!-- Display an alert message with status -->
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <!-- Display the status message -->
        <strong></strong><?php echo $_SESSION['status']; ?>
        <!-- Button to close the alert -->
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php
    // Unset the status message from session to prevent displaying it again
    unset($_SESSION['status']);
}
?>
