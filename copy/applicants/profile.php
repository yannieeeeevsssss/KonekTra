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
$lastname = '';
$email = '';
$contact_no = '';
$description = 'No description provided.';
$address = '';
$preferred_job = '';
$education = '';
$resume_link = '';
$gender = ''; // Initialize gender variable
$age = ''; // Initialize age variable

// Check if the user is logged in
if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];

    // Fetch user and applicant details
    $query = "
        SELECT u.user_type, u.profile_image, u.email, a.firstname, a.lastname, a.contactno, a.aboutme, a.street, a.preferred_job, a.education, a.resume, 
               c.city_name, p.province_name, a.gender, a.age
        FROM users u
        LEFT JOIN applicants a ON u.id_user = a.id_user
        LEFT JOIN cities c ON a.id_city = c.id_city
        LEFT JOIN provinces p ON a.id_province = p.id_province
        WHERE u.id_user = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Check if the user is an applicant
        if ($row['user_type'] == 'applicant') {
            // Set welcome message and other details
            $logged_in = true;
            $welcome_message = 'Welcome ' . $row['firstname'] . '!';
            $profile_image_path = !empty($row['profile_image']) ? '../uploads/profile/' . $row['profile_image'] : $default_avatar;
            
            // Applicant details
            $firstname = $row['firstname'];
            $lastname = $row['lastname'];
            $email = $row['email'];
            $contact_no = $row['contactno'];
            $description = !empty($row['aboutme']) ? $row['aboutme'] : $description;
            $address = $row['street'] . ', ' . $row['city_name'] . ', ' . $row['province_name'];
            $education = !empty($row['education']) ? $row['education'] : 'Not provided';
            $resume_link = !empty($row['resume']) ? '../uploads/resumes/' . $row['resume'] : 'No resume uploaded.';
            $gender = $row['gender']; // Set gender
            $age = $row['age']; // Set age

            // Decode preferred job JSON and extract values
            if (!empty($row['preferred_job'])) {
                $preferred_job_array = json_decode($row['preferred_job'], true);
                $preferred_job_values = array_column($preferred_job_array, 'value');
                $preferred_job = implode(', ', $preferred_job_values);
            } else {
                $preferred_job = 'Not specified';
            }
        } else {
            $welcome_message = 'Welcome to the platform!';
        }
    }

    $stmt->close();

    // Fetch applicant certifications
    $certifications_query = "
        SELECT cert.certificate_name, c.training_center, c.certificate_no, c.issuance_date, c.expiration_date, c.sector, c.certificate_image
        FROM certifications c
        LEFT JOIN certificates cert ON c.id_certificate = cert.id_certificate
        WHERE c.id_applicant = (
            SELECT a.id_applicant FROM applicants a WHERE a.id_user = ?
        )";

    $cert_stmt = $conn->prepare($certifications_query);
    $cert_stmt->bind_param("i", $user_id);
    $cert_stmt->execute();
    $certifications_result = $cert_stmt->get_result();
    $certifications = [];
    
    if ($certifications_result && $certifications_result->num_rows > 0) {
        while ($cert_row = $certifications_result->fetch_assoc()) {
            // Format the dates
            $issuance_date = new DateTime($cert_row['issuance_date']);
            $expiration_date = new DateTime($cert_row['expiration_date']);

            $cert_row['issuance_date'] = $issuance_date->format('F d, Y');
            $cert_row['expiration_date'] = $expiration_date->format('F d, Y');

            $certifications[] = $cert_row;
        }
    }

    $cert_stmt->close();
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
        <li class="active border-t border-b border-yellow-50 px-6 py-2.5 bg-blue-800">
          <a href="profile.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline">
            <i class="fas fa-user"></i> <span class="sidebar-icon-text">Profile</span>
          </a>
        </li>
        <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800">
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


<!-- Main Content and Right Sidebar -->
<div class="content-wrapper flex-1 flex justify-center bg-white p-6">
    <!-- Main Content -->
    <div class="md:w-2/3 px-6">
      <div class="bg-yellow-50 p-6 rounded-lg shadow-md mb-6">
        <!-- Profile Header -->
        <div class="flex items-center space-x-4">
          <img src="<?php echo !empty($profile_image_path) ? $profile_image_path : 'img/default-avatar.png'; ?>" alt="Profile Image" class="w-16 h-16 rounded-full">
          <div class="profile-info">
            <h2 class="text-2xl font-bold font-sans text-indigo-900"><?php echo $firstname . ' ' . $lastname; ?></h2>
            <p class="text-blue-800">
              <i class="fas fa-toolbox"></i> <?php echo $preferred_job; ?>
            </p>
            <p class="text-blue-800">
              <i class="fas fa-map-marker-alt"></i> <?php echo !empty($address) ? $address : 'Location not specified'; ?>
            </p>
          </div>
          <button class="ml-auto text-gray-700 hover:text-blue-600">
            <i class="fas fa-edit"></i>
          </button>
        </div>
      </div>

      <!-- About Section -->
      <div class="bg-yellow-50 p-6 rounded-lg shadow-md mb-6">
        <h5 class="text-lg font-bold text-blue-800">Get To Know <?php echo $firstname; ?></h5>
        <p class="text-gray-600 mt-2"><?php echo !empty($description) ? $description : 'No description provided.'; ?></p>
      </div>

      <!-- Comments Section -->
      <div class="bg-yellow-50 p-6 rounded-lg shadow-md mb-6">
        <h5 class="text-lg font-bold text-blue-800">Comments and Feedback</h5>
        <!-- Sample Comment -->
        <div class="flex items-center space-x-4 mb-4">
          <img src="../img/company.jpg" alt="Reviewer" class="w-10 h-10 rounded-full">
          <div>
            <p class="font-semibold text-gray-800">Reviewer Name</p>
            <p class="text-yellow-500"><i class="fas fa-star"></i> Very magaling. Recommended.</p>
          </div>
        </div>
        <!-- Comment Form -->
        <form class="flex space-x-2">
          <input type="text" placeholder="Add a comment" class="flex-1 p-2 border border-gray-300 rounded-lg focus:outline-none">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-paper-plane"></i>
          </button>
        </form>
      </div>

      <!-- Uploaded Images Section -->
      <div class="bg-gray-50 p-6 rounded-lg shadow-md">
        <h5 class="text-lg font-bold text-blue-800">Uploaded Images</h5>
        <div class="grid grid-cols-3 gap-4 mt-4">
          <!-- Uploaded images (can be dynamic later) -->
          <img src="../img/tesdawork.jpg" class="w-full h-32 object-cover rounded-lg">
          <img src="../img/shun.jpg" class="w-full h-32 object-cover rounded-lg">
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
    <aside class="md:w-1/3 px-4">
      <!-- Main Container for Certifications and Details -->
      <div class="bg-yellow-50 p-4 rounded-lg shadow-md mb-4 border border-gray-200">
        <!-- Certifications (inside a bordered box) -->
        <div class="border-2 border-indigo-500 p-4 rounded-md mb-4">
          <?php foreach($certifications as $cert): ?>
            <p class="text-lg font-semibold text-indigo-600 flex items-center mb-2">
              <i class="fas fa-file-alt mr-2"></i> <?php echo $cert['certificate_name']; ?>
            </p>
          <?php endforeach; ?>
        </div>

        <!-- Small Square Details in Two Columns -->
        <div class="grid grid-cols-2 gap-2">
          <!-- Gender -->
          <div class="border-2 border-indigo-500 p-2 rounded-lg text-center">
            <i class="fas fa-<?php echo ($gender == 'Male') ? 'mars' : 'venus'; ?> text-indigo-600 text-xl mb-1"></i>
            <p class="text-indigo-600 text-xs font-bold"><?php echo $gender; ?></p>
          </div>

          <!-- Age -->
          <div class="border-2 border-indigo-500 p-2 rounded-lg text-center">
            <i class="fas fa-calendar-alt text-indigo-600 text-xl mb-1"></i>
            <p class="text-indigo-600 text-xs font-bold"><?php echo !empty($age) ? $age : 'Not specified'; ?></p>
          </div>

          <!-- Education -->
          <div class="border-2 border-indigo-500 p-2 rounded-lg text-center">
            <i class="fas fa-graduation-cap text-indigo-600 text-xl mb-1"></i>
            <p class="text-indigo-600 text-xs font-bold"><?php echo !empty($education) ? $education : 'Not provided'; ?></p>
          </div>

        <!-- Email -->
<div class="border-2 border-indigo-500 p-2 rounded-lg text-center">
  <i class="fas fa-envelope text-indigo-600 text-xl mb-1"></i>
  <p class="text-indigo-600 text-xs sm:text-[10px] md:text-[11px] font-bold break-words leading-tight" style="word-break: break-all;">
    <?php echo !empty($email) ? $email : 'No email provided'; ?>
  </p>
</div>

        </div>
      </div>
    </aside>
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
