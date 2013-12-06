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
 if(typeof(Storage)=="undefined")
  {
 alert("Your browser is not HTML 5 compliant.  You'll find this ePortfolio won't work for you. Try upgrading to a HTML 5 compliant browser like Firefox or Chrome");
  }
else {	
	var steps;
	if(localStorage.steps) 
		steps = JSON.parse(localStorage.steps);
	else
		steps = [];
		
	$('.story-pane select#steps option').each(function(i,o) {
		for(var j=0;j<steps.length;j++) {
		 	if($(o).val()[0] == steps[j]) {
				$(o).attr('selected','selected');
		 	}
		}
	});
	
	$('select#level option[value="'+localStorage.level+'"]').attr('selected','selected');
	
	$('.story-pane input[type="checkbox"]').change(function(e) {
		if($(e.target).is(':checked')) {
			$('.story-pane select#level').attr('disabled', 'disabled');
			$('.story-pane select#steps').attr('disabled', 'disabled');
			$('.story-pane .modopacity').css('opacity', '0.2');
		} else {
			$('.story-pane .modopacity').css('opacity', '1');
			$('.story-pane select#level').removeAttr('disabled');
			$('.story-pane select#steps').removeAttr('disabled');
		}
	});
	
	$("#otpp-thumb a").click(function(e) {
		e.preventDefault();
		$('div#otpp').show();
	});
	
	$("#otpp #exit-button").click(function(e) {
		$(e.target).parent().hide();
	});
	
	$('a.button#continue').click(function(e) {
				
		localStorage.level = $('.story-pane select#level option:selected').text();		
		var steps = [];
		
		$('.story-pane select#steps option:selected').each(function(i,o) {
			steps[i] = $(o).text()[0];
		});
		
		localStorage.steps = JSON.stringify(steps);
	});
 }
});
