<?php
	session_start(); 
	$userid = isset($_SESSION['userid'])?$_SESSION['userid']:0; 
	include_once("config_sistem.php");
	include_once("class/mssql.inc.php"); 
	include_once("class/class.menu.php"); 
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	header('Content-Type: application/json');			
	if ($userid) {
		$menu = new Menu(true); 
		$all = $menu->getAllMenu(MENU_TITLE);
		echo $all; 
	} else {
		$menuLock = "
				[
					{
						text: '".MENU_TITLE."',
						cls:'feeds-node',
						iconCls:'base',
						children:[
							{
								text:'Menu Has Been Locked',
								iconCls:'lock',
								leaf:true
							}
						]
					}
				]
		"; 
		echo $menuLock; 
	}
?>