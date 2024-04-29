<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
            session_start();

            if (!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] !== TRUE) {
                session_destroy();
                echo "<script>alert('You have not logged in. Please log in first');</script>";
                header("Refresh:0; url=form.php");
                die();
            }

            if (isset($_POST["fullname"]) && isset($_POST["email"]) && isset($_POST["phone"])) {
                $fullname = $_POST["fullname"];
                $email = $_POST["email"];
                $phone = $_POST["phone"];

                if (editProfile($_SESSION['username'], $fullname, $email, $phone)) {
                    $_SESSION['fullname'] = $fullname;
                    $_SESSION['email'] = $email;
                    $_SESSION['phone'] = $phone;
                    echo "<p>Profile updated successfully!</p>";
                } else {
                    echo "<p>Failed to update profile!</p>";
                }
            }
        ?>
        <a href="index.php">&lt; Back to home page</a>
    </div>
</body>
</html>

