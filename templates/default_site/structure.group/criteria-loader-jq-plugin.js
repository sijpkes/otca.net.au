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
<?php
$member_id = ee()->session->userdata('member_id');
if($member_id == 0) return;

/*
This query returns the latest assessment for each piece of evidence uploaded by the student.
This clever syntax obtained here:
http://stackoverflow.com/questions/12102200/get-records-with-max-value-for-each-group-of-grouped-sql-results
Note this only works for MySQL dbs.
*/
$sql = "SELECT * FROM (select data.entry_id, data.field_id_6 as self_assessment, av.matrix_ids as supervisor_assessment, av.date_assessed FROM  exp_channel_data data LEFT JOIN (exp_channel_titles title , otca_evidence ev, otca_evidence_validated av) ON (data.entry_id = ev.entry_id AND title.entry_id = ev.entry_id AND ev.entry_id = av.evidence_id) WHERE title.author_id = '$member_id' ORDER BY data.entry_id, av.date_assessed desc) ua group by ua.entry_id";

// get supervisor assessments of evidence
$query = $this->EE->db->query($sql);
$json_array = array();
$sep = "";
$i=0;
foreach($query->result_array() as $row) // returns one row
{
	if($i++ > 0) { 
		$sep=","; 
	} else {
		$sep="";
	}
	
	$json_array = array_merge(json_decode($row['supervisor_assessment']), $json_array);
}
$query = $this->EE->db->query("SELECT id from otca_pracsot ORDER BY id ASC");
$pracArray = array();
foreach($query->result_array() as $row) // returns one row
{
	$pracArray[] = $row['id'];
}

$json_evidence_str = json_encode($json_array);

$db_vars = "var evidence = $json_evidence_str;\nvar pracsot = ".json_encode($pracArray).";\n";
?>
(function( $ ) {
$.fn.criteriaLoader = function() {
	var weight = 1;
	var threshold = 133;

	var $me = this;

<?php echo $db_vars; ?>

var emer = 0;
var cons = 0;
var comp = 0;

var checkedPrac = {
	emer : [],
	cons : [],
	comp : []
};
$.each(evidence, function(i, v) {
	if(typeof v.pracsot !== 'undefined') {
	var evPrac = v.pracsot.split(',');
	$.each(evPrac, function(i, prac) {
		if($.inArray(prac, pracsot) != -1) {
			switch(v.level) {
			case 1:
				if($.inArray(prac, checkedPrac.emer) == -1) {
					checkedPrac.emer.push(prac);
					emer += weight;
				}
			break;
			case 2:
				if($.inArray(prac, checkedPrac.cons) == -1) {
					checkedPrac.cons.push(prac);
					cons += weight;
				}
			break;
			case 3:	
				if($.inArray(prac, checkedPrac.comp) == -1) {
					checkedPrac.comp.push(prac);
					comp += weight;
				}
			}	
		}
	});
	} else {
		console.log("Pracsot undefined for element "+i+" in assessed evidence array.");
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
return this;
};
})( jQuery );
