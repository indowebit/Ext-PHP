<?php
    $action = $_REQUEST['action'];
    $handler->loadModel('buyer_m');
    $buyer = new Buyer;

    switch ($action){
        case 'getBuyer':
            echo $buyer->getBuyer($_POST);
            break;
        case 'getOrder':
        	echo $buyer->getOrder($_POST['buyer_id'],$_POST); 
        	break; 
        case 'create':
            echo $buyer->create($_POST);
            break;
        case 'update':
            echo $buyer->update($_POST);
            break;
        case 'destroy':
            echo $buyer->destroy($_POST['data']);
            break;            
        case 'edit':
          echo $buyer->edit($_POST['id'],$_POST); 
          break; 
    }
?>