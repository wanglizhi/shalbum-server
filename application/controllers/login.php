<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require 'static.php';
class Login extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->helper('form','url');
		session_start();
	}

	function logins() {
		$this->load->model('User');
		if(!isset($_POST['username']) ) {
			echoJson(10001,'no user');
			exit;
		}
		$username = $_POST['username'];
		$userdata = $this->User->getUser($username);
		if(empty($userdata)) {
			$data = array(
				'userid' => 0,
				'username' => $username,
				'sign' => isset($_POST['sign']) ? $_POST['sign'] : '',
				'face' => isset($_POST['face']) ? $_POST['face'] : '',
				'albumcount' => 0,
				'fanscount' => 0,
				'uptime' => date('Y-m-d H:i:s')
			);
			$this->User->addUser($data);
			$userdata = $this->User->getUser($username);
		}
		$userdata['sessionid'] = session_id();
		//$userdata['sessionid'] = $this->session->userdata('session_id');
		if(empty($userdata)) {
			echoJson(10001,'no user');
		}
		else {
			// $this->session->set_userdata('userid',$userdata['userid']);
			// $this->session->set_userdata('username',$userdata['username']);
			$_SESSION['userid'] = $userdata['userid'];
			$_SESSION['username'] = $userdata['username'];
			echoJson(10000,'login success',array('User' => $userdata));
		}
	}

	
//此方法已废弃
	function register() {
		$this->load->model('User');
		if(!isset($_POST['username']) || !isset($_POST['password'])) {
			echoJson(10003,'no user or no password');
			exit;
		}
		$userExist = $this->User->getUser($_POST['username']);
		if(!empty($userExist)){
			echoJson(10004,'username already exist');
			exit;	
		}
		$data = array(
			'userid' => 0,
			'username' => $_POST['username'],
			'password' => $_POST['password'], 
			'sign' => isset($_POST['sign']) ? $_POST['sign'] : '',
			'face' => isset($_POST['face']) ? $_POST['face'] : '',
			'albumcount' => 0,
			'fanscount' => 0,
			'uptime' => date('Y-m-d H:i:s')
		);
		$user_id = $this->User->addUser($data);
		$data['userid'] = $user_id;
		if($user_id != 0) {
			echoJson(001,'register success',$data);
		} else {
			echoJson(10005,'register failed');
		}
	}

//此方法暂时不用
	function do_download() {
		$this->load->library('session');
		$userid = $this->session->userdata('userid');
		if(empty($userid)) {
			echoJson(10006,'no login');
			exit;
		}
		$storage = new SaeStorage();
		$domain = 'userface';
		$filename = $userid;
		$data = $storage->read($domain,$filename);
		echoJson(004,'download success',$data);
	}

	function do_upload() {
		// $this->load->library('session');
		// //$this->session->set_userdata('userid','1');
		// $userid = $this->session->userdata('userid');
		// if(empty($userid)) {
		// 	echoJson(10006,'no login');
		// 	exit;
		// }
		// if(!is_dir('./image/'.$userid)) {
		// 	mkdir('./image/'.$userid);
		// }
		// if(!is_dir('./image/'.$userid.'/'.'face/')) {
		// 	mkdir('./image/'.$userid.'/'.'face/');
		// }
		// $config['upload_path'] = './image/'.$userid.'/'.'face/';
		// $config['allowed_types'] = '*';
		// $this->load->library('upload', $config);
		// if (!$this->upload->do_upload()) {
		// 	$error = array('error' => $this->upload->display_errors());
	 //  		echoJson(10007,'upload failed',$error);
	 //    } 
	 //    else {
	 //    	$data = array('upload_data' => $this->upload->data());
	 //    	$this->load->model('User');
	 //    	$updateFace = $this->User->addFace($userid,$config['upload_path'].$data['upload_data']['file_name']);
	 //    	echoJson(003,'upload success',$data);
	 //    }
// 		$this->load->library('session');
// 		$userid = $this->session->userdata('userid');
// if(empty($userid)) {
// 	echoJson(10006,'no login');
// 	exit;
// }
		if ($_FILES["userfile"]["error"] > 0)  {  
			echoJson(10007,'upload failed');
        	exit;
        } 
        if(!isset($_FILES["userfile"]["name"])) {
        	echoJson(10008,'no user id');
        	exit;
        }
		$filename = $_FILES["userfile"]["name"]; 
		//$userid = $_FILES['filename'];
		$userid = $filename;
		echoJson(10007,'upload ok');
		$storage = new SaeStorage();
 		$domain = 'userface';
 		$destFileName = $userid.'';
 		if(empty($userid)) {
 			echoJson(10008,'no user id');
 			exit;
 		}
 		$srcFileName = $_FILES['userfile']['tmp_name'];
 		//$attr = array('encoding'=>'gzip');
 		$result = $storage->upload($domain,$destFileName, $srcFileName);
 		$this->load->model('User');
 		$updateFace = $this->User->addFace($userid,$result);
 		echoJson(003,'upload success',$result);
	}
}