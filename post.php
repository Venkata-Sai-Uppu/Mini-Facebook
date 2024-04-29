<?php
session_start();

if (!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] !== TRUE) {
    session_destroy();
    echo "<script>alert('You have not logged in. Please log in first');</script>";
    header("Refresh:0; url=form.php");
    die();
}

if (isset($_POST["title"]) && isset($_POST["content"])) {
    $title = $_POST['title'];
    $content = $_POST["content"];

    // Database connection
    $mysqli = new mysqli('localhost', 'team30', 'team30', 'waph_team');
    if ($mysqli->connect_errno) {
        printf("Database connection failed: %s\n", $mysqli->connect_error);
        exit();
    }

    // Insert the post into the database
    $stmt = $mysqli->prepare("INSERT INTO posts (title, content, owner) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $content, $_SESSION['username']);
    if ($stmt->execute()) {
        echo "Post created successfully!";
        header("Refresh:0; url=index.php");
    } else {
        echo "Failed to create post!";
    }
    $stmt->close();
    $mysqli->close();
}
?>

