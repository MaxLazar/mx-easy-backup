<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once PATH_THIRD . 'mx_easy_backup/config.php';

/**
 *
 * @package  MX Easy Backup
 * @subpackage ThirdParty
 * @category Modules
 * @author    Max Lazar <max@eec.ms>
 * @copyright Copyright (c) 2013 Max Lazar (http://eec.ms)
 * @link  http://eec.ms/
 */

function assignError($errno, $errstr, $errfile, $errline)
{
	throw new Exception($errstr);
}


class Mx_easy_backup_mcp
{
	var $base; // the base url for this module
	var $form_base; // base url for forms
	var $module_name = MX_EASY_BACKUP_KEY;
	var $pro_mode = true;
	var $settings = array();
	var $CP = true;
	var $backup_filename = '';
	var $dir_tree = array();
	var $target_dir = '';
	var $upload_list = array();
	var $file_size = array();
	var $errors = array('aws_errors' => false, 'rackspace_errors' => false, 'ftp_errors' => false, 's_errors' => false, 'other_errors' => false, 'message_failure' => false, 'message_success' => false);
	var $time_web = 0;
	var $method = 'none';
	var $mysqldump_comm = "mysqldump";
	var $email_data = array();
	var $pack = array("gzip" => array("com" => "gzip -9c", "ext" => ".gz"), "bzip2" => array("com" => "bzip2 -c", "ext" => ".bz2") /*,
    "lzma" => array (
    "com" => "lzma",
    "ext" => ".lzma"
    )*/ );
	var $data_file = "";


	function Mx_easy_backup_mcp($switch = TRUE)
	{
		$this->EE =& get_instance();

		$this->EE->_ci_view_path = PATH_THIRD . 'mx_easy_backup/libraries/';

		$this->EE->load->library('misc/mx_common');

		if (defined('BASE') == FALSE) {
			define('BASE', '');
			$this->CP = false;
		}

		if (defined('URL_THEMES') == FALSE and defined('BASE') != FALSE) {
			$this->cp_theme = (!$this->EE->session->userdata('cp_theme')) ? $this->EE->config->item('cp_theme') : $this->EE->session->userdata('cp_theme');
			define('URL_THEMES', $this->EE->config->slash_item('theme_folder_url') . 'cp_themes/' . $this->cp_theme . '/images/');
		}
		;

		if (defined('SITE_ID') == FALSE)
			define('SITE_ID', $this->EE->config->item('site_id'));

		$this->base      = BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=' . $this->module_name;
		$this->form_base = 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=' . $this->module_name;

		
		if ($this->CP) {
			$this->EE->cp->set_right_nav(array(
					$this->EE->lang->line('tasks') => $this->base,
					$this->EE->lang->line('backup_file') => $this->base . AMP . 'method=backup_files',
					$this->EE->lang->line('settings') => $this->base . AMP . 'method=settings',
					$this->EE->lang->line('help') => $this->base . AMP . 'method=help'
				));


		}

		$this->settings = $this->crypted_fields($this->EE->mx_common->getSettings(), 2);
	}

		function system_info()
	{
		$this->EE->load->library('misc/system_info');

		die($this->EE->system_info->compatibility_test());
	}

	function help()
	{


		if ($help = $this->EE->input->post('help')) {
			if ($this->email_send($help['subject'], $help['message']."\n".$help['system_information'], array(), $help['to'], $help['from'], '')) {

				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('help_request_success'));
			} else {
				$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('help_request_faild'));
			}

			if ($this->CP) {
				$this->EE->functions->redirect($this->base . AMP . 'method=help');
			} else {
				return true;
			}
		}


		$vars = array(
			'addon_name' => $this->module_name,
			'error' => FALSE,
			'input_prefix' => __CLASS__,
			'message' => FALSE,
			'settings_form' => FALSE,
			'aws_errors' => FALSE,
			'rackspace_errors' => FALSE,
			'buckets_list' => '',
			'containers_list' => ''
		);
		$this->EE->load->helper('file');
		$string = read_file(PATH_THIRD . 'mx_easy_backup/_docs/README.md');

		$this->EE->load->library('typography');
		$this->EE->typography->initialize();
		$this->EE->typography->parse_smileys = FALSE;
		$this->EE->typography->popup_links   = TRUE;

		$text_format = (in_array('markdown', $this->EE->typography->text_fmt_plugins)) ? 'markdown' : 'xhtml';




		$vars['docs'] = $this->EE->typography->parse_type($string, array(
				'text_format' => 'none',
				'html_format' => 'all',
				'auto_links' => 'n',
				'allow_img_url' => 'n'
			));

		$vars['help'] = array(
			'subject' => $this->module_name . ' issue :',
			'to' => 'service@eec.ms',
			'from' => $this->EE->config->item('webmaster_email')
		);

		if ($this->EE->input->post('compatibility_test')) {
			$vars = array_merge($vars, (array) $this->compatibility_test());
			$vars['compatibility_test'] = true;
		}
		;

		return $this->content_wrapper('help', $this->EE->lang->line('help'), $vars);

	}

	function settings()
	{
		$vars = array();

		$vars = array(
			'addon_name' => $this->module_name,
			'error' => FALSE,
			'input_prefix' => __CLASS__,
			'message' => FALSE,
			'settings_form' => FALSE,
			'aws_errors' => FALSE,
			'rackspace_errors' => FALSE,
			'buckets_list' => '',
			'containers_list' => ''
		);

		if ($new_settings = $this->EE->input->post(__CLASS__)) {
			if (isset($new_settings['create_s3_bucket'])) {
				if (trim($new_settings['create_s3_bucket']) != "") {
					$this->EE->load->library("s3", $this->settings);

					/*@@ test @@ */
					// Create bucket func();

					/*@@ test @@ */

					if ($this->EE->s3->create_bucket($new_settings['create_s3_bucket'])) {
						$new_settings['create_s3_bucket']  = "";
						$new_settings['aws_refresh']       = "ok";
						$this->errors['message_success'][] = $this->EE->lang->line();
					} else {
						die("Error: create_s3_bucket;");
					}

				}
			}


			$this->EE->mx_common->saveSettingsToDB($this->crypted_fields($new_settings, 1));
			$vars['message'] = $this->EE->lang->line('settings_saved_success');
			$this->settings  = $new_settings;
		}

		if ($this->EE->input->post('encryption_key')) {
			$this->EE->config->_update_config(array(
					'encryption_key' => $this->EE->input->post('encryption_key', TRUE)
				));
			$this->EE->functions->redirect($this->base . AMP . 'method=settings');
		}

		$vars['settings'] = $this->settings;

		$vars['methods'] = array(
			'php' => $this->EE->lang->line('php'),
			'system' => $this->EE->lang->line('system')
		);

		if (!empty($errors)) {
			$vars['message'] = $this->EE->lang->line('problems');
		}

		$vars['errors'] = (isset($errors)) ? $errors : false;

		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		$this->EE->load->model('tools_model');

		$this->EE->jquery->tablesorter('.mainTable', '{
			headers: {
			0: {sorter: false},
			2: {sorter: false}
		},
			widgets: ["zebra"]
		}');

		$this->EE->javascript->output('
									$(".toggle_all").toggle(
										function(){
											$("input.toggle").each(function() {
												this.checked = true;
											});
										}, function (){
											var checked_status = this.checked;
											$("input.toggle").each(function() {
												this.checked = false;
											});
										}
									);');

		$this->EE->javascript->compile();

		$vars['sql_table']      = $this->EE->tools_model->get_table_status();
		$vars['encryption_key'] = (!$this->EE->config->item('encryption_key')) ? false : true;
		$vars['buckets']        = false;
		if ($vars['encryption_key']) {
			if (!empty($this->settings['aws_access_key']) and !empty($this->settings['aws_secret_key']) and (isset($this->settings['aws_refresh']) or empty($this->settings['buckets_list']))) {
				$this->EE->load->library("s3", $this->settings);
				$vars['buckets']      = $this->EE->s3->bucket_list();
				$vars['buckets_list'] = ($vars['buckets']) ? implode(';', $vars['buckets']) : '';

			} elseif (!empty($this->settings['buckets_list'])) {
				$vars['buckets'] = $this->array_to_select(explode(';', $this->settings['buckets_list']));
			}
		}

		$vars['settings']['aws_refresh']       = null;
		$vars['settings']['rackspace_refresh'] = null;
		$vars['first_start']                   = (!isset($this->settings['local_path'])) ? false : true;

		foreach ($this->pack as $arch => $arch_settings) {
			$vars['archive_methods'][$arch] = $arch;
		}

		$vars['win']      = (stristr(PHP_OS, 'WIN'));
		$vars['pro_mode'] = $this->pro_mode;


		if ($notice = $this->errors_generator($this->errors)) {
			$this->EE->javascript->output('$.ee_notice([' . $notice . ']);');
		}

		return $this->content_wrapper('settings', $this->EE->lang->line('Settings'), $vars);
	}


	function crypted_fields($obj, $way = "1")
	{
		$encrypted_fields = array(
			"sftp_password",
			"sftp_username",
			"password",
			"username",
			"aws_secret_key",
			"aws_access_key"
		);

		if ($this->EE->config->item('encryption_key')) {
			$this->EE->load->library('encrypt');

			foreach ($obj as $key => $val) {
				if (in_array($key, $encrypted_fields))
					$obj[$key] = ($way == 1) ? $this->EE->encrypt->encode($val) : $this->EE->encrypt->decode($val);

			}
		}
		return $obj;
	}


	function compatibility_test()
	{
		$vars             = array();
		$vars['win']      = (stristr(PHP_OS, 'WIN'));
		$vars['pro_mode'] = $this->pro_mode;

		$vars['safe_mode']   = (ini_get('safe_mode')) ? false : true;
		$vars['exec']        = (function_exists('shell_exec')) ? true : false;
		$vars['curl_ok']     = ($this->_iscurlinstalled()) ? true : false;
		$vars['mbstring_ok'] = ($this->_ismbstringinstalled()) ? true : false; //RC

		$out = "";
		if ($vars['exec'] && $vars['safe_mode']) {
			exec('command -v tar &>/dev/null && echo "OK" || echo "NOT OK"', $out);
			$vars['tar'] = ($out[0] == "OK") ? true : false;
			exec('command -v mysql &>/dev/null && echo "OK" || echo "NOT OK"', $out);
			$vars['mysql'] = ($out[0] == "OK") ? true : false;
			exec('command -v mysqldump &>/dev/null && echo "OK" || echo "NOT OK"', $out);
			$vars['mysqldump'] = ($out[0] == "OK") ? true : false;
			exec('command -v gunzip &>/dev/null && echo "OK" || echo "NOT OK"', $out);
			$vars['gunzip'] = ($out[0] == "OK") ? true : false;
		}

		// Required
		$vars['php_ok']       = (function_exists('version_compare') && version_compare(phpversion(), '5.2.0', '>=')); // php version
		$vars['simplexml_ok'] = extension_loaded('simplexml'); // Amazon S3
		$vars['json_ok']      = (extension_loaded('json') && function_exists('json_encode') && function_exists('json_decode')); //json
		$vars['spl_ok']       = extension_loaded('spl');
		$vars['file_ok']      = (function_exists('file_get_contents') && function_exists('file_put_contents'));
		$vars['pcre_ok']      = extension_loaded('pcre');

		return ($vars);
	}


	function array_to_select($list)
	{
		$out = array();
		foreach ($list as $val) {
			$out[$val] = $val;
		}
		return $out;
	}

	function cleanup_backups($method, $max_size)
	{
		$ts_size     = 0;
		$backup_list = $this->EE->db->where('method', $method)->order_by('backup_id', 'ASC')->get('mx_easy_backup');

		if ($backup_list->num_rows()) {
			foreach ($backup_list->result() as $row) {
				$ts_size = $ts_size + $row->size;
			}

			if (round($ts_size / 1048576, 1) > $max_size) {
				foreach ($backup_list->result() as $row) {
					$this->delete_backup($row->backup_id, false);

					$ts_size = $ts_size - $row->size;

					if (round($ts_size / 1048576, 1) < $max_size) {
						return true;

					}

				}

			}

		}

		return true;
	}

	function delete_task()
	{
		$task_id = ($this->EE->input->get('task_id') != '') ? $this->EE->input->get('task_id') : false;

		if ($task_id) {
			$this->EE->db->delete('mx_easy_backup_tasks', array(
					'task_id' => $task_id
				));
			$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('task_delete_success'));
		}

		$this->EE->functions->redirect($this->base);
	}

	/* New Task */
	function task($task_settings = false)
	{
		$vars = array();

		$task_id = ($this->EE->input->get('task_id') != '') ? $this->EE->input->get('task_id') : false;

		$vars = array(
			'addon_name' => $this->module_name,
			'error' => FALSE,
			'input_prefix' => __CLASS__,
			'message' => FALSE,
			'settings_form' => FALSE,
			'aws_errors' => FALSE,
			'task_id' => $task_id,
			'task_name' => 'backup_' . rand(1, 10000)

		);

		$vars['send_to_list'] = array(
			'none' => $this->EE->lang->line('none')
		);

		if (!empty($this->settings['aws_access_key']) and !empty($this->settings['aws_secret_key'])) {
			$vars['send_to_list']['s3'] = $this->EE->lang->line('aws');
		}
		if (!empty($this->settings['username']) and !empty($this->settings['password']) and !empty($this->settings['host'])) {
			$vars['send_to_list']['ftp_backup'] = $this->EE->lang->line('ftp');
		}

		if (!empty($this->settings['send_to_email_address'])) {
			$vars['send_to_list']['email_backup'] = $this->EE->lang->line('send_to_email');
		}

		if (!empty($this->settings['sftp_username']) and !empty($this->settings['sftp_password']) and !empty($this->settings['sftp_host'])) {
			$vars['send_to_list']['sftp'] = $this->EE->lang->line('sftp');
		}
		$vars['system_mode'] = ($this->settings['method'] == 'php') ? false : true;

		$act_query = $this->EE->db->select('action_id')->where('method', 'start_backup')->where('class', 'Mx_easy_backup')->get('actions')->row('action_id');

		if ($new_settings = $this->EE->input->post(__CLASS__)) {
			$vars['settings'] = $new_settings;
			$this->EE->mx_common->saveTaskToDB($new_settings, $task_id);
			$vars['message'] = $this->EE->lang->line('settings_saved_success');
			$this->EE->functions->redirect($this->base);
		}

		$vars['backup_types'] = array(
			'full' => $this->EE->lang->line('full'),
			'differential' => $this->EE->lang->line('differential')

		);

		$vars['settings'] = $this->EE->mx_common->getTask(TRUE, $task_id);

		if (!empty($errors)) {
			$vars['message'] = $this->EE->lang->line('problems');
		}

		if (!$task_id) {
			$vars['settings']['local_path'] = (isset($this->settings['local_path'])) ? $this->settings['local_path'] : '';
		}

		$vars['errors'] = (isset($errors)) ? $errors : false;

		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		$this->EE->load->model('tools_model');

		$this->EE->jquery->tablesorter('.mainTable', '{
			headers: {
			0: {sorter: false},
			2: {sorter: false}
		},
			widgets: ["zebra"]
		}');

		$this->EE->javascript->output('

									$(".c_toogle").click(function () {
										$("#" + $(this).attr("rel")).toggle();
									});

									$(".toggle_all").toggle(
										function(){
											$("input.toggle").each(function() {
												this.checked = true;
											});
										}, function (){
											var checked_status = this.checked;
											$("input.toggle").each(function() {
												this.checked = false;
											});
										}
									);');

		$this->EE->javascript->compile();

		if (!$task_id) {
			$vars['settings']['uniqid'] = uniqid();
		}

		if (!$task_id or !isset($vars['settings']['uniqid'])) {
			$vars['settings']['uniqid'] = uniqid();
		}
		$vars['url'] = $this->EE->config->item('site_url') . "?ACT=" . $act_query . AMP . 'task_id=' . $task_id . AMP . 'key=' . $vars['settings']['uniqid'];

		$vars['sql_table'] = $this->EE->tools_model->get_table_status();

		if ($notice = $this->errors_generator($this->errors)) {
			$this->EE->javascript->output('$.ee_notice([' . $notice . ']);');
		}

		return $this->content_wrapper('tasks', $this->EE->lang->line('task_settings'), $vars);
	}


	function make_backup($task_id = false, $CP = true)
	{
		$errors = false;

		$task_id = ($task_id) ? $task_id : $this->EE->input->get('task_id');

		$task_settings = $this->EE->mx_common->getTask(TRUE, $task_id);

		$task_settings['task_id'] = $task_id;

		if (!ini_get('safe_mode') && function_exists('set_time_limit') && strpos(ini_get('disable_functions'), 'set_time_limit') === false)
			@set_time_limit($this->time_web);

		$this->settings['date_fmt'] = ($this->EE->session->userdata('time_format') != '') ? $this->EE->session->userdata('time_format') : $this->EE->config->item('time_format');

		$backup_filename = 'backup_' . date('y_m_d-his');

		$timestamp = $this->EE->localize->now;

		$this->target_dir = (trim($task_settings['local_path']) != "") ? reduce_double_slashes(trim($task_settings['local_path']) . '/') : ((trim($task_settings['local_path']) != "") ? $task_settings['local_path'] : sys_get_temp_dir());

		//Back up DB start
		if (isset($task_settings['db_backup'])) {
			$this->EE->benchmark->mark('db_backup_start');

			if ($bkname = $this->db_backup($this->target_dir, $backup_filename . '.sql', ((isset($task_settings['c_tables'])) ? ((isset($task_settings['db'])) ? $task_settings['db'] : false) : false), false, $this->settings['method'])) {
				$this->EE->benchmark->mark('db_backup_end');
				$this->upload_list[$bkname]['time']      = $this->EE->benchmark->elapsed_time('db_backup_start', 'db_backup_end');
				$this->upload_list[$bkname]['filesize']  = filesize($this->target_dir . $bkname);
				$this->upload_list[$bkname]['plan_name'] = $task_settings['task_name'];
				$this->upload_list[$bkname]['plan_id']   = $task_id;
				$this->upload_list[$bkname]['type']      = "db";
			}

		}
		//Back up DB end



		//Back up files begin
		$this->EE->benchmark->mark('file_backup_start');
		if ($bkname = $this->file_backup($this->target_dir, $backup_filename . '.file', $task_settings, $this->settings['method'])) {
			$this->EE->benchmark->mark('file_backup_end');
			$this->upload_list[$bkname]['filesize']  = filesize(reduce_double_slashes($this->target_dir . '/' . $bkname));
			$this->upload_list[$bkname]['time']      = $this->EE->benchmark->elapsed_time('file_backup_start', 'file_backup_end');
			$this->upload_list[$bkname]['plan_name'] = $task_settings['task_name'];
			$this->upload_list[$bkname]['plan_id']   = $task_id;
			$this->upload_list[$bkname]['type']      = "files";

		}
		//Back up files end


		$data = array(
			'backup_id' => '',
			'site_id' => SITE_ID,
			'task_id' => $task_id,
			'backup_name' => '',
			'method' => $task_settings['send_to'],
			'date' => $timestamp,
			'size' => '',
			'time' => ''
		);


		$send_to_funct = $task_settings['send_to'];

		$this->cleanup_backups($send_to_funct, ((!isset($this->settings[$send_to_funct . '_space'])) ? 9999999 : (($this->settings[$send_to_funct . '_space'] == 0) ? 9999999 : $this->settings[$send_to_funct . '_space'])));

		$this->target_dir = (trim($task_settings['local_path']) != "") ? reduce_double_slashes(trim($task_settings['local_path']) . '/') : ((trim($task_settings['local_path']) != "") ? $task_settings['local_path'] : sys_get_temp_dir());

		$this->EE->load->library($send_to_funct, $this->settings);

		$this->upload_list = $this->EE->$send_to_funct->upload($this->upload_list, $this->target_dir);

		$this->EE->load->library('logger');

		if (!empty($this->upload_list)) {
			foreach ($this->upload_list as $filename => $val) {
				$data['time']        = $val['time'];
				$data['backup_name'] = $filename;
				$data['size']        = $val['filesize'];
				$data['type']        = $val['type'];
				$this->EE->db->insert('exp_mx_easy_backup', $data);

			}
			;
			// $this->insert2db($this->upload_list);

			$this->EE->db->where('task_id ', $task_id)->update('mx_easy_backup_tasks', array(
					'last_run' => $timestamp
				));

			$this->EE->logger->log_action('MX Easy BackUp: ' . $this->EE->lang->line('backup_success'));

		} else {
			$this->EE->load->library('logger');
			$this->EE->logger->log_action('MX Easy BackUp: ' . $this->EE->lang->line('backup_faild'));
		}


		if ($task_settings['send_to'] != 'none') {
			foreach ($this->upload_list as $filename => $filesize) {
				unlink($this->target_dir . $filename);
			}
		}

		if (trim($this->settings['email_address']) != '' && $send_to_funct != "email_backup" && $task_settings['send_notification']) {
			$email_time     = "";
			$email_size     = 0;
			$email_filename = "";

			foreach ($this->upload_list as $filename => $val) {
				$email_time     = $email_time + $val['time'];
				//      ($val['time']> $email_time) ? $val['time'] : $email_time;
				$email_filename = $email_filename . $filename . ' ';
				$email_size     = $email_size + $val['filesize'];
			}
			;

			$this->email_data = array(
				"size" => $this->EE->mx_common->format_size($email_size),
				"filename" => $email_filename,
				"plan_id" => $task_id,
				"data" => $this->EE->localize->decode_date((($this->settings['date_fmt'] == 'us') ? '%m/%d/%y %h:%i %a' : '%Y-%m-%d %H:%i'), $timestamp, TRUE),
				"time" => $email_time,
				"plan_name" => $task_settings['task_name']
			);
			$this->email_send(((isset($this->settings['enable_template'])) ? $this->settings['email_subject'] : $this->EE->lang->line('email_subject_template')), ((isset($this->settings['enable_template'])) ? $this->settings['email_body'] : $this->EE->lang->line('email_body_template')), $this->email_data, $this->settings['email_address'], $this->EE->config->item('webmaster_email'), $this->EE->config->item('site_name'));

		}
		;

		if ($this->dowehaveerrors($this->errors)) {
			$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('backup_success'));
		} else {
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('backup_faild'));
		}

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER ['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
			die("OK");
		}

		if ($CP) {
			$this->EE->functions->redirect($this->base . AMP . 'method=backup_files');
		} else {
			return true;
		}
	}

	function dowehaveerrors($obj)
	{
		foreach ($obj as $filename => $val) {
			if ($val != "") {
				return false;
			}

		}
		return true;
	}

	function insert2db($obj)
	{
		foreach ($obj as $filename => $val) {
			$data['time']        = $val['time'];
			$data['backup_name'] = $filename;
			$data['size']        = $val['filesize'];
			$data['type']        = $val['type'];
			$this->EE->db->insert('exp_mx_easy_backup', $data);

		}
		;
	}

	function delete_backup($backup_id = false, $redirect = true)
	{
		$errors     = false;
		$path_array = array();

		$backup_id = ($backup_id) ? $backup_id : $this->EE->input->get('backup_id');

		$query = $this->EE->db->where('backup_id', $backup_id)->get('mx_easy_backup', 1);

		if ($query->num_rows()) {
			$task_settings = $this->EE->mx_common->getTask(TRUE, $query->row()->task_id);


			$this->target_dir = (trim($task_settings['local_path']) != "") ? trim($task_settings['local_path']) : ((trim($task_settings['local_path']) != "") ? $task_settings['local_path'] : sys_get_temp_dir());


			$send_to_funct = $query->row()->method;
			$this->EE->load->library($send_to_funct, $this->settings);

			$this->EE->$send_to_funct->delete(array(
					$query->row()->backup_name
				), reduce_double_slashes($this->target_dir . '/'));


			$this->EE->db->delete('mx_easy_backup', array(
					'backup_id' => $backup_id
				));


		}

		if ($redirect) {
			if (!$errors) {
				$this->errors['message_success'][] = $this->EE->lang->line('delete_backup_success');
			} else {
				$this->errors['message_failure'][] = $errors;
			}

			$this->EE->functions->redirect($this->base . '&method=backup_files');
		} else {
			return $errors;
		}

		return true;
	}

	function download()
	{
		$errors    = false;
		$backup_id = $this->EE->input->get('backup_id');

		$backup_name = $this->download_func($backup_id);

		if ($backup_name) {
			$this->EE->load->helper('download');
			force_download($backup_name, $this->data_file);
		} else {
			die("Download error");
		}


	}

	function download_func($backup_id, $direct = true)
	{
		$query = $this->EE->db->where('backup_id', $backup_id)->get('mx_easy_backup', 1);

		if ($query->num_rows()) {
			$task_settings = $this->EE->mx_common->getTask(TRUE, $query->row()->task_id);
			$backup_name   = $query->row()->backup_name;

			$this->target_dir = ($query->row()->method == 'none') ? reduce_double_slashes($this->settings['local_path'] . '/') : reduce_double_slashes($this->settings['local_path'] . '/' . 'temp/');

			if (!@is_dir($this->target_dir)) {
				if (!@mkdir($this->target_dir, 0777)) {
					die('Temp directory does not exist');
				}

				@chmod($this->target_dir, 0777);
			}

			$send_to_funct = $query->row()->method;
			$this->method  = $send_to_funct;

			$this->EE->load->library($send_to_funct, $this->settings);

			$this->data_file = $this->EE->$send_to_funct->download($query->row()->backup_name, $this->target_dir, $direct);

			if (!$direct && !file_exists($this->target_dir . $backup_name)) {
				die("File not exist");
			}

			return $backup_name;

		} else {
			die('Backup is empty');
		}
		return false;
	}

	function restore_files()
	{
		$errors    = false;
		$backup_id = $this->EE->input->get('backup_id');

		$backup_name = $this->download_func($backup_id, false);

		if ($backup_name) {
			if (!file_exists($this->target_dir . $backup_name)) {
				die("File not exist");
			}

			if ($this->settings['method'] == 'php') {
				$errors = $this->restore_files_php($this->target_dir . $backup_name);
			} else {
				$errors = $this->restore_files_system($this->target_dir . $backup_name);
			}

			if ($errors) {
				if ($this->method != 'none') {
					unlink($this->target_dir . $filename);
				}

				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('backup_restore_success'));
			} else {
				$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('backup_restore_faild'));
			}

			if ($this->CP) {
				$this->EE->functions->redirect($this->base . AMP . 'method=backup_files');
			} else {
				return true;
			}

		}

	}

	private function restore_files_php($backup)
	{
		$zip = new PclZip($backup);

		if ($zip->extract(PCLZIP_OPT_PATH, '/') == 0) {
			return false;
		} else {
			return true;
		}

	}

	private function restore_files_system($backup)
	{
		if ((bool)shell_exec("tar -xf $backup -C / && echo true")) {
			return true;
		}
		else {
			die("Extract error");
		}

	}

	public function restore_db()
	{
		$errors      = false;
		$backup_id   = $this->EE->input->get('backup_id');
		$backup_name = $this->download_func($backup_id, false);
		//die($this->target_dir.$backup_name);
		if ($backup_name) {
			if (!file_exists($this->target_dir . $backup_name)) {
				die("File not exist: " . $this->target_dir . $backup_name);
			}

			if ($this->settings['method'] == 'php') {
				$errors = $this->restore_db_php($this->target_dir . $backup_name);
			} else {
				$errors = $this->restore_db_system($this->target_dir . $backup_name);
			}



		} else {
			die('Download problem');
		}

		if ($errors) {
			if ($this->method != 'none') {
				unlink($this->target_dir . $filename);
			}
			$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('backup_restore_success'));
		} else {
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('backup_restore_faild'));
		}

		if ($this->CP) {
			$this->EE->functions->redirect($this->base . AMP . 'method=backup_files');
		} else {
			return true;
		}
	}

	private function restore_db_php($backup_name)
	{
		$hostname = $this->EE->db->hostname;
		$username = $this->EE->db->username;
		$password = $this->EE->db->password;
		$database = $this->EE->db->database;
		$port     = $this->EE->db->port;

		$dumper = new Dumper_Decorator($hostname, $username, $password, $port);

		$dumper->setTmpDir($this->target_dir)->restore($database, $backup_name, null, 7);

		return true;
	}

	private function restore_db_system($backup_name)
	{
		$hostname = $this->EE->db->hostname;
		$username = $this->EE->db->username;
		$password = $this->EE->db->password;
		$database = $this->EE->db->database;
		$port     = $this->EE->db->port;

		$command = "gunzip < $backup_name | mysql -u $username -p$password -h $hostname $database";
		$result  = shell_exec($command);
		if ($result != "") {
			die("System Restore error :" . $result);
		}
		/*
        if(preg_match('/^[^\/]+?\.sql(\.(gz|bz2|tgz))?$/', $backup_name)) {
        $command = "gunzip < $this->target_dir$backup_name | mysql -u $username -p$password $hostname $database";

        }
        else {
        //die('restore_db_system: file error :'.$backup_name);
        }
        */
		return true;
	}

	function email_send($subject, $body, $data, $email, $from, $site)
	{
		$subject = $this->EE->functions->var_swap($subject, $data);
		$body    = $this->EE->functions->var_swap($body, $data);

		$this->EE->load->helper('text');
		$this->EE->load->library('email');

		$this->EE->email->wordwrap = true;
		$this->EE->email->mailtype = 'text';

		$this->EE->email->from($from, $site);


		$this->EE->email->to($email);
		$this->EE->email->subject($subject);
		$this->EE->email->message($body);

		if (!$this->EE->email->Send()) {
			return false;
		}

		return true;
	}

	function db_backup($path, $bk_filename, $db_tables, $direc = false, $method = 'php', $archive_method = 'gzip')
	{
		$return = false;

		$hostname = $this->EE->db->hostname;
		$username = $this->EE->db->username;
		$password = $this->EE->db->password;
		$database = $this->EE->db->database;
		$database = $this->EE->db->database;
		$dbprefix  = $this->EE->db->dbprefix;
		
		$port     = $this->EE->db->port;

		if ($method == 'php') {
			$bk_filename = $bk_filename . '.zip';

			$tables_e = null;
			
		//	$tables_e[$dbprefix."mx_easy_backup"] = $dbprefix."mx_easy_backup";
		//	$tables_e[$dbprefix."mx_easy_backup_tasks"] = $dbprefix."mx_easy_backup_tasks";
			
			if ($db_tables) {
				$tables_e = array_merge($db_tables, $tables_e);
			}
		
			$dumper = new Dumper_Decorator($hostname, $username, $password, $port);
	
			$dumper->setTmpDir($path)->setOutputFile($bk_filename)->backup($database, $tables_e, 'utf8', 7);

			$return = true;

		} else {
			$tables = '';

			if ($db_tables) {
				$tables = '--tables';
				foreach ($db_tables as $table => $name) {
					$tables = $tables . ' ' . $table;
				}
			}

			$bk_filename = $bk_filename . $this->pack['gzip']["ext"];

			$command = $this->mysqldump_comm . " --opt --add-drop-table -h$hostname -u$username -p$password  $database  $tables | " . $this->pack['gzip']["com"] . " > " . $path . $bk_filename;

			shell_exec($command);

			if (file_exists($path . $bk_filename)) {
				$return = true;
			} else {
				if (filesize($path . $bk_filename) < 10000) {
					die($this->EE->lang->line('system_method_error'));
				}
			}
		}
		return ($return) ? $bk_filename : false;
	}

	function file_backup($path, $bk_filename, $task_settings, $method = 'php', $archive_method = "gzip")
	{
		$return        = false;
		$file_list     = array();
		$dir_list      = array();
		$archive_list  = '';
		$exclude_files = '';
		$exclude       = '';

		if (isset($task_settings['config_files'])) {
			$dir_list[] = reduce_double_slashes(APPPATH . '/config/');
			/*$file_list[] = $this->EE->functions->remove_double_slashes(APPPATH . '/config/config.php');
            $file_list[] = $this->EE->functions->remove_double_slashes(APPPATH . '/config/database.php');*/
		}

		if (isset($task_settings['themes_folder'])) {
			$dir_list[] = PATH_THEMES;
		}

		if (isset($task_settings['addons_folder'])) {
			$dir_list[] = PATH_THIRD;
		}

		if (isset($task_settings['templates_folder'])) {
			$dir_list[] = reduce_double_slashes(APPPATH . 'templates/');
		}

		if (isset($task_settings['language_folder'])) {
			$dir_list[] = reduce_double_slashes(APPPATH . '/language/');
		}

		if (isset($task_settings['optional_files'])) {
			$optional_files = explode("\n", $task_settings['optional_files_list']);

			foreach ($optional_files as $val) {
				if (trim($val) != "") {
					if (is_dir($val)) {
						$dir_list[] = reduce_double_slashes($val . '\\');
					} else {
						$file_list[] = $val;
					}
				}
			}

			if (!empty($task_settings['exclude_files_list'])) {
				$exclude_files = explode("\n", $task_settings['exclude_files_list']);
				$exclude_dirs  = "";
				$exclude_files = "";
				foreach ($exclude_files as $val) {
					if (trim($val) != "") {
						if (is_dir($val)) {
							$exclude_dirs = $exclude . ' ' . reduce_double_slashes($val . '/');
						} else {
							$exclude_files = $exclude . ' ' . $val;
						}
					}
				}


				$exclude = "--exclude '" . trim($exclude_dirs . ' ' . $exclude_files) . "'";

			}

		}

		if ($method == 'php') {
			if (!defined('PCLZIP_TEMPORARY_DIR')) {
				define('PCLZIP_TEMPORARY_DIR', reduce_double_slashes($path . '/'));
			}
			;

			$archive = new PclZip(reduce_double_slashes($path . '/' . $bk_filename . '.zip'));

			$files_to_add = array_merge($file_list, $dir_list);

			if (count($files_to_add) != 0) {
				$bk_filename = $bk_filename . '.zip';

				$v_list = $archive->create($files_to_add, PCLZIP_OPT_ADD_TEMP_FILE_ON);
				if ($v_list == 0) {
					die("Error : " . $archive->errorInfo(true));
				}
				$return = true;
			}

		} else {
			if ($file_list) {
				foreach ($file_list as $val) {
					$archive_list = $archive_list . ' ' . $val;
				}
				$return = true;
			}

			if ($dir_list) {
				foreach ($dir_list as $val) {
					$archive_list = $archive_list . ' ' . $val;
				}
				$return = true;
			}



			if ($return) {
				$query = false;
				if ($task_settings['backup_type'] == 'differential') {
					$query = $this->EE->db->where('task_id', $task_settings['task_id'])->get('mx_easy_backup_tasks', 1);
					if (!$query->num_rows())
						$query = false;
				}

				$newer_mtime = (!$query) ? '' : "--newer-mtime '" . date(DATE_RFC2822, $query->row()->last_run) . "'";

				$bk_filename = $bk_filename . '.tgz';

				$command = "tar -cpzf $path$bk_filename $archive_list $exclude $newer_mtime";

				$out = shell_exec($command);

				if (file_exists($path . $bk_filename)) {
					$return = true;
				} else {
					$this->errors['message_failure'][] = $this->EE->lang->line('system_method_error');
				}
			}

		}

		return ($return) ? $bk_filename : false;
	}

	function _iscurlinstalled()
	{
		if (in_array('curl', get_loaded_extensions())) {
			return true;
		} else {
			return false;
		}
	}

	function _ismbstringinstalled()
	{
		if (in_array('mbstring', get_loaded_extensions())) {
			return true;
		} else {
			return false;
		}
	}


	function backup_files()
	{
		$vars   = array();
		$errors = false;

		if ($this->EE->input->post('toggle')) {
			foreach ($_POST['toggle'] as $key => $val) {
				$errors[] = $this->delete_backup($val, false);
			}
		}

		$vars = array(
			'addon_name' => $this->module_name,
			'error' => FALSE,
			'input_prefix' => __CLASS__,
			'message' => FALSE,
			'settings_form' => FALSE,
			'export_out' => false,
			'task_id' => ($this->EE->input->get_post('task')) ? $this->EE->input->get_post('task') : false,
			'backup_list' => $this->EE->db->where('site_id', SITE_ID)->order_by('backup_id', 'desc')->get('mx_easy_backup'),
			'tasks_list' => array(),
			'method' => $this->settings['method']
		);

		$tasks_list = $this->EE->db->where('site_id', SITE_ID)->get('mx_easy_backup_tasks');

		if ($tasks_list->num_rows()) {
			foreach ($tasks_list->result() as $row) {
				$task_settings                     = unserialize($row->settings);
				$vars['tasks_list'][$row->task_id] = $task_settings['task_name'];
			}

		}



		$date_fmt = ($this->EE->session->userdata('time_format') != '') ? $this->EE->session->userdata('time_format') : $this->EE->config->item('time_format');

		if ($date_fmt == 'us') {
			$vars['datestr'] = '%m/%d/%y %h:%i %a';
		} else {
			$vars['datestr'] = '%Y-%m-%d %H:%i';
		}

		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		$this->EE->load->model('tools_model');

		$this->EE->jquery->tablesorter('.mainTable', '{
			headers: {
			0: {sorter: false},
			8: {sorter: false},
			9: {sorter: false}
		},
			widgets: ["zebra"]
		}');

		$this->EE->javascript->output('
									$(".toggle_all").toggle(
										function(){
											$("input.toggle").each(function() {
												this.checked = true;
											});
										}, function (){
											var checked_status = this.checked;
											$("input.toggle").each(function() {
												this.checked = false;
											});
										}
									);');

		if ($notice = $this->errors_generator($this->errors)) {
			$this->EE->javascript->output('$.ee_notice([' . $notice . ']);');
		}

		$this->EE->javascript->compile();

		return $this->content_wrapper('backup_list', $this->EE->lang->line('backup_list'), $vars);
	}

	function index()
	{
		if ($this->EE->input->post('toggle')) {
			foreach ($_POST['toggle'] as $key => $val) {
				$this->EE->db->delete('mx_easy_backup_tasks', array(
						'task_id' => $val
					));
			}
		}

		$vars = array();

		// Create the variable array
		$vars = array(
			'addon_name' => $this->module_name,
			'error' => FALSE,
			'input_prefix' => __CLASS__,
			'message' => FALSE,
			'settings_form' => FALSE,
			'export_out' => false,
			'task_id' => ($this->EE->input->get_post('task')) ? $this->EE->input->get_post('task') : false,
			'task_list' => $this->EE->db->where('site_id', SITE_ID)->get('mx_easy_backup_tasks')

		);

		// Date
		$date_fmt = ($this->EE->session->userdata('time_format') != '') ? $this->EE->session->userdata('time_format') : $this->EE->config->item('time_format');

		if ($date_fmt == 'us') {
			$vars['datestr'] = '%m/%d/%y %h:%i %a';
		} else {
			$vars['datestr'] = '%Y-%m-%d %H:%i';
		}

		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		$this->EE->load->model('tools_model');

		$this->EE->jquery->tablesorter('.mainTable', '{
			headers: {
			0: {sorter: false},
			4: {sorter: false},
			5: {sorter: false},
			6: {sorter: false}
		},
			widgets: ["zebra"]
		}');

		$this->EE->javascript->output('
									$(".toggle_all").toggle(
										function(){
											$("input.toggle").each(function() {
												this.checked = true;
											});
										}, function (){
											var checked_status = this.checked;
											$("input.toggle").each(function() {
												this.checked = false;
											});
										}
									);');

		if ($notice = $this->errors_generator($this->errors)) {
			$this->EE->javascript->output('$.ee_notice([' . $notice . ']);');
		}

		$this->EE->javascript->compile();

		return $this->content_wrapper('index', $this->EE->lang->line('tasks'), $vars);

	}

	function errors_generator($error_list)
	{
		$message = "";
		if ($error_list['message_failure']) {
			foreach ($error_list['message_failure'] as $error) {
				$message = $message . "{message:'$error', type:'error'},";
			}
		}
		if ($error_list['message_success']) {
			foreach ($error_list['message_success'] as $error) {
				$message = $message . "{message:'$error', type:'succes'},";
			}
		}
		return ($message != "") ? trim($message, ',') : false;
	}


	function content_wrapper($content_view, $lang_key, $vars = array())
	{
		$vars['content_view'] = $content_view;
		$vars['_base']        = $this->base;
		$vars['_form_base']   = $this->form_base;
		$this->EE->view->cp_page_title =  lang($lang_key);
		$this->EE->cp->set_breadcrumb($this->base, lang('mx_easy_backup_module_name'));

		return $this->EE->load->view('_wrapper', $vars, TRUE);
	}

}

require_once PATH_THIRD . 'mx_easy_backup/libraries/misc/pclzip.lib.php';
require_once PATH_THIRD . 'mx_easy_backup/libraries/db/dumper.php';
/* End of file mcp.mx_easy_backup.php */
/* Location: ./system/expressionengine/third_party/mx_easy_backup/mcp.mx_easy_backup.php */