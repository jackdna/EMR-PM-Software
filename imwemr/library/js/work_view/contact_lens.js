function justify2DecimalCL(objElem, global_sign, nosign){
	if(objElem.name.indexOf("Cyl")!=-1 || objElem.name.indexOf("sphere")!=-1 || objElem.name.indexOf("Sphere")!=-1){
		var valElem = $.trim(objElem.value);		
		if(valElem!="" && !isNaN(valElem) && nosign!=1){
			var tmp=global_sign;
			if(typeof(tmp)=="undefined"||tmp==""){tmp="+"}
			var ptrn = "^[\+|\-]";
			var reg = new RegExp(ptrn,'g');
			var sign = valElem.match(reg);		
			var unJustNumber = (sign == null) ? valElem : valElem.substr(1);				
			var justNumber = (unJustNumber == "") ? "" : parseFloat(unJustNumber).toFixed(2);		
			if(justNumber==0.00)tmp=sign="";
			if(objElem.name.indexOf("sphere")!=-1 || objElem.name.indexOf("Sphere")!=-1){tmp="";}
			objElem.value = (sign == null) ? tmp+justNumber : sign+justNumber;
		}else{ 	
			objElem.value = valElem;
		}
	}
}


//check 2 blur

function check2BlurCL(obj,wh,wh2,container, decPoint){
	var e = window.event;
	var kCode = e.keyCode

	if(!decPoint){ decPoint=2; }

	if( ((kCode >= 48) && (kCode<=57)) || 
	   ((kCode >=65 ) && (kCode <= 90)) || 
	   ((kCode >= 96) && ( kCode <= 111)) ||
	   ((kCode >= 186) && ( kCode <= 191)) ||
	   ((kCode >= 219) && ( kCode <= 222)) 
	){	
		//var oWh2 = gebi(wh2);
		//alert(oWh2);
		var val = ( typeof obj.value == "undefined" ) ? "" : obj.value;
		if(((wh == "I") || (wh == "A")) && ( val.length >= 3 ) ){
			// 
			ths = $(obj).attr('id');
			var nextFd = 0;
			$('input[type=text]',$('#'+container)).each(function()
			{
				if(nextFd == 1){
					$(this).focus();
					nextFd = 0;
					return 0;	
				}
				if(ths == $(this).attr('id')){
					nextFd = 1;	
				}else{
					nextFd = 0;
				}
			});
			//oWh2.focus();
		}else{
			var dInx = val.indexOf(".");			
			if( (dInx != -1) ){
				var sbStr = val.substr(dInx+1);					
				if( sbStr.length >= decPoint ){					
					// 
					ths = $(obj).attr('id');
					var nextFd = 0;
					$('input[type=text]',$('#'+container)).each(function()
					{
						if(nextFd == 1){
							$(this).focus();
							nextFd = 0;
							return 0;	
						}
						if(ths == $(this).attr('id')){
							nextFd = 1;	
						}else{
							nextFd = 0;
						}
					});
					//oWh2.focus();
				}
			}
		}
	}
}
//

/*
function gebi(id){
	return document.getElementById(id);
}
*/
function copyValuesODToOS(odName,osName){
	if(dgi(odName) && dgi(osName)){
		if(dgi(osName).value==''){
			//alert($('#'+osName).val()+' '+$('#'+osName).attr('class'));
			if(dgi(odName).value!=''){
			$("#"+osName).val(dgi(odName).value).trigger("click");				
			}
	
			//		$('#'+osName).removeClass("active");
			//		$('#'+osName).addClass("inact");
			//		$('#'+osName).addClass("active");
			//		$('#'+osName).addClass("acquity");
			//alert($('#'+osName).attr('class'));
	
			if(dgi(odName+'ID') && dgi(osName+'ID')){
				dgi(osName+'ID').value=dgi(odName+'ID').value;
			}
		}
	}
}
function showAppletsDiv(eye, sno, idoc_drawing_id, od_desc, os_desc){

	//if(typeof(drawingDataPath)!="undefined" && drawingDataPath!=""){  drawingDataPath = encodeURI(drawingDataPath); }
	/*	if(dgi(divId).style.display=='block'){
			hideAppletsDiv(divId);
		}else if(dgi(divId)) {
				dgi(divId).style.display='block';
		}
*/
	//window.open('cl_drawing.php?eye='+eye+'&sno='+sno+'&divId='+divId+'&drawingData='+drawingData+'&drawingOriginalData='+drawingOriginalData+'&description='+description,'clDrawing'+sno,'height=110px, width=500px,resizable=yes');

	parentCtrlId='idoc_drawing_id_'+eye;
	parentImgId='DrawingPngImg_'+eye;

	//window.open('cl_drawing.php?eye='+eye+'&sno='+sno+'&divId='+divId+'&drawingData='+drawingData+'&drawingDataPath='+drawingDataPath,'clDrawing'+sno,'height=720px, width=950px, left='+leftVal+'px, top=100px, resizable=yes');
	var clWidth = document.body.clientWidth;
	var clHeight = window.screen.availHeight;
	if(clHeight==null){clHeight=675;}
	w=clWidth; h=parseInt(clHeight)-125;	
	/*
	var width=1200; var height=730;
	var br=navigator.userAgent;
	if(br.search("Chrome")>-1 || br.search("Firefox")>-1){
	  width=1220; height=740;	
	}else{
	  width=1200; height=730;
	}	
	*/
	window.open('onload_wv.php?elem_action=Drawingpane&cl_draw=1&sno='+sno+'&eye='+eye+'&idoc_drawing_id='+idoc_drawing_id+'&parentCtrlId='+parentCtrlId+'&parentImgId='+parentImgId+'&od_desc='+od_desc+'&os_desc='+os_desc,'clDrawing'+sno,'height='+h+'px, width='+w+'px, left=0px, top=0px, resizable=yes');	
	
/*		if(dgi('drawingEvaluationOSId').style.display=='block' && str=="OS"){
			hideAppletsDiv("OS")
		}else if(dgi('drawingEvaluationOSId') && str=="OS") {
				dgi('drawingEvaluationOSId').style.display='block';
		}
*/		
  }
function hideAppletsDiv(divId){
		if(dgi(divId)) {
			dgi(divId).style.display='none';
		 }
}


function getCLImages_SCL(eye,num,callFrom,sdata, simg, desc ){	
	
	if(eye=='od'){
		
		var v1='corneaSCL_od_desc'+num;
		var v5="elem_SCLOdDrawingPath"+num;
		//top.fmain.all_data.getAssessmentSign(signType,1,"",objData.value,objImg.value);	
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,""+v1);
		$.post('cl_drawing.php',{ 'elem_formAction':'GetSign','sData':sdata,'sImg':simg},function(data){ 
			
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,""+v1);					
			if(data==''|| data=='0')data='';
	
			$('input[name='+v5+']').val(data);
			if(data==''){
				if(dgi("scl_odDrawingPngImg"+num)){
					dgi("scl_odDrawingPngImg"+num).src="images/odDrawing.png";
				}						
			}else{
				if(dgi("scl_odDrawingPngImg"+num)){
					dgi("scl_odDrawingPngImg"+num).src="images/odHasDrawing.png";
				}		
			}
			
		});
		
		if(callFrom=='wv'){			
			$('#corneaSCL_od_desc'+num).val(desc);
		}
	
	}
	if(eye=='os'){
		
		var v1='corneaSCL_os_desc'+num;
		var v5="elem_SCLOsDrawingPath"+num;		
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,""+v1);
		$.post('cl_drawing.php',{ 'elem_formAction':'GetSign','sData':sdata,'sImg':simg},function(data){ 
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,""+v1);					
			if(data==''|| data=='0')data='';

			$('input[name='+v5+']').val(data);

			if(data==''){
				if(dgi("scl_osDrawingPngImg"+num)){
					dgi("scl_osDrawingPngImg"+num).src="images/odDrawing.png";
				}						
			}else{
				if(dgi("scl_osDrawingPngImg"+num)){
					dgi("scl_osDrawingPngImg"+num).src="images/odHasDrawing.png";
				}		
			}
		});
		
		if(callFrom=='wv'){		
			$('#corneaSCL_os_desc'+num).val(desc);
		}
		
	}
	
}

function getCoords_SCL(eye,num,callFrom,flgGetImg)
{
	//alert(eye+' - '+num);
	if(eye=='od'){
		
		if(typeof(isHTML5OK)!="undefined" && isHTML5OK==1){	
			
			if(flgGetImg == 1){
				var objData = gebi("sig_dataapp_scl_od_drawing"+num);
				var objImg = gebi("sig_imgapp_scl_od_drawing"+num);
				
				window.opener.getCLImages_SCL(eye,num,callFrom,objData.value,objImg.value,dgi('corneaSCL_od_desc'+num).value);
				window.close();
			}
			
		}else{
		
			var coords;
			var objElemConjunctivaOdDrawing = dgi("elem_SCLOdDrawing"+num);
			coords = getCoords('app_scl_od_drawing'+num);	
			objElemConjunctivaOdDrawing.value = refineCoords(coords);
			var objConjunctivaOdDrawingOriginal = dgi("hdSCLOdDrawingOriginal"+num);	
	
			// ConjunctivaOd
			var checkDrawingConjunctivaOd = checkAppletDrawing(objElemConjunctivaOdDrawing.value);
			
			if((checkDrawingConjunctivaOd) && (objConjunctivaOdDrawingOriginal.value == ""))
			{
				objElemConjunctivaOdDrawing.onchange();
				objConjunctivaOdDrawingOriginal.value = objElemConjunctivaOdDrawing.value;
				if(dgi("scl_odDrawingPngImg"+num)){
						dgi("scl_odDrawingPngImg"+num).src="images/odHasDrawing.png";
					}		
			}
			else if((objElemConjunctivaOdDrawing.value != objConjunctivaOdDrawingOriginal.value) && (objConjunctivaOdDrawingOriginal.value != ""))
			{
					if(dgi("scl_odDrawingPngImg"+num)){
						dgi("scl_odDrawingPngImg"+num).src="images/odHasDrawing.png";
					}		
				objElemConjunctivaOdDrawing.onchange();
				objConjunctivaOdDrawingOriginal.value = objElemConjunctivaOdDrawing.value;
			}
			if(callFrom=='wv'){
				window.opener.$('#elem_SCLOdDrawing'+num).val(objElemConjunctivaOdDrawing.value);
				window.opener.$('#hdSCLOdDrawingOriginal'+num).val(objConjunctivaOdDrawingOriginal.value);
				window.opener.$('#corneaSCL_od_desc'+num).val(dgi('corneaSCL_od_desc'+num).value);
			}
		
		}
	}
	if(eye=='os'){
		
		if(typeof(isHTML5OK)!="undefined" && isHTML5OK==1){	
			
			if(flgGetImg == 1){
				var objData = gebi("sig_dataapp_scl_os_drawing"+num);
				var objImg = gebi("sig_imgapp_scl_os_drawing"+num);
				
				window.opener.getCLImages_SCL(eye,num,callFrom,objData.value,objImg.value,dgi('corneaSCL_os_desc'+num).value);
				window.close();
			}
			
		}else{
		
			// ConjunctivaOs
			var objElemConjunctivaOsDrawing = dgi("elem_SCLOsDrawing"+num);
			coords = getCoords('app_scl_os_drawing'+num);	
			objElemConjunctivaOsDrawing.value = refineCoords(coords);

			var objConjunctivaOsDrawingOriginal = dgi("hdSCLOsDrawingOriginal"+num);		

			var checkDrawingConjunctivaOs = checkAppletDrawing(objElemConjunctivaOsDrawing.value);
			if((checkDrawingConjunctivaOs) && (objConjunctivaOsDrawingOriginal.value == ""))
			{
				objElemConjunctivaOsDrawing.onchange();
				objConjunctivaOsDrawingOriginal.value = objElemConjunctivaOsDrawing.value;
				if(dgi("scl_oSDrawingPngImg"+num)){
					dgi("scl_oSDrawingPngImg"+num).src="images/odHasDrawing.png";
				}		
			}
			else if((objElemConjunctivaOsDrawing.value != objConjunctivaOsDrawingOriginal.value) && (objConjunctivaOsDrawingOriginal.value != ""))
			{
				objElemConjunctivaOsDrawing.onchange();
				objConjunctivaOsDrawingOriginal.value = objElemConjunctivaOsDrawing.value;
				if(dgi("scl_oSDrawingPngImg"+num)){
					
					dgi("scl_oSDrawingPngImg"+num).src="images/odHasDrawing.png";
				}		
			}
			if(callFrom=='wv'){
				window.opener.$('#elem_SCLOsDrawing'+num).val(objElemConjunctivaOsDrawing.value);
				window.opener.$('#hdSCLOsDrawingOriginal'+num).val(objConjunctivaOsDrawingOriginal.value);
				window.opener.$('#corneaSCL_os_desc'+num).val(dgi('corneaSCL_os_desc'+num).value);
			}
		
		}
	}
}

function getCLImages_RGP(eye,num,callFrom,sdata, simg, desc ){	
	
	if(eye=='od'){
		
		var v1='cornea_od_desc'+num;
		var v5="elem_conjunctivaOdDrawingPath"+num;
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,""+v1);
		$.post('cl_drawing.php',{ 'elem_formAction':'GetSign','sData':sdata,'sImg':simg},function(data){ 

			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,""+v1);					
			if(data==''|| data=='0')data='';

			$('input[name='+v5+']').val(data);
			if(data==''){
				if(dgi("rgp_odDrawingPngImg"+num)){
					dgi("rgp_odDrawingPngImg"+num).src="images/odDrawing.png";
				}						
			}else{
				if(dgi("rgp_odDrawingPngImg"+num)){
					dgi("rgp_odDrawingPngImg"+num).src="images/odHasDrawing.png";
				}		
			}
			
		});
		
		if(callFrom=='wv'){			
			$('#cornea_od_desc'+num).val(desc);
		}
	
	}
	if(eye=='os'){
		var v1='cornea_os_desc'+num;
		var v5="elem_conjunctivaOsDrawingPath"+num;		
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,""+v1);
		$.post('cl_drawing.php',{ 'elem_formAction':'GetSign','sData':sdata,'sImg':simg},function(data){ 
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,""+v1);					
			if(data==''|| data=='0')data='';

			$('input[name='+v5+']').val(data);
			if(data==''){
				if(dgi("rgp_osDrawingPngImg"+num)){
					dgi("rgp_osDrawingPngImg"+num).src="images/odDrawing.png";
				}						
			}else{
				if(dgi("rgp_osDrawingPngImg"+num)){
					dgi("rgp_osDrawingPngImg"+num).src="images/odHasDrawing.png";
				}		
			}			
		});
		
		if(callFrom=='wv'){		
			$('#cornea_os_desc'+num).val(desc);
		}
		
	}
}

function getCoords_RGP(eye,num,callFrom,flgGetImg){
	if(eye=='od'){
		if(typeof(isHTML5OK)!="undefined" && isHTML5OK==1){	
			if(flgGetImg == 1){
				var objData = gebi("sig_dataapp_rgp_od_drawing"+num);
				var objImg = gebi("sig_imgapp_rgp_od_drawing"+num);
				
				window.opener.getCLImages_RGP(eye,num,callFrom,objData.value,objImg.value,dgi('cornea_od_desc'+num).value);
				window.close();
			}
			
		}else{
		
			var coords;
			var objElemConjunctivaOdDrawing = dgi("elem_conjunctivaOdDrawing"+num);
			coords = getCoords('app_rgp_od_drawing'+num);	
			objElemConjunctivaOdDrawing.value = refineCoords(coords);
			var objConjunctivaOdDrawingOriginal = dgi("hdConjunctivaOdDrawingOriginal"+num);	
	
			// ConjunctivaOd
			var checkDrawingConjunctivaOd = checkAppletDrawing(objElemConjunctivaOdDrawing.value);
			
			if((checkDrawingConjunctivaOd) && (objConjunctivaOdDrawingOriginal.value == ""))
			{
				objElemConjunctivaOdDrawing.onchange();
				objConjunctivaOdDrawingOriginal.value = objElemConjunctivaOdDrawing.value;
				if(dgi("rgp_odDrawingPngImg"+num)){
						dgi("rgp_odDrawingPngImg"+num).src="images/odHasDrawing.png";
					}		
			}
			else if((objElemConjunctivaOdDrawing.value != objConjunctivaOdDrawingOriginal.value) && (objConjunctivaOdDrawingOriginal.value != ""))
			{
					if(dgi("rgp_odDrawingPngImg"+num)){
						dgi("rgp_odDrawingPngImg"+num).src="images/odHasDrawing.png";
					}		
				objElemConjunctivaOdDrawing.onchange();
				objConjunctivaOdDrawingOriginal.value = objElemConjunctivaOdDrawing.value;
			}
			if(callFrom=='wv'){
				window.opener.$('#elem_conjunctivaOdDrawing'+num).val(objElemConjunctivaOdDrawing.value);
				window.opener.$('#hdConjunctivaOdDrawingOriginal'+num).val(objConjunctivaOdDrawingOriginal.value);
				window.opener.$('#cornea_od_desc'+num).val(dgi('cornea_od_desc'+num).value);
			}
		
		}
	}
	if(eye=='os'){

		if(typeof(isHTML5OK)!="undefined" && isHTML5OK==1){	
			if(flgGetImg == 1){
				var objData = gebi("sig_dataapp_rgp_os_drawing"+num);
				var objImg = gebi("sig_imgapp_rgp_os_drawing"+num);
				
				window.opener.getCLImages_RGP(eye,num,callFrom,objData.value,objImg.value,dgi('cornea_os_desc'+num).value);
				window.close();
			}
			
		}else{
		
			// ConjunctivaOs
			var objElemConjunctivaOsDrawing = dgi("elem_conjunctivaOsDrawing"+num);
			coords = getCoords('app_rgp_os_drawing'+num);	
			objElemConjunctivaOsDrawing.value = refineCoords(coords);

			var objConjunctivaOsDrawingOriginal = dgi("hdConjunctivaOsDrawingOriginal"+num);		

			var checkDrawingConjunctivaOs = checkAppletDrawing(objElemConjunctivaOsDrawing.value);
			if((checkDrawingConjunctivaOs) && (objConjunctivaOsDrawingOriginal.value == ""))
			{
				objElemConjunctivaOsDrawing.onchange();
				objConjunctivaOsDrawingOriginal.value = objElemConjunctivaOsDrawing.value;
				if(dgi("rgp_oSDrawingPngImg"+num)){
					dgi("rgp_oSDrawingPngImg"+num).src="images/osHasDrawing.png";
				}		
			}
			else if((objElemConjunctivaOsDrawing.value != objConjunctivaOsDrawingOriginal.value) && (objConjunctivaOsDrawingOriginal.value != ""))
			{
				objElemConjunctivaOsDrawing.onchange();
				objConjunctivaOsDrawingOriginal.value = objElemConjunctivaOsDrawing.value;
				if(dgi("rgp_oSDrawingPngImg"+num)){
					
					dgi("rgp_oSDrawingPngImg"+num).src="images/osHasDrawing.png";
				}		
			}
			if(callFrom=='wv'){
				window.opener.$('#elem_conjunctivaOsDrawing'+num).val(objElemConjunctivaOsDrawing.value);
				window.opener.$('#elem_conjunctivaOsDrawing'+num).val(objConjunctivaOsDrawingOriginal.value);
				window.opener.$('#cornea_os_desc'+num).val(dgi('cornea_os_desc'+num).value);
			}
		
		}
	}
}

function getclear(appletId)
{
	if(typeof(isHTML5OK)!="undefined" && isHTML5OK==1){	
		if(oSimpDrw && oSimpDrw[appletId]){ oSimpDrw[appletId].clearCanvas();	}		
	}else{	
		with(document.applets[appletId])
		{
			clearIt();					
			onmouseout();
		}
	}	
}

function changeRow(clType, odos, rowName, totRows, rowNum, arrManufac, arrManufacId, arrManufacInfo)
{
	var showTitle = 0;
	//alert(clType+' - '+odos+' - '+rowName+' - '+totRows+' - '+rowNum);return false;
	var typeName ='';

	if(odos == 'od') { typeName = 'clTypeOD'; }else if(odos == 'os') { typeName = 'clTypeOS'; }else if(odos == 'ou') {typeName = 'clTypeOU'; }
	
	if(rowNum ==1) { showTitle =1; }
	if(rowNum > 1){
		if(dgi(typeName+(rowNum-1))){
			var prevClType = dgi(typeName+(rowNum-1)).value;
			if(clType != prevClType) {	showTitle = 1; }
		}
	}
	top.show_loading_image('show');
	$.ajax({ 
		url: "add_contactLens_row.php?mode=change&addType="+clType+"&showTitle="+showTitle+"&rowName="+rowName+"&odos="+odos+"&totRows="+totRows+"&rowNum="+rowNum+"&referer=wv",
		success: function(newRow){
			top.show_loading_image('hide');
			$("#"+rowName+rowNum).replaceWith(newRow);	// CHANGE ROw
			
			//DISABLING ON CASE OF PRESTHESIS OR NO-CL
			if(clType=='prosthesis' || clType=='no-cl'){
				$("#"+rowName+rowNum+' input[type="text"]').attr("disabled", true);
			}
			applyTypeAhead();
			//cl_ajax_type_ahd_all();
			var typeAheadWidth = "";
			if(clType == "scl"){
				typeAheadWidth = "300px";
			}else if(clType == "rgp"){
				typeAheadWidth = "250px";
			}else if(clType == "cust_rgp"){
				typeAheadWidth = "250px";
			}
			$('ul.typeahead').css('width', typeAheadWidth);
			$('ul.typeahead').css('max-width', typeAheadWidth);
			$('ul.typeahead').css('overflow', 'scroll');
			$("ul.typeahead li").each(function(){
				$(this).css("width", typeAheadWidth);
				$(this).css("max-width", typeAheadWidth);
			});
		}
	});
	
	var sclExist =''; 
	var rgpExist = '';
	if($('[id^="clTypeOD"')) {
		var ODLen = $('#txtTotOD').val();
		if(ODLen > 0) {
			for(i=1; i<=ODLen; i++)
			{
				if(dgi('clTypeOD'+i).value == 'scl') { sclExist =1; }
				if(dgi('clTypeOD'+i).value == 'rgp' || dgi('clTypeOD'+i).value == 'cust_rgp') { rgpExist =1; }
			}
		}
	}

	if($('[id^="clTypeOS"')) {

		var ODLen = $('#txtTotOS').val();
		if(ODLen > 0) {
			for(i=1; i<=ODLen; i++)
			{	
				if(dgi('clTypeOS'+i).value == 'scl') { sclExist =1; }
				if(dgi('clTypeOS'+i).value == 'rgp' || dgi('clTypeOS'+i).value == 'cust_rgp') { rgpExist =1; }
			}
		}
	}
	if($('[id^="clTypeOU"')) {

		var OULen = $('#txtTotOU').val();
		if(OULen > 0) {
			for(i=1; i<=OULen; i++)
			{	
				if(dgi('clTypeOU'+i).value == 'scl') { sclExist =1; }
				if(dgi('clTypeOU'+i).value == 'rgp' || dgi('clTypeOU'+i).value == 'cust_rgp') { rgpExist =1; }
			}
		}
	}
	

//	document.getElementById('CL_EvaluationTR').style.display = 'none'; 
//	document.getElementById('CL_EvaluationFittingsTR').style.display = 'none'; 

/*	if(sclExist == 1) {
		document.getElementById('CL_EvaluationTR').style.display = 'block'; 
		if(dgi('clGrpOU').checked == true || dgi('clGrpOD').checked == true){
			dgi('sclEvaluationODRow').style.display= 'block';
		}
		if(dgi('clGrpOU').checked == true || dgi('clGrpOS').checked == true){
			dgi('sclEvaluationOSRow').style.display= 'block';
		}
//		if($('[id^="clTypeOD"').length > 0){ dgi('sclEvaluationODRow').style.display= 'block'; }
//		if($('[id^="clTypeOS"').length > 0){ dgi('sclEvaluationOSRow').style.display= 'block'; }

	}*/

/*	if(rgpExist == 1) {
		document.getElementById('CL_EvaluationFittingsTR').style.display = 'block'; 
		if(dgi('clGrpOU').checked == true || dgi('clGrpOD').checked == true){
			dgi('rgpEvaluationODRow').style.display= 'block';
		}
		if(dgi('clGrpOU').checked == true || dgi('clGrpOS').checked == true){
			dgi('rgpEvaluationOSRow').style.display= 'block';
		}
	//	if($('[id^="clTypeOD"').length > 0){ dgi('rgpEvaluationODRow').style.display= 'block'; }
	//	if($('[id^="clTypeOS"').length > 0){ dgi('rgpEvaluationOSRow').style.display= 'block'; }
		
	}*/

}

function setTitleRows(clType){
//	alert(clType);
	if(clType == 'scl') {	
		var titleRow = '<tr>';
			titleRow+= '<td class="txt_10b valignBottom">&nbsp;</td>';
			titleRow+= '<td class="txt_10b valignBottom">B. Curve</td>';
			titleRow+= '<td class="txt_11b alignLeft">Diameter</td>';
			titleRow+= '<td class="txt_11b alignLeft" >Sphere</td>';
			titleRow+= '<td class="txt_11b nowrap alignLeft">Cylinder</td>';
			titleRow+= '<td class="txt_11b alignLeft" >Axis</td>';
			//titleRow+= '<td class="txt_11b alignLeft" >Color</td>';
			titleRow+= '<td class="txt_11b alignLeft" >ADD</td>';
			titleRow+= '<td class="txt_11b alignLeft nowrap">DVA</td>';
			titleRow+= '<td class="txt_11b alignLeft nowrap">NVA</td>';
			titleRow+= '<td class="txt_11b alignLeft nowrap">Type</td>';
			titleRow+= '</tr>';
	}
	if(clType == 'rgp')
	{
		var titleRow = '<tr>';
			titleRow+= '<td>&nbsp;</td>';
			titleRow+= '<td class="txt_11b nowrap">BC</td>';
			titleRow+= '<td class="txt_11b">Diameter</td>';						
			titleRow+= '<td class="txt_11b">Power</td>';
			titleRow+= '<td class="txt_11b">OZ</td>';
			titleRow+= '<td class="txt_11b">CT</td>';
			titleRow+= '<td class="txt_11b">Color</td>';
			titleRow+= '<td class="txt_11b">Latitude</td>'; 
			titleRow+= '<td class="txt_11b">Add</td>';
			titleRow+= '<td class="txt_11b">DVA</td>';
			titleRow+= '<td class="txt_11b">NVA</td>';
			titleRow+= '<td class="txt_11b">Type</td>'; 
			titleRow+= '<td class="txt_11b">Warranty</td>';
			titleRow+= '</tr>';
	}
	if(clType == 'cust_rgp')
	{
		var titleRow= '<tr class="txt_11b" >';
			titleRow+= '<td>&nbsp;</td>';
			titleRow+= '<td>BC</td>';							
			titleRow+= '<td>Diameter</td>';
			titleRow+= '<td>Power</td>';	
			titleRow+= '<td>2&#176;/W</td>';
			titleRow+= '<td>3&#176;/W</td>';
			titleRow+= '<td>PC/W</td>';
			titleRow+= '<td>OZ</td>';
			titleRow+= '<td>CT</td>';
			titleRow+= '<td>Color</td>';
			titleRow+= '<td>Blend</td>';
			titleRow+= '<td>Edge</td>';
			titleRow+= '<td>Latitude</td>';
			titleRow+= '<td>Add</td>';
			titleRow+= '<td>DVA</td>';
			titleRow+= '<td>NVA</td>';
			titleRow+= '<td>Type</td>';
			titleRow+= '<td>Warranty</td>';
			titleRow+= '</tr>';
	}

	return titleRow;
}

// DELETE ROW
function removeTableRow(rowId)
{
	$("#"+rowId).remove();
}
// ADD ROWS
function addNewRow(addType, odos, oldRow, imgId, rowNum, rowCols, referer, arrManufac, arrManufacId, arrManufacInfo)
{
	// Count number of OD rows
	var newNum = parseInt(rowNum) + 1;
	var newTrId = oldRow+newNum;
	var newImgId = imgId+newNum;
	$("#" + imgId + rowNum).removeClass('glyphicon-plus');
	$("#" + imgId + rowNum).addClass('glyphicon-remove');
	$("#" + imgId + rowNum).css('cursor', 'pointer');
	$("#" + imgId + rowNum).attr('title', 'Delete row');
	$("#" + imgId + rowNum).attr('data-original-title', 'Delete Row');
	$("#" + imgId + rowNum).attr('onclick',"removeTableRow('" + oldRow + rowNum + "')");

	$.ajax({ 
		url: "add_contactLens_row.php?addType="+addType+"&rowName="+oldRow+"&odos="+odos+"&rowNum="+rowNum+"&rowCols="+rowCols+"&referer="+referer,
		success: function(newRow){
			// Set Old Row Values
			//var imgObj = dgi(imgId+rowNum);
			//imgObj.title = 'Delete Row';
			//imgObj.style.height = '16';
			//imgObj.src = '../../images/cancelled.gif';
			
			
			
        	//$("#" + imgId + rowNum).onclick=function(){
				//removeTableRow(oldRow+rowNum);
			//}

			//-------------------	
			$("#"+oldRow+rowNum).after(newRow);	// ADD NEW ROW
			
			if(odos =='od') {
				$('#txtTotOD').val(newNum);
			}else if(odos =='os') { 
				$('#txtTotOS').val(newNum);
			}
			applyTypeAhead();
			fillValues(addType, odos, rowNum);	// Fill Values
			var typeAheadWidth = "";
			if(addType == "scl"){
				typeAheadWidth = "300px";
			}else if(addType == "rgp"){
				typeAheadWidth = "250px";
			}else if(addType == "cust_rgp"){
				typeAheadWidth = "250px";
			}
			$('ul.typeahead').css('width', typeAheadWidth);
			$('ul.typeahead').css('max-width', typeAheadWidth);
			$('ul.typeahead').css('overflow', 'scroll');
			$("ul.typeahead li").each(function(){
				$(this).css("width", typeAheadWidth);
				$(this).css("max-width", typeAheadWidth);
			});
		}
	});

			
	/*
var newRow = '<tr id="'+newTrId+'" style="display:block"><td style="width:15px;"  class="blue_color txt_10b">OD</td>';
    newRow+= '<td style="width:65px;" class="txt_11b alignLeft"><input type="text" name="SclsphereOD'+newNum+'" value="" size="6" class="txt_10"  id="SclsphereOD'+newNum+'"></td>';
    newRow+= '<td style="width:65px;" class="txt_11b alignLeft"><input  id="SclCylinderOD'+newNum+'" type="text" name="SclCylinderOD'+newNum+'" value="" size="6" class="txt_10" ></td>';
    newRow+= '<td style="width:59px;" class="txt_11b alignLeft" ><input type="text" name="SclaxisOD'+newNum+'" value="" size="6" class="txt_10"  id="SclaxisOD'+newNum+'" /></td>';
    newRow+= '<td style="width:70px;" class="txt_11b nowrap alignLeft" ><input class="txt_10" type="text" id="SclBcurveOD'+newNum+'" name="SclBcurveOD'+newNum+'" value="" size="6"  ></td>';
    newRow+= '<td style="width:65px;" class="txt_11b alignLeft" ><input  id="SclDiameterOD'+newNum+'" type="text" class="txt_10 " name="SclDiameterOD'+newNum+'" value="" size="7"></td>';
    newRow+= '<td style="width:60px;" class="txt_11b alignLeft"><input  id="SclAddOD'+newNum+'" type="text" class="txt_10" name="SclAddOD'+newNum+'" value=""   size="5" ></td>';
    newRow+= '<td style="width:100px;" class="txt_11b alignLeft nowrap" ><input id="SclDvaOD'+newNum+'" type="text" name="SclDvaOD'+newNum+'" value="" size="8" class="txt_10"></td>';
    newRow+= '<td style="width:130px;" class="txt_11b alignLeft nowrap" ><input type="text" name="SclNvaOD'+newNum+'"  id="SclNvaOD'+newNum+'" value="" size="12" class="txt_10" ></td>';
    newRow+= '<td style="width:auto;" class="txt_11b alignLeft nowrap" ><div class="nowrap fl"><table class="table_collapse_autoW"><tr>';
    newRow+= '<td><input class="txt_10" type="text" name="SclTypeOD'+newNum+'" id="SclTypeOD'+newNum+'" value="" style="width:250px;"></td>';
    newRow+= '<td></td></tr></table></div>';
	newRow+= '<div class="fl">&nbsp;<img src="images/odDrawing.png" id="odDrawingPngImg" onClick="showAppletsDiv(\'OD\');"  style="cursor:hand;" title="Show/Hide OD Drawing"></div>';
	newRow+= '<div class="fl">&nbsp;<img id="'+newImgId+'" class="link_cursor" src="../../images/add_medical_history.gif" alt="Add More" onClick="addNewRow(\''+oldRow+'\', \''+imgId+'\', \''+newNum+'\');"></div>';

	newRow+= '</td></tr>';
	*/
}

function set_properties(id, arrManufacInfo, addType){
	//alert(id+' '+addType);
	if(document.getElementById(id+'ID')){
		var odos=(id.indexOf('OD')=='-1') ? 'OS' : 'OD';
		
		arrN=id.split(odos);
		n=arrN[1];
		if(n){
			makeId=$('#'+id+'ID').val();
			if(makeId>0){
				if(typeof arrManufacInfo[makeId]!= "undefined"){
					arrVals= arrManufacInfo[makeId].split('~');
					if(addType=='scl'){
						if(arrVals[0]){ $('#SclBcurve'+odos+n).val(arrVals[0]); }
						if(arrVals[1]){ $('#SclDiameter'+odos+n).val(arrVals[1]); }
					}else if(addType=='rgp'){
						if(arrVals[0]){ $('#RgpBC'+odos+n).val(arrVals[0]); }
						if(arrVals[1]){ $('#RgpDiameter'+odos+n).val(arrVals[1]); }
					}else if(addType=='cust_rgp'){
						if(arrVals[0]){ $('#RgpCustomBC'+odos+n).val(arrVals[0]); }
						if(arrVals[1]){ $('#RgpCustomDiameter'+odos+n).val(arrVals[1]); }
					}
				}
			}
			//COPY TO OS
			if(odos=='OD'){
				osId=id.replace('OD','OS');
				if($('#'+osId)){ $('#'+osId).val($('#'+id).val()); }
				if($('#'+osId+'ID')){ $('#'+osId+'ID').val($('#'+id+'ID').val()); }
				if(addType=='scl'){
					if($('#SclBcurveOS'+n)){ $('#SclBcurveOS'+n).val($('#SclBcurveOD'+n).val()); }
					if($('#SclDiameterOS'+n)){ $('#SclDiameterOS'+n).val($('#SclDiameterOD'+n).val()); }
				}else if(addType=='rgp'){
					if($('#SclBcurveOS'+n)){ $('#RgpBCOS'+n).val($('#RgpBCOD'+n).val()); }
					if($('#SclDiameterOS'+n)){ $('#RgpDiameterOS'+n).val($('#RgpDiameterOD'+n).val()); }
				}else if(addType=='cust_rgp'){
					if($('#SclBcurveOS'+n)){ $('#RgpCustomBCOS'+n).val($('#RgpCustomBCOD'+n).val()); }
					if($('#SclDiameterOS'+n)){ $('#RgpCustomDiameterOS'+n).val($('#RgpCustomDiameterOD'+n).val()); }
				}
					
			}
		}
	}
}

function fillValues(addType, odos, rowNum)
{
	var newNum = parseInt(rowNum) + 1;
	if(addType == 'scl'){
		var ar = {
					scl_elemMake:'SclType',
					scl_elemBc:'SclBcurve',
					scl_elemDiameter:'SclDiameter',
					scl_elemSphere:'Sclsphere',
					scl_elemCylinder:'SclCylinder',
					scl_elemAxis:'Sclaxis',
					scl_elemColor:'SclColor',
					scl_elemAdd:'SclAdd',
					scl_elemDva:'SclDva',
					scl_elemNva:'SclNva',
				};
		
		
		/*
		if(odos =='od'){
			
				
				
			dgi('SclsphereOD'+newNum).value = dgi('SclsphereOD'+rowNum).value;
			dgi('SclCylinderOD'+newNum).value = dgi('SclCylinderOD'+rowNum).value;
			dgi('SclaxisOD'+newNum).value = dgi('SclaxisOD'+rowNum).value;
			dgi('SclBcurveOD'+newNum).value = dgi('SclBcurveOD'+rowNum).value;
			dgi('SclDiameterOD'+newNum).value = dgi('SclDiameterOD'+rowNum).value;
			dgi('SclAddOD'+newNum).value = dgi('SclAddOD'+rowNum).value;
			dgi('SclDvaOD'+newNum).value = dgi('SclDvaOD'+rowNum).value;
			dgi('SclNvaOD'+newNum).value = dgi('SclNvaOD'+rowNum).value;
			dgi('SclTypeOD'+newNum).value = dgi('SclTypeOD'+rowNum).value;
			dgi('SclTypeOD'+newNum+"ID").value = dgi('SclTypeOD'+rowNum+"ID").value;
			dgi('SclCylinderOD'+newNum).value = dgi('SclCylinderOD'+rowNum).value;
		}else
		if(odos =='os') {
			
			dgi('SclsphereOS'+newNum).value = dgi('SclsphereOS'+rowNum).value;
			dgi('SclCylinderOS'+newNum).value = dgi('SclCylinderOS'+rowNum).value;
			dgi('SclaxisOS'+newNum).value = dgi('SclaxisOS'+rowNum).value;
			dgi('SclBcurveOS'+newNum).value = dgi('SclBcurveOS'+rowNum).value;
			dgi('SclDiameterOS'+newNum).value = dgi('SclDiameterOS'+rowNum).value;
			dgi('SclAddOS'+newNum).value = dgi('SclAddOS'+rowNum).value;
			dgi('SclDvaOS'+newNum).value = dgi('SclDvaOS'+rowNum).value;
			dgi('SclNvaOS'+newNum).value = dgi('SclNvaOS'+rowNum).value;
			dgi('SclTypeOS'+newNum).value = dgi('SclTypeOS'+rowNum).value;
			dgi('SclTypeOS'+newNum+"ID").value = dgi('SclTypeOS'+rowNum+"ID").value;
			dgi('SclCylinderOS'+newNum).value = dgi('SclCylinderOS'+rowNum).value;
		}
		*/
	}
	
	if(addType == 'rgp') {
		var ar = {
			rgp_elemMake:'RgpType',
			rgp_elemBc:'RgpBC',
			rgp_elemCylinder:'RgpCylinder',
			rgp_elemDiameter:'RgpDiameter',	
			rgp_elemSphere:'RgpPower',
			rgp_elemAxis:'RgpAxis',
			rgp_elemColor:'RgpColor',
			rgp_elemOZ:'RgpOZ',
			rgp_elemCT:'RgpCT',
			rgp_elemAdd:'RgpAdd',
			rgp_elemDva:'RgpDva',
			rgp_elemNva:'RgpNva'	
		};
		
		/*
		if(odos == 'od')
		{
			dgi('RgpPowerOD'+newNum).value = dgi('RgpPowerOD'+rowNum).value;
			dgi('RgpBCOD'+newNum).value = dgi('RgpBCOD'+rowNum).value;
			dgi('RgpDiameterOD'+newNum).value = dgi('RgpDiameterOD'+rowNum).value;
			dgi('RgpOZOD'+newNum).value = dgi('RgpOZOD'+rowNum).value;
			//dgi('RgpCTOD'+newNum).value = dgi('RgpCTOD'+rowNum).value;
			dgi('RgpColorOD'+newNum).value = dgi('RgpColorOD'+rowNum).value;
			//dgi('RgpLatitudeOD'+newNum).value = dgi('RgpLatitudeOD'+rowNum).value;
			dgi('RgpAddOD'+newNum).value = dgi('RgpAddOD'+rowNum).value;
			dgi('RgpDvaOD'+newNum).value = dgi('RgpDvaOD'+rowNum).value;
			dgi('RgpNvaOD'+newNum).value = dgi('RgpNvaOD'+rowNum).value;
			dgi('RgpTypeOD'+newNum).value = dgi('RgpTypeOD'+rowNum).value;
			dgi('RgpTypeOD'+newNum+"ID").value = dgi('RgpTypeOD'+rowNum+"ID").value;
			dgi('RgpWarrantyOD'+newNum).value = dgi('RgpWarrantyOD'+rowNum).value;
			
		}else
		if(odos == 'os')
		{
			dgi('RgpPowerOS'+newNum).value = dgi('RgpPowerOS'+rowNum).value;
			dgi('RgpBCOS'+newNum).value = dgi('RgpBCOS'+rowNum).value;
			dgi('RgpDiameterOS'+newNum).value = dgi('RgpDiameterOS'+rowNum).value;
			dgi('RgpOZOS'+newNum).value = dgi('RgpOZOS'+rowNum).value;
			//dgi('RgpCTOS'+newNum).value = dgi('RgpCTOS'+rowNum).value;
			dgi('RgpColorOS'+newNum).value = dgi('RgpColorOS'+rowNum).value;
			//dgi('RgpLatitudeOS'+newNum).value = dgi('RgpLatitudeOS'+rowNum).value;
			dgi('RgpAddOS'+newNum).value = dgi('RgpAddOS'+rowNum).value;
			dgi('RgpDvaOS'+newNum).value = dgi('RgpDvaOS'+rowNum).value;
			dgi('RgpNvaOS'+newNum).value = dgi('RgpNvaOS'+rowNum).value;
			dgi('RgpTypeOS'+newNum).value = dgi('RgpTypeOS'+rowNum).value;
			dgi('RgpTypeOS'+newNum+"ID").value = dgi('RgpTypeOS'+rowNum+"ID").value;
			dgi('RgpWarrantyOS'+newNum).value = dgi('RgpWarrantyOS'+rowNum).value;
			
		}
		*/
	}

	if(addType == 'cust_rgp') {
		var ar = {
		cust_rgp_elemMake:'RgpCustomType',
			cust_rgp_elemBc:'RgpCustomBC',
			cust_rgp_elemDiameter:'RgpCustomDiameter',
			cust_rgp_elemPower:'RgpCustomPower',
			cust_rgp_elemCylinder:'RgpCustomCylinder',
			cust_rgp_elemAxis:'RgpCustomAxis',	
			cust_rgp_elemTwoDegree:'RgpCustom2degree',
			cust_rgp_elemThreeDegree:'RgpCustom3degree',
			cust_rgp_elemPCW:'RgpCustomPCW',
			cust_rgp_elemOZ:'RgpCustomOZ',
			cust_rgp_elemCT:'RgpCustomCT',
			cust_rgp_elemColor:'RgpCustomColor',
			cust_rgp_elemBlend:'RgpCustomBlend',
			cust_rgp_elemEdge:'RgpCustomEdge',
			cust_rgp_elemAdd:'RgpCustomAdd',
			cust_rgp_elemDva:'RgpCustomDva',
			cust_rgp_elemNva:'RgpCustomNva'
		};	
		
		/*
		if(odos == 'od')
		{
			dgi('RgpCustomPowerOD'+newNum).value = dgi('RgpCustomPowerOD'+rowNum).value;
			dgi('RgpCustomBCOD'+newNum).value = dgi('RgpCustomBCOD'+rowNum).value;
			dgi('RgpCustom2degreeOD'+newNum).value = dgi('RgpCustom2degreeOD'+rowNum).value;
			dgi('RgpCustom3degreeOD'+newNum).value = dgi('RgpCustom3degreeOD'+rowNum).value;
			dgi('RgpCustomPCWOD'+newNum).value = dgi('RgpCustomPCWOD'+rowNum).value;
			dgi('RgpCustomDiameterOD'+newNum).value = dgi('RgpCustomDiameterOD'+rowNum).value;
			dgi('RgpCustomOZOD'+newNum).value = dgi('RgpCustomOZOD'+rowNum).value;
			//dgi('RgpCustomCTOD'+newNum).value = dgi('RgpCustomCTOD'+rowNum).value;
			dgi('RgpCustomColorOD'+newNum).value = dgi('RgpCustomColorOD'+rowNum).value;
			dgi('RgpCustomBlendOD'+newNum).value = dgi('RgpCustomBlendOD'+rowNum).value;
			dgi('RgpCustomEdgeOD'+newNum).value = dgi('RgpCustomEdgeOD'+rowNum).value;
			//dgi('RgpCustomLatitudeOD'+newNum).value = dgi('RgpCustomLatitudeOD'+rowNum).value;
			dgi('RgpCustomAddOD'+newNum).value = dgi('RgpCustomAddOD'+rowNum).value;
			dgi('RgpCustomDvaOD'+newNum).value = dgi('RgpCustomDvaOD'+rowNum).value;
			dgi('RgpCustomNvaOD'+newNum).value = dgi('RgpCustomNvaOD'+rowNum).value;
			dgi('RgpCustomTypeOD'+newNum).value = dgi('RgpCustomTypeOD'+rowNum).value;
			dgi('RgpCustomTypeOD'+newNum+"ID").value = dgi('RgpCustomTypeOD'+rowNum+"ID").value;
			dgi('RgpCustomWarrantyOD'+newNum).value = dgi('RgpCustomWarrantyOD'+rowNum).value;
						
		}else
		if(odos == 'os')
		{
			dgi('RgpCustomPowerOS'+newNum).value = dgi('RgpCustomPowerOS'+rowNum).value;
			dgi('RgpCustomBCOS'+newNum).value = dgi('RgpCustomBCOS'+rowNum).value;
			dgi('RgpCustom2degreeOS'+newNum).value = dgi('RgpCustom2degreeOS'+rowNum).value;
			dgi('RgpCustom3degreeOS'+newNum).value = dgi('RgpCustom3degreeOS'+rowNum).value;
			dgi('RgpCustomPCWOS'+newNum).value = dgi('RgpCustomPCWOS'+rowNum).value;
			dgi('RgpCustomDiameterOS'+newNum).value = dgi('RgpCustomDiameterOS'+rowNum).value;
			dgi('RgpCustomOZOS'+newNum).value = dgi('RgpCustomOZOS'+rowNum).value;
			//dgi('RgpCustomCTOS'+newNum).value = dgi('RgpCustomCTOS'+rowNum).value;
			dgi('RgpCustomColorOS'+newNum).value = dgi('RgpCustomColorOS'+rowNum).value;
			dgi('RgpCustomBlendOS'+newNum).value = dgi('RgpCustomBlendOS'+rowNum).value;
			dgi('RgpCustomEdgeOS'+newNum).value = dgi('RgpCustomEdgeOS'+rowNum).value;
			//dgi('RgpCustomLatitudeOS'+newNum).value = dgi('RgpCustomLatitudeOS'+rowNum).value;
			dgi('RgpCustomAddOS'+newNum).value = dgi('RgpCustomAddOS'+rowNum).value;
			dgi('RgpCustomDvaOS'+newNum).value = dgi('RgpCustomDvaOS'+rowNum).value;
			dgi('RgpCustomNvaOS'+newNum).value = dgi('RgpCustomNvaOS'+rowNum).value;
			dgi('RgpCustomTypeOS'+newNum).value = dgi('RgpCustomTypeOS'+rowNum).value;
			dgi('RgpCustomTypeOS'+newNum+"ID").value = dgi('RgpCustomTypeOS'+rowNum+"ID").value;
			//dgi('RgpCustomWarrantyOS'+newNum).value = dgi('RgpCustomWarrantyOS'+rowNum).value;
		}
		*/
	}
	
	
	for(var a in ar){		
		var site = odos.toUpperCase();		
		var newf = ar[a]+site+newNum;
		var oldf = ar[a]+site+rowNum;
		
		$("#"+newf).val($("#"+oldf).val()).trigger("click");
		
		if(ar[a]=="SclType" || ar[a]=="RgpType" || ar[a]=="RgpCustomType"){
			var newf = newf + "ID";
			var oldf = oldf + "ID";
			$("#"+newf).val($("#"+oldf).val()).trigger("click");
		}
	}
}
function checkwnls(){}

function print_CL_Mr()
{
	var givenMrValue=dgi('mrVals').value;
	var pr=flg_printMr=0;
	var parWidth=parent.document.body.clientWidth;
	var parHeight=parent.document.body.clientHeight;
	winPrintMr=window.open('../main/print_patient_mr.php?printType=1&givenMr='+givenMrValue,'printPatientMr','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
}


	function generate_ajax_menus(eye_class){
		$('.'+eye_class).menu({
			content: $('.'+eye_class).next().html(),
			crumbDefaultText: ' '
		});	

		$.get('simpleMenuContent.php', function(data){
			$('.'+eye_class).each(function(){
				var elemID = $(this).attr('id');
				$('#'+elemID).keydown(function() {
				  $('#'+elemID+'ID').val('');
				});
				$(this).menu({ content: data, hierarchy: true});
			});
		});	
	}	

	function printRx(method,workSheetId){
		var parWidth = parent.document.body.clientWidth/2;
		var parHeight = parent.document.body.clientHeight/2;
		top.popup_win('print_patient_contact_lenses.php?printType=2&method='+method+'&workSheetId='+workSheetId,'printPatientContact','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
//		winPrintMr = window.open('../main/print_patient_contact_lenses.php?printType=2&method='+method+'&workSheetId='+workSheetId,'printPatientContact','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
	}

	function enbDisTrialNo(ctrlId,ctrlVal){
		if(ctrlVal=='Current Trial'){
			document.getElementById('clws_trial_number').disabled = false;
		}else{
			document.getElementById('clws_trial_number').disabled = true;
		}
	}
	
	function checkTrialNo(ctrlId){

	}

	function checkDOS(txtBox){
	//dgi('newSave').value='newSave';
/*			if(parseInt(gebi('chartNoteFORMID').value)>parseInt(gebi('worksheetFORMID').value)){
		var dd = confirm('Do you like to add new Contact Lens Worksheet?');
		if(dd){
			var wvdos = gebi('chartNoteDOS').value;
			var wvclws_id = gebi('clws_id').value;
			gebi(txtBox).blur();
			//window.open('contact_lens_new.php?loadLatest=1&wvdos='+wvdos+'&wvclws_id='+wvclws_id,'ContactLens','width=1200,height=650,resizable=Yes,scrollbars=yes');
			window.open('contact_lens_new.php?mode=newContactLensSheet','ContactLens','width=1200,height=650,resizable=Yes,scrollbars=yes');
		}
	}
*/	
	}
	
	
function copyAllCLValues(addType,fromEye,rowNum)
{
	var newNum = parseInt(rowNum) + 1;

	if(addType == 'scl') {	
		if(fromEye=='OD'){
			if(dgi('SclsphereOS'+rowNum)){
				dgi('SclsphereOS'+rowNum).value = dgi('SclsphereOD'+rowNum).value;
				dgi('SclCylinderOS'+rowNum).value = dgi('SclCylinderOD'+rowNum).value;
				dgi('SclaxisOS'+rowNum).value = dgi('SclaxisOD'+rowNum).value;
				dgi('SclBcurveOS'+rowNum).value = dgi('SclBcurveOD'+rowNum).value;
				dgi('SclDiameterOS'+rowNum).value = dgi('SclDiameterOD'+rowNum).value;
				dgi('SclAddOS'+rowNum).value = dgi('SclAddOD'+rowNum).value;
				dgi('SclDvaOS'+rowNum).value = dgi('SclDvaOD'+rowNum).value;
				dgi('SclNvaOS'+rowNum).value = dgi('SclNvaOD'+rowNum).value;
				dgi('SclTypeOS'+rowNum).value = dgi('SclTypeOD'+rowNum).value;
				dgi('SclTypeOS'+rowNum+"ID").value = dgi('SclTypeOD'+rowNum+"ID").value;
				dgi('SclCylinderOS'+rowNum).value = dgi('SclCylinderOD'+rowNum).value;
			}else{
				alert('No corresponding row exists.');
			}
		}
	}
	
	if(addType == 'rgp') {
		if(fromEye=='OD'){
			if(dgi('RgpPowerOS'+rowNum)){
				dgi('RgpPowerOS'+rowNum).value = dgi('RgpPowerOD'+rowNum).value;
				dgi('RgpBCOS'+rowNum).value = dgi('RgpBCOD'+rowNum).value;
				dgi('RgpDiameterOS'+rowNum).value = dgi('RgpDiameterOD'+rowNum).value;
				dgi('RgpOZOS'+rowNum).value = dgi('RgpOZOD'+rowNum).value;
				dgi('RgpCTOS'+rowNum).value = dgi('RgpCTOD'+rowNum).value;
				dgi('RgpColorOS'+rowNum).value = dgi('RgpColorOD'+rowNum).value;
				dgi('RgpLatitudeOS'+rowNum).value = dgi('RgpLatitudeOD'+rowNum).value;
				dgi('RgpAddOS'+rowNum).value = dgi('RgpAddOD'+rowNum).value;
				dgi('RgpDvaOS'+rowNum).value = dgi('RgpDvaOD'+rowNum).value;
				dgi('RgpNvaOS'+rowNum).value = dgi('RgpNvaOD'+rowNum).value;
				dgi('RgpTypeOS'+rowNum).value = dgi('RgpTypeOD'+rowNum).value;
				dgi('RgpTypeOS'+rowNum+"ID").value = dgi('RgpTypeOD'+rowNum+"ID").value;
				dgi('RgpWarrantyOS'+rowNum).value = dgi('RgpWarrantyOD'+rowNum).value;
			}else{
				alert('No Corresponding row exists.');
			}
		}
	}

	if(addType == 'cust_rgp') {
		if(fromEye=='OD'){
			if(dgi('RgpCustomPowerOS'+rowNum)){
				dgi('RgpCustomPowerOS'+rowNum).value = dgi('RgpCustomPowerOD'+rowNum).value;
				dgi('RgpCustomBCOS'+rowNum).value = dgi('RgpCustomBCOD'+rowNum).value;
				dgi('RgpCustom2degreeOS'+rowNum).value = dgi('RgpCustom2degreeOD'+rowNum).value;
				dgi('RgpCustom3degreeOS'+rowNum).value = dgi('RgpCustom3degreeOD'+rowNum).value;
				dgi('RgpCustomPCWOS'+rowNum).value = dgi('RgpCustomPCWOD'+rowNum).value;
				dgi('RgpCustomDiameterOS'+rowNum).value = dgi('RgpCustomDiameterOD'+rowNum).value;
				dgi('RgpCustomOZOS'+rowNum).value = dgi('RgpCustomOZOD'+rowNum).value;
				dgi('RgpCustomCTOS'+rowNum).value = dgi('RgpCustomCTOD'+rowNum).value;
				dgi('RgpCustomColorOS'+rowNum).value = dgi('RgpCustomColorOD'+rowNum).value;
				dgi('RgpCustomBlendOS'+rowNum).value = dgi('RgpCustomBlendOD'+rowNum).value;
				dgi('RgpCustomEdgeOS'+rowNum).value = dgi('RgpCustomEdgeOD'+rowNum).value;
				dgi('RgpCustomLatitudeOS'+rowNum).value = dgi('RgpCustomLatitudeOD'+rowNum).value;
				dgi('RgpCustomAddOS'+rowNum).value = dgi('RgpCustomAddOD'+rowNum).value;
				dgi('RgpCustomDvaOS'+rowNum).value = dgi('RgpCustomDvaOD'+rowNum).value;
				dgi('RgpCustomNvaOS'+rowNum).value = dgi('RgpCustomNvaOD'+rowNum).value;
				dgi('RgpCustomTypeOS'+rowNum).value = dgi('RgpCustomTypeOD'+rowNum).value;
				dgi('RgpCustomTypeOS'+rowNum+"ID").value = dgi('RgpCustomTypeOD'+rowNum+"ID").value;
				dgi('RgpCustomWarrantyOS'+rowNum).value = dgi('RgpCustomWarrantyOD'+rowNum).value;
			}else{
				alert('No Corresponding row exists.');
			}
		}
	}
}

function showHideCopyImgs(clGrp){

	var totRows = parseInt(dgi('txtTotOD').value) + parseInt(dgi('txtTotOS').value);
	if(clGrp=='ou'){
		$('[id="copyValsDiv"]').show();
	}else{
		$('[id="copyValsDiv"]').hide();
	}
}

//Jq Menu --
var jq_curMnuOb=null;
function jq_showMenu(ob){
var o = $(ob).offset();
var mtop = (parseInt(o.top))+"px";
$( "#menu_manuf" ).css({"left":o.left,"top":mtop,"display":"block"});
//$( "#menu_manuf ul" ).bind("mouseout",function(){setTimeout(jq_hideMenu(),2000);});
jq_curMnuOb=ob;

}    
var jq_hm_f=0;
function jq_hideMenu(){
	if(jq_hm_f==0){	
		jq_curMnuOb=null;
		$( "#menu_manuf" ).css({"display":"none"});      	
	}
}

function jq_hideMenu_can(v){jq_hm_f=v;}

function jq_menuclick(ob){
	
//alert(ob.innerText+" - "+jq_curMnuOb.name+" - "+jq_curMnuOb.id);
txtBoxID=jq_curMnuOb.id+"ID";	
	
$(jq_curMnuOb).val(ob.innerText);			
	
jq_aftermenuclick(ob,txtBoxID);
jq_hm_f=0;	
jq_hideMenu();	
	
}

function jq_aftermenuclick(item,txtBoxID){
	if($('#'+txtBoxID)){
		var mkID = $(item).attr('name');
		$('#'+txtBoxID).val(mkID);
	}

	if($('#'+txtBoxID).attr('funVars')){ 
		var funVals =$('#'+txtBoxID).attr('funVars');
		var fVals = funVals.split("~");
		
		// GET and SET B/C, Diameter
		if(dgi('ctrlListVals')){
			var ctrlListVals = $('#ctrlListVals').val();
			var txtBoxBC = fVals[3];
			var txtBoxDiam = fVals[4];		
			
			if(ctrlListVals!='' && dgi(txtBoxBC) && dgi(txtBoxDiam)){
				var parts='';
				var tempArr = ctrlListVals.split(',');
				var fBCArr= new Array();
				var fDiamArr= new Array();
				for(t=0; t<tempArr.length; t++){
					parts = tempArr[t].split('$');
					fBCArr[parts[0]] = parts[1];
					fDiamArr[parts[0]] = parts[2];
				}
				$('#'+txtBoxBC).val(fBCArr[mkID]);
				$('#'+txtBoxDiam).val(fDiamArr[mkID]);

				var txtBoxBC_OS = txtBoxBC.replace("OD","OS");
				var txtBoxDiam_OS = txtBoxDiam.replace("OD","OS");
				if($('#'+txtBoxBC_OS)){ $('#'+txtBoxBC_OS).val(fBCArr[mkID]); }
				if($('#'+txtBoxDiam_OS)){ $('#'+txtBoxDiam_OS).val(fDiamArr[mkID]); }
			}
		}
		
		if(fVals[0]=='printOrder'){
			setPrice(fVals[1], fVals[2], $('#'+txtBoxID).val());
		}
		if(fVals[0]=='worksheet'){
			if($('#'+fVals[2])){
				copyValuesODToOS(fVals[1], fVals[2]);
			}
		}
	}	
}

//Jq Menu --

//r6-qa
function showSave(command){
		dgi('buttonSavediv').style.display='inline-table';
		if(command=="close"){
		dgi('buttonSavediv').style.display='none';
		}
   }
   function Contactlensformsubmit(){
	   //alert("In work_view");
		var lengthArray=document.getElementsByName('clws_type').length;
		var iCounter=0;
		var cTrial='no';
		var SubMitForm=false;
		for(iCounter=0;iCounter<lengthArray;iCounter++){
			if(document.getElementsByName('clws_type')[iCounter].checked==true){
				SubMitForm=true;

				if(document.getElementsByName('clws_type')[iCounter].value=='Current Trial'){
					cTrial = 'yes';
				}
			}
		}
		if(document.getElementById('otherSave').value!=''){	SubMitForm=true;}
		//alert("Other save: " + $("#otherSave").val());

		if(cTrial=='yes' && document.getElementById('clws_trial_number').value==''){
			SubmitForm=false;
			alert('Please fill trial no value.');
		}
		
		if(SubMitForm==false){
			alert("Please select Evaluation/Trial option.");
			return false;
		}else{
			//dgi("frmContactlensNew").submit();
			var selClwsVals = $('#clws_types').val();
			document.getElementById('CLName').innerHTML = selClwsVals;
			//$('#newSave').val('1');
			//$('#clws_id').val('');
			top.$(':input[name=save][type=button]').trigger('click');
			showSave('close')
		}
}
function checkSingle(elemId, elemPrice, grpName, callFor)
{
	var selTypes = '';
	var charges_id = '';
	var checked=0;
	var noOfChkd= totPrice = 0;
	if(callFor=='sheet'){
		var obgrp = document.getElementsByName(grpName);
		var objele = dgi(elemId);
		var len = obgrp.length;
		if(dgi('cpt_evaluation_fit_refit').value<=0 || dgi('cpt_evaluation_fit_refit').value=='') { dgi('cpt_evaluation_fit_refit').value=0;}
	
		if(objele.checked == true)
		{	
			dgi('cpt_evaluation_fit_refit').value = (parseInt(dgi('cpt_evaluation_fit_refit').value) + parseInt(elemPrice))+'.00';
		}else if(objele.checked == false){
			dgi('cpt_evaluation_fit_refit').value = parseInt(dgi('cpt_evaluation_fit_refit').value) - parseInt(elemPrice)+'.00';
		}

		// GET CHARGE IDS
		for(var i=1;i<=4;i++)
		{
			if(document.getElementById('chargesChk'+i).checked)
			{ 
				noOfChkd+=1;
				totPrice+= parseInt($('#chargesChk'+i).attr('clPrice'));
			}
		}

		if(noOfChkd>1){
			$('#cpt_evaluation_fit_refit').val(totPrice+'.00');
			$('#cpt_evaluation_fit_refit').attr('readonly', true);
		}else{
			$('#cpt_evaluation_fit_refit').attr('readonly', false);
		}
	}
	if(callFor=='popup'){
		var obgrp = $('#buttonSavediv input:checkbox[name^="'+grpName+'"]');
		var objDiv = $('#buttonSavediv #'+elemId); // dgi(elemId);
		var len = obgrp.length;

		var obgrp1 = document.getElementsByName(grpName);

		for(var i=0;i<len;i++)
		{
			if(obgrp[i].checked == true)
			{
				selTypes+= obgrp[i].value+',';
				checked=1;
			}
		}
		separator='';
		//dgi('clws_types').value	 = trim(selTypes.substr(0, (selTypes.length)-1));
		dgi('clws_types').value	 = selTypes.substr(0, (selTypes.length)-1);
		if(checked==1){ separator=',';}
		if(dgi('otherSave').value!=''){
			dgi('clws_types').value+= separator+dgi('otherSave').value;	
		}		
	}
}
function loadCopyFromWorkSheet(strcopysheetId){
	if(strcopysheetId>0){
		var prvdos = dgi('prvdos').value;
		//window.location.href="cl_block.php?copySheetId="+strcopysheetId+"&md=copy&prvdos="+prvdos;
		$.ajax({
			url: "cl_block.php?copySheetId="+strcopysheetId,
			success: function(resp){
				if(resp) {
					$('#ContactLens').html(resp);
					//User Type Color: Color coding ------------
/*					var arrElems=[];
					var elem=elemString='';
					oldVals=document.getElementById('elem_utElems').value;
					curVals=document.getElementById('elem_utElems_cur').value;
					arrElems=curVals.split(',');
					for(x in arrElems){
						elem=arrElems[x];
						if(oldVals.indexOf(elem)=='-1'){
							elemString+=elem+',';
						}
					}
					if(elemString!=''){
						document.getElementById('elem_utElems').value=oldVals+'|1@'+elemString+'|';
					}*/
					
					utElem_setBgColor();
				}
			}
		});	
	}
}
function enbDisTrialNo1(ctrlId){
	if(dgi(ctrlId).checked==true){
		document.getElementById('clws_trial_number').disabled = false;
	}else{
		document.getElementById('clws_trial_number').disabled = true;
	}
	
}

//
//<script>
			var arrManufac=[]; 
			var arrManufacId=[]; 
			var arrManufacInfo=[]; 
			
  		   function showSave(command){
				dgi('buttonSavediv').style.display='inline-table';
				if(command=="close"){
				dgi('buttonSavediv').style.display='none';
				}
		   }
		   function Contactlensformsubmit(){
			   //alert("In wv 1337");
				var lengthArray=document.getElementsByName('clws_type').length;
				var iCounter=0;
				var SubMitForm=false;
				for(iCounter=0;iCounter<lengthArray;iCounter++){
					if(document.getElementsByName('clws_type')[iCounter].checked==true){
						SubMitForm=true;
					}
				}
				if(document.getElementById('otherSave').value!=''){	SubMitForm=true;}
				if(SubMitForm==false){
					alert("Please select Evaluation/Trial option.");
					return false;
				}else{
					//dgi("frmContactlensNew").submit();
					var selClwsVals = $('#clws_types').val();
					if($("#CLName").innerHTML != null && $("#CLName").innerHTML != undefined){
						document.getElementById('CLName').innerHTML = selClwsVals;
					}
					//$('#newSave').val('1');
					//$('#clws_id').val('');
					cl_save_wv();
					showSave('close');					
				}
	    }
		function checkSingle(elemId, elemPrice, grpName, callFor)
		{
			var selTypes = '';
			var charges_id = '';
			var checked=0;
			var noOfChkd= totPrice = 0;
			if(callFor=='sheet'){
				var obgrp = document.getElementsByName(grpName);
				var objele = dgi(elemId);
				var len = obgrp.length;
				if(dgi('cpt_evaluation_fit_refit').value<=0 || dgi('cpt_evaluation_fit_refit').value=='') { dgi('cpt_evaluation_fit_refit').value=0;}
			
				if(objele.checked == true)
				{	
					dgi('cpt_evaluation_fit_refit').value = (parseInt(dgi('cpt_evaluation_fit_refit').value) + parseInt(elemPrice))+'.00';
				}else if(objele.checked == false){
					dgi('cpt_evaluation_fit_refit').value = parseInt(dgi('cpt_evaluation_fit_refit').value) - parseInt(elemPrice)+'.00';
				}

				// GET CHARGE IDS
				for(var i=1;i<=4;i++)
				{
					if(document.getElementById('chargesChk'+i).checked)
					{ 
						noOfChkd+=1;
						totPrice+= parseInt($('#chargesChk'+i).attr('clPrice'));
					}
				}
		
				if(noOfChkd>1){
					$('#cpt_evaluation_fit_refit').val(totPrice+'.00');
					$('#cpt_evaluation_fit_refit').attr('readonly', true);
				}else{
					$('#cpt_evaluation_fit_refit').attr('readonly', false);
				}
			}
			if(callFor=='popup'){
				var obgrp = $('#buttonSavediv input:checkbox[name^="'+grpName+'"]');
				var objDiv = $('#buttonSavediv #'+elemId); // dgi(elemId);
				var len = obgrp.length;
		
				var obgrp1 = document.getElementsByName(grpName);
		
				for(var i=0;i<len;i++)
				{
					if(obgrp[i].checked == true)
					{
						selTypes+= obgrp[i].value+',';
						checked=1;
					}
				}
				separator='';
				//dgi('clws_types').value	 = trim(selTypes.substr(0, (selTypes.length)-1));
				dgi('clws_types').value	 = selTypes.substr(0, (selTypes.length)-1);
				if(checked==1){ separator=',';}
				if(dgi('otherSave').value!=''){
					dgi('clws_types').value+= separator+dgi('otherSave').value;	
				}		
			}
		}
		function loadCopyFromWorkSheet(strcopysheetId, elemChanged, copied_to_clws_id, callFrom){
			if(typeof(callFrom)=="undefined"){ callFrom=""; }
			if(strcopysheetId>0 || callFrom=='popup'){
				var url_str='';	
				var prvdos = dgi('prvdos').value;
				if (typeof copied_to_clws_id!= 'string'){copied_to_clws_id = '';}

				if(callFrom=='popup'){
					url_str='cl_block.php'; //IT WILL ALWAYS LOAD LATEST CREATED WORKSHEET. 
				}else{
					url_str='cl_block.php?copySheetId='+strcopysheetId+'&copied_to_clws_id='+copied_to_clws_id;
				}
				
				//window.location.href="cl_block.php?copySheetId="+strcopysheetId+"&md=copy&prvdos="+prvdos;
				$.ajax({
					url: url_str,
					success: function(resp){
						if(resp) {
							$('#ContactLens').html(resp);
							/*var arrElems=[];
							var elem=elemString='';
							oldVals=document.getElementById('elem_utElems').value;
							curVals=document.getElementById('elem_utElems_cur').value;
							arrElems=curVals.split(',');
							for(x in arrElems){
								elem=arrElems[x];
								if(oldVals.indexOf(elem)=='-1'){
									elemString+=elem+',';
								}
							}
							if(elemString!=''){
								document.getElementById('elem_utElems').value=oldVals+'|1@'+elemString+'|';
							}
							*/
							applyTypeAhead();
							utElem_setBgColor();							
							curVals=document.getElementById('elem_utElems_cur').value;
							arrElems=curVals.split(',');
							//*
							for(x in arrElems){
								//--
								elem=arrElems[x];								
								if(elem!=""){set_vis_el_active(document.getElementById(elem));	utElem_capture(document.getElementById(elem));} //this will work for curval								
								//--
							}
							//*/	
							for(x in elemChanged){
								//--
								elem=x;							
								if(elem!=""){set_vis_el_active(document.getElementById(elem));	utElem_capture(document.getElementById(elem));} //this will work for curval								
								//--
							}

							if(callFrom==''){
								$('div[id^="CLRowOD"] input').each(function(){
									$(this).trigger('click');
								});
								$('div[id^="CLRowOS"] input').each(function(){
									$(this).trigger('click');
								});								
							}
						}
					}
				});	
			}else{
				$("#clws_id").val("0");
			}
			/*$(".typeAhead").each(function(i, obj){
				var autocomplete = $("#" + $(this).attr('id')).typeahead();
		    	autocomplete.data('typeahead').source = arrManufac;
		    	autocomplete.data('typeahead').updater = function(item)
		    	{
		    		return item;
		    	}
			});*/
			
			
			//alert("Type ahead added");
		}
		function enbDisTrialNo1(ctrlId){
			if(dgi(ctrlId).checked==true){
				document.getElementById('clws_trial_number').disabled = false;
			}else{
				document.getElementById('clws_trial_number').disabled = true;
			}
			
		}
		 //  </script> 
		
var cl_ajax_type_ahd_obj = function(o){
			$(o).autocomplete({
			    source: "cl_block.php?elem_formAction=TypeAhead",
			    minLength: 1,
			    autoFocus: true,	
			    focus: function( event, ui ) {return false;},		
			    select: function( event, ui ) {  if(ui.item.value!=""){
					this.value=""+ui.item.value; 
				    
					id=$(this).attr('id');
					addType='';
					if(id.indexOf('Scl')!='-1')addType='scl';else if(id.indexOf('RgpType')!='-1')addType='rgp';else if(id.indexOf('RgpCustom')!='-1')addType='cust_rgp';
					if(ui.item.id && typeof(ui.item.id)!="undefined"){
						$('#'+id+'ID').val(""+ui.item.id);
						//--
						if(ui.item.info && typeof(ui.item.info)!="undefined"){
						if(document.getElementById(id+'ID')){
						var odos=(id.indexOf('OD')=='-1') ? 'OS' : 'OD';							
						arrN=id.split(odos);
						n=arrN[1];
						if(n){
							makeId=$('#'+id+'ID').val();
							if(makeId>0){
								//if(typeof arrManufacInfo[makeId]!= "undefined"){
									var tmp = ""+ui.item.info; 
									arrVals= tmp.split('~');
									if(addType=='scl'){
										if(arrVals[0]){ $('#SclBcurve'+odos+n).val(arrVals[0]).trigger("click"); }
										if(arrVals[1]){ $('#SclDiameter'+odos+n).val(arrVals[1]).trigger("click"); }
									}else if(addType=='rgp'){
										if(arrVals[0]){ $('#RgpBC'+odos+n).val(arrVals[0]).trigger("click"); }
										if(arrVals[1]){ $('#RgpDiameter'+odos+n).val(arrVals[1]).trigger("click"); }
									}else if(addType=='cust_rgp'){
										if(arrVals[0]){ $('#RgpCustomBC'+odos+n).val(arrVals[0]).trigger("click"); }
										if(arrVals[1]){ $('#RgpCustomDiameter'+odos+n).val(arrVals[1]).trigger("click"); }
									}
								//}
							}
							//COPY TO OS
							if(odos=='OD'){
								osId=id.replace('OD','OS');
								if($('#'+osId)){ $('#'+osId).val($('#'+id).val()).trigger("click"); }
								if($('#'+osId+'ID')){ $('#'+osId+'ID').val($('#'+id+'ID').val()); }
								if(addType=='scl'){
									if($('#SclBcurveOS'+n)){ $('#SclBcurveOS'+n).val($('#SclBcurveOD'+n).val()).trigger("click"); }
									if($('#SclDiameterOS'+n)){ $('#SclDiameterOS'+n).val($('#SclDiameterOD'+n).val()).trigger("click"); }
								}else if(addType=='rgp'){
									if($('#SclBcurveOS'+n)){ $('#RgpBCOS'+n).val($('#RgpBCOD'+n).val()).trigger("click"); }
									if($('#SclDiameterOS'+n)){ $('#RgpDiameterOS'+n).val($('#RgpDiameterOD'+n).val()).trigger("click"); }
								}else if(addType=='cust_rgp'){
									if($('#SclBcurveOS'+n)){ $('#RgpCustomBCOS'+n).val($('#RgpCustomBCOD'+n).val()).trigger("click"); }
									if($('#SclDiameterOS'+n)){ $('#RgpCustomDiameterOS'+n).val($('#RgpCustomDiameterOD'+n).val()).trigger("click"); }
								}	
							}
						}
						}
						}
						//--
					}					
				    }					
				}
			});	
		};

function cl_ajax_type_ahd_all(){	
	$('.typeAhead').bind("focus", function(){
		if(!$(this).hasClass("ui-autocomplete-input")){
			cl_ajax_type_ahd_obj(this);
		};
	});
}
function cl_save_wv(){top.fmain.zSaveWithoutPopupSave=1;top.$(':input[name=save][type=button]').trigger('click');}

function applyTypeAhead(){
	$(".typeAhead").each(function(i, obj){
		var odOrOS = "";
    	var tmpId = $(this).attr('id');
    	var rowCount = parseInt(tmpId.match(/\d+/)[0], 10);
    	var autocomplete = $("#" + tmpId).typeahead({items:arrManufac.length});
    	autocomplete.data('typeahead').source = arrManufac;
    	autocomplete.data('typeahead').updater = function(item)
    	{
    		for(var j=0;j<arrManufac.length;j++)
    		{
    			if(item == arrManufac[j])
    			{
    				if(tmpId.indexOf("OD") !== -1){			// Check whether row belongs to OD or OS
    					odOrOS = "OD";
    				}else if(tmpId.indexOf("OS") !== -1){
    					odOrOS = "OS";
    				}
    				var lensMakeId = arrManufacId[j];				// Get contact lens make id
    				$("#SclType" + odOrOS + rowCount + "ID").val(lensMakeId);
    				$("#clDetId" + odOrOS + rowCount).val(lensMakeId);
    				var curve = arrManufacInfo[arrManufacId[j]].split('~')[0];			// Get lens curve			
    				var diameter = arrManufacInfo[arrManufacId[j]].split('~')[1];		// Get lens diameter
    				
    				if($("#clType" + odOrOS + rowCount).val() == "scl" || $("#clType" + odOrOS + rowCount).val() == "prosthesis" || $("#clType" + odOrOS + rowCount).val() == "no-cl"){
    					$("#SclBcurve" + odOrOS + rowCount).val(curve);					// Update curve for selected lens
	    				$("#SclDiameter" + odOrOS + rowCount).val(diameter);			// Update diameter for selected lens
					}
    				if($("#clType" + odOrOS + rowCount).val() == "rgp"){
    					$("#RgpType" + odOrOS + rowCount + "ID").val(lensMakeId);
    					$("#RgpBC" + odOrOS + rowCount).val(curve);					// Update curve for selected lens
	    				$("#RgpDiameter" + odOrOS + rowCount).val(diameter);		// Update diameter for selected lens
	    				//alert($("#RgpType" + odOrOS + rowCount + "ID").val());
					}
    				if($("#clType" + odOrOS + rowCount).val() == "cust_rgp"){
    					$("#RgpCustomType" + odOrOS + rowCount + "ID").val(lensMakeId);
    					$("#RgpCustomBC" + odOrOS + rowCount).val(curve);					// Update curve for selected lens
	    				$("#RgpCustomDiameter" + odOrOS + rowCount).val(diameter);			// Update diameter for selected lens
					}
    				if(odOrOS == "OD"){			// If first OD's lens type is selected
        				// Copy same to first OS
    					if($("#clType" + odOrOS + rowCount).val() == "scl" || $("#clType" + odOrOS + rowCount).val() == "prosthesis" || $("#clType" + odOrOS + rowCount).val() == "no-cl"){
    						$("#SclBcurveOS" + rowCount).val(curve);					// Update curve for selected lens
    	    				$("#SclDiameterOS" + rowCount).val(diameter);
    	    				$("#SclTypeOS" + rowCount + "ID").val(lensMakeId);
    	    				$("#clDetIdOS" + rowCount).val(lensMakeId);
    	    				$("#SclTypeOS" + rowCount).val(item);
    					}
    					if($("#clType" + odOrOS + rowCount).val() == "rgp"){
    						$("#RgpBCOS" + rowCount).val(curve);					// Update curve for selected lens
    	    				$("#RgpDiameterOS" + rowCount).val(diameter);			// Update diameter for selected lens
    	    				$("#RgpTypeOS" + rowCount + "ID").val(lensMakeId);
    	    				$("#RgpTypeOS" + rowCount).val(item);
    					}
    					if($("#clType" + odOrOS + rowCount).val() == "cust_rgp"){
    						$("#RgpCustomBCOS" + rowCount).val(curve);					// Update curve for selected lens
    	    				$("#RgpCustomDiameterOS" + rowCount).val(diameter);			// Update diameter for selected lens
    	    				$("#RgpCustomTypeOS" + rowCount + "ID").val(lensMakeId);
    	    				$("#RgpCustomTypeOS" + rowCount).val(item);
    					}
    				}
    				return item;
    			}
    		}
    	}
    });
}