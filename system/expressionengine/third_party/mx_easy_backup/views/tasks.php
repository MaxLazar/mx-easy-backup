<div class="mor">

<?php if($message) : ?>
<div class="mor alert notice">
<p><?php print($message); ?></p>
</div>
<?php endif; ?>

<?=form_open($_form_base."&method=task".(($task_id) ? '&task_id='.$task_id : ''), array('name'=>'backupsettings', 'id'=>'backupsettings'), '')?>
			
<table class="mainTable padTable" id="event_table" border="0" cellpadding="0" cellspacing="0">
<tr class="header" >
<th colspan="3"><?= lang('backup_options')?></th>
</tr>
<tbody>
<tr>
<td style="width: 200px;"><?=lang('task_name')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?=$input_prefix;?>[task_name]" id="task_name" value="<?=((isset($settings['task_name'])) ? $settings['task_name'] : $task_name )?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr>
<td><?=lang('local_path')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?=$input_prefix;?>[local_path]" id="local_path" value="<?=((isset($settings['local_path'])) ? $settings['local_path'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr>
<td><?=lang('backup_options')?></td>
<td>
<?=form_checkbox($input_prefix.'[db_backup]', 'yes', ((isset($settings['db_backup'])) ? $settings['db_backup'] : '' ), 'id="db_bk"')?> <label for="db_bk"><?=lang('db').NBS.NBS?></label> <br/>
<?=form_checkbox($input_prefix.'[config_files]', 'yes', ((isset($settings['config_files'])) ? $settings['config_files'] : '' ), 'id="config_files"')?> <label for="config_files"><?=lang('config_files').NBS.NBS.NBS?></label><br/>
<?=form_checkbox($input_prefix.'[themes_folder]', 'yes', ((isset($settings['themes_folder'])) ? $settings['themes_folder'] : '' ), 'id="themes_folder"')?> <label for="themes_folder"><?=lang('themes_folder').NBS.NBS?></label><br/>
<!--
<?=form_checkbox($input_prefix.'[themes_folder_third_party]', 'yes', ((isset($settings['themes_folder_third_party'])) ? $settings['themes_folder_third_party'] : '' ), 'id="themes_folder_third_party"')?> <label for="themes_folder_third_party"><?=lang('themes_folder_third_party').NBS.NBS?></label><br/> -->

<?=form_checkbox($input_prefix.'[addons_folder]', 'yes', ((isset($settings['addons_folder'])) ? $settings['addons_folder'] : '' ), 'id="addons_folder"')?> <label for="addons_folder"><?=lang('addons_folder').NBS.NBS?></label><br/>
<?=form_checkbox($input_prefix.'[templates_folder]', 'yes', ((isset($settings['templates_folder'])) ? $settings['templates_folder'] : '' ), 'id="templates_folder"')?> <label for="templates_folder"><?=lang('templates_folder').NBS.NBS?></label><br/>
<?=form_checkbox($input_prefix.'[language_folder]', 'yes', ((isset($settings['language_folder'])) ? $settings['language_folder'] : '' ), 'id="language_folder"')?> <label for="language_folder"><?=lang('language_folder').NBS.NBS?></label>
</td>
</tr>
<tr>
<td><?=lang('send_files_after_backup')?></td>
<td><?=form_dropdown($input_prefix.'[send_to]', $send_to_list, ((isset($settings['send_to'])) ? $settings['send_to'] : 'none' ), 'id="f_method"').NBS.NBS?>
<!-- <?=form_checkbox($input_prefix.'[send_files_after_backup]', 'yes', ((isset($settings['send_files_after_backup'])) ? $settings['send_files_after_backup'] : '' ), 'id="send_files_after_backup"')?> <label for="send_files_after_backup"><?=lang('send_files_after_backup_info').NBS.NBS?></label> --> </td>
</tr>
<?php if($system_mode) : ?>
<tr>
<td><?=lang('backup_type')?><label></label><div class="subtext"><?=lang('url_info')?></div></td>
<td><?=form_dropdown($input_prefix.'[backup_type]', $backup_types, ((isset($settings['backup_type'])) ? $settings['backup_type'] : 'full' ), 'id="f_backup_type"').NBS.NBS?></td>
</tr>
<?php endif; ?>
<tr>
<td><?=lang('send_notification')?></td>
<td><?=form_checkbox($input_prefix.'[send_notification]', 'yes', ((isset($settings['send_notification'])) ? $settings['send_notification'] : '' ), 'id="send_notification"')?></td>
</tr>
<!--
<tr>
<td><?=lang('schedule').lang('optional')?></td>
<td><?=select_c($settings,$input_prefix)?></td>
</tr>

<tr>
<td><label><?=lang('cron_file')?></label><div class="subtext"><?=lang('')?></div></td>
<td><?=(($task_id == '') ? "" : ((isset($settings['second'])) ? $settings['second'].NBS.$settings['minutes'].NBS.$settings['hours'].NBS.$settings['days'].NBS.$settings['months'].NBS.$settings['years'].NBS : '* * * * * *') . ' /your/path/to/php '.PATH_THIRD."mx_easy_backup/cron.mx_easy_backup.php ".$task_id)?></td>
</tr>

-->
<tr>
<td><label><?=lang('url')?></label><div class="subtext"><?=lang('url_info')?></div></td>
<td><?=(($task_id == '') ? "" : $url)?><?=form_hidden($input_prefix.'[uniqid]', $settings['uniqid'])?></td>
</tr>



</tbody>

<tr class="header" >
<th colspan="3">
<?=form_checkbox($input_prefix.'[optional_files]', 'yes', ((isset($settings['optional_files'])) ? $settings['optional_files'] : '' ), 'id="optional_files_id" class="c_toogle" rel="optional_files_table"').NBS.NBS?><label for="optional_files_id"><?=lang('files_for_backup')?></label></th>
</tr>
<tbody  id="optional_files_table" <?=((isset($settings['optional_files'])) ? "" : "" )?>>
<tr>
<td colspan="3"  class="alert info"><strong><?=lang('include_files')?></strong></td>
</tr>
<tr> 
<td colspan="3"><textarea name="<?=$input_prefix;?>[optional_files_list]" id="optional_files_list" rows="10"><?=((isset($settings['optional_files_list'])) ? $settings['optional_files_list'] : '' );?></textarea></td>
</tr>
<tr>
<td colspan="3"  class="alert info"><strong><?=lang('exclude_files')?></strong></td>
</tr>
<tr> 
<td colspan="3"><textarea name="<?=$input_prefix;?>[exclude_files_list]" id="exclude_files_list" rows="10"><?=((isset($settings['exclude_files_list'])) ? $settings['exclude_files_list'] : '' );?></textarea></td>
</tr>
<tr> 
<td colspan="3"><?=lang('files_for_backup_info')?></td>
</tr>

</tbody>

</table>


<p class="centerSubmit"><input name="edit_field_group_name" value="<?= lang('save_settings'); ?>" class="submit" type="submit"></p>

<?= form_close(); ?>
<script type="text/javascript">
		jQuery(function() {


			$('#system_info').click(function()
			{
			
			})
			
			});
</script>			

</div>

<?php
function select_c ($settings, $prefix) {
 $s = array (
 				"second" => array ("start" => 0, "end"=> 59),
 				"minutes" => array ("start" => 0, "end"=> 59),
 				"hours" => array ("start" => 0, "end"=> 23),
 				"days" => array ("start" => 0, "end"=> 31),
 				"months" => array ("start" => 0, "end"=>12),
 				"years" => array ("start" => 2011, "end"=>2070)
 );
 
 $out = "";

 foreach ($s as $key => $val) {
 	$out .= '<select name="'.$prefix.'['.$key.']"><option value="*" '.((isset($settings[$key])) ? (($settings[$key] == '*') ? 'selected="selected"' : '') : '').'>*</option>';
 	for ($i = $val['start']; $i <= $val['end']; $i++) {
 		$out .= '<option value="'.$i.'" '.((isset($settings[$key])) ? (($settings[$key] == $i && $settings[$key] != '*') ? 'selected="selected"' : '') : '').'>'.$i.'</option>';
 	};
 
 	$out .= '</select>'.NBS;
 }
 return $out;
}
?>