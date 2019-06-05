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
                $user_signed = false;
            } else {
                // Signed in
                $user_signed = true;
                $user_id = $_SESSION['User_id'];
                $user_name = $_SESSION['User_name'];
            }

            $series_id = $_GET['series_id'];
            $episode_id = $_GET['episode_id'];

            // Prepare SQL Query
            require "util/connection.php";

            $sql_query_episode_information = "SELECT Title, Update_time FROM EPISODE WHERE Series_id = $series_id AND Episode_id = $episode_id";
            $sql_query_image_list = "SELECT Image_number, Image_path FROM IMAGELIST WHERE Series_id = $series_id AND Episode_id = $episode_id";
            $sql_query_image_list = "SELECT Image_number, Image_path FROM IMAGELIST WHERE Series_id = $series_id AND Episode_id = $episode_id";

            // Connect to database
            $database_connection = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

            if ($database_connection->connect_error) {
                echo "<script>alert('Database connection failed.');history.back();</script>";
                exit;
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
                            if($user_signed) {
                                // Signed in
                                echo "<li class='nav-item active'><a class='nav-link' href='#'>";
                                echo ($user_name != "") ? "$user_name" : "$user_id";
                                echo "</a></li>";
                                if($user_id === 'admin') {
                                    // Admin signed in
                                    echo "<li class='nav-item'><a class='nav-link' href='register_series.html'>Register Series</a></li>";
                                    echo "<li class='nav-item'><a class='nav-link' href='register_episode.php'>Register Episode</a></li>";
                                }
                                echo "<li class='nav-item'><a class='nav-link' href='/util/user_signout.php'>Sign out</a></li>";
                            } else {
                                // Not signed in
                                echo "<li class='nav-item'><a class='nav-link' href='signin.html'>Sign in</a></li>";
                                echo "<li class='nav-item'><a class='nav-link' href='signup.html'>Sign up</a></li>";
                            }
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
                            $update_time = explode(" ", $row["Update_time"])[0];
                        }

                        // Display series information
                        echo "<p><a class='btn btn-secondary btn-sm' href='series.php?series_id=$series_id' role='button'>< Back</a></p>";
                        echo "<h2>$title</h2>";
                        echo "<div class='float-left'>$update_time</div>";
                        echo "<div class='float-right'>";
                        echo     "<span class='fa fa-star checked'></span>";
                        echo     "<span class='fa fa-star checked'></span>";
                        echo     "<span class='fa fa-star checked'></span>";
                        echo     "<span class='fa fa-star'></span>";
                        echo     "<span class='fa fa-star'></span>";
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

                            // Close connection
                            $database_connection->close();
                        ?>
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
                                <th scope="col">#</th>
                                <th scope="col">First</th>
                                <th scope="col">Handle</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td>Mark</td>
                                <td>@mdo</td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td>Jacob</td>
                                <td>@fat</td>
                            </tr>
                            <tr>
                                <th scope="row">3</th>
                                <td>Larry</td>
                                <td>@twitter</td>
                            </tr>
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

