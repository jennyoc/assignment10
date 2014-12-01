<?php

/* %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
 * the purpose of this page is to display a list of users sorted
 */
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^
include "include/top.php";
include "include/editNav.php";
?>
<?php

$dbUserName = get_current_user() . '_admin';
$whichPass = "a"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_Shelter';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

$admin = true;
$delete = false;
$errorMsg = array();

$debug = true;
// SECTION: 1a.
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

if (isset($_GET["id"])) {
    $pmkShelterId = htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");
    $query = 'SELECT fldShelterName, fldAddress, fldCity, fldZip, fldPhone ';
    $query .= 'FROM tblShelters WHERE pmkShelterId = ?';

    $results = $thisDatabase->select($query, array($pmkShelterId));

    $shelterName = $results[0]["fldShelterName"];
    $address = $results[0]["fldAddress"];
    $city = $results[0]["fldCity"];
    $zip = $results[0]["fldZip"];
    $phone = $results[0]["fldPhone"];

    if ($pmkShelterId > 0) {
        $delete = true;
    }

    $dataDeleted = false;
    try {
        $thisDatabase->db->beginTransaction();
        if ($delete) {
            $query = "DELETE FROM tblShelters ";
            $query .= "WHERE pmkShelterId = '" . $pmkShelterId . "'";
            $results = $thisDatabase->delete($query, array($pmkShelterId));
            $dataDeleted = $thisDatabase->db->commit();
        }
        if ($debug)
            print "<p>transaction complete ";
    } catch (Exception $ex) {
        $thisDatabase->db->rollback();
        if ($debug)
            print "Error!: " . $e->getMessage() . "</br>";
        $errorMsg[] = "There was a problem deleting the data, please contact us directly.";
    }
}
?>
<article id="main">
    <?php 
if ($dataDeleted) {
    print "<h2>The recorded was successfully delete</h2>";
}else{
        if ($errorMsg) {
            print '<div id="errors">';
            print "<ol>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ol>\n";
            print '</div>';
        }
        
    }
    ?>
</article>    
<?php
include "include/footer.php";
if ($debug)
    print "<p>END OF PROCESSING</p>";
?>
</body>
</html>
   
