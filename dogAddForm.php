<?php
include 'include/top.php';
include 'include/editNav.php';

$dbUserName = get_current_user() . '_admin';
$whichPass = "a"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_Final_Project';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// variables for the classroom purposes to help find errors.
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

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variable
// Initialize variables one for each form element
// in the order they appear on the form
$dogName = "";
$breed = "";
$size = "";
$age = "";
$stage = "";
$coat = "";
$color = "";
$gender = "";
$children = "";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
$dogNameERROR = false;
$breedERROR = false;
$sizeERROR = false;
$ageERROR = false;
$stageERROR = false;
$coatERROR = false;
$colorERROR = false;
$genderERROR = false;
$childrenERROR = false;



//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();
$dataRecord = array();
$dataEntered = false;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
if (isset($_POST["btnSubmit"])) {
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2a Security
//
    if (!securityCheck(true)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported</p>";
        die($msg);
    }

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2b Sanitize (clean) data
// remove any potential JavaScript or html code from users input on the
// form. Note it is best to follow the same order as declared in section 1c.
    $pmkDogId = htmlentities($_POST["hidDogId"], ENT_QUOTES, "UTF-8");

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

    $gender = htmlentities($_POST["radGender"], ENT_QUOTES, "UTF-8");

    $children = htmlentities($_POST["chkChildren"], ENT_QUOTES, "UTF-8");



//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
// SECTION: 2c Validation
    // Validation section
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
    } elseif (!verifyAlphaNum($age)) {
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
        $genderERROR = true;
    }
    if ($children == "") {
        $errorMsg[] = "Please select if the dog is ok with children.";
        $childrenERROR = true;
    }

// SECTION: 2d Process Form - Passed Validation
//
// Process for when the form passes validation (the errorMsg array is empty)
//
    if (!$errorMsg) {
        if ($debug)
            print "<p>Form is valid</p>";

        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        // SECTION: 2e Save Data

        $primaryKey = "";
        $dataEntered = false;
        try {
            $thisDatabase->db->beginTransaction();
            $query = 'INSERT INTO tblDogs(fldDogName, fldBreed, fldSize, fldAge, fldStage, fldCoat, fldColor, fldGender, fldChildren) VALUES (?,?,?,?,?,?,?,?,?,?)';
            $data = array($dogName, $breed, $size, $age, $stage, $coat, $color, $gender, $children);

            if ($debug) {
                print "<p>sql " . $query;
                print"<p><pre>";
                print_r($data);
                print"</pre></p>";
            }
            $results = $thisDatabase->insert($query, $data);

            $primaryKey = $thisDatabase->lastInsert();
            if ($debug)
                print "<p>pmk= " . $primaryKey;

// all sql statements are done so lets commit to our changes
            $dataEntered = $thisDatabase->db->commit();
            $dataEntered = true;
            if ($debug)
                print "<p>transaction complete ";
        } catch (PDOException $e) {
            $thisDatabase->db->rollback();
            if ($debug)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem accepting your data please contact us directly.";
        }


        // If the transaction was successful, give success message
        if ($dataEntered) {
            if ($debug)
                print "<p>data entered now prepare keys ";
            //#################################################################
            //#################################################################
            //Put forms information into a variable to print on the screen
            //

            $messageA = '<h2>Thank you for adding a dog to our database!</h2>';
            $messageB = "<p>This is the information you have added:  ";
            $messageB .= "Dog Name:  .'$dogName'";
            $messageB .= "Breed:  .'$breed'";
            $messageB .= "Size:  .'$size'";
            $messageB .= "Age:  .'$age'";
            $messageB .= "Stage:  .'$stage'";
            $messageB .= "Coat:  .'$coat'";
            $messageB .= "Color:  .'$color'";
            $messageB .= "Gender:  .'$gender'";
            $messageB .= "Good with children:  .'$children'</p>";
            $messageC = '<p>If you have an image of the dog please <a href="jmagie.w3.uvm.edu/cs148/assignment10/about-us.php>contact us</a> and we will post a photo of the dog to our gallery page.</p>';

            //##############################################################
        } //data entered
    } // end form is valid
} // ends if form was submitted.
//#############################################################################
//
// SECTION 3 Display Form
//
?>

<!-- ######################     Article Section   ############################## -->
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
if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
    print "<h3>Your Request has ";
    if (!$dataEntered) {
        print "not ";
    }
    print "been processed</h3>";
    print $messageA . $messageB . $messageC;
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
              id="frmAdd">
            <fieldset class="wrapper">
                <legend>Add a dog today!</legend>
                <fieldset class="wrapperTwo">
                    <legend>Please complete the following form with the dogs information</legend>
                    <label for="txtDogName" class="required">Dog Name
                        <input type="text" id="txtDogName" name="txtDogName"
                               value="<?php print $dogName; ?>"
                               tabindex="120" maxlength="45" placeholder="Ex: <i>Murphey</i>"
    <?php if ($dogNameERROR) print 'class="mistake"'; ?>
                               onfocus="this.select()"
                               >
                    </label>
                    <label for="txtBreed" class="required">Breed Name
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
    $query = "SELECT pmkDogId, fldGender ";
    $query .= "FROM tblDogs ";
    $query .= "GROUP BY fldGender ";

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






                </fieldset> <!-- ends contact -->
            </fieldset> <!-- ends wrapper Two -->
            <fieldset class="buttons">
                <legend></legend>
                <input type="submit" id="btnSubmit" name="btnSubmit" value="Add Dog" tabindex="900" class="button">
                <input type="reset" id="btnReset" value="Reset" tabindex="901" class="button">
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