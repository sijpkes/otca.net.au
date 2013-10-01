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