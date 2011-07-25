<?php

class GridGrouping extends msDB {
	
	private $grid; 
	
	public function __construct(){
		
		$this->connect(); 
		$this->grid = new Grid; 
		
		$this->grid->setTable('gridgrouping'); 
		
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
                    'field' => 'groupby',
                    'name'  => 'groupby',
                    'meta' => array(
                      'st' => array('type' => 'string'), 
                      'cm' => array('header' => 'Group By','width' => 100,'sortable' => true, 'groupable' => true),
                      'filter' => array('type' => 'string')
                    )
                ));   

        $this->grid->addField(
                array(
                    'field' => 'name',
                    'name'  => 'name',
                    'meta' => array(
                      'st' => array('type' => 'string'), 
                      'cm' => array('header' => 'Name','width' => 150,'sortable' => true, 'groupable' => false),
                      'filter' => array('type' => 'string')
                    )
                ));    	

        $this->grid->addField(
                array(
                    'field' => 'number',
                    'name'  => 'number',
                    'meta' => array(
                      'st' => array('type' => 'int'), 
                      'cm' => array('header' => 'Number','width' => 80,'sortable' => true, 'groupable' => false),
                      'filter' => array('type' => 'string')
                    )
                ));    	                
                
	}
	
   public function read($request){
     return $this->grid->doRead($request); 
   }

   
	
}