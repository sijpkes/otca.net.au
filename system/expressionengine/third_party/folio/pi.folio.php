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
	'pi_name' => 'EEPortfolio Folio',
	'pi_version' => '1.0.0',
	'pi_author' => 'Paul Sijpkes',
	'pi_author_url' => '',
	'pi_description' => 'Folio view plugin',
	'pi_usage' => Folio::usage()
);

class Folio {

private $statement_tags = array('letters' => 'prepare-statement', 'reflections' => 'self-assess-statement', 
                                'general' => 'general_diary_entry');

private $grouped_diary_entry_types = array('prepare-statement' => "Introductory Letter to Educator",
                                         'self-assess-statement' => 'Reflection/Planning'
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
$letters = ee()->input->get('letters');
$reflections = ee()->input->get('reflections');

  $whereClause = "";
    
    if($diary == 1) {
        $str = $this->statement_tags['general'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }
    if($letters == 1) {
        if(strlen($whereClause) > 0) {
            $whereClause .= " OR ";
        }
        $str = $this->statement_tags['letters'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }   
    if($reflections == 1) {
        if(strlen($whereClause) > 0) {
            $whereClause .= " OR ";
        }
        $str = $this->statement_tags['reflections'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }  

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
 
if($diary == 1 || $letters == 1 || $reflections == 1) {
$sql = "SELECT `creation_date` FROM `otca_diary` WHERE `member_id`='$this->member_id' AND ($whereClause) ORDER BY `creation_date` DESC";

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
$letters = ee()->input->get('letters');
$reflections = ee()->input->get('reflections');

$whereClause = "";
    
   if($diary == 1) {
        $str = $this->statement_tags['general'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }
    if($letters == 1) {
        if(strlen($whereClause) > 0) {
            $whereClause .= " OR ";
        }
        $str = $this->statement_tags['letters'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }   
    if($reflections == 1) {
        if(strlen($whereClause) > 0) {
            $whereClause .= " OR ";
        }
        $str = $this->statement_tags['reflections'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }  

$record_array = array();
$countDuplicates = array();

$diary_highlights = array();
$contract_highlights = array();
$evidence_highlights = array();
$diary_hl_colors = array();
$contract_hl_colors = array();
$evidence_hl_colors = array();

$hlSelectControl = "<br clear='all'><br clear='all'><label for='highlight'>Highlight for educators:  </label><select id=\"highlight\" title='Highlighted items will appear at the top of the page when your educator accesses your portfolio. Highlighting will automatically show hidden diary entries.' data-code='%code%'> 
    <option value='none'>No highlight</option>
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
$sql = "SELECT `id`, `color`, `type`, `entry_id` FROM `otca_folio_highlights` WHERE `member_id` = '$this->member_id' LIMIT 0, 20";

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
$sql = "SELECT `ev`.`upload_time`, `ev`.`entry_id`, `ev`.`filename`, `ev`.`last_assessed`, 
`ti`.`title`, `data`.`field_id_4` as `description`, `data`.`field_id_3` as `file_url`, 
`data`.`field_id_13` as `step`, `data`.`field_id_14` as level 
FROM `exp_channel_titles` `ti`, `otca_evidence` `ev`, `exp_channel_data` `data` 
WHERE `ti`.`entry_id` = `ev`.`entry_id` AND `data`.`entry_id`= `ev`.`entry_id` AND `ti`.`author_id` = '$this->member_id'
	ORDER BY `ev`.`upload_time` DESC";
	
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
$localEntryTime = ee()->localize->format_date('%D, %F %d, %Y %g:%i%a', $row['upload_time']);

$unique_code = "evidence_".$this->member_id."_".$row['entry_id'];
$hc = str_replace("%code%", $unique_code, $hlSelectControl);
$hc = str_replace("%options%", $options, $hc);
$hc = str_replace("%delete_option%", $deleteControl, $hc);

$title = strlen($row['title']) > 0? $row['title']:"";
$level = strlen($row['level']) > 0? $row['level']:"";
$step  = strlen($row['step']) > 0? $row['step']:"";
$filename = strlen($row['filename']) > 0? $row['filename']:"";
$description = strlen($row['description']) > 0? "<p><em>$row[description]</em></p>":"";

/* work out whether this has been assessed within the last 30 days */
$thirty_days_secs = 2592000;
$recent = time() - $thirty_days_secs;
$raw_days = time() - $row['last_assessed'];
$days = floor($raw_days / 60 / 60 / 24);
$days_str = $days == 0 ? "today" : "$days day(s) ago.";
$recently_assessed = $row['last_assessed'] > $recent ? " <span style='color:#FFB510; background-color: #333'><strong>*** Verified $days_str ***</strong></span> " : "";
$file_link = strlen($row['file_url']) > 0? "<a href='$row[file_url]/$row[entry_id]' title='Download this file' target='_blank' style='color: #639'>Open attached file in a new tab</a>":"";

$file_link = preg_replace('~\{(.*?)\}~', '/download/secure/', $file_link);

$record_array[$upload_time] = "<li $styling><span style=\"color: rgb(68, 68, 68); font-size:15px\">
<p style=\"margin-top: 0\"><strong>".$recently_assessed."<u style='font-size: 12px'>OTCEM Evidence/Supporting Evidence</u> &ndash; \"$title\"
<br><span style='font-size:10px'>($step, $level)</span></strong> 
<br><a href='/pages/assessed-matrix/$row[entry_id]/$row[title]' style='color: blue; line-height: 22px'>
View Assessed OTCEM Competency Statements for this Evidence</a></p> <p>
</p> $description $file_link <span style=\"float: right; line-height:0px\">$localEntryTime</span></span>$hc</li>";

	}
}
}

if($diary == 1 || $letters == 1 || $reflections == 1) {

$sql = "SELECT `entry_id`, `entry_text`, `last_updated`, `creation_date`, `hidden`, `tag`, `current_practice_cycle` FROM `otca_diary`
    WHERE `member_id`='$this->member_id' AND ($whereClause) ORDER BY `last_updated` DESC";

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
		
                    $item_data = array('text' => nl2br($row['entry_text']), 'date' => $row['last_updated'], 'label' => $my_label,
                                        'entry_id' => $row['entry_id'], 'hidden' => $hidden);
                  
                    $grouped_entry_set[$row['current_practice_cycle']."_$postfix"][$my_tag] = $item_data;
              
                    unset($results[$key]);
	}
    }
    
    //var_dump($grouped_entry_set);
    
    foreach($grouped_entry_set as $group_key => $grouped_item) {
	ksort($grouped_item);
        $grouped_entry_text = "";
        $group_date = null;
        $hidden = "";
        $styling = "";
	
	$group_type = end(explode("_", $group_key));
	
	//$colorSet = FALSE;
        foreach($grouped_item as $item) {
            $grouped_entry_text .= "<p>$item[text]</p>";    
            $group_date = $item['date'];
            $group_label = $item['label'];
            $group_entry_id = $item['entry_id'];
            $group_hidden = $item['hidden'];
	    $selectedColor = "none";
	    $styling = "";
        }
	
	/* last items entry id becomes the group entry id */
	if(array_key_exists($group_entry_id, $diary_hl_colors)) {
		    $styling = "style='border: thick solid ".$diary_hl_colors[$group_entry_id]."'";
		    $selectedColor = $diary_hl_colors[$group_entry_id];
		   // $colorSet = true;
	}
        
        //print($selectedColor."<br>");
        $options = generateOptions($selectedColor);
        
        $groupLocalTime = ee()->localize->format_date('%d %M %Y', $group_date);
	$group_unique_code = "diary_".$this->member_id."_".$group_entry_id;
        $ghc = str_replace("%code%", $group_unique_code, $hlSelectControl);
        $ghc = str_replace("%options%", $options, $ghc);
        $ghc = str_replace("%delete_option%", $deleteControl, $ghc);
        $record_array[$group_date] = "<li $styling class='$group_type'><label style='float: right'><input id='hide' type='checkbox' data-id='$group_entry_id' data-entrytype='checkbox' name='action' title='Hide this item from educators.  Untick this if you wish to share this diary entry with your educators.' $group_hidden> Hidden</label>
	<span style=\"color: rgb(68, 68, 68); font-size:13px\"><p style=\"margin-top: 0\"><strong><u>$group_label</u></strong> $grouped_entry_text</p> <span style=\"float: right; font-size: 10px; margin-right: 2em\">$groupLocalTime</span></span>$ghc</li>";        
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
		$record_array[$creation_date] = "<li $styling><label style='float: right'><input id='hide' type='checkbox' data-id='$hide_id' data-entrytype='checkbox' name='action' title='Hide this item from educators.  Untick this if you wish to share this diary entry with your educators.' $hidden> Hidden</label>
		<span style=\"color: rgb(68, 68, 68); font-size:13px\"><p style=\"margin-top: 0\"><strong><u>Diary Entry</u></strong> $row[entry_text] </p> <span style=\"float: right\">$localEntryTime</span></span>$hc</li>";
	}
}
}
if($contracts == 1) {
    $sql = "SELECT `id`, `time`,`title` FROM `otca_user_status_history` WHERE `member_id` = '$this->member_id' ORDER BY `time` DESC";
    /*AND `time` >= $end AND `time` < $start*/
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
            $record_array[$creation_date] = "<li $styling><a href='/pages/learning-contract?id=$row[id]'><span style=\"font-family: arial,helvetica,sans-serif; color: black; font-size:12pt\">
            <p style=\"margin-top: 0\">Learning Contract for Practice Placement Cycle: 
            \"$row[title]\"</a> 
            <span style=\"float: right; margin-right:2em; font-size: 10px\">$localEntryTime</span></p>$hc</li>";
        }
    }
}
// sort array by key to maintain date ordering
krsort($record_array);

$output .= "<ul>";
if( count($record_array) > 0 ) {
    
    foreach($record_array as $list_item){
    	$output .= $list_item;
    }
    
} else {
    $output .= "<li>Nothing to show!</li>";
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
$letters = ee()->input->get('letters');
$reflections = ee()->input->get('reflections');

$whereClause = "";
    
if($diary == 1) {
        $str = $this->statement_tags['general'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }
    if($letters == 1) {
        if(strlen($whereClause) > 0) {
            $whereClause .= " OR ";
        }
        $str = $this->statement_tags['letters'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }   
    if($reflections == 1) {
        if(strlen($whereClause) > 0) {
            $whereClause .= " OR ";
        }
        $str = $this->statement_tags['reflections'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }  

$ho = ee()->input->get('ho'); /* highlights only */

$record_array = array();
$countDuplicates = array();
$diary_highlights = array();
$contract_highlights = array();
$evidence_highlights = array();
$diary_hl_colors = array();
$contract_hl_colors = array();
$evidence_hl_colors = array();

/* get highlighted items*/
$sql = "SELECT `id`, `color`, `type`, `entry_id` FROM `otca_folio_highlights` WHERE `member_id` = '$suid'";

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

$sql = "SELECT `ev`.`upload_time`, `ev`.`entry_id`, `ev`.`filename`, `ti`.`title`, 
`data`.`field_id_4` as `description`, `data`.`field_id_3` as `file_url`,
`data`.`field_id_13` as `step`, `data`.`field_id_14` as level FROM `exp_channel_titles` `ti`, `otca_evidence` `ev`, `exp_channel_data` `data` 
WHERE `ti`.`entry_id` = `ev`.`entry_id` AND `data`.`entry_id`= `ev`.`entry_id` AND `ti`.`author_id` = '$suid' 
ORDER BY `ev`.`upload_time` DESC";

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
$level = strlen($row['level']) > 0? $row['level']:"";
$step  = strlen($row['step']) > 0? $row['step']:"";
$filename = strlen($row['filename']) > 0? $row['filename']:"";
$description = strlen($row['description']) > 0? "<p><em>$row[description]</em></p>":"";
$file_link = strlen($row['file_url']) > 0? "<a href='$row[file_url]/$row[entry_id]' title='Download this file' style='color: #369' target='_blank'>Open attached file in a new tab</a>":"";

$file_link = preg_replace('~\{(.*?)\}~', '/download/secure/', $file_link);

if((!empty($ho) && !empty($styling)) || empty($ho) ) {
	
	$step_level = "";
	if(!empty($step) && !empty($level)) {
		$step_level = "<span style='font-size: 10px'>($step, $level)</span>";
	}
	
    $record_array[$upload_time] = "<li $styling><span style=\"color: rgb(68, 68, 68); font-size:13px\">
                <p style=\"margin-top: 0\"><strong><u>OTCEM Evidence/Supporting Evidence</u> &ndash; \"$title\" $step_level</strong><br>
                <a href='/pages/educator-matrix/$row[entry_id]/$suid/$title/$level/$step' 
                style='color: blue; line-height: 22px'>Assess OTCEM Competency Statements for this Evidence</a></p> 
                <p> $title </p> $description $file_link <span style=\"float: right; line-height:0px\">$localEntryTime</span></span>
                </li>";
}
	}
    }
}

if($diary == 1 || $letters == 1 || $reflections == 1) { 

$sql = "SELECT `entry_id`, `entry_text`, `tag`, `creation_date`, `last_updated`, `current_practice_cycle`, `hidden` FROM `otca_diary` WHERE `member_id`='$suid' AND `hidden` = '0' AND ($whereClause) ORDER BY `last_updated` DESC";

//echo $sql; 

$query =  ee()->db->query($sql);

if ($query->num_rows() > 0)
{
    $results = $query->result_array();
    $grouped_entry_set = array();
    //$ddeCount = 0; //duplicate diary entry count
    $empty_item = array('styling' => '', 'selectedColor' => '',
			'date' => '', 'entry_id' => '', 'label' => '',
			'items' => array());
        
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
		   if(!isset($grouped_entry_set[$row['current_practice_cycle']."_$postfix"])) {
		    $grouped_entry_set[$row['current_practice_cycle']."_$postfix"] = array();
		 }
		   
                    $item_data = array('text' => nl2br($row['entry_text']), 
                    					'date' => empty( $row['last_updated'] ) ? $row['creation_date'] : $row['last_updated'],
                    					'label' => $my_label,
                                        'entry_id' => $row['entry_id'], 
                                        'hidden' => $row['hidden']);
                  
                    $grouped_entry_set[$row['current_practice_cycle']."_$postfix"][$my_tag] = $item_data;
              
                    unset($results[$key]);
                }
    }
    
    foreach($grouped_entry_set as $key => $grouped_item) {
	
        ksort($grouped_item);
        $grouped_entry_text = "";
        $group_date = null;
        $hidden = "";
        $styling = "";
	
	$group_type = end(explode("_", $key));
	
	//$colorSet = FALSE;
        foreach($grouped_item as $item) {
            $grouped_entry_text .= "<p>$item[text]</p>";    
            $group_date = $item['date'];
            $group_label = $item['label'];
            $group_entry_id = $item['entry_id'];
            $group_hidden = $item['hidden'];
	    $selectedColor = "none";
	    $styling = "";
        }
	
	/* last items entry id becomes the group entry id */
	if(array_key_exists($group_entry_id, $diary_hl_colors)) {
		    $styling = "style='border: thick solid ".$diary_hl_colors[$group_entry_id]."'";
		    $selectedColor = $diary_hl_colors[$group_entry_id];
		   // $colorSet = true;
	}
	
        $groupLocalTime = ee()->localize->format_date("%d %M %Y",$group_date);
        
	if((isset($ho) && $ho > 0 && !empty($styling)) || (!isset($ho) || empty($ho)) ) {
	    $record_array[$group_date] = "<li $styling><span style=\"color: rgb(68, 68, 68); font-size:13px\"><p style=\"margin-top: 0\"><strong><u>$group_label</u></strong> &ndash; "
	    .nl2br($grouped_entry_text)."</p> <span style=\"float: right; font-size: 10px\">$groupLocalTime</span></span></li>";        
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
		$localEntryTime =   ee()->localize->format_date('%d/%M/%Y %g:%i%a', $row['creation_date']);
		$radio_name = $row['entry_id'];
		if((isset($ho) && $ho > 0 && !empty($styling)) || (!isset($ho) || empty($ho)) ) {
		    $record_array[$creation_date] = "<li $styling><span style=\"color: rgb(68, 68, 68); font-size:15px\"><p style=\"margin-top: 0\"><strong><u>Diary Entry</u></strong> ".
		                          nl2br($row['entry_text'])."</p><span style=\"float: right; font-size: 10px\">$localEntryTime</span></span></li>";
		}    
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
            $creation_date = $row['time'];
	    $styling = "";
	    
	    if(array_key_exists($row['id'], $contract_hl_colors)) {
		$styling = "style='border: thick solid ".$contract_hl_colors[$row['id']]."'";
	    }
	    	$localEntryTime =   ee()->localize->format_date('%d %M %Y', $creation_date);
            if((isset($ho) && $ho > 0 && !empty($styling)) || (!isset($ho) || empty($ho)) ) {
	       $record_array[$creation_date] = "<li $styling><p style=\"margin-top: 0; font-size: 13px\">
	       <a href='/pages/learning-contract?id=$row[id]&m=$suid'>
	       Learning Contract for Practice Placement Cycle: \"$row[title]\"</a></p>  
	       <span style=\"float: right; font-size: 10px\">$localEntryTime</span></li>";
	    }		
        }
    }
}
// sort array by key to maintain date ordering
krsort($record_array);
$output .= "<ul>";
if( count($record_array) > 0 ) {
    
    foreach($record_array as $list_item){
        $output .= $list_item;
    }
    
} else {
    $output .= "<li>Nothing to show!</li>";
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
$letters = ee()->input->get('letters');
$reflections = ee()->input->get('reflections');

$whereClause = "";
    
if($diary == 1) {
        $str = $this->statement_tags['general'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }
    if($letters == 1) {
        if(strlen($whereClause) > 0) {
            $whereClause .= " OR ";
        }
        $str = $this->statement_tags['letters'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }   
    if($reflections == 1) {
        if(strlen($whereClause) > 0) {
            $whereClause .= " OR ";
        }
        $str = $this->statement_tags['reflections'];
        $whereClause .= "`tag` LIKE '%$str%'";   
    }  

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

if(($diary == 1 || $letters == 1 || $reflections == 1) && ($group_id == 7 || $group_id == 9 || $group_id == 1)) {
    
    $sql = "SELECT `last_updated`,`creation_date`, `tag` FROM `otca_diary` WHERE `member_id`='$suid' AND `hidden`='0' AND ($whereClause) ORDER BY `last_updated` DESC";
    
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

$output_date = ee()->localize->format_date('%l, %d%S %M, %Y %g:%i%a', $time);
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
    
    ob_start();
        include 'educator.js';
    $str = ob_get_clean();
    
return "<script>$str</script>";
}

public function studentJavascript() {
    
    ob_start();
    include 'student.js';
    $str = ob_get_clean();
    
return "<script>$str</script>";    
}
 
public static function usage()
    {
        ob_start();  ?>

The Folio Plugin provides a scrollable portfolio view of all diary entries, evidence and learning contracts.

    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }    
}
