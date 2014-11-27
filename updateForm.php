<?php

include 'include/top.php';
include 'include/editNav.php';

$pmkUserId = $_GET["id"]; 
$firstName = $_POST["txtFirstName"];
$lastName = $_POST["txtLastName"];
$email = $_POST["txtEmail"];
$password = $_POST['txtPassword'];

$dbUserName = get_current_user() . '_admin';
$whichPass = "a"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_Final_Project';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

// search database

$query = "SELECT * FROM `tblUsers` WHERE `pmkUserId` = '$pmkUserId'";
$results = $thisDatabase->select($query, $data);


if ($pmkUserId > 0) {
 
    $query = "UPDATE `tblUsers` SET 
                                `fldFirstName` = '$firstName',
                                `fldLastName` = '$lastName',
                                `fldEmail` = '$email',
                                `fldPassword` = '$password',     
                             WHERE `pmkUserId` = '$pmkUserId'"
     or die();
    
} else {

    $query = ("INSERT INTO `tblUsers` (pmkUserId, fldFirstName, fldLastName, fldEmail, fldPassword) VALUES ('$pmkUserId', '$firstName', '$lastName','$email', '$password')") or die();                                              
}
?>