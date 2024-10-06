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
  <title>Manage Applicants</title>
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
        <a href="#" class="text-indigo-900 hover:text-blue-800" id="toggleSidebar">
          <i class="fa fa-bars text-xl"></i> <!-- FontAwesome hamburger icon -->
        </a>

        <!-- Logo -->
        <a href="index.php" class="flex items-center space-x-2">
          <img src="../img/logo.png" alt="KonekTra" class="h-10"> <!-- Replace with your logo -->
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

          <li class="active border-t border-b border-yellow-50 px-6 py-2.5 bg-blue-800"><a href="man-applicants.php" class="flex items-center space-x-2 text-yellow-100 font-semibold hover:text-yellow-50 hover:no-underline"><i class="fa fa-users"></i> <span>Manage Applicants</span></a></li>

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
        <h3 class="text-2xl font-bold text-indigo-900">Applicants</h3>
        <div class="mt-4">
          <table class="min-w-full bg-white shadow-md rounded overflow-hidden">
            <thead>
              <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">Name</th>
                <th class="py-3 px-6 text-left">Status</th>
                <th class="py-3 px-6 text-left">Qualification</th>
                <th class="py-3 px-6 text-left">Action</th>
              </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
            <?php
              // SQL query to fetch concatenated name, status, and certificates for each applicant
              $sql = "
                SELECT 
                  a.firstname, 
                  a.lastname, 
                  u.status, 
                  GROUP_CONCAT(c.certificate_name SEPARATOR '\n') AS qualifications,
                  a.id_applicant -- needed for the action buttons
                FROM applicants a
                JOIN users u ON a.id_user = u.id_user
                LEFT JOIN certifications ct ON a.id_applicant = ct.id_applicant
                LEFT JOIN certificates c ON ct.id_certificate = c.id_certificate
                GROUP BY a.id_applicant
              ";
              
              // Execute the query
              $result = $conn->query($sql);

              // Check if any results were returned
              if($result->num_rows > 0) {
                // Loop through each applicant row
                while($row = $result->fetch_assoc()) {
                  // Concatenate first name and last name
                  $fullName = $row['firstname'] . ' ' . $row['lastname'];
                  // Get the qualifications (certificates) and separate them with new lines
                  $qualifications = nl2br($row['qualifications']); // Converts \n to <br> for HTML display
              ?>
              <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td class="py-3 px-6"><?php echo $fullName; ?></td>
                <td class="py-3 px-6"><?php echo $row['status']; ?></td>
                <td class="py-3 px-6"><?php echo $qualifications; ?></td>
                <td class="py-3 px-6 flex space-x-2">
                <a href="view-applicant.php?id=<?php echo $row['id_applicant']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">View</a>
                  <!-- Approve Button -->
                  <?php if ($row['status'] !== 'Active') { ?>
        <button 
            onclick="approveApplicant(<?php echo $row['id_applicant']; ?>, '<?php echo $fullName; ?>')" 
            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">
            Approve User
        </button>
    <?php } ?>
                  <!-- Delete Button -->
                  <button 
        onclick="confirmDelete(<?php echo $row['id_applicant']; ?>, '<?php echo $fullName; ?>')" 
        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">
        Delete
    </button>
                </td>
              </tr>
              <?php
                }
              } else {
                echo "<tr><td colspan='4' class='text-center py-4'>No applicants found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>



  <!-- Approval Modal -->
  <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveApplicantModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveApplicantModal">Approve Applicant</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve <strong id="applicantName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="approveLink" class="btn btn-success">Approve</a>
            </div>
        </div>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteApplicantModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteApplicantModal">Delete Applicant</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteApplicantName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>






  <!-- Footer -->
  <!-- <footer class="text-center py-4 bg-gray-100 text-gray-600">
    <strong>&copy; KonekTra</strong> All rights reserved.
  </footer> -->

  <!-- Toggle Sidebar Script -->
  <script>
    document.getElementById('toggleSidebar').addEventListener('click', function () {
      document.getElementById('sidebar').classList.toggle('hidden');
    });





    function approveApplicant(id, name) {
    // Show the modal
    $("#approveModal").modal("show");

    // Populate the modal fields
    $("#applicantName").text(name);

    // Set the approval link with the applicant's ID
    $("#approveLink").attr("href", "approve-applicant.php?id=" + id);
}

    function confirmDelete(id, name) {
        // Show the delete modal
        $("#deleteModal").modal("show");

        // Populate the modal fields
        $("#deleteApplicantName").text(name);

        // Set the delete link with the applicant's ID
        $("#deleteLink").attr("href", "delete-applicant.php?id=" + id);
    }


  </script>
      <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>

</html>
