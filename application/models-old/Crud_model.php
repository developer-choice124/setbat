<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class Crud_model extends CI_Model 
	{
        
		function __construct()
        {
            parent::__construct();
        }
		
        public function hash_password($password)
	{
		$salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		$hash = hash('sha256', $salt . $password);

		return $salt . $hash;
	}

        public function insert_record($tbl,$data)
		{
            if($this->db->insert($tbl,$data))
			{
				return TRUE;
			} else {
				return FALSE;
			}	
        }
		
        public function edit_record($tbl,$id,$data)
		{
            $this->db->where('id',$id);
            if($this->db->update($tbl,$data))
			{ 
				return true;
            } else {
				return false;	
            }
        }
		
		function countrow($qry)
		{
			$query = $this->db->query($qry);
			$rowcount = $query->num_rows();
			return $rowcount;
		}
		
		
		public function edit_record_resource($tbl,$id,$data)
		{
            $this->db->where('r_id',$id);
            if($this->db->update($tbl,$data))
			{ 
				return true;
            } else {
				return false;	
            }
        }
		
		public function edit_record_by_uhid($tbl,$id,$data)
		{
            $this->db->where('uhid',$id);
            if($this->db->update($tbl,$data))
			{ 
				return true;
            } else {
				return false;	
            }
        }
		
				
		public function edit_record_by_anyid($tbl,$id,$data,$where)
		{
            $this->db->where($where,$id);
            if($this->db->update($tbl,$data))
			{ 
				return true;
            } else {
				return false;	
            }
        }
		
		
		
		public function edit_record_by_admit_uhid($tbl,$id,$data)
		{
            $this->db->where('admit_id',$id);
            if($this->db->update($tbl,$data))
			{ 
				return true;
            } else {
				return false;	
            }
        }
		
				
		 public function edit_record_by_multiplecondition($tbl,$id_name,$id,$condi,$no,$data)
		{
            $this->db->where($id_name,$id);
			 $this->db->where($condi,$no);
            if($this->db->update($tbl,$data))
			{ 
				return true;
            } else {
				return false;	
            }
        }
		
		
        public function delete_record($tbl,$id)
		{
            $this->db->where('id', $id);
			if($this->db->delete($tbl))
			{ 
				return true;
			} else {
				return false;	
			}
        }
		
		  public function delete_record_any_id($tbl,$id,$idname)
		{
		
		$this->db->delete($tbl,array($idname=>$id));
          
        }	
		
		
		
		public function get_active_record($tbl)
		{
			$this->db->from($tbl);
			$this->db->where('status',1);
			$query = $this->db->get();
			return $query->result_array();
		}
		
		public function remove_record($tbl,$condition)
		{
			$this->db->where($condition);
			$this->db->delete($tbl);
			return;	
		}
			 public function edit_record_xray($tbl,$id,$data)
		{
            $this->db->where('xray_id',$id);
            if($this->db->update($tbl,$data))
			{ 
				return true;
            } else {
				return false;	
            }
        }
		 public function edit_record_by_any_id($tbl,$id_name,$id,$data)
		{
            $this->db->where($id_name,$id);
            if($this->db->update($tbl,$data))
			{ 
				return true;
            } else {
				return false;	
            }
        }

        public function edit_record_by_any_two_field($tablename,$firstid,$secondid,$dataarray,$attribute1,$attribute2)
		{
            $this->db->where($attribute1,$firstid);
            $this->db->where($attribute2,$secondid);
            if($this->db->update($tablename,$dataarray))
			{ 
				return true;
            } else {
				return false;	
            }
        }

        
		
		public function Invoiceqty($invoiceno)
		{
			$query = $this->db->query("SELECT  sum(purch_totlqty) as totqty  FROM purchase	WHERE purch_billno='$invoiceno'");
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['totqty'];
			} else {
			}
		}
		
		
		public function InvoiceAmount($invoiceno)
		{
				 $rate=0;
				 $tax=0;
				 $disc=0;
				 $finalamt=0;
				 $gtotal=0;
				 $netcal=0;
				 $data['purchase'] = $this->Common_model->get_data_by_query("select purch_rate,purch_vat,purch_totlqty,purch_disc from purchase
				 WHERE purch_billno='".$invoiceno."'");
				 $tax=$data['purchase'][0]['purch_vat'];
				 $disc=$data['purchase'][0]['purch_disc'];
				 foreach ($data['purchase'] as $key=>$row)
						{
						 $gtotal +=$row['purch_rate']*$row['purch_totlqty'];
							
						}
				 $netcal=($gtotal*$tax)/100;
				 $finalamt=$netcal+$gtotal;
				 return $finalamt-$disc;
		}
		
		
		public function MaxEmpCode()
		{
			$query = $this->db->query("SELECT max(emp_code) as mcode  FROM employee  WHERE 1");
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['mcode']+1;
			} else {
			}
		}
		
		
		public function Currentpackage($empid)
		{
			$query = $this->db->query("select emppac_packid from hr_emppackage	WHERE emppac_emp_id='$empid' order by emppac_id desc limit 1  ");
			// echo $this->db->last_query();
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['emppac_packid'];
			} else {
			}
		}
		
		public function Currentpackageid($empid)
		{
			$query = $this->db->query("select emppac_id from hr_emppackage	WHERE emppac_emp_id='$empid' order by emppac_id desc limit 1  ");
			// echo $this->db->last_query();
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['emppac_id'];
			} else {
			}
		}
		
		public function BasepackageEfftiveDT($empid)
		{
			$query = $this->db->query("select emppac_entrydt from hr_emppackage 
			WHERE emppac_emp_id='$empid' order by emppac_id desc limit 1  ");
			// echo $this->db->last_query();
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['emppac_entrydt'];
			} else {
			}
		}
		
		public function Basepackage($empid)
		{
			$query = $this->db->query("select emppac_packid from hr_emppackage 
			WHERE emppac_emp_id='$empid' order by emppac_id desc limit 1  ");
			// echo $this->db->last_query();
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['emppac_packid'];
			} else {
			}
		}
		
		public function shiftAllotedId($empid)
		{
			$query = $this->db->query("SELECT shiftallot_shiftid FROM hr_shift_alloted WHERE shiftallot_empid='$empid' order by shiftallot_id desc limit 1");
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['shiftallot_shiftid'];
			} else {
			}
		}
		
		
		public function shiftAllotedName($shiftid)
		{
			$query = $this->db->query("SELECT shift_name  FROM hr_shift  WHERE shift_id='$shiftid'");
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['shift_name'];
			} else {
			}
		}
		
			public function EmployeePFNo($empid)
		{
			$query = $this->db->query("SELECT pf_no  FROM hr_pf  WHERE pf_empid='$empid'");
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['pf_no'];
			} else {
			}
		}
		
		public function EmployeePFApplicable($empid)
		{
			$query = $this->db->query("SELECT pf_yesno  FROM hr_pf  WHERE pf_empid='$empid'");
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['pf_yesno'];
			} else {
			}
		}
		
		public function Incramt($empid,$month,$year)
		{
			$query = $this->db->query("SELECT sum(`incr_dir_amt`) as finalamt FROM `hr_increment` WHERE `incr_empid` = '$empid' and date_format(`incr_month`,'%m') <= $month and date_format(incr_month , '%Y') <= $year");
			$row = $query->row_array();
			if ($query->num_rows() > 0) 
			{
				return $row['finalamt'];
			}
			else
			{
			}
		}
		
		
		public function shift_duration($shiftid)
		{
			$query = $this->db->query("SELECT shift_tothours FROM emp_shift_master WHERE shift_id='$shiftid'");
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['shift_tothours'];
			} else {
			}
		}
		
		
		
		public function gateadvance($empid,$month,$year)
		{
			$query = $this->db->query("select sum(gateadv_amount) as togateadv from hr_gateadv where gateadv_empid='$empid' and 
			date_format(gateadv_date,'%m')=".$month." and date_format(gateadv_date,'%Y')=".$year." and gateadv_status=1");
			// $this->db->last_query();
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['togateadv'];
			} 
		}
		
		
		public function Currentchallan($empid,$month,$year)
		{
			$query = $this->db->query("SELECT sum(chal_dig_amount) as chaamt FROM hr_challan WHERE chal_approve !=2 and chal_empid='$empid' and date_format(chal_date,'%m')=$month and date_format(chal_date,'%Y')=$year");
			
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['chaamt'];
			} 
		}
		
		public function CurrAdvDeduMonthly($empid,$month,$year)
		{
			
			$query = $this->db->query("SELECT sum(emi_dedu_amt) as emi_dedu_amt  FROM hr_adv_emi_details e
			inner join hr_adv a on a.adv_id=e.emi_advance_id
			 WHERE emi_status=1 and adv_approved_status=1 and emi_empid='$empid' and emi_month=$month and emi_year=$year");
			
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['emi_dedu_amt'];
			} 
		}
		
		
		public function security_of_a_month($empid,$m,$y)
		{
			$query = $this->db->query("select sum(sec_emi_amt) as security from hr_sec_emi_tran where sec_emi_emp='$empid' and sec_emi_month='$m' and sec_emi_year ='$y' and sec_emi_tran_status = 1");
			// $this->db->last_query();
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['security'];
			} 
		}
		
		
		public function Totalusers($gname)
		{
			$query = $this->db->query("select count(*) as totuser from groups	WHERE name='$gname'");
			// echo $this->db->last_query();
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['totuser'];
			} else {
			}
		}

		public function Totusers($gname)
		{
			$query = $this->db->query("select count(*) as totuser from users_groups	WHERE group_id='$gname'");
			// echo $this->db->last_query();
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['totuser'];
			} else {
			}
		}

		

		public function Expquestion($gname)
		{
			$query = $this->db->query("select count(*) as totq from savsoft_qbank	WHERE user_id='$gname'");
			// echo $this->db->last_query();
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				return $row['totq'];
			} else {
			}
		}


	function import_testers($testers) {
//echo "<pre>"; print_r($question);exit;
        $client_cat = $this->input->post('client_cat');
     //   $questiondid = $this->input->post('did');

 $logged_in = $this->session->userdata('logged_in');
     
        $client_id = $logged_in['id'];


        foreach ($testers as $key => $tester) {
            //$ques_type= 
//echo $ques_type; 

            if ($key != 0) {
                echo "<pre>";
                print_r($tester);
                $tester_name = str_replace('"', '&#34;', $tester['0']);
                $tester_name = str_replace("`", '&#39;', $tester_name);
                $tester_name = str_replace("‘", '&#39;', $tester_name);
                $tester_name = str_replace("’", '&#39;', $tester_name);
                $tester_name = str_replace("â€œ", '&#34;', $tester_name);
                $tester_name = str_replace("â€˜", '&#39;', $tester_name);
                $tester_name = str_replace("â€™", '&#39;', $tester_name);
                $tester_name = str_replace("â€", '&#34;', $tester_name);
                $tester_name = str_replace("'", "&#39;", $tester_name);
                $tester_name = str_replace("\n", "<br>", $tester_name);
                

                $tester_email = str_replace('"', '&#34;', $tester['1']);
                $tester_email = str_replace("`", '&#39;', $tester_email);
                $tester_email = str_replace("‘", '&#39;', $tester_email);
                $tester_email = str_replace("’", '&#39;', $tester_email);
                $tester_email = str_replace("â€œ", '&#34;', $tester_email);
                $tester_email = str_replace("â€˜", '&#39;', $tester_email);
                $tester_email = str_replace("â€™", '&#39;', $tester_email);
                $tester_email = str_replace("â€", '&#34;', $tester_email);
                $tester_email = str_replace("'", "&#39;", $tester_email);
                $tester_email = str_replace("\n", "<br>", $tester_email);

  
$q = $this->db->query("SELECT tester_email FROM testers WHERE tester_email  = '$tester_email' and   tester_category  = '$client_cat'  LIMIT 1 ");
$data = array_shift($q->result_array());
  
        if(empty($data))

        {

                $insert_data = array(
                    'tester_name' => $tester_name,
                    'tester_email' => $tester_email,
                    'tester_category' => $client_cat,
                    'client_id' => $client_id
                );

             $this->db->insert('testers', $insert_data);
           }

            }

        }
    }	






    	function import_account($accounts) {



        foreach ($accounts as $key => $tester) {
            //$ques_type= 
//echo $ques_type; 

            if ($key != 0) {
                echo "<pre>";
                print_r($tester);
                $tester_name = str_replace('"', '&#34;', $tester['0']);
                $tester_name = str_replace("`", '&#39;', $tester_name);
                $tester_name = str_replace("‘", '&#39;', $tester_name);
                $tester_name = str_replace("’", '&#39;', $tester_name);
                $tester_name = str_replace("â€œ", '&#34;', $tester_name);
                $tester_name = str_replace("â€˜", '&#39;', $tester_name);
                $tester_name = str_replace("â€™", '&#39;', $tester_name);
                $tester_name = str_replace("â€", '&#34;', $tester_name);
                $tester_name = str_replace("'", "&#39;", $tester_name);
                $tester_name = str_replace("\n", "<br>", $tester_name);
                

                $tester_email = str_replace('"', '&#34;', $tester['1']);
                $tester_email = str_replace("`", '&#39;', $tester_email);
                $tester_email = str_replace("‘", '&#39;', $tester_email);
                $tester_email = str_replace("’", '&#39;', $tester_email);
                $tester_email = str_replace("â€œ", '&#34;', $tester_email);
                $tester_email = str_replace("â€˜", '&#39;', $tester_email);
                $tester_email = str_replace("â€™", '&#39;', $tester_email);
                $tester_email = str_replace("â€", '&#34;', $tester_email);
                $tester_email = str_replace("'", "&#39;", $tester_email);
                $tester_email = str_replace("\n", "<br>", $tester_email);

                $tester_type = str_replace('"', '&#34;', $tester['2']);
                $tester_amount  = str_replace('"', '&#34;', $tester['3']);
                $tester_date  = str_replace('"', '&#34;', $tester['4']);

  

  
        if(1)

        {

                $insert_data = array(
                    'name' => $tester_name,
                    'desc' => $tester_email,
                    'amount' => $tester_amount,
                    'inc_exp' => $tester_type,
                    'date' => $tester_date
                );

             $this->db->insert('accounting', $insert_data);
           }

            }

        }
    }	
		
	function getstate($country_id='')
     {
        $this ->db->select('jas_state.*');
        $this ->db->where('country_id', $country_id);
        $query = $this->db->get('jas_state');
        return $query->result();
     }
    
      function getcity($state_id='')
     {
        $this->db->select('jas_city.*');
        $this->db->where('state_id', $state_id);
        $query = $this->db->get('jas_city');
        return $query->result();
     }
	//---------------------------------------------End Class brasses-----------------------------------------------	
    }

?>
