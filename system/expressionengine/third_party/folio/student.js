      $(document).ready(function() { 
          
   $('body').append("<div class='file-viewer upload-box' style='display: none;'> </div>");  
   $('body').append("<div class='file-viewer dialog' style='display: none;'><a href='#close' class='exit'><img src='/img/close-icon.png' alt='close'></a><span id='message'></span></div>");
   $('body').append("<div class='ajax-loader' style='display: none;'> </div>");
   
   var $viewer = $('div.file-viewer.upload-box');   
   var $dialog = $('div.file-viewer.dialog');
   var tabExpand = "<span style=\"float:right; font-family: verdana, arial, sans-serif; font-size:11px; margin-right: 1em\"><a class=\"link-expand\" href=\"#\">Expand Tab</a></span>";
        
    $.fn.collapse = function(link_html) {
               $("li", this).each(function() {
                                    var myheight = $(this).css('height');
                                   $(this).data('origHeight', myheight).addClass('collapsed');
                                   $('#hide', this).parent().after(link_html);
                        });
               };
    
    var loader = '<img src=/img/ajax-loader-circle.gif class=loader></img>';
    var split = location.search.replace('?', '').split('=');
    var highlightID = split[1];
    
    //alert("Testing this page "+split[0]+" "+split[1]);
    
    $.checkHistoryID(function() { 
        window.location.href = '/practice-placement/prepare-for-practice';
    }, 
        true /* no reload on history_id > 0 */
    );
    
    $(document).on('click', '#add_another', function() {
         $('div.dialog').hide();
         $('#save_item').trigger('click'); 
    });
    
    $(document).on('click', 'div.file-viewer a.exit', function(e) {
            e.preventDefault();
            $(this).closest('div.file-viewer').hide(100);
    }); 
    
    var add_evidence = function() {
        
        $(document).on('click', '#cancel', function(e) {
            e.preventDefault();
            window.location.reload();
        });
        
        $(document).on('click', '#save_item', function(e) {
         $("div.ajax-loader").show();
        e.preventDefault();
                  
        $viewer.load('/pages/evidence-upload', 
        function() { 
                //$viewer.find("form#publishForm #criteria_mapping").text(JSON.stringify(critJSONdata));
                //$viewer.find("form#publishForm #searchable_mapping").text(JSON.stringify(sData));
                $viewer.find('form#publishForm textarea#supervisor_emails').after("<input id='emailMe' name='emailMe' type='checkbox'/><label for='emailMe'>Send me a copy of this email.</label>");
                $viewer.find("form#publishForm input[name='cycle_name']").val(window.userProfile.title);
                $viewer.show();
                
                $("div.ajax-loader").hide();
                
                $viewer.find('#supervisor_emails').hide().before("<input id='asminput' name='asminput'></input><button id='addsup'>Add supervisor</button><br><ul id='emailList'></ul>");
                var supArray = [];
                $(this).on('click','#addsup', function(e) {
                    e.preventDefault(); 
                    var email = $('#asminput').val();
                    if(0 == email.length) return false;
                    var i = supArray.indexOf(email);
                    if(-1 == i) { 
                        i = supArray.push(email)-1;
                        $('#emailList').append('<li data-index="'+i+'">'+email+'   <a href="#">Remove</a></li>');
                        $('#supervisor_emails').val(supArray.join());
                    } else {
                        $('#emailList li').each(function(li_index, v){
                            if($(v).data('index') == i) {
                                //$(v).fadeOut(1000).fadeIn(1000);
                                $(v).fadeOut(20).queue(function(next) {$(this).css({'background-color': '#FFF', 'border-color':'yellow'}); next();}).fadeIn(100).delay(50).queue(function(next) { $(this).css({'background-color': 'inherit', 'border-color': '#aaa'}); next(); });
                            }
                        });
                    }
                    return false;
                });

                $(this).on('click', 'ul#emailList a', function(e) { 
                    e.preventDefault();
                    var index = $(this).parents('li').data('index');
                    supArray.splice(index, 1);
                    $(this).parents('li').remove();
                    $('#supervisor_emails').val(supArray.join());
                });
            
                $viewer.find('#publishForm').ajaxForm({
                    dataType: 'json',
                    success: function(data) {
                            if (data.success) {
                                //alert(pracsotStr);
                                var fullPath = $("div.safecracker_file_input input[name='evidence']").val();
                                var filename = fullPath.replace(/^.*[\\\/]/, '');
                                
                                // emails now sent via share interface
                                $.get('/ajax/map-evidence?id='+data.entry_id+"&filename="+filename, function(data) {
                                    var jsonData = JSON.parse(data);
                                    
                                    $viewer.html("");
                                    $viewer.hide();
                                    $dialog.css({ width: "400px", height : "220px", left: "546px", top: "499px" });
                                    
                                    var strLinks = "<p><a class='button' href='/practice-placement/summary-of-your-competencies?show="+jsonData.id+"'>View this item in your ePortfolio</a></p>";
                                    strLinks += "<p><a class='button' id='add_another' href='#' >Add another item</a></p>";
                                    
                                    $dialog.find('span#message').html("<h3>Upload Successful</h3>"+jsonData.message+strLinks);
                                    $dialog.show(250);
                                    $("div.ajax-loader").hide();
                                });
                                
                            } else {
                                    var str = "<p>The following errors with your form were reported:<br>";

                                    $('input, textarea').each(function(i,v){
                                    var id = $(v).attr('id');
                                    if(typeof data.field_errors[id] != 'undefined') {
                                        str += data.field_errors[id]+"<br>";
                                            $(v).css({ "background-color" : "yellow" });
                                        } else {
                                            $(v).css({ "background-color" : "none" });  
                                        }
                                    });
                                    str += "</p>";
                                
                                   $dialog.find('span#message').html(str);
                                   $dialog.show(250);           
                                }
                            }
                });
             });
        }); 
    };
    
    
    var loadContainer = function() { 
        var diary = $("input#diary:checked").length > 0 ? 1 : 0;
        var evidence = $("input#evidence:checked").length > 0 ? 1 : 0;
        var contracts = $("input#contracts:checked").length > 0 ? 1 : 0;
        var letters = $("input#letters:checked").length > 0 ? 1 : 0;
        var reflections = $("input#reflections:checked").length > 0 ? 1 : 0;
        
        $('#timeline-container').html(loader).load("/ajax/timeline-list?diary="+diary+"&evidence="+evidence+"&contracts="+contracts+"&letters="+letters
                        +"&reflections="+reflections, function() {
                        $(this).addClass("open-panel");
                        
                        var highlightedItem = $(this).find("a[href*='assessed-matrix/"+highlightID+"']").closest("li");
                                
                                if(typeof highlightedItem !== 'undefined') {
                                    highlightedItem.css("background-color", "#FFFF9C");
                                    try {
                                    $('html, body').animate({
                                        scrollTop: highlightedItem.offset().top
                                     }, 500);
                                     } catch(e) {
                                    console.log('no scroll, student not adding');
                                     }
                                 } else {
                                    console.log("highlightedItem undefined: "+highlightID);
                                 }
                                 
                      $(this).collapse(tabExpand);
        }); 
        
        add_evidence();   
    };
        
<?php
    include 'input_eventHandler.js';
    include 'link_expand_eventHandler.js';
?>

$(document).on('change', "input#hide", function() {
    var entry_id = $(this).data('id');
    var value = $(this).is(":checked") ? 1 : 0;
    var group_type = $(this).closest('li').attr('class');
    $(this).before(loader+" ");
    $.post('/ajax/hide-diary-entry/', { entry_id: entry_id, hidden: value, group_type: group_type }, function(data) {
        $("img.loader").remove();
    });
});

$(document).on('change', "select#highlight", function() {
    $(this).after("  "+loader);
    var code = $(this).data("code");
    var serverColorVal = $(":selected", this).val();
    var colorVal = "thick solid " + serverColorVal;
    $me = $(this);
    if(typeof colorVal === 'undefined' || serverColorVal == 'none') {
        colorVal = "thin solid #663399";
    }
        $.post('/ajax/save-folio-highlight', { code : code, color : serverColorVal } , function()
        {
                $me.closest("li").css({ border : colorVal });
                
                if(serverColorVal != 'none') {
                    $me.closest("li").find("input#hide:checked").trigger('click'); 
                } else {
                    $me.closest("li").find("input#hide:not(:checked)").trigger('click');   
                }
                $('.loader').remove();
        }
    );
    
});

$(document).on('click', 'img.exit', function(e) {
e.preventDefault();
var confirmed = $(e.target).data('confirmed');
var str_id = $(e.target).prev('select#highlight').data('code');

var id_arr = str_id.split("_");
var type = id_arr[0];
var member = id_arr[1];
var id = id_arr[2];
var eTarget = $(e.target);
var parentDiv = eTarget.parents('.open-panel').first();

var removeEmptyPanel = function() {
    // remove parent;
    var liLen = $(parentDiv).find("li").length;
    if(liLen == 0) {
        if(parentDiv.hasClass('timeline-top')) {
            parentDiv.next().removeClass('timeline-center').addClass('timeline-top');
        }
        if(parentDiv.hasClass('timeline-bottom')) {
            parentDiv.prev().removeClass('timeline-center').addClass('timeline-bottom');
        }
        $(parentDiv).remove();     
    }   
};
 
if(typeof confirmed != 'undefined' && confirmed == 1) {
$(e.target).parents('.otca-textbox').find('br').before("<img id='loader' src='/img/ajax-loader-circle.gif'></img>");
    
    if(typeof id !== 'undefined') {
        
    if(typeof type !== 'undefined') {
        switch(type) {
        case 'diary':
            $.get('/ajax/remove-diary-entry?id='+id, function() {
                var box = $(e.target).parents('li');
                $(box).remove();
                removeEmptyPanel();
            });
        break;
        case 'evidence':
            $.get('/ajax/remove-evidence-entry?id='+id, function() {
                var box = $(e.target).parents('li');
                $(box).remove();
                removeEmptyPanel();
            });
        break;
        }
    }
    
    }
} else {
$('div#confirm-message').remove();
$('img.exit').each(function(i, v) {
if(v != e.target) {
$(v).removeAttr('data-confirmed').removeData('confirmed');
}
});
if(typeof id !== 'undefined') {
$(e.target).after('<div id="confirm-message">\
            Are you sure? Click again to delete the item forever.<br> </div>');
$('div#confirm-message').css({top: e.pageY-10, left: e.pageX+20});
$(e.target).data('confirmed', 1);
}
}
});
loadContainer();
});
