<?php
    $action = $_REQUEST['action'];
    $handler->loadModel('customer_m');
    $customer = new Customer;

    switch ($action){
        case 'read':
            echo $customer->read($_POST);
            break;
        case 'create':
            echo $customer->create($_POST['data']);
            break;
        case 'update':
            echo $customer->update($_POST['data']);
            break;
        case 'destroy':
            echo $customer->destroy($_POST['data']);
            break;            
    }
?>