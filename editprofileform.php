<?php
require 'database.php';
session_start();

if (!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] !== TRUE) {
    session_destroy();
    echo "<script>alert('You have not logged in. Please log in first');</script>";
    header("Refresh:0; url=form.php");
    die();
}

    getprofile($_SESSION['username']);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
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
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"],
        input[type="email"] {
            width: calc(100% - 16px); /* Subtract padding and border width */
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        button[type="backHome"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        

        button[type="backHome"]:hover {
            background-color: #0056b3;
        }

        a {
            display: inline-block;
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
    
    <form action="editprofile.php" method="POST" onsubmit="return validateForm(this);">
    <h2>Edit Profile</h2>
        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($_SESSION['fullname']); ?>"><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>"><br>
        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($_SESSION['phone']); ?>"><br>
        <input type="submit" value="Submit">
        <button type="backHome" onclick="window.location.href='index.php'">Back to Home</button>
    </form>
    
</body>
</html>



<script>
    function validateForm(form) {
            var fullname = form.elements["fullname"].value;
            var email = form.elements["email"].value;
            var phone = form.elements["phone"].value;

        // Validate full name
        if (fullname == "") {
            alert("Please enter your full name.");
            return false;
        }

        // Validate email
        if (email == "") {
            alert("Please enter your email.");
            return false;
        } else if (!isValidEmail(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        // Validate phone
        if (phone == "") {
            alert("Please enter your phone number.");
            return false;
        } else if (!isValidPhone(phone)) {
            alert("Please enter a valid phone number.");
            return false;
        }

        return true;
    }

    function isValidEmail(email) {
        var regex = /^\S+@\S+\.\S+$/;
        return regex.test(email);
    }

    function isValidPhone(phone) {
        var regex = /^\d{10}$/;
        return regex.test(phone);
    }
</script>
