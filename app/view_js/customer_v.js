valid_script = true;  
ajax_url = 'ajax.handler.php?id=' + page;  

var dynamic_editor_customer = new Ext.ux.DynamicEditorGridTbar({
    border:false,
    remoteSort:true, //optional default true
    storeUrl:ajax_url,
    colStartEdit:1, //when click edit point to column number start from 0
    sortInfo:{field:'name',direction:'ASC'}, //must declaration
    tbarDisable:{  //if not declaration default is true
      add:!ROLE.ADD_DATA,
      save:!ROLE.SAVE_DATA,
      autosave:!ROLE.SAVE_DATA,
      remove:!ROLE.REMOVE_DATA
    }    
}); 

dynamic_editor_customer.store.reload(); 

var main_content = {
  id : id_panel,  
  title:n.text,  
  iconCls:n.attributes.iconCls,  
  items : [dynamic_editor_customer]
}; 