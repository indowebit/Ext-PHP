<?php

/**
 * Menu Class to Build Recursive Tree Menu 
 * Extends msDB Database 
 */
class Menu extends msDB {
	private $text = "";
	/**
	 * Variable to Set show publish menu or not publish menu
	 * variable setting : 1 or 0 
	 * @var integer
	 */
	private $publish = 1; 
	/**
	 * Variable to show checked mode on Json Menu
	 * Use Value 0 or 1 
	 * @var integer
	 */
	private $check = 0; 
	
	private $group= -1; 
	
	private $allmenu =0; 
	
	/**
	 * Constructor to connect Database
	 * Using true 
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
	 * Get Child Node on Table 
	 *
	 * @param integer $parent_id
	 * @return recordset
	 */
	function getChild($parent_id) {
		$user_name = $_SESSION['user_name']; 
		$publish = ($this->publish)?" and menu.published =1":"";
		if ($this->group ==-1){
			$str_sql = 
				"SELECT menu.id AS id, menu.title AS title, menu.iconcls as iconcls, menu.parent_id AS parent_id,
						role_menu_group.is_active as published
				FROM menu,role_menu_group
				WHERE parent_id=? AND menu.id = role_menu_group.menu_id AND 
				role_menu_group.group_id =? AND role_menu_group.is_active = 1
				$publish
				ORDER BY menu.sort_id";
				$args = array($parent_id,$_SESSION['group_id']);	
		} else {
			$str_sql = 
				"SELECT menu.id AS id, menu.title AS title, menu.iconcls as iconcls, menu.parent_id AS parent_id,
						role_menu_group.is_active as published
				FROM menu,role_menu_group
				WHERE parent_id=? AND menu.id = role_menu_group.menu_id AND 
				role_menu_group.group_id =?
				$publish
				ORDER BY menu.sort_id";
				$args = array($parent_id,$this->group);
		}
		
		if ($this->allmenu){
			$str_sql = "SELECT * from menu
						WHERE parent_id=?
						$publish
						ORDER BY sort_id
						";
			$args = Array($parent_id); 
		}
		$rs = $this->execSQL($str_sql, $args);
		return $rs;
	}
	
	function updateIconcls($id,$icon) {
		$str_sql = "update menu set iconCls =? where id=?";
		$args = array($icon,$id);
		$this->execSQL($str_sql,$args);
	}

	/**
	 * Creating Json Menu 
	 *
	 * @param integer $parent_id
	 * @return recordset
	 */
	function getMenuJson($parent_id) {
		$rs = $this->getChild($parent_id);
		$temp = "";
		while ($row=$rs->FetchNextObject()) {
			$check = ($row->PUBLISHED)?"true":"false";
			$temp = ($temp=="")?$temp:$temp.",";
				$temp .= $this->tagJson($row->TITLE,
						    $this->getMenuJson($row->ID),
						    $row->ID,
						    $row->ICONCLS,
							$check
					);
		}
		return $temp;
	}

	/**
	 * Getting Text Menu Recursive
	 *
	 * @param integer $parent_id
	 * @param string $indent
	 * @return recordset
	 */
	function getMenuText($parent_id, $indent) {
		$rs = $this->getChild($parent_id);
		while ($row=$rs->FetchNextObject()) {
			$this->text.= $indent.$row->TITLE."<br>";
			$this->getMenuText($row->MENU_ID, $indent."=>");			
		}
		return $this->text; 
	}
	
	/**
	 * Format Json Array Menu
	 *
	 * @param string $tag_name
	 * @param string $value
	 * @param integer $id
	 * @param string $iconCls
	 * @param boolean $check
	 * @return string
	 */
	function tagJson($tag_name, $value,$id=0,$iconCls="",$check="false") {
		$tmp = "{"."text: '$tag_name'"; 
		$checkmenu = ($this->check)?",checked:$check":""; 
		if (!$id){
			if ($this->group > -1)
                            $tmp .=",expanded : true"; //jika child tidak di expand
                        if (!$this->allmenu)
                            $tmp .= ",cls:'feeds-node'";
			$checkmenu = ""; 
		}
		$tmp .=",id:'numberid.$id'"; 
		$tmp .=",iconCls:'$iconCls'";
		$tmp .=$checkmenu;
		if ($value){
			if ($this->group > -1)
				$tmp .= ",expanded:true"; //agar child nampak/dibuka
			$tmp .= ", children:["."$value"."]"; 
		} else {
			$tmp .= ",leaf:true"; 
		} 
		$tmp .= "}";
		return $tmp; 
	}
	/**
	 * Get Menu on Json Format or Text Format
	 *
	 * @param string $root_title
	 * @param string:json/text $mode
	 * @param integer $allMenu
	 * @param integer $publish
	 * @param boolean $check
	 * @return string
	 */
	function getAllMenu($root_title,$mode='json', $publish=0,$check=0,$group=-1,$allmenu=0) {
		$this->publish = ($publish)? 0 : 1; 
		$this->check = $check;
		$this->group = $group;  
		$this->allmenu = $allmenu; 
		switch ($mode) {
			case 'json':
				$temp = "[". $this->tagJson($root_title, $this->getMenuJson(0),0,"base")."]";
				break; 

			case 'text': 
				$temp = $this->getMenuText(0, "=>");
				break; 
		}
		return $temp;
	}
}

?>