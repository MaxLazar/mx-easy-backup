<div class="mor">

<?php if($message) : ?>
<div class="mor alert notice">
<p><?php print($message); ?></p>
</div>
<?php endif; ?>

<?php if(!$export_out) : ?>
<?php //success
echo form_open($_form_base."&method=task_screen", '');
	
?>		

<table class="mainTable padTable" id="event_table" border="0" cellpadding="0" cellspacing="0">
<tr class="header" >
<th colspan="3"><?= lang('backup_options')?></th>
</tr>
<tbody>

<tr style="width: 33%;">
<td><?=lang('sfs_action')?></td>
<td><input name="<?=$input_prefix;?>[auto_checking]" value="y"  id="auto_checking_y" label="yes" type="radio" <?=((isset($settings['auto_checking'])) ? (($settings['auto_checking'] == "y") ? 'checked="checked"' : ''): '' );?>>&nbsp;<label for="auto_checking_y">Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<input name="<?=$input_prefix;?>[auto_checking]" value="n" id="auto_checking_n" label="no" type="radio" <?=((isset($settings['auto_checking'])) ? (($settings['auto_checking'] == "n") ? 'checked="checked"' : ''): '' );?>>&nbsp;<label for="auto_checking_n">No</label></td>
</tr>
<tr style="width: 33%;">
<td><?=lang('min_frequency')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?=$input_prefix;?>[min_frequency]" id="min_frequency" value="<?=((isset($settings['min_frequency'])) ? $settings['min_frequency'] : '1' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr style="width: 33%;">
<td><?=lang('sfs_api')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?=$input_prefix;?>[sfs_api]" id="sfs_api" value="<?=((isset($settings['sfs_api'])) ? $settings['sfs_api'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>

</tbody>
</table>

<p class="centerSubmit"><input name="edit_field_group_name" value="<?= lang('save_settings'); ?>" class="submit" type="submit"></p>


<?= form_close(); ?>

<?php endif; ?>
</div>