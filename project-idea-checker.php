<?php
require "config.php";

if (empty($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
$userEmail = $_SESSION["email"];
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


require __DIR__ . '/vendor/autoload.php';
use Orhanerday\OpenAi\OpenAi;

$open_ai_key = "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";
$open_ai = new OpenAi($open_ai_key);
$projectTitle;
$response;
$percentage;
$category;
$resultTitles;
$boxshaa;
$categBG = "transparent";
$heightt = "700px";

if (isset($_POST['submitG'])) {

    $projectTitle = trim($_POST['projectTitle']);
    $projectDesc = trim($_POST['projectDesc']);
    if (empty($projectTitle) || empty($projectTitle)) {
        header("Location: project-idea-checker.php");
    }


    $categBG = "rgba(0, 0, 0, 0.5)";






    $complete = $open_ai->completion([
        'model' => 'text-davinci-003',
        'prompt' => <<<EOT
        The only response you will always display is the PERCENTAGE ONLY, then the explanation. NOTHING ELSE.
    If the project idea is similar to other applications or systems, frankly say it in the explanation and give low percentage.
    If the same prompt provide like earlier, do not change your response especially the percentage.
    Scan and assess how unique the startup project idea is among all the existing systems, applications, or startup projects on the web.
    Do not be biased, be frank. Be honest whether it is common or unique.
    If the project idea is similar to other applications or systems, frankly say it in the explanation that it is common and give low percentage.
    Be formal in writing, do not use 'I'. 
    It does not need to be real-time web scanning or access current databases.
    Just provide a general assessment based on my training data up to January 2022.
    Do not say 'I am unable to perform real-time web scanning, but I can offer a general assessment based on my training data up to January 2022.'
    If the title or description is too vague or just gibberish, it is automatically 0%, do not assess it and say that the user needs to add more context for clarity.
    Make sure the explanation complements the percentage.
    Format Instructions:
    First line: Display ONLY AND STRICTLY the percentage, nothing else. then line break.
    And on the last line: Provide a brief explanation.
    Sample:
    35.56%
    The startup project title "PikPok," a social media platform for creating, sharing, and discovering short videos, falls within a familiar idea category. While the platform may have unique features, the concept of a social media platform for short video content is relatively common in the startup landscape.
    This is what will you assess:
    Startup Project Title: $projectTitle
    Startup Project Description: $projectDesc
    EOT,
        'temperature' => 0.9,
        'max_tokens' => 150,
        'frequency_penalty' => 0,
        'presence_penalty' => 0.6,
    ]);

    $response = json_decode($complete, true);
    $boxshaa = "0px 4px 8px rgba(0, 0, 0, 0.1)";
    $heightt = "1700px";
    if (isset($response["choices"][0]["text"])) {
        $response = $response["choices"][0]["text"];
        $resultts = "Results";
        $string = $response;
        if (preg_match('/(\d+\.\d+%)\s*(.*)/', $string, $matches)) {
            $percentage = trim($matches[1]);
            $response = trim($matches[2]);
            preg_match('/(\d+(\.\d+)?)%/', $percentage, $matches) && $number = $matches[1];

            $dataPoints = array(
                array("label" => "Uniqueness", "y" => $number, "color" => "#FF9D00", "indexLabelFontColor" => "#FFFFFF"),
                array("label" => "Commonness", "y" => (100 - $number), "color" => "#006BB9", "indexLabelFontColor" => "#FFFFFF")
            );





            switch (true) {
                case ($number >= 0 && $number <= 25.99):
                    $category = "Common Concept";
                    break;
                case ($number >= 26 && $number <= 50.99):
                    $category = "Familiar Idea";
                    break;
                case ($number >= 51 && $number <= 75.99):
                    $category = "Average Approach";
                    $completee = $open_ai->completion([
                        'model' => 'text-davinci-003',
                        'prompt' => <<<EOT
                        Recommend 10 much better and proper titles for $projectTitle
                        EOT,
                        'temperature' => 0.9,
                        'max_tokens' => 150,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0.6,
                    ]);
                    $responseTitles = json_decode($completee, true);
                    $responseTitles = $responseTitles["choices"][0]["text"];
                    $resultTitles = "Suggested Project Titles for Your Startup Project Idea";
                    break;
                case ($number >= 76 && $number <= 89.99):
                    $category = "Innovative Solution";
                    $completee = $open_ai->completion([
                        'model' => 'text-davinci-003',
                        'prompt' => <<<EOT
                        Recommend 10 much better and proper titles for $projectTitle
                        EOT,
                        'temperature' => 0.9,
                        'max_tokens' => 150,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0.6,
                    ]);
                    $responseTitles = json_decode($completee, true);
                    $responseTitles = $responseTitles["choices"][0]["text"];
                    $resultTitles = "Suggested Project Titles for Your Startup Project Idea";
                    break;
                case ($number >= 90 && $number <= 100):
                    $category = "Trailblazing Idea!";
                    $completee = $open_ai->completion([
                        'model' => 'text-davinci-003',
                        'prompt' => <<<EOT
                        Recommend 10 much better and proper startup project titles for $projectTitle
                        EOT,
                        'temperature' => 0.9,
                        'max_tokens' => 150,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0.6,
                    ]);
                    $responseTitles = json_decode($completee, true);
                    $responseTitles = $responseTitles["choices"][0]["text"];
                    $resultTitles = "Suggested Project Titles for Your Startup Project Idea";
                    break;
                default:
                    $category = "Unknown Category"; 
            }


        } else {
            
            $percentage = "N/A";
            $category = "For better assistance, please provide more context. Thank you!";
            $response = "The title and description of this startup project are too vague and do not provide enough context. More detailed information is required to provide an accurate assessment.";
        }



    } else {
        $category = "For better assistance, please provide more context. Thank you!";
        $response = "The title and description of this startup project are too vague and do not provide enough context. More detailed information is required to provide an accurate assessment.";
    }


}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Idea Checker - Launchpad</title>
    <link rel="icon" href="/launchpad/images/favicon.svg" />
    <script>
        window.onload = function () {


            var chart = new CanvasJS.Chart("chartContainer", {
                theme: "light2",
                animationEnabled: true,
                title: {
                    text: "<?php echo $projectTitle . "'s Uniqueness" ?>"
                },
                data: [{
                    type: "pie",
                    indexLabel: "{y}",
                    yValueFormatString: "#,##0.00\"%\"",
                    indexLabelPlacement: "inside",
                    indexLabelFontSize: 18,
                    indexLabelFontWeight: "bolder",
                    showInLegend: true,
                    legendText: "{label}",
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }]

            });
            chart.render();

        }
    </script>
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        .content {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin-left: 300px;
            margin-right: 50px;
            margin-bottom: 50px;
            padding: 50px;
            height:
                <?php if (isset($heightt))
                    echo $heightt; ?>
            ;
        }

        .inputTitle {
            width: 100%;
            height: 50px;
            background-color: #ffffff00;
            border: 1px solid var(--pblue-color);
            font-family: inherit;
            font-size: 15px;
            padding: 0 16px;
            border-radius: 1.25rem;
            transition: all 0.375s;
            margin: 10px;
        }

        .inputTitle:hover {
            border: 1px solid var(--pyellow-color);
        }

        .generatebtn {
            width: 100%;
            height: 50px;
            cursor: pointer;
            width: 100%;
            padding: 0 16px;
            border-radius: 1.25rem;
            background: var(--pblue-color);
            color: #f9f9f9;
            border: 0;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            letter-spacing: 2px;
            transition: all 0.375s;
            margin-top: 5px;
        }


        .output-text {
            border-radius: 20px;
            padding: 20px;
        }

        .output-category {
            justify-content: center;
            font-size: large;
            font-weight: 700;
            background-color:  <?php if (isset($categBG))
                    echo $categBG; ?>;
            color: white;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
        }


        .custom-chart-container {
            height: 370px;
            width: 95%;
            margin: 20px;
        }

        .output-titles {
            text-align: left;
            border-radius: 20px;
            padding: 20px;
            white-space: break-spaces;
        }

        .output-titles > span {
            text-align: center;
            font-weight: 600;
            font-size: 20px;
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
            <a href="#" class="active">
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
                   
                                <img src="\launchpad\images\join-company-icon.png" alt="Join Company Icon" width="15px" height="15px" style="margin-left: 70px;">
                            
                </a></p>
                         
                
                <?php if ($hasCompany): ?>
                <?php foreach ($resultCompany as $row): ?>
                    <a href="company_view.php?Company_id=<?php echo $row['Company_ID']; ?>">
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
            <a href="profile.php"style="position: fixed; bottom: 0; background-color: white;">
                <button>
                    <span>
                    <div class="avatar2" id="initialsAvatar3"></div>
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
        <div>
            <h1>Startup Project Idea Checker</h1>
            <p>This will assess your project idea's uniqueness and help you make the best out of it.</p><br>
        </div>
        <div>
            <form action="" method="post">
                <div>
                    <label for="projectTitle" style="margin-left: 20px;">Project Title:</label><br>
                    <input class="inputTitle" type="text" name="projectTitle" id="projectTitle"
                        placeholder="Enter your project title" value="<?php if (isset($projectTitle))
                            echo $projectTitle; ?>" required>
                </div><br><br>
                <div>

                    <label for="projectDesc" style="margin-left: 20px">Project Description:</label><br>
                    <textarea class="inputTitle" name="projectDesc" id="projectDesc" cols="30" rows="50"
                        style="padding: 10px;" placeholder="Enter your project title's description" required><?php if (isset($projectDesc))
                            echo $projectDesc; ?></textarea>

                </div><br><br>
                <div>
                    <input type="submit" value="Evaluate my Project Idea" name="submitG" class="generatebtn">
                </div>
            </form>
        </div><br>
        <div>
            <h2>
                <?php if (isset($resultts))
                    echo $resultts; ?>
            </h2>

            <div id="chartContainer" class="custom-chart-container"></div><br>
            <div class="output-category">
                <?php if (isset($category))
                    echo "$category"; ?>
            </div><br>
            <div class="output-text">
                <?php if (isset($response))
                    echo "$response"; ?>

            </div><br>
            <div class="output-titles">
                <span>
                    <?php if (isset($resultTitles))
                        echo $resultTitles; ?>
                </span><br>
                <?php if (isset($responseTitles))
                    echo $responseTitles; ?>
            </div>
        </div>
        <br><br>
    </div><br><br>

    <!-- <script type="text/javascript">
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
            }, 0);

        }
        loadDoc();
    </script> -->
    <script>
        // JavaScript to set the initials
        document.addEventListener("DOMContentLoaded", function() {
            const firstName = "<?php echo $fname?>"; // Replace with actual first name
            const lastName = "<?php echo $lname?>"; // Replace with actual last name

            const initials = getInitials(firstName, lastName);
            document.getElementById("initialsAvatar3").innerText = initials;
        });

        // Function to get initials from first and last names
        function getInitials(firstName, lastName) {
            return (
                (firstName ? firstName[0].toUpperCase() : "") +
                (lastName ? lastName[0].toUpperCase() : "")
            );
        }
    </script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</body>

</html>