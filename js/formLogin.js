/**-------login form panel **-------------------**/ 
	function enterFunct(f, e, ff) {
			if (e.getKey() == e.ENTER){
				ff.focus();
			}
	}

var loginForm = {
					xtype: 'form',
					region: 'center', 
					bodyStyle:"padding: 5px;",
					labelAlign:'top',
					id:'frmLogin',
					labelWidth: 50,
					border:false,
					bodyStyle:'padding: 15 5 5 5; background: url('+ Ext.BLANK_IMAGE_URL +');',
					defaultType:'textfield',
					defaults: {anchor: '97%',labelSeparator:''},
					items : [
							{
								fieldLabel:'User Name',
								name: 'username',
								id:'username',
								allowBlank:false,
								listeners:{
										specialkey: function(o,e){enterFunct(o,e,Ext.getCmp('pwd'));}
								}
							},{
								fieldLabel:'Password',
								name:'pwd',
								id:'pwd',
								inputType:'password',
								allowBlank:false,
								enableKeyEvents : true,
								listeners:{
										specialkey: function(o, e){
											if (e.getKey() == e.ENTER){
												btn = Ext.getCmp('btn-login'); 
												btn.handler.call(btn.scope); 
											}
												
										}, 
										render:function(){
											var css = '.ux-auth-warning {background:url("images/icon/error.png") no-repeat center left; padding: 2px; padding-left:20px; font-weight:bold;}';
											Ext.util.CSS.createStyleSheet(css, this._cssId);
											this.capsWarningTooltip = new Ext.ToolTip({
												target: this.id,
												anchor: 'top',
												width: 305,
												html: '<div class="ux-auth-warning">Caps Lock is On</div>' +
													'<div>Having Caps Lock on may cause you to enter your password incorrectly.</div>' +
													'<div>You should press Caps Lock to turn it off before entering your password.</div>'
											});

												// disable to tooltip from showing on mouseover
											this.capsWarningTooltip.disable();

											// When the password field fires the blur event,
											// the tootip gets enabled automatically (possibly an ExtJS bug).
											// Disable the tooltip everytime it gets enabled
											// The tooltip is shown explicitly by calling show()
											// and enabling/disabling does not affect the show() function.
											this.capsWarningTooltip.on('enable', function() {
												this.disable();
											});

										},

										keypress: {
											fn: function(field, e) {
													var charCode = e.getCharCode();
													if((e.shiftKey && charCode >= 97 && charCode <= 122) ||
														(!e.shiftKey && charCode >= 65 && charCode <= 90)) {
														field.capsWarningTooltip.show();
													}
													else {
														if(field.capsWarningTooltip.hidden == false) {
															field.capsWarningTooltip.hide();
														}
													}
												
											},
											scope: this
										},

										blur: function(field) {
											if(this.capsWarningTooltip.hidden == false) {
												this.capsWarningTooltip.hide();
											}
										}

								}
							}
					]
};

var picLogin = {
   bodyStyle: 'padding:0px',
   xtype: 'box',
   region:'west',
   width: 130,
   autoEl: { tag: 'div',
			 html: '<img id="pic" src=images/gembok.png style="background:transparent;" />'
	}

};
