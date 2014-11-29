<?php
//print_r($_POST);
require_once('lib/security.php');
//include "lib/validation-functions.php";

require_once('../bin/myDatabase.php');
$dbUserName = get_current_user() . '_reader';
$whichPass = "r"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_Final_Project';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);



// SECTION: 1 Initialize variables
// 1s. variables for the classroom purposes to help find errors
$debug = false;


if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}

if ($debug)
    print "<p>DEBUG MODE IS ON</p>";

// 1b. security: define security variable to be used in SECTION 2a.
$yourURL = $domain . $phpSelf;
$url = "https://jocallag.w3.uvm.edu/cs148/assignment10/include/tblDogs.csv";
/* ##### Step one
 *
 * create your database object using the appropriate database username
 */


// SECTION: 1c form variables
$breed = " ";
$size = "";
$age = "";
$coat = " ";
$gender = "";
$children = "";
$data = array();
$breedERROR = false;

// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// array used to hold form values that will be written to a CSV file
$data = array();

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//

if (isset($_POST["btnSubmit"])) {

// open and close Spring courses

    $file = fopen($url, "r");
    /* the variable $url will be empty or false if the file does not open */
    if ($file) {
        if ($debug)
            print "<p>File Opened. Begin reading data into an array.</p>\n";
        /* This reads the first row which in our case is the column headers:
         * Subj # Title Comp Numb Sec Lec Lab Camp Code
         * Max Enrollment Current Enrollment Start Time End Time
         * Days Credits Bldg Room Instructor NetId Email
         */
        $headers = fgetcsv($file);
        /* the while loop keeps exectuing until we reach the end of the file at
         * which point it stops. the resulting variable $records is an array with
         * all our data.
         */
        while (!feof($file)) {
            $records[] = fgetcsv($file);
        }
//closes the file
        fclose($file);
        if ($debug) {
            print "<p>Finished reading. File closed.</p>\n";
            print "<p>Contents of my array<p><pre> ";
            print_r($records);
            print "</pre></p>";
        }

        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
    // SECTION: 2a Security
        //
        if (!securityCheck(true)) {
            $msg = "<p>Sorry you cannot access this page. ";
            $msg.= "Security breach detected and reported</p>";
            die($msg);
        }

// SECTION: 2b Sanitize (clean) data

        $breed = htmlentities($_POST["lstBreed"], ENT_QUOTES, "UTF-8");

        //size
        // age
        $coat = htmlentities($_POST["lstCoat"], ENT_QUOTES, "UTF-8");

        $gender = htmlentities($_POST["radGender"], ENT_QUOTES, "UTF-8");
        $dataRecord[] = $gender;

        $children = htmlentities($_POST["radChildren"], ENT_QUOTES, "UTF-8");
        $dataRecord[] = $children;

// SECTION: 2c Validation
        // SECTION: 2e prepare query

        $query = "SELECT tblDogs.fldDogName AS Name, tblDogs.fldBreed AS Breed, tblDogs.fldSize AS Size, tblDogs.fldAge AS Age, tblDogs.fldCoat AS Coat, tblDogs.fldColor AS Coloring, tblDogs.fldGender AS Gender, tblDogs.fldChildren AS Children, tblShelters.fldShelterName AS Shelter  ";
        $query .= " FROM tblDogs,tblShelters ";
        $query .= " WHERE tblShelters.pmkShelterId=tblDogs.fnkShelterId ";

        if ($breed != "") {
            $query .= " AND fldBreed = ? ";
            $data[] = $breed;
        }

        if ($size != "") {
            $query .= " AND fldSize = ? ";
            $data[] = $size;
        }

        if ($age != "") {
            $query .= " AND fldStage = ? ";
            $data[] = $age;
        }

        if ($coat != "") {
            $query .= " AND fldCoat = ? ";
            $data[] = $coat;
        }

        if ($gender != "") {
            $query .= " AND fldGender = ? ";
            $data[] = $gender;
        }

        if ($children != "") {
            $query .= " AND fldChildren = ? ";
            $data[] = $children;
        }


        // execute query using a  prepared statement
        $results = $thisDatabase->select($query, $data);
        $numberRecords = count($results);
    }
}


if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
    print "<h2>Dogs Available: " . $numberRecords . "</h2>";
    print "<table>";

    $firstTime = true;

    /* since it is associative array display the field names */
    foreach ($results as $row) {
        if ($firstTime) {
            print "<tr>";
            $keys = array_keys($row);
            foreach ($keys as $key) {
                if (!is_int($key)) {
                    print "<th>" . $key . "</th>";
                }
            }
            print "</tr>";
            $firstTime = false;
        }

        /* display the data, the array is both associative and index so we are
         *  skipping the index otherwise records are doubled up */
        print "<tr>";
        foreach ($row as $field => $value) {
            if (!is_int($field)) {
                print "<td>" . $value . "</td>";
            }
        }
        print "</tr>";
    }
    print "</table>";
} else {
    if ($errorMsg) {
        print '<div id="errors">';
        print "<ol>\n";
        foreach ($errorMsg as $err) {
            print "<li>" . $err . "</li>\n";
        }
        print "</ol>\n";
        print '</div>';
    }
    ?>

    <form action="search.php"
          method="post"
          id="frmViewAll">

        <fieldset class="buttons">
            <input type="submit" id="btnSubmit" name="btnSubmit" value="View All Dogs" tabindex="900" class="button">

        </fieldset> <!-- ends buttons -->

    </form>


    <?php
}
?>
