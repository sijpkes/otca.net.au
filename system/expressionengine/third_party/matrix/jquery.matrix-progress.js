(function( $ ) {    
   $.fn.progress = function() { 
    var select = $("#table-nav").clone().attr('id', 'nav-copy');  
    
    $(this).append("<div id='progress'><p>You are now self assessing.  Steps self assessed so far...</><ul></ul>\
    </div>");
    $('#progress', this).append("<p>Other steps you may assess: ");
    $('#progress', this).append(select);
    $('#progress', this).append("</p><p><button id='pupload'>Save and Upload</button></p>");
    
    console.log('attached progress pane');
    $(document).on('change', "table input[type='checkbox'], table input[type='radio']", function() {
            var changed = $("select#table-nav option:selected").text(); 
            var id = $("select#table-nav option:selected").val();
            if($("#progress > ul :contains('"+changed+"')").length === 0) {
                console.log('triggered next section');
                if( $("#progress").is(':hidden') ) {
                    $("#progress").show(500);
                }
                $("#progress > ul").append("<li><a href='#' data-id='"+id+"'>"+changed+"</li></a>");
            }        
    });
    
    $(document).on('click', '#progress a', function(e) {
        e.preventDefault();
        var item = $(this).text();
        var id = $(this).data('id');
        $("select#table-nav").val(id).trigger('change');
    });
    
     $(document).on('click', '#pupload', function(e) {
         $("#save_otcem").trigger('click');
     });
     
      $(document).on('change', '#nav-copy', function(e) {
         e.preventDefault();
         var id = $(this).val();
         $("select#table-nav").val(id).trigger('change');
     });
    
    };
})( jQuery );