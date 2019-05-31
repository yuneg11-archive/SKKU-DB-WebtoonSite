<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Webtoon</title>
    </head>
    <body>
        <?php
            session_start();

            if(!isset($_SESSION['User_id'])) {
                // Not signed in
                echo "<p><a href='signin.html'>Sign in</a></p>";
                echo "<p><a href='signup.html'>Sign up</a></p>";
            } else {
                // Signed in
                $user_id = $_SESSION['User_id'];
                $user_name = $_SESSION['User_name'];

                if($user_name !== "") {
                    echo "<p>Hello $user_name</p>";
                } else {
                    echo "<p>Hello $user_id</p>";
                }
                echo "<p><a href='/util/user_signout.php'>Sign out</a></p>";
            }
        ?>
    </body>
</html>
