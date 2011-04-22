/** untuk menandakan bahwa script ini valid dan akan di eksekusi * */
valid_script = true;
ajax_url = 'ajax.handler.php?id=' + page; //biarin saja
/** =============================================================* */

/* =========================membuat form edit =========================== */
var item_form = [{
			xtype : 'hidden',
			name : 'id',
			allowBlank : false
		},{
                        fieldLabel:'Jenis',
                        name:'jenis',
                        width:200
                }, {
			xtype : 'datefield',
			fieldLabel : 'Tanggal',
			name : 'tanggal',
			format : 'd/m/Y',
                        width:200,
			allowBlank : false

		}, {
			xtype : 'textfield',
			fieldLabel : 'Perihal',
                        name:'perihal',
			allowBlank : false,
                        width:200
		}, {
			fieldLabel : 'Kode',
			name : 'kode',
			width : 200,
			allowBlank : false
		}, {
			xtype : 'numberfield',
			fieldLabel : 'Tahun',
			name : 'tahun',
                        width:120
		}];
            
var frm_edit = new Ext.FormPanel({
            title:'Sample Form',
            frame : true,
            url : ajax_url,
            border : false,
            bodyStyle : 'padding:5px 5px 0 5px',
            labelAlign : 'left',
            labelWidth : 200,
            defaults : {
                    width : 100,
                    labelSeparator : ''
            },
            defaultType : 'textfield',
            items : item_form

});

the_content = {
   border:false,
   bodyStyle:'padding:5px',
   layout:'fit',
   items:[frm_edit],
   tbar: [
       {
           text:'Save',
           iconCls:'icon-save',
           disabled: checkRole(!ROLE.SAVE_DATA),
           handler:function(){
                if (frm_edit.getForm().isValid()) {
                        frm_edit.getForm().submit({
                                waitMsg : 'Saving Data',
                                params : {
                                        task : 'saveEdit'
                                },
                                success : function(a, b) {
                                        frm_edit.getForm().reset(); 
                                        Ext.example.msg('Save Data',
                                                        'Data has been successfully saved');
                                },
                                failure : function(a, b) {
                                        Ext.MessageBox.show({
                                                                title : 'Alert',
                                                                msg : 'Failed Save Data : '
                                                                                + b.result.msg,
                                                                buttons : Ext.MessageBox.OK,
                                                                icon : Ext.MessageBox.ERROR
                                                        });

                                }
                        });
                }

           }
       },'-',{
           text:'Reset',
           iconCls:'drop',
           handler:function(){
                frm_edit.getForm().reset(); 
           }
       }
   ]
};


var main_content = {
	id : id_panel,
        title:n.text,
        iconCls:n.attributes.iconCls,
	items : [the_content],
	listeners : {
		destroy : function() {

		}
	}
};
