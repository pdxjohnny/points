<?php
require_once('/var/www/lib/all.php');
$protect = new ProtectWithAuth;
$login_err = false;

$args = array(
    'username'	=> FILTER_SANITIZE_ENCODED,
    'password'	=> FILTER_SANITIZE_ENCODED,
);

$user = client_input($args);
if ($user != false) {
    $database = new Database;
    $user = $database->login_user($user);
    if ($user == false) {
        // They failed so tell them
        $login_err = "Invalid Email or Password";
    } else {
        // Success so set the login cookie and redirect
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
    <title>Login - Points</title>

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
            <a href="/login/" class="header item">Login</a>
        </div>
    </div>

    <div class="ui main text container">
        <div class="ui middle aligned center aligned grid">
            <div class="column">
                <h2 class="ui teal image header">
                    <div class="content">Login</div>
                </h2>
                <form class="ui large form" action="/login/" method="POST">
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
                        <button class="ui fluid large teal button">Login</button>
                    </div>
                    <?php if ($login_err != false) { ?>
                    <div class="ui negative message">
                        <p><?php echo $login_err;?></p>
                    </div>
                    <?php } ?>
                </form>

                <div class="ui message">
                    Need an account? <a href="/register/">Register</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
