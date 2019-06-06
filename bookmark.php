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
        ?>
        <header>
            <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
                <a class="navbar-brand" href="index.php">Webtoon</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                        aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav mr-auto">
                        <?php
                            // Signed in
                            echo "<li class='nav-item active'><a class='nav-link' href='notification.php'>";
                            echo ($user_name != "") ? "$user_name" : "$user_id";
                            echo "</a></li>";
                            if($user_id === 'admin') {
                                // Admin signed in
                                echo "<li class='nav-item'><a class='nav-link' href='register_series.html'>Register Series</a></li>";
                                echo "<li class='nav-item'><a class='nav-link' href='register_episode.php'>Register Episode</a></li>";
                            }
                            echo "<li class='nav-item'><a class='nav-link' href='subscribe.php'>Subscribes</a></li>";
                            echo "<li class='nav-item active'><a class='nav-link' href='bookmark.php'>Bookmarks</a></li>";
                            echo "<li class='nav-item'><a class='nav-link' href='/util/user_signout.php'>Sign out</a></li>";
                        ?>
                    </ul>
                    <form class="form-inline mt-2 mt-md-0">
                        <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
                        <button class="btn my-2 my-sm-0" type="submit"><img src="/img/search.png" alt="Search"></button>
                    </form>
                </div>
            </nav>
        </header>
        <main role="main">
            <section class="jumbotron">
                <div class="container">
                    <h2><?= ($user_name != "") ? "$user_name" : "$user_id" ?>'s bookmarks</h2>
                </div>
            </section>
            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <?php
                            // Prepare SQL Query
                            require "util/connection.php";

                            $sql_query_episode_list = "SELECT EPISODE.Series_id, EPISODE.Episode_id, EPISODE.Title AS Episode_title, SERIES.Title AS Series_title, AVG(Value) AS Average, EPISODE.Cover_path, Update_time FROM SERIES, EPISODE LEFT JOIN EVALUATION ON EPISODE.Series_id = EVALUATION.Series_id AND EPISODE.Episode_id = EVALUATION.Episode_id WHERE EPISODE.Series_id = SERIES.Series_id AND EPISODE.Episode_id IN (SELECT Episode_id FROM BOOKMARK WHERE EPISODE.Episode_id = BOOKMARK.Episode_id AND EPISODE.Series_id = BOOKMARK.Series_id AND User_id = '$user_id') GROUP BY Series_id, Episode_id";

                            // Connect to database
                            $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

                            if($database_connection->connect_error) {
                                die("Database connection failed.");
                            }

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
                                    echo "<h5>No bookmarks</h5>";
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
