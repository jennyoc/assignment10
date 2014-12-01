<?php

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
    $pmkDogId = htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");
    $query = 'SELECT fnkShelterId, fldDogName, fldBreed, fldSize, fldAge, fldStage, fldCoat, fldColor, fldGender, fldChildren ';
    $query .= 'FROM tblDogs WHERE pmkDogId = ? ';

    $results = $thisDatabase->select($query, array($pmkDogId));

    $shelterId = $results[0]["fnkShelterId"];
    $dogName = $results[0]["fldDogName"];
    $breed = $results[0]["fldBreed"];
    $size = $results[0]["fldSize"];
    $sizeId = $results[0]["fldSizeId"];
    $age = $results[0]["fldAge"];
    $stage = $results[0]["fldStage"];
    $coat = $results[0]["fldCoat"];
    $color = $results[0]["fldColor"];
    $gender = $results[0]["fldGender"];
    $genderId = $results[0]["fldGenderId"];
    $children = $results[0]["fldChildren"];
    $childrenId = $results[0]["fldChildrenId"];


    if ($pmkDogId > 0) {
        $delete = true;
    }

    $dataDeleted = false;
    try {
        $thisDatabase->db->beginTransaction();
        if ($delete) {
            $query = "DELETE FROM tblDogs ";
            $query .= "WHERE pmkDogId = '" . $pmkDogId . "'";
            $results = $thisDatabase->delete($query, array($pmkDogId));
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
   
