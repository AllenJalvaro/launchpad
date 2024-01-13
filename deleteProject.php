<?php
require 'config.php';

if (isset($_POST['projecid'])) {
    $projectID = $_POST['projecid'];
    $deleteQuery = "DELETE FROM project WHERE project_id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $projectID);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error']);
}
mysqli_close($conn);
?>
