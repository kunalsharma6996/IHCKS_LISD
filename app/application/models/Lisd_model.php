<?php 

	/**
	* 
	*/
	class Lisd_model extends CI_Model
	{
		
		function __construct()
		{
			parent::__construct();
		}

		function insert_user()
		{
			$this->db->where('status',1);
			$query=$this->db->get('sliders');
			return $query->result_array();
		}

		function fetch_sliders()
		{
			$query=$this->db->get('sliders');
			return $query->result_array();
		}

		function add_slider($data)
		{
			
			$this->db->insert('sliders',$data);

		}

		function disable_id($id)
		{
			$this->db->where('slider_id',$id);
			$data['status']=0;
			$this->db->update('sliders',$data);
		}

		function enable_id($id)
		{
			$this->db->where('slider_id',$id);
			$data['status']=1;
			$this->db->update('sliders',$data);
		}
		
	}

 ?>