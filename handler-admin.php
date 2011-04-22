<?php
	session_start();
	$userid = isset($_SESSION['userid'])?$_SESSION['userid']:0;
        $group_id = isset($_SESSION['group_id'])?$_SESSION['group_id']:0;
	$user_name = isset($_SESSION['user_name'])?$_SESSION['user_name']:"";
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	header('Content-Type: text/javascript');
	if ($userid) {
		$page = isset($_POST['page'])?$_POST['page']:0;
		$js = 0; 
		switch($page){
			case 'menu-manager': 
				if ($user_name=="admin")
					$js ='menu_manager.js';
			break; 	
			case 'user-manager':
				if (($user_name=="admin") ||($group_id ==1))
					$js = 'user_manager.js';
					
			break; 
			case 'db-manager':
				if ($user_name=="admin")
					$js = 'db_manager.js';
			break; 
			
			case 'user-profile': 
				if ($userid)
					$js = 'user_profile.js';
			break; 
			
		}
			if ($js)
				if (file_exists("layouts/$js")){
					$result = file_get_contents("layouts/$js");
					echo stripslashes(trim($result)); 
				}

	}
?>