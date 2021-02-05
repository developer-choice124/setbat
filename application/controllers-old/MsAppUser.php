<?php

class MsAppUser extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation', 'match'));
        $this->load->helper(array('url', 'language'));
        $this->load->model(array('Setting_model', 'MsAppUser_model'));
        $this->settings = $this->Setting_model->get_setting();
        if (!empty($this->settings->timezone)) {
            date_default_timezone_set($this->settings->timezone);
        } else {
            date_default_timezone_set("Asia/Kolkata");
        }
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
        if (!$this->ion_auth->logged_in()) {
            redirect('MsAuth/login');
        }
        $this->id = $this->session->userdata('user_id');
        $this->panel = $this->Common_model->get_single_query("SELECT * FROM panel_title ORDER BY id DESC");
    }

    public function index1()
    {
        echo 'hi';
        echo 'hello';
    }

    public function index()
    {

        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $matches = array();
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        if(!empty($matches)) {
            foreach ($matches as $mkey => $m) {
                $odd = $this->match->matchOdd($m['market_id']);
                $runners = $odd['runners'];
                $matches[$mkey]['odds'] = $runners;
                $matches[$mkey]['status'] = $odd['inplay'];
            }
        }

        $data['matches'] = $matches;
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $this->load->view('msappuser/index', $data);
        $this->load->view('msappuser/footer');
    }

    public function inPlay()
    {

        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $matches = array();
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        if (!empty($matches)) {
            foreach ($matches as $mkey => $m) {
                $odd = $this->match->matchOdd($m['market_id']);
                $runners = $odd['runners'];
                $matches[$mkey]['odds'] = $runners;
                $matches[$mkey]['status'] = $odd['inplay'];
            }
        }

        $data['matches'] = $matches;
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $this->load->view('msappuser/in_play', $data);
        $this->load->view('msappuser/footer');
    }

    public function getInPlay()
    {
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        foreach ($matches as $mkey => $m) {
            $odd = $this->match->matchOdd($m['market_id']);
            $runners = $odd['runners'];
            $matches[$mkey]['odds'] = $runners;
            $matches[$mkey]['status'] = $odd['inplay'];
        }
        $data = '<table id="" class="table table-bordered table-striped table-sm" cellspacing="0" width="100%">
            <tbody>';
        $i = 1;
        foreach ($matches as $mkey => $m) {
            if ($m['status'] == 1 || $m['status'] == true) {
                $mst = 'In Play';

                $data .= '<tr>
                          <td><a href="' . base_url('MsAppUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']) . '">' . $m['event_name'] . '</a></td>
                          <td><a href="' . base_url('MsAppUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']) . '">' . $mst . '</a></td>
                          <td>' . date('D d-M-Y H:i:sa', strtotime($m['start_date'])) . '</td>
                          <td>';
                foreach ($m['odds'] as $r) :
                    $data .= '<a href="' . base_url('MsAppUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']) . '" class="btn btn-info" style="color: white;">
                                ' . $r['ex']['availableToBack'][0]['price'] . '
                              </a>
                              <a href="' . base_url('MsAppUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']) . '" class="btn btn-danger" style="color: white;">
                                ' . $r['ex']['availableToLay'][0]['price'] . '
                              </a>';
                endforeach;
                $data .= '</td>
                        </tr>';
            }
        }
        $data .= '</tbody>
        </table>';
        echo $data;
    }

    public function crickets()
    {
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $matches = array();
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        if (!empty($matches)) {
            foreach ($matches as $mkey => $m) {
                $odd = $this->match->matchOdd($m['market_id']);
                $runners = $odd['runners'];
                $matches[$mkey]['odds'] = $runners;
                $matches[$mkey]['status'] = $odd['inplay'];
            }
        }

        $data['matches'] = $matches;
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $this->load->view('msappuser/crickets', $data);
        $this->load->view('msappuser/footer');
    }

    public function getCrickets()
    {
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        foreach ($matches as $mkey => $m) {
            $odd = $this->match->matchOdd($m['market_id']);
            $runners = $odd['runners'];
            $matches[$mkey]['odds'] = $runners;
            $matches[$mkey]['status'] = $odd['inplay'];
        }
        $data = '<table id="" class="table table-bordered table-striped table-sm" cellspacing="0" width="100%">
            <tbody>';
        $i = 1;
        $mst = '';
        foreach ($matches as $mkey => $m) {
            if ($m['status'] == 1 || $m['status'] == true) {
                $mst = 'In Play';
            }
                $data .= '<tr>
                          <td><a href="' . base_url('MsAppUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']) . '">' . $m['event_name'] . '</a></td>
                          <td><a href="' . base_url('MsAppUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']) . '">' . $mst . '</a></td>
                          <td>' . date('D d-M-Y H:i:sa', strtotime($m['start_date'])) . '</td>
                          <td>';
                foreach ($m['odds'] as $r) :
                    $data .= '<a href="' . base_url('MsAppUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']) . '" class="btn btn-info" style="color: white;">
                                ' . $r['ex']['availableToBack'][0]['price'] . '
                              </a>
                              <a href="' . base_url('MsAppUser/match?market_id=' . $m['market_id'] . '&match_id=' . $m['event_id']) . '" class="btn btn-danger" style="color: white;">
                                ' . $r['ex']['availableToLay'][0]['price'] . '
                              </a>';
                endforeach;
                $data .= '</td>
                        </tr>';
        }
        $data .= '</tbody>
        </table>';
        echo $data;
    }

    public function editStake()
    {
        $data = array(
            'chip_name_1' => $this->input->post('chip_name_1'),
            'chip_value_1' => $this->input->post('chip_value_1'),
            'chip_name_2' => $this->input->post('chip_name_2'),
            'chip_value_2' => $this->input->post('chip_value_2'),
            'chip_name_3' => $this->input->post('chip_name_3'),
            'chip_value_3' => $this->input->post('chip_value_3'),
            'chip_name_4' => $this->input->post('chip_name_4'),
            'chip_value_4' => $this->input->post('chip_value_4'),
            'chip_name_5' => $this->input->post('chip_name_5'),
            'chip_value_5' => $this->input->post('chip_value_5'),
            'chip_name_6' => $this->input->post('chip_name_6'),
            'chip_value_6' => $this->input->post('chip_value_6'),
            'user_id' => $this->id,
            'updated_at' => date('Y-m-d h:i:s')
        );
        $chip = $this->Common_model->get_single_query("select * from chip_setting where user_id = $this->id");
        if ($chip) {
            $this->Crud_model->edit_record('chip_setting', $chip->id, $data);
        } else {
            $data['created_at'] = date('Y-m-d h:i:s');
            $this->Crud_model->insert_record('chip_setting', $data);
        }
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Chip Setting has been updated successfully</div>");
        redirect('MsAppUser/index');
    }

    public function accountInfo()
    {
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $data['info'] = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
        $data['ud'] = $this->Common_model->get_single_query("SELECT SUM(profit) as up, SUM(loss) as down FROM `profit_loss` WHERE user_id = $this->id");
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $this->load->view('msappuser/account_info', $data);
        $this->load->view('msappuser/footer');
    }

    public function accountStatement()
    {
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $data['statements'] = $this->Common_model->get_data_by_query("select * from credits_debits where user_id = $this->id order by id DESC");
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $this->load->view('msappuser/account_statement', $data);
        $this->load->view('msappuser/footer');
    }

    public function chipHistory()
    {
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $data['history'] = $this->Common_model->get_data_by_query("select * from profit_loss where user_id = $this->id order by id DESC");
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $this->load->view('msappuser/chip_history', $data);
        $this->load->view('msappuser/footer');
    }

    public function profitLoss()
    {
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $data['profitLosses'] = $this->Common_model->get_data_by_query("select * from profit_loss where user_id = $this->id");
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $this->load->view('msappuser/profit_loss', $data);
        $this->load->view('msappuser/footer');
    }

    public function betHistory()
    {
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $data['bets'] = $this->Common_model->get_data_by_query("select * from bet where user_id = $this->id order by id DESC");
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $this->load->view('msappuser/bet_history', $data);
        $this->load->view('msappuser/footer');
    }

    public function bet()
    {
        $id = $this->input->get('bet_id');
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $data['bet'] = $this->Common_model->get_single_query("select * from bet where id = $id");
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $this->load->view('msappuser/bet', $data);
        $this->load->view('msappuser/footer');
    }

    public function changePassword()
    {
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $this->load->view('msappuser/change_password');
        $this->load->view('msappuser/footer');
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
            redirect('MsAppUser/changePassword');
        } else {
            $identity = $this->session->userdata('identity');

            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

            if ($change) {
                //if the password was successfully changed
                $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" . $this->ion_auth->messages() . "</div>");
                redirect('MsAuth/logout');
            } else {
                $this->session->set_flashdata('message', "<div class='alert alert-error alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" . $this->ion_auth->errors() . "</div>");
                redirect('MsAppUser/changePassword', 'refresh');
            }
        }
    }

    public function match()
    {
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $mkid = $this->input->get('market_id');
        $mid = $this->input->get('match_id');
        $data['match'] = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = '$mkid' AND event_id = $mid");
        $data['odds'] = $this->match->matchOdd($mkid);
        $data['dfancy'] = $this->Common_model->get_data_by_query("SELECT * FROM fancy_data WHERE market_id = '$mkid' AND status='playing'");
        $data['fancy'] = $this->match->fancyData($mkid);
        //print_r($data['odds']);die;
        $this->load->view('msappuser/match', $data);
        $this->load->view('msappuser/footer');
    }

    public function unmatchReload() {
        $mkid = $this->input->get('market_id');
        $mid = $this->input->get('match_id');
        $match = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = '$mkid' AND event_id = $mid");
        $ubets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE user_id = $this->id AND market_id = '$mkid' AND match_id = $mid AND bet_type = 'unmatched' AND status = 'pending'");
        $odds = $this->match->matchOdd($mkid);
        //$res = $this->plReload($mkid,$mid,$odds);
        $runners = $odds['runners'];
        $teams = json_decode($match->teams);
        $teamIds = array();
        foreach ($teams as $tm) {
          $teamIds[] = $tm->id;
        }
        $bprice = 0; $bsize = 0; $lprice = 0; $lsize = 0;
        $data = '';
        foreach ($runners as $rk => $r):
            $bprice = $r['ex']['availableToBack'][0]['price'];
            $bsize = $r['ex']['availableToBack'][0]['size'];
            $lprice = $r['ex']['availableToLay'][0]['price'];
            $lsize = $r['ex']['availableToLay'][0]['size'];
            $untid = $teams[$rk]->id;
            //unmatched check
            if(!empty($bprice)) {
                $backBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mkid' AND bet_type = 'unmatched' AND back_lay = 'back' AND team_id = $untid AND odd <= $bprice AND status = 'pending' AND user_id = $this->id");
                if($backBets) {
                    foreach ($backBets as $bk => $b) {
                       $bdata = array(
                        'bet_type'  => 'matched',
                        'updated_at'=> date('Y-m-d H:i:s')
                       );
                       $this->Crud_model->edit_record('bet',$b['id'],$bdata);
                    }
                }
            }

            if(!empty($lprice)) {
                $layBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mkid' AND bet_type = 'unmatched' AND back_lay = 'lay' AND team_id = $untid AND odd >= $lprice AND status = 'pending' AND user_id = $this->id");
            
                if($layBets) {
                    foreach ($layBets as $lk => $l) {
                       $ldata = array(
                        'bet_type'  => 'matched',
                        'updated_at'=> date('Y-m-d H:i:s')
                       );
                       $this->Crud_model->edit_record('bet',$l['id'],$ldata);
                    }
                }
            }
        endforeach;
        $msg = '';
        $msg .= '<table id="" class="table table-bordered table-sm" cellspacing="0" width="100%">
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
        foreach ($ubets as $ub) {
            $cls = $ub['back_lay'] == 'back' ? 'back' : 'lay';
            $delM = "deleteUnmatched('" . $ub['id'] . "')";
            $msg .= '<tr class="'.$cls.'">
                      <td><a href="javascript:void(0)" onclick="' . $delM . '"><i class="fa fa-trash-o"></i></a>&nbsp;&nbsp;'.$ub['team'].'</td>
                      <td>'. $ub['back_lay'] .'</td>
                      <td>'. $ub['odd'] .'</td>
                      <td>'. $ub['stake'] .'</td>
                      <td>'. $ub['profit'] .'</td>
                      <td>'. $ub['loss'] .'</td>
                      <td>'. $ub['ip'] .'</td>
                      <td>'. $ub['id'] .'</td>
                  </tr>';
        }
        $msg .= '</table>';
        echo json_encode(array('msg' => $msg, 'tot' => count($ubets)));
    }

    public function callAsync() {
        $mkid = $this->input->get('market_id');
        $mid = $this->input->get('match_id');
        $odds = $this->oddsReload($mkid,$mid);
        $fancies = $this->fancyReload($mkid,$mid);
        //$fancies = '';
        echo json_encode(array(
            'oddData' => $odds,
            'fancyData' => $fancies
        ));
    }

    public function oddsReload($mkid,$mid) {
        $match = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = '$mkid' AND event_id = $mid");
        $odds = $this->match->matchOdd($mkid);
        //$res = $this->plReload($mkid,$mid,$odds);
        $runners = $odds['runners'];
        $teams = json_decode($match->teams);
        $teamIds = array();
        foreach ($teams as $tm) {
          $teamIds[] = $tm->id;
        }
        $bprice = 0; $bsize = 0; $lprice = 0; $lsize = 0;
        $data = '';
        foreach ($runners as $rk => $r):
        $bprice = $r['ex']['availableToBack'][0]['price'];
        $bsize = $r['ex']['availableToBack'][0]['size'];
        $lprice = $r['ex']['availableToLay'][0]['price'];
        $lsize = $r['ex']['availableToLay'][0]['size'];
        $untid = $teams[$rk]->id;
        //unmatched check
        if(!empty($bprice)) {
            $backBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mkid' AND bet_type = 'unmatched' AND back_lay = 'back' AND team_id = $untid AND odd <= $bprice AND status = 'pending' AND user_id = $this->id");
            if($backBets) {
                foreach ($backBets as $bk => $b) {
                   $bdata = array(
                    'bet_type'  => 'matched',
                    'updated_at'=> date('Y-m-d H:i:s')
                   );
                   $this->Crud_model->edit_record('bet',$b['id'],$bdata);
                }
            }
        }

        if(!empty($lprice)) {
            $layBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mkid' AND bet_type = 'unmatched' AND back_lay = 'lay' AND team_id = $untid AND odd >= $lprice AND status = 'pending' AND user_id = $this->id");
        
            if($layBets) {
                foreach ($layBets as $lk => $l) {
                   $ldata = array(
                    'bet_type'  => 'matched',
                    'updated_at'=> date('Y-m-d H:i:s')
                   );
                   $this->Crud_model->edit_record('bet',$l['id'],$ldata);
                }
            }
        }
        
        
        //end unmatched check
        //$class = $res[$rk]['pl'] >= 0 ? 'text-success' : 'text-danger';
        
        $backy = "showBackBetDiv('".$teams[$rk]->id."','".$teams[$rk]->name."','".$rk."','back','matched','".$bprice."','".$bsize."')";
        $layy = "showLayBetDiv('".$teams[$rk]->id."','".$teams[$rk]->name."','".$rk."','lay','matched','".$lprice."','".$lsize."')";
        $data .= '<div class="row">
              <div class="col-6 border">
                <span class="font-weight-bold pl-1 clearfix">'.$teams[$rk]->name.'</span>
                <span id="'.$teams[$rk]->id.'_pl" 
                  class="pl-1 font-weight-bold "></span>
              </div>
              <div class="col-3 text-center border" id="'.$teams[$rk]->id.'_backParentdiv" style="background: #ffffea; cursor:pointer;">
                <div 
                data-others = "'.json_encode($teamIds).'" id="'.$teams[$rk]->id.'_backdiv" onclick="'.$backy.'">
                  <span id="'.$teams[$rk]->id.'_backodd">
                    <center><b>'.$bprice.'</b><br/>'.$bsize.'</center>
                  </span>
                </div>
              </div>
              <div class="col-3 text-center border" id="'.$teams[$rk]->id.'_layParentdiv" style="background: #ffffea; cursor:pointer;">
                <div data-others = "'.json_encode($teamIds).'" id="'.$teams[$rk]->id.'_laydiv" onclick="'.$layy.'">
                  <span id="'.$teams[$rk]->id.'_layodd">
                    <center><b>'.$lprice.'</b><br/>'.$lsize.'</center>
                  </span>
                </div>
              </div>
            </div>';

        endforeach;
        return $data;
    }

    public function fancyReload($mkid,$mid) {
        $dfancy = $this->Common_model->get_data_by_query("SELECT * FROM fancy_data WHERE market_id = '$mkid' AND status='playing'");
        $fancy = $this->match->fancyData($mkid);
        // start
        $data = '';
        $did = array();
        foreach ($dfancy as $dkey => $d) {
            $did[] = $d['fancy_id'];
        }
        if(!empty($fancy['session'])) {
          $fancies = $fancy['session'];
          foreach ($fancies as $fkey => $f) {
            if (in_array($f['SelectionId'], $did)) { 
             $lprice = $f['LayPrice1'];
             $lsize = $f['LaySize1'];
             $bprice = $f['BackPrice1'];
             $bsize = $f['BackSize1'];
             $yes = "showBackBetDiv('".$f['SelectionId']."','".$f['RunnerName']."','".$fkey."','back','fancy','".$bprice."','".$bsize."')";
             $no = "showLayBetDiv('".$f['SelectionId']."','".$f['RunnerName']."','".$fkey."','lay','fancy','".$lprice."','".$lsize."')";
             $data .= '<div class="row">
                <div class="col-6 border pt-2">'.$f['RunnerName'].'</div>
                <div class="col-3 text-center border" style="background: #ffffea; cursor:pointer;" onclick="'.$no.'">
                  <b>'.$f['LayPrice1'].'</b><br/>'.$f['LaySize1'].'</div>
                <div class="col-3 text-center border" style="background: #ffffea;cursor:pointer;" 
                  onclick="'.$yes.'"
                  ><b>'.$f['BackPrice1'].'</b><br/>'.$f['BackSize1'].'</div>
              </div>';
            }
          }
        }
        return $data;
    }

    public function plReload($market_id,$mid,$odds) {
        
        $runners = $odds['runners'];
        foreach ($runners as $rk => $r) {
            $tid = $r['selectionId'];
            $allTeams[$rk]['back'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'back' AND bet_type = 'matched'");
            $allTeams[$rk]['lay'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'lay' AND bet_type = 'matched'");
            $allTeams[$rk]['tid'] = $tid;
        }
        $res = array();
        foreach ($runners as $rk => $r) {
            $tid = $r['selectionId'];
            $res[$rk]['pl'] = $this->calculateResult($allTeams,$rk);
            $res[$rk]['id'] = $tid;
            
        }
        $res = array_values($res);
        return $res;
    }

    public function unmatchedBets()
    {
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $mkid = $this->input->get('market_id');
        $mid = $this->input->get('match_id');
        $data['match'] = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = '$mkid' AND event_id = $mid");
        $data['ubets'] = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE user_id = $this->id AND market_id = '$mkid' AND match_id = $mid AND bet_type = 'unmatched' AND status = 'pending'");
        $this->load->view('msappuser/unmatched_bets', $data);
        $this->load->view('msappuser/footer');
    }

    public function matchedBets()
    {
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $mkid = $this->input->get('market_id');
        $mid = $this->input->get('match_id');
        $data['match'] = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = '$mkid' AND event_id = $mid");
        $data['mbets'] = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE user_id = $this->id AND market_id = '$mkid' AND match_id = $mid AND bet_type = 'matched' AND status = 'pending'");
        $this->load->view('msappuser/matched_bets', $data);
        $this->load->view('msappuser/footer');
    }

    public function fancyBets()
    {
        $cuser = $this->MsAppUser_model->index();
        $hdata['heading'] = $this->panel->title;
        $hdata['cuser'] = $cuser;
        $this->load->view('msappuser/header', $hdata);
        $this->load->view('msappuser/sidebar');
        $mkid = $this->input->get('market_id');
        $mid = $this->input->get('match_id');
        $data['match'] = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = '$mkid' AND event_id = $mid");
        $data['fbets'] = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE user_id = $this->id AND market_id = '$mkid' AND match_id = $mid AND bet_type = 'fancy'");
        $this->load->view('msappuser/fancy_bets', $data);
        $this->load->view('msappuser/footer');
    }

    public function deleteUnmatched()
    {
        $bid = $this->input->get('bet_id');
        $this->Crud_model->delete_record('bet', $bid);
        $this->getBalance();
        echo json_encode(array('status' => 'success','message' => 'bet deleted'));
    }

    public function placeBet()
    {
        $params = $_REQUEST;
        $match_id     = $params['match_id'];
        $market_id    = $params['market_id'];
        $match_name   = $params['match_name'];
        $team         = $params['team'];
        $team_id      = $params['team_id'];
        $market       = $params['market'];
        $back_lay     = $params['back_lay'];
        $odd          = $params['odd'];
        $stake        = $params['stake'];
        $profit       = $params['profit'];
        $loss         = $params['loss'];
        $bet_type     = $params['bet_type'];
        $line         = $params['line'];
        $place        = 'yes';
        $ok           = 'yes';
        if(empty($match_id) || $match_id == null || trim($match_id) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if(empty($market_id) || $market_id == null || trim($market_id) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if(empty($match_name) || $match_name == null || trim($match_name) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if(empty($team) || $team == null || trim($team) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if(empty($team_id) || $team_id == null || trim($team_id) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if(empty($market) || $market == null || trim($market) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if(empty($odd) || $odd == null || trim($odd) == '' || $odd <= 0) {
            $place = 'no';
            $ok = 'no';
        }
        if(empty($profit) || $profit == null || trim($profit) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if(empty($loss) || $loss == null || trim($loss) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if(empty($bet_type) || $bet_type == null || trim($bet_type) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if(empty($line) || $line == null || trim($line) == '') {
            $place = 'no';
            $ok = 'no';
        }
        $lockBetting = $this->MsAppUser_model->lockBetting();
        $cuser = $this->MsAppUser_model->index();
        $ateam = $this->Common_model->get_single_query("select * from cron_data where event_id = $match_id");
        $Modds = $this->match->matchOdd($market_id);
        $runners = $Modds['runners'];
        $allTeams = array();
        $matchDetails = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = '$market_id'");
        if($matchDetails->match_result == 'paused') {
            $place = 'no';
            $message = 'bet could not be placed as match has been paused';
            echo json_encode(array(
                'message'   => $message,
                'class'     => $place == 'yes' ? 'alert-success' : 'alert-danger', 
                'bal'       => $cuser->balanced_chips
            ));
        } else {
            if ($lockBetting == 'yes') {
                $place = 'no';
                $message = 'bet could not be placed as betting account is locked';
                echo json_encode(array(
                    'message'   => $message,
                    'class'     => $place == 'yes' ? 'alert-success' : 'alert-danger', 
                    'bal'       => $cuser->balanced_chips
                ));
            } else {
                $checkFancy = '';
                if($bet_type == 'fancy') {
                    $fancy = $this->match->fancyData($market_id);
                    $fancies = $fancy['session'];
                    foreach ($fancies as $fk => $f) {
                        if ($f['RunnerName'] == $team) {
                            $frodd = $back_lay == 'back' ? $f['BackPrice1'] : $f['LayPrice1'];
                            $frline = $back_lay == 'back' ? $f['BackSize1'] : $f['LaySize1'];
                            $profit = $back_lay == 'back' ? ($line * $stake) / 100 : $stake;
                            $loss = $back_lay == 'back' ? $stake : ($line * $stake) / 100;
                            if ($odd > 0 && is_numeric($odd)) {
                                if($frodd != $odd || $frline != $line ) {
                                    $place = 'no';
                                    $message = 'Fancy bet could not be placed';
                                } else {
                                   $place = 'yes';
                                }
                            } else {
                                $place = 'no';
                                $message = 'Fancy bet could not be placed';
                            }
                            $checkFancy = $f['RunnerName'];
                        }
                    }
                    if(empty($checkFancy) || $checkFancy == null || strlen($checkFancy) == 0) {
                        $place = 'no';
                    }
                    if($place == 'yes') {
                        $flm = 0;
                        if ($back_lay == 'back') {
                            $fbl = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$team' AND odd >= '$odd'");
                            $fmp = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$team' AND odd <= '$odd'");
                        } else {
                            $fbl = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$team' AND odd <= '$odd'");
                            $fmp = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$team' AND odd >= '$odd'");
                        }
                        if($fbl) {
                            $flm = (2*($fbl->l) - ($fmp ? $fmp->l : 0));
                        }
                        $actualLoss = abs($flm) + $cuser->balanced_chips;
                        if($loss > $actualLoss) {
                            $place = 'no';
                            $message = 'Fancy bet could not be placed due to insufficient balance';
                        }
                    }
                } else {
                    foreach ($runners as $rk => $r) {
                        if ($r['selectionId'] == $team_id) {
                            if ($back_lay == 'back') {
                                $rodd = $r['ex']['availableToBack'][0]['price'];
                                if ($rodd >= $odd) {
                                    $odd = $rodd;
                                    $profit = ($rodd * $stake) - $stake;
                                    $loss = $stake;
                                } else {
                                    $bet_type = 'unmatched';
                                    $profit = ($odd * $stake) - $stake;
                                    $loss = $stake;
                                }
                            } else {
                                $rodd = $r['ex']['availableToLay'][0]['price'];
                                if ($rodd <= $odd) {
                                    $odd = $rodd;
                                    $profit = $stake;
                                    $loss = ($rodd * $stake) - $stake;
                                } else {
                                    $bet_type = 'unmatched';
                                    $profit = $stake;
                                    $loss = ($odd * $stake) - $stake;
                                }
                            }
                        }
                        
                    }
                    if($bet_type == 'unmatched') {
                        if($loss > $cuser->balanced_chips) {
                            $place = 'no';
                            $message = 'Unmatched bet could not be placed due to insufficient balance';
                        }
                    }

                    if ($bet_type == 'matched') {
                        foreach ($runners as $rk => $r) {
                            $tid = $r['selectionId'];
                            $allTeams[$rk]['back'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'back' AND bet_type = 'matched'");
                            $allTeams[$rk]['lay'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'lay' AND bet_type = 'matched'");
                            $allTeams[$rk]['tid'] = $tid;
                        }
                        $res = array();
                        foreach ($runners as $rk => $r) {
                            $tid = $r['selectionId'];
                            
                            $res[$rk]['pl'] = $this->calculateResult($allTeams,$rk);
                            $res[$rk]['id'] = $tid;
                            if($tid == $team_id) {
                                $current = $res[$rk]['pl'];
                                unset($res[$rk]);
                            }
                            
                        }
                        $res = array_values($res);
                        $t1pl = $current;
                        $t2pl = $res[0]['pl'];
                        $t3pl = 0;
                        if(count($allTeams) > 2) {
                            $t3pl = $res[1]['pl'];
                        }
                        $team3Final = 0;
                        if($back_lay == 'back') {
                            $team1Final = $t1pl + $profit;
                            $team2Final = $t2pl - $loss;
                            if(count($allTeams) > 2) {
                                $team3Final = $t3pl - $loss;
                            }

                        } else {
                            $team1Final = $t1pl - $loss;
                            $team2Final = $t2pl + $profit;
                            if(count($allTeams) > 2) {
                                $team3Final = $t3pl + $profit;
                            }

                        }
                        $limit = $this->checkMaxLimit($market_id);
                        $max = min($team1Final,$team2Final,$team3Final);
                        if(abs($max) > $limit)
                        {
                            $place = 'no';
                            $message = 'Bet Can not be placed because loss is higher than balanced chips';
                        }
                    }
                    if($odd >= 10) {
                        $place = 'no';
                        $ok = 'no';
                        $message = 'Bet Can not be placed';
                    }
                }
                if((is_numeric($odd) && $odd > 0) && $profit > 0 && $loss > 0 && $stake >= 100 ) {

                } else {
                    $ok = 'no';
                    $message = 'Bet Can not be placed';
                }
                if($odd <= 0) {
                    $place = 'no';
                    $ok = 'no';
                    $message = 'Bet Can not be placed';
                }
                //print_r($allTeams);
                
                if($place == 'yes' && $ok == 'yes') {
                    $data = array(
                        'user_id' => $this->id,
                        'market_id' => $market_id,
                        'match_id' => $match_id,
                        'match_name' => $match_name,
                        'team' => $team,
                        'team_id' => $team_id,
                        'market' => $market,
                        'back_lay' => $back_lay,
                        'odd' => $odd,
                        'stake' => $stake,
                        'profit' => $profit,
                        'loss' => $loss,
                        'status' => 'pending',
                        'bet_type' => $bet_type,
                        'ip' => $this->input->ip_address(),
                        'all_teams' => $ateam->teams,
                        'line' => $line,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                    
                    if($this->Crud_model->insert_record('bet', $data)) {
                        $message = $bet_type == 'fancy' ? '<strong>Fancy</strong> bet Placed successfully' : ($bet_type == 'matched' ? '<strong>Matched</strong> bet placed successfully' : '<strong>Unmatched</strong> bet placed successfully');
                    } else {
                        $message = 'Bet Cannot be placed';
                        $place = 'no';
                    }
                }
                if($balLeft = $this->getBalance()) {
                    echo json_encode(array(
                        'message'   => $message,
                        'class'     => $place == 'yes' ? 'alert-success' : 'alert-danger', 
                        'bal'       => $balLeft
                    ));
                }
                //$balLeft = $this->getBalance();
            }
        }


        

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

    public function calculateProfitLoss() {
        $market_id = $this->input->get('market_id');
        $Modds = $this->match->matchOdd($market_id);
        $runners = $Modds['runners'];
        foreach ($runners as $rk => $r) {
            $tid = $r['selectionId'];
            $allTeams[$rk]['back'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'back' AND bet_type = 'matched'");
            $allTeams[$rk]['lay'] = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'lay' AND bet_type = 'matched'");
            $allTeams[$rk]['tid'] = $tid;
        }
        $res = array();
        foreach ($runners as $rk => $r) {
            $tid = $r['selectionId'];
            $res[$rk]['pl'] = $this->calculateResult($allTeams,$rk);
            $res[$rk]['id'] = $tid;
            
        }
        $res = array_values($res);
        echo json_encode($res);
    }

    public function checkMaxLimit($mkid) {
        $fancyFinal = 0;
        $tt1w = 0;
        $tt2w = 0;
        $tt3w = 0;
        $smt = 0;
        $untotal = 0;
        $smallest = 0;
        $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'fancy' and status='pending'");
        if (empty($fids)) {
            
        } else {
            $fblid = array();
            foreach ($fids as $mk => $mv) {
                $fmkid = $mv['market_id'];
                //New Code start

                $list = $this->Common_model->ReadRaw("select * from bet where market_id='$fmkid' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
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
                        $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay' AND odd < $minBackOdd AND market_id = '$fmkid' AND team = '$team' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
                    } else {
                        $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay'  AND market_id = '$fmkid' AND team = '$team' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
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
        $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'matched' and status='pending' AND market_id != '$mkid' ");
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
                $team3 = count($teams) > 2 ? $teams[2]->id : 0;
                $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
                $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
                $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
                $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
                if($team3 > 0) {
                    $team3backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'back' AND bet_type = 'matched'");
                    $team3layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'lay' AND bet_type = 'matched'");
                }
                
                $team1win = 0; $team2win = 0; $team3win = 0;
                $team1win = ($team1backprofitloss->p + $team2layprofitloss->p + $team3layprofitloss->p) - ($team1layprofitloss->l + $team2backprofitloss->l + $team3backprofitloss->l);
                $team2win = ($team2backprofitloss->p + $team1layprofitloss->p + $team3layprofitloss->p) - ($team2layprofitloss->l + $team1backprofitloss->l + $team3backprofitloss->l);
                if($team3 > 0) {
                    $team3win = ($team3backprofitloss->p + $team2layprofitloss->p + $team1layprofitloss->p) - ($team3layprofitloss->l + $team2backprofitloss->l + $team1backprofitloss->l);
                }

                $smallest = min($team1win,$team2win,$team3win);
                if($smallest < 0) {
                    $smt += abs($smallest);
                }
            }
        }
        //unmatched part
        $unmids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'unmatched' AND status='pending'");
        if(empty($unmids)) {

        } else {
            foreach ($unmids as $unk => $unm) {
                $unmkids[] = $unm['market_id'];
            }
            $unmmids = array_unique($unmkids);
            foreach ($unmmids as $unmk => $uv) {
                $unmatchedBets = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$uv' AND bet_type = 'unmatched'");
                $untotal += $unmatchedBets->l;
            }
        }
        $ubal = $this->Common_model->get_single_query("SELECT SUM(credits) as c, SUM(debits) as d FROM credits_debits WHERE user_id = $this->id");
        $ubalance = $ubal->c - $ubal->d;
        $bchips = $ubalance - ($smt + $fancyFinal + $untotal);
        return $bchips;
    }

    public function showMaxLimit($mkid) {
        $fancyFinal = 0;
        $tt1w = 0;
        $tt2w = 0;
        $tt3w = 0;
        $smt = 0;
        $untotal = 0;
        $smallest = 0;
        $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'fancy' and status='pending'");
        if (empty($fids)) {
            
        } else {
            $fblid = array();
            foreach ($fids as $mk => $mv) {
                $fmkid = $mv['market_id'];
                //New Code start

                $list = $this->Common_model->ReadRaw("select * from bet where market_id='$fmkid' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
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
                        $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay' AND odd < $minBackOdd AND market_id = '$fmkid' AND team = '$team' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
                    } else {
                        $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay'  AND market_id = '$fmkid' AND team = '$team' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
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
        $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'matched' and status='pending' AND market_id != '$mkid' ");
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
                $team3 = count($teams) > 2 ? $teams[2]->id : 0;
                $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
                $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
                $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
                $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
                if($team3 > 0) {
                    $team3backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'back' AND bet_type = 'matched'");
                    $team3layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'lay' AND bet_type = 'matched'");
                }
                
                $team1win = 0; $team2win = 0; $team3win = 0;
                $team1win = ($team1backprofitloss->p + $team2layprofitloss->p + $team3layprofitloss->p) - ($team1layprofitloss->l + $team2backprofitloss->l + $team3backprofitloss->l);
                $team2win = ($team2backprofitloss->p + $team1layprofitloss->p + $team3layprofitloss->p) - ($team2layprofitloss->l + $team1backprofitloss->l + $team3backprofitloss->l);
                if($team3 > 0) {
                    $team3win = ($team3backprofitloss->p + $team2layprofitloss->p + $team1layprofitloss->p) - ($team3layprofitloss->l + $team2backprofitloss->l + $team1backprofitloss->l);
                }

                $smallest = min($team1win,$team2win,$team3win);
                if($smallest < 0) {
                    $smt += abs($smallest);
                }
            }
        }
        //unmatched part
        $unmids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'unmatched' AND status='pending'");
        if(empty($unmids)) {

        } else {
            foreach ($unmids as $unk => $unm) {
                $unmkids[] = $unm['market_id'];
            }
            $unmmids = array_unique($unmkids);
            foreach ($unmmids as $unmk => $uv) {
                $unmatchedBets = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$uv' AND bet_type = 'unmatched'");
                $untotal += $unmatchedBets->l;
            }
        }
        $ubal = $this->Common_model->get_single_query("SELECT SUM(credits) as c, SUM(debits) as d FROM credits_debits WHERE user_id = $this->id");
        $ubalance = $ubal->c - $ubal->d;
        $bchips = $ubalance - ($smt + $fancyFinal + $untotal);
        echo $bchips;
    }

    public function calculateBalance() {
        $fancyFinal = 0;
        $tt1w = 0;
        $tt2w = 0;
        $tt3w = 0;
        $smt = 0;
        $untotal = 0;
        $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'fancy' and status='pending'");
        if (empty($fids)) {
            
        } else {
            $fblid = array();
            foreach ($fids as $mk => $mv) {
                $fmkid = $mv['market_id'];
                //New Code start

                $list = $this->Common_model->ReadRaw("select * from bet where market_id='$fmkid' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
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
                        $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay' AND odd < $minBackOdd AND market_id = '$fmkid' AND team = '$team' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
                    } else {
                        $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay'  AND market_id = '$fmkid' AND team = '$team' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
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
        $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'matched' and status='pending'");
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
            $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
            $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
            $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
            $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
            if($team3 > 0) {
                $team3backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'back' AND bet_type = 'matched'");
                $team3layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'lay' AND bet_type = 'matched'");
            }
            
            $team1win = 0; $team2win = 0; $team3win = 0;
            $team1win = ($team1backprofitloss->p + $team2layprofitloss->p + $team3layprofitloss->p) - ($team1layprofitloss->l + $team2backprofitloss->l + $team3backprofitloss->l);
            $team2win = ($team2backprofitloss->p + $team1layprofitloss->p + $team3layprofitloss->p) - ($team2layprofitloss->l + $team1backprofitloss->l + $team3backprofitloss->l);
            if($team3 > 0) {
                $team3win = ($team3backprofitloss->p + $team2layprofitloss->p + $team1layprofitloss->p) - ($team3layprofitloss->l + $team2backprofitloss->l + $team1backprofitloss->l);
            }

            $smallest = min($team1win,$team2win,$team3win);
            if($smallest < 0) {
                $smt += abs($smallest);
            }
        }
        //unmatched part
        $unmids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'unmatched' AND status='pending'");
        if(empty($unmids)) {

        } else {
            foreach ($unmids as $unk => $unm) {
                $unmkids[] = $unm['market_id'];
            }
            $unmmids = array_unique($unmkids);
            foreach ($unmmids as $unmk => $uv) {
                $unmatchedBets = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$uv' AND bet_type = 'unmatched'");
                $untotal += $unmatchedBets->l;
            }
        }
        $ubal = $this->Common_model->get_single_query("SELECT SUM(credits) as c, SUM(debits) as d FROM credits_debits WHERE user_id = $this->id");
        $ubalance = $ubal->c - $ubal->d;
        $bchips = $ubalance - ($smt + $fancyFinal + $untotal);
        $cchips = $ubalance - $untotal;
        $bcfdata = array(
            'balanced_chips' => $bchips,
            'current_chips' => $cchips,
            'updated_at' => date('Y-m-d H:i:sa')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips', $this->id, $bcfdata, 'user_id');
        return $bchips;
    }

    public function showBalance() {
        $fancyFinal = 0;
        $tt1w = 0;
        $tt2w = 0;
        $tt3w = 0;
        $smt = 0;
        $untotal = 0;
        $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'fancy' and status='pending'");
        if (empty($fids)) {
            
        } else {
            $fblid = array();
            foreach ($fids as $mk => $mv) {
                $fmkid = $mv['market_id'];
                //New Code start

                $list = $this->Common_model->ReadRaw("select * from bet where market_id='$fmkid' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
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
                        $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay' AND odd < $minBackOdd AND market_id = '$fmkid' AND team = '$team' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
                    } else {
                        $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay'  AND market_id = '$fmkid' AND team = '$team' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
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
        $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'matched' and status='pending'");
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
            $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
            $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
            $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
            $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
            if($team3 > 0) {
                $team3backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'back' AND bet_type = 'matched'");
                $team3layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'lay' AND bet_type = 'matched'");
            }
            
            $team1win = 0; $team2win = 0; $team3win = 0;
            $team1win = ($team1backprofitloss->p + $team2layprofitloss->p + $team3layprofitloss->p) - ($team1layprofitloss->l + $team2backprofitloss->l + $team3backprofitloss->l);
            $team2win = ($team2backprofitloss->p + $team1layprofitloss->p + $team3layprofitloss->p) - ($team2layprofitloss->l + $team1backprofitloss->l + $team3backprofitloss->l);
            if($team3 > 0) {
                $team3win = ($team3backprofitloss->p + $team2layprofitloss->p + $team1layprofitloss->p) - ($team3layprofitloss->l + $team2backprofitloss->l + $team1backprofitloss->l);
            }
            
            $smallest = min($team1win,$team2win,$team3win);
            if($smallest < 0) {
                $smt += abs($smallest);
            }
            echo $team3.'***<---------->'.$team1win.'<---------->'.$team2win.'<---------->'.$team3win.'<---------->*****';
            
        }
        //unmatched part
        $unmids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'unmatched' AND status='pending'");
        if(empty($unmids)) {

        } else {
            foreach ($unmids as $unk => $unm) {
                $unmkids[] = $unm['market_id'];
            }
            $unmmids = array_unique($unmkids);
            foreach ($unmmids as $unmk => $uv) {
                $unmatchedBets = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$uv' AND bet_type = 'unmatched'");
                $untotal += $unmatchedBets->l;
            }
        }
        $ubal = $this->Common_model->get_single_query("SELECT SUM(credits) as c, SUM(debits) as d FROM credits_debits WHERE user_id = $this->id");

        $ubalance = $ubal->c - $ubal->d;
        echo $ubalance.'<---------->'.$smt.'<---------->'.$fancyFinal.'<---------->'.$untotal;
        $bchips = $ubalance - ($smt + $fancyFinal + $untotal);
        $cchips = $ubalance - $untotal;
        $bcfdata = array(
            'balanced_chips' => $bchips,
            'current_chips' => $cchips,
            'updated_at' => date('Y-m-d H:i:sa')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips', $this->id, $bcfdata, 'user_id');
        //echo  $bchips;
    }

    public function getBalance() {
        $fancyFinal = 0;
        $tt1w = 0;
        $tt2w = 0;
        $tt3w = 0;
        $smt = 0;
        $untotal = 0;
        $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'fancy' and status='pending'");
        if (empty($fids)) {
            
        } else {
            $fblid = array();
            foreach ($fids as $mk => $mv) {
                $fmkid = $mv['market_id'];
                //New Code start

                $list = $this->Common_model->ReadRaw("select * from bet where market_id='$fmkid' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
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
                        $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay' AND odd < $minBackOdd AND market_id = '$fmkid' AND team = '$team' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
                    } else {
                        $minloss = $this->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay'  AND market_id = '$fmkid' AND team = '$team' and user_id = $this->id and bet_type = 'fancy' and status = 'pending'");
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
        $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'matched' and status='pending'");
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
            $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
            $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
            $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
            $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
            if($team3 > 0) {
                $team3backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'back' AND bet_type = 'matched'");
                $team3layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team3 AND back_lay = 'lay' AND bet_type = 'matched'");
            }
            
            $team1win = 0; $team2win = 0; $team3win = 0;
            $team1win = ($team1backprofitloss->p + $team2layprofitloss->p + $team3layprofitloss->p) - ($team1layprofitloss->l + $team2backprofitloss->l + $team3backprofitloss->l);
            $team2win = ($team2backprofitloss->p + $team1layprofitloss->p + $team3layprofitloss->p) - ($team2layprofitloss->l + $team1backprofitloss->l + $team3backprofitloss->l);
            if($team3 > 0) {
                $team3win = ($team3backprofitloss->p + $team2layprofitloss->p + $team1layprofitloss->p) - ($team3layprofitloss->l + $team2backprofitloss->l + $team1backprofitloss->l);
            }
            
            $smallest = min($team1win,$team2win,$team3win);
            if($smallest < 0) {
                $smt += abs($smallest);
            }
            //echo $team3.'***<---------->'.$team1win.'<---------->'.$team2win.'<---------->'.$team3win.'<---------->*****';
            
        }
        //unmatched part
        $unmids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'unmatched' AND status='pending'");
        if(empty($unmids)) {

        } else {
            foreach ($unmids as $unk => $unm) {
                $unmkids[] = $unm['market_id'];
            }
            $unmmids = array_unique($unmkids);
            foreach ($unmmids as $unmk => $uv) {
                $unmatchedBets = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$uv' AND bet_type = 'unmatched'");
                $untotal += $unmatchedBets->l;
            }
        }
        $ubal = $this->Common_model->get_single_query("SELECT SUM(credits) as c, SUM(debits) as d FROM credits_debits WHERE user_id = $this->id");

        $ubalance = $ubal->c - $ubal->d;
        //echo $ubalance.'<---------->'.$smt.'<---------->'.$fancyFinal.'<---------->'.$untotal;
        $bchips = $ubalance - ($smt + $fancyFinal + $untotal);
        $cchips = $ubalance - $untotal;
        $bcfdata = array(
            'balanced_chips' => $bchips,
            'current_chips' => $cchips,
            'updated_at' => date('Y-m-d H:i:sa')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips', $this->id, $bcfdata, 'user_id');
        return  $bchips;
    }

    function _outlist($response)
    {
        $outlist = array();
        foreach ($response as $value) {
            $value = (object) $value;
            $outlist[] = $value;
        }
        return $outlist;
    }
}
