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
	var loader = '<img src=/img/ajax-loader-circle.gif class=loader></img>';
	var split = location.search.replace('?', '').split('=');
	var highlightID = split[1];
	
	//alert("Testing this page "+split[0]+" "+split[1]);
	
	$.checkHistoryID(function() { 
		window.location.href = '/practice-placement/prepare-for-practice';
	}, 
		true /* no reload on history_id > 0 */
	);
	
	var loadContainer = function() { 
		var diary = $("input#diary:checked").length > 0 ? 1 : 0;
		var evidence = $("input#evidence:checked").length > 0 ? 1 : 0;
		var contracts = $("input#contracts:checked").length > 0 ? 1 : 0;
		
		$('#timeline-container').
		html(loader).
		load('/ajax/timeline-container?evidence='+evidence+"&diary="+diary+"&contracts="+contracts, 
		function() {
			$('.timeline-label').click( 
				function(e){		
						var end = $(this).parent().data('end');
						var start = $(this).parent().data('start');
			
						var label = $(this).clone();
						var folks = $(this).parent();
						
						$(folks).html('').
							addClass('open-panel').
							html("").
							html(loader).
							load('/ajax/timeline-list?start='+start+'&end='+end+'&diary='+diary+'&evidence='+evidence+'&contracts='+contracts, 
							function() {
								$(this).prepend(label);
								var highlightedItem = $(folks).find("a[href*='assessed-matrix/"+highlightID+"']").closest("li");
								highlightedItem.css("background-color", "#FFFF9C");
								$('html, body').animate({
									scrollTop: highlightedItem.offset().top
								 }, 500);

							}
						);
				}
			).trigger('click');
		}
	);
}

	loadContainer();

$("input#diary, input#evidence, input#contracts").change(
	function() {
		loadContainer();
	}
);

$(document).on('change', "input#hide", function() {
	var entry_id = $(this).data('id');
	var value = $(this).is(":checked") ? 1 : 0;
	$(this).before(loader+" ");
	$.post('/ajax/hide-diary-entry/', { entry_id: entry_id, hidden: value }, function(data) {
		$("img.loader").remove();
	});
});

$(document).on('change', "select#highlight", function() {
	$(this).after("  "+loader);
	var code = $(this).data("code");
	var serverColorVal = $(":selected", this).val();
	var colorVal = "thick solid " + serverColorVal;
	$me = $(this);
	if(typeof colorVal === 'undefined' || serverColorVal == 'none') {
		colorVal = "thin solid #663399";
	}
		$.post('/ajax/save-folio-highlight', { code : code, color : serverColorVal } , function()
		{
				$me.closest("li").css({ border : colorVal });
				
				if(serverColorVal != 'none') {
					$me.closest("li").find("input#hide:checked").trigger('click');	
				} else {
					$me.closest("li").find("input#hide:not(:checked)").trigger('click');	
				}
				$('.loader').remove();
		}
	);
	
});

$(document).on('click', 'img.exit', function(e) {
e.preventDefault();
var confirmed = $(e.target).data('confirmed');
var str_id = $(e.target).prev('select#highlight').data('code');

var id_arr = str_id.split("_");
var type = id_arr[0];
var member = id_arr[1];
var id = id_arr[2];
var eTarget = $(e.target);
var parentDiv = eTarget.parents('.open-panel').first();

var removeEmptyPanel = function() {
	// remove parent;
	var liLen = $(parentDiv).find("li").length;
	if(liLen == 0) {
		if(parentDiv.hasClass('timeline-top')) {
			parentDiv.next().removeClass('timeline-center').addClass('timeline-top');
		}
		if(parentDiv.hasClass('timeline-bottom')) {
			parentDiv.prev().removeClass('timeline-center').addClass('timeline-bottom');
		}
		$(parentDiv).remove();		
	}	
};
 
if(typeof confirmed != 'undefined' && confirmed == 1) {
$(e.target).parents('.otca-textbox').find('br').before("<img id='loader' src='/img/ajax-loader-circle.gif'></img>");
	
	if(typeof id !== 'undefined') {
		
	if(typeof type !== 'undefined') {
		switch(type) {
		case 'diary':
			$.get('/ajax/remove-diary-entry?id='+id, function() {
				var box = $(e.target).parents('li');
				$(box).remove();
				removeEmptyPanel();
			});
		break;
		case 'evidence':
			$.get('/ajax/remove-evidence-entry?id='+id, function() {
				var box = $(e.target).parents('li');
				$(box).remove();
				removeEmptyPanel();
			});
		break;
		}
	}
	
	}
} else {
$('div#confirm-message').remove();
$('img.exit').each(function(i, v) {
if(v != e.target) {
$(v).removeAttr('data-confirmed').removeData('confirmed');
}
});
if(typeof id !== 'undefined') {
$(e.target).after('<div id="confirm-message">\
			Are you sure? Click again to delete the item forever.<br> </div>');
$('div#confirm-message').css({top: e.pageY-10, left: e.pageX+20});
$(e.target).data('confirmed', 1);
}
}
}); 

});
