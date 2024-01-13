<?php
require 'config.php';

if (isset($_POST['copid'])) {
    $companyID = $_POST['copid'];
    $deleteQuery = "DELETE FROM company_registration WHERE company_id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $companyID);
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
