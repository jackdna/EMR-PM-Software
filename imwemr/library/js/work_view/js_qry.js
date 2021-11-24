
var oneEye_issue=oneEye_eye="";
function oneEye_isSet(flagPu){

	var arrRet = null;
	if(flagPu == 2){
		//consider Poor View Also
		var oprph = $("#phth_pros");
		var oeye = $("#is_od_os");
		if(oprph.length > 0 && oprph.val() != "" && oeye.length > 0 && oeye.val() != ""){
			issue = ""+oprph.val();
			eye = ""+oeye.val();
			arrRet = {"issue":issue,"eye":eye};
		}
		//consider Poor View Also

	}else{
		//Not consider Poor View and Phthisis

		if(flagPu == 1){
			if(window.opener){
			var tmp_iss =  window.opener.$("#phth_pros").val();
			var tmp_eye =  window.opener.$("#is_od_os").val();
			}
		}else{
			var tmp_iss =  $("#phth_pros").val();
			var tmp_eye =  $("#is_od_os").val();
		}

		//1.when protheses is checked, you are not able to put anything in that eye for prescription or medical reason. Could you pls fix it that you can type everything into that eye as if it was not checked.
		arrRet = {"issue":tmp_iss,"eye":tmp_eye};
		//Not consider Poor View and Phthisis

	}

	return arrRet;
}

function oneEye_disable(i){

	var elOu =",:input[id*='aneseye_ou'][type='radio'],:input[id*='dileye_ou'][type='radio'],:input[id*='oodeye_ou'][type='radio']";
	if(i== "OD"){

		idChk = ":input[name*='od_'][type='checkbox'],:input[name*='od_'][type='radio']";
		idChk +=",:input[name*='Od'][type='radio'],:input[name*='Od'][type='checkbox']";
		idChk +=",:input[name*='_od'][type='checkbox'],:input[name*='_od'][type='radio'],:input[id*='_od'][type='radio']";
		idChk +=elOu;

		idElm = ":input[name*='Od'][type!='checkbox'][type!='radio']";
		idElm += ",:input[name*='_od'][type!='checkbox'][type!='radio']";
		idElm += ",:input[name*='od_'][type!='checkbox'][type!='radio']";

		$(idChk).attr("disabled", "disabled").removeAttr("checked");
		$(idElm).attr("disabled", "disabled").val("");

	}else if(i == "OS") {
		idChk = ":input[name*='os_'][type='checkbox'],:input[name*='os_'][type='radio']";
		idChk += ",:input[name*='_os'][type='checkbox'],:input[name*='_os'][type='radio']";
		idChk += ",:input[name*='Os'][type='radio'],:input[name*='Os'][type='checkbox'],:input[id*='_os'][type='radio']";
		idChk +=elOu;

		idElm = ":input[name*='Os'][type!='checkbox'][type!='radio']";
		idElm += ",:input[name*='os_'][type!='checkbox'][type!='radio']";
		idElm += ",:input[name*='_os'][type!='checkbox'][type!='radio']";

		$(idChk).attr("disabled", "disabled").removeAttr("checked");
		$(idElm).attr("disabled", "disabled").val("");
	}
}

function oneEye_display(wid, wtxt){
	wtxt = (typeof wtxt != "undefined") ? ""+wtxt : "";
	$("#"+wid).html(""+wtxt);
}

function oneEye_setCN(flgTdClr){
	var oe_issue = $("#phth_pros");
	var oe_eye = $("#is_od_os");
	//if null, return
	if(!oe_issue.length || !oe_eye.length){return;}

	//Color Td default
	flgTdClr = (typeof flgTdClr == "undefined") ? "1" : flgTdClr;

	//dis_od_chart();-- Set Labels in Chart Summery
	var strchartsumm_OD = new Array( "#pupil_ref","#lidRefOd","#lesionRefOd","#lidPositionRefOd","#lacrimalRefOd",
									 "#iop_OD","#iopRefOd","#conjuctOd","#slecorneaOd","#antChamberOd","#sleIrsiOd","#sleLensOd",
									 "#vitreous_od","#macula_od","#periphery_od","#vessels_od","#opticNerve_od","#external_od",
									 "#cd_od");

	var strchartsumm_OS = new Array("#pupil_refOs","#lidRefOs","#lesionRefOs","#lidPositionRefOs","#lacrimalRefOs",
									"#iop_OS","#iopRefOs","#conjuctOs","#slecorneaOs","#antChamberOs","#sleIrsiOs","#sleLensOs",
									"#vitreous_os", "#macula_os","#periphery_os","#vessels_os","#opticNerve_os","#external_os",
									"#cd_os");
	var arrSe = new Array("","1","2","3","4","","Iop","1","2","3","4","5","1","2","3","4","6","","6");

	//Loop
	for(var i=0;i<19;i++){
		var tmpOd =$(strchartsumm_OD[i]);
		var tmpOs =$(strchartsumm_OS[i]);
		var tmpOdi = $(strchartsumm_OD[i]+i);
		var tmpOsi = $(strchartsumm_OS[i]+i);
		var tmpOdV = (tmpOdi.length != 0 && tmpOdi.val() != "") ? tmpOdi.val() : "&nbsp;";
		var tmpOsV = (tmpOsi.length != 0 && tmpOsi.val() != "") ? tmpOsi.val() : "&nbsp;";
		var tmp="";
		//Empty OD and OS
		if(oe_issue.val() == "" || oe_eye.val() == ""){
			tmpOd.html(tmpOdV);
			tmpOs.html(tmpOsV);

		}else{ //Fill OD with issue label and Empty OS

			if(oe_eye.val() == "OU"){
				//R6.5: Dt.: 22-09-2014: Prosthesis � Need the ability to select both eyes.
				//if(oe_issue.val() != "Prosthesis"){
					tmpOd.html(tmpOdV);
					tmpOs.html(tmpOsV);
				//}

			}else if(oe_eye.val() == "OD"){
				//od
				//R6.5: Dt.: 22-09-2014: Prosthesis � Need the ability to select both eyes.
				/*
				if(oe_issue.val() == "Prosthesis"){
					tmpOd.html("&nbsp;");//.parent().addClass("bgSmoke");
				}else{
				*/
					tmpOd.html(tmpOdV);
				//}

				//os
				tmpOs.html(tmpOsV);

			}else if(oe_eye.val() == "OS"){ //Fill OD with issue label and Empty OS
				//od
				tmpOd.html(tmpOdV);
				//os
				//R6.5: Dt.: 22-09-2014: Prosthesis � Need the ability to select both eyes.
				/*
				if(oe_issue.val() == "Prosthesis"){
					tmpOs.html("&nbsp;");//.parent().addClass("bgSmoke");
				}else{
				*/
					tmpOs.html(tmpOsV);
				//}
			}
		}

		//Set Bg Color
		var flgOd = false;
		var flgOs = false;

		if(i == 0){ //Pupil
			var vSe = $("#elem_se_Pupil").val();

		}else if(i>=1 && i<=4){ //LA
			var vSe = $("#elem_se_La").val();

		}else if(i==6){ // Gonio
			var vSe = $("#elem_se_Gonio").val();

		}else if(i>=7 && i<=11){ // SLE
			var vSe = $("#elem_se_Sle").val();

		}else if(i>=12 && i<=15){ // RV
			var vSe = $("#elem_se_Rv").val();

		}else if(i==16 || i==18){ // optic
			var vSe = $("#elem_se_Optic").val();
		}else if(i==17){ //Ee
			var vSe = $("#elem_se_Ee").val();
		}

		if(i != 5){ // Not Iop
			if(vSe == "" || vSe.indexOf(arrSe[i]+"_Od=0")!=-1){
				flgOd = true;
			}
			if(vSe == "" || vSe.indexOf(arrSe[i]+"_Os=0")!=-1){
				flgOs = true;
			}
		}

		if(tmpOd.html() == "" || tmpOd.html() == "&nbsp;" || flgOd == true ){	//
			tmpOd.parent().addClass("bgSmoke");
		}else{
			tmpOd.parent().removeClass("bgSmoke");
		}

		if(tmpOs.html() == "" || tmpOs.html() == "&nbsp;" || flgOs == true ){ //
			tmpOs.parent().addClass("bgSmoke");
		}else{
			tmpOs.parent().removeClass("bgSmoke");
		}

	}
	//dis_od_chart();--


	//First Row --
	//Display/Hide First Row and insert label of issue in eye
	var strFRSumm = $("#tr_fr_pupil,#tr_fr_ee,#tr_fr_la,#tr_fr_iop,#tr_fr_sle,#tr_fr_rv");
	//var strFRSumm_od = $("#fr_od_pupil,#fr_od_ee,#fr_od_la,#fr_od_iop,#fr_od_sle,#fr_od_rv");
	//var strFRSumm_os = $("#fr_os_pupil,#fr_os_ee,#fr_os_la,#fr_os_iop,#fr_os_sle,#fr_os_rv");

	if((oe_issue.val() != "") && (oe_eye.val() != "")){
		strFRSumm.each(function(){

			//In case of POOR View
			//Check if row is Not SLE And RV
			if(oe_issue.val() == "Poor View" && this.id != "tr_fr_sle" && this.id != "tr_fr_rv"){
				$(this).hide();
				return;
			}
			//

			$(this).show();
			var tds = $(this).children();
			if(oe_eye.val() == "OU"){
				//if(oe_issue.val() != "Prosthesis"){ //R6.5: Dt.: 22-09-2014: Prosthesis � Need the ability to select both eyes.
					tds.eq(1).children().html(""+oe_issue.val()).addClass("css_oneEyeDesc"); //OD
					tds.eq(2).children().html(""+oe_issue.val()).addClass("css_oneEyeDesc");//OS

					if(flgTdClr == 1){
						tds.eq(1).addClass("bgWhite").removeClass("bgSmoke");
						tds.eq(2).addClass("bgWhite").removeClass("bgSmoke");
					}
				//}

			}else if(oe_eye.val() == "OD"){

				tds.eq(1).children().html(""+oe_issue.val()).addClass("css_oneEyeDesc"); //OD
				tds.eq(2).children().html("&nbsp;").removeClass("css_oneEyeDesc"); //OS

				//Set Td Color
				if(flgTdClr == 1){
					tds.eq(2).removeClass("bgWhite").addClass("bgSmoke");

					//if(oe_issue.val() != "Prosthesis"){ //R6.5: Dt.: 22-09-2014: Prosthesis � Need the ability to select both eyes.
						tds.eq(1).addClass("bgWhite").removeClass("bgSmoke");
					/*
					}else{
						tds.eq(1).removeClass("bgWhite").addClass("bgSmoke");
					}
					*/
				}

			}else if(oe_eye.val() == "OS"){

				tds.eq(1).children().html("&nbsp;").removeClass("css_oneEyeDesc"); //OD
				tds.eq(2).children().html(""+oe_issue.val()).addClass("css_oneEyeDesc");//OS

				// Set Td Color
				if(flgTdClr == 1){
					tds.eq(1).removeClass("bgWhite").addClass("bgSmoke");

					//if(oe_issue.val() != "Prosthesis"){ ////R6.5: Dt.: 22-09-2014: Prosthesis � Need the ability to select both eyes.
						tds.eq(2).addClass("bgWhite").removeClass("bgSmoke");
					/*
					}else{
						tds.eq(2).removeClass("bgWhite").addClass("bgSmoke");
					}
					*/
				}
			}
		});


		/*
		if(oe_eye.val() == "OD"){
			strFRSumm_od.html(""+oe_issue.val()).addClass("css_oneEyeDesc");
			strFRSumm_os.html("").removeClass("css_oneEyeDesc");
		}else if(oe_eye.val() == "OS"){
			strFRSumm_od.html("").removeClass("css_oneEyeDesc");
			strFRSumm_os.html(""+oe_issue.val()).addClass("css_oneEyeDesc");
		}
		*/

	}else{
		// Empty One Eye, so hide First Row
		strFRSumm.hide();
	}
	//First Row --


	//dis_od_fns();-- disable Fields in Vision
	var oVisElemsOd = $("#divVision :input[type!=hidden][name*=Od]");
	var oVisElemsOs = $("#divVision :input[type!=hidden][name*=Os]");

	//if((oe_issue.val() != "Prosthesis") || oe_eye.val() == ""){ //Enable OD and OS //R6.5: Dt.: 22-09-2014: Prosthesis � Need the ability to select both eyes.

		oVisElemsOd.removeAttr("disabled");
		oVisElemsOs.removeAttr("disabled");
	/*
	}else{
		if(oe_eye.val() == "OD"){
			oVisElemsOd.attr("disabled","disabled");
			oVisElemsOs.removeAttr("disabled");
		}else if(oe_eye.val() == "OS"){
			oVisElemsOd.removeAttr("disabled");
			oVisElemsOs.attr("disabled","disabled");
		}
	}
	*/
	//dis_od_fns();--

	//dis_od_nlp(); --
	var oVisElemsOd = $("#divVision :input[type!=hidden][name*=Od][name*=Txt],#divVision :input[type!=hidden][name*=Od][name*=OverrefV]");
	var oVisElemsOs = $("#divVision :input[type!=hidden][name*=Os][name*=Txt],#divVision :input[type!=hidden][name*=Os][name*=OverrefV]");

	//if(oe_issue.val() != "Prosthesis" || oe_eye.val() == ""){ //Add 20/ //R6.5: Dt.: 22-09-2014: Prosthesis � Need the ability to select both eyes.
		oVisElemsOd.val(function(index, value){ return (value != "") ? this.value : "20/"; });
		oVisElemsOs.val(function(index, value){ return (value != "") ? this.value : "20/"; });
	/*
	}else{
		if(oe_eye.val() == "OD"){
			oVisElemsOd.val("NLP");
			oVisElemsOs.val(function(index, value){ return (value != "") ? this.value : "20/"; });
		}else if(oe_eye.val() == "OS"){
			oVisElemsOd.val(function(index, value){ return (value != "") ? this.value : "20/"; });
			oVisElemsOs.val("NLP");
		}
	}
	*/
	//dis_od_nlp(); --


	/***
		//dis_od_chart();--
		//dis_od_fns();
		//dis_od_img();
		//dis_od_nlp();

		//dis_os_chart();--
		//dis_os_fns();
		//dis_os_img();
		//dis_os_nlp();
	***/
}

//Check for One Eye Exam (Prothesis/Phthisis)
function oneEye_check(){
	var oChk = oneEye_isSet(1);
	if(oChk && (oChk.issue != "") && (oChk.issue == "Phthisis" || oChk.issue == "Prosthesis")){
		//Issue
		wTxt = ""+oChk.issue;

		//set
		oneEye_issue = ""+oChk.issue;
		oneEye_eye = ""+oChk.eye;
		//--

		//disable
		oneEye_disable(oChk.eye);
	}
}
//Check for One Eye Exam (Prothesis/Phthisis)

//Check if One Eye Exam and disable elems
//
function oneEye_isOpAllwd(eye){
	var ret=true;
	var iss = "";
	if(typeof(eye)!="undefined" && eye!=""){
		var oChk_oe = oneEye_isSet();
		var ignore_i = "";

		if((oChk_oe != null) && (oChk_oe.issue != "") &&
				(oChk_oe.issue == "Phthisis" || oChk_oe.issue == "Prosthesis") ){
				iss = oChk_oe.issue;
			ignore_i = oChk_oe.eye;
			if(ignore_i != ""){
				if(eye==ignore_i||ignore_i=="OU"){
					ret=false;
				}else{
					if(eye=="OU"){
						if(ignore_i=="OD"){eye="OS";}
						else if(ignore_i=="OS"){eye="OD";}
					}
				}
			}
		}
	}
	return {alwd:ret, eye:eye, iss:iss};
}

function oneEye_checkinexam(){
	var ret=true;
	var oChk_oe = oneEye_isSet(1);
	if((oChk_oe != null) && (oChk_oe.issue != "") &&
			(oChk_oe.issue == "Phthisis" || oChk_oe.issue == "Prosthesis") ){
				ignore_i = oChk_oe.eye;
				if(ignore_i=="OU"){
					ret=false;
				}
			}
	return ret;
}

//NewExamTest --

function newET_setGray(ptrn){

	if(typeof ptrn != "undefined" && ptrn != ""){
		$(""+ptrn).addClass("greyAll_v2").bind("click change",function(){

			 var nm = $(this).attr('name');
			 if(nm.indexOf("Od") != -1){
				$(""+ptrn+"[name*='Od']").removeClass("greyAll_v2");
			 }else{
				$(""+ptrn+"[name*='Os']").removeClass("greyAll_v2");
			 }

		});
	}
}

function newET_setGray_v2(arr,ptrnin){
	var ptrn = "";
	if(typeof ptrnin != "undefined"){
		ptrn = ""+ptrnin;
	}else{

		var len=arr.length;
		for(var i=0;i<len;i++){
			//ptrn = "#divPupil :input[type!='hidden']";
			if(arr[i] != ""){

				if(newET_chkB4GrayExe(""+arr[i],"od")){
					if(ptrn != ""){
						ptrn += ",";
					}

					ptrn += "#"+arr[i]+" :input[name*='Od_'],"+
							"#"+arr[i]+" :input[name*='_od'],"+
							"#"+arr[i]+" :input[name*='Od'],"+
							"#"+arr[i]+" :input[name*='od_']";

				}

				if(newET_chkB4GrayExe(""+arr[i],"os")){
					if(ptrn != ""){
						ptrn += ",";
					}

					ptrn += "#"+arr[i]+" :input[name*='Os_'],"+
							"#"+arr[i]+" :input[name*='_os'],"+
							"#"+arr[i]+" :input[name*='Os'],"+
							"#"+arr[i]+" :input[name*='os_']";

				}

			}
		}

	}

	if(typeof ptrn != "undefined" && ptrn != ""){
		var should_not_grey = "input[type='button'],:input[name*=sc_elem], button, #elem_noChange, #elem_pharmadilated, #elem_pharmadilated_eye";

		$(""+ptrn).not(should_not_grey).addClass("greyAll_v2").bind("click change keyup",function(e){

			 //$(this).removeClass("greyAll_v2");
			 //$(""+ptrn).each(function(){$(this).removeClass("greyAll_v2");});

			if(e && e.type=="click"&& typeof(e.srcElement)!="undefined" && e.srcElement.type=="select-one")return;
			var nm = $(this).attr('name');
			if(typeof(nm)=="undefined"||nm==""){ return; }
			if($(this).parents("div.tab-pane").length>0){
				var idDiv = $(this).parents("div.tab-pane").attr("id");
				if($("#"+idDiv).parents("div.tab-pane").length>0){ idDiv = $("#"+idDiv).parents("div.tab-pane").attr("id");}//la
			}else if($(this).parents("div.subExam").length>0){
				var idDiv = $(this).parents("div.subExam").attr("id");
			}else{
				//
			}

			var eye="";
			//if(e.srcElement.tagName.toLowerCase()=="canvas"){
				//var t=$("#hidDrawingChangeYesNo").val();

				//alert($("#hidDrawingChangeYesNo").val());

				//window.status="TEST: "+t;

				//if(typeof(t)!="undefined" && t=="no"){
				//	eye="ou";
				//}
			//}else{
				//
				//This Element Change Color
				$(this).removeClass("greyAll_v2");
				 if(nm.toLowerCase().indexOf("od_") != -1 || nm.toLowerCase().indexOf("_od") != -1){
					 eye="od";
				 }else if(nm.toLowerCase().indexOf("os_") != -1 || nm.toLowerCase().indexOf("_os") != -1){
					 eye="os";
				 }else if(nm.toLowerCase().indexOf("od") != -1){
					 eye="od";
				 }else if(nm.toLowerCase().indexOf("os") != -1){
					 eye="os";
				 }/*else if( examName == "External" ){
					 eye="ou";
				 }*/
				//
			//}

			//is smart pop parent
			var is_sc_con_det = ""; if($(this).parents("#div_sc_con_detail").length>0){  is_sc_con_det = "#div_sc_con_detail";   };

			//Check and set
			if(newET_chkB4GrayExe(idDiv,eye,is_sc_con_det)){
				newET_setGray_Exe(idDiv,eye,is_sc_con_det);
			}

		});
	}
}

function newET_chkB4GrayExe(divId,eye,flgscdet){
	if(typeof(flgscdet)=="undefined"||flgscdet==""){ flgscdet=""; }else{ flgscdet=flgscdet+" "; }
	var ret = true;
	if(eye == "od"){
		if($(flgscdet+"#elem_chng_"+divId+"_Od").val() == "1"){
			ret = false;
		}
	}else if(eye == "os"){
		if($(flgscdet+"#elem_chng_"+divId+"_Os").val() == "1"){
			ret = false;
		}
	}else if(eye == "ou"){
		if($(flgscdet+"#elem_chng_"+divId+"_Od").val() == "1" && $(flgscdet+"#elem_chng_"+divId+"_Os").val() == "1"){
			ret = false;
		}
	}

	return ret;
}

function newET_setGray_Exe(divId,eye,flgscdet){
	if(typeof(flgscdet)=="undefined"||flgscdet==""){ flgscdet=""; }else{ flgscdet=flgscdet+" "; }
	 var ptrn_2 = flgscdet+"#"+divId+" :input";
	 if(eye.toLowerCase().indexOf("od") != -1 || eye.toLowerCase().indexOf("ou") != -1){
		var str = ""+ptrn_2+"[name*='Od_'], "+
				  ""+ptrn_2+"[name*='_od'], "+
				  ""+ptrn_2+"[name*='Od'], "+
				  ""+ptrn_2+"[name*='od_'] ";
		$(""+str).removeClass("greyAll_v2");
		$(flgscdet+"#elem_chng_"+divId+"_Od").val("1");
	 }

	 if(eye.toLowerCase().indexOf("os") != -1 || eye.toLowerCase().indexOf("ou") != -1){
		var str = ""+ptrn_2+"[name*='Os_'], "+
				  ""+ptrn_2+"[name*='_os'], "+
				  ""+ptrn_2+"[name*='Os'], "+
				  ""+ptrn_2+"[name*='os_'] ";
		$(""+str).removeClass("greyAll_v2");
		$(flgscdet+"#elem_chng_"+divId+"_Os").val("1");
	 }
}

function newET_setIndicators_Exe(divId,eye,val){
	if(eye.toLowerCase().indexOf("od") != -1 || eye.toLowerCase().indexOf("ou") != -1){
		$("#elem_chng_"+divId+"_Od").val(""+val);
	}
	if(eye.toLowerCase().indexOf("os") != -1 || eye.toLowerCase().indexOf("ou") != -1){
		$("#elem_chng_"+divId+"_Os").val(""+val);
	}
}

function newET_emptyGray(arr){

	var str = "";
	var len = arr.length;
	for(var i=0;i<len;i++){
		if(str != ""){
			str += ", ";
		}
		str += "#"+arr[i]+" .greyAll_v2";
	}

	if(str != ""){

		$(""+str).each(function(){
			if($(this).is(":checkbox")){
				if($(this).attr('checked')){
					$(this).attr('checked',false);
				}
			}else{
				$(this).val('');
			}
		});
	}
}

function newET_b4Done(){
	//Check if No Change Selected
	var obj = docNoChange();
	if(obj.divNC0.length > 0){
		//Check if No Change is not set
		//newET_emptyGray(obj.divNC0);
	}
}

function newET_bindEvDone(){
	$(":input[type='submit'][value='Done']").bind("click", function(){
		newET_b4Done();
	});
}

function newET_drawingChanged(){

	var oDiv = getWcId("draw");
	var eye="ou";

	//Check and set
	if(newET_chkB4GrayExe(oDiv.div,eye)){
		newET_setGray_Exe(oDiv.div,eye);
	}

	//
	if(typeof(examName)!="undefined" && examName=="Gonio"){
		$("#divIop3 textarea").triggerHandler("keyup");
	}
}


function newET_setGray_v3(flgfpu){
	//el_chng_div_0 // od
	//el_chng_div_1 //os
	var strPtrnGray = "", parentid="", exmid="", srchin="";
	var ar_parentid = [], ar_exmid=[];

	if(typeof(examName)=="undefined"){examName="";}//

	if(typeof(flgfpu)!=""&&flgfpu==1||examName==""){
		ar_parentid = ["#div_sc_con_detail"];
		var tmp = $(ar_parentid[0]+" div.subExam").attr("id");
		ar_exmid = [tmp];
	}else if(examName == "LA"||examName == "SLE" || examName == "Fundus"|| examName == "Refractive Surgery"){
		strPtrnGray=$("#el_strPtrnGray").val();
	}else if(examName == "Pupil"){
		ar_parentid =["#divPupil"];
		ar_exmid =["divPupil"];
	}else if(examName == "External"){
		ar_parentid =["#divCon","#divDraw"];
		ar_exmid =["divCon","divDraw"];
	}

	var ln = ar_parentid.length;
	for(var i=0;i<ln;i++){
		parentid = ar_parentid[i];
		exmid = ar_exmid[i];
		srchin = (parentid == "#div_sc_con_detail") ? parentid+" " : "";
		var od = $(srchin+"#elem_chng_"+exmid+"_Od").val(), os = $(srchin+"#elem_chng_"+exmid+"_Os").val();

		if((od==""||od=="0") && (os==""||os=="0")){
			strPtrnGray += parentid+" :input,";
		}else{
			if((od==""||od=="0")){
				strPtrnGray += parentid+" :input[name*='Od_'],"+parentid+" :input[name*='Od'],";
			}else if((os==""||os=="0")){
				strPtrnGray += parentid+" :input[name*='Os_'],"+parentid+" :input[name*='Os'],";
			}
		}
	}

	if(strPtrnGray != ""){
		strPtrnGray = strPtrnGray.replace(/,\s*$/, '');
		newET_setGray_v2("",""+strPtrnGray+"");
	}
}

//NewExamTest --
