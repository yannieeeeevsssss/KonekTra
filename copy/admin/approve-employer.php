<?php
session_start();
if (empty($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

require_once("../db.php");

// Check if the employer ID is provided
if (isset($_GET['id'])) {
    $id_employer = intval($_GET['id']);

    // Prepare and execute the update query
    $sql = "UPDATE users SET status = 'Active' WHERE id_user = (SELECT id_user FROM employers WHERE id_employer = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_employer);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Employer successfully approved!";
    } else {
        $_SESSION['message'] = "Failed to approve employer.";
    }

    // Redirect back to the manage employers page
    header("Location: man-employers.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid employer ID.";
    header("Location: man-employers.php");
    exit();
}
