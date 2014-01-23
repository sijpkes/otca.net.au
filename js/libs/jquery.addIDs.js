(function( $ ) {
$.fn.addIDs = function() {
	/* removed :not(.bap) from selector */
	this.find('table:not(.bap)').each(function(tablei, tableo) {
	$(tableo).find('tr').each(function(rowi, rowo) {
	if(rowi > 2) { // skip row headers
			var nocells = $('td', rowo).length;
			$('td', rowo).each(function(coli, colo) {
				  if(! (nocells == 4 && coli == 0) ) {// skip column headers
						if(nocells < 4) coli += 1;
						$(colo).find('p').each(function(cbi, cbo) {		
							$(cbo).addClass('otca-step'+(tablei+1));
							$(cbo).addClass('otca-level'+coli);
							$(cbo).attr('id','otca-'+(tablei+1).toString()+'_'+rowi.toString()+'_'+coli.toString()+'_'+cbi.toString());
						});
				  }
			});
		}
		});
	});
	
	try {
		console.log("checking pseudo :nth-of-type support: "+$('.bap tr:nth-of-type(4) p').length+"results... Succesful.");
	} catch(err) {
		console.log('pseudo not supported, creating...');
		
		$.expr[':']['nth-of-type'] = function(elem, i, match) {
    		if (match[3].indexOf("n") === -1) return i + 1 == match[3];
    		var parts = match[3].split("+");
    	return (i + 1 - (parts[1] || 0)) % parseInt(parts[0], 10) === 0;
		};
	}
	// Being a professional
	$('.bap tr:nth-of-type(4) p', this).each(function(i,v) {
		$(v).addClass('otca-step8');
		$(v).addClass('otca-level0');
		$(v).attr('id','otca-8_0_' + i.toString());
	});
	return this;
};     
})( jQuery );