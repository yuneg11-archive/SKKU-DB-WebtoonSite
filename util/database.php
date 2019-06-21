<?php
    class Database {
        private $mysql_hostname = "127.0.0.1";
        private $mysql_username = "root";
        private $mysql_password = "12345678";
        private $mysql_database = "webtoon";
        private $mysql_connection;

        function __construct() {
            $this->mysql_connection = new mysqli($this->mysql_hostname, $this->mysql_username, $this->mysql_password, $this->mysql_database);
            echo $this->mysql_connection->connect_error;

            if($this->mysql_connection->connect_error) {
                throw new Exception("Database connection failed.");
            }
        }

        function query($sql) {
            if(($result = $this->mysql_connection->query($sql)) == FALSE) {
                throw new Exception("Database operation failed.");
            } else {
                $index = 0;
                $rows = array();

                while($row = $result->fetch_assoc()) {
                    $rows[$index] = $row;
                    $index++;
                }

                return $rows;
            }
        }

        function __destruct() {
            $this->mysql_connection->close();
        }
    }
?>