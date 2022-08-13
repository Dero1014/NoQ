<?php
spl_autoload_register(function ($class)
{
    $class = lcfirst($class . '.class.php');
    $url = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
    if (strpos($url, 'includes') || strpos($url, 'sites') || strpos($url, 'header')) {
        $path = "../classes/";
    }else {
        $path = "classes/";
    }

    $fullPath = $path.$class;
    echo $fullPath . "\n";
    include_once $fullPath;
});



/*
function loadIncludes($includesName)
{
    $url = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

    if (strpos($url, 'classes')) {
        $path = "../includes/";
    }else {
        $path = "includes/";
    }

    if (strpos($url, 'sites')) {
        $path = "../includes/";
    }else {
        $path = "includes/";
    }
    $ext = ".inc.php";
    $fullPath = $path . $includesName . $ext;
    include_once $fullPath;
}
*/