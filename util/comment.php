<?php
    // Get post data
    $user_id = $_POST['User_id'];
    $series_id = $_POST['Series_id'];
    $episode_id = $_POST['Episode_id'];
    $content = $_POST['Content'];

    if(trim($user_id) == "") {
        echo "<script>alert('Please sign in.');history.back();</script>";
        exit;
    }

    if(trim($content) == "") {
        echo "<script>alert('Please fill the comment.');history.back();</script>";
        exit;
    }

    // Prepare SQL Query
    require "connection.php";

    $sql_query_comment_exist = "SELECT User_id FROM COMMENT WHERE User_id = '$user_id' AND Series_id = '$series_id' AND Episode_id = '$episode_id'";
    $sql_query_comment = "INSERT INTO COMMENT(User_id, Series_id, Episode_id, Content) VALUES('$user_id', '$series_id', '$episode_id', '$content')";

    // Connect to database
    $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

    if($database_connection->connect_error) {
        die("Database connection failed: [Error Code: ".$mysql_connection->connect_errno."]".$mysql_connection->connect_error);
    }

    // Check comment already exist
    if(($result = $database_connection->query($sql_query_comment_exist)) == FALSE) {
        echo "<script>alert('Database operation failed');history.back();</script>";
        exit;
    } else {
        if($result->num_rows > 0) {
            echo "<script>alert('Comment already exist');history.back();</script>";
            exit;
        }
    }

    // Register User

    if($database_connection->query($sql_query_comment) == FALSE) {
        echo "<script>alert('Database operation failed');history.back();</script>";
        exit;
    } else {
        echo "<meta http-equiv='refresh' content='0;url=../episode.php?series_id=$series_id&episode_id=$episode_id'>";
        exit;
    }
?>