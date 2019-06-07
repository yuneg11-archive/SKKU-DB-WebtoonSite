<?php
    require "database.php";

    class Query {
        private static function evaluation($value) {
            return round($value / 2);
        }

        private static function update_date($time) {
            return explode(" ", $time)[0];
        }

        static function unread_notification_count($db, $user_id) {
            $sql = "SELECT COUNT(Notification_id) AS Count
                    FROM NOTIFICATION
                    WHERE User_id = '$user_id' AND Notified IS NULL";

            try {
                return $db->query($sql)[0]["Count"];
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        static function series_list($db) {
            $sql = "SELECT S.Series_id AS Series_id, S.Title AS Title, Author, AVG(Value) as Evaluation, MAX(Update_time) AS Update_time, S.Cover_path AS Cover_path
                    FROM SERIES S
                        LEFT JOIN EPISODE E ON S.Series_id = E.Series_id
                        LEFT JOIN EVALUATION EV ON E.Series_id = EV.Series_id
                    GROUP BY S.Series_id";

            try {
                $rows = $db->query($sql);

                $result = array();
                $index = 0;

                foreach($rows as $row) {
                    $result[$index]["Series_id"] = $row["Series_id"];
                    $result[$index]["Title"] = $row["Title"];
                    $result[$index]["Author"] = $row["Author"];
                    $result[$index]["Evaluation"] = QUERY::evaluation($row["Evaluation"]);
                    $result[$index]["Cover_path"] = $row["Cover_path"];
                    $result[$index]["Update_date"] = QUERY::update_date($row["Update_time"]);

                    $index++;
                }

                return $result;
            } catch (Exception $exception) {
                throw $exception;
            }
        }
    }
?>