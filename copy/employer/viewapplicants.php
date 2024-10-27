<?php
// Start session
session_start();

// Include your database connection
require_once("../db.php");

// Initialize variables
$default_avatar = 'img/default-avatar.png'; // Default avatar
$logged_in = false;
$profile_image_path = ''; // To store the full profile image path
$welcome_message = 'Welcome Admin!'; // Default message

// Check if the user is logged in and is an employer
if (isset($_SESSION['id_user']) && isset($_SESSION['id_employer'])) {
    $user_id = $_SESSION['id_user'];
    $id_employer = $_SESSION['id_employer']; // Get employer ID from session

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
    // If not logged in or not an employer, redirect to index.php
    header("Location: ../index.php");
    exit();
}

// Assuming you pass the job ID as a query parameter
$job_id = $_GET['id_job'];

// Query to get applicants who applied for the job
$query = "
    SELECT a.*, u.profile_image, u.email, u.user_type, c.city_name, p.province_name, j.job_title, app.resume, app.cover_letter, app.status 
    FROM applicants a 
    JOIN users u ON a.id_user = u.id_user 
    JOIN applications app ON a.id_applicant = app.id_applicant 
    JOIN cities c ON a.id_city = c.id_city 
    JOIN provinces p ON a.id_province = p.id_province 
    JOIN jobs j ON app.id_jobs = j.id_jobs
    WHERE app.id_jobs = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

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
        <li class="border-t border-b border-yellow-50 px-6 py-2.5 bg-blue-800">
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

  <div class="content-wrapper flex-grow p-6 mx-10">
  <a href="javascript:history.back()" class="flex items-center text-blue-900 hover:text-blue-700 mb-2 hover:no-underline">
    <i class="fa fa-arrow-left text-xl mr-2"></i> Back
  </a>
  <?php if ($result->num_rows > 0): ?>
    <!-- Display applicant cards if there are applicants -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php while ($applicant = $result->fetch_assoc()) : ?>
        <div class="bg-yellow-50 p-6 rounded-lg shadow-lg flex flex-col">
          <!-- Profile Image and Name Section -->
          <a href="profile.php?id=<?php echo $applicant['id_applicant']; ?>" class="flex items-center space-x-4 text-left">
            <img src="<?php echo !empty($applicant['profile_image']) ? '../uploads/profile/' . $applicant['profile_image'] : $default_avatar; ?>" 
                 alt="Avatar" 
                 class="w-20 h-20 rounded-full shadow-md">
            <div>
              <h2 class="font-bold text-xl text-blue-900"><?php echo $applicant['firstname'] . ' ' . $applicant['lastname']; ?></h2>
              <p class="text-sm text-gray-500 flex items-center">
                <i class="fas fa-map-marker-alt mr-1"></i>
                <?php echo $applicant['city_name'] . ', ' . $applicant['province_name']; ?>
              </p>
            </div>
          </a>
          <!-- Resume and Cover Letter -->
          <div class="text-left mt-4">
            <a href="../uploads/resume/<?php echo $applicant['resume']; ?>" 
               class="text-blue-700 font-semibold hover:underline"
               download><i class="fas fa-file-alt mr-1"></i>
              Resume/CV
            </a>
          </div>
          <div class="text-left mt-2">
            <a href="#" 
               class="text-blue-700 font-semibold hover:underline" 
               data-toggle="modal" 
               data-target="#coverLetterModal-<?php echo $applicant['id_applicant']; ?>"><i class="fas fa-envelope-open-text mr-1"></i>
              View Cover Letter
            </a>
          </div>
          <!-- Action Buttons -->
          <div class="flex justify-center items-center space-x-4 mt-4">
            <?php if ($applicant['status'] === 'Hired'): ?>
              <button type="button" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg cursor-not-allowed" disabled>
                Applicant Hired
              </button>
            <?php elseif ($applicant['status'] === 'Rejected'): ?>
              <button type="button" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg cursor-not-allowed" disabled>
                Applicant Rejected
              </button>
            <?php else: ?>
              <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg" 
                      data-toggle="modal" data-target="#hireModal-<?php echo $applicant['id_applicant']; ?>">
                Hire Now
              </button>
              <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg" 
                      data-toggle="modal" data-target="#rejectModal-<?php echo $applicant['id_applicant']; ?>">
                Reject
              </button>
            <?php endif; ?>
          </div>
        </div>


      <!-- Hire Confirmation Modal -->
<div class="modal fade" id="hireModal-<?php echo $applicant['id_applicant']; ?>" tabindex="-1" role="dialog" aria-labelledby="hireModalLabel-<?php echo $applicant['id_applicant']; ?>" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="hireModalLabel-<?php echo $applicant['id_applicant']; ?>">Confirm Hire</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to hire <?php echo $applicant['firstname'] . ' ' . $applicant['lastname']; ?>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a href="be/hire.php?id=<?php echo $applicant['id_applicant']; ?>&job_id=<?php echo $job_id; ?>" class="btn btn-primary">Confirm Hire</a>
      </div>
    </div>
  </div>
</div>

<!-- Reject Confirmation Modal -->
<div class="modal fade" id="rejectModal-<?php echo $applicant['id_applicant']; ?>" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel-<?php echo $applicant['id_applicant']; ?>" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rejectModalLabel-<?php echo $applicant['id_applicant']; ?>">Confirm Rejection</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to reject <?php echo $applicant['firstname'] . ' ' . $applicant['lastname']; ?>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a href="be/reject.php?id=<?php echo $applicant['id_applicant']; ?>&job_id=<?php echo $job_id; ?>" class="btn btn-danger">Confirm Rejection</a>
      </div>
    </div>
  </div>
</div>


      <!-- Cover Letter Modal -->
      <div class="modal fade" id="coverLetterModal-<?php echo $applicant['id_applicant']; ?>" tabindex="-1" role="dialog" aria-labelledby="coverLetterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="coverLetterModalLabel">Cover Letter</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p><?php echo nl2br(htmlspecialchars($applicant['cover_letter'])); ?></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <?php else: ?>
    <!-- Display message if there are no applicants -->
    <div class="flex justify-center items-center text-gray-600 text-lg mt-6">
      <p>There are currently no applications for this job.</p>
    </div>
  <?php endif; ?>
</div>


</div>




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
