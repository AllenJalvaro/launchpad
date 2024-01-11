<?php
    require "config.php";

    if (empty($_SESSION["email"])) {
        header("Location: login.php");
        exit(); 
    }
    $instructorEmail = $_SESSION["email"];
    if (isset($_GET['pitching_id'])) {
        $pitching_id = $_GET['pitching_id'];
    }
    if (isset($_POST['btnCommentPitching'])) {
        $commentInVideo = $_POST['commentVideo'];
        $commentInDeck = $_POST['commentDeck'];
        // echo "<script>alert('" . $commentInOverview."');</script>";

        $insertComment = mysqli_query($conn, "INSERT INTO comment_pitching VALUES ('', $pitching_id, '$commentInVideo', '$commentInDeck', NOW())");
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
      .btnCommentPitching{
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
        $query = "SELECT pitching_phase.VideoPitch, pitching_phase.PitchDeck FROM pitching_phase WHERE pitching_phase.PitchingID=$pitching_id";
        $result = mysqli_query($conn, $query);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $video = $row['VideoPitch'];
                $deck = $row['PitchDeck'];
            }
        }
    ?>

    <div class="content">
        <br><br>
    <h1>PITCHING PHASE </h1>
        <!-- <p>This is where you can manage your home content.</p><br><br> -->
        <div class="project-container">
            <div class="content-project">
                <form action="" method="post">
                <div class="project-video">
                    <h3>Project Video Pitch: </h3>
                    <br>
                    <video width="930" height="400" controls>
                        <source src="videos/<?php echo $video ?>" type="video/mp4">
                    </video>
                </div>
                <div class="comment-video">
                    <h5>Comment: </h5>
                    <textarea name="commentVideo" id="commentVideo" cols="130" rows="5"></textarea>
                </div>
                <h3>Project Pitch Deck: </h3>
                <embed type="application/pdf" src="pdf/<?php echo $deck; ?>" width="930" height="600">
                <div class="comment-deck">
                    <h5>Comment: </h5>
                    <textarea name="commentDeck" id="commentDeck" cols="130" rows="5"></textarea>
                </div>
                <button name="btnCommentPitching" class="btnCommentPitching">Save</button>
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
