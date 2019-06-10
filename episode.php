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
    $episode_id = $_GET['episode_id'];

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

    $sql_query_episode_information = "SELECT Title, AVG(Value) AS Average, Update_time FROM EPISODE LEFT JOIN EVALUATION ON EPISODE.Series_id = EVALUATION.Series_id AND EPISODE.Episode_id = EVALUATION.Episode_id WHERE EPISODE.Series_id = $series_id AND EPISODE.Episode_id = $episode_id";
    $sql_query_image_list = "SELECT Image_number, Image_path FROM IMAGELIST WHERE Series_id = $series_id AND Episode_id = $episode_id";
    $sql_query_comment_list = "SELECT User_id, Content, Update_time FROM COMMENT WHERE Series_id = $series_id AND Episode_id = $episode_id";
    $sql_query_bookmark_exist = "SELECT User_id FROM BOOKMARK WHERE User_id = '$user_id' AND Series_id = $series_id AND Episode_id = $episode_id";
    $sql_query_evaluation_exist = "SELECT Value FROM EVALUATION WHERE User_id = '$user_id' AND Series_id = $series_id AND Episode_id = $episode_id";

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
            echo headerSection($user_signed, $user_id, $user_name, $unread_notification_count, "episode");
        ?>
        <main role="main">
            <section id='jumbotron-episode' class="jumbotron">
                <div class="container">
                    <?php
                        // Get episode information
                        if(($result = $database_connection->query($sql_query_episode_information)) == FALSE) {
                            echo "<script>alert('Database operation failed');history.back();</script>";
                            exit;
                        } else {
                            $row = $result->fetch_assoc();
                            $title = $row["Title"];
                            $average = round($row["Average"] / 2);
                            $update_time = explode(" ", $row["Update_time"])[0];
                        }

                        // Display series information
                        echo "<p><a class='btn btn-secondary btn-sm' href='series.php?series_id=$series_id' role='button'>< Back</a></p>";
                        echo "<h2>$title</h2>";
                        echo "<div class='float-left'>$update_time</div>";
                        echo "<div class='float-right'>";
                        for($i = 1; $i <= 5; $i++) {
                            if($i <= $average) {
                                echo "<span class='fa fa-star checked'></span>";
                            } else {
                                echo "<span class='fa fa-star'></span>";
                            }
                        }
                        echo "</div>";
                    ?>
                </div>
            </section>
            <div>
                <div class="container text-center">
                    <div class="row">
                        <?php
                            // Get series list
                            if(($result = $database_connection->query($sql_query_image_list)) == FALSE) {
                                echo "<script>alert('Database operation failed');history.back();</script>";
                                exit;
                            } else {
                                while ($row = $result->fetch_assoc()) {
                                    $image_number = $row["Image_number"];
                                    $image_path = $row["Image_path"];

                                    echo "<img src='content/$image_path' alt='Image $image_number' class='' width='100%' height='100%'>";
                                }
                            }
                        ?>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-3"></div>
                        <div class="col-3">
                            <form class="form-inline" method='post' action='util/evaluation.php'>
                                <div class="form-group">
                                    <input type='hidden' name='User_id' value='<?= $user_id?>' />
                                    <input type='hidden' name='Series_id' value='<?= $series_id?>' />
                                    <input type='hidden' name='Episode_id' value='<?= $episode_id?>' />
                                    <select class="form-control custom-select" id="inputSeriesID" name="Value" required>
                                        <option value=''>Episode score...</option>
                                        <?php
                                            if(($result = $database_connection->query($sql_query_evaluation_exist)) == FALSE) {
                                                echo "<script>alert('Database operation failed');history.back();</script>";
                                                exit;
                                            } else {
                                                if($result->num_rows > 0) {
                                                    $row = $result->fetch_assoc();
                                                    $value = $row['Value'];
                                                }
                                            }

                                            for($i = 1; $i <= 10; $i++) {
                                                if($i == $value) {
                                                    echo "<option value='$i' selected='selected'>$i</option>";
                                                } else {
                                                    echo "<option value='$i'>$i</option>";
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <button class="btn btn-secondary" type="submit">Evaluate</button>
                            </form>
                        </div>
                        <div class="col-3">
                            <?php
                                // Get subscribe information
                                if(($result = $database_connection->query($sql_query_bookmark_exist)) == FALSE) {
                                    echo "<script>alert('Database operation failed');history.back();</script>";
                                    exit;
                                } else {
                                    if($result->num_rows > 0) {
                                        $bookmark_exist = true;
                                    } else {
                                        $bookmark_exist = false;
                                    }
                                }

                                if($bookmark_exist) {
                                    echo "<a href='util/bookmark.php?series_id=$series_id&episode_id=$episode_id&user_id=$user_id' class='btn btn-secondary'>Unbookmark</a>";
                                } else {
                                    echo "<a href='util/bookmark.php?series_id=$series_id&episode_id=$episode_id&user_id=$user_id' class='btn btn-primary'>Bookmark</a>";
                                }
                            ?>
                        </div>
                        <div class="col-3"></div>
                    </div>
                    <p></p>
                    <form class="row form-comment" method='post' action='util/comment.php'>
                        <input type='hidden' name='User_id' value='<?= $user_id?>' />
                        <input type='hidden' name='Series_id' value='<?= $series_id?>' />
                        <input type='hidden' name='Episode_id' value='<?= $episode_id?>' />
                        <div class="col-9">
                            <label for="inputComment" class="sr-only">Enter comment here...</label>
                            <textarea id="inputComment" name="Content" class="form-control" placeholder="Enter comment..." rows="3"></textarea>
                        </div>
                        <div class="col-3 text-center">
                            <button class="btn btn-lg btn-secondary btn-block" type="submit">Comment</button>
                        </div>
                    </form>
                    <p></p>
                    <div class="row">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">User</th>
                                    <th scope="col">Comment</th>
                                    <th scope="col">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Get comment list
                                    if(($result = $database_connection->query($sql_query_comment_list)) == FALSE) {
                                        echo "<script>alert('Database operation failed');history.back();</script>";
                                        exit;
                                    } else {
                                        while ($row = $result->fetch_assoc()) {
                                            $comment_user_id = $row["User_id"];
                                            $comment_content = str_replace("\n", "<br>", $row["Content"]);
                                            $comment_update_time = $row["Update_time"];

                                            echo "<tr>";
                                            echo "<th scope='row'>$comment_user_id</th>";
                                            echo "<td>$comment_content</td>";
                                            echo "<td>$comment_update_time</td>";
                                            echo "</tr>";
                                        }

                                        if($result->num_rows == 0) {
                                            echo "<tr><td colspan='3'>No comments</td></tr>";
                                        }
                                    }
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

