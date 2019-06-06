<?php
    // Get post data
    $user_id = $_POST['User_id'];
    $series_id = $_POST['Series_id'];
    $episode_id = $_POST['Episode_id'];
    $value = $_POST['Value'];

    if(trim($series_id) == "" || trim($episode_id) == "" || trim($value) == "") {
        echo "<script>alert('Invalid access');history.back();</script>";
        exit;
    }

    if(trim($user_id) == "") {
        echo "<script>alert('Please sign in.');history.back();</script>";
        exit;
    }

    // Prepare SQL Query
    require "connection.php";

    $sql_query_evaluation_exist = "SELECT User_id FROM EVALUATION WHERE User_id = '$user_id' AND Series_id = $series_id AND Episode_id = $episode_id";
    $sql_query_evaluation_insert = "INSERT INTO EVALUATION(User_id, Series_id, Episode_id, Value) VALUES('$user_id', $series_id, $episode_id, $value)";
    $sql_query_evaluation_update = "UPDATE EVALUATION SET Value = $value WHERE User_id = '$user_id' AND Series_id = $series_id AND Episode_id = $episode_id";

    // Connect to database
    $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

    if($database_connection->connect_error) {
        die("Database connection failed: [Error Code: ".$mysql_connection->connect_errno."]".$mysql_connection->connect_error);
    }

    // Check evaluation already exist
    if(($result = $database_connection->query($sql_query_evaluation_exist)) == FALSE) {
        echo "<script>alert('Database operation failed');history.back();</script>";
        exit;
    } else {
        if($result->num_rows > 0) {
            $evaluation_exist = true;
        } else {
            $evaluation_exist = false;
        }
    }

    // Register User
    if($evaluation_exist) {
        if($database_connection->query($sql_query_evaluation_update) == FALSE) {
            echo "<script>alert('Database operation failed');history.back();</script>";
            exit;
        } else {
            echo "<meta http-equiv='refresh' content='0;url=../episode.php?series_id=$series_id&episode_id=$episode_id'>";
            exit;
        }
    } else {
        if($database_connection->query($sql_query_evaluation_insert) == FALSE) {
            echo "<script>alert('Database operation failed');history.back();</script>";
            exit;
        } else {
            echo "<meta http-equiv='refresh' content='0;url=../episode.php?series_id=$series_id&episode_id=$episode_id'>";
            exit;
        }
    }
?>