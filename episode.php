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
    require "util/view.php";

    try {
        $db = new Database();
        $unread_notification_count = QUERY::unread_notification_count($db, $user_id);
        $episode_information = QUERY::episode_information($db, $series_id, $episode_id);
        $image_list = QUERY::image_list($db, $series_id, $episode_id);
        $evaluation_check = QUERY::evaluation_check($db, $user_id, $series_id, $episode_id);
        $bookmark_exist = QUERY::episode_bookmark_check($db, $series_id, $episode_id, $user_id);
        $comment_list = QUERY::comment_list($db, $series_id, $episode_id);
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
        <?php echo headerSection($user_signed, $user_id, $user_name, $unread_notification_count, "episode") ?>
        <main role="main">
            <?php echo episodeInformationSection($episode_information) ?>
            <div>
                <div class="container text-center">
                    <?php echo imageListRow($image_list) ?>
                    <p></p>
                    <div class="row">
                        <div class="col-3"></div>
                        <div class="col-3">
                            <?php echo evaluationForm($user_id, $series_id, $episode_id, $evaluation_check) ?>
                        </div>
                        <div class="col-3">
                            <?php
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
                    <?php echo commentFormRow($user_id, $series_id, $episode_id) ?>
                    <p></p>
                    <?php echo commentListRow($comment_list) ?>
                </div>
            </div>
        </main>

        <script src="/js/jquery-slim.min.js"></script>
        <script src="/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

