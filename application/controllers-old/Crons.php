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

    public function index()
    {

        $today = date('Y-m-d');
        $crickets = $this->getCricket();
        $json = json_encode($crickets);

        $jdata = array(
            'json_data' => $json,
            'created_at' => date('Y-m-d H:i:s'),
        );
        $this->Crud_model->insert_record('cron_json', $jdata);
        foreach ($crickets as $key => $c) {
            $edate = date('Y-m-d H:i:s', strtotime($c['MstDate']));
            $runners = json_decode($c['runners']);
            $teams = array();
            foreach ($runners->runners as $rkey => $r) {
                $teams[$rkey]['id'] = $r->selectionId;
                $teams[$rkey]['name'] = $r->runnerName;
            }
            $data = array(
                'market_id' => $c['Id'],
                'event_id' => $c['matchid'],
                'event_name' => $c['matchName'],
                'event_date' => $edate,
                'event_typeid' => $c['SportID'],
                'competition_id' => $c['seriesId'],
                'competition_name' => $c['seriesname'],
                'start_date' => $edate,
                'btype' => $c['name'],
                'mtype' => $c['name'],
                'teams' => json_encode($teams),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );

            $mid = $c['matchid'];
            $mdate = date('Y-m-d', strtotime($c['MstDate']));

            $match = $this->Common_model->get_single_query("select * from cron_data where event_id = $mid");
            if ($match) {
                $this->Crud_model->edit_record('cron_data', $match->id, $data);
            } else {
                $this->Crud_model->insert_record('cron_data', $data);
            }
            $fancies = $this->fancyData($c['Id']);
            if ($fancies) {
                foreach ($fancies['session'] as $key => $f) {
                    $fid = $f['SelectionId'];
                    $rname = $f['RunnerName'];
                    $mid = $c['Id'];
                    $fan = $this->Common_model->get_single_query('SELECT * from fancy_data where fancy_name = "'.$rname.'" AND market_id ="'.$mid.'"');
                    if ($fan) {

                    } else {
                        $fdata = array(
                            'fancy_id' => $f['SelectionId'],
                            'fancy_name' => $f['RunnerName'],
                            'market_id' => $c['Id'],
                            'event_id' => $c['matchid'],
                            'event_name' => $c['matchName'],
                            'event_date' => $edate,
                            'event_typeid' => $c['SportID'],
                            'competition_id' => $c['seriesId'],
                            'competition_name' => $c['seriesname'],
                            'start_date' => $edate,
                            'mtype' => $c['name'],
                            'odds_type' => 'fancy',
                            'status' => 'playing',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        );
                        $this->Crud_model->insert_record('fancy_data', $fdata);
                    }

                }
            }
        }
        //$testingData = array('msgc' => time(), 'created_at' => date('Y-m-d H:i:s'));
        //$this->Crud_model->insert_record('testing',$testingData);
        return true;
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
        echo $response;
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
        echo $response;
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
        echo $response;
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
        echo $response;
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
        //echo $response;
        print_r($result);
    }

    public function matchesByMarketId() {
        $url = "http://178.79.131.131/api/v1/listMarketBookOdds?market_id=1.157205158,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499,1.157288278,1.157205499";
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

    public function FunctionName($value='')
    {
        $url = "https://betdip.com/betfairapi/series.php?sport_id=4";
    }

    public function testing() {
        return true;
    }

    public function updateUserBal() {
        $users = $this->Common_model->get_data_by_query("SELECT id FROM users_with_groups WHERE group_id =5");
        foreach ($users as $key => $u) {
            //$final = $this->match->calculateProfitLossAllMatch($this->id);
            //echo $final;
        }
    }

    public function testing_willCheck() {
        $matches = $this->Common_model->get_data_by_query("SELECT * FROM running_matches WHERE match_result = 'running' AND admin_enable = 'yes'");
        foreach ($matches as $mkey => $m) {
            $odd = $this->matchOdd($m['market_id']);
            $market_id = $m['market_id'];
            $runners = $odd['runners'];
            if ($odd['inplay'] == 1 || $odd['inplay'] == true) {
                foreach ($runners as $rk => $r){
                    $bprice = $r['ex']['availableToBack'][0]['price'];
                    $bsize = $r['ex']['availableToBack'][0]['size'];
                    $lprice = $r['ex']['availableToLay'][0]['price'];
                    $lsize = $r['ex']['availableToLay'][0]['size'];
                    $backBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$market_id' AND bet_type = 'unmatched' AND back_lay = 'back' AND odd <= $bprice AND status = 'pending'");
                    $layBets = $this->Common_model->get_data_by_query("SELECT * FROM bet WHERE market_id = '$market_id' AND bet_type = 'unmatched' AND back_lay = 'lay' AND odd >= $lprice AND status = 'pending'");
                    if($backBets) {
                        foreach ($backBets as $bk => $b) {
                           $bdata = array(
                            'bet_type'  => 'matched',
                            'odd'       => $bprice,
                            'profit'    => $bprice * $b['stake'] - $b['stake'],
                            'loss'      => $b['stake'],
                            'updated_at'=> date('Y-m-d H:i:s')
                           );
                           $this->Crud_model->edit_record('bet',$b['id'],$bdata);
                        }
                    }
                    if($layBets) {
                        foreach ($layBets as $lk => $l) {
                           $ldata = array(
                            'bet_type'  => 'matched',
                            'odd'       => $lprice,
                            'profit'    => $b['stake'],
                            'loss'      => $lprice * $b['stake'] - $b['stake'],
                            'updated_at'=> date('Y-m-d H:i:s')
                           );
                           $this->Crud_model->edit_record('bet',$b['id'],$ldata);
                        }
                    }
                }
            }
        }
        // echo 'thank you';
        //$testingData = array('msgc' => time(), 'created_at' => date('Y-m-d H:i:s'));
        //$this->Crud_model->insert_record('testing',$testingData);
        return true;
    }
}
