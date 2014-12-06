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
    $dbName = strtoupper(get_current_user()) . '_Final_Project';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// prepare the sql statement
$orderBy = "ORDER BY fldDogName";

$query  = "SELECT tblDogs.pmkDogId AS Admin, tblDogs.fldDogName AS Name, tblDogs.fldBreed AS Breed, tblDogs.fldSize AS Size, tblDogs.fldAge AS Age, tblDogs.fldStage AS Stage, tblDogs.fldCoat AS Coat, tblDogs.fldColor AS Coloring, tblDogs.fldGender AS Gender, tblDogs.fldChildren AS Children, tblShelters.fldShelterName AS Shelter ";
$query .= "FROM tblDogs, tblShelters ";
$query .= "WHERE tblDogs.fnkShelterId = tblShelters.pmkShelterId ";
$query .= "ORDER BY fldDogName";

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
$firstTime = true;
foreach ($dogs as $dog) {
    /*print "<tr>";
    if ($firstTime) {
    $keys = array_keys($dog);
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
        print '<td><a href="dogUpdateForm.php?id=' . $dog["Admin"] . '">[Edit]</a><a href="dogDelete.php?id=' . $dog["Admin"] . '">[Delete]</a></td> ';
    }
    print "<td>".$dog['Name'] . "</td><td>   " . $dog['Breed'] . "  </td><td>" . $dog['Size'] . "</td><td>  " . $dog['Age'] . "</td><td>  " . $dog['Stage'] . "</td><td>".$dog['Coat'] . "</td><td>".$dog['Coloring'] . "</td><td>".$dog['Gender'] . "</td><td>".$dog['Children'] . "</td><td>".$dog['Shelter'] . "</td></tr>\n";
}
print "</table>\n";
print '</section>';
print "</article>";
include "include/footer.php";
?>
</body>
</html>
