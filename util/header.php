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
?>