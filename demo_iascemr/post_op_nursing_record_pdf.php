<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$tablename = "postopnursingrecord";
//include("common/linkfile.php");
include("common_functions.php");
include_once("admin/classObjectFunction.php"); 
$objManageData = new manageData;
include("new_header_print.php");
$patient_id = $_REQUEST['patient_id'];
$pconfId = $_REQUEST['pConfId'];
if(!$pconfId) {
	$pconfId= $_SESSION['pConfId'];
}	
if(!$patient_id) {
	$patient_id= $_SESSION['patient_id'];
}
//VIEW RECORD FROM DATABASE
		$ViewPostopnursingQry = "select * from `postopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewPostopnursingRes = imw_query($ViewPostopnursingQry) or die(imw_error()); 
		$ViewPostopnursingNumRow = imw_num_rows($ViewPostopnursingRes);
		$ViewPostopnursingRow = imw_fetch_array($ViewPostopnursingRes); 
		
		$postOpSite = stripslashes($ViewPostopnursingRow["postOpSite"]);
		
		$postOpSiteTime = $objManageData->getTmFormat($ViewPostopnursingRow["postOpSiteTime"]);
		$hidd_postOpSiteTime = $ViewPostopnursingRow["postOpSiteTime"];
		$bs_na = $ViewPostopnursingRow["bs_na"];
		$bs_value = $ViewPostopnursingRow["bs_value"];
		$blood_sugar = ($bs_na) ? 'N/A' : $bs_value;
		$blood_sugar = ($blood_sugar) ? $blood_sugar : '___';
		$painLevel = $ViewPostopnursingRow["painLevel"];
		$nourishKind = stripslashes($ViewPostopnursingRow["nourishKind"]);
		$removedIntact = $ViewPostopnursingRow["removedIntact"];
		$heparinLockOutTime = $objManageData->getTmFormat($ViewPostopnursingRow["heparinLockOutTime"]);
		$heparinLockOutNA = $ViewPostopnursingRow["heparinLockOutNA"];
		$patient_aox3 = $ViewPostopnursingRow["patient_aox3"];
		$other_mental_status = stripslashes($ViewPostopnursingRow["other_mental_status"]);
		$recoveryComments = stripslashes($ViewPostopnursingRow["recoveryComments"]);
		$relivedNurseId = $ViewPostopnursingRow["relivedNurseId"];
		$patientReleased2Adult = $ViewPostopnursingRow["patientReleased2Adult"];
		$patientsRelation = $ViewPostopnursingRow["patientsRelation"];
		$patientsRelationOther = $ViewPostopnursingRow["patientsRelationOther"];
		$nurseId = $ViewPostopnursingRow["nurseId"]; 
		$nurseInitials = $ViewPostopnursingRow["nurseInitials"]; 
		$form_status  = $ViewPostopnursingRow["form_status"]; 
		$postNurseFormStatus = $ViewPostopnursingRow["form_status"];  
		$dischargeTime = $objManageData->getTmFormat($ViewPostopnursingRow['dischargeTime']);
		$patient_transfer = $ViewPostopnursingRow['patient_transfer'];
		
		$signNurseId =  $ViewPostopnursingRow["signNurseId"];
		$signNurseFirstName =  $ViewPostopnursingRow["signNurseFirstName"];
		$signNurseMiddleName =  $ViewPostopnursingRow["signNurseMiddleName"];
		$signNurseLastName =  $ViewPostopnursingRow["signNurseLastName"]; 
		$signNurseStatus =  $ViewPostopnursingRow["signNurseStatus"];
		$signNursePostOpDateTime =  $ViewPostopnursingRow["signNurseDateTime"];
		$signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
		
		$version_num = $ViewPostopnursingRow['version_num'];
		//$signNurseDateTime =  $ViewPreopnursingRow["signNurseDateTime"];
									
		//GET NURSE ID FIRST TIME FROM PATIENT CONFIRMATION
			if($nurseId=="" || $nurseId==0) {
				$ViewNurseIdQry = "select * from `patientconfirmation` where  patientConfirmationId = '".$_REQUEST["pConfId"]."'";
				$ViewNurseIdRes = imw_query($ViewNurseIdQry) or die(imw_error()); 
				$ViewNurseIdRow = imw_fetch_array($ViewNurseIdRes); 
				$nurseId = $ViewNurseIdRow["nurseId"];
			}	
		//END GET NURSE ID FIRST TIME FROM PATIENT CONFIRMATION
	//GET NURSE NAME
		if($nurseId!=0 && $nurseId!=""){
			$ViewNurseNameQry = "select * from `users` where  usersId = '".$nurseId."'";
			$ViewNurseNameRes = imw_query($ViewNurseNameQry) or die(imw_error()); 
			$ViewNurseNameRow = imw_fetch_array($ViewNurseNameRes); 
			$NurseName = $ViewNurseNameRow["lname"].", ".$ViewNurseNameRow["fname"]." ".$ViewNurseNameRow["mname"];
		}
	//END GET NURSE NAME
	
	//TEMPRARY NAME OF NURSE
		if(trim($NurseName)=="") {
			$NurseName = "Nurse Name";
		}
	//END TEMPRARY NAME OF NURSE
		
	//CODE TO SET POSTOP SITE TIME
		if($postOpSiteTime=="00:00:00" || $postOpSiteTime=="") {
			//$hidd_postOpSiteTime = date("H:i:s");
			//$postOpSiteTime=date("h:i A");
			$postOpSiteTime="____";
		}
	//END CODE TO SET POSTOP SITE TIME
									
	//START CODE TO SET HEPARINLOCKOUT TIME
		if($heparinLockOutTime=="00:00:00" || $heparinLockOutTime=="") {
			$heparinLockOutTime = "____";
		}	
	 
	//END CODE TO SET HEPARINLOCKOUT TIME	
	
	
//END VIEW RECORD FROM DATABASE
//
$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 

$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
$loggedInUserType = $ViewUserNameRow["user_type"];
$loggedInSignatureOfNurse = $ViewUserNameRow["signature"];

if($loggedInUserType<>"Nurse") {
	$loginUserName = $_SESSION['loginUserName'];
	$callJavaFun = "return noAuthorityFun('Nurse');";
//}else if ($loggedInUserType=="Nurse" && !$loggedInSignatureOfNurse) {
	//$callJavaFun = "return noSignInAdmin();";
}else {
	$loginUserId = $_SESSION["loginUserId"];
	$callJavaFun = "document.frm_post_op_nurse.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','post_op_nursing_record_ajaxSign.php','$loginUserId');";
    //$NurseNameShow = $loggedInUserName;
}

//$signOnFileStatus = "Yes";
$TDnurseNameIdDisplay = "block";
$TDnurseSignatureIdDisplay = "none";


if($signNurseId<>0 && $signNurseId<>"") {
	$NurseNameShow = $signNurseName;
	$signOnFileStatus = $signNurseStatus;	
	
	$TDnurseNameIdDisplay = "none";
	$TDnurseSignatureIdDisplay = "block";
}


//
$condArr					=	array();
$condArr['confirmation_id']	=	$_REQUEST["pConfId"];
$condArr['chartName']		=	'post_op_physician_order_form';
$pOrderData					=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$condArr,'physician_order_type="medication" DESC,recordId','Asc');
$pOrderStatus				=	(is_array($pOrderData) && count($pOrderData) > 0 ) ? 1	: 0 ;		
//Recovery vital sign

//SHOW OR TIME FORM operatingroomrecords
$opRoomOrQry 				= "SELECT surgeryTimeIn, surgeryStartTime, surgeryEndTime, surgeryTimeOut FROM operatingroomrecords WHERE confirmation_id = '".$_REQUEST["pConfId"]."' LIMIT 0,1 ";
$opRoomOrRes 				= imw_query($opRoomOrQry) or die(imw_error());
$opRoomOrNumRow 			= imw_num_rows($opRoomOrRes);
$surgeryTimeInOpRoom = $surgeryStartTimeOpRoom = $surgeryEndTimeOpRoom = $surgeryTimeOutOpRoom = "";
if($opRoomOrNumRow>0) {
	$opRoomOrRow			= imw_fetch_array($opRoomOrRes);
	$surgeryTimeInOpRoom 	= $objManageData->getTmFormat($opRoomOrRow["surgeryTimeIn"]);
	$surgeryStartTimeOpRoom = $objManageData->getTmFormat($opRoomOrRow["surgeryStartTime"]);
	$surgeryEndTimeOpRoom 	= $objManageData->getTmFormat($opRoomOrRow["surgeryEndTime"]);
	$surgeryTimeOutOpRoom 	= $objManageData->getTmFormat($opRoomOrRow["surgeryTimeOut"]);
}
$table=$head_table."<br>";
$table.='<table style="width:700px; font-size:14px; border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
			<tr>
				<td class="bgcolor" colspan="2" style="font-size:15px; padding:10px 0 5px 0;text-decoration:underline; text-align:center; font-weight:bold;border-bottom:1px solid #C0C0C0;">Post-Op Nursing Record</td>
			</tr>';
			
			if( $version_num > 1) {
				
					$table.='<tr>
					<td colspan="2" style="width:700px;border-bottom:1px solid #C0C0C0; font-size:14px; padding-top:5px;">';
					$table.=	'<table style="width:100%; font-size:14px;" cellpadding="0" cellspacing="0">';
					$table.=		'<tr>';
					$table.=			'<td style="width:220px;padding:5px;border:1px solid #C0C0C0;"><b>Physician Orders/Medications&nbsp;</b></td>';
					$table.=			'<td style="width:200px;padding:5px;border:1px solid #C0C0C0;"><b>Time</b></td>';
					$table.=			'<td style="width:280px;padding:5px;border:1px solid #C0C0C0;"><b>Not Given</b></td>';
					$table.=		'</tr>';	
					if($pOrderStatus) {
						foreach($pOrderData as $pOrderRow)
						{
						$time  =	($pOrderRow->physician_order_time <> '00:00:00') ? $objManageData->getTmFormat($pOrderRow->physician_order_time) : '' ;
						$table.=	'<tr>';
						$table.=		'<td style="width:220px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities(stripslashes($pOrderRow->physician_order_name)).'</td>';
						$table.=		'<td style="width:200px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.$time.'</td>';
						$table.=		'<td style="width:280px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.$pOrderRow->physician_order_not_given.'</td>';
						$table.=	'</tr>';	
						}	
					}
					else
					{
						for($u = 0; $u < 3; $u++)
						{
							$table .= '<tr>
							<td style="width:180px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
							<td style="width:200px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
							<td style="width:320px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
							</tr>';		
						}
					}
					$table.=	'</table>';
	$table.='	</td>
			 </tr>';
				
			}
	
$ViewPostopNurseVitalSignQry = "select * from `vitalsign_tbl` where  confirmation_id = '".$_REQUEST["pConfId"]."' order by vitalsign_id";
	$ViewPostopNurseVitalSignRes = imw_query($ViewPostopNurseVitalSignQry) or die(imw_error()); 
	$ViewPostopNurseVitalSignNumRow = imw_num_rows($ViewPostopNurseVitalSignRes);
	if($ViewPostopNurseVitalSignNumRow>0) {
		$k=1;
		$table.='
					<tr>
						<td style="width:450px;padding:5px 0px 5px 0px;border-bottom:1px solid #C0C0C0;font-size:14px; font-weight:bold;">Recovery Vital Signs</td>
						<td style="width:150px;padding:5px 0px 5px 0px;border-bottom:1px solid #C0C0C0;font-weight:bold;">Pain Level:&nbsp;'.$painLevel.' </td>
					</tr>
			';		
		while($ViewPostopNurseVitalSignRow = imw_fetch_array($ViewPostopNurseVitalSignRes)) {
			$vitalsign_id=$ViewPostopNurseVitalSignRow["vitalsign_id"];  
			$vitalSignBp = $ViewPostopNurseVitalSignRow["vitalSignBp"];
			$vitalSignP = $ViewPostopNurseVitalSignRow["vitalSignP"];
			$vitalSignR = $ViewPostopNurseVitalSignRow["vitalSignR"];
			$vitalSignO2SAT = $ViewPostopNurseVitalSignRow["vitalSignO2SAT"];
			$vitalSignTime = $objManageData->getTmFormat($ViewPostopNurseVitalSignRow["vitalSignTime"]);
			$vitalSignTemp = $ViewPostopNurseVitalSignRow["vitalSignTemp"];
		$table.='<tr>
					<td colspan="2" style="width:700px;border-bottom:1px solid #C0C0C0; font-size:14px; padding-top:5px;">
						<table style="width:700px; " cellpadding="0" cellspacing="0"> 	
							<tr>
								<td style="width:100px;"><b>BP:</b>&nbsp;'.$vitalSignBp.'</td>
								<td style="width:80px;"><b>P:</b>&nbsp;'.$vitalSignP.'</td>
								<td style="width:80px;"><b>R:</b>&nbsp;'.$vitalSignR.'</td>
								<td style="width:100px;"><b>O2SAT:</b>&nbsp;'.$vitalSignO2SAT.'</td>
								<td style="width:120px;"><b>Time:</b>&nbsp;'.$vitalSignTime.'</td>
								<td style="width:150px;"><b>Temp:</b>&nbsp;'.$vitalSignTemp.'</td>
							</tr>
						</table>
					</td>
				</tr>
				';
		$k++;
		}
	}
	else
	{
		$table.='
			<tr>
				<td style="width:450px;padding:5px 0px 5px 0px;border-bottom:1px solid #C0C0C0;font-size:14px; font-weight:bold;">&nbsp;</td>
				<td style="width:150px;padding:5px 0px 5px 0px;border-bottom:1px solid #C0C0C0;font-weight:bold;">Pain Level:&nbsp;'.$painLevel.' </td>
			</tr>
			';		
	}
	$table.='<tr>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Post-Operative Site:</b>&nbsp;'.$postOpSite.'</td>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Time:</b>&nbsp;';
					if($postOpSiteTime){$table.=$postOpSiteTime;}else{$table.="_____";}
				$table.='</td>
			</tr>
			<tr>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;">
					<table style="width:550px; font-size:14px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:130px;vertical-align:top; font-weight:bold">Nourishment Kind:</td>
							<td style="width:370px;vertical-align:top;">';
							if($nourishKind){$table.=$nourishKind;}else{$table.="________________";}
							$table.='</td>
						</tr>
					</table>
				</td>
				<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;">'.($version_num > 3 ? '<b>Blood Sugar:</b>&nbsp;'.$blood_sugar : '&nbsp;').'</td>
			</tr>
			<tr>
				<td style="padding-top:10px;font-size:15px;border-bottom:1px solid #C0C0C0;"><b>IV Discontinued&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Removed Intact/Pressure Dressing Applied:&nbsp;</b>';
				if($removedIntact=="Yes"){$table.=$removedIntact;}else{$table.="_____";}
				$table.='</td>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Time:</b>&nbsp;';
					if($heparinLockOutTime){$table.=$heparinLockOutTime;}else{$table.="_____";}
					if($heparinLockOutNA=="Yes"){$table.="&nbsp;&nbsp;&nbsp;<b>N/A:</b>&nbsp;Yes";}
				$table.='</td>
			</tr>
			<tr>
				<td colspan="2" style="width:700px;border-bottom:1px solid #C0C0C0; font-size:14px; padding-top:5px;">
					<table style="width:700px; " cellpadding="0" cellspacing="0"> 	
						<tr>
							<td style="width:250px;"><b>Patient Awake, Alert and Oriented times 3:</b>';
								if($patient_aox3=="Yes"){$table.=$patient_aox3;}else{$table.="_____";}
							$table.=	
							'</td>
							<td style="width:250px;"><b>Patient Discharged To Home Via:</b>';
								if($patientReleased2Adult=="Yes"){$table.=$patientReleased2Adult;}else{$table.="_____";}
								$table.=
							'</td>
							<td style="width:200px;"><b>Relationship:</b>';
								if($patientsRelation && $patientsRelation!="other"){$table.=$patientsRelation;}
								else if($patientsRelationOther && $patientsRelation=="other"){$table.=$patientsRelationOther;}else{$table.="_____";}
								$table.=
							'</td>
						</tr>';
						if( $version_num > 2){
							$table.='<tr><td colspan="3" style="width:700px;"><b>Other Mental Status :</b>';
							if($other_mental_status){$table.=$other_mental_status;}else{$table.="_____";}
							$table.='</td></tr>';
						}
					$table.='
					</table>
				</td>
			</tr>';
			if( $version_num > 4){
			$table.='
			<tr>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Patient Transferred To Hospital:</b>&nbsp;';
					if($patient_transfer){$table.=$patient_transfer;}else{$table.="_____";}
				$table.='</td>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
			</tr>';
			}
			$table.='
			<tr>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Discharge Time:</b>&nbsp;';
					if($dischargeTime){$table.=$dischargeTime;}else{$table.="_____";}
				$table.='</td>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
			</tr>';
			
			$table.='
			<tr>
				<td colspan="2" class="bgcolor" style="width:700px;font-size:14px;font-weight:bold;">Surgery (OR)</td>
			</tr>
			<tr>
				<td colspan="2" style="width:700px;border-bottom:1px solid #C0C0C0;">
					<table  style="width:700px;" cellpadding="0" cellspacing="0">';
					$table.='
						<tr>
							<td style="width:230px;font-size:14px;border-bottom:1px solid #C0C0C0;padding-top:5px;"><b>In Room Time:</b> '.($surgeryTimeInOpRoom ? $surgeryTimeInOpRoom : "_____").'</td>
							<td style="width:230px;font-size:14px;border-bottom:1px solid #C0C0C0;padding-top:5px;"><b>Surgery Start Time:</b> '.($surgeryStartTimeOpRoom ? $surgeryStartTimeOpRoom : "_____").'</td>
							<td style="width:230px;font-size:14px;border-bottom:1px solid #C0C0C0;padding-top:5px;"><b>Surgery End Time:</b> '.($surgeryEndTimeOpRoom ? $surgeryEndTimeOpRoom : "_____").'</td>
						</tr>
						<tr>
							<td colspan="3" style="width:700px;font-size:14px;border-bottom:1px solid #C0C0C0;padding-top:5px;"><b>Out of Room:</b> '.($surgeryTimeOutOpRoom ? $surgeryTimeOutOpRoom : "_____").'</td>
						</tr>
					</table>
				</td>
			</tr>
			';
			//START CODE FOR NURSE POST OP CHECKLIST
			$postopNurseCheckListQry = "SELECT postOpNurseQuestionName,postOpNurseOption 
										FROM patient_postop_nurse_checklist 
										WHERE confirmation_id='".$_REQUEST['pConfId']."' ORDER BY `postOpNurseQuestionName`";
			$postopNurseCheckListRes = imw_query($postopNurseCheckListQry) or die(imw_error());
			$postopNurseCheckListNumRow = imw_num_rows($postopNurseCheckListRes);
			if($postopNurseCheckListNumRow>0) {
				$k=0;
				$table.='
				<tr>
					<td colspan="2" style="width:700px;">
						<table  style="width:100%;font-size:14px;" cellpadding="0" cellspacing="0">';
						$table.='
							<tr>
								<td class="bgcolor" style="width:300px;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">Nurse Post-Op Checklist</td>
								<td class="bgcolor" style="width:50px;text-align:right;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;padding-right:8px;">Yes/No</td>
								<td class="bgcolor" style="width:300px;border-bottom:1px solid #C0C0C0;padding-top:5px;">&nbsp;</td>
								<td class="bgcolor" style="width:50px;text-align:right;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;padding-right:8px;">Yes/No</td>
							</tr>
							<tr>';				
								while($postopNurseCheckListRow 	= imw_fetch_array($postopNurseCheckListRes)) {
									$k++;
									$postopNurseCheckListName 	= $postopNurseCheckListRow['postOpNurseQuestionName'];
									$postopNurseCheckListOption = $postopNurseCheckListRow['postOpNurseOption'];
									$table.='<td style="width:300px;border-bottom:1px solid #C0C0C0;padding-top:5px;">'.$postopNurseCheckListName.'</td>
											 <td style="width:50px;text-align:right;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0; padding-top:5px;padding-right:8px;">';
												if($postopNurseCheckListOption!=""){$table .= $postopNurseCheckListOption;}else{$table .= "____";}
									$table.='</td>';
									if(($k%2)!=0 && $k == $postopNurseCheckListNumRow) {
										//code for blank TD in the last
										$table.='<td style="width:300px;border-bottom:1px solid #C0C0C0;padding-top:5px;"></td><td style="width:50px;text-align:left;border-bottom:1px solid #C0C0C0; padding-top:5px;"></td>';		
									}
									if(($k%2)==0 && $k != $postopNurseCheckListNumRow) {
										$table.='</tr><tr>';		
									}
								}
					$table.='</tr>
						</table>
					</td>
				</tr>';
			}
			//END CODE FOR NURSE POST OP CHECKLIST
			
	$table.='<tr>
				<td colspan="2" style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Comments:</b>&nbsp;';
					if($recoveryComments){$table.=$recoveryComments;}else{$table.="________________";}
				$table.='</td>
			</tr>
			';

$qryQualityMeasures="SELECT qualityName,qualityStatus FROM qualitymeasures where  confirmation_id = '".$_REQUEST["pConfId"]."'";
$resQualityMeasures=imw_query($qryQualityMeasures);
$CheckNumRows=imw_num_rows($resQualityMeasures);
	$tdCtr=1;
	if($CheckNumRows>0){
		$table.='<tr>
					<td colspan="2" style="width:700px; ">
						<table style="width:700px; font-size:14px; border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">';
					$table.='<tr>
								<td style="width:300px;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">ASC Quality Control Measures</td>
								<td style="width:50px;text-align:center;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">Yes/No</td>
								<td style="width:300px;border-bottom:1px solid #C0C0C0;padding-top:5px;">&nbsp;</td>
								<td style="width:50px;text-align:center;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">Yes/No</td>
							</tr>
						<tr>';
		while($getRowsQualityMeasures=imw_fetch_array($resQualityMeasures)){
			$getQualityName=$getRowsQualityMeasures['qualityName'];
			$getQualityStatus=$getRowsQualityMeasures['qualityStatus'];
			$table.='<td style="width:300px;padding:3px;border-bottom:1px solid #C0C0C0;">'.$getQualityName.'</td>';
			$table.='<td style="width:50px;padding:3px;text-align:center;border-bottom:1px solid #C0C0C0;"><b>';
			if($getQualityStatus!=""){$table.=$getQualityStatus;}else{$table.="___"; }
			$table.='</b></td>';
			if($tdCtr%2==0){ $table.='</tr><tr>'; }
			$tdCtr++;
			
		}
		$table.='</tr></table>
			</td>
		</tr>';
	}
	$table.='
		<tr>
			<td style="padding-top:10px;"><b>Nurse Signature:&nbsp;</b>&nbsp;';
			if($NurseNameShow){$table.=$NurseNameShow;}else{$table.="_______";}	
			if($relivedNurseId!=0 && $relivedNurseId!=""){
				$relivednurseQry="select lname,fname from users where usersId=".$relivedNurseId."";
				$relivednurseRec= imw_query($relivednurseQry);
				$relivednurseRes=imw_fetch_array($relivednurseRec);
				$relivedNurseName= $relivednurseRes['lname'].', '.$relivednurseRes['fname'];		
			}
	$table.='</td>
			<td style="padding-top:10px;"><b>Relief Nurse Name:&nbsp;</b>&nbsp;';
			if($relivednurseRes['lname'] && $relivednurseRes['fname']){$table.=$relivedNurseName;}else{$table.="______";}			
	$table.='</td>	
		</tr>
		<tr>
			<td colspan="2"><b>Electronically Signed:&nbsp;</b>&nbsp;';
			if($signOnFileStatus){$table.=$signOnFileStatus;}else{$table.="____";}			
	$table.='</td>
		</tr>
		<tr>
			<td colspan="2"><b>Signature Date:&nbsp;</b>&nbsp;';
			if($signOnFileStatus && $signNursePostOpDateTime!='0000-00-00 00:00:00'){$table.=$objManageData->getFullDtTmFormat($signNursePostOpDateTime);}else{$table.="____";}			
	$table.='</td>
		</tr>';
	$table.='</table>';	

$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut = fputs(fopen('new_html2pdf/pdffile.html','w+'),$table);
fclose($fileOpen);
//$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';

if($postNurseFormStatus=='completed' || $postNurseFormStatus=='not completed') {
?>

<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
	</tr>
</table>	

<form name="printpost_op_nurse" action="new_html2pdf/createPdf.php?op=p" method="post">
	</form> 

<script language="javascript">
	function submitfn()
	{
		document.printpost_op_nurse.submit();
	}
</script>

<script type="text/javascript">
	submitfn();
</script>
<?php
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>	



