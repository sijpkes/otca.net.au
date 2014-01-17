<?php extend_template('default') ?>

	<div>
		<?php echo form_open('C=subscription_details'.AMP.'M=institution_confirm');
			echo $pagination_html;
		 	echo $table_html;
			
		    if (count($institution_action_options) > 0) {
					echo form_dropdown('action', $institution_action_options).NBS.NBS;
			}
			
			echo $pagination_html;
			echo form_submit('effect_institutions', $delete_button_label, 'class="submit"'); 
			echo form_close();
		 ?>
	</div>
	<div style='float:right; margin-top: -25px'>	
		<?php 
				echo form_open('C=subscription_details'.AMP.'M=institution_confirm');
			  	echo form_hidden('action', 'add');
			  	echo form_submit('effect_institutions', $add_button_label, 'class="submit"');
			  	echo form_close(); 
			?>
</div>
