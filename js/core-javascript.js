$(document).ready(function(){ 		
	window.emptyUserProfile = {
			"reflections" : [],
			"startNewCycle" : false,
			"steps" : [],
			"level" : 0,
			"beginner" : true,
            "objectives" : [],
			"history_id" : 0,
			"title" : "No cycle loaded",
			"time" : 0
	};
        
        window.stepDefinitions = [ "null", "Request for Service", "Information Gathering", "Occupational Assessment", "Identification of Occupational Issues", "Goal Setting", "Intervention",
                                              "Evaluation", "Being a Professional"  ];          
	
    $('div.cycle-name-box').append(window.userProfile.title + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Date created:</strong> " + cycleCreated);	
					      
    $("#traffic-light-set").criteriaLoader(); 

    var arr = screen_name.split(" ");
	var firstname = arr[0];
	      
	    if((firstname.substr(0, firstname.length-1)).toUpperCase() == 'S') 
			firstname = firstname + "'";
	    else 
			firstname = firstname + "'s";	

	      $("body #firstname").text(firstname);	
	       
		$(".cycle-toolbar").hover(
			function() {
			if($(this).height() <= 15) 
				$(this).animate({ height: "+"+window.toolbarHeightChange }, 500);
			}, 
			function() {
			if($(this).height() >= window.toolbarMaxHeight)	
				$(this).animate({ height: "-"+window.toolbarHeightChange }, 500);
			}
		);
		
		if($(".ajax-loader").length == 0) {
			$('body').append("<div class='ajax-loader' style='display: none;'><p id='message' style='color: #FFB510;'><strong>Please wait...</strong></p></div>");
		}
		if($(".global_file-viewer").length == 0) {
			console.log("added global viewer");
			$('body').append("<div class='global_file-viewer' style='display: none'><a href='#close' class='exit'><img src='/img/close-icon.png' alt='close'></a><span id='content'></span></div>");
		}
		
		$(document).on('click', 'div.global_file-viewer a.exit', function(e) {
				e.preventDefault();
				$('div.global_file-viewer > span#content').html('');
				$('div.global_file-viewer').hide(100);
		});
		
		
		
		// restore practice cycle
		$(document).on('click', 'div.global_file-viewer > span#content ul.cycles a', function(e) {
			e.preventDefault();
			$('.ajax-loader').show(500);
			$.get($(this).attr('href'), function(data) {
				// userprofile restored from database and refreshed to js scope using refresh	
				window.location.reload();
			});
		});
		
		// save and create new practice cycle
		$('.cycle-toolbar a#save').click(function(e){
			e.preventDefault();
				
			if(confirm("This will begin a new Practice Placement Cycle (PPC).  If you choose 'OK' and you want to revert to a previous cycle later,\n you can restore that PPC by selecting 'Manage Practice Placement Cycle' in Your Toolbar.\n\n Continue?")) {
				window.userProfile = window.emptyUserProfile;
					//update database
					$.post('/ajax/user-status', 
						{ "userProfile" : JSON.stringify(window.userProfile) }, 
						function(data) {
							$.checkHistoryID(function() {
								window.location.reload();
							});
						} 
						,'json');
			}
		});
		
		$('.cycle-toolbar a#restore').click(function(e) {
			e.preventDefault();
			$('div.global_file-viewer > span#content').append("<h2>Restore a previous practice cycle</h2><ul class=cycles></ul>");
			
			$.get('/ajax/get-user-status-history', function(data) {
				var str = "<li>You don't have any saved practice cycles yet.</li>";
				if(typeof data != 'undefined' && data.length > 0) {
				var json = JSON.parse(data);
				$('div.global_file-viewer ul').html('');
				$.each(json, function(i, v) {
						if(i==0) str = '';
						str += "<li><a href=/ajax/restore-user-status?id=" + v.id + ">"+ v.title + " &ndash; (" + v.time + ")</a></li>";		
				});		
				}	
				$('div.global_file-viewer ul').append(str);
				$('div.global_file-viewer').show(100);
			});
		});
});
