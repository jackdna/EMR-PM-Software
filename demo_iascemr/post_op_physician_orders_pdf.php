<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php"); 
$tablename = "postopphysicianorders";
//include("common/linkfile.php");
include("common_functions.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
extract($_GET);
include_once("new_header_print.php");
$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];
$patient_id = $_REQUEST['patient_id'];
if(!$patient_id) {
	$patient_id = $_SESSION['patient_id'];
}	

$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {
	$pConfId = $_SESSION['pConfId'];
}	

//UPDATING PATIENT STATUS IN STUB TABLE

 $patientdata=imw_query("select patientconfirmation.patientId,patientconfirmation.dos,
patientconfirmation.surgery_time,patient_data_tbl.patient_fname,patient_mname,
patient_data_tbl.patient_lname,patient_data_tbl.date_of_birth from patientconfirmation 
left join patient_data_tbl on patientconfirmation.ascId = patient_data_tbl.asc_id
where  patientconfirmation.patientConfirmationId='$pConfId'");
while($patient_data=imw_fetch_array($patientdata))
{
   $dos= $patient_data['dos'];
   $surgerytime=$patient_data['surgery_time'];
   $patient_fname=$patient_data['patient_fname'];
   $patient_mname=$patient_data['patient_mname'];
   $patient_lname=$patient_data['patient_lname'];
   $dob=$patient_data['date_of_birth'];
}

 $stub_data=imw_query("select * from stub_tbl where dos='$dos' && patient_first_name ='$patient_fname' && patient_middle_name='$patient_mname'
&& patient_last_name='$patient_lname' && patient_dob='$dob' && surgery_time='$surgerytime'");

while($stubtbl_data=imw_fetch_array($stub_data))
{
  $stub_id=$stubtbl_data['stub_id'];
} 

//GETTING CONFIRNATION DETAILS
	$getConfirmationDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if($getConfirmationDetails){
		extract($getConfirmationDetails);
	}
//GETTING CONFIRNATION DETAILS

// GETTING SURGEONS SIGN YES OR NO
	unset($conditionArr);
	$conditionArr['usersId'] = $surgeonId;
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if(count($surgeonsDetails)>0){
		foreach($surgeonsDetails as $usersDetail){
			$signatureOfSurgeon = $usersDetail->signature;
		}
	}
// GETTING SURGEONS SIGN YES OR NO

// GETTING NURSE SIGN YES OR NO
	unset($conditionArr);
	$conditionArr['usersId'] = $nurseId;
	$nurseDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if(count($nurseDetails)>0){
		foreach($nurseDetails as $usersDetail){
			$signatureOfNurse = $usersDetail->signature;
		}
	}
// GETTING NURSE SIGN YES OR NO

$query_rsNotes = "SELECT * FROM eposted WHERE table_name = 'postopphysicianorders' AND patient_conf_id = '$pConfId' ";
$rsNotes =imw_query($query_rsNotes);
$totalRows_rsNotes =imw_num_rows($rsNotes);

$table = 'patient2takehome';
$width = '250';
include("common/pre_defined_popup.php");

//GETTING POST OP IS OR NOT
	$getPostOpDetails = $objManageData->getExtractRecord('postopphysicianorders', 'patient_confirmation_id', $pConfId);
	if($getPostOpDetails){
		extract($getPostOpDetails);
		$postPhyFormStatus = $form_status;
		//$postOpPhysicianOrdersId;
	}
//GETTING POST OP IS OR NOT

//START GET LOCAL ANES POST-OP SIGNATURE
$localAnesQry = "SELECT signAnesthesia3Id,signAnesthesia3FirstName,signAnesthesia3MiddleName,signAnesthesia3LastName,signAnesthesia3Status,signAnesthesia3DateTime, date_format(signAnesthesia3DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia3DateTimeFormat FROM localanesthesiarecord WHERE confirmation_id = '".$pConfId."'";
$localAnesRes =imw_query($localAnesQry);
if(imw_num_rows($localAnesRes)>0) {
	$localAnesRow 					= imw_fetch_array($localAnesRes);	
	$signAnesthesia3Id 				= $localAnesRow["signAnesthesia3Id"];
	$signAnesthesia3FirstName 		= $localAnesRow["signAnesthesia3FirstName"];
	$signAnesthesia3MiddleName 		= $localAnesRow["signAnesthesia3MiddleName"];
	$signAnesthesia3LastName 		= $localAnesRow["signAnesthesia3LastName"];
	$signAnesthesia3Status 			= $localAnesRow["signAnesthesia3Status"];
	$signAnesthesia3DateTime 		= $localAnesRow["signAnesthesia3DateTime"];
	$signAnesthesia3DateTimeFormat 	= $localAnesRow["signAnesthesia3DateTimeFormat"];
	if($signAnesthesia3Id<>0 && $signAnesthesia3Id<>"") {
		$Anesthesia3SubType = getUserSubTypeFun($signAnesthesia3Id); //FROM common/commonFunctions.php
	}
	$Anesthesia3PreFix = 'Dr.';
	if($Anesthesia3SubType=='CRNA') {
		$Anesthesia3PreFix = '';
	}				
}
//END GET LOCAL ANES POST-OP SIGNATURE

	$condArr		=	array();
	$condArr['confirmation_id']	=	$pConfId ;
	$condArr['chartName']			=	'post_op_physician_order_form' ;
	$pOrderData		=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$condArr,'physician_order_name','ASC');
	$pOrderStatus		=	(is_array($pOrderData) && count($pOrderData) > 0 ) ? 1	: 0 ;		
	
$table_pdf.=$head_table;
$postphyOdrWidth = "220px;";
$postphyOdrWidths = "10px;";
$postphyOdrWidthz = "100px;";
if($version_num < 2) {
	$postphyOdrWidth = "190px;";
	$postphyOdrWidths = "40px;";
	$postphyOdrWidthz = "100px;";
}
$table_pdf.='
	<table style="width:680px;font-size:14px;padding-top:10px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="3" style="width:680px;text-align:center;font-weight:bold;font-size:16px; text-decoration:underline;padding:5px 0px 5px 0px;">Post-Op Physician Orders</td>
		</tr>
		<tr>
			<td colspan="3" style="width:680px;background:#C0C0C0;font-weight:bold;height:20px;padding-left:5px;">Post Op Orders</td>
		</tr>
		<tr>
			<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Discharge Patient when</td>
			<td style="width:50px;padding:5px;font-weight:bold;border-bottom:1px solid #C0C0C0;">Done</td>
			<td style="width:330px;padding:5px;border-bottom:1px solid #C0C0C0;border-left:1px solid #C0C0C0;">';
			if($patientToTakeHome && !$pOrderStatus )
			{
				$table_pdf.=	'<b>Physician Orders/Medications&nbsp;</b>'.$physician_order_date_time ;
			}
			else
			{
				$table_pdf.='
				<table style="width:330px;font-size:14px; " cellpadding="0" cellspacing="0">
					<tr>		
						<td style="width:'.$postphyOdrWidth.'"><b>Physician Orders/Medications&nbsp;</b>'.(($patientToTakeHome && $pOrderStatus) ? $physician_order_date_time : '' ).'</td>';
				if($version_num < 2) {	
					$table_pdf.='
						<td style="width:'.$postphyOdrWidths.'"><b>Time</b></td>';
				}
				$table_pdf.='
						<td style="width:'.$postphyOdrWidthz.'" style="padding:10px;"><b>Order Type</b></td>
					</tr>
				</table>';
			}
$table_pdf.='
			</td>
		</tr>
		<tr>
			<td colspan="2" style="width:350px; vertical-align:top; border-left:solid 1px #C0C0C0; border-right:1px solid #C0C0C0;">
				<table style="width:350px;font-size:14px; " cellpadding="0" cellspacing="0">
					<tr>
						<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Patient Assessed. Patient has recovered satisfactorily from sedation. This patient may be discharged after instructions given.</td>
						<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0;">';
				
						if($patientAssessed=='Yes'){
							$table_pdf.='Done';
						}else{
							$table_pdf.="____";
						}
						$table_pdf.='</td>
					</tr>
					
					<tr>
						<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Vital signs are stable</td>
						<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0; ">';
				
						if($vitalSignStable=='Yes'){
							$table_pdf.='Done';
						}else{
							$table_pdf.="____";
						}
						$table_pdf.='</td>
					</tr>
					<tr>
						<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Post-Op Evaluation Completed</td>
						<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0;">';
						if($postOpEvalDone=='Yes'){
							$table_pdf.='Done';
						}else{
							$table_pdf.="____";
						}
						$table_pdf.='</td>
					</tr>
				</table>
			</td>
			<td style="width:330px; vertical-align:top;">';
				//$table_pdf.=stripslashes($patientToTakeHome);
				$condArr		=	array();
				$condArr['confirmation_id']	=	$pConfId ;
				$condArr['chartName']			=	'post_op_physician_order_form' ;
				$pOrderData		=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$condArr,'physician_order_name','ASC');
				
				if($pOrderStatus )
				{
					$table_pdf.=	'<table style="width:100%; font-size:14px; " cellpadding="0" cellspacing="0">';
					foreach($pOrderData as $pOrderRow)
					{
						$time	=	($pOrderRow->physician_order_time <> '00:00:00') ? $objManageData->getTmFormat($pOrderRow->physician_order_time) : '' ;
						$table_pdf.=	'<tr>';
						$table_pdf.=	'<td style="width:'.$postphyOdrWidth.' padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities(stripslashes($pOrderRow->physician_order_name)).'</td>';
						if($version_num < 2) {
							$table_pdf.='<td style="width:'.$postphyOdrWidths.' padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.$time.'</td>';
						}
						$table_pdf.='<td style="width:'.$postphyOdrWidthz.' padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.ucwords($pOrderRow->physician_order_type).'</td>';
						$table_pdf.=	'</tr>';	
					}
					$table_pdf.=	'</table>';
				}
				elseif($patientToTakeHome)
				{
					$pOrderData	=	explode(',',str_replace("\n",",",$patientToTakeHome	));
					
					$table_pdf.=	'<table style="width:330px;font-size:14px;" cellpadding="0" cellspacing="0">';	
					foreach($pOrderData as $pOrderRow)
					{
						$table_pdf.=	'<tr>';
						$table_pdf.=	'<td style="width:'.$postphyOdrWidth.' padding:5px;border-bottom:1px solid #C0C0C0;">'.$pOrderRow.'</td>';
						if($version_num < 2) {
							$table_pdf.='<td style="width:130px;padding:5px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>';
						}
						$table_pdf.=	'</tr>';	
					}
					$table_pdf.=	'</table>';
				}
				
			$table_pdf.='</td>
		</tr>';		
		if($version_num > 2) {
		$Nurse1NameShow = stripslashes($signNurse1LastName.', '.$signNurse1FirstName);
		$table_pdf.='
		<tr>
			<td colspan="3" style="width:700px;border-top:1px solid #C0C0C0;padding-top:5px;">
				<table style="width:700px; font-size:14px;" cellpadding="0">
					
					<tr>
						<td style="width:250px;" valign="top"><b>Post-Op orders noted by nurse:&nbsp;</b>
						'.(($notedByNurse==1) ? 'Yes' : '____').'
						</td>
						<td style="width:250px;vertical-align:top;" ><b>Nurse:&nbsp;</b>
						'.(($signNurse1Status=="Yes") ? $Nurse1NameShow : '_________').'
						<br><b>Electronically Signed:&nbsp;</b>
						'.(($signNurse1Status=="Yes") ? $signNurse1Status : '_________').'
						<br><b>Signature Date:&nbsp;</b>
						'.(($signNurse1Status=="Yes") ? $objManageData->getFullDtTmFormat($signNurse1DateTime) : '_________').'
						</td>
						<td style="width:200px;vertical-align:top;">&nbsp;</td>
					</tr>
				</table>	
			</td>		
		</tr>';
		}
		$table_pdf.='
		<tr>
			<td colspan="3" style="width:680px;background:#C0C0C0;font-weight:bold;height:20px;padding-left:5px;">Post Op Instruction Given</td>
		</tr>
		<tr>
			<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Written</td>
			<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0;">';
			 if($postOpInstructionMethodWritten=='Yes'){
				$table_pdf.='Done';
			}else{
				$table_pdf.="____";
			}
			$table_pdf.='</td>
			<td style="width:330px;padding:5px;padding-bottom:5px;border-bottom:1px solid #C0C0C0;border-left:1px solid #C0C0C0;"><b>Time:&nbsp;</b>';
			if(trim($postOpPhyTime)){
				$table_pdf.=$objManageData->getTmFormat(stripslashes($postOpPhyTime));
			}else{
				$table_pdf.="____";
			}
			$table_pdf.='</td>
		</tr>
		<tr>
			<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Verbal</td>
			<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0;">';
			if($postOpInstructionMethodVerbal=='Yes'){
				$table_pdf.='Done';
			}else{
				$table_pdf.="____";
			}
			$table_pdf.='</td>
			<td style="width:330px;padding:5px;padding-bottom:5px;border-bottom:1px solid #C0C0C0;border-left:1px solid #C0C0C0;">&nbsp;</td>
		</tr>
		<tr>
			<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Patient safely discharged from the center</td>
			<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0;">';
			if($patientAccompaniedSafely=='Yes'){
				$table_pdf.='Done';
			}else{
				$table_pdf.="____";
			}
			$table_pdf.='</td>
			<td style="width:330px;padding:5px;padding-bottom:5px;border-bottom:1px solid #C0C0C0;border-left:1px solid #C0C0C0;">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;"><b>Comments:&nbsp;</b>';
			if(trim($comment)){
				$table_pdf.=stripslashes($comment);
			}else{
				$table_pdf.="__________________";
			}
			
			$table_pdf.='</td>
		</tr>
		<tr>
			<td colspan="3" style="padding-top:10px; padding-left:5px; width:700px;">
				<table style="width:700px; font-size:14px;" cellpadding="0">
					<tr>
						<td style="width:250px;"><b>Surgeon:&nbsp;</b>';
							$signSurgeon="_________";
							if($signSurgeon1Status!='' && $signSurgeon1Status=="Yes"){
								$signSurgeon1Name= "Dr. ".$signSurgeon1LastName.','.$signSurgeon1FirstName;
								$table_pdf.=$signSurgeon1Name;
								$signSurgeon=$signSurgeon1Status;
							}else{
								$table_pdf.="_________";
							}
							$table_pdf.='<br><b>Electronically Signed:&nbsp;</b>'.$signSurgeon;
							$table_pdf.='<br><b>Signature Date:&nbsp;</b>';
							if($signSurgeon1DateTime!='' && $signSurgeon1Status=="Yes"){
								$table_pdf.=$objManageData->getFullDtTmFormat($signSurgeon1DateTime);
							}else{
								$table_pdf.="_________";
							}
						$table_pdf.='
						</td>
						<td style="width:250px;"><b>Nurse:&nbsp;</b>'; 
							$signNurse="_________";
							if($signNurseStatus=="Yes"){
								$signNurseName=$signNurseLastName.','.$signNurseFirstName;
								$table_pdf.=$signNurseName;
								$signNurse=$signNurseStatus;
							}else{
								$table_pdf.="_________";
							}
							$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>".$signNurse;
							
							$table_pdf.='<br><b>Signature Date:&nbsp;</b>';
							if($signNurseDateTime!='' && $signNurseStatus=="Yes"){
								$table_pdf.=$objManageData->getFullDtTmFormat($signNurseDateTime);
							}else{
								$table_pdf.="_________";
							}
				$table_pdf.='</td>
						<td style="width:200px; vertical-align:top;"><b>Relief Nurse:&nbsp;</b>'; 
							if($relivednurse!=''){
								$qry=imw_query("select lname,fname from users where usersId=$relivednurse");
								$res=imw_fetch_array($qry);
								$relivednursename=$res['lname'].','.$res['fname'];
								$table_pdf.=$relivednursename;
							}else{
								$table_pdf.="_________";
							}
			$table_pdf.='
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="padding-top:10px; padding-left:5px; width:700px;">
				<table style="width:700px; font-size:14px;" cellpadding="0">
					<tr>
						<td style="width:250px;">';
							if($signAnesthesia3Status=="Yes"){
								$table_pdf.="<b>Anesthesia Provider  :&nbsp;</b>". " ".$Anesthesia3PreFix." ". $signAnesthesia3LastName.", ".$signAnesthesia3FirstName." ".$signAnesthesia3MiddleName;
								$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>Yes";
								$table_pdf.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signAnesthesia3DateTime);
							}else {
								$table_pdf.="<b>Anesthesia Provider :&nbsp;</b>________";
								$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>________";
								$table_pdf.="<br><b>Signature Date:&nbsp;</b>________";
							}
						$table_pdf.='
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
';
//die($table_pdf);
//$table_pdf.='</table>';
$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut = fputs($fileOpen,$table_pdf);
fclose($fileOpen);
$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';

if($postPhyFormStatus=='completed' || $postPhyFormStatus=='not completed'){
?>	

 <form name="printpost_op_phy" action="new_html2pdf/createPdf.php?op=p" method="post">
 </form>

<script language="javascript">
	function submitfn()
	{
		document.printpost_op_phy.submit();
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