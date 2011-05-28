valid_script = true;  
ajax_url = 'ajax.handler.php?id=' + page;  


/*----window buyer ---- */

buyer_grid_order = Ext.extend(Ext.grid.EditorGridPanel, {
    title: 'Order',
    region: 'center',
    clicksToEdit:1,
    loadMask:true,
    removeData:[],
    store: new Ext.data.JsonStore({
      url:ajax_url, 
      root:'data', 
      successProperty:'success',
      totalProperty:'total',
      autoLoad: false,
      remoteSort:false, 
      baseParams:{
        action :'getOrder', 
        buyer_id:0
      }, 
      fields:[
      {name:'id', type:'int'},
      {name:'name', type:'string'},
      {name:'price', type:'float'},
      {name:'count', type:'int'}
      ]
    }), 
    initComponent: function() {
        this.createTbar(); 
        this.columns = [
	        {
	          xtype:'numbercolumn', 
              hidden:true,
              dataIndex:'id', 
              hideable:false
	        },
            {
                xtype: 'gridcolumn',
                dataIndex: 'name',
                header: 'Name',
                sortable: true,
                width: 290,
                editor: {
                    xtype: 'textfield'
                }
            },
            {
                xtype: 'numbercolumn',
                dataIndex: 'price',
                header: 'Price',
                sortable: true,
                format:'0.00',
                width: 100,
                align: 'right',
                editor: {
                    xtype: 'numberfield'
                }
            },
            {
                xtype: 'numbercolumn',
                header: 'Count',
                format:'0',
                sortable: true,
                width: 100,
                align: 'right',
                dataIndex: 'count',
                editor: {
                    xtype: 'numberfield'
                }
            }
        ];
        buyer_grid_order.superclass.initComponent.call(this);
    }, 
    createTbar:function(){
      this.tbar = [
      {
        text:'Add Data',
        iconCls:'add-data', 
        scope:this,
        handler:function(){
          rec = new this.store.recordType({});
          this.store.insert(0,rec);
          this.getView().refresh();
          this.startEditing(0,1);                
        }
      },{
        text:'Remove Data',
        iconCls:'table-delete',
        scope:this,
        handler:function(){
          this.stopEditing();
          rec = this.getSelectionModel().getSelectedCell(); 
          if (!rec){
             Ext.example.msg('Peringatan','Seleksi data terlebih dahulu');
             return false;
          }
          record_data = this.store.getAt(rec[0]); 
          if (record_data.data.id){
            this.removeData.push(record_data.data.id);             
          }
	      this.store.remove(this.store.getAt(rec[0]));
	      this.getView().refresh();
	      if (this.store.getCount() > 0){
	        if (rec[0] > 0)
	          this.getSelectionModel().select(rec[0] - 1, rec[1]);
	        else
	          this.getSelectionModel().select(rec[0], rec[1]);
	      }          
        }
      }
      ]
    },
    getRemoveData:function(){
      return this.removeData; 
    }
});



buyer_form = Ext.extend(Ext.Window, {
    title: 'Buyer',
    width: 530,
    height: 459,
    layout: 'border',
    border: false,
    closeAction: 'hide',
    id: 'buyerWindow',
    modal:true,
    initComponent: function() {
        this.buttons = this.createButton(); 
        this.grid = new buyer_grid_order({}); 
        this.items = [
            {
                xtype: 'form',
                region: 'north',
                border: false,
                frame: true,
                url:ajax_url, 
                autoHeight: true,
                layoutConfig: {
                    labelSeparator: ' '
                },
                items: [
                    {
                        xtype: 'hidden',
                        fieldLabel: 'Label',
                        anchor: '100%',
                        name: 'id'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Name',
                        anchor: '100%',
                        name: 'name',
                        allowBlank: false
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Address',
                        anchor: '100%',
                        name: 'address',
                        allowBlank: false
                    },
                    {
                        xtype: 'textarea',
                        anchor: '100%',
                        fieldLabel: 'Information',
                        name: 'information'
                    }
                ]
            },this.grid
        ];
        buyer_form.superclass.initComponent.call(this);
    }, 
    createButton:function(){
      this.botBar = [
      {
        text:'Save',
        scope:this,
        handler:function(){
          this.saveData(); 
        }
      },{
        text:'Close',
        scope:this,
        handler:function(){
          this.hide(); 
        }
      }
      ]; 
      return this.botBar; 
    },
    saveData:function(){
      form = this.get(0).form; 
      if (!form.isValid()){
        Ext.example.msg('Peringatan','Ada data yang tidak valid!'); 
        return false;
      }
      data =[]; 
      this.grid.store.each(function(r,i){
        data.push(r.data); 
      });
      id_data = form.getValues().id; 
      action = (id_data)?'update':'create'; 
      form.submit({
        scope:this,
        params:{
          action:action, 
          detail:Ext.encode(data),
          remove:this.grid.getRemoveData().join(',')
        }, 
        waitMsg:'Saving Data', 
        success:function(){
          this.hide(); 
          this.onSuccess(action); 
        },
        failure:function(form,action){          
	         switch (action.failureType) {
	            case Ext.form.Action.CLIENT_INVALID:
	                Ext.MessageBox.alert('Failure', 'Form fields may not be submitted with invalid values');
	                break;
	            case Ext.form.Action.CONNECT_FAILURE:
	                Ext.MessageBox.alert('Failure', 'Ajax communication failed');
	                break;
	            case Ext.form.Action.SERVER_INVALID:
	                Ext.MessageBox.alert('Failure', action.result.message);
	                break;
	        }  
          
        }
      });
    }, 
    onSuccess:function(action){
      
    },
    onAddData:function(){
      this.get(0).form.reset(); 
      this.grid.store.baseParams.buyer_id = 0; 
      this.grid.store.reload(); 
    },
    reloadGrid:function(buyer_id){
      this.grid.store.baseParams.buyer_id = buyer_id;
      this.grid.store.reload(); 
    }
    
});

win_buyer_form = new buyer_form({
   onSuccess:function(action){
    grid_buyer.store.reload(); 
    grid_order.store.reload(); 
   }
}); 



/*----end window---- */

var grid_buyer = new Ext.ux.PhpDynamicGridPanel({
    title:'Buyers List', 
    region:'center', 
    border:false,
    remoteSort:true, //optional default true
    autoLoadStore:true, //optional default true
    storeUrl:ajax_url,
    sortInfo:{field:'name',direction:'ASC'}, //must declaration
    baseParams:{
      action:'getBuyer'
    }, 
    tbarDisable:{  //if not declaration default is true
      add:!ROLE.ADD_DATA,
      edit:!ROLE.EDIT_DATA,
      remove:!ROLE.REMOVE_DATA
    },
    onAddData:function(bt){
      win_buyer_form.show(bt.id);    
      win_buyer_form.onAddData(); 
   },
    onEditData:function(bt,rec){
      win_buyer_form.setTitle('Edit Data');
      win_buyer_form.show(bt.id); 
      win_buyer_form.get(0).getForm().load({
          waitMsg:'Loading Data..',
          params:{action:'edit',id:rec.data.id}
      }); 
      win_buyer_form.reloadGrid(rec.data.id); 
    },   
    onRemoveData:function(bt,rec){
      data = []; 
      Ext.each(rec,function(r){
        data.push(r.data.id); 
      }); 
      Ext.Ajax.request({
        url: ajax_url, 
        params:{
          action:'destroy',
          data:data.join(",")
        },
        success:function(res){
          result = Ext.decode(res.responseText); 
          if (result.success){
	          this.store.reload(); 
	          grid_order.store.reload();            
          }else{
            Ext.MessageBox.alert('Error',result.message);
          }

        },
        scope:this
      });       
    }    
}); 

grid_buyer.getSelectionModel().on('rowselect',function(sel,index,rec){
  grid_order.store.baseParams.buyer_id = rec.data.id; 
  grid_order.store.reload(); 
}); 

var grid_order = new Ext.ux.DynamicGridPanel({
  title :'Order', 
  border:false,
  region:'south', 
  height:270,
  collapsible:true,  
  remoteSort:true,
  storeUrl:ajax_url, 
  sortInfo:{field:'name', direction:'ASC'}, 
  baseParams:{
    action:'getOrder', 
    buyer_id:0
  }
}); 



var main_content = {
  id : id_panel,  
  title:n.text,  
  iconCls:n.attributes.iconCls,  
  layout:'border', //split for 2 column layout
  items : [grid_buyer,grid_order],
  listeners:{
    destroy:function(){
      my_win = Ext.getCmp('buyerWindow');
      if (my_win)
          my_win.destroy(); 
    }
  }
}; 