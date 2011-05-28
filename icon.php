<?php
		include_once("config_sistem.php");
		include_once("class/class.msDB.php"); 
		include_once("class/class.grid.php");

		$grid = new grid(true); 
		
		$task = $_REQUEST['task']; 
		
		switch($task) {
			case 'getIcon': 
				$grid->setTable("iconcls"); 
				$grid->addField(
						array(
							"field"=>"id",
							"name"=>"id"
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
				
				$result = $grid->doRead($_REQUEST); 	
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
				header('Cache-Control: no-cache, must-revalidate');
				header('Pragma: no-cache');
				header('Content-Type: application/json');
				echo $result; 
			break; 
		}
		
?> 