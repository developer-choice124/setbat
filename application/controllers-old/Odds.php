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

    public function index() {
    	$crickets = $this->getAllMatches();
    	$today = date('Y-m-d');
    	foreach ($crickets as $ck => $c) {
    		$marketId = $c['market']['Id'];
    		$match = $this->Common_model->get_single_query("SELECT * FROM cron_data WHERE market_id = '$marketId'");
    		if(!empty($match)) {
    			continue;
    		}
    		$edate = date('Y-m-d H:i:s', strtotime($c['MstDate']));
    		$data = array(
                'market_id' => $marketId,
                'event_id' => $c['MstCode'],
                'event_name' => $c['matchName'],
                'event_date' => $edate,
                'event_typeid' => $c['market']['sportsId'],
                'competition_id' => $c['seriesId'],
                'competition_name' => $c['seriesName'],
                'start_date' => $edate,
                'btype' => $c['market']['Name'],
                'mtype' => $c['market']['Name'],
                'teams' => $c['market']['teams'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
    		$this->Crud_model->insert_record('cron_data', $data);
    		$fancies = $this->matchFancyByMarketId($marketId);
    		if(!empty($fancies) && !empty($fancies['value']['session'])) {
    			foreach($fancies['value']['session'] as $f) {
    				
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
    			}
    		}
    	}
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

    public function matchFancyByMarketId1($marketId)
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
        foreach ($result[0]['value']['session'] as $r) {
        	$a[$r['SelectionId']] = $r;

        }
        print_r($a);
        //return $result;
    }
}