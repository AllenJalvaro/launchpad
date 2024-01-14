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
        echo '<div class="project-date">'; 
        date_default_timezone_set('Asia/Manila');

$projectDate = new DateTime($row['Project_date']);
$currentDate = new DateTime();
$timeElapsed = $currentDate->getTimestamp() - $projectDate->getTimestamp();

if ($timeElapsed < 60) {
    echo 'created Just Now';
} elseif ($timeElapsed < 3600) {
    $minutes = floor($timeElapsed / 60);
    echo 'created ' . (($minutes == 1) ? '1 min ago' : $minutes . ' mins ago');
} elseif ($timeElapsed < 86400) {
    $hours = floor($timeElapsed / 3600);
    echo 'created ' . (($hours == 1) ? '1 hr ago' : $hours . ' hrs ago');
} elseif ($timeElapsed < 604800) {
    $days = floor($timeElapsed / 86400);
    echo 'created ' . (($days == 1) ? '1 day ago' : $days . ' days ago');
} elseif ($timeElapsed < 1209600) { 
    echo 'created 1 week ago';
} elseif ($timeElapsed < 1814400) { 
    echo 'created 2 weeks ago';
} elseif ($timeElapsed < 2419200) {  
    echo 'created 3 weeks ago';
} else {
    echo 'created on ' . $projectDate->format('j M Y, g:i a');
}
        
        
        
        echo '</div>';
        echo '</div>';
        echo '</a>';
    }
} else {
  //  echo "No search term or company ID provided.";
}
?>
