<?php
    // Get post data
    $user_id = $_POST['User_id'];
    $user_pw = $_POST['User_password'];
    $user_pw_check = $_POST['User_password_check'];
    $user_name = $_POST['User_name'];

    if(trim($user_id) == "" || trim($user_pw) == "") {
        echo "<script>alert(\"Invalid access\");history.back();</script>";
        exit;
    }

    if($user_pw !== $user_pw_check) {
        echo "<script>alert('Passwords do not match.');history.back();</script>";
        exit;
    }

    // Prepare SQL Query
    require "connection.php";

    $sql_query_id_exist = "SELECT * FROM USER WHERE User_id = '$user_id'";
    if(trim($_POST['User_name']) == "") {
        $sql_query_user_register = "INSERT INTO USER(User_id, User_password) VALUES('$user_id', '$user_pw')";
    } else {
        $sql_query_user_register = "INSERT INTO USER(User_id, User_password, User_name) VALUES('$user_id', '$user_pw', '$user_name')";
    }

    // Connect to database
    $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

    if($database_connection->connect_error) {
        die("Database connection failed: [Error Code: ".$mysql_connection->connect_errno."]".$mysql_connection->connect_error);
    }

    // Check ID already exists
    if(($result = $database_connection->query($sql_query_id_exist)) == FALSE) {
        echo "<script>alert(\"Database operation failed\");history.back();</script>";
        exit;
    } else {
        if($result->num_rows > 0) {
            echo "<script>alert(\"User ID '$user_id' already exist.\");history.back();</script>";
            exit;
        }
    }

    // Register User
    if($database_connection->query($sql_query_user_register) == FALSE) {
        echo "<script>alert(\"Database operation failed\");history.back();</script>";
        exit;
    } else {
        echo "<script>alert(\"Sign up success\");</script>";

        session_start();
        $_SESSION['User_id'] = $user_id;
        $_SESSION['User_name'] = $user_name;

        echo "<meta http-equiv='refresh' content='0;url=../index.php'>";
        exit;
    }
?>