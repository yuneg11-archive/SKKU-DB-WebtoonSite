<?php
    // Get post data
    $user_id = $_POST['User_id'];
    $user_pw = $_POST['User_password'];

    if(trim($user_id) == "" || trim($user_pw) == "") {
        echo "<script>alert(\"Invalid access\");history.back();</script>";
        exit;
    }

    // Prepare SQL Query
    require "connection.php";

    $sql_query_check_user = "SELECT User_id, User_name FROM USER WHERE User_id = '$user_id' AND User_password = '$user_pw'";

    // Connect to database
    $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

    if($database_connection->connect_error) {
        die("Database connection failed: [Error Code: ".$mysql_connection->connect_errno."]".$mysql_connection->connect_error);
    }

    // Register User
    if(($result = $database_connection->query($sql_query_check_user)) == FALSE) {
        echo "<script>alert(\"Database operation failed\");history.back();</script>";
        exit;
    } else {
        if($result->num_rows === 1) {
            session_start();
            $row = $result->fetch_assoc();
            $_SESSION['User_id'] = $row["User_id"];
            $_SESSION['User_name'] = $row["User_name"];

            echo "<meta http-equiv='refresh' content='0;url=../index.php'>";
            exit;
        }
        echo "<script>alert(\"Sign in failed\");history.back();</script>";
        exit;
    }
?>