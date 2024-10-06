<?php
session_start();
if (empty($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

require_once("../db.php");

// Check if the applicant ID is provided
if (isset($_GET['id'])) {
    $id_applicant = intval($_GET['id']);

    // Prepare and execute the delete query
    $sql = "DELETE FROM applicants WHERE id_applicant = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_applicant);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Applicant successfully deleted!";
    } else {
        $_SESSION['message'] = "Failed to delete applicant.";
    }

    // Redirect back to the manage applicants page
    header("Location: man-applicants.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid applicant ID.";
    header("Location: man-applicants.php");
    exit();
}
