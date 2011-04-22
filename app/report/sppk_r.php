<?php
    $file = ($_GET['mode']=='pdf')?"sppk_r.pdf.php":"sppk_r.xls.php";
    $handler->loadModel("sppk_m");
	$sppk = new sppk(true); 
    $rs = $sppk->doRead($_REQUEST,1);
    include($file);
?>
