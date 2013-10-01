$(document).ready(function() {
  /*
Sliding skinny banners script. Randomised banners, no banner displayed twice.
Paul Sijpkes - CTL - December 2011
*/
window.banners = $('.slidingbanners');
  if (window.banners.length > 0) {
  var bURL = 'banners.html';
  window.bl = 0;
  window.bi = 0;
  window.last = -1;
  window.used = new Array(0);
  var interval;
  window.c = 0;
  window.t = 0;

  var showBanner = function() {
    if (window.c < window.t)
    return;

    clearInterval(interval);

    var r = 0;
    while ($.inArray(r, window.used) != -1 || r == 0 || r > window.bl || r == window.last)
    r = Math.ceil(Math.random() * window.bl);
    if ($.inArray(r, window.used) == -1) window.used.push(r);
    if (window.used.length >= window.bl) window.used = new Array(0);
    $(".slidingbanners #banner" + r).show(100);
    $(".slidingbanners #banner" + window.last).hide(100);
    window.last = r;

    window.setTimeout("window.rf()", 8000);
  }

  window.rf = function() {
    showBanner();
  }

  interval = window.setInterval("window.rf()", 1000);
  
    $(window.banners
	).load(bURL,
    function()
    {
      if (window.t = 0) window.t = $(".slidingbanners img").length;

      $(".slidingbanners img").each(function(i, o) {
        $(o).load(function() {
          window.c++;
          console.log(c + " loaded");
        });
      });
      $('.slidingbanners a').each(function(n, o) {
        $(this).wrap("<div id='banner" + (n + 1) + "' style='display: none;' class='banner'/>");
        window.bl++;
      });
    });
  }


});