<?php extend_template('default') ?>

<?=form_open('C=subscription_details'.AMP.'M=institutions_add')?>
	
	<p><?=lang('add_institution_details')?></p>
	<p>
	<?= form_input(array(
              'name'        => 'add_name',
              'id'          => 'add_name',
              'value'       => '',
              'maxlength'   => '100',
              'size'        => '20',
			  'style' => 'width: 200px')) ?></p>
	<p><?=form_submit('renew_institutions', lang('add'), 'class="submit"')?></p>

<?=form_close()?>