<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	public function FirstDayOfCurrentMonth(){
        $row=$this->db->query("SELECT MAKEDATE(YEAR(CURDATE()), 1) + INTERVAL month(CURDATE()) month- INTERVAL 1 month dt")->row();
        return $row->dt;
    }
    public function LastDayOfCurrentMonth(){
        $row=$this->db->query("SELECT MAKEDATE(YEAR(CURDATE()), 1) + INTERVAL month(CURDATE()) month- INTERVAL 1 day dt ")->row();
        return $row->dt;
    }
    public function FirstDayOfCurrentWeek(){
        $row=$this->db->query("Select  SUBDATE(curdate(), WEEKDAY(curdate())) dt ")->row();
        return $row->dt;
    }
    public function LastDayOfCurrentWeek(){
        $row=$this->db->query("Select  ADDDATE(curdate(), 6-WEEKDAY(curdate())) dt ")->row();
        return $row->dt;
    }
    
	public function Totnotification()
	{
		$query = $this->db->query("select count(*) as totnot from notification	WHERE noti_status = 0");
			// echo $this->db->last_query();
		$row = $query->row_array();
		if ($query->num_rows() > 0) {
			return $row['totnot'];
		} else {
		}
	}

	public function get_data_by_limit($limit, $start,$table) {
        $this->db->select('bet.*,users.username');
        $this->db->limit($limit, $start);
        $this->db->join('users', 'users.id = bet.user_id');
        $this->db->order_by('bet.id', 'DESC');
        $query = $this->db->get($table);
        return $query->result_array();
    }

    public function record_count($table) {
		return $this->db->count_all($table);
	}

	public function get_all_data($tbl)
	{
		$this->db->from($tbl);
		$this->db->order_by('createdate','desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function insert1($table,$data)
	{
		$this->db->insert($table,$data);
	}

	public function update_record($table,$updateid,$data)
	{
		$this->db->where('id',$updateid);
		$this->db->update($table,$data);
	}


	public function get_data($qry)
	{
		$query = $this->db->query($qry);	
		return $query;
	}

	public function generate_id($table)
	{
		$this->db->select('max(id) as id');
		$this->db->from($table);
		$query = $this->db->get();
		return $query->row();
	}

	public function findfield($table,$fieldname1,$fieldvalue1,$returnfield)
	{
		$this->db->select($returnfield);
		$this->db->from($table);
		$this->db->where($fieldname1,$fieldvalue1);
		$query = $this->db->get();
		foreach ($query->result() as $value)
		{}
		return @$value->$returnfield;
	}


	public function get_record_by_id($tbl,$id)
	{
		$this->db->from($tbl);
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->row();
	}


	public function search_record($tbl,$code="",$name="",$dt_from="",$dt_to="")
	{
		$this->db->from($tbl);

		if($code!="" && $name=="" && $dt_from=="")
		{
			$this->db->where('code',$code);	
		}else if($code=="" && $name !="" && $dt_from=="")
		{
			$this->db->where('fname',$name);		
		}else if($code=="" && $name =="" && $dt_from !="")
		{
			$this->db->where('name',$name);	
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_limited_record($tbl,$limit, $start,$groupby=''){
		$this->db->limit($limit, $start);
		if(!empty($groupby))
		{
			$this->db->group_by($groupby);	
		}
		$query = $this->db->get($tbl);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;	
	}

	public function get_total_record($tbl,$groupby=''){
		$this->db->from($tbl);
		if(!empty($groupby))
		{
			$this->db->group_by($groupby);	
		}
		$query = $this->db->get();
		return $query->num_rows();	
	}

	public function get_multiple_record_byid($tbl,$id,$field="id")
	{
		$this->db->from($tbl);
		$this->db->where($field,$id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_data_from_two_table($tbl1,$tbl2,$fields,$on_cond,$condition=''){
		$this->db->select($fields);
		$this->db->from($tbl1);
		$this->db->join($tbl2,$on_cond);
		if(!empty($condition))
		{
			$this->db->where($condition);	
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_data_from_three_table($tbl1,$tbl2,$tbl3,$fields,$on_cond1,$on_cond2,$condition=""){
		$this->db->select($fields);
		$this->db->from($tbl1);
		$this->db->join($tbl2,$on_cond1);
		$this->db->join($tbl3,$on_cond2);
		if(!empty($condition))
		{
			$this->db->where($condition);	
		}
		$this->db->order_by('id','desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_specific_field_byid($tbl,$fields,$cond)
	{
		$this->db->select($fields);
		$this->db->from($tbl);
		$this->db->where($cond);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_by_query($qry)
	{
		$query = $this->db->query($qry);	
		return $query->result_array();
	}
	public function get_single_query($qry)
	{
		$query = $this->db->query($qry);	
		return $query->row();
	}
	public function ReadRaw($sql) {
        return $this->db->query($sql)->result();
    }
	public function get_data_json_query($qry)
	{
		$query = $this->db->query($qry);	
		foreach ($query->result() as $row)
      {
        $data[] = $row;
      }
      return $data;
	}
	public function get_record_by_fieldvalue($tbl,$field,$value)
	{
		$this->db->from($tbl);
		$this->db->where($field,$value);
		$query = $this->db->get();
		return $query->row();
	}



	public function FillDynamicCombo($query)  {	
		
		$data['result'] = $this->Common_model->get_data_by_query("$query");

		$option="";
		foreach ($data['result'] as $key=>$value)
		{

			$option= $option." "."<option>".$value['dep_name']."</option>";
		}


		return $option;
	}





	public function Generate($digit,$type)
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






	public function Generate_rcode($digit,$type)
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
    function alert_local_email($toemail, $subj, $msg)
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


    public function SaveNotification($event, $desc, $other_user){
    	$title='';
		//$user=0;
    	$user = $this->Common_model->get_data_by_query("select * from users_groups where group_id = 5");
		//$user=array_slice($this->session->userdata,9,1);
    	foreach ($user as $key => $value) {

    		$ids[] = $value['user_id']; 
    		$user_id = implode(',', $ids);
    	}
		//print_r($user_id); die;

    	switch($event)
    	{
    		case 1:
    		$title='New Order Placed';
    		break;

    		case 2:
    		$title='Order Assigned to Vendor';
    		break;

    		case 3:
    		$title='New Order From Front Placed';
    		break;
    		case 4:
    		$title='New User Created';
    		break;
    		case 5:
    		$title='Message From Front';
    		break;
    	}

    	$data['noti_event'] = $event;
    	$data['noti_title'] = $title;
    	$data['noti_desc'] = $desc;
    	$data['noti_user'] = $user_id;
		$data['noti_other_users'] = 0; //eg: 1,5,6
		$data['noti_entrydt'] = date('Y-m-d H:i:s');
		//print_r($data);
		$this->Crud_model->insert_record('notification',$data);
		//------------- Send Email --------------//

	}
}

?>
