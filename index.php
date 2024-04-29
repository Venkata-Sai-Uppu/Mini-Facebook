<?php
require 'database.php';
session_start();

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = md5($_POST["password"]); // Hash the password using md5()

    if (checklogin_mysqli($username, $password)) {
        $_SESSION["authenticated"] = TRUE;
        $_SESSION["username"] = $username;
        $_SESSION["browser"] = $_SERVER["HTTP_USER_AGENT"]; // Store browser info in the session
    } else {
        echo "<script>alert('Invalid User Name or Password');window.location='form.php';</script>";
        session_unset();
        die();
    }
}
if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== TRUE) {
    session_destroy();
    echo "<script>alert('You have not logged in. Please log in first');</script>";
    header("Refresh:0; url=form.php");
    die();
}

// Check for session hijacking
if ($_SESSION["browser"] !== $_SERVER["HTTP_USER_AGENT"]) {
    echo "<script>alert('Session hijacking is detected!!');</script>";
    session_unset();
    session_destroy();
    header("Refresh:0; url=form.php");
    die();
}

// Handle posting new content
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["title"]) && isset($_POST["content"])) {
        $title = $_POST["title"];
        $content = $_POST["content"];

        $owner = $_SESSION['username'];

        // Insert the post into the database
        $stmt = $mysqli->prepare("INSERT INTO posts (title, content, owner) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $content, $owner);
        if ($stmt->execute()) {
            echo "<script>alert('Post created successfully');</script>";
            header("Refresh:0; url=index.php");
        } else {
            echo "<script>alert('Failed to create post');</script>";
        }
        $stmt->close();
    } 
    //else {
        //echo "<script>alert('Invalid form data');</script>";
    //}

    // Handle adding new comments
    if (isset($_POST["comment_content"]) && isset($_POST["post_id"])) {
        $comment_content = $_POST["comment_content"];
        $post_id = $_POST["post_id"];

        $owner = $_SESSION['username'];

        // Insert the comment into the database
        $stmt = $mysqli->prepare("INSERT INTO comments (post_id, owner, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $post_id, $owner, $comment_content);
        if ($stmt->execute()) {
            echo "<script>alert('Comment added successfully');</script>";
            header("Refresh:0; url=index.php");
        } else {
            echo "<script>alert('Failed to add comment');</script>";
        }
        $stmt->close();
    }
    // Handle deleting a post
    if (isset($_POST["delete_post_id"])) {
        $post_id = $_POST["delete_post_id"];
        $owner = $_SESSION["username"];

        // Check if the user is the owner of the post
        $stmt_check_owner = $mysqli->prepare("SELECT owner FROM posts WHERE post_id = ?");
        $stmt_check_owner->bind_param("i", $post_id);
        $stmt_check_owner->execute();
        $stmt_check_owner->store_result();

        if ($stmt_check_owner->num_rows == 1) {
            $stmt_check_owner->bind_result($post_owner);
            $stmt_check_owner->fetch();

            if ($post_owner === $owner) {
                // Delete the post from the database
                $stmt_delete_post = $mysqli->prepare("DELETE FROM posts WHERE post_id = ?");
                $stmt_delete_post->bind_param("i", $post_id);
                if ($stmt_delete_post->execute()) {
                    echo "<script>alert('Post deleted successfully');</script>";
                    header("Refresh:0; url=index.php");
                } else {
                    echo "<script>alert('Failed to delete post');</script>";
                }
            } else {
                echo "<script>alert('You are not the owner of this post');</script>";
            }
        } else {
            echo "<script>alert('Post not found');</script>";
        }
        $stmt_check_owner->close();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h2 {
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .post {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .post p {
            margin: 5px 0;
        }

        .post p:first-child {
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <a class="btn" href="changePassword.php">CHANGE PASSWORD</a>
        <a class="btn" href="editprofileform.php">EDIT PROFILE</a>
        <a class="btn" style='background-color:#FF0000' href="logout.php">LOGOUT</a>
        <h2> ADD the POSTS you LIKE </h2>
        <form action="index.php" method="POST">
            <input type="text" class="text_field" name="title" placeholder="Title">
            <textarea name="content" rows="4" cols="50" placeholder="Content"></textarea><br>
            <div style='padding: 2px;'>
            <button style='margin-left: 10px; background-color: #007bff; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; cursor: pointer;width:130px' type="submit" >Post</button>
            </div>
        </form>

        <?php
        // Fetch posts from the database
        $prepared_sql = "SELECT post_id, owner, title, content, created_at FROM posts ORDER BY created_at DESC";
        $stmt = $mysqli->prepare($prepared_sql);
        if (!$stmt->execute()) {
            echo "Failed to fetch posts from the database";
            return false;
        }

        $post_id = NULL;
        $title = NULL;
        $content = NULL;
        $created_at = NULL;
        $owner = NULL;

        if (!$stmt->bind_result($post_id, $owner, $title, $content, $created_at)) {
            echo "Binding failed for posts";
            return false;
        }

        while ($stmt->fetch()) {
            echo "<div class='post'>";
            echo "<p>Posted by: {$owner}</p>";
            echo "<p>{$title}</p>";
            echo "<p>{$content}</p>";
            echo "<p>{$created_at}</p>";
            
            
            // Add comment form for each post
            echo "<form action='index.php' method='POST'>";
            echo "<input type='hidden' name='post_id' value='{$post_id}'>";
            echo "<textarea name='comment_content' placeholder='Add a comment'></textarea><br>";
            echo "<div style='padding: 2px;'>";
            echo "<button style='margin-left: 10px; background-color: #007bff; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; cursor: pointer;width:130px;' type='submit'>Comment</button>";
            echo "</div>";
            echo "</form>";
            
            // Link to view comments for this post
            echo "<div style='padding: 2px;'>";
            
            echo "<button onclick=\"window.location.href='view_comments.php?post_id={$post_id}'\" style='margin-left: 10px; background-color: #007bff; color: #fff; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;width:130px;'>View Comments</button>";

            echo "</div>";


            
            // Edit and delete buttons for post owner
            if ($_SESSION['username'] === $owner) {
            echo "<div style='padding: 2px;'>";
            echo "<button onclick=\"window.location.href='edit_post.php?post_id={$post_id}'\" style='margin-left: 10px; background-color: #007bff; color: #fff; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;width:130px;'>Edit Post</button>";
                 echo "</div>";
                echo "<form action='index.php' method='POST'>";
                echo "<input type='hidden' name='delete_post_id' value='{$post_id}'>";
                echo "<div style='padding: 2px;'>";
                echo "<input type='submit' value='Delete Post' style='margin-left: 10px; background-color: #FF0000; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; cursor: pointer;width:130px;'>";
                echo "</div>";
                echo "</form>";
            }
            
            echo "</div>"; // End of post div
        }
        $stmt->close();
        ?>
    </div>
</body>
</html>
