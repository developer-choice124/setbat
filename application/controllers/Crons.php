<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Crons extends MY_Controller
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
        $this->load->library('match');
    }

    public function index(){
        $crickets = $this->matches();
        
        foreach($crickets->data as $ck => $c){
            $details = $this->matchesDetails($c->EventId);
            
            $crickets->data[$ck]->detail = $details->data ? $details->data[0] : "";
        }
        $today = date('Y-m-d');
        foreach($crickets->data as $ck => $c){
            $match = $this->Common_model->get_single_query("SELECT * FROM cron_data WHERE market_id =". $c->detail->MarketId);
            if(!empty($match)) {
                
            }else{
                    $edate = date('Y-m-d H:i:s', strtotime($c->EventDate));
                    $data = array(
                        'market_id' => $c->detail->MarketId,
                        'event_id' => $c->EventId,
                        'event_name' => $c->EventName,
                        'event_date' => $edate,
                        'event_typeid' => 4,
                        'competition_id' => "",
                        'competition_name' => "",
                        'start_date' => $edate,
                        'btype' => $c->detail->MarketName,
                        'mtype' => $c->detail->MarketName,
                        'teams' => "",
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    );
                    $this->Crud_model->insert_record('cron_data', $data);
                }
        }
    }

    // public function index() {
    //     $crickets = $this->getAllMatchesSortBySeries();
    //     $today = date('Y-m-d');
    //     foreach ($crickets as $ck => $c) {
    //         if($c['market'] && !empty($c['market'])) {
    //             $marketId = $c['market']['marketId'];
    //             $eventId = $c['event']['id'];
    //             $edate = date('Y-m-d H:i:s', strtotime($c['event']['openDate']));
    //             $teams = $c['market']['runners'];
    //             $allTeams = array();
    //             foreach($teams as $t):
    //                 $obj = new stdClass();
    //                 $obj->id = $t['selectionId'];
    //                 $obj->name = $t['runnerName'];
    //                 $allTeams[] = $obj;
    //             endforeach;
    //             $match = $this->Common_model->get_single_query("SELECT * FROM cron_data WHERE market_id = '$marketId'");
    //             if(!empty($match)) {
                    
    //             } else {
    //                 $data = array(
    //                     'market_id' => $marketId,
    //                     'event_id' => $c['event']['id'],
    //                     'event_name' => $c['event']['name'],
    //                     'event_date' => $edate,
    //                     'event_typeid' => 4,
    //                     'competition_id' => $c['series']['competition']['id'],
    //                     'competition_name' => $c['series']['competition']['name'],
    //                     'start_date' => $edate,
    //                     'btype' => $c['market']['marketName'],
    //                     'mtype' => $c['market']['marketName'],
    //                     'teams' => json_encode($allTeams),
    //                     'created_at' => date('Y-m-d H:i:s'),
    //                     'updated_at' => date('Y-m-d H:i:s'),
    //                 );
    //                 $this->Crud_model->insert_record('cron_data', $data);
    //             }
    //             $fancies = $this->matchSessionByMatchId($eventId);
    //             if(!empty($fancies)) {
    //                 foreach ($fancies as $fk => $f) {
    //                     $fid = $f['SelectionId'];
    //                     $fname = $f['RunnerName'];
    //                     $fan = $this->Common_model->get_single_query("select * from fancy_data where fancy_name = '$fname' and market_id = '$marketId'");
    //                     if($fan) {
    //                         continue;
    //                     }
    //                     $fdata = array(
    //                         'fancy_id' => $f['SelectionId'],
    //                         'fancy_name' => $f['RunnerName'],
    //                         'market_id' => $marketId,
    //                         'event_id' => $eventId,
    //                         'event_name' => $c['event']['name'],
    //                         'event_date' => $edate,
    //                         'event_typeid' => 4,
    //                         'competition_id' => $c['series']['competition']['id'],
    //                         'competition_name' => $c['series']['competition']['name'],
    //                         'start_date' => $edate,
    //                         'mtype' => $c['market']['marketName'],
    //                         'odds_type' => 'fancy',
    //                         'status' => $f['GameStatus'],
    //                         'created_at' => date('Y-m-d H:i:s'),
    //                         'updated_at' => date('Y-m-d H:i:s'),
    //                     );
    //                     $this->Crud_model->insert_record('fancy_data',$fdata);
    //                 }
    //             }
    //         }
    //     }
    //     echo 'done';
    // }

    public function allSeries() {
        $url = "http://178.79.131.131/api/v1/seriestList?sport_id=4";
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

    public function matchesBySeriesId($seriesId) {
        $url = "http://178.79.131.131/api/v1/matchList?seriesId=".$seriesId;
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

    public function getAllMatches() {
        $series = $this->allSeries();
        $matchRecords = array();
        foreach ($series as $sk => $s) {
            $sid = $s['seriesId'];
            $matches = $this->matchesBySeriesId($sid);
            if(empty($matches)) {
                continue;
            } else {
                foreach ($matches as $mk => $m) {
                    $obj = new stdClass();
                    $obj->seriesId = $m['seriesId'];
                    $obj->seriesName = $s['Name'];
                    $obj->MstCode = $m['MstCode'];
                    $obj->MstDate = $m['MstDate'];
                    $obj->matchName = $m['matchName'];
                    $market = $this->singleMatch($m['MstCode']);
                    if($market) {
                        $obj->market = $market;
                    } else $obj->market = '';
                    $matchRecords[] = $obj;
                }
            }
        }
        return $matchRecords;   
    }

    public function matchesWithOdds() {
        $matches = $this->getAllMatches();
        print_r($matches);
    }

    public function singleMatch($matchId) {
        $match = $this->marketsByMatchId($matchId);
        if(!empty($match)) {
            $data = $match[0];
            $teamData = $data['market_runner_json'];
            $teams = array();
            foreach ($teamData as $tk => $t) {
                $obj = new stdClass();
                $obj->id = $t['selectionId'];
                $obj->name = $t['name'];
                $teams[] = $obj;
            }
            $data['teams'] = json_encode($teams);
            return $data;
        }
        return false;
    }

    public function marketsByMatchId($matchId)
    {
        $url = "http://178.79.131.131/api/v1/marketList?matchId=".$matchId;
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

    public function teamsByMarketId($marketId) {
        $q = $this->Common_model->get_single_query("SELECT teams FROM cron_data WHERE market_id = '$marketId'");
        $teams = json_decode($q->teams);
        return $teams;
    }

    public function matchOddByMarketId($marketId)
    {
        $url = "http://178.79.131.131/api/v1/listMarketBookOdds?market_id=".$marketId;
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
        $runners = $result[0]['runners'];
        $teams = $this->teamsByMarketId($marketId);
        foreach ($runners as $rk => $r) {
            $teams[$rk]->back = $r['back'][0];
            $teams[$rk]->lay = $r['lay'][0]; 
        }
        $result[0]['teams'] = $teams;
        return $result;

    }

    public function matchFancyByMarketId($marketId)
    {
        $url = "http://178.79.131.131/api/v1/listMarketBookSession?market_id=".$marketId;
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

    public function matchFancies($marketId)
    {
        $url = "http://178.79.131.131/api/v1/listMarketBookSession?market_id=".$marketId;
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
        $a = array();
        if(!empty($result)) {
            foreach ($result[0]['value']['session'] as $r) {
                $a[$r['SelectionId']] = $r;
            }
        }
        return $a;
    }

    public function emptyRecord()
    {

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
            if ($d['SportID'] == 4 && ($d['name'] == 'Match Odds' || $d['name'] == 'Winner')) {
                $cricket[] = $data[$key];
            }
        }
        return $cricket;
    }
    public function fancyData($marketId)
    {
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
        //print_r(json_encode($result));
    }
    public function fancies($marketId)
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
        //return $result;
        print_r(json_encode($result));
    }

    public function allMatches()
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

        print_r(json_encode($result));
    }

    public function allCrickets()
    {
        $data = $this->getData();
        //$mdata = $data['result'];
        $cricket = array();
        foreach ($data as $key => $d) {
            if ($d['SportID'] == 4 && ($d['name'] == 'Match Odds' || $d['name'] == 'Winner')) {
                $cricket[] = $data[$key];
            }
        }
        echo json_encode($cricket);
    }

    public function matchOddLocal($marketId)
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
        echo $response;
        //print_r(json_encode($result));
    }
    public function fanciesLocal($marketId)
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
        echo $response;
        //print_r(json_encode($result));
    }

    public function checkUnmatched() {
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        if($matches && !empty($matches)) {
            foreach ($matches as $mkey => $m) {
                $odd = $this->match->matchOddByMarketId($m['market_id']);
                //print_r($odd);
                $market_id = $m['market_id'];
                $teams = json_decode($m['teams']);
                $teamIds = array();
                foreach ($teams as $tm) {
                  $teamIds[] = $tm->id;
                }
                $runners = $odd[0]['teams'];
                if ($odd[0]['inplay'] == 1 || $odd[0]['inplay'] == true || $odd[0]['inPlay'] == 1 || $odd[0]['inPlay'] == true) {
                    foreach ($runners as $rk => $r){
                        $bprice = $r->back ? $r->back['price'] : 0;
                        $bsize = $r->back ? $r->back['size'] : 0;
                        $lprice = $r->lay ? $r->lay['price']: 0;
                        $lsize = $r->lay ? $r->lay['size'] : 0;
                        $untid = $teams[$rk]->id;
                        //unmatched check
                        if(!empty($bprice)) {
                            $backBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$market_id' AND bet_type = 'unmatched' AND back_lay = 'back' AND team_id = $untid AND odd <= $bprice AND status = 'pending'");
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
                            $layBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$market_id' AND bet_type = 'unmatched' AND back_lay = 'lay' AND team_id = $untid AND odd >= $lprice AND status = 'pending'");
                        
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
                    }
                }
            }
        }
    }

    public function testing() {
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        if($matches && !empty($matches)) {
            foreach ($matches as $mkey => $m) {
                $odd = $this->match->matchOddByMarketId($m['market_id']);
                //print_r($odd);
                $market_id = $m['market_id'];
                $teams = json_decode($m['teams']);
                $teamIds = array();
                foreach ($teams as $tm) {
                  $teamIds[] = $tm->id;
                }
                $runners = $odd[0]['teams'];
                if ($odd[0]['inplay'] == 1 || $odd[0]['inplay'] == true || $odd[0]['inPlay'] == 1 || $odd[0]['inPlay'] == true) {
                    foreach ($runners as $rk => $r){
                        $bprice = $r->back ? $r->back['price'] : 0;
                        $bsize = $r->back ? $r->back['size'] : 0;
                        $lprice = $r->lay ? $r->lay['price']: 0;
                        $lsize = $r->lay ? $r->lay['size'] : 0;
                        $untid = $teams[$rk]->id;
                        //unmatched check
                        if(!empty($bprice)) {
                            $backBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$market_id' AND bet_type = 'unmatched' AND back_lay = 'back' AND team_id = $untid AND odd <= $bprice AND status = 'pending'");
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
                            $layBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$market_id' AND bet_type = 'unmatched' AND back_lay = 'lay' AND team_id = $untid AND odd >= $lprice AND status = 'pending'");
                        
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
                    }
                }
            }
        }
        return true;
    }

    public function allSports() {
        $url = $this->utils->absolute("/api/v1/fetch_data?Action=listEventTypes");
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
        echo $response;
    }

    public function seriesBySportsId($sid) {
        $url = $this->utils->absolute("/api/v1/fetch_data?Action=listCompetitions&EventTypeID=".$sid);
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
        // echo $response;
    }

    public function getAllMatchesSortBySeries() {
        $series = $this->seriesBySportsId(4);
        if(!empty($series)) {
            $data = array();
            foreach ($series as $sk => $s) {
                $matches = $this->matchesBySeriesAndSportsId(4,$s['competition']['id']);
                if(!empty($matches)) {
                    $competition = $s['competition']['name'];
                    foreach ($matches as $mk => $m) {
                        $markets = $this->marketByMatchCode($m['event']['id']);
                        if(!empty($markets)) {
                            foreach ($markets as $mks) {
                                if($mks['marketName'] != 'Match Odds') {
                                    continue;
                                } else {
                                    $matches[$mk]['market'] = $mks;
                                    $m['market'] = $mks;
                                }
                            }
                        } else {
                            $matches[$mk]['market'] = '';
                        }
                        $m['series'] = $s;
                        $matches[$mk]['series'] = $s;
                        $data[] = $m;
                    }
                }
            }
        }
        return $data;
    }



    public function matchesBySeriesAndSportsId($eid,$cid) {
        $url = $this->utils->absolute("/api/v1/fetch_data?Action=listEvents&EventTypeID=".$eid."&CompetitionID=".$cid);
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
        // echo $response;
    }

    public function marketByMatchCode($mcode) {
        $url = $this->utils->absolute("/api/v1/fetch_data?Action=listMarketTypes&EventID=".$mcode);
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

    public function printMarketByMatchCode($mcode) {
        $url = $this->utils->absolute("/api/v1/fetch_data?Action=listMarketTypes&EventID=".$mcode);
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
        echo $response;
    }

    public function RunnersByMarketId($mkid) {
        $url = $this->utils->absolute("/api/v1/fetch_data?Action=listMarketRunner&MarketID=".$mkid);
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
        echo $response;
    }

    public function matchOddsByMarketId($mkid) {
        $url = $this->utils->absolute("/api/v1/listMarketBookOdds?market_id=".$mkid);
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
        $runners = $result[0]['runners'];
        $teams = $this->teamsByMarketId($mkid);
        foreach ($runners as $rk => $r) {
            $teams[$rk]->back = $r['ex']['availableToBack'][0];
            $teams[$rk]->lay = $r['ex']['availableToLay'][0]; 
        }
        $result[0]['teams'] = $teams;
        return $result;
    }

    public function matchSessions($mid) {
        $url = $this->utils->absolute("/api/v1/listMarketBookSession?match_id=".$mid);
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
        $a = array();
        if(!empty($result)) {
            foreach ($result as $r) {
                $a[$r['SelectionId']] = $r;
            }
        }
        return $a;
    }

    public function matchSessionByMatchId($mid) {
        $url = $this->utils->absolute("/api/v1/listMarketBookSession?match_id=".$mid);
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

    public function matchScoreByMatchId($mid) {
        $url = $this->utils->absolute("/api/v1/score?match_id=".$mid);
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
        echo $response;
    }

    public function matches() {
        $url = $this->utils->absolute("/apidata/matches.php");
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
        return json_decode($response);
    }

    public function matchesDetails($eventId) {
        $url = $this->utils->absolute("/apidata/matchdetail.php?eventId=$eventId");
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
        return json_decode($response);
    }

    public function OddByMarketId($marketId)
    {
        $url = $this->utils->absolute("apidata/odds.php?marketId=$marketId");
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
        var_dump($result);
        die;
        // $runners = $result[0]['runners'];
        // $teams = $this->teamsByMarketId($marketId);
        // foreach ($runners as $rk => $r) {
        //     $teams[$rk]->back = $r['back'][0];
        //     $teams[$rk]->lay = $r['lay'][0]; 
        // }
        // $result[0]['teams'] = $teams;
        // return $result;

    }

    public function matchFancyByMarketId2($marketId)
    {
        $url = "http://178.79.131.131/api/v1/listMarketBookSession?market_id=".$marketId;
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
}
