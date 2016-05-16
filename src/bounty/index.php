<?php
require_once('/var/www/lib/all.php');
$protect = new ProtectWithAuth;
if (!$protect->logged_in()) {
    $protect->error(false, "Please login to access bountys");
    return;
}
$search_err = false;
$search_res = false;

$args = array(
    'id'    =>  FILTER_VALIDATE_INT,
);

$bounty = client_input($args);
if ($bounty != false) {
    $database = new Database;
    $bounty = $database->check_bounty($bounty);
    if ($bounty == false) {
        $search_err = "Could not find that bounty";
    } else {
        $search_res = $bounty->to_html();
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
                    <a href="/bounty/create/" class="item">Create</a>
                </div>
            </div>
            <a href="/login/" class="item">Login</a>
        </div>
    </div>

    <div class="ui main text container">
        <div class="ui middle aligned center aligned grid">
            <div class="column">
                    <?php if ($search_err != false) { ?>
                    <div class="ui negative message">
                        <p><?php echo $search_err;?></p>
                    </div>
                    <?php } ?>
                    <?php if ($search_res != false) {
                        echo $search_res;
                    } ?>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
