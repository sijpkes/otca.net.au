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
