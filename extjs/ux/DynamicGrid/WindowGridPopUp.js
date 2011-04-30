Ext.ux.WindowGridPopUp = Ext.extend(Ext.Window, {
    autoLoadStore:true,
    hiddenSelect: false,
    remoteSort: true,
    forceFit:false,
    sortInfo: {},
    limit: 25,
    baseParams: {},
    storeUrl: '',
    initComponent: function () {
        config = {
            closeAction: 'hide',
            closeable: true,
            modal: true,
            layout: 'fit',
            items: this.createGrid(),
            buttons: this.createButton()
        };
        Ext.apply(this, config);
        Ext.apply(this.initialConfig, config);
        Ext.ux.WindowGridPopUp.superclass.initComponent.apply(this, arguments);
        //jika mau memakai listeners
        //this.addEvents('select');
    },
    createGrid: function () {
        this.grid = new Ext.ux.DynamicGridPanel({
            forceFit:this.forceFit,
            autoLoadStore:this.autoLoadStore,
            storeUrl: this.storeUrl,
            baseParams: this.baseParams,
            sortInfo: this.sortInfo,
            limit: this.limit,
            remoteSort:this.remoteSort
        })
        return this.grid
    },
    createButton: function () {
        this.button = [
      {
          text: 'Select',
          scope: this,
          hidden: this.hiddenSelect,
          handler: function () {
              record = this.grid.getSelectionModel().getSelected();
              if (!record) {
                  this.msgError('Alert', 'Select data first');
                  return false;
              }
              this.onSelect(record);
          }
      }, {
          text: 'Close',
          scope: this,
          handler: function () {
              this.onCloseWindow();
          }
      }
    ];
        return this.button
    },
    onCloseWindow: function () {
        this.hide();
    },
    onSelect: function (record) {
        //inject ke listeners fire event
        //this.fireEvent('select', this, record);
    },
    reload: function () {
        this.grid.store.reload(); 
    },
    msgError: function (title, message) {
        Ext.MessageBox.show({
            title: title,
            msg: message,
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.WARNING
        });
    }
});