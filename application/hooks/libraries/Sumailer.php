<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Sumailer {
	public function __construct()
	{
		$this->config->load('ion_auth', TRUE);
		$this->load->library(array('email'));
		$this->lang->load('ion_auth');
		$this->load->helper(array('cookie', 'language','url'));
		$this->load->library('session');
		$this->load->model('ion_auth_model');

	}
	
	public function send_email($params)
	{
		// If using SMTP
		$settings = $this->setting_model->get_setting();
			if(empty($settings->smtp_host) || empty($settings->smtp_user) || empty($settings->smtp_pass) || empty($settings->smtp_port)){
				$this->session->set_flashdata('error', "SMTP Settings Required");
				redirect('admin/settings');
			}
				$this->load->library('encrypt');
				$raw_smtp_pass =  $this->encrypt->decode($settings->smtp_pass);
				$config = array(
						'smtp_host' => $settings->smtp_host,
						'smtp_port' => $settings->smtp_port,
						'smtp_user' => $settings->smtp_user,
						'smtp_pass' => $raw_smtp_pass,
						'crlf' 		=> "\r\n",    							
						'protocol'	=> 'smtp',
				);						
			// Send email 
			$config['useragent'] = 'Suraaj';
			$config['mailtype'] = "html";
			$config['newline'] = "\r\n";
			$config['charset'] = 'utf-8';
			$config['wordwrap'] = TRUE;
			
			$this->load->library('email',$config);
			$this->email->from($settings->email, $settings->name);
			$this->email->to($params['recipient']);
			$this->email->subject($params['subject']);
			$this->email->message($params['message']);
			    if($params['attached_file'] != ''){ 
			    	$this->email->attach($params['attached_file']);
			    }
			$this->email->send();
	//echo $this->email->print_debugger();;die;
    }
}