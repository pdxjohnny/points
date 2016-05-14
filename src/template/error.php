<!DOCTYPE html>
<html>
<head>
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- Site Properties -->
    <title>Error - Points</title>

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
            <a href="/award/" class="item">Award</a>
            <a href="/login/" class="item">Login</a>
        </div>
    </div>

    <div class="ui main text container">
        <div class="ui middle aligned center aligned grid">
            <div class="column">
                <h1 class="ui red image header">
                    <div class="content">Error <?php echo $err['code'];?></div>
                </h1>
                <div class="ui negative message">
                    <p><?php echo $err['reason'];?></p>
                    <p><?php echo $err['message'];?></p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
