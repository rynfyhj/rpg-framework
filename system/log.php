<?php

class log
{

    public static function error_response(Throwable $e)
    {
        $error = [
            "Line" => $e->getLine(),
            "Status" => get_class($e),
            "File" => $e->getFile(),
            "Message" => $e->getMessage(),
            "Stack Trace" => explode("\n", $e->getTraceAsString())
        ];

        $json = json_encode($error, JSON_INVALID_UTF8_IGNORE);

        header("Content-Type: application/json");
        http_response_code(500);
        echo $json;
    
        if (settings::$logs == 1)
        {
            return file_put_contents
            (
                settings::$root."/system/logs/error/".settings::$date.".json",
                $json."\n", FILE_APPEND | LOCK_EX
            );
        }
    }

    public static function request()
    {
        $request = new stdClass;

        $request->status    = settings::$status;
        $request->time      = settings::$time;
        $request->date      = settings::$date;
        $request->ip        = settings::$ip;
        $request->host      = settings::$scheme."://".settings::$host;
        $request->request   = settings::$method." ".settings::$uri." ".settings::$protocol;
        $request->useragent = settings::$ua;

        return json_encode($request, JSON_INVALID_UTF8_IGNORE);
    }

    public static function access()
    {
        return file_put_contents
        (
            settings::$root."/system/logs/access/".settings::$date.".json",
            self::request()."\n", FILE_APPEND | LOCK_EX
        );
    }

    public static function access_logs($line = 10, $return = 0)
    {
        $log_path = settings::$root."/system/logs/access/".settings::$date.".json";

        if (!is_file($log_path))
        {
            if ($return == 1)
            {
                return new stdClass;
            }

            header("Content-Type: application/json");
            echo "[]";
            return;
        }

        $log = file_get_contents($log_path);

        $parse = array_reverse(explode("\n", $log));
        $build = implode(",", array_slice($parse, 1, $line));

        $access_logs = "[".ltrim($build, ",")."]";

        if ($return == 1)
        {
            return self::obj(json_decode($access_logs));
        }
        else
        {
            header("Content-Type: application/json");
            echo $access_logs;
        }
    }

    public static function error_logs($line = 10, $return = 0)
    {
        $log_path = settings::$root."/system/logs/error/".settings::$date.".json";

        if (!is_file($log_path))
        {
            if ($return == 1)
            {
                return new stdClass;
            }

            header("Content-Type: application/json");
            echo "[]";
            return;
        }

        $log = file_get_contents($log_path);

        $parse = array_reverse(explode("\n", $log));
        $build = implode(",", array_slice($parse, 1, $line));

        $error_logs = "[".ltrim($build, ",")."]";

        if ($return == 1)
        {
            return self::obj(json_decode($error_logs));
        }
        else
        {
            header("Content-Type: application/json");
            echo $error_logs;
        }
    }

    public static function obj($array)
    {
        $data = new stdClass;
        $super = "_";

        foreach ($array as $key => $val)
        {
            $key = $super.$key;
            $data->$key = $val;
        }

        return $data;
    }

}
