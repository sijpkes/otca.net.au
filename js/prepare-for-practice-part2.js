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
$(document).ready(function(){
	
	$.checkHistoryID(function() { 
		window.location.href = '/practice-placement/prepare-for-practice';
	}, 
		true /* no reload on history_id > 0 */
	);
	
	if(typeof userProfile !== 'undefined') {
	var steps = userProfile.steps;
	var level = userProfile.level;
		
	$('select#steps option').each(function(i,o) {
		if(undefined != steps) {
			for(var j=0;j<steps.length;j++) {
			 	if($(o).val()[0] == steps[j]) {
					$(o).attr('selected','selected');
			 	}
			}
		}
	});
	
	$('select#level option[value="'+level+'"]').attr('selected','selected');
	
	$('input[type="checkbox"]').change(function(e) {
		if($(e.target).is(':checked')) {
			$('select#level').attr('disabled', 'disabled');
			$('select#steps').attr('disabled', 'disabled');
			$('.modopacity').css('opacity', '0.2');
		} else {
			$('.modopacity').css('opacity', '1');
			$('select#level').removeAttr('disabled');
			$('select#steps').removeAttr('disabled');
		}
	});
	
	$("#otpp-thumb a").click(function(e) {
		e.preventDefault();
		$('div#otpp').show();
	});
	
	$("#otpp #exit-button").click(function(e) {
		$(e.target).parent().hide();
	});
	
	$('select#steps').change(function() {
		$(this).css('border', 'none');
		$(this).closest('.userMessage').remove();
	});
	
	$('a.button#save').click(function(e) {
		e.preventDefault();
		$('img#loader').remove();
		$(this).after("  <img src='/img/ajax-loader-circle.gif' id='loader'/>");
		saveProfile(function() {
			$('img#loader').replaceWith("<span style='color: #FFB510'> Saved...</span>");
		});
	});
	
	$('a.button#continue').click(function(e) {
		e.preventDefault();
		$(".userMessage").remove();
		
		var ctarget = e.target;
		$.checkHistoryID(function() {
		var saveLevel = $('select#level option:selected').val();		
		var saveSteps = [];
		var myhref = $(ctarget).attr('href');
		$('select#steps option:selected').each(function(i,o) {
			saveSteps[i] = $(o).text()[0];
		});
		
		if(saveSteps.length == 0) {
			$('select#steps').after("<p style='color: yellow' class='userMessage'><strong>Please select at least one step before continuing</strong></p>");
			return false;
		} 
		// add holistic "step 8" Being a Professional
		saveSteps.push("8");
		
		window.userProfile.steps = saveSteps;
		window.userProfile.level = saveLevel;
		//localStorage.userProfile = JSON.stringify(userProfile);
		var jsonProfile = JSON.stringify(window.userProfile);
			
			//update database
			$.post('/ajax/user-status', 
				{ "userProfile" : jsonProfile }, 
				function(data) {
					window.location.href = myhref;
				} 
				,'json');
		return false;
	});
	});
	$("select#steps").asmSelect();
	}
});
