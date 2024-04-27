<?php
session_start();

// Define the valid username and password
$validUsername = 'admin';
$validPassword = '12345';

// Initialize or retrieve the failure count
if (!isset($_SESSION['fail_count'])) {
    $_SESSION['fail_count'] = 0;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Implementing delay to slow down brute force attack
    sleep(1);

    // Check if the username and password keys exist in the $_POST array
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Get the entered username and password
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check if the entered username and password are correct
        if ($username === $validUsername && $password === $validPassword) {
            // Reset fail count on successful login
            $_SESSION['fail_count'] = 0;
            // Redirect to the home page
            header('Location: home.php');
            exit;
        } else {
            // Increment the failure count
            $_SESSION['fail_count']++;

            // Check if there have been more than 5 failed attempts
            if ($_SESSION['fail_count'] > 5) {
                $error = 'Too many failed login attempts. Please try again later.';
                // Optionally, introduce a longer delay or lockout period here
                session_destroy();  // Reset the session to clear failure count
            } else {
                $error = 'Invalid username or password.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-form {
            max-width: 400px;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-form h2 {
            text-align: center;
        }

        .login-form label {
            display: block;
            margin-bottom: 10px;
        }

        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }

        .login-form input[type="submit"] {
            width: 100%;
            padding: 8px;
            background-color: #4caf50;
            border: none;
            color: #fff;
            cursor: pointer;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Login</h2>
        <?php if (isset($error)) { ?>
            <p><?php echo $error; ?></p>
        <?php } ?>
        <form method="POST" action="login.php">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
