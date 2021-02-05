<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Politician extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array('ion_auth','form_validation'));
		$this->load->helper(array('url','language'));
		$this->load->model('Setting_model');
		
		$this->settings = $this->Setting_model->get_setting();
		if(!empty($this->settings->timezone)){
			date_default_timezone_set($this->settings->timezone);
		}
		else{
			date_default_timezone_set("Asia/Kolkata");
		}
	}
	
	public function index()
	{
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}
		elseif (!$this->ion_auth->is_politician()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			return show_error('You must be a Politician to view this page. <a href="'.base_url('auth/logout').'">Logout</a>');
		}
		else
		{
			
			$this->load->view('layout/backend_header', $this->session->userdata);
			$this->load->view('layout/backend_sidebar');
			$this->load->view('politician/dashboard');
			$this->load->view('layout/backend_footer');
			
		}
	}
}
