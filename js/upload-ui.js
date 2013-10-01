$(document).ready(function() {
	$('#supervisor_emails').hide().before("<input id='asminput' name='asminput'></input><button id='addsup'>Add supervisor</button><br><ul id='emailList'></ul>");
	var supArray = [];
	$(document).on('click','#addsup', function(e) {
		e.preventDefault();
		var email = $('#asminput').val();
		supArray.push(email);
		var i = supArray.indexOf(email);
		$('#emailList').append('<li data-index="'+i+'">'+email+'   <a href="#">Remove</a></li>');
		$('#supervisor_emails').val(supArray.join());
	});
	
	$(document).on('click', 'ul#emailList a', function(e) { 
		e.preventDefault();
		var index = $(this).data('index');
		supArray.splice(index, 1);
		$(this).parents('li').remove();
		$('#supervisor_emails').val(supArray.join());
	});
});