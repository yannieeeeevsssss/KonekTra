<?php
// Database connection
require_once('db.php');

// Fetch all certificate from the database
$query = "SELECT * FROM certificates";
$result = $conn->query($query);

// Output each certificate as an <option> tag
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['id_certificate'] . '">' . $row['certificate_name'] . '</option>';
    }
} else {
    echo '<option value="">No certificates found</option>';
}

// Close the database connection
$conn->close();
?>
