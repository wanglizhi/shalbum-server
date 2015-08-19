<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require 'static.php';
class UserC extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->helper('form','url');
		session_start();
	}

	function get_me() {
		if(isset($_SESSION['userid'])) {
			echoJson(10001,'no login');	
			exit;
		} 
		echoJson(300,'you got success',array('User' => $_SESSION['userid']));
	}

	function get_all_user() {
		$this->load->model('User');
		$users = $this->User->getAllUser();
		echoJson(032,'get user success',array('User' => $users));
	}

	function get_recommend_user() {
		if(!isset($_SESSION['userid'])) {
			echoJson(10001,'no login');
			exit;
		}
		$this->load->model('User');
		$users = $this->User->getRecommendUser($_SESSION['userid']);
		echoJson(032,'get user success',array('User' => $users));
	}

	function fan_add() {
		if(!isset($_POST['userid'])) {
			echoJson(10030,'no userid');
			exit;
		} else {
			$fansid = $_POST['userid'];
		}
		if(!isset($_POST['followid'])) {
			echoJson(10031,'no followid');
			exit;
		} else {
			$followid = $_POST['followid'];
		}
		$this->load->model('User');
		if(isset($_SESSION['userid'])) {
			$fansid = $_SESSION['userid'];
		}
		//$fansid = $this->session->userdata('userid');
		if(empty($fansid)) {
			echoJson(10006,'no login');
			exit;
		}
		$fanData = array(
			'userid' => $followid,
			'fansid' => $fansid,
			'uptime' => date('Y-m-d H:i:s'),
			'user_fansid' => 0
		);
		$user_fansid = $this->User->addFans($fanData);
		$fanData['user_fansid'] = $user_fansid;
		echoJson(031,'addFans success',array('User' => $fanData));
	}

	function fan_delete() {
		if(!isset($_POST['userid'])) {
			echoJson(10030,'no userid');
			exit;
		} else {
			$fansid = $_POST['userid'];
		}
		if(!isset($_POST['followid'])) {
			echoJson(10031,'no followid');
			exit;
		} else {
			$followid = $_POST['followid'];
		}
		$this->load->model('User');
		if(isset($_SESSION['userid'])) {
			$fansid = $_SESSION['userid'];
		}
		//$fansid = $this->session->userdata('userid');
		if(empty($fansid)) {
			echoJson(10006,'no login');
			exit;
		}
		$fanData = array(
			'userid' => $followid,
			'fansid' => $fansid,
			'uption' => date('Y-m-d H:i:s'),
			'user_fansid' => 0
		);
		$affected_rows = $this->User->deleteFans($fanData);
		echoJson(034,'delete fans success','');	
	}

	function get_fans() {
		if(!isset($_POST['userid'])) {
			echoJson(10030,'no userid');
			exit;
		} else {
			$userid = $_POST['userid'];
		}
		$this->load->model('User');
		if(isset($_SESSION['userid'])) {
			$userid = $_SESSION['userid'];
		}
		// $userid = $this->session->userdata('userid');
		if(empty($userid)) {
			echoJson(10006,'no login');
			exit;
		}
		$fans = $this->User->getFans($userid);
		echoJson(030,'user_fans getted',array('User' => $fans));
	}
	
	function get_follows() {
		if(!isset($_POST['userid'])) {
			echoJson(10030,'no userid');
			exit;
		} else {
			$userid = $_POST['userid'];
		}
		$this->load->model('User');
		if(isset($_SESSION['userid'])) {
			$userid = $_SESSION['userid'];
		}
		$userid = $_SESSION['userid'];
		if(empty($userid)) {
			echoJson(10006,'no login');
			exit;
		}
		$follows = $this->User->getFollows($userid);
		echoJson(030,'user_follows getted',array('User' => $follows));
	}

}