<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once  PATH_THIRD.'mx_easy_backup/libraries/rackspace/cloudfiles.php';
/**
* 
*/
class Rackspace
{

	private $connection;
	private $container_ini;
	var $container	= '';
	var $rackspace_username	= '';
	var $rackspace_api_key	= '';
	var $new_bucket	= '';
	var $out = array ();
	
	function Rackspace($config = array())
	{	
		if (count($config) > 0)
		{
			$this->initialize($config);
		}
		
		$this->EE =& get_instance();
		
	    $auth =  new CF_Authentication($this->rackspace_username, $this->rackspace_api_key);
     
		$auth->authenticate();
		
		$this->connection  = new CF_Connection($auth);
	
		$this->container_ini = (empty($this->container)) ?  false  : $this->connection->get_container($this->container);
	
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
          foreach ($this->connection->get_containers() as $cont) {
              $this->out[$cont->name] = $cont->name;
          }
		  return $this->out;
	}
	
	function delete_bucket ($obj) 
	{

	}
	
	function create_bucket ($obj)  
	{

	}	

	function upload ($object , $path ) 
	{
        foreach ($object as $filename => $filesize) {
		
			$this->EE->benchmark->mark('tmp_backup_start');
            $obj = $this->container_ini->create_object($filename);
            $obj->load_from_filename($path . $filename);
			$this->EE->benchmark->mark('tmp_backup_end');
			
			$object[$filename]['time']  =  $object[$filename]['time'] + $this->EE->benchmark->elapsed_time('tmp_backup_start', 'tmp_backup_end');
        }
		
		return $object;
	}		
	
	function delete ($object) 
	{
         foreach ($object as $filename) {
             $this->container_ini->delete_object($filename);
         }
	}		
	
	function download ($file, $path = '', $direct = true) 
	{ 
		  return (($direct) ? $this->container_ini->get_object($file)->read() : $this->container_ini->get_object($file)->save_to_filename($path.$file));
	}	
}