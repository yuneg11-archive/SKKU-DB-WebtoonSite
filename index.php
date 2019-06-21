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

    require "util/query.php";
    require "util/view.php";

    try {
        $db = new Database();
        $unread_notification_count = QUERY::unread_notification_count($db, $user_id);
        $series_list = QUERY::series_list($db, "", "", "index");
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
        <?php echo headerSection($user_signed, $user_id, $user_name, $unread_notification_count, "index"); ?>
        <main role="main">
            <?php echo seriesListSection($series_list, "No series"); ?>
        </main>

        <script src="/js/jquery-slim.min.js"></script>
        <script src="/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

