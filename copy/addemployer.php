<?php
// To handle session variables on this page
session_start();

// Including database connection
require_once("db.php");
require 'vendor/autoload.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// If the user clicked register button
if (isset($_POST)) {
    // Escape special characters in string first
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
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $regno = mysqli_real_escape_string($conn, $_POST['regno']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $aboutme = mysqli_real_escape_string($conn, $_POST['aboutme']);
    
    // Encrypt password
    $password = base64_encode(strrev(md5($password)));

    // Validate age
    function calculateAge($dob) {
        $dobDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($dobDate)->y;
        return $age;
    }
    $age = calculateAge($dob);
    if ($age < 18) {
        $_SESSION['registerError'] = "You must be 18 or older to register.";
        header('Location: register-employer.php');
        exit();
    }

    // Check password mismatch
    if ($_POST['password'] !== $_POST['cpassword']) {
        $_SESSION['registerError'] = "Passwords do not match!";
        header('Location: register-employer.php');
        exit();
    }

    // Check CAPTCHA
    if (empty($_POST['g-recaptcha-response'])) {
        $_SESSION['registerError'] = "Please verify the captcha!";
        header('Location: register-employer.php');
        exit();
    }

    // SQL query to check if email already exists
    $sql = "SELECT email FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {

        // Handle profile image upload
        $uploadOk = true;
        $folder_dir = "uploads/profile/";
        $base = basename($_FILES['employer_image']['name']);
        $imageFileType = pathinfo($base, PATHINFO_EXTENSION);
        $file = uniqid() . "." . $imageFileType;
        $filename = $folder_dir . $file;

        if (file_exists($_FILES['employer_image']['tmp_name'])) {
            if (in_array($imageFileType, ["jpg", "jpeg", "png"]) && $_FILES['employer_image']['size'] < 5000000) {
                move_uploaded_file($_FILES["employer_image"]["tmp_name"], $filename);
            } else {
                $_SESSION['uploadError'] = "Invalid image format or size.";
                $uploadOk = false;
            }
        } else {
            $_SESSION['uploadError'] = "Error uploading image.";
            $uploadOk = false;
        }

        if ($uploadOk) {
            // Insert into users table
            $hash = md5(uniqid());
            $sql = "INSERT INTO users (email, password, profile_image, user_type, hash, active, created_at) 
                    VALUES ('$email', '$password', '$file', 'employer', '$hash', 0, NOW())";
            
            if ($conn->query($sql) === TRUE) {
                $id_user = $conn->insert_id;

                // Insert into employers table
                $sql = "INSERT INTO employers (id_user, firstname, middlename, lastname, gender, dob, age, street, id_city, id_province, contactno, email, company_name, registration_no, aboutme) 
                        VALUES ('$id_user', '$firstname', '$middlename', '$lastname', '$gender', '$dob', '$age', '$house_no', '$city', '$province', '$contactno', '$email', '$company_name', '$regno', '$aboutme')";

if ($conn->query($sql) === TRUE) {
    // Email verification starts here
    $mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
    $mail->SMTPAuth = true;    // Enable SMTP authentication
    $mail->Username = 'lorem.ipsum.sample.email@gmail.com';  // Your Gmail email
    $mail->Password = 'novtycchbrhfyddx';  // Your Gmail password or app-specific password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // TLS encryption, or PHPMailer::ENCRYPTION_SMTPS for SSL
    $mail->Port = 587;  // TCP port for TLS (587); change to 465 for SSL

    // Recipients
    $mail->setFrom('lorem.ipsum.sample.email@gmail.com', 'KonekTra');  // Sender's email and name
    $mail->addAddress($email);  // Add recipient (user's email)

    // Content
    $mail->isHTML(true);  // Set email format to HTML
    $mail->Subject = 'KonekTra - Confirm Your Email Address';

    // Compose the verification email
    $verification_link = 'http://localhost/copy/verify.php?token=' . $hash . '&email=' . $email;
    $mail->Body = '<html><body><p>Click the following link to confirm your email address:</p>';
    $mail->Body .= '<a href="' . $verification_link . '">Verify Email</a></body></html>';

    // Send the email
    $mail->send();
    echo '<script>alert("Check your email for verification.");</script>';
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

    $_SESSION['registerSuccess'] = "Your account has been created. Please check your email for approval.";
    header("Location: login.php");
    exit();
} else {
    $_SESSION['registerError'] = "Error: Could not insert employer details.";
    header("Location: register-employer.php");
    exit();
}
} else {
$_SESSION['registerError'] = "Error: Could not create user account.";
header("Location: register-employer.php");
exit();
}
}
} else {
$_SESSION['registerError'] = "Email already exists!";
header('Location: register-employer.php');
exit();
}

$conn->close();
} else {
header("Location: register-employer.php");
exit();
}
?>