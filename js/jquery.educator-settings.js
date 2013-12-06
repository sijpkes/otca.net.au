/*
*The MIT License (MIT)
*
*Copyright (c) 2013 Paul Sijpkes.
*
*Permission is hereby granted, free of charge, to any person obtaining a copy
*of this software and associated documentation files (the "Software"), to deal
*in the Software without restriction, including without limitation the rights
*to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
*copies of the Software, and to permit persons to whom the Software is
*furnished to do so, subject to the following conditions:
*
*The above copyright notice and this permission notice shall be included in
*all copies or substantial portions of the Software.
*
*THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
*IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
*FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
*AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
*LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
*OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
*THE SOFTWARE.
*/
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
