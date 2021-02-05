<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Odds extends MY_Controller
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

    public function store() {
        $crickets = $this->getAllMatchesSortBySeries();
        $today = date('Y-m-d');
        foreach ($crickets as $ck => $c) {
            if($c['market'] && !empty($c['market'])) {
                $marketId = $c['market']['marketId'];
                $eventId = $c['event']['id'];
                $edate = date('Y-m-d H:i:s', strtotime($c['event']['openDate']));
                $teams = $c['market']['runners'];
                $allTeams = array();
                foreach($teams as $t):
                    $obj = new stdClass();
                    $obj->id = $t['selectionId'];
                    $obj->name = $t['runnerName'];
                    $allTeams[] = $obj;
                endforeach;
                $match = $this->Common_model->get_single_query("SELECT * FROM cron_data WHERE market_id = '$marketId'");
                if(!empty($match)) {
                    
                } else {
                    $data = array(
                        'market_id' => $marketId,
                        'event_id' => $c['event']['id'],
                        'event_name' => $c['event']['name'],
                        'event_date' => $edate,
                        'event_typeid' => 4,
                        'competition_id' => $c['series']['competition']['id'],
                        'competition_name' => $c['series']['competition']['name'],
                        'start_date' => $edate,
                        'btype' => $c['market']['marketName'],
                        'mtype' => $c['market']['marketName'],
                        'teams' => json_encode($allTeams),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    );
                    $this->Crud_model->insert_record('cron_data', $data);
                    $fancies = $this->matchSessionByMatchId($eventId);
                    if(!empty($fancies)) {
                        foreach ($fancies as $fk => $f) {
                            $fid = $f['SelectionId'];
                            $fname = $f['RunnerName'];
                            $fan = $this->Common_model->get_single_query("select * from fancy_data where fancy_name = '$fname' and market_id = '$marketId'");
                            if($fan) {
                                continue;
                            }
                            $fdata = array(
                                'fancy_id' => $f['SelectionId'],
                                'fancy_name' => $f['RunnerName'],
                                'market_id' => $marketId,
                                'event_id' => $eventId,
                                'event_name' => $c['event']['name'],
                                'event_date' => $edate,
                                'event_typeid' => 4,
                                'competition_id' => $c['series']['competition']['id'],
                                'competition_name' => $c['series']['competition']['name'],
                                'start_date' => $edate,
                                'mtype' => $c['market']['marketName'],
                                'odds_type' => 'fancy',
                                'status' => $f['GameStatus'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            );
                            $this->Crud_model->insert_record('fancy_data',$fdata);
                            echo $this->db->last_query();
                            echo '<hr/>';
                        }
                    }
                }
            }
        }
    }

    public function index() {
        $crickets = $this->getAllMatches();
        $today = date('Y-m-d');
        foreach ($crickets as $ck => $c) {
            $marketId = $c->market['Id'];
            $edate = date('Y-m-d H:i:s', strtotime($c->MstDate));
            $match = $this->Common_model->get_single_query("SELECT * FROM cron_data WHERE market_id = '$marketId'");
            if(!empty($match)) {
                
            } else {
                
                $data = array(
                    'market_id' => $marketId,
                    'event_id' => $c->MstCode,
                    'event_name' => $c->matchName,
                    'event_date' => $edate,
                    'event_typeid' => $c->market['sportsId'],
                    'competition_id' => $c->seriesId,
                    'competition_name' => $c->seriesName,
                    'start_date' => $edate,
                    'btype' => $c->market['Name'],
                    'mtype' => $c->market['Name'],
                    'teams' => $c->market['teams'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );
                $this->Crud_model->insert_record('cron_data', $data);
            }
            $fancies = $this->matchFancyByMarketId($marketId);
            if(!empty($fancies) && !empty($fancies[0]['value']['session'])) {
                foreach($fancies[0]['value']['session'] as $f) {
                    $fid = $f['SelectionId'];
                    $fname = $f['RunnerName'];
                    $fan = $this->Common_model->get_single_query("select * from fancy_data where fancy_name = '$fname' and market_id = '$marketId'");
                    if($fan) {
                        continue;
                    }
                    $fdata = array(
                        'fancy_id' => $f['SelectionId'],
                        'fancy_name' => $f['RunnerName'],
                        'market_id' => $marketId,
                        'event_id' => $c->MstCode,
                        'event_name' => $c->matchName,
                        'event_date' => $edate,
                        'event_typeid' => $c->market['sportsId'],
                        'competition_id' => $c->seriesId,
                        'competition_name' => $c->seriesName,
                        'start_date' => $edate,
                        'mtype' => $c->market['Name'],
                        'odds_type' => 'fancy',
                        'status' => $f['GameStatus'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    );
                    $this->Crud_model->insert_record('fancy_data',$fdata);
                }
            }
            
        }
        echo 'done';
    }

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
        //print_r($result);
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
        //print_r($matchRecords);
    }

    public function matchesWithOdds() {
        $matches = $this->getAllMatches();
        print_r(json_encode($matches));
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
        // return $result;
        print_r(json_encode($result));
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

    public function matchFanciesJson($marketId)
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
        return $response;
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
        // return $a;
        print_r(json_encode($a));
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
        // return $result;
        echo $response;
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
}