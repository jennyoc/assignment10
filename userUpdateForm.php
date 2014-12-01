<?php
/* the purpose of this page is to display a form to allow a poet and allow us
 * to add a new poet or update an existing poet 
 */

include "include/top.php";
include "include/editNav.php";

$dbUserName = get_current_user() . '_admin';
$whichPass = "a"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_Shelter';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
$update = true;
$debug= true;
// SECTION: 1a.
// $debug = true;
if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}
if ($debug)
    print "<p>DEBUG MODE IS ON</p>";

$errorMsg = array();
$data = array();
$dataEntered = false;
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

if (isset($_GET["id"])) {
    $pmkUserId = htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");
    $query = 'SELECT fldFirstName, fldLastName, fldEmail, fldPassword ';
    $query .= 'FROM tblUsers WHERE pmkUserId = ?';

    $results = $thisDatabase->select($query, array($pmkUserId));

    $firstName = $results[0]["fldFirstName"];
    $lastName = $results[0]["fldLastName"];
    $email = $results[0]["fldEmail"];
    $password = $results[0]["fldPassword"];
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$firstNameERROR = false;
$lastNameERROR = false;
$emailERROR = false;
$passwordERROR = false;


//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
// 

if (isset($_POST["btnSubmit"])) {
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2a Security
//
    /*  if (!securityCheck(true)) {
      $msg = "<p>Sorry you cannot access this page. ";
      $msg.= "Security breach detected and reported</p>";
      die($msg);
      }
     * 
     */

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2b Sanitize (clean) data
// remove any potential JavaScript or html code from users input on the
// form. Note it is best to follow the same order as declared in section 1c.
    $pmkUserId = htmlentities($_POST["hidUserId"], ENT_QUOTES, "UTF-8");
    
    if ($pmkUserId > 0) {
        $update = true;
    }
    // I am not putting the ID in the $data array at this time

    $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
    $data[] = $firstName;

    $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
    $data[] = $lastName;

    $email = htmlentities($_POST["txtEmail"], ENT_QUOTES, "UTF-8");
    $data[] = $email;

    $password = htmlentities($_POST["txtPassword"], ENT_QUOTES, "UTF-8");
    $data[] = $password;



//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2c Validation
//


    if ($firstName == "") {
        $errorMsg[] = "Please enter the first name";
        $firstNameERROR = true;
    }
    
      if ($lastName == "") {
      $errorMsg[] = "Please enter the last name";
      $lastNameERROR = true;
      }
    

    if ($email == "") {
        $errorMsg[] = "Please enter the email";
        $emailERROR = true;
    } elseif (!verifyEmail($email)) {
        $errorMsg[] = "The email appears to contain invalid characters";
        $emailERROR = true;
    }

    if ($password == "") {
        $errorMsg[] = "Please enter the password";
        $passwordERROR = true;
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

        $dataEntered = false;
        try {
            $thisDatabase->db->beginTransaction();

            if ($update) {
                $query = 'UPDATE tblUsers SET ';

                $query .= 'fldFirstName = ?, ';
                $query .= 'fldLastName = ?, ';
                $query .= 'fldEmail = ?, ';
                $query .= 'fldPassword = ? ';

                $query .= 'WHERE pmkUserId = ?';
                $data[] = $pmkUserId;

                $results = $thisDatabase->update($query, $data);

             $dataEntered = $thisDatabase->db->commit();
            }
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
//
//
//
//

    
    
  
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
        print "First Name: " . $firstName . "<br>";
        print "Last Name: " . $lastName . "<br>";
        print "Email: " . $email . "<br>";
        print "Password: " . $password. "<br>";
    }else{
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
              id="frmUserUpdate">
            <fieldset class="wrapper">
                <legend>Update user information</legend>

                <input type="hidden" id="hidUserId" name="hidUserId"
                       value="<?php print $pmkUserId; ?>"
                       >
                
                <label for="txtFirstName" class="required">First Name:
                    <input type="text" id="txtFirstName" name="txtFirstName"
                           value="<?php print $firstName; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter first name"
                           <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           autofocus>
                </label>


                <label for="txtLastName" class="required">Last Name:
                    <input type="text" id="txtLastName" name="txtLastName"
                           value="<?php print $lastName; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter last name"
                           <?php if ($lastNameERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label>   

                <label for="txtEmail" class="required">Email:
                    <input type="text" id="txtEmail" name="txtEmail"
                           value="<?php print $email; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter email address"
                           <?php if ($emailERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label>   

                <label for="txtPassword" class="required">Password:
                    <input type="password" id="txtPassword" name="txtPassword"
                           value="<?php print $password; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter zip code"
                           <?php if ($passwordERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label> 
                <fieldset class="buttons">
                    <legend></legend>
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Update Users" tabindex="900" class="button">
                    
                    <input type="reset" id="btnReset" name="btnReset" value="Reset" tabindex="901" class="button">
                    
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