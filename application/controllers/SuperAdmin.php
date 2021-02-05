<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SuperAdmin extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->settings = $this->Setting_model->get_setting();
        if (!empty($this->settings->timezone)) {
            date_default_timezone_set($this->settings->timezone);
        } else {
            date_default_timezone_set("Asia/Kolkata");
        }
        if (!$this->ion_auth->logged_in()) {
            redirect('Auth/login');
        }
        if(!$this->ion_auth->is_superadmin()) {
            redirect('Auth');
        }
        $this->id = $this->session->userdata('user_id');
    }

    public function index() {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/index');
        $this->load->view('layout/backend_footer');
    }

    public function allUsers() {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups order by group_id"));
        $data['admins'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'admin' order by id DESC"));
        $data['supermasters'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'supermaster' order by id DESC"));
        $data['masters'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'master' order by id DESC"));
        $data['customers'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'user' order by id DESC"));
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'admin'");
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/allusers',$data);
        $this->load->view('layout/backend_footer');
    }

    public function addUser() {
        $tables = $this->config->item('tables','ion_auth');
        $identity_column = $this->config->item('identity','ion_auth');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|is_unique[' . $tables['users'] . '.email]');
        $this->form_validation->set_rules('phone', 'Phone', 'required|is_unique[' . $tables['users'] . '.phone]');
        $this->form_validation->set_rules('full_name', 'Full Name', 'required');
        $this->form_validation->set_rules('groups', 'User Groups', 'required');
        if ($this->form_validation->run() == true)
        {
            $email    = strtolower($this->input->post('email'));
            $identity = ($identity_column==='email') ? $email : $this->input->post('identity');
            $password = 'set123';
            $additional_data = array(
                'full_name'  => $this->input->post('full_name'),
                'phone'      => $this->input->post('phone'),
                'gender'     => $this->input->post('gender'),
                'parent_id'  => $this->id,
                'commission' => $this->input->post('commission')
            );
            $groups = array(
                'id' => $this->input->post('groups')
            );
        }
        if ($this->form_validation->run() == true && $this->ion_auth->register($identity, $password, $email, $additional_data, $groups))
        {
            // check to see if we are creating the user
            // redirect them back to the admin page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("SuperAdmin/allUsers", 'refresh');
        }
        else
        {
            $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            redirect("SuperAdmin/allusers", 'refresh');
        }
    }

    public function editUser() {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $data['user'] = $this->Common_model->get_single_query("select * from users_with_groups where id = $id");
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'admin'");
        $data['csrf'] = $this->_get_csrf_nonce();
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/edit_user',$data);
        $this->load->view('layout/backend_footer');
    }

    public function updateUser($id) {
        $tables = $this->config->item('tables','ion_auth');
        // validate form input
        $this->form_validation->set_rules('full_name', $this->lang->line('edit_user_validation_fullname_label'), 'required');
        $this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required');

        if (isset($_POST) && !empty($_POST))
        {
            // do we have a valid request?
            if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
            {
                show_error($this->lang->line('error_csrf'));
            }

            // update the password if it was posted
            if ($this->input->post('password'))
            {
                $this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
                $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
            }

            if ($this->form_validation->run() === TRUE)
            {
                $data = array(
                    'full_name'     => $this->input->post('full_name'),
                    'phone'         => $this->input->post('phone'),
                    'gender'        => $this->input->post('gender'),
                    'commission'    => $this->input->post('commission')
                );

                // update the password if it was posted
                if ($this->input->post('password'))
                {
                    $data['password'] = $this->input->post('password');
                }

                //Update the groups user belongs to
                $groupData = $this->input->post('groups');
                
                if (isset($groupData) && !empty($groupData)) {

                    $this->ion_auth->remove_from_group('', $id);
                    //print_r($groupData); die;
                    
                    $this->ion_auth->add_to_group($groupData, $id);
                    

                }

                // check to see if we are updating the user
               if($this->ion_auth->update($id, $data))
                {
                    // redirect them back to the admin page if admin, or to the base url if non admin
                    $this->session->set_flashdata('message', $this->ion_auth->messages() );
                    redirect('SuperAdmin/allUsers');

                }
                else
                {
                    // redirect them back to the admin page if admin, or to the base url if non admin
                    $this->session->set_flashdata('message', $this->ion_auth->errors() );
                    redirect('SuperAdmin/editUser?user_id='.$id);

                }

            } else {
                $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                redirect('SuperAdmin/editUser?user_id='.$id);
            }
        }
    }

    public function activateUser()
    {
        $id = $this->input->get('id');
        $status = $this->input->get('status');
        if($status == 1) {
            $this->ion_auth->deactivate($id);
            $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User deactivated</div>");
            redirect('SuperAdmin/allusers');
        } else {
            $this->ion_auth->activate($id);
            $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Activated successfully</div>");
            redirect('SuperAdmin/allusers');
        }
        
    }

    public function addMoney()
    {
        $uid = $this->input->post('user_id');
        $chips = $this->input->post('chips');
        $old = $this->Common_model->get_single_query("select * from user_chips where user_id = $uid");
        $cd = $this->Common_model->get_single_query("select sum('credits') as c, sum(debits) as d from credits_debits where user_id = $this->id");
        $bal = $cd->c - $cd->d;
        $cbalance = $bal + $chips;
        $data = array(
            'user_id'           => $uid,
            'txnid'             => md5(microtime()),
            'free_chips'        => $this->input->post('free_chips'),
            'amount'            => $this->input->post('amount'),
            'credits'           => $chips,
            'balance'           => $cbalance,
            'description'       => $this->input->post('description'),
            'assigned_by'       => $this->id,
            'transaction_date'  => $this->input->post('transaction_date'),
            'expiry_date'       => $this->input->post('expiry_date'),
            'type'              => 'credit',
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits',$data);
        
        if($old) {
            $total_chips = $old->total_chips + $chips;
            $balance = $old->balanced_chips + $chips;
            $chipData = array(
                'user_id'           => $uid,
                'total_chips'       => $total_chips,
                'balanced_chips'    => $balance,
                'updated_at'        => date('Y-m-d H:i:s')
            );
            if($this->input->post('free_chips') == 'yes') {
                $chipData['free_chips'] = $old->free_chips + $chips;
            } else {
                $chipData['paid_chips'] = $old->paid_chips + $chips;
            }
            $this->Crud_model->edit_record('user_chips',$old->id,$chipData);
        } else {
            $total_chips = $chips;
            $balance = $chips;
            $chipData = array(
                'user_id'           => $uid,
                'total_chips'       => $total_chips,
                'balanced_chips'    => $balance,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s')
            );
            if($this->input->post('free_chips') == 'yes') {
                $chipData['free_chips'] = $chips;
            } else {
                $chipData['paid_chips'] = $chips;
            }
            $this->Crud_model->insert_record('user_chips',$chipData);
        }
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Credit Record Added</div>");
        redirect('SuperAdmin/allUsers');
    }

    public function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key   = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

    public function _valid_csrf_nonce()
    {
        $csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
        if ($csrfkey && $csrfkey == $this->session->flashdata('csrfvalue'))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function _outlist($response)
    {
        $outlist = array();
        foreach ($response as $value) {
            $value = (object)$value;
            $outlist[] = $value;
        }
        return $outlist;
    }
}