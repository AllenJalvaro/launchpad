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
$confirmed = mysqli_query($conn, "UPDATE invitation SET Status='REJECTED' WHERE InvitationID=$invitationID");


echo "Invitation request has been deleted.";
?>
