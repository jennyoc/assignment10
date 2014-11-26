<?php 
//print_r($_POST);
require_once('../bin/myDatabase.php');
$dbUserName = get_current_user() . '_reader';
$whichPass = "r"; //flag for which one to use.
$dbName = strtoupper(get_current_user()) . '_Final_Project';
$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);



include ("include/top.php");
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
$url = "https://jocallag.w3.uvm.edu/cs148/assignment10/include/dog.csv";
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

        $breed = htmlentities($_POST["lstBreed"], ENT_QUOTES, "UTF-8");

        $coat = htmlentities($_POST["lstCoat"], ENT_QUOTES, "UTF-8");


// re-do up
        $children = htmlentities($_POST["lstChildren"], ENT_QUOTES, "UTF-8");

// SECTION: 2c Validation
        // SECTION: 2e prepare query

        $query = "SELECT tblDogs.fldDogName AS Name, tblDogs.fldBreed AS Breed, tblDogs.fldSize AS Size, tblDogs.fldAge AS Age, tblDogs.fldCoat AS Coat, tblDogs.fldHypo AS Hypoallergenic, tblDogs.fldColor AS Coloring, tblDogs.fldGender AS Gender, tblDogs.fldChildren AS Children, tblShelters.fldShelterName AS Shelter  ";
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

    <article id="main">
        <h2>Search For Your New Companion Today!</h2>

        <form action="search.php"
              method="post"
              id="frmRegister">
            <fieldset class="wrapper">

                <fieldset class="wrapperTwo">
                    <fieldset class="search">
                        <!--Breed list box-->
                        <?php
// Step Two: code can be in initialize variables or where step four needs to be
                        $query = "SELECT DISTINCT fldBreed ";
                        $query .= "FROM tblDogs ";
                        $query .= "ORDER BY fldBreed";


                        $breed = $thisDatabase->select($query);

                        $output = array();

                        $output[] = '<label for="lstBreed">Breed: ';
                        $output[] = '<select id="lstBreed" ';
                        $output[] = '        name="lstBreed"';
                        $output[] = '        tabindex="300" >';


                        foreach ($breed as $row) {

                            $output[] = '<option ';
                            if ($breed == $row["fldBreed"])
                                $output[] = ' selected ';

                            $output[] = 'value="' . $row["fldBreed"] . '">' . $row["fldBreed"];

                            $output[] = '</option>';
                        }

                        $output[] = '</select></label>';

                        print join("\n", $output);  // this prints each line as a separate  line in html
                        ?>

                        <!--Size check boxes-->
                        <?php
                        $query = "SELECT DISTINCT fldSize   ";
                        $query .= "FROM tblDogs ";
                        $query .= "ORDER BY tblDogs.fldSize DESC ";

                        $size = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<fieldset class="checkbox">';
                        $output[] = '<legend>Size:</legend>';


                        foreach ($size as $row) {

                            $output[] = '<label for="chk' . str_replace(" ", "-", $row["fldSize"]) . '"><input type="checkbox" ';
                            $output[] = ' id="chk' . str_replace(" ", "-", $row["fldSize"]) . '" ';
                            $output[] = ' name="chk' . str_replace(" ", "-", $row["fldSize"]) . '" ';
                            $output[] = 'value="' . $row["pmkDogId"] . '">' . $row["fldSize"];
                            $output[] = '</label>';
                        }

                        $output[] = '</fieldset>';

                        print join("\n", $output);  // this prints each line as a separate  line in html
                        ?>

                        <!--                        Age check boxes-->
                        <?php
                        $query = "SELECT DISTINCT fldStage  ";
                        $query .= "FROM tblDogs ";
                        $query .= "ORDER BY fldStage ";

                        $age = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<fieldset class="checkbox">';
                        $output[] = '<legend>Age:</legend>';


                        foreach ($age as $row) {

                            $output[] = '<label for="chk' . str_replace(" ", "-", $row["fldStage"]) . '"><input type="checkbox" ';
                            $output[] = ' id="chk' . str_replace(" ", "-", $row["fldStage"]) . '" ';
                            $output[] = ' name="chk' . str_replace(" ", "-", $row["fldStage"]) . '" ';
                            $output[] = 'value="' . $row["pmkDogId"] . '">' . $row["fldStage"];
                            $output[] = '</label>';
                        }

                        $output[] = '</fieldset>';

                        print join("\n", $output);  // this prints each line as a separate  line in html
                        ?>

                        <!--                        Coat list box-->
                        <?php
                        $query = "SELECT DISTINCT fldCoat ";
                        $query .= "FROM tblDogs ";
                        $query .= "ORDER BY tblDogs.fldCoat  DESC ";

                        $coat = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<label for="lstCoat">Type of coat: ';
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


                        <!--Gender radio buttons-->
                        <?php
                        $query = "SELECT DISTINCT fldGender ";
                        $query .= "FROM tblDogs ";

                        $gender = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<fieldset class="radio">';
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

                        $output[] = '</fieldset>';

                        print join("\n", $output);  // this prints each line as a separate  line in html 
                        ?>

                        <!-- Children -->
                        <?php
                        $query = "SELECT DISTINCT fldChildren ";
                        $query .= "FROM tblDogs ";

                        $children = $thisDatabase->select($query);

                        $output = array();
                        $output[] = '<fieldset class="radio">';
                        $output[] = '<legend>Good with children:</legend>';

                        foreach ($children as $row) {

                            $output[] = '<label for="rad' . str_replace(" ", "-", $row["fldChildren"]) . '"><input type="radio" ';
                            $output[] = ' id="rad' . str_replace(" ", "-", $row["fldChildren"]) . '" ';
                            $output[] = ' name="radChildren" ';

                            if ($gender == $row["pmkDogId"])
                                $output[] = " checked ";

                            $output[] = 'value="' . $row["pmkDogId"] . '">' . $row["fldChildren"];
                            $output[] = '</label>';
                        }

                        $output[] = '</fieldset>';

                        print join("\n", $output);  // this prints each line as a separate  line in html 
                        ?>

                    </fieldset>

                    <fieldset class="buttons">
                        <input type="submit" id="btnSubmit" name="btnSubmit" value="Find a dog" tabindex="900" class="button">
                        <input type="reset" id="btnReset" value="Reset" tabindex="901" class="button">

                    </fieldset> <!-- ends buttons -->

                </fieldset> <!-- ends wrapper Two -->

            </fieldset> <!-- Ends Wrapper -->
        </form>

    </article>
<article class="aside">
    <h4>Age (Years)</h4>
    <h5>
        Puppy: 0-2<br><br>
        Adult: 3-5<br><br>
        Senior: 6-9<br><br>
        Geriatric: 10+ 
    </h5>
    <h4>Size (Pounds)</h4>
    <h5>
        Small: Under 25<br><br>
        Medium: 26-40<br><br>
        Large: 41-70<br><br>
        XL: 70+
    </h5>

    
</article>
    <?php
}
// end body submit
include ("include/footer.php");
?>
</body>
</html>