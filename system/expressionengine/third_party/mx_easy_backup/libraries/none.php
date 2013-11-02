<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
*/
class none
{

	private $connection;
	var $basket	= '';
	var $rackspace_username	= '';
	var $rackspace_api_key	= '';
	var $new_basket	= '';
	
	function none($config = array())
	{	
		if (count($config) > 0)
		{
			$this->initialize($config);
		}
		$this->EE =& get_instance();
		return true;
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

	function bucket_list () 
	{
		return true;
	}
	
	function delete_bucket ($obj) 
	{
		return true;
	}
	
	function create_bucket ($obj) 
	{
		return true;
	}	

	function upload ($object , $path) 
	{
	        foreach ($object as $filename => $filesize) {
		
			$this->EE->benchmark->mark('tmp_backup_start');

			$this->EE->benchmark->mark('tmp_backup_end');
			
			$object[$filename]['time']  =  $object[$filename]['time'] + $this->EE->benchmark->elapsed_time('tmp_backup_start', 'tmp_backup_end');
        }
        
		return $object;
	}		
	
	function delete ($object, $path = null) 
	{
                foreach ($object as $filename) {
                    unlink($path . $filename);
                }
	}		
	
	function download ($file, $path = '', $direct = true) 
	{
		if ($direct) {
			return file_get_contents($path . $file);
		} else {		
			return $file;
		}
		
	}	
}