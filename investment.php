<?php
    require "config.php";

    if (empty($_SESSION["email"])) {
        header("Location: login.php");
        exit();
    }

    $userEmail = $_SESSION["email"];

    // Fetch user information
    $selectUserInfoQuery = "SELECT * FROM student_registration WHERE Student_email='$userEmail'";
    $resultUserInfo = mysqli_query($conn, $selectUserInfoQuery);
    $stud_id = "";
    if ($resultUserInfo && mysqli_num_rows($resultUserInfo) > 0) {
        $rowUserInfo = mysqli_fetch_assoc($resultUserInfo);
        $stud_id = $rowUserInfo['Student_ID'];
        $fname = $rowUserInfo['Student_fname'];
        $lname = $rowUserInfo['Student_lname'];
    }

    $selectedCompanyID = isset($_GET['Company_id']) ? $_GET['Company_id'] : null;

    // Fetch company information
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
    $companyName = "";
    $companyLogo = "";

    if ($hasCompany) {
        $row = mysqli_fetch_assoc($resultCompany);
        $companyName = $row["Company_name"];
        $companyLogo = $row["Company_logo"];
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Requests - Launchpad</title>
    <link rel="icon" href="/launchpad/images/favicon.svg" />
    <link rel="stylesheet" href="css/navbar.css">
    <!-- same style sa invitation -->
    <link rel="stylesheet" href="css/investorRequest.css"> 

    <style>
        .modal {
            display: none; 
            position: fixed;
            z-index: 1; 
            padding-top: 50px; /* LOCATION NG BOX */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4);
        }

        .modal::-webkit-scrollbar {
            display: none;
        }

        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: auto;
            bottom: 10px;
            padding: 30px 35px;
            border: 1px solid #888;
            border-radius: 40px;
            width: 45%;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
            -webkit-animation-name: animatetop;
            -webkit-animation-duration: 0.4s;
            animation-name: animatetop;
            animation-duration: 0.4s
        }

        @-webkit-keyframes animatetop {
            from {bottom:-300px; opacity:0}
            to {bottom:10px; opacity:1}
        }
        
        @keyframes animatetop {
            from {bottom:-300px; opacity:0}
            to {bottom:10px; opacity:1}
        }  
        
        /* The Close Button */
        .close {
            color: #333;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
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
                        <!-- <div class="notifNo" id="notifNo" aria-hidden="true"></div> -->
                    </span>
                </button>
            </a>
            <a href="investment.php" class="active">
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
                <?php foreach ($resultCompany as $row): ?>
                    <a href="company_view.php?Company_id=<?php echo $row['Company_ID']; ?>">
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

            <a href="profile.php" style="position: fixed; bottom: 0; background-color: white;">
                <button>
                    <span>
                        <div class="avatar2" id="initialsAvatar5"></div>
                        <span>Profile</span>
                    </span>
                </button>
            </a>

        </nav>


    </aside>

    <div class="content">
        <div class="containerin">
            <h2>Investment Requests</h2>

            <?php
            // $query = "SELECT * FROM investor_request INNER JOIN published_project ON published_project.PublishedProjectID = investor_request.PublishedProjectID INNER JOIN project ON published_project.Project_ID = project.Project_ID INNER JOIN company_registration ON project.Company_ID = company_registration.Company_ID WHERE company_registration.Company_ID = '$selectedCompanyID'";
            $query = "SELECT * FROM investor_request 
            INNER JOIN published_project ON published_project.PublishedProjectID = investor_request.PublishedProjectID 
            INNER JOIN project ON published_project.Project_ID = project.Project_ID 
            INNER JOIN company_registration ON project.Company_ID = company_registration.Company_ID 
            INNER JOIN student_registration ON company_registration.Student_ID = student_registration.Student_ID 
            WHERE company_registration.Student_ID = '$stud_id'";
            $result = mysqli_query($conn, $query);
            if (!$result) {
                die("Error in the SQL query: " . mysqli_error($conn));
            }
            if (mysqli_num_rows($result) < 1){
                echo "<br><br><br><br><br><br><br><br><br><br><p style='text-align: center;'>No investment requests at the moment.<p>";
                }

            while ($row = mysqli_fetch_assoc($result)) {
                $dateString = htmlspecialchars($row['Submission_date']);
                $dateTime = new DateTime($dateString);
                $formattedDate = $dateTime->format('F j, Y');
                
                echo "
                    <div class='invi-card'>
                        <span class='projectT'>" . htmlspecialchars($row['InvestorName']) . "</span>" .
                        "<span> Project: " . htmlspecialchars($row['Project_title']) . "</span>" .
                        "<span>" . $formattedDate . "</span>" .
                        "<span>
                              <!-- VIEW BUTTON -->
                                <button class='myBtn confirm-btn' data-investor-id='" . htmlspecialchars($row['InvestorRequestID']) . "'>
                                    View
                                </button>
                            
                        </span>
                    </div>
                ";
            }
            ?>

        </div>

        <div id="myModal" class="modal">
            <div class="modal-content">
                <!-- <span class="close">&times;</span> -->

            </div>
        </div>
    </div>


        <script>
            // JavaScript to set the initials
            document.addEventListener("DOMContentLoaded", function () {
                const firstName = "<?php echo $fname ?>"; // Replace with actual first name
                const lastName = "<?php echo $lname ?>"; // Replace with actual last name

                const initials = getInitials(firstName, lastName);
                document.getElementById("initialsAvatar5").innerText = initials;
            });

            // Function to get initials from first and last names
            function getInitials(firstName, lastName) {
                return (
                    (firstName ? firstName[0].toUpperCase() : "") +
                    (lastName ? lastName[0].toUpperCase() : "")
                );
            }

            var modal = document.getElementById("myModal");
            var buttons = document.querySelectorAll('.myBtn');
            var modalContent = document.querySelector(".modal-content");

            // Attach click event listeners to each "View" button
            buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                    // Access the InvestorID from the data attribute
                    var investorID = this.getAttribute('data-investor-id');

                    // Make an AJAX request to get investor details
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            var investorDetails = JSON.parse(xhr.responseText);
                            updateModalContent(investorDetails);
                            modal.style.display = "block";
                        }
                    };
                    xhr.open("GET", "get_investor_details.php?investorID=" + investorID, true);
                    xhr.send();
                });
            });

            // Function to update modal content with investor details
            function updateModalContent(investorDetails) {
                modalContent.innerHTML = `
                <span class="close" style="float: right;">&times;</span>
               
                <b style="font-size:20px; color:#006BB9">INVESTOR'S DETAILS</b> <br><br>
                    <p><b>Project:</b> ${investorDetails.Project_title}</p>
                    <p><b>Investor Name:</b> ${investorDetails.InvestorName}</p>
                    <!-- pag ito ginamit, mag oopen muna siya ng email app -->
                    <!--<p>Investor Email: <a href="mailto:${investorDetails.Email}">${investorDetails.Email}</a></p>-->
                    <!-- pag ito naman ginamit, mag oopen ng new link tas dun na mag eemail/galing ito sa stackoverflow/mas okay daw to -->
                    <p><b>Investor Email:</b> <a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=${investorDetails.Email}" target="_blank">${investorDetails.Email}</a></p>
                    <p><b>Source of income:</b> ${investorDetails.SourceofIncome}</p>
                    <p><b>Requested documents:</b><br><br> ${investorDetails.RequestedDocuments}</p>
                    <p><b>Identity proof:</b></p><br>
                    <img src='${investorDetails.IdentityProof}' width='100%'>
                `;

                // Attach event listener to the new close button
                var newCloseButton = modalContent.querySelector('.close');
                newCloseButton.addEventListener('click', function() {
                    modal.style.display = "none";
                });
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            };


            // When the user clicks anywhere outside of the modal, close it
            // window.onclick = function(event) {
            //     if (event.target == modal) {
            //         modal.style.display = "none";
            //     }
            // }
        </script>

</body>

</html>