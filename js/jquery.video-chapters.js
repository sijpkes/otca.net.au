
(function( $ ) {	
	$.fn.videoChapters = function(iframeID) {
		if(typeof window.orig_video === 'undefined') {
			window.orig_video = [];
		}
		window.orig_video[iframeID] = $('#'+iframeID).clone();
		
		var chapterContainer = this;
		$(this).find('a').on('click', function(e) {
				e.preventDefault();
				$('#'+iframeID).remove();
				$(chapterContainer).prevAll('.video-iframe').first().find('h3').after(window.orig_video[iframeID]);
				
				var video = $('#'+iframeID);
				if(typeof window.video_source === 'undefined') {
					window.video_source = [];
				}
				
				if(typeof window.video_source[iframeID] === 'undefined') {
					window.video_source[iframeID] = $(video).attr('src');
				}
				/*  to use it with a normal anchor change this to something like the below, of course it will vary for URL
				 *  so it'd be better to use a regex, but I'll let someone else worry about that.
				 *
				 *   var str = $(e.target).attr('href').split('#t')[1];
				 *   var nstr = $(str).split("s")[0];
				 *   var mstr = $(nstr).split("m");
				 *   var min = Number(mstr[0]);
				 *   var sec = Number(mstr[1]);
				 *   
				 * */
				
				var seconds = $(e.target).data('seconds');
				
				/* youtube dropped old min/secs format, now we convert what's there to pure seconds */
				var splsec = seconds.split('m');
				var min = Number(splsec[0]);
				var sec = Number(splsec[1].split('s')[0]);
				
				min = min * 60;
				
				var total = min + sec; 
				
				var newSource = window.video_source[iframeID] + "?start="+total+"&autoplay=1&rel=0";
				$(video).attr('src', newSource);
		});

	};	
})( jQuery );