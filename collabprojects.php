<?php
require "config.php";

if (empty($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION["email"];
$select_user_info = "SELECT * FROM student_registration WHERE Student_email='$userEmail'";
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

//fetch user id

// $select_user_id = mysqli_query($conn, "SELECT Student_ID FROM student_registration WHERE Student_email='$userEmail'");
// if (mysqli_num_rows($select_user_id) > 0) {
//     $row = mysqli_fetch_assoc($select_user_id);
//     $userID = $row['Student_ID'];
// }

$checkCompanyQuery = "SELECT c.*, s.Student_ID 
                        FROM company_registration c
                        INNER JOIN student_registration s ON c.Student_ID = s.Student_ID
                        WHERE s.Student_email = '$userEmail'";

$resultCompany = mysqli_query($conn, $checkCompanyQuery);

$companies = [];

while ($row = mysqli_fetch_assoc($resultCompany)) {
    $companies[] = $row;
}

$selectedCompanyID = isset($_GET['Company_id']) ? $_GET['Company_id'] : null;


$hasCompany = count($companies) > 0;
$companyID = "";
$companyName = "";
$companyLogo = "";

if ($selectedCompanyID) {
    $selectedCompanyQuery = "SELECT * FROM company_registration WHERE Company_ID = ?";
    $stmt = mysqli_prepare($conn, $selectedCompanyQuery);
    mysqli_stmt_bind_param($stmt, "i", $selectedCompanyID);
    mysqli_stmt_execute($stmt);
    $resultSelectedCompany = mysqli_stmt_get_result($stmt);

    if ($resultSelectedCompany) {
        $row = mysqli_fetch_assoc($resultSelectedCompany);
        $companyID = $row["Company_ID"];
        $companyName = $row["Company_name"];
        $companyLogo = $row["Company_logo"];
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collab Project</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/company.css">
    <style>
        .container {
            margin-top: 30px;
            margin-left: 300px;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <header class="sidebar-header">
            <img src="\launchpad\images\logo-text.svg" class="logo-img">
        </header>

        <nav>
            <a href="index.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\home-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Home</span>
                    </span>
                </button>
            </a>
            <a href="project-idea-checker.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\project-checker-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Project Idea Checker</span>
                    </span>
                </button>
            </a>
            <a href="invitations.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\invitation-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Invitations</span>
                    </span>
                </button>
            </a>
            <a href="investment.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\iconinvestment.png" alt="home-logo" class="logo-ic"></i>
                        <span>Investment Requests</span>
                    </span>
                </button>
            </a>
            <a href="collabprojects.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\iconpuzzle.png" alt="home-logo" class="logo-ic"></i>
                        <span>Collab Projects</span>
                    </span>
                </button>
            </a>
            <p class="divider-company">YOUR COMPANY<a href="create-company.php" style="text-decoration: none;">

                    <img src="\launchpad\images\join-company-icon.png" alt="Join Company Icon" width="15px"
                        height="15px" style="margin-left: 70px;">

                </a></p>
            <?php if ($hasCompany): ?>
                <?php foreach ($companies as $row): ?>
                    <?php if ($row['Company_ID'] == $selectedCompanyID): ?>
                        <a class="active" href="company_view.php?Company_id=<?php echo $row['Company_ID']; ?>">
                        <?php else: ?>
                            <a href="company_view.php?Company_id=<?php echo $row['Company_ID']; ?>">
                            <?php endif; ?>
                            <button>
                                <span class="<?php echo 'btn-company-created'; ?>">
                                    <div class="circle-avatar">
                                        <?php if (!empty($row['Company_logo'])): ?>
                                            <img src="\launchpad\<?php echo $row['Company_logo']; ?>" alt="Company Logo"
                                                class="img-company">
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

                <!-- <p class="divider-company">COMPANIES YOU'VE JOINED</p>
                    <a href="#">
                    <button>
                        <span  class="btn-join-company">
                            <i > <div class="circle-avatar">
                                <img src="\launchpad\images\join-company-icon.png" alt="">
                            </div></i>
                            <span class="join-company-text">Join companies</span>
                        </span>
                    </button>
                    </a> -->
                <a href="profile.php" style="position: fixed; bottom: 0; background-color: white;">
                    <button>
                        <span>
                            <div class="avatar2" id="initialsAvatar6"></div>
                            <span>Profile</span>
                        </span>
                    </button>
                </a>
        </nav>
    </aside>
    <?php
    $select_user_projects = mysqli_query($conn, "SELECT project.Project_ID, company_registration.Company_name, project.Project_title, project.Project_date
        FROM project
        INNER JOIN project_member ON project.Project_ID = project_member.Project_ID
        INNER JOIN company_registration ON project.Company_ID = company_registration.Company_ID
        WHERE project_member.Student_ID = '$stud_id' ORDER BY project.Project_date DESC, project.Project_title ASC;
        ");
 
    if (mysqli_num_rows($select_user_projects) > 0) {
        // while ($row = mysqli_fetch_assoc($select_user_projects)) {
        //     // $company_id = $row['Company_ID'];
        //     // $project_title = $row['Project_title'];
        //     // $project_date = $row['Project_date'];
        // }
    }

    ?>


    <div class="content2">

        <div class="search-bar">
            <input type="text" id="projectSearch" name="projectSearch"
                placeholder="Search collab projects..."><span class="forspace"></span>
        </div>
    </div>
   
    <div class="content">

    <?php

if (mysqli_num_rows($select_user_projects) < 1){
    echo "<br><br><br><br><br><br><br><br><br><br><p style='text-align: center;'>No collab projects at the moment.<p>";
    }
    ?>

        <?php while ($row = mysqli_fetch_assoc($select_user_projects)): ?>
            <a href="collab-projectView.php?project_id=<?php echo $row['Project_ID']; ?>" class="project-card">
                <div class="status-badge">Company:
                    <?php echo $row['Company_name'] ?>
                </div>
                <div>
                    <div class="project-title">
                        <?php echo $row['Project_title']; ?>
                    </div>
                    <div class="project-date">
                        <?php


                        date_default_timezone_set('Asia/Manila');

                        $projectDate = new DateTime($row['Project_date']);
                        $currentDate = new DateTime();
                        $timeElapsed = $currentDate->getTimestamp() - $projectDate->getTimestamp();

                        if ($timeElapsed < 60) {
                            echo 'created Just Now';
                        } elseif ($timeElapsed < 3600) {
                            $minutes = floor($timeElapsed / 60);
                            echo 'created ' . (($minutes == 1) ? '1 min ago' : $minutes . ' mins ago');
                        } elseif ($timeElapsed < 86400) {
                            $hours = floor($timeElapsed / 3600);
                            echo 'created ' . (($hours == 1) ? '1 hr ago' : $hours . ' hrs ago');
                        } elseif ($timeElapsed < 604800) {
                            $days = floor($timeElapsed / 86400);
                            echo 'created ' . (($days == 1) ? '1 day ago' : $days . ' days ago');
                        } elseif ($timeElapsed < 1209600) {
                            echo 'created 1 week ago';
                        } elseif ($timeElapsed < 1814400) {
                            echo 'created 2 weeks ago';
                        } elseif ($timeElapsed < 2419200) {
                            echo 'created 3 weeks ago';
                        } else {
                            echo 'created on ' . $projectDate->format('j M Y, g:i a');
                        }
                        ?>  

                    </div>
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
        document.addEventListener("DOMContentLoaded", function () {
            const firstName = "<?php echo $fname ?>"; // Replace with actual first name
            const lastName = "<?php echo $lname ?>"; // Replace with actual last name

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