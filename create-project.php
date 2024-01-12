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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/create_project.css">
    <style>
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
    <form method="post" action="process_create_project.php?Company_id=<?php echo $_GET['Company_id']; ?>">

      
    <h2>Create New Project</h2> <br>
        <label for="projectName">Project Name:</label>
        <input type="text" id="projectName" name="projectName" required><br><br>

        <label for="projectDescription">Project Description:</label>
        <textarea  id="projectDescription" name="projectDescription" rows="10" required></textarea><br><br>

        <label for="memberSearch">Add Members:</label>
        <div class="search-container">
        <i class="fas fa-search search-icon" style="color: #006BB9;"></i>
        <input class="search-input" type="text" id="memberSearch" oninput="searchMembers(this.value)" placeholder="Search members...">
    </div>
        <br>
        <div id="memberResults" class="search-results" ></div><div id="selectedMembers" class="color-selected" >
        </div>
<br>
    
        <label for="mentorSearch">Add Mentor:</label>
        <div class="search-container">
        <i class="fas fa-search search-icon" style="color: #006BB9;"></i>
        <input class="search-input" type="text" id="mentorSearch" oninput="searchMentors(this.value)" placeholder="Search mentor...">
    </div>
        <br> <div id="mentorResults" class="search-results"></div>
        <div id="selectedMentor"  class="color-selected">
        
        </div><br>

        <label for="evaluatorSearch">Add Evaluators:</label>
        
        <div class="search-container">
        <i class="fas fa-search search-icon" style="color: #006BB9;"></i>
        <input class="search-input" type="text" id="evaluatorSearch" oninput="searchEvaluators(this.value)" placeholder="Search evaluators...">
    </div>

        
        <br>
        <div id="evaluatorResults" class="search-results"></div>
        <div id="selectedEvaluators" class="color-selected">
        </div>
     <br><br>
        <button id="submit-btn" type="submit">Create Project</button>
    </form>
</div>

<script>
 function searchEvaluators(query) {
    if (query.trim() === '') {
        $('#evaluatorResults').html('').removeAttr('style');
        return;
    }

    $.ajax({
        url: 'search_evaluators.php',
        type: 'POST',
        data: { query: query },
        success: function (data) {
            $('#evaluatorResults').html(data).css({
                'cursor': 'pointer',
                'border-radius': '10px',
                'background-color': 'transparent',
            });
            attachClickHandlers('evaluator');
        }
    });
}




   function searchMembers(query) {
    if (query.trim() === '') {
        $('#memberResults').html('').removeAttr('style'); 
        
        return;
    }

    $.ajax({
        url: 'search_members.php',
        type: 'POST',
        data: { query: query },
        success: function (data) {
$('#memberResults').html(data).css({
                'cursor': 'pointer',
                
                'border-radius': '10px',

                'background-color': 'transparent',
            });
            attachClickHandlers('member');
            
            
        }
    });
}


    function searchMentors(query) {
        if (query.trim() === '') {
        $('#mentorResults').html(''); 
        return;
    }
        $.ajax({
            url: 'search_mentors.php',
            type: 'POST',
            data: { query: query },
            success: function (data) {
                $('#mentorResults').html(data).css({
                'cursor': 'pointer',
                
                'border-radius': '10px',

                'background-color': 'transparent',
            });
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
            } else if(type === 'mentor'){
                addMentor(id, name);
            } else if(type === 'evaluator'){
                addEvaluator(id, name);
            }
        });
    }
    function addEvaluator(evaluatorID, evaluatorName) {
        if ($('#selectedEvaluators').find(`[data-id="${evaluatorID}"]`).length === 0) {
            $('#selectedEvaluators').append(`<div data-id="${evaluatorID}">${evaluatorName} <span onclick="removeEvaluator('${evaluatorID}')">x</span></div>`);
        }
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
    function removeEvaluator(evaluatorID) {
        
        $(`#selectedEvaluators [data-id="${evaluatorID}"]`).remove();
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