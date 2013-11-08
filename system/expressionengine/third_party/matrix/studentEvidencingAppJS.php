<?= $emptyUserProfile ?>

   (function( $ ) {
$.fn.evidencing = function() {
	var $me = this;
	var entry_id = <?= $id ?>;
	// last search results
	var $searchResults = null;
	var legend = "<div style=\"clear: both; width: 100%; padding-bottom: 20px\">\
<h3><?= $legend_title ?></h3>\
<div style=\"border: thin solid white; background-color: #<?= $colors[0] ?>; display:inline;\">\
&nbsp;&nbsp;&nbsp;&nbsp;</div><label style=\"margin-left:8px\">\
= <?= $current ?> &nbsp;&nbsp;&nbsp;</label>\
<div style=\"border: thin solid white; background-color: #<?= $colors[1] ?>;display:inline\">\
&nbsp;&nbsp;&nbsp;&nbsp;</div>\
<label style=\"margin-left:8px;margin-right:8px\">= <?= $previous ?> </label>\
<div style=\"border: thin solid white; background-color: #<?= $colors[2] ?>;display:inline\">\
&nbsp;&nbsp;&nbsp;&nbsp;</div>\
<label style=\"margin-left:8px;margin-right:8px\">= <?= $waiting ?> </label></div>";

<?= $assessed_items_js ?>
<?= $self_assessed_item_js ?>

	jQuery.expr[":"].Contains = jQuery.expr.createPseudo(function(arg) {
	    return function( elem ) {
	        return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
	    };
	});
	
	/* setup table drop-down selector -updated */
	$me.prepend("<?= $step_label ?> <select id='table-nav'></select>");
			
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
	
	var radioCount = 0;	
	/* verified matrix version */
	$.fn.addCheckBoxes = function(stepn) {
		var verifyCheckBox = function(criteria) {
			
			/*if(typeof assessed_items == 'undefined') {
				wasAssessed = false;
			}*/
			var bgcolor = '';
		
		var str = "";
			var wasAssessed = false;
			var selfChecked = false;
			var assessor_agreed = false;
			//console.log(">>>>>>>>>>>>>>> "+self_assessed_item.self_assessment);
			if(typeof self_assessed_item !== 'undefined' && self_assessed_item !== false) {
			$(self_assessed_item).each(function(saindex, saobject) { 
			if(typeof saobject.self_assessment !== 'undefined' && saobject.self_assessment.length > 0) {
				var statements = JSON.parse(saobject.self_assessment);
				var item = saobject;
				$(statements).each(function() {
				
				//console.log(this);
				if(criteria.step == this.step &&
					criteria.row == this.row &&
					criteria.level == this.level &&
					criteria.checkbox == this.checkbox
				) {	
					// only 1 assessor per criteria
					if(str.length==0) {
						wasAssessed = false; 
						bgcolor = "#1BBFE0";
					
				var radioButtons = "This competency statement has not yet been verified by a supervisor.<br>";
				radioCount++;
				selfChecked = true;
				str += radioButtons;
				console.log('entry_id '+entry_id+'item.entry_id '+item.entry_id);
				if(!item.is_current_entry) {
				str += "<br><a href='/pages/assessed-matrix/"+item.entry_id+"/"+item.title+"'>View Unverified Supporting Evidence/Description:<br>"+ item.title +"   &ndash;   "+ item.entry_date+"</a>";
				}
						//str += item.screen_name + ", <a style='color:"+bgcolor+"' href='mailto:"+item.email+"'>"+item.email+"</a>";
					}
				}
				});
			}
			});
			}
			if(typeof assessed_items !== 'undefined' && assessed_items.length > 0) {
				
				var lastDate = 0;
				var current_assessed_item_filter = $(assessed_items).filter(
				function(a) {
					return (this.is_current_entry);
				}
				);
				
				$(current_assessed_item_filter).each(function() {
				var item = this;
				var statements = JSON.parse(this.supervisor_assessment);
				$(statements).each(function() {
				if(criteria.step == this.step &&
					criteria.row == this.row &&
					criteria.level == this.level &&
					criteria.checkbox == this.checkbox
				) {
					
					/* 	Only 1 assessor per criteria
					* 	This will only place the top item in the 'assessed item' array in the statement box.
					*	The array is sorted in descending raw sql date order in the SQL statement, so the last
					*	assessment will always be on top.
					*/		
					if(str.length==0) {
						wasAssessed = true;
						bgcolor = window.yellow;
						var agreed;
						var disagreed;
						if(typeof this.agreed !== 'undefined') {
							agreed = this.agreed == 1 ? "checked" : "";
							disagreed = this.agreed != 1 ? "checked" : "";
						} else {
							agreed = "checked";
							disagreed = "";
						}
						assessor_agreed = (this.agreed == 1);	
				var radioButtons = "Agree: <input type='radio' value='1' name='radio"+radioCount+"' "+agreed+" disabled='disabled'> Disagree: <input value='0' type='radio' name='radio"+radioCount+"' "+disagreed+" disabled='disabled'><br>";
				radioCount++;
						str += radioButtons + "Assessed by: "; 
						str += item.screen_name + ", <a style='color:"+bgcolor+"' href='mailto:"+item.email+"'>"+item.email+"</a>";
					}
				}
				});
			});
			
			var other_assessed_item_filter = $(assessed_items).filter(
					function(a) {
						return (!this.is_current_entry);
					}
			);
			
				$(other_assessed_item_filter).each(function() {
					var item = this;
					var statements = JSON.parse(this.supervisor_assessment);
					$(statements).each(function() {
					if(criteria.step == this.step &&
						criteria.row == this.row &&
						criteria.level == this.level &&
						criteria.checkbox == this.checkbox
					) {

						// only 1 assessor per criteria
						if(str.length==0) {
							wasAssessed = true;
							bgcolor = window.purple;
							var agreed = this.agreed == 1 ? "checked" : "";
							var disagreed = this.agreed != 1 ? "checked" : "";
							assessor_agreed = (this.agreed == 1);	
					var radioButtons = "Agree: <input type='radio' value='1' name='radio"+radioCount+"' disabled='disabled' "+agreed+"> Disagree: <input type='radio' value='0' name='radio"+radioCount+"' disabled='disabled' "+disagreed+"><br>";
					radioCount++;
							str += radioButtons + "Assessed by: "; 
							str += item.screen_name + ", <a style='color:"+bgcolor+"' href='mailto:"+item.email+"'>"+item.email+"</a><br>";
							str += "<br><a href='/pages/assessed-matrix/"+item.entry_id+"/"+item.title+"'>View Supporting Evidence/Description:<br>"+ item.title +"   &ndash;   "+ item.entry_date+"</a>";
						}
					}
					});
					/*if(str.length == 0){
					var statements = JSON.parse(this.self_assessment);
					$(statements).each(function() {
					if(criteria.step == this.step &&
						criteria.row == this.row &&
						criteria.level == this.level &&
						criteria.checkbox == this.checkbox
						) {
							bgcolor = "pink";
							str += "<span style='color: pink'>This is your self-assessment of another piece of evidence which has not yet been validated.</span>";	
							selfChecked = true;
						}
					});
					}*/
				});
			}
			var isChecked = wasAssessed ? assessor_agreed : selfChecked; 
			return { assessorsStr : str, wasAssessed : wasAssessed, isChecked : isChecked, highlightColor: bgcolor};			
		}

	// traverse rows
	var rowspan = 0;
	var boxesAssessed = [];
	this.find('tbody tr').each(function(rowi, rowo) {
	if(rowi > 2) {
		var nocells = $('td', rowo).length;
		$(rowo).find('td').each(function(coli, colo) {
			//var tempRowSpan = Number($(colo).attr('rowspan'));

			if(! (nocells == 4 && coli == 0) ) {// skip column headers
				//if(nocells < 4) coli += 1;
						$(colo).find('p').each(function(cbi, cbo) {
							//var tcoli = (stepn==0) ? coli + 1 : coli;
			
							var criteria = { step: stepn, row: rowi, level: coli, checkbox: cbi, pracsot: 'empty' };
							var assessCheck = verifyCheckBox(criteria);			
							var assessed = "<p style='color: "+assessCheck.highlightColor+"; font-size: 12px'>  "+assessCheck.assessorsStr+"</p>";
							var checked =  assessCheck.isChecked?"checked":"";	
							var criteriaStr = JSON.stringify(criteria);
							$(cbo).html("<label><input type='checkbox' id='c"+stepn.toString()+rowi.toString()+coli.toString()+cbi.toString()+"' data-criteria='"+criteriaStr+"' "+checked+">"+$(this).text()+assessed+"</label>");
							if(assessCheck.wasAssessed || assessCheck.isChecked) { $(cbo).css({'border':'1px solid '+assessCheck.highlightColor}); }
						});
			}
		});
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
	
	/* add feedback and assessment date, assessor name*/
	var wasAssessed = false;
	var acount = 0;	
	if(typeof assessed_items !== 'undefined') {
		
		$(assessed_items).each(function(){
			
			if(this.is_current_entry)  { 
				$("#notAssessed").remove();
				wasAssessed = true;
				var asssed_by_str = acount === 0 ? "Last Assessed by:  " : "Previously Assessed by:  ";
				$(".feedback p:nth-of-type(2) ").before("<strong style='color: blue'>"+asssed_by_str+this.screen_name+" on: "+this.date_assessed+"</strong><br><br>"+this.feedback+"<br><br>");
				acount++;
				//$me.before("<p class='feedback'><strong style='color: red'>Last Assessed by: "+assessed_items[0].screen_name+" on "+assessed_items[0].date_assessed+"</strong><br><br>"+assessed_items[0].feedback+"</p>");
			}
		});
	}
	
	if(!wasAssessed && $.type(entry_id) !== 'string') {
		$(".feedback").append("<strong style='color: red' id='notAssessed'>This evidence has not yet been assessed.</strong>");
	}
		
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
	
	$.fn.additionalCellInfo = function(selectedTableId) {
	//console.log('called '+$(this).data('pracsot'));
	
	if($("span", this).length == 0) {
	    var pracsotChck = $(this).data('pracsot');
			if(typeof pracsotChck === 'undefined') return false;
			var pracsotArr = $(this).data('pracsot').split(',');
			
			var level = $(this).closest('td').index();
			var cellCount = $(this).closest('td').parent().find("td").length;
			
			if(cellCount == 3) {
				level = level + 1;				
			}
			
			var row = $(this).closest("tr").index()-2;
				
			var pracsotStr = selectedTableId == 0 ? "" : "<span><br><a id='behaviours' data-level='"+level+"' data-row='"+row+"' href='#'>Observed behaviours</a></span>";
			pracsotStr += "<span id='pracsot-crit'><br><br>PRACSOT Performance Criteria:<br>";
			var comma = "";
			$(pracsotArr).each(function(i, v) {
				if(i > 0) comma = ", ";
				pracsotStr += comma+"<a class='getPracsot' href='"+pracsotChck+"'>"+v+"</a>";
			});
			
			pracsotStr += "</span>";
			$(this).append(pracsotStr);
	}
	return this;
	}
	
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
	
	$(document).on('click', 'span > a#behaviours', function(e) {
		e.preventDefault();
		$("div.ajax-loader").show();
		var $dialog = $('div.file-viewer.dialog');
		$dialog.css({ width: "400px", height : "auto", left: "546px", top: "499px", maxHeight: "400px", overflow: "auto" });
		
		var level = $(this).data('level');
		var step = $("#table-nav option:selected").data("tableid");
		var row = $(this).data('row'); // don't include header rows
		
		$("#message", $dialog).load("/ajax/get-behaviours/"+step+"/"+level+"/"+row, function() {
			$dialog.show(250);
			$("div.ajax-loader").hide();
		});
	
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
	
	
	var add_evidence = function() {
		
		$("td input[type='checkbox']").removeAttr('disabled').removeAttr('checked');
		
		$('select#table-nav').after(<?= $info ?>);
		if(entry_id != 'add_evidence') {
			$("#save_otcem").after("<p style='font-size: 14pt'><a href='#' id='cancel' style='color: #639'>Cancel upload of evidence and refresh.</a></p>");
		}
		
		$me.on('click', '#cancel', function(e) {
			e.preventDefault();
			window.location.reload();
		});
		
		$me.on('click', '#save_otcem', function(e) {
		e.preventDefault();
		
		var data = getCheckedCrit();
		var	sData = makeSearchable(data[0]);
		var pracsotStr = data[2].join(',');	
		
		var critJSONdata = [];
		
		$.each(data[0], function(i, o) {
			critJSONdata.push(o);
		});
					
		$viewer.load('/pages/evidence-upload', 
		function() { 
			    $viewer.find("form#publishForm #criteria_mapping").text(JSON.stringify(critJSONdata));
			    $viewer.find("form#publishForm #searchable_mapping").text(JSON.stringify(sData));
				$viewer.find('form#publishForm textarea#supervisor_emails').after("<input id='emailMe' name='emailMe' type='checkbox'/><label for='emailMe'>Send me a copy of this email.</label>");
			    $viewer.find("form#publishForm input[name='cycle_name']").val(window.userProfile.title);
			    $viewer.show();
			    
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
					return false;
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
								//alert(pracsotStr);
								var fullPath = $("div.safecracker_file_input input[name='evidence']").val();
								var filename = fullPath.replace(/^.*[\\\/]/, '');
								
								// emails now sent via share interface
								$.get('/ajax/map-evidence?id='+data.entry_id+"&filename="+filename, function(data) {
									var jsonData = JSON.parse(data);
									
									$viewer.html("");
									$viewer.hide();
									$dialog.css({ width: "400px", height : "220px", left: "546px", top: "499px" });
									
									var strLinks = "<p><a class='button' href='/practice-placement/summary-of-your-competencies?show="+jsonData.id+"'>View this evidence in your ePortfolio</a></p>";
									strLinks += "<p><a class='button' href='javascript:document.location.reload(true);' >Add another piece of evidence</a></p>";
									
									$dialog.find('span#message').html("<h3>Upload Successful</h3>"+jsonData.message+strLinks);
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
	}
	
	$me.on('click', '.show-files', function(e) {
		e.preventDefault();
		var data = getCheckedCrit();
		data = makeSearchable(data[0]);
		var uristr = data.join('|');
		$viewer.html("").load('/pages/evidence-list/'+uristr, function() {
		  $viewer.show(100);
		  $("div.ajax-loader").hide();
	       }); 			
	});
	
	var getCheckedCrit = function() {
		var map = [];
		var pracsot = [];
			var selStr = "<div class='file-viewer-inner scrollbar'><p><strong>This piece of evidence will be provided for the following criteria at level "+levelName+"</strong></p>";
			$('input:checked').each(function(is, io) {
				$(io).attr("checked", "true");
				var d = $(io).data('criteria');
			    var p = $(io).closest('p').data('pracsot');
				if(typeof p != 'undefined' && null != p && p.length > 0) {
					p = p.trim();
					d.pracsot = p;
			 		if(-1 == map.indexOf(d))
					{	
						map.push(d); 
					}
					if(-1 == pracsot.indexOf(p)) {
			    		pracsot.push(p);
					}
					selStr += "<p>"+$(io).parent().html()+"</p>";
				}
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
		
		var matrixS = $(this).parent().prevAll('label').text();
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
	
	$me.on('change', '#table-nav', function() {
	     var selectedTableId = $('option:selected', this).data("tableid");
        $("table:eq("+selectedTableId+") td p[data-pracsot]").each(function() { $(this).additionalCellInfo(selectedTableId) });
        $("table:eq("+selectedTableId+") a").first().focus();  
	});
	
	$("input[type='checkbox']", $me).attr('disabled', 'disabled');
	$me.find('#table-nav').after(legend);
	$('#table-nav').trigger('change');
	// add evidence view
	if(entry_id == 'add_evidence') {
		add_evidence();
	}
};

})( jQuery );

$(document).ready(function() {
    $('<?= $selector ?>').evidencing();
});
