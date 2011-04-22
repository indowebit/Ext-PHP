<?php
class userLogin extends msDB {
	
	function __construct($connection) {
		$this->messsage = "initialize class";
		if ($connection ==true) {
			$radiochecked = $this->connect();
		}
	}
	
	function __destruct() {
		unset($radiochecked);
	}
	
	function doLogin($user_name,$pwd){
		$result = Array();
		$result['success'] = false; 
		$strSql = "select user_id , user_name, group_id from users where user_name=? and user_password=? and is_active=1"; 
		$args = Array($user_name,base64_encode($pwd)); 
		$rs = $this->execSQL($strSql,$args); 
		if ($rs->RecordCount()>0){
			$result['success'] = true;
			$sql = "update users set last_login = NOW(), count_login = count_login + 1 where user_id =?";
			$this->execSQL($sql,Array($rs->fields['user_id']));  
			$_SESSION['userid']= $rs->fields['user_id'];
			$_SESSION['group_id']= $rs->fields['group_id']; 
			$_SESSION['user_name'] = $user_name;
			$result['userid'] = $rs->fields['user_id'];
			$result['username'] = ucfirst($rs->fields['user_name']);
		}
		return json_encode($result); 
	}
	
	function loadUser(){
		$result = new stdClass(); 
		$result->success = false; 
		$strSql = "select * from users where user_id =?"; 
		$args = Array($_SESSION['userid']); 
		$rs = $this->execSQL($strSql,$args); 
		if ($rs->RecordCount()>0){
			$result->success = true; 
			$result->data = new stdClass(); 
			$result->data->user_name = $rs->fields['user_name']; 
			$result->data->real_name =  $rs->fields['real_name'];
			$result->data->user_password = base64_decode($rs->fields['user_password']);	
		}
		return json_encode($result); 
	}
	
	function saveUser($post){
		$result = new stdClass(); 
		$sql = "update users set real_name =?, user_password=? where user_id =?"; 
		$args = Array(
					$post['real_name'],
					base64_encode($post['user_password']),
					$_SESSION['userid']
				); 	
		$this->execSQL($sql,$args); 
		$result->success = ($this->db->ErrorMsg()=="")?true:false; 
		return json_encode($result); 		
	}
}
?>