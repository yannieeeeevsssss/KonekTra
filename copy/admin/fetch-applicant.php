<?php
require_once("../db.php");

if (isset($_GET['id'])) {
  $id_applicant = $_GET['id'];

  // SQL query to fetch applicant details
  $sql = "
    SELECT 
      a.firstname, a.middlename, a.lastname, a.age, a.date_of_birth, a.gender,
      a.house_no, a.city, a.province, a.email, a.contact_no, a.about_me, 
      a.education, c.certificate_name, ct.training_center, ct.certificate_no, 
      ct.issuance_date, ct.expiration_date
    FROM applicants a
    LEFT JOIN certifications ct ON a.id_applicant = ct.id_applicant
    LEFT JOIN certificates c ON ct.id_certificate = c.id_certificate
    WHERE a.id_applicant = ?
  ";
  
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i', $id_applicant);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    // Display applicant details as read-only input fields
    echo '<p>First Name: ' . htmlspecialchars($row['firstname']) . '</p>';
    echo '<p>Middle Name: ' . htmlspecialchars($row['middlename']) . '</p>';
    echo '<p>Last Name: ' . htmlspecialchars($row['lastname']) . '</p>';
    echo '<p>Age: ' . htmlspecialchars($row['age']) . '</p>';
    echo '<p>Date of Birth: ' . htmlspecialchars($row['date_of_birth']) . '</p>';
    echo '<p>Gender: ' . htmlspecialchars($row['gender']) . '</p>';
    echo '<p>Address: ' . htmlspecialchars($row['house_no']) . ', ' . htmlspecialchars($row['city']) . ', ' . htmlspecialchars($row['province']) . '</p>';
    echo '<p>Email: ' . htmlspecialchars($row['email']) . '</p>';
    echo '<p>Contact No: ' . htmlspecialchars($row['contact_no']) . '</p>';
    echo '<p>About: ' . htmlspecialchars($row['about_me']) . '</p>';
    echo '<p>Highest Educational Attainment: ' . htmlspecialchars($row['education']) . '</p>';
    echo '<p>Certificates: ' . htmlspecialchars($row['certificate_name']) . '</p>';
    echo '<p>Training Center: ' . htmlspecialchars($row['training_center']) . '</p>';
    echo '<p>Certificate No: ' . htmlspecialchars($row['certificate_no']) . '</p>';
    echo '<p>Issuance Date: ' . htmlspecialchars($row['issuance_date']) . '</p>';
    echo '<p>Expiration Date: ' . htmlspecialchars($row['expiration_date']) . '</p>';
  } else {
    echo 'No applicant details found.';
  }

  $stmt->close();
}
?>
