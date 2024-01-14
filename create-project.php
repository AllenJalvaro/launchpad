<?php
require "config.php";

if (empty($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION["email"];

$checkCompanyQuery = "SELECT c.*, s.Student_ID 
                        FROM company_registration c
                        INNER JOIN student_registration s ON c.Student_ID = s.Student_ID
                        WHERE s.Student_email = '$userEmail'";

$resultCompany = mysqli_query($conn, $checkCompanyQuery);

$companies = [];
while ($row = mysqli_fetch_assoc($resultCompany)) {
    $companies[] = $row;
    $_SESSION['studid'] = $row['Student_ID'];
}

$selectedCompanyID = isset($_GET['Company_id']) ? $_GET['Company_id'] : null;

$hasCompany = count($companies) > 0;
$companyID = "";
$companyName = "";
$companyLogo = "";

if ($selectedCompanyID) {
    $selectedCompanyQuery = "SELECT * FROM company_registration WHERE Company_ID = ?";
    $stmt = mysqli_prepare($conn, $selectedCompanyQuery);
    mysqli_stmt_bind_param($stmt, "i", $selectedCompanyID);
    mysqli_stmt_execute($stmt);
    $resultSelectedCompany = mysqli_stmt_get_result($stmt);

    if ($resultSelectedCompany) {
        $row = mysqli_fetch_assoc($resultSelectedCompany);
        $companyID = $row["Company_ID"];
        $companyName = $row["Company_name"];
        $companyLogo = $row["Company_logo"];
    }
}

$projectQuery = "SELECT * FROM project WHERE Company_ID = '$companyID' ORDER BY Project_date DESC";
$resultProjects = mysqli_query($conn, $projectQuery);

// echo "<script>alert('COMPANY ID: $selectedCompanyID')</script>";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $hasCompany && !empty($companyName) ? $companyName . " - Launchpad" : 'Create Company - Launchpad'; ?>
    </title>
    <link rel="icon" href="/launchpad/images/favicon.ico" id="favicon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/create_project.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        $(document).ready(function () {
            console.log("Document is ready.");
            console.log("<?php echo $_SESSION['studid']; ?>");

            $('form').submit(function (event) {
                console.log("Form submission intercepted.");
                event.preventDefault();
                const projectName = $('#projectName').val();
                const projectDescription = $('#projectDescription').val();
                const selectedMembers = getSelectedValues('#selectedMembers');
                const selectedMentor = getSelectedValue('#selectedMentor');
                const selectedEvaluators = getSelectedValues('#selectedEvaluators');
                const companyId = <?php echo $_SESSION['copid']; ?>;
                let studid = "<?php echo $_SESSION['studid']; ?>";

                const formData = {
                    projectName: projectName,
                    projectDescription: projectDescription,
                    selectedMembers: selectedMembers,
                    selectedMentor: selectedMentor,
                    selectedEvaluators: selectedEvaluators,
                    companyId: companyId,
                    studid: studid
                };
                $.ajax({
                    type: 'POST',
                    url: 'create_project_process.php',
                    data: formData,
                    success: function (response) {

                        console.log('!!Response:', response);
                        Swal.fire({
                            title: 'Success!',
                            text: 'Your project has been created successfully!',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000,
                        }).then(function () {
                            window.location.href = 'company_view.php?Company_id=<?php echo $_SESSION['copid']; ?>';
                        });

                    },
                    error: function (error) {

                        console.error(error);
                    }
                });
            });

            function getSelectedValues(containerId) {
                const selectedValuesSet = new Set();
                $(`${containerId} [data-id]`).each(function () {
                    selectedValuesSet.add($(this).data('id'));
                });
                return Array.from(selectedValuesSet);
            }


            function getSelectedValue(containerId) {
                return $(`${containerId} [data-id]`).data('id');
            }
        });
    </script>

    <script>
        function changeFavicon(url) {
            const favicon = document.getElementById('favicon');
            favicon.href = url;
        }
        <?php if ($hasCompany && !empty($companyLogo)): ?>
            const companyLogoUrl = "/launchpad/<?php echo $companyLogo; ?>";
            changeFavicon(companyLogoUrl);
        <?php endif; ?>
    </script>
</head>

<body>


    <aside class="sidebar">
        <header class="sidebar-header">
            <img src="\launchpad\images\logo-text.svg" class="logo-img">
        </header>

        <nav>
            <a href="index.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\home-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Home</span>
                    </span>
                </button>
            </a>
            <a href="project-idea-checker.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\project-checker-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Project Idea Checker</span>
                    </span>
                </button>
            </a>
            <a href="invitations.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\invitation-icon.png" alt="home-logo" class="logo-ic"></i>
                        <span>Invitations</span>
                    </span>
                </button>
            </a> <a href="investment.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\iconinvestment.png" alt="home-logo" class="logo-ic"></i>
                        <span>Investment Requests</span>
                    </span>
                </button>
            </a>
            <a href="collabprojects.php">
                <button>
                    <span>
                        <i><img src="\launchpad\images\iconpuzzle.png" alt="home-logo" class="logo-ic"></i>
                        <span>Collab Projects</span>
                    </span>
                </button>
            </a>
            <p class="divider-company">YOUR COMPANY<a href="create-company.php" style="text-decoration: none;">

                    <img src="\launchpad\images\join-company-icon.png" alt="Join Company Icon" width="15px"
                        height="15px" style="margin-left: 70px;">

                </a></p>
            <?php if ($hasCompany): ?>
                <?php foreach ($companies as $row): ?>
                    <?php if ($row['Company_ID'] == $selectedCompanyID): ?>
                        <a class="active" href="company_view.php?Company_id=<?php echo $row['Company_ID']; ?>">
                        <?php else: ?>
                            <a href="company_view.php?Company_id=<?php echo $row['Company_ID']; ?>">
                            <?php endif; ?>
                            <button>
                                <span class="<?php echo 'btn-company-created'; ?>">
                                    <div class="circle-avatar">
                                        <?php if (!empty($row['Company_logo'])): ?>
                                            <img src="\launchpad\<?php echo $row['Company_logo']; ?>" alt="Company Logo"
                                                class="img-company">
                                        <?php endif; ?>
                                    </div>
                                    <span class="create-company-text">
                                        <?php echo $row['Company_name']; ?>
                                    </span>
                                </span>
                            </button>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- <p class="divider-company">COMPANIES YOU'VE JOINED</p>
                <a href="#">
                <button>
                    <span  class="btn-join-company">
                        <i > <div class="circle-avatar">
                            <img src="\launchpad\images\join-company-icon.png" alt="">
                        </div></i>
                        <span class="join-company-text">Join companies</span>
                    </span>
                </button>
                </a> --><br><br>
                <a href="profile.php" style="position: fixed; bottom: 0; background-color: white;">
                    <button>
                        <span>
                            <div class="avatar2" id="initialsAvatar10"></div>
                            <span>Profile</span>
                        </span>
                    </button>
                </a>

        </nav>


    </aside>
    <?php
    $email = $_SESSION['email'];

    $select_user_info = "SELECT * FROM student_registration WHERE Student_email='$email'";
    $result_user_info = mysqli_query($conn, $select_user_info);
    if ($result_user_info) {
        if (mysqli_num_rows($result_user_info) > 0) {
            $row = mysqli_fetch_assoc($result_user_info);
            $stud_id = $row['Student_ID'];
            $fname = $row['Student_fname'];
            $lname = $row['Student_lname'];
            $course = $row['Course'];
            $year = $row['Year'];
            $block = $row['Block'];
            $contactNo = $row['Student_contactno'];
        }
    }
    ?>
    <div class="content">
        <a href="company_view.php?Company_id=<?php echo $_SESSION['copid']; ?>"
            style="text-decoration:none; color:#006BB9;position:fixed;" title="Back"><i class="fas fa-angle-left"
                style="font-size: 40px; "></i>
        </a>
        <form method="post" action="">


            <h2>Create New Project</h2> <br>
            <label for="projectName">Project Name:</label>
            <input type="text" id="projectName" name="projectName" required><br><br>

            <label for="projectDescription">Project Description:</label>
            <textarea id="projectDescription" name="projectDescription" rows="10" required></textarea><br><br>

            <label for="memberSearch">Add Members:</label>
            <div class="search-container">
                <i class="fas fa-search search-icon" style="color: #006BB9;"></i>
                <input class="search-input" type="text" id="memberSearch" oninput="searchMembers(this.value)"
                    placeholder="Search members...">
            </div>
            <br>
            <div id="memberResults" class="search-results"></div>
            <div id="selectedMembers" class="color-selected">
            </div>
            <br>

            <label for="mentorSearch">Add Mentor:</label>
            <div class="search-container">
                <i class="fas fa-search search-icon" style="color: #006BB9;"></i>
                <input class="search-input" type="text" id="mentorSearch" oninput="searchMentors(this.value)"
                    placeholder="Search mentor...">
            </div>
            <br>
            <div id="mentorResults" class="search-results"></div>
            <div id="selectedMentor" class="color-selected">

            </div><br>

            <label for="evaluatorSearch">Add Evaluators:</label>

            <div class="search-container">
                <i class="fas fa-search search-icon" style="color: #006BB9;"></i>
                <input class="search-input" type="text" id="evaluatorSearch" oninput="searchEvaluators(this.value)"
                    placeholder="Search evaluators...">
            </div>


            <br>
            <div id="evaluatorResults" class="search-results"></div>
            <div id="selectedEvaluators" class="color-selected">
            </div>
            <br><br>
            <button id="submit-btn" type="submit">Create Project</button>
        </form>
    </div>

    <script>
        function searchEvaluators(query) {
            if (query.trim() === '') {
                $('#evaluatorResults').html('').removeAttr('style');
                return;
            }

            $.ajax({
                url: 'search_evaluators.php',
                type: 'POST',
                data: { query: query },
                success: function (data) {
                    $('#evaluatorResults').html(data).css({
                        'cursor': 'pointer',
                        'border-radius': '10px',
                        'background-color': 'transparent',
                    });
                    attachClickHandlers('evaluator');
                }
            });
        }




        function searchMembers(query) {
            if (query.trim() === '') {
                $('#memberResults').html('').removeAttr('style');

                return;
            }

            $.ajax({
                url: 'search_members.php',
                type: 'POST',
                data: { query: query },
                success: function (data) {
                    $('#memberResults').html(data).css({
                        'cursor': 'pointer',

                        'border-radius': '10px',

                        'background-color': 'transparent',
                    });
                    attachClickHandlers('member');


                }
            });
        }


        function searchMentors(query) {
            if (query.trim() === '') {
                $('#mentorResults').html('');
                return;
            }
            $.ajax({
                url: 'search_mentors.php',
                type: 'POST',
                data: { query: query },
                success: function (data) {
                    $('#mentorResults').html(data).css({
                        'cursor': 'pointer',

                        'border-radius': '10px',

                        'background-color': 'transparent',
                    });
                    attachClickHandlers('mentor');
                }
            });
        }


        const selectedMembers = [];
        let selectedMentor = "";
        const selectedEvaluators = [];


        console.log("Initial selectedMembers:", selectedMembers);
        console.log("Initial selectedMentor:", selectedMentor);
        console.log("Initial selectedEvaluators:", selectedEvaluators);





        function attachClickHandlers(type) {
            $(`.search-results .${type}-result`).click(function () {
                const id = $(this).data('id');
                const name = $(this).text();

                if (type === 'member') {
                    addMember(id, name);
                } else if (type === 'mentor') {
                    addMentor(id, name);
                } else if (type === 'evaluator') {
                    addEvaluator(id, name);
                }
            });
        }

        const maxEvaluators = 3;
        function addEvaluator(evaluatorID, evaluatorName) {
            if (selectedEvaluators.length < maxEvaluators) {
                if (!selectedEvaluators.includes(evaluatorID)) {
                    selectedEvaluators.push(evaluatorID);
                    $('#selectedEvaluators').append(`<div data-id="${evaluatorID}">${evaluatorName} <span class="removeEvaluator" data-id="${evaluatorID}">x</span></div>`);
                    console.log("Added Evaluator:", evaluatorID);
                    console.log("Selected Evaluators after addition:", selectedEvaluators);
                } else {
                    console.log("Evaluator already exists:", evaluatorID);
                }
            } else {
                Swal.fire({
                    text: 'Your can only select 3 evaluators!',
                    icon: 'warning',
                    showConfirmButton: true
                });

                console.log("Maximum number of evaluators reached:", maxEvaluators);
            }
        }


        $('#selectedEvaluators').on('click', '.removeEvaluator', function () {
            const evaluatorID = $(this).data('id');
            removeEvaluator(evaluatorID);
        });

        function removeEvaluator(evaluatorID) {
            const index = selectedEvaluators.indexOf(evaluatorID);
            if (index !== -1) {
                selectedEvaluators.splice(index, 1);
                $(`#selectedEvaluators [data-id="${evaluatorID}"]`).remove();
                console.log("Removed Evaluator:", evaluatorID);
                console.log("Selected Evaluators after removal:", selectedEvaluators);
            } else {
                console.log("Evaluator not found:", evaluatorID);
            }
        }


        function addMember(studentID, studentName) {
            if (!selectedMembers.includes(studentID)) {
                selectedMembers.push(studentID);
                $('#selectedMembers').append(`<div data-id="${studentID}">${studentName} <span onclick="removeMember('${studentID}')">x</span></div>`);

                console.log("Selected Members after addition:", selectedMembers);
            }
        }

        function addMentor(mentorID, mentorName) {
            selectedMentor = mentorID;
            $('#selectedMentor').html(`<div data-id="${mentorID}">${mentorName} <span onclick="removeMentor('${mentorID}')">x</span></div>`);

            console.log("Selected Mentor after addition:", selectedMentor);
        }

        function removeMember(studentID) {
            const index = selectedMembers.indexOf(studentID);
            if (index !== -1) {
                selectedMembers.splice(index, 1);
                $(`#selectedMembers [data-id="${studentID}"]`).remove();
            }

            console.log("Selected Members after removal:", selectedMembers);
        }



        function removeMentor(mentorID) {
            selectedMentor = "";
            $('#selectedMentor').empty();

            console.log("Selected Mentor after removal:", selectedMentor);
        }
        console.log("Final selectedMembers:", selectedMembers);
        console.log("Final selectedMentor:", selectedMentor);
        console.log("Final selectedEvaluators:", selectedEvaluators);
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const firstName = "<?php echo $fname ?>";
            const lastName = "<?php echo $lname ?>";

            const initials = getInitials(firstName, lastName);
            document.getElementById("initialsAvatar10").innerText = initials;
        });

        function getInitials(firstName, lastName) {
            return (
                (firstName ? firstName[0].toUpperCase() : "") +
                (lastName ? lastName[0].toUpperCase() : "")
            );
        }
    </script>
</body>

</html>