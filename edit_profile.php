<?php
require "config.php";

if (empty($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION["email"];
if (isset($userEmail)) {
    $checkCompanyQuery = "SELECT c.*, s.Student_ID 
                        FROM company_registration c
                        INNER JOIN student_registration s ON c.Student_ID = s.Student_ID
                        WHERE s.Student_email = '$userEmail'";
} else {
    $checkCompanyQuery = "SELECT * FROM company_registration WHERE Company_ID = '$selectedCompanyID'";
}


$resultCompany = mysqli_query($conn, $checkCompanyQuery);

$hasCompany = mysqli_num_rows($resultCompany) > 0;
$companyID = "";
$companyName = "";
$companyLogo = "";

if ($hasCompany) {
    $row = mysqli_fetch_assoc($resultCompany); 
    $companyID = $row["Company_ID"];
    $companyName = $row["Company_name"];
    $companyLogo = $row["Company_logo"]; 
}

$projectQuery = "SELECT * FROM project WHERE Company_ID = '$companyID' ORDER BY Project_date DESC";
$resultProjects = mysqli_query($conn, $projectQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Launchpad</title>
		<link rel="icon" href="/launchpad/images/favicon.svg" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/editprof.css">
</head>
<body>

<aside class="sidebar">
            <header class="sidebar-header">
                <img src="\launchpad\images\logo-text.svg" class="logo-img">
            </header>

            <nav>
                <a href="index.php" >
                <button>
                    <span>
                        <i ><img src="\launchpad\images\home-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Home</span>
                    </span>
                </button>
            </a>
            <a href="project-idea-checker.php">
                <button>
                    <span>
                        <i ><img src="\launchpad\images\project-checker-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Project Idea Checker</span>
                    </span>
                </button>
    </a>
    <a href="invitations.php">
                <button>
                    <span>
                        <i ><img src="\launchpad\images\invitation-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Invitations</span>
                    </span>
                </button>
    </a>
                <p class="divider-company">YOUR COMPANY<a href="create-company.php" style="text-decoration: none;">
                   
                   <img src="\launchpad\images\join-company-icon.png" alt="Join Company Icon" width="15px" height="15px" style="margin-left: 70px;">
               
   </a></p>
                <?php if ($hasCompany): ?>
                <?php foreach ($resultCompany as $row): ?>
                    <a href="company_view.php?Company_id=<?php echo $row['Company_ID']; ?>">
                        <button>
                            <span class="<?php echo 'btn-company-created'; ?>">
                                <div class="circle-avatar">
                                    <?php if (!empty($row['Company_logo'])): ?>
                                        <img src="\launchpad\<?php echo $row['Company_logo']; ?>" alt="Company Logo" class="img-company">
                                    <?php endif; ?>
                                </div>
                                <span class="create-company-text">
                                    <?php echo $row['Company_name']; ?>
                                </span>
                            </span>
                        </button>
                    </a>
                <?php endforeach; ?>
                <?php endif; ?> 
                <p class="divider-company">COMPANIES YOU'VE JOINED</p>
                <a href="#">
                <button>
                    <span  class="btn-join-company">
                        <i > <div class="circle-avatar">
                            <img src="\launchpad\images\join-company-icon.png" alt="">
                        </div></i>
                        <span class="join-company-text">Join companies</span>
                    </span>
                </button>
                </a>
<a href="#" class="active">
                <button>
                    <span>
                    <div class="avatar2" id="initialsAvatar7"></div>
                        <span>Profile</span>
                    </span>
                </button>
</a>
               
            </nav>


        </aside>

        <?php 
        $email = $_SESSION['email'];

        $select_user_info = "SELECT * FROM student_registration WHERE Student_email='$email'";
        $result_user_info = mysqli_query($conn, $select_user_info);
        if ($result_user_info) {
            if (mysqli_num_rows($result_user_info) > 0) {
                $row = mysqli_fetch_assoc($result_user_info);
                $stud_id = $row['Student_ID'];
                $fname = $row['Student_fname'];
                $lname = $row['Student_lname'];
                $course = $row['Course'];
                $year = $row['Year'];
                $block = $row['Block'];
                $contactNo = $row['Student_contactno'];
            }
        }

    $email = $_SESSION['email'];

        $select_user_info = "SELECT * FROM student_registration WHERE Student_email='$email'";
        $result_user_info = mysqli_query($conn, $select_user_info);
        if ($result_user_info) {
            if (mysqli_num_rows($result_user_info) > 0) {
                $row = mysqli_fetch_assoc($result_user_info);
                $stud_id = $row['Student_ID'];
                $fname = $row['Student_fname'];
                $lname = $row['Student_lname'];
                $course = $row['Course'];
                $year = $row['Year'];
                $block = $row['Block'];
                $contactNo = $row['Student_contactno'];
                $emailValue = $email;
                $password = $row['Student_password'];
            }
        }
    if (isset($_POST['btnEditProfile'])) {
        $new_fname = $_POST['new_student_fname'];
        $new_lname = $_POST['new_student_lname'];
        $new_course = $_POST['new_course'];
        $new_year = $_POST['new_yearlevel'];
        $new_block = $_POST['new_block'];
        $new_contact = $_POST['new_student_contactno'];


        $update_info = "UPDATE student_registration SET Student_fname='$new_fname', Student_lname='$new_lname', Course='$new_course', Year='$new_year', Block='$new_block', Student_contactno='$new_contact' WHERE Student_email='$email'";

        if (mysqli_query($conn, $update_info)) {
            echo "<script>alert('Updated the profile successfully');</script>";
        }else {
            echo "<script>alert('Error in updating');</script>";
        }
    }

    if (isset($_POST['btnEditCredentials'])) {
        $old_pass = $_POST['password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        $select_login_info = "SELECT Student_email, Student_password FROM student_registration WHERE Student_email='$email'";

        $result_login_info = mysqli_query($conn, $select_login_info);
        if ($result_login_info) {
            if (mysqli_num_rows($result_login_info) > 0) {
                $row = mysqli_fetch_assoc($result_login_info);
                
                if ($old_pass == $row['Student_password']) {
                    if ($new_pass == $confirm_pass) {
                        
                        $update_login = "UPDATE student_registration
                        SET  Student_password = '$new_pass'
                        WHERE Student_email = '$email'";

                        if (mysqli_query($conn, $update_login)) {
                            echo "<script>alert('Updated the Login credential successfully');</script>";
                        }
                    }else {
                        echo "<script>alert('New password does match');</script>";
                    }
                }else {
                    echo "<script>alert('Old password does not match');</script>";
                }
            }
        }
    }
?>
<div class="content">
    <br><br>
    <div class="editpro">
    <a href="profile.php">Back</a>
    <header><h1>Edit my Profile</h1></header>

    <form action="" method="post">
            <label for="new_studentid">Student ID:</label>
            <input type="text" name="new_studentid" value="<?php echo $stud_id?>" required readonly>
<br><br>
            <label for="new_student_fname">First Name:</label>
            <input type="text" name="new_student_fname" value="<?php echo $fname?>" required>
<br><br>
            <label for="new_student_lname">Last Name:</label>
            <input type="text" name="new_student_lname" value="<?php echo $lname?>" required>
<br><br>
            <label for="course">Program</label>
            <select id="course" name="new_course" required>
                <option value="BS Information Technology" <?php if(isset($course) && $course=="BS Information Technology"){ echo "selected"; }?>>BS Information Technology</option>
            <option value="BS Civil Engineering" <?php if(isset($course) && $course=="BS Civil Engineering"){ echo "selected"; }?>>BS Civil Engineering</option>
                <option value="BS Mechanical Engineering" <?php if(isset($course) && $course=="BS Mechanical Engineering"){ echo "selected"; }?>>BS Mechanical Engineering</option>
                <option value="BS Electrical Engineering" <?php if(isset($course) && $course=="BS Electrical Engineering"){ echo "selected"; }?>>BS Electrical Engineering</option>
                <option value="BS Computer Engineering" <?php if(isset($course) && $course=="BS Computer Engineering"){ echo "selected"; }?>>BS Computer Engineering</option>
                <option value="BS Mathematics" <?php if(isset($course) && $course=="BS Mathematics"){ echo "selected"; }?>>BS Mathematics</option>
                <option value="BS Architecture" <?php if(isset($course) && $course=="BS Architecture"){ echo "selected"; }?>>BS Architecture</option>
                <option value="AB English Language" <?php if(isset($course) && $course=="AB English Language"){ echo "selected"; }?>>AB English Language</option>
                <option value="Bachelor of Secondary Education" <?php if(isset($course) && $course=="Bachelor of Secondary Education"){ echo "selected"; }?>>Bachelor of Secondary Education</option>
                <option value="Bachelor of Early Childhood Education" <?php if(isset($course) && $course=="Bachelor of Early Childhood Education"){ echo "selected"; }?>>Bachelor of Early Childhood Education</option>
            
            </select>
            <label for="yearlevel">Year Level</label>
            <select name="new_yearlevel" id="yearlevel">
                <option value="1st Year" <?php if(isset($year) && $year=="1st Year"){ echo "selected"; }?>>1st Year</option>
                <option value="2nd Year" <?php if(isset($year) && $year=="2nd Year"){ echo "selected"; }?>>2nd Year</option>
                <option value="3rd Year" <?php if(isset($year) && $year=="3rd Year"){ echo "selected"; }?>>3rd Year</option>
                <option value="4th Year" <?php if(isset($year) && $year=="4th Year"){ echo "selected"; }?>>4th Year</option>
                <option value="5th Year" <?php if(isset($year) && $year=="5th Year"){ echo "selected"; }?>>5th Year</option>
            </select>
            
            <label for="block">Block</label>
            <select name="new_block" id="block">
                <option value="A" <?php if(isset($block) && $block=="A"){ echo "selected"; }?>>A</option>
                <option value="B" <?php if(isset($block) && $block=="B"){ echo "selected"; }?>>B</option>
                <option value="C" <?php if(isset($block) && $block=="C"){ echo "selected"; }?>>C</option>
                <option value="D" <?php if(isset($block) && $block=="D"){ echo "selected"; }?>>D</option>
                <option value="E" <?php if(isset($block) && $block=="E"){ echo "selected"; }?>>E</option>
            </select>
<br><br>
            <label for="new_student_contactno">Contact Number:</label>
            <input type="tel" name="new_student_contactno" value="<?php echo $contactNo?>" required>
<br><br>
            <button name="btnEditProfile" type="submit">Save Changes</button>
        </form>
        <br><br><br>
        <hr>
<br>
        <h1>Change Password</h1>
<br>
<form action="" method="post">
            <label for="password">Type your old password: </label>
            <input type="password" name="password" id="password" required>
       <br>  <br>   
            <label for="new_password">Type your new password: </label>
            <input type="password" name="new_password" id="new_password" required>
<br><br>
            <label for="confirm_password">Confirm you password: </label>
            <input type="password" name="confirm_password" id="confirm_password" required>
<br><br>
            <button name="btnEditCredentials" type="submit">Save Changes</button>
        </form>
    </div>
    </div>
</div>
    
<script>
        // JavaScript to set the initials
        document.addEventListener("DOMContentLoaded", function() {
            const firstName = "<?php echo $fname?>"; // Replace with actual first name
            const lastName = "<?php echo $lname?>"; // Replace with actual last name

            const initials = getInitials(firstName, lastName);
            document.getElementById("initialsAvatar7").innerText = initials;
        });

        // Function to get initials from first and last names
        function getInitials(firstName, lastName) {
            return (
                (firstName ? firstName[0].toUpperCase() : "") +
                (lastName ? lastName[0].toUpperCase() : "")
            );
        }
    </script>
</body>
</html>