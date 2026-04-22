<?php

class route
{

    public static function app()
    {
        if (settings::$logs == 1)
        {
            log::access();
        }

        require settings::$root."/app/root/controller.php";

        $get = stripos(settings::$uri, "?");

        if ($get !== false)
        {
            $params = explode("?", settings::$uri);
            $param = explode("&", security::xss_protection($params[1]));

            self::set($param);

            $url = explode("/", $params[0]);

            if (!empty($url[1]) && is_file(settings::$root."/app/controllers/".$url[1].".php"))
            {
                require settings::$root."/app/controllers/".$url[1].".php";
                call_user_func([new $url[1], "main"]);
            }
            else if ($params[0] == "/")
            {
                if (is_file(settings::$root."/app/controllers/".settings::$index.".php"))
                {
                    require settings::$root."/app/controllers/".settings::$index.".php";
                    call_user_func([new settings::$index, "main"]);
                }
                else
                {
                    self::index();
                }
            }
            else
            {
                self::not_found();
            }
        }
        else
        {
            $url = explode("/", settings::$uri);

            if (!empty($url[1]) && is_file(settings::$root."/app/controllers/".$url[1].".php"))
            {
                require settings::$root."/app/controllers/".$url[1].".php";
                call_user_func([new $url[1], "main"]);
            }
            else if (settings::$uri == "/")
            {
                if (is_file(settings::$root."/app/controllers/".settings::$index.".php"))
                {
                    require settings::$root."/app/controllers/".settings::$index.".php";
                    call_user_func([new settings::$index, "main"]);
                }
                else
                {
                    self::index();
                }
            }
            else
            {
                self::not_found();
            }
        }
        
    }

    public static function set($param = array())
    {
        $_GET = array();
    
        if (count($param) > 0)
        {
            foreach ($param as $value)
            {
                $x = explode("=", $value, 2);

                if (count($x) === 2 && $x[0] !== "")
                {
                    $_GET[urldecode($x[0])] = urldecode($x[1]);
                }
            }
        }
    }

    public static function index()
    {
        echo file_get_contents(settings::$root."/system/templates/index.html");
    }

    public static function not_found()
    {
        if (is_file(settings::$root."/app/controllers/".settings::$not_found.".php"))
        {
            require settings::$root."/app/controllers/".settings::$not_found.".php";
            call_user_func([new settings::$not_found, "main"]);
        }
        else
        {
            echo file_get_contents(settings::$root."/system/templates/404.html");
        }
    }

}