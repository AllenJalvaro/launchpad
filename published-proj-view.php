<?php
    require "config.php";
    
    $viewedProj = isset($_GET['project_id']) ? $_GET['project_id'] : null;

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
        }

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 50%;
}

/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
.modal-content form input {
    /* width: 100%; */
}
    </style>
</head>
<body>

    <div class="container">
        <nav>
                <img src="\launchpad\images\logo-text.svg" class="logo-img">
                <div class="left-btn">
                    <a href="login.php" class="login-btn">Students and Instructors</a>
                </div>
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

    <div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Invest in this project</h2>
<?php

if (isset($_POST['submit'])) {
    $investor = $_POST['investor-name'];
    $email = $_POST['investor-email'];
    $income = $_POST['income'];
    //$request = 

    $sql = "INSERT INTO investor_request (PublishedProjectID,InvestorName,Email, SourceofIncome, Submission_date) VALUES ('$viewedProj', '$investor', '$email', '$income', NOW())";

    $result = $conn->query($sql);

    if ($result) {
        echo "<script>alert('REQUEST SUBMITTED');</script>";
    }
}

?>

    <form action="" method="post" enctype="multipart/form-data">
        <p>Name:</p>
        <input type="text" name="investor-name">
        <p>Email:</p>
        <input type="email" name="investor-email">
        <p>Source of income:</p>
        <input type="text" name="income">
        <p>Identity Proof:</p>
        <input type="file" name="proof"><br><br>
        <p>Request Documents:</p>
        <input type="checkbox" name="" id="">Startup Model Canvas
        <br>
        <input type="checkbox" name="" id="">Video Pitch
        <br>
        <input type="checkbox" name="" id="">Pitch Deck
        <br>
        <p>Others:</p>
        <input type="text" name="doc"><br><br>
        <input type="submit" value="Submit" name="submit">
    </form>
  </div>

</div>

<script>
// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>

</body>
</html>