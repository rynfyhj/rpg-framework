<?php

class rpg
{

    public static function view($name, $data = null)
    {
        if (is_file(settings::$root.'/app/views/'.$name.'.php'))
        {
            extract($data);
            require settings::$root.'/app/views/'.$name.'.php';
        }
        else if (is_file(settings::$root."/app/views/".$name.".html") && (is_array($data) || is_object($data)))
        {
            $file = file_get_contents(settings::$root."/app/views/".$name.".html");

            foreach($data as $content => $value)
            {
                if (is_array($value) || is_object($value))
                {
                    foreach($value as $key => $val)
                    {
                        $file = str_replace("{{".$key."}}", $val, $file);
                    }
                }
                else
                {
                    $file = str_replace("{{".$content."}}", $value, $file);
                }
            }

            echo $file;
        }
        else if (is_file(settings::$root."/app/views/".$name.".html") && $data == null)
        {
            echo file_get_contents(settings::$root."/app/views/".$name.".html");
        }
    }

    public static function model($name)
    {
        if (is_file(settings::$root.'/app/models/'.$name.'.php'))
        {
            require settings::$root.'/app/models/'.$name.'.php';
            return new $name;
        }
    }

    public static function module($name)
    {
        if (is_file(settings::$root.'/system/modules/'.$name.'.php'))
        {
            require settings::$root.'/system/modules/'.$name.'.php';
            return new $name;
        }
    }

    public static function controller($dir, $script)
    {
        if (is_dir(settings::$root.'/app/controllers/'.$dir))
        {
            if (is_file(settings::$root.'/app/controllers/'.$dir.'/'.$script.'.php'))
            {
                require settings::$root.'/app/controllers/'.$dir.'/'.$script.'.php';
                call_user_func([new $script, "main"]);
            }
            else
            {
                route::not_found();
            }
        }
    }

    public static function redirect($url, $wait = 0)
    {
        if ($wait != 0)
        {
            return header("Refresh: ".$wait."; url=".$url);
        }
        else
        {
            return header("Location: ".$url);
        }
    }

    public static function dump($data)
    {
        header("Content-Type: text/plain; charset=UTF-8");
        print_r($data);
        die();
    }

    public static function segment($no)
    {
        $uri = explode("/", settings::$uri);

        if ($no < count($uri))
        {
            return $uri[$no];
        }
        else
        {
            return null;
        }
    }

    public static function get($name = null)
    {
        if ($name != null)
        {
            return (array_key_exists($name, $_GET) ? $_GET[$name] : null);
        }
        else
        {
            return $_GET;
        }
    }

    public static function post($name = null)
    {
        if ($name != null)
        {
            return (array_key_exists($name, $_POST) ? $_POST[$name] : null);
        }
        else
        {
            return $_POST;
        }
    }

    public static function file($name = null)
    {
        if ($name != null)
        {
            return (array_key_exists($name, $_FILES) ? $_FILES[$name] : null);
        }
        else
        {
            return $_FILES;
        }
    }

    public static function cookie($name = null)
    {
        if ($name != null)
        {
            return (array_key_exists($name, $_COOKIE) ? $_COOKIE[$name] : null);
        }
        else
        {
            return $_COOKIE;
        }
    }

}
