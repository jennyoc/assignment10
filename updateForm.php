<?php

include 'include/top.php';
include 'include/userAddNav.php';

$pmkUserId = $_GET["id"]; 
$firstName = $_POST["txtFirstName"];
$lastName = $_POST["txtLastName"];
$email = $_POST["txtEmail"];
$password = $_POST['txtPassword'];

$dbUserName = get_current_user() . '_admin';
$whichPass = "a"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_Shelter';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

// search database

$query = "SELECT * FROM `tblUser` WHERE `pmkUserId` = '$pmkUserId'";
$results = $thisDatabase->select($query, $data);


if ($pmkUserId > 0) {
 
    $query = "UPDATE `tblUser` SET 
                                `fldFirstName` = '$firstName',
                                `fldLastName` = '$lastName',
                                `fldEmail` = '$email',
                                `fldPassword` = '$password',     
                             WHERE `pmkUserId` = '$pmkUserId'"
     or die();
    
} else {

    $query = ("INSERT INTO `tblUser` (pmkUserId, fldFirstName, fldLastName, fldEmail, fldPassword) VALUES ('$pmkUserId', '$firstName', '$lastName','$email', '$password')") or die();                                              
}
?>