<?php
require "config.php";

if (isset($_POST["searchTerm"])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST["searchTerm"]);

    $searchQuery = "SELECT * FROM published_project INNER JOIN project ON published_project.Project_ID = project.Project_ID INNER JOIN ideation_phase ON project.Project_ID = ideation_phase.Project_ID INNER JOIN company_registration ON company_registration.Company_ID = project.Company_ID WHERE project.Project_title LIKE '%$searchTerm%'";
    $resultSearch = mysqli_query($conn, $searchQuery);

    if (mysqli_num_rows($resultSearch) > 0) {
        while ($row = mysqli_fetch_assoc($resultSearch)) {
            echo '<div class="row">';
            echo '<a class="col-md-4" href="published-proj-view.php?project_id=' . $row['PublishedProjectID'] . '">';
            echo '<div class="project-card">';
            echo '<img src="' . $row['Project_logo'] . '" alt="Project Image" class="project-img">';       
            echo '<div class="project-card-content">';
            echo '<div class="top-content">';
            echo '<h3 class="project-title">' . $row['Project_title'] . '</h3>';
            echo '<p class="date">' . date("F j, Y", strtotime($row['Published_date'])) . '</p>';
            echo '</div>';
            echo '<div class="project-overview">';
            echo '<p>' . $row['Project_Description'] . '</p>';
            echo '</div>';
            echo '<div class="bottom-content">';
            echo '<img src="' . $row['Company_logo'] . '" alt="Logo image" class="company-logo">';
            echo '<p class="company-name">' . $row['Company_name'] . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
    } else {
        echo '<p>No project found.</p>';
    }
} else {
    // Handle the case where no search term is provided
    echo "No search term provided.";
}
?>
