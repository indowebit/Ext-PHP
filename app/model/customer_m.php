<?php
 class Customer extends msDB {
   
   private $grid; 
   
   public function __construct(){
        $this->connect();
        $this->grid = new Grid;
        $this->grid->setTable('customers');

        $this->grid->addField(
                array(
                    'field' => 'id',
                    'name'  => 'id',
                    'primary'=> true,
                    'meta' => array(
                      'st' => array('type' => 'int'),
                      'cm' => array('hidden' => true, 'hideable' => false, 'menuDisabled' => true)
                    )
                ));

        $this->grid->addField(
                array(
                    'field' => 'name',
                    'name'  => 'name',
                    'meta' => array(
                      'st' => array('type' => 'string', 'allowBlank' => false), 
                      'cm' => array('header' => 'Name','width' => 200,'sortable' => true),
                      'editor' => array('xtype' => 'textfield'), 
                      'filter' => array('type' => 'string')
                    )
                ));    
        $this->grid->addField(
                array(
                    'field' => 'register_date',
                    'name'  => 'register_date',
                    'meta' => array(
                      'st' => array('type' => 'date', 'allowBlank' => false,'dateFormat' => 'Y-m-d'), 
                      'cm' => array('header' => 'Register Date','width' => 90, 'sortable' => true, 'renderer' => "Ext.util.Format.date(val,'d/m/Y')"),
                      'editor' => array('xtype' => 'datefield','format' => 'd/m/Y'),
                      'filter' => array('type' => 'date')
                    )                
                )); 
                

        
        $this->grid->addField(
                array(
                    'field' => 'is_active',
                    'name'  => 'is_active',
                    'meta' => array(
                      'st' => array('type' => 'bool'), 
                      'cm' => array('xtype' => 'checkcolumn','header' => 'Active','width' => 60, 'sortable' => true),
                      'filter' => array('type' => 'boolean')
                    )                
                ));     
                
            
        $this->grid->addField(
                array(
                    'field' => 'is_login',
                    'name'  => 'is_login',
                    'meta' => array(
                      'st' => array('type' => 'bool'), 
                      'cm' => array('xtype' => 'checkcolumn','header' => 'Is Login','width' => 60, 'sortable' => true),
                      'filter' => array('type' => 'boolean')
                    )                
                ));     
       
   }
   
   public function read($request){
     return $this->grid->doRead($request); 
   }
   
   public function create($data){
     return $this->grid->doCreate($data);
   }
   
   public function update($data){
     return $this->grid->doUpdate($data); 
   }
   
   public function destroy($data){
     return $this->grid->doDestroy($data);  
   }
   
   
 }
?>