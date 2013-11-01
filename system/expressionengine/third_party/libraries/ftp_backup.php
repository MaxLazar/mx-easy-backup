<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
*/
class ftp_backup
{

	private $connection;
	private $ftp;
	var $host	= '';
	var $username	= '';
	var $password	= '';
	var $port		= 21;
	var $passive	= TRUE;
	var $debug		= TRUE;
	var $conn_id	= FALSE;
	var $ftp_path = '/';

	public function __construct($config = array())
	{
		if (count($config) > 0)
		{
			$this->initialize($config);
		}
	}

	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}

		// Prep the hostname
		$config['hostname'] = preg_replace('|.+?://|', '', $this->host);
		
		$this->EE =& get_instance();
		
		$this->EE->load->library('ftp');

		if(!$this->EE->ftp->connect($config)) {	
			return false;
		}
		
	}
	
	public function connect($config = array())
	{
		if (count($config) > 0)
		{
			$this->initialize($config);
		}			
		
		return true;
	}	


	public function upload($object, $path)
	{
        foreach ($object as $filename => $filesize) {
			$this->EE->benchmark->mark('tmp_backup_start');

			if (!$this->EE->ftp->upload($path . $filename, $this->ftp_path.$filename, 'auto', 0775)) {

			}
			$this->EE->benchmark->mark('tmp_backup_end');
			
			$object[$filename]['time']  =  $object[$filename]['time'] + $this->EE->benchmark->elapsed_time('tmp_backup_start', 'tmp_backup_end');
        }
        
		$this->EE->ftp->close(); 
		return $object;
	}

	public function download ($file, $path = '', $direct = true) 
	{
			$this->EE->ftp->download($this->ftp_path.$file, $path.$file);

			return (($direct) ? file_get_contents($path.$file) : true);
	}
	
	function delete($object){
		
			foreach ($object as $filename) {
				$this->EE->ftp->delete_file($this->ftp_path.$filename);
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
	/*	if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		@ftp_close($this->conn_id); */
		return true;
	}

}

