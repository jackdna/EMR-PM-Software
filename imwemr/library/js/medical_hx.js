


function last_exm_all(pid,objVal)
{
	if(objVal!="no")
	{
		top.popup_win(top.JS_WEB_ROOT_PATH + '/interface/Medical_history/last_examined.php?pid='+pid+'&objVal='+objVal,'last_exm','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0');
		document.getElementById("selLastReviwed").selectedIndex = 0;
		$("#selLastReviwed").selectpicker('refresh');
	}
}

function reviewed_save_all(elem,pid,opid)
{
    top.show_loading_image('show');
	var query_string = "?patient_id="+pid+"&operator_id="+opid+"&section_name=complete";
    $.ajax({
		url: top.JS_WEB_ROOT_PATH+'/interface/Medical_history/save_reviewed_medical_hx.php'+query_string,
		type: 'POST',
		success: function(curr_tab)
		{
            top.show_loading_image('hide');
		},
		complete: function (curr_tab)
		{
            $("#leftPanell").load("medical_summary.php?ajaxReq=1&showpage="+curr_tab.responseText,function(){
                $('.selectpicker').selectpicker();
            });
		}
	});
}
			
function setChkChangeDefault(){
	if(top.document.getElementById("hid_chk_change_data_main")){
		top.document.getElementById("hid_chk_change_data_main").value = "no";
	}
}

/*
* Function : chk_change 
* Purpose - To Detect change in Prev and current value
*/

function chk_change(olddata,obj,e)
{
	e = e || event;
	try{ characterCode = e.keyCode; }
	catch(err){ characterCode = 0; }
	
	if(obj.type == "text" || obj.type == "textarea")
	{
		var newData = obj.value;
		if(characterCode != 9 && characterCode != 16 )
		{
			if(olddata != newData){
				top.document.getElementById("hid_chk_change_data_main").value = "yes";
			}
			else{
				if(top.document.getElementById("hid_chk_change_data_main").value != "yes"){
					top.document.getElementById("hid_chk_change_data_main").value = "no";
				}
			}
		}	
	}
	else if(obj.type == "checkbox")
	{
		var newData = "";
		if(obj.checked == true){
			newData = "checked";
		}
		if(olddata != newData){
			top.document.getElementById("hid_chk_change_data_main").value = "yes";
		}
		else{
			if(top.document.getElementById("hid_chk_change_data_main").value != "yes"){
				top.document.getElementById("hid_chk_change_data_main").value = "no";
			}
		}
	}
	else if(obj.type == "radio")
	{					
		var newData = "";
		if(obj.checked == true){
			newData = "checked";
		}
		if(olddata != newData){
			top.document.getElementById("hid_chk_change_data_main").value = "yes";
		}
		else{
			if(top.document.getElementById("hid_chk_change_data_main").value != "yes"){
				top.document.getElementById("hid_chk_change_data_main").value = "no";
			}
		}
	}
	else if(obj.type == "select-one")
	{	
		top.document.getElementById("hid_chk_change_data_main").value = "yes";					
	}
}

function get_date_separator(obj){
	var global_date_format = top.jquery_date_format;
	if( window.opener ) {
		if( window.opener.top )
			if( window.opener.top.global_date_format)
				var global_date_format = window.opener.top.global_date_format;
	}
	
	//Default Values
	var seprator1 = global_date_format.split('/');
	var seprator2 = global_date_format.split('-');
	// Checking separator in provided date whether it is '/' or '-'
	if(obj){
		seprator1 = $(obj).val().split('/');
		seprator2 = $(obj).val().split('-');
	}
	
	lseprator1 = seprator1.length;
	lseprator2 = seprator2.length;
	
	// Setting separator 
	if (lseprator1>1){
		var separator = '/';
	}
	else if (lseprator2>1){
		var separator = '-';
	}
	return separator;
}

function validate_date_range(obj,msg){
	// Get date separator
	var separator = get_date_separator(obj);
	var current_date = new Date();
	current_date = Date.parse((current_date.getMonth() + 1) + separator + current_date.getDate() + separator +  current_date.getFullYear());
	if(obj.val().length > 0){
		var provided_date = new Date(obj.val());
		provided_date = Date.parse((provided_date.getMonth() + 1) + separator + provided_date.getDate() + separator +  provided_date.getFullYear());
		if(provided_date > current_date){
			top.fAlert(msg);
			obj.val("");
			return false;
		}
		return true;
	}
}

function compare_dates(obj,comp_obj_end,comp_obj_start,msg,opp_comp){
	//obj => Object from where call is made
	//comp_obj_start => The id of the first date to compare
	//comp_obj_end	 => The id of the second date to compare with
	//msg => msg to display after comparison
	//opp_comp => to reverse the comparison logic i.e second date to be compared with first date
	
	// Get date separator
	var separator = get_date_separator(obj);
	
	// Get row number from where call is made
	var obj_row_no = $(obj).attr('id');
	obj_row_no = obj_row_no.substr(-1);
	
	var obj_start = $('#'+comp_obj_start+obj_row_no);
	var obj_end = $('#'+comp_obj_end+obj_row_no);
	
	if(obj_start.val().length > 0 && obj_end.val().length > 0){
		//Date to compare 
		var start_date_comp = new Date(obj_start.val());
		start_date_comp = Date.parse((start_date_comp.getMonth() + 1) + separator + start_date_comp.getDate() + separator +  start_date_comp.getFullYear());
		
		//Date to compare with
		var end_date_comp = new Date(obj_end.val());
		end_date_comp = Date.parse((end_date_comp.getMonth() + 1) + separator + end_date_comp.getDate() + separator +  end_date_comp.getFullYear());	
		
		
		if(opp_comp==1){
			if(end_date_comp > start_date_comp){
				top.fAlert(msg);
				$(obj).val("");
				return false;
			}
		}else{
			if(end_date_comp < start_date_comp){
				top.fAlert(msg);
				$(obj).val("");
				return false;
			}
		}
	}
}

function checkdate(obj){
	var resultResp = '';
	var obj = $(obj);
	if(obj.val() != ''){
		if(obj.hasClass('begin_date_med')){
			resultResp = validate_date_range(obj,'Begin date should be less than or equal to current date');
			if(resultResp != false){
				compare_dates(obj,'md_begindate','md_enddate','Begin date should be less than or equal to end date',1);
			}
		}else if(obj.hasClass('order_dt_rad')){
			resultResp = validate_date_range(obj,'Order date should be less than or equal to current date');
			if(resultResp != false){
				compare_dates(obj,'rad_order_date','rad_results_date','Order date should be less than or equal to Result date',1);
			}
		}else if($(obj).hasClass('end_date_med')){
			resultResp = validate_date_range(obj,'End date should be less than or equal to current date');
			if(resultResp != false){
				compare_dates(obj,'md_enddate','md_begindate','End date should be greater than or equal to begin date',0);
			}
		}else if($(obj).hasClass('dt_surgery')){
			validate_date_range(obj,'Date of Surgery should be less than or equal to current date');
		}else if($(obj).hasClass('allergy_bg_date')){
			validate_date_range(obj,'Begin date should be less than or equal to current date');
		}else if($(obj).hasClass('vs_dt')){
			validate_date_range(obj,'Date should be less than or equal to current date');
		}else if($(obj).hasClass('pl_dt')){
			validate_date_range(obj,'Onset date should be less than or equal to current date');
		}
		else if($(obj).hasClass('or_dt_lab')){
			resultResp = validate_date_range(obj,'Order date should be less than or equal to current date');
			if(resultResp!=false){
				compare_dates(obj,'lab_order_date','lab_result_date','Order date should be less than or equal to Test report date',1);
			}
		}
		else if($(obj).hasClass('rp_dt_lab')){
			resultResp = validate_date_range(obj,'Test report date should be less than or equal to current date');
			if(resultResp!=false){
				compare_dates(obj,'lab_result_date','lab_order_date','Test report date should be greater than or equal to Order date',0);
			}
		}
		else if($(obj).hasClass('result_dt_rad')){
			resultResp = validate_date_range(obj,'Result date should be less than or equal to current date');
			if(resultResp != false){
				compare_dates(obj,'rad_results_date','rad_order_date','Result date should be greater than or equal to Order date',0);
			}
		}
		else if($(obj).hasClass('order_dt_rad')){
			resultResp = validate_date_range(obj,'Order date should be less than or equal to current date');
			if(resultResp != false){
				compare_dates(obj,'rad_order_date','rad_results_date','Order date should be less than or equal to Result date',1);
			}
		}
		else if($(obj).hasClass('im_adm_dt')){
			validate_date_range(obj,'Admintd. date should be less than or equal to current date');
		}
		else if($(obj).hasClass('im_consent_dt')){
			validate_date_range(obj,'Consent date should be less than or equal to current date');
		}
		else if($(obj).hasClass('last_eye_exam_dt')){
			validate_date_range(obj,'Last eye exam date should be less than or equal to current date');
		}
		else if($(obj).hasClass('gh_blood_sugar_dt')){
			validate_date_range(obj,'Blood Sugar date should be less than or equal to current date');
		}
		else if($(obj).hasClass('gh_cholesterol_dt')){
			validate_date_range(obj,'Cholesterol date should be less than or equal to current date');
		}
		else if($(obj).hasClass('gh_counselling_dt')){
			validate_date_range(obj,'Counselling date should be less than or equal to current date');
		}
	}
}

function line_chart(chartTitle,chartData,div_id,cat_field,val_field,labelRotation)
{
	if(typeof chartData === 'boolean') return;
	if(typeof chartData === 'string')
	{ 
		$("#"+div_id).html(chartData); return;
	}
	
	if(typeof labelRotation === 'undefined' || labelRotation === '')
		var labelRotation = 0;
	
	// SERIAL CHART
  chart = new AmCharts.AmSerialChart();
	chart.dataProvider = chartData;
	chart.categoryField = cat_field;
	//Title
	chart.addTitle(chartTitle+' Values', '14', '#333', '0.7', true);
	chart.addLabel(3,270,chartTitle,'center','12','#333',270,0.7,true);
	
	// AXES
		// category
		var categoryAxis = chart.categoryAxis;
		if(cat_field.toLowerCase().indexOf("date") >= 0){
			categoryAxis.parseDates = true;		// as our data is date-based, we set parseDates to true
		}
		categoryAxis.minPeriod = "ss"; // our data is daily, so we set minPeriod to DD
		categoryAxis.dashLength = 2;
		categoryAxis.gridAlpha = 0.10;
		categoryAxis.axisColor = "#DADADA";

		// value
		var valueAxis = new AmCharts.ValueAxis();
		valueAxis.axisColor = "#DADADA";
		valueAxis.dashLength = 10;
		valueAxis.logarithmic = true; // this line makes axis logarithmic
		chart.addValueAxis(valueAxis);

	// GRAPH
	var graph = new AmCharts.AmGraph();
	graph.type = "smoothedLine";
	graph.balloonText = "[[category]]<br><b><span style='font-size:14px;'>[[value]]</span></b>";
	graph.bullet = "round";
	graph.bulletColor = "#FFFFFF";
	graph.useLineColorForBulletBorder = true;
	graph.bulletBorderAlpha = 1;
	graph.bulletBorderThickness = 2;
	graph.bulletSize = 7;
	graph.title = chartTitle;
	graph.valueField = val_field;
	graph.lineThickness = 2;
	graph.lineColor = "#00BBCC";
	chart.addGraph(graph);

	// CURSOR
	var chartCursor = new AmCharts.ChartCursor();
	chartCursor.cursorPosition = "mouse";
	chartCursor.categoryBalloonDateFormat='MM-DD-YYYY LL:NN A';
	chartCursor.categoryBalloonEnabled = true;
	chart.addChartCursor(chartCursor);

	// SCROLLBAR
	var chartScrollbar = new AmCharts.ChartScrollbar();
	chartScrollbar.graph = graph;
	chartScrollbar.scrollbarHeight = 1;
	chart.addChartScrollbar(chartScrollbar);
								
	// WRITE
	chart.write(div_id);
	
}
	
function save_medical_history()
{
	top.show_loading_image('show',100);
	var current_tab = document.getElementById("curr_tab").value;
	if(current_tab != "")
	{
		switch(current_tab.toLowerCase())
		{
			case "ocular": top.fmain.ocular_form.submit(); break;
			case "general_health":
				if(top.fmain.check_ref_phy_name())
				{
					top.show_loading_image('hide');
					if(top.fmain.check_smoking_status())
					{
						top.show_loading_image('show',100);
						top.fmain.general_form.submit();
					}
				}
			break;
			case "medication":
				if(top.fmain.fn_ocu_site_chk())
				{
					if( typeof searchMedsTW === undefined || typeof searchMedsTW !== 'function')
					{
						top.document.getElementById('save_medical_history').disabled=true;
						top.fmain.medications_form.submit();
					}
					else
					{
						top.show_loading_image('hide',100);
						searchMedsTW();
					}
				}
			break;
			case "sx_procedures":
				top.document.getElementById('save_medical_history').disabled=true;
				top.fmain.sx_procedures_form.submit();
			break;
			case "allergies":
				top.document.getElementById('save_medical_history').disabled=true;
				top.fmain.allergies_form.submit();
			break;
			case "immunizations": top.fmain.immunizations_form.submit(); break;
			case "social": top.fmain.social_form.submit(); break;
			case "family_hx": top.fmain.familyhx_form.submit(); break;
			case "vs": top.fmain.vs_form.submit(); break;
			case "problem_list": top.fmain.problem_list_form.submit(); break;
			case "lab":
			case "radiology": top.fmain.save_rad_test_form.submit(); break;
			case "hp": top.fmain.hp_form.submit(); break;
		}
	}
}

function show_hide(show_obj,hide_obj,_this)
{
	//show elements
	var status = false;
	if(			(typeof _this === 'object' && _this.value.indexOf('Other') != '-1') 
			||	typeof _this === 'undefined' )
		status = true;
	
	if(typeof show_obj ==='object') {
		$.each(show_obj,function(i,elem){
			if(status) $('#'+elem).removeClass('hidden');
		});
	}
	else {
		if(status) $("#"+show_obj).removeClass('hidden');
	}
	
	//hide elements	
	if(typeof hide_obj ==='object') {
			$.each(hide_obj,function(i,elem){
				if(status) $('#'+elem).addClass('hidden');
			});
	}
	else {
		if(status) $("#"+hide_obj).addClass('hidden');	
	}
	
}

function refine_data(obj)
{
	var data = obj.value;
	data = data.replace('&quot;','"');
	obj.value = data;
}	

var ModalID			=	"";
var txtFieldArr	=	"";
var hiddFieldTxt	=	"";
function show_multi_phy(op, phyType)
{
			op = op || 0;
			phyType = phyType || 0;
			
			var pTypeStr		=	"";
			var pTypeHidStr		=	"";
				
			if(phyType == 1)
			{
					ModalID			=	"referringPhysician";
					txtFieldArr		=	"txtRefPhyArr[]";
					pTypeStr		=	"strRefPhy";
					pTypeHidStr		=	"strRefPhyHid";
					hiddFieldTxt	=	"hidRefPhy";
			}
			else if(phyType == 2)
			{
					ModalID			=	"coManagedPhysician";
					txtFieldArr		=	"txtCoPhyArr[]";
					pTypeStr		=	"strCoPhy";
					pTypeHidStr		=	"strCoPhyHid";
					hiddFieldTxt	=	"hidCoPhy";
			}
			else if(phyType == 3)
			{
					ModalID			=	'primaryCareProvider';
					txtFieldArr	=	"txtPCPMedHxArr[]";
					pTypeStr		=	"strPCPMedHx";
					pTypeHidStr	=	"strPCPMedHxHid";
					hiddFieldTxt=	"hidPCPMedHx";
			}
			else if(phyType == 4)
			{
					ModalID			=	"primaryCarePhysician";
					txtFieldArr		=	"txtPCPDemoArr[]";
					pTypeStr		=	"strPCPDemoPhy";
					pTypeHidStr		=	"strPCPDemoHid";
					hiddFieldTxt	=	"hidPCPDemo";
			}
				
			if(op == 1)
			{
				var arrPhy 		= new Array();
				var arrPhyHid 	= new Array();
				var strPhy 		= "";
				var strPhyHid 	= "";
				
				if(document.getElementsByName(txtFieldArr))
				{
						var objPhyArr = document.getElementsByName(txtFieldArr);
						for(var i = 0; i < objPhyArr.length; i++){
							var objPhyArrID = objPhyArr[i].id;
							var arrPhyArrID = objPhyArrID.split("-");
							var hidPhyArrID = "hidPhyArr-" + arrPhyArrID[1];
							if((document.getElementById(objPhyArrID)) && (document.getElementById(hidPhyArrID))){
								arrPhy[i] = document.getElementById(objPhyArrID).value;
								arrPhyHid[i] = document.getElementById(hidPhyArrID).value;
							}							
						}
						if(arrPhy.length > 0)
						{
							strPhy = arrPhy.join("!~#~!");
							strPhyHid = arrPhyHid.join("!~#~!");
						}
				}
				
				var d = 'mode=get&phyType='+phyType+'&'+pTypeStr+'='+strPhy+'&'+pTypeHidStr+'='+strPhyHid;
				var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
				
				top.master_ajax_tunnel(url,top.fmain.show_multi_phy_handler);
				
			}
			else if(op == 0){
				if($("#tat_table"))
					$("#tat_table").hide();	
				$("#"+ModalID).modal('hide');
			}
			else if(op == 2){
				var selectedEffect = "blind";
				if(phyType == 1){
					var strTxtRefPhyArr = "";
					var strHidRefPhyArrID = "";	
					var strHidRefPhyIdID = "";				
					if(document.getElementsByName("txtRefPhyArr[]")){
						var objRefPhyArr = document.getElementsByName("txtRefPhyArr[]");
						for(var i = 0; i < objRefPhyArr.length; i++){
							var objRefPhyArrID = objRefPhyArr[i].id;
							var arrRefPhyArrID = objRefPhyArrID.split("-");
							var hidRefPhyArrID = "hidRefPhyArr-" + arrRefPhyArrID[1];
							var hidRefPhyIdID = "hidRefPhyId" + arrRefPhyArrID[1];
							if((document.getElementById(objRefPhyArrID)) && (document.getElementById(hidRefPhyArrID))){
								strTxtRefPhyArr += document.getElementById(objRefPhyArrID).value + "!$@$!";
								strHidRefPhyArrID += document.getElementById(hidRefPhyArrID).value + "!$@$!";
								if(document.getElementById(hidRefPhyIdID)){
									strHidRefPhyIdID += document.getElementById(hidRefPhyIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeleteRefPhyVal = document.getElementById("hidDeleteRefPhy").value;
					
					var d = 'mode=save&phyType='+phyType+'&strTxtRefPhyArr='+strTxtRefPhyArr+'&strHidRefPhyIdID='+strHidRefPhyIdID+'&strHidRefPhyArrID='+strHidRefPhyArrID+'&hidDeleteRefPhyVal='+hidDeleteRefPhyVal;
					var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,top.fmain.show_multi_phy_save_handler);
				}
				else if(phyType == 2){
					var strTxtCoPhyArr = "";
					var strHidCoPhyArrID = "";
					var strHidCoPhyIdID = "";
					if(document.getElementsByName("txtCoPhyArr[]")){
						var objCoPhyArr = document.getElementsByName("txtCoPhyArr[]");
						for(var i = 0; i < objCoPhyArr.length; i++){
							var objCoPhyArrID = objCoPhyArr[i].id;
							var arrCoPhyArrID = objCoPhyArrID.split("-");
							var hidCoPhyArrID = "hidCoPhyArr-" + arrCoPhyArrID[1];
							var hidCoPhyIdID = "hidCoPhyId" + arrCoPhyArrID[1];
							if((document.getElementById(objCoPhyArrID)) && document.getElementById(hidCoPhyArrID)){
								strTxtCoPhyArr += document.getElementById(objCoPhyArrID).value + "!$@$!";
								strHidCoPhyArrID += document.getElementById(hidCoPhyArrID).value + "!$@$!";
								if(document.getElementById(hidCoPhyIdID)){
									strHidCoPhyIdID += document.getElementById(hidCoPhyIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeleteCoPhyVal = document.getElementById("hidDeleteCoPhy").value;
					
					var d = "mode=save&phyType="+phyType+"&strTxtCoPhyArr="+strTxtCoPhyArr+"&strHidCoPhyIdID="+strHidCoPhyIdID+"&strHidCoPhyArrID="+strHidCoPhyArrID+"&hidDeleteCoPhyVal="+hidDeleteCoPhyVal;
					var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,top.fmain.show_multi_phy_save_handler_2);
				}
				else if(phyType == 3){
					var strTxtPCPMedHxArr = "";
					var strHidPCPMedHxArr = "";
					var strHidPCPMedHxId = "";
					if(document.getElementsByName("txtPCPMedHxArr[]")){
						var objPCPMedHxArr = document.getElementsByName("txtPCPMedHxArr[]");
						for(var i = 0; i < objPCPMedHxArr.length; i++){
							var objPCPMedHxArrID = objPCPMedHxArr[i].id;
							var arrPCPMedHxArrID = objPCPMedHxArrID.split("-");
							var hidPCPMedHxArrID = "hidPCPMedHxArr-" + arrPCPMedHxArrID[1];
							var hidPCPMedHxIdID = "hidPCPMedHxId" + arrPCPMedHxArrID[1];
							if((document.getElementById(objPCPMedHxArrID)) && document.getElementById(hidPCPMedHxArrID)){
								strTxtPCPMedHxArr += document.getElementById(objPCPMedHxArrID).value + "!$@$!";
								strHidPCPMedHxArr += document.getElementById(hidPCPMedHxArrID).value + "!$@$!";
								if(document.getElementById(hidPCPMedHxIdID)){
									strHidPCPMedHxId += document.getElementById(hidPCPMedHxIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeletePCPMedHxVal = document.getElementById("hidDeletePCP").value;
					
					var d = "mode=save&phyType="+phyType+"&strTxtPCPMedHxArr="+strTxtPCPMedHxArr+"&strHidPCPMedHxArr="+strHidPCPMedHxArr+"&strHidPCPMedHxId="+strHidPCPMedHxId+"&hidDeletePCPMedHxVal="+hidDeletePCPMedHxVal;
					var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,top.fmain.show_multi_phy_save_handler_3);
					
				}
				else if(phyType == 4){
					var strTxtPCPDemoArr = "";
					var strHidPCPDemoArrID = "";
					var strHidPCPDemoIdID = "";
					if(document.getElementsByName("txtPCPDemoArr[]")){
						var objPCPDemoArr = document.getElementsByName("txtPCPDemoArr[]");
						for(var i = 0; i < objPCPDemoArr.length; i++){
							var objPCPDemoArrID = objPCPDemoArr[i].id;
							var arrPCPDemoArrID = objPCPDemoArrID.split("-");
							var hidPCPDemoArrID = "hidPCPDemoArr-" + arrPCPDemoArrID[1];
							var hidPCPDemoIdID = "hidPCPDemoId" + arrPCPDemoArrID[1];
							if((document.getElementById(objPCPDemoArrID)) && document.getElementById(hidPCPDemoArrID)){
								strTxtPCPDemoArr += document.getElementById(objPCPDemoArrID).value + "!$@$!";
								strHidPCPDemoArrID += document.getElementById(hidPCPDemoArrID).value + "!$@$!";
								if(document.getElementById(hidPCPDemoIdID)){
									strHidPCPDemoIdID += document.getElementById(hidPCPDemoIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeletePCPDemoVal = document.getElementById("hidDeletePCPDemo").value;
					
					var d = "mode=save&phyType="+phyType+"&strTxtPCPDemoArr="+strTxtPCPDemoArr+"&strHidPCPDemoArrID="+strHidPCPDemoArrID+"&strHidPCPDemoIdID="+strHidPCPDemoIdID+"&hidDeletePCPDemoVal="+hidDeletePCPDemoVal;
					var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,top.fmain.show_multi_phy_save_handler_4);
					
				}
			}
		}
		
function show_multi_phy_handler(respRes)
{
		var arrResp = respRes.split("!~-1-~!");
		var arrTemp = arrResp[1].split("~-~");
		var phyName = new Array();
		for(var a = 0; a <= arrTemp.length; a++){
			phyName[a] = arrTemp[a];
		}
		arrTemp = arrResp[2].split("~-~");
		var phyNameID = new Array();
		for(var a = 0; a <= arrTemp.length; a++){
			phyNameID[a] = arrTemp[a];
		}
		
		$("#"+ModalID).html(arrResp[0]);
		
		if(document.getElementsByName(txtFieldArr)){
			var objPhyArr = document.getElementsByName(txtFieldArr);
			for(var i = 0; i < objPhyArr.length; i++){
				var objPhyArrID = objPhyArr[i].id;
				var arrPhyArrID = objPhyArrID.split("-");
				var hidPhyArrID = hiddFieldTxt + "Arr-" + arrPhyArrID[1];
				if((document.getElementById(objPhyArrID)) && (document.getElementById(hidPhyArrID))){
				}							
			}
		}
		
		$("#"+ModalID).modal('toggle');

}

function show_multi_phy_save_handler(respRes)
{
	var arrRespRes = respRes.split("-!-");
	if(arrRespRes[0] == "DONE"){
		document.getElementById("med_doctor").className = "form-control";
		document.getElementById("med_doctor").value = arrRespRes[1];
		document.getElementById("hidd_med_doctor").value = arrRespRes[2];
		$("#"+ModalID).modal('hide');
	}
}

function show_multi_phy_save_handler_3(respRes)
{
	var arrRespRes = respRes.split("-!-");
	if(arrRespRes[0] == "DONE"){
		document.getElementById("med_doctor").className = "form-control";
		document.getElementById("med_doctor").value = arrRespRes[1];
		document.getElementById("hidd_med_doctor").value = arrRespRes[2];
		$("#"+ModalID).modal('hide');
	}
}

function add_phy_row(add_image_id, del_image_id, intCounter, phyType)
{
			var objDelImg = $("#"+del_image_id);
			var objAddImg = $("#"+add_image_id);
			
			if(objAddImg){ objAddImg.addClass('hidden') }			
			if(objDelImg){ objDelImg.removeClass('hidden');	}
			
			var intCounterTemp = parseInt(intCounter) + 1;
			var divTrTag = document.createElement("div");
			divTrTag.id = "divTR" + "-" + phyType + "-" + intCounterTemp;
			divTrTag.className = "col-xs-12 margin-top-5";
			//divTrTag.style.marginBottom = "5px";
			
			var divTDTag1 = document.createElement("div");
			divTDTag1.className = "col-xs-2 text-center";
			divTDTag1.innerHTML = intCounterTemp;			
			divTrTag.appendChild(divTDTag1);
			
			var divTDTag2 = document.createElement("div");
			divTDTag2.className = "col-xs-9";
			
			if(phyType == 1){
				var txtId = "txtRefPhyArr-"+intCounterTemp;
			}
			else if(phyType == 2){
				var txtId = "txtCoPhyArr-"+intCounterTemp;
			}
			else if(phyType == 3){
				var txtId = "txtPCPMedHxArr-"+intCounterTemp;
			}
			else if(phyType == 4){
				var txtId = "txtPCPDemoArr-"+intCounterTemp;
			}
			var txtBox = document.createElement("input");
			txtBox.type = "text";
			if(phyType == 1){
				txtBox.name = "txtRefPhyArr[]";
			}
			else if(phyType == 2){
				txtBox.name = "txtCoPhyArr[]";
			}
			else if(phyType == 3){
				txtBox.name = "txtPCPMedHxArr[]";
			}
			else if(phyType == 4){
				txtBox.name = "txtPCPDemoArr[]";
			}
			txtBox.id = txtId;
			txtBox.value = "";
			txtBox.className = "form-control";
			if(phyType == 1){
				var hidId = "hidRefPhyArr-"+intCounterTemp;
			}
			else if(phyType == 2){
				var hidId = "hidCoPhyArr-"+intCounterTemp;
			}
			else if(phyType == 3){
				var hidId = "hidPCPMedHxArr-"+intCounterTemp;
			}
			else if(phyType == 4){
				var hidId = "hidPCPDemoArr-"+intCounterTemp;
			}
            
            //
			txtBox.setAttribute('onKeyup',"top.loadPhysicians(this,'"+hidId+"');");
			txtBox.setAttribute('onFocus',"top.loadPhysicians(this,'"+hidId+"');");
            
			var hidBox = document.createElement("input");
			hidBox.type = "hidden";
			if(phyType == 1){
				hidBox.name = "hidRefPhyArr[]";
			}
			else if(phyType == 2){
				hidBox.name = "hidCoPhyArr[]";
			}
			else if(phyType == 3){
				hidBox.name = "hidPCPMedHxArr[]";
			}
			else if(phyType == 4){
				hidBox.name = "hidPCPDemoArr[]";
			}
			divTDTag2.appendChild(txtBox);
			hidBox.id = hidId;
			hidBox.value = "";
			divTDTag2.appendChild(hidBox);
			divTrTag.appendChild(divTDTag2);
			
			var divTDTag3 = document.createElement("div");
			divTDTag3.className = "col-xs-1";
			var imgDelId = "imgDel" + "-" + phyType + "-" + intCounterTemp;
			var imgAddId = "imgAdd" + "-" + phyType + "-" + intCounterTemp;
			var strImgHTML = "<span id=\""+imgDelId+"\" name=\""+imgDelId+"\" class=\"pointer hidden\" onClick=\"del_phy_row('"+imgDelId+"','"+intCounterTemp+"', '','"+phyType+"');\" ><i class=\"glyphicon glyphicon-remove\"></i></span>";
			
			strImgHTML += "<span id=\""+imgAddId+"\" name=\""+imgAddId+"\" onClick=\"add_phy_row('"+imgAddId+"','"+imgDelId+"', '"+intCounterTemp+"', '"+phyType+"');\" class=\"pointer\" ><i class=\"glyphicon glyphicon-plus\"></i></span>";
			
			divTDTag3.innerHTML = strImgHTML;
			
			divTrTag.appendChild(divTDTag3);
			if(phyType == 1){
				document.getElementById("divMultiPhyInner1").appendChild(divTrTag);
			}
			else if(phyType == 2){
				document.getElementById("divMultiPhyInner2").appendChild(divTrTag);
			}
			else if(phyType == 3){
				document.getElementById("divMultiPhyInner3").appendChild(divTrTag);
			}
			else if(phyType == 4){
				document.getElementById("divMultiPhyInner4").appendChild(divTrTag);
			}
			//txtBox.addEventListener("keyup",function(){loadPhysicians(txtBox,hidId,'','','popup')});
			//txtBox.addEventListener("focus",function(){loadPhysicians(txtBox,hidId,'','','popup')});
			document.getElementById(txtId).focus();
		}
		
function del_phy_row(del_image_id, intCounter, intPhyIdDB, phyType)
{
			var objDelImg = $("#"+del_image_id);
			
			intPhyIdDB = intPhyIdDB || 0;
			//var divTrTag = "divTR" + intCounter;
			var divTrTag = "divTR" + "-" + phyType + "-" + intCounter
			if((intPhyIdDB > 0) && phyType == 1){				
				document.getElementById("hidDeleteRefPhy").value += intPhyIdDB+"~~"+intCounter+'-';
			}
			else if((intPhyIdDB > 0) && phyType == 2){				
				document.getElementById("hidDeleteCoPhy").value += intPhyIdDB+"~~"+intCounter+'-';
			}
			else if((intPhyIdDB > 0) && phyType == 3){				
				document.getElementById("hidDeletePCP").value += intPhyIdDB+"~~"+intCounter+'-';
			}
			else if((intPhyIdDB > 0) && phyType == 4){				
				document.getElementById("hidDeletePCPDemo").value += intPhyIdDB+"~~"+intCounter+'-';
			}
			if(document.getElementById(divTrTag)){
				var divType = "divMultiPhyInner" + phyType;
				var objMainDiv = document.getElementById(divType);
				objMainDiv.removeChild(document.getElementById(divTrTag));
			}
		}


function get_date_format(Format, Date){
	var t = [];
	$.each(Format , function(index, val) { 
		if(val === Date){
			t.push(index)
		}
	});
	return t;
}
	
//Setting Date and time 
function getDate_and_setToField(txtField_name, txtFieldName_for_time){	
	var separator = get_date_separator();
	var date_object = new Date(Date.now());
	
	//Global Format Date Array
	var date_format_arr = top.jquery_date_format.split(separator);
	
	//Current Date
	var curr_month = date_object.getMonth()+1;
	if (curr_month < 10) curr_month = "0" + curr_month;
	
	var curr_day = date_object.getDate();
	if (curr_day < 10) curr_day = "0" + curr_day;
	
	var curr_year = date_object.getFullYear();
	
	var current_date = curr_month+separator+curr_day+separator+curr_year;
	if(typeof txtField_name === 'object')
		txtField_name = $(txtField_name).attr('id');
	
	//Checking if provided value is valid date or note
	if($.trim($('#'+txtField_name).val()) != ''){
		var date_string = $('#'+txtField_name).val();
		date_string += ' '+date_object.getHours()+ ":" + date_object.getMinutes()+ ":" + date_object.getSeconds();
		
		date_object = new Date(date_string);
		if(date_object == 'Invalid Date'){
			date_object = new Date();
		}
	}
	
	if(txtField_name!=""){
	 //Formatting date acc. to global format
		var new_date_format = {};
		$.each(date_format_arr,function(id,val){
			if(val.toLowerCase() == 'd'){
				var st_date = get_date_format(date_format_arr, val);
				new_date_format[st_date] = ((date_object.getDate()) < 10 ? "0"+(date_object.getDate()) : (date_object.getDate()));
			}else if(val.toLowerCase() == 'm'){
				var DT_MonTH = ((date_object.getMonth()+1) < 10 ? "0"+(date_object.getMonth()+1) : (date_object.getMonth()+1))
				var st_month = get_date_format(date_format_arr, val);
				new_date_format[st_month] = DT_MonTH;
			}else if(val.toLowerCase() == 'y'){
				var st_year = get_date_format(date_format_arr, val);
				new_date_format[st_year] = date_object.getFullYear();
			}
		});
		str_date = new_date_format[0]+separator+new_date_format[1]+separator+new_date_format[2];
		var object_date_field = $('#'+txtField_name);
		object_date_field.val(str_date);
	}
	if(txtFieldName_for_time!=""){
		
		// Time set into field
		hh = date_object.getHours();
		mm = new String(date_object.getMinutes()); //convert minute to string
		ss = new String(date_object.getSeconds()); //convert seconds to string	
		var dn="PM"; if (hh<12) dn="AM";
		//if (hh>12) hh=hh-12; if (hh==0) hh=12;
		if (hh < 10) hh = "0" + hh;
		if (mm < 10) mm = "0" + mm;
		if(ss.length == 1){
		   ss = "0" + ss;
		} //seconds are removed
		
		str_time = hh + ":" + mm+""+":"+ss;// +" "+dn;
		var object_time_field = $('#'+txtFieldName_for_time);	
		if(str_time != '00:00:00' && $.trim(object_time_field.val()) == ''){
			object_time_field.val(str_time);
		}
	}
}	

function set_window_height(){
	var window_height = $(top.window).outerHeight(true);
	
	//Header height
	var header_height = $("#first_toolbar",top.document).height();
	var header_position = $("#first_toolbar",top.document).position();
	header_height = parseInt(header_height );
	//Footer height
	var footer_height = $('footer',top.document).height();
	
	//Final window height
	var final_height =  parseInt(window_height-(header_height+footer_height));
	
	$('.sdpanel',top.fmain.document).height(final_height).css('overflow-y','scroll');
}

//Medical Hx -> Order Set func()
function changeValue(){
	$('#change_order_set_val').val('yes');		
}

function filter_table(str,table)
{
	str = str.replace(/ +/g, ' ').toLowerCase();
	var tbl = $("#"+table + " > tbody");
	var tr = tbl.find('tr');
	
	tr.show().filter(function() {
		var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
		return !~text.indexOf(str);
	}).hide();
}

function save_order_set_data(){
	top.show_loading_image('show');
	$('#save_data').val('yes');
	document.order_set_frm.submit();
}	

//Common printing function
function data_action_function(id,curr_tab){
	var curr_tab = $('#curr_tab').val();
	switch(id){
		//All medication print requests
		case "print_medical_history":
			switch(curr_tab){
				case 'vs':window.open(top.JS_WEB_ROOT_PATH+'/interface/Medical_history/vs/vs_print.php');break;
				case 'immunizations':window.open(top.JS_WEB_ROOT_PATH+'/interface/Medical_history/immunizations/immunization_print.php');break;
				case 'allergies':window.open(top.JS_WEB_ROOT_PATH+'/interface/Medical_history/allergies/allergy_print.php');break;
				case 'medication':window.open(top.JS_WEB_ROOT_PATH+'/interface/Medical_history/medications/medication_print.php');break;
				case 'sx_procedures':window.open(top.JS_WEB_ROOT_PATH+'/interface/Medical_history/sx_procedures/sx_procedure_print.php');break;
				case 'hp':window.open(top.JS_WEB_ROOT_PATH+'/interface/Medical_history/hp/hp_print.php');break;
			}
			window.close();
		break;	
		//All medication export requests
		case 'export_medical_history':
			switch(curr_tab){
				case 'vs':window.location = (top.JS_WEB_ROOT_PATH+'/interface/Medical_history/vs/vs_csv.php');break;
				case 'immunizations':window.location = (top.JS_WEB_ROOT_PATH+'/interface/Medical_history/immunizations/immunization_csv.php');break;
				case 'allergies':window.location = (top.JS_WEB_ROOT_PATH+'/interface/Medical_history/allergies/allergy_csv.php');break;
				case 'medication':window.location=(top.JS_WEB_ROOT_PATH+'/interface/Medical_history/medications/medication_csv.php');break;
				case 'sx_procedures':window.location=(top.JS_WEB_ROOT_PATH+'/interface/Medical_history/sx_procedures/sx_procedure_csv.php');break;
			}
		break;	
		
		//All medication inport requests
		case 'import_medical_history':
			var window_height = $(window).innerHeight();
			window.open(''+top.JS_WEB_ROOT_PATH+'/interface/Medical_history/import/index.php?showpage='+curr_tab+'','Medical Import','width=1350,height='+window_height+',left=150,top=80,location=0,status=1,resizable=1,left=0,top=0,scrollbars=0');
		break;
	}
}

function export_hl7(dowhat){
	$("#div_disable").css("display", "block");
	url 		= top.JS_WEB_ROOT_PATH+'/interface/Medical_history/hl7stage2.php';
	url_suffix = '';
	if(dowhat=='Import'){
		url_suffix = '?for=LAB&task=Import';
	}
	url += url_suffix;			//top.popup_win('../Medical_history/hl7stage2.php'+url_suffix,'medHXHL7Export','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=520,left=10,top=100');
    $('#lab_hl7_upload_div #popup_title').text('UPLOAD LAB DATA HL7');
	$('#lab_im_ex_iframe').attr('src',url);
	//$('#lab_hl7_upload_div').show();
	$('#lab_hl7_upload_div').removeClass('hide');
	$('#lab_hl7_upload_div').modal('toggle');
}

function save_form_on_tab_changed(prev_tab, next_tab)
{
	var next_tab = next_tab || '';
	top.show_loading_image("show", 100);
	var frame = top.fmain;
	
	var next_dir = "";
	if(next_tab != ""){
		switch(next_tab.toLowerCase()){
			case "ocular":
			case "defaulttd":		
				next_dir = "ocular"; 
			break;					
			case "medication":		
				next_dir = "medications"; 
			break;
			default:
				next_dir = next_tab;
			break;
		}
	}
	
	var form_name = "";
	var tabName = "";
	prev_tab = prev_tab.toLowerCase();
	switch(prev_tab){
		case "ocular":
		case "defaulttd":
			form_name = frame.document.getElementById('ocular_form');
		break;
		case "general_health":
			tabName = "general_health";
			form_name = frame.document.getElementById('general_form');
		break;
		case "medication":
			form_name = frame.document.getElementById('medications_form');
		break;
		case "sx_procedures":
			form_name = frame.document.getElementById('sx_procedures_form');
		break;
		case "allergies":
			form_name = frame.document.getElementById('allergies_form');
		break;
		case "immunizations":
			form_name = frame.document.getElementById('immunizations_form');
		break;
		case "family_hx":
			form_name = frame.document.getElementById('familyhx_form');
		break;
		case "vs":
			form_name = frame.document.getElementById('vs_form');
		break;
		case "order_sets":
			form_name = frame.document.getElementById('order_set_frm');
			frame.document.getElementById('save_data').value = "yes";
		break;
		case "problem_list":
			form_name = frame.document.getElementById('problem_list_form');						
		break;
		case "lab":
			form_name = frame.document.getElementById('save_lab_test_form');
		break;
		case "radiology":
			form_name = frame.document.getElementById('save_rad_test_form');
		break;					
		case "hp":
			form_name = frame.document.getElementById('hp_form');
		break;					
	}
	
	if(form_name != ""){ 
		if(frame.document.getElementById('next_tab')){												
			frame.document.getElementById('next_tab').value = next_tab;
			frame.document.getElementById('next_dir').value = next_dir;
			if(top.document.getElementById('medical_tab_change')) { 
				top.document.getElementById('medical_tab_change').value='yes';
			}
			if(top.document.getElementById("div_alert_notifications")) {
				top.document.getElementById("div_alert_notifications").style.display = "none";
			}
			form_name.submit();
		}
	}
}
		
$(function(){
		//top.fmain.set_window_height();
	
		$('body').on('show','.accordion', function (e) {
			$(e.target).prev('.accordion-heading').addClass('accordion-opened');
		});
		$('body').on('hide','.accordion', function (e) {
			$(this).find('.accordion-heading').not($(e.target)).removeClass('accordion-opened');
		});
		
		$('[data-toggle="tooltip"]').tooltip();
		$('[data-toggle="popover"]').popover();
		
		$('.selectpicker').selectpicker().on('changed.bs.select', function(event, changedIndex, newValue) {
			var $this = $(this);
			
				
			if($this.hasClass('selectpicker_new') || $this.hasClass('selectpicker_all') )
			{
				var opt_len = $(this).find('option').length;
				var last_index = parseInt(opt_len - 1 );
				var temp_all_cnt = ($this.hasClass('selectpicker_new')) ? parseInt(opt_len - 2) : last_index; 
				var id = $this.attr('id');
			
				if (changedIndex === 0)
				{
					if(newValue === true)
					{
						$this.selectpicker('selectAll');
						$this.find('option[value="Other"]').prop('selected',false);
					}
					else if(newValue === false)
					{
						$this.selectpicker('deselectAll');
					}
					$('#div_'+id).removeClass('hidden');
					$('#other_'+id).addClass('hidden');
				}
				else if(changedIndex == last_index && $this.hasClass('selectpicker_new') )
				{
					$this.selectpicker('deselectAll').selectpicker('val','Other');
					
					$('#div_'+id).addClass('hidden');
					$('#other_'+id).removeClass('hidden');
				}
				else
				{ 
					var is_checked_all = false; var c = 0 ;
					var obj = $(this).find('option');
					
					obj.each(function(i,v){
						if(i > 0 && i < last_index)
							if($(this).is(':selected'))	c++;	
					});
					
					if(temp_all_cnt == c ) is_checked_all = true;
					
					$this.find('option[value="All"]').prop('selected',is_checked_all);
					$this.find('option[value="Other"]').prop('selected',false);
					
					$('#div_'+id).removeClass('hidden');
					$('#other_'+id).addClass('hidden');
				}
				$this.selectpicker('refresh');
				
			}
			
		});
		
		$('body').on('changed.bs.select','#sel_columns',changeColumnSelection);
	
		dtFormat = top.global_date_format;
		if(typeof(callFrom) != 'undefined' && callFrom == 'WV') {
			dtFormat = window.opener.top.global_date_format;
		}
		
		$('.datepicker').datetimepicker({lazyInit:true,timepicker:false,format:dtFormat,autoclose: true,scrollInput:false});
		$('.datetimepicker').datetimepicker({lazyInit:true,format:top.jquery_date_time_format,step:5,autoclose: true,scrollInput:false});
		$('.timepicker').datetimepicker({lazyInit:true,datepicker:false, format:'H:i'});
		
		$("body").on('click','.back_other',function(){
			var t = $(this).data('tab-name');
			$('#div_'+t).removeClass('hidden');
			$('#other_'+t).addClass('hidden');
			$('#' + t ).selectpicker('val','');
		});
		
});

function changeClassCombo(obj){
    var obj = $(obj);
    if(obj.val() != '' && (obj.hasClass('mandatory') === true)){
        obj.removeClass('mandatory');
    }else if(obj.val() == '' && obj.hasClass('mandatory') === false){
        obj.addClass('mandatory');
    }
    $(obj).selectpicker('refresh');
}

function close_lab_order(){
	//$('#lab_hl7_upload_div').addClass('hide');
	window.top.fmain.location.href='index.php?showpage=lab';
	$('#lab_im_ex_iframe').attr('src','about:blank');

}

function redirect_page(page) {
	
	page = page || '';
	var valid_pages = {'ocular':'medhx_ocu','general_health':'medhx_gen','medication':'medhx_med','sx_procedures':'medhx_proc','immunizations':'medhx_imm','allergies':'medhx_allergies'};
	
	if( $.inArray(page,valid_pages )){
		var elem = valid_pages[page] ? valid_pages[page] : '';
		if( elem ){
			top.$('.'+elem).click();
			//$('li[data-elem='+elem+']',top.document).trigger('click');
		}
	}
	return false;
}