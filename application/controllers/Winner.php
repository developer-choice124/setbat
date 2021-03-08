<?php

class Winner extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        date_default_timezone_set("Asia/Kolkata");
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
        if (!$this->ion_auth->logged_in()) {
            redirect('MsAuth/login');
        }
        if (!$this->ion_auth->is_user()) {
            redirect('Auth');
        }
        $this->id = $this->session->userdata('user_id');
        $this->panel = $this->Common_model->get_single_query("SELECT * FROM panel_title ORDER BY id DESC");
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
        $data['match'] = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mkid'");
        $data['odds'] = $this->match->matchOddByMarketId($mkid);
        $this->calculateFinalBalance();
        //print_r($data['odds']);die;
        $this->load->view('msappuser/winner', $data);
        $this->load->view('msappuser/footer');
    }

    public function callAsync()
    {
        $mkid = $this->input->get('market_id');
        $mid = $this->input->get('match_id');
        $odds = $this->oddsReload($mkid, $mid);
        echo json_encode(array(
            'oddData' => $odds
        ));
    }

    public function callFancy()
    {
        $mkid = $this->input->get('market_id');
        $mid = $this->input->get('match_id');
        $fancies = $this->fancyReload($mkid, $mid);
        echo json_encode(array(
            'fancyData' => $fancies
        ));
    }

    public function oddsReload($mkid, $mid)
    {
        $match = $this->Common_model->get_single_query("select * from running_matches where market_id = '$mkid'");
        $odds = $this->match->matchOddByMarketId($mkid);
        //$res = $this->plReload($mkid,$mid,$odds);
        // $runners = $odds[0]['teams'];
        $teams = json_decode($match->teams);
        // echo json_encode($teams);die;
        // print_r($runners);die;
        $teamIds = array();
        foreach ($teams as $tm) {
            $tm = (object) $tm;
            $teamIds[] = $tm->id;
        }
        $bprice = 0;
        $bsize = 0;
        $lprice = 0;
        $lsize = 0;
        // $data = "";
        foreach ($odds as $rk => $r) :
            $r = (object) $r;
            $bprice = $r->BackPrice1 ? $r->BackPrice1 : 0;
            $bsize = $r->BackSize1 ? $r->BackSize1 : 0;
            $lprice = $r->LayPrice1 ? $r->LayPrice1: 0;
            $lsize = $r->LaySize1 ? $r->LaySize1 : 0;

            $untid = $teams[$rk]->id;
            //unmatched check
            if (!empty($bprice)) {
                $backBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mkid' AND bet_type = 'unmatched' AND back_lay = 'back' AND team_id = $untid AND odd <= $bprice AND status = 'pending' AND user_id = $this->id");
                if ($backBets) {
                    foreach ($backBets as $bk => $b) {
                        $bdata = array(
                            'bet_type'  => 'matched',
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                        $this->Crud_model->edit_record('bet', $b['id'], $bdata);
                    }
                }
            }

            if (!empty($lprice)) {
                $layBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$mkid' AND bet_type = 'unmatched' AND back_lay = 'lay' AND team_id = $untid AND odd >= $lprice AND status = 'pending' AND user_id = $this->id");

                if ($layBets) {
                    foreach ($layBets as $lk => $l) {
                        $ldata = array(
                            'bet_type'  => 'matched',
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                        $this->Crud_model->edit_record('bet', $l['id'], $ldata);
                    }
                }
            }


            //end unmatched check
            //$class = $res[$rk]['pl'] >= 0 ? 'text-success' : 'text-danger';

            $backy = "showBackBetDiv('" . $r->SelectionId . "','" . $r->RunnerName . "','" . $rk . "','back','matched','" . $bprice . "','" . $bsize . "')";
            $layy = "showLayBetDiv('" . $r->SelectionId . "','" . $r->RunnerName . "','" . $rk . "','lay','matched','" . $lprice . "','" . $lsize . "')";
            $data .= '<div class="row">
              <div class="col-6 border">
                <span class="font-weight-bold pl-1 clearfix">' . $r->RunnerName . '</span>
                <span id="' . $r->SelectionId . '" 
                  class="pl-1 font-weight-bold "></span>
              </div>
              <div class="col-3 text-center border" id="' . $r->SelectionId . '_backParentdiv" style="background: #ffffea; cursor:pointer;">
                <div 
                data-others = "' . json_encode($teamIds) . '" id="' . $r->SelectionId . '_backdiv" onclick="' . $backy . '">
                  <span id="' . $r->SelectionId . '_backodd">
                    <center><b>' . $bprice . '</b><br/>' . $bsize . '</center>
                  </span>
                </div>
              </div>
              <div class="col-3 text-center border" id="' . $r->SelectionId . '_layParentdiv" style="background: #ffffea; cursor:pointer;">
                <div data-others = "' . json_encode($teamIds) . '" id="' . $r->SelectionId . '_laydiv" onclick="' . $layy . '">
                  <span id="' . $r->SelectionId . '_layodd">
                    <center><b>' . $lprice . '</b><br/>' . $lsize . '</center>
                  </span>
                </div>
              </div>
            </div>';

        endforeach;
        return $data;
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

    public function fancyReload($mkid, $mid)
    {
        $dfancy = $this->Common_model->get_data_by_query("SELECT * FROM fancy_data WHERE market_id = '$mkid' AND status NOT IN ('settled','paused')");
        $fancy = $this->match->matchFancies($mid);
        // start
        $data = '';
        $did = array();
        foreach ($dfancy as $dkey => $d) {
            $did[] = $d['SelectionId'];
        }
        if ($fancy) {
            foreach ($fancy as $fk => $f) {
                if (in_array($f['SelectionId'], $did)) {
                    $lprice = $f['LayPrice1'];
                    $lsize = $f['LaySize1'];
                    $bprice = $f['BackPrice1'];
                    $bsize = $f['BackSize1'];
                    if ($f['GameStatus'] == '') {
                        $yes = "showBackBetDiv('" . $f['SelectionId'] . "','" . $f['RunnerName'] . "','" . $fk . "','back','fancy','" . $bprice . "','" . $bsize . "')";
                        $no = "showLayBetDiv('" . $f['SelectionId'] . "','" . $f['RunnerName'] . "','" . $fk . "','lay','fancy','" . $lprice . "','" . $lsize . "')";
                        $showMsg = '';
                        $data .= '<div class="row">
                                <div class="col-6 border pt-2">' . $f['RunnerName'] . '</div>
                                <div class="col-3 text-center border" style="background: #ffffea; cursor:pointer;" onclick="' . $no . '">' . $showMsg . '
                                  <b>' . $f['LayPrice1'] . '</b><br/>' . $f['LaySize1'] . '</div>
                                <div class="col-3 text-center border" style="background: #ffffea;cursor:pointer;" 
                                  onclick="' . $yes . '">' . $showMsg . '
                                  <b>' . $f['BackPrice1'] . '</b><br/>' . $f['BackSize1'] . '</div>
                              </div>';
                    } else {
                        $yes = "";
                        $no = "";
                        $showMsg = '<div class="overlay">' . $f['GameStatus'] . '</div>';
                    }
                }
            }
        }
        return $data;
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
        if (empty($match_id) || $match_id == null || trim($match_id) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if (empty($market_id) || $market_id == null || trim($market_id) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if (empty($match_name) || $match_name == null || trim($match_name) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if (empty($team) || $team == null || trim($team) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if (empty($team_id) || $team_id == null || trim($team_id) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if (empty($market) || $market == null || trim($market) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if (empty($odd) || $odd == null || trim($odd) == '' || $odd <= 0) {
            $place = 'no';
            $ok = 'no';
        }
        if (empty($profit) || $profit == null || trim($profit) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if (empty($loss) || $loss == null || trim($loss) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if (empty($bet_type) || $bet_type == null || trim($bet_type) == '') {
            $place = 'no';
            $ok = 'no';
        }
        if (empty($line) || $line == null || trim($line) == '') {
            $place = 'no';
            $ok = 'no';
        }
        $lockBetting = $this->MsAppUser_model->lockBetting();
        $cuser = $this->MsAppUser_model->index();
        $Modds = $this->match->matchOddByMarketId($market_id);
        $runners = $Modds[0]['teams'];
        $allTeams = array();
        $matchDetails = $this->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = '$market_id'");
        if ($matchDetails->match_result == 'paused') {
            $place = 'no';
            $message = 'bet could not be placed as match has been paused';
            echo json_encode(array(
                'message'   => $message,
                'class'     => $place == 'yes' ? 'alert-success' : 'alert-danger',
                'bal'       => $cuser->balanced_chips
            ));
        } elseif ($matchDetails->match_result == 'settled') {
            $place = 'no';
            $message = 'bet could not be placed as match has been declared';
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
                // if ($bet_type == 'fancy') {
                //     $fancy = $this->match->matchFancies($match_id);
                //     // $fancies = $fancy['session'];
                //     $cFancy = $fancy[$team_id];
                //     if ($cFancy['GameStatus'] == '') {
                //         $frodd = $back_lay == 'back' ? $cFancy['BackPrice1'] : $cFancy['LayPrice1'];
                //         $frline = $back_lay == 'back' ? $cFancy['BackSize1'] : $cFancy['LaySize1'];
                //         $profit = $back_lay == 'back' ? ($line * $stake) / 100 : $stake;
                //         $loss = $back_lay == 'back' ? $stake : ($line * $stake) / 100;
                //         if ($odd > 0 && is_numeric($odd)) {
                //             if ($frodd != $odd || $frline != $line) {
                //                 $place = 'no';
                //                 $message = 'Fancy bet could not be placed1';
                //             } else {
                //                 $place = 'yes';
                //             }
                //         } else {
                //             $place = 'no';
                //             $message = 'Fancy bet could not be placed2';
                //         }
                //         $checkFancy = $cFancy['RunnerName'];
                //     } else {
                //         $place = 'no';
                //         $message = 'Fancy bet could not be placed3';
                //     }
                //     if (empty($checkFancy) || $checkFancy == null || strlen($checkFancy) == 0) {
                //         $place = 'no';
                //         $message = 'Fancy bet could not be placed4';
                //     }
                //     if ($place == 'yes') {
                //         $flm = 0;
                //         if ($back_lay == 'back') {
                //             $fbl = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$team' AND odd >= '$odd'");
                //             $fmp = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$team' AND odd <= '$odd'");
                //         } else {
                //             $fbl = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$team' AND odd <= '$odd'");
                //             $fmp = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$team' AND odd >= '$odd'");
                //         }
                //         if (isset($fbl) && isset($fbl->l)) {
                //             $flm = 2 * ($fbl->l - ($fmp ? $fmp->l : 0));
                //         }
                //         $actualLoss = abs($flm) + $cuser->balanced_chips;
                //         if ($loss > $actualLoss) {
                //             $place = 'no';
                //             $message = 'Fancy bet could not be placed due to insufficient balance5';
                //         }
                //     }
                // } else {
                //     foreach ($runners as $rk => $r) {
                //         if ($r->id == $team_id) {
                //             if ($back_lay == 'back') {
                //                 $rodd = $r->back['price'];
                //                 if ($rodd >= $odd) {
                //                     $odd = $rodd;
                //                     $profit = ($rodd * $stake) - $stake;
                //                     $loss = $stake;
                //                 } else {
                //                     $bet_type = 'unmatched';
                //                     $profit = ($odd * $stake) - $stake;
                //                     $loss = $stake;
                //                 }
                //             } else {
                //                 $rodd = $r->lay['price'];
                //                 if ($rodd <= $odd) {
                //                     $odd = $rodd;
                //                     $profit = $stake;
                //                     $loss = ($rodd * $stake) - $stake;
                //                 } else {
                //                     $bet_type = 'unmatched';
                //                     $profit = $stake;
                //                     $loss = ($odd * $stake) - $stake;
                //                 }
                //             }
                //         }
                //     }
                //     if ($bet_type == 'unmatched') {
                //         if ($loss > $cuser->balanced_chips) {
                //             $place = 'no';
                //             $message = 'Unmatched bet could not be placed due to insufficient balance';
                //         }
                //     }

                //     if ($bet_type == 'matched') {
                //         $limit = $this->match->maxLimitByMarketId($this->id, $market_id);
                //         $res = $this->match->calculateOddProfitLossByMarketId($this->id, $market_id);
                //         foreach ($res as $rk => $rv) {
                //             if ($back_lay == 'back') {
                //                 if ($team_id == $rv['id']) {
                //                     $res[$rk]['pl'] = $rv['pl'] + $profit;
                //                 } else {
                //                     $res[$rk]['pl'] = $rv['pl'] - $loss;
                //                 }
                //             } else {
                //                 if ($team_id == $rv['id']) {
                //                     $res[$rk]['pl'] = $rv['pl'] - $loss;
                //                 } else {
                //                     $res[$rk]['pl'] = $rv['pl'] + $profit;
                //                 }
                //             }
                //         }
                //         $numbers = array_column($res, 'pl');
                //         $max = min($numbers);
                //         $uloss = $this->match->calculateUnmatchedLoss($this->id, $market_id);
                //         $maxFinal = $max + $uloss;
                //         if (abs($maxFinal) > $limit) {
                //             $place = 'no';
                //             $message = 'Bet Can not be placed because loss is higher than balanced chips';
                //         }
                //     }
                //     if ($odd >= 4) {
                //         $place = 'no';
                //         $ok = 'no';
                //         $message = 'Bet Can not be placed as odd is above 4';
                //     }
                // }
                if ($bet_type == 'fancy') {
                    $fancy = $this->match->matchFancies($match_id);
                    // $fancies = $fancy['session'];
                    $cFancy = $fancy[$team_id];
                    if ($cFancy['GameStatus'] == '') {
                        $frodd = $back_lay == 'back' ? $cFancy['BackPrice1'] : $cFancy['LayPrice1'];
                        $frline = $back_lay == 'back' ? $cFancy['BackSize1'] : $cFancy['LaySize1'];
                        $profit = $back_lay == 'back' ? ($line * $stake) / 100 : $stake;
                        $loss = $back_lay == 'back' ? $stake : ($line * $stake) / 100;
                        if ($odd > 0 && is_numeric($odd)) {
                            if ($frodd != $odd || $frline != $line) {
                                $place = 'no';
                                $message = 'Fancy bet could not be placed1';
                            } else {
                                $place = 'yes';
                            }
                        } else {
                            $place = 'no';
                            $message = 'Fancy bet could not be placed2';
                        }
                        $checkFancy = $cFancy['RunnerName'];
                    } else {
                        $place = 'no';
                        $message = 'Fancy bet could not be placed3';
                    }
                    if (empty($checkFancy) || $checkFancy == null || strlen($checkFancy) == 0) {
                        $place = 'no';
                        $message = 'Fancy bet could not be placed4';
                    }
                    if ($place == 'yes') {
                        $flm = 0;
                        if ($back_lay == 'back') {
                            $fbl = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$team' AND odd >= '$odd'");
                            $fmp = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$team' AND odd <= '$odd'");
                        } else {
                            $fbl = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$team' AND odd <= '$odd'");
                            $fmp = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$market_id' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$team' AND odd >= '$odd'");
                        }
                        if (isset($fbl) && isset($fbl->l)) {
                            $flm = 2 * ($fbl->l) - ($fmp ? $fmp->l : 0);
                        }
                        if($flm < 0) {
                            $actualLoss = $cuser->balanced_chips;
                        } else {
                            $actualLoss = abs($flm) + $cuser->balanced_chips;
                        }

                        
                        // echo $cuser->balanced_chips; print_r($fmp); print_r($fbl); echo $actualLoss.'<br/>'.$flm;  die;
                        if ($loss > $actualLoss) {
                            $place = 'no';
                            $message = 'Fancy bet could not be placed due to insufficient balance5';
                        }
                    }
                } else {
                    foreach ($runners as $rk => $r) {
                        if ($r->id == $team_id) {
                            if ($back_lay == 'back') {
                                $rodd = $r->back['price'];
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
                                $rodd = $r->lay['price'];
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
                    if ($bet_type == 'unmatched') {
                        if ($loss > $cuser->balanced_chips) {
                            $place = 'no';
                            $message = 'Unmatched bet could not be placed due to insufficient balance';
                        }
                    }

                    if ($bet_type == 'matched') {
                        // $limit = $this->match->maxLimitByMarketId($this->id, $market_id);
                        $res = $this->match->calculateOddProfitLossByMarketId($this->id, $market_id);
                        $earlier = array_column($res, 'pl');
                        $min = min($earlier);
                        $earlierMax = 0;
                        if($min < 0) $earlierMax = $min;
                        $newLimit = $cuser->balanced_chips + abs($earlierMax);
                        foreach ($res as $rk => $rv) {
                            if ($back_lay == 'back') {
                                if ($team_id == $rv['id']) {
                                    $res[$rk]['pl'] = $rv['pl'] + $profit;
                                } else {
                                    $res[$rk]['pl'] = $rv['pl'] - $loss;
                                }
                            } else {
                                if ($team_id == $rv['id']) {
                                    $res[$rk]['pl'] = $rv['pl'] - $loss;
                                } else {
                                    $res[$rk]['pl'] = $rv['pl'] + $profit;
                                }
                            }
                        }
                        $numbers = array_column($res, 'pl');
                        $max = min($numbers);
                        if($max < 0 && (abs($max) > $newLimit)) {
                            $place = 'no';
                            $message = 'Bet Can not be placed because loss is higher than balanced chips';
                        }
                        // $uloss = $this->match->calculateUnmatchedLoss($this->id, $market_id);
                        // $maxFinal = $max + $uloss;
                        // echo $max.'===='.$uloss."=======".$limit."=============".$cuser->balanced_chips."==========".$earlierMax;
                        // print_r($res);die;
                        // if (abs($max) > $newLimit) {
                        //     $place = 'no';
                        //     $message = 'Bet Can not be placed because loss is higher than balanced chips';
                        // }
                    }
                    if ($odd >= 4) {
                        $place = 'no';
                        $ok = 'no';
                        $message = 'Bet Can not be placed as odd is above 4';
                    }
                }
                if ((is_numeric($odd) && $odd > 0) && $profit > 0 && $loss > 0 && $stake >= 100) {
                } else {
                    $ok = 'no';
                    $message = 'Bet Can not be placed';
                }
                if ($odd <= 0) {
                    $place = 'no';
                    $ok = 'no';
                    $message = 'Bet Can not be placed';
                }
                //print_r($allTeams);

                if ($place == 'yes' && $ok == 'yes') {
                    $master = $this->Common_model->get_single_query("SELECT * FROM users WHERE id = $cuser->parent_id");
                    $commission = 0;
                    if ($bet_type == "fancy") {
                        if ($master->session_commission > 0) {
                            $commission = ($stake * $master->session_commission) / 100;
                        }
                    }
                    $userCommission = 0;
                    if ($bet_type == "fancy") {
                        if ($cuser->session_commission > 0) {
                            $userCommission = ($stake * $cuser->session_commission) / 100;
                        }
                    }
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
                        'all_teams' => $matchDetails->teams,
                        'line' => $line,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                    if ($commission > 0) {
                        $data['master_commission'] = $commission;
                    }
                    if ($userCommission > 0) {
                        $data['user_commission'] = $userCommission;
                    }
                    if ($this->Crud_model->insert_record('bet', $data)) {
                        $message = $bet_type == 'fancy' ? '<strong>Fancy</strong> bet Placed successfully' : ($bet_type == 'matched' ? '<strong>Matched</strong> bet placed successfully' : '<strong>Unmatched</strong> bet placed successfully');
                    } else {
                        $message = 'Bet Cannot be placed';
                        $place = 'no';
                    }
                }
                $balLeft = $this->match->calculateProfitLossAllMatch($this->id);
                $balData = array('balanced_chips' => $balLeft);
                if ($this->Crud_model->edit_record('user_chips', $cuser->user_chips_id, $balData)) {
                    echo json_encode(array(
                        'message'   => $message,
                        'class'     => $place == 'yes' ? 'alert-success' : 'alert-danger',
                        'bal'       => round($balLeft,2)
                    ));
                }
                //$balLeft = $this->getBalance();
            }
        }
    }

    public function calculateTeamPLByMarketId()
    {
        $market_id = $this->input->get('market_id');
        $res = $this->match->calculateOddProfitLossByMarketId($this->id, $market_id);
        $numbers = array_column($res, 'pl');
        $min = min($numbers);
        //$res['final'] = $min;
        echo json_encode(array('plData' => $res, 'loss' => $min));
    }

    public function calculateFinalBalance()
    {
        $final = $this->match->calculateProfitLossAllMatch($this->id);
        $balData = array('balanced_chips' => $final);
        $cuser = $this->MsAppUser_model->index();
        $this->Crud_model->edit_record('user_chips', $cuser->user_chips_id, $balData);
        return $final;
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
