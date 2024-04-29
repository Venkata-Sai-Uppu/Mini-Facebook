<?php
require 'database.php';
session_start();

if (!isset($_GET['post_id']) || empty($_GET['post_id'])) {
    echo "<script>alert('Invalid post ID');</script>";
    header("Refresh:0; url=index.php");
    die();
}

$post_id = $_GET['post_id'];

// Fetch post details
$stmt_post = $mysqli->prepare("SELECT owner, title, content, created_at FROM posts WHERE post_id = ?");
$stmt_post->bind_param("i", $post_id);
$stmt_post->execute();
$stmt_post->store_result();

if ($stmt_post->num_rows == 0) {
    echo "<script>alert('Post not found');</script>";
    header("Refresh:0; url=index.php");
    die();
}

$stmt_post->bind_result($owner, $title, $content, $created_at);
$stmt_post->fetch();
$stmt_post->close();

// Handle deleting a comment
if (isset($_POST["delete_comment_id"])) {
    $comment_id = $_POST["delete_comment_id"];
    $owner = $_SESSION["username"];

    // Check if the user is the owner of the comment
    $stmt_check_owner = $mysqli->prepare("SELECT owner FROM comments WHERE id = ?");
    $stmt_check_owner->bind_param("i", $comment_id);
    $stmt_check_owner->execute();
    $stmt_check_owner->store_result();

    if ($stmt_check_owner->num_rows == 1) {
        $stmt_check_owner->bind_result($comment_owner);
        $stmt_check_owner->fetch();

        if ($comment_owner === $owner) {
            // Delete the comment from the database
            $stmt_delete_comment = $mysqli->prepare("DELETE FROM comments WHERE id = ?");
            $stmt_delete_comment->bind_param("i", $comment_id);
            if ($stmt_delete_comment->execute()) {
                echo "<script>alert('Comment deleted successfully');</script>";
                header("Refresh:0;");
                exit;
            } else {
                echo "<script>alert('Failed to delete comment');</script>";
            }
        } else {
            echo "<script>alert('You are not the owner of this comment');</script>";
        }
    } else {
        echo "<script>alert('Comment not found');</script>";
    }
    $stmt_check_owner->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Comments</title>
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

        .comment {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .comment p {
            margin: 5px 0;
        }

        .comment p:first-child {
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
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
        <h2>Post Details</h2>
        <p>Posted by: <?php echo htmlspecialchars($owner); ?></p>
        <p>Title: <?php echo htmlspecialchars($title); ?></p>
        <p>Content: <?php echo htmlspecialchars($content); ?></p>
        <p>Created at: <?php echo htmlspecialchars($created_at); ?></p>

        <h2>Comments</h2>
        
        <?php
        // Fetch comments for this post
        $stmt_comments = $mysqli->prepare("SELECT id, owner, content, created_at FROM comments WHERE post_id = ?");
        $stmt_comments->bind_param("i", $post_id);
        $stmt_comments->execute();
        $stmt_comments->store_result();

        if ($stmt_comments->num_rows > 0) {
            $stmt_comments->bind_result($comment_id, $comment_owner, $comment_content, $comment_created_at);
            while ($stmt_comments->fetch()) {
                echo "<div class='comment'>";
                echo "<p>Commented by: {$comment_owner}</p>";
                echo "<p>{$comment_content}</p>";
                echo "<p>Commented at: {$comment_created_at}</p>";
                // Edit and delete buttons for comment
            if ($_SESSION['username'] === $comment_owner) {
                //echo "<a href='edit_comment.php?comment_id={$comment_id}'>Edit</a> | ";
                echo "<div style='padding: 2px;'>";
            echo "<button onclick=\"window.location.href='edit_comment.php?comment_id={$comment_id}'\" style='margin-left: 10px; background-color: #007bff; color: #fff; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;width:130px;'>Edit</button>";
                 echo "</div>";
                echo "<form action='view_comments.php?post_id={$post_id}' method='POST'>";
                echo "<input type='hidden' name='delete_comment_id' value='{$comment_id}'>";
                 echo "<div style='padding: 2px;'>";
                echo "<input type='submit' value='Delete' style='margin-left: 10px; background-color: #FF0000; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; cursor: pointer;width:130px;'>";
                echo "</div>";
                echo "</form>";
            }
                echo "</div>";
            }
        } else {
            echo "<p>No comments available for this post</p>";
        }
        $stmt_comments->close();
        ?>



        <a class="btn" href="index.php">Back to Home</a>
    </div>
</body>
</html>

