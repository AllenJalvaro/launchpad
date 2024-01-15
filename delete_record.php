<?php
require("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recordId']) && isset($_POST['recordType'])) {
    $recordId = $_POST['recordId'];
    $recordType = $_POST['recordType'];

    // Perform the deletion based on record type
    switch ($recordType) {
        case 'student':
            $sql = "DELETE FROM student_registration WHERE Student_ID = ?";
            break;
        case 'instructor':
            $sql = "DELETE FROM instructor_registration WHERE Instructor_ID = ?";
            break;
        // Add more cases for other record types if needed

        default:
            echo "Invalid record type";
            exit();
    }

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }

    $stmt->bind_param('s', $recordId);

    echo "SQL Statement: " . $sql; // Add this line for debugging

    if ($stmt->execute()) {
        echo "Record deleted successfully";
    } else {
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
