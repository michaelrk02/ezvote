<html>
    <head>
        <title><?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <script src="<?php echo site_url('content/lib').'?name=vue.min.js&type=text%2Fjavascript'; ?>"></script>
        <link rel="stylesheet" href="<?php echo site_url('content/lib').'?name=spectre.min.css&type=text%2Fcss'; ?>">
        <link rel="stylesheet" href="<?php echo site_url('content/lib').'?name=spectre-icons.min.css&type=text%2Fcss'; ?>">
        <link rel="stylesheet" href="<?php echo site_url('content/lib').'?name=spectre-exp.min.css&type=text%2Fcss'; ?>">
    </head>
    <body>
        <div style="display: flex; flex-direction: column; min-height: 100vh">
            <div style="flex: 1 0 auto">
                <div class="bg-primary" style="padding: 0.5rem">
                    <h1><?php echo APP_TITLE; ?></h1>
                    <h5><?php echo $title; ?></h5>
                </div>
                <div style="margin: 0.5rem">
