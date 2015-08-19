<?php
class Comment extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->load->database();
	}

	function addComment($comment) {
		$this->db->insert('comment',$comment);
		$comment_id = $this->db->insert_id();
		return $comment_id;
	}

	function get_album_comment($albumid) {
		$this->db->where('albumid',$albumid);
		$this->db->select('*');
		$query = $this->db->get('comment');
		return $query->result();
	}
}