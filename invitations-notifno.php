<?php

require 'config.php';

$userEmail = $_SESSION['email'];

// Use prepared statement to prevent SQL injection
$notifquery = "SELECT * FROM invitation, student_registration WHERE invitation.inviteeid = student_registration.student_id AND student_email = ?";
$stmt = mysqli_prepare($conn, $notifquery);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $userEmail);
    mysqli_stmt_execute($stmt);

    // Check if the query execution is successful
    if ($result = mysqli_stmt_get_result($stmt)) {
        // Fetch the number of rows
        $numRows = $result->num_rows;

        // Output the result
        echo $numRows;

        // Free the result set
        mysqli_free_result($result);
    } else {
        // Handle the case when the result is not available
        echo "Error fetching result.";
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    // Handle the case when the statement preparation fails
    echo "Error preparing statement.";
}

?>
