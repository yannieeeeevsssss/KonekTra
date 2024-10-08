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
  <!-- Font-Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">

<header class="bg-yellow-100 font-sans leading-normal tracking-normal shadow-lg py-3 px-3">
    <div class="container mx-auto flex justify-between items-center">
        <a href="index.php" class="text-white font-bold text-2xl">
            <img src="img/logo.png" alt="KonekTra" class="h-10 inline-block"> <!-- Replace with your logo -->
        </a>
        <nav>
            <ul class="flex space-x-6 text-blue-900">
                <li><a href="tempjobs.php" class="font-bold hover:underline">Jobs</a></li>
                <li><a href="#about" class="font-bold hover:underline">About Us</a></li>
                <?php if(empty($_SESSION['id_user']) && empty($_SESSION['id_company'])) { ?>
                  <li><a href="sign-up.php" class="font-bold hover:underline">Dashboard</a></li>
                    <li><a href="login.php" class="font-bold hover:underline">Login</a></li>
                    <li><a href="sign-up.php" class="font-bold hover:underline">Sign Up</a></li>
                <?php } else { ?>
                    <?php if(isset($_SESSION['id_user'])) { ?>
                        <li><a href="user/index.php" class="font-bold hover:underline">Dashboard</a></li>
                    <?php } elseif(isset($_SESSION['id_company'])) { ?>
                        <li><a href="company/index.php" class="font-bold hover:underline">Dashboard</a></li>
                    <?php } ?>
                    <li class="relative">
                        <button id="userMenu" class="text-white focus:outline-none">
                            <img src="path_to_avatar_image" class="h-8 w-8 rounded-full" alt="User Image">
                        </button>
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-10">
                            <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</header>

<div class="container mx-auto py-12">
    <h1 class="text-center text-4xl font-bold mb-8 text-indigo-900">CREATE YOUR PROFILE</h1>
    
    <form method="post" id="registerApplicants" action="addemployer.php" enctype="multipart/form-data" class="bg-yellow-50 mx-auto px-10 py-4 rounded-lg shadow-lg" onsubmit="return validateForm()">
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
                    <input class="w-full p-2 border border-dark rounded" type="text" id="house_no" name="house_no" placeholder="House No/Street/Brgy">
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
                <div class="relative mb-4 rounded">
                  <input class="w-full p-2 border-2 border-dark rounded pr-10" type="password" id="cpassword" name="cpassword" placeholder="Confirm Password *" required oninput="comparePasswords()">
                  <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePasswordVisibility('cpassword', 'cpassword-icon')">
                    <i id="cpassword-icon" class="fas fa-eye text-gray-500"></i>
                  </span>
                  <div id="confirm-password-error" class="text-red-600 mt-2 hidden">Passwords do not match.</div>
                </div>
            </div>
            
            <!-- Company Information Section -->
            <div>
                <h3 class="text-xl text-blue-900 font-bold mb-4">Company Information</h3>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="text" id="company_name" name="company_name" placeholder="Company Name(Optional)" required>
                </div>
                <div class="mb-4 border border-dark rounded">
                <input class="w-full p-2 border border-dark rounded" type="text" name="regno" placeholder="Registration No" required>
                </div>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="text" id="contactno" name="contactno" minlength="10" maxlength="11" placeholder="Contact No *" required>
                </div>
                <div class="mb-4 border border-dark rounded">
                    <input class="w-full p-2 border border-dark rounded" type="email" id="email" name="email" placeholder="Email *" required>
                </div>

                <div class="mb-1">
                        <textarea class="w-full p-2 border-2 border-dark rounded" rows="4" name="aboutme" placeholder="Brief Description of you/your company..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="text-base text-blue-900 font-semibold">Upload Profile Image (JPEG, PNG only)</label>
                    <input type="file" name="employer_image" accept="image/*" class="w-md p-2 rounded-lg bg-red-700 text-white" required>
                </div>
                <div class="g-recaptcha" data-sitekey="6Ld9SSEqAAAAACaAUOjU-XWq6LPfmYyc-WTahfNt"></div>
                <div id="recaptcha-warning" style="color: red; display: none;">Please check the reCAPTCHA first.</div>
                <div class="mb-1">
                    <label class="inline-flex items-center">
                        <input type="checkbox" required> 
                        <span class="ml-2">I accept terms & conditions</span>
                    </label>
                </div>
                <div class="mb-4">
                    <button type="submit" class="w-sm px-4 py-2 rounded-lg bg-green-600 text-white font-bold">Register</button>
                </div>

            </div>


                        <?php if(isset($_SESSION['registerError'])) { ?>
                            <div class="mb-4 border border-dark rounded">
                                <label style="color: red;">Email Already Exists! Choose A Different Email!</label>
                            </div>
                            <?php unset($_SESSION['registerError']); } ?>

                        <?php if(isset($_SESSION['uploadError'])) { ?>
                            <div class="mb-4 border border-dark rounded">
                                <label style="color: red;"><?php echo $_SESSION['uploadError']; ?></label>
                            </div>
                            <?php unset($_SESSION['uploadError']); } ?>

        </div>
    </form>
</div>



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
// Assuming you are using Google reCAPTCHA v2
$("#registerApplicants").on("submit", function(e) {
    const captchaResponse = grecaptcha.getResponse(); 

    if (captchaResponse.length == 0) {
        e.preventDefault();
        alert("Please verify the captcha.");
        return false;
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
