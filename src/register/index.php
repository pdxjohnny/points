<?php
require_once('/var/www/lib/all.php');
$protect = new ProtectWithAuth;
$register_err = false;

$args = array(
    'username'	=> FILTER_SANITIZE_ENCODED,
    'password'	=> FILTER_SANITIZE_ENCODED,
);

$user = client_input($args);
if ($user != false) {
    $database = new Database;
    $user = $database->create_user($user);
    if ($user == false) {
        // They failed so tell them
        $register_err = "That Email is already in use";
    } else {
        // Success so set the register cookie and redirect
        $protect->set_token_and_redirect($user, 202, '');
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
    <title>Register - Points</title>

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
            <a href="/login/" class="item">Login</a>
            <a href="/register/" class="header item">Register</a>
        </div>
    </div>

    <div class="ui main text container">
        <div class="ui middle aligned center aligned grid">
            <div class="column">
                <h2 class="ui teal image header">
                    <div class="content">Register</div>
                </h2>
                <form class="ui large form" action="/register/" method="POST">
                    <div class="ui stacked segment">
                        <div class="field">
                            <div class="ui left icon input">
                                <i class="user icon"></i>
                                <input type="text" name="username" placeholder="E-mail address">
                            </div>
                        </div>
                        <div class="field">
                            <div class="ui left icon input">
                                <i class="lock icon"></i>
                                <input type="password" name="password" placeholder="Password">
                            </div>
                        </div>
                        <button class="ui fluid large teal button">Register</button>
                    </div>
                    <?php if ($register_err != false) { ?>
                    <div class="ui negative message">
                        <p><?php echo $register_err;?></p>
                    </div>
                    <?php } ?>
                </form>

                <div class="ui message">
                    Have an account? <a href="/login/">Login</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
