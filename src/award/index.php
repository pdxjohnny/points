<?php
require_once('/var/www/lib/all.php');
$protect = new ProtectWithAuth;
if (!$protect->logged_in()) {
    $protect->error(false, "Please login to award points");
    return;
}
$target_user = "";
$target_err = false;
$target_res = false;

$args = array(
    'username'	=> FILTER_VALIDATE_EMAIL,
    'points'    => FILTER_VALIDATE_INT,
);

$target = client_input($args);

$giver = $protect->user_data();
// Give the target some of the givers points
if (isset($giver['uid']) && $target != false) {
    $database = new Database;
    $target_user = $target['username'];
    // Make the giver
    $giver = array(
        'id'    =>  $giver['uid'],
    );
    // Give the points from the giver to the target
    // FIXME do this
    // var_dump($target);
    // if ($database->give_points($giver, $target)) {
    // }
    // Report both users points
    $target = $database->check_user($target);
    if ($target == false) {
        $target_err = "Could not find that user";
    } else {
        $target_res = $target->to_html();
    }
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
    <title>Award - Points</title>

    <link rel="stylesheet" type="text/css" href="/deps/semantic/semantic.min.css">

    <style type="text/css">
        .main.container {
            margin-top: 7em;
        }
    </style>
</head>

<body>

    <div class="ui fixed menu">
        <div class="ui container">
            <a href="/" class="item">Leaderboard</a>
            <a href="/search/" class="item">Search</a>
            <a href="/award/" class="header item">Award</a>
            <a href="/login/" class="item">Login</a>
        </div>
    </div>

    <div class="ui main text container">
        <div class="ui middle aligned center aligned grid">
            <div class="column">
                <h2 class="ui teal image header">
                    <div class="content">Award</div>
                </h2>
                <form class="ui large form" action="/award/" method="GET">
                    <div class="field">
                        <div class="ui left icon input">
                            <i class="user icon"></i>
                            <input type="text" name="username" placeholder="E-mail address" value="<?php echo $target_user;?>">
                        </div>
                    </div>
                    <button class="ui fluid large teal button">Award</button>
                    <?php if ($target_err != false) { ?>
                    <div class="ui negative message">
                        <p><?php echo $target_err;?></p>
                    </div>
                    <?php } ?>
                    <?php if ($target_res != false) { ?>
                    <div class="ui message">
                        <p><?php echo $target_res;?></p>
                    </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
