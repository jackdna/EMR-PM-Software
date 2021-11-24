

/**
clearSuperBill
sb_clearDiabetesAssessments
sb_clearDbAsTaking
sb_addAssessOption
isDiabetesAssess
isDbTakeAssess
sb_checkDiabetes
opSuperBill
isTestOrderBySelected
opTestSuperBill
confirmVisitCode
setMedComplexity
setServiceLevel
getLOSfromCode
decideVisitType
makeVisitCode
sb_getVisitCodeCPTCost
confirmVisitCode_v3
confirmVisitCode_v2
sb_get_vc_opt
getVisitCodeDetail
checkCurrRowId
getRealIndx
editSBRowValues
Save_form
opAddCptRow
sb_reorder_srno
sb_swap_vals
emptyFirstRowSB
opRemAllCptRow
opRemCptRow
getMxSB
checkChiefComplaintDetailed
decideChiefComp
getCptDescJs
sb_checknwarn4WrongDxcode
sb_setDxCodeTitle
sb_getProcId4del
checkCptCodesChart
checkProcUnitsChart
checkDXCodesChart
checkModCodesChart
checkSBDxCodeFilled
insertExamsDoneSB
getSelPQRICodes
setVisitProcedure
getPatientCategoryCode
decideNewPatient_consult
decideNewPatient
decideLevelofService2
getVisitCode
setVisitCode
isVisitCodeUsed
setVisitCodeFinal
getWantedDxCodes
isSuperBillMade
promptMedDecCom
sb_addTypeAhead
sb_check_dx_in_icd10
sb_crt_dx_dropdown
sb_set_dx_typeahead_icd10
sb_copy_dx_codes
icd10_charts_popup
**/


//test_superbill.js



	function clearSuperBill(){
		opRemAllCptRow(3);
		$(".dxallcodes,#superbill input[name=elem_todaysCharges],#elem_levelComplexity,#elem_proc_only_visit,#elem_post_op_visit,#elem_visitCode").val("");
		if(""+$("#elem_flgIsPtNew").val()=="Yes_confirmed"){$("#elem_flgIsPtNew").val("New");}else if(""+$("#elem_flgIsPtNew").val()=="No_confirmed"){$("#elem_flgIsPtNew").val("Establish");}
		$(".dxallcodes").tooltip("destroy");
	}

	var z_flg_diabetes="", z_flg_diab_sb="";

	//clear diab assess and dx
	function sb_clearDiabetesAssessments(flgonlychk, clrtake){
		clrtake = (typeof(clrtake)!="undefined" && clrtake=="1") ? "1" : "0";
		var pln="";
		var ret=0;
		var ar_dm_as = ["Diabetes Type 1 No retinopathy","DM Type 1 Mild with ME","DM Type 1 Mod with ME","DM Type 1 Severe with ME","DM Type 1 Proliferative with ME","DM Type 1 Mild without ME",
					"DM Type 1 Mod without ME","DM Type 1 Severe without ME","DM Type 1 Proliferative without ME","DM Type 1","DM Type 1 no retinopathy","Diabetes Type 1","Diabetes Type I","Diabetes 1","DM 1"];
		var ass_nm = "textarea[id*=elem_assessment]";
		if(typeof(wpage)!="undefined" && wpage=="accSB"){
			ass_nm = ".assnm";
		}
		$(ass_nm).each(function(indx){

				//
				if(this.id.toLowerCase().indexOf("dx")==-1){
				var tmp = $.trim(this.value);
				if($.trim(tmp)!=""){
					tmp=tmp.toLowerCase();
					//pure it
					var ar_tmp = tmp.split(";");
					tmp = $.trim(ar_tmp[0]);

					//taking--
					if(typeof(flgonlychk)!="undefined" && flgonlychk==1){ }
					else{if(clrtake==1){

						if(isDbTakeAssess(tmp)){

							this.value='';
							//elem_assessment_dxcode
							var asdxid = this.id.replace("elem_assessment","elem_assessment_dxcode");
							$("#"+asdxid).val('');
							var asplid = this.id.replace("elem_assessment","elem_plan");
							//pln = $("#"+asplid).val();
							$("#"+asplid).val('');
							var indxid = this.id.replace("elem_assessment","");
							$("#elem_apOu"+indxid).prop("checked", false);
							$("#elem_apOd"+indxid).prop("checked", false);
							$("#elem_apOs"+indxid).prop("checked", false);
							$("#no_change_"+indxid).prop("checked", false).triggerHandler("click");
							$("#elem_resolve"+indxid).prop("checked", false).triggerHandler("click");
							return;
						} }}
					//--

					for(var t in ar_dm_as){
						var dm1=ar_dm_as[t].toLowerCase();
						var dm2 = (ar_dm_as[t]=="Diabetes Type I") ? "Diabetes Type II".toLowerCase() : ar_dm_as[t].replace("1","2").toLowerCase();

						//
						var flgdm1 =false;
						var flgdm2 =false;
						if(isDiabetesAssess(tmp, 2)){
							flgdm1 = (tmp.indexOf(dm1)!=-1) ? true : false;
							flgdm2 = (tmp.indexOf(dm2)!=-1) ? true : false;
						}

						if(tmp==dm1 || flgdm1 ||
							tmp==dm2 || flgdm2){
							if(typeof(flgonlychk)!="undefined" && flgonlychk==1){ // Do not stop if '"DM Type 1"' or '"DM Type 2"' is in assessment

								//insert modifer back--
								if(typeof(z_flg_diab_sb)=="undefined" || (z_flg_diab_sb!="2"&&z_flg_diab_sb!="3")){
								var tmp_dx_mod = $.trim(ar_tmp[1]);
								tmp_dx_mod = tmp_dx_mod.toLowerCase();
								if(typeof(tmp_dx_mod)!="undefined" && tmp_dx_mod!=""){
									if(z_flg_diab_sb=="1"){
										if((tmp_dx_mod.indexOf("mild")!=-1||tmp_dx_mod.indexOf("mod")!=-1||tmp_dx_mod.indexOf("severe")!=-1||tmp_dx_mod.indexOf("proliferative")!=-1)){
											tmp=tmp+"; "+tmp_dx_mod;
										}
									}else{
										tmp=tmp+"; "+tmp_dx_mod;
									}
								}
								}
								//insert modifer back--

								if(tmp!="DM Type 1".toLowerCase()&&tmp!="Diabetes Type 1".toLowerCase()&&tmp!="DM Type 2".toLowerCase()&&tmp!="Diabetes Type 2".toLowerCase() &&
										tmp!="Diabetes Type I".toLowerCase() && tmp!="Diabetes Type II".toLowerCase()){
									//code already exists
									ret=1;
								}
								else{
									//if assessment is DM type 1 or Diabetes Type 1 or Diabetes Type 2 or DM type 2	and NE is checked then ignore DM ;
									var neid = this.id.replace("elem_assessment", "no_change_");
									if($("#"+neid).prop("checked") == true){
										ret=1; //this will stop pop up of DM
									}
								}

							}else{
								this.value='';
								//elem_assessment_dxcode
								var asdxid = this.id.replace("elem_assessment","elem_assessment_dxcode");
								$("#"+asdxid).val('');
								var asplid = this.id.replace("elem_assessment","elem_plan");
								pln = $("#"+asplid).val();
								$("#"+asplid).val('');
								var indxid = this.id.replace("elem_assessment","");
								$("#elem_apOu"+indxid).prop("checked", false);
								$("#elem_apOd"+indxid).prop("checked", false);
								$("#elem_apOs"+indxid).prop("checked", false);
								$("#no_change_"+indxid).prop("checked", false).triggerHandler("click");
								$("#elem_resolve"+indxid).prop("checked", false).triggerHandler("click");

								//
								if($.trim(pln)!=""){ break;}
							}
						}
					}
				}
				}
			});

		//
		if(typeof(flgonlychk)!="undefined" && flgonlychk==1){
			//code Do not exists
			return ret;
		}else{
			return pln; //return plan
		}
	}

	function sb_clearDbAsTaking(){
		var ass_nm = "textarea[id*=elem_assessment]";
		if(typeof(wpage)!="undefined" && wpage=="accSB"){
			ass_nm = ".assnm";
		}
		$(ass_nm).each(function(indx){
			var tmp = $.trim(this.value);
			if($.trim(tmp)!=""){
			tmp=tmp.toLowerCase();
			//pure it
			var ar_tmp = tmp.split(";");
			tmp = $.trim(ar_tmp[0]);
			if(isDbTakeAssess(tmp)){

				this.value='';
				//elem_assessment_dxcode
				var asdxid = this.id.replace("elem_assessment","elem_assessment_dxcode");
				$("#"+asdxid).val('');
				var asplid = this.id.replace("elem_assessment","elem_plan");
				pln = $("#"+asplid).val();
				$("#"+asplid).val('');
				var indxid = this.id.replace("elem_assessment","");
				$("#elem_apOu"+indxid).prop("checked", false);
				$("#elem_apOd"+indxid).prop("checked", false);
				$("#elem_apOs"+indxid).prop("checked", false);
				$("#no_change_"+indxid).prop("checked", false).triggerHandler("click");
				$("#elem_resolve"+indxid).prop("checked", false).triggerHandler("click");
				return;
			}
			}
		});
	}

	function sb_addAssessOption(a,b, c, d, e, f, g){
		if(typeof(addAssessOption)=="function"){
			addAssessOption(a,b, c, d, e, f, g);
		}else{

			//* Pending to do
			//alert($("#listAp li").length);
			/*For edit accounting*/
			if($("#assessplan .planbox .assnm[id*=elem_assessment][value='']").length>0){

				var tmpid = $("#assessplan .planbox .assnm[id*=elem_assessment][value='']")[0].id;
				$("#"+tmpid).val(a);
				var tmpdxid = tmpid.replace(/elem_assessment/g,"elem_assessment_dxcode");
				$("#"+tmpdxid).val(f);

				// if diabetes exists then change
				if((finalize_flag==0||isReviewable==1)&&elem_per_vo != "1"&&$("#hid_icd10").val()=="1"){

					a = encodeURI(a);
					f = encodeURI(f);
					var fid = $("#elem_masterId").val();
					if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"superbill");
					//save in db
					$.get(zPath+"/chart_notes/saveCharts.php?elem_saveForm=SET_DIABETES_IN_ASMNT&as="+a+"&dx="+f+"&fid="+fid, function(data){
							if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"superbill");
							//console.log(data);
						});
				}

			}else{
				/*
				var ln = $("#listAp li").length;
				var indx = ln+1;
				var elem_assessment = "elem_assessment"+indx;
				var elem_assessmentDx = "elem_assessment_dxcode"+indx;

				var htm = '<li>';
				htm += '<input type="hidden" id="'+elem_assessment+'" name="'+elem_assessment+'" value="'+a+'" class="assnm" >';//.$$elem_assessment.
				htm += '<input type="hidden" id="'+elem_assessmentDx+'" name="'+elem_assessmentDx+'" value="'+f+'">';	//'.$$elem_assessmentDx.'
				htm += '<input type="hidden" id="'+no_change_Assess+'" name="'+no_change_Assess+'" value="">';
				htm += '<input type="hidden" id="'+elem_resolve+'" name="'+elem_resolve+'" value="">';
				htm += '<input type="hidden" id="'+elem_apOs+'" name="'+elem_apOs+'" value="">';
				htm += '</li>';
				$("#listAp").append(""+htm);
				*/
			}



			//*/
		}
	}

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

	function isDbTakeAssess(s){ return s.match(/Long\s+term\s+\(current\)\s+use\s+of/gi); }

	function sb_getSeverityScaleObj(){
			return { text : "Severity Scale", id: "dmb_svrt_scl",
									    click : function() {
											var x=""+
											"<div class=\"section\" id=\"svrt_scl_info\" >"+
											    "<div id=\"staging_code_header\" class=\"purple_bar\">Severity Scale <span title=\"Close\"  onClick=\"$('#svrt_scl_info').remove();\" class=\"glyphicon glyphicon-remove pull-right\"  ></span> </div>"+
											    "<div >"+
												"<ol type=\"a\">"+
												    "<li><b>Mild</b> = microaneurysms"+"</li>"+
												    "<li><b>Moderate</b> = more than just microaneurysms but less than severe NPDR"+"</li>"+
												    "<li><b>Severe</b> = any of the following (4-2-1 rule) and no signs of PDR"+
													"<ol type=\"i\">"+
													    "<li>Severe intraretinal hemorrhages and microaneurysms  in each of 4 quadrants </li>"+
													    "<li>Definite venous beading in two or more quadrants </li>"+
													    "<li>Moderate IRMA in one or more quadrants</li>"+
													"</ol>"+
												    "</li>"+
												    "<li><b>PDR</b>  = neovascularization or vitreous/pre-retinal hemorrhage"+"</li>"+
												"</ol>"+
											    "</div>"+
											"</div>";

											$("#svrt_scl_info").remove();
											$("body").append(x);
											//var xp = $("#dmb_svrt_scl").position();
											$("#svrt_scl_info").show();
											$("#svrt_scl_info").draggable({handle:"#staging_code_header"});

										    }};

		}



	function opSuperBill(flg_ids,flg_medComp){

		//check diabetes --
		if(typeof(flg_ids)=="undefined"){ z_flg_diab_sb="1";	 sb_checkDiabetes();   return; } //&& $("#hid_icd10").val() == 1
		//check diabetes --
		//Clear Superbill for Refresh --
		sb_getProcId4del();
		clearSuperBill();
		//Clear Superbill for Refresh --

		/*
		var flgMdc=true;
		if(typeof(flg_medComp) != "undefined"){
			if(flg_medComp == "1"){
				flgMdc=false;
			}
		}

		//Check for Medical Decision Complexity
		//Only For E/M codes OR if consult type
		var oPrBillCd = gebi("elem_practiceBillCode");
		//var oCCHx = gebi("elem_ccHx");
		//|| oCCHx && ""+oCCHx.value.toLowerCase().indexOf("referred by")!=-1)

		if(flgMdc && oPrBillCd && $.trim(oPrBillCd.value) == "992"){
			var oComp = gebi("elem_levelComplexity");
			if( oComp ){
				//Empty then Prompt for Mdc
				promptMedDecCom(1);
				return;
			}
		}
		*/

		//Get Assessments
		//var oTblAssess = gebi("tblAssessment");
		var lenAssess = $("#assessplan .planbox").length;
		var strAssess = "";
		var valAssess,valAssessDx, idAsmtDx;
		var oAssess,oAssessDx;
		var oNe,ores;

		for(var i=1;i<lenAssess;i++){
			oAssess = gebi("elem_assessment"+i);
			oNe = gebi("no_change_"+i);
			ores = gebi("elem_resolve"+i);
			oAssessDx = gebi("elem_assessment_dxcode"+i);
			valAssessDx = valAssess = idAsmtDx = "";
			if( oAssess ){
				valAssess = (typeof oAssess.value != "undefined") ? oAssess.value : "";
			}

			if( oAssessDx && typeof oAssessDx.value != "undefined" && $.trim(oAssessDx.value)!="" ){
				valAssessDx = oAssessDx.value ;
				idAsmtDx = $(oAssessDx).data("dxid");
				if(typeof idAsmtDx == "undefined"){ idAsmtDx=""; }
			}

			//if assessment is not NE and not Resolved
			if(oNe&&ores && (valAssess != "")){
				if(
				((oNe.type=="checkbox"&&oNe.checked == false) && (ores.type=="checkbox"&&ores.checked==false))||
				((oNe.type=="hidden"&&oNe.value == 0) && (ores.type=="hidden"&&ores.value==0))
				){
					strAssess += "&elem_assessment"+i+"="+valAssess;
					if(valAssessDx!=""){
						strAssess += "&elem_assessmentDx"+i+"="+valAssessDx;
						if(idAsmtDx!=""){
							strAssess += "&elem_asmtDxId"+i+"="+idAsmtDx;
						}
					}
				}
			}
		}

		//if( strAssess != "" ){
			strAssess += "&elem_mxLen="+lenAssess;
			// check db for dx codes
			getDxCodesFromAssess(strAssess);
		//}
	}

	//TEst Super bill ---

	function isTestOrderBySelected(){
		var oOp = document.getElementById("elem_opidTestOrdered");
		if(oOp && oOp.value != "" ){
			return true;
		}else{
			return false;
		}
	}

	//open
	function opTestSuperBill(){

		if(typeof sb_testName == "undefined"){
			return;
		}

		if(!isTestOrderBySelected()){
			top.fAlert("Please select order by.");
			var oOp = document.getElementById("elem_opidTestOrdered");
			if(oOp){ oOp.focus(); }
			return;
		}

		//var oCptChk = elem_testCptCode;
		//var oCptDescChk = elem_testCptDesc;

		//get Test CPT Codes ---
		var test_eye = "";
		//alert(sb_testName+" - "+);
		if(sb_testName=="HRT" || sb_testName=="OCT" || sb_testName=="OCT-RNFL" || sb_testName=="GDX"){
			test_eye = ""+$(":checked[name=elem_scanLaserEye]").val();
		}else if(sb_testName=="VF" || sb_testName=="VF_GL"){
			test_eye = ""+$(":checked[name=elem_vfEye]").val();
		}else if(sb_testName=="Pachy"){
			test_eye = ""+$(":checked[name=elem_pachyMeterEye]").val();
		}else if(sb_testName=="IVFA"){
			test_eye = ""+$(":checked[name=elem_ivfa_od]").val();
		}else if(sb_testName=="ICG"){
			test_eye = ""+$(":checked[name=elem_icg_od]").val();
			if(test_eye==1){  test_eye="OU"; }
			else if(test_eye==2){  test_eye="OD"; }
			else if(test_eye==3){  test_eye="OS"; }
		}else if(sb_testName=="Fundus" || sb_testName=="External"){
			test_eye = ""+$(":checked[name=elem_photoEye]").val();
		}else if(sb_testName=="Topography" || sb_testName=="TestOther" || sb_testName=="TestLabs"){
			test_eye = ""+$(":checked[name=elem_topoMeterEye]").val();
		}else if(sb_testName=="CellCount"){
			test_eye = ""+$(":checked[name=elem_cellCntEye]").val();
		}else if(sb_testName=="A/Scan" || sb_testName=="iOLMaster"){
			var tod = ""+$(":input[name=performedByOD]").val();
			var tos = ""+$(":input[name=performedByOS]").val();

			var tod_2 = ""+$(":input[name=performedByPhyOD]").val();
			var tos_2 = ""+$(":input[name=phyTechListOS]").val();

			if(tod != "" || tod_2!=""){	test_eye = "OD";}
			if(tos != "" || tos_2!=""){	test_eye = (test_eye == "OD") ? "OU" : "OS";	}

		}else if(sb_testName=="BScan"){
			test_eye = ""+$(":checked[name=elem_bscanMeterEye]").val();
		}

		// test name re adjusted --
		var sb_testName_2 = sb_testName;
		if(sb_testName=="OCT"){
			var a = $(":checked[name=elem_scanLaserOct]").val();
			if(a==2){
				sb_testName_2="OCT-Retina";
			}else if(a==3){
				sb_testName_2="OCT-Anterior Segment";
			}
		}else if(sb_testName=="External"){
			var a = $(":checked[name=elem_fundusDiscPhoto]").val();
			if(a==2){
				sb_testName_2="Anterior Segment Photos";
			}else{
				sb_testName_2="External Photos";
			}
		}else if(sb_testName=="iOLMaster"){
			sb_testName_2= "IOL Master";
		}else if(sb_testName=="BScan"){
			sb_testName_2= "B-Scan";
		}else if(sb_testName=="CellCount"){
			sb_testName_2= "Cell Count";
		}else if(sb_testName=="VF_GL"){
			sb_testName_2= "VF-GL";
		}else if(sb_testName=="TestLabs"){
			sb_testName_2= "Laboratories";
		}

		// test name re adjusted --
		/******CUSTOM TEST VARIATION HANDLING START*****/
		custom_test_variation_subid = 0;
		if($('#hidd_test_cpt_preference_variation_id').get(0) != 'undefined'){
			custom_test_variation_subid = $('#hidd_test_cpt_preference_variation_id').val();
		}

		/*****CUSTOME TEST VARIATION HANDLING END****/

		//alert("sb_testName_2: "+sb_testName_2+", eye: "+test_eye);
		//return;

		if(typeof(test_eye)=="undefined" || test_eye==""){test_eye="OU";}

		$.get(zPath+"/chart_notes/requestHandler.php?elem_formAction=GetTestCptCodes&testname="+sb_testName_2+"&eye="+test_eye+"&variation_id="+custom_test_variation_subid, function(data){

				var obj = data.dx;
				if(obj!=null){
					var ln = obj.length; var y =1;
					if(ln>0){for(var x in obj){ if(typeof(obj[x])!="undefined" && obj[x]!=""){  var ad = obj[x].split(" :Dsc: "); var dx = $.trim(ad[0]); var dx_dsc = $.trim(ad[1]);   $("#elem_dxCode_"+y).val(dx).attr("title", dx_dsc).tooltip();  y+=1; } if(y>12){break;} } if(y>1){ $("#elem_dxCode_1").trigger("blur");  }}
				}

				//alert(data);
				var obj = data.cpt;
				//--
				if(obj==null || obj[0]==null){
					top.fAlert("No CPT code is specified for this Test.You can enter yourself.");
				}else{
				//--
				var strCptAlrdySel="";
				//
				for(var x in obj){

					var ocpt=obj[x];

					if($.trim(ocpt["practice_code"])){

						var units = ocpt["units"];
						var ccodeDesc = ocpt["desc"];
						var arrMd = ocpt["modifier"];

						//Check if Cpt Code Exits in super bill
						var cCpt = $.trim(ocpt["practice_code"]);
						var mxSbId = gebi("elem_mxSBId").value;
						var indxTbl = indxTblEmpty = "";
						var cFlag = false;
						var eTblSb = gebi("tblSuperbill");
						var oRows = eTblSb.rows;
						var lenRows = oRows.length;
						for(var i=0;i<lenRows;i++){

							var tCptId = oRows[i].id.replace(/elem_trSB/, "elem_cptCode_");
							var oCpt = gebi(tCptId);
							var sCpt = $.trim(oCpt.value);

							//First Empty Row
							if((indxTblEmpty == "") && (sCpt == "")){
								indxTblEmpty = (i+1); // indx + 1
							}

							//Filled
							if(oCpt && (sCpt != "") && (sCpt == cCpt)){
								indxTbl = ""+i;
							}
						}

						//Insert
						if(indxTbl != ""){
							//alert("CPT code already exists in super bill!");
							strCptAlrdySel=""+cCpt+" ";
							continue;
						}

						if(indxTblEmpty != ""){

							var oCpt = gebi("elem_cptCode_"+indxTblEmpty);
							oCpt.value = cCpt; // Insert;
							oCpt.title = ccodeDesc;
							renew_title(oCpt,ccodeDesc);

							$("#elem_procUnits_"+indxTblEmpty).val(units);

							if(arrMd.length>0){
								for(var a=0;a<4;a++){
									if($.trim(arrMd[a])!=""){
										$("#elem_modCode_"+indxTblEmpty+"_"+(a+1)).val(arrMd[a]);
									}
								}
							}

							if(typeof oCpt.onblur == "function"){
								checkDB4Code_flg=0;
								oCpt.onblur();
							}

						}else{

							var oVal = {"cptCode":cCpt,"procId":"","units":units,"arrDx":new Array(),"arrMd":arrMd, "cptDesc":ccodeDesc};
							opAddCptRow(mxSbId,oVal);
						}

					}
				}

				//return;

				//--
				if(strCptAlrdySel!=""){
					window.status = "CPT code ("+strCptAlrdySel+") already exists in super bill! ";
				}
				//--
				} //end CPT


			},"json");

		//return;

		//get Test CPT Codes ---

	}

	//Test Super bill ---

	//Procedure Superbill --
	function opProcedureSuperBill(){setSuperBillValue_onchange();}
	//Procedure Superbill --

	/*
	function confirmVisitCode(){

		var flgMdc=true;
		if(typeof arguments[0] != "undefined"){
			if(arguments[0] == 1){
				flgMdc = false;
			}
		}

		//Check for Medical Decision Complexity
		var oComp = gebi("elem_levelComplexity");
		var opov = gebi("elem_proc_only_visit");
		var opopv = gebi("elem_post_op_visit");
		if(flgMdc && (oComp && ((oComp.value == "") || (typeof oComp.value == "undefined"))) && (opov && ((opov.value == "") || (typeof opov.value == "undefined"))) && (opopv && ((opopv.value == "") || (typeof opopv.value == "undefined"))) ){
			//Prompt for Mdc
			promptMedDecCom();
			return;
		}
		//Form Id
		var params = "";
		params += "&elem_formId="+document.getElementById("elem_masterId").value;
		//Level of Complexity
		var oLc = document.getElementById("elem_levelComplexity");
		if(oLc){
			oLc.value = (typeof oLc.value != "undefined") ? ""+oLc.value : "";
			params += "&elem_levelComplexity="+oLc.value;
		}
		//
		//Rvs
		var rvsSt = getRvsDoneSt();
		params += "&elem_rvs="+rvsSt;

		//Vision
		//Vision
		var arrVisDis=new Array("elem_visDisOdSel1","elem_visDisOdSel2","elem_visDisOsSel1",
					"elem_visDisOsSel2","elem_visDisOdTxt1","elem_visDisOdTxt2",
					"elem_visDisOsTxt1","elem_visDisOsTxt2","elem_disDesc");
		var vis = (isVisElemDone(arrVisDis)) ? "1" : "0";
		params += "&elem_vision="+vis;

		//cc&his
		//CC & His
		var oCCHx = gebi("elem_ccHx");
		var oCC = gebi("elem_ccompliant");
		var strCCHx = oCC.value;
		//Refine it from default String
		//var ptrn = "\\s*A\\s*((\\d)+\\s*(years|months|days)\\s*old\\s*)?(Male|Female|male|female)\\s*with\\s*history\\s*of\\s*(\\r)?\\s*";
		var ptrn = "\\s*(A|An)((\\s*[0-9]{1,3}\\s*(Yr\\.|Yrs\\.|Year|Years|Months|Days|Mon\\.))?(\\s*old)?)?\\s*(Male|Female|male|female)?\\s*(with\\s*history\\s*of|patient|with\\s*chief\\s*complaint\\s*of)?\\s*(\\r)?\\s*";
		strCCHx = regReplace(ptrn,"",strCCHx);
		if($.trim(strCCHx) == "" && $.trim(oCCHx.value)!=""){
			strCCHx = regReplace(ptrn,"",oCCHx.value);
		}
		params += "&elem_ccHx="+strCCHx;

		//Assess & plan
		var strAss = (isAssessmentDone()) ? "1" : "0";
		params += "&elem_assessment="+strAss;

		//Neuro/Psych
		var oNpsych = gebi("elem_neuroPsych");
		var strNpsych = ($.trim(oNpsych.value) != "") ? "1" : "0";
		params += "&elem_neuroPsych="+strNpsych;

		if(params != ""){
			//alert(""+params);
			getAssistenceSB(params);
		}
	}
	*/

	//super bill coding
	function setMedComplexity(obj,flg){
		var val = obj.value;
		//single check
		$("input[type=checkbox][id*=elem_chkMedComp_]").each(function(inx){
				if(obj.checked && this.value!=val && this.checked){this.checked=false;}
			});

		var val = $(":checked[id*=elem_chkMedComp_]").val();
		var oPrnt=top.fmain;
		if(typeof(wpage)!="undefined" && wpage=="accSB"){
			oPrnt=top;
		}
		//oPrnt.hideConfirmYesNo();

		var oComp = gebi("elem_levelComplexity");
		if( oComp ){

			if(val==6){
				$("#elem_post_op_visit").val("1");
				oComp.value = "";
			}else if(val==5){
				$("#elem_proc_only_visit").val("1");
				oComp.value = "";
			}else{
				oComp.value = val;	//oComp.onchange();
				$("#elem_proc_only_visit").val("");
				$("#elem_post_op_visit").val("");
			}


			//
			var objCategory = gebi("elem_category");

			//alert(objCategory.value+" - "+$("#elem_flgIsPtNew").val());

			if( $("#elem_flgIsPtNew").val()=="Yes_confirmed"||$("#elem_flgIsPtNew").val()=="No_confirmed" || $("#elem_flgIsPtNew").val() == "Establish"){
				if(typeof(flg_checkAssocPqri4All_checkvisitcode)!="undefined" && flg_checkAssocPqri4All_checkvisitcode!=0 && flg_checkAssocPqri4All_checkvisitcode!=""){
					checkAssocPqri4All_checkvisitcode(flg_checkAssocPqri4All_checkvisitcode);
				}else{
					checkAssocPqri4All();
				}
			}
			//


			/*
			if(flg == 1){
				opSuperBill(1,1);
			}else{
				confirmVisitCode(1);
			}
			*/
		}
	}
	/*
	function setServiceLevel(){
		var oPBC = gebi("elem_practiceBillCode");
		var oHis = gebi("elem_levelHistory");
		var oExm = gebi("elem_levelExam");
		var oComp = gebi("elem_levelComplexity");
		var oLoS =  gebi("elem_levelOfService");
		var lvl = "";
		if((oHis.value != "") && (oExm.value != "") && (oComp.value != "") ){
			if( (oHis.value == 3) && ( oExm.value == 4 ) && (oComp.value == 4) && ( oPBC.value != "920" ) ){
				lvl = "5";
			}else if( (oHis.value == 3) && ( oExm.value == 4 ) && (oComp.value >= 3) ){
				lvl = "4";
			}else if( (oHis.value == 3) && (oExm.value >= 3) && (oComp.value >= 2) && ( oPBC.value != "920" ) ){
				lvl = "3";
			}else if( (oHis.value >= 2) && ( oExm.value >= 2 ) && (oComp.value >= 1) ){
				lvl = "2";
			}else if( (oHis.value >= 1) && ( oExm.value >= 1 ) && (oComp.value >= 1) ){
				lvl = "1";
			}else{
				lvl = "0";
			}
		}

		if(lvl != ""){
			oLoS.value = lvl;
			oLoS.onchange();
		}else{
			//Level zero
		}
	}
	*/

	function getLOSfromCode( cd ){
		var cd = cd.toString();
		if( cd == "Intermediate" ){
			return 2;
		}else if( cd == "Comprehensive" ){
			return 4;
		}else if( (typeof cd != "undefined") && ( cd.length == 5 ) ){
			var lastDgt = cd.substring(4);
			return lastDgt;
		}
		return "";
	}

	/*
	function decideVisitType( val ){
		//top.fmain.hideConfirmYesNo();
		if( val != -1 ){
			var oPBC = gebi("elem_practiceBillCode");
			var oLoS =  gebi("elem_levelOfService");
			var oStrDoneEx = gebi("elem_strExmDone");
			var oStrNotDoneEx = gebi("elem_strExmNotDone");//12141

			if( val == 920 ){
				var oLoSEye = gebi("elem_levelOfServiceEye");
				var oStrDoneExEye = gebi("elem_strExmDoneEye");
				var oStrNotDoneExEye = gebi("elem_strExmNotDoneEye");

				oLoS.value = oLoSEye.value;
				oStrDoneEx.value = oStrDoneExEye.value;
				oStrNotDoneEx.value = oStrNotDoneExEye.value;

			}else if( val == 992 ){
				var oLoSEM = gebi("elem_levelOfServiceEM");
				var oStrDoneExEM = gebi("elem_strExmDoneEM");
				var oStrNotDoneExEM = gebi("elem_strExmNotDoneEM");

				oLoS.value = oLoSEM.value;
				oStrDoneEx.value = oStrDoneExEM.value;
				oStrNotDoneEx.value = oStrNotDoneExEM.value;
			}

			oPBC.value = val;
			confirmVisitCode(1);
		}
	}
	*/

	function makeVisitCode(obj){

		var practiceBillCode = obj["elem_practiceBillCode"];
		var strCategory = obj["elem_category"];
		var levelOfService = obj["elem_levelOfService"];
		var visitCode = category = "";

		if(strCategory != null&&practiceBillCode!=null)
		{
			category = getPatientCategoryCode(strCategory, practiceBillCode);
		}

		//
		if(practiceBillCode && strCategory && levelOfService)
		{
			visitCode = practiceBillCode+category+levelOfService;
		}
		//

		var ptrn = "^[0-9]{5}$";
		var reg = new RegExp(ptrn,"g");
		var iscodeOk = visitCode.match(reg);
		return (iscodeOk) ? visitCode : false;
		//alert("PracticeCode: "+objPracticeBillCode.value+"\nCategory: "+category+"\nLevel: "+objLevelOfService.value);
	}

	function sb_getVisitCodeCPTCost(vpbc){
		var parm ="";
		var ar = ["920","992"];

		var str_stop_dup = "";
		for(var z in ar){
			var t = ar[z];
			var c=0;
			$(":input[type=checkbox][id*=lb_qlfy_lvl_code"+t+"]").each(function(){
					if(this.checked){
					var qd = $(this).val();
					if(typeof(qd)=="undefined"){ qd=""; }
					if(qd!=""){
						if(str_stop_dup.indexOf(qd+",")==-1){
							str_stop_dup += qd+",";
							//parm +="&lb_qlfy_lvl_code"+t+"_"+c+"="+qd;
							parm +="&lb_qlfy_lvl_code"+t+"="+qd;
							c++;
						}
					}
					}
				});
		}

		//set 00 when not qualified
		for(var z in ar){
			var t = ar[z];
			var tv="$0.00";
			$("label[id*=lb_rs_qlfy_def_"+t+"]").html(tv);
			$("label[id*=lb_rs_qlfy_ins_"+t+"]").html(tv);
		}
		if(parm==""){
			$("label[id*=lb_rs_qlfy_ins_], #lb_rs_ins_v").html(tv).hide();
			return;
		}

		var ins = $("#elem_masterCaseId").val();
		if(typeof(ins)=="undefined"){ins="";}
		parm +="&elem_insCaseId="+ins;
		parm +="&elem_dos="+$("#elem_dos").val();

		//console.log("parm: "+parm+"");


		var url=zPath+"/chart_notes/requestHandler.php?elem_formAction=sb_getVisitCodeCPTCost"+parm;
		$.get(url, function(data){

				//console.log("data: "+data);

				for(var x in data){

					var xd = x.replace("lb_qlfy_lvl_code","lb_rs_qlfy_def_");
					var xi = x.replace("lb_qlfy_lvl_code","lb_rs_qlfy_ins_");

					var tv = (data[x] && data[x]["def"] && typeof(data[x]["def"])!="undefined") ? "$"+data[x]["def"] : "$0.00";
					$("label[id*="+xd+"]").html(tv).show();

					var tv = (data[x] && data[x]["ins"] && typeof(data[x]["ins"])!="undefined") ? "$"+data[x]["ins"] : "$0.00";
					$("label[id*="+xi+"]").html(tv).show();

				}

				//check insurance fee
				var flg=0;
				$("label[id*=lb_rs_qlfy_ins_]").each(function(){ var t = $(this).html(); if(t!="$0.00"){ flg=1; } });
				if(flg==0){	$("label[id*=lb_rs_qlfy_ins_], #lb_rs_ins_v").html(tv).hide();  }

				/*
				for(var z in ar){
					var t = ar[z];
					var tv = (data["lb_qlfy_lvl_code"+t+"_val"] && data["lb_qlfy_lvl_code"+t+"_val"]["def"] && typeof(data["lb_qlfy_lvl_code"+t+"_val"]["def"])!="undefined") ?  data["lb_qlfy_lvl_code"+t+"_val"]["def"] : "0.00";
					$("label[id*=lb_rs_qlfy_def_"+t+"]").html(tv);

					var tv = (data["lb_qlfy_lvl_code"+t+"_val"] && data["lb_qlfy_lvl_code"+t+"_val"]["ins"] && typeof(data["lb_qlfy_lvl_code"+t+"_val"]["ins"])!="undefined") ?  data["lb_qlfy_lvl_code"+t+"_val"]["ins"] : "0.00";
					$("label[id*=lb_rs_qlfy_ins_"+t+"]").html(tv);

					var tv = (data["lb_nxt_lvl_code"+t+"_val"] && data["lb_nxt_lvl_code"+t+"_val"]["def"] && typeof(data["lb_nxt_lvl_code"+t+"_val"]["def"])!="undefined") ?  data["lb_nxt_lvl_code"+t+"_val"]["def"] : "0.00";
					$("label[id*=lb_rs_nxt_def_"+t+"]").html(tv);

					var tv = (data["lb_nxt_lvl_code"+t+"_val"] && data["lb_nxt_lvl_code"+t+"_val"]["ins"] && typeof(data["lb_nxt_lvl_code"+t+"_val"]["ins"])!="undefined") ?  data["lb_nxt_lvl_code"+t+"_val"]["ins"] : "0.00";
					$("label[id*=lb_rs_nxt_ins_"+t+"]").html(tv);
				}
				*/
			}, "json");
	}

	function confirmVisitCode_v3(){
		var oCat = gebi("elem_category");
		var oPBC = gebi("elem_practiceBillCode");
		var olvlHis = gebi("elem_levelHistory");
		var olvlComp = gebi("elem_levelComplexity");
		var olvlService = gebi("elem_levelOfService");
		var oPoeVC = gebi("elem_poe_visit_code");
		var opov = gebi("elem_proc_only_visit");
		var opopv = gebi("elem_post_op_visit");


		var close_flg=0;
		if((oPoeVC.value == sb_cpt_code_poe)){
			setVisitCode(oPoeVC.value);
			close_flg=1;
		}else if(typeof(opov.value)!="undefined" && opov.value=="1"){close_flg=1;}
		else if(typeof(opopv.value)!="undefined" && opopv.value=="1"){setVisitCode(oPoeVC.value); close_flg=1;}
		if(close_flg==1){
			$("#dialog-Mdc-cum-new-pt").remove(); //remove mdc div
			return;
		}

		var objEye = { "elem_category":oCat.value,"elem_practiceBillCode":oPBC.value,"elem_levelOfService":olvlService.value };
		var strVisitCodeEye = makeVisitCode(objEye);

		if(strVisitCodeEye && (strVisitCodeEye != "-1") && ($.trim(strVisitCodeEye) != "")){ //&& (olvlService.value > 0)

			/*
			alert("Category: "+oCat.value+
					"\nBillCode: "+oPBC.value+
					"\nHistiry Level: "+olvlHis.value+
					"\nComplexity: "+olvlComp.value+
					"\nLevelOfService: "+olvlService.value+
					"\nVisit Code: "+strVisitCodeEye);
			*/
			//Complexity Prompt
			var prmptComp = ($.trim(olvlComp.value) == "") ? 1 : 0;
			//Set Hightest
			var lvlInsert = 5;
			if(oPBC.value == "920"){
				lvlInsert = 4;
			}

			//Check Level Code
			if(lvlInsert == olvlService.value){
				//Insert if Hightest
				//setVisitCode(strVisitCodeEye);
				confirmVisitCode_v2(); //AK: 03-09-2015: Even if they qualify for 99215 code, still show this pop-up and allow them to change to lower level
			}else{
				//Show Prompt 4 Exams Not Done
				confirmVisitCode_v2();
			}
		}
	}

	function confirmVisitCode_v2(){
		var oCat = gebi("elem_category");
		//var oPBC = gebi("elem_practiceBillCode");
		var olvlHis = gebi("elem_levelHistory");
		var olvlComp = gebi("elem_levelComplexity");
		var opov = gebi("elem_proc_only_visit");
		var opopv = gebi("elem_post_op_visit");

		var olvlSerEye = gebi("elem_levelOfServiceEye");
		var olvlSerEM = gebi("elem_levelOfServiceEM");
		var oExNotDoneEye = gebi("elem_strExmNotDoneEye");
		var oExNotDoneEM = gebi("elem_strExmNotDoneEM");
		var oElem_pls_sel_nq_em = gebi("elem_pls_sel_nq_em");
		var oElem_pls_sel_nq_eye = gebi("elem_pls_sel_nq_eye");


		var detailEye="";
		var detailEM="";

		var objEye = { "elem_category":oCat.value,"elem_practiceBillCode":"920","elem_levelOfService":olvlSerEye.value };
		var strVisitCodeEye = makeVisitCode(objEye);

		if(strVisitCodeEye){
			//make pop up content for Eye
			var obj = { "vCode":strVisitCodeEye,"elem_levelOfService":olvlSerEye.value,"elem_practiceBillCode":"920","elem_strExmNotDone": oExNotDoneEye.value, "elem_pls_sel_nq":oElem_pls_sel_nq_eye.value };
			detailEye = getVisitCodeDetail(obj);
		}

		//show E/M when selected in admin
		var str_tbl_hdr_em=["",""];
		if($("#elem_practiceBillCode").val()=="992"){

		var objEM = { "elem_category":oCat.value,"elem_practiceBillCode":"992","elem_levelOfService":olvlSerEM.value };
		var strVisitCodeEM = makeVisitCode(objEM);
		if(strVisitCodeEM){
			//make pop up content for EM
			var obj = { "vCode":strVisitCodeEM,"elem_levelOfService":olvlSerEM.value,"elem_practiceBillCode":"992","elem_strExmNotDone": oExNotDoneEM.value, "elem_pls_sel_nq":oElem_pls_sel_nq_em.value };
			detailEM = getVisitCodeDetail(obj);
		}

		str_tbl_hdr_em[0]="<td width=\"1\"></td><td width=\"550\" valign=\"top\"><b>E/M Code</b></td>";
		str_tbl_hdr_em[1]="<td width=\"3\" height=\"1\" ><div style=\"background-color:gray;width:1px;height:100%;\"></div></td><td valign=\"top\" id=\"td_em_cd\">"+detailEM+"</td>";

		}

		//if Empty then donot show prompt
		if(($.trim(detailEM) == "") && ($.trim(detailEye) == "")){
			return;
		}

		var msg = "<table class=\"table table-bordered\"  border=\"0\" width=\"100%\" >"+
				"<tr><td width=\"550\" valign=\"top\"><b>Eye Code</b></td>"+str_tbl_hdr_em[0]+"</tr>"+
				"<tr><td valign=\"top\" id=\"td_eye_cd\">"+detailEye+"</td>"+str_tbl_hdr_em[1]+"</tr>"+
			     "</table>";

		//if(  ){

			var title = "Assessed Service Level";
			var btn1="0";
			var btn2="0";

			if(typeof top.fmain == "undefined"){
				var func= ( (olvlSerEye.value != "0") || (olvlSerEM.value != "0") ) ?  "decideLevelofService2" : "decideLevelofService2" ; //"hideConfirmYesNo" ;
			}else{
				var func= ( (olvlSerEye.value != "0") || (olvlSerEM.value != "0") ) ?  "top.fmain.decideLevelofService2" : "top.fmain.decideLevelofService2"; // "hideConfirmYesNo" ;
			}
			var oArr = {};
			if(olvlSerEye.value != "0"){
				oArr["Current Eye Code"] = "Eye Code"; //strVisitCodeEye;
			}

			//show E/M when selected in admin
			if($("#elem_practiceBillCode").val()=="992"){
			if(olvlSerEM.value != "0"){
				oArr["Current E/M Code"] = "EM Code"; //strVisitCodeEM;
			}
			var pLeft=100;
			}else{
			var pLeft=200;
			}

			var misc= ( (olvlSerEye.value != "0") || (olvlSerEM.value != "0") ) ? new Array(oArr) : new Array({"OK":"OK"}) ;
			//var showCancel = ( (olvlSerEye.value != "0") || (olvlSerEM.value != "0") ) ? 1 : 0 ;


			//--

			var btnhtm="";
			var len=misc.length;
			for(var i=0;i<len;i++)
		       {
			   var tmp = misc[i];
			   for(x in tmp)
			   {
				//if(x == "Current Eye Code" || x ==  "Current E/M Code"){ if(flag==0){ flag=1; text +="<div style=\"height:20px;width:10px; display:inline-block;\"></div>"; }}//
				btnhtm+="<input type=\"button\" value=\""+x+"\" onClick=\"window."+func+"('"+tmp[x]+"')\" class=\"btn btn-success\">&nbsp;";
			   }
		       }

			btnhtm+='<input type="button" value="Cancel" onClick="window.'+func+'(-1)" class="btn btn-danger">';

			//
			msg=msg+"<div id=\"module_buttons\" class=\"pt10 row\"><div class=\"col-sm-12 text-center\"><div class=\"form-group\">"+btnhtm+"</div></div></div>";


			/*
			if(top.fmain){
				var oDiv = top.fmain.displayConfirmYesNo_v2(title,msg,btn1,btn2,func,showCancel,0,misc);
			}else{
				var oDiv = displayConfirmYesNo_v2(title,msg,btn1,btn2,func,showCancel,0,misc);
			}
			*/



			/// --
		       if($("#div_as_srv_lvl").length<=0){ //
				var htm = "<div id=\"dialog-Mdc-cum-new-pt\">"+
						"<div style=\"\">"+
						"<table class=\"table\" height=\"30\"><tr class='purple_bar'><th>Assessed Service Level<span class=\"glyphicon glyphicon-remove pull-right\" title=\"close\"></span></th></tr></table>"+
						"</div>"+
						"<div id=\"div_as_srv_lvl\"></div>"+
						"</div>";
				$("#dialog-Mdc-cum-new-pt").remove();
				$("body").append(htm);
			        $("#dialog-Mdc-cum-new-pt").draggable({"handle":"th"});
				$("#dialog-Mdc-cum-new-pt th span[title=close]").bind("click", function(){$("#dialog-Mdc-cum-new-pt").remove();});
				$("#dialog-Mdc-cum-new-pt th").css({"cursor":"move"});
		       }
		       	$("#div_as_srv_lvl").html(msg);
			/// --

			//
			sb_getVisitCodeCPTCost();

			//*
			var pTop=60;
			//var pLeft=100;
			//var thisScrollTop = document.body.scrollTop;
			//pTop += parseInt(thisScrollTop);
			//oDiv.style.top=pTop+"px";
			//oDiv.style.left=pLeft+"px";

			/* $("#div_as_srv_lvl").find("input[type=button][value='Current Eye Code']").css({"position":"absolute", "left":"20%"});
			$("#div_as_srv_lvl").find("input[type=button][value='Current E/M Code']").css({"position":"absolute", "left":"65%"});
			$("#div_as_srv_lvl").find("input[type=button][value='OK']").css({"position":"absolute", "left":"44%", "margin-top":"30px"});
			$("#div_as_srv_lvl").find("input[type=button][value='Cancel']").css({"position":"absolute", "left":"48%", "margin-top":"30px"}); */

			$("#div_as_srv_lvl").find("tr").css({"background-color":"white"});
			$("#div_as_srv_lvl").find("input[id*=lb_qlfy_lvl_code]").bind("click", function(){ //change of visit code
					var d = this.checked;
					var f = this.name;
					var e = this.id;
					$("input[type=checkbox][name*="+f+"]").each(function(index){ if(this.id == e){   $(this).prop("checked",d); }else{ $(this).prop("checked",false); }  }); //
					var clr = (d) ? "red" : "transparent";
					$("label[for*="+f+"]").each(function(index){ if($(this).attr("for") == e){   $(this).css({"border":"1px solid "+clr}); }else{ $(this).css({"border":"1px solid transparent"}); } }); //

					//proc only visit
					if(this.value == "Procedure only visit"){
						$("#elem_proc_only_visit").val("1");
					}else{
						$("#elem_proc_only_visit").val("");
					}

					//post op visit
					if(this.value == "Post Op Visit"){
						$("#elem_post_op_visit").val("1");
					}else{
						$("#elem_post_op_visit").val("");
					}

					if(d){
						opRemAllCptRow(3);
						checkAssocPqri4All();
					}else{
						sb_getVisitCodeCPTCost();
					}

					stopClickBubble();
				});
			$("#div_as_srv_lvl #td_eye_cd .seprtr").each(function(ii){  $(this).css({"width":"200px"});});

			//small pop up
			if($("#elem_practiceBillCode").val()=="920"){
				$("#dialog-Mdc-cum-new-pt").css({"width":"60%","left":"250px"});

				var lalign = ($("#div_as_srv_lvl").find("input[type=button][value='OK']").length>0) ? "50%" : "70%";
				/* $("#div_as_srv_lvl").find("input[type=button][value='OK']").css({"position":"absolute", "left":"40%", "margin-top":"0px"});
				$("#div_as_srv_lvl").find("input[type=button][value='Cancel']").css({"position":"absolute", "left":lalign,"margin-top":"0px"}); */
			}

			//*/
		//}
	}
	function sb_get_vc_opt(vcd, vpbc, vcat){

		//alert(vcd+", "+vpbc+", "+vcat);
		var vcat_in="";
		if(vcat=="New"){
			vcat_in="0";
		}else if(vcat=="Establish"){
			vcat_in="1";
		}

		if(vpbc=="920"){
			var ar =  ["Intermediate - "+vpbc+vcat_in+"2","Comprehensive - "+vpbc+vcat_in+"4"];
		}else{
			var ar = ["992"+vcat_in+"1", "992"+vcat_in+"2", "992"+vcat_in+"3", "992"+vcat_in+"4", "992"+vcat_in+"5"];
		}

		//
		//var ret = "<option value=\"\">None</option>";
		var ret ="<label class=\"lbl_asl_1\">Qualify for: </label><div class=\"seprtr\"><label for=\"lb_qlfy_lvl_code"+vpbc+"_0\">None</label></div>";
					//"<label id=\"lb_rs_qlfy_def_"+vpbc+"_0\" >Loading..</label><label id=\"lb_rs_qlfy_ins_"+vpbc+"_0\">Loading..</label>";
		//if(vcd!=""&&vcd!="None"){
			ret = "";
			for(var z in ar){
				//var sel = (vcd==ar[z]) ? "selected" : "";
				var sel = (vcd==ar[z]) ? "checked" : "";
				var tmp = ar[z].replace(/(Intermediate\s+\-\s+|Comprehensive\s+\-\s+)/g,"");
				//ret += "<option value=\""+tmp+"\" "+sel+">"+ar[z]+"</option>";
				//var lb = (z==0) ? "Qualify for: " : "";
				ret += "<div class=\"seprtr\"><input type=\"checkbox\" name=\"lb_qlfy_lvl_code"+vpbc+"\" id=\"lb_qlfy_lvl_code"+vpbc+"_"+z+"\" value=\""+tmp+"\" "+sel+" ><label for=\"lb_qlfy_lvl_code"+vpbc+"_"+z+"\">"+ar[z]+"</label></div>";
				//if(sel == "checked"){break;}
			}
			//if practice bill code is eye, then add proc only visit
			if($("#elem_practiceBillCode").val()=="920"){
				z=z+1;
				ret += "<div class=\"seprtr\" style=\"float:right; text-align:left;\"><input type=\"checkbox\" name=\"lb_qlfy_lvl_code"+vpbc+"\" id=\"lb_qlfy_lvl_code"+vpbc+"_"+z+"\" value=\""+"Procedure only visit"+"\" ><label for=\"lb_qlfy_lvl_code"+vpbc+"_"+z+"\">"+"Procedure only visit"+"</label></div>";
				z=z+1;
				ret += "<br/><div class=\"seprtr\" style=\"float:right;text-align:left;\"><input type=\"checkbox\" name=\"lb_qlfy_lvl_code"+vpbc+"\" id=\"lb_qlfy_lvl_code"+vpbc+"_"+z+"\" value=\""+"Post Op Visit"+"\" ><label for=\"lb_qlfy_lvl_code"+vpbc+"_"+z+"\">"+"Post Op Visit"+"</label></div>";
			}

			if(ret != ""){
				ret = "<label class=\"lbl_asl_1\">Qualify for: </label>"+ret;
				ret +="<br><label class=\"lbl_asl_1\" id=\"lb_rs_def_v\">Default Fees: </label><label id=\"lb_rs_qlfy_def_"+vpbc+"\" >Loading..</label>"+
					  "<br><label class=\"lbl_asl_1\" id=\"lb_rs_ins_v\">Ins. Contract Fees: </label><label id=\"lb_rs_qlfy_ins_"+vpbc+"\">Loading..</label>";
			}
		//}
		return ret;
	}
	function getVisitCodeDetail(obj){
		var oPBC = obj["elem_practiceBillCode"];
		var objCategory = gebi("elem_category");
		var vCode_cpt = vCode = obj["vCode"];
		var pls_sel_nq = obj["elem_pls_sel_nq"];

		var msg = "";

		if( vCode != false ){
			var oLoS =  obj["elem_levelOfService"];
			var oNotDoneExm = obj["elem_strExmNotDone"];
			//var oExsHist = gebi("elem_levelHistory");
			//var oComp = gebi("elem_levelComplexity");
			var arrNotDoneExm = (typeof oNotDoneExm != "undefined") ? oNotDoneExm.split(",") : new Array() ;
			//if(oLoS.value < "5"){
				var nxtVCode = (oLoS < "5"&&pls_sel_nq=="") ? eval(vCode)+1:eval(vCode);
				if( oPBC == "920" ){

					if( oLoS == "2" ){
						vCode = "Intermediate";
					}else if( oLoS == "4" ){
						vCode = "Comprehensive";
					}
					nxtVCode = (oLoS <= "1") ? "Intermediate" : "Comprehensive";
				}else{
					if(pls_sel_nq==""){
						if(nxtVCode==99201){nxtVCode = "99202";}else if(nxtVCode==99211){nxtVCode = "99212";}
					}
				}

				///
				if(pls_sel_nq!=""){nxtVCode=vCode;}

				var nxtLos = getLOSfromCode( nxtVCode );

				if( (oLoS == "0") ){
					vCode = "None";
					vCode_cpt="";
				}else{
					vCode_cpt=" - "+vCode_cpt;
				}

				if( vCode == nxtVCode && pls_sel_nq=="" ){
					nxtVCode = "None";
				}

				if(oPBC == "992"){vCode_cpt="";}

				//msg = "<br><label class=\"lb_rs_def_v\" >Default Fees</label><label class=\"lb_rs_ins_v\" style=\"width:125px;margin-right:0px;\">Ins. Contract Fees</label>"+
				msg = ""+
						//"<br><label class=\"lbl_asl_1\">Qualify for: </label><select id=\"lb_qlfy_lvl_code"+oPBC+"\" >"+sb_get_vc_opt(vCode+""+vCode_cpt, oPBC, objCategory.value)+"</select>";
						"<br>"+sb_get_vc_opt(vCode+""+vCode_cpt, oPBC, objCategory.value)+"";
				//if(nxtVCode!="None"){msg +=    "<br><label class=\"lbl_asl_1\" >Next level: </label><label id=\"lb_nxt_lvl_code"+oPBC+"\" >"+(nxtVCode)+"</label><label id=\"lb_rs_nxt_def_"+oPBC+"\" >Loading..</label><label id=\"lb_rs_nxt_ins_"+oPBC+"\">Loading..</label>";}
				msg +="<br>";

				if( ((oLoS < "5") && ( vCode != "Comprehensive" ))||(pls_sel_nq!="") ){

					var tmp_list = "";
					//Exams
					var arrExmCate = new Array();
					var len = arrNotDoneExm.length;
					for(var i=0;i<len;i++){
						if((typeof arrNotDoneExm[i] != "undefined") && (arrNotDoneExm[i] != "")){
							if( (arrNotDoneExm[i].indexOf("Fundus Exams") != -1) || ( arrNotDoneExm[i].indexOf("SLE") != -1 ) ||
								(arrNotDoneExm[i].indexOf("L&A") != -1) ){
								if( arrNotDoneExm[i].indexOf("-") != -1 ){
									var arrTmpCate = arrNotDoneExm[i].split("-");
									var key = $.trim(arrTmpCate[0]);
									var val = $.trim(arrTmpCate[1]);
									if(typeof arrExmCate[ key ] == "undefined"){
										arrExmCate[ key ] = new Array();
									}
									arrExmCate[ key ].push(val);
								}
							}else{
								//
								tmp_list  += "<br>   - "+arrNotDoneExm[i];
							}
						}
					}
					//Other Categorized
					for( var i in arrExmCate ){
						tmp_list += "<br>   - "+i;
						var len2 = arrExmCate[i].length;
						for( var j=0;j<len2;j++ ){
							var strSubCate = $.trim( arrExmCate[i][j] );
							if( strSubCate != "" ){
								strSubCate = strSubCate.replace(/;/g, ", ");
								tmp_list += "<br>&nbsp;&nbsp;&nbsp;&nbsp;- "+strSubCate;
							}
						}
					}

					if(tmp_list!=""){
						msg+="<br>List items that must be completed to qualify for level - "+nxtVCode+"";
						msg+="<div class=\"dv_ls_todo_exm\">"+tmp_list+"</div>";
					}
				}
			msg += "";
		}
		return msg;
	}


	//Super Bill
	function checkCurrRowId(currRowId){
		var tbl = gebi("tblSuperbill");
		var lastRow = tbl.rows.length;
		for(var i=0;i<lastRow;i++){
			//var temp = tbl.rows[i];
			var tId = "elem_trSB"+(i+1);
			var temp = gebi(tId);
			if(temp == null){
				currRowId=(i+1);
				break;
			}
		}
		return currRowId;
	}

	function getRealIndx(id, strTr, strTbl){
		if( typeof strTbl == "undefined" ){
			strTbl = "tblSuperbill";
			strTr = "elem_trSB";
		}

		var rowId = strTr+id;
		var tbl = gebi(strTbl);
		var lastRow = tbl.rows.length;
		var i=0;
		for(i=0;i<lastRow;i++){
			var temp = tbl.rows[i];
			if(rowId == temp.id){
			return i;
			break;
			}
		}

		if(id=="LASTINDX"){
			return i-1;
		}

		return 0;
	}

	function editSBRowValues( oVal ){
		var strCpt = oVal.cptCode;
		var arrMd = new Array();
		arrMd = oVal.arrMd;

		if( $.trim(strCpt) != "" ){
			var mxId = gebi("elem_mxSBId").value;
			var tbl = gebi("tblSuperbill");
			var lastRow = tbl.rows.length;
			for(var i=0;i<mxId;i++){
				//var temp = tbl.rows[i];
				var tId = "elem_cptCode_"+(i+1);
				var temp = gebi(tId);
				if(temp && ( temp.value != "") && ( typeof temp.value != "undefined" ) && ( $.trim(temp.value) == $.trim(strCpt) )){
					//Edit Values
					//Md
					for(var j=1;j<=4;j++){
						var oMd = gebi("elem_modCode_"+(i+1)+"_"+j);
						if( oMd ){
							oMd.value = (typeof arrMd[j-1] != "undefined") ? ""+arrMd[j-1] : "";
						}
					}
				}
			}
		}
	}

	function Save_form(i,cnfrm){
		//
		if(typeof(del_proc_noti)=="undefined" || del_proc_noti=="0" || del_proc_noti==""){ cnfrm=true; }

		if(typeof(cnfrm)=="undefined") {
			var str= "top.Save_form('"+i+"',true)";
			if(typeof(top.fmain) != "undefined"){
				if(typeof(top.fmain.ifrm_FolderContent) != "undefined" && typeof(top.fmain.ifrm_FolderContent.Save_form) == "function"){
					var str= "top.fmain.ifrm_FolderContent.Save_form('"+i+"',true)" ;
				}else{var str= "top.fmain.Save_form('"+i+"',true)";}
			}

			top.fancyConfirm("Would you like to delete the selected CPT row from superbill ?","",""+str);
		}
		else{
			opRemCptRow(i);
			if(document.getElementById('save_form')){
				document.getElementById('save_form').value='1';
				document.frmSuperBill.submit();
			}
		}
	}
	function opAddCptRow(indx,oVal,flagStpGtMx,fRindx,sup_enc_chk){

		var enc_chk=false;
		if(sup_enc_chk==1){
			enc_chk=true;
		}
		var cptCode="";
		var procId="";
		var units="1";
		var arrDx = new Array();
		var arrMd = new Array();
		var tmp="";
		var cptDesc = "";
		flagStpGtMx = (typeof flagStpGtMx != "undefined") ? flagStpGtMx : 0;
		if(typeof sb_pdiv == "undefined" && gebi("divWorkView")){
			sb_pdiv = "divWorkView";
		}
		if(typeof oVal != "undefined" && oVal!=""){

			cptCode = oVal.cptCode ;
			//if(!isCptCodeEnterSB(cptCode)){
			procId = oVal.procId ;

			units = oVal.units ;
			arrDx = oVal.arrDx ;

			arrMd = oVal.arrMd ;
			cptDesc = ( typeof oVal.cptDesc != "undefined" ) ? oVal.cptDesc : "" ;
			//alert("ARRMD: "+arrMd.length)
			/*}else{
				editSBRowValues(oVal);
				return;
			}*/

		}
		/*
		//--
		var w_cpt = $(".dxcodeshd td").eq(0).prop("width");
		var w_unit = $(".dxcodeshd td").eq(1).prop("width");
		var w_dx = $(".dxcodeshd td").eq(2).prop("width");
		var w_md = $(".dxcodeshd td").eq(3).prop("width");
		var w_ed = $(".dxcodeshd td").eq(6).prop("width");
		//--

		console.log(w_cpt, w_unit, w_dx, w_md, w_ed);
		*/
		var w_dx = $(".dxcodeshd td").eq(2).prop("width");//dxwidth
		var oTbl = $("#tblSuperbill").get(0);
		var lastRow = oTbl.rows.length;
		var currRowId = lastRow+1;
		var elemId;

		//insert at end
		if(indx=="new"){  indx=lastRow;  }

		//Insert
		var rIndx = (fRindx==1) ? indx : getRealIndx(indx);
		rIndx = parseInt(rIndx);

		var row = oTbl.insertRow(rIndx+1);
		currRowId = checkCurrRowId(currRowId);
		row.id = "elem_trSB"+currRowId;
		/*
		var cellSno = row.insertCell(0); //sno cell
		$(cellSno).css({"cursor":"pointer"}).bind("click", function(){ sb_swap_vals(this); });
		cellSno.innerHTML = ""+
						""+currRowId+".";
		*/
		var cellCpt = row.insertCell(0); //cpt cell
		$(cellCpt).addClass("cpt_td");
		cellCpt.innerHTML = ""+
			"<div class=\"input-group\">"+
				"<label class=\"pointer input-group-addon text-center contno\" onclick=\"sb_swap_vals(this)\">"
						+currRowId+
				"</label>"+
				"<input type=\"text\" id=\"elem_cptCode_"+currRowId+"\" name=\"elem_cptCode_"+currRowId+"\" value=\""+cptCode+"\" title=\""+cptDesc+"\" class=\"cptcode form-control\" onblur=\"checkCptCodesChart(this);\">"+

			//""+getSimpleMenuJs("elem_cptCode_"+currRowId,"menu_CptCats4Menu",imgPath,"0","0",sb_pdiv)+""+

			"<input type=\"hidden\" id=\"elem_procedureId_"+currRowId+"\" name=\"elem_procedureId_"+currRowId+"\" value=\""+procId+"\">"+
			"<input type=\"hidden\" id=\"elem_procedureOrder_"+currRowId+"\" name=\"elem_procedureOrder_"+currRowId+"\" value=\""+(rIndx+1)+"\">"+
			//"<input type=\"hidden\" name=\"elem_procUnits_"+currRowId+"\" value=\""+units+"\" >"+

			"</div>";

		//TypeAhead
		//new actb(gebi("elem_cptCode_"+currRowId),arrCptCodeAndDesc);



		//Units
		var cellUnits = row.insertCell(1);
		$(cellUnits).addClass("unit");
		cellUnits.innerHTML = "<input type=\"text\" id=\"elem_procUnits_"+currRowId+"\"  name=\"elem_procUnits_"+currRowId+"\" value=\""+units+"\" class=\"cptunit form-control\" onchange=\"checkProcUnitsChart(this);\">";

		//dx
		/*
		for(var i=1;i<=4;i++){
			var cellDx = row.insertCell(i+1);
			elemId = "elem_dxCodeAssoc_"+currRowId+"_"+i;
			var oeDx=gebi("elem_dxCode_"+i);
			var chkCB = "";
			if(arrDx.length > 0){

				for(var j=0;j<4;j++){

					if( (typeof arrDx[j] != "undefined") && (oeDx.value != "") && (oeDx.value == arrDx[j]) ){

						//alert(arrDx[j]+"\n"+oeDx.value);
						chkCB = "checked";
						break;
					}
				}
			}

			cellDx.innerHTML =	"<input type=\"checkbox\" id=\""+elemId+"\" name=\""+elemId+"\" value=\"1\" onclick=\"checkSBDxCodeFilled(this,'"+i+"')\"  "+chkCB+" >";

		}*/
		var dxw = $("#elem_trSB1 .dx").attr("width");
		var cellDx = row.insertCell(2);
		elemId = "elem_dxCodeAssoc_"+currRowId;
		cellDx.innerHTML = "<select  id=\""+elemId+"\" name=\""+elemId+"[]\" multiple=\"multiple\" data-width=\"100%\" class=\"diagText_all_css minimal selectpicker dropupalways\" data-actions-box=\"true\" title=\"\">"+
						"</select>";
		cellDx.width = dxw;
		var dxCdId = elemId;


		for(var i=1;i<=4;i++){
			var cellMod = row.insertCell(i+2);
			$(cellMod).addClass("md");
			elemId = "elem_modCode_"+currRowId+"_"+i;
			tmp = (typeof arrMd[i-1] != "undefined") ? ""+arrMd[i-1] : "";
			cellMod.innerHTML = ""+
					"<div class=\"input-group\"><input type=\"text\" id=\""+elemId+"\" name=\""+elemId+"\" value=\""+tmp+"\" class=\"form-control modcode\" onblur=\"checkModCodesChart(this);\"></div>"+
					//""+getSimpleMenuJs(elemId,"menu_MdCodes",imgPath,"0","0",sb_pdiv)+
					"";

			//new actb(gebi(elemId),arrMdCodesTypeAhead);
		}
		//Last
		var cellLast = row.insertCell(7);
		$(cellMod).prop("valign","middle");
		//alert(imgPath);

		/*
		if(enc_chk==true){
			document.getElementById('sm_elem_cptCode_'+currRowId).innerHTML="<img src=\""+imgPath+"/images/scrollDown.gif\"  style=\"cursor:pointer\">";
			document.getElementById('sm_elem_modCode_'+currRowId+'_1').innerHTML="<img src=\""+imgPath+"/images/scrollDown.gif\"  style=\"cursor:pointer\">";
			document.getElementById('sm_elem_modCode_'+currRowId+'_2').innerHTML="<img src=\""+imgPath+"/images/scrollDown.gif\"  style=\"cursor:pointer\">";
			document.getElementById('sm_elem_modCode_'+currRowId+'_3').innerHTML="<img src=\""+imgPath+"/images/scrollDown.gif\"  style=\"cursor:pointer\">";
			cellLast.innerHTML = "<img style=\"cursor:pointer;\" src=\""+imgPath+"/images/acc_add_img.png\" title=\"Add More\" onClick=\"opAddCptRow('"+currRowId+"','','','','1');\">&nbsp;<img style=\"cursor:pointer;\" src=\""+imgPath+"/images/cancelled.gif\" title=\"Delete Row\" onClick=\"opRemCptRow('"+currRowId+"');\">";
		}else{
			cellLast.innerHTML = "<a href=\"javascript:void(0);\" onclick=\"opAddCptRow('"+currRowId+"')\">+</a>&nbsp;<a href=\"javascript:void(0);\" onclick=\"opRemCptRow('"+currRowId+"')\">x</a>";
		}*/

		/*
		cellLast.innerHTML = "<img src=\""+zPath+"/../library/images/closerd.png\" alt=\"Delete\" onclick=\"Save_form('"+currRowId+"');\" title=\"Delete\" /> "+
						"<img src=\""+zPath+"/../library/images/add_icon.png\" alt=\"Insert\" onclick=\"opAddCptRow('"+currRowId+"')\" title=\"Insert\"  />";
		*/

		cellLast.innerHTML = "<span class=\"glyphicon glyphicon-remove\" onclick=\"Save_form('"+currRowId+"');\" title=\"Delete\" ></span> "+
						"<span class=\"glyphicon glyphicon-plus\" onclick=\"opAddCptRow('"+currRowId+"')\" title=\"Insert\"  ></span> ";


		if( flagStpGtMx != 1 ){
			var objsbnr = getMxSB(1);
		}

		//blur
		gebi("elem_cptCode_"+currRowId).onblur();

		sb_addTypeAhead();
		// Set Today Charges
		//setSBTodayCharges();
		/*
		$("#"+dxCdId).multiselect({
			selectedList: 12,
			noneSelectedText:'Select Dx Codes',
			 position: {
				my: 'left bottom',
				at: 'left top'
			}
		});
		*/
		sb_crt_dx_dropdown("#"+dxCdId,'',arrDx);
		fun_mselect("#"+dxCdId, 'render') ; //$("#"+dxCdId).selectpicker('render');
		fun_mselect("#"+dxCdId, 'onchange', function(){ dx_assoc_cpt(this); });
		fun_mselect("#"+dxCdId, 'width') ;



		///reorder serials
		sb_reorder_srno();
		// Add menues in superbill
		sb_add_menu();
	}

	function sb_reorder_srno(){ $("#tblSuperbill tr").each(function(i){ $(this).find(".contno").html(""+(i+1)+"");  }); }

	var st_sb_dx = ed_sb_dx = st_sb_rw = ed_sb_rw = 0;
	function sb_swap_vals(obj){

		if(typeof(elem_per_vo)=="undefined"){ elem_per_vo="0";  }
		if(typeof(finalize_flag)=="undefined"){ finalize_flag="0";  }
		if(typeof(isReviewable)=="undefined"){ isReviewable="0";  }


		if((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1"))){ return;}
		var id = ($(obj).parents("#tblSuperbill").length>0) ? "tblSuperbill" : $(obj).parents("table").attr("id"); //tblSuperbill

		if(id == "tblSuperbill"){
			//
			var num = ""+$(obj).parents("tr").attr("id");
			num = parseInt(num.replace(/elem_trSB/,""));

			if(st_sb_rw == 0){
				st_sb_rw = num;
				obj.style.color = "red";
				return;
			}else{
				ed_sb_rw = num;
			}

			if((st_sb_rw != 0) && (ed_sb_rw != 0)){
				//$("#tblSuperbill>tr td:first-child").css("color","black");
				$("#tblSuperbill tr").each(function(i){ $(this).find("td:first-child .contno").css("color","white");  });
				//ap_rowAdj(st_ap_adjt,ed_ap_adjt);

				//
				//console.log(st_sb_rw, ed_sb_rw);

				var tcv = $("#elem_cptCode_"+ed_sb_rw).val();
				var tct = $("#elem_cptCode_"+ed_sb_rw).attr("title");
				var tcvd = $("#elem_cptCode_"+ed_sb_rw).attr("valid_dxcodes");

				var tcpv = $("#elem_procedureId_"+ed_sb_rw).val();
				var tcov = $("#elem_procedureOrder_"+ed_sb_rw).val();
				var tcuv = $("#elem_procUnits_"+ed_sb_rw).val();

				var tcm1v = $("#elem_modCode_"+ed_sb_rw+"_1").val();
				var tcm1t = $("#elem_modCode_"+ed_sb_rw+"_1").attr("title");
				var tcm2v = $("#elem_modCode_"+ed_sb_rw+"_2").val();
				var tcm2t = $("#elem_modCode_"+ed_sb_rw+"_2").attr("title");
				var tcm3v = $("#elem_modCode_"+ed_sb_rw+"_3").val();
				var tcm3t = $("#elem_modCode_"+ed_sb_rw+"_3").attr("title");
				var tcm4v = $("#elem_modCode_"+ed_sb_rw+"_4").val();
				var tcm4t = $("#elem_modCode_"+ed_sb_rw+"_4").attr("title");

				//var tcdxv = $("#elem_dxCodeAssoc_"+ed_sb_rw).val();
				var tcdxv = ""+fun_mselect("#elem_dxCodeAssoc_"+ed_sb_rw, 'val') ; //""+$("#elem_dxCodeAssoc_"+ed_sb_rw).selectpicker('val'); //""+$("#elem_dxCodeAssoc_"+ed_sb_rw).multiselect("getChecked").map(function(){ return this.value; }).get();
				var tcdxt = ""+fun_mselect("#elem_dxCodeAssoc_"+ed_sb_rw, 'title') ; //$("#elem_dxCodeAssoc_"+ed_sb_rw).multiselect("getButton").prop("title");

				//t
				$("#elem_cptCode_"+ed_sb_rw).val($("#elem_cptCode_"+st_sb_rw).val());
				$("#elem_cptCode_"+ed_sb_rw).attr("title",$("#elem_cptCode_"+st_sb_rw).attr("title"));
				$("#elem_cptCode_"+ed_sb_rw).attr("valid_dxcodes", $("#elem_cptCode_"+st_sb_rw).attr("valid_dxcodes"));
				$("#elem_procedureId_"+ed_sb_rw).val($("#elem_procedureId_"+st_sb_rw).val());
				$("#elem_procedureOrder_"+ed_sb_rw).val($("#elem_procedureOrder_"+st_sb_rw).val());
				$("#elem_procUnits_"+ed_sb_rw).val($("#elem_procUnits_"+st_sb_rw).val());
				$("#elem_modCode_"+ed_sb_rw+"_1").val($("#elem_modCode_"+st_sb_rw+"_1").val());
				$("#elem_modCode_"+ed_sb_rw+"_1").attr("title", $("#elem_modCode_"+st_sb_rw+"_1").attr("title"));
				$("#elem_modCode_"+ed_sb_rw+"_2").val($("#elem_modCode_"+st_sb_rw+"_2").val());
				$("#elem_modCode_"+ed_sb_rw+"_2").attr("title", $("#elem_modCode_"+st_sb_rw+"_2").attr("title"));
				$("#elem_modCode_"+ed_sb_rw+"_3").val($("#elem_modCode_"+st_sb_rw+"_3").val());
				$("#elem_modCode_"+ed_sb_rw+"_3").attr("title", $("#elem_modCode_"+st_sb_rw+"_3").attr("title"));
				$("#elem_modCode_"+ed_sb_rw+"_4").val($("#elem_modCode_"+st_sb_rw+"_4").val());
				$("#elem_modCode_"+ed_sb_rw+"_4").attr("title", $("#elem_modCode_"+st_sb_rw+"_4").attr("title"));
				//dx
				var scdxv = ""+fun_mselect("#elem_dxCodeAssoc_"+st_sb_rw, 'val') ; // ""+$("#elem_dxCodeAssoc_"+st_sb_rw).multiselect("getChecked").map(function(){ return this.value; }).get();
				var scdxt = ""+fun_mselect("#elem_dxCodeAssoc_"+st_sb_rw, 'title') ; //$("#elem_dxCodeAssoc_"+st_sb_rw).multiselect("getButton").prop("title");
				//$("#elem_dxCodeAssoc_"+ed_sb_rw).multiselect("widget").find(":checkbox").each(function(){  var art=this.value.split('\*\*');  this.checked=(scdxv.indexOf(art[0])!=-1) ? true : false; }); //uncheck
				//$("#elem_dxCodeAssoc_"+ed_sb_rw).multiselect("update");
				//$("#elem_dxCodeAssoc_"+ed_sb_rw).multiselect("getButton").prop("title",scdxt);
				fun_mselect("#elem_dxCodeAssoc_"+ed_sb_rw, 'select', scdxv, scdxt) ;


				//s
				$("#elem_cptCode_"+st_sb_rw).val(tcv);
				$("#elem_cptCode_"+st_sb_rw).attr("title",tct);
				renew_title("#elem_cptCode_"+st_sb_rw,tct);
				$("#elem_cptCode_"+st_sb_rw).attr("valid_dxcodes", tcvd);
				$("#elem_procedureId_"+st_sb_rw).val(tcpv);
				$("#elem_procedureOrder_"+st_sb_rw).val(tcov);
				$("#elem_procUnits_"+st_sb_rw).val(tcuv);
				$("#elem_modCode_"+st_sb_rw+"_1").val(tcm1v);
				$("#elem_modCode_"+st_sb_rw+"_1").attr("title", tcm1t);
				renew_title("#elem_modCode_"+st_sb_rw+"_1",tcm1t);
				$("#elem_modCode_"+st_sb_rw+"_2").val(tcm2v);
				$("#elem_modCode_"+st_sb_rw+"_2").attr("title", tcm2t);
				renew_title("#elem_modCode_"+st_sb_rw+"_2",tcm2t);
				$("#elem_modCode_"+st_sb_rw+"_3").val(tcm3v);
				$("#elem_modCode_"+st_sb_rw+"_3").attr("title", tcm3t);
				renew_title("#elem_modCode_"+st_sb_rw+"_3",tcm3t);
				$("#elem_modCode_"+st_sb_rw+"_4").val(tcm4v);
				$("#elem_modCode_"+st_sb_rw+"_4").attr("title", tcm4t);
				renew_title("#elem_modCode_"+st_sb_rw+"_4",tcm4t);
				//$("#elem_dxCodeAssoc_"+st_sb_rw).multiselect("widget").find(":checkbox").each(function(){  var art=this.value.split('\*\*');  this.checked=(tcdxv.indexOf(art[0])!=-1) ? true : false; }); //uncheck
				//$("#elem_dxCodeAssoc_"+st_sb_rw).multiselect("update");
				//$("#elem_dxCodeAssoc_"+st_sb_rw).multiselect("getButton").prop("title",tcdxt);
				fun_mselect("#elem_dxCodeAssoc_"+st_sb_rw, 'select', tcdxv, tcdxt) ;

				getMxSB();

				//Reset global
				st_sb_rw = ed_sb_rw = 0;
			}

		}else{
			//
			var num = parseInt($(obj).html());
			if(st_sb_dx == 0){
				st_sb_dx = num;
				obj.style.color = "red";
				return;
			}else{
				ed_sb_dx = num;
			}
			//
			if((st_sb_dx != 0) && (ed_sb_dx != 0)){
				//$("#tblSBDX>tr th").css("color","black");
				$("#tblSBDX tr").each(function(i){ $(this).find("th").css({"color":"black"});  });

				//Swap--
				var tv=$("#elem_dxCode_"+ed_sb_dx).val();
				var tt=$("#elem_dxCode_"+ed_sb_dx).attr("title");
				var tlv=$("#lit_diagText_"+ed_sb_dx).val();
				var tdocv=$("#dx_oldCode_"+ed_sb_dx).val();
				//t
				$("#elem_dxCode_"+ed_sb_dx).val($("#elem_dxCode_"+st_sb_dx).val());
				$("#elem_dxCode_"+ed_sb_dx).attr("title", $("#elem_dxCode_"+st_sb_dx).attr("title"));
				$("#lit_diagText_"+ed_sb_dx).val($("#lit_diagText_"+st_sb_dx).val());
				$("#dx_oldCode_"+ed_sb_dx).val($("#dx_oldCode_"+st_sb_dx).val());
				//s
				$("#elem_dxCode_"+st_sb_dx).val(tv);
				$("#elem_dxCode_"+st_sb_dx).attr("title", tt);
				renew_title("#elem_dxCode_"+st_sb_dx,tt);
				$("#lit_diagText_"+st_sb_dx).val(tlv);
				$("#dx_oldCode_"+st_sb_dx).val(tdocv);

				//Reset global
				st_sb_dx = ed_sb_dx = 0;
			}
		}
	}


	function emptyFirstRowSB(){
		for(var i=1;i<=4;i++){
			if(i==1){
				var cptCode = gebi("elem_cptCode_"+i);
				var procid =  gebi("elem_procedureId_"+i);
				var units = gebi("elem_procUnits_"+i);

				cptCode.value = cptCode.title = "";
				procid.value = "";
				units.value = "1";
				sb_crt_dx_dropdown("#elem_dxCodeAssoc_"+i, flgSetEmpty=1);
				renew_title(cptCode,"");

			}
			if(i<=4){
				var mdCode = gebi("elem_modCode_1_"+i);
				mdCode.value = "";
			}

			//var dxCode = gebi("elem_dxCodeAssoc_1_"+i);
			//dxCode.checked = false;
		}
	}

	function opRemAllCptRow(n){
		if(typeof(n)=="undefined")n = 1;
		var tbl = gebi("tblSuperbill");
		if(!tbl) return;
		var lastRow = tbl.rows.length;
		for(var i=lastRow-1;i>=0;i--){
			if(i == 0){
				//First Row
				emptyFirstRowSB();
				break;
			}else{
				//
				tbl.deleteRow(i); //Delete
			}
		}
		if(n>1){	for(var i=1;i<n;i++){opAddCptRow("LASTINDX");}	}
		//
		var objsbnr = getMxSB();
	}

	function opRemCptRow(indx){
		//alert(indx)
		var tbl = gebi("tblSuperbill");
		var lastRow = tbl.rows.length;
		var rIndx = getRealIndx(indx);

		//-
		//if
		if(typeof(indx)!="undefined"){
			var v = $("#elem_procedureId_"+indx).val();
			if(typeof(v)!="undefined" && v!=""){
				var y = $("#elem_proc_del_id").val();
				if(y!=""){ y+=","; }
				$("#elem_proc_del_id").val(y+v);
			}
		}
		//-

		if(indx > 1){
			tbl.deleteRow(rIndx); //Delete
		}else{
			emptyFirstRowSB();
		}

		var objsbnr = getMxSB();

		// Set Today Charges
		setSBTodayCharges();

		//reorder sup bill sno.
		sb_reorder_srno();
	}

	function getMxSB(flgAdd){

		var tbl = gebi("tblSuperbill");
		var lastRow = tbl.rows.length;
		var str = "";
		var mx = 0;
		var strCode = "";
		var strUnit = "";
		var tmpUnit = "";
		var isCptElemVacant=1;
		var oCpt,flagLastRow=0;

		var checkId="";
		for(var i=0;i<lastRow;i++){
			var temp = tbl.rows[i];
			str += ""+temp.id+",";
			var id = temp.id.replace(/elem_trSB/, "");
			mx = (id > mx) ? id : mx;
			var cptId = temp.id.replace(/elem_trSB/, "elem_cptCode_");
			var unitId = temp.id.replace(/elem_trSB/, "elem_procUnits_");
			oCpt = gebi(cptId);
			var oUnit = gebi(unitId);
			checkId += id+", ";

			if(oCpt && (typeof oCpt.value != "undefined" ) && ($.trim(oCpt.value) != "")){
				strCode += (strCode != "") ? ","+oCpt.value : ""+oCpt.value;
				tmpUnit = (oUnit && (typeof oUnit.value != "undefined" ) && ($.trim(oUnit.value) != "")) ? oUnit.value : 1;
				strUnit += (strUnit != "") ? ","+tmpUnit : ""+tmpUnit;
				flagLastRow=i;

				//Procedure Order --
				var proOrdrId = temp.id.replace(/elem_trSB/, "elem_procedureOrder_");
				proOrdrId = gebi(proOrdrId);
				proOrdrId.value=(i+1);
				//Procedure Order --

			}else{
				flagLastRow=0;
			}
		}

		lastRow = ( (typeof lastRow != "undefined")) ? lastRow : 4 ;
		mx = (mx > lastRow) ? mx : lastRow ;

		strCode = $.trim(strCode);
		strCode = strCode.replace(/,$/,"");
		strUnit = $.trim(strUnit);
		strUnit = strUnit.replace(/,$/,"");
		gebi("elem_mxSBId").value = mx;
		gebi("elem_procOrder").value = strCode;
		gebi("elem_procUnitOrder").value = strUnit;

		//if(flagLastRow>0){
			//alert(flagLastRow+" - "+lastRow+"--"+mx);
			//oCpt = gebi("elem_cptCode_"+flagLastRow);
			//if(oCpt && (typeof oCpt.value != "undefined" ) && ($.trim(oCpt.value) != "")){
				//opAddCptRow(lastRow);
				//return;
			//}
		//}

		if(flgAdd==1 && flagLastRow!=0){
			//return {"flagLastRow":flagLastRow,"mx":mx,"lastRow":lastRow};
			//alert("flagLastRow: "+flagLastRow+",mx: "+mx+", lastRow: "+lastRow+", \n\ncheckId:"+checkId+"\n\n"+strCode);
			if(sup_enc==1){
				opAddCptRow("LASTINDX",'','','',sup_enc);
			}else{
				opAddCptRow("LASTINDX");
			}
		}
	}

	//Super Bill
	function checkChiefComplaintDetailed(){
		var ret="false";

		var c = getRvsDoneSt();
		if(c>=3){ret="true";}


		return ret;
	}
	var zChiefCompElemName="";
	function decideChiefComp(val){
		//hideConfirmYesNo();
		if(val == "1"){
			// OK
			//alert("OK");
		}else if(val == "0"){
			// Remove Cpt
			var oElem = gebi(zChiefCompElemName);
			if(oElem){
				oElem.value = "";
			}
		}
		zPqriElemName="";
		//
		var objsbnr = getMxSB();

		//Set Today Charges
		setSBTodayCharges();
	}

	function getCptDescJs( cptCode ){
		var ret = "";
		cptCode = $.trim( cptCode );
		if( ( cptCode != "" ) && (typeof arrCptCodeDescActive[0] != "undefined")){
			for( var x in arrCptCodeDescActive[0]){
				if(arrCptCodeDescActive[0][x] == cptCode){
					ret = x;
					break;
				}
			}
		}
		return ret;
	}

	var sb_cw4WD_flgnoalert="";
	function sb_checknwarn4WrongDxcode(obj){

		//
		var flgnoalert = sb_cw4WD_flgnoalert;
		if(typeof(flgnoalert)=="undefined" || flgnoalert==""){flgnoalert=0;}
		var v_icd_10 = $("#hid_icd10").val();

		//get count
		var indx = obj.id.replace(/elem_cptCode_|elem_dxCodeAssoc_/g,"");

		//alert(obj.id+" is now "+indx);

		var valid_dxCode="", cur_dxCode ="";
		if(obj.id.indexOf("elem_cptCode_")!=-1){
			var proc_code = $.trim(""+obj.value);
			//get valid dx code
			valid_dxCode=obj.getAttribute("valid_dxcodes");

			//get cur_dx_code
			//cur_dxCode = ""+$("#elem_dxCodeAssoc_"+indx).val();
			//cur_dxCode = ""+$("#elem_dxCodeAssoc_"+indx).multiselect("getChecked").map(function(){ return this.value; }).get();
			cur_dxCode = ""+fun_mselect("#elem_dxCodeAssoc_"+indx, 'val') ;

		}else{
			//check cpt code and valid dx codes
			//get cur_cpt_code
			var proc_code = $("#elem_cptCode_"+indx).val();
			//get valid dx code
			valid_dxCode=$("#elem_cptCode_"+indx).attr("valid_dxcodes");
			//get cur_dx_code
			//cur_dxCode = ""+$(obj).val();
			//cur_dxCode = ""+$(obj).multiselect("getChecked").map(function(){ return this.value; }).get();
			cur_dxCode = ""+fun_mselect(obj, 'val') ;
		}

		if(typeof(valid_dxCode)!="undefined"){ valid_dxCode=""+valid_dxCode; }
		if(typeof(proc_code)!="undefined"){ proc_code=""+proc_code; }

		//cpt code empty
		if(proc_code==""){
			//set Title dx
			var strtitle="";

			//*
			$("#elem_dxCodeAssoc_"+indx).find('option').each(function() {

				if(cur_dxCode.indexOf(""+$(this).val())!=-1){
					strtitle+=this.text+" - "+$(this).data("desc")+"\n";
				}

				});
			//*/

			//$("#elem_dxCodeAssoc_"+indx).prop("title",strtitle);
			//$("#elem_dxCodeAssoc_"+indx).multiselect("getButton").prop("title",strtitle);
			fun_mselect("#elem_dxCodeAssoc_"+indx, "settitle", strtitle );
			return;
		}



		//
		if(v_icd_10=="1"){
		if(valid_dxCode && valid_dxCode!="null" && valid_dxCode!="" && typeof(valid_dxCode)!="undefined"  && cur_dxCode && cur_dxCode!="null" && typeof(cur_dxCode) !="undefined" && cur_dxCode !="" ){
			//alert("Enter 0");
			var ar_valid_dxCode=valid_dxCode.toLowerCase().split(",");
			var ar_cur_dxCode=cur_dxCode.split(",");

			//alert("Enter 0.1"+ar_cur_dxCode);

			var ar_wrong_dx = [];

			//alert(""+valid_dxCode+" -- "+cur_dxCode+" -- "+cur_dxCode.length+" - "+typeof(cur_dxCode)+" -- "+ar_cur_dxCode.length);

			if(ar_cur_dxCode.length > 0 && valid_dxCode.length > 0){

				for(var x in ar_cur_dxCode){
					var tmp = $.trim(ar_cur_dxCode[x]);
					if(tmp=="")continue;

					var arr_tmp = tmp.split("**");
					if(arr_tmp[0] && ""+arr_tmp[0] != ""){
						arr_tmp[0]=$.trim(""+arr_tmp[0]).toLowerCase();
						//alert(""+ar_valid_dxCode+" - "+arr_tmp[0]);
						if(ar_valid_dxCode.indexOf(""+arr_tmp[0])==-1){

							//if icd 10 --
							if(v_icd_10=="1"){
								var q1=""+arr_tmp[0].slice(0,-1)+"-";
								var q2=""+arr_tmp[0].slice(0,-2)+"--";
								var q3=""+arr_tmp[0].slice(0,-3)+"-x-";

								//
								if(ar_valid_dxCode.indexOf(""+q1)!=-1 || ar_valid_dxCode.indexOf(""+q2)!=-1 || ar_valid_dxCode.indexOf(""+q3)!=-1){ continue; }
							}
							//if icd 10 --

							ar_wrong_dx[ar_wrong_dx.length]=""+arr_tmp[0];

							//
							//$("#elem_dxCodeAssoc_"+indx).multiselect("widget").find(":checkbox").each(function(){  if(this.value==tmp){ this.checked=false; }}); //uncheck
							//$("#elem_dxCodeAssoc_"+indx).multiselect("update");
							fun_mselect("#elem_dxCodeAssoc_"+indx, "unselect", tmp );

						}

					}
				}

			}
			//alert("Enter 6");
			if(ar_wrong_dx.length > 0 && flgnoalert==0){
				//alert("Enter 7");
				var dx_alert = ar_wrong_dx.join();
				dx_alert=dx_alert.toUpperCase();
				top.fAlert("Dx code(s) "+dx_alert+" can not be applied for procedure "+proc_code+".");
			}
		}
		}
		//Check DX code More than 4: More than 4 are not allowed--
		if(v_icd_10!="1"){//works for icd 9 only
		var arcurdx = ""+fun_mselect("#elem_dxCodeAssoc_"+indx, 'val') ; //""+$("#elem_dxCodeAssoc_"+indx).multiselect("getChecked").map(function(){ return this.value; }).get();
		if(arcurdx && arcurdx!="null" && arcurdx !=""){
			var tmp_arcurdx=arcurdx.split(",");
			var lm =4;
			if(tmp_arcurdx.length > lm){

				if(flgnoalert==0){
					top.fAlert("You cannot select more than 4 Dx codes for a procedure.");
				}

				for(var x in tmp_arcurdx){
					var tmp = $.trim(tmp_arcurdx[x]);
					if(tmp=="")continue;

					var arr_tmp = tmp.split("**");
					if(arr_tmp[0] && ""+arr_tmp[0] != ""){

						//alert(""+ar_valid_dxCode+" - "+arr_tmp[0]);
						if(x >= lm){ //More than 4 are not allowed

							//
							//$("#elem_dxCodeAssoc_"+indx).multiselect("widget").find(":checkbox").each(function(){  if(this.value==tmp){ this.checked=false; }}); //uncheck
							//$("#elem_dxCodeAssoc_"+indx).multiselect("update");
							fun_mselect("#elem_dxCodeAssoc_"+indx, "unselect", tmp );

						}
					}
				}
			}
		}
		}

		//set Title dx
		sb_setDxCodeTitle($("#elem_dxCodeAssoc_"+indx)[0]);
	}

	function sb_setDxCodeTitle(obj){
		//set Title dx
		var strtitle="";
		var cur_dxCode = ""+fun_mselect(obj, 'val') ; // ""+$(obj).val(); // ""+$(obj).multiselect("getChecked").map(function(){ return this.value; }).get();
		//*
		$(obj).find('option').each(function() {

			if(cur_dxCode.indexOf(""+$(this).val())!=-1){
				var tmp = (typeof($(this).data("desc"))!="undefined") ? " - " + $(this).data("desc") : "";
				strtitle+=this.text+""+tmp+"\n";
			}

			});
		//*/

		//$(obj).prop("title",strtitle);
		//$(obj).multiselect("getButton").prop("title",strtitle);
		fun_mselect(obj, "settitle", strtitle );
	}

	function sb_getProcId4del(){
		var flg=0;
		var str="";
		$("#superbill :hidden[name*=elem_procedureId_]").filter(function(){return this.value!=='';}).each(function(){ if(str!=""){str+=",";} str+=""+this.value;});
		var s=$("#elem_proc_del_id").val();
		if(typeof(s)!="undefined" && s!=""){
		if(str!=""){str+=",";}
		str+=s;
		}
		$("#elem_proc_del_id").val(str);
	}

	var flgMultiVisitCodeShowOnce=[], flgcheckingMVC=0, flgMultiVisitCodeShowOnce_2=[];
	function checkCptCodesChart(obj,fOk){

		//Typeahead hack
		if (document.getElementById('tat_table'))return;

		var cptCd = $.trim(obj.value);
		var cptCdFull = $.trim(obj.value);
		if( (obj.value != "") && (cptCd.length >= 2 ) ){

			if(typeof fOk == "undefined"){
				var indx = cptCd.lastIndexOf("~!~");
				cptCd = (indx != -1) ? $.trim(cptCd.substring(0,indx)) : cptCd;
				obj.value = cptCd;
				checkDB4Code(obj,"Cpt");
				return;
			}

			/*
			var indx = cptCd.lastIndexOf("~!~");
			cptCd = (indx != -1) ? $.trim(cptCd.substring(0,indx)) : cptCd;
			var fOk = false;

			for(var x in arrCptCodeAndDesc){
				if((arrCptCodeAndDesc[x].toUpperCase() == cptCd.toUpperCase())){
					obj.value = cptCd;
					fOk = true;
					break;
				}else if( (arrCptCodeAndDesc[x].toUpperCase() == cptCdFull.toUpperCase()) ) {
					obj.value = cptCd = cptCdFull;
					fOk = true;
					break;
				}
			}


			if(fOk == false){
				if(!confirm("This Procedure name does not exists in database. \nDo you still want to use this??")){
					//alert("Enter valid CPT Code.");
					obj.value = obj.title = "";
				}
			}else{

				//Check DB
				//checkDB4Code(obj,"Cpt");
				if(typeof arrCptCodeDescActive[0][cptCd] == "string"){
					obj.value =  arrCptCodeDescActive[0][cptCd];
					//Check ChiefComplaint
					//alert(""+complaint1+"\n\n"+complaint2+"\n\n"+complaint3);
				}
			}

			//Desc in title
			obj.title = getCptDescJs( obj.value );
			*/

			// Check if Not test Super bill  --
			if((typeof sb_testName == "undefined" || sb_testName == "") && (typeof examName == "undefined" || examName != "Procedures")){

				var pTop=200;
				var pLeft=300;

				//Get Visit Type
				if(typeof gebi("elem_masterPtVisit") != "undefined"){
					var typeVisit = gebi("elem_masterPtVisit").value;
				}else{
					var typeVisit ="";
				}

				//getcpt
				var cpt4cd = obj.getAttribute("cpt4code");
				var prcBillCd = $("#elem_practiceBillCode").val();

				if( (prcBillCd=="992" && ((cpt4cd == "99205") || (cpt4cd == "99204") || (cpt4cd == "99203") ||  (cpt4cd == "99202") || (cpt4cd == "99201") ||
					(cpt4cd == "99215") || (cpt4cd == "99214") || (cpt4cd == "99213") || (cpt4cd == "99212") || (cpt4cd == "99211"))) ||
					(cpt4cd == "92004") || (cpt4cd == "92002") || (cpt4cd == "92014") || (cpt4cd == "92012")
													){
					//alert("You have entered Visit code - "+cpt4cd);
					if(setVisitProcedure_nochk==1){setVisitProcedure_nochk=0;}else{  obj.value = ""; decideLevelofService2_obj_id=obj.id; checkAssocPqri4All_checkvisitcode(cpt4cd); getMxSB(); /*Set Today Charges*/ setSBTodayCharges();  return;  }
				}

				if(($.trim(typeVisit).toUpperCase() == "FOLLOW-UP") && ( (cpt4cd == "99205") || (cpt4cd == "99204") || (cpt4cd == "99203") ||
													(cpt4cd == "99215") || (cpt4cd == "99214") || (cpt4cd == "99213") ||
													(cpt4cd == "99245") || (cpt4cd == "99244") || (cpt4cd == "99243") ||
													(cpt4cd == "92005") || (cpt4cd == "92004") || (cpt4cd == "92003") ||
													(cpt4cd == "92015") || (cpt4cd == "92014") || (cpt4cd == "92013")
													)){
					//Warn Msg
					var msgFollowUp = "Visit Type is Follow-up.<br>Are you sure you want to code to next level?";

					if((obj.value != "") && ( (cpt4cd == "99204") || (cpt4cd == "99205") ||
								   (cpt4cd == "99214") || (cpt4cd == "99215") ||
								   (cpt4cd == "99244") || (cpt4cd == "99245")  ) ){
						var chkStr = checkChiefComplaintDetailed();
						if(chkStr == "false"){
							var msgFollowUp ="1. You have not detailed minimum requirements of 4 points of the chief complaint<br>"+
										"2. Visit Type is Follow-up and you are coding to next level.<br>"+
										"Are you sure you would like to use the selected visit code? ";
						}
					}

					//Prompt
					zChiefCompElemName=obj.name;
					var title = "Visit code comfirmation";
					var msg = ""+msgFollowUp+"<BR>";
					var btn1 = "Yes";
					var btn2 = "No";
					var func = "decideChiefComp";
					var oDiv;
					//if(gebi("idVCC")){
					//	oDiv =gebi("idVCC");
					//}else{
						displayConfirmYesNo_v2(title,msg,btn1,btn2,func,0);
					//	oDiv.id="idVCC";
					//}
					//var thisScrollTop = document.body.scrollTop;
					//pTop += parseInt(thisScrollTop);
					//oDiv.style.top=pTop+"px";
					//oDiv.style.left=pLeft+"px";

				}else if(($.trim(typeVisit).toUpperCase() == "CEE") && ( (cpt4cd == "99201") || (cpt4cd == "99202") || (cpt4cd == "99203") ||
													(cpt4cd == "99211") || (cpt4cd == "99212") || (cpt4cd == "99213") ||
													(cpt4cd == "99241") || (cpt4cd == "99242") || (cpt4cd == "99243") ||
													(cpt4cd == "92001") || (cpt4cd == "92002") || (cpt4cd == "92003") ||
													(cpt4cd == "92011") || (cpt4cd == "92012") || (cpt4cd == "92013")
													) ){
					/*
					//Check Db mandatory fields
					//checkVisitCodeExams(obj);
					if(obj.value.indexOf("992") != -1){ //E/M
						var oStr = gebi("elem_strExmNotDoneEM");
					}else if(obj.value.indexOf("920") != -1){ //Eye
						var oStr = gebi("elem_strExmNotDoneEye");
					}
					if(oStr && (typeof oStr.value != "undefined")){
						var arr = oStr.value.split(",");

						var msgFollowUp = "Visit Code is CEE and Following exams are not done:--<br>";
						msgFollowUp += oStr.value.replace(",","<br>      -");
						var title = "Visit code warning";
						var msg = ""+msgFollowUp+"<BR>";
						var btn1 = "OK";
						var btn2 = "";
						var func = "hideConfirmYesNo";
						var oDiv = displayConfirmYesNo_v2(title,msg,btn1,btn2,func,0);
						var thisScrollTop = document.body.scrollTop;
						pTop += parseInt(thisScrollTop);
						oDiv.style.top=pTop+"px";
						oDiv.style.left=pLeft+"px";
					}
					*/
				}else{

					if((obj.value != "") && ( (cpt4cd == "99204") || (cpt4cd == "99205") ||
								   (cpt4cd == "99214") || (cpt4cd == "99215") ||
								   (cpt4cd == "99244") || (cpt4cd == "99245")  ) ){
						var chkStr = checkChiefComplaintDetailed();
						if(chkStr == "false"){
							zChiefCompElemName=obj.name;
							var msgPrompt ="You have not detailed minimum requirements of 4 points of the chief complaint<br>"+
							"Are you sure you would like to use the selected visit code?";
							//Prompt
							var title = "Chief Complaint Requirement";
							var msg = ""+msgPrompt+"<BR>";
							var btn1 = "Yes";
							var btn2 = "No";
							var func = "decideChiefComp";
							var oDiv;
							//if(gebi("idCCR")){
							//	oDiv = gebi("idCCR");
							//}else{
								//oDiv =
								displayConfirmYesNo_v2(title,msg,btn1,btn2,func,0);
							//	oDiv.id="idCCR";
							//}
							/*
							var thisScrollTop = document.body.scrollTop;
							pTop += parseInt(thisScrollTop);
							oDiv.style.top=(pTop+10)+"px";
							oDiv.style.left=(pLeft+10)+"px";
							*/
						}
					}
				}

				/* Stopped on 05-10-2013 : Please see below, Lucent�s Units are not reading from the CPT table.  IN the following case it should have been 3 units.
				//Check for cpt J2778, Set units to 5 --
				if(""+cpt4cd.toUpperCase() == "J2778"){
					var idUnit = obj.name.replace(/elem_cptCode_/g, "elem_procUnits_");
					var oUnit=gebi(idUnit);
					if(oUnit){
						oUnit.value = 5;
					}
				}
				//Check for cpt J2778, Set units to 5 --
				*/


				//check if multiple visit code are used for same patient and for same DOS
				var tmp = chk_multi_visit_code_exist(obj);


			}
			// Check if Not test Super bill  --

			//check if multiple Ophtha code are used for same superbill
			remif_multi_Ophtha_code_exist(obj);

			//Check and warn if wrong DX code is attached:-
				sb_checknwarn4WrongDxcode(obj);
			//--


		}else{
			obj.value = obj.title = obj.cpt4code= "";
			obj.setAttribute("valid_dxcodes","");
			// //check if all cpts are empty and procid are not
			//check if all cpts are empty and procid are not
			if($("#superbill .cptcode").filter(function(){return this.value!=='';}).length==0 && $("#superbill :hidden[name*=elem_procedureId_]").filter(function(){return this.value!=='';}).length>0){
				//confirm with user before delete --
				flg = confirm("You have removed all CPTs of this super bill ! Are you sure?");
				if(flg){
					sb_getProcId4del();
				}
			}
		}

		//
		var objsbnr = getMxSB(1);

		//Set Today Charges
		setSBTodayCharges();
	}

	function chk_multi_visit_code_exist(obj){
		if(typeof(sb_multi_vst_cd_noalert)!="undefined" && sb_multi_vst_cd_noalert==1){ return true; }
		if(obj.value != ""){
			var visit_code_arr=["92001","92011","92002","92012","92004","92014","99201","99202","99203","99204",
							"99205","99241","99242","99243","99244","99245","99211","99212","99213","99214","99215"];
			var a = visit_code_arr.indexOf(obj.value);
			if(a!=-1){

				var cvc = 0;
				var cvc_flg = 0, cvc_flg_2 = 0;
				var strCptCodes="";
				var tmp_arCptCodes=[];
				$(".cptcode").each(function(){
						var tmp = "";
						//check cpt4code ::: value of obj shouldbe empty and cpt4 code should be in array
						var tmp = $.trim($(this).attr("cpt4code"));
						if(typeof(tmp)=="undefined" || tmp==""){ tmp = $.trim(this.value); }

						if(tmp!=""){
							var a = visit_code_arr.indexOf(tmp);
							if(a!=-1){
								cvc=cvc+1;
								if(flgMultiVisitCodeShowOnce.indexOf(tmp+cvc)==-1){
									flgMultiVisitCodeShowOnce[flgMultiVisitCodeShowOnce.length]=tmp+cvc;
									cvc_flg = 1;
								}

								if(flgMultiVisitCodeShowOnce_2.indexOf(tmp+cvc)==-1){
									cvc_flg_2 = 1;
									if(strCptCodes!=""){strCptCodes+=",";}
									strCptCodes+="'"+tmp+"'";
									tmp_arCptCodes[tmp_arCptCodes.length]=tmp;
									flgMultiVisitCodeShowOnce_2[flgMultiVisitCodeShowOnce_2.length]=tmp+cvc;
								}
							}
						}
					});
				if(cvc >= 2 && cvc_flg == 1){
					top.fAlert("Visit Code already exists for DOS "+$("#elem_dos").val());
				}else if(strCptCodes!="" && cvc_flg_2 == 1){
					if(flgcheckingMVC==0){

						flgcheckingMVC=1;
						//check db
						var url = zPath+"/chart_notes/requestHandler.php";
						var params = "elem_formAction=checkMultiVisitCode";
						params += "&elem_strCptCodes="+strCptCodes;
						params += "&elem_superbillDOS="+$("#elem_dos").val();
						params += "&elem_encounterId="+$("#elem_masterEncounterId").val();
						//alert(url+" - "+params);
						$.get(url,params,function(data){
							//alert(data);
							if(data=="1"){
								top.fAlert("Visit Code already exists for DOS "+$("#elem_dos").val());
							}

							flgcheckingMVC=0;
						});

					}
				}
			}
		}
	}

	function has_ophtha_code(o){
		if($.trim(o.value) == ""){return false;}
		var code_arr=["92201","92202","92225","92226"];
		var obj4cd = $(o).attr("cpt4code");
		if(typeof(obj4cd)=="undefined" || obj4cd==""){ obj4cd = $.trim(o.value); }
		var a = code_arr.indexOf(obj4cd);
		return (a!=-1) ? true : false;
	}

	function chk_multi_Ophtha_code_exist(){
		var cvc = 0, ret=false ;
		$(".cptcode").each(function(){
				if(has_ophtha_code(this)){cvc=cvc+1;}
			});
		if(cvc >= 2){
			ret=true;
		}
		return ret;
	}

	function remif_multi_Ophtha_code_exist(obj){
		if(has_ophtha_code(obj)){
			var res = chk_multi_Ophtha_code_exist();
			if(res == true){
				top.fAlert("Ophthalmoscopy Code already exists in this superbill!");
				obj.value = obj.title = obj.cpt4code= "";
			}
		}
	}


	function checkProcUnitsChart(obj){
		if(obj.name.indexOf("elem_procUnits_") != -1){
			//Check Units for zero or empty
			var n = $.trim(obj.value);
			obj.value = n;
			if((n == "") || !isFinite(n) || (n<=0)){
				top.fAlert("Units can not be less than zero.");
				obj.value = "1";
			}
			// Check for cpt
			var idCpt = obj.name.replace(/elem_procUnits_/g, "elem_cptCode_");
			var oCpt = gebi(idCpt);
			if(oCpt && ($.trim(oCpt.value) != "")){
				//
				var objsbnr = getMxSB();

				//Set Today Charges
				setSBTodayCharges();
			}
		}
	}

	function show_asmt_site(obj, dx, fop){

		//enable site
		var ch = $(obj)[0].id;

		if(typeof(ch)=="undefined" || ch.indexOf("elem_assessment_dxcode")==-1){ return; }

		var op = 0;
		if(typeof(fop)!='undefined' && fop!=''){
			op = fop;
		}else{
			op = (typeof(dx)!='undefined' && dx!='' && dx.indexOf("-")==-1) ? 1 : 0 ;
		}

		var indx = ch.replace(/elem_assessment_dxcode/,"");

		if(op==1){ //show
			$("#el_amst_site"+indx).parent().removeClass("hidden");
			$("#elem_assessment"+indx).parent().removeClass("col-sm-12").addClass("col-sm-11");

			var s="";
			if($("#elem_apOu"+indx).prop("checked")){ s="OU"; }
			else if($("#elem_apOd"+indx).prop("checked")){ s="OD"; }
			else if($("#elem_apOs"+indx).prop("checked")){ s="OS"; }
			$("#el_amst_site"+indx).val(""+s);

		}else{ //hide
			$("#el_amst_site"+indx).parent().addClass("hidden");
			$("#elem_assessment"+indx).parent().addClass("col-sm-12").removeClass("col-sm-11");
			$("#el_amst_site"+indx).val("");
		}
	}

	function checkDXCodesChart(obj){
		//Typeahead hack
		if (document.getElementById('tat_table'))return;

		var dxCd = $.trim(obj.value);
		var icd10 = $("#hid_icd10").val();
		if(icd10==1){  if(dxCd == ""){obj.title = "";}    /*return;*/}


		var fOk = false;
		if((dxCd != "") && (dxCd.length >= 3)){

			//remove yellow
			if(dxCd.indexOf("-")==-1){
				$(obj).removeClass('mandatory');
			}else{

			}
			/*
			for(var x in arrDxCodeAndDesc){
				if(arrDxCodeAndDesc[x].toUpperCase() == dxCd.toUpperCase()){
					fOk = true;
					break;
				}
			}*/
			//if(fOk == false){

				//if(confirm("Entered Dx Code doen not exists in database.\nDo you still want to use this?")){
				//	obj.value = obj.title = "";
				//}

			//}else{
				//if(!isDxCorrectCode(dxCd)){
					//Check DB

					checkDB4Code(obj,"Dx");

				//}
			//}
		}else{
			obj.value = obj.title = "";
			$(obj).removeData("dxid");
			renew_title(obj,'');
			if(obj.id.indexOf("elem_dxCode") != -1){
				//Set DropDow Dx codes
				sb_crt_dx_dropdown();
			}
		}
	}

	function checkModCodesChart(obj){
		//Typeahead hack
		if (document.getElementById('tat_table'))return;

		var mdCd = $.trim(obj.value);
		var fOk = false;
		if((mdCd != "") && ( mdCd.length >= 2 ) ){
			/*
			for( var x in arrMdCodesTypeAhead ){
				if(arrMdCodesTypeAhead[x].toUpperCase() == mdCd.toUpperCase()){
					fOk = true;
					break;
				}
			}
			if(fOk == false){
				alert("Enter valid Modifier Code.");
				obj.value = "";
			}else{
			*/
				//Check DB
				checkDB4Code(obj,"Md");
			//}
		}else{
			obj.value = "";
			setSBTodayCharges();
		}
	}

	function checkSBDxCodeFilled(obj, num){
		var oDx = gebi("elem_dxCode_"+num);
		if(obj.checked == true){
			if(oDx){
				obj.checked = ($.trim(oDx.value) != "") ? true : false;
			}else{
				obj.checked = false;
			}
		}
	}

	function insertExamsDoneSB(arrED){
		var mxSbId = gebi("elem_mxSBId").value;
		var eTblSb = $("#tblSuperbill").get(0);

		//var oRows = eTblSb.rows;
		var lenArrED = arrED.length;
		//var lenTbl = oRows.length;
		var arrDxCodesNotUsed = new Array();
		//var arrCptSecChance = new Array();

		//Insert Cpt
 		for(var j=0;j<lenArrED;j++){ ////FIRST ROW LEFT INTENTIONALLY

			//alert(arrED[j]["cpt"]+"\n"+arrED[j]["modifier1"]+"\n"+arrED[j]["units"]+"\n"+arrED[j]["norepeat"]);
			//continue;
			//var oVal = {"cptCode":arrED[j]["cpt"],"procId":"","units":arrED[j]["units"],"arrDx":new Array(),"arrMd":new Array(arrED[j]["modifier1"],arrED[j]["modifier2"]),"cptDesc": arrED[j]["cptDesc"]};
			//var ctr = j+1;
			if(( typeof arrED[j] != "undefined" )){

				var flag = true;
				var oRows = eTblSb.rows;
				var lenTbl = oRows.length;
				var cTryAgain=0;
				// Check in table first
				for(var b=0;b<lenTbl;b++){
					if(oRows[b]){
						var tCptId = oRows[b].id.replace(/elem_trSB/, "elem_cptCode_");
						var oCpt = gebi(tCptId);
						var cCpt = (oCpt) ? $.trim(oCpt.value) : null;
						if(oCpt && (cCpt != "") && (cCpt == arrED[j]["cpt"])){
							var tMdId = tCptId.replace(/elem_cptCode_/,"elem_modCode_");

							//check all Mods
							var curMd = "", insrtMd = "";
							for(var inMd=1;inMd<=4;inMd++){
								var oMd = gebi(tMdId+"_"+inMd);
								curMd += ((oMd) && (typeof oMd.value != "")) ? oMd.value : "" ;
								insrtMd += (typeof arrED[j]["modifier"+inMd] != "undefined") ? arrED[j]["modifier"+inMd] : "";
							}

							//check units
							var tUntId = tCptId.replace(/elem_cptCode_/,"elem_procUnits_");
							var curUnt = "", insrtUnt = "";
							var oUnt = gebi(tUntId);
							curUnt = ((oUnt) && (typeof oUnt.value != "")) ? oUnt.value : "" ;
							insrtUnt = (typeof arrED[j]["units"] != "undefined") ? arrED[j]["units"] : "";

							if(insrtMd == curMd && curUnt == insrtUnt ){

								if(cCpt == "92133" && arrED[j]["norepeat"] == 0){
									cTryAgain = cTryAgain+1;
								}else if(cCpt.indexOf("J")!=-1 && curMd=="JW"){//For Botox codes with Jw Modifier
									//do nothing
								}else{

									flag = false;
									break;

								}

								/*
								if(typeof arrCptSecChance[cCpt] == "undefined"){
									arrCptSecChance[cCpt] = 1;
									flag = false;
									break;
								}else{

									if(arrCptSecChance[cCpt] > cTryAgain){
										cTryAgain=cTryAgain+1;
									}else{
										arrCptSecChance[cCpt] = arrCptSecChance[cCpt]+1;
										flag = false;
										break;
									}
								}
								*/

							}
						}//else{
							//alert("check: "+cCpt+" == "+arrED[j]);
						//}
					}
				}

				// Check max 4 can enter for 92133 --
				if(cTryAgain >= 2){
					flag = false;
				}
				// Check max 4 can enter for 92133 --

				// Check in table first

				if( flag == true ){
				//var oRows = eTblSb.rows;
					//Add in table
					var v_prc_o_visit=$("#elem_proc_only_visit").val();
					v_prc_o_visit=(typeof(v_prc_o_visit)!="undefined" && v_prc_o_visit==1) ? 0 : 1;
					for( var k=v_prc_o_visit;k<lenTbl;k++ ){
						//alert("Inside: "+arrED[j]["cpt"]);

						//if( !isCptCodeEnterSB(arrED[j]["cpt"]) ){
							var rowId = oRows[k].id.replace(/elem_trSB/, "");
							var tCptId = "elem_cptCode_"+rowId;
							var oCpt = gebi(tCptId);
							if(oCpt && (($.trim(oCpt.value) == "")) ){
								oCpt.value = arrED[j]["cpt"];
								oCpt.title = arrED[j]["cptDesc"];
								if(typeof(arrED[j]["valid_dxcodes"])!="undefined" && arrED[j]["valid_dxcodes"]!="") {
									oCpt.setAttribute("valid_dxcodes", ""+arrED[j]["valid_dxcodes"]);
								}
								//curCpt[curCpt.length] = oCpt.value;

								var oMod = gebi("elem_modCode_"+(rowId)+"_1");
								if(oMod && (typeof arrED[j]["modifier1"] != "undefined") ){
									oMod.value = arrED[j]["modifier1"];
									//curMd[curMd.length] = oMod.value;
								}

								var oMod = gebi("elem_modCode_"+(rowId)+"_2");
								if(oMod && (typeof arrED[j]["modifier2"] != "undefined") ){
									oMod.value = arrED[j]["modifier2"];
								}

								var oMod = gebi("elem_modCode_"+(rowId)+"_3");
								if(oMod && (typeof arrED[j]["modifier3"] != "undefined") ){
									oMod.value = arrED[j]["modifier3"];
								}

								var oMod = gebi("elem_modCode_"+(rowId)+"_4");
								if(oMod && (typeof arrED[j]["modifier4"] != "undefined") ){
									oMod.value = arrED[j]["modifier4"];
								}

								var ounits = gebi("elem_procUnits_"+(rowId));
								if(ounits && (typeof arrED[j]["units"] != "undefined")){
									ounits.value = arrED[j]["units"];
								}

								//Dx Codes
								var dxcntr=1;
								while(true){
									//alert("CHK: "+dxcntr+" -- "+typeof(arrED[j]["dx"+dxcntr]));
									if(typeof(arrED[j]["dx"+dxcntr]) != "undefined"){

										var indEmptyDxCd = null;
										var flgSet = false;
										//Check if Dx Code is added and where
										for( var d=1;d<=12;d++ ){
											var oDx = gebi("elem_dxCode_"+d);
											if(oDx && ($.trim(oDx.value) != "")){
												var tdx = $.trim(oDx.value);
												var tdxId = $(oDx).data("dxid");
												if(typeof tdxId == "undefined"){ tdxId=""; }
												//match dx code with -/--
												var tdx1=tdx, tdx2=tdx, tdx3=tdx;
												if(arrED[j]["dx"+dxcntr].indexOf("-")!=-1){
													var tdx1=""+tdx.slice(0,-1)+"-";
													var tdx2=""+tdx.slice(0,-2)+"--";
													var tdx3=""+tdx.slice(0,-3)+"-x-";
												}
												//-----------

												if(tdx == arrED[j]["dx"+dxcntr] || tdx1==arrED[j]["dx"+dxcntr] || tdx2==arrED[j]["dx"+dxcntr] || tdx3==arrED[j]["dx"+dxcntr]){

												var flgDxIdNM=1;
												if(tdxId!="" && typeof arrED[j]["dx"+dxcntr+"Id"] != "undefined" && $.trim(arrED[j]["dx"+dxcntr+"Id"])!=$.trim(tdxId)){
													flgDxIdNM=0;
												}

												//Dx Code already exists just Select this Check box
												//var oDxElem = gebi("elem_dxCodeAssoc_"+(rowId)+"_"+d);
												var oDxElem = gebi("elem_dxCodeAssoc_"+(rowId));
												if(oDxElem && flgDxIdNM==1){
													//oDxElem.checked = true;
													sb_crt_dx_dropdown("#elem_dxCodeAssoc_"+(rowId), '', new Array(arrED[j]["dx"+dxcntr]));

													flgSet = true;
													break;
												}
												}
											}else if(oDx && ($.trim(oDx.value) == "") && (indEmptyDxCd == null)){
												//Get Empty index of dx text box
												indEmptyDxCd = d;
											}
										}

										//if dx Code not set
										if((flgSet == false)){
											// Empty dx Code text box exists
											// Add Dx Code in Empty text box and select it
											if(indEmptyDxCd != null){
												var oDx = gebi("elem_dxCode_"+indEmptyDxCd);
												var oDxElem = gebi("elem_dxCodeAssoc_"+(rowId));
												if(oDx){
													//Set Dx code in empty text box
													oDx.value = ""+arrED[j]["dx"+dxcntr];
													var ttitle = (typeof arrED[j]["dx"+dxcntr+"Desc"] != "undefined") ? ""+arrED[j]["dx"+dxcntr+"Desc"] : "";
													renew_title(oDx,ttitle);
													var tdxid = (typeof arrED[j]["dx"+dxcntr+"Id"] != "undefined") ? arrED[j]["dx"+dxcntr+"Id"] : "";
													$(oDx).data("dxid", tdxid);
													$(oDx).triggerHandler("blur");

													//select the check box
													if(oDxElem){
														//oDxElem.checked = true;
														sb_crt_dx_dropdown("#elem_dxCodeAssoc_"+(rowId), '', new Array(arrED[j]["dx"+dxcntr]));
														flgSet = true;
													}
												}
											}else{
												//Add in Arr to Prompt User
												var tmp = (typeof arrED[j]["dx"+dxcntr+"Desc"] != "undefined") ? ""+arrED[j]["dx"+dxcntr+"Desc"] : "";
												arrDxCodesNotUsed[arrDxCodesNotUsed.length] = new Array(arrED[j]["dx"+dxcntr],tmp);
											}
										}
									}else{
										break;
									}

									dxcntr=dxcntr+1;
									if(dxcntr>50){ break; }

								}

								//AP Dx Codes associate
								for(var ia=1;ia<=4;ia++){
									if(typeof arrED[j]["adx"+ia] != "undefined" && arrED[j]["adx"+ia] != ""){
										//Check if Dx Code is added and where
										for( var d=1;d<=12;d++ ){
											var oDx = gebi("elem_dxCode_"+d);
											if(oDx && ($.trim(oDx.value) != "") ){
												var flgMtch=0;
												//check dx codes in icd 10 format --
												if($("#hid_icd10").val()=="1"){ //icd10
													var p0=$.trim(""+arrED[j]["adx"+ia].toLowerCase());
													var q=q0=$.trim(""+oDx.value.toLowerCase());
													var q1=""+q0.slice(0,-1)+"-";
													var q2=""+q0.slice(0,-2)+"--";
													var q3=""+q0.slice(0,-3)+"-x-";
													if(q0==p0 || q1==p0 || q2==p0 || q3==p0){  flgMtch=1; }
												}else{
													if(($.trim(oDx.value) == arrED[j]["adx"+ia])){ flgMtch=1; }
												}
												//--

												if(flgMtch==1){
													//Dx Code already exists just Select this Check box
													//var oDxElem = gebi("elem_dxCodeAssoc_"+(rowId)+"_"+d);
													var oDxElem = gebi("elem_dxCodeAssoc_"+(rowId));
													if(oDxElem){
														//oDxElem.checked = true;
														sb_crt_dx_dropdown("#elem_dxCodeAssoc_"+(rowId), '', new Array(arrED[j]["adx"+ia]));
														break;
													}
												}
											}
										}
									}
								}

								/*
								//Second Chance
								if(typeof arrCptSecChance[arrED[j]["cpt"]] == "undefined"){
										arrCptSecChance[arrED[j]["cpt"]]=1;
								}else{
										arrCptSecChance[arrED[j]["cpt"]] = arrCptSecChance[arrED[j]["cpt"]] + 1;
								}
								//Second Chance
								*/

								if(sup_enc==1){
									opAddCptRow("LASTINDX",'','','',sup_enc);
								}else{
									opAddCptRow("LASTINDX");
								}
								break;
							}
						/*}else{
							editSBRowValues(oVal);
							return;
						}*/
					}
				}

			}
			/*else{
				var oVal = {"cptCode":arrChoosenPqri[x],"units":1};
				opAddCptRow(mxSbId,oVal);
			}*/
		} // End Insert Cpt

		//set Today Charges
		setSBTodayCharges();

		//Alert Dx Code Not Used
		if(arrDxCodesNotUsed.length > 0){
			/*
			var msg = "Following Dx codes are not used as all four dx codes are filled<br>";
			for(var z in arrDxCodesNotUsed){
				var cdDx = (typeof arrDxCodesNotUsed[z][0] != "undefined") ? arrDxCodesNotUsed[z][0] : null;
				var dscDx = (typeof arrDxCodesNotUsed[z][1] != "undefined") ? arrDxCodesNotUsed[z][1] : "";
				if(cdDx){
					msg += "<br>      -"+dscDx+" "+cdDx;
				}
			}

			//Prompt
			var title = "Dx Codes not used";
			var msg = ""+msg+"<BR>";
			var btn1 = "OK";
			var btn2 = "0";
			var func = "hideConfirmYesNo";
			var oDiv = displayConfirmYesNo_v2(title,msg,btn1,btn2,func,0);
			var thisScrollTop = document.body.scrollTop;
			var pTop=200;
			var pLeft=300;
			pTop += parseInt(thisScrollTop);
			oDiv.style.top=(pTop+10)+"px";
			oDiv.style.left=(pLeft+10)+"px";
			*/
		}

		//Check to Insert Blank
		var objsbnr = getMxSB(1);
	}

	function getSelPQRICodes(actn){
		var oDiv = gebi("divChoosePqriCodes");
		if(actn==0){oDiv.style.display="none";return;}
		var mxSbId = gebi("elem_mxSBId").value;
		var selectedCode = gebi("elem_procOrder").value;

		var oElems = oDiv.getElementsByTagName("INPUT");
		var len = oElems.length;
		var arrChoosenPqri = new Array();
		for(var i=0;i<len;i++){
			if((oElems[i].type == "checkbox") && (oElems[i].checked == true)){
				var tmpCode = oElems[i].value;
				var tmpDx = oElems[i].getAttribute("dx");
				if(selectedCode.indexOf(tmpCode) == -1){ //Check if already exists
					arrChoosenPqri[arrChoosenPqri.length] = [tmpCode,tmpDx];
				}
			}
		}
		oDiv.style.display="none";
		if(arrChoosenPqri.length > 0){
			//alert("Pqri\n:- "+arrChoosenPqri);
			for(var x in arrChoosenPqri){
				var cpt = arrChoosenPqri[x][0];
				var dx = arrChoosenPqri[x][1];

				var tmpDesc = getCptDescJs( cpt );
				var oVal = {"cptCode":cpt,"procId":"","units":1,"arrDx":[dx],"arrMd":new Array(),"cptDesc": tmpDesc };
				if(sup_enc==1){
					opAddCptRow(1,oVal,1,'',sup_enc); //Insert Always After Visit code as money is involved
				}else{
					opAddCptRow(1,oVal,1); //Insert Always After Visit code as money is involved
				}
			}

			/*
			var arrEmpty = new Array();
			//check Empty rows of SB
			var eTblSb = gebi("tblSuperbill");
			var oRows = eTblSb.rows;
			var lenRows = oRows.length;
			for(var i=lenRows-1;i>0;i--){ //LEFT FIRST ROW
				var tCptId = oRows[i].id.replace(/elem_trSB/, "elem_cptCode_");
				var oCpt = gebi(tCptId);
				if(oCpt && ($.trim(oCpt.value) == "")){
					arrEmpty[arrEmpty.length] = tCptId;
				}
			}

			for(var x in arrChoosenPqri){
				if(arrEmpty.length > 0){
					var tCptId = arrEmpty.pop();
					var oCptElem = gebi(tCptId);
					if(oCptElem && ($.trim(oCptElem.value) == "")){
						oCptElem.value = arrChoosenPqri[x];
						oCptElem.title = getCptDescJs( arrChoosenPqri[x] );
						//oCptElem.onblur();
					}
				}else{
					//var oVal = {"cptCode":arrChoosenPqri[x],"units":1};
					var tmpDesc = getCptDescJs( arrChoosenPqri[x] );
					var oVal = {"cptCode":arrChoosenPqri[x],"procId":"","units":1,"arrDx":new Array(),"arrMd":new Array(),"cptDesc": tmpDesc };
					opAddCptRow(mxSbId,oVal);
				}
			}
			*/

		}

		var objsbnr = getMxSB(1);
	}

	///Visit Code
	// 2/1/2011 : By default all Dx codes are associated with the visit CPT code.  Therefore automatically check the Visit CPT code with al the Dx codes.
	// 2/3/2011 : If refraction was done then still all the Dx code should be associated with the visit code except for the refractive code (Myopia, Presbyopia etc.)
	var setVisitProcedure_nochk=0;
	function setVisitProcedure(ccode)
	{
		var mxSbId = gebi("elem_mxSBId").value;
		if(!isVisitCodeUsed(ccode)){
			return;
		}

		var arrED = new Array();
		var arrtemp = new Array();
		arrtemp["cpt"] = ccode;
		arrED[arrED.length] = arrtemp;
		//insertExamsDoneSB(arrED);

		var cFlag = false;
		var eTblSb = gebi("tblSuperbill");
		var oRows = eTblSb.rows;
		var lenRows = oRows.length;
		for(var i=0;i<lenRows;i++){
			if(typeof oRows[i].id == "undefined" || oRows[i].id == ""){continue;}

			var tCptId = oRows[i].id.replace(/elem_trSB/, "elem_cptCode_");
			var oCpt = gebi(tCptId);
			var sCpt = $.trim(oCpt.value);
			//Assumption: only one visit code will be used in a super bill
			if( (sCpt == "92001") || (sCpt == "92011") || (sCpt == "92002") || (sCpt == "92012") || (sCpt == "92004") || (sCpt == "92014") ||
				(sCpt == "99201") || (sCpt == "99202") || (sCpt == "99203") || (sCpt == "99204") || (sCpt == "99205") ||
				(sCpt == "99241") || (sCpt == "99242") || (sCpt == "99243") || (sCpt == "99244") || (sCpt == "99245") ||
				(sCpt == "99211") || (sCpt == "99212") || (sCpt == "99213") || (sCpt == "99214") || (sCpt == "99215")
			){
				sCpt = "";
			}

			if(oCpt && (sCpt == "")){
				oCpt.value = ccode; // Insert;
				oCpt.title = getCptDescJs( ccode );
				cFlag = true;

				//set flag for no chk
				setVisitProcedure_nochk=1;

				if(typeof oCpt.onblur == "function"){
					oCpt.onblur();
				}

				// Get Dx Codes if cpt is sb_cpt_code_poe
				//if(ccode == sb_cpt_code_poe){

					for(var j=1;j<=12;j++){
						var oEDx = gebi("elem_dxCode_"+j);
						if(oEDx.value != ""){

							//if refraction + dxcode
							if(sb_objRefraction.isRef==1 && sb_objRefraction.dxCode == oEDx.value){
								continue;
							}

							//var oT = gebi("elem_dxCodeAssoc_"+(i+1)+"_"+j);
							var oT = gebi("elem_dxCodeAssoc_"+(i+1));
							if(oT){
								//oT.checked = true;
								sb_crt_dx_dropdown("#elem_dxCodeAssoc_"+(i+1), '', new Array(oEDx.value));
							}
						}
					}

				//}
				//

				break;
			}
		}

		if(cFlag == false){

			var arrDx = new Array();
			// Get Dx Codes if cpt is sb_cpt_code_poe
			// if(ccode == sb_cpt_code_poe){

				for(var i=0;i<12;i++){
					var oEDx = gebi("elem_dxCode_"+(i+1));
					if(oEDx.value != ""){

						//if refraction + dxcode
						if(sb_objRefraction.isRef==1 && sb_objRefraction.dxCode == oEDx.value){
							continue;
						}

						arrDx[arrDx.length] = ""+oEDx.value;
					}
				}
			//}
			//

			var ccodeDesc = getCptDescJs( ccode );
			var oVal = {"cptCode":ccode,"procId":"","units":1,"arrDx":arrDx,"arrMd":new Array(), "cptDesc":ccodeDesc};
			opAddCptRow(mxSbId,oVal);
		}

		/*
		var val2var = procedureName.replace(/\s/g,"_");
		var objProcedure = gebi("elem_Desc_".concat(val2var));
		if(objProcedure)
		{
			objProcedure.click();
		}*/
	}

	var decodePatientCategory = 1;
	function getPatientCategoryCode(str, pracbillcode)
	{
		category = false;
		if((decodePatientCategory == 1) && (str != null))
		{
			switch(str)
			{
				case "New":
				case "0":
				category = "0";
				break;
				case "Establish":
				case "1":
					category = "1";
				break;
				case "Consult":
				case "2":
				case "4":
					//category = "2";
				        category = "4"; //5528 - Dr Gillies:13-03-2015

					/*
					//tkt: 5528: 3/9/15 Anish- The referred physician changing the category to 2 only takes place in E/M codes, for eye codes the category should stay as 1. For these eye codes, please make sure that the category is either 0 for new patients and 1 for established patient visits.
					*/
					if(pracbillcode==920){
						var newpt=$("#elem_ptcategory_consult").val(); //get new patient info: pending
						category = 1; //establish
						if(newpt==0||newpt=="New"){////new
							//var np = confirm("is this a New Patient?");
							//if(np!=false){
								category = "0";
							//}
						}
					}

				break;
			}
		}
		return category;
	}

	/*
	function decideNewPatient_consult(val){
		var ctop=top;
		if(top.fmain){
			ctop = top.fmain;
		}
		var objCategory = gebi("elem_ptcategory_consult");
		ctop.hideConfirmYesNo();
		if((val == "1") || (val == "Yes"))
		{
			objCategory.value = "New";
		}
		else
		{
			objCategory.value = "Establish";
		}
		confirmVisitCode_v3(); //run
	}
	*/

	function decideNewPatient(obj)
	{
		var val = obj.value;

		if($("input[type=checkbox][id*=el_sb_pt_type_]").length>0){
			//single check
			$("input[type=checkbox][id*=el_sb_pt_type_]").each(function(inx){
					if(obj.checked && this.value!=val && this.checked){this.checked=false;}
				});
			var val = $(":checked[id*=el_sb_pt_type_]").val();
			if(typeof(val)=="undefined" || val==""){ val="No_confirmed"; }
		}

		var ctop=top;
		if(top.fmain){
			ctop = top.fmain;
		}

		var objCategory = gebi("elem_category");
		//ctop.hideConfirmYesNo();
		if((val == "1") || (val == "Yes") || (val == "Yes_confirmed"))
		{
			objCategory.value = "New";
			decodePatientCategory=1;
		}
		else
		{
			objCategory.value = "Establish";
			decodePatientCategory=1;
		}

		if((val == "Yes_confirmed")||(val == "No_confirmed")){
			$("#elem_flgIsPtNew").val(val);

			//
			var oPBC = gebi("elem_practiceBillCode");
			var oComp = gebi("elem_levelComplexity");
			if( (oComp && oComp.value!="")||oPBC.value == "920" || $("#elem_post_op_visit").val()=="1" || $("#elem_proc_only_visit").val() == "1"){

				if(typeof(flg_checkAssocPqri4All_checkvisitcode)!="undefined" && flg_checkAssocPqri4All_checkvisitcode!=0 && flg_checkAssocPqri4All_checkvisitcode!=""){
					checkAssocPqri4All_checkvisitcode(flg_checkAssocPqri4All_checkvisitcode);
				}else{
					checkAssocPqri4All();
				}

			}

			//alert("Find Assess level");
			return;
		}

		/*
		//Function
		if( typeof objCategory.onchange == "function" ){
			objCategory.onchange();
		}*/
		if((val == "Yes") || (val == "No")){
			confirmVisitCode_v3(); //run
		}else{
			confirmVisitCode_v2(); //run
		}
	}

	var decideLevelofService2_obj_id;
	function decideLevelofService2(val)
	{
		//var objLevelOfService = gebi("elem_levelOfService");
		if((val != "-1") && ($.trim(val) != "") && ($.trim(val) != "OK")){
			if(val=="Eye Code"){
				val = $(":checked[id*=lb_qlfy_lvl_code920]").eq(0).val();
			}else if(val=="EM Code"){
				val = $(":checked[id*=lb_qlfy_lvl_code992]").eq(0).val();
			}

			//if(typeof(decideLevelofService2_obj_id)!="undefined" && decideLevelofService2_obj_id!=""){ $("#"+decideLevelofService2_obj_id).val(val); }
			setVisitCode(val, decideLevelofService2_obj_id);
		}

		//
		decideLevelofService2_obj_id="";
		$("#dialog-Mdc-cum-new-pt").remove();

		/*
		if(typeof top.fmain == "undefined"){
			hideConfirmYesNo();
		}else{
			top.fmain.hideConfirmYesNo();
		}
		*/

		/*
		switch(val)
		{
			case "Level 1":
				objLevelOfService.value = "1";
				objLevelOfService.onchange();
			break;
			case "Level 2":
				objLevelOfService.value = "2";
				objLevelOfService.onchange();
			break;
			case "Level 3":
				objLevelOfService.value = "3";
				objLevelOfService.onchange();
			break;
			case "Current Level":
				setVisitCode();
			break;
			case "Continue":
				//donot set any code
				top.objCreatePopUp.undoAll();
			break;


			case -1:
				//top.window.close();
			break;
		}
		*/
	}

	function getVisitCode()
	{
		var objPracticeBillCode = gebi("elem_practiceBillCode");
		var objCategory = gebi("elem_category");
		var objLevelOfService = gebi("elem_levelOfService");
		var objVisitCode = gebi("elem_visitCode");
		var visitCode = category = "";

		if(objCategory.value != null && objPracticeBillCode!=null)
		{
			category = getPatientCategoryCode(objCategory.value, objPracticeBillCode.value);
		}
		//
		if(objPracticeBillCode && objCategory && objLevelOfService && objVisitCode)
		{
			visitCode = objPracticeBillCode.value+category+objLevelOfService.value;
		}
		//

		var ptrn = "^[0-9]{5}$";
		var reg = new RegExp(ptrn,"g");
		var iscodeOk = visitCode.match(reg);
		return (iscodeOk) ? visitCode : false;
		//alert("PracticeCode: "+objPracticeBillCode.value+"\nCategory: "+category+"\nLevel: "+objLevelOfService.value);
	}
	/*
	function checkVisitCodeChange(){
		alert("3")
		var obj=null;
		var e = window.event;
		if(e){
			obj = e.srcElement;
		}
		if(obj){
			alert(""+obj.value);
		}
	}*/

	function setVisitCode(val, flg_chk_mlti){
		var objVisitCode = gebi("elem_visitCode");
		var vCode = (typeof val != "undefined") ? val : false ; //getVisitCode();
		if(vCode != false){

			if(typeof(flg_chk_mlti)!="undefined" && flg_chk_mlti!=""){ setVisitProcedure_nochk=1; checkDB4Code_chk_versions=1; $("#"+flg_chk_mlti).val(vCode).triggerHandler("blur"); }
			else{

				//var cvc = $.trim(objVisitCode.value);
				//Donot change if user is inserting multi visit codes manually
				//if(typeof(flg_chk_mlti)=="undefined" || flg_chk_mlti==''){	cvc = ""; }

				//if(typeof(cvc)=="undefined" || cvc==""){
				checkDB4Code_chk_versions=1;
				objVisitCode.value = vCode;
				objVisitCode.onchange();
				//}else{
					//var x = isVisitCodeUsed(vCode); //

				//}
			}

			//if( (objVisitCode.value == "92002") || (objVisitCode.value == "92012") ){
				//alert("2")
				//objVisitCode.attachEvent("onchange", checkVisitCodeChange);
			//}
			//Set Today Charges
			var objsbnr = getMxSB();
			setSBTodayCharges();
			fun_mselect('.selectpicker','width');
		}
	}

	function isVisitCodeUsed(cCd){
		/*
		// Check Code
		var procedureName = arrIndexOf_v2(arrCptCodeDescActive[0],cCd);
		if(procedureName == -1)
		{
			alert("Currently, CPT Code "+cCd+" is not used by the practice.");
			return false;
		}
		*/
		if(typeof(sb_multi_vst_cd_noalert)!="undefined" && sb_multi_vst_cd_noalert==1){ return true; }

		var selectedCode = gebi("elem_procOrder").value;
		if(selectedCode.indexOf(cCd) != -1)
		{
			top.fAlert("CPT Code "+cCd+" is already selected.");
			return false;
		}
		return true;
	}

	function setVisitCodeFinal(objElem)
	{
		//objTable = gebi("tblVisitingCode");
		//objTd = gebi("tdVisitingCode");
		var ptrn = "^[0-9]{5}$";
		var reg = new RegExp(ptrn,"g");
		var matched = objElem.value.match(reg);
		if(matched)
		{
			if(isVisitCodeUsed(objElem.value)){
				//objTd.innerText = objElem.value;
				//objTable.style.visibility = "visible";
				setVisitProcedure(objElem.value);
			}

		}else if(objElem.value == sb_cpt_code_poe){
			setVisitProcedure(objElem.value);
		}
	}

	/*
	function checkDxCodeValid(dx){
		var ret = "";
		var len = arrTHPracCode.length;
		for(var i=0;i<len;i++){
			if( $.trim(arrTHPracCode[i]) == dx ){
				ret = arrTHPracCode[i];
				break;
			}
		}
		return ret;
	}
	*/

	//function getUnwantedDxCodes(){
	function getWantedDxCodes(flgnw){
		var oDivDxCode = gebi("divChooseDxCodes");
		var oElem = oDivDxCode.getElementsByTagName("INPUT");
		var len = oElem.length;
		var strdx="", dsc_tmp="";
		var arr = new Array(), arr_id = new Array();
		for(i=0;i<len;i++){
			if(oElem[i].name.indexOf("Lat")!=-1 || oElem[i].name.indexOf("Svr")!=-1 || oElem[i].name.indexOf("Stg")!=-1 ){ continue; }
			var t_dxid = $.trim($(oElem[i]).data("dxid"));
			if(typeof(t_dxid)=="undefined" || t_dxid==""){ t_dxid=""; }

			if((oElem[i].type == "checkbox") && (oElem[i].checked == true)){
				if(($.trim(oElem[i].value)!="" && (arr.indexOf(oElem[i].value)==-1 || (t_dxid!="" && arr_id.indexOf(t_dxid)==-1)))&&$(oElem[i]).parents("tr").css("display")!="none"){
					arr[arr.length] = oElem[i].value;
					arr_id[arr_id.length] = t_dxid ;
				}
			}else if((oElem[i].type == "text") && (oElem[i].value != "")){
				if(arr.indexOf(oElem[i].value)==-1 || (t_dxid!="" && arr_id.indexOf(t_dxid)==-1)){
					arr[arr.length] = oElem[i].value;
					arr_id[arr_id.length] = t_dxid ;
				}
			}
		}

		if(arr.length > 12){
			top.fAlert("You can choose maximum twelve dx codes.");
		}else{
			for(var i=0,j=0;j<12;j++){
				var oDxCode = gebi("elem_dxCode_"+(i+1));
				var tmp = ( (typeof arr[j] != "undefined") ) ? $.trim( arr[j] ) : "" ;
				var tmp_dxid = ( (typeof arr_id[j] != "undefined") ) ? $.trim( arr_id[j] ) : "" ;

				if((oDxCode) && ( ($.trim(oDxCode.value) == "") || ( typeof oDxCode.value == "undefined" ))
						&& ((typeof tmp != "undefined" ) && (tmp != ""))){
					dx_exist = 0;
					$("input[name^='elem_dxCode_']").each(function(index, element) {
						if($(this).val()!="" && $(this).val() == tmp && $(this).data("dxid") == tmp_dxid){
							dx_exist = 1;
						}
					});

					if(dx_exist == 0){
						oDxCode.value = tmp;
						$(oDxCode).data("dxid", tmp_dxid);
						if(strdx!=""){strdx+=",";}
						strdx+=tmp;
						i++;
					}
				}else{
					//oDxCode.value = "";
				}
			}
			oDivDxCode.style.display = "none";

			//Set DropDow Dx codes
			setTimeout(function(){set_dx_code_titles();}, 100);

			if(typeof(flgnw)!="undefined" && flgnw=="get_proc_dx_codes"){
				ogetprocdxcodes.dx = strdx.split(",");
				fill_codes_in_proc_sb(ogetprocdxcodes);
			}else{
				//Check PQRI All
				checkAssocPqri4All();
			}
		}
	}

	function isSuperBillMade(){
		//Super Bill
		var SBill = false;
		var DXCodeOK = false;
		var DXCodeAssocOK = true;
		var CptNotAssoc="";
		var DXCodeComplete = true;
		var dxids="";
		var c_ophth_cd=0;

		var tbl = $("#tblSuperbill").get(0);
		if(tbl){
			var len = (gebi("elem_mxSBId")) ? gebi("elem_mxSBId").value : 0 ;

			var lastRow = (tbl.length>0) ? tbl.rows.length : 0 ;
			len = (typeof len == "undefined") ? 0 : len;
			for(var i=0;i<len;i++){
				var tmp = gebi("elem_trSB"+(i+1));
				if(tmp == null){
					continue;
				}

				var temp = gebi("elem_cptCode_"+(i+1));
				if(($.trim(temp.value) != "")){
					SBill = true;
					//break;
					var tempAssoc = false;
					/*
					for(var j=0;j<4;j++){
						var elemDxAssoc = gebi( "elem_dxCodeAssoc_"+(i+1)+"_"+(j+1) );
						var oDxCodeTmp = gebi("elem_dxCode_"+(j+1));
						if((elemDxAssoc.checked == true)){
							if( $.trim(oDxCodeTmp.value) != "" ){
								tempAssoc = true;
							}else{
								elemDxAssoc.checked = false;
							}
						}
					}
					*/
					var elemDxAssoc = $( "#elem_dxCodeAssoc_"+(i+1) );
					var valDxAssoc = ""+fun_mselect("#elem_dxCodeAssoc_"+(i+1), 'val') ; //""+$("#elem_dxCodeAssoc_"+(i+1)).multiselect("getChecked").map(function(){  return this.value; }).get();
					//if(elemDxAssoc.val()!=null && $.trim(elemDxAssoc.val())!=""){
					if(valDxAssoc!=null && typeof(valDxAssoc)!="undefined" && $.trim(valDxAssoc)!=""&&valDxAssoc.indexOf("-")==-1){
						tempAssoc = true;
					}

					if(tempAssoc == false){
						DXCodeAssocOK = false;
						CptNotAssoc += "\n\t-"+temp.value+"";
						//break;
					}

					if(has_ophtha_code(temp)){c_ophth_cd=c_ophth_cd+1;}
				}
			}

			//DX Code
			if(SBill == true){
				for(var i=0;i<12;i++){
					var oDxCode = $("#elem_dxCode_"+(i+1)); //gebi("elem_dxCode_"+(i+1)); diagText_
					var t=$.trim(oDxCode.val());
					var tdx="";
					if((t != "")){
						DXCodeOK = true;
						if(t.indexOf("-")!=-1){DXCodeComplete=false;}
						//break;
						var dxid = oDxCode.data("dxid");
						if(typeof(dxid)!="undefined" && dxid!=""){
							tdx=dxid;
						}
					}
					dxids=dxids+""+tdx+";";
				}
			}

			//check multiple ophtha codes
			var mul_ophth_cd = (c_ophth_cd>=2) ? true : false;
		}

		return {"SBill":SBill,"DXCodeOK":DXCodeOK,"DXCodeAssocOK":DXCodeAssocOK,"CptNotAssoc":CptNotAssoc,"DXCodeComplete":DXCodeComplete,"dxids":dxids,
				"mul_ophth_cd":mul_ophth_cd};
	}

	function promptMedDecCom(opSB){

		var flg = 0;
		if(opSB == "1"){
			flg = 1;
		}

		var oPrnt=top.fmain;
		var strPrnt="top.fmain";
		if(typeof(wpage)!="undefined" && wpage=="accSB"){
			oPrnt=top;
			strPrnt="top";
		}

		//Get Medical Complexity
		///var title = "Medical Decision Complexity Prompt";
		var msg =""+
				"<label style=\"width:250px;display:inline-block;\"><b>Medical Decision Complexity :</b> </label>"+
				"<div id=\"divmdc\" style=\"\">"+
				"<input type=\"checkbox\" id=\"elem_chkMedComp_Strght\" value=\"1\" onclick=\""+strPrnt+".setMedComplexity(this,"+flg+")\"><label for=\"elem_chkMedComp_Strght\" >Straight Forward</label>"+
				"<input type=\"checkbox\" id=\"elem_chkMedComp_Low\" value=\"2\" onclick=\""+strPrnt+".setMedComplexity(this,"+flg+")\"><label for=\"elem_chkMedComp_Low\" >Low Complexity</label>"+
				"<input type=\"checkbox\" id=\"elem_chkMedComp_Moderate\" value=\"3\" onclick=\""+strPrnt+".setMedComplexity(this,"+flg+")\"><label for=\"elem_chkMedComp_Moderate\" >Moderate Complexity</label>"+
				"<input type=\"checkbox\" id=\"elem_chkMedComp_High\" value=\"4\" onclick=\""+strPrnt+".setMedComplexity(this,"+flg+")\"><label for=\"elem_chkMedComp_High\" >High Complexity</label>"+
				"<input type=\"checkbox\" id=\"elem_chkMedComp_procovisit\" value=\"5\" onclick=\""+strPrnt+".setMedComplexity(this,"+flg+")\"><label for=\"elem_chkMedComp_procovisit\" >Procedure only Visit</label><br/>"+
				"<input type=\"checkbox\" id=\"elem_chkMedComp_postopvisit\" value=\"6\" onclick=\""+strPrnt+".setMedComplexity(this,"+flg+")\"><label for=\"elem_chkMedComp_postopvisit\" style=\"float:right;margin-top:10px;\" >Post Op Visit</label>"+
				"</div>"+
				"";
		return ""+msg;

		/*
		var btn1 = "0";
		var btn2 = "0";
		var func = strPrnt+".hideConfirmYesNo";
		var oPDiv =oPrnt.displayConfirmYesNo_v2(title,msg,btn1,btn2,func,0);


		oPDiv.style.top = "200px";
		oPDiv.style.left = "300px";
		*/
	}


	/*This function will find match in every case: loosly*/
	function sb_check_dx_in_icd10(str_n,str_h){
		flgMtch=0;
		//check dx codes in icd 10 format --
		if($("#hid_icd10").val()=="1"){ //icd10
			var p0=$.trim(""+str_n.toLowerCase());
			var q=q0=$.trim(""+str_h.toLowerCase());
			var q1=""+q0.slice(0,-1)+"-";
			var q2=""+q0.slice(0,-2)+"--";
			var q3=""+q0.slice(0,-3)+"-x-";
			var p1=""+p0.slice(0,-1)+"-";
			var p2=""+p0.slice(0,-2)+"--";
			var p3=""+p0.slice(0,-3)+"-x-";
			if(q0==p0 || q1==p0 || q2==p0 || q3==p0 || q0==p1 || q0==p2 || q0==p3){  flgMtch=1; }
			else if(q0.indexOf(p0)!=-1 || q1.indexOf(p0)!=-1 || q2.indexOf(p0)!=-1 || q3.indexOf(p0)!=-1 ||
					p0.indexOf(q0)!=-1 || p1.indexOf(q0)!=-1 || p2.indexOf(q0)!=-1 || p3.indexOf(q0)!=-1){  flgMtch=1; }
		}
		return flgMtch;
	}

	//Sb ICD-10
	function sb_crt_dx_dropdown(objid, flgSetEmpty, arrmoresel,olddx){

		var all_rec = new Array;
		var all_rec_title = new Array;
		var all_rec_old = new Array;
		var all_rec_id = new Array;

		//
		var ptrnChckd=".dxallcodes[id*=elem_dxCode_]";

		$(""+ptrnChckd).each(function() {
			if($(this).val()!=""){
			all_rec.push($(this).val());
			var tdsc = $(this).attr("data-original-title");
			if(typeof(tdsc)=="undefined" || $.trim(tdsc)==""){
				tdsc = $(this).attr("title");
				if(typeof(tdsc)=="undefined"){ tdsc = ""; }
			}
			all_rec_title.push(tdsc);
			var tmpid = $(this).attr("id");
			var oldid = tmpid.replace("elem_dxCode","dx_oldCode");

			//alert(oldid+" - "+$("#"+oldid).length+" - "+$("#"+oldid).val());

			all_rec_old.push($("#"+oldid).val());
			var tdxid = $(this).data("dxid");
			if(typeof(tdxid)=="undefined"){ tdxid=""; }
			all_rec_id.push(tdxid);
			}else{
				all_rec.push('');
				all_rec_title.push('');
				all_rec_old.push('');
				all_rec_id.push('');
			}
		});

		//
		//alert(all_rec_old);

		//Unique
		//fix in chrome
		//if(navigator.userAgent.indexOf("Chrome") > -1){
			var txtRec="";
			var txtRecTitle="";
			var txtRecOld="";
			var txtRecId="";
			var all_rec_tmp=[];
			var all_rec_title_tmp=[];
			var all_rec_old_tmp=[];
			var all_rec_id_tmp=[];
			for(var s=0;s<12;s++){
				var tmpRec = $.trim(all_rec[s]);
				var tmpRecid = $.trim(all_rec_id[s]);
				var tmpRecTitle = $.trim(all_rec_title[s]);
				var tchk = tmpRec+tmpRecTitle+",";
				var tmpRecOld = $.trim(all_rec_old[s]);

				if(tmpRec != "" && txtRec.indexOf(tchk) == -1){
					txtRec+=tchk;
					all_rec_tmp[s] = tmpRec;
					all_rec_id_tmp[s] = tmpRecid;
					all_rec_title_tmp[s] = tmpRecTitle;
					all_rec_old_tmp[s] = tmpRecOld;
				}
				//
				/*
				if(tmpRecTitle != "" && txtRecTitle.indexOf(tmpRecTitle) == -1){
					txtRecTitle+=tmpRecTitle+",";

				}
				//

				if(tmpRecOld != "" && txtRecOld.indexOf(tmpRecOld) == -1){
					txtRecOld+=tmpRecOld+",";

				}*/
			}

			all_rec = all_rec_tmp;
			all_rec_title = all_rec_title_tmp;
			all_rec_old = all_rec_old_tmp;
			all_rec_id = all_rec_id_tmp;

		//}
		/*
		else{
			all_rec = $.unique(all_rec);
			all_rec_title = $.unique(all_rec_title);
			all_rec_old = $.unique(all_rec_old);
		}*/

		//alert(all_rec_old);

		var ptrn = "select.diagText_all_css";
		if(typeof(objid)=="string" && objid != ""){
			ptrn = objid;
		}


		$(""+ptrn).each(function(){
			if(this.type!="select-multiple"){return true;}
			var all_opt_data = "";
			var dd_title="";
			if(flgSetEmpty!=1){	var sel_val_arr= fun_mselect(this, 'val') ; /*$(this).val();*/ /*$(this).multiselect("getChecked").map(function(){ return this.value; }).get();*/ }
			if(typeof(arrmoresel)!="undefined" && arrmoresel!="" && arrmoresel.length>0){
				if(typeof(sel_val_arr)=="undefined" || sel_val_arr==null || sel_val_arr==''){ var sel_val_arr=[];  }
				sel_val_arr=sel_val_arr.concat(arrmoresel);
			}

			if(typeof sel_val_arr !="undefined" && sel_val_arr!=null && sel_val_arr!=''){

				for(x in all_rec){
					if(all_rec[x]!=""){

						var sel_opt="";
						//var sel_val_arr_spt=sel_val_arr.split('**');
						var yy=parseInt(x)+1;
						var chk_sel_rec=  all_rec[x]+'**'+yy;
						var chk_sel_rec2= all_rec[x];

						if(typeof(olddx)!="undefined" && olddx==1) {
							var chk_sel_rec_old=all_rec_old[x]+'**'+yy ;
							var chk_sel_rec2_old= all_rec_old[x] ;
							if($.inArray(chk_sel_rec_old,sel_val_arr)!=-1 || $.inArray(chk_sel_rec2_old,sel_val_arr)!=-1){
								sel_opt="selected";
							}
						}else{

							if($.inArray(chk_sel_rec,sel_val_arr)!=-1 || $.inArray(chk_sel_rec2,sel_val_arr)!=-1){
								sel_opt="selected";
							}else{
								///wwww
								//check dx codes in icd 10 format --
								for(var cc in sel_val_arr){
									var str_n = ""+sel_val_arr[cc].split("**")[0];
									if(sb_check_dx_in_icd10(str_n,chk_sel_rec2)){ //icd10
										sel_opt="selected";
									}
								}
							}
						}

						var chk_title = all_rec_title[x];
						if(typeof(chk_title)=="undefined"){  chk_title=""; }

						if(sel_opt=="selected"){ dd_title += ""+all_rec[x]+" - " +chk_title+"\n";}

						all_opt_data += '<option value="'+chk_sel_rec+'" '+sel_opt+' data-desc="'+chk_title+'"  >'+all_rec[x]+'</option>';
					}
				}
			}else{
				for(x in all_rec){
					if(all_rec[x]!=""){
						var yy=parseInt(x)+1;
						var chk_title = all_rec_title[x];
						if(typeof(chk_title)=="undefined"){  chk_title=""; }
						all_opt_data += '<option value="'+all_rec[x]+'**'+yy+'" data-desc="'+chk_title+'" >'+all_rec[x]+'</option>';
					}
				}
			}
			//
			var tmpid = $(this).attr("id");
			$("#"+tmpid).attr("title", dd_title).attr("data-original-title", dd_title).html(all_opt_data);
			//$("#"+tmpid).selectpicker('refresh');
			fun_mselect("#"+tmpid, 'refresh') ;
			if(dd_title==""){	fun_mselect("#"+tmpid, 'settitle', '') ; }

			/*
			console.log(tmpid);
			//console.log(all_opt_data);
			//
			//$("#"+tmpid).html("<option value=\"\"  ></option><option value=\"Q150\"  >Q150</option>");
			console.log($(this));
			/*r8 todo
			$(this).multiselect('refresh');
			if(dd_title!=""){ $(this).multiselect("getButton").prop("title",dd_title);  }
			*/

			//cc=ss;

		});
		fun_mselect('.selectpicker', 'width') ;
	}

	function sb_set_dx_typeahead_icd10(action){
		/*
		if(action=='no'){
			if(document.getElementById('enc_icd10').value>0){
				document.getElementById('enc_icd10').value = 0;
			}else{
				document.getElementById('enc_icd10').value = 1;
			}
			return false;
		}
		*/

		/*
		if(action=='yes'){
			for(var j=1;j<=12;j++){
				document.getElementById('diagText_'+j).value='';
			}
			crt_dx_dropdown();
		}
		*/


		//parent.document.getElementById('ICD10_butt').style.display='inline';
		//if(document.getElementById('enc_icd10').value>0){
			getDataFilePath = "../common/getICD10data.php";
			//parent.document.getElementById("ICD10_butt").className='dff_buttonog';
		//}else{
		//	parent.document.getElementById("ICD10_butt").className='dff_button';
		//}
		for(var j=1;j<=12;j++){
			//actb(document.getElementById('diagText_'+j),'');
			//bind_autocomp($("#diagText_"+j),'');
			if(document.getElementById('enc_icd10').value==1){
				bind_autocomp($("#elem_dxCode_"+j),getDataFilePath);
				$('.diagText_span_'+j).css({"display":"table-cell"});
				//document.getElementById('diagText_'+j).readOnly=false;
			}
			/*else{
				if(j<=4){
					var obj11 = new actb(document.getElementById('diagText_'+j),customarrayDiag);
				}else{
					$('.diagText_span_'+j).css({"display":"none"});
					//document.getElementById('diagText_'+j).readOnly='readonly';
				}
			}*/
		}
	}

	//Copy Dx Code in all dx fields
	function sb_copy_dx_codes(flgfrst){
		sb_cw4WD_flgnoalert=1; //alert off
		var copyvalues=[];
		$( ".cptcode" ).each(function(){
			var id = this.id;
			var val= $.trim(this.value);
			var indx = id.replace("elem_cptCode_","");
			if(indx == "1"){
				//get Dx values to copy
				/*
				copyvalues = $("select[id=elem_dxCodeAssoc_"+indx+"]").multiselect("getChecked").map(function(){
										   return this.value;
										}).get();
				*/

				copyvalues = fun_mselect("select[id=elem_dxCodeAssoc_"+indx+"]", 'val') ;
				if(typeof(flgfrst)!="undefined" && flgfrst==1){copyvalues.length=1;}
				//alert(copyvalues);
			}else{
				if(val==""){
				}else{
					if(copyvalues.length>0){
						//
						/*
						$("select[id=elem_dxCodeAssoc_"+indx+"]").multiselect("widget").find(":checkbox").each(function(){
							if(copyvalues.indexOf(this.value)!=-1 && this.checked==false){
								this.click();
							}
						});
						*/
						fun_mselect("select[id=elem_dxCodeAssoc_"+indx+"]", 'select', copyvalues) ;
					}
				}
				//$("select[id=elem_dxCodeAssoc_"+indx+"]").multiselect("refresh");
				fun_mselect("select[id=elem_dxCodeAssoc_"+indx+"]", 'render') ;
			}

			//

			sb_checknwarn4WrongDxcode(this);

		});

		fun_mselect('.selectpicker','width');
		sb_cw4WD_flgnoalert=""; //alert on

	}

	function icd10_charts_popup(show_pop,obj,str,input_id){
		var str_val = (str.toLowerCase()).replace(">>","");
		if(show_pop==1){
			if(js_icd10_charts_data_arr){
				//alert(js_icd10_charts_data_arr['ESOTROPIA']);
				if($( "#dialog-msg-charts" ).length<=0){$("body").append(js_icd10_charts_data_arr[str_val]);}
				//alert(js_icd10_charts_all_data_arr);
				if(str_val=='congenital'){
					var wid=815;
				}else{
					var wid=500;
				}
				$( "#dialog-msg-charts" ).show().dialog({modal: true, width: wid, buttons: {  Done: function() { icd10_charts_popup(0,'','',input_id); }  ,Cancel: function() {  $( this ).dialog( "destroy" ); $("#dialog-msg-charts").remove(); }  }  });
			}
		}else{
			if(obj.id){
				var charts_chk_box_id=obj.id;
				if(($('#dialog-msg-heading').val()).toLowerCase()=="congenital"){
					$('label[for='+charts_chk_box_id+']').css({"color":"red","font-weight":"bold"}).addClass("highlight");
					$(".elem_sub_cong").each(function(){
						if(this.id!=charts_chk_box_id || $('#'+charts_chk_box_id).is(':checked')==false){
							$('label[for='+this.id+']').css({"color":"purple","font-weight":"normal"}).removeClass("highlight");
							this.checked=false;
						}
					});
				}else{
					if(obj.value.toLowerCase()=="unspecified"){
						if($('#'+charts_chk_box_id).is(':checked')==true){
							$(".unspecified_css").each(function(){
								$('label[for='+this.id+']').css({"color":"gray","font-weight":"normal"}).removeClass("highlight");
								this.checked=false;
							});
						}else{
							$(".unspecified_css").each(function(){
								$('label[for='+this.id+']').css({"color":"purple","font-weight":"normal"});
							});
						}
					}else if(obj.value.toLowerCase()=="alternating"){
						if($('#'+charts_chk_box_id).is(':checked')==true){
							$(".unspecified_css").each(function(){
								$('label[for='+this.id+']').css({"color":"purple","font-weight":"normal"});
							});
							$(".alternating_css").each(function(){
								$('label[for='+this.id+']').css({"color":"gray","font-weight":"normal"}).removeClass("highlight");
								this.checked=false;
							});
						}else{
							$(".unspecified_css").each(function(){
								$('label[for='+this.id+']').css({"color":"purple","font-weight":"normal"});
							});
						}
					}else{
						if($('#elem_sub_unspecified').is(':checked')==true){
							if(obj.value.toLowerCase()!="monocular" && obj.value.toLowerCase()!="alternating"){
								$(".unspecified_css").each(function(){
									this.checked=false;
								});
								return;
							}else{
								$(".unspecified_css").each(function(){
									$('label[for='+this.id+']').css({"color":"purple","font-weight":"normal"});
								});
							}
						}else if($('#elem_sub_alternating').is(':checked')==true){
							if(obj.value.toLowerCase()=="left" || obj.value.toLowerCase()=="right"){
								$(".alternating_css").each(function(){
									this.checked=false;
								});
								return;
							}else{
								if(obj.value.toLowerCase()=="monocular"){
									$(".unspecified_css").each(function(){
										$('label[for='+this.id+']').css({"color":"purple","font-weight":"normal"});
									});
								}
							}
						}
					}


					if(obj.value.toLowerCase()=="unspecified" || obj.value.toLowerCase()=="monocular" || obj.value.toLowerCase()=="alternating"){
						$('label[for='+charts_chk_box_id+']').css({"color":"red","font-weight":"bold"}).addClass("highlight");
						$(".elem_sub_charts1").each(function(){
							if(this.id!=charts_chk_box_id || $('#'+charts_chk_box_id).is(':checked')==false){
								$('label[for='+this.id+']').css({"color":"purple","font-weight":"normal"}).removeClass("highlight");
								this.checked=false;
							}
						});
					}
					if(obj.value.toLowerCase()=="a" || obj.value.toLowerCase()=="v" || obj.value.toLowerCase()=="intermittent"){
						$('label[for='+charts_chk_box_id+']').css({"color":"red","font-weight":"bold"}).addClass("highlight");
						$(".elem_sub_charts2").each(function(){
							if(this.id!=charts_chk_box_id || $('#'+charts_chk_box_id).is(':checked')==false){
								$('label[for='+this.id+']').css({"color":"purple","font-weight":"normal"}).removeClass("highlight");
								this.checked=false;
							}
						});
					}
					if(obj.value.toLowerCase()=="right" || obj.value.toLowerCase()=="left"){
						$('label[for='+charts_chk_box_id+']').css({"color":"red","font-weight":"bold"}).addClass("highlight");
						$(".elem_sub_charts3").each(function(){
							if(this.id!=charts_chk_box_id || $('#'+charts_chk_box_id).is(':checked')==false){
								$('label[for='+this.id+']').css({"color":"purple","font-weight":"normal"}).removeClass("highlight");
								this.checked=false;
							}
						});
					}
				}
			}else{
				var charts_val1="";
				var charts_val2="";
				var charts_val3="";
				var main_charts_val=$('#dialog-msg-heading').val();
					main_charts_val = main_charts_val.toLowerCase();
				if(main_charts_val=="congenital"){
					var full_ass_id=input_id.replace("elem_assessment_dxcode","");
						full_ass_id=full_ass_id.replace("elem_assessment","");
					var ass_id = "elem_assessment"+full_ass_id;
					var ass_dx_id = "elem_assessment_dxcode"+full_ass_id;

					$(".elem_sub_cong").each(function(){
						var charts_chk_box_id=this.id;
						if($('#'+charts_chk_box_id).is(':checked')==true){
							var charts_val_ass_dx=$('#'+charts_chk_box_id).val();
							var charts_val_ass=$('#'+charts_chk_box_id+"_desc").val();
							$('#'+ass_id).val(charts_val_ass);
							$('#'+ass_dx_id).val(charts_val_ass_dx);
							$("#dialog-msg-charts").remove();
							return;
						}
					});

				}else{
					var selected_opt_arr = new Array();
					var arr_i=0;
					selected_opt_arr[arr_i]=main_charts_val;
					$(".elem_sub_charts1").each(function(){
						var charts_chk_box_id=this.id;
						if($('#'+charts_chk_box_id).is(':checked')==true){
							charts_val1=$('#'+charts_chk_box_id).val();
							arr_i++;
							selected_opt_arr[arr_i]=charts_val1;
						}
					});
					$(".elem_sub_charts2").each(function(){
						var charts_chk_box_id=this.id;
						if($('#'+charts_chk_box_id).is(':checked')==true){
							charts_val2=$('#'+charts_chk_box_id).val();
							arr_i++;
							selected_opt_arr[arr_i]=charts_val2;
						}
					});
					$(".elem_sub_charts3").each(function(){
						var charts_chk_box_id=this.id;
						if($('#'+charts_chk_box_id).is(':checked')==true){
							charts_val3=$('#'+charts_chk_box_id).val();
							arr_i++;
							selected_opt_arr[arr_i]=charts_val3;
						}
					});
					for(var j=0;j<=js_icd10_charts_all_data_arr[main_charts_val].length;j++){
						var dx_flag_cont=0;

						if(typeof(js_icd10_charts_all_data_arr[main_charts_val][j])!="undefined"){
							var dx_org_val=js_icd10_charts_all_data_arr[main_charts_val][j].split('>>>');
						}else{
							var full_ass_id=input_id.replace("elem_assessment_dxcode","");
							full_ass_id=full_ass_id.replace("elem_assessment","");
							var ass_id = "elem_assessment"+full_ass_id;
							var v = $("#"+ass_id).val();
							if(typeof(v)=="undefined"){v="";}
							if(v!=""){ v=v.replace(/>/g,''); $("#"+ass_id).val(v);}
							$("#dialog-msg-charts").remove();
							return;
						}
						var dx_org_val=js_icd10_charts_all_data_arr[main_charts_val][j].split('>>>');
						var dx_org_val_desc_spt=dx_org_val[0].split(',');
						var dx_org_val_desc="";
						for(var g=0;g<dx_org_val_desc_spt.length;g++){
							dx_org_val_desc+=dx_org_val_desc_spt[g];
						}
						var dx_org_val_desc_exp=dx_org_val_desc.split(' ');
						if(dx_org_val[1]!=""){
							var dx_org_val_code=dx_org_val[1];
							for(var k=0;k<=dx_org_val_desc_exp.length;k++){
								var dx_org_val_desc_exp_final=dx_org_val_desc_exp[k];
								for(var l=0;l<selected_opt_arr.length;l++){
									if(selected_opt_arr[l]!=""){
										//alert(dx_org_val_desc+"=="+selected_opt_arr[l]+'=='+dx_org_val_desc_exp_final);
										if(selected_opt_arr[l]==dx_org_val_desc_exp_final){
											dx_flag_cont=parseInt(dx_flag_cont)+1;
										}
									}
								}
							}
						}
						//alert(selected_opt_arr.length+'=='+dx_flag_cont+'='+dx_org_val_desc);
						if(selected_opt_arr.length==dx_flag_cont){
							var full_ass_id=input_id.replace("elem_assessment_dxcode","");
								full_ass_id=full_ass_id.replace("elem_assessment","");
							var ass_id = "elem_assessment"+full_ass_id;
							var ass_dx_id = "elem_assessment_dxcode"+full_ass_id;
							var elem_sub_ass_comm ="";
							if($('#elem_sub_ass_comm').val()!=""){
								elem_sub_ass_comm = " ; \n"+$('#elem_sub_ass_comm').val();
							}
							$('#'+ass_id).val(dx_org_val[0]+elem_sub_ass_comm);
							$('#'+ass_dx_id).val(dx_org_val_code);

							$("#dialog-msg-charts").remove();

							$("#"+ass_id).triggerHandler("click");
							$("#"+ass_id).triggerHandler("blur");
							$("#"+ass_id).triggerHandler("change");
							$("#"+ass_id).triggerHandler("keyup");

							return;
						}
					}
				}
			}
		}
	}

	function isVisitCodeExits(){
		if(typeof(sb_cpt_code_poe) == "undefined"){ sb_cpt_code_poe=""; }
		var allVisCd=[	"999",""+sb_cpt_code_poe,
						"92002","92012","92004","92014",
						"99201","99202","99203","99204","99205",
						"99211","99212","99213","99214","99215",
						"99241","99242","99243","99244","99245",
						"99024" //tkt5590
					];
		var ret=-1;
		var ln = $(".cptcode").length;
		for(var i=0;i<ln;i++){
			var v = $(".cptcode").eq(i).val();
			if(v!=""){
				if(allVisCd=="")continue;
				if(jQuery.inArray(v, allVisCd)!=-1){
					//Visit Code Exists
					return 1;
				}else{
					ret=0;
				}
			}
		}
		return ret;
	}

//
var set_multi_version_cpt_id;
function set_multi_version_cpt(s){
	if(s==1 && typeof(set_multi_version_cpt_id)!="undefined" && set_multi_version_cpt_id!=""){
		var x = $("#dialog-confirm :checked[name=el_multi_version_cpts]").val();
		if(typeof(x)!="undefined" && x!=""){$("#"+set_multi_version_cpt_id).val(x).triggerHandler("blur");}
	}
}

//Check Dx Code for validity
var checkDB4Code_flg=0; checkDB4Code_chk_versions="";
function checkDB4Code(obj,str,type,cnfrm){

	// if nothing typed return || flg is 1
	if(cnfrm && typeof(obj)=="string") {
		obj = document.getElementById(obj);
	}

	if($.trim(obj.value).length == 0||checkDB4Code_flg==1)
	{
		return;
	}
	var chk_versions = checkDB4Code_chk_versions;
	var type_chk = type;
	if(type_chk=="account"){
		var url = zPath+"/chart_notes/requestHandler.php";
		var elem_dxCode="diag1Text_";
		var elem_asDx="diag1Text_";
	}else if(type_chk=="superbill"){
		var url = zPath+"/chart_notes/requestHandler.php";
		var elem_dxCode=obj.name;
		var elem_asDx=obj.name;
		var elem_assessment_dxcode=obj.name;
	}else if(type_chk=="Procedures"){
		var url = zPath+"/chart_notes/requestHandler.php";
		var elem_dxCode=obj.name;
		var elem_asDx=obj.name;
		var elem_assessment_dxcode=obj.name;
	}else{
		var url = (typeof(wpage)!="undefined"&&wpage=="accSB") ? zPath+"/chart_notes/requestHandler.php" : zPath+"/chart_notes/requestHandler.php";
		var elem_dxCode="elem_dxCode_";
		var elem_asDx="elem_asDx";
		var elem_assessment_dxcode="elem_assessment_dxcode";
	}
	//var params = "elem_formAction:"+str+", ";
	//params += "elem_desc:"+encodeURI(obj.value)+", ";
	var params = {"elem_formAction":str, "elem_desc" : encodeURI(obj.value), "check_versions":chk_versions };

	var icd10 = ($("#hid_icd10").length>0) ? $("#hid_icd10").val() : $("#enc_icd10").val();
	if(icd10==1){
		//params += "ICD10:1, ";
		params.ICD10=1;
	}

	//Set P image
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","tblSuperbill");
	//objElementEffected = obj;
	//flg
	if(checkDB4Code_flg==0){checkDB4Code_flg=1;}

	//console.log("TEST 11", type_chk, elem_dxCode,elem_asDx,elem_assessment_dxcode, obj.name );

	if((obj.name.indexOf(elem_dxCode) == -1) && (obj.name.indexOf(elem_asDx) == -1) && (obj.name.indexOf(elem_assessment_dxcode) == -1) && type_chk!="account"){ //

		$.post(url,params,function(data){

			checkDB4Code_chk_versions = ""; //empty flag

			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","tblSuperbill");
			checkDB4Code_flg=0;

			var strTmp=data.code;

			strTmp = (strTmp) ? $.trim(strTmp) : "";
			if(strTmp != ""){

				//if Multiple versions exists
				if(str=="Cpt" && data.all_version && data.all_version.length>0){
					var msg = "";
					for(var x in data.all_version){
						if(typeof(data.all_version[x])!="undefined" && data.all_version[x]!=""){
							msg += "<div class=\"radio radio-inline\"><input type=\"radio\" name=\"el_multi_version_cpts\" id=\"el_multi_version_cpts"+x+"\" value=\""+data.all_version[x]+"\" ><label for=\"el_multi_version_cpts"+x+"\">"+data.all_version[x]+"</label></div>";
						}
					}
					if(msg!=""){
					set_multi_version_cpt_id = obj.id;
					obj.value = "";
					displayConfirmYesNo_v2("Multiple versions of CPT exists. Choose one.", ""+msg, "Insert","","set_multi_version_cpt","",0);
					return;
					}
				}


				obj.value = strTmp;
				if(data.desc)obj.title = data.desc;
				if(str=="Cpt"){
					//Units
					if(data.units){
						var unitId = obj.name.replace(/elem_cptCode_/i,"elem_procUnits_");
						var tmp = $("#"+unitId).data("unit");
						if(typeof(tmp)!="undefined" && tmp!=""){ data.units=tmp; }
						$("#"+unitId).val(""+data.units);
					}

					//Mod
					var mId = obj.name.replace(/elem_cptCode_/i,"elem_modCode_");
					var tmod="";
					for(var m=1,n=0;m<=4;m++){
						var t=$.trim($("#"+mId+"_"+m).val());
						if(tmod.indexOf(t)!=-1){
							$("#"+mId+"_"+m).val("");//empty duplicate
						}
						if(data.mod&&data.mod[n]){
							if(typeof(t)=="undefined"||t==""){
								$("#"+mId+"_"+m).val(""+data.mod[n]);
								tmod+=""+data.mod[n]+",";
								n++;
							}else{
								if(t==data.mod[n]){
								tmod+=""+data.mod[n]+",";
								n++;
								}
							}
						}
					}
					//cpt4Code
					if(data.cpt4_code){
						obj.setAttribute("cpt4code",data.cpt4_code);
					}
					//validDxCode
					if(data.dx_codes){
						obj.setAttribute("valid_dxcodes",data.dx_codes);
					}else{
						obj.setAttribute("valid_dxcodes","");
					}
					//After Processing functions
					checkCptCodesChart(obj,1);
				}else if(str=="Md"){ setSBTodayCharges(); }

			}else{
				//alert("Please enter valid desc or practice code.");
				//objElementEffected.value = "";
				if(typeof(cnfrm)=="undefined"){
					if(top.fmain) {
						top.fancyConfirm("Entered Text Does not match with any code.<br />Do you want to use this?",'','','top.fmain.checkDB4Code("'+obj.id+'","'+str+'", "'+type+'",true)');
					}
					else{
						top.fancyConfirm("Entered Text Does not match with any code.<br />Do you want to use this?",'','','top.checkDB4Code("'+obj.id+'","'+str+'", "'+type+'",true)');
					}
					return;
				}
				else{
					obj.value = obj.title = obj.cpt4code =  "" ;
					obj.setAttribute("valid_dxcodes","");
				}
			}

		},'json');
	}
	else{

		//DX Code Only
		var dxid = $(obj).data("dxid");
		if(typeof(dxid)!="undefined"){
			dxid = $.trim(dxid);
			if($.trim(dxid)!="" && $.trim(dxid)!="0"){
				params.dxid=dxid;
			}
		}

		$.post(url,params,
			function(data){
				//Debugging
				//var xmlDoc = data;
				//alert(""+xmlDoc);
				//Debugging
				if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","tblSuperbill");
				checkDB4Code_flg=0;
				var xmlDoc = data;
				var arrDxCode = xmlDoc.getElementsByTagName("dxcode");
				var arrDxCodeDesc = xmlDoc.getElementsByTagName("dxcodedesc");
				var arrPqriCode = xmlDoc.getElementsByTagName("pqri");
				var arrDxCode_multi = xmlDoc.getElementsByTagName("otherdxCodes_multi");
				var arrDxCode_incomp = xmlDoc.getElementsByTagName("incomplete");

				var oDxCode = (arrDxCode.length > 0) ? xmlDoc.getElementsByTagName("dxcode")[0] : null ;
				var oDxCodeDesc = (arrDxCodeDesc.length > 0) ? xmlDoc.getElementsByTagName("dxcodedesc")[0] : null ;
				var oPqriCode = (arrPqriCode.length > 0) ? xmlDoc.getElementsByTagName("pqri")[0] : null ;
				var oDxCode_multi = (arrDxCode_multi.length > 0) ? xmlDoc.getElementsByTagName("otherdxCodes_multi")[0] : null ;
				var oDxCode_incomp = (arrDxCode_incomp.length > 0) ? arrDxCode_incomp[0] : null ;

				if((oDxCode) && (oDxCode.firstChild)){
					var tDx = $.trim(oDxCode.firstChild.data);
					//if call from Dx Code Selection Pop Up, return
					if(type_chk!="account" && type_chk!="superbill"){
						if((obj.name.indexOf("elem_asDx") != -1) || (obj.name.indexOf("elem_assessment_dxcode") != -1)){
							if(oDxCode_multi && oDxCode_multi.firstChild.data){ // if multiple
								tDx=oDxCode_multi.firstChild.data+", "+tDx;
							}
							obj.value=""+tDx;
							//fill in dx code also
							if(obj.name.indexOf("elem_asDx") != -1) {
								var tmp = $(obj).data("asindx");
								if(typeof(tmp)!="undefined" && tmp!=""){
									var as_dx_id = tmp;
								}else{
									var as_dx_id = obj.name.replace(/(elem_asDx)/g,""); //
									as_dx_id = as_dx_id.replace("_0","");
									as_dx_id = parseInt(as_dx_id)+1;
								}
								$("#elem_assessment_dxcode"+as_dx_id).val(""+tDx);
							}else{
								//Show/hide site drop down in assessment
								var op = $.trim(oDxCode_incomp.firstChild.data); op = (op=="1") ? "0" : "1" ;
								show_asmt_site(obj, '', op);
							}
							return;
						}
					}
					fExists = false;
					//Check if Dx Codes exits
					for(var i=1;i<=12;i++){
						var oDxChk = document.getElementById("elem_dxCode"+i);
						if((oDxChk) && ($.trim(tDx) != "") && (oDxChk.value == tDx)){
							fExists = true;
							//Desc
							if(oDxCodeDesc.firstChild){
								if($.trim(oDxCodeDesc.firstChild.data) != ""){
									renew_title(oDxChk, ""+oDxCodeDesc.firstChild.data);
								}

							}
						}
					}
					if(fExists == false){
						obj.value = tDx;
						//Desc
						if(oDxCodeDesc.firstChild){
							if($.trim(oDxCodeDesc.firstChild.data) != ""){
								renew_title(obj, ""+oDxCodeDesc.firstChild.data);
							}
						}
					}
				}else{
					//alert("pass 1");
					//if(!isDxCorrectCode(obj.value) || (!confirm("Entered Text Does not match with any DxCode.\n Do you want to use this?"))){
					if($.trim(obj.value).substr(-2,2)=='>>'){
						return;
					}

					//
					$(obj).removeData("dxid");
					renew_title(obj, "");

					if(typeof(cnfrm)=="undefined"){
						if (top.fmain) {
							top.fancyConfirm("Entered Text Does not match with any DxCode.<br />Do you want to use this?",'','','top.fmain.checkDB4Code("'+obj.id+'","'+str+'", "'+type+'",true)');
						}
						else{
							top.fancyConfirm("Entered Text Does not match with any DxCode.<br />Do you want to use this?",'','','top.checkDB4Code("'+obj.id+'","'+str+'", "'+type+'",true)');
						}
					}
					else{
						obj.value = obj.title = "";
					}
					return;
				}

				if(obj.value!=""){
					var oPCodes = oPqriCode.childNodes;
					var len = oPCodes.length;

					var arrPqriCodes = new Array();
					var tmpCpt = [];
					for(var i=0;i<len;i++){
						if(oPCodes[i].firstChild){
							tmpCpt[tmpCpt.length] = oPCodes[i].firstChild.data;
						}
					}

					if(tmpCpt.length>0){
						arrPqriCodes[obj.value]=tmpCpt;
						//Array Dx Codes
						//alert(""+arrPqriCodes+"\n"+arrPqriCodes.length);
						PqriSetter(arrPqriCodes);
						//*/
					}
				}

				//set dropdowns
				//Set DropDow Dx codes
				sb_crt_dx_dropdown();
				//setTimeout(function(){}, 800);
				//$(".dxallcodes").triggerHandler("blur");

			},"xml");

	}
}

//--
function getRefInfoJs(giveonly){

	var arr = new Array();
	var len = $("#Vision #MR div[id*=row_mr_]").length;
	for(var i=1; i<=len; i++){
		var othr = (i>1) ? "Other" : "";
		var sfx =  (i>2) ? "_"+i : "";
		var ar1 = new Array("elem_visMr"+othr+"OdS"+sfx,"elem_visMr"+othr+"OdC"+sfx,"elem_visMr"+othr+"OdA"+sfx,
						"elem_visMr"+othr+"OsS"+sfx,"elem_visMr"+othr+"OsC"+sfx,"elem_visMr"+othr+"OsA"+sfx,
						"elem_visMr"+othr+"OdAdd"+sfx,"elem_visMr"+othr+"OsAdd"+sfx,"elem_mrNoneGiven"+i);
		arr[arr.length] = ar1;
	}

	var dx = "";
	var flag = 0;
	var eye = "";
	for(var z in arr){
		var flg = isVisElemDone(arr[z]);
		if(flg){
			//policy is given only
			if(typeof(giveonly)!="undefined"&&giveonly=="1"){
				//check given
				if($(":checked[name='"+arr[z][8]+"']").length<=0 || !$(":checked[name='"+arr[z][8]+"']").hasClass("active")){
					continue; //
				}
			}

			flag = 1;
			var add = gebi(arr[z][6]);
			var ads = gebi(arr[z][7]);

			var sd = gebi(arr[z][0]);
			var ss = gebi(arr[z][3]);

			if(eye==""){
				if(sd && sd.value!=""){eye="OD";}
				if(ss && ss.value!=""){eye=(eye=="OD") ? "OU" : "OS" ;}
			}

			if((add && ($.trim(add.value) != "") && ($.trim(add.value) != "+")) || (ads && ($.trim(ads.value) != "") && ($.trim(ads.value) != "+"))){
				dx = "367.4";
				break;
			}else if((sd && (sd.value != "-") && (sd.value.indexOf("-") != -1)) || (ss && (ss.value != "-") && (ss.value.indexOf("-") != -1))){
				dx = "367.1";
				break;
			}else if((sd && (sd.value != "+") && (sd.value != "")) || (ss && (ss.value != "+") && (ss.value != ""))){
				dx = "367.0";
				break;
			}
		}
	}

	return { "flag":flag,"dx":dx, "eye":eye};
}

function isAssessmentDone(){
	//Get Assessments
	var lenAssess = $("#assessplan .planbox").length;
	for(var i=1;i<lenAssess;i++){
		var oAssess = gebi("elem_assessment"+i);
		if( (oAssess) && ($.trim(oAssess.value) != "") ){
			return true;
		}
	}
	return false;
}

function checkAssocPqri4All_getParam(cptcd){

	var params = "elem_formAction=PQRI";
	params += "&elem_formId="+gebi("elem_masterId").value;

	//Dx
	for(var i=1,j=1;i<=12;i++,j++){
		var oDx = gebi("elem_dxCode_"+i);
		if(oDx && (oDx.value != "")){
			params += "&elem_dxCode"+j+"="+oDx.value;
		}
	}
	//ApIds
	if(typeof getDCFA_strApId != "undefined"){
		params += "&elem_apid="+getDCFA_strApId;
	}

	//Refrection
	if($("#Vision").length>0){
	var oRefInfo = getRefInfoJs(sb_refGivenOnly);
	if(oRefInfo.flag == 1){
		params += "&elem_refraction=1";
		params += "&elem_refractionDx="+oRefInfo.dx;
		params += "&elem_vision=1";
		params += "&elem_visionEye="+oRefInfo.eye;
	}else{
		//Vision
		var arrVisDis=new Array("elem_visDisOdSel1","elem_visDisOdSel2","elem_visDisOsSel1",
						   "elem_visDisOsSel2","elem_visDisOdTxt1","elem_visDisOdTxt2",
						   "elem_visDisOsTxt1","elem_visDisOsTxt2","elem_disDesc");
		var vis = (isVisElemDone(arrVisDis)) ? "1" : "0";
		if(vis == "1"){
			params += "&elem_vision="+vis;
			params += "&elem_refraction=0";
		}else{
			params += "&elem_vision=0";
			params += "&elem_refraction=0";
		}
	}
	}else{
		params += "&elem_vision=db";
		params += "&elem_refraction=db";
	}

	//Medical Decision Complexity
	var oComp = gebi("elem_levelComplexity");
	if( oComp){
		var tmp = (typeof oComp.value != "undefined") ? $.trim(oComp.value) : "";
		params += "&elem_levelComplexity="+tmp;
	}

	//pro_only_visit
	var opov = gebi("elem_proc_only_visit");
	if( opov){
		var tmp = (typeof opov.value != "undefined") ? $.trim(opov.value) : "";
		params += "&elem_proc_only_visit="+tmp;
	}

	//post_op_visit
	var opopv = gebi("elem_post_op_visit");
	if( opopv){
		var tmp = (typeof opopv.value != "undefined") ? $.trim(opopv.value) : "";
		params += "&elem_post_op_visit="+tmp;
	}

	//Practice Code
	var oPrBillCd = gebi("elem_practiceBillCode");
	if(oPrBillCd){
		var tmp = (typeof oPrBillCd.value != "undefined") ? $.trim(oPrBillCd.value) : "";
		params += "&elem_practiceBillCode="+tmp;
	}

	//Rvs
	if(typeof getRvsDoneSt != "undefined"){
		var rvsSt = getRvsDoneSt();
		params += "&elem_rvs="+rvsSt;
	}else{
		params += "&elem_rvs=db";
	}

	//CCHx
	var oCCHx = gebi("elem_ccHx");
	var oCC = gebi("elem_ccompliant");
	if(oCCHx && oCC){
		var strCCHx = oCC.value;
		//Refine it from default String
		var ptrn = "\\s*A\\s*((\\d)+\\s*(years|months|days)\\s*old\\s*)?(Male|Female|male|female)\\s*with\\s*history\\s*of\\s*(\\r)?\\s*";
		strCCHx = regReplace(ptrn,"",strCCHx);
		if($.trim(strCCHx) == "" && oCCHx && $.trim(oCCHx.value)!=""){
			strCCHx = regReplace(ptrn,"",oCCHx.value);
		}
		params += "&elem_ccHx="+strCCHx;
	}else{
		params += "&elem_ccHx=db";
	}

	//Assess
	if(typeof(isAssessmentDone) != "undefined"){
	var strAss = (isAssessmentDone()) ? "1" : "0";
	params += "&elem_assessment="+strAss;
	}else{
	params += "&elem_assessment=db";
	}

	//Neuro/Psych
	var oNpsych = gebi("elem_neuroPsych");
	if(oNpsych){
	var strNpsych = ($.trim(oNpsych.value) != "") ? "1" : "0";
		params += "&elem_neuroPsych="+strNpsych;
	}else{
		params += "&elem_neuroPsych=db";
	}

	//insurance case Id
	var oSBCaseId = gebi("elem_sb_insuranceCaseId");
	if(oSBCaseId && (typeof oSBCaseId.value != "undefined") && (oSBCaseId.value != "") && (oSBCaseId.value != "0") ){
		params += "&elem_insCaseId="+oSBCaseId.value;

	}else{
		var oMasterCaseId = gebi("elem_masterCaseId");
		if(oMasterCaseId && (typeof oMasterCaseId.value != "undefined")){
			params += "&elem_insCaseId="+oMasterCaseId.value;
		}
	}

	//DOS
	var oDOS = gebi("elem_dos");
	if(oDOS && (typeof oDOS.value != "undefined")){
		params += "&elem_dos="+oDOS.value;
	}

	//DoctorId
	var oPhyId = $("div.divsign :input[name*=elem_physicianId][value!=''][type!=text]"); //$("div.divsign :input[name*=elem_physicianId][value!='']").val();//gebi("elem_physicianId");
	if(oPhyId && oPhyId.length>0 && typeof(oPhyId.val()) != "undefined"){
		params += "&elem_doctorId="+oPhyId.val();
	}

	//wpage: if open in accounting: do not merge
	flgMergeSB = 1;
	if(typeof(wpage)!="undefined" && wpage=="accSB"){
		flgMergeSB = 0;
	}
	params += "&flgMergeSB="+flgMergeSB;
	//wpage: if open in accounting: do not merge

	//isnewpt
	var isnewpt = ""+$("#elem_flgIsPtNew").val();
	params += "&elem_flgIsPtNew="+isnewpt;

	//get visit_codes for superbill
	if($(":input[type=checkbox][id*=lb_qlfy_lvl_code]").length>0){
		var str_vc_check = "";
		$(":checked[id*=lb_qlfy_lvl_code]").each(function(ini){  str_vc_check += "&"+this.name+"="+this.value;  });
		params += str_vc_check;
	}else if(typeof(cptcd)!="undefined" && cptcd!=""){
		var type_cpt = "";
		if(cptcd.indexOf("992")!=-1){
			type_cpt = "992";
		}else if(cptcd.indexOf("920")!=-1){
			type_cpt = "920";
		}
		if(type_cpt != ""){
			params +="&lb_qlfy_lvl_code"+type_cpt+"="+cptcd;
		}
	}
	//ICD10:
	var v_icd10 = ""+$("#hid_icd10").val();
	params += "&elem_v_icd10="+v_icd10;

	return params;
}


///in this we will not autopopulate superbill codes but will display assessed service level pop up only
var flg_checkAssocPqri4All_checkvisitcode=0; //this will set if request is from this method
function checkAssocPqri4All_checkvisitcode(cptcd){
	if(cptcd==""){return;}
	if(cptcd.indexOf("920")!=-1){
		type_cpt = "920";
	}else if(cptcd.indexOf("992")!=-1){
		type_cpt = "992";
	}else{
		return;
	}

	///pt type in code
	var isnewpt="Establish";
	if(typeof(cptcd)!="undefined" && cptcd!=""){
		var isnewpt_t = cptcd.substr(-2, 1);
		isnewpt = (isnewpt_t==0) ? "Yes_confirmed":"No_confirmed";
		$("#elem_flgIsPtNew").val(isnewpt); //this will set patient type confirmed without asking user
	}

	//decide which function to call
	flg_checkAssocPqri4All_checkvisitcode=cptcd;

	//--
	//prompt msg
	var prompt_msg="";
	//check New Patient confirm
	if($("#elem_flgIsPtNew").val()=="New"){

		var ctop=top;
		var stop="top";
		if(top.fmain){
			ctop = top.fmain;
			stop="top.fmain";
		}

		prompt_msg += "<label style=\"width:250px;display:inline-block;\"><b>Is this a New Patient?</b></label><div id=\"divisnwpt\">"+
				"<input type=\"checkbox\" name=\"el_sb_pt_type\" id=\"el_sb_pt_type_new\" value=\"Yes_confirmed\" onclick=\""+stop+".decideNewPatient(this)\"><label for=\"el_sb_pt_type_new\">Yes</label>"+
				"<input type=\"checkbox\" name=\"el_sb_pt_type\" id=\"el_sb_pt_type_estb\" value=\"No_confirmed\" onclick=\""+stop+".decideNewPatient(this)\"><label for=\"el_sb_pt_type_estb\">No</label></div>";

		/*
		var title = "New Patient Prompt";
		var btn1 = "0";
		var btn2 = "0";
		var func = stop+".decideNewPatient";
		var oPDiv = ctop.displayConfirmYesNo_v2(title,msg,btn1,btn2,func,0,1,new Array({"Yes":"Yes_confirmed","No":"No_confirmed"}));
		var pTop = 200;
		var pLeft = 300;
		oPDiv.style.top = pTop+"px";
		oPDiv.style.left = pLeft+"px";
		return;
		*/
	}

	//Medical Complexity--
	/*
	var flgMdc=true;
	if(typeof(flg_medComp) != "undefined"){
		if(flg_medComp == "1"){
			flgMdc=false;
		}
	}*/


	//Check for Medical Decision Complexity
	//Only For E/M codes OR if consult type
	var oPrBillCd = gebi("elem_practiceBillCode");
	//var oCCHx = gebi("elem_ccHx");
	//|| oCCHx && ""+oCCHx.value.toLowerCase().indexOf("referred by")!=-1)

	//if(flgMdc && oPrBillCd && $.trim(oPrBillCd.value) == "992"){
	if(oPrBillCd && $.trim(oPrBillCd.value) == "992"){
		var oComp = gebi("elem_levelComplexity");
		var opov = gebi("elem_proc_only_visit");
		var opopv = gebi("elem_post_op_visit");
		///if( oComp) { oComp.value = ""; } //this is OK donot change it!
		if( oComp && $.trim(oComp.value) == "" && (opov && $.trim(opov.value) == "") && (opopv && $.trim(opopv.value) == "") ){
			//Empty then Prompt for Mdc
			str_prmpt_tmp = promptMedDecCom(1);
			if(prompt_msg!=""){prompt_msg+="<br/>";}
			prompt_msg+=str_prmpt_tmp;
		}
	}

	//--

	//show prompt--
	if( prompt_msg!="" ){
		var htm = "<div id=\"dialog-Mdc-cum-new-pt\">"+
				"<div style=\"\">"+
				"<table class=\"table\"  height=\"30\"><tr class='purple_bar'><th>Assessed Service Level<span  class=\"glyphicon glyphicon-remove pull-right\" title=\"close\"></span></th></tr></table>"+
				prompt_msg+
				//"<input type=\"button\" id=\"el_mdc_pop_up\" value=\"Find Service Level\" class=\"dff_button\" style=\"float:right;margin-right:20px;\" >"+
				"</div>"+
				"<div id=\"div_as_srv_lvl\"></div>"+
				"</div>";
		$("#dialog-Mdc-cum-new-pt").remove();
		$("body").append(htm);

		//small pop up
		if($("#elem_practiceBillCode").val()=="920"){
			$("#dialog-Mdc-cum-new-pt").css({"width":"60%","left":"250px"});
		}

		//
		/*
		$("#dialog-Mdc-cum-new-pt #el_mdc_pop_up").bind("click", function(){
				var err_msg = "";

				if($("#dialog-Mdc-cum-new-pt input[type=checkbox][id*=el_sb_pt_type_]").length>0){
					if($("#dialog-Mdc-cum-new-pt :checked[id*=el_sb_pt_type_]").length<=0){
						err_msg += "Please confirm patient type.\n";
					}
				}
				if($("#dialog-Mdc-cum-new-pt input[type=checkbox][id*=elem_chkMedComp_]").length>0){
					if($("#dialog-Mdc-cum-new-pt :checked[id*=elem_chkMedComp_]").length<=0){
						err_msg += "Please choose Medical Decision Complexity.\n";
					}
				}

				if(err_msg != ""){
					alert(err_msg);
				}else{
					checkAssocPqri4All();
				}
			});
		*/
		//
		$("#dialog-Mdc-cum-new-pt").draggable({"handle":"th"});
		$("#dialog-Mdc-cum-new-pt th span[title=close]").bind("click", function(){$("#dialog-Mdc-cum-new-pt").remove();});
		$("#dialog-Mdc-cum-new-pt th").css({"cursor":"move"});

		return;
	}
	//--
	//--


	var imgPath_tmp = (typeof(imgPath_remote)!="undefined") ? imgPath_remote : imgPath;
	var url = imgPath_tmp+"/interface/chart_notes/requestHandler.php";
	var params = checkAssocPqri4All_getParam(cptcd);
	params += "&el_manual_insert_visitcode=1";
	//Set P image
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","superbill");

	//console.log(params);

	$.post(url,params,
		function(data){
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","superbill");

			//console.log(data);

			var xmlDoc = data;

			//autocode
			var oAutoCode = xmlDoc.getElementsByTagName("autocode")[0];
			var oAutos = ( oAutoCode != null ) ? oAutoCode.childNodes : null ;
			var lenAutos = ( oAutos != null ) ? oAutos.length : 0 ;

			var ptcategory="";
			var practicebillcode="";
			var ptlevelhistory="";
			var ptlevelcomp="";
			var ptservicelevel = "";
			var ptcategory_consult = "";

			//Visit Code------
			var pt_levelofservice_eye = "";
			var pt_levelofservice_em = "";
			var pt_strexamdone_eye = "";
			var pt_strexamnotdone_eye = "";
			var pt_strexamdone_em = "";
			var pt_strexamnotdone_em = "";
			var poe_visit_code = "";
			var pls_sel_nq_em="", pls_sel_nq_eye="";
			//Visit Code------

			for(var i=0;i<lenAutos;i++){
				var oData = oAutos[i];
				if(oData.firstChild){
					if(oData.nodeName == "ptcategory"){
						ptcategory=""+oData.firstChild.data;
					}else if(oData.nodeName == "practicebillcode"){
						practicebillcode=""+oData.firstChild.data;
					}else if(oData.nodeName == "ptlevelhistory"){
						ptlevelhistory =""+oData.firstChild.data;
					}else if(oData.nodeName == "ptlevelcomp"){
						ptlevelcomp = ""+oData.firstChild.data;
					}else if(oData.nodeName == "ptservicelevel"){
						ptservicelevel = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_levelofservice_eye"){
						pt_levelofservice_eye = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_levelofservice_em"){
						pt_levelofservice_em = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_strexamdone_eye"){
						pt_strexamdone_eye = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_strexamnotdone_eye"){
						pt_strexamnotdone_eye = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_strexamdone_em"){
						pt_strexamdone_em = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_strexamnotdone_em"){
						pt_strexamnotdone_em = ""+oData.firstChild.data;
					}else if(oData.nodeName == "poe_visit_code"){
						poe_visit_code = ""+oData.firstChild.data;
					}else if(oData.nodeName == "ptcategory_consult"){
						ptcategory_consult = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pls_sel_nq_em"){
						pls_sel_nq_em = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pls_sel_nq_eye"){
						pls_sel_nq_eye = ""+oData.firstChild.data;
					}
				}
			}

			//set values in fields
			gebi("elem_category").value=ptcategory;
			gebi("elem_practiceBillCode").value=practicebillcode;
			gebi("elem_levelHistory").value=ptlevelhistory;
			gebi("elem_levelComplexity").value=ptlevelcomp;
			gebi("elem_levelOfService").value=ptservicelevel;
			gebi("elem_ptcategory_consult").value=ptcategory_consult;
			//Visit Code------
			gebi("elem_levelOfServiceEye").value=pt_levelofservice_eye;
			gebi("elem_levelOfServiceEM").value=pt_levelofservice_em;
			gebi("elem_strExmDoneEye").value=pt_strexamdone_eye;
			gebi("elem_strExmNotDoneEye").value=pt_strexamnotdone_eye;
			gebi("elem_strExmDoneEM").value=pt_strexamdone_em;
			gebi("elem_strExmNotDoneEM").value=pt_strexamnotdone_em;
			gebi("elem_poe_visit_code").value=poe_visit_code;
			gebi("elem_pls_sel_nq_em").value=pls_sel_nq_em;
			gebi("elem_pls_sel_nq_eye").value=pls_sel_nq_eye;
			//Visit Code------

			if(setVisitProcedure_nochk==0){//when manually added visit code, show pop up
			}else{
			//check if something is pending to do as per level selected, then show pop up
			var los_t = cptcd.substr(-1, 1);
			if(cptcd.indexOf("920")!=-1){
				if(gebi("elem_strExmNotDoneEye").value==""){ $("#dialog-Mdc-cum-new-pt").remove(); return;}

			}else if(cptcd.indexOf("992")!=-1){
				if(gebi("elem_strExmNotDoneEM").value==""){ $("#dialog-Mdc-cum-new-pt").remove(); return;}
			}
			}
			//---


			//--
			//prompt msg
			var prompt_msg="";

			//Check for Medical Decision Complexity
			//Only For E/M codes OR if consult type
			var oPrBillCd = gebi("elem_practiceBillCode");
			var oComp = gebi("elem_levelComplexity");
			var opov = gebi("elem_proc_only_visit");
			var opopv = gebi("elem_post_op_visit");
			//var oCCHx = gebi("elem_ccHx");
			//|| oCCHx && ""+oCCHx.value.toLowerCase().indexOf("referred by")!=-1)

			//if(flgMdc && oPrBillCd && $.trim(oPrBillCd.value) == "992"){

			if(oPrBillCd && $.trim(oPrBillCd.value) == "992"){

				if( oComp || opov || opopv){
					//Empty then Prompt for Mdc
					str_prmpt_tmp = promptMedDecCom(1);
					if(prompt_msg!=""){prompt_msg+="<br/>";}
					prompt_msg+=str_prmpt_tmp;
				}
			}

			//--

			//show prompt--
			if( prompt_msg!="" ){
				var htm = "<div id=\"dialog-Mdc-cum-new-pt\">"+
						"<div style=\"\">"+
						"<table class=\"table\" height=\"30\"><tr class='purple_bar'><th>Assessed Service Level<span class=\"glyphicon glyphicon-remove pull-right\" title=\"close\"></span></th></tr></table>"+
						prompt_msg+
						//"<input type=\"button\" id=\"el_mdc_pop_up\" value=\"Find Service Level\" class=\"dff_button\" style=\"float:right;margin-right:20px;\" >"+
						"</div>"+
						"<div id=\"div_as_srv_lvl\"></div>"+
						"</div>";
				if($("#dialog-Mdc-cum-new-pt").length>0){$("#dialog-Mdc-cum-new-pt").remove();}

				$("body").append(htm);

				//small pop up
				if($("#elem_practiceBillCode").val()=="920" && oPrBillCd=="920"){
					$("#dialog-Mdc-cum-new-pt").css({"width":"60%","left":"250px"});
				}

				//
				$("#dialog-Mdc-cum-new-pt").draggable({"handle":"th"});
				$("#dialog-Mdc-cum-new-pt th span[title=close]").bind("click", function(){$("#dialog-Mdc-cum-new-pt").remove();});
				$("#dialog-Mdc-cum-new-pt th").css({"cursor":"move"});

				if( (oComp && oComp.value !="") || (opov && opov.value !="") || (opopv && opopv.value !="") ){

					var tmp = (oComp.value =="") ? "5" : oComp.value;
					if(opov && opov.value !=""){ tmp = 5; }
					else if(opopv && opopv.value !=""){ tmp = 6; }

					$("input[id*=elem_chkMedComp_][value='"+tmp+"']").prop("checked", true);
				}
			}
			//--
			//--

			//set visit code
			confirmVisitCode_v3();

		}
		,"xml");

}

function setSBTodayCharges(){

	var oTodaysCharges = gebi("elem_todaysCharges");
	var oProcOrder = gebi("elem_procOrder");
	var oProcUnitOrder = gebi("elem_procUnitOrder");
	if( !oProcOrder || (typeof oProcOrder.value == "undefined") || ($.trim(oProcOrder.value) == "")){
		if( oTodaysCharges ){
			oTodaysCharges.value = "";
		}
		return;
	}

	var mod_50="";
	$("#superbill .modcode").each(function(){ if(typeof(this.value)!='undefined' && $.trim(this.value)=="50"){var cid = this.id.replace(/elem_modCode/,'elem_cptCode'); cid = cid.replace(/_\d$/,""); if($("#"+cid).length>0){var cpt=$("#"+cid).val(); if(cpt!=""){ if(mod_50.indexOf(cpt)==-1){ mod_50 +=""+cpt+","; }} }  } });

	var imgPath_tmp = (typeof(imgPath_remote)!="undefined") ? imgPath_remote : imgPath;
	var url = imgPath_tmp+"/interface/chart_notes/requestHandler.php";
	//var mstrId = document.getElementById("elem_masterId").value;
    var insCaseId = "";
    if(typeof gebi("elem_masterCaseId") != "undefined"){
        var insCaseId = gebi("elem_masterCaseId").value;
    }
    var vDos = "";
    if(typeof gebi("elem_dos") != "undefined"){
        var vDos = gebi("elem_dos").value;
    }
	var vVIP = ($("#vipSuperBill:checked").length>0) ? 1 : 0;

	params = "elem_formAction=todayCharges";
	//params += "&elem_formId="+mstrId;
	params += "&elem_insCaseId="+insCaseId;
	params += "&elem_strCptCodes="+oProcOrder.value;
	params += "&elem_dos="+vDos;
	params += "&elem_strCptUnits="+oProcUnitOrder.value;
	params += "&elem_vVIP="+vVIP;
	params += "&elem_mod50="+mod_50;
	//console.log("params: "+params);

	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","superbill");

	// test --
	$.post(url, params,
	   function(data){
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","superbill");
		str = ""+data;
		if( oTodaysCharges ){
				oTodaysCharges.value = ""+str;
		}
		$(".amount").html(""+str);
	});

	// test --

}

function getLSSHtml(obj,attr,type,asindx){
	var str_lss_tmp="";
	if(obj){
	var oLat=obj.attributes.getNamedItem(attr);
	vLat = (oLat) ? ""+oLat.nodeValue : "";
	var arrvLat = vLat.split(",");
	if(arrvLat.length>0){
	for(var vt in arrvLat){
		if(arrvLat[vt]!=""){
			var tmpLat = arrvLat[vt].split(" - ");//Test
			str_lss_tmp+="<input type=\"checkbox\" id=\"elem_asDx"+type+"_"+vt+"\" name=\"elem_asDx"+type+"\" value=\""+tmpLat[1]+"\" onclick=\"icd10_restdxcodesinpopup(this)\" style=\"display:none;\" data-asindx=\""+asindx+"\" >"+
					"<label style=\"cursor:pointer;color:purple;margin-left:10px;padding:2px;border:2px solid transparent;\" for=\"elem_asDx"+type+"_"+vt+"\">"+tmpLat[0]+"</label>&nbsp;";
			//alert(""+str_lss_tmp);
		}
	}
	}
	}
	return str_lss_tmp;
}

var sb_objRefraction={};
function checkAssocPqri4All(){

	//console.log("IN 0");

	flg_checkAssocPqri4All_checkvisitcode=0;//

	//prompt msg
	var prompt_msg="";
	//check New Patient confirm
	if($("#elem_flgIsPtNew").val()=="New"){

		var ctop=top;
		var stop="top";
		if(top.fmain){
			ctop = top.fmain;
			stop="top.fmain";
		}

		prompt_msg += "<label style=\"width:250px;display:inline-block;\"><b>Is this a New Patient?</b></label><div id=\"divisnwpt\">"+
				"<input type=\"checkbox\" name=\"el_sb_pt_type\" id=\"el_sb_pt_type_new\" value=\"Yes_confirmed\" onclick=\""+stop+".decideNewPatient(this)\"><label for=\"el_sb_pt_type_new\">Yes</label>"+
				"<input type=\"checkbox\" name=\"el_sb_pt_type\" id=\"el_sb_pt_type_estb\" value=\"No_confirmed\" onclick=\""+stop+".decideNewPatient(this)\"><label for=\"el_sb_pt_type_estb\">No</label></div>";

		/*
		var title = "New Patient Prompt";
		var btn1 = "0";
		var btn2 = "0";
		var func = stop+".decideNewPatient";
		var oPDiv = ctop.displayConfirmYesNo_v2(title,msg,btn1,btn2,func,0,1,new Array({"Yes":"Yes_confirmed","No":"No_confirmed"}));
		var pTop = 200;
		var pLeft = 300;
		oPDiv.style.top = pTop+"px";
		oPDiv.style.left = pLeft+"px";
		return;
		*/
	}

	//Medical Complexity--
	/*
	var flgMdc=true;
	if(typeof(flg_medComp) != "undefined"){
		if(flg_medComp == "1"){
			flgMdc=false;
		}
	}*/


	if($("#poeModal").length<=0){ //when poe is set, mdc is not needed
		//Check for Medical Decision Complexity
		//Only For E/M codes OR if consult type
		var oPrBillCd = gebi("elem_practiceBillCode");
		//var oCCHx = gebi("elem_ccHx");
		//|| oCCHx && ""+oCCHx.value.toLowerCase().indexOf("referred by")!=-1)



		//if(flgMdc && oPrBillCd && $.trim(oPrBillCd.value) == "992"){
		if(oPrBillCd && $.trim(oPrBillCd.value) == "992"){
			var oComp = gebi("elem_levelComplexity");
			var opov = gebi("elem_proc_only_visit");
			var opopv = gebi("elem_post_op_visit");
			if( (oComp && $.trim(oComp.value) == "") && (opov && $.trim(opov.value) == "") && (opopv && $.trim(opopv.value) == "") ){
				//Empty then Prompt for Mdc
				str_prmpt_tmp = promptMedDecCom(1);
				if(prompt_msg!=""){prompt_msg+="<br/>";}
				prompt_msg+=str_prmpt_tmp;

			}

		}
	}
	//--

	//show prompt--
	if( prompt_msg!="" ){

		var htm = "<div id=\"dialog-Mdc-cum-new-pt\">"+
				"<div style=\"padding:0px;\">"+
				"<table class=\"table\" height=\"30\"><tr class='purple_bar'><th>Assessed Service Level<span class=\"glyphicon glyphicon-remove pull-right\" title=\"close\"></span></th></tr></table>"+
				prompt_msg+
				//"<input type=\"button\" id=\"el_mdc_pop_up\" value=\"Find Service Level\" class=\"dff_button\" style=\"float:right;margin-right:20px;\" >"+
				"</div>"+
				"<div id=\"div_as_srv_lvl\" ></div>"+
				"</div>";
		$("#dialog-Mdc-cum-new-pt").remove();
		$("body").append(htm);

		//small pop up
		if($("#elem_practiceBillCode").val()=="920"){
			$("#dialog-Mdc-cum-new-pt").css({"width":"60%","left":"250px"});
		}

		//
		/*
		$("#dialog-Mdc-cum-new-pt #el_mdc_pop_up").bind("click", function(){
				var err_msg = "";

				if($("#dialog-Mdc-cum-new-pt input[type=checkbox][id*=el_sb_pt_type_]").length>0){
					if($("#dialog-Mdc-cum-new-pt :checked[id*=el_sb_pt_type_]").length<=0){
						err_msg += "Please confirm patient type.\n";
					}
				}
				if($("#dialog-Mdc-cum-new-pt input[type=checkbox][id*=elem_chkMedComp_]").length>0){
					if($("#dialog-Mdc-cum-new-pt :checked[id*=elem_chkMedComp_]").length<=0){
						err_msg += "Please choose Medical Decision Complexity.\n";
					}
				}

				if(err_msg != ""){
					alert(err_msg);
				}else{
					checkAssocPqri4All();
				}
			});
		*/
		//
		$("#dialog-Mdc-cum-new-pt").draggable({"handle":"th"});
		$("#dialog-Mdc-cum-new-pt th span[title=close]").bind("click", function(){$("#dialog-Mdc-cum-new-pt").remove();});
		$("#dialog-Mdc-cum-new-pt th").css({"cursor":"move"});

		return;
	}
	//--

	var imgPath_tmp = (typeof(imgPath_remote)!="undefined") ? imgPath_remote : imgPath;
	var url = imgPath_tmp+"/interface/chart_notes/requestHandler.php";
	var params = checkAssocPqri4All_getParam();

	//params += "&lb_qlfy_lvl_code992=99213";

	//alert(params);
	//console.log("IN 1");

	//Set P image
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","superbill");

	$.post(url,params,
		function(data){

			//console.log("IN 2");

			//DxCode
			//Debugging

				//var  dw = window.open("", "ss", "width=200,height=200, resizable=1");
				//data = ""+data.replace(/</g,"&lt;").replace(/>/g,"&gt;");
				//dw.document.write(data);
				///
				if($("textarea[name=commentsForPatient]").val()=="debug123"){	console.log(data);  }

				//var xmlDoc = data;
				//document.writeln(""+xmlDoc);
				//document.writeln(""+data.replace(/</g,"&lt;").replace(/>/g,"&gt;"));
				//alert(""+xmlDoc);
				//document.close();
				//alert("1");
			//Debugging

			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","superbill");

			var xmlDoc = data;
			var arrCptCodes=new Array();
			var arrDxCodes=new Array();
			var arrExamDone=new Array();
			var arrExamTesting=new Array();


			//Refraction
			sb_objRefraction.isRef=0;

			//Testing Codes : superbill in test will overwrite auto code dx codes --
			var tsb_ids = "";
			var oTestSbInfo = xmlDoc.getElementsByTagName("testing_sb");
			var oTSB = (oTestSbInfo!=null&&oTestSbInfo[0]&&oTestSbInfo[0].childNodes) ? oTestSbInfo[0].childNodes : null;
			var lenTSB = (oTSB !=null) ? oTSB.length : 0 ;
			for(var a=0;a<lenTSB;a++){
				var oSBNode = oTSB[a];
				if(oSBNode==null)continue;
				//Get sb id
				var osb_id=oSBNode.attributes.getNamedItem("id");
				if(osb_id) tsb_ids += ""+osb_id.nodeValue+",";

				var oCpts = oSBNode.childNodes;
				var lenCpts = (oCpts) ? oCpts.length : 0;
				for(var b=0;b<lenCpts;b++){
					var arrTmp = new Array();
					var oCptNode = oCpts[b];
					var oCptData = oCptNode.childNodes;
					var lenCptData = oCptData.length;
					for(var c=0;c<lenCptData;c++){
						var oData = oCptData[c];
						var tmpCpt="";
						if(oData.firstChild){
							if(oData.nodeName == "code"){ //CPT Code
								//var tmpStr = (arrCptCodes.length > 0) ? arrCptCodes.join(",") : "" ;
								//if(tmpStr.indexOf(oData.data) == -1){
									//arrCptCodes[arrCptCodes.length] = oData.firstChild.data;
									arrTmp["cpt"]=""+oData.firstChild.data;
									tmpCpt=""+oData.firstChild.data;
									//Desc
									var oCptDesc=oData.attributes.getNamedItem("desc");
									arrTmp["cptDesc"] = (typeof oCptDesc != "undefined") ? oCptDesc.nodeValue : "";
									//Units
									var oUnits=oData.attributes.getNamedItem("units");
									arrTmp["units"] = (typeof oUnits != "undefined") ? oUnits.nodeValue : "";
									arrTmp["norepeat"]="1";
									//isref
									var oIsRef=oData.attributes.getNamedItem("isRef");
									if(oIsRef&&typeof(oIsRef.nodeValue)!="undefined"){
										arrTmp["isRef"]=""+oIsRef.nodeValue;
									}
									var o_valid_dx_code=oData.attributes.getNamedItem("valid_dxcodes");
									if(o_valid_dx_code&&typeof(o_valid_dx_code.nodeValue)!="undefined"){
										arrTmp["valid_dxcodes"]=""+o_valid_dx_code.nodeValue;
									}
								//}
							}else if(oData.nodeName == "dx"){
								//num
								var oDxNum=oData.attributes.getNamedItem("num");
								var oDxId=oData.attributes.getNamedItem("dxid");

								var dxId = (typeof oDxNum != "undefined" && oDxNum) ? oDxNum.nodeValue : "";
								var dxCdId = (typeof oDxId != "undefined" && oDxId) ? oDxId.nodeValue : "";
								if(dxId != ""){
									var idDx = "dx"+dxId;
									arrTmp[idDx]=""+oData.firstChild.data;

									//Desc
									var oDxDesc=oData.attributes.getNamedItem("desc");
									arrTmp[idDx+"Desc"] = (typeof oDxDesc != "undefined") ? oDxDesc.nodeValue : "";
									//dxid
									arrTmp[idDx+"Id"] = (typeof dxCdId != "undefined") ? dxCdId : "";
								}
							}else if(oData.nodeName == "modifier"){
								//num
								var oModNum=oData.attributes.getNamedItem("num");
								var modId = (typeof oModNum != "undefined") ? oModNum.nodeValue : "";
								if(modId != ""){
									var idMod = "modifier"+modId;
									arrTmp[""+idMod]=""+oData.firstChild.data;
								}
							}
						}
					}

					if((typeof arrTmp["cpt"] != "undefined") && (arrTmp["cpt"] != "")){
						//alert(arrTmp["cpt"]+"\n"+arrTmp["modifier"]+"\n"+arrTmp["units"]);
						arrExamDone[arrExamDone.length] = arrTmp;

						//Refraction--
						if(arrTmp["isRef"]=="1"){
							sb_objRefraction.isRef=1;
							sb_objRefraction.dxCode=""+arrTmp["dx1"];
						}
						//Refraction--
					}
				}
			}

			//test sb ids
			if(tsb_ids != ""){
				tsb_ids = tsb_ids.replace(/,\s*$/g,"");
				gebi("elem_sb_tsbIds").value = ""+tsb_ids;
			}

			//Testing Codes --

			//allcptcode
			var oCptInfo = xmlDoc.getElementsByTagName("allcptcode")[0];
			var oCpts = (oCptInfo!=null) ? oCptInfo.childNodes : null;
			var lenCpts = (oCpts !=null) ? oCpts.length : 0 ;
			for(var i=0;i<lenCpts;i++){

				var arrTmp = new Array();
				var oCptNode = oCpts[i];
				var oCptData = oCptNode.childNodes;
				var lenCptData = oCptData.length;
				for(var j=0;j<lenCptData;j++){

					var oData = oCptData[j];
					var tmpCpt="";
					if(oData.firstChild){
						if(oData.nodeName == "cptcode"){
							//var tmpStr = (arrCptCodes.length > 0) ? arrCptCodes.join(",") : "" ;
							//if(tmpStr.indexOf(oData.data) == -1){
								//arrCptCodes[arrCptCodes.length] = oData.firstChild.data;
								arrTmp["cpt"]=""+oData.firstChild.data;
								tmpCpt=""+oData.firstChild.data;
								//Desc
								var oCptDesc=oData.attributes.getNamedItem("desc");
								arrTmp["cptDesc"] = (typeof oCptDesc != "undefined") ? oCptDesc.nodeValue : "";
								arrTmp["norepeat"]="0";
								//isref
								var oIsRef=oData.attributes.getNamedItem("isRef");
								if(oIsRef&&typeof(oIsRef.nodeValue)!="undefined"){
									arrTmp["isRef"]=""+oIsRef.nodeValue;
								}
								var o_valid_dx_code=oData.attributes.getNamedItem("valid_dxcodes");
								if(o_valid_dx_code&&typeof(o_valid_dx_code.nodeValue)!="undefined"){
									arrTmp["valid_dxcodes"]=""+o_valid_dx_code.nodeValue;
								}
							//}
						}
						else if(oData.nodeName == "units"){
							//arrUnits[tmpCpt] = oData.firstChild.data;
							arrTmp["units"]=""+oData.firstChild.data;
						}
						else if(oData.nodeName == "modifier1") {
							//alert("modifier: "+oData.firstChild.data)
							//arrModifiers[tmpCpt] = oData.firstChild.data;
							arrTmp["modifier1"]=""+oData.firstChild.data;
						}else if(oData.nodeName == "modifier2"){
							arrTmp["modifier2"]=""+oData.firstChild.data;
						}else if(oData.nodeName == "modifier3"){
							arrTmp["modifier3"]=""+oData.firstChild.data;
						}else if(oData.nodeName == "modifier4"){
							arrTmp["modifier4"]=""+oData.firstChild.data;
						}else if(oData.nodeName == "dx1" || oData.nodeName == "dx2" ||
									oData.nodeName == "dx3" || oData.nodeName == "dx4" ||
									oData.nodeName == "dx5" || oData.nodeName == "dx6" ||
									oData.nodeName == "dx7" || oData.nodeName == "dx8" ||
									oData.nodeName == "dx9" || oData.nodeName == "dx10" ||
									oData.nodeName == "dx11" || oData.nodeName == "dx12"
						){
							var idDx = oData.nodeName;
							arrTmp[idDx]=""+oData.firstChild.data;
							//Desc
							var oDxDesc=oData.attributes.getNamedItem("desc");
							arrTmp[idDx+"Desc"] = ""+(typeof oDxDesc != "undefined") ? oDxDesc.nodeValue : "";
						}else if(oData.nodeName == "adx1" || oData.nodeName == "adx2" ||
									oData.nodeName == "adx3" || oData.nodeName == "adx4"
						){
							var idDx = oData.nodeName;
							arrTmp[idDx]=""+oData.firstChild.data;
							//Desc
							var oDxDesc=oData.attributes.getNamedItem("desc");
							arrTmp[idDx+"Desc"] = ""+(typeof oDxDesc != "undefined") ? oDxDesc.nodeValue : "";
						}
					}
				}

				//arrExamDone
				if((typeof arrTmp["cpt"] != "undefined") && (arrTmp["cpt"] != "")){
					//alert(arrTmp["cpt"]+"\n"+arrTmp["modifier"]+"\n"+arrTmp["units"]);
					arrExamDone[arrExamDone.length] = arrTmp;

					//Refraction--
					if(arrTmp["isRef"]=="1"){
						sb_objRefraction.isRef=1;
						sb_objRefraction.dxCode=""+arrTmp["dx1"];
					}
					//Refraction--
				}
			}

			//Procedure Code --
			var arProcDxInfo = [];
			var oProcSbInfo = xmlDoc.getElementsByTagName("proc_sb");
			/*
			var oProcCptInfo=null;
			if(oProcSbInfo!=null&&oProcSbInfo[0]&&oProcSbInfo[0].childNodes){
				oProcCptInfo = xmlDoc.getElementsByTagName("cpt_info");
			}

			alert(oProcCptInfo);
			*/
			//var oPSB = (oProcCptInfo!=null&&oProcCptInfo[0]&&oProcCptInfo[0].childNodes) ? oProcCptInfo[0].childNodes : null;
			var oPSB = (oProcSbInfo!=null&&oProcSbInfo[0]&&oProcSbInfo[0].childNodes) ? oProcSbInfo[0].childNodes : null;
			var lenPSB = (oPSB !=null) ? oPSB.length : 0 ;
			for(var a=0;a<lenPSB;a++){
				var oSBNode = oPSB[a];
				if(oSBNode==null)continue;

				if(oSBNode.tagName == "dx_info"){
					var oDxs = oSBNode.childNodes;
					var lenDxs = (oDxs) ? oDxs.length : 0;
					for(var b=0;b<lenDxs;b++){
						var oDxNode = oDxs[b];
						var tmpDx = ""+oDxNode.firstChild.data;
						var tmpDxDesc=oDxNode.attributes.getNamedItem("desc");

						if(tmpDx!=""){
							arProcDxInfo[tmpDx]=""+(typeof tmpDxDesc != "undefined") ? tmpDxDesc.nodeValue : "";
						}
					}

				}else{

					var oCpts = oSBNode.childNodes;
					var lenCpts = (oCpts) ? oCpts.length : 0;
					for(var b=0;b<lenCpts;b++){
						var arrTmp = new Array();
						var oCptNode = oCpts[b];
						var oCptData = oCptNode.childNodes;
						var lenCptData = oCptData.length;
						for(var c=0;c<lenCptData;c++){
							var oData = oCptData[c];
							var tmpCpt="";
							if(oData.firstChild){
								if(oData.nodeName == "code"){ //CPT Code
									//var tmpStr = (arrCptCodes.length > 0) ? arrCptCodes.join(",") : "" ;
									//if(tmpStr.indexOf(oData.data) == -1){
										//arrCptCodes[arrCptCodes.length] = oData.firstChild.data;
										arrTmp["cpt"]=""+oData.firstChild.data;
										tmpCpt=""+oData.firstChild.data;
										//Desc
										var oCptDesc=oData.attributes.getNamedItem("desc");
										arrTmp["cptDesc"] = (typeof oCptDesc != "undefined") ? oCptDesc.nodeValue : "";
										//Units
										var oUnits=oData.attributes.getNamedItem("units");
										arrTmp["units"] = (typeof oUnits != "undefined") ? oUnits.nodeValue : "";
										arrTmp["norepeat"]="1";
										//isref
										var oIsRef=oData.attributes.getNamedItem("isRef");
										if(oIsRef&&typeof(oIsRef.nodeValue)!="undefined"){
											arrTmp["isRef"]=""+oIsRef.nodeValue;
										}
										var o_valid_dx_code=oData.attributes.getNamedItem("valid_dxcodes");
										if(o_valid_dx_code&&typeof(o_valid_dx_code.nodeValue)!="undefined"){
											arrTmp["valid_dxcodes"]=""+o_valid_dx_code.nodeValue;
										}
									//}
								}else if(oData.nodeName == "dx"){
									//num
									var oDxNum=oData.attributes.getNamedItem("num");
									var oDxId=oData.attributes.getNamedItem("dxid");
									var dxId = (typeof oDxNum != "undefined" && oDxNum) ? ""+oDxNum.nodeValue : "";
									var dxCdId = (typeof oDxId != "undefined" && oDxId) ? ""+oDxId.nodeValue : "";
									if(dxId != ""){
										var idDx = "dx"+dxId;
										arrTmp[idDx]=""+oData.firstChild.data;

										//Desc
										var oDxDesc=oData.attributes.getNamedItem("desc");
										arrTmp[idDx+"Desc"] = (typeof oDxDesc != "undefined") ? ""+oDxDesc.nodeValue : "";

										//dxid
										arrTmp[idDx+"Id"] = (typeof dxCdId != "undefined") ? dxCdId : "";
									}
								}else if(oData.nodeName == "modifier"){
									//num
									var oModNum=oData.attributes.getNamedItem("num");
									var modId = ""+(typeof oModNum != "undefined") ? oModNum.nodeValue : "";
									if(modId != ""){
										var idMod = "modifier"+modId;
										arrTmp[""+idMod]=""+oData.firstChild.data;
									}
								}
							}
						}

						if((typeof arrTmp["cpt"] != "undefined") && (arrTmp["cpt"] != "")){
							//alert(arrTmp["cpt"]+"\n"+arrTmp["modifier"]+"\n"+arrTmp["units"]);
							arrExamDone[arrExamDone.length] = arrTmp;

							//Refraction--
							if(arrTmp["isRef"]=="1"){
								sb_objRefraction.isRef=1;
								sb_objRefraction.dxCode=""+arrTmp["dx1"];
							}
							//Refraction--
						}
					}
				}//tagname == cpt_info
			}
			//Procedure Code --

			//autocode
			var oAutoCode = xmlDoc.getElementsByTagName("autocode")[0];
			var oAutos = ( oAutoCode != null ) ? oAutoCode.childNodes : null ;
			var lenAutos = ( oAutos != null ) ? oAutos.length : 0 ;

			var ptcategory="";
			var practicebillcode="";
			var ptlevelhistory="";
			var ptlevelcomp="";
			var ptservicelevel = "";
			var ptcategory_consult = "";

			//Visit Code------
			var pt_levelofservice_eye = "";
			var pt_levelofservice_em = "";
			var pt_strexamdone_eye = "";
			var pt_strexamnotdone_eye = "";
			var pt_strexamdone_em = "";
			var pt_strexamnotdone_em = "";
			var poe_visit_code = "";
			var pls_sel_nq_em="", pls_sel_nq_eye="";
			//Visit Code------

			for(var i=0;i<lenAutos;i++){
				var oData = oAutos[i];
				if(oData.firstChild){
					if(oData.nodeName == "ptcategory"){
						ptcategory=""+oData.firstChild.data;
					}else if(oData.nodeName == "practicebillcode"){
						practicebillcode=""+oData.firstChild.data;
					}else if(oData.nodeName == "ptlevelhistory"){
						ptlevelhistory =""+oData.firstChild.data;
					}else if(oData.nodeName == "ptlevelcomp"){
						ptlevelcomp = ""+oData.firstChild.data;
					}else if(oData.nodeName == "ptservicelevel"){
						ptservicelevel = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_levelofservice_eye"){
						pt_levelofservice_eye = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_levelofservice_em"){
						pt_levelofservice_em = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_strexamdone_eye"){
						pt_strexamdone_eye = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_strexamnotdone_eye"){
						pt_strexamnotdone_eye = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_strexamdone_em"){
						pt_strexamdone_em = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pt_strexamnotdone_em"){
						pt_strexamnotdone_em = ""+oData.firstChild.data;
					}else if(oData.nodeName == "poe_visit_code"){
						poe_visit_code = ""+oData.firstChild.data;
					}else if(oData.nodeName == "ptcategory_consult"){
						ptcategory_consult = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pls_sel_nq_em"){
						pls_sel_nq_em = ""+oData.firstChild.data;
					}else if(oData.nodeName == "pls_sel_nq_eye"){
						pls_sel_nq_eye = ""+oData.firstChild.data;
					}
				}
			}

			//Debugging
				//ptcategory = "New"
			//Debugging

			//set values in fields
			gebi("elem_category").value=ptcategory;
			gebi("elem_practiceBillCode").value=practicebillcode;
			gebi("elem_levelHistory").value=ptlevelhistory;
			gebi("elem_levelComplexity").value=ptlevelcomp;
			gebi("elem_levelOfService").value=ptservicelevel;
			gebi("elem_ptcategory_consult").value=ptcategory_consult;
			//Visit Code------
			gebi("elem_levelOfServiceEye").value=pt_levelofservice_eye;
			gebi("elem_levelOfServiceEM").value=pt_levelofservice_em;
			gebi("elem_strExmDoneEye").value=pt_strexamdone_eye;
			gebi("elem_strExmNotDoneEye").value=pt_strexamnotdone_eye;
			gebi("elem_strExmDoneEM").value=pt_strexamdone_em;
			gebi("elem_strExmNotDoneEM").value=pt_strexamnotdone_em;
			gebi("elem_poe_visit_code").value=poe_visit_code;
			gebi("elem_pls_sel_nq_em").value=pls_sel_nq_em;
			gebi("elem_pls_sel_nq_eye").value=pls_sel_nq_eye;
			//Visit Code------

			//ElemInfo
			var oDxInfo = xmlDoc.getElementsByTagName("eleminfo")[0];
			var oElems = ( oDxInfo != null ) ? oDxInfo.childNodes : null ;
			var lenElems = ( oElems != null ) ? oElems.length : 0 ;
			var arrDxInfo = new Array();

			//var strProcs = document.getElementById().value;
			for(var i=0;i<lenElems;i++){
				var oPqri = oElems[i].childNodes;
				var lenPqri = oPqri.length;
				var thisDxCode = "";
				var tmpCptCode = new Array();

				for(var j=0;j<lenPqri;j++){
					var node = oPqri[j];
					if(node.nodeName == "dxcode"){
						thisDxCode = (node.firstChild) ? node.firstChild.data : "" ;
						//desc
						var oDxDesc=node.attributes.getNamedItem("desc");
						arrDxInfo[thisDxCode] = (typeof oDxDesc != "undefined") ? ""+oDxDesc.nodeValue : "";
					}
					else if(node.nodeName == "pqri"){
						var cptNodes = node.childNodes;
						var lencptNodes = cptNodes.length;
						for(var k=0;k<lencptNodes;k++){
							var tmpCpt = cptNodes[k].firstChild;
							if(tmpCpt) {
								var tmpStr = (tmpCptCode.length > 0) ? tmpCptCode.join(",") : "" ;
								if(tmpStr.indexOf(tmpCpt.data) == -1){
									tmpCptCode[tmpCptCode.length] = tmpCpt.data;
								}
								//arrDxCodes[arrDxCodes.length] = thisDxCode;
							}
							//alert("i: "+i+" \n NodeName: "+node.nodeName+" \n cptNodes: "+cptNodes.length+"\ncptCode: "+cptNodes[k].firstChild.data);
						}
					}

				}

				if(tmpCptCode.length > 0){
					arrCptCodes[thisDxCode] = tmpCptCode;
				}
				//break;
			}

			//remove rows if procedure only visit so that it starts from first row
			if($("#elem_proc_only_visit").val()=="1"){ opRemAllCptRow(3); }

			//Add Exams Done
			insertExamsDoneSB(arrExamDone);

			//Set Pqri
			PqriSetter(arrCptCodes);

			//set assisst button visible
			var oAssist = gebi("elem_buttonAssist");
			if(oAssist){
				oAssist.style.visibility = "visible";
			}


			//Confirm New Patient
			/*
			if((ptcategory == "New" || (ptcategory == "Consult" && ptcategory_consult=="New"))){


				var ctop=top;
				var stop="top";
				if(top.fmain){
					ctop = top.fmain;
					stop="top.fmain";
				}

				if($("#elem_flgIsPtNew").val()=="Yes_confirmed" || $("#elem_flgIsPtNew").val()=="No_confirmed"){

					var v_flgIsPtNew=($("#elem_flgIsPtNew").val()=="Yes_confirmed") ? "Yes": "No";

					if(ptcategory == "Consult" && ptcategory_consult=="New"){
						ctop.decideNewPatient_consult(v_flgIsPtNew);
					}else{

						ctop.decideNewPatient($("#elem_flgIsPtNew").val());
					}
				}else{

				var title = "New Patient Prompt";
				var msg = "is this a New Patient?<BR>";
				var btn1 = "0";
				var btn2 = "0";
				var func = (ptcategory == "Consult" && ptcategory_consult=="New") ? stop+".decideNewPatient_consult" : stop+".decideNewPatient";
				var oPDiv = ctop.displayConfirmYesNo_v2(title,msg,btn1,btn2,func,0,1,new Array({"Yes":"Yes","No":"No"}));
				var pTop = 200;
				var pLeft = 300;
				oPDiv.style.top = pTop+"px";
				oPDiv.style.left = pLeft+"px";

				}


			}else{
			*/

				//set visit code
				confirmVisitCode_v3();
			//}

			//CL Supply Total -----
			var ocstInfo = xmlDoc.getElementsByTagName("cl_supply_total")[0];
			if((ocstInfo != null) && (ocstInfo.firstChild)){
				var tmpCst = ocstInfo.firstChild.data;
				if(parseInt(tmpCst) > 0){
					var oTd_cst = gebi("td_clsupplytotal");
					if(oTd_cst){
						oTd_cst.innerHTML = "CL order: "+tmpCst;
					}
				}
			}
			//CL Supply Total -----

			//Set Dx Desc
			var strAllDx= "";
			var flgEmptyDx=0;
			for(var i=1;i<=12;i++){
				var oDxElem = gebi("elem_dxCode_"+i);
				if(oDxElem){
					var tmp = $.trim(oDxElem.value);
					if((typeof tmp != "undefined") && (tmp != "")){
						if(typeof arrDxInfo[tmp] != "undefined") {oDxElem.title = arrDxInfo[tmp];}
						strAllDx += tmp+", ";
					}else{
						oDxElem.title = "";
						flgEmptyDx=1;
					}
				}
			}

			///
			if(flgEmptyDx==1){
				//alert(arProcDxInfo.length+" - "+arProcDxInfo);
				for(var x in arProcDxInfo){
					//alert(x+" - "+arProcDxInfo[x]+""+strAllDx);
					if(strAllDx.indexOf(x) == -1){

						for(var i=1;i<=12;i++){
							var oDxElem = gebi("elem_dxCode_"+i);
							if(oDxElem){
								var tmp = $.trim(oDxElem.value);
								if((typeof tmp != "undefined") && (tmp != "")){
								}else{
									oDxElem.value=x;
									oDxElem.title = arProcDxInfo[x];
									break;
								}
							}
						}

						/*
						$("input[name*='elem_dxCode_']").each(function(){
							if(this.value==""){
								this.value = x;
								this.title = arProcDxInfo[x];

							}

							});
						*/
						/*
						if($("input[name*='elem_dxCode_'][value='']").length>0){
							//alert($("input[name*='elem_dxCode_'][value='']").get(0))
							$("input[name*='elem_dxCode_'][value='']").get(1).val(""+x).attr("title",""+arProcDxInfo[x]);
						}*/
					}
				}
			}

			//Set Menu Dx code
			sb_crt_dx_dropdown();

			/*
			for(x in arrCptCodes){
				alert(x+"\n"+arrCptCodes[x]);

			}*/

		}
		,"xml");

}

function icd10_pre_restdxcodesinpopup(len){

for(var h=0;h<=len;h++){
	for(var mg=0;mg<=10;mg++){
		var dx_code_id="elem_asDx"+h+"_"+mg;
		if($("#"+dx_code_id).length>0){
			if(typeof($("#"+dx_code_id).data("valbakdb")) != 'undefined' && $("#"+dx_code_id).data("valbakdb")!=null){
				var dx_code_db_val=$("#"+dx_code_id).data("valbakdb");
				var dx_code_cur_val=$("#"+dx_code_id).val();
				var dx_str_len=String(dx_code_db_val).length;
				var dx_sss_val="";
				//alert(dx_code_id);
				//alert(dx_str_len+'='+dx_code_db_val);
				for(var k=0;k<=dx_str_len;k++){
					var x=dx_code_cur_val[k];
					if(x=="-"){ x='Y';  }//missing
					//if(dx_code_db_val[k]!=dx_code_cur_val[k]){
					if(dx_code_db_val[k]!=x){
						dx_sss_val+=dx_code_cur_val[k]+',';
					}
				}


				//for diabetic code -- it is SEL
				var isDbtsCode=0;
				if(dx_str_len==8 && (dx_code_cur_val.indexOf("E10.")!=-1 || dx_code_cur_val.indexOf("E11.")!=-1)){
					isDbtsCode=1;

					// get missing values again only for dbts code --
					dx_sss_val="";
					for(var k=0;k<=dx_str_len;k++){
						var x=dx_code_cur_val[k];
						if(x=="-"){ x='0';  }//missing
						if(dx_code_db_val[k]!=x){
							dx_sss_val+=dx_code_cur_val[k]+',';
						}
					}
					//--

					//
					var arx = dx_sss_val.split(",");
					var arx_ln = arx.length;
					var dx_sss_val_tmp="";
					for(var t=0;t<arx_ln;t++){
						if(typeof(arx[t]) != "undefined" && arx[t]!=""){
							dx_sss_val_tmp = arx[t] +","+ dx_sss_val_tmp;
						}
					}
					if(dx_sss_val_tmp!=""){	dx_sss_val = dx_sss_val_tmp; }
				}
				//--

				var dx_sss_val_exp=dx_sss_val.split(',');
				//alert(dx_sss_val_exp.length);
				if(dx_sss_val_exp.length>2){
					for(var g=0;g<=10;g++){
						var chk_lat_id='elem_asDxLat'+h+'_'+mg+'_'+g;
						var chk_stg_id='elem_asDxStg'+h+'_'+mg+'_'+g;
						var chk_svr_id='elem_asDxSvr'+h+'_'+mg+'_'+g;
						//alert(dx_code_cur_val+'=='+chk_lat_id+'=='+dx_sss_val);
						if($("#"+chk_lat_id) && $("#"+chk_lat_id).val()==dx_sss_val_exp[0]){
							$("#"+chk_lat_id).prop("checked",true);
							$("label[for="+chk_lat_id+"]").css({"color":"Red","border":"2px solid blue","background-color":"white"});
						}else{
							if($("#"+chk_lat_id) && typeof(js_icd10_bilateral_arr[dx_code_db_val.toLowerCase()]) != 'undefined'){
								if(js_icd10_bilateral_arr[dx_code_db_val.toLowerCase()]==1 && $("#"+chk_lat_id).val()==3){
									$("label[for="+chk_lat_id+"]").css("display",'none');
								}
							}
						}
						if($("#"+chk_stg_id) && $("#"+chk_stg_id).val()==dx_sss_val_exp[1]){
							$("#"+chk_stg_id).prop("checked",true);
							$("label[for="+chk_stg_id+"]").css({"color":"Red","border":"2px solid blue","background-color":"white"});
						}


						//dibetes code --
						if(typeof(dx_sss_val_exp[2])!="undefined" && dx_sss_val_exp[2]!=""){
							if(isDbtsCode==1){
								if($("#"+chk_svr_id) && $("#"+chk_svr_id).val()==dx_sss_val_exp[2]){
									$("#"+chk_svr_id).prop("checked",true);
									$("label[for="+chk_svr_id+"]").css({"color":"Red","border":"2px solid blue","background-color":"white"});
								}
							}
							//dibetes code --
						}else{
							//normal case--
							if($("#"+chk_svr_id) && $("#"+chk_svr_id).val()==dx_sss_val_exp[1]){
								$("#"+chk_svr_id).prop("checked",true);
								$("label[for="+chk_svr_id+"]").css({"color":"Red","border":"2px solid blue","background-color":"white"});
							}
						}

					}
				}else{
					for(var g=0;g<=10;g++){
						var chk_lat_id='elem_asDxLat'+h+'_'+mg+'_'+g;
						var chk_stg_id='elem_asDxStg'+h+'_'+mg+'_'+g;
						var chk_svr_id='elem_asDxSvr'+h+'_'+mg+'_'+g;
						//alert(chk_lat_id);
						if($("#"+chk_lat_id) && $("#"+chk_lat_id).val()==dx_sss_val_exp[0]){
							$("#"+chk_lat_id).prop("checked",true);
							$("label[for="+chk_lat_id+"]").css({"color":"Red","border":"2px solid blue","background-color":"white"});
						}else{
							if($("#"+chk_lat_id) && typeof(js_icd10_bilateral_arr[dx_code_db_val.toLowerCase()]) != 'undefined'){
								if(js_icd10_bilateral_arr[dx_code_db_val.toLowerCase()]==1 && $("#"+chk_lat_id).val()==3){
									$("label[for="+chk_lat_id+"]").css("display",'none');
								}
							}
						}
						if($("#"+chk_stg_id) && $("#"+chk_stg_id).val()==dx_sss_val_exp[0]){
							$("#"+chk_stg_id).prop("checked",true);
							$("label[for="+chk_stg_id+"]").css({"color":"Red","border":"2px solid blue","background-color":"white"});
						}
						if($("#"+chk_svr_id) && $("#"+chk_svr_id).val()==dx_sss_val_exp[0]){
							$("#"+chk_svr_id).prop("checked",true);
							$("label[for="+chk_svr_id+"]").css({"color":"Red","border":"2px solid blue","background-color":"white"});
						}
					}
				}
			}
		}
	}

	/*var dx_code_lat = fldid.replace("elem_assessment_dxcode","elem_assessment");
	alert(dx_code_cur_val+'='+dx_code_db_val);*/

}

}

function disDbtsProlifOptions(){
	$("#dx_sel_tbl :checked[id*=elem_asDx][data-valbak]").each(function(){
			var x = $(this).val();
			if(typeof(x)!="undefined" && (x.indexOf("E10.")!=-1||x.indexOf("E11.")!=-1) && x.length==8){
				var id = $(this).attr("id");
				var ids = id.replace(/elem_asDx/, "elem_asDxSvr");
				var svr = 0;
				$("#dx_sel_tbl input[id*="+ids+"]").each(function(){ if(this.checked){ svr = this.value; }   });
				var flg = 0;//hide
				if(svr==0||svr==5){flg = 1;}//show
				ids = ids + "_3";
				if($("#"+ids).length>0 ){ //&& $("#"+ids).val() == 5
					var idst = id.replace(/elem_asDx/, "elem_asDxStg");
					$("#dx_sel_tbl label[for*="+idst+"]").each(function(){
						var a = $(this).html();
						if(a==2||a==3||a==4||a==5){  if(flg==0){$(this).hide();}else{$(this).show(); }}
					});
				}
			}
		});
}

function PqriSetter(arrPqriCodes){

	/// WORKING HERE --
	var strHtml = "";
	for(var z in arrPqriCodes){
		var tmpDx = z;
		var tmpCpt = arrPqriCodes[tmpDx];
		var len = tmpCpt.length;

		var lim =4;
		var cntr = lim+1;

		strHtml += "<div class=\"bgColor_pupil\"><b>Dx Code: "+tmpDx+"</b></div>";
		strHtml += "<table>";

		for(var i =0; i<len;i++){
			if(cntr > lim){
				strHtml += "<tr>";
				cntr = 1;
			}
			if(cntr <= lim){
			strHtml += "<td><input type=\"checkbox\" dx=\""+tmpDx+"\" name=\"elem_selPqri"+i+"\" value=\""+tmpCpt[i]+"\" onmousedown=\"stopClickBubble();\" >"+tmpCpt[i]+"</td>";
				cntr += 1;
			}
			if(cntr > lim){
				strHtml += "</tr>";
			}
		}

		strHtml += "</table>";
	}

	if(strHtml != ""){

		//Show Prompt to Choose From
		var oPromptDiv = gebi("divChoosePqriCodes");
		if(oPromptDiv){
			oPromptDiv.style.backgroundColor="#FFDDCC";
			oPromptDiv.style.width="300px";

		strHtml = "<table >"+
					"<tr><th >PQRI Codes</th></tr>"+
					"<tr><td>Select PQRI codes to display in Super Bill:-</td></tr>"+
					"<tr><td>"+strHtml+"</td></tr>";
		strHtml += "<tr><td style='text-align:center;'>"+
					"<input name=\"button\" type=\"button\" id=\"elem_pqricBtn\" class=\"dff_button\" "+
					"value=\"Insert\" onmousedown=\"stopClickBubble();\" onclick=\"getSelPQRICodes()\"> "+
					"<input name=\"button\" type=\"button\" id=\"elem_pqrixBtn\" class=\"dff_button\" "+
					"value=\"Cancel\" onmousedown=\"stopClickBubble();\" onclick=\"getSelPQRICodes(0)\">"+
					"</td></tr>";
		strHtml += "</table>";

		oPromptDiv.innerHTML = ""+strHtml;
		oPromptDiv.style.display = "block";

		var thisScrollTop = gebi("divWorkView").scrollTop;
		if(thisScrollTop == 0){
			//thisScrollTop = 700;
		}else{
			oPromptDiv.style.top = (thisScrollTop+100)+"px";
		}

		oPromptDiv.focus();
		stopClickBubble();
		}
	}


}

//get give LSS pop up
function get_dx_popup_lss(oAsInfo, funcname){

	funcname = (typeof(funcname)!="undefined" && funcname!="") ? funcname : "";

	var str = "";
	var cck = 0;
	var strUni="", strUniDesc="";
	var arrdxth=new Array();
	var len = oAsInfo.length;
	for(var i=0;i<len;i++){
		var tAss = oAsInfo[i];
		var name = tAss.getElementsByTagName("name")[0].firstChild.nodeValue;
		var asindx = tAss.getElementsByTagName("name")[0].attributes.getNamedItem("indx").nodeValue;
		var arrdx = tAss.getElementsByTagName("dx");
		var ln2 = arrdx.length;
		var tmp = "";
		var chk_str_lss_tmp = "";
		if(typeof arrdx[0] != "undefined"){
			chk_str_lss_tmp = getLSSHtml(arrdx[0],"strLat","Lat"+i+"_0",asindx);
		}
		var dup_dx_row=0;
		if(chk_str_lss_tmp.indexOf("RUL")!=-1 && ln2<2){
			ln2=2;
			dup_dx_row=1;
		}

		if(ln2 > 0){
			for(var k=0;k<ln2;k++){
				if(dup_dx_row>0){
					kf=0;
				}else{
					kf=k;
				}
				var tdx = arrdx[kf].firstChild.nodeValue;
				var odxcat=arrdx[kf].attributes.getNamedItem("cat");
				var vdxcat = (odxcat) ? " - "+odxcat.nodeValue : "";

				var odxid=arrdx[kf].attributes.getNamedItem("dxid");
				var vdxid = (odxid) ? ""+odxid.nodeValue : "";

				flgDisable = "";
				if(strUni.indexOf(tdx+name) == -1 || dup_dx_row>0){
					strUni += tdx+name+",";
				}else{
					//flgDisable = "disabled";
					continue;
				}

				var ck = "";

				/*if(dup_dx_row>0){
					if(k==0 && (cck<12) && (flgDisable != "disabled")) {
						ck = "checked";
						cck += 1;
					}
				}else{
					if((ln2 == 1) && (cck<12) && (flgDisable != "disabled")) {
						ck = "checked";
						cck += 1;
					}
				}*/

				if((cck<12) && (flgDisable != "disabled")) {
					ck = "checked";
					cck += 1;
				}
				//
				var str_lss="";
				var str_as_nm="";
				var str_as_sr_nm="";
				var tdx_db="";
				if(""+$("#hid_icd10").val() == "1"){
					str_as_nm=name;
					//lat
					var str_lss_tmp ="";
					str_lss_tmp = getLSSHtml(arrdx[kf],"strLat","Lat"+i+"_"+k, asindx);
					str_lss += "<td title=\"Laterality\">"+str_lss_tmp+"</td>";
					//Stg
					str_lss_tmp = getLSSHtml(arrdx[kf],"strStg","Stg"+i+"_"+k, asindx);
					str_lss += "<td title=\"Staging\">"+str_lss_tmp+"</td>";
					//Svr
					str_lss_tmp = getLSSHtml(arrdx[kf],"strSvr","Svr"+i+"_"+k, asindx);
					str_lss += "<td title=\"Severity\">"+str_lss_tmp+"</td>";

					str_as_sr_nm="<td>"+(i+1)+".</td>";

					//
					var odxicd10bak=arrdx[kf].attributes.getNamedItem("strICD10Dx");
					tdx_db = (odxicd10bak) ? ""+odxicd10bak.nodeValue : "" ;

				}
				var dx_code_box="";
				dx_code_box="<span class=\"checkbox\"><input type=\"checkbox\" id=\"elem_asDx"+i+"_"+k+"\" name=\"elem_asDx"+i+"_"+k+"\" value=\""+tdx+"\"  data-valbak=\""+tdx+"\"  data-valbakdb=\""+tdx_db+"\" data-asindx=\""+asindx+"\" data-dxid=\""+vdxid+"\"   "+ck+" "+flgDisable+" > <label for=\"elem_asDx"+i+"_"+k+"\">"+tdx+"</label>"+vdxcat+"</span>";
				/*if(str_lss.indexOf("RUL")!=-1 && ln2<2){
					dx_code_box +="&nbsp;<span id=\"elem_asDx"+i+"_"+k+"_os\" style=\"display:none;\"><input type=\"checkbox\" id=\"elem_asDx"+i+"_1\" name=\"elem_asDx"+i+"_1\" value=\""+tdx+"\"  data-valbak=\""+tdx+"\"  data-valbakdb=\""+tdx_db+"\"  "+flgDisable+" > <label for=\"elem_asDx"+i+"_1\">"+tdx+"</label>"+vdxcat+"</span>";
				}*/
				if(dup_dx_row>0 && k>0){
					tmp += "<tr style=\"display:none;\" id=\"elem_asDx"+i+"_"+k+"_tr\">";
				}else{
					tmp += "<tr id=\"elem_asDx"+i+"_"+k+"_tr\">";
				}
				tmp += str_as_sr_nm+"<td class=\"small\">"+str_as_nm+"</td>"+
						"<td>"+dx_code_box+"</td>"+
						str_lss+
						"</tr>";


			}
		}else{

			var str_as_nm="Enter Dx Code:";
			str_lss="";
			var str_as_sr_nm="";
			if(""+$("#hid_icd10").val() == "1"){
				str_as_nm=""+name;
				str_lss="<td></td><td></td><td></td>";
				str_as_sr_nm="<td>"+(i+1)+".</td>";
			}

			tmp += "<tr>"+str_as_sr_nm+"<td class=\"small\">"+str_as_nm+"</td>"+
						"<td>"+
						//"<input type=\"text\" name=\"elem_asDx"+i+"_0\" id=\"elem_asDx"+i+"_0\" value=\"\" class=\"dxCode\" size=\"6\" onmousedown=\"stopClickBubble();\" onblur=\"checkDXCodesChart(this);stopClickBubble();\" >"+
						//getSimpleMenuJs("elem_asDx"+i+"_0","menu_DxCodes",imgPath,"0","0",sb_pdiv)+
						"<input type=\"text\" placeholder=\"Enter Dx Code:\" name=\"elem_asDx"+i+"_0\" id=\"elem_asDx"+i+"_0\" data-asindx=\""+asindx+"\" value=\"\" class=\"dxallcodes form-control\" size=\"15\" onmousedown=\"stopClickBubble();\" onblur=\"checkDXCodesChart(this);stopClickBubble();\">"+
						"</td>"+str_lss+
						"</tr>";

			//Type ahead
			arrdxth[arrdxth.length]="elem_asDx"+i+"_0";

		}
		if($.trim(tmp) != ""){

			if($("#hid_icd10").val()==1){

				//str += "<tr><td colspan=\"2\"><b>"+name+"</b></td></tr>";
				str += ""+tmp;

			}else{

				str += "<tr><td colspan=\"2\"><b>"+name+"</b></td></tr>";
				str += ""+tmp;

			}

		}
	}

	if(str != ""){

		var oDivDxCode = document.getElementById("divChooseDxCodes");
		oDivDxCode.innerHTML = "";
		oDivDxCode.style.display = "block";
		//oDivDxCode.style.backgroundColor="#FFFFFF";
		oDivDxCode.style.width="300px";

		var clsstbl="";
		if(""+$("#hid_icd10").val() == "1"){
			str="<tr class=\"grythead\"><td>#</td><td>Assessment</td><td>ICD10</td><td>Site</td><td>Staging</td><td>Severity</td></tr>"+str;
			oDivDxCode.style.width="90%";
			oDivDxCode.style.left="80px";
			clsstbl=" class=\"icd10\" ";
		}

		var strInnerHTML = "<table id=\"dx_sel_tbl\" class=\"table table-bordered table-responsive\" >"+
						"<tr class=\"head\"><th class='section_header1' >Dx Codes : Display in Super Bill</th></tr>";
		if(""+$("#hid_icd10").val() != "1"){ strInnerHTML +=		"<tr><td></td></tr>"; }
		strInnerHTML +=		"<tr ><td  valign=\"top\" >";
		strInnerHTML += "<div onmousedown=\"stopClickBubble();\"><table "+clsstbl+" onmousedown=\"stopClickBubble();\"  >"+str+"</table></div>";
		strInnerHTML += "</td></tr>";

		strInnerHTML += "<tr id=\"module_buttons\" class=\"ad_modal_footer\"><td align=\"center\"><input name=\"button\" class=\"btn btn-success\" type=\"button\" id=\"elem_dxcBtn\"  value=\"Done\" onclick=\"getWantedDxCodes('"+funcname+"')\" onmousedown=\"stopClickBubble();\">"+
						"&nbsp;&nbsp;&nbsp;&nbsp;<input name=\"button\" class=\"btn btn-danger\" type=\"button\" id=\"elem_dxcanBtn\"  value=\"Cancel\" onclick=\"$('#divChooseDxCodes').hide();\" >"+
						"</td></tr>";
		strInnerHTML += "</table>";

		oDivDxCode.innerHTML = ""+strInnerHTML;

		//hgt
		if($("#superbill #dx_sel_tbl div").height()<$("#superbill #dx_sel_tbl div table").outerHeight()){

			var tmphgt = ($("#superbill #dx_sel_tbl div table").outerHeight()<600)?$("#superbill #dx_sel_tbl div table").outerHeight():600;
			$("#superbill #dx_sel_tbl div").height(tmphgt+"px");
		}

		//oDivDxCode.style.top = "10px";
		//var thisScrollTmp = $(".superbillhd").position(); // (gebi("divWorkView")) ? gebi("divWorkView").scrollTop : document.scrollTop ;
		//var thisScrollLeft = $(".superbillhd").position().top;
		//console.log(thisScrollTmp);



		//if(!thisScrollTmp){
			//thisScrollTop = 700;
		//}else{
			//console.log(thisScrollTmp);
			//oDivDxCode.style.top = (thisScrollTmp+10)+"px";
			//console.log(thisScrollTmp,thisScrollTmp.top,thisScrollTmp.left);
			//$(oDivDxCode).css({"top":thisScrollTmp.top,"left":thisScrollTmp.left});
			//$(oDivDxCode).find(".small").css({'white-space': 'normal'});

		//}
		//drag
		$(oDivDxCode).draggable({handle:".section_header"});


		//TH --
		var t = arrdxth.length;
		if(t > 0){

			/*
			for(var i=0;i<t;i++){
				new actb(gebi(arrdxth[i]),arrDxCodeAndDesc);
			}
			*/
			sb_addTypeAhead();

		}
		//TH --

		icd10_pre_restdxcodesinpopup(len);

		//hide dibetes codes w.r.t proliferative
		disDbtsProlifOptions();

		//stopClickBubble();
	}
	return str;
}

var getDCFA_strApId;
//Get Dx Codes From Assessments
function getDxCodesFromAssess(strAses){
	//Checks
	strAses = $.trim(strAses);
	if(strAses == ""){
		return;
	}

	//Define Var -----

	var imgPath_tmp = zPath; //(typeof(imgPath_remote)!="undefined") ? imgPath_remote : imgPath;
	var url = imgPath_tmp+"/chart_notes/requestHandler.php";
	var params = "elem_formAction=getDxFrmAses";
	if(typeof(document.getElementById('hid_icd10')) != "undefined"){
		params += "&ICD_type="+$("#hid_icd10").val();
	}
	params += strAses;
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","superbill");

	//-------------------------------------------
	//console.log(url,params);

	$.post(url,params,
			function(data){

			//------  processing after connection   ----------
			//console.log(data);
			//Debugging
			//document.write(data.replace(/</g,"&lt;").replace(/>/g,"&gt;"));
			//alert(""+data);
			//Debugging
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","superbill");

			var str = "";
			var xmlDoc = data;
			var oShowPop = xmlDoc.getElementsByTagName("prompt_user")[0];
			var flagShowPop = (oShowPop) ? oShowPop.firstChild.nodeValue : 0;
			var oAsInfo = xmlDoc.getElementsByTagName("assess");
			var len = oAsInfo.length;

			var oApId = xmlDoc.getElementsByTagName("apid")[0];
			if(oApId && oApId.firstChild){
				getDCFA_strApId = oApId.firstChild.nodeValue;
			}

			if(typeof sb_pdiv == "undefined"){
				sb_pdiv = "";
			}

			if(flagShowPop == 1){
				str = get_dx_popup_lss(oAsInfo);

			}else{
				//Enter Values in fields if less than 12 and
				var j=0;
				for(var i=0;(j<len)&&(i<12);i++){
					var vdx = vdxid = "";
					var tAss = oAsInfo[j];
					vdx = tAss.getElementsByTagName("dx")[0].firstChild.nodeValue;
					vdx = (typeof vdx != "undefined") ? vdx : "";
					if(vdx!=""){
						var odxid = tAss.getElementsByTagName("dx")[0].attributes.getNamedItem("dxid");
						vdxid = (odxid && typeof odxid!= "undefined" && typeof odxid.nodeValue != "undefined") ? odxid.nodeValue : "" ;
					}
					//Check duplicate
					flgD = true;
					if(vdx != ""){
						for(var k=1;k<=12;k++){
							var oC = gebi("elem_dxCode_"+(k));
							var cdxid = $(oC).data("dxid");
							if(typeof cdxid == "undefined"){ cdxid = ""; }
							cdxid = $.trim(cdxid);

							if(oC && (oC.value != "") && (oC.value == vdx)  ){

								if(cdxid!="" && vdxid!="" && vdxid!=cdxid){ //check dxid
									//
								}else{
									flgD = false;
									j++;
									break;
								}
							}
						}
					}

					if(flgD == true){
						var oT = gebi("elem_dxCode_"+(i+1));
						if(oT && ( ($.trim(oT.value) == "") || ( typeof oT.value == "undefined" ) )){
							oT.value = vdx;
							$(oT).data("dxid", vdxid); //set dxid
							j++;
						}else{
							//alert(oT.value+" : "+vdx);
						}
					}
				}

				//Set DropDow Dx codes
				//$(".dxallcodes").triggerHandler("blur");
				setTimeout(function(){set_dx_code_titles();}, 100);

			}

			//
			if(str == ""){
				//go for super bill
				//Check PQRI All
				checkAssocPqri4All();
			}

			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","superbill");
			//------  processing done --------------------------

			},"xml");



}

function set_dx_code_titles(){
	var ar_dx=[], tdx="", tdxid="", flg=0;
	$(".dxallcodes").each(function(indx){
		tdx = $.trim(this.value);
		tdxid = $(this).data("dxid");
		if(typeof(tdxid)!="undefined"){ tdxid = $.trim(tdxid); }
		if(tdx!=""){
		//checkDB4Code_flg=2;
		//$(this).triggerHandler("blur");
		ar_dx[ar_dx.length] = ""+tdx+","+tdxid+","+this.id;
		flg++;
		}
	});
	if(flg>0){
		$.post(zPath+"/chart_notes/requestHandler.php", {"elem_formAction": "get_dx_titles", "req_ptwo":"1", "ar_dx[]" : ar_dx}, function(d){
				if(typeof(d)!="undefined"){	for(var x in d){if(typeof(d[x])!="undefined"){ renew_title($("#"+x)[0], ""+d[x]);  }}}
			}, "json");
	}
}

function is_dxcode_row_exists(valbakdb, tdxid, objVal, objhtml, trId){
	if(typeof valbakdb == "undefined" || typeof objVal == "undefined" || objVal==""){return 0;}
	if(typeof tdxid == "undefined"){tdxid="";}

	//console.log(valbakdb, tdxid, objVal, objhtml, trId);
	var ret = 0;
	var retObj = "";
	var flgChk=1;
	//check if curent row was empty
	//console.log("Row::", trId, $("#"+trId).find(":checked").length)
	if($("#"+trId).find("td[title='Laterality'] :checked").length<2){


		flgChk=0;


	}

	//if(flgChk==1){
	$("#dx_sel_tbl tr span input[type=checkbox]").each(function(){
			if(ret == 1){return false;}
			var tvalbakdb = $(this).data("valbakdb");

			if(valbakdb!="" && tvalbakdb == valbakdb){
				var ftt=1;
				if(tdxid!=""){
					ftt=0;
					var tmpdxid = $(this).data("dxid");
					if(tdxid==tmpdxid){
						ftt=1;
					}
				}
				if(ftt==1){
				var objtr = $(this).parent().parent().parent();
				var tmptrid = objtr.attr("id");

				if(typeof(trId)!="undefined" && trId!=""){
					if (trId==tmptrid || $("#"+tmptrid+":visible").length==0){
					//if($("#"+trId).find(":checked").length>2){ ret = 1; return false; }

					return true;
					}
				}

				var objchecked = objtr.find("td[title='Laterality'] :checked");
				if(objchecked.length>0){
				objchecked.each(function(){
							if(this.value==objVal){
								ret = 1;
								return false;
							}
						});
				}else{
					retObj = objtr.attr("id");
					//console.log(objtr);
				}
				}
			}
		});
	//}

	if(retObj!="" && flgChk==0){
		ret=0;retObj="";
	}

	return {'ret':ret, 'retObj':retObj};
}

function icd10_restdxcodesinpopup(obj){
	var objVal = $("label[for*="+obj.id+"]").html();

	if(objVal=='RUL' || objVal=='RLL' || objVal=='LUL' || objVal=='LLL' || objVal=='R' || objVal=='L' || objVal=='B'){
		if(obj.checked == true){

			//Check if Same dx code row exists--

			//console.log(obj);

			var tmpres=0;
			var objCurRow = $(obj).parent().parent();//tr
			//console.log(objCurRow);
			var chk_dx_id = objCurRow.find("span input[type=checkbox]");
			if(chk_dx_id.length>0){
				var tdxid = chk_dx_id.data("dxid");
				var valbakdb = chk_dx_id.data("valbakdb");
				var tmpres = is_dxcode_row_exists(valbakdb, tdxid, obj.value, objVal, $(objCurRow).attr("id"));
				//console.log("CHK Exists:", tmpres);
				if(tmpres.ret==1){ $("#"+obj.id).prop("checked",false); $("label[for*="+obj.id+"]").css({"color":"purple","border":"2px solid transparent","background-color":"transparent"}); return;	}
				else if(tmpres.retObj!=""){
					$("#"+obj.id).prop("checked",false);
					$("label[for*="+obj.id+"]").css({"color":"purple","border":"2px solid transparent","background-color":"transparent"});
					//console.log(tmpres.retObj);
					$("#"+tmpres.retObj).find(":checkbox[value='"+obj.value+"']").prop("checked", true).triggerHandler("click");
					return;
				}

			}
			//--End

			var cont_lat_Sel=0;
			for(var i=0;i<4;i++){
				var ele_chk_id=(obj.id).substr(0,((obj.id).length-1));
				var final_ele_chk_id=ele_chk_id+i;
				if(document.getElementById(final_ele_chk_id)){
					if(document.getElementById(final_ele_chk_id).checked == true){
						cont_lat_Sel=parseInt(cont_lat_Sel)+1;
					}
				}
			}
			if(cont_lat_Sel>1){
				var new_ele_obj_name=(obj.name).substr(0,((obj.name).length-1));
				var last_obj_id="";
				$("#"+obj.id).prop("checked",false); $("label[for*="+obj.id+"]").css({"color":"purple","border":"2px solid transparent","background-color":"transparent"});
				for(var i=0;i<20;i++){
					var current_trgtid = obj.name.replace(/(Lat|Svr|Stg)/g,"")+'_tr';
					var curr_tr_data=$("#"+current_trgtid).html();
					var chk_new_tr_id=new_ele_obj_name.replace(/(Lat|Svr|Stg)/g,"")+i+'_tr';
					if(typeof($("#"+chk_new_tr_id).html())=="undefined"){
						var old_obj_id_num=(obj.name).substr(((obj.name).length-3),((obj.name).length));
						var	old_obj_id_num_exp=old_obj_id_num.split('_');
						var new_obj_id=old_obj_id_num_exp[0]+'_'+i;
						var final_data='<tr id="'+chk_new_tr_id+'">'+curr_tr_data+'</tr>';
						var obj_id_rep=new RegExp(old_obj_id_num, "g");
							final_data = final_data.replace(obj_id_rep,new_obj_id);
							$(final_data).insertAfter($("#"+last_obj_id));

							var new_tr_obj=	obj.id.replace(old_obj_id_num,new_obj_id);
							obj=document.getElementById(new_tr_obj);
							$("label[for*="+obj.name+"]").css({"color":"purple","border":"2px solid transparent","background-color":"transparent"});
							$("#"+obj.id).prop("checked",true);
							$("label[for="+obj.id+"]").css({"color":"Red","border":"2px solid blue","background-color":"white"});
						break;
					}
					last_obj_id=chk_new_tr_id;
				}
			}else{
				$("label[for="+obj.id+"]").css({"color":"Red","border":"2px solid blue","background-color":"white"});
			}
		}else{
			$("label[for*="+obj.id+"]").css({"color":"purple","border":"2px solid transparent","background-color":"transparent"});
		}
	}else{
		$("label[for*="+obj.name+"]").css({"color":"purple","border":"2px solid transparent","background-color":"transparent"});
		if(obj.checked){$("label[for="+obj.id+"]").css({"color":"Red","border":"2px solid blue","background-color":"white"});$(":checked[name="+obj.name+"][id!="+obj.id+"]").prop("checked",false);}
	}
	var trgtid = obj.name.replace(/(Lat|Svr|Stg)/g,"");
	var trgtval = $("#"+trgtid).val();
	var valChk= $.trim($("#"+trgtid).data("valbak"));
	var valChkdb= $.trim($("#"+trgtid).data("valbakdb"));
	if(valChk.length == valChkdb.length){
		var valChkdb_tmp=""+valChkdb.replace(/-/g,"\\w");
		var rex = new RegExp(""+valChkdb_tmp,"gi");
		var rez = valChk.match(rex);
		if(rez){valChk=valChkdb;}
	}

	$("#"+trgtid).prop("checked",true);
	var setVal = (obj.checked) ? obj.value : "-";

	//
	//for diabetic code -- it is SEL
	var isDbtsCode=0;
	if(valChkdb.length==8 && (valChkdb.indexOf("E10.")!=-1 || valChkdb.indexOf("E11.")!=-1)){	isDbtsCode=1;	}
	//--

	var trgtvalNew="";
	if(obj.id.indexOf("Lat")!=-1){//lat
		//trgtval
		if(isDbtsCode==1){
		if(valChkdb.charAt(7)=='-'){//
			var tt =trgtval.substr(0,7);
			var tt1 =trgtval.substr(8);
			trgtvalNew=tt+setVal;
		}
		}else{
		if(valChk.charAt(6)=='-'){//
			var tt =trgtval.substr(0,6);
			var tt1 =trgtval.substr(7);
			trgtvalNew=tt+setVal+tt1;
		}else{
			if(valChk.substr(-3,3)!='-X-' || valChk.substr(-3,3)!='-x-'){
				var tt =trgtval.substr(0,5);
				var tt1 =trgtval.substr(6);
				trgtvalNew=tt+setVal+tt1;
			}
		}
		}

	}else{ //Svr or stg
		if(isDbtsCode==1){

			if(obj.id.indexOf("Stg")!=-1){
				if(valChkdb.charAt(6)=='-'){//
					var tt =trgtval.substr(0,6);
					var tt1 =trgtval.substr(7);
					trgtvalNew=tt+setVal+tt1;
				}

			}else if(obj.id.indexOf("Svr")!=-1){
				if(valChkdb.charAt(5)=='-'){//
					var tt =trgtval.substr(0,5);
					var tt1 =trgtval.substr(6);
					trgtvalNew=tt+setVal+tt1;
				}
			}

		}else{
		if(valChk.indexOf("-")!=-1 && valChk.indexOf("--")==-1 && valChk.indexOf("-X-")==-1 && valChk.indexOf("-x-")==-1){
			trgtvalNew 	= valChk.replace('-',setVal);
		}else{
			if(valChk.indexOf("--")!=-1){
				var tt =trgtval.substr(0,7);
				trgtvalNew=tt+setVal;
			}else if(valChk.indexOf("-X-")!=-1 || valChk.indexOf("-x-")!=-1){
				var tt =trgtval.substr(0,7);
				trgtvalNew=tt+setVal;
			}
		}
		}
	}

	//alert(trgtval);
	if(trgtvalNew!=""){
		//
		$("#"+trgtid).val(""+trgtvalNew);
		var asindx = $("#"+trgtid).data("asindx");
		$("label[for="+trgtid+"]").html(""+trgtvalNew);
		var tm = ""+trgtid.replace("elem_asDx","").split("_");
		if(""+tm[0]!=""){
			var tmp_asindx = (asindx&&asindx!="") ? asindx : parseInt(tm[0])+1;
			var tmid = "elem_assessment_dxcode"+tmp_asindx;

			if($("#"+tmid).length>0 && ""+$("#"+tmid).val()!=""){
				if(""+$("#"+tmid).val().indexOf(",")!=-1||""+$("#"+tmid).val().indexOf(";")!=-1){
					//mupliple
					var arrtmp = ($("#"+tmid).val().indexOf(",")!=-1) ? $("#"+tmid).val().split(",") : $("#"+tmid).val().split(";");
					if(arrtmp.length>0){
						var strdx="";
						var flgin = 0;
						for(var t in arrtmp ){
							arrtmp[t] = $.trim(arrtmp[t]); //trim spaces

							if(arrtmp[t] != ""){
								if($("#"+tmid).val().indexOf("-")!=-1){//old case
									var inx = valChk.indexOf("-");
									var ck = valChk.substring(0, inx);
									if(arrtmp[t].indexOf(ck)!=-1){
										//
										if(strdx!=""){ strdx+=", "; }
										strdx+=trgtvalNew;

									}else{
										//
										if(strdx!=""){ strdx+=", "; }
										strdx+=arrtmp[t];
									}
									flgin = 1;
								}else{//new case added
									if(strdx!=""){ strdx+=", "; }
									if($.trim(arrtmp[t]) != $.trim(trgtvalNew)){
										strdx+=arrtmp[t];
									}else{
										flgin = 1;
									}
								}
							}
						}

						// no inserted; insert now
						if(flgin == 0){
							if(strdx!=""){ strdx+=", "; }
							strdx+=trgtvalNew;
						}
					}
					$("#"+tmid).val(strdx).trigger("keyup");
				}else{
					var tdx = $("#"+tmid).val();
					//--
					var tdx_1 = tdx.slice(0,-1);
					var tdx_2 = trgtvalNew.slice(0,-1);
					if(tdx_1==tdx_2 && tdx.indexOf("-")!=-1){
						tdx=trgtvalNew;
					}else{
						tdx=tdx+", "+trgtvalNew;
					}
					//--
					$("#"+tmid).val(tdx).trigger("keyup");
				}
			}else{
				$("#"+tmid).val(trgtvalNew).trigger("keyup");
			}
		}



		//fill value in Assessment dx --
		//console.log($("#"+trgtid));
		var asdx="";
		var x = trgtid.match(/elem_asDx\d+/);
		$("#dx_sel_tbl :checked[id*="+x+"][data-valbak]").each(function(){ if(typeof(this.value)!="undefined" && this.value!="" && asdx.indexOf(this.value)==-1){  if(asdx!=""){ asdx+=", "; }  asdx+=this.value;}   });
		$("#"+tmid).val(asdx).trigger("keyup");
		//console.log(trgtid, x);
		//fill value in Assessment dx --

	}
	disDbtsProlifOptions(); //hide dibtese options w.r.t prolif
}

function replace_cid10_extra_string(val){
	val=val.replace('Right Eye','Right');
	val=val.replace('Left Eye','Left');
	val=val.replace('Both Eyes','Both');


	val=val.replace('Initial Encounter Closed Fracture','Closed');
	val=val.replace('Initial Encounter Open Fracture','Open');
	val=val.replace('Subsequent Encounter for Fracture with Routine Healing','Routine');
	val=val.replace('Subsequent Encounter for Fracture with Delayed Healing','Delayed');
	val=val.replace('Subsequent Encounter for Fracture with Non Union','Union');

	val=val.replace('Initial Encounter','Initial');
	val=val.replace('Subsequent Encounter','Subsequent');

	val=val.replace('Adv atrophic w/o subfoveal Involvement','w/osubfoveal');
	val=val.replace('Adv atrophic w subfoveal Involvement','wsubfoveal');
	val=val.replace('Active neovascularization','Active');
	val=val.replace('Inactive neovasculization','Inactive');
	val=val.replace('AdInactive scar','scar');
	val=val.replace('w/ macular edema','edema');
	val=val.replace('w/ret neovaculaization','neovaculaization');

	return val;
}

//RVS --
// Used in superbill
function getRvsDoneLevel(str){
	var arrLoc = new Array("Right Eye","Left Eye","Both Eyes","Right Eyelids","Left Eyelids","RUL","RLL","LUL","LLL","Peripheral vision","Paraxial vision","Central vision",
						"Head","left side of head","right side of head","left side of face","right side of face","fore head","scalp","selLocOpt","selLocOther");
	var arrQuality = new Array("Stabbing","Dull","Aching","Doing well","Feels improvement","Tolerating","Quality~Worsening","Quality~Stable","Resolved","No changes noted","New",
					"Quality~Increased","Quality~Decreased","Initially improved then worsened",
					"Quality~Burning","distorted","dry","foggy","ghosting","Quality~glare","hazy","Quality~itching","pressure","scratchy","sharp","throbbing","watery",
					"Quality~patient unsure","since surgery","since birth","since childhood","since last visit","many years","otherQty");
	var arrSeverity = new Array("Mild","Moderate","Severe","None",'Severity~Intermittent','Severity~Constant',"Decreased","Increased","Worsening","scale1To10","rvs_other_svrity");
	//var arrDuration =
	//var arrTimeOnset = new Array("Sudden","Gradual","Constant","Comes and Goes","Patient unsure","otherTiming","onSetDate","elem_sp_surtype","elem_sp_surdate");
	var arrTimeOnset = new Array("selectNo-","selectDate-","elem_sp_surtype","elem_sp_surdate","other_onset","many years", "Patient unsure", "Since surgery", "Since birth","Since childhood","Since last visit");
	var arrContext = new Array("Reading","driving","outside","inside","otherContext");
	var arrModFact = new Array("MF~inside","outdoors","night","distance vision than near","near vision than distance",
					"when transition from dark to light","when transition from light to dark","in dim light","early in the day",
					"late in the day","with intensive visual activity (readingcomma computer)",
					"with daily activities","makesBetter-", "makesWorse-","otherFactors","rvs_painrelievedby");
	var arrAssocSign = new Array("Pain","Itching","Burning","Headache","Redness","Light sensitivity","Irritation","Blurry Vision","Halos","Foreign body sensation",
						"Sharp",'ASAS~Dull',"Ache","Headaches",
					"Eye pain","double vision","flashes","floater","ocular redness","glare","dizziness or light-headedness","weakness","tearing",
					"high ocular pressure","none","otherSymptoms");
	var arrDoe=new Array("DoE~Intermittent","DoE~Uncertain","selectDoE", "Sudden","Gradual","Constant","otherDoe","onSetDate");
	var arrVis=new Array("Blurry","Improved","Worse","Stable","rvsdet_otherVision");
	var arrDip=new Array("Dip~None","Dip~Mild","Dip~Improved","Dip~Worse");
	var arrMed=new Array("is following medication instructions","is not taking medications","ran out of meds","has finished meds as instructed",
							"needs med refill","rvs_ranoutmeds","rvs_needs_med_refill");
	var arrCI=new Array("otherFollowCareInstruct");
	var arrOther=new Array("rvs_followup_detail_other");

	var arrParNeg = ["no eye Pain","no Itching","no Tearing","no Flashes","no floaters","no Glare",
					"no Red Eye","no Headache","no Dryness","no Distortion","no Change in Amsler Grid",
					"no Ocular Trauma",
					"no Pain with Eye Movement",
					"no Loss of Consciousness",
					"no Visual Phenomenon",
					"no Pain or Tearing",
					"no ShadowcommaCurtain or Veil",
					"no Flashescommafloatercommashadowcommacurtain or Veil",
					"no FevercommaWeight LosscommaScalp TendernesscommaHeadache or Jaw Claudication","other_par_neg"]; //Pertinent Negatives
	/*
	var arrAll = arrLoc.concat(arrQuality).concat(arrSeverity).concat(arrTimeOnset).concat(arrContext).
				concat(arrModFact).concat(arrAssocSign).concat(arrDoe).concat(arrVis).concat(arrDip).concat(arrMed).concat(arrCI).concat(arrOther).concat(arrParNeg); //concat(arrDuration)//v2
	*/
	var arrAll = [arrLoc,arrQuality,arrSeverity,arrTimeOnset,arrContext,arrModFact,arrAssocSign,arrDoe,arrMed];//v1 //arrVis,arrDip, ,arrCI,arrOther,arrParNeg

	//"other_par_neg","rvsdet_otherVision", "rvs_painrelievedby","otherFollowCareInstruct", "rvs_followup_detail_other",
	var arr_txt_fields = ["rvs_needs_med_refill","rvs_ranoutmeds",
						"rvs_other_svrity","otherQty","other_onset","selLocOther","otherSymptoms",
						"otherFactors","otherContext","scale1To10","otherDoe","selLocOther"];
	//var vLoc=vQuality=vSeverity=vDuration=vTimeOnset=vContext=vModFact=vAssocSign=false;
	var ret=1;
	var cntr=0;
	if($.trim(str) != ""){
		var arrStr = str.split(",");
		if(arrStr.length>0){ //remove
			for(var x in arrStr){
				var t = (typeof(arrStr[x])!="undefined" && arrStr[x]!="") ? arrStr[x] : "";
				if(t!="" && t.indexOf("-")!=-1){
					var art = t.split("-");
					if(arr_txt_fields.indexOf(art[0])!=-1){
						arrStr[x]=art[0];
					}else if(art[0]=="selectNo" || art[0]=="selectDate"){
						arrStr[x]=art[0]+"-";
					}
				}
			}
		}

		var len = arrAll.length;
		for(var i=0;i<len;i++){
			//*v1
			var arin = arrAll[i];
			for(var x in arin ){
				if(-1 != arrStr.indexOf(arin[x])){
					//alert(str+"\n"+arin[x]);
					cntr+=1;
					break;
				}
			}
			//*/

			//v2
			/*
			if(-1 != str.indexOf(arrAll[i])){
					//alert(str+"\n"+arin[x]);
					cntr+=1;
			}
			*/

			if(cntr>=4){
				break;
			}
		}
	}

	ret = (cntr >= 4 ) ? 3 : 2;
	return ret;
}
function getRvsDoneSt(){
	/* Ammendents as per guidence-06/11/09
	var cntr=0;
	var arrElemNames = new Array("elem_vpDis[]","elem_vpMidDis[]","elem_vpNear[]","elem_vpGlare[]","elem_vpOther[]",
						"elem_irrLidsExt[]","elem_irrOcu[]","elem_postSegSpots","elem_postSegFL[]","elem_postSegFloat[]");
	for(var x in arrElemNames){
		var elem = document.getElementsByName(arrElemNames[x]);
		var len = elem.length;
		for( var i=0;i<len;i++ ){
			if( elem[i].checked == true ){
				cntr += 1;
				break;
			}
		}
	}
	*/

	/* Ammendents as per guidence-06/11/09
	*/
	//if( cntr >= 1 ){
	//cntrRet = 1;
	var cntrRet=0;
		if((complaint1.toString() != "") || (complaint2.toString() != "") || (complaint3.toString() != "") ){
			/* Stopped as per arun sir 's guidence 06 nov 09
			//cntrRet = (cntr > 3) ? 3 : 2;
			*/
			cntrRet=1;
			if(complaint1.toString() != ""){
				cntr = getRvsDoneLevel(""+complaint1);
				if(cntr > cntrRet){
					cntrRet = cntr;
				}
			}
			if(complaint2.toString() != ""){
				cntr = getRvsDoneLevel(""+complaint2);
				if(cntr > cntrRet){
					cntrRet = cntr;
				}
			}
			if(complaint3.toString() != ""){
				cntr = getRvsDoneLevel(""+complaint3);
				if(cntr > cntrRet){
					cntrRet = cntr;
				}
			}
		}else{

			///check if RVS is done without detailing a complaint: HPI = 1
			var ar = ["-Vision Problem: Difficulty in","\r\n-Glare Problem: ","\r\n-Other Vision Problem: ","\r\n-Patient Comments: ",
					"\r\n-Irritation Lids: ","\r\n-Irritation Ocular: ","\r\n-Post Segment: ","\r\n-Flashing Lights: ",
					"\r\n-Floaters: ","\r\n-Amsler Grid: ","\r\n-Double vision: ","\r\n-Temporal Arteritis symptoms: ",
					"\r\n-Loss of vision: ","\r\n-Headaches: ","\r\n-Migraine Headaches: ","\r\n-Post Op: ",
					"\r\n-Follow Up: "];
			var s = $("#elem_ccompliant").val();
			if(typeof(s)=="undefined"){ s="";  }
			for(var x in ar){
				if(s.toLowerCase().indexOf(ar[x].toLowerCase()) != -1){
					cntrRet=1;
				}
			}

			//
			if(cntrRet==0){
				var arrElemNames = new Array("elem_vpDis[]","elem_vpMidDis[]","elem_vpNear[]","elem_vpGlare[]","elem_vpOther[]",
									"elem_irrLidsExt[]","elem_irrOcu[]","elem_postSegSpots","elem_postSegFL[]","elem_postSegFloat[]",
									"elem_postSegAmsler[]","elem_neuroDblVis[]","elem_neuroTAS[]","elem_neuroMigHead[]",
									"elem_neuroMigHeadAura[]","elem_neuroVisLoss[]",
									"elem_fuFollowUp[]","elem_fuPostOp[]","elem_neuroHeadaches[]");
				for(var x in arrElemNames){
					var elem = document.getElementsByName(arrElemNames[x]);
					var len = elem.length;
					for( var i=0;i<len;i++ ){
						if( elem[i].checked == true ){
							cntrRet=1;
							break;
						}
					}
				}
			}

			if(cntrRet==0){
				var arT = ["elem_vpDisOther","elem_vpMidDisOther","elem_vpNearOther","elem_vpOtherOther",
						"vpComment","elem_irrLidsExtOther","elem_irrOcuOther","elem_neuroTASOther",
						"elem_neuroMigHeadAuraOther","elem_neuroVisLossOther","elem_fuPostOp_other","elem_fuFollowUp_other"]	;
				for(var x in arT){
					var t = $("#"+arT[x]).val();
					if(t!=""&&typeof(t)!="undefined"){ cntrRet=1;}
				}
			}
		}
		return (typeof cntrRet != "undefined") ? cntrRet : 0;
	//}
	return 0;
}
//RVS --

//DM POP up--
	var sb_chkDia_inx;
	var sb_dm_popup_old; //value is set in below ajx call
	var call_CHECK_DIABETES;
	function sb_checkDiabetes(dib_type, obj, num, hgl, com){

		if((finalize_flag!=0&&isReviewable!=1)||elem_per_vo == "1"||$("#hid_icd10").val()!="1"){
			if(z_flg_diab_sb=="1" ){opSuperBill(1);} //&& z_flg_diab_sb!="2"
			return;
		}

		//if called from superbill and DM code already exists then return
		if(z_flg_diab_sb=="1" ){ //&& z_flg_diab_sb!="2"
			var flgDMCodeEntered = sb_clearDiabetesAssessments(1);
			if(flgDMCodeEntered==1){opSuperBill(1); return;}
		}

		//just highlight and return
		if(typeof(hgl)!="undefined" && hgl=="1"){

			//if checked highlighted cell
			var chk = $(obj).hasClass("highlight");
			if(chk==true){ $(obj).removeClass("highlight"); return;}

			//
			var stg = $(obj).html();

			if(num=="1"){
				if(sb_dm_popup_old){	$("#dialog-msg-diab td[onclick]").removeClass("highlight");	}
			}else if(num=="t1" || num=="t2"){ //Taking
				//return;
				//$(obj).parent().parent().parent().find("td[onclick]").removeClass("highlight"); //one check

			}else{

				//Make another for second Eye --
				if(stg=="Right" || stg=="Left"){

					if($("#tbl_dbts_2").length<=0){

					//if($(obj).parent().parent().parent().parent().find(".highlight").length>0){
						//
						var x=0;
						//$(obj).parent().parent().parent().parent().parent().find(".highlight").each(function(){  if($(this).html()!=""&&$(this).html()=="Both"){ x+=1; }   }); // issue it stop new creation when both is selected and user click left \ right

						if(x==0){
							//create a new
							var clone_htm = $("#tbl_dbts_1").html();
							clone_htm = "<table id=\"tbl_dbts_2\" cellpadding=\"2\" style=\"width:100%;\">"+clone_htm+"</table>";
							//console.log(clone_htm);
							//$( "#tbl_dbts_1" ).clone().appendTo( ".goodbye" );
							$(clone_htm).insertAfter("#tbl_dbts_1");
							$("#tbl_dbts_2 td[onclick]").removeClass("highlight");
							//$("#tbl_dbts_2 td[onclick]").each(function(){  if($(this).html() == stg){ $(this).trigger("click");  }   });
							//return;
						}
					//}

					}else{
						//If I pick right in the first section, then I should not be able to select right again in the second section.
						var x=0;
						$("#dialog-msg-diab td.highlight").each(function(){  if($(this).html()!=""&&$(this).html()==stg){ x+=1; }   });
						if(x>0){ return; }
					}

				}else if(stg=="Both"){
					//remove second
					var both_tbl_id = $(obj).parents("table[id*=tbl_dbts_]").attr("id");
					if(both_tbl_id=="tbl_dbts_2"){
						$("#tbl_dbts_1").remove();
						$("#tbl_dbts_2").attr("id","tbl_dbts_1");
					}else{
						$("#tbl_dbts_2").remove();
					}
				}

				//Make another for second Eye --

				$(obj).parent().parent().parent().parent().find("td[onclick]").removeClass("highlight"); //("#dialog-msg-diab td[onclick]").removeClass("highlight");
				//$("#dialog-msg-diab #dbts_icd10_1").parent().parent().find("td[onclick]").removeClass("highlight");
			}

			//$(obj).css({"color":"red","font-weight":"bold"});
			sb_chkDia_inx=num;
			$(obj).addClass("highlight");

			// show diferent edema if prolif is selected
			if(stg=="Proliferative"){
				$(obj).parents("table[id*=tbl_dbts_]").find("#tbl_trd").show();
				$(obj).parents("table[id*=tbl_dbts_]").find("#tbl_wo_me").show();
			}else if(stg=="Mild" || stg=="Mod" || stg=="Severe"){
				$(obj).parents("table[id*=tbl_dbts_]").find("#tbl_trd").hide();
				$(obj).parents("table[id*=tbl_dbts_]").find("#tbl_wo_me").show();
			}

			return;
		}
		//--

		//show pop up
		if(typeof(obj)!="undefined"){

			var dx_org = $("#dialog-msg-diab").data("dxcode");
			var assess_org = $("#dialog-msg-diab").data("assess");
			var assess=[], dx=[], eye=[];
			//
			var assess_take=[], dx_take=[], eye_take=[];

			$("#dialog-msg-diab td.highlight").each(function(){
						var a = $(this).data("md");
						var b = $(this).html();

						//taking--
						if(a=="t1" || a=="t2"){ //Taking

							var assess_t = ""+$(this).data("asmt");
							var dx_t = ""+$(this).data("dx");

							assess_take[assess_take.length] = assess_t;
							dx_take[dx_take.length] = dx_t;

							return;
						}
						//taking--

						var tbl_id = ""+$(this).parents("table[id*=tbl_dbts_]").attr("id");
						if(typeof(assess[tbl_id])=="undefined"){ assess[tbl_id]="";  }
						if(typeof(dx[tbl_id])=="undefined"){ dx[tbl_id]="";  }
						if(typeof(eye[tbl_id])=="undefined"){ eye[tbl_id]="";  }
						//
						var assess_t = ""+assess[tbl_id];
						var dx_t = ""+dx[tbl_id];
						var eye_t = ""+eye[tbl_id];

						if(sb_dm_popup_old!=1){

						if((b.indexOf("E1")!=-1 && b.indexOf(".9")!=-1) || (b.indexOf("Diabetes Type 1 No retinopathy")!=-1) || (b.indexOf("Diabetes Type 2 No retinopathy")!=-1)){
							dx_t = $("#dbts_icd10_dx_1").html();
							assess_t = b;
						}else{

							var w1=""; var a1="";
							if(a.indexOf("s")!=-1){
								a1 = "s";
								w1 = "5";
							}else if(a.indexOf("e")!=-1){
								a1 = "e";
								w1 = "6";
							}else if(a.indexOf("l")!=-1){
								a1 = "l";
								w1 = "7";
							}else{
								a1 = "";
								w1 = "100";
							}

							if(a1!=""){

								//initialize
								if(dx_t==""){ dx_t = dx_org;  }

								a = a.replace(a1,"");
								var dx1 = dx_t.substr(0, w1);
								var dx3 = dx_t.substr(parseInt(w1)+1);
								dx_t = dx1+a+dx3;

								if(a1!="l"){
									//initialize
									//if(assess_t==""){ assess_t = assess_org;  }
									assess_t = assess_t + " "+ b;
								}else{
									if(b=="Right"){eye_t = "OD";}
									else if(b=="Left"){eye_t = "OS";}
									else if(b=="Both"){eye_t = "OU";}
								}
							}
						}

						}else{ //old
							assess_t = $("#dbts_icd10_"+num).html();
							dx_t = b;
						}

						//
						if(assess_t!=""){assess[tbl_id]=assess_t;	}
						if(dx_t!=""){dx[tbl_id]=dx_t;	}
						if(eye_t!=""){eye[tbl_id]=eye_t;	}


					});


			//console.log(assess, dx, eye, assess.length);
			var udx;
			var pl = sb_clearDiabetesAssessments(udx,1);
			var assess_t=assess_org;
			var eye_t="", dx_t="";
			com = $.trim(com);

			for(var x in assess){
				if(assess[x] && typeof(assess[x])!="undefined" && assess[x]!=""){
					//alert(dx+" - "+assess);
					var as =$.trim(assess[x]);
					as_cd = dx[x];

					//add Eye
					//--
					if(typeof(eye[x])!="undefined" && eye[x]!=""){
						if(eye_t==""){
							eye_t=eye[x];
						}else{
							if((eye[x]=="OD" && eye_t=="OS")||(eye[x]=="OS" && eye_t=="OD")){
								eye_t="OU";
							}
						}

						//add in asmt
						if(eye[x]=="OD"){  as = "Right " + as;  }
						if(eye[x]=="OS"){  as = "Left " + as;  }
						if(eye[x]=="OU"){  as = "Both " + as;  }
					}
					//--

					//
					if(sb_dm_popup_old!=1){
					if(as.indexOf("No retinopathy")==-1)	{
						if(assess_t.indexOf(";")==-1){ assess_t = assess_t +"; ";  }else{ assess_t = assess_t +", "; }
						assess_t = assess_t +""+ as;
					}else{
						assess_t = as;
					}
					}else{ //old
						assess_t = as;
					}

					//--
					if(typeof(as_cd)!="undefined" && as_cd!="" ){
						if(dx_t!=""){ dx_t=dx_t+", "; }
						dx_t = dx_t+as_cd;
					}
				}
			}

			if(typeof(eye_t)=="undefined"){ eye_t=""; }
			if(typeof(com)!="undefined" && com!=""){ if(assess_t!=""){  assess_t = assess_t+"; \n"+com; } }
			if(typeof(pl)=="undefined"){ pl=""; }
			if(typeof(dx_t)=="undefined"){ dx_t=""; }

			if(typeof(assess_t)!="undefined" && assess_t!=""){
				sb_addAssessOption(""+assess_t,""+pl, 0, ""+eye_t, "", ""+dx_t, "");
			}

			//Taking--
			if(assess_take.length>0){
				for(var z in assess_take){
					if(typeof(assess_take[z])!="undefined" && assess_take[z]!=""){
						sb_addAssessOption(""+assess_take[z],"", 0, "", "", ""+dx_take[z], "");
					}
				}
			}
			//Taking--

			$( "#dialog-msg-diab" ).dialog( "destroy" );
			$("#dialog-msg-diab").remove();

			//call when clicked superbill
			if(z_flg_diab_sb=="1" ){ //&& z_flg_diab_sb!="2"
				//alert("NOW OPSUPERBILL");
				opSuperBill(1);
			}

			return;
		}

		var dib_type_qry = "";
		if(typeof(dib_type)!="undefined"){  $("#dialog-msg-diab").dialog( "destroy" );$("#dialog-msg-diab").remove(); dib_type_qry="&set_diabetes_type="+dib_type; } //

		//get DM type 1 or 2 from Assessment if exists
		var dib_type_as = "";
		var ass_nm = "#assessplan textarea[name*='elem_assessment[]']";
		if(typeof(wpage)!="undefined" && wpage=="accSB"){
			ass_nm = ".assnm";
		}

		var ass_nm_w_dm="";
		var ass_nm_w_dm_take=""; //taking
		$(""+ass_nm).each(function(){
							var as=$.trim(this.value).toLowerCase();
							var mtch = isDiabetesAssess(as,z_flg_diab_sb);
							if(mtch){ dib_type_as = "&get_diabetes_assess="+mtch+"&as="+as; ass_nm_w_dm=this.id; }
							if(isDbTakeAssess(as)){
								if(as.indexOf("insulin")!=-1){ass_nm_w_dm_take+="t1";}
								if(as.indexOf("hypoglycemic")!=-1){ass_nm_w_dm_take+="t2";}
							}
							});
		if(ass_nm_w_dm_take!=""){ dib_type_as+="&dm_asmt_take="+ass_nm_w_dm_take; }


		//pr
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"assessplan");
		//distroy if exists
		if($("#dialog-msg-diab").length>0){  $("#dialog-msg-diab").remove(); $( "#dialog-msg-diab" ).dialog( "destroy" );}

		if(typeof(call_CHECK_DIABETES) != "undefined" && call_CHECK_DIABETES==1){return;} //stop duplicate call
		call_CHECK_DIABETES=1;
		var turl=zPath+"/chart_notes/requestHandler.php?elem_formAction=CHECK_DIABETES&z_flg_diab_sb="+z_flg_diab_sb+dib_type_qry+dib_type_as;
		//console.log(turl);
		$.get(turl, function(data){
				call_CHECK_DIABETES=0;
				//var myWindow = window.open("","MsgWindow","width=200,height=100, resizable=1");
				//myWindow.document.write(""+data);

				//console.log(data);

				//pr
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"assessplan");

				if(z_flg_diab_sb==""){
					////onload no selection
				}else{

				sb_dm_popup_old = data.flgShowOldPop

				if(data.msg=="DIABETES TYPE POPUP" || data.msg=="DIABETES TYPE 1 POPUP" || data.msg=="DIABETES TYPE 2 POPUP"){
					$("body").append(data.html);

					//highlight options
					if(data.show_pop_up_h_option){
						$( "#dialog-msg-diab td[id]" ).each(function(){
								if($(this).html().toLowerCase().indexOf(data.show_pop_up_h_option.toLowerCase()) != -1){
									$(this).css({"background-color":"Yellow"});
									}
							});

					}

					//
					var obtns=[];


					if(data.msg!="DIABETES TYPE POPUP"){
					if(sb_dm_popup_old!=1){
					obtns[obtns.length] =sb_getSeverityScaleObj();
					}//end if
					obtns[obtns.length] ={ text : "Done",
									    click : function() { var cm = $("#dialog-msg-diab #ta_dbts_icd10_cm").val(); if($("#dialog-msg-diab td.highlight").length>0){  sb_checkDiabetes('',$("#dialog-msg-diab td.highlight")[0],sb_chkDia_inx,'',cm); }else if(cm!=""){ if( typeof(ass_nm_w_dm)!="undefined" && ass_nm_w_dm!=""){ var nm = ass_nm_w_dm.replace("elem_assessment","");  var x = $("#elem_assessment"+nm+"").val(); if(x!=""){ var arx=x.split(";"); x=arx[0];  }    x=(typeof(x)!="undefined"&&x!="") ? x+";"+cm : cm;  $("#elem_assessment"+nm+"").val(x).trigger("blur"); $( "#dialog-msg-diab" ).dialog( "close" );  }else{ $( "#dialog-msg-diab" ).dialog( "close" ); } }    }};
					}


					obtns[obtns.length] = { text : "Cancel",  click : function() { $( "#dialog-msg-diab" ).dialog( "close" ); $("#svrt_scl_info").remove(); }  }

					if(data.msg!="DIABETES TYPE POPUP"){
					obtns[obtns.length] = { id: "dmb_reset", text : "Reset",  click : function() {  $("#dialog-msg-diab textarea").val(""); $("#dialog-msg-diab td[onclick]").removeClass("highlight");  if( typeof(ass_nm_w_dm)!="undefined" && ass_nm_w_dm!=""){ var nm = ass_nm_w_dm.replace("elem_assessment","");    $("#elem_assessment"+nm+",#elem_assessment_dxcode"+nm+",#elem_plan"+nm).val("").trigger("blur"); $("#elem_apOu"+nm+",#elem_apOd"+nm+",#elem_apOs"+nm+",#elem_resolve"+nm+",#no_change_"+nm).prop("checked",false).triggerHandler("click");} sb_clearDbAsTaking(); } };
					}

					//modal is false otherwise it gives issue IN IE when two dialog opens.
					$( "#dialog-msg-diab" ).dialog({modal: false, width: 600, buttons: obtns , close: function( event, ui ) {  $( this ).dialog( "destroy" ); $("#dialog-msg-diab").remove(); if(z_flg_diab_sb=="1" && z_flg_diab_sb!="2"){opSuperBill(1);};  }  });
					$("#dmb_reset").css({"float":"right"});
					$("#dmb_svrt_scl").css({"float":"left","background":"none","color":"purple","border":"none","font-weight":"bold"});
					return;
				}

				}

				//
				if(data.assess && typeof(data.assess)!="undefined" && data.assess!=""){
						var as_cd = (data.assess_code && typeof(data.assess_code)!="undefined" && data.assess_code!="") ? ""+data.assess_code : "";
						if(!sb_clearDiabetesAssessments(1)){ // check if alreasy exists and if not, insert it
							sb_clearDiabetesAssessments();
							sb_addAssessOption(""+data.assess,"", 0, "", "", ""+as_cd, "");
						}
				}

				//call when clicked superbill
				if(z_flg_diab_sb=="1" ){ //&& z_flg_diab_sb!="2"
					//alert("NOW OPSUPERBILL");
					opSuperBill(1);
				}

			}, "json");
	}

	//
	function printPlanFun(ocu) {

		var final_flag="", form_id="";
		if(document.getElementById('hidd_final_flag')) { final_flag 	= document.getElementById('hidd_final_flag').value;}
		if(document.getElementById('hidd_final_flag')) { form_id 	= document.getElementById('hidd_formId').value; }
		if(typeof(form_id)=="undefined" || form_id==""){
			form_id=$("#form_id").val();
		}
		if(typeof(final_flag)=="undefined" || final_flag==""){
			final_flag=0;
		}
		if(typeof(ocu)=="undefined"){ ocu=0; }

		medication = null;
		/*var meds = typeof(document.getElementsByName('medication'))!="undefined" ? document.getElementsByName('medication') : '';

		for (var i=0;i<=meds.length-1;i++) {
		if(meds[i].checked)
		medication = meds[i].value;
		}*/
		$.ajax({
			url: zPath+"/chart_notes/requestHandler.php?elem_formAction=chart_plan_print&final_flag="+final_flag+"&form_id="+form_id+"&medication="+medication+"&ocu="+ocu,
			success: function(resp){
				if(resp) {
					//window.open('../common/new_html2pdf/createPdf.php','print_plan_win','toolbar=0,height=400,resizable=1');
					//console.log(zPath+'/../library/html_to_pdf/createPdf.php?file_location');
					//file_location =
					if(top.fmain&&top.fmain.showOtherForms){top.fmain.showOtherForms(zPath+'/../library/html_to_pdf/createPdf.php?file_location='+resp,'print_plan_win','1000','400');}
					else{window.open(zPath+'/../library/html_to_pdf/createPdf.php?file_location='+resp,'print_plan_win','toolbar=0,width=1000,height=400,resizable=1');}
				}else {
					top.fAlert('No Medication Exists');
				}
			}
		});
	}

	//Check Dx Codes with cpt
	function dx_assoc_cpt(o){
		var v_icd_10 = $("#hid_icd10").val();
		if(v_icd_10!="1"&&v_icd_10!="10"){ //works for icd 9 only
			var arcurdx = ""+fun_mselect(this, "val");   //$(this).multiselect("getChecked").map(function(){ return this.value; }).get();
			if(arcurdx && arcurdx!="null" && arcurdx !=""){
				var tmp_arcurdx=arcurdx.split(",");
				var lm =4;
				if(tmp_arcurdx.length > lm){
					top.fAlert("You cannot select more than 4 Dx codes for a procedure.");
					return false;
				}
			}
		}
		//$(this).triggerHandler("blur");
		sb_checknwarn4WrongDxcode(o);
		//set width
		fun_mselect('.selectpicker','width');
	}

	//--

	//Dx Assist
	function dx_assist(){
		var a = $("#superbill .dxallcodes").filter(function(){return $.trim(this.value) !="" ? 1 : 0;}).length;
		var b = $("#tblSuperbill .cptcode").filter(function(){return $.trim(this.value) !="" ? 1 : 0;}).length;

		if(a==0||b==0){ top.fAlert("Please enter CPT and DX codes in superbill."); return; }

		var ar = [];
		var str_cpt = "";
		$("#tblSuperbill .cptcode").each(function(){ 	if(typeof(this.value)!="undefined" && $.trim(this.value)!=""){ar[this.id] = this.value;	str_cpt += ""+this.id+"="+encodeURIComponent(this.value)+"&"; } 	});
		var param = "elem_formAction=get_valid_dx_codes&"+str_cpt;
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","superbill");
		$.get(zPath+"/chart_notes/requestHandler.php", param, function(d){
				if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","superbill");
				if(d){
					for(var a in d){
						if(a && d[a]){
							var x = $.trim(d[a]);
							if(x!=""){
								var chk_all="";
								var ax = x.split(","); var l=ax.length;
								for(var i =0;i<l;i++){
									if(typeof(ax[i])!="undefined" && ax[i] != ""){
										var chk = $.trim(ax[i]);
										$("#superbill .dxallcodes").each(function(){
												var t = $.trim(this.value);
												if(typeof(t)!="undefined" && t != ""){
													var q1=""+t.slice(0,-1)+"-";
													var q2=""+t.slice(0,-2)+"--";
													var q3=""+t.slice(0,-3)+"-x-";
													if(t == chk || q1 == chk || q2 == chk || q3 == chk){  if(chk_all!=""){ chk_all+=","; }  chk_all+=t; return false;  }
												}
											});
									}
								}

								//Select
								if(chk_all!=""){
									var cdx = a.replace("elem_cptCode_", "elem_dxCodeAssoc_");
									fun_mselect("#"+cdx, "select", chk_all);
									$("#"+a).triggerHandler("blur");
								}
							}
						}
					}
				}
			},"json");
	}

	//Add Menues in superbill
	function sb_add_menu(o, h, t){

		if(typeof(o)!="undefined"){

			if(!$(o).hasClass("dropdown-toggle")){

				if(typeof(h)=="undefined"){
					var g="";
					if($(o).parent().hasClass("menu_cpt")){
						g='menu_cpt';
					}else if($(o).parent().hasClass("menu_mod")){
						g='menu_mod';
					}

					if(g!=""){
						if($("#superbill ."+g+"_ul").length<=0){
							if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","superbill");
							$.get(zPath+"/chart_notes/requestHandler.php?elem_formAction=get_sb_menu&wh="+g+"&req_ptwo=1",function(d){
									if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","superbill");
									if(typeof(d)!="undefined" && d!=""){
										sb_add_menu(o, d, 1);
									}
							});
						}else{
							var d = $("#superbill ."+g+"_ul").get(0).outerHTML;
							if(typeof(d)!="undefined" && d!=""){sb_add_menu(o, d);}
						}
						return;
					}
				}else{

					var trgtid = ''+$(o).parent().parent().find(".form-control").attr("id");
					$(o).parent().append(h);
					if(($("#hid_save_section").length>0 && $("#hid_save_section").val() == "accounts")||1==1){  $(o).parent().addClass("dropup"); } //in accounting
					$(o).attr("data-toggle", "dropdown");
					$(o).attr("data-trgt-id", trgtid);
					$(o).addClass("dropdown-toggle");
					$(o).unbind("click",'sb_add_menu');
					$(o).dropdown();

					$(o).parent().find("a").bind("click", function(e){

							if(!$(this).hasClass("cat")){
								var t = $(o).data("trgt-id"); $("#"+t).val($(this).data("val")).trigger("blur");
								$(o).parent().find("ul.dropdown-menu-active").removeClass("dropdown-menu-active").toggle();
								$(o).dropdown("toggle");
							}else{
								$(this).next('ul').addClass("dropdown-menu-active").toggle();
							}
							e.stopPropagation();    e.preventDefault();
						});
					if(typeof(t)!="undefined" && t == "1"){$(o).dropdown("toggle");}

				}
			}
		}else{
			for(var i=0;i<2;i++){
				var m=e=t="";
				if(i==0){
					m="menu_cpt";
					t="tr[id*=elem_trSB] .cpt_td .input-group";
					e=".cptcode";
				}else if(i==1){
					m="menu_mod";
					t="tr[id*=elem_trSB] .md .input-group";
					e=".modcode";
				}

				var s = '<div class="input-group-btn menu '+m+' "><button type="button" class="btn" onclick="sb_add_menu(this)" ><span class="caret"></span></button></div>';
				$(""+t+"").each(function(){ if($(this).find("div").length==0){ $(this).find(e).after(s); }});
				if(typeof(sb_testName)!="undefined" && sb_testName!=""){ $(""+e+"").parent().find(".btn").css({"height":"26px"});  }
			}
		}
	}
