<?php
// Start session
session_start();

// Database connection
require_once("../db.php");

// Initialize variables
$default_avatar = 'img/default-avatar.png'; // Default avatar
$profile_image_path = $default_avatar;
$welcome_message = 'Welcome Admin!';
$logged_in = false;
$company_name = '';
$email = '';
$contact_no = '';
$description = 'No description provided.';
$address = '';

// Check if the user is logged in
if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];

    // Fetch user and employer details
    $query = "
        SELECT u.user_type, u.profile_image, e.firstname, e.lastname, e.company_name, e.email, e.contactno, e.aboutme, e.street, c.city_name, p.province_name
        FROM users u
        LEFT JOIN employers e ON u.id_user = e.id_user
        LEFT JOIN cities c ON e.id_city = c.id_city
        LEFT JOIN provinces p ON e.id_province = p.id_province
        WHERE u.id_user = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Check if the user is an employer
        if ($row['user_type'] == 'employer') {
            // Set welcome message and other details
            $logged_in = true;
            $welcome_message = 'Welcome ' . $row['firstname'] . '!';
            $profile_image_path = !empty($row['profile_image']) ? '../uploads/profile/' . $row['profile_image'] : $default_avatar;
            
            // Employer details
            $company_name = $row['company_name'];
            $email = $row['email'];
            $contact_no = $row['contactno'];
            $description = !empty($row['aboutme']) ? $row['aboutme'] : $description;
            $address = $row['street'] . ', ' . $row['city_name'] . ', ' . $row['province_name'];
        } else {
            // If user is not an employer, show a custom message or redirect as needed
            $welcome_message = 'Welcome to the platform!';
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
        <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800">
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
        <li class="border-t border-b border-yellow-50 px-6 py-2.5 bg-blue-800">
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
<div class="flex-grow bg-white shadow-lg rounded-lg px-6 content-wrapper">
    <div class="flex">
      
      <!-- Notifications List -->
      <div class="w-full p-4 rounded-lg">
        <h5 class="font-bold text-lg mb-4">Notifications</h5>

        <div class="space-y-3">
          <!-- Notification Item -->
          <div class="flex items-center bg-yellow-100 p-4 rounded-lg shadow-sm">
            <img src="../img/profile-applicant.jpg" alt="CURA Corp Logo" class="w-10 h-10 rounded-full mr-4">
            <div class="flex-grow">
              <h6 class="font-bold">Shun Maxim</h6>
              <p class="text-sm text-gray-600">CURA Corp. posted a new job! They need electricians.</p>
            </div>
            <button class="text-red-600 hover:text-red-800">
              <i class="fas fa-trash"></i>
            </button>
          </div>

          <!-- Notification Item -->
          <div class="flex items-center bg-yellow-100 p-4 rounded-lg shadow-sm">
            <img src="../img/profile-applicant.jpg" alt="CURA Corp Logo" class="w-10 h-10 rounded-full mr-4">
            <div class="flex-grow">
              <h6 class="font-bold">Shun Maxim</h6>
              <p class="text-sm text-gray-600">CURA Corp. posted a new job! They need electricians.</p>
            </div>
            <button class="text-red-600 hover:text-red-800">
              <i class="fas fa-trash"></i>
            </button>
          </div>

          <!-- Notification Item -->
          <div class="flex items-center bg-yellow-100 p-4 rounded-lg shadow-sm">
            <img src="../img/profile-applicant.jpg" alt="CURA Corp Logo" class="w-10 h-10 rounded-full mr-4">
            <div class="flex-grow">
              <h6 class="font-bold">Shun Maxim</h6>
              <p class="text-sm text-gray-600">CURA Corp. posted a new job! They need electricians.</p>
            </div>
            <button class="text-red-600 hover:text-red-800">
              <i class="fas fa-trash"></i>
            </button>
          </div>

          <!-- Notification Item -->
          <div class="flex items-center bg-yellow-100 p-4 rounded-lg shadow-sm">
            <img src="../img/profile-applicant.jpg" alt="CURA Corp Logo" class="w-10 h-10 rounded-full mr-4">
            <div class="flex-grow">
              <h6 class="font-bold">Shun Maxim</h6>
              <p class="text-sm text-gray-600">CURA Corp. posted a new job! They need electricians.</p>
            </div>
            <button class="text-red-600 hover:text-red-800">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

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


  </script>
      <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>

</html>
