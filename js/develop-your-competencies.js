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
	
		var steps = localStorage.steps;
		var leveln = 0;
		var bgClass = '';
		var colorClass = '';
		if(localStorage.level == 1) {
			leveln = 1;
			bgClass = "dyc-%step%-emerging";
			colorClass = 'emerging';
		} else if (localStorage.level == 2){
			leveln = 2;
			bgClass = "dyc-%step%-consolidating";
			colorClass = 'consolidating';
		} else if (localStorage.level == 3){
			leveln = 3;
			bgClass = "dyc-%step%-competent";
			colorClass = 'competent';
		}
		$(".dyc-text").addClass('unfocus');
		
		$.each(steps, function(i, v) {
				var path_selector = 'div#ot-practice-process-model div#dyc-step'+v;
				var str_step = 'step'+v;
				var text_step = '#text-step'+v;
				var newBgClass = bgClass.replace('%step%', str_step);
				$(text_step).removeClass('unfocus').addClass(colorClass);
				$(path_selector).css('visibility','visible').addClass(newBgClass);
		});
});
