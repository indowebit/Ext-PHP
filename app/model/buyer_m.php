<?php
class Buyer extends msDB {
  private $grid; 
  
  public function __construct(){
    $this->connect(); 
    $this->grid = new Grid; 
    $this->grid->setTable("buyers"); 
    $this->grid->addField(
                array(
                    'field' => 'id',
                    'name'  => 'id',
                    'primary'=> true,
                    'meta' => array(
                      'st' => array('type' => 'int'),
                      'cm' => array('hidden' => true, 'hideable' => false)
                    )      
    ));
    $this->grid->addField(
            array(
                'field' => 'name',
                'name'  => 'name',
                'meta' => array(
                  'st' => array('type' => 'string'), 
                  'cm' => array('header' => 'Name','width' => 200,'sortable' => true),
                  'filter' => array('type' => 'string')
                )
            ));  
    $this->grid->addField(
            array(
                'field' => 'address',
                'name'  => 'address',
                'meta' => array(
                  'st' => array('type' => 'string'), 
                  'cm' => array('header' => 'Address','width' => 300,'sortable' => true),
                  'filter' => array('type' => 'string')
                )
            ));     

    $this->grid->addField(
            array(
                'field' => 'information',
                'name'  => 'information',
                'meta' => array(
                  'st' => array('type' => 'string'), 
                  'cm' => array('header' => 'Information','width' => 350,'sortable' => true),
                  'filter' => array('type' => 'string')
                )
            ));               
                    
  }
  
  public function getBuyer($request){
    return $this->grid->doRead($request); 
  }
  
  public function edit($id,$request){
     $this->grid->loadSingle = true;
     $this->grid->setManualFilter(" and id = $id"); 
     return $this->grid->doRead($request); 
  }

    
  public function getOrder($buyer_id,$request){
    $grid_order = new Grid; 
    $grid_order->setTable('orders');
    $grid_order->setManualFilter(" and buyer_id = $buyer_id");
    $grid_order->addField(
                  array(
                    'field' => 'id',
                    'name' => 'id',
                    'primary' => true,
                    'meta' => array(
                      'st' => array('type' => 'int'),
                      'cm' => array('hidden' => true, 'hideable' => false)
                    )                      
                  )
                );
    $grid_order->addField(
                  array(
                    'field' => 'name',
                    'name' => 'name',
                    'meta' => array(
                      'st' => array('type' => 'string'), 
                      'cm' => array('header' => 'Name', 'width' => 250, 'sortable' => true)
                    )
                  )
                );
    $grid_order->addField(
                  array(
                    'field' => 'price',
                    'name' => 'price',
                    'meta' => array(
                      'st' => array('type' => 'float'), 
                      'cm' => array('header' => 'Price', 'width' => 100, 'sortable' => true, 'align' => 'right')
                    )                  
                  )
                );   

    $grid_order->addField(
                  array(
                    'field' => 'count',
                    'name' => 'count',
                    'meta' => array(
                      'st' => array('type' => 'int'), 
                      'cm' => array('header' => 'Count', 'width' => 100, 'sortable' => true, 'align' => 'right')
                    )                  
                  )
                ); 

  return $grid_order->doRead($request);                 
                    
  }

  public function create($post){
   
    /** start build query **/
    $this->db->BeginTrans(); 
    /** parent query **/     
    $str ="INSERT INTO buyers (name,address,information) VALUES('%s','%s','%s')"; 
    $query= sprintf($str,mysql_real_escape_string($post['name']),
                         mysql_real_escape_string($post['address']), 
                         mysql_real_escape_string($post['information'])); 
                         
   $this->setSQL($query);   
    /** child query **/
   $ok = $this->executeSQL(); 
   if ($ok)
    if ($post['detail'] != '[]'){
      $sql = array(); 
      $buyer_id = $this->getLastID(); 
      $detail = json_decode(stripslashes($post['detail'])); 
      foreach ($detail as $row){
        $col = array(); 
        $val = array(); 
        $col[]= 'buyer_id'; 
        $val[]= $buyer_id; 
        foreach ($row as $head=>$value){
          $col[] =  $head; 
          $val[] = "'". mysql_real_escape_string($value) ."'";     
        }
        $sql[] = sprintf("INSERT INTO orders (%s) VALUES (%s)", implode(',',$col),implode(',',$val));
      }    
      
      foreach ($sql as $str){
        if ($ok){
          $this->setSQL($str);
          $ok = $this->executeSQL(); 
        }
      }
    }
    if ($ok)
      $this->db->CommitTrans(); 
    else
      $this->db->RollbackTrans(); 
    /** end build query **/

    $result = new stdClass(); 
    $result->success = ($ok)?true:false; 
    $result->message = $this->db->ErrorMsg(); 
    
    return json_encode($result); 
  }
  
  public function update($post){
   
    /** start build query **/
    $this->db->BeginTrans(); 
    /** parent query **/     
    $str ="UPDATE buyers SET name='%s', address='%s', information = '%s' WHERE id = %s"; 
    $query= sprintf($str,mysql_real_escape_string($post['name']),
                         mysql_real_escape_string($post['address']), 
                         mysql_real_escape_string($post['information']),
                         mysql_real_escape_string($post['id'])); 
                                               
   $this->setSQL($query);   
   $ok = $this->executeSQL();
   /** child query update **/ 
   if ($ok)
    if ($post['detail'] != '[]'){
      $sql = array(); 
      $detail = json_decode(stripslashes($post['detail'])); 
      foreach ($detail as $row){
        if (isset($row->id)){
            $fields = array();
            $id = 0; 
            foreach ($row as $head=>$value){
              if ($head != 'id'){
                $fields[] = $head . '='. "'".mysql_real_escape_string($value)."'";
              }else{
                $id = $value; 
              }
    
            }
           $query = "UPDATE orders SET %s WHERE id=%s";
           $query = sprintf($query,implode(',',$fields),$id); 
           $sql[] = $query;           
        }else{
          $col = array(); 
          $val = array(); 
          $col[]= 'buyer_id'; 
          $val[]= $post['id']; 
          foreach ($row as $head=>$value){
            $col[] =  $head; 
            $val[] = "'". mysql_real_escape_string($value) ."'";     
          }
          $sql[] = sprintf("INSERT INTO orders (%s) VALUES (%s)", implode(',',$col),implode(',',$val)); 
        }

      }    
      
      foreach ($sql as $str){
        if ($ok){
          $this->setSQL($str);
          $ok = $this->executeSQL(); 
        }
      }
    }
    
    if ($post['remove'])
      if ($ok){
        $sql = "DELETE FROM orders WHERE id IN (%s)"; 
        $query = sprintf($sql,$post['remove']); 
        $this->setSQL($query);
        $ok = $this->executeSQL(); 
      }
      
    if ($ok)
      $this->db->CommitTrans(); 
    else
      $this->db->RollbackTrans(); 
    /** end build query **/

    $result = new stdClass(); 
    $result->success = ($ok)?true:false; 
    $result->message = $this->db->ErrorMsg(); 
    
    return json_encode($result); 
  }
  
  
  public function destroy($data){
    $this->db->BeginTrans();     
    $sql = "DELETE FROM orders WHERE buyer_id in(%s)"; 
    $query = sprintf($sql,$data);    
    $this->setSQL($query);
    $ok = $this->executeSQL();
    if ($ok){
      $sql = "DELETE FROM buyers WHERE id in (%s)"; 
      $query = sprintf($sql,$data);
      $this->setSQL($query);
      $ok = $this->executeSQL();
    }
        
    if ($ok)
      $this->db->CommitTrans(); 
    else
      $this->db->RollbackTrans(); 
          
    $result = new stdClass(); 
    $result->success = ($this->db->ErrorMsg()!='')?false:true; 
    $result->message = $this->db->ErrorMsg(); 
    
    return json_encode($result); 
  }
}
?>