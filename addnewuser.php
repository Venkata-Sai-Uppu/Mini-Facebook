<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
        }

        p {
            font-size: 24px;
            margin-bottom: 20px;
        }

        a {
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
            require 'database.php';
            if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["fullname"]) && isset($_POST["email"]) && isset($_POST["phone"])) {
                $username = $_POST["username"];
                $password = $_POST["password"];
                $fullname = $_POST["fullname"];
                $email = $_POST["email"];
                $phone = $_POST["phone"];

                // Check if username already exists
                if (checkUsernameExists($username)) {
                    echo "<script>alert('Username already exists. Please choose a different username.');</script>";
                    echo "<p>Registration failed !!</p>";
                    echo "<p>Please go back to the <a href='form.php'>registration form</a> and try again.</p>";
                } else {
                    // Attempt to add the new user
                    if (addnewuser($username, $password, $fullname, $email, $phone)) {
                        echo "<p>Registration success !!</p>";
                        echo "<p>You may login <a href='form.php'>here</a>.</p>";
                    } else {
                        echo "<p>Registration failed !!</p>";
                    }
                }
            } else {
                echo "<p>No username/password provided !</p>";
            }
        ?>
    </div>
</body>
</html>

