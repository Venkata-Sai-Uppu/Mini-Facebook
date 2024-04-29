<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'database.php';

    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if the provided credentials match those of a super user
    $stmt = $mysqli->prepare("SELECT username FROM super_users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $_SESSION["super_user"] = $username;
        $stmt->close();
        header("Location: superuser_dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password";
        header("Location: form.php");
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super User Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #bedf;
            color: #333;
            padding-top: 50px;
            text-align: center;
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        form {
            max-width: 300px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: #dc3545;
            margin-top: 10px;
        }

        .back-login {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Super User Login</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>
    <div class="back-login">
        <a href="form.php">Back to Main Login Page</a>
    </div>
</body>
</html>

