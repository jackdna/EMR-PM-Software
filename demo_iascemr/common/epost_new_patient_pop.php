<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
require_once('conDb.php'); 
$showAllApptStatus = $_REQUEST['showAllApptStatus'];
$patient_id = $_REQUEST['patient_id'];
$pConfirmId = $_REQUEST['pConfirmId'];
$imwPatientId = $_REQUEST['imwPatientId'];
$ptStubId = $_REQUEST['ptStubId'];
$andStubIdQry = "";
if(!$pConfirmId) {
	$pConfirmId = '0';
	$andStubIdQry = " AND stub_id = '".$ptStubId."' AND stub_id != '0' ";
}
$txt_patient_search_id = $_REQUEST['txt_patient_search_id'];
$spanPtAlertId=$_REQUEST['span_pt_alert_id'];
$alert_type=$_REQUEST['alert_type'];

//GET MULTIPLE CONSENT FORMS
$consentFormTemplateSelectQry = "select * from `consent_forms_template` order by consent_id";
$consentFormTemplateSelectRes = imw_query($consentFormTemplateSelectQry) or die(imw_error()); 
$consentFormTemplateSelectNumRow = imw_num_rows($consentFormTemplateSelectRes);
$consentFormAliasArr = array();
$consentFormTemplateSelectConsentId =array();
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

//START GET PATIENT DETAIL
	$epostPatientNameTblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
	$epostPatientNameTblRes = imw_query($epostPatientNameTblQry) or die(imw_error());
	$epostPatientNameTblNumRow = imw_num_rows($epostPatientNameTblRes);
	if($epostPatientNameTblNumRow>0) {
		$epostPatientNameTblRow = imw_fetch_array($epostPatientNameTblRes);
		$epostPatientFName = $epostPatientNameTblRow['patient_fname'];
		$epostPatientMName = $epostPatientNameTblRow['patient_mname'];
		$epostPatientLName = $epostPatientNameTblRow['patient_lname'];

		if($epostPatientMName) {
			$epostPatientMName = ' '.$epostPatientMName;
		}
		$epostPatientName = $epostPatientLName.', '.$epostPatientFName.$epostPatientMName;
	}	
//END GET PATIENT DETAIL
$alert_header="EpostIt";
$query_rsNotes = "SELECT * FROM eposted WHERE patient_id = '$patient_id' AND patient_id != '' AND patient_conf_id = '$pConfirmId' AND epost_consent_purge_status !='true' AND table_name!='alert'  ".$andStubIdQry." order by dtdate desc,T_time desc";
if($alert_type=="span_pt_alert"){
	$alert_header="Patient Alert";
	$query_rsNotes = "SELECT * FROM eposted WHERE patient_id = '$patient_id' AND patient_id != '' AND patient_conf_id = '$pConfirmId' AND epost_consent_purge_status !='true' AND table_name='alert'  ".$andStubIdQry." order by dtdate desc,T_time desc";	
}
$rsNotes = imw_query($query_rsNotes, $link) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
$i=0;  	
?>
<style>
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
</style>	
	<table  border="0" cellpadding="0" cellspacing="0" align="center" width="500">
		<tr>
			<td height="32"   valign="top" background="images/top_bg.jpg" bgcolor="#D1E0C9"   class="text_10b">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
					<tr align="left"  class="text_10b">
						<td width="37%" valign="middle" ><img src="images/tpixel.gif" width="3" height="8"><span class="text_10" style="color:#FFFFFF; font-weight:normal; "></span> <span style="color:#FFFFFF; "><?php echo $epostPatientName;?></span></td>
						<td width="33%" valign="middle" >
							<div style="color:#CB6B43; " class="top_headding txt_10b" align="center"><?php echo $alert_header;?></div>
						</td>
						<td width="23%" align="right" valign="middle"><div  style="color:#FFFFFF; font-weight:normal; "></div></td>
						<td width="7%" align="center" valign="middle">&nbsp;&nbsp;<img src="images/close.jpg" alt="Close" width="20" height="22" onClick="javascript:closeEpostPopNew();"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	 <table  border="0" cellspacing="0" cellpadding="0" width="500" style="border:0px solid red;background-color:#E0E0E0;" >
		<tr  height="22">
			<td colspan="10" align="right" valign="top"  class="text_10b"  nowrap> 
		 		<a class="link_slid_right" href="javascript:void(0);" style="cursor:hand;" onClick="javascript:chkEpostPatientExist('<?php echo $ptStubId;?>','<?php echo $txt_patient_search_id;?>','<?php echo $patient_id;?>','<?php echo $pConfirmId;?>','<?php echo $imwPatientId;?>','<?php echo $showAllApptStatus;?>','<?php echo $spanPtAlertId; ?>');closeEpostPopNew();">
					Click to Add/Edit Epost&nbsp;
				</a>
			</td>
		</tr>	
	</table> 	
	<div align="center"  onMouseOver="stopCloseEpostTime();"  style="border:0px solid red;background-color:#E0E0E0; height:200px; width:500px; overflow:x-hidden; overflow:auto;">  <!-- onMouseOut="closeEpostPopNewSetTime();" -->
	 <table  border="0" cellspacing="0" cellpadding="0" width="480" >
		<!-- <tr  height="22">
			<td colspan="10" align="right" valign="top"  class="text_10b"  nowrap> 
		 		<a class="link_slid_right" href="javascript:void(0);" style="cursor:hand;" onClick="javascript:chkEpostPatientExist('<?php echo $ptStubId;?>','<?php echo $txt_patient_search_id;?>','<?php echo $patient_id;?>','<?php echo $pConfirmId;?>','<?php echo $showAllApptStatus;?>');closeEpostPopNew();">
					Click to Add/Edit Epost
				</a>
			</td>
		</tr> -->	
		  <?php 
		  if($totalRows_rsNotes > 0) { 
				$a='';  
				while ($row_rsNotes = imw_fetch_array($rsNotes)) { 
					if($i%2==0) { 
						$a="#FFFFFF"; 
					} else { 
						$a="#F1F4F0";
					} 
					$EpostDataTime = $row_rsNotes['T_time'];
					
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
					<tr bgcolor="<?php echo $a;?>" height="22">
						<td align="left" valign="top" class="text_10b"  nowrap><?php $datestring= $row_rsNotes['dtdate']; 
							$d=explode("-",$datestring);
							echo $d[1]."/".$d[2]."/".$d[0];
							
							?>
						</td>
						<td>&nbsp;</td>
						<td valign="top" class="text_10b" rowspan="0" nowrap><?php echo $EpostDataTime; ?></td>
						<td>&nbsp;</td>
					</tr>
					<tr bgcolor="<?php echo $a;?>" align="left">
						<td  colspan="10" align="left" class="text_10"><?php echo stripslashes($row_rsNotes['epost_data']); ?></td>
					</tr>
					<tr bgcolor="<?php echo $a;?>"><td colspan="4"></td></tr>
		  <?php 
				}	
				$i++;
			} //END WHILE
		} else { echo "<tr><td colspan='5' align='center' class='text_10b'>No record found</td></tr>"; 
		
		} ?>
	</table>
   </div>

<?php
imw_free_result($rsNotes);
?>
