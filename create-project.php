<?php
    require "config.php";

    if (empty($_SESSION["email"])) {
        header("Location: login.php");
        exit();
    }

    $userEmail = $_SESSION["email"];

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

    $projectQuery = "SELECT * FROM project WHERE Company_ID = '$companyID' ORDER BY Project_date DESC";
    $resultProjects = mysqli_query($conn, $projectQuery);

    // echo "<script>alert('COMPANY ID: $selectedCompanyID')</script>";
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $hasCompany && !empty($companyName) ? $companyName." - Launchpad" : 'Create Company - Launchpad'; ?></title> 
    <link rel="icon" href="/launchpad/images/favicon.ico" id="favicon">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/create_project.css">
    <style>
        .color-selected{
            color: green;
            font-weight: 700;
        }
    </style>
    <script>
        function changeFavicon(url) {
            const favicon = document.getElementById('favicon');
            favicon.href = url;
        }
        <?php if ($hasCompany && !empty($companyLogo)): ?>
            const companyLogoUrl = "/launchpad/<?php echo $companyLogo; ?>";
            changeFavicon(companyLogoUrl);
        <?php endif; ?>
    </script>
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
    <a href="invitations.php" >
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
<a href="profile.php">
                <button>
                    <span>
                    <div class="avatar2" id="initialsAvatar2"></div>
                        <span>Profile</span>
                    </span>
                </button>
</a>
               
            </nav>


        </aside>

<div class="content">
    <h2>Create Project</h2> 
    <form method="post" action="process_create_project.php?Company_id=<?php echo $_GET['Company_id']; ?>">

      
        <label for="projectName">Project Name:</label>
        <input type="text" id="projectName" name="projectName" required><br><br>

        <label for="projectDescription">Project Description:</label>
        <textarea id="projectDescription" name="projectDescription" required></textarea><br><br>
<hr><br>
      
        <label for="memberSearch">Add Members:</label><br><br>
        <input type="text" id="memberSearch" oninput="searchMembers(this.value)"><br>
        <div id="memberResults" class="search-results"></div><br>
        <h4>Selected Members</h4>
 
        <div id="selectedMembers" class="color-selected">
            
          
        </div><br><br>
<hr><br>
    
        <label for="mentorSearch">Add Mentor:</label><br><br>
        <input type="text" id="mentorSearch" oninput="searchMentors(this.value)"><br>
        <div id="mentorResults" class="search-results"></div><br>
        <h4>Selected Mentor</h4>
       
        <div id="selectedMentor"  class="color-selected">
        
        </div><br>

     
        <button id="submit-btn" type="submit">Create Project</button>
    </form>
</div>

<script>
    function searchMembers(query) {
        $.ajax({
            url: 'search_members.php',
            type: 'POST',
            data: { query: query },
            success: function (data) {
                $('#memberResults').html(data);
                attachClickHandlers('member');
            }
        });
    }

    function searchMentors(query) {
        $.ajax({
            url: 'search_mentors.php',
            type: 'POST',
            data: { query: query },
            success: function (data) {
                $('#mentorResults').html(data);
                attachClickHandlers('mentor');
            }
        });
    }

    function attachClickHandlers(type) {
        $(`.search-results .${type}-result`).click(function () {
            const id = $(this).data('id');
            const name = $(this).text(); 

            if (type === 'member') {
                addMember(id, name);
            } else {
                addMentor(id, name);
            }
        });
    }

    function addMember(studentID, studentName) {
        if ($('#selectedMembers').find(`[data-id="${studentID}"]`).length === 0) {
            $('#selectedMembers').append(`<div data-id="${studentID}">${studentName} <span onclick="removeMember('${studentID}')">x</span></div>`);
        }
    }

    function addMentor(mentorID, mentorName) {
        $('#selectedMentor').html(`<div data-id="${mentorID}">${mentorName} <span onclick="removeMentor('${mentorID}')">x</span></div>`);
    }

    function removeMember(studentID) {
        $(`#selectedMembers [data-id="${studentID}"]`).remove();
    }

    function removeMentor(mentorID) {
        $('#selectedMentor').empty();
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