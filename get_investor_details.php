<?php
    require "config.php";

    // Get InvestorID from the AJAX request
    $investorID = $_GET['investorID'];

    // Fetch investor details
    $query = "SELECT * FROM investor_request 
    INNER JOIN published_project ON published_project.PublishedProjectID = investor_request.PublishedProjectID 
    INNER JOIN project ON published_project.Project_ID = project.Project_ID 
    INNER JOIN company_registration ON project.Company_ID = company_registration.Company_ID 
    INNER JOIN student_registration ON company_registration.Student_ID = student_registration.Student_ID 
    WHERE InvestorRequestID = '$investorID'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $investorDetails = mysqli_fetch_assoc($result);
        echo json_encode($investorDetails);
    } else {
        echo json_encode(['error' => 'Investor details not found']);
    }
?>
