<?php


include "include/top.php";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
$update = false;

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

// SECTION: 1 Initialize variables
$update = false;
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form

if (isset($_GET["id"])) {
    $pmkUserId = htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");

    $query = 'SELECT fldFirstName, fldLastName, fldEmail, fldPassword ';
    $query .= 'FROM tblUser WHERE pmkUserId = ?';

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
    $password = "";
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
//
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
    $pmkPoetId = htmlentities($_POST["hidPoetId"], ENT_QUOTES, "UTF-8");
    if ($pmkPoetId > 0) {
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
    $dbUserName = get_current_user() . '_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_Shelter';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

    if ($firstName == "") {
        $errorMsg[] = "Please enter your first name";
        $firstNameERROR = true;
    } elseif (!verifyAlphaNum($firstName)) {
        $errorMsg[] = "Your first name appears to have extra character.";
        $firstNameERROR = true;
    }

    if ($lastName == "") {
        $errorMsg[] = "Please enter your last name";
        $lastNameERROR = true;
    } elseif (!verifyAlphaNum($lastName)) {
        $errorMsg[] = "Your last name appears to have extra character.";
        $lastNameERROR = true;
    }
    
    if ($email == "") {
    $errorMsg[] = "Please enter your email address";
    $emailERROR = true;
    } elseif (!verifyEmail($email)) {
    $errorMsg[] = "Your email address appears to be incorrect.";
        $emailERROR = true;
            // check if they already exist
    } elseif (isUser($email)) {
    $emailERROR = true;
    $errorMsg[] = "There is already a user with that email";
    }
    
    if ($password == "") {
        $errorMsg[] = "Please enter a password";
        $passwordERROR = true;
    } elseif (!verifyAlphaNum($password)) {
        $errorMsg[] = "Your password appears to contain incorrect characters";
        $passwordERROR = true;
    }
    //// should check to make sure its the correct date format
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
                $query = 'UPDATE tblUser SET ';
            } else {
                $query = 'INSERT INTO tblUser SET ';
            }

            $query .= 'fldFirstName = ?, ';
            $query .= 'fldLastName = ?, ';
            $query .= 'fldEmail = ? ';
            $query .= 'fldPassword = ? ';

            if ($update) {
                $query .= 'WHERE pmkUserId = ?';
                $data[] = $pmkUserId;
                
                $results = $thisDatabase->update($query, $data);
            } else {
                $results = $thisDatabase->insert($query, $data);

                $primaryKey = $thisDatabase->lastInsert();
                if ($debug) {
                    print "<p>pmk= " . $primaryKey;
                }
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

    // If the transaction was successful, give success message
        if ($dataEntered) {
            if ($debug)
                print "<p>data entered now prepare keys ";
            //#################################################################
            // create a key value for confirmation

            $query = "SELECT fldDateJoined FROM tblUser WHERE pmkUserId=" . $primaryKey;
            $results = $thisDatabase->select($query);

            $dateSubmitted = $results[0]["fldDateJoined"];

            $key1 = sha1($dateSubmitted);
            $key2 = $primaryKey;

            if ($debug)
                print "<p>key 1: " . $key1;
            if ($debug)
                print "<p>key 2: " . $key2;


            //#################################################################
            
            //Put forms information into a variable to print on the screen
            //

            $messageA = '<h2>Welcome,'.$firstName.'! Thank you for registering to become a member of Puppy Lovermont,</h2>';
            $messageB = "<p>Click this link to confirm your registration: ";
            $messageB .= '<a href="' . $domain . $path_parts["dirname"] . '/confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . '">Confirm Registration</a></p>';
            $messageB .= "<p>or copy and paste this url into a web browser: ";
            $messageB .= $domain . $path_parts["dirname"] . '/confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . "</p>";
            $messageC = '<p>Make sure you remember your login email and password so you can access the member section of the website.</p>';
            $messageC .= "<p><b>Email:</b><i>   " . $email . "</i></p>";
            $messageC .= "<p><b>Password:</b><i>   " . $password . "</i></p>";
            $messageD = "<p>You will receive an email shortly with a link to confirm your membership.</p>";        
            //##############################################################
            //
            // email the form's information
            //
            $to = $email; // the person who filled out the form
            $cc = "";
            $bcc = "";
            $from = "PUPPY LOVERMONT REGISTRATION <noreply@puppylovermont.com>";
            $subject = "PUPPY LOVERMONT: CONFIRM REGISTRATION";

            $mailed = sendMail($to, $cc, $bcc, $from, $subject, $messageA . $messageB . $messageC);
        }
    } // end form is valid
} // ends if form was submitted.
//#############################################################################
//
// SECTION 3 Display Form
//
?>
<nav>
    <ol>
        <li><a href="userList.php">Users</a></li><li class="activePage">Add Poet</li>    </ol>
</nav><article id="main">
      
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
              id="frmRegister">
            <fieldset class="wrapper">
                <legend>Add a user</legend>
<label for="txtFirstName" class="required">First Name
<input type="text" id="txtFirstName" name="txtFirstName"
value="<?php print $firstName; ?>"
tabindex="100" maxlength="45" placeholder="Enter the first name"
<?php if ($firstNameERROR) print 'class="mistake"'; ?>
onfocus="this.select()"
autofocus>
</label>
                
                <label for="txtLastName" class="required">Last Name
<input type="text" id="txtLastName" name="txtLastName"
value="<?php print $lastName; ?>"
tabindex="100" maxlength="45" placeholder="Enter the last name"
<?php if ($lastNameERROR) print 'class="mistake"'; ?>
onfocus="this.select()"
>
</label>
                
  <label for="txtEmail" class="required">Email
<input type="text" id="txtEmail" name="txtEmail"
value="<?php print $email; ?>"
tabindex="100" maxlength="45" placeholder="Enter the email"
<?php if ($emailERROR) print 'class="mistake"'; ?>
onfocus="this.select()"
>
</label>   
                
    <label for="txtPassword" class="required">Password
<input type="password" id="txtPassword" name="txtPassword"
value="<?php print $password; ?>"
tabindex="100" maxlength="45" placeholder="Enter the password"
<?php if ($passwordERROR) print 'class="mistake"'; ?>
onfocus="this.select()"
>
</label>             
                    </fieldset> <!-- ends contact -->
                </fieldset> <!-- ends wrapper Two -->
                <fieldset class="buttons">
                    <legend></legend>
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Register" tabindex="900" class="button">
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