<?php
class Store extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->load->database();
	}

	function addStore($store) {
		$this->db->insert('store',$store);
		$store_id = $this->db->insert_id();
		return $store_id;
	}

	function deleteStore($userid,$albumid) {
		$query_string = 'delete from store where userid = '.$userid.' and albumid = '.$albumid.';';
		$query = $this->db->query($query_string);
		return $this->db->affected_rows();
	}

	function get_user_store($userid) {
		$this->db->where('userid',$userid);
		$this->db->select('*');
		$query = $this->db->get('store');
		return $query->result_array();	
	}
}