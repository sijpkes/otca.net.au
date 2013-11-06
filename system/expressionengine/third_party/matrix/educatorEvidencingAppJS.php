<script> 
    window.stepDefinitions = [ "null", "Request for Service", "Information Gathering", "Occupational Assessment", 
                                "Identification of Occupational Issues", "Goal Setting", "Intervention",
                                "Evaluation", "Being a Professional" ];

 (function( $ ) {
$.fn.evidencing = function() {
    var $me = this;
    var entry_id = <?= $entry_id ?>;
 
    // last search results
    var $searchResults = null;
    var legend = "<div style=\"clear: both; width: 100%; padding-bottom: 20px\">\
<h3>Assessment/Verification Legend</h3>\
<div style=\"border: thin solid white; background-color: #FFB510; display:inline;\">\
&nbsp;&nbsp;&nbsp;&nbsp;</div><label style=\"margin-left:8px\">\
= Current Assessed Competencies&nbsp;&nbsp;&nbsp;</label>\
<div style=\"border: thin solid white; background-color: #852C8A;display:inline\">\
&nbsp;&nbsp;&nbsp;&nbsp;</div>\
<label style=\"margin-left:8px;margin-right:8px\">= Previously Assessed Competencies</label>\
<div style=\"border: thin solid white; background-color: #1BBFE0;display:inline\">\
&nbsp;&nbsp;&nbsp;&nbsp;</div>\
<label style=\"margin-left:8px;margin-right:8px\">= Self-Assessed Competencies Awaiting Assessment</label></div>";
    
<?= $assessed_items_js ?>
<?= $self_assessed_item_js ?>
var suid = <?= $student_id ?>

    jQuery.expr[":"].Contains = jQuery.expr.createPseudo(function(arg) {
        return function( elem ) {
            return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
        };
    });
    
    /* setup table drop-down selector */
    $me.prepend("<br clear='both'>View step: <select id='table-nav'></select>");
    
    $("strong#submitted_by").html("Submitted by: <?= $student_screen_name ?> - <a href='mailto: <?= $student_email ?>'><?= $student_email ?></a>");     
    /* setup file viewer window and ajax loader */
    $me.append("<div class='file-viewer upload-box' style='display: none;'> </div>");
    $me.append("<div class='file-viewer dialog' style='display: none;'><a href='#close' class='exit'><img src='/img/close-icon.png' alt='close'></a><span id='message'></span></div>");
    $me.append("<div class='file-viewer pracsot' style='display: none;'>\
                </div>");
    $me.append("<div class='ajax-loader' style='display: none;'> </div>");
    var $viewer = $('div.file-viewer.upload-box');
    var $pracsot_viewer = $('div.file-viewer.pracsot');
    var $dialog = $('div.file-viewer.dialog');
        
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
            //var currentAssessment = false;
            
            /* check if this is an un-verified AND self-assessed competency statement */    
            if(typeof self_assessed_item !== 'undefined' && 
                self_assessed_item !== false &&
                typeof self_assessed_item.self_assessment !== 'undefined' &&
                 self_assessed_item.self_assessment !== false) {
             var statements =  self_assessed_item.self_assessment.length > 0 ? JSON.parse(self_assessed_item.self_assessment) : {};
                $(statements).each(function() {
                if(criteria.step == this.step &&
                    criteria.row == this.row &&
                    criteria.level == this.level &&
                    criteria.checkbox == this.checkbox
                ) {
                    //currentAssessment = true;
                    // only 1 assessor per criteria
                    if(str.length==0) {
                        wasAssessed = false; 
                        bgcolor = "#1BBFE0";
                    
                var radioButtons = "This is the student's self-assessment of achieving this competency statement<br>Agree: <input type='radio' value='1' name='radio"+radioCount+"' checked='checked'> Disagree: <input type='radio' value='0' name='radio"+radioCount+"'><br>";
                radioCount++;
                selfChecked = true;
                str += radioButtons;
                if(!item.is_current_entry) {
                str += "<br><a href='/pages/educator-matrix/"+item.entry_id+"/"+suid+"/"+item.title+"'>View Unverified Supporting Evidence/Description:<br>"+ item.title +"   &ndash;   "+ item.entry_date+"</a>";
                }
                        //str += item.screen_name + ", <a style='color:"+bgcolor+"' href='mailto:"+item.email+"'>"+item.email+"</a>";
                    }
                }
                });
            }
            if(typeof assessed_items !== 'undefined' && assessed_items.length > 0) {
                /* check if this is an verified AND self-assessed competency statement */   
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
                    //currentAssessment = true;
                    // only 1 assessor per criteria
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
                var radioButtons = "Agree: <input type='radio' value='1' name='radio"+radioCount+"' "+agreed+"> Disagree: <input value='0' type='radio' name='radio"+radioCount+"' "+disagreed+"><br>";
                radioCount++;
                        str += radioButtons + "Assessed by: "; 
                        str += item.screen_name + ", <a style='color:"+bgcolor+"' href='mailto:"+item.email+"'>"+item.email+"</a>";
                    }
                }
                });
            });
            }
            
            if(typeof assessed_items !== 'undefined' && assessed_items !== false) {
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
                            bgcolor = window.purple;
                            var agreed = this.agreed == 1 ? "checked" : "";
                            var disagreed = this.agreed != 1 ? "checked" : "";
                            assessor_agreed = (this.agreed == 1);   
                    var radioButtons = "Agree: <input type='radio' value='1' name='radio"+radioCount+"' "+agreed+"> Disagree: <input type='radio' value='0' name='radio"+radioCount+"'"+disagreed+"><br>";
                    radioCount++;
                            str += radioButtons + "Assessed by: "; 
                            str += item.screen_name + ", <a style='color:"+bgcolor+"' href='mailto:"+item.email+"'>"+item.email+"</a><br>";
                            str += "<br><a href='/pages/educator-matrix/"+item.entry_id+"/"+suid+"/"+item.title+"'>View Supporting Evidence/Description:<br>"+ item.title +"   &ndash;   "+ item.entry_date+"</a>";
                        }
                    }
                    });
                });
            }
            var isChecked = wasAssessed ? assessor_agreed : selfChecked; 
            return { assessorsStr : str, wasAssessed : wasAssessed, isChecked : isChecked, highlightColor: bgcolor/*, currentAssessment: selfChecked*/};            
        } // end addCheckBoxes
    
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
                            var criteria = { step: stepn, row: rowi, level: coli, checkbox: cbi, pracsot: 'empty' };
                            var assessCheck = verifyCheckBox(criteria);
                            /*if(assessCheck.currentAssessment) {
                                $(cbo).addClass('current');    
                            }*/
                            var assessed = "<p style='color: "+assessCheck.highlightColor+"; font-size: 12px'>  "+assessCheck.assessorsStr+"</p>";
                            var checked =  assessCheck.isChecked?"checked":"";  
                            var criteriaStr = JSON.stringify(criteria);
                            var selfAssessed = (!assessCheck.wasAssessed && assessCheck.isChecked) || assessCheck.wasAssessed ? "class='preclicked'" : "";
                            $(cbo).html("<label><input type='checkbox' id='c"+stepn.toString()+rowi.toString()+coli.toString()+cbi.toString()+"' data-criteria='"+criteriaStr+"' "+checked+" "+selfAssessed+">"+$(this).text()+assessed+"</label>");
                            if(assessCheck.wasAssessed || assessCheck.isChecked) { $(cbo).css({'border':'1px solid '+assessCheck.highlightColor}); }
                        });
            }
            /*if (typeof tempRowSpan !== 'undefined' && tempRowSpan !== false) {
                if(!isNaN(tempRowSpan) && rowspan == 0) {
                    rowspan = tempRowSpan;
                } 
            }*/
        });
        //rowspan = rowspan>0 ? rowspan-1 : 0;
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
    
   /* $me.on("mouseenter", "td:not(.nocheckbox) p", function(event) {
        //$("span", this).remove();
        if($("span", this).length == 0) {
        if (event.type == 'mouseenter') {
            var pracsotChck = $(this).data('pracsot');
            if(typeof pracsotChck === 'undefined') return false;
            var pracsotArr = $(this).data('pracsot').split(',');
            
            var level = $(this).closest('td').index();
            var cellCount = $(this).closest('td').parent().find("td").length;
            
            if(cellCount == 3) {
                level = level + 1;              
            }
            
            var row = $(this).closest("tr").index()-2;
                
            var pracsotStr = $("#table-nav option:selected").data("tableid") == 0 ? "" : "<span><br><a id='behaviours' data-level='"+level+"' data-row='"+row+"' href='#'>Observed behaviours</a></span>";
            pracsotStr += "<span id='pracsot-crit'><br><br>PRACSOT Performance Criteria:<br>";
            var comma = "";
            $(pracsotArr).each(function(i, v) {
                if(i > 0) comma = ", ";
                pracsotStr += comma+"<a class='getPracsot' href='"+pracsotChck+"'>"+v+"</a>";
            });
            
            pracsotStr += "</span>";
            $(this).append(pracsotStr);//.css({border: 'thin solid #ccc'}).animate({borderWidth: "5px", margin: "-=2px", padding : "+=3px"}, 100);
            
        }
        }
    return false;
    });*/
    
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
    
   /* $me.on('change', "select#table-nav", function(e) 
    {
        $(e.target).setSelectedTab();
    });*/
    
    $me.on('change', 'select#table-nav', function() {
        
        $(this).setSelectedTab();
         var selectedTableId = $('option:selected', this).data("tableid");
        $("table:eq("+selectedTableId+") td p[data-pracsot]").each(function() { $(this).additionalCellInfo(selectedTableId) });
        $("table:eq("+selectedTableId+") a").first().focus();  
    });
    
    /* add evidencing buttons */
    //$me.prepend("<a class='button show-files'>Show existing evidence for the selected competency statements</a>");
    
    $me.after("<div class='contrast'><p id='feedback'>Please provide meaningful written feedback for the student.  The word limit has been removed.<br><textarea style='min-width: 700px; height: 180px;'></textarea></p><br><button id='submitFeedback'>Submit and Complete Assessment</button></div>");
       
    /*$("#feedback textarea").on('keyup', function() {
        var chars = $(this).val().length;
        $('#charmsg').html((100-chars)+" characters to go.");
        if(chars > 100) {
            $(this).parent().nextAll("#submitFeedback").removeAttr('disabled');
        } else {
            $(this).parent().nextAll("#submitFeedback").attr('disabled', 'disabled');
        }
    });*/
    
    $('button#submitFeedback').on('click', function() {
        $(this).after("<img href='/img/ajax_loader.gif'/>");
        var criteria = getAssessedCriteria();
        
        var data = {evidence_id: entry_id, criteria: JSON.stringify(criteria), feedback: $("#feedback > textarea").val() };
        
        $.post('/ajax/save-educator-feedback', data, function(data){
            window.history.back();
        }, 'json');
    });
            
    var getAssessedCriteria = function() {
        var map = [];
        $("input[type='checkbox'].preclicked, p.assessed input[type='checkbox']:checked").each(function() {
                var crit = $(this).data('criteria');
                var p = $(this).closest('p').data('pracsot');
                crit.pracsot = p;
                crit.agreed = (typeof crit.agreed === 'undefined') ? 1 : crit.agreed;  // agreed unless specifically unchecked by educator
                
                if(-1 == map.indexOf(crit))
                {   
                        map.push(crit); 
                }   
        });
    return map;
    }
    
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

    //$me.find("a.button").css('visibility','hidden');
    
    /*$me.on('click', "td input", function(e) {
        if($('td input:checked').length > 0) 
            $me.find("a.button").removeAttr('disabled');
        else 
            $me.find("a.button").removeAttr('disabled');
    });*/
    
    $me.on('click', 'div.file-viewer a.exit', function(e) {
            e.preventDefault();
            $(this).closest('div.file-viewer').hide(100);
    }); 
    
    $me.ajaxStart(function(){
        $("div.ajax-loader").show();
     }).ajaxStop(function(){
        $("div.ajax-loader").hide();
     });
    
    $me.on("change", "input[type='radio']", function() {
        var checkboxInput = $(this).parent().prevAll('input');
        var criteria = checkboxInput.data('criteria');
        checkboxInput.unbind('change');
        
        if(parseInt($(this).val()) == 1) {
            checkboxInput.attr('checked', 'checked');
            criteria.agreed = 1;
        } else {
            checkboxInput.removeAttr('checked');
            criteria.agreed = 0;    
        }
        
        checkboxInput.data('criteria', criteria).attr('disabled','disabled');
        $(this).parents('label').closest('p').addClass('assessed').css({borderColor: 'red'});  
    });
    
    /* verify non self-assessed competency statement */
    $me.on("change", "input[type='checkbox']:not(.preclicked)", function() {
        var criteria =  $(this).data('criteria');
        if($(this).closest('label').find("input[type='radio']").length == 0) {
            var bgcolor = window.yellow;
            var radioButtons = "<p style=\"color: #FFB510; font-size: 12px\" id='radios'>Agree: <input type='radio' disabled='disabled' value='1' name='radio"+radioCount+"' checked='checked'> Disagree: <input type='radio' value='0' disabled='disabled' name='radio"+radioCount+"'></p>";
            radioCount++;
            $(this).closest('label').parent().addClass('assessed');
            $(this).closest('label').append(radioButtons).parent().css('border', 'thin solid red');
            criteria.agreed = 1;
        } else {
            $(this).closest('label').parent().css('border', 'none').removeClass('assessed');
            $(this).closest('label').find("input[type='radio']").parent().remove();
            criteria.agreed = 0;
        }
        $(this).data('criteria', criteria);
    });
    
    // don't allow direct unticking of already assessed items, use disagree or agree instead.
    $("input[type='checkbox'].preclicked", $me).attr('disabled', 'disabled');
        
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
                $pracsot_viewer.find('.file-viewer-inner').append("<tr><td><a href='/pracsot/educator-pracsot-unit-table?element="+elprefix+"&suid="+suid+"#"+v.id+"' target='_blank'>"+v.id+"</a></td><td>"+v.question+"</td></tr>");
            });
            $pracsot_viewer.find('.file-viewer-inner').append("</tbody></table>");
            $pracsot_viewer.show();
        });
    });
    
    $me.find('#table-nav').after(legend);
};

})( jQuery );

$('.evidencing-app').evidencing();
$('.evidencing-app select#table-nav').trigger('change');
</script>
