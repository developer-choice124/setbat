<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blockedevent_model extends CI_Model 
{

    function insert($data){
        $this->db->trans_start();
        $this->db->insert('event_block', $data);
        $this->db->trans_commit();
        return $this->db->insert_id();
    }
    function update($user_id, $data){
        $this->db->where('user_id',$user_id);
		$this->db->update('event_block',$data);
    }
}
