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
	$.fn.otcaDiary = function() {
		var cleanTextArea = "<p class='otca-textbox'>"+currentTime+"<br><textarea data-tag='general-diary-entry' data-id='0' class='diary-entry'></textarea><img class=\"add\" src=\"/img/plus-icon.png\"><img class=\"exit\" src=\"/img/close-icon-white.png\"><br><button id='save'>Save</button></p>";
		
		$("button#save").attr('disabled','disabled');
		
		var saveChanges = function(obj) {
			var id = $(obj).data('id');
				var tag = $(obj).data('tag');
				$(obj).prev('br').before("<img id='loader' src='/img/ajax-loader-circle.gif'></img>");
				$.post('/ajax/diary-add?id='+id+'&tag='+tag+'&history_id='+window.userProfile.history_id, 
				{ "entry" : $(obj).val() }, 
				function() {
					$('#savedMsg').remove();
					$('img#loader').after("<strong id='savedMsg'> Entry saved...</strong>").remove();
				});
			$("button#save").attr('disabled','disabled');
		};
		
		// tricky save button, all it does is force user to move focus from textarea to trigger
		// this... saves us saving stuff that hasn't changed.
		$(document).on('change','.otca-textbox textarea', function() {
			saveChanges(this);	
		});
		
		$(document).on("click", "button#save", function() {
			var textarea = $(this).prevAll('textarea').first();
			saveChanges(textarea);
		});
		
		$(document).on('keyup','.otca-textbox textarea', function() {
			$(this).nextAll("#save").removeAttr('disabled');	
		});
		
		$(document).on('click', 'img.add', function(e) {
				e.preventDefault();	
				$(e.target).parent().before(cleanTextArea).after('<br>');
				$(e.target).parent().prev().autosize();
		});
		
		$(document).on('click', 'img.exit', function(e) {
					e.preventDefault();
					var confirmed = $(e.target).data('confirmed');
					var id = $(e.target).parents('.otca-textbox').find('textarea').data('id');
					
					if(typeof confirmed != 'undefined' && confirmed == 1) {		
							$(e.target).parents('.otca-textbox').find('br').before("<img id='loader' src='/img/ajax-loader-circle.gif'></img>");
							if(typeof id !== 'undefined') {
								$.get('/ajax/remove-diary-entry?id='+id, function() {
									var box = $(e.target).parents('.otca-textbox');
									$(box).remove();
								});
							}
					} else {
						$('div#confirm-message').remove();
						$('img.exit').each(function(i, v) {
							if(v != e.target) {
								$(v).removeAttr('data-confirmed').removeData('confirmed');
							}
						});
						if(typeof id !== 'undefined') {
							$(e.target).after('<div id="confirm-message">Are you sure? Click again to delete forever.</div>');
							$('div#confirm-message').css({top: e.pageY-10, left: e.pageX+20});
							$(e.target).data('confirmed', 1);
						}
					}
		});
		
		return this;
	};
	
	$("a.button#printable").click(function(e) {
		e.preventDefault();
		if($("input[type='checkbox']").is(':checked').length == 0) {
			alert("Please check some entries to print first.");
		}
		
		var txtBoxContents = "";
		
		$(".otca-textbox").each(function() {
			if($(this).find("input[type='checkbox']").is(':checked')) {
				var textVal = $(this).find('textarea').val();
				//var txtRep = "<p>"+textVal+"</p>";
				
				var clonedTbox = $(this).clone();
				$(clonedTbox).find('textarea').replaceWith(textVal);
				$(clonedTbox).find("input[type='checkbox'], img, label, button").remove();
				txtBoxContents += "<p class='printable-entry'>"+$(clonedTbox).html()+"<p>";
			}
		});
		
		var data = {
			diaryHTML : txtBoxContents
		};
		
		$.post("/printview/diary", data, function(data) {
			var w = window.open();
			$(w.document.body).html(data);
		});
	});
})( jQuery );
