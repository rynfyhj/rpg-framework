<?php

require __DIR__."/../system/boot.php";

ob_start();

try
{
    $route->app();
    $output = ob_get_clean();
    echo $output;
}
catch (Throwable $e)
{
    ob_end_clean();
    $log->error_response($e);
}
