
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