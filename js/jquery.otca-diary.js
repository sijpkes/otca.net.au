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