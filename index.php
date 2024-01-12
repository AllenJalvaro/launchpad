<?php
require "config.php";

if (empty($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION["email"];
$selectedCompanyID = isset($_GET['Company_id']) ? $_GET['Company_id'] : null;

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
// else{
//     header("Location: index.php");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/navbar.css">
    <title>Home - Launchpad</title>
    <link rel="icon" href="/launchpad/images/favicon.svg" />
    <style>
        .logo {
            width: 200px;
            height: auto;
            display: block;
            margin: 20px auto;
        }

        .project-card:hover {
            box-shadow: 0 0 15px rgba(3, 33, 81, 0.402);
        }

        .content2 {
            margin-top: 30px;
            margin-left: 300px;
        }

        .content {
            margin-top: 20px;
            margin-left: 270px;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .project-card:active {
            transform: scale(0.98);
            box-shadow: none;
        }

        .project-card {
            position: relative;
            margin: 0;
            flex: 0 0 calc(33.33% - 20px);
            max-width: calc(33.33% - 20px);
            box-sizing: border-box;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        p {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }

        .logo {
            width: 200px;
            height: auto;
            display: block;
            margin: 20px auto;
        }

        .profile-info {
            display: flex;
            align-items: center;
            margin-top: 10px;

        }


        .profile-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 10px;
            box-shadow: 0 0 15px rgba(3, 33, 81, 0.202);
        }

        .profile {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .project-card:hover {
            box-shadow: 0 0 15px rgba(3, 33, 81, 0.402);
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <header class="sidebar-header">
            <img src="\launchpad\images\logo-text.svg" class="logo-img">
        </header>

        <nav>
            <a href="#" class="active">
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


            <!-- <p class="divider-company">COMPANIES YOU'VE JOINED</p>
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
            </a> -->
            <br><br>
            <a href="profile.php" style="position: fixed; bottom: 0; background-color: white;">
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
        <br>
        <h1>
            Latest Projects
        </h1>
    </div>

    <div class="content">







        <!-- 

        <div class="project-card">
            <div class="container">
                <img src="/launchpad/images/m1.jpg" alt="MindfulMood Logo" class="logo">
                <h1>AdventureTracker</h1>
                <p>Mar 15, 2023</p>
                <p>AdventureTracker is a mobile app designed for outdoor enthusiasts, providing real-time tracking,
                    route planning, and personalized adventure statistics.</p>
                <div class="profile-info">
                    <div class="profile-circle">
                        <img src="/launchpad/images/l1.jpg" class="profile" alt="Developer Profile">
                    </div>
                    <p>TrailBlaze Tech</p>
                </div>
            </div>
        </div> -->
        <?php


        $query = "SELECT 
    ide.Project_logo, 
    pro.Project_title, 
    pub.Published_date, 
    pro.Project_Description, 
    com.Company_logo, 
    com.Company_name 
FROM 
    published_project pub
LEFT JOIN 
    project pro ON pro.Project_ID = pub.Project_ID
LEFT JOIN 
    company_registration com ON com.Company_ID = pro.Company_ID
LEFT JOIN 
    ideation_phase ide ON ide.Project_ID = pub.Project_ID order by 
    pub.Published_date desc;";

        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $projectLogo = $row['Project_logo'];
                $projectTitle = $row['Project_title'];
                $publishedDate = $row['Published_date'];
                $projectDescription = $row['Project_Description'];
                $companyLogo = $row['Company_logo'];
                $companyName = $row['Company_name'];

              
                $cutDescription = implode(' ', array_slice(str_word_count($projectDescription, 2), 0, 20));

                
                echo '<div class="project-card">';
                echo '<div class="container">';
                echo '<img src="' . $projectLogo . '" class="logo">';
                echo '<h1>' . $projectTitle . '</h1>';
                echo '<p>' . $publishedDate . '</p>';
                echo '<p>' . $cutDescription . '...</p>';
                echo '<div class="profile-info">';
                echo '<div class="profile-circle">';
                echo '<img src="' . $companyLogo . '">';
                echo '</div>';
                echo '<p>Company Name: ' . $companyName . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "No records found";
        }

        $conn->close();

        ?>


    </div>
    <script>
        // JavaScript to set the initials
        document.addEventListener("DOMContentLoaded", function () {
            const firstName = "<?php echo $fname ?>"; // Replace with actual first name
            const lastName = "<?php echo $lname ?>"; // Replace with actual last name

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
</body>

</html>