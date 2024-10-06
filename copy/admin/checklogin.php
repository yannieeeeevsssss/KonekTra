<?php

// To Handle Session Variables on This Page
session_start();

// Including Database Connection From db.php file to avoid rewriting in all files
require_once("../db.php");

// If user clicked login button
if(isset($_POST)) {

    // Escape special characters in string
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Encrypt Password (if your database stores hashed passwords)
    // Uncomment this if using hashed passwords
    // $password = base64_encode(strrev(md5($password)));

    // SQL query to check admin login based on user_type and active status
    $sql = "SELECT * FROM users WHERE email='$email' AND user_type='admin' AND active=1 LIMIT 1";
    $result = $conn->query($sql);

    // If users table has this login details
    if($result->num_rows > 0) {
        // Output data
        $row = $result->fetch_assoc();

        // Verify password (use this if your password is hashed)
        // if(password_verify($password, $row['password'])) {

        // If plain text, match passwords directly
        if($password == $row['password']) {

            // Set session variables for the admin
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['user_type'] = $row['user_type']; // store user type

            // Redirect to admin dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // If password doesn't match
            $_SESSION['loginError'] = true;
            header("Location: index.php");
            exit();
        }
    } else {
        // If login failed, set login error and redirect to login page
        $_SESSION['loginError'] = true;
        header("Location: index.php");
        exit();
    }

    // Close the connection
    $conn->close();

} else {
    // If the request is not from the login form, redirect to the login page
    header("Location: index.php");
    exit();
}
