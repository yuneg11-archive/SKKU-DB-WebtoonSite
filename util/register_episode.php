<?php
    // Get post data
    $title = $_POST['Title'];
    $series_id = $_POST['Series_id'];
    $cover_path = $_POST['Cover_path'];
    $content_path = $_POST['Content_path'];

    if(trim($title) == "" || trim($series_id) == "" || trim($content_path) == "") {
        echo "<script>alert('Invalid access');history.back();</script>";
        exit;
    }

    // Connect to database
    require "connection.php";

    $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);
    if($database_connection->connect_error) {
        die("Database connection failed: [Error Code: ".$mysql_connection->connect_errno."]".$mysql_connection->connect_error);
    }

    // Prepare SQL Query
    $sql_query_episode_count = "SELECT COUNT(*) as Count FROM EPISODE WHERE Series_id = $series_id";
    if(($result = $database_connection->query($sql_query_episode_count)) == FALSE) {
        echo "<script>alert('Database operation failed');history.back();</script>";
        exit;
    } else {
        $episode_id = $result->fetch_assoc()['Count'] + 1;
    }

    $sql_query_episode_register = "INSERT INTO EPISODE(Series_id, Episode_id, Title, Cover_path) VALUES($series_id, $episode_id, '$title', '$cover_path')";
    $sql_query_episode_remove = "DELETE FROM EPISODE WHERE Series_id = $series_id AND Episode_id = $episode_id";
    $sql_query_image_register_partial = "INSERT INTO IMAGELIST(Series_id, Episode_id, Image_number, Image_path) VALUES($series_id, $episode_id, ";
    $sql_query_subscribe_user_list = "SELECT User_id FROM SUBSCRIBE WHERE Series_id = $series_id";

    // Register Series
    if($database_connection->query($sql_query_episode_register) == FALSE) {
        echo "<script>alert('Database operation failed');history.back();</script>";
        exit;
    }

    // Register Images
    if($content_path[0] === '/') {
        $directory = "../content".$content_path;
    } else {
        $directory = "../content/".$content_path;
    }
    if(substr($directory, -1) === '/') {
        $file_path = $directory."*.{jpg,png,gif}";
    } else {
        $file_path = $directory."/*.{jpg,png,gif}";
    }

    $image_number = 1;
    foreach(glob($file_path, GLOB_BRACE) as $image_path) {
        $image_path = str_replace("../content/", "", $image_path);
        $sql_query_image_register = $sql_query_image_register_partial."$image_number, '$image_path')";
        if($database_connection->query($sql_query_image_register) == FALSE) {
            $database_connection->query($sql_query_episode_remove);
            echo "<script>alert('Database operation failed');history.back();</script>";
            exit;
        }
        $image_number++;
    }

    // Notify to users
    if(($result = $database_connection->query($sql_query_subscribe_user_list)) == FALSE) {
        echo "<script>alert('Database operation failed');history.back();</script>";
        exit;
    } else {
        while($row = $result->fetch_assoc()) {
            $user_id = $row["User_id"];

            $sql_query_notification_count = "SELECT COUNT(*) as Count FROM NOTIFICATION WHERE User_id = '$user_id'";
            if(($notification_count_result = $database_connection->query($sql_query_notification_count)) == FALSE) {
                echo "<script>alert('Database operation failed');history.back();</script>";
                exit;
            }

            $notification_id = $notification_count_result->fetch_assoc()["Count"] + 1;
            $message = "Episode &episode of Series &series is updated.";
            $sql_query_notify_user = "INSERT INTO NOTIFICATION(Notification_id, User_id, Series_id, Episode_id, Message) VALUES($notification_id, '$user_id', $series_id, $episode_id, '$message')";
            if($database_connection->query($sql_query_notify_user) == FALSE) {
                echo "<script>alert('Database operation failed');history.back();</script>";
                exit;
            }
        }
    }

    // Close connection
    $database_connection->close();

    echo "<script>alert('Episode registration success');</script>";
    echo "<meta http-equiv='refresh' content='0;url=../index.php'>";
    exit;
?>