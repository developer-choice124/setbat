<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SuperAdmin extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation', 'settlement'));
        $this->load->helper(array('url', 'language'));
        $this->load->model('Setting_model');
        $this->load->model('MyModel');
        $this->load->library('pagination');
        $this->settings = $this->Setting_model->get_setting();
        if (!empty($this->settings->timezone)) {
            date_default_timezone_set($this->settings->timezone);
        } else {
            date_default_timezone_set("Asia/Kolkata");
        }
        if (!$this->ion_auth->logged_in()) {
            redirect('Auth/login');
        }
        if (!$this->ion_auth->is_superadmin()) {
            redirect('Auth');
        }
        $this->id = $this->session->userdata('user_id');
        $this->panel = $this->Common_model->get_single_query("SELECT * FROM panel_title ORDER BY id DESC");
    }

    public function index()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        foreach ($matches as $mkey => $m) {
            $odds = $this->match->matchOddByMarketId($m['market_id']);
            $matches[$mkey]['odds'] = $odds;
        }
        $data['matches'] = $matches;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/index', $data);
        $this->load->view('layout/backend_footer');
    }

    public function changePassword()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/change_password');
        $this->load->view('layout/backend_footer');
    }

    public function updatePassword()
    {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        $old = $this->input->post('old');
        $new = $this->input->post('new');
        $new_confirm = $this->input->post('new_confirm');
        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-error alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' . (validation_errors()) ? validation_errors() : $this->session->flashdata('message') . '</div>');
            redirect('SuperAdmin/changePassword');
        } else {
            $identity = $this->session->userdata('identity');

            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

            if ($change) {
                //if the password was successfully changed
                $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" . $this->ion_auth->messages() . "</div>");
                redirect('Auth/logout');
            } else {
                $this->session->set_flashdata('message', "<div class='alert alert-error alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" . $this->ion_auth->errors() . "</div>");
                redirect('SuperAdmin/changePassword', 'refresh');
            }
        }
    }

    public function admins() {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'admin' and deleted = 'no' order by id DESC"));
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'admin'");
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/admins', $data);
        $this->load->view('layout/backend_footer');
    }

    public function supermasters()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $aid = $this->input->get('admin_id');
        if (isset($aid)) {
            $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'supermaster' and parent_id = $aid and deleted = 'no' order by id DESC"));
        } else {
            $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'supermaster' and deleted = 'no' order by id DESC"));
        }
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'supermaster'");
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/supermasters', $data);
        $this->load->view('layout/backend_footer');
    }

    public function masters()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $smid = $this->input->get('supermaster_id');
        if (isset($smid)) {
            $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'master' and parent_id = $smid and deleted = 'no' order by id DESC"));
        } else {
            $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'master' and deleted = 'no' order by id DESC"));
        }
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'supermaster'");
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/masters', $data);
        $this->load->view('layout/backend_footer');
    }

    public function users()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $mid = $this->input->get('master_id');
        if (isset($mid)) {
            $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'user' and parent_id = $mid and deleted = 'no' order by id DESC"));
        } else {
            $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("SELECT * FROM `users_with_groups` WHERE group_name = 'user' AND deleted = 'no' ORDER BY id DESC"));
        }
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'supermaster'");
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/users', $data);
        $this->load->view('layout/backend_footer');
    }

    public function deletedUsers()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $data['users'] = $this->_outlist($this->Common_model->get_data_by_query("SELECT * FROM `users_with_groups` WHERE deleted = 'yes' ORDER BY id DESC"));
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'supermaster'");
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/deleted_users', $data);
        $this->load->view('layout/backend_footer');
    }

    public function restoreUser($id)
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $data = array('deleted' => 'no');
        $this->Crud_model->edit_record('users', $id, $data);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Restored successfully</div>");
        redirect('SuperAdmin/deletedUsers');
    }

    public function resetUserPassword()
    {
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        $id = $this->input->post('user_id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
        if ($this->form_validation->run() == true) {
            $id = $this->input->post('user_id');
            $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
            $new = $this->input->post('new');
            $q = $this->db->select('password, salt')->from('users')->where('id', $id)->get()->row();
            $hashed_new_password  = $this->ion_auth->hash_password($new, $q->salt);
            $data = array(
                'password' => $hashed_new_password,
                'remember_code' => NULL,
            );
            $this->db->where('id', $id)->update('users', $data);
            $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Password Changed successfully</div>");
            redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : 'supermasters')));
        }
        $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : 'supermasters')));
    }

    public function addChild()
    {
        $tables = $this->config->item('tables', 'ion_auth');
        $identity_column = $this->config->item('identity', 'ion_auth');
        $this->form_validation->set_rules('identity', 'Username', 'required|is_unique[' . $tables['users'] . '.username]');
        $this->form_validation->set_rules('parent_id', 'Parent Id', 'required');
        $this->form_validation->set_rules('groups', 'User Groups', 'required');
        if ($this->form_validation->run() == true) {
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
            $ug = $this->Common_model->findfield('users_with_groups', 'id', $pid, 'group_name');
            if ($id) {
                $udata = array(
                    'user_id'       => $id,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                );
                $this->Crud_model->insert_record('user_settings', $udata);
                $cug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
                if ($cug == 'user') {
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
                redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : 'supermasters')));
            } else {
                $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : 'supermasters')));
            }
        } else {
            $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            redirect('SuperAdmin/supermasters');
        }
    }

    public function addUser()
    {
        $tables = $this->config->item('tables', 'ion_auth');
        $identity_column = $this->config->item('identity', 'ion_auth');
        $this->form_validation->set_rules('identity', 'Username', 'required|is_unique[' . $tables['users'] . '.username]');
        if ($this->form_validation->run() == true) {
            $email    = $this->input->post('identity');
            $identity = ($identity_column === 'email') ? $email : $this->input->post('identity');
            $password = 'set123';
            $additional_data = array(
                'full_name'  => $this->input->post('full_name'),
                'parent_id'  => $this->id
            );
            $groups = array(
                'id' => 2
            );
            // check to see if we are creating the user
            // redirect them back to the admin page
            $id = $this->ion_auth->register($identity, $password, $email, $additional_data, $groups);
            $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
            if ($id) {
                $udata = array(
                    'user_id'       => $id,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                );
                $this->Crud_model->insert_record('user_settings', $udata);
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
            } else {
                $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                redirect('SuperAdmin/admins', 'refresh');
            }
        } else {
            $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            redirect('SuperAdmin/admins', 'refresh');
        }
    }

    public function editUser()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $data['user'] = $this->Common_model->get_single_query("select * from users_with_groups where id = $id");
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name='user'");
        $data['csrf'] = $this->_get_csrf_nonce();
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/edit_user', $data);
        $this->load->view('layout/backend_footer');
    }

    public function updateUser($id)
    {
        $tables = $this->config->item('tables', 'ion_auth');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
        // validate form input

        if (isset($_POST) && !empty($_POST)) {
            // do we have a valid request?
            if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
                show_error($this->lang->line('error_csrf'));
            }

            $data = array(
                'full_name'     => $this->input->post('full_name'),
                'commission'    => $this->input->post('commission')
            );

            // update the password if it was posted
            if ($this->input->post('password')) {
                $data['password'] = $this->input->post('password');
            }

            // check to see if we are updating the user
            if ($this->ion_auth->update($id, $data)) {
                // redirect them back to the admin page if admin, or to the base url if non admin
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
            } else {
                // redirect them back to the admin page if admin, or to the base url if non admin
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect('SuperAdmin/editUser?user_id=' . $id);
            }
        }
    }

    public function activateUser()
    {
        $id = $this->input->get('id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
        $status = $this->input->get('status');
        if ($status == 1) {
            $this->ion_auth->deactivate($id);
            $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User deactivated</div>");
            redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
        } else {
            $this->ion_auth->activate($id);
            $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Activated successfully</div>");
            redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
        }
    }

    public function userInfo()
    {
        $id = $this->input->get('user_id');
        $user = $this->Common_model->get_single_query("select * from user_settings where user_id = $id");
        echo json_encode($user);
    }

    public function deleteUser($id)
    {
        $data = array('deleted' => 'yes');
        $this->Crud_model->edit_record('users', $id, $data);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Deleted successfully</div>");
    }

    public function lockBetting()
    {
        $id = $this->input->get('user_id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
        $status = $this->input->get('status');
        $data = array('lock_betting' => $status);
        $this->Crud_model->edit_record('users', $id, $data);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Betting Status updated</div>");
        redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
    }

    public function updateSessionCommission()
    {
        $id = $this->input->get('user_id');
        $commission = $this->input->get('commission');
        $data = array('session_commission' => $commission);
        $this->Crud_model->edit_record('users', $id, $data);
        echo json_encode(['status' => 'success', 'message' => 'session commission updated']);
    }

    public function updateOddCommission()
    {
        $id = $this->input->get('user_id');
        $commission = $this->input->get('commission');
        $data = array('odd_commission' => $commission);
        $this->Crud_model->edit_record('users', $id, $data);
        echo json_encode(['status' => 'success', 'message' => 'odd commission updated']);
    }

    public function showMatch()
    {
        $id = $this->input->get('user_id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
        $status = $this->input->get('status');
        $data = array('show_match' => $status);
        $this->Crud_model->edit_record('users', $id, $data);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Match Status has been updated</div>");
        redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
    }

    public function updateUserInfo()
    {
        $id = $this->input->post('user_id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
        $data = array(
            'max_stake'             => $this->input->post('max_stake'),
            'in_play_stake'         => $this->input->post('in_play_stake'),
            'max_profit_market'     => $this->input->post('max_profit_market'),
            'max_profit_fancy'      => $this->input->post('max_profit_fancy'),
            'bet_delay'             => $this->input->post('bet_delay'),
            'fancy_bet_delay'       => $this->input->post('fancy_bet_delay'),
            'updated_at'            => date('Y-m-d H:i:s'),
        );
        $this->Crud_model->edit_record_by_anyid('user_settings', $id, $data, 'user_id');
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Info updated successfully</div>");
        redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
    }

    public function addMoney()
    {
        $uid = $this->input->post('user_id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $uid, 'group_name');
        $user = $this->Common_model->get_single_query("select * from users_with_groups where id = $uid");
        $chips = $this->input->post('chips');
        if ($chips <= 0) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Please add some Chips first</div>");
            redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
        }
        $pid = $user->parent_id;
        $pcreditdebit = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $pid");
        $parentChips = $pcreditdebit->c - $pcreditdebit->d;
        if ($ug != 'admin' && $parentChips <= 0) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Please add some Chips to parent first</div>");
            redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
        }
        if ($ug != 'admin' && $chips > $parentChips) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Chips can not be more than parent balanced chips</div>");
            redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
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
        $this->Crud_model->insert_record('credits_debits', $data);
        if ($old) {
            $total_chips = $old->total_chips + $chips;
            $balance = $old->balanced_chips + $chips;
            $chipData = array(
                'user_id'           => $uid,
                'total_chips'       => $total_chips,
                'balanced_chips'    => $old->balanced_chips + $chips,
                'current_chips'     => $old->current_chips + $chips,
                'updated_at'        => date('Y-m-d H:i:s')
            );
            if ($this->input->post('free_chips') == 'yes') {
                $chipData['free_chips'] = $old->free_chips + $chips;
            } else {
                $chipData['paid_chips'] = $old->paid_chips + $chips;
            }
            $this->Crud_model->edit_record('user_chips', $old->id, $chipData);
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
            if ($this->input->post('free_chips') == 'yes') {
                $chipData['free_chips'] = $chips;
            } else {
                $chipData['paid_chips'] = $chips;
            }
            $this->Crud_model->insert_record('user_chips', $chipData);
        }
        $mchips = $this->Common_model->get_single_query("select * from user_chips where user_id = $pid");
        $mcd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $pid");
        if ($ug == 'admin') {
            $mcdata = array(
                'user_id'           => $pid,
                'txnid'             => md5(microtime()),
                'debits'            => $chips,
                'debited_to'        => $uid,
                'balance'           => '',
                'description'       => 'Transferred to ' . $user->username,
                'transferred_to'    => $uid,
                'transaction_date'  => date('Y-m-d H:i:s'),
                'type'              => 'debit',
                'settled'           => 'yes',
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('credits_debits', $mcdata);
        } else {
            $mdata = array(
                'balanced_chips'    => $mchips->balanced_chips - $chips,
                'current_chips'     => $balance,
                'spent_chips'       => $mchips->spent_chips + $chips,
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->edit_record('user_chips', $mchips->id, $mdata);
            $mbal = $mcd->c - $mcd->d;
            $mcbalance = $mbal - $chips;
            $mcdata = array(
                'user_id'           => $pid,
                'txnid'             => md5(microtime()),
                'debits'            => $chips,
                'debited_to'        => $uid,
                'balance'           => $mcbalance,
                'description'       => 'Transferred to ' . $user->username,
                'transferred_to'    => $uid,
                'transaction_date'  => date('Y-m-d H:i:s'),
                'type'              => 'debit',
                'settled'           => 'yes',
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('credits_debits', $mcdata);
        }
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Credit Record Added</div>");
        redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
    }

    public function witdrawChips()
    {
        $uid = $this->input->post('user_id');
        $chips = $this->input->post('chips');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $uid, 'group_name');
        $maxChips = $this->Common_model->findfield('user_chips', 'user_id', $uid, 'balanced_chips');
        if ($chips > $maxChips) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>WIthdraw Chips can not be more than balanced chips</div>");
            redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
        }
        $pid = $this->Common_model->findfield('users', 'id', $uid, 'parent_id');
        $data = array(
            'user_id'           => $uid,
            'txnid'             => md5(microtime()),
            'debits'            => $chips,
            'debited_to'        => $pid,
            'balance'           => $maxChips - $chips,
            'description'       => 'Chips Withdrawn by ' . $this->Common_model->findfield('users', 'id', $pid, 'username'),
            'assigned_by'       => $this->id,
            'transaction_date'  => date('Y-m-d H:i:s'),
            'type'              => 'debit',
            'settled'           => 'yes',
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $data);
        $chipData = array(
            'withdraw_chips'    => $this->Common_model->findfield('user_chips', 'user_id', $uid, 'withdraw_chips') + $chips,
            'balanced_chips'    => $maxChips - $chips,
            'current_chips'     => $maxChips - $chips,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips', $uid, $chipData, 'user_id');
        //parent record
        $pchips = $this->Common_model->findfield('user_chips', 'user_id', $pid, 'balanced_chips');
        $pdata = array(
            'user_id'           => $pid,
            'txnid'             => md5(microtime()),
            'credits'           => $chips,
            'credited_from'     => $uid,
            'balance'           => $pchips + $chips,
            'description'       => 'Chips Withdrawn from ' . $this->Common_model->findfield('users', 'id', $uid, 'username'),
            'assigned_by'       => $this->id,
            'transaction_date'  => date('Y-m-d H:i:s'),
            'type'              => 'credit',
            'settled'           => 'yes',
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $pdata);
        $pchipData = array(
            'balanced_chips'    => $pchips + $chips,
            'current_chips'     => $pchips + $chips,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips', $pid, $pchipData, 'user_id');
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>chips has been withdrawn from user</div>");
        redirect('SuperAdmin/' . ($ug == 'user' ? 'users' : ($ug == 'master' ? 'masters' : ($ug == 'supermaster' ? 'supermasters' : 'admins'))));
    }

    public function userBetHistory()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
        if($ug == 'admin') {
            $data['bets'] = $this->Common_model->get_data_by_query("SELECT * FROM `bet` WHERE user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $id))) ORDER BY id DESC");
        }
        elseif ($ug == 'supermaster') {
            $data['bets'] = $this->Common_model->get_data_by_query("SELECT * FROM `bet` WHERE user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $id)) ORDER BY id DESC");
        } elseif ($ug == 'master') {
            $data['bets'] = $this->Common_model->get_data_by_query("SELECT * FROM `bet` WHERE user_id IN (SELECT id FROM users WHERE parent_id = $id) ORDER BY id DESC");
        } else {
            $data['bets'] = $this->Common_model->get_data_by_query("select * from bet where user_id = $id order by id DESC");
        }
        //echo $this->db->last_query();die;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/userbet_history', $data);
        $this->load->view('layout/backend_footer');
    }

    public function deleteUserBet()
    {
        $id = $this->input->get('id');
        $uid = $this->input->get('user_id');
        $this->Crud_model->delete_record('bet', $id);
        $this->Crud_model->delete_record_any_id('credits_debits', $id, 'bet_id');
        $this->Crud_model->delete_record_any_id('profit_loss', $id, 'bet_id');
        $this->finalBalance();
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User bet has been deleted successfully</div>");
        redirect('SuperAdmin/userBetHistory?user_id=' . $uid);
    }

    public function deleteBetById()
    {
        $id = $this->input->get('id');
        $this->Crud_model->delete_record('bet', $id);
        $this->Crud_model->delete_record_any_id('credits_debits', $id, 'bet_id');
        $this->Crud_model->delete_record_any_id('profit_loss', $id, 'bet_id');
        // $this->finalBalance();
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User bet has been deleted successfully</div>");
        redirect('SuperAdmin/betHistory');
    }

    public function userAccountStatement()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
        $statements = $this->Common_model->get_data_by_query("SELECT *  FROM `credits_debits` where user_id = $id order by id asc");
        $slist = array();
        foreach ($statements as $sk => $s) {
            if ($s['type'] == 'bet') {
                if (!in_array($s['match_id'], $slist)) {
                    $slist[] = $s['match_id'];
                    $mid = $s['match_id'];
                    $cd = $this->Common_model->get_single_query("SELECT SUM(credits) as c, SUM(debits) as d FROM credits_debits WHERE user_id = $id AND match_id = $mid");
                    $statements[$sk]['credits'] = $cd->c;
                    $statements[$sk]['debits'] = $cd->d;
                } else {
                    unset($statements[$sk]);
                }
            }
        }
        $balance = 0;
        foreach ($statements as $sl => $sv) {
            $balance += ($sv['credits'] - $sv['debits']);
            $statements[$sl]['balance'] = $balance;
        }
        // $outlist = array();
        // foreach ($statements as $skey => $s) {
        //     $match_id = $s['match_id'];
        //     if(!isset($match_id)) {
        //         $match_id = uniqid();
        //     }
        //     if (array_key_exists($match_id, $outlist)) {
        //         $outlist[$match_id][] = $s;
        //     } else {
        //         $outlist[$match_id] = array($s);
        //     }
        // }
        // $st = array();
        // foreach ($outlist as $key => $o) {
        //     $st[] = $o[0];
        // }
        $data['statements'] = array_reverse($statements);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/useraccount_statement', $data);
        $this->load->view('layout/backend_footer');
    }

    public function statementByMatchId()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $match_id = $this->input->get('match_id');
        $uid = $this->input->get('user_id');
        $data['statements'] = $this->Common_model->get_data_by_query("SELECT s.*, b.id as bid, b.bet_type, b.user_commission, b.master_commission from credits_debits s left join bet b on s.bet_id = b.id where s.user_id = $uid and s.match_id = $match_id order by b.bet_type");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/statementby_matchid', $data);
        $this->load->view('layout/backend_footer');
    }

    public function oddFancyByMatchId()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);

        $match_id = $this->input->get('match_id');
        $uid = $this->input->get('user_id');
        $type = $this->input->get('type');

        //user calculation
        $up = array();
        $down = array();
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a LEFT JOIN bet b ON a.bet_id = b.id WHERE a.user_id = $uid AND a.match_id = $match_id AND b.bet_type = '$type'");
        $ubal = $ucd->c - $ucd->d;

        if ($ubal == 0) {
        } elseif ($ubal > 0) {
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
        $cup = array();
        $cdown = array();
        foreach ($childs as $ckey => $c) {
            $cid = $c['id'];
            $child = $this->Common_model->get_single_query("select * from users_with_groups where id = $cid");
            $ccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a LEFT JOIN bet b ON a.bet_id = b.id WHERE a.user_id = $cid AND a.match_id = $match_id AND b.bet_type = '$type' AND a.credited_from = $uid");
            $cdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a LEFT JOIN bet b ON a.bet_id = b.id WHERE a.user_id = $cid AND a.match_id = $match_id AND b.bet_type = '$type' AND a.debited_to = $uid");
            $cbal = $ccredit->c - $cdebit->d;
            if ($cbal == 0) {
            } elseif ($cbal > 0) {
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
        $pid = $this->Common_model->findfield('users', 'id', $uid, 'parent_id');
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pcredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a LEFT JOIN bet b ON a.bet_id = b.id WHERE a.user_id = $pid AND a.match_id = $match_id AND b.bet_type = '$type' AND a.credited_from = $uid");
        $pdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a LEFT JOIN bet b ON a.bet_id = b.id WHERE a.user_id = $pid AND a.match_id = $match_id AND b.bet_type = '$type' AND a.debited_to = $uid");
        $pbal = $pcredit->c - $pdebit->d;
        $pup = array();
        $pdown = array();
        if ($pbal == 0) {
        } elseif ($pbal > 0) {
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
        $cpup = array();
        $cpdown = array();
        $cpup = array_merge($pup, $cup);
        $cpdown = array_merge($pdown, $cdown);
        $plus = array();
        $minus = array();
        $plus = array_merge($cpup, $up);
        $minus = array_merge($cpdown, $down);
        $data['type'] = $type;
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['bets'] = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE id IN(SELECT DISTINCT a.bet_id FROM credits_debits a LEFT JOIN bet b ON b.id = a.bet_id WHERE a.user_id = $uid AND a.type = 'bet' AND a.match_id = $match_id AND b.bet_type = '$type')");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/oddfancyby_matchid', $data);
        $this->load->view('layout/backend_footer');
    }

    public function userProfitLoss()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
        $data['profitLosses'] = $this->Common_model->get_data_by_query("select * from profit_loss where user_id = $id");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/userprofit_loss', $data);
        $this->load->view('layout/backend_footer');
    }

    public function bet()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('bet_id');
        $data['bet'] = $this->Common_model->get_single_query("select * from bet where id = $id");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/bet', $data);
        $this->load->view('layout/backend_footer');
    }

    public function accountInfo()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $data['up'] = $this->Common_model->get_single_query("SELECT SUM(credits) as up FROM `credits_debits` WHERE user_id = $this->id AND type='bet'");
        $data['down'] = $this->Common_model->get_single_query("SELECT SUM(debits) as down FROM `credits_debits` WHERE user_id = $this->id AND type='bet'");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/account_info', $data);
        $this->load->view('layout/backend_footer');
    }

    public function accountStatement()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $statements = $this->Common_model->get_data_by_query("SELECT *  FROM `credits_debits` where user_id = $this->id order by id desc");
        $outlist = array();
        foreach ($statements as $skey => $s) {
            $match_id = $s['match_id'];
            if (!isset($match_id)) {
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
        $this->load->view('superadmin/account_statement', $data);
        $this->load->view('layout/backend_footer');
    }

    public function chipHistory()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $data['history'] = $this->Common_model->get_data_by_query("SELECT a.*, b.username, c.id as bid, c.match_name, c.team, c.bet_type, c.market, d.winner FROM `credits_debits` a LEFT JOIN users b ON a.user_id = b.id LEFT JOIN bet c ON a.bet_id = c.id LEFT JOIN running_matches d ON c.market_id = d.market_id WHERE a.user_id = $this->id AND a.type='bet' ORDER BY a.id DESC");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/chip_history', $data);
        $this->load->view('layout/backend_footer');
    }

    public function chipSummary()
    {
        $uid = $this->input->get('user_id');
        $group = $this->Common_model->findfield('users_with_groups', 'id', $uid, 'group_name');
        if ($group == 'user') {
            redirect('SuperAdmin/userChipSummary?user_id=' . $uid);
        } elseif ($group == 'master') {
            redirect('SuperAdmin/masterChipSummary?user_id=' . $uid);
        } elseif ($group == 'supermaster') {
            redirect('SuperAdmin/superMasterChipSummary?user_id=' . $uid);
        } elseif ($group == 'admin') {
            redirect('SuperAdmin/adminChipSummary?user_id=' . $uid);
        } else {
            redirect('SuperAdmin/superAdminChipSummary?user_id=' . $uid);
        }
    }

    public function addSettlementTable()
    {
        $cd = $this->Common_model->get_data_by_query("SELECT * FROM `credits_debits` WHERE type = 'settlement' AND user_id IN (SELECT id FROM users_with_groups WHERE group_name = 'user')");
        foreach ($cd as $ck => $c) {
            $pid = $this->Common_model->findfield('users', 'id', $c['user_id'], 'parent_id');
            $p = $this->Common_model->get_single_query("SELECT * FROM users WHERE id = $pid");
            $data = array(
                'user_id'           => $c['user_id'],
                'settlement'        => $c['credits'] > 0 ? $c['credits'] : -$c['debits'],
                'message'           => 'chip settlement by ' . $p->username,
                'parent_id'         => $pid,
                'settlement_date'   => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('settlement', $data);
        }
    }

    public function userSettleBalance()
    {
        $users = $this->Common_model->get_data_by_query("SELECT id,parent_id FROM users_with_groups WHERE group_name = 'user'");
        foreach ($users as $u) {
            $uid = $u['id'];
            $pid = $u['parent_id'];
            $cd = $this->Common_model->get_data_by_query("SELECT * FROM credits_debits  WHERE user_id = $uid AND type = 'bet' ORDER BY id ASC");

            foreach ($cd as $ck => $c) {
                $bal = $c['credits'] - $c['debits'];
                $data = array(
                    'set_bal'           => $bal,
                    'parent_bal'        => $c['credited_from'] == $pid ? -$c['credits'] : $c['debits']
                );
                $this->Crud_model->edit_record('credits_debits', $c['id'], $data);
            }
        }
    }

    public function masterSettleBalance()
    {
        $users = $this->Common_model->get_data_by_query("SELECT id,parent_id FROM users_with_groups WHERE group_name = 'master'");
        foreach ($users as $u) {
            $uid = $u['id'];
            $pid = $u['parent_id'];
            $cd = $this->Common_model->get_data_by_query("SELECT * FROM credits_debits  WHERE user_id = $uid AND type = 'bet' ORDER BY id ASC");
            foreach ($cd as $k => $ck) {
                $bal = $ck['credits'] - $ck['debits'];
                $data = array(
                    'set_bal'           => $bal,
                    'parent_bal'        => $ck['credited_from'] == $pid ? $ck['credits'] : -$ck['debits']
                );
                $this->Crud_model->edit_record('credits_debits', $ck['id'], $data);
            }
        }
    }

    public function superMasterSettleBalance()
    {
        $users = $this->Common_model->get_data_by_query("SELECT id,parent_id FROM users_with_groups WHERE group_name = 'supermaster'");
        foreach ($users as $u) {
            $uid = $u['id'];
            $pid = $u['parent_id'];
            $cd = $this->Common_model->get_data_by_query("SELECT * FROM credits_debits  WHERE user_id = $uid AND type = 'bet' ORDER BY id ASC");
            foreach ($cd as $k => $ck) {
                $bal = $ck['credits'] - $ck['debits'];
                $data = array(
                    'set_bal'           => $bal,
                    'parent_bal'        => $ck['credited_from'] == $pid ? $ck['credits'] : -$ck['debits']
                );
                $this->Crud_model->edit_record('credits_debits', $ck['id'], $data);
            }
        }
    }

    public function adminSettleBalance()
    {
        $users = $this->Common_model->get_data_by_query("SELECT id,parent_id FROM users_with_groups WHERE group_name = 'admin'");
        foreach ($users as $u) {
            $uid = $u['id'];
            $pid = $u['parent_id'];
            $cd = $this->Common_model->get_data_by_query("SELECT * FROM credits_debits  WHERE user_id = $uid AND type = 'bet' ORDER BY id ASC");
            foreach ($cd as $k => $ck) {
                $bal = $ck['credits'] - $ck['debits'];
                $data = array(
                    'set_bal'           => $bal,
                    'parent_bal'        => $ck['credited_from'] == $pid ? $ck['credits'] : -$ck['debits']
                );
                $this->Crud_model->edit_record('credits_debits', $ck['id'], $data);
            }
        }
    }

    public function userChipSummary()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array();
        $down = array();
        $ub = $this->Common_model->get_single_query("SELECT SUM(set_bal) as b FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet' AND settled = 'no'");
        $usb = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement a WHERE a.user_id = $uid");
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $pid = $user->parent_id;
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pb = $this->Common_model->get_single_query("SELECT SUM(parent_bal) as b FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet' AND settled = 'no'");
        $ubal = $ub->b + $usb->b;
        //$pbal = $pb->b;
        if ($ubal == 0) {
        } elseif ($ubal > 0) {
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
        $pup = array();
        $pdown = array();
        if ($ubal == 0) {
        } elseif ($ubal < 0) {
            $pup[0]['username']    = 'Parent A/C';
            $pup[0]['name']        = $puser->full_name;
            $pup[0]['uid']         = $pid;
            $pup[0]['chips']       = abs($ubal);
        } else {
            $pdown[0]['username']  = 'Parent A/C';
            $pdown[0]['name']      = $puser->full_name;
            $pdown[0]['uid']       = $pid;
            $pdown[0]['chips']     = abs($ubal);
        }
        $plus = array();
        $minus = array();
        $plus = array_merge($pup, $up);
        $minus = array_merge($pdown, $down);
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/userchip_summary', $data);
        $this->load->view('layout/backend_footer');
    }


    public function masterChipSummary()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array();
        $down = array();
        $ub = $this->Common_model->get_single_query("SELECT SUM(set_bal) as b FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet' AND a.settled = 'no'");
        $usb = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement a WHERE a.user_id = $uid");
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $pid = $user->parent_id;
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pb = $this->Common_model->get_single_query("SELECT SUM(parent_bal) as b FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet' AND a.settled = 'no'");
        $ubal = $ub->b + $usb->b;
        $pbal = $pb->b + $usb->b;
        if ($ubal == 0) {
        } elseif ($ubal > 0) {
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
        $pup = array();
        $pdown = array();
        if ($pbal == 0) {
        } elseif ($pbal < 0) {
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
        $cup = array();
        $cdown = array();
        foreach ($childs as $ck => $c) {
            $cid = $c['id'];
            $ccredit = $this->Common_model->get_single_query("SELECT SUM(set_bal) AS b FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.settled = 'no'");
            $csb = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement a WHERE a.user_id = $cid");
            // print_r($ccredit);
            // echo '-----';
            // print_r($csb);
            $cbal = $ccredit->b + $csb->b;

            if ($cbal == 0) {
            } elseif ($cbal > 0) {
                $cp['name']     = $c['full_name'];
                $cp['username'] = $c['username'];
                $cp['uid']      = $cid;
                $cp['chips']    = abs($cbal);
                $cup[] = $cp;
            } else {
                $cn['name']     = $c['full_name'];
                $cn['username'] = $c['username'];
                $cn['uid']      = $cid;
                $cn['chips']    = abs($cbal);
                $cdown[] = $cn;
            }
        }
        //die;
        $cash = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement WHERE parent_id = $uid");
        $cashbal = $cash->b;
        $cashup = array();
        $cashdown = array();
        if ($cashbal == 0) {
        } elseif ($cashbal < 0) {
            $cashup[0]['name']     = $user->full_name;
            $cashup[0]['username'] = 'Cash';
            $cashup[0]['uid']      = $uid;
            $cashup[0]['chips']    = abs($cashbal);
        } else {
            $cashdown[0]['name']     = $user->full_name;
            $cashdown[0]['username'] = 'Cash';
            $cashdown[0]['uid']      = $uid;
            $cashdown[0]['chips']    = abs($cashbal);
        }
        $plus = array();
        $minus = array();
        $plus = array_merge($pup, $cup, $up, $cashup);
        $minus = array_merge($pdown, $cdown, $down, $cashdown);
        //print_r($plus);
        // print_r($minus);
        // die;
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/masterchip_summary', $data);
        $this->load->view('layout/backend_footer');
    }

    public function superMasterChipSummary()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array();
        $down = array();
        $ub = $this->Common_model->get_single_query("SELECT SUM(set_bal) as b FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet' AND a.settled = 'no'");
        $usb = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement a WHERE a.user_id = $uid");
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $pid = $user->parent_id;
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pb = $this->Common_model->get_single_query("SELECT SUM(parent_bal) as b FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet' AND a.settled = 'no'");
        $ubal = $ub->b + $usb->b;
        $pbal = $pb->b + $usb->b;

        if ($ubal == 0) {
        } elseif ($ubal > 0) {
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
        $pup = array();
        $pdown = array();
        if ($pbal == 0) {
        } elseif ($pbal < 0) {
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
        $cup = array();
        $cdown = array();
        foreach ($childs as $ck => $c) {
            $cid = $c['id'];
            $ccredit = $this->Common_model->get_single_query("SELECT SUM(parent_bal) AS b FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.settled = 'no'");
            $csb = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement a WHERE a.user_id = $cid");
            // print_r($ccredit);
            // echo '-----';
            // print_r($csb);
            $cbal = $ccredit->b + $csb->b;
            if ($cbal == 0) {
            } elseif ($cbal > 0) {
                $cp['name']     = $c['full_name'];
                $cp['username'] = $c['username'];
                $cp['uid']      = $cid;
                $cp['chips']    = abs($cbal);
                $cup[] = $cp;
            } else {
                $cn['name']     = $c['full_name'];
                $cn['username'] = $c['username'];
                $cn['uid']      = $cid;
                $cn['chips']    = abs($cbal);
                $cdown[] = $cn;
            }
        }
        //die;
        $cash = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement WHERE parent_id = $uid");
        $cashbal = $cash->b;
        $cashup = array();
        $cashdown = array();
        if ($cashbal == 0) {
        } elseif ($cashbal < 0) {
            $cashup[0]['name']     = $user->full_name;
            $cashup[0]['username'] = 'Cash';
            $cashup[0]['uid']      = $uid;
            $cashup[0]['chips']    = abs($cashbal);
        } else {
            $cashdown[0]['name']     = $user->full_name;
            $cashdown[0]['username'] = 'Cash';
            $cashdown[0]['uid']      = $uid;
            $cashdown[0]['chips']    = abs($cashbal);
        }
        $plus = array();
        $minus = array();
        $plus = array_merge($pup, $cup, $up, $cashup);
        $minus = array_merge($pdown, $cdown, $down, $cashdown);
        //print_r($plus);
        // print_r($minus);
        // die;
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/supermasterchip_summary', $data);
        $this->load->view('layout/backend_footer');
    }

    public function adminChipSummary()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array();
        $down = array();
        $ub = $this->Common_model->get_single_query("SELECT SUM(set_bal) as b FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet' AND a.settled = 'no'");
        $usb = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement a WHERE a.user_id = $uid");
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $pid = $user->parent_id;
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pb = $this->Common_model->get_single_query("SELECT SUM(parent_bal) as b FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet' AND a.settled = 'no'");
        $ubal = $ub->b + $usb->b;
        $pbal = $pb->b + $usb->b;

        if ($ubal == 0) {
        } elseif ($ubal > 0) {
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
        $pup = array();
        $pdown = array();
        if ($pbal == 0) {
        } elseif ($pbal < 0) {
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
        $cup = array();
        $cdown = array();
        foreach ($childs as $ck => $c) {
            $cid = $c['id'];
            $ccredit = $this->Common_model->get_single_query("SELECT SUM(parent_bal) AS b FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.settled = 'no'");
            $csb = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement a WHERE a.user_id = $cid");
            
            $cbal = $ccredit->b + $csb->b;
            if ($cbal == 0) {
            } elseif ($cbal > 0) {
                $cp['name']     = $c['full_name'];
                $cp['username'] = $c['username'];
                $cp['uid']      = $cid;
                $cp['chips']    = abs($cbal);
                $cup[] = $cp;
            } else {
                $cn['name']     = $c['full_name'];
                $cn['username'] = $c['username'];
                $cn['uid']      = $cid;
                $cn['chips']    = abs($cbal);
                $cdown[] = $cn;
            }
        }
        //die;
        $cash = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement WHERE parent_id = $uid");
        $cashbal = $cash->b;
        $cashup = array();
        $cashdown = array();
        if ($cashbal == 0) {
        } elseif ($cashbal < 0) {
            $cashup[0]['name']     = $user->full_name;
            $cashup[0]['username'] = 'Cash';
            $cashup[0]['uid']      = $uid;
            $cashup[0]['chips']    = abs($cashbal);
        } else {
            $cashdown[0]['name']     = $user->full_name;
            $cashdown[0]['username'] = 'Cash';
            $cashdown[0]['uid']      = $uid;
            $cashdown[0]['chips']    = abs($cashbal);
        }
        $plus = array();
        $minus = array();
        $plus = array_merge($pup, $cup, $up, $cashup);
        $minus = array_merge($pdown, $cdown, $down, $cashdown);
        //print_r($plus);
        // print_r($minus);
        // die;
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/adminchip_summary', $data);
        $this->load->view('layout/backend_footer');
    }

    public function superAdminChipSummary()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array();
        $down = array();
        $ub = $this->Common_model->get_single_query("SELECT SUM(set_bal) as b FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet' AND a.settled = 'no'");
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $pid = $user->parent_id;
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pb = $this->Common_model->get_single_query("SELECT SUM(parent_bal) as b FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet' AND a.settled = 'no'");
        $ubal = $ub->b;
        $pbal = $pb->b;
        if ($ubal == 0) {
        } elseif ($ubal > 0) {
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
        $pup = array();
        $pdown = array();
        if ($pbal == 0) {
        } elseif ($pbal < 0) {
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
        $cup = array();
        $cdown = array();
        foreach ($childs as $ck => $c) {
            $cid = $c['id'];
            $ccredit = $this->Common_model->get_single_query("SELECT SUM(parent_bal) AS b FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.settled = 'no'");
            $csb = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement a WHERE a.user_id = $cid");
            // print_r($ccredit);
            // echo '-----';
            // print_r($csb);
            $cbal = $ccredit->b + $csb->b;
            if ($cbal == 0) {
            } elseif ($cbal > 0) {
                $cp['name']     = $c['full_name'];
                $cp['username'] = $c['username'];
                $cp['uid']      = $cid;
                $cp['chips']    = abs($cbal);
                $cup[] = $cp;
            } else {
                $cn['name']     = $c['full_name'];
                $cn['username'] = $c['username'];
                $cn['uid']      = $cid;
                $cn['chips']    = abs($cbal);
                $cdown[] = $cn;
            }
        }
        //die;
        $cash = $this->Common_model->get_single_query("SELECT SUM(settlement) as b FROM settlement WHERE parent_id = $uid");
        $cashbal = $cash->b;
        $cashup = array();
        $cashdown = array();
        if ($cashbal == 0) {
        } elseif ($cashbal < 0) {
            $cashup[0]['name']     = $user->full_name;
            $cashup[0]['username'] = 'Cash';
            $cashup[0]['uid']      = $uid;
            $cashup[0]['chips']    = abs($cashbal);
        } else {
            $cashdown[0]['name']     = $user->full_name;
            $cashdown[0]['username'] = 'Cash';
            $cashdown[0]['uid']      = $uid;
            $cashdown[0]['chips']    = abs($cashbal);
        }
        $plus = array();
        $minus = array();
        $plus = array_merge($pup, $cup, $up, $cashup);
        $minus = array_merge($pdown, $cdown, $down, $cashdown);
        //print_r($plus);
        // print_r($minus);
        // die;
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/superadminchip_summary', $data);
        $this->load->view('layout/backend_footer');
    }

    public function olduserChipSummary()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array();
        $down = array();
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet'");
        $ubal = $ucd->c - $ucd->d;

        if ($ubal == 0) {
        } elseif ($ubal > 0) {
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
        $pid = $this->Common_model->findfield('users', 'id', $uid, 'parent_id');
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pcredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.credited_from = $uid");
        $pdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.debited_to = $uid");
        $pbal = $pcredit->c - $pdebit->d;
        $pup = array();
        $pdown = array();
        if ($pbal == 0) {
        } elseif ($pbal > 0) {
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
        $plus = array();
        $minus = array();
        $plus = array_merge($pup, $up);
        $minus = array_merge($pdown, $down);
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/userchip_summary', $data);
        $this->load->view('layout/backend_footer');
    }

    public function oldmasterChipSummary()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array();
        $down = array();
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet'");
        $ubal = $ucd->c - $ucd->d;

        if ($ubal == 0) {
        } elseif ($ubal > 0) {
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
        $pid = $this->Common_model->findfield('users', 'id', $uid, 'parent_id');
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pcredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.credited_from = $uid");
        $pdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.debited_to = $uid");
        $pbal = $pcredit->c - $pdebit->d;

        $pup = array();
        $pdown = array();
        if ($pbal == 0) {
        } elseif ($pbal > 0) {
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
        $cup = array();
        $cdown = array();
        foreach ($childs as $ck => $c) {
            $cid = $c['id'];
            $ccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.credited_from = $uid");
            $cdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.debited_to = $uid");
            $cbal = $ccredit->c - $cdebit->d;
            $sccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.credited_from = $uid");
            $scdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.debited_to = $uid");
            $scbal = $sccredit->c - $scdebit->d;

            if ($cbal < 0 && $scbal < 0) {
                if ($cbal < $scbal) {
                    $fcbal = $cbal - ($scbal);
                } else {
                    $fcbal = $scbal - ($cbal);
                }
            } elseif ($cbal > 0 && $scbal < 0) {
                $fcbal = $cbal + ($scbal);
            } elseif ($cbal < 0 && $scbal > 0) {
                $fcbal = $cbal + ($scbal);
            } else {
                $fcbal = $cbal - $scbal;
            }
            //echo $c['username'].' ### '.$cbal.' **** '.$scbal.' $$$ '.$fcbal.'<hr/>';
            if ($fcbal == 0) {
            } elseif ($fcbal > 0) {
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
        $sbal = $stc->c - $std->d;
        $sup = array();
        $sdown = array();
        if ($sbal == 0) {
        } elseif ($sbal > 0) {
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

        $plus = array();
        $minus = array();
        $plus = array_merge($pup, $cup, $up, $sup);
        $minus = array_merge($pdown, $cdown, $down, $sdown);
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/masterchip_summary', $data);
        $this->load->view('layout/backend_footer');
    }

    public function oldsuperMasterChipSummary()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array();
        $down = array();
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet'");
        $ubal = $ucd->c - $ucd->d;

        if ($ubal == 0) {
        } elseif ($ubal > 0) {
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
        $pid = $this->Common_model->findfield('users', 'id', $uid, 'parent_id');
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pcredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.credited_from = $uid");
        $pdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'bet' AND a.debited_to = $uid");
        $pbal = $pcredit->c - $pdebit->d;

        $pup = array();
        $pdown = array();
        if ($pbal == 0) {
        } elseif ($pbal > 0) {
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
        $cup = array();
        $cdown = array();
        foreach ($childs as $ck => $c) {
            $cid = $c['id'];
            $ccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.credited_from = $uid");
            $cdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.debited_to = $uid");
            $cbal = $ccredit->c - $cdebit->d;
            echo $cbal . '<br/>';
            $sccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.credited_from = $uid");
            $scdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.debited_to = $uid");
            $scbal = $sccredit->c - $scdebit->d;
            if ($cbal < 0 && $scbal < 0) {
                $fcbal = $cbal + ($scbal);
            } elseif ($cbal > 0 && $scbal < 0) {
                $fcbal = $cbal + ($scbal);
            } elseif ($cbal < 0 && $scbal > 0) {
                $fcbal = $cbal + ($scbal);
            } else {
                $fcbal = $cbal - $scbal;
            }
            if ($fcbal == 0) {
            } elseif ($fcbal > 0) {
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
        die;
        //settlement calculation
        $stc = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'settlement' AND a.credited_from IN (SELECT id FROM users WHERE parent_id = $uid)");
        $std = $this->Common_model->get_single_query("SELECT SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'settlement' AND a.debited_to IN (SELECT id FROM users WHERE parent_id = $uid)");
        $sbal = $stc->c - $std->d;
        $sup = array();
        $sdown = array();
        if ($sbal == 0) {
        } elseif ($sbal > 0) {
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

        $plus = array();
        $minus = array();
        $plus = array_merge($pup, $cup, $up, $sup);
        $minus = array_merge($pdown, $cdown, $down, $sdown);
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/supermasterchip_summary', $data);
        $this->load->view('layout/backend_footer');
    }

    public function oldadminChipSummary()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $uid = $this->input->get('user_id');
        $up = array();
        $down = array();
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'bet'");
        $ubal = $ucd->c - $ucd->d;

        if ($ubal == 0) {
        } elseif ($ubal > 0) {
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
        $cup = array();
        $cdown = array();
        foreach ($childs as $ck => $c) {
            $cid = $c['id'];
            $ccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.credited_from = $uid");
            $cdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'bet' AND a.debited_to = $uid");
            $cbal = $ccredit->c - $cdebit->d;
            $sccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.credited_from = $uid");
            $scdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.debited_to = $uid");
            $scbal = $sccredit->c - $scdebit->d;
            if ($cbal < 0 && $scbal < 0) {
                $fcbal = $cbal + ($scbal);
            } elseif ($cbal > 0 && $scbal < 0) {
                $fcbal = $cbal + ($scbal);
            } elseif ($cbal < 0 && $scbal > 0) {
                $fcbal = $cbal + ($scbal);
            } else {
                $fcbal = $cbal - $scbal;
            }
            if ($fcbal == 0) {
            } elseif ($fcbal > 0) {
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
        $sbal = $stc->c - $std->d;
        $sup = array();
        $sdown = array();
        if ($sbal == 0) {
        } elseif ($sbal > 0) {
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

        $plus = array();
        $minus = array();
        $plus = array_merge($cup, $up, $sup);
        $minus = array_merge($cdown, $down, $sdown);
        $data['type'] = 'bet';
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/adminchip_summary', $data);
        $this->load->view('layout/backend_footer');
    }

    public function chipSummaryOld()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);

        $uid = $this->input->get('user_id');
        $type = 'bet';

        //user calculation
        $up = array();
        $down = array();
        $user = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $uid");
        $ucd = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = '$type'");
        $ubal = $ucd->c - $ucd->d;

        if ($ubal == 0) {
        } elseif ($ubal > 0) {
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
        $cup = array();
        $cdown = array();
        foreach ($childs as $ckey => $c) {
            $cid = $c['id'];
            $child = $this->Common_model->get_single_query("select * from users_with_groups where id = $cid");
            $ccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = '$type' AND a.credited_from = $uid");
            $cdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = '$type' AND a.debited_to = $uid");
            $cbal = $ccredit->c - $cdebit->d;
            $sccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.credited_from = $uid");
            $scdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $cid AND a.type = 'settlement' AND a.debited_to = $uid");
            $scbal = $sccredit->c - $scdebit->d;
            if ($cbal == 0) {
            } elseif ($cbal > 0) {
                if ($scbal && (abs($cbal) - abs($scbal)) > 0) {
                    $cup[$ckey]['name']     = $child->full_name;
                    $cup[$ckey]['username'] = $child->username;
                    $cup[$ckey]['uid']      = $cid;
                    $cup[$ckey]['chips']    = abs(abs($cbal) - abs($scbal));
                } elseif ($scbal && (abs($cbal) - abs($scbal)) < 0) {
                    $cdown[$ckey]['name']     = $child->full_name;
                    $cdown[$ckey]['username'] = $child->username;
                    $cdown[$ckey]['uid']      = $cid;
                    $cdown[$ckey]['chips']    = abs(abs($cbal) - abs($scbal));
                } else {
                    $cup[$ckey]['name']     = $child->full_name;
                    $cup[$ckey]['username'] = $child->username;
                    $cup[$ckey]['uid']      = $cid;
                    $cup[$ckey]['chips']    = abs(abs($cbal) - abs($scbal));
                }
            } else {
                if ($scbal && (abs($cbal) - abs($scbal)) > 0) {
                    $cdown[$ckey]['name']     = $child->full_name;
                    $cdown[$ckey]['username'] = $child->username;
                    $cdown[$ckey]['uid']      = $cid;
                    $cdown[$ckey]['chips']    = abs(abs($cbal) - abs($scbal));
                } elseif ($scbal && (abs($cbal) - abs($scbal)) < 0) {
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
        }
        //parent calculation
        $pid = $this->Common_model->findfield('users', 'id', $uid, 'parent_id');
        $puser = $this->Common_model->get_single_query("SELECT * FROM users_with_groups WHERE id = $pid");
        $pcredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $pid AND a.type = '$type' AND a.credited_from = $uid");
        $pdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $pid AND a.type = '$type' AND a.debited_to = $uid");
        $pbal = $pcredit->c - $pdebit->d;
        $pccredit = $this->Common_model->get_single_query("SELECT SUM(a.credits) AS c FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'settlement' AND a.credited_from = $uid");
        $pcdebit = $this->Common_model->get_single_query("SELECT SUM(a.debits) AS d FROM credits_debits a WHERE a.user_id = $pid AND a.type = 'settlement' AND a.debited_to = $uid");
        $pcbal = $pccredit->c - $pcdebit->d;

        $pup = array();
        $pdown = array();
        if ($pbal == 0) {
        } elseif ($pbal > 0) {
            if ($pcbal && (abs($pbal) - abs($pcbal)) > 0) {
                $pup[0]['username']    = 'Parent A/C';
                $pup[0]['name']        = $puser->full_name;
                $pup[0]['uid']         = $pid;
                $pup[0]['chips']       = abs(abs($pbal) - abs($pcbal));
            } elseif ($pcbal && (abs($pbal) - abs($pcbal)) < 0) {
                $pdown[0]['username']  = 'Parent A/C';
                $pdown[0]['name']      = $puser->full_name;
                $pdown[0]['uid']       = $pid;
                $pdown[0]['chips']     = abs(abs($pbal) - abs($pcbal));
            } else {
                $pup[0]['username']    = 'Parent A/C';
                $pup[0]['name']        = $puser->full_name;
                $pup[0]['uid']         = $pid;
                $pup[0]['chips']       = abs(abs($pbal) - abs($pcbal));
            }
        } else {
            if ($pcbal && (abs($pbal) - abs($pcbal)) > 0) {
                $pdown[0]['username']  = 'Parent A/C';
                $pdown[0]['name']      = $puser->full_name;
                $pdown[0]['uid']       = $pid;
                $pdown[0]['chips']     = abs(abs($pbal) - abs($pcbal));
            } elseif ($pcbal && (abs($pbal) - abs($pcbal)) < 0) {
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
        }
        //settlement calculation
        $sdc = $this->Common_model->get_single_query("SELECT SUM(a.credits) as c, SUM(a.debits) as d FROM credits_debits a WHERE a.user_id = $uid AND a.type = 'settlement'");
        $sbal = $sdc->c - $sdc->d;
        $sup = array();
        $sdown = array();
        if ($sbal == 0) {
        } elseif ($sbal > 0) {
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
        $cpup = array();
        $cpdown = array();
        $cpup = array_merge($pup, $cup);
        $cpdown = array_merge($pdown, $cdown);
        $plus = array();
        $minus = array();
        $plus = array_merge($cpup, $up);
        $minus = array_merge($cpdown, $down);
        $data['type'] = $type;
        $data['plus'] = $plus;
        $data['minus'] = $minus;
        $data['cuser'] = $uid;
        $data['sup'] = $sup;
        $data['sdown'] = $sdown;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/chip_summary', $data);
        $this->load->view('layout/backend_footer');
    }

    public function chipSettlement()
    {
        $cid = $this->input->post('cuser_id'); //current user id
        $chips = $this->input->post('chips');
        $uid = $this->input->post('user_id');
        $type = $this->input->post('type');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $uid, 'group_name');
        $pid = $this->Common_model->findfield('users', 'id', $uid, 'parent_id');
        $message = 'chip settlement by ' . $this->Common_model->findfield('users', 'id', $pid, 'username');
        if ($type == 'plus') {
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
            $this->Crud_model->insert_record('chips_withdraw', $wdata);
            $txnid = md5(microtime());
            $udcdata = array(
                'txnid'             => $txnid,
                'user_id'           => $uid,
                'credits'           => 0,
                'credited_from'     => 0,
                'debits'            => $chips,
                'debited_to'        => $pid,
                'balance'           => 0,
                'assigned_by'       => $this->id,
                'transaction_date'  => date('Y-m-d H:i:s'),
                'description'       => $message,
                'type'              => 'settlement',
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('credits_debits', $udcdata);
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
            $this->Crud_model->insert_record('credits_debits', $pdcdata);
            $sdata = array(
                'user_id'           => $uid,
                'settlement'        => -$chips,
                'message'           => $message,
                'parent_id'         => $pid,
                'settlement_date'   => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('settlement', $sdata);
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
            $this->Crud_model->insert_record('chips_withdraw', $wdata);
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
            $this->Crud_model->insert_record('credits_debits', $udcdata);
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
            $this->Crud_model->insert_record('credits_debits', $pdcdata);
            $sdata = array(
                'user_id'           => $uid,
                'settlement'        => $chips,
                'message'           => $message,
                'parent_id'         => $pid,
                'settlement_date'   => date('Y-m-d H:i:s')
            );
            $this->Crud_model->insert_record('settlement', $sdata);
        }
        $this->updateDCBal($uid);
        $this->updateDCBal($pid);
        $this->updateBal($uid);
        $this->updateBal($pid);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User account has been settled</div>");
        redirect('SuperAdmin/chipSummary?user_id=' . $cid);
    }

    public function profitLoss()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $statements = $this->Common_model->get_data_by_query("SELECT a.*, b.id as bid, b.market FROM credits_debits a LEFT JOIN bet b on a.bet_id = b.id where a.user_id = $this->id AND a.type = 'bet' order by a.id desc");
        $outlist = array();
        foreach ($statements as $skey => $s) {
            $match_id = $s['match_id'];
            if (!isset($match_id)) {
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
            $cd = $this->Common_model->get_single_query("SELECT SUM(credits) AS c, SUM(debits) AS d FROM credits_debits WHERE match_id = " . $sv['match_id'] . " AND user_id = " . $sv['user_id'] . "");
            $commission = $this->Common_model->get_single_query("SELECT SUM(credits) AS c, SUM(debits) AS d FROM credits_debits WHERE match_id = " . $sv['match_id'] . " AND user_id = " . $this->id . " AND commission = 'yes' ");
            $credits = $cd->c;
            $debits = $cd->d;
            $pl = $credits - $debits;
            $cl = $pl >= 0 ? 'text-success' : 'text-danger';
            $st[$ss]['p_l'] = $pl;
            $st[$ss]['a_c'] = abs($commission->c - $commission->d);
            $st[$ss]['c_l'] = $cl;
        }
        $data['statements'] = $st;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/profit_loss', $data);
        $this->load->view('layout/backend_footer');
    }

    public function betByMatchId()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        $mid = $this->input->get('match_id');
        $data['bets'] = $this->Common_model->get_data_by_query("select * from bet where match_id = $mid order by id DESC");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/bet_matchid', $data);
        $this->load->view('layout/backend_footer');
    }

    public function betHistory()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/backend_header', $hdata);
        //pagination
        $config = array();
        $config["base_url"] = base_url() . "SuperAdmin/betHistory";
        $config["total_rows"] = $this->Common_model->record_count('bet');
        $config["per_page"] = 1000;
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = $this->Common_model->record_count('bet');
        $config["uri_segment"] = 3;
        // custom paging configuration
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;

        $config['full_tag_open'] = '<ol class="pagination">';
        $config['full_tag_close'] = '</ol>';

        $config['first_link'] = 'First Page';
        $config['first_tag_open'] = '<li class="firstlink">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'Last Page';
        $config['last_tag_open'] = '<li class="lastlink">';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = 'Next Page';
        $config['next_tag_open'] = '<li class="nextlink">';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = 'Prev Page';
        $config['prev_tag_open'] = '<li class="prevlink">';
        $config['prev_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="numlink">';
        $config['num_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        if ($page > 0) {
            $page = $page - 1;
        }
        $data["bets"] = $this->Common_model->get_data_by_limit($config["per_page"], $page * 1000, 'bet');
        //echo $this->db->last_query();die;
        $str_links = $this->pagination->create_links();
        $data["links"] = $this->pagination->create_links();
        //print_r($data['bets']);die;
        //$data['bets'] = $this->Common_model->get_data_by_query("SELECT a.*, b.username FROM bet a LEFT JOIN users b ON a.user_id = b.id ORDER BY a.id DESC");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/bet_history', $data);
        $this->load->view('layout/backend_footer');
    }

    public function getData()
    {
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

    public function getCricket()
    {
        $data = $this->getData();
        //$mdata = $data['result'];
        $cricket = array();
        foreach ($data as $key => $d) {
            if ($d['SportID'] == 4 && $d['name'] == 'Match Odds') {
                $cricket[] = $data[$key];
            }
        }
        return $cricket;
    }

    public function matchOdd($marketId)
    {
        $url = "http://rohitash.dream24.bet:3000/getmarket?id=" . $marketId;
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

    public function fileData()
    {
        $result = file_get_contents('./uploads/cricket.json');
        return json_decode($result, true);
    }

    public function fancyData($marketId)
    {
        //$marketId = $this->input->get('market_id');
        //$url = "http://fancy.royalebet.uk/".$eid;
        //$url = "http://fancy.royalebet.uk/";
        $url = "http://fancy.dream24.bet/price/?name=" . $marketId;
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

    public static function modify($json_data)
    {
        $data = '}{"status":{"statusCode"';
        $output = $json_data;
        $output = preg_replace('!\s+!', ' ', $json_data);
        $position = strpos($output, $data);
        while ($position > 0) {
            $string = ",";
            $output = substr_replace($output, $string, $position + 1, 0);
            $position = strpos($output, $data, $position + 1);
        }
        return $output;
    }

    public function modifyJson($json_data)
    {
        $data = '}{';
        $position = strpos($json_data, $data);
        if ($position > 0) {
            $newData = substr($json_data, 0, strpos($json_data, $data));
            $newData .= "}";
            $result = json_decode($newData, true);
        } else {
            $result = json_decode($data, true);
        }
        return $result;
    }

    public function allCricket()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $today = date('Y-m-d');
        $data['crickets'] = $this->Common_model->get_data_by_query("SELECT * FROM cron_data where event_id NOT IN (SELECT event_id FROM running_matches)");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/all_cricket', $data);
        $this->load->view('layout/backend_footer');
    }

    public function saveCricbuzzId() {

        $id = $this->input->get('id');
        $matchId = $this->input->get('cricbuzz_id');
        $match = $this->Common_model->get_single_query("select * from running_matches where id = $id");
        $data = array(
            'cricbuzz_id' => $matchId,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record('running_matches', $id, $data);
        echo json_encode(array('success' => true, 'message' => 'Cricbuzz Id updated'));
    }

    public function enableBetting()
    {
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
        $this->Crud_model->insert_record('running_matches', $data);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Match added for betting</div>");
        redirect('SuperAdmin/allCricket');
    }

    public function runningCricket()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $today = date('Y-m-d');
        $data['crickets'] = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' OR match_result = 'paused'");

        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/running_matches', $data);
        $this->load->view('layout/backend_footer');
    }

    public function playPauseMatch()
    {
        $id = $this->input->get('id');
        $match = $this->Common_model->get_single_query("select * from running_matches where id = $id");
        $status = $match->match_result == 'running' ? 'paused' : 'running';
        $data = array(
            'match_result' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record('running_matches', $id, $data);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Match status has been changed</div>");
        redirect('SuperAdmin/runningCricket');
    }

    public function matchOdds()
    {
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $eid = $this->input->get('match_id');
        $mid = $this->input->get('market_id');
        $data['match'] = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $data['odds'] = $this->match->matchOddByMarketId($mid);
        $data['dfancy'] = $this->Common_model->get_data_by_query("SELECT * FROM fancy_data WHERE market_id = '$mid' AND status NOT IN ('settled','paused')");
        $data['fancy'] = $this->match->matchFancies($mid);
        $data['ubets'] = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and bet_type = 'unmatched'");
        $data['mbets'] = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and bet_type = 'matched'");
        $data['fbets'] = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and bet_type = 'fancy'");

       
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/match_odds', $data);
        $this->load->view('layout/backend_footer');
    }

    public function scoreReload() {
        $mkid = $this->input->get('market_id');
        $mid = $this->input->get('match_id');
        $match = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = '$mkid' AND event_id = $mid");
        $score = '';
        if(isset($match) && ($match->cricbuzz_id != '' || $match->cricbuzz_id != null)) {
            $scoreData = $this->match->cricketScore($match->cricbuzz_id);
            $miniscore = $scoreData['miniscore'];
            // print_r($scoreData['miniscore']);die;
            $score .= '<p class="text-danger">'.$miniscore['matchScoreDetails']['customStatus'].'</p>';
            $score .= '<table class="table table-bordered table-sm table-striped"><tbody>';
            foreach ($miniscore['matchScoreDetails']['inningsScoreList'] as $key => $sc) {
                $score .= '<tr><td>'.$sc['batTeamName'].' '.$sc['score'].'/'.$sc['wickets'].' ('.$sc['overs'].' ov)</td></tr>';
            }
            $score .= '<tr><th><div class="d-flex justify-content-between mb-3"><div class="p-2 ">Cur Ov '.$miniscore['overs'].'</div><div class="p-2">Runrate '.$miniscore['currentRunRate'].'</div></div></th></tr>';
            $score .= '<tr><th>Recent Stats '.$miniscore['recentOvsStats'].'</th></tr>';

        }
        echo json_encode(array(
            'score' => $score
        ));
    }

    public function fancyReload()
    {
        $mid = $this->input->get('market_id');
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $dfancy = $this->Common_model->get_data_by_query("SELECT * FROM fancy_data WHERE market_id = '$mid' AND status NOT IN ('settled','paused')");
        $fancy = $this->match->matchFancies($match->event_id);
        $fancyData = '<table class="table table-bordered" width="100%">
        <tr>
        <th style="border: none !important;" width="50%"></th>
        <th style="background: red; color: white; border: none !important;" width="25%"><center>NO(L)</center></th>
        <th style="background: #2c5ca9; color: white; border: none !important;" width="25%"><center>YES(B)</center></th>
      </tr>';
        if ($dfancy) {
            $did = array();
            foreach ($dfancy as $dkey => $d) {
                $did[] = $d['fancy_id'];
            }
            if ($fancy) {
                foreach ($fancy as $fk => $f) {
                    if (in_array($f['SelectionId'], $did)) {
                        $fff = $f['RunnerName'];
                        $mmm = $match->market_id;
                        $fanca = "getBookedFancy('$fff','$mmm')";
                        $fancyData .= '<tr>
                                        <td>' . $f['RunnerName'] . '<span class="pull-right"><button class="btn btn-warning btn-sm" onclick="' . $fanca . '" data-toggle="modal" data-target="#bookFancyModal">book</button></span></td>
                                        <td style="background-color: #ffbfcd; cursor: pointer; text-align: center;"><b>' . $f['LayPrice1'] . '</b><br>' . $f['LaySize1'] . '</td>
                                        <td style="background-color: #b5e0ff; cursor: pointer; text-align: center;"><b>' . $f['BackPrice1'] . '</b><br>' . $f['BackSize1'] . '</td>
                                      </tr>';
                    }
                }
            }
        }
        $fancyData .= '</table>';
        $data = array('score' => '', 'fancy' => $fancyData);
        echo json_encode($data);
    }

    public function matchReload()
    {
        $mid = $this->input->get('market_id');
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $odds = $this->match->matchOddByMarketId($mid);
        // $matchOdds = $odds[0]['teams'];
        $oddData = '<table class="table table-bordered table-condensed" width="100%" >
                        <tr>
                          <th style="border: none !important;"><b style="color: red;">Min stake:100 Max stake:200000</b></th>
                          <th colspan="3" style="background: #2c5ca9; color: white; border: none !important; min-width: 50px;  max-width: 50px;"><center>back</center></th>
                          <th colspan="3" style="background: red; color: white; border: none !important; min-width: 50px; max-width: 50px;"><center>lay</center></th>
                        </tr>';
        foreach ($odds as $mk => $mo) {

            $oddData .= '<tr>
                <td><b>' . $mo['RunnerName'] . '</b><span class="pull-right" id="' . $mo['SelectionId'] . '"></span></td>
                <td style="background: #b5e0ff; cursor: pointer;"><center><b>' . $mo['BackPrice1'] . '</b><br/>' . $mo['BackSize1'] . '</center></td>
                <td style="background: #b5e0ff; cursor: pointer;"><center><b>' . $mo['BackPrice2'] . '</b><br/>' . $mo['BackSize2'] . '</center></td>
                <td style="background: #b5e0ff; cursor: pointer;"><center><b>' . $mo['BackPrice3'] . '</b><br/>' . $mo['BackSize3'] . '</center></td>
                <td style="background: #ffbfcd; cursor: pointer;" ><center><b>' . $mo['LayPrice1'] . '</b><br/>' . $mo['LaySize1'] . '</center></td>
                <td style="background: #ffbfcd; cursor: pointer;" ><center><b>' . $mo['LayPrice2'] . '</b><br/>' . $mo['LaySize2'] . '</center></td>
                <td style="background: #ffbfcd; cursor: pointer;" ><center><b>' . $mo['LayPrice3'] . '</b><br/>' . $mo['LaySize3'] . '</center></td>
            </tr>';
        }
        $oddData .= '</table>';
        echo $oddData;
    }

    public function betReload()
    {
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
        foreach ($ubets as $ub) :
            if ($ub['back_lay'] == 'back') $class = 'back';
            else $class = 'lay';
            echo '<tr class="' . $class . '">
                            <td>' . $ub['team'] . '</td>
                            <td>' . $ub['back_lay'] . '</td>
                            <td>' . $ub['odd'] . '</td>
                            <td>' . $ub['stake'] . '</td>
                            <td>' . $ub['profit'] . '</td>
                            <td>' . $ub['loss'] . '</td>
                            <td>' . $ub['ip'] . '</td>
                            <td>' . $ub['id'] . '</td>
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
        foreach ($mbets as $mb) :
            if ($mb['back_lay'] == 'back') $class = 'back';
            else $class = 'lay';
            echo '<tr class="' . $class . '">
                            <td>' . $mb['team'] . '</td>
                            <td>' . $mb['back_lay'] . '</td>
                            <td>' . $mb['odd'] . '</td>
                            <td>' . $mb['stake'] . '</td>
                            <td>' . $mb['profit'] . '</td>
                            <td>' . $mb['loss'] . '</td>
                            <td>' . $mb['ip'] . '</td>
                            <td>' . $mb['id'] . '</td>
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
        foreach ($fbets as $fb) :
            if ($fb['back_lay'] == 'back') $class = 'back';
            else $class = 'lay';
            echo '<tr class="' . $class . '">
                            <td>' . $fb['team'] . '</td>
                            <td>' . $fb['back_lay'] . '</td>
                            <td>' . $fb['odd'] . '</td>
                            <td>' . $fb['stake'] . '</td>
                            <td>' . $fb['profit'] . '</td>
                            <td>' . $fb['loss'] . '</td>
                            <td>' . $fb['ip'] . '</td>
                            <td>' . $fb['id'] . '</td>
                          </tr>';
        endforeach;
        echo '</table>
                    </div>
                  </div>   
                  <div class="clearfix"></div>
              </div>';
    }

    public function profitNLoss()
    {
        $market_id = $this->input->get('market_id');
        $match = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = $market_id");
        $runners = json_decode($match->teams);
        $supermasters = $this->Common_model->get_data_by_query("select * from users where parent_id = $this->id");
        $abcd = array();
        foreach ($supermasters as $sk => $sv) {
            $ssd = $sv['id'];
            foreach ($runners as $rk => $r) {
                $tid = $r->id;
                $allTeams[$rk]['back'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $ssd)) AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'back' AND bet_type = 'matched'");
                $allTeams[$rk]['lay'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $ssd)) AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'lay' AND bet_type = 'matched'");
                $allTeams[$rk]['tid'] = $tid;
            }
            $adminPartnership = $this->Common_model->findfield('users', 'id', $ssd, 'commission');

            foreach ($runners as $rk => $rv) {
                $abcd[$rk]['pl'] += ($adminPartnership * $this->calculateNewResult($allTeams, $rk)) / 100;
            }
        }
        $res = array();
        foreach ($runners as $rk => $r) {
            $tid = $r->id;
            $res[$rk]['pl'] = $abcd[$rk]['pl'];
            $res[$rk]['id'] = $tid;
        }
        $res = array_values($res);
        echo json_encode($res);
    }

    function calculateNewResult($input_array, $index)
    {
        $final = 0;
        $plus = array();
        $minus = array();
        for ($i = 0; $i < count($input_array); $i++) {
            if ($i == $index) {
                $plus[$i] = $input_array[$i]['back']->p;
                $minus[$i] = $input_array[$i]['lay']->l;
                $final += ($input_array[$i]['back']->p - $input_array[$i]['lay']->l);
            } else {
                $plus[$i] = $input_array[$i]['lay']->p;
                $minus[$i] = $input_array[$i]['back']->l;
                $final += ($input_array[$i]['lay']->p - $input_array[$i]['back']->l);
            }
        }
        return $final;
    }

    function calculateResult($input_array, $index)
    {
        $final = 0;
        if (count($input_array) == 3) {

            if ($index == 0) {
                $team1 = $input_array[0];
                $team2 = $input_array[1];
                $team3 = $input_array[2];
            }
            if ($index == 1) {
                $team1 = $input_array[1];
                $team2 = $input_array[0];
                $team3 = $input_array[2];
            }
            if ($index == 2) {
                $team1 = $input_array[2];
                $team2 = $input_array[1];
                $team3 = $input_array[0];
            }

            $x1 = $team1['back']->p;
            $x2 = $team2['lay']->p;
            $x3 = $team3['lay']->p;

            $y1 = $team1['lay']->l;
            $y2 = $team2['back']->l;
            $y3 = $team3['back']->l;
            $final = ($x1 + $x2 + $x3) - ($y1 + $y2 + $y3);
            return $final;
        }
        if (count($input_array) == 2) {

            if ($index == 0) {
                $team1 = $input_array[0];
                $team2 = $input_array[1];
            }
            if ($index == 1) {
                $team1 = $input_array[1];
                $team2 = $input_array[0];
            }

            $x1 = $team1['back']->p;
            $x2 = $team2['lay']->p;

            $y1 = $team1['lay']->l;
            $y2 = $team2['back']->l;
            $final = ($x1 + $x2) - ($y1 + $y2);
            return $final;
        }
    }

    public function showPana()
    {
        $mkid = $this->input->get('market_id');
        $match = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = $mkid");
        $runners = json_decode($match->teams);
        $smasters = $this->Common_model->get_data_by_query("select * from users where parent_id = $this->id");
        echo '<table class="table table-responsive table-striped table-bordered"><thead><tr><th>Username</th>';
        foreach ($runners as $r) {
            echo '<th>' . $r->name . '</th>';
        }
        echo '</tr></thead><tbody>';
        foreach ($smasters as $sk => $s) {
            $uid = $s['id'];
            echo '<tr><td>' . $s['username'] . '</td>';
            foreach ($runners as $rk => $r) {
                $tid = $r->id;
                $allTeams[$rk]['back'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $uid)) AND market_id = '$mkid' AND team_id = $tid AND back_lay = 'back' AND bet_type = 'matched'");
                $allTeams[$rk]['lay'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id IN(SELECT id FROM users WHERE parent_id = $uid)) AND market_id = '$mkid' AND team_id = $tid AND back_lay = 'lay' AND bet_type = 'matched'");
                $allTeams[$rk]['tid'] = $tid;
            }
            $adminPartnership = $this->Common_model->findfield('users', 'id', $uid, 'commission');
            foreach ($runners as $rk => $r) {
                $tid = $r->id;
                $pl = ($adminPartnership * $this->calculateNewResult($allTeams, $rk)) / 100;
                $class = $pl >= 0 ? 'text-danger' : 'text-success';
                echo '<td><span class="' . $class . '">' . abs($pl) . '</span></td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table>';
    }

    public function teamProfitLossSuperMaster()
    {
        $mid = $this->input->get('market_id');
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $teams = json_decode($match->teams, true);
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
            $adminPartnership = $this->Common_model->findfield('users', 'id', $ssd, 'commission');
            $team1_pl = ($team1win * $adminPartnership) / 100;
            $team2_pl = ($team2win * $adminPartnership) / 100;
            $team1_pl = abs(round($team1_pl, 2));
            $team2_pl = abs(round($team2_pl, 2));
            if ($team1win > 0) {
                $team1_cl = 'text-danger';
            } else {
                $team1_cl = 'text-success';
            }
            if ($team2win > 0) {
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
                    <td>' . $team1Name . '</td>
                    <td>' . $team2Name . '</td>
                    <td>Draw</td>
                  </tr>
                </thead>
                <tbody>';
        foreach ($p_l as $plk => $p) :
            echo '<tr>
                    <td>' . $p['username'] . '</td>
                    <td><span class="' . $p['team1_cl'] . '">' . $p['team1_pl'] . '</span></td>
                    <td><span class="' . $p['team2_cl'] . '">' . $p['team2_pl'] . '</span></td>
                    <td>0</td>
                  </tr>';
        endforeach;
        echo   '</tbody>
              </table>';
    }

    public function getBookedFancy()
    {
        $runner = $this->input->get('runner');
        $mid = $this->input->get('market_id');
        $bets = $this->Common_model->get_data_by_query("SELECT a.* FROM bet a LEFT JOIN users_with_groups b ON a.user_id = b.id WHERE a.market_id = '$mid' AND a.team = '$runner' AND a.bet_type = 'fancy' ORDER BY a.odd ASC");
        echo '<h4>'.$runner.'</h4>';
        $aa =  '<table class="table table-bordered table-striped" width="100%">
                <thead>
                    <tr class="headings">
                        <th>Score</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>';
        $i = 1;
        $odds = array();
        foreach ($bets as $bk => $b) {
            if (in_array($b['odd'], $odds)) {
            } else {
                $odds[] = $b['odd'];
            }
        }
        foreach ($odds as $ok => $o) {
            $yy = $this->Common_model->get_single_query("SELECT SUM(a.profit) AS p FROM bet a LEFT JOIN users_with_groups b ON a.user_id = b.id WHERE a.market_id = '$mid' AND a.team = '$runner' AND a.bet_type = 'fancy' AND a.odd = $o AND back_lay = 'back'");
            $nn = $this->Common_model->get_single_query("SELECT SUM(a.profit) AS p FROM bet a LEFT JOIN users_with_groups b ON a.user_id = b.id WHERE a.market_id = '$mid' AND a.team = '$runner' AND a.bet_type = 'fancy' AND a.odd = $o AND back_lay = 'lay'");
            $pp = $yy->p;
            $ll = $nn->p;
            $final = 0;
            $final = $pp - $ll;
            if($final > 0) {
                $show = '<span class="text-danger">'.$final.'</span>';
            } else {
                $show = '<span class="text-success">'.$final.'</span>';
            }
            $aa .= '<tr>
                    <td>'.$o.'</td>
                    <td>'.$show.'</td>
                </tr>';
        }
        $aa .= '</tbody></table>';
        echo $aa;
    }

    public function viewMatchFancy()
    {
        $eid = $this->input->get('match_id');
        $mid = $this->input->get('market_id');
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $data['dfancy'] = $this->Common_model->get_data_by_query("select * from fancy_data where market_id = '$mid'");
        $data['match'] = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/fancy_data', $data);
        $this->load->view('layout/backend_footer');
    }

    public function fancyStatus()
    {
        $fdid = $this->input->get('fdid');
        $fid = $this->input->get('fancy_id');
        $mid = $this->input->get('market_id');
        $fdata = $this->Common_model->get_single_query("select * from fancy_data where id = $fdid");
        $status = $fdata->status;
        if ($status == 'paused') {
            $nstatus = 'playing';
        } else {
            $nstatus = 'paused';
        }
        $data = array(
            'status'        => $nstatus,
            'updated_at'    => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record('fancy_data', $fdata->id, $data);
        $msg = array('status' => $nstatus);
        echo json_encode($msg);
    }

    public function singleFancy()
    {
        $mid = $this->input->get('market_id');
        $fname = $this->input->get('fancy_name');
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $data['fancy'] = $this->Common_model->get_single_query("select * from fancy_data where market_id = '$mid' and fancy_name = '$fname'");
        $data['match'] = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/single_fancy', $data);
        $this->load->view('layout/backend_footer');
    }

    public function unsettleFancy()
    {
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
        $this->Crud_model->edit_record('fancy_data', $fdid, $fdata);
        $this->fancyUnSettlement($mid, $fid, $fdid);
    }

    public function fancyUnSettlement($mid, $fid, $fdid)
    {
        $fancy = $this->Common_model->get_single_query("select * from fancy_data where id = $fdid");
        $bets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mid' and team = '$fancy->fancy_name' and bet_type = 'fancy'");
        foreach ($bets as $bkey => $b) {
            $data = array(
                'profit'    => 0,
                'loss'      => 0,
                'status'    => 'settled',
                'updated_at' => date('Y-m-d H:i:s')
            );
            $this->Crud_model->edit_record('bet', $b['id'], $data);
        }
        $this->updateBalForUsers();
        $this->updateBalForAll();
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Fancy result has been unsettled</div>");
        redirect('SuperAdmin/viewMatchFancy?match_id=' . $fancy->event_id . '&market_id=' . $mid);
    }

    public function declareFancy()
    {
        $mid = $this->input->get('market_id');
        $fid = $this->input->get('fancy_id');
        $fline = $this->input->get('fancy_score');
        $fdid = $this->input->get('fdid');
        $fancyRecord = $this->Common_model->get_single_query("SELECT * FROM fancy_data WHERE id = $fdid");
        if ($fancyRecord->status != 'settled') {
            $fdata = array(
                'status'        => 'settled',
                'result'        => 'declared',
                'line'          => $fline,
                'updated_at'    => date('Y-m-d H:i:s')
            );
            $this->Crud_model->edit_record('fancy_data', $fdid, $fdata);

            $this->fancySettlement($mid, $fid, $fdid);
        }
    }

    public function matchResult()
    {
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
        foreach ($teams as $t) {
            $data .= '<option value="' . $t->id . '_' . $t->name . '">' . $t->name . '</option>';
        }
        $data .= '<option value="0_tie">Tie/Abondoned</option>';
        $data .= '</select>
                            <input type="hidden" name="match_id" id="match_id" value="' . $match->event_id . '">
                            <input type="hidden" name="market_id" id="market_id" value="' . $match->market_id . '">
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

    public function resultDeclare()
    {
        $eid = $this->input->post('match_id');
        $mid = $this->input->post('market_id');
        $winner = $this->input->post('winner');
        $parts = explode('_', $winner);
        $teamName = $parts[1];
        $teamId = $parts[0];
        $matchDetail = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = '$mid'");
        if ($matchDetail->match_result != 'settled') {
            if ($teamName == 'tie') {
                $data = array(
                    'match_result'  => 'settled',
                    'winner'        =>  $teamName,
                    'winner_id'     =>  $teamId,
                    'updated_at'    => date('Y-m-d H:i:s')
                );
                $this->Crud_model->edit_record_by_anyid('running_matches', $mid, $data, 'market_id');
                $this->tieSettlement($eid, $mid);
            } else {
                $data = array(
                    'match_result'  => 'settled',
                    'winner'        =>  $teamName,
                    'winner_id'     =>  $teamId,
                    'updated_at'    => date('Y-m-d H:i:s')
                );
                $this->Crud_model->edit_record_by_anyid('running_matches', $mid, $data, 'market_id');
                $this->betSettlement($eid, $mid);
            }
        }
        //$this->finalBalance();
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Match result has been declared</div>");
        redirect('SuperAdmin/runningCricket');
    }

    public function finalBalance()
    {
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
            if (empty($oids)) {
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
            if (empty($unmids)) {
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

    public function userBalance()
    {
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
            $this->Crud_model->edit_record_by_anyid('user_chips', $uid, $uchipdata, 'user_id');
            $running = $this->Common_model->get_data_by_query("select * from running_matches where match_result = 'running'");
            foreach ($running as $rk => $r) {
                $mkid = $r['market_id'];
                $mid = $r['event_id'];
                $bets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mkid' and user_id = $uid");
                if ($bets) {
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
                    $this->Crud_model->edit_record_by_anyid('user_chips', $uid, $bcdata, 'user_id');
                }
            }
        }
    }

    public function tieSettlement($eid, $mkid)
    {
        $bets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mkid' and status = 'pending'");
        if (empty($bets)) {
        } else {
            foreach ($bets as $bkey => $b) {
                $bid = $b['id'];
                $data = array(
                    'profit'    => 0,
                    'loss'      => 0,
                    'status'    => 'settled',
                    'bet_result'    => 'tie',
                    'updated_at' => date('Y-m-d H:i:s')
                );
                $this->Crud_model->edit_record('bet', $bid, $data);
            }
        }
        $this->updateBalForAll();
        // $this->updateBalForUsers();
        $this->updateBalForUsersByMarket($mkid);
    }

    public function fancySettlement($mkid, $fid, $fdid)
    {
        $fancy = $this->Common_model->get_single_query("select * from fancy_data where id = $fdid");
        $bets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mkid' and team = '$fancy->fancy_name' and bet_type = 'fancy'");
        foreach ($bets as $bkey => $b) {
            $this->betSettlementFancy($b['id'], $mkid);
        }
        $this->updateBalForAll();
        // $this->updateBalForUsers();
        $this->updateBalForUsersByMarket($mkid,$fancy->fancy_name);
        //$this->Crud_model->delete_record('fancy_data',$fdid);
    }

    public function betSettlement($eid, $mkid)
    {
        $bets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mkid' and bet_type != 'fancy'");
        foreach ($bets as $bkey => $b) {
            if ($b['bet_type'] == 'matched') {
                $this->betSettlementMatched($b['id'], $mkid);
            } else {
                $this->betSettlementUnMatched($b['id'], $mkid);
            }
        }
        $this->settleOddCommission($mkid);
        $this->updateBalForAll();
        // $this->updateBalForUsers();
        $this->updateBalForUsersByMarket($mkid);
    }

    public function settleOddCommission($mkid)
    {
        $match = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = $mkid");
        $match_id = $match->event_id;
        $users = $this->Common_model->get_data_by_query("SELECT DISTINCT(user_id) FROM bet WHERE market_id = '$mkid' AND bet_type = 'matched' ");
        if (count($users) > 0) {
            foreach ($users as $u) {
                $uid = $u['user_id'];
                $user = $this->Common_model->get_single_query("select * from users where id = $uid");
                $mid = $user->parent_id; //master id
                $master = $this->Common_model->get_single_query("select * from users where id = $mid");
                $mc = $master->commission; //master partnershipt to super master
                if (empty($mc)) $mc = 0;
                $smid = $master->parent_id; //supermaster id
                $supermaster = $this->Common_model->get_single_query("select * from users where id = $smid");
                $smc = $supermaster->commission; //supermaster partnership to admin
                if (empty($smc)) $smc = 0;
                $aid = $this->Common_model->findfield('users', 'id', $smid, 'parent_id'); //admin id
                $admin = $this->Common_model->get_single_query("select * from users where id = $aid");
                $ac = $supermaster->commission; //admin partnership to superadmin
                if (empty($ac)) $ac = 0;
                $said = $this->Common_model->findfield('users', 'id', $aid, 'parent_id'); //superadmin id
                if ($user->odd_commission > 0 || $master->odd_commission > 0) {
                    $userBetPL = $this->Common_model->get_single_query("SELECT SUM(profit) as p, SUM(loss) as l from bet where user_id = $uid AND match_id = $match_id AND bet_type = 'matched'");
                    $userLoss = $userBetPL->p - $userBetPL->l;
                    if ($userLoss < 0) {
                        $userLoss = abs($userLoss);
                        // user commission
                        $ucd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $uid");
                        $ubal = $ucd->c - $ucd->d;
                        $userCommission = $userLoss * $user->odd_commission / 100;
                        $ucommissiondcdata = array(
                            'txnid'             => md5(microtime()),
                            'user_id'           => $uid,
                            'credits'           => $userCommission,
                            'credited_from'     => $mid,
                            'balance'           => $ubal + $userCommission,
                            'assigned_by'       => $mid,
                            'transaction_date'  => date('Y-m-d H:i:s'),
                            'description'       => "Commission from " . $match->event_name,
                            'type'              => 'bet',
                            'commission'        => 'yes',
                            'match_id'          => $match_id,
                            'set_bal'           => $userLoss * $user->odd_commission / 100,
                            'parent_bal'        => -($userLoss * $user->odd_commission / 100),
                            'updated_at'        => date('Y-m-d H:i:s')
                        );
                        $this->Crud_model->insert_record('credits_debits', $ucommissiondcdata);
                        // Master Commission
                        $mcd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $mid");
                        $mbal = $mcd->c - $mcd->d;
                        $masterCommission = $userLoss * $master->odd_commission / 100;
                        $mcommissiondcdata = array(
                            'txnid'             => md5(microtime()),
                            'user_id'           => $mid,
                            'credits'           => ($masterCommission* ($smc + $mc +$ac) / 100),
                            'credited_from'     => $smid,
                            'debits'            => $userCommission,
                            'debited_to'        => $uid,
                            'balance'           => $mbal + ($masterCommission* ($smc + $mc + $ac) / 100) - $userCommission,
                            'assigned_by'       => $smid,
                            'transaction_date'  => date('Y-m-d H:i:s'),
                            'description'       => "Commission from " . $match->event_name,
                            'type'              => 'bet',
                            'commission'        => 'yes',
                            'match_id'          => $match_id,
                            'set_bal'           => ($masterCommission* ($smc + $mc +$ac) / 100)  - $userCommission,
                            'parent_bal'        => -($masterCommission* ($smc + $mc + $ac) / 100),
                            'updated_at'        => date('Y-m-d H:i:s')
                        );
                        $this->Crud_model->insert_record('credits_debits', $mcommissiondcdata);
                        // Supermaster Commission
                        $smcd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $smid");
                        $smbal = $smcd->c - $smcd->d;
                        $smcommissiondcdata = array(
                            'txnid'             => md5(microtime()),
                            'user_id'           => $smid,
                            'credits'           => $masterCommission * ($smc + $ac) / 100,
                            'credited_from'     => $aid,
                            'debits'            => ($masterCommission* ($smc + $mc +$ac) / 100),
                            'debited_to'        => $mid,
                            'balance'           => $smbal + ($masterCommission * ($smc + $ac) / 100) - ($masterCommission* ($smc + $mc + $ac) / 100),
                            'assigned_by'       => $aid,
                            'transaction_date'  => date('Y-m-d H:i:s'),
                            'description'       => "Commission from " . $match->event_name,
                            'type'              => 'bet',
                            'commission'        => 'yes',
                            'match_id'          => $match_id,
                            'set_bal'           => ($masterCommission * ($smc + $ac) / 100)  - ($masterCommission* ($smc + $mc + $ac) / 100),
                            'parent_bal'        => -($masterCommission * ($smc + $ac) / 100),
                            'updated_at'        => date('Y-m-d H:i:s')
                        );
                        $this->Crud_model->insert_record('credits_debits', $smcommissiondcdata);
                        // admin Commission
                        $acd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $aid");
                        $abal = $acd->c - $acd->d;
                        $acommissiondcdata = array(
                            'txnid'             => md5(microtime()),
                            'user_id'           => $aid,
                            'credits'           => $masterCommission * ($ac) / 100,
                            'credited_from'     => $said,
                            'debits'            => $masterCommission * ($smc + $ac) / 100,
                            'debited_to'        => $smid,
                            'balance'           => $abal + $masterCommission * ($ac) / 100 - $masterCommission * ($smc + $ac) / 100,
                            'assigned_by'       => $said,
                            'transaction_date'  => date('Y-m-d H:i:s'),
                            'description'       => "Commission from " . $match->event_name,
                            'type'              => 'bet',
                            'commission'        => 'yes',
                            'match_id'          => $match_id,
                            'set_bal'           => $masterCommission * ($ac) / 100 - $masterCommission * ($smc + $ac) / 100,
                            'parent_bal'        => -$masterCommission * ($ac) / 100,
                            'updated_at'        => date('Y-m-d H:i:s')
                        );
                        $this->Crud_model->insert_record('credits_debits', $acommissiondcdata);
                        // superadmin commission
                        $sacd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $said");
                        $sabal = $sacd->c - $sacd->d;
                        $sacommissiondcdata = array(
                            'txnid'             => md5(microtime()),
                            'user_id'           => $aid,
                            'debits'            => $masterCommission * ($ac) / 100,
                            'debited_to'        => $aid,
                            'balance'           => $sabal - $masterCommission * ($ac) / 100,
                            'assigned_by'       => 0,
                            'transaction_date'  => date('Y-m-d H:i:s'),
                            'description'       => "Commission from " . $match->event_name,
                            'type'              => 'bet',
                            'commission'        => 'yes',
                            'match_id'          => $match_id,
                            'set_bal'           => -$masterCommission * ($ac) / 100,
                            'parent_bal'        => 0,
                            'updated_at'        => date('Y-m-d H:i:s')
                        );
                        $this->Crud_model->insert_record('credits_debits', $sacommissiondcdata);
                    }
                }
            }
        }
    }

    public function betSettlementMatched($bid, $mid)
    {
        $bet = $this->Common_model->get_single_query("select * from bet where id = $bid");
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $winner = $match->winner_id;
        if ($bet->back_lay == 'back') {
            if ($winner == $bet->team_id) {
                $result = 'win';
            } else {
                $result = 'loose';
            }
        } else {
            if ($winner == $bet->team_id) {
                $result = 'loose';
            } else {
                $result = 'win';
            }
        }
        $this->profitLossCalculation($bid, $mid, $result);
    }

    public function profitLossCalculation($bid, $mid, $result)
    {
        $bet = $this->Common_model->get_single_query("select * from bet where id = $bid");
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $uid = $bet->user_id;
        $user = $this->Common_model->get_single_query("select * from users where id = $uid");
        $mid = $user->parent_id; //master id
        $master = $this->Common_model->get_single_query("select * from users where id = $mid");
        $mc = $master->commission; //master partnershipt to super master
        if (empty($mc)) $mc = 0;
        $smid = $master->parent_id; //supermaster id
        $supermaster = $this->Common_model->get_single_query("select * from users where id = $smid");
        $smc = $supermaster->commission; //supermaster partnership to admin
        if (empty($smc)) $smc = 0;
        $aid = $this->Common_model->findfield('users', 'id', $smid, 'parent_id'); //admin id
        $admin = $this->Common_model->get_single_query("select * from users where id = $aid");
        $ac = $supermaster->commission; //admin partnership to superadmin
        if (empty($ac)) $ac = 0;
        $said = $this->Common_model->findfield('users', 'id', $aid, 'parent_id'); //superadmin id
        if ($result == 'win') {
            $p_l = 'profit';
            $userProfit = $bet->profit;
            $userLoss = 0;
            $userCredit = $userProfit;
            $userDebit = 0;
            $masterProfit = 0;
            $masterLoss = $userProfit;
            $masterDebit = $userProfit;
            $masterCredit = $userProfit * ($smc + $mc + $ac) / 100;
            $supermasterLoss = $masterCredit;
            $supermasterDebit = $masterCredit;
            $supermasterCredit = $userProfit * ($smc + $ac) / 100;
            $adminLoss = $supermasterCredit;
            $adminDebit = $supermasterCredit;
            $adminCredit = $userProfit * ($ac) / 100;
            $superAdminDebit = $adminCredit;
            $superAdminCredit = 0;
            //profitloss
            $mpl = $masterDebit - $masterCredit;
            $smpl = $supermasterDebit - $supermasterCredit;
            $apl = $adminDebit - $adminCredit;
            $sapl = $superAdminDebit - $superAdminCredit;
        } else {
            $p_l = 'loss';
            $userProfit = 0;
            $userLoss = $bet->loss;
            $userCredit = 0;
            $userDebit = $userLoss;
            $masterProfit = $userDebit;
            $masterLoss = 0;
            $masterCredit = $userDebit;
            $masterDebit = $userLoss * ($smc + $mc + $ac) / 100;
            $supermasterProfit = $masterDebit;
            $supermasterCredit = $masterDebit;
            $supermasterDebit = $userLoss * ($smc + $ac) / 100;
            $adminProfit = $supermasterDebit;
            $adminCredit = $supermasterDebit;
            $adminDebit = $userLoss * ($ac) / 100;
            $superAdminDebit = 0;
            $superAdminCredit = $adminDebit;
            //profitloss
            $mpl = $masterCredit - $masterDebit;
            $smpl = $supermasterCredit - $supermasterDebit;
            $apl = $adminCredit - $adminDebit;
            $sapl = $superAdminCredit - $superAdminDebit;
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
        $this->Crud_model->insert_record('profit_loss', $pldata);
        //update betting record
        $bdata = array(
            'status'        => 'settled',
            'bet_result'    => $result,
            'profit'        => $userProfit,
            'loss'          => $userLoss,
            'updated_at'    => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record('bet', $bet->id, $bdata);
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
            'set_bal'           => $userProfit - $userLoss,
            'parent_bal'        => $userProfit > 0 ? -$userProfit : $userLoss,
            'updated_at'        => date('Y-m-d H:i:s')
        );

        $this->Crud_model->insert_record('credits_debits', $udcdata);
        //echo '<hr/>'.$this->db->last_query();
        // $userChips = $this->Common_model->get_single_query("select * from user_chips where user_id = $uid");

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
            'set_bal'           => $masterCredit - $masterDebit,
            'parent_bal'        => $userProfit > 0 ? $masterCredit : -$masterDebit,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $mdcdata);
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
            'balance'           => $smbal + $supermasterCredit - $supermasterDebit,
            'assigned_by'       => $aid,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'set_bal'           => $supermasterCredit - $supermasterDebit,
            'parent_bal'        => $userProfit > 0 ? $supermasterCredit : -$supermasterDebit,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $smdcdata);
        //admin debit credit
        $acd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $aid");
        $abal = $acd->c - $acd->d;
        $adcdata = array(
            'txnid'             => md5(microtime()),
            'user_id'           => $aid,
            'credits'           => $adminCredit,
            'credited_from'     => $userProfit > 0 ? $said : $smid,
            'debits'            => $adminDebit,
            'debited_to'        => $userProfit > 0 ? $smid : $said,
            'balance'           => $abal + $adminCredit - $adminDebit,
            'assigned_by'       => $said,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'set_bal'           => $adminCredit - $adminDebit,
            'parent_bal'        => $userProfit > 0 ? $adminCredit : -$adminDebit,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $adcdata);

        //superadmin debit credit
        $sacd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $said");
        $sabal = $sacd->c - $sacd->d;
        $sadcdata = array(
            'txnid'             => md5(microtime()),
            'user_id'           => $said,
            'credits'           => $superAdminCredit,
            'credited_from'     => $userProfit > 0 ? 0 : $smid,
            'debits'            => $superAdminDebit,
            'debited_to'        => $userProfit > 0 ? $smid : 0,
            'balance'           => $sabal + $adminCredit - $adminDebit,
            'assigned_by'       => 0,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'set_bal'           => $superAdminCredit - $superAdminDebit,
            'parent_bal'        => $userProfit > 0 ? $superAdminCredit : -$superAdminDebit,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $sadcdata);
    }

    public function betSettlementUnMatched($bid, $mid)
    {
        $bet = $this->Common_model->get_single_query("select * from bet where id = $bid");
        //update betting record
        $bdata = array(
            'status'        => 'settled',
            'bet_result'    => 'unmatched',
            'profit'        => 0,
            'loss'          => 0,
            'user_commission' => 0,
            'master_commission' => 0,
            'updated_at'    => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record('bet', $bet->id, $bdata);
        $stake = $bet->stake;
        $uid = $bet->user_id;
        $userChips = $this->Common_model->get_single_query("select * from user_chips where user_id=$uid");
        $uchipdata = array(
            'balanced_chips' => $userChips->balanced_chips + $bet->stake,
            'current_chips'  => $userChips->current_chips + $bet->stake,
            'updated_at'     => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips', $uid, $uchipdata, 'user_id');
    }

    public function betSettlementFancy($bid, $mid)
    {
        $bet = $this->Common_model->get_single_query("select * from bet where id = $bid");
        $fancy = $this->Common_model->get_single_query("select * from fancy_data where market_id = '$mid' and fancy_name = '$bet->team'");
        $winner = $fancy->line;
        if ($bet->back_lay == 'back') {
            if ($winner >= $bet->odd) {
                $result = 'win';
            } else {
                $result = 'loose';
            }
        } else {
            if ($winner >= $bet->odd) {
                $result = 'loose';
            } else {
                $result = 'win';
            }
        }
        $this->profitLossFancyCalculation($bid, $mid, $result);
        //$this->Crud_model->delete_record('fancy_data',$fancy->id);
    }

    public function profitLossFancyCalculation($bid, $mid, $result)
    {
        $bet = $this->Common_model->get_single_query("select * from bet where id = $bid");
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $uid = $bet->user_id;
        $user = $this->Common_model->get_single_query("select * from users where id = $uid");
        $mid = $user->parent_id; //master id
        $master = $this->Common_model->get_single_query("select * from users where id = $mid");
        $mc = $master->commission; //master partnershipt to super master
        if (empty($mc)) $mc = 0;
        $smid = $master->parent_id; //supermaster id
        $supermaster = $this->Common_model->get_single_query("select * from users where id = $smid");
        $smc = $supermaster->commission; //supermaster partnership to admin
        if (empty($smc)) $smc = 0;
        $aid = $this->Common_model->findfield('users', 'id', $smid, 'parent_id'); //admin id
        $admin = $this->Common_model->get_single_query("select * from users where id = $aid");
        $ac = $supermaster->commission; //admin partnership to superadmin
        if (empty($ac)) $ac = 0;
        $said = $this->Common_model->findfield('users', 'id', $aid, 'parent_id'); //superadmin id
        if ($result == 'win') {
            $p_l = 'profit';
            $userProfit = $bet->profit;
            $userLoss = 0;
            $userCredit = $userProfit;
            $userDebit = 0;
            $masterProfit = 0;
            $masterLoss = $userProfit;
            $masterDebit = $userProfit;
            $masterCredit = $userProfit * ($smc + $mc + $ac) / 100;
            $supermasterLoss = $masterCredit;
            $supermasterDebit = $masterCredit;
            $supermasterCredit = $userProfit * ($smc + $ac) / 100;
            $adminLoss = $supermasterCredit;
            $adminDebit = $supermasterCredit;
            $adminCredit = $userProfit * ($ac) / 100;
            $superAdminDebit = $adminCredit;
            $superAdminCredit = 0;
            //profitloss
            $mpl = $masterDebit - $masterCredit;
            $smpl = $supermasterDebit - $supermasterCredit;
            $apl = $adminDebit - $adminCredit;
            $sapl = $superAdminDebit - $superAdminCredit;
        } else {
            $p_l = 'loss';
            $userProfit = 0;
            $userLoss = $bet->loss;
            $userCredit = 0;
            $userDebit = $userLoss;
            $masterProfit = $userDebit;
            $masterLoss = 0;
            $masterCredit = $userDebit;
            $masterDebit = $userLoss * ($smc + $mc + $ac) / 100;
            $supermasterProfit = $masterDebit;
            $supermasterCredit = $masterDebit;
            $supermasterDebit = $userLoss * ($smc + $ac) / 100;
            $adminProfit = $supermasterDebit;
            $adminCredit = $supermasterDebit;
            $adminDebit = $userLoss * ($ac) / 100;
            $superAdminDebit = 0;
            $superAdminCredit = $adminDebit;
            //profitloss
            $mpl = $masterCredit - $masterDebit;
            $smpl = $supermasterCredit - $supermasterDebit;
            $apl = $adminCredit - $adminDebit;
            $sapl = $superAdminCredit - $superAdminDebit;
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
        $this->Crud_model->insert_record('profit_loss', $pldata);
        //update betting record
        $bdata = array(
            'status'        => 'settled',
            'bet_result'    => $result,
            'profit'        => $userProfit,
            'loss'          => $userLoss,
            'updated_at'    => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record('bet', $bet->id, $bdata);
        //echo '<hr/>'.$this->db->last_query();
        //user credit debit
        $ucd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $uid");
        $ubal = $ucd->c - $ucd->d;
        $ufinalBal = $ubal + $userProfit - $userLoss;
        $udcdata = array(
            'txnid'             => md5(microtime()),
            'user_id'           => $bet->user_id,
            'credits'           => $userProfit + $bet->user_commission,
            'credited_from'     => $userProfit > 0 ? $mid : 0,
            'debits'            => $userLoss,
            'debited_to'        => $userLoss > 0 ? $mid : 0,
            'balance'           => $ubal + $userProfit - $userLoss + $bet->user_commission,
            'assigned_by'       => $mid,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'set_bal'           => $userProfit + $bet->user_commission - $userLoss,
            'parent_bal'        => ($userProfit > 0 ? -$userProfit : $userLoss) - $bet->user_commission,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $udcdata);
        //master debit credit
        $mcd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $mid");
        $mbal = $mcd->c - $mcd->d;
        $mfinalBal = $mbal + $masterCredit - $masterDebit;
        $mupCommission = $bet->master_commission*($smc + $mc + $ac)/100;
        $mdownCommission = $bet->user_commission;
        $mpbal = $userProfit > 0 ? $masterCredit : -$masterDebit;

        $mdcdata = array(
            'txnid'             => md5(microtime()),
            'user_id'           => $mid,
            'credits'           => $masterCredit,
            'credited_from'     => $userProfit > 0 ? $smid : $uid,
            'debits'            => $masterDebit,
            'debited_to'        => $userProfit > 0 ? $uid : $smid,
            'balance'           => $mfinalBal + $mupCommission - $mdownCommission,
            'assigned_by'       => $smid,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'set_bal'           => $masterCredit + $mupCommission - $masterDebit - $mdownCommission,
            'parent_bal'        => $mpbal + $mupCommission,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $mdcdata);
        // master commission data
        // if ($master->session_commission > 0 || $user->session_commission > 0) {
        //     $mcommissiondcdata = array(
        //         'txnid'             => md5(microtime()),
        //         'user_id'           => $mid,
        //         'credits'           => $bet->master_commission*($smc + $mc)/100,
        //         'credited_from'     => $smid,
        //         'debits'            => $bet->user_commission,
        //         'debited_to'        => $uid,
        //         'balance'           => $mfinalBal + $bet->master_commission*($smc + $mc)/100 - $bet->user_commission,
        //         'assigned_by'       => $smid,
        //         'transaction_date'  => $bet->created_at,
        //         'description'       => "Commission from " . $bet->match_name,
        //         'type'              => 'bet',
        //         'commission'        => 'yes',
        //         'bet_id'            => $bet->id,
        //         'match_id'          => $bet->match_id,
        //         'set_bal'           => $bet->master_commission*($smc + $mc)/100 - $bet->user_commission,
        //         'parent_bal'        => ($bet->master_commission*($smc + $mc)/100 ),
        //         'updated_at'        => date('Y-m-d H:i:s')
        //     );
        //     $this->Crud_model->insert_record('credits_debits', $mcommissiondcdata);
        // }
        //supermaster debit credit
        $smcd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $smid");
        $smbal = $smcd->c - $smcd->d;
        $smfinalbal = $smbal + $supermasterCredit - $supermasterDebit;
        $smupCommission = $bet->master_commission*($smc + $ac)/100;
        $smdownCommission = $bet->master_commission*($smc + $mc + $ac)/100;
        $smpbal = $userProfit > 0 ? $supermasterCredit : -$supermasterDebit;
        $smdcdata = array(
            'txnid'             => md5(microtime()),
            'user_id'           => $smid,
            'credits'           => $supermasterCredit,
            'credited_from'     => $userProfit > 0 ? $aid : $mid,
            'debits'            => $supermasterDebit,
            'debited_to'        => $userProfit > 0 ? $mid : $aid,
            'balance'           => $smfinalbal + $smupCommission - $smdownCommission,
            'assigned_by'       => $aid,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'set_bal'           => $supermasterCredit + $smupCommission - $supermasterDebit - $smdownCommission,
            'parent_bal'        => $smpbal + $smupCommission,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $smdcdata);
        // supermaster commission
        // if ($master->session_commission > 0 || $user->session_commission > 0) {
        //     $smcommissiondcdata = array(
        //         'txnid'             => md5(microtime()),
        //         'user_id'           => $smid,
        //         'credits'           => $bet->master_commission * $smc / 100,
        //         'credited_from'     => $aid,
        //         'debits'            => $bet->master_commission*($smc + $mc)/100,
        //         'debited_to'        => $mid,
        //         'balance'           => $smfinalbal + ($bet->master_commission * $smc / 100) - ($bet->master_commission*($smc + $mc)/100),
        //         'assigned_by'       => $aid,
        //         'transaction_date'  => $bet->created_at,
        //         'description'       => "Commission from " . $bet->match_name,
        //         'type'              => 'bet',
        //         'commission'        => 'yes',
        //         'bet_id'            => $bet->id,
        //         'match_id'          => $bet->match_id,
        //         'set_bal'           => ($bet->master_commission * $smc / 100)  - ($bet->master_commission*($smc + $mc)/100),
        //         'parent_bal'        => ($bet->master_commission * $smc / 100),
        //         'updated_at'        => date('Y-m-d H:i:s')
        //     );
        //     $this->Crud_model->insert_record('credits_debits', $smcommissiondcdata);
        // }
        //admin debit credit
        $acd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $aid");
        $abal = $acd->c - $acd->d;
        $afinalbal = $abal + $adminCredit - $adminDebit;
        $aupCommission = $bet->master_commission*($ac)/100;
        $adownCommission = $bet->master_commission*($smc + $ac)/100;
        $apbal = $userProfit > 0 ? $adminCredit : -$adminDebit;
        $adcdata = array(
            'txnid'             => md5(microtime()),
            'user_id'           => $aid,
            'credits'           => $adminCredit,
            'credited_from'     => $userProfit > 0 ? $said : $smid,
            'debits'            => $adminDebit,
            'debited_to'        => $userProfit > 0 ? $smid : $said,
            'balance'           => $afinalbal + $aupCommission - $adownCommission,
            'assigned_by'       => $said,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'set_bal'           => $adminCredit + $aupCommission - $adminDebit - $adownCommission,
            'parent_bal'        => $apbal + $aupCommission,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $adcdata);
        // admin commission
        // if ($master->session_commission > 0 || $user->session_commission > 0) {
        //     $acommissiondcdata = array(
        //         'txnid'             => md5(microtime()),
        //         'user_id'           => $aid,
        //         'debits'            => $bet->master_commission * $smc / 100,
        //         'debited_to'        => $smid,
        //         'balance'           => $afinalbal - ($bet->master_commission * $smc / 100),
        //         'assigned_by'       => 8,
        //         'transaction_date'  => $bet->created_at,
        //         'description'       => "Commission from " . $bet->match_name,
        //         'type'              => 'bet',
        //         'commission'        => 'yes',
        //         'bet_id'            => $bet->id,
        //         'match_id'          => $bet->match_id,
        //         'set_bal'           => -($bet->master_commission * $smc / 100),
        //         'parent_bal'        => ($bet->master_commission * $smc / 100),
        //         'updated_at'        => date('Y-m-d H:i:s')
        //     );
        //     $this->Crud_model->insert_record('credits_debits', $acommissiondcdata);
        // }
        // Superadmin debit credit
        $sacd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $said");
        $sabal = $sacd->c - $sacd->d;
        $safinalbal = $sabal + $superAdminCredit - $superAdminDebit;
        $sadownCommission = $bet->master_commission*($ac)/100;
        $sapbal = $userProfit > 0 ? $adminCredit : -$adminDebit;
        $sadcdata = array(
            'txnid'             => md5(microtime()),
            'user_id'           => $said,
            'credits'           => $superAdminCredit,
            'credited_from'     => $userProfit > 0 ? 0 : $aid,
            'debits'            => $superAdminDebit,
            'debited_to'        => $userProfit > 0 ? $aid : 0,
            'balance'           => $safinalbal - $sadownCommission,
            'assigned_by'       => 0,
            'transaction_date'  => $bet->created_at,
            'description'       => $bet->match_name,
            'type'              => 'bet',
            'bet_id'            => $bet->id,
            'match_id'          => $bet->match_id,
            'set_bal'           => $superAdminCredit - $superAdminDebit - $sadownCommission,
            'parent_bal'        => $sapbal,
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $sadcdata);
    }

    public function updateBalForUsers()
    {
        $users = $this->Common_model->get_data_by_query("SELECT id FROM users_with_groups WHERE group_id =5");
        foreach ($users as $key => $u) {
            $this->calFinalB($u['id']);
        }
    }

    public function updateBalForUsersByMarket($mkid,$fancyName = null) {
        if($fancyName != null) {
            $users = $this->Common_model->get_data_by_query("SELECT DISTINCT(user_id) FROM bet WHERE market_id = '$mkid' AND team = '$fancyName' ");
        } else {
            $users = $this->Common_model->get_data_by_query("SELECT DISTINCT(user_id) FROM bet WHERE market_id = '$mkid' AND bet_type != 'fancy' ");
        }
        foreach ($users as $key => $u) {
            $this->calFinalB($u['user_id']);
        }
    }

    public function updateDCForUsers()
    {
        $users = $this->Common_model->get_data_by_query("SELECT id FROM users_with_groups WHERE group_id =5");
        foreach ($users as $key => $u) {
            $this->updateDCBal($u['id']);
        }
    }

    public function updateBalForAll()
    {
        $users = $this->Common_model->get_data_by_query("SELECT id FROM users_with_groups WHERE group_id !=5");
        foreach ($users as $key => $u) {
            $this->updateDCBal($u['id']);
            // $this->updateBal($u['id']);
            //$this->calFinalB($u['id']);
        }
    }

    public function updateDCBal($uid)
    {
        $dc = $this->Common_model->get_data_by_query("SELECT * FROM credits_debits where user_id = $uid AND balance_calculated = 'no' order by id ASC");
        $balance = $this->settlement->calculateCreditDebitBalanceByUserId($uid);
        $plus = $balance['initial'];
        $minus = 0;
        foreach ($dc as $d) {
            $plus += $d['credits'] - $d['debits'];
            $data = array('balance' => $plus);
            $this->Crud_model->edit_record('credits_debits', $d['id'], $data);
        }
        $unchipdata = array(
            'balanced_chips' => $balance['final'],
            'current_chips'  => $balance['final'],
            'updated_at'     => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips', $uid, $unchipdata, 'user_id');
        // print_r($this->db->last_query()."<hr/>");
        //return TRUE;
    }


    public function updateBal($uid)
    {
        $ucd = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $uid");
        $unchipdata = array(
            'balanced_chips' => $ucd->c - $ucd->d,
            'current_chips'  => $ucd->c - $ucd->d,
            'updated_at'     => date('Y-m-d H:i:s')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips', $uid, $unchipdata, 'user_id');
    }

    public function calFinalB($uid)
    {
        $fancyFinal = 0;
        $tt1w = 0;
        $tt2w = 0;
        $tt3w = 0;
        $smt = 0;
        $untotal = 0;
        $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $uid AND bet_type = 'fancy' and status='pending'");
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
        $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $uid AND bet_type = 'matched' and status='pending'");
        foreach ($oids as $ok => $of) {
            $okids[] = $of['market_id'];
        }
        $onkids = array_unique($okids);
        foreach ($onkids as $onk => $ov) {
            $ateam = $this->Common_model->get_single_query("select * from cron_data where market_id = '$ov'");
            $teams = json_decode($ateam->teams);
            $team1 = $teams[0]->id;
            $team2 = $teams[1]->id;
            $team3 = count($teams) > 2 ? $teams[2]->id : 0;
            $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
            $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
            $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
            $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
            if ($team3 > 0) {
                $team3backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'back' AND bet_type = 'matched'");
                $team3layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'lay' AND bet_type = 'matched'");
            }
            $team1win = 0;
            $team2win = 0;
            $team3win = 0;
            $team1win = ($team1backprofitloss->p + $team2layprofitloss->p + $team3layprofitloss->p) - ($team1layprofitloss->l + $team2backprofitloss->l + $team3backprofitloss->l);
            $team2win = ($team2backprofitloss->p + $team1layprofitloss->p + $team3layprofitloss->p) - ($team2layprofitloss->l + $team1backprofitloss->l + $team3backprofitloss->l);
            if ($team3 > 0) {
                $team3win = ($team3backprofitloss->p + $team2layprofitloss->p + $team1layprofitloss->p) - ($team3layprofitloss->l + $team2backprofitloss->l + $team1backprofitloss->l);
            }
            $smallest = min($team1win, $team2win, $team3win);
            if ($smallest < 0) {
                $smt += abs($smallest);
            }
        }
        //unmatched part
        $unmids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $uid AND bet_type = 'unmatched' AND status='pending'");
        if (empty($unmids)) {
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
        $bchips = $ubalance - ($smt + $fancyFinal + $untotal);
        $cchips = $ubalance - $untotal;
        $bcfdata = array(
            'balanced_chips' => $bchips,
            'current_chips' => $cchips,
            'updated_at' => date('Y-m-d H:i:sa')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips', $uid, $bcfdata, 'user_id');
        //return $bchips;
    }

    public function creditDebitBalance()
    {
        $users = $this->Common_model->get_data_by_query("SELECT id FROM users_with_groups WHERE group_id !=5");
        foreach ($users as $key => $u) {
            $this->settlement->calculateAllCreditDebitBalanceByUserId($u['id']);
        }
    }

    public function panelTitle()
    {
        $title = $this->input->get('title');
        if (empty($title)) {
            $title = 'Welcome to betcric';
        }
        if (isset($_GET) && !empty($_GET)) {
            $message = array(
                'title'     => $title,
                'status'    => 'active'
            );
            $r = $this->Common_model->get_single_query("SELECT * FROM panel_title ORDER BY id DESC");
            if ($r) {
                $this->Crud_model->edit_record('panel_title', $r->id, $message);
            } else {
                $this->Crud_model->insert_record('panel_title', $message);
            }
            $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Panel title updated successfully</div>");
        }
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $data['panel'] = $this->Common_model->get_single_query("SELECT * FROM panel_title");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/panel_title', $data);
        $this->load->view('layout/backend_footer');
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
        if ($csrfkey && $csrfkey == $this->session->flashdata('csrfvalue')) {
            return TRUE;
        } else {
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

    function json_output($statusHeader, $response)
    {
        $ci = &get_instance();
        $ci->output->set_content_type('application/json');
        $ci->output->set_status_header($statusHeader);
        $ci->output->set_output(json_encode($response));
    }


    function series(){
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $series = $this->Common_model->get_data_by_query("SELECT * FROM series");
        $data['series'] = $series;
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/series', $data);
        $this->load->view('layout/backend_footer');
    }
    function addSeries()
    {
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('duration', 'Duration', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');
        if ($this->form_validation->run() == true) {
            
            $sdata = array(
                'name' => $this->input->post('name'),
                'duration' => $this->input->post('duration'),
                'status' => $this->input->post('status')
            );
            
            $result = $this->Crud_model->insert_record('series', $sdata);
            if($result){
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('SuperAdmin/series', 'refresh');
            }
        } else {
            $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            redirect('SuperAdmin/series', 'refresh');
        }
    }

    function editSeries($id, $name){
        $hdata['title'] = 'SuperAdmin Panel | SetBat';
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $data['series'] = $this->Common_model->get_single_query("SELECT * FROM series where id = $id");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('superadmin/editseries', $data);
        $this->load->view('layout/backend_footer');
    }

    function updateSeries($id){
        $data = array(
            'name' => $this->input->post('name'),
            'duration' => $this->input->post('duration'),
            'status' => $this->input->post('status')
        );
        $this->db->where('id', $id)->update('series', $data);

        
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect("SuperAdmin/editSeries/$id/".$this->input->post('name'), 'refresh');
    }
}