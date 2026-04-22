<?php

if (is_file(__DIR__."/../vendor/autoload.php"))
{
    require __DIR__."/../vendor/autoload.php";
}

require __DIR__."/settings.php";
require __DIR__."/security.php";
require __DIR__."/route.php";
require __DIR__."/init.php";
require __DIR__."/log.php";

$settings = new settings;
$security = new security;
$route    = new route;
$init     = new init;
$log      = new log;

// Config
require settings::$root."/app/root/config.php";

// Database
require __DIR__."/database/driver.php";
require __DIR__."/database/model.php";

// Framework
require __DIR__."/rpg.php";
