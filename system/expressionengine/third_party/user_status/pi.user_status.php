<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
POST a JSON object to this script to persist the current practice cycle.
The profile consists of user reflections and general settings for the practice cycle.  
Reflections (javascript: userProfile.reflections[]) are persisted in the otca_diary mySQL table.
The user status (javascript: userProfile.steps, userProfile.level, userProfile.beginner, userProfile.objectives) 
are persisted to the otca_user_status mySQL table.
*/

$plugin_info = array(
    'pi_name' => 'OTCA User Status',
    'pi_version' => '1.0.0',
    'pi_author' => 'Paul Sijpkes',
    'pi_author_url' => '',
    'pi_description' => 'User status plugin',
    'pi_usage' => User_status::usage()
);

class User_status {

private $member_id = 0;
    
public function __construct() {
    $this->member_id = ee()->session->userdata('member_id');
}

public function save() {
    if(!isset($this->member_id) || $this->member_id == 0) return;

$userProfileInput = ee()->input->post('userProfile');

if(!empty($userProfileInput)) {
    $userProfile = json_decode($userProfileInput);
    if($userProfile->startNewCycle == 'true') $endCycle = TRUE;
    else $endCycle = FALSE;
    
        $steps = isset($userProfile->steps)?mysql_real_escape_string(json_encode($userProfile->steps)):'';
        $level = isset($userProfile->level)?$userProfile->level:0;
        $beginner = isset($userProfile->beginner)?$userProfile->beginner:0;
        $objectives = isset($userProfile->objectives)?mysql_real_escape_string(json_encode($userProfile->objectives)):"[]";
        $title = isset($userProfile->title)?mysql_real_escape_string($userProfile->title):"";
    
    if(empty($title)) {
        $error_msg = array("error" => "Error saving user profile!");
        return json_encode($error_msg);
    }
    
    // create blank history item ready for saving this in the history later on... and so we can get the history_id  
    if($userProfile->history_id == 0) {
            $query = ee()->db->query("INSERT INTO `otca_user_status_history` (`member_id`, `steps`, `level`, `beginner`,`objectives`, `title`, `time`) VALUES ('$this->member_id','$steps','$level','$beginner','$objectives', '$title', UNIX_TIMESTAMP() );");
            $userProfile->history_id = ee()->db->insert_id(); 
     }
    $history_id = $userProfile->history_id;
    foreach($userProfile->reflections as &$entry) {
       $text = mysql_real_escape_string($entry->text);
          
       $timestamp = $entry->date;   
       $entry_id = isset($entry->internalId)?$entry->internalId:0;  
       $tag = isset($entry->tag)?$entry->tag:'';
            
          // echo "$text >>>> $tag \n\n"; 
            
          if(!empty($text) && !empty($tag)) {
        if($entry_id != 0) {
            $query =  ee()->db->query("UPDATE `otca_diary` SET `entry_text`='$text', `last_updated`='$timestamp' WHERE `member_id`='$this->member_id' AND `entry_id`='$entry_id' AND `tag` = '$tag'"); 
        } else {        
            $query =  ee()->db->query("INSERT INTO `otca_diary` (`member_id`,`entry_text`,`creation_date`, `current_practice_cycle`, `tag`) VALUES ('$this->member_id','$text','$timestamp','$history_id', '$tag')");
        }
           }
    }
// save status
$query = ee()->db->query("INSERT INTO `otca_user_status` (`member_id`, `steps`, `level`, `beginner`,`objectives`,`title`, `history_id`)
                            VALUES ('$this->member_id','$steps','$level','$beginner','$objectives', '$title', '$history_id')
                            ON DUPLICATE KEY UPDATE `steps`='$steps', `level`='$level', `beginner`='$beginner', `objectives`='$objectives',
                            `title`='$title', `history_id`='$history_id'");

// update history
ee()->db->query("UPDATE otca_user_status_history a, otca_user_status b SET a.steps = b.steps, a.level = b.level, a.beginner = b.beginner,a.objectives = b.objectives, a.title = b.title WHERE a.id = b.history_id AND a.member_id = '$this->member_id'");

return json_encode($userProfile->history_id);
}
}
 
public static function usage()
{
        ob_start();  ?>

Saves OTCA user status for otca.net.au.

POST a JSON object to this script to persist the current practice cycle.
The profile consists of user reflections and general settings for the practice cycle.  
Reflections (javascript: userProfile.reflections[]) are persisted in the otca_diary mySQL table.
The user status (javascript: userProfile.steps, userProfile.level, userProfile.beginner, userProfile.objectives) 
are persisted to the otca_user_status mySQL table.

    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
}    
}
