<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="apple-mobile-web-app-title" content="GalaxyPHP">

    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />

    <title>Welcome - GalaxyPHP</title>

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

        span {
            font-size: 50px;
            display: inline-block;
            padding-right: 12px;
            padding-left: 12px;
            position: relative;
            animation: galaxy 10s infinite;
            animation-direction: alternate;
        }

        @keyframes galaxy {
            from {
                left: -200px;
                box-shadow: inset -3px 0px 0px #888;
            }
            to {
                left: 200px;
                box-shadow: inset 3px 0px 0px #888;
            }
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
            <span>GalaxyPHP</span>
            <p>GalaxyPHP is a very lightweight PHP Framework for web artisans. GalaxyPHP adopts MVC design pattern and can be used to boot up versioned APIs.</p>
            <p>&copy<?=date('Y')?>. All rights reserved.</p>
        </div>
    </div>

</body>

</html>