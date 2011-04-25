<?php
    $action = $_REQUEST['action'];
    $handler->loadModel('people_m');
    $person = new Person;

    switch ($action){
        case 'read':
            echo $person->read($_POST);
            break;
        case 'create':
            echo $person->create($_POST);
            break;
        case 'update':
            echo $person->update($_POST);
            break;
        case 'destroy':
            echo $person->destroy($_POST['data']);
            break;            
        case 'edit':
          echo $person->edit($_POST['id'],$_POST); 
          break; 
    }
?>