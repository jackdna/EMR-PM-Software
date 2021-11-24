//TESTS JavaScript Document

//Load Tests in Main Pane
function loadTest(test_table, tId, test_type, ptd){
	var popup = true; if(typeof(window.top.fmain)!='undefined') popup = false;
	if(popup) oiFrm = window.top; else oiFrm = window.top.fmain;
	if((typeof(test_table) == "undefined") || (test_table=="")) return;
	if(test_table=='test_other') test_table += test_type;
	if(typeof (tId) == "undefined" || tId=='') tId = 0;

	noP_val = '1';
	pop_val = '0';
	try{
		if(typeof(document.getElementsByName("pop").item(0))!='undefined' && document.getElementsByName("pop").item(0).value != '') pop_val = document.getElementsByName("pop").item(0).value;
	}catch(err){
		
	}
	if(pop_val=='1') noP_val = '0';
	switch(test_table){
		case "vf":
			oiFrm.location.href = "test_vf.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		case "vf_gl":
			oiFrm.location.href = "test_vf_gl.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;

		case "nfa":
			oiFrm.location.href = "test_nfa.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "oct":
			oiFrm.location.href = "test_oct.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "oct_rnfl":
			oiFrm.location.href = "test_oct_rnfl.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "test_gdx":
			oiFrm.location.href = "test_gdx.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "pachy":
			oiFrm.location.href = "test_pacchy.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "ivfa":
			oiFrm.location.href = "test_ivfa.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;

		case "icg":
			oiFrm.location.href = "test_icg.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "disc":
			oiFrm.location.href = "test_disc.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "disc_external":
			oiFrm.location.href = "test_external.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "topography":
			oiFrm.location.href = "test_topography.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "Ophthalmoscopy":
			oiFrm.location.href = "ophtha.php?noP=1&pop=0&tId="+tId;
		break;

		case "test_other0":
			oiFrm.location.href = "test_other.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;

		case "test_other1":
			oiFrm.location.href = "test_template.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "test_custom_patient":
			oiFrm.location.href = "test_template_custom_patient.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "test_bscan":
			oiFrm.location.href = "test_bscan.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		
		case "test_cellcnt":
			oiFrm.location.href = "test_cellcount.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;

		case "test_labs":
			oiFrm.location.href = "test_labs.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		case "DICOM Import":
			oiFrm.location.href = "dicom/dicom_import.php?pid="+ptd+"&callFrom=test";
		break;
		case "surgical_tbl":
			oiFrm.location.href = "ascan.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		case "iol_master_tbl":		
			oiFrm.location.href = "iol_master.php?noP="+noP_val+"&pop="+pop_val+"&tId="+tId;
		break;
		default:
			alert('Interface not ready');
			//oiFrm.location.href = "ascan.php?noP=1&pop=0&tId="+tId;
	}
}

//----
function fillInterpretationProfileData(id){//alert(id+' :: '+arr_interpretation_pofiles[1]);
	if(typeof(arr_interpretation_pofiles)=='undefined') return;
	main_data_array = arr_interpretation_pofiles[id];
	data_array = jQuery.parseJSON(main_data_array);
	f = document.forms[0];//.test_form;
	if(id=='' && typeof(reset_form_if_no_inter_pro_selected)=='boolean' && reset_form_if_no_inter_pro_selected) {f.reset();return;}
	e = f.elements;
	for(i=0;i<e.length;i++){
		o = e[i];
		on	= o.name;
		v	= data_array[on];
		
		/*if name of the element is an array*/
		if(on && on.indexOf('[]')!= '-1' && on.substr(on.length - 2)=='[]'){
			on = on.trim();
			name1 = on.substr(0, (on.length - 2));
			v1	= data_array[name1];
			elementarray = document.getElementsByName(on);
			if(elementarray.length && v1){
				for(j=0;j<elementarray.length;j++){
					elem = elementarray[j];
					val = v1[j];
					if (elem.tagName == "INPUT" || elem.tagName == "SELECT" || elem.tagName == "TEXTAREA"){
						if (elem.type == "checkbox"){
							if(val==elem.value) $(elem).prop('checked',true);
						}else if(elem.type == "radio"){
							if(val==elem.value) $(elem).prop('checked',true);	
						}else if(elem.type!='submit' && elem.type!='button'){
							elem.value = val;
							if(elem.tagName == "TEXTAREA"){elem.value = elem.value.replace(/<br>/g,"\n");}
						}
					}
				}
			}
		}

		if(typeof(v)=='undefined')continue;
		if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
			if(on=='elem_performedByName' || on=='elem_performedBy') continue;
			
			
			if (o.type == "checkbox"){
				if(v==o.value) $(o).prop('checked',true);
			}else if(o.type == "radio"){
				if(v==o.value) $(o).prop('checked',true);	
			}
			else if(o.type!='submit' && o.type!='button'){
				o.value = v;
				if(on=='elem_diagnosis' && v=='Other' && typeof(checkDiagnosis)=='function'){checkDiagnosis(v);}
				else if(on=='elem_diagnosis' && v!='Other' && typeof(checkDiagnosis)=='function'){checkDiagnosis('');}
				if(o.tagName == "TEXTAREA"){o.value = o.value.replace(/<br>/g,"\n");}
			}
		}
	}
	
	if($("input[name='elem_saveForm']")){
		if($("input[name='elem_saveForm']").val()=="Disc" && data_array['elem_diagnosis']){
			setDiagOpts();
			$('select[name="elem_diagnosis"]').val(data_array['elem_diagnosis']);
			var fu_retina = gebi('elem_fuRetina');
			document.getElementById("coment").style.visibility = (fu_retina.checked == true) ? "visible" : "hidden" ;
		}
		else if($("input[name='elem_saveForm']").val()=="ICG"){
			if($("input[name='elem_ArgonLaserEye']:checked").length>0){
				$("#als").css('visibility','visible');
			}
			if($("#Hot_Spot_OD:checked").length>0){
				$("#Hot_Spot_Val_OD").attr('disabled',false);
			}
			if($("#Hot_Spot_OS:checked").length>0){
				$("#Hot_Spot_Val_OS").attr('disabled',false);
			}
		}
		else if($("input[name='elem_saveForm']").val()=="IVFA"){
			if($("input[name='elem_ArgonLaserEye']:checked").length>0){
				$("#als").css('visibility','visible');
			}
		}
		else if($("input[name='elem_saveForm']").val()=="Pachy"){
			if($("#elem_pachy_od_average").val()!=""){
				$("#td_pachy_od_avg").css('display', 'block');
			}
			if($("#elem_pachy_od_correction_value").val()!=""){
				$("#td_pachy_od_correction_value").css('display', 'block');
			}
			if($("#elem_pachy_os_average").val()!=""){
				$("#td_pachy_os_avg").css('display', 'block');
			}
			if($("#elem_pachy_os_correction_value").val()!=""){
				$("#td_pachy_os_correction_value").css('display', 'block');
			}
		}
	}
}

var inPrvVal_flg;
function inPrvVal(flg){
	if(flg == 2){//cl
		$("#idprvhtm").hide();
	}else{		
		
		if(typeof(flg)=="undefined" || flg==3){
			if(typeof(flg)=="undefined"){inPrvVal_flg = setTimeout(function(){  inPrvVal(4); }, 700);}
			else if(flg==3){clearTimeout(inPrvVal_flg);}
            setProcessImg(0);
			return;
		}
		
		if(typeof oPrvDt == 'undefined'){			
			getPrevTestDt();
		}else if(oPrvDt == null){
			alert("No Previous Value Found.");
		}else{		
	
			if(flg == 1){//In
				for(var x in oPrvDt.dt){
					//Donot Show date and time.
					if(x == "elem_examDate" || x == "elem_examTime")continue;	

					var t = $("#"+x).attr("type");					
					if(t=="checkbox"){
						$("#"+x).attr("checked", function(){return (oPrvDt.dt[x]!=0 && oPrvDt.dt[x] != "") ? true : false; });
					}else{
						if(x == "techComment")$("#"+x).focus();						
						$("#"+x).val(""+oPrvDt.dt[x]);						
					}				
				}
				inPrvVal(2);	
			}else{
				var str=""+
						"<table class=\"table table-bordered table-striped\" conmouseout=\"inPrvVal(2);\""+
						"conclick=\"inPrvVal(2);\" >"+
						"<tr style=\"background-color:#696969; color: #fff;font-size:14px;\">"+
						"<td nowrap><b>Date: "+oPrvDt.dt["Date1"]+"</b> </td>"+
						"<td class=\"odstrip\">OD</td>"+
						"<td class=\"osstrip\">OS</td>"+
						"</tr>";
					
					if(typeof oPrvDt.dt["elem_examDate"] == "string"){
						
						for(var x in oPrvDt.hm){
							str+="<tr valign=\"top\">";
							str+="<td><b>"+x+"</b></td>";
							
							if(oPrvDt.hm[x]){
								if(typeof oPrvDt.hm[x]["OD"] != 'undefined'){
									str+="<td>"+oPrvDt.hm[x]["OD"]+"</td>";
									str+="<td>"+oPrvDt.hm[x]["OS"]+"</td>";
								}else if(typeof oPrvDt.hm[x] != 'undefined'){
									str+="<td colspan=\"2\">"+oPrvDt.hm[x]+"</td>";
								}
							}

							str+="</tr>";
						}

					}else {
						str+="<tr valign=\"top\"><td colspan=\"3\">No Previous Test from "+getDateFormat(oPrvDt.dt["Date1"],"yyyy-mm-dd")+"</td></tr>";
					}

				//str+="<tr><td colspan=\"3\" align=\"center\" class=\"txt_10b hand_cur\" onclick=\"inPrvVal(2);\">Close</td></tr>"
				str+="<tr><td colspan=\"3\" align=\"center\" class=\"txt_10b hand_cur\" >";
				
				var tv = (oPrvDt.prv["id"]!=null) ? "visible" : "hidden";
				str+="<span style=\"visibility:"+tv+"\" >"+
					 "<input class=\"btn btn-info\" name=\"btnBckPr\" id=\"btnBckPr\" "+
					 "		type=\"button\" value=\"<< Prev\" onclick=\"getPrevTestDt('"+oPrvDt.prv["id"]+"','"+oPrvDt.prv["dt"]+"','"+oPrvDt.prv["tm"]+"','Prev');\" >"+
					 "</span>";
				
				str+="<span style=\"width:20px; display:inline-block\"></span>";
				str+="<span >"+
					 "<input class=\"btn btn-success\" name=\"btnSelPr\" id=\"btnSelPr\" "+
					 "		type=\"button\" value=\"Select\" onclick=\"inPrvVal(1);\" >"+
					 "</span>";
				
				str+="<span style=\"width:20px; display:inline-block\"></span>";
				str+="<span >"+
					 "<input class=\"btn btn-danger\" name=\"btnXPr\" id=\"btnXPr\" "+
					 "		type=\"button\" value=\"Close\" onclick=\"inPrvVal(2);\" >"+
					 "</span>";
				
				str+="<span style=\"width:20px; display:inline-block\"></span>";
				
				var tv = (oPrvDt.nxt["id"]!=null) ? "visible" : "hidden";
				str+="<span style=\"visibility:"+tv+"\" >"+
					 "<input class=\"btn btn-info\" name=\"btnNxtPr\" id=\"btnNxtPr\" "+
					 "		type=\"button\" value=\"Next >>\" onclick=\"getPrevTestDt('"+oPrvDt.nxt["id"]+"','"+oPrvDt.nxt["dt"]+"','"+oPrvDt.nxt["tm"]+"','Next');\" >"+
					 "</span>";
					 
				str+="</td></tr>"+
					 "</table>";

				
				//if(c==1){			
					str = "<div id=\"idprvhtm_c\" style=\"position:absolute;width:570px;left:10px;top:35px;"+
											"border:0px solid black;background-color:white;\">"+str+"</div>";
					$("#idprvhtm").html(str).css({'display':'block','z-index':'+1000','top':'20px','left':'100px','position':'absolute'});
					//alert("DONE");
				//}
			}
		}
	}
	
}


var sig_resp = false;
function getPhySigForTest(obj,obj_hidd,folder_name,user_id,path){
	//alert("hay");
	//top.show_loading_image('show',100);
	if(typeof(opener) == "undefined" && typeof(top.show_loading_image)!="undefined"){  top.show_loading_image("show",100);}
	var obj_hidd_val = obj_hidd.value;
	var url = ''+path+'/tests/test_user_signature_ajax.php';
	url += '?user_id='+user_id;
	url += '&hidd_val='+obj_hidd_val;
	url += '&folder_name='+folder_name;
	$.getJSON(url, function(data, status){
		sig_resp = true;
		var strResponseVal = data;
		//alert(strResponseVal.strsignpath);
		if(obj && strResponseVal && ((strResponseVal.strpixls && strResponseVal.strpixls != "") || (strResponseVal.strsignpath && strResponseVal.strsignpath != ""))){	
			//alert(strResponseVal);			
			obj.innerHTML = strResponseVal.str;
			obj_hidd.value = strResponseVal.strsignpath;
		}
		if(typeof(opener) == "undefined" && typeof(top.show_loading_image)!="undefined"){  top.show_loading_image("hide");}
	});
}

//Previous  values  in Tests--
function getPrevTestDtExe(ctid,cdt,ctm,dir,cnm,template_id){
	if(typeof(template_id)=='undefined') template_id='';
	if (typeof dir == "undefined" || dir == "") dir = "Prev";	
	if(typeof ctid!='undefined'){
		var id = ctid;
		//var etm = cdt;		
		var edt = cdt;
		stopClickBubble();
		var flgDtCom = 0; 
	}else{
		var id = $("#elem_testId").val();
		//var etm = $("#elem_examTime").val();		
		var edt = $("#elem_examDate").val();
		flgDtCom = 1;
	}	
	if(typeof etm == 'undefined') etm = "";
	var prm = "elem_formAction=PrevTest";
	prm += "&elem_testId="+id;
	//prm += "&elem_examTime="+etm;
	prm += "&elem_examDate="+edt;
	prm += "&elem_examNM="+cnm;
	prm += "&elem_dir="+dir;
	prm += "&elem_flgComp="+flgDtCom;
	prm += "&template_id="+template_id;
	var url = "prevtest.php";	
	if(typeof(setProcessImg)=="function") setProcessImg("1","",2,2);
	//alert(url+"\n\n"+ prm);
	$.post(url, prm, function(data){
		if(typeof(setProcessImg)=="function")setProcessImg(0);
			if(data != ""){				
				oPrvDt = data;
				inPrvVal();
			}
			
	}, 'json');	
}

//Diagnosis
function checkDiagnosis(val)
{
	//document.getElementById("td_diagnosisOther").style.visibility = (obj.value == "Other") ? "visible" : "hidden";
	var osel = gebi("elem_diagnosis");
	var otd = gebi("td_diagnosisOther");
	if((val == "Other")){
		osel.style.display = "none";
		otd.style.display = "block";
	}else{
		osel.style.display = "block";
		otd.style.display = "none";
	}
}

function checkTreatment(val){
	var osel = gebi("elem_treat");
	var otd = gebi("td_treatOther");
	if((val == "Other")){
		osel.style.display = "none";
		otd.style.display = "inline-block";
	}else{
		osel.style.display = "inline-block";
		otd.style.display = "none";
	}
}

function stopClickBubble()
{
	var ev = window.event;
	if(ev){
		ev.cancelBubble = true;
		if (ev.stopPropagation) ev.stopPropagation();
	}
}

/*code, Purpose: Zeiss HL7 message Generation*/
function sendToZeiss(type, action) {
	var forum_procedure = $("#forum_procedure").val();
	if(!forum_procedure || forum_procedure=="") {
		alert("Please select 'Forum Procedure' first");
		return(false);
	}
	$("#zeissAction").val(action);
	if(type=="BSCAN" || type=="CELLCOUNT" || type=="TOPOGRAPHY") {
		if(typeof(top.fmain)=='undefined'){savePachy();}
		else{top.fmain.savePachy();}
	}
	else if(type=="A/SCAN" || type=="IOL-MASTER") {
		if(typeof(top.fmain)=='undefined'){saveAscan();}
		else{top.fmain.saveAscan();}
	}
	else if(type=="DISC" || type=="DISCEXTERNAL") {
		if(typeof(top.fmain)=='undefined'){saveDisc();}
		else{top.fmain.saveDisc();}
	}
	else if(type=="ICG") {
		if(typeof(top.fmain)=='undefined'){saveIcg();}
		else{top.fmain.saveIcg();}
	}
	else if(type=="IVFA") {
		if(typeof(top.fmain)=='undefined'){saveIvfa();}
		else{top.fmain.saveIvfa();}
	}
	else if(type=="OCT" || type=="OCT-RNFL") {
		if(typeof(top.fmain)=='undefined'){saveNfa();}
		else{top.fmain.saveNfa();}
	}
	else if(type=="VF" || type=="VF-GL") {
		if(typeof(top.fmain)=='undefined'){saveVF();}
		else{top.fmain.saveVF();}
	}
}

//
var interpreter_timeout;
function test_interpreted(){
	var id = $("#elem_phyName_order").val();
	var nm = $("#elem_phyName_order").data("phynm");
	$("#signature_cell").trigger("click");
	if(typeof(nm)!="undefined" && nm!=""){
		$("#phyName").val(nm);
		if($("#physicianSelected").get(0) && $("#physicianSelectedOS").get(0)){
			$("#physicianSelected,#physicianSelectedOS").val(id);
		}
		$("#elem_physician").val(id);
		$("#elem_phyName").val(id);
	}
	//console.log(document.getElementById('hidd_signature_path').value);

	if(typeof(isDssEnable)!='undefined' && isDssEnable == 1) {

		var orderable_id = top.fmain.$('#dss_service_orderable_item').val();
		var place_of_consult = top.fmain.$('#dss_placeOfConsult').val();
		var request_reason = top.fmain.$('#dss_reasonForRequest').val();
		var diagnosis_code = top.fmain.$('#elem_dxCode_dss').val();
		var diagnosis_title = top.fmain.$('#elem_dxText_dss').val();

		var error = 0;
		if(typeof(orderable_id)=="undefined" || orderable_id==""){
			error++;
			top.fAlert('DSS orderable id is required.');
		} else if(typeof(place_of_consult)=="undefined" || place_of_consult==""){
			error++;
			top.fAlert('DSS place of consult is required.');
		} else if(typeof(request_reason)=="undefined" || request_reason==""){
			error++;
			top.fAlert('DSS Request a reason is required.');
		} else if(typeof(diagnosis_code)=="undefined" || diagnosis_code==""){
			error++;
			top.fAlert('Diagnosis code is required.');
		} else if(typeof(diagnosis_title)=="undefined" || diagnosis_title==""){
			error++;
			top.fAlert('Diagnosis name is required.');
		} else {
			error = 0;
		}

		if(error == 0) {
			call_back_interpreter();
		}

	} else {
		call_back_interpreter();
	}
}

function call_back_interpreter()
{
	//alert('Calling IN');
	clearTimeout(interpreter_timeout);
	if(sig_resp){
		if(document.getElementById('hidd_signature_path').value){
			if( top.fmain ) { top.$("#save").trigger("click");}
			else { $("#save").trigger("click"); }
		}
		else{top.fAlert('Please fill the physician signature');}
	}else {interpreter_timeout = setTimeout(call_back_interpreter,500);}
}

function openScan(show,test_id,test_name,form_id,test_master_id){
//	var n = "scanImgDis_c<?php echo $disc_edid;?>";
	var features = 'left=200,top=10,width=700,height=650,menuBar=no,scrollBars=no,toolbar=no,resizable=1';
	if(typeof(zPath) == 'undefined') prefix_path = ''; else prefix_path = zPath+'/tests/';
	url = prefix_path+show+'.php?formName='+test_name+'&show='+show+'&formId='+form_id+"&testId="+test_id+'&test_master_id='+test_master_id;
	if(typeof(top.popup_win)=='function'){
		top.popup_win(url,features);
	}else if(typeof(top.window.opener.top.popup_win)=='function'){
		top.window.opener.top.popup_win(url,features);
	}else if(typeof(top.window.opener.top.opener.popup_win)=='function'){
		top.window.opener.top.opener.popup_win(url,features);
	}
}

//Set Tests and Reliability fields as per interpretation
function setReli_Exe(wh,arrTestOd,arrReli){
	//Note: Control value in loop is static.	
	//Reliability
	if( typeof arrReli != "undefined" ){
		var oROd = document.getElementsByName(arrReli[0]);
		var oROs = document.getElementsByName(arrReli[1]);
		var len= 3;		
		for(var i=0;i<3;i++){
			oROd[i].checked = oROd[i].defaultChecked;
			oROs[i].checked = oROs[i].defaultChecked;
			oROd[i].disabled = false;
			oROs[i].disabled = false;
			if(wh == "OD"){
				oROs[i].disabled = true;
				oROs[i].checked = false;
			}else if(wh == "OS"){
				oROd[i].disabled = true;
				oROd[i].checked = false;
			}
		}
	}
	
	//
	if( typeof arrTestOd != "undefined" ){
		var len = arrTestOd.length;
		for(var i=0;i<len;i++ ){ 
			var od = document.getElementById(arrTestOd[i]);
			var str_val = arrTestOd[i];
			if(str_val.indexOf('od')!= -1){
				str_val = str_val.replace(/od/, "os");
			}else if(str_val.indexOf('Od')!= -1){
				str_val = str_val.replace(/Od/, "Os");
			}else{
				str_val = str_val.replace(/OD/, "OS");
			}
			var os = document.getElementById(str_val);
			
			if(od==null || os == null){continue;}		
			
			od.disabled = false;
			os.disabled = false;
			
			if((od.type == "checkbox") || (od.type == "radio")){
				if(od.checked==false) od.checked = od.defaultChecked;
				if(os.checked==false) os.checked = os.defaultChecked;
			}else{
				if(od.value=="") od.value = od.defaultValue;
				if(os.value=="") os.value = os.defaultValue;
			}			
			
			if((wh=="OD")){
				os.disabled = true;
				if((os.type == "checkbox") || (os.type == "radio")){
					os.checked = false;
				}else{
					os.value = "";
				}
			}else if((wh=="OS")){
				od.disabled = true;
				if((od.type == "checkbox") || (od.type == "radio")){
					od.checked = false;
				}else{
					od.value = "";
				}
			}
		}
	}
}

//Set OpNameId
function setOpNameId(tnm){
	if(typeof tnm == "undefined"){
		return;
	}	
	var osid = document.getElementById("elem_operatorId");
	var osname = document.getElementById("elem_operatorName");
	var otid = document.getElementById(tnm.replace(/Name/g,""));
	var otname = document.getElementById(tnm);
	
	if(osid && osname && otid && otname && (osid.value != "") && (osname.value != "")){		
		otid.value = osid.value;	
		otname.value = osname.value;
	}
}

function load_this_test_images(patient_id,test_table,test_id,test_type,gen_thumb,container_id){
	if(typeof(container_id)=='undefined') container_id = 0;
	if(typeof(top.show_loading_image)=='function'){
		top.show_loading_image('hide');
		top.show_loading_image('show','','Loading test images &amp; generating previews...');
	}
	$.ajax({url: "ajax.php?task=load_this_test_images&patient_id="+patient_id+"&test_table="+test_table+"&test_scan_edit_id_scan="+test_id+"&test_type="+test_type+"&gen_thumb="+gen_thumb+"&container_id="+container_id, success: function(r){ 
		if(container_id){
			$('#'+container_id).html(r);
		}else{
			$('#saved_tests_images').html(r);
			makeOrgangeBox();
		}
		if(typeof(top.show_loading_image)=='function') top.show_loading_image('hide');
	}});
}

function log_view_log_image(scan_id){
	$.ajax({url: "ajax.php?task=provider_view_log&scan_id="+scan_id, success: function(r){
		//alert(r);
	}});
}
/*$(document).bind('oB_init', function(event, id){alert(id);});*/
$(document).bind('oB_navigate',function(event, id){log_view_log_image(scan_id_array[id]);});

function del_test_scan_upload_image(scan_id,cnfrm){
	if(typeof(cnfrm)=='undefined'){
		if(typeof(top.del_test_scan_upload_image)=='function'){YsAction="window.top.del_test_scan_upload_image('"+scan_id+"',true)";}
		else if(typeof(top.fmain.del_test_scan_upload_image)=='function'){YsAction="window.top.fmain.del_test_scan_upload_image('"+scan_id+"',true)";}
		top.fancyConfirm('All the selected Image/PDF will be deleted. Press "<b>Yes</b>" to continue or "<b>No</b>" to stop.', '', YsAction);
		return false;
	}

	$.ajax({url: "ajax.php?task=del_test_scan_upload&scan_id="+scan_id, success: function(r){
		reloadTestImages();
	}});
}

function view_all_images(scan_id,test_table,destination){
	if(typeof(destination)=='undefined') destination = '';
	h = window.top.innerHeight-100;
	w = window.top.innerWidth-50;
	var features = 'left=0,top=0,width='+w+',height='+w+',menuBar=no,scrollBars=no,toolbar=no,resizable=1';
	url = 'test_image_viewer.php?scan_id='+scan_id+'&test_table='+test_table;
	if(destination==''){
		if(typeof(window.top.popup_win)!='undefined') window.top.popup_win(url,features);
		else if(typeof(window.opener.top.popup_win)!='undefined') window.opener.top.popup_win(url,features);
		else if(typeof(window.opener.opener.top.popup_win)!='undefined') window.opener.opener.top.popup_win(url,features);
	}else if (destination=='self'){window.location.href = url;}	
}

function init_page_display(){
	if(typeof(window.top.fmain)!='undefined'){var main_height = window.top.$('#fmain').height();}
	else if(typeof(window.opener.top.fmain)!='undefined'){var main_height = window.opener.top.$('#fmain').height();}
	
	var test_top_bar_h 	= $('.testtopbar').height();
	var saved_test_h	= parseInt((main_height-test_top_bar_h)/2);
	$('#saved_tests_container').height(saved_test_h);
	//alert(main_height);
	
}

var test_main_options_arr = new Array();
function make_printable_custom_test_data(tod,trd,ttd){
	/*
	tod	= test options data (top)   (always checkbox)
	trd	= test result data (middle)	 (mixed contents)
	ttd	= test treatment data (bottom)	(always checkbox)
	*/
	var childiFrame = document.getElementById("frm_custom_test_print"); 
	var innerDoc = childiFrame.contentDocument || childiFrame.contentWindow.document; 
	
		
	//if(tod!=''){ //TEST TOP OPTIONS
		var test_main_options_html = '<table style="width:100%"><tr>';
		$('table#test_main_options tr td').each(function(i,e) {
			ele_val 	= $(e).find('input[type="checkbox"]').prop('checked') ? ' (y)' : ' (n)';
			ele_text	= $(e).find('span.label_txt').text();
			test_main_options_html += '<td class="alignLeft nowrap">'+ ele_text+ele_val+ '</td>';
		});
		test_main_options_html += '</tr></table>';
		innerDoc.getElementById("test_main_options").innerHTML = test_main_options_html;
	//}
	
	//if(ttd!=''){ //TEST TREATMENT OPTIONS
		var test_treatment_options_html = '<table style="width:100%">';
		var ttd_td_cnt = 0;
		$('div#custom_treatment_prognosis div.row div').each(function(i,e) {
			ele_val 	= $(e).find('input[type="checkbox"]').prop('checked') ? ' (y)' : ' (n)';
			ele_text	= $(e).find('span.label_txt').text();
			if(ttd_td_cnt==0) {test_treatment_options_html += '<tr>'; ttd_tr = 'opened';}
			test_treatment_options_html += '<td style="width:33.3%" class="alignLeft nowrap">'+ ele_text+ele_val+ '</td>';
			ttd_td_cnt++;
			if(ttd_td_cnt==3) {test_treatment_options_html += '</tr>'; ttd_tr = 'closed';ttd_td_cnt=0;}
		});
		if(ttd_tr == 'opened') test_treatment_options_html += '</tr>';
		test_treatment_options_html += '</table>';
		innerDoc.getElementById("test_treatment_options").innerHTML = test_treatment_options_html;
	//}
	
	if(trd!=''){ //TEST RESULT INTERFACE
		var test_result_options_html = '';
		var ttd_td_cnt = 0;
		$('table#tbl_test_results tr').each(function(i,e) {
			if(i>0){
				label_txt = $(e).children('td.editable_label').text();
				test_result_options_html += '<tr><td style="width:120px" class="valignTop">'+label_txt+'</td><td class="valignTop" style="width:470px;">';

				//OD part
				if($(e).children('td[id^=row_od_]').get(0)){//row form elements
					od_td_id	= $(e).children('td[id^=row_od_]').prop('id');
					$('#'+od_td_id).children('div[id^='+od_td_id+'-con_]').each(function(j, elod){
						con_div_id_val 			= $(elod).prop('id');
						con_div_child_type 		= $('#'+con_div_id_val).children().prop('tagName');
						if(con_div_child_type != 'DIV'){
							con_div_child_id 		= $('#'+con_div_id_val).children().prop('id');
							con_div_child_inputtype	= $('#'+con_div_child_id).prop('type');
						}
						if(con_div_child_type=='TEXTAREA' || (con_div_child_type=='INPUT' && (con_div_child_inputtype=='text' || con_div_child_inputtype=='number')) || con_div_child_type=='SELECT'){
							test_result_options_html += ''+$('#'+con_div_child_id).val()+'<br />';							
						}else if(con_div_child_type=='DIV'){
                            var trd_od_input_cls = $('#'+con_div_id_val).children().children('div').find('input[type="checkbox"]').attr("name");
                            if(trd_od_input_cls.indexOf('txtmo') != -1){
                                test_result_options_html += '<table style="width:100%">';
                                $('#'+con_div_id_val).children().children('div').each(function(k, ele) {
                                    ele_val 	= $(ele).find('input[type="checkbox"]').prop('checked') ? ' (y)' : ' (n)';
                                    ele_text	= $(ele).find('span.label_txt').text();
                                    test_result_options_html += '<tr><td class="alignLeft nowrap">'+ ele_text+ele_val+ '</td></tr>';
                                });
                                test_result_options_html += '</table>';
                            } else {
                                test_result_options_html += '<table style="width:100%"><tr>';
                                $('#'+con_div_id_val).children().children('div').each(function(k, ele) {
                                    ele_val 	= $(ele).find('input[type="checkbox"]').prop('checked') ? ' (y)' : ' (n)';
                                    ele_text	= $(ele).find('span.label_txt').text();
                                    test_result_options_html += '<td class="alignLeft nowrap" style="width:15%">'+ ele_text+ele_val+ '</td>';
                                });
                                test_result_options_html += '</tr></table>';
                            }
						}
						
					});
				}
				test_result_options_html += '</td><td class="valignTop" style="width:470px;">';
				
				//OS part
				if($(e).children('td[id^=row_os_]').get(0)){//row form elements
					os_td_id	= $(e).children('td[id^=row_os_]').prop('id');
					$('#'+os_td_id).children('div[id^='+os_td_id+'-con_]').each(function(j, elos){
						con_div_id_val = $(elos).prop('id');
						con_div_child_type 		= $('#'+con_div_id_val).children().prop('tagName');
						if(con_div_child_type != 'DIV'){
							con_div_child_id 		= $('#'+con_div_id_val).children().prop('id');
							con_div_child_inputtype	= $('#'+con_div_child_id).prop('type');
						}
						if(con_div_child_type=='TEXTAREA' || (con_div_child_type=='INPUT' && (con_div_child_inputtype=='text' || con_div_child_inputtype=='number')) || con_div_child_type=='SELECT'){
							test_result_options_html += ''+$('#'+con_div_child_id).val()+'<br />';							
						}else if(con_div_child_type=='DIV'){
                            var trd_os_input_cls = $('#'+con_div_id_val).children().children('div').find('input[type="checkbox"]').attr("name");
                            if(trd_os_input_cls.indexOf('txtmo') != -1){
                                test_result_options_html += '<table style="width:100%">';
                                $('#'+con_div_id_val).children().children('div').each(function(k, ele) {
                                    ele_val 	= $(ele).find('input[type="checkbox"]').prop('checked') ? ' (y)' : ' (n)';
                                    ele_text	= $(ele).find('span.label_txt').text();
                                    test_result_options_html += '<tr><td class="alignLeft nowrap">'+ ele_text+ele_val+ '</td></tr>';
                                });
                                test_result_options_html += '</table>';
                            } else {
                                test_result_options_html += '<table style="width:100%"><tr>';
                                $('#'+con_div_id_val).children().children('div').each(function(k, ele) {
                                    ele_val 	= $(ele).find('input[type="checkbox"]').prop('checked') ? ' (y)' : ' (n)';
                                    ele_text	= $(ele).find('span.label_txt').text();
                                    test_result_options_html += '<td class="alignLeft nowrap" style="width:15%">'+ ele_text+ele_val+ '</td>';
                                });
                                test_result_options_html += '</tr></table>';
                            }
                            
                            
							
						}
						
					});
				}
				test_result_options_html += '</td></tr>';
			}
		});
		//alert(test_result_options_html);
		innerDoc.getElementById("tbl_test_results").innerHTML = test_result_options_html;
	}
	
	//--PROVIDER SIGNATURE
	innerDoc.getElementById("insert_sign_html_here").innerHTML = $('#pdf_signature_data_div').html();
	
	//CREATING HTMLFILE VIA AJAX CALL for the above rendered HTML
	var url = "test_template_custom_patient_print.php";
	var params = "custom_html_full="+escape(innerDoc.getElementById('body_main_content').innerHTML);
		params += "&final_html_file_name_path="+$('#hidd_final_html_file_name_path').val();
		params += "&final_html_test_images_data="+$('#hidd_images_data').val();
	$.post(url,params,function(d){
		console.log(d);
	});
	
}

function fill_saved_custom_test_data(tod,trd,ttd){
	/*
	tod	= test options data (top)   (always checkbox)
	trd	= test result data (middle)	 (mixed contents)
	ttd	= test treatment data (bottom)	(always checkbox)
	*/
	$.each(tod.split('&'), function (index, elem) {
	   vals = elem.split('=');
	   id_val = vals[0];
	   val_val= vals[1];
	   if(val_val=='1') $('#'+id_val).prop('checked',true);
	   else $('#'+id_val).prop('checked',false);
	});

	$.each(ttd.split('&'), function (index, elem) {
	   vals = elem.split('=');
	   id_val = vals[0];
	   val_val= vals[1];
	   if(val_val=='1') $('#'+id_val).prop('checked',true);
	   else $('#'+id_val).prop('checked',false);
	});
	
	$.each(trd.split('&'), function (index, elem) {
	   vals = elem.split('=');
	   id_val = vals[0];
	   val_val= decodeURIComponent((vals[1]+'').replace(/\+/g, '%20'));
	   //alert(id_val+' :: '+$('#'+id_val).prop('tagName'));
	   //if(val_val=='1') $('#'+id_val).prop('checked',true);
	   etn = $('#'+id_val).prop('tagName');
	   elt = $('#'+id_val).prop('type');
	   elv = $('#'+id_val).val();
	   if(etn == "INPUT" || etn == "SELECT" || etn == "TEXTAREA"){
			if(elt == "checkbox" || elt == "radio"){
				if(val_val==elv) $('#'+id_val).prop('checked',true); else $('#'+id_val).prop('checked',false);
			}else if(elt!='submit' && elt!='button'){
				if(etn == "TEXTAREA"){$('#'+id_val).val(val_val.replace(/<br>/g,"\n"));}
				//else if(etn == "INPUT" && elt == "number"){if(val_val > 0) {$('#'+id_val).parent().append('+');}}
				else {$('#'+id_val).val(val_val);}

			}
	   }
	});
	
	$('#frm_custom_test_print').load(function(){
		make_printable_custom_test_data(tod,trd,ttd);
    }); 
	//setTimeout(function(){make_printable_custom_test_data(tod,trd,ttd);},1000);
}

function calCulate(obj1, obj2, obj3, obj4, obj5, obj6, obj7){
	var flag = 0;
	var obk1 = parseFloat(document.getElementById(obj1).value);
	var obk2 = parseFloat(document.getElementById(obj2).value);
	var obk3 = parseFloat(document.getElementById(obj3).value);
	var obk4 = parseFloat(document.getElementById(obj4).value);

	if(isNaN(obk1)){	var obk1 = 0;	}
	if(isNaN(obk2)){	var obk2 = 0;	}
	if(isNaN(obk3)){	var obk3 = 0;	}
	if(isNaN(obk4)){	var obk4 = 0;	}

	if((obk1=='') || (obk1==0)){ flag+= 1; }
	if((obk2=='') || (obk2==0)){ flag+= 1; }
	if((obk3=='') || (obk3==0)){ flag+= 1; }
	if((obk4=='') || (obk4==0)){ flag+= 1; }

	if((obk1!='') && (obk3!='')){
		if(obk1>obk3){
			var cyl1 = obk1-obk3;
		}else{
			var cyl1 = obk3-obk1;
		}
		document.getElementById(obj5).value = cyl1;
		var ave = (obk1+obk3)/2;
		document.getElementById(obj7).value = ave;
	}

	if(flag<=0){
		// CYL -->
		if(obk1>obk3){
			var cyl1 = obk1-obk3;
			var cyl2 = obk2;
		}else{
			var cyl1 = obk3-obk1;
			var cyl2 = obk4;
		}

		if(cyl1!=0){
			document.getElementById(obj5).value = cyl1;
			if(document.getElementById(obj5+'H').value!=cyl1){
				document.getElementById(obj5).style.background="#FFFFFF";
			}
		}else{
			document.getElementById(obj5).value = '';
		}
//				alert(obk1+'-'+obk2+'-'+obk3+'-'+obk4+'-'+document.getElementById(obj5).value+'-'+document.getElementById(obj6).value+'-'+document.getElementById(obj7).value);
		if(cyl2!=0){
			document.getElementById(obj6).value = cyl2;
			if(document.getElementById(obj6+'H').value!=cyl2){
				document.getElementById(obj6).style.background="#FFFFFF";
			}
		}else{
			document.getElementById(obj6).value = '';
		}
		// CYL -->

		// AVE -->
		var ave = (obk1+obk3)/2;
		if(ave!=0){
			document.getElementById(obj7).value = ave;
			if(document.getElementById(obj7+'H').value!=ave){
				document.getElementById(obj7).style.background="#FFFFFF";
			}
		}
		// AVE -->
	}
}

function getFormulaValues(phyId, type, pId, fId){
	if(type=='OD'){
		var phyId = document.getElementById('performedByPhyOD').value;
		document.getElementById('physicianSelected').value = phyId;
	}else{
		var phyId = document.getElementById('performedByOS').value;
		document.getElementById('physicianSelectedOS').value = phyId;
	}
	
	for(var j=1; j<=4; j++){
		document.getElementById('iol'+j+type).value = '';
	}
	document.getElementById("selecedIOLs"+type).options.length = 0;
	document.getElementById('iol1Power'+type).value = '';
	document.getElementById('iol1Holladay'+type).value = '';
	document.getElementById('iol1srk_t'+type).value = '';
	document.getElementById('iol1Hoffer'+type).value = '';
	document.getElementById('iol2Power'+type).value = '';
	document.getElementById('iol2Holladay'+type).value = '';
	document.getElementById('iol2srk_t'+type).value = '';
	document.getElementById('iol2Hoffer'+type).value = '';
	document.getElementById('iol3Power'+type).value = '';
	document.getElementById('iol3Holladay'+type).value = '';
	document.getElementById('iol3srk_t'+type).value = '';
	document.getElementById('iol3Hoffer'+type).value = '';
	document.getElementById('iol4Power'+type).value = '';
	document.getElementById('iol4Holladay'+type).value = '';
	document.getElementById('iol4srk_t'+type).value = '';
	document.getElementById('iol4Hoffer'+type).value = '';

	// -- GET FORMULA VALUES FOR PROVIDER TO THE PATIENT 
	document.getElementById("selecedIOLs"+type).options.length = 0;
	
	js_url_prefix = '';
	if(typeof(top.JS_WEB_ROOT_PATH)!='undefined' && top.JS_WEB_ROOT_PATH!='') js_url_prefix = top.JS_WEB_ROOT_PATH+'/interface/tests/';
	$.get(js_url_prefix+"getFormulaValuesPhysician.php?phyId="+phyId+"&type="+type+"&pId="+pId+"&fId="+fId, function(result, status){
        //alert("Result: " + result + "\nStatus: " + status);
			var len = result.length;
			for(var j=0; j<len; j++){
				var flag=0;
				do{
					++flag;
					var r = result.indexOf(",");
					var newResult = result.substr(0,r);
					var result = result.substr(r+1);
					switch(flag){
						case 1:{
								if(newResult.indexOf("!*!")!=-1){
									n = newResult.indexOf("!*!");
									newResult1 = newResult.substr(0,n);
									newResult2 = newResult.substr(n+3);
									if(newResult2!=''){
										document.getElementById('iol1'+type).value = newResult1;
										document.getElementById('selecedIOLs'+type).options[1] = new Option(newResult1, newResult2);
									}
								}
								break;
						}
						case 2:{
								document.getElementById('iol1Power'+type).value = newResult;
								break;
						}
						case 3:{
								document.getElementById('iol1Holladay'+type).value = newResult;
								break;
						}
						case 4:{
								document.getElementById('iol1srk_t'+type).value = newResult;
								break;
						}
						case 5:{
								document.getElementById('iol1Hoffer'+type).value = newResult;
								break;
						}
						case 6:{
								if(newResult.indexOf("!*!")!=-1){
									n = newResult.indexOf("!*!");
									newResult1 = newResult.substr(0,n);
									newResult2 = newResult.substr(n+3);
									if(newResult2!=''){
										document.getElementById('iol2'+type).value = newResult1;
										document.getElementById('selecedIOLs'+type).options[2] = new Option(newResult1, newResult2);
									}
								}
								break;
						}
						case 7:{
								document.getElementById('iol2Power'+type).value = newResult;
								break;
						}
						case 8:{
								document.getElementById('iol2Holladay'+type).value = newResult;
								break;
						}
						case 9:{
								document.getElementById('iol2srk_t'+type).value = newResult;
								break;
						}
						case 10:{
								document.getElementById('iol2Hoffer'+type).value = newResult;
								break;
						}
						case 11:{
								if(newResult.indexOf("!*!")!=-1){
									n = newResult.indexOf("!*!");
									newResult1 = newResult.substr(0,n);
									newResult2 = newResult.substr(n+3);
									if(newResult2!=''){
										document.getElementById('iol3'+type).value = newResult1;
										document.getElementById('selecedIOLs'+type).options[3] = new Option(newResult1, newResult2);
									}
								}
								break;
						}
						case 12:{
								document.getElementById('iol3Power'+type).value = newResult;
								break;
						}
						case 13:{
								document.getElementById('iol3Holladay'+type).value = newResult;
								break;
						}
						case 14:{
								document.getElementById('iol3srk_t'+type).value = newResult;
								break;
						}
						case 15:{
								document.getElementById('iol3Hoffer'+type).value = newResult;
								break;
						}
						case 16:{
								if(newResult.indexOf("!*!")!=-1){
									n = newResult.indexOf("!*!");
									newResult1 = newResult.substr(0,n);
									newResult2 = newResult.substr(n+3);
									if(newResult2!=''){
										document.getElementById('iol4'+type).value = newResult1;
										document.getElementById('selecedIOLs'+type).options[4] = new Option(newResult1, newResult2);
									}
								}
								break;
						}
						case 17:{
								document.getElementById('iol4Power'+type).value = newResult;
								break;
						}
						case 18:{
								document.getElementById('iol4Holladay'+type).value = newResult;
								break;
						}
						case 19:{
								document.getElementById('iol4srk_t'+type).value = newResult;
								break;
						}
						case 20:{
								document.getElementById('iol4Hoffer'+type).value = newResult;
								break;
						}
						case 21:{
								if(newResult!=''){
									document.getElementById('cellCount'+type).value = newResult;
								}
								break;
						}
						case 22:{
								if(newResult!=''){
									document.getElementById('pachymetryVal'+type).value = newResult;
								}
								break;
						}
						case 23:{
								if(newResult!=''){
									document.getElementById('pachymetryCorrec'+type).value = newResult;
								}
								break;
						}
						case 24:{
								if(newResult!=''){
									document.getElementById('cornealDiam'+type).value = newResult;
								}
								break;
						}
						case 25:{
								if(newResult!=''){
									document.getElementById('dominantEye'+type).value = newResult;
								}
								break;
						}
						case 26:{
								if(newResult!=''){
									document.getElementById('pupilSize1'+type).value = newResult;
								}
								break;
						}
						case 27:{
								if(newResult!=''){
									document.getElementById('pupilSize2'+type).value = newResult;
								}
								break;
						}
						case 28:{
								document.getElementById('notes'+type).value = newResult;
								break;
						}
					}
				}while((result.indexOf(","))!=-1);
			}
			if(type=='OD'){ var c = "#0000FF";}else{ var c = "#009900";}
			var selPhyLen = document.getElementById("selecedIOLs"+type).options.length;
			for(i=1; i<selPhyLen;i++){
				if((result!=0) && (document.getElementById('selecedIOLs'+type).options[i].value!=0)){
					if(document.getElementById('selecedIOLs'+type).options[i].text==result){
						document.getElementById('selecedIOLs'+type).options[i].selected = "selected";
						document.getElementById('selecedIOLs'+type).options[i].style.color = c;
						if(document.getElementById('selecedIOLs'+type).options[i].selected == true){
							break;
						}
					}
				}
			}
		});
		//end of ajax
	return;
}

function makeEmpty(obj){
	var str = trim(obj.value);
	if(str=='Notes...'){
		obj.value='';
	}
}

function checkIolVal(obj){
	var chkVal = document.getElementById(obj).value;
	if(chkVal==''){
		return false;
	}
}

function isValid(strVal){
	var bag = "0123456789,";
	var strLength = strVal.length;
	var chr;
	for(i=0;i<strLength;i++){
		chr = strVal.charAt(i);
		if(bag.indexOf(chr) == -1){
			return false;
		}
	}
	return true;
}

function getCorrectionValue(val){
	var maxVal,minVal,corVal,maxCorVal,minCorVal;
	if((val < 445) || (val > 645)){
		fAlert("Average Value is out of the range of Correction Table (445 - 645).");
	}else if((val >= 445) && (val < 455)){
		maxCorVal = 7;
		minCorVal = 6;
		maxVal = 445;
		minVal = 455;
	}else if((val >= 455) && (val < 465)){
		maxCorVal = 6;
		minCorVal = 6;
		maxVal = 455;
		minVal = 465;
	}else if((val >= 465) && (val < 475)){
		maxCorVal = 6;
		minCorVal = 5;
		maxVal = 465;
		minVal = 475;
	}else if((val >= 475) && (val < 485)){
		maxCorVal = 5;
		minCorVal = 4;
		maxVal = 475;
		minVal = 485;
	}else if((val >= 485) && (val < 495)){
		maxCorVal = 4;
		minCorVal = 4;
		maxVal = 485;
		minVal = 495;
	}else if((val >= 495) && (val < 505)){
		maxCorVal = 4;
		minCorVal = 3;
		maxVal = 495;
		minVal = 505;
	}else if((val >= 505) && (val < 515)){
		maxCorVal = 3;
		minCorVal = 2;
		maxVal = 505;
		minVal = 515;
	}else if((val >= 515) && (val < 525)){
		maxCorVal = 2;
		minCorVal = 1;
		maxVal = 515;
		minVal = 525;
	}else if((val >= 525) && (val < 535)){
		maxCorVal = 1;
		minCorVal = 1;
		maxVal = 525;
		minVal = 535;
	}else if((val >= 535) && (val < 545)){
		maxCorVal = 1;
		minCorVal = 0;
		maxVal = 545;
		minVal = 535;
	}else if((val >= 545) && (val < 555)){
		maxCorVal = 0;
		minCorVal = -1;
		maxVal = 545;
		minVal = 555;
	}else if((val >= 555) && (val < 565)){
		maxCorVal = -1;
		minCorVal = -1;
		maxVal = 555;
		minVal = 565;
	}else if((val >= 565) && (val < 575)){
		maxCorVal = -1;
		minCorVal = -2;
		maxVal = 565;
		minVal = 575;
	}else if((val >= 575) && (val < 585)){
		maxCorVal = -2;
		minCorVal = -3;
		maxVal = 575;
		minVal = 585;
	}else if((val >= 585) && (val < 595)){
		maxCorVal = -3;
		minCorVal = -4;
		maxVal = 585;
		minVal = 595;
	}else if((val >= 595) && (val < 605)){
		maxCorVal = -4;
		minCorVal = -4;
		maxVal = 595;
		minVal = 605;
	}else if((val >= 605) && (val < 615)){
		maxCorVal = -4;
		minCorVal = -5;
		maxVal = 605;
		minVal = 615;
	}else if((val >= 615) && (val < 625)){
		maxCorVal = -5;
		minCorVal = -6;
		maxVal = 615;
		minVal = 625;
	}else if((val >= 625) && (val < 635)){
		maxCorVal = -6;
		minCorVal = -6;
		maxVal = 625;
		minVal = 635;
	}else if((val >= 635) && (val <= 645)){
		maxCorVal = -6;
		minCorVal = -7;
		maxVal = 635;
		minVal = 645;
	}
	corVal = refineCorrectionValue(maxCorVal,minCorVal,maxVal,minVal,val);
	return corVal;
}

function refineCorrectionValue(mxc,mnc,mx,mn,av){
	var totalVal = parseInt(mx) + parseInt(mn);
	var avgVal = parseInt(totalVal/2);
	if(av >= avgVal){
		return mnc;
	}else if(av < avgVal){
		return mxc;
	}
}

function doSelectOD(obj){
	var objLen = obj.options.length;
	for(var i=0; i<objLen; i++){
		obj.options[i].style.color="#000000";
	}
	var ele = obj.selectedIndex;
	obj.options[ele].style.fontWeight="900";
	obj.options[ele].style.color="#0000FF";

////////////////////////////////////////////////////////////////////////////
	for(i=1; i<=4; i++){
		document.getElementById("iolOD"+i).style.background="#d9e4f2";
	}
	var iol1OD = document.getElementById("iol1OD").value;
	var iol2OD = document.getElementById("iol2OD").value;
	var iol3OD = document.getElementById("iol3OD").value;
	var iol4OD = document.getElementById("iol4OD").value;
	var selectedText = obj.options[ele].text;
	if(selectedText == iol1OD){
		if(selectedText!=''){
				document.getElementById("iolOD1").style.background="#0000FF";
		}
	}
	if(selectedText == iol2OD){
		if(selectedText!=''){
			document.getElementById("iolOD2").style.background="#0000FF";
		}
	}
	if(selectedText == iol3OD){
		if(selectedText!=''){
			document.getElementById("iolOD3").style.background="#0000FF";
		}
	}
	if(selectedText == iol4OD){
		if(selectedText!=''){
			document.getElementById("iolOD4").style.background="#0000FF";
		}
	}
	//document.getElementById("iol45").style.background="#d9e4f2";
////////////////////////////////////////////////////////////////////////////
}
function doSelectOS(obj){
	var objLen = obj.options.length;
	for(var i=0; i<objLen; i++){
		obj.options[i].style.color="#000000";
	}

	var ele = obj.selectedIndex;
	obj.options[ele].style.fontWeight="900";
	obj.options[ele].style.color="#009900";

////////////////////////////////////////////////////////////////////////////
	for(i=1; i<=4; i++){
		document.getElementById("iolOS"+i).style.background="#d9e4f2";

	}
	var iol1OS = document.getElementById("iol1OS").value;
	var iol2OS = document.getElementById("iol2OS").value;
	var iol3OS = document.getElementById("iol3OS").value;
	var iol4OS = document.getElementById("iol4OS").value;
	var selectedText = obj.options[ele].text;
	if(selectedText == iol1OS){
		if(selectedText!=''){
			document.getElementById('iolOS1').style.background="#009900";
		}
	}
	if(selectedText == iol2OS){
		if(selectedText!=''){
			document.getElementById('iolOS2').style.background="#009900";
		}
	}
	if(selectedText == iol3OS){
		if(selectedText!=''){
			document.getElementById('iolOS3').style.background="#009900";
		}
	}
	if(selectedText == iol4OS){
		if(selectedText!=''){
			document.getElementById('iolOS4').style.background="#009900";
		}
	}
	////////////////////////////////////////////////////////////////////////////
}

function showHide(tr, obj, tr2){
	var chkStatus = document.getElementById(obj).checked;
	if(chkStatus==true){
		$('#'+tr).show();
	}else{
		$('#'+tr2).hide();
		$('#'+tr).hide();
	}
}
function showCutsChk(tr, obj){
	var cutsValue = document.getElementById(obj).value;
	if(cutsValue==1){
		$('#'+tr).show();
	}else{
		$('#'+tr).hide();
	}
}

//Clear Record of Testing Exams from database
//if user select reset button
function resetTestExam(flgPurge,cnfrm){
	var purg = (typeof(flgPurge)!="undefined" && flgPurge==1) ? 1 : 0;
	var purgeStatus=0;

	//Edit Mode
	var oEdMode = document.getElementById("elem_edMode");
	if(oEdMode){
		if(oEdMode.value == "new"){
			//Empty All Values
			if(purg==0) funReset();
			return;
		}
	}
	
	//Checks	
	if(purg==0){
		if(typeof(cnfrm)=="undefined"){
			yesFunc = "window.top.fmain.resetTestExam('"+flgPurge+"',true)";
			if(typeof(top.fmain)=='undefined'){yesFunc = "window.top.resetTestExam('"+flgPurge+"',true)";}
			top.fancyConfirm("Are you sure you want to reset? <br />All saved data will be lost for this test.",'',yesFunc);
			return;
		}
	}

	//Purge
	if(purg==1){
		if(top.$("#btnPurge").val()=="UndoPurge"){
			purgeStatus=0;
		}else{
			purgeStatus=1;
		}
	}

	//TestName
	var oTestName = document.getElementById("elem_saveForm");
	var vTestName = "";	
	if(oTestName && $.trim(oTestName.value) != ""){
		vTestName = $.trim(oTestName.value);
	}
	if(vTestName == ""){
		return;
	}

	//Form id
	var oFrmId = document.getElementById("elem_formId");
	var vFrmId = "";
	if(oFrmId && (typeof oFrmId.value != "undefined")){
		vFrmId = oFrmId.value;
	}	
	if($.trim(vFrmId) == ""){
		return;
	}
		
	//Test Id --
	var oTestId = document.getElementById("elem_testId");
	var vTestId = "";
	if(oTestId && $.trim(oTestId.value) != ""){
		vTestId = $.trim(oTestId.value);
	}
	if(vTestId == ""){
		return;
	}
	//Test Id --
	
	//Test Master ID (tests_name table)
	var oTestMainId = document.getElementById("elem_tests_name_id");
	var vTestMainId = "";
	if(oTestMainId && $.trim(oTestMainId.value) != ""){
		vTestMainId = $.trim(oTestMainId.value);
	}
	if(vTestMainId == ""){
		return;
	}
	//Test Master ID
	
	
	//Define Var -----
	var url = "ajax.php";
	var params = "task=resetTestForm";
	params += "&elem_formId="+vFrmId;
	params += "&elem_testId="+vTestId;
	params += "&elem_testName="+vTestName;
	params += "&elem_purge="+purg;
	params += "&elem_purgeStatus="+purgeStatus;
	params += "&elem_tests_name_id="+vTestMainId;
	// Add form Id
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","superbill");

	//-------------------------------------------	
	$.post(url,params,
	function(data){
	//------  processing after connection   ----------
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","superbill");
		var xmlDoc = data;
		if(0 == ""+xmlDoc){
					
			if(purg==0){
				//Empty All Values
				//funReset();
				//if(oEdMode){oEdMode.value = "new";}
				a = window.location.href;
				a = a.split('?');
				if(typeof(top.fmain)=='undefined') a[0] = a[0]+'?pop=1';
				window.location.href = a[0];
			}else if(purg==1){
				a = window.location.href;
				window.location.href = a;
			}
			
			//hide Slider Flag
			if(vTestName == "discExternal"){ vTestName = "External"; }
			if(vTestName == "NFA"){ vTestName = "HRT"; }
			if(window.opener){
				if(typeof(window.opener.loadSlider) != 'undefined'){window.opener.loadSlider(1);}
				else if(window.opener.opener && typeof(window.opener.opener.loadSlider) != 'undefined'){window.opener.opener.loadSlider(1);}
				
				if(typeof window.opener.addActiveExam != "undefined"){
					window.opener.addActiveExam(vTestName);
				}else if((window.opener.opener) && (typeof window.opener.opener.addActiveExam != "undefined")){
					window.opener.opener.addActiveExam(vTestName);
				}
			}
		}
		else if(2 == ""+xmlDoc){
			top.fAlert("This test can not be unpurged.");
		}else{
			alert("Exception encounterd: "+xmlDoc);
		}
	});	
}

function funReset(){
	// if View Only Access 
	if(elem_per_vo == "1"){ return; }
	
	var o = top.fmain.test_form_frm.elements;
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

function test_image_slideshow(test_table, tId, test_type, patient_id){
	//top.show_loading_image('show', '', 'Processing. Please hold a moment...');
	botButton = '<button type="button" class="btn btn-success" data-dismiss="modal">Close</button>';
	param 	= 'task=test_image_slideshow&test_table='+test_table+'&tId='+tId+'&test_type='+test_type+'&patient_id='+patient_id;
	html = '<iframe frameborder="0" width="100%" height="500" src="../../library/jssor/slideshow.php?'+param+'"></iframe>';
	//$.get( "ajax.php?"+param, function(r){
	  top.show_modal('TestImages','Test Image Slideshow Viewer',html,botButton,'550','modal-lg');
	  //top.show_loading_image('hide');
	//});
}

function opPag(){
	if(typeof(top.icon_popups)!="undefined"){ top.icon_popups('pt_at_glance');  }
	else if(window.opener && typeof(window.opener.top.icon_popups) !="undefined"){ window.opener.top.icon_popups('pt_at_glance'); }
}

function op_synergy(usr, pt_fnm, pt_lnm, pt_dob){
	var features = 'left=200,top=10,width=700,height=650,menuBar=no,scrollBars=no,toolbar=no,resizable=1';
	var url = "https://usct010101app01.topconsynergy.com/usct0101/SynergySL.aspx#lt=1&u="+encodeURI(usr)+"&ln="+encodeURI(pt_lnm)+"&fn="+encodeURI(pt_fnm)+"&dob="+encodeURI(pt_dob)+"";
	if(typeof(window.top.popup_win)!='undefined'){ window.top.popup_win(url,features); }
	else if(window.opener && typeof(window.opener.top.icon_popups) !="undefined"){ window.opener.top.popup_win(url,features); }	
}

function setPrevValues(wh){
	var t = location.search;
	var p = location.pathname;

	var y="";
//	if(t.indexOf("noP=1&pop=0") != -1) y+="&noP=1&pop=0";

	if(wh == "+1"){
		if(t_nxtid != "" && !isNaN(t_nxtid)){
			y+=t+"&tId="+t_nxtid;
			window.location.replace(p+y);
		}else{
			if(typeof(fAlert)=='function'){
				fAlert("No Next record exists.");
			}else{
				alert("No Next record exists.");	
			}
		}
	}else if(wh == "-1"){
		if(t_previd != "" && !isNaN(t_previd)){
			y=t+"&tId="+t_previd;			
			window.location.replace(p+y);
		}else{
			if(typeof(fAlert)=='function'){
				fAlert("No Previous record exists.");
			}else{
				alert("No Previous record exists.");	
			}
		}
	}else{
		window.location.replace(p+""+t+"&prevVal=1");
	}
}