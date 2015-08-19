<?php
class Album extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->load->database();
	}

	function addAlbum($data) {
		$this->db->insert('album',$data);
		$album_id = $this->db->insert_id();
		$query_string = 'update user set albumcount = albumcount +1 where userid = '.$data['userid'].';';
		$query = $this->db->query($query_string);
		return $album_id;
	}

	function getAlbum($userid) {
		$this->db->where('userid',$userid);
		$this->db->select('*');
		$query = $this->db->get('album');
		return $query->result();
	}

	function getAllAlbum() {
		$this->db->select('*');
		$query = $this->db->get('album');
		return $query->result_array();
	}

	function getFollowsAlbum($pageid,$userid) {
		$query_string = 'select * from album where userid in (select user_fans.userid from user_fans where user_fans.fansid = '.$userid.') order by uptime desc limit '.($pageid * 10).',10;';
		$query = $this->db->query($query_string);
		return $query->result_array();
	}

	function getAlbumByPage($pageid, $userid) {
		$query_string = 'select * from album order by uptime desc limit '.($pageid * 10).',10;';
		$query = $this->db->query($query_string);
		return $query->result_array();
	}

	function getHotAlbumByPage($pageid, $userid) {
		$query_string = 'select * from album order by zancount desc limit '.($pageid * 10).',10;';
		$query = $this->db->query($query_string);
		return $query->result_array();
	}

	function getStoreAlbumByPage($pageid, $userid) {
		$query_string = 'select * from album where albumid in (select store.albumid from store where store.userid = '.$userid.' ) order by uptime desc limit '.($pageid * 10).',10;';
		$query = $this->db->query($query_string);
		return $query->result_array();
	}

	function getAlbumByTag($pageid, $tag) {
		$query_string = 'select * from album where tag = \''.$tag.'\' order by uptime desc limit '.($pageid * 10).',10;';
		$query = $this->db->query($query_string);
		return $query->result_array();
	}
}