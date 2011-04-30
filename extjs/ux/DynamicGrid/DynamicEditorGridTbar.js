Ext.ux.DynamicEditorGridTbar = Ext.extend(Ext.ux.DynamicEditorGrid, {  
  tbarDisable:{add:false,autosave:false,save:false,remove:false},
  colStartEdit:0,
  initComponent: function () {
        this.createTbar(); 
        if (this.tbar)
         this.topBar.add(this.tbar);                 
        this.tbar = this.topBar;          
        Ext.ux.DynamicEditorGridTbar.superclass.initComponent.apply(this, arguments);       
  },
  
    createTbar:function(){
      this.topBar = new Ext.Toolbar({items:[
      {
        text:'Add Data',
        iconCls:'add-data',
        scope:this,
        disabled:this.tbarDisable.add,
        handler:function(bt){
          rec = new this.store.recordType({});
          this.store.insert(0,rec);
          this.getView().refresh();
          this.startEditing(0,this.colStartEdit);           
        }
      },'-',{
        text:'Auto Save',
        iconCls:'autosave',
        disabled:this.tbarDisable.autosave,
        enableToggle:true,
        pressed:false,
        scope:this,
        handler:function(bt){
          this.store.autoSave = bt.pressed;
          if (bt.pressed)
            this.topBar.get(4).disable();
          else
              this.topBar.get(4).enable(); 
          if (bt.pressed)
            this.store.save();   
            
        }
      },'-',{
        text:'Save',
        iconCls:'icon-save',
        disabled:this.tbarDisable.save,
        scope:this,
        handler:function(){
          this.store.save();
        }
        
      },'-',{
        text:'Remove Data',
        iconCls:'table-delete',
        disabled:this.tbarDisable.remove,
        scope:this,
        handler:function(bt){
          this.stopEditing();
          the_rec = this.getSelectionModel().getSelectedCell(); 
          if (!the_rec)
            Ext.example.msg('Peringatan','Seleksi data terlebih dahulu');
          else{
            Ext.MessageBox.show({ 
                title:'Peringatan',  
                msg:'Yakin akan menghapus data ini?',  
                buttons : Ext.MessageBox.YESNO,  
                animEl:bt.id,  
                icon :Ext.MessageBox.WARNING,  
                fn:function(b){
                  if (b =='yes')
                    this.removeData(the_rec);
                },
                  scope:this
            });
          }
        }
      }
      ]});
      return this.topBar; 
    },
    
    removeData:function(rec){
      this.store.remove(this.store.getAt(rec[0]));
      this.getView().refresh();
      if (this.store.getCount() > 0){
        if (rec[0] > 0)
          this.getSelectionModel().select(rec[0] - 1, rec[1]);
        else
          this.getSelectionModel().select(rec[0], rec[1]);
      }
      this.store.save();
    }
    
});