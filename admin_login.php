<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username and password match the static values
    if ($username == 'admin' && $password == 'admin') {
        // Successful login, redirect to admin.php
        header("Location: admin.php");
        exit();
    } else {
        // Invalid login, show an error message
        $errorMessage = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="card">
    <div class="container">
        <h2>Login</h2>
        <?php
        if (isset($errorMessage)) {
            echo '<div class="alert alert-danger" role="alert">' . $errorMessage . '</div>';
        }
        ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</div>

</body>
</html>
