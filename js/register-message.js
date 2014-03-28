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
	
	$('body').append("<div class='global_file-viewer' style='display:none'><span id='content'></span></div>");
	$('div.global_file-viewer > span#content').append("<h1 style=\"color: #663399; text-align:center\">Please <a href=\"/member/login\">login</a></h1>\
							  <p>To access the diary, OTCEM and ePortfolio you will need to <a href=\"/member/login\">login</a> or contact your intstitution's<a href=\"/partners/web-admin-list\">OTCA administrator</a> for information on how to register.\
							  </p><p>You can browse and view the videos and documents under the Practice Resources, Practice Contexts, Occupational Therapy Practice Process\
							  and some of the links under Evidencing without logging in.</p>").
				parent().css({height: "156px"}).show(100);
});
