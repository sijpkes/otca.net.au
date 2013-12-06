/*
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
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'EEPortfolio Folio TESTING',
	'pi_version' => '1.0.0',
	'pi_author' => 'Paul Sijpkes',
	'pi_author_url' => '',
	'pi_description' => 'Folio view plugin',
	'pi_usage' => Folio_test::usage()
);

class Folio_test {

private $grouped_diary_entry_types = array('prepare-statement' => "Introductory Letter to Educator",
                                         'self-assess-statement' => 'Self Assessment'
                                         );
private $member_id = 0;

public function __construct() {
    $this->member_id = ee()->session->userdata('member_id');
}

public function student_timeline_container() {
$this->member_id = ee()->session->userdata('member_id');
if($this->member_id == 0) return;
$output = "";
$diary = ee()->input->get('diary');
$evidence = ee()->input->get('evidence');
$contracts = ee()->input->get('contracts');

if($evidence == 1) {
/* evidence query */
$sql = "SELECT `ev`.`upload_time` FROM `exp_channel_titles` `ti`, `otca_evidence` `ev` WHERE `ti`.`entry_id` = `ev`.`entry_id` AND `ti`.`author_id` = '$this->member_id' ORDER BY ev.upload_time DESC";

$query =  ee()->db->query($sql);

if ($query->num_rows() > 0)
{
	foreach($query->result_array() as $row) // returns all evidence entries for this practice cycle
    {
		$date_array[] = $row['upload_time'];
	}
}
}
 
if($diary == 1) {
$sql = "SELECT `creation_date` FROM `otca_diary` WHERE `member_id`='$this->member_id' ORDER BY `creation_date` DESC";

$query =  ee()->db->query($sql);

if ($query->num_rows() > 0)
{
	foreach($query->result_array() as $row) // returns all diary entries for this user
    {
		$date_array[] = $row['creation_date'];
	}
}
}
 
if($contracts == 1) {
$sql = "SELECT `id`, `time`,`title` FROM `otca_user_status_history` WHERE `member_id` = '$this->member_id' ORDER BY `time` DESC";

$query =  ee()->db->query($sql);
    if ($query->num_rows() > 0)
    {
        foreach($query->result_array() as $row) // returns all learning contracts
        {
                    $date_array[] = $row['time'];
        }
    }
}

$time = time();

$output_date = date('l dS M, Y g:i a',  ee()->localize->now);
$current_date = "";
$output .= "<p>Current time: ".$output_date." </p>";

if(isset($date_array) && count($date_array) > 0) {

	$output .= "<div class='timeline-center timeline' data-end='0' data-start='$time'>";
		 	$output .= "<div class='timeline-label'>Portfolio</div><!-- /div -->";
	$output .= "</div>";
        
} else {
	$output .= "Nothing to show!";
}

return $output;
}

public function student_timeline_list() {
if($this->member_id == 0) return;

$output = "";

$start = ee()->input->get('start');
$end =  ee()->input->get('end');
$evidence = ee()->input->get('evidence');
$diary = ee()->input->get('diary');
$contracts = ee()->input->get('contracts');

$record_array = array();
$countDuplicates = array();

$diary_highlights = array();
$contract_highlights = array();
$evidence_highlights = array();
$diary_hl_colors = array();
$contract_hl_colors = array();
$evidence_hl_colors = array();

$hlSelectControl = "<br clear='all'><br clear='all'><label for='highlight'>Highlight for educators:  </label><select id=\"highlight\" title='Highlighted items will appear at the top of the page when your educator accesses your portfolio. Highlighting will automatically show hidden diary entries.' data-code='%code%'> 
    <option value='none'>This has been updated</option>
    %options%
    </select>%delete_option%";
    
$deleteControl = "<img class=\"exit\" src=\"/img/close-icon.png\" style=\"float: right;\">";   
    

function generateOptions($selectedColor) {
    $colors = array("Yellow" => "#FFFF00", "Light Green" => "#81F781", "Dark Green" => "#0D6E32",
                    "Pink" => "#F781F3", "Cyan" => "#00FFFF", "Dark Blue" => "#374DCC",
                    "Light Blue" => "#38A6EB", "Red" => "#FA0A2A", "Chestnut" => "#C32148");
    $str = "";
    foreach($colors as $name => $colorCode) {
    $selected = "";
        if($colorCode == $selectedColor) {
            $selected = "selected";
        }
        $str .= "<option value='$colorCode' $selected>$name</option>";
    }
    return $str;
}    
    
/* get highlighted items*/
$sql = "SELECT `id`, `color`, `type`, `entry_id` FROM `otca_folio_highlights` WHERE `member_id` = '$this->member_id' LIMIT 0, 500";

$query =  ee()->db->query($sql);

	if ($query->num_rows() > 0)
	{
	    foreach($query->result_array() as $row) // returns all evidence entries for this practice cycle
	    {
			if($row['type'] === 'diary') {
				$diary_hl_colors[$row['entry_id']] = $row['color'];
			}
			else if($row['type'] === 'contract') {	
				$contract_hl_colors[$row['entry_id']] = $row['color'];
			}
			else if($row['type'] === 'evidence') {
				$evidence_hl_colors[$row['entry_id']] = $row['color'];
			}
	    }
	}

if($evidence == 1) {
/* evidence query */
$sql = "SELECT `ev`.`upload_time`, `ev`.`entry_id`, `ev`.`filename`, `ev`.`last_assessed`, `ti`.`title`, `data`.`field_id_4` as `description`, `data`.`field_id_3` as `file_url` FROM `exp_channel_titles` `ti`, `otca_evidence` `ev`, `exp_channel_data` `data` WHERE `ti`.`entry_id` = `ev`.`entry_id` AND `data`.`entry_id`= `ev`.`entry_id` AND `ti`.`author_id` = '$this->member_id' AND `ev`.`upload_time` >= $end AND `ev`.`upload_time` <= $start ORDER BY `ev`.`upload_time` DESC";
//echo $sql;

$query =  ee()->db->query($sql);

if ($query->num_rows() > 0)
{
    foreach($query->result_array() as $row) // returns all evidence entries for this practice cycle
    {	
	$upload_time = $row['upload_time'];
        
        $styling = "";
	
        $selectedColor = "none";
	if(array_key_exists($row['entry_id'], $evidence_hl_colors)) {
		$styling = "style='border: thick solid ".$evidence_hl_colors[$row['entry_id']]."'";
                $selectedColor = $evidence_hl_colors[$row['entry_id']];
	}
        $options = generateOptions($selectedColor);
        
	if(array_key_exists($upload_time, $record_array)) {
		if(!array_key_exists($upload_time, $countDuplicates)) {
			$countDuplicates[$upload_time] = 1;
		} else {
			$countDuplicates[$upload_time] =  $countDuplicates[$upload_time] + 1;
		}
		$upload_time = $upload_time . "_" . $countDuplicates[$upload_time];
	}
$localEntryTime = date('d/M/Y g:i:s a', ee()->localize->set_localized_time($row['upload_time']));

$unique_code = "evidence_".$this->member_id."_".$row['entry_id'];
$hc = str_replace("%code%", $unique_code, $hlSelectControl);
$hc = str_replace("%options%", $options, $hc);
$hc = str_replace("%delete_option%", $deleteControl, $hc);

$title = strlen($row['title']) > 0? $row['title']:"";
$filename = strlen($row['filename']) > 0? $row['filename']:"";
$description = strlen($row['description']) > 0? "<p><em>$row[description]</em></p>":"";

/* work out whether this has been assessed within the last 30 days */
$thirty_days_secs = 2592000;
$recent = time() - $thirty_days_secs;
$raw_days = time() - $row['last_assessed'];
$days = floor($raw_days / 60 / 60 / 24);
$days_str = $days == 0 ? "today" : "$days day(s) ago.";
$recently_assessed = $row['last_assessed'] > $recent ? " <span style='color:red'><strong>*** Assessed $days_str ***</strong></span>" : "";
$file_link = strlen($row['file_url']) > 0? "<a href='$row[file_url]/$row[entry_id]' title='Download this file' target='_blank' style='color: #639'>Open attached file in a new tab</a>":"";

$file_link = preg_replace('~\{(.*?)\}~', '/download/secure/', $file_link);

$record_array[$upload_time] = "<li $styling><span style=\"font-family: courier,courier-new,sans-serif; color: rgb(68, 68, 68); font-size:15px\"><p style=\"margin-top: 0\"><strong><u>Evidence Entry</u></strong><br><a href='/pages/assessed-matrix/$row[entry_id]/$row[title]' style='color: blue; line-height: 22px'>View Assessed OTCEM Competency Statements for this Evidence</a></p> <p> $title $recently_assessed
</p> $description $file_link <em style=\"float: right; line-height:0px\">$localEntryTime</em></span>$hc</li>";

	}
}
}

if($diary == 1) {
$sql = "SELECT `entry_id`, `entry_text`, `last_updated`, `creation_date`, `hidden`, `tag`, `current_practice_cycle` FROM `otca_diary`
    WHERE `member_id`='$this->member_id' ORDER BY `last_updated` DESC";

//echo "<code>$sql</code><br><br>";  
    /*  AND `creation_date` >= $end AND `creation_date` < $start */

$query =  ee()->db->query($sql);

if ($query->num_rows() > 0)
{
    $results = $query->result_array();
 
    $grouped_entry_set = array();
   
    $is_grouped = FALSE;
    $my_tag = "";
    $postfix = "";
    $my_label = "";
      
    foreach($results as $key => $row) // groups diary entries by tag name
    {
    $output .= "$key =====> ".json_encode($row)."<br><br>";	
	
    $is_grouped = FALSE;
        foreach($this->grouped_diary_entry_types as $tag => $label) {
                if(strpos($row['tag'], $tag) !== false) {
		    $postfix = $tag;
                    $my_tag = $row['tag'];
		    $is_grouped = TRUE;
		    $my_label = $label;
		}
	}
	if($is_grouped === TRUE) {
	     $output .= "is grouped<br><br>";
	     $text_group_id = $row['current_practice_cycle']."_$postfix";
		if(!isset($grouped_entry_set[$text_group_id])) {
			$grouped_entry_set[$text_group_id] = array();
		 }
		   
                    $hidden = $row['hidden'] == 1 ? "checked='checked'" : "";
		
                    $item_data = array('text' => $row['entry_text'], 'date' => $row['last_updated'], 'label' => $my_label,
                                        'entry_id' => $row['entry_id'], 'hidden' => $hidden);
		    
		    $output .= "$my_tag >>> ".json_encode($item_data);
                    $grouped_entry_set[$text_group_id][$my_tag] = $item_data;
              
                    unset($results[$key]);
	}
    }
    
    $output .= "<br><hr><br>" . json_encode($grouped_entry_set) . "<br><hr><br>";
        
    foreach($grouped_entry_set as $group_key => $grouped_item) {
	ksort($grouped_item);
	
	$output .= "$group_key =====> ".json_encode($grouped_item)."<br><br>";
	

        $grouped_entry_text = "";
        $group_date = null;
        $hidden = "";
        $styling = "";
	
	$group_type = end(explode("_", $group_key));
	
	$colorSet = false;
        foreach($grouped_item as $item) {
            $grouped_entry_text .= "<p>$item[text]</p>";    
            $group_date = $item['date'];
            $group_label = $item['label'];
            $group_entry_id = $item['entry_id'];
            $group_hidden = $item['hidden'];
	    
	    $selectedColor = "none";
	    if(array_key_exists($group_entry_id, $diary_hl_colors) && !$colorSet) {
		    $styling = "style='border: thick solid ".$diary_hl_colors[$group_entry_id]."'";
		    $selectedColor = $diary_hl_colors[$group_entry_id];
		    $colorSet = true;
	    }
        }
        
        
        $options = generateOptions($selectedColor);
        
        $groupLocalTime = ee()->localize->format_date('%d %M %Y', $group_date);
	$group_unique_code = "diary_".$this->member_id."_".$group_entry_id;
        $ghc = str_replace("%code%", $group_unique_code, $hlSelectControl);
        $ghc = str_replace("%options%", $options, $ghc);
        $ghc = str_replace("%delete_option%", $deleteControl, $ghc);
        $record_array[$group_date] = "<li $styling class='$group_type'><label style='float: right'><input id='hide' type='checkbox' data-id='$group_entry_id' data-entrytype='checkbox' name='action' title='Hide this item from educators.  Untick this if you wish to share this diary entry with your educators.' $group_hidden> Hidden</label><span style=\"font-family: cursive,helvetica,sans-serif; color: rgb(68, 68, 68); font-size:15px\"><p style=\"margin-top: 0\"><strong><u>$group_label</u></strong></p> <em>$grouped_entry_text</em> <em style=\"float: right\">$groupLocalTime</em></span>$ghc</li>";        
    }    
    
    foreach($results as $row) // returns all diary entries for this practice cycle
    {
		$creation_date = $row['last_updated'];
                 $styling = "";
                
                $selectedColor = "none";
		if(array_key_exists($row['entry_id'], $diary_hl_colors)) {
		    $styling = "style='border: thick solid ".$diary_hl_colors[$row['entry_id']]."'";
                    $selectedColor = $diary_hl_colors[$row['entry_id']];
		}
                $options = generateOptions($selectedColor);
                 
		if(array_key_exists($creation_date, $record_array)) {
			if(!array_key_exists($creation_date, $countDuplicates)) {
				$countDuplicates[$creation_date] = 1;
			} else {
				$countDuplicates[$creation_date] =  $countDuplicates[$creation_date] + 1;
			}
			$creation_date = $creation_date . "_" . $countDuplicates[$creation_date];
		}
		$localEntryTime = ee()->localize->format_date('%d %M %Y', $row['last_updated']);
		$hide_id = $row['entry_id'];
                $hidden = $row['hidden'] == 1 ? "checked='checked'" : "";
                $unique_code = "diary_".$this->member_id."_".$row['entry_id'];
                $hc = str_replace("%code%", $unique_code, $hlSelectControl);
                $hc = str_replace("%options%", $options, $hc);
                $hc = str_replace("%delete_option%", $deleteControl, $hc);
		$record_array[$creation_date] = "<li $styling><label style='float: right'><input id='hide' type='checkbox' data-id='$hide_id' data-entrytype='checkbox' name='action' title='Hide this item from educators.  Untick this if you wish to share this diary entry with your educators.' $hidden> Hidden</label><span style=\"font-family: cursive,helvetica,sans-serif; color: rgb(68, 68, 68); font-size:15px\"><p style=\"margin-top: 0\"><strong><u>Diary Entry</u></strong></p> <em>$row[entry_text]</em> <em style=\"float: right\">$localEntryTime</em></span>$hc</li>";
	}
}
}
if($contracts == 1) {
    $sql = "SELECT `id`, `time`,`title` FROM `otca_user_status_history` WHERE `member_id` = '$this->member_id' AND `time` >= $end AND `time` < $start ORDER BY `time` DESC";
    $query =  ee()->db->query($sql);
    
    if ($query->num_rows() > 0)
    {
        foreach($query->result_array() as $row) // returns all diary entries for this practice cycle
        {
            $creation_date = $row['time'];
             $styling = "";
	    
            $selectedColor = "none";
	    if(array_key_exists($row['id'], $contract_hl_colors)) {
		$styling = "style='border: thick solid ".$contract_hl_colors[$row['id']]."'";
                $selectedColor = $contract_hl_colors[$row['id']];
	    }
            $options = generateOptions($selectedColor);
            
            $localEntryTime =  ee()->localize->format_date('%d %M %Y', $creation_date);
            
            $unique_code = "contract_".$this->member_id."_".$row['id'];
            $hc = str_replace("%code%", $unique_code, $hlSelectControl);
            $hc = str_replace("%options%", $options, $hc);
            $hc = str_replace("%delete_option%", "", $hc); /* no delete option for learning contracts */
            $record_array[$creation_date] = "<li $styling><span style=\"font-family: arial,helvetica,sans-serif; color: black; font-size:12pt\"><p style=\"margin-top: 0\"><strong>Learning Contract</strong></p> <a href='/pages/learning-contract?id=$row[id]'>$row[title]</a> <span style=\"float: right\"><strong>Started: $localEntryTime</strong></span>$hc</li>";
        }
    }
}
// sort array by key to maintain date ordering
krsort($record_array);
$output .= "<ul>";
foreach($record_array as $list_item){
	$output .= $list_item;
}

$output .= "</ul>";

return $output;
}

public function educator_timeline_list() {
$this->member_id = ee()->session->userdata('member_id');
if($this->member_id == 0) return;
$output = "";

$start = ee()->input->get('start');
$end =  ee()->input->get('end');
$evidence = ee()->input->get('evidence');
$diary = ee()->input->get('diary');
$suid = ee()->input->get('suid');
$contracts = ee()->input->get('contracts');
$ho = ee()->input->get('ho'); /* highlights only */

$record_array = array();
$countDuplicates = array();
$diary_highlights = array();
$contract_highlights = array();
$evidence_highlights = array();
$diary_hl_colors = array();
$contract_hl_colors = array();
$evidence_hl_colors = array();

//if(isset($ho) && $ho > 0) {
/* get highlighted items*/
$sql = "SELECT `id`, `color`, `type`, `entry_id` FROM `otca_folio_highlights` WHERE `member_id` = '$suid' LIMIT 0, 30";

$query =  ee()->db->query($sql);

	if ($query->num_rows() > 0)
	{
	    foreach($query->result_array() as $row) // returns all evidence entries for this practice cycle
	    {
			if($row['type'] === 'diary') {
				$diary_highlights[] = $row['entry_id'];
				$diary_hl_colors[$row['entry_id']] = $row['color'];
			}
			else if($row['type'] === 'contract') {	
				$contract_highlights[] = $row['entry_id'];
				$contract_hl_colors[$row['entry_id']] = $row['color'];
			}
			else if($row['type'] === 'evidence') {
				$evidence_highlights[] = $row['entry_id'];
				$evidence_hl_colors[$row['entry_id']] = $row['color'];
			}
	    }
	}
//}

if($evidence == 1) {

/*$highlight_sql = "`ev`.`upload_time` >= $end AND `ev`.`upload_time` < $start";
if(count($evidence_highlights) > 0) {
	$hids = implode(",", $evidence_highlights);
	$highlight_sql = "`ev`.`entry_id` IN ($hids)";
}*/

$sql = "SELECT `ev`.`upload_time`, `ev`.`entry_id`, `ev`.`filename`, `ti`.`title`, `data`.`field_id_4` as `description`, `data`.`field_id_3` as `file_url` FROM `exp_channel_titles` `ti`, `otca_evidence` `ev`, `exp_channel_data` `data` WHERE `ti`.`entry_id` = `ev`.`entry_id` AND `data`.`entry_id`= `ev`.`entry_id` AND `ti`.`author_id` = '$suid' ORDER BY `ev`.`upload_time` DESC";

$query =  ee()->db->query($sql);

if ($query->num_rows() > 0)
{
    foreach($query->result_array() as $row) // returns all evidence entries for this practice cycle
    {	
	$upload_time = $row['upload_time'];
	$styling = "";
	    
	if(array_key_exists($row['entry_id'], $evidence_hl_colors)) {
		$styling = "style='border: thick solid ".$evidence_hl_colors[$row['entry_id']]."'";
	}
	
	if(array_key_exists($upload_time, $record_array)) {
		if(!array_key_exists($upload_time, $countDuplicates)) {
			$countDuplicates[$upload_time] = 1;
		} else {
			$countDuplicates[$upload_time] =  $countDuplicates[$upload_time] + 1;
		}
		$upload_time = $upload_time . "_" . $countDuplicates[$upload_time];
	}
$localEntryTime = ee()->localize->format_date('%d %M %Y', $row['upload_time']);

$title = strlen($row['title']) > 0? $row['title']:"";
$filename = strlen($row['filename']) > 0? $row['filename']:"";
$description = strlen($row['description']) > 0? "<p><em>$row[description]</em></p>":"";
$file_link = strlen($row['file_url']) > 0? "<a href='$row[file_url]/$row[entry_id]' title='Download this file' style='color: #369' target='_blank'>Open attached file in a new tab</a>":"";

$file_link = preg_replace('~\{(.*?)\}~', '/download/secure/', $file_link);

if((isset($ho) && $ho > 0 && !empty($styling)) || (!isset($ho) || empty($ho)) ) {
    $record_array[$upload_time] = "<li $styling><span style=\"font-family: courier,courier-new,sans-serif; color: rgb(68, 68, 68); font-size:15px\"><p style=\"margin-top: 0\"><strong><u>Evidence Entry</u></strong><br><a href='/pages/educator-matrix/$row[entry_id]/$suid/$row[title]' style='color: blue; line-height: 22px'>Assess OTCEM Competency Statements for this Evidence</a></p> <p> $title </p> $description $file_link <em style=\"float: right; line-height:0px\">$localEntryTime</em></span></li>";
}
	}
    }
}

if($diary == 1) {
/*$highlight_sql = "`creation_date` >= $end AND `creation_date` < $start";
if(count($diary_highlights) > 0) {
	$hids = implode(",", $diary_highlights);
	$highlight_sql = "`entry_id` IN ($hids)";
}*/
/* diary query */
$sql = "SELECT `entry_id`, `entry_text`, `tag`, `creation_date`, `last_updated`, `current_practice_cycle`, `hidden` FROM `otca_diary` WHERE `member_id`='$suid' AND `hidden` = '0' ORDER BY `last_updated` DESC";
$query =  ee()->db->query($sql);

if ($query->num_rows() > 0)
{
    $results = $query->result_array();
    $grouped_entry_set = array();
    //$ddeCount = 0; //duplicate diary entry count
    $empty_item = array('styling' => '', 'selectedColor' => '',
			'date' => '', 'entry_id' => '', 'label' => '',
			'items' => array());
    
    /*
     *
    $is_grouped = FALSE;
    $my_tag = "";
    $postfix = "";
    $my_label = "";
     *foreach($results as $key => $row) // groups diary entries by tag name
    {
    $is_grouped = FALSE;
        foreach($this->grouped_diary_entry_types as $tag => $label) {
                if(strpos($row['tag'], $tag) !== false) {
		    $postfix = $tag;
                    $my_tag = $row['tag'];
		    $is_grouped = TRUE;
		    $my_label = $label;
		}
	}
	if($is_grouped === TRUE) {
		if(!isset($grouped_entry_set[$row['current_practice_cycle']."_$postfix"])) {
		    $grouped_entry_set[$row['current_practice_cycle']."_$postfix"] = array();
		 }
		   
                    $hidden = $row['hidden'] == 1 ? "checked='checked'" : "";
		
                    $item_data = array('text' => $row['entry_text'], 'date' => $row['last_updated'], 'label' => $my_label,
                                        'entry_id' => $row['entry_id'], 'hidden' => $hidden);
                  
                    $grouped_entry_set[$row['current_practice_cycle']."_$postfix"][$my_tag] = $item_data;
              
                    unset($results[$key]);
	}
    }
     *
     */
    
    foreach($results as $key => $row) // groups diary entries by tag name
    {
    $is_grouped = FALSE;
        foreach($this->grouped_diary_entry_types as $tag => $label) {
                if(strpos($row['tag'], $tag) !== false) {
		    $postfix = $tag;
                    $my_tag = $row['tag'];
		    $is_grouped = TRUE;
		    $my_label = $label;
		}
	}
	if($is_grouped === TRUE) { 	      
		    $styling = "";
                    $selectedColor = "none";
		    $entry_id = $row['entry_id'];
		    if(array_key_exists($entry_id, $diary_hl_colors)) {
			 $styling = "style='border: thick solid ".$diary_hl_colors[$entry_id]."'";
			 $selectedColor = $diary_hl_colors[$entry_id];
		    }
		    
		    $item_data = array('entry_id' => $row['entry_id'], 'text' => $row['entry_text']);
                    
		    $unique_id = $row['current_practice_cycle']."_$postfix";
		    
		    if(!isset($grouped_entry_set[$unique_id])) {
                            $grouped_entry_set[$unique_id] = $empty_item;
                    }
		    
		    $grouped_entry_set[$unique_id]['items'][$my_tag] = $item_data;
                    
		    if(!empty($styling) && empty($grouped_entry_set[$unique_id]['styling'])) {    
			    $grouped_entry_set[$unique_id]['styling'] = $styling;
			if(!empty($row['entry_id']) && empty($grouped_entry_set[$unique_id]['entry_id'])) {
				$grouped_entry_set[$unique_id]['entry_id'] = $row['entry_id'];
			}
		    }
		    
		    if(!empty($selectedColor) && empty($grouped_entry_set[$unique_id]['selectedColor'])) {    
			    $grouped_entry_set[$unique_id]['selectedColor'] = $selectedColor;
		    }
		    if(isset($row['last_updated']) && empty($grouped_entry_set[$unique_id]['date'])) {
			    $grouped_entry_set[$unique_id]['date'] = $row['last_updated'];
		    }
		    
		    if(!empty($my_label) && empty($grouped_entry_set[$unique_id]['label'])) {
			$grouped_entry_set[$unique_id]['label'] = $my_label;
		    }
		    
                    unset($results[$key]); 
                }
        }
    }

    foreach($grouped_entry_set as $key => $grouped_item) {
	
        ksort($grouped_item['items']);
        $grouped_entry_text = "";
        $group_date = null;
	$group_entry_id = "";
	$styling = "";
    
	//$output .= var_export($grouped_item);
	$group_date = $grouped_item['date'];
        $group_label = $grouped_item['label'];
	$groupd_hide_id = $grouped_item['entry_id'];
	$styling = $grouped_item['styling'];
	$selectedColor = $grouped_item['selectedColor'];
		
        foreach($grouped_item['items'] as $item) {
            $grouped_entry_text .= "<p>$item[text]</p>";
        }
	
        $groupLocalTime = ee()->localize->format_date("%d %M %Y",$group_date);
        
    if((isset($ho) && $ho > 0 && !empty($styling)) || (!isset($ho) || empty($ho)) ) {
        $record_array[$group_date] = "<li $styling><span style=\"font-family: cursive,helvetica,sans-serif; color: rgb(68, 68, 68); font-size:15px\"><p style=\"margin-top: 0\"><strong><u>$group_label</u></strong></p> <em>$grouped_entry_text</em> <em style=\"margin-left: 500px\">$groupLocalTime</em></span></li>";        
    }
    }
    
    foreach($results as $row) // returns all diary entries for this practice cycle
    {
        
		$creation_date = $row['creation_date'];
		 $styling = "";
	    
		if(array_key_exists($row['entry_id'], $diary_hl_colors)) {
		    $styling = "style='border: thick solid ".$diary_hl_colors[$row['entry_id']]."'";
		}
		
		if(array_key_exists($creation_date, $record_array)) {
			if(!array_key_exists($creation_date, $countDuplicates)) {
				$countDuplicates[$creation_date] = 1;
			} else {
				$countDuplicates[$creation_date] =  $countDuplicates[$creation_date] + 1;
			}
			$creation_date = $creation_date . "_" . $countDuplicates[$creation_date];
		}
		$localEntryTime = date('d/M/Y g:i a', ee()->localize->set_localized_time($row['creation_date']));
		$radio_name = $row['entry_id'];
		if((isset($ho) && $ho > 0 && !empty($styling)) || (!isset($ho) || empty($ho)) ) {
		    $record_array[$creation_date] = "<li $styling><span style=\"font-family: cursive,helvetica,sans-serif; color: rgb(68, 68, 68); font-size:15px\"><p style=\"margin-top: 0\"><strong><u>Diary Entry</u></strong></p> <em>$row[entry_text]</em> <em style=\"float: right\">$localEntryTime</em></span></li>";
		}    
	}
}

if($contracts == 1) {
/*$highlight_sql = "`time` >= $end AND `time` < $start";
if(count($contract_highlights) > 0) {
	$hids = implode(",", $contract_highlights);
	$highlight_sql = "`id` IN ($hids)";
}*/

    $sql = "SELECT `id`, `time`,`title` FROM `otca_user_status_history` WHERE `member_id` = '$suid' ORDER BY `time` DESC";
    $query =  ee()->db->query($sql);
    if ($query->num_rows() > 0)
    {
        foreach($query->result_array() as $row) // returns all diary entries for this practice cycle
        {
            $creation_date = $row['time'];
	    $styling = "";
	    
	    if(array_key_exists($row['id'], $contract_hl_colors)) {
		$styling = "style='border: thick solid ".$contract_hl_colors[$row['id']]."'";
	    }
	    
            $localEntryTime = date('d/M/Y g:i a', ee()->localize->set_localized_time($creation_date));
            if((isset($ho) && $ho > 0 && !empty($styling)) || (!isset($ho) || empty($ho)) ) {
	       $record_array[$creation_date] = "<li $styling><span style=\"font-family: arial,helvetica,sans-serif; color: black; font-size:12pt\"><p style=\"margin-top: 0\"><strong>Learning Contract</strong></p> <a href='/pages/learning-contract?id=$row[id]&m=$suid'>$row[title]</a> <span style=\"float: right\"><strong>Started: $localEntryTime</strong></span></li>";
	    }		
        }
    }
}
// sort array by key to maintain date ordering
krsort($record_array);
$output .= "<ul>";
foreach($record_array as $list_item){
	$output .= $list_item; 
}
$output .= "</ul>";

return $output;
}

public function educator_timeline_container() {
$this->member_id = ee()->session->userdata('member_id');
if($this->member_id == 0) return;

$output = "";

$group_id = ee()->session->userdata('group_id');

$diary = ee()->input->get('diary');
$evidence = ee()->input->get('evidence');
$suid = ee()->input->get('suid');
$contracts = ee()->input->get('contracts');

if($evidence == 1) {
/* evidence query */
$sql = "SELECT `ev`.`upload_time` FROM `exp_channel_titles` `ti`, `otca_evidence` `ev` WHERE `ti`.`entry_id` = `ev`.`entry_id` AND `ti`.`author_id` = '$suid' ORDER BY ev.upload_time DESC";

$query =  ee()->db->query($sql);

if ($query->num_rows() > 0)
{
	foreach($query->result_array() as $row) // returns all evidence entries for this practice cycle
    {
		$date_array[] = $row['upload_time'];
	}
}
}

if($diary == 1 && ($group_id == 7 || $group_id == 9 || $group_id == 1)) {
    $sql = "SELECT `last_updated`,`creation_date` FROM `otca_diary` WHERE `member_id`='$suid' AND `hidden`='0' ORDER BY `last_updated` DESC";
    
    $query =  ee()->db->query($sql);
    
    if ($query->num_rows() > 0)
    {
            foreach($query->result_array() as $row) // returns all diary entries for this practice cycle
        {
                    $date_array[] = $row['last_updated'];
            }
    }
}

if($contracts == 1) {
    $sql = "SELECT `id`, `time`,`title` FROM `otca_user_status_history` WHERE `member_id` = '$suid' ORDER BY `time` DESC";
    
    $query =  ee()->db->query($sql);
        if ($query->num_rows() > 0)
        {
            foreach($query->result_array() as $row) // returns all diary entries for this practice cycle
            {
                        $date_array[] = $row['time'];
            }
        }
}

$time = time();

$output_date = date('l dS M, Y g:i a',  ee()->localize->set_localized_time($time));
$current_date = "";
$output .= "<p>Current time: ".$output_date." </p>";

if(isset($date_array) && count($date_array) > 0) {

	$output .= "<div class='timeline-center timeline' data-end='0' data-start='$time'>";
	$output .= "<div class='timeline-label'>Portfolio</div></div>";
	$output .= "</div>";
        
} else {
	$output .= "Nothing to show!";
}

return $output;
}


public function educatorJavascript() {
    $student_id = ee()->TMPL->fetch_param('student_id');
    $screen_name = ee()->TMPL->fetch_param('screen_name');
    
    $str = <<<javascript
        <script>
        \$(document).ready(function() {	
	window.evStudentId = '$student_id';
        window.evStudentScreenName = '$screen_name';
	var tabExpand = "<span style=\"float:right; font-family: verdana, arial, sans-serif; font-size:11px\"><a class=\"link-expand\" href=\"#\">Expand Tab</a></span>";
	
	var loader = '<img src=/img/ajax-loader-circle.gif class=loader></img>';
	var loadContainer = function() { 
		var diary = \$("input#diary:checked").length > 0 ? 1 : 0;
		var evidence = \$("input#evidence:checked").length > 0 ? 1 : 0;
		var contracts = \$("input#contracts:checked").length > 0 ? 1 : 0;
		
		\$('#timeline-container').
		html(loader).
		load('/ajax/educator-timeline-container?evidence='+evidence+"&diary="+diary+'&suid='+window.evStudentId+'&contracts='+contracts, 
		function() {
		
			\$('.timeline-label').click( 
				function(e){		
						var end = \$(this).parent().data('end');
						var start = \$(this).parent().data('start');
			
						var label = \$(this).clone();
						var folks = \$(this).parent();
						
						\$(folks).html('').
							addClass('open-panel').
							html("").
							html(loader).
							load('/ajax/educator-timeline-list?start='+start+'&end='+end+'&diary='+diary+'&evidence='+evidence+'&suid='+window.evStudentId+'&contracts='+contracts, 
							function() {
								\$('.open-panel li').each(function() {
								    var myheight = $(this).css('height');
								   $(this).data('origHeight', myheight).prepend(tabExpand).addClass('collapsed');
								});
								\$(this).prepend(label);
							}
						);
				}
			).first().click();
			
		}
	);	
      }
      
      var loadHighlights = function() {
		\$('#highlights-container').
		html(loader).addClass('open-panel').html("").
			html(loader).
			   load('/ajax/educator-timeline-list?ho=1&diary=1&evidence=1&contracts=1&start=0&end=0&'+'&suid='+window.evStudentId);
      };
	
	

\$("input#diary, input#evidence, input#contracts").change(
	function() {
		loadContainer();
	}
);

\$("select[name='checkAction']").change(function(){
	var text = \$(this).text().toLowerCase();
	
	if(text=='share') {
		\$("input#action:checked").each(function(i,v) {
			
		});
	}
});

\$(document).on('click', 'a.link-expand', function(e) {
	e.preventDefault();
	var li = \$(this).closest('li');
	var ydif = li.data('origHeight');
	
	if(li.hasClass('collapsed')) {
	    \$(this).text('Collapse Tab');
	    var str = "+="+ydif;
	    \$(this).closest('li').animate({height: str}, 500).removeClass('collapsed').addClass('expanded');
	    
	} else {
	    var str = "-="+ydif;
	    \$(this).closest('li').animate({height: str}, 500).removeClass('expanded').addClass('collapsed');
	    \$(this).text('Expand Tab'); 
	}
});
  
loadHighlights();
loadContainer();
});
</script>
javascript;

return $str;
}

public function studentJavascript() {
    $str = <<<javascript
    <script>    
    \$(document).ready(function() {	
	var loader = '<img src=/img/ajax-loader-circle.gif class=loader></img>';
	var split = location.search.replace('?', '').split('=');
	var highlightID = split[1];
	
	//alert("Testing this page "+split[0]+" "+split[1]);
	
	\$.checkHistoryID(function() { 
		window.location.href = '/practice-placement/prepare-for-practice';
	}, 
		true /* no reload on history_id > 0 */
	);
	
	var loadContainer = function() { 
		var diary = \$("input#diary:checked").length > 0 ? 1 : 0;
		var evidence = \$("input#evidence:checked").length > 0 ? 1 : 0;
		var contracts = \$("input#contracts:checked").length > 0 ? 1 : 0;
		
		\$('#timeline-container').
		html(loader).
		load('/ajax/timeline-container?evidence='+evidence+"&diary="+diary+"&contracts="+contracts, 
		function() {
			\$('.timeline-label').click( 
				function(e){		
						var end = \$(this).parent().data('end');
						var start = \$(this).parent().data('start');
			
						var label = \$(this).clone();
						var folks = \$(this).parent();
						
						\$(folks).html('').
							addClass('open-panel').
							html("").
							html(loader).
							load('/ajax/test-timeline-list?start='+start+'&end='+end+'&diary='+diary+'&evidence='+evidence+'&contracts='+contracts, 
							function() {
								\$(this).prepend(label);
								
								var highlightedItem = \$(folks).find("a[href*='assessed-matrix/"+highlightID+"']").closest("li");
								
								if(typeof highlightedItem !== 'undefined') {
								    highlightedItem.css("background-color", "#FFFF9C");
								    try {
									\$('html, body').animate({
										scrollTop: highlightedItem.offset().top
								     }, 500);
								     } catch(e) {
									console.log('no scroll, student not adding');
								     }
								 } else {
								    console.log("highlightedItem undefined: "+highlightID);
								 }

							}
						);
				}
			).trigger('click');
		}
	);
}
\$("input#diary, input#evidence, input#contracts").change(
	function() {
		loadContainer();
	}
);

\$(document).on('change', "input#hide", function() {
	var entry_id = \$(this).data('id');
	var value = \$(this).is(":checked") ? 1 : 0;
	var group_type = \$(this).closest('li').attr('class');
	\$(this).before(loader+" ");
	\$.post('/ajax/hide-diary-entry/', { entry_id: entry_id, hidden: value, group_type: group_type }, function(data) {
		\$("img.loader").remove();
	});
});

\$(document).on('change', "select#highlight", function() {
	\$(this).after("  "+loader);
	var code = \$(this).data("code");
	var serverColorVal = \$(":selected", this).val();
	var colorVal = "thick solid " + serverColorVal;
	\$me = \$(this);
	if(typeof colorVal === 'undefined' || serverColorVal == 'none') {
		colorVal = "thin solid #663399";
	}
		\$.post('/ajax/save-folio-highlight', { code : code, color : serverColorVal } , function()
		{
				\$me.closest("li").css({ border : colorVal });
				
				if(serverColorVal != 'none') {
					\$me.closest("li").find("input#hide:checked").trigger('click');	
				} else {
					\$me.closest("li").find("input#hide:not(:checked)").trigger('click');	
				}
				\$('.loader').remove();
		}
	);
	
});

\$(document).on('click', 'img.exit', function(e) {
e.preventDefault();
var confirmed = \$(e.target).data('confirmed');
var str_id = \$(e.target).prev('select#highlight').data('code');

var id_arr = str_id.split("_");
var type = id_arr[0];
var member = id_arr[1];
var id = id_arr[2];
var eTarget = \$(e.target);
var parentDiv = eTarget.parents('.open-panel').first();

var removeEmptyPanel = function() {
	// remove parent;
	var liLen = \$(parentDiv).find("li").length;
	if(liLen == 0) {
		if(parentDiv.hasClass('timeline-top')) {
			parentDiv.next().removeClass('timeline-center').addClass('timeline-top');
		}
		if(parentDiv.hasClass('timeline-bottom')) {
			parentDiv.prev().removeClass('timeline-center').addClass('timeline-bottom');
		}
		\$(parentDiv).remove();		
	}	
};
 
if(typeof confirmed != 'undefined' && confirmed == 1) {
\$(e.target).parents('.otca-textbox').find('br').before("<img id='loader' src='/img/ajax-loader-circle.gif'></img>");
	
	if(typeof id !== 'undefined') {
		
	if(typeof type !== 'undefined') {
		switch(type) {
		case 'diary':
			\$.get('/ajax/remove-diary-entry?id='+id, function() {
				var box = \$(e.target).parents('li');
				\$(box).remove();
				removeEmptyPanel();
			});
		break;
		case 'evidence':
			\$.get('/ajax/remove-evidence-entry?id='+id, function() {
				var box = \$(e.target).parents('li');
				\$(box).remove();
				removeEmptyPanel();
			});
		break;
		}
	}
	
	}
} else {
\$('div#confirm-message').remove();
\$('img.exit').each(function(i, v) {
if(v != e.target) {
\$(v).removeAttr('data-confirmed').removeData('confirmed');
}
});
if(typeof id !== 'undefined') {
\$(e.target).after('<div id="confirm-message">\
			Are you sure? Click again to delete the item forever.<br> </div>');
\$('div#confirm-message').css({top: e.pageY-10, left: e.pageX+20});
\$(e.target).data('confirmed', 1);
}
}
});
loadContainer();
});
</script>
javascript;
    
return $str;    
}
 
public static function usage()
    {
        ob_start();  ?>

        This is the TESTING VERSION
The Folio Plugin provides a scrollable portfolio view of all diary entries, evidence and learning contracts.

    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }    
}
