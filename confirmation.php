<?php

include ('include/top.php');


print '<article id="main">';

print '<h1>Registration Confirmation</h1>';

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// variables for the classroom purposes to help find errors.
$debug = false;
if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = false;
}
if ($debug)
    print "<p>DEBUG MODE IS ON</p>";
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%

$adminEmail = "jmagie@uvm.edu";
$message = "<p>I am sorry but this project cannot be confirmed at this time. Please call (802) 656-1234 for help in resolving this matter.</p>";


//##############################################################
//
// SECTION: 2 

// 
// process request

if (isset($_GET["q"])) {
    $key1 = htmlentities($_GET["q"], ENT_QUOTES, "UTF-8");
    $key2 = htmlentities($_GET["w"], ENT_QUOTES, "UTF-8");

    $data = array($key2);
    //##############################################################
    // get the membership record 
//validation functions
    $dbUserName = get_current_user() . '_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_Shelter';
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
    
    $query = "SELECT fldFirstName, fldLastName, fldEmail, fldDateJoined FROM tblUsers WHERE pmkUserId = ? ";
    $results = $thisDatabase->select($query, $data);

    $firstName = $results[0]["fldFirstName"];
    $lastName = $results[0]["fldLastName"];
    $dateSubmitted = $results[0]["fldDateJoined"];
    $email = $results[0]["fldEmail"];

    $k1 = sha1($dateSubmitted);

    if ($debug) {
        print "<p>Date: " . $dateSubmitted;
        print "<p>email: " . $email;
        print "<p><pre>";
        print_r($results);
        print "</pre></p>";
        print "<p>k1: " . $k1;
        print "<p>q : " . $key1;
    }
    //##############################################################
    // update confirmed
    if ($key1 == $k1) {
        if ($debug)
            print "<h1>Confirmed</h1>";

        $query = "UPDATE tblUsers SET fldConfirmed=1 WHERE pmkUserId = ? ";
        $results = $thisDatabase->update($query, $data);

        if ($debug) {
            print "<p>Query: " . $query;
            print "<p><pre>";
            print_r($results);
            print_r($data);
            print "</pre></p>";
        }
        
        
        // notify admin
        $messageAdmin = "<p>The following member has been registered:</p>";
        $messageAdmin .= "<p><b>First Name:</b><i>   " . $firstName . "</i></p>";
        $messageAdmin .= "<p><b>Last Name:</b><i>   " . $lastName . "</i></p>";
        $messageAdmin .= "<p><b>Email Address:</b><i>   " . $email . "</i></p>";
              

        if ($debug)
            print "<p>" . $messageAdmin;

        $to = $adminEmail;
        $cc = "";
        $bcc = "";
        $from = "PUPPY LOVERMONT REGISTRATION <noreply@puppylovermont.com>";
        $subject = "PUPPY LOVERMONT: NEW MEMBER";

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $messageAdmin);

        if ($debug) {
            print "<p>";
            if (!$mailed) {
                print "NOT ";
            }
            print "mailed to admin ". $to . ".</p>";
        }

        // notify user
        $to = $email;
        $cc = "";
        $bcc = "";
        $from = "PUPPY LOVERMONT <noreply@puppylovermont.com>";
        $subject = "PUPPY LOVERMONT REGISTRATION CONFIRMED";
        $message = "<p>Thank you for taking the time to confirm your registration. You can now access the member page of the site by clicking the link below:";
        $messageLink = '<a href="' . $domain . $path_parts["dirname"] . '/login.php' .'">Member Login</a></p>';

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message, $messageLink);

        print $message;
        print $messageLink;
        //header("Location: profile.php");
        if ($debug) {
            print "<p>";
            if (!$mailed) {
                print "NOT ";
            }
            print "mailed to member: " . $to . ".</p>";
        }
    }else{
        print $message;
    }
} // ends isset get q
?>



<?php
include "footer.php";
if ($debug)
    print "<p>END OF PROCESSING</p>";
?>
</article>
</body>
</html>
