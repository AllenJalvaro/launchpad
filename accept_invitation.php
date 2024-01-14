<?php
require "config.php";

if (empty($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

//user info fetch
$userEmail = $_SESSION["email"];

$select_user_id = mysqli_query($conn, "SELECT Student_ID FROM student_registration WHERE Student_email='$userEmail'");
if (mysqli_num_rows($select_user_id) > 0) {
    $row = mysqli_fetch_assoc($select_user_id);
    $userID = $row['Student_ID'];
}

$invitationID = $_POST['invitationID'];
//Pending -> Confirmed
$confirmed = mysqli_query($conn, "UPDATE invitation SET Status='CONFIRMED' WHERE InvitationID=$invitationID");

//fetch projectid and inv_status
$select_project_id = mysqli_query($conn, "SELECT * FROM invitation i, project p WHERE i.InvitationID=$invitationID and i.projectid=p.project_id");
if (mysqli_num_rows($select_project_id) > 0) {
    $row = mysqli_fetch_assoc($select_project_id);
    $projectID = $row['ProjectID'];
    $inv_status = $row['Status'];
    $projT = $row['Project_title'];
}

//insert na member
if ($inv_status == 'CONFIRMED') {
    $insert_member = mysqli_query($conn, "INSERT INTO project_member VALUES ('', $projectID, '$userID')");
}

echo "You can now access the ".$projT." on Collab Projects";
?>
