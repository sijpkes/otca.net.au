$("input#diary, input#evidence, input#contracts, input#letters, input#reflections").change(
    function() {
         if($(this).attr('id') === 'diary') {
             if(!$(this).is(':checked')) {
                $("input#letters, input#reflections").removeAttr('checked').attr('disabled','disabled');
             } else {
                 $("input#letters, input#reflections").removeAttr('disabled');
             }
         }
         
        if($(this).attr('id') === 'letters' || $(this).attr('id') === 'reflections') {
            $("input#diary").attr('checked', 'checked');
        }
        
        loadContainer();
    }
);