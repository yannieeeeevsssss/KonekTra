<?php 
// Start session
session_start();

// Database connection
require_once("../db.php");

// Initialize variables
$default_avatar = 'img/default-avatar.png'; // Default avatar
$profile_image_path = $default_avatar;
$welcome_message = 'Welcome Applicant!';
$logged_in = false;
$firstname = '';

// Check if the user is logged in
if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];

    // Query to fetch user details for the welcome message
    $user_query = "
        SELECT u.email, a.firstname, u.profile_image
        FROM users u
        LEFT JOIN applicants a ON u.id_user = a.id_user
        WHERE u.id_user = ?";
    
    $stmt_user = $conn->prepare($user_query);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();
    
    if ($user_result && $user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
        $logged_in = true; // Set user as logged in
        $firstname = $user_data['firstname'];
        $welcome_message = "Welcome, " . htmlspecialchars($firstname) . "!";
        // Set profile image if available, else use default avatar
        $profile_image_path = !empty($user_data['profile_image']) ? '../uploads/profile/' . $user_data['profile_image'] : $default_avatar;
    }

    $stmt_user->close();

    // Query to fetch the user's job applications along with employer's profile image
$applications_query = "
SELECT j.job_title, j.location, a.status, a.applied_at, a.resume, a.cover_letter, j.id_jobs,
       u.profile_image AS employer_profile_image
FROM applications a
LEFT JOIN jobs j ON a.id_jobs = j.id_jobs
LEFT JOIN employers e ON j.id_employer = e.id_employer
LEFT JOIN users u ON e.id_user = u.id_user
WHERE a.id_applicant = (
    SELECT id_applicant FROM applicants WHERE id_user = ?
)";
    
    $stmt = $conn->prepare($applications_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $applications = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Set employer logo if available, else default avatar
            $row['employer_logo_path'] = !empty($row['employer_logo']) ? '../uploads/company/' . $row['employer_logo'] : $default_avatar;
            $applications[] = $row;
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
        <li class="active border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800">
          <a href="profile.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline">
            <i class="fas fa-user"></i> <span class="sidebar-icon-text">Profile</span>
          </a>
        </li>
        <li class="border-t border-b border-yellow-50 px-6 py-2.5 bg-blue-800">
          <a href="myapp.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline">
            <i class="fas fa-briefcase"></i> <span class="sidebar-icon-text">My Applications</span>
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


  <div class="content-wrapper container mx-6 py-8">
  <h1 class="text-2xl font-bold text-gray-800 mb-6">My Applications</h1>

  <div class="space-y-6">
    <?php if (count($applications) > 0): ?>
      <?php foreach ($applications as $app): ?>
        <div class="bg-gray-100 rounded-lg shadow-md p-6 flex items-center justify-between">
          <!-- Job Info Section -->
          <div class="flex items-center space-x-4">
            <img src="<?= !empty($app['employer_profile_image']) ? '../uploads/profile/' . $app['employer_profile_image'] : $default_avatar; ?>" 
            alt="Company Logo" 
            class="h-20 w-20 object-cover rounded">
          <div>
              <h3 class="text-xl font-bold text-gray-800"><?= $app['job_title'] ?></h3>
              <p class="text-sm text-gray-500 flex items-center">
              <i class="fas fa-calendar-alt text-gray-600 mr-1"></i>
                <?= date('Y-m-d H:i:s', strtotime($app['applied_at'])) ?>
              </p>
              <span class="text-md flex items-center space-x-1 <?= ($app['status'] == 'Pending') ? 'text-yellow-600 font-bold' : (($app['status'] == 'Rejected') ? 'text-red-600 font-bold' : 'text-green-600 font-bold') ?>">
            <?php if ($app['status'] == 'Pending'): ?>
                <i class="fas fa-hourglass-half"></i> <!-- Pending Icon -->
            <?php elseif ($app['status'] == 'Rejected'): ?>
                <i class="fas fa-times-circle"></i> <!-- Rejected Icon -->
            <?php elseif ($app['status'] == 'Hired'): ?>
                <i class="fas fa-check-circle"></i> <!-- Approved Icon -->
            <?php endif; ?>
            <span><?= ucfirst($app['status']) ?></span>
        </span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex space-x-2">
            <?php if ($app['status'] === 'Hired'): ?>
              <!-- Rate Button -->
              <a href="rate.php?id=<?= $app['id_jobs'] ?>" class="bg-yellow-300 text-indigo-900 font-bold px-4 py-2 rounded-lg hover:no-underline">
                Rate
              </a>
            <?php elseif ($app['status'] === 'Pending'): ?>
              <!-- Withdraw Button for Pending Applications -->
              <button data-toggle="modal" data-target="#withdrawModal<?= $app['id_jobs'] ?>" class="bg-red-600 text-white font-bold px-4 py-2 rounded-lg hover:bg-red-700">
                Withdraw
              </button>
            <?php endif; ?>

            <!-- View Button (always visible) -->
            <a href="../viewjob.php?id=<?= $app['id_jobs'] ?>" class="bg-blue-600 text-white font-bold px-4 py-2 rounded-lg hover:no-underline">
              View
            </a>
          </div>
        </div>

        <!-- Withdraw Confirmation Modal (Bootstrap) -->
        <div class="modal fade" id="withdrawModal<?= $app['id_jobs'] ?>" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel<?= $app['id_jobs'] ?>" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="withdrawModalLabel<?= $app['id_jobs'] ?>">Confirm Withdrawal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                Are you sure you want to withdraw your application for the job: <strong><?= $app['job_title'] ?></strong>?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="withdraw.php?id=<?= $app['id_jobs'] ?>" class="btn btn-danger">Withdraw</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center text-gray-500">You have not applied for any jobs yet.</p>
    <?php endif; ?>
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
