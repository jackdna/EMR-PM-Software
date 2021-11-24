// work view page
/**
showPrevCharts
saveMainPage
chckMainSaveReqFlds
isCurMRDone
isVisElemDone
isSuperBillMade--superbill.js
mandotry_chk--
saveMainPageExe
funClosePopUpExe
disable13Elems
setElem2Default
emptyArcRedStrikeB4Save
setSavedBtn
postSaveMainPageFunctions
utElem_setBgColor
FinalizeMainPage
funClose
chartNoteUnfinalize
chartNoteEdit
getconfirmationPurgeRequest
getPhyViewWV
showPrevCharts
setReviewableFunction
setfinalizedFunction
sc_userPress
loadAsPlanTBar
upAsPlanProbid
printMr
getPhySign_db
chckMainSaveReqFlds
isVisitCodeExits
chrt_showChartInfo
get_reff_address_v2

**/

var isiPad=(navigator.userAgent.match(/iPad/i) != null) ? 1 : 0;
var isSafari=(navigator.userAgent.match(/Safari/i) != null&&navigator.userAgent.match(/Chrome/i) == null) ? 1 : 0;

var oPUF=[];
var oPP=[];
var oPO=[];
var oPF=[];
var arrSchPop=[];

var icd10_unique_obj_id = '';

function setSavedBtn(flg,txt){
	top.$("#save,#btnFinalize,#cancel").prop("disabled",function(){return (flg==1) ? true:false;});
	var tmp = (typeof(txt)!="undefined"&&txt!="") ? txt:"Saving chart notes. Please Wait! ";
	//top.$("#divProcessing2").html(tmp).css("display",function(){return (flg==1) ? "block":"none";});
	if(typeof(setProcessImg) != 'undefined' )setProcessImg(flg,"",tmp);
}

//
function showOtherForms(u,n,w,h,scrl)
{
	scrl = (typeof scrl != "undefined") ? scrl : 1;
	if(u != ""){
		var features = 'location=0,status=1,resizable=1,left=0,top=0,scrollbars='+scrl+',width='+w+',height='+h;
		top.popup_win(u,features);
	}
}

function setThisChangeStatus(obj){isFormChanged=1;}

// RVS --
//Load RVS --
function loadRVS(flag,wh){
	if($("#div_rvs").length==0){
		//var url="common/requestHandler.php";
		var url="onload_wv.php";
		var p="elem_action=loadRVS";
			p+="&finalize_flag="+$("#elem_isFormFinalized").val();
			p+="&form_id="+$("#elem_masterId").val();
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","rvsBtn");

		$.post(url,p,function(data){

			if(data!=""){
				if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","rvsBtn");

				//$("#divWorkView").append(""+data);
				$("#frmMain").append(""+data);

				//if(flag!=1){
					$("#div_rvs").show();

				//}
				$("#div_rvs,#detailDesc1,#detailDesc2").draggable({handle:".handleDrag"});
				if(typeof(wh)!="undefined" && wh!=""){ $("#"+wh).triggerHandler("click");  }

			}
		});
	}else{
		if($("#div_rvs").css("display")=="none" || (typeof(wh)!="undefined" && wh!="") ){
			$("#div_rvs").show();
			if(typeof(wh)!="undefined" && wh!=""){ $("#"+wh).triggerHandler("click");  }
		}else{
			$("#div_rvs").hide();

		}
	}
	stopClickBubble();
}
//Load RVS --

//User
//set userName
function setUserName(str){
	var obj = { "id":""+authUserID,"name":""+authUserNM };
	if(str == "elem_ccHx"){
		var otd = gebi("td_cosigner");
		var ocid = gebi("elem_cosigner_id");
		var ocname = gebi("elem_cosigner_name");
	}
	//--
	if(str.indexOf("MR")!=-1){
		var ocid = "elem_providerId";
		var ocname = "elem_providerName";
		var mri = str.replace("MR","");
		if(mri>1){ocid += "Other"; ocname += "Other"; }
		if(mri>2){ocid += "_"+mri; ocname += "_"+mri; }
		ocid = gebi(ocid);
		ocname = gebi(ocname);
	}
	//--

	/*
	if(str == "elem_physicianId"){
		var osign = gebi("elem_signCoords");
		var vsign = (osign && (osign.value != "") && (osign.value != "0-0-0:;")) ? true : false;

		if(vsign != false){
			alert("Please clear signature first to change physician.");
		}
		if((vsign == false) && confirm("Do you want to change physician ?")){
			var ocid = gebi("elem_physicianId");
			var ocname = gebi("elem_physicianIdName");
		}
	}
	//START
	if(str == "elem_cosignerId"){
		var osign = gebi("elem_signCoordsCosigner");
		var vsign = (osign && (osign.value != "") && (osign.value != "0-0-0:;")) ? true : false;

		if(vsign != false){
			alert("Please clear signature first to change cosigner.");
		}
		if((vsign == false) && confirm("Do you want to change cosigner ?")){
			var ocid = gebi("elem_cosignerId");
			var ocname = gebi("elem_cosignerIdName");
		}
	}
	//END
	*/

	if(ocid && ocname){
		//CC Hx.
		if(str == "elem_ccHx"){
			var oPrId = gebi("elem_pro_id");
			if(oPrId && ((oPrId.value != "") && (oPrId.value == obj.id))){
				return false;
			}
		}

		ocid.value = obj.id;
		if(typeof ocid.onchange == "function"){
			ocid.onchange();
		}
		if(typeof ocid.onkeyup == "function"){
			ocid.onkeyup();
		}
		if(typeof ocid.onkeydown == "function"){
			ocid.onkeydown();
		}
		ocname.value = obj.name;

		//CC hx
		if( str != "elem_ccHx" ){
			changeVisionStatus(ocname,1,1);
			changeVisionStatus(ocid,1,1);

			if(str.indexOf("MR")!=-1){
				renew_title(ocname,ocname.value);
			}
		}
		if(otd){
			otd.style.visibility = "visible";
		}

		/*
		//Physician id
		if(str == "elem_physicianId"){
			var oPhysign = gebi("elem_is_phy_sign");
			var oUsrsign = gebi("elem_is_user_sign");
			var oLblSig  = gebi("lbl_phy_sig");

			if(oPhysign && oUsrsign){
				oPhysign.value = (typeof oUsrsign.value != "undefined") ? oUsrsign.value : "0";
			}
			// set link color
			if(oPhysign.value == "1"){
				oLblSig.className = "clickable";
				//oLblSig.attachEvent("onclick", getPhySign_db);
				$(oLblSig).bind("click", getPhySign_db);
			}else{
				oLblSig.className = "";
				//oLblSig.detachEvent("onclick", getPhySign_db);
				$(oLblSig).unbind("click", getPhySign_db);
			}
		}
		//START
		if(str == "elem_cosignerId"){
			var oPhysign = gebi("elem_is_cosigner_sign");
			var oUsrsign = gebi("elem_is_user_signCosigner");
			var oLblSig  = gebi("lbl_cosigner_sig");

			if(oPhysign && oUsrsign){
				oPhysign.value = (typeof oUsrsign.value != "undefined") ? oUsrsign.value : "0";
			}
			// set link color
			if(oPhysign.value == "1"){
				//oLblSig.className = "clickable";
				//oLblSig.attachEvent("onclick", getPhySign_db);
				oLblSig.innerHTML='<span class="clickable"  onclick="getPhySign_db(0,1);">Cosigner</span>';
			}else{
				oLblSig.innerHTML='Cosigner';
				oLblSig.className = "";
				//oLblSig.detachEvent("onclick", getPhySign_db);
			}
		}
		//END
		*/

	}
}

//Tech Mandatory--
function getTechMandatory(obj){
	var str = $.trim(obj.value);
	if(str == ""||user_type!='3'){
		$("#tm_ocualr,#tm_general_health,#tm_medication,#tm_surgeries,#tm_allergies,#tm_immunizations,"+
						"#tm_social,#tm_cvf,#tm_visit,#tm_cc_history,#tm_vision,#tm_distance,#tm_near,"+
						"#tm_ar,#tm_pc,#tm_mr,#tm_cvf_c,"+
						"#tm_amsler_grid,#tm_icp_color_plates,#tm_steroopsis,#tm_diplopia,#tm_retinoscopy,"+
						"#tm_exophthalmometer,#tm_pupil,#tm_eom,#tm_external,#tm_iop").each(function(){$(this).val("");});
		return;
	}
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","techman");
	$.get('requestHandler.php?elem_formAction=getTechMandtory&elem_visitCode='+str, function(d){
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","techman");
			if(d){for(var x in d){ if(x!="tech_id" && x!="ptVisit"){ $("#tm_"+x).val(d[x]); }  }}
		},'json');
}
function tm_checkExmdone(exm){
	var os=gebi("elem_se_"+exm);
	var s = (os) ? gebi("elem_se_"+exm).value : "";
	if(s != "" && s.indexOf("=1")!=-1){
		if(gebi("elem_pos"+exm).value==1 || gebi("elem_wnl"+exm+"").value==1){
			return true;
		}else if(exm != "Eom" && (gebi("elem_wnl"+exm+"Od").value==1 || gebi("elem_wnl"+exm+"Os").value==1)){
			return true;
		}
	}
	return false;
}
function mandatory_ovr(){}
function mandotry_chk(val){
	if(val==1){return;}
	if(user_type=='3'){
		var ocualr= $("#tm_ocualr").val();
		var general_health= $("#tm_general_health").val();
		var medication= $("#tm_medication").val();
		var surgeries= $("#tm_surgeries").val();
		var allergies= $("#tm_allergies").val();
		var immunizations= $("#tm_immunizations").val();

		var social= $("#tm_social").val();
		var cvf= $("#tm_cvf").val();
		var visit= $("#tm_visit").val();
		var cc_history= $("#tm_cc_history").val();
		var vision= $("#tm_vision").val();
		var distance= $("#tm_distance").val();
		var near= $("#tm_near").val();
		var ar= $("#tm_ar").val();
		var pc= $("#tm_pc").val();
		var mr= $("#tm_mr").val();
		var cvf_c= $("#tm_cvf_c").val();
		var amsler_grid= $("#tm_amsler_grid").val();

		var icp_color_plates= $("#tm_icp_color_plates").val();
		var steroopsis= $("#tm_steroopsis").val();
		var diplopia= $("#tm_diplopia").val();
		var retinoscopy= $("#tm_retinoscopy").val();
		var exophthalmometer= $("#tm_exophthalmometer").val();
		var pupil= $("#tm_pupil").val();
		var eom= $("#tm_eom").val();
		var external= $("#tm_external").val();
		var iop= $("#tm_iop").val();
		var show_name="Please fill in the following:- <br>\n";
		var flag_chk=0;

		var tmp,tmp1;
		if(visit=='yes'){
			tmp = top.fmain.gebi('elem_ptVisit_chk');
			tmp1=1;
			if(tmp && tmp.value==''){
				tmp1=0;
				show_name+='  -Visit<br>\n';
				flag_chk+=1;
			}
		}
		if(cc_history=='yes'){
			tmp = gebi("elem_chk");
			tmp = ccHxDefStr(tmp.value, 1);
			tmp1=1;
			if(tmp==''){
				tmp1=0;
				show_name+='  -CC & History<br>\n';
				flag_chk+=1;
			}
		}

		if(vision=='yes'){
			var show_name1='';
			if(distance=='yes'){
				tmp = isVisElemDone(["elem_visDisOdSel1","elem_visDisOsSel1"]);
				tmp1=1;
				if(tmp==false){
					tmp1=0;
					show_name1+='    -Distance<br>\n';
					flag_chk+=1;
				}
			}

			if(near=='yes'){
				tmp = isVisElemDone(["elem_visNearOdSel1","elem_visNearOsSel1"]);
				tmp1=1;
				if(tmp==false){
					tmp1=0;
					show_name1+='    -Near<br>\n';
					flag_chk+=1;
				}
			}

			if(ar=='yes'){
				tmp = isVisElemDone(["elem_visArOdS","elem_visArOsS"]);
				tmp1=1;
				if(tmp==false){
					tmp1=0;
					show_name1+='    -AR<br>\n';
					flag_chk+=1;
				}
			}

			if(pc=='yes'){
				tmp = isVisElemDone(["elem_visPcOdSel1","elem_visPcOsSel1"]);
				tmp1=1;
				if(tmp==false){
					tmp1=0;
					show_name1+='    -PC<br>\n';
				}
			}
			if(mr=='yes'){
				//elem_providerName
				tmp = isVisElemDone(["elem_visMrOdS","elem_visMrOsS"]);
				tmp1=1;
				if(tmp==false){
					tmp1=0;
					show_name1+='    -MR<br>\n';
					flag_chk+=1;
				}
			}
			if(show_name1){
				show_name+='  -Vision<br>\n';
				show_name+=show_name1;
			}

		}

		if(icp_color_plates=='yes'){
				tmp = isVisElemDone(["elem_color_sign_od","elem_color_od_1", "elem_color_od_2","elem_color_sign_os","elem_color_os_1", "elem_color_os_2"]);
				tmp1=1;
				if(tmp==false){
					vis_show_sec('r4',1);
					tmp1=0;
					show_name+='  -ICP Color Plate<br>\n';
					flag_chk+=1;
				}
			}
			if(steroopsis=='yes'){
				tmp = isVisElemDone(["elem_stereo_SecondsArc"]);
				tmp1=1;
				if(tmp==false){
					vis_show_sec('r4',1);
					tmp1=0;
					show_name+='  -Stereopsis<br>\n';
					flag_chk+=1;
				}
			}
			if(retinoscopy=='yes'){
				tmp = isVisElemDone(["elem_visExoOdS","elem_visExoOsS"]);
				tmp1=1;
				if(tmp==false){
					vis_show_sec('r4',1);
					tmp1=0;
					show_name+='  -Retinoscopy<br>\n';
					flag_chk+=1;
				}
			}
			if(exophthalmometer=='yes'){
				tmp = isVisElemDone(["elem_visRetPD","elem_visRetOd","elem_visRetOs"]);
				tmp1=1;
				if(tmp==false){
					vis_show_sec('r4',1);
					tmp1=0;
					show_name+='  -Exophthalmometer<br>\n';
					flag_chk+=1;
				}
			}
			if(pupil=='yes'){
				tmp = tm_checkExmdone("Pupil");
				tmp1=1;
				if(tmp==false){
					tmp1=0;
					show_name+='  -Pupil<br>\n';
					flag_chk+=1;
				}
			}
			if(eom=='yes'){
				tmp = tm_checkExmdone("Eom");
				tmp1=1;
				if(tmp==false){
					tmp1=0;
					show_name+='  -EOM<br>\n';
					flag_chk+=1;
				}
			}
			if(external=='yes'){
				tmp = tm_checkExmdone("Ee");
				tmp1=1;
				if(tmp==false){
					tmp1=0;
					show_name+='  -External<br>\n';
					flag_chk+=1;
				}
			}
			if(iop=='yes'){
				tmp1=1;
				var sid = gebi('elem_sumOdIOP');
				var sis = gebi('elem_sumOsIOP');
				var sad = gebi('elem_sumOdAnesthetic');
				var sas = gebi('elem_sumOsAnesthetic');
				var sdd = gebi('elem_sumOdDilation');
				var sds = gebi('elem_sumOsDilation');

				if(sid && sis && sad && sas && sdd && sds &&  ($.trim(sid.innerHTML) == '') && ($.trim(sis.innerHTML)=='') &&
					($.trim(sad.innerHTML)=='') && ($.trim(sas.innerHTML)=='') &&
					($.trim(sdd.innerHTML)=='') && ($.trim(sds.innerHTML)=='')  ){ //
					tmp1=0;
					show_name+='  -IOP<br>';
					flag_chk+=1;
				}
			}

			if(amsler_grid=="yes"){
				if($("#amsgrid .positive").length <= 0 && $("#amsgrid .wnl_lbl").length <= 0){
					show_name+='  -Amsler Grid<br>';
					flag_chk+=1;
				}
			}

			if(cvf_c=="yes"){
				if($("#cvf .positive").length <= 0 && $("#cvf .wnl_lbl").length <= 0){
					show_name+='  -CVF<br>';
					flag_chk+=1;
				}
			}

			if(diplopia=="yes"){
				if($("#dv_w4dot .active").length <= 0){
					show_name+='  -Diplopia/Worth for dot<br>';
					flag_chk+=1;
				}
			}

			if(val==2){
				if(flag_chk>0){
					//Show
					//Prompt
					var title = "Tech Warning";
					var msg = ""+show_name+"<BR>";
					var btn1 = "Continue & Save";
					var func = "top.fmain.decideTechWarning";
					top.fmain.displayConfirmYesNo_v2(title,msg,btn1,0,func,1);

				}else{
					return true;
				}
			}

	}else{
		return true;
	}
}
function decideTechWarning( val ){ if(val == "1"){	top.fmain.saveMainPage(1); 	} }
//Tech Mandatory--

// User Type touched Elements ----------

function utElem_capture(e){
	if(!e){ return; }
	var clr=(typeof(ProClr[logged_user_type])!="undefined")?ProClr[logged_user_type]:""; //Global
	var clr1,clr2;
	if(typeof(clr)=="string"){
		clr1=clr2=clr;
	}else if(typeof(clr)=="object"){
		clr1=clr[0];
		clr2=clr[1];
	}

	if(e.target){
		var eo = e.target;
		var enm = eo.name;
		var eid = eo.id;
	}else{
		//Object
		var eo=e;
		var enm = e.name;
		var eid = e.id;
	}

	var wc = (typeof(examName)!="undefined" && examName!="" && typeof(getWcId)!="undefined" && (examName=="LA" || examName=="Fundus" || examName=="SLE")) ? getWcId() : {nm:""};
	var elue = "#elem_utElems"+wc.nm;
	var elue_cr = elue+"_cur";

	//if Peri/CD in work view
	if( typeof(enm)!="undefined" && (enm.indexOf("_peri")!=-1 || enm.indexOf("elem_rvcd")!=-1) && wc.nm=="" ){
		return;
	}
	//Set Color and save ---

	var tmp = $(elue_cr).val();
	if(typeof(tmp) == "undefined") tmp = "";
	var str = ""+$(elue).val();
	if(typeof(eid)=="undefined"||eid==""){eid=""+enm;}

	//check eid with special characters
	var n = "[^a-zA-Z0-9_\-]";
	var rs = eid.match(n);
	if(rs){return;}

	str=str.replace(eid+",","");
	$(elue).val(str);
	//add in
	if(tmp.indexOf(eid+",")==-1){tmp=tmp+eid+",";}
	$(elue_cr).val(tmp);


	//Set Color and save ---

	if(typeof(enm)!="undefined" && enm.indexOf("elem_ocMeds")==-1){
		//Clear if not checked
		//
		if((eo.type=="checkbox"&&eo.checked==false) || ((eo.type=="text"||eo.type=="textarea"||eo.type=="select-one")&&(eo.value==""||eo.value=="20/"))){
			if(eo.type=="checkbox"){
				clr2 = "transparent";
			}else{
				clr1 = "white";
			}

			//remove from save
			tmp = ""+tmp.replace(eid+",","");
			$(elue_cr).val(tmp);
		}



		//set color here
		if(eo.type=="checkbox"){
			$(eo).css("background-color",clr2);

			//R6
			$("label[for="+eid+"]").css({"border-color": ""+clr2})

		}

		//*
		else if(eo.type=="select-one" && (typeof(examName)!="undefined" && examName == "Fundus") && typeof(eo.onclick)!="function"){
		//This hack is made because when dropdown is open and color is set on it by following statement, IE closes drop down. Now it is fixed.This happens in retina and viterous only.
		$(eo).css("background-color",clr1).trigger("click");
		}
		//*/
		else{
		$(eo).css("background-color",clr1);
		}
	}
}



function utElem_setBgColor(dvSmartC){
	if(typeof(dvSmartC) != "undefined" && dvSmartC!=""){
		var str = $("#elem_utElems_smartchart").val();
		var maincontainer = " #"+dvSmartC+" ";
	}else{
		var str = $("#elem_utElems").val();
		var maincontainer = " body ";
	}

	if(typeof(str)!="undefined" && str!=""){
		var rep,clr,clr1,clr2;
		var ar = str.split("|");
		var ln=ar.length;
		for(i=0;i<ln;i++){

			/*
			if(ar[i].indexOf("3@")!=-1){ //tech
				rep="3@";
				clr = "#00FFFF";
			}else if(ar[i].indexOf("11@")!=-1){	//Resi. Phy.
				rep="11@";
				clr = "#00FF00";
			}else if(ar[i].indexOf("13@")!=-1){	//Scribe
				rep="13@";
				clr = "#FF00FF";
			}else if(ar[i].indexOf("12@")!=-1){	//Attd. phy
				rep="12@";
				clr = "#FF1493";
			}
			*/
			/*else if(ar[i].indexOf("1@")!=-1){	//Fellow

			}else if(ar[i].indexOf("1@")!=-1){	//Photographer

			}*/

			//
			var t= ar[i].split("@");
			clr=ProClr[t[0]];
			if(typeof(clr)=="string"){
				clr1=clr2=clr;
			}else if(typeof(clr)=="object"){
				clr1=clr[0];
				clr2=clr[1];
			}

			var a = (t[1] && typeof(t[1])!="undefined") ? t[1].split(",") : [];
			var l = a.length;
			for(var j=0;j<l;j++){
				if(a[j]!=""){

					//Exception Where Not To color
					if(a[j].indexOf("[]")!=-1|| a[j].indexOf("[")!=-1 ||a[j].indexOf("elem_ocMeds")!=-1||a[j].indexOf("%")!=-1){
					continue;
					}

					$(""+maincontainer+" *[name="+a[j]+"], "+maincontainer+" #"+a[j]+"").css("background-color",function(){

							if(this.type=="checkbox"){

								//R6: label color
								if(this.checked!=false){
									var tmpfor = (typeof(this.id)!="undefined" && this.id!="") ? this.id :  a[j] ;

									//check if label exists
									if($("label[for="+tmpfor+"]").length==0){
										if($(":input[name='"+tmpfor+"']").length>0 && $(":input[name='"+tmpfor+"']").attr("id")!=""){
											tmpfor = ""+$(":input[name='"+tmpfor+"']").attr("id");
										}
									}

									//$("label[for="+a[j]+"]").css({"border-color": ""+clr2}).addClass("r6checkbox");
									$("label[for="+tmpfor+"]").css({"border-color": ""+clr2});

									//in Vision:fixed in php
									//if($(this).parents("#vision").length>0){$(":input[name='"+tmpfor+"'], #"+tmpfor+"").addClass("active");}
								}

								//Clear if not checked
								return (this.checked==false)?"transparent":clr2;
							}else{
								return (this.value=='')? "" : clr1;
							}

						});
				}
			}
		}
	}
}

// User Type touched Elements ----------

//
function set_refer_phy_tbar(flgdb){

	if(typeof(flgdb)!="undefined" && flgdb=="1"){
		//
		$.get(zPath+'/chart_notes/requestHandler.php?elem_formAction=get_refer_phy',function(d){
				if(typeof(d)!="undefined"){ $("#strPtRefPhy").val(d); set_refer_phy_tbar(); }
			});
		return;
	}

	//
	var d = $("#strPtRefPhy").val();
	if(d && d != ""){
		//alert(data.PhyInfo);
		var arrTemp = new Array();
		arrTemp = d.split("!@!");
		var ref_phy_name = arrTemp[0]
		if(arrTemp[0].search(",") > 0){
			ref_phy_name = arrTemp[0];
		}

		var pcp_name = arrTemp[1]
		if(arrTemp[1].search(",") > 0){
			pcp_name = arrTemp[1];
		}

		var cm_name = arrTemp[2]
		if(arrTemp[2].search(",") > 0){
			cm_name = arrTemp[2];
		}

		if(typeof(ref_phy_name) != 'undefined'){
			top.$("#spanDetailRefPhy").html(""+ref_phy_name);
		}

		if(typeof(pcp_name) != 'undefined'){
			top.$("#spanDetailPCP").html(""+pcp_name);
		}

		if(typeof(cm_name) != 'undefined'){
			top.$("#spanDetailCoMangPhy").html(""+cm_name);
		}


		if(ref_phy_name){
			top.$("#spanDetailRefPhy").attr('data-original-title',"<span class='text-nowrap'>"+arrTemp[6]+"</span>");
		}

		if(pcp_name){
			top.$("#spanDetailPCP").attr('data-original-title',"<span class='text-nowrap'>"+arrTemp[7]+"</span>");
		}

		if(cm_name){
			top.$("#spanDetailCoMangPhy").attr('data-original-title',"<span class='text-nowrap'>"+arrTemp[8]+"</span>");
		}

		top.$('#spanMoreRefPhy').removeClass("hidden"); //css({"position":'absolute'})
		top.$("#spanMoreRefPhy").html(""+arrTemp[3]);
		top.$('#spanMorePCP').removeClass("hidden"); //css({"position":'absolute'})
		top.$("#spanMorePCP").html(""+arrTemp[4]);
		top.$('#spanMoreCoMangPhy').css({"position":'absolute'}).removeClass("hidden");
		top.$("#spanMoreCoMangPhy").html(""+arrTemp[5]);

		//$("#vis_ptphy").html(""+data.PhyInfo);
	}
}
/**

Purpose: display Chart note info (or window)
*/
function chrt_showChartInfo(h){

	top.$("#li_rc").hide();
	top.$('#chart_phy_note').attr('title',"");
	top.$("#icoPtLock").addClass("hidden").unbind("click");
	top.$("#ddmenu_p3 li").removeClass("active");
	top.$("#chartdos, #infoIns").html("");
	//top.$("#el_visit").val("").unbind("change");
	//top.$("#el_testing").val("").unbind("change");
	top.$("#el_charttemp").val("").unbind("change");
	top.$("#btnPtForms").unbind("click");
	top.$( "#chart_status" ).html("");
	window.top.$( "#chartdos" ).unbind("change");
	window.top.$( "#lichartp3dd ul a" ).unbind("click");	//, #lichartneurodd ul a

	if(typeof(h)!='undefined' && h=="1"){top.$(".elchart").css("display","none");return;}
	top.$("#chartdos").html($("#elem_dos").val());
	//top.$("#el_visit").val($("#elem_ptVisit_chk").val());
	//top.$("#el_testing").val($("#elem_ptTesting").val());
	top.$("#el_charttemp").val($("#elem_ptTemplate").val());

	//--
	/*
	top.$("#el_visit").bind("change",function(){ var t=top.$(this).val(); top.fmain.$("#elem_ptVisit_chk").val(t).trigger("change"); });
	var tmp = $("#divMenuVisit").html();
	top.$( "#el_visit_ig ul" ).replaceWith(tmp);
	top.$( "#el_visit_ig ul a" ).bind("click", function(){ var h=top.$(this).html();  var a= top.$("#el_visit").val(); if(typeof(a)!="undefined" && a!=""){h=h+", "+a;}  top.$("#el_visit").val(h).trigger("change"); });


	top.$("#el_testing").bind("change",function(){ var t=top.$(this).val(); top.fmain.$("#elem_ptTesting").val(t).trigger("change").trigger("change"); });
	var tmp = $("#divMenuTesting").html();
	top.$( "#el_testing_ig ul" ).replaceWith(tmp);
	top.$( "#el_testing_ig ul a" ).bind("click", function(){ var h=top.$(this).html(); if(h=="Other"){h="";}  top.$("#el_testing").val(h).trigger("change"); });
	*/
	//

	$("#el_visit").bind("change", function(){var x = $.trim($(this).val()); if(x==""){ $("#elem_ptVisit_chk,#elem_ptTesting,#elem_masterPtVisit,#elem_masterTesting").val('').triggerHandler("change");  }
					else{
						var v="",t="";
						if(x.indexOf("-")!=-1){ var ar = x.split("-");
							ar[0] = $.trim(ar[0]);
							if(ar[0] && ar[0]!=""){v=ar[0];}if(ar[1] && ar[1]!=""){t=ar[1];}
						}else{
							if($("#el_visit_ig .mega-dropdown-menu li.col-sm-6").eq(1).find("a:contains('"+x+"')").length){t=x}else{v=x;}
						}

						$("#elem_ptVisit_chk").val(""+v); $("#elem_masterPtVisit").val(""+v).triggerHandler("change");
						$("#elem_ptTesting").val(""+t); $("#elem_masterTesting").val(""+t).triggerHandler("change");

					}});

	top.$("#el_charttemp").bind("change",function(){ var t=$(this).val(); var o=top.fmain.$("#elem_ptTemplate"); var pv=o.val(); o.val(t).data("prv",pv).trigger("change"); top.$(this).val(""); });
	var tmp = $("#divMenuTemplate").html();
	top.$( "#el_charttemp_ig ul, #lichart_status ul" ).replaceWith(tmp);
	top.$( "#el_charttemp_ig ul a, #lichart_status ul a" ).bind("click", function(){ var h=top.$(this).data("id");  top.$("#el_charttemp").val(h).trigger("change"); });
	if(hasActChart == "0"){ top.$("#btn_new_chart").removeClass("hidden"); }else{ top.$("#btn_new_chart").addClass("hidden"); }

	if($("#divLockPassPrompt").length>0){ top.$("#icoPtLock").removeClass("hidden").bind("click", function(){ top.fmain.lock_showPassPrompt();  }); }
	top.$("#btnPtForms").bind("click", function(){  top.fmain.showChartNotesTree();  }); highlight_pt_form_btn();
	var s="Active"; var clbl="label-info";
	if($("#elem_isFormFinalized").val() == "1" || $("#elem_masterpurge_status").val() == "1"){
		s = $("#elem_masterpurge_status").val() == 1 ? "Purged" : "Final";	 clbl="label-danger";
	}
	top.$( "#chart_status" ).html(s).show().removeClass("label-info label-danger").addClass(clbl);
	var t = $("#infoIns_hidden").html()||"";
	top.$("#infoIns").html(t);	//insurance name

	//Refer Phy on top
	set_refer_phy_tbar();

	//P3
	var sep = " - ";
	var p3 = $("#phth_pros").val()||"";
	var p3_i = $("#is_od_os").val()||"";
	if(p3!=""){ window.top.$("#lichartp3dd ul a:contains('"+p3+"')").parent().addClass("active"); p3+=sep;}
	if(p3_i!=""){window.top.$("#lichartp3dd ul a:contains('"+p3_i+"')").parent().addClass("active");}
	window.top.$("#el_chartp3").val(p3+p3_i);
	window.top.$( "#lichartp3dd ul a" ).bind("click", function(){
				var h=$(this).text()||"";
				if(h.indexOf("Eye")!=-1){ return ;}

				//hglight
				$(this).parent().siblings().removeClass("active");
				$(this).parent().addClass("active");

				var tmp = window.top.$("#el_chartp3").val()||"";
				var ar_tmp = tmp.split(sep);
				ar_tmp[0] = $.trim(ar_tmp[0]);
				if(typeof(ar_tmp[1]) != "undefined"){ ar_tmp[1] = $.trim(ar_tmp[1]); }else{ar_tmp[1]="";}
				if(ar_tmp[0] == "OU" || ar_tmp[0] == "OD" || ar_tmp[0] == "OS"){ar_tmp[0] = "";}

				//crnt
				if(h != ""){
					if(h == "OU" || h == "OD" || h == "OS"){
						ar_tmp[1] = h;
					}else if(h == "No Defect"){
						ar_tmp[0]=""; ar_tmp[1]="";
						$(this).parent().parent().find("li").removeClass("active");
					}else{
						ar_tmp[0] = h;
					}
				}

				//
				var curset = (ar_tmp[0]!="") ? "1" : "0" ;
				window.top.fmain.$("#elem_curset_phth_pros").val(curset);
				window.top.fmain.$("#phth_pros").val(""+ar_tmp[0]);
				window.top.fmain.$("#is_od_os").val(""+ar_tmp[1]);
				//reload exam summary
				window.top.fmain.loadExamsSummaryAll();

				//
				if(ar_tmp[0]!="" && ar_tmp[1]!=""){
					h=ar_tmp[0]+sep+ar_tmp[1];
					window.top.$("#el_chartp3").val(h);
					//top.$("#el_chartp3").trigger("change");
				}else if(ar_tmp[0]!=""){
					h=ar_tmp[0]+"";
					window.top.$("#el_chartp3").val(h);
					//top.$("#el_chartp3").trigger("change");
				}else{
					window.top.$("#el_chartp3").val("");
					//top.$("#el_chartp3").trigger("change");
				}
				return false;
			 });

	//neuro/psych
	//var np = $("#elem_neuroPsych").val()||"";
	//window.top.$("#el_chartneuro").val(np);
	//if(np!=""){window.top.$("#lichartneurodd ul a:contains('"+np+"')").parent().addClass("active");}
	$( "#el_chartneuro_ig ul a" ).bind("click", function(){
				$("#el_chartneuro_ig ul li").removeClass("active");
				var h=$(this).text()||"";
				$(this).parent().addClass("active");
				//window.top.$("#el_chartneuro").val(h);
				$("#elem_neuroPsych").val(""+h);
			});
	//memo
	var m = $("#elem_get_memo").val(); var mo= window.top.$("#el_chartp3_ig");
	if(m==1){ mo.hide(); }else{ mo.show(); }//

	//Set title of insurance
	var t = (top.$("#ins_pro")[0]) ? top.$("#ins_pro")[0].title : "" ;
	if(typeof(t)=="undefined"){ t=""; }else if(t!=""){ t=" ( "+t+" )"; }
	var t1 = top.$("#vis_ins").html();
	if(typeof(t1)=="undefined"){ t1=""; }
	var t2 = $.trim(t1+" "+t+"");
	top.$("#infoIns").attr("title", t2);


	//DOS
	window.top.$( "#chartdos" ).val($("#elem_dos").val()).datepicker({dateFormat:top.jQueryIntDateFormat}).bind("change",function(){ window.top.fmain.$("#elem_dos").val(this.value).trigger("change");  });

	//phy notes
	if(phy_note_exists=='1'){window.top.$("#chart_phy_note").removeClass("hidden");}else{window.top.$("#chart_phy_note").addClass("hidden");}
}

function highlight_pt_form_btn(){	if(is_test_uninterpreted=='1'){top.$("#btnPtForms").addClass("highlight");}else{top.$("#btnPtForms").removeClass("highlight");}	}

/*
//Returns first letter only of the provided name
function get_init_names(value){
	var name_arr = new Array();
	var single_nm = '';
	var init_name = value.split(',');
	$.each(init_name,function(id,val){
		var name = $.trim(val).charAt(0);
		name_arr.push(name);
	});
	var final_str = name_arr.join('');
	if(final_str.length > 0){
		return final_str;
	}else{
		return ;
	}

}

//--
*/

function open_medgrid(){
	$("#divMedGridMain").remove();
	var str =	"<div id=\"divMedGridMain\"  class=\"section\">"+
			"<div id=\"load_med_save\" style=\"position:absolute; display:none; background-color:red; color:white; margin:200px 400px; font-size:14px; height:50px;\" >Please wait saving is in process..</div>"+
			"<div id=\"divHeader\" class=\"section_header\" style=\"width:1200px;cursor:move;\"><span class=\"closeBtn\" onclick=\"$('#divMedGridMain').hide()\"></span>Ocular Medication Grid</div>"+
			"<iframe src=\"\" width=\"1200px\"  height=\"100%\" scrolling=\"yes\" border=\"0\" style=\"border:0px\" id=\"frmMedGrid\">"+
			"</iframe>"+ //src=\"../Medical_history/medications/index.php?callFrom=WV&subcall=grid\"
			"</div>";
	$("body").append(str);$("#load_med_save").hide();
	$("#divMedGridMain").css({"display":"none","position":"absolute", "height":"400px", "top":"20px", "left":"20px", "width":"1200px", "z-index":"100","background-color":"white"}).show();
	$("#divMedGridMain").draggable({"handle":"#divHeader"});
}

function showUnFinalize(u,n,w,h)
{
	var oPUF = (typeof(top.oPUF)!="undefined") ? top.oPUF : top.fmain.oPUF;
	// if View Only Access
	if(elem_per_vo == "1"){
		if(n == 'Amendments'){return;}
	}

	if(u != ""){
		//Hack 4 IE 9 : not working in IE9
		if(n == "VF-GL"){  n = "VFGL"; }
		if(n == "OCT-RNFL"){  n = "OCTRNFL"; }

		if(!oPUF[n] || !(oPUF[n].open) || (oPUF[n].closed == true)){
			oPUF[n] = window.open(u,n,'location=0,status=1,resizable=1,left=1,top=1,scrollbars=1,width='+w+',height='+h);
			oPUF[n].focus();
		}else{
			oPUF[n].focus();
		}
	}
}

function makeMemoChart(){
	makeNewChart('',1);
}

// Make New Chart
function makeNewChart(val,memo)
{
	// if View Only Access
	if(typeof(elem_per_vo)!="undefined" && elem_per_vo == "1"){
		return;
	}

	if(typeof(zPath)=="undefined")zPath="";
	var u = (typeof(zPath_remote)!="undefined") ? zPath_remote : zPath;

	if(typeof val == "undefined"){
		val = "GETFORMSETTINGS";
	}

	if(typeof(memo) == "undefined"){memo="";}

	top.show_loading_image('show','','Loading...');
	tmp=encodeURIComponent("Make New Chart Notes");
	top.fmain.window.location.replace(u+"/chart_notes/saveCharts.php?elem_saveForm="+tmp+"&elem_make=1&elem_tempId="+val+"&memo="+memo);

}

// Set Patient Chart ---
function setPtTemplate(obj){
	//alert(""+val);
	if((obj.value != "")){

		var sep = "-[_]-";
		var arr = obj.value.split(sep);
		//alert(arr[0]+" # "+arr[1]);
		if((arr[0] != "") && (arr[1] != "")){

			//new chart
			if((typeof(hasActChart)!="undefined"&&hasActChart=="0") || obj.value=="Default" || obj.value=="Memo" || obj.value=="Amendment" ){
				if(arr[0] == "Memo" && arr[1] == "0"){makeMemoChart();}
				else if(arr[0] == "Amendment" && arr[1] == "0"){ var t=$(obj).data("prv");  obj.value = (typeof(t)!="undefined") ? t : ""; showUnFinalize(zPath+'/chart_notes/onload_wv.php?elem_action=Amendments','Amendments','800','400');}
				else{makeNewChart(arr[1]);	}
				return;
			}

			//Set Value in text box
			obj.value = ""+arr[0];

			/*
			//Submit Form Elems
			var oF1 = document.frm_chart_notes_template;
			if(oF1){
				var oTid = oF1.elem_templateId;
				var oTFid = oF1.elem_tempFormId;
			}else{
				return;
			}
			*/

			//Center Page Elems
			var oiFrm = top.fmain;
			var oF2 = (oiFrm) ? oiFrm.document.frmMain : null;
			if(oF2){
				var oMid = oF2.elem_masterId;
				var oMFz = oF2.elem_masterFinalize;
				var oMRvw = oF2.elem_isFormReviewable
				var oPer = oF2.elem_per_vo;
				var oPrgd = oF2.elem_masterpurge_status;
				var msg="";

				//View Only Permission
				if(oPer && oPer.value == "1"){
					msg+="\nYou do not have permission to make changes.";
				}

				//7/18/2011: We do not need �Not Reviewable� condition.  If chart is Purged or Finalized they cannot change Template.
				//&& (oMRvw && (oMRvw.value != "1"))
				if(oMFz && oMFz.value == "1"){
					//Finalized and not reviewable
					msg+="\nFinalized chart cannot be changed.";
				}

				if(oPrgd && oPrgd.value == "1"){
					//Purged
					msg+="\nPurged chart cannot be changed.";
				}

				if(msg!=""){
					top.fAlert(""+msg);
					obj.value = obj.defaultValue;
					return;
				}

				//Check Changes
				if(isFormChanged == 1){
					//Set Value in Main Form and save form
					oF2.elem_ptGo2.value="ChangeTemplate";
					oF2.elem_ptSrchFindBy.value=arr[1]; //Template id
					oF2.submit(); //Submit Main Form
				}else{
					/*
					//Set Values and submit form
					oTid.value = arr[1];
					oTFid.value = oMid.value;
					oF1.submit();
					*/
					tmp="Change Chart Notes Template";
					top.fmain.window.location.replace(zPath+"/chart_notes/saveCharts.php?elem_saveForm="+tmp+"&elem_templateId="+arr[1]+"&elem_tempFormId="+oMid.value);
					//working
				}
			}
		}
	}
}
// Set Patient Chart ---

// Hide Buttons
function hideButtons(f,id,r,e,cvu,prg)
{
	var ar = [];
	ar["elem_per_vo"]=elem_per_vo;
	ar["userTypeCn"]=user_type;
	ar["f"]=f;
	ar["r"]=r;
	ar["prg"]=prg;
	ar["e"]=e;
	ar["cvu"]=cvu;
	ar["fpv"]=flg_phy_view;
	ar["fz"]=show_finalize_btn;
	ar["prgdel"]=per_prgdel;

	top.btn_show("WV",ar);

	/*
	top.$("#divProcessing2").css("display","none");
	//Label Finalized --
	var b = (top.$("#lblFinalized").get(0)) ? top.$("#lblFinalized") : null;
	if(prg == "1"){
		b.css("visibility","visible").html("<b>Purged</b>");
	}else{
		b.css("visibility",function(){ return (f=="1") ? "visible" : "hidden";}).html("<b>Finalized</b>");
	}
	//Label Finalized --


	if(f == "1"){
		var olbl = top.fmain.$("#liActiveLabel"+id);
		if(olbl && olbl.length > 0)	{
			var lbl = olbl.attr("innerText");
			if(lbl && lbl.indexOf("Active") != -1){
				lbl = lbl.replace(/Active/, "Chart Note"); //"Finalized"
			}
			/*
			if(lbl.indexOf("CEE") != -1){
				lbl = trim(lbl);
			}*-/
			olbl.attr("innerText", lbl);
		}
	}
	*/
}
//--
//Show Prev Chrts --

function showPrevCharts(val){
	if(val == "+1"){
		if(typeof varNxtFId.id != "undefined"){
			top.fmain.showFinalize(varNxtFId.typeChart,varNxtFId.id,varNxtFId.chartStatus,varNxtFId.releaseNum);
		}else{
			top.fAlert("No next chart exits.");
		}

	}else if(val == "-1"){
		if(typeof varPrevFId.id != "undefined"){
			top.fmain.showFinalize(varPrevFId.typeChart,varPrevFId.id,varPrevFId.chartStatus,varPrevFId.releaseNum);
		}else{
			top.fAlert("No previous chart exits.");
		}
	}
}

//Show Prev Chrts --

function showPrevChartsImags( n, id ){
	if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"");
	$.get(zPath+"/chart_notes/requestHandler.php?elem_formAction=ChartPrevImage&id="+id,function(data){
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"");
			if(data){ if(data=="No File Found."){top.fAlert(data);return;} top.$("#chart_img_modal").remove(); top.$("body").append(data); top.$("#chart_img_modal .close").bind("click", function(){ top.$("#chart_img_modal").remove(); });	}
		});
}

function one_eye_valid(w){
	var ret = true;
	//One Eye Values --
	if(w == 'drawingpane' || w == 'Pupil' || w == 'External' || w == 'Ee' || w == 'L&A' || w == 'LA' || w == 'IOP/Gonio' || w == 'Gonio' || w == 'IOP' || w == 'SLE' || w == 'Opt.Nev/Disc' || w == 'Fundus Exam' || w == 'Dilation' || w == 'Fundus Exam Drawing' || w == 'RV' || w == 'Ref_Surg' || w == 'Refractive Surgery'){
		var op = oneEye_isOpAllwd("OU");
		if(!op.alwd || ((w == 'drawingpane') && op.iss != "")){
			flash_msg('Prosthesis/Phthisis is set. This operation is not allowed!', 'danger');
			ret = false;
		}
	}
	//One Eye Values --
	return ret;
}

function openPW(w,s,ainf)
{	var clWidth = document.body.clientWidth;
	var clHeight = window.screen.availHeight;
	if(clHeight==null){clHeight=675;}

	if(!one_eye_valid(w)){
		return;
	}


	if(typeof s != "undefined"){
		s = s.toLowerCase();
	}

	//
	var u="", v="",ag="";
	switch(w){
		case "drawingpane":
			v = "drawingpane.php"; n="drawingpane"; w=clWidth; h=parseInt(clHeight)-125; u="?elem_action=Drawingpane";
			if(typeof(s) != "undefined" && s != ""){ u += "&id="+s;}
		break;
		case "AG":
			v = "amsler_grid.php";n="amsler";w="825";h="340";u="?elem_action=AmslerGrid";ag="agModal";
		break;
		case "CVF":
			v = "cvf.php";n="cvf";w="1000";h="340";u="?elem_action=CVF";ag="cvfModal";
		break;
		/*
		case "worth4dot":
			//var tdip = gebi("sptitle_Dip");
			var odip = $("#dipImgD");
			var tw4d = $("#sptitle_w4dot");
			var ow4d = $("#w4dotD");

			if(ow4d.css("display") == "none"){
				odip.hide();
				ow4d.show();
				tw4d.addClass("w4dot_title"); //tw4d.className.replace(/w4dot_1/,"");
			}else{
				odip.show();
				ow4d.hide();
				tw4d.removeClass("w4dot_1");
			}
			//tdip.className = "bgSmoke";
			return;
		break;

		case "Dip":
			v = "diplopia.php";n="diplopia";w="1002";h="340";u="?elem_action=Diplopia";
		break;
		*/
		case "Pupil":
			v = "pupil_2.php"; n="pupil";w=clWidth;h=parseInt(clHeight)-125;u="?elem_action=PupilExam";
		break;
		case "EOM":
			v = "eom_2.php";n="eom";w=clWidth;h=parseInt(clHeight)-125;u="?elem_action=EOM";
			if(s == "draw"){
				u += "&pg_tab=drw_tab&pg=draw";
			}
		break;
		case "External":
		case "Ee":
		case "Ex":
			v = "external_2.php";n="external";w=clWidth;h=parseInt(clHeight)-125;u="?elem_action=External";
			if(s == "draw"){
				u += "&pg_tab=drw_tab&pg=draw";
			}
		break;
		case "L&A":
		case "LA":
			v = "l_and_a_2.php";n="l_a";w=clWidth;h=parseInt(clHeight)-125; //"695";   //"470";
			u="?elem_action=LA";
			if(s == "lids"){
				u += "&pg_tab=lid_tab&pg=Lid";
			}else if(s == "lesion"){
				u += "&pg_tab=lesion_tab&pg=Lesion";
			}else if(s == "lidpos"){
				u += "&pg_tab=lidposition_tab&pg=LidPosition";
			}else if(s == "lacsys"){
				u += "&pg_tab=lacrimal_tab&pg=Lacrimal_System";
			}else if(s == "drawla"||s == "draw"){
				u += "&pg_tab=drw_tab&pg=draw";
			}else{
				u+= "&pg_tab=lid_tab&pg=Lid";
			}
		break;
		case "IOP/Gonio":
		case "Gonio":
		case "IOP":
			v = "iop_gon_2.php";n="iop";w=clWidth;h="695";//"550";
			u="?elem_action=IOP_Gonio";
			if(s == "iop"){
				u += "&pg_tab=IOP_tab&pg=IOP";
			}else if(s == "gonio"){
				u += "&pg_tab=Gonio_tab&pg=Gonio";
			}else if(s == "drawgonio"||s == "draw"){
				u += "&pg_tab=Drawing_tab&pg=Drawing";
			}
		break;
		case "SLE":
			v = "sle_2.php";n="sle";w=clWidth;h=parseInt(clHeight)-125;//"525//620//490";
			u="?elem_action=SLE";
			if(s == "conj"){
				u+= "&pg_tab=Conj_tab&pg=Conj";
			}else if(s == "corn"){
				u+= "&pg_tab=Cornea_tab&pg=Cornea";
			}else if(s == "ant" ){
				u+= "&pg_tab=AntChamber_tab&pg=AntChamber";
			}else if(s == "iris"){
				u+= "&pg_tab=Iris_tab&pg=Iris";
			}else if(s == "lens"){
				u+= "&pg_tab=lens_tab&pg=lens";
			}else if(s == "drawsle"||s == "draw"){
				u+= "&pg_tab=drw_tab&pg=Drawing";
			}
		break;
		/*case "Optic":
			u = "optic_nerve.php";n="optic";w="1024";h="450";
			if(s == "Disc"){
				u+= "?pg_tab=Disc_tab&pg=Disc";
			}else if(s == "Optic"){
				u+= "?pg_tab=Optic_tab&pg=Optic";
			}
		break;	*/
		case "Fundus Exam":
		case "RV":
			v = "retina_vitreous_2.php";n="r_v";w=clWidth;h=parseInt(clHeight)-125; //"440";
			u="?elem_action=FundusExam";
			if(s == "vit"){
				u+= "&pg_tab=Vitreous_tab&pg=Vitreous";
			}else if(s == "mac"){
				u+= "&pg_tab=Macula_tab&pg=Macula";
			}else if(s == "drawrv"||s == "draw"||s=="drawrvma"||s=="drawrvon"){
				pg = (s=="drawrvma"||s=="drawrvon") ? s : "Drawing";
				u+= "&pg_tab=drw_tab&pg="+pg;
			}else if(s == "peri"){
				u+= "&pg_tab=Periphery_tab&pg=Periphery";
			}else if(s == "bv"){
				u+= "&pg_tab=bv_tab&pg=bv";
			}else if(s == "optic"){
				u+= "&pg_tab=optic_tab&pg=Optic";
			}else if(s == "ret"){
				u+= "&pg_tab=ret&pg=ret";
			}
		break;

		case "Ref_Surg":
		case "Refractive Surgery":
			u="?elem_action=RefractiveSurgery";
			var ar = getMRinfo_SC();
			v = "refractive_surgery.php"; u+="&sod="+ar["sod"]+"&sos="+ar["sos"]+"&cod="+ar["cod"]+"&cos="+ar["cos"]+"&aod="+ar["aod"]+"&aos="+ar["aos"];n="ref_surg";
			w=clWidth;h=parseInt(clHeight)-125;
		break;

		case "VF-GL":
			u='../tests/test_vf_gl.php?pop=1';
			if(typeof(s) != "undefined" && s != ""){ u += "&tId="+s;}
			showUnFinalize(u,'VF-GL',clWidth,clHeight);
			return;
		break;

		case "OCT - RNFL":
			u='../tests/test_oct_rnfl.php?pop=1';
			if(typeof(s) != "undefined" && s != ""){ u += "&tId="+s;}
			showUnFinalize(u,'OCT-RNFL',clWidth,clHeight);
			return;
		break;

		default:
			return;
		break;

	}

	if(u != ""){
		var t = top.fmain.oPUF;
		u = zPath+"/chart_notes/onload_wv.php"+u;

		if(typeof(ainf) != "undefined" && ainf != "" ){
			u+= (u.indexOf("?") == -1) ? "?" : "";
			u+= "&"+ainf;
		}

		//Amsgrid + cvf --
		if(ag!=""){
			//
			//stop
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"");
			$.get(u, function(data){
					if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"");
					if($("#"+ag).length<=0){$("body").append(data);}
					else{$("#"+ag).replaceWith(data);}
					$(".modal-backdrop").remove();
					var xhgt = top.$(window).height();
					var xwdth = top.$(window).width();
					$("#"+ag).on('shown.bs.modal', function () {
					    top.$(this).find('.modal-dialog').css({width:'auto',
								       height:'auto',
								      'max-height':'100%'}); //margin:'30px auto',
						xhgt = (parseInt(xhgt) - (parseInt(top.$(this).find('.modal-header').css("height")) + parseInt(top.$(this).find('.modal-footer').css("height")) + 100));
						top.$(this).find('.modal-body').css({height:xhgt+"px", "padding":"2px"});
						//
						$("#app_ams_od, #app_ams_os, #app_cvf_od_drawing, #app_cvf_os_drawing ").each(function(){  oSimpDrw[this.id]=new SimpleDrawing(this.id); oSimpDrw[this.id].init(); });
						if($('#elem_examDate_amsler').length>0){$('#elem_examDate_amsler').datepicker({changeMonth:true,changeYear:true,dateFormat:z_js_dt_frmt}); ag_setWnlEyeFlag( "0" );} //Amsler grid date
						if($("#"+ag+" .bggrey").length>0){$(":input").bind("click", function(){ $("#"+ag+" .bggrey").each(function(){ $(this).removeClass("bggrey"); });  });	}
					});

					$("#"+ag).css({'z-index':'1041'});
					$("#"+ag).modal("show");
					cn_ta_cvf_ag("#"+ag+" textarea");
				});
			return;
		}
		//--

		if(!t[n] || !(t[n].open) || (t[n].closed == true) || t[n].document==null){
			t[n] = window.open(u,n,'location=0,status=1,resizable=1,left=1,top=1,scrollbars=1,width='+w+',height='+h);
			t[n].focus();
		}else{
			t[n].focus();
		}
	}
}

//Show Pop up exams
function showFinalize(str,id,st,rel,opn,tstId){
	var clWidth = document.body.clientWidth;
	var clHeight = window.screen.availHeight;
	if(clHeight==null){clHeight=675;}

	if(!one_eye_valid(str)){
		return;
	}

	rel = (rel == "1") ? "1" : "0";
	opn = ((rel == "0") || (opn == "1")) ? "1" : "0";
	tstId = (typeof tstId != "undefined") ? tstId : "0";

	//alert(str+"\n"+id+"\n"+st+"\n"+opn+"\n"+rel);
	//return;

	var path="";
	if(typeof(zPath_remote) != "undefined"){ path=""+zPath_remote; }
	else if(typeof(zPath) != "undefined"){ path=""+zPath; }

	var url = "",w,h,n;
	var memoVal = '';
	switch(str)
	{
		case "Amsler Grid":
			//url = "amsler_grid.php";w = "825";h = "340",n="docAG";
			return;
		break;
		case "A/Scan":
		case "Ascan":
			url = "ascan.php?pop=1";n="docAS";
			str="Ascan";
		break;
		case "IOL Master":
		case "IOL_Master":
			url = "iol_master.php?pop=1";n="docIOL_Master";
			str="IOL_Master";
		break;
		case "CVF":
			//url = "cvf.php";w = "1000";h = "340",n="docCVF";
			return;
		break;

		case "EOM":
			url = "onload_wv.php?elem_action=EOM";w = clWidth;h = "675",n="docEOM";
		break;
		case "External":
			url = "onload_wv.php?elem_action=External";w = clWidth;h = clHeight,n="docEx";
		break;
		case "Dilation":
		case "Gonio":
		case "IOP/Gonio":
			url = "onload_wv.php?elem_action=IOP_Gonio";w = clWidth;h = "695",n="docIOP";
			if(str=="Gonio"){ url+="&pg_tab=Gonio_tab&pg=Gonio"; }
		break;
		case "L&A":
			url = "onload_wv.php?elem_action=LA";w = clWidth;h = clHeight,n="docLA";  //"470"
		break;
		/*case "Opt.Nev/Disc":
			url = "optic_nerve.php";w = "1024";h = "440",n="docOpt";
		break;*/
		case "Pupil":
			url = "onload_wv.php?elem_action=PupilExam";w = clWidth;h = clHeight,n="docPupil";
		break;
		case "Opt.Nev/Disc":
		case "Fundus Exam":
		case "Fundus Exam Drawing":
			url = "onload_wv.php?elem_action=FundusExam";w = clWidth;h = parseInt(clHeight)-125,n="docRV";
			if(str=="Fundus Exam Drawing"){ url+="&pg_tab=drw_tab&pg=Drawing"; }
		break;
		case "SLE":
			url = "onload_wv.php?elem_action=SLE";w = clWidth;h = parseInt(clHeight)-125,n="docSLE";
		break;
		case "Fundus":
		case "Disc":
			url = "test_disc.php?pop=1";n="docDisc";
		break;

		case "Topography":
			url = "test_topography.php?pop=1";n="docTopo";
		break;
		case "External/Anterior":
			url = "test_external.php?pop=1";n="docExAnt";
			str = "External_Anterior";
		break;

		case "IVFA":
			url = "test_ivfa.php?pop=1";n="docIvfa";
		break;

		case "ICG":
			url = "test_icg.php?pop=1";n="docIcg";
		break;

		case "HRT":
		case "NFA":
			url = "test_nfa.php?pop=1";n="docNfa";
		break;
		/*case "Ophthalmoscopy":
			url = "ophtha.php";w = "825";h = "390",n="docOptha";
		break;*/
		case "Pachy":
			url = "test_pacchy.php?pop=1";n="docPachy";
		break;
		case "VF":
			url = "test_vf.php?pop=1";n="docVF";
		break;
		case "VF-GL":
			url = "test_vf_gl.php?pop=1";n="docVFGL";
			str=n="VFGL";
		break;
		case "OCT":
			url = "test_oct.php?pop=1";n="docOCT";
		break;
		case "OCT-RNFL":
			url = "test_oct_rnfl.php?pop=1";n="docOCTRNFL";
			str=n="OCTRNFL";
		break;
		case "GDX":
			url = "test_gdx.php?pop=1";n="docGDX";
		break;
		case "Amendment":
			url = "onload_wv.php?elem_action=Amendments";w = "800";h = "400",n="docAmendment";
		break;
		/*case "Contact Lens":
			url = "contact_lens.php";w = "995";h = "550",n="docContact";
		break;*/

		case "Other":
			url = "test_other.php?pop=1";n="docOthr";
		break;
		case "TemplateTests":
			url = "test_template.php?pop=1";n="docOthr";
		break;
		case "CustomTests":
		case "Customtests":
		case "customtests":
			url = "test_template_custom_patient.php?pop=1";n="docOthr";
		break;
		case "Labs":
			url = "test_labs.php?pop=1";n="docLabs";
		break;
		case "B-Scan":
			url = "test_bscan.php?pop=1";n="docbscan";
			str=n="BScan";
		break;

		case "Cell Count":
			url = "test_cellcount.php?pop=1";n="doccellcnt";
			str=n="CellCount";
		break;

		case "Assessment & Plan":
		case "Vision":
		case "Memo Chart Note":
			var memoVal = 'true';
		case "Chart Note": //"Complete Chart Note"
			url = "";
			if(id != ""){
				if(rel!=0){
					var z;
					for(z in oPF){
						if((oPF[z]) && (oPF[z].closed == false)) oPF[z].close();
					}
					for(z in oPUF){
						if((oPUF[z]) && (oPUF[z].closed == false)) oPUF[z].close();
					}
				}

				var strQS="";

				if(st == "Final") {
					/*
					var f = document.frm_prev_chart_notes;	//
					f.hd_finalize_id.value=id;
					f.elem_openForm.value=opn;
					*/

					strQS+="&elem_saveForm=Show Prev Chart Notes";
					strQS+="&hd_finalize_id="+id;
					strQS+="&elem_openForm="+opn;

					if(rel!=1){
						var url;
						url = path+"/common/print_function.php?print_form_id="+id;
						window.open (url,'imedic_print','location=0,status=1,resizable=1,left=1,top=1,scrollbars=1,width=700,height=680');
						return;
					}
				}else{
					//var f = document.frm_new_chart;	//
					strQS+="&elem_saveForm=Make New Chart Notes";
				}
				if(rel!=0){
					/*
					f.memo.value = memoVal;
					f.submit();
					*/
					strQS+="&memo="+memoVal;
					var frmObj;
					if(top.fmain){	frmObj = top.fmain;	}
					else{if(window.opener && window.opener.top.fmain){frmObj = window.opener.top.fmain;}	}

					if(frmObj && typeof(frmObj)!="undefined"){
						if(typeof(zPath) == "undefined"){// for scheduler pag dos
							if(typeof(frmObj.JS_SCHEDULER_VERSION)!=""){zPath = window.opener.top.JS_WEB_ROOT_PATH+"/interface";	}
							if(window.opener && window.opener.top.$('#appt_scheduler_status').length>0){ window.opener.top.$('#appt_scheduler_status').val('unloaded');}
						}
						frmObj.window.location.replace(zPath+"/chart_notes/saveCharts.php?a=1"+strQS);
					}
					return;
				}
			}else{
				//throw("Error Occured");
			}
		break;

	}
	if(url != ""){
		if(tstId != 0){
			url = "/tests/"+url;
			if(url.indexOf("?")==-1) url += "?";
			url += "&tId="+tstId;
			n += "_"+str+"_"+tstId; //
			w = clWidth; h = parseInt(clHeight)-70;

		}else{
			url = "/chart_notes/"+url;
			if(url.indexOf("?")==-1) url += "?";
			url += (st == "Final") ? "&finalize_id="+id : "";
			n += (typeof id != "undefined") ? "_"+id : ""; //Add formid so that all form can be opened.
		}
		var z;
		for(z in oPF){
			if((oPF[z]) && (oPF[z].closed == false)) oPF[z].close();
		}
		if(((typeof(oPF) == "undefined")) || (!oPF[n]) || (!(oPF[n].open)) || (oPF[n].closed == true)){
			oPF[n] = window.open(path+url,n,'location=0,status=1,resizable=1,left=1,top=1,scrollbars=1,width='+w+',height='+h);
			oPF[n].focus();
		}else{
			oPF[n].focus();
		}
	}
}

function saveMainPage(flgSkip,btn_final_pressed) {
	// if View Only Access
	if(elem_per_vo == "1"){ return; }

	if(typeof flgSkip == "undefined" || flgSkip == ""){
		flgSkip = "0";
	}

	var ophpr = $("#phth_pros");
	var oisOdOs = $("#is_od_os");
	if(ophpr.val() != "" && oisOdOs.val() == ""){
		top.fAlert(ophpr.val()+" is selected, please select an Eye to save the Information.");
		return false;
	}

	if(gebi("closeWorkView").value != 'true'){
		var flag = chckMainSaveReqFlds();
		if(flag == false){
			return false;
		}

	}else{
		flgSkip = "1";
	}

	var flg = true;
	if(user_type=="3" && flgSkip == "0" ){
		var flg = mandotry_chk('2');
	}
	if(flg == true){
		if(zSaveWithoutPopupSave=="0"){
			var all_warn="";
			if(str_warn_finalize!=""){ all_warn+=str_warn_finalize; }
			//1.       Work View � Neuro/Physc � If not filled then on Save or Finalize pop-up warning Neuro/Physc is not completed.
			var n = $("#elem_neuroPsych").val(); var m = $("#elem_get_memo").val();
			if($.trim(n)=="" && m!="1"){ all_warn+="Warning! Neuro/Psych is not completed.<br/>"; }
			if($.trim(all_warn)!=""){ if(typeof(zStopSaveWarning)=="undefined" || zStopSaveWarning==""){top.fAlert(all_warn);}  }
		}
		$("#otherSaveVal").val($("#otherSave").val());
		saveMainPageExe(btn_final_pressed);
	}
}

function chckMainSaveReqFlds(){
	var memoPage = gebi('elem_get_memo').value;
	var oCCPro=gebi('elem_pro_id');
	if(oCCPro){
		var getCCvalue = gebi('elem_pro_id').value;
		getCCvalue = ((typeof getCCvalue != "undefined") && (getCCvalue != "")) ? getCCvalue : 0;
	}else{
		getCCvalue = 0;
	}

	var flag = flag2 = 0;
	var Mr1Val = Mr2Val = 0;
	var isMr1Done = isMr2Done = changeMr1Done = changeMr2Done = false;
	var getMROperator = getMR2Operator = 0;
	var isObjNote = (gebi("elem_objNotes")) ? 1 : 0;

	if(isObjNote==0){
		var oProId = gebi('elem_providerId');
		if(oProId){
			var getMROperator = oProId.value;
			var ar = new Array("elem_visMrOdS","elem_visMrOdC","elem_visMrOdA","elem_visMrOdAdd","elem_visMrOsS","elem_visMrOsC","elem_visMrOsA","elem_visMrOsAdd");
			var isMr1Done = false;
			isMr1Done = isCurMRDone(ar);
		}

		var oProId_2 = gebi('elem_providerIdOther');
		if(oProId_2){
			var getMR2Operator = oProId_2.value;
			var ar2 = new Array("elem_visMrOtherOdS","elem_visMrOtherOdC","elem_visMrOtherOdA","elem_visMrOtherOdAdd","elem_visMrOtherOsS","elem_visMrOtherOsC","elem_visMrOtherOsA","elem_visMrOtherOsAdd");
			var isMr2Done = false;
			isMr2Done = isCurMRDone(ar2);
			//TEST
		}
	}

	var msg = "<b>Please address the following:-</b><div class='m10'>";
	var fFalse=0;

	if(memoPage == ''){
		var cchisCount = gebi("elem_ccHx").value;
		var ccHisLength = cchisCount.length;
		if(getCCvalue == 0 && ccHisLength > 0 && memoPage == ''){
			msg += '<li>Operator not authorized for CC & History.</li>';
			fFalse += 1;
		}
	}

	if(( (getMROperator == 0) && (isMr1Done == true) ) || ( (getMR2Operator == 0) && (isMr2Done == true) )){
		msg += '<li>Provider for MR</li>';
		fFalse += 1;
	}

	//Super Bill
	var oSB = isSuperBillMade();
	var sb_dxids="";
	if(oSB.SBill == true){
		if(( oSB.DXCodeOK == false ) || ( oSB.DXCodeAssocOK == false )){
		msg += '<li>Dx code in Super bill for CPT Codes:- '+oSB.CptNotAssoc+'</li>';
		fFalse += 1;
		}
		if(oSB.DXCodeComplete==false){
			msg += '<li>Incomplete ICD-10 DX code(s) in Super bill</li>';
			fFalse += 1;
		}
		if(oSB.mul_ophth_cd==true){
			msg += '<li>Multiple Ophthalmoscopy Code exists in this superbill!</li>';
			fFalse += 1;
		}
		sb_dxids=oSB.dxids;
	}
	$("#sb_dxids").val(sb_dxids);

	if(fFalse > 0){
		if(msg!='') msg +='</div>';
		top.fAlert(""+msg);
		return false;
	}

	return true;
}

function isCurMRDone(te){
	return  isVisElemDone(te);
}

function isVisElemDone(te,isObjArr){
	isObjArr = (typeof isObjArr != "undefined") ? isObjArr : 0;
	if(typeof te != "undefined"){
		tl = te.length;
			for(var j=0;j<tl;j++){
				le = (isObjArr == 1) ? te[j] : gebi(te[j]);
				if(le){
					if((le.type == "text") || (le.type == "select-one") || (le.type == "textarea") ){
						if((le.value != "") && (le.value != "20\\") ){
							if($(le).hasClass("active")){
								return true;
							}
						}

					}
				}
			}
	}
	return false;
}

//After Saving Main Sheet functions
var psm_actAfSave="";
function postSaveMainPageFunctions(data) {
	//console.log(data);

	//if Form Saved
	isFormChanged=0;

	//if Chart is already Finalized
	if(typeof(data.chartFinalized) != "undefined" && data.chartFinalized==1){
		top.fAlert("This Chart is Finalized. \nso this chart will be reloaded with new permissions.","Close chart","top.clean_patient_session(undefined,undefined, 1);");
		return;
	}

	//TW alert Message
	if(typeof(data.as_msg) != "undefined" && data.as_msg!=''){
		top.fAlert("<br />"+data.as_msg);
	}

	//Close Work View
	if(typeof(data.closePtChart) != "undefined" && data.closePtChart==1){
		top.clean_patient_session(undefined,undefined, 1); //
		return;
	}

	/*
	//Pt Redirect to
	if(typeof(data.flg_ptGo2) != "undefined" && data.flg_ptGo2==1&&
		typeof(data.ptGo2) != "undefined" && data.ptGo2!=""){
		top.core_redirect_to(data.ptGo2,'','1');
	}
	*/

	// Variables reset--
	finalize_flag = data.finalize_flag;
	isReviewable = data.isReviewable;

	$("#elem_masterFinalize").val(data.finalize_flag);
	$("#elem_masterRecordValidity").val(1);
	$("#elem_isFormFinalized").val(data.finalize_flag);
	$("#elem_isFormReviewable").val(data.isReviewable);
	$("#elem_masterIsSuperBilled").val(data.elem_masterIsSuperBilled);
	$("#elem_masterFinalizerId").val(data.elem_masterFinalizerId);
	$("#elem_masterpurge_status").val(data.purge_status);
	$("#elem_masterFinalDate").val(data.elem_masterFinalDate);
	$("#elem_masterProvIds").val(data.elem_masterProvIds);
	$("#elem_activeFormId").val(data.elem_activeFormId);
	$("#clws_id").val(data.clws_id);
	$("#trial_no_old").val(data.cl_trialno);
	$("#clws_type_old").val(data.clws_type);
	$("#cl_label").text(data.clws_type_label);
	$("#clws_type").css('width','100px');
	$("#clws_type").text(data.clws_type_dd);


	if(data.elem_procIds){
		var b = data.elem_procIds;
		for(var a in b){
			$("#elem_procedureId_"+a).val(b[a]);
		}
	}

	// Variables reset--

	//finalized
	if(data.finalize_flag == 1){
		hasActChart=data.elem_activeFormId;
		if(data.isReviewable == 1) { setReviewableFunction('frmMain');}else{ setfinalizedFunction('frmMain');}
		chrt_showChartInfo();
		if(hasActChart==0){top.$( "#el_charttemp_ig ul .def_opts, #lichart_status ul .def_opts" ).removeClass("hidden");}//enable menu options
	}

	//Smart chart save
	if(sc_userPress_newchart_save==1){sc_userPress_newchart_save=0;sc_userPress(1);}

	//Buttons
	//console.log(''+data.finalize_flag,''+data.form_id,''+data.isReviewable,''+data.isEditable,''+data.iscur_user_vphy,''+data.purge_status);
	top.fmain.hideButtons(''+data.finalize_flag,''+data.form_id,''+data.isReviewable,''+data.isEditable,''+data.iscur_user_vphy,''+data.purge_status);

	//update Slider : will reload when clicked
	$("#sliderRight").attr("attrFilled",0);
	$("#sliderLeft").attr("attrFilled",0);

	//update yellow bar
	loadAsPlanTBar();

	//update assessment plans
	upAsPlanProbid(data.apid);

	//Print_MR
	if(psm_actAfSave.indexOf("Print_MR")!=-1){
		var w = psm_actAfSave.replace(/Print_MR/,"");
		psm_actAfSave="";
		zSaveWithoutPopupSave=0;
		printMr("show", w);
	}

	if(send_to_ibra==1){cnct_ibra(1);}

	send_to_ibra=0;
	str_warn_finalize=""; //clear warning msg
}

function get_asmt_dx_code_id(){
	var elem_asmt_dxcode_id="";
	$("#assessplan textarea[id*=elem_assessment_dxcode]").each(function(){ var t = $(this).data("dxid"); if(typeof(t)=="undefined"||t==""){ t=""; } elem_asmt_dxcode_id+=""+t+"@@"; });
	$("#elem_asmt_dxcode_id").val(elem_asmt_dxcode_id);

	//console.log(elem_asmt_dxcode_id);

}

// Save Chart Notes
var submitStart=false;
var zSaveWithoutPopupSave=0;
function saveMainPageExe(el_btfinalize_pressed) {
	// if View Only Access
	if(elem_per_vo == "1" || submitStart==true){ return; }


	//close windows
	if(zSaveWithoutPopupSave==0){
		top.fmain.funClosePopUpExe(1);
	}

	//enable elems
	//Check for red_strike
	//freezeVisionExe('0');

	//enabled disabled elems
	if((user_type != 1) && (user_type != 3)){
		disable13Elems(0);
	}

	//Check for red_strike On save
	emptyArcRedStrikeB4Save();
	//alert("before calling setSavedBtn");
	var svtxt="";
	if(typeof(el_btfinalize_pressed)!="undefined" && el_btfinalize_pressed=="1"){ svtxt="Finalize in Progress. Please wait!"; }
	top.fmain.setSavedBtn("1", svtxt);
	//alert("after calling setSavedBtn");
	// vision Status
	//var t = getVisionStatus();
	//document.frmMain.elem_statusElements.value = t;
	var objMainFrm = document.frmMain;
	//objMainFrm.submit();
	submitStart=true;

	//Get DxIDs --
	get_asmt_dx_code_id();
	//return;

	//*
	//SUBMIT by Ajax
	var strsave=$("#frmMain").serialize();
	strsave+="&savedby=ajax";
	if(typeof(el_btfinalize_pressed)!="undefined" && el_btfinalize_pressed=="1"){ //add flag for finalized button pressed
		strsave+="&el_btfinalize_pressed=1";
	}

	//add dx code
	/* To be done later
	var tmp_dx_str = "";
	$(".diagText_all_css").each(function(){

		var tmp_arr = $(this).multiselect("getChecked").map(function(){ return this.value; }).get();
		if(tmp_arr.length>0){
			for(var tmp_x in tmp_arr){
				strsave += "&"+this.id+"["+tmp_x+"]="+encodeURI(tmp_arr[tmp_x]);
			}
		}

	});
	*/

	//var myWindow = window.open("","MsgWindow","width=200,height=100, resizable=1");
	//myWindow.document.write(""+strsave);
	//alert("Before post");
	//console.clear();
	//console.log("strsave: " + strsave);
	$.post("saveCharts.php", strsave, function(data) {
		//document.write("Data Loaded: " + data);
		//alert("Line 1623");
		if(typeof(top.get_pt_edu_alert)!="undefined"){
			top.get_pt_edu_alert();
		}
		submitStart=false;
		zSaveWithoutPopupSave=0;

		//console.log(data);
		postSaveMainPageFunctions(data);
		top.fmain.setSavedBtn("0");

		$.ajax({
			url: "cl_block.php",
			success: function(resp){
				if(resp){
					if($('#ContactLens').length){
						var clParent = $('#ContactLens').parent();
						$('#ContactLens').remove();
						clParent.append(resp);
					}
					applyTypeAhead();
					//User Type Color: Color coding ------------
					utElem_setBgColor(); //this willl work for old
					var arrElems=[];
					var elem=elemString='';
					//oldVals=document.getElementById('elem_utElems').value;
					var curVals=document.getElementById('elem_utElems_cur').value;
					arrElems=curVals.split(',');
					for(x in arrElems){
						elem=arrElems[x];
						if(elem!=""){utElem_capture(document.getElementById(elem));} //this will work for curval
						/*
						if(oldVals.indexOf(elem)=='-1'){
							elemString+=elem+',';
						}
						*/
					}
					/*
					if(elemString!=''){
						document.getElementById('elem_utElems').value=oldVals+'|1@'+elemString+'|';
					}
					*/
					//utElem_setBgColor();

				}
			}
		});

	},'json').fail(function(data){ if(data.responseText.indexOf("Current session experiencing a change")!=-1){top.fancyConfirm(""+data.responseText,"Session error", "window.top.fmain.wv_reload(1)", "window.top.fmain.wv_reload(0)", '', '', 'Refresh','Close Application'); }
						else{  top.fmain.setSavedBtn("0");
							var ermsg = "Error Occurred, Please close this window and try later!  \ncode:"+data.status+", \nstatusText:"+data.statusText+", \n"+data.responseText;

							if(typeof(isDssEnable)!='undefined' && isDssEnable){ermsg=ermsg.replace(/(?:\n)/g, "<br />");top.fAlert(ermsg);}else{alert(ermsg);}

							$.post(zPath+'/chart_notes/requestHandler.php',{'elem_formAction':'LogError', 'req_ptwo':'1' ,'msg':''+ermsg},function(){});
							console.log(data);} });
	//alert("After post");

}

function wv_reload(flg){if(flg==0){top.window.close();}else if(flg==1){top.window.location.reload();}}

function funClose(flg)
{
	if(typeof(flg)=="undefined" || flg!=1){	funClosePopUpExe(); }
	tmp=encodeURIComponent("Close this patient work view");
	var u = (typeof(zPath_remote)!="undefined") ? zPath_remote : zPath;
	top.fmain.window.location.replace(u+"/chart_notes/saveCharts.php?elem_saveForm="+tmp);
}
//

// Finalize Chart Notes
var str_warn_finalize ="";
function FinalizeMainPage(flgPrmpt,confrmed){
	// if View Only Access
	if(elem_per_vo == "1"){ return; }

	var objMainFrm = document.frmMain;
	var chartsign_1 = chartsign_2 = true;

	//Check user have signed
	if((flgPrmpt == "1")){

	}else{
		if(typeof(confrmed)=='undefined' && 1==2){
			top.fancyConfirm('Finalize: Are you sure?', 'Finalize Chart', "top.fmain.FinalizeMainPage("+flgPrmpt+",true)","flgPrmpt=false;");
			flgPrmpt=false;
		}else{
			flgPrmpt=true;
		}
		if(flgPrmpt){
			//For Applet
			var str_num="";
			//var str_phyid="";
			var tmp = $("div.divsign :input[name*=elem_physicianId][value='"+objMainFrm.elem_chartOprtrId.value+"']").parents(".divsign");

			if(tmp.find(":input[name*=elem_signCoords][value!=''][value!='0-0-0:;']").length<=0 && tmp.find(":input[name*=elem_sign_path][value!='']").length<=0){
				if(tmp.find("label.clickable[id*=lbl_phy_sig]").length>0){

					var cid =  ""+tmp.attr("id");
					var num = ""+cid.replace("sign_phy","");
					if(typeof(num)!="undefined"&&num!=""){
						//getPhySign_db(num,'1');
						//return;
						str_num+=num+",";
					}
				}
			}

			//Get Scribe Signature if it Exists
			var tmp_arScribe = "";
			$("div.divsign :input[name*=elem_physicianIdName][value*='(Scribe)']").each(function(data){
					var tmp = $(this).parents(".divsign");
					if(tmp.find(":input[name*=elem_signCoords][value!=''][value!='0-0-0:;']").length<=0 && tmp.find(":input[name*=elem_sign_path][value!='']").length<=0){
						var num = tmp.attr("id").replace("sign_phy","");
						if(typeof(num)!="undefined"&&num!=""){
							//getPhySign_db(num,'1');
							//return;
							str_num+=num+",";
						}
					}
				});

			if(str_num!=""){

				getPhySign_db(str_num, 1);
				return;
			}

		}
	}

	if(flgPrmpt){

		var flag = chckMainSaveReqFlds();
		if(flag == false){
			return false;
		}
		//gebi('memo').value = '';

		var err = false;
		var msg = "<b>Please Fill the following:</b><div class='m10'>";

		var tmp = $("div.divsign :input[name*=elem_physicianId][value='"+objMainFrm.elem_chartOprtrId.value+"']");
		if(tmp.length>0){

			var t=tmp.parents(".divsign").find(":input[name*=elem_signCoords][value!='0-0-0:;']");
			var t2 =tmp.parents(".divsign").find(":input[name*=elem_sign_path][value!='']");

			if((t.length<=0||t.val().length<=0)&&t2.length<=0){
				err = true;
				msg += "<li>Signature</li>";
			}
		}else{
			err = true;
			msg += "<li>Physician Name</li>";
		}



		if(err == false){
			var ispov = $("#elem_proc_only_visit").val();
			var m = $("#elem_get_memo").val();
			str_warn_finalize ="";
			//Warning Super bill: Visit Code NOT filled
			var t = isVisitCodeExits();
			if(typeof(sb_warnVisCd)=="undefined"){ sb_warnVisCd=""; }
			if(sb_warnVisCd=="1" && t==0 && ispov!="1"){	str_warn_finalize += "Warning! Super Bill does not include any visit code.<br/>";	}
			else if(t==-1 && m!="1"){str_warn_finalize += "Warning! No superbill created for this visit.<br/>"; }

			//Attestation warnings
			if( $("#btn_attest_scribe").length > 0 ){var t = $("#btn_attest_scribe").data("attested");  if(typeof(t)=="undefined"||t==""){ str_warn_finalize += "Warning! Scribe Attestation not complete.<br/>";  }}
			if( $("#btn_attest_attend_scribe").length > 0 ){var t = $("#btn_attest_attend_scribe").data("attested"); if(typeof(t)=="undefined"||t==""){ str_warn_finalize += "Warning! Attending scribe attestation not complete.<br/>";  }}
			if( $("#btn_attest_teach").length > 0 ){var t = $("#btn_attest_teach").data("attested"); if(typeof(t)=="undefined"||t==""){ str_warn_finalize += "Warning! Teaching attestation not complete.<br/>";  }}

			objMainFrm.elem_masterFinalize.value = "1";
			objMainFrm.elem_masterFinalizerId.value = objMainFrm.elem_chartOprtrId.value;

			// DSS E-Signature and TIU Title Check
			if(isDssEnable == 1) {
				var tiuTitle = top.fmain.document.getElementById('dssTiuTitle').value;
				if(typeof(tiuTitle) != 'undefined' && tiuTitle != '') {
					top.dssValidateElectronicSignature('show','','','saveChartNote');
				} else {
					top.fAlert('Please select TIU Title from the DSS TIU Title Dropdown.','DSS Required Field');
					return false;
				}
			} else {
				saveMainPage(0,1); //finalized pressed
			}

		}else{if(msg!='') msg += '</div>'; top.fAlert(msg);}
	}
}

function chartNoteUnfinalize(){
	var title = "Unfinalize Chart";
	var msg = "Are you sure you want to unfinalize chart note?<BR>"+
			  "You will need to re-finalize it.";
	var func = "top.fmain.decideChartNoteUnfinalize(1)";
	top.fancyConfirm(msg, title, func);
	return;
}

function chartNoteEdit(){
	var title = "Edit Chart";
	var msg = "Are you sure you want to edit chart notes?<br>"+
			  "You will need to re-finalize it.";
	var func = "top.fmain.decideChartNoteEdit(1)";
	top.fancyConfirm(msg, title, func);
	return;
}

//Edit Chart Notes
function decideChartNoteEdit(val, flg_fnlz){

	//hideConfirmYesNo();
	if(val == 1){ //YES
		//fId
		var oFid = top.fmain.document.getElementById("elem_masterId");
		if(oFid && (oFid.value != "")){
			/*
			var oFrm = document.frm_prev_chart_notes;
			if(oFrm){
				oFrm.hd_finalize_id.value = oFid.value; //
				oFrm.elem_saveForm.value = "ChartNoteEdit";
				oFrm.submit();
			}
			*/
			var memo = $("#elem_get_memo").val();
			var tmp="ChartNoteEdit";
			if(typeof(flg_fnlz)!="undefined" && flg_fnlz == "1"){ tmp="ChartNoteUnfinalize"; }
			top.fmain.window.location.replace(zPath+"/chart_notes/saveCharts.php?elem_saveForm="+tmp+"&hd_finalize_id="+oFid.value+"&elem_openForm=0&memo="+memo);
		}
	}
}

//Unfinalize Chart Notes
function decideChartNoteUnfinalize(val){
	decideChartNoteEdit(val, 1);
}
// --- PURGE CHART ---
function getconfirmationPurgeRequest(){

	// if View Only Access
	if(elem_per_vo == "1" && per_prgdel!="1"){
		top.fAlert('You don\'t have permissions.');
		return;
	}

	var vBtnPrge = top.$("#buttonPurge").val();

	if(vBtnPrge == "Undo Purge"){
		var title="Undo Purge Chart Note";
		var msg="Do you want to undo purge this chart note?";
	}else{
		var title="Purge Chart Note";
		var msg="Do you want to purge this chart note?";
	}
		var func = "top.fmain.confirmPurgeRequest(1);";
		top.fancyConfirm(msg, title, func);
}
function confirmPurgeRequest(strVal){

	//hideConfirmYesNo();
	if(strVal==1){

		var vBtnPrge = top.$("#buttonPurge").val();
		if(vBtnPrge == "Undo Purge"){
			var status = "0", msg="Un-Purging ";
		}else{
			var status = "1", msg="Purging ";
		}

		top.fmain.setSavedBtn("1", msg+"Chart Note. Please wait! ");

		//top.ifrmCenterPage.doPurgeAndFinalizeMainPage();
		//-- Changed ajax call to form submit --

		//var ofrm = document.frm_purgeCn;
		//if(ofrm){
		//	ofrm.elem_status.value = ""+status;
			//ofrm.submit();

			//SUBMIT by Ajax
			$.post("saveCharts.php", {"elem_saveForm":"PurgeCN","elem_status":status}, function(data) {
				top.fmain.setSavedBtn("0");
				//alert("Data Loaded: " + data);
				if(top.$("#lblFinalized").get(0)){
					var htmlStr = (data.purge_status==1) ? '<b>Purged</b>' : '<b>Finalized</b>' ;
					top.$("#lblFinalized").html(htmlStr);
				}

				if(data.purge_status==1){
					$("body").append("<div id=\"dvPrgdSgn\" title=\"Purged chart note\">X</div>");
				}else{
					$("#dvPrgdSgn").remove();
				}

				postSaveMainPageFunctions(data); //defined in center_page.js

			},'json');


		//}
		//-- Changed ajax call to form submit --
	}
}
// --- PURGE CHART ---

//Delete Chart ---
function deleteChart(flg){

	// if View Only Access
	if(elem_per_vo == "1" && per_prgdel!="1"){
		top.fAlert('You don\'t have permissions.');
		return;
	}

	if(typeof(flg)!="undefined" && flg==1){
		top.fmain.setSavedBtn("1", "Deleting Chart Note. Please wait! ");
		$.post("saveCharts.php", {"elem_saveForm":"DeleteCN"}, function(data) {
			//top.fmain.setSavedBtn("0");
			if(typeof varNxtFId.id != "undefined"){ top.fmain.showFinalize(varNxtFId.typeChart,varNxtFId.id,varNxtFId.chartStatus,varNxtFId.releaseNum); }
			else if(typeof varPrevFId.id != "undefined"){ top.fmain.showFinalize(varPrevFId.typeChart,varPrevFId.id,varPrevFId.chartStatus,varPrevFId.releaseNum); }
			else{ wv_reload(1); }
		},'json');
	}else{
		var msg="Do you want to remove this chart note? This operation cannot be reversed.";
		var func = "top.fmain.deleteChart(1);";
		var title= "Delete Chart Note";
		top.fancyConfirm(msg, title, func);
	}
}
//Delete Chart ---

//function check chart before close
function chkWVB4Close(){
	// if View Only Access
	if(typeof(elem_per_vo) == "undefined" || (typeof(elem_per_vo) != "undefined" && elem_per_vo == "1")){
		if(typeof(funClosePopUpExe)=="function") funClosePopUpExe(0);
		return false;
	}

	//if TEst Open
	if(typeof(isTestFormsOpen) == "undefined" || (typeof(isTestFormsOpen) != "undefined" && isTestFormsOpen())){
		return true;
	}

	//close pops
	funClosePopUpExe(1);

	if(top.fmain&&top.fmain.isFormChanged && typeof(top.fmain.isFormChanged)!="undefined" && top.fmain.isFormChanged==1
			&& top.fmain.$("#elem_closePtChart").length>0&&top.$("#save").length>0){ // if work view
			top.fmain.$("#elem_closePtChart").val(1);
			top.fmain.zSaveWithoutPopupSave=1;
			top.$("#save").trigger("click");
			return true;
	}else{
		return false;
	}
}

//Check chart notes changes before moving to other tabs
//returns false if no change else true.
function chkWVB4Move(ptGo2,ptSrch,ptSrchFB,callFrom){
	// if View Only Access
	if(typeof(elem_per_vo) == "undefined" || (typeof(elem_per_vo) != "undefined" && elem_per_vo == "1")){
		if(typeof(funClosePopUpExe)=="function") funClosePopUpExe(0);
		return false;
	}

	/*
	[1/23/13 AK] We should not close any clinical pop-up (i.e. Tests, PAG, GFS, etc.) when they move from tab to tab.  The only time we should close all the pop-ups when they either select different patient or switch user or simply log out
	*/
	//if(ptGo2=="Search"||ptGo2=="Search2"||ptGo2=="Logout"){

		//if TEst Open
		if(typeof(isTestFormsOpen) == "undefined" || (typeof(isTestFormsOpen) != "undefined" && isTestFormsOpen())){
			return true;
		}

	//}//

	//close Pop ups
	if(callFrom!='CL'){
		if(typeof(funClosePopUpExe) != "undefined"){
			funClosePopUpExe(1);
		}
	}
	//
	var flgDel_EId=1;
	var CLSavedCheck='';
	if(callFrom=='CL'){ CLSavedCheck=1; }
	var o1 =(top.fmain && top.fmain.document.frmMain) ? top.fmain.document.frmMain : null;

	if(o1){
		//is changed
		var flag = (isFormChanged==1) ? true : false; //top.fmain.isMainFormChanged();
		if(callFrom=='CL'){ flag = true; } // ADDED BY JASWANT
		if(flag == false){
			return false;
		}

		var fnl = o1.elem_isFormFinalized;
		var rel = o1.elem_isFormReviewable;
		var purge = o1.elem_masterpurge_status;
		if(purge.value>0){return false;}//if purged, then do not save.

		if((fnl.value == "0") || (rel.value == "1")){
			top.fmain.get_asmt_dx_code_id();
			top.fmain.setSavedBtn("1");
			//Disabled
			if(top.fmain.disable13Elems){
				top.fmain.disable13Elems(0);
			}
			o1.elem_ptGo2.value = ptGo2;
			o1.elem_ptSrchPatient.value = ptSrch;
			o1.elem_ptSrchFindBy.value = ptSrchFB;

			o1.elem_clPopUpSaved.value = CLSavedCheck;
			o1.submit();
			o1.elem_clPopUpSaved.value = '';
			flgDel_EId=0;
			return true;
		}
	}

	if(flgDel_EId==1){
	//Empty EncounterId session for accounting section --
	$.get(zPath+'/chart_notes/requestHandler.php?elem_formAction=EmptyEncounterID',function(){});
	//Empty EncounterId session for accounting section --
	}

	return false;
}
///----


// Chart - template:  --

function getPhyViewWV(val){

	var res = chkWVB4Move("ShowPhyView");

	if(res==true){
		//do nothing
	}else{
		//load
		var tmp=encodeURIComponent(val);
		top.fmain.window.location.replace(zPath+"/chart_notes/requestHandler.php?elem_formAction=ShowPhyView&op="+tmp);
	}
}

//--

function funClosePopUpExe( sv ){
	// if View Only Access
	if(typeof(elem_per_vo)!="undefined" && elem_per_vo == "1"){ sv='0'; }

	try{

		var arr = [top.fmain.oPP,top.fmain.oPO,top.fmain.oPF,top.fmain.oPUF,top.fmain.arrSchPop,top.tb_oPU, top.arr_opened_popups];
		for(var zx in arr){
			var tmpoP = arr[zx];
			if(tmpoP){
			//Scan POP
			for(var z in tmpoP){
				if(tmpoP[z] && tmpoP[z].closed == false && z!="day_charges_list"){
					var savebtn = tmpoP[z].document.getElementById("save");
					if(!savebtn && tmpoP[z].jQuery){ savebtn = tmpoP[z].$("button:contains('Done')").filter(function() {   return $(this).text() === 'Done';}); }
					var oSaveClose = tmpoP[z].document.getElementById("elem_saveClose");
					if(oSaveClose){
						oSaveClose.value = "1";
					}

					if(savebtn && savebtn.length>0 && (sv == "1") && (tmpoP[z].name != "winChangePassword") && (tmpoP[z].name != "alerts") && (tmpoP[z].name != "contact_lens_worksheet_popup")){
						savebtn.click();
					}else{
						tmpoP[z].close();
					}
				}
			}
			}
		}

	}catch(ignored){console.log("ERROR: "+ignored.name+" - "+ignored.description);}

}

function funReset()
{
	// if View Only Access
	if(elem_per_vo == "1"){ return; }

	var o = top.fmain.document.frmMain.elements;
	var l = o.length;
	for(var i=0; i<l;i++)
	{
		if((o[i].type == "text") || (o[i].type == "textarea")){
			o[i].value = "";
		}else if(o[i].type == "checkbox"){
			if(o[i].checked == true){
				o[i].checked = false;
			}
		}
	}
}

function setSmartApEye(name1,iter,elem){var name=name1+iter+"[]";key=0;$("input[name='"+name+"']").each(function(index,element){if($(element).attr("id")!=$(elem).attr("id"))
$(element).prop("checked",false);id=name1+iter+key;$("label[for="+id+"]").css({"border-color":"transparent"});key++;});$("input[name='elem_ap_assess[]'][value='"+iter+"']").prop("checked",true);}
function setSmartAssessEye(name1,iter,elem){if($(elem).is(":checked")==false){name=name1+iter+"[]";key=0;$("input[name='"+name+"']").each(function(index,element){$(element).prop("checked",false);id=name1+iter+key;$("label[for="+id+"]").css({"border-color":"transparent"});key++;});}}


function disable13Elems(v){
	if((user_type != 1) && (user_type != 3))return;

	var strElm = "#divMR :input[type!=hidden][type!=button][type!=submit], "+
				 "#div_rvs :input[type!=hidden][type!=button][type!=submit], "+
				 "#div_rvs :input[type!=hidden][type!=button][type!=submit], "+
				 "#elem_chk, "+
				 "#elem_mrPrism1,#elem_mrNoneGiven1,#elem_mrGLPH1, "+
				 "#elem_mrPrism2,#elem_mrList2,#elem_mrGLPH2,#elem_mrCL2, "+
				 "#elem_mrBat2";

	$(strElm).each(function(){
		if(v){
			$(this).attr("disabled","disabled").bind("click change",setElem2Default);
		}else{
			$(this).removeAttr("disabled");
		}
	});


	strElm = "#imgClearMr1,#imgCopyMr1_Pc,#imgCopyMr1_Ar,#imgClearMr2,#imgClearMr3";
	$(strElm).unbind("click");

}//End Funtions
function setElem2Default(){
	if((user_type != 1) && (user_type != 3))return;

	if((this.type == "checkbox") || (this.type == "radio")){
		this.checked = this.defaultChecked;
	}else{
		this.value = this.defaultValue;
	}
	return false;
}

//Archive Divs--
//Chart_summ_table
var to_showdivArc="";
//var to_showdivArc_show="";
var oDivNames = new Array();

function hideAlldivArc(mn){
	//var len = oDivNames.length;
	for(var i in oDivNames){

		//alert(oDivNames[i]+"\n\n"+i);

		//if(oDivNames[i] == 1){
			//var od = gebi("div_"+mn+"_"+i+"");
			//var os = gebi("div_"+mn+"_"+i+"");
			//if(od){od.style.display = "none";}
			//if(os){os.style.display = "none";}
			var od = gebi("divArcCmn"+i);
			if(od){od.style.display = "none";}
			oDivNames[i] = 0;
		//}
	}
}

function postionDivArc(id,mn){
	var idO = id;
	if(id == "FundusExam"){ id="RV"; }
	var str="";
	if($(":input[name='"+id+"']").length>0){
		str=":input[name='"+id+"']";
	}else if($("#lblHx"+id+"").length>0){
		str="#lblHx"+id+"";
	}
	if(str!=""){
		var opos = $(str).offset();
		var hgt = $(str).parents(".esum").height();
		var sTop = $("#divWorkView").scrollTop();
		var hgtD = $("#divArcCmn"+idO).height();
		var top = parseInt(opos.top)+parseInt(sTop)-parseInt(hgtD)-30;

		if(top<sTop){
			top = sTop;
			if(sTop>(opos.top+hgtD)){
				top +=opos.top+hgt-30;
			}else{
				//top +=hgt; //Method by H&T
			}

		}
		//$("#div_"+mn+"_"+id).css({"display":"block","top":top,"left":opos.left+"px"});
		$("#divArcCmn"+idO).css({"display":"block","left":opos.left+"px"});

	}
	/*
	var imgT,imgL,offE;
	oImg = document.getElementById("div_arc_"+id);
	offE = document.getElementById(id);
	imgT = (offE) ? parseInt(offE.getBoundingClientRect().bottom) : 400 ;
	imgL = (offE) ? parseInt(offE.getBoundingClientRect().left) : 200 ;
	var thisScrollTop = document.body.scrollTop;
	imgT += parseInt(thisScrollTop);
	//alert(imgT+"\n"+imgL);
	if(typeof imgT != "undefined"){
		oImg.style.top = imgT+"px";
	}
	if(typeof imgL != "undefined"){
		oImg.style.left = imgL+"px";
	}
	oImg.style.display = "block";
	*/
}

function showdivArc(divId, flg,mn){

	/*
	if(typeof(mn)!="undefined" && mn=="1"){
		mn = "mn";
	}else{
		mn = "arc";
	}
	*/

	//var oDiv = gebi("div_"+mn+"_"+divId);
	var oDiv = gebi("divArcCmn"+divId);
	//var oAbsDiv = gebi("divProcessing");

	if(oDiv){
		if(flg == "1"){ //Display
			//oDiv.style.display="block";
			oDivNames[divId]=1;
			hideAlldivArc(mn);
			postionDivArc(divId,mn);

			if(to_showdivArc){
				to_showdivArc = clearTimeout(to_showdivArc);
			}

		}else{ //Hide
			//oDiv.style.display="none";
			to_showdivArc = setTimeout(function(){if(to_showdivArc){ oDiv.style.display="none";}},500);
		}
	}
}

//Change class style on focus
function remRedStyleArc(obj){
	obj.className = obj.className.replace(/red_strike/g, "");
	if(obj.select) obj.select();
}

//Empty values before save
function emptyArcRedStrikeB4Save(){
	//Set Empty with red _strike
	$(".arcvDeleted").val("");

	//if bg color has bgSmoke
	var arr = new Array("commentsForPatient");
	var len = arr.length;
	for(var i=0;i<len;i++){
		var to = gebi(arr[i]);
		if(to && (to.className.indexOf("bgSmoke") != -1)){
			to.value = "";
		}
	}
}
//Archive Div --

function isTestFormsOpen(){
	var arrOpenPU = new Array();
	var arrObj = new Array(top.fmain.oPF,top.fmain.oPUF,top.fmain.oPO,top.tb_oPU);
	try{
	for(var i in arrObj){
		for(var j in arrObj[i]){
			if(arrObj[i][j] && arrObj[i][j].open && arrObj[i][j].closed != true){
				if(j.indexOf("docAG") != -1 || j.indexOf("amsler") != -1 ){
					arrOpenPU[arrOpenPU.length] = "Amsler Grid";
				}else if(j.indexOf("docDisc") != -1 || j.indexOf("Disc") != -1){
					arrOpenPU[arrOpenPU.length] = "Fundus";
				}else if( j.indexOf("docTopo") != -1 || j.indexOf("Topography") != -1){
					arrOpenPU[arrOpenPU.length] = "Topography";
				}else if( j.indexOf("docExAnt") != -1 || j.indexOf("External") != -1){
					arrOpenPU[arrOpenPU.length] = "External/Anterior";
				}else if( j.indexOf("docIvfa") != -1 || j.indexOf("IVFA") != -1){
					arrOpenPU[arrOpenPU.length] = "IVFA";
				}else if( j.indexOf("docNfa") != -1 || j.indexOf("HRT") != -1 ){
					arrOpenPU[arrOpenPU.length] = "HRT";
				}else if( j.indexOf("docOptha") != -1 || j.indexOf("Optha") != -1 ){
					arrOpenPU[arrOpenPU.length] = "Ophtha";
				}else if( j.indexOf("docPachy") != -1 || j.indexOf("Pachy") != -1 ){
					arrOpenPU[arrOpenPU.length] = "Pachy";
				}else if( j.indexOf("docVF") != -1 || j.indexOf("VF") != -1 ){
					arrOpenPU[arrOpenPU.length] = "VF";
				}else if( j.indexOf("docOCT") != -1 || j.indexOf("OCT") != -1 ){
					arrOpenPU[arrOpenPU.length] = "OCT";
				}else if( j.indexOf("docIcg") != -1 || j.indexOf("ICG") != -1 ){
					arrOpenPU[arrOpenPU.length] = "ICG";
				}else if( j.indexOf("doccellcnt") != -1 || j.indexOf("CellCount") != -1 ){
					arrOpenPU[arrOpenPU.length] = "Cell Count";
				}else if( j.indexOf("docAscan") != -1 || j.indexOf("Ascan") != -1 ){
					arrOpenPU[arrOpenPU.length] = "A/Scan";
				}else if( j.indexOf("docIOL_Master") != -1 || j.indexOf("IOL_Master") != -1 ){
					arrOpenPU[arrOpenPU.length] = "IOL Master";
				}else if( j.indexOf("docbscan") != -1 || j.indexOf("BScan") != -1 ){
					arrOpenPU[arrOpenPU.length] = "B-Scan";
				}else if( j.indexOf("docOthr") != -1 || j.indexOf("Other") != -1 ){
					arrOpenPU[arrOpenPU.length] = "Other-Tests";
				}else if( j.indexOf("docLabs") != -1 || j.indexOf("Labs") != -1 ){
					arrOpenPU[arrOpenPU.length] = "Laboratories";
				}else if( j.indexOf("winSuperBill") != -1 ){
					arrOpenPU[arrOpenPU.length] = "Super Bill";
				}else if( j.indexOf("GDX") != -1 ){
					arrOpenPU[arrOpenPU.length] = "GDX";
				}/*else if( j.indexOf("docGlucoma") != -1 ){
					arrOpenPU[arrOpenPU.length] = "Glaucoma Flow Sheet";
				}*/
			}
		}
	}
	}catch(err){ console.log("first: "+err); }

	if(arrOpenPU.length > 0){
		top.fAlert("Please save and close test forms<br/>"+"<br/>     -"+arrOpenPU.join("<br/>     -"));
		return true;
	}else{
		return false;
	}
}

//Common Chart notes functions : common in work view + exam pop ups
// Reviewable Table
	function setReviewableFunction(id){
		/*
		var frmElem = document.forms[id].elements;
		var len = frmElem.length;
		for(var i=0;i<len;i++){
			if(((frmElem[i].type == "button") || (frmElem[i].type == "submit")) && ((frmElem[i].value == "Save") || (frmElem[i].value == "Done") || (frmElem[i].value == "Cancel"))){
				continue;
			}
			frmElem[i].onfocus = function() { checkAlert(); };
		}
		*/

		if((typeof(id)=="undefined" || id=="")&&$("form[id]").length>0){ id = $("form[id]").get(0).id; } //empty then use first form
		if(typeof(id)!="undefined" && id!=""){
		var strElm = "#"+id+" :input[type!=button][type!=submit][value!=Save][value!=Done][value!=Cancel]";
		$(strElm).bind("focus",checkAlert);

		var strElm = "#"+id+" label[for]";
		$(strElm).bind("mousedown",function(event){ var f = $(this).attr("for"); if(typeof(f)!="undefined" && f!=""){ $("#"+f).triggerHandler("focus"); $(strElm).unbind("mousedown");} });
		}
	}
	function checkAlert()
	{
		if(typeof(window.flgCheckAlert) == "undefined" && (typeof(event)!="undefined" && event.target.id != 'sl_paging' && event.target.id != 'el_pt_crry_frwd_dos'))
		{
			top.fAlert("You are modifying a finalized patient chart note");
			window.flgCheckAlert=1;
		}
	}

//Open Form
	function openNewChart(o, flgmsg)
	{
		if(top.$(".messi").length<=0 || top.$("#wv_old_chart_alert").length<=0){
		var msg = flgmsg ? "You don't have permission to edit chart notes." : "Please Open New Chart Note!";
		top.fAlert(msg);
		top.$(".messi")[0].id="wv_old_chart_alert";
		if(o.type != "checkbox"){
		o.value = o.defaultValue;
		}
		return false;
		}
	}
	function setfinalizedFunction(id, flgmsg){
		/*
		var frmElem = document.forms[id].elements;
		var len = frmElem.length;
		for(var i=0;i<len;i++){
			if(((frmElem[i].type == "button") || (frmElem[i].type == "submit")) && ((frmElem[i].value == "Save") || (frmElem[i].value == "Done") || (frmElem[i].value == "Cancel"))){
				continue;
			}

			//TechTo Dr.Todo
			if(  (frmElem[i].id=="to_do") || (frmElem[i].name=="patient_task") || (frmElem[i].id=="dr_to_do") || (frmElem[i].name=="dr_task") || (frmElem[i].name=="phy_name_drop")  ){
				continue;
			}

			if((frmElem[i].type == "text") || (frmElem[i].type == "textarea") || (frmElem[i].type == "select")){
				frmElem[i].readOnly = true;
			}else{
				frmElem[i].disabled = true;
			}

			frmElem[i].onclick = openNewChart;
			frmElem[i].onchange = openNewChart;
		}
		*/

		if((typeof(id)=="undefined" || id=="")&&$("form[id]").length>0){ id = $("form[id]").get(0).id; } //empty then use first form
		if(typeof(id)!="undefined" && id!=""){

		var strElm = "#"+id+" :input[type!=button][type!=submit][value!=Save][value!=Done][value!=Cancel]"+
					 "[id!=to_do][name!=patient_task][id!=dr_to_do][name!=dr_task][name!=phy_name_drop][id!=elem_mrBat2][name!=pamOrBat][id!=elem_sur_ocu_hx]"+
					 "[type!=hidden][id!=drawing-line-width][id!=text-font-size][id!=elem_lockAdminPass][name!=btn_act_done]";
		var tgNm="";
		$(strElm).each(function(){
			if($(this).parents("#genHealthDiv_wv").length>0){return false;}

			var typee = this.type;
			tgNm = this.tagName.toLowerCase();

			if(typee == "text" || tgNm == "textarea"){
				$(this).attr("readonly","readonly");
			}else{
				$(this).attr("disabled","disabled");
			}
			$(this).bind("click change", function(){openNewChart(this, flgmsg);});

		});
		}
	}

//

var sc_userPress_newchart_save=0;
function sc_userPress(val){
	if(val == 1){
		//V3 + v5 --
		if($(":checked[name*=elem_ap_assess]").length>0){
			if($("#elem_masterRecordValidity").val()=="0"){
				sc_userPress_newchart_save=1;
				saveMainPage();
				return;
			}else{
				//
				if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","bodyMP","",300,600);
				var icd10 = ($("#hid_icd10").length>0) ? $("#hid_icd10").val() : $("#enc_icd10").val();
				document.frmsc.elem_sc_icd10.value=icd10;
				//document.frmsc.submit();

				var param = "elem_saveForm="+document.frmsc.elem_saveForm.value;
					param +=  "&elem_sc_icd10="+icd10;

				$(":checked[name*=elem_ap_assess]").each(function(){
							var v = this.value;
							param += "&elem_ap_assess[]="+v;
							param += "&elem_symptom_"+v+"="+$("#accordion_smart_chart :hidden[name=elem_symptom_"+v+"]").val();
							param += "&elem_site_"+v+"[]="+$(":checked[name*=elem_site_"+v+"]").val();
							param += "&elem_todoid_"+v+"="+$("#accordion_smart_chart  :hidden[name=elem_todoid_"+v+"]").val();
					});
				//
				if(typeof(setProcessImg) != 'undefined' )setProcessImg("1");
				$.post("saveCharts.php", param, function(d){
						window.location.reload();
					});
			}
		}
		$("#div_sc_con").hide();
	}else{
		$("#div_sc_con").hide();
		$("#div_sc_con_detail").modal('hide');
		$("#div_sc_con_detail1").modal('hide');
	}
}


function loadAsPlanTBar(){
	$.post('onload_wv.php', { 'elem_action':"GetListExamDone"},
			function(data){
			//stop
			//if(typeof(setProcessImg)=="function")setProcessImg(0,"summarysheet");
			//document.write(data);
			$('#imp_ex_done').html(data);
			}
		,"");

}

//update assessment and plans with probids
	function upAsPlanProbid(arr){
		//console.log("TEST", arr);
		if(!arr || !arr[0] || arr[0].length<=0) return;
		var arr0 = arr[0], arr1 = arr[1]; arr2 = arr[2];
		/*
		$("input[name*=elem_problist_id_assess]").each(function(indx){ if(this.value==""||this.value=="0"){
				var tasid = this.id.replace(/elem_problist_id_assess/,"elem_assessment"); 	var tasv =$("#"+tasid).val();
				if(tasv!=""){	for(var z in arr0){		if(arr0[z].indexOf(tasv)!=-1&&arr1[z]!=""&&arr1[z]!="0"){	this.value=arr1[z];	}	}	}
			} });
		*/


		$("input[name*=el_pt_ap_id]").each(function(indx){

			var tasid = this.id.replace(/el_pt_ap_id/,"elem_assessment"); 	var tasv = $.trim($("#"+tasid).val());
			var tpl = this.id.replace(/el_pt_ap_id/,"elem_problist_id_assess"); 	var tplv = $.trim($("#"+tpl).val());

			var re = null ;
			if(tasv!=""){ tasv = escapeRegExp(tasv);  re = new RegExp("^"+tasv,"g"); } //&&

			if(this.value==""||this.value=="0"){
				if(tasv!=""){	for(var z in arr0){	if(arr0[z].indexOf(tasv)!=-1 && re.test($.trim(arr0[z]))  && arr2 && arr2[z] && arr2[z]!=""&&arr2[z]!="0"){	this.value=arr2[z];	}	}	}
			}else if(tasv==""){this.value='';}

			if(tplv=="" || tplv == "0"){
				if(tasv!=""){	for(var z in arr0){		if(arr0[z].indexOf(tasv)!=-1&&arr1[z]!=""&&arr1[z]!="0"){	$("#"+tpl).val(arr1[z]); 	}	}	}
			}

			});
	}

function printPC(){
	//top.fmain.showOtherForms('pdf/visits/printpc.php','printpc',902,675);
	//var pr = flg_printMr;
	//pr = (pr == "") ? "0" : "1";
	//winPrintMr = window.open('../main/prescription/pdf/print_mr.php?defaultValsVis='+pr,'winDocChild','width=800,height=600,top=0,left=0');
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;
	/*winPrintPC = window.open('../main/print_patient_pc.php?printType=1','printPatientPC','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');*/
	top.popup_win(zPath+'/chart_notes/requestHandler.php?printType=1&elem_formAction=print_pc','printPatientPC','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
}

function printMr(value,w){
	//
	if(elem_per_vo == "1" || (finalize_flag == "1" && isReviewable != "1")){ value="show"; }
	//
	if(value=="show"){
	var givenMrValue ="";
	$(":checked[name*=elem_mrNoneGiven]").each(function(){
			var a = this.name.replace(/elem_mrNoneGiven/,"");
			if(a!=""&&typeof(a)!="undefined"){ if(givenMrValue!=""){ givenMrValue+=","; }	givenMrValue +="MR "+a+"";	}
		});
	//var givenMrValue = $("select[name=elem_mrNoneGiven1]").val();

	var pr = flg_printMr;
	pr = (pr == "") ? "0" : "1";
	var printone = "";
	if(typeof(w)!="undefined" && w!=""){ printone="&printone="+w; }
	//winPrintMr = window.open('../main/prescription/pdf/print_mr.php?defaultValsVis='+pr,'winDocChild','width=800,height=600,top=0,left=0');
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;
	/*winPrintMr = window.open('../main/print_patient_mr.php?printType=1&givenMr='+givenMrValue,'printPatientMr','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');*/
	top.popup_win(zPath+'/chart_notes/requestHandler.php?printType=1&elem_formAction=print_mr&givenMr='+givenMrValue+printone,'printPatientMr','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
	}else{
		if(typeof(w)=="undefined"){ w=""; }
		psm_actAfSave="Print_MR"+w;
		zSaveWithoutPopupSave=1;
		saveMainPage();
	}
}

//Get Physician Signature Applet
function getPhySign_db(num, flgSave, coId){
	var phyId_value="";
	var num0="";
	//Default
	if(typeof(num) == "undefined"||num==""){
		num=1;
	}

	//make array
	num = ""+num;
	var ar_num = (""+num.indexOf(",")!=-1) ? ""+num.split(",") : [num] ;
	if(ar_num.length>0){
		for(var t in ar_num){
			var tnum=ar_num[t];
			if(typeof(tnum)!="undefined" && tnum!=""){
				if(num0==""){num0=tnum;}
				var phyId = gebi("elem_physicianId"+tnum);
				if(phyId && typeof(phyId.value)!="undefined" && phyId.value!=""){
					phyId_value += phyId.value+",";
				}
			}
		}
	}

	window.status="";
	if((phyId_value == "")){
			var em = "Please select any signer.";
			window.status=em;
			return;
	}

	//Define Var -----
	flgSave = (typeof flgSave == "undefined") ? "0" : flgSave;
	var url = "requestHandler.php";
	params = "elem_formAction=CaptureSign";
	params += "&elem_flgApplet="+flgSave;
	params += "&elem_form_id="+$("#elem_masterId").val();
	params += "&elem_physicianId="+phyId_value;
	params += "&num="+num;
	//-------------------------------------------



	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","td_signature_applet"+num0);

	$.post(url,params,
			function(dt){
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","td_signature_applet"+num0);
			//------  processing after connection   ----------

			if(dt){

				var flgSave1=(typeof(dt.flgSaveDef)!="undefined") ? dt.flgSaveDef : 0 ;
				var data = dt.data;

				for(var x in data){


					var odata = data[x];
					if(odata && ((odata.strpixls && odata.strpixls != "") || (odata.strsignpath && odata.strsignpath != ""))){


						var num = odata.num;
						/*
						if(coId==1) {
							var oT = gebi("td_signature_cosigner_applet");
							var oE= gebi("elem_signCoordsCosigner");
							var oE1= gebi("hdSignCoordsOriginalCosigner");
							var oSP= gebi("elem_cosign_path");
						}else {
						*/
							var oT = gebi("td_signature_applet"+num);
							var oE= gebi("elem_signCoords"+num);
							var oE1= gebi("hdSignCoordsOriginal"+num);
							var oSP= gebi("elem_sign_path"+num);
						//}

						if(oT){
							if(oE){oE.value=""+odata.strpixls;}
							if(oE1){oE1.value=""+odata.strpixls;}
							if(oSP){oSP.value=""+odata.strsignpath;}
							oT.innerHTML = ""+odata.str;
							flgSave1=1;
						}

					}else{
						window.status="Physician signature do not exists.";
					}
				}

				//


				if(flgSave == "1" && flgSave1==1){
					FinalizeMainPage("1");
				}
			}


			//------  processing done --------------------------
			},'json');
}

//WNL links --
//Show Wnl Options like OD, OS, OU
	var wnlOptTimer;
	var wnlOptExm="";
	var wnlOptW="";
	function showWnlOpts(flg,w,val){
		var o2=$("#div_wnlOpts"+w);
		if(o2.length<=0){
			var funcl="javascript:void";
			if(w==1){funcl="setWnlStart";}
			else if(w==2){funcl="setNcStart";}
			else if(w==3){funcl="setResetStart";}
			var ss = ""+
				"<div id=\"div_wnlOpts"+w+"\" class=\"wnlOpts\" onmouseover=\"clearWnlOpts();\" onmouseout=\"showWnlOpts(0,"+w+")\" >"+
				"<a href=\"javascript:void(0);\" class=\"ou\" onclick=\""+funcl+"('OU')\">OU</a><br/>"+
				"<a href=\"javascript:void(0);\" class=\"od\" onclick=\""+funcl+"('OD')\">OD</a><br/>"+
				"<a href=\"javascript:void(0);\" class=\"os\" onclick=\""+funcl+"('OS')\">OS</a>"+
				"</div>";
			$("#summarysheet").append(ss);
			showWnlOpts(flg,w,val);
			return;
		}

		if(flg == 1){
			var o;

			if(typeof val != "undefined"){

				//alert(wnlOptExm+" - "+val+" - "+wnlOptW+" - "+w);

				if(wnlOptExm != "" && val != wnlOptExm){
					o2.hide();
					clearWnlOpts();
				}
				if(wnlOptW!=""&&w!=wnlOptW){
					$("#div_wnlOpts"+wnlOptW).hide();
				}

				//Set
				var id= "";
				if(w==1){id="elem_btnWnl";}
				else if(w==2){id="elem_btnNoChange";}
				else if(w==3){id="btn_reset_chart";}

				o = $("#"+id+val);
				if(o.val()=="Prv" || o.val()=="UNDO")return;
			}

			var pos = o.offset();
			var pos_ss = $("#summarysheet").offset();
			var dwv_scrolltop = $("#divWorkView").scrollTop();

			if(o2 && o2.is(':hidden')){
				var left = (w==1)?pos.left+40:pos.left+30;
				//var top = pos.top - (pos_ss.top+dwv_scrolltop);
				//top +=dwv_scrolltop;
				var top = pos.top;
				top +=dwv_scrolltop;
				top = top - 25;
				var objcss = {"left":""+left+"px", "top":""+top+"px"};
				o2.css(objcss).show();
				wnlOptExm=""+val;
				wnlOptW=w;
			}

			clearWnlOpts();

		}else{
			if(wnlOptTimer==null){
				wnlOptTimer = setTimeout(function(){showWnlOpts(0,w);},1000);
			}else{
				o2.hide();
				clearWnlOpts();
				wnlOptExm="";
				wnlOptW="";
			}
		}
	}

	function clearWnlOpts(){
		wnlOptTimer = clearTimeout(wnlOptTimer);
	}

	function setWnlStart(val){
		if(wnlOptExm != ""){
			showWnlOpts(0,1);
			if(wnlOptExm == "Mas"){
				setWnlValues_all(val);
			}else{
				setWnlValues(wnlOptExm,val);
			}
		}
	}

	function setNcStart(val){
		if(wnlOptExm != ""){
			showWnlOpts(0,2);
			if(wnlOptExm == "Mas"){
				autoSaveNoChange_all(val);
			}else{
				autoSaveNoChange(wnlOptExm,val);
			}
		}
	}
	function setWnlValues_all(iv){

		//Pending
		iv = (typeof iv != "undefined") ?  ""+iv : "OU";
		var arr = new Array("pupil","eom","External","La","Sle","Rv");
		$("#summarysheet .btn[id*=elem_btnWnl]").each(function(){
				if(this.id == 'elem_btnWnlMas'){ return true;}

				var t = this.id.replace(/elem_btnWnl/,'');
				t = t.charAt(0).toUpperCase() + t.slice(1).toLowerCase();
				if(t=="Pupil" || t=="Eom"){t=t.toLowerCase();}

				var tmp = iv;
				//No OD+OS wnl for eom,external
				if(t == "eom" || t == "External" || t == "Gonio"){
					if(tmp!="OU") return true;
				}
				///
				setWnlValues(t,tmp);

			});

	}

// WNL Buttons ----
function autoSaveWnl(w,eye)
{
	// if nothing typed return
	if(w.length == 0)
	{
		return;
	}

	//var objElementEffected = w;  //Local
	var url = "saveCharts.php";
	var params = {};
	params.elem_saveForm = "WNL";
	var f = document.getElementById("elem_masterId");
	params.elem_formId = f.value;
	params.w = w;
	var p = document.getElementById("elem_patientId");
	params.elem_patientId = p.value;
	params.elem_exmEye = eye;
	params.artemp = arrTempProc_js;
	params.cryfwd_form_id = $("#cryfwd_form_id").val();

	//start
	if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"");

	$.post(url, params,
	   function(data){

			//stop
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"");

			//console.log(data);
			//return;

			if(data == "1"){

				top.window.status = "wnl is saved.";
				//
				w = w.toLowerCase();
				if(w=="rv"){
					w="fundus_exam";
				}else if(w=="ag"){
					w="amsgrid";
				}

				loadExamsSummary(w);

				//Tech Mandatory --
				if(w=="Pupil" || w=="Eom"||w=="Ee"){
					mandotry_chk(1);
				}
				//Tech Mandatory --

			}else{
				top.window.status = "Error: wnl is not saved.\nPlease Try again.";
			}
		}

	,"");
}

function setWnlValues(s,eye){

	if((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1"))){
		return;
	}

	//Default Set
	eye = (typeof eye != "undefined") ? eye : "OU";

	if(s.toUpperCase()!="EOM"){
		//One Eye Values --
		var op = oneEye_isOpAllwd(eye);
		if(!op.alwd){
			flash_msg('Prosthesis/Phthisis is set. This operation is not allowed!', 'danger');
			return;
		}
		eye = op.eye;
		//One Eye Values --
	}

	//Save
	autoSaveWnl(s,eye);
}

//----



//No change buttons ---
// No Change
function autoSaveNoChange(w,eye)
{
	//Default Set
	eye = (typeof eye != "undefined") ? eye : "OU";

	//
	if((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1"))){
		return;
	}

	// if nothing typed return
	if(w.length == 0)
	{
		return;
	}

	if(w.toUpperCase()!="EOM"){
		//One Eye Values --
		var op = oneEye_isOpAllwd(eye);
		if(!op.alwd){
			flash_msg('Prosthesis/Phthisis is set. This operation is not allowed!', 'danger');
			return;
		}
		eye = op.eye;
		//One Eye Values --
	}

	//var objElementEffected = w; // local
	var url = "saveCharts.php";
	var params = "elem_saveForm=NoChange";
	var f = $("#elem_masterId");
	params += "&elem_formId="+f.val();
	params += "&w="+w;
	var p = $("#elem_patientId");
	params += "&elem_patientId="+p.val();
	params += "&elem_exmEye="+eye;
	params += "&cryfwd_form_id="+$("#cryfwd_form_id").val();

	var eId = "elem_btnNoChange"+w;
	var eObj = $(":input[id='"+eId+"']");

	if(eObj.length){
		//var r = (eObj.is(":checked")) ? eObj.val() : "0";
		var r = "1";
		params += "&"+eId+"="+r;

	}else{return ;}

	//start
	if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,eId);

	$.post(url, params,
	    function(data){

			//stop
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,eId);

			if(data == "1"){

				w = w.toLowerCase();
				if(w=="rv"){
					w="fundus_exam";
				}else if(w=="gonio"){
					w="iop_gon";
				}else if(w=="ag"){
					w="amsgrid";
				}
				loadExamsSummary(w);

				//Tech Mandatory --
				if(w=="Pupil" || w=="Eom"||w=="Ee"){
					mandotry_chk(1);
				}
				//Tech Mandatory --



			}else{
				top.window.status = "Error: \'no change\' is not saved.\nPlease Try again.";
				//alert("Error: \'no change\' is not saved.\nPlease Try again.")
				console.log(data);
			}
		}
	,"");

}
function autoSaveNoChange_all(v){
	v = (typeof v != "undefined") ?  ""+v : "OU";
	var arr = new Array("Pupil","EOM",
					"External","LA",
					"Gonio","SLE",
					"RV","Ref_Surg");
	var j=0;
	for(var i=0;i<8;i++){
		var o = $("#elem_btnNoChange"+arr[i]);
		if(o.length>0){
			if(o.val()=="NC"){
			autoSaveNoChange(arr[i],v);
			j+=1;
			}else{
			//o.triggerHandler("click");
			}
		}
	}

	if(j==0){
		var cfrm = confirm("Do you want to set all exams to previous visit?");
		if(cfrm!=false){
			for(var i=0;i<7;i++){
				var o = $("#elem_btnNoChange"+arr[i]);
				if(o.length>0){
					if(o.val()=="Prv"||o.val()=="UNDO"){
						o.triggerHandler("click");
					}
				}
			}
		}
	}
}
//No change buttons ---

//WNL links --


//Signature Script ------------------

function addMoreSigns(v){
	var len = $("#elem_signatureNum").val();
	if(len==""){len=1;}
	var num = parseInt(len) +1;
	$("#elem_signatureNum").val(num);
	if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"signatures");
	$.get("requestHandler.php", {"elem_formAction":"addMoreSigns","num":num},function(data){
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"signatures");
			if(data!=""){
				var wh = (v==2) ? ".phy_right" : ".phy_left";
				$("#signatures").append(data);
				$(document).scrollTop($(document).height());
			}
		});
}

function checkDupPhy(obj){
	if(obj && obj.value!=""){
		if($(".divsign :hidden[name*=elem_physicianId][value="+obj.value+"]").length>0 ||
			$(":hidden[name=elem_cosignerId][value="+obj.value+"]").length>0 ||
			$(":hidden[name=elem_physicianId][value="+obj.value+"]").length>0 ){
				top.fAlert("Physician already exists.");
				obj.value="";
				obj.focus();
			}else{
				var num = obj.name.replace("elem_physicianId","");
				$(":input[name=elem_physicianIdName"+num+"]").val(obj[obj.selectedIndex].text);
			}
	}
}

// Applet Signature
	//Applet Function

function getAssessmentSign(num,n,coords,sdata,simg, cnfrm)
{

	// if View Only Access
	if(elem_per_vo == "1"){ return; }
	if(typeof(num) == "undefined"){num=1;}


		var v1="td_signature_applet"+num;
		var v2="dv_ShowSign"+num;
		var v3="elem_signCoords"+num;
		var v4="hdSignCoordsOriginal"+num;
		var v5="elem_sign_path"+num;


	if(typeof(n) == "undefined"){
		var w = parseInt(225*2.5);
		var h =  parseInt(45*2.5);
		var opd= 0; //$("#divWorkView").scrollTop();
		var tmp = $('#'+v1).position();
		if($("#"+v2).length<=0){
			var final_flg = $("#elem_isFormFinalized").val();
			var strpixls="";
			var img =  "<iframe id=\"ifrm_signApp"+num+"\" src=\"signApplet.php?final_flg="+final_flg+"&signType="+num+"\" border=\"0\" height=\"100%\" width=\"100%\" scrolling=\"0\"></iframe>";
			var str = "<div id=\""+v2+"\" >"+
					img+"</div>";
			$('#'+v1).append(str);
			$('#'+v2).css({'position':'absolute','width':''+w+"px",'height':''+h+"px",'background-color':'white','border':'1px solid black','z-Index':2,'overflow':'hidden'});
		}else{
			$("#"+v2).show();
		}
		$("#"+v2).css({"left":tmp.left+"px","top":(opd+tmp.top-h)+"px"});
	}else if(n==1){
			/*
			var objElemSignCoords = gebi(v3);
			objElemSignCoords.value = refineCoords(coords);
			if(typeof objElemSignCoords.onchange == "function"){
				objElemSignCoords.onchange();
			}
			var objSignCoordsOriginal = gebi(v4);
			//Sign
			var checkSignDrawing = checkAppletDrawing(objElemSignCoords.value);
			if((checkSignDrawing) && (objSignCoordsOriginal.value == ""))
			{
				objSignCoordsOriginal.value = objElemSignCoords.value;
			}
			else if((objElemSignCoords.value != objSignCoordsOriginal.value) && (objSignCoordsOriginal.value != ""))
			{
				objSignCoordsOriginal.value = objElemSignCoords.value;
			}
			*/
			var fid = $("#elem_masterId").val();
			$('#'+v1+' img').remove();
			$("#"+v2).hide();
			var final_flg = $("#elem_isFormFinalized").val();
			var proId=(final_flg==1) ? $('#'+v1).parents("div.divsign").find(":input[name*=elem_physicianId][type!=text]").val() : "";
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,""+v1);
			var p = { 'elem_formAction':'GetSign','strpixls':''+coords+'','fid':''+fid,'signType':num,'final_flg':final_flg,'proId':proId,'sData':sdata,'sImg':simg};
			$.post('requestHandler.php',p,function(data){
					if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,""+v1);
					if(data&&data.src&&data.src!=''){
					var u = zPath;
					$('#'+v1).append("<img src=\""+data.src+"\" alt=\"sign\" width=\"150\" height=\"30\" >");
					$('input[name='+v5+']').val(data.sign_path);
				}  }, "json");
			//$('#td_signature_applet').load('common/requestHandler.php',{ 'elem_action':'GetSign','strpixls':''+objElemSignCoords.value+'','enc':1});

	}else if(n==2){
		$("#"+v2).hide();
	}else if(n==3){ //Clear Sign
		/*
		26-11 : Del of signature should simply erase the signature not delete the entire signature box.
		*/
		//Delete if num > 1
		//if(num > 1){
		if($('#'+v1+' img').length>0){

			$('#'+v1+' img').remove();
			$('input[name='+v5+']').val("");
			$("#"+v3).val("");

		}else{

			if($('#'+v1+'').parents("div.divsign").children("select[value='']").length>0){
				$('#'+v1+'').parents("div.divsign").remove();
			}else{
				if(typeof(cnfrm)=="undefined") {
					top.fancyConfirm("Are you sure to delete this physician?", "", 'top.fmain.getAssessmentSign("'+num+'","'+n+'","'+coords+'","'+sdata+'","'+simg+'",true)');
					return
				}
				else if(cnfrm==true){

					var fid = $("#elem_masterId").val();

					if($('#'+v1+'').parents("div.divsign").find(":hidden[name*=elem_physicianId]").length>0){
						var proid=$('#'+v1+'').parents("div.divsign").find(":hidden[name*=elem_physicianId]").val();
					}else if($('#'+v1+'').parents("div.divsign").find("select[name*=elem_physicianId]").length>0){
						var proid=$('#'+v1+'').parents("div.divsign").find("select[name*=elem_physicianId]").val();
					}

					if($.isNumeric(proid)){
						//return;
						$.get("saveCharts.php",{"elem_saveForm":"DelSignature","proid":proid,"fid":fid},function(data){
							if(data==0){
								$('#'+v1+'').parents("div.divsign").remove();
								//$("#switchSign"+num).remove();
							}else{ window.status="Error: "+data;}
						});
					}else{
						$('#'+v1+'').parents("div.divsign").remove();
					}

					return;
				}
			}
		}

		//}
		//*/
	}
}

//Signature Script ------------------

function setPrPh(o)
{
	if(o.checked == true){
		$("#phth_pros").val(""+o.value);
		$("#elem_curset_phth_pros").val("1");
		$("#p3s input[name!='"+o.name+"']").attr("checked", false).each(function(){utElem_capture(this);});
		$("#is_od_os").css("visibility","visible");

		/*//R6.5: Dt.: 22-09-2014: Prosthesis � Need the ability to select both eyes.
		if(o.value=="Prosthesis" && $("#is_od_os").val()=="OU"){
			alert("Prosthesis can be either OD or OS.");
			$("#is_od_os").val("OD");
		}
		*/

	}else{
		$("#is_od_os").val("").css("visibility","hidden");
		$("#phth_pros").val("");
		$("#elem_curset_phth_pros").val("0");
	}

	//reload exam summary
	loadExamsSummaryAll();
	/*//Check and Disable Fields and Insert Labels
	//oneEye_setCN_v2();*/



	/* Stopped Temporary
	//Stop tempo--
	return;
	//--

	var td = gebi("td_eyePrPh");
	var ph = gebi("elem_phthisis");
	var pr = gebi("elem_prosthesis");
	var oe = gebi("is_od_os"); //elem_eyePrPh
	var pv = gebi("elem_poorView");
	var pp = gebi("phth_pros");
	var oCurSetPP = gebi("elem_curset_phth_pros");
	oCurSetPP.value = "0";

	var flag = 0;
	var os_issue=os_eye="";

	//Eye
	if((td.style.visibility == "visible") && (oe.value != "")){
		os_eye=""+oe.value;
	}

	if(o.checked == true){
		flag = 1;
		os_issue = pp.value = ""+o.value;

		if(o.value == "Poor View"){
			pr.checked = false; //prosthesis
			ph.checked = false;	//phthisis

		}else if(o.value == "Phthisis"){
			pr.checked = false; //prosthesis
			pv.checked = false;	//poor view
		}else{
			ph.checked = false; //phthisis
			pv.checked = false;	//poor view
		}
		if(td.style.visibility == "hidden"){
			td.style.visibility = "visible";
		}else{
			setPhPrEye(oe);
			return;
		}

	}else {
		td.style.visibility = "hidden";
		oe.value = "";
		pp.value = "";
		emptyPP();
	}

	//Check and Disable Fields and Insert Labels
	oneEye_setCN();
	//Check and Disable Fields and Insert Labels

	if(o.name == 'elem_poorView'){
		setTimeout(function(){

		},50);

	else if(o.name == 'elem_phthisis'){
		setTimeout(function(){
			dis_od_chart();
			dis_os_chart();
			dis_od_fns();
			dis_os_fns();
			dis_od_img();
			dis_os_img();
			dis_od_nlp();
			dis_os_nlp();
		},50);
	}else if(o.name == 'elem_prosthesis'){
		setTimeout(function(){
			dis_od_chart();
			dis_os_chart();
			dis_od_fns();
			dis_os_fns();
			dis_od_img();
			dis_os_img();
			dis_od_nlp();
			dis_os_nlp();
		},50);
	}
	*/

}

function setPhPrEye(o)
	{
		var prph = $("#phth_pros").val();

		if(prph=="" || o.value==""){
			$("#elem_curset_phth_pros").val("0");
		}else{
			//R6.5: Dt.: 22-09-2014: Prosthesis � Need the ability to select both eyes.
			/*
			if(prph=="Prosthesis" && o.value=="OU"){
				alert("Prosthesis can be either OD or OS.");
				o.selectedIndex = (o.options[3].defaultSelected == true) ? 3 : 2;
			}
			*/

			$("#elem_curset_phth_pros").val("1");
		}

		//reload all exams
		loadExamsSummaryAll();
		//Check and Disable Fields and Insert Labels
		//oneEye_setCN_v2();


		/*
		//Stop tempo--
		return;
		//--

		var ph = gebi("elem_phthisis");
		var pr = gebi("elem_prosthesis");
		var pv = gebi("elem_poorView");
		var oCurSetPP = gebi("elem_curset_phth_pros");
		oCurSetPP.value = "0";

		var os_issue = os_eye = "";
		emptyPP();
		var flag = false;
		if(ph.checked == true){
			os_issue = "Phthisis";

			if(o.value == "OU"){
				gebi("elem_phthisisOd").value = "1";
				gebi("elem_phthisisOs").value = "1";
				flag = true;
			}else if(o.value == "OD"){
				gebi("elem_phthisisOd").value = "1";
				flag = true;
			}else if(o.value == "OS"){
				gebi("elem_phthisisOs").value = "1";
				flag = true;
			}
		}
		else if(pr.checked == true){
			os_issue = "Prosthesis";

			if(o.value == "OU"){
				alert("Prosthesis can be either OD or OS.");
				o.selectedIndex = (o.options[3].defaultSelected == true) ? 3 : 2;
				gebi("elem_prosthesisOd").value = "1";
				flag = true;
			}else if(o.value == "OD"){
				gebi("elem_prosthesisOd").value = "1";
				flag = true;
			}else if(o.value == "OS"){
				gebi("elem_prosthesisOs").value = "1";
				flag = true;
			}
		}else if(pv.checked == true){
			os_issue = "Poor View";
			flag = true;
		}

		//TEST
		if(flag == true){
			//Set Current Ph_pr value
			oCurSetPP.value = "1";
			//Set Current Ph_pr value

			//Check and Disable Fields and Insert Labels
			oneEye_setCN();
			//Check and Disable Fields and Insert Labels
		}
		*/
	}

//Load Summary ---

function loadExamsSummary(enm){
	if(enm == "")return;

	var url = "onload_wv.php";
	var params =  {};
	params.elem_action = "GetExamSummary";
	params.enm = enm;
	//One Eye
	var oe_issue = $("#phth_pros").val();
	var oe_eye = $("#is_od_os").val();
	var oe_curr = $("#elem_curset_phth_pros").val();
	if(oe_issue!="" && oe_eye!=""){
		params.oe =""+oe_issue+"~!~"+oe_eye+"~!~"+oe_curr;
	}

	//Template
	if(enm=="sle" || enm=="fundus_exam"){
		params.artemp =arrTempProc_js;
	}

	//start
	setProcessImg(1,"summarysheet");

	$.get(url, params,
	    function(data){
			//stop
			setProcessImg(0,"summarysheet");

			if(data[enm] != "" && enm!="drawingpane"){
				$("#"+enm).html(data[enm]);
			}else if(enm=="vf_oct_gl"){
				$("#"+enm).html(data[enm]);
			}

			if(enm!="dv_cpctrl" && enm!="dv_stereopsis" && enm!="dv_w4dot"){	setExamsLoaded(enm); }

			//drawing
			if(data["drawingpane"] != ""){
				if($.trim(data["drawingpane"]) == "No Drawing exits."){ data["drawingpane"]=""; }
				$("#drawingpane").html(data["drawingpane"]);
			}

			//
			if(data["imp_ex_done"] != ""){$("#imp_ex_done").html(data["imp_ex_done"]);}
		},"json");
}

function loadExamsSummaryAll(){

	//console.log($('#summarysheet>div').length);

	$('#summarysheet>div').each(function(indx){
			var eid = $(this).prop("id");
			if(typeof(eid)!="undefined" && eid!=""){
			if(eid!="sec_wv_sum" && eid.indexOf("div_wnlOpts")==-1){
				if($(this).html()!=""){
					loadExamsSummary(eid);
				}
			}
			}
		});

	/*
	var arr = $('#summarysheet>div'); //['pupil','eom','external','la','iop_gon','sle','fundus_exam','ref_surg'];
	for(var x in arr){
		if($("#"+arr[x]).length>0){
			//loadExamsSummary(arr[x]);
		}
	}
	*/
}
//Load Summary ---

//Set Exams After Loading--
function setExamsLoaded(enm){
	setFindingsLinks();
}

//Set Exams After Loading--
//set findings links in summary--
function setFindingsLinks(){
	// if View Only Access  : //ifchart is finalized than do not fire
	if(elem_per_vo == "1" || (finalize_flag==1&&isReviewable==0)){ return; }
	$("#summarysheet b.finding").each(function(){ var ea = $(this).data("ev_atch"); if(typeof(ea)=="undefined"){ $(this).data("ev_atch", 1);  $(this).bind("click", function(){ var cs=$(this).data("sen"); var cfind = $(this).html();
				if(cfind=="Comments:"){ cfind = cfind+cs;  }
				var cfind_f=$(this).data("symp-full"); if(typeof(cfind_f)!="undefined" && cfind_f!="" ){cfind = cfind_f;}//for multi level symp
				if(typeof(cfind)!="undefined" && cfind!="" ){ checkSympTyped(this,cfind,'',cs);  }});   }});
}
//--
var checkSympTyped_qry="";
function checkSympTyped(ob, symp,fromas,exm){

	var searchVal = symp; //ob.value;
	var searchVal = searchVal.split(";");
	searchVal =$.trim(searchVal[0]);
	searchVal =encodeURI(searchVal);
	var oid = (typeof(ob.id)!="undefined"&&ob.id!="") ? ob.id : "summarysheet";
	var ct = ""+$("#elem_ptTemplate").val();
	ct =encodeURI(ct);
	if(typeof(fromas)=="undefined"){ fromas=""; }
	if(typeof(exm)=="undefined"){ exm=""; }

	//icd 1 or 9
	var vicd10 = $("#hid_icd10").val();
	if(typeof(vicd10)=="undefined"){
		if(window.opener && window.opener.top.fmain){
		vicd10 = window.opener.top.fmain.$("#hid_icd10").val();
		}
	}

	var url = "requestHandler.php";
	var params = "elem_formAction=SmartChartDetail&symp="+searchVal+"&mode=NoAP&ctemp="+ct+"&fromas="+fromas+"&icd10="+vicd10+"&sen="+exm;
	if(checkSympTyped_qry!=params){checkSympTyped_qry=params;}else if($("#div_sc_con_detail").length>0){return;}

	$("#div_sc_con_detail").modal('hide');
	$("#div_sc_con_detail, .modal-backdrop").remove();

	//alert(params+" - "+oid);
	//return;

	//
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1",""+oid);

	$.get(url, params,
		function(data){

			//
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0",""+oid);

			//var  dw = window.open("", "ss", "width=200,height=200");
			//dw.document.write(data);

			if(data != ""){
				//remove if any
				$("#div_sc_con_detail").modal('hide');
				$("#div_sc_con_detail, .modal-backdrop").remove();
				//close typeahead if any
				//
				$("body").append(data);
				$("#div_sc_con_detail").modal({backdrop: false});
				$("#div_sc_con_detail").modal('show');
				//$("#div_sc_con_detail").draggable({handle:"th"});
				//$("#idMultiPlanSCPop").draggable({handle:"#hdrMultiPlanSCPop"});
				//date
				$( ".dacry input[type=text], .lacsci input[type=text], .ctmri input[type=text]" ).datepicker({dateFormat:"mm-dd-yy", showOn: "button"});
				utElem_setBgColor("div_sc_con_detail");
				// typeahead --
				$( "#div_sc_con_detail textarea, #div_sc_con_detail input[type=text]" ).bind("focus", function(){ if(!$(this).hasClass("ui-autocomplete-input")){cn_ta1_no_assess(this, '');};  });
				newET_setGray_v3();

			}
		}
	);

}





//-- Assessment plan ---
function clearAssessRow(id){ //enter any id and clear row values
	var a = ["elem_assessment_dxcode", "elem_assessment", "elem_plan", "elem_apOu", "elem_apOd", "elem_apOs", "no_change_", "elem_resolve"];
	var rx = rgp(a.join("|"));
	var asdxid = id.replace(rx,"");
	for(var x in a){
		var t = "#"+a[x]+asdxid;
		if($(t).is(":checkbox")){
			$(t).prop("checked", false);
			if(t.indexOf("no_change_")!=-1 || t.indexOf("elem_resolve")!=-1){ $(t).triggerHandler("click"); }
		}else{
			$(t).val("");
		}
	}
}

function chkSelection(s, obj){

	if(obj.tagName == "SMALL"){
		var lna = $("#assessplan :checkbox[name='elem_apnc[]']").length-1;
		var lnb = $("#assessplan :checked[name='elem_apnc[]']").length;
		$("#assessplan :checkbox[name='elem_apnc[]']").each(function(){
				if(lnb>=lna){ //clear all
					if(this.checked){ $(this).prop("checked", false).triggerHandler("click");}
				}else{ //check all
					if(!this.checked){ $(this).prop("checked", true).triggerHandler("click");}
				}
			});
		return;
	}

	if(obj.id.indexOf("no_change_") != -1){
		if(obj.checked == true){
			gebi("elem_resolve"+s).checked = false;
		}
	}else if(obj.id.indexOf("elem_resolve") != -1){
		if(obj.checked == true){
			var o = gebi("no_change_"+s);
			if(o){
				o.checked = false;
				//o.onclick();
			}
		}
	}
	setAPClr(s, obj);
}
function setAPClr(w,o){
	var w1=w-1;
	var clr="";

	if($("#no_change_"+w+":checked").length>0){
		clr="apClr";
	}else if($("#elem_resolve"+w+":checked").length>0){
		clr="apClrRes";
	}

	$("#assessplan .planbox:eq("+w1+") textarea").removeClass("apClr apClrRes");
	if(clr!=""){
		$("#assessplan .planbox:eq("+w1+") textarea").addClass(clr);
	}

	restorePlan_2(w,o);
}

function restorePlan_2(val,obj){
	//ifchart is finalized than do not fire
	if(finalize_flag==1&&isReviewable==0)return;

	if(obj.checked == true){
		var oAssess = gebi("elem_assessment"+val);

		var targetPlan,cfPlan=null;
		targetPlan = $("#assessplan .planbox:eq("+(val-1)+" textarea)");
		cfPlan = targetPlan.attr("data-elem_CFPlan");

		if(typeof(cfPlan)!="undefined" && (($.trim(oAssess.value) != "") && ($.trim(targetPlan.val()) == ""))){
			targetPlan.val(cfPlan);
		}
	}
}

//Function Adjust
function ap_rowAdj(mf,m2){

	//mf = 3;
	//m2 = 1;
	if(((typeof mf != "undefined") && (mf != 0)) &&
	   ((typeof m2 != "undefined") && (m2 != 0)) &&
		(mf != m2)
	  ){

		var arr = ["elem_assessment","elem_plan","elem_resolve","no_change_",
					"elem_apOu","elem_apOd","elem_apOs","elem_assessment_dxcode","elem_problist_id_assess",
					"el_pt_ap_id","el_amst_site"];
		for(var x in arr){
			var elm = arr[x];
			var t2,t,cft ="";

			if($("#"+elm+m2).attr("type") == "checkbox"){

				t= $("#"+elm+m2).is(":checked");
				t2=$("#"+elm+mf).is(":checked");
				if(typeof(t2)=="undefined"){t2=false;}
				$("#"+elm+m2).prop("checked", t2).each(function(){
						if(elm=="no_change_"||elm=="elem_resolve"){
							setAPClr(m2,this);
						}
					});


			}else{
				t= $("#"+elm+m2).val();
				if(typeof(t)=="undefined"){ t=""; }
				$("#"+elm+m2).val(""+$("#"+elm+mf).val()).triggerHandler("keyup");

				if(elm == "elem_plan"){
					cft = $("#"+elm+m2).attr("data-elem_CFPlan");
					$("#"+elm+m2).attr("data-elem_CFPlan",$("#"+elm+mf).attr("data-elem_CFPlan"));
				}else if(elm == "el_amst_site"){
					cft = $("#"+elm+m2).parent().hasClass("hidden");
					if($("#"+elm+mf).parent().hasClass("hidden")){
						$("#"+elm+m2).parent().addClass("hidden");
						$("#elem_assessment"+m2).parent().removeClass("col-sm-11").addClass("col-sm-12");
					}else{
						$("#"+elm+m2).parent().removeClass("hidden");
						$("#elem_assessment"+m2).parent().removeClass("col-sm-12").addClass("col-sm-11");
					}
				}else if(elm == "elem_assessment_dxcode"){
					cft = $("#"+elm+m2).attr("data-dxid");
					$("#"+elm+m2).attr("data-dxid",$("#"+elm+mf).attr("data-dxid"));
				}
			}

			if(mf < m2){
				for(var i=m2-1;i>=mf;i--){

					if($("#"+elm+i).parents(".planbox").css("display")=="none"){ continue; } // leave deleted ap rows

					var cft1 ="";

					if($("#"+elm+i).attr("type") == "checkbox"){

						var t1 = $("#"+elm+i).is(":checked");
						if(typeof(t)=="undefined"){t=false;}
						$("#"+elm+i).prop("checked",t).each(function(){
								if(elm=="no_change_"||elm=="elem_resolve"){
									setAPClr(i,this);
								}
							});

					}else{
						var t1 = $("#"+elm+i).val();
						if(typeof(t1)=="undefined"){ t1=""; }
						$("#"+elm+i).val(""+t).triggerHandler("keyup");

						if(elm == "elem_plan"){
							cft1 = $("#"+elm+i).attr("data-elem_CFPlan");
							$("#"+elm+i).attr("data-elem_CFPlan",cft);
						}else if(elm == "el_amst_site"){
							cft1 = $("#"+elm+i).parent().hasClass("hidden");
							if(cft){
								$("#"+elm+i).parent().addClass("hidden");
								$("#elem_assessment"+i).parent().removeClass("col-sm-11").addClass("col-sm-12");
							}else{
								$("#"+elm+i).parent().removeClass("hidden");
								$("#elem_assessment"+i).parent().removeClass("col-sm-12").addClass("col-sm-11");
							}
						}else if(elm == "elem_assessment_dxcode"){
							cft1 = $("#"+elm+i).attr("data-dxid");
							$("#"+elm+i).attr("data-dxid",cft);
						}
					}

					t=t1;
					cft=cft1;
				}
			}else if(mf > m2){
				for(var i=m2+1;i<=mf;i++){

					if($("#"+elm+i).parents(".planbox").css("display")=="none"){ continue; } // leave deleted ap rows

					if($("#"+elm+i).attr("type") == "checkbox"){
						var t1 = $("#"+elm+i).is(":checked");
						if(typeof(t)=="undefined"){t=false;}
						$("#"+elm+i).prop("checked",t).each(function(){
								if(elm=="no_change_"||elm=="elem_resolve"){
									setAPClr(i,this);
								}
							});

					}else{
						var t1 = $("#"+elm+i).val();
						if(typeof(t1)=="undefined"){ t1=""; }
						$("#"+elm+i).val(""+t).triggerHandler("keyup");

						if(elm == "elem_plan"){
							cft1 = $("#"+elm+i).attr("data-elem_CFPlan");
							$("#"+elm+i).attr("data-elem_CFPlan",cft);
						}else if(elm == "el_amst_site"){
							cft1 = $("#"+elm+i).parent().hasClass("hidden");
							if(cft){
								$("#"+elm+i).parent().addClass("hidden");
								$("#elem_assessment"+i).parent().removeClass("col-sm-11").addClass("col-sm-12");
							}else{
								$("#"+elm+i).parent().removeClass("hidden");
								$("#elem_assessment"+i).parent().removeClass("col-sm-12").addClass("col-sm-11");
							}
						}else if(elm == "elem_assessment_dxcode"){
							cft1 = $("#"+elm+i).attr("data-dxid");
							$("#"+elm+i).attr("data-dxid",cft);
						}
					}

					t=t1;
					cft=cft1;
				}
			}
		}
	}
}
var st_ap_adjt = ed_ap_adjt = 0;
function ap_adjt(num, obj){
	//Only Physician can do
	var tmp_u_t=(user_type=="1"||(ssFollowPhy!="" && ssFollowPhy!="0")) ? "1" : "0";
	if((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1")) || (tmp_u_t != "1")){ return;}

	if(st_ap_adjt == 0){
		st_ap_adjt = num;
		obj.style.color = "red";
		return;
	}else {
		ed_ap_adjt = num;
	}
	if((st_ap_adjt != 0) && (ed_ap_adjt != 0)){
		$("#assessplan .ap_num").css("color","white");
		ap_rowAdj(st_ap_adjt,ed_ap_adjt);
		isFormChanged=1;
		//Reset global
		st_ap_adjt = ed_ap_adjt = 0;
	}
}

//ICD 10 show dx code modifier --
function showICD10CodeModifier(obj){
	if($.trim(obj.value)==""){return;}
	var icd10 = $("#hid_icd10").val();
	if(icd10!=1){ return false; }

	//check diabetese ass
	if(isDiabetesAssess(obj.value)){
		//show diabetese pop up
		if(typeof(obj.onchange)=="function"){obj.onchange();}
		if(typeof(obj.onblur)=="function"){obj.onblur();}
		return;
	}

	var dxid = obj.id.replace("elem_assessment", "elem_assessment_dxcode");
	if(""+$("#"+dxid).val().length<=0){ return; }
	//alert("33");
	$("#"+dxid).data("checkfullcode",1);
	$("#"+dxid).autocomplete("search");
	//$("#"+dxid)[0].focus();
	//obj.blur();
	//$("#"+dxid).trigger("keydown");
}
//ICD 10 show dx code modifier --

//ICD 10 check diabetese assessment
function isDiabetesAssess(str, fstp){
	str=$.trim(str);
	//explode by semi colon
	if(fstp=="2"){
		var ar_str = str.split(";");
		if(ar_str.length>0){ str = $.trim(ar_str[0]);  }
	}

	var ret=str.match(/^((diabetes|diabetes\s+mellitus|dm)\s+type\s+[1|2])/gi); /*if(ret){ if(str.match(/(with|No)/gi)){ret=0;}}*/  str=str.toLowerCase();    if(str=="diabetes 1"||str=="diabetes 2"||str=="dm 1"||str=="dm 2"||str=="diabetes type 1 no retinopathy"||str=="diabetes type 2 no retinopathy"){ ret=str; } /*console.log(ret);*/	return ret;
}

var arrScrlHgt_setTaPlanHgt=[];
function setTaPlanHgt(num, obj){

	//stop in safari
	if(!isiPad && navigator.userAgent.indexOf("Safari") > -1 && navigator.userAgent.indexOf("Chrome") == -1){return;}

	var num2 = ""+num;
	if(num == "elem_consult_reason" || num == "commentsForPatient" || num == "elem_notes" || num == "elem_transition_reason" || num == "elem_transition_notes"  || num2.indexOf("elem_visMrDesc")!=-1 || num2.indexOf("elem_visPcDesc")!=-1 ){
		var x = $("#"+num)[0].scrollHeight;
		var y = (typeof(x)!="undefined" && x>20) ? x : 20;
		$("#"+num).attr("style", "height: "+y+"px !important");
		return;
	}


	if((num == 0) && (typeof obj != "undefined")){
		num = obj.id.replace("elem_plan", "");
	}

	var objAsses = gebi("elem_assessment"+num);
	var objAssesDxCode = gebi("elem_assessment_dxcode"+num);
	var objPlan = gebi("elem_plan"+num);
	var tdA = gebi("td_assessment"+num);
	var tdP = gebi("td_plan"+num);

	//
	if(typeof(arrScrlHgt_setTaPlanHgt[num])=="undefined"){
		arrScrlHgt_setTaPlanHgt[num]=[];
	}


	if(objAsses){
		var tmp1=(typeof(arrScrlHgt_setTaPlanHgt[num][0])!="undefined"&&arrScrlHgt_setTaPlanHgt[num][0]>18) ? arrScrlHgt_setTaPlanHgt[num][0] : 18;
		var tmp = (typeof(objAsses.scrollHeight) != "undefined" && objAsses.scrollHeight > 18 ) ? (objAsses.scrollHeight) : tmp1;
		if(tmp){

			if (tmp1 < tmp && tmp-tmp1 >= 8)
			{
				objAsses.style.maxHeight =objAsses.style.minHeight = objAsses.style.height = tmp+"px";
				arrScrlHgt_setTaPlanHgt[num][0] = tmp1 = tmp;
				$(objAsses).prop("style", "height: "+tmp+"px !important");
			}
		}
	}

	if(objAssesDxCode){
		//addnewline
		objAssesDxCode.value = $.trim(""+objAssesDxCode.value.replace(/\s*/g,""));
		objAssesDxCode.value = ""+objAssesDxCode.value.replace(",",", ");
		//alert(objAssesDxCode.value);
		var tmp1=(typeof(arrScrlHgt_setTaPlanHgt[num][1])!="undefined"&&arrScrlHgt_setTaPlanHgt[num][1]>18) ? arrScrlHgt_setTaPlanHgt[num][1] : 18;
		var tmp = (typeof(objAssesDxCode.scrollHeight) != "undefined" && objAssesDxCode.scrollHeight > 18 ) ? (objAssesDxCode.scrollHeight) : tmp1;
		if(tmp){

			if (tmp1 < tmp && tmp-tmp1 >= 8) //
			{
				objAssesDxCode.style.maxHeight =objAssesDxCode.style.minHeight = objAssesDxCode.style.height = tmp+"px";
				arrScrlHgt_setTaPlanHgt[num][1] = tmp;
				$(objAssesDxCode).prop("style", "height: "+tmp+"px !important");
			}
		}
	}

	if(objPlan){
		var tmp1=(typeof(arrScrlHgt_setTaPlanHgt[num][2])!="undefined"&&arrScrlHgt_setTaPlanHgt[num][2]>18) ? arrScrlHgt_setTaPlanHgt[num][2] : 18;
		var tmp = (typeof(objPlan.scrollHeight) != "undefined" && objPlan.scrollHeight > 18 ) ? (objPlan.scrollHeight) : tmp1;
		if(tmp){

			if (tmp1 < tmp && tmp-tmp1 >= 8)
			{
				objPlan.style.maxHeight =objPlan.style.minHeight = objPlan.style.height = tmp+"px";
				arrScrlHgt_setTaPlanHgt[num][2] = parseInt(tmp);
				$(objPlan).prop("style", "height: "+tmp+"px !important");
			}
		}
	}

	if(tdA && tdP){
		tdA.style.height= (tmp1+5)+"px";
		tdP.style.height= (tmp1+5)+"px";
	}
}
var strCalledMAses = "";
function checkConsolePlan(obj,num,flgDx){
	var str = $.trim(obj.value);
	//check diabetes assessment if ICD 10 and show appropriate pop up
	if($("#hid_icd10").val()=="1"){
		if(isDiabetesAssess(str,2)){
			if($("#no_change_"+num).prop("checked")==false){ //check NE before DM pop up
				//ICD - 10 DM coding
				z_flg_diab_sb="2";
				sb_checkDiabetes();
				//--
				return;
			}
		}else if(str.match(/esotropia/gi) || str.match(/esotropia>>/gi)
			 || str.match(/exotropia/gi) || str.match(/exotropia>>/gi)
			 || str.match(/congenital/gi) || str.match(/congenital>>/gi)){
			icd10_charts_popup(1,'',str,obj.id);
			return;
		}
	}

	//Check for Duplicate assessment
	var strdx = $("#elem_assessment_dxcode"+num).val();
	var flagAs = isAssessAlreadyAdded(str,num,strdx);
	if(flagAs.Flag){
		top.fAlert('Assessment already exists.');
		obj.value="";
		$("#elem_assessment_dxcode"+num).val("");
		$("#elem_apOu"+num).prop("checked", false);
		$("#elem_apOd"+num).prop("checked", false);
		$("#elem_apOs"+num).prop("checked", false);
		return;
	}

	//
	//Check for Dx code
	//Set desc if exists
	if((flgDx != 1) && isDxCorrectCode(str)){
		check4DxDesc(obj,num);
	}else{

		//remove additional text after ; semicolon
		var str = str.split(";");
		str =$.trim(str[0]);

		//Check for console Plan
		//Insert into plan if exists

		var symp=as=pln="";
		var oPlan = gebi("elem_plan"+num);
		var addstr = num+":"+str+",";
		/*
		var len = arrTHAssess.length;
		for(var i=0;i<len;i++){
			if( arrTHAssess[i] == str ){
				symp=""+arrTHSymp[i];
				as=arrTHAssess[i];
				pln=arrTHPlan[i];
				if( oPlan.value.indexOf(arrTHPlan[i]) == -1){
					//Testing --->
					if( (strCalledMAses.indexOf(addstr) == -1) &&
						(checkAP4MultiPlans(str,arrTHPlan[i],"",num))){
						oPlan.value = ($.trim(oPlan.value) != "") ? oPlan.value + "; " + arrTHPlan[i] : ""+arrTHPlan[i]+"";
					}else{
						strCalledMAses += addstr;
					}
				}
				break;
			}
		}
		*/
		var url="requestHandler.php";
		var params = "elem_formAction=GetAPDataofAsmt";
		params += "&srch="+str;
		$.get(url,params,function(data){

				//Testing --->
				//*
				var chk_as = ""+data[0];
				var chk_plan = $.trim(""+data[1]);
				var chk_symp = $.trim(""+data[2]);

				if(chk_as=="false" && chk_plan=="false" && chk_symp=="false"){return;}

				var obj2 = isApPlansMulti(chk_plan,1);
				var lenk = obj2.lenk;
				if(typeof(cn_rem_escp_char)!="undefined"){chk_plan =cn_rem_escp_char(chk_plan);} //chk_plan.replace(/\\'/g,"'");

				if( (strCalledMAses.indexOf(addstr) == -1) && (lenk==false)){ //
					var strPlan=$.trim(oPlan.value);
					if(strPlan != "" ){
						if(strPlan.indexOf(chk_plan)==-1){ strPlan =  strPlan + "; " + chk_plan; }
					}else{
						strPlan=""+chk_plan;
					}
					oPlan.value =  strPlan;
				}else{
					strCalledMAses += addstr;
				}

				// Set FU
				setApPolFU(chk_symp,chk_as,chk_plan);
				//*/

				//check if symptom is typed
				if(chk_symp!=""){
					checkSympTyped(obj,chk_symp,1);
				}

			},"json");
	}
}
//Set Cursor at End
function setCursorAtEnd(){
	var obj = arguments[0];
	var st2 = arguments[1];
	var e = window.event;
	if(e && (typeof arguments[1] == "undefined")){
		obj = e.srcElement;
	}

	if(obj){
		var st=st2||0;
		if(st==0){
			if(obj.value.indexOf("20/") != -1){
				st=3;
			}
			if(obj.name.indexOf("_dxcode") != -1&& $.trim(obj.value)!=""){
				st=obj.value.length;
			}
		}
		//obj.value = obj.value;
		if (obj.setSelectionRange) {
			//alert(""+st);
			//obj.focus();
			obj.setSelectionRange(st,st);

		}else if(obj.createTextRange)
		{

			var FieldRange = obj.createTextRange();
			FieldRange.moveStart('character', st);
			//FieldRange.collapse();
			FieldRange.select();
		}
	}
}

// prev.plan in assessment plan --
function restorePlan_all(){
	//ifchart is finalized than do not fire
	if(finalize_flag==1&&isReviewable==0)return;

	if($("#divPPlans").length<=0){showPrevPlan(); return;}//ipad

	//var i=0;
	$("#divPPlans table tr").each(function(){

		var indx = $(this).find("td:nth-child(1)").html();
		if(typeof(indx)!="undefined" && indx!=""){ indx = indx.replace(/\./, ""); indx=$.trim(indx); }

		if(typeof(indx)!="undefined" && indx!=""){

			var td = gebi("elem_plan"+indx);
			if(td){
				var t = $.trim(td.value); t = t.replace(/[\n\r\u00AD]/g,'');
				var pln = $(this).find("td:nth-child(2)").text();
				pln = $.trim(pln);
				if(typeof(pln)!="undefined" && pln!="" && t==""){					
					td.value = pln;
					if(typeof(td.onkeyup) == "function"){
						td.onkeyup();
					}
				}
			}
		}

		/*
		var td = gebi("elem_plan"+i);
		if(!td||i>50) break;

		var cfPlan = td.getAttribute("data-elem_CFPlan");

		var cfPlan_rem = null;
		cfPlan_rem = td.getAttribute("data-elem_CFPlan_rem");

		//Inc
		i++;

		if(typeof(cfPlan_rem)=="string" && cfPlan_rem=="1"){
			continue; //do not add or show.
		}

		//Test
		var t = $.trim(td.value); t = t.replace(/[\n\r\u00AD]/g,'');
		if(cfPlan && cfPlan != "" && typeof(cfPlan) != "undefined" && t == ""){
			td.value = cfPlan;
			if(typeof(td.onkeyup) == "function"){
				td.onkeyup();
			}
		}
		*/
	});
	$('#divPPlans').hide();
}

function remPrevPlan(i){

	if(typeof(i)!="undefined"){
		//var td = gebi("elem_plan"+i);
		//if(td){
			//var cfPlan = td.getAttribute("data-elem_CFPlan");
			//td.setAttribute("data-elem_CFPlan_rem", "1");
			var td2 = gebi("tdprevplan"+i);
			if(td2){
				td2.innerHTML="";
			}
		//}
	}
	showPrevPlan();
	stopClickBubble();
}

var showPrevPlan_flg;
function showPrevPlan(flg){

	if(typeof(flg)!="undefined"){
		if(flg==1){showPrevPlan_flg = setTimeout(function(){  showPrevPlan(); }, 700);}
		else if(flg==2){clearTimeout(showPrevPlan_flg);}
		return;
	}

	//
	if($("#divPPlans").length>0){ $('#divPPlans').show();  return; }

	var i=1;
	var str = "";
	while(true){

		var td = gebi("elem_plan"+i);
		if(!td||i>50) break;
		var cfPlan = td.getAttribute("data-elem_CFPlan");
		var cfPlan_rem = td.getAttribute("data-elem_CFPlan_rem");
		//alert(typeof(cfPlan_rem));

		if(typeof(cfPlan_rem)=="string" && cfPlan_rem=="1"){
			//continue; //do not add or show.
			cfPlan="";
		}


		//Test
		if(cfPlan!=null && cfPlan != "" && typeof(cfPlan) != "undefined"){
			str += "<tr valign=\"top\"><td>"+i+".</td><td id=\"tdprevplan"+i+"\" >"+cfPlan+"</td><td align=\"center\"  title=\"Remove\" onclick=\"remPrevPlan('"+i+"')\"><span class=\"glyphicon glyphicon-remove\"></span></td></tr>";
		}
		//Inc
		i++;
	}

	//Test
	if(str != ""){
		str = "<table border=\"0\" class=\"table table-bordered table-hover table-striped\">"+str+"</table>";
	}else{
		str = "<span>No Previous Plan</span>";
	}

	//Close
	str = "<div class=\"header\">"+
		"<span id=\"spn_dd_dos\" class=\"pull-left primary form-inline\" ><label for=\"sl_prv_dd_dos\">DOS:</label><select id=\"sl_prv_dd_dos\" class=\"form-control\" onchange=\"load_visit_plans(this)\"><option value=\"\"> -Select- </option></select></span>"+
		"<span class=\"glyphicon glyphicon-remove\" onclick=\"$('#divPPlans').remove();\"></span></div>"+str;

	//Show
	$("#divPPlans").remove();
	var oP =$("#linkPrevPlan").position();
	var tmp = parseInt(oP.top) + 25 ; //$("#divWorkView").scrollTop()+75;
	$("#assessplan").append("<div id='divPPlans'>"+str+"</div>");
	$("#assessplan #divPPlans").css('top',tmp+'px');
	$("#divPPlans").draggable({handle:"div"});

	//Load DOS
	$.get(zPath+"/chart_notes/requestHandler.php?elem_formAction=load_dd_dos", function(d){if(d){ $.each(d, function(k, itm) {  var sl = (k==0) ? "selected" : "";  $("#sl_prv_dd_dos").append(   $('<option '+sl+'></option>').val(itm[0]).html(itm[1])   ); }); } }, "json");
}

//Load plans of previous charts
function load_visit_plans(o){
	var i = $(o).val();
	if(i==""){return;}
	if((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1"))){return;}
	top.show_loading_image('show');
	//
	$.get(zPath+"/chart_notes/requestHandler.php?elem_formAction=load_dos_plans&fid="+i, function(d){top.show_loading_image('hide');
			if(typeof(d)!="undefined" && d!=""){
				$("#divPPlans .header").nextAll().remove();
				if(d=="No Previous Plan"){d = "<span>"+d+"</span>";}
				$(""+d).insertAfter("#divPPlans .header");
			}
		});
}

//Reset the Assessment value
function reset_assessment(fldid){
	if(fldid.indexOf("elem_assessment_dxcode")!=-1){
		var ass_dx_id = fldid;
		var ass_id = fldid.replace("elem_assessment_dxcode","elem_assessment");
		var ass_ou_id = fldid.replace("elem_assessment_dxcode","elem_apOu");
		var ass_od_id = fldid.replace("elem_assessment_dxcode","elem_apOd");
		var ass_os_id = fldid.replace("elem_assessment_dxcode","elem_apOs");
		var pop_ass_id = fldid.replace("elem_assessment_dxcode","pop_elem_assessment");
		var ass_plan_pt_id = fldid.replace("elem_assessment_dxcode","el_pt_ap_id");
		var ass_plan_id = fldid.replace("elem_assessment_dxcode","elem_plan");
		if($("#"+ass_plan_id).val()==""){ $("#"+ass_plan_pt_id).val(''); }
		$("#"+ass_id).val('');
		$("#"+ass_ou_id).attr("checked",false);
		$("#"+ass_od_id).attr("checked",false);
		$("#"+ass_os_id).attr("checked",false);
		$("#elem_ass_comm").val('');
		$("#"+pop_ass_id).val('');
		$("#"+ass_dx_id).val('').removeData("dxid").attr("data-dxid", "");

	}else if(fldid.indexOf("ap_elem_assessment")!=-1){
		var ass_id = fldid.replace("ap_elem_assessment","elem_assessment");
		var ass_dx_id = fldid.replace("ap_elem_assessment","elem_assessment_dxcode");
		var ass_ou_id = fldid.replace("ap_elem_assessment","elem_apOu");
		var ass_od_id = fldid.replace("ap_elem_assessment","elem_apOd");
		var ass_os_id = fldid.replace("ap_elem_assessment","elem_apOs");
		var pop_ass_id = fldid.replace("ap_elem_assessment","pop_elem_assessment");
		var ass_plan_id = fldid.replace("ap_elem_assessment","elem_plan");
		var ass_plan_pt_id = fldid.replace("ap_elem_assessment","el_pt_ap_id");

		$("#"+ass_id).val('');
		$("#"+ass_plan_id).val('');
		$("#"+ass_ou_id).attr("checked",false);
		$("#"+ass_od_id).attr("checked",false);
		$("#"+ass_os_id).attr("checked",false);
		$("#elem_ass_comm").val('');
		$("#"+pop_ass_id).val('');
		$("#"+ass_dx_id).val('').removeData("dxid").attr("data-dxid", "");  $("#"+ass_plan_pt_id).val('');
		$("#"+ass_id).parents(".planbox").hide();
	}else if(fldid.indexOf("elem_assessment")!=-1){
		var ass_id = fldid;
		var ass_dx_id = fldid.replace("elem_assessment","elem_assessment_dxcode");
		var ass_ou_id = fldid.replace("elem_assessment","elem_apOu");
		var ass_od_id = fldid.replace("elem_assessment","elem_apOd");
		var ass_os_id = fldid.replace("elem_assessment","elem_apOs");
		var pop_ass_id = fldid.replace("elem_assessment","pop_elem_assessment");
		var ass_plan_pt_id = fldid.replace("elem_assessment","el_pt_ap_id");
		var ass_plan_id = fldid.replace("elem_assessment","elem_plan");
		if($("#"+ass_plan_id).val()==""){ $("#"+ass_plan_pt_id).val(''); }

		$("#"+ass_id).val('');
		$("#"+ass_ou_id).attr("checked",false);
		$("#"+ass_od_id).attr("checked",false);
		$("#"+ass_os_id).attr("checked",false);
		$("#elem_ass_comm").val('');
		$("#"+pop_ass_id).val('');
		$("#"+ass_dx_id).val('').removeData("dxid").attr("data-dxid", "");
	}else if(fldid.indexOf("elem_dxCode")!=-1){
		$("#"+fldid).val('');
		$("#elem_ass_comm").val('');
		//remove dxid and title
		$("#"+fldid).removeData("dxid");
		renew_title($("#"+fldid)[0], "");
	}
	$("#dialogLSS td.highlight").removeClass('highlight');
	$("#dialogLSS").dialog( "close" );
}

function getForthAssess(flg)
{
	var objTbl = $("#assessplan .planbox");
	var lastRow = objTbl.length;
	var tmp = lastRow;

	var objAssess = gebi("elem_assessment"+tmp);
	var objAssessDx = gebi("elem_assessment_dxcode"+tmp);
	var objPlan = gebi("elem_plan"+tmp);

	if( ((objAssess.value != "") || (objAssessDx.value != "") || (objPlan.value != "")) || (typeof(flg)!="undefined" && flg=="1") ){
		addAssessPlanRow("","","");
	}
}



function makeAssessmentPure(assess){
	var retDx = "";
	assess = $.trim(assess);
	if(assess != ""){
		var indx = assess.indexOf(";"); // before comments
		if(indx != -1){
			assess = assess.substring(0,indx);
		}

		indx=-1;
		var chkEye = "";
		indx = assess.lastIndexOf("-"); // check Eye assoc
		if(indx != -1){
			var ptrn="\\-\\s*(OD|OS|OU)\\s*";
			var reg = new RegExp(ptrn,"g");
			if(assess.match(reg)){
				chkEye = assess.match(new RegExp("(OD|OS|OU)","g"));
				assess = assess.replace(reg,"");
			}
		}

		//paranthesis dx
		indx = -1;
		indx = assess.lastIndexOf("(");
		if(indx != -1){
			var tmp = $.trim(assess.substring(indx));
			tmp = $.trim(tmp.replace(/[\\(\\)]/g, ""));
			//check multiple dx

			if(tmp.indexOf(",") != -1){
				var arr = tmp.split(",");
			}else{
				var arr = new Array(tmp);
			}
			var flag = true;
			var len = arr.length;
			for(var i=0;i<len;i++)
			{
				if( (typeof arr[i] != "undefined")  ) {
					if(! isDxCorrectCode(arr[i])){
						flag = false;
						break;
					}
				}
			}

			if(flag == true){
				assess = $.trim(assess.substring(0,indx));
				retDx = tmp;
			}
		}
	}
	return { "assess": assess, "dx" : retDx, "eye" : chkEye};
}

function isDxAlreadyAdded(dx, dxid){
	dx=$.trim(dx); dxid = $.trim(dxid);
	if(typeof(dx)=="undefined" || dx == ""){return 1;} // if empty return 1
	var ret =0;
	var lastRow = $("#assessplan .planbox").length;
	for(var i=1;i<=lastRow;i++){
		var oDx = gebi("elem_assessment_dxcode"+i);
		if((oDx != null)){
			var chk = $.trim(oDx.value);
			if(chk != ""){
				var chkid = $(oDx).data("dxid");
				if(typeof(chkid)=="undefined"){ chkid=""; }
				var chk2 = chk.substring(0, chk.length-1)+"-";
				var chk3 = chk.substring(0, chk.length-2)+"--";
				var chk4 = chk.substring(0, chk.length-3)+"-x-";
				if(chk == dx || chk.indexOf(dx)!=-1 || dx==chk2 || dx==chk3 || dx==chk4){

					if(dxid!="" && dxid!=chkid){
						//
					}else{
						ret =1;
						break;
					}
				}
			}
		}
	}
	return ret;
}
function isAssessAlreadyAdded(assessment, asnum, asdx, asdxid){
	var curNum = (typeof asnum != "undefined" && asnum!="") ? asnum : null;
	var curDx = (typeof asdx != "undefined" && asdx!="") ? asdx : "";
	curDx = $.trim(curDx.replace(/\s/g,""));
	asdxid = (typeof asdxid != "undefined" && asdxid!="") ? $.trim(asdxid) : "" ;

	var oAssessment =  makeAssessmentPure(assessment);
	var assessment = oAssessment.assess;
	assessment=$.trim(assessment);
	//var oAssessTbl = gebi("tblAssessment");
	//var oPlanTbl = gebi("tblPlan");
	var lastRow = $("#assessplan .planbox").length;
	var flag = false;
	var emptyIndx = 0;
	var addedIndx = 0;
	for(var i=1;i<=lastRow;i++){
		var oAssess = gebi("elem_assessment"+i);
		if((oAssess != null)){
			var oDx = gebi("elem_assessment_dxcode"+i);
			if($.trim(oAssess.value) != ""){
				//chk
				//makeAssessmentPure(oAssess.value);
				//alert(""+oAssess.value+"\n"+assessment+"\ncurNum: "+curNum+"\ni:"+i);
				//chk

				var otemp = makeAssessmentPure(oAssess.value);
				var temp = $.trim(otemp.assess);
				var tmpdx = oDx.value; tmpdx = $.trim(tmpdx.replace(/\s/g,""));
				var tmpdxid = $(oDx).data("dxid");
				if(typeof(tmpdxid)=="undefined"){ tmpdxid=""; }
				tmpdxid = $.trim(tmpdxid);

				if((temp.toUpperCase() == assessment.toUpperCase()) && ((curNum == null) || (curNum != i))){
					var flg = (curNum != null && (curDx != tmpdx || (tmpdxid!=asdxid))) ? false : true;
					if(flg){
					flag = true;
					addedIndx = i;
					break;
					}
				}else{
					//alert("temp: "+temp+", \nAssess: "+assessment+", \nCurNum: "+curNum+", \ni: "+i+
					//		"\n oAssess.value: "+oAssess.value+",\nassessment: "+assessment);
				}
			}else{
				var oPlan = gebi("elem_plan"+i);
				if( emptyIndx == 0  && $.trim(oPlan.value) == "" && $.trim(oDx.value) == ""){
					emptyIndx = i;
				}
			}
		}
	}

	return {"Flag":flag,"EmptyIndx":emptyIndx,"AddedIndx":addedIndx};
}

function isDxCorrectCode(elemVal)
{
	if($.trim(elemVal) != "")
	{
		//var arrCodes = 	elemVal.split(", ");
		var ptrn = "^[0-9a-zA-Z]{3}((\\.[0-9a-zA-Z\\-]{1,4}))?$";
		var reg = new RegExp(ptrn,"g");
		//for(x in arrCodes)
		//{
			if($.trim(elemVal).match(reg)== null || $.trim(elemVal).match(/[0-9]/) == null)
			{
				return false;
			}
		//}
	}
	return true;
}

function isApPlansMulti(plan,flg){
	var ptrn = "(^\\n|\\n$)";
	plan = regReplace(ptrn,"",plan);
	if(typeof(flg)!="undefined" && flg==1){
		var arrPlans = plan.split("\\n");
	}else{
		var arrPlans = plan.split("\n");
	}
	var lenk = arrPlans.length;
	return ( lenk > 1 ) ? {"lenk":true,"arr":arrPlans} : {"lenk":false};
}

//Get Fu based on symptom and fill in assessment plan -> f/u
function setApPolFU(symp,as,pln,a_n,idOdr,idOdrset,site,sig,fInsrtOrdr){
	if(typeof(symp)=="undefined")symp="";
	if(typeof(as)=="undefined")as="";
	if(typeof(pln)=="undefined")pln="";
	if(typeof(idOdr)=="undefined")idOdr="";
	if(typeof(idOdrset)=="undefined")idOdrset="";
	if(typeof(a_n)=="undefined")a_n="";
	if(typeof(site)=="undefined")site="";
	if(typeof(sig)=="undefined")sig="";
	if(typeof(fInsrtOrdr)=="undefined")fInsrtOrdr="";

	if(typeof(cn_rem_escp_char)!="undefined"){
	if(as!=""){as =""+as; as = cn_rem_escp_char(as); /*as.replace(/\\'/g,"'");*/}
	if(pln!=""){pln =""+pln; pln=cn_rem_escp_char(pln); /*pln.replace(/\\'/g,"'");*/}
	}


	if(symp==""&&as==""&&pln=="")return;
	var url="requestHandler.php";
	var p={"elem_formAction":"setApPolFU",
			"symp":symp,
			"as":as,
			"pln":pln,
			"idOdrset":idOdrset,
			"idOdr":idOdr,
			"a_n":a_n,
			"site":site,
			"sig":sig,
			"fInsrtOrdr":fInsrtOrdr
			};

	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","follow_up");
	$.post(url,p,function(data){
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","follow_up");
		if(data){
			var a,b,c;
			for(var z in data){
				a = $.trim(data[z]['number']);
				b = $.trim(data[z]['time']);
				c = $.trim(data[z]['visit_type']);
				d = $.trim(data[z]['provider']);

				if(typeof(a)=="undefined"||a=="undefined"){a="";}
				if(typeof(b)=="undefined"||b=="undefined"){b="";}
				if(typeof(c)=="undefined"||c=="undefined"){c="";}

				if(a != ""||b != ""||c != ""){
					fu_addOpts(""+a,""+b,""+c, ""+d);
				}
			}
		}
	},"json");
}


//Add new row with assess and plan
function addAssessPlanRow( assessment, plan, eye, dx,ordrids, afill){

	assessment = ((typeof assessment != "undefined") && ($.trim(assessment) != "" ) ) ? ""+assessment : "";
	plan = ((typeof plan != "undefined") && ($.trim(plan) != "" )) ? ""+plan+"" : "";
	dx = ((typeof dx != "undefined") && ($.trim(dx) != "" )) ? ""+dx+"" : "";

	var oAssessTbl = $("#assessplan");
	var lastRow = $("#assessplan .planbox").length;
	var currRowId = lastRow+1;

	//Global
	lenAssess = currRowId;

	//Eye
	var tOu=tOd=tOs="";
	if(eye=="OU"){
		tOu=" checked=\"checked\" ";
	}else if(eye=="OD"){
		tOd=" checked=\"checked\" ";
	}else if(eye=="OS"){
		tOs=" checked=\"checked\" ";
	}


	//--
	var str = ""+
	"<div class=\"planbox\">"+
		// This section is displayed none and it is not in use. this is kept here for js function.
	"	<div class=\"row planchoose ap_eye_removed\" >"+
	"		<div class=\"col-lg-3 col-md-3 col-sm-5 \">"+
	"			<ul>"+
	"			<li class=\"ouc\"><input name=\"elem_apOu[]\" id=\"elem_apOu"+currRowId+"\" type=\"checkbox\" value=\""+currRowId+"\" onclick=\"setApEye(this,0)\"  onfocus=\"setApEye(this,1)\"  /><label for=\"elem_apOu"+currRowId+"\"></label></li>"+
	"			<li class=\"odc\"><input name=\"elem_apOd[]\" id=\"elem_apOd"+currRowId+"\" type=\"checkbox\" value=\""+currRowId+"\" onclick=\"setApEye(this,0)\"  onfocus=\"setApEye(this,1)\"  /><label for=\"elem_apOd"+currRowId+"\"></label></li>"+
	"			<li class=\"osc\"><input name=\"elem_apOs[]\" id=\"elem_apOs"+currRowId+"\" type=\"checkbox\" value=\""+currRowId+"\" onclick=\"setApEye(this,0)\"  onfocus=\"setApEye(this,1)\"   /> <label for=\"elem_apOs"+currRowId+"\"></label></li>"+
	"			</ul>"+
	"		</div>"+
	"	</div>"+
	"	<div class=\"clearfix ap_eye_removed\"></div>"+
		// End

	"	<div class=\"row planform\" >"+
	"		<div class=\"col-lg-1 col-md-1 col-sm-1 col-lg-1-sn col-md-1-sn col-sm-1-sn\" >"+
	"			<div class=\"planchoose\">"+
	"			<div class=\"counter ap_num\" onClick=\"ap_adjt("+currRowId+", this);\" >"+currRowId+"</div>"+
	"			</div>"+
	"		</div>"+
	"		<div class=\"col-lg-1 col-md-1 col-sm-1 col-lg-1-opt col-md-1-opt col-sm-1-opt\" >	"+
	"			<div class=\"planchoose\">"+
	"			<ul class=\"ul_ne\">	"+
	"			<li class=\"ne\"><div class=\"checkboxO\"><input type=\"checkbox\" name=\"elem_apnc[]\" id=\"no_change_"+currRowId+"\" value=\""+currRowId+"\" onClick=\"chkSelection('"+currRowId+"', this);\"  /><label for=\"no_change_"+currRowId+"\">NE</label></div></li>   "+
	"			<li class=\"res\"><div class=\"checkboxO\"><input name=\"elem_apres[]\" id=\"elem_resolve"+currRowId+"\" type=\"checkbox\" value=\""+currRowId+"\" onClick=\"chkSelection('"+currRowId+"', this);\"  /><label for=\"elem_resolve"+currRowId+"\">RES</label></div></li> "+
	"			</ul>"+
	"			</div>"+
	"		</div>"+
	"		<div class=\"col-lg-4 col-md-4 col-sm-7 col-lg-4-as col-md-4-as col-sm-7-as \">"+
	"		<div class=\"row\">"+
	"		<div class=\"col-sm-12\">"+
	"		<textarea name=\"elem_assessment[]\" rows=\"3\" id=\"elem_assessment"+currRowId+"\" onkeyup=\"setTaPlanHgt("+currRowId+");\"  onchange=\"checkConsolePlan(this,'"+currRowId+"');getForthAssess();\" class=\"form-control  \" onclick=\"showICD10CodeModifier(this)\" tabindex=\"1\"   >"+assessment+"</textarea> "+
	"		</div>"+
	"		<div class=\"col-sm-1 hidden \">"+
	"		<select name=\"el_amst_site[]\" id=\"el_amst_site"+currRowId+"\" class=\"form-control \" onchange=\"setApEye(this,2)\"><option></option>"+
	"			<option value=\"OU\"  >OU</option>"+
	"			<option value=\"OD\"  >OD</option>"+
	"			<option value=\"OS\"  >OS</option>"+
	"		</select>"+
	"		</div>"+
	"		</div>"+
	"		</div>"+
	"		<div class=\"col-lg-1 col-md-1 col-sm-2 col-lg-1-dx col-md-1-dx col-sm-2-dx\">"+
	"		<textarea  class=\" form-control dx \" rows=\"3\"  onkeyup=\"setTaPlanHgt("+currRowId+");\" name=\"elem_assessment_dxcode[]\" id=\"elem_assessment_dxcode"+currRowId+"\" tabindex=\"5\" onblur=\"checkDXCodesChart(this);setTaPlanHgt("+currRowId+");getForthAssess();\" onfocus=\"setCursorAtEnd(this);\"    >"+dx+"</textarea> "+
	"		</div>"+
	"		<div class=\"col-lg-4 col-md-4 col-sm-12 col-lg-4-pl col-md-4-pl col-sm-12-pl\">"+
	"		<textarea  name=\"elem_plan[]\" rows=\"3\" id=\"elem_plan"+currRowId+"\" onkeyup=\"setTaPlanHgt("+currRowId+");\" onchange=\"setTaPlanHgt("+currRowId+");\"  data-elem_CFPlan=\"\" class=\"  form-control plantext\" tabindex=\"5\"   >"+plan+"</textarea>"+
	"		</div>"+
	"		<div class=\"col-lg-1 col-md-1 col-sm-1 col-lg-1-x col-md-1-x col-sm-1-x\">	"+
	//"			<div class=\"planchoose\"><div class=\"closebtn\"><img src=\""+zPath+"/../library/images/closerd.png\" alt=\"Delete\" onclick=\"reset_assessment('ap_elem_assessment"+currRowId+"');\" /></div></div>	"+
	"			<span class=\"glyphicon glyphicon-remove\" alt=\"Delete\" onclick=\"reset_assessment('ap_elem_assessment"+currRowId+"');\"></span>"+
	"		</div>"+
	"	</div>"+
	"<input name=\"elem_apConMeds[]\" id=\"elem_apConMeds"+currRowId+"\" type=\"hidden\" value=\"\" /> "+
	"<input name=\"elem_problist_id_assess[]\" id=\"elem_problist_id_assess"+currRowId+"\" type=\"hidden\" value=\"\" /> "+
	"<input name=\"elem_assessment_typeAhead"+currRowId+"\" id=\"elem_assessment_typeAhead"+currRowId+"\"  type=\"hidden\" value=\"\" /> "+
	"<input name=\"el_pt_ap_id[]\" id=\"el_pt_ap_id"+currRowId+"\" type=\"hidden\" value=\"\" />"+
	"</div>"+
	"<div class=\"clearfix\"></div>";
	//--


	/*
	var str = ""+
				"<li>"+
					"<input type=\"checkbox\" name=\"elem_apnc[]\" id=\"no_change_"+currRowId+"\" value=\""+currRowId+"\" "+
							"onClick=\"chkSelection('"+currRowId+"', this);\" "+
						"/>"+
					"<input name=\"elem_apres[]\" id=\"elem_resolve"+currRowId+"\" type=\"checkbox\" value=\""+currRowId+"\" "+
							"onClick=\"chkSelection('"+currRowId+"', this);\" "+
						">"+
					"<label class=\"hand_cur\" onClick=\"ap_adjt("+currRowId+", this);\">"+currRowId+". </label>"+
					"<textarea "+
										"name=\"elem_assessment[]\" id=\"elem_assessment"+currRowId+"\""+
										"onKeyUp=\"setTaPlanHgt("+currRowId+");\""+
										"onChange=\"checkConsolePlan(this,'"+currRowId+"');getForthAssess();\" "+
					" tabindex=\""+(currRowId*5+1)+"\" style=\"width:400px\" onclick=\"showICD10CodeModifier(this)\"  >"+assessment+"</textarea>"+

					"<input name=\"elem_apOu[]\" id=\"elem_apOu"+currRowId+"\" type=\"checkbox\" value=\""+currRowId+"\" "+
								"onclick=\"setApEye(this)\" "+
								"onfocus=\"setApEye(this,1)\" "+
								"tabindex=\""+(currRowId*5+2)+"\" "+tOu+
								"/>"+
					"<input name=\"elem_apOd[]\" id=\"elem_apOd"+currRowId+"\" type=\"checkbox\" value=\""+currRowId+"\" "+
								"onclick=\"setApEye(this)\" "+
								"onfocus=\"setApEye(this,1)\" "+
								"tabindex=\""+(currRowId*5+3)+"\" "+tOd+
								"/>"+
					"<input name=\"elem_apOs[]\" id=\"elem_apOs"+currRowId+"\" type=\"checkbox\" value=\""+currRowId+"\" "+
								"onclick=\"setApEye(this)\" "+
								"onfocus=\"setApEye(this,1)\" "+
								"tabindex=\""+(currRowId*5+4)+"\" "+tOs+
								"/>"+
					"<textarea name=\"elem_assessment_dxcode[]\" id=\"elem_assessment_dxcode"+currRowId+"\"  tabindex=\""+(currRowId*5+5)+"\"  onblur=\"checkDXCodesChart(this);setTaPlanHgt("+currRowId+");getForthAssess();\" onfocus=\"setCursorAtEnd(this);\" >"+dx+"</textarea>"+
					"<textarea name=\"elem_plan[]\" id=\"elem_plan"+currRowId+"\" "+
											"oncontextmenu=\"detectRightClick(this); return false;\" "+
											//"onChange=\"setThisChangeStatus(this);\" "+
											"onchange=\"setTaPlanHgt("+currRowId+");\" "+
											"style=\"width:620px;\" "+
											"onKeyUp=\"setTaPlanHgt("+currRowId+");\" "+
					" tabindex=\""+(currRowId*5+5)+"\" >"+plan+"</textarea>  <span title=\"Remove A&P\" class=\"spnFuDel\"  onclick=\"reset_assessment('ap_elem_assessment"+currRowId+"');\">x</span>"+
					"<input name=\"elem_apConMeds[]\" id=\"elem_apConMeds"+currRowId+"\" type=\"hidden\" value=\"0\" /> "+
					"<input name=\"elem_problist_id_assess[]\" id=\"elem_problist_id_assess"+currRowId+"\" type=\"hidden\" value=\"0\" /> "+
					//"<a  href=\"javascript:void(0);\" onClick=\"planPop('',document.getElementById('"+currRowId+"'));\" class=\"a_oset\" >Rx</a>  "+
					//"<a href=\"javascript:void(0);\" onClick=\"set_order_pop_up("+currRowId+",'')\" class=\"a_oset\" >OSets</a>"+

				"</li>"+
				"";
	*/
	//oAssessTbl.append(str);
	$(str).insertBefore("#assessplan .addbut");
	sb_addTypeAhead();

	//Type Ahead
	//new actb(gebi("elem_assessment"+currRowId),arrTypeAhead.concat(arrTHAssess,arrDxCodeAndDesc));
	//new actb(gebi("elem_plan"+currRowId),arrTypeAhead);
	cn_typeahead();

	//order set id and order id
	if(typeof(ordrids)!="undefined" && ordrids!=""){
		attachOrder2Chart(currRowId, ordrids);
	}

	//autofill if dx not empty
	if(typeof(afill)!="undefined" && afill==1 && dx!=""){
		fill_dx_ases(currRowId,dx);
	}
}

function fill_dx_ases(currRowId,dx,dxid){
	var o = $("#elem_assessment"+currRowId);
	var c = o.val();
	if(dx=="" || o.length<=0 || (typeof(c)!="undefined" && $.trim(c)!="")){return;}
	$.get(zPath+"/chart_notes/requestHandler.php?elem_formAction=getDxAses&dx="+encodeURIComponent(dx)+"&dxid="+dxid,function(d){if(typeof(d)!="undefined" && $.trim(d)!=""){$("#elem_assessment"+currRowId).val(d);}});
}

//Add in assess and plan
	function addAssessOption(assessment,plan, fRem, eye, fRemPlan, dx, ordrids,apnum,dxid){ //Check Multiple Dx
		if(typeof(asmt_sep_multi_dx_code)=="undefined"){ asmt_sep_multi_dx_code=0; }
		if(asmt_sep_multi_dx_code==1 && typeof(dx)!="undefined" && dx!="" && (dx.indexOf(",")!=-1||dx.indexOf(";")!=-1)){
			if(dx.indexOf(",")!=-1){ var ardx = dx.split(","); }else if(dx.indexOf(";")!=-1){ var ardx = dx.split(";"); }
			if(ardx.length>1){ //1 will not come
				var ardxId = (typeof(dxid)!="undefined" && dxid!="") ? dxid.split(",") : [] ;
				for(var x=0;x<ardx.length;x++){
					dxid = (typeof ardxId[x] != "undefined" && $.trim(ardxId[x])!="") ? ardxId[x] : "";
					if(x==0){ addAssessOptionExe(assessment,plan, fRem, eye, fRemPlan, ardx[x], ordrids, apnum,0,dxid); }
					else{ if($.trim(ardx[x])!=""){ addAssessOptionExe("", plan, fRem, eye, fRemPlan, ardx[x],'', apnum,1,dxid); } }
				}
			}
		}else{
			addAssessOptionExe(assessment,plan, fRem, eye, fRemPlan, dx, ordrids,apnum,0,dxid);
		}
	}//test

	function addAssessOptionExe(assessment,plan, fRem, eye, fRemPlan, dx, ordrids, apnum, afill, dxid){

		//check for fill auto
		var flg_auto_fill=0;
		if(typeof(dx)!="undefined" && $.trim(dx)!="" && typeof(afill)!="undefined" && afill==1){
			flg_auto_fill=1;
			//check duplicate dx
			if(isDxAlreadyAdded(dx,dxid)){return;}
		}

		//check for empty assessment and plan
		if(($.trim(assessment)=="undefined"||$.trim(assessment)==""||typeof(assessment)=="undefined") &&
			($.trim(plan)=="undefined"||$.trim(plan)==""||typeof(plan)=="undefined") &&
			(flg_auto_fill==0)){  return; }

		var ar = ["Ou","Od","Os"];
		if(typeof apnum!='undefined' && apnum!=''){var obj ={"Flag":true,"AddedIndx":apnum};}
		else{var obj = isAssessAlreadyAdded(assessment,'',dx,dxid);}

		//Check If already addedd
		if(obj.Flag)
		{
			var oAssess = gebi("elem_assessment"+obj.AddedIndx);
			var oPlan = gebi("elem_plan"+obj.AddedIndx);
			var oDx = gebi("elem_assessment_dxcode"+obj.AddedIndx);
			//remove
			if( ( typeof fRem != "undefined" ) && (fRem == true) ){

				if(oAssess && oPlan){
					oAssess.value = "";
					oPlan.value = "";
					oDx.value = ""; $(oDx).data("dxid", "");
					$("#elem_apOu"+obj.AddedIndx).prop("checked", false);
					$("#elem_apOd"+obj.AddedIndx).prop("checked", false);
					$("#elem_apOs"+obj.AddedIndx).prop("checked", false);
					if(typeof oPlan.onkeyup == "function"){
						oPlan.onkeyup();
					}
				}

			}else{
				//update
				if(oAssess && oPlan){
					//oAssess.value = ""+assessment; // [10/11/2015] assessment from ap removes comments added after semicolon
					// Add comments in Assessment
					var indx = assessment.indexOf(";");
					if(indx!=-1){
						var chk_as = oAssess.value;
						indx=parseInt(indx)+1;
						var as_com = assessment.substr(indx);
						var ar_as_com = as_com.split(",");
						var ar_lids_as=["Right Lower Lid",  "Left Upper Lid",  "Left Lower Lid",  "Right Upper Lid", "Left Eye",  "Right Eye", "Both Eyes"];
						var l = ar_lids_as.length;
						for(var x=0;x<l;x++){
							var t_ad = ""+ar_lids_as[x];
							if(typeof(t_ad)!="undefined" && t_ad!="undefined" && t_ad!=""){
								if(as_com.indexOf(t_ad)!=-1){
									if(chk_as.indexOf(t_ad)==-1){
										if(chk_as.indexOf(";")==-1){  chk_as=chk_as+"; "; }else{ chk_as=chk_as+", "; }
										chk_as = chk_as + t_ad;
									}
								}else{
									if(chk_as.indexOf(t_ad)!=-1){
										chk_as=chk_as.replace(t_ad+", ","");
										chk_as=chk_as.replace(t_ad,"");
										chk_as=$.trim(chk_as);
									}
								}
							}
						}
						chk_as=chk_as.replace(";,",";");
						//
						oAssess.value = chk_as;
					}

					//oPlan.value = ( (typeof plan != "undefined") && ($.trim(plan) != "" ) ) ? ""+plan+"" : "" ;
					if( (typeof plan != "undefined") && ($.trim(plan) != "" )){
						if(typeof(fRemPlan)!="undefined" && fRemPlan==1){

							if($.trim(oPlan.value)!=""){
								//delete plan
								//strip special charater before checking to avoid issues with old data
								var plan_old=plan.replace("\u00AD","");
								var del_from_start = plan.indexOf("\u00AD")!=-1 ? 1 : 0 ;
								if(""+oPlan.value.toLowerCase().indexOf(""+plan.toLowerCase()+"\n")!=-1){
									oPlan.value = oPlan.value.replace(""+plan+"\n","");
								}else if(""+oPlan.value.toLowerCase().indexOf(""+plan_old.toLowerCase()+"\n")!=-1){
									oPlan.value = oPlan.value.replace(""+plan_old+"\n","");
								}else if(""+oPlan.value.toLowerCase().indexOf(""+plan.toLowerCase())!=-1){
									oPlan.value = oPlan.value.replace(""+plan,"");
								}else if(""+oPlan.value.toLowerCase().indexOf(""+plan_old.toLowerCase()+"")!=-1){
									if(del_from_start==1){
										//-- if CPOE Order values comes for deletion, check it is starting string or after new line character or equal string: only then delete.
										if(""+oPlan.value.toLowerCase().indexOf("\n"+plan.toLowerCase()+"")!=-1){
											oPlan.value = oPlan.value.replace(""+plan+"","");
										}else{
											var reg_pln = rgp("^"+plan_old+"\n",'i');
											var mtch = oPlan.value.toLowerCase().match(reg_pln);
											if(mtch){
												oPlan.value = oPlan.value.replace(reg_pln, "");
											}else{
												if(oPlan.value.toLowerCase() == plan_old.toLowerCase()){
													oPlan.value = oPlan.value.replace(plan_old, "");
												}
											}
										}
										//--
									}
									else{oPlan.value = oPlan.value.replace(""+plan_old+"","");}
								}
							}

						}else{

							//strip special charater before checking to avoid issues with old data
							plan=plan.replace("\u00AD","");
							if(oPlan.value.indexOf(plan)==-1){
								if(typeof(oPlan.value)!="undefined" && oPlan.value!=""){
									oPlan.value +=  "\n";
								}
								oPlan.value +=  ""+plan+"\u00AD";
							}
						}
					}else{
						oPlan.value += "";
					}

					//trim
					oPlan.value = $.trim(oPlan.value);
					oPlan.value = oPlan.value.replace(/^\n+|\n+$|^\u00AD+|^\r+|\r+$/g, ""); //remove leading/trailing line breaks

					if(typeof dx != "undefined" && $.trim(dx) != ""){
						oDx.value = ""+dx;
						$(oDx).data("dxid",dxid);
						if(dx.indexOf("-")!=-1){ $(oDx).addClass("mandatory"); show_asmt_site($(oDx),dx);}else{$(oDx).triggerHandler("blur");}
					}

					if(typeof oPlan.onkeyup == "function"){
						oPlan.onkeyup();
					}

					//order set id and order id
					if(typeof(ordrids)!="undefined" && ordrids!=""){
						attachOrder2Chart(obj.AddedIndx, ordrids);
					}

				}
			}

			//Eye
			//get Site Based on lids
			var teye="";
			if(eye.indexOf("RLL")!=-1||eye.indexOf("RUL")!=-1){ teye="OD"; }
			if(eye.indexOf("LLL")!=-1||eye.indexOf("LUL")!=-1){
				if(eye.indexOf("RLL")!=-1||eye.indexOf("RUL")!=-1){teye="OU";}
				else{teye="OS"; }
			}
			if(teye!=""){ eye = teye; }
			//--

			for(var x in ar){
				if(eye=="def")continue;
				var oEye = gebi("elem_ap"+ar[x]+obj.AddedIndx);
				if(oEye){
					if(oAssess.value!="" && ar[x].toUpperCase()==eye.toUpperCase()){
						oEye.checked = true;
					}else{
						oEye.checked = false;
					}
				}
			}

			getForthAssess();
			return;
		}

		if(obj.EmptyIndx != 0){
			var oAssess = gebi("elem_assessment"+obj.EmptyIndx);
			var oPlan = gebi("elem_plan"+obj.EmptyIndx);
			var oDx = gebi("elem_assessment_dxcode"+obj.EmptyIndx);

			if(oAssess && oPlan){
				oAssess.value = assessment;
				oPlan.value = ((typeof plan != "undefined") && ($.trim(plan) != "")) ? plan+"" : "";

				if(typeof oPlan.onkeyup == "function"){
					oPlan.onkeyup();
				}

				//Eye
				//get Site Based on lids
				var teye="";
				if(eye.indexOf("RLL")!=-1||eye.indexOf("RUL")!=-1){ teye="OD"; }
				if(eye.indexOf("LLL")!=-1||eye.indexOf("LUL")!=-1){
					if(eye.indexOf("RLL")!=-1||eye.indexOf("RUL")!=-1){teye="OU";}
					else{teye="OS"; }
				}
				if(teye!=""){ eye = teye; }
				//--
				for(var x in ar){
					var oEye = gebi("elem_ap"+ar[x]+obj.EmptyIndx);
					if(oEye){
						if(ar[x].toUpperCase()==eye.toUpperCase()){
							oEye.checked = true;
						}else{
							oEye.checked = false;
						}
					}
				}

				//
				if((typeof dx != "undefined") && ($.trim(dx) != "")){
					dx = dx+"";
					dxid = ((typeof dxid != "undefined") && ($.trim(dxid) != "")) ? dxid : "";
				}else{
					dx = "";
					dxid = "";
				}

				oDx.value = dx;
				$(oDx).data("dxid",dxid);

				if(typeof oDx.onblur == "function"){
					if(dx.indexOf("-")!=-1){ $(oDx).addClass("mandatory"); show_asmt_site($(oDx),dx);}else{$(oDx).triggerHandler("blur");}
				}
				//order set id and order id
				if(typeof(ordrids)!="undefined" && ordrids!=""){
					attachOrder2Chart(obj.EmptyIndx, ordrids);
				}

				//autofill if dx not empty
				if(typeof(afill)!="undefined" && afill==1 && dx!=""){
					fill_dx_ases(obj.EmptyIndx,dx,dxid);
				}

				//display
				$(oAssess).parents(".planbox").show();
			}

		}else{
			addAssessPlanRow( assessment, plan, eye, dx, ordrids, afill); //Add
		}
		getForthAssess();
	}

// Show Multi AP ------

function showMultiAp_exe(val,num){
	if(!num){return;}
	var oDiv = document.getElementById("div_plan_ap_show_2");
	if(oDiv){

		if(val == 1){
			//Get selected AP and insert
			var oIfrm = top.fmain;
			var flagNum = false;
			var strAPExist="";
			var oAps = document.getElementsByName("elem_apAs[]");
			var lenAps = (oAps != null) ? oAps.length : 0 ;
			for(var i=0;i<lenAps;i++){
				if(oAps[i].checked == true){

					var strAssess=""+oAps[i].value;
					var strPlan="";
					var oPlans = document.getElementsByName("elem_apPlan_"+i+"[]");
					var lenPlans = (oPlans != null) ? oPlans.length : 0 ;
					for(var j=0;j<lenPlans;j++){
						if(oPlans[j].checked){
								strPlan += (strPlan != "") ? "\n" : "";
								strPlan += ""+oPlans[j].value;
						}
					}

					//Set in AP
					var oChkAses = isAssessAlreadyAdded(strAssess);
					if(oChkAses.Flag==false){
						if(flagNum == false){
							flagNum = true;
							var oTmpAsses = oIfrm.document.getElementById("elem_assessment"+num);
							var oTmpPlan = oIfrm.document.getElementById("elem_plan"+num);
							if(oTmpAsses){
								oTmpAsses.value = ""+strAssess;
							}
							if(oTmpPlan){
								oTmpPlan.value = ""+strPlan;
								if(typeof oTmpPlan.onchange == "function"){
									oTmpPlan.onchange();
								}
								if(typeof oTmpPlan.onkeyup == "function"){
									oTmpPlan.onkeyup();
								}
							}

						}else{
							//Insert more
							oIfrm.addAssessOption(strAssess,strPlan);
						}
					}else{
						strAPExist+="<br/>  -"+strAssess+"";
					}
				}
			}

			if(strAPExist!=""){
				top.fAlert("Following assessments are already exits:--<br/>"+strAPExist);
			}

			//--- Code For Order set Associate -----------
			var orderSetObj = document.getElementsByName("elem_order_set[]");
			var order_set_val_arr = Array();
			for(o=0,oi=0;o<orderSetObj.length;o++){
				if(orderSetObj[o].checked == true){
					var order_set_id = orderSetObj[o].value;
					var orderString = order_set_id;
					var ordersObj = document.getElementsByName("elem_orders["+order_set_id+"][]");
					var order_id_arr = Array();
					for(or=0;or<ordersObj.length;or++){
						if(ordersObj[or].checked == true){
							order_id_arr[or] = ordersObj[or].value;
						}
						else{
							order_id_arr[or] = '';
						}
					}
					var order_id_str = order_id_arr.join(', ');
					if(order_id_str != ''){
						orderString += '__'+order_id_str;
					}

					var orders_options_obj = document.getElementsByName("elem_orders_options["+order_set_id+"][]");
					var orders_options_arr = Array();
					for(or=0;or<orders_options_obj.length;or++){
						if(orders_options_obj[or].checked == true){
							orders_options_arr[or] = orders_options_obj[or].value;
						}
						else{
							orders_options_arr[or] = '';
						}
					}

					var orders_options_str = orders_options_arr.join(', ');

					if(orders_options_arr != ''){
						orderString += '__'+orders_options_arr;
					}

					order_set_val_arr[oi] = orderString;
					oi++;
				}
			}
			var order_set_val_str = order_set_val_arr.join('---');

			//--- SAVE SINGLE ORDER SET ---
			//--- GET XML OBJECT FOR AJAX FUNCTION --

			/** To be enable Later --
			var url = "saveOrderSets.php";
			params = "order_set_val="+order_set_val_str;
			params += "&plan_num="+num;
			$.post(url, params, function(){
					var iframeObj = top.fmain;
					if(content){
						var elem_val = iframeObj.document.getElementById("elem_plan"+num).value;
						var elem_val_arr = elem_val.split(';');

						content += ' ; '+elem_val_arr[elem_val_arr.length-1];

						iframeObj.document.getElementById("elem_plan"+num).value = content;
						iframeObj.setTaPlanHgt(num);
					}
				});
			/** To be enable Later --**/

		}
		//Close
		oDiv.style.display = "none";
	}
}

function showMultiAp(str,num){
	$("#div_plan_ap_show_2").remove();
	var str1 = "<div id=\"div_plan_ap_show_2\">"+
			   //"<label style=\"background-color:grey;color:white;\">"+"Assessment and Plan"+"</label><br/>"+
			   "<div>"+str+"</div>"+
			   "<input type=\"button\" class=\"dff_button\" id=\"btnSMP_ok\" onClick=\"showMultiAp_exe('1',"+num+")\" value=\"OK\"/> "+
			   "<input type=\"button\" class=\"dff_button\" id=\"btnSMP_close\" onClick=\"showMultiAp_exe('0',"+num+")\" value=\"Close\"/>"+
			   "</div>";
	$("body").append(""+str1);
	$("#div_plan_ap_show_2").show();
}

//Get desc of dx code
function check4DxDesc(obj,num){

	//Checks
	var str = "";
	if(obj && (typeof obj.value != "undefined")){
		str = obj.value;
	}
	if(str == "" || num == ""){
		return;
	}

	var oElemPlan = gebi("elem_plan"+num);
	if(oElemPlan == null){
		return;
	}

	//Define Var -----

	var url = zPath+"/chart_notes/requestHandler.php";
	var params = "elem_formAction=getDxAP";
	params += "&elem_dxCode="+str;
	//-------------------------------------------
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1",""+obj.id);

	$.post(url,params,
		function(data){
		//------  processing after connection   ----------
			//console.log(data);
			//document.write("Check: "+xmlHttp.responseText);
			//alert("Check: "+data);
			//alert(xmlHttp.responseText);
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0",""+obj.id);
			var xmlDoc = data;
			var str = "";
			var cPlans = 0;
			var tmpAssess ="";
			var tmpPlan ="";
			var oAllAp = xmlDoc.getElementsByTagName("allap")[0];
			var oAps = ( oAllAp != null ) ? oAllAp.childNodes : null ;
			var lenAps = ( oAps != null ) ? oAps.length : 0;
			var chk = false;
			for(var i=0;i<lenAps;i++){
				var oData = oAps[i];
				if(oData.firstChild){
					if(oData.nodeName == "ap"){
						str += "<tr valign=\"top\" align=\"left\" >";
						var oApCn = oData.childNodes;
						var lenApcn = (oApCn != null) ? oApCn.length : 0;
						for(var k=0;k<lenApcn;k++){
							var ofData = oApCn[k];
							if(ofData.firstChild){
								if(ofData.nodeName == "ases"){
									str += "<td colspan='2'><input type=\"checkbox\" name=\"elem_apAs[]\" value=\""+ofData.firstChild.data+"\">"+ofData.firstChild.data+"</td>";
									tmpAssess = ""+ofData.firstChild.data;

								}else if(ofData.nodeName == "plans"){

									var strPlan = "<table>";
									var oPlans = (ofData != null) ? ofData.childNodes : null;
									var lenPlans = (oPlans != null) ? oPlans.length : 0;
									for(var j=0;j<lenPlans;j++){
										var oPlan = oPlans[j];
										if(oPlan.firstChild){
											//str += " - Plans: "+oPlan.firstChild.data+",";
											strPlan += "<tr><td><input type=\"checkbox\" name=\"elem_apPlan_"+i+"[]\" value=\""+oPlan.firstChild.data+"\">"+oPlan.firstChild.data+"</td></tr>";
											tmpPlan = ""+oPlan.firstChild.data;
											cPlans++;
										}
									}
									strPlan +="</table>";
									//
									str += "<td>"+strPlan+"</td>";
								}
							}
						}
						str += "</tr>";
					}
					//---- For Order set -----
					if(oData.nodeName == "order_dx"){
						if(chk == false){
							chk = true;
							str += "<tr class=\"txt_10b\"><td colspan=\"3\">Order set associate with Assessment</td></tr>";
							str += "<tr class=\"txt_10b\">";
							str += "<td width='100'>Order set</td>";
							str += "<td width='100'>Orders</td>";
							str += "<td width='100'>Order set options</td>";
							str += "</tr>";
						}
						str += "<tr valign=\"top\">";
						var oApCn = oData.childNodes;
						var lenApcn = (oApCn != null) ? oApCn.length : 0;
						for(var k=0;k<lenApcn;k++){
							var ofData = oApCn[k];
							if(ofData.firstChild){
								if(ofData.nodeName == "order_set_id"){
									var order_set_id = ofData.firstChild.data;
								}
								else if(ofData.nodeName == "order_set"){
									str += "<td width='100'><input type=\"checkbox\" name=\"elem_order_set[]\" value=\""+order_set_id+"\" checked=\"checked\">"+ofData.firstChild.data+"</td>";
									tmpAssess = ""+ofData.firstChild.data;
								}
								else if(ofData.nodeName == "order"){
									var strPlan = "<table>";
									var oPlans = (ofData != null) ? ofData.childNodes : null;
									var lenPlans = (oPlans != null) ? oPlans.length : 0;
									for(var j=0;j<lenPlans;j++){
										var oPlan = oPlans[j];
										if(oPlan.firstChild){
											if(oPlan.nodeName == "orders_id"){
												var order_id = oPlan.firstChild.data;
											}
											else{
												strPlan += "<tr><td><input type=\"checkbox\" name=\"elem_orders["+order_set_id+"][]\" value=\""+order_id+"\" checked=\"checked\">"+oPlan.firstChild.data+"</td></tr>";
												tmpPlan = ""+oPlan.firstChild.data;
												cPlans++;
											}
										}
									}
									strPlan +="</table>";
									str += "<td width='100'>"+strPlan+"</td>";
								}
								else if(ofData.nodeName == "set_options"){
									var strPlan = "<table width='100'>";
									var oPlans = (ofData != null) ? ofData.childNodes : null;
									var lenPlans = (oPlans != null) ? oPlans.length : 0;
									for(var j=0;j<lenPlans;j++){
										var oPlan = oPlans[j];
										if(oPlan.firstChild){
											if(oPlan.nodeName == "order_set_options"){
												strPlan += "<tr><td><input type=\"checkbox\" name=\"elem_orders_options["+order_set_id+"][]\" value=\""+oPlan.firstChild.data+"\" checked=\"checked\">"+oPlan.firstChild.data+"</td></tr>";
												cPlans++;
											}
										}
									}
									strPlan +="</table>";
									str += "<td width='100'>"+strPlan+"</td>";
								}
							}
						}
						str += "</tr>";
					}
				}
			}

			//Set
			if(tmpAssess != ""){
				if(cPlans > 1){
					//Show Pop
					str = "<table width=\"100%\" border=\"0\">"+
							"<tr><td colspan='2'><b>Assessment</b></td><td><b>Plan</b></td></tr>"+
							str+
						  "</table>";

					var ctop=top;
					if(top.fmain){
						ctop = top.fmain;
					}

					ctop.showMultiAp(str,num);

				}else{
					//Set AP
					//alert(tmpAssess +"\n-"+ tmpPlan);
					obj.value = ""+tmpAssess;
					oElemPlan.value = ""+tmpPlan;
				}
			}

			/*
			var dxdesc = xmlHttp.responseText;
			obj.value = dxdesc;
			checkConsolePlan(obj,num,1);
			*/

		//------  processing done --------------------------
		},"xml");
}

//After Save:: multiple
function setAssessOption_v2(indx,fRem){

	if(indx == "1"){	//insert asssessments selected

		if($(":checked[id*=el_chk_as_]").length>0){

			$(":checked[id*=el_chk_as_]").each(function(indx){
					var x = this.id;
					var y = x.replace(/el_chk_as_/,"el_chk_pl_");
					var as = ""+this.value;
					var pln="";
					$(":checked[id*="+y+"]").each(function(indx2){
							pln+=""+this.value+"\n";
						});
					var z = x.replace(/el_chk_as_/,"el_td_dx_");
					var dx = $.trim(""+$("#"+z).html());
					var dxid = 	$("#"+z).data("dxid");
					var z1 = x.replace(/el_chk_as_/,"el_chk_site_");
					var site = $.trim(""+$("#"+z1).val());
					site=site||"";
					var to_do_id=$(this).data("to_do_id");
					//alert(x+" - "+as+" - "+pln+" - "+dx);
					if(as!=""||pln!=""){
						addAssessOption(as,""+pln,fRem,site,"",dx,'','',dxid);

						if(typeof(to_do_id)!="undefined" && to_do_id!=""){ setApPolFU("to_do_id", to_do_id); }
					}
				});

		}else{
			top.fAlert("Please select any assessment!");
			return;
		}

	}

	hide_modal("div_sel_assess_for_symp");
}//Pending to do

//After Save exam: add assessments
var setAssessOption_c=0; //counter
function setAssessOption(symp,fRem,sEye,flgPop,cnslid){
	if(typeof(symp)=="undefined"){symp="";}
	if(typeof(cnslid)=="undefined"){cnslid="";}
	if((symp && symp!="") || (cnslid && cnslid!="")){
		var symp1 = ""+symp;
		var icd_code_10 = (""+$("#hid_icd10").val()=="1") ? "1" : "0";
		var dos=$("#elem_dos").val();

		//*
		var url = zPath+"/chart_notes/requestHandler.php?elem_formAction=GetAssessOption&symp="+encodeURIComponent(symp)+"&icd_code_10="+icd_code_10+"&eye="+sEye+"&cnslid="+cnslid+"&dos="+dos;
		$.get(url,
				function(data){
					//alert("Pending to work: make menu to select assessment: "+data.length);
					//return;

					if(data && data.length>0){

						if(data.length>1){
							//show menu to select assessment : R7
							var symp_var = symp.replace(/\W+/g,"");///make alphanumeric

							var str_sel_htm = "";
							for(var ww in data){
								if(data[ww].assess || data[ww].plan){
									var tAssess = "",tPlan = "",tDx = "",tDxId = "",tpu = "",tapsite = "",tto_do_id = "", tdxas="";
									tAssess = ""+$.trim(data[ww].assess);
									tPlan = ""+$.trim(data[ww].plan);
									obj2 = isApPlansMulti(tPlan);
									tDx = ""+$.trim(data[ww].dxcode);
									tDxId = ""+$.trim(data[ww].dxId);
									tpu = data[ww].pu||"";
									tapsite = ""+$.trim(data[ww].ap_site);
									if(tapsite==""){ tapsite=""+sEye; }
									tto_do_id = ""+$.trim(data[ww].to_do_id);
									tdxas = $.trim(data[ww].asmt_com);
									if(typeof(tdxas)!="undefined" &&  tdxas!=""){
										tAssess = tAssess + "; "+	tdxas;
									}

									if(tpu!=""){
									//
									symp_var = symp_var+setAssessOption_c;
									setAssessOption_c++;

									//
									if(obj2.lenk == true){
										//tPlan = ""+tPlan.replace(/\n/g,"<br/>");
										tPlan = "";
										for(var zx in obj2.arr){
											var t = $.trim(obj2.arr[zx]);
											if(t!=""){
												tPlan += "<input type=\"checkbox\" id=\"el_chk_pl_"+setAssessOption_c+"_"+zx+"\" value=\""+t+"\" ><label for=\"el_chk_pl_"+setAssessOption_c+"_"+zx+"\" >"+t+"</label><br/>";
											}
										}
									}else if(tPlan!=""){
										var t = tPlan;tPlan="";
										tPlan = "<input type=\"checkbox\" id=\"el_chk_pl_"+setAssessOption_c+"_0\" value=\""+t+"\" ><label for=\"el_chk_pl_"+setAssessOption_c+"_0\" >"+t+"</label><br/>";
									}

									str_sel_htm += "<tr valign=\"top\">"+
													"<td><input type=\"checkbox\" id=\"el_chk_as_"+setAssessOption_c+"\" value=\""+tAssess+"\" data-to_do_id=\""+tto_do_id+"\" >"+
													"<label for=\"el_chk_as_"+setAssessOption_c+"\">"+tAssess+"</label>"+
													"<input type=\"hidden\" id=\"el_chk_site_"+setAssessOption_c+"\" value=\""+tapsite+"\" ></td>"+
													"<td id=\"el_td_pl_"+setAssessOption_c+"\">"+tPlan+"</td>"+
													"<td id=\"el_td_dx_"+setAssessOption_c+"\" data-dxid=\""+tDxId+"\">"+tDx+"</td></tr>";
									}else{
										//insert
										var obj2 = isApPlansMulti(tPlan);

										if(obj2.lenk == true || flgPop>1){
											/*Add in plan div and let user decide to add in assessment and plan later*/
											//var obj = {"symp":symp1,"assess":tAssess,"plan":tPlan,"rem":fRem,"site":sEye};
											//addAPplan(obj);
											//
											addAssessOption(tAssess,"",fRem,tapsite,"",tDx,'','',tDxId);
										}else{
											addAssessOption(tAssess,tPlan,fRem,tapsite,"",tDx,'','',tDxId);
										}

										//fu
										if(typeof(tto_do_id)!="undefined"){ setApPolFU("to_do_id", tto_do_id);  }

									}
								}
							}

							//
							if(str_sel_htm!=""){

								var ar_clr_symp = symp.split("!~!");
								var clr_symp=ar_clr_symp[0];

								str_sel_htm= "<div>"+
											//"<div class=\"alert alert-info\">Select assessments and plans for symptom: "+clr_symp+"</div>"+
											"<table class=\"table table-responsive table-striped\" ><tr><td>Assessment</td><td>Plan</td><td>Dx code</td>"+
											"</tr>"+str_sel_htm+"</table>"+
											"</div>";

								if($("#div_sel_assess_for_symp").length<=0){
									//alert("1"+str_sel_htm);
									/*
									str_sel_htm="<div id=\"div_sel_assess_for_symp\" >"+ //style=\"\"
												"<div class=\"con frcb\" >"+
												str_sel_htm+
												"</div>"+
												"<center>"+
												"<input type=\"button\" name=\"el_dsafs_btn_done\" value=\"Done\" onclick=\"setAssessOption_v2(1,'"+fRem+"')\" class=\"dff_button btn btn-success\" /> "+
												"<input type=\"button\" name=\"el_dsafs_btn_cancel\" value=\"Cancel\" onclick=\"setAssessOption_v2(0)\" class=\"dff_button btn btn-danger\" /> "+
												"</center>"+
												"</div>";
									*/

									str_sel_htm="	<div id=\"div_sel_assess_for_symp\" class=\"modal fade\" role=\"dialog\">"+
												"<div class=\"modal-dialog\">"+

												"<div class=\"modal-content\">"+
												"<div class=\"modal-header\">"+
												"<button type=\"button\" class=\"close\" data-dismiss=\"modal\" onclick=\"setAssessOption_v2(0)\">×</button>"+
												"<h4 class=\"modal-title\">Select assessments and plans for symptom: "+clr_symp+"</h4>"+
												"</div>"+
												"<div class=\"modal-body con frcb\">"+
												str_sel_htm+
												"</div>"+
												"<div class=\"modal-footer text-center\">"+
												"<button type=\"button\" class=\"btn btn-success\"  name=\"el_dsafs_btn_done\" onclick=\"setAssessOption_v2(1,'"+fRem+"')\">Done</button>"+
												"<button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\" name=\"el_dsafs_btn_cancel\" onclick=\"setAssessOption_v2(0)\">Cancel</button>"+
												"</div>"+
												"</div>"+

												"</div>"+
												"</div>";

									$("body").append(str_sel_htm);
									$("#div_sel_assess_for_symp").draggable({handle:".modal-header"});
									$("#div_sel_assess_for_symp").modal({show: true, backdrop: false});

									///
									//console.log(str_sel_htm);

								}else{
									//alert("2: "+$( "#div_sel_assess_for_symp" ).children("div").last().length);
									//$("#div_sel_assess_for_symp").append(str_sel_htm);
									$( "#div_sel_assess_for_symp .con" ).children("div").last().after( str_sel_htm );
									$("#div_sel_assess_for_symp").draggable({handle:".modal-header"});
									$("#div_sel_assess_for_symp").modal({show: true, backdrop: false});
								}

								//console.log(str_sel_htm);

								//
								$("input[id*=el_chk_pl_]").bind("click", function(){  $(this).parents("tr").find("input[id*=el_chk_as]").prop("checked", true);  });
							}

						}else{
							// insert assessment
							//
							if(data[0].assess || data[0].plan){
								var tAssess = $.trim(data[0].assess);
								var tPlan = $.trim(data[0].plan);
								var tDx = ""+$.trim(data[0].dxcode);
								var tDxId = ""+$.trim(data[0].dxId);
								tapsite = ""+$.trim(data[0].ap_site);
								if(tapsite==""){ tapsite=""+sEye; }
								var tto_do_id = ""+$.trim(data[0].to_do_id);
								var tdxas = $.trim(data[0].asmt_com);
								if(typeof(tdxas)!="undefined" &&  tdxas!=""){
									if(tAssess.indexOf(";")==-1){ tAssess=tAssess+"; ";  }else{ tAssess=tAssess+", "; }
									tAssess = tAssess + ""+	tdxas;
								}

								var obj2 = isApPlansMulti(tPlan);

								if(obj2.lenk == true || flgPop>1){
									/*Add in plan div and let user decide to add in assessment and plan later*/
									//var obj = {"symp":symp1,"assess":tAssess,"plan":tPlan,"rem":fRem,"site":sEye};
									//addAPplan(obj);
									//
									addAssessOption(tAssess,"",fRem,tapsite,"",tDx,'','',tDxId);
								}else{
									addAssessOption(tAssess,tPlan,fRem,tapsite,"",tDx,'','',tDxId);
								}

								//fu
								if(typeof(tto_do_id)!="undefined"){ setApPolFU("to_do_id", tto_do_id);  }
							}
						}
					}

				},"json");
		//*/
	}
}
//--

//No needed to define as OD/OS/OU options are not working in assessment
function setApEye(o,focs){
	if(focs==2){
		var ind = $(o)[0].id.replace(/el_amst_site/,"");
		var v = $(o).val();
		$("#elem_apOu"+ind+", #elem_apOd"+ind+", #elem_apOs"+ind).prop("checked", false);
		if(v=="OU"){
			$("#elem_apOu"+ind).prop("checked", true);
		}else if(v=="OD"){
			$("#elem_apOd"+ind).prop("checked", true);
		}else if(v=="OS"){
			$("#elem_apOs"+ind).prop("checked", true);
		}
	}
}

//-- Assessment plan ---

//pt discuss and comments
//
function setExamDescChange(obj,v2){
	if(v2 == 1){
		obj.className = obj.className.replace("bgSmoke", "");

	}else{
		var oCIn = gebi(obj.name+"_changed");
		if( oCIn ){
			oCIn.value = "1";
			$(obj).unbind("change",setExamDescChange);
			if($(obj).val()!="" && $(obj).parent().hasClass("bgWhite")==false){
				$(obj).parent().addClass("bgWhite");
			}
		}
	}
}


//----
//--------- FUTURE SCH TESTS APPOINTMENT  --------------------
function add_future_sch_tests_appoints(flg, eid, cnfrm){
	if(typeof(flg)!="undefined" && flg != ""){
		//Save

		if(flg==1){

			if(finalize_flag==1&&isReviewable!=1){top.fAlert("Please open a new chart note"); return;}

			//Check--
			var msg = "";
			//if($.trim($(":input[name=elem_fsta_reason]").val()) == ""){ msg += "\n    - Reason"; } as per sarika
			var test_check_flag = $.trim($(":checked[name=elem_fsta_test_appoint]").val());
			if($.trim(test_check_flag) == ""){ msg += "<li>Test or Appointment</li>"; }

			if(test_check_flag == "Test"){
				if($.trim($(":input[name=elem_fsta_test_name]").val()) == ""){ msg += "<li>Test Name</li>"; }
				var tmp_testtype = $.trim($(":checked[name=elem_fsta_test_type]").val());
				if(tmp_testtype==""){msg += "<li>Test Type</li>";}
			}else if(test_check_flag == "Appointment" || test_check_flag == "Referral"){
				if($.trim($(":input[name=elem_fsta_phy_name]").val()) == ""){ msg += "<li>Physician Name</li>"; }
			}

			if(msg != ""){
				top.fAlert("<b>Please fill in the following:</b><div class='m10'>"+msg+"</div>");
				return;
			}

			//Check--

			var param = $("#frm_future_tests_extr").serialize();
			param += "&elem_dos="+$("#elem_dos").val();

			//alert(param);

			//start
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"frm_future_tests_extr");
			$.post("requestHandler.php", param, function(data){
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"frm_future_tests_extr");
				add_future_sch_tests_appoints_processing(data);
			} );

		}else if(flg==3){

			if(finalize_flag==1&&isReviewable!=1){ top.fAlert("Please open a new chart note"); return;}
			var divtype = (eid=="2") ? "div_saved_future_appoint_ref" : "div_saved_future_tests";

			var delid = "";
			$("#"+divtype+" :checked[name*=elem_FSTA_delid]").each(function(){delid += this.value+",";})
			if(delid == ""){
				top.fAlert("Please check any record to delete.");
			}else{

				if(typeof(cnfrm)=="undefined"){
					top.fancyConfirm("Are you sure you want to delete?","","top.fmain.add_future_sch_tests_appoints('"+flg+"', '"+eid+"',true);");
					return;
				}


				//var chk_tmp = confirm("Are you sure you want to delete?");
				//if(chk_tmp){
					var strEdit = "&elem_delid="+encodeURI(delid);

					//start
					if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"ft_appnt");

					//$("#div_add_future_tests").remove();
					$.get("requestHandler.php?elem_formAction=add_future_sch_tests_appoints"+strEdit, function(data){
						//alert(data);
						//stop
						if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"ft_appnt");
						add_future_sch_tests_appoints_processing(data);
					});

				//}

			}


		}else if(flg==0){
			hide_modal("div_add_future_tests");
			return;
		}

	}else{

		var strEdit = "";
		if(typeof(eid)!="undefined" && eid != ""){
			strEdit = "&elem_showId="+eid;
		}

		//start
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"ft_appnt");

		if($("#div_add_future_tests").length>0){ hide_modal("div_add_future_tests"); }
		$.get("requestHandler.php?elem_formAction=add_future_sch_tests_appoints"+strEdit, function(data){
			//stop
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"ft_appnt");
			add_future_sch_tests_appoints_processing(data);
		});
	}
}

//Future Scheduled Tests/appointments(Outside) --
function add_future_sch_tests_appoints_processing(data, idpimg){
	//alert(data);

	if($("#div_add_future_tests").length>0){
		hide_modal("div_add_future_tests");
		setTimeout(function(){ add_future_sch_tests_appoints_processing(data, idpimg); }, 500);
		return;
		//var position_cur = $("#div_add_future_tests").position();
		//$("#div_add_future_tests").replaceWith(data);
		//$("#div_add_future_tests").css({"left" : position_cur.left+"px", "top" : position_cur.top+"px"});
	}

	$("body").append(""+data);
	$("#div_add_future_tests").modal("show");

	var dt = $( "input[name=elem_fsta_sch_date]" ).val();
	if($.trim(dt) == ""){dt = new Date();}
	var dt_format = top.jQueryIntDateFormat;

	$( "input[name=elem_fsta_sch_date]" ).datepicker().datepicker( "option", "dateFormat", dt_format ).datepicker("setDate",dt);

	// ref to consult, doctor name
	multiphy_typeahead(6);
	//$( "#div_add_future_tests" ).draggable({ handle: "#div_add_future_tests .hdr", cancel: "#div_add_future_tests #div_saved_future_tests" });
	/*
	$( "#div_add_future_tests" ).draggable({
		handle: "#div_add_future_tests .hdr",
		drag: function( event, ui ) {
				var et = event.srcElement;

				//*
				if($(et).hasClass("hdr")){
					return true;
				}else{
					return false;
				}
				//*-/
			}
		});
	*/
	//
	//if($( "#div_add_future_tests .hdr" ).length>0){ $( "#div_add_future_tests .hdr" )[0].trigger("click"); }

}

function set_future_sch_tests_options(val){

	//var tmp ="label[for=elem_fsta_test_name], label[for=elem_fsta_test_type], .lbl_test_type, "+
	//		"input[name=elem_fsta_test_name], input[name=elem_fsta_test_type]";
	var tmp ="#dvFSTA_test";

	if(val == "Test"){
		$(tmp).show();
	}else{
		$(tmp).hide();
	}

	//var tmp ="label[for=elem_fsta_sch_snomed], label[for=elem_fsta_sch_cpt], label[for=elem_fsta_sch_loinc], "+
	//		"input[name=elem_fsta_sch_snomed], input[name=elem_fsta_sch_cpt], input[name=elem_fsta_sch_loinc] ";
	tmp ="#dvFSTA_loinc, #dvFSTA_cpt, #dvFSTA_snomed";

	if(val == "Referral"){
		$(tmp).hide();
	}else{
		set_future_sch_testsType_options();
	}
}

function set_future_sch_testsType_options(){

	var tmp = $.trim($(":checked[name=elem_fsta_test_type]").val());
	var test_check_flag = $.trim($(":checked[name=elem_fsta_test_appoint]").val());

	$("#dvFSTA_loinc, #dvFSTA_cpt, #dvFSTA_snomed").hide();

	if(test_check_flag == "Test"){
		if(tmp == "Lab" || tmp==""){
			$("#dvFSTA_loinc").show();
		}
		if(tmp == "Procedure" || tmp==""){
			$("#dvFSTA_cpt").show();
		}
		if(tmp == "Imaging" || tmp==""){
			$("#dvFSTA_snomed").show();
		}
	}
}

//-----------------------------

//-----------CHART LOCK ------------------
function lock_checkPass(mtd){
	//Checks
	var oIn = $("#elem_lockAdminPass");
	if(oIn.length && ($.trim(oIn.val()) == "")){
		top.fAlert("Enter password.");
		return;
	}

	//tab
	oIn = $("#elem_lockAdminPass");
	var cTab = $("#elem_tab").val();

	//
	if(mtd=="MD5"){oIn.val(md5($.trim(oIn.val())));}
	else{oIn.val(Sha256.hash($.trim(oIn.val())));}

	if(typeof(zPath)=="undefined"){zPath="";}
	//Define Var -----
	var u = (typeof(zPath_remote) != "undefined") ? zPath_remote : zPath;
	var url=u+"/chart_notes/requestHandler.php";

	//var params = "elem_formAction=checkAdminPass4Lock";
	//params+="&elem_lockAdminPass="+oIn.val();
	var params = { "elem_formAction": "checkAdminPass4Lock", "elem_lockAdminPass": ""+oIn.val(), "elem_tab":""+cTab };

	//-------------------------------------------

	$.post(url, params,
	   function(data){
		if(data == 1){
			//Close all opened windows and reload
			if(typeof(funClosePopUpExe)!='undefined'){funClosePopUpExe();}
			top.window.location.reload();
		}else{
			top.fAlert("Please enter correct password.");
			oIn.val("");
			oIn.focus();
		}
	});

}

//chart lock
function lock_showPassPrompt(){
	var odiv = document.getElementById("divLockPassPrompt");
	var oIn = document.getElementById("elem_lockAdminPass");
	if(odiv){
		odiv.style.display = "block";
	}
	if(oIn){
		oIn.value="";
		oIn.focus();
	}
	stopClickBubble();
}
//chart lock

//-------------------------------

//-------------- SLIDER  -----------------

function showChartNotesTree(flg){

	if(flg!="2" && flg!="1" && flg!="srch"){
		//Checks
		if($("#sliderRight").length>0 && $("#sliderRight").attr("attrFilled")=="1"){
			$("body").trigger("click"); //close with clicked again
			return;
		}
	}

	//called from test
	if(flg=="1"){update_toolbar_icon();}

	//
	var response="json";

	//Define Var -----
	var url = zPath+"/chart_notes/requestHandler.php"; //"common/sliderFeeder_2.php";
	var params = "elem_formAction=chartNoteTree";

	if(flg=="srch"){
		var srchVal = gebi('elem_findSearch').value;
		var fndStatus='';
		if(gebi('elem_findStatus')) {
			fndStatus = gebi('elem_findStatus').value;
		}
		var fndPhy='';
		if(gebi('elem_findPhy')) {
			fndPhy = gebi('elem_findPhy').value;
		}

		//Define Var -----
		params += "&elem_formAction_ptforms=Search";
		params += "&srchVal="+srchVal;
		params += "&fndStatus="+fndStatus;
		params += "&fndPhy="+fndPhy;

		$("#sec_exams,#sec_tests").html("Loading..");
		//response="text";
	}else{
		params += "&elem_formAction_ptforms=chartNoteTree";
		//response="json";
	}

	//-------------------------------------------
	$("#sliderRight").remove();
	$("body").append("<div id=\"sliderRight\">Loading...</div>");
	if(typeof(setProcessImg)=="function")setProcessImg("1","sliderRight");

	$.post(url, params,
	   function(data){
			if(typeof(setProcessImg)=="function")setProcessImg("0","sliderRight");
			//------  processing after connection   ----------
				if(response=="text"){console.log(data);}
				var tmp = ""+data.innerHtml;
				if($("#sliderRight").length<=0){ $("body").append("<div id=\"sliderRight\">Loading...</div>"); }

				//
				$("#sliderRight").html(tmp).attr("attrFilled", "1").bind("click", function(){ stopClickBubble(); });
				//$("#elem_ccompliant").val("<div id=\"sliderRight\">"+tmp+"</div>");
				$('[data-toggle="tooltip"]').tooltip();
				//set color of pt btn
				if(typeof(data.is_test_uninterpreted)!="undefined") { is_test_uninterpreted = data.is_test_uninterpreted; highlight_pt_form_btn(); }
			//------  processing done --------------------------

	},response); // End Function Post

}

function sl_showopts(obj){
	var tmp = $(obj).parent().parent().find("ul");

	if(tmp.css("display")=="none") tmp.show();
	else tmp.hide();

	stopClickBubble();
}

//-------------------------------------------

//Capture main_page body Click ---------
var flgHMPE1=0;
function handlerMainPageEvents(e){

	var etar = e.target;
	var chk = etar.nodeName;
	var chktag = etar.tagName;
	var eType = e.type;



	if(chk == "INPUT" || chk == "TEXTAREA" || ((eType == "change" || eType == "keyup" || eType == "mousedown") && chk == "SELECT")){

		var enm = e.target.name;
		var eid = e.target.id;

		//
		if(e.target.type=="button")return;

		//--
		if(elem_per_vo=="1"){
			if(enm!="elem_lockAdminPass" && enm!="elem_btnLock"){
				if($("#icoPtLock").length>0){
					$("#icoPtLock").triggerHandler('click');
				}else if(flgHMPE1==0){
					if($(etar).parents("#genHealthDiv_wv").length<=0){
					var t = "You don't have permission to edit chart notes. ";
					top.fAlert(""+t);
					flgHMPE1=1;
					window.status = "Note: "+t;
					setfinalizedFunction('',1);
					}

				}
			}
			return;
		}
		//--

		//Final Warning
		if(finalize_flag=='1'&&isReviewable=='1'){
			if(eid!="elem_ptTemplate"){
			checkAlert();
			}
		}

		//isMainChanged
		isFormChanged=1;

		//Vision Bg color--
		if($(etar).parents("#Vision").length>0){ //hack for jquery error
			if(enm != "clws_charges[]"){
				//if($("#vision "+chk+"[name="+enm+"]").hasClass("active")==false){
				//$("#vision "+chk+"[name="+enm+"]").addClass("active");
				if($(etar).hasClass("active")==false){
				set_vis_el_active(etar);
				/*
				$(etar).addClass("active");

				var str = $("#elem_statusElements").val();
				if(typeof(str) == "undefined") str = "";

				if(str.indexOf(enm+"=1,")==-1){
					str = str.replace(""+enm+"=0,", "");
					str=str+""+enm+"=1,";
				}

				$("#elem_statusElements").val(str);
				*/
				//alert("11");
				if($(etar).parents("#MR").length>0 || $(etar).parents("#PC").length>0){

				if($(etar).parents(".prism,#pc3_prism,#mr3_prism").length>0){ //Prism
					var eye = (enm.indexOf("Od")!=-1) ? "Od" : "Os";
					$(etar).parents(".prism,#pc3_prism,#mr3_prism").children(":input[name*="+eye+"]").each(function(){ $(this).trigger("keyup"); });
				}

				//MR Time
				if($(etar).parents("#MR").length>0){ //MR
					var vissectch = ""+$("#elem_vis_section_touched").val();
					if(vissectch.indexOf("MR")==-1){ $("#elem_vis_section_touched").val(vissectch+",MR"); }
				}
				//
				if(enm.indexOf("OsAdd")!=-1 && $(etar).val()==""){ var t =  enm.replace("OsAdd","OdAdd"); $(etar).val($("#"+t).val()); 	}

				}

				}

				//add myopia/presbyopia
				if(enm.indexOf("elem_visMrO")!=-1||enm.indexOf("elem_mrNoneGiven")!=-1){add_opyia_assessment(enm, etar.value);}
				//Lasik
				if($(etar).parents("#div_lasik_common").length>0){
					$("#el_lasik_userid").val(authUserID);
					if(enm.indexOf("el_visLasikTrgtTime")!=-1){ $("#"+enm).val(""+currenttime()); }
				}

			}
		}
		//Vision Bg color--

		//CVF --
		if($(etar).parents("#CVF").length>0){
			$("#CVF div.bggrey").removeClass("bggrey");
		}
		//--

		//AmslerGrid --
		if($(etar).parents("#AmslerGrid").length>0){
			ag_clear_grey_bg();
		}
		//--

		//CN Progress Notes--
		if($(etar).parents("#dv_cn_progess_notes").length>0){ //hack for jquery error
			if($(etar).hasClass("active")==false){
				$(etar).addClass("active");
			}
		}
		//--

		//soc
		if($(etar).prop("checked") && $(etar).parents("#ft_strd_of_cr").length>0){
			var eid = $(etar).attr("id"); if(eid.indexOf("el_soc")!=-1){ $('#ft_strd_of_cr :checked').not("#"+eid).prop('checked', false);} //single select SOC
		}

		//Assessment & plan : No Color Coding on assessment on plan section
		//if($("#assessplan *[name="+enm+"]").length>0){ return;}
		if($(etar).parents("#assessplan,#detailDesc1,#detailDesc2,#divApPlan,.OrderDetail, #dialog-Mdc-cum-new-pt, #dv_cn_progess_notes").length>0){return;}

		//<?php
		//User Type Color: Color coding ------------
		// Entire Summary sheet elements: ut color status will be in vision table :(

			//if($_SESSION["logged_user_type"]==3||$_SESSION["logged_user_type"]==11||
			//	$_SESSION["logged_user_type"]==12||$_SESSION["logged_user_type"]==13){
				//echo "
				utElem_capture(e);
				//";
			//}

		// Entire Summary sheet elements
		//User Type Color: Color coding ------------
		//?>

		//show bigger HX
		if(eid=="elem_chk"||eid=="elem_ccompliant"){	setTimeout(function(){if($(etar).is(":focus")){$(etar).addClass("mk_bigger_el").bind("blur",function(){ $(etar).removeClass("mk_bigger_el"); });}}, 2000);	}

		top.$("body").click(); //closePopUps(); // to close menu on top when cchistory is clicked.

	}else if(eType == "click"||eType == "touchstart"){

		//soc
		if($(etar).parents("#ft_strd_of_cr").length>0){
			var eid = $(etar).attr("for"); if(eid.indexOf("el_soc")!=-1){ $('#ft_strd_of_cr :checked').not("#"+eid).prop('checked', false);} //single select SOC
		}

		//
		//var  dw = window.open("", "ss", "width=200,height=200");
		//dw.document.write(""+isiPad+" - "+$(etar).parents('#div_rvs').length>0);
		//Close Vision
		/*
		if((chk=="HEADER" && $(etar).parent('#vision').length) || (chk=="LABEL" && $(etar).parent('#vision>header').length) ){
			vis_show_sec('vision');
		}else if(chktag=="DIV" && ($(etar).parent('#distance').length||$(etar).parent('#near').length)){
			vis_show_sec('r1');
		}
		*/

		if(isiPad){
			if(($(etar).parents('#sliderRight').length>0)){
				// close after some time //fix for ipad: but will
				setTimeout(function(){ closePopUps(); },500);
			}else if($(etar).parents('#div_rvs').length>0){
				//do nothing
				//alert("222");
			}else{
				//Close Pop Up
				closePopUps();
			}
		}
		else{
			//Close Pop Up
			closePopUps();
		}
	}
}
//Capture main_page body Click ---------

//Amsler Grid -----
function ag_clear_grey_bg(){$("#AmslerGrid div.bggrey").removeClass("bggrey");}
//Amsler Grid -----

//Vision -------
function clearMr(w, rem){
	if(typeof(rem)!="undefined"&&rem==1){
		$("#row_mr_"+w+", #row_mr_"+(w-1)).hide();
		return;
	}
	var ar = new Array("elem_visMrOdP","elem_visMrOdPrism","elem_visMrOdSlash","elem_visMrOdSel1","elem_visMrOsP","elem_visMrOsPrism",
				"elem_visMrOsSlash","elem_visMrOsSel1","elem_visMrOdS","elem_visMrOdC","elem_visMrOdA","elem_visMrOdAdd",
				"elem_visMrOdTxt1","elem_visMrOdSel2","elem_visMrOdTxt2","elem_visMrOsS","elem_visMrOsC","elem_visMrOsA",
				"elem_visMrOsAdd","elem_visMrOsTxt1","elem_visMrOsSel2","elem_visMrOsTxt2","elem_visMrDesc","elem_visMrOdSel2Vision","elem_visMrOsSel2Vision",
				"elem_providerName","elem_providerId","elem_visMrPrismDesc_","elem_visMrOuTxt1","elem_mr_pres_dt_", "elem_mrNoneGiven", "elem_mr_type");
	var l = ar.length;
	for(var i=0;i<l;i++){
		var n = ar[i];
		if(((w > "1")) && (n != "elem_visMrDesc")){
				n = (n.indexOf("Od") != -1)? n.replace(/Od/,"OtherOd") : n ;
				n = (n.indexOf("Os") != -1)? n.replace(/Os/,"OtherOs") : n ;
				n = (n.indexOf("Ou") != -1)? n.replace(/Ou/,"OtherOu") : n ;
		}

		if((w > "1") && (n=="elem_providerName"||n=="elem_providerId")){
			n+="Other";
		}

		if((w > "2") && (n != "elem_visMrDesc")){
			n = n+"_"+w;
		}
		if((w >= "2") && (n == "elem_visMrDesc")){
			n = "elem_visMrDescOther";
		}
		if((w > "2") && (n == "elem_visMrDescOther")){
			n = "elem_visMrDescOther_"+w;
		}
		if(n.indexOf("elem_visMrPrismDesc_") != -1){ n = "elem_visMrPrismDesc_"+w;  }
		if(n.indexOf("elem_mr_pres_dt_") != -1){ n = "elem_mr_pres_dt_"+w;  }
		if(n.indexOf("elem_mrNoneGiven") != -1){ n = "elem_mrNoneGiven"+w;  }
		if(n.indexOf("elem_mr_type") != -1){ n = "elem_mr_type"+w;  }

		var o = gebi(n);
		if(o){
			if(o.type == "checkbox"){
				o.checked=false;
			}else{
			if(o.value.indexOf("20/") < 0){
				o.value="";
			}else{
				o.value="20/";
			}
			}
		}
	}

	//stopClickBubble();
}
function changeVisionStatus(){
	var obj = arguments[0]; //if argument given
	var e = window.event;
	if(e && (typeof arguments[1] == "undefined")){
		obj = e.srcElement;
	}
	var flg_frc = (typeof arguments[2] != "undefined" && arguments[2]==1) ? 1 : 0;

	if(obj){
		var ev="click";
		if(typeof obj.type != "undefined"&&obj.type=="select-one"){ ev="keyup"; }
		if(!$(obj).hasClass("active")||flg_frc==1){$(obj).trigger(ev);}
	}
}

//
function setGivenMr(o,str){}
//
function showTranspose(indx, wh){

	var arreye = ["Od","Os"];

	for(var x in arreye){
		var eye = arreye[x];
		if(typeof(wh)!="undefined" && wh=="PC"){
			var sufx="";
			if(indx>"1"){
				sufx=""+indx;
			}
			var vs="elem_visPc"+eye+"S"+sufx;
			var vc="elem_visPc"+eye+"C"+sufx;
			var va="elem_visPc"+eye+"A"+sufx;

		}else{
			var othr="";
			if(indx>"1"){
				othr="Other";
			}
			var sufx="";
			if(indx>"2"){
				sufx="_"+indx;
			}

			var vs="elem_visMr"+othr+eye+"S"+sufx;
			var vc="elem_visMr"+othr+eye+"C"+sufx;
			var va="elem_visMr"+othr+eye+"A"+sufx;
		}

		//od
		var s = $("input[name="+vs+"]").val();s=$.trim(s);
		var c = $("input[name="+vc+"]").val();c=$.trim(c);
		var a = $("input[name="+va+"]").val();a=$.trim(a);

		if(s.toUpperCase()=="PLANO"){ s="0"; }

		if(typeof(s)=="undefined"  || s=="" || isNaN(s)){	continue;	}

		if(typeof(c)=="undefined" || c.toUpperCase()=="SPHERE" || c=="" || isNaN(c)){ continue; }

		//if(s.toUpperCase()=="PLANO" || c.toUpperCase()=="SPHERE"  || s == "0" || c == "0"){continue;}

		var sig_sc=parseFloat(s) + parseFloat(c);
		sig_sc = sig_sc.toFixed(2);
		if(""+sig_sc.indexOf("+")==-1 && ""+sig_sc.indexOf("-")==-1){ sig_sc = "+"+sig_sc;  }

		var op_sign_c = (-1) * parseFloat(c);
		op_sign_c = ""+op_sign_c.toFixed(2);
		if(""+op_sign_c.indexOf("+")==-1 && ""+op_sign_c.indexOf("-")==-1){ op_sign_c = "+"+op_sign_c;  }

		if(typeof(a)!="undefined" && a!="" && !isNaN(a)){
			var new_axis = parseFloat(a) + 90;
			if(new_axis<1 || new_axis>180){	new_axis = a - 90;	}
		}

		if(isNaN(sig_sc)){ sig_sc=""; }
		if(isNaN(op_sign_c)){ op_sign_c=""; }
		if(isNaN(new_axis)){ new_axis=""; }
		else if(new_axis!="" && !isNaN(new_axis)){var t = ""+new_axis;if(t.length<3){ new_axis = (t.length==1) ? "00"+new_axis : "0"+new_axis ; }}

		//var trns_od = "<label class=\"od\">OD</label> : "+sig_sc+" "+op_sign_c+" X "+new_axis+"<br/></br>";
		$("input[name="+vs+"]").val(sig_sc).triggerHandler("blur");
		$("input[name="+vc+"]").val(op_sign_c);
		$("input[name="+va+"]").val(new_axis);

	}

	stopClickBubble();

}
//
function checkValue(num, val){
	var c=$("input[type=checkbox][name*=elem_mrNoneGiven"+num+"]");
	if(c.prop("checked")==true&&c.hasClass("inact")){
		c.prop("checked",false);
		c.trigger('click');
	}

	if(val!=""){
		if(val == "cycloplegic"){val="CR "+num;}
		else if(val == "Over Refraction"){val="OR "+num;}
		else if(val == "trial frame"){val="TF "+num;}
		else if(val == "Final"){val="FR "+num;}
		else if(val == "Outside Rx"){val="ORx "+num;}
		$("#h2_mr_id"+num).html(val);
	}else{
		$("#h2_mr_id"+num).html("MR "+num);
	}
}
//
function showPrescDtBox(indx,op){ /* TO DO  */ }
//
function mrOtherExe(objDiv,flag)
{
	if(typeof objDiv != "undefined")
	{
		if(flag == true)
		{
			objDiv.style.display = "block";
		}
		else
		{
			objDiv.style.display = "none";
			emptyPcValues(objDiv);
		}
	}
}
//
function emptyPcValues(objDiv){
	var objElems = new Array();
	var objElemsInput = objDiv.getElementsByTagName("INPUT");
	var objElemsSelect = objDiv.getElementsByTagName("SELECT");
	if(objElemsInput.length > 0){
		objElems = objElems.concat(objElemsInput);
	}
	if(objElemsSelect.length > 0){
		objElems = objElems.concat(objElemsSelect);
	}
	var len = objElems[0].length;
	for(var i=0;i<len;i++)
	{
		if((objElems[0][i].name.indexOf("elemHoriPosition") == -1) && (objElems[0][i].name.indexOf("elemTargetName") == -1)){
			objElems[0][i].value = "";
		}
	}
}

function copyPcMr(ob){
	var v = $(ob).val();
	if(v==""){ return; }
	var ti = $(ob).data("mr"), t="MR", tdv="row_mr_", tprsmdv="";
	if(typeof(ti)=="undefined"){ ti = $(ob).data("pc"); t="PC"; tdv="pc"; tprsmdv="pc_ovr_ref_prism"; }

	if(v.indexOf(t)!=-1){
		var si=$.trim(v.replace(t,"")), sid, flgOpPrsm=0;
		$("#"+tdv+ti+" :input").each(function(){
			if(this.type=="text" || this.type=="select-one" || this.type=="textarea" || this.type=="checkbox"){
				sid="";
				if(t=="PC"){
					if(ti>1||this.type=="checkbox"){
						tsi = (si>1||this.id.indexOf("elem_visPcPrismDesc_")!=-1) ? si : "";
						sid = this.id.replace(ti, tsi);
					}else{
						if(this.id.indexOf("elem_visPcPrismDesc_") != -1){this.id="elem_visPcPrismDesc_";}
						sid = this.id+si;
					}
				}else if(t=="MR"){
					if(ti>2){
						if(si>2){
							sid =  this.id.replace(ti, si);
						}else if(si>1){
							if(this.id.indexOf("PrismDesc")!=-1 || this.id.indexOf("elem_mr_pres_dt")!=-1 || this.id.indexOf("elem_mr_type")!=-1 || (this.id.indexOf("Other")==-1 && this.type=="checkbox") ){sid = this.id.replace(ti, si);}
							else { sid = this.id.replace("_"+ti, ""); }
						}else{
							if(this.id.indexOf("PrismDesc")!=-1 || this.id.indexOf("elem_mr_pres_dt")!=-1 || this.id.indexOf("elem_mr_type")!=-1 || (this.id.indexOf("Other")==-1 && this.type=="checkbox") ){sid = this.id.replace(ti, si);}
							else{ sid = this.id.replace("_"+ti, "").replace("Other", ""); }
						}
					}else if(ti>1){
						if(si>2){
							sid =  this.id+"_"+si;
						}else{
							sid = this.id.replace("Other", "");
						}
					}else if(ti<=1){
						if(this.id.indexOf("PrismDesc")!=-1 || this.id.indexOf("elem_mr_pres_dt")!=-1 || this.id.indexOf("elem_mr_type")!=-1 || this.id.indexOf("mrNoneGiven")!=-1 ){sid = this.id.replace(ti, si);}
						else{
							sid = (this.id.indexOf("elem_visMrDesc")!=-1) ? this.id+"Other" : this.id.replace("elem_visMr", "elem_visMrOther");
							if(si>2){
								sid = (this.type=="checkbox") ? sid.replace("Sel2_", "Sel2_"+si+"_")  : sid+"_"+si;
							}
						}
					}
				}

				if(sid!=""){
					//
					//if(this.type=="checkbox"){console.log(this.id, this.name, this.type, sid);}
					if(this.type=="checkbox"){
						if($("#"+this.id).prop("checked")!=$("#"+sid).prop("checked")){
							$("#"+this.id).parent().find("label[for='"+this.id+"']").trigger("click");
						}
					}else{
						var tmp = $("#"+sid).val();
						if(typeof(tmp)=="undefined"){tmp="";}
						tmp = $.trim(tmp);
						$("#"+this.id).val(tmp);
						utElem_capture(this);
						set_vis_el_active(this);
					}

					if(t=="PC"){
						if($(this).parents("#"+tprsmdv+ti).length>0 && tmp!="" && tmp!="20/"){ flgOpPrsm=1; }
					}else if(t=="MR"){
						if($(this).parents("#mr_gl_ph"+ti+",#mr_prism"+ti).length>0 && tmp!="" && tmp!="20/"){ flgOpPrsm=1; }
					}
				}
			}
		});

	}else{

		var ar = ['S', 'C', 'A', 'Add', 'Sel1', 'P', 'Slash', 'Prism']; //'Sel2',
		var fmr = "elem_visMr", fpc = "elem_visPc" ;
		var sod, sos, tod, tos, oth="", otht="", sfx="", sfxt, tmp;
		var si = "";
		if(v.indexOf("PC")!=-1 || v.indexOf("MR")!=-1){
			si=parseInt($.trim(v.replace(/PC|MR/g,"")));
		}

		//src
		if(v.indexOf("PC")!=-1){
			var fs = fpc;
			sfx = (si<2) ? "" : si ;
		}else if(v.indexOf("MR")!=-1){
			var fs = fmr;
			oth = (si>1) ? "Other" : "" ;
			sfx = (si>2) ? "_"+si : "";
		}else if(v.indexOf("ARC")!=-1){
			var fs = "elem_visCycAr";
		}else if(v.indexOf("AR")!=-1){
			var fs = "elem_visAr";
		}

		//trgt
		if(t=="MR"){
			var ft = fmr;
			otht = (ti>1) ? "Other" : "" ;
			sfxt = (ti>2) ? "_"+ti : "";
		}else if(t=="PC"){
			var ft = fpc;
			sfxt = (ti<2) ? "" : ti ;
		}

		for(var z in ar){
			//
			if(v.indexOf("AR")!=-1 && ar[z]!="S" && ar[z]!="C" && ar[z]!="A"){continue;}

			sod = sos = tod = tos = null;
			//src
			if(v.indexOf("PC")!=-1){
				tmp = (ar[z] == "Sel1") ?  "Sel2" : ar[z] ;
				sod = $("#"+fs+"Od"+tmp+sfx);
				sos = $("#"+fs+"Os"+tmp+sfx);
			}else{ //if(v.indexOf("MR")!=-1)
				sod = $("#"+fs+oth+"Od"+ar[z]+sfx);
				sos = $("#"+fs+oth+"Os"+ar[z]+sfx);
			}

			//trgt
			if(t=="MR"){
				tod = $("#"+ft+otht+"Od"+ar[z]+sfxt);
				tos = $("#"+ft+otht+"Os"+ar[z]+sfxt);
			}else if(t=="PC"){
				tmp = (ar[z] == "Sel1") ? "Sel2" : ar[z] ;
				tod = $("#"+ft+"Od"+tmp+sfxt);
				tos = $("#"+ft+"Os"+tmp+sfxt);
			}

			//
			if(sod && tod){
				tod.val(sod.val());
				utElem_capture(tod[0]);
				set_vis_el_active(tod[0]);
			}

			//
			if(sos && tos){
				tos.val(sos.val());
				utElem_capture(tos[0]);
				set_vis_el_active(tos[0]);
			}
			if(t=="PC"){
				if($(tod, tos).parents("#"+tprsmdv+ti).length>0 && tmp!="" && tmp!="20/"){ flgOpPrsm=1; }
			}else if(t=="MR"){
				if($(tod, tos).parents("#mr_gl_ph"+ti+",#mr_prism"+ti).length>0 && tmp!="" && tmp!="20/"){ flgOpPrsm=1; }
			}
		}

		//Desc
		sod = $("#"+fs+"Desc"+oth+sfx);
		tod = $("#"+ft+"Desc"+otht+sfxt);
		if(sod && tod){
			tod.val(sod.val());
			utElem_capture(tod[0]);
			set_vis_el_active(tod[0]);
		}

		//prism desc
		if(v.indexOf("PC")!=-1 || v.indexOf("MR")!=-1){
		sod = $("#"+fs+"PrismDesc_"+si);
		tod = $("#"+ft+"PrismDesc_"+ti);
		if(sod && tod){
			tod.val(sod.val());
			utElem_capture(tod[0]);
			set_vis_el_active(tod[0]);
		}
		}
	}

	if(flgOpPrsm==1){
		if(t=="PC"){ $("#"+tprsmdv+ti).addClass("in"); }
		else if(t=="MR"){ $("#mr_gl_ph"+ti+",#mr_prism"+ti).addClass("in"); }
	}

	//Set Provider Id
	if(t=="MR"){ if(ti>=1){setUserName("MR"+ti);} }

}

//
function changePc(n){
	if(typeof(n)=="object"){

		//IF SPHERE O THEN CONVERT IT INTO PLANO
		if(n.name.indexOf("S")!=-1 && (n.value=='0' || n.value=='0.0' || n.value=='0.00')){
			 $("input[name='"+n.name+"']").val("PLANO");
		}
	}
}

//
function justify2Decimal(objElem,nosign){
	var valElem = $.trim(objElem.value);
	if(valElem!="" && !isNaN(valElem) && nosign!=1){
		var tmp=def_clindr_sign;
		if(typeof(tmp)=="undefined"||tmp==""||objElem.name.indexOf("S")!=-1){tmp="+"}
		var ptrn = "^[\+|\-]";
		var reg = new RegExp(ptrn,'g');
		var sign = valElem.match(reg);
		var unJustNumber = (sign == null) ? valElem : valElem.substr(1);
		var justNumber = (unJustNumber == "") ? "" : parseFloat(unJustNumber).toFixed(2);
		if(justNumber==0.00)tmp=sign="";
		objElem.value = (sign == null) ? tmp+justNumber : sign+justNumber;
	}else{
		objElem.value = valElem;
	}
}
//
function ak_sel(obj){$("input[name='elem_kType']").each(function(){ if(obj.checked && this.id != obj.id){this.checked=false;utElem_capture(this);}});}
//check 2 blur
function check2Blur(obj,wh,wh2){
	var e = window.event;
	var kCode = e.keyCode
	if( ((kCode >= 48) && (kCode<=57)) ||
	   ((kCode >=65 ) && (kCode <= 90)) ||
	   ((kCode >= 96) && ( kCode <= 111)) ||
	   ((kCode >= 186) && ( kCode <= 191)) ||
	   ((kCode >= 219) && ( kCode <= 222))
	){
		var oWh2 = gebi(wh2,1)[0];
		var val = ( typeof obj.value == "undefined" ) ? "" : $.trim(obj.value);

		if( ((wh == "I") || (wh == "A") || (wh == "X")) && ( val.length >= 3 ) ){
			oWh2.focus();
			if(oWh2.type!="select-one")oWh2.select();

		}else if((wh == "K") && (wh == "slash")){ //K - XX.XX
			var ptrn = "^\\w\\w\.\\w\\w$";
			var reg = new RegExp(ptrn, "g");
			var mtch = val.match(reg);
			if(mtch){
				oWh2.focus();
				if(oWh2.type!="select-one")oWh2.select();
			}
		}else{
			var dInx = val.indexOf(".");
			if( (dInx != -1) ){
				var sbStr = val.substr(dInx+1);
				if( sbStr.length >= 2 ){
					oWh2.focus();
					if(oWh2.type!="select-one")oWh2.select();
				}
			}
		}
	}
}
//
function changeMr(n){
	//ifchart is finalized than do not fire
	if((finalize_flag==1 && isReviewable==0) || elem_per_vo == "1")return;

	var ar = null;
	var e="OU";
	if(typeof(n)=="object"){

		if(n.name.indexOf("Od")!=-1){
			e="OD";
		}else if(n.name.indexOf("Os")!=-1){
			e="OS";
		}

		//IF SPHERE O THEN CONVERT IT INTO PLANO
		if(n.name.indexOf("S")!=-1 && (n.value=='0' || n.value=='0.0' || n.value=='0.00')){
			 $("input[name='"+n.name+"']").val("PLANO");
		}

		//flag for copy Add in MR Os
		var c=0,vn="";
		if(n.name.indexOf("Add")==-1&&(n.name.indexOf("S")!=-1||n.name.indexOf("C")!=-1||n.name.indexOf("A")!=-1||n.name.indexOf("Txt1")!=-1)){
			c=1;
			vn=n.name;
		}
		//flag for copy Add in MR Os

		//if(n.name.indexOf("Ak")!=-1){
		//	n=4;
		//}else

		if(n.name.indexOf("Other")!=-1){
			var mri = n.name.match(/_\d+/);
			if(mri){ mri=mri[0]; mri = ""+mri.replace(/_/,''); n = parseInt(mri);}else{n = 2;}
		}else{
			n=1;
		}
	}

	//set UserName
	if(n>=1){
		setUserName("MR"+n);
		if(e=="OS"&&c==1){
			if(vn && typeof(vn)!="undefined" && vn!=""){
				var a1 = vn.replace(/(S|C|A|Txt1)/,"Add");
				a1=a1.replace(/(Od|Os)/,"Od");
				var a2=a1.replace(/(Od)/,"Os");
				if($("#vision input[name="+a1+"]").length>0&&$("#vision input[name="+a2+"]").length>0){
					//If the second eye (OS) text box is activated then the Add should get copied from OD for MR1,MR2 and MR3
					var t= $("#vision input[name="+a2+"]");
					var s= ""+$("#vision input[name="+a1+"]").val();
					if(s!=""&&s!="+"){	t.val(""+$("#vision input[name="+a1+"]").val());utElem_capture(t[0]);}
					//--
				}
			}
		}
	}
}
//
function set_vis_el_active(oenm){
	var enm = "";
	if (oenm != null) {
		enm = oenm.name;
	}
	$(oenm).addClass("active");
	var str = $("#elem_statusElements").val();
	if(typeof(str) == "undefined") str = "";

	if(str.indexOf(enm+"=1,")==-1){
		str = str.replace(""+enm+"=0,", "");
		str=str+""+enm+"=1,";
	}

	$("#elem_statusElements").val(str);
	//*/
}
function chkOther(o){if(o.value.toLowerCase()=='other')o.value='';}
function wv_activate_menu_click(obj_el, flg_top){
	var slctr = " .menu a";
	if(typeof(obj_el)!="undefined" && obj_el!=""){ slctr = obj_el;  }
	var obj = $(""+slctr).not("#ContactLens ul.dropdown-menu a");
	if(typeof(flg_top)!="undefined" && flg_top=="1"){ obj = top.$(""+slctr);  }
	obj.bind("click", function(){
			var me = $(this);
			if(typeof(flg_top)!="undefined" && flg_top=="1"){ me = top.$(this);  }
			var tid = me.parents(".menu").find("button").data("trgt-id");
			var otid = (typeof(flg_top)!="undefined" && flg_top=="1") ? top.$("#"+tid) : $("#"+tid) ;
			var v = me.data("val");
			if(tid.indexOf("elem_followUpNumber_")!=-1){ var pv = otid.val(); if(typeof(pv)!="undefined" && pv!=""){ v=v+","+pv; } }
			otid.val(v).triggerHandler("change");
			otid.triggerHandler("blur");
			otid.triggerHandler("keyup");
			otid.trigger("click");
		});
}
function wv_activate_mr(){
	$(":checkbox[id*=elem_mrNoneGiven]").bind("click", function(){ var x=this.name.replace("elem_mrNoneGiven","elem_mr_pres_dt_"); var d="";  if(this.checked){ d=$("#elem_examDate").val();  } $("#"+x).val(d).trigger("click"); });//set curdate
	$("input[id*=elem_mr_pres_dt]").datepicker({dateFormat:"mm-dd-yy"});
	//$("input[id*=elem_mr_pres_dt]").datepicker( "option", "dateFormat", "mm-dd-yy" );
}
var reset_vis_nav_ept;
function reset_vis_nav(){
	if(typeof(reset_vis_nav_ept)=="undefined"){	reset_vis_nav_ept = $('.examleftpan').position().top; }
	var wt = $(window).scrollTop();	var th = $(".workvision")[0].offsetHeight; var sh = $(".examleftpan")[0].offsetHeight;
	var sp = $('.examleftpan').position().top; var wp = $(".workvision").position().top;
	var ct = reset_vis_nav_ept+wt+sh;
	var dt = wp+th;
	if(ct>=dt){
		wt = dt-sh;
	}

	if(wt>=wp){
		var d = wt-wp;
		var t = reset_vis_nav_ept+d;
	}else{t=0;}
	$('.examleftpan').css({"top":t+"px"});
	//
}
function vision_event_handler(){
	wv_activate_menu_click();

	$("#Vision .menu button").attr("tabindex", "-1"); //
	$(".reloadpos").bind("click", function(){setResetValues('vis');});

	wv_activate_mr();
	//add event on tab change

	//vision links
	//alert($('.ar .nav-tabs a').length);
	$('#Vision .nav-pills a').bind("click", function(){
		var t = $(this).attr('href');
		var v = '';
		if(t=='#pill_ar' ){
			$("#elem_visArOdSel1").removeClass("hidden");
			$("#elem_visCycArOdSel1").addClass("hidden");
			if($('#pill_ar').hasClass("in")){ v = 'popAR';}
		}else if(t=='#pill_cyc_ar' ){
			$("#elem_visArOdSel1").addClass("hidden");
			$("#elem_visCycArOdSel1").removeClass("hidden");
			if($('#pill_cyc_ar').hasClass("in")){ v = 'popCycAR';}
		}else if(t=='#pill_bat'){
			$("#bat .hdr div").addClass("hidden");
			if($('#pill_bat').hasClass("in")){ v = 'popBAT';}
		}else if(t=='#pill_pam'){
			$("#bat .hdr div").removeClass("hidden");
			if($('#pill_pam').hasClass("in")){ v = 'popPAM';}
		}

		//var x = $(t).c1ss();
		//console.log(x);
		if(v != ''){callPopup('',v,'1250','760');}
		else{$(this).tab('show'); }
		return false;
	});
	//
	var menu = function(o){ var c = $(o).hasClass("in") ? "up" : "down"; $(".examleftpan a[href='#"+o.id+"'] span").removeClass("glyphicon-menu-up glyphicon-menu-down").addClass("glyphicon-menu-"+c);  };
	var menu_el= '#Distance, #PC, #MR, #LASIK, #OtherVisionExams, #ContactLens';
	$(menu_el).each(function(){menu(this);});
	$(menu_el).on('shown.bs.collapse', function(){ var t = $(this).attr('id'); if($("#"+t).hasClass("in")){ scroll_into_view("#"+t);  }; menu(this);  });
	$(menu_el).on('hidden.bs.collapse', function(){ var t = $(this).attr('id'); $(window).triggerHandler("scroll"); menu(this); });
	$(window).scroll(function(){ 	reset_vis_nav();		});	 //capture scroll
	$("#MR .dv_glph_hdr .header :checkbox").bind("click", function(){ if($(this).prop("checked")){ $(this).parents(".header").find(":checkbox[id!='"+$(this).attr("id")+"']").prop("checked", false); } });

	//NC
	$("#Vision .examsectbox .header .glyphicon-ok-circle").bind("click", function(){
			var w = $(this).parents(".examsectbox").parent().attr("id");
			var c,c1="",flg_blur=0;
			if(w=="ar"||w=="bat"){
				var x = $("#"+w+" table.in").attr("id");
				if(x=="pill_ar"){
					c1+="#elem_visArOdSel1, #elem_visArRefPlace";
				}else if(x=="pill_cyc_ar"){
					c1+="#elem_visCycArOdSel1, #elem_visArRefPlace";
				}else if(x=="pill_pam"){
					c1+="#elem_visPam, #elem_visPamOdSel2";
				}
				c=$("#"+x);
			}else{
				w= $(this).parents("div[id*=pc]").attr("id")||"";
				if(w!="" && w.indexOf("pc")!=-1){
					c=$("#"+w);
				}else{
					w= $(this).parents("div[id*='row_mr_']").attr("id")||"";
					if(w!="" && w.indexOf("row_mr_")!=-1){
						c=$("#"+w);
						flg_blur=1;
					}else{
						c=$(this).parents(".examsectbox");
					}
				}
			}
			if(c){c.find(":input").each(function(){var v = $(this).val();if(v!="" &&v !="20/"){ var thid = $(this).attr("id"); if( typeof(thid)!="undefined" && thid.indexOf("elem_addvisDisOdSel")!=-1){ return true; }  $(this).trigger("change"); if(flg_blur==1 && typeof(this.id)!='undefined' && this.id.indexOf("OdS")!=-1){ $(this).triggerHandler("blur"); c.find(":checked[name*=elem_mrNoneGiven]").each(function(){ $(this).prop("checked", false).trigger("click"); }); flg_blur=0; }  }});}
			if(c1!=""){	$(""+c1).each(function(){var v = $(this).val();if(v!="" &&v !="20/"){ $(this).trigger("change"); }});}
		});
}

//14-01-2016: Myopia and Hyperopia are primary diagnosis and they are getting overwritten Presbyopia which is a secondary diagnosis.   If Pt is Myopic (- sph)  or Hyperopic (+Sph) and the also Presbiopic (Add)  then the Primary diagnosis should be listed first and then the Secondary with corresponding Dx Codes
function add_opyia_assessment(enm, val){
	if(typeof(flgRefDig)=="undefined"){ flgRefDig=1; }//ok
	if(flgRefDig==0){ return; }//explicite no
	if(typeof(flgRefGivenOnly)=="undefined"){ flgRefGivenOnly=0; }
	if(typeof(flgPhyRefSet)=="undefined"){ flgPhyRefSet=0; }
	//if(flgPhyRefSet=="0"){return;}
	var etar_v=$.trim(val);
	if(etar_v!=""){
	var v_icd_10 = $("#hid_icd10").val();
	var t_as="", t_dx="", site="", t_as_2="", t_dx_2="", site_2="" ;

	var s_od, s_os, ad_od, ad_os,mr_givn;

	if(enm.indexOf("_3")!=-1){
		s_od=$(":input[name=elem_visMrOtherOdS_3]").val(), s_os=$(":input[name=elem_visMrOtherOsS_3]").val(),
		ad_od=$(":input[name=elem_visMrOtherOdAdd_3]").val(), ad_os=$(":input[name=elem_visMrOtherOsAdd_3]").val();
		mr_givn = $(":checked[name=elem_mrNoneGiven3]").length;
	}else if(enm.indexOf("Other")!=-1){
		s_od=$(":input[name=elem_visMrOtherOdS]").val(), s_os=$(":input[name=elem_visMrOsS]").val(),
		ad_od=$(":input[name=elem_visMrOtherOdAdd]").val(), ad_os=$(":input[name=elem_visMrOsAdd]").val();
		mr_givn = $(":checked[name=elem_mrNoneGiven2]").length;
	}else{
		s_od=$(":input[name=elem_visMrOdS]").val(), s_os=$(":input[name=elem_visMrOsS]").val(),
		ad_od=$(":input[name=elem_visMrOdAdd]").val(), ad_os=$(":input[name=elem_visMrOsAdd]").val();
		mr_givn = $(":checked[name=elem_mrNoneGiven1]").length;
	}

	//
	s_od=(typeof(s_od)=="undefined") ? "" : $.trim(s_od);if(""+s_od.toLowerCase()=="plano"){s_od="";}
	s_os=(typeof(s_os)=="undefined") ? "" : $.trim(s_os);if(""+s_os.toLowerCase()=="plano"){s_os="";}
	ad_od=(typeof(ad_od)=="undefined" || parseFloat(ad_od)<=0) ? "" : $.trim(ad_od);
	ad_os=(typeof(ad_os)=="undefined" || parseFloat(ad_os)<=0) ? "" : $.trim(ad_os);
	mr_givn=(typeof(mr_givn)=="undefined") ? 0 : $.trim(mr_givn);

	if(flgRefGivenOnly=="1" && mr_givn<=0){return;}

	if((ad_od!="" && ad_od!="+")||(ad_os!="" && ad_os!="+")){
		t_as_2="Presbyopia"; t_dx_2=(v_icd_10=="9")? "367.4" : "H52.4";
		if(ad_od!=""){site_2="OD";}
		if(ad_os!=""){site_2=(ad_od!="")?"OU":"OS";}
	}

	if((s_od!="" && s_od!="-" && s_od!="+") || (s_os!="" && s_os!="-" && s_os!="+")){
		if(s_od.indexOf("-")!=-1||s_os.indexOf("-")!=-1){t_as="Myopia"; t_dx=(v_icd_10=="9")? "367.1" : "H52.1-";}
		else{t_as="Hyperopia"; t_dx=(v_icd_10=="9")? "367.0" : "H52.0-";}


		if(t_dx == "H52.1-"){
			if(s_od!="" && s_od.indexOf("-")!=-1){site="OD";}
			if(s_os!="" && s_os.indexOf("-")!=-1){site=(site!="")?"OU":"OS";}
		}else{
			if(s_od!=""){ site="OD"; }
			if(s_os!=""){site=(s_od!="")?"OU":"OS";}
		}
		var r="";
		if(site=="OD"){r=1;}else if(site=="OU"){r=3;}else if(site=="OS"){r=2;}
		t_dx=t_dx.replace("-",r);
	}

	if(t_as!=""||t_as_2!=""){
		cn_clearOpiaAssessments(t_as,t_as_2);
		if(t_as!=""){addAssessOption( t_as, "", "",site,"",t_dx); }
		if(t_as_2!=""){addAssessOption( t_as_2, "", "",site_2,"",t_dx_2); } //cn_clearOpiaAssessments();
	}

	}
}

//function add assessment of myopia / presbyopia
function cn_clearOpiaAssessments(a1, a2){
	var ass_nm = "textarea[id*=elem_assessment]";
	$(ass_nm).each(function(indx){
		if(this.id.indexOf("elem_assessment_dxcode")!=-1){ return true; }
		var tmp = $.trim(this.value);
		if($.trim(tmp)!=""){
			tmp=tmp.toLowerCase();
			//pure it
			var ar_tmp = tmp.split(";");
			tmp = $.trim(ar_tmp[0]);
			if(tmp=="presbyopia" || tmp=="myopia" || tmp=="hyperopia"){
				var flgdo=1;
				//a1
				if(typeof(a1)!="undefined" && a1!=""){
					a1=a1.toLowerCase();
					if(tmp==a1){flgdo=0;a1="";}
				}
				//a2
				if(typeof(a2)!="undefined" && a2!=""){
					a2=a2.toLowerCase();
					if(tmp==a2){flgdo=0;a2="";}
				}

				if(flgdo==1){
					clearAssessRow(this.id);
				}
			}
		}
	});
}

function vis_show_sec(wh,flgshow){
}

//Set All Acuity fields to white in Dis and Near--
function setActuity(obj){ }
//Set All Acuity fields to white in Dis and Near--

//Add Sign
function addArthSign(obj){
	var valElem = $.trim(obj.value);
	obj.value = "";
	var ptrn = "^[\+|\-]";
	var reg = new RegExp(ptrn,'g');
	var sign = valElem.match(reg);
	if(valElem!="" && !isNaN(valElem)){
		if(sign != null){valElem = valElem.replace(reg,"");}else{sign="+";}
		if(valElem != ""){
			valElem = parseFloat(valElem).toFixed(2);
			obj.value = sign+valElem;
		}
	}
}

//Set OS
function setOs(obj){

	var n = obj.name;
	var valElem = $.trim(obj.value);
	//Add +
	if(( n.indexOf("Add") != -1 ) && (valElem != "")){
		addArthSign(obj);
	}

	/*
	**[AK: 12/5/11 - Yes please disable this function.  Let each eye be independently controlled]
	//copy
	var tn = n.replace("Od","Os");
	var t = gebi(tn,1)[0];
	if(t){t.value = obj.value;}
	if(typeof t.onkeyup == "function"){ t.onkeyup();}
	changeVisionStatus(t,1);
	*/
}

function scrollDropDowns(obj){
	var parentModal = $(obj);

	if(parentModal.length){
		var mainContainer = parentModal.find('.modal-body');
		var inputElem = mainContainer.find("input[type=text]");
		$.each(inputElem, function(id, val){
			var inputTxt = this.id ; // val.input;
			//Get all inputs
			mainContainer.find('input[id$='+inputTxt+']:visible').each(function(id, elem){
				var inputID = $(elem).attr('id');
				var selectID = inputID.replace('text_', '');
				var inputVal = $(elem).val(); inputVal = $.trim(inputVal);
				if(inputTxt == selectID){return true;}
				var selectElem = mainContainer.find('#'+selectID);
				if(selectElem){
					//Get scroll Height
					var scrollHeight = 0;
					if(inputVal != '' && inputVal != "None"){
						//var optCount = selectElem.find('option').length;
						//if(optCount > 0) optCount = optCount / 2;

						var flgc=0;
						selectElem.find('option').each(function(id , optElement){
							//if(id < optCount) scrollHeight += 15;
							if(inputID.indexOf("S_text_input")!=-1 || inputID.indexOf("C_text_input")!=-1 || inputID.indexOf("A_text_input")!=-1){
								if(parseFloat(this.value)<parseFloat(inputVal)){ scrollHeight += 15; }
								flgc=1;
							}else{
								if(this.value==inputVal){ flgc=1; }
								if(flgc==0){scrollHeight += 15;}
							}
						});
						if(flgc==0){ scrollHeight=0; }
					}

					selectElem.animate({ scrollTop: scrollHeight }, 5);
				}
			});
		});
	}
}

//By Jaswant
function callPopup(fileName, popName, width, height,section){
	//CONDITION ADDED TO CALL THIS POP FROM PRS SECTION =>ADD EXTERNAL VA BUTTON - USING FILE /chart_notes/patient_refractive_sheet.php
	if(section!="PRS"){
		if((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1"))){ return;}
	}
	//showOtherForms(fileName, popName, width, height,'Yes');

	var popmodal="";
	popmodal=popName+"Modal";

	//if(popName== "popDistance"){popmodal="popDistanceModal";}
	//if(popName == "popArBat"){popmodal="popArModal";}
	//if(popName == "popPC"){popmodal="popPcModal";}
	//if(popName == "popMR"){popmodal="popMrModal";}

	if(popmodal!=""){
		//get label
		var indx = ""+popName.replace(/popMR/,"");
		var indx2 = ""+popName.replace(/popExtMR/,"");  //SET INDEX FOR PRS EXTERNAL VA WORK
		var ext_mr_lbl = $("#h2_mr_id"+indx).html();   //SET LABEL FOR PRS EXTERNAL VA WORK
		var mr_lbl = $("#h2_mr_id"+indx).html();

		if(top.$("#"+popmodal).length>0){
				/*
				top.$("#"+popmodal).modal("show");
				if(typeof(top.getValuesFromParent)!="undefined"){top.getValuesFromParent(popmodal);}
				return;
				*/
				top.$("#"+popmodal).modal('hide');
				top.$("#"+popmodal+", .modal-backdrop").remove();
		}
		//stop
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"");
		var url=zPath+"/chart_notes/requestHandler.php?elem_formAction=VisPopUp&popName="+popName+"&mr_lbl="+mr_lbl+"&ext_mr_lbl="+ext_mr_lbl;
		//var url=zPath+"/chart_notes/requestHandler.php?elem_formAction=VisPopUp&popName="+popName+"&mr_lbl="+mr_lbl;
		$.get(url, function(d){
				//stop
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"");
				top.$("body").append(d);
				//
				top.$("#"+popmodal).modal("show");
				top.$('#'+popmodal).on('shown.bs.modal', function () {
						if(popName.indexOf("popPC")!=-1 || popName.indexOf("popMR")!=-1 || popName.indexOf("popBAT")!=-1 || popName.indexOf("popExtMR")!=-1){top.$("#"+popmodal).find('.modal-dialog').css({ width:'auto'});}
						top.$("#"+popmodal).find('.modal-dialog').css({ height:'auto', 'max-height':'100%'}); //width:'auto',
						top.$("#"+popmodal).find('.modal-body').css({padding:'2px'});
						var xhgt = top.$(window).height();
						var xwdth = top.$(window).width();
						//SET FOR PRS EXTERNAL VA CASE - AS IT IS ALREADY OPENING IN POPUP SO WE NEED TO ADJUST DIMENSIONS AS PER THEIR PARENT POPUP
						if(section=="PRS"){ xhgt = parent.document.body.clientHeight-120; }

						xhgt = parseInt(xhgt) - (parseInt(top.$(this).find('.modal-footer').css("height")) + 150); //parseInt(top.$(this).find('.modal-header').css("height")) +

						var fun = function(){
								var x = $(this).val();
								$(this).find("option").prop("selected", false);
								$(this).find("option[value='"+x+"']").prop("selected", true);
								var v = $(this).val() || "";
								var tid = $(this).attr("id").replace("_input","_text_input");
								top.$("#"+tid).val(v);
							};
						if(isiPad==1){
							top.$("#"+popmodal+" .modal-body select[data-size]").attr("data-size", 1).prop('multiple',false).bind("blur", fun);
						}else{
							top.$("#"+popmodal+" .modal-body select[data-size]").css({"min-height":xhgt+"px"}).bind("change", fun);
						}

						//
						if(popName.indexOf("popPC")!=-1 || popName.indexOf("popMR")!=-1 || popName.indexOf("popExtMR")!=-1){  top.$("#"+popmodal+" .modal-body").css({"overflow-x":"hidden", "height":xhgt+150}); top.$("#"+popmodal+" .panel-body").css({"padding":"2px"}); }
						if(popName.indexOf("popMR")!=-1){
							top.$("#"+popmodal+" .table").css({"margin-bottom":"2px"});
							top.$("#"+popmodal+" .table td").prop("style", "padding: 2px !important");

						}
						//BELOW CONDITION ADDED FOR PRS EXTERNAL VA WORK
						if(popName.indexOf("popExtMR")!=-1){
							top.$("#"+popmodal+" .table").css({"margin-bottom":"2px"});
							top.$("#"+popmodal+" .table td").prop("style", "padding: 2px !important");

						}
						top.$("#"+popmodal+" .modal-footer").css({"text-align":"center"});//
						if(typeof(top.getValuesFromParent)!="undefined" && popName.indexOf("popExtMR")==-1){top.getValuesFromParent(popmodal);}

						//
						if(typeof(top.bl_exe)!="undefined"){top.$("#"+popmodal+" .bl ").each(function(){ $(this).bind("click", function(){ top.bl_exe(this);}); });   }//

						setTimeout(function(){ scrollDropDowns(top.$("#"+popmodal)); }, 500);
					});
				if(popName.indexOf("popPC")==-1&&popName.indexOf("popMR")==-1){	wv_activate_menu_click("#"+popmodal+" .menu a", 1);	}
			});
	}
}

//AR --
function opArUp(op){

	if(typeof op == "undefined")op="";
	var url=zPath+"/chart_notes/onload_wv.php?elem_action=ScarAR&op="+op;
	var n = "childWinArScan";
	top.fmain.showOtherForms(url,n,810,600,0);
	//stopClickBubble();

}
//AR --

//Add PC/MR
var flg_add_more_vision=0;
function add_more_vision(w){
	var indx=0;
	var wh = (w=="pc") ? "#PC" : "#MR";
	$(wh+">.row>div[id]").each(function(){ if($(this).css("display")=="none"){return false;} var id=$(this).attr("id"); id=id.replace(/pc|row_mr_/g,""); id = parseInt(id); if(id>indx){indx=id;}  });
	indx+=1;
	if(w=="pc"){if($("#pc"+indx).length>0){ $("#pc"+indx+", #pc"+(indx+1)+", #pc"+(indx+2)).show();return; }}
	else{if($("#row_mr_"+indx).length>0){ $("#row_mr_"+indx+", #row_mr_"+(indx+1)).show();return; }}
	if(flg_add_more_vision==1){return;}
	flg_add_more_vision=1;
	var pcln = $("#PC").find(".row>div[id*=pc]").length;
	var mrln = $("#MR").find("div[id*=row_mr_]").length;
	var url=zPath+"/chart_notes/requestHandler.php?elem_formAction=addVisionPCMR&indx="+indx+"&w="+w+"&ctmpMr3="+$("#ctmpMr3").val()+"&pcln="+pcln+"&mrln="+mrln;
	if(typeof(setProcessImg)=="function"){setProcessImg("1","vision");}
	$.get(url, function(data){ flg_add_more_vision=0; if(typeof(setProcessImg)=="function"){setProcessImg("0","vision");} if(data!=""){$(wh+">.row").append(data);} wv_activate_menu_click();wv_activate_mr();  	});
}

function clearPc(w, rem){
	if(typeof(rem)!="undefined"&&rem==1){
		$("#pc"+w+", #pc"+(w-1)+", #pc"+(w-2)).hide();
	}
}

function chk_mr_given(o){if($(o).val()!=""){	var tmpdt = $(o).val(); var a=o.id.replace(/elem_mr_pres_dt_/,"elem_mrNoneGiven"); if($("#"+a).prop("checked")==false){ $("#"+a).trigger("click"); $(o).val(tmpdt);}}}

function vis_add_menu(o, mid, eid, h, t){

	if(typeof(o)!="undefined"){

		if(!$(o).hasClass("dropdown-toggle")){

			if(typeof(h)=="undefined"){

				var g=""+mid;
				if(g!=""){
					if($("#Vision ."+g+"_ul").length<=0){
						if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","Vision");
						$.get(zPath+"/chart_notes/requestHandler.php?elem_formAction=get_sb_menu&wh="+g+"&req_ptwo=1&mid="+mid+"&eid="+eid,function(d){
									if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","Vision");
									if(typeof(d)!="undefined" && d!=""){
										vis_add_menu(o, mid, eid, d, 1);
									}
							});
					}else{
						var d = $("#Vision ."+g+"_ul").get(0).outerHTML;
						if(typeof(d)!="undefined" && d!=""){vis_add_menu(o, mid, eid, d);}
					}
					return;
				}
			}else{

				var trgtid = ''+$(o).parent().parent().find(".form-control").attr("id");
				$(o).parent().append(h);
				$(o).attr("data-toggle", "dropdown");
				$(o).attr("data-trgt-id", trgtid);
				$(o).addClass("dropdown-toggle");
				$(o).unbind("click",'vis_add_menu');
				$(o).dropdown();

				// link ---
				if(mid=="menu_visit_type"){
					$( "#el_visit_ig ul" ).bind("click", function(){stopClickBubble();});
					$( "#el_visit_ig ul a" ).bind("click", function(){

							var x = "elem_ptVisit_chk";
							var y = "elem_ptTesting";
							var h=top.$(this).html();
							var c=$(this).parent().parent().find(".dropdown-header").html();

							var w="";
							if(c=="VISIT"){w=x;}else if(c=="TESTING"){w=y;}
							if(w!=""){

								var a= $("#"+w).val(); //el_visit
								if(h == "Other"){h="";a="";}
								if(typeof(a)!="undefined" && a!=""){
									if(c=="VISIT"){if(a.indexOf(h)==-1&&a.indexOf(h+",")==-1){h=h+", "+a;}else{return;}}
									else if(c=="TESTING"){ if(a!=h){ a=h; }else{return;} }
								}

								$("#"+w).val(h);
								//concat
								var a = $("#"+x).val()||"";
								var b = $("#"+y).val()||"";
								var w="";
								if(a!=""){ w +=a;  if(b!=""){w += " - ";}  }
								if(b!=""){  w += b;}
								$("#el_visit").val(w);
								$("#elem_masterPtVisit").val(a).triggerHandler("change");
								$("#elem_masterTesting").val(b).triggerHandler("change");

							}
							stopClickBubble();
						});
				}else{
					wv_activate_menu_click("."+mid+" a");
				}
				// link ---

				if(typeof(t)!="undefined" && t == "1"){

					$(o).dropdown("toggle");
				}

			}
		}
	}
}

function add_more_actuity(hh){

	indx = "4";
	var iddv = "div_pop_acuity"+indx;
	var sel_val = $("#elem_addvisDisOdSel").val();
	if(typeof(hh)!="undefined"){
		if(hh==1){
			$("#"+iddv+"").addClass("hidden");

			if(sel_val!=""){$("#flg_addvisDisOdSel").removeClass("hidden");}else{$("#flg_addvisDisOdSel").addClass("hidden");}

		}else if(hh==2){

			$("#"+iddv+" input[id]").val('');
		}
		return;
	}

	//var indx = $(":input[id*=elem_visDisOuSel]").length;

	if($("#elem_visDisOdSel"+indx+"").length>0){
		if(sel_val!=""){
			$("#"+iddv).removeClass("hidden");
			var wh = $("#elem_addvisDisOdSel").offset();
			$("#"+iddv).css({"top":wh.top+"px", "left":wh.left+"px"});
			$("#elem_visDisOdSel"+indx).val(sel_val);

			$("#"+iddv).draggable({'handle': '.header'});

		}else{
			$("#"+iddv).addClass("hidden");
			$("#"+iddv+" input[id]").val('');
			$("#flg_addvisDisOdSel").addClass("hidden");
		}

	}else{

	}
}

//Vision -------

//--
function closePopUps()
{
	$("#sliderRight").remove();
	$("#btn_attest_scribe, #btn_attest_attend_scribe, #btn_attest_teach").popover("destroy");
	top.$('#spanMoreCoMangPhy, #spanMoreRefPhy, #spanMorePCP').popover("destroy");
	top.$("body").click();//clicked parent to hide menus
}
function close_modal_js(obj){
	$(obj).parent().parent().remove();
}
//-----

//Save CD RV values --
var flg_saveCDRV="";
function saveCDRV(){
	if(finalize_flag==1&&isReviewable!=1){return;}
	var fid = $("#elem_masterId").val();
	var cd_od = $.trim($("input[name=elem_rvcd_od]").val());
	var cd_os = $.trim($("input[name=elem_rvcd_os]").val());
	if(cd_od=="" && cd_os==""){ /*alert("please fill CD values.");*/ return; }

	//
	//if(cd_od=="" || cd_os==""){
		if(typeof(flg_saveCDRV)=="undefined" || flg_saveCDRV==""){
			flg_saveCDRV = setTimeout(function(){ saveCDRV(); }, 5000);
			return;
		}
	//}else{
		flg_saveCDRV = clearTimeout(flg_saveCDRV);
	//}

	var params = { "elem_saveForm":"saveCDRV",
				"fid":fid,"elem_cdValOd":cd_od,"elem_cdValOs":cd_os};
	if(typeof(setProcessImg)=="function")setProcessImg("1","fundus_exam");
	$.post("saveCharts.php",params,function(data){
			if(typeof(setProcessImg)=="function")setProcessImg("0","fundus_exam");
			if(data!=0){window.status="Error: C:D values are not saved.";
			}else{loadExamsSummary("fundus_exam");}});

}
//Save CD RV values --
//Periphery Not Exam -------------

function checkPneEye(x){
	var vchk = "elem_periNotExamined";
	var veye = "elem_peri_ne_eye";
	if(x.id!=vchk&&x.id!=veye){
		vchk = "elem_periNotExamined_peri";
		veye = "elem_peri_ne_eye_peri";
	}

	if(x.type=="checkbox"){
		var o =  $("#"+veye);
		if($("#"+vchk).prop("checked") && o.val()==""){
			o.val("OU");

		}else if(!$("#"+vchk).prop("checked")){
			o.val("");
		}
	}else if(x.type=="select-one"){
		var o =  $("#"+vchk);
		if($("#"+veye).val()!="" && o.prop("checked") == false){
			o.prop("checked", true).triggerHandler("click");
		}else if($("#"+veye).val()==""){
			o.prop("checked", false);
		}
	}
	utElem_capture(o[0]);

	//Active sides
	if(typeof(examName)!="undefined" && examName == "Fundus"){
		if($("#"+vchk).prop("checked")){
			if($("#"+veye).val()=="OD"||$("#"+veye).val()=="OU"){
				$("#"+vchk).parents(".tab-pane").find("textarea[id*=AdOptionsOd]").trigger("click");
			}
			if($("#"+veye).val()=="OS"||$("#"+veye).val()=="OU"){
				$("#"+vchk).parents(".tab-pane").find("textarea[id*=AdOptionsOs]").trigger("click");
			}
		}
	}
}

function savePNE(o){
	if(finalize_flag==1&&isReviewable!=1){return;}

	var vchk = "elem_periNotExamined";
	var veye = "elem_peri_ne_eye";
	var ex = "Ret";
	if(o.id!=vchk && o.id!=veye){
		vchk = "elem_periNotExamined_peri";
		veye = "elem_peri_ne_eye_peri";
		ex = "Peri";
	}

	var fid = $("#elem_masterId").val();
	var pne = $(":checked[name="+vchk+"]").length > 0 ? 1 : 0 ;
	var pne_i = $(":input[name="+veye+"]").val();
	var params = { "elem_saveForm":"SavePeriphery",
				"fid":fid,"pne":pne,"pne_i":pne_i,"ex":ex};
	if(typeof(setProcessImg)=="function")setProcessImg("1","fundus_exam");
	$.get("saveCharts.php",params,function(data){
			if(typeof(setProcessImg)=="function")setProcessImg("0","fundus_exam");
			if(data!=0){window.status="Error: periphery Not exam is not saved.";
			}else{loadExamsSummary("fundus_exam");}});
}

//Periphery Not Exam -------------
//Reset Buttons -------------------
function setResetValues(val,site,confrmed){

	if((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1"))){
		return;
	}

	/*
	//CVF + Amsler Grid + Contact Lens
	if(val=="vis"){
		var act_tab = $("#equalheight .tab-content").children("div.active").attr("id");
		if(act_tab=="CVF"){
			funReset_cvf();return;
		}else if(act_tab=="AmslerGrid"){
		}else if(act_tab=="ContectLens"){
		}
	}
	//--
	*/

	if(val=="vis")val="Vision";
	//var c = confirm("Do you want to reset "+val+" exam(s)?");
	if(typeof(confrmed)=='undefined'){
		top.fancyConfirm("Do you want to reset "+val+" exam(s)?", '', "top.fmain.setResetValues(\'"+val+"\', \'"+site+"\',true)");
		c=false;
	}else{
		c=true;
	}
	if(c){
		if(val=="Vision"){
			var a = "#Vision :input[type=text], "+
					"#Vision textarea,#Vision select, "+
					"#elem_statusElements ";
			$(a).val("").removeClass("active").addClass("inact");
			$("#Vision .acuity").val("20/");
			$("#Vision :input[type=checkbox]").attr("checked",false);
			$("#a_pcPrism1,#a_pcPrism2,#linkpc3,#a_mrPrism1,#a_mrPrism2,#indBat,#indMR3,#sptitle_w4dot").removeClass("positive");
			$("#Vision #el_lasik_userid").val(authUserID);
		}else{
			if(typeof(site)=="undefined"){ site=""; }

			var url = "saveCharts.php";
			var params="elem_saveForm=setResetValues";
				params+="&elem_section="+val;
				params+="&elem_fid="+$("#elem_masterId").val();
				params+="&site="+site;
				params+="&cryfwd_form_id="+$("#cryfwd_form_id").val();
			if(typeof(setProcessImg)=="function")setProcessImg("1","");

			$.post(url, params,
				function(data){
				//stop
				if(typeof(setProcessImg)=="function")setProcessImg(0,"");
				//document.write(data);
				if(data == "1"){
					loadExamsSummaryAll();
				}
			}
			,"");
		}
	}
	stopClickBubble();
}
function setResetStart(s){ setResetValues('All',s);  }

//Reset Buttons -------------------

//-- Progness Notes ----

function cn_progess_notes(op){
	if(user_type!=1 && logged_user_type != 6){return;}
	var sv_per = ((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1"))) ? 0 : 1;

	if(op==2){ $("#dv_cn_progess_notes").remove(); return;  }

	//start
	if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"btnPrgs");

	var params="";
	var f = $("#elem_masterId");
	params += "&elem_formId="+f.val();
	var p = $("#elem_patientId");
	params += "&elem_patientId="+p.val();
	var d = $("#elem_dos");
	params += "&elem_dos="+d.val();
	params += "&sv_per="+sv_per;

	//load prevoius
	if(op==3){
		var prv_frm_id = $("#el_all_prg_note").val();
		params += "&load_prev_prog="+prv_frm_id;
	}

	var url = "requestHandler.php?elem_formAction=get_cn_progess_notes";
	if(op==4){//print
		params += "&op=print";
		//console.log(url+params);
		$.get(url+params, function(d){
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"btnPrgs");
			if(d) {
				//top.fmain.showOtherForms('../common/new_html2pdf/createPdf.php','print_cn_prgnote_win','1000','400');
				top.fmain.showOtherForms(zPath+'/../library/html_to_pdf/createPdf.php?file_location='+d,'print_plan_win','1000','400');
			}else {
				top.fAlert('No progress note exists');
			}
		});
		return;
	}

	if(op==1){
		params += "&op=save";
		params += "&txt="+$("#el_cpn_txt").val();
		$.post(url,params, function(d){
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"btnPrgs");
			cn_progess_notes(2);
		});
		return;
	}

	$.get(url+params, function(d){
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"btnPrgs");
		cn_progess_notes(2);
		$("body").append("<div id=\"dv_cn_progess_notes\" class=\"modal-dialog modal-lg\"><div class=\"modal-content\"><div class=\"modal-header bg-primary\"><button type=\"button\" class=\"close\" onclick=\"cn_progess_notes(2)\">×</button><h4 class=\"modal-title\">Progress Note</h4></div><div class=\"modal-body\">"+d+"</div></div></div>");
	});
}

//-- Progness Notes ----


// Post Save exam --
function AfterSave(obj){
		var exm = obj.Exam.toLowerCase();
		if(exm == "ee"){
			exm = "external";
		}else if(exm == "iop" || exm == "gonio"){
			exm = "iop_gon";
		}else if(exm == "rv"){
			exm = "fundus_exam";
		}else if(exm == "vf-gl" || exm == "oct-rnfl"){
			exm = "vf_oct_gl";
			if($("#vf_oct_gl").length<=0){	return;}
		}

		//
		loadExamsSummary(exm);

		//EOM --

		if(exm == "eom"){
			loadExamsSummary("dv_cpctrl");
			loadExamsSummary("dv_stereopsis");
			loadExamsSummary("dv_w4dot");
		}

		//EOM --

		//RV --
		if(exm == "fundus_exam"){
			//ICD - 10 DM coding
			z_flg_diab_sb="3";//selection on click of superbill
			sb_checkDiabetes();
			var ir_as = obj.arDrawInter[0],ir_dx = obj.arDrawInter[1],ir_pln = obj.arDrawInter[2],ir_dxid = obj.arDrawInter[3];
			addAssessOption(ir_as,ir_pln,0,"OU","",ir_dx,'','',ir_dxid);
		}
		//RV --

		//

		//Set values in A/p
		var arrMain=[];
		//
		if(obj.arExamDone != null&&obj.arExamDone.length>0){
			var arED = obj.arExamDone;
			var len = arED.length;
			var valEye, chkExm_org, tmp;
			for( var i=0;i<len;i++ ){
				tmp = arED[i].split("~*~");
				chkExm_org = $.trim(tmp[0])||"";
				valEye = $.trim(tmp[1])||"";
				if(chkExm_org!=""){ arrMain[arrMain.length]=[chkExm_org,valEye];}
			}
		}

		len = arrMain.length;
		for(var i=0;i<len;i++){
			chkExm = arrMain[i][0];
			valEye = arrMain[i][1];
			setAssessOption(chkExm,false,valEye,len);
		}
		//Set values in A/p

		//Tech Mandatory
		mandotry_chk('1');

	}
//--


// Get Med List for Sx Procedure / Medications
function get_MedList(num)
{
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","elem_ptVisit_chk");
	$.get(top.JS_WEB_ROOT_PATH + "/interface/chart_notes/requestHandler.php?elem_formAction=show_med_list"+num,function(data){
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","elem_ptVisit_chk");
		$("body").append(data);
		if(num==1){$("#medicationDiv").show();}
		if(num==6){$("#surgeryDiv").show();}
	});
}

function getMedHx()
{
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","divOcMeds");
	$.get(top.JS_WEB_ROOT_PATH + "/interface/chart_notes/onload_wv.php?elem_action=GetMedHx",function(data){
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","divOcMeds");

		$("#divOcMeds").html(data);

	});
}


var SxWinObj = false;
function openMedHX(op, winH)
{
	if(op == "medication" || op == "medication_grid" ){
		var ec = $("#tbl_ocu_grid").data("edit_chart"), prv_frmid="0";
		if(op == "medication_grid" && typeof(ec)!="undefined" && ec == 1 ){if(elem_per_vo=='1' || ((finalize_flag == "1") && (isReviewable != "1"))){}else{prv_frmid=$("#elem_masterId").val();}}
		divH = winH - 130;
		winH = winH - 100;
		window.open(top.JS_WEB_ROOT_PATH +"/interface/Medical_history/index.php?showpage=medication&callFrom=WV"+(op == 'medication_grid' ? '&subcall=grid' : '')+"&divH="+divH+"&prv_frmid="+prv_frmid+"",'Medications','height='+winH+',width=1400,top=10,left=20,status=1');
	}
	else if(op == "sxPro" || op == "sxPro2"){
		divH = winH - 230;
		winH = winH - 150;
		flgSxIco= (op == "sxPro2") ? "1" : "0";
		var w = document.body.clientWidth;
		SxWinObj = window.open(top.JS_WEB_ROOT_PATH +"/interface/Medical_history/index.php?showpage=sx_procedures&callFrom=WV&divH="+divH+"&flgSxIco="+flgSxIco,'Sx Procedures','height='+winH+',width='+w+',top=10,left=20,status=1');
	}
	else if(op == "allergies"){
		divH = winH - 230;
		winH = winH - 150;
		window.open(top.JS_WEB_ROOT_PATH +"/interface/Medical_history/index.php?showpage=allergies&callFrom=WV&divH="+divH+"",'Allergies','height='+winH+',width=1060,top=10,left=20,status=1');
	}

}

function gebi(id,t)
{
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

var tkGenHlth;
function showMedList(id,frld)
{
	if(typeof(gebi)=="undefined") return;
		if(id == 1){
			var o = gebi('medicationDiv');
			if(o){
				o.style.display = 'block';
			}else{
				get_MedList(1);
			}
		}
		if(id == 6){
			if(gebi('surgeryDiv')){
			gebi('surgeryDiv').style.display = 'inline';
			}else{
				get_MedList(6);
			}
		}

	if(id == "PMH"){
		if(gebi('genHealthDiv_wv')){
			if(gebi('genHealthDiv_wv').innerHTML==""||frld==1){
				if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","genHealthDiv_wv");
				$('#genHealthDiv_wv').load('onload_wv.php',{ 'elem_action':'GetGenHealth'},function(){
					if(gebi('genHealthDiv_wv').innerHTML!=""){
						if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","genHealthDiv_wv");
						$('#genHealthDiv_wv').modal("show");set_modal_height($('#genHealthDiv_wv'));
						$('#genHealthDiv_wv').find('select.selectpicker').selectpicker();
						$('#genHealthDiv_wv').find("input.datepicker").datepicker({dateFormat:z_js_dt_frmt});
						clearTimeout(tkGenHlth);
						if(isERPPortalEnabled && !postpone_pghd){setTimeout(function(){ load_pghd_chart_reqs(); }, 1000);}
					}
				});
			}
			else{
				if(gebi('genHealthDiv_wv').innerHTML!=""){
					$('#genHealthDiv_wv').modal("show");
					clearTimeout(tkGenHlth);
					if(isERPPortalEnabled && !postpone_pghd){setTimeout(function(){ load_pghd_chart_reqs(); }, 1000);}
				}
			}
		}
	}
}

function load_pghd_chart_reqs(){
	var oPUF = (typeof(top.oPUF)!="undefined") ? top.oPUF : top.fmain.oPUF;
	var n="pghd_reqs";
	if(!oPUF[n] || !(oPUF[n].open) || (oPUF[n].closed == true)){
		oPUF[n] = window.open(top.JS_WEB_ROOT_PATH + "/interface/Medical_history/get_pghd_req_med_hx.php?callFrom=WV",n,'location=1,status=1,resizable=1,left=10,top=1,scrollbars=1,width=1000,height=500');
		oPUF[n].focus();
	}else{
		oPUF[n].focus();
	}

}

function hideMedListExe(id, frc)
{
	if(id == 1){
		var o = gebi('medicationDiv');
		if(o) o.style.display = 'none';
	}
	if(id == 6){
		$('#surgeryDiv').fadeOut('fast');
	}
	if(id == "PMH"){
		if((tkGenHlth) || (frc == 1)){
			if(document.getElementById('genHealthDiv_wv')){
				//$('#genHealthDiv_wv').fadeOut('10');
				$('#genHealthDiv_wv').modal('hide');
			}
			clearTimeout(tkGenHlth);
		}
	}
}

function hideMedList(id)
{
	if(id == 1){
		if(typeof(gebi)=="undefined")return;
		var o = gebi('medicationDiv');
		if(o) o.style.display = 'none';
	}
	if(id == 6){
		$('#surgeryDiv').fadeOut('fast');
	}
	if(id == "PMH"){
		tkGenHlth = setTimeout("hideMedListExe('"+id+"')", 500);
	}
}

function showAllergy(val)
{
	$("#allergies_patient").remove();

	if(val==1){
		var url = top.JS_WEB_ROOT_PATH + "/interface/chart_notes/requestHandler.php";
		var params="elem_formAction=PtAllergy";
		if(typeof(setProcessImg)=="function")setProcessImg("1","btn_allergy");

		$.post(url, params,
			function(data){
			if(typeof(setProcessImg)=="function")setProcessImg(0,"btn_allergy");
			if(data != ""){
				$("body").append(""+data);
				$('#allergies_patient').show();
			}
		}
		,"");
	}
}


function sc_showExamDetails(inx, mval){
	var symp = todoId = searchVal = "";

	if(inx=="menu"){
		if(typeof(mval)=="undefined" || mval==""){return;}
		searchVal = symp = encodeURI(mval);

	}else{
		var symp = $(":hidden[name=elem_symptom_"+inx+"]").val();
		searchVal = symp = encodeURI(symp);

		var todoId = $(":hidden[name=elem_todoid_"+inx+"]").val();
		if(typeof(todoId) == "undefined" || todoId==""){ todoId=""; }

	}

	//icd 1 or 9
	var vicd10 = $("#hid_icd10").val();
	if(typeof(vicd10)=="undefined"){
		if(window.opener && window.opener.top.fmain){
			vicd10 = window.opener.top.fmain.$("#hid_icd10").val();
		}
	}

	//dos
	var dos = $("#elem_dos").val();
	if(typeof(dos)=="undefined"){
		if(window.opener && window.opener.top.fmain){
		dos = window.opener.top.fmain.$("#elem_dos").val();
		}
	}

	var url = top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php';
	var params = "elem_formAction=SmartChartDetail&symp="+symp+"&todoId="+todoId+"&searchVal="+searchVal+"&empty_symp_allow=1&icd10="+vicd10+"&dos="+dos;

	$("#div_sc_con_detail").modal('hide');

	//$( "#elem_search_symptopms" ).autocomplete( "close" );
	//
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","div_sc_con");

	//alert(params);

	$.get(url, params,
		function(data){
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","div_sc_con");

			if(data != ""){
				if($('#div_sc_con_detail').length > 0){
					$("#div_sc_con_detail").modal('hide');
					$('#div_sc_con_detail, .modal-backdrop').remove();
				}
				$("body").append(data);
				$("#div_sc_con_detail").modal('show');
				//$("#div_sc_con_detail").draggable({handle:"th"});
				//date
				$( ".dacry input[type=text], .lacsci input[type=text], .ctmri input[type=text]" ).datepicker(); //{timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false}
				utElem_setBgColor("div_sc_con_detail");
				// typeahead --
				$( "#div_sc_con_detail textarea, #div_sc_con_detail input[type=text]" ).bind("focus", function(){ if(!$(this).hasClass("ui-autocomplete-input")){cn_ta1_no_assess(this, '');};  });
				// --
				//
				newET_setGray_v3();

			}else{
				top.fAlert("No Assessment policy defined.");
			}
		}
	);
}

function sc_check_bl(obj){
	var flg=0;
	var nm_2="";
	$("#d_"+obj+"_od :input").each(function(i){
		if(this.type=="button"){return;}
		nm_2 = this.name.replace(/Od/g, "Os");
		if($("#d_"+obj+"_os :input[name="+nm_2+"]").length==0){
			if(this.value=="RUL"){nm_2 = nm_2.replace(/rul/g, "lul");}
			else if(this.value=="RLL"){nm_2 = nm_2.replace(/rll/g, "lll");}
		}
		if(this.type=="checkbox"){
			$("#d_"+obj+"_os :input[name="+nm_2+"]").prop("checked",this.checked).each(function(){ utElem_capture(this); });
			if(this.checked && flg==0){ $("#d_"+obj+"_os :input[name="+nm_2+"]").triggerHandler("click"); flg=1; }
		}else{
			$("#d_"+obj+"_os :input[name="+nm_2+"]").val(this.value).each(function(){ utElem_capture(this); });
			if($.trim(this.value)!="" && flg==0){ $("#d_"+obj+"_os :input[name="+nm_2+"]").triggerHandler("click"); flg=1; }
		}
	});
}



function sc_insertMultiPlan1(indx){

	var strOrderId = '';
	var str = '';

	//auto check assessment --
	if($("#sc_elem_assess"+indx).length>0 && $("#sc_elem_assess"+indx).prop("checked")==false){
		if($('form[name=frmSC_Exam]  :checked[name*=elem_multiPlanSCPop'+indx+'][type=checkbox]').length>0){ $("label[for=sc_elem_assess"+indx+"]").trigger("click");  }
	}
	//--

	$('form[name=frmSC_Exam]  :checked[name*=elem_multiPlanSCPop'+indx+']').each(function(){

		//stop eye elem
		if(""+this.name.indexOf("_eye")==-1){

			var val=""+this.value;
			if(this.id.indexOf("elem_multiPlanSCPop"+indx+"_OrdrS")!=-1){

				var tmpsep="~!~||~!~";
				if(val.indexOf(tmpsep)!=-1){
					var arrVal = val.split(tmpsep);
					val = ''+arrVal[0]+'';
					if(typeof(arrVal[1]) && arrVal[1]!=""){

						var tmpid = this.id;

						//get sig --
						var tmpidsig = tmpid+"_sig";
						var tmpsig = $('select[name='+tmpidsig+']').val();
						if(typeof(tmpsig)=="undefined"){ tmpsig=""; }
						tmpsig = $.trim(tmpsig);
						if(tmpsig!=""){
							//add Eye value in order for plan
							//get order nm
							var oOdrNm = $(this).attr("data-ordernm");
							if(typeof(oOdrNm)=="undefined"){ oOdrNm=""; }
							if(oOdrNm!=""){//add sig value
								val = val.replace(oOdrNm,oOdrNm+" "+tmpsig+"");
							}

							//attach sig
							arrVal[1]+="~!~|sig|~!~"+tmpsig;
						}

						//get Eye Value --
						var tmpideye = tmpid+"_eye";
						var tmpeye = $(':checked[name='+tmpideye+']').val();
						if(typeof(tmpeye)=="undefined"){ tmpeye=""; }
						if(tmpeye!=""){

							//add Eye value in order for plan
							//get order nm
							var oOdrNm = $(this).attr("data-ordernm");
							if(typeof(oOdrNm)=="undefined"){ oOdrNm=""; }
							if(oOdrNm!=""){//add site value
								val = val.replace(oOdrNm,oOdrNm+" ("+tmpeye+")");
							}

							//attach eye
							arrVal[1]+="~!~|eye|~!~"+tmpeye;

						}
						//get Eye Value --

						strOrderId+=""+arrVal[1]+",";
					}
				} //~!~||~!~
			}

			if(val!=''){str += ''+val+'\n';}

		} //

	});
	str= $.trim(str);
	//$('textarea[name=sc_elem_plan'+indx+']').val(str);
	$('form[name=frmSC_Exam] #sc_elem_plan_ap'+indx+'').val(str);
	$('form[name=frmSC_Exam] :input[name="elem_sc_orderids'+indx+'"]').val(strOrderId);
	//$('#idMultiPlanSCPop').hide();

}

function sc_searchExam(ob){
	var v = ob.innerHTML;
	if(typeof(v)!="undefined"){
		sc_showExamDetails('menu',v);
		$( ".selector" ).menu( "blur" );
		$( ".selector" ).menu( "collapseAll", null, true );
		$( '#div_sc_con #sc_menu_exams' ).hide();
	}

}

function getSmartChartPopUp(){
	if(top.fmain==null)return;
	//Check
	var vIsFin = top.fmain.$(":input[name='elem_isFormFinalized']").val();
	var vIsRev = top.fmain.$(":input[name='elem_isFormReviewable']").val();
	if(vIsFin==1 && vIsRev == 0){
		top.fAlert("Chart note is finalized and is not reviewable.");
		return;
	}
	if((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1"))){return;}

	var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/smart_charting.php";
	var params = "elem_formAction=SmartChartPopUp";

	//dos
	var dos=$("#elem_dos").val();
	params += "&dos="+dos;

	if(typeof(setProcessImg) != 'undefined' ){setProcessImg("1","bodyMP",0,0);}
	$.post(url, params,
		function(data){
			//Enable
			top.$("#tl_smc .icon24").attr("disabled", false);
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","bodyMP");

			var armenu = data.armenu;
			data = data.str;

			if(data != ""){
				if($('#div_sc_con_detail1').length > 0){
					if($('#div_sc_con_detail1').next('style').length > 0){
						$('#div_sc_con_detail1').next('style').remove();
					}
					$('#div_sc_con_detail1').remove();
				}

				$("body").append(data);
				$('#div_sc_con_detail1').modal('show');
				var typeahead_search = $("#elem_search_symptopms").typeahead();
				typeahead_search.data('typeahead').source = armenu;
				typeahead_search.data('typeahead').updater = function(item){
					var v = item;
					sc_showExamDetails('menu',v)
					return item;
				};

				$('body').on('click','.dropdown-submenu a', function(e){
					//Window attributes
					var window_attr = {};
					window_attr.width = $(window).width();
					window_attr.height = $(window).height();

					//Current clicked elem
					var current_elem = $(this);

					//Next Dropdown elem
					var next_ul = $(this).next('ul');

					$(this).next('ul').toggle();
					e.stopPropagation();
					e.preventDefault();
				});
			}else{
				top.fAlert("No Assessment policy defined.");
			}
		}, "json"
	);
}

/* smart chart detail */
function sc_saveExamDetail(ob){

	var val = ob.value;
	if(val == "Reset"){
		$("#div_sc_con_detail input[type=text], #div_sc_con_detail select, #div_sc_con_detail textarea").val("");
		$("#div_sc_con_detail :checked").each(function(){ $(this).trigger("click");  });
		$("#div_sc_con_detail form").append("<input type=\"hidden\" id=\"elem_exm_find_pop_reset\" name=\"elem_exm_find_pop_reset\" value=\"1\">"); //
		return;
	}else if(val == "Done"){

		//for testing
		//$("form[name=frmSC_Exam]").attr("action", "saveCharts.php"); //?elem_saveForm=Smart Charting_v6
		//$("form[name=frmSC_Exam]").get(0).submit();
		//

		var url = "saveCharts.php";
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","div_sc_con_detail");

		//for drawing symp
		var drw_symp = $("form[name=frmSC_Exam] :hidden[name=elem_drawingSymp]").val();
		if(typeof(drw_symp) != "undefined" && drw_symp==""){ drw_symp=0; }

		//icd 1 or 9
		var vicd10 = $("#hid_icd10").val();
		if(typeof(vicd10)=="undefined"){
			if(window.opener && window.opener.top.fmain){
			vicd10 = window.opener.top.fmain.$("#hid_icd10").val();
			}
		}

		var flgDoNotSave=0;
		if(drw_symp==1){
			var examNameCalled = ""+$("form[name=frmSC_Exam] input[name=elem_examNameCalled]").val();
			var temp_check_exam_name = ""+$("form[name=frmSC_Exam] input[name=elem_temp_check_exam_name]").val();

			//alert(examNameCalled+" == "+temp_check_exam_name);

			if(examNameCalled==temp_check_exam_name){

				if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","div_sc_con_detail");

				//Do not Save: just fill values in repective section
				var tmp_eye_od = 0, tmp_eye_os = 0,subExamDivId="";
				$("form[name=frmSC_Exam] .symOpt1 :input").each(function(indx){
						var tmpid = ""+this.id;
						if(typeof(tmpid)=="undefined"||tmpid==""){ tmpid =""+this.name; }
						//remove drawing ids special suffix
						tmpid = tmpid.replace(/_drwpusx/g, '');

						//var tmpname = ""+this.name;
						tmpid_target = tmpid.replace("_duplicate","");

						if(this.type=="checkbox" || this.type=="radio"){
							if(this.checked==true){
								if(this.name.toLowerCase().indexOf("od_")!=-1 || this.name.indexOf("Od")!=-1){
									tmp_eye_od = 1;
								}
								if(this.name.toLowerCase().indexOf("os_")!=-1 || this.name.indexOf("Os")!=-1){
									tmp_eye_os = 1;
								}

								if($("#"+tmpid_target).length>0){
								$("#"+tmpid_target).each(function(){if(!this.checked){this.click(); utElem_capture(this);checkwnls(this);} if(this.checked){checkwnls(this);}});
								if(subExamDivId==""){subExamDivId = $("#"+tmpid_target).parents(".subExam").attr("id");}
								}else if($(":input[name="+tmpid_target+"]").length>0){
								$(":input[name="+tmpid_target+"]").each(function(){if(!this.checked){this.click(); utElem_capture(this);} if(this.checked){checkwnls(this);}});
								if(subExamDivId==""){subExamDivId = $(":input[name="+tmpid_target+"]").parents(".subExam").attr("id");}
								}
							}
						}else{

							if($.trim(this.value)!=""){
								if(this.name.toLowerCase().indexOf("od_")!=-1 || this.name.indexOf("Od")!=-1){
									tmp_eye_od = 1;
								}
								if(this.name.toLowerCase().indexOf("os_")!=-1 || this.name.indexOf("Os")!=-1){
									tmp_eye_os = 1;
								}

								if($("#"+tmpid_target).length>0){
									$("#"+tmpid_target).val(""+this.value).each(function(){this.click(); utElem_capture(this);checkwnls(this);});
									if(subExamDivId==""){subExamDivId = $("#"+tmpid_target).parents(".subExam").attr("id");}
								}else if($(":input[name="+tmpid_target+"]").length>0){
									$(":input[name="+tmpid_target+"]").val(""+this.value).each(function(){this.click(); utElem_capture(this);checkwnls(this);});
									if(subExamDivId==""){subExamDivId = $(":input[name="+tmpid_target+"]").parents(".subExam").attr("id");}
								}
							}
						}
					});

				//call changes functions
				if(subExamDivId!=""){
					top.window.status="Please save changes from Done button!";
				}

				//Exam comments
				var tmp_comm=""+$("form[name=frmSC_Exam] :input[name=sc_elem_comments]").val();
				tmp_comm = $.trim(tmp_comm);
				if(tmp_comm!="" && subExamDivId!=""){
					$("#"+subExamDivId+" textarea[name*=AdOptions]").each(function(idx){
							if(this.value!=""){ this.value+=", "; }
							this.value+=""+tmp_comm;
							if(typeof(this.onblur)=="function"){this.onblur();}
						});
				}

				//fill assessment and plan
				var f_clck=0;
				var ln = $("form[name=frmSC_Exam] :input[name*=sc_elem_assess]").length;
				for(var i=1;i<=ln;i++){

					var tmp_assmt="", tmp_plan="", tmp_eye = "", tmp_dx = "";
					var o_assmt = $("form[name=frmSC_Exam] :input[name=sc_elem_assess"+i+"]");
					if(o_assmt.prop("type")=="checkbox" && o_assmt.prop("checked")==true){  }
					else if(o_assmt.prop("type")=="textarea" && o_assmt.val()!=""){}
					else{ continue; }

					tmp_assmt = ""+$("form[name=frmSC_Exam] :input[name=sc_elem_assess"+i+"]").val();
					tmp_plan = ""+$("form[name=frmSC_Exam] textarea[name=sc_elem_plan"+i+"]").val();
					tmp_plan_ap = ""+$("form[name=frmSC_Exam] #sc_elem_plan_ap"+i+"").val();
					if(tmp_plan_ap!="" && tmp_plan_ap!="undefined"){ if(tmp_plan!="" && tmp_plan!="undefined"){ tmp_plan = tmp_plan + "\n"; }  tmp_plan = tmp_plan + tmp_plan_ap;  }

					tmp_dx = ""+$("form[name=frmSC_Exam] :input[name=sc_elem_dxcode"+i+"]").val();
					if(typeof(tmp_dx)=="undefined" || tmp_dx=="undefined"){ tmp_dx="";  }

					if(tmp_eye_od == 1 && tmp_eye_os == 1 ){
						tmp_eye = "OU";
					}else if(tmp_eye_od == 1){
						tmp_eye = "OD";
					}else if(tmp_eye_os == 1){
						tmp_eye = "OS";
					}

					if(tmp_assmt!="" || tmp_plan!=""){
						window.opener.top.fmain.zSaveWithoutPopupSave=1;
						window.opener.top.fmain.addAssessOption(""+tmp_assmt, ""+tmp_plan, 0, ""+tmp_eye, 0, ""+tmp_dx);
						f_clck=1;
					}
				}

				if(f_clck==1){		window.opener.top.$("#save").trigger("click"); }

				flgDoNotSave=1;
			}

		}



		if(flgDoNotSave==0){
		$("#div_sc_con_detail .greyAll_v2").each(function() { if(this.type=="checkbox" && this.checked){ this.checked=false;  }else if(this.type=="text"||this.type=="textarea"||this.type=="select-one"){this.value="";} });//empty grey values
		var params = $("form[name=frmSC_Exam]").serialize();
		params += "&elem_saveForm=Smart Charting_v6";
		params += "&hid_icd10="+vicd10;

		//
		$("#div_sc_con_detail input:checkbox").not(":checked").each(function() {	params +="&"+this.name+"=";	});

		//remove drawing ids special suffix
		params = params.replace(/_drwpusx/g, '');

		//alert(params);

		//alert("sc_saveExamDetail0"+params);
		//return;

		$.post(url, params, function(data){
				if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","div_sc_con_detail");
				//alert(data);
					//*
					//var oalert = window.open("","dd","width=200,height=200,resizable=1");
					//oalert.document.write(""+data);
					//console.log(""+data);
					//*/

					//console.log(""+data);

					//Fill AP--
					if(data.AP){
						//alert(data.AP["A"]+","+data.AP["P"]+","+0+","+data.AP["EYE"]+","+data.AP["DX"]);
						var ln = data.AP["A"].length;
						for(var i=0;i<ln;i++){
							if(data.AP["A"][i] && typeof(data.AP["A"][i])!="undefined" && data.AP["A"][i]!=""){
								if(drw_symp==0){
									addAssessOption(""+data.AP["A"][i], ""+data.AP["P"][i], 0, ""+data.AP["EYE"][i],"",data.AP["DX"][i],data.AP["ORDERIDS"][i],'',data.AP["DXID"][i]);
								}else{
									window.opener.top.fmain.addAssessOption(""+data.AP["A"][i], ""+data.AP["P"][i], 0, ""+data.AP["EYE"][i],"",data.AP["DX"][i],data.AP["ORDERIDS"][i],'',data.AP["DXID"][i]);
								}
							}
						}
					}

					//Fill FU--
					if(data.FU){
						//setApPolFU(symp1);
						var ln = data.FU.length;
						for(var i=0;i<ln;i++){
							if( ""+$.trim(data.FU[i]["number"])!="" || ""+$.trim(data.FU[i]["time"])!="" || ""+$.trim(data.FU[i]["visit_type"])!="" ){
								if(drw_symp==0){
									fu_addOpts(""+data.FU[i]["number"],""+data.FU[i]["time"],""+data.FU[i]["visit_type"]);
								}else{
									window.opener.top.fmain.fu_addOpts(""+data.FU[i]["number"],""+data.FU[i]["time"],""+data.FU[i]["visit_type"]);
								}
							}
						}
					}

					//Savework view quitly
					if(drw_symp==0){
						top.fmain.zSaveWithoutPopupSave=1;
						top.$("#save").trigger("click");
						loadExamsSummaryAll();
					}else{
						window.opener.top.fmain.zSaveWithoutPopupSave=1;
						window.opener.top.$("#save").trigger("click");
						window.opener.top.fmain.loadExamsSummaryAll();
					}

					hide_modal("div_sc_con_detail");

				}, "json");
		}else{
			hide_modal("div_sc_con_detail");
		}

	}else{
		hide_modal("div_sc_con_detail");
	}
}


function showPhyNotes(val,evnt){
	$('[id^=physician_notes_div]').each(function(id,elem){
		$(elem).remove();
	});
	if(val == 1){
		$.ajax({
			type:'POST',
			url: top.JS_WEB_ROOT_PATH+'/interface/chart_notes/physician_notes.php?ajax_request=yes&get_modal=yes',
			success:function(response){
				$("body").append(""+response);
				$('#physician_notes_div').show();
			}
		});
	}

}



function donePtLExam(str){	$("#tdPtLExam").html("L. Exm. "+str); }

function close_gh_window() {
	//$('#genHealthDiv_wv').fadeOut(10);
	$('#genHealthDiv_wv').modal("hide");
}

function ptInfoReviewed()
{
	if((finalize_flag==1 && isReviewable==0) || elem_per_vo == "1"){return;}
	hideMedListExe("PMH",1);
	//var o = top.fmain.ifrmCenterPage;
	var o = top.fmain;
	var oFrmRewSt = o.gebi("elem_isFormReviewable");
	var oFrmFinz =  o.gebi("elem_isFormFinalized");
	var oMasterId = o.gebi("elem_masterId");
	var mId = "";
	var prm = "elem_formAction=genHealthReviewd";
	if( ( oFrmFinz.value != "1" ) || (oFrmRewSt.value == "1") ){
		if(typeof oMasterId.value != "undefined") mId = oMasterId.value;
		//Add info CC
		var msg_ownr = $("#tr_ownership_msg").data("msg");
		if(typeof(msg_ownr)!="undefined" && msg_ownr!=""){
			prm += "&elem_owner_msg="+msg_ownr;
		}
	}

	prm += "&elem_mId="+mId;
	//x_setPtLExam(mId,donePtLExam);
	$.post(top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php',""+prm, function(data){
			if(data){
			if(data.noti_genhealth){$(".okayNav #general_health").removeClass(); $(".okayNav #general_health").addClass(""+data.noti_genhealth);}
			donePtLExam(data.tmp); close_gh_window();
			if(typeof(msg_ownr)!="undefined" && msg_ownr!=""){
				var cc = $("#elem_ccompliant").val();
				cc=$.trim(cc);
				var ptrn = "HPI was performed by([\\w ]+)\.";
				cc = regReplace(ptrn,"",cc);
				if(cc!=""){cc = cc+"\n";}
				if(msg_ownr){cc = cc+msg_ownr;}
				$("#elem_ccompliant").val(cc);

				//
				if(data.proid){ 	$("#elem_pro_id").val(data.proid);	}
				if(data.pro_nm){ 	$("#elem_pro_name").val(data.pro_nm);	}
				if(data.cosignid){ 	$("#elem_cosigner_id").val(data.cosignid);	}
				if(data.cosign_nm){ 	$("#elem_cosigner_name").val(data.cosign_nm);	}

				//change color to current user
				$("#elem_ccompliant, #elem_chk").trigger("click");
				$("#elem_painCc").trigger("change");
				$("#elem_neuroPsych").trigger("click");
			}
			//
			top.$("#save").trigger("click");
			}
		},"json");
}



function bind_datepicker()
{
		$("input.datepicker").datepicker({timepicker:false,format:top.jquery_date_format,maxDate:new Date(),autoclose: true, scrollInput:false});

}

// ref_surg --
function getMRinfo_SC(){
	var sod="",sos="",cod="",cos="",aod="",aos="",tm="";

	var a=0;b=0;tm=0;
	$("#Vision input[name*=elem_mrNoneGiven").each(function(){
			var x = this.name.replace(/elem_mrNoneGiven/,"");
			var othr=""; sfx="";
			if(x>1){
				othr="Other";
				if(x>2){ sfx="_"+x; }
			}
			var y = $("#Vision input[name=elem_providerName"+othr+sfx+"][class*=active]").length;
			if(this.checked && y>0){
				if(a<x){ a=x; }
			}else if(y>0){
				if(b<x){ b=x; }
			}
		});
	if(a>0){tm=a;}
	else if(b>0){tm=b;}

	if(tm>0){

		var othr=""; sfx="";
		if(tm>1){
			othr="Other";
			if(tm>2){ sfx="_"+tm; }
		}

		if($("#Vision input[name=elem_visMr"+othr+"OdS"+sfx+"]").length>0){
			sod=$("#Vision input[name=elem_visMr"+othr+"OdS"+sfx+"]").val();
		}
		if($("#Vision input[name=elem_visMr"+othr+"OdC"+sfx+"]").length>0){
			cod=$("#Vision input[name=elem_visMr"+othr+"OdC"+sfx+"]").val();
		}
		if($("#Vision input[name=elem_visMr"+othr+"OdA"+sfx+"]").length>0){
			aod=$("#Vision input[name=elem_visMr"+othr+"OdA"+sfx+"]").val();
		}
		if($("#Vision input[name=elem_visMr"+othr+"OsS"+sfx+"]").length>0){
			sos=$("#Vision input[name=elem_visMr"+othr+"OsS"+sfx+"]").val();
		}
		if($("#Vision input[name=elem_visMr"+othr+"OsC"+sfx+"]").length>0){
			cos=$("#Vision input[name=elem_visMr"+othr+"OsC"+sfx+"]").val();
		}
		if($("#Vision input[name=elem_visMr"+othr+"OsA"+sfx+"]").length>0){
			aos=$("#Vision input[name=elem_visMr"+othr+"OsA"+sfx+"]").val();
		}

	}

	return {"sod":sod,"sos":sos,"cod":cod,"cos":cos,"aod":aod,"aos":aos};
}
//--

/*IOP*/
function updateADTime(w){

	var saveForm="";
	w = $.trim(w);
	if(w == ""||elem_per_vo=='1' || ((finalize_flag == "1") && (isReviewable != "1"))){
		return;
	}

	if(w == "Anes"){
		saveForm="updateAnesTime";
	}else if(w == "Dial"){
		saveForm="updateDialTime";
	}else if(w == "OOD"){
		saveForm="updateOODTime";
	}


	if(saveForm == ""){
		return;
	}
	var wP="tdTitleIop";
	var Digital=new Date();
	var hours=Digital.getHours();
	var minutes=Digital.getMinutes();
	var seconds=Digital.getSeconds();
	var dt=Digital.getDate(); if(dt<9){ dt="0"+dt; }
	var mnth=Digital.getMonth(); mnth+=1; if(mnth<9){ mnth="0"+mnth; }
	var year=Digital.getFullYear();
	var dn="PM"
	if (hours<12)
		dn="AM"
	if (hours>12)
		hours=hours-12
	if (hours==0)
		hours=12
	if (minutes<=9)
		minutes="0"+minutes
	if (seconds<=9)
		seconds="0"+seconds
	var ctime=hours+":"+minutes+dn
	var cdt=mnth+"-"+dt+"-"+year;

	var url = "saveCharts.php";
	var params = "elem_saveForm=updateIOPTime";
	var f = gebi("elem_masterId");
	params += "&elem_formId="+f.value;
	params += "&w="+w;
	var p = gebi("elem_patientId");
	params += "&elem_patientId="+p.value;
	params += "&elem_newTime="+ctime;
	params += "&elem_newdt="+cdt;
	//objElementEffected=w;
	//Set P image
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1",wP);

	//alert(params);
	//return;
	$.post(url,params, function(data){
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("0",wP);

		loadExamsSummary("iop_gon");
	});

}

//vf-gl + oct-rnfl --
function showPrvSynthesis(tst, tid, intrprt){

	//$( ".dvprvsyn" ).remove();
	if(tst=="x"){
	return;
	}else if(tst=="VF-GL"){
		var divid="dv_vf_gl";
		var divsyn="dv_syn_vf_gl";
	}else if(tst=="OCT-RNFL"){
		var divid="dv_oct_rnfl";
		var divsyn="dv_syn_oct_rnfl";
	}

	var op = "";
	if(typeof(intrprt)!="undefined" && intrprt==1){	op = "Interpret";	}

	if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,divid);
	//
	$.get("saveCharts.php?elem_saveForm=showPrvSynthesis&tst="+tst+"&tid="+tid+"&op="+op, function(data){
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,divid);
				//cajxres(data);
				if(op == "Interpret"){
					if(data==1){ if(typeof(setProcessImg) != 'undefined' ){ setProcessImg(1,"","Test is interpreted.","","",1800); }   $("input[type=button][name='elem_btn"+tst+"Rwd']").hide(); }else{top.fAlert("Error: Test is NOT interpreted.");}
				}else{
				//
				if(data!=""){
							$("body").append(data);
							$('#modal_prev_synth').modal('show');
					}
				}
			});
}
function tests_interpret(tst, tid){showPrvSynthesis(tst, tid, 1);}
function go_test_tab(){top.$("#Tests").click();}
//vf-gl + oct-rnfl --

//Memo --
function getMemoTable( arrVal, addIndx,dfdt ){

	var oTblCntr = gebi("elem_cntrTableMemo");
	var currRowId = ((typeof oTblCntr.value != "undefined") && (oTblCntr.value != "")) ? parseInt(oTblCntr.value)+1 : 1 ; //

	//
	var url = zPath+"/chart_notes/requestHandler.php"; //"common/sliderFeeder_2.php";
	var params = "elem_formAction=add_memo&elem_cntrTableMemo="+currRowId;
	$.get(url,params,function(data){
			oTblCntr.value = currRowId; //
			$("#memo_panel_grp").append(data);

			$(".date-pick" ).datepicker({dateFormat:z_js_dt_frmt});
			cn_typeahead();
		});
}
function remMemoTable( remIndx ){
	if(confirm("Are you sure you want to delete it?")){
		if(remIndx>1){
		$("#memo"+remIndx).remove();
		}else{ $("#memo"+remIndx+" :input").val(""); }
	}
}
function openPatientProblemsList(){
	var url = zPath + "/Medical_history/problem_list/patient_problems_list_popup.php";
	var n = "childWinArScan";
	top.fmain.showOtherForms(url,n,810,600,0);
}
//Memo --

//CC Hx-----
function showcCChxpop(){
	var chk = $('#cchx_link').attr('title');
	if(typeof(chk)=="undefined"){
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"cchx_link");
		$.get(zPath+"/chart_notes/requestHandler.php?elem_formAction=showcCChxpop",function(data){
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"cchx_link");
				$('#cchx_link').popover({ container:'body', title: "<strong>CC & History</strong>", content: ""+data, html: true, placement: "bottom", animation: false});
				$("#cchx_link").on('shown.bs.popover', function(o){
						var hgt = window.innerHeight;
						var popover_id = $(this).attr('aria-describedby');
						var ptop = $("#"+popover_id+"").css("top")||0;
						hgt = parseInt(hgt) - parseInt(ptop)-50; //
						$("#"+popover_id+"").css({'max-width':'90%','z-index':'99999999'});
						$("#"+popover_id+" .popover-content").css({'height':hgt+"px", "overflow":"auto"});
					});
				$('#cchx_link').popover('show');
				$('#cchx_link').popover('hide');
				$('#cchx_link').popover('show');
			});
	}else{
		$('#cchx_link').popover('toggle');
	}
}
//function  CC Hx default string Regex ---
function ccHxDefStr(str, op){
	var ptrn = "\\s*(A|An)?((\\s*[0-9]{1,3}\\s*(Yr\\.|Yrs\\.|Year|Years|Months|Days|Mon\\.))?(\\s*old)?)?\\s*(Male|Female|male|female)?\\s*(with\\s*history\\s*of|patient|with\\s*chief\\s*complaint\\s*of)?\\s*";
	var reg = new RegExp(ptrn,"gi");
	if(op == 1){ //Rem
		return str.replace(reg, "");
	}else{
		return reg;
	}
}
//function  CC Hx default string Regex ---
//CC Hx-----


function get_reff_address_v2(obj){
	obj.title="";
	var str = $.trim(obj.value);
	if(str!=""){
		$.get(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/requestHandler.php?elem_formAction=get_reff_address_v2&refnm="+str,function(d){  if(d!=""){obj.title=""+d;}else{ obj.title=""; } });}
}

//Set Dx Code and Desc in Pt Problem List
//Called From medical_history/problem_list
function ptProbList_fillInDxDesc(obj,pth){

	//Checks
	var str = "";
	if(obj && (typeof obj.value != "undefined")){
		str = obj.value;
	}
	//
	if(typeof pth == "undefined"||pth==""){
		if(typeof(zPath)=="undefined"){ zPath=""; }
		pth = ""+zPath;
	}

	if(str == "" || !isDxCorrectCode(str)){
		return;
	}

	//Define Var -----

	//var url = "../../interface/chart_notes/getSuperBillInfo.php";
	var url = pth+"/chart_notes/requestHandler.php";
	params = "elem_formAction=Dx";
	params += "&elem_desc="+encodeURI(str);
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1",""+obj.name);
	//alert(""+params);
	//-------------------------------------------

	$.post(url,params,
			function(data){
			//------  processing after connection   ----------
			/*
			var xmlTxt = xmlHttp.responseText;
			alert(xmlTxt);
			*/
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0",""+obj.name);
			var xmlDoc = data;
			var arrDxCode = xmlDoc.getElementsByTagName("dxcode");
			var arrDxCodeDesc = xmlDoc.getElementsByTagName("dxcodedesc");
			var arrPqriCode = xmlDoc.getElementsByTagName("pqri");

			var oDxCode = (arrDxCode.length > 0) ? xmlDoc.getElementsByTagName("dxcode")[0] : null ;
			var oDxCodeDesc = (arrDxCodeDesc.length > 0) ? xmlDoc.getElementsByTagName("dxcodedesc")[0] : null ;
			if((oDxCodeDesc) && (oDxCodeDesc.firstChild)){
				var tDx = oDxCodeDesc.firstChild.data;
				tDx += ' - ' +oDxCode.firstChild.data;
				obj.value = ""+tDx;
			}

			//------  processing done --------------------------
			},"xml");
}

function pt_g_hc(obj){
	/*var elem = $(obj).parent();
	var pt_health_concern = elem.find('input[name=pt_health_concern]').val();
	var pt_goals = elem.find('input[name=chart_pt_goals]').val();

	if(pt_health_concern != ''){
		if($('#pt_goal_health_modal .modal-body').find('textarea[name=chart_pt_health_concern]').val() == ''){
			$('#pt_goal_health_modal .modal-body').find('textarea[name=chart_pt_health_concern]').val(pt_health_concern);
		}
	}

	if(pt_goals != ''){
		if($('#pt_goal_health_modal .modal-body').find('textarea[name=chart_pt_goals]').val() == ''){
			$('#pt_goal_health_modal .modal-body').find('textarea[name=chart_pt_goals]').val(pt_goals);
		}
	}

	$('#pt_goal_health_modal').modal('show');1
	show_goals_hc();*/

	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php',
		type:'post',
		data:{elem_formAction:'show_goals_hc'},
		beforeSend: function(r){
			top.show_loading_image('show', '', 'Please wait, loading data...');
		},
		complete:function(){
			top.show_loading_image('hide');
		},
		success:function(res){

			if( $("#pt_goal_health_modal",top.fmain.document).length > 0 ) {
				$("#pt_goal_health_modal",top.fmain.document).remove();
			}

			$("body",top.fmain.document).append(res);
			$("#pt_goal_health_modal",top.fmain.document).find('select').selectpicker();
			$(".date-pick" ).datepicker({dateFormat:z_js_dt_frmt});

			$("#pt_goal_health_modal",top.fmain.document).modal('show');
		}

	});

}

function pt_health(obj){
	/*var elem = $(obj).parent();
	var pt_health_concern = elem.find('input[name=pt_health_concern]').val();
	var pt_goals = elem.find('input[name=chart_pt_goals]').val();

	if(pt_health_concern != ''){
		if($('#pt_health_status_modal .modal-body').find('textarea[name=chart_pt_health_concern]').val() == ''){
			$('#pt_health_status_modal .modal-body').find('textarea[name=chart_pt_health_concern]').val(pt_health_concern);
		}
	}

	if(pt_goals != ''){
		if($('#pt_health_status_modal .modal-body').find('textarea[name=chart_pt_goals]').val() == ''){
			$('#pt_health_status_modal .modal-body').find('textarea[name=chart_pt_goals]').val(pt_goals);
		}
	}

	$('#pt_health_status_modal').modal('show');
	show_health_status();*/

	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php',
		type:'post',
		data:{elem_formAction:'show_health_status'},
		beforeSend: function(r){
			top.show_loading_image('show', '', 'Please wait, loading data...');
		},
		complete:function(){
			top.show_loading_image('hide');
		},
		success:function(res){

			if( $("#pt_health_status_modal",top.fmain.document).length > 0 ) {
				$("#pt_health_status_modal",top.fmain.document).remove();
			}

			$("body",top.fmain.document).append(res);
			$("#pt_health_status_modal",top.fmain.document).find('select').selectpicker();
			$(".date-pick" ).datepicker({dateFormat:z_js_dt_frmt});

			$("#pt_health_status_modal",top.fmain.document).modal('show');

		}

	});

}

function delete_goal(id,index)
{
	if(id)
	{
		$.ajax({
			url:top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php',
			type:'post',
			data:{elem_formAction:'del_goal', record_id : id},
			beforeSend: function(r){
				top.show_loading_image('show', '', 'Please wait, Deleting Record(s)...');
			},
			complete:function(){
				top.show_loading_image('hide');
			},
			success:function(res){
				var r =  res.split('_');
				if(r[0] == 1)
					$("#goal_row_"+index).remove();
				top.fAlert(r[1]);
			}
		});
	}
	else
	{
		$("#goal_row_"+index).remove();
	}

}

function add_goals(_this)
{
	var html = '';
	var addBtnObj = $("#add_goal_btn");
	var p_index = addBtnObj.data('rows');
	var c_index = p_index + 1;

	var delBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_goal(\'\',\''+p_index+'\');"></span>';

	html += '<tr id="goal_row_'+c_index+'">';
	html += '<td class="col-xs-3">';

	html +='<input type="hidden" name="goal_id['+c_index+']" id="goal_id_'+c_index+'" value="" />';
	html +='<input type="text" class="form-control" name="goal_set['+c_index+']" id="goal_set_'+c_index+'" value="" />';
	html +='</td>';

	html +='<td class="col-xs-1">';
	html +='<input type="text" class="form-control" name="goal_code['+c_index+']" id="goal_code_'+c_index+'" value="" />';
	html +='</td>';

	html +='<td class="col-xs-1">';
	/*html +='<select class="selectpicker" data-width="100%" data-container="#selectContainer" name="goal_data_type['+c_index+']" id="goal_data_type_'+c_index+'" onChange="enableField(this);" >';
	html +='<option value="text" selected>Text</option>';
	html +='<option value="range">Range</option>';
	html +='</select>';*/
	html += '<input type="text" class="form-control" name="goal_data_type['+c_index+']" id="goal_data_type_'+c_index+'" value="" maxlength="5" />';
	html +='</td>';

	html +='<td class="col-xs-3">';
	html +='<input type="text" class="form-control" name="goal_data['+c_index+']" id="goal_data_'+c_index+'" value="" />';
	html +='</td>';

	html +='<td class="col-xs-1">';
	html +='<input type="text" class="form-control" name="goal_unit['+c_index+']" id="goal_unit_'+c_index+'" value="" />';
	html +='</td>';

	html +='<td class="col-xs-1">';
	/*html +='<select class="selectpicker" data-width="100%" data-container="#selectContainer" name="goal_opr_id['+c_index+']" id="goal_opr_id_'+c_index+'" title="select" >';

	$.each(usersArr,function(userId,userName){
		var sel = (authUserId == userId) ? 'selected' : '';
		html +='<option value="'+userId+'" '+sel+'>'+userName+'</option>';
	});
	html +='</select>';*/
	html +='<input type="hidden" class="form-control" name="goal_opr_id['+c_index+']" id="goal_opr_id_'+c_index+'" value="'+authUserID+'" />';
	html +='<input type="text" class="form-control" name="goal_opr_name['+c_index+']" id="goal_opr_name_'+c_index+'" value="'+authUserNM+'" readonly />';
	html +='</td>';

	html +='<td class="col-xs-1">';
	html += '<div class="input-group">';
	html +='<input type="text" class="form-control date-pick" name="goal_date['+c_index+']" id="goal_date_'+c_index+'" value="" />';
	html += '<label class="input-group-addon" for="goal_date_'+c_index+'"><i class="glyphicon glyphicon-calendar"></i></label>';
	html += '</div>';
	html +='</td>';
	html +='<td >&nbsp;</td>';
	html +='</tr>';

	$("#goal_row_"+p_index).find('td:last').html(delBtn);
	$("#goal_row_"+p_index).after(html);
	$("#goal_row_"+c_index).find('select.selectpicker').selectpicker();
	addBtnObj.data('rows',c_index);

	$(".date-pick" ).datepicker({dateFormat:z_js_dt_frmt});
}

function save_goals()
{
	var formObj = $("#goalDataDiv",top.fmain.document).find('input, select');
	var formData = formObj.serialize() + '&elem_formAction=save_goal';
	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php',
		type:'post',
		data:formData,
		beforeSend: function(r){
			top.show_loading_image('show', '', 'Please wait, Saving Record(s)...');
		},
		complete:function(){
			top.show_loading_image('hide');
		},
		success:function(res){
			$("#goalDataDiv",top.fmain.document).html(res).find('select').selectpicker();
			 $(".date-pick" ).datepicker({dateFormat:z_js_dt_frmt});
		}

	});
}

function add_health_status(type)
{
	var html = '';
	var addBtnObj = $("#add_hs_btn_"+type);
	var p_index = addBtnObj.data('rows');
	var c_index = p_index + 1;

	var delBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="del_pt_health_status(\'\',\''+p_index+'\',\''+type+'\');"></span>';

	html += '<tr id="hs_row_'+type+c_index+'">';
	html += '<td class="col-xs-3">';

	html +='<input type="hidden" name="'+type+'_id['+c_index+']" id="'+type+'_id_'+c_index+'" value="" />';
	html +='<input type="text" class="form-control" name="'+type+'_status_text['+c_index+']" id="'+type+'_status_text_'+c_index+'" value="" />';
	html +='</td>';

	html +='<td class="col-xs-1">';
	html +='<input type="text" class="form-control" name="'+type+'_ccd_code['+c_index+']" id="'+type+'_ccd_code_'+c_index+'" value="" />';
	html +='</td>';


	html +='<td width="10%">';
	html += '<div class="input-group">';
	html +='<input type="text" class="form-control date-pick" name="'+type+'_status_date['+c_index+']" id="'+type+'_status_date_'+c_index+'" value="" />';
	html += '<label class="input-group-addon" for="'+type+'_status_date_'+c_index+'"><i class="glyphicon glyphicon-calendar"></i></label>';
	html += '</div>';
	html +='</td>';
	html +='<td width="3%" >&nbsp;</td>';
	html +='</tr>';

	$("#hs_row_"+type+p_index).find('td:last').html(delBtn);
	$("#hs_row_"+type+p_index).after(html);
	$("#hs_row_"+type+c_index).find('select.selectpicker').selectpicker();
	addBtnObj.data('rows',c_index);

	$(".date-pick" ).datepicker({dateFormat:z_js_dt_frmt});
}
function save_health_status()
{
	var formObj = $("#pt_health_status_data_div",top.fmain.document).find('input, select');
	var formData = formObj.serialize() + '&elem_formAction=save_health_status';
	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php',
		type:'post',
		data:formData,
		beforeSend: function(r){
			top.show_loading_image('show', '', 'Please wait, Saving Record(s)...');
		},
		complete:function(){
			top.show_loading_image('hide');
		},
		success:function(res){
			$("#pt_health_status_data_div",top.fmain.document).html(res).find('select').selectpicker();
			 $(".date-pick" ).datepicker({dateFormat:z_js_dt_frmt});
		}

	});
}
function del_pt_health_status(id,index,type)
{
	if(id)
	{
		$.ajax({
			url:top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php',
			type:'post',
			data:{elem_formAction:'del_pt_health_status', record_id : id},
			beforeSend: function(r){
				top.show_loading_image('show', '', 'Please wait, Deleting Record(s)...');
			},
			complete:function(){
				top.show_loading_image('hide');
			},
			success:function(res){
				var r =  res.split('_');
				if(r[0] == 1)
					$("#hs_row_"+type+index).remove();
				top.fAlert(r[1]);
			}
		});
	}
	else
	{
		$("#hs_row_"+type+index).remove();
	}

}
function enableField(_this)
{
	var obj = $("#goal_unit_"+$(_this).data('index'));

	if( $(_this).val() == 'range')
		obj.prop('disable',false);
	else	obj.prop('disable',true);
}

function save_hc()
{
	var formObj = $("#hcDataDiv",top.fmain.document).find('input, select');
	var formData = formObj.serialize() + '&elem_formAction=save_hc';
	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php',
		type:'post',
		data:formData,
		beforeSend: function(r){
			top.show_loading_image('show', '', 'Please wait, Saving Record(s)...');
		},
		complete:function(){
			top.show_loading_image('hide');
		},
		success:function(res){
			$("#hcDataDiv",top.fmain.document).html(res);
			$(".date-pick" ).datepicker({dateFormat:z_js_dt_frmt});
		}

	});
}

function delete_hc(type,id,index,parent_index)
{
	type = type || '';
	parent_index = parent_index || '';
	index = index |'';
	var p_index = parent_index ? parent_index+'_' : '';
	var row = '';
	if( type == 'obs') row =  'hc_row_'+index;
	else if( type == 'con') row =  'con_'+p_index+index;
	else if( type == 'rel') row =  'rel_'+p_index+index;

	if(id)
	{
		$.ajax({
			url:top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php',
			type:'post',
			data:{'elem_formAction':'del_hc', 'type':type,'record_id': id},
			beforeSend: function(r){
				top.show_loading_image('show', '', 'Please wait, Deleting Record(s)...');
			},
			complete:function(){
				top.show_loading_image('hide');
			},
			success:function(res){
				var r =  res.split('_');
				if(r[0] == 1 && index && row )
					$("#"+row).remove();
				top.fAlert(r[1]);
			}
		});
	}
	else
	{
		if( index && row)
			$("#"+row).remove();
	}

}

/*Attestation*/
function chart_set_attestation(indx){
	var btn="";
	if(indx==1){
		btn = "btn_attest_scribe";
	}else if(indx==2){
		btn = "btn_attest_attend_scribe";
	}else if(indx==3){
		btn = "btn_attest_teach";
	}

	if(btn!=""){
		var msg = $("#"+btn).data("msg");
		var attested = $("#"+btn).data("attested");
		var proId = authUserID;
		var formId = $("#elem_masterId").val();
		if((attested==""||typeof(attested)=="undefined") && typeof(msg)!="undefined" && msg != ""){
			//ifchart is finalized than do not fire
			if((finalize_flag!=1 || isReviewable!=0) && elem_per_vo != "1"){
				var url="saveCharts.php";
				param = "elem_saveForm=setAttestation&proId="+proId+"&msg="+msg+"&formId="+formId+"&indx="+indx;
				$.post(url,param,function(data){ $("#"+btn).removeClass("btn-warning").addClass("btn-success"); $("#"+btn).data("attested","1"); $("#"+btn).data("msg",""+msg);   if(indx==3){ $("input[name=elem_resiHxReviewd]").val(1);$("#assessplan #spnRxHxRvd").addClass("active"); }  });
			}
		}

		if(msg!=""){
		//var htm = "<div id=\"div_atts_msg\" style=\"text-align:center;padding:5px;position:absolute;display:block;background-color:white;border:1px solid black;font-size:14px;width:400px;margin-top:-150px;\" ><p>"+msg+"</p>"+
		//		"<input type=\"button\" class=\"dff_button_sm\" id=\"btn_ats_msg_close\" value=\"Close\" onclick=\"$('#div_atts_msg').remove();\"></div>";
		//$(htm).insertAfter("#btn_attest_scribe");
		$('#'+btn).popover('destroy');
		$('#'+btn).popover({'title':"", 'content':""+msg, 'html':true});
		$('#'+btn).popover('toggle');
		}
	}
}

function change_attending_phy(o){
	var id = o.value;
	if(id!=""){
	if((finalize_flag!=1 || isReviewable!=0) && elem_per_vo != "1"){
		var formId = $("#elem_masterId").val();
		var url="saveCharts.php";
		param = "elem_saveForm=setAttestation&new_pro_Id="+id+"&formId="+formId;
		//start
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"el_change_attending");
		$.post(url,param,function(data){
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"el_change_attending");
			if(data){
				if(data.btn_attest_scribe && typeof(data.btn_attest_scribe)!="undefined" && data.btn_attest_scribe!=""){
					$("#btn_attest_scribe").data("msg",""+data.btn_attest_scribe);
				}
				if(data.btn_attest_attend_scribe && typeof(data.btn_attest_attend_scribe)!="undefined" && data.btn_attest_attend_scribe!=""){
					$("#btn_attest_attend_scribe").data("msg",""+data.btn_attest_attend_scribe);
				}
			}
		},"json");
	}
	}
}
/*End Attestation*/

/* HPI */
function rvs_sep_usr_coments(){
	var x1="", x2="";
	var x = $.trim($('#elem_ccompliant').val());
	var DBLCIRCLE = String.fromCharCode(9678);
	var w = x.indexOf(DBLCIRCLE);
	if(w!=-1){
		x1 = x.substring(w);
		x1 = $.trim(x1.replace(DBLCIRCLE,""));
		x2 = 	$.trim(x.substring(0, w));
	}else if(x!=""){
		x2 = x;
	}
	return {'COM':x1,'CCHX':x2};
}
//
function rvs_mk_cchx_readonly(){
	$('#elem_ccompliant').prop('readonly',true).bind('click', function(){
		//
		var o_cchx_com =rvs_sep_usr_coments();

		var str_modal = '<!-- Modal -->'+
			'<div id=\'cchxModal\' class=\'modal fade\' role=\'dialog\'>'+
			  '<div class=\'modal-dialog modal-lg\'>'+

			    '<!-- Modal content-->'+
			    '<div class=\'modal-content\'>'+
			      '<div class=\'modal-header\'>'+
				'<button type=\'button\' class=\'close\' data-dismiss=\'modal\'>×</button>'+
				'<h4 class=\'modal-title\'>Enter comments into chief complaint </h4>'+
			      '</div>'+
			      '<div class=\'modal-body\'>'+
				'<p>'+o_cchx_com.CCHX+'</p>'+
				'<p><textarea id=\'ta_ccHX_com_modal\' class=\'form-control\' rows=\'5\'>'+o_cchx_com.COM+'</textarea></p>'+
			      '</div>'+
			      '<div class=\'modal-footer\'>'+
				'<button type=\'button\' class=\'btn btn-success\' >Done</button>'+
				'<button type=\'button\' class=\'btn btn-danger\' data-dismiss=\'modal\'>Close</button>'+
			      '</div>'+
			    '</div>'+

			  '</div>'+
			'</div>';

		$('body').append(str_modal);

		$('#cchxModal .btn-success').bind('click', function(){
				var x = $.trim($('#ta_ccHX_com_modal').val());
				var y = $.trim(o_cchx_com.CCHX);
				if(x!=""){
					if(y!=""){ y+='\n'; }
					var DBLCIRCLE = String.fromCharCode(9678);
					y += DBLCIRCLE+' '+x;
				}
				$('#elem_ccompliant').val(y).triggerHandler('click');
				$('#cchxModal').modal('hide');
			});

		$('#cchxModal').modal({backdrop: false});
		$('#ta_ccHX_com_modal').focus();
	});
}
/* HPI */

function save_inpatient()
{
	var formObj = $("#inpatientDataDiv",top.fmain.document).find('input, select');
	var formData = formObj.serialize() + '&elem_formAction=save_inpatient';
	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php',
		type:'post',
		data:formData,
		beforeSend: function(r){
			top.show_loading_image('show', '', 'Please wait, Saving Record(s)...');
		},
		complete:function(){
			top.show_loading_image('hide');
		},
		success:function(res){
			$("#inpatientDataDiv",top.fmain.document).html(res);
			//$(".date-pick" ).datepicker({dateFormat:z_js_dt_frmt});
		}

	});
}

function ptPayerModal(callFrom){
	if(callFrom == '') callFrom = 'get';

	switch(callFrom){
		case 'get':

			$.ajax({
				url: top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php?elem_formAction=getPtPayer',
				type:'GET',
				beforeSend:function(){
					top.show_loading_image('show');
				},
				success:function(response){
					if(response != ''){
						$('#patient_payer_modal').find('#patientPayerDiv').html(response);
						$('.selectpicker').selectpicker('refresh');
						$('.datePickIn').datepicker({dateFormat:"mm-dd-yy"});
					}
				},
				complete:function(){
					top.show_loading_image('hide');
				}
			});

		break;

		case 'save':
			var dat = $("#patientPayerDiv").find('input,select').serialize();
			$.ajax({
				url: top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php?elem_formAction=savePtPayer',
				data:dat,
				type:'POST',
				beforeSend:function(){
					top.show_loading_image('show');
				},
				success:function(response){
					if(response != ''){
						$('#patient_payer_modal').find('#patientPayerDiv').html(response);
						$('.selectpicker').selectpicker('refresh');
						$('.datePickIn').datepicker({dateFormat:"mm-dd-yy"});
					}
				},
				complete:function(){
					top.show_loading_image('hide');
				}
			});

		break;
	}
}

// Assessment Plan Hx Pop up -----
function showAsHx(flg){
	if(flg==1){
		if($("#divAsHx").length<=0){
			//Get from db
			var fid = $("#elem_masterId").val();
			if(typeof(setProcessImg)=="function")setProcessImg("1","assessplan");
			$.get("requestHandler.php",{"elem_formAction":"showAsHxNew","fid":fid},function(data){
				//document.write(data);
				if(typeof(setProcessImg)=="function")setProcessImg("0","assessplan");
				if(data!=""){
					$("body").append(data);
					/*
					var thgt = $('#divWorkView').scrollTop();
					//var tmp = $("#follow_up").offset();
					var tmp = $("#assessplan label.hxlbl").offset();
					var hgt = $("#divAsHx").height();
					var top =  parseInt(tmp.top) + parseInt(thgt); //
					//$('#divWorkView').scrollTop(thgt);
					//$('#divWorkView').animate({scrollTop:$('#divWorkView')[0].scrollHeight}, 2000,function(){$("#divAsHx").css({"display":"block","top":top+"px","left":"40px"});});
					///$('#divWorkView').scrollTop($('#divWorkView')[0].scrollHeight);
					$("#divAsHx").css({"display":"block","top":top+"px","left":"40px"});
					*/
					$("#divAsHx").css({"display":"block"});
					$("#divAsHx").draggable({handle:".panel-heading"})
					$("#divAsHx .panel-body").css({'overflow':'auto', 'height':$(window).height()-50});
					$("#divAsHx .panel-heading").css({'cursor':'move'});
				}});
		}else{
			$("#divAsHx").show();
		}
	}else{
		$("#divAsHx").hide();
	}
}
// Assessment Plan Hx Pop up -----

//-- update icon bar --
function update_toolbar_icon(){	$.get("requestHandler.php",{"elem_formAction":"update_icons"},function(d){if(d){for(var a in d){	if(d[a]!=null){ $("li#"+a).removeClass("cbred cbgreen cborange").addClass(d[a]);	}}	}	}, 'json');}

//Advance Directive
function show_ad_div(op){
	if(typeof(op)=="undefined"){op=0;}
	if(op=="1"){ hide_modal("advancedDirectiveModal"); }
	else if(op=="2"){
		var adoval=$("#ado_option").val();
		show_ad_div(1);
		if(typeof(setProcessImg)=="function")setProcessImg("1");
		$.post("saveCharts.php",{"elem_saveForm":"save_advance_directive", "ado_option":""+adoval},function(d){
				if(typeof(setProcessImg)=="function")setProcessImg("0");
				if(typeof(d)!="undefined" && d!=""){
					$("#ad_dir").html(d);
				}
			});
	}else{
	if(typeof(setProcessImg)=="function")setProcessImg("1");
	$.get("requestHandler.php?elem_formAction=get_adv_directive", function(d){
		if(typeof(setProcessImg)=="function")setProcessImg("0");
		if(d && d!=""){
			$("body").append(d);
			$("#advancedDirectiveModal").modal("show");
			$("#advancedDirectiveModal .menu_advdirective a").bind("click", function(){ $("#ado_option").val($(this).data("val"));  });
		}
	});
	}
}
// Pt Alerts
function show_pop_up_pt_alert(msg){
	var a = "divPtDemographicAlertWV";
	if(typeof(top.patient_pop_up)=="undefined"){ top.patient_pop_up=new Array(); }
	if(msg!=""  && (jQuery.inArray(a, top.patient_pop_up) == "-1")){
		top.fAlert('<div style="max-height:450px;overflow-y:scroll;">'+msg+'</div>','Patient Notes');
		top.patient_pop_up[top.patient_pop_up.length] = a;
	}
}

function hideWVSxDiv(id) {
	var e =  event;
	id = id || '';
	if( typeof id === 'undefined') id = '';
	if( !id) return;

	var container = $("#"+id);
	// if the target of the click isn't the container nor a descendant of the container
  if (!container.is(e.target) && container.has(e.target).length === 0 && container.css('display') !== 'none' ) {
     container.hide();
  }
}

$(document).mouseup(function(e) {
	hideWVSxDiv('surgeryDiv');
	//hideWVSxDiv('surgeryDiv');
});


//IOP graphs ---
function show_iop_graphs(flg,vision){
	if(typeof(flg)!="undefined" && flg==1){ if($("#iol_graph_doc").length>0){ $("#iol_graph_doc").remove(); }else if(top.$("#iol_graph_doc").length>0){ top.$("#iol_graph_doc").hide().remove();} return;  }
	if($("#iol_graph_doc").length<=0){
		var v=(typeof(vision)!="undefined" && vision!="") ? "&vision="+vision : "";
		$("body").append("<div id='iol_graph_doc'><iframe id=\"ifrm_iop_graph\" src=\""+zPath+"/chart_notes/view/iop_graphs.php?inc_glbl=1"+v+"\" border=\"0\" height=\"100%\" width=\"100%\" scrolling=\"0\"></iframe></div>");
		//top.show_loading_image('show');
		//$("#iol_graph_doc").load(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/view/iop_graphs.php?inc_glbl=1", function() { top.show_loading_image('hide'); /*IOP_showGraphsAm();*/ });
	}$("#iol_graph_doc").show();
}
//IOP graphs ---

function pging_cn_dig(st){
	if(isNaN(st) && typeof(st.value)!="undefined"){ st=st.value; }else if(isNaN(st)){ st=0; }
	var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/requestHandler.php";
	$.get(url,{elem_formAction:"PtAtAGlancePopUp",limit_records:"1", "st":st},
	function(resp){
		resp = $.parseJSON(resp);
		$('#pgd_showpop',top.fmain.document).html(resp.data)
		if(typeof(top.fmain.setHgtGrpDiv)=='function'){top.fmain.setHgtGrpDiv();}
		var sh = 0;
		$('#pgd_showpop').find('table:not(:last-child)').each(function(){
			sh += $(this).height();
		});
		$('#pgd_showpop .modal-body',top.fmain.document).scrollTop(0).scrollTop(sh-50);

	});
	stopClickBubble();
	}
//Role charges --
function role_change_options(o,h){
	if(typeof(h)!="undefined" && h==1){ $("#legendDiv .clickable").popover('hide'); return;}
	if(typeof($(o).attr("aria-describedby"))=="undefined"){
	var x = top.$("#li_rc").html(); x = x.replace("<h4><label class=\"\">Role as</label>","");	x = x.replace(/change_role\(this\)/g,"top.change_role(this,1)");
	$(o).unbind("click").popover({title: "Role Change<small>Unsaved data will be lost.</small><span class=\"glyphicon glyphicon-remove pull-right\" onclick=\"role_change_options('',1)\"></span>",
					content: ""+x,
					html: true, placement: "top"});
	$(o).popover('show');
	$("#legendDiv input[name=el_usr_role]").each(function(){ this.checked = (this.value==logged_user_type) ? true : false; });
	}
}
//Role charges --

function show_pgd() {
	var ob = $("#pgd_showpop");
	if( ob.length > 0 ) {
		var c = ob.css('display');
		if( c == 'none') ob.show();
	}
}

//Set Hgt of patient past diagnonis
// PAG  - copied from Pt_glance.js
function setHgtGrpDiv(){

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

					tmp = parseInt(tmp) + parseInt(15); //increased some height to remove overlapping

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

function show_inpatient_data(){

	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/chart_notes/requestHandler.php',
		type:'post',
		data:{elem_formAction:'show_inpatient_data'},
		beforeSend: function(r){
			top.show_loading_image('show', '', 'Please wait, loading data...');
		},
		complete:function(){
			top.show_loading_image('hide');
		},
		success:function(res){

			if( $("#inpatient_modal",top.fmain.document).length > 0 ) {
				$("#inpatient_modal",top.fmain.document).remove();
			}

			$("body",top.fmain.document).append(res);
			$("#inpatient_modal",top.fmain.document).find('select').selectpicker();
			$(".date-pick" ).datepicker({dateFormat:z_js_dt_frmt});

			$("#inpatient_modal",top.fmain.document).modal('show');

		}

	});

}

function show_pt_payer(){

	var html = '';
	html += '<div id="patient_payer_modal" class="modal fade" role="dialog">';
	html += '<div class="modal-dialog modal-lg" style="width:80%;">';
	html += '<!-- Modal content-->';
	html += '<div class="modal-content">';
	html += '<div class="modal-header bg-primary">';
	html += '<button type="button" class="close" data-dismiss="modal">×</button>';
	html += '<h4 class="modal-title">Patient Payer</h4>';
	html += '</div>';
	html += '<div class="modal-body">';
	html += '<div id="selectContainer" style="position:absolute;"></div>';
	html += '<div class="row">';
	html += '<div class="col-sm-12" id="patientPayerDiv">';
	html += '</div>';

	html += '</div>';
	html += '</div>';

	html += '<div id="module_buttons" class="ad_modal_footer modal-footer">';
	html += '<button type="button" class="btn btn-success" onclick="top.fmain.ptPayerModal(\'save\');">Save Patient Payer</button>';
	html += '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>';
	html += '</div>';

	html += '</div>';
	html += '</div>';
	html += '</div>';

	if( $("#patient_payer_modal",top.fmain.document).length > 0 ) {
		$("#patient_payer_modal",top.fmain.document).remove();
	}

	$("body",top.fmain.document).append(html);

	top.fmain.ptPayerModal();
	$('#patient_payer_modal').modal('show');

}

// Funtions related to editable social history form in General Health modal in Work View
function showSocialForm(){

	$("#socialBody #socialDataTbl, #socialGHDiv #edit_icon").addClass('hidden');
	$("#socialBody #socialForm, #socialGHDiv #close_icon, #socialGHDiv #save_icon").removeClass('hidden');
	if( !$("#social").hasClass('in') ) {
		$("#socialGHDiv").find('h4').trigger('click');
	}
	scroll_div();
}

function hideSocialForm(){
	$("#socialBody #socialForm, #socialGHDiv #close_icon, #socialGHDiv #save_icon").addClass('hidden');
	$("#socialBody #socialDataTbl, #socialGHDiv #edit_icon").removeClass('hidden');
	if( !$("#social").hasClass('in') ) {
		$("#socialGHDiv").find('h4').trigger('click');
	}

}

function scroll_div() {

	//GParent.scrollTop() + Parent.position().top
	var obj = $("#genHealthDiv_wv").find('.modal-body');
	obj.animate({
		scrollTop:obj.scrollTop() + $("#socialGHDiv").offset().top
	},1000);

}

function saveSocialForm() {
	//console.log('Saving Social Data');
	var frm = $("#socialBody").find("#genHlthSocialForm");
	var data = frm.serialize();

	var chk = chkSocialFrmChange();
	if( chk )
	{
			var url = top.JS_WEB_ROOT_PATH + '/interface/chart_notes/requestHandler.php';
			$.post(url,data).done(function(r){
				//console.log(r);
				r = $.parseJSON(r);
				if(r.success) {
					//console.log('Social data saved successfully.');
					$("#socialBody #socialDataTbl").html(r.data.html);
					$("#socialBody #socialForm").html(r.data.form_data);
					hideSocialForm();
					$('#genHealthDiv_wv').find('select.selectpicker').selectpicker();
					$('#genHealthDiv_wv').find("input.datepicker").datepicker({dateFormat:z_js_dt_frmt});
				}
				else{
					var m = 'Error occured..Please try again';
					//console.log(m);
					top.fAlert(m);
				}
			});
	}
	else {
			hideSocialForm();
	}
}

function cancelSocialFrmEditing(){

	var chk = chkSocialFrmChange();
	if( chk ) {
		var m = "There is change in the social information! Would you like to save and continue?"
		top.fancyConfirm(m,'','top.fmain.saveSocialForm();','top.fmain.resetSocialForm();');
	} else {
		hideSocialForm();
	}

}

function chkSocialFrmChange(){
	//console.log('Check for change');
	var frm = $("#socialBody").find("#genHlthSocialForm");
	var inputs = frm.find('input,select,textarea');

	var chk_change = false;
	$.each(inputs,function(i,v){
		//console.log(v.type);
		var t = v.type;
		var c_val = $(v).val();
		var o_val = $(v).data('prev');
		//console.log(c_val + '---' + o_val );
		if( t == 'checkbox'){
			c_val = $(v).is(':checked') ? 'checked' : '';
			o_val = $(v).data('prev');
		}
		o_val = $.trim(o_val);
		c_val = $.trim(c_val);

		if(c_val !== o_val && v.name !== 'elem_formAction' ) {
			//console.log(v.name + '==' + c_val + '===' + o_val);
			chk_change = true;
		}

	});

	return chk_change;
}

function resetSocialForm(){
	//console.log('Reset Form Data');
	var frm = $("#socialBody").find("#genHlthSocialForm");
	var inputs = frm.find('input,select,textarea');

	$.each(inputs,function(i,v){
		//console.log(v.type);
		var t = v.type;
		var c_val = $(v).val();
		var o_val = $(v).data('prev');

		if( t == 'checkbox'){
			c_val = $(v).is(':checked') ? 'checked' : '';
			o_val = $(v).data('prev');
		}

		c_val = $.trim(c_val);o_val = $.trim(o_val);

		if(c_val !== o_val ) { //console.log(v.name + '===' + t + '====' + 'Change Found');
			if( t == 'checkbox' ) {
				var c = ( o_val == 'checked') ? true :false;
				$(v).prop('checked',c);
			}
			else if( t == 'select-multiple' ){
				var d = o_val.split(',');
				d = d.join(',');
				//console.log(typeof o_val); conosle.log(d);
				$(v).selectpicker('val',d);
			}
			else {
				$(v).val(o_val);
			}
		}

	});

	hideSocialForm();
}
// Funtions related to editable social history form in General Health modal in Work View
function save_sur_ocu(o){
	if(elem_per_vo == "1"){return;}
	if(finalize_flag == "1" && isReviewable != "1"){
		top.fmain.setSavedBtn("1", "Saving Surgical Ocular Hx..");
		var v = o.checked ? 1 : 0;
		var strsave="elem_saveForm=save_sur_ocu&elem_sur_ocu_hx="+v;
		$.post("saveCharts.php", strsave, function(d) {top.fmain.setSavedBtn("0");});
	}else{
		top.$("#save").click();
	}
}

// New Carry Forward --
function set_new_carry(flgdone){
	if(elem_per_vo == "1" || (finalize_flag == "1" && isReviewable != "1")){ return; }

	if(typeof(zPath)=="undefined")zPath="";
	var u = zPath;
	//
	var qry="";
	if(typeof flgdone != "undefined" && flgdone == "1"){
		var cr_fwd_id = $("#sl_pt_dos_modal :checked[id*=el_nfc_ds]").val();
		if(typeof cr_fwd_id != "undefined" && cr_fwd_id!=""){
			top.show_loading_image('show','','Loading...');
			qry="&set_cf_form_id="+cr_fwd_id;
			$.get(u+"/chart_notes/requestHandler.php?elem_formAction=set_crfrwd_id"+qry, function(d){ window.location.reload(); });
		}else{
			top.fAlert("Please select DOS!");
		}
		return;
	}

	//Show prompt for DOS to carry forward
	qry="&cur_cf_form_id="+$("#cryfwd_form_id").val()+"&cur_form_id="+$("#elem_masterId").val();

	//
	if($("#sl_pt_dos_modal").length>0){

		var t = $("#el_filter_pro").val() || "";
		qry+="&el_filter_pro="+t;
		t = $("#el_filter_facility").val() || "";
		qry+="&el_filter_facility="+t;

		$("#sl_pt_dos_modal").modal('hide');
		$("#sl_pt_dos_modal, .modal-backdrop").remove();
	}

	top.show_loading_image('show','','Loading...');
	$.get(u+"/chart_notes/requestHandler.php?elem_formAction=get_dos_prompt"+qry, function(d){
			$("body").append(d);
			var hgt = parseInt($(window).height()*70/100);
			$("#sl_pt_dos_modal .dos_cf").css({"height":hgt+"px","overflow":'auto'});
			$("#sl_pt_dos_modal").modal("show");
			$("#sl_pt_dos_modal").draggable();
			top.show_loading_image('hide');
		});

}
// New Carry Forward --

//IBRA--
var send_to_ibra=0;
function cnct_ibra(flg_snd_dt){
	if(elem_per_vo == "1" || (finalize_flag == "1" && isReviewable != "1")){ return; }
	var a1 = $("#el_lasik_trgt_method").val(), a2 = $("#el_visLasikTrgtDate").val(), a3 = $("#el_lasik_trgt_intervention").val();
	if(typeof(a1) == "undefined" || a1=="" || typeof(a2) == "undefined" || a2=="" || typeof(a3) == "undefined" || a3==""){top.fAlert("Please enter Lasik data!");return;}


	if(typeof(zPath)=="undefined")zPath="";
	var u = zPath;

	if(typeof flg_snd_dt != "undefined"){
	/*
		if(flg_snd_dt == 1){

			var no_case_created = $("#no_case_created").prop("checked") ? "1" : "0";
	*/
			top.show_loading_image('show');
			var param="elem_formAction=ibra_case&elem_ibra_action=send_lasik";
			//param+="no_case_created="+no_case_created+"";
			$.post(u+"/chart_notes/requestHandler.php",param, function(d){
				top.show_loading_image('hide');
				if(typeof(d)!="undefined" && d=='-1'){top.fAlert("Lasik data is sent to IBRA!");}
			});

	/*
		}else if(flg_snd_dt == 2){
			hide_modal("ibraModal");
		}
		return;
	*/
	}else{
		send_to_ibra=1;
		top.$("#save").trigger("click");

		/*
		top.show_loading_image('show','','Loading...');
		$.get(u+"/chart_notes/requestHandler.php?elem_formAction=ibra_case&elem_ibra_action=launch_ibra", function(d){
			$("body").append(d);
			var hgt = parseInt($(window).height()*70/100);
			$("#ibraModal .dos_cf").css({"height":hgt+"px","overflow":'auto'});
			$("#ibraModal").modal("show");
			$("#ibraModal").draggable();
			top.show_loading_image('hide');
		});*/

	}

}
//IBRA--

//FOR DSS check service eligibility checkbox trigger
//Works only if DSS is enabled
function service_eligibility_check(obj){
    if($(obj).is(':checked')==true){$(obj).val(1);$(obj).prop('checked', true);}
    else {$(obj).val(0);$(obj).prop('checked', false);}
}
//
function dssLoadTiuTitles() {
	var $select = $('#dssTiuTitle');
	$.ajax({
		url: top.JS_WEB_ROOT_PATH + "/interface/core/ajax_handler.php?task=dssLoadTiuTitles",
		type: 'GET',
	}).done(function(response) {
		$select.append(response);
	});
}

//Js Click To Move Signatures ----
var clMs_mvFrom = "";
function click2MoveSign(a){
	if(finalize_flag==1&&isReviewable!=1){return;}
	if(clMs_mvFrom==""){
		clMs_mvFrom = a;
		$(".divsign .signernm").css("color","green");
		$("#sign_phy"+a+" .signernm").css("color","red");

	}else{
		//replace values
		if(clMs_mvFrom!=a){
			var arr = ["elem_signCoords", "hdSignCoordsOriginal", "elem_is_user_sign", "elem_is_phy_sign", "elem_sign_path", "elem_physicianIdName", "elem_physicianId"];
			var tmp="";
			for(var z in arr){
				tmp ="";
				tmp = $(":input[name="+arr[z]+a+"]").val();
				$(":input[name="+arr[z]+a+"]").val($(":input[name="+arr[z]+clMs_mvFrom+"]").val());
				$(":input[name="+arr[z]+clMs_mvFrom+"]").val(tmp);
			}

			tmp = $("#td_signature_applet"+a).parent().html();
			$("#td_signature_applet"+a).parent().html($("#td_signature_applet"+clMs_mvFrom).parent().html());
			$("#td_signature_applet"+clMs_mvFrom).parent().html(tmp);

			//
			if($("#lbl_phy_sig"+a).hasClass("clickable") && !$("#lbl_phy_sig"+clMs_mvFrom).hasClass("clickable")){
				$("#lbl_phy_sig"+a).removeClass("clickable").unbind("click");
				$("#lbl_phy_sig"+a)[0].onclick=function(){};
				$("#lbl_phy_sig"+clMs_mvFrom).addClass("clickable").bind("click",function(){ getPhySign_db(clMs_mvFrom); });
			}else if(!$("#lbl_phy_sig"+a).hasClass("clickable") && $("#lbl_phy_sig"+clMs_mvFrom).hasClass("clickable")){
				$("#lbl_phy_sig"+a).addClass("clickable").bind("click",function(){ getPhySign_db(a); });
				$("#lbl_phy_sig"+clMs_mvFrom).removeClass("clickable").unbind("click");
				$("#lbl_phy_sig"+clMs_mvFrom)[0].onclick=function(){};
			}

			//
			if($("#sign_phy"+a+" i").length>0||$("#sign_phy"+clMs_mvFrom+" i").length>0){
				tmp = $("#sign_phy"+a+" i").html();
				$("#sign_phy"+a+" i").html($("#sign_phy"+clMs_mvFrom+" i").html());
				$("#sign_phy"+clMs_mvFrom+" i").html(tmp);
			}

		}

		$(".divsign .signernm").css("color","#663366");
		clMs_mvFrom = "";
	}
}
//Js Click To Move Signatures ----

//
function show_review_detail(o){
	var id=$(o).data("lstid"),
		secName=$(o).data("scnm"),
		opId=$(o).data("opid"),
		dateTime=$(o).data("dtm");
	if(typeof(id)=="undefined"){ id=""; }
	if(typeof(secName)=="undefined"){ secName=""; }
	if(typeof(opId)=="undefined"){ opId=""; }
	if(typeof(dateTime)=="undefined"){ dateTimes=""; }
	var parWidth = document.body.clientWidth;
	top.popup_win(zPath+'/Medical_history/review_details.php?masterId='+id+'&secName='+secName+'&opId='+opId+'&dateTime='+dateTime,'medHXReviwed','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='+parWidth+',height=520');
}
