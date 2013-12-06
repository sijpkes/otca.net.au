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
       var q = 1;
       
       var t =  $("#quiz > li").length;
       
       $("#quiz > li:nth-child(1)").slideDown();
       
       $("#quiz").after("<button id='prevQ' disabled>&larr; Previous question</button><button id='answer'>This is my answer</button>&nbsp;&nbsp;<button id='nextQ'>Next question &rarr;</button>");
       
       $("button#answer").click(function() {
            $("p.feedback").remove();
            
            var textArea = $("textarea", "li:visible");
            var isLongAnswer = $(textArea).length;
            
            if(isLongAnswer == 0) {
            var ck = $("li:visible input[name='answer']:checked");
            var feedback = $(ck).data('feedback');
            var correct = $(ck).val();
            
            if(correct == 1) {
                $(ck).parent().after("<p class='feedback' style='color: lightgreen; font-family: arial, helvetica, sans-serif;'><strong style='font-size: 12pt'>Correct! :-)</strong><br>"+feedback+"</p>");
            } else {
                $(ck).parent().after("<p class='feedback' style='color: red; font-family: arial, helvetica, sans-serif'><strong style='font-size: 12pt'>Incorrect :-( </strong><br><br>"+feedback+"</p>");
            }
            } else {
              var feedback = $(textArea).data("feedback");
              var yourAnswer = $(textArea).val();
              
              $(textArea).replaceWith("<p class='feedback'>Your answer:<br><br>"+yourAnswer+"</p><br><p><pre style='color: lightgreen; font-family: arial, helvetica, sans-serif; font-size: 10pt'>"+feedback+"</pre></p>");
            }
       });
       
        $("button#nextQ, button#prevQ").click(function() {
            $("#quiz > li:nth-child("+q+")").slideUp();
            
            if($(this).attr('id') == "nextQ") 
                q += 1;
            else
                q -= 1;
            
            if(q >= t) {
                $("#nextQ").attr('disabled', 'disabled');
                $("#prevQ").removeAttr('disabled');
            }
            else if(q <= 1) {
                $("#prevQ").attr('disabled', 'disabled');
                 $("#nextQ").removeAttr('disabled');
            } else {
                $("#prevQ").removeAttr('disabled');
                $("#nextQ").removeAttr('disabled');
            }

            $("#quiz > li:nth-child("+q+")").slideDown(); 
       });
});    
