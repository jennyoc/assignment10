<?php

/* 
 */
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//-----------------------------------------------------------------------------
//
// Initialize variables
//
// SQL to create tables, drop if they exist
// any other error checking may be good, parsing html entities etc
//choose which semester data you want to scrape
$url = "http://giraffe.uvm.edu/~rgweb/batch/curr_enroll_fall.csv";

$outputBuffer[] = "";

$debug = false;
if (isset($_GET["debug"])) {
    $debug = true;
}

if ($debug)
    print "<p>DEBUG MODE IS ON</p>";

// include libraries
require_once('../bin/myDatabase.php');

// set up variables for database
$dbUserName = get_current_user() . '_admin';

$whichPass = "a"; //flag for which one to use.

$dbName = strtoupper(get_current_user()) . '_UVM_Shelter';

$thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

// Process file
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
 
//Create tables if they dont exisit
// -- Table structure for table `tblUserRegistration`
    $query = "DROP TABLE IF EXISTS tblUserRegistration";
    $results = $thisDatabase->delete($query);
    $query = "CREATE TABLE IF NOT EXISTS tblUserRegistration ( ";
    $query .= "fldLastName varchar(100) NOT NULL, ";
    $query .= "fldFirstName varchar(100) NOT NULL, ";
    $query .= "pmkUserName varchar(12) NOT NULL, ";
    $query .= "fldEmail int(11) NOT NULL, ";
    $query .= "fldApproved varchar(7) NOT NULL, ";
    $query .= "PRIMARY KEY (pmkUserName)";
    $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $results = $thisDatabase->insert($query);
    $outputBuffer[] = "<p>tblUserRegistration Created.</p>";
    
// -- Table structure for table `tblDogs`
    $query = "DROP TABLE IF EXISTS tblDogs";
    $results = $thisDatabase->delete($query);
    $query = "CREATE TABLE IF NOT EXISTS tblDogs ( ";
    $query .= "pmkDogId int(11) NOT NULL AUTO_INCREMENT, ";
    $query .= "fldDogBreed int(11) NOT NULL, ";
    $query .= "fldDogGender varchar(250) NOT NULL, ";
    $query .= "fldDogAge varchar(5) NOT NULL, ";
    $query .= "fldDogSize varchar(5) NOT NULL";
    $query .= "PRIMARY KEY (`pmkDogId`)";
    $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $results = $thisDatabase->insert($query);
    $outputBuffer[] = "<p>tblDogs Created.</p>";
    
// -- Table structure for table `tblCats`
    $query = "DROP TABLE IF EXISTS tblCats";
    $results = $thisDatabase->delete($query);
    $query = "CREATE TABLE IF NOT EXISTS tblCats ( ";
    $query .= "pmkCatId int(11) NOT NULL, ";
    $query .= "fldCatBreed int(11) NOT NULL, ";
    $query .= "fldCatGender varchar(12) NOT NULL, ";
    $query .= "fldCatAge int(11) NOT NULL, ";
    $query .= "fldCatSize int(11) NOT NULL, ";
    $query .= "fldSection varchar(3) NOT NULL, ";
    $query .= "fldType varchar(6) NOT NULL, ";
    $query .= "fldStart time, ";
    $query .= "fldStop time, ";
     $query .= "fldDays varchar(8) NOT NULL, ";
    $query .= "fldBuilding varchar(10) NOT NULL, ";
     $query .= "fldRoom varchar(5) NOT NULL, ";
    $query .= "PRIMARY KEY (`fnkCourseId`,`fldCRN`,`fnkTeacherNetId`)";
    $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

    $results = $thisDatabase->insert($query);

    $outputBuffer[] = "<p>tblSections Created.</p>";
} else {
    if ($debug)
        print "<p>File Opened Failed.</p>\n";
}
//prepare the output
$outputBuffer[] = "<h1>Courses from: " . $url . "</h1>";
$outputBuffer[] = "<p>Showing all courses</p>";
$outputBuffer[] = "<table>";

// intialize variables
$pmkUserName = "";
$breed = "";
$age = 0;
$name = "";

// put records into database tables
foreach ($records as $oneClass) {
// course table
    $query = "INSERT INTO tblCourses(fldCourseNumber, fldCourseName, fldDepartment, fldCredits) ";
    $query .= "VALUES (?, ?, ?, ?)";
    $data = array($oneClass[1], $oneClass[2], $oneClass[0], $oneClass[12]);
    if ($debug) {
        print "<p>sql " . $query . "</p><p><pre> ";
        print_r($data);
        print "</pre></p>";
    }
    $style = "background-color: lightblue;";
    if (!($breed == $oneClass[0] and
            $age == $oneClass[1] and
            $title == $oneClass[2])) {
        $results = $thisDatabase->insert($query, $data);
        $pmkCourseId = $thisDatabase->lastInsert();
        if ($results) {
            $style = "background-color: lightgreen;";
        } else {
            $style = "background-color: lightred;";
        }
    }
    $outputBuffer[] = "\t<tr></th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[1] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[2] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[0] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[12] . "</th>";

//avoid duplicates
    $subj = $oneClass[0];
    $num = $oneClass[1];
    $title = $oneClass[2];

// teacher table:
    $query = "INSERT IGNORE INTO tblTeachers(fldLastName, fldFirstName, pmkNetId, fldSalary, fldPhone) ";
    $query .= "VALUES (?, ?, ?, ?, ?)";
    $data = explode(', ', $oneClass[15]); // name

    $data[] = $oneClass[16]; // net id
    $data[] = rand(24000, 250000); // salary
    $data[] = "656" . str_pad(rand(0,9999), 4, "0", STR_PAD_LEFT); // phone
    
    
    if ($debug) {
        print "<p>sql " . $query . "</p><p><pre> ";
        print_r($data);
        print "</pre></p>";
    }
    $debug=false;
    
    $results = $thisDatabase->insert($query, $data);
    if ($results) {
        $style = "background-color: green;";
    } else {
        $style = "background-color: red;";
    }
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[16] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $data[1] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $data[0] . "</th>";

// section table
    $query = "INSERT INTO tblSections(fnkCourseId, fldCRN, fnkTeacherNetId, fldMaxStudents, fldNumStudents, fldSection, fldType, fldStart, fldStop, fldDays, fldBuilding, fldRoom) ";
    
    $query .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $data = array($pmkCourseId, $oneClass[3], $oneClass[16], $oneClass[7], $oneClass[8], $oneClass[4], $oneClass[5], $oneClass[9], $oneClass[10], $oneClass[11], $oneClass[13], $oneClass[14]);
    

    if ($debug) {
        print "<p>sql " . $query . "</p><p><pre> ";
        print_r($data);
        print "</pre></p>";
    }

    
    $results = $thisDatabase->insert($query, $data);
    if ($results) {
        $style = "background-color: green;";
    } else {
        $style = "background-color: red;";
    }
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $pmkCourseId . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[3] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[16] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[7] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[8] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[4] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[5] . "</th>";
        $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[9] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[10] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[11] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[13] . "</th>";
    $outputBuffer[] = "\t\t<th style='" . $style . "'>" . $oneClass[14] . "</th>";
    $outputBuffer[] = "\n\n\t</tr>";
} // ends looping through all records

$outputBuffer[] = "</table>";
$outputBuffer = join("\n", $outputBuffer);
echo $outputBuffer;
?>