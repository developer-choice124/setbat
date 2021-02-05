<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Cricket {
	public function __construct()
	{
		
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
        print_r($cricket);
    }

    public function newData() {
        $data = $this->getData();
        $mdata = $data['result'];
        $cricket = array();
        foreach ($mdata as $key => $d) {
           if($d['eventTypeId'] == 4) {
            $cricket[] = $mdata[$key];
           }
        }
        return $cricket;
    }

    public function newSingleData($mid) {
        $data = $this->getData();
        $mdata = $data['result'];
        $cricket = array();
        foreach ($mdata as $key => $d) {
           if($d['groupById'] == $mid) {
            $cricket = $mdata[$key];
           }
        }
        return $cricket;
    }

    public function singleCricket() {
        $marketId = $this->input->get('market_id');
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
        //return $result;
        print_r($result);
    }

    public function fileData() {
        $result = file_get_contents('./uploads/cricket.json');
        return json_decode($result, true);
    }

    public function fancyData($eid) {
        $url = "http://fancy.royalebet.uk/".$eid;
        //$url = "http://fancy.royalebet.uk/";
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

    public static function modify($json_data) {
       $data = '}{"status":{"statusCode"';
       $output = $json_data;
       $output = preg_replace('!\s+!', ' ', $json_data);
       $position = strpos($output, $data);
       while ($position > 0) {
           $string = ",";
           $output = substr_replace($output, $string, $position+1, 0);
           $position = strpos($output, $data,$position+1);
       }
       return $output;
    }

    public function modifyJson($json_data) {
        $data = '}{';
        $position = strpos($json_data, $data);
        if($position > 0) {
            $newData = substr($json_data, 0, strpos($json_data, $data));
            $newData .= "}";
            $result = json_decode($newData, true);
        } else {
            $result = json_decode($mdata,true);
        }
        return $result;
    }
}