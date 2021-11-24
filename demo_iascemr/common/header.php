<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	session_start();
	//START session expires
	/* 
	$session_expire = 30; // Minutes  
	session_cache_limiter('private'); 
	session_cache_expire($session_expire); 
	session_set_cookie_params($session_expire * 60); 
	*/
//END  session expires
set_time_limit(-1);

if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	//@header("location:index.php");
	echo '<script>top.location.href="index.php"</script>';
}
include_once('conDb.php');
include_once('admin/classObjectFunction.php');
/*
$expiretime = 60*30; // 30 mins (Sec*Min)
$_SESSION['expire'] = time()+$expiretime; // 
*/
$objManageData = new manageData;
$patient_id = $_REQUEST['patient_id'];
$pConfId = $_REQUEST['pConfId'];
//$ascId = $_REQUEST['ascId'];

$_SESSION['patient_id'] = $patient_id;
$_SESSION['pConfId'] = $pConfId;
//$_SESSION['ascId'] = $ascId;

$multiwin = $_REQUEST['multiwin']; // FOR OPENING MULTIPLE WINDOW  

// GETTING LOGIN USER NAME
	$str = "SELECT CONCAT_WS(' ', fname, mname, lname) 
			as userName FROM users
			WHERE usersId = '$userLoginId'";
	$qry = imw_query($str);
	$fetchRows = imw_fetch_array($qry);
	$userName = $fetchRows['userName'];
// GETTING LOGIN USER NAME


// PROGRESS REPORT
	$query_rsNotes = "SELECT tblprogress_report.txtNote, users.fname, users.user_type FROM tblprogress_report, users WHERE users.usersId = tblprogress_report.usersId ORDER BY tTime DESC LIMIT 1";
	$rsNotes = imw_query($query_rsNotes, $link) or die(imw_error());
	$totalRows_rsNotes = imw_num_rows($rsNotes);

//FUNCTION EDIT BY SURINDER
function dateDiffHeader($dformat, $endDate, $beginDate){
	$date_parts1=explode($dformat, $beginDate);
	$date_parts2=explode($dformat, $endDate);
	$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
	$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
	return $end_date - $start_date;
}
function getPtSxAlert($alertPtId,$ptName) {
	$alertQry 	= "SELECT * FROM iolink_patient_alert_tbl WHERE patient_id = '".$alertPtId."' AND iosync_status='Syncronized' AND alert_disabled!='yes'";	
	$alertRes 	= imw_query($alertQry) or die(imw_error());
	$alrtVal 	= '';
	if(imw_num_rows($alertRes)>0) {
		$bgCol 		= '#BCD2B0';
		$borderCol 	= '#BCD2B0';
		$rowcolor	= '#F1F4F0';
		$alrtVal .= '<div id="divPtSxAlert" onMouseDown="drag_div_move(this, event);" style="position:absolute; display:none; left:300px; top:80px; z-index:1; border:solid 1px '.$borderCol.'; width:350px" class="row">
							<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">
								Patient Sx Alert
								<span onClick="sxAlertDivFun(\'divPtSxAlert\',\'none\',\''.$alertPtId.'\',\'\',\'\',\'\',\'\',\'disableCancelBtnId\');" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
							</div>
							
							<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:90px; background: #FFF; overflow:auto">
						';	
								$i=0;
								while($alertRow = imw_fetch_array($alertRes))	 {
									if($i%2==0) { 
										$a="#F1F4F0"; 
									} else { 
										$a="#FFFFFF";
									} 
									
									$alrtVal .= '	
										<div class="row hoverdiv" style="background-color:'.$a.'; border-bottom : solid 1px #DDD; padding-top:5px; padding-bottom:5px; " >
											<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" >'.$alertRow["alert_content"].'</div>
										</div>
										
											
										';
									$i++;
								}
								
		$alrtVal .= '</div>
						    
							<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" id="disableCancelBtnId"  style=" width:100%;display:none; border-top:solid 1px #EEE; background: #FFF; ">
						  	
								<a href="#" class="btn btn-success" id="alertDisableBtn" style="border:none;" title="Disable" onClick="sxAlertDivFun(\'divPtSxAlert\',\'none\',\''.$alertPtId.'\',\''.$ptName.'\',\'ptNameId\',\''.$_SESSION["loginUserId"].'\',\'\',\'disableCancelBtnId\');">Disable</a>
								
								<a href="#" class="btn btn-danger" id="alertCancelBtn" onClick="sxAlertDivFun(\'divPtSxAlert\',\'none\',\''.$alertPtId.'\',\'\',\'\',\'\',\'\',\'disableCancelBtnId\');">Cancel</a>
								
						  </div>
						  
						</div>';
	}
	return $alrtVal;
}

	$patientHeaderInfo = $objManageData->getRowRecord('patient_data_tbl', 'patient_id', $_SESSION['patient_id']);
	$patientHeaderFname = $patientHeaderInfo->patient_fname;
	$patientHeaderMname = $patientHeaderInfo->patient_mname;
	$patientHeaderNname = $patientHeaderInfo->patient_lname;
	$patientHeaderImwPatientId = $patientHeaderInfo->imwPatientId;
	//$patientHeaderName = $patientHeaderFname." ".$patientHeaderMname." ".$patientHeaderNname;
	$patientHeaderID	= $patientHeaderInfo->patient_id;
	$imwPatientHeaderID = "";
	if( defined("SHOW_IMW_PATIENT_ID") && constant("SHOW_IMW_PATIENT_ID")=="YES" && trim($patientHeaderImwPatientId)) {
		$imwPatientHeaderID = " / ".$patientHeaderImwPatientId;
	}
	$patientHeaderName = $patientHeaderNname.", ".$patientHeaderFname.' - '.$patientHeaderID.$imwPatientHeaderID;
	
	$addressPatientstreet1 	= addslashes($patientHeaderInfo->street1);
	$addressPatientstreet2 	= addslashes($patientHeaderInfo->street2);	
	$addressPatientcity 	= addslashes($patientHeaderInfo->city);	
	$addressPatientstate 	= $patientHeaderInfo->state;	
	$addressPatientzip 		= $patientHeaderInfo->zip;	
	
?>
<script type="text/javascript" src="js/epost.js"></script>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
</script>

<script type="text/JavaScript">
	function MM_openBrWindow(theURL,winName,features) {
	  window.open(theURL,winName,features).focus();
	}
	function closeDiv(){	
		if(document.getElementById('evaluationProgressDiv')){
			document.getElementById('evaluationProgressDiv').style.display = 'none';
		}
	}


	function close_mainpage() {
		var multiwin = '<?php echo $multiwin;?>';
		if(multiwin=='yes') {
			window.close();
		}else {
			location.href = 'home.php?pageclose=y&amp;cancelConfirm=true';
		}
		
	}
	
	function GetXmlHttpObjectAssist() {
		var objXMLHttp=null
		if (window.XMLHttpRequest)
		{
		objXMLHttp=new XMLHttpRequest()
		}
		else if (window.ActiveXObject)
		{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
		}
		return objXMLHttp
	}	

//SET ASSIST BY TRANSLATOR CHECKBOX (CHECKED OR UNCHECKED) IN HEADER
	function assistChangeFun(chbx_id,pConfId) {
		xmlHttp=GetXmlHttpObjectAssist();
		if (xmlHttp==null) {
			alert ("Browser does not support HTTP Request");
			return;
		} 
		var chbx_idChecked = document.getElementById(chbx_id).checked;
		//CODE TO SET ASSIST BY TRANSLATOR CHECKBOX (CHECKED OR UNCHECKED) IN LOCAL ANESTHESIA RECOED
			if(top.frames[0].frames[0].document.getElementById('chbx_assist_id')) {
				if(chbx_idChecked==true) {
					top.frames[0].frames[0].document.getElementById('chbx_assist_id').checked=true;
				}else {
					top.frames[0].frames[0].document.getElementById('chbx_assist_id').checked=false;
				}
			}
		//END CODE TO SET ASSIST BY TRANSLATOR CHECKBOX (CHECKED OR UNCHECKED) IN LOCAL ANESTHESIA RECOED
		
		if(chbx_idChecked==true) {
			chkBxAssist = "yes";
		}else {
			chkBxAssist = "no";
		}
		var url="common/assist_by_translator_ajax.php";
		url=url+"?chkBxAssist="+chkBxAssist;
		url=url+"&pConfId="+pConfId;
		
		xmlHttp.onreadystatechange=displayAssistChangeFun;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function displayAssistChangeFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") {
			//alert(xmlHttp.responseText);
			//DO NOTHING , IT IS DONE IN OTHER WAY
		}
	}
//END SET ASSIST BY TRANSLATOR CHECKBOX (CHECKED OR UNCHECKED) IN HEADER
			
function headerInfoDisplayFn(addressDivId) {
	if(document.getElementById(addressDivId)) {
		if(document.getElementById(addressDivId).style.display=='none') {
			document.getElementById(addressDivId).style.display='block';
		}else if(document.getElementById(addressDivId).style.display=='block') {
			document.getElementById(addressDivId).style.display='none';
		}
	}
}

function sxAlertFinalizeFun() { //THIS FUNCTION CALL IN finalize_form.php
	var ptName = "<?php echo $patientHeaderName;?>";
	if(typeof(document.getElementById("ptNameId"))!="undefined") {
		document.getElementById("ptNameId").innerHTML='&nbsp;<span onclick=" " style=" ">'+ptName+'</span>';	
	}
	if(document.getElementById("divPtSxAlert") && typeof(document.getElementById("divPtSxAlert"))!="undefined") {
		document.getElementById("divPtSxAlert").style.display='none';	
	}
}

function sxAlertDivFun(id,blockNone,patient_id1,ptName,ptNameTdId,usrId,hover,btnId) {
	
	
	if(typeof(id && document.getElementById(id))!="undefined") {
		document.getElementById(id).style.display=blockNone;	
	}
	if(typeof(btnId && top.document.getElementById(btnId))!="undefined") {
		top.document.getElementById(btnId).style.display='none';	
	}
	if(hover) {
		if(hover=="clicked") {
			if(typeof(document.getElementById(btnId))!="undefined") {
				document.getElementById(btnId).style.display='block';	
			}
		}
	}
	if(ptName) {//IF PRESS ON Disable BUTTON
		if(typeof(document.getElementById(ptNameTdId))!="undefined") {
			document.getElementById(ptNameTdId).innerHTML='&nbsp;<span onclick=" " style=" ">'+ptName+'</span>';	
			
			xmlHttp=GetXmlHttpObjectAssist();
			if (xmlHttp==null) {
				alert ("Browser does not support HTTP Request");
				return;
			} 
			var url="common/disable_alert_ajax.php";
			url=url+"?patient_id="+patient_id1;
			url=url+"&usrId="+usrId;
			//xmlHttp.onreadystatechange=sxAlertDivFunAjax;
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
			
		}		
	}
}

function progress_notes(id,keyWord,patientId,stub_dos,pConfirmId,imwPatientId,showAllApptStatus) {
	
	var day = '<?php echo $_REQUEST["day"];?>';
	var selected_month_number = '<?php echo $_REQUEST["selected_month_number"];?>';
	var sel_year_number = '<?php echo $_REQUEST["sel_year_number"];?>';
	var sortSurgeonName = '<?php echo $_REQUEST["sortSurgeonName"];?>';
	var sortFieldName = '<?php echo $_REQUEST["sortFieldName"];?>';
	var sortPtAscDsc = '<?php echo $_REQUEST["sortPtAscDsc"];?>';
	var sortStAscDsc = '<?php echo $_REQUEST["sortStAscDsc"];?>';
	var sortAscDscNew='';
	if(sortPtAscDsc) {sortAscDscNew = '&sortPtAscDsc='+sortPtAscDsc;}
	if(sortStAscDsc) {sortAscDscNew = '&sortStAscDsc='+sortStAscDsc;}
	var dosScan = '<?php echo $selected_date; ?>';
	
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	
	var	W	=	( SW > 750 ) ?  960	: SW ;
	var	H	=	W * 0.65
	
	var L	=	(SW - W ) / 3;
    var T	= 	(SH - H ) / 2 - 100 ;
	
	//alert('Screen Width : ' + SW + '\n Screen Height : ' + SH + '\nWindow Width : ' + W + '\n Window Height : ' + H + '\n Left : ' + L + '\n Top : ' + T );
	
	if(patientId) {
		
		window.open('progressnotesPopUp.php?patient_id='+patientId+'&pConfirmId='+pConfirmId+'&ptStubId='+id+'&dosScan='+dosScan,'scanWin', 'width='+W+', height='+ H+',location=no, status=no, resizable=no, scrollbars=no, resizable=no, top='+T+', left='+L);
	}else {
		parent.top.$(".loader").fadeIn('fast').show('fast'); 
		if(keyWord) {
			top.frames[0].location = 'home_inner_front.php?showAllApptStatus='+showAllApptStatus+'&txt_patient_search_id='+keyWord+'&ptStubId='+id+'&pConfirmId='+pConfirmId+'&day='+day+'&selected_month_number='+selected_month_number+'&sel_year_number='+sel_year_number+'&display_cal=none&display_patient_sch=none&display_patient_search=display&chkPtExist=yes&stub_dos='+stub_dos+'&sortSurgeonName='+sortSurgeonName+'&sortFieldName='+sortFieldName+sortAscDscNew;
		}else {
			top.frames[0].location = 'home_inner_front.php?showAllApptStatus='+showAllApptStatus+'&ptStubId='+id+'&pConfirmId='+pConfirmId+'&day='+day+'&selected_month_number='+selected_month_number+'&sel_year_number='+sel_year_number+'&chkPtExist=yes&stub_dos='+stub_dos+'&sortSurgeonName='+sortSurgeonName+'&sortFieldName='+sortFieldName+sortAscDscNew;
		}
	}	
}
function edit(doc)
{
	if(doc==2)
	{
		$('#secProcText').hide();
		$('#secProcForm').show();	
	}
	else
	{
		$('#priProcText').hide();
		$('#priProcForm').show();
			
	}	
}
function closeIt(doc)
{
	if(doc==2)
	{
		$('#secProcText').show();
		$('#secProcForm').hide();	
	}
	else
	{
		$('#priProcText').show();
		$('#priProcForm').hide();	
	}	
}
function save(doc)
{
	var seletedVal='';
	var seletedTxt='';
	var seletedTxtSub='';
	if(doc==2)
	{
		
		$('#secProcText').show();
		$('#secProcForm').hide();
		seletedVal=$('#secProcedureList').val();
		seletedTxt=$("#secProcedureList option:selected").text();	
		if(seletedTxt=="Select Procedure")seletedTxt='';
		if(seletedTxt.length>17)seletedTxtSub = seletedTxt.substring(0,17)+'...';
		else seletedTxtSub = seletedTxt;
		//update shown text	
		var text=seletedTxtSub;//+' <span class="fa fa-edit" onClick="edit(\'2\')"></span>';
		$('#secProcText .hover_tool').html(text);
		$('#secProcText .tooltip_c').html(seletedTxt);
	}
	else
	{
		$('#priProcText').show();
		$('#priProcForm').hide();	
		seletedVal=$('#priProcedureList').val();
		seletedTxt=$("#priProcedureList option:selected").text();
		if(seletedTxt=="Select Procedure")seletedTxt='';
		if(seletedTxt.length>17)seletedTxtSub = seletedTxt.substring(0,17)+'...';
		else seletedTxtSub = seletedTxt;
		var text=seletedTxtSub;//+' <span class="fa fa-edit" onClick="edit(\'1\')"></span>';
		$('#priProcText .hover_tool').html(text);	
		$('#priProcText .tooltip_c').html(seletedTxt);
	}
	
		//cal ajax to update values
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return;
		}
			
		var url='common/update_procedure_ajax.php?field='+doc+'&id='+seletedVal+'&text='+seletedTxt+'&stub_id=<?php echo $_REQUEST['stub_id'];?>&pConfId=<?php echo $_REQUEST['pConfId'];?>&rand='+Math.random();
		
		xmlHttp.onreadystatechange = function () {
			if(typeof(top.mainFrame.main_frmInner.document.forms[0])!='undefined')
			{
			
				var val=top.mainFrame.main_frmInner.document.forms[0].frmAction.value;
				
				if(val=='discharge_summary_sheet.php' && xmlHttp.readyState == 4 && xmlHttp.status == 200)
				{
					//code shifted to end
				}
			}
		};
		xmlHttp.open("GET",url,true);
		
		xmlHttp.send();
		var ascID=top.mainFrame.main_frmInner.document.forms[0].ascId.value;
		var pConfId=top.mainFrame.main_frmInner.document.forms[0].pConfId.value;
		var patient_id=top.mainFrame.main_frmInner.document.forms[0].patient_id.value;
		var innerKey=top.mainFrame.main_frmInner.document.forms[0].innerKey.value;
		var preColor=top.mainFrame.main_frmInner.document.forms[0].preColor.value;
		//reload disharge summary page if procedure is modified
		top.left_link_click('discharge_summary_sheet.php',0,innerKey,preColor,patient_id,pConfId,ascID,0,0,0,1);
}

</script>
<style>
.popupDummy
{
	z-index:1001;
	position: absolute;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #999;
    border: 1px solid rgba(0, 0, 0, .2);
    border-radius: 6px;
    outline: none;
    -webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, .5);
    box-shadow: 0 3px 9px rgba(0, 0, 0, .5);
	padding:10px 0px;
	top:-10px; 
	background:#FFF;
}
</style>
<div id="post" class="drsElement drsMoveHandle" style="display:none; position:absolute;"></div>
<div style="position:static;width:100%;overflow:hidden;">
<?php 
//include("common/allergies_header_pop_up.php");
//include("common/header_address.php");
//include("common/progress_pop_up.php"); 
include("common/pre_defined_popup.php");
//Done By Mamta
$basicgreen		=	"#ECF1EA";
$dark_green		=	"#BCD2B0";
$light_green	=	"#D1E0C9";
$title1_color	=	"#FFFFFF"; //white
$title2_color	=	"#000000"; //black

//Pre_op_physician_order (2 Forms)
$bgdark_orange_physician="#C06E2D";
$bgmid_orange_physician="#DEA068";
$border_color_physician="#BB5E00";
$bglight_orange_physician="#FFE6CC";
$row1color_physician="#FFF2E6";
//Local/gen_anes_record(4 forms)
//$border_blue_local_anes="#080E4C";
$border_blue_local_anes="#323CC0";
$bgdark_blue_local_anes="#3232F0";
$bgmid_blue_local_anes="#80AFEF";
$bglight_blue_local_anes="#EAF4FD";
$tablebg_local_anes="#C5D8FD";
$white="#FFFFFF";
//End edit by mamta


// Done BY Munisha
//Post_op_nursing_order
$title_post_op_nursing_order="#C0AA1E";
$bgcolor_post_op_nursing_order="#F5EEBD";
$border_post_op_nursing_order="#C0AA1E";
$heading_post_op_nursing_order="#EFE492";
$rowcolor_post_op_nursing_order="#FAF6DC";
//pre_op_nursing_order
$title_pre_op_nursing_order="#C0AA1E";
$bgcolor_pre_op_nursing_order="#F5EEBD";
$border_pre_op_nursing_order="#C0AA1E";
$heading_pre_op_nursing_order="#EFE492";
//$rowcolor_pre_op_nursing_order="#FAF6DC";
$rowcolor_pre_op_nursing_order="#F7F3DC";

//laser procedure
$bgcolor_laser_procedure="#F5EEBD";

//op_room_record 
$title_op_room_record="#004587";
$bgcolor_op_room_record="#CFE1F7";
$border_op_room_record="#004587";
$heading_op_room_record="#80A7D6";
$rowcolor_op_room_record="#E2EDFB";
//discharge summary sheet
$title_discharge_summary_sheet="#FF950E";
$bgcolor_discharge_summary_sheet="#FBE8D2";
$border_discharge_summary_sheet="#FF950E";
$heading_discharge_summary_sheet="#FCBE6F";
$rowcolor_discharge_summary_sheet="#FBF5EE";
//Amendments_notes
$title_Amendments_notes="#A0A0C8";
$bgcolor_Amendments_notes="#EEEEFA";
$border_Amendments_notes="#A0A0C8";
$heading_Amendments_notes="#D0D0ED";
$rowcolor_Amendments_notes="#F0F0FA";
//End edit by Munisha

//get detail for logged in facility
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$surgerycenterName=stripcslashes($dataFac->fac_name);
$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);

//$surgeryCenterName = $objManageData->getRowRecord('surgerycenter', 'surgeryCenterId', 1);
//$surgerycenterName = $surgeryCenterName->name;

$patientHeaderstreet1 = $patientHeaderInfo->street1;
if(strlen($patientHeaderstreet1)>13){ $patientHeaderstreet1 = substr($patientHeaderstreet1,0,13)."..";}
$patientHeaderstreet2 = $patientHeaderInfo->street2;	
$patientHeadercity = $patientHeaderInfo->city;	
if(strlen($patientHeadercity)>7){ $patientHeadercity = substr($patientHeadercity,0,7);}
$patientHeaderstate = $patientHeaderInfo->state;	
$patientHeaderzip = $patientHeaderInfo->zip;	
$patientHeaderdate_of_birth = $patientHeaderInfo->date_of_birth;	
		$patientHeader_dob_split = explode("-",$patientHeaderdate_of_birth);
		$patientHeader_dob = $patientHeader_dob_split[1]."-".$patientHeader_dob_split[2]."-".$patientHeader_dob_split[0];

	//CODE TO CALCULATE AGE OF PATIENT
	if($patientHeaderInfo->date_of_birth!="" && $patientHeaderInfo->date_of_birth!="0000-00-00"){
		$tmpHeader_date = $patientHeaderInfo->date_of_birth;
		$patientHeader_age=$objManageData->dob_calc($tmpHeader_date);
	}
	
	//END CODE TO CALCULATE AGE OF PATIENT
	

$patientHeadersexTemp = $patientHeaderInfo->sex;
	$patientHeadersex='';
	if($patientHeadersexTemp=="m") { $patientHeadersex = "Male";} else if($patientHeadersexTemp=="f") { $patientHeadersex = "Female";  }	
$patientHeaderhomePhone = $patientHeaderInfo->homePhone;	
$patientHeaderworkPhone = $patientHeaderInfo->workPhone;
	if($patientHeaderhomePhone<>"") { $patientHeaderPhone =  $patientHeaderhomePhone;}else { $patientHeaderPhone = $patientHeaderworkPhone; }


$patientHeaderID = $patientHeaderInfo->patient_id;

$Confirm_patientHeaderInfo = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $_REQUEST["pConfId"]);
$Confirm_patientHeaderAscID = $Confirm_patientHeaderInfo->ascId;
$finalized_status=$Confirm_patientHeaderInfo->finalize_status;
$Confirm_patientHeaderAdvanceDirective = $Confirm_patientHeaderInfo->advanceDirective;
$Confirm_patientHeaderDosTemp = $Confirm_patientHeaderInfo->dos;
		$Confirm_patientHeaderDos_split = explode("-",$Confirm_patientHeaderDosTemp);
		$Confirm_patientHeaderDos = $Confirm_patientHeaderDos_split[1]."-".$Confirm_patientHeaderDos_split[2]."-".$Confirm_patientHeaderDos_split[0];
$Confirm_patientHeaderSurgeon_name = stripslashes($Confirm_patientHeaderInfo->surgeon_name);
$Confirm_patientHeaderSurgeon_id = stripslashes($Confirm_patientHeaderInfo->surgeonId);
$Confirm_patientHeaderAnesthesiologist_name = stripslashes($Confirm_patientHeaderInfo->anesthesiologist_name);
if(strlen($Confirm_patientHeaderAnesthesiologist_name)<=17){ $anesHeaderNowrap = "nowrap"; } else { $anesHeaderNowrap = "";}

$Confirm_patientHeaderPrimProcIsMisc	= $Confirm_patientHeaderInfo->prim_proc_is_misc;
$Confirm_patientHeaderSiteTemp = $Confirm_patientHeaderInfo->site;
	// APPLYING NUMBERS TO PATIENT SITE
		if($Confirm_patientHeaderSiteTemp == 1) {
			$Confirm_patientHeaderSite = "Left Eye";  //OS
		}else if($Confirm_patientHeaderSiteTemp == 2) {
			$Confirm_patientHeaderSite = "Right Eye";  //OD
		}else if($Confirm_patientHeaderSiteTemp == 3) {
			$Confirm_patientHeaderSite = "Both Eye";  //OU
		}else if($Confirm_patientHeaderSiteTemp == 4) {
			$Confirm_patientHeaderSite = "Left Upper Lid";
		}else if($Confirm_patientHeaderSiteTemp == 5) {
			$Confirm_patientHeaderSite = "Left Lower Lid";
		}else if($Confirm_patientHeaderSiteTemp == 6) {
			$Confirm_patientHeaderSite = "Right Upper Lid";
		}else if($Confirm_patientHeaderSiteTemp == 7) {
			$Confirm_patientHeaderSite = "Right Lower Lid";
		}else if($Confirm_patientHeaderSiteTemp == 8) {
			$Confirm_patientHeaderSite = "Bilateral Upper Lid";
		}else if($Confirm_patientHeaderSiteTemp == 9) {
			$Confirm_patientHeaderSite = "Bilateral Lower Lid";
		}
	// END APPLYING NUMBERS TO PATIENT SITE

//$Confirm_patientHeaderSite = $Confirm_patientHeaderInfo->site_description;
$Confirm_patientHeaderPrimProc = stripslashes($Confirm_patientHeaderInfo->patient_primary_procedure);
$primProcFullNameForDiv = $Confirm_patientHeaderPrimProc;
$primProcId = $Confirm_patientHeaderInfo->patient_primary_procedure_id;
if(strlen($Confirm_patientHeaderPrimProc)>17){ $Confirm_patientHeaderPrimProc = substr($Confirm_patientHeaderPrimProc,0,17)."... ";}

$Confirm_patientHeaderSecProc = stripslashes($Confirm_patientHeaderInfo->patient_secondary_procedure);
$secProcFullNameForDiv = $Confirm_patientHeaderSecProc;
$secProcId = $Confirm_patientHeaderInfo->patient_secondary_procedure_id;
if(strlen($Confirm_patientHeaderSecProc)>16){ $Confirm_patientHeaderSecProc = substr($Confirm_patientHeaderSecProc,0,16)."... ";}

$Confirm_patientHeaderAssist_by_translator = $Confirm_patientHeaderInfo->assist_by_translator;

//check whether procedure is laser procedure or not
	$str_procedure = "SELECT * FROM procedures WHERE name = '".$primProcFullNameForDiv."'";
	$qry_procedure = imw_query($str_procedure);
	$fetchRows_procedure = imw_fetch_array($qry_procedure);
	$patient_consent_categoryID = $fetchRows_procedure['catId'];
	$patient_primary_procedure_id	=	$fetchRows_procedure['procedureId'];
	
	// Check Whether Procedure is Injection Procedure
	if($patient_consent_categoryID <> '2')
	{
		if($Confirm_patientHeaderPrimProcIsMisc == '')
		{
			//$chkprocedurecatDetails = $objManageData->getRowRecord('procedurescategory', 'proceduresCategoryId', $patient_consent_categoryID);
			$Confirm_patientHeaderPrimProcIsMisc	=	$objManageData->verifyProcIsInjMisc($patient_primary_procedure_id);
			//($chkprocedurecatDetails->isMisc) ?	'true'	:	'';
		}
		
	}else
	{
		$Confirm_patientHeaderPrimProcIsMisc	=	'';
	}
	// Check Whether Procedure is Injection Procedure

	
	if($patient_consent_categoryID!=2)
	{
		if($Confirm_patientHeaderPrimProcIsMisc)
		{
			$Confirm_patientHeaderAnesthesiologist_name_ad="N/A";	
		}
		else
		{
			$Confirm_patientHeaderAnesthesiologist_name_ad=$Confirm_patientHeaderAnesthesiologist_name;
		}
	}
	else
	{
		$Confirm_patientHeaderAnesthesiologist_name_ad="N/A";
	}
	
//end check whether procedure is laser procedure or not




$query_rsNotes = "SELECT txtNote FROM tblprogress_report, users WHERE tblprogress_report.confirmation_id = '".$_REQUEST["pConfId"]."' AND users.usersId = tblprogress_report.usersId ";

$rsNotes = imw_query($query_rsNotes, $link) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
$a='';
$b='#336699';
if ($totalRows_rsNotes == 0) { $a="#DFF4FF"; $srcImage = "images/progress_notes.gif"; } else { $a="red"; $b="white"; $srcImage = "images/progress_notes_hover.gif"; } 
//GET BASE LINE VITAL SIGN FROM PREOP NURSING RECORD
	if($patient_consent_categoryID!=2)
	{
		if($Confirm_patientHeaderPrimProcIsMisc)
		{
			$vitalSignBp_NursingHeader = "";
			$vitalSignP_NursingHeader = "";
			$vitalSignR_NursingHeader = "";
			$vitalSignO2SAT_NursingHeader = "";
			$vitalSignTemp_NursingHeader = "";
			$vitalSignHeight_NursingHeader = "";
			$vitalSignWeight_NursingHeader = "";
			$vitalSignBmi_NursingHeader	=	"";
			$vitalSignBgl_NursingHeader	=	"";
			
			$selectVitalSignCopyHeaderQry = "SELECT * FROM `injection` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
			$selectVitalSignCopyHeaderRes = imw_query($selectVitalSignCopyHeaderQry) or die($selectVitalSignCopyHeaderQry.imw_error());
			$selectVitalSignCopyHeaderNumRow = imw_num_rows($selectVitalSignCopyHeaderRes);
			if($selectVitalSignCopyHeaderNumRow>0) {
				$selectVitalSignCopyHeaderRow 	= imw_fetch_array($selectVitalSignCopyHeaderRes);
				$vitalSignBp_NursingHeader 			= $selectVitalSignCopyHeaderRow["preVitalBp"];
				$vitalSignP_NursingHeader 			= $selectVitalSignCopyHeaderRow["preVitalPulse"];
				$vitalSignR_NursingHeader 			= $selectVitalSignCopyHeaderRow["preVitalResp"];
				$vitalSignO2SAT_NursingHeader 	= $selectVitalSignCopyHeaderRow["preVitalSpo"];
				$vitalSignTemp_NursingHeader 		= 'N/A';
				$vitalSignHeight_NursingHeader	= "N/A";
				$vitalSignWeight_NursingHeader	= "N/A";
				$vitalSignBmi_NursingHeader			= "N/A";
				$vitalSignBgl_NursingHeader			= "N/A";
				
			}
		}
		else
		{
			$selectPreOpNursingHeaderQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
			$selectPreOpNursingHeaderRes = imw_query($selectPreOpNursingHeaderQry) or die(imw_error());
			$selectPreOpNursingHeaderNumRow = imw_num_rows($selectPreOpNursingHeaderRes);
				
			$vitalSignBp_NursingHeader = "";
			$vitalSignP_NursingHeader = "";
			$vitalSignR_NursingHeader = "";
			$vitalSignO2SAT_NursingHeader = "";
			$vitalSignTemp_NursingHeader = "";
			$vitalSignHeight_NursingHeader = "";
			$vitalSignWeight_NursingHeader = "";
			$vitalSignBmi_NursingHeader	=	"";
			$vitalSignBgl_NursingHeader	=	"";
			if($selectPreOpNursingHeaderNumRow>0) {
			$selectPreOpNursingHeaderRow = imw_fetch_array($selectPreOpNursingHeaderRes);
			$preopnursing_vitalsign_id 				= $selectPreOpNursingHeaderRow["preopnursing_vitalsign_id"];
			$vitalSignHeight_NursingHeader		=	$selectPreOpNursingHeaderRow["patientHeight"]	;
			$vitalSignHeight_NursingHeader		.=	!empty($vitalSignHeight_NursingHeader)	?	'"' : '';
			$vitalSignWeight_NursingHeader	=	$selectPreOpNursingHeaderRow["patientWeight"]	;
			$vitalSignWeight_NursingHeader	.=	!empty($vitalSignWeight_NursingHeader)	?	' lbs' : '';
			$vitalSignBmi_NursingHeader	=	$selectPreOpNursingHeaderRow["patientBmi"]	;
			$vitalSignBgl_NursingHeader	=	trim($selectPreOpNursingHeaderRow["NA"]) ? "N/A" : trim(stripslashes($selectPreOpNursingHeaderRow["bsValue"]));
			
			
			if($preopnursing_vitalsign_id) { 
				$ViewPreopNurseVitalHeaderSignQry = "select * from `preopnursing_vitalsign_tbl` where  vitalsign_id = '".$preopnursing_vitalsign_id."'";
				$ViewPreopNurseVitalHeaderSignRes = imw_query($ViewPreopNurseVitalHeaderSignQry) or die(imw_error()); 
				$ViewPreopNurseVitalHeaderSignNumRow = imw_num_rows($ViewPreopNurseVitalHeaderSignRes);
				if($ViewPreopNurseVitalHeaderSignNumRow>0) {
					$ViewPreopNurseVitalHeaderSignRow = imw_fetch_array($ViewPreopNurseVitalHeaderSignRes);
				
					$vitalSignBp_NursingHeader = $ViewPreopNurseVitalHeaderSignRow["vitalSignBp"];
					$vitalSignP_NursingHeader = $ViewPreopNurseVitalHeaderSignRow["vitalSignP"];
					$vitalSignR_NursingHeader = $ViewPreopNurseVitalHeaderSignRow["vitalSignR"];
					$vitalSignO2SAT_NursingHeader = $ViewPreopNurseVitalHeaderSignRow["vitalSignO2SAT"];
					$vitalSignTemp_NursingHeader = $ViewPreopNurseVitalHeaderSignRow["vitalSignTemp"];
				}
			}
			
			
			/*$vitalSignBp_NursingHeader = $selectPrmeOpNursingHeaderRow["vitalSignBp"];
			$vitalSignP_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignP"];
			$vitalSignR_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignR"];
			$vitalSignO2SAT_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignO2SAT"];
			$vitalSignTemp_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignTemp"];
			*/
			if($vitalSignTemp_NursingHeader<>"") {
				$vitalSignTemp_NursingHeader = $vitalSignTemp_NursingHeader;
			}
			
			
			
		}
		}
	}
	else{
		$selectLaserProedureHeaderQry = "SELECT * FROM `laser_procedure_patient_table` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectLaserProedureHeaderRes = imw_query($selectLaserProedureHeaderQry) or die(imw_error());
		$selectLaserProedureHeaderNumRow = imw_num_rows($selectLaserProedureHeaderRes);
	
		$vitalSignBp_NursingHeader = "";
		$vitalSignP_NursingHeader = "";
		$vitalSignR_NursingHeader = "";
		$vitalSignO2SAT_NursingHeader = "";
		$vitalSignTemp_NursingHeader = "";
		$vitalSignHeight_NursingHeader = "";
		$vitalSignWeight_NursingHeader = "";
		if($selectLaserProedureHeaderNumRow>0) {
			$selectlaserProcedureRow = imw_fetch_array($selectLaserProedureHeaderRes);
			$vitalSignBp_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignBP"];
			$vitalSignP_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignP"];
			$vitalSignR_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignR"];
			$vitalSignO2SAT_NursingHeader = 'N/A';
			$vitalSignTemp_NursingHeader = 'N/A';
			$vitalSignHeight_NursingHeader = "N/A";
			$vitalSignWeight_NursingHeader = "N/A";
			$vitalSignBmi_NursingHeader			= "N/A";
			$vitalSignBgl_NursingHeader			= "N/A";
		}
	
	}	
//END GET BASE LINE VITAL SIGN FROM PREOP NURSING RECORD
$sxAlertDiv =  getPtSxAlert($patient_id,$patientHeaderName);
$sxAlertOnClick = $sxAlertStyle = "";
if($sxAlertDiv) {// && $_SESSION['loginUserType']=="Surgeon"
	echo $sxAlertDiv; //show div of sx alert on click on patient name
	$sxAlertOnClick = "sxAlertDivFun('divPtSxAlert','inline-block','".$patient_id."','','','','clicked','disableCancelBtnId');";
	$sxAlertHover = "sxAlertDivFun('divPtSxAlert','inline-block','','','','','','disableCancelBtnId');";
	$sxAlertStyle = "color:#FF0000; cursor:pointer;";
}

// Get Surgeon Practice Match Result if Peer Review Option is on
	$surgeryCenterSettings	=	$objManageData->loadSettings('peer_review');
	$surgeryCenterPeerReview=	$surgeryCenterSettings['peer_review'];
	$practiceNameMatch	=	'';
	if($surgeryCenterPeerReview == 'Y' && $_SESSION['loginUserType'] == 'Surgeon')
	{
		$practiceNameMatch	=	$objManageData->getPracMatchUserId($_SESSION["loginUserId"],$Confirm_patientHeaderSurgeon_id);
	}
	
?>

	<div class="all_content1_slider" id="mainHeader" style="">
    	
       	
         		
        <div class="head_scheduler new_head_slider">
            
            	<div class="loggedin-Info  "> 
        
        			<label class="">Logged in </label>
                    
                    <label> <strong> <?php echo $userName; ?> </strong> </label>&nbsp;
                    
                    <label class="date-time" id="dt_tm"></label>
                    
        		</div>
        
                <span><?php echo $surgerycenterName;?></span>
            	
        </div>
            
            
        <div class="scheduler_table_Complete">
        	
            	<div class="buttons-sch new_buttonbar" style="z-index:9;  width:auto !important">
                	
                    <span class="btn " style=" padding:1px; ">
                    	<a href="javascript:void(0)" class="btn btn-info" id="pt_ocular_sx_hx_m"><b class="glyphicon "></b> Ocular Surgery</a>
                    </span>
                    <span class="btn <?=($totalRows_rsNotes > 0 ? 'bg-orange' : '' )?> " id="progressNotesBg" style=" padding:1px; ">
                    	<a class="btn btn-info" onClick="MM_openBrWindow('progressnotesPopUp.php?ascId=<?php echo $Confirm_patientHeaderAscID; ?>&amp;pConfId=<?php echo $_REQUEST["pConfId"];?>','Progress','scrollbars=no,width=960,height=650');return false" id="progressNotesBtn" href="javascript:void(0)">Progress Notes <?php echo (($totalRows_rsNotes > 0 ) ? '<span class="badge">'.$totalRows_rsNotes.'</span>' : '') ?> </a>	
                    </span>
                    <span class="btn " style=" padding:1px; ">
                    	<a href="javascript:void(0)" class="btn btn-info" id="patient_form_m"><b class="glyphicon glyphicon-new-window"></b> Patient Forms</a>
                    </span>
                    <span class="btn " style=" padding:1px; ">
                    	<a class="btn btn-default " data-target="" href="javascript:void(0)"> Help  <b class="fa fa-question"></b></a>
                  	</span>
				
                </div>
            	
                <div class="table_head_sch" style="padding:0; height:38px;">
                    
                   		<?PHP include 'common/mainslider.php'; ?> 
                    
                </div>
          
             	
                <div class="slider_inner">
                	
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    	
                        <div class="display_form">
                        	<div class="row">
                            	
                                <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                	<Span class="robo"> Patient Name  </Span>
								</div>
                                
                                <div id="ptNameId" class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left capitalize">
									<Span class="data_i" onmouseover=" <?php echo $sxAlertHover;?>" onclick=" <?php echo $sxAlertOnClick;?>" style=" <?php echo $sxAlertStyle;?>">  <?php echo $patientHeaderName;?> </Span>
                               	</div>
                               
                            </div>   
                        </div>
                    </div>
                     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                   <Span class="robo"> Address  </Span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left capitalize">
                                   	<Span class="data_i hover_tool">
								   		<?PHP echo stripslashes($patientHeaderstreet1);?><br />
									</Span>
                                   	<div class="tooltip_c">
                                            <?PHP echo $addressPatientstreet1;?><br />
                                            <?PHP
												if($addressPatientstreet2)	echo $addressPatientstreet2.'<br />';
												echo stripslashes($addressPatientcity.",".$addressPatientstate." ".$addressPatientzip);
											?>
									</div>
                               </div>
                            </div>   
                        </div>
                    </div>
                     <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                   <Span class="robo"> Site  </Span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left">
                                   <Span class="data_i" style="white-space:nowrap;"><?php echo $Confirm_patientHeaderSite;?></Span>
                               </div>
                            </div>   
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                   <Span class="robo"> A/D  </Span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left">
                                   <Span class="data_i"><?php echo $Confirm_patientHeaderAdvanceDirective;?></Span>
                               </div>
                            </div>   
                        </div>
                    </div>
                    
            </div> <!--  Slider Inner-->
            
            	<div class="slider_inner">
                 
                     <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                   <Span class="robo"> DOB  </Span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left ">
                                   <Span class="data_i"><?php echo $patientHeader_dob;?></Span>
                               </div>
                            </div>   
                        </div>
                    </div>
                     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                   <Span class="robo"> Age  </Span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left">
                                   <Span class="data_i"><?php echo $patientHeader_age;?> years</Span>
                               </div>
                            </div>   
                        </div>
                    </div>
                     <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
								   <Span class="robo"> Tel. </Span>
								   <span class="robo" style="font-size:14px!important;">(H)</span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left">
                                   <Span class="data_i"> <?php echo $patientHeaderhomePhone;?> </Span>
                               </div>
                            </div>   
                        </div>
                    </div>
                     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"> 
                       <div class="display_form">
                            <div class="row popupDummy" id="priProcForm" style="display:none;"> 
                            <div class="form col-lg-9">                                    
                            	<div class="form" style="max-width:240px; width:240px">
                              <select name="priProcedureList" id="priProcedureList" class="form-control selectpicker" data-width="100%" onChange="save('1');">	
                                    <option value="">Select Procedure</option>
                                    <?php
                                        $userProcedureDetails = $objManageData->getArrayRecords('procedures', '1', '1','name','ASC');
                                        if($userProcedureDetails) {
                                            foreach($userProcedureDetails as $procedureVal){
                                                $del_status = $procedureVal->del_status;
												if($primProcId == $procedureVal->procedureId && $procedureVal->name <> $primProcFullNameForDiv)
												{
													echo '<option value="'.$primProcId.'" selected >'.$primProcFullNameForDiv.'</option>';
												}
												if($del_status <> "yes" || $primProcId == $procedureVal->procedureId) {
                                    ?>
                                                    <option value="<?php echo $procedureVal->procedureId; ?>" <?php if($primProcFullNameForDiv == $procedureVal->name) echo "SELECTED"; ?>><?php echo $procedureVal->name; ?></option>
                                    <?php
												}
                                            }
                                        }	
                                    ?>
                            </select>
                              	</div>
                            </div>
                                 <div class="form  col-lg-1"></div>
                                 <div class="form  col-lg-2">
                                <a id='cancelDateTimeBtn' class='btn btn-danger' href='javascript:void(0)' onClick="closeIt('1')">
                                <b class='fa fa-close'></b></a></div>
                            </div>
                            <div class="row" id="priProcText">                                     
                            	<div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                	<Span class="robo"> Pri. Procedure  </Span>
                               	</div>
                               	<div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left ">
                                   	<span class="data_i hover_tool col-lg-10 col-md-10 col-sm-10 col-xs-10 padding_0">
                  				        <?php echo $Confirm_patientHeaderPrimProc;?>
                                    </span>
                  					<div class="tooltip_c"><?php echo $primProcFullNameForDiv;?></div>      
                                  	<span class="col-lg-2 col-md-2 col-sm-2 col-xs-2 padding_0">
                                    	<?php
                                        	if($Confirm_patientHeaderAscID && !$finalized_status) {
												echo' <span class="fa fa-edit" onClick="edit(\'1\')"></span>';
											}
										?>
                                  	</span>   
                              	</div>
                            </div>   
                        </div>
                    </div>
            
            </div> <!--  Slider Inner-->

			<?php
            //SET ALLERGIES VALUE
                $Confirm_NKDA = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $_REQUEST["pConfId"]);
				$Confirm_patientHeaderAllergiesNKDA_status = $Confirm_NKDA->allergiesNKDA_status;
				$patient_allergies_tblQry = "SELECT `allergy_name` FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '".$_REQUEST["pConfId"]."'";
                $patient_allergies_tblRes = imw_query($patient_allergies_tblQry) or die(imw_error());
                $patient_allergies_tblNumRow = imw_num_rows($patient_allergies_tblRes);
                if($patient_allergies_tblNumRow>0) {
                    //$allergiesValue = '<img src="images/Interface_red_image003.gif" width="17" height="15" align="middle" onMouseOver="showAllergiesPopFn(550, 95);" onMouseOut="closeAllergiesPopFn();">';
                    $patient_allergies_tblRow = imw_fetch_array($patient_allergies_tblRes);
                    $patientHeaderAllergyName = $patient_allergies_tblRow['allergy_name'];
                    if(trim(strtoupper($patientHeaderAllergyName))=='NKA' && $patient_allergies_tblNumRow==1) {
                        $allergiesValue = 'NKA';
                    }else {
                        $allergiesValue = '<img src="images/Interface_red_image003.gif" style="width:17px; height:15px;vertical-align:middle;" onClick="showAllergiesPopUpFn('.$_REQUEST["pConfId"].');">';
                    }
                }else if($Confirm_patientHeaderAllergiesNKDA_status=="Yes") {
                    $allergiesValue = 'NKA';
                }else {
                    $allergiesValue = '';
                }
            //END SET ALLERGIES VALUE
            
            ?>            
            	<div class="slider_inner">
                 
                     <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                   <Span class="robo"> Surgery Date  </Span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left">
                                   <Span class="data_i"><?php echo $Confirm_patientHeaderDos;?> </Span>
                               </div>
                            </div>   
                        </div>
                    </div>
                      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                   <Span class="robo"> Sex </Span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left">
                                   <Span class="data_i"><?php echo $patientHeadersex;?></Span>
                               </div>
                            </div>   
                        </div>
                    </div>
                     <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> 
                       <div class="display_form">
                            <div class="row">                                     
								<div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
								   <Span class="robo"> Tel. </Span>
								   <span class="robo" style="font-size:14px!important;">(W)</span>
                               	</div>
                               	<div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left ">
                                	<Span class="data_i"><?php echo $patientHeaderworkPhone;?></Span>
                               	</div>
                            </div>   
                        </div>
                    </div>
                     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"> 
                       <div class="display_form">
                            <div class="row popupDummy" id="secProcForm" style="display:none">                                     
                            	<div class="form  col-lg-9">                                    
                            	<div class="form" style="max-width:240px; width:240px">
                              <select name="secProcedureList" id="secProcedureList" class="form-control selectpicker" onChange="save('2');">	
                                    <option value="">Select Procedure</option>
                                    <?php
                                        $userProcedureDetails = $objManageData->getArrayRecords('procedures', '1', '1','name','ASC');
                                        if($userProcedureDetails) {
                                            foreach($userProcedureDetails as $procedureVal){
                                                $del_status = $procedureVal->del_status;
												if($secProcId == $procedureVal->procedureId && $procedureVal->name <> $secProcFullNameForDiv)
												{
													echo '<option value="'.$patient_prim_proc.'$$'.$PrimaryProcedureId.'" selected >'.$secProcFullNameForDiv.'</option>';
												}
												if($del_status <> "yes" || $secProcId == $procedureVal->procedureId) {
                                    ?>
                                                    <option value="<?php echo $procedureVal->procedureId; ?>" <?php if($secProcFullNameForDiv == $procedureVal->name) echo "SELECTED"; ?>><?php echo $procedureVal->name; ?></option>
                                    <?php
												}
                                            }
                                        }	
                                    ?>
                            </select>
                            </div>
                              	</div>
                                 <div class="form  col-lg-1"></div>
                                <div class="form col-lg-2"><!--
                                <a id='updateDateTimeBtn' class='btn btn-success' href='javascript:void(0)' onClick="save('2')">
                                <b class='fa fa-save'></b></a>
                                &nbsp;
                                --><a id='cancelDateTimeBtn' class='btn btn-danger' href='javascript:void(0)' onClick="closeIt('2')">
                                <b class='fa fa-close'></b></a></div>
                            </div>
                            <div class="row" id="secProcText">                                    
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                   <Span class="robo"> Sec. Proc  </Span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7 text-left">
                               		<span class="data_i hover_tool col-lg-10 col-md-10 col-sm-10 col-xs-10 padding_0">
                  				        <?php echo $Confirm_patientHeaderSecProc;?>
                                    </span>
                  					<div class="tooltip_c"><?php echo $secProcFullNameForDiv;?></div>      
                                  	<span class="col-lg-2 col-md-2 col-sm-2 col-xs-2 padding_0">
                                    	<?php
                                        	if($Confirm_patientHeaderAscID && !$finalized_status) {
												echo' <span class="fa fa-edit" onClick="edit(\'2\')"></span>';
											}
										?>
                                  	</span>
                                    
                                    
                                    <!--<Span class="data_i hover_tool"><?php echo $Confirm_patientHeaderSecProc;
                                    if($Confirm_patientHeaderAscID && !$finalized_status)echo' &nbsp;<span class="fa fa-edit" onClick="edit(\'2\')"></span>';?></Span>
                                   	<div class="tooltip_c"><?php echo $secProcFullNameForDiv;?></div>-->
                               </div>
                            </div>   
                        </div>
                    </div> 
            </div> <!--  Slider Inner-->
         
             	<div class="slider_inner">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                   <Span class="robo"> Surgeon </Span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left">
                                   <Span class="data_i"><?php echo $Confirm_patientHeaderSurgeon_name;?></Span>
                               </div>
                            </div>   
                        </div>
                    </div>
                     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                   <Span class="robo"> Anesthesia&nbsp;Provider  </Span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left ">
                                   <Span class="data_i" id="headerAnesNameID"><?php echo $Confirm_patientHeaderAnesthesiologist_name_ad;?>  </Span>
                               </div>
                            </div>   
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
							  		<div class="row" >
										<div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left ">
											<Span class="robo"> Allergies  </Span>
										</div>
										<div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
											<Span class="data_i" id="allergiesHeaderId"><?php echo $allergiesValue;?></Span>
										</div>
									</div>
								</div>
                               	<div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left ">
								   <div class="row" >
										<div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left ">
											<Span class="robo"> Translator  </Span>
										</div>
										<div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
											<Span class="data_i">
												<input class="checkbox" id="headerAssistID" onClick="javascript:assistChangeFun('headerAssistID','<?php echo $_REQUEST["pConfId"];?>');" name="headerAssist"  type="checkbox" <?php if($Confirm_patientHeaderAssist_by_translator=="yes") { echo "checked"; }?>  value="Yes"  tabindex="7" />
											</Span>
										</div>
									</div>
                               	</div>
                            </div>   
                        </div>
                    </div>
        
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"> 
                       <div class="display_form">
                            <div class="row">                                     
                              <div class="col-md-5 col-lg-5 col-xs-12 col-sm-5 text-left">
                                   <Span class="robo"> ASC  </Span>
                               </div>
                               <div class="col-md-7 col-lg-7 col-xs-12 col-sm-7  text-left ">
                                   <Span class="data_i"><?php echo $Confirm_patientHeaderAscID;?></Span>
                               </div>
                            </div>   
                        </div>
                    </div>
             </div> <!--  Slider Inner-->
             
             	<div class="b_vital_signs">
             
                	<table class="table-bordered table-condensed ">
                       
                        <tr >
                            <th  style="background-color:#FFF; white-space:nowrap;">Base Line Vital Signs</th>
                            <td class="text-center">B/P<span class="blue_txtb" id="header_BP" style="padding-left:5px;"><?php echo $vitalSignBp_NursingHeader;?></span></td>
                            <td class="text-center">P<span class="blue_txtb" id="header_P" style="padding-left:5px;"><?php echo $vitalSignP_NursingHeader;?></span></td>
                            <td class="text-center">R<span class="blue_txtb" id="header_R" style="padding-left:5px;"><?php echo $vitalSignR_NursingHeader;?></span></td>
                            <td class="text-center">O<sub>2</sub>SAT<span class="blue_txtb" style="padding-left:5px;" id="header_O2SAT"><?php echo $vitalSignO2SAT_NursingHeader;?></span></td>
                            <td class="text-center">Temp<span class="blue_txtb" id="header_Temp"  style="padding-left:5px;"><?php echo $vitalSignTemp_NursingHeader;?></span></td>
                            <td class="text-center">Height<span class="blue_txtb" id="header_Height"  style="padding-left:5px;"><?php echo $vitalSignHeight_NursingHeader;?></span></td>
                            <td class="text-center">Weight<span class="blue_txtb" id="header_Weight"  style="padding-left:5px;"><?php echo $vitalSignWeight_NursingHeader;?></span></td>
                            <td class="text-center">BMI<span class="blue_txtb" id="header_Bmi"  style="padding-left:5px;"><?php echo $vitalSignBmi_NursingHeader;?></span></td>
                            <td class="text-center">BGL<span class="blue_txtb" id="header_Bgl"  style="padding-left:5px;"><?php echo $vitalSignBgl_NursingHeader;?></span></td>
						</tr>
				</table>
             </div>
        </div>
    </div>