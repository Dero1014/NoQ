<?php
//check the location of the include to know how to include user.inf.php
if (getcwd() === "/var/www/html") {
    include 'includes/user.inf.php';
    include 'includes/worker.inf.php';
} else {
    include '../includes/user.inf.php';
    include '../includes/worker.inf.php';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous">
    </script>

    <style>
        table,
        th,
        td {
            border: 1px solid black;
        }
    </style>
</head>

<body>

    <div class="header">
        <a href="index.php" class="logo">NoQ</a>
        <div class="header-right">
            <a href="../index.php">Home</a>
            <?php

            if (isset($user)) {
                echo "<a href='../includes/logout.inc.php'>LogOut</a>";

                if ($user->getCompanyTag() == 1) {
                    echo "<a href='../sites/company.site.php?page=service'>Service Management</a>";
                    echo "<a href='../sites/company.site.php?page=worker'>Workers</a>";
                } else {
                    echo "<a href='https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=Qu=$uId&choe=UTF-8'>Your QR</a>";
                    echo "<a href='../sites/user.site.php'>Service Registration</a>";
                }
            }elseif (isset($_SESSION["workerid"])) {
                echo "<a href='../sites/worker.site.php?access=granted'>Working services</a>";
                echo "<a href='../includes/logout.inc.php'>LogOut</a>";
            } else {
                echo "<a href='../sites/login.site.php'>LogIn</a>";
                echo "<a href='../sites/signup.site.php'>SignUp</a>";
            }

            ?>

        </div>
    </div>