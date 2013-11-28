<?php
require_once PATH_THIRD .'mx_easy_backup/libraries/misc/markdown.php';
$docs = Markdown($docs);
?>

<?=form_open($_form_base."&method=help", array('name'=>'help', 'id'=>'help'), '')?>
<?=form_hidden('compatibility_test', 'ok')?>
<p class="centerSubmit"><input name="compatibility_test" value="<?= lang('compatibility_test'); ?>" class="submit" type="submit" >  <input name="help_request" value="<?= lang('help_request'); ?>" class="submit" type="button"  id="system_info" href="<?=BASE . AMP .($_form_base."&method=system_info")?>"></p>
<?=form_close();?>

<!-- <span class="button"><a  class="submit" id="system_info"><?=lang('help_request')?></a></span> -->

<div class="contents help" id="help-form" style="display:none;">

<?=form_open($_form_base."&method=help", array('name'=>'help_email', 'id'=>'help_email'), '')?>
<div id="communicate_info">
					<p>
						<label for="name"><?=lang('from')?></label> 
						<input type="text" name="help[from]" value="<?php echo ((isset($help['from'])) ? $help['from'] : '' );?>" id="from" class="fullfield"></p>
					<p>
						<label for="name"><?=lang('to')?></label> 
						<input type="text" name="help[to]" value="<?php echo ((isset($help['to'])) ? $help['to'] : '' );?>" id="to" class="fullfield"></p>
</div>

<div id="communicate_compose">
<p>
						<strong class="notice">*</strong> <label for="subject"><?=lang('subject')?></label> 
						<input type="text" name="help[subject]" value="<?php echo ((isset($help['subject'])) ? $help['subject'] : '' );?>" id="subject" class="fullfield">											</p>
						
<p style="margin-bottom:15px">
						<strong class="notice">*</strong> <label for="message"><?=lang('message')?></label><br>
												<textarea name="help[message]" cols="85" rows="20" id="message" class="fullfield"></textarea>					</p> 
<p style="margin-bottom:15px">
						<strong class="notice">*</strong> <label for="system_information"><?=lang('system_information')?></label><br>
												<textarea name="help[system_information]" cols="85" rows="20" id="system_information" class="fullfield"></textarea>					</p>  




						
<p class="centerSubmit"><input name="submit" value="<?= lang('send'); ?>" class="submit" type="submit"></p>
</div>
<?=form_close();?>
</div>

<?php
echo '<div class="help"><p>';
if (isset($compatibility_test)) {
if ($php_ok && $file_ok && $safe_mode)
{
	echo '<span class="go_notice">Your environment meets the minimum requirements for using the MX Easy Backup! </span><br/>' . PHP_EOL . PHP_EOL;
}
else
{
	if (!$php_ok) { echo '* PHP: You are running an unsupported version of PHP. <br/>' . PHP_EOL . PHP_EOL; }

	if (!$safe_mode) {echo 'SAFE_MODE: MX EasyBackup requires PHP safe mode to be turned off <br/>';}

	if (!$file_ok) { echo '* File System Read/Write: The file_get_contents() and/or file_put_contents() functions have been disabled. Without them, the MX Easy Backup cannot read from, or write to, the file system. <br/>' . PHP_EOL . PHP_EOL; }
}

echo 'System methods:' . PHP_EOL . PHP_EOL;
if (!$win && $exec && $tar && $mysql && $mysqldump && $gunzip) {echo "<b class='go_notice'>OK</b> <br/>";} else {echo "<span class='notice'>Disable, <i>please choose PHP method</i></span> <br/>";}
if ($win) {echo '* Windows: At this moment, this method is available only for the *nix system. <br/>' . PHP_EOL . PHP_EOL;  }
else {
	if (!$exec) { echo '* shell_exec: The shell_exec function is not available. You can\'t used System method. <br/>' . PHP_EOL . PHP_EOL; }
	else {
		if (!$tar) { echo '* tar: The tar is not available. You can\'t do File backup <br/>' . PHP_EOL . PHP_EOL; }
		if (!$mysql) { echo '* tar: The mysql is not available. You can\'t do DB restore. <br/>'. PHP_EOL . PHP_EOL; }
		if (!$mysqldump) { echo  '* tar: The mysqldump is not available. . You can\'t do DB backup. <br/>' . PHP_EOL . PHP_EOL; }
	}
}

echo 'Amazon S3: ' . PHP_EOL . PHP_EOL;
if ($curl_ok && $simplexml_ok && $spl_ok && $json_ok ) {echo "<b class='go_notice'>OK</b> <br/>";}else {echo "<span class='notice'>Disable</span> <br/>";}
if (!$curl_ok) { echo '* cURL: The cURL extension is not available. <br/>' . PHP_EOL . PHP_EOL; }
if (!$simplexml_ok) { echo '* SimpleXML: The SimpleXML extension is not available.<br/>' . PHP_EOL . PHP_EOL; }
if (!$spl_ok) { echo '* SPL: Standard PHP Library support is not available. <br/>' . PHP_EOL . PHP_EOL; }
if (!$json_ok) { echo '* JSON: JSON support is not available.<br/>' . PHP_EOL . PHP_EOL; }
if (!$pcre_ok) { echo '* PCRE: Your PHP installation doesn\'t support Perl-Compatible Regular Expressions (PCRE).<br/>' . PHP_EOL . PHP_EOL; }

echo 'DropBox:' . PHP_EOL . PHP_EOL; 
if ($curl_ok) {echo "<b class='go_notice'>OK</b> <br/>";} else {echo "<span class='notice'>Disable</span> <br/>";}

if (!$curl_ok) { echo '* cURL: The cURL extension is not available. <br/>' . PHP_EOL . PHP_EOL; }

echo 'Rackspace Cloud Files:' . PHP_EOL . PHP_EOL;
if ($curl_ok && $mbstring_ok) {echo "<b class='go_notice'>OK</b> <br/>";} else {echo "<span class='notice'>Disable</span> <br/>";}
if (!$curl_ok) { echo '* cURL: The cURL extension is not available. <br/>' . PHP_EOL . PHP_EOL; }
if (!$mbstring_ok) { echo '* mbstring: The mbstring extension is not available. <br/>' . PHP_EOL . PHP_EOL; }
echo '</p></div>';
}
?>

<div class="mor">
<div class="help">
<?php echo $docs?>
</div>
</div>
<script type="text/javascript">
		jQuery(function() {


			$('#system_info').click(function()
			{
			
	
		$.ajax({
 				 url: $(this).attr("href"),
  				 success: function(data){
  				 
  						$('#system_information').html(data);
  						$('#help-form').show();
  				    }
				});
				//alert ("asdasd");
				return false;	
			});
		});
</script>		
		
<style>
.help {background-color:#fff; color:#333333; padding: 10px; font-size: 13px;}
.help  ol {padding-left:35px;}
.help  ul {padding-left:15px;}
.help h1, .help h2 {padding:10px 0px;}
.help h3, .help h4 {padding:5px 0 5px 5px;}
.help p{ padding: 0 0 10px 15px;}
.help code {overflow-x:hide;}
	#umenu span.current a, #umenu span a:hover {
	    color: #fff;
	    font-weight: bold;
	    background: #27343C;
	    text-shadow: 0 -1px 0 rgba(0,0,0,0.2);
	    box-shadow: rgba(0,0,0,0.2) 0 1px 0 0 inset;
	}

	#umenu span a {
	    background: #ABB7C3;
	    color: #fff;
	    font-size: 11px;
	    font-weight: bold;
	    margin: 3px 7px 0 0;
	    padding: 5px 10px 5px 10px;
	    display: block;
	    text-decoration: none;
	    text-transform: uppercase;
	    border-radius: 5px;
	}
</style>