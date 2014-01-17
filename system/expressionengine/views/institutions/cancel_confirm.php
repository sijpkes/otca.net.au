<?php extend_template('default') ?>

<?=form_open('C=subscription_details'.AMP.'M=institutions_cancel')?>

	<?php foreach($cancel as $institution_id):?>
		<?=form_hidden('cancel[]', $institution_id)?>
	<?php endforeach;?>

	<p><strong><?=lang('cancel_institutions_confirm')?></strong></p>

	<p class="notice"><?=lang('action_can_not_be_undone')?></p>

	<p><?=form_submit('cancel_institutions', lang('cancel'), 'class="submit"')?></p>

<?=form_close()?>