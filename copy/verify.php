<?php
// Start the session
session_start();

// Include the database connection
require_once("db.php");

// Check if token and email are present in the URL
if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $email = mysqli_real_escape_string($conn, $_GET['email']);

    // Query to check if the token and email match
    $sql = "SELECT * FROM users WHERE email='$email' AND hash='$token' AND active=0";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Activate the user's account
        $sql_update = "UPDATE users SET active=1 WHERE email='$email'";
        if ($conn->query($sql_update) === TRUE) {
            // Success: Redirect user or show a success message
            $_SESSION['message'] = "Your email has been verified successfully!";
            header('Location: login.php'); // Redirect to a success page
            exit();
        } else {
            $_SESSION['error'] = "Something went wrong. Please try again.";
            header('Location: error.php');
            exit();
        }
    } else {
        // Invalid token or email
        $_SESSION['error'] = "Invalid or expired verification link.";
        header('Location: error.php');
        exit();
    }
} else {
    // If token or email is missing, show an error
    $_SESSION['error'] = "Invalid request. Missing token or email.";
    header('Location: error.php');
    exit();
}
?>
