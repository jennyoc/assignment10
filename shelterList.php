<?php
/* %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
 * the purpose of this page is to display a list of shelters sorted
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
$orderBy = "ORDER BY fldShelterName";

$query  = "SELECT pmkShelterId, fldShelterName, fldAddress, fldCity, fldState, fldZip, fldPhone ";
$query .= "FROM tblShelters " . $orderBy;

if ($debug)
    print "<p>sql " . $query;

$shelters = $thisDatabase->select($query);

if ($debug) {
    print "<pre>";
    print_r($shelters);
    print "</pre>";
}

// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// print out the results
print '<section id="listOfShelters">';
print "<table>\n";

foreach ($shelters as $shelter) {
   
    print "<tr>";
    if ($admin) {
        print '<td><a href="shelterUpdateForm.php?id=' . $shelter["pmkShelterId"] . '">[Edit]</a></td> ';
    }
    print "<td>".$shelter['fldShelterName'] . "</td><td>   " . $shelter['fldAddress'] . "  </td><td>" . $shelter['fldCity'] . "</td><td>  " . $shelter['fldState'] . "</td><td>" . $shelter['fldZip'] . "</td><td>" . $shelter['fldPhone'] . "</td></tr>\n";
}
print "</table>\n";
print '</section>';
print "</article>";
include "include/footer.php";
?>
</body>
</html>
