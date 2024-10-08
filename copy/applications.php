<?php
// Include your database connection file
include 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get the form data
    $id_job = $_POST['id_job']; // This should be passed in a hidden field in the form
    $id_applicant = $_POST['id_applicant']; // Applicant ID should also be passed
    $cover_letter = $_POST['cover_letter'];
    
    // File upload handling
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == UPLOAD_ERR_OK) {
        $resume_tmp_name = $_FILES['resume']['tmp_name'];
        $resume_name = basename($_FILES['resume']['name']);
        $upload_dir = 'uploads/resumes/';
        $resume_path = $upload_dir . $resume_name;
        
        // Move the uploaded file to the server directory
        if (move_uploaded_file($resume_tmp_name, $resume_path)) {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO applications (id_job, id_applicant, resume, cover_letter) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $id_job, $id_applicant, $resume_name, $cover_letter);
            
            // Execute the query
            if ($stmt->execute()) {
                echo "Application successfully submitted!";
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
}

// Close the connection
$conn->close();
?>
