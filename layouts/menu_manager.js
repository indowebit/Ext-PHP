/** untuk menandakan bahwa script ini valid dan akan di eksekusi * */
valid_script = true;
res_url = 'ajax.admin.php?id=' + mypage;
/** =============================================================* */

var nodeSelected;
function addMenu(button) {
	node = treeMenu.getSelectionModel().getSelectedNode();
	if (node) {
                winMenu.show(button.id);
                if (node.getDepth() < 2 )
                    node = node;
		else if (node.isLeaf())
			node = node.parentNode;
		nodeSelected = node;
		disableTrigger = (button.text == "Add Menu") ? false : true;
		disableHandler(disableTrigger);
		detailMenu = Ext.getCmp('detailMenu');
		textMenu = button.text + ' On ' + node.text;
		winMenu.setTitle(textMenu);
		winMenu.setIconClass(node.attributes.iconCls);
		detailMenu.getBottomToolbar().get(2).setText('Cancel');
		Ext.getCmp('GroupMenu').disable();
		detailMenu.expand();
		detailMenu.getForm().reset();
		parent_tmp = "";
		parent_tmp = node.id;
		parent_tmp = parent_tmp.split(".");
		detailMenu.getForm().setValues({
					parent_id : parent_tmp[1],
					isMenu : !disableTrigger
				});

	} else {
		Ext.MessageBox.show({
					title : 'Alert',
					msg : 'Please Select Menu Item First',
					buttons : Ext.MessageBox.OK,
					icon : Ext.MessageBox.WARNING,
					animEl : this.id
				});
	}
}

function disableHandler(disable) {
	trigger = Ext.getCmp('detailMenu').findByType('trigger');
	if (disable) {
		trigger[0].disable();
		trigger[1].disable();
                trigger[2].disable();
	} else {
		trigger[0].enable();
		trigger[1].enable();
                trigger[2].enable();
	}

}

/** Membuat Tree Panel * */
var treeMenu = new Ext.tree.TreePanel({
	id : 'treeMenu',
	region : 'center',
	frame : false,
	border : true,
	autoScroll : true,
        enableDD: true,
	rootVisible : false,
	margins : '3 0 3 3',
	lines : true,
	useArrows : false,
	singleExpand : false,
        containerScroll: true,
        dropConfig: {appendOnly:true},
	loader : new Ext.tree.TreeLoader({
				dataUrl : res_url + '&task=getMenu',
				listeners : {
					load : function() {
						treeMenu.body.unmask();
						treeMenu.root.expand(true);
                                                //treeMenu.root.firstChild.expand(false);
					},
					beforeload : function() {
						treeMenu.body.mask('Loading Menu', 'x-mask-loading');
					}
				}
			}),
	root : new Ext.tree.AsyncTreeNode({draggable: false}),
	tbar : [{
				text : 'Add Menu',
				iconCls : 'menu-add',
				tooltip : 'Add New Menu in Selected Menu',
				handler : addMenu
			}, '-', {
				text : 'Add Sub Menu',
				iconCls : 'submenu-add',
				tooltip : 'Add New Sub Menu in Selected Sub Menu',
				handler : addMenu
			}, '-', {
				text : 'Remove Menu',
				iconCls : 'menu-remove',
				tooltip : 'Remove Menu Item or Sub Menu Item Selected',
				handler : function() {
					function getChild(node) {
						node_id = "";
						node_id = node.id;
						node_id = node_id.split(".");
						if (node_id[1] != "0")
							data.push({
										id : node_id[1]
									});
						node.eachChild(function(r, i) {
									getChild(r);
								});
					}
					nodeS = treeMenu.getSelectionModel().getSelectedNode();
					if (nodeS) {
						if (nodeS.getDepth() > 1) {
							var data = [];
							getChild(nodeS);
							Ext.MessageBox.show({
								title : 'Delete Menu?',
								msg : "Are You Sure to Delete Selected Menu? </br> Child Menu under sub menu selection also to be removed ",
								buttons : Ext.MessageBox.YESNO,
								fn : function(a) {
									if (a == "yes") {
										Ext.getCmp('GroupMenu').body.mask(
												'Removing Menu',
												'x-mask-loading');
										Ext.Ajax.request({
											url : res_url,
											params : {
												task : "remove",
												dataList : Ext.encode(data)
											},
											success : function(response) {
												Ext.getCmp('GroupMenu').body
														.unmask();
												var res = Ext.decode(response.responseText);
												if (res.success) {
													Ext.example
															.msg('Remove Menu',
																	'Menu Has Been Removed');
													Ext.getCmp('detailMenu')
															.getForm().reset();
													nodeS.remove();
												} else {
													Ext.MessageBox.show({
														title : 'Alert',
														msg : 'Failed Remove Menu: Error Message : '
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
					} else {
						Ext.MessageBox.show({
									title : 'Alert',
									msg : 'Please Select Menu Item First',
									buttons : Ext.MessageBox.OK,
									icon : Ext.MessageBox.WARNING,
									animEl : this.id
								});
					}
				}
			}, '-', {
				text : 'Publish Checked Item',
				iconCls : 'accept',
				tooltip : 'Save checked item to published menu',
				handler : function() {
					function getChild(node) {
						node_id = "";
						node_id = node.id;
						node_id = node_id.split(".");
						if (node_id[1] != "0")
							data.push({
										id : node_id[1],
										checked : node.ui.checkbox.checked
									});
						node.eachChild(function(r, i) {
									getChild(r);
								});
					}
					var data = [];
					rootNode = treeMenu.getRootNode().firstChild;
					getChild(rootNode);
					Ext.MessageBox.show({
						title : 'Save Change?',
						msg : "Are You Sure to save published menu? </br> Checked menu will be published ",
						buttons : Ext.MessageBox.YESNO,
						fn : function(a) {
							if (a == "yes") {
								Ext.getCmp('GroupMenu').body.mask(
										'Published Menu', 'x-mask-loading');
								Ext.Ajax.request({
									url : res_url,
									params : {
										task : "publish",
										dataList : Ext.encode(data)
									},
									success : function(response) {
										Ext.getCmp('GroupMenu').body.unmask();
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
        bbar: [
            {
                text:'Edit Menu',
                iconCls:'form',
                tooltip:'Edit Selected Menu',
                handler:function(){
                    node = treeMenu.getSelectionModel().getSelectedNode();
                    if (node){
                        loadDetail(node); 
                    }
                }
            },'-',{
                text:'Reorder Menu',
                iconCls:'sort',
                tooltip:'Reorder Selected Menu',
                handler: function() {
                    node = treeMenu.getSelectionModel().getSelectedNode();                    
                    if (!node)
                        return false;                    
                     parentNode = (node.getDepth()>1)?node.parentNode:node;
                     if (!parentNode)
                         return false;
                     winSort.setTitle('Items Of ' + parentNode.text);
                     winSort.setIconClass(parentNode.attributes.iconCls);
                     dsSort.baseParams.parent_id = parentNode.id.split(".")[1];
                     dsSort.baseParams.update =0;
                     dsSort.load({params:{start:0,limit:10000}});
                     winSort.show(this.id);
                }

            }/**,'-',{
                text:'Move Down',
                iconCls:'arrow-down',
                handler: function() {
                    function clearS(node){
                        treeMenu.getSelectionModel().clearSelections();
                        //node.select();
                    }
                    node = treeMenu.getSelectionModel().getSelectedNode();
                    if (!node)
                        return false;                    
                    if (node.getDepth() >1)
                        if (!node.isLast()){
                                prevNode = node;
                                x = node.nextSibling;
                                parentNode = node.parentNode;                                
                                parentNode.insertBefore(prevNode,x);
                                clearS.defer(50,this,[node]);
                            }

                }

            } **/
        ],
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

		},
                movenode:function(tree,node,oldParent,newParent){
                    //parent = node.parentNode;
                    id_parent = newParent.id.split(".")[1];
                    id_node = node.id.split(".")[1];
                    node.select();
                    if (oldParent.id != newParent.id)
                    Ext.Ajax.request({
                        url: res_url,
                        params:{
                            task:'doDrop',
                            node:id_node,
                            parent: id_parent
                        },
                        success:function(response){
                            res = Ext.decode(response.responseText);
                            if (res.success)
                                Ext.example.msg('Drop Menu', 'Menu has been saved');
                        }
                    })
                },
                insert:function(tree,parent,node,refnode){
                    //console.log(node.text);
                    //console.log(refnode.text);
                }
	}
});

function loadDetail(node) {
	if (node.getDepth() > 1) {
                Ext.getCmp('GroupMenu').disable();
                winMenu.show(node.id); 
		detailMenu = Ext.getCmp('detailMenu');
		textMenu = (node.isLeaf()) ? " Menu" : " SubMenu";
		disableHandler(!node.isLeaf());
		winMenu.setTitle('Detail ' + node.text + textMenu);
		winMenu.setIconClass(node.attributes.iconCls);
		detailMenu.getBottomToolbar().get(2).setText('Reset');
		tmp_id = "";
		tmp_id = node.id;
		tmp_id = tmp_id.split(".");
		Ext.getCmp('detailMenu').body.mask('Loading Detail', 'x-mask-loading');
		Ext.getCmp('detailMenu').getForm().load({
					url : res_url,
					params : {
						task : 'getDetail',
						menu_id : tmp_id[1],
						isMenu : node.isLeaf()
					},
					success : function(a, b) {
						Ext.getCmp('detailMenu').body.unmask();
					},
					failure : function() {
						Ext.getCmp('detailMenu').body.unmask();
					}
				});
	}
}
// selection model
treeMenu.getSelectionModel().on("selectionchange", function(s, node) {
			if (node){
                            dsEvent.load({
                                params : {
                                        start : 0,
                                        limit : 500,
                                        menu_id : node.id.split(".")[1]
                                }});
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
			idProperty : 'id',
			root : 'data'
		}, [{
					name : 'id'
				}, {
					name : 'menu_id'
				}, {
					name : 'event_name',
					allowBlank : false
				}]);

var writerEvent = new Ext.data.JsonWriter({
			encode : true,
			writeAllFields : false
		});

var dsEvent = new Ext.data.Store({
			proxy : proxyEvent,
			reader : readerEvent,
			writer : writerEvent,
			autoSave : false,
			sortInfo : {
				field : 'event_name',
				direction : 'ASC'
			},
			remoteSort : false,
			listeners : {
				write : function(store, action, result, res, rs) {
					// App.setAlert(res.success, res.message); // <-- show
					// user-feedback for all write actions 
					if (!this.autoSave)
						Ext.getCmp('eventPanel').body.unmask();
					Ext.example.msg('Save', res.raw.message.note);
				},
				beforewrite : function() {
					if (!this.autoSave)
						Ext.getCmp('eventPanel').body.mask('Saving Data',
								'x-mask-loading');
				},
				exception : function(proxy, type, action, options, res, arg) {
					if (!this.autoSave)
						Ext.getCmp('eventPanel').body.unmask();
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

var cmEvent = new Ext.grid.ColumnModel({
			defaults : {
				sortable : true
			},
			columns : [{
						dataIndex : 'id',
						hidden : true,
						hideable : false,
						menuDisabled : true
					}, {
						dataIndex : 'menu_id',
						hidden : true,
						hideable : false,
						menuDisabled : true
					}, {
						dataIndex : 'event_name',
						header : 'Event Name',
						sortable : true,
						editor : {
							xtype : 'textfield',
							vtype:'alphanum',
							allowBlank : false
						}
					}

			]
		});

var eventPanel = new Ext.grid.EditorGridPanel({
	id : 'eventPanel',
	region : 'east',
	border : true,
	width : 200,
	collapsible : true,
	split : false,
	cmargins : '3 3 3 3',
	margins : '3 3 3 3',
	ds : dsEvent,
	cm : cmEvent,
	viewConfig : {
		forceFit : true
	},
	clicksToEdit : 1,
	stripeRows : true,
	enableColLock : false,
	loadMask : true,
	tbar : [
			'<img class="x-panel-inline-icon event-menu" src="extjs/resources/images/default/s.gif"/>',
			'Event Menu Detail', '->', '-', {
				iconCls : 'add-data',
				tooltip : 'Add Event Menu',
				handler : function() {
					node = treeMenu.getSelectionModel().getSelectedNode();
					if (node) {
						tmp = "";
						tmp = node.id;
						tmp = tmp.split(".");
						var u = new dsEvent.recordType({
									menu_id : tmp[1],
									event_name : ''
								});
						Ext.getCmp('eventPanel').stopEditing();
						dsEvent.insert(0, u);
						Ext.getCmp('eventPanel').startEditing(0, 2);
					} else {
						Ext.MessageBox.show({
									title : 'Alert',
									msg : 'Please Select Menu Item First',
									buttons : Ext.MessageBox.OK,
									icon : Ext.MessageBox.WARNING,
									animEl : this.id
								});
					}

				}
			}, {
				iconCls : 'row-delete',
				tooltip : 'Remove Selected Event',
				handler : function() {
					var index = Ext.getCmp('eventPanel').getSelectionModel().getSelectedCell();
					if (!index) {
						return false;
					}
					var rec = Ext.getCmp('eventPanel').store.getAt(index[0]);
					Ext.getCmp('eventPanel').store.remove(rec);
					if (dsEvent.getCount() > 0) {
						if (index[0] > 0)
							Ext.getCmp('eventPanel').getSelectionModel()
									.select(index[0] - 1, index[1]);
						else
							Ext.getCmp('eventPanel').getSelectionModel()
									.select(index[0], index[1]);
					}
					if (!dsEvent.autoSave)
						dsEvent.save(); 


				}
			}],
	bbar : [{
				text : 'Save',
				iconCls : 'icon-save',
				id : 'saveevent',
				tooltip : 'Save Changed',
				handler : function() {
					dsEvent.save();
				}
			}, '-', {
				text : 'Auto Save',
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

			}, '-', {
				text : 'Reset',
				iconCls : 'drop',
				tooltip : 'Reload Event Manager',
				handler : function() {
					node = treeMenu.getSelectionModel().getSelectedNode();
					if (node)
						dsEvent.reload();
				}
			}]
});

var GroupMenu = {
	id : 'GroupMenu',
	border : true,
	layout : 'border',
	region : 'center',
	title : 'Menu List',
	iconCls : 'mymenu',
	items : [treeMenu, eventPanel],
	tools : [{
				id : 'plus',
				qtip : 'Expand Menu All',
				handler : function() {
					treeMenu.root.expand(true);
				}
			}, {
				id : 'minus',
				qtip : 'Collapse Menu All',
				handler : function() {
					treeMenu.root.collapse(true);
				}

			}
	// ,{
	// id:'refresh',
	// qtip:'Refresh Menu',
	// handler: function() {
	// treeMenu.getRootNode().reload();
	// Ext.getCmp('detailMenu').getForm().reset();
	// Ext.getCmp('detailMenu').setTitle('Detail Menu/Submenu');
	// Ext.getCmp('detailMenu').setIconClass('mymenu');
	// disableHandler(false);
	// }
	// }
	]

};

var detailMenu = {
	id : 'detailMenu',
	//region : 'south',
	//height : 205,
	//title : 'Detail Menu/Submenu',
	//iconCls : 'mymenu',
	//split : false,
	//collapsible : true,
	//cmargins : '3 0 0 0',
	//margins : '3 0 0 0',
        border:true,
	frame : true,
	xtype : 'form',
	labelAlign : 'left',
	labelWidth : 150,
	defaultType : 'textfield',
	defaults : {
		labelSeparator : ''
	},
	bodyStyle : 'padding:5px',
	items : [{
				xtype : 'hidden',
				name : 'menu_id'
			}, {
				xtype : 'hidden',
				name : 'parent_id'
			}, {
				xtype : 'hidden',
				name : 'isMenu'
			}, {
				fieldLabel : 'Title',
				name : 'menu_title',
				anchor : '95%',
				allowBlank : false
			}, {
				xtype : 'trigger',
				fieldLabel : 'User Interface File',
				anchor : '95%',
				name : 'handler',
                                mod_handler:'js',
				allowBlank : true,
				triggerConfig : {
					tag : "img",
					src : Ext.BLANK_IMAGE_URL,
					cls : "x-form-trigger js-file"
				},
				onTriggerClick : function() {
					showHandler(this);
				}
			}, {
				xtype : 'trigger',
				fieldLabel : 'Controller File',
				anchor : '95%',
				name : 'ajax',
                                mod_handler:'php',
				allowBlank : true,
				triggerConfig : {
					tag : "img",
					src : Ext.BLANK_IMAGE_URL,
					cls : "x-form-trigger php-file " + this.triggerClass
				},
				onTriggerClick : function() {
					showHandler(this);
				}
			},{
				xtype : 'trigger',
				fieldLabel : 'Report File',
				anchor : '95%',
				name : 'report',
                                mod_handler:'pdf',
				allowBlank : true,
				triggerConfig : {
					tag : "img",
					src : Ext.BLANK_IMAGE_URL,
					cls : "x-form-trigger report-pdf " + this.triggerClass
				},
				onTriggerClick : function() {
					showHandler(this);
				}
			}, {
				xtype : 'iconcombo',
				hiddenName : 'iconCls',
				fieldLabel : 'Select Icon Cls',
				triggerAction : 'all',
				editable : true,
				store : iconStore,
				displayField : 'title',
				valueField : 'clsname',
				iconClsField : 'clsname',
				mode : 'local',
				allowBlank : true,
				width : 200

			}, {
				xtype : 'checkbox',
				id : 'published',
				//fieldLabel:'Published',
				checked : true,
				name : 'published',
				boxLabel : 'Published'
			}],
	bbar : [{
		text : 'Save',
		iconCls : 'icon-save',
		tooltip : 'Save Changed',
		handler : function() {
			detailMenu = Ext.getCmp('detailMenu');
			a = (detailMenu.getForm().getValues().menu_id == "") ? false : true;
			b = (detailMenu.getForm().getValues().parent_id == "")
					? false
					: true;
			id_valid = a || b;
			if (detailMenu.getForm().isValid() && id_valid) {
				detailMenu.body.mask('Save Data', 'x-mask-loading');
				detailMenu.getForm().submit({
					url : res_url,
					params : {
						task : 'save'
					},
					success : function(a, b) {
						detailMenu.body.unmask();
						Ext.getCmp('GroupMenu').enable();
						if (b.result.new_menu) {
							detailMenu.getForm().reset();
							nodeSelected
									.appendChild(new Ext.tree.TreeNode(b.result.config));
							nodeSelected.expand();
							lastnode = nodeSelected.lastChild;
							lastnode.select();
						} else {
							nodeSelected = treeMenu.getSelectionModel()
									.getSelectedNode();
							nodeSelected.ui.node.ui.iconNode.className = 'x-tree-node-icon '
									+ b.result.iconCls;
							nodeSelected.setText(b.result.menu_title);
							nodeSelected.ui.checkbox.checked = b.result.published;
						}
                                                winMenu.hide();
						Ext.example.msg('Save Data',
								'Menu has been successfully saved');
					},
					failure : function(a, b) {
						Ext.getCmp('GroupMenu').enable();
						Ext.MessageBox.show({
									title : 'Error On Saving Data',
									msg : b.result.msg,
									buttons : Ext.MessageBox.OK,
									icon : Ext.MessageBox.ERROR
								});
					}
				});
			} else
				Ext.example.msg('Failed to save',
						'Please check your data input');
		}
	}, '-', {
		text : 'Reset',
		iconCls : 'drop',
		tooltip : 'Reload Detail Menu',
		handler : function() {
			if (this.text == "Cancel") {
				Ext.getCmp('GroupMenu').enable();
				Ext.getCmp('detailMenu').getForm().reset();
				Ext.getCmp('detailMenu').setTitle('Detail Menu/Submenu');
				Ext.getCmp('detailMenu').setIconClass('mymenu');
				this.setText('Reset');
				disableHandler(false);
                                winMenu.hide();
			} else{
                            node = treeMenu.getSelectionModel().getSelectedNode();
                            if (node)
                                loadDetail(node);
                        }

		}
	}]
};

var winMenu = new Ext.Window({
                    layout : 'fit',
                    id : 'winMenu',
                    width : 500,
                    height : 230,
                    closeAction : 'hide',
                    iconCls : 'form',
                    title : '',
                    modal : false,
                    plain : true,
                    border : false,
                    items : [detailMenu],
                    listeners:{
                        hide:function(){
                            Ext.getCmp('GroupMenu').enable();
                        }
                    }
});

var itemWest = {
	region : 'center',
	border : false,
	layout : 'border',
	items : [GroupMenu]

};

/** Using CRUD TO SAVE TO DATABASE * */

var proxyIcon = new Ext.data.HttpProxy({
			api : {
				read : res_url + '&task=gridIcon&action=doRead',
				create : res_url + '&task=gridIcon&action=doCreate',
				update : res_url + '&task=gridIcon&action=doUpdate',
				destroy : res_url + '&task=gridIcon&action=doDestroy'
			}
		});

var readerIcon = new Ext.data.JsonReader({
			totalProperty : 'total',
			successProperty : 'success',
			idProperty : 'id',
			root : 'data'
		}, [{
					name : 'id'
				}, {
					name : 'title',
					allowBlank : false
				}, {
					name : 'clsname',
					allowBlank : false
				}, {
					name : 'icon',
					allowBlank : false
				}]);

var writerIcon = new Ext.data.JsonWriter();

var dsIcon = new Ext.data.Store({
			proxy : proxyIcon,
			reader : readerIcon,
			writer : writerIcon,
			autoSave : false,
			sortInfo : {
				field : 'title',
				direction : 'ASC'
			},
			remoteSort : true,
			listeners : {
				write : function(store, action, result, res, rs) {
					// App.setAlert(res.success, res.raw.message); // <-- show
					// user-feedback for all write actions
					if (!this.autoSave)
						Ext.getCmp('iconPanel').body.unmask();
					Ext.example.msg('Save', res.raw.message.note);
				},
				beforewrite : function() {
					if (!this.autoSave)
						Ext.getCmp('iconPanel').body.mask('Saving Data',
								'x-mask-loading');
				},
				exception : function(proxy, type, action, options, res, arg) {
					if (!this.autoSave)
						Ext.getCmp('iconPanel').body.unmask();
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
			dsIcon.load({
						params : {
							start : 0,
							limit : parseInt(limit_combo.getValue())
						}
					});
		});

var filterIcon = new Ext.ux.grid.GridFilters({
			filters : [{
						type : 'string',
						dataIndex : 'title'
					}, {
						type : 'string',
						dataIndex : 'clsname'
					}]
		});

var chooser;

var cmIcon = new Ext.grid.ColumnModel({
	defaults : {
		sortable : true
	},
	columns : [{
				dataIndex : 'id',
				hidden : true,
				hideable : false,
				menuDisabled : true
			}, {
				dataIndex : 'icon',
				header : 'Icon',
				width : 35,
				renderer : function(value, data, record, rowIndex) {
					return String
							.format(
									'<div id="ico-{1}" style="cursor:pointer;" title="Double Click to Change Icon"><img class="x-panel-inline-icon " src="images/icon/{0}" /></div>',
									value, rowIndex);
				}
			}, {
				dataIndex : 'title',
				header : 'Title',
				width : 100,
				editor : {
					xtype : 'textfield',
					allowBlank : false
				}
			}, {
				dataIndex : 'clsname',
				header : 'Cls Name',
				width : 100,
				editor : {
					xtype : 'textfield',
					allowBlank : false
				}
			}

	]
});

var iconPanel = {
	id : 'iconPanel',
	region : 'east',
	split : true,
	border : true,
	collapsible : true,
	cmargins : '0 0 0 3',
	title : 'Icon Cls Manager',
	iconCls : 'image-add',
	width : 258,
	xtype : 'editorgrid',
	clicksToEdit : 1,
	ds : dsIcon,
	cm : cmIcon,
	stripeRows : true,
	enableColLock : false,
	loadMask : true,
	plugins : [filterIcon],

	tbar : [{
				// text:'Add',
				iconCls : 'add-data',
				tooltip : 'Add New Icon',
				handler : function() {
					var u = new dsIcon.recordType({
								title : '',
								clsname : '',
								icon : 'help.png'
							});
					Ext.getCmp('iconPanel').stopEditing();
					dsIcon.insert(0, u);
					Ext.getCmp('iconPanel').startEditing(0, 2);

				}
			}, '-', {
				// text:'Delete',
				iconCls : 'row-delete',
				tooltip : 'Remove Icon',
				handler : function() {
					var index = Ext.getCmp('iconPanel').getSelectionModel()
							.getSelectedCell();
					if (!index) {
						return false;
					}
					var rec = Ext.getCmp('iconPanel').store.getAt(index[0]);
					Ext.getCmp('iconPanel').store.remove(rec);
					if (dsIcon.getCount() > 0) {
						if (index[0] > 0)
							Ext.getCmp('iconPanel').getSelectionModel().select(
									index[0] - 1, index[1]);
						else
							Ext.getCmp('iconPanel').getSelectionModel().select(
									index[0], index[1]);
					}
					if (!dsIcon.autoSave)
						dsIcon.save(); 

				}
			}, '-', {
				// text:'Save',
				iconCls : 'icon-save',
				id : 'saveicon',
				tooltip : 'Save Changed Icon',
				handler : function() {
					dsIcon.save();
				}
			}, '-', {
				// text:'Auto Save',
				iconCls : 'autosave',
				tooltip : 'Quick Save when finish Editing',
				enableToggle : true,
				pressed : false,
				toggleHandler : function(btn, press) {
					if (press)
						Ext.getCmp('saveicon').disable();
					else
						Ext.getCmp('saveicon').enable();
					dsIcon.autoSave = press;
					if (press)
						dsIcon.save();
				}

			}, '->', {
				iconCls : 'css-refresh',
				tooltip : 'Refresh Change Icon On Application',
				handler : function() {
					reloadIcon = true;
					iconStore.reload();
				}
			}, '-', 'Limit ', limit_combo],
	bbar : new Ext.PagingToolbar({
				id : 'pagingBar',
				store : dsIcon,
				pageSize : parseInt(limit_combo.getValue()),
				plugins : filterIcon,
				displayInfo : false,
				// displayMsg: 'Icon {0} - {1} of {2}',
				// emptyMsg: "No Data to display",
				items : ['-', {
							tooltip : {
								title : 'Clear Filter',
								text : 'Clear Searching Filter'
							},
							iconCls : 'drop',
							handler : function() {
								filterIcon.clearFilters();
							}
						}]
			}),
	listeners : {
		render : function() {
			filterIcon.clearFilters();
			for (i = 0; i < Ext.getCmp('iconPanel').getColumnModel()
					.getColumnCount(); i++)
				if (Ext.getCmp('iconPanel').getColumnModel().isMenuDisabled(i) == false)
					Ext.getCmp('iconPanel').getColumnModel()
							.setHidden(i, false);
		},
		celldblclick : function(grid, rowIndex, colIndex) {
			if (colIndex == 1) {
				if (!chooser) {
					chooser = new ImageChooser({
								url : res_url,
								baseParams : {
									task : 'iconList'
								},
								width : 515,
								height : 350
							});
				}
				var thisTrigger = this;
				chooser.show('ico-' + rowIndex, function(data) {
							rec = dsIcon.getAt(rowIndex);
							rec.beginEdit();
							rec.set('icon', data.name);
							rec.endEdit();
						});
			}
		}
	}
};

dsIcon.load({
			params : {
				start : 0,
				limit : parseInt(limit_combo.getValue())
			}
		});

var centerPanel = {
	border : false,
	layout : 'border',
	items : [itemWest, iconPanel]
};

var handler_select = 'js';

/** listView File * */
var storeHanlder = new Ext.data.JsonStore({
			url : res_url,
			root : 'filelist',
			baseParams : {
				task : 'handlerList'
			},
			fields : ['name', 'url', {
						name : 'size',
						type : 'float'
					}, {
						name : 'lastmod',
						type : 'date',
						dateFormat : 'timestamp'
					}]
		});
// storeHanlder.load();

var listView = new Ext.ListView({
	store : storeHanlder,
	singleSelect : true,
	emptyText : 'No File To display',
	reserveScrollOffset : true,
	loadingText : 'loading filelist',
	columns : [{
		header : 'File',
		width : .5,
		dataIndex : 'name',
		tpl : '<img class="x-panel-inline-icon js-file" src="images/s.gif" />{name}'
	}, {
		header : 'Last Modified',
		width : .35,
		dataIndex : 'lastmod',
		tpl : '{lastmod:date("m-d h:i a")}'
	}, {
		header : 'Size',
		dataIndex : 'size',
		tpl : '{size:fileSize}',
		align : 'right'
	}]
});

var winHanlder = new Ext.Window({
			id : 'winHanlder',
			title : 'Select Handler File',
			iconCls : 'js-file',
			autoScroll : true,
			closeAction : 'hide',
			modal : true,
			layout : 'fit',
			bodyStyle : 'background-color:white;',
			width : 425,
			height : 300,
			items : [listView],
			listeners : {
				show : function() {
                                        if (handler_select == 'js')
                                            task = 'handlerList';
                                        if (handler_select == 'php')
                                            task = 'ajaxList';
                                        if (handler_select == 'pdf')
                                            task = 'reportList';
					storeHanlder.baseParams = {
						task : task
					};
					storeHanlder.load();
				}
			},
			buttons : [{
				text : 'Select File',
				iconCls : 'js-file',
				tooltip : 'Select File Handler',
				handler : function() {
					dataku = listView.getSelectedRecords();
					if (dataku.length) {
						if (handler_select == 'js')
							Ext.getCmp('detailMenu').findByType('trigger')[0]
									.setValue(dataku[0].data.name);
						if (handler_select == 'php')
							Ext.getCmp('detailMenu').findByType('trigger')[1]
									.setValue(dataku[0].data.name);
						if (handler_select == 'pdf')
							Ext.getCmp('detailMenu').findByType('trigger')[2]
									.setValue(dataku[0].data.name);
						winHanlder.hide();
						Ext.getCmp('detailMenu').body.highlight();
					}
				}
			}, {
				text : 'Close',
				iconCls : 'row-delete',
				handler : function() {
					winHanlder.hide();
				}
			}]
		});

function showHandler(btn) {
	handler_select = btn.mod_handler; 
	column_select = (handler_select == 'js') ? 'js-file' : 'php-file';
        if (handler_select =='pdf')
            column_select = 'report-pdf'; 
	winHanlder.setIconClass(column_select);
	winHanlder.setTitle('Select ' + btn.fieldLabel);
	//winHanlder.show(btn.id);
        winHanlder.show();
}
/** end of List File * */

var dsSort = new Ext.data.JsonStore({
			url : res_url,
			totalProperty : 'total',
			baseParams : {
				task : 'sortMenu',
                                update:0,
                                parent_id:0
			},
			root : 'data',
                        fields:[
                            {name:'id'},{name:'iconcls'},{name:'title'},{name:'sort_id'}
                        ],
			sortInfo : {
				field : 'sort_id',
				direction : 'ASC'
			},
			remoteSort : true

});
var cmSort = new Ext.grid.ColumnModel({
	defaults : {
		sortable : false
	},
	columns : [
        {
            dataIndex : 'id',
            hidden : true,
            hideable : false,
            menuDisabled : true
	},{
            dataIndex:'title',
            header:'Menu Title',
            menuDisabled:true,
            renderer:function(val,cell,rec){
                return String.format('<img class="x-panel-inline-icon " src="images/icon/{0}" /> {1}',rec.data.iconcls,val);
            }
        },{
            dataIndex : 'sort_id',
            hidden : true,
            hideable : false,
            menuDisabled : true

        }
        ]
});

var gridSort = new Ext.grid.GridPanel({
	ds : dsSort,
	cm : cmSort,
	sm : new Ext.grid.RowSelectionModel({
				singleSelect : true
			}),
	stripeRows : true,
        viewConfig:{
            forceFit:true
        },
	enableColLock : false,
	loadMask : true,
	bbar :[
            {
                text:'Move Up',
                iconCls:'arrow-up',
                handler:function(){
                    sel = gridSort.getSelectionModel().getSelected();
                    if (!sel)
                        return false;
                    index = gridSort.store.indexOf(sel);
                    if (index < 1)
                        return false;
                    rc = gridSort.store.getAt(index-1).copy();                    
                    sort_id = rc.data.sort_id;
                    rc.data.sort_id = sel.data.sort_id;
                    sel.data.sort_id = sort_id;
                    gridSort.store.getAt(index-1).data = sel.data;
                    sel.data = rc.data;
                    gridSort.getView().refresh();
                    gridSort.getSelectionModel().selectPrevious();
                    
                }
            },'-',{
                text:'Move Down',
                iconCls:'arrow-down',
                handler:function(){
                    sel = gridSort.getSelectionModel().getSelected();
                    if (!sel)
                        return false;
                    index = gridSort.store.indexOf(sel);
                    if (index >= (gridSort.store.getCount()-1))
                        return false;
                    rc = gridSort.store.getAt(index+1).copy();
                    sort_id = rc.data.sort_id;
                    rc.data.sort_id = sel.data.sort_id;
                    sel.data.sort_id = sort_id;
                    gridSort.store.getAt(index+1).data = sel.data;
                    sel.data = rc.data;
                    gridSort.getView().refresh();
                    gridSort.getSelectionModel().selectNext();

                }

            },'-',{
                text:'Save',
                iconCls:'icon-save',
                handler:function(){
                    data = [];
                    dsSort.each(function(r,i){
                        data.push({
                            id:r.data.id,
                            sort_id:r.data.sort_id
                        });
                    });
                    if (data.length){
                        winSort.body.mask('Save Menu', 'x-mask-loading');
                        Ext.Ajax.request({
                            url:res_url,
                            params: {
                                task:'sortMenu',
                                parent_id: dsSort.baseParams.parent_id,
                                update:1,
                                data: Ext.encode(data)
                            },
                            success:function(response){
                                winSort.body.unmask();
                                res = Ext.decode(response.responseText);
                                if (res.success){                                    
                                    winSort.hide();
                                    Ext.example.msg('Save', 'Reording Menu successfully saved');
                                    treeMenu.getRootNode().reload();
                                }else{
                                    Ext.MessageBox.alert('Error','Failed to Save');
                                }
                            }
                        })
                    }
                }
            }
        ]
});

var winSort = new Ext.Window({
	layout : 'fit',
	id : 'winSort',
	width : 300,
	height : 400,
	closeAction : 'hide',
	iconCls : 'app-grid',
	title : '',
	modal : true,
	plain : true,
	border : false,
	items : [gridSort]
});

var main_content = {
	id : 'main_content',
	layout : 'fit',
	title : 'Menu Event Manager',
	iconCls : 'app',
	border : true,
	bodyStyle : 'padding:5px',
	frame : false,
	items : [centerPanel],
	listeners : {
		render : function() {
			//treePanel.disable();
		},
		destroy : function() {
			//treePanel.enable();
			treePanel.getRootNode().reload();
			pageAdmin = "";
			winHanlder = Ext.getCmp('winHanlder');
			if (winHanlder)
				winHanlder.destroy();
			winchooser = Ext.getCmp('img-chooser-dlg');
			if (winchooser)
				winchooser.destroy();
                        winMenu = Ext.getCmp('winMenu');
                        if (winMenu)
                            winMenu.destroy(); 
                        winMenu = Ext.getCmp('winSort');
                        if (winMenu)
                            winMenu.destroy();
		}
	}
};