$(document).ready(function() {
	
	$('body').append("<div class='global_file-viewer' style='display:none'><span id='content'></span></div>");
	$('div.global_file-viewer > span#content').append("<h1 style=\"color: #663399; text-align:center\">Please <a href=\"/member/login\">login</a> or <a href=\"/member/register\">register</a>.</h1>\
							  <p>To access the diary, OTCEM and ePortfolio you will need to <a href=\"/member/login\">login</a> or <a href=\"/member/register\">register</a>.\
							  </p><p>You can browse and view the videos and documents under the Practice Resources, Practice Contexts, Occupational Therapy Practice Process\
							  and some of the links under Evidencing without logging in.</p>").
				parent().css({height: "156px"}).show(100);
});