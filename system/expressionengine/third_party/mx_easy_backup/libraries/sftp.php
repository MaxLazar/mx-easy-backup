<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
*/


require_once  PATH_THIRD.'mx_easy_backup/libraries/sftp/SFTP.php';

class Sftp
{

	private $connection;
	private $sftp;
	var $sftp_host	= '';
	var $sftp_username	= '';
	var $sftp_password	= '';
	var $sftp_port		= 22;
	var $sftp_path	= "/";

	var $debug		= FALSE;
	var $conn_id	= FALSE;

	public function __construct($config = array())
	{
		if (count($config) > 0)
		{
			$this->initialize($config);
		}
		
		$this->EE =& get_instance();
			
		$this->sftp_connect();

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

		// Prep the hostname
		$this->hostname = preg_replace('|.+?://|', '', $this->sftp_host);
	}
	
	function sftp_connect()
	{

		$this->sftp = new Net_SFTP($this->sftp_host, $this->sftp_port);

		$this->_login();
			
		return true;
	}	
	
	public function _login()
	{
		 if (!$this->sftp->login($this->sftp_username, $this->sftp_password)) {
			 throw new Exception("Could not authenticate with username $this->sftp_username " . "and password $this->sftp_password.");
		 }
	 		
	} 

	public function upload($object, $path)
	{

        foreach ($object as $filename => $filesize) {
			$this->EE->benchmark->mark('tmp_backup_start');
      	   $this->sftp->put($this->sftp_path.$filename, $path . $filename, NET_SFTP_LOCAL_FILE);
			$this->EE->benchmark->mark('tmp_backup_end');
			
			$object[$filename]['time']  =  $object[$filename]['time'] + $this->EE->benchmark->elapsed_time('tmp_backup_start', 'tmp_backup_end');
        }

		return $object;
	}
	

		
	function download ($file, $path = '', $direct = true) 
	{
		return (($direct) ? $this->sftp->get($this->sftp_path.$file) : $this->sftp->get($this->sftp_path.$file, $path.$file));
	}
	
	public function delete($object){

		 foreach ($object as $filename) {
		 	$this->sftp->delete($this->sftp_path.$filename);
	     }

		return true;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Close the connection
	 *
	 * @access	public
	 * @param	string	path to source
	 * @param	string	path to destination
	 * @return	bool
	 */
	function close()
	{

		return true;
	}

}

