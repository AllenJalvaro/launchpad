<!-- SA FINAL NA PO TO SIR HEHE -->


<?php
require "config.php";

if (!empty($_SESSION["email"])) {
    if ($_SESSION["user_type"] === "teacher") {
        header("Location: teacher-dashboard.php");
    } elseif ($_SESSION["user_type"] === "student") {
        header("Location: index.php");
    }
    exit(); 
}

    $fetchPubProjects = "SELECT * FROM published_project LEFT JOIN project ON published_project.Project_ID = project.Project_ID LEFT JOIN ideation_phase ON ideation_phase.Project_ID = published_project.Project_ID LEFT JOIN company_registration ON project.Company_ID = company_registration.Company_ID WHERE published_project.Project_ID";
    $result = $conn->query($fetchPubProjects);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - Launchpad</title>
		<link rel="icon" href="/launchpad/images/favicon.svg" />
        <link rel="stylesheet" href="css/landingpage.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
    <!-- <h1>(LANDING PAGE)</h1>
        <a href="login.php" class="loginnn"><div class="loginn">LOGIN</div></a> -->
        <!-- <a href="registration.php"><div>JOIN</div></a> -->

        <div class="container">
            <nav>
                <img src="\launchpad\images\logo-text.svg" class="logo-img">
                <div class="left-btn">
                    <a href="login.php" class="login-btn">Students and Instructors</a>
                </div>
            </nav>

            <h1 class="header">
                Launchpad: Igniting Ideas, <br>Fostering Futures
            </h1>
            <p class="small-header">Explore Opportunities, Fuel Innovation: Your Gateway to Investing in Future Ventures</p>

            <!-- <div class="category">
                <a href="" class="active">All</a>
                <a href="">Category</a>
                <a href="">Category</a>
                <a href="">Category</a>
                <a href="">Category</a>
                <a href="">Category</a>
                <a href="">Category</a>
                <input type="search" placeholder="Search">
            </div> -->
            <h2 style="color: #006BB9;">Published Projects</h2>
            <!-- <div class="category">
                <input classs="search-input" type="search" placeholder="Search">
            </div> -->

            <div class="cards">
    <div class="row">
        <?php
            if ($result->num_rows > 0) {
                echo '<a class="card-link" href="#">';

                while ($row = $result->fetch_assoc()) {
        ?>
                    <div class="col-md-4 project-card">
                        <a class="card-link" href="published-proj-view.php?project_id=<?php echo $row['PublishedProjectID']; ?>">
                            <!-- Your card content here -->
                            <img src="<?php echo $row['Project_logo']; ?>" alt="" class="card-img">
                            <div class="top-card-content">
                                <p class="card-category"></p>
                                <p class="date"><?php echo date("F j, Y", strtotime($row['Published_date'])); ?></p>
                            </div>
                            <div class="project-content">
                                <h2 class="project-title"><?php echo $row['Project_title']; ?></h2>
                                <div class="project-desc">
                                    <p><?php echo $row['Project_Description']; ?></p>
                                </div>
                            </div>
                            <div class="bottom-card-content">
                                <img class="comp-logo" src="<?php echo $row['Company_logo']; ?>" alt="">
                                <p><?php echo $row['Company_name']; ?></p>
                            </div>
                        </a>
                    </div>
        <?php 
                }

                echo '</a>'; // Closing anchor tag after the loop
            }
        ?>
    </div>
</div>

        </div>
    
</body>
</html>