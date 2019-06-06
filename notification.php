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
                            echo "<li class='nav-item'><a class='nav-link' href='bookmark.php'>Bookmarks</a></li>";
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
                    <h2><?= ($user_name != "") ? "$user_name" : "$user_id" ?>'s notifications</h2>
                </div>
            </section>
            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <table class="table table-striped">
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

                                    $sql_query_notification_list = "SELECT Series_id, Episode_id, Message, Update_time, Notified FROM NOTIFICATION WHERE User_id = '$user_id'";

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
                                            $series_id = $row["Series_id"];
                                            $episode_id = $row["Episode_id"];
                                            $message = $row["Message"];
                                            $update_time = explode(" ", $row["Update_time"])[0];
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

                                            echo "<tr>";
                                            echo "<td>$update_time</td>";
                                            echo "<td><a href='util/notification.php?user_id=$user_id&series_id=$series_id&episode_id=$episode_id'>$message</a></td>";
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