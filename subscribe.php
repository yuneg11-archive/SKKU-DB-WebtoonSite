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
        $series_list = QUERY::series_list($db);
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
            echo headerSection(true, $user_id, $user_name, $unread_notification_count, "subscribe");
        ?>
        <main role="main">
            <section class="jumbotron">
                <div class="container">
                    <h2><?= ($user_name != "") ? "$user_name" : "$user_id" ?>'s subscription</h2>
                </div>
            </section>
            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <?php
                            // Prepare SQL Query
                            require "util/connection.php";

                            $sql_query_series_list = "SELECT SERIES.Series_id AS Series_id, SERIES.Title AS Title, Author, AVG(Value) as Average, MAX(Update_time) AS Update_time, SERIES.Cover_path AS Cover_path FROM SERIES LEFT JOIN EPISODE ON SERIES.Series_id = EPISODE.Series_id LEFT JOIN EVALUATION ON EPISODE.Series_id = EVALUATION.Series_id WHERE SERIES.Series_id IN (SELECT Series_id FROM SUBSCRIBE WHERE User_id = '$user_id') GROUP BY SERIES.Series_id";

                            // Connect to database
                            $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

                            if($database_connection->connect_error) {
                                die("Database connection failed.");
                            }

                            // Get series list
                            if(($result = $database_connection->query($sql_query_series_list)) == FALSE) {
                                die("Database operation failed.");
                            } else {
                                while($row = $result->fetch_assoc()) {
                                    $series_id = $row["Series_id"];
                                    $title = $row["Title"];
                                    $author = $row["Author"];
                                    $average = round($row["Average"] / 2);
                                    $cover_path = $row["Cover_path"];
                                    $update_time = explode(" ", $row["Update_time"])[0];

                                    echo "<a href='series.php?series_id=$series_id' class='col-md-4' style='text-decoration: none'>";
                                    echo     "<div class='card mb-4 shadow-sm'>";
                                    if($cover_path == "") {
                                        echo     "<svg class='card-img-top' width='100%' height='225' focusable='false'>";
                                        echo         "<title>Thumbnail</title>";
                                        echo         "<rect width='100%' height='100%' fill='#55595c'/>";
                                        echo         "<text x='50%' y='50%' fill='#eceeef' dy='.3em'>No Thumbnail</text>";
                                        echo     "</svg>";
                                    } else {
                                        echo     "<img src='content/$cover_path' alt='Thumbnail' class='card-img-top' width='100%' height='225'>";
                                    }
                                    echo         "<div class='card-body'>";
                                    echo             "<div class='d-flex justify-content-between align-items-center'>";
                                    echo                 "<p class='card-text series-title'>$title</p>";
                                    echo                 "<small class='text-muted series-author float-right'>$author</small>";
                                    echo             "</div>";
                                    echo             "<div class='d-flex justify-content-between align-items-center'>";
                                    echo                 "<div class='float-right'>";
                                    for($i = 1; $i <= 5; $i++) {
                                        if($i <= $average) {
                                            echo             "<span class='fa fa-star checked'></span>";
                                        } else {
                                            echo             "<span class='fa fa-star'></span>";
                                        }
                                    }
                                    echo                 "</div>";
                                    echo                 "<small class='text-muted'>$update_time</small>";
                                    echo             "</div>";
                                    echo         "</div>";
                                    echo     "</div>";
                                    echo "</a>";
                                }

                                if($result->num_rows == 0) {
                                    echo "<h5>No subscribed series</h5>";
                                }
                            }

                            // Close connection
                            $database_connection->close();
                        ?>
                    </div>
                </div>
            </div>
        </main>

        <script src="/js/jquery-slim.min.js"></script>
        <script src="/js/bootstrap.bundle.min.js"></script>
    </body>
</html>