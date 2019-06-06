<?php
    // Get post data
    $user_id = $_GET['user_id'];
    $series_id = $_GET['series_id'];

    if(trim($series_id) == "") {
        echo "<script>alert('Invalid access');history.back();</script>";
        exit;
    }

    if(trim($user_id) == "") {
        echo "<script>alert('Please sign in.');history.back();</script>";
        exit;
    }


    // Prepare SQL Query
    require "connection.php";

    $sql_query_subscribe_exist = "SELECT User_id FROM SUBSCRIBE WHERE User_id = '$user_id' AND Series_id = '$series_id'";
    $sql_query_subscribe = "INSERT INTO SUBSCRIBE(User_id, Series_id) VALUES('$user_id', '$series_id')";
    $sql_query_unsubscribe = "DELETE FROM SUBSCRIBE WHERE User_id = '$user_id' AND Series_id = '$series_id'";

    // Connect to database
    $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

    if($database_connection->connect_error) {
        die("Database connection failed: [Error Code: ".$mysql_connection->connect_errno."]".$mysql_connection->connect_error);
    }

    // Check subscribe already exist
    if(($result = $database_connection->query($sql_query_subscribe_exist)) == FALSE) {
        echo "<script>alert('Database operation failed');history.back();</script>";
        exit;
    } else {
        if($result->num_rows > 0) {
            $subscribe_exist = true;
        } else {
            $subscribe_exist = false;
        }
    }

    // Register User
    if($subscribe_exist) {
        if($database_connection->query($sql_query_unsubscribe) == FALSE) {
            echo "<script>alert('Database operation failed');history.back();</script>";
            exit;
        } else {
            echo "<meta http-equiv='refresh' content='0;url=../series.php?series_id=$series_id'>";
            exit;
        }
    } else {
        if($database_connection->query($sql_query_subscribe) == FALSE) {
            echo "<script>alert('Database operation failed');history.back();</script>";
            exit;
        } else {
            echo "<meta http-equiv='refresh' content='0;url=../series.php?series_id=$series_id'>";
            exit;
        }
    }
?>