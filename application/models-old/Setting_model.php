<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	function get_setting()
	{
		return $this->db->get('settings')->row();
	}
	
	
	function update($save)
	{
		$this->db->where('id',1);
		$this->db->update('settings',$save);
	}
	
	
}