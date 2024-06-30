<?php
require 'db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];

                $redirectUrl = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'index.php';
                unset($_SESSION['redirect_url']);

                header("Location: $redirectUrl");
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="login-body">
    <?php include 'nav.php'; ?>
    <div class="login-container">
        <div class="login-form">
            <h2>Login</h2>
            <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const redirectUrl = sessionStorage.getItem('redirectAfterLogin');
            if (redirectUrl) {
                sessionStorage.removeItem('redirectAfterLogin');
                window.location.href = redirectUrl;
            }
        });
    </script>
</body>
</html>
