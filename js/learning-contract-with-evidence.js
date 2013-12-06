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
	
	$.fn.moreless = function(n) {
		        var $me = this;
				var moreLink = "<a href='#' class='more-click'> ...more.</a>";
				var lessLink = "<a href='#' class='less-click'> ....less</a>"
				if(this.text().length > n) {
					this.data('fullText', this.text());
					var newText = this.text().substring(0,n);
					this.data('lessText', newText);
					newText += moreLink;
					this.html(newText);
				}
				
				this.on('click', 'a.more-click', function(e) {
						e.preventDefault();
						$me.html($me.data('fullText')+lessLink);
				});	
				
				this.on('click', 'a.less-click', function(e) {
						e.preventDefault();
						$me.html($me.data('lessText')+moreLink);
				});	
		return this;
	};

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
		
		/*
			Get pracsot evidence
		*/
		var pracsot_json = (function () {
		    var json = null;
		    $.ajax({
		        'async': false,
		        'global': false,
		        'url': '/ajax/evidence-learning-contract',
		        'dataType': "json",
		        'success': function (data) {
		            json = data;
		        }
		    });
		    return json;
		})();
		
		$.fn.insertArrayAsUL = function(array) {
				var ul = "<ul>";
					$.each(array, function(i, v) {
						ul += "<li>"+v+"</li>";
					});
				ul+= "</ul>";
				this.html(ul);
			return this;
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
			$('span#steps').html(stepStr);
			$('span#level').html("<span class='"+levelName.toLowerCase()+"'>"+levelName+"</span>");
		}
		
		objs = window.userProfile.objectives;
		
		$(objs).each(function(i, v) {
			stepStr = "";
			$(v.steps).each(function(j, sv) {
				stepStr += sv+" &ndash; "+window.stepDefinitions[sv]+"<br>";
			});
			
			var ob_prac_array = [];
			
			$(v.pracsot).each(function(ipv, pv) {
				var sp_prac = pv.split(',');
				ob_prac_array = ob_prac_array.concat(sp_prac);
			});
			
			var filename = [];
			for(var i=0;i<pracsot_json.length; i++) {
				if(typeof pracsot_json[i].pracsot != 'undefined') {
				var ev_prac_array = pracsot_json[i].pracsot.split(',');
				for(var j=0;j<ob_prac_array.length; j++) {
					if($.inArray(ob_prac_array[j], ev_prac_array) > -1) {
						if(typeof pracsot_json[i+1].filename != 'undefined') {
							filename.push(pracsot_json[i+1].filename);
							console.log(ob_prac_array[j]);
						}
					}
				} 
			}
			}
			
			$("table.learning-contract tbody").append("<tr><td>"+stepStr+"</td><td>"+v.text+"</td><td style=\"overflow: hidden; text-overflow: ellipsis\" id='statement'></td><td id='pracsot'></td><td id='planning'></td><td id='filename'></td><td></td></tr>").
			find('td#statement').last().insertArrayAsUL(v.competencyStatementText).parent().
			find('td#pracsot').last().insertArrayAsUL(ob_prac_array).parent().
			find('td#planning').last().insertArrayAsUL(v.planning).parent().
			find('td#filename').last().insertArrayAsUL(filename);
		});
		$('table td').each(function(i,v) { $(v).moreless(75); });
	});	
	
	$("a.button#printable").click(function(e) {
		e.preventDefault();
		
		var data = {
			tableContractHTML : $("table.learning-contract tbody").html()
		};
		
		$.post("/printview/learning_contract", data, function(data) {
			var w = window.open();
			$(w.document.body).html(data);
		});
	});
	
});

