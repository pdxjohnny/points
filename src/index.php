<?php
require_once('/var/www/lib/all.php');
$protect = new ProtectWithAuth;

function display_user($user) {
    echo $user->to_html();
}
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- Site Properties -->
    <title>Leaderboard - Points</title>

    <link rel="stylesheet" type="text/css" href="/deps/semantic/semantic.min.css">

    <style type="text/css">
        .main.container {
            margin-top: 7em;
        }
        .list .item {
            margin-top: 1em;
            margin-bottom: 1em;
        }
    </style>
</head>

<body>

    <div class="ui fixed menu">
        <div class="ui container">
            <a href="/" class="header item">Leaderboard</a>
            <a href="/search/" class="item">Search</a>
            <a href="/login/" class="item">Login</a>
        </div>
    </div>

    <div class="ui main text container">
        <div class="ui middle aligned center aligned grid">
            <div class="column">
                <h2 class="ui teal image header">
                    <div class="content">Leaderboard</div>
                </h2>
                <p>Who's got the most points?</p>
            </div>
        </div>
        <div class="ui list">
            <?php
            $database = new Database;
            $database->top_100_users(display_user);
            ?>
        </div>
    </div>

</body>
</html>
