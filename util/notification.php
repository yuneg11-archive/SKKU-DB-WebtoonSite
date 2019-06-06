<?php
    // Get post data
    $user_id = $_GET['user_id'];
    $notification_id = $_GET['notification_id'];
    $series_id = $_GET['series_id'];
    $episode_id = $_GET['episode_id'];

    if(trim($user_id) == "" || trim($notification_id) == "") {
        echo "<script>alert('Invalid access');history.back();</script>";
        exit;
    }

    // Prepare SQL Query
    require "connection.php";

    $timestamp = date('Y-m-d G:i:s');
    $sql_query_notification_update = "UPDATE NOTIFICATION SET Notified = '$timestamp' WHERE User_id = '$user_id' AND Notification_id = $notification_id";

    // Connect to database
    $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

    if($database_connection->connect_error) {
        die("Database connection failed: [Error Code: ".$mysql_connection->connect_errno."]".$mysql_connection->connect_error);
    }

    // Update notify timestamp
    if($database_connection->query($sql_query_notification_update) == FALSE) {
        echo "<script>alert('Database operation failed');history.back();</script>";
        exit;
    } else {
        if(trim($series_id) == "" || trim($episode_id) == "") {
            echo "<script>history.back();</script>";
            exit;
        } else {
            echo "<meta http-equiv='refresh' content='0;url=../episode.php?series_id=$series_id&episode_id=$episode_id'>";
            exit;
        }
    }
?>