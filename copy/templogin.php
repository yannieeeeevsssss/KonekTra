<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KonekTra Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <!-- Include Bootstrap, Font Awesome, and AdminLTE -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/AdminLTE.min.css">
  <link rel="stylesheet" href="css/_all-skins.min.css">
  <link rel="stylesheet" href="css/custom.css">
  <link rel="stylesheet" href="css/styles.css">
  <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> -->
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
            <li><a href="login.php">Login</a></li>
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

    <!-- Login Section -->
    <section class="login-section">
        <div class="login-container">
            <div class="left-box">
                <img src="img/tesdawork.jpg" alt="TESDA Logo">
                <p>We are glad that you are part of our growing community!</p>
                <p><strong>New to KonekTra?</strong> <a href="signup.html">Sign up</a></p>
            </div>
            <div class="right-box">
                <h3>Welcome back</h3>
                <form>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    
                    <div class="g-recaptcha input-box" data-sitekey="6Ld9SSEqAAAAACaAUOjU-XWq6LPfmYyc-WTahfNt"></div>
                <div id="recaptcha-warning" style="color: red; display: none;">Please check the reCAPTCHA first.</div>

                    <button type="submit">Login</button>

                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; KonekTra. All Rights Reserved</p>
    </footer>
    </div>
</body>
</html>
