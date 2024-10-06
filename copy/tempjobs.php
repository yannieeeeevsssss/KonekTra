<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Job Board</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <!-- Include Bootstrap, Font Awesome, and AdminLTE -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/AdminLTE.min.css">
  <link rel="stylesheet" href="css/_all-skins.min.css">
  <link rel="stylesheet" href="css/custom.css">
  <link rel="stylesheet" href="css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini">

<div class="wrapper">
  <header class="main-header">
    <!-- Logo -->
    <a href="index.php" class="logo logo-bg">
      <img src="img/logo.png" alt="KonekTra"> <!-- Replace with your logo -->
    </a>

    <nav class="navbar navbar-static-top">
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li><a href="tempjobs.php">Jobs</a></li>
          <li><a href="index.php">About Us</a></li>
          <?php if(empty($_SESSION['id_user']) && empty($_SESSION['id_company'])) { ?>
            <li><a href="sign-up.php">Dashboard</a></li>
            <li><a href="templogin.php">Login</a></li>
            <li><a href="sign-up.php">Sign Up</a></li>
          <?php } else { if(isset($_SESSION['id_user'])) { ?>
            <li><a href="user/index.php">Dashboard</a></li>
          <?php } else if(isset($_SESSION['id_company'])) { ?>
            <li><a href="company/index.php">Dashboard</a></li>
          <?php } ?>
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="path_to_avatar_image" class="user-image" alt="User Image">
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="path_to_avatar_image" class="img-circle" alt="User Image">
                <p>Username - Role</p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="profile.php" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="logout.php" class="btn btn-default btn-flat">Logout</a>
                </div>
              </li>
            </ul>
          </li>
          <?php } ?>
        </ul>
      </div>
    </nav>
  </header>
    
    <!-- Filters Section -->
    <div class="main-container">
        <!-- Filters Section -->
        <div class="filters-section">
          <div class="filter-header">
            <h3>Search by Sector</h3>
            <select>
              <option value="all">All Sectors</option>
              <option value="tech">Tech</option>
              <option value="hospitality">Hospitality</option>
              <!-- Add more options as needed -->
            </select>
          </div>
          <div class="filter-header">
            <h3>Search by Location</h3>
            <select>
              <option value="all">All Locations</option>
              <option value="leyte">Leyte</option>
              <option value="cebu">Cebu</option>
              <!-- Add more options as needed -->
            </select>
          </div>
          <div class="filter-header">
            <h3>Job Type</h3>
            <div class="checkbox-container">
              <label><input type="checkbox" name="full-time"> Full-Time</label>
              <label><input type="checkbox" name="task-based"> Task-Based</label>
              <label><input type="checkbox" name="remote"> Remote</label>
              <label><input type="checkbox" name="on-site"> On-Site</label>
            </div>
          </div>
          <div class="search-button">
            <button>Search</button>
          </div>
        </div>
    
        <!-- Job Cards Section -->
        <div class="job-cards-section">
          <div class="job-card-container">
            <div class="job-card">
              <div class="card-header">
                <img src="img/company.png" alt="Company Logo" class="company-logo">
                <div class="job-info">
                  <div class="job-title">Front Desk Officer</div>
                  <div class="company-name">ABC Hotel Inc.</div>
                  <div class="job-location"><i class="fas fa-map-marker-alt"></i> Abuyog, Leyte</div>
                </div>
              </div>
              <div class="job-details">
                <p><i class="fas fa-money-bill-wave"></i> ₱18,000 - ₱20,000</p>
                <p><i class="fas fa-clock"></i> Full-Time <span class="dot">•</span> On-site</p>
                <p><i class="fas fa-calendar-alt"></i> Apply before Sept 19</p>
              </div>
              <div class="card-footer">
                <a href="viewjob.html"><button class="view-btn">View Job</button></a>
                <a href="viewjob.html"><button class="apply-btn" data-toggle="modal" data-target="#applyModal">Apply</button></a>
              </div>
            </div>

            
    
            <!-- Duplicate card for layout reference -->
            <div class="job-card">
              <div class="card-header">
                <img src="img/company.png" alt="Company Logo" class="company-logo">
                <div class="job-info">
                  <div class="job-title">Front Desk Officer</div>
                  <div class="company-name">ABC Hotel Inc.</div>
                  <div class="job-location"><i class="fas fa-map-marker-alt"></i> Abuyog, Leyte</div>
                </div>
              </div>
              <div class="job-details">
                <p><i class="fas fa-money-bill-wave"></i> ₱18,000 - ₱20,000</p>
                <p><i class="fas fa-clock"></i> Full-Time <span class="dot">•</span> On-site</p>
                <p><i class="fas fa-calendar-alt"></i> Apply before Sept 19</p>
              </div>
              <div class="card-footer">
                <a href="viewjob.html"><button class="view-btn">View Job</button></a>
                <a href="viewjob.html"><button class="apply-btn">Apply</button></a>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
  </div>
  
  
  <div class="modal fade" id="applyModal" tabindex="-1" role="dialog" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applyModalLabel">Apply Job</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <!-- Resume Upload -->
                    <div class="form-group">
                        <label for="resume">Resume</label>
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


</body>
</html>
