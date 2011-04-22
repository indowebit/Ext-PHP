<?php
	session_start();
	$userid = isset($_SESSION['userid'])?$_SESSION['userid']:0; 
        
	function formatDate($date){
		$tmp = explode("-",$date); 
		$result = array_reverse($tmp); 
		return implode("/",$result); 
	}
	function formatDate2($date){
                $dateMonth = Array();
                $dateMonth[0] = "";
                $dateMonth[1] = "Januari";
                $dateMonth[2] = "Februari";
                $dateMonth[3] = "Maret";
                $dateMonth[4] = "April";
                $dateMonth[5] = "Mei";
                $dateMonth[6] = "Juni";
                $dateMonth[7] = "Juli";
                $dateMonth[8] = "Agustus";
                $dateMonth[9] = "September";
                $dateMonth[10] = "Oktober";
                $dateMonth[11] = "Nopember";
                $dateMonth[12] = "Desember";
		$tmp = explode("-",$date);
                $month_number = $tmp[1];
                $month_number = $month_number + 0;
                $tmp[1] = $dateMonth[$month_number];
		$result = array_reverse($tmp);
		return implode(" ",$result);
	}
        function getYear($val){
            $temp = explode('-', $val);
            return $temp[0];
        }
        function addNull($val){
            $tmp = $val;
            if (is_numeric($val)){
                $num = $val + 0;
                if ($num <10)
                    $tmp = "0". $num;
            }
            return $tmp;
        }

        if ($userid){
		include_once("config_sistem.php");
		include_once("class/mssql.inc.php"); 
		include_once("class/class.grid.php");
		include_once("class/class.handler.php");
		$handler = new handler(true); 
		$id = isset($_GET['id'])?$_GET['id']:0;
		if ($id){
			$id = explode(".",$id);
			$report = $handler->getReport($id[1]); 
			if ($report)
				if (file_exists("app/report/$report")){
                                        include_once 'app/config/config.db.php';
					include_once("app/report/$report");
				}
		}
	}
?>