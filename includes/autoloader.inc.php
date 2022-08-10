<?php
spl_autoload_register('loadClasses');

function loadClasses($className)
{
    $url = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

    if (strpos($url, 'includes')) {
        $path = "../classes/";
    }else {
        $path = "classes/";
    }

    if (strpos($url, 'sites')) {
        $path = "../classes/";
    }else {
        $path = "classes/";
    }
    
    $ext = ".class.php";
    $fullPath = $path . $className . $ext;
    include_once $fullPath;
}

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