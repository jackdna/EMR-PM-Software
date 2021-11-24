//rvs.js
var curTabRvs="Vision_Problem";
function displayTabRvs(id)
{
	if(curTabRvs != id)
	{
		$("#"+id).show();
		$("#tab"+id).addClass("rvsOn");
		$("#"+curTabRvs).hide();
		$("#tab"+curTabRvs).removeClass("rvsOn");
		curTabRvs=id;
		$("#div_rvs").css({'width':'', 'height':''});		
	}
	stopClickBubble();
}

function unselectSelect(theObj){
	//Shift to
	rv_unselectSelect(theObj);
	return;	
}

function noFloatersFn(theObj){
	//Shift
	rv_noFloatersFn(theObj);
	return;
	//Shift	
}
function noFlashFn(theObj){
	
	//Shift to
	rvfun_noFlashFn(theObj);
	return;
	//Shift to	
}
function itchYesNoFn(theObj){
	
	//Shift to 
		rvsfun_itchYesNoFn(theObj);
		return
	//Shift to 	
	
}

function chkDateFrmat(obj)
{
	var retVal = false;
	if(checkDateCorrectness(obj) == true)
	{		
		retVal = true;
	}
	return retVal;
}

function rvs_hasDtDesc(obj){
	
	var str_com_head ="";
	if(complaintHead.length>0){
		var str_com_head = complaintHead.join(",");
		str_com_head =$.trim(str_com_head)+",";
	}
	
	if(str_com_head != ""){		
		if(str_com_head.indexOf(obj.id)!=-1){
			return true;	
		}		
	}
	
	return false;
}


//v1 ---

var rvs_em = ".\r";
// VP ------
function rvfun_getRVS_VP(rvs_sm){
	
	var oe_dis = document.getElementsByName("elem_vpDis[]");
	var oe_dis_othr = gebi("elem_vpDisOther");	
	var oe_mddis = document.getElementsByName("elem_vpMidDis[]");
	var oe_mddis_othr = gebi("elem_vpMidDisOther");	
	var oe_nr = document.getElementsByName("elem_vpNear[]");
	var oe_nr_othr = gebi("elem_vpNearOther");
	
	var str = "";
	var arr = new Array(oe_dis,oe_mddis,oe_nr);
	var arr_othr = new Array(oe_dis_othr,oe_mddis_othr,oe_nr_othr);

	for(var i=0;i<3;i++){
		var oT = arr[i];
		var ln = oT.length;
		for(var j=0;j<ln;j++){
			if((oT[j].type == "checkbox") && (oT[j].checked == true)){
				
				//
				if(rvs_hasDtDesc(oT[j])){continue;}				
				
				str += (str != "") ? ", " : "";				
				if(oT[j].value != "Other"){
					str += oT[j].value;
				}else{
					var oOthr = arr_othr[i];
					if(oOthr && (oOthr.value != "")){	
						//alert(oOthr.id+"\n"+oOthr.value+"\n"+rvs_check4Dot(oOthr.value))
						str += rvs_check4Dot(oOthr.value);
					}
				}				
			}
		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}

	return str;
}

function rvfun_setRVS_VP(){
	var rvs_sm_vp = "\r\n-Vision Problem: Difficulty in ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_VP(rvs_sm_vp);		
	var obj = rvfun_checkCcHx(rvs_sm_vp);
	rvfun_setCcHx(str,obj);	
}
// VP ------

// GP ------
function rvfun_getRVS_GP(rvs_sm){	
	var oe_gp = document.getElementsByName("elem_vpGlare[]");
	var str = "";
	
	var oT = oe_gp;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){

			//
			if(rvs_hasDtDesc(oT[j])){continue;}
			
			str += (str != "") ? ", " : "";			
			str += oT[j].value;
		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;
}

function rvfun_setRVS_GP(){
	var rvs_sm_gp = "\r\n-Glare Problem: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_GP(rvs_sm_gp);		
	var obj = rvfun_checkCcHx(rvs_sm_gp);
	rvfun_setCcHx(str,obj);
}
// GP ------

//Other VP ------
function rvfun_getRVS_OVP(rvs_sm){
	var oe = document.getElementsByName("elem_vpOther[]");
	var oe_othr = gebi("elem_vpOtherOther");	
	var str = "";
	
	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){

			//
			if(rvs_hasDtDesc(oT[j])){continue;}
			
			str += (str != "") ? ", " : "";			

			if(oT[j].value != "Other"){
					str += oT[j].value;
			}else{
				var oOthr = oe_othr;
				if(oOthr && (oOthr.value != "")){

					str += rvs_check4Dot(oOthr.value);
				}
			}
		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;
}

function rvfun_setRVS_OVP(){
	var rvs_sm = "\r\n-Other Vision Problem: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_OVP(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}
//Other VP ------

// Pt Comments ----

function rvfun_getRVS_PtCom(rvs_sm){
	var oe = document.getElementsByName("vpComment");
	var str = "";
	
	var oT = oe;
	var ln = oT.length;	
	for(var j=0;j<ln;j++){		
		if((oT[j].type == "textarea") && (oT[j].value != "")){			
			str += rvs_check4Dot(oT[j].value);
		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;	
}

function rvfun_setRVS_PtCom(){
	var rvs_sm = "\r\n-Patient Comments: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_PtCom(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);	
	rvfun_setCcHx(str,obj);
}

// Pt Comments ----

// Irritation Lids ----
function rvfun_getRVS_IrrLids(rvs_sm){
	var oe = document.getElementsByName("elem_irrLidsExt[]");
	var oe_othr = gebi("elem_irrLidsExtOther");	
	var oe_no = gebi("itchingLisNo");
	var oe_yes = gebi("itchingLisYes");
	var str = "";

	if(oe_no && oe_no.checked == true){ // No Lids		
		str = oe_no.value;		
		
	}else{
		
		if(oe_yes && oe_yes.checked == true){ // Yes Lids		
			if(rvs_hasDtDesc(oe_yes)){}else{str = "External lid irritation";} //oe_yes.value;
		}
		
		var oT = oe;
		var ln = oT.length;
		for(var j=0;j<ln;j++){
			if((oT[j].type == "checkbox") && (oT[j].checked == true)){	

				//
				if(rvs_hasDtDesc(oT[j])){continue;}
				
				str += (str != "") ? ", " : "";			

				if(oT[j].value != "Other"){
						str += oT[j].value;
				}else{
					var oOthr = oe_othr;
					if(oOthr && (oOthr.value != "")){						
						
						str += rvs_check4Dot(oOthr.value);
					}
				}
			}
		}

	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;
	
}

function rvfun_setRVS_IrrLids(){
	var rvs_sm = "\r\n-Irritation Lids: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_IrrLids(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}

function rvsfun_itchYesNoFn(obj){
	
	//set Yes No lidsItchingYesNo
	var oe = document.getElementsByName("lidsItchingYesNo");
	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){	
		if((obj.checked == true) && (obj.id != oT[j].id)){
			oT[j].checked = false;			
			$("label[for="+oT[j].id+"]").css({"border":"2px solid transparent"});
			if(rvs_isDescribed(oT[j].id)){	clearRvsOne(oT[j].id, oT[j].id);	}		
		}
	}	
	//
	
	//clear all in cchx
	if(obj.checked == true){
		var tid=obj.id;
		obj.checked = false;
		rvfun_setRVS_v1(obj,'-Irritation Lids');
		obj.checked = true;
	}	
	//	
	
	//Set All Other Elements
	var oe = document.getElementsByName("elem_irrLidsExt[]");
	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){	
		if((obj.checked == true)){
			
			if(obj.id == "itchingLisNo"){
				//Uncheck and Disable All Elements				
				oT[j].checked = false;
				oT[j].disabled = true;
				$("label[for="+oT[j].id+"]").css({"border":"2px solid transparent"});
				
				//Hide Other Text box
				if((oT[j].value == "Other") && (typeof oT[j].onclick == "function")){
					oT[j].onclick();	
				}

			}else if(obj.id == "itchingLisYes"){
				//Enable All Elements				
				oT[j].disabled = false;			

			}

		}else{

			if(obj.id == "itchingLisNo"){
				//Enable All Elements
				oT[j].disabled = false;
			}
		}
	}

	//Set All Values
	if(obj.id == "itchingLisNo"){ //||obj.checked == false
		rvfun_setRVS_v1(obj,'-Irritation Lids');		
	}else if(obj.id == "itchingLisYes"){ // || obj.checked == true
		setRVS(obj,'-Irritation Lids');		
	}
}

// Irritation Lids ----

// Irritation Ocular ----
function rvfun_getRVS_IrrOcu(rvs_sm){
	var oe = document.getElementsByName("elem_irrOcu[]");
	var oe_othr = gebi("elem_irrOcuOther");	
	var str = "";

	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){	

			//
			if(rvs_hasDtDesc(oT[j])){continue;}
			
			str += (str != "") ? ", " : "";	
			
			if(oT[j].value == "Itching"){ // Itching
				var tmp = oT[j].value;
				//
				var oe_tp = document.getElementsByName("elem_irrOcuItchingType");				
				var ln_tp = oe_tp.length;				
				for(var k=0;k<ln_tp;k++){					
					if(oe_tp[k].checked == true){
						tmp = "" + oe_tp[k].value;
					}
				}

				//Add in str
				str += tmp;

			}else if(oT[j].value == "Pressure sensation"){ //Pressure sensation
				var tmp = oT[j].value;
				var oe_tp = document.getElementsByName("elem_irrOcuPresSensType");
				var ln_tp = oe_tp.length;
				for(var k=0;k<ln_tp;k++){						
					if(oe_tp[k].checked == true){
						tmp += " " + oe_tp[k].value;
					}
				}

				//Add in str
				str += tmp;

			}else if(oT[j].value != "Other"){
				str += oT[j].value;
			}else{
				var oOthr = oe_othr;
				if(oOthr && (oOthr.value != "")){					
					
					str += rvs_check4Dot(oOthr.value);
				}
			}

		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;
}

function rvfun_setRVS_IrrOcu(){
	var rvs_sm = "\r\n-Irritation Ocular: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_IrrOcu(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}
// Irritation Ocular ----

// Post Segment ----
function rvfun_getRVS_PS(rvs_sm){
	var oe = document.getElementsByName("elem_postSegSpots");
	var str = "";
	
	var oT = oe;
	var ln = oT.length;	
	for(var j=0;j<ln;j++){		
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){
			//
			if(rvs_hasDtDesc(oT[j])){continue;}
			
			str += oT[j].value;
		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;
	
}

function rvfun_setRVS_PS(){
	var rvs_sm = "\r\n-Post Segment: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_PS(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}
// Post Segment ----

// Flashing Lights ----
function rvfun_getRVS_FL(rvs_sm){
	var oe = document.getElementsByName("elem_postSegFL[]");
	var str = "";

	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		//alert(""+oT[j].value);
		
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){			
			
			//
			if(rvs_hasDtDesc(oT[j])){continue;}
			
			str += (str != "") ? ", " : "";	
			
			if(oT[j].value == "No Flashing Lights"){ //No Flashing Lights		
				str = oT[j].value;
				break;
					
			}else if(oT[j].value == "Sparks"){ // Sparks
				var tmp = oT[j].value;
				//
				var oe_tp = gebi("elem_postSegFLSparks");
				if(oe_tp && (oe_tp.value != "")){
					tmp = rvs_check4Dot(oe_tp.value);
				}
				//Add in str
				str += tmp;

			}else if(oT[j].value == "Lightning Bolts"){ //Lightening Bolts
				var tmp = oT[j].value;
				var oe_tp = gebi("elem_postSegFLBolts");
				if(oe_tp && (oe_tp.value != "")){
					tmp = rvs_check4Dot(oe_tp.value);
				}
				//Add in str
				str += tmp;

			}else if(oT[j].value == "Arcs lasting seconds"){ //Arcs lasting seconds
				var tmp = oT[j].value;
				var oe_tp = gebi("elem_postSegFLArcs");
				if(oe_tp && (oe_tp.value != "")){
					tmp = rvs_check4Dot(oe_tp.value);
				}
				//Add in str
				str += tmp;

			}/*else if(oT[j].value == "Strobe lights many minutes or longer"){ //Strobe lights many minutes or longer
				var tmp = oT[j].value;
				var oe_tp = gebi("elem_postSegFLStrobe");
				if(oe_tp && (oe_tp.value != "")){
					tmp = oe_tp.value;
				}
				//Add in str
				str += tmp;

			}*/else{				
				//
				str += oT[j].value;
			}
		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;
}

function rvfun_setRVS_FL(){
	var rvs_sm = "\r\n-Flashing Lights: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_FL(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}

function rvfun_noFlashFn(theObj){
	var oe = document.getElementsByName("elem_postSegFL[]");
	var str = "";

	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		
		if(oT[j].value != "No Flashing Lights"){
			if(theObj.checked == true){
				oT[j].checked = false;
				oT[j].disabled = true;
				//$("label[for="+oT[j].id+"]").css({"border":"2px solid transparent"});
				utElem_capture(oT[j]);
				//
				//
				if(rvs_isDescribed(oT[j].id)){
					clearRvsOne(oT[j].id, oT[j].id);
				}			
				
				//onclick
				/*
				if(typeof oT[j].onclick == "function"){					
					oT[j].onclick();
				}*/

			}else{
				oT[j].disabled = false;				
			}
		}
	}
	
	//
	$("#elem_postSegFLSparks,#elem_postSegFLBolts,#elem_postSegFLArcs").val("").each(function(){ this.readOnly=(theObj.checked == true)?true:false; utElem_capture(this);});
	
	// Set Values	
	rvfun_setRVS_v1(theObj,'-Flashing Lights');
}

// Flashing Lights ----

// Floaters ----
function rvfun_getRVS_Fltrs(rvs_sm){
	var oe = document.getElementsByName("elem_postSegFloat[]");
	var str = "";

	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		//alert(""+oT[j].value);
		
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){

			//
			if(rvs_hasDtDesc(oT[j])){continue;}
			
			str += (str != "") ? ", " : "";	
			
			if(oT[j].value == "No Floaters"){ //No Flashing Lights		
				str = oT[j].value;
				break;
					
			}else if((oT[j].value == "Cobwebs") || (oT[j].value == "Black spots")){ //Cobwebs / Black spots
				var tmp = oT[j].value;
				
				var oe_tp = null;
				
				if(oT[j].value == "Cobwebs"){
					oe_tp = gebi("elem_postSegFloatCobwebs");
				}else if(oT[j].value == "Black spots"){
					oe_tp = gebi("elem_postSegFloatBlackSpots");
				}

				if(oe_tp && (oe_tp.value != "")){
					tmp += " " + rvs_check4Dot(oe_tp.value);
				}
				
				//Add to str
				str += tmp;
					
			}else{				
				//
				str += oT[j].value;
			}
		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;
}

function rvfun_setRVS_Fltrs(){
	var rvs_sm = "\r\n-Floaters: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_Fltrs(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}

function rv_noFloatersFn(theObj){
	var oe = document.getElementsByName("elem_postSegFloat[]");
	var str = "";

	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		
		if(oT[j].value != "No Floaters"){
			if(theObj.checked == true){
				oT[j].checked = false;
				oT[j].disabled = true;
				utElem_capture(oT[j]);
				//$("label[for="+oT[j].id+"]").css({"border":"2px solid transparent"});
				//
				if(rvs_isDescribed(oT[j].id)){
					clearRvsOne(oT[j].id, oT[j].id);
				}
				
				//onclick
				/*
				if(typeof oT[j].onclick == "function"){
					oT[j].onclick();
				}
				*/

			}else{
				oT[j].disabled = false;
			}
		}

	}
	//
	$("#elem_postSegFloatCobwebs,#elem_postSegFloatBlackSpots").val("").each(function(){	this.readOnly=(theObj.checked == true)?true:false; utElem_capture(this);});	
	
	// Set Values	
	rvfun_setRVS_v1(theObj,'-Floaters');
}

// Floaters ----

// Amsler Grid ----
function rvfun_getRVS_AG(rvs_sm){
	var oe = document.getElementsByName("elem_postSegAmsler[]");
	var str = "";

	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		//alert(""+oT[j].value);
		
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){
			//
			if(rvs_hasDtDesc(oT[j])){continue;}
			
			str += (str != "") ? ", " : "";	
			//
			str += oT[j].value;
		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;
}

function rvfun_setRVS_AG(){
	var rvs_sm = "\r\n-Amsler Grid: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_AG(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}
// Amsler Grid ----

// Double vision ----
function rvfun_getRVS_DV(rvs_sm){
	var oe = document.getElementsByName("elem_neuroDblVis[]");
	var str = "";

	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		//alert(""+oT[j].value);
		
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){	
			//
			if(rvs_hasDtDesc(oT[j])){continue;}
			
			str += (str != "") ? ", " : "";	
			//
			str += oT[j].value;
		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;
}

function rvfun_setRVS_DV(){
	var rvs_sm = "\r\n-Double vision: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_DV(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}
// Double vision ----

// Temporal Arteritis symptoms ---
function rvfun_getRVS_TAS(rvs_sm){
	var oe = document.getElementsByName("elem_neuroTAS[]");
	var oe_othr = gebi("elem_neuroTASOther");	
	var str = "";

	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){	
			//
			if(rvs_hasDtDesc(oT[j])){continue;}
			
			str += (str != "") ? ", " : "";	
			
			if(oT[j].value != "Other"){
				str += oT[j].value;
			}else{
				var oOthr = oe_othr;
				if(oOthr && (oOthr.value != "")){					
					
					str += rvs_check4Dot(oOthr.value);
				}
			}

		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;

}

function rvfun_setRVS_TAS(){
	var rvs_sm = "\r\n-Temporal Arteritis symptoms: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_TAS(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}

function rv_unselectSelect(obj){
	//set Yes No lidsItchingYesNo
	var oe = document.getElementsByName("THAYesNo");
	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){	
		if((obj.checked == true) && (obj.id != oT[j].id)){
			oT[j].checked = false;
			$("label[for="+oT[j].id+"]").css({"border":"2px solid transparent"});
			if(rvs_isDescribed(oT[j].id)){	clearRvsOne(oT[j].id, oT[j].id);	}
		}
	}
	
	//clear all in cchx
	if(obj.checked == true){
		var tid=obj.id;
		obj.checked = false;
		rvfun_setRVS_v1(obj,"-Temporal Arteritis symptoms");
		obj.checked = true;
	}
	
	//Set Other Elements
	var oe = document.getElementsByName("elem_neuroTAS[]");
	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){	
		if((obj.checked == true)){
			
			if(obj.value == "No"){
				//Uncheck and Disable All Elements				
				oT[j].checked = false;
				oT[j].disabled = true;
				$("label[for="+oT[j].id+"]").css({"border":"2px solid transparent"});

				//Hide Other Text box
				if((oT[j].value == "Other") && (typeof oT[j].onclick == "function")){
					oT[j].onclick();	
				}

			}else if(obj.value == "Yes" || obj.value == "Temporal Arteritis Symptoms"){
				//Enable All Elements				
				oT[j].disabled = false;			

			}

		}else{

			if(obj.value == "No"){
				//Enable All Elements
				oT[j].disabled = false;
			}
		}
	}

	//Set Values
	if(obj.id == "selectNoTHA"){ //||obj.checked == false
		rvfun_setRVS_v1(obj, "-Temporal Arteritis symptoms");
	}else if(obj.id == "selectYesTHA"){ // || obj.checked == true
		setRVS(obj,"-Temporal Arteritis symptoms");
	}	
}

// Temporal Arteritis symptoms ---

// Loss of vision ----
function rvfun_getRVS_LoV(rvs_sm){
	var oe = document.getElementsByName("elem_neuroVisLoss[]");
	var oe_othr = gebi("elem_neuroVisLossOther");	
	var str = "";

	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){

			//
			if(rvs_hasDtDesc(oT[j])){continue;}	
			
			// do not add
			if((oT[j].value == "Last Minutes") || oT[j].value == "Complete Loss of vision"){
				continue;	
			}
			
			//Add comma
			str += (str != "") ? ", " : "";			
			
			if(oT[j].value != "Other"){
				str += oT[j].value;

				if(oT[j].value == "Amaurosis fugax"){

					var ar = new Array("elem_fugaxLastMinutes","elem_fugaxCompleteLoss");
					for(var k=0;k<2;k++){
						var oe_tp = gebi(ar[k]);
						if(oe_tp && (oe_tp.checked == true)){
							str += " "+oe_tp.value;
						}
					}
				}

			}else{
				var oOthr = oe_othr;
				if(oOthr && (oOthr.value != "")){					
					
					str += rvs_check4Dot(oOthr.value);
				}
			}

		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;
}

function rvfun_setRVS_LoV(){
	var rvs_sm = "\r\n-Loss of vision: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_LoV(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}
// Loss of vision ----

// Headaches ---
function rvfun_getRVS_HdAch(rvs_sm){
	var oe = document.getElementsByName("elem_neuroHeadaches[]");	
	var str = "";

	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){

			//
			if(rvs_hasDtDesc(oT[j])){continue;}	
			
			str += (str != "") ? ", " : "";	
			str += oT[j].value;
		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	
	return str;
}

function rvfun_setRVS_HdAch(){
	var rvs_sm = "\r\n-Headaches: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_HdAch(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}
// Headaches ---

// Migraine Headaches  ----
function rvfun_getRVS_MigHdAch(rvs_sm){
	var oe_hd = document.getElementsByName("elem_neuroMigHead[]");
	var oe_ar = document.getElementsByName("elem_neuroMigHeadAura[]");
	var oe_othr = gebi("elem_neuroMigHeadAuraOther");	
	var arr = new Array(oe_hd,oe_ar);
	var arr_othr = new Array(null,oe_othr);	
	var str = "";

	for(var i=0;i<2;i++){

		var oT = arr[i];
		var ln = oT.length;
		for(var j=0;j<ln;j++){
			//alert(ln+""+oT[j].value+""+j);
			if((oT[j].type == "checkbox") && (oT[j].checked == true)){	
				//
				if(rvs_hasDtDesc(oT[j])){continue;}
				
				str += (str != "") ? ", " : "";	
				
				if(oT[j].value != "Other"){
					str += oT[j].value;
				}else{
					//alert(""+arr_othr[i]);
					var oOthr = arr_othr[i];
					if(oOthr && (oOthr.value != "")){						
						
						str += rvs_check4Dot(oOthr.value);
					}
				}

			}
		}
	}

	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str); 
		str = rvs_sm+str+rvs_em;
	}
	return str;
}

function rvfun_setRVS_MigHdAch(){
	var rvs_sm = "\r\n-Migraine Headaches: ";	
	//var rvs_CcHx = gebi("elem_chk");	
	var str = rvfun_getRVS_MigHdAch(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}
// Migraine Headaches  ----

// Post Op ----------------
function rvfun_getRVS_PostOp(rvs_sm){
	//var str= "The patient returns today for [] status post operative procedure follow-up";
	return rvfun_getRVS_Section_T1(rvs_sm,"elem_fuPostOp[]","elem_fuPostOp_other");
}

function rvfun_setRVS_PostOp(){
	var rvs_sm = "\r\n-Post Op: ";	
	var str = rvfun_getRVS_PostOp(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);	
	rvfun_setCcHx(str,obj);
}
// Post Op ----------------

// Follwo Up --------------
function rvfun_getRVS_FollowUp(rvs_sm){
	//var str = "The patient returns today for a(or an) [] follow-up";
	return rvfun_getRVS_Section_T1(rvs_sm,"elem_fuFollowUp[]","elem_fuFollowUp_other");
}

function rvfun_setRVS_FollowUp(){
	var rvs_sm = "\r\n-Follow Up: ";	
	var str = rvfun_getRVS_FollowUp(rvs_sm);
	var obj = rvfun_checkCcHx(rvs_sm);
	rvfun_setCcHx(str,obj);
}
// Follwo Up --------------

//GET OPtions selected in a section
function rvfun_getRVS_Section_T1(rvs_sm,chkNm,othrNm, str2){
	var oe = document.getElementsByName(""+chkNm);
	var oe_othr = gebi(""+othrNm);	
	var str = "";

	var oT = oe;
	var ln = oT.length;
	for(var j=0;j<ln;j++){
		if((oT[j].type == "checkbox") && (oT[j].checked == true)){	

			//
			if(rvs_hasDtDesc(oT[j])){continue;}
			
			str += (str != "") ? ", " : "";	
			
			if(oT[j].value != "Other"){
				str += oT[j].value;
			}else{
				var oOthr = oe_othr;
				if(oOthr && (oOthr.value != "")){					
					
					str += rvs_check4Dot(oOthr.value);
				}
			}

		}
	}
	
	//trim
	str = $.trim(str);
	//Add Modifier	
	if(str != ""){		
		//Check for last comma
		str = rvfun_removeLastComma(str);

		if(typeof(str2)!="undefined"&&str2!=""){
			str = str2.replace(/\[\]/g,str);
		}
		
		str = rvs_sm+str+rvs_em;
	}
	return str;
}

//Remove Last Comma
function rvfun_removeLastComma(cstr){
	var ptrn = "\\s*\,\\s*$";
	var reg = new RegExp(ptrn,"g");
	var mtc = cstr.match(reg);
	if(mtc != null){
		cstr = cstr.replace(reg,"");
	}
	return cstr;
}

//Check with header
function rvfun_checkCcHx(chkStr){	
	var rvs_CcHx = gebi("elem_ccompliant");	
	var strCcHx = rvs_CcHx.value;	
	var stIndx = -1;
	var edIndx = -1;
	var flg = 0;
	
	if((typeof strCcHx != "undefined")){	
		chkStr = chkStr.replace("\r\n","");
		
		stIndx = strCcHx.indexOf(chkStr);
		
		if(stIndx != -1){
			// Exists	
			flg = 1; 
	
			//Check for End index
			var tmIndx,tmIndx2;
			tmIndx = strCcHx.indexOf(rvs_em,stIndx);
			if(tmIndx != -1){
				edIndx = tmIndx;
			}else{ //check for only dot .
				var t1 = strCcHx.indexOf(" . ",stIndx);
				var t2 = strCcHx.indexOf(".",stIndx);

				if(t1!=-1){
					var c=0;
					do{
					
					if((t1+1)==t2){
						//edIndx =strCcHx.indexOf(".",t2+1);
						t1 = strCcHx.indexOf(" . ",t2+1);
						t2 = strCcHx.indexOf(".",t2+1);
					}else{
						edIndx =t2;
						break;
					}
					c+=1;
					if(c>50)break;

					}while(true);

				}else{
					edIndx =t2;
				}
				
				//edIndx = strCcHx.indexOf(".",stIndx);
			}
		}
	}
	
	//alert("chkStr: "+chkStr+", flg:"+flg+", st:"+stIndx+", ed: "+edIndx);
	return {"flg":flg, "st":stIndx, "ed":edIndx};
}

//remove only dot in a line

function rvfun_remonlydot(str){
	
	var ptrn = "((\\r|\\r\\n|\\n|\r\n)\\s*\\.\\s*(\\r|\\r\\n|\\n|\r\n))+"; //
	var reg = new RegExp(ptrn,"g");
	var mtch = str.match(reg);
	//alert(mtch);
	if(mtch){		
		str = str.replace(reg,"\r\n");
		str = rvfun_remonlydot(str);
		//alert(""+str_st.addslashes());
		//str_st+="\r";		
	}
	return str;
}

//Set in CcHx
function rvfun_setCcHx(str,obj){
	var rvs_CcHx = gebi("elem_ccompliant");
	var curVal = (typeof rvs_CcHx.value != "undefined") ? $.trim(rvs_CcHx.value) : "";
	if((""+obj.flg == 1) && (""+obj.st != -1) && (""+obj.ed != -1)){
		
		//Update
		var str_st = $.trim(curVal.substring(0,obj.st));
		if(str_st=="-")str_st="";
		/*
		//Check for last '.'
		//if exists and not \r\n then add
		var ptrn = "\\r$";
		var reg = new RegExp(ptrn,"g");
		var mtch = str_st.match(reg);
		alert(mtch);
		if(!mtch){
			alert(""+str_st.addslashes());
			//str_st+="\r";		
		}
		*/

		if(str == ""){
			str += "\r";
		}
		
		var str_ed = $.trim(curVal.substring(obj.ed+1));
		var new_str = str_st+str+str_ed;
		rvs_CcHx.value = ""+new_str;
		
		rvs_CcHx.value = rvfun_remonlydot(""+rvs_CcHx.value);
		
		if(typeof rvs_CcHx.onchange == "function"){
			rvs_CcHx.onchange();	
		}
		if(typeof rvs_CcHx.onkeypress == "function"){
			rvs_CcHx.onkeypress();
		}
		
	}else{
		
		//Insert if str is not empty
		if(str != ""){
			rvs_CcHx.value = $.trim(curVal)+""+str;
			
			rvs_CcHx.value = rvfun_remonlydot(""+rvs_CcHx.value);
			
			if(typeof rvs_CcHx.onchange == "function"){
				rvs_CcHx.onchange();
			}
			if(typeof rvs_CcHx.onkeypress == "function"){
				rvs_CcHx.onkeypress();
			}
		}
	}
}

//Display Set
function rvs_disOth(ob,id,tId){	
	var t = gebi(id);	
	if(t){
		if(ob.checked == true){
			t.style.visibility = "visible";					
		}else{
			//t.style.visibility = "hidden";
			t.style.visibility = "visible";
			
			//Empty value and call onchange 
			var ot = gebi(tId);
			if(ot.value != ""){ ob.checked=true;  }
			if(ot){				
				//ot.value = "";
				ot.onchange();
			}
		}
	}
}

//Display and cc values set
function rv_showOther(obj, id, head, textBox){
	var flg=(obj.id=="div38" || obj.id=="div39" || obj.id=="div40" || obj.id=="div47" || obj.id=="div48") ? 1 : 0;	
	var t = gebi(id);	
	if(t){
		if(obj.checked == true){
			t.style.visibility = "visible";					
		}else{
			//t.style.visibility = "hidden";
			t.style.visibility = "visible";
			
			//Empty value and call onchange 
			if(textBox != ""){
				var ot = gebi(textBox);
				if(ot.value != ""){ obj.checked=true;  }
				if(ot){
					//ot.value = "";
					if(flg==0){ot.onchange();}						
				}
			}
		}
	}
	//Set Cc values
	if(flg==1){rvfun_setRVS_v2(obj,head);}
}

//Check for Dot
function rvs_check4Dot(val){	
	if(val.indexOf(".") != -1){
		var ptrn = "\\.";
		var reg = new RegExp(ptrn, "g");
		var mtch = val.match(reg);
		//alert(mtch);
		if(mtch){
			val = val.replace(reg," . ");
		}
	}
	return val;
}

function rvfun_checkboxOther(o){
	var id_chk = $(o).parent("label").attr("for");
	if(typeof(id_chk)=="undefined" || id_chk==""){return;}
	if($.trim(o.value)==""){
		$("#"+id_chk).prop("checked", false);
	}else{		
		$("#"+id_chk).prop("checked", true);
	}
	utElem_capture($("#"+id_chk)[0]);
}

var rvfun_setRVS_v2_head="",rvfun_setRVS_v2_show_1=0;

function setRVS_radio(obj, arg, head, otherId){	
	var onm = obj.name;
	var oid = obj.id;
	if(obj.checked){ $(":checked[name="+onm+"][id!="+oid+"]").prop("checked", false).each(function(){ utElem_capture(this); }); }
	
	var t = gebi('elem_ccompliant');
	var nt=arg.value;
	var ccv = t.value;
	
	
	if(oid.indexOf("elem_irrOcuPresSensType")!=-1){
		var r="pressure sensation";
		if(obj.checked){var m = $.trim($(":checked[name=elem_irrOcuPresSensType]").val());if(typeof(m)!="undefined"&&m!=""){ r=r+" and "+m.toLowerCase(); }}
		var ptrn=new RegExp("The patient experiences pressure sensation(\\s*and\\s*)?(no sinusitis|post nasal drip)?","gi");//
		if(ccv.search(ptrn)!=-1){ r="The patient experiences "+r;ccv=ccv.replace(ptrn,r);t.value=ccv;t.onchange(); }else{setCcHx3(arg, head, otherId);}
	}else{		
		var r="itching eyes";
		if(obj.checked){ var m = $(":checked[name=elem_irrOcuItchingType]").val();
			if(m=="Mild Itching"){ r="mildly "+r; }else if(m=="Severe Itching"){ r="severely "+r; }else if(m=="Seasonal allergies Itching"){ r=r+" due to seasonal allergies"; }			
		}	
		var ptrn=new RegExp("The patient experiences\\s*(mildly|severely)?\\s*itching eyes\\s*(due to seasonal allergies)?","gi");//		
		if(ccv.search(ptrn)!=-1){ r="The patient experiences "+r;ccv=ccv.replace(ptrn,r);t.value=ccv;t.onchange(); }else{setRVS(arg, head, otherId);}		
	}
}
function rvfun_setRVS_v2(obj, head, otherId){	
	if(obj.id=="elem_postSegFLSparks" || obj.id=="elem_postSegFLBolts" || obj.id=="elem_postSegFLArcs" || 
		obj.id=="elem_postSegFloatCobwebs" || obj.id=="elem_postSegFloatBlackSpots"){
		obj=$("#"+otherId)[0];obj.checked=true;	
	}
	
	var str_com_head ="";
	if(complaintHead.length>0){
		var str_com_head = complaintHead.join(",");
		str_com_head =$.trim(str_com_head)+",";
	}
	
	//if unchecked	
	if(obj.type=="radio" || $(obj).hasClass("radiotype")){
		rvfun_setRVS_v1(obj, head, otherId);
		return;
	}else if(obj.type=="checkbox"){
		
		if(obj.checked==true){ 
			if(complaintHead.length>=3&&rvfun_setRVS_v2_show_1!=1){
				top.fAlert("Provider can detail up to 3 complaints only.") ;
				rvfun_setRVS_v2_show_1=1;
			}
		
			if((obj.checked==true&&complaintHead.length>=3)){			
				rvfun_setRVS_v1(obj, head, otherId);
			}	
			
		}else{
			
			if(str_com_head.indexOf(""+obj.id+",")!=-1){				
				if($("label[for="+obj.id+"]").length>0){					
					obj.checked = true;
					utElem_capture(obj);
					///$("label[for="+obj.id+"]").triggerHandler("click");					
				}
			}
			rvfun_setRVS_v1(obj, head, otherId);
		}		
		
		//alert(str_com_head+"  - "+obj.id+" - "+str_com_head.indexOf(""+obj.id+","));
		
		
		if(complaintHead.length>=3){			
			if((obj.checked==true && str_com_head.indexOf(""+obj.id+",")==-1)){
				return;
			}
		}
		
		if((obj.checked==false && str_com_head.indexOf(""+obj.id+",")==-1)){
			return;	
		}
	}else if(obj.type=="text"){
		
		if(obj.value!=""){ 
			if(complaintHead.length>=3&&rvfun_setRVS_v2_show_1!=1){
				top.fAlert("Provider can detail up to 3 complaints only.") ;
				rvfun_setRVS_v2_show_1=1;
			}
		
			if((obj.value!=""&&complaintHead.length>=3)){			
				rvfun_setRVS_v1(obj, head, otherId);
			}
		}else{
			
			rvfun_setRVS_v1(obj, head, otherId);
		}
		
		//checkbox checked in safari--
		if(typeof(isSafari)!="undefined" && isSafari==1){	rvfun_checkboxOther(obj); 	}
		//checkbox checked in safari--
		
		if(complaintHead.length>=3){			
			if((obj.value!="" && str_com_head.indexOf(""+obj.id+",")==-1)){
				return;
			}
		}
		
		if((obj.value=="" && str_com_head.indexOf(""+obj.id+",")==-1)){
			return;	
		}
		
	}
	//--
	
	//var x = rvs_getEntryBegining(obj.id);
	//return;
	
	
	//if checked
	rvfun_setRVS_v2_head=head;	
	switch(head){
		case "-Vision Problem":
			describeMe('Difficulty in', obj, obj.id);
		break;
		case "-Glare Problem":
			describeMe('Glare', obj, obj.id);			
		break;
		case "-Other Vision Problem":
			describeMe('Difficulty in Vision', obj, obj.id);
		break;
		case "-Patient Comments":
			rvfun_setRVS_PtCom();
		break;
		case "-Irritation Lids":
			describeMe('Lids', obj, obj.id);
		break;
		case "-Irritation Ocular":
			describeMe('Ocular', obj, obj.id);
		break;
		case "-Post Segment":				
			describeMe('Post Segment', obj, obj.id);
		break;
		case "-Flashing Lights":
			describeMe('Flashing Lights', obj, obj.id);
		break;
		case "-Floaters":
			describeMe('Floaters', obj, obj.id);
		break;
		case "-Amsler Grid":
			describeMe('Amsler Grid', obj, obj.id);
		break;
		case "-Double vision":
			describeMe('Double vision', obj, obj.id);
		break;
		case "-Temporal Arteritis symptoms":			
			describeMe('Temporal Arteritis symptoms', obj, obj.id);
		break;
		case "-Loss of vision":			
			describeMe('Last Minutes', obj, obj.id);
		break;
		case "-Headaches":			
			describeMe('Headaches', obj, obj.id);
		break;
		case "-Migraine Headaches":			
			describeMe('Migraine Headaches', obj, obj.id);
		break;
		case "-Post op":
			describeMe('Post op', obj, obj.id);
		break;
		case "-Follow Up":
			describeMe('Follow Up', obj, obj.id);
		break;
	}
	
}

function rvfun_setRVS_v1(obj, head, otherId){
	
	//alert(obj+", "+head+", "+otherId);			
	switch(head){
		case "-Vision Problem":		
			rvfun_setRVS_VP();
		break;
		case "-Glare Problem":		
			rvfun_setRVS_GP();
		break;
		case "-Other Vision Problem":
			rvfun_setRVS_OVP();
		break;
		case "-Patient Comments":
			rvfun_setRVS_PtCom();
		break;
		case "-Irritation Lids":
			rvfun_setRVS_IrrLids();
		break;
		case "-Irritation Ocular":
			rvfun_setRVS_IrrOcu();
		break;
		case "-Post Segment":				
			rvfun_setRVS_PS();
		break;
		case "-Flashing Lights":
			rvfun_setRVS_FL();
		break;
		case "-Floaters":
			rvfun_setRVS_Fltrs();
		break;
		case "-Amsler Grid":
			rvfun_setRVS_AG();
		break;
		case "-Double vision":
			rvfun_setRVS_DV();
		break;
		case "-Temporal Arteritis symptoms":			
			rvfun_setRVS_TAS();
		break;
		case "-Loss of vision":			
			rvfun_setRVS_LoV();
		break;
		case "-Headaches":			
			rvfun_setRVS_HdAch();
		break;
		case "-Migraine Headaches":			
			rvfun_setRVS_MigHdAch();
		break;
		case "-Post op":
			rvfun_setRVS_PostOp();
		break;
		case "-Follow Up":
			rvfun_setRVS_FollowUp();
		break;
	}
}

//v1 ---
//Test --
function clearAll(div){	
		var divMark = gebi('divSelected').value;
		var TDMark = gebi('tdSelected').value;
		var flag = 0;
		if(complaintHead.length>0){
			for(var i=0; i<complaintHead.length; i++) {				
				if(complaintHead[i] == divMark){
					///gebi(TDMark).className = 'txt_9';
					$("#"+TDMark).removeClass("rvs_dt_dd_hgt");// = 'txt_9'
					break;
				}
			}
		}
		if(i==0){
			if(complaint3.length>0){
				// II in 1st
					complaint1.length = complaint2.length;
					for(var i=0;i<complaint2.length;i++){
						complaint1[i] = complaint2[i];
					}
					complaint2.length=0;
				// II in 1st

				// IIIrd in IInd
					complaint2.length = complaint3.length;
					for(var i=0;i<complaint3.length;i++){
						complaint2[i] = complaint3[i];
					}
					complaint3.length=0;
				// IIIrd in IInd
			}else if(complaint2.length>0){
				// II in 1st
				complaint1.length = complaint2.length;
				for(var i=0;i<complaint2.length;i++){
					complaint1[i] = complaint2[i];
				}
				complaint2.length=0;
				// II in 1st
			}else{				
				complaint1.length=0;
			}
			
			// SHIFT COMPLAINT HEADING 
				complaintHead.splice(0,1);
				selectedHead.splice(0,1);
				titleHeadArr.splice(0,1);
				strAllConcat.splice(0,1);				
			// SHIFT COMPLAINT HEADING 
		}else if(i==1){
			// SHIFT COMPLAINTS 3 IF EXISTS
			if(complaint3.length>0){
				complaint2.length = complaint3.length;
				for(var i=0;i<complaint3.length;i++){
					complaint2[i] = complaint3[i];
				}
				complaint3.length=0;
			}else{
				complaint2.length=0;
			}
			// SHIFT COMPLAINTS 3 IF EXISTS
			
			// SHIFT COMPLAINT HEADING 
				complaintHead.splice(1,1);
				selectedHead.splice(1,1);
				titleHeadArr.splice(1,1);
				strAllConcat.splice(1,1);
			// SHIFT COMPLAINT HEADING 
		}else if(i==2){
			// SHIFT COMPLAINTS 3 IF EXISTS
			complaint3.length = 0;
			// SHIFT COMPLAINTS 3 IF EXISTS
			
			// SHIFT COMPLAINT HEADING 
				complaintHead.splice(2,1);
				selectedHead.splice(2,1);
				titleHeadArr.splice(2,1);
				strAllConcat.splice(2,1);
			// SHIFT COMPLAINT HEADING 
		}
		
	// RESET VALUES
		var textStr1 = complaint1.join(',');
		gebi('complaint1Text').value = textStr1;
	
		var textStr2 = complaint2.join(',');
		gebi('complaint2Text').value = textStr2;
	
		var textStr3 = complaint3.join(',');
		gebi('complaint3Text').value = textStr3;
	
		var texthead1 = complaintHead.join(',');
		gebi('complaintHeadText').value = texthead1;
		var selectedTexthead1 = selectedHead.join(',');
		gebi('selectedHeadText').value = selectedTexthead1;
		gebi('titleHeadText').value = titleHeadArr.join(',');
	// RESET VALUES
	
	if( (div != null) && (typeof div != "undefined") ){
	div = typeof div === "string" ? gebi(div) : div;	
		var elms = div.getElementsByTagName("*");
		for(var i = 0, maxI = elms.length; i < maxI; ++i) {
			var elm = elms[i];
				switch(elm.type) {		
					case "text":
						elm.value = '';
					break;
					case "textarea":
						elm.value = '';
					break;	
					case "hidden":
						elm.value = '';
					break;
					case "checkbox":
						elm.checked = false;
					break;
					case "select-one":
						gebi('selectNo').value = '';
						gebi('selectDate').value = '';
					break;
					case "select-multiple":
						elm.value = '';
					break;
				}
		}
	}

	//clear on reset --	
	if(describeMe_divId!=""){
		if($("label[for="+describeMe_divId+"]").length>0){
			$("#"+describeMe_divId).prop("checked", false);
		}else{
			$("#"+describeMe_divId).val("");
			var fr = $("#"+describeMe_divId).parent('label').attr("for");
			if(typeof(fr)!="undefined" && $("#"+fr).prop("checked")){ $("#"+fr).trigger("click"); } //uncheck
		}
		utElem_capture($("#"+describeMe_divId)[0]);
		//add in ccHX
		rvfun_setRVS_v1("", rvfun_setRVS_v2_head, "");
	}
	//clear on reset --	
	
	makeStr('1');
	makeStr('2');
	makeStr('3');
	closeWindow();
}

function clearRvsOne(divId, tdId){
	
	gebi("divSelected").value = divId;
	gebi("tdSelected").value = tdId;
	clearAll();
	gebi("divSelected").value = "";
	gebi("tdSelected").value = "";
}


var describeMe_divId="";
function describeMe(preTitle, objId, divId){
	/* //STOPPED To remove value on describing
	if(( gebi(divId).type == "checkbox" ) && (gebi(divId).checked == true)){
		gebi(divId).checked = false;
		gebi(divId).onclick();
	}*/
	
	describeMe_divId=divId;
	if($("label[for="+divId+"]").length>0){
		var objId2_label = $("label[for="+divId+"]").html();	
	}else{		
		var objId2_label=gebi(divId).value;
	}
	title = objId2_label; //objId.innerHTML;
	title2 = objId2_label; //objId.innerHTML;
	if(title.toLowerCase() == "other"){
		title = gebi(divId).value;
	}
	if($.trim(title) == ""){
		return;
	}
	
	if(title.indexOf(', ') != -1){
		var title = title.replace(',', '!');
	}
	globTitle = preTitle+' '+title;
	var flag = 0;
	// DIV AND INPUT ELEMENTS OBJECT
		var detaildivid = "detailDesc1", detailDescriptionHdr="detailDescription1";
		if(preTitle=="Follow Up"||preTitle=="Post op"){detaildivid = "detailDesc2"; detailDescriptionHdr="detailDescription2";}
		var divObj = gebi(detaildivid);
		var elem = divObj.getElementsByTagName('INPUT');
		var eleSelect = divObj.getElementsByTagName('SELECT');
		var eleTextArea = divObj.getElementsByTagName('TEXTAREA');
	// DIV AND INPUT ELEMENTS OBJECT
	
	// MAKE FORM EMPTY 
		makeEmpty(elem, eleSelect, eleTextArea)
	// MAKE FORM EMPTY 
	
	//	
	hpi_chk_entr_dt(null,3);
		
	//	
	if(preTitle=="Follow Up"){ $("#detailDesc2 #lbl_hpi_srgry_type").html("Follow Up "); }
	else if(preTitle=="Post op"){$("#detailDesc2 #lbl_hpi_srgry_type").html("S/P (Surgery type:) ");}
		
	// PRESENTLY SELECTED IN FORMATION
		gebi('divSelected').value = divId;
		gebi('tdSelected').value = objId.id;
	// PRESENTLY SELECTED IN FORMATION
	for(var i=0; i<complaintHead.length;i++){
		if(complaintHead[i] == divId){			
			//REFILL AND DISPLAY VALUES
				if(i==0){
					titleHeadArr[0] = globTitle;
					gebi('titleHeadText').value = titleHeadArr.join(',');
					for(var k=0; k<complaint1.length; k++) {
						for(var l=0; l<elem.length; l++){
							if(elem[l].value == complaint1[k]){
								elem[l].checked = true;
							}								
						}
						if(complaint1[k].indexOf('-') != -1){
							var dashPos = complaint1[k].indexOf('-') + 1;
							var eleVal = complaint1[k].substr(dashPos);
							eleVal = hpi_mask_spl_chars(eleVal,1);
							if(complaint1[k].indexOf('scale1To10') != -1){								
								$('#'+detaildivid+' :input[name=scale1To10]').val(""+eleVal);
							}else if(complaint1[k].indexOf('onSetDate') != -1){								
								gebi('onSetDate').value = eleVal;								
							}else if(complaint1[k].indexOf('elem_os_othr_date') != -1){								
								gebi('elem_os_othr_date').value = eleVal;hpi_chk_entr_dt(null,4);	
							}else if(complaint1[k].indexOf('makesBetter') != -1&&gebi('makesBetter')){																
								gebi('makesBetter').value = eleVal;
							}else if(complaint1[k].indexOf('makesWorse') != -1&&gebi('makesWorse')){								
								gebi('makesWorse').value = eleVal;
							}else if(complaint1[k].indexOf('selectNo') != -1){
								$('#'+detaildivid+' :input[name=selectNo]').val(""+eleVal);
							}else if(complaint1[k].indexOf('selectDate') != -1){									
								$('#'+detaildivid+' :input[name=selectDate]').val(""+eleVal);
							}else if(complaint1[k].indexOf('otherQty') != -1){								
								$('#'+detaildivid+' :input[name=otherQty]').val(""+eleVal);
							}
							else if(complaint1[k].indexOf('otherTiming') != -1){								
								//gebi('otherTiming').value = eleVal;
							}else if(complaint1[k].indexOf('otherContext') != -1){								
								gebi('otherContext').value = eleVal;
							}else if(complaint1[k].indexOf('otherFactors') != -1){								
								gebi('otherFactors').value = eleVal;
							}else if(complaint1[k].indexOf('otherSymptoms') != -1){								
								gebi('otherSymptoms').value = eleVal;
							}else if(complaint1[k].indexOf('selectDoE') != -1){
								$('#'+detaildivid+' :input[name=selectDoE]').val(""+eleVal);
							}else if(complaint1[k].indexOf('elem_sp_surtype') != -1){								
								gebi('elem_sp_surtype').value = eleVal;
							}else if(complaint1[k].indexOf('elem_sp_surdate') != -1){								
								gebi('elem_sp_surdate').value = eleVal;hpi_chk_entr_dt(null,5);
							}else if(complaint1[k].indexOf('rvsdet_otherVision') != -1){								
								gebi('rvsdet_otherVision').value = eleVal;
							}else if(complaint1[k].indexOf('rvs_other_svrity') != -1){								
								gebi('rvs_other_svrity').value = eleVal;
							}else if(complaint1[k].indexOf('rvs_painrelievedby') != -1){								
								gebi('rvs_painrelievedby').value = eleVal;
							}else if(complaint1[k].indexOf('otherFollowCareInstruct') != -1){								
								gebi('otherFollowCareInstruct').value = eleVal;
							}else if(complaint1[k].indexOf('rvs_ranoutmeds') != -1){								
								gebi('rvs_ranoutmeds').value = eleVal;
							}else if(complaint1[k].indexOf('rvs_needs_med_refill') != -1){								
								gebi('rvs_needs_med_refill').value = eleVal;
							}else if(complaint1[k].indexOf('rvs_followup_detail_other') != -1){								
								gebi('rvs_followup_detail_other').value = eleVal;
							}else if(complaint1[k].indexOf('selLocOpt') != -1){
								$('#'+detaildivid+' :input[name=selLocOpt]').val(""+eleVal);
							}else if(complaint1[k].indexOf('selLocOther') != -1){									
								$('#'+detaildivid+' :input[name=selLocOther]').val(""+eleVal);
							}else if(complaint1[k].indexOf('other_par_neg') != -1){									
								$('#'+detaildivid+' :input[name=other_par_neg]').val(""+eleVal);
							}else if(complaint1[k].indexOf('otherDoe') != -1){									
								$('#'+detaildivid+' :input[name=otherDoe]').val(""+eleVal);
							}else if(complaint1[k].indexOf('other_onset') != -1){						
								$('#'+detaildivid+' :input[name=other_onset]').val(""+eleVal);
							}							
						}
					}
				}else if(i == 1){
					titleHeadArr[1] = globTitle;
					gebi('titleHeadText').value = titleHeadArr.join(',');
					for(var k=0; k<complaint2.length; k++) {
						for(var l=0; l<elem.length; l++){
							if(elem[l].value == complaint2[k]){
								elem[l].checked = true;
							}
						}
						if(complaint2[k].indexOf('-') != -1){
							var dashPos = complaint2[k].indexOf('-') + 1;
							var eleVal = complaint2[k].substr(dashPos);
							eleVal = hpi_mask_spl_chars(eleVal,1);
							if(complaint2[k].indexOf('scale1To10') != -1){
								$('#'+detaildivid+' :input[name=scale1To10]').val(""+eleVal);
							}else if(complaint2[k].indexOf('onSetDate') != -1){
								gebi('onSetDate').value = eleVal;								
							}else if(complaint2[k].indexOf('elem_os_othr_date') != -1){								
								gebi('elem_os_othr_date').value = eleVal;hpi_chk_entr_dt(null,4);	
							}else if(complaint2[k].indexOf('makesBetter') != -1&&gebi('makesBetter')){								
								gebi('makesBetter').value = eleVal;								
							}else if(complaint2[k].indexOf('makesWorse') != -1&&gebi('makesWorse')){
								gebi('makesWorse').value = eleVal;								
							}else if(complaint2[k].indexOf('selectNo') != -1){
								$('#'+detaildivid+' :input[name=selectNo]').val(""+eleVal);							
							}else if(complaint2[k].indexOf('selectDate') != -1){
								$('#'+detaildivid+' :input[name=selectDate]').val(""+eleVal);			
							}else if(complaint2[k].indexOf('otherQty') != -1){
								$('#'+detaildivid+' :input[name=otherQty]').val(""+eleVal);		
							}else if(complaint2[k].indexOf('otherTiming') != -1){
								//gebi('otherTiming').value = eleVal;								
							}else if(complaint2[k].indexOf('otherContext') != -1){
								gebi('otherContext').value = eleVal;								
							}else if(complaint2[k].indexOf('otherFactors') != -1){
								gebi('otherFactors').value = eleVal;								
							}else if(complaint2[k].indexOf('otherSymptoms') != -1){
								gebi('otherSymptoms').value = eleVal;								
							}else if(complaint2[k].indexOf('selectDoE') != -1){								
								$('#'+detaildivid+' :input[name=selectDoE]').val(""+eleVal);
							}else if(complaint2[k].indexOf('elem_sp_surtype') != -1){								
								gebi('elem_sp_surtype').value = eleVal;
							}else if(complaint2[k].indexOf('elem_sp_surdate') != -1){								
								gebi('elem_sp_surdate').value = eleVal;hpi_chk_entr_dt(null,5);
							}else if(complaint2[k].indexOf('rvsdet_otherVision') != -1){								
								gebi('rvsdet_otherVision').value = eleVal;
							}else if(complaint2[k].indexOf('rvs_other_svrity') != -1){								
								gebi('rvs_other_svrity').value = eleVal;
							}else if(complaint2[k].indexOf('rvs_painrelievedby') != -1){								
								gebi('rvs_painrelievedby').value = eleVal;
							}else if(complaint2[k].indexOf('otherFollowCareInstruct') != -1){								
								gebi('otherFollowCareInstruct').value = eleVal;
							}else if(complaint2[k].indexOf('rvs_ranoutmeds') != -1){								
								gebi('rvs_ranoutmeds').value = eleVal;
							}else if(complaint2[k].indexOf('rvs_needs_med_refill') != -1){								
								gebi('rvs_needs_med_refill').value = eleVal;
							}else if(complaint2[k].indexOf('rvs_followup_detail_other') != -1){								
								gebi('rvs_followup_detail_other').value = eleVal;
							}else if(complaint2[k].indexOf('selLocOpt') != -1){								
								$('#'+detaildivid+' :input[name=selLocOpt]').val(""+eleVal);
							}else if(complaint2[k].indexOf('selLocOther') != -1){								
								$('#'+detaildivid+' :input[name=selLocOther]').val(""+eleVal);
							}else if(complaint2[k].indexOf('other_par_neg') != -1){									
								$('#'+detaildivid+' :input[name=other_par_neg]').val(""+eleVal);
							}else if(complaint2[k].indexOf('otherDoe') != -1){									
								$('#'+detaildivid+' :input[name=otherDoe]').val(""+eleVal);
							}else if(complaint2[k].indexOf('other_onset') != -1){						
								$('#'+detaildivid+' :input[name=other_onset]').val(""+eleVal);
							}
						}
					}
				}else if(i == 2){
					titleHeadArr[2] = globTitle;
					gebi('titleHeadText').value = titleHeadArr.join(',');
					for(var k=0; k<complaint3.length; k++) {
						for(var l=0; l<elem.length; l++){
							if(elem[l].value == complaint3[k]){
								elem[l].checked = true;
							}
						}
						if(complaint3[k].indexOf('-') != -1){
							var dashPos = complaint3[k].indexOf('-') + 1;
							var eleVal = complaint3[k].substr(dashPos);
							eleVal = hpi_mask_spl_chars(eleVal,1);
							if(complaint3[k].indexOf('scale1To10') != -1){
								$('#'+detaildivid+' :input[name=scale1To10]').val(""+eleVal);						
							}else if(complaint3[k].indexOf('onSetDate') != -1){
								gebi('onSetDate').value = eleVal;
							}else if(complaint3[k].indexOf('elem_os_othr_date') != -1){
								gebi('elem_os_othr_date').value = eleVal;hpi_chk_entr_dt(null,4);
							}else if(complaint3[k].indexOf('makesBetter') != -1&&gebi('makesBetter')){
								gebi('makesBetter').value = eleVal;								
							}else if(complaint3[k].indexOf('makesWorse') != -1&&gebi('makesWorse')){
								gebi('makesWorse').value = eleVal;								
							}else if(complaint3[k].indexOf('selectNo') != -1){
								$('#'+detaildivid+' :input[name=selectNo]').val(""+eleVal);		
							}else if(complaint3[k].indexOf('selectDate') != -1){
								$('#'+detaildivid+' :input[name=selectDate]').val(""+eleVal);		
							}else if(complaint3[k].indexOf('otherQty') != -1){
								$('#'+detaildivid+' :input[name=otherQty]').val(""+eleVal);		
							}else if(complaint3[k].indexOf('otherTiming') != -1){
								//gebi('otherTiming').value = eleVal;								
							}else if(complaint3[k].indexOf('otherContext') != -1){
								gebi('otherContext').value = eleVal;								
							}else if(complaint3[k].indexOf('otherFactors') != -1){
								gebi('otherFactors').value = eleVal;								
							}else if(complaint3[k].indexOf('otherSymptoms') != -1){
								gebi('otherSymptoms').value = eleVal;								
							}else if(complaint3[k].indexOf('selectDoE') != -1){								
								$('#'+detaildivid+' :input[name=selectDoE]').val(""+eleVal);
							}else if(complaint3[k].indexOf('elem_sp_surtype') != -1){								
								gebi('elem_sp_surtype').value = eleVal;
							}else if(complaint3[k].indexOf('elem_sp_surdate') != -1){								
								gebi('elem_sp_surdate').value = eleVal;hpi_chk_entr_dt(null,5);
							}else if(complaint3[k].indexOf('rvsdet_otherVision') != -1){								
								gebi('rvsdet_otherVision').value = eleVal;
							}else if(complaint3[k].indexOf('rvs_other_svrity') != -1){								
								gebi('rvs_other_svrity').value = eleVal;
							}else if(complaint3[k].indexOf('rvs_painrelievedby') != -1){								
								gebi('rvs_painrelievedby').value = eleVal;
							}else if(complaint3[k].indexOf('otherFollowCareInstruct') != -1){								
								gebi('otherFollowCareInstruct').value = eleVal;
							}else if(complaint3[k].indexOf('rvs_ranoutmeds') != -1){								
								gebi('rvs_ranoutmeds').value = eleVal;
							}else if(complaint3[k].indexOf('rvs_needs_med_refill') != -1){								
								gebi('rvs_needs_med_refill').value = eleVal;
							}else if(complaint3[k].indexOf('rvs_followup_detail_other') != -1){								
								gebi('rvs_followup_detail_other').value = eleVal;
							}else if(complaint3[k].indexOf('selLocOpt') != -1){								
								$('#'+detaildivid+' :input[name=selLocOpt]').val(""+eleVal);
							}else if(complaint3[k].indexOf('selLocOther') != -1){								
								$('#'+detaildivid+' :input[name=selLocOther]').val(""+eleVal);
							}else if(complaint3[k].indexOf('other_par_neg') != -1){									
								$('#'+detaildivid+' :input[name=other_par_neg]').val(""+eleVal);
							}else if(complaint3[k].indexOf('otherDoe') != -1){									
								$('#'+detaildivid+' :input[name=otherDoe]').val(""+eleVal);
							}else if(complaint3[k].indexOf('other_onset') != -1){						
								$('#'+detaildivid+' :input[name=other_onset]').val(""+eleVal);
							}
						}
					}
				}
				gebi(detailDescriptionHdr).innerHTML = title2+' (Describe Details)';
				gebi(detaildivid).style.display = 'block';
				break;
			//REFILL AND DISPLAY VALUES
		}else{
			++flag;
		}
	}
	if(flag==3&&1==2){
		top.fAlert("Provider can detail up to 3 complaints only.");
	}else{
		if(preTitle == "Post op" || preTitle == "Follow Up"){				
			if(flag==0){$("#detailDesc2 #lbl_hpi_cntr").removeClass("hidden");$("#detailDesc2 .hpi_cntr").html("0"); $("#detailDesc2").bind("click keyup",function(){rvs_dd_we_handle();}); rvs_dd_we_handle('detailDesc2'); }else{$("#detailDesc2 #lbl_hpi_cntr").addClass("hidden"); $("#detailDesc2").unbind("click, keyup");}
			gebi('detailDescription2').innerHTML = title2+' (Describe Details)';
			gebi('detailDesc2').style.display = 'block';
		}else{
			if(flag==0){$("#detailDesc1 #lbl_hpi_cntr").removeClass("hidden");$("#detailDesc1 .hpi_cntr").html("0"); $("#detailDesc1").bind("click keyup",function(){rvs_dd_we_handle();}); rvs_dd_we_handle('detailDesc1'); }else{$("#detailDesc1 #lbl_hpi_cntr").addClass("hidden"); $("#detailDesc2").unbind("click, keyup");}
			gebi('detailDescription1').innerHTML = title2+' (Describe Details)';
			gebi('detailDesc1').style.display = 'block';
		}
	}
}

function hpi_mask_spl_chars(str, flg_unq){
	if(typeof(str)!="undefined" && str!=""){		
		if(typeof(flg_unq)!="undefined" && flg_unq!=""){
			//str = str.replace(/!hpn!/g,"-");
			str = str.replace(/(\^c0ma\^)/g,",");
		}else{			
			//str = str.replace(/-/g, "!hpn!");
			str = str.replace(/,/g, "^c0ma^");
		}
	}
	return str;
}

function hpi_aply_mask(ar){
	var t = ar.join('~Coma~');
	t = hpi_mask_spl_chars(t);
	return t.replace(/(~Coma~)/g,",");
}

function rvs_dd_we_handle(tid){
	var did="";
	if(typeof(tid)!="undefined" && tid!=""){
		did = tid;
	}else{	
		var s = window.event.srcElement;
		var did = $(s).parents("div[id*=detailDesc]").attr("id");		
	}	
	if(typeof(did)!="undefined" && did!=""){
		/*
		var c = $("#"+did).find(":checked[type=checkbox]").length;
		var t = $("#"+did).find(":input[type!=checkbox][type!=button]").each(function(){ if($(this).val()!=""){ c+=1; } });
		
		//On Set:: One set is counter on all the boxes selected.  It should only count once for any number of boxes selected for example 2 Weeks or 2 month years is all counted as one
		var d = $("#"+did+" .ul_onset").find(":checked[type=checkbox]").length;
		var t = $("#"+did+" .ul_onset").find(":input[type!=checkbox][type!=button]").each(function(){ if($(this).val()!=""){ d+=1; } });
		if(d>0){ c = c-d ; c+=1; }
		//--
		*/
		//One point for one section
		if(did == "detailDesc1"){
		var ar = ["ul_location", "ul_onset",  "ul_doe", "ul_quality", "ul_severity", "ul_context", "ul_modi_fac", "ul_asas"]; //, "ul_pn"
		}else if (did == "detailDesc2"){
		var ar = ["ul_location", "ul_onset", "ul_doe", "ul_quality",  "ul_severity", "ul_asas", "ul_modi_fac",  "ul_medi" ]; //"ul_vision", "ul_diplopia", "ul_pt_neg", "ul_other", "ul_care_int",
		}
		c=0;
		if(ar.length>0){
			for(var x in ar){
				if(ar[x]){
					var d = $("#"+did+" ."+ar[x]+"").find(":checked[type=checkbox]").length;
					var t = $("#"+did+" ."+ar[x]+"").find(":input[type!=checkbox][type!=button]").each(function(){ if($(this).val()!=""){ d+=1; } });
					if(d>0){ c+=1; }
				}
			}
		}		
		$("#"+did+" .hpi_cntr").html(c);
	}
}

function fillArray(iddvdt){
	
	//
	if(typeof(iddvdt)=="undefined" || iddvdt==""){iddvdt="detailDesc1";}
	
	// DIV AND INPUT ELEMENTS OBJECT
		var divObj = gebi(iddvdt);
		var elem = divObj.getElementsByTagName('INPUT');
		var eleSelect = divObj.getElementsByTagName('SELECT');
		var eleTextArea = divObj.getElementsByTagName('TEXTAREA');
	// DIV AND INPUT ELEMENTS OBJECT
	
	var divName = gebi('divSelected').value;
	var selTd = gebi('tdSelected').value;
	
	for(var i=0; i<complaintHead.length; i++){		
		if(complaintHead[i] == divName){
			if(i==0) var showArr = 'complaint1';
			if(i==1) var showArr = 'complaint2';
			if(i==2) var showArr = 'complaint3';
		}
	}	
	
	var j = 0;
	if(complaint1.length<=0 || showArr == 'complaint1'){
		
		complaint1.length = 0;		
		for(var i=0; i<elem.length; i++) {
			if(elem[i].type == 'checkbox' && elem[i].checked == true){
				complaint1[j] = elem[i].value;
				j++;
			}else if(elem[i].type == 'text' && elem[i].value != ''){
				var eleName = elem[i].name;
				var eleValue = elem[i].value;
				var elementNameValueStr = eleName+'-'+eleValue;
				complaint1[j] = elementNameValueStr;
				j++;
			}
		}
		for(var i=0; i<eleSelect.length; i++){
			if(eleSelect[i] && eleSelect[i].value != ''){
				var eleSelectName = eleSelect[i].name;
				var eleSelectValue = eleSelect[i].value;
				var eleSelectNameVal = eleSelectName+'-'+eleSelectValue;
				complaint1[j] = eleSelectNameVal;
				j++;
			}
		}
		for(var i=0; i<eleTextArea.length; i++){
			if(eleTextArea[i] && eleTextArea[i].value != ''){
				var eleTextAreaName = eleTextArea[i].name;
				var eleTextAreaValue = eleTextArea[i].value;
				var eleTextAreaNameVal = eleTextAreaName+'-'+eleTextAreaValue;
				complaint1[j] = eleTextAreaNameVal;
				j++;
			}
		}
		if(complaint1.length>0){
			var textStr1 = hpi_aply_mask(complaint1);
			gebi('complaint1Text').value = textStr1;
			complaintHead[0] = divName;
			selectedHead[0] = selTd;
			titleHeadArr[0] = globTitle;
		}
		
	}else if(complaint2.length<=0 || showArr == 'complaint2'){
		
		complaint2.length = 0;		
		for(var i=0; i<elem.length; i++) {
			if(elem[i].type == 'checkbox' && elem[i].checked == true){
				complaint2[j] = elem[i].value;
				j++;
			}else if(elem[i].type == 'text' && elem[i].value != ''){
				var eleName = elem[i].name;
				var eleValue = elem[i].value;
				var elementNameValueStr = eleName+'-'+eleValue;
				complaint2[j] = elementNameValueStr;
				j++;
			}
		}
		for(var i=0; i<eleSelect.length; i++) {
			if(eleSelect[i] && eleSelect[i].value != ''){
				var eleSelectName = eleSelect[i].name;
				var eleSelectValue = eleSelect[i].value;
				var eleSelectNameVal = eleSelectName+'-'+eleSelectValue;
				complaint2[j] = eleSelectNameVal;
				j++;
			}
		}
		for(var i=0; i<eleTextArea.length; i++) {
			if(eleTextArea[i] && eleTextArea[i].value != ''){
				var eleTextAreaName = eleTextArea[i].name;
				var eleTextAreaValue = eleTextArea[i].value;
				var eleTextAreaNameVal = eleTextAreaName+'-'+eleTextAreaValue;
				complaint2[j] = eleTextAreaNameVal;
				j++;
			}
		}
		if(complaint2.length>0){
		var textStr2 = hpi_aply_mask(complaint2);
		gebi('complaint2Text').value = textStr2;		
		complaintHead[1] = divName;
		selectedHead[1] = selTd;
		titleHeadArr[1] = globTitle;
		}
		
	}else if(complaint3.length<=0 || showArr == 'complaint3'){		
		complaint3.length = 0;		
		for(var i=0; i<elem.length; i++) {
			if(elem[i].type == 'checkbox' && elem[i].checked == true){
				complaint3[j] = elem[i].value;
				j++;
			}else if(elem[i].type == 'text' && elem[i].value != ''){
				var eleName = elem[i].name;
				var eleValue = elem[i].value;
				var elementNameValueStr = eleName+'-'+eleValue;
				complaint3[j] = elementNameValueStr;
				j++;
			}
		}
		for(var i=0; i<eleSelect.length; i++) {
			if(eleSelect[i] && eleSelect[i].value != ''){
				var eleSelectName = eleSelect[i].name;
				var eleSelectValue = eleSelect[i].value;
				var eleSelectNameVal = eleSelectName+'-'+eleSelectValue;
				complaint3[j] = eleSelectNameVal;
				j++;
			}
		}
		for(var i=0; i<eleTextArea.length; i++) {
			if(eleTextArea[i] && eleTextArea[i].value != ''){
				var eleTextAreaName = eleTextArea[i].name;
				var eleTextAreaValue = eleTextArea[i].value;
				var eleTextAreaNameVal = eleTextAreaName+'-'+eleTextAreaValue;
				complaint3[j] = eleTextAreaNameVal;
				j++;
			}
		}
		if(complaint3.length>0){
		var textStr3 = hpi_aply_mask(complaint3);
		gebi('complaint3Text').value = textStr3;		
		complaintHead[2] = divName;
		selectedHead[2] = selTd;
		titleHeadArr[2] = globTitle;
		}
	}
	
	// MAKE FORM EMPTY 
		makeEmpty(elem, eleSelect, eleTextArea);
	// MAKE FORM EMPTY 
	
	if(j>0){
		//gebi(selTd).className = 'selectedComplaint';
		$("#"+selTd).addClass("rvs_dt_dd_hgt");	
	}else{	
		//if no description only then add in ccHX	
		//add in ccHX
		rvfun_setRVS_v1("", rvfun_setRVS_v2_head, "");	
	}
	
	//if(complaint1.length>0||complaint2.length>0||complaint3.length>0){			
		var texthead1 = complaintHead.join(',');
		gebi('complaintHeadText').value = texthead1;
		var selectedTexthead1 = selectedHead.join(',');
		gebi('selectedHeadText').value = selectedTexthead1;
		gebi('titleHeadText').value = titleHeadArr.join(',');	
	//}
	
	if(complaint1.length>0){		
		makeStr('1');
	}
	if(complaint2.length>0){		
		makeStr('2');		
	}
	if(complaint3.length>0){		
		makeStr('3');		
	}	
	closeWindow();	
}

function preixA_An(tmp){
	var chr1 = $.trim(tmp).substr(0,1).toLowerCase();
	if(chr1=="a"||chr1=="i"||chr1=="e"||chr1=="o"||chr1=="u"){
		tmp = "an "+tmp+"";
	}else{
		tmp = "a "+tmp+"";
	}
	return tmp;
}

function rvs_getEntryBegining(tcompHd, tHead){
	var ret = "";
	var n= $("#"+tcompHd).attr("name");
	var v= $.trim($("#"+tcompHd).val());
	var type = $("#"+tcompHd).prop("type");
	if(type!="text"){v=v.toLowerCase(); }	
	if($("label[for="+tcompHd+"]").length>0){var v1 = $("label[for="+tcompHd+"]").html().toLowerCase(); if(v1!="yes"){ v=v1; }}
	if(type!="text"){v=v.replace(/\b(fb|ha|vf|nsaids|amd|yag|tv)\b/g, function(c) { return c.toUpperCase(); });	}
	
	
	if(typeof(n)!="undefined" && n!=""){
		var pe= "The patient experiences ";
		if(tcompHd=="div38" || tcompHd=="div39" || tcompHd=="div40" || tcompHd=="div41" || tcompHd=="div42" ||
			tcompHd=="div47" || tcompHd=="div48" || tcompHd=="div54" ||
			n.indexOf("neuroTAS")!=-1 || n.indexOf("THAYesNo")!=-1 || n.indexOf("neuroVisLoss")!=-1 ){	
				v=v.replace(/fever/,"fever associated with temporal arteritis").replace(/chills/,"chills associated with temporal arteritis").replace(/new onset of joint or muscle aches/,"a new onset of joint or muscle aches");
				if(v.search(/\bha\b/)!=-1){ v=v.replace(/\bha\b/,"headaches"); }
				
				ret = pe + v;
			}			
		else if(n.indexOf("vpDis")!=-1 || n.indexOf("vpMidDis")!=-1 || n.indexOf("vpNear")!=-1 ){ ret = "The patient complains of difficulty when " + v;  }
		else if(n.indexOf("vpOther")!=-1){ ret = "The patient complains of " + v;  }
		else if(n.indexOf("irrOcu")!=-1){ 				
				v=v.replace(/burning/,"burning eyes").replace(/redness/,"red eyes").replace(/photophobia/,"light sensitivity");				
				if(v.search(/itching/)!=-1){var r="itching eyes";var m = $(":checked[name=elem_irrOcuItchingType]").val();if(m=="Mild Itching"){ r="mildly "+r; }else if(m=="Severe Itching"){ r="severely "+r; }else if(m=="Seasonal allergies Itching"){ r=r+" due to seasonal allergies"; }v=v.replace(/itching/,r);}
				if(v.search(/pressure sensation/)!=-1){ var m = $.trim($(":checked[name=elem_irrOcuPresSensType]").val()); if(typeof(m)!="undefined"&&m!=""){var r="pressure sensation and "+m.toLowerCase(); v=v.replace(/pressure sensation/,r)}}				
				ret = pe + v;  
		}
		else if(n.indexOf("fuPostOp")!=-1)	{ var tmp = v; if(v=="lasik"||v=="glaucoma laser"||v=="retina laser"){v+=" surgery";} ret = "The patient is returning after "+tmp+"";  }
		else if(n.indexOf("fuFollowUp")!=-1)	{ var tmp = preixA_An(v);  ret = "The patient is returning for "+tmp+" follow-up";  }
		else if(n.indexOf("vpGlare")!=-1)	{ ret = "The patient complains of "+v+" glare causing poor vision";  }
		else if(n.indexOf("postSegSpots")!=-1)	{ ret = pe+v;  }
		else if(n.indexOf("postSegFL")!=-1)	{ v=v.replace(/same/,"the same amount of").replace(/new onset/,"a new onset of");	ret = pe+v+" flashing lights";  }
		else if(n.indexOf("irrLidsExt")!=-1)	{ ret = pe+v+" external lids";  }
		else if(n.indexOf("lidsItchingYesNo")!=-1)	{ ret = pe+v+"";  }
		else if(n.indexOf("postSegFloat")!=-1)	{ v=v.replace(/same/,"the same amount of").replace(/new onset/,"a new onset of"); ret = pe+v+" floaters";  }
		else if(n.indexOf("postSegAmsler")!=-1)	{ ret = "The patient sees "+preixA_An(v)+" amsler grid";  }
		else if(n.indexOf("neuroDblVis")!=-1)	{ ret = pe+v+" double vision";  }
		else if(n.indexOf("neuroMigHead")!=-1)	{
				var fhx="";
				if(v.search(/family history of migraines/)!=-1){ v=v.replace(/family history of migraines(,\s*)?/g,"");  fhx="The patient has a family history of migraines"; };
				v=v.replace(/photosensitive/,"light sensitivity");
				if(v.search(/\bha\b/)!=-1){ v=v.replace(/\bha\b/,"headache");}
				if(v=="blurred vision"||v=="loss of part of vision"||v=="jagged/jig jag lines"||v=="constriction of vision"||v=="flashing lights"||v=="light sensitivity"){v+=" associated with migraines";}
				v=v.replace(/1\/2 hours/g,"a half hour");
				if(fhx!=""){  ret =""+fhx; }else{ ret = pe+v; }
		}
		else if(n.indexOf("neuroHeadaches")!=-1)	{ v=v.replace(/wake up with headaches/,"upon waking up");    ret = pe+"headaches "+v+"";  }
	}	
	return ret;
}

function rvs_getStrFirstLine(str1Concat,tHead,tLocat,tcompHd){	
	var strBegin = rvs_getEntryBegining(tcompHd, tHead);
	
	if(tHead.indexOf("Post op")!=-1){
		//var tmp = tHead.replace(/Post op/g,"");
		//tmp = $.trim(tmp);
		//var str1Concat = str1Concat+'The patient returns today for '+tmp+' status post operative procedure follow-up in the '+tLocat+'. ';	
		//var str1Concat = str1Concat+'Returns today for '+tmp+' status post operative procedure follow-up in the '+tLocat+'. ';	
		//var str1Concat = str1Concat+strBegin+' in '+tLocat+'.  ';
		var str1Concat = str1Concat+strBegin+'.  ';
	}else if(tHead.indexOf("Follow Up")!=-1){
		///var tmp = tHead.replace(/Follow Up/g,"");
		//tmp = $.trim(tmp);
		//tmp = preixA_An(tmp);				
		//var str1Concat = str1Concat+'Returns today for '+tmp+' follow-up in '+tLocat+'. ';	
		//var str1Concat = str1Concat+strBegin+' follow-up in '+tLocat+'.  ';
		var str1Concat = str1Concat+strBegin+'.  ';
	}else{		
		//var str1Concat = str1Concat+strBegin+', afflicting '+tLocat.toLowerCase()+'.  '; //strConcat1Arr.join(', ')
		var str1Concat = str1Concat+strBegin+'.  '; //strConcat1Arr.join(', ')
	}
	return str1Concat;
}

function rvs_andLastComma(tmp){
	if(tmp.indexOf("(reading, computer)")!=-1){
		tmp = tmp.replace("(reading, computer)","(readingcomma computer)");
	}
	
	tmp = rvfun_removeLastComma(tmp);
	tmp = tmp.replace(/,(?=[^,]*$)/, ' and');
	
	if(tmp.indexOf("(readingcomma computer)")!=-1){
		tmp = tmp.replace("(readingcomma computer)","(reading, computer)");
	}
	
	return tmp;
}

//mk lids to uper case
function hpi_mkLids2Upr(tmpstr){
	if(tmpstr!=""){		
		tmpstr=tmpstr.replace(/\brul\b/g, "RUL");		
		tmpstr=tmpstr.replace(/\brll\b/g, "RLL");
		tmpstr=tmpstr.replace(/\blul\b/g, "LUL");
		tmpstr=tmpstr.replace(/\blll\b/g, "LLL");	
	}
	return tmpstr;
}


function makeStr(id){	
	
	var strConcat1Arr = new Array();
	var strConcat4Arr = new Array();
	var strConcat5Arr = new Array();
	var strConcat2Arr = new Array();
	var strConcat6Arr = new Array();
	var strConcat7Arr = new Array();
	var strConcat8Arr = new Array();
	
	var strConcat9Arr = new Array();
	var strConcat10Arr = new Array();
	var strConcat11Arr = new Array();
	var strConcat12Arr = new Array();
	var strConcat13Arr_1 = new Array();
	var strConcat13Arr_2 = new Array();
	var strConcat14Arr = new Array();	
	
	//var ccHxStr = gebi('elem_ccHx').innerHTML;
	var ccHxStr = gebi('elem_ccompliant').value;
	var k = l = m = n = o = p = q = r=s=t=u= 0;
	var dummyArr = new Array();
	var dummyArr_titleHead = titleHeadArr;
	var f_rvs_dtdesc_2=0; f_rvs_dtdesc_3=0;
	
	var id_x = id-1;
	var complaint_cur = [];	
	complaint_cur = eval('complaint'+id);
	if(complaint_cur.length>0){
		dummyArr.length = complaint_cur.length;
		for(i=0;i<complaint_cur.length;i++){
			dummyArr[i] = hpi_mask_spl_chars(complaint_cur[i], 1);
		}
		//
		if(dummyArr_titleHead[id_x].indexOf("Follow Up")!=-1){f_rvs_dtdesc_2=1;f_rvs_dtdesc_3=1;}
		else if(dummyArr_titleHead[id_x].indexOf("Post op")!=-1){f_rvs_dtdesc_2=1;}
	}
	
	//alert(id+" - "+f_rvs_dtdesc_2);
	//var myWindow = window.open("","MsgWindow","width=200,height=100, resizable=1");
	//myWindow.document.write(""+dummyArr+"<br/>--<br/>"+complaintHead+"<br/>--<br/>"+selectedHead+"<br/>--<br/>"+titleHeadArr);	
	
	if(dummyArr.length>0){
		for(i=0;i<dummyArr.length;i++){
			for(j=0;j<str1Array.length;j++){
				if(dummyArr[i].indexOf('selLocOpt')!=-1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					var selLocOpt_str= dummyArr[i].substr(dashPos);					
				}else if(dummyArr[i].indexOf('selLocOther')!=-1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					var selLocOther_str = dummyArr[i].substr(dashPos);					
				}else if(dummyArr[i] == str1Array[j]){
					var tmp = dummyArr[i];
					if(dummyArr[i]!="Both Eyes"){ tmp = "the "+tmp; }
					strConcat1Arr[k] = tmp;					
					k++;
				}
			}			
			
			for(j=0;j<str4Array.length;j++){
				
				if(dummyArr[i].indexOf('elem_os_othr_date')!=-1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					var onsetSPDt = dummyArr[i].substr(dashPos);
				}
				
				//				
				if(str4Array[j]=="Day" || str4Array[j]=="Week" || str4Array[j]=="Month" || str4Array[j]=="Year"){
					if(dummyArr[i].indexOf(str4Array[j]) != -1 && dummyArr[i].indexOf(str4Array[j]+"s") == -1){	/*OK*/}else{continue;}
				}				
				
				if(dummyArr[i].indexOf(str4Array[j]) != -1){					
					if(dummyArr[i].indexOf('selectNo')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var str_onset_dd = dummyArr[i].substr(dashPos);
					}
					if(dummyArr[i].indexOf('selectDate')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+1;
						var str2 = dummyArr[i].substr(dashPos);
						if(typeof(str_onset_dd)=="undefined"){str_onset_dd="";}else{str_onset_dd=str_onset_dd+" ";}
						var str_onset_dd = str_onset_dd+''+str2;
					}					
					l++;
				}
			}
			
			var TimingStr=null;
			
			for(j=0;j<str5Array.length;j++){
				if(dummyArr[i] == str5Array[j]){					
					var tmp = dummyArr[i].split("~");					
					if(typeof(tmp[1])!="undefined" && tmp[1]!=""){						
						strConcat5Arr[m] = ""+tmp[1];	
					}else{
						strConcat5Arr[m] = dummyArr[i];
					}					
					m++;
				}
				/*
				if(dummyArr[i].indexOf('otherTiming')!=-1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					var TimingStr = dummyArr[i].substr(dashPos);
				}
				if(dummyArr[i].indexOf('onSetDate')!=-1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					strConcat5Arr[m] = dummyArr[i].substr(dashPos);
				}
				*/
			}
			
			for(j=0;j<str2Array.length;j++){
				if(dummyArr[i] == str2Array[j]){
					var tmp = dummyArr[i].split("~");
					
					if(typeof(tmp[1])!="undefined" && tmp[1]!=""){
						strConcat2Arr[n] = ""+tmp[1];	
					}else{
						strConcat2Arr[n] = dummyArr[i];
					}					
					n++;
				}
				if(dummyArr[i].indexOf('otherQty')!=-1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					var quantityStr = dummyArr[i].substr(dashPos);
				}
			}

			for(j=0;j<str3Array.length;j++){				
				if(dummyArr[i].indexOf('rvs_other_svrity')!=-1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					var rvs_other_svrity_str = dummyArr[i].substr(dashPos);
				}else if(dummyArr[i].indexOf('scale1To10')!=-1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					var scaleStr = dummyArr[i].substr(dashPos);
				}else{
					if(dummyArr[i] == str3Array[j]){
						var tmp = dummyArr[i].split("~");
						if(typeof(tmp[1])!="undefined" && tmp[1]!=""){
							strConcat7Arr[p] = ""+tmp[1];	
						}else{
							strConcat7Arr[p] = dummyArr[i];
						}
						p++;
					}
				}
			}
			for(j=0;j<str6Array.length;j++){
				if(dummyArr[i] == str6Array[j]){
					strConcat6Arr[o] = dummyArr[i];
					o++;
				}
				if(dummyArr[i].indexOf('otherContext')!=-1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					var ContextStr = dummyArr[i].substr(dashPos);
				}
			}
			
			for(j=0;j<str7Array.length;j++){
				if(dummyArr[i].indexOf('otherSymptoms')!=-1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					var SymptomsStr = dummyArr[i].substr(dashPos);
				}
				if(dummyArr[i] == str7Array[j]){					
					var tmp = dummyArr[i].split("~");
					if(typeof(tmp[1])!="undefined" && tmp[1]!=""){
						strConcat8Arr[q] = ""+tmp[1];	
					}else{					
						strConcat8Arr[q] = dummyArr[i];
					}
					q++;
				}
			}
			for(j=0;j<1;j++){
				if(dummyArr[i].indexOf('makesBetter')!=-1 || dummyArr[i].indexOf('makesWorse')!=-1){
					if(dummyArr[i].indexOf('makesBetter') != -1){
						var dashPos = dummyArr[i].indexOf('-') + 1;
						var strBetter = dummyArr[i].substr(dashPos);
					}
					if(dummyArr[i].indexOf('makesWorse') != -1){
						var dashPos = dummyArr[i].indexOf('-') + 1;
						var strWorse = dummyArr[i].substr(dashPos);
					}
				}
				
			}
			
			//var otherFactorsStr="";
			
			for(j=0;j<str13Array.length;j++){
				if(dummyArr[i].indexOf('otherFactors') != -1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					var otherFactorsStr = dummyArr[i].substr(dashPos);
				}else if(dummyArr[i] == str13Array[j]){
					var tmp = dummyArr[i];
					if(dummyArr[i].indexOf("comma")!=-1){						
						var tmp = ""+dummyArr[i].replace(/(comma)/g,",");						
					}
					var tmp = tmp.split("~");
					var tmp2="";
					if(typeof(tmp[1])!="undefined" && tmp[1]!=""){
						tmp2 = $.trim(""+tmp[1]);	//strConcat13Arr[strConcat13Arr.length]
					}else{					
						tmp2 = $.trim(tmp);
					}
					//
					if(tmp2=="inside" || tmp2== "outdoors" || tmp2== "night" || tmp2== "distance vision than near" || tmp2== "near vision than distance" ||
						tmp2 =="with intensive visual activity (reading, computer)" ||
						tmp2 =="with daily activities"){
						strConcat13Arr_1[strConcat13Arr_1.length]=tmp2;	
					}else{
						strConcat13Arr_2[strConcat13Arr_2.length]=tmp2;
					}
				}
			}
			
			for(j=0;j<str14Array.length;j++){
				if(dummyArr[i].indexOf('other_par_neg')!=-1){
					var dashPos = dummyArr[i].indexOf('-')+ 1;
					var other_par_neg_str = dummyArr[i].substr(dashPos);
				}
				
				if(dummyArr[i] == str14Array[j]){
				var tmp = dummyArr[i];	
					
				var tmp = tmp.split("~");
				if(typeof(tmp[1])!="undefined" && tmp[1]!=""){
					strConcat14Arr[strConcat14Arr.length] = ""+tmp[1];
				}else{
					strConcat14Arr[strConcat14Arr.length]  = tmp[0];
				}
				}
			}
			
			for(j=0;j<str9Array.length;j++){
					if(dummyArr[i].indexOf('otherDoe')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var otherDoeStr = dummyArr[i].substr(dashPos);
					}
					if(dummyArr[i].indexOf('onSetDate')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						if($.trim(dummyArr[i].substr(dashPos))!=""){
						var dateDoeStr	 = ""+dummyArr[i].substr(dashPos);
						}						
					}else if(dummyArr[i].indexOf('selectDoE')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var selectDoEStr = dummyArr[i].substr(dashPos);
					}else if(typeof(dummyArr[i])!="undefined" && dummyArr[i] == str9Array[j]){
						var tmp = dummyArr[i].split("~");
						if(typeof(tmp[1])!="undefined" && tmp[1]!=""){
							strConcat9Arr[r] = ""+tmp[1];	
						}else{
							strConcat9Arr[r] = dummyArr[i];
						}
						r++;
					}
			}
			
			//follow up section only
			if(f_rvs_dtdesc_2==1){				

				for(j=0;j<1;j++){
					if(dummyArr[i].indexOf('rvs_painrelievedby')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var rvs_painrelievedby_str = dummyArr[i].substr(dashPos);
					}else if(dummyArr[i].indexOf('otherFollowCareInstruct')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var otherFollowCareInstruct_str = dummyArr[i].substr(dashPos);
					}else if(dummyArr[i].indexOf('rvs_followup_detail_other')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var rvs_followup_detail_other_str = dummyArr[i].substr(dashPos);
					}else if(dummyArr[i].indexOf('elem_sp_surtype')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var onsetSPStr = dummyArr[i].substr(dashPos);
					}else if(dummyArr[i].indexOf('elem_sp_surdate')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var onsetSPDt = dummyArr[i].substr(dashPos);
					}else if(dummyArr[i].indexOf('other_onset')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var other_onset_str = dummyArr[i].substr(dashPos);
					}
				}					
				
				for(j=0;j<str10Array.length;j++){
					if(dummyArr[i].indexOf('rvsdet_otherVision')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var rvsdet_otherVisionStr = dummyArr[i].substr(dashPos);
					}else if(dummyArr[i] == str10Array[j]){
						strConcat10Arr[s] = dummyArr[i];
						s++;
					}
				}
				
				for(j=0;j<str11Array.length;j++){					
					if(dummyArr[i] == str11Array[j]){
						var tmp = dummyArr[i].split("~");
						if(typeof(tmp[1])!="undefined" && tmp[1]!=""){
							strConcat11Arr[t] = (tmp[1]=="None") ? "No" : ""+tmp[1];
						}else{							
							strConcat11Arr[t] = (dummyArr[i]=="None") ? "No" : dummyArr[i];
						}
						t++;
					}
				}
				
				for(j=0;j<str12Array.length;j++){
					if(dummyArr[i].indexOf('rvs_ranoutmeds')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var rvs_ranoutmeds_str = dummyArr[i].substr(dashPos);
					}else if(dummyArr[i].indexOf('rvs_needs_med_refill')!=-1){
						var dashPos = dummyArr[i].indexOf('-')+ 1;
						var rvs_needs_med_refill = dummyArr[i].substr(dashPos);
					}else if(dummyArr[i] == str12Array[j]){
						strConcat12Arr[u] = dummyArr[i];
						u++;
					}
				}				
			}
		}
	}
	
	//complaint --
	if(complaint_cur.length>0){

		if(z_hpi_bullet_format == 1){
			//9679. 9711, 9673, 9678,11044::: 9673, 9678,::: 9675, 9679	
			var CIRCLE = String.fromCharCode(9675);
			var BULLET = String.fromCharCode(9679);
			var HYPHEN = String.fromCharCode(8209); //8209
			
			var NTAB = '\n '; //32
			var SPACE = '  ';	//
			CIRCLE = NTAB+CIRCLE+" ";
			HYPHEN = SPACE+HYPHEN+" ";
			
			//var str1Concat = ' '+BULLET+' Item1\t\t'+HYPHEN+'Sub1 ';
			///str1Concat += '\n '+CIRCLE+' Item2\t\t'+HYPHEN+'Sub1 ';
			
			var str1Concat = ''+BULLET+' '+titleHeadArr[id_x];	
		}else{
			var str1Concat = ''+id+'. ';
			if(id>1){str1Concat="\r\n"+str1Concat;}	
			
			//unconditional
			str1Concat = rvs_getStrFirstLine(str1Concat,titleHeadArr[id_x],'',complaintHead[id_x]);	
		}		
		
		//Location
		var tmpstr_loc="";
		if(f_rvs_dtdesc_3==1){
			
			//Follow-up should be:::The patient is returning for a [issue] [on set] follow-up of [Location]. e.g 1.       The patient is returning for a blepharitis 6 months follow-up 
			//onset
			var onsetSPStr_tmp =  other_onset_str_tmp = "";
			if(onsetSPStr || onsetSPDt || other_onset_str || (typeof(str_onset_dd)!='undefined'&&str_onset_dd!="")){			
				if(typeof(onsetSPStr)!="undefined" && onsetSPStr!=""){  var splbl = "";  onsetSPStr_tmp = splbl+""+onsetSPStr.trim() ; } //(f_rvs_dtdesc_3==1) ? "follow up" : "S/P";
				if(typeof(other_onset_str)!="undefined" && other_onset_str!=""){ other_onset_str_tmp = ""+other_onset_str.trim() ; }
				
				var tmp_fu_onset = ""; //"Status post "+$("#"+complaintHead[0]).val().toLowerCase()+"";
				if(typeof(onsetSPDt)!="undefined" && onsetSPDt!=""){ tmp_fu_onset += ""+onsetSPDt+" "; }
				if(typeof(str_onset_dd)!='undefined'&&str_onset_dd!=""){ tmp_fu_onset +=""+str_onset_dd.toLowerCase()+" "; }
				//tmp_fu_onset += ": ";
					
				if(onsetSPStr_tmp!="" || other_onset_str_tmp!=""){ if(onsetSPStr_tmp!="" && other_onset_str_tmp!=""){ onsetSPStr_tmp = onsetSPStr_tmp+" and "+other_onset_str_tmp; other_onset_str_tmp=""; }
						tmp_fu_onset += ""+onsetSPStr_tmp+""+other_onset_str_tmp+" ";}
				
				if(z_hpi_bullet_format == 1){
					if(onsetSPStr_tmp!=""){ tmp_fu_onset.replace(onsetSPStr_tmp,"follow up "+onsetSPStr_tmp)  }					
					tmpstr_loc += ''+CIRCLE+'Onset'+HYPHEN+tmp_fu_onset;
					
				}else{		
						
					if(str1Concat.indexOf("follow-up")!=-1){
						tmp_fu_onset+="follow-up";
						str1Concat = str1Concat.replace(/follow\-up/,tmp_fu_onset);
					}
				
				}
			}			
			
			//location
			if(strConcat1Arr.length>0 || (selLocOpt_str&&selLocOpt_str!="") || (selLocOther_str&&selLocOther_str!="")){			
				var tmpstr = (strConcat1Arr.length>0) ? ""+strConcat1Arr.join(', ') : "" ;
				if(selLocOpt_str&&selLocOpt_str!=""){ if(tmpstr!=""){tmpstr+=", ";} tmpstr+=selLocOpt_str;}
				tmpstr = tmpstr.toLowerCase();				
				tmpstr = hpi_mkLids2Upr(tmpstr);
				if(selLocOther_str&&selLocOther_str!=""){ if(tmpstr!=""){tmpstr+=", ";} tmpstr+=selLocOther_str;}			
				tmpstr = rvs_andLastComma(tmpstr);
				
				tmpstr=$.trim(tmpstr);
				
				//tmpstr+= tmpstr.match(/(Eyes|lids)$/i) ? " are " : " is ";
				//tmpstr+= "affected.  ";			
				//
				//tmpstr = tmpstr[0].toUpperCase() + tmpstr.slice(1);
				
				//if(f_rvs_dtdesc_2==1){	var str1Concat = str1Concat+tmpstr;	}else{ tmpstr_loc=tmpstr;  }	
				
				if(z_hpi_bullet_format == 1){
					
					tmpstr_loc = ''+CIRCLE+'Location'+HYPHEN+tmpstr+tmpstr_loc;
					str1Concat += tmpstr_loc;
					tmpstr_loc="";
					
				}else{
				
					if(str1Concat.indexOf("follow-up")!=-1){
						tmpstr ="follow-up of "+tmpstr;	
						str1Concat = str1Concat.replace(/follow\-up/,tmpstr);
					}
				
				}
			}
			
		}else{	
		if(strConcat1Arr.length>0 || (selLocOpt_str&&selLocOpt_str!="") || (selLocOther_str&&selLocOther_str!="")){
			
			var tmpstr = (strConcat1Arr.length>0) ? ""+strConcat1Arr.join(', ') : "" ;
			if(selLocOpt_str&&selLocOpt_str!=""){ if(tmpstr!=""){tmpstr+=", ";} tmpstr+=selLocOpt_str;}
			tmpstr = tmpstr.toLowerCase();
			tmpstr = hpi_mkLids2Upr(tmpstr);
			if(selLocOther_str&&selLocOther_str!=""){ if(tmpstr!=""){tmpstr+=", ";} tmpstr+=selLocOther_str;}			
			tmpstr = rvs_andLastComma(tmpstr);
			
			tmpstr=$.trim(tmpstr);
			
			if(z_hpi_bullet_format == 1){
					
				str1Concat += ''+CIRCLE+'Location'+HYPHEN+tmpstr;
					
			}else{
			
				tmpstr+= tmpstr.match(/(Eyes|lids)$/i) ? " are " : " is ";
				tmpstr+= "affected.  ";			
				//
				tmpstr = tmpstr[0].toUpperCase() + tmpstr.slice(1);
				
				if(f_rvs_dtdesc_2==1){	var str1Concat = str1Concat+tmpstr;	}else{ tmpstr_loc=tmpstr;  }

			}	
			
			//var str1Concat = rvs_getStrFirstLine(str1Concat,titleHeadArr[0],tmpstr,complaintHead[0]);
			/*
			if(titleHeadArr[0].indexOf("Post op")!=-1){
				var tmp = titleHeadArr[0].replace(/Post op/g,"");
				tmp = $.trim(tmp);
				var str1Concat = str1Concat+'The patient returns today for '+tmp+' status post operative procedure follow-up in the '+strConcat1Arr.join(', ')+'. ';	
			}else if(titleHeadArr[0].indexOf("Follow Up")!=-1){
				var tmp = titleHeadArr[0].replace(/Follow Up/g,"");
				tmp = $.trim(tmp);
				tmp = preixA_An(tmp);				
				var str1Concat = str1Concat+'The patient returns today for '+tmp+' follow-up in the '+strConcat1Arr.join(', ')+'. ';	
			}else{
				var str1Concat = str1Concat+'Patient complains of '+titleHeadArr[0]+' in '+strConcat1Arr.join(', ')+'. ';
			}*/
		}
		}//
		
		//Onset : Followup
		if(f_rvs_dtdesc_3==1){}
		else if(f_rvs_dtdesc_2==1){ //Post Op
		var onsetSPStr_tmp =  other_onset_str_tmp = "";
		if(onsetSPStr || onsetSPDt || other_onset_str || (typeof(str_onset_dd)!='undefined'&&str_onset_dd!="")){			
			if(typeof(onsetSPStr)!="undefined" && onsetSPStr!=""){  var splbl = (f_rvs_dtdesc_3==1) ? "follow up" : "S/P";   onsetSPStr_tmp = splbl+" "+onsetSPStr.trim() ; }
			if(typeof(other_onset_str)!="undefined" && other_onset_str!=""){ other_onset_str_tmp = ""+other_onset_str.trim() ; }
			if(onsetSPStr_tmp!="" || other_onset_str_tmp!=""){ if(onsetSPStr_tmp!="" && other_onset_str_tmp!=""){ onsetSPStr_tmp = onsetSPStr_tmp+" and "+other_onset_str_tmp; other_onset_str_tmp=""; }}
			
			if(z_hpi_bullet_format == 1){
				
				var tmp_fu_onset = "";
				if(typeof(onsetSPDt)!="undefined" && onsetSPDt!=""){ tmp_fu_onset += " on "+onsetSPDt+""; }
				if(typeof(str_onset_dd)!='undefined'&&str_onset_dd!=""){ tmp_fu_onset +=" "+str_onset_dd.toLowerCase()+" ago"; }
				if(onsetSPStr_tmp!="" || other_onset_str_tmp!=""){ tmp_fu_onset += " "+onsetSPStr_tmp+""+other_onset_str_tmp; }
				
				str1Concat += ''+CIRCLE+'Onset'+HYPHEN+tmp_fu_onset;					
			}else{	

				var tmp_fu_onset = "Status post "+$("#"+complaintHead[id_x]).val().toLowerCase()+"";
				if(typeof(onsetSPDt)!="undefined" && onsetSPDt!=""){ tmp_fu_onset += " on "+onsetSPDt+""; }
				if(typeof(str_onset_dd)!='undefined'&&str_onset_dd!=""){ tmp_fu_onset +=", which began "+str_onset_dd.toLowerCase()+" ago"; }
				tmp_fu_onset += ": ";
					
				if(onsetSPStr_tmp!="" || other_onset_str_tmp!=""){ 
						tmp_fu_onset += "Onset was "+onsetSPStr_tmp+""+other_onset_str_tmp+".  ";}
				
				var str1Concat = str1Concat+tmp_fu_onset;
			}		
		}
		}
		//Onset : other		
		/*
		if(TimingStr && (strConcat5Arr.length>0 || onsetSPStr_tmp != "" || other_onset_str_tmp != "")){
			//var str1Concat = str1Concat+'Onset of '+titleHeadArr[0]+' has been '+strConcat5Arr.join(', ')+' and '+TimingStr+'. ';
			var tmp = strConcat5Arr.join(', ').toLowerCase();
			if(onsetSPStr_tmp!=""){ if(tmp!=""){tmp+=", ";} tmp+=""+onsetSPStr_tmp; }
			if(other_onset_str_tmp!=""){ if(tmp!=""){tmp+=", ";} tmp+=""+other_onset_str_tmp; }
			var str1Concat = str1Concat+'Onset was '+tmp+' and '+TimingStr+'. ';			
		}else{
			if(strConcat5Arr.length>0 || onsetSPStr_tmp != "" || other_onset_str_tmp != ""){
				var tmp = strConcat5Arr.join(', ').toLowerCase();
				if(onsetSPStr_tmp!=""){ if(tmp!=""){tmp+=", ";} tmp+=""+onsetSPStr_tmp; }
				if(other_onset_str_tmp!=""){ if(tmp!=""){tmp+=", ";} tmp+=""+other_onset_str_tmp; }
				tmp = rvs_andLastComma(tmp);
				//var str1Concat = str1Concat+'Onset of '+titleHeadArr[0]+' has been '+strConcat5Arr.join(', ')+'. ';
				var str1Concat = str1Concat+'Onset was '+tmp+'. ';
			}	
			if(TimingStr)
				//var str1Concat = str1Concat+'Onset of '+titleHeadArr[0]+' has been '+TimingStr+'. ';
				var str1Concat = str1Concat+'Onset was '+TimingStr+'. ';
		}
		*/
		if(f_rvs_dtdesc_2!=1){
		if(onsetSPDt || (typeof(str_onset_dd)!='undefined'&&str_onset_dd!="") || strConcat5Arr.length>0 ){
			//var str1Concat = str1Concat+'Which began '+str_onset_dd.toLowerCase()+' ago. ';
			//strConcat5Arr[strConcat5Arr.length] = ""+str ;
			if(typeof(onsetSPDt)!="undefined" && onsetSPDt!=""){ var tmp_fu_onset = "since "+onsetSPDt; strConcat5Arr[strConcat5Arr.length]=tmp_fu_onset; }	
			//if((typeof(str_onset_dd)!='undefined'&&str_onset_dd!="")){	str_onset_dd = "for "+str_onset_dd; strConcat5Arr[strConcat5Arr.length]=str_onset_dd;}
			if((typeof(str_onset_dd)!='undefined'&&str_onset_dd!="")){	str_onset_dd = ""+str_onset_dd+" ago"; strConcat5Arr[strConcat5Arr.length]=str_onset_dd;}
			var tmp = strConcat5Arr.join(', ').toLowerCase();
			tmp = tmp.replace("many years", "many years ago");
			tmp = tmp.replace("patient unsure", "for an unknown amount of time");
			tmp = rvs_andLastComma(tmp);	
			
			if(z_hpi_bullet_format == 1){
					
				str1Concat += ''+CIRCLE+'Onset'+HYPHEN+tmp;
					
			}else{
			
				str1Concat = $.trim(str1Concat); str1Concat = str1Concat.replace(/\.$/g,", ");			
				//var str1Concat = str1Concat+'Symptoms have been experienced '+tmp+'. ';
				var str1Concat = str1Concat+'which began '+tmp+'.  ';
				
			}			
		}
		//location: others
		if(tmpstr_loc!=""){
			if(z_hpi_bullet_format == 1){
					
				str1Concat += ''+CIRCLE+'Location'+HYPHEN+tmpstr_loc;
					
			}else{
			
				var str1Concat = str1Concat+tmpstr_loc;
				
			}			
		}	
		}
		
		// Duration Of Episode --
		if(selectDoEStr || strConcat9Arr.length>0||dateDoeStr||otherDoeStr){
			var tmp = strConcat9Arr.length>0 ? ""+strConcat9Arr.join(', ').toLowerCase() : "";
			if(typeof(otherDoeStr)!="undefined" && otherDoeStr!=""){ if(tmp!=""){tmp+=", ";} tmp+=""+otherDoeStr; }
			var selectDoEStr = (typeof(selectDoEStr)!="undefined" && selectDoEStr!="") ? "lasts several "+selectDoEStr.toLowerCase() : "";
			if(selectDoEStr!=""){ if(tmp!=""){tmp+=" and ";} selectDoEStr = ""+selectDoEStr; }else if(strConcat9Arr.length>1){ tmp = rvs_andLastComma(tmp);   }
			if(z_hpi_bullet_format == 1){				
				if(typeof(dateDoeStr)!="undefined" && dateDoeStr!=""){ dateDoeStr="Date "+dateDoeStr+" "; }else{dateDoeStr="";}
				str1Concat += ''+CIRCLE+'Duration of Episodes'+HYPHEN+dateDoeStr+tmp+selectDoEStr;
					
			}else{				
				if(typeof(dateDoeStr)!="undefined" && dateDoeStr!=""){ dateDoeStr="Beginning in "+dateDoeStr+", the episode is "; }else{dateDoeStr="The episode is ";}
				var str1Concat = str1Concat+dateDoeStr+tmp+selectDoEStr+".  ";
			}
		}
		//--		
		
		//Quality
		if(f_rvs_dtdesc_2==1){
			if(quantityStr){	strConcat2Arr[strConcat2Arr.length] = quantityStr;}
			if(strConcat2Arr.length>0){
				var tmp = strConcat2Arr.join(', ').toLowerCase();
				tmp = rvs_andLastComma(tmp);	
				var tmp_fu_qlity1 = "", tmp_fu_qlity2 ="", tmp_fu_qlity3 ="";
				for(var i in strConcat2Arr){
					if(strConcat2Arr[i]=="Decreased" || strConcat2Arr[i]=="Increased" || strConcat2Arr[i]=="Initially improved then worsened" || strConcat2Arr[i]=="Resolved"||strConcat2Arr[i]=="Feels improvement"){
						if(tmp_fu_qlity1!=""){tmp_fu_qlity1+=", ";}
						tmp_fu_qlity1 +=strConcat2Arr[i]; 
					}else if(strConcat2Arr[i]=="No changes noted"){
						if(tmp_fu_qlity3!=""){tmp_fu_qlity3+=", ";}
						tmp_fu_qlity3 +=strConcat2Arr[i]; 
					}else{
						if(tmp_fu_qlity2!=""){tmp_fu_qlity2+=", ";}
						tmp_fu_qlity2 +=strConcat2Arr[i]; 
					}
				}
				
				//
				var tmp_fu_qlity="";
				if(tmp_fu_qlity1 !="" || tmp_fu_qlity2 !="" || tmp_fu_qlity3 !=""){
					if(z_hpi_bullet_format == 1){
						
						str1Concat += ''+CIRCLE+'Quality'+HYPHEN+tmp;
							
					}else{
					
						tmp_fu_qlity = "Since the last visit, the affected area ";
						if(tmp_fu_qlity1 !=""){ tmp_fu_qlity1=tmp_fu_qlity1.toLowerCase().trim();   tmp_fu_qlity1= rvs_andLastComma(tmp_fu_qlity1); tmp_fu_qlity +=tmp_fu_qlity1;}
						if(tmp_fu_qlity3 !=""){ if(tmp_fu_qlity1 !=""){tmp_fu_qlity +=" and ";}  tmp_fu_qlity3=tmp_fu_qlity3.toLowerCase().trim();
							tmp_fu_qlity3= rvs_andLastComma(tmp_fu_qlity3); tmp_fu_qlity += "has "+tmp_fu_qlity3;
						}
						if(tmp_fu_qlity2 !=""){ if(tmp_fu_qlity1 !=""||tmp_fu_qlity3 !=""){tmp_fu_qlity +=" and ";}  tmp_fu_qlity2=tmp_fu_qlity2.toLowerCase().trim();
							tmp_fu_qlity2= rvs_andLastComma(tmp_fu_qlity2); tmp_fu_qlity += "is "+tmp_fu_qlity2;
						}
						var str1Concat = str1Concat+""+tmp_fu_qlity+".  ";
					}
				}
			}	
			
			//var str1Concat = str1Concat+'The patient describes '+tmp+'  pain symptoms  affecting their eyes/vision.';
		}else{		
		if(quantityStr && strConcat2Arr.length>0){
			var tmp = strConcat2Arr.join(', ').toLowerCase().trim();
			tmp=tmp.replace(/sharp/, "sharp pain");
			tmp=tmp.replace(/stabbing/, "stabbing pain");
			if(z_hpi_bullet_format == 1){
				
				str1Concat += ''+CIRCLE+'Quality'+HYPHEN+tmp+' and '+quantityStr.trim();
					
			}else{
				
				var str1Concat = str1Concat+'The patient describes '+tmp+' and '+quantityStr.trim()+' pain symptoms affecting their eyes/vision.  ';				
			}	
		}else{
			if(strConcat2Arr.length>0){
				var tmp = strConcat2Arr.join(', ').toLowerCase().trim();
				tmp=tmp.replace(/sharp/, "sharp pain");
				tmp=tmp.replace(/stabbing/, "stabbing pain");
				tmp = rvs_andLastComma(tmp);
				
				if(z_hpi_bullet_format == 1){
				
					str1Concat += ''+CIRCLE+'Quality'+HYPHEN+tmp;
						
				}else{
					
					var str1Concat = str1Concat+'The patient describes '+tmp+' symptoms affecting their eyes/vision.  ';
					
				}	
			}	
			if(quantityStr){
				if(z_hpi_bullet_format == 1){
				
					str1Concat += ''+CIRCLE+'Quality'+HYPHEN+quantityStr.trim();
						
				}else{
					var str1Concat = str1Concat+'The patient describes '+quantityStr.trim()+' pain symptoms affecting their eyes/vision.  ';
				}	
			}	
		}		
		}
		
		//vis
		if(strConcat10Arr.length>0 || rvsdet_otherVisionStr){
			var rvsdet_otherVisionStr = (typeof(rvsdet_otherVisionStr)!="undefined" && rvsdet_otherVisionStr!="") ? ""+rvsdet_otherVisionStr : "";
			var tmp = (strConcat10Arr.length>0) ? ""+strConcat10Arr.join(", ").toLowerCase() : "";
			if(rvsdet_otherVisionStr!=""){ if(tmp!=""){tmp+=" and ";} rvsdet_otherVisionStr = ""+rvsdet_otherVisionStr; }else{ tmp = rvs_andLastComma(tmp);  }			
			
			if(tmp!="" || rvsdet_otherVisionStr!=""){
				if(z_hpi_bullet_format == 1){
				
					str1Concat += ''+CIRCLE+'Vision'+HYPHEN+tmp+rvsdet_otherVisionStr;
						
				}else{
					var str1Concat = str1Concat+'The patient\'s vision is '+tmp+rvsdet_otherVisionStr+'.  ';
				}	
			}			
		}
		
		//dip
		if(strConcat11Arr.length>0){
			var tmp = strConcat11Arr.join(', ').toLowerCase().trim();
			tmp = rvs_andLastComma(tmp);
			if(z_hpi_bullet_format == 1){				
				str1Concat += ''+CIRCLE+'Diplopia'+HYPHEN+tmp;						
			}else{			
				var str_diplopia = "The patient experiences "+tmp+" diplopia.  ";
				var str1Concat = str1Concat+str_diplopia;
			}		
		}		
		
		//severity
		//
		var strConcat7Arr_other="";
		if(rvs_other_svrity_str){			
			if(rvs_other_svrity_str!=""){ strConcat7Arr_other = ""+rvs_other_svrity_str; }
		}
		
		if((strConcat7Arr.length>0||strConcat7Arr_other!="") && scaleStr){
			var tmp = strConcat7Arr.join(', ').toLowerCase();
			if(strConcat7Arr_other!=""){ if(tmp!=""){tmp+=", ";} tmp+=""+strConcat7Arr_other;  }	
			
			var str_in_dc="";
			if(tmp.indexOf("increased")!=-1 ){ str_in_dc="increased"; tmp=tmp.replace(/increased(,)?/,""); }
			if( tmp.indexOf("decreased")!=-1){ str_in_dc="decreased"; tmp=tmp.replace(/decreased(,)?/,""); }
			tmp=$.trim(tmp);			
			
			if(tmp!=""){tmp = rvs_andLastComma(tmp);}
			
			if(z_hpi_bullet_format == 1){
				if(str_in_dc!=""){ if(tmp!=""){ str_in_dc+=", "; }	tmp=str_in_dc+tmp;	}
				str1Concat += ''+CIRCLE+'Severity'+HYPHEN+tmp+' Scale '+scaleStr+' out of 10';
					
			}else{
			
				var tmp_sc = "";
				var startTxt="The condition's severity";	
				if(str_in_dc!=""){ 
					tmp_sc = startTxt+" "+str_in_dc+" since last visit ";  
					if(tmp!=""){ tmp_sc += "and is "+tmp+", "; }
				}else if(tmp!=""){
					tmp_sc = startTxt+" is "+tmp+", ";
				}
				
				tmp_sc += "and is rated "+scaleStr+" out of 10.  ";
				
				//var str1Concat = str1Concat+'The complaint is rated as scale of '+scaleStr+' and severity is '+tmp+'. ';
				var str1Concat = str1Concat+tmp_sc;
			
			}
			
		}else{
			if(scaleStr){
				if(z_hpi_bullet_format == 1){
					
				str1Concat += ''+CIRCLE+'Severity'+HYPHEN+'Scale '+scaleStr+' out of 10';
					
				}else{
					var str1Concat = str1Concat+'The condition\'s severity is rated '+scaleStr+' out of 10.  ';
				}
			}else if(strConcat7Arr.length>0||strConcat7Arr_other!=""){
				var tmp = strConcat7Arr.join(', ').toLowerCase();
				if(strConcat7Arr_other!=""){ if(tmp!=""){tmp+=", ";} tmp+=""+strConcat7Arr_other;  }
				//--
				var str_in_dc="";
				if(tmp.indexOf("increased")!=-1 ){ str_in_dc="increased"; tmp=tmp.replace(/increased(,)?/,""); }
				if( tmp.indexOf("decreased")!=-1){ str_in_dc="decreased"; tmp=tmp.replace(/decreased(,)?/,""); }
				tmp=$.trim(tmp);
				if(tmp!=""){tmp = rvs_andLastComma(tmp);}	
				
				if(z_hpi_bullet_format == 1){
					if(str_in_dc!=""){ if(tmp!=""){ str_in_dc+=", "; }	tmp=str_in_dc+tmp;	}
					str1Concat += ''+CIRCLE+'Severity'+HYPHEN+tmp;
					
				}else{
				
					var tmp_sc = "";
					var startTxt="The condition's severity";	
					if(str_in_dc!=""){ 
						tmp_sc = startTxt+" "+str_in_dc+" since last visit";  
						if(tmp!=""){ tmp_sc += " and is "+tmp; }
						
					}else if(tmp!=""){
						tmp_sc = startTxt+" is "+tmp;  
					}
					
					var str1Concat = str1Concat+tmp_sc+".  ";
				}
				//--
			}
		}
		
		//Associated signs and symptoms : Follow up --
		if(f_rvs_dtdesc_2==1){		
			if(strConcat8Arr.length>0){
				var tmp = strConcat8Arr.join(', ').toLowerCase().trim();				
				tmp = rvs_andLastComma(tmp);
				if(z_hpi_bullet_format == 1){
					
					str1Concat += ''+CIRCLE+'Associated signs and symptoms'+HYPHEN+tmp.trim();
					
				}else{
					//var str1Concat = str1Concat+'The patient describes '+tmp+' pain symptoms affecting their eyes/vision.  ';
					var str1Concat = str1Concat+'The complaint is associated with '+tmp.trim()+'.  ';
				}	
			}
		}
		
		//modify factor : Follow up
		if(typeof(rvs_painrelievedby_str)!="undefined" && rvs_painrelievedby_str!=""){
			if(z_hpi_bullet_format == 1){
					
				str1Concat += ''+CIRCLE+'Modifying factors'+HYPHEN+rvs_painrelievedby_str.trim();
					
			}else{
				var str1Concat = str1Concat+"The affected area is relieved by "+rvs_painrelievedby_str.trim()+".  ";
			}		
		}
		
		//context when happens--
		if(ContextStr && strConcat6Arr.length>0){
			if(z_hpi_bullet_format == 1){
					
				str1Concat += ''+CIRCLE+'Context happens when'+HYPHEN+strConcat6Arr.join(', ').toLowerCase().trim()+' and '+ContextStr.trim();
					
			}else{
				var str1Concat = str1Concat+'Symptoms occur when the patient is '+strConcat6Arr.join(', ').toLowerCase().trim()+' and '+ContextStr.trim()+'.  ';
			}
		}else{
			if(strConcat6Arr.length>0){
				var tmp = strConcat6Arr.join(', ').toLowerCase();
				tmp = rvs_andLastComma(tmp);	
				if(z_hpi_bullet_format == 1){
					
					str1Concat += ''+CIRCLE+'Context happens when'+HYPHEN+tmp.trim();
					
				}else{				
					var str1Concat = str1Concat+'Symptoms occur when the patient is '+tmp.trim()+'.  ';
				}	
			}	
			if(ContextStr){
				if(z_hpi_bullet_format == 1){
					
					str1Concat += ''+CIRCLE+'Context happens when'+HYPHEN+ContextStr.trim();
					
				}else{
					var str1Concat = str1Concat+'Symptoms occur when the patient is '+ContextStr.trim()+'.  ';
				}
			}	
		}
		
		//Modify factors --
		var str_modi_fac	= "";
		if(strConcat13Arr_1.length>0||strConcat13Arr_2.length>0){
			var tmp = "";
			if(strConcat13Arr_1.length>0){
				var tmp1 = strConcat13Arr_1.join(', ').toLowerCase();				
				//tmp = "worse: "+tmp1+" ";
				
				if(z_hpi_bullet_format == 1){
					str_modi_fac += "Worse: "+tmp1;
					
					
				}else{
				
					var strWorse_tmp="";
					var str_dvtn=str_nvtd="";
					if(tmp1.indexOf("distance vision than near")!=-1){ str_dvtn="Distance vision is worse than near.  ";  tmp1 = tmp1.replace(/distance vision than near(,\s*)?/g,""); }
					if(tmp1.indexOf("near vision than distance")!=-1){ str_nvtd="Near vision is worse than distance.  ";  tmp1 = tmp1.replace(/near vision than distance(,\s*)?/g,""); }
					tmp1=$.trim(tmp1);				
					
					if(tmp1!=""){					
						var tmp = tmp1.toLowerCase();
						tmp = rvs_andLastComma(tmp);
						strWorse_tmp="The condition is worse "+tmp+".  ";
					}
					strWorse_tmp+=(str_dvtn!="") ? str_dvtn : "";
					strWorse_tmp+=(str_nvtd!="") ? str_nvtd : "";
					var str1Concat = str1Concat+strWorse_tmp;

				}	
				
			}
			if(strConcat13Arr_2.length>0){
				var tmp1 = strConcat13Arr_2.join(', ').toLowerCase();
				tmp1 = rvs_andLastComma(tmp1);			
				
				if(z_hpi_bullet_format == 1){
					if(str_modi_fac!=""){ str_modi_fac+=NTAB+'\t'+HYPHEN+""; }	
					str_modi_fac += "Better: "+tmp1;
					
					
				}else{
				
					var strBetter_tmp="";
					var tmp = tmp1.toLowerCase();
					tmp=tmp.replace(/transition/g, "transitioning");
					tmp = rvs_andLastComma(tmp);
					strBetter_tmp="The condition is better "+tmp.trim()+".  ";
					var str1Concat = str1Concat+strBetter_tmp;
				}	
				
			}
		}
		if(otherFactorsStr){
			if(z_hpi_bullet_format == 1){
					if(str_modi_fac!=""){ str_modi_fac+=NTAB+'\t'+HYPHEN+""; }	
					str_modi_fac += "Other: "+otherFactorsStr.trim();
					
					
			}else{
				var str1Concat = str1Concat+'Other modifying factors are '+otherFactorsStr.trim()+'.  ';
			}	
		}	
		if(z_hpi_bullet_format == 1&&str_modi_fac!=""){
			str1Concat += ''+CIRCLE+'Modifying factors'+HYPHEN+str_modi_fac;			
		}		
		
		//Associated signs and symptoms : other --
		if(f_rvs_dtdesc_2!=1){
		if(strConcat8Arr.length>0 && SymptomsStr){	
			var tmp = strConcat8Arr.join(', ').toLowerCase();	
			tmp=tmp.replace("none","no additional symptoms");
			if(z_hpi_bullet_format == 1){					
				str1Concat += ''+CIRCLE+'Associated signs and symptoms'+HYPHEN+tmp.trim()+' and '+SymptomsStr.trim();					
			}else{
				var str1Concat = str1Concat+'The complaint is associated with '+tmp.trim()+' and '+SymptomsStr.trim()+'.  ';
			}		
		}else{
			if(strConcat8Arr.length>0){
				var tmp = strConcat8Arr.join(', ').toLowerCase();
				tmp=tmp.replace("none","no additional symptoms");
				tmp = rvs_andLastComma(tmp);
				if(z_hpi_bullet_format == 1){					
					str1Concat += ''+CIRCLE+'Associated signs and symptoms'+HYPHEN+tmp;					
				}else{
					var str1Concat = str1Concat+'The complaint is associated with '+tmp.trim()+'.  ';
				}
			}
			if(SymptomsStr){
				if(z_hpi_bullet_format == 1){					
					str1Concat += ''+CIRCLE+'Associated signs and symptoms'+HYPHEN+SymptomsStr.trim();					
				}else{
					var str1Concat = str1Concat+'The complaint is associated with '+SymptomsStr.trim()+'.  ';
				}	
			}	
		}
		}
		
		//Care Instructions : Follow up
		if(typeof(otherFollowCareInstruct_str)!="undefined" && otherFollowCareInstruct_str!=""){
			if(z_hpi_bullet_format == 1){					
				str1Concat += ''+CIRCLE+'Care instructions'+HYPHEN+otherFollowCareInstruct_str.trim();					
			}else{
				var str1Concat = str1Concat+"The patient is complying with the following care instructions: "+otherFollowCareInstruct_str.trim()+".  ";
			}		
		}
		
		//meds
		if(strConcat12Arr.length>0 || rvs_ranoutmeds_str || rvs_needs_med_refill){
			var tmp = (strConcat12Arr.length>0) ? ""+strConcat12Arr.join(", ").toLowerCase() : "";
			
			var tmp_meds="";
			if(typeof(rvs_needs_med_refill)!="undefined" && rvs_needs_med_refill!=""){
				//if(tmp != "" ) {tmp += ","; }
				tmp = tmp.replace(/(needs med refill)\s*(,)?/i,"");
				rvs_needs_med_refill = "needs a refill of "+rvs_needs_med_refill;
				//tmp = tmp+ ""+rvs_needs_med_refill;
				
			}else{rvs_needs_med_refill="";}	
			
			if(typeof(rvs_ranoutmeds_str)!="undefined" && rvs_ranoutmeds_str!=""){
				//if(tmp != "" ) {tmp += ","; }
				tmp = tmp.replace(/(ran out of meds)\s*(,)?/i,"");
				rvs_ranoutmeds_str = "ran out of "+rvs_ranoutmeds_str;
				//tmp = tmp+ ""+rvs_ranoutmeds_str;	
				if(rvs_needs_med_refill!=""){ rvs_needs_med_refill+=" and "; }	
				rvs_needs_med_refill+= ""+rvs_ranoutmeds_str;
			}
			
			tmp =$.trim(tmp);			
			if(tmp!=""){tmp = rvs_andLastComma(tmp);  tmp_meds+= "Patient "+tmp; }
			if(rvs_needs_med_refill!=""){ tmp_meds+=(tmp_meds=="")?"Patient ":", "; tmp_meds+= rvs_needs_med_refill; }
			if(z_hpi_bullet_format == 1){					
				str1Concat += ''+CIRCLE+'Medication'+HYPHEN+tmp_meds;					
			}else{
				if(tmp_meds != "" ){var str1Concat = str1Concat+tmp_meds+".  ";}
			}
		}
		
		//Pertinent Negatives
		if(strConcat14Arr.length>0||other_par_neg_str){			
			var tmp = strConcat14Arr.join(', ').toLowerCase();
			if(other_par_neg_str!=""&&typeof(other_par_neg_str)!="undefined"){
				
				if(tmp.indexOf("comma")!=-1){tmp = ""+tmp.replace(/(comma)/g,", ");}				
				if(tmp!=""){ tmp += " and ";  }
				tmp += ""+other_par_neg_str;
			}else{
				tmp = rvs_andLastComma(tmp);				
				if(tmp.indexOf("comma")!=-1){tmp = ""+tmp.replace(/(comma)/g,", ");}				
			}
			if(z_hpi_bullet_format == 1){					
				str1Concat += ''+CIRCLE+'Pertinent Negatives'+HYPHEN+tmp.trim();					
			}else{	
				var str1Concat = str1Concat+'The patient experiences '+tmp.trim()+'.  ';
			}	
		}		
		
		
		//Follow up section--		
		
		if(typeof(rvs_followup_detail_other_str)!="undefined" && rvs_followup_detail_other_str!=""){
			if(z_hpi_bullet_format == 1){					
				str1Concat += ''+CIRCLE+'Other'+HYPHEN+rvs_followup_detail_other_str.trim();					
			}else{
				var str1Concat = str1Concat+"Additionally "+rvs_followup_detail_other_str.trim()+".  ";
			}	
		}
		
		// --
		//add newline if not
		if(id>1){	if(str1Concat.search(/^\r\n/)==-1){ str1Concat="\r\n"+str1Concat; } }
		
		strAllConcat[id_x] = str1Concat;
	}
	
	//complaint --
	
	
	var strAll = strAllConcat.join(' ');
	if(strAll.indexOf('!') != -1){
		do{
			strAll = strAll.replace('!', ',');
		}
		while(strAll.indexOf('!') != -1);
	}
	
	
	//New --
	
	//Add Line break if not empty
	strAll = (strAll!="") ? ""+strAll+"\n" : "";	
	
	/*
	//--
	var ccHxStrTmp="",ccHxStrTmp_1="";
	var reg = new RegExp("\-[\\w\\s]*\:");
	var indx = ccHxStr.search(reg);
	if(indx!=-1){
		ccHxStrTmp_1=ccHxStr.substr(indx);
	}
	ccHxStrTmp=strAll+ccHxStrTmp_1;
	
	//Insert/Update
	var ot = gebi('elem_ccompliant');
	ot.value = ccHxStrTmp;
	//fire onchange functions
	if(typeof ot.onchange == "function"){
		ot.onchange();
	}
	//on key press
	if(typeof ot.onkeypress == "function"){
		ot.onkeypress();
	}
	//--
	*/
	
	//Extract User Entered Text. : USER CAN ENTER TEXT in Brackets only. []
	//var reg;
	//reg = new RegExp("\\[.*\\]","gm");
	//var userText = ccHxStr.match(reg);
	var userText="";
	//alert(""+userText+"\n\n"+userText.length);
	
		if(typeof(rvs_sep_usr_coments)!='undefined'){
			var o_ccHx = rvs_sep_usr_coments();
			ccHxStr = o_ccHx.CCHX;
			userText= o_ccHx.COM;
		}
	
	
		/*
		var ptrn = "\\s*(A|An)(\\s*[0-9]{1,3}\\s*(Yrs\\.|Year|Years|Months|Days))?\\s*(Male|Female)\\s*with\\s*history\\s*of\\s*";
		var reg = new RegExp(ptrn,"gi");
		*/	
		var reg = ccHxDefStr(); 
		var mtch = ccHxStr.match(reg);
		var mtch_1 = null;
		if(mtch != null){
			mtch_1 = mtch[0];
		}
		var ccHxStrTmp_1=ccHxStrTmp_2=ccHxStrTmp_3="";
		if(mtch_1 != null){
			ccHxStrTmp_1=$.trim(mtch_1);		
		}
		//else{
		// Not matched				
		
	
		//stIndx  = ccHxStr.indexOf("with history of");
		var reg = new RegExp("\-[\\w\\s]*\:");
		var stIndx = ccHxStr.search(reg);		
		
		//check if date then it is not user comments
		if(stIndx != -1){
			var chkifDate = $.trim(ccHxStr.substr(stIndx-5,10));			
			if(chkifDate.match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/)){stIndx = -1;}
		}
	
		if(stIndx != -1){
			
			ccHxStrTmp_2 = "\n"+$.trim(ccHxStr.substr(stIndx));
			
			//test --
			ccHxStrTmp_3 = ""+$.trim(ccHxStr.substr(0,stIndx));
			
			if(mtch_1!=null || typeof(mtch_1)!="undefined"){
				ccHxStrTmp_3=ccHxStrTmp_3.replace($.trim(mtch_1),"");
			}
			
			if(z_hpi_bullet_format != 1){
			userText=rvs_sepUserText(ccHxStrTmp_3);
			}
			//test --
		}else{			
			
			if(z_hpi_bullet_format != 1){
			if(mtch_1!=null || typeof(mtch_1)!="undefined"){
				userText=ccHxStr.replace($.trim(mtch_1),"");
			}else if($.trim(ccHxStr)!=""){
				userText=ccHxStr;
			}
			
			//Check if Complaint
			userText=$.trim(userText);
			if(userText!=""){
				userText = rvs_sepUserText(userText);				
			}
			}
		}		
		
		
		if(strAll!=""){
			ccHxStrTmp = ccHxStrTmp_1 +"\n"+ $.trim(strAll) + ccHxStrTmp_2;	
			
		}else{
			ccHxStrTmp = ccHxStrTmp_1 + ccHxStrTmp_2;
			
		}	
		
	
		
		//Insert User Text At Bottom ---
		if(typeof(userText)!="undefined"&&userText!=null&&$.trim(userText)!=""){
			
			if(z_hpi_bullet_format == 1){
				var DBLCIRCLE = String.fromCharCode(9678);
				DBLCIRCLE = DBLCIRCLE+" ";
				userText = DBLCIRCLE+userText; 
			}
			
			//alert(userText);
			if(ccHxStrTmp.indexOf(userText)==-1){
				ccHxStrTmp+="\n"+userText;	
			}
				
			/*
			var ln=userText.length;
			//alert(ln);
			for(var i=0;i<ln;i++){
				if(ccHxStrTmp.indexOf(userText[i])==-1){
					ccHxStrTmp+="\n"+userText[i];	
				}
			}
			*/
		}
		//Insert User Text At Bottom ---		
		
		/*
		var stIndx_2 = ccHxStr.lastIndexOf("A",stIndx);
		if(stIndx_2 != -1){
			var ccHxStrTmp = $.trim(ccHxStr.substr(stIndx_2));
			ccHxStrTmp = strAll + ccHxStrTmp;
		*/
			//Insert/Update
			var ot = gebi('elem_ccompliant');
			ot.value = $.trim(ccHxStrTmp);
			//fire onchange functions
			if(typeof ot.onchange == "function"){
				ot.onchange();
			}
			//on key press
			if(typeof ot.onkeypress == "function"){
				ot.onkeypress();
			}
		/*	
		}
		*/
		//}
	//}
	

}

function rvs_sepUserText(str){
	if(str.length<=0)return"";	
	var rt ="";
	var aa = str.split("\n");
	var ln = aa.length;
	if(ln>0){
		for(var x in aa){
			var z = $.trim(aa[x]);
			if($.trim(z)!=""){
				if(z.indexOf("1.")==-1&&z.indexOf("2.")==-1&&z.indexOf("3.")==-1){
					rt +="\n"+z;	
				}
			}
		}
	}
	return $.trim(rt);
}

function makeEmpty(elem, eleSelect, eleTextArea){
	// MAKE FORM EMPTY 
	for(var i=0; i<elem.length; i++) {
		if(elem[i].type == 'checkbox' && elem[i].checked == true){
			elem[i].checked = false;
		}else if(elem[i].type == 'text'){
			elem[i].value = '';
		}
		if(eleSelect[i] &&  eleSelect[i]!=''){
			eleSelect[i].value = '';
		}
		if(eleTextArea[i] &&  eleTextArea[i]!=''){
			eleTextArea[i].value = '';
		}
	}
	// MAKE FORM EMPTY 
}
function closeWindow(){
	gebi('detailDesc1').style.display = 'none';
	gebi('detailDesc2').style.display = 'none';
}
//Test --


//is Decscribed
function rvs_isDescribed(id){
	var ln=complaintHead.length;
	for(var i=0;i<ln;i++){
		if(complaintHead[i]==id){
			return true;
		}
	}
	return false;
}

function hpi_chk_entr_dt(ob,n){
	var a0=$("#elem_os_othr_date_grp");
	var b0=$("#detailDesc1 :input[name=selectNo]");
	var c0=$("#detailDesc1 :input[name=selectDate]");
	var d0=$("#detailDesc1 span[title='Close date']");
	
	var a1=$("#elem_sp_surdate_grp");
	var b1=$("#detailDesc2 :input[name=selectNo]");
	var c1=$("#detailDesc2 :input[name=selectDate]");
	var d1=$("#detailDesc2 span[title='Close date']");
	
	if(n==5){		
		a1.show();d1.show();b1.val('').hide();c1.val('').hide();
		return;
	}else if(n==4){
		a0.show();d0.show();b0.val('').hide();c0.val('').hide();
		return;
	}else if(n==3){
		a0.find("input[type=text]").val(""); a0.hide();  a1.find("input[type=text]").val(""); a1.hide();  d0.hide();d1.hide();b0.show();c0.show();b1.show();c1.show();		
		return;
	}else if(n==1){
		var a =a0;
		var b=b0;
		var c=c0;
		var d=d0;
	}else if(n==2){		
		var a =a1;
		var b=b1;
		var c=c1;
		var d=d1;
	}	
	
	if($(ob).hasClass("ui-icon")==false && ob.value=="Date"){
		a.show();d.show();
		b.val('').hide();c.val('').hide();		
	}else{		
		a.hide(); a.find("input[type=text]").val("");
		d.hide();
		b.show();c.show();
	}
}


//
$("#div_rvs input[type=text][name*=Other], #div_rvs input[type=text][name*=_other]").bind("click",function(){  var frid = $(this).parent("label").attr("for"); if(frid && $("#"+frid+"").prop("checked")==false){  $("#"+frid+"").trigger("click");  }   });
	
// functions copied from other files
function setRVS(obj, head, otherId){
		// Shift to new Function in rvs.php
		rvfun_setRVS_v2(obj, head, otherId);
		return;
		// Shift to new Function in rvs.php
}

function disOth(ob,id,tId)
{
	//Shift to
	if((typeof(isiPad)!="undefined" && isiPad==1)||typeof(isSafari)!="undefined" && isSafari==1){
		//if(ob && ob.checked){rvs_disOth(ob,id,tId);}
	}else{
		rvs_disOth(ob,id,tId);
	}
	return;
	//Shift to	
}
function showOther(obj, id, head, textBox){		
	// Shift to
	if((typeof(isiPad)!="undefined" && isiPad==1)||typeof(isSafari)!="undefined" && isSafari==1){
		//if(ob && ob.checked){rvs_disOth(ob,id,tId);}
	}else{
		rv_showOther(obj, id, head, textBox);
	}
	return;
	// Shift to	
}


