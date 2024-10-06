<?php
session_start();
if (empty($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

require_once("../db.php");

// Get employer ID from the URL parameter
$id_employer = $_GET['id'];

// Fetch employer data
$sql = "
    SELECT e.*, u.email, u.profile_image, c.city_name, p.province_name 
    FROM employers e 
    JOIN users u ON e.id_user = u.id_user
    LEFT JOIN cities c ON e.id_city = c.id_city
    LEFT JOIN provinces p ON e.id_province = p.id_province
    WHERE e.id_employer = $id_employer
";
$result = $conn->query($sql);
$employer = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employer</title>

    <!-- Tailwind CSS and Flowbite -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/flowbite@1.5.3/dist/flowbite.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
<header class="bg-yellow-100 shadow-md py-4">
    <div class="container mx-auto flex items-center justify-between px-4">
        <div class="flex items-center space-x-4">
            <!-- Sidebar Toggle Icon -->
            <a href="#" class="text-gray-600 hover:text-gray-900" id="toggleSidebar">
                <i class="fa fa-bars text-xl"></i>
            </a>
            <!-- Logo -->
            <a href="index.php" class="flex items-center space-x-2">
                <img src="../img/logo.png" alt="KonekTra" class="h-8">
            </a>
        </div>
    </div>
</header>

<div class="flex flex-col md:flex-row">
    <!-- Sidebar -->
    <aside class="bg-blue-900 w-full md:w-64 min-h-screen shadow-md" id="sidebar">
        <div class="p-6">
            <p class="text-lg font-semibold text-white">Welcome Admin!</p>
            <ul class="mt-6 space-y-4">
                <li><a href="dashboard.php" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
                <li><a href="manage-applicants.php" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fa fa-users"></i> <span>Manage Applicants</span></a></li>
                <li class="active"><a href="manage-employers.php" class="flex items-center space-x-2 text-yellow-50 font-bold"><i class="fa fa-building"></i> <span>Manage Employers</span></a></li>
                <li><a href="../logout.php" class="flex items-center space-x-2 text-white hover:text-gray-200"><i class="fa fa-arrow-circle-o-right"></i> <span>Logout</span></a></li>
            </ul>
        </div>
    </aside>

    <!-- Content -->
    <main class="flex-1 p-6">
        <section>
            <h3 class="text-2xl font-semibold text-gray-800 mt-4 flex items-center">
                <a href="man-employers.php" class="flex items-center text-blue-700 hover:text-blue-800">
                    <i class="fa fa-chevron-left text-sm text-gray-500 mr-2"></i>
                </a>
                <span>Employer Details</span>
            </h3>

            <div class="bg-white shadow-md rounded px-8 py-6 mt-4 grid grid-cols-2 gap-6">
                <!-- Left Column -->
                <div>
                    <!-- Profile Image -->
                    <div class="flex flex-col mb-4">
                        <label class="font-bold text-gray-700">Profile Image of the Employer</label>
                        <img src="../uploads/profile/<?php echo $employer['profile_image']; ?>" alt="Profile Image" class="w-40 h-40 object-cover rounded">
                    </div>

                    <!-- Age -->
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Age</label>
                        <input type="text" value="<?php echo $employer['age']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>

                    <!-- House/Street/Brgy -->
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">House No./Street/Brgy</label>
                        <input type="text" value="<?php echo $employer['street']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Email</label>
                        <input type="email" value="<?php echo $employer['email']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>

                    <!-- Contact Number -->
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Contact No.</label>
                        <input type="text" value="<?php echo $employer['contactno']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Description</label>
                        <textarea class="w-full border border-gray-300 rounded p-2" disabled><?php echo $employer['aboutme']; ?></textarea>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <!-- Personal Information -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="font-bold text-gray-700">Last Name</label>
                            <input type="text" value="<?php echo $employer['lastname']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="font-bold text-gray-700">First Name</label>
                            <input type="text" value="<?php echo $employer['firstname']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="font-bold text-gray-700">Middle Name</label>
                            <input type="text" value="<?php echo $employer['middlename']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="font-bold text-gray-700">Date of Birth</label>
                            <input type="text" value="<?php echo $employer['dob']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="font-bold text-gray-700">Gender</label>
                            <input type="text" value="<?php echo $employer['gender']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="font-bold text-gray-700">City</label>
                            <input type="text" value="<?php echo $employer['city_name']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="font-bold text-gray-700">Province</label>
                            <input type="text" value="<?php echo $employer['province_name']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Company Name</label>
                        <input type="text" value="<?php echo $employer['company_name']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>

                    <div class="mb-4">
                        <label class="font-bold text-gray-700">Registration No.</label>
                        <input type="text" value="<?php echo $employer['registration_no']; ?>" class="w-full border border-gray-300 rounded p-2" disabled>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <button class="bg-green-500 text-white px-6 py-2 rounded">Approve</button>
                        <button class="bg-red-500 text-white px-6 py-2 rounded">Delete</button>
                    </div>
                    
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    // Sidebar Toggle for small screens
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    toggleSidebar.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
    });
</script>

</body>
</html>
