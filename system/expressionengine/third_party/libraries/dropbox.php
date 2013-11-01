<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once  PATH_THIRD.'mx_easy_backup/libraries/dropbox/droplib.class.php';
/**
* 
*/
class Dropbox
{
	private $connection;
	var $basket	= '';
	var $dropbox_key	= '';
	var $dropbox_secret	= '';
	var $dropbox_password	= '';
	var $dropbox_email	= '';
	var $new_basket	= '';
	var $dropbox_remotedir = '';
	
	function Dropbox($config = array())
	{	 
		if (count($config) > 0)
		{
			$this->initialize($config);
		}
	   
	    $this->dropbox_remotedir = (trim(trim($this->dropbox_remotedir, '/')) != "") ? trim($this->dropbox_remotedir, '/') : null;
		
		$this->EE =& get_instance();

		$this->connection =   new DropLib($this->dropbox_key, $this->dropbox_secret);
		//new DropLib($this->dropbox_key, $this->dropbox_secret);
		$this->connection->setNoSSLCheck(true);
		$this->_get_token();
	//	print_r($this->_get_token());
	//	die();	
	//"jn9zzdjhc4bmbvj", "yb1363xbzpt3hc"
		//jn9zzdjhc4bmbvj [secret] => yb1363xbzpt3hc
		//$oauth->setToken($this->_get_token());
	}

	function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
	}
	
	 function _get_token () 
	 {
	 	return $this->connection->authorize($this->dropbox_email, $this->dropbox_password);
	 }
	 
	function bucket_list () 
	{

	}
	
	function delete_bucket ($obj) 
	{

	}
	
	function create_bucket ($obj) 
	{

	}	

	function upload ($object, $path ) 
	{
		
		foreach ($object as $filename => $filesize) {
				$this->EE->benchmark->mark('tmp_backup_start');
			    $this->connection->upload($path . $filename, $this->dropbox_remotedir . '/' );
		   		$this->EE->benchmark->mark('tmp_backup_end');
				$object[$filename]['time']  =  $object[$filename]['time'] + $this->EE->benchmark->elapsed_time('tmp_backup_start', 'tmp_backup_end');
		}

		return $object;
	}		
	
	function delete ($object) 
	{
        foreach ($object as $filename) {
            $this->connection->delete($this->dropbox_remotedir . '/' . $filename);
        }
	}		
	
	function download ($file, $path = '', $direct = true) 
	{ 
		if ($direct) {
			return $this->connection->download($this->dropbox_remotedir . '/' . $file);
		} else {
			file_put_contents($path.$file, $this->connection->download($this->dropbox_remotedir . '/' . $file));
			return true;
		}
		
		  
	}	
	
}