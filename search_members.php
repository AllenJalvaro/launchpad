<?php

require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $query = mysqli_real_escape_string($conn, $_POST['query']);
    $currentUserEmail = $_SESSION["email"];

   
    $searchMembersQuery = "SELECT Student_ID, Student_fname, Student_lname FROM student_registration
                           WHERE CONCAT(Student_fname, ' ', Student_lname) LIKE '%$query%'
                           AND Student_email != '$currentUserEmail'";
    
    $resultMembers = mysqli_query($conn, $searchMembersQuery);

    
    while ($row = mysqli_fetch_assoc($resultMembers)) {
        echo '<div class="member-result" data-id="' . $row['Student_ID'] . '">' . $row['Student_fname'] . ' ' . $row['Student_lname'] . '</div>';
    }
}
?>
