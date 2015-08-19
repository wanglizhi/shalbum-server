<?php
class User extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->load->database();
	}
	function getAllUser() {
		$this->db->select('*');
		$query = $this->db->get('user');
		$data = $query->result();
		return $data;
	}
	function getRecommendUser($userid) {
		$query_string = 'select * from user where userid <> '.$userid.' and userid not in (select user_fans.userid from user_fans where user_fans.fansid = '.$userid.');';
		$query = $this->db->query($query_string);
		$data = $query->result_array();
		return $data;
	}
	function getUser($name) {
		$this->db->where('username',$name);
		$this->db->select('*');
		$query = $this->db->get('user');
		$data = $query->first_row('array');
		return $data;
	}
	function getUserById($userid) {
		$this->db->where('userid',$userid);
		$this->db->select('*');
		$query = $this->db->get('user');
		$data = $query->first_row('array');
		return $data;
	}
	function addUser($userdata) {
		$this->db->insert('user',$userdata);
		$user_id = $this->db->insert_id();
		return $user_id;
	}
	function addFace($userid,$url) {
		$query_string = 'update user set face = "'.$url.'" where userid = '.$userid.';';
		$query = $this->db->query($query_string);
		return $this->db->affected_rows();
	}
	function addFans($user_fans) {
		$this->db->insert('user_fans',$user_fans);
		$user_fansid = $this->db->insert_id();
		$addCount = $this->db->affected_rows();
		$query_string = 'update user set fanscount = fanscount + '.$addCount.' where userid = '.$user_fans['userid'].';';
		$query = $this->db->query($query_string);
		$query_string2 = 'update user set followcount = followcount + '.$addCount.' where userid = '.$user_fans['fansid'].';';
		$query2 = $this->db->query($query_string2);
		return $user_fansid;
	}
	function deleteFans($user_fans) {
		$query_string = 'delete from user_fans where userid = '.$user_fans['userid'].' and fansid = '.$user_fans['fansid'].';';
		$query = $this->db->query($query_string);
		$deleteCount = $this->db->affected_rows();
		$deduct_string = 'update user set fanscount = fanscount - '.$deleteCount.' where userid = '.$user_fans['userid'].';';
		$query_deduct = $this->db->query($deduct_string);
		$deduct_string2 = 'update user set followcount = followcount - '.$deleteCount.' where userid = '.$user_fans['fansid'].';';
		$query_deduct2 = $this->db->query($deduct_string2);
		return $deleteCount;
	}
	function getFans($userid) {
		$this->db->where('userid',$userid);
		$this->db->select('*');
		$query = $this->db->get('user_fans');
		$user_fans = $query->result_array();
		$users = array();
		foreach ($user_fans as $user_fan) {
            array_push($users,$this->getUserById($user_fan['fansid']) );
		}
		return $users;
	}
	function getFollows($fansid) {
		$this->db->where('fansid',$fansid);
		$this->db->select('*');
		$query = $this->db->get('user_fans');
		$user_fans = $query->result_array();
		$users = array();
		foreach ($user_fans as $user_fan) {
			array_push($users,$this->getUserById($user_fan['userid']) );
		}
		return $users;
	}
}