<?php
/*
Copyright (C) Ad5001 2016 All rights reserved.
@link http://ad5001.ga
Do not attemped to modify this file if you're not sure on how it works.
This file process 404, 403 requests and custom index.
*/
if(!file_exists(__DIR__ . $_SERVER["REQUEST_URI"])) {
    echo file_get_contents(__DIR__ . "/" . yaml_parse(file_get_contents(__DIR__ . "/config.yml"))[404]);
} elseif($_SERVER["REQUEST_URI"] == "/") {
    echo file_get_contents(__DIR__ . "/" . yaml_parse(file_get_contents(__DIR__ . "/config.yml"))["index"]);
} elseif(in_array($_SERVER["REQUEST_URI"], yaml_parse(file_get_contents(__DIR__ . "/config.yml"))["denied-pages"])) {
    echo file_get_contents(__DIR__ . "/" . yaml_parse(file_get_contents(__DIR__ . "/config.yml"))[403]);
} else {
    return false;
}