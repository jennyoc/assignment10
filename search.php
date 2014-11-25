<?php
include 'include/top.php';
//print_r($_POST);
require_once('../bin/myDatabase.php');
$dbUserName = get_current_user() . '_reader';
$whichPass = "r"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_Shelter';
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
/* ##### Step one
 *
 * create your database object using the appropriate database username
 */


// SECTION: 1c form variables
$name = "";
$breed = "";
$size = "";
$age = "";
$gender = "";
$coat = "";
$children = "";

$data= array();

$nameERROR = false;
$breedERROR = false;
$sizeERROR = false;
$ageERROR = false;
$genderERROR = false;
$coatERROR = false;
$childrenERROR = false;

// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// array used to hold form values that will be written to a CSV file
$dataRecord = array();

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
        $name = htmlentities($_POST["txtName"], ENT_QUOTES, "UTF-8");
        $dataRecord[] = $name;
        
        $breed = htmlentities($_POST["lstBreed"], ENT_QUOTES, "UTF-8");
        
        $size = htmlentities($_POST["lstSize"], ENT_QUOTES, "UTF-8");

        $age = htmlentities($_POST["lstAge"], ENT_QUOTES, "UTF-8");

        $gender = htmlentities($_POST ["radGender"], ENT_QUOTES, "UTF-8");
        
        $coat = htmlentities($_POST ["lstCoat"], ENT_QUOTES, "UTF-8");
        
        $children = htmlentities($_POST ["chkChildren"], ENT_QUOTES, "UTF-8");


// SECTION: 2c Validation

        if ($name != "") {
            if (!verifyLetters($name)) {
                $errorMsg[] = "You must type letters like: Bailey";
                $nameERROR = true;
            }
        }
        
// SECTION: 2e prepare query

        $query = "SELECT pmkDogId, fnkShelterId, fldDogName, fldBreed, fldSize, fldAge, fldGender, fldCoat, fldChildren";
        $query .= " FROM tblDogs ";
        $query .= " INNER JOIN tblShelters ON pnkShelterId=fnkShelterId ";
        $query .= " WHERE fldDogName LIKE ? ";
        $data[] = "%" . $name . "%";


//here do rest
        if ($name != "") {
            $query .= " AND fldDogName = ? ";
            $data[] = "%" . $name . "%";
        }

        if ($breed != "") {
            $query .= " AND fldBreed = ? ";
            $data[] = $breed;
        }

        if ($size != "") {
            $query .= " AND fldSize LIKE ? ";
            $data[] = $size;
        }

        if ($age != "") {
            $query .= " AND fldAge = ? ";
            $data[] = $age;
        }
        
        if ($gender != "") {
            $query .= " AND fldGender = ? ";
            $data[] = $gender;
        }
        
        if ($coat != "") {
            $query .= " AND fldCoat = ? ";
            $data[] = $coat;
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
    print "<h2>Total Sections Found: " . $numberRecords . "</h2>";
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

    <article id="main">

        <form action="form.php"
              method="post"
              id="frmSearch">
            <fieldset class="wrapper">
                <h2>Start your pet search here:</h2>
                    <fieldset class="search">

                        <label for="txtName">Subject
                            <input type="text" id="txtName" name="txtName"
                                   value= "<?php print $name; ?>"
                                   tabindex="100" maxlength="45" placeholder="Enter dog name like: Bailey"
    <?php if ($nameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>

                        <label for="lstBreed">Breed
                            <select id="lstBreed"
                                    name="lstBreed"
                                    tabindex="300" >
                                <option  selected value=""></option><option value="Bernese Mountain Dog">Bernese Mountain Dog</option><option value="English Bulldog">English Bulldog</option>
                            </select></label>

                        <label for="lstSize">Size
                                    <select id="lstSize"
                                            name="lstSize"
                                            tabindex="300" >
                                        <option  selected value=""></option><option value="Small">Small</option><option value="Medium">Medium</option><option value="Large">Large</option>
                                    </select></label>
                        
                        <label for="lstAge">Age
                                    <select id="lstAge"
                                            name="lstAge"
                                            tabindex="300" >
                                        <option  selected value=""></option><option value="Young">Young</option><option value="Adult">Adult</option><option value="Senior">Senior</option>
                                    </select></label>


                        <label for="radGender">
 


                                    <input type="radio" name="gender" value="male" checked>Male

                                    <input type="radio" name="gender" value="female">Female
                                </label>

                        <label for="lstCoat">Coat
                                    <select id="lstCoat"
                                            name="lstCoat"
                                            tabindex="600" >
                                        <option selected value="">Any</option>
                                        <option value="Short">Short</option><option value="Medium">Medium</option><option value="Long">Long</option>
                                    </select></label>
                        
                        <label for="chkChildren">Children
Good with children: <input type="checkbox" name="children" value="yes"/>


                        <!-- Z sections -->
                    </fieldset>
                    <fieldset class="buttons">
                        <legend></legend>
                        <input type="submit" id="btnSubmit" name="btnSubmit" value="Find a dog" tabindex="900" class="button">

                    </fieldset> <!-- ends buttons -->
            </fieldset> <!-- ends wrapper  -->

        </form>

    </article>
    </body>
    <?php
}
    include 'include/footer.php';
?>
</html>
