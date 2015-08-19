<?php
class Photo extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->load->database();
	}

	function addPhoto($data) {
		$this->db->insert('photo',$data);
		$photo_id = $this->db->insert_id();
		return $photo_id;
	}

	function upload_photo($photoid,$content) {
		$query_string = 'update photo set content = "'.$content.'" where photoid = '.$photoid.';';
		$query = $this->db->query($query_string);
		return $this->db->affected_rows();
	}

	function getPhoto($albumid) {
		$this->db->where('albumid',$albumid);
		$this->db->select('*');
		$query = $this->db->get('photo');
		return $query->result();
	}
}