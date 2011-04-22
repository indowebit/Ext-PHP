<?php
	include_once("class/class.menu.php"); 
	include_once("cores/class.user-manager.php");
	$task = $_REQUEST['task'];
	$userManager = new userManager(true);
	
	switch($task){
		case 'gridGroup':
			$result = $userManager->getGroup($_REQUEST['action'],$_REQUEST);
			echo $result;
		break; 
		case 'gridUser':
			$result = $userManager->getUser($_REQUEST['action'],$_REQUEST);
			echo $result;
		break; 
		case 'getMenu': 
			$result = $userManager->getMenu($_REQUEST['group_id']); 
			echo $result; 
		break; 		
		case 'saveMenu': 
			$result = $userManager->saveMenu($_REQUEST['dataList']); 
			echo $result; 
		break; 
		case 'gridEvent':
			$result = $userManager->getEvent($_REQUEST['action'],
												$_REQUEST,$_REQUEST['group_id'],
												$_REQUEST['menu_id']
											); 
			echo $result;  
		break; 
	}

?>