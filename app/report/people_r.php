<?php
   $handler->loadModel("people_m"); 
  $people = new Person;
  
  $rs = $people->doReport($_POST); 
  
  $include_file = ($_POST['mode']=='pdf')?'pdf.php':'xls.php'; 
  
  include 'people/'.$include_file;
  
  
?>