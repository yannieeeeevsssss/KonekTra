<?php
// Include your database connection
require_once '../../db.php';

// Start session to retrieve employer info
session_start();

// Check if the user is logged in and is an employer
if (!isset($_SESSION['id_user']) || $_SESSION['user_type'] !== 'employer') {
    echo "You must be logged in as an employer to post a job.";
    exit;
}

// Get the logged-in employer's ID from the `employers` table
$id_user = $_SESSION['id_user'];

// Fetch the employer's `id_employer` from the `employers` table
$query = "SELECT id_employer FROM employers WHERE id_user = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$employer = $result->fetch_assoc();

if (!$employer) {
    echo "Employer not found.";
    exit;
}

$id_employer = $employer['id_employer']; // This will be used for inserting the job

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get the POST data and sanitize it
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $min_salary = mysqli_real_escape_string($conn, $_POST['min_salary']);
    $max_salary = mysqli_real_escape_string($conn, $_POST['max_salary']);
    $job_types = isset($_POST['job_type']) ? implode(',', $_POST['job_type']) : ''; // Multiple job types
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);

    // Validation
    if (empty($job_title) || empty($location) || empty($description) || empty($deadline)) {
        echo "Please fill in all required fields.";
        exit;
    }

    // Prepare SQL insert query with employer ID
    $sql = "INSERT INTO jobs (id_employer, job_title, location, description, min_salary, max_salary, job_type, deadline) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "isssssss", 
        $id_employer, $job_title, $location, $description, $min_salary, $max_salary, $job_types, $deadline
    );

    // Execute the query
    if ($stmt->execute()) {
        // Job posted successfully, redirect to jobs.php
        header("Location: ../../jobs.php");
        exit; // Make sure to exit after the header redirect
    } else {
        echo "Error: " . $conn->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
