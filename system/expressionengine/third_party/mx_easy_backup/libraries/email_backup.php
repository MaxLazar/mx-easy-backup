<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once  PATH_THIRD.'mx_easy_backup/libraries/rackspace/cloudfiles.php';
/**
 *
 */
class email_backup
{

	private $connection;
	var $send_to_email_address = '';
	var $send_to_email_subject = '';
	var $send_to_email_body = '';
	var $date_fmt;

	function email_backup($config = array())
	{
		if (count($config) > 0)
		{
			$this->initialize($config);
		}

		$this->EE =& get_instance();


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

	function upload ($object , $path )
	{
		if (isset($this->send_to_email_address)) {
			$date_fmt = ($this->EE->session->userdata('time_format') != '') ? $this->EE->session->userdata('time_format') : $this->EE->config->item('time_format');
			$data =  array (
				'plan_id' =>'',
				'data' => $this->EE->localize->decode_date((($date_fmt == 'us') ?  '%m/%d/%y %h:%i %a' : '%Y-%m-%d %H:%i'), $this->EE->localize->now, TRUE),
				'plan_name' =>'',
				'size' => 0,
				'time' => 0,
				'backup_name' => ''
			);
			$this->EE->load->library('misc/mx_common');
			$this->EE->load->helper('text');
			
			$this->EE->load->library('email');
			$this->EE->email->clear();
			$this->EE->email->wordwrap = true;
			$this->EE->email->mailtype =  'text';

			$this->EE->email->from($this->EE->config->item('webmaster_email'), $this->EE->config->item('site_name'));
			

			foreach ($object as $filename => $val) {
				$this->EE->email->attach($path . $filename);
				$data['plan_name'] = $val['plan_name'];
				$data['filename'] = $data['backup_name'].'['. $filename .']';
				$data['size'] = $val['filesize'] + $data['size'];
				$data['plan_id'] = $val['plan_id'] ;
				$data['time'] = $data['time'] + $val['time'];
			}


			$data['size'] = $this->EE->mx_common->format_size($data['size']);


			$subject = $this->EE->functions->var_swap( $this->send_to_email_subject ,  $data );
			$body = $this->EE->functions->var_swap( $this->send_to_email_body ,  $data );



			$this->EE->email->to($this->send_to_email_address);
			$this->EE->email->from($this->EE->config->item('webmaster_email'),
				$this->EE->config->item('site_name'));
			$this->EE->email->subject($subject);
			$this->EE->email->message($body);

			if ( ! $this->EE->email->Send())
			{
				echo 'email_error'; //@@replace!
				die();
			}


		} else {
			return false;
		}
		return $object;
	}

	function delete ($object)
	{
		return true;
	}

	function download ($file)
	{
		return true;
	}
}