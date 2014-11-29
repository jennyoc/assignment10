<?php
include 'include/top.php';
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
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form
$firstName = "";
$lastName = "";
$email = "";
$password = "";
$password2 = "";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
$firstNameERROR = false;
$lastNameERROR = false;
$emailERROR = false;
$passwordERROR = false;
$password2ERROR = false;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// used for building email message to be sent and displayed
$mailed = false;
$messageA = "";
$messageB = "";
$messageC = "";
$messageD="";

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
    $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $firstName;
    
    $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $lastName;

    $email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
    $dataRecord[] = $email;
    
    $password = htmlentities($_POST["txtPassword"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $password;
    
    $password2 = htmlentities($_POST["txtPassword2"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $password2;
    
    

    

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2c Validation
    
    // Validation section.
    $dbUserName = get_current_user() . '_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_Final_Project';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
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
    if ($password != $password2){
        $errorMsg[] = "Your passwords do not match.";
        $password2Error = true;        
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

        // create a key value for confirmation
            // SECTION: 2e Save Data
        //

        $primaryKey = "";
        $dataEntered = false;
        try {
            $thisDatabase->db->beginTransaction();
            $query = 'INSERT INTO tblUsers(fldfirstName, fldLastName, fldEmail, fldPassword) VALUES (?,?,?,?)';
            $data = array($firstName,$lastName, $email, md5($password)); //md5($password) creates an encryption of the                     password to be displayed in the database - this is done for security reasons.
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
            $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
        }
        
        
        // If the transaction was successful, give success message
        if ($dataEntered) {
            if ($debug)
                print "<p>data entered now prepare keys ";
            //#################################################################
            // create a key value for confirmation

            $query = "SELECT fldDateJoined FROM tblUsers WHERE pmkUserId=" . $primaryKey;
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
    <h2>Become a member today!</h2>
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
        if (!$mailed) {
            print "not ";
        }
        print "been processed</h3>";
        print "<p>A copy of this message has ";
        if (!$mailed) {
            print "not ";
        }
        print "been sent</p>";
        print "<p>To: " . $email . "</p>";
        print "<p>Mail Message:</p>";
        print $messageA . $messageC . $messageD;
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
              id="frmRegister">
            <fieldset class="wrapper">
                    <legend>Please complete the following form with your contact information</legend>
                <fieldset class="wrapperTwo">
                    <fieldset class="contact">

                       
                        <label for="txtFirstName" class="required">First Name
                            <input type="text" id="txtFirstName" name="txtFirstName"
                                   value="<?php print $firstName; ?>"
                                   tabindex="120" maxlength="45" placeholder="Enter your first name"
                                   <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                        


                        <label for="txtLastName" class="required">Last Name
                            <input type="text" id="txtLastName" name="txtLastName"
                                   value="<?php print $lastName; ?>"
                                   tabindex="120" maxlength="45" placeholder="Enter your last name"
                                   <?php if ($lastNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>


                        
                        <label for="txtEmail" class="required">Email
                            <input type="text" id="txtEmail" name="txtEmail"
                                   value="<?php print $email; ?>"
                                   tabindex="120" maxlength="50" placeholder="Enter a valid email"
                                   <?php if ($emailERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                         <label for="txtPassword" class="required">Password
                            <input type="password" id="txtPassword" name="txtPassword"
                                   value=""
                                   tabindex="120" maxlength="45" placeholder="Create a password"
                                   <?php if ($passwordERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                        <label for="txtPassword2" class="required">Confirm Password
                            <input type="password" id="txtPassword2" name="txtPassword2"
                                   value=""
                                   tabindex="120" maxlength="45" placeholder="Create a password"
                                   <?php if ($password2ERROR) print 'class="mistake"'; ?>
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