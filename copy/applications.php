<?php
// Include your database connection file
include 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    print_r($_POST);
    // Get the form data
    $id_job = $_POST['id_jobs']; // Job ID passed from the modal form
    $id_applicant = $_POST['id_applicant']; // Applicant ID passed from the modal form
    $cover_letter = $_POST['cover_letter'];

    echo "Received Job ID: " . $id_job;

    // Check if the job ID exists in the jobs table
    $stmt_check_job = $conn->prepare("SELECT * FROM jobs WHERE id_jobs = ?");
    $stmt_check_job->bind_param("i", $id_job);
    $stmt_check_job->execute();
    $result = $stmt_check_job->get_result();

    if ($result->num_rows > 0) { // If the job exists, proceed

        // Check if resume is uploaded and handle file upload
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] == UPLOAD_ERR_OK) {
            $resume_tmp_name = $_FILES['resume']['tmp_name'];
            $resume_name = basename($_FILES['resume']['name']);
            $upload_dir = 'uploads/resume/';
            $resume_path = $upload_dir . $resume_name;

            // Move the uploaded file to the server directory
            if (move_uploaded_file($resume_tmp_name, $resume_path)) {

                // Insert the application into the database
                $stmt = $conn->prepare("INSERT INTO applications (id_jobs, id_applicant, resume, cover_letter) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $id_job, $id_applicant, $resume_name, $cover_letter);

                // Execute the query
                if ($stmt->execute()) {
                    echo "Application successfully submitted!";
                    // Redirect or display success message
                    header('Location: jobs.php');
                    exit;
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Failed to upload the resume.";
            }
        } else {
            echo "Please upload a resume.";
        }
    } else {
        echo "Invalid Job ID.";
    }

    $stmt_check_job->close();
}

// Close the connection
$conn->close();
?>
