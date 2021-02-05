<?php

class Ms_model extends MY_Model {

    public function distinctTeamCsv($id, $unkids) {
        $val = "'" . implode("','", $unkids) . "'";
        $status = 'pending';
        $sql = "SELECT distinct(team),market_id FROM bet WHERE user_id = $id AND market_id in ($val) AND bet_type = 'fancy' AND status='$status'";
        //echo $sql;
        $list = $this->ReadRaw($sql);
        $outlist = array();
        foreach ($list as $k => $v) {
            $outlist[] = $v->team;
        }
        return $outlist;
    }

    public function TableName() {
        return "dummy";
    }

}
