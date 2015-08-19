<?php
class Tag extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->load->database();
	}
	function addTag($tag) {


		$this->db->insert('tag',$tag);
		$tagid = $this->db->insert_id();
		return $tagid;
	}
	function getTag($tagid) {
		$this->db->where('tagid',$tagid);
		$this->db->select('*');
		$query = $this->db->get('tag');
		return $query->first_row('array');
	}
	function getTagByContent($content) {
		
	}
	function addAlbumTag($album_tag) {
		$this->db->insert('album_tag',$album_tag);
		$album_tagid = $this->db->insert_id();
		return $album_tagid;
	}
	function getAlbumTag($albumid) {
		$this->db->where('albumid',$albumid);
		$this->db->select('*');
		$query = $this->db->get('album_tag');
		$album_tag = $query->result();
		$tags = array();
		foreach ($album_tag as $value) {
			$tags[$value['tagid']] = getTag($value['tagid']);
		}
		return $tags;
	}
}