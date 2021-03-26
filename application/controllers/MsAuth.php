<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MsAuth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation'));
        $this->load->helper(array('url', 'language'));
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    public function index()
    {

    }

    public function login() {
        $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
        $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');
        if ($this->form_validation->run() == true)
        {
            $remember = (bool) $this->input->post('remember');
            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
            {
                
                //if the login is successful
                if ($this->ion_auth->is_user()) {
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    // echo "Ye";die;
                    redirect('MsAppUser/index', 'refresh');
                } else {
                    $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>You are not authorized to access this page</div>");
                    redirect('MsAuth/login');
                } 
                
            }
            else
            {
                // if the login was un-successful
                $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>".$this->ion_auth->errors()."</div>");
                redirect('MsAuth/login', 'refresh');
            }
        }
        else
        {
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['identity'] = array('name' => 'identity',
                'id'    => 'identity',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('identity'),
            );
            $this->data['password'] = array('name' => 'password',
                'id'   => 'password',
                'type' => 'password',
            );

            $this->_render_page('msappuser/login', $this->data);
        }
    }

    public function logout()
    {
        $this->data['title'] = "Logout";

        // log the user out
        $logout = $this->ion_auth->logout();

        // redirect them to the login page
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>".$this->ion_auth->messages()."</div>");
        redirect('auth/login', 'refresh');
    }

    public function _render_page($view, $data=null, $returnhtml=false)//I think this makes more sense
    {

        $this->viewdata = (empty($data)) ? $this->data: $data;

        $view_html = $this->load->view($view, $this->viewdata, $returnhtml);

        if ($returnhtml) return $view_html;//This will return html on 3rd argument being true
    }

}

/* End of file MsAuth.php */
/* Location: ./application/controllers/MsAuth.php */
