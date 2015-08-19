<?php
 function echoJson($code,$message,$result = NULL) {
		if($result === NULL) {
			echo json_encode(array(
				'code' => $code, 
				'message' => $message
			));
		} else {
			echo json_encode(array(
				'code' => $code, 
				'message' => $message,
				'result' => $result
			));
		}
	}
