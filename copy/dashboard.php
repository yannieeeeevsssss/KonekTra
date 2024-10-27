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
$user_type = '';

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
        $user_type = $row['user_type'];
        
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - KonekTra</title>
    
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

  <!-- Tailwind CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <!-- Flowbite CDN -->
  <script src="https://unpkg.com/flowbite@1.4.7/dist/flowbite.js"></script>

  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <style>
    /* Animation for moving icon */
    .bounce {
      animation: bounce 2s infinite;
    }

    @keyframes bounce {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-10px);
      }
    }
  </style>
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

<!-- Dashboard Content -->
<div class="container mx-auto flex justify-center items-center h-screen px-4 text-center">
  <?php if (!$logged_in): ?>
    <!-- Guest User -->
    <div class="">
      <i class="fa fa-exclamation-circle text-indigo-500 text-6xl bounce"></i>
      <h2 class="text-3xl font-bold mt-4">There's nothing here yet!</h2>
      <p class="text-gray-600 mt-2">Please <a href="templogin.php" class="text-indigo-500 font-semibold underline">login</a> or <a href="sign-up.php" class="text-indigo-500 font-semibold underline">sign up</a> to start using your dashboard.</p>
    </div>

  <?php elseif ($status == 'Pending'): ?>
    <!-- Account Pending -->
    <div class="">
      <i class="fa fa-hourglass-half text-yellow-500 text-6xl bounce"></i>
      <h2 class="text-3xl font-bold mt-4">Your account is being processed!</h2>
      <p class="text-gray-600 mt-2">It will not take long to process your account. Please check back soon.</p>
    </div>

  <?php elseif ($status == 'Active'): ?>
    <!-- Redirect based on user type -->
    <?php
      if ($user_type == 'applicant') {
        header("Location: applicants/profile.php");
        exit();
      } elseif ($user_type == 'employer') {
        header("Location: employer/dashboard.php");
        exit();
      }
    ?>

  <?php else: ?>
    <!-- Unknown Status -->
    <div class="">
      <i class="fa fa-exclamation-circle text-red-500 text-6xl bounce"></i>
      <h2 class="text-3xl font-bold mt-4">Unexpected account status!</h2>
      <p class="text-gray-600 mt-2">Please contact support for assistance.</p>
    </div>
  <?php endif; ?>
</div>

<!-- Script for navbar and mobile menu toggle -->
<script>
  const navbarToggle = document.getElementById('navbar-toggle');
  const mobileMenu = document.getElementById('mobile-menu');
  const closeMenuButton = document.getElementById('close-menu');
  const userMenuToggle = document.getElementById('user-menu-toggle');
  const userMenu = document.getElementById('user-menu');

  // Toggle full-screen mobile menu
  navbarToggle.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });

  // Close mobile menu when clicking the close button (X icon)
  closeMenuButton.addEventListener('click', () => {
    mobileMenu.classList.add('hidden');
  });

  // Close mobile menu when clicking outside (e.g., clicking the navbar toggle again)
  window.addEventListener('click', (e) => {
    if (e.target === mobileMenu || e.target === navbarToggle) {
      mobileMenu.classList.add('hidden');
    }
  });

  // Toggle user menu dropdown (desktop)
  userMenuToggle?.addEventListener('click', () => {
    userMenu.classList.toggle('hidden');
  });

  // Hide mobile menu when screen becomes wider
  window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
      mobileMenu.classList.add('hidden');
    }
  });
</script>

</body>
</html>
