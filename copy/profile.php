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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

<header class="bg-yellow-100 font-sans leading-normal tracking-normal shadow-lg py-3 px-3 sticky top-0 z-50">
  <nav class="container mx-auto flex justify-between items-center">
      <!-- Flex container for the toggle icon and logo -->
      <div class="flex items-center space-x-4">
        <!-- Sidebar Toggle Icon -->
        <a href="#" class="text-gray-600 hover:text-gray-900" id="toggleSidebar">
          <i class="fa fa-bars text-xl"></i>
        </a>

        <!-- Logo -->
        <a href="index.php" class="flex items-center space-x-2">
          <img src="img/logo.png" alt="KonekTra" class="h-10 inline-block">
        </a>
      </div>

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

 <!-- Main Container -->
<div class="flex flex-col md:flex-row">
  <!-- Sidebar -->
  <aside class="bg-blue-900 w-full md:w-64 min-h-screen shadow-md" id="sidebar">
    <div class="p-6">
      <p class="text-lg font-semibold text-white">Welcome Shun!</p>
      <ul class="mt-6 space-y-4">
        <li><a href="profile.html" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fas fa-user mr-2"></i>Profile</a></li>
        <li><a href="myapp.html" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fas fa-briefcase mr-2"></i>My Applications</a></li>
        <li><a href="messages.html" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fas fa-envelope mr-2"></i>Messages</a></li>
        <li><a href="notification.html" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fas fa-bell mr-2"></i>Notifications</a></li>
        <li><a href="#" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a></li>
      </ul>
    </div>
  </aside>

  <!-- Main Content and Right Sidebar -->
  <div class="flex flex-col md:flex-row w-full">
    <!-- Main Content -->
    <div class="md:w-2/3 p-6">
      <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <!-- Profile Header -->
        <div class="flex items-center space-x-4">
          <img src="img/profile-applicant.jpg" alt="Profile Image" class="w-16 h-16 rounded-full">
          <div class="profile-info">
            <h2 class="text-xl font-semibold text-gray-800">Shun Maxim</h2>
            <p class="text-gray-600"><i class="fas fa-toolbox"></i> Electrician &nbsp;&nbsp;<i class="fas fa-map-marker-alt"></i> Tacloban City</p>
          </div>
          <button class="ml-auto text-gray-700 hover:text-blue-600">
            <i class="fas fa-edit"></i>
          </button>
        </div>
      </div>

      <!-- About Section -->
      <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h5 class="text-lg font-semibold text-gray-800">Get To Know Shun</h5>
        <p class="text-gray-600 mt-2">No description provided.</p>
      </div>

      <!-- Comments Section -->
      <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h5 class="text-lg font-semibold text-gray-800">Comments About Shun</h5>
        <div class="flex items-center space-x-4 mb-4">
          <img src="img/profile-applicant.jpg" alt="Reviewer" class="w-10 h-10 rounded-full">
          <div>
            <p class="font-semibold text-gray-800">Reviewer Name</p>
            <p class="text-yellow-500"><i class="fas fa-star"></i> Very magaling. Recommended.</p>
          </div>
        </div>
        <form class="flex space-x-2">
          <input type="text" placeholder="Add a comment" class="flex-1 p-2 border border-gray-300 rounded-lg focus:outline-none">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-paper-plane"></i>
          </button>
        </form>
      </div>

      <!-- Uploaded Images Section -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h5 class="text-lg font-semibold text-gray-800">Uploaded Images</h5>
        <div class="grid grid-cols-3 gap-4 mt-4">
          <img src="img/tesdawork.jpg" class="w-full h-32 object-cover rounded-lg">
          <img src="img/shun.jpg" class="w-full h-32 object-cover rounded-lg">
          <div class="w-full h-32 flex items-center justify-center bg-gray-100 rounded-lg">
            <i class="fas fa-plus-square text-3xl text-gray-400"></i>
          </div>
          <div class="w-full h-32 flex items-center justify-center bg-gray-100 rounded-lg">
            <i class="fas fa-plus-square text-3xl text-gray-400"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Sidebar Info -->
    <aside class="md:w-2/5 p-6">
  <!-- Main Container for Certifications and Details -->
  <div class="bg-white p-6 rounded-lg shadow-md mb-4 border border-gray-200">
    <!-- Certifications (inside a bordered box) -->
    <div class="border border-indigo-500 p-4 rounded-md mb-6">
      <p class="text-lg font-semibold text-indigo-600 flex items-center mb-2">
        <i class="fas fa-file-alt mr-2"></i> EIM NCII
      </p>
      <p class="text-lg font-semibold text-indigo-600 flex items-center">
        <i class="fas fa-file-alt mr-2"></i> CSS NCII
      </p>
    </div>
    
    <!-- Small Square Details in Two Columns -->
    <div class="grid grid-cols-2 gap-4">
      <!-- Gender -->
      <div class="bg-white border-2 border-indigo-500 p-4 rounded-lg text-center">
        <i class="fas fa-mars text-indigo-600 text-2xl mb-2"></i>
        <p class="text-indigo-600 text-sm font-bold">Male</p>
      </div>
      
      <!-- Age -->
      <div class="bg-white border-2 border-indigo-500 p-4 rounded-lg text-center">
        <i class="fas fa-calendar-alt text-indigo-600 text-2xl mb-2"></i>
        <p class="text-indigo-600 text-sm font-bold">27</p>
      </div>

      <!-- Education -->
      <div class="bg-white border-2 border-indigo-500 p-4 rounded-lg text-center">
        <i class="fas fa-graduation-cap text-indigo-600 text-2xl mb-2"></i>
        <p class="text-indigo-600 text-sm font-bold">High School Graduate</p>
      </div>

      <!-- Email -->
      <div class="bg-white border-2 border-indigo-500 p-4 rounded-lg text-center">
        <i class="fas fa-envelope text-indigo-600 text-2xl mb-2"></i>
        <p class="align-self-center text-indigo-600 text-sm font-bold">gmail.com</p>
      </div>
    </div>
  </div>
</aside>

  </div>
</div>

  <!-- Footer -->
<!-- <footer class="bg-indigo-900 py-4">
    <div class="container mx-auto text-center text-white">
      &copy; 2024 KonekTra. All Rights Reserved.
    </div>
  </footer> -->


<!-- Scripts for Navbar and Modal Menu -->
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
<!-- Toggle Sidebar Script -->
<script>
    document.getElementById('toggleSidebar').addEventListener('click', function () {
      document.getElementById('sidebar').classList.toggle('hidden');
    });





    function approveApplicant(id, name) {
    // Show the modal
    $("#approveModal").modal("show");

    // Populate the modal fields
    $("#applicantName").text(name);

    // Set the approval link with the applicant's ID
    $("#approveLink").attr("href", "approve-applicant.php?id=" + id);
}

    function confirmDelete(id, name) {
        // Show the delete modal
        $("#deleteModal").modal("show");

        // Populate the modal fields
        $("#deleteApplicantName").text(name);

        // Set the delete link with the applicant's ID
        $("#deleteLink").attr("href", "delete-applicant.php?id=" + id);
    }


  </script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@1.5.1/dist/flowbite.min.js"></script>
</body>
</html>
