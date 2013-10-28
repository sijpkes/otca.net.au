$(document).on('click', 'a.link-expand', function(e) {
    e.preventDefault();
    var li =$(this).closest('li');
    var ydif = li.data('origHeight');
    
    if(li.hasClass('collapsed')) {
       $(this).text('Collapse Tab');
        var str = "+="+ydif;
       $(this).closest('li').animate({height: str}, 500).removeClass('collapsed').addClass('expanded');
        
    } else {
        var str = "-="+ydif;
       $(this).closest('li').animate({height: str}, 500).removeClass('expanded').addClass('collapsed');
       $(this).text('Expand Tab'); 
    }
});