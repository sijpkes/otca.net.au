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

//include "libs/JSMin.php";

$plugin_info = array(
	'pi_name' => 'OTCA Ajax Functions',
	'pi_version' => '1.0.0',
	'pi_author' => 'Paul Sijpkes',
	'pi_author_url' => '',
	'pi_description' => 'OTCA Ajax functions.',
	'pi_usage' => Otca_ajax::usage()
);

class Otca_ajax {
    
    public $return_data = "";
    private $id = 0;
    private $student_id = 0;
    private $member_id = 0;
    //private $info = "";
    
    public function __construct()
    {
        //$this->id = ee()->TMPL->fetch_param('id');
         $this->member_id = ee()->session->userdata('member_id');
    }
    
    public function user_status_history() {
        $result_array = array();  
        $json = json_encode($result_array);  
            
        $query = ee()->db->query("SELECT a.`id`, a.`title`, a.`time`, (a.`id` = b.`history_id`) is_current 
                                    FROM `otca_user_status_history` a, `otca_user_status` b
                                        WHERE a.`member_id` = b.`member_id` AND a.`member_id` = '$this->member_id()'");
        
        if ($query->num_rows() > 0)
        {
            $is_current_set = FALSE;
            foreach($query->result_array() as $row) 
            {
                $result = new stdClass();
                $result->id = $row['id'];
                $result->title = $row['title'];
                $result->time = ee()->localize->format_date('%D, %F %d', intval($row['time']));
                
                /* this is a fail safe to avoid multiple current practice placements */
                if($row['is_current'] == 1 && ! $is_current_set) {
                    $result->is_current = $row['is_current'];
                    $is_current_set = TRUE;
                } else {
                    $result->is_current = 0;
                }
                $result_array[] = $result;  
            }
            
            $json = json_encode($result_array);
        } 
        
        return $json;
    }
    
    /*
     * Currently not implemented within the otca site
     * 
     */
    public function evidence_learning_contract() {
        $sql = "SELECT * FROM (select `data`.`entry_id`, `data`.`field_id_6` as `self_assessment`, 
                av.matrix_ids as supervisor_assessment, av.date_assessed, ev.filename 
                FROM `exp_channel_data` `data` 
                LEFT JOIN (exp_channel_titles title , otca_evidence ev, otca_evidence_validated av) 
                ON (data.entry_id = ev.entry_id AND title.entry_id = ev.entry_id AND ev.entry_id = av.evidence_id) 
                WHERE title.author_id = '$this->member_id' 
                ORDER BY data.entry_id, av.date_assessed desc) ua 
                GROUP BY ua.entry_id";
                
        $json = json_encode(array());
        
        $comparison_query = ee()->db->query($sql);
        
        if ($comparison_query->num_rows() > 0)
        {
            $json = $comparison_query->result_array();
        }
        
        return json_encode($json);
    }

public static function usage()
{
    ob_start();  ?>

This plugin will eventually contain all AJAX functions used by the otca.net.au website. 

    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }
}
