<?php
    require "config.php";
    
    $viewedProj = isset($_GET['project_id']) ? $_GET['project_id'] : null;

    if ($viewedProj == null)  {
        header("Location: landingpage.php");
    }

    $fetchPubProj = "SELECT * FROM published_project INNER JOIN project ON published_project.Project_ID = project.Project_ID INNER JOIN ideation_phase ON ideation_phase.Project_ID = published_project.Project_ID INNER JOIN company_registration ON project.Company_ID = company_registration.Company_ID WHERE published_project.PublishedProjectID = '$viewedProj'";

    $resultProj = mysqli_query($conn, $fetchPubProj);

    $hasProj = mysqli_num_rows($resultProj) > 0;
    $projPubDate = "";  
    $projTitle = "";
    $projLogo = "";
    $projCat = "";
    $projDesc = "";
    $compName = "";
    $compLogo = "";

    if ($hasProj) {
        $row = mysqli_fetch_assoc($resultProj); 
        $projPubDate = $row['Published_date'];
        $projTitle = $row['Project_title'];
        $projLogo = $row['Project_logo'];
        $projDesc = $row['Project_Description'];
        $compName = $row['Company_name'];
        $compLogo = $row['Company_logo'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - Launchpad</title>
		<link rel="icon" href="/launchpad/images/favicon.svg" />
        <link rel="stylesheet" href="css/landingpage.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <style>
        .container {
            max-width: 1200px;
            width: 100%;
        }
        .top-content {
            display: flex;
            justify-content: space-between;
        }

        .top-content img {
            width: 45px;
            height: 45px;
            border-radius: 100px;
        }

        .top-content .right {
            display: flex;
            align-items: center;
        }
        .top-content .right h3 {
            font-size: 18px;
            margin-top: 8px;
            margin-right: 10px;
        }
        .viewed-project .vimg {
            width: 100%;
            min-height: 300px;
            max-height: 300px;
            object-fit: cover;
            border-radius: 30px;
        }
        .viewed-project .vdesc {
            margin: 20px 0;
            font-size: 17px;
        }

        .i-btn {
            display: inline-block;
            display: flex;
            justify-content: center;
        }

        .invest-btn {
            text-decoration: none;
            background-color: #006BB9;
            color: #fff;
            padding: 10px 40px;
            border-radius: 30px;
            border: none;
            margin-bottom: 40px;
        }

        /* The Modal (background) */
        .modal {
            display: none; 
            position: fixed;
            z-index: 1; 
            padding-top: 50px; /* LOCATION NG BOX */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4);
        }

        .modal::-webkit-scrollbar {
            display: none;
        }

        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: auto;
            bottom: 10px;
            padding: 30px 35px;
            border: 1px solid #888;
            border-radius: 40px;
            width: 45%;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
            -webkit-animation-name: animatetop;
            -webkit-animation-duration: 0.4s;
            animation-name: animatetop;
            animation-duration: 0.4s
        }

        @-webkit-keyframes animatetop {
            from {bottom:-300px; opacity:0}
            to {bottom:10px; opacity:1}
        }
        
        @keyframes animatetop {
            from {bottom:-300px; opacity:0}
            to {bottom:10px; opacity:1}
        }  
        
        /* The Close Button */
        .close {
            color: #333;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        form .top {
            display: inline-block;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        form .top h3 {
            font-weight: bold;
        }

        form p {
            margin: 0;
            padding: 0;
            margin: 5px 0;
            font-size: 14px;
            font-weight: 600;
        }
        form label {
            font-size: 14px;
            margin-left: 8px;
        }

        form input {
            background-color: #ffffff00;
            /* border: 1px solid var(--pblue-color); */
            border: 1px solid #666;
            font-family: inherit;
            width: 100%;
            font-size: 15px;
            padding: 10px 16px;
            border-radius: 1.25rem;
            margin: 0;
        }
        form input[type=checkbox] {
            width: 15px;
        }

        form input[type=submit] {
            cursor: pointer;
            width: 100%;
            padding: 10px 16px;
            border-radius: 1.25rem;
            background: var(--pblue-color);
            color: #f9f9f9;
            border: 0;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            letter-spacing: 2px;
            transition: all 0.375s;
            margin-top: 15px;
        }
        form input[type=submit]:hover {
            background: var(--bhov-color);
        }
    </style>
</head>
<body>

    <div class="container">
    <nav>
            <img src="\launchpad\images\logo-text.svg" class="logo-img">
            <a href="login.php" class="login-reg-btn">
                <button>Student and Instructors</button>
            </a>
        </nav>

        <div class="viewed-project">
            <p class="vdate"><?php echo date("F j, Y", strtotime($projPubDate));?></p>
            <div class="top-content">
                <h1 class="vtitle"><?php echo $projTitle; ?></h1>
                <div class="right">
                    <h3 class="vcompName"><?php echo $compName; ?></h3>
                    <img class="vcompImg" src="<?php echo $compLogo; ?>" alt="">
                </div>
            </div>
            <p class="vcategory"></p>

            <img src="<?php echo $projLogo; ?>" alt="" class="vimg">
            <p class="vdesc"><?php echo $projDesc; ?></p>
            
            <div class="i-btn"><button class="invest-btn" id="myBtn">Invest In This Project</button></div>
            
        </div>
    </div>

    <?php
        if (isset($_POST['submit'])) {
            $inverstorName = $_POST['investor-name'];
            $investorEmail = $_POST['investor-email'];
            $sourceIncome = $_POST['source-income'];
            $reqDocuments = isset($_POST['reqDocs']) ? implode(", ", $_POST['reqDocs']) : "";
            $otherDocs = isset($_POST['other-docs']) ? ", " . $_POST['other-docs'] : "";

            if (isset($_FILES["proof"]) && $_FILES["proof"]["error"] == 0) {
                $targetDir = "investor-images/";
                $fileExtension = pathinfo($_FILES["proof"]["name"], PATHINFO_EXTENSION);
                $fileName = uniqid() . "." . $fileExtension;
                $targetFilePath = $targetDir . $fileName;
            
                // Allow only jpg, jpeg, png, and pdf file formats
                $allowedTypes = array("jpg", "jpeg", "png", "pdf");
                if (in_array($fileExtension, $allowedTypes)) {
                    if (move_uploaded_file($_FILES["proof"]["tmp_name"], $targetFilePath)) {
                        $sql = "INSERT INTO investor_request (PublishedProjectID, InvestorName, Email, SourceofIncome, IdentityProof, RequestedDocuments, Submission_date) 
                                VALUES ('$viewedProj', '$inverstorName', '$investorEmail', '$sourceIncome', '$targetFilePath', '$reqDocuments $otherDocs', NOW())";
                        if ($conn->query($sql) === TRUE) {
                            echo "<script> alert('Requested successfully!'); </script>";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }
                    } else {
                        echo "<script> alert('Error uploading file.'); </script>";
                    }
                } else {
                    echo "<script> alert('Invalid file type. Only JPG, JPEG, PNG, and PDF files are allowed.'); </script>";
                }
            } else {
                echo "Error: " . $_FILES["proof"]["error"];
            }            
        }
    ?>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="top">
                    <h3>Invest in this project</h3>
                    <span class="close">&times;</span>
                </div>
                <p>Name:</p>
                <input type="text" name="investor-name" required>
                <p>Email:</p>
                <input type="text" name="investor-email" required>
                <p>Source of Income:</p>
                <input type="text" name="source-income" required>
                <p>Identity proof:</p>
                <input type="file" name="proof" required>
                <p>Request documents:</p>
                <input type="checkbox" name="reqDocs[]" id="canvas" value="Startup Model Canvas"><label for="canvas">Startup Model Canvas</label><br>
                <input type="checkbox" name="reqDocs[]" id="video-pitch" value="Video Pitch"><label for="video-pitch">Video Pitch</label><br>
                <input type="checkbox" name="reqDocs[]" id="pitch-deck" value="Pitch Deck"><label for="pitch-deck">Pitch Deck</label><br>
                <p>Others:</p>
                <input type="text" name="other-docs">
                <input type="submit" value="Submit" name="submit">
            </form>
        </div>
    </div>

<script>
    var modal = document.getElementById("myModal");
    var btn = document.getElementById("myBtn");
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal 
    btn.onclick = function() {
        modal.style.display = "block";
    }
    // When the user clicks on <span> (x), close the modal
    span.onclick = function() { // close button
        modal.style.display = "none";
    }
</script>

</body>
</html>