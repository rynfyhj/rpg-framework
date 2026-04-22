<?php

class settings
{

    // Server
    public static $root;

    public static $method;

    public static $script;

    public static $scheme;

    public static $status;

    public static $cookie;

    public static $protocol;

    public static $host;

    public static $uri;

    public static $ip;

    public static $ua;

    // Datetime
    public static $date;

    public static $time;

    public static $today;

    public static $clock;

    // Constants
    public static $logs      = 0;

    private static $debug    = 0;

    private static $lang     = "tr_TR.UTF-8";

    private static $timezone = "Europe/Istanbul";

    // Defaults
    public static $index     = "index";

    public static $not_found = "not_found";

    public function __construct()
    {
        error_reporting(E_ERROR);
        ini_set("display_errors", self::$debug);

        setlocale(LC_ALL, self::$lang);
        date_default_timezone_set(self::$timezone);

        self::server();
        self::datetime();
    }

    private static function server()
    {
        self::$root = str_replace("/public", "", $_SERVER["DOCUMENT_ROOT"]);

        self::$method   = $_SERVER["REQUEST_METHOD"];
        self::$script   = (isset($_SERVER["SCRIPT_NAME"]) ? $_SERVER["SCRIPT_NAME"] : "");
        self::$scheme   = (isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] : ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http"));
        self::$status   = (isset($_SERVER["REDIRECT_STATUS"]) ? $_SERVER["REDIRECT_STATUS"] : "200");
        self::$protocol = $_SERVER["SERVER_PROTOCOL"];
        self::$host     = $_SERVER["HTTP_HOST"];
        self::$uri      = urldecode($_SERVER["REQUEST_URI"]);
        self::$ip       = $_SERVER["REMOTE_ADDR"];
        self::$ua       = (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "");

        if (isset($_SERVER["HTTP_COOKIE"]))
        {
            self::$cookie = $_SERVER["HTTP_COOKIE"];
        }
        else
        {
            self::$cookie = null;
        }
    }

    private static function datetime()
    {
        self::$date  = date("Y-m-d");
        self::$time  = date("H:i:s");

        self::$today = date("j F Y, l");
        self::$clock = date("H:i");
    }

}
