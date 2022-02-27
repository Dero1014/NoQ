<?php

$servername = "localhost";
$username = "root";
$password = "Ujaxcm+4%psPjyBr";
$dbname = "noQdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (getcwd() != "/var/www/html/sites") {
    //echo "Connected successfully";
}