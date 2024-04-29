<?php
session_start();

// Check if the super user is logged in
if (!isset($_SESSION["super_user"])) {
    header("Location: superuser_login.php");
    exit;
}

// Logout functionality
if (isset($_GET["logout"])) {
    session_unset();
    session_destroy();
    header("Location: superuser_login.php");
    exit;
}

// Database connection
require_once 'database.php';

// Function to fetch all user accounts
function getAllUsers() {
    global $mysqli;
    $users = array();

    $stmt = $mysqli->prepare("SELECT username, fullname, email, phone, status FROM users");
    $stmt->execute();
    $stmt->bind_result($username, $fullname, $email, $phone, $status);

    while ($stmt->fetch()) {
        $user = array(
            "username" => $username,
            "fullname" => $fullname,
            "email" => $email,
            "phone" => $phone,
            "status" => $status
        );
        $users[] = $user;
    }

    $stmt->close();
    return $users;
}

// Function to enable/disable a user account
function updateUserStatus($username, $status) {
    global $mysqli;

    $stmt = $mysqli->prepare("UPDATE users SET status = ? WHERE username = ?");
    $stmt->bind_param("ss", $status, $username);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }

    $stmt->close();
}

// Handle enable/disable user account action
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["action"])) {
        $username = $_POST["username"];
        $action = $_POST["action"];

        $status = ($action == "enable") ? "active" : "inactive";

        if (updateUserStatus($username, $status)) {
            $message = "User account status updated successfully";
        } else {
            $error = "Failed to update user account status";
        }
    }
}

// Fetch all user accounts
$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super User Dashboard</title>
</head>
<style>
    body {
      font-family: Arial, sans-serif;
      background-color: #bedf;
      margin: 0;
      padding: 0;
    }
    h1, h2 {
      text-align: center;
      color: #333;
    }
    #digit-clock {
      text-align: center;
      margin-bottom: 20px;
    }
    .form {
      max-width: 300px;
      margin: 0 auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .text_field {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }
    .button {
      width: 100%;
      padding: 10px;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .button:hover {
      background-color: #0056b3;
    }
  </style>
<body>
    <h2>Welcome, <?php echo $_SESSION["super_user"]; ?>!</h2>
    <p><a href="?logout=true">Logout</a></p>

    <h3>User Accounts</h3>
    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <table border="1">
        <thead>
            <tr>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user["username"]; ?></td>
                    <td><?php echo $user["fullname"]; ?></td>
                    <td><?php echo $user["email"]; ?></td>
                    <td><?php echo $user["phone"]; ?></td>
                    <td><?php echo ucfirst($user["status"]); ?></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="username" value="<?php echo $user["username"]; ?>">
                            <input type="hidden" name="action" value="<?php echo ($user["status"] == "active") ? "disable" : "enable"; ?>">
                            <button type="submit"><?php echo ($user["status"] == "active") ? "Disable" : "Enable"; ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

