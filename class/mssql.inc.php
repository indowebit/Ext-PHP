<?php 

//	File name : connect.php
//	Save database connection


require ABSPATH . "includes/adodb/adodb-exceptions.inc.php";
require ABSPATH . "includes/adodb/adodb.inc.php";



class msDB 
{ // define the properties
  public $db,
         $message;
  protected $sql_query,
            $sql_array;
  // initialize class
  function __construct() 
  { 
  	$this->messsage = "initialize class";
  }
  // define the methods
  function connect()
    {
      global $conf_db; 
  	try 
  	{ 		
      $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
      $mode ="mysql"; 
      if ($conf_db)
          $mode = $conf_db['db_type']; 
      $this->db= ADONewConnection($mode);
      if ($conf_db)
        $this->db->Connect($conf_db['db_host'],$conf_db['db_user'],$conf_db['db_pwd'],$conf_db['db_name']) or die("COULD NOT SELECT DATABASE.<br>");
      else
        $this->db->Connect(HOST,USER,PASS,DB) or die("COULD NOT SELECT DATABASE.<br>");
      return 0;
  	} 
  	catch (exception $e) 
  	{
 	 	 	var_dump($e); 		
 	 	 	adodb_backtrace($e->gettrace());
 	 	 	$this->message = $e." -> ".$this->db->ErrorMsg();
 	 	 	return 1;
  	}
  }
  function connectBy($the_db)
  {
  	$this->db = $the_db;
  }
  /**
   * Execute String Sql 
   * Use executeSQL to return resutl
   * @param string $the_sql
   */
  public function setSQL($the_sql)
  { 
  	$this->sql_query = $the_sql;
  }
  public function setArray($the_array)
  {
  	$this->sql_array = $the_array;
  }
  /**
   * Execute sql with Array Pakcet
   *
   * @param string $sql_query
   * @param array $sql_array
   * @return recordset
   */
  protected function execSQL($sql_query, $sql_array)
  {
	  $result = ""; 
  	try
      { 
      	if ($sql_array)
      	{ $result = $this->db->Execute($this->db->Prepare($sql_query), $sql_array); 
          
        }
      	else
      	{$result = $this->db->Execute($sql_query); }
        $this->message = "Execute SQL Succeed ";
       }
    catch (exception $e)
       {
         $this->message = "Failed to execute ".$sql_query.
                          "---->".$e."--->".$this->db->ErrorMsg();
       }
    return $result;
  }
  /**
   * Return recordset after setSQL
   *
   * @return recordset
   */
  public function executeSQL()
  {
    $result = ""; 	
  	try
      { 
      	if ($this->sql_array)
      	{ $result = $this->db->Execute($this->db->Prepare($this->sql_query), $this->sql_array); 
         }
      	else
      	{$result = $this->db->Execute($this->sql_query); }
        $this->message = "Execute SQL Succeed ";
       }
    catch (exception $e)
       {
         $this->message = "Failed to execute ".$this->sql_query.
                          "---->".$e."--->".$this->db->ErrorMsg();
       }
    return $result;
  }
  
  public function getAskTag($len){
  	$tmp = Array(); 
  	for($i=1;$i<=$len;$i++) {
  		$tmp[] = "?"; 
  	}
  	return implode(",",$tmp); 
  }
   // de-initialize class
  function __destruct()
  {
  	 unset($db);
  	 unset($messsage);
  }
//end of class msDB  
}
//end of file
?>