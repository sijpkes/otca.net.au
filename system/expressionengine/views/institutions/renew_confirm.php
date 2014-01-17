<?php extend_template('default') ?>

<?=form_open('C=subscription_details'.AMP.'M=institutions_renew')?>

	<?php foreach($renew as $institution_id):?>
		<?=form_hidden('renew[]', $institution_id)?>
	<?php endforeach;?>

	<p><strong><?=lang('renew_institutions_confirm')?></strong></p>

	<p><?=form_submit('renew_institutions', lang('renew'), 'class="submit"')?></p>

<?=form_close()?>