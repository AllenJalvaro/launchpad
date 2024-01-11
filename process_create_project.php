<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $projectName = mysqli_real_escape_string($conn, $_POST["projectName"]);
    $projectDescription = mysqli_real_escape_string($conn, $_POST["projectDescription"]);
    $selectedMembers = $_POST["selectedMembers"] ?? [];
    $selectedMentor = $_POST["selectedMentor"] ?? null;

    $selectedCompanyID = isset($_GET['Company_id']) ? $_GET['Company_id'] : null;

    $checkCompanyQuery = "SELECT Company_ID FROM company_registration WHERE Company_ID = '$selectedCompanyID'";
    $resultCompany = mysqli_query($conn, $checkCompanyQuery);

    if ($row = mysqli_fetch_assoc($resultCompany)) {
        $companyId = $row["Company_ID"];

        $insertProjectQuery = "INSERT INTO project (Company_ID, Project_title, Project_Description, Project_date)
                               VALUES ('$companyId', '$projectName', '$projectDescription', NOW())";

        if (mysqli_query($conn, $insertProjectQuery)) {
            $projectId = mysqli_insert_id($conn);

            foreach ($selectedMembers as $studentId) {
                $insertMemberQuery = "INSERT INTO project_member (Project_ID, Student_ID) VALUES ($projectId, '$studentId')";
                mysqli_query($conn, $insertMemberQuery);
            }

            if (!empty($selectedMentor)) {
                $insertMentorQuery = "INSERT INTO project_mentor (Project_ID, Mentor_ID) VALUES ($projectId, $selectedMentor)";
                mysqli_query($conn, $insertMentorQuery);
            }

            echo "<script>alert('Project created successfully!');</script>";
        } else {
            echo "<script>alert('Error creating project.');</script>";
        }
    } else {
        echo "<script>alert('User is not associated with a company.');</script>";
    }
}

// Redirect to company_view.php with the selectedCompanyID
if (isset($selectedCompanyID)) {
    header("Location: company_view.php?Company_id=$selectedCompanyID");
} else {
    // Handle the case when $selectedCompanyID is not set
    // You might want to redirect the user to a different page or handle this differently based on your application logic.
    // For now, let's redirect them to index.php
    header("Location: index.php");
}

exit();
?>
