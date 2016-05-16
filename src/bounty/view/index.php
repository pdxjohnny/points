<?php
require_once('/var/www/lib/all.php');
$protect = new ProtectWithAuth;
if (!$protect->logged_in()) {
    $protect->error(false, "Please login to access bountys");
    return;
}
function display_bounty($bounty) {
    echo $bounty->to_html();
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
            <a href="/" class="item">Leaderboard</a>
            <a href="/search/" class="item">Search</a>
            <div class="ui simple dropdown item">
                <b>Bounty</b> <i class="dropdown icon"></i>
                <div class="menu">
                    <a href="/bounty/view/" class="item"><b>View</b></a>
                    <a href="/bounty/create/" class="item">Create</a>
                </div>
            </div>
            <a href="/login/" class="item">Login</a>
        </div>
    </div>

    <div class="ui main text container">
        <div class="ui middle aligned center aligned grid">
            <div class="column">
                <h2 class="ui teal image header">
                    <div class="content">Bountys</div>
                </h2>
                <p>What points are up for grabs?</p>
            </div>
        </div>
        <div class="ui list">
            <?php
            $database = new Database;
            $database->top_100_bountys(display_bounty);
            ?>
        </div>
    </div>

</body>
</html>
