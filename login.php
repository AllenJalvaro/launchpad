<?php

require "config.php";

if (!empty($_SESSION["email"])) {
    if ($_SESSION["user_type"] === "teacher") {
        header("Location: teacher-dashboard.php");
        exit();
    } elseif ($_SESSION["user_type"] === "student") {
        header("Location: index.php");
        exit();
    }
    exit();
}


if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];


    $checkTeacherQuery = "SELECT * FROM instructor_registration WHERE Instructor_email = '$email' AND Instructor_password = '$password'";
    $resultTeacher = mysqli_query($conn, $checkTeacherQuery);


    $checkStudentQuery = "SELECT * FROM student_registration WHERE Student_email = '$email' AND Student_password = '$password'";
    $resultStudent = mysqli_query($conn, $checkStudentQuery);

    if (mysqli_num_rows($resultTeacher) > 0) {

        $row = mysqli_fetch_assoc($resultTeacher);
        $_SESSION["email"] = $row["Instructor_email"];
        $_SESSION["user_type"] = "teacher";
        header("Location: teacher-dashboard.php");
    } elseif (mysqli_num_rows($resultStudent) > 0) {

        $row = mysqli_fetch_assoc($resultStudent);
        $_SESSION["email"] = $row["Student_email"];
        $_SESSION["user_type"] = "student";
        header("Location: index.php");
    } else {

        $errorMessage = "Invalid username or password";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Launchpad - Login</title>
		<link rel="icon" href="/launchpad/images/favicon.svg" />
		<link rel="stylesheet" href="css/login.css">
	</head>
	<body>
		<div class="login-card">
			
            <a href="landingpage.php" ><h2><img src="/launchpad/images/logo.png" alt="launchpad-logo" class="login-img"/></a><span>OG</span
				><span class="in">IN</span>
			</h2>
			<h5>Enter your credentials as either a student or mentor affiliated exclusively with PSU.</h5>
			
			<!-- <button class="btn-continue-google">
				<img src="/launchpad/images/google-logo.png" alt="google-logo" class="login-img"/>
				Continue with Google
			</button>
				<span class="or">or</span> -->
				<?php if (isset($errorMessage)) : ?>
            <div class="alertm">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
			<form class="login-form" action="" method="post" id="loginForm">
				<input type="email" placeholder="Email" name="email" required/>
				
                <div class="password-container">
                <input type="password" placeholder="Password" name="password" id="password" class="password-input" required/>
            <span class="toggle-icon" onclick="togglePasswordVisibility('password', 'toggle-icon')"><img src="images/hidePass.png" alt="hidePassword" class="img-pw"></span>
        </div>
                
                
				
				<button type="submit" name="login">LOGIN</button>
				<span>Don't have an account? <a href="registration.php">Register Now</a></span>
			</form>
		</div>
        <script>
    function togglePasswordVisibility(inputId, iconClass) {
        const passwordInput = document.getElementById(inputId);
        const icon = document.querySelector('.' + iconClass);

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
