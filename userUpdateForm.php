<?php
/* the purpose of this page is to display a form to allow an admin to update a current users profile */

include "include/top.php";
include "include/editNav.php";

$dbUserName = get_current_user() . '_admin';
$whichPass = "a"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_Final_Project';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
$update = false;

// SECTION: 1a.
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
$debug = false;
if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}
if ($debug)
    print "<p>DEBUG MODE IS ON</p>";

// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
$yourURL = $domain . $phpSelf;


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
/*
  $pmkUserId = "";
  $firstName = "";
  $lastName = "";
  $email = "";
  $password = "";

  //$pmkUserId = $_GET["id"];

  if (isset($_GET["id"])) {

  $query = 'SELECT fldFirstName, fldLastName, fldEmail, fldPassword ';
  $query .= 'FROM tblUser WHERE pmkUserId = ?';

  $results = $thisDatabase->select($query, array($pmkUserId));


  $dbfirstName = $results[0]["fldFirstName"];
  $dblastName = $results[0]["fldLastName"];
  $dbemail = $results[0]["fldEmail"];
  $dbpassword = $results[0]["fldPassword"];
  }
 * 
 */
if (isset($_GET["id"])) {
    $pmkUserId = htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");

    $query = 'SELECT fldFirstName, fldLastName, fldEmail, fldPassword ';
    $query .= 'FROM tblUsers WHERE pmkUserId = ?';

    $results = $thisDatabase->select($query, array($pmkUserId));

    $firstName = $results[0]["fldFirstName"];
    $lastName = $results[0]["fldLastName"];
    $email = $results[0]["fldEmail"];
    $password = $results[0]["fldPassword"];
} else {
    $pmkUserId = -1;
    $firstName = "";
    $lastName = "";
    $email = "";
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
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();
$data = array();
$dataEntered = false;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//SECTION: Process for when the delete button is clicked.
/* if (isset($_POST["btnDelete"])) {

  $query2 = 'DELETE FROM tblUser WHERE pmkUserId =' . $pmkUserId;

  $dataEntered = $thisDatabase->db->commit();

  try {
  if ($debug)
  print "<p>transaction complete ";
  } catch (PDOException $e) {
  $thisDatabase->db->rollback();
  if ($debug)
  print "Error!: " . $e->getMessage() . "</br>";
  $errorMsg[] = "There was a problem with accepting the data please contact us directly.";
  }
  }
 */
// SECTION: 2 Process for when the form is submitted
//
if (isset($_POST["btnSubmit"])) {

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2a Security
//
    /*    if (!securityCheck(true)) {
      $msg = "<p>Sorry you cannot access this page. ";
      $msg.= "Security breach detected and reported</p>";
      die($msg);
      }
     */
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2b Sanitize (clean) data
// remove any potential JavaScript or html code from users input on the
// form. Note it is best to follow the same order as declared in section 1c.
    $pmkUserId = htmlentities($_POST["hidUserId"], ENT_QUOTES, "UTF-8");
    $data[] = $pmkUserId;

    if ($pmkUserId > 0) {
        $update = true;
    }

    $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
    $data[] = $firstName;

    $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
    $data[] = $lastName;

    $email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
    $data[] = $email;

    $password = htmlentities($_POST["txtPassword"], ENT_QUOTES, "UTF-8");
    $data[] = $password;


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2c Validation
//


    if ($firstName == "") {
        $errorMsg[] = "Please enter your first name";
        $firstNameERROR = true;
    } elseif (!verifyAlphaNum($firstName)) {
        $errorMsg[] = "Your first name appears to contain incorrect characters.";
        $firstNameERROR = true;
    }
    if ($lastName == "") {
        $errorMsg[] = "Please enter your last name";
        $lastNameERROR = true;
    } elseif (!verifyAlphaNum($lastName)) {
        $errorMsg[] = "Your last name appears to contain incorrect characters.";
        $lastNameERROR = true;
    }

    if ($email == "") {
        $errorMsg[] = "Please enter your email address";
        $emailERROR = true;
    } elseif (!verifyEmail($email)) {
        $errorMsg[] = "Your email address appears to be incorrect.";
        $emailERROR = true;
    }
    if ($password == "") {
        $errorMsg[] = "Please enter a password";
        $passwordERROR = true;
    } elseif (!verifyAlphaNum($password)) {
        $errorMsg[] = "Your password appears to contain incorrect characters";
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
                $query .= 'fldFirstName = "?", ';
                $query .= 'fldLastName = "?", ';
                $query .= 'fldEmail = "?", ';
                $query .= 'fldPassword = "?", ';
                $query .= 'WHERE pmkUserId = ?';
                $data[] = $pmkUserId;

                $results = $thisDatabase->update($query, $data);
                $dataEntered = $thisDatabase->db->commit();
            }

            // all sql statements are done so lets commit to our changes

            if ($debug)
                print "<p>transaction complete ";
        } catch (PDOException $e) {
            $thisDatabase->db->rollback();
            if ($debug)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accepting the data please contact us directly.";
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
// If its the first time coming to the form or there are errors we are going
// to display the form.
    if ($dataEntered) { // closing of if marked with: end body submit
        print "<h1>Record Saved</h1> ";
        print "User Id: " . $pmkUserId . "<br>";
        print "First Name: " . $firstName . "<br>";
        print "Last Name: " . $lastName . "<br>";
        print "Email: " . $email . "<br>";
        print "Password: " . $password . "<br>";
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
    </article>
    <article>
        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmUpdate">
            <fieldset class="wrapper">
                <legend>Update a current member profile.</legend>

                <input type="hidden" id="hidUserId" name="hidUserId"
                       value="<?php print $pmkUserId; ?>">

                <label for="txtFirstName" class="required">First Name
                    <input type="text" id="txtFirstName" name="txtFirstName" value="<?php print $firstName; ?>" tabindex="120" maxlength="45" placeholder="Please enter your first name"
                    <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()">
                </label>



                <label for="txtLastName" class="required">Last Name
                    <input type="text" id="txtLastName" name="txtLastName"
                           value="<?php print $lastName; ?>"
                           tabindex="120" maxlength="45" placeholder="Please enter your last name"
                           <?php if ($lastNameERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label>



                <label for="txtEmail" class="required">Email
                    <input type="text" id="txtEmail" name="txtEmail"
                           value="<?php print $email; ?>"
                           tabindex="120" maxlength="45" placeholder="Please enter your email"
                           <?php if ($emailERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label>
                <label for="txtPassword" class="required">Password
                    <input type="password" id="txtPassword" name="txtPassword"
                           value="<?php print $password; ?>"
                           tabindex="120" maxlength="45" placeholder="Please enter your password"
                           <?php if ($passwordERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label>
            </fieldset> <!-- ends contact -->
        
            <fieldset class="buttons">
                <legend></legend>
                <input type="submit" id="btnSubmit" name="btnSubmit" value="Update User" tabindex="900" class="button">
                <input type="submit" id="btnDelete" name="btnDelete" value="Delete User" tabindex="900" class="button">
            </fieldset> <!-- ends buttons -->
        </form>
    </article>

    <?php
}
// end body submit
?>
</body>
<?php
include "footer.php";
if ($debug)
    print "<p>END OF PROCESSING</p>";
?>

</html>