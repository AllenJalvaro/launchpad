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



$viewedProj = isset($_GET['project_id']) ? $_GET['project_id'] : null;

    if ($viewedProj == null)  {
        header("Location: index.php");
    }

    $fetchPubProj = "SELECT * FROM published_project INNER JOIN project ON published_project.Project_ID = project.Project_ID INNER JOIN ideation_phase ON ideation_phase.Project_ID = published_project.Project_ID INNER JOIN company_registration ON project.Company_ID = company_registration.Company_ID WHERE published_project.PublishedProjectID = '$viewedProj'";

    $resultProj = mysqli_query($conn, $fetchPubProj);

    $hasProj = mysqli_num_rows($resultProj) > 0;
    $projPubDate = "";  
    $projTitle = "";
    $projLogo = "";
    $projCat = "";
    $projDesc = "";
    $compName = "";
    $compLogo = "";

    if ($hasProj) {
        $row = mysqli_fetch_assoc($resultProj); 
        $projPubDate = $row['Published_date'];
        $projTitle = $row['Project_title'];
        $projLogo = $row['Project_logo'];
        $projDesc = $row['Project_Description'];
        $compName = $row['Company_name'];
        $compLogo = $row['Company_logo'];
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .content {
            margin-top: 20px;
            margin-left: 270px;
            padding: 20px 60px;
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
        .container h1 {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
        .top-content {
            display: flex;
            justify-content: space-between;
        }

        .top-content img {
            width: 45px;
            height: 45px;
            border-radius: 100px;
        }

        .top-content .right {
            display: flex;
            align-items: center;
        }
        .top-content .right h3 {
            font-size: 18px;
            margin-top: 8px;
            margin-right: 10px;
        }
        .viewed-project .vimg {
            width: 100%;
            min-height: 300px;
            max-height: 300px;
            object-fit: cover;
            border-radius: 30px;
        }
        .viewed-project .vdesc {
            margin: 20px 0;
            font-size: 17px;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <header class="sidebar-header">
            <img src="\launchpad\images\logo-text.svg" class="logo-img">
        </header>

        <nav>
            <a href="index.php" class="active">
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

    <div class="content">
    <div class="viewed-project">
    <a href="index.php"
                            style="text-decoration:none; color:#006BB9;" title="Back"><i class="fas fa-angle-left"
                                style="font-size: 40px;"></i>

                        </a>
            <p class="vdate"><?php echo date("F j, Y", strtotime($projPubDate));?></p>
            <div class="top-content">
                <h1 class="vtitle"><?php echo $projTitle; ?></h1>
                <div class="right">
                    <h3 class="vcompName"><?php echo $compName; ?></h3>
                    <img class="vcompImg" src="<?php echo $compLogo; ?>" alt="">
                </div>
            </div>
            <p class="vcategory"></p>

            <img src="<?php echo $projLogo; ?>" alt="" class="vimg">
            <p class="vdesc"><?php echo $projDesc; ?></p>
                
        </div>
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