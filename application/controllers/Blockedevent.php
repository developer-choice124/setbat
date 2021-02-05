<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Blockedevent extends MY_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation', 'settlement'));
        $this->load->helper(array('url', 'language'));
        $this->load->model(['Setting_model','blockedevent_model','Common_model']);
        $this->load->model('MyModel');
        $this->load->library('pagination');
        $this->settings = $this->Setting_model->get_setting();
        if (!empty($this->settings->timezone)) {
            date_default_timezone_set($this->settings->timezone);
        } else {
            date_default_timezone_set("Asia/Kolkata");
        }
        if (!$this->ion_auth->logged_in()) {
            redirect('Auth/login');
        }
        $this->id = $this->session->userdata('user_id');
        $this->p_l = $this->Common_model->get_single_query("select sum('credits') as c, sum(debits) as d from credits_debits where user_id = $this->id and type='bet'");
        $this->panel = $this->Common_model->get_single_query("SELECT * FROM panel_title ORDER BY id DESC");
    }

    public function getblocked_event(){
        $id = $this->input->get('user_id');
        
        $event = $this->Common_model->get_data_by_query("SELECT *  FROM `event_block` where user_id = $id order by id asc");
        foreach($event as $p_key => $list){
            $event_ids = json_decode($list['event_id']);
            $event_list = [];
            foreach($event_ids as $key => $ids){
                $result = $this->Common_model->get_data_by_query("SELECT * FROM cron_data where event_id = $ids"); 
                if(sizeof($result) > 0){
                    $result = $result[0];
                }
                array_push($event_list, $result);
            }
            $event[$p_key]['details'] = json_encode($event_list);
            
        }
        echo json_encode($event);
    }
    
    public function blocked_event(){
        $user_id = $this->input->post('user_id');
        $event_ids = $this->input->post('block_event_id');
        $if_exist = $this->Common_model->get_data_by_query("SELECT *  FROM `event_block` where user_id = $user_id order by id asc");
        if(sizeof($if_exist) > 0){
            $if_exist = $if_exist[0];
            $exist_event_id = json_decode($if_exist['event_id']);
            $result = array_unique(array_merge($exist_event_id,$event_ids));
            $data = [
                "event_id" => json_encode(array_values($result)),
            ];
            $blocked_id = $this->blockedevent_model->update($user_id, $data);
            
        }else{
            $data = [
                "user_id" => $user_id,
                "event_id" => json_encode($event_ids),
                "status" => 1
            ];
    
            $blocked_id = $this->blockedevent_model->insert($data);
        }

        redirect($_SERVER['HTTP_REFERER']);
    }
}