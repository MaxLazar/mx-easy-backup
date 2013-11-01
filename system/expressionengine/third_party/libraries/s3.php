<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once  PATH_THIRD.'mx_easy_backup/libraries/s3/S3.php';
/**
* 
*/
class s3
{
	private $connection;
	var $bucket	= '';
	var $aws_access_key	= '';
	var $aws_secret_key	= '';
	var $new_bucket	= '';
	
	function s3($config = array())
	{	
		if (count($config) > 0)
		{
			$this->initialize($config);
		}
		
		$this->EE =& get_instance();
		$this->connection = new  S3_SDK($this->aws_access_key, $this->aws_secret_key);
		
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
		$out = array();
			
		foreach ($this->connection->listBuckets() as $val => $key) {
			$out [$key] = $key;
		}

		return $out ;
	}
	
	function delete_bucket ($obj) 
	{
	/*	$response = $this->connection->delete_bucket($obj);
		if ($response->isOK()) {
			return $this->connection->get_bucket_list();
		}
		*/
		return false;
	}
	
	function create_bucket ($obj) 
	{
	
	
		$create_bucket_response = $this->connection->putBucket($obj, S3_SDK::ACL_PRIVATE);
		
		if ($create_bucket_response)
		{
		
		}
		else {
			return false;
		}
		
		return true;
	}	

	function upload ($object , $path ) 
	{
        foreach ($object as $filename => $filesize) {
			$this->EE->benchmark->mark('tmp_backup_start');
			
			if (!$this->connection->putObjectFile($path . $filename, $this->bucket, $filename, S3_SDK::ACL_PRIVATE))
			{
			 echo "error";
			 die();
			}
			
			$this->EE->benchmark->mark('tmp_backup_end');
			$object[$filename]['time']  =  $object[$filename]['time'] + $this->EE->benchmark->elapsed_time('tmp_backup_start', 'tmp_backup_end');
        }	

		 return $object;
	}		
	
	function delete ($object) 
	{
        foreach ($object as $filename) {
			if ($this->connection->deleteObject($this->bucket, $filename)) {
				return true;
			}
        }	
	}		
	
	function download ($file, $path = '', $direct = true) 
	{
		 if ($direct) {
		 	return $this->connection->getObject($this->bucket, $file);
		 } else
		 {
    	   $this->connection->getObject($this->bucket, $file, $path.$file);
	   	   return true;
		 }

	}			
	
}