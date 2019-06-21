<?php
    require "database.php";

    class Query {
        private static function evaluation($value) {
            return round($value / 2);
        }

        private static function update_date($time) {
            return explode(" ", $time)[0];
        }

        private static function convert_html_string($string) {
            return str_replace("\n", "<br>", $string);
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

        static function series_list($db, $query, $user_id, $mode) {
            $sql = "SELECT S.Series_id AS Series_id, S.Title AS Title, Author, AVG(Value) as Evaluation, MAX(Update_time) AS Update_time, S.Cover_path AS Cover_path
                    FROM SERIES S
                        LEFT JOIN EPISODE E ON S.Series_id = E.Series_id
                        LEFT JOIN EVALUATION EV ON E.Series_id = EV.Series_id ";
            if($mode == "search") {
                $sql .= "WHERE S.Title LIKE '%$query%' ";
            } else if($mode == "subscribe") {
                $sql .= "WHERE S.Series_id IN (SELECT Series_id FROM SUBSCRIBE WHERE User_id = '$user_id') ";
            }
            $sql .= "GROUP BY S.Series_id";

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

        static function series_information($db, $series_id) {
            $sql = "SELECT S.Series_id, Title, Author, Synopsis, AVG(Value) AS Evaluation, Cover_path
                    FROM SERIES S
                        LEFT JOIN EVALUATION EV ON S.Series_id = EV.Series_id
                    WHERE S.Series_id = $series_id";

            try {
                $rows = $db->query($sql);

                $result["Series_id"] = $rows[0]["Series_id"];
                $result["Title"] = $rows[0]["Title"];
                $result["Author"] = $rows[0]["Author"];
                $result["Synopsis"] = $rows[0]["Synopsis"];
                $result["Evaluation"] = QUERY::evaluation($rows[0]["Evaluation"]);
                $result["Cover_path"] = $rows[0]["Cover_path"];

                return $result;
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        static function episode_list($db, $series_id, $query, $user_id, $mode) {
            if($mode == "series") {
                $sql = "SELECT E.Series_id AS Series_id, E.Episode_id AS Episode_id, E.Title AS Episode_title, AVG(Value) AS Evaluation, Cover_path, Update_time
                        FROM EPISODE E
                            LEFT JOIN EVALUATION EV ON E.Series_id = EV.Series_id AND E.Episode_id = EV.Episode_id
                        WHERE E.Series_id = $series_id
                        GROUP BY E.Series_id, E.Episode_id";
            } else if($mode == "search" || $mode == "bookmark") {
                $sql = "SELECT E.Series_id AS Series_id, E.Episode_id AS Episode_id, E.Title AS Episode_title, S.Title AS Series_title, AVG(Value) AS Evaluation, E.Cover_path, Update_time
                        FROM SERIES S, EPISODE E
                            LEFT JOIN EVALUATION EV ON E.Series_id = EV.Series_id AND E.Episode_id = EV.Episode_id ";
                if($mode == "search") {
                    $sql .= "WHERE E.Series_id = S.Series_id AND E.Title LIKE '%$query%' ";
                } else if($mode == "bookmark") {
                    $sql .= "WHERE E.Series_id = S.Series_id AND E.Episode_id IN (SELECT Episode_id FROM BOOKMARK B
                                                                             WHERE E.Episode_id = B.Episode_id AND E.Series_id = B.Series_id AND User_id = '$user_id') ";
                }
                $sql .= "GROUP BY Series_id, Episode_id";
            }

            try {
                $rows = $db->query($sql);

                $result = array();
                $index = 0;

                foreach($rows as $row) {
                    $result[$index]["Series_id"] = $row["Series_id"];
                    if($mode == "search" || $mode == "bookmark") {
                        $result[$index]["Series_title"] = $row["Series_title"];
                    }
                    $result[$index]["Episode_id"] = $row["Episode_id"];
                    $result[$index]["Episode_title"] = $row["Episode_title"];
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

        static function series_subscribe_check($db, $series_id, $user_id) {
            if($user_id == "") {
                return false;
            } else {
                $sql = "SELECT User_id
                        FROM SUBSCRIBE
                        WHERE User_id = '$user_id' AND Series_id = '$series_id'";

                try {
                    $rows = $db->query($sql);

                    if(count($rows) > 0) {
                        return true;
                    } else {
                        return false;
                    }
                } catch (Exception $exception) {
                    throw $exception;
                }
            }
        }

        static function episode_information($db, $series_id, $episode_id) {
            $sql = "SELECT E.Series_id, Title, AVG(Value) AS Evaluation, Update_time
                    FROM EPISODE E
                        LEFT JOIN EVALUATION EV ON E.Series_id = EV.Series_id AND E.Episode_id = EV.Episode_id
                    WHERE E.Series_id = $series_id AND E.Episode_id = $episode_id";

            try {
                $rows = $db->query($sql);

                $result["Series_id"] = $rows[0]["Series_id"];
                $result["Title"] = $rows[0]["Title"];
                $result["Evaluation"] = QUERY::evaluation($rows[0]["Evaluation"]);
                $result["Update_date"] = QUERY::update_date($rows[0]["Update_time"]);

                return $result;
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        static function image_list($db, $series_id, $episode_id) {
            $sql = "SELECT Image_number, Image_path
                    FROM IMAGELIST
                    WHERE Series_id = $series_id AND Episode_id = $episode_id";

            try {
                $rows = $db->query($sql);

                $result = array();
                $index = 0;

                foreach($rows as $row) {
                    $result[$index]["Image_number"] = $row["Image_number"];
                    $result[$index]["Image_path"] = $row["Image_path"];

                    $index++;
                }

                return $result;
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        static function evaluation_check($db, $user_id, $series_id, $episode_id) {
            if($user_id == "") {
                return -1;
            } else {
                $sql = "SELECT Value
                    FROM EVALUATION
                    WHERE User_id = '$user_id' AND Series_id = $series_id AND Episode_id = $episode_id";

                try {
                    $rows = $db->query($sql);

                    if(count($rows) > 0) {
                        return $rows[0]["Value"];
                    } else {
                        return -1;
                    }
                } catch (Exception $exception) {
                    throw $exception;
                }
            }
        }

        static function episode_bookmark_check($db, $series_id, $episode_id, $user_id) {
            if($user_id == "") {
                return false;
            } else {
                $sql = "SELECT User_id
                        FROM BOOKMARK
                        WHERE User_id = '$user_id' AND Series_id = $series_id AND Episode_id = $episode_id";

                try {
                    $rows = $db->query($sql);

                    if(count($rows) > 0) {
                        return true;
                    } else {
                        return false;
                    }
                } catch (Exception $exception) {
                    throw $exception;
                }
            }
        }

        static function comment_list($db, $series_id, $episode_id) {
            $sql = "SELECT User_id, Content, Update_time
                    FROM COMMENT
                    WHERE Series_id = $series_id AND Episode_id = $episode_id";

            try {
                $rows = $db->query($sql);

                $result = array();
                $index = 0;

                foreach($rows as $row) {
                    $result[$index]["User_id"] = $row["User_id"];
                    $result[$index]["Content"] = QUERY::convert_html_string($row["Content"]);
                    $result[$index]["Update_time"] = $row["Update_time"];

                    $index++;
                }

                return $result;
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        static function notification_list($db, $user_id) {
            $sql = "SELECT Notification_id, Series_id, Episode_id, Message, Update_time, Notified
                    FROM NOTIFICATION
                    WHERE User_id = '$user_id'";

            try {
                $rows = $db->query($sql);

                $result = array();
                $index = 0;

                foreach($rows as $row) {
                    if($row["Series_id"] != "" && $row["Episode_id"] != "") {
                        $series_id = $row["Series_id"];
                        $episode_id = $row["Episode_id"];
                        $sql_sub = "SELECT E.Title AS Episode_title, S.Title AS Series_title
                                    FROM SERIES S, EPISODE E
                                    WHERE E.Series_id = S.Series_id AND E.Series_id = $series_id AND E.Episode_id = $episode_id";

                        $rows_sub = $db->query($sql_sub);

                        $row["Message"] = str_replace("&series", "'".$rows_sub[0]["Series_title"]."'", $row["Message"]);
                        $row["Message"] = str_replace("&episode", "'".$rows_sub[0]["Episode_title"]."'", $row["Message"]);
                    }

                    $result[$index]["Notification_id"] = $row["Notification_id"];
                    $result[$index]["Series_id"] = $row["Series_id"];
                    $result[$index]["Episode_id"] = $row["Episode_id"];
                    $result[$index]["Message"] = $row["Message"];
                    $result[$index]["Update_time"] = $row["Update_time"];
                    $result[$index]["Notified"] = $row["Notified"];

                    $index++;
                }

                return $result;
            } catch (Exception $exception) {
                throw $exception;
            }
        }
    }
?>