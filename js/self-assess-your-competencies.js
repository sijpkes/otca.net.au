$(document).ready(function(){
	
	$.checkHistoryID(function() { 
		window.location.href = '/practice-placement/prepare-for-practice';
	}, 
		true /* no reload on history_id > 0 */
	);
	
	if(typeof userProfile !== 'undefined') {
	$("ul#objectives").html("");
	
	$.each(window.userProfile.objectives, function(i, v) {
		$("ul#objectives").append("<li>"+v+"</li>");
	});
	
	$('textarea').each(function(i,v) {
	    var me = this;

		if(typeof userProfile.reflections !== 'undefined') {
		    var obj = userProfile.reflections[$(this).data('tag')];
		    if(typeof obj !== 'undefined') {
			     $(me).val(obj.text);
			     $(me).data('iuid', obj.internalId);
		    }
		}	
	});
	
	$('a.button#continue').click(function(e) {
		e.preventDefault();
		var ctarget = e.target;
		$.checkHistoryID(function() {
		
	    var myhref = $(ctarget).attr('href');
		//var new_reflections = [];
		
		$('textarea').each(function(i,v) {
			var dateNow = new Date();
			var unixtime = parseInt(dateNow.getTime() / 1000);
			var my_tag = $(this).data('tag');
			userProfile.reflections[my_tag] = {text: $(this).val(), date: unixtime, internalId : $(this).data('iuid'), tag : my_tag };
		});
		
		var jsonProfile = JSON.stringify(window.userProfile);
		
			//update database
			$.post('/ajax/user-status', 
				{ "userProfile" : jsonProfile }, 
				function(data) {
					window.location.href = myhref;
				} 
				,'json');
		});
	});
	
	$("a.button#printable").click(function(e) {
        e.preventDefault();
        
        var txtBoxContents = "";
        
        var data = { 
            statement1: $("#revisedStrengthsStatement").val(),
            statement2: $("#learnedStatement").val(),
            statement3: $("#improvementStatement").val(),
            statement4: $("#reflectStatement").val()
        };
        
        $.post("/printview/reflection_planning", data, function(data) {
            var w = window.open();
            $(w.document.body).html(data);
        });
    });
	
	}
});
