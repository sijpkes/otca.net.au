
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
				var seconds = $(e.target).data('seconds');
				var newSource = window.video_source[iframeID] + "&autoplay=1&#t="+seconds;
				$(video).attr('src', newSource);
		});

	};	
})( jQuery );