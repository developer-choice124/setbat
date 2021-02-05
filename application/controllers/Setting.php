<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

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
		elseif (!$this->ion_auth->is_admin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			return show_error('You must be an administrator to view this page. <a href="'.base_url('auth/logout').'">Logout</a>');
		}
		else
		{
			$this->data['settings'] = $this->settings;
			if ($this->input->server('REQUEST_METHOD') === 'POST')
		    {	
				
				//echo '<pre>'; print_r($_POST);die;
				$this->load->library('form_validation');
				$this->form_validation->set_rules('name', 'lang:company_name', 'required');
				$this->form_validation->set_message('required', lang('custom_required'));
				if ($this->form_validation->run()==true)
		        {
					$photo = array();
					if($_FILES['img'] ['name'] !='')
					{ 
						
					
						$config['upload_path'] = './assets/uploads/images/';
						$config['allowed_types'] = 'gif|jpg|png';
						$config['max_size']	= '10000';
						$config['max_width']  = '10000';
						$config['max_height']  = '6000';
				
						$this->load->library('upload', $config);
				
						if ( !$img = $this->upload->do_upload('img'))
						{
							
						}
						else
						{
							$img_data = array('upload_data' => $this->upload->data());
							$save['image'] = $img_data['upload_data']['file_name'];
						}
						
					}
					
					$save['name'] = $this->input->post('name');
					$save['address'] = $this->input->post('address');
					$save['contact'] = $this->input->post('contact');
					$save['email'] = $this->input->post('email');
					$save['timezone'] = $this->input->post('timezone');
					$save['smtp_host'] = $this->input->post('smtp_host');
					$save['smtp_user'] = $this->input->post('smtp_user');
					$save['smtp_pass'] = $this->input->post('smtp_pass');
					$save['smtp_port'] = $this->input->post('smtp_port');
					$this->Setting_model->update($save);
					
					$this->session->set_flashdata('message', 'Setting updated successfully');
					redirect('admin/setting');
					
				}
			}
			$this->load->view('layout/backend_header', $this->session->userdata);
			$this->load->view('layout/backend_sidebar');
			$this->load->view('admin/setting', $this->data);
			$this->load->view('layout/backend_footer');
			
		}
	}

	public function social_settings()
	{
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}
		elseif (!$this->ion_auth->is_admin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			return show_error('You must be an administrator to view this page. <a href="'.base_url('auth/logout').'">Logout</a>');
		}
		else
		{
			$this->data['settings'] = $this->settings;
			if ($this->input->server('REQUEST_METHOD') === 'POST')
		    {	
				
				
					//echo 'hie'; die;
					$save['facebook'] = $this->input->post('facebook');
					$save['google_plus'] = $this->input->post('google_plus');
					$save['twitter'] = $this->input->post('twitter');
					$save['instagram'] = $this->input->post('instagram');
					$save['linkedin'] = $this->input->post('linkedin');
					$save['youtube'] = $this->input->post('youtube');
					$this->Setting_model->update($save);
					
					$this->session->set_flashdata('message', 'Setting updated successfully');
					redirect('admin/setting/social_settings');
					
				
			}
			$this->load->view('layout/backend_header', $this->session->userdata);
			$this->load->view('layout/backend_sidebar');
			$this->load->view('admin/social_setting', $this->data);
			$this->load->view('layout/backend_footer');
			
		}
	}
}
