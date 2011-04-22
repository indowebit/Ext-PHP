/** untuk menandakan bahwa script ini valid dan akan di eksekusi * */
valid_script = true;
res_url = 'ajax.admin.php?id=' + mypage;
/** =============================================================* */

var userForm = {
					xtype: 'form',
					region: 'center', 
					labelAlign:'left',
					id:'userForm',
					labelWidth: 70,
					autoHeight:true,
					border:false,
					bodyStyle:'padding: 30 0 0 0; background: url('+ Ext.BLANK_IMAGE_URL +');',
					defaultType:'textfield',
					defaults: {anchor: '97%',labelSeparator:''},
					items : [
							{
								fieldLabel: 'User Name',
								name: 'user_name',
								readOnly:true
							},
							{
								fieldLabel:'Real Name',
								name: 'real_name',
								allowBlank:false
							},{
								fieldLabel:'Password',
								name:'user_password',
								inputType:'password',
								allowBlank:false
							}
					],
			buttons: [
					{
						text:'Save',
						iconCls:'icon-save',
						scale:'medium',
						handler:function() {
							if (Ext.getCmp('userForm').getForm().isValid()) {
								Ext.getCmp('winProfile').body.mask('Save Data',
									'x-mask-loading');
								form = Ext.getCmp('userForm').getForm(); 
								form.submit({
										url:res_url,
										params: {
											task:'saveUser'
										},
										success:function() {
											Ext.getCmp('winProfile').body.unmask();
											Ext.example.msg('Save','Data has been changed'); 
										},
										failure:function(){
											Ext.getCmp('winProfile').body.unmask();
											Ext.MessageBox.alert('Alert','Error On Save User');
										}
								});								
							}
							
						}
					},{
						text:'Reload User',
						id:'load-user',
						scale:'medium',
						iconCls:'drop',
						handler:function() {
							Ext.getCmp('winProfile').body.mask('Loading Data',
								'x-mask-loading');
							form = Ext.getCmp('userForm').getForm(); 
							form.load({
									url:res_url,
									params: {
										task:'loadUser'
									},
									success:function() {
										Ext.getCmp('winProfile').body.unmask();
									},
									failure:function(){
										Ext.getCmp('winProfile').body.unmask();
										Ext.MessageBox.alert('Alert','Error On Loading User');
									}
							});

						}					
					}
			],
			listeners: {
				render: function() {
					btn_load = Ext.getCmp('load-user');
					btn_load.handler.call(btn_load); 
					
				}
			}
					
};
 
var userPic = {
   bodyStyle: 'padding:0px',
   xtype: 'box',
   region:'west',
   width: 130,
   autoEl: { tag: 'div',
			 html: '<img id="pic" src=images/user-info.png style="background:transparent;" />'
	}

};

	var winProfile = {
			layout:'border',
			id:'winProfile',
			width:400,
			height:190,
			border:true,
			iconCls:'chk-pwd',
			title:'User Profile',
			plain:true,
			frame:true,
			items:[userPic,userForm]
	}; 


var main_content = {
	id : 'main_content',
	layout : 'hbox',
	title : 'User Profile',
	iconCls : 'chk-pwd',
    layoutConfig: {
    	padding:'5',
    	pack:'center',
    	align:'middle'
	},
	
	items : [winProfile],
	listeners : {
		destroy : function() {
			pageAdmin = "";
		}
	}
};	