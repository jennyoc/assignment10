<?php
include 'include/top.php';
include 'include/editNav.php';

$dbUserName = get_current_user() . '_admin';
$whichPass = "a"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_Final_Project';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

$update = true;
$debug = false;

// SECTION: 1a.
if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}
if ($debug)
    print "<p>DEBUG MODE IS ON</p>";

$errorMsg = array();
$data = array();
$datEntered = false;

//SECTION: 1b Security
$yourURL = $domain . $phpSelf;

//SECTION: 1c form variables
if (isset($_GET["id"])) {
    $pmkDogId = htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");
    $data[] = $pmkDogId;

    $query = 'SELECT fnkShelterId, fldDogName, fldBreed, fldSize, fldAge, fldStage, fldCoat, fldColor, fldGender, fldChildren ';
    $query .= 'FROM tblDogs WHERE pmkDogId = ? ';

    $results = $thisDatabase->select($query, $data);

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

//SECTION: 1d initialize error flags
    $shelterIdERROR = false;
    $dogNameERROR = false;
    $breedERROR = false;
    $sizeERROR = false;
    $ageERROR = false;
    $stageERROR = false;
    $coatERROR = false;
    $colorERROR = false;
    $genderERROR = false;
    $childrenERROR = false;
}

//SECTION 2
if (isset($_POST["btnSubmit"])) {

//SECTION: 2a security
    /* if(!securityCheck(true))  {
      $msg = "<p>Sorry you cannot access this page. ";
      $msg.= "Security breach detected and reported</p>";
      die($msg);
      }
     */


//SECTION: 2b sanitize data
    $pmkDogId = htmlentities($_POST["hidDogId"], ENT_QUOTES, "UTF-8");

    if ($pmkDogId > 0) {
        $update = true;
    }

    $shelterId = htmlentities($_POST["lstShelterId"], ENT_QUOTES, "UTF-8");
    $dogName = htmlentities($_POST["txtDogName"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $dogName;

    $breed = htmlentities($_POST["txtBreed"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $breed;

    $size = htmlentities($_POST["lstSize"], ENT_QUOTES, "UTF-8");
    
    $age = htmlentities($_POST["txtAge"], ENT_QUOTES, "UTF-8");

    $stage = htmlentities($_POST["lstStage"], ENT_QUOTES, "UTF-8");

    $coat = htmlentities($_POST["lstCoat"], ENT_QUOTES, "UTF-8");

    $color = htmlentities($_POST["txtColor"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $color; 
    
    $gender = htmlentities($_POST["lstGender"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $gender;

    $children = htmlentities($_POST["lstChildren"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $children;


    //SECTION: 2c validation

    if ($shelterId == "") {
        $errorMsg[] = "Please select the shelter where this dog is located.";
        $shelterIdERROR = true;
    }

    if ($dogName == "") {
        $errorMsg[] = "Please enter the dog's name";
        $dogNameERROR = true;
    }

    if ($age == "") {
        $ageERROR = false;
    } elseif (!verifyAlphaNum($age)) {
        $errorMsg[] = "The age appears to contain incorrect characters.";
        $ageERROR = true;
    }

    if ($gender == "") {
        $errorMsg[] = "Please select the dog's gender.";
        $genderERROR = true;
    }



//SECTION: 2d process form - passed validation
    if (!$errorMsg) {
        if ($debug) {
            print "<p>Form is valid</p>";
        }

        //SECTION: 2e Save Data

        $dataEntered = false;
        try {
            $thisDatabase->db->beginTransaction();

            $query = 'UPDATE tblDogs SET ';

            $query .= 'fnkShelterId = ?, ';
            $query .= 'fldDogName = ?, ';
            $query .= 'fldBreed = ?, ';
            $query .= 'fldSize = ?, ';
            $query .= 'fldAge = ?, ';
            $query .= 'fldStage = ?, ';
            $query .= 'fldCoat = ?, ';
            $query .= 'fldColor = ?, ';
            $query .= 'fldGender = ?, ';
            $query .= 'fldChildren = ? ';
            $query .= 'WHERE pmkDogId = ? ';
            $data[] = $pmkDogId;
            print_r($data);
            $results = $thisDatabase->update($query, $data);

            $dataEntered = $thisDatabase->db->commit();

            if ($debug)
                print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            if ($debug)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
        }  
    }
}
//SECTION 3 Display Form
?>
<article id="main">
    <?php
//####################################
//
// SECTION 3a.
//
//
//
//
// If its the first time coming to the form or there are errors we are going
// to display the form.
    if ($dataEntered) { // closing of if marked with: end body submit
        print "<h1>Record Saved</h1> ";
        print "Shelter Id: " . $shelterId . "<br>";
        print "Name: " . $dogName . "<br>";
        print "Breed: " . $breed . "<br>";
        print "Size: " . $size . "<br>";
        print "Age: " . $age . "<br>";
        print "Stage: " . $stage . "<br>";
        print "Coat: " . $coat . "<br>";
        print "Color: " . $color . "<br>";
        print "Gender: " . $gender . "<br>";
        print "Children: " . $children . "<br>";
    } else {
//####################################
//
// SECTION 3b Error Messages
//
// display any error messages before we print out the form
        if ($errorMsg) {
            print '<div id="errors">';
            print "<ol>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ol>\n";
            print '</div>';
        }
//####################################
//
// SECTION 3c html Form
        ?>
        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmUpdate">
            <fieldset class="wrapper">
                <legend>Update a current member profile.</legend>
                <fieldset class="wrapperTwo">

                    <legend>Please complete the following form with the dogs information.<br> * denotes a required field.</legend>
                    <input type="hidden" id="hidDogId" name="hidDogId"
                           value="<?php print $pmkDogId; ?>"
                           >

                    <?php
                        $query = "SELECT DISTINCT fnkShelterId, fldShelterName ";
                        $query .=" FROM tblDogs, tblShelters ";
                        $query .="WHERE tblDogs.fnkShelterId = tblShelters.pmkShelterId ";
 
                        $shelterId = $thisDatabase->select($query);
                        

                        $output = array();
                        $output[] = '<label for="lstShelterId" class="required">*Shelter Name: ';
                        $output[] = '<select id="lstShelterId" ';
                        $output[] = '        name="lstShelterId"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled">Select Shelter...</option>';
                        


                        foreach ($shelterId as $row) {

                            $output[] = '<option ';
                            if ($shelterId == $row["fnkShelterId"])
                                $output[] = ' selected ';

                            $output[] = 'value="' . $row["fnkShelterId"] . '">' . $row["fldShelterName"];

                            $output[] = '</option>';
                        }

                        $output[] = '</select></label>';

                        print join("\n", $output);  // this prints each line as a separate  line in html
                        ?>
                    
                        <label for="txtDogName" class="required">*Dog Name
                            <input type="text" id="txtDogName" name="txtDogName"
                                   value="<?php print $dogName; ?>"
                                   tabindex="120" maxlength="45" placeholder="Ex: Murphey"
                                   <?php if ($dogNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                        <label for="txtBreed">Breed Name
                            <input type="text" id="txtBreed" name="txtBreed"
                                   value="<?php print $breed; ?>"
                                   tabindex="120" maxlength="45" placeholder="Ex: Bulldog"
                                   <?php if ($breedERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>


                    <?php                 
                        $query = "SELECT DISTINCT fldSize,fldSizeId ";
                        $query .= "FROM tblDogs ";
                        $query .= "WHERE fldSize IS NOT NULL ";
                        $query .= "ORDER BY fldSizeId";

                        $size = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstSize">Size: ';
                        $output[] = '<select id="lstSize" ';
                        $output[] = '        name="lstSize"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled">Select dog size...</option>';


                        foreach ($size as $row) {

                            $output[] = '<option ';
                            if ($size == $row["fldSize"])
                                $output[] = ' selected ';

                            $output[] = 'value="' . $row["fldSizeId"] . '">' . $row["fldSize"];

                            $output[] = '</option>';
                        }

                        $output[] = '</select></label>';

                        print join("\n", $output);  // this prints each line as a separate  line in html
                        ?>
                        
                         <label for="txtAge" class="required">Age
                            <input type="text" id="txtAge" name="txtAge"
                                   value="<?php print $age; ?>"
                                   tabindex="120" maxlength="45" placeholder="Enter the dogs age"
                                   <?php if ($ageERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                    <?php
                        $query = "SELECT DISTINCT fldStage ";
                        $query .= "FROM tblDogs ";
                        $query .= "WHERE fldStage IS NOT NULL ";

                        $stage = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstStage" class="required">Stage: ';
                        $output[] = '<select id="lstStage" ';
                        $output[] = '        name="lstStage"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled">Select dog stage...</option>';


                        foreach ($stage as $row) {

                            $output[] = '<option ';
                            if ($stage == $row["fldStage"])
                                $output[] = ' selected ';

                            $output[] = 'value="' . $row["fldStage"] . '">' . $row["fldStage"];

                            $output[] = '</option>';
                        }

                        $output[] = '</select></label>';

                        print join("\n", $output);  // this prints each line as a separate  line in html
                        ?>
                        
                        <?php
                        $query = "SELECT DISTINCT fldCoat ";
                        $query .= "FROM tblDogs ";
                        $query .= "WHERE fldCoat IS NOT NULL ";

                        $coat = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstCoat" class="required">Coat: ';
                        $output[] = '<select id="lstCoat" ';
                        $output[] = '        name="lstCoat"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled">Select type of coat...</option>';


                        foreach ($coat as $row) {

                            $output[] = '<option ';
                            if ($coat == $row["fldCoat"])
                                $output[] = ' selected ';

                            $output[] = 'value="' . $row["fldCoat"] . '">' . $row["fldCoat"];

                            $output[] = '</option>';
                        }

                        $output[] = '</select></label>';

                        print join("\n", $output);  // this prints each line as a separate  line in html
                        ?>
                   
                    
                    <label for="txtColor">Color
                            <input type="text" id="txtColor" name="txtColor"
                                   value="<?php print $color; ?>"
                                   tabindex="120" maxlength="45" placeholder="Enter the dogs color"
                                   <?php if ($colorERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                    
                    <?php
                        $query = "SELECT DISTINCT fldGender,fldGenderId ";
                        $query .= "FROM tblDogs ";

                        $gender = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstGender" class="required">*Gender: ';
                        $output[] = '<select id="lstGender" ';
                        $output[] = '        name="lstGender"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled">Select a gender:</option>';


                        foreach ($gender as $row) {

                            $output[] = '<option ';
                            if ($gender == $row["fldGender"])
                                $output[] = ' selected ';

                            $output[] = 'value="' . $row["fldGenderId"] . '">' . $row["fldGender"];

                            $output[] = '</option>';
                        }

                        $output[] = '</select></label>';

                        print join("\n", $output);  // this prints each line as a separate  line in html
                        ?>
                    
                        <?php
                        $query = "SELECT DISTINCT fldChildren ";
                        $query .= "FROM tblDogs ";
                        $query .= "WHERE fldChildren IS NOT NULL ";

                        $children = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstChildren">Good with Children?: ';
                        $output[] = '<select id="lstChildren" ';
                        $output[] = '        name="lstChildren"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled">Not Applicable</option>';

                        
                        foreach ($children as $row) {

                            $output[] = '<option ';
                            if ($children == $row["fldChildren"])
                                $output[] = ' selected ';

                            $output[] = 'value="' . $row["fldChildren"] . '">' . $row["fldChildren"];

                            $output[] = '</option>';
                        }

                        $output[] = '</select></label>';

                        print join("\n", $output);  // this prints each line as a separate  line in html
                        ?>

                </fieldset>



            </fieldset> <!-- ends contact -->
            <fieldset class="buttons">
                <legend></legend>
                <input type="submit" id="btnSubmit" name="btnSubmit" value="Update Dog" tabindex="900" class="button">

            </fieldset> <!-- ends buttons -->
        </form>
        <?php
    } // end body submit
    ?>
</article>

<?php
include "include/footer.php";
if ($debug)
    print "<p>END OF PROCESSING</p>";
?>
</body>
</html>
