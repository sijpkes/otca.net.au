/* Quiz script by Paul Sijpkes - Web Production Officer - Centre for Teaching and Learning */
$(document).ready(function() {
$('#score').click(function() {
var count = 0;
for(var i=1; i<15; i++) {
var $answers = $("input[name='answers"+i+"[]']");
console.log('first loop'+i);
$.each($answers,
function(index, item) {
if ('undefined' != typeof $(item).attr('checked') && false != $(item).attr('checked')) {
var $e = $(item).next('.q');
$e.css('font-weight', 'bold');
if (1 == $(item).val()) {
count++;
$e.append("<span class='comment' style='font-size: 14pt;'> &#10003;</span>");
} else {
$e.append("<span class='comment' style='font-size: 14pt;'> &#10007;</span>");
}
$("#correct"+i).show();
}
});
}
$('#score').hide();
$('#yourscore').append("&nbsp;&nbsp;<p style='font-size: 12pt;'><strong>Your score: " + count + "/14</strong></p><button type='button' id='refresh'>Take the quiz again.</button>");
});
$('#refresh').live('click', function() {
window.location.reload(true);
});
});