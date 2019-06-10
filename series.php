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

    $series_id = $_GET['series_id'];

    require "util/query.php";

    try {
        $db = new Database();
        $unread_notification_count = QUERY::unread_notification_count($db, $user_id);
        $series_list = QUERY::series_list($db);
    } catch (Exception $e) {
        die("Database operation failed.");
    }

    // Prepare SQL Query
    require "util/connection.php";

    $sql_query_series_information = "SELECT Title, Author, Synopsis, AVG(Value) AS Average, Cover_path FROM SERIES LEFT JOIN EVALUATION ON SERIES.Series_id = EVALUATION.Series_id WHERE SERIES.Series_id = $series_id";
    $sql_query_episode_list = "SELECT EPISODE.Series_id, EPISODE.Episode_id, Title, AVG(Value) AS Average, Cover_path, Update_time FROM EPISODE LEFT JOIN EVALUATION ON EPISODE.Series_id = EVALUATION.Series_id AND EPISODE.Episode_id = EVALUATION.Episode_id WHERE EPISODE.Series_id = $series_id GROUP BY EPISODE.Series_id, EPISODE.Episode_id";
    $sql_query_subscribe_exist = "SELECT User_id FROM SUBSCRIBE WHERE User_id = '$user_id' AND Series_id = '$series_id'";

    // Connect to database
    $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

    if ($database_connection->connect_error) {
        echo "<script>alert('Database connection failed.');history.back();</script>";
        exit;
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
            echo headerSection($user_signed, $user_id, $user_name, $unread_notification_count, "series");
        ?>
        <main role="main">
            <section id='jumbotron-series' class="jumbotron">
                <div class="container">
                    <?php
                        // Get series information
                        if(($result = $database_connection->query($sql_query_series_information)) == FALSE) {
                            echo "<script>alert('Database operation failed');history.back();</script>";
                            exit;
                        } else {
                            $row = $result->fetch_assoc();
                            $title = $row["Title"];
                            $author = $row["Author"];
                            $synopsis = $row["Synopsis"];
                            $average = round($row["Average"] / 2);
                            $cover_path = $row["Cover_path"];
                        }

                        // Get subscribe information
                        if(($result = $database_connection->query($sql_query_subscribe_exist)) == FALSE) {
                            echo "<script>alert('Database operation failed');history.back();</script>";
                            exit;
                        } else {
                            if($result->num_rows > 0) {
                                $subscribe_exist = true;
                            } else {
                                $subscribe_exist = false;
                            }
                        }

                        // Display series information
                        echo "<img id='series-cover' src='content/$cover_path' class='rounded float-left' alt='Cover image'>";
                        echo "<h1 class='jumbotron-heading'>$title</h1>";
                        echo "<p class='lead'>$synopsis</p>";
                        echo "<p class='container'>";
                        for($i = 1; $i <= 5; $i++) {
                            if($i <= $average) {
                                echo "<span class='fa fa-star checked'></span>";
                            } else {
                                echo "<span class='fa fa-star'></span>";
                            }
                        }
                        echo "</p>";
                        if($subscribe_exist) {
                            echo "<p><a href='util/subscribe.php?series_id=$series_id&user_id=$user_id' class='btn btn-secondary'>Unsubscribe</a></p>";
                        } else {
                            echo "<p><a href='util/subscribe.php?series_id=$series_id&user_id=$user_id' class='btn btn-primary'>Subscribe</a></p>";
                        }
                    ?>
                </div>
            </section>
            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <?php
                            // Get episode list
                            if(($result = $database_connection->query($sql_query_episode_list)) == FALSE) {
                                echo "<script>alert('Database operation failed');history.back();</script>";
                                exit;
                            } else {
                                while ($row = $result->fetch_assoc()) {
                                    $series_id = $row["Series_id"];
                                    $episode_id = $row["Episode_id"];
                                    $title = $row["Title"];
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
                                    echo             "<div class='d-flex justify-content-between align-items-center'>";
                                    echo                 "<p class='card-text episode-title'>$title</p>";
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

