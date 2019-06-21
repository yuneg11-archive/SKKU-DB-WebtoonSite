<?php
    require "util/query.php";
    require "util/view.php";

    try {
        $db = new Database();
        $series_list = QUERY::series_list($db, "", "", "register");
    } catch (Exception $e) {
        die("Database operation failed.");
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Register Episode</title>

        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <link href="/css/form.css" rel="stylesheet">
    </head>
    <body class="text-center">
        <form class="form-register form-register-episode" method='post' action='util/register_episode.php'>
            <h1 class="h3 mb-3 font-weight-normal">Episode information</h1>

            <label for="inputTitle" class="sr-only">Title</label>
            <input type="text" id="inputTitle" name="Title" class="form-control" placeholder="Title" required autofocus>

            <select class="form-control custom-select" id="inputSeriesID" name="Series_id" required>
                <option value=''>Series...</option>
                <?php
                    foreach($series_list as $series) {
                        $series_id = $series["Series_id"];
                        $series_title = $series["Title"];
                        echo "<option value='$series_id'>$series_title</option>";
                    }
                ?>
            </select>

            <label for="inputCoverPath" class="sr-only">Cover path</label>
            <input type="text" id="inputCoverPath" name="Cover_path" class="form-control" placeholder="Cover path">

            <label for="inputContentPath" class="sr-only">Content directory path</label>
            <input type="text" id="inputContentPath" name="Content_path" class="form-control" placeholder="Content directory path" required>

            <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
        </form>
    </body>
</html>