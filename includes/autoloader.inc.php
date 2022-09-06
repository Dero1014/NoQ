<?php
spl_autoload_register(function ($class)
{
    $class = strtolower($class . '.class.php');
    $url = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
    if (strpos($url, 'includes') || strpos($url, 'sites') || strpos($url, 'header') || strpos($url, 'mobile')) {
        $path = "../classes/";
    }else {
        $path = "classes/";
    }

    $fullPath = $path.$class;
    include_once $fullPath;
});