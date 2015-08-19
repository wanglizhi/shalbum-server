<?php
class Zan extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->load->database();
	}

	function addZan($zan) {
		$this->db->insert('zan',$zan);
		$zan_id = $this->db->insert_id();
		$query_string = 'update album set zancount = zancount + 1 where albumid = '.$zan['albumid'].';';
		$this->db->query($query_string);
		return $zan_id;
	}

	function deleteZan($userid,$albumid) {
		$query_string = 'delete from zan where userid = '.$userid.' and albumid = '.$albumid.';';
		$query = $this->db->query($query_string);
		$query_string2 = 'update album set zancount = zancount - 1 where albumid = '.$zan['albumid'].';';
		$this->db->query($query_string2);
		return $this->db->affected_rows();
	}

	function get_album_zan($albumid) {
		$this->db->where('albumid',$albumid);
		$this->db->select('*');
		$query = $this->db->get('zan');
		return $query->result();
	}

	function get_user_zan($userid) {
		$this->db->where('userid',$userid);
		$this->db->select('*');
		$query = $this->db->get('zan');
		return $query->result_array();	
	}
}