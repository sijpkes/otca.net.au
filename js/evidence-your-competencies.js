$(document).ready(function(){
	
	$.checkHistoryID(function() { 
		window.location.href = '/practice-placement/prepare-for-practice';
	}, 
		true /* no redirect on logged in user */
	);
	
	$("ul#objectives").html("");
	var objectives = window.userProfile.objectives;
	$.each(objectives, function(i, v) {
		$("ul#objectives").append("<li>"+v.text+"</li>");
	});	
	
	$("#evidencing-app").evidencing();
});