

// Chart page JavaScript Document

function pie_chart(chart_type,div_id,data_arr,data_sign,title,precision){
	if(typeof(precision)=='undefined'){ var precision='';};
	if(typeof(data_sign)=='undefined'){ var data_sign='';};
	if(typeof(title)=='undefined'){ var title='';};
		//alert(data_arr)
		var chartData = $.parseJSON(data_arr);

		var chart = AmCharts.makeChart(div_id,{
		  "type"    : chart_type,
		  "titleField"  : "kee",
		  "valueField"  : "val",
		  "pulledField" : "pullOut",
		  "labelText": data_sign+"[[value]]",
		  "precision": precision,
		  "dataProvider"  : chartData,
		  "titles": [{"text": title}],
		  "legend": {
				"enabled": true,
				"align": "center",
				"markerType": "square",
				"position": "right",
				"valueText": data_sign+"[[value]]"
		  },
		  "responsive": {
			"enabled": true
			}
		});
}

function multi_bar_chart(chart_type,div_id,data_arr,data_graph_arr,labelRotation,title,barRotation, side_title,show_unit){
	var axisAlpha=1;
	var labelsEnabled=true;
	if(typeof(labelRotation)=='undefined' || labelRotation==''){ var labelRotation=0};
	if(typeof(show_unit)=='undefined'){ var show_unit='';};
	if(typeof(title)=='undefined'){ var title='';};
	if(typeof(barRotation)=='undefined' || labelRotation==''){ var labelRotation=false};
	if(barRotation==true){axisAlpha=0;labelsEnabled=false;}
	var chartData = $.parseJSON(data_arr);
	var chartData_Graph = $.parseJSON(data_graph_arr);

	var chart = AmCharts.makeChart(div_id, {
		"type": chart_type,
		"categoryField": "category",
		"startDuration": 1,
		"theme": "light",
		"rotate": barRotation,
		"categoryAxis": {
			"gridPosition": "start",
			"labelRotation": labelRotation,
			"axisAlpha": axisAlpha,
			"labelsEnabled": labelsEnabled
		},
		"trendLines": [],
		"graphs": chartData_Graph,
		"guides": [],
		"valueAxes": [{
			"unit": show_unit,
			"unitPosition": "left",
			"axisAlpha": axisAlpha,
			"labelsEnabled": labelsEnabled,
			"integersOnly": true,
			"title": side_title
		}],
		"allLabels": [],
		"balloon": {},
		"legend": {"useGraphSettings": true},
		"titles": [{"text": title}],
		"dataProvider": chartData,

	} );
}

function bar_chart(chart_type,div_id,data_arr,labelRotation,title,barRotation){
	var axisAlpha=1;
	var labelsEnabled=true;
	var value_title="Number of Replies";
	if(typeof(labelRotation)=='undefined' || labelRotation==''){ var labelRotation=0};
	if(typeof(show_unit)=='undefined'){ var show_unit='';};
	if(typeof(title)=='undefined'){ var title='';};
	if(typeof(barRotation)=='undefined' || labelRotation==''){ var labelRotation=false};
	if(barRotation==true){axisAlpha=0;labelsEnabled=false;value_title="";}

	//alert(data_arr)
	var chartData = $.parseJSON(data_arr);

	var chart = AmCharts.makeChart(div_id, {
	"type": chart_type,
	"theme": "light",
	"dataProvider": chartData,
	"titles": [{"text": title}],
	"rotate": barRotation,
	"valueAxes": [ {
		"gridColor": "#FFFFFF",
		"gridAlpha": 0.2,
		"dashLength": 0,
		"unit": show_unit,
		"axisAlpha": axisAlpha,
		"labelsEnabled": labelsEnabled,
		"integersOnly": true,
		"unitPosition": "left",
		"title": value_title
	} ],
	"gridAboveGraphs": true,
	"startDuration": 1,
	"graphs": [ {
		"balloonText": "[[category]]: <b>[[value]]</b>",
		"fillAlphas": 0.8,
		"lineAlpha": 0.2,
		"type": "column",
		"fillColors": "#FF8000",
		"valueField": "val"
	} ],
	"chartCursor": {
		"categoryBalloonEnabled": false,
		"cursorAlpha": 0,
		"zoomable": false
	},
	"categoryField": "kee",
	"categoryAxis": {
	"autoWrap": true,
	"gridPosition": "start",
	"labelOffset": 0,
	"labelRotation": labelRotation,
	"axisAlpha": axisAlpha,
	"labelsEnabled": labelsEnabled,
	"gridAlpha": 0,
	"tickPosition": "start",
	"tickLength": 20
	}
	} );
}

//Open Pop up For iPortal
function iportal_load_app_reqs(){
	if(typeof(erp_api_patient_portal)=='undefined' || erp_api_patient_portal!='1'){return;}
	var oPUF = (typeof(top.oPUF)!="undefined") ? top.oPUF : top.fmain.oPUF;
	var n="app_reqs";
	if(!oPUF[n] || !(oPUF[n].open) || (oPUF[n].closed == true)){
		oPUF[n] = window.open("get_app_req.php",n,'location=1,status=1,resizable=1,left=10,top=1,scrollbars=1,width=1000,height=500');
		oPUF[n].focus();
	}else{
		oPUF[n].focus();
	}
}
