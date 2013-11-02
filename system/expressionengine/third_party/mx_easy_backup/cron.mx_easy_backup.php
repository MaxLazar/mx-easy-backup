<?php 
/**
 *
 * @package  MX Easy Backup
 * @subpackage ThirdParty
 * @category Modules
 * @author    Max Lazar <max@eec.ms>
 * @copyright Copyright (c) 2011 Max Lazar (http://eec.ms)
 * @Commercial - please see LICENSE file included with this distribution
 * @link  http://eec.ms/
 */
 
	$time_cron = 0;
	
	if (isset($_SERVER['REMOTE_ADDR'])) die('Permission denied.');

	if (!ini_get('safe_mode') && function_exists('set_time_limit') && strpos(ini_get('disable_functions'), 'set_time_limit') === false) @set_time_limit($time_cron);	
	
	$_SERVER["REQUEST_URI"] ='/';

	$system_path = join(array_slice(explode( "/" ,dirname($_SERVER['PHP_SELF'])),0,-3),"/").'/';
	$addon_path  = dirname($_SERVER['PHP_SELF']);
	
	$debug = 0;

	$assign_to_config['enable_query_strings'] = TRUE;
	$assign_to_config['subclass_prefix'] = 'EE_';

	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// The PHP file extension
	define('EXT', '.php');

 	// Path to the system folder
	define('BASEPATH', str_replace("\\", "/", $system_path.'codeigniter/system/'));
	
	// Path to the "application" folder
	define('APPPATH', $system_path.'expressionengine/');
	
	// Path to the front controller (this file)
	define('FCPATH', str_replace(SELF, '', __FILE__));
	
	// Name of the "system folder"
	define('SYSDIR', trim(strrchr(trim(str_replace("\\", "/", $system_path), '/'), '/'), '/'));

	// The $debug value as a constant for global access
	define('DEBUG', $debug);  unset($debug);
	
	define('REQ', "ACTION");
	
	$routing['directory'] = '';
	$routing['controller'] = 'ee';
	$routing['function'] = 'index';	
	
	require_once BASEPATH.'core/CodeIgniter'.EXT;
	
	$EE =& get_instance();
	
	$EE->load->add_package_path($addon_path.'/');
	// /usr/bin/php /expressionengine/third_party/mx_easy_backup/cron.mx_easy_backup.php -j=1
	unset($argv[0]); 
	
	$_SERVER['QUERY_STRING'] =  $_SERVER['PATH_INFO'] = $_SERVER['REQUEST_URI'] = '/' . implode('/', $argv) . '/';

	require_once $addon_path.'/mcp.mx_easy_backup.php';

	$task_id = $argv[1];
	 
/*	 if (preg_match("/^-([hupoj])=(.*?)$/", $argv, $m)){
	     switch ($m[1]) {
	         case 'j': $task_id = $m[2]; break; // job
	     }
	 }*/
	/*echo  $task_id.'<<<<<';
http://nashruddin.com/how-to-read-and-parse-command-line-arguments.html
while(count($argv) > 0) {
    $arg = array_shift($argv);
    switch($arg) {
        case '-U':
            $url  = array_shift($argv);
            break;
        case '-u':
            $username = array_shift($argv);
            break;
        case '-p':
            $password = array_shift($argv);
            break;
        case '-t':
            $filetype = array_shift($argv);
            break;
    }
}

	print_r($argv);
	die();*/
	
	$backup  = new Mx_easy_backup_mcp;
	

	if ($backup->make_backup($task_id, false)) {
		echo "OK";
	}else {
		echo "ERROR";
	};

	//echo 'arg:'.$argv[1].$argv[2].$argv[3]
?>

