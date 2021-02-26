<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SuperMaster extends MY_Controller
{

    public function __construct()
    {
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
        if (!$this->ion_auth->is_supermaster()) {
            redirect('Auth');
        }
        $this->id = $this->session->userdata('user_id');
        $this->chipSetting = $this->Common_model->get_single_query("select * from chip_setting where user_id = $this->id");
        $this->chips = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
        $this->p_l = $this->Common_model->get_single_query("select sum('credits') as c, sum(debits) as d from credits_debits where user_id = $this->id and type='bet'");
        $this->panel = $this->Common_model->get_single_query("SELECT * FROM panel_title ORDER BY id DESC");
        $cuser = $this->Common_model->get_single_query("select * from users where id = $this->id");
        $this->showMatch = $cuser->show_match;
    }

    public function index()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        if ($this->showMatch == 'yes') {
            $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
            // foreach ($matches as $mkey => $m) {
            //     $odds = $this->match->matchOddByMarketId($m['market_id']);
            //     $matches[$mkey]['odds'] = $odds;
            // }
            $data['matches'] = $matches;
        } else $data = array();
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/index', $data);
        $this->load->view('layout/backend_footer');
    }

    public function changePassword()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/change_password');
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
            redirect('SuperMaster/changePassword');
        } else {
            $identity = $this->session->userdata('identity');

            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

            if ($change) {
                //if the password was successfully changed
                $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" . $this->ion_auth->messages() . "</div>");
                redirect('Auth/logout');
            } else {
                $this->session->set_flashdata('message', "<div class='alert alert-error alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" . $this->ion_auth->errors() . "</div>");
                redirect('SuperMaster/changePassword', 'refresh');
            }
        }
    }

    public function masters()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $users = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'master' and parent_id = $this->id and deleted = 'no' order by id DESC"));
        foreach ($users as $key => $user) {
            $users[$key]->blocked_event = $this->Common_model->get_data_by_query("SELECT *  FROM `event_block` where user_id = $user->id order by id asc");
        }
        
        $data['users'] = $users;
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM cron_data where event_id NOT IN (SELECT event_id FROM running_matches)");
        $data['matches'] = $matches;
        $fdata['matches'] = $matches;
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'master'");
        $data['chips'] = $this->chips;
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/masters', $data);
        $this->load->view('layout/backend_footer');
    }

    public function users()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $mid = $this->input->get('master_id');
        if(isset($mid)) {
            $users = $this->_outlist($this->Common_model->get_data_by_query("select * from users_with_groups where group_name = 'user' and parent_id = $mid and deleted = 'no' order by id DESC"));
        } else {
            $users = $this->_outlist($this->Common_model->get_data_by_query("SELECT * FROM `users_with_groups` WHERE group_name = 'user' AND parent_id IN (SELECT id FROM users WHERE parent_id = $this->id) AND deleted = 'no' ORDER BY id DESC"));
        }
        
        foreach ($users as $key => $user) {
            $users[$key]->blocked_event = $this->Common_model->get_data_by_query("SELECT *  FROM `event_block` where user_id = $user->id order by id asc");
        }
        
        $data['users'] = $users;
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM cron_data where event_id NOT IN (SELECT event_id FROM running_matches)");
        $data['matches'] = $matches;
        $fdata['matches'] = $matches;
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name = 'master'");
        $data['chips'] = $this->chips;
        $this->load->view('layout/backend_header', $hdata);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/users', $data);
        $this->load->view('layout/backend_footer');
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
            redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'), 'refresh');
        }
        $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'), 'refresh');
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
                redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'), 'refresh');
            } else {
                $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'), 'refresh');
            }
        } else {
            $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            redirect('SuperMaster/masters');
        }
    }

    public function addUser()
    {
        $tables = $this->config->item('tables', 'ion_auth');
        $identity_column = $this->config->item('identity', 'ion_auth');
        $this->form_validation->set_rules('identity', 'Username', 'required|is_unique[' . $tables['users'] . '.username]');
        if ($this->form_validation->run() == true) {
            $email    = $this->input->post('identity');
            $identity = $this->input->post('identity');
            $password = 'set123';
            $additional_data = array(
                'full_name'  => $this->input->post('full_name'),
                'parent_id'  => $this->id,
                'commission' => $this->input->post('commission')
            );
            $groups = array(
                'id' => 4
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
                redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'), 'refresh');
            } else {
                $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                redirect('SuperMaster/masters', 'refresh');
            }
        } else {
            $this->session->set_flashdata('message', validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            redirect('SuperMaster/masters', 'refresh');
        }
    }

    public function editUser()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $data['user'] = $this->Common_model->get_single_query("select * from users_with_groups where id = $id");
        $data['groups'] = $this->Common_model->get_data_by_query("select * from groups where name='user'");
        $data['csrf'] = $this->_get_csrf_nonce();
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/edit_user', $data);
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
                redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
            } else {
                // redirect them back to the admin page if admin, or to the base url if non admin
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
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
            redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
        } else {
            $this->ion_auth->activate($id);
            $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>User Activated successfully</div>");
            redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
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
        redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
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
        redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
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
        redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
    }

    public function addMoney()
    {
        $uid = $this->input->post('user_id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $uid, 'group_name');
        $user = $this->Common_model->get_single_query("select * from users_with_groups where id = $uid");
        $chips = $this->input->post('chips');
        if ($chips <= 0) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Please add some Chips first</div>");
            redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
        }
        $pid = $user->parent_id;
        $pcreditdebit = $this->Common_model->get_single_query("select sum(credits) as c, sum(debits) as d from credits_debits where user_id = $pid");
        $parentChips = $pcreditdebit->c - $pcreditdebit->d;
        if ($parentChips <= 0) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Please add some Chips to parent first</div>");
            redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
        }
        if ($chips > $parentChips) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Chips can not be more than parent balanced chips</div>");
            redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
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

        $mdata = array(
            'balanced_chips'    => $mchips->balanced_chips - $chips,
            'current_chips'     => $mchips->current_chips - $chips,
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
            'balance'           => $mcbalance,
            'description'       => 'Transferred to ' . $user->username,
            'transferred_to'    => $uid,
            'transaction_date'  => date('Y-m-d H:i:s'),
            'type'              => 'debit',
            'settled'           => 'yes',
            'updated_at'        => date('Y-m-d H:i:s')
        );
        $this->Crud_model->insert_record('credits_debits', $mcdata);
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Credit Record Added</div>");
        redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
    }

    public function witdrawChips()
    {
        $uid = $this->input->post('user_id');
        $chips = $this->input->post('chips');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $uid, 'group_name');
        $maxChips = $this->Common_model->findfield('user_chips', 'user_id', $uid, 'balanced_chips');
        if ($chips > $maxChips) {
            $this->session->set_flashdata('message', "<div class='alert alert-danger alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>WIthdraw Chips can not be more than balanced chips</div>");
            redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
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
        redirect('SuperMaster/' . ($ug == 'user' ? 'users' : 'masters'));
    }

    public function userBetHistory()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
        if ($ug == 'supermaster') {
            $data['bets'] = $this->Common_model->get_data_by_query("SELECT * FROM `bet` WHERE user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $id)) ORDER BY id DESC");
        } elseif ($ug == 'master') {
            $data['bets'] = $this->Common_model->get_data_by_query("SELECT * FROM `bet` WHERE user_id IN (SELECT id FROM users WHERE parent_id = $id) ORDER BY id DESC");
        } else {
            $data['bets'] = $this->Common_model->get_data_by_query("select * from bet where user_id = $id order by id DESC");
        }
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/userbet_history', $data);
        $this->load->view('layout/backend_footer');
    }

    public function userAccountStatement()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
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
        $data['statements'] = array_reverse($statements);
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/useraccount_statement', $data);
        $this->load->view('layout/backend_footer');
    }

    public function statementByMatchId()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $match_id = $this->input->get('match_id');
        $uid = $this->input->get('user_id');
        $data['statements'] = $this->Common_model->get_data_by_query("SELECT s.*, b.id as bid, b.bet_type, b.user_commission, b.master_commission FROM credits_debits s LEFT JOIN bet b ON s.bet_id = b.id WHERE s.user_id = $uid AND s.match_id = $match_id ORDER BY b.bet_type");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/statementby_matchid', $data);
        $this->load->view('layout/backend_footer');
    }

    public function oddFancyByMatchId()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
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
        $this->load->view('supermaster/oddfancyby_matchid', $data);
        $this->load->view('layout/backend_footer');
    }

    public function userProfitLoss()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('user_id');
        $ug = $this->Common_model->findfield('users_with_groups', 'id', $id, 'group_name');
        $data['profitLosses'] = $this->Common_model->get_data_by_query("select * from profit_loss where user_id = $id");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/userprofit_loss', $data);
        $this->load->view('layout/backend_footer');
    }

    public function bet()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $id = $this->input->get('bet_id');
        $data['bet'] = $this->Common_model->get_single_query("select * from bet where id = $id");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/bet', $data);
        $this->load->view('layout/backend_footer');
    }

    public function accountInfo()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $data['up'] = $this->Common_model->get_single_query("SELECT SUM(credits) as up FROM `credits_debits` WHERE user_id = $this->id AND type='bet'");
        $data['down'] = $this->Common_model->get_single_query("SELECT SUM(debits) as down FROM `credits_debits` WHERE user_id = $this->id AND type='bet'");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/account_info', $data);
        $this->load->view('layout/backend_footer');
    }

    public function accountStatement()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
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
        $this->load->view('supermaster/account_statement', $data);
        $this->load->view('layout/backend_footer');
    }

    public function chipHistory()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $data['history'] = $this->Common_model->get_data_by_query("SELECT a.*, b.username, c.id as bid, c.match_name, c.team, c.bet_type, c.market, d.winner FROM `credits_debits` a LEFT JOIN users b ON a.user_id = b.id LEFT JOIN bet c ON a.bet_id = c.id LEFT JOIN running_matches d ON c.market_id = d.market_id WHERE a.user_id = $this->id AND a.type='bet' ORDER BY a.id DESC");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/chip_history', $data);
        $this->load->view('layout/backend_footer');
    }

    public function chipSummaryOld()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
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
        $this->load->view('supermaster/chip_summary', $data);
        $this->load->view('layout/backend_footer');
    }

    public function chipSummary()
    {
        $uid = $this->input->get('user_id');
        $group = $this->Common_model->findfield('users_with_groups', 'id', $uid, 'group_name');
        if ($group == 'user') {
            redirect('SuperMaster/userChipSummary?user_id=' . $uid);
        } elseif ($group == 'master') {
            redirect('SuperMaster/masterChipSummary?user_id=' . $uid);
        } elseif ($group == 'supermaster') {
            redirect('SuperMaster/superMasterChipSummary?user_id=' . $uid);
        } elseif ($group == 'admin') {
            redirect('SuperMaster/adminChipSummary?user_id=' . $uid);
        }
    }

    public function userChipSummary()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
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
        $this->load->view('supermaster/userchip_summary', $data);
        $this->load->view('layout/backend_footer');
    }


    public function masterChipSummary()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
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
        $this->load->view('supermaster/masterchip_summary', $data);
        $this->load->view('layout/backend_footer');
    }

    public function superMasterChipSummary()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
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
        $this->load->view('supermaster/supermasterchip_summary', $data);
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
        redirect('SuperMaster/chipSummary?user_id=' . $cid);
    }

    public function updateDCBal($uid)
    {
        $dc = $this->Common_model->get_data_by_query("SELECT * FROM credits_debits where user_id = $uid order by id ASC");
        $plus = 0;
        $minus = 0;
        foreach ($dc as $d) {
            $plus += $d['credits'] - $d['debits'];
            $data = array('balance' => $plus);
            $this->Crud_model->edit_record('credits_debits', $d['id'], $data);
        }
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

    public function profitLoss()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $statements = $this->Common_model->get_data_by_query("SELECT a.*, b.id as bid, b.market, b.user_commission, b.master_commission FROM credits_debits a LEFT JOIN bet b on a.bet_id = b.id where a.user_id = $this->id AND a.type = 'bet' order by a.id desc");
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
        $this->load->view('supermaster/profit_loss', $data);
        $this->load->view('layout/backend_footer');
    }

    public function betByMatchId()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $mid = $this->input->get('match_id');
        $data['bets'] = $this->Common_model->get_data_by_query("select * from bet where match_id = $mid order by id DESC");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/bet_matchid', $data);
        $this->load->view('layout/backend_footer');
    }

    public function betHistory()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $data['bets'] = $this->Common_model->get_data_by_query("SELECT a.*, b.username FROM `bet` a LEFT JOIN users b ON a.user_id = b.id WHERE a.user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $this->id)) ORDER BY a.id DESC");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/bet_history', $data);
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

    public function fancyData()
    {
        $marketId = $this->input->get('market_id');
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
    public function showMatches(){
        $user_id = $this->session->userdata('user_id');
        // get user parent
        $getUSerDetail = $this->Common_model->get_record_by_id("users", $user_id);
       
        $EventList = $this->Common_model->get_data_by_query("SELECT * FROM event_block WHERE user_id IN ($user_id, $getUSerDetail->parent_id)");
        $eventIds = [];
        foreach($EventList as $list){
            $event_ids = json_decode($list['event_id']);
          $eventIds = array_merge($eventIds,$event_ids);
        }

        $id = '';

        foreach($eventIds as $key => $eid){
            if($key === 0){
                $id .= $eid;
            }else{
                $id .=", ".$eid ;
            }
        }
        
        return $id;

    }

    public function runningCricket()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $today = date('Y-m-d');
        $id = $this->showMatches();
        
        if($this->showMatch == 'yes') {
            $data['crickets'] = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND event_id NOT IN ($id)");
        } else $data = array();

        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/running_matches', $data);
        $this->load->view('layout/backend_footer');
    }

    public function matchOdds()
    {
        $hdata['title'] = 'SuperMaster Panel | Setbat Exch';
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $hdata['heading'] = $this->panel->title;
        $this->load->view('layout/backend_header', $hdata);
        $eid = $this->input->get('match_id');
        $mid = $this->input->get('market_id');
        $data['match'] = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $data['odds'] = $this->match->matchOddByMarketId($mid);
        $data['dfancy'] = $this->Common_model->get_data_by_query("SELECT * FROM fancy_data WHERE market_id = '$mid' AND status NOT IN ('settled','paused')");
        $data['fancy'] = $this->match->matchFancies($eid);
        $data['ubets'] = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mid' AND bet_type = 'unmatched' AND user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $this->id))");
        $data['mbets'] = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mid' AND bet_type = 'matched' AND user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $this->id))");
        $data['fbets'] = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mid' AND bet_type = 'fancy' AND user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $this->id))");
        $this->load->view('layout/backend_sidebar');
        $this->load->view('supermaster/match_odds', $data);
        $this->load->view('layout/backend_footer');
    }

    public function scoreReload()
    {
        $mkid = $this->input->get('market_id');
        $mid = $this->input->get('match_id');
        $match = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = '$mkid' AND event_id = $mid");
        $score = '';
        if (isset($match) && ($match->cricbuzz_id != '' || $match->cricbuzz_id != null)) {
            $scoreData = $this->match->cricketScore($match->cricbuzz_id);
            $miniscore = $scoreData['miniscore'];
            // print_r($scoreData['miniscore']);die;
            $score .= '<p class="text-danger">' . $miniscore['matchScoreDetails']['customStatus'] . '</p>';
            $score .= '<table class="table table-bordered table-sm table-striped"><tbody>';
            foreach ($miniscore['matchScoreDetails']['inningsScoreList'] as $key => $sc) {
                $score .= '<tr><td>' . $sc['batTeamName'] . ' ' . $sc['score'] . '/' . $sc['wickets'] . ' (' . $sc['overs'] . ' ov)</td></tr>';
            }
            $score .= '<tr><th><div class="d-flex justify-content-between mb-3"><div class="p-2 ">Cur Ov ' . $miniscore['overs'] . '</div><div class="p-2">Runrate ' . $miniscore['currentRunRate'] . '</div></div></th></tr>';
            $score .= '<tr><th>Recent Stats ' . $miniscore['recentOvsStats'] . '</th></tr>';
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
        $matchOdds = $odds[0]['teams'];
        $oddData = '<table class="table table-bordered table-condensed" width="100%" >
                        <tr>
                          <th style="border: none !important;"><b style="color: red;">Min stake:100 Max stake:200000</b></th>
                          <th style="background: #2c5ca9; color: white; border: none !important; min-width: 50px;  max-width: 50px;"><center>back</center></th>
                          <th style="background: red; color: white; border: none !important; min-width: 50px; max-width: 50px;"><center>lay</center></th>
                        </tr>';
        foreach ($matchOdds as $mk => $mo) {

            $oddData .= '<tr>
                <td><b>' . $mo->name . '</b><span class="pull-right" id="' . $mo->id . '"></span></td>
                <td style="background: #b5e0ff; cursor: pointer;"><center><b>' . $mo->back['price'] . '</b><br/>' . $mo->back['size'] . '</center></td>
                <td style="background: #ffbfcd; cursor: pointer;" ><center><b>' . $mo->lay['price'] . '</b><br/>' . $mo->lay['size'] . '</center></td>
            </tr>';
        }
        $oddData .= '</table>';
        echo $oddData;
    }

    public function betReload()
    {
        $mid = $this->input->get('market_id');
        $ubets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mid' AND bet_type = 'unmatched' AND user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $this->id))");
        $mbets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mid' AND bet_type = 'matched' AND user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $this->id))");
        $fbets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mid' AND bet_type = 'fancy' AND user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $this->id))");
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
        $masters = $this->Common_model->get_data_by_query("select * from users where parent_id = $this->id");
        $tt1pl = 0;
        $tt2pl = 0;
        $tt3pl = 0;
        $abcd = array();
        foreach ($masters as $mk => $mv) {
            $msd = $mv['id'];
            foreach ($runners as $rk => $r) {
                $tid = $r->id;
                $allTeams[$rk]['back'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id = $msd) AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'back' AND bet_type = 'matched'");
                $allTeams[$rk]['lay'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id = $msd) AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'lay' AND bet_type = 'matched'");
                $allTeams[$rk]['tid'] = $tid;
            }
            $supermasterPartnership = $this->Common_model->findfield('users', 'id', $msd, 'commission');

            foreach ($runners as $rk => $rv) {
                $abcd[$rk]['pl'] += ($supermasterPartnership * $this->calculateNewResult($allTeams, $rk)) / 100;
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
        $masters = $this->Common_model->get_data_by_query("select * from users where parent_id = $this->id");
        echo '<table class="table table-responsive table-striped table-bordered"><thead><tr><th>Username</th>';
        foreach ($runners as $r) {
            echo '<th>' . $r->name . '</th>';
        }
        echo '</tr></thead><tbody>';
        foreach ($masters as $mk => $m) {
            $uid = $m['id'];
            echo '<tr><td>' . $m['username'] . '</td>';
            foreach ($runners as $rk => $r) {
                $tid = $r->id;
                $allTeams[$rk]['back'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN (SELECT id FROM users WHERE parent_id = $uid) AND market_id = '$mkid' AND team_id = $tid AND back_lay = 'back' AND bet_type = 'matched'");
                $allTeams[$rk]['lay'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN (SELECT id FROM users WHERE parent_id = $uid) AND market_id = '$mkid' AND team_id = $tid AND back_lay = 'lay' AND bet_type = 'matched'");
                $allTeams[$rk]['tid'] = $tid;
            }
            $supermasterPartnership = $this->Common_model->findfield('users', 'id', $uid, 'commission');
            foreach ($runners as $rk => $r) {
                $tid = $r->id;
                $pl = ($supermasterPartnership * $this->calculateNewResult($allTeams, $rk)) / 100;
                $class = $pl >= 0 ? 'text-danger' : 'text-success';
                echo '<td><span class="' . $class . '">' . abs($pl) . '</span></td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table>';
    }

    public function teamProfitLossMaster()
    {
        $mid = $this->input->get('market_id');
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $teams = json_decode($match->teams, true);
        $team1 = $teams[0]['id'];
        $team2 = $teams[1]['id'];
        $team1Name = $teams[0]['name'];
        $team2Name = $teams[1]['name'];
        $p_l = array();
        $masters = $this->Common_model->get_data_by_query("select * from users where parent_id = $this->id");
        foreach ($masters as $mk => $m) {
            $msd = $m['id'];
            $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id = $msd) AND market_id = '$mid' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
            $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id = $msd) AND market_id = '$mid' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
            $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id = $msd) AND market_id = '$mid' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
            $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id IN(SELECT id FROM users WHERE parent_id = $msd) AND market_id = '$mid' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
            $team1win = $team1backprofitloss->p + $team2layprofitloss->p - $team2backprofitloss->l - $team1layprofitloss->l;
            $team2win = $team2backprofitloss->p + $team1layprofitloss->p - $team1backprofitloss->l - $team2layprofitloss->l;
            $supermasterPartnership = $this->Common_model->findfield('users', 'id', $msd, 'commission');
            $team1_pl = ($team1win * $supermasterPartnership) / 100;
            $team2_pl = ($team2win * $supermasterPartnership) / 100;
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
            $p_l[$mk]['username'] = $m['username'];
            $p_l[$mk]['uid']      = $m['id'];
            $p_l[$mk]['team1_pl'] = $team1_pl;
            $p_l[$mk]['team1_cl'] = $team1_cl;
            $p_l[$mk]['team2_pl'] = $team2_pl;
            $p_l[$mk]['team2_cl'] = $team2_cl;
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
        $mkid = $this->input->get('market_id');
        $bets = $this->Common_model->get_data_by_query("SELECT a.*, b.parent_id FROM bet a LEFT JOIN users_with_groups b ON a.user_id = b.id WHERE a.market_id = '$mkid' AND a.team = '$runner' AND a.bet_type = 'fancy' AND a.user_id IN (SELECT id FROM users WHERE parent_id IN (SELECT id FROM users WHERE parent_id = $this->id)) ORDER BY a.odd ASC");
        echo '<h4>' . $runner . '</h4>';
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
        $oddData = array();
        $plus = 0;
        $minus = 0;

        foreach ($bets as $key => $b) {
            if (array_key_exists($b['odd'], $odds)) {
                $odds[$b['odd']][] = $b['user_id'];
            } else {
                $odds[$b['odd']] = array($b['user_id']);
            }
        }
        foreach ($odds as $ok => $o) {
            $final = 0;
            foreach ($o as $uuid) {
                $yy = $this->Common_model->get_single_query("SELECT SUM(a.profit) AS p FROM bet a WHERE a.market_id = '$mkid' AND a.team = '$runner' AND a.bet_type = 'fancy' AND a.odd = $ok AND a.back_lay = 'back' AND a.user_id = $uuid");
                $nn = $this->Common_model->get_single_query("SELECT SUM(a.profit) AS p FROM bet a WHERE a.market_id = '$mkid' AND a.team = '$runner' AND a.bet_type = 'fancy' AND a.odd = $ok AND a.back_lay = 'lay' AND a.user_id = $uuid");
                $pp = isset($yy) ? $yy->p : 0;
                $ll = isset($nn) ? $nn->p : 0;
                $mid = $this->Common_model->findfield('users', 'id', $uuid, 'parent_id'); //master id
                $master = $this->Common_model->get_single_query("select * from users where id = $mid");
                $mc = $master->commission; //master partnershipt to super master
                if (empty($mc)) $mc = 0;
                $calc = $pp - $ll;
                $msCommission = $calc - abs($calc) * ($mc) / 100;
                $final += $msCommission;
            }
            if ($final > 0) {
                $show = '<span class="text-danger">' . $final . '</span>';
            } else {
                $show = '<span class="text-success">' . $final . '</span>';
            }
            $aa .= '<tr>
                    <td>' . $ok . '</td>
                    <td>' . $show . '</td>
                </tr>';
        }
        $aa .= '</tbody></table>';
        echo $aa;
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
}
