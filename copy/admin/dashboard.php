<?php
session_start();
if(empty($_SESSION['id_user'])) {
  header("Location: index.php");
  exit();
}

require_once("../db.php");

if (isset($_SESSION['message'])) {
  echo "<div class='alert alert-success' role='alert'>";
  echo $_SESSION['message'];
  echo "</div>";
  unset($_SESSION['message']); // Clear the message after displaying
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Manage Employers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

  <!-- Tailwind CSS and Flowbite -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://unpkg.com/flowbite@1.5.3/dist/flowbite.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
  <!-- Main Header -->
  <header class="bg-yellow-100 shadow-md py-4">
    <div class="container mx-auto flex items-center justify-between px-4">
      <!-- Flex container for the toggle icon and logo -->
      <div class="flex items-center space-x-4">
        <!-- Sidebar Toggle Icon -->
        <a href="#" class="text-gray-600 hover:text-gray-900" id="toggleSidebar">
          <i class="fa fa-bars text-xl"></i>
        </a>

        <!-- Logo -->
        <a href="index.php" class="flex items-center space-x-2">
          <img src="../img/logo.png" alt="KonekTra" class="h-10">
        </a>
      </div>
    </div>
</header>

  <!-- Sidebar and Content Wrapper -->
  <div class="flex flex-col md:flex-row">
    <!-- Sidebar -->
    <aside class="bg-blue-900 w-full md:w-64 min-h-screen shadow-md" id="sidebar">
      <div class="py-6">
        <p class="text-lg px-4 font-bold text-yellow-100">Welcome Admin!</p>
        <ul class="mt-6">
          <li class="border-t border-b border-yellow-50 px-6 py-2.5 bg-blue-800"><a href="dashboard.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

          <li class="active border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800"><a href="man-applicants.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-users"></i> <span>Manage Applicants</span></a></li>

          <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800"><a href="man-employers.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-building"></i> <span>Manage Employers</span></a></li>

          <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800"><a href="man-jobs.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-briefcase"></i> <span>Manage Jobs</span></a></li>

          <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800"><a href="reports.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-file-text"></i> <span>Reports</span></a></li>
          
          <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800"><a href="../logout.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-arrow-circle-o-right"></i> <span>Logout</span></a></li>
        </ul>
      </div>
    </aside>
    
    <!-- Content Wrapper -->
    <main class="flex-1 p-6">
      <section class="mb-6">
        <h3 class="text-2xl font-bold text-indigo-900">Overview</h3>
        <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-6">
          <!-- Active Employers Registered -->
          <div class="flex items-center bg-yellow-200 p-4 rounded shadow-md">
            <div class="flex-shrink-0 bg-blue-800 p-3 rounded text-white">
              <i class="fa fa-building fa-2x"></i>
            </div>
            <div class="ml-4">
              <p class="text-gray-700 font-semibold">Active Employers Registered</p>
              <p class="text-2xl font-bold">50</p>
            </div>
          </div>
          
          <!-- Registered Candidates -->
          <div class="flex items-center bg-yellow-200 p-4 rounded shadow-md">
            <div class="flex-shrink-0 bg-blue-800 p-3 rounded text-white">
              <i class="fa fa-users fa-2x"></i>
            </div>
            <div class="ml-4">
              <p class="text-gray-700 font-semibold">Registered Candidates</p>
              <p class="text-2xl font-bold">50</p>
            </div>
          </div>

          <!-- Total Job Posts -->
          <div class="flex items-center bg-yellow-200 p-4 rounded shadow-md">
            <div class="flex-shrink-0 bg-blue-800 p-3 rounded text-white">
              <i class="fa fa-briefcase fa-2x"></i>
            </div>
            <div class="ml-4">
              <p class="text-gray-700 font-semibold">Total Job Posts</p>
              <p class="text-2xl font-bold">50</p>
            </div>
          </div>

          <!-- Pending Employers Approval -->
          <div class="flex items-center bg-yellow-200 p-4 rounded shadow-md">
            <div class="flex-shrink-0 bg-blue-800 p-3 rounded text-white">
              <i class="fa fa-building fa-2x"></i>
            </div>
            <div class="ml-4">
              <p class="text-gray-700 font-semibold">Pending Employers Approval</p>
              <p class="text-2xl font-bold">50</p>
            </div>
          </div>

          <!-- Pending Candidates Confirmation -->
          <div class="flex items-center bg-yellow-200 p-4 rounded shadow-md">
            <div class="flex-shrink-0 bg-blue-800 p-3 rounded text-white">
              <i class="fa fa-users fa-2x"></i>
            </div>
            <div class="ml-4">
              <p class="text-gray-700 font-semibold">Pending Candidates Confirmation</p>
              <p class="text-2xl font-bold">50</p>
            </div>
          </div>

          <!-- Pending Jobs Approval -->
          <div class="flex items-center bg-yellow-200 p-4 rounded shadow-md">
            <div class="flex-shrink-0 bg-blue-800 p-3 rounded text-white">
              <i class="fa fa-briefcase fa-2x"></i>
            </div>
            <div class="ml-4">
              <p class="text-gray-700 font-semibold">Pending Jobs Approval</p>
              <p class="text-2xl font-bold">50</p>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>


<!-- Bootstrap and jQuery JS --> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> 
</body> 
</html> 

