<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Webtoon</title>

  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/font-awesome.min.css" rel="stylesheet">
  <link href="/css/index.css" rel="stylesheet">
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
?>
<header>
  <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <a class="navbar-brand" href="#">Webtoon</a>
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
        <button class="btn my-2 my-sm-0" type="submit"><img src="/img/search.png"></button>
      </form>
    </div>
  </nav>
</header>
<main role="main">
  <div class="album py-5 bg-light">
    <div class="container">
      <div class="row">
          <?php
          for($i = 1; $i <=10; $i++) {
              echo "<a href='#' class='col-md-4' style='text-decoration: none'>"
                  ."<div class='card mb-4 shadow-sm'>"
                  ."<svg class='card-img-top' width='100%' height='225' focusable='false'>"
                  ."<title>Thumbnail</title>"
                  ."<rect width='100%' height='100%' fill='#55595c'/>"
                  ."<text x='50%' y='50%' fill='#eceeef' dy='.3em'>No Thumbnail</text>"
                  ."</svg>"
                  ."<div class='card-body'>"
                  ."<div class='d-flex justify-content-between align-items-center'>"
                  ."<p class='card-text series-title'>Title</p>"
                  ."<small class='text-muted series-author float-right'>Author</small>"
                  ."</div>"
                  ."<div class='d-flex justify-content-between align-items-center'>"
                  ."<div class='float-right'>"
                  ."<span class='fa fa-star checked'></span>"
                  ."<span class='fa fa-star checked'></span>"
                  ."<span class='fa fa-star checked'></span>"
                  ."<span class='fa fa-star'></span>"
                  ."<span class='fa fa-star'></span>"
                  ."</div>"
                  ."<small class='text-muted'>Update: 2019.6.2</small>"
                  ."</div>"
                  ."</div>"
                  ."</div>"
                  ."</a>";
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

