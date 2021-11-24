<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
set_time_limit(0);
include("../../../config/globals.php");
require_once('../../../library/classes/common_function.php');
require_once('../../../library/classes/cls_common_function.php');
$OBJCommonFunction = new CLSCommonFunction;
$phpDateFormat=phpDateFormat();
function getFacilityName($selFacilities='', $savedSearch='0'){
		$query = "select pos_facilityies_tbl.facilityPracCode as name,
				pos_facilityies_tbl.pos_facility_id as id,
				pos_tbl.pos_prac_code
				from pos_facilityies_tbl
				left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
				order by pos_facilityies_tbl.headquarter desc,
				pos_facilityies_tbl.facilityPracCode";
		$qry = imw_query($query);
		$return = '';
		while($qryRes = imw_fetch_assoc($qry)){
			$id = $qryRes['id'];
			$name = $qryRes['name'];
			$pos_prac_code = $qryRes['pos_prac_code'];
			$sel='';
			if($savedSearch=='1'){ $sel=''; }
			if(sizeof($selFacilities)>0){
				if(in_array($id,$selFacilities)) { $sel='selected'; }
			}
			//-----------------------
			
			$return .= '<option '.$sel.' value="'.$id.'">'.$name.' - '.$pos_prac_code.'</option>';
		}						
		return $return;
	}
	$library_path = $GLOBALS['webroot'].'/library';


//--- GET INSURANCE COMPANY DETAILS ----------
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$sel_ins_comp_options = '';
for ($i = 0; $i < count($insQryRes); $i++) {
	$sel='';
	$ins_id = $insQryRes[$i]['attributes']['insCompId'];
	$ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
	$ins_status = $insQryRes[$i]['attributes']['insCompStatus'];
	if ($ins_name == '') {
		$ins_name = $insQryRes[$i]['attributes']['insCompName'];
		if (strlen($ins_name) > 20) {
			$ins_name = substr($ins_name, 0, 20) . '....';
		}
	}
	
	if($selArrInsId[$ins_id])$sel='SELECTED';

	$ins_comp_arr[$ins_id] = $ins_name;
	if ($insQryRes[$i]['attributes']['insCompStatus'] == 0) {
		$sel_ins_comp_options .= "<option value='" . $ins_id . "' $sel>" . $ins_name . "</option>";
	} else {
		$sel_ins_comp_options .= "<option value='" . $ins_id . "' style='color:red' $sel>" . $ins_name . "</option>";
	}
}

?>
<html>
    <title>imwemr</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery-ui.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-colorpicker.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/messi/messi.css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/common.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/admin.css" type="text/css">
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
	<![endif]-->
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery-ui.min.1.11.2.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.dragToSelect.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-formhelpers-colorpicker.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/messi/messi.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/simple_drawing.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/core_main.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/Driving_License_Scanning.js"></script>
		<script language="javascript">	
		$(function(){		// Init. bootstrap tooltip
		$('[data-toggle="tooltip"]').tooltip();
		$('.selectpicker').selectpicker();
		$('.date-pick').datetimepicker({
			timepicker:false,
			format:top.global_date_format,
			formatDate:'Y-m-d'
		});
	});
		$(document).ready( function() {     
			$('#div_enckey').draggable({"handle":'#divHeader'});           
		});
			var strItestFileNameENC = "";
			var strItestFileNamePLAIN = "";
			
			function GetXmlHttpObject(){            
                var objXMLHttp=null;
                if(window.XMLHttpRequest){
                    objXMLHttp=new XMLHttpRequest();
                }else if(window.ActiveXObject){
                    objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP");
                }
                return objXMLHttp;
            }
           
			function searchPatient(){
				var name = document.getElementById("patient").value;
				var findBy = document.getElementById("txt_findBy").value;
				var validate = true;
				  if(name.indexOf('-') != -1){
					name = name.replace(' ','');
					name = name.split('-');
					name = name[0]
					validate = false;
				  }
				  if(validate){
					if(isNaN(name)){
						pt_win = window.open("../../../interface/scheduler/search_patient_popup.php?btn_enter="+findBy+"&btn_sub="+name+"&call_from=physician_console","mywindow","width=800,height=500,scrollbars=yes");
					}
					else{
						getPatientName(name);
					}
				  }
				
				return false;
			}
			function pat_check(val){
				if(val == ''){
					document.getElementById("patientId").value = '';
				}
			}
			function getPatientName(id,obj){
				$.ajax({
					type: "POST",
					url: "<?php echo $GLOBALS['webroot']; ?>/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid="+id,
					dataType:'JSON',
					success: function(r){
						if(r.id){
							if(obj){
								set_xml_modal_values(r.id,r.pt_name);
							}else{
								$("#patient").val(r.pt_name);
								$("#patientId").val(r.id);
							}
						}else{
							fAlert("Patient not exists");
							$("#patient").val('');
							return false;
						}	
					}
				});
			}
			function physician_console(id, name){
				$("#patient").val(name);
				$("#patientId").val(id);
				
			}
			
			function disableChkBx(ad){
				var obj = document.getElementsByName("ccdDocumentOptions[]");
				newval = ad == true ? false : '';
				for(f=1;f<obj.length;f++){
					obj[f].checked = newval;
					obj[f].disabled = ad;
				}				
			}
			
			function exportToCCD(){
				document.getElementById("trLinkDownload").style.display = "none";	
				document.getElementById("trDownloadlabel").style.display = "none";	
							
				var id = document.getElementById('patientId').value;
				if(id){
					var obj = document.getElementsByName("ccdDocumentOptions[]");				
					var arrOption=new Array(); 
					var option = "option-";
					for(f=0;f<obj.length;f++){					
						if(obj[f].checked){						
							option += obj[f].value+'-';
							arrOption[arrOption.length] = obj[f].value;
						}					
					}
					if(option == "option-"){
						top.fAlert('Please Select Export Option(s)');	
						return false;
					}
					top.show_loading_image('show');
					//document.getElementById('pId').value = id;
					//document.getElementById('option').value = option;
					top.fAlert(arrOption);
					createPatCCD(id,option);
					//document.download_CCD.submit();	
					//document.getElementById("loading_img").style.display = "none";
				}
				else{
					top.fAlert('Please Select Patient to precede export');	
				}
			}			
			
			var xmlHttpCCD;
			function createPatCCD(patId,option){		
				xmlHttpCCD = GetXmlHttpObject()
				if(xmlHttpCCD==null){
					top.fAlert ("Browser does not support HTTP Request")
					return;
				}
				var electronicDOSCCD = "";
				if(document.getElementById('cmbxElectronicDOS')){
					electronicDOSCCD = document.getElementById('cmbxElectronicDOS').value;
				}						
				var url = 'create_ccda_r2_xml.php?pid='+patId+'&option='+option+'+"&electronicDOSCCD='+electronicDOSCCD+'';
				xmlHttpCCD.onreadystatechange = getResponseCCD;
				xmlHttpCCD.open("GET",url,true);
				xmlHttpCCD.send(null);
			}
			function getResponseCCD(){
				if(xmlHttpCCD.readyState == 4){	
					var msg = 'Please enter the following information under Medical Hx or Check the option(s) precede to export: \n';
					if(xmlHttpCCD.responseText){
						var strResponseVal = xmlHttpCCD.responseText;												
						var arrResponseValMain = strResponseVal.split("##");	
						if(parseInt(arrResponseValMain[1]) == 1){
							var responseVal = arrResponseValMain[0];						
							var arrResponseVal = responseVal.split("~~");
							var hashValueENC = "";
							hashValueENC = arrResponseVal[0];
							var fileName = "";	
							fileName = arrResponseVal[1];	
							var hashValuePLAIN = "";
							hashValuePLAIN = arrResponseVal[2];
							var ccdPatName = "";
							ccdPatName = arrResponseVal[5];
							
							if(hashValueENC != "" && fileName != ""){																									
								document.getElementById("txtAreaHashValueENC").value = hashValueENC;
								//alert(fileName);						
								var clickValENC = "<a class=\"text_10b_purpule\" onClick=\"download_ccd_export('"+fileName+"','"+ccdPatName+"','xml');\" href=\"javascript:void(0);\">Click Here to Download Encrypted CCD</a>";
								document.getElementById("linkToDownloadENC").innerHTML = clickValENC;
								strItestFileNameENC = fileName;
								document.getElementById('shaENCValue').value = arrResponseVal[3];
								var fileNamePlain = arrResponseVal[4];	
								document.getElementById("txtAreaHashValuePLAIN").value = hashValuePLAIN;
								var clickValPlain = "<a class=\"text_10b_purpule\" onClick=\"download_ccd_export('"+fileNamePlain+"','"+ccdPatName+"','xml');\" href=\"javascript:void(0);\">Click Here to Download Plain CCD</a>";
								document.getElementById("linkToDownloadPLAIN").innerHTML = clickValPlain;
								strItestFileNamePLAIN = fileNamePlain;
								
								var hashEncFileName = "";
								hashEncFileName = arrResponseVal[6];
								//alert(hashEncFileName)
								var clickHashENC = "<a class=\"text_10b_purpule\" onClick=\"download_ccd_export('"+hashEncFileName+"','"+ccdPatName+"','txt');\" href=\"javascript:void(0);\">Click Here to Download SHA2</a>";
								document.getElementById("linkToDownloadENCHASH").innerHTML = clickHashENC;
							}	
							
						}
						else if(parseInt(arrResponseValMain[1]) == 2){							
							var responseVal = arrResponseValMain[0];						
							var arrResponseVal = responseVal.split("~~");														
							var errorMsg = "Patient information regarding \n";
							if(arrResponseVal[0] != ""){
								errorMsg = errorMsg + arrResponseVal[0];
							}
							if(arrResponseVal[1] != ""){
								errorMsg = errorMsg + arrResponseVal[1];
							}							
							//alert(errorMsg.slice(0,errorMsg.length - 2));
							errorMsg = errorMsg.substring(0,errorMsg.length - 2); 
							errorMsg = errorMsg + " is not filled yet!";							
							msg = errorMsg;
						}
						else if(parseInt(arrResponseValMain[1]) == 0){							
							var responseVal = arrResponseValMain[0];						
							var arrResponseVal = responseVal.split("~~");														
							if(parseInt(arrResponseVal[0]) == 1){
								msg = msg + ' - Patient Allergies. \n'; 	
							}
							if(parseInt(arrResponseVal[1]) == 1){
								msg = msg + ' - Patient Medication. \n'; 	
							}
							if(parseInt(arrResponseVal[2]) == 1){
								msg = msg + ' - Patient Problems. \n';
							}
							if(parseInt(arrResponseVal[3]) == 1){
								msg = msg + ' - Patient Labs(Result). \n';
							}
						}
						
						if(msg == 'Please enter the following information under Medical Hx or Check the option(s) precede to export: \n'){					
							document.getElementById("trLinkDownload").style.display = "block";	
							document.getElementById("trDownloadlabel").style.display = "block";														
							top.show_loading_image('hide');
						}
						else{
							top.show_loading_image('hide');
							top.fAlert(msg);
							msg = '';
						}	
																		
					}		
				}
			}
			function download_ccd_export(fileName,ccdPatName,type){
				window.focus();				
				//alert(ccdPatName)
				window.location = "download_CCD.php?fileName="+fileName+"&ccdPatName="+ccdPatName+"&fileType="+type;
			}
			function getPatDOS(){
				document.getElementById("trLinkDownload").style.display = "none";	
				document.getElementById("trDownloadlabel").style.display = "none";							
				
				if(document.getElementById('patientId')){
					if(document.getElementById('patientId').value){
						getDOS(document.getElementById('patientId').value);
					}
					else{
						top.fAlert('Please select patient to proceed to Get DOS');
					}
				}
				else{
					top.fAlert('Please select patient to proceed to Get DOS');
				}
			}
			
			var xmlHttpGetDOS;
			function getDOS(patId){		
				xmlHttpGetDOS = GetXmlHttpObject()
				if(xmlHttpGetDOS==null){
					top.fAlert ("Browser does not support HTTP Request")
					return;
				}
				top.show_loading_image('show');				
				var url = 'get_pat_dos.php?pId='+patId;						
				xmlHttpGetDOS.onreadystatechange = setDOS;
				xmlHttpGetDOS.open("GET",url,true);
				xmlHttpGetDOS.send(null);
			}
			
			function setDOS(){
				if(xmlHttpGetDOS.readyState == 4){	
					//document.write(xmlHttpGetDOS.responseText);					
					if(xmlHttpGetDOS.responseText != ""){
						var strResponseVal = xmlHttpGetDOS.responseText;												
						if(document.getElementById('tdPatDOS')){
							document.getElementById('tdPatDOS').innerHTML = strResponseVal;
							top.show_loading_image('hide');
						}
					}		
					else{
						top.show_loading_image('hide');
						top.fAlert("Patient does not have any DOS");		
								
						
					}
				}
			}
			
			var xmlHttpSentITest;
			function sendToITest(obj){
				xmlHttpSentITest = GetXmlHttpObject()
				if(xmlHttpSentITest==null){
					top.fAlert ("Browser does not support HTTP Request")
					return;
				}
				top.show_loading_image('show');					
					
				if(obj.id == "btSendToITestENC"){					
					if(document.getElementById('patientId')){
						var strENCSHAValue = strSHAENCValue = "";
						if(document.getElementById('patientId').value != ""){
							var intITestPatId = document.getElementById('patientId').value;		
							var strPatientName = document.getElementById('patient').value;		
							
							if(document.getElementById('txtAreaHashValueENC').value != ""){
								strENCSHAValue = document.getElementById('txtAreaHashValueENC').value;	
							}									
							if(document.getElementById('shaENCValue').value != ""){								
								strSHAENCValue = document.getElementById('shaENCValue').value;
							}
							var url = 'getITestResult.php?pId='+intITestPatId+'&type=ENC'+'&fileName='+strItestFileNameENC+'&encSHA='+strENCSHAValue+'&ENCKey='+strSHAENCValue+'&patName='+strPatientName;		
							
							xmlHttpSentITest.onreadystatechange = setITestResultENC;
							xmlHttpSentITest.open("GET",url,true);
							xmlHttpSentITest.send(null);
						}
					}
				}
				else if(obj.id == "btSendToITestPLAIN"){
					if(document.getElementById('patientId')){
						var strPLAINSHAValue = "";
						if(document.getElementById('patientId').value != ""){
							var intITestPatId = document.getElementById('patientId').value;	
							var strPatientName = document.getElementById('patient').value;			
							if(document.getElementById('txtAreaHashValuePLAIN').value != ""){
								strPLAINSHAValue = document.getElementById('txtAreaHashValuePLAIN').value;	
							}																
							var url = 'getITestResult.php?pId='+intITestPatId+'&type=PLAIN'+'&fileName='+strItestFileNamePLAIN+'&plainSHA='+strPLAINSHAValue+'&patName='+strPatientName;									
							xmlHttpSentITest.onreadystatechange = setITestResultPLAIN;
							xmlHttpSentITest.open("GET",url,true);
							xmlHttpSentITest.send(null);
						}
					}
				}
			}
			
			function setITestResultENC(){
				if(xmlHttpSentITest.readyState == 4){	
					//document.write(xmlHttpSentITest.responseText);					
					//return;
					if(xmlHttpSentITest.responseText){
						var strResponseVal = xmlHttpSentITest.responseText;												
						top.show_loading_image('hide');
						//alert(strResponseVal);
						var strENCSHAValue = document.getElementById('txtAreaHashValueENC').value;
						if(strENCSHAValue == strResponseVal){
							var msg = "CCD file sent to iTest Successfully! \n";
							msg = msg + "SHA 2 Hash Value matched Successfully";							
							top.fAlert(msg);
						}
						if(strENCSHAValue != strResponseVal){
							var msg = "CCD file sent to iTest Successfully! \n";
							msg = msg + "SHA 2 Hash Value dose not matched";							
							top.fAlert(msg);
						}						
						
					}		
				}
			}
			
			function setITestResultPLAIN(){
				if(xmlHttpSentITest.readyState == 4){	
					//alert(xmlHttpSentITest.responseText)					
					if(xmlHttpSentITest.responseText){
						var strResponseVal = xmlHttpSentITest.responseText;												
						top.show_loading_image('hide');
						var strPLAINSHAValue = document.getElementById('txtAreaHashValuePLAIN').value;
						if(strPLAINSHAValue == strResponseVal){
							var msg = "CCD file sent to iTest Successfully! \n";
							msg = msg + "SHA 2 Hash Value matched Successfully";							
							top.fAlert(msg);
						}
						if(strPLAINSHAValue != strResponseVal){
							var msg = "CCD file sent to iTest Successfully! \n";
							msg = msg + "SHA 2 Hash Value dose not matched";							
							top.fAlert(msg);
						}						
						
					}		
				}
			}
			
			function get_medications(data_type,objChbx,showalert){
				//pid = document.getElementById('patientId').value;
				var showalert = showalert || '';
				var pid="";
				var form_id = "";
				var objChkid = top.fmain.ccda_export_report.document.getElementsByName("elem_chkpid[]");
				var objChkFormid = top.fmain.ccda_export_report.document.getElementsByName("elem_formid[]");				
				for(fe=0;fe<objChkid.length;fe++){					
					if(objChkid[fe].checked){						
						pid = objChkid[fe].value;
						form_id = objChkFormid[fe].value;
						break;
					}					
				}
				
				if(pid == "" ){
					if(showalert!='no') {
						top.fAlert("Please select patient to proceed");
					}
					objChbx.checked=false;
					//document.getElementById("mu_data_set_"+data_type).click();
					$("#"+data_type).html(" ");
					return;
				}
				if(objChbx.checked==false) {
					$("#"+data_type).html(" ");
					return;	
				}
				
				//$("#div"+data_type).show();
				//form_id = $("#cmbxElectronicDOS").val();
				jQuery.ajax({
						url:'get_med_data.php',
						data:'pid='+pid+'&form_id='+form_id+'&data_type='+data_type,
						type:'POST',
						complete:function(respData)
						{
							resultData = respData.responseText;
							//a = window.open();
							//a.document.write(resultData);
							$("#"+data_type).html(resultData);
						}
				});	
			}
		</script>
        <style>
		.text_12{
			font-size:11px;
		}
		</style>
    </head>
<body class="whtbox">
<form name="curForm" id="curForm" action="ccda_export_report.php">
<input type="hidden" name="shaENCValue" id="shaENCValue"/>
<div id="div_enckey" class="panel panel-default" style="position:absolute;top:100px;left:500px;width:450px;max-height:550px; margin:0px; display:none; z-index:100">
	 <div class="panel-heading" id="divHeader">
		<span><b>Encryption Key</b></span> <span class="pull-right pointer" onClick="$('#div_enckey').hide();"><b>X</b></span>
	</div>	
	<div class="panel-body">
		<label for="enc_key">Please enter encryption key to proceed</label>
		<input type="text" name="enc_key" id="enc_key" class="form-control" />
	</div>
	 <div class="panel-footer" style="padding: 10px;">
		<input type="button" name="submit" id="submit"  value="Submit" onClick="submitForm(1)" class="btn btn-success">
	 </div>
  </div>	
	
<div class="container-fluid" id="report_form">
	<div class="col-sm-7">
		<div class="row">
			<div class="col-sm-2">
				<label for="facility">Facility</label>
				<select name="facility[]" id="facility" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="Select Facility" data-size="15">
					<?php echo getFacilityName('','1');?>
				</select>
			</div>
			<div class="col-sm-2">
				<label for="provider">Provider</label>
				<select name="provider[]" id="provider" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="Select Provider" data-size="15">
					<?php echo $OBJCommonFunction->dropDown_providers('','');?>
				</select>
			</div>
            <div class="col-sm-8">
                <div class="col-sm-3">
                    <label>Ins. Type</label>
                    <select class="selectpicker" name="ins_type[]" id="ins_type" data-container="#common_drop" <?php echo ($temp_id && !isset($filter_arr['ins_types']))?'disabled':''; ?> data-width="100%"  multiple data-actions-box="true" data-title="Select All">
                        <option value="primary" <?php if (in_array('primary', $_POST['ins_type'])) echo 'selected="selected"'; ?>>Primary</option>
                        <option value="secondary" <?php if (in_array('secondary', $_POST['ins_type'])) echo 'selected="selected"'; ?>>Secondary</option>
                        <option value="tertiary" <?php if (in_array('tertiary', $_POST['ins_type'])) echo 'selected="selected"'; ?>>Tertiary</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label for="insId">Ins. Carrier</label>
                    <select name="insId[]" id="insId" class="selectpicker"  <?php echo ($temp_id && !isset($filter_arr['ins_carriers']))?'disabled':''; ?> data-width="100%" data-size="15" multiple data-actions-box="true" data-title="Select All">
                        <?php echo $sel_ins_comp_options; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label for="Start_date">DOS From</label>
                    <div class="input-group">
                        <input type="text" name="Start_date" onBlur="top.checkdate(this);" id="Start_date" value="<?php echo date($phpDateFormat)?>" class="form-control date-pick">
                        <label class="input-group-addon btn" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label for="End_date">DOS To</label>
                    <div class="input-group">
                        <input type="text" name="End_date" onBlur="top.checkdate(this);" id="End_date" value="<?php echo date($phpDateFormat)?>" class="form-control date-pick">
                        <label class="input-group-addon btn" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label for="zip_encrypt">Zip Encrypt</label>
                    <div class="checkbox">
                        <input type="checkbox" name="zip_encrypt" class="mudata" title="Zip Encryption" id="zip_encrypt" value="1" ><label for="zip_encrypt"  class="a_clr1 " style="cursor:pointer"></label>
                    </div>
                </div>
            </div>
		</div>
		<div class="row">
			<script>
				function select_all_mu(obj){
					if($(obj).attr('checked') == true){
						$('.mudata').each(function(index, element) {
						$(this).attr({"checked":true});
						});
					}else{
						$('.mudata').each(function(index, element) {
						$(this).attr({"checked":false});
						});
					}
				}
			</script>
			
			
			<div class="adminbox" style="margin:10px;">
				<div class="head">Select options to exclude</div>
				<div class="pd10">
				<div><b><u>Common MU Data Set</u></b></div>
				<div class="row">
					<div class="col-sm-4">
						<div class="checkbox">
							<input type="checkbox" name="ccdDocumentOptions[]" class="mudata" title="Medications" id="mu_data_set_medications" value="mu_data_set_medications" onClick="get_medications('medications',this);"><label for="mu_data_set_medications"  class="a_clr1 text_purple" style="cursor:pointer">Medications</label>
						</div>
					
						<div class="checkbox">
							<input type="checkbox" name="ccdDocumentOptions[]" class="mudata" title="Allergies" id="mu_data_set_allergies" value="mu_data_set_allergies" onClick="get_medications('allergies',this);"><label for="mu_data_set_allergies" class="a_clr1 text_purple" style="cursor:pointer">Allergies List</label>
						</div>
						
						<div class="checkbox">
							<input type="checkbox" name="ccdDocumentOptions[]" class="mudata" title="Problem List" id="mu_data_set_problem_list" value="mu_data_set_problem_list" onClick="get_medications('problem_list',this);"><label for="mu_data_set_problem_list" class="a_clr1 text_purple" style="cursor:pointer">Problem List</label>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="checkbox">
							<input type="checkbox" name="ccdDocumentOptions[]" class="mudata" title="Smoking Status" id="mu_data_set_smoking" value="mu_data_set_smoking"><label for="mu_data_set_smoking" style="cursor:pointer">Smoking Status</label>
						</div>
						
						<div class="checkbox">
							<input type="checkbox" name="ccdDocumentOptions[]" class="mudata" title="Care Plan Field" id="mu_data_set_ap" value="mu_data_set_ap"><label for="mu_data_set_ap" style="cursor:pointer">Care Plan Field</label>
						</div>
						
						<div class="checkbox">
							<input type="checkbox" name="ccdDocumentOptions[]" class="mudata" title="Procedures" id="mu_data_set_superbill" value="mu_data_set_superbill"><label for="mu_data_set_superbill" style="cursor:pointer">Procedures</label>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="checkbox">
							<input type="checkbox" name="ccdDocumentOptions[]" class="mudata" title="Vital Sign" id="mu_data_set_vs" value="mu_data_set_vs"><label for="mu_data_set_vs" style="cursor:pointer">Vital Sign</label>
						</div>
						
						<div class="checkbox">
							<input type="checkbox" name="ccdDocumentOptions[]" class="mudata" title="Care Team Members" id="mu_data_set_care_team_members" value="mu_data_set_care_team_members"><label for="mu_data_set_care_team_members" style="cursor:pointer">Care Team Members</label>
						</div>
						
						<div class="checkbox">
							<input type="checkbox" name="ccdDocumentOptions[]" class="mudata" title="Lab" id="mu_data_set_lab" value="mu_data_set_lab"><label for="mu_data_set_lab" style="cursor:pointer">Lab</label>
						</div>
					</div>
				</div>
				<br />
				<div><b><u>Other</u></b></div>
					<div class="row">
						<div class="col-sm-4">
							<div class="checkbox">
								<input type="checkbox" name="ccdDocumentOptions[]" id="provider_info" value="provider_info"><label for="provider_info" style="cursor:pointer">Provider's Information</label>
							</div>
							
							<div class="checkbox">
								<input type="checkbox" name="ccdDocumentOptions[]" id="location_info" value="location_info"><label for="location_info" style="cursor:pointer">Date and Location of visit</label>
							</div>
								
							<div class="checkbox">
								<input type="checkbox" name="ccdDocumentOptions[]" id="reason_for_visit" value="reason_for_visit"><label for="reason_for_visit" style="cursor:pointer">Reason for visit</label>
							</div>
							
							<div class="checkbox">
								<input type="checkbox" name="ccdDocumentOptions[]" id="visit_medication_immu" value="visit_medication_immu"><label for="visit_medication_immu" style="cursor:pointer">Diagnostioc Tests Pending</label>
							</div>
						</div>
						<div class="col-sm-4">
							
							<div class="checkbox">
								<input type="checkbox" name="ccdDocumentOptions[]" id="diagnostic_tests_pending" value="diagnostic_tests_pending"><label for="diagnostic_tests_pending" style="cursor:pointer">Immunizations and Medications during visit</label>
							</div>
							
							<div class="checkbox">
								<input type="checkbox" name="ccdDocumentOptions[]" id="clinical_instruc" value="clinical_instruc"><label for="clinical_instruc" style="cursor:pointer">Clinical Instructions</label>
							</div>
							
							<div class="checkbox">
								<input type="checkbox" name="ccdDocumentOptions[]" id="future_appointment" value="future_appointment"><label for="future_appointment" style="cursor:pointer">Future Appointments</label>
							</div>

						</div>
						<div class="col-sm-4">
							
							<div class="checkbox">
								<input type="checkbox" name="ccdDocumentOptions[]" id="provider_referrals" value="provider_referrals"><label for="provider_referrals" style="cursor:pointer">Referrals to other providers</label>
							</div>
							
							<div class="checkbox">
								<input type="checkbox" name="ccdDocumentOptions[]" id="future_sch_test" value="future_sch_test"><label for="future_sch_test" style="cursor:pointer">Future Scheduled Tests</label>
							</div>
							
							<div class="checkbox">
								<input type="checkbox" name="ccdDocumentOptions[]" id="recommended_patient_decision_aids" value="recommended_patient_decision_aids"><label for="recommended_patient_decision_aids" style="cursor:pointer">Recommended patient decision aids</label>
							</div>
						</div>
					</div>
						
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-5">
		<div class="row">
			<div class="col-sm-12"><label>Search Patient</label></div>
		</div> 
		<div class="row">
			<div class="col-sm-5">
				<input type="hidden" name="patientId" id="patientId">
				<input placeholder="Patient" type="text" id="patient" name="patient" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control" onblur="searchPatient(this)">
			</div> 
			<div class="col-sm-4">
				<select name="txt_findBy" id="txt_findBy" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control minimal">
					<option value="Active">Active</option>
					<option value="Inactive">Inactive</option>
					<option value="Deceased">Deceased</option> 
					<option value="Resp.LN">Resp.LN</option> 
					<option value="Ins.Policy">Ins.Policy</option>
				</select>
			</div> 
			<div class="col-sm-3 text-left">
				<button class="btn btn-success btn-sm" type="button" onclick="searchPatient();">Search</button>
			</div>
		</div> 
		<!--<div class="row"><br />
			<div class="col-sm-3 text-left">
				<input type="button" id="btGetDOS" name="btGetDOS" value="Get DOS Visit(s)" class="btn btn-success" onClick="getPatDOS()" />
			</div>
			<div class="col-sm-3 text-left form-inline" id="tdPatDOS">
				<label for="cmbxElectronicDOS">Select Visit</label>
				<select name="cmbxElectronicDOS" id="cmbxElectronicDOS" class="form-control minimal">
					<option value="">-- All --</option>
				</select>    
			</div>
		</div>-->
        <?php $col_height = (int) ($_SESSION['wn_height'] - 660);
		?>
        <div style=" height:<?php echo $col_height;?>px; overflow:auto; overflow-x:hidden;">
        <div id="medications" style="padding:5px;"></div>
        <div style="padding:5px;"><div id="allergies" style="padding:5px;"></div></div>
        <div style="padding:5px"><div id="problem_list" style="padding:5px;"></div></div>
		<div id="divmedications" style="position:absolute; display:none; " class="border bg1">
		<div class="page_block_heading_patch pt4 pl10 boxhead" style="cursor:move; width:287px;">
			<span class="closeBtn" onClick="$('#divmedications').hide();"></span>
			<b style="font-size:14px; ">Medication List</b>
			</div>  
			<div id="div2medications"></div>
		</div>

		<div id="divproblem_list" style="position:absolute; display:none; " class="border bg1">
			<div class="page_block_heading_patch pt4 pl10 boxhead" style="cursor:move; width:287px;">
				<span class="closeBtn" onClick="$('#divproblem_list').hide();"></span>
				<b style="font-size:14px; ">Problem List</b>
			</div>
			<div id="div2problem_list"></div>
		</div>

		<div id="divallergies" style="position:absolute; display:none; " class="border bg1">
			<div class="page_block_heading_patch pt4 pl10 boxhead" style="cursor:move; width:287px;">
				<span class="closeBtn" onClick="$('#divallergies').hide();"></span>
				<b style="font-size:14px; ">Allergies</b>
			</div>
			<div id="div2allergies"></div>
		</div>
		</div>
    </div>
</div>
<?php $col_height_frame = (int) ($_SESSION['wn_height'] - 500);?>
<div class="col-sm-12">
		<iframe class="alignTop W100per" name="ccda_export_report" id="ccda_export_report" style="display:none; width:100%; overflow-x:hidden;" frameborder="0" scrolling="no" src="ccda_export_report.php" ></iframe>
</div>

<div class="row" id="trLinkDownload" style="display:none;">
	<span id="hashValue" >
		<textarea id="txtAreaHashValueENC" name="txtAreaHashValueENC" style="width:550px; height:60px;" readonly onClick="this.focus(); this.select();"></textarea>
	</span>
	<span style="padding-left:5px; height:70px; vertical-align:top;">
		<div id="linkToDownloadENC">                                                            	
		</div>
		<br/>
		<div>
			<input type="button" id="btSendToITestENC" name="btSendToITestENC" value="Send To iTest ENC" class="dff_button" onClick="sendToITest(this)"/>
		</div>
	</span>
	 <span style="padding-left:5px; height:70px; vertical-align:top;">
		<div id="linkToDownloadENCHASH">                                                            	
		</div>
	</span>
	<span id="hashValue" style="padding-left:10px; display:none;">
		<textarea id="txtAreaHashValuePLAIN" name="txtAreaHashValuePLAIN" style="width:310px; height:70px;" readonly onClick="this.focus(); this.select();"></textarea>
	</span>
	<span  style="padding-left:5px; height:70px; vertical-align:top; display:none;">
		<div id="linkToDownloadPLAIN">                                                            	
		</div>
		<br/>
		<div>
			<input type="button" id="btSendToITestPLAIN" name="btSendToITestPLAIN" value="Send To iTest PLAIN" class="dff_button" onClick="sendToITest(this)"/>
		</div>
	</span>
</div>
<div class="row" id="trDownloadlabel" style="display:none;">
	<div class="label col-sm-12" id="tdSHA">
		<span>
			Secure Hash Algorithm 2 for Encrypted Format
		</span>
		<span style="padding-left:325px; ">
			Click to download Encrypted Format
		</span>
		 <span style="padding-left:100px; ">
			Click to download SHA2
		</span>
		<span style="padding-left:90px; display:none;">
			Secure Hash Algorithm 2 for Plain-Text Format
		</span>
		 <span style="padding-left:90px; display:none;">
			Click to download Plain-Text Format
		</span> 
	</div>
</div>
</form>		
		<script type="text/javascript">
			//parent.hide_btns();
			//parent.document.getElementById('export').style.display = 'inline';
			//Btn--
			var ar = [["generate","Generate CCD List","top.fmain.submitForm(0);"], ["export","Export To CCD","top.fmain.submitForm(1);"], ["manage_ccd_schedule","Manage CCD Schedule","top.fmain.manage_ccd_schedule('ccd_schedule');"],["view_log","View Download Log","top.fmain.manage_ccd_schedule('view_log');"]];			
			top.btn_show("O4A",ar);
			//Btn--
			function submitForm(getEnc){
				var curFrmObj = document.curForm;
				var postFrmObj = document.ccda_export_report.ccda_report;
				arrMed = new Array();
				$("input[name='medications[]']:checked").each(function(index, element) {
                    arrMed[index] = $(this).val();
                });
				if(arrMed.length){
					postFrmObj.medications.value = arrMed.join('~~');
				}
				
				arrAller =  new Array();
				$("input[name='allergies[]']:checked").each(function(index, element) {
                    arrAller[index] = $(this).val();
                });
				if(arrAller.length){
					postFrmObj.allergies.value = arrAller.join('~~');
				}
				
				arrProblem = new Array();
				$("input[name='problem_list[]']:checked").each(function(index, element) {
                    arrProblem[arrProblem.length] = $(this).val();
                });
				if(arrProblem.length){
					postFrmObj.problem_list.value = arrProblem.join('~~');
				}
				
				var obj = document.getElementsByName("ccdDocumentOptions[]");	
				arrOption = new Array();
				for(f=0;f<obj.length;f++){					
					if(obj[f].checked){						
						arrOption[arrOption.length] = obj[f].value;
					}					
				}
				strOptions = arrOption;
				
				facility=$('#facility').val();
				provider=$('#provider').val();
				ins_type=$('#ins_type').val();
				insId=$('#insId').val();
				Start_date = $('#Start_date').val();
				End_date = $('#End_date').val();
				zip_encrypt = 0;
				if(document.getElementById("zip_encrypt")) {
					if(document.getElementById("zip_encrypt").checked == true) {
						zip_encrypt=$('#zip_encrypt').val();
					}
				}
				
				patientId = $('#patientId').val();
				cmbxElectronicDOS = $('#cmbxElectronicDOS').val();
				patient = $('#patient').val();
				if(patient == ""){
					document.getElementById("patientId").value="";
					patientId == ""
				}
				if(patientId == ""){
					
					if(Start_date == "" && End_date == ""){
						top.fAlert("Please enter DOS start and end date");
						return;
					}
				}else{
					postFrmObj.patientId.value = $('#patientId').val();
					postFrmObj.cmbxElectronicDOS.value = cmbxElectronicDOS;
					postFrmObj.patient.value = patient;
				}
				postFrmObj.facility.value = facility;
				postFrmObj.provider.value = provider;
				postFrmObj.ins_type.value 	= ins_type;
				postFrmObj.insId.value 		= insId;
				postFrmObj.Start_date.value = Start_date;
				postFrmObj.End_date.value = End_date;
				postFrmObj.zip_encrypt.value = zip_encrypt;
				
				var obj = document.getElementsByName("ccdDocumentOptions[]");				
				var arrOption=new Array(); 
				var option = "option-";
				for(f=0;f<obj.length;f++){					
					if(obj[f].checked){						
						option += obj[f].value+'-';
					}					
				}
				
				//*
				if(typeof(getEnc) != "undefined" && getEnc == 1){
					
					var objChkid = top.fmain.ccda_export_report.document.getElementsByName("elem_chkpid[]");
					var option2 = "option-";
					var aCnt =0;
					for(fe=0;fe<objChkid.length;fe++){					
						if(objChkid[fe].checked){						
							option2 += objChkid[fe].value+'-';
							aCnt++;
						}					
					}
					
					if(!aCnt && objChkid.length=='0') {
						top.fAlert("Please Generate CCD List");
						return false;	
					}else if(!aCnt || aCnt>50) {
						top.fAlert("Please select patient (maximum 50) to proceed");
						return false;	
					}
					enc_key = $('#enc_key').val();
					if(enc_key == ""){
						$('#div_enckey').show();
						return false;
					}else{
						if(enc_key.length < 16 || enc_key.length > 16){
						top.fAlert("Encryption key should be of 16 characters")
						return;
						}
						postFrmObj.enc_key.value = enc_key;
						$('#div_enckey').hide();
					}
				}	
					postFrmObj.ccdDocumentOptions.value = strOptions;
					top.show_loading_image("show");
					$("#ccda_export_report").show();
					postFrmObj.submit();
				
			}	
			function manage_ccd_schedule(view_page){
				switch(view_page){
					case 'ccd_schedule':
					top.fmain.location.href = "<?php echo $GLOBALS['webroot']; ?>/interface/reports/ccd/manage_ccd_schedule.php";
					break;
					case 'view_log':
					top.fmain.location.href = "<?php echo $GLOBALS['webroot']; ?>/interface/reports/ccd/view_ccd_download_log.php";
					break;
				}
			}
			set_header_title('CCD Export');
			
			$(document).ready(function() {
				var header_div 		=	$('#ccda_export_report');
				var hgnt_win 		=	$(window).height();
				var header_height	=	$('#report_form').outerHeight(true);
				var header_height2	=	15;
				
				var height_custom 	=	hgnt_win - (header_height) - (header_height2);
				header_div.css({ 'min-height' : height_custom , 'max-height': height_custom });
			});
			
		</script>
    </body>
</html>