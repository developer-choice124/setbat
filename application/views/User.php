<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

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
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
        if (!$this->ion_auth->logged_in()) {
            redirect('Auth/login');
        }
        $this->id = $this->session->userdata('user_id');
        $cuser = $this->Common_model->get_single_query("select * from users where id = $this->id");
        $this->lockBetting = $cuser->lock_betting;
        $this->chipSetting = $this->Common_model->get_single_query("select * from chip_setting where user_id = $this->id");
        $this->chips = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
        $this->p_l = $this->Common_model->get_single_query("select sum('credits') as c, sum(debits) as d from credits_debits where user_id = $this->id and type='bet'");
    }

    public function index() {

        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/user_header', $hdata);
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        foreach ($matches as $mkey => $m) {
            $odd = $this->matchOdd($m['market_id']);
            $runners = $odd['runners'];
            $matches[$mkey]['odds'] = $runners;
            $matches[$mkey]['status'] = $odd['inplay'];
        }
        $data['matches'] = $matches;
        $this->load->view('users/index',$data);
        $this->load->view('layout/user_footer');
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

    public function matches() {
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        foreach ($matches as $mkey => $m) {
            $odd = $this->matchOdd($m['market_id']);
            $runners = $odd['runners'];
            $matches[$mkey]['odds'] = $runners;
            $matches[$mkey]['status'] = $odd['inplay'];
        }
        //print_r($matches);die;
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $data['matches'] = $matches;
        $data['info'] = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
        //$data['running'] = $this->Common_model->get_data_by_query("select event_id from running_matches where match_result = 'running' and admin_enable = 'yes'");
        $this->load->view('layout/user_header', $hdata);
        $this->load->view('users/matches', $data);
        $this->load->view('layout/user_footer');
    }

    public function getMatches() {
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        foreach ($matches as $mkey => $m) {
            $odd = $this->matchOdd($m['market_id']);
            $runners = $odd['runners'];
            $matches[$mkey]['odds'] = $runners;
            $matches[$mkey]['status'] = $odd['inplay'];
        }
        $data = '<table id="" class="table table-bordered table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th colspan="5">Cricket</th>
                </tr>
            </thead>
            <tbody>';
        $i = 1; foreach ($matches as $mkey => $m) {
            if($m['status'] == 1 || $m['status'] == true) {
                $mst = 'In Play';
            } else {
                $mst = '';
            }
            $data .= '<tr>
                          <td>'.$i++.'</td>
                          <td><a href="'.base_url('User/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']).'">'.$m['event_name'].'</a></td>
                          <td><a href="'.base_url('User/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']).'">'.$mst.'</a></td>
                          <td>'.date('D d-M-Y H:i:sa',strtotime($m['start_date'])).'</td>
                          <td>';
                            foreach($m['odds'] as $r):
                              $data .= '<a href="'.base_url('User/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']).'" class="btn btn-info" style="color: white;">
                                '.$r['ex']['availableToBack'][0]['price'].'
                              </a>
                              <a href="'.base_url('User/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']).'" class="btn btn-danger" style="color: white;">
                                '.$r['ex']['availableToLay'][0]['price'].'
                              </a>';
                            endforeach;
                          $data .= '</td>
                        </tr>';
        }
        $data .= '</tbody>
        </table>';
        echo $data;
    }

    public function inPlay() {
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        foreach ($matches as $mkey => $m) {
            $odd = $this->matchOdd($m['market_id']);
            $runners = $odd['runners'];
            $matches[$mkey]['odds'] = $runners;
            $matches[$mkey]['status'] = $odd['inplay'];
        }
        //print_r($matches);die;
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $data['matches'] = $matches;
        $data['info'] = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
        //$data['running'] = $this->Common_model->get_data_by_query("select event_id from running_matches where match_result = 'running' and admin_enable = 'yes'");
        $this->load->view('layout/user_header', $hdata);
        $this->load->view('users/in_play', $data);
        $this->load->view('layout/user_footer');
    }

    public function getInPlay() {
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        foreach ($matches as $mkey => $m) {
            $odd = $this->matchOdd($m['market_id']);
            $runners = $odd['runners'];
            $matches[$mkey]['odds'] = $runners;
            $matches[$mkey]['status'] = $odd['inplay'];
        }
        $data = '<table id="" class="table table-bordered table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th colspan="5">Cricket</th>
                </tr>
            </thead>
            <tbody>';
        $i = 1; foreach ($matches as $mkey => $m) {
            if($m['status'] == 1 || $m['status'] == true) {
                $mst = 'In Play';
            
            $data .= '<tr>
                          <td>'.$i++.'</td>
                          <td><a href="'.base_url('User/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']).'">'.$m['event_name'].'</a></td>
                          <td><a href="'.base_url('User/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']).'">'.$mst.'</a></td>
                          <td>'.date('D d-M-Y H:i:sa',strtotime($m['start_date'])).'</td>
                          <td>';
                            foreach($m['odds'] as $r):
                              $data .= '<a href="'.base_url('User/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']).'" class="btn btn-info" style="color: white;">
                                '.$r['ex']['availableToBack'][0]['price'].'
                              </a>
                              <a href="'.base_url('User/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']).'" class="btn btn-danger" style="color: white;">
                                '.$r['ex']['availableToLay'][0]['price'].'
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

    public function match() {
        $eid = $this->input->get('match_id');
        $mid = $this->input->get('market_id');
        $data['match'] = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $data['odds'] = $this->matchOdd($mid);
        $data['dfancy'] = $this->Common_model->get_data_by_query("select * from fancy_data where market_id = '$mid' and status='playing'");
        $data['fancy'] = $this->fancyData($mid);
        $data['ubets']= $this->Common_model->get_data_by_query("select * from bet where user_id = $this->id and market_id = '$mid' and bet_type = 'unmatched' and status = 'pending'");
        $data['mbets']= $this->Common_model->get_data_by_query("select * from bet where user_id = $this->id and market_id = '$mid' and bet_type = 'matched' and status = 'pending'");
        $data['fbets']= $this->Common_model->get_data_by_query("select * from bet where user_id = $this->id and market_id = '$mid' and bet_type = 'fancy' and status = 'pending'");

        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $data['info'] = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
        $data['chipSetting'] = $hdata['chipSetting'];
        $data['chips'] = $this->chips;
        $this->load->view('layout/user_header', $hdata);
        $this->load->view('users/match', $data);
        $this->load->view('layout/user_footer');
    }

    public function matchData($mkid) {
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mkid'");
        $teams = json_decode($match->teams,true);
        $odds = $this->matchOdd($mkid);
        $datas = '<table class="table table-bordered table-condensed" width="100%" >
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
        foreach($runners as $rk => $r) {
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
            $bets = $this->Common_model->get_data_by_query("select * from bet where market_id = '$mkid' and team_id = $rid and bet_type = 'unmatched'");
            if($bets) {
                foreach ($bets as $bkey => $b) {
                    if($b['back_lay'] == 'lay') {
                        if($lprice <= $b['odd']) {
                            $bbtype = 'matched';
                            $ubbdata = array('odd' => $lprice, 'bet_type' => $bbtype, 'updated_at' => date('Y-m-d H:i:s'));
                            $this->Crud_model->edit_record('bet',$b['id'],$ubbdata);
                            //$this->updateBalance($b['match_id'],$b['market_id'],$b['user_id'],$b['id']);
                            //$this->updateUserBalance($b['id']);
                            //$this->userFinalBalance();
                        }
                    } else {
                        if($bprice >= $b['odd']) {
                            $bbtype = 'matched';
                            $ubbdata = array('odd' => $bprice, 'bet_type' => $bbtype, 'updated_at' => date('Y-m-d H:i:s'));
                            $this->Crud_model->edit_record('bet',$b['id'],$ubbdata);
                            //$this->updateBalance($b['match_id'],$b['market_id'],$b['user_id'],$b['id']);
                            //$this->updateUserBalance($b['id']);
                            //$this->userFinalBalance();
                        }
                    }
                }
            }
            $this->userFinalBalance();
            $backy = "getBackLay('back','$bprice','$rname','$rid','$rk','matched')";
            $layy = "getBackLay('lay','$lprice','$rname','$rid','$rk','matched')";
            $datas .= '<tr>
                    <td><b>'.$rname.'</b><span class="pull-right" id="team'.$rk.'"></span></td>
                    <td><center><b>'.$back[2]['price'].'</b><br>'.$back[2]['size'].'</center></td>
                    <td><center><b>'.$back[1]['price'].'</b><br/>'.$back[1]['size'].'</center></td>';
            $datas .= '<td style="background: #b5e0ff; cursor: pointer;" onclick="'.$backy.'"><center><b>'.$back[0]['price'].'</b><br/>'.$back[0]['size'].'</center></td>
                    <td style="background: #ffbfcd; cursor: pointer;" onclick="'.$layy.'"><center><b>'.$lay[0]['price'].'</b><br/>'.$lay[0]['size'].'</center></td>';
            $datas .= '<td><center><b>'.$lay[1]['price'].'</b><br/>'.$lay[1]['size'].'</center></td>
                    <td><center><b>'.$lay[2]['price'].'</b><br/>'.$lay[2]['size'].'</center></td>
                </tr>';
        }
        $datas .= '</table>';
        return $datas;
    }

    public function matchReload() {
        $mid = $this->input->get('market_id');
        $selectedKey = $this->input->get('selectedKey');
        $selectedType = $this->input->get('selectedType');
        $mdata = $this->matchData($mid);
        $odds = $this->matchOdd($mid);
        $runners = $odds['runners'];
        if($selectedType == 'back') {
            $price = $runners[$selectedKey]['ex']['availableToBack'][0]['price'];
        } else {
            $price = $runners[$selectedKey]['ex']['availableToLay'][0]['price'];
        }
        $data = array('mdata' => $mdata, 'currentPrice' => $price);
        echo json_encode($data);
    }

    public function fancyPrint() {
        $mid = $this->input->get('market_id');
        $fancy = $this->fancyData($mid);
        echo json_encode($fancy);
    }

    public function fancyReload() {
        $mid = $this->input->get('market_id');
        $fid = $this->input->get('fancy_id');
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mid'");
        $dfancy = $this->Common_model->get_data_by_query("select * from fancy_data where market_id = '$mid' and status='playing'");
        $fancy = $this->fancyData($mid);
        $did = array();
        foreach ($dfancy as $dkey => $d) {
          $did[] = $d['fancy_id'];
        }
        $fancies = $fancy['session'];
        $datas = '<table class="table table-bordered" width="100%">
                        <tr>
                          <th style="border: none !important;" width="63%"></th>
                          <th style="background: red; color: white; border: none !important; min-width: 50px; max-width: 50px;"><center>NO(L)</center></th>
                          <th style="background: #2c5ca9; color: white; border: none !important; min-width: 50px;  max-width: 50px;"><center>YES(B)</center></th>
                          <th width="17%"></th>
                        </tr>';
        $show = 'yes';
        foreach ($fancies as $fkey => $f) {
            if(in_array($f['SelectionId'], $did)) {
                $BackSize1 = $f['BackSize1'];
                $fid = $f['SelectionId'];
                $fname = $f['RunnerName'];
                $BackPrice1 = $f['BackPrice1'];
                $LaySize1 = $f['LaySize1'];
                $LayPrice1 = $f['LayPrice1'];
                $backy = "getBackLay('back','$BackSize1','$fname','$fid','$fkey','fancy','$BackPrice1')";
                $layy = "getBackLay('lay','$LaySize1','$fname','$fid','$fkey','fancy','$LayPrice1')";
                $datas .= '<tr>
                        <td>'.$f['RunnerName'].'</td>
                        <td style="background-color: #ffbfcd; cursor: pointer; text-align: center;" onclick="'.$layy.'"><b>'.$f['LayPrice1'].'</b><br>'.$f['LaySize1'].'</td>
                        <td style="background-color: #b5e0ff; cursor: pointer; text-align: center;" onclick="'.$backy.'"><b>'.$f['BackPrice1'].'</b><br>'.$f['BackSize1'].'</td>
                        <td></td>
                      </tr>';
                if($f['SelectionId'] == $fid) {
                    $show = is_numeric($f['LayPrice1']) ? 'yes' : 'no';
                }
            }
        }
        $datas .= '</table>';
        $result = array(
            'fancy' => $datas,
            'show'  => $show
        );
        echo json_encode($result);
    }

    public function scoreReload() {
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
        echo $scoreData;
    }

    public function profitNLoss() {
        $mid = $this->input->get('market_id');
        $ateams = $this->Common_model->findfield('running_matches','market_id',$mid,'teams');
        $teams = json_decode($ateams);
        $team1 = $teams[0]->id;
        $team2 = $teams[1]->id;
        
        $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$mid' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
        $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$mid' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
        $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$mid' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
        $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$mid' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");

        $team1win = $team1backprofitloss->p + $team2layprofitloss->p - $team2backprofitloss->l - $team1layprofitloss->l;
        $team2win = $team2backprofitloss->p + $team1layprofitloss->p - $team1backprofitloss->l - $team2layprofitloss->l;
        $team1status = $team1win >=0 ? 'p' : 'l';
        $team2status = $team2win >=0 ? 'p' : 'l';
        $data = array('team1pl' => $team1win , 'team2pl' => $team2win, 'team1status' => $team1status, 'team2status' => $team2status);
        echo json_encode($data);
    }

    public function placeBet() {
        if($this->lockBetting == 'yes') {
            $message = array('message' => 'bet could not be placed');
            echo json_encode($message);
        } else {
            $mid = $this->input->post('match_id');
            $ateam = $this->Common_model->get_single_query("select * from cron_data where event_id = $mid");
            $mkid = $this->input->post('market_id');
            $tid = $this->input->post('team_id');
            $back_lay = $this->input->post('back_lay');
            $stake = $this->input->post('stake');
            $btype = $this->input->post('bet_type');
            $change = $this->input->post('change');
            $chips = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
            if($btype == 'fancy') {
                $fancy = $this->fancyData($mkid);
                $fancies = $fancy['session'];
                foreach ($fancies as $fk => $f) {
                   if($f['SelectionId'] == $tid) {
                    $odd    = $back_lay == 'back' ? $f['BackPrice1'] : $f['LayPrice1'];
                    $line   = $back_lay == 'back' ? $f['BackSize1'] : $f['LaySize1'];
                    $profit = $back_lay == 'back' ? ($line*$stake)/100 : $stake;
                    $loss   = $back_lay == 'back' ? $stake : ($line*$stake)/100;
                   }
                   if($odd > 0 && is_numeric($odd)) {
                    $place  = 'yes';
                   } else {
                    $place  = 'no';
                   }
                   
                }
            } else {
                if($change == 'yes') {
                    $btype = 'unmatched';
                    $odd = $this->input->post('odd');
                } else {
                    $Modds = $this->matchOdd($mkid);
                    $runners = $Modds['runners'];
                    foreach($runners as $rk => $r) {
                        if($r['selectionId'] == $tid) {
                            if($back_lay == 'back') {
                                $rodd = $r['ex']['availableToBack'][0]['price'];
                                if($rodd >= $this->input->post('odd')) {
                                    $btype = $this->input->post('bet_type');
                                    $odd    = $rodd;
                                    $profit = $rodd*$stake - $stake;
                                    $loss = $stake;
                                } else {
                                    $btype = 'unmatched';
                                    $odd    = $this->input->post('odd');
                                    $profit = $this->input->post('profit');
                                    $loss   = $this->input->post('loss');
                                }
                            } else {
                               $rodd = $r['ex']['availableToLay'][0]['price'];
                                if($rodd <= $this->input->post('odd')) {
                                    $btype = $this->input->post('bet_type');
                                    $odd = $rodd;
                                    $profit = $stake;
                                    $loss = $rodd*$stake - $stake;
                                } else {
                                    $btype = 'unmatched';
                                    $odd    = $this->input->post('odd');
                                    $profit = $this->input->post('profit');
                                    $loss   = $this->input->post('loss');
                                } 
                            }
                        }
                    }
                }
                $line   = '';
                $place  = 'yes';
            }
            if($btype == 'unmatched') {
                if ($stake > $chips->balanced_chips) {
                    $place = 'no';
                }
            } 
            if($place == 'yes') {
                $data = array(
                    'user_id'       => $this->id,
                    'market_id'     => $mkid,
                    'match_id'      => $mid,
                    'match_name'    => $this->input->post('match_name'),
                    'team'          => $this->input->post('team'),
                    'team_id'       => $this->input->post('team_id'), 
                    'market'        => $this->input->post('market'),
                    'back_lay'      => $back_lay,
                    'odd'           => $odd,
                    'stake'         => $stake,
                    'profit'        => $profit,
                    'loss'          => $loss,
                    'status'        => 'pending',
                    'bet_type'      => $btype,
                    'ip'            => $this->input->ip_address(),
                    'all_teams'     => $ateam->teams,
                    'line'          => $line,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                );
                $this->Crud_model->insert_record('bet',$data);
                $id = $this->db->insert_id();
                
                $schips = $chips->spent_chips + $this->input->post('stake');
                if($btype == 'unmatched') {
                    $bchips = $chips->balanced_chips - $stake;
                    $cchips = $chips->current_chips - $stake;

                } else {
                    $fancyFinal = 0; $tt1w = 0; $tt2w = 0;
                    $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'fancy' and status='pending'");
                    foreach($fids as $fk => $ff) {
                        $mkids[] = $ff['market_id'];
                    }
                    $unkids = array_unique($mkids);
                    foreach ($unkids as $mk => $mv) {
                        $fbets = $this->Common_model->get_data_by_query("SELECT DISTINCT(team) FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND bet_type = 'fancy' AND status='pending'");
                        foreach($fbets as $fb => $ft) {
                            $ftid = $ft['team'];
                            $fbl = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$ftid'");
                            $fll = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$ftid'");
                            $fancyFinal += abs($fbl->l - $fll->l);
                        }
                    }
                    $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'matched' and status='pending'");
                    foreach($oids as $ok => $of) {
                        $okids[] = $of['market_id'];
                    }
                    $onkids = array_unique($okids);
                    foreach ($onkids as $onk => $ov) {
                        $ateam = $this->Common_model->get_single_query("select * from cron_data where market_id = '$ov'");
                        $teams = json_decode($ateam->teams);
                        $team1 = $teams[0]->id;
                        $team2 = $teams[1]->id;
                        $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
                        $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
                        $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
                        $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
                        $team1win = $team1backprofitloss->p + $team2layprofitloss->p - $team2backprofitloss->l - $team1layprofitloss->l;
                        $team2win = $team2backprofitloss->p + $team1layprofitloss->p - $team1backprofitloss->l - $team2layprofitloss->l;
                        if($team1win < 0 && $team2win < 0) {
                            $t1w = $team1win > $team2win ? $team1win : $team2win;
                            $t2w = 0;
                        } else {
                            $t1w = $team1win >= 0 ? 0 : $team1win;
                            $t2w = $team2win >= 0 ? 0 : $team2win;
                        }
                        $tt1w += abs($t1w);
                        $tt2w += abs($t2w);
                    }
                    $bchips = $chips->current_chips - $tt1w - $tt2w - $fancyFinal;
                    $cchips = $chips->current_chips;
                }
                $bcdata = array(
                    'balanced_chips'    => $bchips,
                    'current_chips'     => $cchips,
                    'spent_chips'       => $schips,
                    'updated_at'        => date('Y-m-d H:i:sa')
                );
                $this->Crud_model->edit_record('user_chips',$chips->id,$bcdata);
                if($btype == 'matched') {
                    $message = array('message' => 'bet placed successfully');
                } elseif($btype == 'unmatched') {
                    $message = array('message' => 'unmatched bet placed successfully');
                } else {
                    $message = array('message' => 'fancy bet placed successfully');
                }
                echo json_encode($message);
            } else {
                $message = array('message' => 'bet could not be placed');
                echo json_encode($message);
            }
        }
        
    }

    public function userFinalBalance() {
        $chips = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
        $fancyFinal = 0; $tt1w = 0; $tt2w = 0; $untotal = 0;
        $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'fancy' and status='pending'");
        foreach($fids as $fk => $ff) {
            $mkids[] = $ff['market_id'];
        }
        $unkids = array_unique($mkids);
        foreach ($unkids as $mk => $mv) {
            $fbets = $this->Common_model->get_data_by_query("SELECT DISTINCT(team) FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND bet_type = 'fancy' AND status='pending'");
            foreach($fbets as $fb => $ft) {
                $ftid = $ft['team'];
                $fbl = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$ftid'");
                $fll = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$ftid'");
                $fancyFinal += abs($fbl->l - $fll->l);
            }
        }
        $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'matched' and status='pending'");
        foreach($oids as $ok => $of) {
            $okids[] = $of['market_id'];
        }
        $onkids = array_unique($okids);
        foreach ($onkids as $onk => $ov) {
            $unmatched = $this->Common_model->get_single_query("SELECT SUM(stake) AS s FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND bet_type = 'unmatched'");
            $untotal += $unmatched->s;
            $ateam = $this->Common_model->get_single_query("select * from cron_data where market_id = '$ov'");
            $teams = json_decode($ateam->teams);
            $team1 = $teams[0]->id;
            $team2 = $teams[1]->id;
            $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
            $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
            $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
            $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
            $team1win = $team1backprofitloss->p + $team2layprofitloss->p - $team2backprofitloss->l - $team1layprofitloss->l;
            $team2win = $team2backprofitloss->p + $team1layprofitloss->p - $team1backprofitloss->l - $team2layprofitloss->l;
            if($team1win < 0 && $team2win < 0) {
                $t1w = $team1win > $team2win ? $team1win : $team2win;
                $t2w = 0;
            } else {
                $t1w = $team1win >= 0 ? 0 : $team1win;
                $t2w = $team2win >= 0 ? 0 : $team2win;
            }
            $tt1w += abs($t1w);
            $tt2w += abs($t2w);
        }
        $ubal = $this->Common_model->get_single_query("SELECT SUM(credits) as c, SUM(debits) as d FROM credits_debits WHERE user_id = $this->id");
        $ubalance = $ubal->c - $ubal->d;
        $bchips = $ubalance - $tt1w - $tt2w - $fancyFinal - $untotal;
        $cchips = $ubalance - $untotal;
        $bcdata = array(
            'balanced_chips'    => $bchips,
            'current_chips'     => $cchips,
            'updated_at'        => date('Y-m-d H:i:sa')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips',$this->id,$bcdata,'user_id');
    }

    public function updateUserBalance($bid) {
        $chips = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
        $bet = $this->Common_model->get_single_query("select * from bet where id = $bid");
        $fancyFinal = 0; $tt1w = 0; $tt2w = 0;
        $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'fancy' and status='pending'");
        foreach($fids as $fk => $ff) {
            $mkids[] = $ff['market_id'];
        }
        $unkids = array_unique($mkids);
        foreach ($unkids as $mk => $mv) {
            $fbets = $this->Common_model->get_data_by_query("SELECT DISTINCT(team) FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND bet_type = 'fancy' AND status='pending'");
            foreach($fbets as $fb => $ft) {
                $ftid = $ft['team'];
                $fbl = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$ftid'");
                $fll = $this->Common_model->get_single_query("SELECT SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$ftid'");
                $fancyFinal += abs($fbl->l - $fll->l);
            }
        }
        $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'matched' and status='pending'");
        foreach($oids as $ok => $of) {
            $okids[] = $of['market_id'];
        }
        $onkids = array_unique($okids);
        foreach ($onkids as $onk => $ov) {
            $ateam = $this->Common_model->get_single_query("select * from cron_data where market_id = '$ov'");
            $teams = json_decode($ateam->teams);
            $team1 = $teams[0]->id;
            $team2 = $teams[1]->id;
            $team1backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'back' AND bet_type = 'matched'");
            $team1layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team1 AND back_lay = 'lay' AND bet_type = 'matched'");
            $team2backprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'back' AND bet_type = 'matched'");
            $team2layprofitloss = $this->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $this->id AND market_id = '$ov' AND team_id = $team2 AND back_lay = 'lay' AND bet_type = 'matched'");
            $team1win = $team1backprofitloss->p + $team2layprofitloss->p - $team2backprofitloss->l - $team1layprofitloss->l;
            $team2win = $team2backprofitloss->p + $team1layprofitloss->p - $team1backprofitloss->l - $team2layprofitloss->l;
            $t1w = $team1win >= 0 ? 0 : $team1win;
            $tt1w += abs($t1w);
            $t2w = $team2win >= 0 ? 0 : $team2win;
            $tt2w += abs($t2w);
        }
        $bchips = $chips->current_chips - $tt1w - $tt2w - $fancyFinal +$bet->stake;
        $cchips = $chips->current_chips + $bet->stake;
        $bcdata = array(
            'balanced_chips'    => $bchips,
            'current_chips'     => $cchips,
            'updated_at'        => date('Y-m-d H:i:sa')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips',$this->id,$bcdata,'user_id');
    }

    public function updateBalance($mid,$mkid,$uid,$bid) {
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
        $stake = $this->Common_model->findfield('bet','id',$bid,'stake');
        $chips = $this->Common_model->get_single_query("select * from user_chips where user_id = $uid");
        $cchips = $chips->current_chips + $stake;
        $bchips = $cchips - $t1w - $t2w;
        $bcdata = array(
            'balanced_chips'    => $bchips,
            'current_chips'     => $cchips,
            'updated_at'        => date('Y-m-d H:i:sa')
        );
        $this->Crud_model->edit_record_by_anyid('user_chips',$uid,$bcdata,'user_id');
    }

    public function betReload() {
        $mid = $this->input->get('market_id');
        $ubets = $this->Common_model->get_data_by_query("select * from bet where user_id = $this->id and market_id = '$mid' and bet_type = 'unmatched' and status = 'pending'");
        $mbets = $this->Common_model->get_data_by_query("select * from bet where user_id = $this->id and market_id = '$mid' and bet_type = 'matched' and status = 'pending'");
        $fbets = $this->Common_model->get_data_by_query("select * from bet where user_id = $this->id and market_id = '$mid' and bet_type = 'fancy' and status = 'pending'");
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
                        $delM = "deleteUnmatched('".$ub['id']."')";
                         echo '<tr class="'.$class.'">
                            <td><a href="javascript:void(0)" onclick="'.$delM.'"><i class="fa fa-trash-o"></i></a>&nbsp;&nbsp;'.$ub['team'].'</td>
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

    public function deleteUnmatched() {
        $bid = $this->input->get('bet_id');
        $bet = $this->Common_model->get_single_query("select * from bet where id = $bid");
        $stake = $bet->stake;
        $uid = $bet->user_id;
        $this->Crud_model->delete_record('bet',$bid);
        $chips = $this->Common_model->get_single_query("select * from user_chips where user_id = $uid");
        $data = array('balanced_chips' => $chips->balanced_chips + $stake, 'current_chips' => $chips->current_chips + $stake, 'updated_at' => date('Y-m-d H:i:sa'));
        $this->Crud_model->edit_record('user_chips',$chips->id,$data);
        return true;
    }

    public function updateMainBalance() {
        $this->userFinalBalance();
        $chips = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
        $balance = $chips->balanced_chips;
        $s1 = '<a href="javascript:void(0)">Balance: '.$balance.'</a>';
        $s2 = $chips->current_chips;
        $msg = array('msg' => $s1, 'curr' => $s2);
        echo json_encode($msg);
    }

    public function editStake() {
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
        $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Chip Setting updated successfully</div>");
        redirect('User/index');
    }

    public function accountInfo() {
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $data['info'] = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
        $data['up'] = $this->Common_model->get_single_query("SELECT SUM(profit) as up FROM `profit_loss` WHERE user_id = $this->id");
        $data['down'] = $this->Common_model->get_single_query("SELECT SUM(loss) as down FROM `profit_loss` WHERE user_id = $this->id");
        $this->load->view('layout/user_header', $hdata);
        $this->load->view('users/account_info', $data);
        $this->load->view('layout/user_footer');
    }

    public function accountStatement() {
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $data['statements'] = $this->Common_model->get_data_by_query("select * from credits_debits where user_id = $this->id order by id DESC");
        $this->load->view('layout/user_header', $hdata);
        $this->load->view('users/account_statement',$data);
        $this->load->view('layout/user_footer');
    }

    public function chipHistory() {
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $data['history'] = $this->Common_model->get_data_by_query("select * from profit_loss where user_id = $this->id order by id DESC");
        $this->load->view('layout/user_header', $hdata);
        $this->load->view('users/chip_history', $data);
        $this->load->view('layout/user_footer');
    }

    public function profitLoss() {
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $data['profitLosses'] = $this->Common_model->get_data_by_query("select * from profit_loss where user_id = $this->id");
        $this->load->view('layout/user_header', $hdata);
        $this->load->view('users/profit_loss',$data);
        $this->load->view('layout/user_footer');
    }

    public function betHistory() {
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $data['bets'] = $this->Common_model->get_data_by_query("select * from bet where user_id = $this->id order by id DESC");
        $this->load->view('layout/user_header', $hdata);
        $this->load->view('users/bet_history',$data);
        $this->load->view('layout/user_footer');
    }

    public function bet() {
        $id = $this->input->get('bet_id');
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $data['bet'] = $this->Common_model->get_single_query("select * from bet where id = $id");
        $this->load->view('layout/user_header', $hdata);
        $this->load->view('users/bet',$data);
        $this->load->view('layout/user_footer');
    }

    public function changePassword() {
        $hdata['chipSetting'] = $this->chipSetting;
        $hdata['chips'] = $this->chips;
        $hdata['p_l'] = $this->p_l;
        $this->load->view('layout/user_header', $hdata);
        $this->load->view('users/change_password');
        $this->load->view('layout/user_footer');
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
            redirect('User/changePassword');
        } else {
            $identity = $this->session->userdata('identity');

            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

            if ($change) {
                //if the password was successfully changed
                $this->session->set_flashdata('message', "<div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" . $this->ion_auth->messages() . "</div>");
                redirect('Auth/logout');
            } else {
                $this->session->set_flashdata('message', "<div class='alert alert-error alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" . $this->ion_auth->errors() . "</div>");
                redirect('User/changePassword', 'refresh');
            }
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