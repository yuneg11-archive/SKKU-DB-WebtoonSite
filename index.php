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
        <header>
            <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
                <a class="navbar-brand" href="#">Webtoon</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                        aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav mr-auto">
                        <?php if($user_signed) { ?>
                            <li class='nav-item active'>
                                <a class='nav-link' href='notification.php'>
                                    <?= ($user_name != "") ? $user_name : $user_id ?>
                                    <?= ($unread_notification_count > 0) ? " (".$unread_notification_count.")" : "" ?>
                                </a>
                            </li>
                                <?php if($user_id === 'admin') { ?>
                                    <li class='nav-item'><a class='nav-link' href='register_series.html'>Register Series</a></li>
                                    <li class='nav-item'><a class='nav-link' href='register_episode.php'>Register Episode</a></li>
                                <?php } ?>
                                <li class='nav-item'><a class='nav-link' href='subscribe.php'>Subscribes</a></li>
                                <li class='nav-item'><a class='nav-link' href='bookmark.php'>Bookmarks</a></li>
                                <li class='nav-item'><a class='nav-link' href='/util/user_signout.php'>Sign out</a></li>
                            <?php } else { ?>
                                <li class='nav-item'><a class='nav-link' href='signin.html'>Sign in</a></li>
                                <li class='nav-item'><a class='nav-link' href='signup.html'>Sign up</a></li>
                        <?php } ?>
                    </ul>
                    <form class="form-inline" method="post" action="search.php">
                        <input class="form-control" type="text" placeholder="Search" aria-label="Search" name="Query">
                        <button class="btn" type="submit"><img src="img/search.png" alt="Search"></button>
                    </form>
                </div>
            </nav>
        </header>
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

