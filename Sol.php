<?php
// Define the valid usernames and passwords
$validAccounts = [
    'admin' => '12345',
    'admin1' => '123'
];

// Maximum number of allowed attempts and lockout settings
$maxAttempts = 5;
$lockoutDuration = 10; // Lockout duration in seconds (10 seconds)

// Start the session
session_start();

// Function to check IP lockout status with parameters
function isIPBlocked($ip, $lockoutDuration) {
    if (isset($_SESSION['ip_lockout'][$ip]) && time() < $_SESSION['ip_lockout'][$ip]) {
        return true;
    }
    return false;
}

// Function to log IP on failed attempt with parameters
function logFailedLogin($ip, $maxAttempts, $lockoutDuration) {
    if (!isset($_SESSION['ip_attempts'][$ip])) {
        $_SESSION['ip_attempts'][$ip] = 1;
    } else {
        $_SESSION['ip_attempts'][$ip]++;
    }
    if ($_SESSION['ip_attempts'][$ip] >= $maxAttempts) {
        $_SESSION['ip_lockout'][$ip] = time() + $lockoutDuration;
    }
}

// Use the functions with variables passed as arguments
if (isIPBlocked($_SERVER['REMOTE_ADDR'], $lockoutDuration)) {
    $error = 'Your IP is temporarily blocked due to multiple failed login attempts.';
} else {
    logFailedLogin($_SERVER['REMOTE_ADDR'], $maxAttempts, $lockoutDuration);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'];
    // Pass the required arguments to isIPBlocked
    if (isIPBlocked($ip, $lockoutDuration)) {
        $error = 'Your IP is temporarily blocked due to multiple failed login attempts.';
    } else {
        if (isset($_POST['username'], $_POST['password'], $_POST['captcha'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $captcha = $_POST['captcha'];

            if (isset($_SESSION['lockout'][$username]) && time() < $_SESSION['lockout'][$username]) {
                $error = 'Account locked. Please try again later.';
            } else {
                if ($captcha === $_SESSION['captcha']) {
                    if (isset($validAccounts[$username]) && $validAccounts[$username] === $password) {
                        unset($_SESSION['login_attempts'][$username], $_SESSION['ip_attempts'][$ip]);
                        header('Location: home.php');
                        exit;
                    } else {
                        // Pass the required arguments to logFailedLogin
                        logFailedLogin($ip, $maxAttempts, $lockoutDuration);
                        if (!isset($_SESSION['login_attempts'][$username])) {
                            $_SESSION['login_attempts'][$username] = 1;
                        } else {
                            $_SESSION['login_attempts'][$username]++;
                        }
                        if ($_SESSION['login_attempts'][$username] >= $maxAttempts) {
                            $_SESSION['lockout'][$username] = time() + $lockoutDuration;
                            $error = 'Maximum login attempts exceeded. Account locked.';
                        } else {
                            $error = 'Invalid username or password.';
                        }
                    }
                } else {
                    $error = 'Invalid CAPTCHA.';
                }
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