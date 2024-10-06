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
          <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800"><a href="dashboard.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

          <li class="active border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800"><a href="man-applicants.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-users"></i> <span>Manage Applicants</span></a></li>

          <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800"><a href="man-employers.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-building"></i> <span>Manage Employers</span></a></li>

          <li class="border-t border-b border-yellow-50 px-6 py-2.5 bg-blue-800"><a href="man-jobs.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-briefcase"></i> <span>Manage Jobs</span></a></li>

          <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800"><a href="reports.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-file-text"></i> <span>Reports</span></a></li>
          
          <li class="border-t border-b border-yellow-50 px-6 py-2.5 hover:bg-blue-800"><a href="../logout.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-arrow-circle-o-right"></i> <span>Logout</span></a></li>
        </ul>
      </div>
    </aside>
    
    <!-- Content Wrapper -->
    <main class="flex-1 p-6">
      <section class="mb-6">
        <h3 class="text-2xl font-bold text-indigo-900">Jobs</h3>
        <div class="mt-4">
          <table class="min-w-full bg-white shadow-md rounded overflow-hidden">
            <thead>
              <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">Job Title</th>
                <th class="py-3 px-6 text-left">Company</th>
                <th class="py-3 px-6 text-left">Status</th>
                <th class="py-3 px-6 text-left">Action</th>
              </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
            <?php
              // SQL query to fetch concatenated name, company, and status for each employer
              $sql = "
                SELECT 
                  e.firstname, 
                  e.lastname, 
                  e.company_name, 
                  u.status,
                  e.id_employer -- needed for the action buttons
                FROM employers e
                JOIN users u ON e.id_user = u.id_user
                ";
              
              // Execute the query
              $result = $conn->query($sql);

              // Check if any results were returned
              if($result->num_rows > 0) {
                // Loop through each employer row
                while($row = $result->fetch_assoc()) {
                  // Concatenate first name and last name
                  $fullName = $row['firstname'] . ' ' . $row['lastname'];
              ?>
              <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td class="py-3 px-6"><?php echo $fullName; ?></td>
                <td class="py-3 px-6"><?php echo $row['company_name']; ?></td>
                <td class="py-3 px-6"><?php echo $row['status']; ?></td>
                <td class="py-3 px-6 flex space-x-2">
                  <a href="view-employer.php?id=<?php echo $row['id_employer']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">View</a>
                  <?php if ($row['status'] !== 'Active') { ?>
                    <button 
                      onclick="approveEmployer(<?php echo $row['id_employer']; ?>, '<?php echo $fullName; ?>')" 
                      class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">
                      Approve
                    </button>
                  <?php } ?>
                  <button 
                    onclick="confirmDelete(<?php echo $row['id_employer']; ?>, '<?php echo $fullName; ?>')" 
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">
                    Delete
                  </button>
                </td>
              </tr>
              <?php
                }
              } else {
                echo "<tr><td colspan='4' class='text-center py-4'>No employers found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

<!-- Approval Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveEmployerModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="approveEmployerModal">Approve Employer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to approve <strong id="employerName"></strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a href="#" id="approveLink" class="btn btn-success">Approve</a>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteEmployerModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteEmployerModal">Delete Employer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="deleteEmployerName"></strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a href="#" id="deleteLink" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>


<script> 

function approveEmployer(id, name) { 
  document.getElementById('employerName').innerText = name; document.getElementById('approveLink').href = 'approve-employer.php?id=' + id; $('#approveModal').modal(); 
}

function confirmDelete(id, name) { 
  document.getElementById('deleteEmployerName').innerText = name; document.getElementById('deleteLink').href = 'delete-employer.php?id=' + id; $('#deleteModal').modal(); 

} 
</script> 

<!-- Bootstrap and jQuery JS --> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> 
</body> 
</html> 

