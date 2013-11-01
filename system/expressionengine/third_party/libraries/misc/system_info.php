<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * @package  MX Easy Backup
 * @subpackage ThirdParty
 * @category Modules
 * @author    Max Lazar <max@eec.ms>
 * @copyright Copyright (c) 2011 Max Lazar (http://eec.ms)
 * @Commercial - please see LICENSE file included with this distribution
 * @link  http://eec.ms/
 */


class system_info
{
	function __construct() {
		$this->EE =& get_instance();
	}

	function compatibility_test($config = array("php" => "5.2.0")) {
		$info = array();

		$exeption = array ('php_v','post_max_size','upload_max_filesize','max_input_time','max_execution_time','browser_used', 'mysql_v', 'OS Type', 'SERVER_SOFTWARE' );

		$info['SERVER_SOFTWARE'] = (isset($_SERVER['SERVER_SOFTWARE'])) ? $_SERVER['SERVER_SOFTWARE'] : '';
		$info['win']  =  (stristr(PHP_OS, 'WIN')) ? true : false;
		$info['OS Type'] = php_uname();
		$info['mysql_v'] = mysql_get_server_info();
		
		
		$info['php_v'] = phpversion();
		$info['php'] = (function_exists('version_compare') && version_compare(phpversion(), $config["php"], '>=')); // php version	
		$info['safe_mode'] = ((ini_get('safe_mode') == 1) || (ini_get('safe_mode') == "On")) ? true : false;	
		$info['eaccelerator.enable'] = ((ini_get('eaccelerator.enable') == 1)) ? true : false;	
				
		$info['max_input_time'] = ini_get('max_input_time');
		$info['max_execution_time'] =   ini_get('max_execution_time');
		$info['file_uploads'] = ((ini_get('file_uploads') == 1) || (ini_get('file_uploads') == "On")) ? true : false;
		$info['upload_max_filesize'] = ini_get('upload_max_filesize');
		$info['file'] = (function_exists('file_get_contents') && function_exists('file_put_contents'));

		$info['post_max_size'] = ini_get('post_max_size');

		$info['register_globals'] = ((ini_get('register_globals') == 1) || (ini_get('register_globals') == "On")) ? true : false;
		$info['allow_url_fopen'] = ((ini_get('allow_url_fopen') == 1) || (ini_get('allow_url_fopen') == "On")) ? true : false;

		$info['gd'] = ( ! function_exists('imagecreatefromstring') ) ? false: true;



		$info['exec'] = (function_exists('shell_exec')) ? true : false;
		$info['exec_denied']= preg_match('/exec/', ini_get('disable_functions'));
		$info['curl'] = ($this->_iscurlinstalled()) ? true : false;
		
		if (@function_exists('curl_version'))
		{
			$info['curl_version'] = @curl_version();
			$info['curl_https'] = (in_array('https', $info['curl_version'], true)) ? true : false;
		}
		
		$info['mbstring'] = ($this->_ismbstringinstalled()) ? true : false; //RC
		

		$info['mem_usage_possible'] = function_exists('memory_get_usage');
		$info['memory_limit'] = ini_get('memory_limit');
		$info['simplexml'] = extension_loaded('simplexml'); // Amazon S3
		$info['json'] = (extension_loaded('json') && function_exists('json_encode') && function_exists('json_decode')); //json
		$info['spl'] = extension_loaded('spl');
		$info['pcre'] = extension_loaded('pcre');
		$info['suhosin'] = (!extension_loaded('suhosin')) ? false : true;
		
		if ($info['exec']) {
			exec('command -v tar &>/dev/null && echo "OK" || echo "NOT OK"',$out );
			$info['tar'] =  ($out[0] == "OK") ? true : false;
			exec('command -v mysql &>/dev/null && echo "OK" || echo "NOT OK"',$out );
			$info['mysql'] =  ($out[0] == "OK") ? true : false;
			exec('command -v mysqldump &>/dev/null && echo "OK" || echo "NOT OK"',$out );
			$info['mysqldump'] =  ($out[0] == "OK") ? true : false;
			exec('command -v gunzip &>/dev/null && echo "OK" || echo "NOT OK"',$out );
			$info['gunzip'] =  ($out[0] == "OK") ? true : false;
		}
		
		
		$info['browser_used'] = $_SERVER['HTTP_USER_AGENT'];
		


		$out = "";

		foreach($info as $key => $val) {
			if (in_array($key, $exeption)) {
				$out .= $key . ' = ' . $val . "\n";
			}
			else {
				$out .= $key . ' = ' . (($val) ? 'TRUE' : 'FALSE') ."\n";
			}
		}

		$out .= "\n===============================================\n\n";
		$out .= "".APP_NAME.' '.APP_VER.' '.APP_BUILD.'';
		$out .= "\n\n===============================================\n\n";
		$out .= "".'Installed Modules:'."\n".$this->_get_modules();
		$out .= "\n===============================================\n\n";
		$out .= "".'Installed Fieldtypes:' ."\n".$this->_get_fieldtypes()."\n";
		$out .= "\n===============================================\n\n";
		$out .= "".'Installed Ext:' ."\n".$this->_get_ext()."\n";
		
		return $out;
	}

	function _get_ext() {
		$out = "";

		$this->EE->load->library('addons');
		$this->EE->load->model('addons_model');

		$installed_ext = array();
		$extension_files = $this->EE->addons->get_files('extensions');

		$installed_ext_q = $this->EE->addons_model->get_installed_extensions();

		foreach ($installed_ext_q->result_array() as $row)
		{
			$installed_ext[$row['class']] = $row;
		}

		$installed_ext_q->free_result();

		foreach($extension_files as $ext_name => $ext)
		{
			$class_name = $ext['class'];

			if (isset($installed_ext[$class_name]))
			{
				$out .= $ext_name. '  ' . $installed_ext[$class_name]['version'] . "\n";
			}
		}

		return $out;
	}

	function _get_modules() {
		$out = "";

		$this->EE->load->library('addons');

		$installed = $this->EE->addons->get_installed();
		$modules = $this->EE->addons->get_files();

		foreach ($modules as $module => $module_info)
		{
			$installed_m = isset($installed[$module]);
			if ($installed_m)
			{
				$out .= $module. '  ' .  $installed[$module]['module_version'] . "\n";

			}
		}
		return $out;
	}

	function _get_fieldtypes() {
		$this->EE->load->library('api');

		$this->EE->api->instantiate('channel_fields');
		$this->EE->load->library('addons');
		$out = "";
		$installed = array();
		$fieldtypes = $this->EE->api_channel_fields->fetch_all_fieldtypes();

		$installed = $this->EE->addons->get_installed('fieldtypes');
		
		foreach ($fieldtypes as $fieldtype => $ft_info)
		{
			$installed_fts = isset($installed[$fieldtype]);
			
			if ($installed_fts)
			{
				
				$out .= $ft_info['name'] . '  ' .  $ft_info['version'] . "\n";

			}
		}
		
		return $out;
	}
    
	function email_send($subject, $body, $data, $email)
    {
        $subject = $this->EE->functions->var_swap($subject, $data);
        $body    = $this->EE->functions->var_swap($body, $data);
        
        $this->EE->load->helper('text');
        $this->EE->load->library('email');
        
        $this->EE->email->wordwrap = true;
        $this->EE->email->mailtype = 'text';
        
        $this->EE->email->from($this->EE->config->item('webmaster_email'), $this->EE->config->item('site_name'));
        
        
        $this->EE->email->to($email);
        $this->EE->email->subject($subject);
        $this->EE->email->message($body);
        
        if (!$this->EE->email->Send()) {
        }
    }
	
	function _iscurlinstalled() {
		if  (in_array('curl', get_loaded_extensions())) {
			return true;
		}
		else{
			return false;
		}
	}

	function _ismbstringinstalled() {
		if  (in_array('mbstring', get_loaded_extensions())) {
			return true;
		}
		else{
			return false;
		}
	}

}