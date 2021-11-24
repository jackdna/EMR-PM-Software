$(window).load(function() {
	$(document).ready(function() {

		window.moveTo(0, 0);
		window.resizeTo(screen.availWidth*0.98, screen.availHeight*0.99);
	
		var hippa 		=	$('.all_admin_content_agree');
		var hippa_win 	=	$(window).height();
		
		var header_height		=	$('.header_wrap').outerHeight(true);
		var header_tabs_height	=	$('.tabs_wrap').outerHeight(true);
		var footer_height		=	$('.footer_wrap_2').outerHeight(true);
		var filter_area			=	$('.filter_area').outerHeight(true);
		
		var extra_height=15;
		var height_custom_scroll=	$('.scrollable_yes');
		var height_custom 	=	hippa_win - (header_height+header_tabs_height+footer_height+filter_area+extra_height);

		height_custom_scroll.css({ 'min-height' : height_custom , 'max-height': height_custom, 'overflow-y':'auto' });
	});
});
	
function fancyDialog(msg,title,btn1,func1,btn2,func2,w,h,btn3,func3)
{
	/*
	PARAMETER DESCRIPTION
	title 	  -> to diaplay text on the title bar of the dialog box.
	msg   	  -> this text will be displayed as the body of the dialog box.
	btn1      -> text value displayed as the caption of 1st button. ("false" if want no button)
	btn2      -> text value displayed as the caption of 2nd button. ("false" if want no button)
	func1     -> Function(with parameter if applicable) Executed by click of btn1. ("" if you are using no button)
	func2     -> Function(with parameter if applicable) Executed by click of btn2. ("" if you are using no button)
	btnCancel -> OPTIONAL. value can be true/false. Default="false". To display an additonal CANCEL hide dialog box.
	mask	  -> OPTIONAL. value can be true/false. Setting it to "true" will lock & cover the body with transparency.
	drag      -> OPTIONAL. value can be true/false. Setting it to "true" will enable the user to drag the dialog box.
	callback  -> OPTIONAL. Any function which you want to triger when dialog box will be closed/dis-appeared.
	w		  -> OPTIONAL. Integer value. By Default is 300 (pixels). Width of the dialog box.
	h		  -> OPTIONAL. Integer value. By Default is "auto". Height of the dialog box.
	*****************************************************************************************************/

	var text = ''; 
	if((typeof(w) == "string" && w=='default') || typeof(w) == "undefined" || w<=0){w="500px";}else{w=(parseInt(w)+60)+"px";}
	if((typeof(h) == "string" && h=='default') || typeof(h) == "undefined" || h<=0){h="auto";}else{h=parseInt(h)+"px";}
	if(typeof(isCenter) != "boolean"){isCenter=true;}
	if(!maskOpacity) {maskOpacity=50;maskBrowserOpacity=0.5;}
	if(typeof(mask) != "boolean"){mask=false;maskOpacity=0;maskBrowserOpacity=0;}
	if(maskOpacity > 0) { maskBrowserOpacity = maskOpacity/100; }
	if(typeof(drag) != "boolean"){drag=false;}
	if(typeof(btnCancel) != "boolean"){btnCancel=false;}
	if(typeof(showClose) != "boolean"){showClose=true;}	
	var width = pageWidth();
  	var height = pageHeight();
	var left = leftPosition();
	var top = topPosition();
	if(typeof(l) != "undefined" && !isCenter){left=l+"px";}
	if(typeof(t) != "undefined" && !isCenter){top=t+"px";}
	var dialogwidth = w;  
	var dialogheight = h; //alert('TypeofLeft='+typeof(dialogheight)+', height='+typeof(height))
	var topposition = 100;
	var leftposition = parseInt(left) + (width / 2) - (parseInt(dialogwidth) / 2);
	if(typeof(dialogBoxCounter)=="undefined") dialogBoxCounter=0; else dialogBoxCounter++;
	if(callback==null){callback=false;}else if(callback==''){callback=false;}
	
	/*trying to embedd Messi--*/
	var butonnsArr = new Array();
	YsAction = NoAction = '';
	if(typeof(btn1) == "string" && typeof(func1) == "string"){
		butonnsArr[0] = {'id': 0, 'label': btn1, 'val': 'Y'}
	//	func1 = func1.replace('return false;','');
		YsAction = func1;
	}
	if(typeof(btn2) == "string" && typeof(func2) == "string"){
		butonnsArr[1] = {'id': 0, 'label': btn2, 'val': 'N'}
		if(func2!='return' && func2!='return false'){
			func1 = func1.replace(Array('return false;','return;','return false'),'');
			NoAction = func2;
		}
		
	}
	
	MessiOptionsArr = {
		'title':		title,
		'modal':		true,
		'closeButton':	true,
		'width':		w,
		'height':		h,
		'buttons':		butonnsArr,
		'callback':		function(val){
							if(val=='Y'){
								eval(YsAction);
							//	try{eval(YsAction);} catch(e){alert(e.message);}
							}else if(val=='N' && NoAction!=false){
								eval(NoAction);
							//	try{eval(NoAction);}catch(e){alert(e.message);}
							}
						}
	};
	//a=window.open();a.document.write(btn1+"<hr>"+func1+'<hr><hr>'+btn2+"<hr>"+func2);
	new top.Messi(msg, MessiOptionsArr);
	/* Messi embedding end---*/

}//end of function dialogBox.

/*---MODIFIED FANCY ALERT (USED IN TESTS)--*/
function fancyAlert(msg, title, actionToPerform, width, height_adjustment,obj) {
	if(typeof(title)=='undefined' || title=='') title='Financial Dashboard';
	if(typeof(width)=='undefined' || width=='') width='500px';
	new top.Messi(msg, {'title': title, modal: true, 'width': width, buttons: [{id: 0, label: 'Close', val: 'X'}],callback: function(){if(typeof(actionToPerform)=='string'){eval(actionToPerform);}else if(typeof(actionToPerform)=='object'){actionToPerform.focus();}}});
}
function fancyConfirm(msg, title, YsAction, NoAction, width, height) {
	if(typeof(title)=='undefined' || title=='') title='Financial Dashboard';
	if(typeof(width)=='undefined' || width=='') width='500px';
	if(typeof(NoAction)=='undefined' || NoAction=='') NoAction=false;
	new top.Messi(msg, {'title': title, modal: true, 'width': width, buttons: [{id: 0, label: 'Yes', val: 'Y', "class": 'btn-success'}, {id: 1, label: 'No', val: 'N', "class": 'btn-danger'}],callback: function(val){if(val=='Y'){eval(YsAction);}else if(val=='N' && NoAction!=false){eval(NoAction);}}});
}
function fancyModal(msg,w,h){
	if(typeof(w)!='string'){w='';}
	if(typeof(h)!='string'){h='';}
	new top.Messi(msg,{modal:true,width:w,height:h});
}
function removeMessi(){$('.messi-modal,.messi').remove();}
function UpdateActiveMessi(m){$('.messi-content').html(m);}
/*---END OF MODIFIED FANCY ALERT (USED IN TESTS)--*/

function pie_chart(chart_type,div_id,data_arr,data_sign){
	if(typeof(data_sign)=='undefined'){ var data_sign='$'; var precision=2;}else{var precision ='';};
		//alert(data_arr)
		var chartData = $.parseJSON(data_arr);
		for (var i in chartData) {
			chartData[i].abs_val = Math.abs(chartData[i].val);
		}
		for (var i in chartData) {
			chartData[i].val = (data_sign+chartData[i].val).replace('$-','-$');
		}
		var chart = AmCharts.makeChart(div_id,{
		  "type"    : chart_type,
		  "titleField"  : "kee",
		  "valueField"  : "abs_val",
		  "labelText": "[[title]]: ([[val]])",
		  "balloonText": "[[title]]: [[percents]]% ([[val]])",
		  "precision": precision,
		  "dataProvider"  : chartData,
		  "responsive": {
			"enabled": true
			}
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
	}
	} );
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
					
				} );
}

function validDateCheck(StartDate,EndDate){
	var Start_Date = Date.parse(document.getElementById(StartDate).value)
	var End_Date = Date.parse(document.getElementById(EndDate).value)
	var return_val = false;
	if(Start_Date != '' && End_Date != '' && Start_Date > End_Date){
		return_val = true;		
	}
	return return_val;
}

function show_loading_image(mode, padd_top, show_text){//TO SHOW / HIDE LOADING IMAGE
	if(mode == "show"){
		display_toggle("div_loading_image", "display", "block");
		if(padd_top != "" && typeof(padd_top) != "undefined"){
			$("#div_loading_image").css("margin-top", padd_top+"px");
		}else{
			$("#div_loading_image").css("margin-top", "0px");
		}
		if(show_text != "" && typeof(show_text) != "undefined"){
			$("#div_loading_text").html(show_text);
			display_toggle("div_loading_text", "display", "block");
		}
	}
	if(mode == "hide"){
		$("#div_loading_text").html("");
		display_toggle("div_loading_image", "display", "none");
		display_toggle("div_loading_text", "display", "none");
	}
}

function display_toggle(obj_id, property_name, property_value, iframe_name){//TO TOGGLE ANY STYLE PROPERTY
	if(iframe_name != "" && typeof(iframe_name) != "undefined"){
		eval(iframe_name+".document.getElementById(\""+obj_id+"\").style."+property_name+" = \""+property_value+"\"");
	}else{
		eval("document.getElementById(\""+obj_id+"\").style."+property_name+" = \""+property_value+"\"");
	}
}