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
$.fn.criteriaLoader = function() {
var weight = 1;
var threshold = 40;

var $me = this;

$.getJSON("http://otaltc.net/pages/user-criteria-data", 
function(data) { 
	var emer = 0;
	var cons = 0;
	var comp = 0;
	
	$.each(data, function(i, ob) {
		switch(ob.level) {
                  case "1":
                      emer += weight;
                      console.log(emer);
                  break;
                  case "2":
                      cons += weight;
                  break;
                  case "3":
                      comp += weight;      
                }
         });

		var emer_perc = emer > 0 ? emer / threshold : 0;
		var cons_perc = cons > 0 ? cons / threshold : 0;
		var comp_perc = comp > 0 ? comp / threshold : 0;
			
		$me.find("#emerging").attr('title', 'Emerging '+Math.round(emer_perc*100)+'%');
		$me.find("#consolidating").attr('title', 'Consolidating '+Math.round(cons_perc*100)+'%');
		$me.find("#competent").attr('title', 'Competent to Graduate '+Math.round(comp_perc*100)+'%');
		
		emer_perc = emer_perc + 0.3;
		cons_perc = cons_perc + 0.3;
		comp_perc = comp_perc + 0.3;
		
		if(emer_perc > 0.3) {	
			$me.find("#emerging").css({ 'opacity' : emer_perc, "color": "#C84242"});
		}
		if(cons_perc > 0.3) {	
			$me.find("#consolidating").css({ 'opacity' : cons_perc, "color" : "#FF8400"});
		}
		if(comp_perc > 0.3) {
			$me.find("#competent").css({ 'opacity' : comp_perc, "color" : "#42C842"});
		}
});

return this;
};
})( jQuery );
