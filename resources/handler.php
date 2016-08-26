<?php
/*
Copyright (C) Ad5001 2016 All rights reserved.
@link http://ad5001.ga
Do not attemped to modify this file if you're not sure on how it works.
This file process 404, 403 requests, custom index and some other stuff.
*/
$cfg = yaml_parse(file_get_contents(__DIR__ . "/config.yml"));
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
$uri = $_SERVER["REQUEST_URI"];
if(strpos($uri, "?") !== false) {
    $uri = explode("?", $uri)[0];
}
if(!file_exists(__DIR__ . "/" . $uri)) {
    echo file_get_contents(__DIR__ . "/" . $host . "/" . $cfg[404]);
} elseif(in_array($uri, yaml_parse(file_get_contents(__DIR__ . "/config.yml"))["denied-pages"])) {
    echo file_get_contents(__DIR__ . "/" . $host . "/" . $cfg[403]);
} elseif(is_dir(__DIR__ . "/" .$uri)) {
    if(file_exists(__DIR__ . "/" . $uri . "index.html")) {
        include(__DIR__ . "/" . $uri . "index.html");
    } elseif(file_exists(__DIR__ . "/" . $uri . "index.php")) {
        include(__DIR__ . "/" . $uri . "index.php");
    } elseif(file_exists(__DIR__ . "/" . $uri . $cfg["index"])) {
        include(__DIR__ . "/" . $uri . $cfg["index"]);
    }
} else {
    include(__DIR__ . "/" . $uri);
}



function unallowedDomain() {
    echo <<<A
<html>
<head>
<style>
body {
    padding: 60px;
}
div.container {
    padding: 40px;
    border: 3px solid white;
    border-radius: 8px;
    background-color: gray;
    height: 70%;
}
h1, h2, h3, p {
    font-family: Arial;
}
</style>
<link rel="icon" src="http://ad5001.ga/Online/icon.ico" href="http://ad5001.ga/Online/icon.ico" />
</head>
<body>
<div class="container">
<h1><img src="http://ad5001.ga/Online/icon.png" style="width: 30px; height: 30px;"></img>Unallowed domain</h1><hr>
<p>This IP does not have any domain on this machine. Please refer to your server administartor if you think it's an error.</p>
<h2 style="float: right;"><a href="http://projects.ad5001.ga/Online">Online 1.5 - Eclipse edition</a></h2>
</body>
</style>
A;
}