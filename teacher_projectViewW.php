<?php

$select_mentor_id = mysqli_query($conn, "SELECT Instructor_ID FROM instructor_registration WHERE Instructor_email='$userEmail'");
if (mysqli_num_rows($select_mentor_id) > 0) {
    $row = mysqli_fetch_assoc($select_mentor_id);
    $mentor_ID = $row['Instructor_ID'];
}

$select_pitching_id_comment = mysqli_query($conn, "SELECT pitching_phase.pitchingID FROM pitching_phase INNER JOIN project ON pitching_phase.Project_ID=project.Project_ID WHERE project.Project_ID=$project_id");
if (mysqli_num_rows($select_pitching_id_comment) > 0) {
    $row = mysqli_fetch_assoc($select_pitching_id_comment);
    $pitchingID = $row['pitchingID'];
}


if (isset($_POST['btnCommentVideoPitch'])) {

    $mentor_comment_videopitch = $_POST['mentor_comment_videopitch'];

    if (!empty($mentor_comment_videopitch)) {
        $insert_comment_videopitch = mysqli_query($conn, "INSERT INTO comment_pitching_videopitch (`pitchingID`, `instructor_ID`, `comment_videopitch`)
VALUES ('$pitchingID', '$mentor_ID', '$mentor_comment_videopitch');");

        if ($insert_comment_videopitch) {
            // Comment added successfully
            echo "<script>
Swal.fire({
title: 'Comment Added!',
text: 'Your comment has been added successfully.',
icon: 'success',
showConfirmButton: false,
timer: 3000, 
});
</script>";


        } else {

            echo "<script>
Swal.fire({
title: 'Error!',
text: 'Failed to add comment. Please try again.',
icon: 'error',
showConfirmButton: true,
timer: 3000, 
});
</script>";
        }
    }
}// end saving comment videopitch?>












<?php
$select_pitching_phase = mysqli_query($conn, "SELECT pitching_phase.VideoPitch, pitching_phase.PitchDeck, pitching_phase.status FROM pitching_phase INNER JOIN project ON pitching_phase.Project_ID=project.Project_ID WHERE project.Project_ID=$project_id");

if (mysqli_num_rows($select_pitching_phase) > 0) {

    $row = mysqli_fetch_assoc($select_pitching_phase);
    $vidPitch = $row['VideoPitch'];
    $deckPitch = $row['PitchDeck'];
    $pitchingStatus = $row['status'];

//if may record
    ?>





    <div class="phaseSection">
        <p class="sectionTitle">
            4. Video Pitch</p>
        <br>


        <video class="video-preview" controls>
            <source src="<?php if (isset($vidPitch)) {
                echo $vidPitch;
            } ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <br>








        <div class="feedbackTitle"><i class="fas fa-caret-down"></i> Feedbacks</div>




        <div class="feedbackSection">

            <?php
            $select_comment_videopitch = mysqli_query($conn, "SELECT * FROM instructor_registration INNER JOIN comment_pitching_videopitch ON instructor_registration.Instructor_ID=comment_pitching_videopitch.instructor_ID INNER JOIN ideation_phase ON ideation_phase.IdeationID=comment_pitching_videopitch.ideationID WHERE comment_pitching_videopitch.ideationID=$ideationID ORDER BY comment_pitching_videopitch.comment_date DESC");

            if (mysqli_num_rows($select_comment_videopitch) > 0) {
                while ($row = mysqli_fetch_assoc($select_comment_videopitch)) {
                    $fetch_comment_videopitch = $row['comment_videopitch'];
                    $fetch_mentor_namePitch = $row['Instructor_fname'] . ' ' . $row['Instructor_lname'];
                    date_default_timezone_set('Asia/Manila');

                    $fetch_commentdateV = new DateTime($row['comment_date']);
                    $currentDate = new DateTime();
                    $timeElapsed = $currentDate->getTimestamp() - $fetch_commentdate->getTimestamp();

                    if ($timeElapsed < 60) {
                        $fetch_commentdateV = 'Just now';
                    } elseif ($timeElapsed < 3600) {
                        $minutes = floor($timeElapsed / 60);
                        $fetch_commentdateV = ($minutes == 1) ? '1 min ago' : $minutes . ' mins ago';
                    } elseif ($timeElapsed < 86400) {
                        $hours = floor($timeElapsed / 3600);
                        $fetch_commentdateV = ($hours == 1) ? '1 hr ago' : $hours . ' hrs ago';
                    } elseif ($timeElapsed < 604800) {
                        $days = floor($timeElapsed / 86400);
                        $fetch_commentdateV = ($days == 1) ? '1 day ago' : $days . ' days ago';
                    } elseif ($timeElapsed < 1209600) {
                        $fetch_commentdateV = '1 week ago';
                    } elseif ($timeElapsed < 1814400) {
                        $fetch_commentdateV = '2 weeks ago';
                    } elseif ($timeElapsed < 2419200) {
                        $fetch_commentdateV = '3 weeks ago';
                    } else {
                        $fetch_commentdateV = $fetch_commentdateV->format('j M Y, g:i a');
                    }
                    ?>



                    <div class="feedbackBlock">

                        <div class="feedback-info">
                            <span class="commenter">
                                <?php echo $fetch_mentor_namePitch; ?>
                            </span>
                            <span class="feedbackdate"> •
                                <?php echo $fetch_commentdateV; ?>
                            </span>
                        </div>
                        <p class="feedbackContent">
                            <?php echo $fetch_comment_videopitch; ?>
                        </p>

                    </div> <!-- end of feedbackSection -->
                    <?php
                }
            }
            ?>
        </div>
        <div><!--hiiiiia-->
            <form action="" method="post">
                <div class="overview-comment-section-container">
                    <br>
                    <div class="textarea-comment-overview">
                        <textarea name="mentor_comment_videopitch" id="mentor_comment_videopitch" rows="5"
                            placeholder="Write your feedback here..."></textarea>
                    </div>
                    <div class="floating-icon-overview">
                        <button type="submit" style="border: none; background: none; color: black; text-decoration: none;"
                            name="btnCommentVideoPitch">
                            <i class="fas fa-paper-plane" style="color: #006BB9"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php
} else {
    //if wala pang record
    ?>


    <div class="phaseSection" style="width: 100%;">
        <p class="sectionTitle">
            4. Video Pitch</p>

        <br>

        <div class="video-preview-container">
            <video class="video-preview" controls>
                <source src="" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <br>
        <b style="color:#cc0000">No Video Pitch uploaded yet.</b>
        <br>

    </div>



    <?php
} //end of walang record

?>