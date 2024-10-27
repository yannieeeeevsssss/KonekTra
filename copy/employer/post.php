<?php
// Start session
session_start();

// Database connection
require_once("../db.php");

// Initialize variables
$default_avatar = 'img/default-avatar.png'; // Default avatar
$status = '';
$logged_in = false;
$profile_image_path = ''; // To store the full profile image path
$welcome_message = 'Welcome Admin!'; // Default message

// Check if the user is logged in
if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];

    // Fetch user and employer details from the database
    $query = "
        SELECT u.user_type, u.profile_image, u.status, e.firstname, e.lastname 
        FROM users u 
        LEFT JOIN employers e ON u.id_user = e.id_user 
        WHERE u.id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $status = $row['status'];
        
        // Check if the user has uploaded a profile image
        if (!empty($row['profile_image'])) {
            $profile_image_path = '../uploads/profile/' . $row['profile_image'];
        } else {
            $profile_image_path = $default_avatar;
        }

        // Set the welcome message for employers
        if ($row['user_type'] == 'employer') {
            $welcome_message = 'Welcome ' . $row['firstname'] . '!';
            $logged_in = true;
        } else {
            // If not an employer, redirect to index.php
            header("Location: ../index.php");
            exit();
        }
    }

    $stmt->close();
} else {
    // If not logged in, redirect to index.php
    header("Location: ../index.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>KonekTra</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
  <!-- TinyMCE -->
  <script src="../js/tinymce/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#description',
            height: 300,
            forced_root_block: false,
            force_br_newlines: true,
            force_p_newlines: false,
            convert_newlines_to_brs: true,
            content_style: "body { font-family:Helvetica,Arial,sans-serif; font-size:14px }",
        });
    </script>
</head>

<body>

<header class="bg-yellow-100 font-sans leading-normal tracking-normal shadow-lg py-3 px-3 sticky top-0 z-50">
  <nav class="container mx-auto flex justify-between items-center">
  <div class="flex items-center space-x-4">
    <!-- Logo -->
    <a href="#" class="text-indigo-900 hover:text-blue-800" id="toggleSidebar">
          <i class="fa fa-bars text-xl"></i> <!-- FontAwesome hamburger icon -->
        </a>
    <a href="../index.php" class="text-white font-bold text-xl">
      <img src="../img/logo.png" alt="KonekTra" class="h-10 inline-block"> <!-- Replace with your logo -->
    </a>
  </div>

    <!-- Hamburger Menu (Visible on smaller screens) -->
    <div class="md:hidden">
      <button id="navbar-toggle" class="text-blue-900 focus:outline-none">
        <i class="fas fa-bars fa-2x"></i>
      </button>
    </div>

    <!-- Navbar Links for Desktop -->
    <div class="hidden md:flex space-x-6 text-blue-900 items-center">
      <a href="../jobs.php" class="font-bold">Jobs</a>
      <a href="../about.php" class="font-bold">About Us</a>

      <?php if (!$logged_in) { ?>
        <!-- Guest User -->
        <a href="../dashboard.php" class="font-bold">Dashboard</a>
        <a href="../login.php" class="font-bold">Login</a>
        <a href="../sign-up.php" class="font-bold">Sign Up</a>
      <?php } else { ?>
        <!-- Logged-in User -->
        <a href="../dashboard.php" class="font-bold">Dashboard</a>
        <a href="../notification.php" class="font-bold">Notification</a>

        <!-- User Avatar -->
        <div class="relative">
          <button id="user-menu-toggle" class="focus:outline-none">
            <img src="<?= $profile_image_path; ?>" class="w-9 h-9 rounded-full border-2 border-indigo-900" alt="User Avatar">
          </button>
          <!-- Dropdown Menu -->
          <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg">
            <a href="../profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
            <a href="../logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
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

    <a href="../jobs.php" class="text-white text-2xl mb-6">Jobs</a>
    <a href="../about.php" class="text-white text-2xl mb-6">About Us</a>

    <?php if (!$logged_in) { ?>
      <a href="../dashboard.php" class="text-white text-2xl mb-6">Dashboard</a>
      <a href="../login.php" class="text-white text-2xl mb-6">Login</a>
      <a href="../signup.php" class="text-white text-2xl mb-6">Sign Up</a>
    <?php } else { ?>
      <a href="../dashboard.php" class="text-white text-2xl mb-6">Dashboard</a>
      <a href="../notification.php" class="text-white text-2xl mb-6">Notification</a>
      <a href="../logout.php" class="text-white text-2xl mb-6">Logout</a>
    <?php } ?>
  </div>
</header>


 <!-- Sidebar and Content Wrapper -->
<div class="flex min-h-screen">
  <!-- Sidebar -->
  <aside class="bg-blue-900 w-64 fixed left-0 min-h-screen shadow-md transition-all duration-300 ease-in-out" id="sidebar">
    <div class="py-6">
      <!-- Welcome message dynamically showing the employer's name -->
      <p class="text-lg px-4 font-bold text-yellow-100" id="welcomeMessage"><?= $welcome_message ?></p>
      <ul class="mt-6">
        <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800">
          <a href="dashboard.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline">
            <i class="fas fa-tachometer-alt"></i> <span class="sidebar-icon-text">Dashboard</span>
          </a>
        </li>
        <li class="active border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800">
          <a href="profile.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline">
            <i class="fas fa-user"></i> <span class="sidebar-icon-text">Profile</span>
          </a>
        </li>
        <li class="border-t border-b border-yellow-50 px-6 py-2.5 bg-blue-800">
          <a href="post.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline">
            <i class="fas fa-briefcase"></i> <span class="sidebar-icon-text">Post a Job</span>
          </a>
        </li>
        <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800">
          <a href="myjobs.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline">
            <i class="fas fa-list"></i> <span class="sidebar-icon-text">My Jobs</span>
          </a>
        </li>
        <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800">
          <a href="messages.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline">
            <i class="fas fa-envelope"></i> <span class="sidebar-icon-text">Messages</span>
          </a>
        </li>
        <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800">
          <a href="notifications.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline">
            <i class="fas fa-bell"></i> <span class="sidebar-icon-text">Notifications</span>
          </a>
        </li>
        <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800">
          <a href="../logout.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline">
            <i class="fas fa-sign-out-alt"></i> <span class="sidebar-icon-text">Logout</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>
  
  <!-- Content Wrapper -->
  <div class="content-wrapper flex-1 flex items-center justify-center bg-white p-6">
    <div class="w-full max-w-4xl">
      <h2 class="text-2xl font-bold text-indigo-900 mb-4">Post A Job</h2>

      <form class="space-y-4" method="POST" action="be/post_job.php">
        <!-- Job Title and Location -->
        <div class="grid grid-cols-2 gap-4">
          <input type="text" name="job_title" id="job_title" class="border border-gray-300 p-2 rounded w-full" placeholder="Job Title" required>
          <input type="text" name="location" id="location" class="border border-gray-300 p-2 rounded w-full" placeholder="Location" required>
        </div>

        <!-- Job Description -->
        <div>
          <textarea class="border border-gray-300 p-2 rounded w-full" id="description" name="description" placeholder="Job Description"></textarea>
        </div>

        <!-- Salary Fields -->
        <div class="grid grid-cols-2 gap-4">
          <input type="text" name="min_salary" id="min_salary" class="border border-gray-300 p-2 rounded w-full" placeholder="Minimum Salary" required>
          <input type="text" name="max_salary" id="max_salary" class="border border-gray-300 p-2 rounded w-full" placeholder="Maximum Salary" required>
        </div>

        <!-- Job Type and Deadline -->
        <div class="grid grid-cols-2 gap-4">
          <div class="grid grid-cols-2 gap-2">
            <label><input type="checkbox" name="job_type[]" value="Task Based" class="mr-2"> Task Based</label>
            <label><input type="checkbox" name="job_type[]" value="Full-Time" class="mr-2"> Full-Time</label>
            <label><input type="checkbox" name="job_type[]" value="On-Site" class="mr-2"> On-Site</label>
            <label><input type="checkbox" name="job_type[]" value="Remote" class="mr-2"> Remote</label>
          </div>
          <div>
            <label for="deadline" class="block mb-2">Deadline:</label>
            <input type="date" name="deadline" id="deadline" class="border border-gray-300 p-2 rounded w-full" required>
          </div>
        </div>

        <!-- Post Button -->
        <button type="submit" class="bg-blue-800 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Post</button>
      </form>
    </div>
  </div>
</div>





  <!-- Footer -->
  <!-- <footer class="text-center py-4 bg-gray-100 text-gray-600">
    <strong>&copy; KonekTra</strong> All rights reserved.
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

<!-- Sidebar -->
<aside class="bg-blue-900 w-64 fixed left-0 min-h-screen shadow-md transition-all duration-300 ease-in-out" id="sidebar">
    <div class="py-6">
        <!-- Welcome message dynamically showing the employer's name -->
        <p class="text-lg px-4 font-bold text-yellow-100" id="welcomeMessage"><?= $welcome_message ?></p>
        <!-- Your sidebar items here -->
    </div>
</aside>

<!-- JavaScript -->
<script>
  // Function to adjust layout on page load
  function adjustLayoutOnLoad() {
      const sidebar = document.getElementById('sidebar');
      const content = document.querySelector('.content-wrapper');
      const iconText = document.querySelectorAll('.sidebar-icon-text');
      const welcomeMessage = document.getElementById('welcomeMessage');

      // Check sidebar width on page load and apply the correct classes to content
      if (sidebar.classList.contains('w-64')) {
          content.classList.add('pl-64'); // Make sure content is properly aligned
      } else {
          content.classList.add('pl-16'); // Adjust for smaller sidebar
          iconText.forEach((text) => text.classList.add('hidden')); // Hide icon text if sidebar is small
          welcomeMessage.style.display = 'none'; // Hide welcome message if sidebar is small
      }
  }

  // JavaScript function to toggle the sidebar
  document.getElementById('toggleSidebar').addEventListener('click', function () {
      const sidebar = document.getElementById('sidebar');
      const content = document.querySelector('.content-wrapper');
      const iconText = document.querySelectorAll('.sidebar-icon-text');
      const welcomeMessage = document.getElementById('welcomeMessage');

      // Toggle sidebar width between small (icon only) and full width
      if (sidebar.classList.contains('w-64')) {
          sidebar.classList.replace('w-64', 'w-16'); // Shrinks the sidebar
          content.classList.add('pl-16'); // Adjusts content margin when sidebar shrinks
          content.classList.remove('pl-64');

          // Hide icon text labels and welcome message
          iconText.forEach((text) => text.classList.add('hidden'));
          welcomeMessage.style.display = 'none';
      } else {
          sidebar.classList.replace('w-16', 'w-64'); // Expands the sidebar
          content.classList.add('pl-64');
          content.classList.remove('pl-16');

          // Show icon text labels and welcome message
          iconText.forEach((text) => text.classList.remove('hidden'));
          welcomeMessage.style.display = 'block';
      }
  });

  // Run the adjustLayoutOnLoad function when the page loads
  window.addEventListener('load', adjustLayoutOnLoad);
</script>
      <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>

</html>
