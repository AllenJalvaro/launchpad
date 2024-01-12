<?php

require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $query = mysqli_real_escape_string($conn, $_POST['query']);

 
    $searchEvaluatorsQuery = "SELECT Instructor_ID, Instructor_fname, Instructor_lname FROM instructor_registration WHERE CONCAT(Instructor_fname, ' ', Instructor_lname) LIKE '%$query%'";
    
    $resultEvaluators = mysqli_query($conn, $searchEvaluatorsQuery);

    
    while ($row = mysqli_fetch_assoc($resultEvaluators)) {
        echo '<div class="evaluator-result" data-id="' . $row['Instructor_ID'] . '">' . $row['Instructor_fname'] . ' ' . $row['Instructor_lname'] . '</div>';
    }
}
?>
