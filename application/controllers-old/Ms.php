<?php

class Ms extends MY_Controller {

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

    public function test() {
        $pmkid = "1.158849218";
        $q = "SELECT DISTINCT(market_id) market_id FROM bet WHERE user_id = $this->id AND bet_type = 'fancy' and status='settled' AND market_id = '$pmkid'";
        // echo $q;
        $fids = $this->Common_model->get_data_by_query($q);

        $mkids = array();
        foreach ($fids as $fk => $ff) {
            $mkids[] = $ff['market_id'];
        }
        $unkids = array_unique($mkids);

        $mv_array = $this->Ms_model->distinctTeamCsv($this->id, $unkids);
        $mv_csv = $val = "'" . implode("','", $mv_array) . "'";
        $fbl = $this->Ms_model->ReadRaw("SELECT * FROM bet WHERE user_id = $this->id AND market_id = '$pmkid' AND back_lay = 'back' AND bet_type = 'fancy' AND team in  ($mv_csv) ");
        $outlist_fbl = array();
        foreach ($fbl as $fk => $fv) {
            $outlist_fbl[$fv->market_id][$fv->team][] = $fv;
        }
        print_r(json_encode($outlist_fbl));

        $fbly = $this->Ms_model->ReadRaw("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$pmkid' AND back_lay = 'back' AND bet_type = 'fancy' AND team in  ($mv_csv)");

        //print_r($fbly);
    }

    public function checkUserMaxLimit($pmkid) {
        $chips = $this->Common_model->get_single_query("select * from user_chips where user_id = $this->id");
        $fancyFinal = 0;
        $tt1w = 0;
        $tt2w = 0;
        $untotal = 0;
        $back_lay_type = 'back';
        /////////////////////////////////////////////////
        //$pmkid = "1.158849218";
        $q = "SELECT DISTINCT(market_id) market_id FROM bet WHERE user_id = $this->id AND bet_type = 'fancy' and status='pending' AND market_id = '$pmkid'";
        // echo $q;
        $fids = $this->Common_model->get_data_by_query($q);

        $mkids = array();
        foreach ($fids as $fk => $ff) {
            $mkids[] = $ff['market_id'];
        }
        $unkids = array_unique($mkids);

        $mv_array = $this->Ms_model->distinctTeamCsv($this->id, $unkids);
        $mv_csv = $val = "'" . implode("','", $mv_array) . "'";
        $fbly = $this->Ms_model->ReadRaw("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$pmkid' AND back_lay = 'back' AND bet_type = 'fancy' AND team in  ($mv_csv)");

        $fbl = $this->Ms_model->ReadRaw("SELECT * FROM bet WHERE user_id = $this->id AND market_id = '$pmkid'  AND bet_type = 'fancy' AND team in  ($mv_csv) ");
        $outlist_fbl = array();
        foreach ($fbl as $fk => $fv) {
            $outlist_fbl[$fv->market_id][$fv->team][] = $fv;
        }


        //here
        foreach ($outlist_fbl as $market_id => $array_of_array) {

            foreach ($array_of_array as $team => $array) {
                $used = array();
                for ($i = 0; $i < sizeof($array); $i++) {
                    $record = $array[$i];
                    if ($record->back_lay != $back_lay_type) {
                        continue;
                    }
                    $back_lay = $record->back_lay;
                    $odd = $record->odd;
                    $total_plus = 0;
                    $total_minus = 0;

                    for ($j = 0; $j < sizeof($array); $j++) {
                        $a = $array[$j];
                        if ($a->back_lay == $back_lay_type) {
                            continue;
                        }
                        //echo "<hr />" . $a->odd . " = " . $odd;
                        if ($a->odd > $odd) {
                            if (!in_array($a->id, $used)) {
                                $used[] = $a->id;
                                $total_plus += $a->loss;
                            }
                        } else {
                            if (!in_array($a->id, $used)) {
                                $used[] = $a->id;
                                $total_minus += $a->loss;
                            }
                        }
                    }//innermost
                    $record->total_plus = $total_plus;
                    $record->total_minus = $total_minus;
                }//inner-3                
            }//outer-2
        }//outer-1
        print_r(json_encode($outlist_fbl));
        exit();
        ////////////////////////////////////////////////////////


        $fids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'fancy' and status='pending' AND market_id != '$pmkid'");
        foreach ($fids as $fk => $ff) {
            $mkids[] = $ff['market_id'];
        }
        $unkids = array_unique($mkids);
        foreach ($unkids as $mk => $mv) {
            $fbets = $this->Common_model->get_data_by_query("SELECT DISTINCT(team) FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND bet_type = 'fancy' AND status='pending'");
            $sortFancy = array();
            foreach ($fbets as $fb => $ft) {
                $ftid = $ft['team'];
                // $fbids = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND bet_type = 'fancy' AND team = '$ftid'");
                // foreach ($fbids as $fbk => $fbv) {
                // }
                $fbl = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$ftid'");
                $fbly = $this->Common_model->get_single_query("SELECT SUM(loss) as l FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND back_lay = 'back' AND bet_type = 'fancy' AND team = '$ftid'");

                //$fll = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$ftid'");
                $fllyIds = array();
                $fllnIds = array();
                $fllMinus = 0;
                $fllPlus = 0;
                foreach ($fbl as $flk => $flv) {
                    $fline = $flv['odd'];
                    $flly = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$ftid' AND odd >= $fline ");
                    foreach ($flly as $fv) {
                        if (!in_array($fv['id'], $fllyIds)) {
                            array_push($fllyIds, $fv['id']);
                            $fllMinus += $fv['loss'];
                        }
                    }
                    $flln = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE user_id = $this->id AND market_id = '$mv' AND back_lay = 'lay' AND bet_type = 'fancy' AND team = '$ftid' AND odd < $fline ");
                    foreach ($flln as $fn) {
                        if (!in_array($fn['id'], $fllnIds)) {
                            array_push($fllnIds, $fn['id']);
                            $fllPlus += $fn['loss'];
                        }
                    }
                }
                $fancyFinal += ($fbly->l - $fllMinus + $fllPlus);
                unset($fllyIds);
                unset($fllnIds);
            }//ff
        }
        ////
        //matched
        $oids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'matched' and status='pending' AND market_id != '$pmkid'");
        foreach ($oids as $ok => $of) {
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
        //unmatched part
        $unmids = $this->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $this->id AND bet_type = 'unmatched' and status='pending' AND market_id != '$pmkid'");
        foreach ($unmids as $unk => $unm) {
            $unmkids[] = $unm['market_id'];
        }
        $unmmids = array_unique($unmkids);
        foreach ($unmmids as $unmk => $uv) {
            $unmatchedBets = $this->Common_model->get_single_query("SELECT SUM(stake) AS s FROM bet WHERE user_id = $this->id AND market_id = '$uv' AND bet_type = 'unmatched'");
            $untotal += $unmatchedBets->s;
        }
        $ubal = $this->Common_model->get_single_query("SELECT SUM(credits) as c, SUM(debits) as d FROM credits_debits WHERE user_id = $this->id");
        $ubalance = $ubal->c - $ubal->d - $untotal;
        $bchips = $ubalance - $tt1w - $tt2w - $fancyFinal - $untotal;
        echo $bchips;
    }

}
