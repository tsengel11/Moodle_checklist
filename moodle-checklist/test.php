<?php

use mod_checklist\local\checklist_item;

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/importexportfields.php');
global $CFG, $PAGE, $OUTPUT, $DB;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/csvlib.class.php');


$userid = $USER->id;
$id = required_param('id', PARAM_INT); // Course module id.

$cm = get_coursemodule_from_id('checklist', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$checklist = $DB->get_record('checklist', array('id' => $cm->instance), '*', MUST_EXIST);

$url = new moodle_url('/mod/checklist/import.php', array('id' => $cm->id));
$PAGE->set_url($url);
require_login($course, true, $cm);

//$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);


// $PAGE->set_title($pagetitle);
// $PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter checklistbox');

echo html_writer::tag('div', '&nbsp;', array('id' => 'checklistspinner'));
//require_login($course, true, $cm);

// echo '&nbsp;&nbsp;<form style="display: block;" action="test.php" method="post" />';
// echo '<input type="text" name="breed" value="" />';
// echo '<input type="submit" name="submit" value="add comment for all students" />';
// echo '</form>';
//$result = $DB->get_records_sql("SELECT * FROM `mdl_checklist_item` WHERE checklist=44");
/// DB
$servername = $CFG->dbhost;
$username = $CFG->dbuser;
$password = $CFG->dbpass;
$dbname = $CFG->dbname;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<form action='insert.php'>";

$courseid = $cm->course;
$checklistid = $cm->instance;

///////// Selecting Checklist
$sql_g = "SELECT * FROM `mdl_groups` WHERE courseid='$courseid'";


$result_g = $conn->query($sql_g);

if ($result_g->num_rows > 0) {
    //echo strval ($result);

    echo "<label>Select groups:</label>";
    echo "<select name='groupid'>";
    while ($row_g = $result_g->fetch_assoc()) {
        echo $row_g;
        echo "<option value='" . $row_g["id"] . "'>" . $row_g["name"] . "</option>";
    }
    echo "</select>";
} else {
    echo "0 result";
}

///////// Selecting Groups
$sql = "SELECT * FROM `mdl_checklist_item` WHERE checklist='$checklistid'";

$result = $conn->query($sql);
$iscombo = true;
if ($iscombo) {
    //Selecting Combobox options
    if ($result->num_rows > 0) {
        //echo strval ($result);
        echo "<form action='insert.php'>";
        echo "<input type='hidden' name='id' value='" . $id . "' />";
        echo "<label>Select creteria:</label>";
        echo "<select name='checkid'>";
        while ($row1 = $result->fetch_assoc()) {
            echo $row1;
            echo "<option value='" . $row1["id"] . "'>" . $row1["displaytext"] . "</option>";
        }
        echo "</select>";
        echo "<label>Comment for all students:</label>";
        echo '<input type="text" name="commentid" value="" id="comment"/>';
        echo "&nbsp<input type='submit'>";
        echo "</form>";
    } else {
        echo "0 result";
    }
} else {
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<br> Id: " . $row["id"] . " - Name: " . $row["displaytext"] . "<br>";
            echo '&nbsp;&nbsp;<form style="display: block;" action="insert.php method="post" />';
            echo '<input type="text" name="commentid" value="" id="comment"/>';
            echo '<input type="submit" name="submit" value="add comment for all students" />';
            echo '</form>';
        }
    } else {
        echo "0 results";
    }
}

$conn->close();
// echo '<p>SQL Selecting groups:</p>' . $sql_g;
// echo '<p>SQL Selecting check list :</p>' . $sql;
// echo '<p>Course Id:</p>' . $id;
// echo '<p>Course Link:</p>' . $url;
// echo '<p>Course USER ID:</p>' . $userid;
// echo '<p>Student names:</p>' . $userid;



echo $OUTPUT->footer();
