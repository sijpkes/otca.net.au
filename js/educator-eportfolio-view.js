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
	var loadContainer = function() { 
		var diary = $("input#diary:checked").length > 0 ? 1 : 0;
		var evidence = $("input#evidence:checked").length > 0 ? 1 : 0;
		var contracts = $("input#contracts:checked").length > 0 ? 1 : 0;
		
		$('#timeline-container').
		html(loader).
		load('/ajax/educator-timeline-container?evidence='+evidence+"&diary="+diary+'&suid='+window.evStudentId+'&contracts='+contracts, 
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
							load('/ajax/educator-timeline-list?start='+start+'&end='+end+'&diary='+diary+'&evidence='+evidence+'&suid='+window.evStudentId+'&contracts='+contracts, 
							function() {
								$(this).prepend(label);
							}
						);
				}
			).first().click();
			
		}
	);	
      }
      
      var loadHighlights = function() {
		$('#highlights-container').
		html(loader).addClass('open-panel').html("").
			html(loader).
			   load('/ajax/educator-timeline-list?ho=1&diary=1&evidence=1&contracts=1&start=0&end=0&'+'&suid='+window.evStudentId);
      };
	
	loadHighlights();
	loadContainer();

$("input#diary, input#evidence, input#contracts").change(
	function() {
		loadContainer();
	}
);

$("select[name='checkAction']").change(function(){
	var text = $(this).text().toLowerCase();
	
	if(text=='share') {
		$("input#action:checked").each(function(i,v) {
			
		});
	}
});
});
