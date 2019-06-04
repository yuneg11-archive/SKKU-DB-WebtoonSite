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
        $row = $result->fetch_assoc();
        $episode_id = $row['Count'] + 1;
    }

    $sql_query_episode_register = "INSERT INTO EPISODE(Series_id, Episode_id, Title, Cover_path) VALUES($series_id, $episode_id, '$title', '$cover_path')";
    $sql_query_episode_remove = "DELETE FROM EPISODE WHERE Series_id = $series_id AND Episode_id = $episode_id";
    $sql_query_image_register_partial = "INSERT INTO IMAGELIST(Series_id, Episode_id, Image_number, Image_path) VALUES($series_id, $episode_id, ";

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
        $sql_query_image_register = $sql_query_image_register_partial."$image_number, '$image_path')";
        if($database_connection->query($sql_query_image_register) == FALSE) {
            $database_connection->query($sql_query_episode_remove);
            echo "<script>alert('Database operation failed');history.back();</script>";
            exit;
        }
        $image_number++;
    }

    $database_connection->close();

    echo "<script>alert('Episode registration success');</script>";
    echo "<meta http-equiv='refresh' content='0;url=../index.php'>";
    exit;
?>