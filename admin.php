<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>


<div class="card bg-light shadow">
    <div class="container-fluid">
        <ul class="nav nav-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="student-tab" data-toggle="tab" href="#students">Students</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="instructor-tab" data-toggle="tab" href="#instructors">Instructors</a>
            </li>
        </ul>
    
        <div class="tab-content">
            <div class="tab-pane fade show active" id="students">
                <!-- Students Table -->
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="thead-dark">
                        <tr>
                            <th>Student ID</th>
                            <th>First Name</th>
                       
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Block</th>
                            <th>Contact Number</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            require("config.php");
                            $sql = "SELECT * FROM student_registration";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['Student_ID']}</td>
                                            <td>{$row['Student_fname']}</td>
                                         
                                            <td>{$row['Student_lname']}</td>
                                            <td>{$row['Student_email']}</td>
                                            <td>{$row['Student_password']}</td>
                                            <td>{$row['Course']}</td>
                                            <td>{$row['Year']}</td>
                                            <td>{$row['Block']}</td>
                                            <td>{$row['Student_contactno']}</td>
                                            <td>
                                            <a href='#' class='btn btn-warning btn-sm' onclick='openEditStudentModal(\"{$row['Student_ID']}\"); return false;'>Edit</a>
                                            <a href='#' class='btn btn-danger btn-sm' onclick='deleteRecord(\"{$row['Student_ID']}\", \"student\"); return false;'>Delete</a>
                                            </td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10'>No records found</td></tr>";
                            }
                            $conn->close();
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
    
            <div class="tab-pane fade" id="instructors">
                <div class="table-responsive">
                    <table class="table table-striped mt-3">
                    <thead class="thead-dark">
                        <tr>
                            <th>Instructor ID</th>
                            <th>First Name</th>
                          
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Department</th>
                            <th>Contact Number</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <?php
                            require("config.php");
                            $sql = "SELECT * FROM instructor_registration";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['Instructor_ID']}</td>
                                            <td>{$row['Instructor_fname']}</td>
                                            
                                            <td>{$row['Instructor_lname']}</td>
                                            <td>{$row['Instructor_email']}</td>
                                            <td>{$row['Instructor_password']}</td>
                                            <td>{$row['Department']}</td>
                                            <td>{$row['Instructor_contactno']}</td>
                                            <td>
                                                <a href='#' class='btn btn-warning btn-sm' onclick='openEditInstructorModal(\"{$row['Instructor_ID']}\")'>Edit</a>
                                                <a href='#' class='btn btn-danger btn-sm' onclick='deleteRecord(\"{$row['Instructor_ID']}\", \"instructor\")'>Delete</a>
                                            </td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>No records found</td></tr>";
                            }
                            $conn->close();
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editInstructorModal" tabindex="-1" role="dialog" aria-labelledby="editInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editInstructorModalLabel">Edit Instructor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editInstructorModalBody">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editStudentModalBody">
            </div>
        </div>
    </div>
</div>

<script>
    function openEditStudentModal(studentId) {
        // Load content directly into the modal
        // Adjust the URL and parameters as needed
        $.get('edit_student.php', { studentId: studentId }, function (response) {
            $('#editStudentModalBody').html(response);
            $('#editStudentModal').modal('show');
        });
    }

    // function deleteRecord(recordId, recordType) {
    //     // Same as before
    //     if (confirm("Are you sure you want to delete " + recordType + " ID: " + recordId + "?")) {
    //         $.post('delete_record.php', { recordId: recordId, recordType: recordType })
    //             .done(function (response) {
    //                 console.log(response); // Check the response in the console
    //                 showSuccessAlert(response);
    //                 location.reload();
    //             })
    //             .fail(function (xhr, status, error) {
    //                 console.error(xhr.responseText);
    //             });

    //     }
    // }

    function deleteRecord(recordId, recordType) {
    // Same as before
    alert("Button clicked!"); // Add this line for debugging
    if (confirm("Are you sure you want to delete " + recordType + " ID: " + recordId + "?")) {
        alert("Confirmation received!"); // Add this line for debugging
        $.post('delete_record.php', { recordId: recordId, recordType: recordType })
            .done(function (response) {
                alert(response); // Add this line for debugging
                console.log(response); // Check the response in the console
                showSuccessAlert(response);
                location.reload();
            })
            .fail(function (xhr, status, error) {
                console.error(xhr.responseText);
            });
    }
}








    function showSuccessAlert(message) {
        // Same as before
        console.log(message);
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: message,
            showConfirmButton: false,
            timer: 1500
        });
    }

    function openEditInstructorModal(instructorId) {
        // Load content directly into the modal
        // Adjust the URL and parameters as needed
        $.get('edit_instructor.php', { instructorId: instructorId }, function (response) {
            $('#editInstructorModalBody').html(response);
            $('#editInstructorModal').modal('show');
        });
    }
</script>

</body>
</html>
