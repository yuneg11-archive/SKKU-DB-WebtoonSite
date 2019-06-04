<?php
    // Get post data
    $title = $_POST['Title'];
    $author = $_POST['Author'];
    $synopsis = $_POST['Synopsis'];
    $cover_path = $_POST['Cover_path'];

    if(trim($title) == "") {
        echo "<script>alert(\"Invalid access\");history.back();</script>";
        exit;
    }

    // Prepare SQL Query
    require "connection.php";

    $sql_query_series_register = "INSERT INTO SERIES(Title, Author, Synopsis, Cover_path) VALUES('$title', '$author', '$synopsis', '$cover_path')";

    // Connect to database
    $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

    if($database_connection->connect_error) {
        die("Database connection failed: [Error Code: ".$mysql_connection->connect_errno."]".$mysql_connection->connect_error);
    }

    // Register Series
    if($database_connection->query($sql_query_series_register) == FALSE) {
        echo "<script>alert(\"Database operation failed\");history.back();</script>";
        exit;
    } else {
        echo "<script>alert(\"Series registration success\");</script>";
        echo "<meta http-equiv='refresh' content='0;url=../index.php'>";
        exit;
    }
?>