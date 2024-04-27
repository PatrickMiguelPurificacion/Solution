<?php
// Define the valid usernames and passwords
$validAccounts = [
    'admin' => '12345',
    'admin1' => '123'
];

// Maximum number of allowed attempts
$maxAttempts = 5;

// Lockout duration in seconds (10 seconds)
$lockoutDuration = 10;

// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the username, password, and captcha keys exist in the $_POST array
    if (isset($_POST['username'], $_POST['password'], $_POST['captcha'])) {
        // Get the entered username, password, and captcha value
        $username = $_POST['username'];
        $password = $_POST['password'];
        $captcha = $_POST['captcha'];

        // Check if the account is locked
        if (isset($_SESSION['lockout'][$username]) && time() < $_SESSION['lockout'][$username]) {
            $remainingTime = date('H:i:s', $_SESSION['lockout'][$username] - time());
            $error = 'Account locked. Please try again after ' . $remainingTime;
        } else {
            // Verify the captcha value
            if ($captcha === $_SESSION['captcha']) {  // Captcha verification passed

                // Check if the entered username and password are correct
                if (isset($validAccounts[$username]) && $validAccounts[$username] === $password) {
                    // Redirect to the home page
                    unset($_SESSION['login_attempts'][$username]);
                    header('Location: home.php');
                    exit;
                } else {
                    // Increment the number of failed attempts
                    if (!isset($_SESSION['login_attempts'][$username])) {
                        $_SESSION['login_attempts'][$username] = 1;
                    } else {
                        $_SESSION['login_attempts'][$username]++;
                    }

                    // Check if the maximum number of attempts is reached
                    if ($_SESSION['login_attempts'][$username] >= $maxAttempts) {
                        // Set the lockout timestamp and display an error message
                        $_SESSION['lockout'][$username] = time() + $lockoutDuration;
                        $remainingTime = date('H:i:s', $lockoutDuration);
                        $error = 'Maximum login attempts exceeded. Your account has been locked. Please try again after ' . $remainingTime;
                        // Additional action to lock the account can be performed here
                    } else {
                        // Display an error message
                        $remainingAttempts = $maxAttempts - $_SESSION['login_attempts'][$username];
                        $error = 'Invalid username or password. Remaining attempts: ' . $remainingAttempts;
                    }
                }
            } else {
                // Display an error message for incorrect captcha
                $error = 'Invalid CAPTCHA. Please try again.';
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

        .login-form img {
            display: block;
            margin-bottom: 10px;
        }
    </style>
    <script>
        function checkForm(form) {
            // Check if the CAPTCHA field is not empty
            if (form.captcha.value === '') {
                alert('Please enter the CAPTCHA code.');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="login-form">
        <h2>Login</h2>
        <?php if (isset($error)) { ?>
            <p><?php echo $error; ?></p>
        <?php } ?>
        <form method="POST" action="login.php" onsubmit="return checkForm(this);">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Enter CAPTCHA:</label>
            <img src="captcha.php" alt="CAPTCHA" width="100" height="30">
            <input type="text" name="captcha" required>

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>