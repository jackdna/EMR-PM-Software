	// Function display assessment and plans options
	function setApOpt(obj){
		var tmp;
		//Other opts
		for(var i=2;i<=4;i++){
			tmp = document.getElementById("elem_apOpt_"+i);
			if(tmp){
				//Check if All or Active
				if((obj.value == "All") || ((obj.value == "Active") && (i>2))){
					if(obj.checked == true){
						//Clear and disable other opts
						tmp.checked = false;
						tmp.disabled = true;
					}else{
						//enable other opts
						tmp.disabled = false;
					}
				}
			}
		}
	}

	function showOptDiv(flgClose){
		var oDiv = document.getElementById("div_displayApOptions");
		if(oDiv){
			if(flgClose == "1"){
				oDiv.style.display = "none";
			}else{
				oDiv.style.display = (oDiv.style.display != "block") ? "block" : "none";
			}
		}
		stopClickBubble();
	}
	
	function stopClickBubble(){
		var ev = window.event;
		if(ev){
			ev.cancelBubble = true;
			if (ev.stopPropagation) ev.stopPropagation();
		}
	}

	function bodyClicked(){
		showOptDiv(1);
	}

	//Show Signs
	function show_sign(obj,val){
		var oImg = obj.getElementsByTagName("IMG")[0];
		if(val == 1){
			oImg.style.width="225px";
		}else{
			oImg.style.width="50px";
		}
	}

	function checkCommentsTa(obj){
		if(obj.value == "Comments:"){
			obj.value = "";
			obj.focus();
		}else{
			obj.select();
		}
	}

	function changeApDis(val){
		if(val != ""){
			window.location.replace("?elem_dap="+val);
		}
	}

	var obj_openPtProbList = null;
	function openPtProbList(){
		var url = "problem_list_popup.php?callFrom=WV";
		var feat = "width=950,height=600,scrolling=yes,resizable=1,left=10,top=15";
		if(!obj_openPtProbList || !(obj_openPtProbList.open) || (obj_openPtProbList.closed == true)){
			obj_openPtProbList = window.open(url, "PtProbList", feat);
		}
		//Focus
		obj_openPtProbList.focus();
	}
	
	// Correction Values
function calCorrectionVal(val,wh)
{
	var res = cor_calCorrectionVal(val);
	if(res.isvalid == true)
	{
		var correctionVal = res.correctionVal;
		var counter = res.counter;
		var avgReading = res.avgReading;

		// Set Values
		if(wh == "OD")
		{
			var objTextAvg = gebi("elem_od_average");
			var objTextCor = gebi("elem_od_correction_value");
			var objTextRead = gebi("elem_od_readings");

		}
		else if(wh == "OS")
		{
			var objTextAvg = gebi("elem_os_average");
			var objTextCor = gebi("elem_os_correction_value");
			var objTextRead = gebi("elem_os_readings");
		}	
		
		if((typeof(correctionVal) != "undefined"))
		{
			if(counter > 1)
			{				
				//
				objTextAvg.style.display = "inline-block";
				objTextCor.style.visibility = "visible";
				objTextAvg.value = avgReading;
				objTextCor.value = correctionVal;
			}
			else
			{				
				objTextAvg.style.display = "none";
				objTextCor.style.visibility = "visible";
				objTextAvg.value = 0; //avgReading;
				objTextCor.value = correctionVal;
			}
		}
		else
		{
			objTextRead.value = "";
		}
	}
	saveCorrectVals();
}

function myTrim(str,f)
{
	str = str.replace(/^\s+|\s+$/, '');
	if(f==1)str = str.replace(/^(\&nbsp\;)+|(\&nbsp\;)+$/g, '');
	return str;
}

function cor_calCorrectionVal(val)
{	
	var correctionVal,avgReading,counter;
	var isvalid = false;
	//Remove Spaces
	var strVal = myTrim(val);	
	//Check Value for numeric
	if(!cor_isValid(strVal))
	{
		top.fAlert("Please enter comma separated Numeric values only.<br />(0123456789,)");
	}
	else
	{	
		isvalid = true;		
		var arrReadings = new Array();	
		arrReadings = val.split(",");
		//Length of Array
		var arrLength = arrReadings.length;
		counter = 0;
		var pachyReading = 0;				
		//Add all values
		for(i=0;i<arrLength;i++)
		{
			if(myTrim(arrReadings[i]) != "")
			{		
				pachyReading = parseInt(pachyReading) + parseInt(arrReadings[i]);
				counter += 1;			
			}			
		}				
		//Get Avg 
		avgReading = parseInt(pachyReading)/parseInt(counter);
		avgReading = Math.round(avgReading);
		//Correction Value 	
		correctionVal = cor_getCorrectionValue(avgReading);  
	}
	
	return {"isvalid": isvalid, "correctionVal":correctionVal, "avgReading":avgReading, "counter":counter};
}


function cor_getCorrectionValue(val)
{
	var maxVal,minVal,corVal,maxCorVal,minCorVal;
	
	
	if((val < 445) || (val > 645))
	{
		//alert("Average Value is out of the range of Correction Table (445 - 645).");		
		if(val > 645){
			return ">-7";
		}
		else if(val < 445){
			return ">7";
		}
		
	}
	else if((val >= 445) && (val < 455))
	{
		maxCorVal = 7;
		minCorVal = 6;
		maxVal = 445;
		minVal = 455;		
	}
	else if((val >= 455) && (val < 465))
	{
		maxCorVal = 6;
		minCorVal = 6;
		maxVal = 455;
		minVal = 465;		
				
	}
	else if((val >= 465) && (val < 475))
	{
		maxCorVal = 6;
		minCorVal = 5;
		maxVal = 465;
		minVal = 475;					
	}
	else if((val >= 475) && (val < 485))
	{
		maxCorVal = 5;
		minCorVal = 4;
		maxVal = 475;
		minVal = 485;					
	}
	else if((val >= 485) && (val < 495))
	{
		maxCorVal = 4;
		minCorVal = 4;
		maxVal = 485;
		minVal = 495;					
	}
	else if((val >= 495) && (val < 505))
	{
		maxCorVal = 4;
		minCorVal = 3;		
		maxVal = 495;
		minVal = 505;			
	}
	else if((val >= 505) && (val < 515))
	{
		maxCorVal = 3;
		minCorVal = 2;
		maxVal = 505;
		minVal = 515;					
	}
	else if((val >= 515) && (val < 525))
	{
		maxCorVal = 2;
		minCorVal = 1;
		maxVal = 515;
		minVal = 525;					
	}
	else if((val >= 525) && (val < 535))
	{
		maxCorVal = 1;
		minCorVal = 1;
		maxVal = 525;
		minVal = 535;					
	}
	else if((val >= 535) && (val < 545))
	{
		maxCorVal = 1;
		minCorVal = 0;
		maxVal = 545;
		minVal = 535;					
	}
	else if((val >= 545) && (val < 555))
	{
		maxCorVal = 0;
		minCorVal = -1;
		maxVal = 545;
		minVal = 555;				
	}
	else if((val >= 555) && (val < 565))
	{
		maxCorVal = -1;
		minCorVal = -1;
		maxVal = 555;
		minVal = 565;					
	}
	else if((val >= 565) && (val < 575))
	{
		maxCorVal = -1;
		minCorVal = -2;
		maxVal = 565;
		minVal = 575;					
	}
	else if((val >= 575) && (val < 585))
	{
		maxCorVal = -2;
		minCorVal = -3;
		maxVal = 575;
		minVal = 585;						
	}
	else if((val >= 585) && (val < 595))
	{
		maxCorVal = -3;
		minCorVal = -4;
		maxVal = 585;
		minVal = 595;					
	}
	else if((val >= 595) && (val < 605))
	{
		maxCorVal = -4;
		minCorVal = -4;
		maxVal = 595;
		minVal = 605;					
	}
	else if((val >= 605) && (val < 615))
	{
		maxCorVal = -4;
		minCorVal = -5;
		maxVal = 605;
		minVal = 615;					
	}
	else if((val >= 615) && (val < 625))
	{
		maxCorVal = -5;
		minCorVal = -6;
		maxVal = 615;
		minVal = 625;					
	}
	else if((val >= 625) && (val < 635))
	{
		maxCorVal = -6;
		minCorVal = -6;
		maxVal = 625;
		minVal = 635;					
	}
	else if((val >= 635) && (val <= 645))
	{
		maxCorVal = -6;
		minCorVal = -7;
		maxVal = 635;
		minVal = 645;		
	}
	corVal = cor_refineCorrectionValue(maxCorVal,minCorVal,maxVal,minVal,val);
		
	return corVal; 
}

function cor_refineCorrectionValue(mxc,mnc,mx,mn,av)
{
	var totalVal = parseInt(mx) + parseInt(mn);
	var avgVal = parseInt(totalVal/2);
	//alert(av +" : "+ avgVal);
	if(av >= avgVal)
	{
		return mnc;
	}else if(av < avgVal)
	{
		return mxc;
	} 	
}

function gebi(id,t){
	var o;
	if(t==1){
	o = document.getElementsByName(id);
	}else if(t==2){
	o = document.getElementsByTagName(id);
	}else{
		o = document.getElementById(id);
		
		if(o == null){
			o = document.getElementsByName(id)[0];	
		}
	}
	return o;
}

function cor_isValid(strVal)
{
	var bag = "0123456789,";
	var strLength = strVal.length;
	var chr;
	
	for(i=0;i<strLength;i++)
	{
		chr = strVal.charAt(i);
		if(bag.indexOf(chr) == -1)
		{
			// no Char 
			return false;
		}			
	}
	return true;
}	

function saveCorrectVals(flg){
	var p = "elem_od_readings="+$("input[name=elem_od_readings]").val()+
		   "&elem_od_average="+$("input[name=elem_od_average]").val()+
		   "&elem_od_correction_value="+$("input[name=elem_od_correction_value]").val()+
		   "&elem_os_readings="+$("input[name=elem_os_readings]").val()+
		   "&elem_os_average="+$("input[name=elem_os_average]").val()+
		   "&elem_os_correction_value="+$("input[name=elem_os_correction_value]").val()+
		   "&elem_cor_date="+$("input[name=elem_cor_date]").val();
	var url = global_js_vars.webroot+"/interface/chart_notes/past_diag/ajax_handler.php?save_information=yes&ajax_request=yes";
	$.ajax({
		url:url,
		data:p,
		type:'POST',
		success:function(response){
			if(response != ''){
				alert(response);
			}
			if(typeof(flg)!="undefined"){ 
				flg++; 
				pag_save(flg); 
			} 
		}
	});
}

function saveTrgt(flg){
	
	var pid = $("#elem_ptId").val();
	if((pid == "")  ||  (typeof pid == "undefined" ) ){
		if(typeof(flg)!="undefined" && flg == "1"){
			flg++;	
			pag_save(flg);
		}
		return ;
	}
	
	var url = global_js_vars.webroot+"/interface/chart_notes/past_diag/ajax_handler.php?save_information=yes&save_target=yes&ajax_request=yes";
	$.post(url,{ptId:pid,trgtOd:$("#elem_trgtOd").val(),trgtOs:$("#elem_trgtOs").val()},
	function(data){
		//update IOP
		if(window.opener.top.fmain && window.opener.top.fmain.loadExamsSummary){
			window.opener.top.fmain.loadExamsSummary("iop_gon");
		}
		
		if(typeof(flg)!="undefined"){
			flg++;
			pag_save(flg);
		}
		
	});
}

var pag_save_close=0;
function pag_save(flg){	
	if(typeof(flg)!="undefined" && flg=="saveclose"){pag_save_close=1;flg=1;}
	if(typeof(flg)=="undefined"||flg==""){ flg=1; }
	if(flg==1){
		saveCorrectVals(flg);
	}else if(flg==2){
		saveTrgt(flg);	
	}else if(flg==3){
		saveCommentsTa($("#elem_commentsta")[0], flg);
	}else if(flg==4){
		saveHeardAboutUs(flg);
	}else{		
		if(pag_save_close==1){ top.window.close(); }
		else{ alert("PAG information is saved."); }
	}
}

function saveHeardAboutUs(flg) {
	var pid = $("#elem_ptId").val();
	if((pid == "")  ||  (typeof pid == "undefined" ) ){
		if(typeof(flg)!="undefined" ){
			flg++;	
			pag_save(flg);
		}
		return ;
	}
	
	var url = global_js_vars.webroot+"/interface/chart_notes/past_diag/ajax_handler.php?save_information=yes&ajax_request=yes";
	$.post(url,{elem_heardAbtUs:$("#elem_heardAbtUs").val(),heardAbtOther:$("#heardAbtOther").val(),heardAbtDesc:$("#heardAbtDesc").val(),heardAbtSearchId:$("#heardAbtSearchId").val(),heardAbtSearch:$("#heardAbtSearch").val()},
	function(data){
		console.log(data)
		if(typeof(flg)!="undefined"){
			flg++;
			pag_save(flg);
		}
		
	});
}

function saveCommentsTa(obj, flg){
	var oPtId = document.getElementById("elem_ptId");
	if((trim(oPtId.value) == "")  ||  (typeof oPtId.value == "undefined" ) ){
		if(typeof(flg)!="undefined"){
			flg++;	
			pag_save(flg);
		}
		return ;
	}
	
	var url = global_js_vars.webroot+"/interface/chart_notes/past_diag/ajax_handler.php?save_information=yes&ajax_request=yes";
	params = "ptId="+oPtId.value;
	params += "&comments="+obj.value;
	$.post(url,params,function(data){
		obj.value = (typeof data != "undefined") ? data : "";		
		//
		if(typeof(flg)!="undefined"){
			flg++;	
			pag_save(flg);
		}
	});
}

function showBigImage(id){	
	$("#divFImg").remove();
	var pid = $("#elem_ptId").val(); if(typeof(pid)=="undefined"){pid="";}
	var pth = global_js_vars.webroot+"/interface/chart_notes/requestHandler.php"+"?elem_formAction=showImg2&formId="+id+"&filetype=full&ptid="+pid+"&req_ptwo=1"; 
	var htm = 	"<div id=\"divFImg\" style=\"position:fixed;top:50px;left:300px;border:1px solid black;max-width:90%;max-height:90%;z-index:3;\" >"+
			"<div id=\"divFImg_hdr\" style=\"width:100%;height:20px;background-color:gold;text-align:right;cursor:move;\"   ><span style=\"cursor:pointer;font-weight:bold;padding:2px;\" onmousedown=\"stopClickBubble();\" onclick=\"$('#divFImg').remove();\" >Close</span></div>"+
			"<div style=\"overflow:auto;max-width:100%;max-height:95%;background-color:#FFF;\"  >"+"<img src=\""+pth+"\" alt=\"Full image\" ></div>"+
			"</div>";
	$("body").append(""+htm);
	$("#divFImg").draggable({handle:"#divFImg_hdr"});	
}

function IOP_showGraphsAm(val){
	
	var str_qry="";	
	var pid = $("#elem_ptId").val(); if(typeof(pid)=="undefined"){pid="";}
	if(pid!=""){ str_qry = "&p_id="+pid;}
	
	var u = global_js_vars.webroot+"/interface/chart_notes/past_diag/ajax_handler.php?ajax_request=yes&get_graph_data=yes"+str_qry;
	var p="elem_opts=All";
	$.post(u,p,function(data){
		var ARR_result = JSON.parse(data);
		var line_pay_graph_var_arr_js = ARR_result['line_pay_graph_var_detail'];
		var line_payment_tot_arr_js = ARR_result['line_payment_tot_detail'];
		if(line_pay_graph_var_arr_js && line_payment_tot_arr_js){
		line_chart('serial','IOPGraphChartAmMain',line_payment_tot_arr_js,line_pay_graph_var_arr_js,'90');
		$('#myModal .modal-title').html("IOP Graph");
		$('#myModal').modal('show');
		}
		//document.getElementById('IOPGraphChartAmMain').style.display='block';
	});
}

function show_vis_graphs(val){
	var str_qry="";	
	var pid = $("#elem_ptId").val(); if(typeof(pid)=="undefined"){pid="";}
	if(pid!=""){ str_qry = "&ptid="+pid;}
	var u = global_js_vars.webroot+"/interface/chart_notes/requestHandler.php?elem_formAction=getVisionGraphAm&req_ptwo=1&"+str_qry;
	var p="elem_opts=All";
	$.post(u,p,function(data){
		var ARR_result = JSON.parse(data);
		
		//--
		$("#IOPGraphChartAmMain").html("<div id=\"IOPGraphChartAm\" class=\"pull-left\" ></div><div id=\"IOPGraphChartAm1\" class=\"pull-left\" ></div>");
		
		var line_pay_graph_var_arr_js = (ARR_result['line_pay_graph_var_detail'] && ARR_result['line_pay_graph_var_detail']["dis"]) ? ARR_result['line_pay_graph_var_detail']["dis"] : null ;
		var line_payment_tot_arr_js = (ARR_result['line_payment_tot_detail'] && ARR_result['line_payment_tot_detail']["dis"]) ? ARR_result['line_payment_tot_detail']["dis"] : null ;
		
		var line_pay_graph_var_arr_js_nr = (ARR_result['line_pay_graph_var_detail'] && ARR_result['line_pay_graph_var_detail']["nr"]) ? ARR_result['line_pay_graph_var_detail']["nr"] : null;
		var line_payment_tot_arr_js_nr = (ARR_result['line_payment_tot_detail'] && ARR_result['line_payment_tot_detail']["nr"]) ? ARR_result['line_payment_tot_detail']["nr"] : null;
		var noflg=0;
		
		if(line_payment_tot_arr_js && line_pay_graph_var_arr_js){
			line_chart('serial','IOPGraphChartAm',line_payment_tot_arr_js,line_pay_graph_var_arr_js,'90'); noflg+=1;
		}else{
			//document.getElementById('IOPGraphChartAm').style.marginTop = "15%";
			document.getElementById('IOPGraphChartAm').innerHTML = "No distance data available";
		}
		
		if(line_payment_tot_arr_js_nr && line_pay_graph_var_arr_js_nr){
			line_chart('serial','IOPGraphChartAm1',line_payment_tot_arr_js_nr,line_pay_graph_var_arr_js_nr,'90'); noflg+=1;
		}else{
			//document.getElementById('IOPGraphChartAm1').style.marginTop = "15%";
			document.getElementById('IOPGraphChartAm1').innerHTML = "No Near data available";
		}		
		
		if(noflg==0){
			top.fAlert("Graph information does not exists.");
			return;
		}else{
			document.getElementById('IOPGraphChartAm').style.display='inline-block';	document.getElementById('IOPGraphChartAm1').style.display='inline-block';
			document.getElementById('IOPGraphChartAm').style.width='50%'; document.getElementById('IOPGraphChartAm1').style.width='50%'; 
			document.getElementById('IOPGraphChartAm').style.height='100%'; document.getElementById('IOPGraphChartAm1').style.height='100%';
			$('#myModal .modal-title').html("Acuity");
		}
		//--		
		
		$('#myModal').modal('show');
		$("#myModal").draggable();
		//document.getElementById('IOPGraphChartAmMain').style.display='block';
	});
}

function line_chart(chart_type,div_id,data_arr,data_graph_arr,labelRotation){
	if(typeof(labelRotation)=='undefined' || labelRotation==''){ var labelRotation=0};
	//alert(data_arr)
	var chartData = JSON.parse(data_arr);
	var chartData_Graph = JSON.parse(data_graph_arr);

	var title = 'IOP';
	if(div_id=="IOPGraphChartAm"){ title = "Distance"; }else if(div_id=="IOPGraphChartAm1"){ title = "Near"; }
	
	var chart = AmCharts.makeChart(div_id, {
		"type": chart_type,
		"categoryField": "category",
		"startDuration": 0,
		"theme": "light",
		"fontSize": 12,
		"categoryAxis": {
			"gridPosition": "start",
			"labelRotation": labelRotation,
		},
		"trendLines": [],
		"graphs": chartData_Graph,
		"guides": [],
		"valueAxes": [{
			"unit": "",
			"unitPosition": "left",
		}],
		"allLabels": [],
		"balloon": {},
		"legend": {
			"useGraphSettings": true
		},
		"titles": [{"text": ""+title}],
		"dataProvider": chartData,
		
	} );
}



function showChartRx(o,fid){
	$("#divFImg").remove();
	var htm = 	"<div id=\"divFImg\" style=\"position:absolute;top:10px;left:10px;border:1px solid black;background-color:red;color:white;\" >"+
			"Processing"+
			"</div>";
	$("body").append(""+htm);
	var opos=$(o).position();
	$("#divFImg").offset({ top: opos.top, left: opos.left-400 });	
	$.get("../common/requestHandler.php?elem_formAction=getChartRx&fid="+fid,function(data){
		$("#divFImg").remove();
		if(data!=""){
			var htm = 	"<div id=\"divFImg\" style=\"background-color:white;position:absolute;top:10px;left:10px;border:1px solid black;width:400px;max-height:90%;\" >"+
			"<div style=\"width:100%;height:20px;background-color:gold;text-align:right;cursor:move;\"  onmousedown=\"makeDraggable_v2(this,'divFImg');\" ><span style=\"cursor:pointer;font-weight:bold;padding:2px;\" onmousedown=\"stopClickBubble();\" onclick=\"$('#divFImg').remove();\" >Close</span></div>"+
			""+data
			"</div>";
			$("body").append(""+htm);
			var opos=$(o).position();
			$("#divFImg").offset({ top: opos.top, left: opos.left-400 });
		}
	});
}

function mkprint(){
	var winPtDPrt = window.open("chart_patient_diagnosis.php?cameFrom=print_window","PtDiagPrint","width=1100px,resizable=yes,scrollbars=yes");
}

function openDilateCN(a,b,c,d){
	if(displayPrevDat&&confirm("All unsaved data in work view can be lost. Do you want to continue?")){
		displayPrevDat(a,b,c,d);
	}
}

function displayPrevDat( fid, wh, st, relnum,tId ){		
	if(( wh == "Cee" ) || (wh == "MR" ) ){	
		wh  = "Chart Note";
	}else if( (wh == "Gonio") || (wh  == "Chart Note") || (wh == "Dilation") || (wh == "Ophthalmoscopy") || (wh == "Fundus Exam Drawing") ){			
		window.opener.top.fmain.showFinalize(wh, fid, st, relnum);
	}else{
		window.opener.top.fmain.showFinalize(wh, fid, st, relnum,0,tId); //Tests
	}
}

//
function set_shw_rec(){	
	var shw_rec= $("#el_shw_rec").val();
	var pth = global_js_vars.webroot+"/interface/chart_notes/past_diag/ajax_handler.php"+"?ajax_request=yes&elem_formAction=set_pag_records_lmt&set_shw_rec="+shw_rec+""; 		
	if(typeof(shw_rec)!="undefined" && shw_rec!="" && isNaN(shw_rec)==false && parseInt(shw_rec)>0){ $.get(pth, function(d){ window.location.reload() });	}
}

function pging_cn_dig(st){  if(isNaN(st) && typeof(st.value)!="undefined"){ st=st.value; }else if(isNaN(st)){ st=0; }  window.location.replace("chart_patient_diagnosis.php?st="+st); }

function pag_showDos(tp,fid,st,rn ){	
	if(window.opener && typeof(window.opener.top.fmain.showFinalize) != "undefined"){		
		window.opener.top.fmain.showFinalize(''+tp,''+fid,''+st,''+rn);		
		window.close();
		//window.opener.top.focus(); //in IE only
	}else if(typeof(showFinalize) != "undefined"){
		showFinalize(''+tp,''+fid,''+st,''+rn);
	}else{
		// when called from landling page	
		rn = (rn == "1") ? "1" : "0";
		var opn = (rn == "0") ? "1" : "0";
		var strQS="";
		strQS+="&elem_formAction=Show Prev Chart Notes";
		strQS+="&hd_finalize_id="+fid;
		strQS+="&elem_openForm="+opn;
		strQS+="&elem_ptId="+$("#elem_ptId").val();	
		strQS+="&req_ptwo=1";		
		if(window.opener && window.opener.top.fmain){var frmObj = window.opener.top.fmain;}
		if(frmObj && typeof(frmObj)!="undefined" && global_js_vars.rootdir){ frmObj.window.location.replace(global_js_vars.rootdir+"/chart_notes/requestHandler.php?a=1"+strQS);window.close();}
	}
}

//Set Hgt of patient past diagnonis
function setHgtGrpDiv(){
	var phgt = ($("#print_body").length>0) ? 15 : 0; //add more height for printing.
	var len = $("div[id*=div_assess_]").length;		
	for( var i=0;i<len;i++ ){			
		var oAssess = document.getElementById("div_assess_"+i);
		var oPlan = document.getElementById("div_plan_"+i);
		//var oTesting  = document.getElementById("div_testing_"+i);
		//var oSx  = document.getElementById("div_sx_"+i);
		//alert(oAssess+" \n "+oPlan);

		if( oAssess && oPlan ){
			var len2 = oAssess.childNodes.length;
			for( var j=0;j<len2;j++ ){
				var odivAssess = oAssess.childNodes[j];
				var odivPlan = oPlan.childNodes[j];
				//var odivTesting = oTesting.childNodes[j];
				if( odivAssess || odivPlan  ){ //|| odivTesting
					var hgtAssess = ( odivAssess && (typeof odivAssess.offsetHeight != "undefined")) ? odivAssess.offsetHeight : 15 ;
					var hgtPlan = ( odivPlan && (typeof odivPlan.offsetHeight != "undefined")) ? odivPlan.offsetHeight : 15 ;
				//var hgtTesting = ( odivTesting && (typeof odivTesting.offsetHeight != "undefined")) ? odivTesting.offsetHeight : 15 ;
					var tmp="";
					if( (hgtAssess > hgtPlan) ){
						tmp=hgtAssess;
					}else{
						tmp=hgtPlan;
					}
					
					tmp = parseInt(tmp) + parseInt(15) + parseInt(phgt); //increased some height to remove overlapping					
					
					/*
					if( (tmp < hgtTesting) ){
						tmp=hgtTesting;
					}
					*/

					if(odivAssess){
						odivAssess.style.height = ""+tmp+"px";
					}
					if(odivPlan){
						odivPlan.style.height = ""+tmp+"px";
					}
					/*
					if(odivTesting){
						odivTesting.style.height = ""+tmp+"px";
					}
					*/
				}
			}
		}
	}

}


$(document).ready(
	function(){
		var tt = $(window).height()-75;
		$('.manwhtbox').height(tt);
		var h = tt-(90+90+30+20+35); 
		$("#conPtGlance").height(h);
		setHgtGrpDiv();
		//
		try{
			$('.datepicker').datetimepicker({timepicker:false,format:window.opener.top.jquery_date_format,autoclose: true,scrollInput:false});
			//
			window.opener.$("#tl_pgl span[title='Patient at a glance']").triggerHandler("mouseout");
			$('[data-toggle="tooltip"]').tooltip();
			
			if(opener){
				if(opener.top)
					if( opener.top.fmain){
						var obj = $('#pgd_showpop',opener.top.fmain.document);
						if( obj.length > 0 ) {
							obj.fadeOut();
						}
					}
			}
		}catch(err){}
	}
	
	
);


var heardAboutSearch = ['Family','Friends','Doctor','Previous Patient.','Previous Patient'];
function onHeardAbtUsChnge(_this){
	
	if( $(_this).val() == 'Other' ) {
		$("#otherHeardAboutBox").removeClass('hidden').addClass('inline');
		$("#elem_heardAbtUs").removeClass('inline').addClass('hidden');
	}
	
	var heardAbtVal = $("#elem_heardAbtUs").val();
				
	if(heardAbtVal !== '') {
		
		if( heardAbtVal !== 'Other' ) {
			var tmpArr = heardAbtVal.split("-");
			heardAbtVal = tmpArr[1].trim();
		}
					
		if($.inArray(heardAbtVal, heardAboutSearch) !== -1 ) {
			if( heardAbtVal == 'Doctor') {
				$("#heardAbtSearch").attr('onkeyup',"top.loadPhysicians(this,'heardAbtSearchId')")
					.attr('onfocus',"top.loadPhysicians(this,'heardAbtSearchId')")
					.removeAttr('onKeydown');				
			}
			else {
				$("#heardAbtSearch").removeAttr('onkeyup onkeyup')
					.attr('onKeydown','if( event.keyCode == 13) { searchHeardAbout(); }');	
			}
			
			$("#tdHeardAboutSearch").removeClass('hidden').addClass('inline');
			$("#heardAbtDesc").removeClass('inline').addClass('hidden');
		}
		else {
			$("#heardAbtDesc").removeClass('hidden').addClass('inline');
			$("#tdHeardAboutSearch").removeClass('inline').addClass('hidden');
			//set_heard_type($_this);
			//$('#heardAbtDesc').typeahead({source:type_head_source});
		}
	}
	else {
		$("#heardAbtDesc,#tdHeardAboutSearch").removeClass('inline').addClass('hidden');
	}
}

function backHerdAbtUs(){
	
	$("#elem_heardAbtUs").removeClass('hidden').addClass('inline').val('');
	$("#otherHeardAboutBox").removeClass('inline').addClass('hidden');
	
	$("#heardAbtSearchId,#heardAbtSearch,#heardAbtDesc").val('');
	$("#heardAbtDesc,#tdHeardAboutSearch").removeClass('inline').addClass('hidden');
	//set_heard_type($_this);
	//$('#heardAbtDesc').typeahead({source:type_head_source});
}