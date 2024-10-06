<?php
// Database connection
require_once('db.php');

// Fetch all cities from the database
$sql = "SELECT * FROM cities";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // Output each city as an option
  while($row = $result->fetch_assoc()) {
    echo '<option value="' . $row['id_city'] . '">' . $row['city_name'] . '</option>';
  }
} else {
  echo '<option value="">No cities found</option>';
}

// Close the database connection
$conn->close();
?>
