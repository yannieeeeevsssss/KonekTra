<?php
// reject.php

require_once("../../db.php");

$id_applicant = $_GET['id'];
$id_job = $_GET['job_id'];

if (isset($id_applicant) && isset($id_job)) {
    $query = "UPDATE applications SET status = 'Rejected' WHERE id_applicant = ? AND id_jobs = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id_applicant, $id_job);

    if ($stmt->execute()) {
        // Redirect back with the job ID
        header("Location: ../viewapplicants.php?id_job=$id_job&message=Applicant rejected successfully.");
    } else {
        echo "Error updating application status.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();

?>
