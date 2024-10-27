<?php
// closejob.php
session_start();
require_once("../../db.php");

// Check if the job ID is set
if (isset($_GET['id'])) {
    $id_jobs = $_GET['id'];

    // Prepare the SQL query to delete the job
    $sql = "DELETE FROM jobs WHERE id_jobs = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_jobs);

    if ($stmt->execute()) {
        // Successfully deleted the job
        $_SESSION['message'] = "Job and related applications have been successfully deleted.";
    } else {
        // Failed to delete the job
        $_SESSION['error'] = "Failed to delete the job. Please try again.";
    }

    // Redirect back to the My Jobs page
    header("Location: ../myjobs.php");
    exit();
} else {
    // If no job ID is provided, redirect back with an error message
    $_SESSION['error'] = "Invalid job ID.";
    header("Location: myjobs.php");
    exit();
}
?>