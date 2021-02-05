<?php

class Jsonresult extends CI_Model {

    public $id;
    public $groupById;
    public $name;
    public $aussieExchange;
    public $exchangeId;
    public $start;
    public $btype;
    public $stype;
    public $event;
    public $eventTypeId;
    public $inPlay;
    public $competition;
    public $matched; // ;// 32033481.11
    public $numWinners; // 1
    public $numRunners; // 2
    public $numActiveRunners; // 2
    public $status; // "OPEN"
    public $statusLabel; // null
    public $lastUpdateTime; // null
    public $oddsType; // null
    public $provider; // "BETFAIR"
    public $runners;
    public $maxLiabilityPerBet; // null
    public $maxLiabilityPerMarket; // null
    public $betDelay; // 5
    public $isBettable; // true
    public $bettableTime; // null
    public static function normaLizeResult(&$result){
        $a=array();
        $b = array();
        foreach ($result as $obj){
            
            $a[$obj['groupById']]=$obj;
        }
        foreach ($a as $key => $aa) {
            foreach ($aa['runners'] as $key1 => $v) {
                $b[$key1]['id'] = $v['id'];
                $b[$key1]['name'] = $v['name'];
            }
            $a[$key]['team'] = $b;
            
        }
        $result=$a;
        return $result;
        
    }

}
