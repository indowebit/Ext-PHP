/** untuk menandakan bahwa script ini valid dan akan di eksekusi * */
valid_script = true;
ajax_url = 'ajax.handler.php?id=' + page;
Ext.chart.Chart.CHART_URL = 'extjs/resources/charts.swf';

var dsDate = new Ext.data.JsonStore({
	url: ajax_url,
	autoLoad:true,
	baseParams: {
		task: 'getChart',
		start:0,
		limit:100000
	},
	root: 'data',
	fields: [
		{
			name:'tahun'
		},
		{
			name:'page_view',
			type:'int'
		},{
                        name:'page_visit',
                        type:'int'
                }
	
	],
	  sortInfo: {field: 'tahun', direction: 'ASC'},
	  remoteSort: true,
	  listeners: {
	  	load: function(){
	  		rDsData.refreshData();
	  	}
	  }
 }); 
 
 var rDsData = new Ext.data.JsonStore({
 	fields: ['tahun','page_view','page_visit'],
 	data : [],
 	refreshData: function(){
 		var data =[];
 		dsDate.each(function(r,i){
 			data.push({
 				tahun : r.data.tahun,
 				page_view : Math.floor(r.data.page_view),
                                page_visit : Math.floor(r.data.page_visit)
 			});
 		});
 		rDsData.loadData(data);
 	}
 });

the_first = {
	title:'Sample Statistik 1',
	iconCls:'stat-line2',
	layout:'fit',
	border:false,
	bbar: [{
		text:'Refresh',
		iconCls:'drop',
		qtip:'Refresh Data',
		handler:function(){
			dsDate.reload();
		}
	}],
    items: {
            xtype: 'columnchart',
            store: rDsData,
            xField: 'tahun',
            yAxis: new Ext.chart.NumericAxis({
                displayName: 'Visits',
                labelRenderer : Ext.util.Format.numberRenderer('0,0')
            }),
            tipRenderer : function(chart, record, index, series){
                if(series.yField == 'visits'){
                    return Ext.util.Format.number(record.data.page_visit, '0,0') + ' visits in ' + record.data.tahun;
                }else{
                    return Ext.util.Format.number(record.data.page_view, '0,0') + ' page views in ' + record.data.tahun;
                }
            },
            chartStyle: {
                padding: 10,
                animationEnabled: true,
                font: {
                    name: 'Tahoma',
                    color: 0x444444,
                    size: 11
                },
                dataTip: {
                    padding: 5,
                    border: {
                        color: 0x99bbe8,
                        size:1
                    },
                    background: {
                        color: 0xDAE7F6,
                        alpha: .9
                    },
                    font: {
                        name: 'Tahoma',
                        color: 0x15428B,
                        size: 10,
                        bold: true
                    }
                },
                xAxis: {
                    color: 0x69aBc8,
                    majorTicks: {color: 0x69aBc8, length: 4},
                    minorTicks: {color: 0x69aBc8, length: 2},
                    majorGridLines: {size: 1, color: 0xeeeeee}
                },
                yAxis: {
                    color: 0x69aBc8,
                    majorTicks: {color: 0x69aBc8, length: 4},
                    minorTicks: {color: 0x69aBc8, length: 2},
                    majorGridLines: {size: 1, color: 0xdfe8f6}
                }
            },
            series: [{
                type: 'column',
                displayName: 'Page Views',
                yField: 'page_view',
                style: {
                    //image:'bar.gif',
                    //mode: 'stretch',
                    color:0x99BBE8
                }
            },{
                type:'line',
                displayName: 'Visits',
                yField: 'page_visit',
                style: {
                    color: 0x15428B
                }
            }]
        }
};

the_content = {
	border:true,
	layout:'accordion',
	activeItem:0,
	items:[the_first]
};

var main_content = {
	id : id_panel,
        title:n.text,
        iconCls:n.attributes.iconCls,
        bodyStyle:'padding:5px;',
	items:[the_content]
};
