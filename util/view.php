<?php
    function headerSection($user_signed, $user_id, $user_name, $unread_notification_count, $current_page) {
        $html = "<header>";
            $html .= "<nav class='navbar navbar-expand-md navbar-dark fixed-top bg-dark'>";
                $html .= "<a class='navbar-brand' href='/index.php'>Webtoon</a>";
                $html .= "<button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarCollapse'
                                  aria-controls='navbarCollapse' aria-expanded='false' aria-label='Toggle navigation'>";
                    $html .= "<span class='navbar-toggler-icon'></span>";
                $html .= "</button>";
                $html .= "<div class='collapse navbar-collapse' id='navbarCollapse'>";
                    $html .= "<ul class='navbar-nav mr-auto'>";
                    if($user_signed) {
                        if($current_page == "notification") {
                            $html .= "<li class='nav-item active'>";
                        } else {
                            $html .= "<li class='nav-item'>";
                        }
                            $html .= "<a class='nav-link' href='notification.php'>";
                            $html .= ($user_name != "") ? $user_name : $user_id;
                            $html .= ($unread_notification_count > 0) ? " (".$unread_notification_count.")" : "";
                            $html .= "</a>";
                        $html .= "</li>";
                        if($user_id === "admin") {
                            $html .= "<li class='nav-item'><a class='nav-link' href='/register_series.html'>Register Series</a></li>";
                            $html .= "<li class='nav-item'><a class='nav-link' href='/register_episode.php'>Register Episode</a></li>";
                        }
                        if($current_page == "subscribe") {
                            $html .= "<li class='nav-item active'><a class='nav-link' href='/subscribe.php'>Subscribes</a></li>";
                        } else {
                            $html .= "<li class='nav-item'><a class='nav-link' href='/subscribe.php'>Subscribes</a></li>";
                        }
                        if($current_page == "bookmark") {
                            $html .= "<li class='nav-item active'><a class='nav-link' href='/bookmark.php'>Bookmarks</a></li>";
                        } else {
                            $html .= "<li class='nav-item'><a class='nav-link' href='/bookmark.php'>Bookmarks</a></li>";
                        }
                        $html .= "<li class='nav-item'><a class='nav-link' href='/util/user_signout.php'>Sign out</a></li>";
                    } else {
                        $html .= "<li class='nav-item'><a class='nav-link' href='/signin.html'>Sign in</a></li>";
                        $html .= "<li class='nav-item'><a class='nav-link' href='/signup.html'>Sign up</a></li>";
                    }

                    $html .= "</ul>";
                    $html .= "<form class='form-inline' method='post' action='/search.php'>";
                        $html .= "<input class='form-control' type='text' placeholder='Search' aria-label='Search' name='Query'>";
                        $html .= "<button class='btn' type='submit'><img src='/img/search.png' alt='Search'></button>";
                    $html .= "</form>";
                $html .= "</div>";
            $html .= "</nav>";
        $html .= "</header>";
    
        return $html;
    }

    function seriesListSection($series_list, $empty_placeholder) {
        $html = "<div class='album py-5 bg-light'>";
            $html .= "<div class='container'>";
                $html .= "<div class='row'>";
                if(count($series_list) > 0) {
                    foreach($series_list as $series) {
                        $series_id = $series["Series_id"];
                        $series_title = $series["Title"];
                        $series_author = $series["Author"];
                        $series_cover_path = $series["Cover_path"];
                        $series_evaluation = $series["Evaluation"];
                        $series_update_date = $series["Update_date"];
                        $html .= "<a href='series.php?series_id=$series_id' class='col-md-4' style='text-decoration: none'>";
                            $html .= "<div class='card mb-4 shadow-sm'>";
                                if($series_cover_path == "") {
                                    $html .= "<svg class='card-img-top' width='100%' height='225' focusable='false'>";
                                        $html .= "<title>Thumbnail</title>";
                                        $html .= "<rect width='100%' height='100%' fill='#55595c'></rect>";
                                        $html .= "<text x='50%' y='50%' fill='#eceeef' dy='.3em'>No Thumbnail</text>";
                                    $html .= "</svg>";
                                } else {
                                    $html .= "<img src='content/$series_cover_path' alt='Thumbnail' class='card-img-top' width='100%' height='225'>";
                                }
                                $html .= "<div class='card-body'>";
                                    $html .= "<div class='d-flex justify-content-between align-items-center'>";
                                    $html .= "<p class='card-text series-title'>$series_title</p>";
                                    $html .= "<small class='text-muted series-author float-right'>$series_author</small>";
                                $html .= "</div>";
                                $html .= "<div class='d-flex justify-content-between align-items-center'>";
                                    $html .= "<div class='float-right'>";
                                        for($i = 1; $i <= 5; $i++) {
                                            if($i <= $series_evaluation) {
                                                $html .= "<span class='fa fa-star checked'></span>";
                                            } else {
                                                $html .= "<span class='fa fa-star'></span>";
                                            }
                                        }
                                        $html .= "</div>";
                                        $html .= "<small class='text-muted'>$series_update_date</small>";
                                    $html .= "</div>";
                                $html .= "</div>";
                            $html .= "</div>";
                        $html .= "</a>";
                    }
                } else {
                    $html .= "<h5>$empty_placeholder</h5>";
                }
                $html .= "</div>";
            $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    function seriesInformationSection($series_information, $subscribe_exist, $user_id) {
        $series_id = $series_information["Series_id"];
        $series_title = $series_information["Title"];
        $series_synopsis = $series_information["Synopsis"];
        $series_cover_path = $series_information["Cover_path"];
        $series_evaluation = $series_information["Evaluation"];

        $html = "<section id='jumbotron-series' class='jumbotron'>";
            $html .= "<div class='container'>";
                $html .= "<img id='series-cover' src='content/$series_cover_path' class='rounded float-left' alt='Cover image'>";
                $html .= "<h1 class='jumbotron-heading'>$series_title</h1>";
                $html .= "<p class='lead'>$series_synopsis</p>";
                $html .= "<p class='container'>";
                    for($i = 1; $i <= 5; $i++) {
                        if($i <= $series_evaluation) {
                            $html .= "<span class='fa fa-star checked'></span>";
                        } else {
                            $html .= "<span class='fa fa-star'></span>";
                        }
                    }
                $html .= "</p>";
                if($subscribe_exist) {
                    $html .= "<p><a href='util/subscribe.php?series_id=$series_id&user_id=$user_id' class='btn btn-secondary'>Unsubscribe</a></p>";
                } else {
                    $html .= "<p><a href='util/subscribe.php?series_id=$series_id&user_id=$user_id' class='btn btn-primary'>Subscribe</a></p>";
                }
            $html .= "</div>";
        $html .= "</section>";

        return $html;
    }

    function episodeListSection($episode_list, $empty_placeholder, $mode) {
        $html = "<div class='album py-5 bg-light'>";
            $html .= "<div class='container'>";
                $html .= "<div class='row'>";
                if(count($episode_list) > 0) {
                    foreach($episode_list as $episode) {
                        $series_id = $episode["Series_id"];
                        $series_title = $episode["Series_title"];
                        $episode_id = $episode["Episode_id"];
                        $episode_title = $episode["Episode_title"];
                        $episode_cover_path = $episode["Cover_path"];
                        $episode_evaluation = $episode["Evaluation"];
                        $episode_update_date = $episode["Update_date"];
                        $html .= "<a href='episode.php?series_id=$series_id&episode_id=$episode_id' class='col-md-4' style='text-decoration: none'>";
                            $html .= "<div class='card mb-4 shadow-sm'>";
                                if($episode_cover_path == "") {
                                    $html .= "<svg class='card-img-top' width='100%' height='225' focusable='false'>";
                                        $html .= "<title>Thumbnail</title>";
                                        $html .= "<rect width='100%' height='100%' fill='#55595c'></rect>";
                                        $html .= "<text x='50%' y='50%' fill='#eceeef' dy='.3em'>No Thumbnail</text>";
                                    $html .= "</svg>";
                                } else {
                                    $html .= "<img src='content/$episode_cover_path' alt='Thumbnail' class='card-img-top' width='100%' height='225'>";
                                }
                                $html .= "<div class='card-body'>";
                                    if($mode == "search" || $mode == "bookmark") {
                                        $html .= "<small class='text-muted'>$series_title</small>";
                                    }
                                    $html .= "<div class='d-flex justify-content-between align-items-center'>";
                                        $html .= "<p class='card-text episode-title'>$episode_title</p>";
                                        $html .= "<small class='text-muted float-right'></small>";
                                    $html .= "</div>";
                                    $html .= "<div class='d-flex justify-content-between align-items-center'>";
                                        $html .= "<div class='float-right'>";
                                            for($i = 1; $i <= 5; $i++) {
                                                if($i <= $episode_evaluation) {
                                                    $html .= "<span class='fa fa-star checked'></span>";
                                                } else {
                                                    $html .= "<span class='fa fa-star'></span>";
                                                }
                                            }
                                        $html .= "</div>";
                                        $html .= "<small class='text-muted'>$episode_update_date</small>";
                                    $html .= "</div>";
                                $html .= "</div>";
                            $html .= "</div>";
                        $html .= "</a>";
                    }
                } else {
                    $html .= "<h5>$empty_placeholder</h5>";
                }
                $html .= "</div>";
            $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    function episodeInformationSection($episode_information) {
        $html = "<section id='jumbotron-episode' class='jumbotron'>";
            $html .= "<div class='container'>";
            $series_id = $episode_information["Series_id"];
                $episode_title = $episode_information["Title"];
                $episode_evaluation = $episode_information["Evaluation"];
                $episode_update_date = $episode_information["Update_date"];
                $html .= "<p><a class='btn btn-secondary btn-sm' href='series.php?series_id=$series_id' role='button'>< Back</a></p>";
                $html .= "<h2>$episode_title</h2>";
                $html .= "<div class='float-left'>$episode_update_date</div>";
                    $html .= "<div class='float-right'>";
                        for($i = 1; $i <= 5; $i++) {
                            if($i <= $episode_evaluation) {
                                $html .= "<span class='fa fa-star checked'></span>";
                            } else {
                                $html .= "<span class='fa fa-star'></span>";
                            }
                        }
                    $html .= "</div>";
                $html .= "</div>";
            $html .= "</div>";
        $html .= "</section>";

        return $html;
    }

    function imageListRow($image_list) {
        $html = "<div class='row'>";

        foreach($image_list as $image) {
            $image_number = $image["Image_number"];
            $image_path = $image["Image_path"];
            $html .=  "<img src='content/$image_path' alt='Image $image_number' class='' width='100%' height='100%'>";
        }
        $html .= "</div>";

        return $html;
    }

    function evaluationForm($user_id, $series_id, $episode_id, $value) {
        $html = "<form class='form-inline' method='post' action='/util/evaluation.php'>";
            $html .= "<div class='form-group'>";
                $html .= "<input type='hidden' name='User_id' value='$user_id' />";
                $html .= "<input type='hidden' name='Series_id' value='$series_id' />";
                $html .= "<input type='hidden' name='Episode_id' value='$episode_id' />";
                $html .= "<select class='form-control custom-select' id='inputSeriesID' name='Value' required>";
                    $html .= "<option value=''>Episode score...</option>";
                    for($i = 1; $i <= 10; $i++) {
                        if($i == $value) {
                            $html .= "<option value='$i' selected='selected'>$i</option>";
                        } else {
                            $html .= "<option value='$i'>$i</option>";
                        }
                    }
                $html .= "</select>";
            $html .= "</div>";
            $html .= "<button class='btn btn-secondary' type='submit'>Evaluate</button>";
        $html .= "</form>";

        return $html;
    }
    
    function commentFormRow($user_id, $series_id, $episode_id) {
        $html = "<form class='row form-comment' method='post' action='/util/comment.php'>";
            $html .= "<input type='hidden' name='User_id' value='$user_id' />";
            $html .= "<input type='hidden' name='Series_id' value='$series_id' />";
            $html .= "<input type='hidden' name='Episode_id' value='$episode_id' />";
            $html .= "<div class='col-9'>";
                $html .= "<label for='inputComment' class='sr-only'>Enter comment here...</label>";
                $html .= "<textarea id='inputComment' name='Content' class='form-control' placeholder='Enter comment...' rows='3'></textarea>";
            $html .= "</div>";
            $html .= "<div class='col-3 text-center'>";
                $html .= "<button class='btn btn-lg btn-secondary btn-block' type='submit'>Comment</button>";
            $html .= "</div>";
        $html .= "</form>";
        
        return $html;
    }

    function commentListRow($comment_list) {
        $html = "<div class='row'>";
            $html .= "<table class='table table-striped'>";
                $html .= "<thead>";
                    $html .= "<tr>";
                        $html .= "<th scope='col'>User</th>";
                        $html .= "<th scope='col'>Comment</th>";
                        $html .= "<th scope='col'>Time</th>";
                    $html .= "</tr>";
                $html .= "</thead>";
                $html .= "<tbody>";
                if(count($comment_list) > 0) {
                    foreach ($comment_list as $comment) {
                        $comment_user_id = $comment["User_id"];
                        $comment_content = $comment["Content"];
                        $comment_update_time = $comment["Update_time"];
                        $html .= "<tr>";
                            $html .= "<th scope='row'>$comment_user_id</th>";
                            $html .= "<td>$comment_content</td>";
                            $html .= "<td>$comment_update_time</td>";
                        $html .= "</tr>";
                    }
                } else {
                    $html .= "<tr><td colspan='3'>No comments</td></tr>";
                }                
                $html .= "</tbody>";
            $html .= "</table>";
        $html .= "</div>";
        
        return $html;                    
    }

    function notificationSection($notification_list, $user_id) {
        $html = "<div class='album py-5 bg-light'>";
            $html .= "<div class='container'>";
                $html .= "<div class='row'>";
                    $html .= "<table class='table table-hover'>";
                        $html .= "<thead>";
                            $html .= "<tr>";
                                $html .= "<th scope='col'>Time</th>";
                                $html .= "<th scope='col'>Message</th>";
                            $html .= "</tr>";
                        $html .= "</thead>";
                        $html .= "<tbody>";
                        if(count($notification_list) > 0) {
                            foreach ($notification_list as $notification) {
                                $notification_id = $notification["Notification_id"];
                                $series_id = $notification["Series_id"];
                                $episode_id = $notification["Episode_id"];
                                $message = $notification["Message"];
                                $update_time = $notification["Update_time"];
                                $notified = $notification["Notified"];
                                if ($notified == "") {
                                    $html .= "<tr>";
                                } else {
                                    $html .= "<tr class='table-active'>";
                                }
                                $html .= "<td>$update_time</td>";
                                $html .= "<td><a href='util/notification.php?user_id=$user_id&notification_id=$notification_id&series_id=$series_id&episode_id=$episode_id'>$message</a></td>";
                                $html .= "</tr>";
                            }
                        } else {
                            $html .= "<h5>No notifications</h5>";;
                        }
                        $html .= "</tbody>";
                    $html .= "</table>";
                $html .= "</div>";
            $html .= "</div>";
        $html .= "</div>";
        
        return $html;
    }
?>