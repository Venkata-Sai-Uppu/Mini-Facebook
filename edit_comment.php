<?php
require 'database.php';
session_start();

if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== TRUE) {
    session_destroy();
    header("Location: form.php");
    exit;
}

// Check for session hijacking
if ($_SESSION["browser"] !== $_SERVER["HTTP_USER_AGENT"]) {
    session_unset();
    session_destroy();
    header("Location: form.php");
    exit;
}

// Handle editing a comment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comment_id"])) {
    $comment_id = $_POST["comment_id"];
    $comment_content = $_POST["content"];

    $owner = $_SESSION['username'];

    // Check if the user is the owner of the comment
    $stmt_check_owner = $mysqli->prepare("SELECT owner, post_id  FROM comments WHERE id = ?");
    $stmt_check_owner->bind_param("i", $comment_id);
    $stmt_check_owner->execute();
    $stmt_check_owner->store_result();

    if ($stmt_check_owner->num_rows == 1) {
        $stmt_check_owner->bind_result($comment_owner, $post_id);
        $stmt_check_owner->fetch();

        if ($comment_owner === $owner) {
            // Update the comment in the database
            $stmt_update_comment = $mysqli->prepare("UPDATE comments SET content = ? WHERE id = ?");
            $stmt_update_comment->bind_param("si", $comment_content, $comment_id);
            if ($stmt_update_comment->execute()) {
                echo "<script>alert('comment updated successfully');</script>";
                header("Refresh:0; url=view_comments.php?post_id={$post_id}");
                exit;
            } else {
                echo "<script>alert('Failed to update comment');</script>";
            }
        } else {
            echo "<script>alert('You are not the owner of this comment');</script>";
        }
    } else {
        echo "<script>alert('Comment not found');</script>";
    }
    $stmt_check_owner->close();
}

// Fetch comment details based on comment ID for pre-filling the form fields
if (isset($_GET['comment_id'])) {
    $comment_id = $_GET['comment_id'];

    // Query database to get comment details
    $stmt_get_post = $mysqli->prepare("SELECT content FROM comments WHERE id = ?");
    $stmt_get_post->bind_param("i", $comment_id);
    $stmt_get_post->execute();
    $stmt_get_post->store_result();

    if ($stmt_get_post->num_rows == 1) {
        $stmt_get_post->bind_result($comment_content);
        $stmt_get_post->fetch();
    } else {
        echo "<script>alert('Comment not found');</script>";
        header("Refresh:0; url=index.php");
        exit;
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
        <h2>Edit Comment</h2>
        <form action="edit_comment.php" method="POST">
            <input type="hidden" name="comment_id" value="<?php echo isset($_GET['comment_id']) ? htmlspecialchars($_GET['comment_id']) : ''; ?>">
            <textarea name="content" rows="4" cols="50" placeholder="Content"><?php echo isset($comment_content) ? htmlspecialchars($comment_content) : ''; ?></textarea><br>
            <input type="submit" value="Update Comment">
        </form>
    </div>
</body>
</html>
