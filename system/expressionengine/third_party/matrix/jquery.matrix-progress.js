(function( $ ) {    
   $.fn.progress = function() { 
    var select = $("#table-nav").clone().attr('id', 'nav-copy');  
    
    $(this).append("<div id='otcem-progress'><p>You are now self assessing.  Steps self assessed so far...</><ul></ul>\
    </div>");
    $('#otcem-progress').append("<p>Other steps you may assess: ");
    $('#otcem-progress').append(select);
    $('#otcem-progress').append("</p><p><button id='save_otcem'>Save and Upload</button></p>");
    
    console.log('attached otcem-progress pane');
    $(document).on('change', "table input[type='checkbox'], table input[type='radio']", function() {
            var changed = $("select#table-nav option:selected").text(); 
            var id = $("select#table-nav option:selected").val();
            if($("#otcem-progress > ul :contains('"+changed+"')").length === 0) {
                console.log('triggered next section');
                if( $("#otcem-progress").is(':hidden') ) {
                    $("#otcem-progress").show(500);
                    $('#menu').fadeTo('slow', 0);
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
    
      $(document).on('change', '#nav-copy', function(e) {
         e.preventDefault();
         var id = $(this).val();
         $("select#table-nav").val(id).trigger('change');
     });
    
    };
})( jQuery );