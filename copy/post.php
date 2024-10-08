<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard - Post A Job</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your main CSS -->
    <script src="js/tinymce/tinymce.min.js"></script>

<script>
  tinymce.init({
    selector: '#description',
    height: 300,
    forced_root_block: false,  // Disable wrapping text in <p> tags
    force_br_newlines: true,   // Force newlines to be <br> tags
    force_p_newlines: false,   // Prevent TinyMCE from using <p> tags for newlines
    convert_newlines_to_brs: true,  // Convert newlines in text to <br> automatically
    content_style: "body { font-family:Helvetica,Arial,sans-serif; font-size:14px }", // Optional style
  });
</script>
  
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="img/logo.png" alt="KonekTra Logo"></a>
            </div>
            <nav>
                <ul>
                    <li><a href="jobs.php">Jobs</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="overview.html">My Dashboard</a></li>
                    <li><a href="#"><img src="img/company.png" alt="Profile Image" class="profile-image"></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h4>Welcome Employer!</h4>
            <ul class="sidebar-nav">
                <li><a href="overview.html"><i class="fas fa-home"></i> Overview</a></li>
                <li><a href="profile-comp.html"><i class="fas fa-user"></i> Profile</a></li>
                <li class="active"><a href="post.html"><i class="fas fa-briefcase"></i> Post a Job</a></li>
                <li><a href="myjobs.html"><i class="fas fa-list"></i> My Jobs</a></li>
                <li><a href="message.html"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="notifications.html"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a href="logout.php"><i class="fas fa-bell"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <h2>Post A Job</h2>

            <form class="post-job-form" method="POST" action="post_job.php">
                <!-- Job Title and Location -->
                <div class="form-group">
                    <input type="text" name="job_title" id="job_title" placeholder="Job Title" required>
                    <input type="text" name="location" id="location" placeholder="Location" required>
                </div>

                <!-- Job Description -->
                <div class="form-group">
                    <textarea class="form-control input-lg" id="description" name="description" placeholder="Job Description"></textarea>
                </div>

                <!-- Salary Fields -->
                <div class="form-group">
                    <input type="text" name="min_salary" id="min_salary" placeholder="Minimum Salary" required>
                    <input type="text" name="max_salary" id="max_salary" placeholder="Maximum Salary" required>
                </div>

                <!-- Job Type and Deadline -->
                <div class="form-group">
                    <div class="job-types">
                        <label><input type="checkbox" name="job_type[]" value="Task Based"> Task Based</label>
                        <label><input type="checkbox" name="job_type[]" value="Full-Time"> Full-Time</label>
                        <label><input type="checkbox" name="job_type[]" value="On-Site"> On-Site</label>
                        <label><input type="checkbox" name="job_type[]" value="Remote"> Remote</label>
                    </div>
                    <div>
                        <label for="deadline">Deadline:</label>
                        <input type="date" name="deadline" id="deadline" required>
                    </div>
                </div>

                <!-- Post Button -->
                <button type="submit" class="post-btn">Post</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <!-- <footer>
        <p>&copy; 2024 KonekTra. All rights reserved.</p>
    </footer> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
