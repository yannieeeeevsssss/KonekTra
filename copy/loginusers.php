<?php
// Start session
session_start();

// Include database connection
require_once("db.php");

// Check if form is submitted
if (isset($_POST['login'])) {

    // Escape special characters from the email and password input
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Encrypt the entered password the same way it was encrypted when stored
    $entered_password = base64_encode(strrev(md5($password)));

    // Check if the email exists in the database
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    // If email exists
    if ($result->num_rows > 0) {
        // Fetch the stored user data
        $row = $result->fetch_assoc();

        // Compare the encrypted entered password with the one in the database
        if ($entered_password == $row['password']) {
            // Password matches, proceed to log in
            $_SESSION['email'] = $row['email'];
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['user_type'] = $row['user_type'];

            // Check if the account is active
            if ($row['active'] == 1) {
                // Redirect to the dashboard if account is active
                header("Location: dashboard.php");
            } else {
                // Account is not yet approved
                header("Location: dashboard.php");
            }
            exit();
        } else {
            // Invalid password
            $_SESSION['loginError'] = "Invalid password.";
            header("Location: loginusers.php");
            exit();
        }
    } else {
        // Email doesn't exist in the database
        $_SESSION['loginError'] = "No account found with that email.";
        header("Location: loginusers.php");
        exit();
    }
} else {
    // Redirect back to login page if form wasn't submitted
    header("Location: loginusers.php");
    exit();
}
?>
