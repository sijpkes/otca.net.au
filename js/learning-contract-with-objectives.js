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

