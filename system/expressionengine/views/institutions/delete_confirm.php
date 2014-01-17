<?php extend_template('default') ?>

<?=form_open('C=subscription_details'.AMP.'M=institutions_delete')?>

	<?php foreach($damned as $institution_id):?>
		<?=form_hidden('delete[]', $institution_id)?>
	<?php endforeach;?>

	<p><strong><?=lang('delete_institutions_confirm')?></strong></p>

	<p class="notice"><?=lang('action_can_not_be_undone')?></p>

	<p><?=form_submit('delete_institutions', lang('delete'), 'class="submit"')?></p>

<?=form_close()?>