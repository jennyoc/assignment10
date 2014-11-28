<?php
include 'include/top.php';
include 'include/editNav.php';

    $dbUserName = get_current_user() . '_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_Shelter';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
//SECTION: 1b Security
$yourURL = $domain.$phpSelf;

//SECTION: 1c form variables
if (isset($_GET["id"])) {
    $pmkDogId = htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");

    $query  = "SELECT pmkDogId, fldDogName, fldBreed, fldSize, fldAge, fldStage, fldCoat, fldHypo, fldColor, fldGender, fldChildren, fldShelterName ";
    $query .= "FROM tblDogs, tblShelters ";
    $query .= "WHERE pmkDogId =?";
    $query .= "AND tblDogs.fnkShelterId = tblShelters.pmkShelterId";


    $results = $thisDatabase->select($query, array($pmkDogId));

    $dogName = $results[0]["fldDogName"];
    $breed = $results[0]["fldBreed"];
    $size = $results[0]["fldSize"];
    $age = $results[0]["fldAge"];
    $stage = $results[0]["fldStage"];
    $coat = $results[0]["fldCoat"];
    $hypo = $results[0]["fldHypo"];
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
$hypoERROR = false;
$colorERROR = false;
$genderERROR = false;
$childrenERROR = false;
$shelterNameERROR = false;

//SECTION: 1e misc variables
$errorMsg = array();
$data = array();
$dataEntered = false;

//SECTION 2
if(isset($_POST["btnSubmit"])) {

//SECTION: 2a security
if(!securityCheck(true))  {
     $msg = "<p>Sorry you cannot access this page. ";
     $msg.= "Security breach detected and reported</p>";
     die($msg);
    }
}
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
    
    $hypo = htmlentities($_POST["chkHypo"], ENT_QUOTES, "UTF-8");
    
    $color = htmlentities($_POST["txtColor"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $color;
    
    $gender = htmlentities($_POST["radGender"], ENT_QUOTES, "UTF-8");
    
    $children = htmlentities($_POST["chkChildren"], ENT_QUOTES, "UTF-8");
    
    $shelterName = htmlentities($_POST["lstShelterName"], ENT_QUOTES, "UTF-8");
    
    //SECTION: 2c validation
    
    if ($dogName == "") {
        $errorMsg[] = "Please enter the dog's name";
        $dogNameERROR = true;
    } elseif (!verifyAlphaNum($dogName)) {
        $errorMsg[] = "The dog name appears to contain incorrect characters.";
        $dogNameERROR = true;
    }
    
    if ($breed == "") {
        $errorMsg[] = "Please enter the dog breed.";
        $breedERROR = true;
    } elseif (!verifyAlphaNum($breed)) {
        $errorMsg[] = "The dog name appears to contain incorrect characters.";
        $breedERROR = true;
    }
    if ($size == "") {
        $errorMsg[] = "Please select the dog's size.";
        $sizeERROR = true;
    }
    if ($age == "") {
        $errorMsg[] = "Please select the dog's age.";
        $ageERROR = true;
    }elseif (!verifyAlphaNum($age)) {
        $errorMsg[] = "The age appears to contain incorrect characters.";
        $ageERROR = true;
    }
    
    if ($stage == "") {
        $errorMsg[] = "Please select the dog's stage.";
        $stageERROR = true;
    }
    if ($coat == "") {
        $errorMsg[] = "Please select the dog's coat.";
        $coatERROR = true;
    }
    if ($color == "") {
        $errorMsg[] = "Please enter the dog's color.";
        $colorERROR = true;
    } elseif (!verifyAlphaNum($color)) {
        $errorMsg[] = "The dog color appears to contain incorrect characters.";
        $colorERROR = true;
    }
    if ($gender == "") {
        $errorMsg[] = "Please select the dog's gender.";
        $colorERROR = true;
    }
    if ($children == "") {
        $errorMsg[] = "Please select if the dog is ok with children.";
        $childrenERROR = true;
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
            $query .= 'tblDogs.fldHypo = ?, ';
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
    print $hypo;
    print $color;
    print $gender;
    print $children;
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
                    
                    <legend>Please complete the following form with the dogs information</legend>
                        <input type="hidden" id="hidDogId" name="hidDogId"
                       value="<?php print $pmkDogId; ?>"
                       >
                        <label for="txtDogName" class="required">Dog Name
                            <input type="text" id="txtDogName" name="txtDogName"
                                   value="<?php print $name; ?>"
                                   tabindex="120" maxlength="45" placeholder="Ex: <i>Murphey</i>"
                                   <?php if ($dogNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                        <label for="txtBreed" class="required">Breed Name
                            <input type="text" id="txtBreedName" name="txtBreedName"
                                   value="<?php print $breed; ?>"
                                   tabindex="120" maxlength="45" placeholder="Ex: <i>Bulldog</i>"
                                   <?php if ($breedERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>


                    <?php
                        $query = "SELECT DISTINCT fldSize ";
                        $query .= "FROM tblDogs ";
                        $query .= "ORDER BY tblDogs.fldSize DESC ";

                        $size = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstSize">Size: ';
                        $output[] = '<select id="lstSize" ';
                        $output[] = '        name="lstSize"';
                        $output[] = '        tabindex="150" >';


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
                        $query .= "ORDER BY tblDogs.fldStage DESC ";

                        $stage = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstStage" class="required">Stage: ';
                        $output[] = '<select id="lstStage" ';
                        $output[] = '        name="lstStage"';
                        $output[] = '        tabindex="150" >';


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
                        $query .= "ORDER BY tblDogs.fldCoat DESC ";

                        $coat = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstCoat" class="required">Coat: ';
                        $output[] = '<select id="lstCoat" ';
                        $output[] = '        name="lstCoat"';
                        $output[] = '        tabindex="150" >';


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
                    
                    <label for="chkHypo">
                        <input type="checkbox" id="chkHypo" 
                  name="chkHypo" 
                  value="Hypo"
                  <?php if ($hypo) print ' checked '; ?>
                  tabindex="420">Hypoallergenic</label>
                    
                    <label for="txtColor" class="required">Color
                            <input type="text" id="txtColor" name="txtColor"
                                   value="<?php print $color; ?>"
                                   tabindex="120" maxlength="45" placeholder="Enter the dogs color"
                                   <?php if ($colorERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                    <!--Gender radio buttons-->
                        <?php
                        $query = "SELECT DISTINCT fldGender ";
                        $query .= "FROM tblDogs ";

                        $gender = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<legend>Gender:</legend>';

                        foreach ($gender as $row) {

                            $output[] = '<label for="rad' . str_replace(" ", "-", $row["fldGender"]) . '"><input type="radio" ';
                            $output[] = ' id="rad' . str_replace(" ", "-", $row["fldGender"]) . '" ';
                            $output[] = ' name="radGender" ';

                            if ($gender == $row["pmkDogId"])
                                $output[] = " checked ";

                            $output[] = 'value="' . $row["pmkDogId"] . '">' . $row["fldGender"];
                            $output[] = '</label>';
                        }



                        print join("\n", $output);  // this prints each line as a separate  line in html 
                        ?>
                        <label for="chkChildren">
                        <input type="checkbox" id="chkChildren" 
                  name="chkChildren" 
                  value="Children"
                  <?php if ($children) print ' checked '; ?>
                  tabindex="420">Good with children?</label>
                    
                    <?php
                        $query = "SELECT DISTINCT fldShelterName ";
                        $query .= "FROM tblShelters ";

                        $shelterName = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstShelterName" class="required">Shelter Name: ';
                        $output[] = '<select id="lstShelterName" ';
                        $output[] = '        name="lstShelterName"';
                        $output[] = '        tabindex="150" >';


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
    