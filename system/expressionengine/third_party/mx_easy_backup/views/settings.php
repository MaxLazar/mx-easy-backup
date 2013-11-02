<div class="mor">

<?php if($message) : ?>
<div class="mor alert notice">
<p><?php print($message); ?></p>
</div>
<?php endif; ?>




<?php echo form_open($_form_base."&method=settings", array('name'=>'backupsettings', 'id'=>'backupsettings'), '')?> <!-- -->
<div style="float: left; ">
	<span class="button"><a href="#backup_options" class="submit"><?php echo lang('backup_options')?></a></span>
	<?php if($pro_mode && $encryption_key) : ?>
	<span class="button"><a href="#aws" class="submit"><?php echo lang('aws')?></a></span>
	<span class="button"><a href="#ftp" class="submit"><?php echo lang('ftp')?></a></span>
	<span class="button"><a href="#sftp" class="submit"><?php echo lang('sftp')?></a></span>
	<span class="button"><a href="#backup_to_email" class="submit"><?php echo lang('backup_to_email')?></a></span>
	<?php endif; ?>
	<span class="button"><a href="#notification" class="submit"><?php echo lang('notification')?></a></span>
	<div class="clear_both"></div>
</div>


<br/>
<br/>
<table class="mainTable padTable" id="event_table" border="0" cellpadding="0" cellspacing="0">
<?php if(!$encryption_key && $pro_mode) : ?>
<tr class="header alert" id="dropbox">
<th colspan="3"><?php echo lang('key_title')?></th>
</tr>
<tbody >
<tr style="width: 33%;">

<td><?php echo lang('key_info')?></td>
<td><input dir="ltr" style="width: 100%;" name="encryption_key" id="encryption_key" value="<?php echo ((isset($encryption_key)) ? $encryption_key : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
</tbody>
<?php endif; ?>

<tr class="header" >
<th colspan="3"><?php echo lang('backup_options')?></th>
</tr>
<tbody id="backup_options">
<tr style="width: 33%;">
<td style="width: 33%;"><?php echo lang('default_local_path')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[local_path]" id="local_path" value="<?php echo ((isset($settings['local_path'])) ? $settings['local_path'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('local_backup_space')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[local_space]" id="local_path" value="<?php echo ((isset($settings['local_space'])) ? $settings['local_space'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>

<tr style="width: 33%;">
<td><?php echo lang('method')?></td>
<td>
<?php if($win) : ?>
<?php echo form_hidden($input_prefix.'[method]', 'php')?><span class="go_notice"><?php echo lang('php_only')?></span>
<?php else:?>
<?php echo form_dropdown($input_prefix.'[method]', $methods, ((isset($settings['method'])) ? $settings['method'] : 'php' ), 'id="f_method"').NBS.NBS?>
<?php endif; ?>
</td>
</tr>
<!-- <tr style="width: 33%;<?php echo (($settings['method'] == 'php') ? 'display:none' : 'display:none')?>" id="archive_method">
<td><?php echo lang('archive_method')?></td>
<td>
	<?php echo form_dropdown($input_prefix.'[archive_method]', $archive_methods, ((isset($settings['archive_method'])) ? $settings['archive_method'] : 'zip' ), 'id="a_method"').NBS.NBS.form_checkbox($input_prefix.'[arch_refresh]', 'yes', ((isset($settings['arch_refresh'])) ? $settings['arch_refresh'] : '' ), 'id="arch_refresh"').' <label for="arch_refresh">'.lang('refresh').'</label>'?>
</td>
</tr> -->
<!--
<tr style="width: 33%;">
<td><?php echo lang('cron_file')?></td>
<td>
<strong><?php echo PATH_THIRD?>/mx_easy_backup/cron.mx_easy_backup.php</strong>
</td>
</tr>-->
</tbody>

<?php if($pro_mode && $encryption_key) : ?>
<tr class="header"  id="aws">
<th colspan="3"><?php echo lang('aws_settings')?></th>
</tr>
<tbody>

<tr style="width: 33%;">
<td><?php echo lang('aws_access_key')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[aws_access_key]" id="aws_access_key" value="<?php echo ((isset($settings['aws_access_key'])) ? $settings['aws_access_key'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('aws_secret_key')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[aws_secret_key]" id="aws_secret_key" value="<?php echo ((isset($settings['aws_secret_key'])) ? $settings['aws_secret_key'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('s3_bucket_name')?></td>
<td>
 <?php
$aws_ = false;
if  (!$aws_errors) {

	if  (isset($settings['aws_secret_key']) and isset($settings['aws_access_key']) and $buckets) {
		print (form_dropdown($input_prefix.'[bucket]', $buckets, ((isset($settings['bucket'])) ? $settings['bucket'] : '' ), 'id="id_bucket"').NBS.NBS.form_checkbox($input_prefix.'[aws_refresh]', 'yes', ((isset($settings['aws_refresh'])) ? $settings['aws_refresh'] : '' ), 'id="aws_refresh"').' <label for="aws_refresh">'.lang('refresh').'</label>');
		$aws_ = true;
	}else{
		print('<span class="go_notice">'.lang('bucket_info').'</span> ');
	}

}else {
	print_r('<span class="go_notice">'.$aws_errors.'</span>');

}
?>
<input type="hidden" name="<?php echo $input_prefix;?>[buckets_list]" value="<?php echo $buckets_list?>" /> </td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('backup_space')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[s3_space]" id="aws_space" value="<?php echo ((isset($settings['s3_space'])) ? $settings['s3_space'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr style="width: 33%;<?php if(!$aws_) : ?>display:none;<?php endif; ?>">
<td><?php echo lang('create_s3_bucket')?></td>
<td><input dir="ltr" style="width: 200px;" name="<?php echo $input_prefix;?>[create_s3_bucket]" id="create_s3_bucket"  size="20" maxlength="256" class="input" type="text"> <?php echo lang('create_s3_bucket_info')?></td>
</tr>

</tbody>


<tr class="header" id="ftp">
<th colspan="3"><?php echo lang('ftp_settings')?></th>
</tr>
<tbody >

<tr style="width: 33%;">

<td><?php echo lang('host')?></td>
<td><input dir="ltr" style="width: 200px;" name="<?php echo $input_prefix;?>[host]" id="host" value="<?php echo ((isset($settings['host'])) ? $settings['host'] : '' );?>" size="20" maxlength="256" class="input" type="text"> <?php echo lang('port')?>
 <input dir="ltr" style="width: 40px;" name="<?php echo $input_prefix;?>[ftp_port]" id="ftp_port" value="<?php echo ((isset($settings['ftp_port'])) ? $settings['ftp_port'] : '21' );?>" size="20" maxlength="3" class="input" type="text"></td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('username')?></td>
<td><input dir="ltr" style="width: 200px;" name="<?php echo $input_prefix;?>[username]" id="username" value="<?php echo ((isset($settings['username'])) ? $settings['username'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('password')?></td>
<td><input dir="ltr" style="width: 200px;" name="<?php echo $input_prefix;?>[password]" id="password" value="<?php echo ((isset($settings['password'])) ? $settings['password'] : '' );?>" size="20" maxlength="256" class="input" type="text">
</td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('ftp_path')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[ftp_path]" id="ftp_path" value="<?php echo ((isset($settings['ftp_path'])) ? $settings['ftp_path'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('backup_space')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[ftp_backup_space]" id="ftp_backup_space" value="<?php echo ((isset($settings['ftp_backup_space'])) ? $settings['ftp_backup_space'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>

<tr style="width: 33%;">
<td><?php echo lang('passive_mode')?></td>
<td><?php echo form_checkbox($input_prefix.'[passive_mode]', 'yes', ((isset($settings['passive_mode'])) ? $settings['passive_mode'] : '' ), 'id="passive_mode"')?> </td>
</tr>
</tbody>

<tr class="header" id="sftp">
<th colspan="3"><?php echo lang('sftp_settings')?></th>
</tr>
<tbody>
<tr style="width: 33%;">
<td><?php echo lang('host')?></td>
<td><input dir="ltr" style="width: 200px;" name="<?php echo $input_prefix;?>[sftp_host]" id="sftp_host" value="<?php echo ((isset($settings['sftp_host'])) ? $settings['sftp_host'] : '' );?>" size="20" maxlength="256" class="input" type="text"> <?php echo lang('port')?>
 <input dir="ltr" style="width: 40px;" name="<?php echo $input_prefix;?>[sftp_port]" id="sftp_port" value="<?php echo ((isset($settings['sftp_port'])) ? $settings['sftp_port'] : '22' );?>" size="20" maxlength="3" class="input" type="text"></td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('username')?></td>
<td><input dir="ltr" style="width: 200px;" name="<?php echo $input_prefix;?>[sftp_username]" id="sftp_username" value="<?php echo ((isset($settings['sftp_username'])) ? $settings['sftp_username'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('password')?></td>
<td><input dir="ltr" style="width: 200px;" name="<?php echo $input_prefix;?>[sftp_password]" id="sftp_password" value="<?php echo ((isset($settings['sftp_password'])) ? $settings['sftp_password'] : '' );?>" size="20" maxlength="256" class="input" type="text">
</td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('ftp_path')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[sftp_path]" id="sftp_path" value="<?php echo ((isset($settings['sftp_path'])) ? $settings['sftp_path'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('backup_space')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[sftp_space]" id="sftp_space" value="<?php echo ((isset($settings['sftp_space'])) ? $settings['sftp_space'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>

</tbody>


<tr class="header"   id="backup_to_email">
<th colspan="3"><?php echo lang('send_to_email')?></th>
</tr>
<tbody>
<tr style="width: 33%;">
<td><?php echo lang('send_to_email_address')?><div class="subtext"><?php echo lang('email_address_info')?></div></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[send_to_email_address]" id="send_to_email_address" value="<?php echo ((isset($settings['send_to_email_address'])) ? $settings['send_to_email_address'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr>
<td colspan="3"  class="alert info"><?php echo lang('email_tags')?>
<div style="padding:10px 0 0 0;"><div class="cp_button"><a href="#" class="itag">{size} </a></div> <div class="cp_button"><a href="#" class="itag"> {filename} </a></div> <div class="cp_button"><a href="#" class="itag">{plan_id} </a></div> <div class="cp_button"><a href="#" class="itag"> {data} </a></div> <div class="cp_button"><a href="#" class="itag"> {time}  </a></div><div class="cp_button"><a href="#" class="itag"> {plan_name} </a></div> </div>
</td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('email_subject')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[send_to_email_subject]" id="send_to_email_subject" value="<?php echo ((isset($settings['send_to_email_subject'])) ? $settings['send_to_email_subject'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr>
<td colspan="3"><label for="send_to_email_body"><?php echo lang('email_body')?></label>
<textarea name="<?php echo $input_prefix;?>[send_to_email_body]" id="send_to_email_body" rows="10"><?php echo ((isset($settings['send_to_email_body'])) ? $settings['send_to_email_body'] : '' );?></textarea>
</td>
</tr>
</tbody>
<?php endif; ?>





<tr class="header" id="notification">
<th colspan="3"><?php echo lang('notification_preferences')?></th>
</tr>
<tbody >
<tr style="width: 33%;">
<td><?php echo lang('email_address')?><div class="subtext"><?php echo lang('email_address_info')?></div></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[email_address]" id="email_address" value="<?php echo ((isset($settings['email_address'])) ? $settings['email_address'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr>
<td colspan="3"  class="alert info"><?php echo lang('email_tags')?>
<div style="padding:10px 0 0 0;"><div class="cp_button"><a href="#" class="itag">{size} </a></div> <div class="cp_button"><a href="#" class="itag"> {filename} </a></div> <div class="cp_button"><a href="#" class="itag">{plan_id} </a></div> <div class="cp_button"><a href="#" class="itag"> {data} </a></div> <div class="cp_button"><a href="#" class="itag"> {time}  </a></div><div class="cp_button"><a href="#" class="itag"> {plan_name} </a></div> </div>
</td>
</tr>
<tr style="width: 33%;">
<td><?php echo lang('email_subject')?></td>
<td><input dir="ltr" style="width: 100%;" name="<?php echo $input_prefix;?>[email_subject]" id="email_subject" value="<?php echo ((isset($settings['email_subject'])) ? $settings['email_subject'] : '' );?>" size="20" maxlength="256" class="input" type="text"></td>
</tr>
<tr>
<td colspan="3"><label for="email_body"><?php echo lang('email_body')?></label>
<textarea name="<?php echo $input_prefix;?>[email_body]" id="email_body" rows="10"><?php echo ((isset($settings['email_body'])) ? $settings['email_body'] : '' );?></textarea>
<?php echo form_checkbox($input_prefix.'[enable_template]', 'yes', ((isset($settings['enable_template'])) ? $settings['enable_template'] : '' ), 'id="enable_template_id"')?> <label for="files_id"><?php echo lang('enable_template')?></label>	<?php echo lang('enable_template_info')?>
</td>
</tr>
</tbody>

</table>


<p class="centerSubmit"><input name="edit_field_group_name" value="<?php echo lang('save_settings'); ?>" class="submit" type="submit"></p>


<?php echo form_close(); ?>
<?php


?>

</div>
	<script type="text/javascript">
		var c_obj = false;

		jQuery(function() {

			$("#f_method").change(function() {
				$("#archive_method").toggle();
			});

			$('a.itag').click(function()
			{

				if (c_obj) {
					tag_data = $(this).html();
					$("#" + c_obj).insertAtCaret(tag_data);
				}
				return false;
			});

			$('#email_subject, #email_body,  #send_to_email_subject, #send_to_email_body').click(function()
			{
				c_obj = $(this).attr("id");
			});

		});

	jQuery.fn.extend({
	insertAtCaret: function(myValue){
	  return this.each(function(i) {
		if (document.selection) {
		  this.focus();
		  sel = document.selection.createRange();
		  sel.text = myValue;
		  this.focus();
		}
		else if (this.selectionStart || this.selectionStart == '0') {
		  var startPos = this.selectionStart;
		  var endPos = this.selectionEnd;
		  var scrollTop = this.scrollTop;
		  this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
		  this.focus();
		  this.selectionStart = startPos + myValue.length;
		  this.selectionEnd = startPos + myValue.length;
		  this.scrollTop = scrollTop;
		} else {
		  this.value += myValue;
		  this.focus();
		}
	  })
	}
	});

	</script>