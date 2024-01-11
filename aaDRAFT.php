<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .box {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            border-radius: 20px !important;
            border: 1px solid skyblue !important;
            padding: 30px; !important;
            margin: 15px !important;
            width: 90% !important;
            height: 80px !important;
            background-color: white !important;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1) !important;
        }

        .confirm-btn{
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-align: center !important;
            background-color: darkgreen;
            margin: 20px;
            color: white;
            font-family: Arial, Helvetica, sans-serif;
        }
        .delete-btn {
            
            font-family: Arial, Helvetica, sans-serif;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-align: center !important;
            background-color: gray;
            margin: 20px;
            color: white;
        }
    </style>
</head>

<body>
<?php
require 'aaDRAFTdb.php';

$query = "SELECT p.Project_title, s.Student_fname, s.Student_lname, i.InvitationDate   from project p, invitation i, student_registration s where p.Project_ID=i.ProjectID and i.InviterID = s.Student_ID and i.status='PENDING' ORDER BY InvitationDate desc, Project_title asc";
$result = mysqli_query($conn, $query);

?>
<?php
                while ($row = mysqli_fetch_assoc($result)) {
        ?>

<div class="box">
        <div class="project-title"><?php echo $row['Project_title']?></div>
        <div class="creator"><?php echo $row['Student_fname'].' '.$row['Student_lname']?></div>
        <div class="creation-date"><?php echo $row['InvitationDate']?></div>
        <div class="separator">
        <div class="confirm-btn">Join</div>
        <div class="delete-btn">Delete Request</div>
        </div>
        </div>
        <?php
                }
                ?>
   
    
</body>

</html>

