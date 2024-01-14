<?php
    require "config.php";

    $fetchPublishedProj = "SELECT * FROM published_project INNER JOIN project ON published_project.Project_ID = project.Project_ID INNER JOIN ideation_phase ON project.Project_ID = ideation_phase.Project_ID INNER JOIN company_registration ON company_registration.Company_ID = project.Company_ID WHERE published_project.PublishedProjectID";
    $pubProjs = $conn->query($fetchPublishedProj);
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        .container {
            max-width: 1200px;
            width: 100%;
        }
    </style>
</head>
<body>
   <div class="container">
        <nav>
            <img src="\launchpad\images\logo-text.svg" class="logo-img">
            <a href="login.php" class="login-reg-btn">
                <button>Student and Instructors</button>
            </a>
        </nav>

        <h1 class="hero-text">LaunchPad: Igniting Ideas,<br>Fostering Futures</h1>
        <p class="hero-small-text">Explore Opportunities, Fuel Innovation: Your Gateway to Investing in Future Ventures</p>

        <div class="search-bar">
            <input type="text" id="projectSearch" name="projectSearch" placeholder="Search any project..."></a></span> 
        </div>

        <main class="projects">
            <div class="row">
                <?php
                    if ($pubProjs->num_rows > 0) {
                        while ($row = $pubProjs->fetch_assoc()) {
                ?>
                <a class="col-md-4" href="published-proj-view.php?project_id=<?php echo $row['PublishedProjectID']; ?>">
                    <div class="project-card">
                        <img src="<?php echo $row['Project_logo']; ?>" alt="Project Image" class="project-img">
                        
                        <div class="project-card-content">
                            <div class="top-content">
                                <h3 class="project-title"><?php echo $row['Project_title']; ?></h3>
                                <p class="date"><?php echo date("F j, Y", strtotime($row['Published_date'])); ?></p>
                            </div>

                            <div class="project-overview">
                                <p><?php echo $row['Project_Description']; ?></p>
                            </div>
                            <div class="bottom-content">
                                <img src="<?php echo $row['Company_logo']; ?>" alt="Logo image" class="company-logo">
                                <p class="company-name"><?php echo $row['Company_name']; ?></p>
                            </div>
                        </div>
                    </div>
                </a>
                <?php
                        }
                    }
                ?>
            </div>
        </main>
   </div>

    <script>
        $(document).ready(function() {
            var originalContent = $(".projects").html();

            $("#projectSearch").on("input", function() {
                var searchTerm = $(this).val();

                if (searchTerm.length > 0) {
                    $.ajax({
                        type: "POST",
                        url: "search_pub_projects.php",
                        data: { searchTerm: searchTerm },
                        success: function(response) {
                            $(".projects").empty();
                            $(".col-md-4").hide();
                            $(response).appendTo(".projects");
                        }
                    });
                } else {
                    $(".projects").html(originalContent);
                    $(".col-md-4").show();
                }
            });
        });
    </script>
</body>
</html>