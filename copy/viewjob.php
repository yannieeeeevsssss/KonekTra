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
if (isset($_GET['id'])) {
    $job_id = $_GET['id'];

    $job_query = "
        SELECT jobs.*, employers.company_name, employers.email AS employer_email, employers.contactno, cities.city_name 
        FROM jobs 
        JOIN employers ON jobs.id_employer = employers.id_employer
        LEFT JOIN cities ON employers.id_city = cities.id_city
        WHERE jobs.id_jobs = ?
    ";
    
    $stmt = $conn->prepare($job_query);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $job_result = $stmt->get_result();
    $job = $job_result->fetch_assoc();
    $stmt->close();
}

if (!$job) {
    // If no job is found, redirect to jobs.php
    header("Location: jobs.php");
    exit;
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

  <!-- Full-Screen Modal-like Overlay for Mobile -->
  <div id="mobile-menu" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex flex-col items-center justify-center">
    <button id="close-menu" class="text-white absolute top-4 right-4 focus:outline-none">
      <i class="fa fa-times fa-2x"></i> <!-- Close button (X icon) -->
    </button>

    <a href="jobs.php" class="text-white text-2xl mb-6">Jobs</a>
    <a href="about.php" class="text-white text-2xl mb-6">About Us</a>

    <?php if (!$logged_in) { ?>
      <a href="dashboard.php" class="text-white text-2xl mb-6">Dashboard</a>
      <a href="login.php" class="text-white text-2xl mb-6">Login</a>
      <a href="signup.php" class="text-white text-2xl mb-6">Sign Up</a>
    <?php } else { ?>
      <a href="dashboard.php" class="text-white text-2xl mb-6">Dashboard</a>
      <a href="notification.php" class="text-white text-2xl mb-6">Notification</a>
      <a href="logout.php" class="text-white text-2xl mb-6">Logout</a>
    <?php } ?>
  </div>
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
                    <h2 class="text-2xl font-bold text-indigo-900"><?= htmlspecialchars($job['job_title']); ?></h2>
                    <p class="text-gray-500 font-semibold"><i class="fas fa-building mr-1"></i> <?= htmlspecialchars($job['company_name']); ?></p>
                    <p class="text-gray-500 font-semibold"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['city_name']); ?></p>
                </div>
            </div>
            <div class="space-x-2">
                <button class="bg-indigo-900 text-white py-2 px-8 rounded hover:bg-blue-800 font-bold" data-toggle="modal" data-target="#applyModal">Apply</button>
            </div>
        </div>

        <!-- Job Description -->
        <div class="mt-4">
    <h3 class="text-xl font-semibold text-blue-900">Job Description</h3>
    <p class="text-gray-600 mt-2"><?= nl2br(html_entity_decode($job['description'])); ?></p>
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
                    <p class="text-gray-600"><?= htmlspecialchars($job['deadline']); ?></p>
                </div>
            </div>
        </div>

        <!-- Job Information -->
        <div class="bg-white p-4 rounded-lg shadow-lg space-y-4">
            <!-- Salary -->
            <div class="flex items-center space-x-3">
                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                <div>
                    <h4 class="text-lg font-bold text-blue-900">Salary</h4>
                    <p class="text-gray-600">₱<?= number_format($job['min_salary']); ?> - ₱<?= number_format($job['max_salary']); ?></p>
                </div>
            </div>

            <!-- Job Type -->
            <div class="flex items-center space-x-3">
                <i class="fas fa-briefcase text-indigo-600 text-xl"></i>
                <div>
                    <h4 class="text-lg font-bold text-blue-900">Job Type</h4>
                    <p class="text-gray-600"><?= htmlspecialchars($job['job_type']); ?></p>
                </div>
            </div>

            <!-- Job Location -->
            <div class="flex items-center space-x-3">
                <i class="fas fa-map-marker-alt text-red-600 text-xl"></i>
                <div>
                    <h4 class="text-lg font-bold text-blue-900">Job Location</h4>
                    <p class="text-gray-600"><?= htmlspecialchars($job['location']); ?></p>
                </div>
            </div>
        </div>

    </div>
</div>


    <!-- Apply Job Modal -->
    <div class="modal fade" id="applyModal" tabindex="-1" role="dialog" aria-labelledby="applyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyModalLabel">Apply Job</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <!-- Resume Upload -->
                        <div class="form-group">
                            <label for="resume">Resume</label>
                            <input type="file" class="form-control-file" id="resume">
                        </div>
                        <!-- Cover Letter Textarea -->
                        <div class="form-group">
                            <label for="coverLetter">Cover Letter</label>
                            <textarea class="form-control" id="coverLetter" rows="3" placeholder="Why should we hire you?"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Apply</button>
                </div>
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
