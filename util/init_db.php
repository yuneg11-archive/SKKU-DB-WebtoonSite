<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Initialize Database</title>
    </head>
    <body>
        <?php
            // Prepare SQL Query
            require "connection.php";

            $sql_query_drop_database = "DROP DATABASE $mysql_database";
            $sql_query_create_database = "CREATE DATABASE $mysql_database";

            $sql_query_create_table = file_get_contents("../sql_query/tables.sql");

            // Connect to MySQL
            $mysql_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password);

            // Initialize database
            if($mysql_connection->connect_error) {
                die("Database connection failed: [Error Code: ".$mysql_connection->connect_errno."]".$mysql_connection->connect_error);
            } else {
                // Drop existing database
                $mysql_connection->query($sql_query_drop_database);

                // Create database
                if($mysql_connection->query($sql_query_create_database) == FALSE){
                    die("Database creation failed: [Error Code: ".$mysql_connection->connect_errno."]".$mysql_connection->connect_error);
                } else {
                    echo 'Database creation success<br>';
                }
            }

            // Close connection to MySQL
            $mysql_connection->close();

            // Connect to database
            $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

            // Initialize tables
            if($database_connection->connect_error) {
                die("Database connection failed: [Error Code: ".$database_connection->connect_errno."]".$database_connection->connect_error);
            } else {
                // Create table
                if ($database_connection->multi_query($sql_query_create_table) == FALSE) {
                    die("Table creation failed: [Error Code: ".$database_connection->connect_errno."]".$database_connection->connect_error);
                } else {
                    echo "Table creation success<br>";
                }
            }

            // Close connection to database
            $database_connection->close();
        ?>
    </body>
</html>
