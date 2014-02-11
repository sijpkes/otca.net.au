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
	'pi_name' => 'EEPortfolio Diary',
	'pi_version' => '1.0.0',
	'pi_author' => 'Paul Sijpkes',
	'pi_author_url' => '',
	'pi_description' => 'Diary entries plugin',
	'pi_usage' => Diary::usage()
);

class Diary {

public function add_entry() {
	
	$member_id = ee()->session->userdata('member_id');
	echo $member_id;
	
	if(empty($member_id)) return;
	
	$diary_entry = mysql_real_escape_string(ee()->input->post('entry'));
	$key = ee()->input->get('id');
	$history_id = ee()->input->get('history_id');
	$tag = ee()->input->get('tag');

	if(!empty($tag) && !empty($history_id) && !empty($diary_entry)) {

    ee()->db->query("INSERT INTO `otca_diary` (`entry_id`,`member_id`,`entry_text`,`creation_date`, `current_practice_cycle`, `tag`)
    				VALUES ('$key','$member_id','$diary_entry',UNIX_TIMESTAMP(),'$history_id', '$tag')
    				ON DUPLICATE KEY UPDATE `entry_text`='$diary_entry', `last_updated`= UNIX_TIMESTAMP(), `tag`='$tag'");
					
//	echo "INSERT INTO `otca_diary` (`entry_id`,`member_id`,`entry_text`,`creation_date`, `current_practice_cycle`, `tag`)
  //  				VALUES ('$key','$member_id','$diary_entry',UNIX_TIMESTAMP(),'$history_id', '$tag')
    //				ON DUPLICATE KEY UPDATE `entry_text`='$diary_entry', `last_updated`= UNIX_TIMESTAMP(), `tag`='$tag'";
	} 
}

public function remove_entry() {
	if(empty($member_id)) return;

	$key = ee()->input->get('id');
	ee()->db->query("DELETE FROM `otca_diary` WHERE `entry_id`='$key'");  
}
 
public static function usage()
    {
        ob_start();  ?>

The Diary Plugin provides personal diary features.

    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }    
}
