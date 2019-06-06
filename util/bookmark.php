<?php
    // Get post data
    $user_id = $_GET['user_id'];
    $series_id = $_GET['series_id'];
    $episode_id = $_GET['episode_id'];

    if(trim($series_id) == "" || trim($episode_id) == "") {
        echo "<script>alert('Invalid access');history.back();</script>";
        exit;
    }

    if(trim($user_id) == "") {
        echo "<script>alert('Please sign in.');history.back();</script>";
        exit;
    }


    // Prepare SQL Query
    require "connection.php";

    $sql_query_bookmark_exist = "SELECT User_id FROM BOOKMARK WHERE User_id = '$user_id' AND Series_id = $series_id AND Episode_id = $episode_id";
    $sql_query_bookmark = "INSERT INTO BOOKMARK(User_id, Series_id, Episode_id) VALUES('$user_id', $series_id, $episode_id)";
    $sql_query_unbookmark = "DELETE FROM BOOKMARK WHERE User_id = '$user_id' AND Series_id = $series_id AND Episode_id = $episode_id";

    // Connect to database
    $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

    if($database_connection->connect_error) {
        die("Database connection failed: [Error Code: ".$mysql_connection->connect_errno."]".$mysql_connection->connect_error);
    }

    // Check subscribe already exist
    if(($result = $database_connection->query($sql_query_bookmark_exist)) == FALSE) {
        echo "<script>alert('Database operation failed');history.back();</script>";
        exit;
    } else {
        if($result->num_rows > 0) {
            $bookmark_exist = true;
        } else {
            $bookmark_exist = false;
        }
    }

    // Register User
    if($bookmark_exist) {
        if($database_connection->query($sql_query_unbookmark) == FALSE) {
            echo "<script>alert('Database operation failed');history.back();</script>";
            exit;
        } else {
            echo "<meta http-equiv='refresh' content='0;url=../episode.php?series_id=$series_id&episode_id=$episode_id'>";
            exit;
        }
    } else {
        if($database_connection->query($sql_query_bookmark) == FALSE) {
            echo "<script>alert('Database operation failed');history.back();</script>";
            exit;
        } else {
            echo "<meta http-equiv='refresh' content='0;url=../episode.php?series_id=$series_id&episode_id=$episode_id'>";
            exit;
        }
    }
?>