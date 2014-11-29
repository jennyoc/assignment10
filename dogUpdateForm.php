<?php
include 'include/top.php';
include 'include/editNav.php';

    $dbUserName = get_current_user() . '_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_Shelter';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
$update = true;
$debug= false;
// SECTION: 1a.
// $debug = true;
if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}
if ($debug)
    print "<p>DEBUG MODE IS ON</p>";

$errorMsg = array();
$data = array();

//SECTION: 1b Security
$yourURL = $domain.$phpSelf;

//SECTION: 1c form variables
if (isset($_GET["id"])) {
    $pmkDogId = htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");

    $query  = "SELECT fldDogName, fldBreed, fldSize, fldAge, fldStage, fldCoat, fldColor, fldGender, fldChildren, fldShelterName ";
    $query .= "FROM tblDogs, tblShelters ";
    $query .= "WHERE pmkDogId =? ";
    $query .= "AND tblDogs.fnkShelterId = tblShelters.pmkShelterId";


    $results = $thisDatabase->select($query, array($pmkDogId));

    $dogName = $results[0]["fldDogName"];
    $breed = $results[0]["fldBreed"];
    $size = $results[0]["fldSize"];
    $age = $results[0]["fldAge"];
    $stage = $results[0]["fldStage"];
    $coat = $results[0]["fldCoat"];
    $color = $results[0]["fldColor"];
    $gender = $results[0]["fldGender"];
    $children = $results[0]["fldChildren"];
    $shelterName = $results[0]["fldShelterName"];

//SECTION: 1d initialize error flags
$dogNameERROR = false;
$breedERROR = false;
$sizeERROR = false;
$ageERROR = false;
$stageERROR = false;
$coatERROR = false;
$colorERROR = false;
$genderERROR = false;
$childrenERROR = false;
$shelterNameERROR = false;
}

//SECTION 2
if(isset($_POST["btnSubmit"])) {

//SECTION: 2a security
/*if(!securityCheck(true))  {
     $msg = "<p>Sorry you cannot access this page. ";
     $msg.= "Security breach detected and reported</p>";
     die($msg);
    }
  */
 

//SECTION: 2b sanitize data
    $dogName = htmlentities($_POST["txtDogName"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $dogName;
    
    $breed = htmlentities($_POST["txtBreed"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $breed;
    
    $size = htmlentities($_POST["lstSize"], ENT_QUOTES, "UTF-8");
    
    $age = htmlentities($_POST["txtAge"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $age;
    
    $stage = htmlentities($_POST["lstStage"], ENT_QUOTES, "UTF-8");
    
    $coat = htmlentities($_POST["lstCoat"], ENT_QUOTES, "UTF-8");
    
    $color = htmlentities($_POST["txtColor"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $color;
    
    $gender = htmlentities($_POST["lstGender"], ENT_QUOTES, "UTF-8");
    
    $children = htmlentities($_POST["lstChildren"], ENT_QUOTES, "UTF-8");
    
    $shelterName = htmlentities($_POST["lstShelterName"], ENT_QUOTES, "UTF-8");
    
    //SECTION: 2c validation
    
    if ($dogName == "") {
        $errorMsg[] = "Please enter the dog's name";
        $dogNameERROR = true;
    }
    
    if ($age == "") {
        $ageERROR = false;
    }elseif (!verifyAlphaNum($age)) {
        $errorMsg[] = "The age appears to contain incorrect characters.";
        $ageERROR = true;
    }
    
    if ($gender == "") {
        $errorMsg[] = "Please select the dog's gender.";
        $colorERROR = true;
    }

    if ($shelterName == "") {
        $errorMsg[] = "Please select the shelter where this dog is located.";
        $shelterNameERROR = true;
    }
    
//SECTION: 2d process form - passed validation
    if (!$errorMsg) {
        if ($debug){
            print "<p>Form is valid</p>";
        }
    
    //SECTION: 2e Save Data
        
        $dataEntered = false;
        try{
            $thisDatabase->db->beginTransaction();
            
            $query = 'UPDATE tblDogs, tblShelters SET ';
            $query .= 'tblDogs.fldDogName = ?, ';
            $query .= 'tblDogs.fldBreed = ?, ';
            $query .= 'tblDogs.fldSize = ?, ';
            $query .= 'tblDogs.fldAge = ?, ';
            $query .= 'tblDogs.fldStage = ?, ';
            $query .= 'tblDogs.fldCoat = ?, ';
            $query .= 'tblDogs.fldColor = ?, ';
            $query .= 'tblDogs.fldGender = ?, ';
            $query .= 'tblDogs.fldChildren = ?, ';
            $query .= 'tblShelters.fldShelterName = ? ';
            $query .= 'WHERE tblDogs.fnkShelterId = tblShelters.pmkShelterId ';
            $query .= 'AND tblDogs.pmkDogId = ? ';
            $data[] = $pmkDogId;
            print_r($data);
            $results = $thisDatabase->update($query, $data);
            
            $dataEntered = $thisDatabase->db->commit();
            
            if($debug)
                print "transaction complete.";                    
        }catch (PDOException $e) {
            $thisDatabase->db->rollback();
            if($debug)
                print "Error!: ".$e->getMessage(). "</br";
                $errorMsg[] = "There was a problem accepting your data, please contact us directly."; 
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
if ($dataEntered){ // closing of if marked with: end body submit
    print "<h1>Record Saved</h1> ";
    print $pmkDogId;
    print $dogName;
    print $breed;
    print $size;
    print $age;
    print $stage;
    print $coat;
    print $color;
    print $gender;
    print $children;
    print $shelterName;
    print $fnkShelterId;
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
                        <label for="txtDogName" class="required">*Dog Name
                            <input type="text" id="txtDogName" name="txtDogName"
                                   value="<?php print $dogName; ?>"
                                   tabindex="120" maxlength="45" placeholder="Ex: <i>Murphey</i>"
                                   <?php if ($dogNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                        <label for="txtBreed">Breed Name
                            <input type="text" id="txtBreed" name="txtBreed"
                                   value="<?php print $breed; ?>"
                                   tabindex="120" maxlength="45" placeholder="Ex: <i>Bulldog</i>"
                                   <?php if ($breedERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>


                    <?php
                        $query = "SELECT DISTINCT fldSize ";
                        $query .= "FROM tblDogs ";

                        $size = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstSize">Size: ';
                        $output[] = '<select id="lstSize" ';
                        $output[] = '        name="lstSize"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled" selected="selected">Size:</option>';


                        foreach ($size as $row) {

                            $output[] = '<option ';
                            if ($size == $row["fldSize"])
                                $output[] = ' selected ';

                            $output[] = 'value="' . $row["fldSize"] . '">' . $row["fldSize"];

                            $output[] = '</option>';
                        }

                        $output[] = '</select></label>';

                        print join("\n", $output);  // this prints each line as a separate  line in html
                        ?>
                        
                         <label for="txtAge">Age
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

                        $stage = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstStage">Stage: ';
                        $output[] = '<select id="lstStage" ';
                        $output[] = '        name="lstStage"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled" selected="selected">Stage:</option>';


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

                        $coat = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstCoat">Coat: ';
                        $output[] = '<select id="lstCoat" ';
                        $output[] = '        name="lstCoat"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled" selected="selected">Coat:</option>';


                        foreach ($size as $row) {

                            $output[] = '<option ';
                            if ($size == $row["fldCoat"])
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
                        $query = "SELECT DISTINCT fldGender ";
                        $query .= "FROM tblDogs ";

                        $gender = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstGender" class="required">*Gender: ';
                        $output[] = '<select id="lstGender" ';
                        $output[] = '        name="lstGender"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled" selected="selected">Gender:</option>';


                        foreach ($gender as $row) {

                            $output[] = '<option ';
                            if ($gender == $row["fldGender"])
                                $output[] = ' selected ';

                            $output[] = 'value="' . $row["fldGender"] . '">' . $row["fldGender"];

                            $output[] = '</option>';
                        }

                        $output[] = '</select></label>';

                        print join("\n", $output);  // this prints each line as a separate  line in html
                        ?>
                        
                        <?php
                        $query = "SELECT DISTINCT fldChildren ";
                        $query .= "FROM tblDogs ";

                        $children = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstChildren">Good with Children?: ';
                        $output[] = '<select id="lstChildren" ';
                        $output[] = '        name="lstChildren"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled" selected="selected">Not Applicable</option>';

                        
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
                    
                    <?php
                        $query = "SELECT DISTINCT fldShelterName ";
                        $query .= "FROM tblShelters ";

                        $shelterName = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstShelterName" class="required">*Shelter Name: ';
                        $output[] = '<select id="lstShelterName" ';
                        $output[] = '        name="lstShelterName"';
                        $output[] = '        tabindex="150" >';
                        $output[] = '<option disabled="disabled" selected="selected">Shelter Name:</option>';


                        foreach ($shelterName as $row) {

                            $output[] = '<option ';
                            if ($shelterName == $row["fldShelterName"])
                                $output[] = ' selected ';

                            $output[] = 'value="' . $row["fldShelterName"] . '">' . $row["fldShelterName"];

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
    