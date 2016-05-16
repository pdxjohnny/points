<?php
require_once('/var/www/lib/all.php');
$protect = new ProtectWithAuth;
if (!$protect->logged_in()) {
    $protect->error(false, "Please login to access bountys");
    return;
}
$bounty_user = "";
$bounty_err = false;
$bounty_res = false;

$args = array(
    'title'         =>  FILTER_SANITIZE_STRING,
    'description'   =>  FILTER_SANITIZE_STRING,
    'points'        =>  FILTER_VALIDATE_INT,
);

$bounty_info = client_input($args);

$giver = $protect->user_data();
if (isset($giver['uid']) && isset($bounty_info['points'])) {
    $database = new Database;
    $bounty_info['creator'] = $giver['uid'];
    $bounty = new Bounty($bounty_info);
    $bounty = $database->create_bounty($bounty);
    if ($bounty->id == 0) {
        $bounty_err = "Could not create bounty";
    } else {
        $bounty_res = $bounty->to_html();
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
    <title>Bounty - Points</title>

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
            <div class="ui simple dropdown item">
                <b>Bounty</b> <i class="dropdown icon"></i>
                <div class="menu">
                    <a href="/bounty/view/" class="item">View</a>
                    <a href="/bounty/create/" class="item"><b>Create</b></a>
                </div>
            </div>
            <a href="/login/" class="item">Login</a>
        </div>
    </div>

    <div class="ui main text container">
        <div class="ui middle aligned center aligned grid">
            <div class="column">
                <h2 class="ui teal image header">
                    <div class="content">Create Bounty</div>
                </h2>
                <form class="ui large form" action="/bounty/create/" method="POST">
                    <div class="field">
                        <div class="ui left icon input">
                            <i class="file text icon"></i>
                            <input type="text" name="title" placeholder="Title"/>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui icon input">
                            <textarea type="text" name="description" placeholder="Description"/></textarea>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui left icon input">
                            <i class="gift icon"></i>
                            <input type="number" name="points" placeholder="Points" value="10"/>
                        </div>
                    </div>
                    <button class="ui fluid large teal button">Create</button>
                    <?php if ($bounty_err != false) { ?>
                    <div class="ui negative message">
                        <p><?php echo $bounty_err;?></p>
                    </div>
                    <?php } ?>
                    <?php if ($bounty_res != false) {
                        echo $bounty_res;
                    } ?>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
