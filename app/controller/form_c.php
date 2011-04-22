<?php
	$task = $_REQUEST['task']; //parameter yang dikirim oleh js
	$handler->loadModel("sppk_m");  //model di core
        $sppk = new sppk(true);
        
	switch($task) {
            case 'saveEdit':
                echo $sppk->saveEdit($_REQUEST); //pake $_REQUEST juga sama aja
            break;
        }

?>
