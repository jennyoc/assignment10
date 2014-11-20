<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// series of functions to help you validate your data. notice that each
// function returns true or false

function verifyAlphaNum ($testString) {
	// Check for letters, numbers and dash, period, space and single quote only.
	return (preg_match ("/^([[:alnum:]]|-|\.|_|')+$/", $testString));
}

function verifyEmail ($testString) {
	// Check for a valid email address http://www.php.net/manual/en/filter.examples.validation.php
	return filter_var($testString, FILTER_VALIDATE_EMAIL);
}

function verifyNumeric ($testString) {
	// Check for numbers and period.
	return (is_numeric ($testString));
}

function verifyPhone ($testString) {
	// Check for usa phone number http://www.php.net/manual/en/function.preg-match.php
        $regex = '/^(?:1(?:[. -])?)?(?:\((?=\d{3}\)))?([2-9]\d{2})(?:(?<=\(\d{3})\))? ?(?:(?<=\d{3})[.-])?([2-9]\d{2})[. -]?(\d{4})(?: (?i:ext)\.? ?(\d{1,5}))?$/';

	return (preg_match($regex, $testString));
}

function verifyTime ($testString) {
       $regex = "/^[0-9:]+$/";

       return (preg_match($regex, $testString));
}

function verifyLetters ($testString) {

    $regex = "/^[A-Za-z]+$/";

    return (preg_match($regex, $testString));

}
function isUser($teststring) {
    global $thisDatabase;
    $query = "SELECT fldEmail FROM tblUser WHERE fldEmail=? ";
    $results = $thisDatabase->select($query, array($teststring));
    if (count($results)) {
    return true;
    }
    return false;
    //$regex = mysql_result(mysql_query($query), 0);
    //return (preg_match($regex, $testString));
}

function isScreenName($testString) {
    global $thisDatabase;
    $query = "SELECT fldScreenName FROM tblUser WHERE fldScreenName=? ";
    $results = $thisDatabase->select($query, array($testString));
    if (count($results)) {
        return true;
    }
    return false;
    //$regex = mysql_result(mysql_query($query), 0);
    //return (preg_match($regex, $testString));
}
?>