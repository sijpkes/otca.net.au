$(document).ready(function() {

$("a#ed_invite").click(function(e) {
       e.preventDefault();
       var body = "Dear %name%,\n\n As your practicum/university student, I am inviting you to register with the Occupational Therapy Competencies Australia (OTCA) website.\n\nPlease visit:\n\nhttps://otca.net.au/member/register\n\n        to register as a Practice Educator/Lecturer.\n\nSincerely,\n\n%student_name%";
       var ed_name = $("input#ed_name").val();
       body = body.replace("%name%", ed_name);
       body = body.replace("%student_name%", window.studentName);
       $(this).unbind('click');
       var email = $("input#ed_email").val();
       $(this).attr('href', 'mailto:'+email+"?subject=OTCA Invitation&body="+body);
       $(this).trigger('click');
});

var loader = "  <img src='/img/ajax-loader-circle.gif' id='loader'/>";

$(document).on('change', "input.nominated", function() {
        var id = $(this).val();
        $(this).after(loader);
        if($(this).is(':checked')) {
             $.post('/ajax/save-nomed-settings', { educator: id }, function() {
                 $("#loader").remove();
            });
        } else {
             $.post('/ajax/save-nomed-settings?remove=1', { educator: id }, function() {
                 $("#loader").remove();
            });
        }
});


});
