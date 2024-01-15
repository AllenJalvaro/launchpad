<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Student</title>
            <!-- Include Bootstrap CSS and JS links here -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        </head>
        <body>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form is submitted
    if (isset($_POST['edit_student'])) {
        // Get the student ID from the form
        $studentId = $_POST['student_id'];

        // Connect to the database
        $conn = new mysqli("localhost", "root", "", "launchpad2");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get the updated values from the form
        $newFirstName = $_POST['new_first_name'];
    
        $newLastName = $_POST['new_last_name'];
        $newEmail = $_POST['new_email'];
        $newPassword = $_POST['new_password'];
        $newCourse = $_POST['new_course'];
        $newYear = $_POST['new_year'];
        $newBlock = $_POST['new_block'];
        $newContactNo = $_POST['new_contact_no'];

        // Update the student's details
        $sql = "UPDATE student_registration SET
                Student_fname = '$newFirstName',
            
                Student_lname = '$newLastName',
                Student_email = '$newEmail',
                Student_password = '$newPassword',
                Course = '$newCourse',
                Year = '$newYear',
                Block = '$newBlock',
                Student_contactno = '$newContactNo'
                WHERE Student_ID = '$studentId'";

        if ($conn->query($sql) === TRUE) {
            // Success: Redirect to admin.php
            ?>
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Record updated successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'admin.php';
                });
            </script>
            <?php
        } else {
            // Error: Display error message
            ?>
            <script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Error updating record: <?php echo $conn->error; ?>',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            </script>
            <?php
        }

        // Close the database connection
        $conn->close();
    }
} else {
    // If not a POST request, display the form to edit the student
    $studentId = $_GET['studentId'];

    // Fetch the current student details
    $conn = new mysqli("localhost", "root", "", "launchpad2");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM student_registration WHERE Student_ID = '$studentId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Display the form with the current student details
        ?>
            <div class="container mt-5">
                <h2>Edit Student</h2>
                <form action="edit_student.php" method="post">
                    <input type="hidden" name="student_id" value="<?php echo $row['Student_ID']; ?>">
                    <label>First Name:</label>
                    <input type="text" name="new_first_name" value="<?php echo $row['Student_fname']; ?>"><br>
                
                    <label>Last Name:</label>
                    <input type="text" name="new_last_name" value="<?php echo $row['Student_lname']; ?>"><br>
                    <label>Email:</label>
                    <input type="text" name="new_email" value="<?php echo $row['Student_email']; ?>"><br>
                    <label>Password:</label>
                    <input type="password" name="new_password" value="<?php echo $row['Student_password']; ?>"><br>
                    <label>Course:</label>
                    <input type="text" name="new_course" value="<?php echo $row['Course']; ?>"><br>
                    <label>Year:</label>
                    <input type="text" name="new_year" value="<?php echo $row['Year']; ?>"><br>
                    <label>Block:</label>
                    <input type="text" name="new_block" value="<?php echo $row['Block']; ?>"><br>
                    <label>Contact Number:</label>
                    <input type="text" name="new_contact_no" value="<?php echo $row['Student_contactno']; ?>"><br>
                    <input type="submit" name="edit_student" value="Save Changes">
                </form>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Student not found.";
    }

    // Close the database connection
    $conn->close();
}
?>
