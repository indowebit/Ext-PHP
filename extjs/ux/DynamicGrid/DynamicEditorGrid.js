Ext.ux.DynamicEditorGrid = Ext.extend(Ext.grid.EditorGridPanel, {  
    start: 0,
    limit: 25,
    remoteSort: true,
    forceFit:false,
    sortInfo: { field: '', direction: '' },
    countLoad:0,
    initComponent: function () {
        config = {
            viewConfig: { forceFit: this.forceFit },
            enableCollock: false,
            loadMask: true,
            stripeRows: true,
            clicksToEdit:1,
            ds: this.createStore(),
            cm: this.defaultColModel(),
            bbar: this.createPagingBar(this.storeJson)
        };    
        
        this.filterPlugin = 0; 
        this.loadMeta = false;
        Ext.apply(this, config);
        Ext.apply(this.initialConfig, config);
        Ext.ux.DynamicEditorGrid.superclass.initComponent.apply(this, arguments);     
    },
    
    onRender: function (ct, position) {
        this.getColumnModel().defaultSortable = true;
        Ext.ux.DynamicEditorGrid.superclass.onRender.call(this, ct, position);
        this.store.load({
            params: {
                start: this.start,
                limit: this.limit
            }
        });
    },
    
    createProxy:function(){
      this.storeProxy = new Ext.data.HttpProxy({
        api: {
            read: this.storeUrl + '&action=read',
            create: this.storeUrl + '&action=create',
            update: this.storeUrl + '&action=update',
            destroy: this.storeUrl + '&action=destroy'
        }
    })
    return this.storeProxy
    },
  
  createReader:new Ext.data.JsonReader({
  }),
  
  createWriter: new Ext.data.JsonWriter({
      encode: true,
      writeAllFields: true
  }),
  
    createStore: function () {
        this.storeJson = new Ext.data.Store({
        proxy: this.createProxy(),
        reader: this.createReader,
        writer: this.createWriter,
            baseParams: this.baseParams,
            autoLoad: false,
            autoSave: false,
            remoteSort: this.remoteSort,
            sortInfo: this.sortInfo,

            listeners: {
                metachange: function (st, meta) {
                  if (!this.loadMeta){
                    this.loadMeta = true; 
                    cm = this.getColumnModel();
                    cmModel = this.formatRender(meta.colModel); 
                    cm.setConfig(cmModel);
                    if (meta.filterList.length){
                      this.filterPlugin = new Ext.ux.grid.GridFilters({
                        filters:meta.filterList
                      }); 
                      this.filterPlugin.init(this); 
                      this.filterPlugin.init(this.paging);
                      this.paging.get(12).enable(); 
                    }else{
                      this.paging.get(12).disable(); 
                    }
                    
                  }       
                },
                
                load:function(){                    
                  if (this.countLoad < 2)
                    this.countLoad++;                           
                },
            write: function (store, action, result, res, rs) {
                if (!store.autoSave)
                    this.body.unmask();
                Ext.example.msg('Save', res.raw.message.note);
            },
            beforewrite: function () {
                if (!this.storeJson.autoSave)
                    this.body.mask('Saving Data', 'x-mask-loading');    
            },                
                exception: function (proxy, type, action, options, res, arg) {
                  if (!this.storeJson.autoSave)
                    this.body.unmask();
                    if (type == 'remote') {
                        Ext.MessageBox.alert('Failure', res.raw.message.note);
                    } else {
                      if (this.countLoad)
                          Ext.MessageBox.alert('Failure', 'Ajax Communication failed');
                    }
                },
                scope: this
            }
        });
        return this.storeJson
    },
  
  
    defaultColModel: function () {
        return new Ext.grid.ColumnModel({
            defaults: { align: 'left', resizable: true, sortable: true, width: 50 }
        });
    }, 
    
  formatRender:function(col_model){
      result = []; 
      Ext.each(col_model,function(col){
        if (col.renderer){
          string_render = col.renderer; 
          col.renderer = function(val){
            return eval(string_render);           
          }
        }
        result.push(col); 
      },this); 
      return result; 
    },

    createPagingBar: function (the_store) {
        this.paging = new Ext.PagingToolbar({
            store: the_store,
            pageSize: this.limit,
            displayInfo: true,
            displayMsg: 'Displaying Data {0} - {1} of {2}',
            emptyMsg: "No Data to display",
            items:['-',this.createClearFilterButton(),'-','Display Per Page ',this.createComboPaging()]
        });
        return this.paging;
    },    
    
    createComboPaging:function(){
      this.comboPaging = new Ext.form.ComboBox({
        typeAhead : true,
        mode : 'local',
        triggerAction : 'all',
        width : 45,
        displayField : 'limit',
        valueField : 'limit',
        store:new Ext.data.SimpleStore({
          fields : ['limit'],
          data : [[25], [50], [100]]
        }),
        listeners:{
           render:function(cmb){
              cmb.setValue(this.limit); 
           },
           select:function(cmb,rec){
            this.paging.pageSize = parseInt(rec.data.limit); 
            this.storeJson.load({
              params:{start:0,limit:parseInt(rec.data.limit)}
            }); 
           },
           scope:this
        }
      }); 
      return this.comboPaging; 
    }
    ,
    createClearFilterButton:function(){
      this.buttonClear = {
                      scope:this,
                      iconCls: 'drop',
                      disabled:true,
                      handler: function () {
                          this.filterPlugin.clearFilters();
                      }
                  };
      return this.buttonClear;
    }     
  
});