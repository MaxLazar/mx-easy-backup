<?php  

	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	require_once PATH_THIRD . 'mx_easy_backup/config.php';

	/**
	 * -
	 * @package		MX Easy Backup
	 * @subpackage	ThirdParty
	 * @category	Modules
	 * @author    Max Lazar <max@eec.ms>
	 * @copyright Copyright (c) 2013 Max Lazar (http://eec.ms)
	 * @link		http://eec.ms/
	 */

	require_once 'mcp.mx_easy_backup.php';

	class Mx_easy_backup {

		var $return_data;
		
		function Mx_easy_backup()
		{		
			$this->EE =& get_instance(); // Make a local reference to the ExpressionEngine super object
		}
		
		function  start_backup (){
				$task_id = ($this->EE->input->get('task_id') != '') ? $this->EE->input->get('task_id') : $this->EE->TMPL->fetch_param('task_id');
				$backup  = new Mx_easy_backup_mcp;
				
				if ($backup->make_backup($task_id, false)) {
					$this->EE->output->_display("OK".$task_id);
				}else {
					$this->EE->output->_display("ERROR");
				};
				exit;
		}
		
	}

/* End of file mod.mx_easy_backup.php */ 
/* Location: ./system/expressionengine/third_party/mx_easy_backup/mod.mx_easy_backup.php */ 