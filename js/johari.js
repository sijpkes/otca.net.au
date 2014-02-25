$(document).ready(function() {
/**
 * Developed by Paul Sijpkes, Centre for Teaching and Learning, University of Newcastle, Australia.
 */
//$("body").css({overflow: 'hidden'});	
var defx = 208;
var defy = 208;

var b1y = $('#johari > #b1').height();
var b2y = $('#johari > #b2').height();
var b3y = $('#johari > #b3').height();
var b4y = $('#johari > #b4').height();
		
var b1x = $('#johari > #b1').width();
var b2x = $('#johari > #b2').width();
var b3x = $('#johari > #b3').width();
var b4x = $('#johari > #b4').width();

var minHeight = 7;
var minWidth = 7;
var maxHeight = 379;
var maxWidth = 379;

var currentBoxId = "";

var currentCL = 208;
var currentCT = 208;

var lastScrollL = 0;
var lastScrollT = 0;

var scrollAdjL = 0;
var scrollAdjT = 0;

var setHandlePosition = function(lastX, lastY) {
		var left = (lastX + containerOffset.left) - 17;
		var top = (lastY + containerOffset.top) - 19;
	
		left = left + "px";
		top = top + "px";
	
		$('#handle').css({ left: left, top: top});
};

var setHoverRegion = function(lastX, lastY) {
	  var left = (lastX + containerOffset.left) - 17;
	  var top = (lastY + containerOffset.top) - 17;
	
	  left = left + "px";
	  top = top + "px";
	
	  $('#hover_region').css({ left: left, top: top});
};

var addBoxMouseOver = function() {
	$(".box").on("mouseenter", function() {
		if($(document).scrollLeft() > 0 || $(document).scrollTop() > 0) {
			window.scrollTo(0, 0);
		}
		var myOffset = $(this).offset();
	
		if(currentBoxId !== $(this).attr('id') ) {
			currentBoxId = $(this).attr('id');
			var w = ($(this).width() - 30) + lastScrollL;
			$("#edit").css({ left: myOffset.left + w, top: (myOffset.top - 20) + lastScrollT });
		
			if($("#edit").is(":hidden")) {
				$("#edit").show();
				$("#handle").hide();
				setHoverRegion(currentCL, currentCT);
			}
		}
	});
};

var containerOffset = $('#johari').offset();

var addBoxClick = function() {
	$("#johari > .box").on('click', function() {
			$("#edit, #handle").hide();
			$(".box").off("mouseenter").off("click");
			
			var $currentBox = $("#"+currentBoxId);
			
			var heading = $("#johari #"+currentBoxId).find("h1").text();
			var subheading = $("#johari #"+currentBoxId).find("p").text();
			var text = $("#johari #"+currentBoxId).find("#text").text();
					
			$currentBox.html("<h1>"+heading+"</h1><p>"+subheading+"</p><span class='editGroup'><textarea>"+text+"</textarea><br><button id='save'>Save</button></span>");
			
			$currentBox.find("textarea").focus();
			
			$(".editGroup > button#save", $currentBox).on('mouseup', function() {
				$("#edit").hide();
				
				var text = $(".editGroup > textarea").val();
				
				$(".editGroup").remove();
				$currentBox.html("<h1>"+heading+"</h1><pre id='text'>"+text+"</pre>");
				
				setHoverRegion(currentCL,currentCT);
				addBoxMouseOver();
				addBoxClick();
			});
			
			$('.editGroup > textarea').keypress(function(e){
  				if(e.keyCode == 13 && !e.shiftKey) {
   						e.preventDefault();
   						$('.editGroup > button#save').trigger('mouseup');
  				}
			});
	});
};

setHoverRegion(currentCL, currentCT);
setHandlePosition(currentCL, currentCT);
addBoxMouseOver();
addBoxClick();

$('#hover_region').on('mouseenter', function(e) {	
		$("#handle").show();
		$("#edit").hide();
		setHoverRegion(10000,208);
});

$('#handle').on('mousedown', function(e) {
	
	$(".box").off('mouseenter');
	$("#edit").hide();
	
	$('#johari').on('mousemove', function(e) {
		var x = e.clientX - containerOffset.left;
		var y = e.clientY - containerOffset.top;
		//setHoverRegion(x,y);
		
		currentCL = x;
		currentCT = y;
		//applyScrollAdjustment();
		setHandlePosition(currentCL, currentCT);
		
		var b1height = b1y + (y - defy);
		b1height = b1height < minHeight ? minHeight : b1height > maxHeight ? maxHeight : b1height;  
		
		var b2height = b2y + (y - defy);
		b2height = b2height < minHeight ? minHeight : b2height > maxHeight ? maxHeight : b2height;
		
		var b3height = y < defy ? b3y + Math.abs(y - defy) : b3y + (defy - y);
		b3height = b3height < minHeight ? minHeight : b3height > maxHeight ? maxHeight : b3height;
		
		var b4height = y < defy ? b4y + Math.abs(y - defy) : b4y + (defy - y);
		b4height = b4height < minHeight ? minHeight : b4height > maxHeight ? maxHeight : b4height;
		
		var b1width = b1x + (x - defy);
		b1width = b1width < minWidth ? minWidth : b1width > maxWidth ? maxWidth : b1width;
		
		var b2width = x < defx ? b2x + Math.abs(x - defx) : b2x + (defx - x);
		b2width = b2width < minWidth ? minWidth : b2width > maxWidth ? maxWidth : b2width;
		
		var b3width = b3x + (x - defx);
		b3width = b3width < minWidth ? minWidth : b3width > maxWidth ? maxWidth : b3width;
		
		var b4width = x < defx ? b4x + Math.abs(x - defx) : b4x + (defx - x);
		b4width = b4width < minWidth ? minWidth : b4width > maxWidth ? maxWidth : b4width;
		
		$('#b1', this).height(b1height).width(b1width);
		$('#b2', this).height(b2height).width(b2width);
		$('#b3', this).height(b3height).width(b3width); 
		$('#b4', this).height(b4height).width(b4width);
});		
});

$('#handle').on('mouseup', function(e) {
	$('#handle').hide();
	
	currentCL = e.clientX - containerOffset.left;
	currentCT = e.clientY - containerOffset.top;

	setHandlePosition(currentCL, currentCT);
	setHoverRegion(currentCL, currentCT);
	
	addBoxMouseOver();
	
	$('#johari').off('mousemove');
});

});