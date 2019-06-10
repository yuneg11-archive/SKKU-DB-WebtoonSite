<?php
    session_start();
    if(!isset($_SESSION['User_id'])) {
        // Not signed in
        $user_signed = false;
    } else {
        // Signed in
        $user_signed = true;
        $user_id = $_SESSION['User_id'];
        $user_name = $_SESSION['User_name'];
    }

    $query = $_POST["Query"];

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
            echo headerSection($user_signed, $user_id, $user_name, $unread_notification_count, "search");
        ?>
        <main role="main">
            <section class="jumbotron">
                <div class="container">
                    <h2>Series search result for '<?= $query ?>'</h2>
                </div>
            </section>
            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <?php
                            // Prepare SQL Query
                            require "util/connection.php";

                            $sql_query_series_list = "SELECT SERIES.Series_id AS Series_id, SERIES.Title AS Title, Author, AVG(Value) as Average, MAX(Update_time) AS Update_time, SERIES.Cover_path AS Cover_path FROM SERIES LEFT JOIN EPISODE ON SERIES.Series_id = EPISODE.Series_id LEFT JOIN EVALUATION ON EPISODE.Series_id = EVALUATION.Series_id WHERE SERIES.Title LIKE '%$query%'  GROUP BY SERIES.Series_id";

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
                                    echo "<h5>No series</h5>";
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
            <section class="jumbotron">
                <div class="container">
                    <h2>Episode search result for '<?= $query ?>'</h2>
                </div>
            </section>
            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <?php
                            // Prepare SQL Query
                            $sql_query_episode_list = "SELECT EPISODE.Series_id, EPISODE.Episode_id, EPISODE.Title AS Episode_title, SERIES.Title AS Series_title, AVG(Value) AS Average, EPISODE.Cover_path, Update_time FROM SERIES, EPISODE LEFT JOIN EVALUATION ON EPISODE.Series_id = EVALUATION.Series_id AND EPISODE.Episode_id = EVALUATION.Episode_id WHERE EPISODE.Series_id = SERIES.Series_id AND EPISODE.Title LIKE '%$query%' GROUP BY Series_id, Episode_id";

                            // Get episode list
                            if(($result = $database_connection->query($sql_query_episode_list)) == FALSE) {
                                echo "<script>alert('Database operation failed');history.back();</script>";
                                exit;
                            } else {
                                while ($row = $result->fetch_assoc()) {
                                    $series_id = $row["Series_id"];
                                    $episode_id = $row["Episode_id"];
                                    $episode_title = $row["Episode_title"];
                                    $series_title = $row["Series_title"];
                                    $cover_path = $row["Cover_path"];
                                    $average = round($row['Average'] / 2);
                                    $update_time = explode(" ", $row["Update_time"])[0];

                                    echo "<a href='episode.php?series_id=$series_id&episode_id=$episode_id' class='col-md-4' style='text-decoration: none'>";
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
                                    echo             "<small class='text-muted'>$series_title</small>";
                                    echo             "<div class='d-flex justify-content-between align-items-center'>";
                                    echo                 "<p class='card-text episode-title'>$episode_title</p>";
                                    echo                 "<small class='text-muted float-right'></small>";
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
                                    echo "<h5>No episodes</h5>";
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