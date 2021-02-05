<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MyModel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key       = "betcric";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->config->load('ion_auth', TRUE);
        $this->load->helper('cookie');
        $this->load->helper('date');
        $this->lang->load('ion_auth');

        // initialize db tables data
        $this->tables  = $this->config->item('tables', 'ion_auth');

        //initialize data
        $this->identity_column = $this->config->item('identity', 'ion_auth');
        $this->store_salt      = $this->config->item('store_salt', 'ion_auth');
        $this->salt_length     = $this->config->item('salt_length', 'ion_auth');
        $this->join            = $this->config->item('join', 'ion_auth');


        // initialize hash method options (Bcrypt)
        $this->hash_method = $this->config->item('hash_method', 'ion_auth');
        $this->default_rounds = $this->config->item('default_rounds', 'ion_auth');
        $this->random_rounds = $this->config->item('random_rounds', 'ion_auth');
        $this->min_rounds = $this->config->item('min_rounds', 'ion_auth');
        $this->max_rounds = $this->config->item('max_rounds', 'ion_auth');


        // initialize messages and error
        $this->messages    = array();
        $this->errors      = array();
        $delimiters_source = $this->config->item('delimiters_source', 'ion_auth');

        // load the error delimeters either from the config file or use what's been supplied to form validation
        if ($delimiters_source === 'form_validation')
        {
            // load in delimiters from form_validation
            // to keep this simple we'll load the value using reflection since these properties are protected
            $this->load->library('form_validation');
            $form_validation_class = new ReflectionClass("CI_Form_validation");

            $error_prefix = $form_validation_class->getProperty("_error_prefix");
            $error_prefix->setAccessible(TRUE);
            $this->error_start_delimiter = $error_prefix->getValue($this->form_validation);
            $this->message_start_delimiter = $this->error_start_delimiter;

            $error_suffix = $form_validation_class->getProperty("_error_suffix");
            $error_suffix->setAccessible(TRUE);
            $this->error_end_delimiter = $error_suffix->getValue($this->form_validation);
            $this->message_end_delimiter = $this->error_end_delimiter;
        }
        else
        {
            // use delimiters from config
            $this->message_start_delimiter = $this->config->item('message_start_delimiter', 'ion_auth');
            $this->message_end_delimiter   = $this->config->item('message_end_delimiter', 'ion_auth');
            $this->error_start_delimiter   = $this->config->item('error_start_delimiter', 'ion_auth');
            $this->error_end_delimiter     = $this->config->item('error_end_delimiter', 'ion_auth');
        }


        // initialize our hooks object
        $this->_ion_hooks = new stdClass;

        // load the bcrypt class if needed
        if ($this->hash_method == 'bcrypt') {
            if ($this->random_rounds)
            {
                $rand = rand($this->min_rounds,$this->max_rounds);
                $params = array('rounds' => $rand);
            }
            else
            {
                $params = array('rounds' => $this->default_rounds);
            }

            $params['salt_prefix'] = $this->config->item('salt_prefix', 'ion_auth');
            $this->load->library('bcrypt',$params);
        }

        $this->ion_auth->trigger_events('model_constructor');
    }
    public function check_auth_client(){
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key  = $this->input->get_request_header('Auth-Key', TRUE);
        
        if($client_service == $this->client_service && $auth_key == $this->auth_key){
            return true;
        } else {
            return json_output(200,array('status' => 200,'message' => 'Unauthorized.'));
        }
    }
    function logins($params)
    {
        $email = $params['email'];
        $password = $params['password'];
        $social = $params['social'];
        if($social == 1)
        {
            
            $q  = $this->db->select('email,social,id,full_name')->from('users')->where('email',$email)->get()->row();
            if($q) {
                $id = $q->id;
                $fname = $q->full_name;
                $last_login = date('Y-m-d H:i:s');
                $token = crypt(substr( md5(rand()), 0, 7));
                $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
                $this->db->trans_start();
                $this->db->where('id',$id)->update('users',array('last_login' => $last_login));
                $this->db->insert('users_authentication',array('users_id' => $id,'token' => $token,'expired_at' => $expired_at));
                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    return array('status' => 500,'message' => 'Internal server error.');
                }
                else {
                    $this->db->trans_commit();
                    return array('status' => 200,'message' => 'Successfully login.','id' => $id, 'name' => $fname, 'token' => $token);
                }
            }
            else{
                $full_name = $params['full_name'];
                $data1 = array(
                    "full_name" => $full_name,
                    "email"     => $email,
                    "username"  => $email,
                    "social"    => 1,
                    "active"    => 1
                );
                $this->db->insert('users',$data1);
                $id = $this->db->insert_id();
                $data2 = array(
                    "user_id"   => $id,
                    "group_id"  => 2
                );
                $this->db->insert('users_groups',$data2);
                $last_login = date('Y-m-d H:i:s');
                $token = crypt(substr( md5(rand()), 0, 7));
                $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
                $this->db->trans_start();
                $this->db->where('id',$id)->update('users',array('last_login' => $last_login));
                $this->db->insert('users_authentication',array('users_id' => $id,'token' => $token,'expired_at' => $expired_at));
                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    return array('status' => 500,'message' => 'Internal server error.');
                }
                else {
                    $this->db->trans_commit();
                    $sub = "Registration successful";
                    $msg = "Dear user ".$email."<br>Thank you for registering with us. Your account has been registered successfully.";
                    $this->alert_email_server($email, $sub, $msg);
                    return array('status' => 200,'message' => 'Successfully login.','id' => $id, 'name' => $full_name, 'token' => $token);
                }
            }
        }
        else{
            $q  = $this->db->select('password,id,full_name')->from('users')->where('email',$email)->get()->row();
       
            if($q == ""){
                return array('status' => 201,'message' => 'Email address not found.');
            } else {
                $id = $q->id;
                $hashed_password = $q->password;
                
                 //echo $hashed_password ." ".$password;
            //exit;
                $password = $this->ion_auth->hash_password_db($id, $password);

                if ($password === TRUE)
                {
                    $full_name = $q->full_name;
                    $last_login = date('Y-m-d H:i:s');
                    $token = crypt(substr( md5(rand()), 0, 7));
                    $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
                    $this->db->trans_start();
                    $this->db->where('id',$id)->update('users',array('last_login' => $last_login));
                    $this->db->insert('users_authentication',array('users_id' => $id,'token' => $token,'expired_at' => $expired_at));
                    if ($this->db->trans_status() === FALSE){
                        $this->db->trans_rollback();
                        return array('status' => 500,'message' => 'Internal server error.');
                    }
                    else {
                        $this->db->trans_commit();
                        return array('status' => 200,'message' => 'Successfully login.','id' => $id, 'name' => $full_name, 'token' => $token);
                   }
                }
                else {
                   return array('status' => 201,'message' => 'Wrong password.');
                }
            }
        }
    }
    public function login($username,$password,$social)
    {
        $q  = $this->db->select('password,id')->from('users')->where('email',$username)->get()->row();
       
        if($q == ""){
            return array('status' => 201,'message' => 'Email address not found.');
        } else {
            $id = $q->id;
            if($social == 1)
            {
                $last_login = date('Y-m-d H:i:s');
                $token = crypt(substr( md5(rand()), 0, 7));
                $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
                $this->db->trans_start();
                $this->db->where('id',$id)->update('users',array('last_login' => $last_login));
                $this->db->insert('users_authentication',array('users_id' => $id,'token' => $token,'expired_at' => $expired_at));
                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    return array('status' => 500,'message' => 'Internal server error.');
                }
                else {
                    $this->db->trans_commit();
                    return array('status' => 200,'message' => 'Successfully login.','id' => $id, 'token' => $token);
               }
            }
            else{
                $hashed_password = $q->password;
                
                 //echo $hashed_password ." ".$password;
            //exit;
                $password = $this->ion_auth->hash_password_db($id, $password);

                if ($password === TRUE)
                {
                    $last_login = date('Y-m-d H:i:s');
                    $token = crypt(substr( md5(rand()), 0, 7));
                    $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
                    $this->db->trans_start();
                    $this->db->where('id',$id)->update('users',array('last_login' => $last_login));
                    $this->db->insert('users_authentication',array('users_id' => $id,'token' => $token,'expired_at' => $expired_at));
                    if ($this->db->trans_status() === FALSE){
                        $this->db->trans_rollback();
                        return array('status' => 500,'message' => 'Internal server error.');
                    }
                    else {
                        $this->db->trans_commit();
                        return array('status' => 200,'message' => 'Successfully login.','id' => $id, 'token' => $token);
                   }
                }
                else {
                   return array('status' => 201,'message' => 'Wrong password.');
                }
            }
        }
    }
    public function logout($user_id,$token)
    {
        //$users_id  = $this->input->get_request_header('User-ID', TRUE);
        //$token     = $this->input->get_request_header('Authorization', TRUE);
        $this->db->where('users_id',$user_id)->where('token',$token)->delete('users_authentication');
        return array('status' => 200,'message' => 'Successfully logout.');
    }
    public function create($data)
    {
        $email = $data['email'];
        $phone = $data['phone'];
        $fullname = $data['full_name'];
        $gender = $data['gender'];

        $q  = $this->db->select('email')->from('users')->where('email',$email)->get()->row();
        $q1 = $this->db->select('password,id')->from('users')->where('phone',$phone)->get()->row();
        if($q) {
            return array('status' => 201,'message' => 'This email id is already registered.');
        }
        elseif($q1) {
            return array('status' => 201,'message' => 'This phone number is already in use.');
        }
        else{
            $salt       = $this->store_salt ? $this->salt() : FALSE;
            $password = $data['password'];
            $password   = $this->ion_auth->hash_password($password, $salt);
            $data1 = array(
                "full_name" => $fullname,
                "email"     => $email,
                "phone"     => $phone,
                "gender"    => $gender,
                "username"  => $email,
                "password"  => $password,
                "active"    => 1,
                "social"  => 0
            );
            $this->db->insert('users',$data1);
            $id = $this->db->insert_id();
            $data2 = array(
                "user_id"   => $id,
                "group_id"  => 2
            );
            $this->db->insert('users_groups',$data2);
            return array('status' => 200,'id' => $id,'message' => 'User has been registered successfully.');
        }
    }
    public function userdetail($id) {
        $q  = $this->db->select('id,full_name,email,phone,gender')->from('users')->where('id',$id)->get()->row();
        return $q;
    }
    public function users() {
        $q  = $this->db->select('id,full_name,email,phone')->from('users')->order_by('id','desc')->get()->result();;
        return $q;
    }
    public function auth()
    {
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token     = $this->input->get_request_header('Authorization', TRUE);
        $q  = $this->db->select('expired_at')->from('users_authentication')->where('users_id',$users_id)->where('token',$token)->get()->row();
        if($q == ""){
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        } else {
            if($q->expired_at < date('Y-m-d H:i:s')){
                return json_output(401,array('status' => 401,'message' => 'Your session has been expired.'));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
                $this->db->where('users_id',$users_id)->where('token',$token)->update('users_authentication',array('expired_at' => $expired_at,'updated_at' => $updated_at));
                return array('status' => 200,'message' => 'Authorized.');
            }
        }
    }
    public function forgot($email)
    {
        $q  = $this->db->select('id,full_name,email')->from('users')->where('email',$email)->get()->row();
        if($q)
        {
            return  array('status' => 200, 'q' => $q);
        }
        else
        {
            return array('status' => 201,'message' => 'This email id is not registered with us.');
        }
    }
    public function forgot_code($params)
    {
        $email = $params['email'];
        $code = $params['code'];
        $q = $q  = $this->db->select('*')->from('users')->where('email',$email)->get()->row();
        if($q)
        {
            if($code == $q->forgotten_password_code)
            {
                $cTime = date("Y-m-d H:i:s");
                $cTime = date_create($cTime);
                $rTime = $q->forgotten_password_time;
                $rTime = date_create($rTime);
                $tDiff = date_diff($cTime,$rTime);
                $diff = intval($tDiff->format('%h'));
                if($diff >= 2)
                {
                    return array('status' => 201,'message' => 'The code has been expired, Please request a new code');
                }
                else{
                    return array('status' => 200,'message' => 'Code matched, Please enter new password');
                }
            }
            else{
               return array('status' => 201,'message' => 'The code you have entered is not correct'); 
            }
            
        }
        else
        {
            return array('status' => 201,'message' => 'This email id is not registered with us.');
        }
    }
    public function reset($params)
    {
        $email = $params['email'];
        $password = $params['password'];
        $q = $q  = $this->db->select('id')->from('users')->where('email',$email)->get()->row();
        if($q)
        {
            $salt       = $this->store_salt ? $this->salt() : FALSE;
            $password   = $this->ion_auth->hash_password($password, $salt);
            $data = array(
                'password' => $password,
                'forgotten_password_code' => '',
                'forgotten_password_time' => ''
            );
            $this->db->where('id',$q->id)->update('users',$data);
            return array('status' => 200,'message' => 'Password reset successfully');
        }
        else{
            return array('status' => 201,'message' => 'This email id is not registered with us.');
        }
    }
    public function change_password($params) {
        $uid = $params['user_id'];
        $old = $params['old_password'];
        $new = $params['new_password'];
        $q = $this->db->select('email, password, salt')->from('users')->where('id',$uid)->get()->row();
        $old_password_matches = $this->ion_auth->hash_password_db($uid, $old);
        if ($old_password_matches === TRUE)
        {
            $hashed_new_password  = $this->ion_auth->hash_password($new, $q->salt);
            $data = array(
                'password' => $hashed_new_password,
                'remember_code' => NULL,
            );
            $this->db->where('id',$uid)->update('users',$data);
            return array('status' => 200,'message' => 'Password changed successfully');
        }
        else{
            return array('status' => 201,'message' => 'Error in changing password');
        }
    }
    public function generate_rcode($digit,$type)
    {
      /* list all possible characters, similar looking characters and vowels have been removed */
      if($type=="both")
      $possible = '123456789abcdefghijklmnopqrstuvwxyz';
      else if($type="dig")
      $possible = '123456789';
      $code = '';
      $i = 0;
      while ($i < $digit) { 
       $code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
       $i++;
      }
      return strtoupper($code); 
    }
    
    function alert_email_server($toemail, $subj, $msg)
    {
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
