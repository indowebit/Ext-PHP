<?php
include_once('class/class.menu.php'); 

class userManager extends msDB  {
	
	function __construct($connection=true) {
		$this->messsage = "initialize class";
		if ($connection ==true) {
			$radiochecked = $this->connect();
		}
	}
	
	function __destruct() {
		unset($radiochecked);
	}

	function getMenu($group=0) {
		$menu = new Menu(true); 
		$result = $menu->getAllMenu(MENU_TITLE,'json',0,1,$group);
		return $result; 
	}
	
	function saveMenu($data){
		$json = json_decode(stripslashes($data)); 
		foreach($json as $row){
			$sql = "update role_menu_group set is_active=? where menu_id=? and group_id=?"; 
			$args = Array($row->is_active,$row->menu_id,$row->group_id); 
			$this->execSQL($sql,$args); 
		}
		$result = new stdClass(); 
		$result->success =true; 
		return json_encode($result); 
	}
	
	function getEvent($action,$REQUEST,$group_id=0,$menu_id=0){
		$grid = new grid(true); 
		$grid->setTable("role_menu_event_group");
		$grid->setJoin("INNER JOIN menu_event ON menu_event.id = role_menu_event_group.role_id");
		$grid->setManualFilter("
								 AND role_menu_event_group.group_id = $group_id
								 AND menu_event.menu_id = $menu_id 
								");
		$grid->addField(
					array(
						"field" =>"role_menu_event_group.role_menu_event_id",
						"name"=>"event_id",
						"primary"=>true
					)
		);
		$grid->addField(
					array(
						"field" =>"role_menu_event_group.is_active",
						"name"=>"is_active"
					)
		);
		$grid->addField(
					array(
						"field" =>"menu_event.event_name",
						"name"=>"event_name"
					)
		);
		switch ($action){
			case 'doRead':
				return $grid->doRead($REQUEST);  
			break; 
			case 'doCreate':
				return $grid->doCreate($REQUEST['data']);  
			break; 
			case 'doUpdate':
				return $grid->doUpdate($REQUEST['data']);  
			break; 
			case 'doDestroy':
				return $grid->doDestroy($REQUEST['data']);  
			break; 
			
		}
	}
	
	function getGroup($action,$REQUEST){
		$grid = new grid(true); 
		$grid->setTable("user_group"); 
		
		$grid->addField(
				array(
					"field"=>"group_id",
					"name"=>"group_id",
					"primary"=>true
				));
		$grid->addField(
				array(
					"field"=>"group_name",
					"name"=>"group_name"
				));
		$grid->addField(
				array(
					"field"=>"group_description",
					"name"=>"group_description"
				));
		switch ($action){
			case 'doRead': 
				return $grid->doRead($REQUEST); 	
			break; 
			case 'doCreate': 
				$data = $grid->doCreate($REQUEST['data']);
				$this->createRoleMenu($data);
				$this->createRoleMenuEvent($data);
				return $data;  	
			break; 	
			case 'doUpdate': 
				return $grid->doUpdate($REQUEST['data']); 	
			break; 		
			case 'doDestroy': 
				$this->dropRoleMenu($REQUEST['data']);
				$this->dropRoleMenuEvent($REQUEST['data']);
				return $grid->doDestroy($REQUEST['data']); 	
			break; 				
		}
		
	}
	function getUser($action,$REQUEST){
		$grid = new grid(true); 
		$grid->setTable("users"); 
		$grid->setManualFilter(" and user_id <> 1");
		$grid->addField(
				array(
					"field"=>"user_id",
					"name"=>"user_id",
					"primary"=>true
				));
		$grid->addField(
				array(
					"field"=>"user_name",
					"name"=>"user_name"
				));
		$grid->addField(
				array(
					"field"=>"real_name",
					"name"=>"real_name"
				));

		$grid->addField(
				array(
					"field"=>"user_password",
					"name"=>"user_password"
				)
		);		
		$grid->addField(
				array(
					"field"=>"group_id",
					"name"=>"group_id"
				)
		);		

		$grid->addField(
				array(
					"field"=>"date_created",
					"name"=>"date_created"
				)
		);
		$grid->addField(
				array(
					"field"=>"last_login",
					"name"=>"last_login"
				)
		);		
		$grid->addField(
				array(
					"field"=>"is_active",
					"name"=>"is_active"
				)
		);		
		
		switch ($action){
			case 'doRead': 
				return $this->decodePassword($grid->doRead($REQUEST)); 	
			break; 
			case 'doCreate': 
				return $this->decodePassword($grid->doCreate($this->encodePassword($REQUEST['data']))); 	
			break; 	
			case 'doUpdate': 
				return $this->decodePassword($grid->doUpdate($this->encodePassword($REQUEST['data']))); 	
			break; 		
			case 'doDestroy': 
				return $grid->doDestroy($REQUEST['data']); 	
			break; 				
		}
		
	}
	
	function dropRoleMenu($data){
		$data = str_replace("[","",$data); 
		$data = str_replace("]","",$data);
		$data = str_replace("\"","",$data); 
		$sql = "delete from role_menu_group where group_id in ($data)";  
		$this->setSQL($sql);
		$this->executeSQL();  
	}
	
	function dropRoleMenuEvent($data){
		$data = str_replace("[","",$data); 
		$data = str_replace("]","",$data);
		$data = str_replace("\"","",$data); 
		$sql = "delete from role_menu_event_group where group_id in ($data)";  
		$this->setSQL($sql);
		$this->executeSQL();  
	}
	
	function createRoleMenu($data){
		$data = json_decode(stripslashes($data)); 
		if ($data->data){
			$data = $data->data; 
			$sql = "select id from menu"; 
			$this->setSQL($sql);
			$rs = $this->executeSQL();  
			$temp = Array();
			while ($row = $rs->FetchNextObject()){
				$insert_array = Array();
				if (is_array($data)){
					foreach($data as $field){
						$insert_array['group_id'] = $field->group_id;
						$insert_array['menu_id'] = $row->ID;
						$temp[] = $insert_array;  
					}
				}else{
					$insert_array['group_id'] = $data->group_id; 
					$insert_array['menu_id'] = $row->ID; 
					$temp[] = $insert_array;
				}
			}
			$grid = new grid(true);
			$grid->setTable("role_menu_group");
			$grid->addField(
							array(
									"field"=>"role_menu_id",
									"name"=>"role_menu_id",
									"primary"=>true
								)
							);
			
			$grid->addField(
							array(
									"field"=>"menu_id",
									"name"=>"menu_id"
								)
							);
			$grid->addField(
							array(
									"field"=>"group_id",
									"name"=>"group_id"
								)
							);
			$grid->doCreate(json_encode($temp)); 
		}
	}

	function createRoleMenuEvent($data){
		$data = json_decode(stripslashes($data)); 
		if ($data->data){
			$data = $data->data; 
			$sql = "select id from menu_event"; 
			$this->setSQL($sql);
			$rs = $this->executeSQL();  
			$temp = Array();
			while ($row = $rs->FetchNextObject()){
				$insert_array = Array();
				if (is_array($data)){
					foreach($data as $field){
						$insert_array['group_id'] = $field->group_id;
						$insert_array['role_id'] = $row->ID;
						$temp[] = $insert_array;  
					}
				}else{
					$insert_array['group_id'] = $data->group_id; 
					$insert_array['role_id'] = $row->ID; 
					$temp[] = $insert_array;
				}
			}
			$grid = new grid(true);
			$grid->setTable("role_menu_event_group");
			$grid->addField(
							array(
									"field"=>"role_menu_event_id",
									"name"=>"role_menu_event_id",
									"primary"=>true
								)
							);
			
			$grid->addField(
							array(
									"field"=>"role_id",
									"name"=>"role_id"
								)
							);
			$grid->addField(
							array(
									"field"=>"group_id",
									"name"=>"group_id"
								)
							);
			$grid->doCreate(json_encode($temp)); 
		}
	}
	
	function encodePassword($data){
		$data = json_decode(stripslashes($data));  
		if (is_array($data)){
			$temp = array();
			foreach($data as $rec){
				if (isset($rec->user_password))
					$rec->user_password = base64_encode($rec->user_password); 
				$temp[] = $rec; 
			}
		} else {
			$temp = $data;
			 if (isset($temp->user_password))
				$temp->user_password = base64_encode($temp->user_password);	
		}
		
		return json_encode($temp); 
	}
	
	function decodePassword($data){
		$data = json_decode(stripslashes($data));  
		if ($data->data){
			$data1 = $data->data; 
			if (is_array($data1)){
				$temp = array();
				foreach($data1 as $rec){
					if (isset($rec->user_password))
						$rec->user_password = base64_decode($rec->user_password); 
					$temp[] = $rec; 
				}
			} else {
				$temp = $data1;
				 if (isset($temp->user_password))
					$temp->user_password = base64_decode($temp->user_password);	
			}
			$data->data = $temp; 	
		} 
		return json_encode($data); 
	}
	
}
?>