<?php

class Ff extends MY_Controller {

    public function index() {
        $market_id = "1.158851826";
        $list = $this->Ms_model->ReadRaw("select * from ff where market_id=$market_id ");
        $outlist = array();
        
        foreach ($list as $record) {
            $outlist[$record->team][] = $record;
            
            print_r($record);
            echo "<hr/>";
        }
        
        ///////////////////////////////////
        foreach ($outlist as $team => $record_array) {
            //  print_r($record_array);
            // echo "<br/><hr/>";
            $used = array();
            $total = 0;
            for ($i = 0; $i < count($record_array); $i++) {
                $record_a = $record_array[$i];
                if (in_array($record_a->id, $used)) {
                    continue;
                }
                for ($j = $i + 1; $j < count($record_array); $j++) {
                    $record_b = $record_array[$j];
                    if ($record_a->back_lay == "back") {
                        if ($record_b->back_lay == "lay" && $record_b->odd >= $record_a->odd && !in_array($record_b->id, $used)) {
                            $used[] = $record_a->id;
                            $used[] = $record_b->id;
                            $abs = abs($record_a->loss - $record_b->loss);
                            echo "<br/> For ID " . $record_a->id . " and " . $record_b->id . " , diff is " . $abs;
                            $total += $abs;
                        }
                    }
                }//for-j
            }//for-i
            
            foreach ($record_array as $record) {
                if (!in_array($record->id, $used)) {
                    echo "<br/>".$team."[ For ID " . $record_a->id . " LOSS is " . $abs;
                    $total += $record->loss;
                }
            }
            echo "<hr/> FOR TEAM " . $team . " , total is : ";
            echo $total;
            echo "<hr/><hr/><hr/>";
        }//outer_most
    }
    
    public function shah() {
        $market_id = "1.158852421";
        $list = $this->Ms_model->ReadRaw("select * from ff where market_id='$market_id'");
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
               if($record->back_lay == 'back' && !in_array($record->id,$backIds)) {
                   $backOdds[] = $record->odd;
                   $backIds[] = $record->id;
                   $plus += $record->loss;
               }
            }
            if($backOdds) {
                $minBackOdd = min($backOdds);
                $minloss = $this->Ms_model->ReadRaw("SELECT id,loss FROM ff WHERE back_lay = 'lay' AND odd < $minBackOdd AND market_id = '$market_id' AND team = '$team'");
            } else {
                $minloss = $this->Ms_model->ReadRaw("SELECT id,loss FROM ff WHERE back_lay = 'lay'  AND market_id = '$market_id' AND team = '$team'");
            }
            for($j = 0; $j < count($minloss); $j++) {
               $min += $minloss[$j]->loss;
               $layUsed[] = $minloss[$j]->id;
            }
            for ($k = 0; $k < count($value); $k++) {
               $record = $value[$k];
               if($record->back_lay == 'lay' && !in_array($record->id, $layUsed) && !in_array($record->id, $layMinusIds)) {
                $minus += $record->loss;
                $layMinusIds[] = $record->id;
               }
               //echo json_encode($record);
            }
            
        }

        $total = abs($plus - $minus) + $min;
        echo '<br/><hr/>'.$total;
    }

}