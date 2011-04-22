<?php 
define('CONF_DB','../config_sistem.php'); 
$errors =array();
/**
 * @param object
 * @param string File name
 */
function populate_db($database, $sqlfile='db_sistem.sql',$sistem=1) {
	global $errors;

	$mqr = @get_magic_quotes_runtime();
	@set_magic_quotes_runtime(0);
        if ($sistem)
            $sqlfile = 'sql/' . $sqlfile;
	$query = fread( fopen($sqlfile, 'r' ), filesize($sqlfile) );
	@set_magic_quotes_runtime($mqr);
	$pieces  = split_sql($query);

	for ($i=0; $i<count($pieces); $i++) {
		$pieces[$i] = trim($pieces[$i]);
		if(!empty($pieces[$i]) && $pieces[$i] != "#") {
			if (!mysql_query($pieces[$i],$database)) 
                $errors[] = mysql_error($database); 
		}
	}
}

/**
 * @param string
 */
function split_sql($sql) {
	$sql = trim($sql);
	$sql = @ereg_replace("\n#[^\n]*\n", "\n", $sql);

	$buffer = array();
	$ret = array();
	$in_string = false;

	for($i=0; $i<strlen($sql)-1; $i++) {
		if($sql[$i] == ";" && !$in_string) {
			$ret[] = substr($sql, 0, $i);
			$sql = substr($sql, $i + 1);
			$i = 0;
		}

		if($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
			$in_string = false;
		}
		elseif(!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {
			$in_string = $sql[$i];
		}
		if(isset($buffer[1])) {
			$buffer[0] = $buffer[1];
		}
		$buffer[1] = $sql[$i];
	}

	if(!empty($sql)) {
		$ret[] = $sql;
	}
	return($ret);
}


function test_sql_connection($ndb=0)
{
    global $errors;

        if (!function_exists('mysql_connect')){
                $errors[] = "PHP tidak mensupport mysql!";
         } elseif (! $connect_id = @mysql_connect($_POST['dbserver'], $_POST['dbuser'], $_POST['dbpass'])) {
        $errors[] = "Tidak bisa terkoneksi dengan Mysql<br /Pesan dari Mysql: " . mysql_error();
    } else {
	 if (!$ndb) {
	   if (! mysql_select_db($_POST['dbname'], $connect_id))
              $errors[]= "Database yang anda sebutkan tidak ditemukan DB :'{$_POST['dbname']}' Silahkan Cek Nama Database Anda";
	 } else { 
	    //mysql_query("DROP DATABASE IF EXISTS {$_POST['dbname']}",$connect_id) or die(mysql_error());  
		if (! mysql_query("CREATE DATABASE {$_POST['dbname']}",$connect_id))
		    $errors[] = mysql_error($connect_id); 
	 } 		  
    }
}


function build_cfg_file()
{
$cfg = "<?php
// MySQL configuration
define('HOST','{$_POST['dbserver']}');        // Your database server
define('USER','{$_POST['dbuser']}');        // Your mysql username
define('PASS','{$_POST['dbpass']}');                // Your mysql password
define('DB','{$_POST['dbname']}');        // Your mysql database name
define('SITE_TITLE','{$_POST['site_title']}');
define('MENU_TITLE','{$_POST['menu_title']}');
define( 'ABSPATH', dirname(__FILE__) . '/' );
?>"; 
return $cfg; 
}

function write_config_file()
{
   global $errors;
    $config = build_cfg_file();
    @unlink(CONF_DB);
    if ($fd = @fopen(CONF_DB, 'wb')) {
        fwrite($fd, $config);
        fclose($fd);
    } else {
        $errors[]= "Tidak bisa Menulis config file :  '{CONF_DB}'";
    }
}

?> 