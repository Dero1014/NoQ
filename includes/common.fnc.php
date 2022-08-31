<?php

// MISCS //
function checkSet($set)
{
    if (isset($set)) {
        $set = 1;
    } else {
        $set = 0;
    }
    return $set;
}

function randomString()
{
    $ranString = "";
    $string = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    
    for ($i = 0; $i < 10; $i++) {
        $index = rand(0, strlen($string) - 1);
        $ranString .= $string[$index];
    }

    return $ranString;
}