$(document).ready(function() {
	
	$.fn.addIDs = function() {
		this.find('table').each(function(tablei, tableo) {
		$(tableo).find('tr').each(function(rowi, rowo) {
		if(rowi > 0) { // skip row headers
				$(rowo).find('td').each(function(coli, colo) {
					if(coli > 0) { // skip column headers
							$(colo).find('p').each(function(cbi, cbo) {
								$(cbo).addClass('otca-step'+(tablei+1));
								$(cbo).addClass('otca-level'+coli);
								$(cbo).attr('id','otca-'+(tablei+1).toString()+'_'+rowi.toString()+'_'+coli.toString()+'_'+cbi.toString());
							});
					}
				});
			}
			});
		});
		};
	
	$('#learning-contract-pane').load('/ajax/learning-contract',
		function() {
		var steps = userProfile.steps;
		var stepStr = "";
		var objs = [];
		var separator = ", ";
		
		stepStr += "<ul>";
		for(var i=0;i<steps.length; i++){
			stepStr += "<li style='list-style: none'>"+steps[i].trim()+" &ndash; "+window.stepDefinitions[i+1]+"</li>";
		}
		stepStr += "</ul>";
		
		var levelName = "";
		
		if(userProfile.level == 1) {
			levelName = 'Emerging';
		} else if (userProfile.level == 2){
			levelName = 'Consolidating';
		} else if (userProfile.level == 3){
			levelName = 'Competent to Graduate';
		}	
		
		if(stepStr != "")  { 
			$('h2 span#steps').html(stepStr);
			$('h2 span#level').html("<span class='"+levelName.toLowerCase()+"'>"+levelName+"</span>");
		}
		
		objs = window.userProfile.objectives;
		
		$(objs).each(function(i, v) {
			stepStr = "";
			var compStr = "<ul>";
			$(v.steps).each(function(j, sv) {
				stepStr += sv+" &ndash; "+window.stepDefinitions[sv]+"<br>";
			});
			
			$(v.competencyStatementText).each(function(k, cs) {
				compStr += "<li>"+cs+"</li>";
			});
			
			compStr += "</ul>";
			
			$("table.learning-contract tbody").append("<tr><td>"+stepStr+"</td><td>"+v.text+"</td><td style=\"overflow: hidden; text-overflow: ellipsis\">"+compStr+"</td><td></td><td></td><td></td><td></td></tr>");
		});
	});
});

