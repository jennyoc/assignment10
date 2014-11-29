<!DOCTYPE HTML>
<html lang="en">

    <head>
        <title>Puppy LoVermont</title>
        <meta charset="utf-8">
        <meta name="author" content="Jennifer Magie and Jennifer O'Callaghan">
        <meta name="description" content="Vermont shelter site for members to search for rescued dogs.">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="style.css" type="text/css" media="screen">

        <link rel="shortcut icon" href="image/paw.ico">
        <link rel="icon" type="image/ico" href="image/paw.ico">

        <?php
        $debug = false;

// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// PATH SETUP
//
//  $domain = "https://www.uvm.edu" or http://www.uvm.edu;

        $domain = "http://";
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS']) {
                $domain = "https://";
            }
        }

        $server = htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES, "UTF-8");

        $domain .= $server;

        $phpSelf = htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, "UTF-8");

        $path_parts = pathinfo($phpSelf);

        if ($debug) {
            print "<p>Domain" . $domain;
            print "<p>php Self" . $phpSelf;
            print "<p>Path Parts<pre>";
            print_r($path_parts);
            print "</pre>";
        }

// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// inlcude all libraries
//

        require_once('lib/security.php');

        if ($path_parts['filename'] == "search") {
            include "lib/validation-functions.php";
        }

        if ($path_parts['filename'] == "index") {
            include "lib/validation-functions.php";
            include "lib/mail-message.php";
            include "../bin/myDatabase.php";
        }
        if ($path_parts['filename'] == "about-us") {
            include "lib/validation-functions.php";
            include "lib/mail-message.php";
            include "../bin/myDatabase.php";
        }
        if ($path_parts['filename'] == "adopt") {
            include "lib/validation-functions.php";
            include "lib/mail-message.php";
            include "../bin/myDatabase.php";
        }

        if ($path_parts['filename'] == "register") {
            include "lib/validation-functions.php";
            include "lib/mail-message.php";
            include "../bin/myDatabase.php";
        }
        if ($path_parts['filename'] == "confirmation") {
            include "lib/validation-functions.php";
            include "lib/mail-message.php";
            include "../bin/myDatabase.php";
        }

        if ($path_parts['filename'] == "userAddForm") {
            include "lib/validation-functions.php";
            include "lib/mail-message.php";
            include "../bin/myDatabase.php";
        }

        if ($path_parts['filename'] == "userList") {
            include "lib/validation-functions.php";
            include "lib/mail-message.php";
            include "../bin/myDatabase.php";
        }
        if ($path_parts['filename'] == "userUpdateForm") {
            include "lib/validation-functions.php";
            include "lib/mail-message.php";
            include "../bin/myDatabase.php";
        }
        if ($path_parts['filename'] == "shelterUpdateForm") {
            include "lib/validation-functions.php";
            include "../bin/myDatabase.php";
        }
        if ($path_parts['filename'] == "shelterList") {
            include "lib/validation-functions.php";
            include "../bin/myDatabase.php";
        }
        if ($path_parts['filename'] == "shelterAddForm") {
            include "lib/validation-functions.php";
            include "../bin/myDatabase.php";
        }
        
        if ($path_parts['filename'] == "dogAddForm") {
            include "lib/validation-functions.php";
            include "../bin/myDatabase.php";
        }
        
        if ($path_parts['filename'] == "dogList") {
            include "../bin/myDatabase.php";
        }
        if ($path_parts['filename'] == "dogUpdateForm") {
            include "lib/validation-functions.php";
            include "../bin/myDatabase.php";
        }
        
        ?>

    </head>
    <!-- ################ body section ######################### -->

    <?php
    print '<body id="' . $path_parts['filename'] . '">';

    include "nav.php";
    ?>
