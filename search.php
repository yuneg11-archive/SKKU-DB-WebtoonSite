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

    $query = $_POST["Query"];

    require "util/query.php";
    require "util/view.php";

    try {
        $db = new Database();
        $unread_notification_count = QUERY::unread_notification_count($db, $user_id);
        $episode_search_list = QUERY::episode_list($db, "", $query, "", "search");
        $series_search_list = QUERY::series_list($db, $query, "", "search");
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
        <?php echo headerSection($user_signed, $user_id, $user_name, $unread_notification_count, "search") ?>
        <main role="main">
            <section class="jumbotron">
                <div class="container">
                    <h2>Series search result for '<?= $query ?>'</h2>
                </div>
            </section>
            <?php echo seriesListSection($series_search_list, "No series") ?>
            <section class="jumbotron">
                <div class="container">
                    <h2>Episode search result for '<?= $query ?>'</h2>
                </div>
            </section>
            <?php echo episodeListSection($episode_search_list, "No episode", "search") ?>
        </main>

        <script src="/js/jquery-slim.min.js"></script>
        <script src="/js/bootstrap.bundle.min.js"></script>
    </body>
</html>