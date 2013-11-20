$(document).ready(function() {

    <?= $this->check_history_id() ?>

    if ( typeof userProfile !== 'undefined') {
        // repository for the criteria list, replaced every time a user selects step(s) in the dropdown
        window.optionsCopy = "";
        window.cleanObjectiveEl = "";
        window.pracsotMap = {};

        $.each(window.userProfile.objectives, function(i, v) {
            if (i > 0) {
                var copy = $("p#objectives").last().clone();
                $("p#objectives").last().after(copy);
            }
            $("p#objectives textarea").last().val(v.text);
        });

        var isVowel = function(c) {
            var vowels = ['a', 'e', 'i', 'o', 'u'];
            if (c) {
                c = c.toLowerCase();
            } else {
                return false;
            }
            return $.inArray(c, vowels) > -1;
        };

        $("div.ajax-loader").ajaxStart(function() {
            $(this).show();
        }).ajaxStop(function() {
            $(this).hide();
        });
        $("body").append("<div class='ajax-loader' style='display: none;'> </div>");

        var stepStr = "";
        var separator = ", ";

        var steps = userProfile.steps;

        if ( typeof steps !== 'undefined' && steps.length > 0) {
            for (var i = 0; i < steps.length; i++) {
                if (i == steps.length - 2)
                    separator = ' & ';
                if (i == steps.length - 1)
                    separator = '';
                stepStr += window.stepDefinitions[steps[i]].trim() + separator;
            }
        } else {
            alert("Ok, speedy, you got here a bit early. Please complete choosing your steps for Prepare for Practice part 2.  I'll take you there when you click OK.");
            window.location.href = "/practice-placement/prepare-for-practice-part2";
        }

        var levelName = "no level selected";
        var leveln = userProfile.level;

        if (userProfile.level == 1) {
            levelName = 'Emerging';
        } else if (userProfile.level == 2) {
            levelName = 'Consolidating';
        } else if (userProfile.level == 3) {
            levelName = 'Competent to Graduate';
        }
        var indefinite = isVowel(levelName) ? "an" : "a";
        var startStr = "You have chosen " + indefinite + " <span class='" + levelName.toLowerCase() + "'>" + levelName + "</span> level";

        if (stepStr != "") {
            $('.storedSteps').html(startStr + " and are focusing on the steps: <strong>" + stepStr + "</strong> for this practice cycle.");
            $(".steps").html(stepStr + " at a <span class='" + levelName.toLowerCase() + "'>" + levelName + "</span> level.");
        }

        $("#otpp-thumb a").click(function(e) {
            e.preventDefault();
            $('div#otpp').show();
        });

        $("#otpp #exit-button").click(function(e) {
            $(e.target).parent().hide();
        });

        $(document).on('click', 'p#objectives img.add', function(e) {
            e.preventDefault();

            var content = window.cleanObjectiveEl.clone();

            $(e.target).parent().after(content).after('<br>');

            $(content).find('select#steps').asmSelect();
            $(content).find('select#objective-crit').asmSelect();
        });

        $(document).on('change', 'select#objective-crit', function(e) {
            $(this).css('border', 'none');
            $(this).closest('.userMessage').remove();
        });

        $('a#continue').click(function(e) {
            e.preventDefault();

            $(".userMessage").remove();

            $.checkHistoryID(function() {

                var objs = window.userProfile.objectives;
                var error = false;
                var myhref = $(e.target).attr('href');

                $('.userMessage').remove();
                $('p#objectives').each(function(i, o) {
                    var arrStrID = [];
                    var arrStrText = [];
                    var arrStrPracsot = [];
                    var stepArr = [];
                    
                    /* data is applied to hidden <select>, so don't focus this search on the visible .asmSelect <select>*/
                    $(o).find("select#objective-crit option:selected").each(function(j, ov) {
                        var id = $(ov).attr('value');
                        var ovPracsot = window.pracsotMap[id];

                        // account for blank values in comma separated string
                        if ( typeof ovPracsot === 'undefined' || ovPracsot.length === 0) {
                            console.log('PRACSOT value empty in document index: ' + j);
                        } else {
                            arrStrID.push($(ov).val());
                            arrStrText.push($(ov).text());
                            arrStrPracsot.push(ovPracsot);
                        }
                    });

                    if (arrStrID.length == 0) {
                        $(this).find('select#objective-crit.asmSelect').css('border', 'thin solid yellow').after("<p style='color: yellow' class='userMessage'><strong>Please select atleast one Matrix competency statement for this objective</strong></p>");
                        error = true;
                    }

                    $(o).find("select#steps option:selected").each(function(k, sv) {
                        stepArr.push($(sv).val());
                    });

                    if (stepArr.length == 0) {
                        $(this).find('select#steps').after("<p style='color: yellow' class='userMessage'><strong>Please select atleast one step for this objective</strong></p>");
                        error = true;
                    }

                    if ( typeof objs[i] === 'undefined') {
                        objs[i] = {
                            "text" : $(o).find("textarea").val(),
                            "competencyStatementID" : arrStrID,
                            "steps" : stepArr,
                            "competencyStatementText" : arrStrText,
                            "pracsot" : arrStrPracsot
                        };
                    } else {
                        objs[i].text = $(o).find("textarea").val();
                        objs[i].competencyStatementID = arrStrID;
                        objs[i].steps = stepArr;
                        objs[i].competencyStatementText = arrStrText;
                        objs[i].pracsot = arrStrPracsot;
                    }

                });

                if (error) {
                    return false;
                }

                window.userProfile.objectives = objs;

                var jsonProfile = JSON.stringify(window.userProfile);
                //update database
                $.post('/ajax/user-status', {
                    "userProfile" : jsonProfile
                }, function(data) {
                    window.location.href = myhref;
                }, 'json');

                return false;
            });
        });

        $('div#criteria-data').load('/pages/ajax-criteria-tables', function() {
            $(this).addIDs();

            var criteria = this;
            $('ul#criteria-list').html('');
            $('#objectives img.add').after("<br><select id=\"steps\" multiple=\"1\" title=\"Step(s) for this learning objective\"></select><br>The competency statements below have been drawn in from the OTCEM based the level and step(s) you selected.<br><select id=\"objective-crit\" multiple=\"multiple\" title=\"OTCEM competency statements for this learning objective\"></select>");
            $.each(steps, function(i, v) {
                $('#objectives select#steps').each(function(obi, obv) {
                    var selected = '';
                    if (undefined !== userProfile.objectives[obi]) {
                        selected = $.inArray(v, userProfile.objectives[obi].steps) != -1 ? 'selected=selected' : '';
                    }
                    $(obv).append("<option value='" + v.trim() + "' " + selected + ">" + v + " &ndash; " + window.stepDefinitions[v] + "</option>");
                });

                leveln = v == 8 ? 0 : leveln;
                var selector = 'p.otca-step' + v + ".otca-level" + leveln;

                $(criteria).find(selector).each(function(criti, crito) {

                    var id = $(crito).attr('id');
                    var text = $(crito).text();
                    var pracsot = $(crito).data('pracsot');
                    if (criti == 0) {
                        var templevel = "";
                        if (leveln > 0) {
                            templevel = " &ndash; Level: " + levelName;
                        }
                        window.optionsCopy += '<option value="header" disabled="disabled">' + window.stepDefinitions[v] + templevel + '</option>';
                    }

                    if ( typeof id !== 'undefined' && typeof text !== 'undefined') {
                        window.optionsCopy += '<option value="' + id + '">' + text + '</option>';
                        
                        /* grab from hidden select, this is where the data attributes are attached */
                        $('#objectives select#objective-crit').each(function(obci, obcv) {
                            var selected = '';
                            if ( typeof userProfile.objectives[obci] !== 'undefined') {
                                selected = $.inArray(id, userProfile.objectives[obci].competencyStatementID) != -1 ? 'selected=selected' : '';
                            }
                            if (criti == 0) {
                                $(obcv).append('<option value="header" disabled="disabled">' + window.stepDefinitions[v] + templevel + '</option>');
                            }
                            window.pracsotMap[id] = pracsot;
                            $(obcv).append('<option value="' + id + '" ' + selected + '">' + text + '</option>');
                        });
                    }
                });
            });
            // create a clean copy of our select boxes so we can re-apply asmSelect on dynamic addition
            $('#objectives .loader').remove();
            window.cleanObjectiveEl = $('#objectives').clone();

            $('#objectives select#steps').asmSelect();
            $('#objectives select#objective-crit').asmSelect();
        });

        $(document).on('change', '#objectives select#steps', function(e) {
            //	console.log('changed');
            $(this).css('border', 'none');
            $(this).closest('.userMessage').remove();

            $(this).closest('#objectives').find("select#objective-crit option:not(:selected)").remove();
            $(this).closest('#objectives').find("select#objective-crit").append(window.optionsCopy);

            var valArray = [];

            $(this).find("option:selected").each(function(i, v) {
                if (-1 == $.inArray($(v).val(), valArray)) {
                    valArray.push($(v).val());
                }
            });

            /*if(valArray.length == 0) return;

             $(this).closest('#objectives').find("#objective-crit option").each(function(i, v) {

             if('empty' != $(v).val()) {
             var code = $(v).val().split('-')[1];
             var stepId = code.split('_')[0];

             if(-1 == $.inArray(stepId, valArray)) {
             $(v).remove();
             //$(v).prevAll("[value='header']").first().hide();
             } else {
             $(v).show();
             //$(v).prevAll("[value='header']").first().show();
             }
             }
             });*/

            $(this).closest('#objectives').find("select#objective-crit").change().click();
        });

        $(document).on('click', "p#objectives img.exit", function(e) {
            if ($("p#objectives").length > 1) {
                var obj = $(e.target).closest("p#objectives");
                var index = $.inArray($(obj).val(), window.userProfile.objectives);
                window.userProfile.objectives.splice(index, 1);
                //userProfile.objectives = objectives;
                //localStorage.userProfile = JSON.stringify(userProfile);

                obj.next('br').remove();
                obj.remove();
            } else {
                $("p#objectives textarea").val("");
                $("p#objectives select option:selected").removeAttr("selected");
            };
        });
    }
});
