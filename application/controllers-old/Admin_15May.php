<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation'));
        $this->load->helper(array('url', 'language'));
        $this->load->model('Setting_model');
        $this->load->model('MyModel');
        $this->settings = $this->Setting_model->get_setting();
        if (!empty($this->settings->timezone)) {
            date_default_timezone_set($this->settings->timezone);
        } else {
            date_default_timezone_set("Asia/Kolkata");
        }
        if (!$this->ion_auth->logged_in()) {
            redirect('Auth/login');
        }
        if(!$this->ion_auth->is_admin()) {
            redirect('Auth');
        }
        $this->id = $this->session->userdata('user_id');
        $this->p_l = $this->Common_model->get_single_query("select sum('credits') as c, sum(debits) as d from credits_debits where user_id = $this->id and type='bet'");
    }

    public function index() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $this->load->view('layout/backend_header', $hdata);
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        foreach ($matches as $mkey => $m) {
            $odd = $this->matchOdd($m['market_id']);
            $runners = $odd['runners'];
            $matches[$mkey]['odds'] = $runners;
            $matches[$mkey]['status'] = $odd['inplay'];
        }
        $data['matches'] = $matches;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/index',$data);
        $this->load->view('layout/backend_footer');
    }

    public function changePassword() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/change_password');
        $this->load->view('layout/backend_footer');
    }

    public function updatePassword() {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        $old = $this->input->post('old');
        $new = $this->input->post('new');
        $new_confirm = $this->input->post('new_confirm');
        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-error alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' . (validation_errors()) ? validation_errors() : $this->session->flashdata('message') . '</div>');
            redirect('Admin/changePassword');
        } else {
            $identity = $this->session->userdata('identity');

            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

            if ($change) {
                //if the password was successfully changed
                $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" . $this->ion_auth->messages() . "</div>");
                redirect('Auth/logout');
            } else {
                $this->session->set_flashdata('message', "<div class='alert alert-error alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" . $this->ion_auth->errors() . "</div>");
                redirect('Admin/changePassword', 'refresh');
            }
        }
    }

    public function supermasters() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'supermaster' and deleted = 'no' order by id DESC"));
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'supermaster'");
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/supermasters',$data);
        $this->load->view('layout/backend_footer');
    }

    public function masters() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $smid = $this->input->get('supermaster_id');
        if(isset($smid)) {
            $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'master' and parent_id = $smid and deleted = 'no' order by id DESC"));
        } else {
            $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'master' and deleted = 'no' order by id DESC"));
        }
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'supermaster'");
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/masters',$data);
        $this->load->view('layout/backend_footer');
    }

    public function users() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $mid = $this->input->get('master_id');
        if(isset($mid)) {
            $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'user' and parent_id = $mid and deleted = 'no' order by id DESC"));
        } else {
            $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("SELECT * FROM `users_with_groups` WHERE group_name = 'user' AND deleted = 'no' ORDER BY id DESC"));
        }
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'supermaster'");
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/users',$data);
        $this->load->view('layout/backend_footer');
    }

    public function deletedUsers() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("SELECT * FROM `users_with_groups` WHERE deleted = 'yes' ORDER BY id DESC"));
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'supermaster'");
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/deleted_users',$data);
        $this->load->view('layout/backend_footer');
    }

    public function restoreUser($id) {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $data = array('deleted' => 'no');
        $this->Crud_model->edit_record('users',$id,$data);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Restored successfully</div>");
        redirect('Admin/deletedUsers');
    }

    public function resetUserPassword() {
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        $id = $this->input->post('user_id');
        $ug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
        if($this->form_validation->run() == true) {
            $id = $this->input->post('user_id');
            $ug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
            $new = $this->input->post('new');
            $q = $this->db->select('password, salt')->from('users')->where('id',$id)->get()->row();
            $hashed_new_password  = $this->ion_auth->hash_password($new, $q->salt);
            $data = array(
                'password' => $hashed_new_password,
                'remember_code' => NULL,
            );
            $this->db->where('id',$id)->update('users',$data);
            $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Password Changed successfully</div>");
            redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
        }
        $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
    }

    public function addChild() {
        $tables = $this->config->item('tables','ion_auth');
        $identity_column = $this->config->item('identity','ion_auth');
        $this->form_validation->set_rules('identity', 'Username', 'required|is_unique[' . $tables['users'] . '.username]');
        $this->form_validation->set_rules('parent_id', 'Parent Id', 'required');
        $this->form_validation->set_rules('groups', 'User Groups', 'required');
        if ($this->form_validation->run() == true)
        {
            $email    = $this->input->post('identity');
            $identity = $this->input->post('identity');
            $password = 'set123';
            $additional_data = array(
                'full_name'  => $this->input->post('full_name'),
                'parent_id'  => $this->input->post('parent_id'),
                'commission' => $this->input->post('commission')
            );
            $groups = array(
                'id' => $this->input->post('groups')
            );
            $id = $this->ion_auth->register($identity, $password, $email, $additional_data, $groups);
            $pid = $this->input->post('parent_id');
            $ug = $this->Common_model->findfield('users_with_groups','id',$pid,'group_name');
            if($id){
                $udata = array(
                    'user_id'       => $id,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                );
                $this->Crud_model->insert_record('user_settings',$udata);
                $cug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
                if($cug == 'user') {
                    $stakeData = array(
                        'chip_name_1'   => '1k',
                        'chip_value_1'  => 1000,
                        'chip_name_2'   => '5k',
                        'chip_value_2'  => 5000,
                        'chip_name_3'   => '10k',
                        'chip_value_3'  => 10000,
                        'chip_name_4'   => '25k',
                        'chip_value_4'  => 25000,
                        'chip_name_5'   => '50k',
                        'chip_value_5'  => 50000,
                        'chip_name_6'   => '1L',
                        'chip_value_6'  => 100000,
                        'user_id'       => $id,
                        'updated_at'    => date('Y-m-d h:i:s')
                    );
                    $stakeChip = $this->Common_model->get_single_query("select * from chip_setting where user_id = $id");
                    if ($stakeChip) {
                        $this->Crud_model->edit_record('chip_setting', $stakeChip->id, $stakeData);
                    } else {
                        $stakeData['created_at'] = date('Y-m-d h:i:s');
                        $this->Crud_model->insert_record('chip_setting', $stakeData);
                    }
                }
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
            } else {
                $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
            }
        } else {
            $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
        }
    }

    public function addUser() {
        $tables = $this->config->item('tables','ion_auth');
        $identity_column = $this->config->item('identity','ion_auth');
        $this->form_validation->set_rules('identity', 'Username', 'required|is_unique[' . $tables['users'] . '.username]');
        if ($this->form_validation->run() == true)
        {
            $email    = $this->input->post('identity');
            $identity = ($identity_column==='email') ? $email : $this->input->post('identity');
            $password = 'set123';
            $additional_data = array(
                'full_name'  => $this->input->post('full_name'),
                'parent_id'  => $this->id,
                'commission' => $this->input->post('commission')
            );
            $groups = array(
                'id' => 3
            );
            // check to see if we are creating the user
            // redirect them back to the admin page
            $id = $this->ion_auth->register($identity, $password, $email, $additional_data, $groups);
            $ug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
            if($id){
                $udata = array(
                    'user_id'       => $id,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                );
                $this->Crud_model->insert_record('user_settings',$udata);
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
            } else {
                $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                redirect('Admin/supermasters', 'refresh');
            }
            
        }
        else
        {
            $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            redirect('Admin/supermasters', 'refresh');
        }
    }

    public function editUser() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $data['user'] = $this->Common_model->get_single_query("select * from users_with_groups where id = $id");
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name='user'");
        $data['csrf'] = $this->_get_csrf_nonce();
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/edit_user',$data);
        $this->load->view('layout/backend_footer');
    }

    public function updateUser($id) {
        $tables = $this->config->item('tables','ion_auth');
        $ug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
        // validate form input
        
        if (isset($_POST) && !empty($_POST))
        {
            // do we have a valid request?
            if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
            {
                show_error($this->lang->line('error_csrf'));
            }

            $data = array(
                'full_name'     => $this->input->post('full_name'),
                'commission'    => $this->input->post('commission')
            );

            // update the password if it was posted
            if ($this->input->post('password'))
            {
                $data['password'] = $this->input->post('password');
            }

            // check to see if we are updating the user
           if($this->ion_auth->update($id, $data))
            {
                // redirect them back to the admin page if admin, or to the base url if non admin
                $this->session->set_flashdata('message', $this->ion_auth->messages() );
                redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));

            }
            else
            {
                // redirect them back to the admin page if admin, or to the base url if non admin
                $this->session->set_flashdata('message', $this->ion_auth->errors() );
                redirect('Admin/editUser?user_id='.$id);

            }
            
        }
    }

    public function activateUser()
    {
        $id = $this->input->get('id');
        $ug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
        $status = $this->input->get('status');
        if($status == 1) {
            $this->ion_auth->deactivate($id);
            $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User deactivated</div>");
            redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
        } else {
            $this->ion_auth->activate($id);
            $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Activated successfully</div>");
            redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
        }
        
    }

    public function userInfo() {
        $id = $this->input->get('user_id');
        $user = $this->Common_model->get_single_query("select * from user_settings where user_id = $id");
        echo json_encode($user);
    }

    public function deleteUser($id){
        $data = array('deleted'=>'yes');
        $this->Crud_model->edit_record('users',$id,$data);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Deleted successfully</div>");
    }

    public function lockBetting()
    {
        $id = $this->input->get('user_id');
        $ug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
        $status = $this->input->get('status');
        $data = array('lock_betting' => $status);
        $this->Crud_model->edit_record('users',$id,$data);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Betting Status updated</div>");
        redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
        
    }

    public function updateUserInfo() {
        $id = $this->input->post('user_id');
        $ug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
        $data = array(
            'max_stake'             => $this->input->post('max_stake'),
            'in_play_stake'         => $this->input->post('in_play_stake'),
            'max_profit_market'     => $this->input->post('max_profit_market'),
            'max_profit_fancy'      => $this->input->post('max_profit_fancy'),
            'bet_delay'             => $this->input->post('bet_delay'),
            'fancy_bet_delay'       => $this->input->post('fancy_bet_delay'),
            'updated_at'            => date('Y-m-d H:i:s'),
        );
        $this->Crud_model->edit_record_by_anyid('user_settings',$id,$data,'user_id');
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Info updated successfully</div>");
        redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
    }

    public function addMoney()
    {
        $uid = $this->input->post('user_id');
        $ug = $this->Common_model->findfield('users_with_groups','id',$uid,'group_name');
        $user = $this->Common_model->get_single_query("select * from users_with_groups where id = $uid");
        $chips = $this->input->post('chips');
        if($chips <= 0) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Please add some Chips first</div>");
            redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
        }
        $pid = $user->parent_id;
        $parentChips = $this->Common_model->findfield('user_chips','user_id',$pid,'balanced_chips');
        if($ug != 'supermaster' && $parentChips <= 0) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Please add some Chips to parent first</div>");
            redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
        }
        if($ug != 'supermaster' && $chips > $parentChips) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Chips can not be more than parent balanced chips</div>");
            redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
        }
        $old = $this->Common_model->get_single_query("select * from user_chips where user_id = $uid");
        $cd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $uid");
        $bal = $cd->c - $cd->d;
        $cbalance = $bal + $chips;
        $date = new DateTime($this->input->post('transaction_date'));
        $now = new DateTime('now');
        $time = $date->diff($now);
        $date->add($time);
        $data = array(
            'user_id'           => $uid,
            'txnid'             => md5(microtime()),
            'free_chips'        => $this->input->post('free_chips'),
            'amount'            => $this->input->post('amount'),
            'credits'           => $chips,
            'credited_from'     => $pid,
            'balance'           => $cbalance,
            'description'       => $this->input->post('description'),
            'assigned_by'       => $this->id,
            'transaction_date'  => $date->format('Y-m-d H:i:s'),
            'expiry_date'       => $this->input->post('expiry_date'),
            'type'              => 'credit',
            'settled'           => 'yes',
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits',$data);
        
        if($old) {
            $total_chips = $old->total_chips + $chips;
            $balance = $old->balanced_chips + $chips;
            $chipData = array(
                'user_id'           => $uid,
                'total_chips'       => $total_chips,
                'balanced_chips'    => $old->balanced_chips + $chips,
                'current_chips'     => $old->current_chips + $chips,
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
                'current_chips'     => $balance,
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
        if($ug=='user') {
            $mchips = $this->Common_model->get_single_query("select * from user_chips where user_id = $user->parent_id");
            $mcd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $user->parent_id");
        } elseif($ug=='master') {
            $mchips = $this->Common_model->get_single_query("select * from user_chips where user_id = $user->parent_id");
            $mcd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $user->parent_id");
        } else {
            $mchips = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
            $mcd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $this->id");
        }
        if($ug == 'supermaster') {
            $mcdata = array(
                'user_id'           => $this->id,
                'txnid'             => md5(microtime()),
                'debits'            => $chips,
                'debited_to'        => $uid,
                'balance'           => '',
                'description'       => 'Transferred to '.$this->Common_model->findfield('users','id',$uid,'username'),
                'transferred_to'    => $uid,
                'transaction_date'  => date('Y-m-d H:i:s'),
                'type'              => 'debit',
                'settled'           => 'yes',
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('credits_debits',$mcdata);
        } else {
            $mdata = array(
                'balanced_chips'    => $mchips->balanced_chips - $chips,
                'current_chips'     => $balance,
                'spent_chips'       => $mchips->spent_chips + $chips,
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->edit_record('user_chips',$mchips->id,$mdata);
            $mbal = $mcd->c - $mcd->d;
            $mcbalance = $mbal - $chips;
            $mcdata = array(
                'user_id'           => $user->parent_id,
                'txnid'             => md5(microtime()),
                'debits'            => $chips,
                'debited_to'        => $uid,
                'balance'           => $mcbalance,
                'description'       => 'Transferred to '.$this->Common_model->findfield('users','id',$uid,'username'),
                'transferred_to'    => $uid,
                'transaction_date'  => date('Y-m-d H:i:s'),
                'type'              => 'debit',
                'settled'           => 'yes',
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('credits_debits',$mcdata);
        }
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Credit Record Added</div>");
        redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
    }

    public function witdrawChips() {
        $uid = $this->input->post('user_id');
        $chips = $this->input->post('chips');
        $ug = $this->Common_model->findfield('users_with_groups','id',$uid,'group_name');
        $maxChips = $this->Common_model->findfield('user_chips','user_id',$uid,'balanced_chips');
        if($chips > $maxChips) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>WIthdraw Chips can not be more than balanced chips</div>");
            redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
        }
        $pid = $this->Common_model->findfield('users','id',$uid,'parent_id');
        $data = array(
            'user_id'           => $uid,
            'txnid'             => md5(microtime()),
            'debits'            => $chips,
            'debited_to'        => $pid,
            'balance'           => $maxChips - $chips,
            'description'       => 'Chips Withdrawn by '.$this->Common_model->findfield('users','id',$pid, 'username'),
            'assigned_by'       => $this->id,
            'transaction_date'  => date('Y-m-d H:i:s'),
            'type'              => 'debit',
            'settled'           => 'yes',
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits',$data);
        $chipData = array(
            'withdraw_chips'    => $this->Common_model->findfield('user_chips','user_id',$uid,'withdraw_chips') + $chips,
            'balanced_chips'    => $maxChips - $chips,
            'current_chips'     => $maxChips - $chips,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips',$uid,$chipData,'user_id');
        //parent record
        $pchips = $this->Common_model->findfield('user_chips','user_id',$pid,'balanced_chips');
        $pdata = array(
            'user_id'           => $pid,
            'txnid'             => md5(microtime()),
            'credits'           => $chips,
            'credited_from'     => $uid,
            'balance'           => $pchips + $chips,
            'description'       => 'Chips Withdrawn from '.$this->Common_model->findfield('users','id',$uid, 'username'),
            'assigned_by'       => $this->id,
            'transaction_date'  => date('Y-m-d H:i:s'),
            'type'              => 'credit',
            'settled'           => 'yes',
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits',$pdata);
        $pchipData = array(
            'balanced_chips'    => $pchips + $chips,
            'current_chips'     => $pchips + $chips,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips',$pid,$pchipData,'user_id');
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>chips has been withdrawn from user</div>");
        redirect('Admin/'.($ug=='user'?'users': ($ug == 'master' ? 'masters' : 'supermasters')));
    }

    public function userBetHistory() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $ug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
        if($ug == 'supermaster'){
            $data['bets'] = $this->Common_model->get_data_by_query("SELECT * FROM `bet` WHERE user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $id)) ORDER BY id DESC");
        } elseif($ug == 'master') {
            $data['bets'] = $this->Common_model->get_data_by_query("SELECT * FROM `bet` WHERE user_id IN (SELECT id FROM users WHERE parent_id = $id) ORDER BY id DESC");
        } else {
            $data['bets'] = $this->Common_model->get_data_by_query("select * from bet where user_id = $id order by id DESC");
        }
        //echo $this->db->last_query();die;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/userbet_history',$data);
        $this->load->view('layout/backend_footer');
    }

    public function userAccountStatement() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $ug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
        $statements = $this->Common_model->get_data_by_query("SELECT *  FROM `credits_debits` where user_id = $id order by id desc");
        $outlist = array();
        foreach ($statements as $skey => $s) {
            $match_id = $s['match_id'];
            if(!isset($match_id)) {
                $match_id = uniqid();
            }
            if (array_key_exists($match_id, $outlist)) {
                $outlist[$match_id][] = $s;
            } else {
                $outlist[$match_id] = array($s);
            }
        }
        $st = array();
        foreach ($outlist as $key => $o) {
            $st[] = $o[0];
        }

        $data['statements'] = $st;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/useraccount_statement',$data);
        $this->load->view('layout/backend_footer');
    }

    public function statementByMatchId() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $match_id = $this->input->get('match_id');
        $uid = $this->input->get('user_id');
        $data['statements'] = $this->Common_model->get_data_by_query("select s.*, b.id as bid, b.bet_type from credits_debits s left join bet b on s.bet_id = b.id where s.user_id = $uid and s.match_id = $match_id order by b.bet_type");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/statementby_matchid',$data);
        $this->load->view('layout/backend_footer');
    }

    public function oddFancyByMatchId() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);

        $match_id = $this->input->get('match_id');
        $uid = $this->input->get('user_id');
        $type = $this->input->get('type');

        //user calculation
        $up = array(); $down = array(); 
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a LEFT JOIN bet b ON a.bet_id = b.id WHERE a.user_id = $uid AND a.match_id = $match_id AND b.bet_type = '$type'");
        $ubal = $ucd->c - $ucd->d;

        if($ubal == 0) {

        } elseif($ubal > 0) {
            $up[0]['username']    = 'Own';
            $up[0]['name']        = $user->full_name;
            $up[0]['uid']         = $uid;
            $up[0]['chips']       = abs($ubal);
        } else {
            $down[0]['username']  = 'Own';
            $down[0]['name']      = $user->full_name;
            $down[0]['uid']       = $uid;
            $down[0]['chips']     = abs($ubal);
        }

        //child calculation
        $childs = $this->Common_model->get_data_by_query("select id from users where parent_id = $uid");
        $cup = array(); $cdown = array();
        foreach ($childs as $ckey => $c) {
            $cid = $c['id'];
            $child = $this->Common_model->get_single_query("select * from users_with_groups where id = $cid");
            $ccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a LEFT JOIN bet b ON a.bet_id = b.id WHERE a.user_id = $cid AND a.match_id = $match_id AND b.bet_type = '$type' AND a.credited_from = $uid");
            $cdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a LEFT JOIN bet b ON a.bet_id = b.id WHERE a.user_id = $cid AND a.match_id = $match_id AND b.bet_type = '$type' AND a.debited_to = $uid");
            $cbal = $ccredit->c - $cdebit->d;
            if($cbal == 0) {

            } elseif($cbal > 0) {
                $cup[$ckey]['name']     = $child->full_name;
                $cup[$ckey]['username'] = $child->username;
                $cup[$ckey]['uid']      = $cid;
                $cup[$ckey]['chips']    = abs($cbal);
            } else {
                $cdown[$ckey]['name']     = $child->full_name;
                $cdown[$ckey]['username'] = $child->username;
                $cdown[$ckey]['uid']      = $cid;
                $cdown[$ckey]['chips']    = abs($cbal);
            }
        }

        //parent calculation
        $pid = $this->Common_model->findfield('users','id',$uid,'parent_id');
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pcredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a LEFT JOIN bet b ON a.bet_id = b.id WHERE a.user_id = $pid AND a.match_id = $match_id AND b.bet_type = '$type' AND a.credited_from = $uid");
        $pdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a LEFT JOIN bet b ON a.bet_id = b.id WHERE a.user_id = $pid AND a.match_id = $match_id AND b.bet_type = '$type' AND a.debited_to = $uid");
        $pbal = $pcredit->c - $pdebit->d;
        $pup = array(); $pdown = array();
        if($pbal == 0) {

        } elseif($pbal > 0) {
            $pup[0]['username']    = 'Parent A/C';
            $pup[0]['name']        = $puser->full_name;
            $pup[0]['uid']         = $pid;
            $pup[0]['chips']       = abs($pbal);
        } else {
            $pdown[0]['username']  = 'Parent A/C';
            $pdown[0]['name']      = $puser->full_name;
            $pdown[0]['uid']       = $pid;
            $pdown[0]['chips']     = abs($pbal);
        }
        $cpup = array(); $cpdown = array();
        $cpup = array_merge($pup,$cup);
        $cpdown = array_merge($pdown,$cdown);
        $plus = array(); $minus = array();
        $plus = array_merge($cpup,$up);
        $minus = array_merge($cpdown,$down);
        $data['type'] = $type;
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['bets'] = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE id IN(SELECT DISTINCT a.bet_id FROM credits_debits a LEFT JOIN bet b ON b.id = a.bet_id WHERE a.user_id = $uid AND a.type = 'bet' AND a.match_id = $match_id AND b.bet_type = '$type')");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/oddfancyby_matchid',$data);
        $this->load->view('layout/backend_footer');
    }

    public function userProfitLoss() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $ug = $this->Common_model->findfield('users_with_groups','id',$id,'group_name');
        $data['profitLosses'] = $this->Common_model->get_data_by_query("select * from profit_loss where user_id = $id");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/userprofit_loss',$data);
        $this->load->view('layout/backend_footer');
    }

    public function bet() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('bet_id');
        $data['bet'] = $this->Common_model->get_single_query("select * from bet where id = $id");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/bet',$data);
        $this->load->view('layout/backend_footer');
    }

    public function accountInfo() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $data['up'] = $this->Common_model->get_single_query("SELECT SUM(credits) as up FROM `credits_debits` WHERE user_id = $this->id AND type='bet'");
        $data['down'] = $this->Common_model->get_single_query("SELECT SUM(debits) as down FROM `credits_debits` WHERE user_id = $this->id AND type='bet'");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/account_info',$data);
        $this->load->view('layout/backend_footer');
    }

    public function accountStatement() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $statements = $this->Common_model->get_data_by_query("SELECT *  FROM `credits_debits` where user_id = $this->id order by id desc");
        $outlist = array();
        foreach ($statements as $skey => $s) {
            $match_id = $s['match_id'];
            if(!isset($match_id)) {
                $match_id = uniqid();
            }
            if (array_key_exists($match_id, $outlist)) {
                $outlist[$match_id][] = $s;
            } else {
                $outlist[$match_id] = array($s);
            }
        }
        $st = array();
        foreach ($outlist as $key => $o) {
            $st[] = $o[0];
        }

        $data['statements'] = $st;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/account_statement',$data);
        $this->load->view('layout/backend_footer');
    }

    public function chipHistory() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $data['history'] = $this->Common_model->get_data_by_query("SELECT a.*, b.username, c.id as bid, c.match_name, c.team, c.bet_type, c.market, d.winner FROM `credits_debits` a LEFT JOIN users b ON a.user_id = b.id LEFT JOIN bet c ON a.bet_id = c.id LEFT JOIN running_matches d ON c.market_id = d.market_id WHERE a.user_id = $this->id AND a.type='bet' ORDER BY a.id DESC");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/chip_history',$data);
        $this->load->view('layout/backend_footer');
    }

    public function chipSummary() {
        $uid = $this->input->get('user_id');
        $group = $this->Common_model->findfield('users_with_groups','id',$uid,'group_name');
        if($group == 'user') {
            redirect('Admin/userChipSummary?user_id='.$uid);
        } elseif($group == 'master') {
            redirect('Admin/masterChipSummary?user_id='.$uid);
        } elseif($group == 'supermaster') {
            redirect('Admin/superMasterChipSummary?user_id='.$uid);
        } elseif($group == 'admin') {
            redirect('Admin/adminChipSummary?user_id='.$uid);
        }
        
    }

    public function userChipSummary() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array(); $down = array(); 
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet'");
        $ubal = $ucd->c - $ucd->d;

        if($ubal == 0) {

        } elseif($ubal > 0) {
            $up[0]['username']    = 'Own';
            $up[0]['name']        = $user->full_name;
            $up[0]['uid']         = $uid;
            $up[0]['chips']       = abs($ubal);
        } else {
            $down[0]['username']  = 'Own';
            $down[0]['name']      = $user->full_name;
            $down[0]['uid']       = $uid;
            $down[0]['chips']     = abs($ubal);
        }
        //parent
        $pid = $this->Common_model->findfield('users','id',$uid,'parent_id');
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pcredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.credited_from = $uid");
        $pdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.debited_to = $uid");
        $pbal = $pcredit->c - $pdebit->d;
        $pup = array(); $pdown = array();
        if($pbal == 0) {

        } elseif($pbal > 0) {
            $pup[0]['username']    = 'Parent A/C';
            $pup[0]['name']        = $puser->full_name;
            $pup[0]['uid']         = $pid;
            $pup[0]['chips']       = abs($pbal);
        } else {
            $pdown[0]['username']  = 'Parent A/C';
            $pdown[0]['name']      = $puser->full_name;
            $pdown[0]['uid']       = $pid;
            $pdown[0]['chips']     = abs($pbal);
        }
        $plus = array(); $minus = array();
        $plus = array_merge($pup,$up);
        $minus = array_merge($pdown,$down);
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/userchip_summary',$data);
        $this->load->view('layout/backend_footer');
    }

    public function masterChipSummary() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array(); $down = array(); 
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet'");
        $ubal = $ucd->c - $ucd->d;

        if($ubal == 0) {

        } elseif($ubal > 0) {
            $up[0]['username']    = 'Own';
            $up[0]['name']        = $user->full_name;
            $up[0]['uid']         = $uid;
            $up[0]['chips']       = abs($ubal);
        } else {
            $down[0]['username']  = 'Own';
            $down[0]['name']      = $user->full_name;
            $down[0]['uid']       = $uid;
            $down[0]['chips']     = abs($ubal);
        }
        //parent
        $pid = $this->Common_model->findfield('users','id',$uid,'parent_id');
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pcredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.credited_from = $uid");
        $pdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.debited_to = $uid");
        $pbal = $pcredit->c - $pdebit->d;
        
        $pup = array(); $pdown = array();
        if($pbal == 0) {

        } elseif($pbal > 0) {
            $pup[0]['username']    = 'Parent A/C';
            $pup[0]['name']        = $puser->full_name;
            $pup[0]['uid']         = $pid;
            $pup[0]['chips']       = abs($pbal);
        } else {
            $pdown[0]['username']  = 'Parent A/C';
            $pdown[0]['name']      = $puser->full_name;
            $pdown[0]['uid']       = $pid;
            $pdown[0]['chips']     = abs($pbal);
        }

        //child
        $childs = $this->Common_model->get_data_by_query("SELECT * FROM users_with_groups WHERE parent_id = $uid");
        $cup = array(); $cdown = array();
        foreach ($childs as $ck => $c) {
           $cid = $c['id'];
           $ccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.credited_from = $uid");
            $cdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.debited_to = $uid");
            $cbal = $ccredit->c - $cdebit->d;
            $sccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.credited_from = $uid");
            $scdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.debited_to = $uid");
            $scbal = $sccredit->c - $scdebit->d;
            
            if($cbal < 0 && $scbal < 0) {
                if($cbal < $scbal) {
                    $fcbal = $cbal - ($scbal);
                } else {
                    $fcbal = $scbal - ($cbal);
                }
                
            } elseif($cbal > 0 && $scbal < 0) {
                $fcbal = $cbal + ($scbal);
            } elseif($cbal < 0 && $scbal > 0) {
                $fcbal = $cbal + ($scbal);
            } else {
                $fcbal = $cbal - $scbal;
            }
            //echo $c['username'].' ### '.$cbal.' **** '.$scbal.' $$$ '.$fcbal.'<hr/>';
            if($fcbal == 0) {

            } elseif($fcbal > 0) {
                $cp['name']     = $c['full_name'];
                $cp['username'] = $c['username'];
                $cp['uid']      = $cid;
                $cp['chips']    = abs($fcbal);
                $cup[] = $cp;
            } else {
                $cn['name']     = $c['full_name'];
                $cn['username'] = $c['username'];
                $cn['uid']      = $cid;
                $cn['chips']    = abs($fcbal);
                $cdown[] = $cn;
            }
        }
        //die;
        //settlement calculation
        $stc = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'settlement' AND a.credited_from IN (SELECT id FROM users WHERE parent_id = $uid)");
        $std = $this->Common_model->get_single_query("SELECT SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'settlement' AND a.debited_to IN (SELECT id FROM users WHERE parent_id = $uid)");
        $sbal = $stc->c -$std->d;
        $sup = array(); $sdown = array();
        if($sbal == 0) {

        } elseif($sbal > 0) {
            $sp['username']    = 'Cash';
            $sp['name']        = $user->username;
            $sp['uid']         = $uid;
            $sp['chips']       = abs($sbal);
            $sup[] = $sp;
        } else {
            $sn['username']  = 'Cash';
            $sn['name']      = $user->username;
            $sn['uid']       = $uid;
            $sn['chips']     = abs($sbal);
            $sdown[] = $sn;
        }

        $plus = array(); $minus = array();
        $plus = array_merge($pup,$cup,$up,$sup);
        $minus = array_merge($pdown,$cdown,$down,$sdown);
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/masterchip_summary',$data);
        $this->load->view('layout/backend_footer');
    }

    public function superMasterChipSummary() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array(); $down = array(); 
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet'");
        $ubal = $ucd->c - $ucd->d;

        if($ubal == 0) {

        } elseif($ubal > 0) {
            $up[0]['username']    = 'Own';
            $up[0]['name']        = $user->full_name;
            $up[0]['uid']         = $uid;
            $up[0]['chips']       = abs($ubal);
        } else {
            $down[0]['username']  = 'Own';
            $down[0]['name']      = $user->full_name;
            $down[0]['uid']       = $uid;
            $down[0]['chips']     = abs($ubal);
        }
        //parent
        $pid = $this->Common_model->findfield('users','id',$uid,'parent_id');
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pcredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.credited_from = $uid");
        $pdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.debited_to = $uid");
        $pbal = $pcredit->c - $pdebit->d;
        
        $pup = array(); $pdown = array();
        if($pbal == 0) {

        } elseif($pbal > 0) {
            $pup[0]['username']    = 'Parent A/C';
            $pup[0]['name']        = $puser->full_name;
            $pup[0]['uid']         = $pid;
            $pup[0]['chips']       = abs($pbal);
        } else {
            $pdown[0]['username']  = 'Parent A/C';
            $pdown[0]['name']      = $puser->full_name;
            $pdown[0]['uid']       = $pid;
            $pdown[0]['chips']     = abs($pbal);
        }

        //child
        $childs = $this->Common_model->get_data_by_query("SELECT * FROM users_with_groups WHERE parent_id = $uid");
        $cup = array(); $cdown = array();
        foreach ($childs as $ck => $c) {
           $cid = $c['id'];
           $ccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.credited_from = $uid");
            $cdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.debited_to = $uid");
            $cbal = $ccredit->c - $cdebit->d;
            $sccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.credited_from = $uid");
            $scdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.debited_to = $uid");
            $scbal = $sccredit->c - $scdebit->d;
            if($cbal < 0 && $scbal < 0) {
                $fcbal = $cbal + ($scbal);
            } elseif($cbal > 0 && $scbal < 0) {
                $fcbal = $cbal + ($scbal);
            } elseif($cbal < 0 && $scbal > 0) {
                $fcbal = $cbal + ($scbal);
            } else {
                $fcbal = $cbal - $scbal;
            }
            if($fcbal == 0) {

            } elseif($fcbal > 0) {
                $cp['name']     = $c['full_name'];
                $cp['username'] = $c['username'];
                $cp['uid']      = $cid;
                $cp['chips']    = abs($fcbal);
                $cup[] = $cp;
            } else {
                $cn['name']     = $c['full_name'];
                $cn['username'] = $c['username'];
                $cn['uid']      = $cid;
                $cn['chips']    = abs($fcbal);
                $cdown[] = $cn;
            }
        }
        //settlement calculation
        $stc = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'settlement' AND a.credited_from IN (SELECT id FROM users WHERE parent_id = $uid)");
        $std = $this->Common_model->get_single_query("SELECT SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'settlement' AND a.debited_to IN (SELECT id FROM users WHERE parent_id = $uid)");
        $sbal = $stc->c -$std->d;
        $sup = array(); $sdown = array();
        if($sbal == 0) {

        } elseif($sbal > 0) {
            $sp['username']    = 'Cash';
            $sp['name']        = $user->username;
            $sp['uid']         = $uid;
            $sp['chips']       = abs($sbal);
            $sup[] = $sp;
        } else {
            $sn['username']  = 'Cash';
            $sn['name']      = $user->username;
            $sn['uid']       = $uid;
            $sn['chips']     = abs($sbal);
            $sdown[] = $sn;
        }

        $plus = array(); $minus = array();
        $plus = array_merge($pup,$cup,$up,$sup);
        $minus = array_merge($pdown,$cdown,$down,$sdown);
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/supermasterchip_summary',$data);
        $this->load->view('layout/backend_footer');
    }

    public function adminChipSummary() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array(); $down = array(); 
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet'");
        $ubal = $ucd->c - $ucd->d;

        if($ubal == 0) {

        } elseif($ubal > 0) {
            $up[0]['username']    = 'Own';
            $up[0]['name']        = $user->full_name;
            $up[0]['uid']         = $uid;
            $up[0]['chips']       = abs($ubal);
        } else {
            $down[0]['username']  = 'Own';
            $down[0]['name']      = $user->full_name;
            $down[0]['uid']       = $uid;
            $down[0]['chips']     = abs($ubal);
        }
        
        //child
        $childs = $this->Common_model->get_data_by_query("SELECT * FROM users_with_groups WHERE parent_id = $uid");
        $cup = array(); $cdown = array();
        foreach ($childs as $ck => $c) {
           $cid = $c['id'];
           $ccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.credited_from = $uid");
            $cdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.debited_to = $uid");
            $cbal = $ccredit->c - $cdebit->d;
            $sccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.credited_from = $uid");
            $scdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.debited_to = $uid");
            $scbal = $sccredit->c - $scdebit->d;
            if($cbal < 0 && $scbal < 0) {
                $fcbal = $cbal + ($scbal);
            } elseif($cbal > 0 && $scbal < 0) {
                $fcbal = $cbal + ($scbal);
            } elseif($cbal < 0 && $scbal > 0) {
                $fcbal = $cbal + ($scbal);
            } else {
                $fcbal = $cbal - $scbal;
            }
            if($fcbal == 0) {

            } elseif($fcbal > 0) {
                $cp['name']     = $c['full_name'];
                $cp['username'] = $c['username'];
                $cp['uid']      = $cid;
                $cp['chips']    = abs($fcbal);
                $cup[] = $cp;
            } else {
                $cn['name']     = $c['full_name'];
                $cn['username'] = $c['username'];
                $cn['uid']      = $cid;
                $cn['chips']    = abs($fcbal);
                $cdown[] = $cn;
            }
        }
        //settlement calculation
        $stc = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'settlement' AND a.credited_from IN (SELECT id FROM users WHERE parent_id = $uid)");
        $std = $this->Common_model->get_single_query("SELECT SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'settlement' AND a.debited_to IN (SELECT id FROM users WHERE parent_id = $uid)");
        $sbal = $stc->c -$std->d;
        $sup = array(); $sdown = array();
        if($sbal == 0) {

        } elseif($sbal > 0) {
            $sp['username']    = 'Cash';
            $sp['name']        = $user->username;
            $sp['uid']         = $uid;
            $sp['chips']       = abs($sbal);
            $sup[] = $sp;
        } else {
            $sn['username']  = 'Cash';
            $sn['name']      = $user->username;
            $sn['uid']       = $uid;
            $sn['chips']     = abs($sbal);
            $sdown[] = $sn;
        }

        $plus = array(); $minus = array();
        $plus = array_merge($cup,$up,$sup);
        $minus = array_merge($cdown,$down,$sdown);
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/adminchip_summary',$data);
        $this->load->view('layout/backend_footer');
    }

    public function chipSummaryOld() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);

        $uid = $this->input->get('user_id');
        $type = 'bet';

        //user calculation
        $up = array(); $down = array(); 
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = '$type'");
        $ubal = $ucd->c - $ucd->d;

        if($ubal == 0) {

        } elseif($ubal > 0) {
            $up[0]['username']    = 'Own';
            $up[0]['name']        = $user->full_name;
            $up[0]['uid']         = $uid;
            $up[0]['chips']       = abs($ubal);
        } else {
            $down[0]['username']  = 'Own';
            $down[0]['name']      = $user->full_name;
            $down[0]['uid']       = $uid;
            $down[0]['chips']     = abs($ubal);
        }

        //child calculation
        $childs = $this->Common_model->get_data_by_query("select id from users where parent_id = $uid");
        $cup = array(); $cdown = array();
        foreach ($childs as $ckey => $c) {
            $cid = $c['id'];
            $child = $this->Common_model->get_single_query("select * from users_with_groups where id = $cid");
            $ccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = '$type' AND a.credited_from = $uid");
            $cdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = '$type' AND a.debited_to = $uid");
            $cbal = $ccredit->c - $cdebit->d;
            $sccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.credited_from = $uid");
            $scdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.debited_to = $uid");
            $scbal = $sccredit->c - $scdebit->d;
            if($cbal == 0) {

            } elseif($cbal > 0) {
                $cup[$ckey]['name']     = $child->full_name;
                $cup[$ckey]['username'] = $child->username;
                $cup[$ckey]['uid']      = $cid;
                $cup[$ckey]['chips']    = abs(abs($cbal) - abs($scbal));

            } else {
                $cdown[$ckey]['name']     = $child->full_name;
                $cdown[$ckey]['username'] = $child->username;
                $cdown[$ckey]['uid']      = $cid;
                $cdown[$ckey]['chips']    = abs(abs($cbal) - abs($scbal));

            }
        }
        //parent calculation
        $pid = $this->Common_model->findfield('users','id',$uid,'parent_id');
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pcredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $pid AND a.type = '$type' AND a.credited_from = $uid");
        $pdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $pid AND a.type = '$type' AND a.debited_to = $uid");
        $pbal = $pcredit->c - $pdebit->d;
        $pccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'settlement' AND a.credited_from = $uid");
        $pcdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'settlement' AND a.debited_to = $uid");
        $pcbal = $pccredit->c - $pcdebit->d;
        
        $pup = array(); $pdown = array();
        if($pbal == 0) {

        } elseif($pbal > 0) {
            $pup[0]['username']    = 'Parent A/C';
            $pup[0]['name']        = $puser->full_name;
            $pup[0]['uid']         = $pid;
            $pup[0]['chips']       = abs(abs($pbal) - abs($pcbal));

        } else {
            $pdown[0]['username']  = 'Parent A/C';
            $pdown[0]['name']      = $puser->full_name;
            $pdown[0]['uid']       = $pid;
            $pdown[0]['chips']     = abs(abs($pbal) - abs($pcbal));
            
        }
        //settlement calculation
        $sdc = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'settlement'");
        $sbal = $sdc->c -$sdc->d;
        $sup = array(); $sdown = array();
        if($sbal == 0) {

        } elseif($sbal > 0) {
            $sup['username']    = $user->username;
            $sup['name']        = 'Cash';
            $sup['uid']         = $uid;
            $sup['chips']       = abs($sbal);
        } else {
            $sdown['username']  = $user->username;
            $sdown['name']      = 'Cash';
            $sdown['uid']       = $uid;
            $sdown['chips']     = abs($sbal);
        }
        $cpup = array(); $cpdown = array();
        $cpup = array_merge($pup,$cup);
        $cpdown = array_merge($pdown,$cdown);
        $plus = array(); $minus = array();
        $plus = array_merge($cpup,$up);
        $minus = array_merge($cpdown,$down);
        $data['type'] = $type;
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $data['sup'] = $sup;
        $data['sdown'] = $sdown;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/chip_summary',$data);
        $this->load->view('layout/backend_footer');
    }

    public function chipSettlement() {
        $cid = $this->input->post('cuser_id');//current user id
        $chips = $this->input->post('chips');
        $uid = $this->input->post('user_id');
        $type = $this->input->post('type');
        $message = $this->input->post('message');
        $ug = $this->Common_model->findfield('users_with_groups','id',$uid,'group_name');
        $pid = $this->Common_model->findfield('users','id',$uid,'parent_id');
        if($type == 'plus') {
            $wdata = array(
                'user_id'       => $uid,
                'debits'        => $chips,
                'debited_to'    => $pid,
                'assigned_by'   => $this->id,
                'message'       => $message,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'type'          => 'settlement'
            );
            $this->Crud_model->insert_record('chips_withdraw',$wdata);
            $udcdata = array(
                'txnid'             => md5(microtime()),         
                'user_id'           => $uid,
                'credits'           => 0,
                'credited_from'     => 0,
                'debits'            => $chips,
                'debited_to'        => $pid,
                'balance'           => 0,
                'assigned_by'       => $this->id,
                'transaction_date'  => date('Y-m-d H:i:s'),
                'description'       => 'settleme',
                'type'              => 'settlement',
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('credits_debits',$udcdata);
            $pdcdata = array(
                'txnid'             => md5(microtime()),         
                'user_id'           => $pid,
                'credits'           => $chips,
                'credited_from'     => $uid,
                'debits'            => 0,
                'debited_to'        => 0,
                'balance'           => 0,
                'assigned_by'       => $this->id,
                'transaction_date'  => date('Y-m-d H:i:s'),
                'description'       => $message,
                'type'              => 'settlement',
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('credits_debits',$pdcdata);
        } else {
            $wdata = array(
                'user_id'       => $uid,
                'credits'       => $chips,
                'credited_from' => $pid,
                'assigned_by'   => $this->id,
                'message'       => $message,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'type'          => 'settlement'
            );
            $this->Crud_model->insert_record('chips_withdraw',$wdata);
            $udcdata = array(
                'txnid'             => md5(microtime()),         
                'user_id'           => $uid,
                'credits'           => $chips,
                'credited_from'     => $pid,
                'debits'            => 0,
                'debited_to'        => 0,
                'balance'           => 0,
                'assigned_by'       => $this->id,
                'transaction_date'  => date('Y-m-d H:i:s'),
                'description'       => $message,
                'type'              => 'settlement',
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('credits_debits',$udcdata);
            $pdcdata = array(
                'txnid'             => md5(microtime()),         
                'user_id'           => $pid,
                'credits'           => 0,
                'credited_from'     => 0,
                'debits'            => $chips,
                'debited_to'        => $uid,
                'balance'           => 0,
                'assigned_by'       => $this->id,
                'transaction_date'  => date('Y-m-d H:i:s'),
                'description'       => $message,
                'type'              => 'settlement',
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('credits_debits',$pdcdata);
        }
        $this->updateDCBal($uid);
        $this->updateDCBal($pid);
        $this->updateBal($uid);
        $this->updateBal($pid);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User account has been settled</div>");
        redirect('Admin/chipSummary?user_id='.$cid);
    }

    public function profitLoss() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $statements = $this->Common_model->get_data_by_query("SELECT a.*, b.id as bid, b.market FROM credits_debits a LEFT JOIN bet b on a.bet_id = b.id where a.user_id = $this->id AND a.type = 'bet' order by a.id desc");
        $outlist = array();
        foreach ($statements as $skey => $s) {
            $match_id = $s['match_id'];
            if(!isset($match_id)) {
                $match_id = uniqid();
            }
            if (array_key_exists($match_id, $outlist)) {
                $outlist[$match_id][] = $s;
            } else {
                $outlist[$match_id] = array($s);
            }
        }
        $st = array();
        foreach ($outlist as $key => $o) {
            $st[] = $o[0];
        }

        foreach ($st as $ss => $sv) {
            $cd = $this->Common_model->get_single_query("SELECT SUM(credits) AS c, SUM(debits) AS d FROM credits_debits WHERE match_id = ".$sv['match_id']." AND user_id = ".$sv['user_id']."");
            $credits = $cd->c;
            $debits = $cd->d;
            $pl = $credits - $debits;
            $cl = $pl >= 0 ? 'text-success' : 'text-danger';
            $st[$ss]['p_l'] = $pl;
            $st[$ss]['c_l'] = $cl;
        }

        $data['statements'] = $st;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/profit_loss',$data);
        $this->load->view('layout/backend_footer');
    }

    public function betByMatchId() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $mid = $this->input->get('match_id');
        $data['bets'] = $this->Common_model->get_data_by_query("select * from bet where match_id = $mid order by id DESC");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/bet_matchid',$data);
        $this->load->view('layout/backend_footer');
    }

    public function betHistory() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $data['bets'] = $this->Common_model->get_data_by_query("SELECT a.*, b.username FROM bet a LEFT JOIN users b ON a.user_id = b.id ORDER BY a.id DESC");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/bet_history',$data);
        $this->load->view('layout/backend_footer');
    }

    public function getData() {
        $url = "http://master.heavyexch.com/api/markets/";
        //$url = "http://cricket.royalebet.uk/";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0),
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $result = json_decode($response, true);

        return $result;
    }

    public function getCricket() {
        $data = $this->getData();
        //$mdata = $data['result'];
        $cricket = array();
        foreach ($data as $key => $d) {
           if($d['SportID'] == 4 && $d['name'] == 'Match Odds') {
            $cricket[] = $data[$key];
           }
        }
        return $cricket;
    }

    public function matchOdd($marketId) {
        $url = "http://rohitash.dream24.bet:3000/getmarket?id=".$marketId;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0),
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        return $result;
        //print_r($result);
    }

    public function fileData() {
        $result = file_get_contents('./uploads/cricket.json');
        return json_decode($result, true);
    }

    public function fancyData($marketId) {
        //$marketId = $this->input->get('market_id');
        //$url = "http://fancy.royalebet.uk/".$eid;
        //$url = "http://fancy.royalebet.uk/";
        $url = "http://fancy.dream24.bet/price/?name=".$marketId;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0),
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        return $result;
        //print_r($result);die;
    }

    public static function modify($json_data) {
       $data = '}{"status":{"statusCode"';
       $output = $json_data;
       $output = preg_replace('!\s+!', ' ', $json_data);
       $position = strpos($output, $data);
       while ($position > 0) {
           $string = ",";
           $output = substr_replace($output, $string, $position+1, 0);
           $position = strpos($output, $data,$position+1);
       }
       return $output;
    }

    public function modifyJson($json_data) {
        $data = '}{';
        $position = strpos($json_data, $data);
        if($position > 0) {
            $newData = substr($json_data, 0, strpos($json_data, $data));
            $newData .= "}";
            $result = json_decode($newData, true);
        } else {
            $result = json_decode($mdata,true);
        }
        return $result;
    }

    public function allCricket() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $this->load->view('layout/backend_header', $hdata);
        $today = date('Y-m-d');
        $data['crickets'] = $this->Common_model->get_data_by_query("SELECT * FROM cron_data where event_id NOT IN (SELECT event_id FROM running_matches) AND DATE(start_date)=CURDATE()");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/all_cricket',$data);
        $this->load->view('layout/backend_footer');
    }

    public function enableBetting() {
        $eid = $this->input->get('event_id');
        $match = $this->Common_model->get_single_query("select * from cron_data where event_id = $eid");
        $data = array(
            'market_id'         => $match->market_id,
            'event_id'          => $match->event_id,
            'event_name'        => $match->event_name,
            'event_date'        => $match->event_date,
            'start_date'        => $match->start_date,
            'event_typeid'      => $match->event_typeid,
            'competition_id'    => $match->competition_id,
            'competition_name'  => $match->competition_name,
            'mtype'             => $match->mtype,
            'teams'             => $match->teams,
            'admin_enable'      => 'yes',
            'match_result'      => 'running',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('running_matches',$data);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Match added for betting</div>");
        redirect('Admin/allCricket');
    }

    public function runningCricket() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $this->load->view('layout/backend_header', $hdata);
        $today = date('Y-m-d');
        $data['crickets'] = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' OR match_result = 'paused'");

        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/running_matches',$data);
        $this->load->view('layout/backend_footer');
    }

    public function playPauseMatch() {
        $id = $this->input->get('id');
        $match = $this->Common_model->get_single_query("select * from running_matches where id = $id");
        $status = $match->match_result == 'running' ? 'paused' : 'running';
        $data = array(
            'match_result' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record('running_matches',$id,$data);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Match status has been changed</div>");
        redirect('Admin/runningCricket');
    }

    public function matchOdds() {
        $hdata['title'] = 'Admin Panel | SetBat';
        $this->load->view('layout/backend_header', $hdata);
        $eid = $this->input->get('match_id');
        $mid = $this->input->get('market_id');
        $data['match'] = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $data['odds'] = $this->matchOdd($mid);
        $data['dfancy'] = $this->Common_model->get_data_by_query("select * from fancy_data where market_id = '$mid' and status='playing'");
        $data['fancy'] = $this->fancyData($mid);
        $data['ubets']= $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and bet_type = 'unmatched'");
        $data['mbets']= $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and bet_type = 'matched'");
        $data['fbets']= $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and bet_type = 'fancy'");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/match_odds',$data);
        $this->load->view('layout/backend_footer');
    }

    public function fancyReload() {
        $mid = $this->input->get('market_id');
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $dfancy = $this->Common_model->get_data_by_query("select * from fancy_data where market_id = '$mid' and status='playing'");
        $fancy = $this->fancyData($mid);
        $scores = $fancy['score'];
        $scoreData = '<tr>
                        <th class="text-white">Team</th>
                        <th class="text-white">RR</th>
                        <th class="text-white">Over</th>
                      </tr>
                      <tr class="text-white">
                        <td>'.$scores['Team1']['score'].'</td>
                        <td>'.$scores['Team1']['RR'].'</td>
                        <td>'.$scores['Team1']['over'].'</td>
                      </tr>
                      <tr class="text-white">
                        <td>'.$scores['Team2']['score'].'</td>
                        <td>'.$scores['Team2']['RR'].'</td>
                        <td>'.$scores['Team2']['over'].'</td>
                      </tr>
                      <tr class="text-white">
                        <td colspan="3"><b>Commentary: </b>'.$scores['comm'].'</td>
                      </tr>';
        $did = array();
        foreach ($dfancy as $dkey => $d) {
          $did[] = $d['fancy_id'];
        }
        $fancies = $fancy['session'];
        $fancyData = '<table class="table table-bordered" width="100%">
                        <tr>
                          <th style="border: none !important;" width="63%"></th>
                          <th style="background: red; color: white; border: none !important; min-width: 50px; max-width: 50px;"><center>NO(L)</center></th>
                          <th style="background: #2c5ca9; color: white; border: none !important; min-width: 50px;  max-width: 50px;"><center>YES(B)</center></th>
                          <th width="17%"></th>
                        </tr>';
        foreach ($fancies as $fkey => $f) {
            if(in_array($f['SelectionId'], $did)) {
                
                $fff = $f['RunnerName'];
                $mmm = $match->market_id;
                $fanca = "getBookedFancy('$fff','$mmm')";
                $fancyData .= '<tr>
                                <td>'.$f['RunnerName'].'<span class="pull-right"><button class="btn btn-warning btn-sm" onclick="'.$fanca.'" data-toggle="modal" data-target="#bookFancyModal">book</button></span></td>
                                <td style="background-color: #ffbfcd; cursor: pointer; text-align: center;"><b>'.$f['LayPrice1'].'</b><br>'.$f['LaySize1'].'</td>
                                <td style="background-color: #b5e0ff; cursor: pointer; text-align: center;"><b>'.$f['BackPrice1'].'</b><br>'.$f['BackSize1'].'</td>
                                <td></td>
                              </tr>
                            ';
            }
        }
        $fancyData .= '</table>';
        $data = array('score' => $scoreData, 'fancy' => $fancyData);
        echo json_encode($data);
    }

    public function matchReload() {
        $mid = $this->input->get('market_id');
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $teams = json_decode($match->teams,true);
        $odds = $this->matchOdd($mid);
        $oddData = '<table class="table table-bordered table-condensed" width="100%" >
                        <tr>
                          <th style="border: none !important;"><b style="color: red;">Min stake:100 Max stake:200000</b></th>
                          <th style="border: none !important; min-width: 50px; max-width: 50px;"></th>
                          <th style="border: none !important; min-width: 50px; max-width: 50px;"></th>
                          <th style="background: #2c5ca9; color: white; border: none !important; min-width: 50px;  max-width: 50px;"><center>back</center></th>
                          <th style="background: red; color: white; border: none !important; min-width: 50px; max-width: 50px;"><center>lay</center></th>
                          <th style="border: none !important; min-width: 50px; max-width: 50px;"></th>
                          <th style="border: none !important; min-width: 50px; max-width: 50px;"></th>
                        </tr>';
        $t1id = $teams[0]['id'];
        $t2id = $teams[1]['id'];
        $runners = $odds['runners'];
        foreach($runners as $rk => $r){
            $back = $r['ex']['availableToBack'];
            $bprice = $back[0]['price'];
            $lay = $r['ex']['availableToLay'];
            $lprice = $lay[0]['price'];
            //print_r($lay[0]['price']);die;
            $rid = $r['selectionId'];
            $mid = $match->event_id;
            foreach ($teams as $tkey => $t) {
              if($t['id'] == $rid) {
                $rname = $t['name'];
              }
            }
            $oddData .= '<tr>
                <td><b>'.$rname.'</b><span class="pull-right" id="team'.$rk.'"></span></td>
                <td><center><b>'.$back[2]['price'].'</b><br>'.$back[2]['size'].'</center></td>
                <td><center><b>'.$back[1]['price'].'</b><br/>'.$back[1]['size'].'</center></td>
                <td style="background: #b5e0ff; cursor: pointer;"><center><b>'.$back[0]['price'].'</b><br/>'.$back[0]['size'].'</center></td>
                <td style="background: #ffbfcd; cursor: pointer;" ><center><b>'.$lay[0]['price'].'</b><br/>'.$lay[0]['size'].'</center></td>
                <td><center><b>'.$lay[1]['price'].'</b><br/>'.$lay[1]['size'].'</center></td>
                <td><center><b>'.$lay[2]['price'].'</b><br/>'.$lay[2]['size'].'</center></td>
            </tr>';
        }                              
        $oddData .= '</table>';
        echo $oddData;
    }

    public function betReload() {
        $mid = $this->input->get('market_id');
        $ubets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and bet_type = 'unmatched'");
        $mbets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and bet_type = 'matched'");
        $fbets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and bet_type = 'fancy'");
        echo '<ul class="nav customtab nav-tabs" role="tablist">
                <li role="presentation" class=""><a href="#unmatchedTab" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Unmatched</span></a></li>
                <li role="presentation" class="active"><a href="#matchedTab" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Matched</span></a></li>
                <li role="presentation" class=""><a href="#fancyTab" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-email"></i></span> <span class="hidden-xs">Fancy</span></a></li>
              </ul>
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade " id="unmatchedTab">
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <tr class="headings">
                          <th class="">Runner </th>
                          <th class="">Type </th>
                          <th class="">Odds </th>
                          <th class="">Stack </th>
                          <th class="">Profit </th>
                          <th class="">Loss </th>
                          <th class="">IP </th>
                          <th class="">ID </th>
                        </tr>';
                        foreach($ubets as $ub):
                            if($ub['back_lay'] == 'back') $class = 'back'; else $class = 'lay';
                         echo '<tr class="'.$class.'">
                            <td>'.$ub['team'].'</td>
                            <td>'.$ub['back_lay'].'</td>
                            <td>'.$ub['odd'].'</td>
                            <td>'.$ub['stake'].'</td>
                            <td>'.$ub['profit'].'</td>
                            <td>'.$ub['loss'].'</td>
                            <td>'.$ub['ip'].'</td>
                            <td>'.$ub['id'].'</td>
                          </tr>';
                        endforeach;
                      echo '</table>
                    </div>
                  <div class="clearfix"></div>
                </div>
                <div role="tabpanel" class="tab-pane fade active in" id="matchedTab">
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <tr class="headings">
                          <th class="">Runner </th>
                          <th class="">Type </th>
                          <th class="">Odds </th>
                          <th class="">Stack </th>
                          <th class="">Profit </th>
                          <th class="">Loss </th>
                          <th class="">IP </th>
                          <th class="">ID </th>
                        </tr>';
                        foreach($mbets as $mb):
                          if($mb['back_lay'] == 'back') $class = 'back'; else $class = 'lay';
                          echo '<tr class="'.$class.'">
                            <td>'.$mb['team'].'</td>
                            <td>'.$mb['back_lay'].'</td>
                            <td>'.$mb['odd'].'</td>
                            <td>'.$mb['stake'].'</td>
                            <td>'.$mb['profit'].'</td>
                            <td>'.$mb['loss'].'</td>
                            <td>'.$mb['ip'].'</td>
                            <td>'.$mb['id'].'</td>
                          </tr>';
                        endforeach;
                      echo '</table>
                    </div>
                  <div class="clearfix"></div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="fancyTab">
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <tr class="headings">
                          <th class="">Runner </th>
                          <th class="">Type </th>
                          <th class="">Odds </th>
                          <th class="">Stack </th>
                          <th class="">Profit </th>
                          <th class="">Loss </th>
                          <th class="">IP </th>
                          <th class="">ID </th>
                        </tr>';
                        foreach($fbets as $fb):
                          if($fb['back_lay'] == 'back') $class = 'back'; else $class = 'lay';
                          echo '<tr class="'.$class.'">
                            <td>'.$fb['team'].'</td>
                            <td>'.$fb['back_lay'].'</td>
                            <td>'.$fb['odd'].'</td>
                            <td>'.$fb['stake'].'</td>
                            <td>'.$fb['profit'].'</td>
                            <td>'.$fb['loss'].'</td>
                            <td>'.$fb['ip'].'</td>
                            <td>'.$fb['id'].'</td>
                          </tr>';
                        endforeach;
                      echo '</table>
                    </div>
                  </div>   
                  <div class="clearfix"></div>
              </div>';           
    }

    public function profitNLoss() {
        $mid = $this->input->get('market_id');
        $team1 = $this->input->get('team1');
        $team2 = $this->input->get('team2');
        $smasters = $this->Common_model->get_data_by_query("select * from users where parent_id = $this->id");
        $tt1pl = 0; $tt2pl = 0;
        foreach ($smasters as $sk => $s) {
            $ssd = $s['id'];
            $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $ssd)) AND market_id = '$mid' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
            $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $ssd)) AND market_id = '$mid' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
            $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $ssd)) AND market_id = '$mid' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
            $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $ssd)) AND market_id = '$mid' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");

            $team1win = $team1backprofitloss->p + $team2layprofitloss->p - $team2backprofitloss->l - $team1layprofitloss->l;
            $team2win = $team2backprofitloss->p + $team1layprofitloss->p - $team1backprofitloss->l - $team2layprofitloss->l;
            $adminPartnership = $this->Common_model->findfield('users','id',$ssd,'commission');
            $team1_pl = ($team1win*$adminPartnership)/100;
            $team2_pl = ($team2win*$adminPartnership)/100;
            $tt1pl += $team1_pl;
            $tt2pl += $team2_pl;
        }
        $pl_t1 = abs($tt1pl);
        $pl_t2 = abs($tt2pl);
        $team1status = $tt1pl > 0 ? 'text-danger' : 'text-success';
        $team2status = $tt2pl > 0 ? 'text-danger' : 'text-success';
        $data = array('team1pl' => $pl_t1 , 'team2pl' => $pl_t2, 'team1status' => $team1status, 'team2status' => $team2status);
        echo json_encode($data);
    }

    public function teamProfitLossSuperMaster() {
        $mid = $this->input->get('market_id');
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $teams = json_decode($match->teams,true);
        $team1 = $teams[0]['id'];
        $team2 = $teams[1]['id'];
        $team1Name = $teams[0]['name'];
        $team2Name = $teams[1]['name'];
        $p_l = array();
        $smasters = $this->Common_model->get_data_by_query("select * from users where parent_id = $this->id");
        foreach ($smasters as $sk => $s) {
           $ssd = $s['id'];
           $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $ssd)) AND market_id = '$mid' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");

           $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $ssd)) AND market_id = '$mid' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
           $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $ssd)) AND market_id = '$mid' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
           $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $ssd)) AND market_id = '$mid' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");

           $team1win = $team1backprofitloss->p + $team2layprofitloss->p - $team2backprofitloss->l - $team1layprofitloss->l;
           $team2win = $team2backprofitloss->p + $team1layprofitloss->p - $team1backprofitloss->l - $team2layprofitloss->l;
           $adminPartnership = $this->Common_model->findfield('users','id',$ssd,'commission');
           $team1_pl = ($team1win*$adminPartnership)/100;
           $team2_pl = ($team2win*$adminPartnership)/100;
           $team1_pl = abs(round($team1_pl,2));
           $team2_pl = abs(round($team2_pl,2));
           if($team1win > 0) {
            $team1_cl = 'text-danger';
           } else {
            $team1_cl = 'text-success';
           }
           if($team2win > 0) {
            $team2_cl = 'text-danger';
           } else {
            $team2_cl = 'text-success';
           }
           $p_l[$sk]['username'] = $s['username'];
           $p_l[$sk]['uid']      = $s['id'];
           $p_l[$sk]['team1_pl'] = $team1_pl;
           $p_l[$sk]['team1_cl'] = $team1_cl;
           $p_l[$sk]['team2_pl'] = $team2_pl;
           $p_l[$sk]['team2_cl'] = $team2_cl;
        }
        echo '<table class="table table-responsive table-striped table-border">
                <thead>
                  <tr class="headings">
                    <td>User</td>
                    <td>'.$team1Name.'</td>
                    <td>'.$team2Name.'</td>
                    <td>Draw</td>
                  </tr>
                </thead>
                <tbody>';
        foreach($p_l as $plk => $p):
            echo '<tr>
                    <td>'.$p['username'].'</td>
                    <td><span class="'.$p['team1_cl'].'">'.$p['team1_pl'].'</span></td>
                    <td><span class="'.$p['team2_cl'].'">'.$p['team2_pl'].'</span></td>
                    <td>0</td>
                  </tr>';
        endforeach;
        echo   '</tbody>
              </table>';
    }

    public function getBookedFancy() {
        $runner = $this->input->get('runner');
        $mid = $this->input->get('market_id');
        $bets = $this->Common_model->get_data_by_query("SELECT a.* FROM bet a LEFT JOIN users_with_groups b ON a.user_id = b.id WHERE a.market_id = '$mid' AND a.team = '$runner' AND a.bet_type = 'fancy'");
        $aa =  '<table class="table table-bordered table-striped" width="100%">
                <thead>
                    <tr class="headings">
                        <th>S.No</th>
                        <th>Odd</th>
                        <th>Lay(NO)</th>
                        <th>Back(YES)</th>
                    </tr>
                </thead>
                <tbody>';
        $i = 1;
        $odds = array();
        foreach ($bets as $bk => $b) {
            if(in_array($b['odd'], $odds)) {

            } else {
                $odds[] = $b['odd'];
            }
        }
        foreach ($odds as $ok => $o) {
            $yy = $this->Common_model->get_single_query("SELECT SUM(a.stake) AS p FROM bet a LEFT JOIN users_with_groups b ON a.user_id = b.id WHERE a.market_id = '$mid' AND a.team = '$runner' AND a.bet_type = 'fancy' AND a.odd = $o AND back_lay = 'back'");
            $nn = $this->Common_model->get_single_query("SELECT SUM(a.stake) AS p FROM bet a LEFT JOIN users_with_groups b ON a.user_id = b.id WHERE a.market_id = '$mid' AND a.team = '$runner' AND a.bet_type = 'fancy' AND a.odd = $o AND back_lay = 'lay'");
            $pp = $yy->p;
            $ll = $nn->p;

            $aa .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$o.'</td>
                    <td>'.$pp.'</td>
                    <td>'.$ll.'</td>
                </tr>';
        }
        $aa .= '</tbody></table>';
        echo $aa;
    }

    public function viewMatchFancy() {
        $eid = $this->input->get('match_id');
        $mid = $this->input->get('market_id');
        $hdata['title'] = 'Admin Panel | SetBat';
        $this->load->view('layout/backend_header', $hdata);
        $data['fancy'] = $this->fancyData($mid);
        $data['dfancy'] = $this->Common_model->get_data_by_query("select * from fancy_data where market_id = '$mid'");
        $data['match'] = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/fancy_data',$data);
        $this->load->view('layout/backend_footer');
    }

    public function fancyStatus()
    {
        $fdid = $this->input->get('fdid');
        $fid = $this->input->get('fancy_id');
        $mid = $this->input->get('market_id');
        $fancies = $this->fancyData($mid);;
        $fancy = array();
        foreach ($fancies['session'] as $key => $f) {
           if($f['SelectionId'] == $fid) {
            $fancy = $fancies['session'][$key];
           }
        }
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $fdata = $this->Common_model->get_single_query("select * from fancy_data where id = $fdid");
        $status = $fdata->status;
        if($status) {
            if($status == 'playing') {
                $nstatus = 'paused';
            } else {
                $nstatus = 'playing';
            }
            $data = array(
                'status'        => $nstatus,
                'updated_at'    => date('Y-m-d H:i:s')
            );
            $this->Crud_model->edit_record('fancy_data',$fdata->id,$data);
        } else {
            $nstatus = 'playing';
            $data = array(
                'fancy_id'          => $fid,
                'fancy_name'        => $fancy['RunnerName'],
                'market_id'         => $mid,
                'event_id'          => $match->event_id,
                'event_name'        => $match->event_name,
                'event_date'        => $match->event_date,
                'event_typeid'      => $match->event_typeid,
                'competition_id'    => $match->competition_id,
                'competition_name'  => $match->competition_name,
                'start_date'        => $match->start_date,
                'mtype'             => $match->mtype,
                'odds_type'         => 'fancy',
                'status'            => $nstatus,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('fancy_data',$data);
        }
        $msg = array('status' => $nstatus);
        echo json_encode($msg);
    }

    public function singleFancy() {
        $mid = $this->input->get('market_id');
        $fname = $this->input->get('fancy_name');
        $hdata['title'] = 'Admin Panel | SetBat';
        $this->load->view('layout/backend_header', $hdata);
        $data['fancy'] = $this->Common_model->get_single_query("select * from fancy_data where market_id = '$mid' and fancy_name = '$fname'");
        $data['match'] = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('admin/single_fancy',$data);
        $this->load->view('layout/backend_footer');
    }

    public function unsettleFancy() {
        $mid = $this->input->get('market_id');
        $fid = $this->input->get('fancy_id');
        $fline = 0;
        $fdid = $this->input->get('fdid');
        $fdata = array(
            'status'        => 'settled',
            'result'        => 'cancelled',
            'line'          => $fline,
            'updated_at'    => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record('fancy_data',$fdid,$fdata);
        $this->fancyUnSettlement($mid,$fid,$fdid);
    }

    public function fancyUnSettlement($mid,$fid,$fdid) {
        $fancy = $this->Common_model->get_single_query("select * from fancy_data where id = $fdid");
        $bets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and team = '$fancy->fancy_name' and bet_type = 'fancy'");
        foreach ($bets as $bkey => $b) {
            $data = array(
                'profit'    => 0,
                'loss'      => 0,
                'status'    => 'settled',
                'updated_at'=> date('Y-m-d H:i:s')
            );
            $this->Crud_model->edit_record('bets',$b['id'],$data);
        }
        $this->finalBalance();
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Fancy result has been unsettled</div>");
        redirect('Admin/viewMatchFancy?match_id='.$fancy->event_id.'&market_id='.$mid);
    }

    public function declareFancy() {
        $mid = $this->input->get('market_id');
        $fid = $this->input->get('fancy_id');
        $fline = $this->input->get('fancy_score');
        $fdid = $this->input->get('fdid');
        $fdata = array(
            'status'        => 'settled',
            'result'        => 'declared',
            'line'          => $fline,
            'updated_at'    => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record('fancy_data',$fdid,$fdata);
        $this->fancySettlement($mid,$fid,$fdid);
    }

    public function matchResult() {
        $id = $this->input->get('id');
        $match = $this->Common_model->get_single_query("select * from running_matches where id = $id");
        $fancies = $this->Common_model->get_data_by_query("select * from fancy_data where market_id = '$match->market_id'");
        $teams = json_decode($match->teams);
        $data = '<div class="modal-body" id="betFormData">
                      <div class="row">
                        <div class="form-group">
                          <div class="col-md-12">
                            <label for="winner">Winning Team</label>
                            <select class="form-control" name="winner" id="winner">';
                             foreach($teams as $t) { 
                              $data .= '<option value="'.$t->id.'_'.$t->name.'">'.$t->name.'</option>';
                             }
                             $data .= '<option value="0_tie">Tie/Abondoned</option>'; 
                            $data .= '</select>
                            <input type="hidden" name="match_id" id="match_id" value="'.$match->event_id.'">
                            <input type="hidden" name="market_id" id="market_id" value="'.$match->market_id.'">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="resultDeclare()" class="btn btn-info">submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>';
        echo $data;
    }

    public function resultDeclare() {
        $eid = $this->input->post('match_id');
        $mid = $this->input->post('market_id');
        $winner = $this->input->post('winner');
        $parts = explode('_', $winner);
        $teamName = $parts[1];
        $teamId = $parts[0];
        if($teamName == 'tie') {
            $data = array(
                'match_result'  => 'settled',
                'winner'        =>  $teamName,
                'winner_id'     =>  $teamId,
                'updated_at'    => date('Y-m-d H:i:s')
            );
            $this->Crud_model->edit_record_by_anyid('running_matches',$mid,$data,'market_id');
            $this->tieSettlement($eid,$mid);
        } else {
            $data = array(
                'match_result'  => 'settled',
                'winner'        =>  $teamName,
                'winner_id'     =>  $teamId,
                'updated_at'    => date('Y-m-d H:i:s')
            );
            $this->Crud_model->edit_record_by_anyid('running_matches',$mid,$data,'market_id');
            $this->betSettlement($eid,$mid);
        }
        $this->finalBalance();
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Match result has been declared</div>");
        redirect('Admin/runningCricket');
    }

    public function finalBalance() {
        $users = $this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'user'");
        $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE bet_type = 'fancy' and status='pending'");
        $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE bet_type = 'matched' and status='pending'");
        $unmids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE bet_type = 'unmatched' AND status='pending'");
        foreach ($users as $uk => $u) {
            $uid = $u['id'];
            $fancyFinal = 0;
            $tt1w = 0;
            $tt2w = 0;
            $untotal = 0;
            if (empty($fids)) {
                
            } else {
                $fblid = array();
                foreach ($fids as $mk => $mv) {
                    $fmkid = $mv['market_id'];
                    //New Code start

                    $list = $this->Common_model->ReadRaw("select * from bet where market_id='$fmkid' and user_id = $uid and bet_type = 'fancy' and status = 'pending'");
                    $outlist = array();

                    foreach ($list as $record) {
                        $outlist[$record->team][] = $record;
                    }

                    $total = 0;
                    $min = 0;
                    $plus = 0;
                    $minus = 0;
                    foreach ($outlist as $team => $value) {
                        $used = array();
                        $layUsed = array();
                        $backOdds = array();
                        $backIds = array();
                        $layMinusIds = array();
                        for ($i = 0; $i < count($value); $i++) {
                            $record = $value[$i];
                            if ($record->back_lay == 'back' && !in_array($record->id, $backIds)) {
                                $backOdds[] = $record->odd;
                                $backIds[] = $record->id;
                                $plus += $record->loss;
                            }
                        }
                        if ($backOdds) {
                            $minBackOdd = min($backOdds);
                            $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay' AND odd < $minBackOdd AND market_id = '$fmkid' AND team = '$team' and user_id = $uid and bet_type = 'fancy' and status = 'pending'");
                        } else {
                            $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay'  AND market_id = '$fmkid' AND team = '$team' and user_id = $uid and bet_type = 'fancy' and status = 'pending'");
                        }
                        for ($j = 0; $j < count($minloss); $j++) {
                            $min += $minloss[$j]->loss;
                            $layUsed[] = $minloss[$j]->id;
                        }
                        for ($k = 0; $k < count($value); $k++) {
                            $record = $value[$k];
                            if ($record->back_lay == 'lay' && !in_array($record->id, $layUsed) && !in_array($record->id, $layMinusIds)) {
                                $minus += $record->loss;
                                $layMinusIds[] = $record->id;
                            }
                        }
                    }

                    $total = abs($plus - $minus) + $min;
                    //New Code end
                    $fancyFinal += $total;
                }
            }
            //matched part
            if(empty($oids)) {

            } else {
                foreach ($oids as $ok => $of) {
                    $okids[] = $of['market_id'];
                }
                $onkids = array_unique($okids);
                foreach ($onkids as $onk => $ov) {

                    $ateam = $this->Common_model->get_single_query("select * from cron_data where market_id = '$ov'");
                    $teams = json_decode($ateam->teams);
                    $team1 = $teams[0]->id;
                    $team2 = $teams[1]->id;
                    $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
                    $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
                    $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
                    $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
                    $team1win = $team1backprofitloss->p + $team2layprofitloss->p - $team2backprofitloss->l - $team1layprofitloss->l;
                    $team2win = $team2backprofitloss->p + $team1layprofitloss->p - $team1backprofitloss->l - $team2layprofitloss->l;
                    if ($team1win < 0 && $team2win < 0) {
                        $t1w = $team1win < $team2win ? $team1win : $team2win;
                        $t2w = 0;
                    } else {
                        $t1w = $team1win >= 0 ? 0 : $team1win;
                        $t2w = $team2win >= 0 ? 0 : $team2win;
                    }
                    $tt1w += abs($t1w);
                    $tt2w += abs($t2w);
                }
            }
            //unmatched part
            if(empty($unmids)) {

            } else {
                foreach ($unmids as $unk => $unm) {
                    $unmkids[] = $unm['market_id'];
                }
                $unmmids = array_unique($unmkids);
                foreach ($unmmids as $unmk => $uv) {
                    $unmatchedBets = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$uv' AND bet_type = 'unmatched'");
                    $untotal += $unmatchedBets->l;
                }
            }
            
            $ubal = $this->Common_model->get_single_query("SELECT SUM(credits) as c, SUM(debits) as d FROM credits_debits WHERE user_id = $uid");
            $ubalance = $ubal->c - $ubal->d;
            $bchips = $ubalance - ($tt1w + $tt2w + $fancyFinal + $untotal);
            $cchips = $ubalance - $untotal;
            $bcfdata = array(
                'balanced_chips' => $bchips,
                'current_chips' => $cchips,
                'updated_at' => date('Y-m-d H:i:sa')
            );
            $this->Crud_model->edit_record_by_anyid('user_chips', $uid, $bcfdata, 'user_id');
        }
    }

    public function userBalance() {
        $users = $this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'user'");
        foreach ($users as $uk => $u) {
           $uid = $u['id'];
           $cd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $uid");

           $bal = $cd->c - $cd->d;
           $uchipdata = array(
               'balanced_chips' => $bal,
               'current_chips'  => $bal,
               'updated_at'     => date('Y-m-d H:i:s')
           );
           $this->Crud_model->edit_record_by_anyid('user_chips',$uid,$uchipdata,'user_id');
           $running = $this->Common_model->get_data_by_query("select * from running_matches where match_result = 'running'");
            foreach ($running as $rk => $r) {
                $mkid = $r['market_id'];
                $mid = $r['event_id'];
                $bets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mkid' and user_id = $uid");
                if($bets) {
                    $ateam = $this->Common_model->get_single_query("select * from cron_data where event_id = $mid");
                    $teams = json_decode($ateam->teams);
                    $team1 = $teams[0]->id;
                    $team2 = $teams[1]->id;
                    $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$mkid' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
                    $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$mkid' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
                    $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$mkid' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
                    $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$mkid' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
                    $team1win = $team1backprofitloss->p + $team2layprofitloss->p - $team2backprofitloss->l - $team1layprofitloss->l;
                    $team2win = $team2backprofitloss->p + $team1layprofitloss->p - $team1backprofitloss->l - $team2layprofitloss->l;
                    $t1w = $team1win >= 0 ? 0 : $team1win;
                    $t1w = abs($t1w);
                    $t2w = $team2win >= 0 ? 0 : $team2win;
                    $t2w = abs($t2w);
                    $chips = $this->Common_model->get_single_query("select * from user_chips where user_id = $uid");
                    $bchips = $chips->balanced_chips - $t1w - $t2w;
                    $bcdata = array(
                        'balanced_chips'    => $bchips,
                        'current_chips'     => $chips->current_chips,
                        'updated_at'        => date('Y-m-d H:i:sa')
                    );
                    $this->Crud_model->edit_record_by_anyid('user_chips',$uid,$bcdata,'user_id');
                }
            }
        }
    }

    public function tieSettlement($eid,$mid) {
        $bets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and status = 'pending'");
        if(empty($bets)) {

        } else {
            foreach ($bets as $bkey => $b) {
               $bid = $b['id'];
               $data = array(
                'profit'    => 0,
                'loss'      => 0,
                'status'    => 'settled',
                'bet_result'    => 'tie',
                'updated_at'=> date('Y-m-d H:i:s')
               );
               $this->Crud_model->edit_record('bet',$bid,$data);
            }
        }
    }

    public function fancySettlement($mid,$fid,$fdid) {
        $fancy = $this->Common_model->get_single_query("select * from fancy_data where id = $fdid");
        $bets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and team = '$fancy->fancy_name' and bet_type = 'fancy'");
        foreach ($bets as $bkey => $b) {
            $this->betSettlementFancy($b['id'],$mid);
        }
        //$this->Crud_model->delete_record('fancy_data',$fdid);
    }

    public function betSettlement($eid,$mid) {
        $bets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and bet_type != 'fancy'");
        foreach ($bets as $bkey => $b) {
           if($b['bet_type'] == 'matched') {
            $this->betSettlementMatched($b['id'],$mid);
           } else {
            $this->betSettlementUnMatched($b['id'],$mid);
           }
        }
    }

    public function betSettlementMatched($bid,$mid) {
        $bet = $this->Common_model->get_single_query("select * from bet where id = $bid");
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $winner = $match->winner_id;
        if($bet->back_lay == 'back') {
            if($winner == $bet->team_id) {
                $result = 'win';
            } else {
                $result = 'loose';
            }
        } else {
            if($winner == $bet->team_id) {
                $result = 'loose';
            } else {
                $result = 'win';
            }
        }        
        $this->profitLossCalculation($bid,$mid,$result);
    }

    public function betSettlementUnMatched($bid,$mid) {
        $bet = $this->Common_model->get_single_query("select * from bet where id = $bid");
        //update betting record
        $bdata = array(
            'status'        => 'settled',
            'bet_result'    => 'unmatched',
            'profit'        => 0,
            'loss'          => 0,
            'updated_at'    => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record('bet',$bet->id,$bdata);
        $stake = $bet->stake;
        $uid = $bet->user_id;
        $userChips = $this->Common_model->get_single_query("select * from user_chips where user_id=$uid");
        $uchipdata = array(
            'balanced_chips' => $userChips->balanced_chips + $bet->stake,
            'current_chips'  => $userChips->current_chips + $bet->stake,
            'updated_at'     => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips',$uid,$uchipdata,'user_id');

    }

    public function betSettlementFancy($bid,$mid) {
        $bet = $this->Common_model->get_single_query("select * from bet where id = $bid");
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $fancy = $this->Common_model->get_single_query("select * from fancy_data where market_id = '$mid' and fancy_name = '$bet->team'");
        $winner = $fancy->line;
        if($bet->back_lay == 'back') {
            if($winner >= $bet->odd) {
                $result = 'win';
            } else {
                $result = 'loose';
            }
        } else {
            if($winner >= $bet->odd) {
                $result = 'loose';
            } else {
                $result = 'win';
            }
        }        
        $this->profitLossCalculation($bid,$mid,$result);
        //$this->Crud_model->delete_record('fancy_data',$fancy->id);
    }

    public function profitLossCalculation($bid,$mid,$result) {
        $bet = $this->Common_model->get_single_query("select * from bet where id = $bid");
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $uid = $bet->user_id;
        $uc = $this->Common_model->findfield('users','id',$uid,'commission');//user partnership to master
        $mid = $this->Common_model->findfield('users','id',$uid,'parent_id');//master id
        $mc = $this->Common_model->findfield('users','id',$mid,'commission');//master partnershipt to super master
        if(empty($mc)) $mc = 0;
        $smid = $this->Common_model->findfield('users','id',$mid,'parent_id');//supermaster id
        $smc = $this->Common_model->findfield('users','id',$smid,'commission');//supermaster partnership to admin
        if(empty($smc)) $smc = 0;
        $aid = $this->Common_model->findfield('users','id',$smid,'parent_id');//admin id
        if($result == 'win') {
            $p_l = 'profit';
            $userProfit = $bet->profit;
            $userLoss = 0;
            $userCredit = $userProfit;
            $userDebit = 0;
            $masterProfit = 0;
            $masterLoss = $userProfit;
            $masterDebit = $userProfit;
            $masterCredit = $userProfit * ($smc + $mc)/100;
            $supermasterLoss = $masterCredit;
            $supermasterDebit = $masterCredit;
            $supermasterCredit = $userProfit * ($smc)/100;
            $adminLoss = $supermasterCredit;
            $adminDebit = $supermasterCredit;
            $adminCredit = 0;
            //profitloss
            $mpl = $masterDebit - $masterCredit;
            $smpl = $supermasterDebit - $supermasterCredit;
            $apl = $adminDebit - $adminCredit;
        } else {
            $p_l = 'loss';
            $userProfit = 0;
            $userLoss = $bet->loss;
            $userCredit = 0;
            $userDebit = $userLoss;
            $masterProfit = $userDebit;
            $masterLoss = 0;
            $masterCredit = $userDebit;
            $masterDebit = $userLoss * ($smc + $mc)/100;
            $supermasterProfit = $masterDebit;
            $supermasterCredit = $masterDebit;
            $supermasterDebit = $userLoss * ($smc)/100;
            $adminProfit = $supermasterDebit;
            $adminCredit = $supermasterDebit;
            $adminDebit = 0;
            //profitloss
            $mpl = $masterCredit - $masterDebit;
            $smpl = $supermasterCredit - $supermasterDebit;
            $apl = $adminCredit - $adminDebit;
        }
        //profit loss entry to bd
        $pldata = array(
            'bet_id'            => $bet->id,
            'user_id'           => $bet->user_id,
            'market_id'         => $bet->market_id,
            'market'            => $bet->market,
            'match_id'          => $bet->match_id,
            'match_name'        => $bet->match_name,
            'selection'         => $bet->team,
            'team_id'           => $bet->team_id,
            'winner_team'       => $match->winner,
            'winner_teamid'     => $match->winner_id,
            'stake'             => $bet->stake,
            'p_l'               => $p_l,    
            'profit'            => $userProfit,
            'loss'              => $userLoss,
            'bet_type'          => $bet->bet_type,
            'admin'             => $apl,
            'supermaster'       => $smpl,
            'master'            => $mpl,
            'commission'        => 0,
            'created_at'        => $bet->created_at,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('profit_loss',$pldata);
        //update betting record
        $bdata = array(
            'status'        => 'settled',
            'bet_result'    => $plresult,
            'profit'        => $userProfit,
            'loss'          => $userLoss,
            'updated_at'    => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record('bet',$bet->id,$bdata);
        //echo '<hr/>'.$this->db->last_query();
        //user credit debit
        $ucd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $uid");
        $ubal = $ucd->c - $ucd->d;
        $udcdata = array(
            'txnid'             => md5(microtime()),         
            'user_id'           => $bet->user_id,
            'credits'           => $userProfit,
            'credited_from'     => $userProfit > 0 ? $mid : 0,
            'debits'            => $userLoss,
            'debited_to'        => $userLoss > 0 ? $mid : 0,
            'balance'           => $ubal + $userProfit - $userLoss,
            'assigned_by'       => $uid,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits',$udcdata);
        //echo '<hr/>'.$this->db->last_query();
        $userChips = $this->Common_model->get_single_query("select * from user_chips where user_id = $uid");
        if($bet->bet_type == 'fancy') {
            
            $ubalAfterFancy = $this->Common_model->get_single_query("SELECT SUM(credits) AS c, SUM(debits) AS d FROM credits_debits WHERE user_id = $uid");

            $afterFancyBalance = $ubalAfterFancy->c - $ubalAfterFancy->d;

            $fancyFinal = 0; $tt1w = 0; $tt2w = 0;
            $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $uid AND bet_type = 'fancy' and status='pending'");
            foreach($fids as $fk => $ff) {
                $mkids[] = $ff['market_id'];
            }
            $unkids = array_unique($mkids);
            foreach ($unkids as $mk => $mv) {
                $fbets = $this->Common_model->get_data_by_query("SELECT DISTINCT(team) FROM bet WHERE user_id = $uid AND market_id = '$mv' AND bet_type = 'fancy' AND status='pending'");
                foreach($fbets as $fb => $ft) {
                    $ftid = $ft['team'];
                    $fbl = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$mv' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$ftid'");
                    $fll = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$mv' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$ftid'");
                    $fancyFinal += abs($fbl->l - $fll->l);
                }
            }
            $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $uid AND bet_type = 'matched' and status='pending'");
            foreach($oids as $ok => $of) {
                $okids[] = $of['market_id'];
            }
            $onkids = array_unique($okids);
            foreach ($onkids as $onk => $ov) {
                $ateam = $this->Common_model->get_single_query("select * from cron_data where market_id = '$ov'");
                $teams = json_decode($ateam->teams);
                $team1 = $teams[0]->id;
                $team2 = $teams[1]->id;
                $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
                $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
                $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
                $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
                $team1win = $team1backprofitloss->p + $team2layprofitloss->p - $team2backprofitloss->l - $team1layprofitloss->l;
                $team2win = $team2backprofitloss->p + $team1layprofitloss->p - $team1backprofitloss->l - $team2layprofitloss->l;
                $t1w = $team1win >= 0 ? 0 : $team1win;
                $tt1w += abs($t1w);
                $t2w = $team2win >= 0 ? 0 : $team2win;
                $tt2w += abs($t2w);
            }
            $uChips = $this->Common_model->get_single_query("SELECT SUM(debits) AS d, SUM(credits) AS c from credits_debits where user_id = $uid");
            $bchips = $uChips->c - $uChips->d - $tt1w - $tt2w - $fancyFinal;
            $cchips = $uChips->c - $uChips->d;
            $unchipdata = array(
                'balanced_chips' => $bchips,
                'current_chips'  => $cchips,
                'updated_at'     => date('Y-m-d H:i:s')
            );
            $this->Crud_model->edit_record_by_anyid('user_chips',$uid,$unchipdata,'user_id');
        }
        //echo $this->db->last_query(); die;
        //master debit credit
        $mcd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $mid");
        $mbal = $mcd->c - $mcd->d;
        $mdcdata = array(
            'txnid'             => md5(microtime()),         
            'user_id'           => $mid,
            'credits'           => $masterCredit,
            'credited_from'     => $userProfit > 0 ? $smid : $uid,
            'debits'            => $masterDebit,
            'debited_to'        => $userProfit > 0 ? $uid : $smid,
            'balance'           => $mbal + $masterCredit - $masterDebit,
            'assigned_by'       => $mid,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits',$mdcdata);
        //supermaster debit credit
        $smcd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $smid");
        $smbal = $smcd->c - $smcd->d;
        $smdcdata = array(
            'txnid'             => md5(microtime()),         
            'user_id'           => $smid,
            'credits'           => $supermasterCredit,
            'credited_from'     => $userProfit > 0 ? $aid : $mid,
            'debits'            => $supermasterDebit,
            'debited_to'        => $userProfit > 0 ? $mid : $aid,
            'balance'           => $smbal + $supermasterCredit,
            'assigned_by'       => $sid,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits',$smdcdata);
        //admin debit credit
        $acd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $aid");
        $abal = $acd->c - $acd->d;
        $adcdata = array(
            'txnid'             => md5(microtime()),         
            'user_id'           => $aid,
            'credits'           => $adminCredit,
            'credited_from'     => $userProfit > 0 ? 8 : $smid,
            'debits'            => $adminDebit,
            'debited_to'        => $userProfit > 0 ? $smid : 8,
            'balance'           => $abal + $adminCredit,
            'assigned_by'       => 8,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits',$adcdata);
        
    }

    public function updateDCBal($uid) {
        $dc = $this->Common_model->get_data_by_query("SELECT * FROM credits_debits where user_id = $uid order by id ASC");
        $plus = 0; $minus = 0;
        foreach($dc as $d) {
            $plus += $d['credits'] - $d['debits'];
            $data = array('balance' => $plus);
            $this->Crud_model->edit_record('credits_debits',$d['id'],$data);
        }
        //return TRUE;
    }

    public function updateBal($uid) {
        $ucd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $uid");
        $unchipdata = array(
            'balanced_chips' => $ucd->c - $ucd->d,
            'current_chips'  => $ucd->c - $ucd->d,
            'updated_at'     => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips',$uid,$unchipdata,'user_id');
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
    
    function json_output($statusHeader, $response) {
        $ci = & get_instance();
        $ci->output->set_content_type('application/json');
        $ci->output->set_status_header($statusHeader);
        $ci->output->set_output(json_encode($response));
    } 
}