<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("common/conDb.php");
//$_SESSION['iolink_loginUserName']
//include("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
//include("common/iOLinkCommonFunction.php");
$objManageData = new manageData;

//$rowcolor_discharge_summary_sheet="#FBF5EE";
$rowcolor_discharge_summary_sheet="#F8F9F7";
//$bgCol = '#C0AA1E';
//$borderCol = '#C0AA1E';
$bgHeadingImage = "images/header_bg.jpg";
$cancelSubmitWaitingId = $_GET['cancelSubmitId'];//FROM AJAX -- iOLink.php
$cancelId = $_GET['cancelId'];//FROM AJAX -- iOLinkPtDetail.php
$cancelComment = stripslashes($_GET['cancelComment']);
$iasc_facility_id = $_GET['iasc_facility_id'];//FROM AJAX -- iOLinkPtDetail.php
if(!(trim($iasc_facility_id))) {
	$imwFacIdArr = explode(",",$_SESSION['iolink_iasc_facility_id']);//$constantImwFacilityId	
	$iasc_facility_id = $imwFacIdArr[0];
}
if($cancelSubmitWaitingId) {
	unset($arrayPatientRecord);
	$arrayPatientRecord['comment'] = addslashes($cancelComment);
	$arrayPatientRecord['patient_status'] = "Canceled";
	$objManageData->updateRecords($arrayPatientRecord, 'patient_in_waiting_tbl', 'patient_in_waiting_id', $cancelSubmitWaitingId);
	
	$stubTblUpdtQry = "UPDATE stub_tbl SET patient_status = 'Canceled', comment='".addslashes($cancelComment)."' WHERE iolink_patient_in_waiting_id = '".$cancelSubmitWaitingId."' AND iolink_patient_in_waiting_id !='0'";
	$stubTblUpdtRes = imw_query($stubTblUpdtQry) or die(imw_error());
	
	include('connect_imwemr.php'); //imwemr connection Canceled
	$andSchUpdtQry = "";
	if(trim($iasc_facility_id)) {
		$andSchUpdtQry = " AND sa_facility_id = '".$iasc_facility_id."' ";	
	}
	$iASCschUpdtQry = "UPDATE schedule_appointments SET sa_patient_app_status_id = '18', sa_comments='".addslashes($cancelComment)."' WHERE iolink_iosync_waiting_id='".$cancelSubmitWaitingId."' ".$andSchUpdtQry;
	$iASCschUpdtRes = imw_query($iASCschUpdtQry) or die(imw_error());
	imw_close($link_imwemr); //CLOSE imwemr connection
	include("common/conDb.php");
}
if($cancelId) {

	$patientInWaitingDetails = $objManageData->getRowRecord('patient_in_waiting_tbl', 'patient_in_waiting_id', $cancelId);
	$patientInWaitingId = $patientInWaitingDetails->patient_in_waiting_id;
	$patient_dos_temp = $patientInWaitingDetails->dos;
	$patient_dos='';
	if($patient_dos_temp) { $patient_dos = date('m-d-Y',strtotime($patient_dos_temp)); }

	$surgery_time = $patientInWaitingDetails->surgery_time;
	$surgery_time_temp='';
	if($surgery_time) { $surgery_time_temp = date('h:i A',strtotime($surgery_time)); }
	
	$patient_prim_proc = stripslashes(trim($patientInWaitingDetails->patient_primary_procedure));
	$comment = $patientInWaitingDetails->comment;
	$patient_id = $patientInWaitingDetails->patient_id;
	
	$patientDetails = $objManageData->getRowRecord('patient_data_tbl', 'patient_id', $patient_id);
	$patient_first_name = $patientDetails->patient_fname;
	$patient_middle_name = $patientDetails->patient_mname;
	$patient_last_name = $patientDetails->patient_lname;
	$patient_name = $patient_last_name.", ".$patient_first_name;
	
?>
	<form name="frmiOLinkCancelPatient" action="iOLinkPtCancel.php" method="post">
		<table border="0" cellpadding="0" cellspacing="0" width="500">
			<tr height="25">	
				<td width="500" align="left" valign="middle" background="<?php echo $bgHeadingImage;?>" class="text_printb">&nbsp;&nbsp;iOLink</td>
			</tr>
			<tr>
				<td align="left" width="100%" >
					<table border="1"  cellpadding="0" cellspacing="0" width="100%" style=" border:0.5px;border:solid 1px; border-color:#9FBFCC; ">
						<tr>
							<td bgcolor="<?php echo $rowcolor_discharge_summary_sheet; ?>" class="text_print" align="left">
								<table border="0" bordercolor="<?php echo $borderCol; ?>" cellpadding="0" cellspacing="0" width="100%" style=" border:0.5px;">
									<tr><td colspan="2" height="3"></td></tr>	
									<tr>
										<td bgcolor="<?php echo $rowcolor_discharge_summary_sheet; ?>" class="text_print" align="left" valign="middle">
											<img src="images/stop.gif" align="bottom" alt="stop">
										</td>
										<td bgcolor="<?php echo $rowcolor_discharge_summary_sheet; ?>" class="text_print" align="right" valign="top">
											<label class="text_printb">Cancellation:</label>
												<select  name="del_reason" id="del_reason" class="text_print">
												  <option value="">Select Reason</option>
												  <option value="Death in the Family">Death in the Family</option>
												  <option value="Deceased/Expired">Deceased/Expired</option>
                                                  <option value="Dr.Vacation">Dr.Vacation</option>
                                                  <option value="Earlier Appointment">Earlier Appointment</option>
												  <option value="Illness">Illness</option>
                                                  <option value="Later Appointment">Later Appointment</option>
												  <option value="No Reason">No Reason</option>
                                                  <option value="other">Other</option>
												  <option value="Patient Leaving Practice">Patient Leaving Practice</option>
												  <option value="Patient wants to wait">Patient wants to wait</option>
												  <option value="Weather">Weather</option>
											  </select>
										</td>
									</tr>
									<tr>
										<td colspan="2" bgcolor="<?php echo $rowcolor_discharge_summary_sheet; ?>" class="text_printb" align="left">
											<img src="images/tpixel.gif" width="10" height="2">
											Cancel <?php echo $patient_prim_proc;?> for <?php echo $patient_name;?> at <?php echo $patient_dos;?>&nbsp;<?php echo $surgery_time_temp;?>
										</td>
									</tr>
									<tr><td colspan="2" height="5"></td></tr>
									<tr>
										<td colspan="2" bgcolor="<?php echo $rowcolor_discharge_summary_sheet; ?>" class="text_printb" align="center">
											<a href="#" onClick="MM_swapImage('iOLinkokBtn','','images/ok_onclick.jpg',1);" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('iOLinkokBtn','','images/ok_hover.jpg',1)"><img src="images/ok.jpg" name="iOLinkokBtn" border="0" id="iOLinksaveBtn" alt="ok" onClick="cancelBookedPatient('<?php echo $patientInWaitingId?>');"/></a>
											<a href="#" onClick="MM_swapImage('iOLinkCancelBtn','','images/cancel_onclick1.jpg',1)" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('iOLinkCancelBtn','','images/cancel_hover1.jpg',1)"><img src="images/cancel.jpg" name="iOLinkCancelBtn" width="70" height="25" border="0" id="iOLinkCancelBtn" onClick="top.iframeHome.document.getElementById('ptCancelDiv').style.display='none';" alt="Cancel" /></a>
											<br><br>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
								
				</td>
			</tr>
		</table>
	</form>	
<?php
}	
?>
