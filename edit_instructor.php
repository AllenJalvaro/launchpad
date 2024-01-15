<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Instructor</title>
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
    if (isset($_POST['edit_instructor'])) {
        $instructorId = $_POST['instructor_id'];

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
        $newDepartment = $_POST['new_department'];
        $newContactNo = $_POST['new_contact_no'];

        // Update the instructor's details
        $sql = "UPDATE instructor_registration SET
                Instructor_fname = '$newFirstName',
              
                Instructor_lname = '$newLastName',
                Instructor_email = '$newEmail',
                Instructor_password = '$newPassword',
                Department = '$newDepartment',
                Instructor_contactno = '$newContactNo'
                WHERE Instructor_ID = '$instructorId'";

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
    // If not a POST request, display the form to edit the instructor
    $instructorId = $_GET['instructorId'];

    // Fetch the current instructor details
    $conn = new mysqli("localhost", "root", "", "launchpad2");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM instructor_registration WHERE Instructor_ID = '$instructorId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Display the form with the current instructor details
        ?>
        <div class="container mt-5">
            <h2>Edit Instructor</h2>
            <form action="edit_instructor.php" method="post">
                <input type="hidden" name="instructor_id" value="<?php echo $row['Instructor_ID']; ?>">
                <label>First Name:</label>
                <input type="text" name="new_first_name" value="<?php echo $row['Instructor_fname']; ?>"><br>
                
                <label>Last Name:</label>
                <input type="text" name="new_last_name" value="<?php echo $row['Instructor_lname']; ?>"><br>
                <label>Email:</label>
                <input type="text" name="new_email" value="<?php echo $row['Instructor_email']; ?>"><br>
                <label>Password:</label>
                <input type="password" name="new_password" value="<?php echo $row['Instructor_password']; ?>"><br>
                <label>Department:</label>
                <input type="text" name="new_department" value="<?php echo $row['Department']; ?>"><br>
                <label>Contact Number:</label>
                <input type="text" name="new_contact_no" value="<?php echo $row['Instructor_contactno']; ?>"><br>
                <button type="submit" name="edit_instructor">Save Changes</button>
            </form>
    </div>
        </body>
        </html>
        <?php
    } else {
        echo "Instructor not found.";
    }

    // Close the database connection
    $conn->close();
}
?>
