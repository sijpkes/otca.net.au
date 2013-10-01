$(document).ready(function() {
	
	$.checkHistoryID(function() { 
		window.location.href = '/practice-placement/prepare-for-practice';
	}, 
		true /* no reload on history_id > 0 */
	);
	
	if(typeof userProfile !== 'undefined') {
	var objs = window.userProfile.objectives;
	var objElement = '<p id="objectives"><label for="text"> Your plan for this objective: </label><textarea name="text" class="planning"></textarea></p>';
	$(objs).each(function(i, v) {
		$('span#objectives-for-planning').append(objElement);
		
		var stepStr = "<strong>Steps for this objective:</strong> <br>";
		var compStr = "<strong>Matrix Competency Statements for this objective:<br> <ul>";
		$(v.steps).each(function(j, sv) {
			stepStr += sv+" &ndash; "+window.stepDefinitions[sv]+"<br>";
		});
		
		$(v.competencyStatementText).each(function(k, cs) {
			compStr += "<li>"+cs+"</li>";
		});
		
		compStr += "</ul>";
		
		$('span#objectives-for-planning #objectives').last().find('label').before("<strong>Description</strong><p>"+v.text+"</p>"+stepStr +"<br>"+ compStr);
		$('span#objectives-for-planning #objectives textarea.planning').last().text(v.planning);
	});
	
	$('a#continue').click(function(e) {
		e.preventDefault();
		var ctarget = e.target;
		$.checkHistoryID(function() {
		
		var newobjs = window.userProfile.objectives;
		var myhref = $(ctarget).attr('href');
		
		$('span#objectives-for-planning #objectives').each(function(i, o) {
			newobjs[i].planning = $(o).find('textarea.planning').val();
		});
		window.userProfile.objectives = newobjs; 
		//localStorage.userProfile = JSON.stringify(window.userProfile); // store planning inside existing objectives
		var jsonProfile =  JSON.stringify(window.userProfile);
				//update database
				$.post('/ajax/user-status', 
					{ "userProfile" : jsonProfile }, 
					function(data) {
						window.location.href = myhref;
					} 
					,'json');
		});
	});
	
	$("#otpp-thumb a").click(function(e) {
		e.preventDefault();
		$('div#otpp').show();
	});
	
	$("#otpp #exit-button").click(function(e) {
		$(e.target).parent().hide();
	});
	}
});