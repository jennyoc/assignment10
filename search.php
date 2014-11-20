<?php
//print_r($_POST);
require_once('../bin/myDatabase.php');
$dbUserName = get_current_user() . '_reader';
$whichPass = "r"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_UVM_Shelter';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);



include 'include/top.php';
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
$url = "dog-information.csv";
/* ##### Step one
 *
 * create your database object using the appropriate database username
 */


// SECTION: 1c form variables
$animal = "";
$breed = "";
$age = "";
$gender = "";
$spadeNeutured = "";
$hypoallergenic = "";
$children = "";
$pets = "";
$data = array();
$animalERROR = false;

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
        $animal = htmlentities($_POST["radAnimal"], ENT_QUOTES, "UTF-8");
        
        $breed = htmlentities($_POST["lstBreed"], ENT_QUOTES, "UTF-8");

        $age = htmlentities($_POST["lstAge"], ENT_QUOTES, "UTF-8");

        $gender = htmlentities($_POST ["radGender"], ENT_QUOTES, "UTF-8");
        
        $spadeNeutured = htmlentities($_POST ["chkSpadeNeutured"], ENT_QUOTES, "UTF-8");
        
        $hypoallergenic = htmlentities($_POST ["chkHypoallergeinc"], ENT_QUOTES, "UTF-8");
        
        $children = htmlentities($_POST ["chkChildren"], ENT_QUOTES, "UTF-8");


// SECTION: 2c Validation

        if ($animal = "") {
                $errorMsg[] = "You must choose which animal you would like to search for";
                $animalERROR = true;
            }
        }
        
// SECTION: 2e prepare query

        $query = "SELECT CONCAT(fldDepartment, fldCourseNumber) AS Course, fldCRN AS CRN, CONCAT(fldFirstName, fldLastName) AS Professor, fldMaxStudents-fldNumStudents AS SeatsAvailable, fldSection AS Section, fldType AS Type, fldStart AS StartTime, fldStop AS StopTime, fldDays AS Days, fldBuilding AS Building, fldRoom AS Room";
        $query .= " FROM tblSections ";
        $query .= " INNER JOIN tblCourses ON pmkCourseId=fnkCourseId ";
        $query .= " INNER JOIN tblTeachers ON pmkNetId=fnkTeacherNetId ";
        $query .= " WHERE fldStart LIKE ? ";
        $data[] = $startTime . "%";


//here do rest
        if ($animal != "") {
            $query .= " AND fldDepartment = ? ";
            $data[] = $subject;
        }

        if ($number != "") {
            $query .= " AND fldCourseNumber = ? ";
            $data[] = $number;
        }

        if ($building != "") {
            $query .= " AND fldBuilding = ? ";
            $data[] = $building;
        }

        if ($professor != "") {
            $query .= " AND fldLastName LIKE ? ";
            $data[] = $professor . "%";
        }

        if ($type != "") {
            $query .= " AND fldType = ? ";
            $data[] = $type;
        }


// execute query using a  prepared statement
        $results = $thisDatabase->select($query, $data);
        $numberRecords = count($results);
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

                        <label for="radAnimal">Subject
                            <input type="radio" id="radAnimal" name="radAnimal"
                                   value= "<?php print $animal; ?>"
                                   tabindex="100" maxlength="45" placeholder="Enter course subject like: CS"
    <?php if ($animalERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>

                        <label for="txtNumber">Number
                            <input type="text" id="txtNumber" name="txtNumber"
                                   value="<?php print $number; ?>"
                                   tabindex="200" maxlength="45" placeholder="Enter course number like: 148"
    <?php if ($numberERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>


                        <label for="lstBuilding">Building
                            <select id="lstBuilding"
                                    name="lstBuilding"
                                    tabindex="300" >
                                <option  selected value="">Any</option><option value="31 SPR">31 SPR</option><option value="481 MN">481 MN</option><option value="70S WL">70S WL</option><option value="AIKEN">AIKEN</option><option value="ALLEN">ALLEN</option><option value="ANGELL">ANGELL</option><option value="BLLNGS">BLLNGS</option><option value="COOK">COOK</option><option value="DELEHA">DELEHA</option><option value="DEWEY">DEWEY</option><option value="FAHC">FAHC</option><option value="FLEMIN">FLEMIN</option><option value="GIVN">GIVN</option><option value="GIVN B">GIVN B</option><option value="GIVN C">GIVN C</option><option value="GIVN E">GIVN E</option><option value="GUTRSN">GUTRSN</option><option value="HARRIS">HARRIS</option><option value="HILLS">HILLS</option><option value="HSRF">HSRF</option><option value="JEFFRD">JEFFRD</option><option value="JERCHO">JERCHO</option><option value="KALKIN">KALKIN</option><option value="L/L CM">L/L CM</option><option value="L/L-A">L/L-A</option><option value="L/L-B">L/L-B</option><option value="L/L-D">L/L-D</option><option value="LAFAYE">LAFAYE</option><option value="MANN">MANN</option><option value="MEDED">MEDED</option><option value="ML SCI">ML SCI</option><option value="MORRIL">MORRIL</option><option value="MRC">MRC</option><option value="MRC-CO">MRC-CO</option><option value="MUSIC">MUSIC</option><option value="OFFCMP">OFFCMP</option><option value="OLDMIL">OLDMIL</option><option value="OMANEX">OMANEX</option><option value="ONCMP">ONCMP</option><option value="ONLINE">ONLINE</option><option value="PATGYM">PATGYM</option><option value="PERKIN">PERKIN</option><option value="POMERO">POMERO</option><option value="ROWELL">ROWELL</option><option value="RT THR">RT THR</option><option value="SOUTHW">SOUTHW</option><option value="STAFFO">STAFFO</option><option value="TERRIL">TERRIL</option><option value="TRAVEL">TRAVEL</option><option value="UHTN">UHTN</option><option value="UHTN23">UHTN23</option><option value="UHTS">UHTS</option><option value="UHTS23">UHTS23</option><option value="VOTEY">VOTEY</option><option value="WATERM">WATERM</option><option value="WHEELR">WHEELR</option><option value="WILLMS">WILLMS</option>
                            </select></label>


                        <label for="txtStartTime">Start Time
                            <input type="text" id="txtStartTime" name="txtStartTime"
                                   value="<?php print $startTime; ?>"
                                   tabindex="400" maxlength="45" placeholder="Enter the start time of the class like: 13:00"
    <?php if ($startTimeERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>

                        <label for="txtProfessor">Professor
                            <input type="text" id="txtProfessor" name="txtProfessor"
                                   value="<?php print $professor; ?>"
                                   tabindex="500" maxlength="45" placeholder="Enter the Professor's Last name like: Erickson"
    <?php if ($professorERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>

                        <label for="lstType">Type of Class
                            <select id="lstType"
                                    name="lstType"
                                    tabindex="600" >
                                <option selected value="">Any</option>
                                <option value="ACT">ACT</option><option value="CLN">CLN</option><option value="DIS">DIS</option><option value="FWRK">FWRK</option><option value="H">H</option><option value="HYBD">HYBD</option><option value="INTN">INTN</option><option value="IS">IS</option><option value="LAB">LAB</option><option value="LCDS">LCDS</option><option value="LCLB">LCLB</option><option value="LEC">LEC</option><option value="ONL">ONL</option><option value="PERF">PERF</option><option value="PRAC">PRAC</option><option value="REC">REC</option><option value="RSCH">RSCH</option><option value="SEM">SEM</option><option value="STD">STD</option><option value="TD">TD</option>
                            </select></label>

                        <!-- Z sections -->
                    </fieldset>
                    <fieldset class="buttons">
                        <legend></legend>
                        <input type="submit" id="btnSubmit" name="btnSubmit" value="Find a class" tabindex="900" class="button">

                    </fieldset> <!-- ends buttons -->
            </fieldset> <!-- ends wrapper  -->

        </form>

    </article>
    <?php
}
    include 'include/footer.php';
?>
</body>
</html>
