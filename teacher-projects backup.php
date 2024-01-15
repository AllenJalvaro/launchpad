<?php
    require "config.php";

    if (empty($_SESSION["email"])) {
        header("Location: login.php");
        exit(); 
    }
    $instructorEmail = $_SESSION["email"];
    $query = "SELECT Instructor_fname, Instructor_ID from instructor_registration where instructor_email='$instructorEmail'";
    $result = mysqli_query($conn, $query);
    $fname;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $fname = $row['Instructor_fname'];
            $instructor_Id = $row['Instructor_ID'];
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/company.css">
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
            <a href="teacher-projects.php" class="active">
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
            <a href="teacher-profile.php" style="position: fixed; bottom: 0; background-color: white;">
                <button>
                    <span>
                        <div class="avatar2" id="initialsAvatar4"></div>
                        <span>Profile</span>
                    </span>
                </button>
            </a>

        </nav>


    </aside>

    <?php
        $select_mentor_project = mysqli_query($conn, "SELECT project.Project_ID, company_registration.Company_name, project.Project_title, project.Project_date
        FROM project
        INNER JOIN project_mentor ON project.Project_ID = project_mentor.Project_ID
        INNER JOIN company_registration ON project.Company_ID = company_registration.Company_ID
        WHERE project_mentor.Mentor_ID = $instructor_Id");

                
        if (mysqli_num_rows($select_mentor_project) > 0 ) {
        }        

    ?>
    <div class="content2">
                        
    <div class="search-bar">
    <input type="text" id="projectSearch" name="projectSearch" placeholder="Search any <?php echo $fname ?>'s projects"><span class="forspace"></span><span> <a href="#"><img src="images/options.png" alt="options-icon" height="30px"></a></span> 
    </div>
        </div>
    <div class="content">

    <?php while ($row = mysqli_fetch_assoc($select_mentor_project)) : ?>
    <a href="teacher_projectView.php?project_id=<?php echo $row['Project_ID']; ?>" class="project-card">
    <div class="status-badge">Company: <?php echo $row['Company_name'] ?></div>
    <div>
    <div class="project-title"><?php echo $row['Project_title']; ?></div>
    <div class="project-date">Date created: <?php echo date('m-d-y g:i A', strtotime($row['Project_date'])); ?></div>
    </div>
    </a>
    <?php endwhile; ?>

    </div>
    <script>
    $(document).ready(function () {
    var originalContent = $(".content").html();

    $("#projectSearch").on("input", function () {
    var searchTerm = $(this).val();

    if (searchTerm.length > 0) {
    $.ajax({
        type: "POST",
        url: "search-collab-project.php",
        data: { searchTerm: searchTerm, stud_id: <?php echo $stud_id; ?> },
        success: function (response) {
            $(".content").empty(); // Clear existing content
            $(".project-card").hide();
            $(".project-card2").hide();
            $(response).appendTo(".content");
        }
    });
    } else {
    // Restore the original content
    $(".content").html(originalContent);
    $(".project-card").show();
    $(".project-card2").show();
    }
    });
    });
    </script>
    <script>
    // JavaScript to set the initials
    document.addEventListener("DOMContentLoaded", function() {
    const firstName = "<?php echo $fname?>"; // Replace with actual first name
    const lastName = "<?php echo $lname?>"; // Replace with actual last name

    const initials = getInitials(firstName, lastName);
    document.getElementById("initialsAvatar6").innerText = initials;
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