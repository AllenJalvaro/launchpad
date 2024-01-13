<?php
require "config.php";

if (isset($_POST["searchTerm"]) && isset($_POST["companyID"])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST["searchTerm"]);
    $companyID = mysqli_real_escape_string($conn, $_POST["companyID"]);

    $searchQuery = "SELECT * FROM project WHERE Company_ID = '$companyID' AND Project_title LIKE '%$searchTerm%' ORDER BY Project_date DESC";
    $resultSearch = mysqli_query($conn, $searchQuery);

    while ($row = mysqli_fetch_assoc($resultSearch)) {
        echo '<a href="project.php?project_id=' . $row['Project_ID'] . '" class="project-card">';
        echo '<div>';
        echo '<div class="project-title"><p style="   white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 90ch;">' . $row['Project_title'] . '</p></div>';
        echo '<div class="project-date">' . date('j M Y, g:i a', strtotime($row['Project_date'])) . '</div>';
        echo '</div>';
        echo '</a>';
    }
} else {
  //  echo "No search term or company ID provided.";
}
?>
