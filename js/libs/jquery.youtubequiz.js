/**
	jquery.youtubequiz plugin by Paul Sijpkes
	
	requires: tubeplayer jquery plugin
*/

(function( $ ){
$.fn.youTubeQuiz = function(options) {
	
	var videohtml = "<div class='video-wrapper'><img class='exit' src='/img/exit.png'></img><div id='video'></div><div class='quiz'></div>";	
	var $me = this;
	var $video = null;
	var $quiz = null;
	var $wrapper = null;
	
	var totalCorrect = 0;
	var currentInterval = 0;
	
	var initWrapper = function() {
		$wrapper = $me.find('div.video-wrapper');
		$video = $me.find('div#video');
		$quiz = $me.find('div.quiz');
	};
	
		this.find('.quiz input').live('click', function(e) {
			console.log("Question clicked: "+$(e.target).val());
			checkAnswer($(e.target).val());			
			$(e.target).parent().parent().toggle(1000);
		});
							
		this.find('.ytvclickable').click(function(e) {
				e.preventDefault();
				if($wrapper == null) {
					$me.append(videohtml);
					initWrapper();
				}
				$wrapper.toggle();
				activatePlayer(options.ytVideoId);			
		});
		
		this.find('img.exit').live('click', function(e) {
				$wrapper.remove();
				$wrapper = null;
		});
		
			var activatePlayer = function(ytVideoId) {
				if(options.tubeplayerOptions == undefined)
				$video.tubeplayer({
					width: 600,
					height: 450,
					allowFullScreen: true,
					initialVideo: ytVideoId,
					autoPlay: true,
					autoHide: true,
					iframed: true,
					showinfo: true,	
					preferredQuality: "default"
				});
				else
					$video.tubeplayer(tubeplayerOptions);

				setInterval(function() { videoIntervals($video.tubeplayer("data")); }, 1000);
			}
			
			var getCurrentQuestion = function() {
				
				for(var i=0; i<options.quiz.length; i++) {
					if(options.quiz[i].interval == currentInterval)
						return options.quiz[i];
				}
			}
			
			var checkAnswer = function(answerClicked) {
				var question = getCurrentQuestion();
				
				for(var i=0; i < question.answers.length; i++) {
						if(question.answers[i].id == answerClicked) 
							if(question.answers[i].correct) ++totalCorrect;
				}
			}	
							
			var videoIntervals = function(data) {		
				// Possible values for data.state are unstarted (-1), ended (0), playing (1), paused (2), buffering (3), video cued (5).
				if(options.quiz == undefined) $me.find(".quiz").toggle().html("<div class='quiz-question'><p>No questions defined.</p></div>").toggle(1000);
				if(data.state == 1) {	
					var message = "";
					var $quiz = $me.find(".quiz");
					$.each(options.quiz, function(index, question)  {
							if(Math.floor(data.currentTime) == question.interval) {
							if($quiz.is(":visible")) $quiz.hide(200);
							currentInterval = Math.floor(data.currentTime);
							var astr = "";
							$.each(question.answers, function(i, answer) {
								astr +="<label><input type='radio' value='"+answer.id+"'>&nbsp;&nbsp;"+answer.text+"</input></label>";
							}
							);
							
							message = "<div class='quiz-question'><p>"+question.question+astr+"</div>";
						
							$quiz.html(message).show(500);
							}
					});	
					
					if(Math.floor(data.currentTime) == options.finish.interval) {
						
						var msg = options.finish.message.replace("%totalQuestions%", options.quiz.length);
						
						msg = msg.replace('%correct%', totalCorrect);
						message = "<div class='quiz-question'><p>"+msg+"</p></div>";
						$quiz.toggle().html(message).toggle(1000);
					}
				}
			}
}; 
})( jQuery );