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
	$('#supervisor_emails').hide().before("<input id='asminput' name='asminput'></input><button id='addsup'>Add supervisor</button><br><ul id='emailList'></ul>");
	var supArray = [];
	$(document).on('click','#addsup', function(e) {
		e.preventDefault();
		var email = $('#asminput').val();
		supArray.push(email);
		var i = supArray.indexOf(email);
		$('#emailList').append('<li data-index="'+i+'">'+email+'   <a href="#">Remove</a></li>');
		$('#supervisor_emails').val(supArray.join());
	});
	
	$(document).on('click', 'ul#emailList a', function(e) { 
		e.preventDefault();
		var index = $(this).data('index');
		supArray.splice(index, 1);
		$(this).parents('li').remove();
		$('#supervisor_emails').val(supArray.join());
	});
});
