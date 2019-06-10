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
            echo headerSection($user_signed, $user_id, $user_name, $unread_notification_count, "index");
        ?>
        <main role="main">
            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <?php
                            foreach($series_list as $series) {
                                ?>
                                    <a href='series.php?series_id=<?=$series["Series_id"]?>' class='col-md-4' style='text-decoration: none'>
                                        <div class='card mb-4 shadow-sm'>
                                            <?php if($series["Cover_path"] == "") { ?>
                                                <svg class='card-img-top' width='100%' height='225' focusable='false'>
                                                    <title>Thumbnail</title>
                                                    <rect width='100%' height='100%' fill='#55595c'></rect>
                                                    <text x='50%' y='50%' fill='#eceeef' dy='.3em'>No Thumbnail</text>
                                                </svg>
                                            <?php } else { ?>
                                                <img src='content/<?=$series["Cover_path"]?>' alt='Thumbnail' class='card-img-top' width='100%' height='225'>
                                            <?php } ?>
                                            <div class='card-body'>
                                                <div class='d-flex justify-content-between align-items-center'>
                                                    <p class='card-text series-title'><?=$series["Title"]?></p>
                                                    <small class='text-muted series-author float-right'><?=$series["Author"]?></small>
                                                </div>
                                                <div class='d-flex justify-content-between align-items-center'>
                                                    <div class='float-right'>
                                                        <?php
                                                            for($i = 1; $i <= 5; $i++)
                                                                if ($i <= $series["Evaluation"]) {
                                                                    ?>
                                                                        <span class='fa fa-star checked'></span>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                        <span class='fa fa-star'></span>
                                                                    <?php }
                                                            ?>
                                                    </div>
                                                    <small class='text-muted'><?=$series["Update_date"]?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </main>

        <script src="/js/jquery-slim.min.js"></script>
        <script src="/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

