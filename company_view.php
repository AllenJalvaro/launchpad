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

    $projectQuery = "SELECT * FROM project WHERE Company_ID = '$selectedCompanyID' ORDER BY Project_date DESC";
    $resultProjects = mysqli_query($conn, $projectQuery);
    $copid = $_GET['Company_id'];
    $_SESSION['copid'] = $copid; 
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
        <link rel="stylesheet" href="css/company.css">
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
                    <a href="index.php">
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
                        <div class="avatar2" id="initialsAvatar6"></div>
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
    ?> 
            <div class="content2">
                       
                       <div class="search-bar">
                   <input type="text" id="projectSearch" name="projectSearch" placeholder="Search any <?php echo $companyName ?>'s projects"><span class="forspace"></span><span> <a href="#"><img src="images/options.png" alt="options-icon" height="30px"></a></span> 
               </div>
                           <h1><?php echo $companyName ?>'s projects </h1>
                       </div>
                <div class="content">
                <a href="create-project.php?Company_id=<?php echo $_GET['Company_id']; ?>" class="project-card2">   
                  
        
            <br><br>
            <img src="images/add-company-icon.png" alt="add-icon" width="30px">
            <h3>Create new project</h3>
        </a>

        <?php while ($row = mysqli_fetch_assoc($resultProjects)) : ?>
    <a href="project.php?project_id=<?php echo $row['Project_ID']; ?>" class="project-card">
        <div class="status-badge">Phase 1 on progress</div>
        <div>
            <div class="project-title"><?php echo $row['Project_title']; ?></div>
            <div class="project-date">Date created: <?php echo date('m-d-y g:i A', strtotime($row['Project_date'])); ?></div>
        </div>
    </a>
<?php endwhile; ?>

        <script>
   $(document).ready(function() {
    // Store the original content
    var originalContent = $(".content").html();

    $("#projectSearch").on("input", function() {
        var searchTerm = $(this).val();

        if (searchTerm.length > 0) {
            $.ajax({
                type: "POST",
                url: "search-projects.php",
                data: { searchTerm: searchTerm, companyID: <?php echo $selectedCompanyID; ?> },
                success: function(response) {
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
    </div>
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