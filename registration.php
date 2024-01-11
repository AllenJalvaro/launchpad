<?php
require "config.php";

if (!empty($_SESSION["email"])) {
    if ($_SESSION["user_type"] === "teacher") {
        header("Location: teacher-dashboard.php");
    } elseif ($_SESSION["user_type"] === "student") {
        header("Location: index.php");
    }
    exit(); 
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Launchpad</title>
    <link rel="icon" href="/launchpad/images/favicon.svg">
    <link rel="stylesheet" href="css/registration.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="js/registrationToggle.js"></script>
    <script src="js/error-reg-handling.js"></script>
      <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>

<?php
if (isset($_POST["submitStudent"])) {
    $studentid = $_POST["studentid"];
    $email = $_POST["email"];
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $course = $_POST["course"];
    $yearlevel = $_POST["yearlevel"];
    $block = $_POST["block"];
    $contactno = $_POST["contactno"];
    $password = $_POST["password"];


    $checkStudentIdQuery = "SELECT * FROM student_registration WHERE Student_ID = '$studentid'";
    $resultStudentId = mysqli_query($conn, $checkStudentIdQuery);


    $checkEmailQuery = "SELECT * FROM student_registration WHERE Student_email = '$email'";
    $resultEmail = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($resultStudentId) > 0) {

        echo "<script>
        Swal.fire({
            title: 'Student ID: ".$studentid." is an existing account.',
            text: '',
            icon: 'warning',
            showCancelButton: false,
            showDenyButton: false,
            showCloseButton: false,
            confirmButtonText: 'OK'
        });
      </script>";
    } elseif (mysqli_num_rows($resultEmail) > 0) {

        echo "<script>
        Swal.fire({
            title: 'Email: ".$email." is an existing account.',
            text: '',
            icon: 'warning',
            showCancelButton: false,
            showDenyButton: false,
            showCloseButton: false,
            confirmButtonText: 'OK'
        });
      </script>";
    } else {
        $query = "INSERT INTO student_registration (Student_ID, Student_fname, Student_lname, Student_email, Student_password, Course, Year, Block, Student_contactno) 
                  VALUES ('$studentid', '$firstname', '$lastname', '$email', '$password', '$course', '$yearlevel', '$block', '$contactno')";

        if (mysqli_query($conn, $query)) {
              echo "<script>
                Swal.fire({
                    title: 'Your account has been created Successfully!',
                    text: '',
                    icon: 'success',
                    showConfirmButton: false,
                });
              </script>";
            header("Refresh: 3 url=login.php");
        }
    }
} elseif (isset($_POST["submitTeacher"])) {
    $empID = $_POST["empID"];
    $temail = $_POST["temail"];
    $tfirstname = $_POST["tfirstname"];
    $tlastname = $_POST["tlastname"];
    $department = $_POST["department"];
    $tcontactno = $_POST["tcontactno"];
    $tpassword = $_POST["tpassword"];

    $checkEmailQuery = "SELECT * FROM instructor_registration WHERE Instructor_email = '$temail'";
    $resultEmail = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($resultEmail) > 0) {
        echo "<script>
        Swal.fire({
            title: 'Email: ".$temail." is an existing account.',
            text: '',
            icon: 'warning',
            showCancelButton: false,
            showDenyButton: false,
            showCloseButton: false,
            confirmButtonText: 'OK'
        });
      </script>";
    } else {
        $query = "INSERT INTO instructor_registration (Instructor_fname, Instructor_lname, empID, Instructor_email, Instructor_password, Department, Instructor_contactno) 
                  VALUES ('$tfirstname', '$tlastname', '$empID', '$temail', '$tpassword', '$department', '$tcontactno')";

        if (mysqli_query($conn, $query)) {
            echo "<script>
            Swal.fire({
                title: 'Your account has been created Successfully!',
                text: '',
                icon: 'success',
                showConfirmButton: false,
            });
          </script>";
        header("Refresh: 3 url=login.php");
        }
    }
}
?>


    <br>
    <div class="student-register-card">
        <a href="landingpage.php"><img src="/launchpad/images/logo-text.svg" alt="launchpad-logo" class="logotext"/></a>
       
        <h3 id="accountTypeTitle">Create account as a Student</h3>
<h5 id="accountTypeDescription">Launch your company, create startups with your team, or join exciting projects to attract investors!</h5>
<h6><i>This is ONLY for students and instructors form PSU-Urdaneta City Campus.</i></h6>

        <div class="registration-toggle">
    <input type="radio" name="registrationType" id="studentToggle" value="student" checked>
    <label for="studentToggle" class="studentToggle">I'm a student</label>

    <input type="radio" name="registrationType" id="teacherToggle" value="teacher">
    <label for="teacherToggle" class="teacherToggle">I'm a teacher</label>
</div>



        <form id="studentForm" class="student-register-form" action="" method="post">
            <label for="studentid">Student ID (Format: 00-UR-0000)</label>
            <input type="text" placeholder="Student ID (e.g., 20-UR-0123)" id="studentid" name="studentid" required/>
            <h6 id="error-studentid" style="color: red; text-align: left; margin:0px"></h6>
            <label for="email">Email</label>
            <input type="email" placeholder="Email" id="email" name="email" required/>
            <h6 id="error-email" style="color: red; text-align: left; margin:0px"></h6>
            <label for="firstname">Name</label>
            <div class="name-inputs">
                <input type="text" placeholder="First Name" id="firstname" name="firstname" required class="fullname"/>
                <input type="text" placeholder="Last Name" name="lastname" required class="fullname"/>
            </div>
            <label for="course">Program:</label>
            <select id="course" name="course" required>
                <option value="BS Information Technology">BS Information Technology</option>
            <option value="BS Civil Engineering">BS Civil Engineering</option>
                <option value="BS Mechanical Engineering">BS Mechanical Engineering</option>
                <option value="BS Electrical Engineering">BS Electrical Engineering</option>
                <option value="BS Computer Engineering">BS Computer Engineering</option>
                <option value="BS Mathematics">BS Mathematics</option>
                <option value="BS Architecture">BS Architecture</option>
                <option value="AB English Language">AB English Language</option>
                <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
                <option value="Bachelor of Early Childhood Education">Bachelor of Early Childhood Education</option>
            
            </select>
            <label for="yearlevel">Year Level</label>
            <select name="yearlevel" id="yearlevel">
            <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
                <option value="5th Year">5th Year</option>
            </select>
            
            <label for="block">Block</label>
            <select name="block" id="block">
            <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
            </select>
            <label for="contactno">Contact Number</label>
            <input type="text" placeholder="Contact No." id="contactno" name="contactno" required/>
            <h6 id="error-cpn" style="color: red; text-align: left; margin:0px"></h6>
            
            <label for="password">Create Password</label>
        <div class="password-container">
            <input type="password" id="password" name="password" class="password-input" placeholder="Create Password">
            <span class="toggle-icon" onclick="togglePasswordVisibility('password', 'toggle-icon')"><img src="images/hidePass.png" alt="hidePassword" class="img-pw"></span>
        </div>
            <div class="password-requirements">
    <ul>
        <li class="title-pwReq">Your password must include:</li>
        <li id="uppercaseRequirement">At least one uppercase letter (A-Z)</li>
        <li id="lowercaseRequirement">At least one lowercase letter (a-z)</li>
        <li id="numberRequirement">At least one number (0-9)</li>
        <li id="specialCharRequirement">At least one special character (@, $, !, %, *, ?, &)</li>
        <li id="lengthRequirement">Minimum length of 6 characters</li>
    </ul>
</div>
            <label for="confirmpassword">Confirm Password</label>
        <div class="password-container">
            <input type="password" id="confirmpassword" name="confirmpassword" class="password-input" placeholder="Confirm Password">
            <span class="toggle-icon2" onclick="togglePasswordVisibility('confirmpassword', 'toggle-icon2')"><img src="images/hidePass.png" alt="hidePassword" class="img-pw"></span>
        </div>





            <h6 id="error-pw" style="color: red; text-align: left; margin:0px"></h6>
            <button type="submit" name="submitStudent">CREATE ACCOUNT</button>
            <span>Already have an account? <a href="login.php">Log in</a></span>
        </form>

        <form id="teacherForm" class="student-register-form" action="" method="post">
        <label for="empID">Employee ID (Format: URDA-0000)</label>
            <input type="text" placeholder="Employee ID (e.g., URDA-0123)" id="empID" name="empID" required/>
            <h6 id="terror-empID" style="color: red; text-align: left; margin:0px"></h6>
            <label for="temail">Email</label>
            <input type="email" placeholder="Email" id="temail" name="temail" required/>
            <h6 id="terror-email" style="color: red; text-align: left; margin:0px"></h6>
            <label for="tfirstname">Name</label>
            <div class="name-inputs">
                <input type="text" placeholder="First Name" id="tfirstname" name="tfirstname" required class="fullname"/>
                <input type="text" placeholder="Last Name" name="tlastname" required class="fullname"/>
            </div>
            <label for="department">College</label>
            <select id="department" name="department" required>
            <option value="College of Computing">College of Computing</option>
                <option value="College of Engineering and Architecture">College of Engineering and Architecture</option>
                <option value="College of Arts and Education">College of Arts and Education</option>
            
            </select>
            <label for="tcontactno">Contact Number</label>
            <input type="text" placeholder="Contact No." name="tcontactno" id="tcontactno" required/>
            <h6 id="terror-cpn" style="color: red; text-align: left; margin:0px"></h6>
            
            <label for="tpassword">Create your Password</label>
        <div class="password-container">
            <input type="password" id="tpassword" name="tpassword" class="password-input" placeholder="Create Password">
            <span class="toggle-icon3" onclick="togglePasswordVisibility('tpassword', 'toggle-icon3')"><img src="images/hidePass.png" alt="hidePassword" class="img-pw"></span>
        </div>


            <div class="password-requirements">
    <ul>
        <li class="ttitle-pwReq">Your password must include:</li>
        <li id="tuppercaseRequirement">At least one uppercase letter (A-Z)</li>
        <li id="tlowercaseRequirement">At least one lowercase letter (a-z)</li>
        <li id="tnumberRequirement">At least one number (0-9)</li>
        <li id="tspecialCharRequirement">At least one special character (@, $, !, %, *, ?, &)</li>
        <li id="tlengthRequirement">Minimum length of 6 characters</li>
    </ul>
</div>

            <label for="tconfirmpassword">Create your Password</label>
        <div class="password-container">
            <input type="password" id="tconfirmpassword" name="tconfirmpassword" class="password-input" placeholder="Confirm Password">
            <span class="toggle-icon4" onclick="togglePasswordVisibility('tconfirmpassword', 'toggle-icon4')"><img src="images/hidePass.png" alt="hidePassword" class="img-pw"></span>
        </div>




            <h6 id="terror-pw" style="color: red; text-align: left; margin:0px"></h6>
            <button type="submit" name="submitTeacher">CREATE ACCOUNT</button>
            <span>Already have an account? <a href="login.php">Log in</a></span>
        </form>
    </div><br> <hr>
    <script>
    function togglePasswordVisibility(inputId, iconClass) {
        let passwordInput = document.getElementById(inputId);
        let icon = document.querySelector('.' + iconClass);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.innerHTML = '<img src="images/seePass.png" alt="Show Password" class="img-pw">';
        } else {
            passwordInput.type = 'password';
            icon.innerHTML = '<img src="images/hidePass.png" alt="Hide Password" class="img-pw">';
        }
    }
</script>
</body>
</html>
