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
  <title>About Us - KonekTra</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <!-- Tailwind CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- FontAwesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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


  <!-- About Section -->
  <section id="about" class="md:py-20 py-4 px-3">
    <div class="container mx-auto">
      <div class="row md:flex flex-col-reverse flex-row">
        <div class="col-md-6 mt-3">
          <img src="img/tesda.jpg" alt="TESDA" class="img-fluid rounded-lg shadow-lg">
        </div>
        <div class="col-md-6">
          <p>Welcome to <strong>KonekTra</strong>, a dedicated platform designed to bridge the gap between TESDA-certified graduates and employers seeking specialized skills. Our mission is to empower skilled workers by providing a seamless job search experience tailored specifically for those who have honed their expertise through TESDA programs.</p>
          <h4 class="font-bold text-indigo-900">What We Do</h4>
          <ul class="list-disc pl-4">
            <li><strong>Browse Job Listings:</strong> Find job opportunities based on specific TESDA certifications and skill sets.</li>
            <li><strong>Create Profiles:</strong> Showcase your qualifications, skills, and certifications to stand out to employers.</li>
            <li><strong>Apply for Jobs:</strong> Directly apply for jobs that fit your expertise.</li>
          </ul>
          <h4 class="font-bold text-indigo-900">For Employers</h4>
          <ul class="list-disc pl-4">
            <li><strong>Job Posting:</strong> Post positions tailored to skilled workers certified by TESDA.</li>
            <li><strong>Browse Certifications:</strong> Access a pool of highly qualified, technically skilled professionals.</li>
            <li><strong>Hire with Confidence:</strong> All candidates are verified TESDA graduates, ensuring their skills meet your needs.</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- Our Mission Section -->
  <section id="mission" class="py-7 bg-gray-100 px-3">
    <div class="container mx-auto">
      <h2 class="text-2xl font-bold text-center mb-4 text-indigo-900">Our Mission</h2>
      <p class="text-center">At KonekTra, our mission is to help TESDA graduates find meaningful work by connecting them with employers who value their skills. We strive to create a platform that ensures fair and transparent opportunities for everyone.</p>
    </div>
  </section>

  <!-- FAQ Section -->
  <section id="faqs" class="py-7 bg-white px-3">
    <div class="container mx-auto">
      <h2 class="text-2xl font-bold text-center mb-4 text-indigo-900">Frequently Asked Questions</h2>
      <div class="accordion" id="faqAccordion">
        <div class="card">
          <div class="card-header" id="faqOne">
            <h5 class="mb-0">
              <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                How can I create a profile on KonekTra?
              </button>
            </h5>
          </div>

          <div id="collapseOne" class="collapse show" aria-labelledby="faqOne" data-parent="#faqAccordion">
            <div class="card-body">
              To create a profile, simply click on the 'Sign Up' link in the top menu and follow the registration process.
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header" id="faqTwo">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                What certifications are required to apply for jobs?
              </button>
            </h5>
          </div>
          <div id="collapseTwo" class="collapse" aria-labelledby="faqTwo" data-parent="#faqAccordion">
            <div class="card-body">
              Only TESDA-certified graduates are eligible to apply for jobs listed on KonekTra.
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header" id="faqThree">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                How do employers post job listings?
              </button>
            </h5>
          </div>
          <div id="collapseThree" class="collapse" aria-labelledby="faqThree" data-parent="#faqAccordion">
            <div class="card-body">
              Employers can post jobs by creating an account and selecting the 'Post a Job' option from their dashboard.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Us Section -->
  <section id="contact" class="py-7 bg-gray-100 px-3">
    <div class="container mx-auto">
      <h2 class="text-2xl font-bold text-center mb-4 text-indigo-900">Contact Us</h2>
      <p class="text-center">Have any questions? Feel free to reach out to us!</p>
      <div class="row">
        <div class="col-md-6">
          <h4 class="font-bold text-indigo-900">Address</h4>
          <p>123 KonekTra Lane, Manila, Philippines</p>
        </div>
        <div class="col-md-6">
          <h4 class="font-bold text-indigo-900">Email</h4>
          <p><a href="mailto:support@konektra.com">support@konektra.com</a></p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-indigo-900 py-4">
    <div class="container mx-auto text-center text-white">
      &copy; 2024 KonekTra. All Rights Reserved.
    </div>
  </footer>

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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@1.5.1/dist/flowbite.min.js"></script>
</body>
</html>
