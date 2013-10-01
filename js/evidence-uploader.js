$('#publishForm').ajaxForm({
                                        dataType: 'json',
                                        success: function(data) {
                                                if (data.success) {
                                                        alert('You successfully added '+data.title);
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
							//alert(str);
							var emails = $("textarea#supervisor_emails").val();
							var fullPath = $("div.safecracker_file_input input[name='evidence']").val();
							var filename = fullPath.replace(/^.*[\\\/]/, '');
							
							$.get('/ajax/map-evidence?id='+data.entry_id+"&emails="+emails+"&filename="+filename, function(data) {
								alert(str + data.message);
							});
                                                }
                                        }
                                });