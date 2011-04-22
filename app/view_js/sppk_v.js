/** untuk menandakan bahwa script ini valid dan akan di eksekusi * */
valid_script = true;
ajax_url = 'ajax.handler.php?id=' + page; //biarin saja
/** =============================================================* */
Date.monthNames =
   					["Januari",
    					"Februari",
    					"Maret",
    					"April",
    					"Mei",
    					"Juni",
    					"Juli",
    					"Agustus",
    					"September",
    					"Oktober",
    					"November",
    					"Desember"];

var dsSppk = new Ext.data.GroupingStore({
			url : ajax_url,
			id : 'id',
			baseParams : {
				task : 'doRead' //lihat di controller
			},
                        reader : new Ext.data.JsonReader({
			totalProperty : 'total',
                        root : 'data',
                        fields: [
                            {
                                name:'id'
                            },{
                                name:'jenis'
                            },{
                                name:'tanggal',
                                type:'date',
                                dateFormat: 'Y-m-d'
                            },{
                                name:'perihal'
                            },{
                                name:'kode'
                            },{
                                name:'tahun'
                            }
                        ]}),
			sortInfo : {
				field : 'jenis',
				direction : 'ASC'
			},
			remoteSort : true,
                        remoteGroup:false,
                        groupField:'jenis'

});
		    function date_style(val){
				
				var dt = new Date( val );
		        return dt.format('j F Y');
		    }

var filterSppk = new Ext.ux.grid.GridFilters({
    filters: [
        {
            type:'string',
            dataIndex:'jenis'
        },{
            type:'date',
            
			dataIndex:'tanggal'
			
        },{
            type:'string',
            dataIndex:'string'
        },{
            type:'string',
            dataIndex:'kode'
			
        },{
            type:'numeric',
            dataIndex:'tahun'
        }
    ]
});

var cmSppk = new Ext.grid.ColumnModel({
	defaults : {
		sortable : true
		
	},
	columns : [
            {
                dataIndex : 'id',
                hidden : true,
                hideable : false,
                menuDisabled : true
             },{
                dataIndex:'jenis',
                header:'Jenis',
                width: 150
             },{
                dataIndex:'tanggal',
                header:'Tanggal',
		renderer: date_style,
                width: 100,
                align:'right'

             },{
                dataIndex:'perihal',
                header:'Perihal',
                width: 300

             },{
                dataIndex:'kode',
                header:'Kode',
                width: 130

             },{
                dataIndex:'tahun',
                header:'Tahun',
                width: 50

             }
        ]
});


var limit_store = new Ext.data.SimpleStore({
	fields : ['limit'],
	data : [[30], [50], [100]]
	});

limit_combo = {
        xtype:'combo',
        store : limit_store,
        displayField : 'limit',
        valueField : 'limit',
        typeAhead : true,
        mode : 'local',
        triggerAction : 'all',
        selectOnFocus : true,
        width : 45,
        listeners:{
            render:function(){
                this.setValue(30);
            },
            select:function(){
                gridSppk.getBottomToolbar().get(0).pageSize = parseInt(this.getValue());
                dsSppk.load({
                    params : {
                        start : 0,
                        limit : parseInt(this.getValue())
                    }
                });

            }
        }
};


var gridSppk = new Ext.grid.GridPanel({
	iconCls : 'parent-form',
        border:false,
	ds : dsSppk,
	cm : cmSppk,
	stripeRows : true,
	enableColLock : false,
	view : new Ext.grid.GroupingView({
		forceFit : false,
		groupTextTpl : '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
	}),

	loadMask : true,
	plugins : [filterSppk],
        tbar: [
            {
                text:'Tambah Data',
                iconCls:'add-data',
                tooltip:{
                    title:'Tambah Data',
                    text:'Tambah Data Baru'
                },
                disabled: checkRole(!ROLE.ADD_DATA),
                handler: function(){
                    winEdit.setTitle('Add Data');
                    winEdit.show(this.id); 
                    frm_edit.getForm().reset();

                }
            },'-',{
                text:'Edit Data',
                iconCls:'form-edit',
                disabled: checkRole(!ROLE.EDIT_DATA),
                handler: function(){
                    if (!gridSppk.getSelectionModel().getSelected())
                        return false;
                    frm_edit.getForm().setValues(
                    gridSppk.getSelectionModel().getSelected().data
                    );
                    winEdit.setTitle('Edit Data');
                    winEdit.show(this.id); 
                }
            },'-',{
		text : 'Remove Selected',
		iconCls : 'table-delete',
                disabled: checkRole(!ROLE.REMOVE_DATA),
		id : 'remove-s',
		tooltip : {
			title : 'Remove Selected Item',
			text : 'Remove Selected row in grid'
		},
		handler : function() {
                    a = gridSppk.getSelectionModel().getSelections();
                    if (!a)
                        return false;
                   var data = [];
                   Ext.each(a, function(r, i) {
                       data.push({id:r.data.id});
                   });
				Ext.MessageBox.show({
					title : 'Delete?',
					msg : "Are You Sure to delete Selected(s) Data?",
					buttons : Ext.MessageBox.YESNO,
					fn : function(a) {
						if (a == "yes") {
							Ext.getCmp('content-panel').body.mask(
									'Removing Data', 'x-mask-loading');
							Ext.Ajax.request({
								url : ajax_url,
								params : {
									task : "remove",
									data : Ext.encode(data)
								},
								success : function(response) {
									Ext.getCmp('content-panel').body.unmask();
									var res = Ext.decode(response.responseText);
									if (res.success) {
										dsSppk.reload();
										Ext.example
												.msg('Remove Selected Item',
														'Data has been deleted from Database');

									} else {
										Ext.MessageBox.show({
											title : 'Alert',
											msg : 'Failed to Delete Data: Error Message : '
													+ res.msg,
											buttons : Ext.MessageBox.OK,
											animEl : this.id,
											icon : Ext.MessageBox.ERROR
										});
									}
								}
							});
						}
					},
					animEl : this.id,
					icon : Ext.MessageBox.WARNING
				});

                }

            },'-',{
                text:'Print Mode',
                iconCls:'report-mode',
                menu: [
                    {
                        text:'PDF Report',
                        group:'print',
                        checked:true,
                        checkHandler:function(){
                            btn = gridSppk.getTopToolbar().findByType('button')[4];
                            btn.setText('Print (PDF)');
                            btn.mode = 'PDF';
                            btn.setIconClass('report-pdf');
                        }
                    },{
                        text:'XLS Report',
                        group:'print',
                        checked:false,
                        checkHandler:function(){
                            btn = gridSppk.getTopToolbar().findByType('button')[4];
                            btn.setText('Print (XLS)');
                            btn.mode = 'XLS';
                            btn.setIconClass('report-xls');

                        }
                    }
                ]
            },'-',{
                text:'Print (PDF)',
                mode:'PDF',
                iconCls:'report-pdf',
                disabled: checkRole(!ROLE.PRINT_DATA),
                handler:function(){
                    option = gridSppk.store.lastOptions.params;
                    mode_report = (this.mode=="PDF") ? "&mode=pdf" : "&mode=xls";
                    report_link = 'report.php?id=' + page + mode_report;
                    winReport({
                        id : this.id,
                        title : 'Sample Report',
                        url : report_link,
                        type : this.mode,
                        params:option
                    });
                    
                }

            }
        ],
	bbar : new Ext.PagingToolbar({
				store : dsSppk,
				pageSize : 30,
				plugins : filterSppk,
				displayInfo : true,
				displayMsg : 'Displaying Data {0} - {1} of {2}',
				emptyMsg : "No Data to display",
				items : ['-', {
							// text:'Clear Filter',
							tooltip : {
								title : 'Clear Filter',
								text : 'Clear Searching Filter'
							},
							iconCls : 'drop',
							handler : function() {
								filterSppk.clearFilters();
							}
						}, '-', 'Display Per Page ', limit_combo
                                            ]
			})
        
});


dsSppk.load({
			params : {
				start : 0,
				limit : parseInt(30)
			}
});

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
			allowBlank : false

		}, {
			xtype : 'textfield',
			fieldLabel : 'Perihal',
                        name:'perihal',
			allowBlank : false
		}, {
			fieldLabel : 'Kode',
			name : 'kode',
			width : 200,
			allowBlank : false
		}, {
			xtype : 'numberfield',
			fieldLabel : 'Tahun',
			name : 'tahun'
		}];
var frm_edit = new Ext.FormPanel({
			id : 'frm_edit',
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

var winEdit = new Ext.Window({
			layout : 'fit',
			id : 'winEdit',
			width : 650,
			height : 300,
			closeAction : 'hide',
			iconCls : 'form',
			title : 'Add',
			modal : true,
			plain : true,
			border : false,
			items : [frm_edit

			],
			buttons : [{
				text : 'Save',
				handler : function() {
					if (frm_edit.getForm().isValid()) {
						frm_edit.getForm().submit({
							waitMsg : 'Saving Data',
							params : {
								task : 'saveEdit'
							},
							success : function(a, b) {
								winEdit.hide();
								dsSppk.reload();
								Ext.example.msg('Save Data',
										'Data has been successfully saved');
							},
							failure : function(a, b) {
								Ext.MessageBox.show({
											title : 'Alert',
											msg : 'Failed Save Data : '
													+ b.result.msg,
											buttons : Ext.MessageBox.OK,
											// animEl: n.id,
											icon : Ext.MessageBox.ERROR
										});

							}
						});
					}
				}
			}, {
				text : 'Close',
				handler : function() {
					winEdit.hide();
				}
			}],
			listeners : {
				hide : function() {
					
				}
			}
		});
/* ====================================================================== */

//definisi Panel

var main_content = {
	id : id_panel,
        title:n.text,
        iconCls:n.attributes.iconCls,
	items : [gridSppk],
	listeners : {
		destroy : function() {
			myWin = Ext.getCmp('winEdit');
			if (myWin)
				myWin.destroy();

		}
	}
};

