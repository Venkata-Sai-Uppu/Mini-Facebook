<?php
require 'database.php';
session_start();

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

// Handle editing a post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_id"])) {
    $post_id = $_POST["post_id"];
    $title = $_POST["title"];
    $content = $_POST["content"];

    $owner = $_SESSION['username'];

    // Check if the user is the owner of the post
    $stmt_check_owner = $mysqli->prepare("SELECT owner FROM posts WHERE post_id = ?");
    $stmt_check_owner->bind_param("i", $post_id);
    $stmt_check_owner->execute();
    $stmt_check_owner->store_result();

    if ($stmt_check_owner->num_rows == 1) {
        $stmt_check_owner->bind_result($post_owner);
        $stmt_check_owner->fetch();

        if ($post_owner === $owner) {
            // Update the post in the database
            $stmt_update_post = $mysqli->prepare("UPDATE posts SET title = ?, content = ? WHERE post_id = ?");
            $stmt_update_post->bind_param("ssi", $title, $content, $post_id);
            if ($stmt_update_post->execute()) {
                echo "<script>alert('Post updated successfully');</script>";
                header("Refresh:0; url=index.php");
            } else {
                echo "<script>alert('Failed to update post');</script>";
            }
        } else {
            echo "<script>alert('You are not the owner of this post');</script>";
        }
    } else {
        echo "<script>alert('Post not found');</script>";
    }
    $stmt_check_owner->close();
}

// Fetch post details based on post ID for pre-filling the form fields
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Query database to get post details
    $stmt_get_post = $mysqli->prepare("SELECT title, content FROM posts WHERE post_id = ?");
    $stmt_get_post->bind_param("i", $post_id);
    $stmt_get_post->execute();
    $stmt_get_post->store_result();

    if ($stmt_get_post->num_rows == 1) {
        $stmt_get_post->bind_result($title, $content);
        $stmt_get_post->fetch();
    } else {
        echo "<script>alert('Post not found');</script>";
        header("Refresh:0; url=index.php");
        die();
    }
    $stmt_get_post->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
        <h2>Edit Post</h2>
        <form action="edit_post.php" method="POST">
            <input type="hidden" name="post_id" value="<?php echo $_GET['post_id']; ?>">
            <input type="text" class="text_field" name="title" placeholder="Title" value="<?php echo htmlspecialchars($title); ?>">
            <textarea name="content" rows="4" cols="50" placeholder="Content"><?php echo htmlspecialchars($content); ?></textarea><br>
            <input type="submit" value="Update Post">
        </form>
    </div>
</body>
</html>

