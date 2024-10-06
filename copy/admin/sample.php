<?php
// To Handle Session Variables on This Page
session_start();

// Including Database Connection
require_once("db.php");
require 'vendor/autoload.php';

// If user clicked register button
if (isset($_POST)) {

    // Escape Special Characters In String First
    $lastname = mysqli_real_escape_string($conn, $_POST['lname']);
    $firstname = mysqli_real_escape_string($conn, $_POST['fname']);
    $middlename = mysqli_real_escape_string($conn, $_POST['mname']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $house_no = mysqli_real_escape_string($conn, $_POST['house_no']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $province = mysqli_real_escape_string($conn, $_POST['province']);
    $contactno = mysqli_real_escape_string($conn, $_POST['contactno']);
    $education = mysqli_real_escape_string($conn, $_POST['education']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $aboutme = mysqli_real_escape_string($conn, $_POST['aboutme']);
    
    if (is_array($_POST['preferred_job'])) {
        $preferred_job = implode(", ", $_POST['preferred_job']);
    } else {
        $preferred_job = mysqli_real_escape_string($conn, $_POST['preferred_job']); // If it's a single value
    }

    // Encrypt password
    $password = base64_encode(strrev(md5($password)));


    function calculateAge($dob) {
        $dobDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($dobDate)->y;
        return $age;
    }
    // Check age
$dob = $_POST['dob'];
$age = calculateAge($dob);
if ($age < 18) {
    // Redirect back with an error
    $_SESSION['registerError'] = "You must be 18 or older to register.";
    header('Location: register-candidates.php');
    exit();
}

// Function to calculate age


// Check password mismatch
if ($_POST['password'] !== $_POST['cpassword']) {
    $_SESSION['registerError'] = "Passwords do not match!";
    header('Location: register-candidates.php');
    exit();
}

// Check CAPTCHA
if (empty($_POST['g-recaptcha-response'])) {
    $_SESSION['registerError'] = "Please verify the captcha!";
    header('Location: register-candidates.php');
    exit();
}

// Validate issuance and expiration dates
foreach ($_POST['issuance_date'] as $index => $issuanceDate) {
    $expirationDate = $_POST['expiration_date'][$index];
    if (strtotime($expirationDate) <= strtotime($issuanceDate)) {
        $_SESSION['registerError'] = "Expiration date must be later than issuance date for certificate $index.";
        header('Location: register-candidates.php');
        exit();
    }
}


    // SQL query to check if email already exists
    $sql = "SELECT email FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {

        $uploadOk = true;

        // Handle Resume upload
        $folder_dir = "uploads/resume/";
        $base = basename($_FILES['resume']['name']);
        $resumeFileType = pathinfo($base, PATHINFO_EXTENSION);
        $file = uniqid() . "." . $resumeFileType;
        $filename = $folder_dir . $file;

        if (file_exists($_FILES['resume']['tmp_name'])) {
            if ($resumeFileType == "pdf" && $_FILES['resume']['size'] < 5000000) {
                move_uploaded_file($_FILES["resume"]["tmp_name"], $filename);
            } else {
                $_SESSION['uploadError'] = "Invalid file format or size.";
                $uploadOk = false;
            }
        } else {
            $_SESSION['uploadError'] = "Error uploading resume.";
            $uploadOk = false;
        }

        // Handle profile image upload
        $folder_dir_profile = "uploads/profile/";
        $base_profile = basename($_FILES['applicant_image']['name']);
        $profileFileType = pathinfo($base_profile, PATHINFO_EXTENSION);
        $file_profile = uniqid() . "." . $profileFileType;
        $filename_profile = $folder_dir_profile . $file_profile;

        if (file_exists($_FILES['applicant_image']['tmp_name'])) {
            if (in_array($profileFileType, ["jpg", "jpeg", "png"]) && $_FILES['applicant_image']['size'] < 5000000) {
                move_uploaded_file($_FILES["applicant_image"]["tmp_name"], $filename_profile);
            } else {
                $_SESSION['uploadError'] = "Invalid profile image format or size.";
                $uploadOk = false;
            }
        } else {
            $_SESSION['uploadError'] = "Error uploading profile image.";
            $uploadOk = false;
        }

        // If file uploads are successful
        if ($uploadOk) {
            $hash = md5(uniqid());

            // Insert data into users table
            $sql = "INSERT INTO users(email, password, profile_image, user_type, hash, active, status) VALUES ('$email', '$password',  '$file_profile', 'applicant', '$hash', 1, 'Pending')";
            if ($conn->query($sql) === TRUE) {
                $id_user = $conn->insert_id;

                // Insert applicant data into applicants table
                $sql_applicant = "INSERT INTO applicants(id_user, firstname, lastname, middlename, gender, age, dob, street, id_city, id_province, contactno, aboutme, preferred_job, education, resume) 
                                  VALUES ('$id_user', '$firstname', '$lastname', '$middlename', '$gender', '$age', '$dob', '$house_no', '$city', '$province', '$contactno', '$aboutme', '$preferred_job', '$education', '$file')";
                $conn->query($sql_applicant);
                $id_applicant = $conn->insert_id;

                // Insert certifications into the certifications table
                foreach ($_POST['certificate_name'] as $key => $certificate_name) {
                    $certificate_no = mysqli_real_escape_string($conn, $_POST['certificate_no'][$key]);
                    $training_center = mysqli_real_escape_string($conn, $_POST['training_center'][$key]);
                    $issuance_date = mysqli_real_escape_string($conn, $_POST['issuance_date'][$key]);
                    $expiration_date = mysqli_real_escape_string($conn, $_POST['expiration_date'][$key]);
                    $sector = mysqli_real_escape_string($conn, $_POST['sector']);
                    $file_cert = $_FILES['certificate_image']['name'][$key];

                    // Handle Certificate Image Upload
                    $folder_dir_cert = "uploads/certificate/";
                    $base_cert = basename($file_cert);
                    $certificateFileType = pathinfo($base_cert, PATHINFO_EXTENSION);
                    $filename_cert = $folder_dir_cert . uniqid() . "." . $certificateFileType;
                    move_uploaded_file($_FILES['certificate_image']['tmp_name'][$key], $filename_cert);

                    $sql_cert = "INSERT INTO certifications(id_certificate, training_center, certificate_no, issuance_date, expiration_date, sector, certificate_image, id_applicant) 
                                 VALUES ('$certificate_name', '$training_center', '$certificate_no', '$issuance_date', '$expiration_date', '$sector', '$filename_cert', '$id_applicant')";
                    $conn->query($sql_cert);
                }

                // Send verification email
                $to = $email;
                $subject = "Job Portal - Confirm Your Email Address";
                $message = '<html><body><p>Click Link To Confirm</p><a href="yourdomain.com/verify.php?token=' . $hash . '&email=' . $email . '">Verify Email</a></body></html>';
                $headers = 'MIME-VERSION: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'To: ' . $to . "\r\n";
                $headers .= 'From: hello@yourdomain.com' . "\r\n";
                mail($to, $subject, $message, $headers);

                $_SESSION['registerCompleted'] = true;
                header("Location: login-candidates.php");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            header("Location: register-candidates.php");
            exit();
        }
    } else {
        $_SESSION['registerError'] = true;
        header("Location: register-candidates.php");
        exit();
    }

    $conn->close();
} else {
    header("Location: register-candidates.php");
    exit();
}
?>
