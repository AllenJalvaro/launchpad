<?php
    require "config.php";

    if (empty($_SESSION["email"])) {
        header("Location: login.php");
        exit(); 
    }
    $instructorEmail = $_SESSION["email"];
    $query = "SELECT * FROM instructor_registration WHERE Instructor_email='$instructorEmail'";
    $result = mysqli_query($conn, $query);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $instructor_Id = $row['Instructor_ID'];
            $employee_id = $row['empID'];
            $fname = $row['Instructor_fname'];
            $lname = $row['Instructor_lname'];
            $department = $row['Department'];
            $instructor_contact = $row['Instructor_contactno'];
        }
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/profile.css">
    <title>Mentored Project</title>
    <link rel="icon" href="/launchpad/images/favicon.svg" />
</head>
<body>
<aside class="sidebar">
        <header class="sidebar-header">
            <img src="\launchpad\images\logo-text.svg" class="logo-img">
        </header>

        <nav>
            <a href="teacher-dashboard.php" >
                <button>
                    <span>
                        <i><img src="\launchpad\images\home-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Home</span>
                    </span>
                </button>
            </a>
            <a href="teacher-projects.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\iconmentor.png" alt="home-logo" class="logo-ic"></i>
                        <span>My Mentored Projects</span>
                    </span>
                </button>
            </a>
            <a href="teacher-evaluation.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\evaluationicon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Projects for Evaluation</span>
                    </span>
                </button>
            </a>
            
            
            <br><br>
            <a href="teacher-profile.php" style="position: fixed; bottom: 0; background-color: white;" class="active">
                <button>
                    <span>
                        <div class="avatar2" id="initialsAvatar4"></div>
                        <span>Profile</span>
                    </span>
                </button>
            </a>

        </nav>


    </aside>

    <div class="content">        
        <div class="profile-card">
        <header>
            <h1>Hello, Instructor <b> <?php echo $fname?><b>!</h1><br>
            
        </header>

        <div class="form-container">

            <div class="avatar" id="initialsAvatar"></div><br><br>
            <!-- <button class="buttonEdit"><a href="edit_profile.php" class="editp">Edit Profile</a></button><br><br> -->
            <form action="" method="post">
                <label for="studentid">Teacher ID:</label>
                <input type="text" id="studentid" name="studentid" value="<?php echo $instructor_Id?>" required readonly><br>

                <label for="student_fname">First Name:</label>
                <input type="text" id="student_fname" name="student_fname" value="<?php echo $fname?>" required readonly><br>

                <label for="student_lname">Last Name:</label>
                <input type="text" id="student_lname" name="student_lname" value="<?php echo $lname?>" required readonly><br>

                <label for="course">Department: </label>
                <input type="text" id="course" name="course" value="<?php echo $department?>" required readonly> <br>

                <label for="student_contactno">Contact Number:</label>
                <input type="tel" id="student_contactno" name="student_contactno" value="<?php echo $instructor_contact?>" required readonly> <br>

            </form>
        </div>
        <a id="buttonLogout" href="logout.php" class="editp">
                    <button>
                        <span>
                            <i ><img src="\launchpad\images\logout-icon.png" alt="logout-icon" class="logo-ic" ></i>
                            <span idstyle="color: red">LOG OUT</span>
                        </span>
                    </button>
                </a>
    </div>

    <script>
    // JavaScript to set the initials
    document.addEventListener("DOMContentLoaded", function() {
    const firstName = "<?php echo $fname?>"; // Replace with actual first name
    const lastName = "<?php echo $lname?>"; // Replace with actual last name

    const initials = getInitials(firstName, lastName);
    document.getElementById("initialsAvatar4").innerText = initials;
    });

    // Function to get initials from first and last names
    function getInitials(firstName, lastName) {
    return (
    (firstName ? firstName[0].toUpperCase() : "") +
    (lastName ? lastName[0].toUpperCase() : "")
    );
    }
    </script>
    <script>
        // JavaScript to set the initials
        document.addEventListener("DOMContentLoaded", function() {
            const firstName = "<?php echo $fname?>"; // Replace with actual first name
            const lastName = "<?php echo $lname?>"; // Replace with actual last name

            const initials = getInitials(firstName, lastName);
            document.getElementById("initialsAvatar").innerText = initials;
            document.getElementById("initialsAvatar2").innerText = initials;
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