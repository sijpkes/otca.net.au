$(document).ready(function() {    
    window.evStudentId = '<?= $student_id ?>';
    window.evStudentScreenName = '<?= $screen_name ?>';
    
   var tabExpand = "<span style=\"float:right; font-family: verdana, arial, sans-serif; font-size:11px\"><a class=\"link-expand\" href=\"#\">Expand Tab</a></span>";
    
    var loader = '<img src=/img/ajax-loader-circle.gif class=loader></img>';
    
   $.fn.collapse = function(link_html) {
               $("li", this).each(function() {
                                    var myheight = $(this).css('height');
                                   $(this).data('origHeight', myheight).prepend(link_html).addClass('collapsed');
                        });
               };
                   
    var loadContainer = function() { 
        var diary =$("input#diary:checked").length > 0 ? 1 : 0;
        var evidence =$("input#evidence:checked").length > 0 ? 1 : 0;
        var contracts =$("input#contracts:checked").length > 0 ? 1 : 0;
        var letters =$("input#letters:checked").length > 0 ? 1 : 0;
        var reflections =$("input#reflections:checked").length > 0 ? 1 : 0;
        
       $('#timeline-container').html(loader).load("/ajax/educator-timeline-list?suid=<?= $student_id ?>&diary="+diary+"&evidence="+evidence+"&contracts="+contracts+"&letters="+letters
                        +"&reflections="+reflections, function() {
                        $(this).addClass("open-panel");
                        
                       $(this).collapse(tabExpand);
    
        });
        
    };
        
      var loadHighlights = function() {
       $('#highlights-container').
        html(loader).addClass('open-panel').html("").
               load('/ajax/educator-timeline-list?ho=1&diary=1&evidence=1&contracts=1&letters=1&reflections=1&suid=<?= $student_id ?>',
               function() {
                $(this).collapse(tabExpand);
               });
      };
    
<?php   
    include 'input_eventHandler.js';
    include 'link_expand_eventHandler.js';
?>

$("select[name='checkAction']").change(function(){
    var text =$(this).text().toLowerCase();
    
    if(text=='share') {
       $("input#action:checked").each(function(i,v) {
            
        });
    }
});
  
loadHighlights();
loadContainer();
});