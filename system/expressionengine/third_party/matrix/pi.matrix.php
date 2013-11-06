<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'EEPortfolio Matrix',
	'pi_version' => '1.0.0',
	'pi_author' => 'Paul Sijpkes',
	'pi_author_url' => '',
	'pi_description' => 'Render assessment matrix.',
	'pi_usage' => Matrix::usage()
);

class Matrix {
    
    public $return_data = "";
    private $id = 0;
    private $student_id = 0;
    private $member_id = 0;
    //private $info = "";
    
    public function __construct()
    {
        $this->id = ee()->TMPL->fetch_param('id');
        $this->member_id = ee()->session->userdata('member_id');
	//$this->info = ee()->TMPL->tagdata;
    }
    /*
     *	Testing phase
     *
     *
     */
    public function educatorJavascript() {
     
$member_id = ee()->session->userdata('member_id');
if($member_id == 0) return;

$group_id = ee()->session->userdata('group_id');
if($group_id != 1 && $group_id != 6 && $group_id != 7) return;

$entry_id = ee()->TMPL->fetch_param("entry_id");
$student_id =  ee()->TMPL->fetch_param("member");

$sql = "SELECT screen_name, email FROM exp_members WHERE member_id = '$student_id' LIMIT 1";

$query =  ee()->db->query($sql);
$student_screen_name = "";
$student_email = "";

if ($query->num_rows() > 0)
{
    foreach($query->result_array() as $row) // returns all assessed matrix statements for this evidence
    {   
        $student_screen_name = $row['screen_name'];
        $student_email = $row['email'];
    }
}

// get item directly, as self assessed regardless of whether or not it has been assessed by an educator. 
$sql = "SELECT title.entry_date, data.entry_id, title.author_id, data.field_id_6 AS self_assessment, student.screen_name, student.email, student.group_id
FROM exp_channel_data data, exp_channel_titles title, otca_evidence ev, exp_members student WHERE data.entry_id = ev.entry_id
AND title.entry_id = ev.entry_id AND student.member_id = title.author_id AND student.member_id = '$student_id' AND data.entry_id = '$entry_id'
AND ev.last_assessed = '0'";

$query =  ee()->db->query($sql);

if ($query->num_rows() > 0)
{
    foreach($query->result_array() as $row) // returns all assessed matrix statements for this evidence
    {   
        $self_assessment = $row['self_assessment'];
        $current = $row['entry_id'] == $entry_id;
        $self_assessed_array = array('is_current_entry' => $current, 'entry_date' => ee()->localize->format_date('%D, %F %d, %Y %g:%i:%s%a',$row['entry_date']),'entry_id' => $row['entry_id'], 'group_id' => $row['group_id'], 'self_assessment' => "$self_assessment");
    }
}

// get history of matrix assessments for the historical view of the matrix.
$sql = "SELECT title.entry_date, data.entry_id, title.author_id, title.title, data.field_id_6 as self_assessment, av.matrix_ids as supervisor_assessment,
    av.date_assessed, m.screen_name, m.email, m.group_id FROM  exp_channel_data data, exp_channel_titles title , otca_evidence ev,
    otca_evidence_validated av, exp_members m, exp_members student WHERE data.entry_id = ev.entry_id AND title.entry_id = ev.entry_id
    AND ev.entry_id = av.evidence_id AND m.member_id = av.assessor_id AND student.member_id = title.author_id
    AND student.member_id ='$student_id' ORDER BY m.group_id, av.date_assessed DESC, data.entry_id";

$query =  ee()->db->query($sql);

if ($query->num_rows() > 0)
{
    foreach($query->result_array() as $row) // returns all assessed matrix statements for this evidence
    {   
        $sa = $row['supervisor_assessment'];
        $selfa = $row['self_assessment'];
        $current = $row['entry_id'] == $entry_id;
        $assess_array[] = array('is_current_entry' => $current, 'entry_date' => ee()->localize->format_date('%D, %F %d, %Y %g:%i:%s%a',$row['entry_date']),'entry_id' => $row['entry_id'], 'title' => $row['title'] /* added 5/06/13 */,'date_assessed' => $row['date_assessed'], 'supervisor_assessment' => "$sa", 'screen_name' => $row['screen_name'], 'email' => $row['email'], 'group_id' => $row['group_id'], 'self_assessment' => "$selfa");
    }
}
if(isset($assess_array)) {
    $items = json_encode($assess_array);
    $assessed_items_js = "var assessed_items = $items;\n\n";
} else
    $assessed_items_js = "var assessed_items = [];\n\n";
if(isset($self_assessed_array)) {
    $sa_item = json_encode($self_assessed_array);
    $self_assessed_item_js = "var self_assessed_item = $sa_item;\n\n";
} else
    $self_assessed_item_js = "var self_assessed_item = [];\n\n";

if(empty($form)) $form = "";

$form .= $this->fetchEducatorAppJS($entry_id, $this->member_id, $student_screen_name, $student_email, $assessed_items_js, $self_assessed_item_js);
return $form;
}

public function studentJavascript() {
$emptyUserProfile = "";	
$form = "";

if($this->id !== "guest") {
       
// get item directly, as self assessed regardless of whether or not it has been assessed by an educator. 
$sql = "SELECT title.entry_date, data.entry_id, title.author_id, data.field_id_6 AS self_assessment, student.screen_name, student.email, student.group_id, title.title
FROM exp_channel_data data, exp_channel_titles title, otca_evidence ev, exp_members student WHERE data.entry_id = ev.entry_id
AND title.entry_id = ev.entry_id AND student.member_id = title.author_id AND student.member_id = '$this->member_id' AND ev.last_assessed = '0'";

$query =  ee()->db->query($sql);
$numrows = $query->num_rows();
$self_assessed_array = array();
if ($query->num_rows() > 0)	
{
	foreach($query->result_array() as $row) // returns all self-assessed matrix statements for this evidence
	{
		$self_assessment = $row['self_assessment'];
		$current = $row['entry_id'] == $this->id;
		$self_assessed_array[] = array('is_current_entry' => $current, 'entry_date' => ee()->localize->format_date('%D, %F %d, %Y',$row['entry_date']),'entry_id' => $row['entry_id'], 'title' => $row['title'], 'group_id' => $row['group_id'], 'self_assessment' => "$self_assessment");
	}
}

$sql = "SELECT * FROM (SELECT title.entry_date, data.entry_id, title.author_id, title.title, data.field_id_6 as self_assessment, av.matrix_ids as supervisor_assessment, av.feedback, av.date_assessed, m.screen_name, m.email, m.group_id FROM  exp_channel_data data, exp_channel_titles title , otca_evidence ev, otca_evidence_validated av, exp_members m, exp_members student WHERE data.entry_id = ev.entry_id AND title.entry_id = ev.entry_id AND ev.entry_id = av.evidence_id AND m.member_id = av.assessor_id AND student.member_id = title.author_id AND student.member_id ='$this->member_id' ORDER BY av.date_assessed DESC, m.group_id, data.entry_id) ua";

$debugsql = $sql;

$query =  ee()->db->query($sql);

if ($query->num_rows() > 0)
{
	$rightnow = ee()->localize->now;
	$mostrecent = 0;
	foreach($query->result_array() as $row) // returns all assessed matrix statements for this evidence
       {	
		$sa = $row['supervisor_assessment'];
		$selfa = $row['self_assessment'];
		$date_assessed = ee()->localize->format_date('%D, %F %d, %Y %g:%i:%s%a', $row['date_assessed']);
		$current = $row['entry_id'] == $this->id;
		$assess_array[] = array('raw_date' => $row['date_assessed'], 'is_current_entry' => $current,'entry_date' => ee()->localize->format_date('%D, %F %d, %Y %g:%i:%s%a',$row['entry_date']), 'entry_id' => $row['entry_id'], 'title' => $row['title'] /* added 5/06/13 */, 'date_assessed' => $date_assessed, 'supervisor_assessment' => "$sa", 'screen_name' => $row['screen_name'], 'email' => $row['email'], 'group_id' => $row['group_id'], 'self_assessment' => "$selfa", 'feedback' => $row['feedback']);
	}
}
} else {
	$emptyUserProfile = "window.userProfile = {
			\"reflections\" : {},
			\"startNewCycle\" : false,
			\"steps\" : [],
			\"level\" : 0,
			\"beginner\" : true,
			\"objectives\" : [],
			\"history_id\" : 0,
			\"title\" : \"\",
			\"time\" : 0
	}; 
	
	window.stepDefinitions = [ \"null\", \"Request for Service\", \"Information Gathering\", \"Occupational Assessment\", \"Identification of Occupational Issues\", \"Goal Setting\", \"Intervention\",
\"Evaluation\", \"Being a Professional\" ];"; 
}

if(isset($assess_array)) {
	$items = json_encode($assess_array);
	$assessed_items_js = "var assessed_items = $items;\n\n";
} else
    $assessed_items_js = "var assessed_items = [];\n\n";
if(isset($self_assessed_array)) {
	$sa_item = json_encode($self_assessed_array);
	$self_assessed_item_js = "var self_assessed_item = $sa_item;\n\n";
} else
    $self_assessed_item_js = "var self_assessed_item = [];\n\n";

$form .= self::fetchStudentAppJS($this->id, $assessed_items_js, $self_assessed_item_js, $emptyUserProfile, ee()->TMPL->tagdata);
return $form;
}

private static function fetchStudentAppJS($id, $assessed_items_js, $self_assessed_item_js, $emptyUserProfile = "", $info) {
    $id = is_string($id)?"\"$id\"":$id;
    
    $selector = ee()->TMPL->fetch_param('jquery-selector');
    $legend_title = ee()->TMPL->fetch_param('legend-title');
    $current = ee()->TMPL->fetch_param('legend-current');
    $previous =  ee()->TMPL->fetch_param('legend-previous');
    $waiting =  ee()->TMPL->fetch_param('legend-waiting');
    $colors = explode(",", ee()->TMPL->fetch_param('legend-colors'), 3);
    $step_label = ee()->TMPL->fetch_param('step-label');
    
    foreach($colors as $key => $color) {
	$colors[$key] = trim($color);
    }
    
    $info = json_encode($info);
    
    ob_start();
	include 'studentEvidencingAppJS.php';
    $js = ob_get_clean();

return $js;
}
   
private static function fetchEducatorAppJS($entry_id, $student_id, $student_screen_name, $student_email, $assessed_items_js = "", $self_assessed_item_js = "") {
        
    ob_start();
     include 'educatorEvidencingAppJS.php';
    $js = ob_get_clean();

return $js;
}
    public static function usage()
    {
        ob_start();  ?>

The Matrix Plugin outputs HTML Rubric tables with additional features specified in the database.

    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }
}