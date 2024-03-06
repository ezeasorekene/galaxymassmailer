<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="apple-mobile-web-app-title" content="GalaxyPHP">

    <link rel="shortcut icon" type="image/x-icon"
        href="/favicon.ico" />

    <title>403 Access Forbidden - <?=$_ENV['APP_NAME']?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Lato:100,300'>

    <style>
        * {
            transition: all 0.6s;
        }

        html {
            height: 100%;
        }

        body {
            font-family: 'Lato', sans-serif;
            color: #888;
            margin: 0;
        }

        #main {
            display: table;
            width: 100%;
            height: 100vh;
            text-align: center;
        }

        .fof {
            display: table-cell;
            vertical-align: middle;
        }

        .fof h1 {
            font-size: 50px;
            display: inline-block;
            padding-right: 12px;
            animation: type .5s alternate infinite;
        }

        .fof h2 {
            font-size: 30px;
            display: inline-block;
            padding-right: 12px;
            animation: type .5s alternate infinite;
        }

        @keyframes type {
            from {
                box-shadow: inset -3px 0px 0px #888;
            }

            to {
                box-shadow: inset -3px 0px 0px transparent;
            }
        }
    </style>

</head>

<body translate="no">
    <div id="main">
        <div class="fof">
            <h2>403 | <?= $data['message'] ?? "Access Forbidden" ?></h2>
        </div>
    </div>

</body>

</html>