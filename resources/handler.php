<?php
/*
Copyright (C) Ad5001 2016 All rights reserved.
@link http://ad5001.ga
Do not attemped to modify this file if you're not sure on how it works.
This file process 404, 403 requests, custom index and some other stuff.
*/

// Definitions
$cfg = yaml_parse(file_get_contents(__DIR__ . "/../config.yml"));
$args = json_decode(file_get_contents(__DIR__ . "/args"), true);
$_POCKETMINE = $args["POCKETMINE"];
$_PLUGINS = $args["PLUGINS"];
$_PLAYERS = $args["PLAYERS"];
$_LEVELS = $args["LEVELS"];


// Removing GET from the request
$uri = $_SERVER["REQUEST_URI"];
if(strpos($uri, "?") !== false) {
    $uri = explode("?", $uri)[0];
}
$_SERVER["REQUEST_URI"] = $uri;


// Calling handlers.
foreach($args["HANDLERS"] as $handler) {
    if((include $handler) == false) {
        return true;
    }
}


// Domains parsing
$host = $_SERVER["HTTP_HOST"];
if(isset($cfg["Parked domains"][$host])) {
    $host = $cfg["Parked domains"][$host];
}
if(in_array($host, $cfg["Domains"])) {
    $_SERVER["REQUEST_URI"] = $host . $_SERVER["REQUEST_URI"];
} else {
    unallowedDomain();
    return true;
}


// Getting the file & output it if possible.
if(!file_exists(__DIR__ . "/../$host" . $uri)) {
    include __DIR__ . "/../$host" . "/" . $cfg[404];
} elseif(in_array($uri, $cfg["denied-pages"])) {
    include __DIR__ . "/../$host" . "/" . $cfg[403];
} elseif(is_dir(__DIR__ . "/../" .$uri)) {
    if(file_exists(__DIR__ . "/../$host" . $uri . "index.html")) {
        include __DIR__ . "/../$host" . $uri . "index.html";
    } elseif(file_exists(__DIR__ . "/../$host" . $uri . "index.php")) {
        include __DIR__ . "/../$host" . $uri . "index.php";
    } elseif(file_exists(__DIR__ . "/../$host" . $uri . $cfg["index"])) {
        include __DIR__ . "/../$host" . $uri . $cfg["index"];
    }
} else {
    include __DIR__ . "/" . $host  . $uri;
}



function unallowedDomain() {
    echo <<<A
<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300" rel="stylesheet"> 
<style>
body {
    padding: 60px;
}
div.container {
    padding: 40px;
    border: 3px solid lightgray;
    border-radius: 8px;
    background: linear-gradient(45deg, gray, grey);
    height: 70%;
}
h1, h2, h3, p {
    font-family: 'Roboto Condensed', sans-serif;
}
</style>
<link rel="icon" src="http://serveur.cf/Online/icon.ico" href="http://serveur.cf/Online/icon.ico" />
</head>
<body>
<div class="container">
<h1><img src="http://serveur.cf/Online/icon.png" style="width: 30px; height: 30px;"></img>Unallowed domain</h1><hr>
<p>This IP does not have any domain on this machine. Please refer to your server administartor if you think it's an error.</p>
<h2 style="float: right;">Online 1.6 - Eclipse edition</a></h2>
</body>
</style>
A;
}