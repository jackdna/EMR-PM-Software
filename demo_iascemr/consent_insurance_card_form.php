<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
$tablename = "insurance_consent_form";
//include("common/linkfile.php");
include("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

extract($_GET);
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
//CODE TO DISABLE SLIDER LINK AT SINGLE CLICK  
	$patient_id = $_REQUEST["patient_id"];
	$ascId = $_REQUEST["ascId"];
	$pConfId = $_REQUEST["pConfId"];


	$thisId = $_REQUEST["thisId"];
	if($innerKey=="") {
		$innerKey = $_REQUEST["innerKey"];
	}
	if($preColor=="") {
		$preColor = $_REQUEST["preColor"];
	}	

	
	$fieldName = "insurance_card_form";
	$pageName = "consent_insurance_card_form.php?patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";
	if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON
		$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";
	}
	
	include("left_link_hide.php");
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 

//GET PATIENT DETAIL

	$Insur_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$_REQUEST['patient_id']."'";
	$Insur_patientName_tblRes = imw_query($Insur_patientName_tblQry) or die(imw_error());
	$Insur_patientName_tblRow = imw_fetch_array($Insur_patientName_tblRes);
	$Insur_patientName = $Insur_patientName_tblRow["patient_lname"].", ".$Insur_patientName_tblRow["patient_fname"]." ".$Insur_patientName_tblRow["patient_mname"];

	$Insur_patientNameDobTemp = $Insur_patientName_tblRow["date_of_birth"];
		$Insur_patientNameDob_split = explode("-",$Insur_patientNameDobTemp);
		$Insur_patientNameDob = $Insur_patientNameDob_split[1]."-".$Insur_patientNameDob_split[2]."-".$Insur_patientNameDob_split[0];
	
	$Insur_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$Insur_patientConfirm_tblRes = imw_query($Insur_patientConfirm_tblQry) or die(imw_error());
	$Insur_patientConfirm_tblRow = imw_fetch_array($Insur_patientConfirm_tblRes);
	$Insur_patientConfirmDosTemp = $Insur_patientConfirm_tblRow["dos"];
	$finalizeStatus = $Insur_patientConfirm_tblRow["finalize_status"];

	$Insur_patientConfirmDos_split = explode("-",$Insur_patientConfirmDosTemp);
	$Insur_patientConfirmDos = $Insur_patientConfirmDos_split[1]."-".$Insur_patientConfirmDos_split[2]."-".$Insur_patientConfirmDos_split[0];
	$Insur_patientConfirmSurgeon = $Insur_patientConfirm_tblRow["surgeon_name"];
	$Insur_patientConfirmSiteTemp = $Insur_patientConfirm_tblRow["site"];
	// APPLYING NUMBERS TO PATIENT SITE
		if($Insur_patientConfirmSiteTemp == 1) {
			$Insur_patientConfirmSite = "Left Eye";  //OD
		}else if($Insur_patientConfirmSiteTemp == 2) {
			$Insur_patientConfirmSite = "Right Eye";  //OS
		}else if($Insur_patientConfirmSiteTemp == 3) {
			$Insur_patientConfirmSite = "Both Eye";  //OU
		}
	// END APPLYING NUMBERS TO PATIENT SITE
	$Insur_patientConfirmPrimProc = $Insur_patientConfirm_tblRow["patient_primary_procedure"];
	$Insur_patientConfirmSecProc = $Insur_patientConfirm_tblRow["patient_secondary_procedure"];

//END GET PATIENT DETAIL


$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&ascId='.$ascId;

//SAVE RECORD IN DATABASE
if($_POST['SaveRecordForm']=='yes'){

	$text = $_REQUEST['getText'];
	$tablename = "insurance_consent_form";
	//Save_eposts($text,$tablename);
	
	//SET FORM STATUS ACCORDING TO MANDATORY FIELD
		$form_status = "completed";
		if($_POST["consentInsurance_patient_sign"]=="" || $_POST["consentInsurance_patient_sign"]=="255-0-0:;") {
			$form_status = "not completed";
		}
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	$chkConsentInsuranceQry = "select * from `insurance_consent_form` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkConsentInsuranceRes = imw_query($chkConsentInsuranceQry) or die(imw_error()); 
	$chkConsentInsuranceNumRow = imw_num_rows($chkConsentInsuranceRes);
	if($chkConsentInsuranceNumRow>0) {
	  	//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
			$chkFormStatusRow = imw_fetch_array($chkConsentInsuranceRes);
			$chk_form_status = $chkFormStatusRow['form_status'];
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
		
		$SaveConsentInsuranceQry = "update `insurance_consent_form` set 
									insurance_consent_data = '".addslashes($_POST["insurance_consent_data"])."',
									insurance_consent_sign = '".$_POST["consentInsurance_patient_sign"]."', 
									form_status ='".$form_status."' 
									WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
	}else {
		$SaveConsentInsuranceQry = "insert into `insurance_consent_form` set 
									insurance_consent_data = '".addslashes($_POST["insurance_consent_data"])."',
									insurance_consent_sign = '".$_POST["consentInsurance_patient_sign"]."', 
									form_status ='".$form_status."', 
									confirmation_id='".$_REQUEST["pConfId"]."'";
	}
	//echo $SaveConsentInsuranceQry;
	$SaveConsentInsuranceRes = imw_query($SaveConsentInsuranceQry) or die(imw_error());
	
	//SAVE ENTRY IN chartnotes_change_audit_tbl 
		
		$chkAuditChartNotesQry = "select * from `chartnotes_change_audit_tbl` where 
									user_id='".$_SESSION['loginUserId']."' AND
									patient_id='".$_REQUEST["patient_id"]."' AND
									confirmation_id='".$_REQUEST["pConfId"]."' AND
									form_name='".$fieldName."' AND
									status = 'created'";
									
		$chkAuditChartNotesRes = imw_query($chkAuditChartNotesQry) or die(imw_error());	
		$chkAuditChartNotesNumRow = imw_num_rows($chkAuditChartNotesRes);	
		if($chkAuditChartNotesNumRow>0) {
			$SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
										user_id='".$_SESSION['loginUserId']."',
										patient_id='".$_REQUEST["patient_id"]."',
										confirmation_id='".$_REQUEST["pConfId"]."',
										form_name='$fieldName',
										status='modified',
										action_date_time='".date("Y-m-d H:i:s")."'";
		}else {
			$SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
										user_id='".$_SESSION['loginUserId']."',
										patient_id='".$_REQUEST["patient_id"]."',
										confirmation_id='".$_REQUEST["pConfId"]."',
										form_name='$fieldName',
										status='created',
										action_date_time='".date("Y-m-d H:i:s")."'";
		}					
		$SaveAuditChartNotesRes = imw_query($SaveAuditChartNotesQry) or die(imw_error());
	//END SAVE ENTRY IN chartnotes_change_audit_tbl
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		if($form_status == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.frames[0].location='blankform.php?frameHref=consent_insurance_card_form.php&SaveForm_alert=true$saveLink';</script>";
		}else if($form_status=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.frames[0].location='blankform.php?frameHref=consent_insurance_card_form.php&SaveForm_alert=true$saveLink';</script>";
		}
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
	
	/* echo "<script> location.href='consent_insurance_card_form.php?formStatus=filled$saveLink';</script>";*/
}
//END SAVE RECORD IN DATABASE

//VIEW RECORD FROM DATABASE
	$ViewConsentInsuranceQry = "select * from `insurance_consent_form` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$ViewConsentInsuranceRes = imw_query($ViewConsentInsuranceQry) or die(imw_error()); 
	$ViewConsentInsuranceNumRow = imw_num_rows($ViewConsentInsuranceRes);
	$ViewConsentInsuranceRow = imw_fetch_array($ViewConsentInsuranceRes); 
	
	$consentInsurance_patient_sign = $ViewConsentInsuranceRow["insurance_consent_sign"];
	$insurance_consent_data = stripslashes($ViewConsentInsuranceRow["insurance_consent_data"]);

	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE	
		if(trim($insurance_consent_data)=="") {
			$ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = 4";
			$ViewConsentTemplateRes = imw_query($ViewConsentTemplateQry) or die(imw_error()); 
			$ViewConsentTemplateNumRow = imw_num_rows($ViewConsentTemplateRes);
			$ViewConsentTemplateRow = imw_fetch_array($ViewConsentTemplateRes); 
				
			$insurance_consent_data = stripslashes($ViewConsentTemplateRow["consent_data"]);
		}
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE
	
	//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
		$insurance_consent_data= str_replace("{Patient first Name}","<b>".$Insur_patientName_tblRow["patient_fname"]."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Middle Initial}","<b>".$Insur_patientName_tblRow["patient_mname"]."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Last Name}","<b>".$Insur_patientName_tblRow["patient_lname"]."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{DOB}","<b>".$Insur_patientNameDob."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{DOS}","<b>".$Insur_patientConfirmDos."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Surgeon Name}","<b>".$Insur_patientConfirmSurgeon."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Site}","<b>".$Insur_patientConfirmSite."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Procedure}","<b>".$Insur_patientConfirmPrimProc."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Secondary Procedure}","<b>".$Insur_patientConfirmSecProc."</b>",$insurance_consent_data);
	//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			

//END VIEW RECORD FROM DATABASE

?>
<script src="js/epost.js"></script>
<script>
//Applet
function get_App_Coords(objElem){
	var coords,appName;
	var objElemSign = document.frm_consent_ins_card.consentInsurance_patient_sign;
	appName = objElem.name;
	coords = getCoords(appName);
	objElemSign.value = refineCoords(coords);
}
function refineCoords(coords){	
	isEmpty = coords.lastIndexOf(";");	
	if(isEmpty == -1){
		coords += ";";	
	}else{
		coords = coords.substr(0,isEmpty+1);		
	}		
	return coords;	
}
function getCoords(){		
	var coords = document.applets["app_ConsentInsuranceSignature"].getSign();
	return coords;
}
function getclear_os(){
	document.applets["app_ConsentInsuranceSignature"].clearIt();
	changeColorThis(255,0,0);
	document.applets["app_ConsentInsuranceSignature"].onmouseout();
}
function changeColorThis(r,g,b){				
	document.applets['app_ConsentInsuranceSignature'].setDrawColor(r,g,b);								
}
//Applet
top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');
</script>
<body onClick="document.getElementById('divSaveAlert').style.display = 'none'; closeEpost(); return top.frames[0].main_frmInner.hideSliders();">
<div id="post" style="display:none;"></div>
<?php 
	$query_rsNotes = "SELECT * FROM eposted WHERE table_name = 'insurance_consent_form' AND patient_conf_id = '$pConfId' ";
	$rsNotes =imw_query($query_rsNotes);
	$totalRows_rsNotes =imw_num_rows($rsNotes);
?> 

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
	 <form name="frm_consent_ins_card" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="consent_insurance_card_form.php?saveRecord=true&SaveForm_alert=true<?php echo $saveLink;?>">
		<input type="hidden" name="SaveRecordForm" value="yes">	
		<input type="hidden" name="formIdentity" value="">	
		<input type="hidden" name="getText" id="getText">
		<input type="hidden" name="go_pageval" value="<?php echo $tablename;?>"/>
		<input type="hidden" name="frmAction" value="consent_insurance_card_form.php">		
			<tr>
				<td><img src="images/tpixel.gif" width="1" height="2"></td>
			</tr>
			<tr>
				<td  valign="top" align="center">
					<table width="30%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="3" align="right"><img src="images/left.gif" width="3" height="24"></td>
							<td valign="middle" bgcolor="#BCD2B0" align="center" class="text_10b" >Insurance Card</td>
							<td align="left" valign="top" width="3"><img src="images/right.gif" width="3" height="24"></td>
							<td>&nbsp;</td><td nowrap id="epostDelId"> <?php while($row = imw_fetch_array($rsNotes)) { if($totalRows_rsNotes > 0) { ?> <img src="images/sticky_note.gif" onMouseOver="showEpost('<?php echo $row['epost_id'];?>')">  <?php } } ?></td>
						</tr>
				  </table>
				</td>
			</tr>
			<tr>
			   <td><img src="images/tpixel.gif" width="4" height="1"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:350px; padding-top:25px;">
					<div id="divSaveAlert" style="position:absolute;left:350; top:200; display:none;">
						<?php 
							$bgCol = '#BCD2B0';
							$borderCol = '#BCD2B0';
							//$leftSrc = 'images/leftblu.gif';
							//$rightSrc = 'images/rightblue.gif';
							include('saveDivPopUp.php'); 
						?>
					</div>
				</td>
			</tr>
			<tr  valign="top" height="300">
			  <td bgcolor="#ECF1EA" align="left">
				<table width="99%"  align="center" border="0" cellpadding="0" cellspacing="0" class="all_border">
					<tr height="22" bgcolor="#D1E0C9">
						<td width="1%"></td>
						<td colspan="2" class="text_10b">Patient Consent</td>
					</tr>
					<tr height="10" bgcolor="#FFFFFF">
						<td width="1%"></td>
						<td colspan="2" class="text_10b"></td>
					</tr>
					<tr height="300" bgcolor="#FFFFFF">
						<td width="1%"></td>
						<td height="22" width="95%" class="text_10" valign="top" align="left">
							<?php
								if($img_content){
									?>
									<img style="cursor:hand;" src="admin/logoImg.php?from=Consent&scan_upload_id=<?php echo $scan_upload_id; ?>">
									<?php
								}else{
									?>
									<!-- <input type="hidden" name="insurance_consent_data" value="<?php //echo $insurance_consent_data;?>"> -->
									<?php echo $insurance_consent_data;?>
									<textarea name="insurance_consent_data" style=" height:1px;visibility:hidden; "><?php echo $insurance_consent_data;?></textarea>
									<?php
								}
								?>
						</td>
						<td width="2%"></td>
					</tr>
					<tr height="22" bgcolor="#FFFFFF">
						<td width="1%"></td>
						<td colspan="2" class="text_10b"></td>
					</tr>
				</table>
			 </td>	
		   </tr> 
		   <tr>
				<td> <img src="images/tpixel.gif" width="1" height="3"></td>
		   </tr>
		   <tr>
				<td width="99%"  valign="top" align="center">
					<table cellpadding="0"  cellspacing="0" rules="none" border="1" bgcolor="#F1F4F0" bordercolor="#D1E0C9"   width="99%">
						<tr bgcolor="#FFFFFF" height="6"><td colspan="9"></td></tr>
						<tr height="80">
							<td width="41"></td>
							<td class="text_10b" nowrap><?php echo $Insur_patientName.' : ';?></td>
							<td width="780" colspan="1">
								<!-- <input type="text"  class="all_border" style="height:50px; width:200px; " disabled> -->
								<input type="hidden" name="consentInsurance_patient_sign" value="<?php echo $consentInsurance_patient_sign;?>">
								<applet name="app_ConsentInsuranceSignature" code="MyCanvasColored.class" 
									archive="DrawApplet.jar" width="250" height="65" 
									codebase="common/applet/" onmouseout="get_App_Coords(this)">
									<param name="bgImage" value="images/white.jpg">
									<param name="strpixls" value="<?php echo $consentInsurance_patient_sign;?>">
									<param name="mode" value="edit">
								</applet>
								<img src="images/eraser.gif" onClick="return getclear_os();">
							</td>
						    <!-- <td width="200" align="center" nowrap="nowrap" class="text_10">Signature on File </td>
						    <td width="29" class="text_10">Yes</td>
						    <td width="44"  height="29" >
							  <table cellpadding="0" cellspacing="0" border="0">
											
									<tr>
										<td width="24" height="24" onClick="javascript:checkSingle('chbx_patient_sign_yes','chbx_patient_sign')">
											<input type="checkbox" class="field checkbox" name="chbx_patient_sign" value="Yes"  id="chbx_patient_sign_yes"/>
										</td>
									</tr>
							   </table>
							</td>
							 <td width="27" class="text_10">No</td>
							 <td width="44" height="24" align="center" valign="middle">
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td width="44" height="24" onClick="javascript:checkSingle('chbx_patient_sign_no','chbx_patient_sign')">
											<input type="checkbox" class="field checkbox" name="chbx_patient_sign" value="No"  id="chbx_patient_sign_no"/>
										</td>
									</tr>
								</table>
							</td> -->
							<td width="40"></td>
						</tr> 
						<tr bgcolor="#FFFFFF" height="6"><td colspan="9"></td></tr>
				 </table>
			 </td>
		 </tr>
		 <tr>
				<td> <img src="images/tpixel.gif" width="1" height="5"></td>
		 </tr>
		 
	</form>
	<!-- WHEN CLICK ON CANCEL BUTTON -->
	<!-- <form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="mainpage.php?cancelRecord=true<?php //echo $saveLink;?>" target="_top"> -->
	<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="consent_insurance_card_form.php?cancelRecord=true<?php echo $saveLink;?>" target="_self">
	</form>
	<!-- END WHEN CLICK ON CANCEL BUTTON -->
</table>
</body>
<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "consent_insurance_card_form.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM

if($finalizeStatus!='true'){
	?>
	<script>
		top.frames[0].setPNotesHeight();
		top.frames[0].displayMainFooter();	
	</script>
	<?php
}else{
	?>
	<script>
		top.frames[0].setPNotesHeight();		
		top.document.getElementById('footer_button_id').style.display = 'none';
	</script>
	<?php
}

if($SaveForm_alert == 'true'){
	?>
	<script>
		document.getElementById('divSaveAlert').style.display = 'block';
	</script>
	<?php
}

?>