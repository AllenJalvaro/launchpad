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

$projectQuery = "SELECT * FROM project WHERE Company_ID = '$selectedCompanyID' ORDER BY Project_date DESC";
$resultProjects = mysqli_query($conn, $projectQuery);
$copid = $_GET['Company_id'];
$_SESSION['copid'] = $copid;
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/company.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
            </a>
            <a href="investment.php">
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
                <br><br>
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
                    </a> -->
                <a href="profile.php" style="position: fixed; bottom: 0; background-color: white;">
                    <button>
                        <span>
                            <div class="avatar2" id="initialsAvatar6"></div>
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
    <div class="content2">
        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['form1'])) {
                $newCompanyName = mysqli_real_escape_string($conn, $_POST["company_name"]);
                $newCompanyDescription = mysqli_real_escape_string($conn, $_POST["company_description"]);
                $selectedCompanyID = mysqli_real_escape_string($conn, $selectedCompanyID);

                if ($_FILES["company_logo"]["error"] == 0) {
                    $newCompanyLogo = uploadCompanyLogo();
                } else {
                    $selectLogo = mysqli_query($conn, "SELECT Company_logo FROM company_registration WHERE company_id='$selectedCompanyID'");
                    if (mysqli_num_rows($selectLogo) > 0) {
                        $row = mysqli_fetch_assoc($selectLogo);
                        $newCompanyLogo = $row['Company_logo'];
                    }
                }
                $updateQuery = "UPDATE company_registration SET 
                    Company_name='$newCompanyName',
                    Company_description='$newCompanyDescription',
                    Company_logo='$newCompanyLogo'
                    WHERE company_id='$selectedCompanyID'";

                if (mysqli_query($conn, $updateQuery)) {
                    echo "<script>
                    Swal.fire({
                        title: 'Changes saved successfully!',
                        text: '',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000, 
                    }).then(function() {
                        window.location.href = 'company_view.php?Company_id=" . $_SESSION['copid'] . "';
                    });
                </script>";
                } else {
                    echo '<script type="text/javascript">';
                    echo 'swal("Error!", "Error updating record: ' . mysqli_error($conn) . '", "error");';
                    echo '</script>';
                }

            } elseif (isset($_POST['form2'])) {
                echo "
            <script>
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Deleting the company will also delete all related projects.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: 'deleteCompany.php',
                            data: { copid: " . $_SESSION['copid'] . " },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Company deleted successfully!',
                                    text: '',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 2000,
                                }).then(function () {
                                    window.location.href = 'index.php';
                                });
                            }
                        });
                    }
                });
            </script>
            ";
            }

        }

        function uploadCompanyLogo()
        {
            $targetDir = "images/";
            $timestamp = time();
            $targetFile = $targetDir . $timestamp . '_' . basename($_FILES["company_logo"]["name"]);
            move_uploaded_file($_FILES["company_logo"]["tmp_name"], $targetFile);
            return $targetFile;
        }
        ?>

        <div class="search-bar">
            <input type="text" id="projectSearch" name="projectSearch"
                placeholder="Search any <?php echo $companyName ?>'s projects"><span class="forspace"></span>

            <div class="kebab-container">
                <span class="kebab-menu" onclick="toggleModal()">
                    <img src="images/options.png" alt="options-icon" height="30px">
                </span>

                <div class="modal-container" id="modalContainer">
                    <div class="modal-content">
                        <a href="#" class="akeb" id="viewComp">View Details
                        </a>
                        <a href="#" class="akeb" id="editComp">Edit
                            Company
                        </a>
                        <form action="" method="post">
                            <input type="hidden" name="form2">
                            <button type="submit" class="akebd" id="deleteComp">Delete Company</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>














        <div id="editModal" class="modalBlock">
            <div class="modal-edit">
                <form class="editForm" action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="form1">
                    <div class="editTop" style="display: flex; justify-content: space-between;">
                        <h3>Edit
                            <?php echo $companyName ?>'s Details
                        </h3>
                        <span class="closeEditM" style="cursor: pointer; color: #006BB9;">&times;</span>
                    </div>
                    <?php
                    $selectLogo = mysqli_query($conn, "SELECT * FROM company_registration WHERE company_id='$selectedCompanyID'");
                    if (mysqli_num_rows($selectLogo) > 0) {
                        $row = mysqli_fetch_assoc($selectLogo);
                        $companyLogo = $row['Company_logo'];
                        ?>
                        <p>Company Name:</p>
                        <input type="text" id="company_name" name="company_name" value="<?php echo $companyName ?>"
                            required>
                        <p>Company Description:</p>
                        <textarea id="company_description" name="company_description" rows="8"
                            required><?php echo $row['Company_description']; ?></textarea>
                        <p>Your Current Company Logo:</p>

                        <div class="img-container">
                            <img src="<?php if (isset($companyLogo)) {
                                echo $companyLogo;
                            } ?>" alt="Logo_img" height="100px" width="100px">
                        </div>
                    <?php } ?>
                    <p><i class="fas fa-caret-down"></i>
                        Choose New Logo: <span
                            style="text-decoration: none; font-weight: 400; font-size: 12px; font-style:italic;">(Ignore
                            this when you do not want to change your company logo)</span></p>
                    <input type="file" id="company_logo" name="company_logo" accept="image/*">
                    <input type="submit" value="Save Changes" name="submit"><br>
                </form>
            </div>
        </div>



        <div id="viewModal" class="modalBlock">
            <div class="modal-edit">

                <div class="editTop" style="display: flex; justify-content: space-between;">
                    <h3>
                        <?php echo $companyName ?>'s Details
                    </h3>
                    <span class="closeViewM" style="cursor: pointer; color: #006BB9;">&times;</span>
                </div>
                <?php
                $selectLogo = mysqli_query($conn, "SELECT * FROM company_registration WHERE company_id='$selectedCompanyID'");
                if (mysqli_num_rows($selectLogo) > 0) {
                    $row = mysqli_fetch_assoc($selectLogo);
                    $companyLogo = $row['Company_logo'];
                    ?>
                    <p>Company Name:</p>
                    <input type="text" id="company_name" name="company_name" value="<?php echo $companyName ?>" readonly
                        required>
                    <p>Company Description:</p>
                    <textarea id="company_description" name="company_description" rows="8" required
                        readonly><?php echo $row['Company_description']; ?></textarea>
                    <p>Your Current Company Logo:</p>

                    <div class="img-container">
                        <img src="<?php if (isset($companyLogo)) {
                            echo $companyLogo;
                        } ?>" alt="Logo_img" height="100px" width="100px">
                    </div>
                <?php } ?>

            </div>
        </div>

















    </div>
    <h1 style="margin-left: 300px;">
        <?php echo $companyName ?>'s projects
    </h1>

    <div class="content">
        <a href="create-project.php?Company_id=<?php echo $_GET['Company_id']; ?>" class="project-card2">


            <br><br>
            <img src="images/add-company-icon.png" alt="add-icon" width="30px">
            <h3>Create new project</h3>
        </a>

        <?php while ($row = mysqli_fetch_assoc($resultProjects)): ?>
            <a href="project.php?project_id=<?php echo $row['Project_ID']; ?>" class="project-card">
                <div class="status-badge" style="visibility: hidden;">Phase 1 on progress</div>
                <div class="status-badge2">Phase 2 on progress</div>
                <div>
                    <div class="project-title">
                        <p>
                            <?php echo $row['Project_title']; ?>
                        </p>
                    </div>
                    <div class="project-date">
                        <?php
                        date_default_timezone_set('Asia/Manila');

                        $projectDate = new DateTime($row['Project_date']);
                        $currentDate = new DateTime();
                        $timeElapsed = $currentDate->getTimestamp() - $projectDate->getTimestamp();

                        if ($timeElapsed < 60) {
                            echo 'created Just Now';
                        } elseif ($timeElapsed < 3600) {
                            $minutes = floor($timeElapsed / 60);
                            echo 'created ' . (($minutes == 1) ? '1 min ago' : $minutes . ' mins ago');
                        } elseif ($timeElapsed < 86400) {
                            $hours = floor($timeElapsed / 3600);
                            echo 'created ' . (($hours == 1) ? '1 hr ago' : $hours . ' hrs ago');
                        } elseif ($timeElapsed < 604800) {
                            $days = floor($timeElapsed / 86400);
                            echo 'created ' . (($days == 1) ? '1 day ago' : $days . ' days ago');
                        } elseif ($timeElapsed < 1209600) {
                            echo 'created 1 week ago';
                        } elseif ($timeElapsed < 1814400) {
                            echo 'created 2 weeks ago';
                        } elseif ($timeElapsed < 2419200) {
                            echo 'created 3 weeks ago';
                        } else {
                            echo 'created on ' . $projectDate->format('j M Y, g:i a');
                        }
                        ?>


                    </div>
                </div>
            </a>
        <?php endwhile; ?>


    </div>
    <script>
        $(document).ready(function () {
            var originalContent = $(".content").html();

            $("#projectSearch").on("input", function () {
                var searchTerm = $(this).val();

                if (searchTerm.length > 0) {
                    $.ajax({
                        type: "POST",
                        url: "search-projects.php",
                        data: { searchTerm: searchTerm, companyID: <?php echo $selectedCompanyID; ?> },
                        success: function (response) {
                            $(".content").empty(); // Clear existing content
                            $(".project-card").hide();
                            $(".project-card2").hide();
                            $(response).appendTo(".content");
                        }
                    });
                } else {
                    // Restore the original content
                    $(".content").html(originalContent);
                    $(".project-card").show();
                    $(".project-card2").show();
                }
            });
        });
    </script>
    <script>
        // JavaScript to set the initials
        document.addEventListener("DOMContentLoaded", function () {
            const firstName = "<?php echo $fname ?>"; // Replace with actual first name
            const lastName = "<?php echo $lname ?>"; // Replace with actual last name

            const initials = getInitials(firstName, lastName);
            document.getElementById("initialsAvatar6").innerText = initials;
        });

        // Function to get initials from first and last names
        function getInitials(firstName, lastName) {
            return (
                (firstName ? firstName[0].toUpperCase() : "") +
                (lastName ? lastName[0].toUpperCase() : "")
            );
        }
    </script>
    <script>
        const modalContainer = document.getElementById('modalContainer');

        function toggleModal() {
            modalContainer.style.display = (modalContainer.style.display === 'none' || modalContainer.style.display === '') ? 'block' : 'none';
        }

        document.addEventListener('click', function (event) {
            if (!modalContainer.contains(event.target) && !document.querySelector('.kebab-menu').contains(event.target)) {
                closeModal();
            }
        });

        function closeModal() {
            modalContainer.style.display = 'none';
        }
    </script>
    <script>
        var modal = document.getElementById("editModal");
        var btn = document.getElementById("editComp");
        var span = document.getElementsByClassName("closeEditM")[0];

        // When the user clicks the button, open the modal 
        // When the user clicks the button, open the modal 
        btn.onclick = function () {
            modal.style.display = "block";
            document.body.style.overflow = "hidden";
        }

        // When the user clicks on <span> (x) or outside the modal, close the modal
        span.onclick = function () {
            modal.style.display = "none";
            document.body.style.overflow = "visible";
        }

        // window.onclick = function (event) {
        //     if (event.target == modal) {
        //         modal.style.display = "none";
        //         document.body.style.overflow = "visible";
        //     }
        // }

    </script>
    <script>
        var vmodal = document.getElementById("viewModal");
        var vbtn = document.getElementById("viewComp");
        var vspan = document.getElementsByClassName("closeViewM")[0];

        vbtn.onclick = function () {
            vmodal.style.display = "block";
            document.body.style.overflow = "hidden";
        }
        vspan.onclick = function () {
            vmodal.style.display = "none";
            document.body.style.overflow = "visible";
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
                document.body.style.overflow = "visible";
            }
        }


    </script>


</body>

</html>