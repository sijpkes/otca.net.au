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
	
	/*$('textarea').each(function(i,v) {
		if(typeof array_reflections != 'undefined') {
			for(var j = 0; j < array_reflections.length; j++) {
				if(typeof array_reflections[j] != 'undefined') {
					if($(v).data('tag') == array_reflections[j].tag) {
						$(v).val(array_reflections[j].text);
						$(v).data('iuid', array_reflections[j].internalId); 
					}
				}
			}
		}
	});*/
	
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
	
	
	/*var merge_arrays = function(arr1, arr2) {
	
		var arr3 = [];
			for(var i in arr1){
				var shared = false;
				for (var j in arr2)
					if (arr2[j].internalId == arr1[i].internalId) {
					 shared = true;
					 break;
			}
		if(!shared) arr3.push(arr1[i])
		}
	return arr3.concat(arr2);			
	};*/
	
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
		
		//userProfile.reflections = merge_arrays(userProfile.reflections, new_reflections);
		//localStorage.userProfile = JSON.stringify(userProfile);
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
	}
});
