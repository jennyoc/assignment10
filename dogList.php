<?php
include "include/top.php";
include "include/editNav.php";

/* %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
 * the purpose of this page is to display a list of users sorted
 */
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^
$debug = false;
if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}
if ($debug)
    print "<p>DEBUG MODE IS ON</p>";


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
$yourURL = $domain . $phpSelf;
$admin = true;
?>

<?php

print "<article>";
$dbUserName = get_current_user() . '_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_Shelter';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// prepare the sql statement
$orderBy = "ORDER BY fldDogName";

$query  = "SELECT pmkDogId, fldDogName, fldBreed, fldSize, fldAge, fldStage, fldCoat, fldHypo, fldColor, fldGender, fldChildren, fldShelterName ";
$query .= "FROM tblDogs, tblShelters ";
//$query .= "WHERE tblDogs.fnkShelterId = tblShelters.pmkShelterId ";

if ($debug)
    print "<p>sql " . $query;

$dogs = $thisDatabase->select($query);

if ($debug) {
    print "<pre>";
    print_r($dogs);
    print "</pre>";
}
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// print out the results
print '<section id="listOfDogs">';
print "<table>\n";

foreach ($dogs as $dog) {
   
    print "<tr>";
    if ($admin) {
        print '<td><a href="dogUpdateForm.php?id=' . $dog["pmkDogId"] . '">[Edit]</a></td> ';
    }
    print "<td>".$dog['fldDogName'] . "</td><td>   " . $dog['fldBreed'] . "  </td><td>" . $dog['fldSize'] . "</td><td>  " . $dog['fldAge'] . "</td><td>".$dog['fldCoat'] . "</td><td>".$dog['fldHypo'] . "</td><td>".$dog['fldColor'] . "</td><td>".$dog['fldGender'] . "</td><td>".$dog['fldChildren'] . "</td><td>".$dog['fldShelterName'] . "</td></tr>\n";
}
print "</table>\n";
print '</section>';
print "</article>";
include "include/footer.php";
?>
</body>
</html>






