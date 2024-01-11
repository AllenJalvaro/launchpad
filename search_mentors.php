<?php

require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $query = mysqli_real_escape_string($conn, $_POST['query']);

 
    $searchMentorsQuery = "SELECT Instructor_ID, Instructor_fname, Instructor_lname FROM instructor_registration WHERE CONCAT(Instructor_fname, ' ', Instructor_lname) LIKE '%$query%'";
    
    $resultMentors = mysqli_query($conn, $searchMentorsQuery);

    
    while ($row = mysqli_fetch_assoc($resultMentors)) {
        echo '<div class="mentor-result" data-id="' . $row['Instructor_ID'] . '">' . $row['Instructor_fname'] . ' ' . $row['Instructor_lname'] . '</div>';
    }
}
?>
