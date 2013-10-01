$(document).ready(function() {
		
	var steps = userProfile.steps;

	for(var i=0;i<steps.length; i++){
		if(i == steps.length-2) separator = ' & ';
		if(i == steps.length-1) separator = '';
		stepStr += steps[i].trim()+" &ndash; "+window.stepDefinitions[sv]+separator;
	}
	
	var levelName = "";
	
	if(userProfile.level == 1) {
		levelName = 'Emerging';
	} else if (userProfile.level == 2){
		levelName = 'Consolidating';
	} else if (userProfile.level == 3){
		levelName = 'Competent to Graduate';
	}	
	
	if(stepStr != "")  { 
		$('h2 span#steps').html(stepsStr);
		$('h2 span#level').html("<span class='"+levelName.toLowerCase()+"'>"+levelName+"</span>");
	}
	
	objs = window.userProfile.objectives;
	
	$(objs).each(function(i, v) {
		stepStr = "";
		var compStr = "";
		$(v.steps).each(function(j, sv) {
			stepStr += sv+" &ndash; "+window.stepDefinitions[sv]+"<br>";
		});
		
		$(v.competencyStatement).each(function(k, cs) {
			compStr += cs+"<br>";
		});
		
		$("table.learning-contract tbody").append("<tr><td>"+stepStr+"</td><td>"+v.text+"</td><td>"+compStr+"</td><td></td><td></td><td></td><td></td></tr>");
	});
	
	return (false);
});