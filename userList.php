<?php
/* %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
 * the purpose of this page is to display a list of poets sorted 
 * 
 * Written By: Robert Erickson robert.erickson@uvm.edu
 * Last updated on: November 20, 2014
 */
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^

$admin = true;
include "include/top.php";
?>
<nav>
    <ol>
        <li class="activePage">Poets</li><li><a href="userForm.php">Add User</a></li>    </ol>
</nav>
<?php

print "<article>";
$dbUserName = get_current_user() . '_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_Shelter';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// prepare the sql statement
$orderBy = "ORDER BY fldLastName";

$query  = "SELECT pmkUserId, fldFirstName, fldLastName, fldEmail, fldPassword ";
$query .= "FROM tblUser " . $orderBy;

if ($debug)
    print "<p>sql " . $query;

$users = $thisDatabase->select($query);

if ($debug) {
    print "<pre>";
    print_r($users);
    print "</pre>";
}

// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// print out the results
print "<ol>\n";

foreach ($users as $user) {
   
    print "<li>";
    if ($admin) {
        print '<a href="userForm.php?id=' . $user["pmkUserId"] . '">[Edit]</a> ';
    }
    print $user['fldFirstName'] . "   " . $user['fldLastName'] . "  " . $user['fldEmail'] . "  " . $user['fldPassword'] . "</li>\n";
}
print "</ol>\n";
print "</article>";
include "include/footer.php";
?>
</body>
</html>






