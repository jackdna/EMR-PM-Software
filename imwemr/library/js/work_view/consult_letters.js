function selectTempFn(){
	var chkPCP = chkRefPhy = chkCoManPhy = txtOther = cbkOther = url = hiddtxtOther = hiddtxtOtherFax = "";
	var chkPCP_hidd = chkRefPhy_hidd = chkCoManPhy_hidd = hiddfirstSelected = "";
	var hiddselectReferringPhy =  hiddselectReferringPhyCc1 = selectReferringPhyCc1 = send_fax_numberCc1 = "";
	var hiddselectReferringPhyCc2 = selectReferringPhyCc2 = send_fax_numberCc2 = "";
	var hiddselectReferringPhyCc3 = selectReferringPhyCc3 = send_fax_numberCc3 = "";
	var Addressee = AddresseeOther = cc1 = cc1Other = cc2 = cc2Other = cc3 = cc3Other = "";
	var hidd_AddresseeOther = hidd_cc1Other = hidd_cc2Other = hidd_cc3Other = "";
	var tempId = document.getElementById('templateList').value;
	var patientConsultId = document.getElementById('patient_consult_id').value;
	document.getElementById('templateId').value = tempId;	
	if(document.getElementById('templateList').value == 'Other'){
		//document.getElementById('tempNameTd').style.display = 'block';
		$("#tempNameTd").removeClass("hidden");
		//document.getElementById('tempNameTdLabel').style.display = 'block';		
		document.getElementById('patientTempName').value = '';
		//window.location = 'template.php?selectedList=Other';
		return true;
	}else if(document.getElementById('templateList').value != 'Other'){
		//document.getElementById('tempNameTd').style.display = 'none';
		$("#tempNameTd").addClass("hidden");
		//document.getElementById('tempNameTdLabel').style.display = 'none';
		document.getElementById('patientTempName').value = '';
	}
	var items=[];
	$('#chkPCP option:selected').each(function(){ items.push($(this).val());});
	$('#chkRefPhy option:selected').each(function(){ items.push($(this).val()); });
	$('#chkCoManPhy option:selected').each(function(){ items.push($(this).val()); });
	
	 if(document.getElementById('chkPCP')){
		if(document.getElementById('chkPCP').checked == true){
			chkPCP = document.getElementById('chkPCP').value;
			chkPCP_hidd = document.getElementById('chkPCP_hidd').value;
		}
	}	
	if(document.getElementById('chkRefPhy')){
		if(document.getElementById('chkRefPhy').checked == true){
			chkRefPhy = document.getElementById('chkRefPhy').value;
			chkRefPhy_hidd = document.getElementById('chkRefPhy_hidd').value;
		}
	}	
	if(document.getElementById('chkCoManPhy')){
		if(document.getElementById('chkCoManPhy').checked == true){
			chkCoManPhy = document.getElementById('chkCoManPhy').value;
			chkCoManPhy_hidd = document.getElementById('chkCoManPhy_hidd').value;
		}
	} 
	if(document.getElementById('cbkOther')){	
		if(document.getElementById('cbkOther').checked == true){	
			cbkOther = document.getElementById('cbkOther').value;
			txtOther = document.getElementById('txtOther').value;
			hiddtxtOther = document.getElementById('hiddtxtOther').value;
			hiddtxtOtherFax = document.getElementById('hiddtxtOtherFax').value;
			hiddselectReferringPhy = document.getElementById('hiddselectReferringPhy').value;
		}
	}
	
    if(document.getElementById('hiddselectReferringPhyCc1')){
        hiddselectReferringPhyCc1 = document.getElementById('hiddselectReferringPhyCc1').value;
    }
    if(document.getElementById('selectReferringPhyCc1')){
        selectReferringPhyCc1 = document.getElementById('selectReferringPhyCc1').value;
    }
    if(document.getElementById('send_fax_numberCc1')){
        send_fax_numberCc1 = document.getElementById('send_fax_numberCc1').value;
    }
	if(document.getElementById('hiddselectReferringPhyCc2')){
        hiddselectReferringPhyCc2 = document.getElementById('hiddselectReferringPhyCc2').value;
    }
    if(document.getElementById('selectReferringPhyCc2')){
        selectReferringPhyCc2 = document.getElementById('selectReferringPhyCc2').value;
    }
    if(document.getElementById('send_fax_numberCc2')){
        send_fax_numberCc2 = document.getElementById('send_fax_numberCc2').value;
    }
	if(document.getElementById('hiddselectReferringPhyCc3')){
        hiddselectReferringPhyCc3 = document.getElementById('hiddselectReferringPhyCc3').value;
    }
    if(document.getElementById('selectReferringPhyCc3')){
        selectReferringPhyCc3 = document.getElementById('selectReferringPhyCc3').value;
    }
    if(document.getElementById('send_fax_numberCc3')){
        send_fax_numberCc3 = document.getElementById('send_fax_numberCc3').value;
    }
    if(document.getElementById('hiddfirstSelected')){
        hiddfirstSelected = document.getElementById('hiddfirstSelected').value;
    }

	if(document.getElementById('Addressee'))			{ Addressee 			= document.getElementById('Addressee').value;			}
	if(document.getElementById('AddresseeOther'))		{ AddresseeOther		= document.getElementById('AddresseeOther').value;		}
	if(document.getElementById('cc1'))					{ cc1 					= document.getElementById('cc1').value;					}
	if(document.getElementById('cc1Other'))				{ cc1Other 				= document.getElementById('cc1Other').value;			}
	if(document.getElementById('cc2'))					{ cc2 					= document.getElementById('cc2').value;					}
	if(document.getElementById('cc2Other'))				{ cc2Other 				= document.getElementById('cc2Other').value;			}
	if(document.getElementById('cc3'))					{ cc3 					= document.getElementById('cc3').value;					}
	if(document.getElementById('cc3Other'))				{ cc3Other 				= document.getElementById('cc3Other').value;			}
	if(document.getElementById('hidd_AddresseeOther'))	{ hidd_AddresseeOther 	= document.getElementById('hidd_AddresseeOther').value;	}
	if(document.getElementById('hidd_cc1Other'))		{ hidd_cc1Other 		= document.getElementById('hidd_cc1Other').value;		}
	if(document.getElementById('hidd_cc2Other'))		{ hidd_cc2Other 		= document.getElementById('hidd_cc2Other').value;		}
	if(document.getElementById('hidd_cc3Other'))		{ hidd_cc3Other 		= document.getElementById('hidd_cc3Other').value;		}
	if(tempId!=''){
		url = zPath+'/chart_notes/onload_wv.php?elem_action=Consult_letters&templateList='+tempId+"&patient_consult_id="+patientConsultId+"&chkPCP="+chkPCP+"&chkPCP_hidd="+chkPCP_hidd+"&chkRefPhy="+chkRefPhy+"&chkRefPhy_hidd="+chkRefPhy_hidd+"&chkCoManPhy="+chkCoManPhy+"&chkCoManPhy_hidd="+chkCoManPhy_hidd+"&cbkOther="+cbkOther+"&txtOther="+txtOther+"&hiddtxtOther="+hiddtxtOther+"&hiddtxtOtherFax="+hiddtxtOtherFax;		
		url=url+"&hiddselectReferringPhy="+hiddselectReferringPhy;
		url=url+"&hiddselectReferringPhyCc1="+hiddselectReferringPhyCc1+"&selectReferringPhyCc1="+selectReferringPhyCc1+"&send_fax_numberCc1="+send_fax_numberCc1;
		url=url+"&hiddselectReferringPhyCc2="+hiddselectReferringPhyCc2+"&selectReferringPhyCc2="+selectReferringPhyCc2+"&send_fax_numberCc2="+send_fax_numberCc2;
		url=url+"&hiddselectReferringPhyCc3="+hiddselectReferringPhyCc3+"&selectReferringPhyCc3="+selectReferringPhyCc3+"&send_fax_numberCc3="+send_fax_numberCc3;
		url=url+"&Addressee="+Addressee;
		url=url+"&AddresseeOther="+AddresseeOther;
		url=url+"&cc1="+cc1;
		url=url+"&cc1Other="+cc1Other;
		url=url+"&cc2="+cc2;
		url=url+"&cc2Other="+cc2Other;
		url=url+"&cc3="+cc3;
		url=url+"&cc3Other="+cc3Other;
		url=url+"&hidd_AddresseeOther="+hidd_AddresseeOther;
		url=url+"&hidd_cc1Other="+hidd_cc1Other;
		url=url+"&hidd_cc2Other="+hidd_cc2Other;
		url=url+"&hidd_cc3Other="+hidd_cc3Other;
		url=url+"&hiddfirstSelected="+hiddfirstSelected;
		url=url+"&items="+items;
		window.location = url;
	}
}

//START FUNCTION FOR SCAN
var oPO;
if(window.opener){
oPO = window.opener.oPO;
}else{
oPO = window.oPO;	
}
var thisoPO = new Array();
/*
function openScan(show, fId, formNme){
	if( typeof(oPO) == "undefined" ){		
		oPO = thisoPO;
	}
	
	if( show == "delete" ){
		if(elem_per_vo=="1"){ return; } // if View Only Access  		
		deleteTestScan(formNme,""+form_id);
		
	}else if( show != "preview" ){
		if(elem_per_vo=="1"){ return; } // if View Only Access
		
		var n = "scanImgConsultLetter"+form_id;
		var features = 'left=200,top=75,width=1000,height=750,menuBar=no,scrollBars=no,toolbar=no,resizable=1';
		oPO[n] = window.open('scanImages.php?formName='+formNme+'&show='+show+'&formId='+form_id,'Scaner', features);
		oPO[n].focus();
		
	}else{ // Preview
		if(typeof fId != "undefined"){ //drop down
			if(fId){				
				var n = (typeof fId != "undefined") ? "scanShowv_f"+fId : "scanShowv_f"+""+form_id;
				var formId = (typeof fId != "undefined") ? ""+fId : ""+form_id ;
				
				if(!oPO[n] || !(oPO[n].open) || (oPO[n].closed == true)){		
					var url = "logoImgWrapper.php?formName="+formNme+"&formId="+formId;
					var height = document.body.clientHeight;
					var width = parseInt(document.body.clientWidth) * 30/100;
					var cont_wind_opn=document.getElementById('wind_opn').value;
					document.getElementById('wind_opn').value=parseInt(cont_wind_opn)+100;
					var top =200;
					if((parseInt(cont_wind_opn)+100) > document.body.clientWidth){
						var left =1;
						document.getElementById('wind_opn').value=0;
					}else{
						var left =1+parseInt(cont_wind_opn);
					}
					oPO[n] = window.open(url,"","width="+width+",height="+height+",resizable=1,scrollbars=1,top="+top+",left="+left);				
				}
				oPO[n].focus();
			}
		}else{
			var n = (typeof fId != "undefined") ? "scanShowv_f"+fId : "scanShowv_f"+""+form_id;
			var formId = (typeof fId != "undefined") ? ""+fId : ""+form_id ;
			if(!oPO[n] || !(oPO[n].open) || (oPO[n].closed == true)){		
				var url = "logoImgWrapper.php?formName="+formNme+"&formId="+formId;
				var height = document.body.clientHeight;
				var width = parseInt(document.body.clientWidth) * 30/100;
				var cont_wind_opn=document.getElementById('wind_opn').value;
				document.getElementById('wind_opn').value=parseInt(cont_wind_opn)+100;
				var top =200;
				if(parseInt(parseInt(cont_wind_opn)+100) > document.body.clientWidth){
					var left =1;
					document.getElementById('wind_opn').value=0;
				}else{
					var left =1+parseInt(cont_wind_opn);
				}
				oPO[n] = window.open(url,"","width="+width+",height="+height+",resizable=1,scrollbars=1,top="+top+",left="+left);				
			}
			oPO[n].focus();
		}
	}
	
}
*/
function selDeSelAllChkBox(obj,faxNo){
	var fieldID = obj.id;
	var control; 		
	if(document.getElementById(fieldID).checked == true){
		for (var i = 0; i < document.patientConsultLetter.elements.length; i++) { 
			control = document.patientConsultLetter.elements[i]; 		
			switch (control.type) { 
				case 'checkbox': 				
					control.checked = false;
				break; 					
			}
		}				
		document.getElementById(fieldID).checked = true;												
	}else{
		document.getElementById("send_fax_number").value='';
		document.getElementById("selectReferringPhy").value='';
	}
	if(faxNo){
		var getPhyFAX="";	
		var getPhyID="";
		var getPhyVal="";
		if(faxNo!="" && document.getElementById(fieldID).checked == true){
			getPhyVal=faxNo.split("@@");
			getPhyID=getPhyVal[0];
			getPhyFAX=getPhyVal[1];
			document.getElementById("send_fax_number").value=getPhyFAX;
			document.getElementById("selectReferringPhy").value=getPhyID;
		}
	}else{	
		var getArrPhyNameVal="";
		var getArrPhyID="";
		var getArrPhyFAX="";
		var arrPhyNameVal="";
		if(obj.value=="cbkOther" && document.getElementById(fieldID).checked == true){
			var otherPhyID=document.getElementById("hiddtxtOther").value;
			var other_txt_ref_name=$("#txtOther").val();
			var other_txt_fax_no=$("#hiddtxtOtherFax").val();	
			if(otherPhyID!=""){
				arrPhyNameVal=arrRefIdNameFax[otherPhyID];
				if((arrPhyNameVal!="")&&(arrPhyNameVal)){
					getArrPhyNameVal=arrPhyNameVal.split("@@");
					getArrPhyName=getArrPhyNameVal[0];
					getArrPhyFAX=getArrPhyNameVal[1];
					document.getElementById("hiddselectReferringPhy").value=otherPhyID;
					document.getElementById("send_fax_number").value=getArrPhyFAX;
					document.getElementById("selectReferringPhy").value=getArrPhyName;	
				}else{
					document.getElementById("hiddselectReferringPhy").value=otherPhyID;
					document.getElementById("send_fax_number").value=other_txt_fax_no;
					document.getElementById("selectReferringPhy").value=other_txt_ref_name;
				}
				
			}	
			if(other_txt_ref_name!=""){
				$("#selectReferringPhy").val(other_txt_ref_name);
			}
			if(other_txt_fax_no){
				$("#send_fax_number").val(other_txt_fax_no);	
			}
		}
	}	
}
//END FUNCTION FOR SCAN
//=== Function fire on onchange the Ref. Phy Select box in send fax div==//
/*
function setFaxNumber(faxObj){
	var getRefPhyDetail="";
	var getRefPhyFax="";
	var	getRefPhyName="";
	var getObjVal=arrRefIdNameFax[faxObj.value];
	if(faxObj.value!="" && faxObj.value!="-"){
		getRefPhyDetail=getObjVal.split("@@");
		getRefPhyID=getRefPhyDetail[0];
		getRefPhyFAX=getRefPhyDetail[1];
		if(getRefPhyFAX){
			document.getElementById("send_fax_number").value=getRefPhyFAX;	
		}else{
			document.getElementById("send_fax_number").value='';
		}
	}else{
		document.getElementById("send_fax_number").value="";
	}
}
*/
//== End Function ===//

//=== Function fire on blur the "Other Ref Phy(Last, First Name)" Text Box ==//
/*
function GetFaxValOnText(strVal){
	var getRefPhyDetail="";
	var getRefPhyID=""; 
	var getRefPhyFAX="";
	var getArrPhyVal="";
	if(document.getElementById("cbkOther").checked == true){
		var otherPhyName=document.getElementById("txtOther").value;
		
		if(strVal!=""){
			if((getArrPhyVal!='')&&(getArrPhyVal)){
				//getRefPhyDetail=getArrPhyVal.split("@@");
				//getRefPhyID=getRefPhyDetail[0];
				//getRefPhyFAX=getRefPhyDetail[1];
			
				document.getElementById("send_fax_number").value=getRefPhyFAX;	
				document.getElementById("selectReferringPhy").value=getArrPhyVal;
			}else{
				document.getElementById("send_fax_number").value="";
				document.getElementById("selectReferringPhy").value="-";	
			}	
		}	
	}
}
*/
//== End Function ===//

//--

	function hold_dr_sig(){
		//scrol = $(window).scrollTop();
		//$('#hold_to_phy_div').css('top',scrol+310);
		//$('#hold_to_phy_div').show();
		//$(window).scroll(function(){$('#hold_to_phy_div').css('top',$(window).scrollTop()+310);});
		$("#hold_to_phy_divModal").modal("show");
		
		$("#hold_phy_save_btn").unbind().click(function() {
    		//using to omit multiple click binding
		});
		
		$('#hold_phy_save_btn').click(function(){
			if($('#hold_to_physician').val()==''){
				top.fAlert('Please select a physician');
			}else{
				$('#hidd_hold_to_physician').val($('#hold_to_physician').val());
				document.getElementById('saveBtn').click();
				//$('#hold_to_phy_div').hide();
				$("#hold_to_phy_divModal").modal("hide");
			}
		});
		
	}
	/****
	function create_pdf_sendfax(pdf_create){
		alert(pdf_create);
		$('#div_load_image').show();
				
		var tempFaxNo = $('#send_fax_number').val();
		tempFaxNo = tempFaxNo.replace(/[^0-9+]/g,"");
		
		var tempFaxNoCc1 = $('#send_fax_numberCc1').val();
		tempFaxNoCc1 = tempFaxNoCc1.replace(/[^0-9+]/g,"");
		
		var tempFaxNoCc2 = $('#send_fax_numberCc2').val();
		tempFaxNoCc2 = tempFaxNoCc2.replace(/[^0-9+]/g,"");
		
		var tempFaxNoCc3 = $('#send_fax_numberCc3').val();
		tempFaxNoCc3 = tempFaxNoCc3.replace(/[^0-9+]/g,"");
		
		url_hold_sig = zPath+"/chart_notes/requestHandler.php?elem_formAction=sendfax&txtFaxname="+$('#selectReferringPhy').val()+"&txtFaxNo="+tempFaxNo+"&txtFaxNameCc1="+$('#selectReferringPhyCc1').val()+"&txtFaxNoCc1="+tempFaxNoCc1+"&txtFaxNameCc2="+$('#selectReferringPhyCc2').val()+"&txtFaxNoCc2="+tempFaxNoCc2+"&txtFaxNameCc3="+$('#selectReferringPhyCc3').val()+"&txtFaxNoCc3="+tempFaxNoCc3+"&pdf_name="+pdf_create+"&send_fax_subject="+$('#send_fax_subject').val();
		$.ajax({  
		type: "POST",
		url: url_hold_sig,
		success: function(resp){
			$('#div_load_image').hide();
			resp = $.trim(resp);			
				
				resp = $.parseJSON(resp);
				var alertSuccess = '';
				var alertError = '';
				//=================UPDOX FAX WORKS GET DATA FROM SENDFAX.PHP FILE=================
				//=====PRIMARY RECIPENT DATA=====
				if( typeof(resp.primary.fax_id) !== 'undefined' ){ //resp.primary.
					alertSuccess += 'Primary: '+resp.primary.fax_id+', ';
					$('#fax_type_primary').val(resp.primary.log_id);
				}  
				else if( typeof(resp.primary.error) !== 'undefined' ) alertError += 'Primary: '+resp.primary.error+', ';
				
				//==CC1 RECIPENT DATA==
				if( typeof(resp.cc1.fax_id) !== 'undefined' ){
					alertSuccess += 'CC1: '+resp.cc1.fax_id+', ';
					$('#fax_type_cc1').val(resp.cc1.log_id);
				}
				else if( typeof(resp.cc1.error) !== 'undefined' ) alertError += 'CC1: '+resp.cc1.error+', ';
				//==CC2 RECIPENT DATA==	
				if( typeof(resp.cc2.fax_id) !== 'undefined' ){ 
					alertSuccess += 'CC2: '+resp.cc2.fax_id+', ';
					$('#fax_type_cc2').val(resp.cc2.log_id);
				}
				else if( typeof(resp.cc2.error) !== 'undefined' ) alertError += 'CC2: '+resp.cc2.error+', ';
				//==CC3 RECIPENT DATA==	
				if( typeof(resp.cc3.fax_id) !== 'undefined' ){
					alertSuccess += 'CC3: '+resp.cc3.fax_id+', ';
					$('#fax_type_cc3').val(resp.cc3.log_id);
				}
				else if( typeof(resp.cc3.error) !== 'undefined' ) alertError += 'CC3: '+resp.cc3.error+', ';
				
				var alertMsg = '';
				if(alertSuccess!=='')
					alertMsg += 'Fax sent successfully:<br />'+alertSuccess+'<br /><br />'; //<br/>'+alertSuccess+"<br />
				if(alertError!=='')
					alertMsg += 'Fax sending failed:<br />'+alertError;  //<br />'+alertError+"<br />"
				
				top.fAlert(alertMsg);
				if(alertSuccess!=''){
					$('#faxStatus').val("1");
					document.getElementById('saveBtn').click();
				}
			}
		});
	}
	****/
	
	//
	function fax_pdf_creater(typ,items){
		top.show_loading_image('show', '', '');
		var url=zPath+"/chart_notes/requestHandler.php";
		
		var params = {};
		
		params['elem_formAction'] = "fax_pdf_creater";
		//var params= "elem_formAction=fax_pdf_creater";		
		if(typeof(typ)=="undefined" || typ==""){ typ="";  } //p=1&typ=e
		
		var t = $('#FCKeditor1').redactor('code.get');//CKEDITOR.instances.FCKeditor1.getData();
		params['html_d'] = t;
		params['typ'] = typ;
		params['selectedRefPhyID'] = items;
		//params+="&html_d="+t;
		//params+="&typ="+typ;
		var temp_id="";
		if(typ=="e"){ 
			temp_id=""+$("#template_id_email").val()+"";
		}else{
			temp_id=""+$("#template_id_fax").val()+"";
		}
		if(typeof(temp_id)=="undefined" || temp_id==""){ temp_id="";  }
		//params+="&template_id="+temp_id;
		params['template_id'] = temp_id;
		$.post(url,params, function(d){
			top.show_loading_image('hide');	
			if(d.error)
				{
					top.fAlert(d.error);
					return false;
				}
			else
				{
					if(d.temp_id){
						//alert("CHECK:"+d.setNameFaxPDF);
						if(typ=="e"){ /*sendEmailFun(d.temp_id,d.setNameFaxPDF);*/  }
						else{ sendFaxFun(d.temp_id,d.setNameFaxPDF); }
					}
				}
		},"json");
	}
	
	function getRefPhyData(chkPCPids) {
		var postdata={chkPCPids:chkPCPids};
		url = zPath+'/chart_notes/onload_wv.php?elem_action=fetchPhyFaxDetails';
		$.ajax({
			type: "POST",
			url: url,
			data:postdata,
			success: function(r){
				$('.multiPhyHtml').html(r);//console.log(r);
			}
		});
	}
	function sendFaxFun(template_id,pdf_create){
		var items = [];
		var dataKey=[];
		$('#chkPCP option:selected').each(function(){ items.push($(this).val()); });
		$('#chkRefPhy option:selected').each(function(){ items.push($(this).val()); });
		$('#chkCoManPhy option:selected').each(function(){ items.push($(this).val()); });
        if($('#chkPCP').attr('type')=='checkbox' && ($('#chkPCP').is(":checked") == "checked" || $('#chkPCP').is(":checked") == true) ){
            if($('#chkPCP_hidd').val() != '') { items.push($('#chkPCP_hidd').val());}
        }
        if($('#chkRefPhy').attr('type')=='checkbox' && ($('#chkRefPhy').is(":checked") == "checked" || $('#chkRefPhy').is(":checked") == true) ){
            if($('#chkRefPhy_hidd').val() != '') { items.push($('#chkRefPhy_hidd').val());}
        }
        if($('#chkCoManPhy').attr('type')=='checkbox' && ($('#chkCoManPhy').is(":checked") == "checked" || $('#chkCoManPhy').is(":checked") == true) ){
            if($('#chkCoManPhy_hidd').val() != '') { items.push($('#chkCoManPhy_hidd').val());}
        }
        if($('#cbkOther').is(":checked") == "checked" || $('#cbkOther').is(":checked") == true){
            if($('#hiddtxtOther').val() != '') { items.push($('#hiddtxtOther').val());}
        }
        $('#cc1 option:selected').each(function(){ 
            if($(this).val()!=''){
                if($(this).val()=='Other'){
                    if($('#hidd_cc1Other').val() != '') { items.push($('#hidd_cc1Other').val());}
                }else{
                    var cc1ref=$(this).data('cc1ref');
                    items.push(String(cc1ref));
                }
            }
        });
        $('#cc2 option:selected').each(function(){
            if($(this).val()!=''){
                if($(this).val()=='Other'){
                    if($('#hidd_cc2Other').val() != '') { items.push($('#hidd_cc2Other').val());}
                }else{
                    var cc2ref=$(this).data('cc2ref');
                    items.push(String(cc2ref));
                }
            } 
        });
        $('#cc3 option:selected').each(function(){
            if($(this).val()!=''){
                if($(this).val()=='Other'){
                    if($('#hidd_cc3Other').val() != '') { items.push($('#hidd_cc3Other').val());}
                }else{
                    var cc3ref=$(this).data('cc3ref');
                    items.push(String(cc3ref));
                }
            }
        });
        $('#Addressee option:selected').each(function(){
            if($(this).val()!=''){
                if($(this).val()=='Other'){
                    if($('#hidd_AddresseeOther').val() != '') { items.push($('#hidd_AddresseeOther').val());}
                }else{
                    var adrseref=$(this).data('adrese');
                    items.push(String(adrseref));
                }
            }
        });
        

        if(items.length>0){
            getRefPhyData(items);
        }
		if(!template_id) { top.fAlert("Please select template first");return }		
		/*
		scrol = $(window).scrollTop();
		$('#send_fax_div').css('top',scrol+310);
		$('#send_fax_div').show();
		*/
		$("#send_fax_divModal").modal("show");
		
		//$('#selectReferringPhy').val("-");
		//$('#send_fax_number').val("");
		var getFaxNo=$('#send_fax_number').val();
		//$(window).scroll(function(){$('#send_fax_div').css('top',$(window).scrollTop()+310);});
		$("#send_close_btn").unbind().click(function() {
    		//using to omit multiple click binding
		});
		$('#send_close_btn').click(function(){
			if($('#send_fax_number').val()==''){
				top.fAlert('Please enter fax number for Referring Physician');
				$('#send_fax_number').focus();
				return false;

			}else{
				var coverLetter=''+fax_coverLetter;
				var fram = 'FCKeditor1___Frame';
				
				var FCKtext = $('#FCKeditor1').redactor('code.get');//CKEDITOR.instances.FCKeditor1.getData();
				FCKtext=coverLetter+escape(FCKtext);
				
				if(!pdf_create){
					//$('#div_load_image').show();
					//window.open("fax_pdf_creater.php?p=1",'','width=5,height=5');
					fax_pdf_creater('',items);  //IN 'items' SELECTED REFERRING PHYSICIAN ID IS COMING..
				}else{
					create_pdf_sendfax(pdf_create);
					return;
				}
			}
		});   //+"&patient_consult_id="+patientConsultId+"&templateId="+templateId+"&templateName="+templateName;
		if(pdf_create){			
			create_pdf_sendfax(pdf_create);
		}
	}
	
	/**
	function sendEmailFun(template_id,pdf_create){
		if(!template_id) {top.fAlert("Please select template first");return }
		scrol = $(window).scrollTop();
		$('#send_email_div').css('top',scrol+310);
		$('#send_email_div').show();
		//$('#selectReferringPhy').val("-");
		//$('#send_fax_number').val("");
		var getEmailId=$('#send_email_id').val();
		$(window).scroll(function(){$('#send_email_div').css('top',$(window).scrollTop()+310);});
		$('#send_email_div .hold').click(function(){
			if($('#send_email_id').val()==''){
				top.fAlert('Please enter email for Referring Physician');
				$('#send_email_id').focus();
				return false;

			}else{
				var coverLetter='';
				var fram = 'FCKeditor1___Frame';
				var FCKtext = $('#FCKeditor1').redactor('code.get');
				FCKtext=coverLetter+escape(FCKtext);
				
				if(!pdf_create){
					$('#div_load_image_e').show();
					window.open("fax_pdf_creater.php?p=1&typ=e",'','width=1,height=1');
				}else{
				$('#div_load_image_e').show();
				url_hold_sig = "send_email.php?txtEmailId="+$('#send_email_id').val()+"&txtEmailIdCc1="+$('#send_email_idCc1').val()+"&txtEmailIdCc2="+$('#send_email_idCc2').val()+"&txtEmailIdCc3="+$('#send_email_idCc3').val()+"&pdf_name="+pdf_create+"&txtEmailIdName="+$('#selectReferringPhyEmail').val()+"&txtEmailIdNameCc1="+$('#selectReferringPhyEmailCc1').val()+"&txtEmailIdNameCc2="+$('#selectReferringPhyEmailCc2').val()+"&txtEmailIdNameCc3="+$('#selectReferringPhyEmailCc3').val();
				$.ajax({
					type: "POST",
					url: url_hold_sig,
					success: function(r){
						$('#div_load_image_e').hide();
						window.opener.top.fAlert(r);
						if(r=="Mail sent successfully"){
							$('#emailStatus').val("1");
							document.getElementById('saveBtn').click();
						}
					}
				});
				}
			}
		});
		if(pdf_create){
			$('#div_load_image_e').show();
			url_hold_sig = "send_email.php?txtEmailId="+$('#send_email_id').val()+"&txtEmailIdCc1="+$('#send_email_idCc1').val()+"&txtEmailIdCc2="+$('#send_email_idCc2').val()+"&txtEmailIdCc3="+$('#send_email_idCc3').val()+"&pdf_name="+pdf_create+"&txtEmailIdName="+$('#selectReferringPhyEmail').val()+"&txtEmailIdNameCc1="+$('#selectReferringPhyEmailCc1').val()+"&txtEmailIdNameCc2="+$('#selectReferringPhyEmailCc2').val()+"&txtEmailIdNameCc3="+$('#selectReferringPhyEmailCc3').val();
				$.ajax({
				type: "POST",
				url: url_hold_sig,
				success: function(r){
					$('#div_load_image_e').hide();
					window.opener.top.fAlert(r);
					if(r=="Mail sent successfully"){
						$('#emailStatus').val("1");
						document.getElementById('saveBtn').click();
					}
				}
			});
		}
	}
	**/
//--

//--
function selAddrCCFun(obj,idTD,idInput,idHiddInput) {
	if(obj) {
		var objValArr = new Array();
		var objvalTemp = obj.value;
		var objvalArr = objvalTemp.split("@@");
		var objval=objvalArr[0];
		var faxNo=objvalArr[1];
		var refPhyNme=objvalArr[2];
		if(obj.value=="Other") {
			if(idTD) {
				if(document.getElementById(idTD)) {
					//document.getElementById(idTD).style.display="block";
					$("#"+idTD).removeClass("hidden");					
				}
			}
		}else {
			if(document.getElementById(idTD)) {
				if(document.getElementById(idInput)) {
					document.getElementById(idInput).value="";
				}
				if(document.getElementById(idHiddInput)) {
					document.getElementById(idHiddInput).value="";
				}
				//document.getElementById(idTD).style.display="none";
				$("#"+idTD).addClass("hidden");
			}
			
			if(document.getElementById("send_fax_numberCc1") && obj.id=="cc1") {
				if(refPhyNme || faxNo) {
					document.getElementById("send_fax_numberCc1").value=faxNo;
					document.getElementById("selectReferringPhyCc1").value=refPhyNme;
				}else {
					document.getElementById("send_fax_numberCc1").value='';
					document.getElementById("selectReferringPhyCc1").value='';
				}
			}
			if(document.getElementById("send_fax_numberCc2") && obj.id=="cc2") {
				if(refPhyNme || faxNo) {
					document.getElementById("send_fax_numberCc2").value=faxNo;
					document.getElementById("selectReferringPhyCc2").value=refPhyNme;
				}else {
					document.getElementById("send_fax_numberCc2").value='';
					document.getElementById("selectReferringPhyCc2").value='';	
				}
			}
			if(document.getElementById("send_fax_numberCc3") && obj.id=="cc3") {
				if(refPhyNme || faxNo) {
					document.getElementById("send_fax_numberCc3").value=faxNo;
					document.getElementById("selectReferringPhyCc3").value=refPhyNme;
				}else {
					document.getElementById("send_fax_numberCc3").value='';
					document.getElementById("selectReferringPhyCc3").value='';
				}
			}
		}
	}
}
function validate_direct(){
	dgi('hid_send_direct_bool').value = 1; 
	arrRefHid = new Array("#hiddselectReferringPhy", 
							"#hidd_AddresseeOther", 
							"#hidd_cc1Other", 
							"#hidd_cc2Other", 
							"#hidd_cc3Other", 
							"#Addressee", 
							"#cc1", 
							"#cc2", 
							"#cc3"
							);
	arrRefChk = new Array("#chkPCP", "#chkRefPhy", "#chkCoManPhy", "#cbkOther");
	flag_ref_exist = 0;
	
	$(arrRefChk).each(function(i) {
		$(arrRefChk[i]).each(function(){
			id  = $(this).attr('id');
			if($(this).is(":checked") == "checked" || $(this).is(":checked") == true){
				if(id == "cbkOther"){
					if($("#hiddtxtOther").val() != 0)
					flag_ref_exist = 1;	
				}
				flag_ref_exist = 1;
			}
		});
		//return; 
	});
	
	$(arrRefHid).each(function(i) {
		$(arrRefHid[i]).each(function(){
			val = $(this).val();
			id  = $(this).attr('id');
			if(val != ""){
				if(id == "cc1" && val == "Other"){
					if($("#hidd_cc1Other").val() != 0)
					flag_ref_exist = 1;	
				}
				else if(id == "cc2" && val == "Other"){
					if($("#hidd_cc2Other").val() != 0)
					flag_ref_exist = 1;	
				}
				else if(id == "cc3" && val == "Other"){
					if($("#hidd_cc3Other").val() != 0)
					flag_ref_exist = 1;	
				}
				else if(id == "Addressee" && val == "Other"){
					if($("#hidd_AddresseeOther").val() != 0)
					flag_ref_exist = 1;	
				}
				else flag_ref_exist = 1;
			}
		});
		//return; 
	});
	if(!flag_ref_exist){
		top.fAlert("Please select referring physician to process direct");
		return false;
	}
	if($("#ccda").is(":checked") == "checked" || $("#ccda").is(":checked") == true || $("#templateList").val() != ""){			
		//$('#div_popup').show();
		$("#div_send_directModal").modal("show");
		return false;
	}else{
		top.fAlert("Please select CCDA or template to send to direct");
		return false;
	}
}
function get_options_string(){
	var obj = document.getElementsByName("ccdDocumentOptions[]");
	arrOption = new Array();
	for(f=0;f<obj.length;f++){					
		if(obj[f].checked){						
			arrOption[arrOption.length] = obj[f].value;
		}					
	}
	return arrOption;
}
function send_direct(){
	strOptions = get_options_string();
	$("#ccdDocumentOptions").val(strOptions);
	frm_data = $("#patientConsultLetter").serialize();

	var selectedRefVal = $('#hidden_direct_email_id').data('check');
	
	if(selectedRefVal == 'false' || typeof(selectedRefVal) == 'undefined'){
		
		var selectedUser = JSON.parse(sessionStorage.getItem("clickedUsers"));
		var arrData = {};
		if(Object.keys(selectedUser).length){
			var selPhy = selectedUser.join(',');
			$.ajax({
				url:zPath+'/admin/ReferringPhysician/ajax.php',
				type: "GET",
				dataType:'JSON',
				data: {'refId' : selPhy, 'ajax_task' : 'get_multi_direct', 'returnType' : 'array'},
				success:function(response){
					if(Object.keys(response).length){
						askForMailSelection(response);
					}else{
						top.show_loading_image("show",200,"Please wait Direct message is being sent.");
						document.patientConsultLetter.submit();
					}
				}
			});	
		}
		return false;
	}else{
		top.show_loading_image("show",200,"Please wait Direct message is being sent.");
		document.patientConsultLetter.submit();
	}
}
//--

//--
//<script type="text/ecmascript">-->
      function InsertInRedactorText(t){$('#FCKeditor1').redactor('insert.text',window.top.$('.messi #txtarea_voice2text').val());clearSlate();}
      function showVoice2TextTool4RichText(richID,po){
	  html  = '<div class="m2" id="v2tlisten_status_icons"><label class="fr hide"><input type="checkbox" id="v2t_conti_chk" checked> Continuous</label><img id="v2tlisten_icon" align="absmiddle" src="'+top.WRP+'/interface/common/record_av/images/listen.png" style="cursor:pointer;" onclick="Voice2TextAction();"> <b><i>I am listeninig...</i></b></div>';
	  html += '<span id="v2t_notfinal"></span><span id="v2t_warning"></span>';
	  html += '<textarea class="border" style="width:99%; height:80px;display:block; overflow-y:auto;overflow-x:hidden;" id="txtarea_voice2text"></textarea>';
	  html += '<div class="m10" style="text-align:center">';
	  //html += '<input type="button" class="dff_button" style="margin-right:20px;" value="Start Dictation" onclick="javascript:action();return false;">';
	  html += '<input type="button" class="dff_button" style="margin-right:20px;" value="Insert Text" onclick="InsertInRedactorText()">';
      //	html += '<input type="button" class="dff_button" style="margin-right:20px;" value="Clear Text" onclick="clearSlate();">';
	  html += '<input type="button" class="dff_button" value="Close" onclick="Voice2TextAction(\'close\');window.top.removeMessi();"></div>';
	  window.top.fancyModal(html,'Voice to Text','500','',false,'',false);
	  Voice2Text(document.getElementById('txtarea_voice2text'));
	  Voice2TextAction();
      }
//< -/script>
//--

//--

      //function giveMecode(){return $('#FCKeditor1').redactor('code.get');}
      //function giveMePanelcode(p){return $('#'+p+'_panel').val();}
      function TemplatePreview(){
	 /* top.fancyModal('<iframe src="'+top.WRP+'/interface/admin/documents/template_preview.php?editor=WVConsults" style="height:550px; width:950px;" frameborder=0></iframe>'); */
      }

//--

//<script type="text/javascript">
      /*
	function getFax_Num(){
		timer_refPhyFax = setTimeout("return setFaxNumber(document.getElementById('hiddselectReferringPhy'))",1000);
	}
      */
//<-/script>
	
	//fax format
	function set_fax_format(a,b){	set_phone_format(a,b,'','fax');	}
	
	function showPrevConsultLetters(flgClose){
		top.show_loading_image('show', '', '');
		var spanID="btn_pt_consult_letters";
		top.$("#"+spanID).popover("destroy");
		if(typeof(flgClose)!="undefined" && flgClose=="1"){ return;}
		var url=zPath+"/chart_notes/requestHandler.php?elem_formAction=pt_consult_letters";
		
		$.get(url, function(data){
			top.show_loading_image('hide');
			var r="";
			if(data && data.data){ r=""+data.data; }			
			if(r==''){				
				top.$("#"+spanID).popover("destroy");
			}else{	
				var title_r="", content_r="";
				content_r=r;					
				var template = '<div class="popover" role="tooltip" style="min-width: 500px;"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"><div class="data-content"></div></div></div>';
				top.$("#"+spanID).popover({ template:""+template, title: 'Pt Consult Letters<span class="pull-right glyphicon glyphicon-remove-sign" onclick=\"showPrevConsultLetters(1)\"></span>', content: ''+content_r, html: true, placement:"bottom", container: 'body'});
				top.$("#"+spanID).popover("show");
			}
		},"json");
	}

	
//<script> Pt Consult letters
function show_cl_popup(x){
	var y="";
	if(x=="E"){
		y="send_email_div";
	}
	if(x=="F"){
		y="send_fax_div";
	}
	if(x=="L"){
		//y="send_fax_log";
		if(typeof(zPath)=="undefined" || zPath==""){ //if called from pt consult letters
			if( typeof(JS_WEB_ROOT_PATH) != "undefined" && JS_WEB_ROOT_PATH!='' ){
				zPath = JS_WEB_ROOT_PATH+"/interface";
			}
		}
		var u = zPath+"/chart_notes/requestHandler.php?elem_formAction=send_fax_log";
		window.open(""+u, 'fax_log', 'width=800,height=500,scrollbars=yes');
		return;
		
	}
	if(y!=""){		
		$("#"+y).modal({backdrop: false});		
	}
}

function create_pdf_sendfax(pdf_create){
	top.show_loading_image('show', '', '');
	
    
	var tempFaxNo = $('#send_fax_number').val();
    if(tempFaxNo)
	tempFaxNo = tempFaxNo.replace(/[^0-9+]/g,"");
	
	var lastPhyCcCount = $('#lastPhyCcCount').val();
	lastPhyCcCount = parseInt(lastPhyCcCount);

    var ccArr={};
    var ccPostStr='';
    if(lastPhyCcCount>0)
    {
        
        if( tempFaxNo != '' )
        {
        	ccArr['refPhy'] = tempFaxNo;	/*Referring Physician Fax number*/
        	tempFaxNo = '';
        }

        /*Interate through other fax numbers - in send fax dialogue box*/
        for(var i=1;i<=lastPhyCcCount;i++){

        	var tempFaxNumber = $('#send_fax_numberCc'+i).val();
            ccArr["txtFaxNameCc"+i] = tempFaxNumber.replace(/[^0-9+]/g, "");
            ccPostStr+="&tempFaxNameCc"+i+"="+$('#hiddselectReferringPhyCc'+i).val();
        }
    }
    else
    {
		var tempFaxNoCc1 = $('#send_fax_numberCc1').val();
        if(tempFaxNoCc1)
		tempFaxNoCc1 = tempFaxNoCc1.replace(/[^0-9+]/g,"");
		
		var tempFaxNoCc2 = $('#send_fax_numberCc2').val();
        if(tempFaxNoCc2)
		tempFaxNoCc2 = tempFaxNoCc2.replace(/[^0-9+]/g,"");
		
		var tempFaxNoCc3 = $('#send_fax_numberCc3').val();
        if(tempFaxNoCc3)
		tempFaxNoCc3 = tempFaxNoCc3.replace(/[^0-9+]/g,"");
    }

	var pat_template_id=$("#pat_temp_id").val()
	var ref_phy_id=$("#hiddselectReferringPhy").val();
	var getFaxName=$('#sendSaveFaxName').val();
	var savedfrom = (typeof(pat_template_id) != "undefined") ? "pt_consult_letters" : "consult_letters";
	
	if(typeof(zPath)=="undefined" || zPath==""){ //if called from pt consult letters
		if( typeof(JS_WEB_ROOT_PATH) != "undefined" && JS_WEB_ROOT_PATH!='' ){
			zPath = JS_WEB_ROOT_PATH+"/interface";
		}
	}
    //ccArr=ccArr.join(',');
	//
	if(typeof(pdf_create)=="undefined" || pdf_create==""){ pdf_create="";  }
	if(typeof(getFaxName)=="undefined" || getFaxName==""){ getFaxName="";  }


    var postData = {txtFaxname:$('#selectReferringPhy').val(),txtFaxNo:tempFaxNo,pdf_name:pdf_create,send_fax_subject:$('#send_fax_subject').val(),
                    txtFaxPdfName:getFaxName,pat_temp_id:pat_template_id,ref_phy_id:ref_phy_id,savedfrom:savedfrom,ccArr:ccArr};

	url_hold_sig = zPath+"/chart_notes/requestHandler.php?elem_formAction=sendfax&txtFaxname="+$('#selectReferringPhy').val()+"&txtFaxNo="+tempFaxNo+
				"&txtFaxNameCc1="+$('#selectReferringPhyCc1').val()+"&txtFaxNoCc1="+tempFaxNoCc1+"&txtFaxNameCc2="+$('#selectReferringPhyCc2').val()+
				"&txtFaxNoCc2="+tempFaxNoCc2+"&txtFaxNameCc3="+$('#selectReferringPhyCc3').val()+"&txtFaxNoCc3="+tempFaxNoCc3+"&pdf_name="+pdf_create+
				"&send_fax_subject="+$('#send_fax_subject').val()+"&txtFaxPdfName="+getFaxName+ccPostStr+
				"&pat_temp_id="+pat_template_id+"&ref_phy_id="+ref_phy_id+"&savedfrom="+savedfrom;
			
	/*
	url_hold_sig = "sendsavedfax.php?txtFaxName="+getFaxRecipentName+"&txtFaxNo="+getFaxNo+"&txtFaxNameCc1="+$('#selectReferringPhyCc1').val()+
				"&txtFaxNoCc1="+faxNoCc1+"&txtFaxNameCc2="+$('#selectReferringPhyCc2').val()+"&txtFaxNoCc2="+faxNoCc2+"&txtFaxNameCc3="+$('#selectReferringPhyCc3').val()+
				"&txtFaxNoCc3="+faxNoCc3+"&pat_temp_id="+pat_template_id+"&ref_phy_id="+ref_phy_id;;
	*/
   
	$.ajax({  
	type: "POST",
	url: url_hold_sig,
    data:postData,
	success: function(resp){			
			
			top.show_loading_image('hide');
			resp = $.trim(resp);
			resp = $.parseJSON(resp);
			var alertSuccess = '';
			var alertError = '';
			
			if( typeof(resp.fax_log_id) !== 'undefined' ){ 
				$('#fax_log_id').val(resp.fax_log_id);
			}  
			//=================UPDOX FAX WORKS GET DATA FROM SENDFAX.PHP FILE=================
			//=====PRIMARY RECIPENT DATA=====
			if( typeof(resp.primary.fax_id) !== 'undefined' ){ //resp.primary.
				alertSuccess += 'Primary: '+resp.primary.fax_id+', ';
				$('#fax_type_primary').val(resp.primary.log_id);
			}  
			else if( typeof(resp.primary.error) !== 'undefined' ) alertError += 'Primary: '+resp.primary.error;
			
            if(lastPhyCcCount>0){
                for(var i=1;i<=lastPhyCcCount;i++){
                    var resp_var=resp['cc'+i];
                    var errorcc='';
                    var fax_idcc='';
                    var log_idcc='';
                    if (resp_var && resp_var.hasOwnProperty('error')) {
                        errorcc=resp['cc'+i].error;
                    }
                    if (resp_var && resp_var.hasOwnProperty('fax_id')) {
                        fax_idcc=resp['cc'+i].fax_id;
                    }
                    if (resp_var && resp_var.hasOwnProperty('log_id')) {
                        log_idcc=resp['cc'+i].log_id;
                    }
                    if( typeof(fax_idcc) !== 'undefined' && fax_idcc!='' ){ 
                        alertSuccess += '<br/>CC'+i+' :'+fax_idcc;
                        $('#fax_type_cc'+i).val(log_idcc);
                    } else if( typeof(errorcc) !== 'undefined' && errorcc!='' ){
                        alertError += '<br/>CC'+i+' :'+errorcc;
                    }
                }
            } else {
                //==CC1 RECIPENT DATA==
                if( typeof(resp.cc1.fax_id) !== 'undefined' ){
                    alertSuccess += 'CC1: '+resp.cc1.fax_id+', ';
                    $('#fax_type_cc1').val(resp.cc1.log_id);
                }
                else if( typeof(resp.cc1.error) !== 'undefined' ) alertError += '<br/>CC1: '+resp.cc1.error;
                //==CC2 RECIPENT DATA==	
                if( typeof(resp.cc2.fax_id) !== 'undefined' ){ 
                    alertSuccess += 'CC2: '+resp.cc2.fax_id+', ';
                    $('#fax_type_cc2').val(resp.cc2.log_id);
                }
                else if( typeof(resp.cc2.error) !== 'undefined' ) alertError += '<br/>CC2: '+resp.cc2.error;
                //==CC3 RECIPENT DATA==	
                if( typeof(resp.cc3.fax_id) !== 'undefined' ){
                    alertSuccess += 'CC3: '+resp.cc3.fax_id+', ';
                    $('#fax_type_cc3').val(resp.cc3.log_id);
                }
                else if( typeof(resp.cc3.error) !== 'undefined' ) alertError += '<br/>CC3: '+resp.cc3.error;
            }
        
			var alertMsg = '';
			if(alertSuccess!=='')
				alertMsg += 'Fax sent successfully:<br />'+alertSuccess+'<br /><br />'; //<br/>'+alertSuccess+"<br />
			if(alertError!=='')
				alertMsg += 'Fax sending failed:<br />'+alertError;  //<br />'+alertError+"<br />"
			
			top.fAlert(alertMsg);
			if(alertSuccess!=''){
				if( typeof(pat_template_id)=="undefined" || pat_template_id=="" ){ //when called from consult letters pop up
					$('#faxStatus').val("1");
					document.getElementById('saveBtn').click();
				}else{	//when called from Pt consult letters pop up
					
					top.$("#send_fax_div").modal("hide");
					//$("#consult_tree_id").attr("src","tree4consult_letter.php");
					opConsult("&refresh=1");
				}
			}
		}
	});
}

function sendSavedFax(){
	var getFaxNo=$('#send_fax_number').val();
	var getFaxRecipentName=$('#selectReferringPhy').val();
	var getFaxName=$('#sendSaveFaxName').val();
	var pat_template_id=$("#pat_temp_id").val()
	var ref_phy_id=$("#hiddselectReferringPhy").val();
	if($('#send_fax_number').val()==''){
		alert('Please enter fax number for Referring Physician');
		$('#send_fax_number').focus();
		return false;
	}else{
		create_pdf_sendfax();
	}
}

function sendSavedEmail(){
	var getEmailId=$('#send_email_id').val();
	var getEmailName=$('#sendSaveEmailName').val();
	var pat_template_id=$("#pat_temp_id").val();
	var ref_phy_id=$("#hiddselectReferringPhy").val();
	if($('#send_email_id').val()==''){
		alert('Please enter email id for Referring Physician');
		$('#send_email_id').focus();
		return false;
	}else{
		//$('#div_load_image_e').show();
		top.show_loading_image('show', '', '');
		//
		if(typeof(zPath)=="undefined" || zPath==""){ //if called from pt consult letters
			if( typeof(JS_WEB_ROOT_PATH) != "undefined" && JS_WEB_ROOT_PATH!='' ){
				zPath = JS_WEB_ROOT_PATH+"/interface";
			}
		}
		
		var url_hold_sig = zPath+"/chart_notes/requestHandler.php?elem_formAction=sendemail&txtEmailId="+getEmailId+"&txtEmailPdfName="+getEmailName+"&txtEmailIdCc1="+$('#send_email_idCc1').val()+"&txtEmailIdCc2="+$('#send_email_idCc2').val()+"&txtEmailIdCc3="+$('#send_email_idCc3').val()+"&pat_temp_id="+pat_template_id+"&ref_phy_id="+ref_phy_id;
		
		$.ajax({
			type: "POST",
			url: url_hold_sig,
			success: function(r){
				//$('#div_load_image_e').hide();
				top.show_loading_image('hide');
				alert(r.trim());
				if(r.trim()=="Mail sent successfully"){					
					top.$("#send_email_div").modal("hide");
					//$("#consult_tree_id").attr("src","tree4consult_letter.php");
					opConsult("&refresh=1");
				}
			}
		});
	}
}

//This function is used to show preview of template
function get_template_preview(){
	var myinstances = [];
	var content_data = '';
	var template_header = $('#templateList option:selected').text();
	var left_panel = '';
	var header_panel = '';
	var footer_panel = '';
	var preview_str = '';
	var billing_server = js_array.billing_server;
    
    content_data = $('#FCKeditor1').redactor('code.get');
	
	//Editor data
/*	for(var i in CKEDITOR.instances) {
		//myinstances[CKEDITOR.instances[i].name] =  CKEDITOR.instances[i].getData(); 
		content_data = CKEDITOR.instances[i].getData();
	}
*/	
	//If panel key exists in js php array
		// which is in Documents > Consults [ Now ]
	if(js_array.temp_panels){
		if($('#header_chk').prop('checked') == true){
			header_panel = js_array.temp_panels.header+'<br /><br />'; 
		}
		
		if($('#footer_chk').prop('checked') == true){
			footer_panel = '<br /><br />'+js_array.temp_panels.footer;
		}

		if($('#leftpanel_chk').prop('checked') == true){
			left_panel = js_array.temp_panels.leftpanel; 
			//Switching left panel acc. to global billing server
			switch(js_array.billing_server){
				case 'patel':
					content_data = '<table style="width:100%;"><tr><td valign="top" style="width:80%;">'+content_data+'</td><td valign="top" style="width:20%;">'+left_panel+'</td></tr></table>';
				break;	
				
				default:
					content_data = '<table style="width:100%;"><tr><td valign="top" style="width:20%">'+left_panel+'</td><td valign="top" style="width:80%;">'+content_data+'</td></tr></table>';
			}
		}
	}
	
	//Str to be shown
	preview_str = header_panel+content_data+footer_panel;
	
	if(content_data.length){
		show_modal('template_preview_modal',template_header,preview_str,'','550','modal-lg');
	}
} 
//</script>

function addFaxSelectedPhy(){
    var firstitem={};
    $('#chkPCP option:selected').each(function(){ firstitem['chkPCP']=$(this).val(); });
    $('#chkRefPhy option:selected').each(function(){ firstitem['chkRefPhy']=$(this).val(); });
    $('#chkCoManPhy option:selected').each(function(){ firstitem['chkCoManPhy']=$(this).val(); });
    if($('#chkPCP').attr('type')=='checkbox' && ($('#chkPCP').is(":checked") == "checked" || $('#chkPCP').is(":checked") == true) ){
        if($('#chkPCP_hidd').val() != '') { firstitem['chkPCP']=$('#chkPCP_hidd').val();}
    }
    if($('#chkRefPhy').attr('type')=='checkbox' && ($('#chkRefPhy').is(":checked") == "checked" || $('#chkRefPhy').is(":checked") == true) ){
        if($('#chkRefPhy_hidd').val() != '') { firstitem['chkRefPhy']=$('#chkRefPhy_hidd').val();}
    }
    if($('#chkCoManPhy').attr('type')=='checkbox' && ($('#chkCoManPhy').is(":checked") == "checked" || $('#chkCoManPhy').is(":checked") == true) ){
        if($('#chkCoManPhy_hidd').val() != '') { firstitem['chkCoManPhy']=$('#chkCoManPhy_hidd').val();}
    }
    
    var countr=0;
    $.each(firstitem, function(key, value) {
        countr++;
        if(countr==1){
            $('#hiddfirstSelected').val(key+'__'+value);
        }
        return false;
    });
}