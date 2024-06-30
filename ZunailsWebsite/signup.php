<?php
require 'db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $errors = [];


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (!preg_match('/^[0-9]{10}$/', $phone_number)) {
        $errors[] = "Invalid phone number format";
    }

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters long, contain at least one letter, one number, and one special character";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $created_at = date('Y-m-d H:i:s');
        $sql = "INSERT INTO users (username, email, password, created_at, phone_number) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('sssss', $username, $email, $hashed_password, $created_at, $phone_number);
            if ($stmt->execute()) {
                $_SESSION['username'] = $username;

                $redirectUrl = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'index.php';
                unset($_SESSION['redirect_url']);

                header("Location: $redirectUrl");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        function validateForm() {
            const email = document.getElementById('email').value;
            const phoneNumber = document.getElementById('phone_number').value;
            const password = document.getElementById('password').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^[0-9]{10}$/;
            const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            let errors = [];

            if (!emailRegex.test(email)) {
                errors.push("Invalid email format");
            }

            if (!phoneRegex.test(phoneNumber)) {
                errors.push("Invalid phone number format");
            }

            if (!passwordRegex.test(password)) {
                errors.push("Password must be at least 8 characters long, contain at least one letter, one number, and one special character");
            }

            if (errors.length > 0) {
                alert(errors.join("\n"));
                return false;
            }

            return true;
        }
    </script>
</head>
<body class="signup-body">
    <?php include 'nav.php'; ?>
    <div class="signup-container">
        <div class="signup-form">
            <h2>Sign Up</h2>
            <?php if (!empty($errors)) { echo "<p style='color: red;'>" . implode("<br>", $errors) . "</p>"; } ?>
            <form method="POST" action="signup.php" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" placeholder="Phone Number" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>
                <button type="submit">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
