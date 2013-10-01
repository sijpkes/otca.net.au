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