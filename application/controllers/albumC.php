<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require 'static.php';
class AlbumC extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->helper('form','url');
		session_start();
	}

	function create_photo() {
		//$this->load->library('session');
		if(!isset($_POST['albumid'])) {
			if(!isset($_SESSION['albumid'])) {
				$albumid = $_SESSION['albumid'];
			}
			//$albumid = $this->session->userdata('albumid');
		} else {
			$albumid = $_POST['albumid'];
			$_SESSION['albumid'] = $albumid;
			//$this->session->set_userdata('albumid',$albumid);
		}
		$this->load->model('Photo');
		if(isset($_SESSION['userid'])) {
			$userid = $_SESSION['userid'];
		}
		//$userid = $this->session->userdata('userid');
		if(empty($userid)) {
			echoJson(10006,'no login');
			exit;
		}
		if(empty($albumid)) {
			echoJson(10010,'not selected album');
			exit;
		}
		if(!isset($_POST['describe'])) {
			$describe = '没有描述';
		} else {
			$describe = $_POST['describe'];
		}
        if(!isset($_POST['content'])) {
            $content = NULL;
		} else {
			$content = $_POST['content'];
		}
		$new_photo = array(
			'photoid' => 0,
			'albumid' => $_POST['albumid'],
			'content' => $content,
			'describe' => $describe
		);
		$photo_id = $this->Photo->addPhoto($new_photo);
		$new_photo['photoid'] = $photo_id;
		$_SESSION['photoid'] = $photo_id;
		//$this->session->set_userdata('photoid',$photo_id);
		echoJson(012,'create photo success',array('Photo' => $new_photo));
	}

	function upload_photo() {
		if ($_FILES["userfile"]["error"] > 0)  {  
        	exit;
        } 
        if(!isset($_FILES["userfile"]["name"])) {
        	echoJson(10012,'no photoid');
        	exit;
        }
		$photoid = $_FILES["userfile"]["name"]; 
		if(empty($photoid)) {
			echoJson(10012,'no photoid');
			exit;
		}
		$storage = new SaeStorage();
 		$domain = 'photo';
 		$destFileName = $photoid.'';
 		$srcFileName = $_FILES['userfile']['tmp_name'];
 		$result = $storage->upload($domain,$destFileName, $srcFileName);
	    $this->load->model('Photo');
	    $this->Photo->upload_photo($photoid,$result);
	    echoJson(013,'upload photo success', $result);
	}

//此方法暂时不用
	function download_photo() {
		$this->load->library('session');
		$userid = $this->session->userdata('userid');
		if(!isset($_POST['photoid'])) {
			echoJson(10011,'not selected photo');
			exit;
		}
		$photoid = $_POST['photoid'];
		if(empty($userid)) {
			echoJson(10006,'no login');
			exit;
		}
		$storage = new SaeStorage();
		$domain = 'photo';
		$filename = $photoid;
		$data = $storage->read($domain,$filename);
		echoJson(004,'download success',$data);
	}

	function create_album() {
		// $this->load->library('session');
		$this->load->model('Album');
		// $userid = $this->session->userdata('userid');
		// $username = $this->session->userdata('username');
		 if(isset($_SESSION['userid'])) {
		 	$userid = $_SESSION['userid'];
		 }
		 if(isset($_SESSION['username'])) {
		 	$username = $_SESSION['username'];
		 }
		 if(empty($userid)) {
		  	echoJson(10006,'no login');
		  	exit;
		 }
		if(!isset($_POST['name'])) {
			$name = $username;
		} else {
			$name = $_POST['name'];
		}
		if(isset($_POST['tag'])) {
			$tag = $_POST['tag'];
		} else {
			$tag = '';
		}
		$new_album = array(
			'albumid' => 0,
			'userid' => $userid,
			'name' => $name,
			'commentcount' => 0,
			'tag' => $tag,
			'uptime' => date('Y-m-d H:i:s')
		);
		$album_id = $this->Album->addAlbum($new_album);
		$new_album['albumid'] = $album_id;
		echoJson(010,'create album success',array('Album' => $new_album));
	}

	function get_user_albums() {
		$this->load->model('Album');
		if(!isset($_POST['userid'])) {
			if(isset($_SESSION['userid'])) {
				$userid =  $_SESSION['userid'];
			}
			//$userid = $this->session->userdata('userid');
			if(empty($userid)) {
				echoJson(10010,'no login');
				exit;
			}
		} else {
			$userid = $_POST['userid'];
		}
		$user_album = $this->Album->getAlbum($userid);
		echoJson(011,'fetch album success',$user_album);
	}
//得到所有的相册
	function get_all_albums() {
		// if(!isset($_SESSION['userid'])) {
		// 	echoJson(001,'no login');
		// 	exit;
		// }
		if(isset($_SESSION['userid'])){
			$userid = $_SESSION['userid'];
		} else {
			$userid = 0;
		}
		if(!isset($_POST['pageid'])){
			$this->get_all_album_no_page();
		} else {
			$pageid = $_POST['pageid'];
			$this->get_all_album_by_page($pageid, $userid);
		}
	}
	function get_hot_albums() {
		if(isset($_SESSION['userid'])){
			$userid = $_SESSION['userid'];
		} else {
			$userid = 0;
		}
		if(!isset($_POST['pageid'])){
			$this->get_all_album_no_page();
		} else {
			$pageid = $_POST['pageid'];
			$this->get_hot_album_by_page($pageid, $userid);
		}
	}
//好友的album,分页
	function get_follows_album() {
		if(!isset($_SESSION['userid'])) {
			echoJson(10010,'no login');
			exit;
		}
		if(!isset($_POST['pageid'])) {
			echoJson(10031,'no pageid');
			exit;
		}
		$pageid = $_POST['pageid'];
		$userid = $_SESSION['userid'];
		$this->load->model('Album');
		$this->load->model('Zan');
		$this->load->model('Store');
		$user_zans = $this->Zan->get_user_zan($userid);
		$all_album = $this->Album->getFollowsAlbum($pageid,$userid);
		$user_stores = $this->Store->get_user_store($userid);
		foreach ($all_album as &$album) {
			$album['photo_list'] = $this->get_album_photo($album['albumid']);
			$album['user'] = $this->get_album_user($album['userid']);
			$album['zan'] = false;
			$album['store'] = false;
			foreach ($user_zans as $user_zan) {
				if($user_zan['albumid'] == $album['albumid']) {
					$album['zan'] = true;
					break;
				}
			}
			foreach ($user_stores as $user_store) {
				if($user_store['albumid'] == $album['albumid']) {
					$album['store'] = true;
					break;
				}
			}
		}
		echoJson(011,'fetch album success',array('Album' => $all_album));
	}
//按标签找相册
	function get_tag_album() {
		if(!isset($_POST['tag'])) {
			echoJson(10040,'no tag');
			exit;
		}
		if(!isset($_POST['pageid'])) {
			echoJson(10031,'no pageid');
			exit;
		}
		$this->load->model('Album');
		if(isset($_SESSION['userid'])) {
			$this->load->model('Zan');
			$this->load->model('Store');
			$user_zans = $this->Zan->get_user_zan($_SESSION['userid']);
			$user_stores = $this->Store->get_user_store($_SESSION['userid']);
		}
		$all_album = $this->Album->getAlbumByTag($_POST['pageid'],$_POST['tag']);
		foreach ($all_album as &$album) {
			$album['photo_list'] = $this->get_album_photo($album['albumid']);
			$album['user'] = $this->get_album_user($album['userid']);
			if(isset($_SESSION['userid'])) {
				$album['zan'] = false;
				$album['store'] = false;
				foreach ($user_zans as $user_zan) {
					if($user_zan['albumid'] == $album['albumid']) {
						$album['zan'] = true;
						break;
					}
				}
				foreach ($user_stores as $user_store) {
				if($user_store['albumid'] == $album['albumid']) {
					$album['store'] = true;
					break;
				}
			}
			}

		}
		echoJson(011,'fetch album success',array('Album' => $all_album));
	}

	private function get_album_user($userid) {
		$this->load->model('User');
		$album_user = $this->User->getUserById($userid);
		return $album_user;
	}

	private function get_album_photo($albumid) {
		$this->load->model('Photo');
		$album_photos = $this->Photo->getPhoto($albumid);
		return $album_photos;
	}

	function get_album_photos() {
		$this->load->model('Photo');
		if(!isset($_POST['albumid'])) {
			if(isset($_SESSION['albumid'])){
				$albumid = $_SESSION['albumid'];
			}
			//$albumid = $this->session->userdata('albumid');
			if(empty($albumid)) {
				echoJson(10012,'not select album ');
				exit;
			}
		} else {
			$albumid = $_POST['albumid'];
		}
		$album_photos = $this->Photo->getPhoto($albumid);
		echoJson(012,'fetch photo success',array('Photo' => $album_photos));
	}

	function addTag() {
		if(!isset($_POST['albumid'])) {
			if(isset($_SESSION['albumid'])) {
				$albumid = $_SESSION['albumid'];
			}
			//$albumid = $this->session->userdata('albumid');
			if(empty($albumid)) {
				echoJson(10012,'not select album');
				exit;
			}
		} else {
			$albumid = $_POST['albumid'];
		}
		if(!isset($_POST['tag_content'])) {
			echoJson(10032,'no tag_content');
			exit;
		} 
		$this->load->model('Tag');
		$content = $_POST['tag_content'];
		$newTag = array('tagid' => 0, 'content' => $content);
		$tagid = $this->Tag->addTag($newTag);
		$newTag['tagid'] = $tagid;
		$newAlbumTag = array('album_tagid' => 0, 'albumid' => $albumid, 'tagid' => $tagid);
		$album_tagid = $this->Tag->addAlbumTag($newAlbumTag);
		$newAlbumTag['album_tagid'] = $album_tagid;
		echoJson(032,'add tag success',array('tag' => $newTag,'album_tag' => $newAlbumTag));
	}

//暂时未用
	function addExistTag() {
		if(!isset($_POST['albumid'])) {
			if(isset($_SESSION['albumid'])) {
				$albumid = $_SESSION['albumid'];
			}
			//$albumid = $this->session->userdata('albumid');
			if(empty($albumid)) {
				echoJson(10012,'not select album');
				exit;
			}
		} else {
			$albumid = $_POST['albumid'];
		}
		if(!isset($_POST['tagid'])) {
			echoJson(10032,'no tag selected');
			exit;
		} 
		$this->load->model('Tag');
		$tagid = $_POST['tagid'];
		$newAlbumTag = array('album_tagid' => 0, 'albumid' => $albumid, 'tagid' => $tagid);
		$album_tagid = $this->Tag->addAlbumTag($newAlbumTag);
		$newAlbumTag['album_tagid'] = $album_tagid;
		echoJson(032,'add tag success',array('album_tag' => $newAlbumTag));
	}

//方法已废弃
	function getAlbumTags() {
		if(!isset($_POST['albumid'])) {
			if(isset($_SESSION['albumid'])) {
				$albumid = $_SESSION['albumid'];
			}
			//$albumid = $this->session->userdata('albumid');
			if(empty($albumid)) {
				echoJson(10012,'not select album');
				exit;
			}
		} else {
			$albumid = $_POST['albumid'];
		}
		$this->load->model('Tag');
		$tags = $this->Tag->getAlbumTags($albumid);
		echoJson(033,'get tags success',array('tags' => $tags));
	}

	function zan() {
		$this->load->model('Zan');
		if(isset($_SESSION['userid'])) {
			$userid = $_SESSION['userid'];
		}
		// $userid = $this->session->userdata('userid');
		if(empty($userid)) {
			echoJson(10006,'no login');
			exit;
		}
		if(!isset($_POST['albumid'])) {
			echoJson(10012,'not select album');
			exit;
		} else {
			$albumid = $_POST['albumid'];
		}
		if(!isset($_POST['album_userid'])) {
			echoJson(10013,'not album\'s user');
			exit;
		} else {
			$albumuserid = $_POST['album_userid'];
		}
		$zanData = array(
			'zanid' => 0,
			'albumuserid' => $albumuserid,
			'albumid' =>$albumid,
			'userid' => $userid
		);
		$zan_id = $this->Zan->addZan($zanData);
		$zanData['zanid'] = $zan_id;
		echoJson(020,'zan success',array('Zan' => $zanData));
	}

	function zan_cancel() {
		$this->load->model('Zan');
		if(isset($_SESSION['userid'])) {
			$userid = $_SESSION['userid'];
		}
		// $userid = $this->session->userdata('userid');
		if(empty($userid)) {
			echoJson(10006,'no login');
			exit;
		}
		if(!isset($_POST['albumid'])) {
			echoJson(10012,'not select album');
			exit;
		} else {
			$albumid = $_POST['albumid'];
		}
		$delete_count = $this->Zan->deleteZan($userid,$albumid);
		echoJson(021,'zan delete success',array('Zan' => $delete_count));
	}

	function get_user_zan() {
		$this->load->model('Zan');
		if(!isset($_POST['userid'])) {
			if(isset($_SESSION['userid'])) {
				$userid = $_SESSION['userid'];
			}
			//$userid = $this->session->userdata('userid');
			if(empty($userid)) {
				echoJson(10012,'not select album');
				exit;
			}
		} else {
			$userid = $_POST['userid'];
		}
		$zans = $this->Zan->get_user_zan($userid);
		echoJson(021,'get zan success',array('Zan' => $zans));
	}
//收藏相册
	function store_album() {
		if(!isset($_SESSION['userid'])) {
			echoJson(10001,'no login');
			exit;
		}
		$this->load->model('Store');
		if(!isset($_POST['albumid'])){
			echoJson(10012,'not select album');
			exit;
		}
		$store_data = array(
			'storeid' => 0,
			'albumid' => $_POST['albumid'],
			'userid' => $_SESSION['userid']
		);
		$storeid = $this->Store->addStore($store_data);
		$store_data['storeid'] = $storeid;
		echoJson(022,'get store success',array('Store' => $store_data));
 	} 
 //取消收藏相册
 	function store_cancel() {
 		if(!isset($_SESSION['userid'])) {
			echoJson(10001,'no login');
			exit;
		}
		$this->load->model('Store');
		if(!isset($_POST['albumid'])){
			echoJson(10012,'not select album');
			exit;
		}
		$cancel_count = $this->Store->deleteStore($_SESSION['userid'],$_POST['albumid']);
		echoJson(023,'cancel store success',array('Store' => $cancel_count));
 	}
//取出收藏相册
 	function get_store_albums() {
 		if(!isset($_SESSION['userid'])) {
 			echoJson(10010,'no login');
 			exit;
 		}
 		if(!isset($_POST['pageid'])) {
 			echoJson(10033,'no selected album');
 			exit;
 		}
 		$pageid = $_POST['pageid'];
 		$userid = $_SESSION['userid'];
 		$this->load->model('Album');
 		$this->load->model('Zan');
 		$this->load->model('Store');
 		$user_zans = $this->Zan->get_user_zan($userid);
 		$user_stores = $this->Store->get_user_store($userid);
 		$all_album = $this->Album->getStoreAlbumByPage($pageid, $userid);
 		foreach ($all_album as &$album) {
			$album['photo_list'] = $this->get_album_photo($album['albumid']);
			$album['user'] = $this->get_album_user($album['userid']);
			$album['zan'] = false;
			$album['store'] = false;
			foreach ($user_zans as $user_zan) {
				if($user_zan['albumid'] == $album['albumid']) {
					$album['zan'] = true;
					break;
				}
			}
			foreach ($user_stores as $user_store) {
				if($user_store['albumid'] == $album['albumid']) {
					$album['store'] = true;
					break;
				}
			}
		}
		echoJson(011,'fetch album success',array('Album' => $all_album));
 	}

	function change_photo_to_album() {
		
	}

	//推荐的album
	private function get_all_album_no_page() {
		$this->load->model('Album');
		$all_album = $this->Album->getAllAlbum();
		foreach ($all_album as &$album) {
			$album['photo_list'] = $this->get_album_photo($album['albumid']);
			$album['user'] = $this->get_album_user($album['userid']);
		}
		echoJson(011,'fetch album success',array('Album' => $all_album));
	}
//推荐的album,用userid
	private function get_all_album_by_page($pageid, $userid) {
		$this->load->model('Album');
		$this->load->model('Zan');
		$this->load->model('Store');
		$user_zans = $this->Zan->get_user_zan($userid);
		$user_stores = $this->Store->get_user_store($userid);
		$all_album = $this->Album->getAlbumByPage($pageid, $userid);
		foreach ($all_album as &$album) {
			$album['photo_list'] = $this->get_album_photo($album['albumid']);
			$album['user'] = $this->get_album_user($album['userid']);
			$album['zan'] = false;
			$album['store'] = false;
			foreach ($user_zans as $user_zan) {
				if($user_zan['albumid'] == $album['albumid']) {
					$album['zan'] = true;
					break;
				}
			}
			foreach ($user_stores as $user_store) {
				if($user_store['albumid'] == $album['albumid']) {
					$album['store'] = true;
					break;
				}
			}
		}
		echoJson(011,'fetch album success',array('Album' => $all_album));	
	}
//和上述方法类似，但是用的是zan排序
	private function get_hot_album_by_page($pageid,$userid) {
		$this->load->model('Album');
		$this->load->model('Zan');
		$this->load->model('Store');
		$user_zans = $this->Zan->get_user_zan($userid);
		$user_stores = $this->Store->get_user_store($userid);
		$all_album = $this->Album->getHotAlbumByPage($pageid, $userid);
		foreach ($all_album as &$album) {
			$album['photo_list'] = $this->get_album_photo($album['albumid']);
			$album['user'] = $this->get_album_user($album['userid']);
			$album['zan'] = false;
			$album['store'] = false;
			foreach ($user_zans as $user_zan) {
				if($user_zan['albumid'] == $album['albumid']) {
					$album['zan'] = true;
					break;
				}
			}
			foreach ($user_stores as $user_store) {
				if($user_store['albumid'] == $album['albumid']) {
					$album['store'] = true;
					break;
				}
			}
		}
		echoJson(011,'fetch album success',array('Album' => $all_album));
	}
}