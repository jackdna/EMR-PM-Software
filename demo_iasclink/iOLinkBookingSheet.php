<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
include("common/conDb.php");
include('connect_imwemr.php');
include("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$loginUser = $_SESSION['iolink_loginUserId'];
$year_now=isset($_REQUEST["year_now"]) ? $_REQUEST["year_now"] : '';
$selected_month_number=isset($_REQUEST["sel_month_number"]) ? $_REQUEST["sel_month_number"] : '';
$day_number=isset($_REQUEST["day_number"]) ? $_REQUEST["day_number"] : '';
$reqUserId= isset($_REQUEST['reqUserId']) ? $_REQUEST['reqUserId'] : '';
$show_canceled = isset($_REQUEST["show_canceled"]) ? $_REQUEST["show_canceled"] : '';
if(strlen($selected_month_number)==1) { $selected_month_number='0'.$selected_month_number;}
if(strlen($day_number)==1) { $day_number='0'.$day_number;}

$operatorInitialsArr = array();
$operatorInitialsArr = $objManageData->getOperatorInitialsArray();

$old_days_count = intval(90);
$expireDaysQry = "SELECT * FROM surgerycenter WHERE surgeryCenterId = '1' LIMIT 0,1";
$expireDaysRes = imw_query($expireDaysQry) or die(imw_error());
if(imw_num_rows($expireDaysRes)>0) {
	$expireDaysRow = imw_fetch_assoc($expireDaysRes);
	if(trim($expireDaysRow["documentsExpireDays"])) {
		$old_days_count = intval($expireDaysRow["documentsExpireDays"]);	
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<script>
function iOlinkClose(){
	//CHANGE TAB COLOR OF ADMIN
		if(top.document.getElementById("iOLinkTab")) {
			top.document.getElementById("iOLinkTab").className="link_a";
			top.document.getElementById("TDiOLinkTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
			top.document.getElementById("TDiOLinkMiddleTab").style.background="url(images/bg_tab.jpg)";
			top.document.getElementById("TDiOLinkBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
		}
	//END CHANGE TAB COLOR OF ADMIN, AUDIT, REPORT
		
	//top.frames[0].location = 'home_inner_front.php';
	top.window.close();
}
function opnConsentWindow(patient_id, patient_in_waiting_id,selected_template_id,consentMultipleTemplateId,opnDisplayWin) {
	//window.open('consentWinPop.php?patient_id='+patient_id+'&patient_in_waiting_id='+patient_in_waiting_id+'&selected_template_id='+selected_template_id+'&consentMultipleTemplateIdArr='+consentMultipleTemplateIdArr,'consentWin', 'top=10, width=785, height=650, scrollbars=1,location=yes,status=yes');
	document.frmOpenConsentWin.hidd_patient_id.value=patient_id;
	document.frmOpenConsentWin.hidd_patient_in_waiting_id.value=patient_in_waiting_id;
	document.frmOpenConsentWin.hidd_selected_template_id.value=selected_template_id;
	document.frmOpenConsentWin.hidd_consentMultipleTemplateId.value=consentMultipleTemplateId;
	
	if(opnDisplayWin=='opnDisplayWin') {
		document.getElementById('iolinkScanOpnWin').style.display='none';
		opnEFormWindowNew(); //OPEN WINDOW TO SHOW THE SAVED RECORD
	}else {
		document.getElementById('iolinkScanOpnWin').style.display='block';
	}	
	
	//START CODE TO CHECK IF SCAN OR CONSENT FORM ARE DONE
	if(document.getElementById('showScanLabel')) {
		document.getElementById('showScanLabel').innerText='Scan';
	}
	if(document.getElementById('showEFormLabel')) {
		document.getElementById('showEFormLabel').innerText='eForm';
	}
	if(document.frmOpenConsentWin.hidd_scanDoneStatusId) {	
		if(document.frmOpenConsentWin.hidd_scanDoneStatusId.value=='scanDone') {
			if(document.getElementById('showScanLabel')) {
				document.getElementById('showScanLabel').innerText='Scan-Done';
			}
		}
	}
	if(document.frmOpenConsentWin.hidd_eFromDoneStatusId) {
		if(document.frmOpenConsentWin.hidd_eFromDoneStatusId.value=='EFormDone') {
			if(document.getElementById('showEFormLabel')) {
				document.getElementById('showEFormLabel').innerText='eForm-Done';
			}
		}
	}
	//END CODE TO CHECK IF SCAN OR CONSENT FORM ARE DONE	
}
function opnConsentWindowNew() {

	var hidd_patient_id =  document.frmOpenConsentWin.hidd_patient_id.value
	var hidd_patient_in_waiting_id =  document.frmOpenConsentWin.hidd_patient_in_waiting_id.value
	var hidd_selected_template_id =  document.frmOpenConsentWin.hidd_selected_template_id.value
	//var hidd_consentMultipleTemplateId =  document.frmOpenConsentWin.hidd_consentMultipleTemplateId.value
	window.open('admin/scanPopUp.php?patient_id='+hidd_patient_id+'&patient_in_waiting_id='+hidd_patient_in_waiting_id+'&scaniOLinkConsentId='+hidd_selected_template_id+'&CONSENTScan=true','scanWinCONSENT', 'width=920, height=650,location=yes,status=yes');
	document.getElementById('iolinkScanOpnWin').style.display='none';
}
var scanPtClinical='';
function opnscanEformWindow(patient_id, patient_in_waiting_id,selected_template_id,consentMultipleTemplateId,opnDisplayWin,scanPtClinical) {
	document.frmOpenConsentWin.hidd_patient_id.value=patient_id;
	document.frmOpenConsentWin.hidd_patient_in_waiting_id.value=patient_in_waiting_id;
	document.frmOpenConsentWin.hidd_selected_template_id.value=selected_template_id;
	document.frmOpenConsentWin.hidd_consentMultipleTemplateId.value=consentMultipleTemplateId;
	opnEFormWindowNew(scanPtClinical); //OPEN WINDOW TO SHOW THE SAVED RECORD
}
var scanPtClinicalInfoNew='';
function opnEFormWindowNew(scanPtClinicalInfoNew) {
	var hidd_patient_id =  document.frmOpenConsentWin.hidd_patient_id.value
	var hidd_patient_in_waiting_id =  document.frmOpenConsentWin.hidd_patient_in_waiting_id.value
	var hidd_selected_template_id =  document.frmOpenConsentWin.hidd_selected_template_id.value
	var hidd_consentMultipleTemplateId =  document.frmOpenConsentWin.hidd_consentMultipleTemplateId.value
	//window.open('admin/scanPopUp.php?patient_id='+hidd_patient_id+'&patient_in_waiting_id='+hidd_patient_in_waiting_id+'&scaniOLinkConsentId='+hidd_selected_template_id+'&CONSENTScan=true','scanWinCONSENT', 'width=775, height=650,location=yes,status=yes');
	scanPtClinicalInfoValue = '';
	if(scanPtClinicalInfoNew) {
		scanPtClinicalInfoValue = '&scanSelectedFolderName='+scanPtClinicalInfoNew;
	}
	window.open('new_consent_form_page.php?patient_id='+hidd_patient_id+'&intPatientWaitingId='+hidd_patient_in_waiting_id+'&intConsentTemplateId='+hidd_selected_template_id+'&consentAllMultipleId='+hidd_consentMultipleTemplateId+scanPtClinicalInfoValue,'eFormWin', 'width=1270, height=660,location=yes,status=yes,resizable=0');
	document.getElementById('iolinkScanOpnWin').style.display='none';
}

//function iMWSyncFun(selDos) {
var xmlHttpIMW;
var imw_request = false;
function iMWSyncFun(selDos,multiPatientInWaitingId) {
	imw_request = true;
	xmlHttpIMW=GetXmlHttpObject()
	if (xmlHttpIMW==null){
		alert ("Browser does not support HTTP Request")
		return
	 }
	//var url="waiting_patient_info_popup.php"
	var url="imwSync.php"
	
	url=url+"?sync=yes"
	url=url+"&selDos="+selDos
	url=url+"&multiPatientInWaitingId="+multiPatientInWaitingId
	xmlHttpIMW.onreadystatechange=iMWPatientSyncFun
	xmlHttpIMW.open("GET",url,true)
	xmlHttpIMW.send(null)
}
function iMWPatientSyncFun(){ 
	if(xmlHttpIMW.readyState==1) {
		document.getElementById("divAjaxLoadId").style.display='block';
		document.getElementById("divAjaxLoadId").innerHTML='<img src="images/ajax-loader5.gif" width="80" height="80">';
	}
	if (xmlHttpIMW.readyState==4 || xmlHttpIMW.readyState=="complete"){ 
		document.getElementById("divAjaxLoadId").style.display='none';
		var incompRecStr = xmlHttpIMW.responseText;
		var incompRecArr 		= new Array();
		var incompCommentArr 	= new Array();
		var incompCommentMsg='';
		if(incompRecStr.search('@@')>=0) {
			incompCommentMsg='\n Comments of following patients are not updated in iASC\n';
			incompCommentArr = incompRecStr.split('@@');
			for(var i=0;i<incompCommentArr.length-1;i++) {	
				incompCommentMsg=incompCommentMsg+'.'+incompCommentArr[i];
				incompCommentMsg=incompCommentMsg+'\n';
			}
		}
		var incompRecMsg='';
		if(incompRecStr && incompRecStr.search('~~')>=0) {
			incompRecMsg='\n Following patients have incomplete records\n';
			incompRecArr = incompRecStr.split('~~');
			var html="";
			html+='<table border=1 style="width:100%;border-collapse: collapse;border:1px solid #D8D8D8;"><tr>';
			for(var i=1;i<incompRecArr.length;i++) {
				html+="<td style='padding:10px 0;'>"	
				html+=incompRecArr[i];
				html+="</td>";
			}
			html+="</tr></table>";
			document.getElementById('alert_div_content2').style.display="block";
			document.getElementById('alert_div_content2').style.width="350px";
			document.getElementById('io_Sync_Ok_confirm').style.margin="10px 0 0 135px";
			document.getElementById('alert_div_confirm').innerHTML=incompRecMsg;
			document.getElementById('alert_div_confirm1').innerHTML=html;
			document.getElementById('io_Sync_Ok_confirm').setAttribute('onClick',"page_refresh1()");
		}else {
			document.getElementById('alert_div_content2').style.display="block";
			document.getElementById('alert_div_confirm1').innerHTML="Selected booking sheet syncronized with iASC"+incompCommentMsg;
			document.getElementById('io_Sync_Ok_confirm').setAttribute('onClick',"page_refresh1()");
		}
		imw_request = false;
	}
}

function page_refresh()
{
	document.getElementById('alert_div_content2').style.display="none";
	//top.iframeHome.iOLinkBookSheetFrameId.location.reload();
}
function page_refresh1()
{
	document.getElementById('alert_div_content2').style.display="none";
	top.iframeHome.iOLinkBookSheetFrameId.location.reload();
}

function print_patient_license_sheet(selDos)
{
	var multiPatientInWaitingId='';
	
	if(document.getElementsByName("elem_patient_in_waiting_id[]")) {
		obj = document.getElementsByName("elem_patient_in_waiting_id[]");
		objLength = document.getElementsByName("elem_patient_in_waiting_id[]").length;
		var j=0;
		for(i=0; i<objLength; i++){
			if(obj[i].checked == true){
				j++;
				if(j==1) {
					multiPatientInWaitingId = obj[i].value;
				}else if(j>1) {
					multiPatientInWaitingId += ','+obj[i].value;
				}	
			}
		}
	}
	if(!multiPatientInWaitingId)
	{
		document.getElementById('alert_div_content2').style.display="block";
		document.getElementById('alert_div_confirm1').innerHTML="Please select record(s)";
		document.getElementById('io_Sync_Ok_confirm').setAttribute('onClick',"page_refresh()");
	}
	else
	{
			//var url="print_patient_license_sheet.php"
			var url="patient_license_sheet.php"
			url=url+"?multiPatientInWaitingId="+multiPatientInWaitingId
			
			window.open(url,'Sx Planning Sheet','location=0,status=1,resizable=1,left=1,top=1,scrollbars=0,width=1020,height=660');
			
	}
}

var iosync_obj = false;
function check_patient_consent_date(selDos,_this)
{
	iosync_obj = _this
	iosync_obj.setAttribute("onClick","javascript:void(0)");
	var XmlHttp;
	var multiPatientInWaitingId='';
	var msg='';
	var msg1='';
	var new_msg='';
	var old_days_count = '<?php echo $old_days_count;?>';
	if(document.getElementsByName("elem_patient_in_waiting_id[]")) {
		obj = document.getElementsByName("elem_patient_in_waiting_id[]");
		objLength = document.getElementsByName("elem_patient_in_waiting_id[]").length;
		var j=0;
		for(i=0; i<objLength; i++){
			if(obj[i].checked == true){
				j++;
				if(j==1) {
					multiPatientInWaitingId = obj[i].value;
				}else if(j>1) {
					multiPatientInWaitingId += ','+obj[i].value;
				}	
			}
		}
	}
	if(!multiPatientInWaitingId)
	{
		document.getElementById('alert_div_content2').style.display="block";
		document.getElementById('alert_div_confirm1').innerHTML="Please select record to sycronize";
		document.getElementById('io_Sync_Ok_confirm').setAttribute('onClick',"page_refresh()");
		iosync_obj.setAttribute("onClick","javascript:check_patient_consent_date('"+selDos+"',this)");
	}
	else
	{
			XmlHttp=GetXmlHttpObject()
			if(XmlHttp==null)
			{
				alert ("Browser does not support HTTP Request")
				return
			}
			var j=0;
			var url="check_patient_consent_date.php"
			url=url+"?multiPatientInWaitingId="+multiPatientInWaitingId;
			url=url+"&old_days_count="+old_days_count;
			XmlHttp.onreadystatechange = function() {
					if (XmlHttp.readyState == 4 && XmlHttp.status == 200) {
						var incompRecStr = XmlHttp.responseText;
						var pat_wat_id_arr='';
						var i=0;
						new_msg =	JSON.parse(incompRecStr);
						if(new_msg!="")
						{
						var html=	"";
						var html1= "";
						html1	+=	"<table id='html_table1'>";
						html1+="<tr>";
						html1+="<th rowspan=2 align='left'>Patient Detail</th>";
						html1+="<th rowspan=2 align='left'>Signed Forms</th>";
						html1+="<th colspan=2 align='left' style='text-indent: 120px;'>Scanned Docs</th>";
						html1+="</tr>";
						html1+="<tr><th align='left' style='text-indent: -10px;position: relative;top: -5px;'>Folder Name</th><th align='left' style='text-indent: -10px;position: relative;top: -5px;'>File Name</th></tr></table>"
						html+="<table id='html_table'>";
						for(var key in new_msg)
						{
							pat_wat_id_arr +=	","+key;
							var signLen	=	new_msg[key].signedForm.length;
							var scanLen	=	0;
							html	+=	'<tr>';
								for(var pt_det in new_msg[key].patientDetail)
								{
									html	+=	'<td rowspan='+(signLen)+' style="vertical-align:top" >'+ new_msg[key].patientDetail[pt_det] +'</td>';
								}
								html+='<td rowspan='+(signLen)+'>';
								if(signLen==0)
								{
									html+="No Documents are present";
								}
								else
								{
									for(i=0;i<signLen;i++)
									{
										html+='<p>'+new_msg[key].signedForm[i]+'</p>';
									}
								}
								html+='</td>'
								for(var scanK in new_msg[key].scanDocs)
								{
									if(new_msg[key].scanDocs[scanK]=="")
									{
										
									}
									else
									{
									html	+=	'<td>'+ scanK+'</td><td>';
									for(j=0;j<new_msg[key].scanDocs[scanK].length;j++)
									{
									html+="&nbsp;"+new_msg[key].scanDocs[scanK][j]+'<br>';
									}
									html	+='</td></tr>';
									}
								}
								i++;
						}
						html	+=	"</table>"
							if(pat_wat_id_arr!="")
							{
								document.getElementById('alert_div').style.display="block";
								document.getElementById('alert_div_content1').innerHTML=html1;
								document.getElementById('alert_div_content').innerHTML=html;
								document.getElementById('alert_div_cont').innerHTML="These documents are older than "+old_days_count+" days. Do you want to continue ?"
								document.getElementById('io_Sync_Ok').className=document.getElementById('io_Sync_Ok').className+" yes_btn"
								document.getElementById('io_cancel_btn').className=document.getElementById('io_cancel_btn').className+" no_btn"
								document.getElementById('io_Sync_Ok').style.margin="10px 0 0 475px";
								document.getElementById('io_Sync_Ok').setAttribute('onClick', "iOSyncWithScFun('"+selDos+"');");
								document.getElementById('io_cancel_btn').setAttribute('onClick', "ioSyncCancel('"+pat_wat_id_arr.substr(1)+"','"+selDos+"');");
							}
							else
							{
								iOSyncWithScFun(selDos);
							}
						}
						else
						{
							document.getElementById('alert_div_content2').style.display="block";
							document.getElementById('alert_div_confirm1').innerHTML="Are you sure to synchronize the selected booking sheet with iASC";
							document.getElementById('io_Sync_Ok_confirm').style.margin="10px 0 0 75px";
							document.getElementById('io_Sync_Ok_confirm').setAttribute('onClick', "iOSyncWithScFun('"+selDos+"');");
							var a=document.createElement('a');
							document.getElementById('alert_div_content2').appendChild(a);
							a.setAttribute('class','io_cancel_btn');
							a.setAttribute('id','io_cancel_btn1');
							a.setAttribute('onClick',"page_refresh1()");
						}
					}
				}
				XmlHttp.open("GET",url,true)
				XmlHttp.send(null)
	}
}

var ioSyncCancel = function(ptWaitStr,selDos)
{
	if(ptWaitStr=="")
	{
		iOSyncWithScFun(selDos);
	}
	else
	{
		document.getElementById('alert_div').style.display="none";
		var pat_wat_id_arr	=	ptWaitStr.split(",");
		if(document.getElementsByName("elem_patient_in_waiting_id[]"))
		{
			obj = document.getElementsByName("elem_patient_in_waiting_id[]");
			objLength = document.getElementsByName("elem_patient_in_waiting_id[]").length;
			var j=0;
			var boolval = false;
			for(i=0; i<objLength; i++)
			{
			  if(obj[i].checked == true)
			  {
				for(j=0;j<pat_wat_id_arr.length;j++)
				{
				  if(obj[i].value==pat_wat_id_arr[j])
				  {
					obj[i].checked=false ;
					boolval = true
				  }
				}
			  }
			}
			for(i=0; i<objLength; i++)
			{
			  if(obj[i].checked != true)
			  {
				document.getElementById('elemCheckAllId1').checked=false;
			  }
			}
			if(boolval == true) {
				iOSyncWithScFun(selDos);
			}
		}
	}
}

function iOSyncWithScFun(selDos) {
	document.getElementById('alert_div').style.display="none";
	document.getElementById('io_Sync_Ok_confirm').style.margin="10px 0 0 110px";
	document.getElementById('io_Sync_Ok_confirm').setAttribute('onClick', "javascript:void(0);");
	if(document.getElementById('io_cancel_btn1'))
	{
	document.getElementById('io_cancel_btn1').parentNode.removeChild(document.getElementById('io_cancel_btn1'));
	}
	var multiPatientInWaitingId='';
	if(document.getElementsByName("elem_patient_in_waiting_id[]")) {
		obj = document.getElementsByName("elem_patient_in_waiting_id[]");
		objLength = document.getElementsByName("elem_patient_in_waiting_id[]").length;
		var j=0;
		for(i=0; i<objLength; i++){
			if(obj[i].checked == true){
				j++;
				if(j==1) {
					multiPatientInWaitingId = obj[i].value;
				}else if(j>1) {
					multiPatientInWaitingId += ','+obj[i].value;
				}	
			}
		}
	}
	
	
	if(!multiPatientInWaitingId) {
		document.getElementById('alert_div_content2').style.display="block";
		document.getElementById('alert_div_confirm1').innerHTML="Please select record to sycronize"
		document.getElementById('io_Sync_Ok_confirm').setAttribute('onClick',"page_refresh()");
		iosync_obj.setAttribute("onClick","javascript:check_patient_consent_date('"+selDos+"',this)");
	}else {
		
			xmlHttp=GetXmlHttpObject()
			if (xmlHttp==null){
				alert ("Browser does not support HTTP Request")
				return
			 }
			//var url="waiting_patient_info_popup.php"
			
			//START TO ENTER VALUE IN imwemr
			iMWSyncFun(selDos,multiPatientInWaitingId);
			//END TO ENTER VALUE IN imwemr
			
			//patient_date_check(multiPatientInWaitingId);
			
			var url="iOSyncWithSc.php"
			
			url=url+"?syncWithSc=yes"
			url=url+"&selDos="+selDos
			url=url+"&multiPatientInWaitingId="+multiPatientInWaitingId
			
			xmlHttp.onreadystatechange=iOLinkPatientSyncWithScFun;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
	}
}

function iOLinkPatientSyncWithScFun(){ 
	/*
	if(xmlHttp.readyState==1) {
		document.getElementById("divAjaxLoadId").style.display='block';
		//document.getElementById("divAjaxLoadId").innerHTML='<img src="images/pdf_load_img.gif" width="120" height="18">';
		document.getElementById("divAjaxLoadId").innerHTML='<img src="images/ajax-loader5.gif" width="80" height="80">';
	}
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("divAjaxLoadId").style.display='none';
		if(top.iframeHome.document.getElementById("hiddSyncWithScImwId")) {
			top.iframeHome.document.getElementById("hiddSyncWithScImwId").value='yes';
		}
		alert('Selected booking sheet syncronized with surgerycenter');
		top.iframeHome.iOLinkBookSheetFrameId.location.reload();
		//document.getElementById("PatientInfoIdDiv").innerHTML=xmlHttp.responseText; 
	}
	*/ 
	//alert("hello");
	//top.iframeHome.iOLinkBookSheetFrameId.location.reload();
	
}
function checkAllStatusFun(objChbx,numRow,startFrom) {
	
	if(objChbx) {
		if(numRow>0) {
			startFrom = parseFloat(startFrom);
			numRow = parseFloat(numRow);
			var upTo = parseFloat(startFrom+numRow);
			for(var i=startFrom;i<upTo;i++) {
				var obj = document.getElementById("elemPatientInWaitingId"+i);
				if(obj) {
					if(objChbx.checked==true) {
						obj.checked = true;
					}else if(objChbx.checked==false) {
						//obj.checked=false;
						obj.checked=false;
					}
				}
			}
		}
	}
}
function changeBookingBgColor(SrNo) {
	var totalRecordCount= document.getElementById("hiddRecrdCount").value;
	SrNo 				= parseFloat(SrNo);
	totalRecordCount 	= parseFloat(totalRecordCount);
	if(totalRecordCount>0) {
		for(var i=1;i<=totalRecordCount;i++) {
			var bookBgTrColor="#FFFFFF";
			if(i%2==0) { bookBgTrColor = "#F3F8F2";}
			
			var obj = document.getElementById("bookingSrNo"+i);
			var selectedObj = document.getElementById("bookingSrNo"+SrNo);
			if(selectedObj) {
				if(obj) {	
					obj.style.background = bookBgTrColor;
				}
				if(i==SrNo) {
					selectedObj.style.background = "#FFFFCC";
				}
			}
		}
	}
}
function showIncompleteIns(objDivIncompleteInsId,shwHid,topPos,lftPos) {
	var hidVal 	= document.getElementById("hidd_incompleteInsuranceDivId");
	var obj 	= document.getElementById(objDivIncompleteInsId);
	var prevId 	= document.getElementById("hidd_incompleteInsuranceDivId").value
	//alert(objDivIncompleteInsId);
	if(obj) {
		if(prevId && prevId!=obj.id) {
			if(document.getElementById(prevId)) {
				document.getElementById(prevId).style.display='none';
			}
		}
		document.getElementById("hidd_incompleteInsuranceDivId").value=obj.id;
		obj.style.display=shwHid;
		obj.style.top=topPos-35;
		obj.style.left=lftPos-5;
	}else {
		if(prevId) {
			if(document.getElementById(prevId)) {
				document.getElementById(prevId).style.display='none';
			}
		}
	}
}


function closeDiv() {
	var prevId 	= document.getElementById("hidd_incompleteInsuranceDivId").value
	if(prevId) {
		if(document.getElementById(prevId)) {
			document.getElementById(prevId).style.display='none';
		}
	}
}

function iOSyncSplitPdfFun(selDos) {
	window.open('pdf_split_main.php?selDos='+selDos,'pdfSplit','location=0,status=1,resizable=1,left=1,top=1,scrollbars=0,width=1020,height=660');
}
function removeBooking(selDos) {
	var multiPatientInWaitingId='';
	if(document.getElementsByName("elem_patient_in_waiting_id[]")) {
		obj = document.getElementsByName("elem_patient_in_waiting_id[]");
		objLength = document.getElementsByName("elem_patient_in_waiting_id[]").length;
		var j=0;
		for(i=0; i<objLength; i++){
			if(obj[i].checked == true){
				j++;
				if(j==1) {
					multiPatientInWaitingId = obj[i].value;
				}else if(j>1) {
					multiPatientInWaitingId += ','+obj[i].value;
				}	
			}
		}
	}
	if(!multiPatientInWaitingId) {
		alert('Please select patient(s) to delete from booking sheet');
	}else {
		if(confirm('Are you sure to delete selected patient(s) from booking sheet')) {
			//document.getElementById('hidd_delBooking').value='yes';
			//document.frmOpenConsentWin.submit();
			xmlHttp=GetXmlHttpObject()
			if (xmlHttp==null){
				alert ("Browser does not support HTTP Request")
				return
			 }
			var url="remove_booking.php"
			url=url+"?removeBooking=yes"
			url=url+"&selDos="+selDos
			url=url+"&multiPatientInWaitingId="+multiPatientInWaitingId
			xmlHttp.onreadystatechange=removeBookingFun
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
			
		}
	}
}
function removeBookingFun() {
	if(xmlHttp.readyState==1) {
		document.getElementById("divAjaxLoadId").style.display='block';
		document.getElementById("divAjaxLoadId").innerHTML='<img src="images/ajax-loader5.gif" width="80" height="80">';
	}
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		//alert(xmlHttp.responseText);
		document.getElementById("divAjaxLoadId").style.display='none';
		alert("Selected patient(s) are deleted from booking sheet");
		top.iframeHome.iOLinkBookSheetFrameId.location.reload();
	}
}
</script>
<?php
$spec= "
</head>
<body>";
include("common/link_new_file.php");
include("common/iOLinkCommonFunction.php");
include("incomplete_insurance.php");
$practiceName = getPracticeName($loginUser,'Coordinator');
$coordinatorType = getCoordinatorType($loginUser);

function getBookingInsuranceFlag($ptId,$type,$ptName) {
	//global $link_imwemr;
	global $imw_db_name;
	$crossTickImgSrc= "images/check_mark16.png";
	$insProvider 	= '';
	$insCaseName 	= '';
	$insProviderArr	='';
	$insCaseNameArr	='';
	$dataArr 		= array();
	
	//$chkInsDataQry= "SELECT * from insurance_data WHERE  patient_id = '".$ptId."' AND  type = '".$type."' AND actInsComp='1' order by id";
	
	$chkInsDataQry  = "SELECT insCase.case_name, insData.id, insData.ins_provider FROM insurance_data insData 
						LEFT JOIN iolink_insurance_case insCase ON insCase.ins_caseid = insData.ins_caseid
						WHERE insData.patient_id='".$ptId."' AND  insData.type = '".$type."' AND insData.ins_caseid > 0 AND insData.actInsComp='1'";
	
	$chkInsDataRes = imw_query($chkInsDataQry);
	$chkInsDataNumRow = @imw_num_rows($chkInsDataRes);
	if($chkInsDataNumRow >0) {
		while($chkInsDataRow = @imw_fetch_array($chkInsDataRes)) {
			$insId 			= $chkInsDataRow['id'];	
			$insCaseName 	= $chkInsDataRow['case_name'];	
			$insProvider 	= $chkInsDataRow['ins_provider'];	
			//include('connect_imwemr.php'); //imwemr connection
			$chkCompQry 	= "SELECT id FROM ".$imw_db_name.".insurance_companies 
								WHERE  (name = '".addslashes(htmlentities(trim($insProvider)))."' || name = '".addslashes(trim($insProvider))."') ORDER BY id Desc Limit 0,1";
			$chkCompRes 	= imw_query($chkCompQry) or die(imw_error().$chkCompQry);	
			$chkCompNumRow 	= imw_num_rows($chkCompRes);
			//echo '<br>'.$ptName.$chkCompNumRow;
			if($chkCompNumRow<=0) { 
				$crossTickImgSrc = "images/chk_off1.gif";
				$insProviderArr[$insId] = $insProvider;
				$insCaseNameArr[$insId] = $insCaseName;
				
			}
			//imw_close($link_imwemr); //CLOSE imwemr connection
			//include("common/conDb.php");				
		}
		if($insProviderArr) {
			//CREATE DIV TO SHOW WARNING ALERT
			//echo 'hlo '.$insProviderArr;
			
			$dataArr[1] = $type;
			$dataArr[2] = $insProviderArr;
			$dataArr[3] = $insCaseNameArr;
			$dataArr[4] = $ptName;
			$dataArr[5] = $ptId;
			//incompleteInsProviderDiv($type,$insProviderArr,$insCaseNameArr,$ptName,$ptId);	
		}
	}else {
		$crossTickImgSrc = "images/chk_off1.gif";
		
	}
	$dataArr[0] = $crossTickImgSrc;
	return $dataArr;
	//return $crossTickImgSrc;
	
}

//START CODE TO SHOW/HIDE THE 'Sync Sc' link
	$syncScVisibility		= 'hidden';
	$displayStatusCheckBox	= 'false';
	if($coordinatorType=='Master') {
		$syncScVisibility 	= 'visible';
		$displayStatusCheckBox='true';
	}
//END CODE TO SHOW/HIDE THE 'Sync Sc' link

//START CODE TO SHOW/HIDE THE 'Sync Sc' link
	$syncIMWVisibility='hidden';
	if($imwSwitchFile=='sync_imwemr.php' && $coordinatorType=='Master') {
		$syncIMWVisibility = 'visible';
	}
//END CODE TO SHOW/HIDE THE 'Sync Sc' link

if($year_now && $selected_month_number && $day_number) {
	$selDos=$year_now.'-'.$selected_month_number.'-'.$day_number;
}else {
	$selDos=date('Y-m-d');
}
$dayName = date("l",strtotime($selDos));

$showDos = date("m/d/y",strtotime($selDos));

$dayNum  = date("d",strtotime($selDos));

$andStubInfoQry='';
$bookingSrgnName = "";
if($reqUserId) {
	// GETTING LOGIN USER NAME
	$bookingSrgnNameQry = "SELECT fname, mname, lname FROM users WHERE usersId = '".$reqUserId."'";
	$bookingSrgnNameRes = imw_query($bookingSrgnNameQry);
	$bookingSrgnNameRow = imw_fetch_array($bookingSrgnNameRes);
	$bookingSrgnFName 	= stripslashes($bookingSrgnNameRow['fname']);
	$bookingSrgnMName 	= stripslashes($bookingSrgnNameRow['mname']);
	$bookingSrgnLName 	= stripslashes($bookingSrgnNameRow['lname']);
	
	if($bookingSrgnLName && $bookingSrgnFName) {
		$bookingSrgnName = $bookingSrgnLName.', '.$bookingSrgnFName.' '.$bookingSrgnMName;
	}
	// GETTING LOGIN USER NAME
	
	$andStubInfoQry = " AND patient_in_waiting_tbl.surgeon_fname='".addslashes($bookingSrgnFName)."' 
						AND patient_in_waiting_tbl.surgeon_mname='".addslashes($bookingSrgnMName)."' 
						AND patient_in_waiting_tbl.surgeon_lname='".addslashes($bookingSrgnLName)."' 
						AND users.usersId= '".$reqUserId."'
						AND patient_in_waiting_tbl.surgeon_fname=users.fname 
						AND patient_in_waiting_tbl.surgeon_mname=users.mname 
						AND patient_in_waiting_tbl.surgeon_lname=users.lname";

	$andStubInfoSubQry = "AND patient_in_waiting_tbl.surgeon_fname=users.fname 
						AND patient_in_waiting_tbl.surgeon_mname=users.mname 
						AND patient_in_waiting_tbl.surgeon_lname=users.lname";
	

}else {
	
	$AndUserPracticeNameQry="";
	if($coordinatorType!='Master') {
		//if($practiceName){
			$AndUserPracticeNameQry=getPracticeUser($practiceName,"AND","users");  
		//}
		//$AndUserPracticeNameQry = " AND users.practiceName='".($practiceName)."'";
	}
	$andStubInfoQry = " $AndUserPracticeNameQry 
						AND patient_in_waiting_tbl.surgeon_fname=users.fname 
						AND patient_in_waiting_tbl.surgeon_mname=users.mname 
						AND patient_in_waiting_tbl.surgeon_lname=users.lname";
	
	$andStubInfoSubQry = " $AndUserPracticeNameQry 
						AND patient_in_waiting_tbl.surgeon_fname=users.fname 
						AND patient_in_waiting_tbl.surgeon_mname=users.mname 
						AND patient_in_waiting_tbl.surgeon_lname=users.lname";

}

$andSchCancelQry = " AND patient_in_waiting_tbl.dos='".$selDos."' AND patient_in_waiting_tbl.patient_status!='Canceled' ";
$andSchCanceledSubQry = " AND patient_in_waiting_tbl.dos='".$selDos."' AND patient_in_waiting_tbl.patient_status!='Canceled' ";
if($show_canceled == "yes") {
	$andSchCancelQry = " AND patient_in_waiting_tbl.patient_status='Canceled' ";
	$andSchCanceledSubQry = " AND patient_in_waiting_tbl.patient_status='Canceled' ";	
}

$facQry = "";
if(trim($_SESSION['iolink_iasc_facility_id'])) {
	$facQry = " AND patient_in_waiting_tbl.iasc_facility_id IN (".$_SESSION['iolink_iasc_facility_id'].") ";	
}
//START TO GET PATIENTS FROM patient_in_waiting_tbl
	$getStubInfoQry 	= "SELECT patient_in_waiting_tbl.*,users.usersId, users.fname, users.mname, users.lname FROM patient_in_waiting_tbl,users WHERE users.deleteStatus!='Yes' $andSchCancelQry $andStubInfoQry $facQry ORDER BY patient_in_waiting_tbl.surgeon_fname ASC "; //order by patient_in_waiting_tbl.surgery_time
	$getStubInfoRes 	= imw_query($getStubInfoQry) or die($getStubInfoQry.imw_error());
	$getStubInfoNumRow 	= imw_num_rows($getStubInfoRes);
//END TO GET PATIENTS FROM patient_in_waiting_tbl

$bookingCurrYear 		= date('Y');
$bookingCurrMonth 		= date('m');
$bookingCurrDay 		= date('d');
$bookingCurrDt			= date('Y-m-d');
$UploadPdfDt			= $selDos;
if($selDos<$bookingCurrDt) { $UploadPdfDt = $bookingCurrDt; }

//START CODE TO CHECK PENDING PDF SPLIT FILES
$pendingSplitFileExist=false;
$uploadPdfLnkBgColor='';
$filePathSplit ='pdfSplit/'.$UploadPdfDt.'/';
if ($handle = @opendir($filePathSplit)) {
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != ".." && $file != "tmp" && !stristr($file,".txt")) {
			$pendingSplitFileExist = true;
			//$uploadPdfLnkBgColor='#FBD78D';
			$uploadPdfLnkBgColor='#FFFF99';
		}
	}		
}
//END CODE TO CHECK PENDING PDF SPLIT FILES

$patOrder = "lname_asc";
$wt_order = isset($_REQUEST["wt_order"]) ? $_REQUEST["wt_order"] : '';
if($wt_order == "lname_asc") {
	$patOrder = "lname_desc";	
}
?>

<span id='divAjaxLoadId' style="position:absolute; top:220px; left:210px; display:none;"></span>
<div id="iolinkScanOpnWin" style="position:absolute; top:100px; left:150px; display:none; ">
  <table class="text_10 table_pad_bdr" style="width:99%;">
    <tr style="height:25px; background-color:#c0aa1e;" >
      <td class="text_printb alignLeft valignMiddle" style="width:500px; background-color:<?php echo $bgCol; ?>;" >&nbsp;&nbsp;iOLink</td>
      <td class="alignLeft"><img style="border:none;" alt="Close" src="images/close.jpg" onClick="document.getElementById('iolinkScanOpnWin').style.display='none';"></td>
    </tr>
    <tr style="background-color:#c0aa1e;">
      <td colspan="2" class="text_print alignLeft" style="background-color:<?php echo $rowcolor_discharge_summary_sheet; ?>;">
    </tr>
    <tr style="background-color:#FFFFFF;">
      <td colspan="2" class="alignLeft nowrap"><a href="#" class="link_home"  style="width:70px; height:25px; border:none;" onClick="opnConsentWindowNew();"><span id="showScanLabel" style="cursor:pointer; ">Scan</span></a>&nbsp;&nbsp;<a href="#" class="link_home"  onClick="javascript:opnEFormWindowNew(); "  style="width:70px; height:25px; border:none;"><span id="showEFormLabel" style="cursor:pointer; ">eForm</span></a></td>
    </tr>
  </table>
</div>
<!-- START FORM TO OPEN CONSENT FORM POP-UP -->
<form name="frmOpenConsentWin"  enctype="multipart/form-data" method="get" style="margin:0px; " action="iOLinkBookingSheet.php">
  <!-- action="consentWinPop.php" target="_blank"-->
  <input type="hidden" id="hidd_patient_id" name="hidd_patient_id" value="<?php echo $patient_id; ?>">
  <input type="hidden" id="hidd_patient_in_waiting_id" name="hidd_patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
  <input type="hidden" id="hidd_selected_template_id" name="hidd_selected_template_id" value="">
  <input type="hidden" id="hidd_consentMultipleTemplateId" name="hidd_consentMultipleTemplateId" value="">
  <input type="hidden" id="hidd_scanDoneStatusId" name="hidd_scanDoneStatusId" value="">
  <input type="hidden" id="hidd_eFromDoneStatusId" name="hidd_eFromDoneStatusId" value="">
  <input type="hidden" id="hidd_incompleteInsuranceDivId" name="hidd_incompleteInsuranceDivId" value="">
  <input type="hidden" id="year_now" name="year_now" value="<?php echo $year_now; ?>">
  <input type="hidden" id="sel_month_number" name="sel_month_number" value="<?php echo $selected_month_number; ?>">
  <input type="hidden" id="day_number" name="day_number" value="<?php echo $day_number; ?>">
  <input type="hidden" id="hidd_delBooking" name="hidd_delBooking" value="">
</form>
<!-- END FORM TO OPEN CONSENT FORM POP-UP -->

<table class="table_collapse" onClick="closeDiv();">
  <tr class="valignTop">
    <td class="text_10bAdmin " style="width:100%;"><table class="table_collapse">
        <tr class="valignMiddle" style="height:22px;">
          <td class="tst10b alignLeft nowrap" style="width:40%; background-image:url(<?php echo $bgHeadingImage;?>);padding-left:5px; ">Booking Sheet<span style="padding-left:6px;">
            <?php if($bookingSrgnName) { echo $bookingSrgnName.' - '; } if($show_canceled=="yes") {echo "Cancelled Appointment(s)";}else {echo $dayName;?>
            , <?php echo $showDos;}?></span></td>
          <td class="text_10b alignRight valignTop" style="background-image:url(<?php echo $bgHeadingImage;?>);"><a onClick="top.iframeHome.iOLinkBookSheetFrameId.location.href='iOLinkBookingSheet.php?show_canceled=yes';" class="tst11" style=" cursor:pointer;position:relative; right:32px; top:-4px;  " title="Show Canceled Appointment(s)"><b><span style="color:#FF0000; font-family:Times New Roman, Times, serif;" >&nbsp;T</span><i>o</i></b>-Do&nbsp;</a> <a onClick="removeBooking('<?php echo $selDos;?>');" class="tst11" style=" cursor:pointer;position:relative; right:35px; top:-4px;  " title="Remove patient from booking"><b><span style="color:#FF0000; font-family:Times New Roman, Times, serif;" >&nbsp;D</span><i>e</i></b>lete-Patient&nbsp;</a> <img style=" border:none; cursor:pointer;position:relative;right:35px; "  src="images/refresh_icon.png" alt="Refresh Booking Sheet"  onClick="top.iframeHome.iOLinkBookSheetFrameId.location.reload();"> <a onClick="iOSyncSplitPdfFun('<?php echo $UploadPdfDt;?>');" class="tst11" style=" cursor:pointer;position:relative; right:32px; top:-4px; background-color:<?php echo $uploadPdfLnkBgColor;?>; " title="Upload-PDFs"><b><span style="color:#FF0000; font-family:Times New Roman, Times, serif;" >&nbsp;U</span><i>P</i></b>load-PDFs&nbsp;</a> <a onClick="check_patient_consent_date('<?php echo $selDos;?>',this);" class="tst11" style=" cursor:pointer;position:relative; right:32px; top:-4px; visibility:<?php echo $syncScVisibility;?>; " title="Syncronize With Surgery Center"><b><span style="color:#FF0000; font-family:Times New Roman, Times, serif;" >i</span><i>O</i></b>Sync</a> <a title="Syncronize with surgerycenter"  onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('iolinkTodayBtn','','images/iolink_today_hover.jpg',1);" style=" cursor:pointer; position:relative; right:28px; top:3px; " > <img style=" border:none;"  src="images/iolink_today.jpg" alt="Today"  onClick="location.href='iOLinkBookingSheet.php?day_number=<?php echo $bookingCurrDay;?>&sel_month_number=<?php echo $bookingCurrMonth;?>&year_now=<?php echo $bookingCurrYear;?>&reqUserId=<?php echo $reqUserId;?>';"> </a><img src="images/printicon.gif" title="Print Selected Booking Sheet" alt="Print Selected Booking Sheet" style="cursor:pointer; border:none; position:relative; right:26px; top:2px; " onClick="window.open('BookingSheet_pdf.php?selDos=<?php echo $selDos; ?>&reqUserId=<?php echo $reqUserId;?>&wt_order=<?php echo $wt_order;?>&show_canceled=<?php echo $show_canceled;?>','selDosWin<?php echo date('mdy',strtotime($selDos));?>')"><img src="images/print_iol.jpg" title="IOL Print Out" alt="IOL Print Out" style="cursor:pointer; border:none; position:relative; right:20px; top:2px; " onClick="window.open('iol_print.php?selDos=<?php echo $selDos; ?>&reqUserId=<?php echo $reqUserId;?>&wt_order=<?php echo $wt_order;?>&show_canceled=<?php echo $show_canceled;?>','iolPrintWin<?php echo date('mdy',strtotime($selDos));?>')"><img src="images/license.png" title="Sx Planning Sheet" alt="Sx Planning Sheet" style="cursor:pointer; border:none; position:relative; right:10px; top:2px; " onClick="print_patient_license_sheet('<?php echo $selDos;?>')" /> <img src="images/close.jpg" alt="Close" style="border:none; position:relative; right:5px; top:2px; " onClick="return iOlinkClose();" /></td>
        </tr>
      </table></td>
  </tr>
  <tr class="valignTop">
    <td class="alignLeft"><table style="border:none;border:solid 1px; border-color:#9FBFCC; padding:1px; width:100%; ">
        <tr class="valignTop">
          <td><div style="width:1235px;background-color:#F8F9F7;">
              <table style="width:1215px; font-size:9px;">
                <tr class="text_homeb valignTop"  style="font-size:10px;height:20px; " ><!--F8F9F7-->
                  <td class="valignTop" style="width:37px;  border:2px solid; border-color:#FFFFFF;">Status</td>
                  <td style="width:103px; border:2px solid; border-color:#FFFFFF;"><a onClick="top.iframeHome.iOLinkBookSheetFrameId.location.href='iOLinkBookingSheet.php?day_number=<?php echo $_REQUEST["day_number"];?>&sel_month_number=<?php echo $_REQUEST["sel_month_number"];?>&year_now=<?php echo $_REQUEST["year_now"];?>&reqUserId=<?php echo $reqUserId;?>&show_canceled=<?php echo $_REQUEST['show_canceled'];?>&wt_order=<?php echo $patOrder;?>';" style=" color:#800080;cursor:pointer;" title="Sort By Patient Name">Pt.&nbsp;Name&nbsp;-&nbsp;DOB</a></td>
                  <td  class="alignCenter" style="width:117px;border:2px solid; border-color:#FFFFFF;">Procedure
                    <?php if($show_canceled=="yes")  {echo "&nbsp;-&nbsp;DOS";}?></td>
                  <td class="alignCenter nowrap" style="width:100px;border:2px solid; border-color:#FFFFFF;">Comments</td>
                  <td class="nowrap" style="width:20px;border:2px solid; border-color:#FFFFFF;">Consent</td>
                  <td class="nowrap" style="width:15px;border:2px solid; border-color:#FFFFFF;">IOL</td>
                  <td class="nowrap" style="width:35px;border:2px solid; border-color:#FFFFFF;">Oc&nbsp;Hx.</td>
                  <td class="alignCenter nowrap" style="width:50px;border:2px solid; border-color:#FFFFFF;">Health Q</td>
                  <td class="alignCenter nowrap" style="width:30px;border:2px solid; border-color:#FFFFFF;"><?php echo urldecode("H&amp;P");?></td>
                  <td class="alignCenter nowrap" style="width:20px;border:2px solid; border-color:#FFFFFF;">EKG</td>
                  <td class="valignTop" style="width:96px;border:2px solid; border-color:#FFFFFF;"><table class="text_homeb table_collapse" style="font-size:9px; vertical-align:top; ">
                      <tr class="valignTop">
                        <td colspan="3" class="alignCenter">Insurance</td>
                      </tr>
                      <tr>
                        <td class="nowrap" style="width:32px;border:2px solid; border-color:#FFFFFF;">Pri.</td>
                        <td class="nowrap" style="width:32px;padding-left:3px;border:2px solid; border-color:#FFFFFF;">Sec.</td>
                        <td class="nowrap" style="width:32px;padding-left:4px;border:2px solid; border-color:#FFFFFF;">Ter.</td>
                      </tr>
                    </table></td>
                  <td class="nowrap" style="width:30px;border:2px solid; border-color:#FFFFFF;">Meds</td>
                  <td class="nowrap" style="width:30px;border:2px solid; border-color:#FFFFFF;">Allergy</td>
                  <td class="nowrap" style="width:15px; padding-left:2px;border:2px solid; border-color:#FFFFFF;"><table class="text_homeb table_collapse" style="font-size:9px; vertical-align:top; ">
                      <tr class="valignTop">
                        <td class="alignLeft" >iOsync</td>
                      </tr>
                      <tr>
                        <td class="nowrap" >Count</td>
                      </tr>
                    </table></td>
                  <td class="nowrap" style="width:65px; padding-left:2px;border:2px solid; border-color:#FFFFFF;"><table class="text_homeb table_collapse" style="font-size:9px; vertical-align:top; ">
                      <tr class="valignTop">
                        <td class="alignLeft" >IOL&nbsp;Man</td>
                      </tr>
                      <tr>
                        <td class="nowrap" >(Brand)</td>
                      </tr>
                    </table></td>
                  <td class="nowrap" style="width:70px;border:2px solid; border-color:#FFFFFF;">IOL Model</td>
                  <td class="nowrap" style="width:45px;border:2px solid; border-color:#FFFFFF;">IOL&nbsp;Diopter</td>
                  <td class="nowrap" style="width:15px;border:2px solid; border-color:#FFFFFF;"><table class="text_homeb table_collapse" style="font-size:9px; vertical-align:top; ">
                      <tr class="valignTop">
                        <td class="alignLeft" >OPR.</td>
                      </tr>
                    </table></td>
                </tr>
              </table>
            </div>
            <div style="height:222px; width:1235px; overflow:scroll; overflow-x:hidden;   "><!-- overflow-x:hidden; -->
              
              <table style="border:none;  width:1215px;">
                <?php
							$labelConsentTemplateQry = "select * from consent_forms_template order by consent_id";
							$labelConsentTemplateRes = imw_query($labelConsentTemplateQry) or die($labelConsentTemplateQry.imw_error());
							$labelConsentTemplateNumRow = imw_num_rows($labelConsentTemplateRes);
							
							if($getStubInfoNumRow>0) {
								$stub_tbl_groupTemp = array();
								$cntr=1;
								
								$wtOrderByQry = " patient_in_waiting_tbl.surgery_time ASC ";
								if($wt_order=="lname_asc"){ 
									$wtOrderByQry = " patient_data_tbl.patient_lname ASC, patient_data_tbl.patient_fname ASC "; 
								}else if($wt_order=="lname_desc"){
									$wtOrderByQry = " patient_data_tbl.patient_lname DESC, patient_data_tbl.patient_fname DESC "; 
								}
								
								while($stub_tbl_group_row = imw_fetch_array($getStubInfoRes)) {
									$stub_surgeon_name = "";
									$stub_tbl_group_surgeon_name = "";
									$stub_tbl_group_surgeon_fname = trim(stripslashes($stub_tbl_group_row['surgeon_fname']));
									$stub_tbl_group_surgeon_mname = trim(stripslashes($stub_tbl_group_row['surgeon_mname']));
									$stub_tbl_group_surgeon_lname = trim(stripslashes($stub_tbl_group_row['surgeon_lname']));
									$stub_surgeon_name_chk = trim(stripslashes($stub_tbl_group_row['surgeon_fname'])).' '.trim(stripslashes($stub_tbl_group_row['surgeon_mname'])).' '.trim(stripslashes($stub_tbl_group_row['surgeon_lname']));
									$stub_surgeon_name = trim(stripslashes($stub_tbl_group_row['fname'])).' '.trim(stripslashes($stub_tbl_group_row['mname'])).' '.trim(stripslashes($stub_tbl_group_row['lname']));
									$user_tbl_group_surgeon_id = $stub_tbl_group_row['usersId'];
								
									if(!in_array(strtolower($stub_surgeon_name_chk),$stub_tbl_groupTemp)) { 
										$stub_tbl_query = "SELECT patient_in_waiting_tbl.*,users.usersId,DATE_FORMAT(patient_in_waiting_tbl.dos,'%m/%d/%Y') as patient_waiting_dos  
															FROM patient_in_waiting_tbl,users,patient_data_tbl  
															WHERE users.deleteStatus!='Yes' AND users.user_type='Surgeon' 
															AND patient_in_waiting_tbl.surgeon_fname='".addslashes($stub_tbl_group_surgeon_fname)."' 
															AND patient_in_waiting_tbl.surgeon_mname='".addslashes($stub_tbl_group_surgeon_mname)."' 
															AND patient_in_waiting_tbl.surgeon_lname='".addslashes($stub_tbl_group_surgeon_lname)."' 
															AND patient_in_waiting_tbl.patient_id=patient_data_tbl.patient_id
															$andSchCanceledSubQry
															$andStubInfoSubQry  
															$facQry
															ORDER BY $wtOrderByQry ";
										
										$stub_tbl_res = imw_query($stub_tbl_query) or die($stub_tbl_query.imw_error());
										$stub_tbl_num_row=imw_num_rows($stub_tbl_res);
										if($stub_tbl_num_row>0){
								?>
                <tr class="text_home valignMiddle"   style=" height:20px; font-size:11px;" >
                  <td colspan="18" class="nowrap" style=" background-image:url(<?php echo $bgHeadingImage;?>);padding-left:1px;  "><table class="table_pad_bdr alignLeft">
                      <tr class="text_homeb"  style="font-size:9px; ">
                        <td><?php
															if($getStubInfoNumRow>0 && $displayStatusCheckBox=='true') {?>
                          <input title="Check/Uncheck All" type="checkbox"  name="elemCheckAllId<?php echo $cntr;?>" id="elemCheckAllId<?php echo $cntr;?>" onClick="javascript:checkAllStatusFun(this,'<?php echo $stub_tbl_num_row;?>','<?php echo $cntr;?>');" value="" >
                          <?php
															}?>
                          Surgeon   &nbsp;<b>Dr. <?php echo $stub_surgeon_name.' ('.$stub_tbl_num_row.')';?></b></td>
                      </tr>
                    </table></td>
                </tr>
                <?php }
										$stub_tbl_groupTemp[] = strtolower($stub_surgeon_name_chk);
										
										
										$r=0;
										while($getStubInfoRow = imw_fetch_array($stub_tbl_res)) {
											$r++;
											
											$bookBgTrColor="#FFFFFF";
											if($cntr%2==0) { $bookBgTrColor = "#F8F9F7";}//#F3F8F2
											$userTblUsersId = $getStubInfoRow['usersId'];
											$patient_waiting_patient_primary_procedure 	= $getStubInfoRow['patient_primary_procedure'];
											$patient_waiting_patient_secondary_procedure= $getStubInfoRow['patient_secondary_procedure'];
											$patient_waiting_patient_tertiary_procedure = $getStubInfoRow['patient_tertiary_procedure'];
											$patient_waiting_dos = $getStubInfoRow['patient_waiting_dos'];
											$iAscSyncroStatus = $getStubInfoRow['iAscSyncroStatus'];
											$iAscReSyncroStatus = $getStubInfoRow['iAscReSyncroStatus'];
											$reSyncroVia = $getStubInfoRow['reSyncroVia'];
											//echo '<br>'.$getProcedureIdQry = "SELECT * FROM procedures WHERE name != '' AND procedureAlias != '' AND (name = '$patient_waiting_patient_primary_procedure' OR procedureAlias = '$patient_waiting_patient_primary_procedure')";
											$getProcedureIdQry = "SELECT * FROM procedures WHERE ((name != '' AND name = '$patient_waiting_patient_primary_procedure') OR (procedureAlias != '' AND procedureAlias = '$patient_waiting_patient_primary_procedure')) And del_status <> 'yes' ";
											$getProcedureIdRes = imw_query($getProcedureIdQry);
											$patient_primary_procedure_id='';
											if(imw_num_rows($getProcedureIdRes) == 0){
												$getProcedureIdQry = "SELECT * FROM procedures WHERE (name != '' AND name = '$patient_waiting_patient_primary_procedure') OR (procedureAlias != '' AND procedureAlias = '$patient_waiting_patient_primary_procedure') ";
												$getProcedureIdRes = imw_query($getProcedureIdQry);
											}
												
											if(imw_num_rows($getProcedureIdRes)>0){
												$getProcedureIdRow = imw_fetch_array($getProcedureIdRes);
												$patient_primary_procedure_id = $getProcedureIdRow['procedureId'];
											}
											//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
												$surgeonProfileIdArr=array();
												if($userTblUsersId<>"") {
													$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$userTblUsersId'";
													$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
													while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
														$surgeonProfileIdArr[] = $selectSurgeonRow['surgeonProfileId'];
													}
													if(is_array($surgeonProfileIdArr)){
														$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr);
													}
													if(!$surgeonProfileIdImplode) {
														$surgeonProfileIdImplode = 0;
													}
													$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
													$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die($selectSurgeonProcedureQry.imw_error());
													$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
													if($selectSurgeonProcedureNumRow>0) {
														while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
															$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
															if($patient_primary_procedure_id == $surgeonProfileProcedureId) {
																$consentTemplateFound = "true";
																$consentMultipleTemplateId = $selectSurgeonProcedureRow['consentTemplateId'];
															}		
														}
													}	
												}
												$consentMultipleTemplateIdArr = array();
												$consentMultipleTemplateIdArr = explode(',',$consentMultipleTemplateId);
		
											//END GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
											
											// Start getting procedure profile for primary procedure in case of surgeon profile not found
											if($consentTemplateFound !=  "true")
											{
												$proceduresArr	=	array($patient_primary_procedure_id);
												foreach($proceduresArr as $procedureId)
												{
													if($procedureId)
													{		
														$procPrefCardQry	=	"Select * From procedureprofile Where procedureId = '".$procedureId."' ";
														$procPrefCardSql		=	imw_query($procPrefCardQry) or die( 'Error at line no.'. (__LINE__).': '.imw_error());
														$procPrefCardCnt	=	imw_num_rows($procPrefCardSql);
														if($procPrefCardCnt > 0 )
														{
															$procPrefCardRow		=	imw_fetch_object($procPrefCardSql);
															$consentTemplateFound = "true";
															$consentMultipleTemplateId	= $procPrefCardRow->consentTemplateId;
															
															break; 
														}
													}
												}
												
												$consentMultipleTemplateIdArr = array();
												$consentMultipleTemplateIdArr = explode(',',$consentMultipleTemplateId);
											}
											// End getting procedure profile for primary procedure in case of surgeon profile not found
											
											$patient_in_waiting_id 		= $getStubInfoRow['patient_in_waiting_id'];
											$patient_id 				= $getStubInfoRow['patient_id'];
											$patient_status				= $getStubInfoRow['patient_status'];
											
											
											$hrefBookingStart = "<a class='link_home' style='cursor:pointer;' href='#' onClick='changeBookingBgColor(\"$cntr\");top.iframeHome.iOLinkPtDetailFrameId.waitingPatient_info(\"$patient_id\",\"$patient_in_waiting_id\",\"\",\"$patient_status\");'>";
											$hrefBookingEnd="</a>";
											
											$patient_in_waiting_dos_temp= $getStubInfoRow['dos'];
											$patient_in_waiting_dos_split= explode("-",$patient_in_waiting_dos_temp);
											$patient_in_waiting_dos 	= $patient_in_waiting_dos_split[1]."-".$patient_in_waiting_dos_split[2]."-".$patient_in_waiting_dos_split[0];
											
											//START PERSONAL DETAIL
											$patientDataTblQry 			= "SELECT * FROM `patient_data_tbl` WHERE patient_id='".$patient_id."' AND patient_id!=''";
											$patientDataTblRes 			= imw_query($patientDataTblQry) or die(imw_error()); 
											$patientDataTblNumRow 		= imw_num_rows($patientDataTblRes);
											$patient_name				='';
											$patient_dob				='';
											$patient_address1			='';
											$patient_city				='';
											$patient_state				='';
											$patient_zip				= '';
											if($patientDataTblNumRow>0) {
												$patientDataTblRow 		= imw_fetch_array($patientDataTblRes);
										
												$patient_first_name 	= $patientDataTblRow['patient_fname'];
												$patient_middle_name 	= $patientDataTblRow['patient_mname'];
												$patient_last_name 		= $patientDataTblRow['patient_lname'];
												$patient_name 			= $patient_last_name.", ".$patient_first_name;
											
												$patient_dob_temp 		= $patientDataTblRow['date_of_birth'];
												$patient_dob='';
												if($patient_dob_temp!=0) { $patient_dob = date('m/d/y',strtotime($patient_dob_temp)); }
											
												$patient_address1		= $patientDataTblRow['street1'];
												$patient_city 			= $patientDataTblRow['city'];
												$patient_state 			= $patientDataTblRow['state'];
												$patient_zip 			= $patientDataTblRow['zip'];
											}
											//END PERSONAL DETAIL
											
											
											
											$patient_waiting_commentTemp= stripslashes($getStubInfoRow['comment']);
											$patient_waiting_comment 	= wordwrap($patient_waiting_commentTemp, 15, "<br>", 1);
											$patient_waiting_site 		= $getStubInfoRow['site'];
											$iAscSyncroCount			= "";
											if($getStubInfoRow['iAscSyncroCount']) {
												$iAscSyncroCount 		= $getStubInfoRow['iAscSyncroCount'];
											}
											$patient_waiting_operator_id= $getStubInfoRow['operator_id'];
											$operatorInitials=$operatorInitialsArr[$patient_waiting_operator_id];
											
											
											//START APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
												$OsODOuValue = '';
												if($patient_waiting_site=='left') {
													$OsODOuValue=' (OS)';
												}else if($patient_waiting_site=='right') {
													$OsODOuValue=' (OD)';
												}else if($patient_waiting_site=='both') {
													$OsODOuValue=' (OU)';
												}else if($patient_waiting_site=='left upper lid') {
													$OsODOuValue=' (LUL)';
												}else if($patient_waiting_site=='left lower lid') {
													$OsODOuValue=' (LLL)';
												}else if($patient_waiting_site=='right upper lid') {
													$OsODOuValue=' (RUL)';
												}else if($patient_waiting_site=='right lower lid') {
													$OsODOuValue=' (RLL)';
												}
												if(trim($patient_waiting_patient_primary_procedure)) {
													$patient_waiting_patient_primary_procedure = $patient_waiting_patient_primary_procedure.$OsODOuValue;
												}
												$brTagSec=$brTagTer="";
												$patient_waiting_patient_secondary_procedure_eye 	= $patient_waiting_patient_tertiary_procedure_eye = '';
												if(trim($patient_waiting_patient_secondary_procedure)) {
													$patient_waiting_patient_secondary_procedure_eye= $patient_waiting_patient_secondary_procedure.$OsODOuValue;
													$brTagSec = "<br>";	
												}
												if(trim($patient_waiting_patient_tertiary_procedure)) {
													$patient_waiting_patient_tertiary_procedure_eye = $patient_waiting_patient_tertiary_procedure.$OsODOuValue;
													$brTagTer = "<br>";
												}
											//END APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
											
											//GET AGE OF PATIENT
												list($temp_year,$temp_month,$temp_day) = explode('-',$patient_dob_temp);
												$dformatNew="-";
												$create_dateiOLink = $temp_month."-".$temp_day."-".$temp_year;
												$patient_ageNew=round(dateDiffCommon($dformatNew,date("m-d-Y", time()), $create_dateiOLink)/365, 0);
												if(date("m")<$temp_month || date("d")<$temp_day){
													$patient_ageNew=$patient_ageNew-1;
												}
											//END GET AGE OF PATIENT
									
											//START GET SCAN DETAIL
												$iolImgSrc 			= "images/chk_off1.gif";
												$ekgImgSrc 			= "images/chk_off1.gif";
												$hPImgSrc 			= "images/chk_off1.gif";
												$healthQuestImgSrc 	= "images/chk_off1.gif";
												$ocularHxImgSrc 	= "images/chk_off1.gif";
												$EkgHpOcularHealthArr = array('iol','ekg', 'h&p', 'healthQuest','ocularHx');
												foreach($EkgHpOcularHealthArr as $fldrNme) {
													unset($conditionArr);
													$conditionArr['patient_in_waiting_id'] = $patient_in_waiting_id;
													$conditionArr['patient_id'] = $patient_id;
													$conditionArr['iolink_scan_folder_name'] = $fldrNme;
													$consentHpEkgExistDetails = $objManageData->getMultiChkArrayRecords('iolink_scan_consent', $conditionArr);	
													if($consentHpEkgExistDetails) {
														if($fldrNme=='iol') 		{ 	$iolImgSrc 			= "images/check_mark16.png"; }
														if($fldrNme=='ekg') 		{ 	$ekgImgSrc 			= "images/check_mark16.png"; }
														if($fldrNme=='h&p') 		{ 	$hPImgSrc 			= "images/check_mark16.png"; }
														if($fldrNme=='healthQuest') { 	$healthQuestImgSrc 	= "images/check_mark16.png"; }
														if($fldrNme=='ocularHx') 	{ 	$ocularHxImgSrc 	= "images/check_mark16.png"; }
													}
												}
												//START CODE TO CHECK HEALTH-QUEST EXIST IN DATABASE
												$chkHealthQuestFormExistQry = "SELECT preOpHealthQuesId FROM iolink_preophealthquestionnaire 
																				WHERE patient_in_waiting_id='".$patient_in_waiting_id."'
																				AND (form_status='completed' OR form_status='not completed')";
												$chkHealthQuestFormExistRes = imw_query($chkHealthQuestFormExistQry) or die(imw_error());
												$chkHealthQuestFormExistNumRow = imw_num_rows($chkHealthQuestFormExistRes);
												if($chkHealthQuestFormExistNumRow>0) {
													$healthQuestImgSrc 	= "images/check_mark16.png";
												}
												//END CODE TO CHECK HEALTH-QUEST EXIST IN DATABASE
											
												//START CODE TO CHECK HISTORY AND PHYSICAL EXIST IN DATABASE
												$chkHPFormExistQry = "SELECT history_physical_id FROM iolink_history_physical 
																				WHERE pt_waiting_id='".$patient_in_waiting_id."'
																				AND (form_status='completed' OR form_status='not completed')";
												$chkHPFormExistRes = imw_query($chkHPFormExistQry) or die(imw_error());
												$chkHPFormExistNumRow = imw_num_rows($chkHPFormExistRes);
												if($chkHPFormExistNumRow>0) {
													$hPImgSrc 	= "images/check_mark16.png";
												}
												//END CODE TO CHECK HISTORY AND PHYSICAL EXIST IN DATABASE
											
											//END GET SCAN DETAIL
											
											//START GETTING CONSENT FORM COLUMNS
												if(!$consentMultipleTemplateId) { $consentMultipleTemplateId=0;  }
												$selectConsentTemplateQry 	= "select * from consent_forms_template where consent_id in($consentMultipleTemplateId) and consent_delete_status!='true' order by consent_id";
												//$selectConsentTemplateQry 	= "select * from consent_forms_template where consent_delete_status!='true' order by consent_id";
												$selectConsentTemplateRes 	= imw_query($selectConsentTemplateQry) or die(imw_error());
												$selectConsentTemplateNumRow= imw_num_rows($selectConsentTemplateRes);
												$blIncompleteForms 			= false;
												if($selectConsentTemplateNumRow>0) {
													while($selectConsentTemplateRow = imw_fetch_array($selectConsentTemplateRes)) {
														$dispScnEFormStatus='';
														$dispEFormStatus='';
														$blStatus = "off";
														$selectConsentTemplateId = $selectConsentTemplateRow['consent_id'];
														//$showCheckMark = '<img src="images/chk_off1.gif" style=" cursor:pointer; " onClick="document.frmOpenConsentWin.hidd_scanDoneStatusId.value='."''".';document.frmOpenConsentWin.hidd_eFromDoneStatusId.value='."''".';opnConsentWindow('."'$patient_id'".','."'$patient_in_waiting_id'".','."'$selectConsentTemplateId'".','."'$consentMultipleTemplateId'".','."''".');">';
														if(!in_array($selectConsentTemplateId,$consentMultipleTemplateIdArr)) {
															//$showCheckMark = 'N/A';
															$blStatus = "na";
														}
														//START CONSENT E-FORM DETAIL
														unset($conditionArr);
														$conditionArr['fldPatientWaitingId'] = $patient_in_waiting_id;
														$conditionArr['consent_template_id'] = $selectConsentTemplateId;
														
														$consentEFormExistDetails = $objManageData->getMultiChkArrayRecords('iolink_consent_filled_form', $conditionArr);	
														
														if($consentEFormExistDetails) {
															foreach($consentEFormExistDetails as $consentEFormKey) {
																$consentEFormName =  $consentEFormKey->surgery_consent_name;
																$consentEFormSignedStatus =  $consentEFormKey->consentSignedStatus;
																if($consentEFormSignedStatus=='true') {
																	$dispEFormStatus = 'EFormDone';
																	//$showCheckMark = '<img src="images/check_mark16.png" style=" cursor:pointer; " onClick="document.frmOpenConsentWin.hidd_scanDoneStatusId.value='."'$dispScnStatus'".';document.frmOpenConsentWin.hidd_eFromDoneStatusId.value='."'$dispEFormStatus'".';opnConsentWindow('."'$patient_id'".','."'$patient_in_waiting_id'".','."'$selectConsentTemplateId'".','."'$consentMultipleTemplateId'".','."'opnDisplayWin'".');">';
																	$blStatus = "on";
																}
															}
														}
														//END CONSENT E-FORM DETAIL
														
														//START CODE TO CHECK CONSENT FORM SIGNED VIA PDF OR NOT
														$chkIolinkScanConsentQry 	= "select * from iolink_scan_consent where patient_in_waiting_id='".$patient_in_waiting_id."' AND idoc_consent_template_id = '".$selectConsentTemplateId."' order by scan_consent_id";
														$chkIolinkScanConsentRes 	= imw_query($chkIolinkScanConsentQry) or die(imw_error());
														$chkIolinkScanConsentNumRow = imw_num_rows($chkIolinkScanConsentRes);
														if($chkIolinkScanConsentNumRow>0) {
															$blStatus = "on";	
														}
														//END CODE TO CHECK CONSENT FORM SIGNED VIA PDF OR NOT
														
														if($blIncompleteForms == false && $blStatus != "on"){
															$blIncompleteForms = true;
														}
													}
												}else {
													$blIncompleteForms 		= true;
												}
												
											//GETTING CONSENT FORM COLUMNS	
											
											//Start Medication Status	
												$selectCountMedication 		= "select count(*) from iolink_patient_prescription_medication WHERE patient_id = '$patient_id' and patient_in_waiting_id = '$patient_in_waiting_id' ";
												$selectConsentTemplateRes 	= imw_query($selectCountMedication) or die(imw_error());
												list($selectCountMedicationTot) = imw_fetch_array($selectConsentTemplateRes);
												if($selectCountMedicationTot){
													$medicationSatus 		= "Yes";
												}
												else{
													$medicationSatus 		= "None";
												}
												//$selectConsentTemplateNumRow = imw_num_rows($selectConsentTemplateRes);
											
											//End Medication Status	
											//Start Allergy Status	
												$allergyColor 				= '#669966';
												$queryGetAllergy 			= "SELECT allergy_name from iolink_patient_allergy WHERE patient_id = '$patient_id' and patient_in_waiting_id = '$patient_in_waiting_id'";
												$queryGetAllergyRes 		= imw_query($queryGetAllergy) or die(imw_error());
												$queryGetAllergyResNumRow 	= imw_num_rows($queryGetAllergyRes);
												if($queryGetAllergyResNumRow>0){
													
													$queryGetAllergyRow 	= imw_fetch_array($queryGetAllergyRes);
													$strPatientAllergyName 	= $queryGetAllergyRow['allergy_name'];
													if(trim($strPatientAllergyName)=='NKA' && $queryGetAllergyResNumRow==1) {
														$allergyStatus 		= 'NKA';
													}else {
														$allergyStatus 		= 'Allergy';
														$allergyColor 		= '#FF0000';
													}
												}
												else{
													$allergyStatus 			= '';
												}
											
											//End Allergy Status
											
											//START GET IOL MANUFACTURER VALUE	
												$iolManufacturerValue 		= '';
												$iolModelValue				= '';
												$iolDiopterValue			= '';
												$iolManufacturerNameQry 	= "SELECT * FROM iolink_iol_manufacturer 
																				WHERE patient_id = '".$patient_id."'
																				AND patient_id != '' 
																				AND patient_in_waiting_id = '".$patient_in_waiting_id."' 
																				AND patient_in_waiting_id!= ''
																				ORDER BY opRoomDefault DESC, iol_manufacturer_id ASC
																				";
												$iolManufacturerNameRes 	= imw_query($iolManufacturerNameQry) or die(imw_error());
												$iolManufacturerNameNumRow 	= imw_num_rows($iolManufacturerNameRes);
												
											//END GET IOL MANUFACTURER VALUE	
											
											//START TO SET STATUS FIELD
											$reSyncBgColor='#FFFFFF';
											if($iAscSyncroStatus=='Syncronized') {
												$showSyncroStatus 			=  "<img src='images/check_mark16.png' alt='iOsync Status'>";
												if($iAscReSyncroStatus=='yes') {
													$reSyncBgColor			=	'#F6C67A';
													
													if($reSyncroVia == 'iDOC') {
														$reSyncBgColor		=	'#f2dd55';	
													}
												}
											}else {
												$showSyncroStatus 			= "<img src='images/chk_off1.gif' alt='iOsync Status'>";
												$reSyncBgColor='#FFFFFF';	
											}
											//END TO SET STATUS FIELD  
												
											$priDataArr = $secDataArr = $terDataArr = array();
											$priDataArr = getBookingInsuranceFlag($patient_id,'primary',$patient_name);
											$secDataArr = getBookingInsuranceFlag($patient_id,'secondary',$patient_name);
											$terDataArr = getBookingInsuranceFlag($patient_id,'tertiary',$patient_name);
											 
											$priInsImgSrc = $priDataArr[0];
											$secInsImgSrc = $secDataArr[0];
											$terInsImgSrc = $terDataArr[0];
												
												?>
                <tr id="bookingSrNo<?php echo $cntr;?>" class="text_home valignTop"  style="font-size:11px;   height:22px;">
                  <td class="alignLeft nowrap" style="width:37px; background-color:<?php echo $reSyncBgColor;?>;border:2px solid; border-color:#F3F8F2;"><?php 
													if($displayStatusCheckBox=='true') {?>
                    <input type="checkbox"  name="elem_patient_in_waiting_id[]" id="elemPatientInWaitingId<?php echo $cntr;?>" value="<?php echo $patient_in_waiting_id; ?>" >
                    <?php 
													}
													echo $showSyncroStatus;?></td>
                  <td class="capitalize" style="width:103px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><?php 
														$inCompleteinsuranceData='';
														if($patient_id) {
															$inCompleteinsuranceData = getIncompleteInsFun($patient_id,$bgHeadingImage,$patient_name);
															echo $inCompleteinsuranceData;
														
															//START CODE TO SHOW INCOMPLETE INS-COMPANIES
															
															if(isset($priDataArr[2]) && $priDataArr[2]) {
																$inCompInsProvPriData = incompleteInsProviderDiv($priDataArr[1],$priDataArr[2],$priDataArr[3],$priDataArr[4],$priDataArr[5]);
																echo $inCompInsProvPriData;
															}
															if(isset($secDataArr[2]) &&  $secDataArr[2]) {
																$inCompInsProvSecData = incompleteInsProviderDiv($secDataArr[1],$secDataArr[2],$secDataArr[3],$secDataArr[4],$secDataArr[5]);
																echo $inCompInsProvSecData;
															}
															if(isset($terDataArr[2]) &&  $terDataArr[2]) {
																$inCompInsProvTerData = incompleteInsProviderDiv($terDataArr[1],$terDataArr[2],$terDataArr[3],$terDataArr[4],$terDataArr[5]);
																echo $inCompInsProvTerData;
															}
															//END CODE TO SHOW INCOMPLETE INS-COMPANIES
														}
														echo $hrefBookingStart.$patient_name.' - '.$patient_dob.$hrefBookingEnd;?>
                    &nbsp;
                    <?php if($inCompleteinsuranceData) {/*?><img src="images/alert_icon.png" border="0" style="cursor:pointer; " onMouseOver="showIncompleteIns('divIncompleteInsId<?php echo $patient_id;?>','block',findPos_Y('patientDemo<?php echo $cntr;?>'),findPos_X('patientDemo<?php echo $cntr;?>'));"  /><?php */} ?></td>
                  <td id="patientDemo<?php echo $cntr;?>" style="width:122px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><?php echo $hrefBookingStart.wordwrap($patient_waiting_patient_primary_procedure, 20, "<br>", 1).$brTagSec.wordwrap($patient_waiting_patient_secondary_procedure_eye, 20, "<br>", 1).$brTagTer.wordwrap($patient_waiting_patient_tertiary_procedure_eye, 20, "<br>", 1).$hrefBookingEnd;?>
                    <?php if($show_canceled=="yes") {echo " - ".$patient_waiting_dos;}?></td>
                  <td class="alignLeft" style="width:101px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><?php echo $hrefBookingStart.$patient_waiting_comment.$hrefBookingEnd;?></td>
                  <?php
												$anyoneSelectedConsentForm = $consentMultipleTemplateIdArr[0];
												if ($blIncompleteForms == true){
													$consentFormsImageSrc = "images/chk_off1.gif";
												}else{
													$consentFormsImageSrc = "images/check_mark16.png";
												}
												//incompleteInsProviderDiv($type,$insProviderArr,$insCaseNameArr,$ptName,$ptId);
												?>
                  <td class="alignCenter" style="width:45px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><img alt="Consent Form Status" src="<?php echo $consentFormsImageSrc;?>" style="cursor:pointer; " onClick="document.frmOpenConsentWin.hidd_consentMultipleTemplateId.value='<?php echo $consentMultipleTemplateId;?>';opnscanEformWindow('<?php echo $patient_id;?>','<?php echo $patient_in_waiting_id;?>','','<?php echo $consentMultipleTemplateId;?>','opnDisplayWin');"></td>
                  <td class="alignCenter nowrap" style="width:15px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><img alt="IOL Status"  src="<?php echo $iolImgSrc;?>" style="cursor:pointer; " onClick="document.frmOpenConsentWin.hidd_consentMultipleTemplateId.value='<?php echo $consentMultipleTemplateId;?>';opnscanEformWindow('<?php echo $patient_id;?>','<?php echo $patient_in_waiting_id;?>','','<?php echo $consentMultipleTemplateId;?>','opnDisplayWin','scanIOLFolder');"></td>
                  <td class="alignCenter nowrap" style="width:38px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><img alt="Ocular Hx Status"  src="<?php echo $ocularHxImgSrc;?>" style="cursor:pointer; " onClick="document.frmOpenConsentWin.hidd_consentMultipleTemplateId.value='<?php echo $consentMultipleTemplateId;?>';opnscanEformWindow('<?php echo $patient_id;?>','<?php echo $patient_in_waiting_id;?>','','<?php echo $consentMultipleTemplateId;?>','opnDisplayWin','scanOcularHx');"></td>
                  <td class="alignCenter nowrap" style="width:48px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><img alt="Health Quest Status"  src="<?php echo $healthQuestImgSrc;?>" style="cursor:pointer; " onClick="document.frmOpenConsentWin.hidd_consentMultipleTemplateId.value='<?php echo $consentMultipleTemplateId;?>';opnscanEformWindow('<?php echo $patient_id;?>','<?php echo $patient_in_waiting_id;?>','','<?php echo $consentMultipleTemplateId;?>','opnDisplayWin','scanHealthQuest');"></td>
                  <td class="alignCenter nowrap" style="width:30px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><img alt="H&amp;P Status"  src="<?php echo $hPImgSrc;?>" style="cursor:pointer; " onClick="document.frmOpenConsentWin.hidd_consentMultipleTemplateId.value='<?php echo $consentMultipleTemplateId;?>';opnscanEformWindow('<?php echo $patient_id;?>','<?php echo $patient_in_waiting_id;?>','','<?php echo $consentMultipleTemplateId;?>','opnDisplayWin','scanHP');"></td>
                  <td class="alignCenter nowrap" style="width:25px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><img alt="EKG Status"  src="<?php echo $ekgImgSrc;?>" style="cursor:pointer; " onClick="document.frmOpenConsentWin.hidd_consentMultipleTemplateId.value='<?php echo $consentMultipleTemplateId;?>';opnscanEformWindow('<?php echo $patient_id;?>','<?php echo $patient_in_waiting_id;?>','','<?php echo $consentMultipleTemplateId;?>','opnDisplayWin','scanEKG');"></td>
                  <td class="alignCenter nowrap" style="width:25px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><img alt="Primary Ins. Status"  src="<?php echo $priInsImgSrc;?>" style="cursor:pointer; " onClick="showIns('<?php echo $patient_id; ?>','<?php echo $patient_in_waiting_id; ?>','primary');" onMouseOver="showIncompleteIns('divIncompleteInsProviderIdprimary<?php echo $patient_id;?>','block',findPos_Y('patientDemo<?php echo $cntr;?>'),findPos_X('patientDemo<?php echo $cntr;?>'));" /></td>
                  <td class="alignCenter nowrap" style="width:30px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><img alt="Secondary Ins. Status"  src="<?php echo $secInsImgSrc;?>" style="cursor:pointer; " onClick="showIns('<?php echo $patient_id; ?>','<?php echo $patient_in_waiting_id; ?>','secondary');" onMouseOver="showIncompleteIns('divIncompleteInsProviderIdsecondary<?php echo $patient_id;?>','block',findPos_Y('patientDemo<?php echo $cntr;?>'),findPos_X('patientDemo<?php echo $cntr;?>'));" /></td>
                  <td class="alignCenter nowrap" style="width:30px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><img alt="Tertiary Ins. Status"  src="<?php echo $terInsImgSrc;?>" style="cursor:pointer; " onClick="showIns('<?php echo $patient_id; ?>','<?php echo $patient_in_waiting_id; ?>','tertiary');" onMouseOver="showIncompleteIns('divIncompleteInsProviderIdtertiary<?php echo $patient_id;?>','block',findPos_Y('patientDemo<?php echo $cntr;?>'),findPos_X('patientDemo<?php echo $cntr;?>'));"/></td>
                  <td class="alignCenter nowrap" style="width:30px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;cursor:pointer;" onClick="javascript:window.open('medication_popup.php?patient_in_waiting_id=<?php echo $patient_in_waiting_id; ?>&amp;patient_id=<?php echo $patient_id; ?>','iolink_med_win', 'width=590,height=520,top=100,left=300,resizable=yes,scrollbars=1');"><?php echo $medicationSatus; ?></td>
                  <td class="alignCenter nowrap" style="width:40px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;cursor:pointer;color:<?php echo $allergyColor;?>;" onClick="javascript:window.open('allergy_popup.php?patient_in_waiting_id=<?php echo $patient_in_waiting_id; ?>&amp;patient_id=<?php echo $patient_id; ?>','iolink_allergy_win', 'width=400,height=520,top=100,left=300,resizable=yes,scrollbars=1');"><?php echo $allergyStatus; ?></td>
                  <td class="alignCenter nowrap" style="width:35px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><?php echo $iAscSyncroCount; ?></td>
                  <td style="width:220px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;cursor:pointer;" onClick="javascript:window.open('iOLink_iol_manufacturer.php?patient_in_waiting_id=<?php echo $patient_in_waiting_id; ?>&amp;patient_id=<?php echo $patient_id; ?>','iol_manufacturer_win', 'width=850,height=280,top=100,left=300,resizable=yes,scrollbars=1');"><table class="table_pad_bdr">
                      <?php 
														if($iolManufacturerNameNumRow) {
															$iolManCntr=0;
															while($iolManufacturerNameRow = imw_fetch_array($iolManufacturerNameRes)) {
																$iolManCntr++;
																$iolManufacturerName 	= $iolManufacturerNameRow['manufacture'];
																$iolLensBrand 			= $iolManufacturerNameRow['lensBrand'];
																$iolModelName 			= $iolManufacturerNameRow['model'];
																$iolDiopterName 		= $iolManufacturerNameRow['Diopter'];
																
																$iolManufacturerValue 	= $iolManufacturerName;
																$iolModelValue 			= $iolModelName;
																$iolDiopterValue 		= $iolDiopterName;
																
																$iolManBgColor='#FFFFFF';
																if($iolManCntr%2==0) { $iolManBgColor='#ECF1EA'; }
																$iolLensBrandShow='';
																if($iolLensBrand) { $iolLensBrandShow='('.$iolLensBrand.')'; }
														?>
                      <tr class="valignTop" style="background-color:<?php echo $iolManBgColor;?>;font-size:10px; ">
                        <td class="text_home" style="width:70px;"><?php echo $iolManufacturerName.$iolLensBrandShow;?></td>
                        <td class="text_home" style="width:120px;"><?php echo $iolModelName;?></td>
                        <td class="text_home" style="width:20px;"><?php echo $iolDiopterName;?></td>
                      </tr>
                      <?php
															}
														}
														?>
                    </table></td>
                  <td class="alignCenter nowrap" style="width:35px;border:2px solid;border-color:#F3F8F2;background-color:<?php echo $bookBgTrColor;?>;"><?php echo $operatorInitials;?></td>
                </tr>
                <?php
											$cntr++;
										}
									}
								}
							}else {
							?>
                <tr class="text_10b valignTop"  style="font-size:11px; height:20px; background-color:#F8F9F7;" >
                  <td colspan="15" class="alignLeft" style="padding-left:180px; ">No Record Found</td>
                </tr>
                <?php
							}
							?>
                <tr>
                  <td colspan="15"><input type="hidden" name="hiddRecrdCount" id="hiddRecrdCount" value="<?php echo $cntr;?>"></td>
                </tr>
              </table>
            </div></td>
        </tr>
      </table></td>
  </tr>
</table>
<script>
//START CODE TO SET BACKGROUND COLOR OF SELECTED DATE IN CALENDER
	if(top.iframeHome.iOLinkScheduleFrameId) {
		var schedulerFrameObj = top.iframeHome.iOLinkScheduleFrameId;
		var dayNum = '<?php echo $dayNum;?>';
		dayNum =  parseFloat(dayNum);
		if(schedulerFrameObj.document.getElementById("mon_"+dayNum)) {
			schedulerFrameObj.document.getElementById("hiddSelectedDayId").value=dayNum;
			schedulerFrameObj.iOLink_swap_cal_color("mon_"+dayNum,"Yes",dayNum,parseFloat(schedulerFrameObj.document.getElementById("hiddSelectedPrevDayId").value));
		}
	}	
//END CODE TO SET BACKGROUND COLOR OF SELECTED DATE IN CALENDER
</script>

</body>
<div id="alert_div">
  <p id="alert_div_content1"></p>
  <p id="alert_div_content"></p>
  <p id="alert_div_cont"></p>
  <a href="#" id="io_Sync_Ok" class="io_Sync_Ok"></a><a href="#" id="io_cancel_btn" class="io_cancel_btn"></a> </div>
<div id="alert_div_content2">
  <p id="alert_div_confirm"></p>
  <p id="alert_div_confirm1"></p>
  <a href="#" class="io_Sync_Ok io_sync_ok_1" id="io_Sync_Ok_confirm"></a> </div>
</html>
<!--F9F4EB-->