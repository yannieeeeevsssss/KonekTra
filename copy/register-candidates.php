<?php  
session_start();

if(isset($_SESSION['id_user']) || isset($_SESSION['id_company'])) { 
  header("Location: index.php");
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Job Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <!-- Tailwind CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <!-- Flowbite CDN -->
  <script src="https://unpkg.com/flowbite@1.4.7/dist/flowbite.js"></script>

  <!-- Tagify CSS and JS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
  <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
  <!-- Recaptcha -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Jquery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- FontAwesome-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">

<header class="bg-yellow-100 font-sans leading-normal tracking-normal shadow-lg py-3 px-3 sticky top-0 z-50">
  <nav class=" container mx-auto flex justify-between items-center">
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
      <a href="jobs.php"  class="font-bold">Jobs</a>
      <a href="about.php"  class="font-bold">About Us</a>

      <?php if(empty($_SESSION['id_user']) && empty($_SESSION['id_company'])) { ?>
        <!-- New or Pending User -->
        <a href="dashboard.php"  class="font-bold">Dashboard</a>
        <a href="login.php"  class="font-bold">Login</a>
        <a href="sign-up.php"  class="font-bold">Sign Up</a>
      <?php } else { ?>
        <!-- Logged in User (Pending/Active) -->
        <a href="dashboard.php"  class="font-bold">Dashboard</a>
        <a href="notification.php"  class="font-bold">Notification</a>

        <!-- User Avatar -->
        <?php if(isset($_SESSION['profile_image'])) { ?>
          <div class="relative">
            <button id="user-menu-toggle" class="focus:outline-none">
              <img src="<?= $_SESSION['profile_image']; ?>" class="w-10 h-10 rounded-full" alt="User Avatar">
            </button>
            <!-- Dropdown Menu -->
            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg">
              <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
              <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
            </div>
          </div>
        <?php } ?>
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

    <?php if(empty($_SESSION['id_user']) && empty($_SESSION['id_company'])) { ?>
      <a href="dashboard.php" class="text-white text-2xl mb-6">Dashboard</a>
      <a href="login.php" class="text-white text-2xl mb-6">Login</a>
      <a href="signup.php" class="text-white text-2xl mb-6">Sign Up</a>
    <?php } else { ?>
      <a href="dashboard.php" class="text-white text-2xl mb-6">Dashboard</a>
      <a href="notification.php" class="text-white text-2xl mb-6">Notification</a>

      <!-- Avatar for mobile view -->
      <?php if(isset($_SESSION['profile_image'])) { ?>
        <img src="<?= $_SESSION['profile_image']; ?>" class="w-20 h-20 rounded-full mb-6" alt="User Avatar">
        <a href="profile.php" class="text-white text-2xl mb-6">Profile</a>
        <a href="logout.php" class="text-white text-2xl mb-6">Logout</a>
      <?php } ?>
    <?php } ?>
  </div>
</header>

<div class="container mx-auto py-12">
    <h1 class="text-center text-4xl font-bold mb-8 text-indigo-900">CREATE YOUR PROFILE</h1>
    
    <form method="post" id="registerApplicants" action="adduser.php" enctype="multipart/form-data" class="bg-yellow-50 mx-auto px-10 py-4 rounded-lg shadow-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Personal Information Section -->
            <div>
                <h3 class="text-xl text-blue-900 font-bold mb-4">Personal Information</h3>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="text" id="lname" name="lname" placeholder="Last Name *" required>
                </div>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="text" id="fname" name="fname" placeholder="First Name *" required>
                </div>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="text" id="mname" name="mname" placeholder="Middle Name">
                </div>
                <div class="mb-4 border border-dark rounded">
                    <select class="w-full p-2 border border-dark rounded" id="gender" name="gender" required>
                        <option value="" disabled selected>Gender *</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="date" id="dob" name="dob" placeholder="Date of Birth" required>
                </div>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="text" id="age" name="age" placeholder="Age" readonly>
                </div>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="text" id="house_no" name="house_no" placeholder="House No/Street">
                </div>
                <div class="mb-4 border border-dark rounded">
                    <select class="w-full p-2 border border-dark rounded" id="city" name="city" required>
                        <option value="" disabled selected>Select City</option>
                        <!-- Options populated from the database -->
                    </select>
                </div>
                <div class="mb-4 border border-dark rounded">
                    <select id="province" name="province" class="w-full p-2 border border-dark rounded" required>
                        <option value="" disabled selected>Select Province</option>
                        <!-- Options populated from the database -->
                    </select>
                </div>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="text" id="contactno" name="contactno" minlength="10" maxlength="15" placeholder="Contact No *" required>
                </div>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="email" id="email" name="email" placeholder="Email *" required>
                </div>
                <div class="mb-4 rounded">
                  <!-- Input and eye icon inside a relative container -->
                  <div class="relative">
                    <input class="w-full p-2 border-2 border-dark rounded pr-10" type="password" id="password" name="password" placeholder="Password *" required oninput="validatePassword()">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePasswordVisibility('password', 'password-icon')">
                      <i id="password-icon" class="fas fa-eye text-gray-500"></i>
                    </span>
                  </div>
    
                  <!-- Password requirements hint -->
                  <p class="text-xs text-gray-500 mt-2">Password must be at least 8 characters long, and contain at least one letter, one number, and one special character.</p>

                  <!-- Error message for password validation -->
                  <div id="password-error" class="text-red-600 mt-1 hidden">Invalid password format.</div>
                </div>


                <!-- Confirm Password field with toggle eye icon -->
                <div class="relative mb-4 border border-dark rounded">
                  <input class="w-full p-2 border border-dark rounded pr-10" type="password" id="cpassword" name="cpassword" placeholder="Confirm Password *" required oninput="comparePasswords()">
                  <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePasswordVisibility('cpassword', 'cpassword-icon')">
                    <i id="cpassword-icon" class="fas fa-eye text-gray-500"></i>
                  </span>
                  <div id="confirm-password-error" class="text-red-600 mt-2 hidden">Passwords do not match.</div>
                </div>
                <div class="mb-4">
                    <label class="text-base text-blue-900 font-semibold">Upload Profile Image (JPEG, PNG only)</label>
                    <input type="file" name="applicant_image" accept="image/*" class="w-md p-2 rounded-lg bg-red-700 text-white" required>
                </div>
                <div class="g-recaptcha" data-sitekey="6Ld9SSEqAAAAACaAUOjU-XWq6LPfmYyc-WTahfNt"></div>
                <div id="recaptcha-warning" style="color: red; display: none;">Please check the reCAPTCHA first.</div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" required> 
                        <span class="ml-2">I accept terms & conditions</span>
                    </label>
                </div>
                <div class="mb-4">
                    <button class="w-sm px-4 py-2 rounded-lg bg-green-600 text-white font-bold">Register</button>
                </div>
            </div>
            
            <!-- Professional Information Section -->
            <div>
                <h3 class="text-xl text-blue-900 font-bold mb-4">Professional Information</h3>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="text" id="education" name="education" placeholder="Highest Educational Attainment" required>
                </div>
                <div class="mb-4">
                    <label class="text-base text-blue-900 font-semibold">Upload Resume/CV (PDF format only)</label>
                    <input type="file" name="resume" accept="application/pdf" class="w-md p-2 rounded-lg bg-red-700 text-white" required>
                </div>
                <div class="mb-4">
                        <textarea class="w-full p-2 border-2 border-dark rounded" rows="4" name="aboutme" placeholder="Tell us about yourself"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="text-base text-blue-900 font-semibold" for="preferred_job">Preferred Job Tags</label>
                            <input type="text" class="w-full p-2 border-2 border-dark rounded" id="preferred_job" name="preferred_job" data-role="tagsinput" placeholder="Add preferred job tags" required>
                        </div>
                        <h3 class="text-xl text-blue-900 font-bold mb-4">National Certificates</h3>
                        <div id="certificate-section">
                            <!-- Certificate Group 1 -->
                            <div class="mb-4 border border-dark rounded">
                                <select class="w-full p-2 border border-dark rounded" id="certificate_name" name="certificate_name[]" required>
                                    <option value="" disabled selected>Select TESDA Certificate</option>
                                    <!-- Populate from database -->
                                </select>
                            </div>
                            <div class="mb-4 border border-dark rounded">
                                <input class="w-full p-2 border border-dark rounded" type="text" name="certificate_no[]" placeholder="Certificate No." required>
                            </div>
                            <div class="mb-4 border border-dark rounded">
                                <input class="w-full p-2 border border-dark rounded" type="text" name="training_center[]" placeholder="Training Center" required>
                            </div>
                            <div class="mb-4">
                                <label class="text-base text-blue-900 font-semibold">Issuance Date</label>
                                <input class="w-full p-2 border-2 border-dark rounded" type="date" name="issuance_date[]" required>
                            </div>
                            <div class="mb-4">
                                <label class="text-base text-blue-900 font-semibold">Expiration Date</label>
                                <input class="w-full p-2 border-2 border-dark rounded" type="date" name="expiration_date[]" required>
                            </div>
                            <div class="mb-4">
                                <label class="text-base text-blue-900 font-semibold">Upload Certificate Image (JPEG, PNG only)</label>
                                <input type="file" name="certificate_image[]" accept="image/*" class="btn btn-flat btn-danger" required>
                            </div>
            </div>

                        <div class="mb-4">
                            <button type="button" class="w-sm px-4 py-2 rounded-lg bg-green-600 text-white font-bold" id="addCertificate">Add More Certificates</button>
                        </div>

                        <?php if(isset($_SESSION['registerError'])) { ?>
                            <div class="form-group">
                                <label style="color: red;">Email Already Exists! Choose A Different Email!</label>
                            </div>
                            <?php unset($_SESSION['registerError']); } ?>

                        <?php if(isset($_SESSION['uploadError'])) { ?>
                            <div class="form-group">
                                <label style="color: red;"><?php echo $_SESSION['uploadError']; ?></label>
                            </div>
                            <?php unset($_SESSION['uploadError']); } ?>

        </div>
    </form>
</div>

<!-- <script>
  // Flowbite Dropdown functionality
  document.getElementById('userMenu').addEventListener('click', function() {
      document.getElementById('userDropdown').classList.toggle('hidden');
  });

  // Tagify for skills input
  var input = document.querySelector('textarea[name=skills]');
  new Tagify(input);
</script> -->


<script>
// Fetch all provinces when the page loads
    fetchProvinces();

function fetchProvinces() {
    $.ajax({
        url: 'fetch-province.php', // PHP script to fetch provinces from DB
        type: 'GET',
        success: function (data) {
            $('#province').html(data);
        }
    });
}

// Fetch all cities when the page loads
fetchCities();

function fetchCities() {
  $.ajax({
    url: 'fetch-cities.php', // PHP script to fetch all cities from DB
    type: 'GET',
    success: function (data) {
      $('#city').html(data); // Populate the city dropdown
    }
  });
}



// Initialize Tagify on the preferred job input
var input = document.querySelector('#preferred_job');
var tagify = new Tagify(input, {
  maxTags: 5,  // Allow a maximum of 5 tags
  dropdown: {
    maxItems: 20,        // Maximum items to show in the dropdown
    enabled: 0,          // Show suggestions when typing
    closeOnSelect: false // Keep the dropdown open after selecting
  }
});

// Optional: Add an event listener to capture when a tag is added or removed
tagify.on('add', function(e){
    console.log("Tag added: ", e.detail.data.value);
});

tagify.on('remove', function(e){
    console.log("Tag removed: ", e.detail.data.value);
});

// Fetch all certificates when the page loads
fetchCertificates()

function fetchCertificates() {
  $.ajax({
    url: 'fetch-certificate.php', // PHP script to fetch all cities from DB
    type: 'GET',
    success: function (data) {
      $('#certificate_name').html(data); // Populate the city dropdown
    }
  });
}

</script>

<script type="text/javascript">
      function validatePhone(event) {

        //event.keycode will return unicode for characters and numbers like a, b, c, 5 etc.
        //event.which will return key for mouse events and other events like ctrl alt etc. 
        var key = window.event ? event.keyCode : event.which;

        if(event.keyCode == 8 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 39) {
          // 8 means Backspace
          //46 means Delete
          // 37 means left arrow
          // 39 means right arrow
          return true;
        } else if( key < 48 || key > 57 ) {
          // 48-57 is 0-9 numbers on your keyboard.
          return false;
        } else return true;
      }
</script>

<!-- // Age Validation -->
<script type="text/javascript">
  $('#dob').on('change', function() {
    var today = new Date();
    var birthDate = new Date($(this).val());
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();

    if(m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }

    $('#age').val(age);


    if(age < 18) {
        alert("You must be at least 18 years old to register.");
        $('#age').val('');
    }
});
</script>
<script>
// Issuance and Deadline
$(document).on('change', 'input[name="issuance_date[]"], input[name="expiration_date[]"]', function() {
    const issuanceDate = $(this).closest('.form-group').find('input[name="issuance_date[]"]').val();
    const expirationDate = $(this).closest('.form-group').find('input[name="expiration_date[]"]').val();

    if (issuanceDate && expirationDate && new Date(expirationDate) <= new Date(issuanceDate)) {
        alert("Expiration date must be later than issuance date.");
        $(this).closest('.form-group').find('input[name="expiration_date[]"]').val('');
    }
});

// Assuming you are using Google reCAPTCHA v2
$("#registerApplicants").on("submit", function(e) {
    const captchaResponse = grecaptcha.getResponse(); 

    if (captchaResponse.length == 0) {
        e.preventDefault();
        alert("Please verify the captcha.");
        return false;
    }
});


  // add more certificate
  let certificateCount = 1;
    document.getElementById('addCertificate').addEventListener('click', function() {
        certificateCount++;
        const certificateSection = document.getElementById('certificate-section');
        const newCertificate = `
            <div class="form-group">
               <select class="form-control input-lg" id="certificate_name" name="certificate_name[]" required>
                  <option value="" disabled selected>Select TESDA Certificate</option>
                   <!-- Populate from database -->
               </select>
            </div>
            <div class="form-group">
                <input class="form-control input-lg" type="text" name="certificate_no[]" placeholder="Certificate No." required>
            </div>
            <div class="form-group">
                <input class="form-control input-lg" type="text" name="training_center[]" placeholder="Training Center" required>
            </div>
            <div class="form-group">
                <label>Issuance Date</label>
                <input class="form-control input-lg" type="date" name="issuance_date[]" required>
            </div>
            <div class="form-group">
                <label>Expiration Date</label>
                <input class="form-control input-lg" type="date" name="expiration_date[]" required>
            </div>
            <div class="form-group">
                <label>Upload Certificate Image (JPEG, PNG only)</label>
                <input type="file" name="certificate_image[]" accept="image/*" class="btn btn-flat btn-danger" required>
            </div>
        `;
        certificateSection.insertAdjacentHTML('beforeend', newCertificate);
    });
</script>
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
<script>
 // Toggle password visibility function
 function togglePasswordVisibility(passwordFieldId, iconId) {
        const passwordField = document.getElementById(passwordFieldId);
        const icon = document.getElementById(iconId);
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }


    // Validate password function
    function validatePassword() {
        const password = document.getElementById('password');
        const passwordError = document.getElementById('password-error');
        const passwordValue = password.value;

        // Regex for validating password format
        const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;

        // If password doesn't match the criteria, show error and turn input red
        if (!passwordRegex.test(passwordValue)) {
            password.classList.remove('border-green-500');
            password.classList.add('border-red-500');
            passwordError.classList.remove('hidden');
            return false;
        } else {
            password.classList.remove('border-red-500');
            password.classList.add('border-green-500');
            passwordError.classList.add('hidden');
            return true;
        }
    }

    // Compare the password and confirm password fields
    function comparePasswords() {
        const password = document.getElementById('password').value;
        const cpassword = document.getElementById('cpassword');
        const confirmPasswordError = document.getElementById('confirm-password-error');

        if (password !== cpassword.value) {
            cpassword.classList.remove('border-green-500');
            cpassword.classList.add('border-red-500');
            confirmPasswordError.classList.remove('hidden');
            return false;
        } else {
            cpassword.classList.remove('border-red-500');
            cpassword.classList.add('border-green-500');
            confirmPasswordError.classList.add('hidden');
            return true;
        }
    }

    // Overall form validation
    function validateForm() {
        return validatePassword() && comparePasswords();
    }
</script>
<script src="https://unpkg.com/flowbite@1.4.7/dist/flowbite.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
</body>
</html>
