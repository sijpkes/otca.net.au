$(document).ready(function() {
		
		$.checkHistoryID(function() { 
			window.location.href = '/practice-placement/prepare-for-practice';
		}, 
			true /* no reload on history_id > 0 */
		);
		
                if(typeof userProfile !== 'undefined') {
		var steps = userProfile.steps;
		var leveln = 0;
		var bgClass = '';
		var colorClass = '';
		if(userProfile.level == 1) {
			leveln = 1;
			bgClass = "dyc-%step%-emerging";
			colorClass = 'emerging';
		} else if (userProfile.level == 2){
			leveln = 2;
			bgClass = "dyc-%step%-consolidating";
			colorClass = 'consolidating';
		} else if (userProfile.level == 3){
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
                }
});