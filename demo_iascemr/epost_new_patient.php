<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
require_once('common/conDb.php');
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
 
$patient_id 	= $_REQUEST['patient_id'];
$pConfirmId 	= $_REQUEST['pConfirmId'];
$stub_id 		= $_REQUEST['stub_id'];
$spanPtAlertId 	= $_REQUEST['spanPtAlertId'];
$andstubIdQry 	= "";
if(!$pConfirmId) {
	$pConfirmId = '0';
	$andstubIdQry = " AND stub_id = '".$_REQUEST["stub_id"]."' ";
}
$id_del = $_REQUEST['delid'];  
  if($id_del != '')
	{
		$qry_delete = "delete from eposted where epost_id = $id_del";
		$del_Notes = imw_query($qry_delete);
	}
	


function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "dated":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
//print_r($_REQUEST);
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$table_name = $_POST['table_name'];
$table_name_temp = substr($table_name,0,21);
if($table_name_temp=='consent_multiple_form') {
	$table_name_explode = explode('-',$table_name);
	$table_name = $table_name_explode[0];
	$consentTemplateId = $table_name_explode[1];
	//gurleen
	$consentAutoID = $table_name_explode[2];
	//***gurleenend
	
}
if(!$consentTemplateId) {
	$consentTemplateId = '0';
}
if(!$consentAutoID) {
	//gurleen
	$consentAutoID = '0'; 
	//gurleen
}
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1") && $_REQUEST['editid']=='') {
//GURLEEN					   
 $insertSQL = sprintf("INSERT INTO eposted (epost_data,dtdate,T_time,table_name,patient_conf_id,patient_id,consent_template_id,consentAutoIncId,stub_id) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
					   GetSQLValueString($_POST['epost_data'], "text"),
					   GetSQLValueString($_POST['dtdate'], "dated"),
                       GetSQLValueString($_POST['T_time'], "text"),
					   GetSQLValueString($table_name, "text"),
					   GetSQLValueString($_POST['patient_conf_id'], "int"),
					   GetSQLValueString($_POST['patient_id'], "int"),
					   GetSQLValueString($consentTemplateId, "int"),
					   GetSQLValueString($consentAutoID, "int"),
					   GetSQLValueString($_POST['stub_id'], "int"));
//****GURLEEN
					   

  imw_select_db($database_connDb);
  
  $Result1 = imw_query($insertSQL, $link) or die(imw_error());
	
  }elseif($_REQUEST['editid']!='' && $_REQUEST['submit_click']<>""){
  
   $updateSQL = "update eposted set 
   					epost_data = '".$_POST['epost_data']."', 
					dtdate = '".$_POST['dtdate']."',
					T_time = '".$_POST['T_time']."',
					table_name = '".$table_name."',
					patient_conf_id = '".$_POST['patient_conf_id']."',
					patient_id = '".$_POST['patient_id']."',
					stub_id = '".$_POST['stub_id']."',
					consent_template_id = '".$consentTemplateId."'
				where epost_id ='".$_REQUEST['editid']."'";
  imw_query($updateSQL); 
  $_REQUEST['editid']=""; 
}

//GET MULTIPLE CONSENT FORMS
$consentFormTemplateSelectQry = "select * from `consent_forms_template` order by consent_id";
$consentFormTemplateSelectRes = imw_query($consentFormTemplateSelectQry) or die(imw_error()); 
$consentFormTemplateSelectNumRow = imw_num_rows($consentFormTemplateSelectRes);
$consentFormAliasArr = array();
if($consentFormTemplateSelectNumRow>0) {
	while($consentFormTemplateSelectRow = imw_fetch_array($consentFormTemplateSelectRes)) {
		
		$consentFormTemplateSelectConsentAlias = $consentFormTemplateSelectRow['consent_alias'];	
		$consentFormTemplateDeleteStatus = $consentFormTemplateSelectRow['consent_delete_status'];
		
		//DO NOT DISPLAY CONSENT FORM FOR NEW SURGERY IN LEFT SLIDER
		$consentFormTemplateDeleteStatus;
		if($consentFormTemplateDeleteStatus=='true') {
			$consentFormTemplateSelectConsentAlias='';
		}
		//DO NOT DISPLAY CONSENT FORM FOR NEW SURGERY IN LEFT SLIDER
		
		//$consentFormTemplateSelectConsentId[] = $consentFormTemplateSelectRow['consent_id'];
		$consentFormSelectQry = "select * from `consent_multiple_form` where  confirmation_id = '".$pConfirmId."' AND consent_template_id='".$consentFormTemplateSelectRow['consent_id']."' ";
		$consentFormSelectRes = imw_query($consentFormSelectQry) or die(imw_error()); 
		$consentFormSelectNumRow = imw_num_rows($consentFormSelectRes);
		$consentFormSelectRow = imw_fetch_array($consentFormSelectRes);
		$consentFormSelectConsentAlias = $consentFormSelectRow['surgery_consent_alias'];	
		if(!$consentFormSelectConsentAlias) {
			if($consentFormTemplateSelectConsentAlias!='') {
				$consentFormSelectConsentAlias=$consentFormTemplateSelectConsentAlias;
			}
		}
		if($consentFormSelectConsentAlias!='') {
			$consentFormAliasArr[] = $consentFormSelectConsentAlias;
			$consentFormTemplateSelectConsentId[] = $consentFormTemplateSelectRow['consent_id'];
		}	
	}
	
}

	//CODE TO CHECK FOR SCHEDULED PATIENT (IF TEMPLATE EXISTS IN ADMIN OR NOT) 
		$chkConsentFormTemplateQry = "select * from `consent_forms_template` where consent_delete_status!='true' order by consent_id";
		$chkConsentFormTemplateRes = imw_query($chkConsentFormTemplateQry) or die(imw_error()); 
		$chkConsentFormTemplateNumRow = imw_num_rows($chkConsentFormTemplateRes);
		$chkConsentFormTemplateConsent_id=array();
		if($chkConsentFormTemplateNumRow>0) {
			while($chkConsentFormTemplateRow = imw_fetch_array($chkConsentFormTemplateRes)) {
				$chkConsentFormTemplateConsent_id[] = $chkConsentFormTemplateRow['consent_id'];
			}
		}		
	//END CODE TO CHECK FOR SCHEDULED PATIENT (IF TEMPLATE EXISTS IN ADMIN OR NOT) 
	
//END GET MULTIPLE CONSENT FORMS

$query_rsNotes = "SELECT * FROM eposted WHERE patient_id = '$patient_id' AND patient_id != '' AND patient_conf_id = '$pConfirmId' AND epost_consent_purge_status != 'true' ".$andstubIdQry." order by dtdate desc,T_time desc";
$rsNotes = imw_query($query_rsNotes, $link) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
$i=0;

// GET PATIENT DATA FOR GIVEN CONFIRMATION ID
$patientData = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
$patient_tbl_id = $patientData->patientId;
$ascId = $patientData->ascId;
if(!$patient_tbl_id) {
	$patient_tbl_id = $_REQUEST['patient_id'];
}
$getPatientNameQry = imw_query("SELECT patient_fname,patient_mname,patient_lname
						FROM patient_data_tbl WHERE patient_id = '$patient_tbl_id'");
$getPatientNameNumRow = imw_num_rows($getPatientNameQry);
if($getPatientNameNumRow>0) {
	$getPatientNameRow = imw_fetch_array($getPatientNameQry);
	$patientFName = $getPatientNameRow['patient_fname'];
	$patientMName = $getPatientNameRow['patient_mname'];
	$patientLName = $getPatientNameRow['patient_lname'];
}
if($patientMName) {
	$patientMName = ' '.$patientMName;
}
$patientName = $patientLName.', '.$patientFName.$patientMName;
//END GET PATIENT DATA FOR GIVEN CONFIRMATION ID

?>
<!DOCTYPE html>
<html>
<head>
<title>EpostIt</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
<link href="css/form.css" type="text/css" rel="stylesheet">
<style>
	form{margin:0px;}
	a.black:hover{color:"Red";	text-decoration:none;}
	body{
		scrollbar-face-color: #D1E0C9;
		scrollbar-shadow-color: #A4A4A4;
		scrollbar-highlight-color: #808080;
		scrollbar-3dlight-color: #D1E0C9;
		scrollbar-darkshadow-color: #0a7225;
		scrollbar-track-color: #FFFFFF;
		scrollbar-arrow-color: #000000;
	}
	.text_10 { 
		font-family:"verdana"; 
		font-size:14px; 
		color:#000000; 
		font-weight:normal;
	}
	.text_10b { 
		font-family:"verdana"; 
		font-size:14px; 
		color:#000000; 
		font-weight:bold;  
	}
	.blue_txt{
		color:#3232F0;
	}
.style8 {font-size: 16px}
.style9 {font-family: "verdana"; font-size: 16px; color: #000000; font-weight: normal; }
</style>	
<script type="text/javascript" src="js/jsFunction.js"></script>
<script type="text/javascript">
window.focus();

function validate_form ( )
{
    valid = true;

    if ( document.form1.epost_data.value == "" )
    {
        alert ( "Please enter ePostIt" );
        valid = false;
	}else if (document.form1.table_name.value == "")
    
	{
        alert ( "Please Select Chart Note" );
        valid = false;
    }
	if(valid==false) {
		return false;
	}else {
		document.form1.submit();
		return true;
	}
}
</script>
</head>
<body style="background-color:#ECF1EA; margin:0px;" >
	<table class="table_collapse">
		<tr>
			<td style="height:32px; background-image:url(images/top_bg.jpg); background-color:#D1E0C9;" class="valignTop text_10b">
				<table class="table_collapse">
					<tr class="text_10b alignLeft">
						<td style="width:37%;"><img src="images/tpixel.gif" style="width:8px; height:8px;"><?php echo $patientName; /*if($ascId){ echo ' ASC# : '.$ascId; }*/ ?><span class="text_10" style="color:#FFFFFF; font-weight:normal;"></span> <span style="color:#FFFFFF;"></span></td>
						<td style="width:33%;" class="valignMiddle">
							<div style="color:#CB6B43;" class="top_headding txt_10b alignCenter"><?php echo "EpostIt" ?></div>
						</td>
						<td style="width:23%;" class="alignRight valignMiddle"><div id="dt_tm" style="color:#FFFFFF; font-weight:normal;"></div></td>
						<td style="width:7%;" class="alignCenter valignMiddle">&nbsp;&nbsp;<img src="images/close.jpg" alt="Close" style="width:20px;  height:22px;" onClick="javascript:window.close();"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table class="table_collapse">
	   <tr>
		 <td colspan="2" class="alignCenter" style="height:22px; background-color:#F1F4F0;"><span class="text_10b">Enter ePostIt</span></td>
		 <td class="alignCenter"></td>
		 <td style="width:64%;" rowspan="2" class="alignLeft valignTop">
			<div style="border:0px solid red; height:370px; width:600px; overflow:x-hidden; overflow:auto;">
				 <table class="table_collapse" style="width:95%;">
					  <?php 
					  if($totalRows_rsNotes > 0) { 
							$a='';  
							//$consentFormTemplateSelectConsentId;
							$patAlert=false;
							
							while ($row_rsNotes = imw_fetch_array($rsNotes)) { 
								if($i%2==0) { 
									$a="#FFFFFF"; 
								} else { 
									$a="#F1F4F0";
								} 
								$EpostDataTime = $row_rsNotes['T_time'];
								$patAlertImg="";
								if($row_rsNotes['table_name']=="alert"){
									$patAlert=true;	
									$patAlertImg='&nbsp;<img title="Patient Alert" src="images/pt_alert.png" style="height:17px;width:17px;">';
								}
								
								//START CODE TO CHECK TO DISPLAY RECORD
									$consent_template_idShow = $row_rsNotes['consent_template_id'];
									$showConfirmedRecord = 'false';
									$showScheduledRecord = 'false';
									if($pConfirmId && (in_array($consent_template_idShow,$consentFormTemplateSelectConsentId) || $consent_template_idShow=='0')) {
										$showConfirmedRecord='true';
										$showScheduledRecord = 'true';
									}
									
									if(!$pConfirmId && (in_array($consent_template_idShow,$chkConsentFormTemplateConsent_id) || $consent_template_idShow=='0')) {
										$showConfirmedRecord='true';
										$showScheduledRecord = 'true';
									}
								//END CODE TO CHECK TO DISPLAY REOCRD
								
								//CODE TO SET $EpostDataTime 
									if($EpostDataTime=="00:00:00" || $EpostDataTime=="") {
										
									$EpostDataTime=date("h:i A");
									}else {
										$EpostDataTime=$EpostDataTime;
									}
										
									$time_split_EpostDataTime = explode(":",$EpostDataTime);
									if($time_split_EpostDataTime[0]>=12) {
										$am_pm = "PM";
									}else {
										$am_pm = "AM";
									}
									if($time_split_EpostDataTime[0]>=13) {
										$time_split_EpostDataTime[0] = $time_split_EpostDataTime[0]-12;
										if(strlen($time_split_EpostDataTime[0]) == 1) {
											$time_split_EpostDataTime[0] = "0".$time_split_EpostDataTime[0];
										}
									}else {
										//DO NOTHNING
									}
									$EpostDataTime = $time_split_EpostDataTime[0].":".$time_split_EpostDataTime[1]." ".$am_pm;
								
								//END CODE TO SET EpostDataTime
								
							  if($showScheduledRecord=='true' && $showConfirmedRecord=='true') {
							 ?>
								<tr style="background-color:<?php echo $a;?>; height:22px;">
									<td class="text_10b valignTop nowrap"><?php $datestring= $row_rsNotes['dtdate']; 
										$d=explode("-",$datestring);
										echo $d[1]."/".$d[2]."/".$d[0];
										echo $patAlertImg;
										?>
									</td>
									<td>&nbsp;</td>
									<td class="valignTop text_10b nowrap"><?php echo $EpostDataTime; ?></td>
									<td>&nbsp;</td>
									<td class="valignTop text_10b">
										<a class="link_home" href="epost_new_patient.php?editid=<?php echo $row_rsNotes['epost_id']; ?>&amp;patient_id=<?php echo $patient_id;?>&amp;pConfirmId=<?php echo $pConfirmId;?>&amp;spanPtAlertId=<?php echo $_REQUEST['spanPtAlertId'];?>&amp;stub_id=<?php echo $_REQUEST['stub_id'];?>">Edit</a>
									</td>
									<td>&nbsp;</td>
									<td class="text_10b valignTop">
										<a  class="link_home" href="epost_new_patient.php?delid=<?php echo $row_rsNotes['epost_id']; ?>&amp;patient_id=<?php echo $patient_id;?>&amp;pConfirmId=<?php echo $pConfirmId;?>&amp;spanPtAlertId=<?php echo $_REQUEST['spanPtAlertId'];?>&amp;stub_id=<?php echo $_REQUEST['stub_id'];?>" onClick="if(!confirm('Do you really want to delete')) return false;">Delete</a>
									</td>
								</tr>
								<tr style="background-color:<?php echo $a;?>;" class="alignLeft">
									<td colspan="7" class="alignLeft text_10"><?php echo $row_rsNotes['epost_data']; ?></td>
								</tr>
								<tr style="background-color:<?php echo $a;?>;"><td colspan="7"></td></tr>
						  <?php 
							}	
							$i++;
						} //END WHILE
					} else { echo "<tr><td class='alignCenter text_10b'>No record found</td></tr>"; 
					
					} ?>
				</table>
			</div>
			</td>
		</tr>
	<?php 
		$id_edit = $_REQUEST['editid'];
		if($id_edit!='')
		{
			$qry_Notes = "select * from eposted where epost_id = $id_edit";
			$fetch_Notes = imw_query($qry_Notes);
			$row_notes = imw_fetch_array($fetch_Notes);
		}
	
	
	
	
	?>   
   <tr>
     <td style="width:1%; height:196px;" class="alignCenter valignTop">&nbsp;</td>
	 <td style="width:35%; height:196px;" class="alignLeft valignTop">
	 <form method="post" name="form1" action="<?php echo $editFormAction; ?>" style="margin:0px;">
        <textarea name="epost_data" cols="30" rows="8" class="field textarea justi " style="border:1px solid #cccccc; width:290px; height:280px; margin:0px;"><?php echo $row_notes['epost_data']; ?></textarea>
        <input type="hidden" name="editid" value="<?php echo $_REQUEST['editid']; ?>">   
        <input type="hidden" name="dtdate" value="<?php echo date("Y-m-d"); ?>">
		<input type="hidden" name="T_time" value="<?php echo date('H:i:s'); ?>">
        <input type="hidden" name="MM_insert" value="form1"><br>
		<input type="hidden" name="patient_id" value="<?php echo $patient_id;?>">
		<input type="hidden" name="patient_conf_id" value="<?php echo $pConfirmId;?>">
		<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId;?>">
        <input type="hidden" name="stub_id" value="<?php echo $stub_id;?>">
        <input type="hidden" id="spanPtAlertId" name="spanPtAlertId" value="<?php echo $_REQUEST['spanPtAlertId'];?>">
		<table class="table_pad_bdr">
			<tr>
				<td class="nowrap text_10b"><?php echo $EditUserType; ?>&nbsp;&nbsp;
					<select name="table_name" class="text_10" style="width:270px;" >
						<option value="">Select Chart Note</option>
                        <option value="alert" <?php if($row_notes['table_name']=="alert"){ echo 'selected';} ?>>Alert</option>
						<?php
						
						if(!$pConfirmId) {
							$consentFormTemplateQry = "select * from `consent_forms_template` where consent_delete_status!='true' order by consent_id";
							$consentFormTemplateRes = imw_query($consentFormTemplateQry) or die(imw_error()); 
							$consentFormTemplateNumRow = imw_num_rows($consentFormTemplateRes);
							//$consentFormAliasArr = array($light_green);
							if($consentFormTemplateNumRow>0) {
								while($consentFormTemplateRow = imw_fetch_array($consentFormTemplateRes)) {
									$consentFormTemplateConsent_id = $consentFormTemplateRow['consent_id'];
									$consentFormTemplateConsentAlias = $consentFormTemplateRow['consent_alias'];
									
									$consentFormTemplateConsentAliasNew = str_ireplace("H&P","H&amp;P",$consentFormTemplateConsentAlias);
						?>
							<option value="consent_multiple_form-<?php echo $consentFormTemplateConsent_id;?>" <?php if($row_notes['table_name']=='consent_multiple_form' && $row_notes['consent_template_id']==$consentFormTemplateConsent_id) { echo 'selected'; }?>><?php echo stripslashes($consentFormTemplateConsentAliasNew);?></option> 
						<?php			
								}
							}
							
						
						?>
						
						<?php
						}
						
						if($pConfirmId) {
							if($consentFormTemplateSelectConsentId) {
								$t=0;
								foreach($consentFormTemplateSelectConsentId as $chkConsentTemplateArrId) {
									$chkConsentSurgeryArrQry = "select * from `consent_multiple_form` where  confirmation_id = '".$pConfirmId."' AND consent_template_id='".$chkConsentTemplateArrId."' AND consent_purge_status!='true'  ";
									$chkConsentSurgeryArrRes = imw_query($chkConsentSurgeryArrQry) or die(imw_error()); 
									$chkConsentSurgeryArrNumRow = imw_num_rows($chkConsentSurgeryArrRes);
									$chkConsentSurgeryArrId='';
									$chkConsentSurgeryArrAlias='';
									if($chkConsentSurgeryArrNumRow>0) {
										$chkConsentSurgeryArrRow = imw_fetch_array($chkConsentSurgeryArrRes);
										//GURLEEN
										$chkConsentSurgeryAutoIncArrId = $chkConsentSurgeryArrRow['surgery_consent_id'];
										//GURLEEN END
										
										$chkConsentSurgeryArrId = $chkConsentSurgeryArrRow['consent_template_id'];
										$chkConsentSurgeryArrAlias = $chkConsentSurgeryArrRow['surgery_consent_alias'];
										if(!$chkConsentSurgeryArrAlias) {
											$chkConsentSurgeryArrAlias = $consentFormAliasArr[$t];
										}
									}else{
										$chkConsentSurgeryArrAlias = $consentFormAliasArr[$t];
									}	
									if($chkConsentSurgeryArrAlias) {
									?>
										<option value="consent_multiple_form-<?php echo $chkConsentTemplateArrId.'-'.$chkConsentSurgeryAutoIncArrId;?>" <?php if($row_notes['table_name']=='consent_multiple_form' && $row_notes['consent_template_id']==$chkConsentTemplateArrId) { echo 'selected'; }?>><?php echo stripslashes($chkConsentSurgeryArrAlias); ?></option> 
									<?php
									}
									$t++;
								}
							}
							
						}

						?>
						<option value="surgical_check_list" <?php if($row_notes['table_name']=='surgical_check_list') { echo 'selected'; }?>>Check List</option>
						<option value="preophealthquestionnaire" <?php if($row_notes['table_name']=='preophealthquestionnaire') { echo 'selected'; }?>>Pre-Op Health Questionnaire</option>
                        <option value="history_physicial_clearance" <?php if($row_notes['table_name']=='history_physicial_clearance') { echo 'selected'; }?>>H &amp; P Clearance</option>
						<option value="preopnursingrecord" <?php if($row_notes['table_name']=='preopnursingrecord') { echo 'selected'; }?>>Pre-Op Nursing Record</option>
						<option value="postopnursingrecord" <?php if($row_notes['table_name']=='postopnursingrecord') { echo 'selected'; }?>>Post-Op Nursing Record</option>
						<option value="preopphysicianorders" <?php if($row_notes['table_name']=='preopphysicianorders') { echo 'selected'; }?>>Pre-Op Physician Orders</option>
						<option value="postopphysicianorders" <?php if($row_notes['table_name']=='postopphysicianorders') { echo 'selected'; }?>>Post-Op Physician Orders</option>
						<option value="localanesthesiarecord" <?php if($row_notes['table_name']=='localanesthesiarecord') { echo 'selected'; }?>>MAC/Local/Regional Anesthesia Record</option>
						<option value="preopgenanesthesiarecord" <?php if($row_notes['table_name']=='preopgenanesthesiarecord') { echo 'selected'; }?>>Pre-Op General Anesthesia Record</option>
						<option value="genanesthesiarecord" <?php if($row_notes['table_name']=='genanesthesiarecord') { echo 'selected'; }?>>General Anesthesia Record</option>
						<option value="genanesthesianursesnotes" <?php if($row_notes['table_name']=='genanesthesianursesnotes') { echo 'selected'; }?>>General Anesthesia Nurses Notes</option>
						<option value="operatingroomrecords" <?php if($row_notes['table_name']=='operatingroomrecords') { echo 'selected'; }?>>Operating Room Record</option>
						<option value="laser_procedure_patient_table" <?php if($row_notes['table_name']=='laser_procedure_patient_table') { echo 'selected'; }?>>Laser Procedure</option>
						<option value="operativereport" <?php if($row_notes['table_name']=='operativereport') { echo 'selected'; }?>>Operative Report</option>
						<option value="dischargesummarysheet" <?php if($row_notes['table_name']=='dischargesummarysheet') { echo 'selected'; }?>>Discharge Summary Sheet</option>
						<option value="patient_instruction_sheet" <?php if($row_notes['table_name']=='patient_instruction_sheet') { echo 'selected'; }?>>Instruction Sheet</option>
					</select> 
				</td>
				
				<td class="text_10 nowrap">
					<?php echo $EditUserName; ?>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="height:4px;">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" class="nowrap alignCenter">
					<input type="hidden" name="submit_click" value="Save">
					<a href="#" onClick="MM_swapImage('saveBtnPg','','images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveBtnPg','','images/save_hover1.jpg',1)"><img src="images/save.jpg" style="border:none;" id="saveBtnPg" alt="save" onClick="return validate_form ( );"></a>
					<img src="images/tpixel.gif" style="width:2px; height:1px;">
					<a href="#" onClick="MM_swapImage('CloseBtnPg','','images/close_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('CloseBtnPg','','images/close_hover.gif',1)"><img src="images/close.gif" style="width:70px; height:25px; border:none;" id="CloseBtnPg" alt="Close" onClick="javascript:window.close();"></a>
				</td>
			</tr>
		</table>
     </form>
     </td>
     <td style="width:1%; height:196px;" class="alignCenter valignTop">&nbsp;</td>
  </tr>   
</table>
<?php if($patAlert==true){ ?>
	<script>
	if(opener.document.getElementById('<?php echo $spanPtAlertId; ?>')){
		opener.document.getElementById('<?php echo $spanPtAlertId; ?>').style.visibility='visible';
	}
    </script>
<?php }else{ ?>
	<script>
	if(opener.document.getElementById('<?php echo $spanPtAlertId; ?>')){
		opener.document.getElementById('<?php echo $spanPtAlertId; ?>').style.visibility='hidden';
	}
	</script>
<?php } ?>
</body>
</html>
<?php imw_free_result($rsNotes); ?>