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
(function( $ ) {
$.fn.validateEvidenceMatrix = function() {
	var $me = this;
	// last search results
	var $searchResults = null;
	
	jQuery.expr[":"].Contains = jQuery.expr.createPseudo(function(arg) {
	    return function( elem ) {
	        return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
	    };
	});
			
	/* setup file viewer window and ajax loader */
	$me.append("<div class='file-viewer upload-box' style='display: none;'> </div>");
	$me.append("<div class='file-viewer dialog' style='display: none;'><a href='#close' class='exit'><img src='/img/close-icon.png' alt='close'></a><span id='message'></span></div>");
	$me.append("<div class='file-viewer pracsot' style='display: none;'>\
				</div>");
	$me.append("<div class='ajax-loader' style='display: none;'> </div>");
	var $viewer = $('div.file-viewer.upload-box');
	var $pracsot_viewer = $('div.file-viewer.pracsot');
	var $dialog = $('div.file-viewer.dialog');
	
	/*var steps = [];
	if(userProfile.steps) 
		steps = userProfile.steps;
	else
		steps = [];*/
		
	var leveln = userProfile.level;
	levelName = "Emerging";
	
	switch(leveln) {
	case 1:
		levelName='Emerging';
	break;
	case 2:
		levelName='Consolidating';
	break;
	case 3:
		levelName = "Competent to Graduate";
	}
	
	$.fn.addCheckBoxes = function(stepn) {
	// traverse rows
		var rowspan = 0;
	this.find('tbody tr').each(function(rowi, rowo) {
	if(rowi > 0) {
		$(rowo).find('td').each(function(coli, colo) {
			var tempRowSpan = Number($(colo).attr('rowspan'));
			
			if(coli > 0 || rowspan > 0 || stepn == 0) {
						$(colo).find('p').each(function(cbi, cbo) {
							var criteria = { step: stepn, row: rowi, level: coli, checkbox: cbi, pracsot: 'empty' };
							var criteriaStr = JSON.stringify(criteria);
							$(cbo).html("<label><input type='checkbox' id='c"+stepn.toString()+rowi.toString()+coli.toString()+cbi.toString()+"' data-criteria='"+criteriaStr+"'>"+$(this).text()+"</label>");
						});
			}
			if (typeof tempRowSpan !== 'undefined' && tempRowSpan !== false) {
				if(!isNaN(tempRowSpan) && rowspan == 0) {
					rowspan = tempRowSpan;
				} 
			}
		});
		rowspan = rowspan>0 ? rowspan-1 : 0;
	}
	});
	};
	
	$.fn.removeCriteria = function() {
		$me.find('div.popup').remove();
		return this;
	};
	
	$.fn.showCriteria = function() {
		$me.mydiv = this.clone();
		var l = this.offset().left; // get UI offsets
		var t = this.offset().top;
		if($me.mydiv.hasClass("file-info")) {
			$me.mydiv.removeClass("file-info");
			$me.mydiv.addClass('popup');
			$me.mydiv.html("");
		}
		
		$.each(this.data('crit'), function(i, o) {
				var stepn = o[0];
				var rown = o[1];
				var coln = o[2];
				var cbxn = o[3];	
				console.log(o.toString());
		//if(window.anchor == undefined) window.anchor = window.mydiv.html();
		
			$me.find('tbody').each(function(tablei, table) {
			if(tablei+1 == stepn) {
				$(table).find('tr').each(function(rowi, rowo){
				if(rowi == rown) {
						$(rowo).find('td').each(function(coli, colo) {
							if(coli == coln) {
										$(colo).find('p').each(function(cbi, cbo) {
											if(cbi == cbxn) {
												$me.mydiv.append("<p class='criteria'>"+$(cbo).html()+"</p>");
												$me.mydiv.find("input").remove();
											}
										});
							}
						});
				}
				});
			}
		});
			});	

		$me.append($me.mydiv);	
		$me.mydiv.offset({ top : t+10, left : l+300 });
		
		return this;
	};

	$.fn.setSelectedTab = function() {
		$($me.currentTableView).hide();
		$($me.currentTableView).css('border-width','thin');
					
		$me.lastTabSelected = this;
		
		var tableid = this.find('option:selected').data('tableid');
		
		$me.currentTableView = $('table').get(tableid);
		$($me.currentTableView).css('border-width', '3px');
		$($me.currentTableView).show();
		
		return this;
	};
	
	/* setup table drop-down selector */
	$me.prepend("<select id='table-nav'></select><br><label for='search'>or Search all steps:</label><input width=\"100px\" type=\"text\" name=\"search\" style=\"margin-top:7px\"><button id='clearSearch'>Clear </button>");
	
	$me.find('table').each(function(i,o) {
		if(i>0)  {
			$(o).hide();
		}
		else {
			$me.currentTableView = o;
			$($me.currentTableView).show();
		}
		
		console.log('table n = '+i);
		$(o).addCheckBoxes(i);
		
		var selectTxt = "";
		if(i==0) { selectTxt = "Being a Professional"; } else { selectTxt = "Step "+i+" - "+window.stepDefinitions[i]; }
		
		$me.find("select#table-nav").append('<option data-tableid='+i+'>'+selectTxt+'</option>');
	});
	/*
		removes duplicate entries in array1 from array2
	*/
	var removeDuplicates = function(array1, array2){
		$(array2).each(function(i, v) {
			var loc = array1.indexOf(v);
			if(loc != -1) {
				array1.splice(loc, 1);
			}
		});
			
	return array1;
	};
	
	$.fn.addTableRow = function(step, level, statement) {
		if(statement.text().length == 0) { return this; }
			
		var rowspanned = isNaN($(this).data('rows-spanned'))?1:$(this).data('rows-spanned');
		var prev = String($(this).data('prev-step'));
		
		if(step != null && prev == step) {
			$(this).find('td#firstCol').last().attr('rowspan', ++rowspanned);
			$(this).append("<tr><td>"+level+"</td></tr>").find('td').last().after(statement);
		} else {
			rowspanned = 1;
			$(this).append("<tr><td id='firstCol'>"+step+"</td><td>"+level+"</td></tr>").find('td').last().after(statement);
		}
		
		$(this).find('p').last().wrap('<td />');
		
		$(this).data('prev-step', step);
		$(this).data('rows-spanned', rowspanned); 
		return this;
	};
	
	$me.on("mouseenter mouseleave", "td p", function(event) {
		if (event.type == 'mouseenter') {
			var pracsotChck = $(this).data('pracsot');
			if(typeof pracsotChck === 'undefined') return false;
			var pracsotArr = $(this).data('pracsot').split(',');
			
			//console.log($(this).attr('data-pracsot')+" && "+$(this).data('pracsot'));
			var pracsotStr = "<span id='pracsot-crit'><br><br>PRACSOT Performance Criteria:<br>";
			var comma = "";
			$(pracsotArr).each(function(i, v) {
				if(i > 0) comma = ", ";
				pracsotStr += comma+"<a class='getPracsot' href='"+pracsotChck+"'>"+v+"</a>";
			});
			
			pracsotStr += "</span>";
			$(this).append(pracsotStr).css({border: 'thin solid #ccc'}).animate({borderWidth: "5px", margin: "-=2px", padding : "+=3px"}, 100);
			
		} else {
			$(this).animate({ borderWidth : "0px", margin: "+= 2px", padding : "-=3px" }, 100).css({ border : 'none' });
			$(this).find('#pracsot-crit').remove();  
		}	
	});
	
	var resetSearch = function() {
		$('input[name=search]').val('');
		$("table#searchResults").fadeOut().remove();
		$($me.currentTableView).fadeIn();
	}
	
	$me.find('button#clearSearch').click(function(e) {
		resetSearch();
	});
	
	$me.on('change','input[name=search]', function(e) {
		if($(this).val().length == 0) {
			resetSearch();
			return;
		}
		$("table#searchResults").fadeOut().remove(); //reset previous search
		
		var s = $(e.target).val().trim().toLowerCase();
		
		var searchTermArray = s.split(" ");
		var pracArray = s.match(/([1-99]*\.?)+/g);
		
		var resultTable = "<table id='searchResults' style='display: none'><tr><th>Step</th><th>Level</th><th>Competency Statement</th></tr></table>";
		$me.find('.file-viewer').first().before(resultTable);
		
		var numResults = 0
		// check for pracsot codes if they exist in search
		if(typeof pracArray != 'undefined' && pracArray != null && pracArray.length > 0) {
			$me.find('table td p').each(function(i, o) {
				for(var j=0;j<pracArray.length;j++) {
					if(pracArray[j].length > 0) {
					if(typeof $(o).data('pracsot') != 'undefined') {
						if($(o).data('pracsot').indexOf(pracArray[j]) != -1) {
							var crit = $(o).find('input').data('criteria');
							if(typeof crit != 'undefined') {
								if(crit.level != 0 && $(o).text().length > 0) {
									$('table#searchResults').addTableRow(window.stepDefinitions[crit.step+1], crit.level, $(o).clone());
									++numResults;
								}
							}
						}
					}
				}	
				}
			});
		}
	
	searchTermArray = removeDuplicates(searchTermArray, pracArray);
	
	$.each(searchTermArray, function(i, s) {
			if(numResults == 0) {
					$me.find('table td p:Contains('+s+')').each(function(i, o) {
						var crit = $(o).find('input').data('criteria');
						if(typeof crit != 'undefined') {
							if(crit.level != 0 && $(o).text().length > 0) {
								$('table#searchResults').addTableRow(window.stepDefinitions[crit.step+1], crit.level, $(o).clone());
								++numResults;
							}
						}
					});
			} else {
					$('table#searchResults td p:not(:Contains("'+s+'"))').closest('tr').remove();
					numResults = $('table#searchResults tr').length-1;
			}
	});
	
	if(numResults < 1) {
		$('table#searchResults').append('<tr><td colspan=3>No results found.</td></tr>');
	}		
	$me.find('table').hide();
	$('table#searchResults').fadeIn();
	});
	
	$me.on('change', "select#table-nav", function(e) 
	{
		$(e.target).setSelectedTab();
	});
	
	/* add evidencing buttons */
	$me.prepend("<a class='button file-box'>Add evidence for the selected criteria</a><a class='button show-files'>Show my existing evidence for the selected criteria</a>");
		
	$me.on('click', '.file-box', function(e) {
		e.preventDefault();
		
		var data = getCheckedCrit();
		var	sData = makeSearchable(data[0]);
		var pracsotStr = data[2].join(',');	
		
		var critJSONdata = "";
		
		$.each(data[0], function(i, o) {
			if(i>0) critJSONdata += ",";
			critJSONdata += JSON.stringify(o);
		});
					
		$viewer.load('/pages/evidence-upload', 
		function() { 
			    $viewer.find("form#publishForm #criteria_mapping").text(critJSONdata);
			    $viewer.find("form#publishForm #searchable_mapping").text(JSON.stringify(sData));
				$viewer.find('form#publishForm textarea#supervisor_emails').after("<input id='emailMe' name='emailMe' type='checkbox'/><label for='emailMe'>Send me a copy of this email.</label>")
			    $viewer.prepend(data[1]).show(100);
			    
				$("div.ajax-loader").hide();
				
				$viewer.find('#supervisor_emails').hide().before("<input id='asminput' name='asminput'></input><button id='addsup'>Add supervisor</button><br><ul id='emailList'></ul>");
				var supArray = [];
				$(this).on('click','#addsup', function(e) {
					e.preventDefault();	
					var email = $('#asminput').val();
					if(0 == email.length) return false;
					var i = supArray.indexOf(email);
					if(-1 == i) { 
						i = supArray.push(email)-1;
						$('#emailList').append('<li data-index="'+i+'">'+email+'   <a href="#">Remove</a></li>');
						$('#supervisor_emails').val(supArray.join());
					} else {
						$('#emailList li').each(function(li_index, v){
							if($(v).data('index') == i) {
								//$(v).fadeOut(1000).fadeIn(1000);
								$(v).fadeOut(20).queue(function(next) {$(this).css({'background-color': '#FFF', 'border-color':'yellow'}); next();}).fadeIn(100).delay(50).queue(function(next) { $(this).css({'background-color': 'inherit', 'border-color': '#aaa'}); next(); });
							}
						});
					}
				});

				$(this).on('click', 'ul#emailList a', function(e) { 
					e.preventDefault();
					var index = $(this).parents('li').data('index');
					supArray.splice(index, 1);
					$(this).parents('li').remove();
					$('#supervisor_emails').val(supArray.join());
				});
			
			    $viewer.find('#publishForm').ajaxForm({
			        dataType: 'json',
			        success: function(data) {
			                if (data.success) {
			                       title = data.title;
			                       var emails = $("textarea#supervisor_emails").val();
									var fullPath = $("div.safecracker_file_input input[name='evidence']").val();
									var filename = fullPath.replace(/^.*[\\\/]/, '');
									var includeUserInEmail = ($('#emailMe:checked').length == 0)?0:1;  
									$.get('/ajax/map-evidence?id='+data.entry_id+"&emails="+emails+"&filename="+filename+"&pracsot="+pracsotStr+"&iu="+includeUserInEmail, function(data) {
										var jsonData = JSON.parse(data);
										$viewer.hide();
										$dialog.css({ width: "400px", height : "120px", left: "850px" });
										$dialog.find('span#message').html(jsonData.message);
										$dialog.show(250);
										$("div.ajax-loader").hide();
									});
			                } else {
			                        var str = "<p>The following errors with your form were reported:<br>";

								    $('input, textarea').each(function(i,v){
								    var id = $(v).attr('id');
								    if(typeof data.field_errors[id] != 'undefined') {
								    	str += data.field_errors[id]+"<br>";
								    		$(v).css({ "background-color" : "yellow" });
								    	} else {
								    		$(v).css({ "background-color" : "none" });	
								    	}
								    });
								    str += "</p>";
								
								   $dialog.find('span#message').html(str);
								   $dialog.show(250);           
								}
			        		}
			    });
			 });
	    });
		
	$me.on('click', '.show-files', function(e) {
		e.preventDefault();
		var data = getCheckedCrit();
		data = makeSearchable(data[0]);
		var uristr = data.join('|');
		$viewer.html("").load('http://otaltc.net/pages/evidence-list/'+uristr, function() { $viewer.show(100);  $("div.ajax-loader").hide(); }); 			
	});
	
	var getCheckedCrit = function() {
		var map = [];
		var pracsot = [];
			var selStr = "<div class='file-viewer-inner scrollbar'><p><strong>This piece of evidence will be provided for the following criteria at level "+levelName+"</strong></p>";
			$('input:checked').each(function(is, io) {
				$(io).attr("checked", "true");
				var d = $(io).data('criteria');
			    var p = $(io).closest('p').data('pracsot');
				d.pracsot = p;
			 	map.push(d);
			    pracsot.push(p);
				selStr += "<p>"+$(io).parent().html()+"</p>";
			});
			selStr += "</div>";
	return [map, selStr, pracsot];
	};
	
	/*var getPracsotIDs = function(map) {
		
	};*/
	
	var makeSearchable = function(map) {
		var newMap = [];
		$(map).each(function(i, ar) {
			var str = 'c';
			$(ar).each(function(i, obj){
				str += String(obj.step)+String(obj.level)+String(obj.row)+String(obj.checkbox);
			});
			newMap.push(str);
		}
		);
		return newMap;
	};
	
	$viewer.on('mousedown', 'div.file-info', function(e) {			
			$(e.target).showCriteria();
	}).on('mouseup', 'div.file-info', function(e) {
		console.log('mouseleave');
		$(e.target).removeCriteria();
	});

	$me.find("a.button").css('visibility','hidden');
	
	$me.on('click', "td input", function(e) {
		if($('td input:checked').length > 0) 
			$me.find("a.button").css('visibility', 'visible');
		else 
			$me.find("a.button").css('visibility', 'hidden');
	});
	
	$me.on('click', 'div.file-viewer a.exit', function(e) {
			e.preventDefault();
			$(this).closest('div.file-viewer').hide(100);
	}); 
	
	$me.ajaxStart(function(){
	    $("div.ajax-loader").show();
	 }).ajaxStop(function(){
	    $("div.ajax-loader").hide();
	 });
		
	$(this).on('click', 'a.getPracsot', function(event) {
		event.preventDefault();
		
		var matrixS = $(this).parent().prev('label').text();
		$pracsot_viewer.html("<a href='#close' class='exit'><img src='/img/close-icon.png' alt='close'></a><h3>PRACSOT Performance Criteria Details</h3><p><em>These are the PRACSOT Performance Criteria that this Evidencing Matrix competency statement is fulfilling.</em></p><h4>Evidencing Matrix Competency Statement</h4><p>"+matrixS+"</p><div class='file-viewer-inner'></div>");
		
		var array = [];
		
		$.get('/pracsot/select-pracsot?ids='+$(event.target).attr('href'), function(data) {
			array = JSON.parse(data);
				
			$pracsot_viewer.find('.file-viewer-inner').html('').append("<table><tbody>");
			$(array).each(function(i,v) {
				var elprefix = v.id.match(/[^.]*.[^.]*/);
				$pracsot_viewer.find('.file-viewer-inner').append("<tr><td><a href='/pracsot/pracsot-unit-table?element="+elprefix+"#"+v.id+"' target='_blank'>"+v.id+"</a></td><td>"+v.question+"</td></tr>");
			});
			$pracsot_viewer.find('.file-viewer-inner').append("</tbody></table>");
			$pracsot_viewer.show();
		});
	});
};

})( jQuery );


