<?php
// get_student_details.php

// Database connection
$conn = mysqli_connect("localhost", "root", "", "launchpad") or die("error in connecting!");

// Function to sanitize input and prevent SQL injection
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, $input);
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["studentID"])) {
    $studentID = sanitize($_POST["studentID"]);

    // Fetch student details from the database
    $result = $conn->query("SELECT * FROM student_registration WHERE Student_ID = '$studentID'");

    if ($result->num_rows > 0) {
        $studentDetails = $result->fetch_assoc();

        // Return student details as JSON
        echo json_encode($studentDetails);
    } else {
        // Handle error if student ID is not found
        echo json_encode(["error" => "Student not found"]);
    }
} else {
    // Handle error if the request method is not POST or studentID is not set
    echo json_encode(["error" => "Invalid request"]);
}

// Close the database connection
$conn->close();
?>
