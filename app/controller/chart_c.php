<?php
	$task = $_REQUEST['task']; //parameter yang dikirim oleh js
	$handler->loadModel("chart_m");  //model di core
        $chart = new chart(true); 
	switch($task) {
            case 'getChart':
                echo $chart->getChart($_REQUEST); //pake $_REQUEST juga sama aja
            break;
        }

?>
