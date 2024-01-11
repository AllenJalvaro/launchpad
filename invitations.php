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

if ($resultUserInfo && mysqli_num_rows($resultUserInfo) > 0) {
    $rowUserInfo = mysqli_fetch_assoc($resultUserInfo);
    $stud_id = $rowUserInfo['Student_ID'];
    $fname = $rowUserInfo['Student_fname'];
    $lname = $rowUserInfo['Student_lname'];
    $course = $rowUserInfo['Course'];
    $year = $rowUserInfo['Year'];
    $block = $rowUserInfo['Block'];
    $contactNo = $rowUserInfo['Student_contactno'];
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

<!-- Rest of your HTML content -->


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitations - Launchpad</title>
    <link rel="icon" href="/launchpad/images/favicon.svg" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/invitation.css">
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
            <a href="#" class="active">
                <button>
                    <span>
                        <i><img src="\launchpad\images\invitation-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Invitations</span>
                        <div class="notifNo" id="notifNo" aria-hidden="true"></div>
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

            <p class="divider-company">COMPANIES YOU'VE JOINED</p>
            <a href="#">
                <button>
                    <span class="btn-join-company">
                        <i>
                            <div class="circle-avatar">
                                <img src="\launchpad\images\join-company-icon.png" alt="">
                            </div>
                        </i>
                        <span class="join-company-text">Join companies</span>
                    </span>
                </button>
            </a>
            <a href="profile.php">
                <button>
                    <span>
                        <div class="avatar2" id="initialsAvatar5"></div>
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

    <div class="content">
        <div class="containerin">
            <h2>Startup Project Invitations</h2>

            <?php
            $query = "SELECT 
            project.Project_title, 
            CONCAT(owner.Student_fname, ' ', owner.Student_lname) AS owner_name,
            project.Project_date,
            invitation.invitationID,
            invitation.invitationDate 
          FROM invitation
          INNER JOIN project ON invitation.projectID = project.Project_ID 
          INNER JOIN student_registration ON invitation.inviteeID = student_registration.Student_ID 
          INNER JOIN company_registration ON project.Company_ID = company_registration.Company_ID
          INNER JOIN student_registration AS owner ON company_registration.Student_ID = owner.Student_ID where student_registration.student_email = '$userEmail'
          ORDER BY invitation.invitationDate DESC";

            $result = mysqli_query($conn, $query);

            



            if (!$result) {
                die("Error in the SQL query: " . mysqli_error($conn));
            }

            while ($row = mysqli_fetch_assoc($result)) {
                $dateString = htmlspecialchars($row['Project_date']);
                $dateTime = new DateTime($dateString);
                $formattedDate = $dateTime->format('m-d-y g:i A');
                echo "
                    <div class='invi-card'>
                        <span class='projectT'>" . htmlspecialchars($row['Project_title']) . "</span>" .
                        "<span> Creator: " . htmlspecialchars($row['owner_name']) . "</span>" .
                        "<span>" . $formattedDate . "</span>" .
                        "<span>
                            <button class='confirm-btn' onclick='confirm(" . $row['invitationID'] . ")'>Confirm</button>
                            <button class='delete-btn' onclick='deleteInvitation(" . $row['invitationID'] . ")'>Delete</button>
                        </span>
                    </div>
                ";

            }
            ?>
        </div>




        <script type="text/javascript">
            function loadDoc() {

                setInterval(function () {
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            document.getElementById("notifNo").innerHTML = this.responseText;
                        }
                    };
                    xhttp.open("GET", "invitations-notifno.php", true);
                    xhttp.send();
                }, 1);

            }
            loadDoc();
        </script>
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
        </script>

</body>

</html>