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
