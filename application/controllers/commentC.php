<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require 'static.php';
class CommentC extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->helper('form','url');
		session_start();
	}

	function addComment() {
		if(!isset($_POST['albumid'])) {
			echoJson(10020,'no albumid');
			exit;
		} else {
			$albumid = $_POST['albumid'];
		}
		if(!isset($_POST['content'])) {
			$content = '';
		} else {
			$content = $_POST['content'];
		}
		$this->load->model('Comment');
		if(isset($_SESSION['userid'])) {
			$userid = $_SESSION['userid'];
		}
		// $userid = $this->session->userdata('userid');
		if(empty($userid)) {
			echoJson(10006,'no login');
			exit;
		}
		$commentData = array(
			'commentid' => 0,
			'albumid' => $albumid,
			'userid' => $userid,
			'content' =>$content,
			'uptime' => date('Y-m-d H:i:s')
		);
		$comment_id = $this->Comment->addComment($commentData);
		$commentData['commentid'] = $comment_id;
		echoJson(013,'add comment success',$commentData);
	}

	function getComment() {
		$this->load->model('Comment');
		if(!isset($_POST['albumid'])) {
			if(isset($_SESSION['albumid'])) {
				$albumid = $_SESSION['albumid'];
			}
			// $albumid = $this->session->userdata('albumid');
			if(empty($albumid)) {
				echoJson(10012,'not select album');
				exit;
			}
		} else {
			$albumid = $_POST['albumid'];
		}
		$comments = $this->Comment->get_album_comment($albumid);
		echoJson(014,'get comment success',$comments);
	}
	
}