<?php
session_start();
require 'db_connection.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM admin WHERE username=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
  
            if ($password == $admin['password']) {
                $_SESSION['admin_logged_in'] = true;
                header("Location: admin_panel.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid username.";
        }
        $stmt->close();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="admin-login-body">
    <div class="admin-login-container">
        <div class="admin-login-form">
            <h2>Admin Login</h2>
            <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>
                <button type="submit" class="login-button">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
