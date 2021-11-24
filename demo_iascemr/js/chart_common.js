// JavaScript Document

function pie_chart(chart_type,div_id,data_arr,data_sign){
	if(typeof(data_sign)=='undefined'){ var data_sign='$'; var precision=2;}else{var precision ='';}
		var chartData = $.parseJSON(data_arr);
		var chart = AmCharts.makeChart(div_id,{
		  "type"    : chart_type,
		  "titleField"  : "kee",
		  "valueField"  : "val",
		  "labelText": "[[title]] \n "+data_sign+"[[value]]",
		  "precision": 2,
		  "dataProvider"  : chartData,
		  "autoMargins": false,
		  "marginTop": 10,
		  "marginBottom": 10,
		  "marginLeft": 10,
		  "marginRight": 10,
		  "pullOutRadius": 8,
		  "groupedPulled": true,
		  "responsive": {
				"enabled": true
			},
			"export":{
				"enabled": true,
				menu : []
			},
			"pulledField":"val"
			
		});
		
}
function bar_chart(chart_type,div_id,data_arr,labelRotation,show_unit){
	if(typeof(labelRotation)=='undefined' || labelRotation==''){ var labelRotation=0};
	if(typeof(show_unit)=='undefined'){ var show_unit='$'};
	//alert(data_arr)
	var chartData = $.parseJSON(data_arr);
	 
	var chart = AmCharts.makeChart(div_id, {
	"type": chart_type,
	"theme": "light",
	"dataProvider": chartData,
	"valueAxes": [ {
		"gridColor": "#FFFFFF",
		"gridAlpha": 0.2,
		"dashLength": 0,
		"unit": show_unit,
		"unitPosition": "left"
	} ],
	"gridAboveGraphs": true,
	"startDuration": 1,
	"graphs": [ {
		"balloonText": "[[category]]: <b>[[value]]</b>",
		"fillAlphas": 0.8,
		"lineAlpha": 0.2,
		"type": "column",
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
		"gridAlpha": 0,
		"tickPosition": "start",
		"tickLength": 20
	},
	"export":{
		"enabled": true,
		menu : []
	}
	
	} );
}

function line_chart2(data_arr,data_name,data_average,divID,labelRotation,data_sign,ave_sub_title)
{
	if(typeof(data_sign)=='undefined'){ var data_sign=''; }
	if(typeof(ave_sub_title)=='undefined'){ var ave_sub_title=''; }
	
	if(typeof(labelRotation)=='undefined' || labelRotation==''){ var labelRotation=0};
		var chart = AmCharts.makeChart(divID, {
		"type": "serial",
		"theme": "light",
		"legend": {
			"equalWidths": false,
			"useGraphSettings": true,
			"valueAlign": "left",
			"valueWidth": 120
		},
		"marginRight": 10,
		"marginTop": 10,
		"autoMarginOffset": 20,
		"dataProvider": data_arr,
		"valueAxes": [{
			"logarithmic": true,
			"dashLength": 1,
			"unit": data_sign,
			"unitPosition": "left",
			"guides": [{
				"dashLength": 0,
				"lineColor": "#DC6A03",
				"inside": true,
				"label": data_name+" ("+data_sign+data_average+")",
				"lineAlpha": 1,
				"value": data_average
			}],
			"position": "left"
		}],
		"graphs": [{
			"bullet": "round",
			"id": "g1",
			"bulletBorderAlpha": 1,
			"bulletColor": "#FFFFFF",
			"bulletSize": 7,
			"lineThickness": 2,
			"title": "Average "+ave_sub_title,
			"type": "smoothedLine",
			"useLineColorForBulletBorder": true,
			"balloonText": "Average "+ave_sub_title+" "+data_sign+"[[value]]",
			"valueField": "val"
		}],
		"chartScrollbar": {},
		"chartCursor": {
			"valueLineEnabled": true,
			"valueLineBalloonEnabled": true,
			"valueLineAlpha": 0.5,
			"fullWidth": false,
			"cursorAlpha": 0.05
		},
		"dataDateFormat": "YYYY-MM-DD",
		"categoryField": "kee",
		"categoryAxis": {
			"parseDates": false,
			"labelRotation": labelRotation
		},
		"export": {
			"enabled": true,
			menu : []
		}
	});
}
function line_chart(chart_type,div_id,data_arr,data_graph_arr,labelRotation){
	if(typeof(labelRotation)=='undefined' || labelRotation==''){ var labelRotation=0};
	//alert(data_arr)
	var chartData = $.parseJSON(data_arr);
	var chartData_Graph = $.parseJSON(data_graph_arr);
	 
	var chart = AmCharts.makeChart(div_id, {
					"type": chart_type,
					"categoryField": "category",
					"startDuration": 1,
					"theme": "light",
					"categoryAxis": {
						"gridPosition": "start",
						"labelRotation": labelRotation
					},
					"trendLines": [],
					"graphs": chartData_Graph,
					"guides": [],
					"valueAxes": [{
						"unit": "$",
						"unitPosition": "left"
					}],
					"allLabels": [],
					"balloon": {},
					"legend": {
						"useGraphSettings": true
					},
					"titles": [],
					"dataProvider": chartData,
					"export":{
						"enabled": true,
						menu : []
					}
					
				} );
}

function columnAndLine_chart(data_arr,divID,show_unit,labelRotation,leftTitle,rightTitle)
{
	if(typeof(labelRotation)=='undefined' || labelRotation==''){ var labelRotation=0;}
	if(typeof(leftTitle)=='undefined' || leftTitle==''){ var leftTitle='Average Cost';}
	if(typeof(rightTitle)=='undefined' || rightTitle==''){ var rightTitle='No. of Procedure';}
	if(typeof(show_unit)=='undefined'){ var show_unit='$';}
	
	var chart = AmCharts.makeChart(divID, {
		"type": "serial",
		"theme": "light",    "legend": {
			"equalWidths": false,
			"useGraphSettings": true,
			"valueAlign": "left",
			"valueWidth": 70
		},
		"dataProvider": data_arr,
		"valueAxes": [{
			"id": "distanceAxis",
			"axisAlpha": 0,
			"gridAlpha": 0,
			"position": "left",
			"title": leftTitle,
			"unit": show_unit,
			"unitPosition": "left"
		}, {
			"id": "latitudeAxis",
			"axisAlpha": 0,
			"gridAlpha": 0,
			"labelsEnabled": false,
			"position": "right"
		}, {
			"id": "durationAxis",
			"axisAlpha": 0,
			"gridAlpha": 0,
			"inside": true,
			"position": "right",
			"title": rightTitle
		}],
		
		"graphs": [{
			"alphaField": "alpha",
			"balloonText": leftTitle+" "+show_unit+" [[value]]",
			"dashLengthField": "dashLength",
			"fillAlphas": 0.7,
			"legendPeriodValueText": "[[value]]",
			"legendValueText": show_unit+"[[value]]",
			"title": leftTitle,
			"type": "column",
			"valueField": "average_cost",
			"valueAxis": "distanceAxis"
		}, {
			"bullet": "round",
			"bulletBorderAlpha": 1,
			"bulletBorderThickness": 1,
			"useLineColorForBulletBorder": true,
			"bulletSize":5,
			"bulletColor": "#D66B01",
			"bulletSizeField": 1,
			"lineColor":"#D66B01",
			"lineThickness":2,
			"balloonText": rightTitle+" [[value]]",
			"legendValueText": "[[value]]",
			"title": rightTitle,
			"fillAlphas": 0,
			"valueField": "no_of_procedures",
			"valueAxis": "durationAxis"
		}],
		"chartCursor": {
			"cursorAlpha": 0.1,
			"cursorColor":"#000000",
			 "fullWidth":false,
			"valueBalloonsEnabled": false,
			"zoomable": false
		},
		"categoryField": "surgeon",
		"categoryAxis": {
			"parseDates": false,
			"autoGridCount": false,
			"axisColor": "#555555",
			"gridAlpha": 0.1,
			"gridColor": "#FFFFFF",
			"gridCount": 50,
		    "labelRotation": labelRotation
		},
		"export":{
			"enabled": true,
			menu : []
		}
	});
}


function multi_bar_chart(data_arr,divID,show_unit,labelRotation,leftTitle,rightTitle)
{
	if(typeof(labelRotation)=='undefined' || labelRotation==''){ var labelRotation=0;}
	if(typeof(leftTitle)=='undefined' || leftTitle==''){ var leftTitle='Average Cost';}
	if(typeof(rightTitle)=='undefined' || rightTitle==''){ var rightTitle='No. of Procedure';}
	if(typeof(show_unit)=='undefined'){ var show_unit='$';}
	
	//var data_arr = $.parseJSON(data_arr);
	var chart = AmCharts.makeChart(divID, {
    "theme": "light",
    "type": "serial",
    "dataProvider": data_arr,
    "valueAxes": [{
        "unit": show_unit,
        "position": "left",
        "title": leftTitle
    }],
    "startDuration": 1,   
	"legend": {
		"equalWidths": false,
		"useGraphSettings": true,
		"valueAlign": "left",
		"valueWidth": 70
	},
    "graphs": [{
        "balloonText": leftTitle+" [[value]]",
		"legendPeriodValueText": "[[value]]",
		"legendValueText": show_unit+"[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": leftTitle,
        "type": "column",
		"valueAxis": "durationAxis",
        "valueField": "surgeonTime"
    }, {
        "balloonText": rightTitle+" [[value]]",
		"legendValueText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": rightTitle,
        "type": "column",
        "clustered":false,
        "columnWidth":0.5,
		"valueAxis": "durationAxis",
        "valueField": "surgeryTime"
    }], 
    "plotAreaFillAlphas": 0.1,
    "categoryField": "surgeon",
    "categoryAxis": {
        "gridPosition": "start"
    },
	"chartCursor": {
		"valueLineAlpha": 0.5,
		"fullWidth": false,
		"cursorAlpha": 0.05
	},
	"categoryAxis": {
		"gridPosition": "start",
		"labelRotation": labelRotation
	},
    "export": {
    	"enabled": true,
			menu : []
     }

});
}


function downloadPDF(pdfReportTitle,pdfSaveAsName,pdfChartsArray,pdfFilters,pdfContent) {

	// So that we know export was started
	console.log("Start Exporting...");	
	
	// Collect actual chart objects out of the AmCharts.charts array
	var charts = {}, charts_remaining = pdfChartsArray.length;
	for (var i = 0; i < pdfChartsArray.length; i++) {
		for (var x = 0; x < AmCharts.charts.length; x++) {
			if (AmCharts.charts[x].div.id == pdfChartsArray[i])
				charts[pdfChartsArray[i]] = AmCharts.charts[x];
		}
	}
	
	// Trigger export of each chart
	for (var x in charts) {
		if (charts.hasOwnProperty(x)) {
			var chart = charts[x];
			chart["export"].capture({}, function() {
				this.toJPG({}, function(data) {

					// Save chart data into chart object itself
					this.setup.chart.exportedImage = data;
					
					// Reduce the remaining counter
					charts_remaining--;
					
					// Check if we got all of the charts
          if (charts_remaining == 0) {
            // Yup, we got all of them
            // Let's proceed to putting PDF together
            
						for(var c in pdfContent)
						{
							 var cols = pdfContent[c].columns
							 if(cols)
							 {
								 for(var m in cols)
								 {
									 var imgData = parseInt(cols[m].image);
									 var chartId = pdfChartsArray[imgData];
									 if(typeof chartId != 'undefined')
									 { 
											//console.log(charts[chartId].exportedImage);
											pdfContent[c].columns[m]['image'] = charts[chartId].exportedImage;
									 }
									 
								 }
							 }
						}
 
						generatePDF();
						
          }
					
				});
			});
		}
	}
	
	function generatePDF()
	{
		
			// Log
			console.log("Generating PDF...");
		
			// Initiliaze a PDF layout
			var layout = {
					 pageMargins: [10,100,10,30],
					 footer: function(currentPage, pageCount) { return {text: currentPage.toString() + ' of ' + pageCount,alignment:'center'} },
					 header: function(currentPage, pageCount) {
						// you can apply any logic and return any valid pdfmake element
						return [
							{ text: ' '},
							{ text: pdfReportTitle,style:'header'},
							{
								columns :
									[{
										width: "30%",
										stack:['Selected Procedures',pdfFilters.sel_proc],
										alignment:'center',
									},
									{
										width: "30%",
										stack:['Selected Surgeons',pdfFilters.sel_physician],
										alignment:'center'
									},
									{
										width: "*",
										stack:['Date Range',pdfFilters.date_range],
										alignment:'center'
									}],
									columnGap: 0,
									margin:[0,10,0,0]
									
							},
						];
					},
					content: pdfContent,
					styles:
					{
						header: { fontSize: 16, bold: true,alignment: 'center','background':'#CD532F',color:'white',lineHeight:1.25 },
						subHeader: { alignment:'center',lineHeight:1.25,fontSize:12, decoration: 'underline',decorationColor:'#CD532F',background:'#CD532F',color:'white',margin:[0,20,0,0] },
						boxTitle:{ alignment: "left", fontSize : 10, decoration: 'underline'},
						boxText:{ alignment: "left", fontSize : 10, decoration: 'underline'}
					}
				
				}
				
				
			// Trigger the generation and download of the PDF
			// We will use the first chart as a base to execute Export on
			chart["export"].toPDF(layout, function(data) {
				this.download(data, "application/pdf", pdfSaveAsName);
			});
			
	}

}