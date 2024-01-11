
<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "launchpad") or die("error in connecting!");

// Function to sanitize input and prevent SQL injection
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, $input);
}
// Check if the form is submitted for editing or deleting
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["editStudentID"])) {
        // Handle update action
        $id = sanitize($_POST["editStudentID"]);
        $fname = sanitize($_POST["editStudentFname"]);
        $mname = sanitize($_POST["editStudentMname"]);
        $lname = sanitize($_POST["editStudentLname"]);
        $email = sanitize($_POST["editStudentEmail"]);
        $password = sanitize($_POST["editStudentPassword"]);
        $course = sanitize($_POST["editStudentCourse"]);
        $year = sanitize($_POST["editStudentYear"]);
        $block = sanitize($_POST["editStudentBlock"]);
        $contactno = sanitize($_POST["editStudentContactNo"]);

        // Check if the password field is not empty before updating
        // $password = !empty($_POST["editStudentPassword"]) ? password_hash(sanitize($_POST["editStudentPassword"]), PASSWORD_DEFAULT) : '';

        // Construct the update query
        $updateQuery = "UPDATE student_registration SET
            Student_fname = '$fname',
            Student_mname = '$mname',
            Student_lname = '$lname',
            Student_email = '$email',
            Student_password='$password',
            Course = '$course',
            Year = '$year',
            Block = '$block',
            Student_contactno = '$contactno'";

        // Add password update to the query if the password is not empty
        if (!empty($password)) {
            $updateQuery .= ", Student_password = '$password'";
        }

        $updateQuery .= " WHERE Student_ID = '$id'";


             // Display a confirmation alert before submitting the form
             echo "<script>
            var confirmUpdate = confirm('Are you sure you want to save changes?');
            if (confirmUpdate) {
                document.getElementById('editForm').submit();
            }
        </script>";

        if ($conn->query($updateQuery) === TRUE) {
            echo "<script>alert('Record updated successfully');</script>";
        } else {
            echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
        }
    } elseif (isset($_POST["delete"])) {
        // Handle delete action
        $id = sanitize($_POST["delete"]);
           // Display a confirmation alert before submitting the form
           echo "<script>
           var confirmDelete = confirm('Are you sure you want to delete this record?');
           if (confirmDelete) {
               document.getElementById('deleteForm').submit();
           }
       </script>";
        // Implement your delete logic here
        // Perform the delete query
        $deleteQuery = "DELETE FROM student_registration WHERE Student_ID = '$id'";

        if ($conn->query($deleteQuery) === TRUE) {
            echo "<script>alert('Record deleted successfully');</script>";
        } else {
            echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
        }
    }
}






// Function to display user details in a table
function displayTable($tableName, $idColumnName, $conn)
{
    echo "
    <table id='{$tableName}Table' class='table table-bordered table-striped'>
        <thead>
            <tr>";
    
    // Fetch column names dynamically
    $columnsQuery = mysqli_query($conn, "SHOW COLUMNS FROM $tableName");
    while ($column = mysqli_fetch_assoc($columnsQuery)) {
        $columnName = str_replace('_', ' ', $column['Field']); // Convert underscores to spaces
        echo "<th>{$columnName}</th>";
    }
    
    echo "
                <th>Action</th>
            </tr>
        </thead>
        <tbody>";

    // Fetch and display accounts
    $result = $conn->query("SELECT * FROM $tableName");

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>{$value}</td>";
        }
        echo "<td>
                <form method='post'>
                    <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#editStudentModal' data-bs-id='{$row[$idColumnName]}'>Edit</button> 
                    <button type='submit' name='delete' value='{$row[$idColumnName]}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this account?\")'>Delete</button>
                </form>
            </td>
        </tr>";
    }

    echo "
        </tbody>
    </table>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
		<link rel="icon" href="/launchpad/images/favicon.svg" />
    <!-- Add your stylesheets and scripts here -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <style>
        body{
            margin: 20px;
        }
    </style>
</head>

<body>

    <h2 class="mt-3">Admin Panel</h2>

    <ul class="nav nav-tabs mt-3" id="adminTabs">
        <li class="nav-item">
            <a class="nav-link active" id="student-tab" data-bs-toggle="tab" href="#student">Student Accounts</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="instructor-tab" data-bs-toggle="tab" href="#instructor">Instructor Accounts</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="student">
            <h3>Student Accounts</h3>
            <?php displayTable('student_registration', 'Student_ID', $conn); ?>
        </div>
        <div class="tab-pane fade" id="instructor">
            <h3>Instructor Accounts</h3>
            <?php displayTable('instructor_registration', 'Instructor_ID', $conn); ?>
        </div>
    </div>

    <!-- Bootstrap Modal for Editing Student -->
    <div class='modal' id='editStudentModal'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Edit Student</h4>
                    <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                </div>
                <div class='modal-body'>
                    <form method='post'>
                        <input type='hidden' name='editStudentID' id='editStudentID'>
                        <label for='editStudentFname'>First Name:</label>
                        <input type='text' name='editStudentFname' id='editStudentFname' required>
                        <label for='editStudentMname'>Middle Name:</label>
                        <input type='text' name='editStudentMname' id='editStudentMname'>
                        <label for='editStudentLname'>Last Name:</label>
                        <input type='text' name='editStudentLname' id='editStudentLname' required>
                        <label for='editStudentEmail'>Email:</label>
                        <input type='email' name='editStudentEmail' id='editStudentEmail' required>
                        <label for='editStudentPassword'>Password:</label>
                        <input type='password' name='editStudentPassword' id='editStudentPassword' required>
                        <label for='editStudentCourse'>Course:</label>
                        <input type='text' name='editStudentCourse' id='editStudentCourse' required>
                        <label for='editStudentYear'>Year:</label>
                        <input type='text' name='editStudentYear' id='editStudentYear' required>
                        <label for='editStudentBlock'>Block:</label>
                        <input type='text' name='editStudentBlock' id='editStudentBlock' required>
                        <label for='editStudentContactNo'>Contact Number:</label>
                        <input type='text' name='editStudentContactNo' id='editStudentContactNo' required>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                            <button type='submit' name='saveEditStudent' class='btn btn-primary'>Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#studentTable, #instructorTable').DataTable();

            // Handle Bootstrap Modal for Editing Student
            $('#editStudentModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var studentID = button.data('bs-id'); // Extract info from data-bs-* attributes
                var modal = $(this);

                // Ajax request to fetch student details
                $.ajax({
                    url: 'get_student_details.php', // Create a new PHP file to handle this request
                    type: 'post',
                    data: { studentID: studentID },
                    success: function (response) {
                        var studentDetails = JSON.parse(response);

                        // Set the values in the modal
                        modal.find('#editStudentID').val(studentDetails.Student_ID);
                        modal.find('#editStudentFname').val(studentDetails.Student_fname);
                        modal.find('#editStudentMname').val(studentDetails.Student_mname);
                        modal.find('#editStudentLname').val(studentDetails.Student_lname);
                        modal.find('#editStudentEmail').val(studentDetails.Student_email);
                        modal.find('#editStudentPassword').val(studentDetails.Student_password);
                        modal.find('#editStudentCourse').val(studentDetails.Course);
                        modal.find('#editStudentYear').val(studentDetails.Year);
                        modal.find('#editStudentBlock').val(studentDetails.Block);
                        modal.find('#editStudentContactNo').val(studentDetails.Student_contactno);
                    },
                    error: function () {
                        alert('Error fetching student details');
                    }
                });
            });
        });
    </script>

    <!-- Add your additional HTML, styles, and scripts here -->

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>
