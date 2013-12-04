(function( $ ) {    
   $.fn.progress = function(previousFeedback) { 
    var select = $("#table-nav").clone().attr('id', 'nav-copy');  
    
    var uploadBox = "<div class=\"contrast\" style='position: fixed; left: 465px; top: 180px; width: 500px'>\
                <p id=\"feedback\">Please provide meaningful written feedback for the student.\
                Your previous feedback to the student will be displayed below.\
                 <br><textarea style=\"min-width: 300px; max-width: 500px; height: 180px;\">"+previousFeedback+
                 "</textarea></p><br>\
                 <button id=\"submitFeedback\">Submit Assessment to Student's ePortfolio</button></div>";
    
    $(this).append("<div id='otcem-progress'><p>Please verify the steps below that have been\
    self-assessed by the student.</><ul></ul>\
    </div>");
    $('#otcem-progress', this).append("<p>Other steps you may assess: ");
    $('#otcem-progress', this).append(select);
    $('#otcem-progress', this).append("</p><p><button id='commsave'>Comment and Save to\
     Student's ePortfolio</button></p>");
    
    $("table input[type='checkbox']:checked").each(function() {
        var ob = $(this).data('criteria');
        var step = ob.step;
        var stepName = window.stepDefinitions[ob.step == 0 ? 8 : ob.step];
        if($("#otcem-progress > ul li:contains('"+stepName+"')").length === 0) {
            $("#otcem-progress > ul").append("<li><a href='#' data-id='"+step+"'>"+stepName+"</a></li>");
        }
    });

    console.log('attached progress pane');
    $(document).on('change', "table input[type='checkbox'], table input[type='radio']", function() {
            var changed = $("select#table-nav option:selected").text(); 
            var id = $("select#table-nav option:selected").val();
            if($("#otcem-progress > ul :contains('"+changed+"')").length === 0) {
                console.log('triggered next section');
                if( $("#otcem-progress").is(':hidden') ) {
                    $("#otcem-progress").show(500);
                }
                $("#otcem-progress > ul").append("<li><a href='#' data-id='"+id+"'>"+changed+"</a></li>");
            }        
    });   
    
    $(document).on('click', '#otcem-progress a', function(e) {
        e.preventDefault();
        //var item = $(this).text();
        var id = $(this).data('id');
        $("select#table-nav").val(id).trigger('change');
    });
    
     $(document).on('click', '#commsave', function(e) {
         $("body").append(uploadBox);
         $(".feedback, .evidencing-app").fadeTo('slow', 0.3);
     });
     
     $(document).on('change', '#nav-copy', function(e) {
         e.preventDefault();
         var id = $(this).val();
         $("select#table-nav").val(id).trigger('change');
     });
    
      $('#otcem-progress', this).show(500);
      $('#otcem-progress li a').first().trigger('click');
    };
})( jQuery );