<?php 

class menuManager extends msDB {
	/**
	 * Creating connection to Database
	 * using true for connect
	 * @param boolean $connection
	 */
	function __construct($connection) {
		$this->messsage = "initialize class";
		if ($connection ==true) {
			$radiochecked = $this->connect();
		}
	}
	
	function __destruct() {
		unset($radiochecked);
	}
	
	/**
	 * get All Menu From Database
	 *
	 * @return json string
	 */
	function getMenu() {
		$menu = new Menu(true); 
		$result = $menu->getAllMenu(MENU_TITLE,'json',1,1,-1,1);
		return $result; 
	}
	
	/**
	 * Save Menu to Table Menu
	 *
	 * @param integer $menu_id
	 * @param integer $parent_id
	 * @param string $menu_title
	 * @param string $ajax
	 * @param string $handler
	 * @param string $iconCls
	 * @param string $published
	 * @param boolean $isMenu
	 * @return success/failed Message
	 */
	function save(
					$menu_id,
					$parent_id,
					$menu_title,
					$ajax,
					$handler,
                                        $report,
					$iconCls,
					$published,
					$isMenu
				){
				
				$isok = true; 
				$msg = ""; 
				$conf = Array(); 
				if ($menu_id) {
					$strSql = "update menu set title=?,iconCls=?,handler=?,report=?,ajax=?,published=? where id=?";
					$args = Array(
									$menu_title,
									$iconCls,
									$handler,
                                                                        $report,
									$ajax,
									$published?1:0,
									$menu_id
								); 
					$this->execSQL($strSql, $args);
					$result['new_menu'] =  false; 	
					$result['iconCls'] = $iconCls; 
					$result['menu_title'] = $menu_title; 
					$result['published'] = $published?true:false;
					if ($this->db->ErrorMsg() !=""){
						$msg = $this->db->ErrorMsg();
						$isok =false;
					}
					
				} else {
					$strSql = "insert into menu (parent_id,title,iconcls,handler,ajax,report,published)";
					$strSql .= " values(?,?,?,?,?,?,?);";
					$args = Array(
									$parent_id,
									$menu_title,
									$iconCls, 
									$handler?$handler:"",
									$ajax?$ajax:"",
                                                                        $report?$report:"",
									$published?1:0
								);
					$this->execSQL($strSql, $args);
					if ($this->db->ErrorMsg() !=""){
						$msg = $this->db->ErrorMsg();
						$isok =false;
					}
					if ($isok) {
						$strSql ="select id from menu ORDER BY id DESC LIMIT 0,1"; 
						$this->setSQL($strSql); 
						$rs = $this->executeSQL(); 
						$id_menu =  $rs->fields['id'];
						if ($id_menu) {
								$this->createRoleMenu($id_menu);
								$conf['id'] = "numberid.".$id_menu; 
								$conf['text'] = $menu_title; 
								$conf['iconCls'] = $iconCls;
								$conf['checked'] = $published?true:false;
								if ($isMenu=="true")
									$conf['leaf'] = true;
								$result['new_menu'] =  true; 	
						}
                                                $this->updateSort(); 
					}
				}
				
				if ($isok) {
					$result['success'] = true; 
					if ($conf){
						$result['config'] = $conf; 
					}
				} else {
					$result['success'] = false; 
					$result['msg'] = $msg; 
				}
				
				return json_encode($result); 
	}

	/**
	 * Get Detail Menu From Menu Id
	 *
	 * @param integer $menu_id
	 * @param boolean $isMenu
	 * @return json string menu
	 */
	function getDetail($menu_id,$isMenu) {
		$msg['success'] = false;
		$query="
			SELECT * from menu where id ='$menu_id';
			";
					
		$this->setSQL($query);
		$rs=$this->executeSQL();
		
		if ($rs) {
			while ($row = $rs->FetchNextObject()) {
				$msg['success'] = true;
				$msg['data'] = Array(
					"menu_id"=>$row->ID,
					"menu_title"=>$row->TITLE,
					"handler"=>$row->HANDLER,
					"ajax"=>$row->AJAX,
                                        "report"=>$row->REPORT,
					"iconCls"=>$row->ICONCLS,
					"published"=>($row->PUBLISHED)?true:false,
					"isMenu"=>$isMenu
				);		
			}
		}
		
		return json_encode($msg);
	}
	
	/**
	 * Update publish itemn Menu
	 *
	 * @param json Array Menu $dataList
	 * @return success/failed Message
	 */
	function publish($dataList) {
		$data = json_decode(stripslashes($dataList));
		foreach ($data as $row) {
			$strSql ="update menu set published=? where id =?"; 
			$args = Array(
						$row->checked?1:0,
						$row->id
					); 
			$this->execSQL($strSql,$args); 		
		}
		$result['success']= true; 
		return json_encode($result); 
	}
	/**
	 * Remove Menu From Database
	 *
	 * @param json Array Menu $dataList
	 * @return success/failed message
	 */
	function remove($dataList) {
		$data = json_decode(stripslashes($dataList));
		foreach ($data as $row) {
			$strSql ="delete from menu where id =?"; 
			$args = Array(
						$row->id
					); 
			$this->execSQL($strSql,$args); 	
			$this->dropRoleMenu($row->id);	
		}
		$result['success']= true; 
		return json_encode($result); 
	}

	/**
	 * Get Image Icon from folder icon
	 *
	 * @param string $dir
	 * @param string $typefile
	 * @return json string
	 */
	function fileList($dir,$typefile='gif|png') {
		$images = array();
		$d = dir($dir);
		while($name = $d->read()){
			$filter = '/\.('.$typefile.')$/';
			if(!preg_match($filter, $name)) continue;
			$size = filesize($dir.$name);
			$lastmod = filemtime($dir.$name)*1000;
			$images[] = array('name'=>$name, 'size'=>$size,
					'lastmod'=>$lastmod, 'url'=>$dir.$name);
		}
		$d->close();
		$o = array('filelist'=>$images);
		return json_encode($o);
	
	}
	/**
	 * get Handler File List
	 *
	 * @return json string
	 */
	function handlerList() {
		$result = $this->fileList("app/view_js/","js");
		return $result; 
	}

	/**
	 * Get Ajax File List
	 *
	 * @return json string
	 */
	function ajaxList() {
		$result = $this->fileList("app/controller/","php");
		return $result; 
	}

	function reportList() {
		$result = $this->fileList("app/report/","php");
		return $result;
	}

	function iconList() {
		$result = $this->fileList("images/icon/","gif|png"); 
		return $result; 
	}
	
	function gridIcon($action,$REQUEST) {
		$grid = new grid(true); 
		$grid->setTable("iconcls"); 
		
		$grid->addField(
				array(
					"field"=>"id",
					"name"=>"id",
					"primary"=>true
				));
		$grid->addField(
				array(
					"field"=>"title",
					"name"=>"title"
				));
		$grid->addField(
				array(
					"field"=>"clsname",
					"name"=>"clsname"
				));
			$grid->addField(
				array(
					"field"=>"icon",
					"name"=>"icon"
				));
				
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

	function gridEvent($action,$REQUEST,$menu_id) {
		$grid = new grid(true); 
		$grid->setTable("menu_event"); 
		$grid->setManualFilter(" and menu_id ='$menu_id'"); 
		$grid->addField(
				array(
					"field"=>"id",
					"name"=>"id",
					"primary"=>true
				));
		$grid->addField(
				array(
					"field"=>"menu_id",
					"name"=>"menu_id"
				));
		$grid->addField(
				array(
					"field"=>"event_name",
					"name"=>"event_name"
				));
				
		switch ($action){
			case 'doRead': 
				return $grid->doRead($REQUEST); 	
			break; 
			case 'doCreate': 
				$result =  $grid->doCreate($REQUEST['data']);
				$this->createEventMenu($result);
				return $result;  	
			break; 	
			case 'doUpdate': 
				return $grid->doUpdate($REQUEST['data']); 	
			break; 		
			case 'doDestroy': 
				$this->dropEventMenu($REQUEST['data']);
				return $grid->doDestroy($REQUEST['data']); 	
			break; 				
		}
		
	}
	function dropEventMenu($data){
		$data = str_replace("[","",$data); 
		$data = str_replace("]","",$data);
		$data = str_replace("\"","",$data); 
		$sql = "delete from role_menu_event_group where role_id in ($data)";  
		$this->setSQL($sql);
		$this->executeSQL();  
	}

	function dropRoleMenu($menu_id){
		$sql = "delete from role_menu_group where menu_id = '$menu_id'";  
		$this->setSQL($sql);
		$this->executeSQL();  
	}
	
	function createRoleMenu($menu_id){
		$sql = "select group_id from user_group"; 
		$this->setSQL($sql);
		$rs = $this->executeSQL();  
		$temp = Array();
		while ($row = $rs->FetchNextObject()){
			$insert_array = Array();
			$insert_array['menu_id'] = $menu_id; 
			$insert_array['group_id'] = $row->GROUP_ID; 
			$temp[] = $insert_array;
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
	
	function createEventMenu($data){
		$data = json_decode(stripslashes($data)); 
		if ($data->data){
			$data = $data->data; 
			$sql = "select group_id from user_group"; 
			$this->setSQL($sql);
			$rs = $this->executeSQL();  
			$temp = Array();
			while ($row = $rs->FetchNextObject()){
				$insert_array = Array();
				if (is_array($data)){
					foreach($data as $field){
						$insert_array['role_id'] = $field->id;
						$insert_array['group_id'] = $row->GROUP_ID;
						$temp[] = $insert_array;  
					}
				}else{
					$insert_array['role_id'] = $data->id; 
					$insert_array['group_id'] = $row->GROUP_ID; 
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
        function doDrop($node,$parent){
            $sql = "update menu set parent_id=? where id=?";
            $arr = Array($parent,$node);
            $this->execSQL($sql,$arr);
            $result = new stdClass();
            $result->success = ($this->db->ErrorMsg()=="")?true:false;
            $result->msg = $this->db->ErrorMsg();
            return json_encode($result);            
        }

        function sortMenu($REQUEST,$parent_id,$update=0){
            $grid = new grid(true);
            $grid->setTable("menu");
            $grid->setJoin("LEFT JOIN iconcls ON menu.iconcls = iconcls.clsname");
            $grid->setManualFilter(" AND parent_id = '$parent_id'");
            $grid->addField(Array('field'=>'menu.id','name'=>'id','primary'=>true));
            $grid->addField(Array('field'=>'menu.title','name'=>'title'));
            $grid->addField(Array('field'=>'iconcls.icon','name'=>'iconcls'));
            $grid->addField(Array('field'=>'menu.sort_id','name'=>'sort_id'));
            if ($update)
                return $grid->doUpdate($REQUEST['data']);
            else
                return $grid->doRead($REQUEST);
        }

        function updateSort(){
            $sqlStr = "update menu set sort_id = id where sort_id =0";
            $this->setSQL($sqlStr);
            $this->executeSQL(); 
        }
}
	
?>