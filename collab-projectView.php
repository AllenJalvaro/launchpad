<?php
require "config.php";

if (empty($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION["email"];
$project_id = $_GET['project_id'];
$_SESSION['projecid'] = $project_id;
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
    <title>
        Project - Launchpad
    </title>
    <link rel="icon" href="images/favicon.svg">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <link rel="stylesheet" href="css/company.css">
    <link rel="stylesheet" href="css/timeline.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/projectView.css">



    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }


        .back-button {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            /* Customize the color */
            margin: 5;
        }

        .back-icon {
            width: 20px;
            height: 20px;
            margin-right: 5px;
        }

        .container {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
        }

        .process-wrapper {
            width: 85%;
            margin: auto;
        }

        #progress-bar-container ul {
            display: flex;
            list-style: none;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        #progress-bar-container li {
            flex: 1;
            text-align: center;
            color: #aaa;
            font-size: 15px;
            cursor: pointer;
            font-weight: 700;
            position: relative;
            /*originally RELATIVE*/
        }

        #progress-content-section {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: transparent;
            /* BACKGROUND COLOR TRANSPARENT */
        }

        .section-content form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        label {
            font-size: 24px;
            font-weight: 700;
        }

        input,
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .saveBtn {

            background-color: #006BB9;
            color: #ffffff;

        }

        .saveBtn:hover {
            background-color: #1b6499;
        }

        .mentorBtn {
            background-color: transparent;
            color: #006BB9;
            border: 1px solid #006BB9;
        }


        .mentorBtn:hover {
            background-color: #006cb956;
        }



        /* .img-container { */
        /* width: 250px; */
        /* Adjust the width to your preference */
        /* height: 250px; */
        /* Adjust the height to your preference */
        /* overflow: hidden; */
        /* Ensure the image doesn't overflow the container */
        /* position: relative; */
        /* Add this if you want to center the image inside the box */
        /* } */

        /* .img-container img { */
        /* width: 100%; */
        /* Make the image fill the container */
        /* height: auto; */
        /* Maintain the image's aspect ratio */
        /* display: block; */
        /* Remove extra space below the image */
        /* } */

        embed {
            border: 1px solid gray;
            height: 560px;
            width: 100%;
            border-radius: 20px;
        }

        .phaseSection {
            background-color: white;
            padding: 50px;
            border-radius: 30px;
            margin-bottom: 30px;
            text-align: center;
        }

        .sectionTitle {
            color: #006BB9 !important;
            font-weight: bold;
            font-size: 25px !important;
            margin: 0;
        }

        .projectOverviewDirection,
        .projectOverviewDirection2 {
            text-align: left;
            font-size: 14px !important;
        }

        .projectOverview-textarea {
            background-color: transparent;
            margin-top: 30px;
            resize: none;
            border-radius: 10px;
            padding: 20px !important;
        }

        .feedbackTitle {
            margin: 20px 0px;
            font-weight: bold;
            text-align: left;
            color: #093a5d;
        }

        .feedbackSection {
            height: 150px;
            overflow: auto;
            padding: 10px;
            font-size: 11px;
            text-align: left;
        }

        .feedbackBlock {
            margin-bottom: 10px;
            border-bottom: 1px solid #e6e6e6;
            padding-bottom: 5px;
        }

        .feedback-info {
            margin-bottom: 5px;
        }

        .commenter {
            font-weight: bold;
            color: black;
        }

        .feedbackdate {
            color: gray;
            margin-left: 5px;
        }

        .feedbackContent {
            font-size: 13px !important;
            color: black;
            margin-top: 2px;
            text-align: left;
        }

        .projectOverview-textarea::-webkit-scrollbar {
            width: 12px;
        }

        .projectOverview-textarea::-webkit-scrollbar-thumb {
            background-color: #006cb94e;
            border-radius: 6px;
        }

        .projectOverview-textarea::-webkit-scrollbar-track {
            background-color: #006cb929;
        }

        .feedbackSection::-webkit-scrollbar {
            width: 12px;
        }

        .feedbackSection::-webkit-scrollbar-thumb {
            background-color: #006cb94e;
            border-radius: 6px;
        }

        .feedbackSection::-webkit-scrollbar-track {
            background-color: #006cb929;
        }

        .fileInput {
            display: none;
        }

        .fileInputLabel {
            display: inline-block;
            padding: 12px 20px;
            border: 2px solid #006BB9;
            border-radius: 5px;
            background-color: transparent;
            color: #006BB9;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            margin-bottom: 15px;
        }

        .fileInputLabel:hover {
            background-color: #006BB9;
            color: #fff;
        }

        .fileInput[type="file"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
            width: 100%;
            height: 100%;
        }

        .content {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .projectMenu {
            display: flex;
            align-items: center;
        }

        .projectMenu a {
            margin: 0 20px;
            text-decoration: none;
            color: #006BB9;
            font-size: 13px !important;
        }


        .deleteProjectBTN:hover,
        .editProjectBTN:hover,
        .ViewTeamBTN:hover {
            background: #1591fd23;
            border-radius: 10px;
        }

        .editProjectBTN,
        .ViewTeamBTN {
            color: #006BB9;
            background: none;
            border: none;
            padding: 10px;
            margin-right: 10px !important;
            font-size: 13px;
            font-family: inherit;
            cursor: pointer;
            outline: none;
        }

        .deleteProjectBTN {
            color: #cc0000;
            background: none;
            border: none;
            padding: 10px;
            margin: 0;
            font-size: 13px;
            font-family: inherit;
            cursor: pointer;
            outline: none;
        }

        .image-preview {
            width: 320px;
            /* Adjust the width as needed */
            height: 320px;
            /* Adjust the height as needed */
            border: 1px solid #ccc;
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;

        }

        .pdf-preview {
            padding: 10px;
            width: 100%;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .pdf-preview2 {
            padding: 10px;
            width: 100%;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            /* Preserve aspect ratio while covering the entire container */
        }

        .cancelUpdateLogo {
            cursor: pointer;
            margin: 0;
            color: gray;
            display: none;
        }

        .cancelUpdateLogo:hover {
            color: #cc0000;
        }

        .cancelUpdateCanvas {
            cursor: pointer;
            margin: 0;
            color: gray;
            display: none;
        }

        .cancelUpdateCanvas:hover {
            color: #cc0000;
        }

        .name-holder {
            border: 1px solid #093a5d;
            color: #093a5d;
            border-radius: 10px;
            padding: 10px;
            margin: 5px;
            display: inline-block;
        }
    </style>
</head>

<body>


    <?php



    if (isset($_POST['btnSave'])) {
        $projectOverv = trim($_POST['project_overview']);
        $count_ideation_phase_query = "SELECT COUNT(ideation_phase.IdeationID) AS Count 
FROM ideation_phase 
INNER JOIN project ON ideation_phase.Project_ID=project.Project_ID 
WHERE project.Project_ID=$project_id;";

        $count_ideation_phase_result = mysqli_query($conn, $count_ideation_phase_query);

        function uploadProjectLogo()
        {
            $targetDir = "images/";
            $timestamp = time();
            $targetFile = $targetDir . $timestamp . '_' . basename($_FILES["project_logo"]["name"]);
            move_uploaded_file($_FILES["project_logo"]["tmp_name"], $targetFile);
            return $targetFile;
        }
        function uploadPdfFile()
        {
            $targetDir = "pdf/";
            $timestamp = time();
            $targetFile = $targetDir . $timestamp . '_' . basename($_FILES["canvas_file"]["name"]);
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            if ($fileType !== "pdf") {
                echo "<script>
                Swal.fire({
                  icon: 'error',
                  title: 'Invalid File Type',
                  text: 'Please upload a PDF file.',
                });
              </script>";
                return false;
            }
            move_uploaded_file($_FILES["canvas_file"]["tmp_name"], $targetFile);
            return $targetFile;
        }



        if (mysqli_num_rows($count_ideation_phase_result) > 0) {
            $row = mysqli_fetch_assoc($count_ideation_phase_result);
            $count = $row['Count'];






            if ($count > 0) {


                if ($_FILES["project_logo"]["error"] == 0) {
                    $newProjectLogo = uploadProjectLogo();
                } else {
                    $selectLogo = mysqli_query($conn, "SELECT Project_logo FROM ideation_phase WHERE project_id='$project_id'");
                    if (mysqli_num_rows($selectLogo) > 0) {
                        $row = mysqli_fetch_assoc($selectLogo);
                        $newProjectLogo = $row['Project_logo'];
                    }
                }
                if ($_FILES["canvas_file"]["error"] == 0) {
                    $newModelCanvas = uploadPdfFile();
                } else {
                    $selectCanvas = mysqli_query($conn, "SELECT Project_Modelcanvas FROM ideation_phase WHERE project_id='$project_id'");
                    if (mysqli_num_rows($selectCanvas) > 0) {
                        $row = mysqli_fetch_assoc($selectCanvas);
                        $newModelCanvas = $row['Project_Modelcanvas'];
                    }
                }

                $updateQuery = "UPDATE ideation_phase SET 
                Project_Overview='$projectOverv',
                Project_logo='$newProjectLogo',
                Project_Modelcanvas='$newModelCanvas'
                WHERE project_id='$project_id'";

                if (mysqli_query($conn, $updateQuery)) {
                    echo "<script>
                Swal.fire({
                    title: 'Changes saved successfully!',
                    text: '',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2000, 
                }).then(function() {
                    window.location.href = 'collab-projectView.php?project_id=" . $_SESSION['projecid'] . "';
                });
            </script>";
                } else {
                    echo '<script type="text/javascript">';
                    echo 'swal("Error!", "Error updating record: ' . mysqli_error($conn) . '", "error");';
                    echo '</script>';
                }



            } else {  //INSERT NEW PROJECT TO IDEATION PHASE 
                if ($_FILES["project_logo"]["error"] == 0 && $_FILES["project_logo"]["tmp_name"] !== null) {
                    $ProjectLogo = uploadProjectLogo();
                }else{
                    $ProjectLogo = "";
                }
                
                if ($_FILES["canvas_file"]["error"] == 0 && $_FILES["canvas_file"]["tmp_name"] !== null) {
                    $ModelCanvas = uploadPdfFile();
                }else{
                    $ModelCanvas ="";
                }
                
                $insertQuery = "INSERT INTO ideation_phase (`Project_ID`, `Project_logo`, `Project_Overview`, `Project_Modelcanvas`) 
                VALUES ('$project_id', '$ProjectLogo', '$projectOverv', '$ModelCanvas')";



                if (mysqli_query($conn, $insertQuery)) {
                    echo "<script>
                            Swal.fire({
                                title: 'Your work has been saved successfully!',
                                text: '',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000, 
                            }).then(function() {
                                window.location.href = 'collab-projectView.php?project_id=" . $_SESSION['projecid'] . "';
                            });
                            </script>";
                } else {
                    echo "<script>
                            Swal.fire({
                                title: 'Error:'" . mysqli_error($conn) . ",
                                text: '',
                                icon: 'error',
                                showConfirmButton: true,
                            });
                            </script>
                    
                    ";
                }

            }
        }



    }
    //evaluate
    if (isset($_POST['evalBtn'])) {

    }
    //save PITCHING PHASE
    
    if (isset($_POST['btnSavePitch'])) {

        function uploadVideoPitch()
        {
            $targetDir = "videos/";
            $timestamp = time();
            $targetFile = $targetDir . $timestamp . '_' . basename($_FILES["video_pitch"]["name"]);

            move_uploaded_file($_FILES["video_pitch"]["tmp_name"], $targetFile);

            return $targetFile;
        }
        function uploadPitchDeck()
        {
            $targetDir = "pdf/";
            $timestamp = time();
            $targetFile = $targetDir . $timestamp . '_' . basename($_FILES["canvas_file2"]["name"]);
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            if ($fileType !== "pdf") {
                echo "<script>
            Swal.fire({
              icon: 'error',
              title: 'Invalid File Type',
              text: 'Please upload a PDF file.',
            });
          </script>";
                return false;
            }
            move_uploaded_file($_FILES["canvas_file2"]["tmp_name"], $targetFile);
            return $targetFile;
        }



        $count_pitching_phase_query = "SELECT COUNT(pitching_phase.pitchingID) AS Count 
FROM pitching_phase 
INNER JOIN project ON pitching_phase.Project_ID=project.Project_ID 
WHERE project.Project_ID=$project_id;";

        $count_pitching_phase_result = mysqli_query($conn, $count_pitching_phase_query);






        if (mysqli_num_rows($count_pitching_phase_result) > 0) {

            $row = mysqli_fetch_assoc($count_pitching_phase_result);
            $count = $row['Count'];






            if ($count > 0) {


                // Video Pitch
                if ($_FILES["video_pitch"]["error"] == 0 && is_uploaded_file($_FILES["video_pitch"]["tmp_name"])) {
                    $newVideoPitch = uploadVideoPitch();
                } else {
                    $selectVideoPitch = mysqli_query($conn, "SELECT VideoPitch FROM pitching_phase WHERE project_id='$project_id'");
                    if (mysqli_num_rows($selectVideoPitch) > 0) {
                        $row = mysqli_fetch_assoc($selectVideoPitch);
                        $newVideoPitch = $row['VideoPitch'];
                    }
                }

                // Pitch Deck
                if ($_FILES["canvas_file2"]["error"] == 0 && is_uploaded_file($_FILES["canvas_file2"]["tmp_name"])) {
                    $newPitchDeck = uploadPitchDeck();
                } else {
                    $selectPitchDeck = mysqli_query($conn, "SELECT PitchDeck FROM pitching_phase WHERE project_id='$project_id'");
                    if (mysqli_num_rows($selectPitchDeck) > 0) {
                        $row = mysqli_fetch_assoc($selectPitchDeck);
                        $newPitchDeck = $row['PitchDeck'];
                    }
                }


                $updateQuery = "UPDATE pitching_phase SET 
            VideoPitch='$newVideoPitch',
            PitchDeck='$newPitchDeck'
            WHERE project_id='$project_id'";

                if (mysqli_query($conn, $updateQuery)) {
                    echo "<script>
            Swal.fire({
                title: 'Changes saved successfully!',
                text: '',
                icon: 'success',
                showConfirmButton: false,
                timer: 2000, 
            }).then(function() {
                window.location.href = 'collab-projectView.php?project_id=" . $_SESSION['projecid'] . "';
            });
        </script>";
                } else {
                    echo '<script type="text/javascript">';
                    echo 'swal("Error!", "Error updating record: ' . mysqli_error($conn) . '", "error");';
                    echo '</script>';
                }



            } else {  //INSERT NEW PROJECT TO PITCHING  PHASE 
                if ($_FILES["video_pitch"]["error"] == 0) {
                    $VideoPitch = uploadVideoPitch();
                }
                if ($_FILES["canvas_file2"]["error"] == 0) {
                    $PitchDeck = uploadPitchDeck();
                }

                $insertQuery = "INSERT INTO pitching_phase (`Project_ID`, `VideoPitch`, `PitchDeck`) 
            VALUES ('$project_id', '$VideoPitch', '$PitchDeck')";



                if (mysqli_query($conn, $insertQuery)) {
                    echo "<script>
                        Swal.fire({
                            title: 'Your work has been saved successfully!',
                            text: '',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000, 
                        }).then(function() {
                            window.location.href = 'collab-projectView.php?project_id=" . $_SESSION['projecid'] . "';
                        });
                        </script>";
                } else {
                    echo "<script>
                        Swal.fire({
                            title: 'Error:'" . mysqli_error($conn) . ",
                            text: '',
                            icon: 'error',
                            showConfirmButton: true,
                        });
                        </script>
                
                ";
                }

            }
        }



    }
    ?>

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
            </a> <a href="investment.php">
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
                                                                                                        <?php echo $row['Company_name'];
                                                                                                        $companyName = $row['Company_name']; ?>
                                                                                                    </span>
                                                                                                </span>
                                                                                            </button>
                                                                                        </a>
                                                    <?php endforeach; ?>
                <?php endif; ?>
                <br><br>
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
                <a href="profile.php" style="position: fixed; bottom: 0; background-color: white;">
                    <button>
                        <span>
                            <div class="avatar2" id="initialsAvatar9"></div>
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
    <?php
    if (isset($_GET['project_id'])) {
        //PROJECTID
        $project_id = $_GET['project_id'];
        // echo "<h1>".$project_id."</h1>";
    
        $selectProjectInfo = mysqli_query($conn, "SELECT * FROM project WHERE Project_ID = $project_id");

        if (mysqli_num_rows($selectProjectInfo) > 0) {
            $row = mysqli_fetch_assoc($selectProjectInfo);
            $project_name = $row['Project_title'];
        }

        ?>
                                        <?php
                                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                            if (isset($_POST['deleteProject'])) {
                                                echo "
            <script>
                Swal.fire({
                    title: 'Are you sure to delete this project?',
                    text: 'Deleting the project cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        popup: 'popupSwal',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: 'deleteProject.php',
                            data: { projecid: " . $_SESSION['projecid'] . " },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Project deleted successfully!',
                                    text: '',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 2000,
                                }).then(function () {
                                    window.location.href = 'company_view.php?Company_id=" . $_SESSION['copid'] . "';
                                });
                            }
                        });
                    }
                });
            </script>
            ";
                                            } else if (isset($_POST['editProject'])) {
                                                $newProjectName = mysqli_real_escape_string($conn, $_POST["project_name"]);
                                                $newProjectDescription = mysqli_real_escape_string($conn, $_POST["project_description"]);
                                                $selectedProjectID = mysqli_real_escape_string($conn, $_SESSION['projecid']);

                                                $updateQuery = "UPDATE project SET 
                    Project_title='$newProjectName',
                    Project_description='$newProjectDescription'
                    WHERE Project_id='$selectedProjectID'";

                                                if (mysqli_query($conn, $updateQuery)) {
                                                    echo "<script>
                    Swal.fire({
                        title: 'Changes saved successfully!',
                        text: '',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 3000, 
                    }).then(function() {
                        window.location.href = 'collab-projectView.php?project_id=" . $_SESSION['projecid'] . "';
                    });
                </script>";
                                                } else {
                                                    echo '<script type="text/javascript">';
                                                    echo 'swal("Error!", "Error updating record: ' . mysqli_error($conn) . '", "error");';
                                                    echo '</script>';
                                                }
                                            }
                                        }
                                        ?>



                                        <div class="content">
                                            <div class="projectMenu">


                                                <button id="viewProjectTeam" class="ViewTeamBTN"><i class="fas fa-users"></i> Project Team</button>



                                                <button id="editComp" class="editProjectBTN"><i class="fas fa-info-circle" title="Information"></i> Project
                                                    Description</button>



                                            

                                            </div>







                                            <div id="editModal" class="modalBlock">
                                                <div class="modal-edit">
                                                    <form class="editForm" action="" method="post" enctype="multipart/form-data">
                                                        <input type="hidden" name="editProject">
                                                        <div class="editTop" style="display: flex; justify-content: space-between;">
                                                            <h3>
                                                                <?php echo $project_name ?>'s Description
                                                            </h3>
                                                            <span class="closeEditM" style="cursor: pointer; color: #006BB9;">&times;</span>
                                                        </div>
                                                        <p id="editP" style="color: #006BB9; cursor: pointer;"><i class="fas fa-edit"></i> Edit</p><br>
                                                        <?php
                                                        $projectDesc = mysqli_query($conn, "SELECT * FROM project WHERE project_id='{$_SESSION['projecid']}'");
                                                        if (mysqli_num_rows($projectDesc) > 0) {
                                                            $row = mysqli_fetch_assoc($projectDesc);
                                                            ?>
                                                                                            <p>Project Name:</p>
                                                                                            <input type="text" id="project_name" name="project_name" value="<?php echo $project_name ?>"
                                                                                                required readonly>
                                                                                            <p>Project Description:</p>
                                                                                            <textarea id="project_description" name="project_description" rows="12" required readonly
                                                                                                class="prodesc"><?php echo $row['Project_Description']; ?></textarea>

                                                        <?php } ?>

                                                        <input type="submit" value="Save Changes" name="submit" style="visibility: hidden;"><br>
                                                        <p id="cancelBTN" style="text-align: center; cursor: pointer; visibility: hidden;">Cancel</p>

                                                        <br>
                                                    </form>
                                                </div>
                                            </div>




                                            <div id="viewTeam" class="modalBlock">
                                                <div class="modal-edit">
                                                    <div class="editTop" style="display: flex; justify-content: space-between;">
                                                        <h3 style="color: #006BB9;">
                                                            <?php echo $project_name ?>'s Team
                                                        </h3>
                                                        <span class="closeVT" style="cursor: pointer; color: #006BB9;">&times;</span>
                                                    </div>

                                                    <?php
                                                    $selectCreator = "SELECT s.Student_fname, s.Student_lname, s.Student_email, s.Student_ID FROM project p, student_registration s, company_registration c WHERE c.student_id = s.student_id
                          AND p.company_id = c.company_id
                          AND p.project_id='{$_SESSION['projecid']}' LIMIT 1";

                                                    $projectCre = mysqli_query($conn, $selectCreator);
                                                    ?>

                                                    <h4>Project Creator<span style="color: #565656; font-weight: 500; font-size: 13px; font-style: italic;">
                                                            (You)</span></h4>
                                                    <div style="width: 100%; height: auto">
                                                        <?php
                                                        if (mysqli_num_rows($projectCre) > 0) {
                                                            $row = mysqli_fetch_assoc($projectCre);
                                                            $projCreator = $row['Student_ID'];
                                                            echo "<script>console.log(" . $_SESSION['projecid'] . ");</script>";
                                                            ?>

                                                                                            <div class="name-holder">
                                                                                                <span style="color:#093a5d">
                                                                                                    <?php echo $row['Student_fname'] . ' ' . $row['Student_lname'] . " (" . $row['Student_email'] . ")" ?>
                                                                                                </span>
                                                                                            </div>
                                                        <?php } ?>
                                                    </div>

                                                    <br>

                                                    <?php
                                                    $selectQueryTeam = "SELECT s.Student_email, s.Student_fname, s.Student_lname FROM project_member p, student_registration s WHERE p.student_id = s.student_id
                            AND p.project_id='{$_SESSION['projecid']}' AND s.student_id!='{$projCreator}'";

                                                    $projectTea = mysqli_query($conn, $selectQueryTeam);
                                                    ?>

                                                    <h4>Team Members<span style="color: #565656; font-weight: 500; font-size: 12px; font-style: italic;">
                                                            (Members will appear here once they accepted your invitation request.)</span></h4>
                                                    <div style="width: 100%; height: auto">
                                                        <?php
                                                        while ($row = mysqli_fetch_array($projectTea)) {
                                                            ?>
                                                                                            <div class="name-holder">
                                                                                                <span style="color:#093a5d">
                                                                                                    <?php echo $row['Student_fname'] . ' ' . $row['Student_lname'] . " (" . $row['Student_email'] . ")" ?>
                                                                                                </span>
                                                                                            </div>
                                                        <?php } ?>
                                                    </div>

                                                    <?php
                                                    $selectQueryMentor = "SELECT * FROM project_mentor p
                              JOIN instructor_registration i ON p.mentor_id = i.instructor_id
                              WHERE p.project_id='{$_SESSION['projecid']}' LIMIT 1";

                                                    $projectMentor = mysqli_query($conn, $selectQueryMentor);
                                                    ?>

                                                    <br>

                                                    <h4>Project Mentor</h4>
                                                    <div style="width: 100%; height: auto">
                                                        <?php
                                                        while ($row = mysqli_fetch_array($projectMentor)) {
                                                            ?>
                                                                                            <div class="name-holder">
                                                                                                <span style="color:#093a5d">
                                                                                                    <?php echo $row['Instructor_fname'] . ' ' . $row['Instructor_lname'] . " (" . $row['Instructor_email'] . ")" ?>
                                                                                                </span>
                                                                                            </div>
                                                        <?php } ?>
                                                    </div>

                                                    <?php
                                                    $selectQueryEval = "SELECT * FROM project_evaluator p
                            JOIN instructor_registration i ON p.evaluator_id = i.instructor_id
                            WHERE p.project_id='{$_SESSION['projecid']}'";

                                                    $projecteval = mysqli_query($conn, $selectQueryEval);
                                                    ?>

                                                    <br>
                                                    <h4>Project Evaluators</h4>
                                                    <div style="width: 100%; height: auto">
                                                        <?php
                                                        while ($row = mysqli_fetch_array($projecteval)) {
                                                            ?>
                                                                                            <div class="name-holder">
                                                                                                <span style="color:#093a5d">
                                                                                                    <?php echo $row['Instructor_fname'] . ' ' . $row['Instructor_lname'] . " (" . $row['Instructor_email'] . ")" ?>
                                                                                                </span>
                                                                                            </div>
                                                        <?php } ?>
                                                    </div>

                                                </div>
                                            </div>



                                            <!-- echo the company id here -->
                                            <div
                                                style="text-decoration: none; display: flex; align-items: center; justify-content: space-between; width: 90%; padding: 30px; margin: 15px; height: 80px;">

                                                <div style="text-align: left; position:fixed; margin: -50px !important">
                                                    <span style="font-size: medium;">
                                                        <a href="collabprojects.php"
                                                            style="text-decoration:none; color:#006BB9;" title="Back"><i class="fas fa-angle-left"
                                                                style="font-size: 40px;"></i>

                                                        </a>
                                                    </span>
                                                </div>
                                                <div style="text-align: center; flex-grow: 1;">
                                                    <h1
                                                        style="margin: 0; text-decoration: none; line-height: normal; font-family: Arial, Helvetica, sans-serif; font-style: normal; text-transform: none; letter-spacing: normal;">
                                                        <?php echo $project_name ?>
                                                    </h1>
                                                </div>
                                            </div>

                                            <div class="process-wrapper">
                                                <div id="progress-bar-container">
                                                    <ul>
                                                        <li class="step step01 active">
                                                            <div class="step-inner">Ideation Phase</div>
                                                        </li>
                                                        <li class="step step02">
                                                            <div class="step-inner">Pitching Phase</div>
                                                        </li>
                                                        <li class="step step03">
                                                            <div class="step-inner">Finish</div>
                                                        </li>
                                                    </ul>

                                                    <div id="line">
                                                        <div id="line-progress"></div>
                                                    </div>
                                                </div>
                                                <?php
                                                $select_ideation_id = mysqli_query($conn, "SELECT ideation_phase.IdeationID FROM ideation_phase INNER JOIN project ON ideation_phase.Project_ID=project.Project_ID WHERE project.Project_ID=$project_id");
                                                if (mysqli_num_rows($select_ideation_id) > 0) {
                                                    $row = mysqli_fetch_assoc($select_ideation_id);
                                                    $ideationID = $row['IdeationID'];

                                                    $select_comment = mysqli_query($conn, "SELECT comment_ideation.comment_overview, comment_ideation.comment_logo, comment_ideation.comment_canvas FROM comment_ideation INNER JOIN ideation_phase ON comment_ideation.ideationID=ideation_phase.IdeationID WHERE ideation_phase.IdeationID=$ideationID");

                                                    if (mysqli_num_rows($select_comment) > 0) {
                                                        $i = 0;
                                                        while ($row = mysqli_fetch_assoc($select_comment)) {
                                                            $i++;
                                                            $comment_overview = $row['comment_overview'];
                                                            $comment_logo = $row['comment_logo'];
                                                            $comment_canvas = $row['comment_canvas'];

                                                        }
                                                    } else {

                                                    }
                                                } else {
                                                    $comment_overview = '';
                                                    $comment_logo = '';
                                                    $comment_canvas = '';
                                                }
                                                ?>


                                                <?php
                                                $select_pitching_id = mysqli_query($conn, "SELECT pitching_phase.PitchingID FROM pitching_phase INNER JOIN project ON pitching_phase.Project_ID=project.Project_ID WHERE project.Project_ID=$project_id");
                                                if (mysqli_num_rows($select_pitching_id) > 0) {
                                                    $row = mysqli_fetch_assoc($select_pitching_id);
                                                    $pitchingID = $row['PitchingID'];

                                                    $select_comment = mysqli_query($conn, "SELECT comment_pitching.comment_video, comment_pitching.comment_deck FROM comment_pitching inner JOIN pitching_phase ON comment_pitching.pitchingID=pitching_phase.PitchingID WHERE comment_pitching.PitchingID=$pitchingID;");

                                                    if (mysqli_num_rows($select_comment) > 0) {
                                                        $row = mysqli_fetch_assoc($select_comment);
                                                        $comment_video = $row['comment_video'];
                                                        $comment_deck = $row['comment_deck'];
                                                    } else {
                                                        $comment_video = '';
                                                        $comment_deck = '';
                                                    }
                                                }


                                                ?>

                                                <div id="progress-content-section">
                                                    <div class="section-content ideation active">
                                                        <form action="" method="post" enctype="multipart/form-data">



                                                            <?php
                                                            $select_ideation_phase = mysqli_query($conn, "SELECT ideation_phase.Project_Overview, ideation_phase.Project_logo, ideation_phase.Project_Modelcanvas, ideation_phase.status  FROM ideation_phase INNER JOIN project ON ideation_phase.Project_ID=project.Project_ID WHERE project.Project_ID=$project_id LIMIT 1;");
                                                            $ideationStatus;
                                                            if (mysqli_num_rows($select_ideation_phase) > 0) {
                                                                $row = mysqli_fetch_assoc($select_ideation_phase);
                                                                $overview = $row['Project_Overview'];
                                                                $ideationStatus = $row['status'];
                                                                //if merong record
                                                             
                                                                                             
   ?>
                                                                                             

                                                                                                <div class="phaseSection">
                                                                                                    <p class="sectionTitle">
                                                                                                        1. Detailed Project Overview</p>

                                                                                                    <p class="projectOverviewDirection">In this space, provide a comprehensive summary of your
                                                                                                        project, highlighting its key
                                                                                                        aspects and objectives. Consider including the following elements:</p>
                                                                                                    <p class="projectOverviewDirection2">
                                                                                                        <b>Objective/Purpose:</b> Briefly describe the primary goal or purpose of your project.
                                                                                                        What
                                                                                                        problem does it aim to solve or what need does it fulfill?<br>

                                                                                                        <b>Scope: </b>Define the boundaries and limitations of your project. What is included,
                                                                                                        and what
                                                                                                        is excluded? This helps set expectations for stakeholders. <br>

                                                                                                        <b>Target Audience: </b>Identify the intended users or beneficiaries of your project.
                                                                                                        Understanding your audience is crucial for tailoring the project to meet their needs.
                                                                                                        <br>
                                                                                                        <b>Key Features: </b>Outline the main features or functionalities that your project will
                                                                                                        offer.
                                                                                                        This gives readers a snapshot of what to expect.
                                                                                                    </p>

                                                                                                    <textarea class="projectOverview-textarea" name="project_overview" cols="100" rows="20"
                                                                                                        required placeholder="Write your project overview here..."
                                                                                                        style="margin-top: 0;"><?php echo $overview ?></textarea>

                                                                                                    <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>
                                                                                              
                                                                                                    
                                                                                                    <div class="feedbackSection">

<?php
 $select_comment_overview = mysqli_query($conn, "SELECT *
 FROM instructor_registration
 INNER JOIN comment_ideation_overview ON instructor_registration.Instructor_ID = comment_ideation_overview.instructor_ID
 INNER JOIN ideation_phase ON ideation_phase.IdeationID = comment_ideation_overview.ideationID
 INNER JOIN project ON project.Project_ID = ideation_phase.Project_ID
 
 
 
 WHERE project.Project_ID = ".$project_id." AND ideation_phase.IdeationID = comment_ideation_overview.ideationID
 
 ORDER BY comment_ideation_overview.comment_date DESC;");

 if (mysqli_num_rows($select_comment_overview) > 0) {
     while ($row = mysqli_fetch_assoc($select_comment_overview)) {
         $fetch_comment_overview = $row['comment_overview'];
         $fetch_mentor_name = $row['Instructor_fname'].' '.$row['Instructor_lname'];
         date_default_timezone_set('Asia/Manila');

         $fetch_commentdate = new DateTime($row['comment_date']);
         $currentDate = new DateTime();
         $timeElapsed = $currentDate->getTimestamp() - $fetch_commentdate->getTimestamp();

         if ($timeElapsed < 60) {
             $fetch_commentdate = 'Just now';
         } elseif ($timeElapsed < 3600) {
             $minutes = floor($timeElapsed / 60);
             $fetch_commentdate = ($minutes == 1) ? '1 min ago' : $minutes . ' mins ago';
         } elseif ($timeElapsed < 86400) {
             $hours = floor($timeElapsed / 3600);
             $fetch_commentdate = ($hours == 1) ? '1 hr ago' : $hours . ' hrs ago';
         } elseif ($timeElapsed < 604800) {
             $days = floor($timeElapsed / 86400);
             $fetch_commentdate = ($days == 1) ? '1 day ago' : $days . ' days ago';
         } elseif ($timeElapsed < 1209600) {
             $fetch_commentdate = '1 week ago';
         } elseif ($timeElapsed < 1814400) {
             $fetch_commentdate = '2 weeks ago';
         } elseif ($timeElapsed < 2419200) {
             $fetch_commentdate = '3 weeks ago';
         } else {
             $fetch_commentdate = $fetch_commentdate->format('j M Y, g:i a');
         }
?>

                                                                                                           

                                                                                                                <div class="feedbackBlock">

                                                                                                                    <div class="feedback-info">
                                                                                                                        <span class="commenter"><?php echo $fetch_mentor_name;?></span>
                                                                                                                        <span class="feedbackdate"> • <?php echo $fetch_commentdate;?></span>
                                                                                                                    </div>
                                                                                                                    <p class="feedbackContent">
                                                                                                                    <?php echo $fetch_comment_overview;?>
                                                                                                                    </p>
                                                                                                                
                                                                                                            </div> <!-- end of feedbackSection -->
                                        <?php
     }}
                                        ?>  </div>                                                                 
                                                                                                        </div>


 
                                                                                                <?php



                                                            } else {
                                                                //if wala pang record
                                                                ?>
                                                                                                <div class="phaseSection">
                                                                                                    <p class="sectionTitle">
                                                                                                        1. Detailed Project Overview</p>

                                                                                                    <p class="projectOverviewDirection">In this space, provide a comprehensive summary of your
                                                                                                        project, highlighting its key
                                                                                                        aspects and objectives. Consider including the following elements:</p>
                                                                                                    <p class="projectOverviewDirection2">
                                                                                                        <b>Objective/Purpose:</b> Briefly describe the primary goal or purpose of your project.
                                                                                                        What
                                                                                                        problem does it aim to solve or what need does it fulfill?<br>

                                                                                                        <b>Scope: </b>Define the boundaries and limitations of your project. What is included,
                                                                                                        and what
                                                                                                        is excluded? This helps set expectations for stakeholders. <br>

                                                                                                        <b>Target Audience: </b>Identify the intended users or beneficiaries of your project.
                                                                                                        Understanding your audience is crucial for tailoring the project to meet their needs.
                                                                                                        <br>
                                                                                                        <b>Key Features: </b>Outline the main features or functionalities that your project will
                                                                                                        offer.
                                                                                                        This gives readers a snapshot of what to expect.
                                                                                                    </p>
                                                                                                    <textarea class="projectOverview-textarea" name="project_overview" cols="100" rows="20"
                                                                                                        required placeholder="Write your project overview here..."></textarea>
                                                                                                    <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>

                                                                                             
                                                                                                </div>
                                                                                                <?php
                                                            }

                                                            ?>




                                                            <?php
                                                            $select_ideation_phase = mysqli_query($conn, "SELECT ideation_phase.Project_Overview, ideation_phase.Project_logo, ideation_phase.Project_Modelcanvas FROM ideation_phase INNER JOIN project ON ideation_phase.Project_ID=project.Project_ID WHERE project.Project_ID=$project_id LIMIT 1;");

                                                            if (mysqli_num_rows($select_ideation_phase) > 0) {
                                                                $row = mysqli_fetch_assoc($select_ideation_phase);
                                                                $logo_ideation = $row['Project_logo'];
                                                                //if merong record
                                                                ?>
                                                                                                




                                                                                                <div class="phaseSection">
                                                                                                    <p class="sectionTitle">
                                                                                                        2. Project Logo</p>

                                                                                                    <p class="projectOverviewDirection">In this section, kindly upload the logo your project's
                                                                                                        logo in PNG format. Ensure the logo is original, capturing the essence of your project.
                                                                                                        It should be a visual representation that reflects the core values and identity of your
                                                                                                        startup</p><br>
                                                                                                    <h3 style="margin-top: 0;">
                                                                                                        <?php if (isset($project_name)) {
                                                                                                            echo $project_name;
                                                                                                        } ?>'s Current Logo
                                                                                                    </h3>

                                                                                                    <div class="img-container" style="display: flex;
                                            justify-content: center;
                                                align-items: center;">
                                                                                                        <img src="<?php if (isset($logo_ideation)) {
                                                                                                            echo $logo_ideation;
                                                                                                        } ?>" alt="Logo_img" width="320px" height="320px" style=" border-radius: 20px">
                                                                                                    </div>
                                                                                                    <br>
                                                                                                    <hr>
                                                                                                    <h3 style="font-size: 14px; margin: 0; margin-top: 15px;"><i class="fas fa-caret-down"></i>
                                                                                                        Change your Project Logo</h3>
                                                                                                    <span
                                                                                                        style="text-decoration: none; font-weight: 400; font-size: 12px; font-style:italic;">(Ignore
                                                                                                        this when you do not want to change your project logo)</span><br><br>
                                                                                                    <div style="width: 100%; display:flex; justify-content:center;">
                                                                                                        <div class="image-preview" style="align-self: center;">
                                                                                                            Image Preview here.
                                                                                                        </div>
                                                                                                    </div><br>

                                                                                                    <label for="projectLogo" class="fileInputLabel" id="projectLogoInputLabel"><i
                                                                                                            class="fa fa-upload" style="color: #0f73d2;"></i> Choose your New Project
                                                                                                        Logo</label>
                                                                                                    <input id="projectLogo" class="fileInput" type="file" name="project_logo" accept="image/png"
                                                                                                        onchange="displayFileName('projectLogo')">
                                                                                                    <div> <a class="cancelUpdateLogo" id="cancelUpdateLogo"
                                                                                                            onclick="cancelFileUpdate()">Cancel</a></div>






                                                                                                    <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>

                                                                                                    <div class="feedbackSection">

<?php
 $select_comment_overview = mysqli_query($conn, "SELECT *
 FROM instructor_registration
 INNER JOIN comment_ideation_logo ON instructor_registration.Instructor_ID = comment_ideation_logo.instructor_ID
 INNER JOIN ideation_phase ON ideation_phase.IdeationID = comment_ideation_logo.ideationID
 INNER JOIN project ON project.Project_ID = ideation_phase.Project_ID
 
 
 
 WHERE project.Project_ID = ".$project_id." AND ideation_phase.IdeationID = comment_ideation_logo.ideationID
 
 ORDER BY comment_ideation_logo.comment_date DESC;");

 if (mysqli_num_rows($select_comment_overview) > 0) {
     while ($row = mysqli_fetch_assoc($select_comment_overview)) {
         $fetch_comment_logo = $row['comment_logo'];
         $fetch_mentor_nameLogo = $row['Instructor_fname'].' '.$row['Instructor_lname'];
         date_default_timezone_set('Asia/Manila');

         $fetch_commentdateLogo = new DateTime($row['comment_date']);
         $currentDate = new DateTime();
         $timeElapsed = $currentDate->getTimestamp() - $fetch_commentdateLogo->getTimestamp();

         if ($timeElapsed < 60) {
             $fetch_commentdateLogo = 'Just now';
         } elseif ($timeElapsed < 3600) {
             $minutes = floor($timeElapsed / 60);
             $fetch_commentdateLogo = ($minutes == 1) ? '1 min ago' : $minutes . ' mins ago';
         } elseif ($timeElapsed < 86400) {
             $hours = floor($timeElapsed / 3600);
             $fetch_commentdateLogo = ($hours == 1) ? '1 hr ago' : $hours . ' hrs ago';
         } elseif ($timeElapsed < 604800) {
             $days = floor($timeElapsed / 86400);
             $fetch_commentdateLogo = ($days == 1) ? '1 day ago' : $days . ' days ago';
         } elseif ($timeElapsed < 1209600) {
             $fetch_commentdateLogo = '1 week ago';
         } elseif ($timeElapsed < 1814400) {
             $fetch_commentdateLogo = '2 weeks ago';
         } elseif ($timeElapsed < 2419200) {
             $fetch_commentdateLogo = '3 weeks ago';
         } else {
             $fetch_commentdateLogo = $fetch_commentdateLogo->format('j M Y, g:i a');
         }
?>

                                                                                                           

                                                                                                                <div class="feedbackBlock">

                                                                                                                    <div class="feedback-info">
                                                                                                                        <span class="commenter"><?php echo $fetch_mentor_nameLogo;?></span>
                                                                                                                        <span class="feedbackdate"> • <?php echo $fetch_commentdateLogo;?></span>
                                                                                                                    </div>
                                                                                                                    <p class="feedbackContent">
                                                                                                                    <?php echo $fetch_comment_logo;?>
                                                                                                                    </p>
                                                                                                                
                                                                                                            </div> <!-- end of feedbackSection -->
                                        <?php
     }}
                                        ?>  </div>                                                             
                                                                                                        </div>

                                                                                                <?php
                                                            } else {
                                                                //if wala pang record
                                                                ?>
                                                                                             

                                                                                                <div class="phaseSection">
                                                                                                    <p class="sectionTitle">
                                                                                                        2. Project Logo</p>

                                                                                                    <p class="projectOverviewDirection">In this section, kindly upload the logo your project's
                                                                                                        logo in PNG format. Ensure the logo is original, capturing the essence of your project.
                                                                                                        It should be a visual representation that reflects the core values and identity of your
                                                                                                        startup</p><br>

                                                                                                    <div style="width: 100%; display:flex; justify-content:center;">
                                                                                                        <div class="image-preview" style="align-self: center;">
                                                                                                            Image Preview here.
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <br>
                                                                                                    <label for="projectLogo" class="fileInputLabel" id="projectLogoInputLabel"><i
                                                                                                            class="fa fa-upload" style="color: #0f73d2;"></i> Choose your Project
                                                                                                        Logo Here</label>
                                                                                                    <input id="projectLogo" class="fileInput" type="file" name="project_logo" accept="image/png"
                                                                                                        onchange="displayFileName('projectLogo')">
                                                                                                    <div> <a class="cancelUpdateLogo" id="cancelUpdateLogo"
                                                                                                            onclick="cancelFileUpdate()">Cancel</a></div>




                                                                                                    <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>

                                                                                             
                                                                                                </div>



                                                                                                <?php
                                                            }

                                                            ?>


                                                            <?php
                                                            $select_ideation_phase = mysqli_query($conn, "SELECT ideation_phase.Project_Overview, ideation_phase.Project_logo, ideation_phase.Project_Modelcanvas FROM ideation_phase INNER JOIN project ON ideation_phase.Project_ID=project.Project_ID WHERE project.Project_ID=$project_id;");

                                                            if (mysqli_num_rows($select_ideation_phase) > 0) {
                                                                $row = mysqli_fetch_assoc($select_ideation_phase);
                                                                $model_canvas_ideation = $row['Project_Modelcanvas'];
                                                                //if merong record
                                                                ?>
                                                                                                <!-- <h3>Project Model Canvas:</h3>
                                <embed type="application/pdf" src="<?php //echo $model_canvas_ideation; ?>" width="580"
                                    height="600">

                                <label for="model_canvas">
                                    <h5>Select Another Model Canvas: </h5>
                                </label>
                                <input type="file" name="model_canvas">

                                <h5>Comment Model Canvas</h5>
                                <textarea cols="100" rows="5" readonly><?php //if (isset($comment_canvas))
                                        //echo $comment_canvas ?></textarea> -->



                                                                                                <div class="phaseSection">
                                                                                                    <p class="sectionTitle">
                                                                                                        3. Startup Model Canvas</p>

                                                                                                    <p class="projectOverviewDirection">In this section, kindly upload your project's Model
                                                                                                        Canvas in PDF format. The Model Canvas is a strategic management tool that provides a
                                                                                                        holistic view of your project's key components. It typically includes sections on your
                                                                                                        project's value proposition, customer segments, channels, revenue streams, cost
                                                                                                        structure, and more. Ensure your Model Canvas encapsulates crucial details about your
                                                                                                        project's business model, allowing stakeholders to understand its key elements at a
                                                                                                        glance. If you're new to the concept, think of the Model Canvas as a snapshot of your
                                                                                                        project's strategy and execution plan. We look forward to reviewing your comprehensive
                                                                                                        overview!</p>
                                                                                                    <h3 style="margin-top: 0;">
                                                                                                        <?php if (isset($project_name)) {
                                                                                                            echo $project_name;
                                                                                                        } ?>'s Current Startup Model Canvas
                                                                                                    </h3>

                                                                                                    <embed type="application/pdf" src="<?php echo $model_canvas_ideation; ?>">
                                                                                                    <br><br>


                                                                                                    <hr>
                                                                                                    <h3 style="font-size: 14px; margin: 0; margin-top: 15px;"><i class="fas fa-caret-down"></i>
                                                                                                        Change your Startup Model Canvas</h3>
                                                                                                    <span
                                                                                                        style="text-decoration: none; font-weight: 400; font-size: 12px; font-style:italic;">(Ignore
                                                                                                        this when you do not want to change your startup model canvas)</span><br><br>




                                                                                                    <br>
                                                                                                    <div class="pdf-preview">
                                                                                                        PDF preview here.
                                                                                                    </div>
                                                                                                    <br><br>

                                                                                                    <label for="canvasFile" class="fileInputLabel" id="canvasFileInputLabel"><i
                                                                                                            class="fa fa-upload" style="color: #0f73d2;"></i> Choose your New Startup Model
                                                                                                        Canvas</label>
                                                                                                    <input id="canvasFile" class="fileInput" type="file" name="canvas_file"
                                                                                                        accept="application/pdf" onchange="displayPdfFile('canvasFile')">


                                                                                                    <div>

                                                                                                        <a class="cancelUpdateCanvas" id="cancelUpdateCanvas"
                                                                                                            onclick="cancelCanvasUpdate()">Cancel</a>


                                                                                                    </div>


                                                                                                    <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>

                                                                                                    <div class="feedbackSection">

<?php
 $select_comment_model = mysqli_query($conn, "SELECT *
 FROM instructor_registration
 INNER JOIN comment_ideation_model ON instructor_registration.Instructor_ID = comment_ideation_model.instructor_ID
 INNER JOIN ideation_phase ON ideation_phase.IdeationID = comment_ideation_model.ideationID
 INNER JOIN project ON project.Project_ID = ideation_phase.Project_ID
 
 
 
 WHERE project.Project_ID = ".$project_id." AND ideation_phase.IdeationID = comment_ideation_model.ideationID
 
 ORDER BY comment_ideation_model.comment_date DESC;");

 if (mysqli_num_rows($select_comment_model) > 0) {
     while ($row = mysqli_fetch_assoc($select_comment_model)) {
         $fetch_comment_model = $row['comment_model'];
         $fetch_mentor_nameModel = $row['Instructor_fname'].' '.$row['Instructor_lname'];
         date_default_timezone_set('Asia/Manila');
//ditokana
         $fetch_commentdateModel = new DateTime($row['comment_date']);
         $currentDate = new DateTime();
         $timeElapsed = $currentDate->getTimestamp() - $fetch_commentdateModel->getTimestamp();

         if ($timeElapsed < 60) {
             $fetch_commentdateModel = 'Just now';
         } elseif ($timeElapsed < 3600) {
             $minutes = floor($timeElapsed / 60);
             $fetch_commentdateModel = ($minutes == 1) ? '1 min ago' : $minutes . ' mins ago';
         } elseif ($timeElapsed < 86400) {
             $hours = floor($timeElapsed / 3600);
             $fetch_commentdateModel = ($hours == 1) ? '1 hr ago' : $hours . ' hrs ago';
         } elseif ($timeElapsed < 604800) {
             $days = floor($timeElapsed / 86400);
             $fetch_commentdateModel = ($days == 1) ? '1 day ago' : $days . ' days ago';
         } elseif ($timeElapsed < 1209600) {
             $fetch_commentdateModel = '1 week ago';
         } elseif ($timeElapsed < 1814400) {
             $fetch_commentdateModel = '2 weeks ago';
         } elseif ($timeElapsed < 2419200) {
             $fetch_commentdateModel = '3 weeks ago';
         } else {
             $fetch_commentdateModel = $fetch_commentdateModel->format('j M Y, g:i a');
         }
?>

                                                                                                           

                                                                                                                <div class="feedbackBlock">

                                                                                                                    <div class="feedback-info">
                                                                                                                        <span class="commenter"><?php echo $fetch_mentor_nameModel;?></span>
                                                                                                                        <span class="feedbackdate"> • <?php echo $fetch_commentdateModel;?></span>
                                                                                                                    </div>
                                                                                                                    <p class="feedbackContent">
                                                                                                                    <?php echo $fetch_comment_model;?>
                                                                                                                    </p>
                                                                                                                
                                                                                                            </div> <!-- end of feedbackSection -->
                                        <?php
     }}
                                        ?>  </div>                                                          
                                                                                                        </div>




                                                                                                <?php
                                                            } else {
                                                                //if wala pang record
                                                                ?>
                                                                                                <!-- <label for="model_canvas">
                                    <h5>Startup Model Canvas: </h5>
                                </label>
                                <input type="file" name="model_canvas" required> -->
                                                                                                <div class="phaseSection">
                                                                                                    <p class="sectionTitle">
                                                                                                        3. Startup Model Canvas</p>

                                                                                                    <p class="projectOverviewDirection">In this section, kindly upload your project's Model
                                                                                                        Canvas in PDF format. The Model Canvas is a strategic management tool that provides a
                                                                                                        holistic view of your project's key components. It typically includes sections on your
                                                                                                        project's value proposition, customer segments, channels, revenue streams, cost
                                                                                                        structure, and more. Ensure your Model Canvas encapsulates crucial details about your
                                                                                                        project's business model, allowing stakeholders to understand its key elements at a
                                                                                                        glance. If you're new to the concept, think of the Model Canvas as a snapshot of your
                                                                                                        project's strategy and execution plan. We look forward to reviewing your comprehensive
                                                                                                        overview!</p><br>

                                                                                                    <div class="pdf-preview">
                                                                                                        PDF preview here.
                                                                                                    </div>
                                                                                                    <br><br>

                                                                                                    <label for="canvasFile" class="fileInputLabel" id="canvasFileInputLabel"><i
                                                                                                            class="fa fa-upload" style="color: #0f73d2;"></i> Choose your Startup Model
                                                                                                        Canvas</label>
                                                                                                    <input id="canvasFile" class="fileInput" type="file" name="canvas_file"
                                                                                                        accept="application/pdf" onchange="displayPdfFile('canvasFile')">
                                                                                                    <div>

                                                                                                        <a class="cancelUpdateCanvas" id="cancelUpdateCanvas"
                                                                                                            onclick="cancelCanvasUpdate()">Cancel</a>


                                                                                                    </div>
                                                                                                    <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>

                                                                                               
                                                                                                </div>




                                                                                                <?php
                                                            }

                                                            ?>

                                                            <!-- <div style="align-self: center;">
                                <button class="saveBtn" name="btnSave">SUBMIT</button>
                                <button class="submitBtn" name="submitBtn">---</button>
                            </div> -->
                                                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                                <button class="saveBtn" name="btnSave"
                                                                    style="width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; border-radius:20px; padding: 20px; font-weight: bold;"
                                                                    title="This will save your project. Easily return to your work later without losing progress.">Save Ideation Phase</button>
                                                            </div>

                                                            <div
                                                                style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-top: 10px;">
                                                             
                                                            </div>


                                                        </form>
                                                        <a href="#" style="font-size: 15px; text-decoration: none; color: inherit;" onmouseover="this.style.color='#107cce'" onmouseout="this.style.color=''">
        Back to Top
      </a>
                                                    </div>

                                                    <div class="section-content pitching">
                                          <?php
                                          //opening tag ideation
                                          if (!isset($ideationStatus) || strtoupper($ideationStatus) == "PENDING") {
?>

<div class="phaseSection">
<img src="images/closedicon.png" alt="Logo_img" width="200px" height="200px" >
<p>Please note that <b><?php echo $project_name;  ?>'s Ideation Phase </b> requires approval from your evaluators before accessing details here in Pitching Phase. Kindly ensure Ideation Phase approval for continued progress. Thank you!</p>
</div>

<?php
 }else{
                                          ?>
                                                    <form action="" method="post" enctype="multipart/form-data">
                                                     
                                                         <?php
                                                         $select_pitching_phase = mysqli_query($conn, "SELECT pitching_phase.VideoPitch, pitching_phase.PitchDeck, pitching_phase.status FROM pitching_phase INNER JOIN project ON pitching_phase.Project_ID=project.Project_ID WHERE project.Project_ID=$project_id");

                                                         if (mysqli_num_rows($select_pitching_phase) > 0) {

                                                             $row = mysqli_fetch_assoc($select_pitching_phase);
                                                             $vidPitch = $row['VideoPitch'];
                                                             $deckPitch = $row['PitchDeck'];
                                                             $pitchingStatus = $row['status'];
                                                             ?>
        <!-- may pitching na -->
        <div class="phaseSection">
                                                                                                    <p class="sectionTitle">
                                                                                                        4. Video Pitch</p>

                                                                                                    <p class="projectOverviewDirection"><b>Congratulations on reaching the Pitching Phase!</b><br>

                                In this section, we invite you to present a dynamic and compelling video pitch that provides a comprehensive overview of your startup. To ensure a comprehensive review, please adhere to the following guidelines:</p>
                                                                                                    <p class="projectOverviewDirection2">
                                                                                                    When preparing your video pitch, succinctly describe the problem your project addresses, define the specific audience or customer group it targets, outline your project's solution, and highlight key features or functionalities it offers. Share any significant progress, milestones, or traction achieved, and clearly state how you plan to use funds obtained from investors. Ensure a concise presentation for an impactful and informative pitch.
                                                                                                        <br>
                                                                                                        <p class="projectOverviewDirection">
                                                                                                        <b>Duration: </b>Your video pitch should be between 3 to 5 minutes.<br>
                                                                        
                                                                                                        <b>Introduction of Founders: </b>Introduce all founders in the video.<br>
                                                                                                        <b>Rationale Behind Startup Creation: </b>Explain the reasons and motivations behind the creation of your startup.<br>
                                                                                                        <b>Natural Voices: </b>Utilize the natural voices of the founders; no text-to-speech enhancements are allowed.<br>
                                                                                                        <b>Demo (if applicable): </b>If a demo is included, it should not exceed 1 minute.<br><br>
                                                                                                        Thank you for sharing your startup pitch with us. We appreciate your efforts and look forward to the success of your innovative venture!</p>
                                                                             
                                                                                                    </p>
                                                                                                    <h3 style="margin-top: 20px;">
            <?php if (isset($project_name)) {
                echo $project_name;
            } ?>'s Current Video Pitch
        </h3>

            <video class="video-preview" controls>
                <source src="<?php if (isset($vidPitch)) {
                    echo $vidPitch;
                } ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>

        <br><br>
        <h3 style="font-size: 14px; margin: 0; margin-top: 15px;"><i class="fas fa-caret-down"></i> Change your Video Pitch</h3>
        <span style="text-decoration: none; font-weight: 400; font-size: 12px; font-style: italic;">(Ignore this when you do not want to change your video pitch)</span><br><br>

        <div class="video-preview-container">
            <video class="video-preview" controls>
                <source src="" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <br><br>
                                                                                                    <!-- File Input and Label -->
                                                                                                    <label for="videoFile" class="fileInputLabel" id="videoFileLabel">
                                                                                                        <i class="fa fa-upload" style="color: #0f73d2;"></i> Choose your New Video Pitch
                                                                                                    </label>
                                                                                                    <input id="videoFile" class="fileInput" type="file" name="video_pitch" accept="video/mp4"
                                                                                                        onchange="displayVideoFile('videoFile')">

                                                                                                    <!-- Cancel Update Button -->
                                                                                                    <div>
                                                                                                        <a class="cancel-update-button" id="cancelUpdateVideo"
                                                                                                            onclick="cancelVideoUpdate()">Cancel</a>
                                                                                                    </div>
                                                                                                    <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>

                                                <div class="feedbackSection">

                                                    <div class="feedbackBlock">

                                                        <div class="feedback-info">
                                                            <span class="commenter">Moniqua Lee</span>
                                                            <span class="feedbackdate">(Mentor) • 1hr ago</span>
                                                        </div>
                                                        <p class="feedbackContent">
                                                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                                                            dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                                                            sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                                    </div>
                                                    <div class="feedbackBlock">

                                                        <div class="feedback-info">
                                                            <span class="commenter">Sam Brown</span>
                                                            <span class="feedbackdate">(Evaluator) • 3 days ago</span>
                                                        </div>
                                                        <p class="feedbackContent">
                                                            veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex non
                                                            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                                    </div>
                                                </div>




                                                                                                </div>




                                                                                                <div class="phaseSection">
                                                                                                    <p class="sectionTitle">
                                                                                                        5. Pitch Deck</p>

                                                                                                    <p class="projectOverviewDirection">Welcome! We're excited to learn more about your project.
                                                                                                        To facilitate a comprehensive review, please provide a PDF document including the
                                                                                                        following key elements:</p>
                                                                                                    <p class="projectOverviewDirection2">
                                                                                                        <b>Problem Description: </b> Briefly describe the problem your project addresses.<br>

                                                                                                        <b>Target Market Segment: </b>Define the specific audience or customer group your
                                                                                                        project targets.<br>

                                                                                                        <b>Solution: </b>Outline your project's solution to the identified problem.
                                                                                                        <br>
                                                                                                        <b>Features: </b>Highlight the key features or functionalities your project will offer.
                                                                                                        <br>
                                                                                                        <b>Progress/Traction: </b>Share any significant progress, milestones, or traction your
                                                                                                        project has achieved.
                                                                                                        <br>
                                                                                                        <b>Proposed Use of Funds: </b>Clearly state how you intend to use funds obtained from
                                                                                                        investors.
                                                                                                        <br><br>
                                                                                                        Keep responses concise and focused on key points.
                                                                                                        Ensure clarity and specificity in your descriptions.
                                                                                                        Use visuals or data that enhance the understanding of your project.
                                                                                                        We appreciate your participation and look forward to exploring the details of your
                                                                                                        project!
                                                                                                    </p><br>
                                                                                                    <h3 style="margin-top: 0;">
                                                                                                        <?php if (isset($project_name)) {
                                                                                                            echo $project_name;
                                                                                                        } ?>'s Current Pitch Deck
                                                                                                    </h3>

                                                                                                    <embed type="application/pdf" src="<?php echo $deckPitch; ?>">
                                                                                                    <br><br>


                                                                                                    <hr>
                                                                                                    <h3 style="font-size: 14px; margin: 0; margin-top: 15px;"><i class="fas fa-caret-down"></i>
                                                                                                        Change your Pitch Deck</h3>
                                                                                                    <span
                                                                                                        style="text-decoration: none; font-weight: 400; font-size: 12px; font-style:italic;">(Ignore
                                                                                                        this when you do not want to change your pitch deck)</span><br><br>




                                                                                                    <br>
                                                                                    
                                                                                                    <div class="pdf-preview2">
                                                                                                        PDF preview here.
                                                                                                    </div>
                                                                                                    <br><br>

                                                                                                    <label for="canvasFile2" class="fileInputLabel" id="canvasFile2Label"><i
                                                                                                            class="fa fa-upload" style="color: #0f73d2;"></i> Choose your new Pitch Deck</label>
                                                                                                    <input id="canvasFile2" class="fileInput" type="file" name="canvas_file2"
                                                                                                        accept="application/pdf" onchange="displayPdfFile2('canvasFile2')">
                                                                                                    <div>
                                                                                                        <a class="cancelUpdateCanvas" id="cancelUpdateCanvas2"
                                                                                                            onclick="cancelCanvasUpdate2()">Cancel</a>
                                                                                                    </div>


                                                                                                <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>

                                                                                                    <div class="feedbackSection">

                                                                                                        <div class="feedbackBlock">

                                                                                                            <div class="feedback-info">
                                                                                                                <span class="commenter">Moniqua Lee</span>
                                                                                                                <span class="feedbackdate">(Mentor) • 1hr ago</span>
                                                                                                            </div>
                                                                                                            <p class="feedbackContent">
                                                                                                                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                                                                                                                dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                                                                                                                sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                                                                                        </div>
                                                                                                        <div class="feedbackBlock">

                                                                                                            <div class="feedback-info">
                                                                                                                <span class="commenter">Sam Brown</span>
                                                                                                                <span class="feedbackdate">(Evaluator) • 3 days ago</span>
                                                                                                            </div>
                                                                                                            <p class="feedbackContent">
                                                                                                                veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex non
                                                                                                                proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>                                                                        




        <?php




                                                         } else {
                                                             //if wala pang record
                                                             ?>





                                                                                                <div class="phaseSection">
                                                                                                    <p class="sectionTitle">
                                                                                                        4. Video Pitch</p>

                                                                                                    <p class="projectOverviewDirection"><b>Congratulations on reaching the Pitching Phase!</b><br>

                                In this section, we invite you to present a dynamic and compelling video pitch that provides a comprehensive overview of your startup. To ensure a comprehensive review, please adhere to the following guidelines:</p>
                                                                                                    <p class="projectOverviewDirection2">
                                                                                                    When preparing your video pitch, succinctly describe the problem your project addresses, define the specific audience or customer group it targets, outline your project's solution, and highlight key features or functionalities it offers. Share any significant progress, milestones, or traction achieved, and clearly state how you plan to use funds obtained from investors. Ensure a concise presentation for an impactful and informative pitch.
                                                                                                        <br>
                                                                                                        <p class="projectOverviewDirection">
                                                                                                        <b>Duration: </b>Your video pitch should be between 3 to 5 minutes.<br>
                                                                        
                                                                                                        <b>Introduction of Founders: </b>Introduce all founders in the video.<br>
                                                                                                        <b>Rationale Behind Startup Creation: </b>Explain the reasons and motivations behind the creation of your startup.<br>
                                                                                                        <b>Natural Voices: </b>Utilize the natural voices of the founders; no text-to-speech enhancements are allowed.<br>
                                                                                                        <b>Demo (if applicable): </b>If a demo is included, it should not exceed 1 minute.<br><br>
                                                                                                        Thank you for sharing your startup pitch with us. We appreciate your efforts and look forward to the success of your innovative venture!</p>
                                                                             
                                                                                                    </p>


                                                                                                    <div class="video-preview-container">
                                                                                                        <video class="video-preview" controls>
                                                                                                            <source src="" type="video/mp4">
                                                                                                            Your browser does not support the video tag.
                                                                                                        </video>
                                                                                                    </div>

                                                                                                    <br><br>

                                                                                                    <!-- File Input and Label -->
                                                                                                    <label for="videoFile" class="fileInputLabel" id="videoFileLabel">
                                                                                                        <i class="fa fa-upload" style="color: #0f73d2;"></i> Choose your Video Pitch
                                                                                                    </label>
                                                                                                    <input id="videoFile" class="fileInput" type="file" name="video_pitch" accept="video/mp4"
                                                                                                        onchange="displayVideoFile('videoFile')">

                                                                                                    <!-- Cancel Update Button -->
                                                                                                    <div>
                                                                                                        <a class="cancel-update-button" id="cancelUpdateVideo"
                                                                                                            onclick="cancelVideoUpdate()">Cancel</a>
                                                                                                    </div>
                                                                                                    <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>

                                                <div class="feedbackSection">

                                                    <div class="feedbackBlock">

                                                        <div class="feedback-info">
                                                            <span class="commenter">Moniqua Lee</span>
                                                            <span class="feedbackdate">(Mentor) • 1hr ago</span>
                                                        </div>
                                                        <p class="feedbackContent">
                                                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                                                            dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                                                            sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                                    </div>
                                                    <div class="feedbackBlock">

                                                        <div class="feedback-info">
                                                            <span class="commenter">Sam Brown</span>
                                                            <span class="feedbackdate">(Evaluator) • 3 days ago</span>
                                                        </div>
                                                        <p class="feedbackContent">
                                                            veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex non
                                                            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                                    </div>
                                                </div>




                                                                                                </div>











                                                                                                <div class="phaseSection">
                                                                                                    <p class="sectionTitle">
                                                                                                        5. Pitch Deck</p>

                                                                                                    <p class="projectOverviewDirection">Welcome! We're excited to learn more about your project.
                                                                                                        To facilitate a comprehensive review, please provide a PDF document including the
                                                                                                        following key elements:</p>
                                                                                                    <p class="projectOverviewDirection2">
                                                                                                        <b>Problem Description: </b> Briefly describe the problem your project addresses.<br>

                                                                                                        <b>Target Market Segment: </b>Define the specific audience or customer group your
                                                                                                        project targets.<br>

                                                                                                        <b>Solution: </b>Outline your project's solution to the identified problem.
                                                                                                        <br>
                                                                                                        <b>Features: </b>Highlight the key features or functionalities your project will offer.
                                                                                                        <br>
                                                                                                        <b>Progress/Traction: </b>Share any significant progress, milestones, or traction your
                                                                                                        project has achieved.
                                                                                                        <br>
                                                                                                        <b>Proposed Use of Funds: </b>Clearly state how you intend to use funds obtained from
                                                                                                        investors.
                                                                                                        <br><br>
                                                                                                        Keep responses concise and focused on key points.
                                                                                                        Ensure clarity and specificity in your descriptions.
                                                                                                        Use visuals or data that enhance the understanding of your project.
                                                                                                        We appreciate your participation and look forward to exploring the details of your
                                                                                                        project!
                                                                                                    </p><br>
                                                                                    
                                                                                                    <div class="pdf-preview2">
                                                                                                        PDF preview here.
                                                                                                    </div>
                                                                                                    <br><br>

                                                                                                    <label for="canvasFile2" class="fileInputLabel" id="canvasFile2Label"><i
                                                                                                            class="fa fa-upload" style="color: #0f73d2;"></i> Choose your Pitch Deck</label>
                                                                                                    <input id="canvasFile2" class="fileInput" type="file" name="canvas_file2"
                                                                                                        accept="application/pdf" onchange="displayPdfFile2('canvasFile2')">
                                                                                                    <div>
                                                                                                        <a class="cancelUpdateCanvas" id="cancelUpdateCanvas2"
                                                                                                            onclick="cancelCanvasUpdate2()">Cancel</a>
                                                                                                    </div>


                                                                                                <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>

                                                                                                    <div class="feedbackSection">

                                                                                                        <div class="feedbackBlock">

                                                                                                            <div class="feedback-info">
                                                                                                                <span class="commenter">Moniqua Lee</span>
                                                                                                                <span class="feedbackdate">(Mentor) • 1hr ago</span>
                                                                                                            </div>
                                                                                                            <p class="feedbackContent">
                                                                                                                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                                                                                                                dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                                                                                                                sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                                                                                        </div>
                                                                                                        <div class="feedbackBlock">

                                                                                                            <div class="feedback-info">
                                                                                                                <span class="commenter">Sam Brown</span>
                                                                                                                <span class="feedbackdate">(Evaluator) • 3 days ago</span>
                                                                                                            </div>
                                                                                                            <p class="feedbackContent">
                                                                                                                veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex non
                                                                                                                proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>





                                                                                                <?php
                                                         }

                                                         ?>


                                      

        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                            <button class="saveBtn" name="btnSavePitch"
                                                                style="width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; border-radius:20px; padding: 20px; font-weight: bold;"
                                                                title="This will save your project. Easily return to your work later without losing progress.">Save Pitching Phase</button>
                                                        </div>

                                                    <br>

                                                        </form>
                                                        <a href="#" style="font-size: 15px; text-decoration: none; color: inherit;" onmouseover="this.style.color='#107cce'" onmouseout="this.style.color=''">
                                                            Back to Top
                                                        </a>
     <?php
// closing tag ideation
 }
?>                                              
                                                   
                                                   
                                                    </div>





                                                    <div class="section-content finish">
                                                    
    <?php
                                          //opening tag pitching
                                          if(!isset($ideationStatus) && !isset($pitchingStatus) ){
                                          ?>


<div class="phaseSection">
<img src="images/closedicon.png" alt="Logo_img" width="200px" height="200px" >
<p>Please be informed that <b>both Ideation and Pitching Phases</b> are essential for <b><?php echo $project_name;  ?>'s completion</b>. Your Ideation Phase requires approval before accessing details in the Pitching Phase. Kindly ensure both phases are successfully completed for continued progress. Thank you!</p>
</div>


                                          <?php
                                          
                                          
                                          }else
                                          if (!isset($ideationStatus) || strtoupper($ideationStatus) == "PENDING") {
?>

<div class="phaseSection">
<img src="images/closedicon.png" alt="Logo_img" width="200px" height="200px" >
<p>Please note that <b><?php echo $project_name;  ?>'s Ideation and Pitching Phase </b> requires approval from your evaluators before reaching here. Kindly ensure Ideation and Pitching Phase approval for continued progress. Thank you!</p>
</div>

<?php
 }else if(!isset($pitchingStatus) || strtoupper($pitchingStatus) == "PENDING"){
 ?>
<div class="phaseSection">
<img src="images/closedicon.png" alt="Logo_img" width="200px" height="200px" >
<p>Please note that <b><?php echo $project_name;  ?>'s Pitching Phase </b> requires approval from your evaluators before reaching here. Kindly ensure Pitching Phase approval for continued progress. Thank you!</p>
</div>

 <?php
 
 
 }else{
 ?>

                                                    <div class="phaseSection">
                                                    <div class="img-container" style="display: flex;
                                            justify-content: center;
                                                align-items: center;">
                                                                                                    <img src="images/successicon.png" alt="Logo_img" width="200px" height="200px" >
                                                                                                </div>


                                                                                                <p class="sectionTitle2">
                                                                                                   CONGRATULATIONS!</p>

                                                                                                   <p class="projectOverviewDirection">Kudos to the outstanding team powering <b><?php echo $project_name; ?>!</b> Your commitment and tireless efforts have propelled your startup project to an impressive stage. We're delighted to inform you that you now have the option to make your project public, paving the way for potential investors. This marks a significant milestone, and we're eager to witness your innovative venture garner the attention it rightfully deserves. Best of luck as you embark on this exciting next step! 🚀✨ <br><br><b style="color: #006BB9"> Please note that only the project creator has the ability to make the project public.</b></p><br>

                                                                                           


                                                    
                                                    </div>
                                                </div>

<?php
}// closing tag pitching
?>

                                            </div>




                                            <script src="scripts.js"></script>
                                            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                                            <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
                                            <script>
                                                $(".step").click(function () {
                                                    $(this).addClass("active").prevAll().addClass("active");
                                                    $(this).nextAll().removeClass("active");
                                                });

                                                $(".step01").click(function () {
                                                    $("#line-progress").css("width", "3%");
                                                    $(".ideation").addClass("active").siblings().removeClass("active");
                                                });

                                                $(".step02").click(function () {
                                                    $("#line-progress").css("width", "50%");
                                                    $(".pitching").addClass("active").siblings().removeClass("active");
                                                });

                                                $(".step03").click(function () {
                                                    $("#line-progress").css("width", "100%");
                                                    $(".finish").addClass("active").siblings().removeClass("active");
                                                });

                                                document.getElementById('make-public').addEventListener('click', function () {
                                                    alert('Your project is now public!');
                                                });

                                                document.getElementById('not-now').addEventListener('click', function () {
                                                    alert('Your project remains private.');
                                                });
                                            </script>


                                            <?php
    } else {
        echo "<script>alert('Project ID did not set.')</script>";
    }

    ?>
    </div>
    <script>
        function displayFileName(inputId) {
            const labelId = `${inputId}InputLabel`;
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);
            const fileName = input.files[0]?.name;

            const preview = document.querySelector('.image-preview');
            if (!fileName) {
                preview.innerHTML = 'Image Preview here.';
            }

            label.innerHTML = fileName
                ? `Selected file: ${fileName}`
                : '<i class="fa fa-upload" style="color: #0f73d2;"></i> Choose your New Project Logo';


            const cancelLink = document.getElementById('cancelUpdateLogo');

            if (fileName) {
                const file = input.files[0];
                const reader = new FileReader();

                reader.onload = function (e) {
                    const img = new Image();
                    img.src = e.target.result;
                    preview.innerHTML = '';
                    preview.appendChild(img);
                };

                reader.readAsDataURL(file);

                cancelLink.style.display = 'inline';
            } else {
                cancelLink.style.display = 'none';
            }
        }

        function cancelFileUpdate() {
            const input = document.getElementById('projectLogo');
            const label = document.getElementById('projectLogoInputLabel');
            const cancelLink = document.getElementById('cancelUpdateLogo');
            input.value = '';
            label.innerHTML = '<i class="fa fa-upload" style="color: #0f73d2;"></i> Choose your New Project Logo';

            const preview = document.querySelector('.image-preview');
            preview.innerHTML = 'Image Preview here.';
            cancelLink.style.display = 'none';
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const firstName = "<?php echo $fname ?>";
            const lastName = "<?php echo $lname ?>";
            const initials = getInitials(firstName, lastName);
            document.getElementById("initialsAvatar9").innerText = initials;
        });

        function getInitials(firstName, lastName) {
            return (
                (firstName ? firstName[0].toUpperCase() : "") +
                (lastName ? lastName[0].toUpperCase() : "")
            );
        }
    </script>
    <script>
        function displayPdfFile(inputId) {
            const labelId = `${inputId}InputLabel`;
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);
            const fileName = input.files[0]?.name;

            const preview = document.querySelector('.pdf-preview');
            const file = input.files[0];
            if (!fileName) {
                preview.innerHTML = 'PDF Preview here.';
            }

            label.innerHTML = fileName
                ? `Selected file: ${fileName}`
                : '<i class="fa fa-upload" style="color: #0f73d2;"></i> Choose your New Startup Model Canvas';


            const cancelLink = document.getElementById('cancelUpdateCanvas');

            if (file) {
                const objectUrl = URL.createObjectURL(file);

                const embed = document.createElement('embed');
                embed.type = 'application/pdf';
                embed.src = objectUrl;
                embed.width = '100%';
                embed.height = '500px';

                preview.innerHTML = '';
                preview.appendChild(embed);

                cancelLink.style.display = 'inline';
            } else {
                preview.innerHTML = 'No PDF selected';
                cancelLink.style.display = 'none';
            }
        }


        function cancelCanvasUpdate() {
            const input = document.getElementById('canvasFile');
            const label = document.getElementById('canvasFileInputLabel');
            const cancelLink = document.getElementById('cancelUpdateCanvas');
            input.value = '';
            label.innerHTML = '<i class="fa fa-upload" style="color: #0f73d2;"></i> Choose your New Startup Model Canvas';
            const preview = document.querySelector('.pdf-preview');
            preview.innerHTML = 'PDF Preview here.';
            cancelLink.style.display = 'none';
        }



    </script>
    <script>
        function displayPdfFile2(inputId) {
            const labelId = `${inputId}Label`;
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);
            const fileName = input.files[0]?.name;

            const preview = document.querySelector('.pdf-preview2');
            const file = input.files[0];
            if (!fileName) {
                preview.innerHTML = 'PDF Preview here.';
            }

            label.innerHTML = fileName
                ? `Selected file: ${fileName}`
                : '<i class="fa fa-upload" style="color: #0f73d2;"></i> Choose your New Startup Model Canvas';

            const cancelLink = document.getElementById('cancelUpdateCanvas2');

            if (file) {
                const objectUrl = URL.createObjectURL(file);

                const embed = document.createElement('embed');
                embed.type = 'application/pdf';
                embed.src = objectUrl;
                embed.width = '100%';
                embed.height = '500px';

                preview.innerHTML = '';
                preview.appendChild(embed);

                cancelLink.style.display = 'inline';
            } else {
                preview.innerHTML = 'No PDF selected';
                cancelLink.style.display = 'none';
            }
        }

        function cancelCanvasUpdate2() {
            const input = document.getElementById('canvasFile2');
            const label = document.getElementById('canvasFile2Label');
            const cancelLink = document.getElementById('cancelUpdateCanvas2');
            input.value = '';
            label.innerHTML = '<i class="fa fa-upload" style="color: #0f73d2;"></i> Choose your New Startup Model Canvas';
            const preview = document.querySelector('.pdf-preview2');
            preview.innerHTML = 'PDF Preview here.';
            cancelLink.style.display = 'none';
        }
    </script>
    <script>
        var modal = document.getElementById("editModal");
        var btn = document.getElementById("editComp");
        var span = document.getElementsByClassName("closeEditM")[0];
        var cancell = document.getElementById("cancelBTN");

        btn.onclick = function () {
            modal.style.display = "block";
            document.body.style.overflow = "hidden";
        }
        span.onclick = function () {
            modal.style.display = "none";
            document.body.style.overflow = "visible";
        }
        cancell.onclick = function () {
            modal.style.display = "none";
            document.body.style.overflow = "visible";
            location.reload();
        }

    </script>
    <script>
        var modall = document.getElementById("viewTeam");
        var btnn = document.getElementById("viewProjectTeam");
        var spann = document.getElementsByClassName("closeVT")[0];

        btnn.onclick = function () {
            modall.style.display = "block";
            document.body.style.overflow = "hidden";
        }
        spann.onclick = function () {
            modall.style.display = "none";
            document.body.style.overflow = "visible";
        }

    </script>
    <script>
        document.getElementById('editP').addEventListener('click', function () {
            document.querySelector('input[name="submit"]').style.visibility = 'visible';
            document.querySelector('p[style*="visibility: hidden;"]').style.visibility = 'visible';
            document.getElementById('project_name').removeAttribute('readonly');
            document.getElementById('project_description').removeAttribute('readonly');
        });
    </script>
    <script>
  function displayVideoFile(inputId) {
    const labelId = `${inputId}Label`;
    const input = document.getElementById(inputId);
    const label = document.getElementById(labelId);
    const fileName = input.files[0]?.name;

    const previewContainer = document.querySelector('.video-preview-container');
    const video = previewContainer.querySelector('.video-preview');
    const file = input.files[0];

    if (!fileName) {
      // Reset video player
      video.src = '';
    }

    label.innerHTML = fileName
      ? `Selected file: ${fileName}`
      : '<i class="fa fa-upload" style="color: #0f73d2;"></i> Choose your Video';

    const cancelLink = document.getElementById('cancelUpdateVideo');

    if (file) {
      const objectUrl = URL.createObjectURL(file);

      // Update video source
      video.src = objectUrl;

      // Display cancel button
      cancelLink.style.display = 'inline';
    } else {
      // Hide cancel button if no file selected
      cancelLink.style.display = 'none';
    }
  }

  function cancelVideoUpdate() {
    const input = document.getElementById('videoFile');
    const label = document.getElementById('videoFileLabel');
    const cancelLink = document.getElementById('cancelUpdateVideo');

    // Reset file input value
    input.value = '';

    // Reset label text
    label.innerHTML = '<i class="fa fa-upload" style="color: #0f73d2;"></i> Choose your Video';

    // Reset video player
    const previewContainer = document.querySelector('.video-preview-container');
    const video = previewContainer.querySelector('.video-preview');
    video.src = '';

    // Hide cancel button
    cancelLink.style.display = 'none';
  }
</script>
</body>

</html>