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

	// checks if this is the user's first cycle.
	$.checkHistoryID(function() { 
		window.location.href = '/practice-placement/prepare-for-practice';
	}, 
		true /* no reload on history_id > 0 */
	);
		
	var array_reflections = userProfile.reflections;
	
	var saveProfile = function(callback) {
		$('textarea').each(function(i,v) {
			var dateNow = new Date();
			var unixtime = parseInt(dateNow.getTime() / 1000);
			array_reflections.push({text: $(this).val(), date: unixtime, internalId : $(this).data('iuid'), tag : $(this).data('tag') });
		});
		console.log(window.userProfile.history_id);
		window.userProfile.reflections = array_reflections;
		var jsonProfile = JSON.stringify(window.userProfile);
		//update database
		$.post('/ajax/user-status', 
			{ "userProfile" : jsonProfile }, 
			function() {
				callback();
			} 
		,'json');
	}
	
	
	$('textarea').each(function(i,v) {
		if(typeof array_reflections != 'undefined') {
			for(var j = 0; j < array_reflections.length; j++) {
				if(typeof array_reflections[j] != 'undefined') {
					if($(v).data('tag') == array_reflections[j].tag) {
						$(v).val(array_reflections[j].text);
						$(v).data('iuid', array_reflections[j].internalId); 
					}
				}
			}
		}
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
		var myhref = $(this).attr('href');
		$.checkHistoryID(function(title) {
		
		if(typeof title != 'undefined') {
			window.userProfile.title = title;
		}
		$('textarea').each(function(i,v) {
			var dateNow = new Date();
			var unixtime = parseInt(dateNow.getTime() / 1000);
			array_reflections[i] = {text: $(this).val(), date: unixtime, internalId : $(this).data('iuid'), tag : $(this).data('tag') };
		});
		console.log(window.userProfile.history_id);
		window.userProfile.reflections = array_reflections;
		
		//localStorage.userProfile = JSON.stringify(window.userProfile);
		var jsonProfile = JSON.stringify(window.userProfile);
		//update database
		$.post('/ajax/user-status', 
			{ "userProfile" : jsonProfile }, 
			function() {
				window.location.href = myhref;
			} 
			,'json');
		});
	});
		
	
});
