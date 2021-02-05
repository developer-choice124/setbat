<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cricket extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation'));
        $this->load->helper(array('url', 'language'));
        $this->load->model('Setting_model');
        $this->load->model('MyModel');
        $this->settings = $this->Setting_model->get_setting();
        if (!empty($this->settings->timezone)) {
            date_default_timezone_set($this->settings->timezone);
        } else {
            date_default_timezone_set("Asia/Kolkata");
        }
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

    function json_output($statusHeader, $response) {
        $ci = & get_instance();
        $ci->output->set_content_type('application/json');
        $ci->output->set_status_header($statusHeader);
        $ci->output->set_output(json_encode($response,$statusHeader));
    }

    function alert_email_server($toemail, $subj, $msg) {
        $this->load->library('email');
        $config['useragent'] = "CodeIgniter";
        $config['protocol'] = 'mail';
        $config['smtp_host'] = 'smtp.gmail.com';
        $config['smtp_port'] = '25';
        $config['smtp_timeout'] = '7';
        $config['smtp_user'] = 'admin@betcric.in';
        $config['smtp_pass'] = 'AnK56KwIxyfZ2TgIdBjXeBTRuHAo1MupW+hYfmJdgbo0';
        $config['charset'] = 'utf-8';
        $config['newline'] = "\r\n";
        $config['mailtype'] = 'html'; // or html
        $config['validation'] = TRUE; // bool whether to validate email or not      
        $this->email->initialize($config);
        $this->email->from('admin@betcric.in', 'betcric');
        $this->email->to($toemail);
        $this->email->subject($subj);
        $this->email->message($msg);
        $this->email->send();
        //echo $this->email->print_debugger(); die;
    }  
}