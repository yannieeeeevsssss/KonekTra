<?php
session_start();
if (empty($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

require_once("../db.php");

// Get applicant ID from the URL parameter
$id_applicant = $_GET['id'];

// Fetch applicant data
$sql = "
    SELECT a.*, u.email, u.profile_image, c.city_name, p.province_name 
    FROM applicants a 
    JOIN users u ON a.id_user = u.id_user
    LEFT JOIN cities c ON a.id_city = c.id_city
    LEFT JOIN provinces p ON a.id_province = p.id_province
    WHERE a.id_applicant = $id_applicant
";
$result = $conn->query($sql);
$applicant = $result->fetch_assoc();

// Fetch certifications
$sql_certifications = "
    SELECT ct.*, cr.certificate_name 
    FROM certifications ct
    JOIN certificates cr ON ct.id_certificate = cr.id_certificate
    WHERE ct.id_applicant = $id_applicant
";
$certifications = $conn->query($sql_certifications);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applicant</title>

    <!-- Tailwind CSS and Flowbite -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/flowbite@1.5.3/dist/flowbite.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
<header class="bg-yellow-100 shadow-md py-4">
    <div class="container mx-auto flex items-center justify-between px-4">
      <!-- Flex container for the toggle icon and logo -->
      <div class="flex items-center space-x-4">
        <!-- Sidebar Toggle Icon -->
        <a href="#" class="text-gray-600 hover:text-gray-900" id="toggleSidebar">
          <i class="fa fa-bars text-xl"></i> <!-- FontAwesome hamburger icon -->
        </a>

        <!-- Logo -->
        <a href="index.php" class="flex items-center space-x-2">
          <img src="../img/logo.png" alt="KonekTra" class="h-8"> <!-- Replace with your logo -->
        </a>
      </div>
    </div>
</header>

  <!-- Sidebar and Content Wrapper -->
  <div class="flex flex-col md:flex-row">
    <!-- Sidebar -->
    <aside class="bg-blue-900 w-full md:w-64 min-h-screen shadow-md" id="sidebar">
      <div class="p-6">
        <p class="text-lg font-semibold text-white">Welcome Admin!</p>
        <ul class="mt-6 space-y-4">
          <li><a href="dashboard.php" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
          <li><a href="active-jobs.php" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fa fa-briefcase"></i> <span>Active Jobs</span></a></li>
          <li><a href="applications.php" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fa fa-address-card-o"></i> <span>Applications</span></a></li>
          <li class="active"><a href="man-applicants.php" class="flex items-center space-x-2 text-yellow-50 font-bold"><i class="fa fa-users"></i> <span>Applicants</span></a></li>
          <li><a href="../logout.php" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fa fa-arrow-circle-o-right"></i> <span>Logout</span></a></li>
        </ul>
      </div>
    </aside>

        <!-- Content -->
        <main class="flex-1 p-6">
    <section>
    <h3 class="text-2xl font-semibold text-gray-800 mt-4 flex items-center">
    <a href="man-applicants.php" class="flex items-center text-blue-700 hover:text-blue-800">
    <i class="fa fa-chevron-left text-sm text-gray-500 mr-2"></i></i> <!-- Font Awesome Back Icon -->
    </a>
    <span>Applicant Details</span>
</h3>


        <div class="bg-white shadow-md rounded px-8 py-6 mt-4 grid grid-cols-2 gap-6">
            <!-- Left Column -->
            <div>
                <!-- Profile Image -->
                <div class="flex flex-col mb-4">
                    <label class="font-bold text-gray-700">Profile Image of the Applicant</label>
                    <img src="../uploads/profile/<?php echo $applicant['profile_image']; ?>" alt="Profile Image" class="w-40 h-40 object-cover rounded">
                </div>

                <!-- Age -->
                <div class="mb-4">
                    <label class="font-bold text-gray-700">Age</label>
                    <input type="text" value="<?php echo $applicant['age']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                </div>

                <!-- House/Street/Brgy -->
                <div class="mb-4">
                    <label class="font-bold text-gray-700">House No./Street/Brgy</label>
                    <input type="text" value="<?php echo $applicant['street']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="font-bold text-gray-700">Email</label>
                    <input type="email" value="<?php echo $applicant['email']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                </div>

                <!-- Contact Number -->
                <div class="mb-4">
                    <label class="font-bold text-gray-700">Contact No.</label>
                    <input type="text" value="<?php echo $applicant['contactno']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                </div>

                <!-- Highest Educational Attainment -->
                <div class="mb-4">
                    <label class="font-bold text-gray-700">Highest Educational Attainment</label>
                    <input type="text" value="<?php echo $applicant['education']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <!-- Personal Information -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Last Name</label>
                        <input type="text" value="<?php echo $applicant['lastname']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">First Name</label>
                        <input type="text" value="<?php echo $applicant['firstname']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Middle Name</label>
                        <input type="text" value="<?php echo $applicant['middlename']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Date of Birth</label>
                        <input type="text" value="<?php echo $applicant['dob']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Gender</label>
                        <input type="text" value="<?php echo $applicant['gender']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">City</label>
                        <input type="text" value="<?php echo $applicant['city_name']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Province</label>
                        <input type="text" value="<?php echo $applicant['province_name']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>
                </div>

                <!-- About the Applicant -->
                <div class="mb-4">
                    <label class="font-bold text-gray-700">About the Applicant</label>
                    <textarea class="w-full border border-gray-300 rounded p-2" disabled><?php echo $applicant['aboutme']; ?></textarea>
                </div>

                <!-- Resume Button -->
                <div class="mb-4">
                    <a href="../uploads/resume/<?php echo $applicant['resume']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded">Applicant's Resume</a>
                </div>
            </div>
        </div>

        <!-- Certificates Section -->
        <div class="mt-6 bg-white shadow-md rounded px-8 py-6">
            <h4 class="text-lg font-semibold text-gray-800">Certificates</h4>
            <?php while ($cert = $certifications->fetch_assoc()) { ?>
            <div class="grid grid-cols-2 gap-6 mt-4">
                <div>
                    <label class="font-bold text-gray-700">Certificate Title</label>
                    <input type="text" value="<?php echo $cert['certificate_name']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                </div>
                <div>
                    <label class="font-bold text-gray-700">Training Center</label>
                    <input type="text" value="<?php echo $cert['training_center']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                </div>
                <div>
                    <label class="font-bold text-gray-700">Issuance Date</label>
                    <input type="text" value="<?php echo $cert['issuance_date']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                </div>
                <div>
                    <label class="font-bold text-gray-700">Expiration Date</label>
                    <input type="text" value="<?php echo $cert['expiration_date']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                </div>
                <div>
                    <label class="font-bold text-gray-700">Certificate No.</label>
                    <input type="text" value="<?php echo $cert['certificate_no']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                </div>

                <!-- View Certificate Button -->
                <div>
                    <a href="../<?php echo $cert['certificate_image']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded">View Certificate Image</a>
                </div>
            </div>
            <?php } ?>
        </div>

        <!-- Approve/Delete Section -->
        <div class="mt-6 flex justify-end space-x-4">
            <button class="bg-green-500 text-white px-6 py-2 rounded">Approve</button>
            <button class="bg-red-500 text-white px-6 py-2 rounded">Delete</button>
        </div>
    </section>
</main>

    </div>

    <!-- Footer -->
    <!-- Footer can be included if required -->
</body>
</html>
