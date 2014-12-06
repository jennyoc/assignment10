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

$query  = "SELECT pmkShelterId as Admin, fldShelterName as Shelter, fldAddress AS Address, fldCity AS City, fldZip AS Zip, fldPhone AS Phone ";
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
$firstTime = true;
foreach ($shelters as $shelter) {
    /*print "<tr>";
    if ($firstTime) {
    $keys = array_keys($shelter);
    foreach ($keys as $key) {
                if (!is_int($key)) {
                    print "<th>" . $key . "</th>";
                }
            }
    print "</tr>"; 
    $firstTime = false;
    }
     * 
     */
   
    print "<tr>";
    if ($admin) {
        print '<td><a href="shelterUpdateForm.php?id=' . $shelter["Admin"] . '">[Edit]</a><a href="shelterDelete.php?id=' . $shelter["Admin"] . '">[Delete]</a></td> ';
    }
    print "<td>".$shelter['Shelter'] . "</td><td>   " . $shelter['Address'] . "  </td><td>" . $shelter['City'] . "</td><td>" . $shelter['Zip'] . "</td><td>" . $shelter['Phone'] . "</td></tr>\n";
}
print "</table>\n";
print '</section>';
print "</article>";
include "include/footer.php";
?>
</body>
</html>