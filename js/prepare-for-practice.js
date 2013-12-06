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
	
	if(typeof userProfile !== 'undefined') {
		
	var array_reflections = userProfile.reflections;
	
	var saveProfile = function(callback) {
		$('textarea').each(function(i,v) {
			var dateNow = new Date();
			var unixtime = parseInt(dateNow.getTime() / 1000);
			array_reflections[i] = {text: $(this).val(), date: unixtime, internalId : $(this).data('iuid'), tag : $(this).data('tag') };
		});
		console.log(window.userProfile.history_id);
		window.userProfile.reflections = array_reflections;
		window.userProfile.letterEdName = $("#edname").val(); // not that ed name is not saved in db
		//localStorage.userProfile = JSON.stringify(window.userProfile);
		var jsonProfile = JSON.stringify(window.userProfile);
		//update database
		$.post('/ajax/user-status', 
			{ "userProfile" : jsonProfile }, 
			function() {
				callback();
			} 
		,'json');
	}
	
	$('#openLocator strong').click(function(e) {
		$(this).unbind('click').css('cursor','inherit');
		e.preventDefault();
		$('#locator').css({'border':'thin solid white', 'padding': '5px'}).animate({height: '+=675px', width: '+=800px'}, 1000).load('/pages/prac-locator', function(e) {
			$(document).on('click', 'button#find', function(e) {
				e.preventDefault();
				var state = $(e.target).find('option:selected').text();
				var searchTerm = $("input#location").val();
				searchTerm = searchTerm.split(' ').join('+');
				console.log('https://maps.google.com.au/maps?q=internet+%22internet+cafe%22+OR+%22public+library%22+OR+%22wireless+internet%22+OR+%22mcdonalds%22+OR+cafe+-hotel+-motel+-accomodation+loc:+'+searchTerm+',+'+state+',+Australia&radius=5&output=embed');
				$('iframe#map').attr('src','https://maps.google.com.au/maps?q=internet+%22internet+cafe%22+OR+%22public+library%22+OR+%22wireless+internet%22+OR+%22mcdonalds%22+OR+cafe+-hotel+-motel+-accomodation+loc:+'+searchTerm+',+'+state+',+Australia&radius=5&output=embed');
			});
		});
	});
	
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
	
	$("a.button#printable").click(function(e) {
		e.preventDefault();
		
		var data = { edname: $("#edname").val(),
			statement1: $("#statement1").val(),
			statement2: $("#statement2").val(),
			statement3: $("#statement3").val(),
			statement4: $("#statement4").val()
		};
		
		
		$.post("/printview/intro_letter", data, function(data) {
			var w = window.open();
			if(typeof w.document === 'undefined' ) {
				w.close();
				alert("Your security settings don't allow me to open a new window. So I'll\
				       put the print view in this window when you click 'OK'.");
				$("body").html(data);
			} else {
				$(w.document.body).html(data);
			}
		});
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
		window.userProfile.letterEdName = $("#edname").val();
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
	}
});
