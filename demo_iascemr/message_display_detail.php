<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
set_time_limit(180);
include_once("common/conDb.php");
include_once("common/commonFunctions.php");
include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$loginUser = $_SESSION['loginUserId'];


$msg_id = $_REQUEST['msg_id'];

// UPDATE MESSAGE READ STATUS 
	if($msg_id <> "") {
		unset($arrayRecord);
		$arrayRecord['read_status'] = 'true';
		$updateMsgReadStatus = $objManageData->updateRecords($arrayRecord, 'msg_tbl', 'msg_id', $msg_id);
	}
// END UPDATE MESSAGE READ STATUS

//FUNCTION TO SET TIME FORMAT
	function getMessageTime($msg_tbl_all_time) {
		if($msg_tbl_all_time<>'00:00:00' || $msg_tbl_all_time<>'') {
			$time_split = explode(":",$msg_tbl_all_time);
			if($time_split[0]>=12) {
				$am_pm = "PM";
			}else {
				$am_pm = "AM";
			}
			if($time_split[0]>=13) {
				$time_split[0] = $time_split[0]-12;
				if(strlen($time_split[0]) == 1) {
					$time_split[0] = "0".$time_split[0];
				}
			}else {
				//DO NOTHNING  
			}
			$msg_tbl_time = $time_split[0].":".$time_split[1]." ".$am_pm;
		}
		return $msg_tbl_time;
	}
//END FUNCTION TO SET TIME FORMAT

//GET SURGERY CENTER NAME
$surgerycenter_name_qry = "select * from surgerycenter where surgeryCenterId=1";
$surgerycenter_name_res = imw_query($surgerycenter_name_qry) or die(imw_error());
$surgerycenter_name_row = imw_fetch_array($surgerycenter_name_res);
$surgerycenter_name = $surgerycenter_name_row["name"];
//END GET SURGERY CENTER NAME

//SET COUNT OF UNREAD MESSAGE
$msgCountQry = "select * from msg_tbl where read_status = '' AND msg_delete_status='' AND msg_user_id='$loginUser'";
$msgCountRes = imw_query($msgCountQry) or die(imw_error());
$msgCountNumRow = imw_num_rows($msgCountRes);
?>
<script>
	var unreadMessage = '<?php echo $msgCountNumRow;?>';
	if(unreadMessage>0) {
		unreadMessage = '('+unreadMessage+')'
	}else {
		unreadMessage = '';
	}
	if(top.document.getElementById('messg_unread_count_id')){
		top.document.getElementById('messg_unread_count_id').innerText=unreadMessage;
	}	
</script>
<?php
//SET COUNT OF UNREAD MESSAGE

//GET USER INFORMATION
$userLoggedDetails = $objManageData->getRowRecord('users', 'usersId', $loginUser);
$userFname = $userLoggedDetails->fname;
$userMname = $userLoggedDetails->mname;
$userLname = $userLoggedDetails->lname;

if($userMname) {
	$userMname = $userMname.' ';
}
$loggedUserName = $userLname.', '.$userMname.$userFname;
//END GET USER INFORMATION

?>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td align="right"><img src="images/left.gif" width="3" height="24"></td>
			<!-- <td align="left" bgcolor="#BCD2B0" nowrap style="font-weight:bold; font-size:12px; "><a href="#" class="link_top" style="cursor:hand;">Go Back To Message List</a></td> -->
			<td align="center" bgcolor="#BCD2B0" nowrap style="font-weight:bold; color:#CB6B43;"><?php echo $surgerycenter_name;?></td>
			<td align="right" bgcolor="#BCD2B0" ><img src="images/chk_off1.gif" style="cursor:hand;" onClick="top.frames[0].location = 'home_inner_front.php';"></td>
			<td align="left" valign="top"><img src="images/right.gif" width="3" height="24"></td>
		</tr>		
		<tr>
			<td></td>
			<td colspan="2"> 
					<div style="overflow:auto; height:520px;" align="center">				
						<table align="left"  cellpadding="0" cellspacing="0" border="0" width="90%">
							<?php
							$msgDisplayQry = "select * from msg_tbl where msg_id = '".$_REQUEST['msg_id']."'";
							$msgDisplayRes = imw_query($msgDisplayQry) or die(imw_error());
							$msgDisplayNumRow = imw_num_rows($msgDisplayRes);
							$msgDisplayRow = imw_fetch_array($msgDisplayRes);
							$msg_detail =  stripslashes($msgDisplayRow["msg_detail"]);
							$msg_dateTemp = $msgDisplayRow["msg_date"];
							list($msg_year,$msg_month,$msg_day) =explode('-',$msg_dateTemp);
							$msg_date = $msg_month.'-'.$msg_day.'-'.$msg_year;
							$msg_time = getMessageTime($msgDisplayRow["msg_time"]);
								
							//GET PRIMARY PROCEDURE AND SITE, ASCID, DOS
								$msgDetailConfirmationInfo  = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $msgDetailConfirmation_id);
								$msgDetailPatientPrimaryProcedure = $msgDetailConfirmationInfo->patient_primary_procedure;
								$msgDetailPatientSiteNumber = $msgDetailConfirmationInfo->site;
								//SET SITE OS/OD/OU
									if($msgDetailPatientSiteNumber=='1') {
										$msgDetailPatientSite =  'OS';
									}else if($msgDetailPatientSiteNumber=='2') {
										$msgDetailPatientSite =  'OD';
									}else if($msgDetailPatientSiteNumber=='3') {
										$msgDetailPatientSite =  'OU';
									}
								//END SITE OS/OD/OU
								
								/*
								$msgDetailPatientAscId = $msgDetailConfirmationInfo->ascId;
								$msgDetailPatientDosTemp = $msgDetailConfirmationInfo->dos;
							
								if($msgDetailPatientAscId) { $msgDetailPatientDisplayAscId = '('.$msgDetailPatientAscId.')'; }
								if($msgDetailPatientDosTemp) { 
									list($msgDetailPatientDos_year,$msgDetailPatientDos_month,$msgDetailPatientDos_day) =explode('-',$msgDetailPatientDosTemp);
									$msgDetailPatientDisplayDos = $msgDetailPatientDos_month.'-'.$msgDetailPatientDos_day.'-'.$msgDetailPatientDos_year;
									$msgDetailPatientDisplayDos = '('.$msgDetailPatientDisplayDos.')';
								}
								*/
							//END GET PRIMARY PROCEDURE AND SITE, ASCID, DOS
							
							$msg_detail= str_replace("Surgeon","<b>".$loggedUserName."</b>",$msg_detail);
							//$msg_detail= str_replace("To;","To&nbsp;-&nbsp;",$msg_detail);
							$msg_detail= str_replace("CC:"," ",$msg_detail);
							$msg_detail= str_replace("Super"," ",$msg_detail);
							$msg_detail= str_replace("Body&nbsp;-&nbsp;"," ",$msg_detail);
							$msg_detail= str_replace("Following","<br>Following",$msg_detail);
							$msg_detail= str_replace("If not","<br>If not",$msg_detail);
							//$msg_detail= str_replace("Surgery forms","Following",$msg_detail);
							
							$msgDetailFinalizeStatus = $msgDisplayRow["finalize_status"];
							$msgDetailPatient_id = $msgDisplayRow["patient_id"];
							$msgDetailConfirmation_id = $msgDisplayRow["confirmation_id"];
							
							//GET PATIENT INFORMATION
							$msgDetailPatientInfo  = $objManageData->getRowRecord('patient_data_tbl', 'patient_id', $msgDetailPatient_id);
							$msgDetailPatientFname = $msgDetailPatientInfo->patient_fname;
							$msgDetailPatientMname = $msgDetailPatientInfo->patient_mname;
							$msgDetailPatientLname = $msgDetailPatientInfo->patient_lname;
							if($msgDetailPatientMname) {
								$msgDetailPatientMname = $msgDetailPatientMname.' ';
							}
							$msgDetailPatientName = $msgDetailPatientLname.', '.$msgDetailPatientMname.$msgDetailPatientFname;
							//END GET PATIENT INFORMATION 
							
							?>
							<tr height="20">
								<td align="left" class="text_10b" >
									<a href="message_display.php?msg_user_id=<?php echo $loginUser;?>" class="link_top" style="cursor:hand;">
										Go Back To Message List
									</a>	
								</td>
							</tr>	
							<tr height="20">
								<td align="left" class="text_10b" ></td>
							</tr>		
							<tr>
								<td align="left" class="text_10" >
									 
									<table  align="right" border="0" cellpadding="0" cellspacing="0" class="text_10" width="90%">
										<tr height="20">
											<td  align="left" class="text_10" width="9%">
												Date:
											</td>
											<td  align="left" class="text_10b" width="91%">
												<?php echo $msg_date.' '.$msg_time;?>
											</td>
										</tr>
										<tr height="20">
											<td colspan="2" align="left" class="text_10">
												<?php echo $msg_detail;?> 
											</td>
										</tr>
									</table>	
									 										
								</td>
							</tr>
						</table>	
					</div>		
			</td>
			<td></td>
		</tr>
	</table>
