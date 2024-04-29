<?php

$mysqli = new mysqli('localhost', 'team30', 'team30', 'waph_team');
    if ($mysqli->connect_error) {
        printf("Database connection failed %s\n", $mysqli->connect_error);
        exit();
    }


//DB call to verify login credentials
function checklogin_mysqli($username, $password)
{
    global $mysqli;
    $prepared_sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $mysqli->prepare($prepared_sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if ($row['status'] === 'active') {
            return true;
        } else {
           // User is inactive or disabled
            echo "<script>alert('User is inactive or disabled');window.location='form.php';</script>";
        session_unset();
        die();
        }
    }
    return false; // User not found or incorrect credentials
}


//DB call to update new password during password change
function changePasswordMysqli($username, $oldPassword, $newPassword)
{   
    global $mysqli;
    $prepared_sql = "SELECT password FROM users WHERE username = ?;";
    $stmt = $mysqli->prepare($prepared_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $userDBPassword = $row['password'];

        if ($oldPassword !== $userDBPassword) {
            return "mismatch";
        }

        $update_sql = "UPDATE users SET password = ? WHERE username = ?;";
        $update_stmt = $mysqli->prepare($update_sql);
        $update_stmt->bind_param("ss", $newPassword, $username);
        if ($update_stmt->execute()) {
            return "success"; 
        }
    }
    return "failure"; 
}

//DB call for getting profile details
function getprofile($username) {

    global $mysqli;
    $prepared_sql = "SELECT fullname, email, phone FROM users WHERE username = ?;";
    $stmt = $mysqli->prepare($prepared_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['fullname'] = $row['fullname'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['phone'] = $row['phone'];
    }
    else {
        echo 'No such username found';
    }
}


//DB call to add new user information
function addnewuser($username, $password,$fullname,$email,$phone) {
    global $mysqli;
    $prepared_sql = "INSERT INTO users (username,password,fullname,email,phone) VALUES (?,md5(?),?,?,?);";
    $stmt =$mysqli->prepare($prepared_sql);
    $stmt->bind_param("sssss",$username, $password, $fullname, $email, $phone);
    if($stmt->execute())
        return TRUE;
    return FALSE;
}


//DB call to update user profile details
function editProfile($username, $fullname, $email, $phone)
{
    global $mysqli;
    $prepared_sql = "UPDATE users SET fullname=?, email=?, phone=? WHERE username=?";
    $stmt = $mysqli->prepare($prepared_sql);
    $stmt->bind_param("ssss", $fullname, $email, $phone, $username);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
?>
