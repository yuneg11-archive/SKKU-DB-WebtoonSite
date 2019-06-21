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
    require "util/view.php";

    try {
        $db = new Database();
        $unread_notification_count = QUERY::unread_notification_count($db, $user_id);
        $series_subscribe_list = QUERY::series_list($db, "", $user_id, "subscribe");
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
        <?php echo headerSection(true, $user_id, $user_name, $unread_notification_count, "subscribe") ?>
        <main role="main">
            <section class="jumbotron">
                <div class="container">
                    <h2><?= ($user_name != "") ? "$user_name" : "$user_id" ?>'s subscription</h2>
                </div>
            </section>
            <?php echo seriesListSection($series_subscribe_list, "No subscribed series") ?>
        </main>

        <script src="/js/jquery-slim.min.js"></script>
        <script src="/js/bootstrap.bundle.min.js"></script>
    </body>
</html>