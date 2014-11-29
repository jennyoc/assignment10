<?php
/* the purpose of this page is to display a form to allow us
 * to add a new shelter
 */

include "include/top.php";
include "include/editNav.php";

$dbUserName = get_current_user() . '_admin';
$whichPass = "a"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_Final_Project';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
// SECTION: 1a.
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
$yourURL = $domain . $phpSelf;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form
//$pmkShelterId = "";
$shelterName = "";
$address = "";
$city = "";
$state = "";
$zip = "";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$shelterNameERROR = false;
$addressERROR = false;
$cityERROR = false;
$stateERROR = false;
$zipERROR = false;
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();
$data = array();
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
    $pmkShelterId = htmlentities($_POST["hidShelterId"], ENT_QUOTES, "UTF-8");

    $shelterName = htmlentities($_POST["txtShelterName"], ENT_QUOTES, "UTF-8");
    $data[] = $shelterName;

    $address = htmlentities($_POST["txtAddress"], ENT_QUOTES, "UTF-8");
    $data[] = $address;

    $city = htmlentities($_POST["txtCity"], ENT_QUOTES, "UTF-8");
    $data[] = $city;

    $state = htmlentities($_POST["txtState"], ENT_QUOTES, "UTF-8");
    $data[] = $state;

    $zip = (int) htmlentities($_POST["txtZip"], ENT_QUOTES, "UTF-8");
    $data[] = $zip;
    
    $phone = (int) htmlentities($_POST["txtPhone"], ENT_QUOTES, "UTF-8");
    $data[] = $phone;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2c Validation
//


    if ($shelterName == "") {
        $errorMsg[] = "Please enter new shelter name";
        $shelterNameERROR = true;
    } elseif (!verifyAlphaNum($shelterName)) {
        $errorMsg[] = "The name appears to have extra character.";
        $shelterName = true;
    }

    if ($address == "") {
        $errorMsg[] = "Please enter a new address";
        $addressERROR = true;
    }


    if ($city == "") {
        $errorMsg[] = "Please enter a new city";
        $city = true;
    } elseif (!verifyAlphaNum($city)) {
        $errorMsg[] = "The city appears to have extra character.";
        $cityERROR = true;
    }

    if ($state == "") {
        $errorMsg[] = "Please enter a new state";
        $stateERROR = true;
    } elseif (!verifyAlphaNum($state)) {
        $errorMsg[] = "The state appears to have extra character.";
        $state = true;
    }

    if ($zip == "") {
        $errorMsg[] = "Please enter a new zip code";
        $zipERROR = true;
    } elseif (!verifyNumeric($zip)) {
        $errorMsg[] = "The zip code appears to have extra character.";
        $zipERROR = true;
    }
    
    if ($phone == "") {
        $errorMsg[] = "Please enter a phone number";
        $phoneERROR = true;
    } elseif (!verifyPhone($phone)) {
        $errorMsg[] = "The phone number appears to have extra character.";
        $phoneERROR = true;
    }

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2d Process Form - Passed Validation
//
// Process for when the form passes validation (the errorMsg array is empty)
//
    if (!$errorMsg) {
        if ($debug) {
            print "<p>Form is valid</p>";
        }

        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2e Save Data
        //

        $primaryKey = "";
        $dataEntered = false;
        try {
            $thisDatabase->db->beginTransaction();

            $query = 'INSERT INTO tblShelters SET ';
            $query .= 'fldShelterName = ?, ';
            $query .= 'fldAddress = ?, ';
            $query .= 'fldCity = ?, ';
            $query .= 'fldState = ?, ';
            $query .= 'fldZip = ?, ';
            $query .= 'fldPhone = ? ';


            if ($debug) {
                print "<p>sql " . $query;
                print"<p><pre>";
                print_r($data);
                print"</pre></p>";
            }
            $results = $thisDatabase->insert($query, $data);

            $primaryKey = $thisDatabase->lastInsert();
            if ($debug) {
                print "<p>pmk= " . $primaryKey;
            }
// all sql statements are done so lets commit to our changes
            $dataEntered = $thisDatabase->db->commit();

            if ($debug)
                print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            if ($debug)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }
    } // end form is valid
} // ends if form was submitted.
//#############################################################################
//
// SECTION 3 Display Form
//
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
        print "Shelter Id: " . $pmkShelterId . "<br>";
        print "Shelter Name: " . $shelterName . "<br>";
        print "Address: " . $address . "<br>";
        print "City: " . $city . "<br>";
        print "State: " . $state . "<br>";
        print "Zip: " . $zip . "<br>";
        print "Phone: " . $phone . "<br>";
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
//
    /* Display the HTML form. note that the action is to this same page. $phpSelf
      is defined in top.php
      NOTE the line:
      value="<?php print $email; ?>
      this makes the form sticky by displaying either the initial default value (line 35)
      or the value they typed in (line 84)
      NOTE this line:
      <?php if($emailERROR) print 'class="mistake"'; ?>
      this prints out a css class so that we can highlight the background etc. to
      make it stand out that a mistake happened here.
     */
    ?>
        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmShelterUpdate">
            <fieldset class="wrapper">
                <legend>Add a new shelter</legend>


                <label for="txtShelterName" class="required">Shelter Name:
                    <input type="text" id="txtShelterName" name="txtShelterName"
                           value="<?php print $shelterName; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter shelter name"
                           <?php if ($shelterNameERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           autofocus>
                </label>

                <label for="txtAddress" class="required">Address:
                    <input type="text" id="txtAddress" name="txtAddress"
                           value="<?php print $address; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter address"
                           <?php if ($addressERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label>

                <label for="txtCity" class="required">City:
                    <input type="text" id="txtCity" name="txtCity"
                           value="<?php print $city; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter city"
                           <?php if ($cityERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label>   

                <label for="txtState" class="required">State:
                    <input type="text" id="txtState" name="txtState"
                           value="<?php print $state; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter state"
                           <?php if ($stateERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label>   

                <label for="txtZip" class="required">Zip Code:
                    <input type="text" id="txtZip" name="txtZip"
                           value="<?php print $zip; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter zip code"
                           <?php if ($zipERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label> 
                <label for="txtPhone" class="required">Phone Number:
                    <input type="text" id="txtPhone" name="txtPhone"
                           value="<?php print $phone; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter phone number"
                           <?php if ($phoneERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label>
                <fieldset class="buttons">
                    <legend></legend>
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Add Shelter" tabindex="900" class="button">
                    <input type="reset" id="btnReset" name="btnReset" value="Reset" tabindex="900" class="button">
                </fieldset> <!-- ends buttons -->
            </fieldset> <!-- Ends Wrapper -->
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