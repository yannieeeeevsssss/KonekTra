<?php
// Start session
session_start();

// Database connection (adjust according to your setup)
require_once("db.php");

// Initialize variables
$default_avatar = 'img/default-avatar.png'; // Default avatar
$status = '';
$logged_in = false;
$profile_image_path = ''; // To store the full profile image path

// Check if the user is logged in
if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];

    // Fetch user details from the database
    $query = "SELECT user_type, profile_image, status FROM users WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $status = $row['status'];
        
        // Check if the user has uploaded a profile image
        if (!empty($row['profile_image'])) {
            // The profile image is stored in the 'uploads/profile/' directory
            $profile_image_path = 'uploads/profile/' . $row['profile_image'];
        } else {
            // If no profile image, use the default avatar
            $profile_image_path = $default_avatar;
        }
        
        $logged_in = true;
    }

    $stmt->close();
}

// Fetch job details based on job ID from the query parameter
if (isset($_GET['id_job'])) {
    $job_id = $_GET['id_job'];
    
    $query = "
        SELECT jobs.job_title, jobs.location AS job_location, jobs.description, jobs.min_salary, jobs.max_salary, jobs.job_type, jobs.deadline, employers.company_name, cities.city_name
        FROM jobs
        INNER JOIN employers ON jobs.id_employer = employers.id_employer
        LEFT JOIN cities ON employers.id_city = cities.id_city
        WHERE jobs.id_jobs = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $job = $result->fetch_assoc();
    } else {
        echo "Job not found!";
        exit;
    }
    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>KonekTra</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

  <link rel="stylesheet" href="css/style.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <!-- Tailwind CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <!-- Flowbite CDN -->
  <script src="https://unpkg.com/flowbite@1.4.7/dist/flowbite.js"></script>

  <!-- Tagify CSS and JS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
  <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
  <!-- Recaptcha -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <!-- Jquery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- FontAwesome-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<header class="bg-yellow-100 font-sans leading-normal tracking-normal shadow-lg py-3 px-3 sticky top-0 z-50">
  <nav class="container mx-auto flex justify-between items-center">
    <!-- Logo -->
    <a href="index.php" class="text-white font-bold text-xl">
      <img src="img/logo.png" alt="KonekTra" class="h-10 inline-block"> <!-- Replace with your logo -->
    </a>

    <!-- Hamburger Menu (Visible on smaller screens) -->
    <div class="md:hidden">
      <button id="navbar-toggle" class="text-blue-900 focus:outline-none">
        <i class="fa fa-bars fa-2x"></i>
      </button>
    </div>

    <!-- Navbar Links for Desktop -->
    <div class="hidden md:flex space-x-6 text-blue-900 items-center">
      <a href="jobs.php" class="font-bold">Jobs</a>
      <a href="about.php" class="font-bold">About Us</a>

      <?php if (!$logged_in) { ?>
        <!-- Guest User -->
        <a href="dashboard.php" class="font-bold">Dashboard</a>
        <a href="login.php" class="font-bold">Login</a>
        <a href="sign-up.php" class="font-bold">Sign Up</a>
      <?php } else { ?>
        <!-- Logged-in User -->
        <a href="dashboard.php" class="font-bold">Dashboard</a>
        <a href="notification.php" class="font-bold">Notification</a>

        <!-- User Avatar -->
        <div class="relative">
          <button id="user-menu-toggle" class="focus:outline-none">
            <img src="<?= $profile_image_path; ?>" class="w-9 h-9 rounded-full border-2 border-indigo-900" alt="User Avatar">
          </button>
          <!-- Dropdown Menu -->
          <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg">
            <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
            <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
          </div>
        </div>
      <?php } ?>
    </div>
  </nav>

</header>
    
    <!-- Job View Container -->
    <div class="container mx-auto py-8 flex justify-between space-x-8">

        <!-- Job Detail Section -->
        <div class="w-2/3 bg-yellow-50 p-6 rounded-lg shadow-lg">
        <a href="jobs.php" class="flex items-center text-blue-900 hover:text-blue-700 mb-2 hover:no-underline">
        <i class="fa fa-arrow-left text-xl mr-2"></i> Back to Jobs
        </a>
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <a href="prof-comp.html">
                        <img src="img/company.png" alt="Company Logo" class="w-20 h-20 rounded-md">
                    </a>
                    <div>
                        <h2 class="text-2xl font-bold text-indigo-900">Front Desk Officer</h2>
                        <p class="text-gray-500 font-semibold"><i class="fas fa-building mr-1"></i>CURA Corporation</p>
                        <p class="text-gray-500 font-semibold"><i class="fas fa-map-marker-alt"></i> Abuyog, Leyte</p>
                    </div>
                </div>
                <div class="space-x-2">
                    <!-- <button class="bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">Message</button> -->
                    <button class="bg-indigo-900 text-white py-2 px-8 rounded hover:bg-blue-800 font-bold" data-toggle="modal" data-target="#applyModal">Apply</button>
                </div>
            </div>
            
            <!-- Job Description -->
            <div class="mt-4">
                <h3 class="text-xl font-semibold text-blue-900">Job Description</h3>
                <p class="text-gray-600 mt-2">
                    We are looking for a professional Front Desk Officer to oversee all receptionist and secretarial duties at our main entrance desk.
                    You will perform a range of duties including answering phone calls, managing the switchboard, and maintaining the office budget.
                    Your central goal is to provide our clients with outstanding customer service and support.
                    As the “face” of our company, the successful candidate will be presentable and friendly, with outstanding people skills.
                </p>
            </div>
            
            <!-- Qualifications -->
            <div class="mt-6">
                <h3 class="text-xl font-semibold text-blue-900">Qualifications</h3>
                <p class="text-gray-600 mt-2">
                    • Proven work experience as a Receptionist, Front Office Representative or similar role<br>
                    • Proficiency in Microsoft Office Suite<br>
                    • Hands-on experience with office equipment (e.g., fax machines and printers)<br>
                    • Professional attitude and appearance<br>
                    • Solid written and verbal communication skills<br>
                    • Ability to be resourceful and proactive when issues arise<br>
                    • Excellent organizational skills
                </p>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="w-1/3 space-y-4">
    <!-- Date Information -->
    <div class="bg-white p-4 rounded-lg shadow-lg space-y-4">
    <!-- Date Posted -->
    <div class="flex items-center space-x-3">
        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
        <div>
            <h4 class="text-lg font-bold text-blue-900">Date Posted</h4>
            <p class="text-gray-600">Sept. 15, 2024</p>
        </div>
    </div>
    
    <!-- Deadline -->
    <div class="flex items-center space-x-3">
        <i class="fas fa-calendar-check text-red-600 text-xl"></i>
        <div>
            <h4 class="text-lg font-bold text-blue-900">Deadline</h4>
            <p class="text-gray-600">Sept. 30, 2024</p>
        </div>
    </div>
</div>

    <!-- Salary Information -->
    <div class="bg-white p-4 rounded-lg shadow-lg flex items-center space-x-3">
        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
        <div>
            <h4 class="text-lg font-bold text-blue-900">₱18,000 - ₱20,000</h4>
        </div>
    </div>

    <!-- Job Type Information -->
    <div class="bg-white p-4 rounded-lg shadow-lg flex items-center space-x-3">
        <i class="fas fa-briefcase text-yellow-600 text-xl"></i>
        <div>
            <h4 class="text-lg font-bold text-blue-900">Full-Time • On-site</h4>
        </div>
    </div>

    <!-- Location Information -->
    <div class="bg-white p-4 rounded-lg shadow-lg flex items-center space-x-3">
        <i class="fas fa-map-marker-alt text-red-600 text-xl"></i>
        <div>
            <h4 class="text-lg font-bold text-blue-900">Abuyog, Leyte</h4>
        </div>
    </div>
</div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Flowbite JS (for interactivity) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
</body>
</html>
