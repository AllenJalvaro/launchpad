
    <?php
    require 'config.php';
    $response = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $projectName = $_POST['projectName'];
        $projectDescription = $_POST['projectDescription'];
        $selectedMembers = $_POST['selectedMembers'];
        $selectedMentor = $_POST['selectedMentor'];
        $selectedEvaluators = $_POST['selectedEvaluators'];
        $companyId = $_POST['companyId'];
        $studid = $_POST['studid'];

        $insertProjectQuery = "INSERT INTO project
        (`Company_ID`, `Project_title`, `Project_Description`)
        VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertProjectQuery);
        mysqli_stmt_bind_param($stmt, 'iss', $companyId, $projectName, $projectDescription);
        $resultProject = mysqli_stmt_execute($stmt);

        if ($resultProject) {
            $projectId = mysqli_insert_id($conn);

            foreach ($selectedMembers as $memberId) {
                $insertMemberQuery = "INSERT INTO invitation 
                              (`ProjectID`, `InviterID`, `InviteeID`)
                              VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insertMemberQuery);
                mysqli_stmt_bind_param($stmt, 'iss', $projectId, $studid, $memberId);
                mysqli_stmt_execute($stmt);
            }

            $insertMentorQuery = "INSERT INTO project_mentor (`Project_ID`, `Mentor_ID`) 
                          VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insertMentorQuery);
            mysqli_stmt_bind_param($stmt, 'ii', $projectId, $selectedMentor);
            mysqli_stmt_execute($stmt);

            foreach ($selectedEvaluators as $evaluatorId) {
                $insertEvaluatorQuery = "INSERT INTO project_evaluator (`project_id`, `evaluator_id`) 
                                VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $insertEvaluatorQuery);
                mysqli_stmt_bind_param($stmt, 'ii', $projectId, $evaluatorId);
                mysqli_stmt_execute($stmt);
            }

            mysqli_close($conn);

            $response['status'] = 'success';
        $response['message'] = 'The project created successfully!';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to create the project. Please try again.';
        }
    }
    echo json_encode($response);
    ?>

