<?php
require 'database.php';
session_start();

function generateCsrfToken() {
    return bin2hex(random_bytes(32)); // Generate a random token
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
}

if (!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] !== TRUE) {
    echo "<script>alert('You are not logged in. Please log in first');</script>";
    header("Refresh:0; url=form.php");
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'])) {
        echo "<script>alert('CSRF Token Validation Failed');</script>";
        exit;
    }
    
    // Validate password requirements
    $password = $_POST["new_password"];
    if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long and contain at least one letter and one number');</script>";
        exit;
    }

    // Validate new password match
    if ($password !== $_POST["confirm_password"]) {
        echo "<script>alert('New password and confirmation do not match');</script>";
        exit;
    }
    
    $oldPassword = md5($_POST["old_password"]);
    $newPassword = md5($password);

    $changePassResult = changePasswordMysqli($_SESSION["username"], $oldPassword, $newPassword);
    if ($changePassResult === "success") {
        echo "<script>alert('You have successfully changed your password');</script>";
    } elseif ($changePassResult === "mismatch") {
        echo "<script>alert('Sorry!, Please enter correct Old password');</script>";
    } else {
        echo "<script>alert('Sorry! Failed to change your password');</script>";
    }
}

// Generate and store CSRF token
$_SESSION['csrf_token'] = generateCsrfToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h2 {
            color: #333;
        }

        form {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 400px;
            margin: 20px auto;
        }

        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
    <form action="" method="POST">
        <h2>Change Password</h2>
        Old Password: <input type="password" name="old_password" required><br>
        New Password: <input type="password" name="new_password" required><br>
        Confirm New Password: <input type="password" name="confirm_password" required><br>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit">Change Password</button>
        <button type="button" onclick="window.location.href='index.php'">Back to Home</button>
    </form>
</body>
</html>

