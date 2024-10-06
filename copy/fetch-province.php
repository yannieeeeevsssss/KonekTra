<?php
require_once("db.php");

$sql = "SELECT * FROM provinces";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['id_province'] . '">' . $row['province_name'] . '</option>';
    }
} else {
    echo '<option value="">No Provinces Available</option>';
}

$conn->close();
?>
