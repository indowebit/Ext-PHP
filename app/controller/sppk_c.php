<?php
	
	$task = $_REQUEST['task']; //parameter yang dikirim oleh js
	$handler->loadModel("sppk_m");  //model di core
        $sppk = new sppk(true); 
	switch($task) {
            case 'doRead':
                echo $sppk->doRead($_REQUEST); //pake $_REQUEST juga sama aja
            break;
            case 'saveEdit':
                echo $sppk->saveEdit($_REQUEST); //pake $_REQUEST juga sama aja
            break;
            case 'remove':
                echo $sppk->remove($_REQUEST['data']); //pake $_REQUEST juga sama aja
            break;

        }


?>
