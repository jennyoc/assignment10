<?php
/* %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
 * the purpose of this page is to display a list of users sorted
 */
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^

$admin = true;
include "include/top.php";
include "include/editNav.php";
?>

<?php

print "<article>";
$dbUserName = get_current_user() . '_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_Final_Project';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// prepare the sql statement
$orderBy = "ORDER BY fldLastName";

$query  = "SELECT pmkUserId, fldFirstName, fldLastName, fldEmail, fldPassword, fldConfirmed ";
$query .= "FROM tblUsers ";
$query .= "WHERE fldConfirmed = 0";

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
print '<section id="listOfUsers">';
print "<table>\n";

foreach ($users as $user) {
   
    print "<tr>";
    if ($admin) {
        print '<td><a href="userUpdateForm.php?id=' . $user["pmkUserId"] . '">[Edit]</a></td> ';
    }
    print "<td>".$user['fldFirstName'] . "</td><td>   " . $user['fldLastName'] . "  </td><td>" . $user['fldEmail'] . "</td><td>  " . $user['fldPassword'] . "</td></tr>\n";
}
print "</table>\n";
print '</section>';
print "</article>";
include "include/footer.php";
?>
</body>
</html>





