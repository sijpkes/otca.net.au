<?php /**
*The MIT License (MIT)
*
*Copyright (c) 2013 Paul Sijpkes.
*
*Permission is hereby granted, free of charge, to any person obtaining a copy
*of this software and associated documentation files (the "Software"), to deal
*in the Software without restriction, including without limitation the rights
*to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
*copies of the Software, and to permit persons to whom the Software is
*furnished to do so, subject to the following conditions:
*
*The above copyright notice and this permission notice shall be included in
*all copies or substantial portions of the Software.
*
*THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
*IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
*FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
*AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
*LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
*OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
*THE SOFTWARE.
*/ ?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'OT Registration Alert',
	'pi_version' => '1.0.0',
	'pi_author' => 'Paul Sijpkes',
	'pi_author_url' => '',
	'pi_description' => 'Display student data to an educator/teacher.',
	'pi_usage' => Register_alert::usage()
);

class Register_alert {
    
    public $return_data = "";
    
    public function __construct()
    {
    if(end(ee()->uri->segment_array()) === 'details-required-message') return;
    $member_id = ee()->session->userdata('member_id');
    $group_id = ee()->session->userdata('group_id');
    
    $mem_group_ids = ee()->TMPL->fetch_param('alert_groups');
    
    $alert_groups = explode(',', $mem_group_ids);
    
    if($member_id == 0 || ! in_array($group_id, $alert_groups) ) return;
    
    $sql = "SELECT m_field_id_1 first_name, m_field_id_2 last_name, m_field_id_3 student_no,
	    m_field_id_4 institution FROM exp_member_data WHERE member_id = '$member_id'";
    
    $query = ee()->db->query($sql);
    $message = "";        
    if($query->num_rows() > 0) {
	foreach($query->result_array() as $row) {
            if($row['first_name'] === NULL ||
               $row['last_name'] === NULL ||
               $row['student_no'] === NULL ||
               $row['institution'] === NULL) {
                $message .= ee()->functions->redirect("/system/details-required-message");
            }
	}  
    }
    
    return;
    }
    
    public static function usage()
    {
        ob_start();  ?>

Registration alert for users that haven't updated their details.


    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }
}
