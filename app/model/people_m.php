<?php
class Person extends msDB {
    var $grid;

    function  __construct() {
        $this->connect();
        $this->grid = new Grid;
        $this->grid->setTable('people');
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
                      'st' => array('type' => 'string'), 
                      'cm' => array('header' => 'Name','width' => 200,'sortable' => true),
                      'filter' => array('type' => 'string')
                    )
                ));
        $this->grid->addField(
                array(
                    'field' => 'birthday',
                    'name'  => 'birthday',
                    'meta' => array(
                      'st' => array('type' => 'date'), 
                      'cm' => array('header' => 'Birthday','width' => 90, 'sortable' => true, 'renderer' => "Ext.util.Format.date(val,'d/m/Y')"),
                      'filter' => array('type' => 'date')
                    )                
                ));
        $this->grid->addField(
                array(
                    'field' => 'height',
                    'name'  => 'height',
                    'meta' => array(
                      'st' => array('type' => 'float'), 
                      'cm' => array('header' => 'Height','width' => 50, 'sortable' => true),
                      'filter' => array('type' => 'numeric')
                    )                
                ));

    }

    function create($request){
        $data = array(
          'name' => $request['name'],
          'birthday' => $this->grid->formatDate($request['birthday']),
          'height' => ($request['height']?$request['height']:0)
        );                
        return $this->grid->doCreate(json_encode($data));
    }

    function edit($id,$request){
       $this->grid->loadSingle = true;
       $this->grid->setManualFilter(" and id = $id"); 
       return $this->grid->doRead($request); 
    }
    
    function read($request){
        return $this->grid->doRead($request);
    }
    function update($request){
        $data = array(
          'id' => $request['id'],
          'name' => $request['name'],
          'birthday' => $this->grid->formatDate($request['birthday']),
          'height' => ($request['height']?$request['height']:0)
        );                
        return $this->grid->doUpdate(json_encode($data));
    }
    
    function doReport($request){
      return $this->grid->dosql($request); 
    }

    function destroy($request){
        return $this->grid->doDestroy($request);
    }
}
?>