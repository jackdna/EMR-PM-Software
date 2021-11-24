/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
/*
File: rvs.js
Coded in PHP7
Purpose: This file provides functions for RVS section.
Access Type : Include file
*/
//chart_exam.js

/*---MODIFIED FANCY ALERT (USED IN TESTS)--*/
function fAlert(msg, title, actionToPerform, width, height, BtnCaption,ModalMode) {
	if(typeof(title)=='undefined' || title=='') 			title='imwemr';
	if(typeof(width)=='undefined' || width=='') 			width='500px';
	if(typeof(height)=='undefined' || height=='') 			height='auto';
	if(typeof(BtnCaption)=='undefined' || BtnCaption=='') 	BtnCaption='OK';
	if(typeof(ModalMode)!='boolean')						ModalMode=true;
	if(BtnCaption=='CLOSE' || BtnCaption=='Close'){closeBtn=false;} else{closeBtn=true;}
	//if(actionToPerform.indexOf('window.top.')== -1 && actionToPerform.indexOf('top.')== 0){actionToPerform = "window."+actionToPerform;}
	new window.top.Messi(msg, {'title': title, modal: ModalMode, 'width': width, 'closeButton':closeBtn, buttons: [{id: 0, label: BtnCaption, val: 'X'}],callback: function(){if(typeof(actionToPerform)=='string' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')>=0){actionToPerform;}else if(typeof(actionToPerform)=='string' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')<0){eval(actionToPerform);}else if(typeof(actionToPerform)=='object'){actionToPerform.focus();}}});
}
function fancyConfirm(msg, title, YsAction, NoAction, width, height) {
	if(typeof(YsAction)=='undefined') {YsAction=title; title='';}
	if(typeof(title)=='undefined' || title=='') title='imwemr';
	if(typeof(width)=='undefined' || width=='') width='500px';
	if(typeof(NoAction)=='undefined' || NoAction=='') NoAction=false;
	if(YsAction.indexOf('window.top.')==-1 && YsAction.indexOf('top.')==0){YsAction = "window."+YsAction;}
	if(NoAction!=false && NoAction.indexOf('window.top.')==-1 && NoAction.indexOf('top.')==0){NoAction = "window."+NoAction;}
	new window.top.Messi(msg, {'title': title, modal: true, 'width': width, buttons: [{id: 0, label: 'Yes', val: 'Y', "class": 'btn-success'}, {id: 1, label: 'No', val: 'N', "class": 'btn-danger'}],callback: function(val){if(val=='Y'){eval(YsAction);}else if(val=='N' && NoAction!=false){eval(NoAction);}}});
}

//--

//Set tab ids of exam
function getWcId(ind)
{
	switch(examName){
	case "EOM":
		var ct = (typeof ind == "undefined") ? getCurTab() : ind;
		var wcid = {"nm":"Eom","div":"divEom","c":1,"tab":"Eom"};
		ct = ct.toLowerCase();

		if(ct == "1" || ct == "diveom"){
			wcid = {"nm":"Eom","div":"divEom","c":1,"tab":"Eom"};
		}else if(ct == "3" || ct == "diveom3" || ct=="draw" ){
			wcid = {"nm":"Eom3","div":"divEom3","c":3,"tab":"Eom3","app":"app_eom_drawing_2","fod":"elem_eomDrawing_2"};
		}
	break;
	case "Pupil":
		wcid = {"nm":"Pupil","div":"divPupil","c":1};
	break;

	case "External":
		var ct = (typeof ind == "undefined") ? getCurTab() : ind;
		var wcid = {"nm":"Ee","div":"divCon","c":1,"tab":"Con"};
		ct = ct.toLowerCase();
		if(ct == 2 || ct == "draw" || ct == "divdraw"){
			wcid = {"nm":"Draw","div":"divDraw","c":2,"tab":"Draw","app":"app_ee_drawing","fod":"elem_externalOdDrawing"}; //,"mdraw":1
		}else{
			wcid = {"nm":"Ee","div":"divCon","c":1,"tab":"Con"};
		}
	break;

	case "LA":
		var ct = (typeof ind == "undefined") ? getCurTab() : ind;
		var wcid = {"nm":"Lids","div":"div1","c":1};
		ct = ct.toLowerCase();

		if(ct == "1" || ct == "div1" || ct == "lids" || ct == "lid"){
			wcid = {"nm":"Lids","div":"div1","c":1};
		}else if(ct == "2" || ct == "div2" || ct == "lesion"){
			wcid = {"nm":"Lesion","div":"div2","c":2};
		}else if(ct == "3" || ct == "div3" || ct == "lidpos" || ct == "lidposition" ){
			wcid = {"nm":"LidPos","div":"div3","c":3};
		}else if(ct == "4" || ct == "div4" || ct == "lacsys" || ct=="lacrimal_system" ){
			wcid = {"nm":"LacSys","div":"div4","c":4};
		}else if(ct == "5" || ct == "div5" || ct == "draw" || ct == "drawing"){
			wcid = {"nm":"Draw","div":"div5","c":5,"app":"app_la_drawing","fod":"elem_laDrawing"};
		}

	break;
	case "SLE":
		var ct = (typeof ind == "undefined") ? getCurTab() : ind;
		var wcid = {"nm":"Conj","div":"div1","c":1};
		ct = ct.toLowerCase();
		if(ct == "1" || ct == "div1" || ct == "conjunctiva" || ct == "conj"){
			wcid = {"nm":"Conj","div":"div1","c":1};
		}else if(ct == "2" || ct == "div2" || ct == "cornea" || ct == "corn"){
			wcid = {"nm":"Corn","div":"div2","c":2};
		}else if(ct == "3" || ct == "div3" || ct == "ant. chamber" || ct == "ant" || ct == "antchamber"){
			wcid = {"nm":"Ant","div":"div3","c":3};
		}else if(ct == "4" || ct == "div4" || ct == "iris"){
			wcid = {"nm":"Iris","div":"div4","c":4};
		}else if(ct == "5" || ct == "div5" || ct == "lens"){
			wcid = {"nm":"Lens","div":"div5","c":5};
		}else if(ct == "6" || ct == "div6" || ct == "draw" || ct == "drawing"){
			wcid = {"nm":"Draw","div":"div6","c":6,"app":"app_conjunctiva_od_drawing","fod":"elem_conjunctivaOdDrawing"};
		}

	break;

	case "Fundus":
		var ct = (typeof ind == "undefined") ? getCurTab() : ind;
		var wcid = {"nm":"Vitreous","div":"div1","c":1};
		ct = ct.toLowerCase();
		if(ct == "1" || ct == "div1" || ct == "vitreous"){
			wcid = {"nm":"Vitreous","div":"div1","c":1};
		}else if(ct == "2" || ct == "div2" || ct == "macula"){
			wcid = {"nm":"Macula","div":"div2","c":2};
		}else if(ct == "3" || ct == "div3" || ct == "peri" || ct == "periphery"){
				wcid = {"nm":"Peri","div":"div3","c":3};
		}else if(ct == "4" || ct == "div4" || ct == "bv" || ct == "blood vessels" || ct == "blood" ){
			wcid = {"nm":"BV","div":"div4","c":4};
		}else if(ct == "5" || ct == "div5" || ct == "draw" || ct == "drawing" || ct == "drawrvma" || ct == "drawrvon" ){
			wcid = {"nm":"Draw","div":"div5","c":5,"app":"app_od_coords","fod":"elem_odDrawing"};
			if(ct == "drawrvma"){wcid.c=9; }
			if(ct == "drawrvon"){wcid.c=8; }
		}else if(ct == "6" || ct == "div6" || ct == "optic" || ct == "opt.nev"){
			wcid = {"nm":"Optic","div":"div6","c":6};
		}else if(ct == "7" || ct == "div7" || ct == "retinal" || ct == "ret"){
			wcid = {"nm":"Retinal","div":"div7","c":7};
		}
	break;
	case "Refractive Surgery":
		var ct = (typeof ind == "undefined") ? getCurTab() : ind;
		var wcid = {"nm":"RefSurg","div":"div1","c":1};
		ct = ct.toLowerCase();
	break;
	case "Gonio":
		var ct = (typeof ind == "undefined") ? getCurTab() : ind;
		var wcid = {"nm":"IOP","div":"divIop1","c":"Iop1"};
		ct = ct.toLowerCase();
		if(ct == "1" || ct == "diviop1" || ct == "iop"){
			wcid = {"nm":"IOP","div":"divIop1","c":"Iop1"};
		}else if(ct == "2" || ct == "diviop" || ct == "gonio"){
			wcid = {"nm":"Gonio","div":"divIop","c":"Iop"};
		}else if(ct == "3" || ct == "diviop3" || ct == "draw" || ct == "drawing"){
			wcid = {"nm":"Draw","div":"divIop3","c":"Iop3","app":"app_drawing_OD","fod":"elem_Drawing_OD"};
		}
	break;
	case "DrawPane":
		var wcid = {"nm":"DrawPane","div":"divDraw","c":1,"app":"app_drawpane_drawing","fod":"elem_drawpaneDrawing"};
	break;
	case "DrawCL":
		var wcid = {"nm":"DrawCL","div":"divDraw","c":1,"app":"app_draw_cl","fod":"elem_draw_cl"};
	break;

	default:
		top.fAlert("Error:Please set exam tab info.");
	break;
	}

	return wcid;
}
//
function changeTab(id, obj){
	$(".tab-pane").not(".la-advance").removeClass("active");
	$("#div"+id).addClass("active");
	//$(".tabOn").filter(function(){  return $(this).parents(".advance").length == 0; }).removeClass("tabOn").addClass("tab");
	//$("#tab"+id).removeClass("tab").addClass("tabOn");
	if(id == "Pupil" || id == "Refractive Surgery"){return;}

	//
	setFlagCurTab();setNC();
	//Emergency Notes--
	if(examName == "Fundus"){
		if(id == "7"){ $(".emergency_status").show();	}
		else{ $(".emergency_status").hide(); }
	}
	//Emergency Notes--

	//
	$("#btn_rprt_intr").addClass("hidden");

	//alert(id);
	if((id == "Eom3") || (id == "Iop3") || (id == 5) || (id == 6) || (id == "Draw")){

		//drawingInit();
		var hid_drw_id="", dv_con="", exm_id="", exm_nm="";

		if(document.getElementById("hidEOMDrawingId0")){
			hid_drw_id = "hidEOMDrawingId";
			dv_con = "divconExam";
			exm_id = "elem_eomId";
			exm_nm = "EOM_DSU";
		}else if(document.getElementById("hidExternalDrawingId0")){
			hid_drw_id = "hidExternalDrawingId";
			dv_con = "divMaster";
			exm_id = "elem_eeId";
			exm_nm = "EXTERNAL_DSU";
		}else if(document.getElementById("hidLADrawingId0")){
			hid_drw_id = "hidLADrawingId";
			dv_con = "divMaster";
			exm_id = "elem_laId";
			exm_nm = "LA_DSU";
		}else if(document.getElementById("hidIOPDrawingId0")){
			hid_drw_id = "hidIOPDrawingId";
			dv_con = "divMaster";
			exm_id = "elem_gonioId";
			exm_nm = "IOP_GON_DSU";
		}else if(document.getElementById("hidSLEDrawingId0")){
			if(id == 6){
				hid_drw_id = "hidSLEDrawingId";
				dv_con = "divMaster";
				exm_id = "elem_sleId";
				exm_nm = "SLE_DSU";
			}
		}else if(document.getElementById("hidFundusDrawingId0")){
			if(id == 5){
				hid_drw_id = "hidFundusDrawingId";
				dv_con = "divMaster";
				exm_id = "elem_rvId";
				exm_nm = "FUNDUS_DSU";

				var org_key = typeof(obj)!="undefined" ? $(obj).data("key_org") : "" ;
				chngDfDrwType(org_key);

				$("#btn_rprt_intr").removeClass("hidden");
			}
		}

		AJAXLoadDarwingData_exe(hid_drw_id, dv_con, exm_id, exm_nm);
	}
}

function setTestTypeInReport(){
	var tt = "", dt="";
	dt= $("#elem_drawType").val();
	if(dt=="8"){tt = "Optic Nerve Drawing";}
	else if(dt=="9"){tt = "Macula Drawing";}
	else{tt = "Retina Drawing";}

	$("#ir_test_type").val(tt);
	$("#ir_test_type_label").text(tt);
}

function report_gentr(){

	//if($("#ir_test_type").val()==""){
		setTestTypeInReport();
	//}

	$("#rprtIntrModal").modal("show");
}

function del_inter_report(){
	var chart_draw_inter_report_id = $("#chart_draw_inter_report_id").val();
	if(chart_draw_inter_report_id!=""){
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("1");
		var strsave = "elem_saveForm=del_inter_report&id="+chart_draw_inter_report_id;
		$.post(zPath+"/chart_notes/saveCharts.php", strsave, function(data) {
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0");
			if(data.res=="0"){
				$("#elem_assessment1, #elem_assessment_dxcode1, #ir_dxid, #elem_plan1, #ir_ordered_by, #ir_test_type, #chart_draw_inter_report_id, #ir_test_type_label").val("");
				setTestTypeInReport();
				$("#ir_ordered_by").val(data.orderby);
				$("#lbl_ordered_by").val(data.orderbyNm);
			}
		},"json");
	}
}

//
var flg_chng_draw_type=0;
function openExmTab(f){
	var o = getWcId(f);
	if(o.tab && $("#tab"+o.tab).length>0){
		$("#tab"+o.tab).trigger("click");
	}else{
		var oc = o.c;
		if(examName=="Fundus" && (o.c==5||o.c == 8|| o.c==9)){
			var exdttp = $("#elem_drawType").val();
			if(exdttp!="" && exdttp!="0" && exdttp!=o.c){
				oc = exdttp;
				flg_chng_draw_type = o.c;
			}else{

			}
		}
		$("#tab"+oc).trigger("click");
	}
}

//
function setPositive(){
	if(arrSubExams==null||typeof(arrSubExams)=="undefined")return;

	flg = "0";
	var ln = arrSubExams.length;
	for(var i=0;i<ln;i++){
		var a = gebi("elem_pos"+arrSubExams[i]).value;

		if(a=="1"){
			flg = "1";
			break;
		}
	}

	gebi("elem_isPositive").value=flg;
}

function no_exm_hd(o){ return ($(o).parents(".examhd").length>0)?false:true; }
//
function freezeElem_eom(val){
	var wc = getWcId();
	if(val==1){
		$("#"+wc.div+" :input").filter(function(){return no_exm_hd(this);}).prop("disabled", true);
	}else{
		$("#"+wc.div+" :input").filter(function(){return no_exm_hd(this);}).prop("disabled", false);
	}
}

function freezeElem(f,chk)
{
	//EOM
	if(examName == "EOM"){
		freezeElem_eom(f);
		return;
	}
	//--

	var ict = getCurTab();
	freezeExe(ict,f,chk);
}

//
function setFlagCurTab()
{
	var wcid = getWcId();
	var flag = getYFlagCurTab();

	if(flag.Exam == true){
		$("#"+wcid.div+" #examFlag").css({"display":"inline-block"}).addClass("flagPos").removeClass("flagWnl");
	}else if(gebi("elem_wnl"+wcid.nm).value=="1"){
		$("#"+wcid.div+" #examFlag").css({"display":"inline-block"}).addClass("flagWnl").removeClass("flagPos");
	}else{
		$("#"+wcid.div+" #examFlag").hide();
	}
	setWnlEyeFlag(); //set WNL EYE Flags
}

//- Record Multi Message --
function record_MultiMedia_Message(){
	if(finalize_flag == 1){
		if(typeof(top.fAlert)!="undefined"){top.fAlert('Chart is finalized.');}
	}else{
		if(typeof(top.showRecordingControl)!="undefined"){top.showRecordingControl('exam_eom',$("#elem_formId").val(),$("#elem_patientId").val());}
	}
}

function play_MultiMedia_Messages(){
	var x = examName;
	if(x == "EOM"){x="exam_eom";}
	if(typeof(top.showMultiMediaMessage)!="undefined"){top.showMultiMediaMessage(x,$("#elem_formId").val());}
}

//---

function setNC_eom(flg){

	var wc = getWcId();
	//var id = "elem_nc" + wc.nm;
	var ct = wc.c;
	if(ct=="1"){
		var id="elem_examined_no_change";
		var nk = "elem_noChange";
	}else{
		var id="elem_noChange" + ct;
		var nk = "elem_noChange_draw";
	}

	var o = gebi(id);
	if(o){
		var y = gebi(nk);
		y.checked = (o.value == "1") ? true : false;
		if(flg==1&&y.checked==false){
			//Do nothing
		}else{
			freezeElem(o.value);
		}
	}
}

function setNC(flg){

	//EOM
	if(examName == "EOM"){
		setNC_eom(flg);
		return;
	}
	//--

	var wc = getWcId();
	var id = "elem_nc" + wc.nm;
	var o = gebi(id);
	if(o){
		var nck = (wc.c>1) ? wc.nm : "";
		var y = gebi("elem_noChange" + nck);
		y.checked = (o.value == "1") ? true : false;
		utElem_capture(y);
		if(flg==1&&y.checked==false){
			//Do nothing
		}else{
			freezeElem(o.value);
		}
	}

	// One Check Eye
	oneEye_check();
}

function setNC2_eom(o,fclear)
{
	if(fclear==1){
		$(":input").attr("disabled",false);
		return;
	}

	var wcid = getWcId();
	var ct = wcid.c;
	if(ct==1)ct="";
	var nck = (ct==3) ? "elem_noChange_draw" : "elem_noChange";
	if(typeof(o)=="undefined")o=gebi(nck);

	var f = (o.checked == true) ? "1" : "0";
	var od = gebi("divEom"+ct);

	if(od){
		var ar = new Array("INPUT", "TEXTAREA");
		for(var j=0;j<2;j++){

			var e = od.getElementsByTagName(ar[j]);
			var l = e.length;

			for(var i=0;i<l;i++)
			{
				if(e[i].name == nck) continue;
				if((e[i].type == "checkbox") || (e[i].type == "radio")){
					e[i].disabled = (f == "1") ? true : false;
				}else if((e[i].type == "textarea") ||( e[i].type == "text")){
					e[i].readOnly = (f == "1") ? true : false;
					if(f != "1"){e[i].disabled = false;	}
				}
			}
		}
	}

	//
	if(ct==""){
		var ar2 = new Array("elem_rightHeadTilt_Dis","elem_leftHeadTilt_Dis","elem_commentsDis",
					"elem_rightHeadTilt_Near","elem_leftHeadTilt_Near","elem_commentsNear");
		var len = ar2.length;
		for( var i=0;i<len;i++ ){
			var o = gebi(ar2[i]);
			if(o){
				if((o.type == "textarea") ||( o.type == "text")){
					o.readOnly = (f == "1") ? true : false;
				}
			}
		}
	}

	//
	if(ct==""){
		gebi("elem_examined_no_change").value=f;
	}else{
		gebi("elem_noChange"+ct).value=f;
	}

	//Set Exam Active;
	//callClickEv();

	//--
	//if Draw, NC is One
	if(ct==3){
		if(f==1){
			setAllDrawingToSave();
		}
	}
	//--
}

function setNC2(a,b){

	//EOM
	if(examName == "EOM"){
		setNC2_eom(a,b);
		return;
	}
	//--

	var wc = getWcId();
	var id = "elem_nc" + wc.nm;
	var o = gebi(id);

	if(examName == "Gonio"){
		var nck=(wc.c!="Iop") ? wc.nm : "";
	}else{
		var nck=(wc.c>1) ? wc.nm : "";
	}

	var o1 = gebi("elem_noChange"+nck);
	if(o){
		o.value = (o1.checked == true) ? "1" : "0";
		freezeElem(o.value,1);
		//Set GreyColor and changeIndicator
		var idDiv = getCurTab();
		if(newET_chkB4GrayExe(idDiv,"ou")){
			newET_setGray_Exe(idDiv,"ou");
		}
		// ---------
		//if Gonio--
		if(wc.nm=="Gonio" || (examName == "Gonio" && wc.nm=="Draw")){
			//set Indicator
			var tmp = $("#elem_ci_gonio").val();
			tmp = (typeof tmp == "undefined") ? 1 : parseInt(tmp)+1;
			$("#elem_ci_gonio").val(tmp);
		}
		//--

		//--
		//if Draw, NC is One
		if(wc.nm=="Draw"){
			if(o.value==1){
				setAllDrawingToSave();
			}
		}
		//--

	}

	setNCMain();
}

function setNCMain(){
	if(arrSubExams==null||typeof(arrSubExams)=="undefined")return;

	flg = "1";
	var ln = arrSubExams.length;
	for(var i=0;i<ln;i++){
		var a = gebi("elem_nc"+arrSubExams[i]).value;
		if(a!="1"){
			flg = "0";
			break;
		}
	}

	gebi("elem_examined_no_change").value=flg;
}

//--



function getYFlagCurTab(def_ict){
	var sfx="_";
	var flag=flagOd=flagOs=false;
	var ict = (typeof(def_ict)!="undefined" && def_ict!="") ? def_ict : getCurTab();
	var p1 = "#"+ict+" :checked[name*=Od][class!=ignore4YF],#"+ict+" :checked[name*=od"+sfx+"][class!=ignore4YF],#"+ict+" :checked[name*="+sfx+"od][class!=ignore4YF]";
	var p2 = "#"+ict+" :input[type!=checkbox][type!=hidden][class!=ignore4YF][name*=Od], "+
			 "#"+ict+" :input[type!=checkbox][type!=hidden][name*=od"+sfx+"], "+
			 "#"+ict+" :input[type!=checkbox][type!=hidden][name*="+sfx+"od] ";

	if($(p1).not(".ignore4YF").length>0){
		flag=flagOd = true;
	}else{
		var oh=$(p2).not(".ignore4YF").filter(function() { var t = $.trim(this.value);   if(t!="" && t!="Comments:"){return 1;} return 0; });
		if(oh.length>0){
		flag=flagOd = true;
		}
	}

	p1 = "#"+ict+" :checked[name*=Os][class!=ignore4YF],#"+ict+" :checked[name*=os"+sfx+"][class!=ignore4YF],#"+ict+" :checked[name*="+sfx+"os][class!=ignore4YF]";
	p2 = "#"+ict+" :input[type!=checkbox][type!=hidden][class!=ignore4YF][name*=Os], "+
		 "#"+ict+" :input[type!=checkbox][type!=hidden][name*=os"+sfx+"], "+
		 "#"+ict+" :input[type!=checkbox][type!=hidden][name*="+sfx+"os] ";
	if($(p1).not(".ignore4YF").length>0){
		flag=flagOs = true;
	}else{
		var oh=$(p2).not(".ignore4YF").filter(function() { var t = $.trim(this.value);   if(t!="" && t!="Comments:"){return 1;} return 0; });
		if(oh.length>0){
		flag=flagOs = true;
		}
	}

	//Drawing Tab:
	if(flagOd==false||flagOs==false){
		oWcid = getWcId(ict);
		if(oWcid.nm=="Draw"){
			if($("#"+ict+" input[name="+oWcid.fod+"][value!=''][value!='0-0-0:;']").length>0){
				flag=flagOd=flagOs = true;
			}else{
				var val = $("input[name='hidCanvasWNL']").val();
				if(typeof(val)!="undefined" && val.indexOf("no")!=-1){ //ifthere is a 'no' then flag = yes
					flag=flagOd=flagOs = true;
				}
			}
		}
	}

	return {"Exam":flag,"Od":flagOd,"Os":flagOs};
}
//wnl EOM
function setwnl_eom() //
{
	var wcid = getWcId();
	var ct = wcid.c;
	if(ct==1)ct="";
	var w = gebi("elem_wnl"+ct);
	var p = gebi("elem_isPositive"+ct);
	//var f = gebi("flagimage");

	//alert(w.value+" - "+p.value);

	if(w.value == "1"){
		w.value = "0";
		//f.style.display = "none";
		$("#"+wcid.div+" #examFlag").hide();

		//
		if(ct==""){ // This Happens if EOM Main Section is WNL, not in case of Drawing
			//Make NPC=WNL;EOM=Full&Ortho
			//"input[name='elem_npcWnlAbn'][value='WNL'],"+
			$("input[name='elem_eomFull'],input[name='elem_eomOrtho'],input[name='elem_ahp_no'],input[name='elem_nysta_no']").attr("checked",false).each(function(){ utElem_capture(this); });

			$("input[name*=elem_duction_]").val("").each(function(){ utElem_capture(this); });

			$(".headtilt input[type=text], .grid input[type=text][class!=apct]").val(function(indx){ return (indx%2==0) ? "": "";}).each(function(){ utElem_capture(this); });

			$(".grid input[type=text][class=apct]").val("").each(function(){ utElem_capture(this); });

			//f.src = "images/flag_gn2.png";
			//f.style.display = "block";
		}


	}else{

		if((p.value == "0") || (p.value == "")){
			w.value = "1";
			$("#"+wcid.div+" #examFlag").css({"display":"inline-block"}).addClass("flagWnl").removeClass("flagPos");

			if(ct==""){ // This Happens if EOM Main Section is WNL, not in case of Drawing
			//Make NPC=WNL;EOM=Full&Ortho
			//"input[name='elem_npcWnlAbn'][value='WNL'],"+
			$("input[name='elem_eomFull'],input[name='elem_eomOrtho'],input[name='elem_ahp_no'],input[name='elem_nysta_no']").attr("checked","checked").each(function(){ utElem_capture(this); });

			$("input[name*=elem_duction_]").val("0").each(function(){ utElem_capture(this); });

			$(".headtilt input[type=text], .grid input[type=text][class!=apct]").val(function(indx){ return (indx%2==0) ? "ORTHO": "0";}).each(function(){ utElem_capture(this); });

			$(".grid input[type=text][class=apct]").val("APCT").each(function(){ utElem_capture(this); });

			//f.src = "images/flag_gn2.png";
			//f.style.display = "block";
			}
		}
	}
	//Set Exam Active;
	callClickEv();

	//--
	//if Draw, WNL is One
	if(ct==3){
		if(w.value==1){
			setAllDrawingToSave();
		}
	}
	//--
}

function callClickEv(){
	//Set Exam Active;
	var wcid = getWcId();
	var dvId = wcid.div;
	var oDiv = gebi(dvId);
	var e=oDiv.getElementsByTagName("TEXTAREA");
	if(e){
		e[0].click();
	}
}

//wnl
function setwnl(eye){

	//exam name
	if(examName == "EOM"){
		setwnl_eom();
		return;
	}

	var wco = getWcId();
	var flag = getYFlagCurTab();
	var wcid = wco.nm;
	var seOd = gebi("elem_wnl"+wcid+"Od");
	var seOs = gebi("elem_wnl"+wcid+"Os");

	if(seOd.value == "")seOd.value = "0";
	if(seOs.value == "")seOs.value = "0";

	var tmp= (seOd.value == "0"||seOs.value == "0") ? "1" : "0";

	if(typeof(eye)=="undefined")eye="OU";

	if(flag.Exam == true){
		$("#"+wco.div+" #examFlag").css({"display":"inline-block"}).addClass("flagPos").removeClass("flagWnl");
		gebi("elem_isPositive").value=1;
		gebi("elem_pos"+wcid).value=1;
		gebi("elem_wnl").value=0;
		gebi("elem_wnl"+wcid).value=0;

		if(eye=="OU"||eye=="OD"){
			seOd.value= ((seOd.value == "0") && (flag.Od == false) && (oneEye_eye != "OD")) ? "1" : "0";
		}

		if(eye=="OU" || eye=="OS"){
			seOs.value= ((seOs.value == "0") && (flag.Os == false) && (oneEye_eye != "OS")) ? "1" : "0";
		}

	}else if(gebi("elem_wnl"+wcid).value==1){

		gebi("elem_wnl"+wcid).value=0;
		if(eye=="OU"||eye=="OD"){
			seOd.value=0;
		}
		if(eye=="OU"||eye=="OS"){
			seOs.value=0;
		}
		$("#"+wco.div+" #examFlag").hide();

	}else{

		if((oneEye_issue != "") && ((oneEye_eye != ""))){

			gebi("elem_wnl").value=0;
			if(eye=="OU"||eye=="OD"){
				seOd.value= ((seOd.value == "0") && (oneEye_eye != "OD")) ? "1" : "0";
			}
			if(eye=="OU"||eye=="OS"){
				seOs.value= ((seOs.value == "0") && (oneEye_eye != "OS")) ? "1" : "0";
			}

		}else{
			gebi("elem_pos"+wcid).value=0;
			if(eye=="OU"){
				seOd.value=seOs.value=tmp;
			}else if(eye=="OD"){
				seOd.value= (seOd.value == "0") ? "1" : "0";
			}else if(eye=="OS"){
				seOs.value= (seOs.value == "0") ? "1" : "0";
			}

			if(seOd.value==1 && seOs.value==1){
				gebi("elem_wnl"+wcid).value=1;
				$("#"+wco.div+" #examFlag").css({"display":"inline-block"}).addClass("flagWnl").removeClass("flagPos");
			}else{
				gebi("elem_wnl"+wcid).value=0;
				$("#"+wco.div+" #examFlag").hide().removeClass("flagWnl");
			}
		}
	}

	setWnlEyeFlag(); //set WNL EYE Flags

	if(!flag.Exam){
		setMainWNL();
	}

	//Set GreyColor and changeIndicator
	var idDiv = getCurTab();
	eye = eye.toLowerCase();
	if(newET_chkB4GrayExe(idDiv,eye)){
		newET_setGray_Exe(idDiv,eye);
	}

	//if Gonio--
	if(wco.nm=="Gonio" || (examName == "Gonio" && wco.nm=="Draw")){
		//set Indicator
		var tmp = $("#elem_ci_gonio").val();
		tmp = (typeof tmp == "undefined") ? 1 : parseInt(tmp)+1;
		$("#elem_ci_gonio").val(tmp);
	}
	//--

	// Draw : if WNL, set Drawing to save --
	if(wco.nm=="Draw"){
		if(seOd.value == "1" || seOs.value == "1"){
			setAllDrawingToSave();
		}
	}
	//--
}
//--



//
var wnlOptTimer;
function showEyeDD(val){

	if(val==-1){
		wnlOptTimer=clearTimeout(wnlOptTimer);
	}else if(val==0){
		if(wnlOptTimer==null){
				wnlOptTimer = setTimeout(function(){showEyeDD(0);},500);
		}else{
			$("#div_wnlOpts").hide();
			showEyeDD(-1);
		}

	}else{
		var wc = getWcId();
		var div = ""+
			"<div id=\"div_wnlOpts\" class=\"wnlOpts\" onmouseover=\"showEyeDD(-1);\" onmouseout=\"showEyeDD(0)\" >"+
			"<a href=\"javascript:void(0);\" class=\"ou\" onclick=\"setwnl('OU')\">OU</a><br/>"+
			"<a href=\"javascript:void(0);\" class=\"od\" onclick=\"setwnl('OD')\">OD</a><br/>"+
			"<a href=\"javascript:void(0);\" class=\"os\" onclick=\"setwnl('OS')\">OS</a>"+
			"</div>";
		$("body").append(div);
		var pos = $("#"+wc.div+" input.wnl").length>0 ? $("#"+wc.div+" input.wnl").offset() : $("#"+wc.div+" .wnl_btn").offset() ;
		$("#div_wnlOpts").show().css({'left' : ""+(pos.left-33)+"px", 'top' : ""+pos.top+"px"});
		showEyeDD(-1);
	}
}

function setMainWNL(){
	if(arrSubExams==null||typeof(arrSubExams)=="undefined")return;

	flg = "1";
	var ln = arrSubExams.length;
	for(var i=0;i<ln;i++){
		var a = gebi("elem_wnl"+arrSubExams[i]).value;
		if(a!="1"){
			flg = "0";
			break;
		}
	}

	gebi("elem_wnl").value=flg;
}

function setWnlEyeFlag(){
	var wcid = getWcId();
	var oWnl = gebi("elem_wnl"+wcid.nm);
	var seOd = gebi("elem_wnl"+wcid.nm+"Od");
	var seOs = gebi("elem_wnl"+wcid.nm+"Os");
	var flgOd=  $("#"+wcid.div+" #flagWnlOd")[0]; //gebi("flagWnlOd"); //$("")[0];//
	var flgOs=  $("#"+wcid.div+" #flagWnlOs")[0]; //gebi("flagWnlOs");
	if(flgOd)flgOd.style.display = ((oWnl.value == "0") && (seOd.value == "1")) ? "block" : "none";
	if(flgOs)flgOs.style.display = ((oWnl.value == "0") && (seOs.value == "1")) ? "block" : "none";
}

function getCurTab(){
	var ot = $(".tab-pane").not(".la-advance").filter(".active");
	if(ot.length>0){
		return ot.attr("id");
	}else if($(".tab-pane").length>0){
		$(".tab-pane").first().addClass("active");
		return getCurTab();
	}
}
//return tab of obj if found, else return ''
function getCurTabOfObj(o){
	if(typeof(o)!="undefined" && o){
		return $(o).parents(".tab-pane").attr("id");
	}else{
		return '';
	}
}

function npcCheckCb(obj){
	if( obj.checked == true ){
		var oGrp = document.getElementsByName(""+obj.name);
		var len = oGrp.length;
		for( var i=0;i<len;i++ ){
			if( ( oGrp[i].checked == true ) && (oGrp[i].value != obj.value) ){
				oGrp[i].checked = false;
				utElem_capture(oGrp[i]);
			}
		}
	}
}

function checkFunction(o)
{
	if(o.checked == true){

		//No Fly, No Butterfly
		if(o.name=="elem_ranSt[]"){
			var str = "#elem_ranSt_fly, #elem_ranSt_nofly";
			if(o.id.indexOf("butter")!=-1){
				str = "#elem_ranSt_butterfly, #elem_ranSt_nobutterfly";
			}
			$(str).attr("checked", false).each(function(){ utElem_capture(this);});
			o.checked=true;
			return;
		}

		//gebi("elem_eomFull").checked = false;// as per dr. brian's mail
		gebi("elem_eomOrtho").checked = false;// as per dr. brian's mail
		utElem_capture(gebi("elem_eomOrtho"));

		if(o.name=="elem_eomControl[]"){
			$(":input[name*=elem_eomControl]").attr("checked", false).each(function(){ utElem_capture(this);});
			o.checked=true;
			return;
		}

		var nt = o.name;
		var ptrn = "\\d$";
		var reg = new RegExp(ptrn,"g");
		var sn = nt;
		if(nt.match(reg) != null)
		{
			sn = nt.replace(reg,"");
		}
		var u;
		if((nt.indexOf("AesoAexo") != -1) || (nt.indexOf("VesoVexo") != -1)){
			u = 4;
		}else if((nt.indexOf("HyperHypo") != -1) || (nt.indexOf("TrophiaPhoria") != -1) || (nt.indexOf("EsoExo") != -1)){
			u = 2;
		}else if((nt.indexOf("NearFarBoth") != -1) || (nt.indexOf("RightLeftAlter") != -1)){
			u = 3;
		}
		//alert(u)
		for(var i=1;i<=u;i++)
		{
			var snt = (i>1) ? sn+i : sn ;
			var t = gebi(snt);
			if(t){
				if((t.checked == true) && (t.id != o.id))
				{
					t.checked = false;
					utElem_capture(t);
				}
			}
		}
	}
}


function setExamFlag(){
	var wc = getWcId();
	var ps = $("#elem_isPositive").val();
	var wnl = $("#elem_wnl").val();
	if(ps==1){$("#"+wc.div+" #examFlag").css({"display":"inline-block"}).addClass("flagPos").removeClass("flagWnl");}
	else if(wnl==1){$("#"+wc.div+" #examFlag").css({"display":"inline-block"}).addClass("flagWnl").removeClass("flagPos");}
	else{$("#"+wc.div+" #examFlag").hide();}
}

function setTabFlags( tab ){
	var arr =  (typeof tab != "undefined" ) ? new Array( tab ) : arrSubExams;
	var len = arr.length;
	for( var i=0;i<len;i++ ){
		var oPos = gebi("elem_pos"+arr[i]);
		var tmp = arr[i];
		if(tmp=="Gonio") tmp = "Iop"; //Exc
		if(examName=="Fundus" && tmp=="Draw"){
			var tid = $("#elem_drawType").val();
			if(tid=="8"){tmp="DrawON";}
			else if(tid=="9"){tmp="DrawMA";}
			else if(tid=="5"){tmp="Draw";}
		}
		var oImg = gebi("flagimage_"+tmp);
		if(oPos && oImg ){
			oImg.style.display = ( (oPos.value == "1") ) ? "inline-block" : "none";
		}
	}
}

function checkNC1(obj){
	if(no_exm_hd(obj) && (obj.disabled==true||obj.readOnly==true)){ //
		var wc = getWcId();
		var o = $("#"+wc.div+" input[id*=elem_noChange]")[0]; //gebi("elem_noChange");
		if(o && o.checked==true){
			o.checked=false;
			o.onclick();
			utElem_capture(o);//

		}
	}else if(no_exm_hd(obj) && examName == "EOM"){ callClickEv(); }
}

function isNotEomField(name){

	var arrNotEomFields=[
	"elem_eomFull", "elem_eomOrtho",
	"elem_color_sign_od", "elem_color_od_1",
	"elem_color_od_2", "elem_color_sign_os",
	"elem_color_os_1", "elem_color_os_2",
	"elem_comm_colorVis", "elem_w4dot_distance",
	"elem_w4dot_near", "elem_comm_w4Dot",
	"elem_ranSt[]", "elem_ranSt_Dots9",
	"elem_stereo_SecondsArc"
	];

	//return 0;

	if(arrNotEomFields.indexOf(name)!=-1){
		return 1;
	}else{
		return 0;
	}
}

function setYFlag(f)
{
	//
	var wcid = getWcId();
	ct = wcid.c;
	if(ct=="1")ct="";
	if(f == true){
		$("#"+wcid.div+" #examFlag").css({"display":"inline-block"}).addClass("flagPos").removeClass("flagWnl");
		//gebi("flagimage").src = "images/flag_yellow.png";
		//gebi("flagimage").style.display = "block";
		gebi("elem_posEom"+ct).value = gebi("elem_isPositive"+ct).value = "1";
		gebi("elem_wnlEom"+ct).value = gebi("elem_wnl"+ct).value = "0";

	}else{
		if(gebi("elem_wnl"+ct).value != "1"){
			//gebi("flagimage").style.display = "none";
			$("#"+wcid.div+" #examFlag").hide();
		}
		gebi("elem_posEom"+ct).value = gebi("elem_isPositive"+ct).value = "0";
	}
}

function yFinalCheck(){

	//var e = document.frmEom.elements;
	var wcid = getWcId();
	ct=wcid.c;
	if(ct==1)ct="";

	var odiv = gebi("divEom"+ct);

	var e = odiv.getElementsByTagName("*");
	var l = e.length;
	var f,ret=false,ret_2=0;
	for(var i=0;i<l;i++)
	{
		f = e[i];
		//var d_elem = f.getAttribute("elementDefaultValue");

		if(typeof(f.name) == "undefined" || f.id.indexOf("-") != -1 || isNotEomField(f.name)){continue;}

		//check for wnl filled values
		if($("#elem_wnl"+ct).val()=="1"){
			if((f.type == "text"||f.type == "textarea")){

			if((f.name.indexOf("elem_duction_")!=-1&&f.value == "0") ||
				($(f).hasClass("apct") && f.value=="APCT") ||
				(($(f).parents(".headtilt").length>0 || $(f).parents(".grid").length>0) && (f.value=="ORTHO" || f.value=="0"))
				){continue;}
			}else if(f.type == "checkbox"){
				if(f.name=="elem_nysta_no"||f.name=="elem_ahp_no"){ continue; }
			}
		}else{
			if((($(f).hasClass("apct") && f.value=="APCT") || (f.name.indexOf("elem_duction_")!=-1&&f.value == "0")) && $("#elem_chng_divEom").val()!=1){  continue; }
		}

		if(f.type == "checkbox"){
			//if(typeof(d_elem)== "undefined"){
				if(f.checked){

					ret= true;
					break;
				}
			///}

		}else if((f.type == "text") || (f.type == "textarea")){
			if((f.value != "") && (f.value != "Comments")){
				ret= true;break;
			}
		}else if( f.name == "elem_eomDrawing_2" ){

			if( $.trim(f.value) != "" ){
				ret= true;break;
			}
		}
	}

	//Drawing Tab:
	if(ret==false){
		oWcid = wcid;
		if(oWcid.nm=="Eom3"){
			if($("#divEom"+ct+" input[name="+oWcid.fod+"][value!=''][value!='0-0-0:;']").length>0){
				ret= true;
			}else{
				//if($("input[name='hidCanvasWNL'][value='no']").length>0){
				var val = $("input[name='hidCanvasWNL']").val();
				if(typeof(val)!="undefined" && val.indexOf("no")!=-1){ //ifthere is a 'no' then flag = yes
					ret= true;
				}
			}

		}
	}
	//EOM Excep
	if(ret==false && (ret_2==0||ret_2==3)){
		ret=false;
	}else{
		ret=true;
	}
	return ret;
}

function checkYFlagP(o){

	//if(isNotEomField(o.name)){return;}

	var cv = $.trim(o.value);
	var dv;
	dv = ""+o.getAttribute("elementDefaultValue"); //o.elementDefaultValue;
	if(typeof dv != "undefined"){
		dv = $.trim(dv);
	}

	///alert(dv);

	var tt;
	//*
	if(isNotEomField(o.name)){
		// do nothing

	}else if(o.type == "checkbox"){
		if(o.checked){

			setYFlag(true);

		}else{
			// Fc
			tt = yFinalCheck();
			setYFlag(tt);
		}
	}else if((o.type == "textarea") || (o.type == "text")){
		if((cv != "") && (cv != "Comments")&&(cv != "0")&&(cv != "ORTHO")){
			//Ptrue
			setYFlag(true);
		}else{
			//Fc
			tt = yFinalCheck();
			setYFlag(tt);
		}
	}else if(o.type == "radio"){
		if(cv != dv){
			//Ptrue
			setYFlag(true);
		}else{
			//Fc
			tt = yFinalCheck();
			setYFlag(tt);
		}
	}else if(o.name == "elem_eomDrawing_2"){ //drawing
		if( $.trim(o.value) != "" ){
			//Ptrue
			setYFlag(true);
		}else{
			//Fc
			tt = yFinalCheck();
			setYFlag(tt);
		}
	}
	//
	setTabFlags();
}

//check Absent
function checkAbsent(obj,ddmnu,cls){
	var o,eye="";
	var enm = obj.name;
	if(obj.name.indexOf("Od")!=-1){eye="Od";}
	if(obj.name.indexOf("Os")!=-1){eye="Os";}
	if(eye==""){return;}

	//check pupil APD
	if(examName == "Pupil"){
		if(obj.checked){
		if(obj.name.indexOf("elem_apd")!=-1){
			var ar = ['neg','trace','pos1','pos2','pos3','pos4','rapd'];
			if(obj.name.indexOf("neg")!=-1){
				$(":checked[name*=elem_apd"+eye+"_]").filter(function(){return (this.name.indexOf("neg")!=-1) ? false : true;}).prop("checked", false);
			}else{
				$(":checked[name*=elem_apd"+eye+"_neg]").prop("checked", false);
			}
		}
		checkwnls();
		}
		return;
	}
	//

	var trid = $(obj).parents("tr").attr("id");
	if(typeof(trid)=="undefined"){o=$(obj).parents("tr"); }
	else{
		var trid = regReplace('\\d+$','',trid);
		if(trid.indexOf("d_Puncta")!=-1){ trid="d_Puncta"; }
		o=$("tr[id*="+trid+"]");
	}

	//Check Absent
	if((enm.toLowerCase().indexOf("absent")!=-1||obj.value=="-ve"||""+obj.value=="Absent")&&obj.checked==true){
		var str = (typeof(cls)!="undefined") ? ""+cls : ":input";
		//alert("INSIDE: "+$(obj).parents("div.symOpt").find(":input").length);
		//clear all other elements
		if(o){	o.find(str).each(function(index){ if(typeof(this.name) != "undefined" && this.name.indexOf(eye)!=-1){ if(this.name!=enm){  if(this.type=="checkbox"){ this.checked=false; }else{  this.value=""; }  utElem_capture(this);}}    });}
	}else{
		var str = (typeof(cls)!="undefined") ? ""+cls : ":checked";
		//clear absent
		if(o){ o.find(str).each(function(index){  if(typeof(this.name) != "undefined" && this.name.indexOf(eye)!=-1){ if(this.name.toLowerCase().indexOf("absent")!=-1||this.value=="-ve"||""+this.value=="Absent"){  this.checked=false; utElem_capture(this);}}  }); }
	}
	checkwnls();
	if(typeof(ddmnu)!="undefined"&&ddmnu!=""){
		checkSymClr(obj,ddmnu);
	}
}

function checkSymClr(obj,id){
	/*
	if($("#d_"+id+"_od :checked").length>0 || $("#d_"+id+"_od :input[type!=checkbox][value!='']").length>0){
		if($("#d_"+id+"_od .btn").hasClass("sbGrpDone")==false) $("#d_"+id+"_od .btn").addClass("sbGrpDone");
	}else{
		$("#d_"+id+"_od .btn").removeClass("sbGrpDone");
	}

	if($("#d_"+id+"_os :checked").length>0 || $("#d_"+id+"_os :input[type!=checkbox][value!='']").length>0){
		if($("#d_"+id+"_os .btn").hasClass("sbGrpDone")==false) $("#d_"+id+"_os .btn").addClass("sbGrpDone");
	}else{
		$("#d_"+id+"_os .btn").removeClass("sbGrpDone");
	}
	*/
}

//func reset
function funReset_exam(dr){
	dr = dr || "";
	var wcid = getWcId();

	if(examName == "DrawPane"){  resetDrawing(dr); return;}

	$("#"+wcid.div+" :checked").attr("checked",false).each(function(){utElem_capture(this);});
	$("#"+wcid.div+" textarea,"+"#"+wcid.div+" :text,"+"#"+wcid.div+" select").not(".drwctrl").val("").each(function(){utElem_capture(this);});
	if((wcid.nm=="Draw") || (wcid.nm=="Eom3")){
		if($("div[id*=divCanvas]").length>0){ resetDrawing(dr); }
	}

	//wnl+pos
	$("input[name=elem_wnl"+wcid.nm+"],"+"input[name=elem_wnl"+wcid.nm+"Od],"+"input[name=elem_wnl"+wcid.nm+"Os],"+
		"input[name=elem_pos"+wcid.nm+"]").val("0");

	//NC
	$("#"+wcid.div+" input[name=elem_noChange]").attr("checked",false).triggerHandler("click");


	if(examName=="Gonio"){ //Gonio

		$("#hdr_iop :checked").attr("checked",false).each(function(){utElem_capture(this);});
		$("#hdr_iop textarea,"+"#hdr_iop :text,"+"#hdr_iop select").val("").each(function(){utElem_capture(this);});

		//run event handler
		$("#"+wcid.div+" :input:not(.greyAll_v2)").eq(1).triggerHandler("keyup");
		if(wcid.div=="divIop1"){
			$("#divDilation :input:not(.greyAll_v2)").eq(1).triggerHandler("keyup");
			$("#divOOD :input:not(.greyAll_v2)").eq(1).triggerHandler("keyup");
		}
	}else if(examName=="SLE"){ //Sle
		$("input[name=elem_penLight]").attr("checked",false);
	}else if(wcid.nm=="Pupil"){ //Pupil: Pharma
		$("input[name=elem_pharmadilated]").attr("checked",false);
	}

	if(wcid.nm=="Eom"||wcid.nm=="Eom3"){
		//wnl+pos
		var ct = ""+wcid.c;
		if(ct=="1")ct="";
		$("input[name=elem_wnl"+ct+"],"+"input[name=elem_isPositive"+ct+"]").val("0");
	}

	//clearLessionPlasticColors
	$(".btnModifiers").removeClass("btnModifiersActive");

	//
	checkwnls();

}

function checkwnls_eom(){
	//setFlagCurTab();
	var tt = yFinalCheck();

	//
	if(tt){
		var wcid = getWcId();
		ct=wcid.c;
		if(ct == 3){
			$("#divEom3").triggerHandler("click");
		}
	}

	setYFlag(tt);
	setTabFlags();
}

function checkwnls(obj){

	//EOM
	if(examName == "EOM"){
		checkwnls_eom();
		return;
	}
	//--

	if(typeof(obj)=="undefined"){
	var wco = getWcId();
	var flag = getYFlagCurTab();
	}else{
		var inx = getCurTabOfObj(obj);
		var wco = getWcId(inx);
		var flag = getYFlagCurTab(inx);
	}

	var wcid = wco.nm;
	if(wcid == "DrawPane"||wcid == "DrawCL"){return;}



	if(flag.Exam == true){

		$("#"+wco.div+" #examFlag").css({"display":"inline-block"}).addClass("flagPos").removeClass("flagWnl");
		gebi("elem_isPositive").value=1;
		gebi("elem_wnl").value=0;
		gebi("elem_wnl"+wcid).value=0;
		gebi("elem_pos"+wcid).value=1;
		var seOd = gebi("elem_wnl"+wcid+"Od");
		var seOs = gebi("elem_wnl"+wcid+"Os");
		if(seOd && seOd.value == "")seOd.value = "0";
		if(seOs && seOs.value == "")seOs.value = "0";
		if(seOd)seOd.value = ((seOd.value == "1") && (flag.Od==true)) ? "0" : seOd.value;
		if(seOs)seOs.value = ((seOs.value == "1") && (flag.Os==true)) ? "0" : seOs.value;
	}else if(gebi("elem_wnl"+wcid).value==1){
		$("#"+wco.div+" #examFlag").css({"display":"inline-block"}).addClass("flagWnl").removeClass("flagPos");
	}else{
		$("#"+wco.div+" #examFlag").hide();
		gebi("elem_isPositive").value=0;
		gebi("elem_wnl").value=0;
		gebi("elem_pos"+wcid).value=0;
	}

	setWnlEyeFlag(); //set WNL EYE Flags

	if(!flag.Exam){ // Set main Positive
		setPositive();
	}

	//set Tab flags
	setTabFlags();

	/*
	//Set Status if draw
	if(wcid.toLowerCase()=="draw"){
		//Set Change
		$("#"+wco.div+" textarea").triggerHandler("keyup");
	}
	*/
}

//Biletral ---
var ar_check_bl;
function check_bl(obj){
	//run when call all BL : check uniquenes
	if(ar_check_bl && ar_check_bl.check){
		if(ar_check_bl.ar && ar_check_bl.ar.indexOf(obj)==-1){
			ar_check_bl.ar[ar_check_bl.ar.length] = obj;
		}else{
			return;
		}
	}
	//-

	var nm_2="";

	if(obj == "surgiAlt"){
		$("#elem_Od_"+obj+"").each(function(i){
			$("#elem_Os_"+obj+"").attr("checked",this.checked).each(function(){ utElem_capture(this); });
		});
	}else{

	//console.log("IN1");
	if($(".grp_"+obj).length>0){
		var x = ".grp_"+obj;
	}else{
		var x = "#d_"+obj;
	}

	//Search in opened tab only
	if(typeof(examName)!="undefined" && (examName == "LA" || examName == "SLE" || examName == "Fundus")){
		var wc = getWcId(); if(wc.div && $.trim(wc.div)!=$.trim(x)) { x = "#"+wc.div+" "+x;}
	}


	var ptrn = 	x+" :input[name*=Od_],"+
				x+" :input[name*=_od],"+
				x+" :input[name*=Od],"+
				x+" :input[name*=od_]";
	//console.log("IN1:: "+ptrn);

	$(ptrn).each(function(i){
		if(this.type=="button"){return;}
		nm_2 = this.name.replace(/Od/g, "Os");
		if($(x+" :input[name="+nm_2+"]").length==0){
			if(this.value=="RUL"){nm_2 = nm_2.replace(/rul/g, "lul");}
			else if(this.value=="RLL"){nm_2 = nm_2.replace(/rll/g, "lll");}
		}

		if($(this).parents(".divLesionModifier").length==1&&this.type=="checkbox"){
			//check value
			var val_2 = this.value;
			$(x+" :input[name="+nm_2+"][value='"+val_2+"']").prop("checked",this.checked).each(function(){ utElem_capture(this); });

		}else{

			if(this.type=="checkbox"){
				$("#d_Entro :input[name=elem_entroOs_Absent]").hide();
				$(x+" :input[name="+nm_2+"]").prop("checked",this.checked).each(function(){ utElem_capture(this); });
			}else{
				$(x+" :input[name="+nm_2+"]").val(this.value).each(function(){ utElem_capture(this); });
			}
		}

		/*
		//CheckEvents
		$(this).triggerHandler("click");
		$(this).triggerHandler("change");
		$(this).triggerHandler("blur");
		.triggerHandler("click,change,blur")
		.triggerHandler("click,change,blur")
		*/

	});

	}//end else

	if(nm_2!=""){
		var tmp = $(x+" :input[name="+nm_2+"]").eq(0).attr('type');
		var tmp2 = ""+$(x+" :input[name="+nm_2+"]").eq(0).attr('onclick');

		if(tmp=="checkbox"){

			if(tmp2.toLowerCase().indexOf("checkabsent")==-1&&tmp2.toLowerCase().indexOf("placs_checkSingle")==-1){//checked if checkAbsent is not a function
				$(x+" :input[name="+nm_2+"]").triggerHandler("click");
			}else{ // if yes

				if($(x+" :checked").length>0){ //checked if any thing is checked on that is triggered

					$(x+" :checked").each(function(){$(this).triggerHandler("click");});

				}else{// else anybody will do
					$(x+" :input[name="+nm_2+"]").triggerHandler("click");
				}
			}

		}else{
			$(x+" :input[name="+nm_2+"]").triggerHandler("change");
			var tmp2 = $(x+" :input[name="+nm_2+"]").eq(0).attr('onblur');
			if(typeof(tmp2)=="undefined" || $.trim(tmp2)==""){//fix for clinical extension
				checkAbsent($(x+" :input[name="+nm_2+"]")[0]);
			}else{
				$(x+" :input[name="+nm_2+"]").triggerHandler("blur");
			}
		}
		if(typeof(examName)!="undefined"&&examName=="Gonio"){
			$(x+" :input[name="+nm_2+"]").trigger("mouseup");
		}
	}
}
//
function check_bl_strt(comastr){

	if(examName=="Pupil"){
		if($("#os_pr").html() == "PERRLA"){ return; }
	}

	var arr = comastr.split(",");
	var ln=arr.length;
	for(var i=0;i<ln;i++){
		check_bl(arr[i]);
	}
}
//
function check_bilateral(wh){
	ar_check_bl = {};
	ar_check_bl.check = true;
	ar_check_bl.ar = [];
	if(typeof(wh)!="undefined" && wh != ""){
		if(wh == "lids_advc"){
			if($("#div6").hasClass("active")){
				$("#div6 .bilat").not(".bilat_all").trigger("click");
			}else if($("#div7").hasClass("active")){
				$("#div7 .bilat").not(".bilat_all").trigger("click");
			}
		}else if(wh == "lacri_advc"){
			$("#div8 .bilat").not(".bilat_all").trigger("click");
		}
	}else{
		var oTb = getCurTab();
		$("#"+oTb+" .bilat").not($(".la-advance .bilat, .bilat_all")).trigger("click");
	}
	ar_check_bl.check = false;
	ar_check_bl.ar.length = 0;
}
//
function openSubGrp(obj){
	var obj_id="d_"+obj;
	var obj_cls="tr.grp_"+obj;

	if($("#d_"+obj+"").length==0){
		var t = $("tr.grp_"+obj+"").filter(".grp_handle").attr("id");
		if($("#"+t).length>0){obj_id=t;}
	}

	if($("#"+obj_id+"").hasClass("sbGrpOpen")){
		$(""+obj_cls+"").removeClass("sbGrpOpen");
		$("#"+obj_id+" .glyphicon").addClass("glyphicon-menu-down").removeClass("glyphicon-menu-up");
	}else{
		$(""+obj_cls+"").addClass("sbGrpOpen");
		$("#"+obj_id+" .glyphicon").addClass("glyphicon-menu-up").removeClass("glyphicon-menu-down");
	}
}

//freeze
function freezeElemAll(f){
	$(":input").removeAttr("disabled");
}
function freezeExe(ict,f,chk)
{

	if(finalize_flag == 1 && isReviewable != true){
		return;
	}

	//Check for One Eye
	var ignore = "";
	chk = (chk == 1) ? "1" : "0";
	if(chk == 1){
		if((oneEye_issue!="") && (oneEye_eye != "")){
			ignore = ""+oneEye_eye;
		}
	}

	if(ignore == "OD"){

		if(f == true){
			$("#"+ict+" :input[name*='os_'],#"+ict+" :input[name*='Os'],").attr("disabled", "disabled");
		}else{
			$("#"+ict+" :input[name*='os_'],#"+ict+" :input[name*='Os'],").removeAttr("disabled");
		}

	}else if(ignore == "OS"){

		if(f == true){
			$("#"+ict+" :input[name*='od_'],#"+ict+" :input[name*='Od'],").attr("disabled", "disabled");
		}else{
			$("#"+ict+" :input[name*='od_'],#"+ict+" :input[name*='Od'],").removeAttr("disabled");
		}

	}else{

		if(f == true){
			$("#"+ict+" :input[type!='button'][type!='submit'][name!='elem_noChange']").filter(function(){return no_exm_hd(this);}).attr("disabled", "disabled");
		}else{
			$("#"+ict+" :input[type!='button'][type!='submit'][name!='elem_noChange']").removeAttr("disabled");
		}
	}

}





/* EOM */
function setGridVal(c,obj){
	var divIndx =(obj.name.indexOf("_1")!=-1) ? 1 : 0;
	if(obj.value == "Comitant"){
		//Depressing the button will fill in all 9 squares of the grid with the numbers that have been entered in the primary gaze grid
		//get values
		$("#Grid_"+c+" div.grid:eq("+divIndx+") td:eq(4) input[type=text]").each(function(index){
				var val_in = this.value;
				$("#Grid_"+c+" div.grid:eq("+divIndx+")  table:eq(0) td").each(function(index2){
							if(index2!=4&&index2!=0&&index2!=2&&index2!=6&&index2!=8){$(this).find("input[type=text]:eq("+index+")").val(""+val_in).each(function(indx){if(finalize_flag==1&&isReviewable!=1&&indx>0){ return;} this.click(); });}
						});
			});

	}else{
		var flgEmp=0;
		if($("#Grid_"+c+" div.grid:eq("+divIndx+")  table td input[type=text][value='0'][class!=apct]").length==18){
			flgEmp=1;
		}

		//Depressing the button would fill in all boxes with 0s (zero) or empty them
		$("#Grid_"+c+" div.grid:eq("+divIndx+")  table td input[type=text][class!=apct]").val(function(indx){ if(flgEmp==1){ return ""; }else{return (indx%2==0) ? "0" : "ORTHO" ;} 	}).each( function(){ if(finalize_flag==1&&isReviewable!=1&&indx>0){ return;} this.click();	});
	}
}

function wv_get_simple_menu_js(mn_nm,id){
		var x = $("."+mn_nm+":eq(0)").prop("outerHTML");
		var trgtid=	$("."+mn_nm+":eq(0) button").data("trgt-id");
		x = x.replace("data-trgt-id=\""+trgtid+"\"", "data-trgt-id=\""+id+"\"");
		return x;
}

function addGrid(){

	var counterGrid = parseInt($("input[name=elem_counterGrid]").val()) + parseInt(1); //add one
	$("input[name=elem_counterGrid]").val(counterGrid);

	var arr_grid_type=["Distance","Near"];

	var grid = "<div id=\"Grid_"+counterGrid+"\" class=\"row gridrow\" >";

	for(var z in arr_grid_type){

	var labelGrid=arr_grid_type[z];
	var elem_gf = "elem_gf_"+labelGrid+"_"+counterGrid+"[]";
	var elem_comm = "elem_comm_"+labelGrid+"_"+counterGrid+"";

	var elem_apct = "elem_apct_"+labelGrid+"_"+counterGrid+"";
	var elem_sc = "elem_sc_"+labelGrid+"_"+counterGrid+"";
	var elem_cc = "elem_cc_"+labelGrid+"_"+counterGrid+"";
	var elem_ccprisms = "elem_ccprisms_"+labelGrid+"_"+counterGrid+"";
	var elem_bifocal = "elem_bifocal_"+labelGrid+"_"+counterGrid+"";


	var indx=0;

	//
		//Add buttons
		var add_btn = "";
		if(labelGrid=="Near"){
			add_btn ="<input type=\"button\" name=\"btn_delgrid\" value=\"Remove\"  class=\"dff_button_sm btn btn-sm btn-danger\" onclick=\"delGrid('"+counterGrid+"')\">";
		}

		grid +=	"<div class=\"col-sm-6\">"+
					"<div class=\"exambox\">"+
					"<div class=\"grid\">"+
						"<div class=\"head\">"+
							"<div class=\"row\">"+
								"<div class=\"col-md-1\" ><h2>"+labelGrid+"</h2></div>"+
								"<div class=\"col-md-11 form-inline text-right\">"+
										"<input type=\"button\" name=\"btn_rst_"+z+"\" value=\"Set To Ortho\" class=\"dff_button_sm exambut\"  onclick=\"setGridVal('"+counterGrid+"',this)\">"+
										"<div class=\"input-group\">"+
										"<input type=\"text\" id=\""+elem_apct+"\" name=\""+elem_apct+"\" value=\"\" class=\"apct form-control\" onblur=\"checkYFlagP(this);\" >";

		grid += wv_get_simple_menu_js("menu_apct",elem_apct);
		grid +=							"</div>";

		grid +=							"<input type=\"checkbox\" id=\""+elem_sc+"\" name=\""+elem_sc+"\" value=\"1\"  onclick=\"checkYFlagP(this);\" ><label for=\""+elem_sc+"\">SC</label>"+
										"<input type=\"checkbox\" id=\""+elem_cc+"\" name=\""+elem_cc+"\" value=\"1\"  onclick=\"checkYFlagP(this);\" ><label for=\""+elem_cc+"\">CC</label>"+
										"<input type=\"checkbox\" id=\""+elem_ccprisms+"\" name=\""+elem_ccprisms+"\" value=\"1\"  onclick=\"checkYFlagP(this);\" ><label for=\""+elem_ccprisms+"\">CC Prisms</label> ";

		if(labelGrid=="Near"){
		grid +=							"<input type=\"checkbox\" id=\""+elem_bifocal+"\" name=\""+elem_bifocal+"\" value=\"1\"  onclick=\"checkYFlagP(this);\" ><label for=\""+elem_bifocal+"\">Bifocal</label> ";
		}

		grid +=							"<input type=\"button\" name=\"btn_comi_"+z+"\" value=\"Comitant\"  class=\"dff_button_sm exambut\" onclick=\"setGridVal('"+counterGrid+"',this)\">"+
										add_btn+
								"</div>"+
							"</div>"+
						"</div>"+
						"<div class=\"clearfix\"></div>"+
						"<div class=\"pd10\">"+
							"<div class=\"row\">";

		grid +=	"<table class=\"table table-responsive\">";
		for(var tr=1;tr<=3;tr++){
		var csstr="";
		if(tr==3){csstr="class=\"lasttr\"";}

		grid +=	"<tr "+csstr+" >";

			for(var td=1;td<=3;td++){

				if(td==3){var css="class=\"lasttd form-inline\"";}else{var css="class=\"form-inline\"";}

				grid +="<td  "+css+" >";
					for(var inp=1;inp<=4;inp++){
						var elem_gf_id = "elem_gf_"+labelGrid+"_"+counterGrid+"_"+indx;

						grid += "<div class=\"input-group\">";
						grid += "<input type=\"text\" id=\""+elem_gf_id+"\" name=\""+elem_gf+"\" value=\"\" onblur=\"checkYFlagP(this);checkFunction(this);\" class=\"form-control\" aria-label=\"...\" >";


						if(inp==2){
							//arr_grid_options = (labelGrid=="Distance") ? arr_ht_ESO_options_dis : arr_ht_ESO_options_near;
							grid += wv_get_simple_menu_js("menu_grids_eso_"+labelGrid,elem_gf_id);
						}else if(inp==4){
							//arr_grid_options = (labelGrid=="Distance") ? arr_ht_Hyper_options_dis : arr_ht_Hyper_options_near;
							grid += wv_get_simple_menu_js("menu_grids_hyper_"+labelGrid,elem_gf_id);
						}else{
							grid += wv_get_simple_menu_js("menu_grids_num",elem_gf_id);
						}

						grid += "</div>";

						if(inp==2){ grid += "<br/>"; }
						indx++; //Increment
					}
				grid +="</td>";

			}

		grid +=	"</tr>";

		}

		grid +=	"</table>";


		grid += 			"</div>";//row


		grid +=   "<div class=\"examcomt\"><textarea name=\""+elem_comm+"\" onblur=\"checkYFlagP(this);\" class=\"form-control\" ></textarea></div>"+
				"<div class=\"clearfix\"></div>";
		grid +=   "	</div>";//pd
		grid +=   "	</div>";//grid

		//<!-- Head Tilt -->

		grid += addHeadTilt(counterGrid,labelGrid);

		//<!-- Head Tilt -->

		grid +=   "	</div></div>";//col-sm-6, exambox


	}

	grid += "</div>";

	//document.write(grid);

	//append to grids
	$("#Grids").append(grid);
	wv_activate_menu_click();

}

function addHeadTilt(counterHT, labelGrid){

	//var arr_ht_type=["Right","Left"];
	var coords="-1,-1,-1,25";
	var coords_sm="-1,-1,-1,25";

	//var grid = "<div id=\"HT_"+counterHT+"\" class=\"htrow\" >";
	var grid = "";
	//for(var z in arr_ht_type){

	var labelHT= (labelGrid=="Distance") ? "Right" : "Left";  //arr_ht_type[z];
	var elem_ht = "elem_ht_"+labelHT+"_"+counterHT+"[]";
	var elem_comm = "elem_comm_"+labelHT+"_"+counterHT+"";
	var indx=0;

	grid +=	"<div class=\"headtilt\">"+
				"<div class=\"row\">"+
				"<table class=\"table table-responsive\">"+
				"<tr>"+
				"<td>"+
				"<h3>"+labelHT+" Head Tilt</h3>"+
				"</td>";

				//tmpMenuAdd = (labelHT=="Right") ? "Distance" : "Near" ;
				tmpMenuAdd = "Distance";

				grid +="<td class=\"form-inline\">";

				for(inp=1;inp<=4;inp++){
					elem_ht_id = "elem_ht_"+labelHT+"_"+counterHT+"_"+indx;
					grid += "<input type=\"text\" id=\""+elem_ht_id+"\" name=\""+elem_ht+"\" value=\"\" onblur=\"checkYFlagP(this);checkFunction(this);\" class=\"form-control\">";
					grid += "<div class=\"input-group\">";
					if(inp==2){
						//arr_ht_ESO_options = arr_ht_ESO_options_dis;
						grid += wv_get_simple_menu_js("menu_grids_eso_"+tmpMenuAdd,elem_ht_id);
					}else if(inp==4){
						//arr_ht_options = arr_ht_Hyper_options_dis;
						grid += wv_get_simple_menu_js("menu_grids_hyper_"+tmpMenuAdd,elem_ht_id);
					}else{
						grid += wv_get_simple_menu_js("menu_grids_num",elem_ht_id);
					}
					grid +="</div>";
					if(inp==2){ grid += "<br/>"; }
					indx++; //Increment
				}

				grid +="</td>";
				grid +="</tr></table>";
				grid +="</div>";

		grid +=   "<div class=\"examcomt\"><textarea name=\""+elem_comm+"\" class=\"form-control\" ></textarea></div>";
		grid +=   "</div>";

	//}

	return grid;
}

//delete grid
function delGrid(cntr){
	$("#Grid_"+cntr).remove();
}
//--

//----- Pupil --------------

	function chk_Perrla(g){

		var e = document.getElementById("elem_perrla");
		//find from form and reset g::load
		if(typeof(g)!="undefined" && g==-1){
			if(e.value == "1"){
				g=1;
			}else{
				return;
			}
		}//

		//
		var od=os=false;
		//alert(document.pupil.od);
		if(window.opener.document.getElementById("phth_pros").value=="Prosthesis" || window.opener.document.getElementById("phth_pros").value=="Phthisis"){
			od= (window.opener.document.getElementById("is_od_os").value == "OD") ? true : false;
			os= (window.opener.document.getElementById("is_od_os").value == "OS") ? true : false;
		}
		//alert(od+"::"+os);

		if(g != "1"){
			if(e.value == "0"){
				e.value = "1";
				//opener.document.getElementById("pupil_ref").innerHTML = "PERRLA";
			}else {
				e.value = "0";
				//opener.document.getElementById("pupil_ref").innerHTML = "";
			}
		}

		if(od==false){

			for(var i=1;i<=46;i++){
				var t = gebi('od_'+i);
				if(t){
					t.checked = (e.value == "1") ? false : t.defaultChecked;
					t.disabled= (e.value == "1") ? true : false;
					utElem_capture(t);
				}
			}

			gebi('od_des').value = (e.value == "1") ? "" : gebi('od_des').defaultValue;
			gebi('od_des').disabled= (e.value == "1") ? true : false;

			var t = gebi("elem_Od_surgiAlt");
			if(t){
				t.checked = (e.value == "1") ? false : t.defaultChecked;
				t.disabled= (e.value == "1") ? true : false;
				utElem_capture(t);
			}

			gebi('od_pr').innerHTML= (e.value == "1") ? 'PERRLA' : "&nbsp;" ;
			//opener.document.getElementById("pupil_ref").innerHTML = (e.value == "1") ? "PERRLA" : "" ;
		}
		if(os==false){
			gebi('os_pr').innerHTML= (e.value == "1") ? 'PERRLA' : "&nbsp;" ;
			//opener.document.getElementById("pupil_refOs").innerHTML = (e.value == "1") ? "PERRLA" : "" ;
			for(var i=1;i<=46;i++){
				var t = gebi('os_'+i);
				if(t){
					t.disabled= (e.value == "1") ? true : false;
					t.checked=(e.value == "1") ? false : t.defaultChecked;
					utElem_capture(t);
				}
				//alert(as);
			}
			if(gebi('os_des')){
				gebi('os_des').value = (e.value == "1") ? "" : gebi('od_des').defaultValue;
				gebi('os_des').disabled=(e.value == "1") ? true : false;
			}
			var t = gebi("elem_Os_surgiAlt");
			if(t){
				t.checked = (e.value == "1") ? false : t.defaultChecked;
				t.disabled= (e.value == "1") ? true : false;
				utElem_capture(t);
			}
		}

		checkwnls();

		//Set GreyColor and changeIndicator
		var idDiv = "divPupil";
		if(newET_chkB4GrayExe(idDiv,"ou")){
			newET_setGray_Exe(idDiv,"ou");
		}
		// ---------

	}

	function clickPhrma(obj){
		var oChk = oneEye_isSet(1);
		if(oChk){
			if(oChk.eye==obj.value){
				obj.value="";
				return;
			}else if(obj.value=="OU"){
				obj.value=(oChk.eye=="OD") ? "OS" : "OD";
			}
		}
		var x = $("#elem_pharmadilated").prop("checked");
		if(obj.value != "" && x!=true){
			$("#lblPharmaDilate").click();
		}
	}

//-------
//LA --------
function placs_displayModifier(ex,eye){
	if($("#divModifier"+ex+eye+"").css("display")==""||$("#divModifier"+ex+eye+"").css("display")=="block"){
		$("#divModifier"+ex+eye+"").hide();
		if($("#divModifier"+ex+eye+" :checked").length>0||$("#divModifier"+ex+eye+" input[type='text']").filter(function(){ return !!this.value;}).length>0){
			if(!$("#btnMod_"+ex+eye).hasClass("btnModifiersActive")){$("#btnMod_"+ex+eye).addClass("btnModifiersActive");}
		}else{
			$("#btnMod_"+ex+eye).removeClass("btnModifiersActive");
		}
	}else{
		$(".divLesionModifier").hide();
		var ofst=$("#divModifier"+ex+eye+"").show();
	}
}
function placs_reset(ex,eye){
	//alert("hello"+$("#divModifier"+ex+eye+" input").length);
	$("#divModifier"+ex+eye+" input").each(function(){if(this.type=='checkbox'){this.checked=false;utElem_capture(this);}else if(this.type=='text'&&this.value!=''){this.value='';utElem_capture(this);} });

}
function placs_checkSingle(obj){

	if(obj.type=="checkbox" && obj.checked==true){
		var nm = obj.name;
		var o = document.getElementsByName(nm);
		var ln = o.length;
		if(ln>1){
			for(var i=0;i<ln;i++){
				if(obj.value!=o[i].value){
					o[i].checked=false;
					utElem_capture(o[i]);
				}
			}
		}
	}
}
function placs_showAdvance(){}

// IOP / GONIO ------------------------------------------------

function trgat(obj,t){

	var trgtod=gebi("trgtOd").value;
	var trgtos=gebi("trgtOs").value;
	var oFlg = gebi("redflag");
	var oCtr = gebi("fieldsCount");
	var ctr = (oCtr && (typeof oCtr.value != "undefined") && (oCtr.value != "")) ? ""+oCtr.value : "";
	var arr = ctr.split(",");
	var len = arr.length;
	trgtod = ((typeof trgtod != "undefined") && (trgtod != "")) ? trgtod : 21;
	trgtos = ((typeof trgtos != "undefined") && (trgtos != "")) ? trgtos : 21;

	//---------------------------------------------
	oFlg.style.visibility="hidden";

	var arrOd = new Array("elem_appOd","elem_puffOd","elem_XtOd","elem_ttOd");
	var arrOs = new Array("elem_appOs","elem_puffOs","elem_XtOs","elem_ttOs");

	outer:
	for( var i=0;i<len;i++ ){
		var od, os;
		for( var j=0;j<4;j++ ){
			if(i > 0){
				od = gebi(arrOd[j]+""+i);
				os = gebi(arrOs[j]+""+i);
			}else{
				od = gebi(arrOd[j]);
				os = gebi(arrOs[j]);
			}

			if(od && (od.value != "") && (od.value > od.value)){
				//make red display
				oFlg.style.visibility="visible";
				break outer;
			}
			if(os && (os.value != "") && (os.value > trgtos)){
				//make red display
				oFlg.style.visibility="visible";
				break outer;
			}
		}
	}

	//
	if(obj){
		setPresureTime(obj.id,t);

		//Set defualt method
		if(obj.value!=""){
			if(typeof(t)=="undefined") t="";
			var fld_mtd = $.trim($("#elem_appMethod"+t).val());
			if(fld_mtd=="" && (obj.name.indexOf("elem_appOd")!=-1||obj.name.indexOf("elem_appOs")!=-1)){
				if(typeof(iop_def_method)=="undefined"){iop_def_method="";}
				$("#elem_appMethod"+t).val(""+iop_def_method);
			}
		}

	}
}

function setPresureTime(id,t){

	var str="";
	if(id.indexOf("elem_app") != -1){
		if(t){
			str = "elem_appTime"+t;
		}else{
			str = "elem_appTime"
		}
	}else if(id.indexOf("elem_puff") != -1){
		if(t){
			str = "elem_puffTime"+t;
		}else{
			str = "elem_puffTime";
		}
	}else if(id.indexOf("elem_X") != -1){
		if(t){
			str = "elem_xTime"+t;
		}else{
			str = "elem_xTime";
		}
	}else if(id.indexOf("elem_tt") != -1){
		if(t){
			str = "elem_ttTime"+t;
		}else{
			str = "elem_ttTime";
		}
	}

	var o = gebi(str);
	if(o && ($.trim(o.value) == "")){
		o.onclick();
	}
}

function checkdatas(obj, val){
	var cls="";
	if(obj.name.indexOf("tt")!=-1){
		cls="tt";
	}else if(obj.name.indexOf("tx")!=-1){
		cls="tx";
	}else if(obj.name.indexOf("puff")!=-1){
		cls="tp";
	}else if(obj.name.indexOf("applanation")!=-1){
		cls="ta";
	}
	if(isNaN(val)||val=="")val=0;
	val=parseInt(val)+parseInt(1);
	if(cls=="")return;
	if(val==0){val="1";}

	if(obj.checked){
		$("#multiplePressure"+val+" ."+cls).removeClass("tabOff");
	}else{
		$("#multiplePressure"+val+" ."+cls).addClass("tabOff");
		$("#multiplePressure"+val+" ."+cls+" :input[type=text],"+"#multiplePressure"+val+" ."+cls+" textarea").val("");
	}
}

function getFCInfo(){

	var arC = [];
	var cntr=0;
	var e_cntr=0;

	$(".mulPressure").each(function(){
		var id = $(this).attr("id");
		var num = ""+id.replace(/multiplePressure/g,"");

		var eId=$("#"+id+" input[id*=elem_appOd]").eq(0).attr("id");
		var e_num = ""+eId.replace(/elem_appOd/g,"");
		if(e_num=="")e_num=0;

		arC[arC.length] = e_num;
		if(parseInt(cntr)<parseInt(num))cntr=parseInt(num);
		if(parseInt(e_cntr)<parseInt(e_num))e_cntr=parseInt(e_num);
	});

	return {'divcntr':cntr,'e_cntr':e_cntr, "arC":arC};
}

function multiplePressure(){

	var oFc = getFCInfo();
	var cntr = oFc.divcntr;
	var arC = oFc.arC;
	var e_cntr = oFc.e_cntr;

	//Now CNTR
	//console.log(cur_cntr_dv, cur_cntr);
	var cur_cntr_dv= parseInt(cntr)+parseInt(1);
	var cur_cntr = parseInt(e_cntr)+parseInt(1);
	arC[arC.length]=cur_cntr;

	var str =  "";
	var str = ''+
							'<div id="multiplePressure'+cur_cntr_dv+'" class="mulPressure ">'+
									'<div class="row">'+
										'<div class="col-sm-2 ">'+
											'<div class="form-group form-inline">'+
												'<label for="elem_appMethod'+cur_cntr+'">Method</label> '+
												'<input type="text" name="elem_appMethod'+cur_cntr+'"  id="elem_appMethod'+cur_cntr+'" value="" class="form-control iop_method" placeholder="Method">'+
											'</div>'+
										'</div>'+
										'<div class="col-sm-1">'+
											'<div class="input-group">'+
												'<div class="input-group-addon odcolo" >OD</div>'+
												'<input name="elem_appOd'+cur_cntr+'" type="text" id="elem_appOd'+cur_cntr+'" onBlur="trgat(this,\''+cur_cntr+'\')" value="" class="form-control" placeholder="">'+
											'</div>'+
										'</div>'+
										'<div class="col-sm-1">'+
											'<div class="input-group">'+
												'<div class="input-group-addon oscolo">OS</div>'+
												'<input name="elem_appOs'+cur_cntr+'" type="text" id="elem_appOs'+cur_cntr+'" onBlur="trgat(this,\''+cur_cntr+'\')" value="" class="form-control" placeholder="">'+
											'</div>'+
										'</div>'+
										'<div class="col-sm-1">'+
											'<div class="input-group">'+
												'<div class="input-group-addon"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></div>'+
												'<input name="elem_appTime'+cur_cntr+'" type="text" id="elem_appTime'+cur_cntr+'" value="" onClick="this.value=currenttime();" class="form-control" placeholder="">'+
											'</div>'+
										'</div>'+
										'<div class="col-sm-6 form-horizontal">'+
											'<div class="form-group">'+
												'<label class="control-label col-sm-2" >Description</label>'+
												'<div class="col-sm-10">'+
													'<textarea name="elem_descTa'+cur_cntr+'" id="elem_descTa'+cur_cntr+'" rows="1" cols="31" class="form-control " ></textarea>'+
													'<input type="hidden" name="elem_descTa'+cur_cntr+'Prev" value="">'+
												'</div>'+
											'</div>'+
										'</div>'+
										'<div class="col-sm-1 text-center">'+
											'<figure><span class="glyphicon glyphicon-remove-sign" onclick="delImage2(this);"></span></figure>'+
										'</div>'+
									'</div>'+
							'</div>';

	$("#parentTD").append(str);

	$("#fieldsCount").val(function(){return (arC.length>1) ? ""+arC : "";});
	//Set Change
	$("#divIopElem :input[type=text]").eq(1).triggerHandler("keyup");
	//

	cn_ta_clr_npsych("#divIopElem .iop_method");
	cn_ta_clr_npsych("#divIopElem textarea[name*=elem_descTa]");
	cn_typeahead();
	oneEye_check();
}

function delImage2(o){

	var id = $(o).parents(".mulPressure").attr("id");
	if(typeof(id)=="undefined"){return;}

	var rid = id.replace(/multiplePressure/g, "");
	var ar=[];

	if(rid=="1"){
		$("#"+id+" :input[type=text],"+"#"+id+" textarea,").val("");
		$("#"+id+" :input[type=checkbox]").attr("checked",false);
	}else{
		$("#"+id).remove();
	}

	var ofc = getFCInfo();
	$("#fieldsCount").val(function(){return (ofc.arC.length>1) ? ""+ofc.arC : "";});

	//Set Change
	$("#divIopElem :input[type=text]").eq(1).triggerHandler("keyup");
}

//Add addAnes()
function addAnes(){
	var aneslen = $("#conAnes").attr("data-AnesLen");

	var curId = parseInt(aneslen)+1;
	$("#conAnes").attr("data-AnesLen",curId);

	var str_anes="";
	var td_c=1;
	var tr_lm = tr_lm_gl[0];

	for(var x in arr_db_anas){

		if(td_c==1){ str_anes+= "<tr>"; }

		var tvl=arr_db_anas[x];
		var tnm=mk_var_nm(tvl,"anes");
		var tvl_dis = tvl;
		if(tvl_dis.length>od_nm_ln){ tvl_dis=tvl_dis.substr(0,(od_nm_ln-2))+".."; }

		str_anes+= "<td>";
		str_anes+="<input name=\""+tnm+"[]\" type=\"checkbox\" id=\""+tnm+"_"+curId+"\" value=\""+curId+"\" "+
				"	onClick=\"checkAnsTime(this)\" >"+
				"<label for=\""+tnm+"_"+curId+"\" title=\""+tvl+"\">"+tvl_dis+"</label> ";
		str_anes+= "</td>";
		td_c++;
		if(td_c>tr_lm){ str_anes+= "</tr>"; td_c=1;}
	}

	if(str_anes!=""){   str_anes="<div class=\"dv_opthdrop col-lg-6 anelist\" ><table class=\"table-responsive\">"+str_anes+"</table></div>"; }

	var imgAnes = "<span class=\"btnDelIop glyphicon glyphicon-remove-sign\" onClick=\"delAnes("+curId+")\"></span>";

	var str = "<div id=\"tr_ans_"+curId+"\" class=\"row\" > "+
			" <div class=\"col-lg-1 \"><span class=\"iopanthead\">Anesthetic</span></div> "+
	str_anes+
	"<div class=\"dv_opthdrop_time col-lg-5 anthsopt\" >  "+
	"<ul> <li class=\"form-inline\">"+
	"<input name=\"anes_other[]\" type=\"text\" id=\"anes_other"+curId+"\" value=\"Other\" "+
	"			onfocus=\"this.value=(this.value=='Other')?'':this.value;\""+
	"			onChange=\"checkAnsTime(this)\" class=\"form-control\" placeholder=\"Other\" > "+
	"<input name=\"dt_up[]\" id=\"dt_up"+curId+"\" type=\"text\" "+
	"	value=\"\""+
	"	class=\"form-control date-pick\" placeholder=\"Date\" size=\"10\"> "+
	"<input name=\"time_up[]\" id=\"time_up"+curId+"\" type=\"text\" "+
	"	value=\"\""+
	"	onClick=\"this.value=currenttime();\" class=\"form-control\" placeholder=\"Time\" size=\"6\">"+

	"</li> "+
	"<li class=\"ousml\"><input type=\"radio\" name=\"aneseye"+curId+"\" value=\"OU\" "+
				"id=\"aneseye_ou"+curId+"\"  "+
				" checked ><label for=\"aneseye_ou"+curId+"\"></label></li> "+
	"<li class=\"odsml\"><input type=\"radio\" name=\"aneseye"+curId+"\" value=\"OD\" "+
				"id=\"aneseye_od"+curId+"\"  "+
				" ><label for=\"aneseye_od"+curId+"\"></label></li> "+
	"<li class=\"ossml\"><input type=\"radio\" name=\"aneseye"+curId+"\" value=\"OS\" "+
				"id=\"aneseye_os"+curId+"\"  "+
				" ><label for=\"aneseye_os"+curId+"\"></label></li> "+
	"<li >"+imgAnes+"</li></ul>"+
	"</div>"+
	"</div>";

	$("#conAnes .divcontent").append(str);
	$("#divIopElem :input[type=text]").eq(1).triggerHandler("keyup");
	$( ".date-pick" ).datepicker({dateFormat:top.z_js_dt_frmt});
	oneEye_check();
}

function delAnes(id){
	$("#conAnes #tr_ans_"+id).remove();
	$("#divIopElem :input[type=text]").eq(1).trigger("keyup");
}

function checkAnsTime(s){
	var arid= s.id.split("_");
	var id = arid[1] || "";
	id = id.replace(/other/, "");
	//match(/_\d+/g);
	if(id==""){ return; }
	var o = gebi("time_up"+id), p = gebi("dt_up"+id);
	if(((s.type=="checkbox" && s.checked == true)||(s.type=="text" && s.value != ""))){
		if(($.trim(o.value) == "")){o.onclick();}
		if(($.trim(p.value) == "")){p.value = $.datepicker.formatDate('mm-dd-yy', new Date());}
	}
}

//OOD
function addOOD(){
	var len = $("#conOOD").attr("data-OODLen");
	var curId = parseInt(len)+1;
	$("#conOOD").attr("data-OODLen",curId);

	var str_ood="";
	var td_c=1;
	var tr_lm = tr_lm_gl[2];
	for(var x in arr_db_ood){

		if(td_c==1){ str_ood+= "<tr>"; }

		var tvl=arr_db_ood[x];
		var tnm=mk_var_nm(tvl,"ood");
		var tvl_dis = tvl;
		if(tvl_dis.length>od_nm_ln){ tvl_dis=tvl_dis.substr(0,od_nm_ln-2)+".."; }

		str_ood+= "<td>";
		str_ood+="<input name=\""+tnm+"[]\" type=\"checkbox\" id=\""+tnm+"_"+curId+"\" value=\""+curId+"\" "+
				"	onClick=\"checkDTime(this)\" >"+
				"<label for=\""+tnm+"_"+curId+"\" title=\""+tvl+"\">"+tvl_dis+"</label> ";
		str_ood+= "</td>";
		td_c++;
		if(td_c>tr_lm){ str_ood+= "</tr>"; td_c=1;}
	}

	if(str_ood!=""){   str_ood="<div class=\"dv_opthdrop col-lg-5 anelist\"><table class=\"table-responsive\">"+str_ood+"</table></div>"; }

	var imgOOD = "<span class=\"btnDelIop glyphicon glyphicon-remove-sign\" onClick=\"delOOD("+curId+",'OOD')\" ></span>";

	var str = ""+
		"<div id=\"tr_OOD_"+curId+"\" class=\"row\" >"+
		"<div class=\"col-lg-2 \"><span class=\"iopanthead\">Other Ophthalmic Drops</span></div>"+
		str_ood+
		"<div class=\"dv_opthdrop_time col-lg-5 anthsopt\">  "+
		"<ul> <li class=\"form-inline\">"+
		"<input type=\"text\" name=\"other_desc_ood[]\" id=\"other_desc_ood"+curId+"\" value=\"\" onchange=\"chkDOther(this)\" class=\"form-control\" placeholder=\"Other\"  > "+
		"<input type=\"text\" name=\"curdates_ood[]\" id=\"curdates_ood"+curId+"\" "+
		"		value=\"\" class=\"form-control date-pick\" placeholder=\"Date\" size=\"10\"> "+
		"<input type=\"text\" name=\"curtimes_ood[]\" id=\"curtimes_ood"+curId+"\" "+
		"		value=\"\" onClick=\"this.value=currenttime();\" class=\"form-control\" placeholder=\"Time\" size=\"6\"> "+
		"</li> "+
		"<li class=\"ousml\"><input type=\"radio\" name=\"oodeye"+curId+"\" value=\"OU\" "+
					"id=\"oodeye_ou"+curId+"\"  "+
					" checked ><label for=\"oodeye_ou"+curId+"\" ></label></li> "+
		"<li class=\"odsml\"><input type=\"radio\" name=\"oodeye"+curId+"\" value=\"OD\" "+
					"id=\"oodeye_od"+curId+"\"  "+
					" ><label for=\"oodeye_od"+curId+"\" ></label></li> "+
		"<li class=\"ossml\"><input type=\"radio\" name=\"oodeye"+curId+"\" value=\"OS\" "+
					"id=\"oodeye_os"+curId+"\"  "+
					" ><label for=\"oodeye_os"+curId+"\" ></label></li> "+
		"<li>"+imgOOD+"</li></ul> "+
		"</div>"+
		"</div>";
	$("#conOOD").append(str);
	$("#conOOD :input[type=text]").eq(1).triggerHandler("keyup");
	$( ".date-pick" ).datepicker({dateFormat:top.z_js_dt_frmt});
	oneEye_check();
}


function delOOD(id){
	$("#conOOD #tr_OOD_"+id).remove();
	$("#divOOD :input[type=text]").eq(1).triggerHandler("keyup");
}

function checkDTime(s){
	var arid= s.id.split("_");
	var id = arid[1] || "";
	//--

	if(id=="" && s.id.indexOf("Other")!=-1){id = s.id.replace(/OtherOOD|Other/,"");}
	//--

	if(id==""){ return; }
	timeOod="";
	var flg = $(s).parents("#divOOD").length;

	if(flg){
		timeOod="_ood";
	}else{
		//check site
		//if($(":checked[name=elem_sideIop]").length==0){ $("input[name=elem_sideIop]").get(0).checked=true ; }
	}
	var o = gebi("curtimes"+timeOod+id);
	var p = gebi("curdates"+timeOod+id);
	if((s.checked == true)){
		if($.trim(o.value) == ""){o.onclick();}
		if($.trim(p.value) == ""){p.value=$.datepicker.formatDate('mm-dd-yy', new Date());}
	}
}
function chkDOther(s){
	var x=s.id.replace(/other_desc/,"curtimes");
	if($("#"+x).val() == ""){$("#"+x).triggerHandler("click");}
	var y = x.replace(/curtimes/,"curdates");
	if($("#"+y).val() == ""){$("#"+y).val($.datepicker.formatDate('mm-dd-yy', new Date()));}
}

//Dilation
function addDilation(){
	var len = $("#conDilation").attr("data-DilateLen");
	var curId = parseInt(len)+1;
	$("#conDilation").attr("data-DilateLen",curId);

	var str_dilate="";
	var td_c=1;
	var tr_lm = tr_lm_gl[1];

	for(var x in arr_db_dilate){

		if(td_c==1){ str_dilate+= "<tr>"; }

		var tvl=arr_db_dilate[x];
		var tvl_dis = tvl;
		if(tvl_dis.length>od_nm_ln){ tvl_dis=tvl_dis.substr(0,od_nm_ln-2)+".."; }

		var tnm=mk_var_nm(tvl,"dltn");
		str_dilate+= "<td>";
		str_dilate+="<input name=\""+tnm+"[]\" type=\"checkbox\" id=\""+tnm+"_"+curId+"\" value=\""+curId+"\" "+
				"	onClick=\"checkDTime(this)\" >"+
				"<label for=\""+tnm+"_"+curId+"\" title=\""+tvl+"\">"+tvl_dis+"</label> ";
		str_dilate+= "</td>";
		td_c++;
		if(td_c>tr_lm){ str_dilate+= "</tr>"; td_c=1;}
	}

	if(str_dilate!=""){   str_dilate="<div class=\"dv_opthdrop col-lg-8 anelist\"><table class=\"table-responsive\">"+str_dilate+"</table></div>"; }

	var imgDilation = "<span class=\"btnDelIop glyphicon glyphicon-remove-sign\" onClick=\"delDilation("+curId+")\" ></span>";

	var str = ""+
		"<div id=\"tr_dilate_"+curId+"\" class=\"row\" >"+str_dilate+
		"<div class=\"dv_opthdrop_time col-lg-4\">  "+
		"<div class=\"othbox\"> <ul>"+

		"<li class=\"form-inline\"><input type=\"text\" name=\"other_desc[]\" id=\"other_desc"+curId+"\" value=\"\" onchange=\"chkDOther(this)\" class=\"form-control\" placeholder=\"Other\"> "+
		"<input type=\"text\" name=\"curdates[]\" id=\"curdates"+curId+"\" "+
		"		value=\"\" class=\"form-control date-pick\" placeholder=\"Date\" size=\"10\"> "+
		"<input type=\"text\" name=\"curtimes[]\" id=\"curtimes"+curId+"\" "+
		"		value=\"\" onClick=\"this.value=currenttime();\" class=\"form-control\" placeholder=\"Time\" size=\"6\"></li> "+
		"<li class=\"ousml\"><input type=\"radio\" name=\"dileye"+curId+"\" value=\"OU\"  id=\"dileye_ou"+curId+"\" checked /><label for=\"dileye_ou"+curId+"\"></label></li>"+
		"<li class=\"odsml\"><input type=\"radio\" name=\"dileye"+curId+"\" value=\"OD\"  id=\"dileye_od"+curId+"\" /><label for=\"dileye_od"+curId+"\"></label></li>"+
		"<li class=\"ossml\"><input type=\"radio\" name=\"dileye"+curId+"\" value=\"OS\" id=\"dileye_os"+curId+"\" /><label for=\"dileye_os"+curId+"\"></label></li>"+
		"<li >"+imgDilation+"</li>"+
		"</ul></div>"+

		"</div>"+
		"</div>";
	$("#conDilation").append(str);
	$("#divDilation :input[type=text]").eq(1).triggerHandler("keyup");
	$( ".date-pick" ).datepicker({dateFormat:top.z_js_dt_frmt});
	oneEye_check();
}

function delDilation(id){
	$("#conDilation #tr_dilate_"+id).remove();
	$("#divDilation :input[type=text]").eq(1).triggerHandler("keyup");
}

function setNoDilation(flgload){
	var obj = $("#divDilation input[name=elem_noDilation]");
	if(obj.prop("checked")){
		/*
		$("#divDilation input").not(obj).prop("disabled",true).each(function(){
			if($(this).prop("type")=="checkbox" || $(this).prop("type")=="radio"){$(this).prop("checked",false);}
			if($(this).prop("type")=="text"){$(this).val("");}
			$("#divDilation textarea").val("");
			utElem_capture(this);
		});
		*/
		$("#divDilation input[name=elem_revEyes]:eq(1)").prop("checked",true);
	}else{
		if(typeof(flgload)=="undefined"){ //donot act when called onload
			$("#divDilation input").prop("disabled",false);
		}
	}
}

function change_col(obj, textArea, theObj){
	if(obj.checked == true){
		$("#"+theObj).addClass('text-danger');
		$("#"+textArea).removeClass('hidden');
	}else{
		$("#"+theObj).removeClass('text-danger');
		$("#"+textArea).addClass('hidden');
	}
}

// fundus --
function setDrawingCD(obj){
	if(obj.name == "elem_cdValOd"){
		document.getElementById("hidCDRationOD").value = obj.value
	}
	else if(obj.name == "elem_cdValOs"){
		document.getElementById("hidCDRationOS").value = obj.value
	}
	//if(typeof(setCD) !="undefined"){setCD();}
}

//Emergency Notes--
function show_emergency_notes(flg){
	if(typeof(flg) == "undefined"){
		var flg = ""+$("#div_emergency_status").css("display") == "block" ? false : true ;
	}
	$("#div_emergency_status").modal({backdrop: false, show: flg});

	//
	if($("#div_emergency_status :checked[type=checkbox]").length>0){
		$("#btn_emrg").removeClass("btn-default").addClass("btn-danger");
	}else{
		$("#btn_emrg").removeClass("btn-danger").addClass("btn-default");
	}
}

function chkEmerAbsPrt(obj){

	if(obj.type == "checkbox"){
		if(obj.checked){
			if(obj.name != "elem_emerstt_comm_p2p_nodone" && $("#div_emergency_status :checked[name=elem_emerstt_comm_p2p_nodone]").length<=0){
				$("#div_emergency_status input[name=elem_emerstt_comm_p2p_nodone]").prop("checked", true).triggerHandler("click");
			}
		}else{
			if(obj.name == "elem_emerstt_comm_p2p_nodone"){
				$("#div_emergency_status :checked[name!=elem_emerstt_comm_p2p_nodone]").each(function(){ $(this).prop("checked", false).triggerHandler("click");  });
				$("#div_emergency_status :input[name=elem_emerstt_lvlSeverityRetFind]").each(function(){  $(this).val("").triggerHandler("change");  });
			}
		}
	}
	else if(obj.type == "select-one"){
		if(obj.value!="" && $("#div_emergency_status :checked[name=elem_emerstt_comm_p2p_nodone]").length<=0 ){
			$("#div_emergency_status input[name=elem_emerstt_comm_p2p_nodone]").prop("checked", true).triggerHandler("click");
		}else{
			/*
			if( $("#div_emergency_status :checked[name=elem_emerstt_comm_p2p_nodone]").length>0 && $("#div_emergency_status :checked[type=checkbox][name!=elem_emerstt_comm_p2p_nodone]").length<=0 ){
				$("#div_emergency_status input[name=elem_emerstt_comm_p2p_nodone]").prop("checked", false).triggerHandler("click");
			}*/
		}
	}

}
//Emergency Notes--

// Exam Ext ---
function set_exm_ext_arrow(){
	$(".fnd_exm_ext").each(function(){
			var cs = $(this).attr("class");
			var grp_cs = ""+cs.replace(/fnd_exm_ext/g, "").trim();
			if(typeof(grp_cs)!="undefined" && grp_cs!=""){ //check for empty grp_cs
			var wo_cs = grp_cs.replace(/grp_/g,"");
			var op = $(this).prev();
			var flgopn=0;
			var ylw_css="exmhlgcol";
			if(!op.hasClass("exmhlgcol")){

				var c=0;
				do{
					flgstop=false;
					var td = op.find("td:nth-child(1)");
					var txt_nm = $.trim(""+td.html());
					if(typeof(txt_nm)=="undefined" || txt_nm==""){
						op = op.prev();
						flgstop=true;
					}
					c++;
					if(c>=10){ flgstop=false; }//force stop
				}while(flgstop);


				//- check opn-
				$("tr."+grp_cs+" :input").each(function(){ if($(this).attr("type")=="checkbox"){ if($(this).prop("checked")==true){ flgopn=1;return false;}  }else{ if($(this).val()!=""){ flgopn=1;return false;}  } });
				//--
				op.addClass(" exmhlgcol grp_handle "+cs);
				var td = op.find("td:nth-child(1)");
				var txt_nm = ""+td.html();
				var gm = (flgopn>0) ? "glyphicon-menu-up" : "glyphicon-menu-down";
				var h="<label >"+txt_nm+" <span class=\"glyphicon "+gm+"\"></span></label> ";
				td.html(h).addClass("grpbtn_ee").bind("click", function(){ openSubGrp(wo_cs); });
				op.find(".bilat").next().html(h).addClass("grpbtn_ee").bind("click", function(){ openSubGrp(wo_cs); });
			}else{
				/* if prev row is open, then make it open*/
				/* if current row is part of hard coded group, then make it open*/
				if(op.hasClass("sbGrpOpen")||!op.hasClass(""+grp_cs)){ ylw_css+=" sbGrpOpen "; }
			}
			//hide
			$(this).addClass(ylw_css);
			if(flgopn>0){$("tr."+grp_cs).addClass("sbGrpOpen"); }
			}//
		});
}


//buttons ---
function setPrevValues(){
	window.location.replace(window.location.href+"&prevVal=1");
}

function b4_save_processing(){
	freezeElemAll(0);
	if(examName != "Pupil"&&examName != "Refractive Surgery"){	saveCanvas(); }
}

function after_save_processing(d, dncls){
	try{
	if(window.opener && typeof window.opener.AfterSave != 'undefined'){
		window.opener.AfterSave(d);
	}
	}catch(e){}
	if(typeof(dncls)!="undefined" && dncls!=""){ if(dncls=="IOPGRPH"){ IOP_showGraphsAm(1); } }else{window.self.close(); }
}

//--
function save_exam(dncls){
	//check
	if(examName == "Gonio"){
		var flgEr=0;
		$("#divIopElem .iophght .head").each(function(){
				if($(this).find(":checked").length>0 && $(this).find(":checked").hasClass("greyAll_v2")==false && $.trim($(this).find(".iop_method").val())==""){
					if($(this).parent().find(".pd10 input[type=text]").filter(function() { return $.trim(this.value) != ""; }).length){
					flgEr = 1;
					$(this).find(".iop_method").focus();
					return false;
					}
				}
			});
		if(flgEr == 1){
			top.fAlert("Please enter method in IOP!");
			return;
		}
	}

	b4_save_processing();
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1");
	var strsave=$("form").serialize();
	strsave+="&savedby=ajax";
	//console.log(strsave);
	//return;
	$.post(zPath+"/chart_notes/saveCharts.php", strsave, function(data) {
		//console.log(data);
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("0");
		//console.log(data, typeof(data));
		after_save_processing(data, dncls);
	},'json');
}
function cancel_exam(){window.self.close();}
function reset_exam(){funReset_exam();}
function previous_exam(){setPrevValues();}
function purg_exam(){
	//purgChartExm();
	var m = confirm("Do you want to purge this exam? You can not undo it.");
	if(m){

		if(examName == "Gonio"){
			var wcid = getWcId();
			if(wcid.nm == "IOP"){
				$(":hidden[name=elem_purged_IOP]").val("1");
			}else{
				$(":hidden[name=elem_purged]").val("1");
			}
		}else{
			$(":hidden[name=elem_purged]").val("1");
		}

		//$("form").get(0).submit();
		save_exam();
	}
}
//--
//--
function docu_event_handler(e){
	var chk = e.target.nodeName;
	var eType = e.type;
	//
	if($(e.target).parents(".div_idoc_drw").length>0){return;}

	if(chk == "INPUT" || chk == "TEXTAREA" || ((eType == "change" || eType == "keyup" || eType == "mousedown") && chk == "SELECT")){

		var enm = e.target.name;
		var eid = e.target.id;
		//
		if(e.target.type=="button"||e.target.type=="submit")return;


		//Check NoChange
		checkNC1(e.target);

		//User Type Color: Color coding ------------
		// Entire Summary sheet elements:
		utElem_capture(e);
		// Entire Summary sheet elements
		//User Type Color: Color coding ------------

	}else if(chk == "LABEL"){
		if((finalize_flag != "1" || isReviewable == "1") && elem_per_vo!="1"){
			var disele = $(e.target).attr("for");
			var chkkx = $("#"+disele).prop("disabled");
			var chkky = $("#"+disele).attr("type");
			if(typeof(chkkx)!="undefined" && chkkx == true && typeof(chkky)!="undefined" && chkky == "checkbox" ){
				//Check NoChange
				checkNC1($("#"+disele).get(0));
				utElem_capture($("#"+disele).get(0));
			}
		}
	}
}

///-----------------------------------
//main

$(document).ready(function () {
		if(window.opener && typeof(window.opener.top.keyCatcher)!="undefined"){document.onkeydown = window.opener.top.keyCatcher;}
		if(examName == "Procedures"){ //Procedures
			//do nothing
		}
		else if(examName == "DrawPane"||examName == "DrawCL"){ //DrawPane
			var hid_drw_id="hidDrawPaneDrawingId", dv_con="divMaster", exm_id="0", exm_nm="DRAWPANE_DSU";
			if(examName == "DrawCL"){ hid_drw_id="hidDrawCLDrawingId"; exm_nm="CL_DRAW_DSU"; }
			AJAXLoadDarwingData_exe(hid_drw_id, dv_con, exm_id, exm_nm);
			$("#img_load, #dvloading").hide().html("Processing..");
		}else{//Exams
		if(examName == "Refractive Surgery"){
			//calcRSB
			if(myflag=="1"&&finalize_flag!="1"){ //Call when new exam made
				if($("input[name=elem_sphericalEqOd][value!='']").length>0){
					calcRSB('d');
				}
				if($("input[name=elem_sphericalEqOs][value!='']").length>0){
					calcRSB('s');
				}
			}

			//Grey white
			setRefSxMode();

			$("#imgAblat,#imgNomo").draggable();

			//
			$( "#btnNomo" ).click(function() {  $( "#imgNomo" ).toggle(); });
			$( "#btnAblat" ).click(function() {  $( "#imgAblat" ).toggle(); });
		}

		$("body").bind("click keyup change mousedown", function(e){ docu_event_handler(e) });

		//User Type Color: Color coding ------------
		utElem_setBgColor();
		//User Type Color: Color coding ------------

		//
		if(examName == "EOM"){
			wv_activate_menu_click();
			ei_attachIndicator_eom();
		}else if(examName == "Gonio"){
			trgat();
			//set changes indicator
			ev_attachIndicator_iop();
			setNoDilation(1);
			$( "#elem_cor_date, .date-pick" ).datepicker({dateFormat:top.z_js_dt_frmt});
			cn_ta_clr_npsych("#divIopElem .iop_method");
			cn_ta_clr_npsych("#divIopElem textarea[name*=elem_descTa]");

		}else{
			//Bind event and set color on elements
			newET_setGray_v3();
		}

		//
		if(finalize_flag=="1"){	if(isReviewable==1){setReviewableFunction();}else{setfinalizedFunction();}	}

		if(examName == "Pupil"){  chk_Perrla(-1); }

		//nc
		if(examName == "EOM"){
			if($("#elem_ncEom").val()=="1"){$("#elem_noChange")[0].onclick();}
			//
			setExamFlag();
			//
			setTabFlags();

		}else{
			//NC
			setNC(1);

			// Set Positive
			checkwnls();
		}


		if(typeof(def_pg) !="undefined" && def_pg!=""){ if((examName != "LA" && examName != "SLE" && examName != "Fundus" && examName != "Gonio") || def_pg=="draw" || def_pg=="Drawing" || def_pg=="drawrvon" || def_pg=="drawrvma"){	openExmTab(def_pg); }	}

		if(examName == "SLE"){
			$("#elem_mfocalOd_pciol, #elem_mfocalOs_pciol").bind("click", function(){if(this.checked){$("#"+this.id+"_opts").show();}else{$("#"+this.id+"_opts").val("").hide();}	});
			cn_ta_clr_npsych();
		}

		cn_typeahead();

		//LA
		if(examName == "LA"){
		//if(jsAdavceOpen!=""){
		//	$("'"+jsAdavceOpen+"'").each(function(){ $(this).triggerHandler('click'); });
		//}

		//date
		$( ".dacry input[type=text], .lacsci input[type=text], .ctmri input[type=text], .schirmer  input[type=text]" ).datepicker({dateFormat:"mm-dd-yy"});

		//
		$( ".divLesionModifier" ).draggable({ handle: ".examhd" });
		}

		//Exam Ext
		if(examName == "LA" || examName == "SLE" || examName == "Fundus"){ 	set_exm_ext_arrow();	}
		//--



		//enable buttons
		$("input[type=submit][id=save],input[type=button][id=save],input[type=button][id=btnReset],input[type=button][id=btnPrev],input[type=button][id=btnPurg],input[type=button][id=btnRecal]").show();

		$('[data-toggle="tooltip"]').tooltip();
		$("#img_load, #dvloading").hide();
		}//End Exams

	});
