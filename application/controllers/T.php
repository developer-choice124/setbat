<?php

class T extends MY_Controller {

    public function index() {
       $market_id = $this->input->get('market_id');
        $res = $this->match->calculateOddProfitLossByMarketId(840, $market_id);
        $numbers = array_column($res, 'pl');
        $min = min($numbers);
        //$res['final'] = $min;
        echo json_encode(array('plData' => $res, 'loss' => $min));
    }
}
