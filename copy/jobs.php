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
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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

<!-- Main Content -->
<div class="container mx-auto px-5 py-4">
  
  <!-- Search and Filters Section -->
  <div class="mb-8">
    <!-- Search Bar -->
    <div class="flex justify-center mb-2">
      <div class="relative w-full max-w-4xl">
        <input type="text" class="form-control w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-full focus:outline-none focus:border-indigo-500" placeholder="Search jobs...">
        <span class="absolute left-3 top-1.5 text-gray-500">
          <i class="fa fa-search"></i>
        </span>
      </div>
    </div>

    <!-- Filters Section: Location, Job Types, and Search Button -->
    <div class="flex justify-center items-center space-x-4">


      <!-- Job Type Checkboxes -->
      <div class="flex space-x-4">
        <label class="inline-flex items-center"><input type="checkbox" name="full-time" class="mr-2"> Full-Time</label>
        <label class="inline-flex items-center"><input type="checkbox" name="task-based" class="mr-2"> Task-Based</label>
        <label class="inline-flex items-center"><input type="checkbox" name="remote" class="mr-2"> Remote</label>
        <label class="inline-flex items-center"><input type="checkbox" name="on-site" class="mr-2"> On-Site</label>
      </div>
      <!-- Location Dropdown -->
      <div class="w-full max-w-sm">
        <select class="form-control w-full border-2 border-gray-300 rounded-lg">
          <option value="all">Search by Location</option>
          <option value="leyte">Leyte</option>
          <option value="cebu">Cebu</option>
        </select>
      </div>
      <!-- Search Button -->
      <div>
        <button class="bg-indigo-900 text-white py-2 px-6 rounded-md hover:bg-blue-700">Search</button>
      </div>
    </div>
  </div>

  <!-- Job Cards Section -->
  <section class="grid grid-cols-1 md:grid-cols-2 px-5 mx-5 gap-8">

    <!-- Job Cards-->
  <div class="bg-yellow-50 rounded-lg shadow-lg p-6 relative">
  <!-- Job Info and Logo Section -->
  <div class="flex">
    <!-- Company Logo -->
    <img src="img/company.png" alt="Company Logo" class="w-20 h-20 rounded mr-4">
    
    <!-- Job Title, Company Name, and Location (Beside the image) -->
    <div>
    <h4 class="text-2xl font-bold">Front Desk Officer</h4>
    <p class="text-gray-600">
      <i class="fas fa-building mr-2"></i> ABC Hotel Inc.
    </p>
    <p class="text-gray-500">
      <i class="fas fa-map-marker-alt mr-2"></i> Abuyog, Leyte
    </p>
    </div>
  </div>

  <!-- Salary, Job Type, and Application Deadline (Under the image) -->
  <div class="mt-4">
  <p class="text-gray-500"><i class="fas fa-money-bill-wave mr-1"></i> ₱18,000 - ₱20,000</p>
    <p class="text-gray-500"><i class="fas fa-clock mr-1"></i> Full-Time • On-site</p>
    <p class="text-gray-500"><i class="fas fa-calendar-alt mr-1"></i> Apply before Sept 19</p>
  </div>

  <!-- Date Posted (top right) -->
  <p class="absolute top-2 right-2 text-gray-500 text-sm">Posted 3 days ago</p>

  <!-- Buttons Section (Centered below) -->
  <div class="mt-6 flex justify-between gap-4">
  <a href="viewjob.php" class="bg-indigo-900 text-white py-2 flex-1 text-center rounded hover:bg-blue-800 hover:no-underline">View Job</a>
  <a href="#" class="bg-indigo-900 text-white py-2 flex-1 text-center rounded hover:bg-blue-800 hover:no-underline" data-toggle="modal" data-target="#applyModal">Apply</a>
</div>
</div>



     <!-- Job Cards-->
  <div class="bg-yellow-50 rounded-lg shadow-lg p-6 relative">
  <!-- Job Info and Logo Section -->
  <div class="flex">
    <!-- Company Logo -->
    <img src="img/company.png" alt="Company Logo" class="w-20 h-20 rounded mr-4">
    
    <!-- Job Title, Company Name, and Location (Beside the image) -->
    <div>
    <h4 class="text-2xl font-bold">Front Desk Officer</h4>
    <p class="text-gray-600">
      <i class="fas fa-building mr-2"></i> ABC Hotel Inc.
    </p>
    <p class="text-gray-500">
      <i class="fas fa-map-marker-alt mr-2"></i> Abuyog, Leyte
    </p>
    </div>
  </div>

  <!-- Salary, Job Type, and Application Deadline (Under the image) -->
  <div class="mt-4">
  <p class="text-gray-500"><i class="fas fa-money-bill-wave mr-1"></i> ₱18,000 - ₱20,000</p>
    <p class="text-gray-500"><i class="fas fa-clock mr-1"></i> Full-Time • On-site</p>
    <p class="text-gray-500"><i class="fas fa-calendar-alt mr-1"></i> Apply before Sept 19</p>
  </div>

  <!-- Date Posted (top right) -->
  <p class="absolute top-2 right-2 text-gray-500 text-sm">Posted 3 days ago</p>

  <!-- Buttons Section (Centered below) -->
  <div class="mt-6 flex justify-between gap-4">
  <a href="viewjob.php" class="bg-indigo-900 text-white py-2 flex-1 text-center rounded hover:bg-blue-800 hover:no-underline">View Job</a>
  <a href="#" class="bg-indigo-900 text-white py-2 flex-1 text-center rounded hover:bg-blue-800 hover:no-underline" data-toggle="modal" data-target="#applyModal">Apply</a>
</div>
</div>

    <!-- Repeat for more Job Cards as necessary -->

  </section>
</div>

<!-- Apply Job Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" role="dialog" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applyModalLabel">Apply for Job</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <!-- Resume Upload -->
                    <div class="form-group">
                        <label for="resume">Upload Resume</label>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@1.5.1/dist/flowbite.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
</body>
</html>
