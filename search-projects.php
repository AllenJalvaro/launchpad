<?php
require "config.php";

if (isset($_POST["searchTerm"]) && isset($_POST["companyID"])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST["searchTerm"]);
    $companyID = mysqli_real_escape_string($conn, $_POST["companyID"]);

    // Assuming you have a 'project' table with a 'Project_title' column
    $searchQuery = "SELECT * FROM project WHERE Company_ID = '$companyID' AND Project_title LIKE '%$searchTerm%' ORDER BY Project_date DESC";
    $resultSearch = mysqli_query($conn, $searchQuery);

    while ($row = mysqli_fetch_assoc($resultSearch)) {
        echo '<a href="project.php?project_id=' . $row['Project_ID'] . '" class="project-card">';
        echo '<div>';
        echo '<div class="project-title">' . $row['Project_title'] . '</div>';
        echo '<div class="project-date">Date created: ' . date('m-d-y g:i A', strtotime($row['Project_date'])) . '</div>';
        echo '</div>';
        echo '</a>';
    }
} else {
    // Handle the case where no search term or company ID is provided
    echo "No search term or company ID provided.";
}
?>
