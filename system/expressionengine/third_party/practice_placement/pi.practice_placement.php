<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'Practice Placement Javascript Functions',
	'pi_version' => '1.0.0',
	'pi_author' => 'Paul Sijpkes',
	'pi_author_url' => '',
	'pi_description' => 'Practice placement javascript.',
	'pi_usage' => Practice_placement::usage()
);

class Practice_placement {
    
    private $member_group_id = 0;
    private $allowed_group_ids = array();
    private $no_access_message = "";
    
    
    public function __construct() {
        $this->member_group_id = ee()->session->userdata('group_id');
        $this->allowed_group_ids = explode(",", ee()->TMPL->fetch_param('allowed_group_ids'));
        $this->no_access_message =  ee()->TMPL->fetch_param('no_access_message');
    }
    
    private function wrapScriptTag($str) {
	return "<script>\n$str\n</script>\n";
    }
  
    private function defaultJS() {
        $str = <<<javascript
           <script>
           \$(document).ready(function(){
                   \$('input, textarea').attr('disabled', true);
                   \$('.contrast a#continue').before("<p style='color: #000'><strong>As an Educator, you will not see all the features that a Student would viewing this page.</strong></p>").text('Continue with Educator view of the Practice Placement Cycle >>');
                    \$('.contrast a:not(#continue)').attr('href', "#");
                   \$('.contrast a:not(#continue)').click(
                        function(e) {
                        e.preventDefault();
                        
                        alert('$this->no_access_message');
                    });
            });
            </script>
javascript;
    
        return $str;
    }
    
    public function learning_contractJS() {
     if(!in_array($this->member_group_id, $this->allowed_group_ids))
        {
            return $this->defaultJS();
        } else {
	$jquery_selector = ee()->TMPL->fetch_param('jquery_selector');
	$planning = ee()->TMPL->fetch_param('planning');
	$evidence = ee()->TMPL->fetch_param('evidence');
	$objectives = ee()->TMPL->fetch_param('objectives');
	ob_start();
	    include 'learningContract.js';
	$str = ob_get_clean();
	
	return $this->wrapScriptTag($str);
	}
	
    return;
    }
    
    private function check_history_id() {
        $str = <<<javascript
        // checks if this is the user's first cycle.
	\$.checkHistoryID(function() { 
		window.location.href = '/practice-placement/prepare-for-practice';
	}, 
		true /* no reload on history_id > 0 */
	);
javascript;

        return $str;
    }
    
    public function prepare_for_practice_part2JS() {
        
    if(!in_array($this->member_group_id, $this->allowed_group_ids))
        {
            return $this->defaultJS();
        } else {    
    $str = <<<javascript
<script>
\$(document).ready(function(){
	
	{$this->check_history_id()}
	
	if(typeof userProfile !== 'undefined') {
	var steps = userProfile.steps;
	var level = userProfile.level;
		
	\$('select#steps option').each(function(i,o) {
		if(undefined != steps) {
			for(var j=0;j<steps.length;j++) {
			 	if(\$(o).val()[0] == steps[j]) {
					\$(o).attr('selected','selected');
			 	}
			}
		}
	});
	
	\$('select#level option[value="'+level+'"]').attr('selected','selected');
	
	\$('input[type="checkbox"]').change(function(e) {
		if(\$(e.target).is(':checked')) {
			\$('select#level').attr('disabled', 'disabled');
			\$('select#steps').attr('disabled', 'disabled');
			\$('.modopacity').css('opacity', '0.2');
		} else {
			\$('.modopacity').css('opacity', '1');
			\$('select#level').removeAttr('disabled');
			\$('select#steps').removeAttr('disabled');
		}
	});
	
	\$("#otpp-thumb a").click(function(e) {
		e.preventDefault();
		\$('div#otpp').show();
	});
	
	\$("#otpp #exit-button").click(function(e) {
		\$(e.target).parent().hide();
	});
	
	\$('select#steps').change(function() {
		\$(this).css('border', 'none');
		\$(this).closest('.userMessage').remove();
	});
	
	\$('a.button#save').click(function(e) {
		e.preventDefault();
		\$('img#loader').remove();
		\$(this).after("  <img src='/img/ajax-loader-circle.gif' id='loader'/>");
		saveProfile(function() {
			\$('img#loader').replaceWith("<span style='color: #FFB510'> Saved...</span>");
		});
	});
	
	\$('a.button#continue').click(function(e) {
		e.preventDefault();
		\$(".userMessage").remove();
		
		var ctarget = e.target;
		\$.checkHistoryID(function() {
		var saveLevel = \$('select#level option:selected').val();		
		var saveSteps = [];
		var myhref = \$(ctarget).attr('href');
		\$('select#steps option:selected').each(function(i,o) {
			saveSteps[i] = \$(o).text()[0];
		});
		
		if(saveSteps.length == 0) {
			\$('select#steps').after("<p style='color: yellow' class='userMessage'><strong>Please select at least one step before continuing</strong></p>");
			return false;
		} 
		// add holistic "step 8" Being a Professional
		saveSteps.push("8");
		
		window.userProfile.steps = saveSteps;
		window.userProfile.level = saveLevel;
		//localStorage.userProfile = JSON.stringify(userProfile);
		var jsonProfile = JSON.stringify(window.userProfile);
			
			//update database
			\$.post('/ajax/user-status', 
				{ "userProfile" : jsonProfile }, 
				function(data) {
					window.location.href = myhref;
				} 
				,'json');
		return false;
	});
	});
	\$("select#steps").asmSelect();
	}
});
</script>
javascript;
        return $str;
        }
    
    }
    
    public function prepareForPracticeJS() {
        
        if(!in_array($this->member_group_id, $this->allowed_group_ids))
        {
            return $this->defaultJS();
        } else {
	ob_start();
	    include 'prepareForPractice.js';
	$str = ob_get_clean();

    return $this->wrapScriptTag($str); //@TODO update all functions to use inline files instead of heredoc
}
return;
    }
  
public function set_your_learning_objectivesJS() {
 if(!in_array($this->member_group_id, $this->allowed_group_ids))
        {
            return $this->defaultJS();
        } else {    
	ob_start();
	    include 'setYourLearningObjectives.js';
	$str = ob_get_clean();
	
	return $this->wrapScriptTag($str);

        }
return;
}

public function plan_for_your_placementJS() {
if(!in_array($this->member_group_id, $this->allowed_group_ids))
        {
            return $this->defaultJS();
        } else {
$str = <<<javascript
<script>
\$(document).ready(function() {
	
	{$this->check_history_id()}
	
	if(typeof userProfile !== 'undefined') {
	var objs = window.userProfile.objectives;
	var objElement = '<p id="objectives"><label for="text"> Your plan for this objective: </label><textarea name="text" class="planning"></textarea></p>';
	\$(objs).each(function(i, v) {
		\$('span#objectives-for-planning').append(objElement);
		
		var stepStr = "<strong>Steps for this objective:</strong> <br>";
		var compStr = "<strong>Matrix Competency Statements for this objective:<br> <ul>";
		\$(v.steps).each(function(j, sv) {
			stepStr += sv+" &ndash; "+window.stepDefinitions[sv]+"<br>";
		});
		
		\$(v.competencyStatementText).each(function(k, cs) {
			compStr += "<li>"+cs+"</li>";
		});
		
		compStr += "</ul>";
		
		\$('span#objectives-for-planning #objectives').last().find('label').before("<strong>Description</strong><p>"+v.text+"</p>"+stepStr +"<br>"+ compStr);
		\$('span#objectives-for-planning #objectives textarea.planning').last().text(v.planning);
	});
	
	\$('a#continue').click(function(e) {
		e.preventDefault();
		var ctarget = e.target;
		\$.checkHistoryID(function() {
		
		var newobjs = window.userProfile.objectives;
		var myhref = \$(ctarget).attr('href');
		
		\$('span#objectives-for-planning #objectives').each(function(i, o) {
			newobjs[i].planning = \$(o).find('textarea.planning').val();
		});
		window.userProfile.objectives = newobjs; 
		//localStorage.userProfile = JSON.stringify(window.userProfile); // store planning inside existing objectives
		var jsonProfile =  JSON.stringify(window.userProfile);
				//update database
				\$.post('/ajax/user-status', 
					{ "userProfile" : jsonProfile }, 
					function(data) {
						window.location.href = myhref;
					} 
					,'json');
		});
	});
	
	\$("#otpp-thumb a").click(function(e) {
		e.preventDefault();
		\$('div#otpp').show();
	});
	
	\$("#otpp #exit-button").click(function(e) {
		\$(e.target).parent().hide();
	});
	}
});
</script>
javascript;

return $str;
}
return;
}

public function resources_to_dev_competenciesjs() {
if(!in_array($this->member_group_id, $this->allowed_group_ids))
        {
            return $this->defaultJS();
        } else {    
ob_start();
        include 'resourcesToDevelopYourCompetencies.js';
    $str = ob_get_clean();
return "<script>$str</script>";
	}

return;
}
    
    public static function usage()
    {
        ob_start();  ?>

    Provides javascript functions for Practice Placement Cycle


    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }


} 