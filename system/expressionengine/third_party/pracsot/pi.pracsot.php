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
 */
 ?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'Practice Placement Javascript Functions',
	'pi_version' => '1.0.0',
	'pi_author' => 'Paul Sijpkes',
	'pi_author_url' => '',
	'pi_description' => 'Renders PRACSOT tables.',
	'pi_usage' => Pracsot::usage()
);

class Pracsot {
    
    private $member_group_id = 0;
    private $allowed_group_ids = array();
    private $no_access_message = "";
    
    
    public function __construct() {
        $this->member_group_id = ee()->session->userdata('group_id');
        $this->allowed_group_ids = explode(",", ee()->TMPL->fetch_param('allowed_group_ids'));
        $this->no_access_message =  ee()->TMPL->fetch_param('no_access_message');
    }
	
	public function unit_table() {
		$member_id = ee()->session->userdata('member_id');

			$group_id = ee()->session->userdata('group_id');
			if($group_id == 6 || $group_id == 7 || $group_id == 9 || $group_id == 1) {
				$member_id = ee()->input->get('sid');
			}
			
			define('BEING_A_PROFESSIONAL', 1); // check for holistic item which spans all PRACSOT levels
			
			$element = ee()->input->get('element');
			
			$unit_code = substr($element, 0, 1);
			$unit_color = "unit$unit_code-color";
			$unit_heading = "unit$unit_code-heading";
			$unit_bgcolor = "unit$unit_code-bgcolor";
			$unit_sub_bgcolor = "unit$unit_code-sub-bgcolor";
			
			if(strlen($element) < 3) return;
			
			$sql = "SELECT `id`,`header` FROM `otca_pracsot_headers` WHERE id = '$element'";
			
			$query = ee()->db->query($sql);
			
			if ($query->num_rows() > 0)
			{
				foreach($query->result_array() as $row)
			    {
					$header = $row['header'];
				}
			}
			
			$sql = "SELECT `id`, `question` FROM `otca_pracsot` WHERE `id` LIKE('$element.%')";
			
			$query = ee()->db->query($sql);
			
			if(!empty($member_id)) { 
			$assess_sql = "SELECT * FROM (SELECT `data`.`entry_id`, `data`.`field_id_6` as `self_assessment`, 
							`av`.`matrix_ids` as `supervisor_assessment`, `av`.`date_assessed` FROM  
							`exp_channel_data` `data` LEFT JOIN (`exp_channel_titles` `title` , `otca_evidence` `ev`, 
							`otca_evidence_validated` `av`) ON (`data`.`entry_id` = `ev`.`entry_id` AND 
							`title`.`entry_id` = `ev`.`entry_id` AND `ev`.`entry_id` = `av`.`evidence_id`) WHERE 
							`title`.`author_id` = '$member_id' order by `data`.`entry_id`, `av`.`date_assessed` desc) `ua` 
							GROUP BY `ua`.`entry_id`";
			 
			$comparison_query = ee()->db->query($assess_sql);

			if ($comparison_query->num_rows() > 0)
			{
				foreach($comparison_query->result_array() as $row)
			    {	
					// add option to compare later ...? maybe
					$temp_array = json_decode("[$row[supervisor_assessment]]");
					foreach($temp_array as $item) {
						$variable_array['evidence'] = $item;
					}
				}
			}
			
				$output = ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $variable_array);	
			} else {
				$variable_array['evidence'] = 'Nothing to show!';
				$output = ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $variable_array);
			}
}
	
public static function usage()
{
        ob_start();
?>

Renders the PRACSOT content page and tables.

<?php
$buffer = ob_get_contents();
ob_end_clean();

return $buffer;
}
}
