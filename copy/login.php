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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KonekTra Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Flowbite CDN -->
    <script src="https://unpkg.com/flowbite@1.4.7/dist/flowbite.js"></script>
    <!-- Font Awesome -->
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

<section class="min-h-screen flex items-center justify-center">
    <div class="bg-yellow-50 border border-indigo-900 rounded-lg shadow-md w-full max-w-md py-4 px-12">
        <!-- Right Side (Form) -->
        <div>
            <h3 class="text-2xl font-bold text-center text-blue-900 mb-6">Welcome back</h3>
            <form action="loginusers.php" method="POST" class="space-y-4">
                <div>
                    <label for="email" class="block text-indigo-900 font-bold">Email</label>
                    <input type="email" id="email" name="email" class="w-full border-2 border-indigo-900 rounded-lg p-2" required>
                </div>

                <div>
                    <label for="password" class="block text-indigo-900 font-bold">Password</label>
                    <input type="password" id="password" name="password" class="w-full border-2 border-indigo-900 rounded-lg p-2" required>
                </div>

                <div class="g-recaptcha" data-sitekey="6Ld9SSEqAAAAACaAUOjU-XWq6LPfmYyc-WTahfNt"></div>
                <div id="recaptcha-warning" class="text-red-500 mt-2 hidden">Please check the reCAPTCHA first.</div>


                <button type="submit" name="login" class="block w-full bg-indigo-900 text-yellow-50 py-2 font-bold mb-2 rounded-lg hover:bg-blue-700 transition duration-200">Login</button>
            </form>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-indigo-900 py-4">
    <div class="container mx-auto text-center text-white">
      &copy; 2024 KonekTra. All Rights Reserved.
    </div>
</footer>

<script>
    // Toggle mobile menu
    const navbarToggle = document.getElementById('navbar-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMenuButton = document.getElementById('close-menu');
    const userMenuToggle = document.getElementById('user-menu-toggle');
    const userMenu = document.getElementById('user-menu');

    navbarToggle.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
    closeMenuButton.addEventListener('click', () => mobileMenu.classList.add('hidden'));
    userMenuToggle?.addEventListener('click', () => userMenu.classList.toggle('hidden'));

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            mobileMenu.classList.add('hidden');
        }
    });
</script>

<!-- Bootstrap JS and Flowbite JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@1.5.1/dist/flowbite.min.js"></script>

</body>
</html>
