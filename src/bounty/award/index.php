<?php
require_once('/var/www/lib/all.php');
$protect = new ProtectWithAuth;
if (!$protect->logged_in()) {
    $protect->error(false, "Please login to access bountys");
    return;
}
function display_bounty($bounty) {
    $extra = "";
    if ($bounty->awarded == NULL || $bounty->awarded == 0) {
        $extra .= "<form class=\"ui large form\" action=\"/bounty/award/\" method=\"POST\">";
        $extra .= "<div class=\"field\">";
        $extra .= "<div class=\"ui left icon input\">";
        $extra .= "<i class=\"user icon\"></i>";
        $extra .= "<input name=\"awarded\" placeholder=\"E-mail to award to\"/>";
        $extra .= "<input type=\"hidden\" name=\"id\" value=\"" . $bounty->id . "\"/>";
        $extra .= "</div>";
        $extra .= "</div>";
        $extra .= "</form>";
    }
    echo $bounty->to_html($extra);
}

$args = array(
    'id'        =>  FILTER_VALIDATE_INT,
    'awarded'   =>  FILTER_VALIDATE_EMAIL,
);

$bounty_info = client_input($args);

$user = $protect->user_data();
if (isset($user['uid']) && isset($bounty_info['awarded'])) {
    $database = new Database;
    $bounty = $database->check_bounty($bounty_info);
    // The creator of the bounty has to match the logged in user
    if ($bounty != false && $bounty->creator == $user['uid']) {
        $bounty->awarded = $database->user_id($bounty_info['awarded']);
        $bounty = $database->award_bounty($bounty);
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
                    <a href="/bounty/view/" class="item">View</a>
                    <a href="/bounty/create/" class="item">Create</a>
                     <a href="/bounty/award/" class="item"><b>Award</b></a>
                </div>
            </div>
            <a href="/login/" class="item">Login</a>
        </div>
    </div>

    <div class="ui main text container">
        <div class="ui middle aligned center aligned grid">
            <div class="column">
                <h2 class="ui teal image header">
                    <div class="content">Award Bountys</div>
                </h2>
            </div>
        </div>
        <div class="ui list">
            <?php
            $database = new Database;
            $database->bountys_to_award($user['uid'], display_bounty);
            ?>
        </div>
    </div>

</body>
</html>
