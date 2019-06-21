<?php
    session_start();
    if(!isset($_SESSION['User_id'])) {
        // Not signed in
        $user_signed = false;
        $user_id = "";
        $user_name = "";
    } else {
        // Signed in
        $user_signed = true;
        $user_id = $_SESSION['User_id'];
        $user_name = $_SESSION['User_name'];
    }

    $series_id = $_GET['series_id'];

    require "util/query.php";
    require "util/view.php";

    try {
        $db = new Database();
        $unread_notification_count = QUERY::unread_notification_count($db, $user_id);
        $series_information = QUERY::series_information($db, $series_id);
        $episode_list = QUERY::episode_list($db, $series_id, "", "", "series");
        $subscribe_exist = QUERY::series_subscribe_check($db, $series_id, $user_id);
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
        <?php echo headerSection($user_signed, $user_id, $user_name, $unread_notification_count, "series") ?>
        <main role="main">
            <?php echo seriesInformationSection($series_information, $subscribe_exist, $user_id) ?>
            <?php echo episodeListSection($episode_list, "No episode", "series") ?>
        </main>

        <script src="/js/jquery-slim.min.js"></script>
        <script src="/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

