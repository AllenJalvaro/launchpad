<?php
require "config.php";

if (empty($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION["email"];
$project_id = $_GET['project_id'];
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


//IDEATION BACKEND SUBMIT
if (isset($_POST['submitBtn'])) {
    // Count ideation phase
    $count_ideation_phase_query = "SELECT COUNT(ideation_phase.IdeationID) AS Count 
                                    FROM ideation_phase 
                                    INNER JOIN project ON ideation_phase.Project_ID=project.Project_ID 
                                    WHERE project.Project_ID=$project_id;";
    $count_ideation_phase_result = mysqli_query($conn, $count_ideation_phase_query);

    if (mysqli_num_rows($count_ideation_phase_result) > 0) {
        $row = mysqli_fetch_assoc($count_ideation_phase_result);
        $count = $row['Count'];

        if ($count > 0) {
            // Update existing project

            $project_overview = $_POST['project_overview'];

            // Logo upload
            $logo = $_FILES['project_logo']['name'];
            $logo_tmp = $_FILES['project_logo']['tmp_name'];
            $logo_store = "images/" . $logo;

            // Check if the file is an image
            $logoFileType = strtolower(pathinfo($logo_store, PATHINFO_EXTENSION));
            if (!empty($logo) && !in_array($logoFileType, array('jpg', 'jpeg', 'png', 'gif'))) {
                echo "<script>alert('File must be an image');</script>";
            } else {
                if (!empty($logo)) {
                    move_uploaded_file($logo_tmp, $logo_store);
                }

                // Model Canvas (PDF) upload
                $model_canvas = $_FILES['model_canvas']['name'];
                $model_canvas_tmp = $_FILES['model_canvas']['tmp_name'];
                $model_canvas_store = "pdf/" . $model_canvas;

                // Check if a file was uploaded for the model canvas
                if (!empty($model_canvas)) {
                    // Check if the file is a PDF
                    $pdfFileType = strtolower(pathinfo($model_canvas_store, PATHINFO_EXTENSION));
                    if ($pdfFileType != 'pdf') {
                        echo "<script>alert('File must be a PDF');</script>";
                    } else {
                        // Check if the PDF file already exists
                        if (file_exists($model_canvas_store)) {
                            echo "<script>alert('A file with the same name already exists. Please choose a different file name for the PDF');</script>";
                        } else {
                            move_uploaded_file($model_canvas_tmp, $model_canvas_store);

                            // Update data in the database
                            $update_query = "UPDATE ideation_phase 
                            SET 
                               Project_logo = " . (!empty($logo) ? "'$logo_store'" : "Project_logo") . ",
                               Project_Overview = '$project_overview',
                               Project_Modelcanvas = '$model_canvas_store',
                               Submission_date = NOW()
                            WHERE 
                               Project_ID = $project_id";

                            $update_result = mysqli_query($conn, $update_query);

                            if ($update_result) {
                                echo "<script>alert('Your project have been submitted to the evaluator!');</script>";
                            } else {
                                echo "<script>alert('Error updating data in the database.');</script>";
                            }
                        }
                    }
                } else {
                    // Update data in the database without changing the model canvas
                    $update_query = "UPDATE ideation_phase 
                    SET 
                       Project_logo = " . (!empty($logo) ? "'$logo_store'" : "Project_logo") . ",
                       Project_Overview = '$project_overview',
                       Submission_date = NOW()
                    WHERE 
                       Project_ID = $project_id";

                    $update_result = mysqli_query($conn, $update_query);

                    if ($update_result) {
                        echo "<script>alert('Your Project have been submitted to the evaluator!');</script>";
                    } else {
                        echo "<script>alert('Error updating data in the database.');</script>";
                    }
                }
            }
        } else {
            // Insert new project

            $project_overview = $_POST['project_overview'];

            // Logo upload
            $logo = $_FILES['project_logo']['name'];
            $logo_tmp = $_FILES['project_logo']['tmp_name'];
            $logo_store = "images/" . $logo;

            // Check if the file is an image
            $logoFileType = strtolower(pathinfo($logo_store, PATHINFO_EXTENSION));
            if (!in_array($logoFileType, array('jpg', 'jpeg', 'png', 'gif'))) {
                echo "<script>alert('File must be an image');</script>";
            } else {
                move_uploaded_file($logo_tmp, $logo_store);

                // Model Canvas (PDF) upload
                $model_canvas = $_FILES['model_canvas']['name'];
                $model_canvas_tmp = $_FILES['model_canvas']['tmp_name'];
                $model_canvas_store = "pdf/" . $model_canvas;

                // Check if the file is a PDF
                $pdfFileType = strtolower(pathinfo($model_canvas_store, PATHINFO_EXTENSION));
                if ($pdfFileType != 'pdf') {
                    echo "<script>alert('File must be a PDF');</script>";
                } else {
                    // Check if the PDF file already exists
                    if (file_exists($model_canvas_store)) {
                        echo "<script>alert('A file with the same name already exists. Please choose a different file name for the PDF');</script>";
                    } else {
                        move_uploaded_file($model_canvas_tmp, $model_canvas_store);

                        // Insert data into the database
                        $insert_query = "INSERT INTO ideation_phase 
                                         VALUES ('', $project_id, '$logo_store', '$project_overview', '$model_canvas_store', NOW())";
                        $insert_result = mysqli_query($conn, $insert_query);

                        if ($insert_result) {
                            echo "<script>alert('Your data have been submitted to the evaluator!');</script>";
                        } else {
                            echo "<script>alert('Error inserting data into the database.');</script>";
                        }
                    }
                }
            }
        }
    }

}

//IDEATION BACKEND SAVED

if (isset($_POST['btnSave'])) {

    // Count ideation phase
    $count_ideation_phase_query = "SELECT COUNT(ideation_phase.IdeationID) AS Count 
                                    FROM ideation_phase 
                                    INNER JOIN project ON ideation_phase.Project_ID=project.Project_ID 
                                    WHERE project.Project_ID=$project_id;";
    $count_ideation_phase_result = mysqli_query($conn, $count_ideation_phase_query);

    if (mysqli_num_rows($count_ideation_phase_result) > 0) {
        $row = mysqli_fetch_assoc($count_ideation_phase_result);
        $count = $row['Count'];

        if ($count > 0) {
            // Update existing project

            $project_overview = $_POST['project_overview'];

            // Logo upload
            $logo = $_FILES['project_logo']['name'];
            $logo_tmp = $_FILES['project_logo']['tmp_name'];
            $logo_store = "images/" . $logo;

            // Check if the file is an image
            $logoFileType = strtolower(pathinfo($logo_store, PATHINFO_EXTENSION));
            if (!empty($logo) && !in_array($logoFileType, array('jpg', 'jpeg', 'png', 'gif'))) {
                echo "<script>alert('File must be an image');</script>";
            } else {
                if (!empty($logo)) {
                    move_uploaded_file($logo_tmp, $logo_store);
                }

                // Model Canvas (PDF) upload
                $model_canvas = $_FILES['model_canvas']['name'];
                $model_canvas_tmp = $_FILES['model_canvas']['tmp_name'];
                $model_canvas_store = "pdf/" . $model_canvas;

                // Check if a file was uploaded for the model canvas
                if (!empty($model_canvas)) {
                    // Check if the file is a PDF
                    $pdfFileType = strtolower(pathinfo($model_canvas_store, PATHINFO_EXTENSION));
                    if ($pdfFileType != 'pdf') {
                        echo "<script>alert('File must be a PDF');</script>";
                    } else {
                        // Check if the PDF file already exists
                        if (file_exists($model_canvas_store)) {
                            echo "<script>alert('A file with the same name already exists. Please choose a different file name for the PDF');</script>";
                        } else {
                            move_uploaded_file($model_canvas_tmp, $model_canvas_store);

                            // Update data in the database
                            $update_query = "UPDATE ideation_phase 
                            SET 
                               Project_logo = " . (!empty($logo) ? "'$logo_store'" : "Project_logo") . ",
                               Project_Overview = '$project_overview',
                               Project_Modelcanvas = '$model_canvas_store',
                               Submission_date = NOW()
                            WHERE 
                               Project_ID = $project_id";

                            $update_result = mysqli_query($conn, $update_query);

                            if ($update_result) {
                                echo "<script>alert('Saved successfully!');</script>";
                            } else {
                                echo "<script>alert('Error updating data in the database.');</script>";
                            }
                        }
                    }
                } else {
                    // Update data in the database without changing the model canvas
                    $update_query = "UPDATE ideation_phase 
                    SET 
                       Project_logo = " . (!empty($logo) ? "'$logo_store'" : "Project_logo") . ",
                       Project_Overview = '$project_overview',
                       Submission_date = NOW()
                    WHERE 
                       Project_ID = $project_id";

                    $update_result = mysqli_query($conn, $update_query);

                    if ($update_result) {
                        echo "<script>alert('Saved successfully!');</script>";
                    } else {
                        echo "<script>alert('Error updating data in the database.');</script>";
                    }
                }
            }
        } else {
            // Insert new project

            $project_overview = $_POST['project_overview'];

            // Logo upload
            $logo = $_FILES['project_logo']['name'];
            $logo_tmp = $_FILES['project_logo']['tmp_name'];
            $logo_store = "images/" . $logo;

            // Check if the file is an image
            $logoFileType = strtolower(pathinfo($logo_store, PATHINFO_EXTENSION));
            if (!in_array($logoFileType, array('jpg', 'jpeg', 'png', 'gif'))) {
                echo "<script>alert('File must be an image');</script>";
            } else {
                move_uploaded_file($logo_tmp, $logo_store);

                // Model Canvas (PDF) upload
                $model_canvas = $_FILES['model_canvas']['name'];
                $model_canvas_tmp = $_FILES['model_canvas']['tmp_name'];
                $model_canvas_store = "pdf/" . $model_canvas;

                // Check if the file is a PDF
                $pdfFileType = strtolower(pathinfo($model_canvas_store, PATHINFO_EXTENSION));
                if ($pdfFileType != 'pdf') {
                    echo "<script>alert('File must be a PDF');</script>";
                } else {
                    // Check if the PDF file already exists
                    if (file_exists($model_canvas_store)) {
                        echo "<script>alert('A file with the same name already exists. Please choose a different file name for the PDF');</script>";
                    } else {
                        move_uploaded_file($model_canvas_tmp, $model_canvas_store);

                        // Insert data into the database
                        $insert_query = "INSERT INTO ideation_phase 
                                         VALUES ('', $project_id, '$logo_store', '$project_overview', '$model_canvas_store', NOW())";
                        $insert_result = mysqli_query($conn, $insert_query);

                        if ($insert_result) {
                            echo "<script>alert('Data inserted successfully!');</script>";
                        } else {
                            echo "<script>alert('Error inserting data into the database.');</script>";
                        }
                    }
                }
            }
        }
    }
}

if (isset($_POST['submitBtnPitching'])) {
    $video_pitch_name = $_FILES['video_pitch']['name'];
    $video_pitch_tmp_name = $_FILES['video_pitch']['tmp_name'];
    $video_pitch_error = $_FILES['video_pitch']['error'];

    $pitch_deck_name = $_FILES['pitch_deck']['name'];
    $pitch_deck_tmp_name = $_FILES['pitch_deck']['tmp_name'];
    $pitch_deck_error = $_FILES['pitch_deck']['error'];

    // Handle Video Pitch
    if ($video_pitch_error === 0) {
        $video_pitch_ex = pathinfo($video_pitch_name, PATHINFO_EXTENSION);
        $video_pitch_ex_lc = strtolower($video_pitch_ex);
        $allowed_video_exs = array("mp4", "webm", "avi", "flv");

        if (in_array($video_pitch_ex_lc, $allowed_video_exs)) {
            $new_video_pitch_name = uniqid("video-", true) . '.' . $video_pitch_ex_lc;
            $video_pitch_upload_path = 'videos/' . $new_video_pitch_name;
            move_uploaded_file($video_pitch_tmp_name, $video_pitch_upload_path);

            // Handle Pitch Deck
            if ($pitch_deck_error === 0) {
                $pitch_deck_ex = pathinfo($pitch_deck_name, PATHINFO_EXTENSION);
                $pitch_deck_ex_lc = strtolower($pitch_deck_ex);
                $allowed_pitch_deck_exs = array("pdf");

                if (in_array($pitch_deck_ex_lc, $allowed_pitch_deck_exs)) {
                    $new_pitch_deck_name = uniqid("pitch_deck-", true) . '.' . $pitch_deck_ex_lc;
                    $pitch_deck_upload_path = 'pdf/' . $new_pitch_deck_name;
                    move_uploaded_file($pitch_deck_tmp_name, $pitch_deck_upload_path);

                    // Insert both paths into the database
                    $sql = "INSERT INTO pitching_phase VALUES ('', $project_id, '$new_video_pitch_name', '$new_pitch_deck_name', NOW())";
                    mysqli_query($conn, $sql);

                } else {
                    echo "<script>alert('You can\'t upload files of this type for Pitch Deck');</script>";
                }
            }
        } else {
            echo "<script>alert('You can\'t upload files of this type for Video Pitch');</script>";
        }
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

        .evalBtn {
            background-color: transparent;
            color: #006BB9;
            border: 1px solid #006BB9;

        }


        .evalBtn:hover {
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
        .editProjectBTN:hover {
            background: #1591fd23;
            border-radius: 10px;
        }

        .editProjectBTN {
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

        .pdf-preview{
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
        $_SESSION['projecid'] = $project_id;
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
                        window.location.href = 'project.php?project_id=" . $_SESSION['projecid'] . "';
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


                <button id="viewComp" class="editProjectBTN"><i class="fas fa-users"></i> Project Team</button>



                <button id="editComp" class="editProjectBTN"><i class="fas fa-info-circle" title="Information"></i> Project
                    Description</button>



                <form action="" method="post">
                    <input type="hidden" name="deleteProject">
                    <button type="submit" class="deleteProjectBTN"><i class="fas fa-trash-alt"></i> Delete Project</button>
                </form>

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
                            <textarea id="project_description" name="project_description" rows="8" required readonly
                                class="prodesc"><?php echo $row['Project_Description']; ?></textarea>

                        <?php } ?>

                        <input type="submit" value="Save Changes" name="submit" style="visibility: hidden;"><br>
                        <p id="cancelBTN" style="text-align: center; cursor: pointer; visibility: hidden;">Cancel</p>

                        <br>
                    </form>
                </div>
            </div>














            <!-- echo the company id here -->
            <div
                style="text-decoration: none; display: flex; align-items: center; justify-content: space-between; width: 90%; padding: 30px; margin: 15px; height: 80px;">

                <div style="text-align: left; position:fixed; margin: -50px !important">
                    <span style="font-size: medium;">
                        <a href="company_view.php?Company_id=<?php echo $_SESSION['copid']; ?>"
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
                            $select_ideation_phase = mysqli_query($conn, "SELECT ideation_phase.Project_Overview, ideation_phase.Project_logo, ideation_phase.Project_Modelcanvas FROM ideation_phase INNER JOIN project ON ideation_phase.Project_ID=project.Project_ID WHERE project.Project_ID=$project_id LIMIT 1;");

                            if (mysqli_num_rows($select_ideation_phase) > 0) {
                                $row = mysqli_fetch_assoc($select_ideation_phase);
                                $overview = $row['Project_Overview'];
                                //if merong record
                                ?>
                                <!-- <label for="project_overview">
                                    <h5>Project Overview: </h5>
                                </label>
                                <textarea name="project_overview" cols="100" rows="30"><?php //if (isset($overview))
                                        //echo $overview ?></textarea>

                                    <h5>Comment overview</h5>
                                    <textarea cols="100" rows="5" readonly><?php // if (isset($comment_overview))
                                            //echo $comment_overview; ?></textarea> -->

                                <div class="phaseSection">
                                    <p class="sectionTitle">
                                        1. Project Overview</p>

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
<!-- DITOKANA -->
                                    <textarea class="projectOverview-textarea" name="project_overview" cols="100" rows="20"
                                        required
                                        placeholder="Write your project overview here..." style="margin-top: 0;"><?php echo $overview ?></textarea>

                                    <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>
                                    <div class="feedbackSection">

                                        <div class="feedbackBlock">

                                            <div class="feedback-info">
                                                <span class="commenter">Moniqua Lee</span>
                                                <span class="feedbackdate">(Mentor) • 1hr ago</span>
                                            </div>
                                            <p class="feedbackContent">Oh
                                                that's beautiful!Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                                eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                                veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                                                dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                                                sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                        </div>
                                        <div class="feedbackBlock">

                                            <div class="feedback-info">
                                                <span class="commenter">Sam Brown</span>
                                                <span class="feedbackdate">(Evaluator) • 3 days ago</span>
                                            </div>
                                            <p class="feedbackContent">Oh
                                                that's beautiful!Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                                eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
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
                                        1. Project Overview</p>

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

                                    <div class="feedbackSection">

                                        <div class="feedbackBlock">

                                            <div class="feedback-info">
                                                <span class="commenter">Moniqua Lee</span>
                                                <span class="feedbackdate">(Mentor) • 1hr ago</span>
                                            </div>
                                            <p class="feedbackContent">Oh
                                                that's beautiful!Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                                eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                                veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                                                dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                                                sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                        </div>
                                        <div class="feedbackBlock">

                                            <div class="feedback-info">
                                                <span class="commenter">Sam Brown</span>
                                                <span class="feedbackdate">(Evaluator) • 3 days ago</span>
                                            </div>
                                            <p class="feedbackContent">Oh
                                                that's beautiful!Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                                eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                                veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex non
                                                proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                        </div>
                                    </div>
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
                                <!-- <h3>Project Logo:</h3> -->


                                <!-- <label for="project_logo">
                                    <h5>Select another logo:</h5>
                                </label>
                                <input type="file" name="project_logo">

                                <h5>Comment Logo</h5>
                                <textarea cols="100" rows="5" readonly><?php //if (isset($comment_logo)) {
                                        //echo $comment_logo;
                                        //} ?></textarea> -->




                                <div class="phaseSection">
                                    <p class="sectionTitle">
                                        2. Project Logo</p>

                                    <p class="projectOverviewDirection">In this section, kindly upload the logo your project's
                                        logo in PNG format. Ensure the logo is original, capturing the essence of your project.
                                        It should be a visual representation that reflects the core values and identity of your
                                        startup</p><br>
                                    <h3  style="margin-top: 0;">
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
                                <!-- <label for="project_logo">
                                    <h5>Project Logo:</h5>
                                </label>
                                <input type="file" name="project_logo" required> -->

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
                                        <h3  style="margin-top: 0;">
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


                                    <label for="canvasFile" class="fileInputLabel" id="canvasFileInputLabel"><i
                                            class="fa fa-upload" style="color: #0f73d2;"></i> Upload your Startup Model
                                        Canvas</label>
                                    <input id="canvasFile" class="fileInput" type="file" name="canvas_file"
                                        accept="application/pdf" onchange="displayFileName('canvasFile')">

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

                            <!-- <div style="align-self: center;">
                                <button class="saveBtn" name="btnSave">SUBMIT</button>
                                <button class="submitBtn" name="submitBtn">---</button>
                            </div> -->
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <button class="saveBtn" name="btnSave"
                                    style="width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; border-radius:20px; padding: 20px; font-weight: bold;"
                                    title="This will save your project's ideation phase. Easily return to your work later without losing progress.">Save</button>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <button class="mentorBtn" name="mentorBtn"
                                    style="width:100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;border-radius:20px; padding: 20px; font-weight: bold;"
                                    title="This will save and submit your project's ideation phase to your mentor.">Send
                                    to Mentor</button>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <!-- <button class="evalBtn" name="evalBtn" style="width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;border-radius:20px; padding: 20px; font-weight: bold; color: ;" title="You can only send to evaluators when your Ideation Phase is fully approved">Send to Evaluators</button> -->
                            </div>


                        </form>
                    </div>

                    <div class="section-content pitching">
                        <form action="" method="post" enctype="multipart/form-data">
                            <?php
                            $select_pitching_phase = mysqli_query($conn, "SELECT pitching_phase.VideoPitch, pitching_phase.PitchDeck FROM pitching_phase INNER JOIN project ON pitching_phase.Project_ID=project.Project_ID WHERE project.Project_ID=$project_id");

                            if (mysqli_num_rows($select_pitching_phase) > 0) {
                                $row = mysqli_fetch_assoc($select_pitching_phase);
                                $video_pitch = $row['VideoPitch'];
                                //if merong record
                                ?>
                                <h3>Project Video Pitch:</h3>

                                <video width="580" height="400" controls>
                                    <source src="videos/<?php echo $video_pitch ?>" type="video/mp4">
                                </video>
                                <label for="project_video">
                                    <h5>Select another video:</h5>
                                </label>
                                <input type="file" name="video_pitch">

                                <h5>Comment Video</h5>
                                <textarea cols="100" rows="5" readonly><?php echo $comment_video ?></textarea>
                                <?php
                            } else {
                                //if wala pang record
                                ?>
                                <label for="project_video">
                                    <h5>Project Video Pitch :</h5>
                                </label>
                                <input type="file" name="video_pitch" required>
                                <?php
                            }

                            ?>


                            <?php
                            $select_pitching_phase = mysqli_query($conn, "SELECT pitching_phase.VideoPitch, pitching_phase.PitchDeck FROM pitching_phase INNER JOIN project ON pitching_phase.Project_ID=project.Project_ID WHERE project.Project_ID=$project_id;");

                            if (mysqli_num_rows($select_pitching_phase) > 0) {
                                $row = mysqli_fetch_assoc($select_pitching_phase);
                                $PitchDeck = $row['PitchDeck'];
                                //if merong record
                                ?>
                                <h3>Project Pitch Deck:</h3>
                                <embed type="application/pdf" src="pdf/<?php echo $PitchDeck; ?>" width="580" height="600">

                                <label for="model_canvas">
                                    <h5>Select Another Pitch Deck: </h5>
                                </label>
                                <input type="file" name="pitch_deck">

                                <h5>Comment Pitch Deck</h5>
                                <textarea cols="100" rows="5" readonly><?php echo $comment_deck ?></textarea>

                                <?php
                            } else {
                                //if wala pang record
                                ?>
                                <label for="model_canvas">
                                    <h5>Startup Model Canvas: </h5>
                                </label>
                                <input type="file" name="pitch_deck" required>
                                <?php
                            }

                            ?>

                            <div>
                                <button class="submitBtnPitching" name="submitBtnPitching">SUBMIT</button>
                            </div>

                        </form>
                    </div>

                    <div class="section-content finish">
                        <div class="promotion-ui">
                            <img src="images/promotion_img.png" alt="promotion image">
                            <h1
                                style="text-decoration: none; line-height: normal; font-family: Arial, Helvetica, sans-serif; font-style: normal; text-transform: none; text-decoration: none; letter-spacing: normal; text-align: ;">
                                Your project is now ready for promotion!</h1>
                            <p>Do you want to make your project public?</p>
                            <button id="make-public">MAKE PUBLIC</button>
                            <button id="not-now">NOT NOW</button>
                        </div>
                        <script src="scripts.js"></script>
                    </div>
                </div>
            </div>
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
        document.getElementById('editP').addEventListener('click', function () {
            document.querySelector('input[name="submit"]').style.visibility = 'visible';
            document.querySelector('p[style*="visibility: hidden;"]').style.visibility = 'visible';
            document.getElementById('project_name').removeAttribute('readonly');
            document.getElementById('project_description').removeAttribute('readonly');
        });
    </script>
</body>

</html>