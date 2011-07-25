valid_script = true;  
ajax_url = 'ajax.handler.php?id=' + page;  

var grid_grouping = new Ext.ux.DynamicGroupingGrid({
	autoLoadStore:true,
	border:false,
    remoteSort: true,
    sortInfo: { field: 'groupby', direction: 'ASC' },
    storeUrl:ajax_url,
    baseParams:{action:'read'},	
    groupField:'groupby', // select the field for grouping
    groupTpl:'{[values.rs[0].data.groupby]} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
});

var main_content = {
		  id : id_panel,  
		  title:n.text,  
		  iconCls:n.attributes.iconCls,  
		  items : [grid_grouping],
		  listeners:{
		    destroy:function(){
		    }
		  }
}; 