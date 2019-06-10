<?php
    session_start();
    if(!isset($_SESSION['User_id'])) {
        // Not signed in
        echo "<script>alert('Please sign in.');history.back();</script>";
        exit;
    } else {
        // Signed in
        $user_id = $_SESSION['User_id'];
        $user_name = $_SESSION['User_name'];
    }

    require "util/query.php";

    try {
        $db = new Database();
        $unread_notification_count = QUERY::unread_notification_count($db, $user_id);
    } catch (Exception $e) {
        die("Database operation failed.");
    }
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Webtoon</title>

        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <link href="/css/font-awesome.min.css" rel="stylesheet">
        <link href="/css/album.css" rel="stylesheet">
    </head>
    <body>
        <?php
            require "util/header.php";
            echo headerSection(true, $user_id, $user_name, $unread_notification_count, "notification");
        ?>
        <main role="main">
            <section class="jumbotron">
                <div class="container">
                    <h2><?= ($user_name != "") ? "$user_name" : "$user_id" ?>'s notifications</h2>
                </div>
            </section>
            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">Time</th>
                                <th scope="col">Message</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Prepare SQL Query
                                    require "util/connection.php";

                                    $sql_query_notification_list = "SELECT Notification_id, Series_id, Episode_id, Message, Update_time, Notified FROM NOTIFICATION WHERE User_id = '$user_id'";

                                    // Connect to database
                                    $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

                                    if($database_connection->connect_error) {
                                        die("Database connection failed.");
                                    }

                                    // Get notification list
                                    if(($result = $database_connection->query($sql_query_notification_list)) == FALSE) {
                                        echo "<script>alert('Database operation failed');history.back();</script>";
                                        exit;
                                    } else {
                                        while ($row = $result->fetch_assoc()) {
                                            $notification_id = $row["Notification_id"];
                                            $series_id = $row["Series_id"];
                                            $episode_id = $row["Episode_id"];
                                            $message = $row["Message"];
                                            $update_time = $row["Update_time"];
                                            $notified = $row["Notified"];

                                            if(trim($series_id) != "" && trim($episode_id) != "") {
                                                $sql_query_title = "SELECT EPISODE.Title AS Episode_title, SERIES.Title AS Series_title FROM SERIES, EPISODE WHERE EPISODE.Series_id = SERIES.Series_id AND EPISODE.Series_id = $series_id AND EPISODE.Episode_id = $episode_id";
                                                if(($title_result = $database_connection->query($sql_query_title)) == FALSE) {
                                                    echo "<script>alert('Database operation failed');history.back();</script>";
                                                    exit;
                                                }

                                                $titles = $title_result->fetch_assoc();
                                                $message = str_replace("&series", "'".$titles["Series_title"]."'", $message);
                                                $message = str_replace("&episode", "'".$titles["Episode_title"]."'", $message);
                                            }

                                            if(trim($notified) == "") {
                                                echo "<tr>";
                                            } else {
                                                echo "<tr class='table-active'>";
                                            }
                                            echo "<td>$update_time</td>";
                                            echo "<td><a href='util/notification.php?user_id=$user_id&notification_id=$notification_id&series_id=$series_id&episode_id=$episode_id'>$message</a></td>";
                                            echo "</tr>";
                                        }

                                        if($result->num_rows == 0) {
                                            echo "<h5>No notifications</h5>";
                                        }
                                    }

                                    // Close connection
                                    $database_connection->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <script src="/js/jquery-slim.min.js"></script>
        <script src="/js/bootstrap.bundle.min.js"></script>
    </body>
</html>