<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
class Mx_easy_backup_upd {
		
	var $version     = MX_EASY_BACKUP_VER; 
	var $module_name = MX_EASY_BACKUP_KEY;
	var $folder_name = "easy_backup";
	
    function Mx_easy_backup_upd( $switch = TRUE ) 
    { 
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
				if (defined('SITE_ID') == FALSE)
			define('SITE_ID', $this->EE->config->item('site_id'));
    } 

    /**
     * Installer for the Mx_easy_backup module
     */
    function install() 
	{				
						
		$data = array(
			'module_name' 	 => $this->module_name,
			'module_version' => $this->version,
			'has_cp_backend' => 'y'
		);

		$this->EE->db->insert('modules', $data);		

					
		if (!$this->EE->db->table_exists('exp_mx_easy_backup'))
		{
			$this->EE->db->query("CREATE TABLE IF NOT EXISTS exp_mx_easy_backup (
							  `backup_id` int(10) unsigned NOT NULL auto_increment,
							  `site_id` int(10) unsigned NOT NULL,							  
							  `task_id` int(10) unsigned NOT NULL,
							  `backup_name`     varchar(50)     NOT NULL default '',
							  `method`     varchar(20)     NOT NULL default '',
							  `date`       int(10)  unsigned NOT NULL,
							  `time`       int(10)  unsigned NOT NULL,
							  `size`     varchar(50)     NOT NULL default '',
							  `type`     varchar(10)     NOT NULL default '',
							  PRIMARY KEY (`backup_id`)
							)");
		};
		
		if (!$this->EE->db->table_exists('exp_mx_easy_tasks'))
		{
			$this->EE->db->query("CREATE TABLE IF NOT EXISTS exp_mx_easy_backup_tasks (
							  `task_id` int(10) unsigned NOT NULL auto_increment,
							  `site_id` int(10) unsigned NOT NULL,
							  `last_run` int(10) unsigned NOT NULL,
							  `settings`    TEXT     NOT NULL default '',
							  PRIMARY KEY (`task_id`)
							)");
		};
				
		if (!$this->EE->db->field_exists('settings', 'modules')) {
			$this->EE->load->dbforge();
	
			$column = array('settings'	 => array('type' => 'TEXT'));
			$this->EE->dbforge->add_column('modules', $column);
		}

		 $this->EE->load->library('misc/mx_common');
		 $this->create_backup_dir();
		 
		 $settings  = array (
			'email_subject' => $this->EE->lang->line('email_subject_template'),
			'email_body' =>	$this->EE->lang->line('email_body_template'),
			'enable_template' => 'yes',
			'method' =>  'php',
			'archive_method' => 'gzip',
			'local_path' => APPPATH.'cache/'.$this->folder_name,
			'email_address' => ''
		 );
		//'method' => ((stristr (PHP_OS, 'WIN')) ? 'php' : 'system'),
        $this->EE->mx_common->saveSettingsToDB($settings);

	
		$sql[] = "INSERT INTO exp_actions (class, method) VALUES ('$this->module_name', 'start_backup')";
	//	$default_task = ;

		$sql[] = "INSERT INTO exp_mx_easy_backup_tasks (site_id, last_run, settings) VALUES ('".SITE_ID."', '', '".$this->default_task()."')";


		foreach ($sql as $query)
		{
			$this->EE->db->query($query);
		}
			

		//
		// Add additional stuff needed on module install here
		// 
																									
		return TRUE;
	}
	
	function default_task () {
		$vars = array();
		$vars['settings']['uniqid'] = uniqid();
		$vars['send_to'] = 'none';
		$vars['local_path'] = APPPATH.'cache/'.$this->folder_name;
		$vars['task_name'] = 'Pre-upgrade';
		$vars['db_backup'] = 'yes';
		$vars['config_files'] = 'yes';
		$vars['themes_folder'] = 'yes';
		$vars['addons_folder'] = 'yes';
		$vars['templates_folder'] = 'yes';
		$vars['language_folder'] = 'yes';
		$vars['backup_type'] = 'full';
		return serialize($vars);
	}
	
    function create_backup_dir () {

    	if ( ! @is_dir(APPPATH.'cache/'.$this->folder_name))
		{
			if ( ! @mkdir(APPPATH.'cache/'.$this->folder_name, 0777))
			{
				return FALSE;
			}
			
			@chmod(APPPATH.'cache/'.$this->folder_name, 0777); 
		}
    	
		if ( ! @is_dir(APPPATH.'cache/'.$this->folder_name.'/'.'temp'))
		{
			if ( ! @mkdir(APPPATH.'cache/'.$this->folder_name.'/'.'temp', 0777))
			{
				return FALSE;
			}
			
			@chmod(APPPATH.'cache/'.$this->folder_name.'/'.'temp', 0777); 
		}
		
		
    	if ($fp = @fopen(APPPATH.'cache/'.$this->folder_name.'/'.".htaccess", 'wb'))
    	{
    		flock($fp, LOCK_EX);
        	fwrite($fp, "RewriteEngine Off");
			fwrite($fp, "deny from all");
        	flock($fp, LOCK_UN);
        	fclose($fp);
    	}

    	@chmod(APPPATH.'cache/'.$this->folder_name.'/'.'.htaccess', 0777);
	}
       
	function add_column_if_not_exist($db, $column, $column_attr = "VARCHAR( 255 ) NULL" ){
		$exists = false;
		$columns = mysql_query("show columns from $db");
		while($c = mysql_fetch_assoc($columns)){
			if($c['Field'] == $column){
				$exists = true;
				break;
			}
		}
		if(!$exists){
			mysql_query("ALTER TABLE `$db` ADD `$column`  $column_attr");
		}
	}
	
	/**
	 * Uninstall the Mx_easy_backup module
	 */
	function uninstall() 
	{ 				
		
		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => $this->module_name));
		
		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');
		
		$this->EE->db->where('module_name', $this->module_name);
		$this->EE->db->delete('modules');
		
		$this->EE->db->where('class', $this->module_name);
		$this->EE->db->delete('actions');
		
		$this->EE->db->where('class', $this->module_name.'_mcp');
		$this->EE->db->delete('actions');
		
		$this->EE->load->dbforge();

		try{
			$this->EE->dbforge->drop_table('mx_easy_backup');
			$this->EE->dbforge->drop_table('mx_easy_backup_tasks');
		return true;}
		catch(Exception $e){return true;} 		
		
		return TRUE;
	}
	
	/**
	 * Update the Mx_easy_backup module
	 * 
	 * @param $current current version number
	 * @return boolean indicating whether or not the module was updated 
	 */
	
	function update($current = '')
	{
		return FALSE;
	}
    
}

/* End of file upd.mx_easy_backup.php */ 
/* Location: ./system/expressionengine/third_party/mx_easy_backup/upd.mx_easy_backup.php */ 