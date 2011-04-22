<?php

/**
	cara pakai
	$grid->setTable();
	$grid->addField(array kuduan);
	$grid->setJoin() -->optional;
	echo $grid->doRead($_REQUEST); 
**/
class grid extends msDB {

	/**
	 * Column Field Variable
	 *
	 * @var Array
	 */
	public $fields = array(); 
	/**
	 * Primary Key of Table
	 *
	 * @var unknown_type
	 */
	public $keyfield ="id";

	public $columns = array(); 
	/**
	 * Main Table
	 *
	 * @var unknown_type
	 */
	public $master_table =""; 
	/**
	 * Set Join To Relationship Table
	 *
	 * @var String
	 */
	public $join ="";
	/**
	 * Set Where Condition
	 *
	 * @var string
	 */
	public $where="0=0"; 
	public $start=0;
	public $count=0; 
	public $total =0;
	public $sort=""; 
	public $dir="";
	public $errMsg=""; 
	public $json =""; 
	public $manualFilter ="";
	public $groupBy ="";
	public $dataInput; 
	public $dataOutput;
  public $manualOrder ="";
	/**
	 * Array Input For REST Operation
	 *
	 * @var boolean
	 */
	private $isArrayInput = true; 
	private $sqlNolimit =0; 

	
	/**
	 * Constructor to Connection Database
	 *
	 * @param boolean $connection
	 */
	function __construct($connection) {
		$this->message = "initialize class";
		if ($connection ==true) {
			$radiochecked = $this->connect();
		}
	}
	
	/**
	 * Destructor Class
	 *
	 */
	function __destruct() {
		unset($radiochecked);
	}

	/**
	 * Set Main Table
	 *
	 * @param string $table
	 */
	function setTable($table) {
		$this->master_table = $table; 
	}
	
	/**
	 * Set Primary Key on Table
	 *
	 */
	function setKeyfield(){
		$list = $this->fields; 
		foreach ($list as $col){
			if (array_key_exists("primary",$col))
				$this->keyfield = $col['field']; 
		}
	}
	
	/**
	 * Add Relationship Table
	 *
	 * @param string $join
	 */
	function setJoin($join) {
		$this->join = $join; 
	}

  function setOrderBy($order){
    $this->manualOrder = $order; 
  }

	/**
	 * Set Group By field Table
	 *
	 * @param string $groupBy
	 */
	function setGroupBy($groupBy) {
		$this->groupBy = $groupBy;
	}
	
	/**
	 * Adding Fieldlist Table
	 *
	 * @param array $field
	 */
	function addField($field) {
		$this->fields[] = $field; 
	}
	
	function addColumn($column){
		$this->columns[] = $column; 
	}
	
	function formatColumns(){
		return json_encode($this->columns); 
	}
	
	function getColumns() {
		foreach ($this->fields as $field){
			if ($field['cm'])
				$this->addColumn($field['cm']);
		}
		return $this->formatColumns(); 
	}
	/**
	 * Add Manual Filter in clause Where
	 *
	 * @param string $manualFilter
	 */
	function setManualFilter($manualFilter) {
		$this->manualFilter = $manualFilter;
	}

	/**
	 * Find Key Field on Column Name
	 *
	 * @param string $field
	 * @return string
	 */
	function getKeyField($field) {
		$tmp = $this->fields; 
		foreach($tmp as $data)
			if ($data['name'] ==$field)
				$field_name = $data['field'];
		return $field_name;
	}

	function getExtraFunction($field){
		
	}
	/**
	 * Send Parameter to filter base on Extjs Grid
	 *
	 * @param unknown_type $REQUEST
	 */
	function sendParam($REQUEST) {
		$this->start = (!isset($REQUEST["start"]))? 0 : $REQUEST["start"];
		$this->count = (!isset($REQUEST["limit"] ))? 30 : $REQUEST["limit"];
		$this->sort = (!isset($REQUEST["sort"]))? "" : $REQUEST["sort"];
		if (isset($REQUEST["dir"]))
			$this->dir = ($REQUEST["dir"] == "DESC")? "DESC" : "";
		$the_filter = (isset($REQUEST["filter"])?$REQUEST["filter"]:""); 
		$this->buildFilter($the_filter);
	}

	function buildFilter($filter) {
		$this->where = " 0 = 0 ";
		$qs = ""; 		
		if (is_array($filter)) {
			for ($i=0;$i<count($filter);$i++){
				$filter[$i]['field'] = $this->getKeyField($filter[$i]['field']); 
				switch($filter[$i]['data']['type']){
					case 'string' : $qs .= " AND ".$filter[$i]['field']." LIKE '%".$filter[$i]['data']['value']."%'"; Break;
					case 'list' : 
						if (strstr($filter[$i]['data']['value'],',')){
							$fi = explode(',',$filter[$i]['data']['value']);
							for ($q=0;$q<count($fi);$q++){
								$fi[$q] = "'".$fi[$q]."'";
							}
							$filter[$i]['data']['value'] = implode(',',$fi);
							$qs .= " AND ".$filter[$i]['field']." IN (".$filter[$i]['data']['value'].")"; 
						}else{
							$qs .= " AND ".$filter[$i]['field']." = '".$filter[$i]['data']['value']."'"; 
						}
					Break;
					case 'boolean' : $qs .= " AND ".$filter[$i]['field']." = ".($filter[$i]['data']['value']); Break;
					case 'numeric' : 
						switch ($filter[$i]['data']['comparison']) {
							case 'eq' : $qs .= " AND ".$filter[$i]['field']." = ".$filter[$i]['data']['value']; Break;
							case 'lt' : $qs .= " AND ".$filter[$i]['field']." < ".$filter[$i]['data']['value']; Break;
							case 'gt' : $qs .= " AND ".$filter[$i]['field']." > ".$filter[$i]['data']['value']; Break;
						}
					Break;
					case 'date' : 
						switch ($filter[$i]['data']['comparison']) {
							case 'eq' : $qs .= " AND ".$filter[$i]['field']." = '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
							case 'lt' : $qs .= " AND ".$filter[$i]['field']." < '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
							case 'gt' : $qs .= " AND ".$filter[$i]['field']." > '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
						}
					Break;
				}
			}	
			$this->where .= $qs;
		}
	}

	function buildList($list){
		$tmp = array(); 
		foreach($list as $arr) {
			$tmp[] = $arr['field'] . " AS ". $arr['name']; 
		}
		return $tmp; 
	}
	
	function isMasterField($field) {
		$result = true;
		$tmp = $this->fields;
		foreach($tmp as $data)
			if ($data['name'] ==$field)
				if(isset($data['join_table']))
          $result =false;
		return $result; 
	}
  function add_always_column_avaible($column){
    $field =Array();
    foreach($this->fields as $col) {
      if (isset($col['always_include']))
        $field[] = $col['name'];
    }
    if ($field){
      $str_col = implode(" ", $column);
      foreach($field as $col_field)
      if (!preg_match("/\b$col_field\b/i", $str_col))
        $column[] = $this->getKeyField($col_field) . " AS " . $col_field;
    }
    return $column;
  }
	
	function buildField($fields){
		$col_field = array();
		$col_value = array(); 
		$keyvalue =  array(); 
		$col_select = array(); 
		foreach($fields as $arr=>$value) {
			if ($this->getKeyField($arr) != $this->keyfield){
				if ($this->isMasterField($arr)){
					$col_field[] = $this->getKeyField($arr); 
					$col_value[] = $value; 
				}
			} else 
				$keyvalue[] = $value; 
        $col_select[] = $this->getKeyField($arr) . " AS " . $arr;
		}
    $col_select = $this->add_always_column_avaible($col_select);
		$tmp = Array(
					"fields" =>$col_field, 
					"value" =>$col_value,
					"select" =>$col_select,
					"id"=>$keyvalue
					); 
		return $tmp; 
	}
	
	function buildSql() {
		$list_field=""; 
		$list_field = implode(",",$this->buildList($this->fields));
		$query = "SELECT  $list_field from ". $this->master_table ." " . $this->join; 
		$query .= " WHERE ". $this->where ." ".$this->manualFilter;
		if ($this->groupBy !="")
			$query .= " GROUP BY ".$this->groupBy;    
    if ($this->manualOrder != ""){
      $query .= " ORDER BY ". $this->manualOrder;      
    }else{
      if ($this->sort !="")
			$query .= " ORDER BY ".$this->getKeyField($this->sort)." ".$this->dir;
    }
		if (!$this->sqlNolimit)
			$query .= " LIMIT ".$this->start.",".$this->count;
		//$query .= " LIMIT ".$this->count." OFFSET ".$this->start ;
		$this->setSQL($query); 
		$rs = $this->executeSQL(); 
		if ($this->db->ErrorMsg() !="")
			$this->errMsg = "Error On Execute query : $query \n Error: ".$this->db->ErrorMsg(); 
		if (!$this->sqlNolimit)	
			$this->buildJson($rs); 
		else 
			return $rs; 
	}

	function buildJson($rs) {
		if ($this->errMsg ==""){
			$this->getTotal(); 
			$arr = array(); 
			while ($row=$rs->FetchNextObject()) {
				$the_col = array();
					foreach($row as $key=>$x){
						$key = strtolower($key);
						$the_col[$key] = $x;
					}	
				$arr[] = $the_col; 
			}
			$tmp['success'] = true; 
			$tmp['total'] = $this->total; 
			$tmp['data'] = $arr; 
		} else {
			$tmp['success']= false; 
			$tmp['total'] = 0; 
			$tmp['data']= Array(); 
			$tmp['message'] = $this->errMsg;
		}
		
		$this->json = json_encode($tmp); 	
	}

	function buildSingleJson($rs) {
			$the_col = array(); 
			while ($row=$rs->FetchNextObject()) {
					foreach($row as $key=>$x){
						$key = strtolower($key);
						$the_col[$key] = $x;
					}	
			}
		return $the_col;
	}
	
	/**
	 * View Data on Json Format
	 *
	 * @param unknown_type $REQUEST
	 * @return json string
	 */
	function doRead($REQUEST) {
		$this->sendParam($REQUEST); 
		$this->buildSql();
		return $this->json; 
	}

	function doSql($REQUEST) {
		$this->sqlNolimit =1; 
		$this->sendParam($REQUEST); 
		return $this->buildSql();
	}
	
	function getTotal() {
		$sql ="SELECT COUNT(1) as total FROM ". $this->master_table . " ".$this->join; 
		$sql.= " WHERE ". $this->where." ".$this->manualFilter; 
		if ($this->groupBy !="")
			$sql .= " GROUP BY ".$this->groupBy;
		$this->setSQL($sql);
		$rs = $this->executeSQL();
		if ($this->groupBy =="")
			$this->total = $rs->fields['total']; 
		else 
			$this->total = $rs->RecordCount(); 
	}
	
	function setIsArrayInput(){
		$tagArray = substr($this->dataInput,0,1); 
		$this->isArrayInput =($tagArray == "[")?true:false; 
	}
	
	/**
	 * Insert into Table Master using field list
	 *
	 * @param unknown_type $dataInput
	 * @return json string
	 */
	function doCreate($dataInput) {
		$this->dataInput = $dataInput; 
		$this->setIsArrayInput(); 
		$this->setKeyfield(); 
		$this->executeInsert(); 
		return $this->dataOutput; 

		}

	/**
	 * Update Table Master using CRUD
	 *
	 * @param unknown_type $dataInput
	 * @return json string
	 */
	function doUpdate($dataInput) {
		$this->dataInput = $dataInput; 
		$this->setIsArrayInput(); 
		$this->setKeyfield(); 
		$this->executeUpdate(); 
		return $this->dataOutput; 

	}

	function executeUpdate(){
		$query = $this->buildFieldUpdate(); 
		$msgArray = Array(); 
		$json = Array(); 
		$total = 0; 
		foreach ($query as $sql) {
			$sqlUpdate = $sql['sqlUpdate']; 
			$this->execSQL($sqlUpdate['sql'],$sqlUpdate['args']); 
			if ($this->db->ErrorMsg() !=""){
				$msgArray[] = $this->db->ErrorMsg(); 
			} else {
				$total++; 
				$sqlSelect = $sql['sqlSelect']; 
				$rs = $this->execSQL($sqlSelect['sql'],$sqlSelect['args']); 
				$json[] = $this->buildSingleJson($rs); 
			}
		}
		$result = array(); 
		if ($total) {
			if ($this->isArrayInput)
				$result['data'] = $json; 
			else 
				$result['data'] = $json[0]; 
			$result['total'] = $total; 
			$result['success'] = true; 
			$result['message']['note'] = "Update Succesfully Saved"; 	
		} else{
			$result['success'] =false; 
			$result['data'] = $json; 
			$result['message']['note'] = "Failed to Update Record"; 
		}
			$result['message']['error'] = $msgArray; 	
		
		$this->dataOutput = json_encode($result); 
	}

	function executeInsert(){
		$query = $this->buildFieldInsert(); 
		$msgArray = Array(); 
		$json = Array(); 
		$total = 0; 
		foreach ($query as $sql) {
			$sqlInsert = $sql["sqlInsert"];
			$this->execSQL($sqlInsert['sql'],$sqlInsert['args']); 
			if ($this->db->ErrorMsg() !=""){
				$msgArray[] = $this->db->ErrorMsg(); 
			} else {
				$total++; 
				$sqlSelect = $sql["sqlSelect"]; 
				$this->setSQL($sqlSelect);
				$rs = $this->executeSQL();
				$json[] = $this->buildSingleJson($rs); 
			}
		}
		$result = array(); 
		if ($total) {
			if ($this->isArrayInput)
				$result['data'] = $json; 
			else 
				$result['data'] = $json[0]; 
			$result['total'] = $total; 
			$result['success'] = true; 
			$result['message']['note'] = "New Record Succesfully Saved"; 	
		} else{
			$result['success'] =false; 
			$result['data'] = $json; 
			$result['message']['note'] = "Failed to Insert New Record"; 
		}
			$result['message']['error'] = $msgArray; 	
		
		$this->dataOutput = json_encode($result); 
	}
	
	function buildUpdateQuery($data) {
		$row = $this->buildField($data);  
		$fields = $row['fields']; 
		$tmp = Array(); 
		foreach ($fields as $ff){
			if ($ff)
				$tmp[] =$ff; 
		}
		$fields = $tmp; 
		$fieldSql = array(); 
		foreach($fields as $field) {
			$fieldSql[] = $field ."=?";  
		}
		$strSql = "UPDATE " . $this->master_table . " set " . implode(",",$fieldSql); 
		$strSql .= " WHERE ". $this->keyfield ."=?"; 
		$args = array_merge($row['value'],$row['id']);  
		$result = array(
						"sql"=>$strSql,
						"args"=>$args
						); 
		return $result; 
	}

	function buildInsertQuery($data) {
		$row = $this->buildField($data);  
		$fields = $row['fields']; 
		$tmp = Array(); 
		foreach ($fields as $ff){
			if ($ff)
				$tmp[] =$ff; 
		}
		$fields = $tmp; 
		$fieldSql = array(); 
		foreach($fields as $field) {
			$fieldSql[] = "?";  
		}
		$strSql = "INSERT INTO ".$this->master_table ."(". implode(",",$fields) .")"; 
		$strSql .= " VALUES(". implode(",",$fieldSql) .")"; 
		$args = $row['value'];   
		$result = array(
						"sql"=>$strSql,
						"args"=>$args
						); 
		return $result; 
	}
	
	function buildUpdateQuerySelect($data) {
		$row = $this->buildField($data);  
		$fields = $row['select']; 
		$tmp = Array(); 
		foreach ($fields as $ff){
			if ($ff)
				$tmp[] =$ff; 
		}
		$fields = $tmp; 
		$selectlist = implode(",",$fields); 
		$strSql ="SELECT ". $selectlist ." FROM ". $this->master_table ." " . $this->join ." WHERE ". $this->keyfield ."=?"; 
		$args = $row['id'];  
		$result = array(
						"sql"=>$strSql,
						"args"=>$args
						);
		return $result; 
	}

	function getLastRecordSql($data) {
		$row = $this->buildList($this->fields);
		$selectlist = implode(",",$row); 	
		$strSql = "select " . $selectlist . " from " . $this->master_table ." " . $this->join ." order by ". $this->keyfield ." desc limit 0,1"; 
		return $strSql; 
	}
	
	function buildFieldUpdate() {
		$result = array(); 
		$jsonInput = json_decode(stripslashes($this->dataInput)); 
		if ($this->isArrayInput) {
			foreach ($jsonInput as $row) 
				$result[] = Array(
								"sqlUpdate"=>$this->buildUpdateQuery($row),
								"sqlSelect"=>$this->buildUpdateQuerySelect($row)
							); 
		} else {
				$result[] = Array(
								"sqlUpdate"=>$this->buildUpdateQuery($jsonInput),
								"sqlSelect"=>$this->buildUpdateQuerySelect($jsonInput)
							); 

		}
		return $result; 
	}

	function buildFieldInsert() {
		$result = array(); 
		$jsonInput = json_decode(stripslashes($this->dataInput)); 
		if ($this->isArrayInput) {
			foreach ($jsonInput as $row) 
				$result[] =	Array(
								"sqlInsert"=>$this->buildInsertQuery($row),
								"sqlSelect"=>$this->getLastRecordSql($row)
							);
		} else {
				$result[] =	Array(
								"sqlInsert"=>$this->buildInsertQuery($jsonInput),
								"sqlSelect"=>$this->getLastRecordSql($jsonInput)
							);
		}
		return $result; 
	}
	
	/**
	 * Delete Operation on Table Master
	 *
	 * @param json string $dataInput
	 * @return json string
	 */
	function doDestroy($dataInput) {
		$this->dataInput = $dataInput; 
		$this->setIsArrayInput(); 
		$this->setKeyfield(); 
		$result = $this->executeDestroy();
		return $result; 
	}
	function executeDestroy() {
		if ($this->isArrayInput){
			$tmp = json_decode(stripslashes($this->dataInput)); 
			$id_delete = implode(",",$tmp); 
		} else {
			$id_delete = stripslashes($this->dataInput); 
		}
		$sqlStr = "delete from ".$this->master_table ." where ".$this->keyfield ." in(".$id_delete.")";
		$this->setSQL($sqlStr); 
		$this->executeSQL(); 
		
		$result = array(); 
		if ($this->db->ErrorMsg() ==""){
			$result['success'] = true; 
			$result['data'] = array(); 
			$result['total'] =0; 
			$result['message']['note'] = "Data Has Been Delete"; 
		} else {
			$result['success']=false; 
			$result['data'] = array(); 
			$result['total'] = 0; 
			$result['message']['note'] = $this->db->ErrorMsg(); 
		}
		
		return json_encode($result); 
		
	}
}
?>