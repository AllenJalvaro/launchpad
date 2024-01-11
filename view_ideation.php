<?php
    require "config.php";

    if (empty($_SESSION["email"])) {
        header("Location: login.php");
        exit(); 
    }
    $instructorEmail = $_SESSION["email"];
    if (isset($_GET['ideation_id'])) {
        $ideation_id = $_GET['ideation_id'];
    }

    if (isset($_POST['btnCommentIdeation'])) {
        $commentInOverview = $_POST['commentOverview'];
        $commentInLogo = $_POST['commentLogo'];
        $commentInCanvas = $_POST['commentCanvas'];
        // echo "<script>alert('" . $commentInOverview."');</script>";

        $insertComment = mysqli_query($conn, "INSERT INTO comment_ideation VALUES ('', $ideation_id, '$commentInOverview', '$commentInLogo', '$commentInCanvas', NOW())");
        if ($insertComment) {
            echo "<script>alert('Your comment have been saved!')</script>";
        }else {
            echo "<script>alert('Error in saving the comment!')</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Teacher</title>
		<link rel="icon" href="/launchpad/images/favicon.svg" />
    <style>
        .project-container{
            background-color: white; /* Set the background color to white */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add a subtle box shadow for a floating effect */
            padding: 20px; /* Add some padding for space inside the container */
            border-radius: 8px; /* Optional: Add rounded corners for a softer look */
            margin: 20px; /* Optional: Add margin for space around the container */
            height: auto;
        }
        .project-logo {
        width: 250px; /* Set the width to your desired size */
        height: 250px; /* Set the height to your desired size */
        /* Optional: Add other styling properties like margin, padding, etc. */
        }

        .project-logo img {
            width: 100%; /* Make sure the image fills the entire container */
            height: 100%; /* Make sure the image fills the entire container */
            object-fit: cover; /* Optional: Maintain aspect ratio and cover the entire container */
            border-radius: 8px; /* Optional: Add rounded corners for the image */
        }
        embed{
        border: 2px solid black;
        margin-top: 30px;
      }
      .btnCommentIdeation{
        background-color: blue;
        border-radius: 5px;
        border: none;
        color: white;
        height: 50px;
        width: 50%;
        display: block;
        margin: 0 auto;
        margin-top: 30px;
      }
    </style>
</head>
<body>
    <aside class="sidebar">
        <header class="sidebar-header">
            <img src="\launchpad\images\logo-text.svg" class="logo-img">
        </header>
        <hr>
        <nav>
            <a href="teacher-dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'teacher-dashboard.php') ? 'active' : ''; ?>">
                <button>
                    <span>
                        <i><img src="\launchpad\images\home-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Home</span>
                    </span>
                </button>
            </a>

            <!-- Link to the Evaluation page -->
            <a href="evaluation.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'evaluation.php') ? 'active' : ''; ?>">
                <button>
                    <span>
                        <i><img src="\launchpad\images\evaluation-img.png" alt="evaluation-logo" class="logo-ic"></i>
                        <span>Evaluation</span>
                    </span>
                </button>
            </a>
            <br><br><br><br>
            <p>My companies</p>
            <a href="">
                <button>
                    <span>
                        <?php
                        echo $instructorEmail;
                        ?>
                    </span>
                </button>
            </a>
        </nav>
    </aside>


    <?php
        $query = "SELECT ideation_phase.Project_logo, ideation_phase.Project_Overview, ideation_phase.Project_Modelcanvas FROM ideation_phase WHERE ideation_phase.IdeationID=$ideation_id";
        $result = mysqli_query($conn, $query);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $logo = $row['Project_logo'];
                $overview = $row['Project_Overview'];
                $canvas = $row['Project_Modelcanvas'];
            }
        }
    ?>

    <div class="content">

    <h1>IDEATION PHASE </h1>
    <br><br>
        <!-- <p>This is where you can manage your home content.</p><br><br> -->
        <div class="project-container">
            <div class="content-project">
                <form action="" method="post">
                <h3>Project Overview: </h3>
                <textarea cols="130" rows="10" readonly><?php echo $overview ?></textarea>
                <br><br>
                <div class="comment-overview">
                    <h5>Comment: </h5>
                    <textarea name="commentOverview" id="commentOverview" cols="130" rows="5"></textarea>
                </div>
                <br><br>
                <div class="project-logo">
                    <h3>Project Logo: </h3>
                    <img src="<?php echo $logo ?>" alt="Project-logo">
                </div>
                <br><br><br><br>
                <div class="comment-logo">
                    <h5>Comment: </h5>
                    <textarea name="commentLogo" id="commentLogo" cols="130" rows="5"></textarea>
                </div><br><br>
                <h3>Project Model Canvass: </h3>
                <embed type="application/pdf" src="<?php echo $canvas; ?>" width="930" height="600">
                <div class="comment-canvas">
                    <h5>Comment: </h5>
                    <textarea name="commentCanvas" id="commentCanvas" cols="130" rows="5"></textarea>
                </div>
                <button name="btnCommentIdeation" class="btnCommentIdeation">Save</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap scripts (jQuery and Popper.js) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Include Bootstrap JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
