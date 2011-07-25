<?php
    $action = $_REQUEST['action'];
    $handler->loadModel('gridgrouping_m');
    $obj = new GridGrouping;
    
    switch ($action){
    	case 'read':
    		echo $obj->read($_POST);	
    	break;
    }    