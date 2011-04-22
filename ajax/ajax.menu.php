<?php
	include_once("class/class.menu.php"); 
	include_once("cores/class.menu-manager.php");
	$task = $_REQUEST['task'];
	$menuManager = new menuManager(true);
	switch($task) {
		case 'getMenu': 
			$result = $menuManager->getMenu(); 
			echo $result; 
		break; 
		
		case 'save': 
			$result = $menuManager->save(
											$_REQUEST['menu_id'],
											$_REQUEST['parent_id'],
											$_REQUEST['menu_title'],
											$_REQUEST['ajax'],
											$_REQUEST['handler'],
                                                                                        $_REQUEST['report'],
											$_REQUEST['iconCls'], 
											$_REQUEST['published'],
											$_REQUEST['isMenu']
										); 
			echo $result; 
		break; 
		
		case 'getDetail': 
			$result = $menuManager->getDetail($_REQUEST['menu_id'],$_REQUEST['isMenu']); 
			echo $result; 
		break; 

		case 'publish': 
			$result = $menuManager->publish($_REQUEST['dataList']); 
			echo $result; 
		break; 

		case 'remove': 
			$result = $menuManager->remove($_REQUEST['dataList']); 
			echo $result; 
		break; 		
		
		case 'handlerList':
			$result = $menuManager->handlerList();
			echo $result;
		break; 

		case 'ajaxList':
			$result = $menuManager->ajaxList();
			echo $result;
		break; 

		case 'reportList':
			$result = $menuManager->reportList();
			echo $result;
		break;

                case 'iconList':
			$result = $menuManager->iconList();
			echo $result;
		break; 

		case 'gridIcon':
			$result = $menuManager->gridIcon($_REQUEST['action'],$_REQUEST);
			echo $result;
		break; 
		
		case 'gridEvent':
			$result = $menuManager->gridEvent($_REQUEST['action'],$_REQUEST,$_REQUEST['menu_id']);
			echo $result;
		break;

                case 'doDrop':
                        echo $menuManager->doDrop($_REQUEST['node'], $_REQUEST['parent']);
                break;

                case 'sortMenu':
                        echo $menuManager->sortMenu($_REQUEST, $_REQUEST['parent_id'], $_REQUEST['update']);
                break; 
	}


?>