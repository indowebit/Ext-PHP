valid_script =true; 
res_url = 'ajax.admin.php?id=' + mypage;
var first_time  = true; 
var proxyUser = new Ext.data.HttpProxy({
			api : {
				read : res_url + '&task=gridUser&action=doRead',
				create : res_url + '&task=gridUser&action=doCreate',
				update : res_url + '&task=gridUser&action=doUpdate',
				destroy : res_url + '&task=gridUser&action=doDestroy'
			}
		});
var readerUser = new Ext.data.JsonReader({
			totalProperty : 'total',
			successProperty : 'success',
			idProperty : 'user_id',
			root : 'data'
		}, [{
					name : 'user_id'
				}, {
					name : 'user_name',
					allowBlank:false
				}, {
					name : 'group_id',
					allowBlank:false
				},{
					name:'real_name',
					allowBlank: false
				},{
					name:'user_password',
					allowBlank:false
				},{
					name:'date_created',
					type:'date',
					dateFormat:'Y-m-d H:i:s'
				},{
					name:'last_login',
					type:'date',
					dateFormat:'Y-m-d H:i:s'
				},{
					name:'is_active',
					type:'bool'
				}]);
var writerUser = new Ext.data.JsonWriter({
			encode : true,
			writeAllFields : false
		});
				
var dsUser = new Ext.data.Store({
			proxy : proxyUser,
			reader : readerUser,
			writer : writerUser,
			autoSave : false,
			autoLoad:false,
			sortInfo : {
				field : 'user_name',
				direction : 'ASC'
			},
			remoteSort : true,
			listeners : {
				write : function(store, action, result, res, rs) {
					if (!this.autoSave)
						Ext.getCmp('userPanel').body.unmask();
					Ext.example.msg('Save', res.raw.message.note);
				},
				beforewrite : function() {
					if (!this.autoSave)
						Ext.getCmp('userPanel').body.mask('Saving Data',
								'x-mask-loading');
				},
				exception : function(proxy, type, action, options, res, arg) {
					if (!this.autoSave)
						Ext.getCmp('userPanel').body.unmask();
						res = Ext.decode(res.responseText);   
					if (type === 'response') {
						Ext.Msg.show({
									title : 'ERROR EXCEPTION',
									msg : res.message.error[0],
									buttons : Ext.MessageBox.OK,
									icon: Ext.MessageBox.ERROR
								});
					}else 
						Ext.Msg.show({
									title : 'ERROR EXCEPTION',
									msg : 'Duplicate Key',
									buttons : Ext.MessageBox.OK,
									icon: Ext.MessageBox.ERROR
								});
				}

			}
		});

var proxyGroup = new Ext.data.HttpProxy({
			api : {
				read : res_url + '&task=gridGroup&action=doRead',
				create : res_url + '&task=gridGroup&action=doCreate',
				update : res_url + '&task=gridGroup&action=doUpdate',
				destroy : res_url + '&task=gridGroup&action=doDestroy'
			}
		});
var readerGroup = new Ext.data.JsonReader({
			totalProperty : 'total',
			successProperty : 'success',
			idProperty : 'group_id',
			root : 'data'
		}, [{
					name : 'group_id'
				}, {
					name : 'group_name',
					allowBlank:false
				}, {
					name : 'group_description'
				}]);
var writerGroup = new Ext.data.JsonWriter({
			encode : true,
			writeAllFields : false
		});
				
var dsGroup = new Ext.data.Store({
			proxy : proxyGroup,
			reader : readerGroup,
			writer : writerGroup,
			autoSave : false,
			autoLoad:false,
			sortInfo : {
				field : 'group_name',
				direction : 'ASC'
			},
			remoteSort : true,
			listeners : {
				write : function(store, action, result, res, rs) {
					if (!this.autoSave)
						Ext.getCmp('GroupPanel').body.unmask();
					dsGroup1.reload(); 	
					Ext.example.msg('Save', res.raw.message.note);
				},
				beforewrite : function() {
					if (!this.autoSave)
						Ext.getCmp('GroupPanel').body.mask('Saving Data',
								'x-mask-loading');
				},
				exception : function(proxy, type, action, options, res, arg) {
					if (!this.autoSave)
						Ext.getCmp('GroupPanel').body.unmask(); 	
						res = Ext.decode(res.responseText);   
					if (type === 'response') {
						Ext.Msg.show({
									title : 'ERROR EXCEPTION',
									msg : res.message.error[0],
									buttons : Ext.MessageBox.OK,
									icon: Ext.MessageBox.ERROR
								});
					}else 
						Ext.Msg.show({
									title : 'ERROR EXCEPTION',
									msg : 'Duplicate Key',
									buttons : Ext.MessageBox.OK,
									icon: Ext.MessageBox.ERROR
								});
				}
			}
		});
dsGroup.load({params:{start:0,limit:30}});


var checkColumn = new Ext.grid.CheckColumn({
			header : "Active",
			dataIndex : 'is_active',
			width : 60
		});

var dsGroup1 = new Ext.data.JsonStore({
							url: res_url,
							baseParams: {
								task:'gridGroup',
								action:'doRead',
								start:0,
								limit:10000
							},
							id:'group_id',
							autoLoad:true,
							totalProperty: 'total',
							root:'data',
							fields:[
								{name:'group_id'},
								{name:'group_name'}
							],
							sortInfo: {field: 'group_name', direction: 'ASC'},
							remoteSort:true,
							listeners: {
								load : function(){
									if (first_time){
										dsUser.load({params:{start:0,limit:30}}); 
										first_time = false; 
									}
								}
								
							}
						}); 		
						
var cmUser = new Ext.grid.ColumnModel({
			defaults : {
				sortable : true
			},
			columns : [
				{
					dataIndex : 'user_id',
					hidden : true,
					hideable : false,
					menuDisabled : true
				},{
					dataIndex:'user_name',
					header:'User Name',
					width:100,
					editor: {
						xtype:'textfield',
						vtype:'alphanum',
						allowBlank:false
					}
				},{
					dataIndex:'real_name',
					header:'Real Name',
					width:150,
					editor: {
						xtype:'textfield',
						allowBlank:false
					}
				},{
					dataIndex:'user_password',
					header:'Password',
					width:100,
					editor: {
						xtype:'textfield',
						allowBlank:false,
						inputType:'password'
					},
					renderer: function(val){
						r = '';
						if (val !='')
							r =  '[hidden]';
						return r; 
					}
				},{
					dataIndex:'group_id',
					header:'Group User',
					width:100,
					editor : {
						xtype : 'combo',
						typeAhead : true,
						triggerAction : 'all',
						store : dsGroup1,
						displayField : 'group_name',
						valueField : 'group_id',
						lazyRender : true,
						listClass : 'x-combo-list-small'
					},
					renderer: function(val){
						index = dsGroup.findExact('group_id',val); 
						if (index != -1){
							rs = dsGroup.getAt(index).data; 
							return rs.group_name; 
						}
					}
					
				},{
					dataIndex:'date_created',
					header:'Date Created',
					renderer: function(val){
						return val.format('d/m/Y H:i:s'); 
					},
					width:120
				},{
					dataIndex:'last_login',
					header:'Last Login',
					renderer: function(val){
						return val.format('d/m/Y H:i:s'); 
					},					
					width:120
				},checkColumn
				]
	
}); 
var limit_store = new Ext.data.SimpleStore({
	fields : ['limit'],
	data : [[30], [50], [100]]
		// from states.js
	});

var limit_combo = new Ext.form.ComboBox({
			store : limit_store,
			displayField : 'limit',
			valueField : 'limit',
			typeAhead : true,
			mode : 'local',
			triggerAction : 'all',
			//readOnly : true,
			selectOnFocus : true,
			width : 45
		});
limit_combo.setValue(30);

limit_combo.on("select", function() {
			Ext.getCmp('pagingBar').pageSize = parseInt(limit_combo.getValue());
			dsUser.load({
						params : {
							start : 0,
							limit : parseInt(limit_combo.getValue())
						}
					});
		});

var filterUser = new Ext.ux.grid.GridFilters({
			filters : [{
						type : 'string',
						dataIndex : 'user_name'
					}, {
						type : 'string',
						dataIndex : 'real_name'
					},{
						type:'string',
						dataIndex:'group_id'
					},{
						type:'date',
						dataIndex:'date_created'
					},{
						type:'date',
						dataIndex:'last_login'
					},{
						type:'boolean',
						dataIndex:'is_active'
					}]
		});
		
var userPanel = new Ext.grid.EditorGridPanel({
	id : 'userPanel',
	title:'User Manager',
	region : 'center',
	iconCls:'user-comment',
	border : true,
	split : false,
	//cmargins : '3 3 0 3',
	margins : '3 0 0 0',
	ds : dsUser,
	cm : cmUser,
	clicksToEdit : 1,
	stripeRows : true,
	enableColLock : false,
	loadMask : true,
	plugins:[filterUser,checkColumn],
	tbar : [{
				text:'Add User',
				iconCls : 'user-add',
				tooltip : 'Add New User',
				handler : function() {
					var u = new dsUser.recordType({
								user_name : '',
								real_name : '',
								group_name:'',
								user_password:'',
								date_created : new Date(),
								last_login : Date.parseDate("0000-00-00 00:00:00", "Y-m-d H:i:s"),
								is_active : true
							});
					userPanel.stopEditing(); 
					dsUser.add(u); 
					userPanel.getSelectionModel().select(dsUser.getCount() - 1,1);
					userPanel.startEditing(dsUser.getCount() - 1, 1);
				}
			}, '-', {
				text:'Delete User',
				iconCls : 'user-delete',
				tooltip : 'Remove User',
				handler : function() {
					var index = userPanel.getSelectionModel()
							.getSelectedCell();
					if (!index) {
						return false;
					}
					var rec = userPanel.store.getAt(index[0]);
					userPanel.store.remove(rec);
					if (dsUser.getCount() > 0) {
						if (index[0] > 0)
							userPanel.getSelectionModel().select(
									index[0] - 1, index[1]);
						else
							userPanel.getSelectionModel().select(
									index[0], index[1]);
					}
					if (!dsUser.autoSave)
						dsUser.save(); 

				}
			}, '-', {
				text:'Save',
				iconCls : 'icon-save',
				id : 'saveuser',
				tooltip : 'Save Changed User',
				handler : function() {
					dsUser.save();
				}
			}, '-', {
				text:'Auto Save',
				iconCls : 'autosave',
				tooltip : 'Quick Save when finish Editing',
				enableToggle : true,
				pressed : false,
				toggleHandler : function(btn, press) {
					if (press)
						Ext.getCmp('saveuser').disable();
					else
						Ext.getCmp('saveuser').enable();
					dsUser.autoSave = press;
					if (press)
						dsUser.save();
				}

			}],	
	bbar : new Ext.PagingToolbar({
				id : 'pagingBar',
				store : dsUser,
				pageSize : parseInt(limit_combo.getValue()),
				plugins : filterUser,
				displayInfo : true,
				  displayMsg: 'Display {0} - {1} of {2}',
				  emptyMsg: "No Data to display",
				items : ['-', {
							tooltip : {
								title : 'Clear Filter',
								text : 'Clear Searching Filter'
							},
							iconCls : 'drop',
							handler : function() {
								filterUser.clearFilters();
							}
						},'-','Display Per Page ', limit_combo]
			}),
	listeners: {
		afteredit: function(e){
			if (e.field != 'is_active'){
				is_active = e.record.data.is_active; 
				e.record.set('is_active',!is_active);
				is_active = !is_active;
				e.record.set('is_active',!is_active);
			}
		}
	}
}); 

				
var cmGroup = new Ext.grid.ColumnModel({
			defaults : {
				sortable : true
			},
			columns : [
				{
					dataIndex : 'group_id',
					hidden : true,
					hideable : false,
					menuDisabled : true
				},{
					dataIndex:'group_name',
					header:'Group Name',
					editor: {
						xtype:'textfield',
						allowBlank:false
					}
				},{
					dataIndex:'group_description',
					header:'Description',
					editor: {
						xtype:'textfield',
						allowBlank:true
					}
				}]
	
}); 

var filterGroup = new Ext.ux.grid.GridFilters({
			filters : [{
						type : 'string',
						dataIndex : 'group_name'
					}]
		});

var limit_combo1 = new Ext.form.ComboBox({
			store : limit_store,
			displayField : 'limit',
			valueField : 'limit',
			typeAhead : true,
			mode : 'local',
			triggerAction : 'all',
			//readOnly : true,
			selectOnFocus : true,
			width : 45
		});
limit_combo1.setValue(30);

limit_combo1.on("select", function() {
			Ext.getCmp('pagingBar1').pageSize = parseInt(limit_combo1.getValue());
			dsGroup.load({
						params : {
							start : 0,
							limit : parseInt(limit_combo1.getValue())
						}
					});
		});

var groupPanel = new Ext.grid.EditorGridPanel({
	id : 'GroupPanel',
	title:'Group Manager',
	region : 'north',
	iconCls:'group-manager',
	border : true,
	height : 250,
	collapsible:true,
	split : false,
	cmargins : '0 0 0 0',
	margins : '0 0 0 0',
	ds : dsGroup,
	cm : cmGroup,
	viewConfig: {
		forceFit:true
	},
	clicksToEdit : 1,
	stripeRows : true,
	enableColLock : false,
	loadMask : true,
	//selModel: new Ext.grid.RowSelectionModel({}),
	plugins:filterGroup,
	tbar : [{
				text:'Add Group',
				iconCls : 'group-add',
				tooltip : 'Add New Group',
				handler : function() {
					var u = new dsGroup.recordType({
								group_name : '',
								group_description : ''
							});
					groupPanel.stopEditing(); 
					dsGroup.add(u); 
					groupPanel.getSelectionModel().select(dsGroup.getCount() - 1,1);
					groupPanel.startEditing(dsGroup.getCount() - 1, 1);
					}
			}, '-', {
				text:'Delete Group',
				iconCls : 'group-delete',
				tooltip : 'Remove Group',
				handler : function() {
					var index = groupPanel.getSelectionModel()
							.getSelectedCell();
					if (!index) {
						return false;
					}
					var rec = groupPanel.store.getAt(index[0]);
					if (rec.data.group_id ==1){
						Ext.MessageBox.show({
				        title: 'Alert',
				           msg: 'Sorry, Administrator Group cannot be Delete',
				           buttons: Ext.MessageBox.OK,
				           animEl: this.id,
				           icon: Ext.MessageBox.WARNING
       					});
						return false; 						
					}
					groupPanel.store.remove(rec);
					if (dsGroup.getCount() > 0) {
						if (index[0] > 0)
							groupPanel.getSelectionModel().select(
									index[0] - 1, index[1]);
						else
							groupPanel.getSelectionModel().select(
									index[0], index[1]);
					}
					if (!dsGroup.autoSave)
						dsGroup.save(); 

				}
			}, '-', {
				text:'Save',
				iconCls : 'icon-save',
				id : 'savegroup',
				tooltip : 'Save Changed User',
				handler : function() {
					dsGroup.save();
				}
			}, '-', {
				text:'Auto Save',
				iconCls : 'autosave',
				tooltip : 'Quick Save when finish Editing',
				enableToggle : true,
				pressed : false,
				toggleHandler : function(btn, press) {
					if (press)
						Ext.getCmp('savegroup').disable();
					else
						Ext.getCmp('savegroup').enable();
					dsGroup.autoSave = press;
					if (press)
						dsGroup.save();
				}

			}],		
	 bbar : new Ext.PagingToolbar({
				id : 'pagingBar1',
				store : dsGroup,
				pageSize : parseInt(limit_combo1.getValue()),
				plugins : filterGroup,
				displayInfo : true,
				  displayMsg: 'Display {0} - {1} of {2}',
				  emptyMsg: "No Data to display",
				items : ['-', {
							tooltip : {
								title : 'Clear Filter',
								text : 'Clear Searching Filter'
							},
							iconCls : 'drop',
							handler : function() {
								filterGroup.clearFilters();
							}
						},'-','Display Per Page ', limit_combo1]
			}),
	  listeners: {
	  	beforeedit: function(e){
	  		if (e.record.data.group_id ==1)
	  			e.cancel =true; 		
	  	}
	  }
}); 


groupPanel.getSelectionModel().on("cellselect",function(select,row,col){
	group_id = dsGroup.getAt(row).data.group_id;
	group_name = dsGroup.getAt(row).data.group_name;
	if (group_id){
		treeMenu.loader.baseParams.group_id = group_id;
		Ext.getCmp('east-panel').setTitle('Menu For '+ group_name + ' Group'); 
		treeMenu.getRootNode().reload(); 
	}
		
}); 
/** Membuat Tree Panel * */
var treeMenu = new Ext.tree.TreePanel({
	region : 'center',
	frame : false,
	border : false,
	autoScroll : true,
	rootVisible : false,
	margins : '0 0 3 0',
	lines : false,
	useArrows : false,
	singleExpand : false,
	loader : new Ext.tree.TreeLoader({
				dataUrl : res_url + '&task=getMenu',
				baseParams: {
					group_id : 0
				},
				listeners : {
					load : function() {
						treeMenu.body.unmask();
						eventPanel.setTitle('Event Menu'); 
						dsEvent.baseParams.menu_id =0; 
						dsEvent.baseParams.group_id =0;
						dsEvent.load(); 
						//treeMenu.root.expand(true);
						// treeMenu.root.firstChild.expand(false);
					},
					beforeload : function() {
						treeMenu.body.mask('Loading Menu', 'x-mask-loading');
					}
				}
			}),
	root : new Ext.tree.AsyncTreeNode(),
	tbar : [{
				text : 'Check All',
				iconCls : 'accept',
				tooltip : 'Checked All Menu',
				handler : function(){
					function changeTrue(node){
						if (node.getDepth()>1)
							node.ui.checkbox.checked = true;
						node.eachChild(function(r,i){
							changeTrue(r); 
						}); 
					}
					rootNode = treeMenu.getRootNode().firstChild;
					changeTrue(rootNode); 
					
				}
			}, '-', {
				text : 'Check None',
				iconCls : 'check-none',
				tooltip : 'Unchecked All Menu ',
				handler : function(){
					function changeFalse(node){
						if (node.getDepth()>1)
							node.ui.checkbox.checked = false;
						node.eachChild(function(r,i){
							changeFalse(r); 
						}); 
					}
					rootNode = treeMenu.getRootNode().firstChild;
					changeFalse(rootNode); 
					
				}
			}, '-',{
				text : 'Save',
				iconCls : 'icon-save',
				tooltip : 'Save checked item to published Menu In Group',
				handler : function() {
					function getChild(node) {
						node_id = "";
						node_id = node.id;
						node_id = node_id.split(".");
						if (node_id[1] != "0")
							data.push({
										menu_id   : node_id[1],
										is_active : node.ui.checkbox.checked,
										group_id  : treeMenu.loader.baseParams.group_id
									});
						node.eachChild(function(r, i) {
									getChild(r);
								});
					}
					var data = [];
					rootNode = treeMenu.getRootNode().firstChild;
					getChild(rootNode);
					if (data.length)
					Ext.MessageBox.show({
						title : 'Save Change?',
						msg : "Are You Sure to save published menu? </br> Checked menu will be published ",
						buttons : Ext.MessageBox.YESNO,
						fn : function(a) {
							if (a == "yes") {
								treeMenu.body.mask(
										'Published Menu', 'x-mask-loading');
								Ext.Ajax.request({
									url : res_url,
									params : {
										task : "saveMenu",
										dataList : Ext.encode(data)
									},
									success : function(response) {
										treeMenu.body.unmask();
										var res = Ext
												.decode(response.responseText);
										if (res.success) {
											Ext.example
													.msg('Publish Menu',
															'Publish Menu Successfully saved');
										} else {
											Ext.MessageBox.show({
												title : 'Alert',
												msg : 'Failed to Save Data: Error Message : '
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
						icon : Ext.MessageBox.INFO
					});

				}
			}],
	listeners : {
		checkchange : function(nodecheck, checked) {
			function changeFalse(node) {
				if (node.ui.checkbox.checked)
					node.ui.checkbox.checked = false;
				node.eachChild(function(r, i) {
							changeFalse(r);
						});
			}

			function changeTrue(node) {
				parent = node.parentNode;
				if (parent.getDepth() > 1) {
					if (!parent.ui.checkbox.checked)
						parent.ui.checkbox.checked = true;
					changeTrue(parent);
				}
			}
			if (!checked)
				if (nodecheck.hasChildNodes())
					changeFalse(nodecheck);
			if (checked)
				changeTrue(nodecheck);

		}
	}
});

treeMenu.getSelectionModel().on("selectionchange", function(s, node) {
			if (node){
				if (node.getDepth() > 1) {
					tmp_id = node.id
					tmp_id = tmp_id.split(".");
					dsEvent.baseParams.menu_id =tmp_id[1]; 
					dsEvent.baseParams.group_id =treeMenu.loader.baseParams.group_id;
					eventPanel.setTitle(String.format('Event Menu ({0})',node.text)); 
					dsEvent.load(); 
				}
			}
				
		});


var proxyEvent = new Ext.data.HttpProxy({
			api : {
				read : res_url + '&task=gridEvent&action=doRead',
				create : res_url + '&task=gridEvent&action=doCreate',
				update : res_url + '&task=gridEvent&action=doUpdate',
				destroy : res_url + '&task=gridEvent&action=doDestroy'
			}
		});
var readerEvent = new Ext.data.JsonReader({
			totalProperty : 'total',
			successProperty : 'success',
			idProperty : 'event_id',
			root : 'data'
		}, [{
					name : 'event_id'
				}, {
					name : 'event_name'
				}, {
					name : 'is_active',
					type:'bool'
				}
			]);
var writerEvent = new Ext.data.JsonWriter({
			encode : true,
			writeAllFields : false
		});
				
dsEvent = new Ext.data.Store({
			proxy : proxyEvent,
			reader : readerEvent,
			writer : writerEvent,
			autoSave : false,
			autoLoad:false,
			sortInfo : {
				field : 'event_name',
				direction : 'ASC'
			},
			baseParams: {
				start:0,
				limit:10000,
				group_id:0,
				menu_id:0
			},
			remoteSort : true,
			listeners : {
				write : function(store, action, result, res, rs) {
					if (!this.autoSave)
						eventPanel.body.unmask();
					Ext.example.msg('Save', res.raw.message.note);
				},
				beforewrite : function() {
					if (!this.autoSave)
						eventPanel.body.mask('Saving Data',
								'x-mask-loading');
				},
				exception : function(proxy, type, action, options, res, arg) {
					if (!this.autoSave)
						eventPanel.body.unmask();
						res = Ext.decode(res.responseText);   
					if (type === 'response') {
						Ext.Msg.show({
									title : 'ERROR EXCEPTION',
									msg : res.message.error[0],
									buttons : Ext.MessageBox.OK,
									icon: Ext.MessageBox.ERROR
								});
					}else 
						Ext.Msg.show({
									title : 'ERROR EXCEPTION',
									msg : 'Duplicate Key',
									buttons : Ext.MessageBox.OK,
									icon: Ext.MessageBox.ERROR
								});
				}

			}
		});
var checkColumn1 = new Ext.grid.CheckColumn({
			header : "Active",
			dataIndex : 'is_active',
			width : 60
		});
		
cmEvent = new Ext.grid.ColumnModel({
			defaults : {
				sortable : true
			},
			columns : [
				{
					dataIndex : 'event_id',
					hidden : true,
					hideable : false,
					menuDisabled : true
				},{
					dataIndex:'event_name',
					header:'Event Menu'
				},checkColumn1
				]
	
}); 
eventPanel = new Ext.grid.EditorGridPanel({
	title:'Event Menu',
	region : 'south',
	height:210,
	iconCls:'conf',
	border : false,
	split : true,
	collapsible:true,
	cmargins : '3 3 3 3',
	margins : '0 0 0 0',
	ds : dsEvent,
	cm : cmEvent,
	clicksToEdit : 1,
	stripeRows : true,
	enableColLock : false,
	loadMask : true,
	plugins:[checkColumn1],
	viewConfig:{
		forceFit:true
	},
	tbar: [
			{
				text:'Save',
				iconCls : 'icon-save',
				id : 'saveevent',
				tooltip : 'Save Changed Event',
				handler : function() {
					dsEvent.save();
				}
			}, '-', {
				text:'Auto Save',
				iconCls : 'autosave',
				tooltip : 'Quick Save when finish Editing',
				enableToggle : true,
				pressed : false,
				toggleHandler : function(btn, press) {
					if (press)
						Ext.getCmp('saveevent').disable();
					else
						Ext.getCmp('saveevent').enable();
					dsEvent.autoSave = press;
					if (press)
						dsEvent.save();
				}
			},'->',
			{
				iconCls : 'accept',
				tooltip : 'Activate All Event',
				handler : function(){
					dsEvent.each(function(r,i){
						r.set('is_active',true)
					}); 
				}
			}, '-', {
				iconCls : 'check-none',
				tooltip : 'Deactive All Event ',
				handler : function(){
					dsEvent.each(function(r,i){
						r.set('is_active',false)
					}); 
				}
			}			
	]	
}); 

var eastPanel = {
	title:'Menu Manager',
	border:true,
	iconCls:'app',
	id:'east-panel',
	layout:'border',
	region:'east',
	width:250,
	margins:'5 5 5 3',
	cmargins:'5 5 5 3',
	collapsible:true,
	items:[treeMenu,eventPanel]
};

var centerPanel = {
	border : false,
	region:'center',
	layout : 'border',
	margins:'5 0 5 5',
	items : [userPanel,groupPanel]
};

var main_content = {
	id : 'main_content',
	layout : 'border',
	title : 'User Manager',
	iconCls : 'app',
	border : true,
	bodyStyle : 'padding:5px',
	frame : false,
	items : [centerPanel,eastPanel],
	listeners : {
		render : function() {
			//treePanel.disable();
		},
		destroy : function() {
			//treePanel.enable();
			//treePanel.getRootNode().reload();
			pageAdmin = "";
		}
	}
};