<?php
require_once('../db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch employer details from the database
    $sql = "SELECT e.*, u.email FROM employers e JOIN users u ON e.id_user = u.id_user WHERE e.id_employer = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $employer = $result->fetch_assoc();
        echo json_encode($employer);
    } else {
        echo json_encode(['error' => 'No employer found']);
    }
}
?>
