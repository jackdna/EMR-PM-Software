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

//CODE TO DELETE SELECTED MESSAGES
$delRecords =  $_POST['delRecords'];
if($delRecords=='true'){
	$delChkBoxes = $_POST['msgDelChkBox'];  
	if(is_array($delChkBoxes)){
		foreach($delChkBoxes as $msgId){
			//$objManageData->delRecord('msg_tbl', 'msg_id', $msgId);
			unset($arrayRecord);
			$arrayRecord['msg_delete_status'] = 'true';
			$objManageData->updateRecords($arrayRecord, 'msg_tbl', 'msg_id', $msgId);	
		
		}
	}	
}

//END CODE TO DELETE SELECTED MESSAGES

$msg_user_id = $_REQUEST['msg_user_id'];


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

//GET SURGERY CENTER NAME
$surgerycenter_name_qry = "select * from surgerycenter where surgeryCenterId=1";
$surgerycenter_name_res = imw_query($surgerycenter_name_qry) or die(imw_error());
$surgerycenter_name_row = imw_fetch_array($surgerycenter_name_res);
$surgerycenter_name = $surgerycenter_name_row["name"];
//END GET SURGERY CENTER NAME


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

$msgDisplayQry = "select * from msg_tbl where msg_user_id = '".$msg_user_id."' AND msg_delete_status='' order by msg_date DESC,msg_time DESC";
//$msgDisplayQry = "select * from msg_tbl where msg_user_id = '".$msg_user_id."' AND msg_delete_status='' order by msg_date ASC, msg_time ASC LIMIT 0,5";
$msgDisplayRes = imw_query($msgDisplayQry) or die(imw_error());
$msgDisplayNumRow = imw_num_rows($msgDisplayRes);

?>
<script>
function chkMsgFrm() {
	var boxesChk = false;
	obj = document.getElementsByName("msgDelChkBox[]");
	objLength = document.getElementsByName("msgDelChkBox[]").length;
	for(i=0; i<objLength; i++){
		if(obj[i].checked == true){
			var boxesChk = true;
		}
	}
	if(boxesChk!=true){
		alert("Please select message(s) to be deleted!")
	}else{
		var ask = confirm('Are you sure: Delete the selected message(s)?')
		if(ask==true){
			document.frm_msg_list.delRecords.value = 'true';
			document.frm_msg_list.submit();
			//document.getElementById('cancelButton').style.display = 'none';
		}
	}
}

function chk_num_fun(obj){
	var cont=document.getElementsByName("msgDelChkBox[]");
	var val = '';
	if(obj.checked==true){
		var check = true;
	}
	else{
		var check = false;
	}
	var vals = '';
	var getVal = document.getElementById("chk_val2").value;
	for(i=0;i<cont.length;i++){
		cont[i].checked=check;		
		getVal += cont[i].value+",";
		vals += cont[i].value+",";
	}
	var newVal = getVal.split(',');
	newVal.sort();	
		
	if(obj.checked==true){
		for(i=0;i<newVal.length;i++){
			if(newVal[i] != newVal[parseInt(i+1)]){
				val += newVal[i]+",";
			}
		}		
	}
	else {
		for(i=0;i<newVal.length;i++){
			if(newVal[i] != newVal[parseInt(i+1)]){
				val = '';
			}
		}
	}
	/*
	else{		
		var getVal = document.getElementById("chk_val2").value;
		var newVal = getVal.split(',');
		newVal.sort();
		var val2 = '';
		for(i=0;i<newVal.length;i++){
			for(v=0;v<cont.length;v++){
				if(newVal[i] == cont[v].value){
					newVal[i] = '';
				}
			}			
		}
		for(i=0;i<newVal.length;i++){
			if(newVal[i] != ''){
				val += newVal[i]+",";
			}
		}
	}
	*/
	document.getElementById("chk_val2").value = val;
	//document.getElementById("chk_val").value = val;	
	
}
</script>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td align="right"><img src="images/left.gif" width="3" height="24"></td>
			<td align="center" bgcolor="#BCD2B0" nowrap style="font-weight:bold; color:#CB6B43;"><?php echo $surgerycenter_name;?></td>
			<td align="right" bgcolor="#BCD2B0" ><img src="images/chk_off1.gif" style="cursor:hand;" onClick="top.frames[0].location = 'home_inner_front.php';"></td>
			<td align="left" valign="top"><img src="images/right.gif" width="3" height="24"></td>
		</tr>		
		<tr>
			<td></td>
			<td colspan="2">
								
				<table  cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr height="22">
						<td align="left" class="text_10" width="7%" nowrap>
							Select All&nbsp;<Br><input type="checkbox" name="sel_all" onClick="chk_num_fun(this);">
							<!-- <a href="#" style="cursor:hand;" class="link_slid_right" onClick="javascript:chkMsgFrm();"> -->
								
							<!-- </a> -->
						</td>
						<td align="left" class="text_10" width="9%" valign="top">
							From
						</td>
						<td align="left" class="text_10" width="76%" valign="top">
							Subject
						</td>
						<td align="left" class="text_10" width="8%" valign="top">
							Date
						</td>
					</tr>
				</table>
				<div style="overflow:auto; height:480px; ">
					<form name="frm_msg_list" method="post" action="message_display.php?msg_user_id=<?php echo $msg_user_id;?>">
						<input type="hidden" name="delRecords" value="">
						<table  cellpadding="0" cellspacing="0" border="0" width="100%">
							<?php
							$i=0;
							if($msgDisplayNumRow>0) {
								while($msgDisplayRow = imw_fetch_array($msgDisplayRes)) {
									if($i%2==0) { 
										$msg_bgcolor="#FFFFFF"; 
									} else { 
										$msg_bgcolor="#F1F4F0";
									} 
									$msg_id = $msgDisplayRow["msg_id"];
									//$msg_detail =  stripslashes($msgDisplayRow["msg_detail"]);
									$msg_dateTemp = $msgDisplayRow["msg_date"];
									list($msg_year,$msg_month,$msg_day) =explode('-',$msg_dateTemp);
									$msg_date = $msg_month.'-'.$msg_day.'-'.$msg_year;
									$msg_time = getMessageTime($msgDisplayRow["msg_time"]);
									$msg_subject =  stripslashes($msgDisplayRow["msg_subject"]);
									$msg_finalize_status = $msgDisplayRow["finalize_status"];
									$msg_confirmation_id = $msgDisplayRow["confirmation_id"];
									$msg_patient_id = $msgDisplayRow["patient_id"];
									$msg_read_status = $msgDisplayRow["read_status"];
									//GET PATIENT NAME
										$msgPatientNameQry = "select * from patient_data_tbl where patient_id='$msg_patient_id'";
										$msgPatientNameRes = imw_query($msgPatientNameQry) or die(imw_error());
										$msgPatientNameRow = imw_fetch_array($msgPatientNameRes);
											$msgPatientFirstName = $msgPatientNameRow['patient_fname'];
											$msgPatientMiddleName = $msgPatientNameRow['patient_mname'];
											$msgPatientLastName = $msgPatientNameRow['patient_lname'];
											if($msgPatientMiddleName) {
												$msgPatientMiddleName = $msgPatientMiddleName.' ';
											}
											$msgPatientName = $msgPatientLastName.', '.$msgPatientMiddleName.$msgPatientFirstName;
						
									//END GET PATIENT NAME
									
									//GET ASCID, DOS
										$msgConfirmationInfo  = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $msg_confirmation_id);
										$msgPatientAscId = $msgConfirmationInfo->ascId;
										$msgPatientDosTemp = $msgConfirmationInfo->dos;
									
										if($msgPatientAscId) { $msgPatientDisplayAscId = '('.$msgPatientAscId.')'; }else{ $msgPatientDisplayAscId = '(0)'; }
										if($msgPatientDosTemp) { 
											list($msgPatientDos_year,$msgPatientDos_month,$msgPatientDos_day) =explode('-',$msgPatientDosTemp);
											$msgPatientDisplayDos = $msgPatientDos_month.'-'.$msgPatientDos_day.'-'.$msgPatientDos_year;
											$msgPatientDisplayDos = '('.$msgPatientDisplayDos.')';
										}
									//END GET ASCID, DOS 
									
									if($msg_read_status=='') {
										$msg_class = 'text_10b';
									}else {
										$msg_class = 'text_10';
									}
									if($msg_finalize_status=='') {
										if(!$msg_subject) {
											$msg_subject = 'Warning '.$msgPatientName.$msgPatientDisplayAscId.$msgPatientDisplayDos.' Surgery forms Not Finalized'; 
										}
									}else {
										if(!$msg_subject) {
											$msg_subject = 'System has Finalized '.$msgPatientName.$msgPatientDisplayAscId.$msgPatientDisplayDos.' Surgery forms'; 
										}
									}
								?>
									<tr height="22" class="<?php echo $msg_class;?>" bgcolor="#FFFFFF">
										<td align="left" width="7%">
											<input type="checkbox" name="msgDelChkBox[]" value="<?php echo $msg_id;?>">
										</td>
										<td align="left" width="9%">
											System
											<?php //echo $surgerycenter_name;?>
										</td>
										<td align="left" width="76%" style="cursor:hand;" onClick="location.href='message_display_detail.php?msg_id=<?php echo $msg_id;?>&msg_user_id=<?php echo $msg_user_id;?>'">
											<?php echo $msg_subject;?>
										</td>
										<td align="left" width="8%" nowrap>
											<?php echo $msg_date;?>
										</td>
									</tr>
									<tr height="22" class="<?php echo $msg_class;?>" bgcolor="#F1F4F0" >
										<td colspan="4"></td>
									</tr>
								<?php
									$i++;
								}
							}else {
							?>
									<tr height="22" class="text_10b" bgcolor="#FFFFFF">
										<td colspan="4" align="center" width="7%">
											No message found
										</td>
										
									</tr>
							<?php
							}	
							?>
						</table>
						<input type="hidden"  name="chk_val2" value="<?php echo $_REQUEST['chk_val2']; ?>" size="36">	
					</form>
				</div>		
			</td>
			<td></td>
		</tr>
		<tr height="10">
			<td colspan="4" align="left" valign="top">
				
			</td>
		</tr>
		<tr>
			<td colspan="4" align="center" valign="top">
				<a href="#" onClick="MM_swapImage('deleteMessageSelected','','images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deleteMessageSelected','','images/delete_selected_hover.gif',1)"><img src="images/delete_selected.gif" name="deleteMessageSelected" id="deleteMessageSelected"  border="0"  alt="Delete" onClick="javascript:chkMsgFrm();"/></a>
			</td>
		</tr>
	</table>
