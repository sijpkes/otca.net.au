<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'EEPortfolio Student List',
	'pi_version' => '1.0.0',
	'pi_author' => 'Paul Sijpkes',
	'pi_author_url' => '',
	'pi_description' => 'Display student data to an educator/teacher.',
	'pi_usage' => Studentlist::usage()
);

class Studentlist {
    
    public $return_data = "";
    
    public function __construct()
    {
    $member_id = ee()->session->userdata('member_id');
    if($member_id == 0) return;
    
    $liclass = ee()->TMPL->fetch_param('list-class');
    $field_ids = ee()->TMPL->fetch_param('member_field_ids');
    $sort_on = ee()->TMPL->fetch_param('sort_on');
    
    $ids = explode(",", $field_ids);
    
    $sql = "SELECT `m_field_id` `id`, `m_field_label` `label`, `m_field_name` `name` FROM `exp_member_fields` WHERE `m_field_id` IN ($field_ids)";
    $query = ee()->db->query($sql);
    
    $ids_labels = array();
    if($query->num_rows() > 0) {
	foreach($query->result_array() as $row) {
	    $ids_labels[$row['id']] = array("label" => $row['label'], "name" => $row['name']);
	}  
    }
    
    $prefix = "m_field_id_";
    $selectstr = "";
    $order_by = "";
    foreach($ids as $key => $postfix) {
	$selectstr .= ", `data`.`".$prefix.$postfix."`";
	/*if($member_id == 154) {
		$this->return_data .= "$key , $postfix ==> $sort_on<br>";
	}*/
	if($postfix == $sort_on) {
	    $order_by = "`data`.`".$prefix.$postfix."`";
	}
    }
    
    $sql = "SELECT `access`.`student_id` , `mem`.`screen_name` , `mem`.`email` $selectstr
	    FROM `otca_evidence_access` `access`
	    LEFT JOIN `exp_members` `mem` ON ( `access`.`student_id` = `mem`.`member_id` )
	    LEFT JOIN `exp_member_data` `data` ON ( `access`.`student_id` = `data`.`member_id` )
	    WHERE `access`.`educator_id` = '$member_id'
	    ORDER BY $order_by ASC";
    /*if($member_id == 154) {
	$this->return_data .= $sql;
    }  */  
    if(strlen($liclass) === 0)  {
	$liclass = "";
    } else { 
	$liclass = " class='$liclass'";
    }
    $query = ee()->db->query($sql);
    
    $this->return_data .= "<table$liclass>";
    $this->return_data .= "<tr><th>Screen Name</th><th>Student Number</th><th>Last Name</th><th>First Name</th><th>Email</th><th>PRACSOT</th></tr>";
    if($query->num_rows() > 0) {
	foreach($query->result_array() as $row) 
	{
	    $student_no = $row['m_field_id_3']===NULL?'Not Available':$row['m_field_id_3'];
	    $last_name = $row['m_field_id_2']===NULL?'Not Available':$row['m_field_id_2'];
	    $first_name = $row['m_field_id_1']===NULL?'Not Available':$row['m_field_id_1'];
            $this->return_data .= "<tr><td><a href='/pages/student-shared-resources/$row[student_id]/$row[screen_name]'> $row[screen_name]</td> <td>$student_no</td> <td>$last_name</td> <td>$first_name</td> </a>  <td><a href='mailto:$row[email]'>$row[email]</a> </td> <td> <a href='/pracsot?sid=$row[student_id]'>PRACSOT Progress</a></td></tr>";		
	}
    } else {
            $this->return_data .= "<tr><td>No students have yet assigned you as their educator.</td></tr>";
    }
        $this->return_data .= "</table>";
    }
    
    public static function usage()
    {
        ob_start();  ?>

The Studentlist Plugin simply outputs a
list of all the students who have nominated themselves to an educator.

Use the class parameter to assign CSS styling to the list.

    {exp:studentlist class="my-css-list-class"}


    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }
}
