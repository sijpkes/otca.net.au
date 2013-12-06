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
	
	$('body').append("<div class='global_file-viewer' id='level-defs-window' style='display:none;background-color: #EEE; width: 800px; height: 500px; overflow: auto; left: 300px; top: 10%'><span id='content'></span></div>");
	
	$('div#step-defs > span').on('click', function(){
		$('#level-defs-window > span#content').load("/pages/step-definitions/",
		function(){
			$(this).append("<p><a href='#' id='close'>Close this window</a></p>").parent().show(100);
			
			$(this).find('a#close').on('click', function(e) {
				e.preventDefault();
				$('#level-defs-window').hide(100);
			});
		});	
	});	
});
