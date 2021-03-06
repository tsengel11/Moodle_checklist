<?php


use mod_checklist\local\checklist_item;

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/importexportfields.php');
global $CFG, $PAGE, $OUTPUT, $DB, $USER;

//$id = required_param('id', PARAM_INT);



echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter checklistbox');

require_login($course, true, $cm);

// require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
// require_once(dirname(__FILE__) . '/importexportfields.php');
// global $CFG, $PAGE, $OUTPUT, $DB, $USER;
// require_once($CFG->libdir . '/formslib.php');
// require_once($CFG->libdir . '/csvlib.class.php');


// $userid = $USER->id;
// $id = required_param('id', PARAM_INT); // Course module id.

// $cm = get_coursemodule_from_id('checklist', $id, 0, false, MUST_EXIST);
// $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
// $checklist = $DB->get_record('checklist', array('id' => $cm->instance), '*', MUST_EXIST);

// $url = new moodle_url('/mod/checklist/import.php', array('id' => $cm->id));
// $PAGE->set_url($url);
// require_login($course, true, $cm);

$servername = $CFG->dbhost;
$username = $CFG->dbuser;
$password = $CFG->dbpass;
$dbname = $CFG->dbname;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
};

//Retriving parameters
$userid = $USER->id;
$checkid_int = (int)$_GET['checkid'];
$comment = $_GET['commentid'];
$group = $_GET['groupid'];


///SELECTING STUDENT ID SECTION

$insert_sql = "INSERT INTO mdl_checklist_comment (itemid, userid, commentby, text) ";
$insert_sql .= "VALUES";

$sql = "SELECT * FROM `mdl_groups_members` WHERE groupid='$group'";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
  while ($row = $result->fetch_assoc()) {
    //echo "<br> Id: " . $row["userid"] . " - GroupID: " . $row["groupid"];
    $temp_student_id = $row["userid"];
    $insert_sql .= "($checkid_int, $temp_student_id, $userid ,'$comment'),";
  }
  $insert_sql = rtrim($insert_sql, ",");
} else {
  echo "0 results";
}



//$insert_sql = "INSERT INTO mdl_checklist_comment (itemid, userid, commentby, text) ";
//$insert_sql .= "VALUES ('$checkid_int', 212, '$userid' ,'$comment') ";
$insert_sql .= "ON DUPLICATE KEY UPDATE itemid = VALUES(itemid), userid = VALUES(userid), commentby = VALUES(commentby),text = VALUES(text)";

if ($conn->query($insert_sql) === TRUE) {
  echo "<br/>New record created successfully";
} else {
  echo "<br/>Error: " . $sql . "<br>" . $conn->error;
}

$id2 = $_GET['id'];
echo "<form action='test.php'>";
echo "<input type='hidden' name='id' value='" . $id2 . "' />";
echo "&nbsp<input type='submit' value='Back'>";

// echo '<p>SQL:</p>' . $sql;
// echo "<br/>SQL:" . $insert_sql;
// echo "<br>Comment text:" . $_POST['commentid'];
// echo "Checklist Number:" . $_POST['checkid'];
// echo "Checklist Number2:" . $_GET['checkid'];
// echo "Groupid:" . $_GET['groupid'];


$conn->close();

echo $OUTPUT->footer();
