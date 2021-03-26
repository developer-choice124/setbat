<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Match {

	public function __construct() {
		$this->CI = &get_instance();
	}

	public function index() {
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
			if ($d['SportID'] == 4 && ($d['name'] == 'Match Odds' || $d['name'] == 'Winner')) {
				$cricket[] = $data[$key];
			}
		}
		return $cricket;
	}

	public function matchOdd($marketId) {
		//$url = "http://rohitash.dream24.bet:3000/getmarket?id=" . $marketId;
		$url = "http://betcric.in/test/Crons/matchOddLocal/" . $marketId;
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
		//$url = "http://fancy.dream24.bet/price/?name=" . $marketId;
		$url = "http://betcric.in/test/Crons/fanciesLocal/" . $marketId;
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

	public function calculateOddProfitLossByMarketId($uid, $mkid = null) {
		$match = $this->CI->Common_model->get_single_query("SELECT * FROM running_matches WHERE market_id = $mkid");
		$runners = json_decode($match->teams);
		foreach ($runners as $rk => $r) {
			$tid = $r->id;
			$allTeams[$rk]['back'] = $this->CI->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$mkid' AND team_id = $tid AND back_lay = 'back' AND bet_type = 'matched'");
			$allTeams[$rk]['lay'] = $this->CI->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$mkid' AND team_id = $tid AND back_lay = 'lay' AND bet_type = 'matched'");
			$allTeams[$rk]['tid'] = $tid;
		}
		$res = array();
		foreach ($runners as $rk => $r) {
			$tid = $r->id;
			$res[$rk]['pl'] = $this->calculateResult($allTeams, $rk);
			$res[$rk]['id'] = $tid;
		}
		return $res;
	}

	public function calculateProfitLossAllMatch($uid) {
		$mids = $this->CI->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $uid AND status='pending'");
		$matchedLoss = 0;
		$unmatchedLoss = 0;
		$fancyLoss = 0;
		if (!empty($mids)) {
			foreach ($mids as $mk => $m) {
				$market_id = $m['market_id'];
				$Modds = $this->matchOddByMarketId($market_id);
				$runners = $Modds[0]['teams'];
				foreach ($runners as $rk => $r) {
					$tid = $r->id;
					$allTeams[$rk]['back'] = $this->CI->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'back' AND bet_type = 'matched'");
					$allTeams[$rk]['lay'] = $this->CI->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'lay' AND bet_type = 'matched'");
					$allTeams[$rk]['tid'] = $tid;
				}
				$res = array();
				foreach ($runners as $rk => $r) {
					$tid = $r->id;
					$res[$rk]['pl'] = $this->calculateResult($allTeams, $rk);
					$res[$rk]['id'] = $tid;
				}
				//$res = array_values($res);
				$numbers = array_column($res, 'pl');
				$min = min($numbers);
				if ($min < 0) {
					$matchedLoss += abs($min);
				}
				//unmatch loss
				$unmatchedLoss += $this->calculateUnmatchedLoss($uid, $market_id);
				//fancy loss
				$fancyLoss += $this->calculateFancyLoss($uid, $market_id);
			}
		}

		$totalLoss = $matchedLoss + $unmatchedLoss + $fancyLoss;
		$ubal = $this->CI->Common_model->get_single_query("SELECT SUM(credits) as c, SUM(debits) as d FROM credits_debits WHERE user_id = $uid");

		$ubalance = $ubal->c - $ubal->d;
		$currentBal = $ubalance - $totalLoss;
		return $currentBal;
	}

	function calculateSingleFancyLoss($uid, $team = null, $mkid = null) {
		$total = 0;
		$min = 0;
		$plus = 0;
		$minus = 0;
		$record = $this->CI->Common_model->get_single_query("SELECT * FROM bet WHERE market_id='$mkid' AND team = '$team' AND user_id = $uid AND bet_type = 'fancy'");
		$used = array();
		$layUsed = array();
		$backOdds = array();
		$backIds = array();
		$layMinusIds = array();
		if ($record->back_lay == 'back' && !in_array($record->id, $backIds)) {
			$backOdds[] = $record->odd;
			$backIds[] = $record->id;
			$plus += $record->loss;
		}
		if ($backOdds) {
			$minBackOdd = min($backOdds);
			$minloss = $this->CI->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay' AND odd < $minBackOdd AND market_id = '$mkid' AND team = '$team' and user_id = $uid and bet_type = 'fancy'");
		} else {
			$minloss = $this->CI->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay'  AND market_id = '$mkid' AND team = '$team' and user_id = $uid and bet_type = 'fancy'");
		}
		for ($j = 0; $j < count($minloss); $j++) {
			$min += $minloss[$j]->loss;
			$layUsed[] = $minloss[$j]->id;
		}
		if ($record->back_lay == 'lay' && !in_array($record->id, $layUsed) && !in_array($record->id, $layMinusIds)) {
			$minus += $record->loss;
			$layMinusIds[] = $record->id;
		}
		$total = abs($plus - $minus) + $min;
		return $total;
	}

	function calculateFancyLoss($uid, $mkid = null) {
		$list = $this->CI->Common_model->ReadRaw("SELECT * FROM bet WHERE market_id='$mkid' AND user_id = $uid AND bet_type = 'fancy' AND status = 'pending'");
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
				$minloss = $this->CI->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay' AND odd < $minBackOdd AND market_id = '$mkid' AND team = '$team' and user_id = $uid and bet_type = 'fancy' and status = 'pending'");
			} else {
				$minloss = $this->CI->Common_model->ReadRaw("SELECT id,loss FROM bet WHERE back_lay = 'lay'  AND market_id = '$mkid' AND team = '$team' and user_id = $uid and bet_type = 'fancy' and status = 'pending'");
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
		return $total;
	}

	function calculateUnmatchedLoss($uid, $mkid = null) {
		$bets = $this->CI->Common_model->get_data_by_query("SELECT * FROM bet WHERE bet_type = 'unmatched' AND user_id = $uid AND market_id = '$mkid' AND status = 'pending'");
		$unmatchedLoss = 0;
		if (!empty($bets)) {
			foreach ($bets as $bk => $b) {
				$unmatchedLoss += $b['loss'];
			}
		}
		return $unmatchedLoss;
	}

	function calculateResult($input_array, $index) {
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

	public function maxLimitByMarketId($uid, $mkid = null) {
		$mids = $this->CI->Common_model->get_data_by_query("SELECT DISTINCT(market_id) FROM bet WHERE user_id = $uid AND status='pending'");
		$matchedLoss = 0;
		$unmatchedLoss = 0;
		$fancyLoss = 0;
		if (!empty($mids)) {
			foreach ($mids as $mk => $m) {
				$market_id = $m['market_id'];
				if ($market_id == $mkid) {
					continue;
				} else {
					$Modds = $this->matchOddByMarketId($market_id);
					$runners = $Modds[0]['teams'];
					foreach ($runners as $rk => $r) {
						$tid = $r->id;
						$allTeams[$rk]['back'] = $this->CI->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'back' AND bet_type = 'matched'");
						$allTeams[$rk]['lay'] = $this->CI->Common_model->get_single_query("SELECT SUM(profit) AS p, SUM(loss) AS l FROM bet WHERE user_id = $uid AND market_id = '$market_id' AND team_id = $tid AND back_lay = 'lay' AND bet_type = 'matched'");
						$allTeams[$rk]['tid'] = $tid;
					}
					$res = array();
					foreach ($runners as $rk => $r) {
						$tid = $r->id;
						$res[$rk]['pl'] = $this->calculateResult($allTeams, $rk);
						$res[$rk]['id'] = $tid;
					}
					//$res = array_values($res);
					$numbers = array_column($res, 'pl');
					$min = min($numbers);
					if ($min < 0) {
						$matchedLoss += abs($min);
					}
				}
				//unmatch loss
				$unmatchedLoss += $this->calculateUnmatchedLoss($market_id);
				//fancy loss
				$fancyLoss += $this->calculateFancyLoss($market_id);
			}
		}
		$totalLoss = $matchedLoss + $unmatchedLoss + $fancyLoss;

		$ubal = $this->CI->Common_model->get_single_query("SELECT SUM(credits) as c, SUM(debits) as d FROM credits_debits WHERE user_id = $uid");

		$ubalance = $ubal->c - $ubal->d;
		$currentBal = $ubalance - $totalLoss;
		return $currentBal;
	}

	public function teamsByMarketId($marketId) {
		$q = $this->CI->Common_model->get_single_query("SELECT teams FROM cron_data WHERE market_id = '$marketId'");
		$teams = json_decode($q->teams);
		return $teams;
	}

	public function oldmatchOddByMarketId($marketId) {
		$url = "http://178.79.131.131/api/v1/listMarketBookOdds?market_id=" . $marketId;
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

	// This Api write in 2021
	public function matchOddByMarketId($marketId) {
		$CI = &get_instance();
		$url = $CI->utils->absolute("/apidata/odds.php?marketId=$marketId");
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

	// public function matchOddByMarketId($mkid)
	// {
	//     $CI = &get_instance();
	//     $url = $CI->utils->absolute("/api/v1/listMarketBookOdds?market_id=" . $mkid);
	//     $curl = curl_init();
	//     curl_setopt_array($curl, array(
	//         CURLOPT_URL => $url,
	//         CURLOPT_RETURNTRANSFER => true,
	//         CURLOPT_ENCODING => "",
	//         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
	//         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0),
	//         CURLOPT_TIMEOUT => 30000,
	//         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	//         CURLOPT_CUSTOMREQUEST => "GET",
	//     ));
	//     $response = curl_exec($curl);
	//     $err = curl_error($curl);
	//     curl_close($curl);
	//     $result = json_decode($response, true);
	//     $runners = $result[0]['runners'];
	//     $teams = $this->teamsByMarketId($mkid);
	//     foreach ($runners as $rk => $r) {
	//         $teams[$rk]->back = $r['ex']['availableToBack'][0];
	//         $teams[$rk]->lay = $r['ex']['availableToLay'][0];
	//     }
	//     $result[0]['teams'] = $teams;
	//     return $result;
	// }

	public function matchFancyByMarketId($mid) {
		$CI = &get_instance();
		$url = $CI->utils->absolute("/api/v1/listMarketBookSession?match_id=" . $mid);
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

	public function matchFancies($mid) {
		$CI = &get_instance();
		$url = $CI->utils->absolute("/apidata/sessions.php?marketId=$mid");
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

	/* public function matchFancies($mid)
		    {
		        $CI = &get_instance();
		        $url = $CI->utils->absolute("/api/v1/listMarketBookSession?match_id=" . $mid);
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
		        if (!empty($result)) {
		            foreach ($result as $r) {
		                $a[$r['SelectionId']] = $r;
		            }
		        }
		        return $a;
	*/

	public function oldmatchFancyByMarketId($marketId) {
		$url = "http://178.79.131.131/api/v1/listMarketBookSession?market_id=" . $marketId;
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

	public function oldmatchFancies($marketId) {
		$url = "http://178.79.131.131/api/v1/listMarketBookSession?market_id=" . $marketId;
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
		if (!empty($result)) {
			foreach ($result[0]['value']['session'] as $r) {
				$a[$r['SelectionId']] = $r;
			}
		}
		return $a;
	}

	public function cricketScore($matchId) {
		$url = "https://www.cricbuzz.com/api/cricket-match/commentary/" . $matchId;
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
