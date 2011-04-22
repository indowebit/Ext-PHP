<?php 
	include_once('cores/class.login.php'); 
	$userLogin =  new userLogin(true); 
	$task = $_REQUEST['task']; 
	switch ($task) {
			case 'login': 
				$username = $_REQUEST['username']; 
				$pwd = $_REQUEST['pwd'];
				$result = $userLogin->doLogin($username,$pwd);  
				echo $result; 	
			break;
			
			case 'logout': 
					unset($_SESSION["userid"]);
					unset($_SESSION["user_name"]); 
					$result['success']=true;
					echo json_encode($result); 
			break; 
			
			case 'loadUser':
				echo $userLogin->loadUser();  
			break;
			
			case 'saveUser':
				echo $userLogin->saveUser($_REQUEST);  
			break; 
			
	}
?> 