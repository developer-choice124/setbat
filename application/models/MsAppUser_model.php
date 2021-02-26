<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MsAppUser_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->id = $this->session->userdata('user_id');
  }

  public function index()
  {
    $cuser = $this->Common_model->get_single_query("SELECT a.lock_betting, a.username, a.session_commission, a.odd_commission, a.first_login, a.parent_id, b.id AS chip_setting_id, b.chip_name_1, b.chip_value_1, b.chip_name_2, b.chip_value_2, b.chip_name_3, b.chip_value_3, b.chip_name_4, b.chip_value_4, b.chip_name_5, b.chip_value_5, b.chip_name_6, b.chip_value_6,
      c.id AS user_chips_id, c.balanced_chips, c.current_chips,
      SUM(d.credits) AS c, SUM(d.debits) AS d 
      FROM users a 
      LEFT JOIN chip_setting b ON b.user_id = a.id 
      LEFT JOIN user_chips c ON c.user_id = a.id 
      LEFT JOIN credits_debits d ON d.user_id = a.id 
      WHERE a.id = $this->id");
    return $cuser;
  }

  public function lockBetting()
  {
    $user = $this->Common_model->get_single_query("SELECT * FROM users WHERE id = $this->id");
    $master = $this->Common_model->get_single_query("SELECT * FROM users WHERE id = $user->parent_id");
    $smaster = $this->Common_model->get_single_query("SELECT * FROM users WHERE id = $master->parent_id");
    $admin = $this->Common_model->get_single_query("SELECT * FROM users WHERE id = $smaster->parent_id");
    $lock = 'no';
    if ($user->lock_betting == 'yes' || $master->lock_betting == 'yes' || $smaster->lock_betting == 'yes' || $admin->lock_betting == 'yes') {
      $lock = 'yes';
    }
    return $lock;
  }

  public function showMatch()
  {
    $user = $this->Common_model->get_single_query("SELECT * FROM users WHERE id = $this->id");
    $master = $this->Common_model->get_single_query("SELECT * FROM users WHERE id = $user->parent_id");
    $smaster = $this->Common_model->get_single_query("SELECT * FROM users WHERE id = $master->parent_id");
    $admin = $this->Common_model->get_single_query("SELECT * FROM users WHERE id = $smaster->parent_id");
    $show = 'yes';
    if ($user->show_match == 'no' || $master->show_match == 'no' || $smaster->show_match == 'no' || $admin->show_match == 'no') {
      $show = 'no';
    }
    return $show;
  }
}

/* End of file MsAppUser_model.php */
/* Location: ./application/models/MsAppUser_model.php */