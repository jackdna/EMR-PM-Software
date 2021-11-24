<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
set_time_limit(500);
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
include_once("common/commonFunctions.php"); 
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" and st.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>

<!--RESPONSIVE CSS AND JAVASCRIPT-->
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/font-awesome.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />


<script type="text/javascript" src="js/jquery-1.11.3.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/alert_file.js"></script>

<style>
	form{margin:0px;}
	a.black:hover{color:"Red"; text-decoration:none;}
	.col { display:block;  background:white;}
</style>


<script type="text/javascript">
window.focus();
function selAll(objMain,cntr){
	
	var obj,id='';
	for(var i = 1;i<=cntr;i++){
		id=	'chkbxMed'+i;
		obj = document.getElementById(id);
		if(obj) {
			if(objMain.checked==true && !obj.hasAttribute('disabled') ){
				obj.checked=true;
			}else if(objMain.checked==false && !obj.hasAttribute('disabled') ){
				obj.checked=false;
			}
			isChecked(obj,i);
		}
	}
}

function isChecked(obj,cnt){
	var id	=	'patientConfirmationId'+cnt;
	var elm	=	document.getElementById(id);
	
	if(elm)
	{
		if(obj.checked == true) {
			elm.removeAttribute('disabled');
		}else {
			elm.setAttribute('disabled','disabled');	
		}
	}
}

function buttonClick(){
	var isAnyChk	= false ;
	var obj				=	document.getElementsByName('chkbxMed[]');
	var objLen		=	obj.length ;
	if(objLen > 0 )
	{ 	
		for(var i=0; i<objLen; i++)
		{
				if(obj[i].checked === true) 
					isAnyChk = true ;
		}
		
		if(isAnyChk) {
			$('#myModal .modal-body>p').addClass('text-center');
			$('#myModal .modal-body>p').html("Are you sure to sign selected pre-op orders.");
			$("#missing_feilds").addClass('hidden');
			$("#cancel_yes").removeClass('hidden');
			$("#cancel_no").removeClass('hidden');
			$('#myModal').modal('show');
			
		}else {
			$('#myModal .modal-body>p').removeClass('text-center');
			$('#myModal .modal-body>p').html('Please select record(s)');
			$("#missing_feilds").removeClass('hidden');
			$("#cancel_yes").addClass('hidden');
			$("#cancel_no").addClass('hidden');
			$('#myModal').modal('show');
			
		}
	}
}


function pre_op_get_height(){
	
	var Wih	= $(window).height();
	var Tih		= $('#height-adj-pre-op').find('.table_head_sch').outerHeight(true);
	var Buh	= $('#height-adj-pre-op').find('.btn-footer-slider').outerHeight(true);
	var CpH	= $('#height-adj-pre-op').find('#captionHeader').outerHeight(true);
	var Ch		=	Wih - (Tih + Buh);
	var Th		=	(Ch - CpH ) / 2;
	var Bh		=	Th - 45;
	
	$("#dataContainer").css({'height': Ch+'px','min-height': Ch+'px','max-height': Ch+'px','overflow':'hidden'});
	$("#top,#bottom").css({'height': Th+'px','min-height': Th+'px','max-height': Th+'px','overflow':'hidden'});
	$("#tData,#bData").css({'height': Bh+'px','min-height': Bh+'px','max-height': Bh+'px', 'overflow':'auto'});
	
	$(".loader").fadeOut(100);	
}

$(window).load(function(e) {
	pre_op_get_height();
});

$(window).resize(function(){
	pre_op_get_height();
});
</script>	
</head>

<body id='signall_pre_op_body'>
	<div class="alert alert-success alert-msg" id="alert_success" > <strong>Record(s) Saved Successfully</strong> </div>
	
	<div class="loader"  style="top:40%; ">
		<span><b class='fa fa-spinner fa-pulse' ></b>&nbsp;Loading...</span>
	</div>
<?php
$response	=	'';
$dos			= $_REQUEST["dos"];
$selected_date = date("Y-m-d");
if($dos) {
	list($mm,$dd,$yy) = explode("-",$dos);
	$selected_date = $yy."-".$mm."-".$dd;
}
		
//SAVE USER SIGNATURE
	$loggedInUserId = $_GET['loggedInUserId'];
	if(!$loggedInUserId) {
		$loggedInUserId = $_SESSION["loginUserId"];
	}
	$patient_id	=	$_REQUEST['patient_id'];
	$pConfId 	=	$_REQUEST['pConfId'];
	
//GET USER NAME
	$ViewUserNameQry = "select * from `users` where  usersId = '".$loggedInUserId."'";
	$ViewUserNameRes = imw_query($ViewUserNameQry) or die($ViewUserNameQry.imw_error()); 
	$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
	
	$loggedInUserFirstName = $ViewUserNameRow["fname"];
	$loggedInUserMiddleName = $ViewUserNameRow["mname"];
	$loggedInUserLastName = $ViewUserNameRow["lname"];
	
	$signOnFileStatus = 'Yes';
	$signDateTime = date("Y-m-d H:i:s");
	
	$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
//END GET USER  NAME

//GET CONFIRMATION DETAIL												
	$laserSignCatIdArr = array();
	$getConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE  dos='".$selected_date."'";
	$getConfirmationDetailRes = imw_query($getConfirmationDetailQry) or die($getConfirmationDetailQry.imw_error()); 
	$getConfirmationDetailNumRow = imw_num_rows($getConfirmationDetailRes);
	if($getConfirmationDetailNumRow>0) {
		while($getConfirmationDetailRow = imw_fetch_array($getConfirmationDetailRes)) { 
			$ptConfId = $getConfirmationDetailRow["patientConfirmationId"]; 
			$patient_primary_procedure_id = $getConfirmationDetailRow["patient_primary_procedure_id"]; 
			$getLaserCatIdDetailQry = "SELECT * FROM `procedures` WHERE procedureId='".$patient_primary_procedure_id."'  AND procedureId!='0'";
			$getLaserCatIdDetailRes = imw_query($getLaserCatIdDetailQry) or die($getLaserCatIdDetailQry.imw_error()); 
			$getLaserCatIdDetailNumRow = imw_num_rows($getLaserCatIdDetailRes);
			if($getLaserCatIdDetailNumRow>0) {
				$getLaserCatIdDetailRow = imw_fetch_array($getLaserCatIdDetailRes);
				$laserSignCatId = $getLaserCatIdDetailRow['catId'];
				$laserSignCatIdArr[$ptConfId] = $laserSignCatId;
			}
		}
	}
	//echo '<pre>'; print_r($laserSignCatIdArr) ; exit; 
	
//END GET CONFIRMATION DETAIL


if($_REQUEST['submitMe'])
{
	
	//CODE TO MAKE SIGNATURES OF SURGEON IN PRE-OP ORDER CHART NOTES OF PATIENT(IN OPENED DOS)
	$laserConfIdArr = $phyConfIdArr = array();
	$patientConfirmationIdArr = $_REQUEST["patientConfirmationIdArr"];
	$checkBoxArr		=	$_REQUEST['chkbxMed'] ;
	
	//print_r($patientConfirmationIdArr); exit;
	foreach($patientConfirmationIdArr as $key=>$patientConfirmationIdVal) {
		if($checkBoxArr[$key])
		{
			$catId = $laserSignCatIdArr[$patientConfirmationIdVal];
			if($catId==2) {
				$laserConfIdArr[] 	= 	$patientConfirmationIdVal;
			}else {
				$phyConfIdArr[] 	= 	$patientConfirmationIdVal;	
			}
		}
	}
	
	//echo '<pre>'; print_r($checkBoxArr); print_r($patientConfirmationIdArr) ; print_r($laserConfIdArr); print_r($phyConfIdArr); exit; 
	
	$laserConfId	=	implode(",",$laserConfIdArr);
	$phyConfId	=	implode(",",$phyConfIdArr);
	
	if(!trim($phyConfId)) 	{   $phyConfId 	= 0;	 }
	if(!trim($laserConfId)) {   $laserConfId 	= 0;	 }
	
	$SaveSignArr = array('preopphysicianorders','postopphysicianorders','laser_procedure_patient_table');
	$affectedRows	=	0 ;
	foreach($SaveSignArr as $SaveSignArrTableName)
	{
		$signId	=	($SaveSignArrTableName == 'preopphysicianorders' || $SaveSignArrTableName == 'postopphysicianorders') ? '1' : '';
		
		$signUserId 					=	'signNurse'.$signId.'Id';
		$signUserFirstName 		=	'signNurse'.$signId.'FirstName'; 
		$signUserMiddleName		=	'signNurse'.$signId.'MiddleName';
		$signUserLastName			=	'signNurse'.$signId.'LastName'; 
		$signUserStatus				=	'signNurse'.$signId.'Status';
		$signUserDateTime			=	'signNurse'.$signId.'DateTime';
		$signUserconfirmation_id=	'patient_confirmation_id';
		
		if($SaveSignArrTableName == "laser_procedure_patient_table") {
			$signUserconfirmation_id = 'confirmation_id';
		}
		
		$inFields	=	$phyConfId ;
		if($SaveSignArrTableName=='laser_procedure_patient_table') {
			$inFields = $laserConfId;
		}
		$andQry = " AND $signUserconfirmation_id IN(".$inFields.")";
		
		$ordersNotedQry	=	'';
		if($SaveSignArrTableName == 'preopphysicianorders' || $SaveSignArrTableName == 'postopphysicianorders') {
			$ordersNotedQry	=	', notedByNurse = 1 ';
		}
		
		$SaveSignQry = "update $SaveSignArrTableName set 
										$signUserFirstName = IF($signUserId = 0, '".addslashes($loggedInUserFirstName)."',$signUserFirstName), 
										$signUserMiddleName = IF($signUserId = 0, '".addslashes($loggedInUserMiddleName)."',$signUserMiddleName),
										$signUserLastName = IF($signUserId = 0, '".addslashes($loggedInUserLastName)."',$signUserLastName), 
										$signUserStatus = IF($signUserId = 0, '$signOnFileStatus',$signUserStatus),
										$signUserDateTime = IF($signUserId = 0, '$signDateTime',$signUserDateTime),
										$signUserId = IF($signUserId = 0, '$loggedInUserId',$signUserId)
										$ordersNotedQry
										WHERE (form_status = 'not completed' OR form_status = 'completed')
													" . $andQry;;
			
		$SaveSignRes = imw_query($SaveSignQry) or die('Error occured at line no.'.(__LINE__).$SaveSignQry.imw_error());
		$affectedRows		=	$affectedRows + imw_affected_rows();
		//echo 'Rows Affected : '.$affectedRows .'<br>'	;
		
		
		if( $inFields <> 0 )
		{
			//SET FORM STATUS TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
			$tableFormStatusQry = "SELECT * FROM $SaveSignArrTableName WHERE ".str_replace('AND','',$andQry) ;
			$tableFormStatusRes = imw_query($tableFormStatusQry) or die($tableFormStatusQry.imw_error());
			
			if(imw_num_rows($tableFormStatusRes)>0) {
				while($tableFormStatusRow = imw_fetch_array($tableFormStatusRes))
				{
					$confirmationId		=	'';
					$confirmationId		=	$tableFormStatusRow[$signUserconfirmation_id];
					
					{
						//update form status 
						$tableFormStatus	= 'completed';
						$laserSignCatId		=	$laserSignCatIdArr[$confirmationId];
						
						if($SaveSignArrTableName == "preopphysicianorders") 
						{		
								$leftNaviFieldName = "pre_op_physician_order_form";
								$version_num	=	$tableFormStatusRow['version_num'];
								
								$signNurseIdPreOpPhy = $tableFormStatusRow['signNurseId'];
								$signNurse1IdPreOpPhy = $tableFormStatusRow['signNurse1Id'];
								$signSurgeon1IdPreOpPhy = $tableFormStatusRow['signSurgeon1Id'];
								$notedByNurse = $tableFormStatusRow['notedByNurse'];
								
								if($signSurgeon1IdPreOpPhy=="0" || $signNurse1IdPreOpPhy == "0" || $notedByNurse==0){	
									$tableFormStatus = 'not completed';
								}
								
								// Start Validate chart if form is on version no. 2
								if($version_num == 1 && $tableFormStatus == 'completed' && $signNurseIdPreOpPhy == "0")
								{
									$tableFormStatus = 'not completed';
								}
								// End Validate chart if form is on version no. 2 
						}
						else if($SaveSignArrTableName == "postopphysicianorders") 
						{		
					
								$leftNaviFieldName 				= 	"post_op_physician_order_form";
								$post_op_version_num	=	$tableFormStatusRow['version_num'];
								
								$chbx_pa		=	$tableFormStatusRow['patientAssessed'];
								$chbx_vs		=	$tableFormStatusRow['vitalSignStable'];
								$chbx_ec		=	$tableFormStatusRow['postOpEvalDone'];
								$chbx_wr		=	$tableFormStatusRow['postOpInstructionMethodWritten'];
								$chbx_vbl		=	$tableFormStatusRow['postOpInstructionMethodVerbal'];
								$chbx_waar	=	$tableFormStatusRow['patientAccompaniedSafely'];
								$chk_signNurseId		=	$tableFormStatusRow['signNurseId'];
								$chk_signNurse1Id		=	$tableFormStatusRow['signNurse1Id'];
								$chk_signSurgeon1Id	=	$tableFormStatusRow['signSurgeon1Id'];
								$postOpDropExist		=	$_POST['postOpDropExist_'.$confirmationId];
								$postOpNotedByNurse	=	$tableFormStatusRow['notedByNurse'];
								
								if(		($chbx_pa <> 'Yes')
										||($chbx_vs <> 'Yes')
										||($chbx_ec <> 'Yes')
										||($chbx_wr <> 'Yes') 
										||($chbx_vbl <> 'Yes') 
										||($chbx_waar <> 'Yes') 
										||($chk_signNurseId=="0") 
										||($chk_signSurgeon1Id=="0")
										||($chk_signNurse1Id == "0" && $post_op_version_num >2)
										||($postOpNotedByNurse == "0" && $post_op_version_num >2)
										||($postOpDropExist != 'Yes')
								){
										$tableFormStatus = 'not completed';
								}
						
						}
						else if($SaveSignArrTableName == "laser_procedure_patient_table" && $laserSignCatId=='2') 
						{		
					
								$leftNaviFieldName 				= 	"laser_procedure_form";
								
								$chk_laser_chief_complaint 	= 	$tableFormStatusRow['chk_laser_chief_complaint'];
								$laser_chief_complaint			= 	$tableFormStatusRow['laser_chief_complaint'];
								
								$chk_laser_past_med_hx 		= 	$tableFormStatusRow['chk_laser_past_med_hx'];
								$laser_past_med_hx				= 	$tableFormStatusRow['laser_past_med_hx'];
								
								$chk_laser_present_illness_hx=	$tableFormStatusRow['chk_laser_present_illness_hx'];
								$laser_present_illness_hx		=	$tableFormStatusRow['laser_present_illness_hx'];
								
								$chk_laser_medication 		= 	$tableFormStatusRow['chk_laser_medication'];
								$laser_medication				=	$tableFormStatusRow['laser_medication'];		
								
								$allergies_status_reviewed	=	$tableFormStatusRow['allergies_status_reviewed'];
								
								$verified_nurse_name 			=	$tableFormStatusRow['verified_nurse_name'];	
								$verified_surgeon_Name 		=	$tableFormStatusRow['verified_surgeon_Name'];		 			  
								
								$best_correction_vision_R	=	$tableFormStatusRow['best_correction_vision_R'];
								$best_correction_vision_L		=	$tableFormStatusRow['best_correction_vision_L'];
								$laser_sle								=	$tableFormStatusRow['laser_sle'];
								$laser_mental_state				=	$tableFormStatusRow['laser_mental_state'];
								$pre_laser_IOP_R					=	$tableFormStatusRow['pre_laser_IOP_R'];
								$pre_laser_IOP_L					=	$tableFormStatusRow['pre_laser_IOP_L'];
								$pre_iop_na							=	$tableFormStatusRow['pre_iop_na'];
								$laser_fundus_exam				=	$tableFormStatusRow['laser_fundus_exam'];
								$laser_comments					=	$tableFormStatusRow['laser_comments'];
								$laser_other							=	$tableFormStatusRow['laser_other'];
								
								
								$chk_laser_pre_op_diagnosis=	$tableFormStatusRow['chk_laser_pre_op_diagnosis'];
								$pre_op_diagnosis				=	$tableFormStatusRow['pre_op_diagnosis'];
								$laser_other_pre_medication=	$tableFormStatusRow['laser_other_pre_medication'];
								
								$prelaserVitalSignBP			=	$tableFormStatusRow['prelaserVitalSignBP'];
								$prelaserVitalSignP				=	$tableFormStatusRow['prelaserVitalSignP'];
								$prelaserVitalSignR				=	$tableFormStatusRow['prelaserVitalSignR'];
								$laser_spot_duration			=	$tableFormStatusRow['laser_spot_duration'];
								$laser_spot_size					=	$tableFormStatusRow['laser_spot_size'];
								$laser_power						=	$tableFormStatusRow['laser_power'];
								$laser_shots							=	$tableFormStatusRow['laser_shots'];
								$laser_total_energy				=	$tableFormStatusRow['laser_total_energy'];
								$laser_degree_of_opening	=	$tableFormStatusRow['laser_degree_of_opening'];
								$laser_exposure					=	$tableFormStatusRow['laser_exposure'];
								$laser_count							=	$tableFormStatusRow['laser_count'];
								$postlaserVitalSignBP			=	$tableFormStatusRow['postlaserVitalSignBP'];
								$postlaserVitalSignP			=	$tableFormStatusRow['postlaserVitalSignP'];
								$postlaserVitalSignR			=	$tableFormStatusRow['postlaserVitalSignR'];
								$post_op_operative_comment	=	$tableFormStatusRow['post_op_operative_comment'];
								$laser_post_progress			=	$tableFormStatusRow['laser_post_progress'];
								$laser_post_operative			=	$tableFormStatusRow['laser_post_operative'];
								$iop_pressure_l					=	$tableFormStatusRow['iop_pressure_l'];//
								$iop_pressure_r					=	$tableFormStatusRow['iop_pressure_r'];//
								
								$iop_na								=	$tableFormStatusRow['iop_na'];			
								
								$signNurseIdlaser_procedure 	= 	$tableFormStatusRow['signNurseId'];
								$signSurgeon1Idlaser_procedure 	= 	$tableFormStatusRow['signSurgeon1Id'];
					
								if(  ( ($laser_chief_complaint  == '') && ($chk_laser_chief_complaint	== 'on') &&
									   ($laser_past_med_hx  == '') && ($chk_laser_past_med_hx		== 'on') &&
									   ($laser_present_illness_hx 	== '') && ($chk_laser_present_illness_hx== 'on') &&
									   ($laser_medication  == '') && ($chk_laser_medication		== 'on')
									 ) ||
									 ($verified_nurse_name		== ''  || $verified_surgeon_Name		== '' )  || 
									 ( ($pre_op_diagnosis		== '') && ($chk_laser_pre_op_diagnosis	== 'on'))||
									 (  ($prelaserVitalSignBP 	== '') && ($prelaserVitalSignP 			== '' )  &&
										($prelaserVitalSignR 	== '') && ($laser_spot_duration 		== '')   &&
										($laser_spot_size 			== '') && ($laser_power 				== '' )  &&
										($laser_shots 				== '') && ($laser_total_energy 			== '')   &&
										($laser_degree_of_opening 	== '') && ($laser_exposure 				== '' )  &&
										($laser_count 				== '') && ($postlaserVitalSignBP 		== '')   &&
										($postlaserVitalSignP 	== '') && ($postlaserVitalSignR 		== '')   &&
										($laser_post_progress 	== '') 
									 ) ||
									 (($pre_laser_IOP_R 			== '') && ($pre_laser_IOP_L 			== '' )  && ($pre_iop_na == '' )) ||
									 (($iop_pressure_l 				== '') && ($iop_pressure_r 				== '')   && ($iop_na 	 == '')) ||
									 ($signNurseIdlaser_procedure	=="0") || ($signSurgeon1Idlaser_procedure=="0")
								  )
								{
										$tableFormStatus = 'not completed';
								}
						}
						
						if($SaveSignArrTableName == "laser_procedure_patient_table" && $laserSignCatId <> '2') {
							//DO NOT UPDATE FORM STATUS FOR Laser Chartnote if procedure is not laser
						}else if($laserSignCatId=='2' && ($SaveSignArrTableName == "preopphysicianorders" || $SaveSignArrTableName == "postopphysicianorders")) {
							//DO NOT UPDATE FORM STATUS FOR preopphysicianorders  Chartnote if procedure is laser
						}else{	
							$updateTableFormStatusQry = "update $SaveSignArrTableName SET form_status='$tableFormStatus' WHERE $signUserconfirmation_id='".$tableFormStatusRow[$signUserconfirmation_id]."' ";
							imw_query($updateTableFormStatusQry) or die('Error occured at line no. '.(__LINE__).': '.imw_error());
							
						}
						
						//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
						$chartSignedBySurgeon 	= chkSurgeonSignNew($tableFormStatusRow[$signUserconfirmation_id]);
						$updateStubTblQry 		= "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$tableFormStatusRow[$signUserconfirmation_id]."'";
						$updateStubTblRes 		= imw_query($updateStubTblQry) or die($updateStubTblQry.imw_error());
						//END CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
					
					
						//CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
						$chartSignedByAnes 		= chkAnesSignNew($tableFormStatusRow[$signUserconfirmation_id]);
						$updateAnesStubTblQry 	= "UPDATE stub_tbl SET chartSignedByAnes='".$chartSignedByAnes."' WHERE patient_confirmation_id='".$tableFormStatusRow[$signUserconfirmation_id]."'";
						$updateAnesStubTblRes 	= imw_query($updateAnesStubTblQry) or die($updateAnesStubTblQry.imw_error());
						//END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
						
						
						//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
						$chartSignedByNurse 	= chkNurseSignNew($tableFormStatusRow[$signUserconfirmation_id]);
						$updateNurseStubTblQry 	= "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$tableFormStatusRow[$signUserconfirmation_id]."'";
						$updateNurseStubTblRes 	= imw_query($updateNurseStubTblQry) or die($updateNurseStubTblQry.imw_error());
						//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE	
						
					}
					
					
				
				}
			}
			
			//END SET FORM STATUS(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
		}
	
	}
	
	$response	=	$affectedRows." Records(s) Updated" ;
	
}
//END CODE TO MAKE SIGNATURES OF SURGEON IN PRE-OP ORDER CHART NOTES OF PATIENT(IN OPENED DOS)	


//START CODE TO GET RECORDS PRE / Post Op Orders To be Noted By Nurse
$stubNumRow = 0;
$stubRowArr = $pendingStubRowArr = $patient_confirmation_id_arr =  $patient_laser_confirmation_id_arr = array();
$patient_confirmation_id_implode =  0;
$patient_laser_confirmation_id_implode = 0;
if( $_SESSION['loginUserType'] == 'Nurse' ) 
{
		$stubQry = "SELECT TRIM(CONCAT(st.patient_last_name,', ',st.patient_first_name,' ',st.patient_middle_name)) as patient_name , 
									DATE_FORMAT(st.patient_dob,'%m/%d/%Y') as dob, st.patient_primary_procedure, st.patient_confirmation_id,  
									pc.patient_primary_procedure_id, pc.patient_secondary_procedure_id, pc.patient_tertiary_procedure_id,
									pr.form_status as pre_op_phy_form_status, pr.preOpPhysicianOrdersId, 
									pr.signSurgeon1Id AS preOpPhySignSurgeonId,  pr.signNurse1Id AS preOpPhySignNurse1Id, pr.notedByNurse as preOpOrdersNoted,
									ps.form_status as post_op_phy_form_status, ps.postOpPhysicianOrdersId, ps.notedByNurse as postOpOrdersNoted, ps.version_num as postOpPhysicianVersionNum, 
									ps.signSurgeon1Id AS postOpPhySignSurgeonId,  ps.signNurse1Id AS postOpPhySignNurse1Id,
									lp.signSurgeon1Id AS laserProcedureSignSurgeonId,  lp.signNurseId AS laserProcedureSignNurseId,
									lp.sign_all_pre_op_order_status, lp.sign_all_post_op_order_status, 
									pcr.catId as procedureCatId 
								FROM stub_tbl st 
								LEFT JOIN patientconfirmation pc ON (pc.patientConfirmationId = st.patient_confirmation_id)
								LEFT JOIN preopphysicianorders pr ON (pr.patient_confirmation_id = st.patient_confirmation_id AND pr.patient_confirmation_id !='0')
								LEFT JOIN postopphysicianorders ps ON (ps.patient_confirmation_id = st.patient_confirmation_id AND ps.patient_confirmation_id !='0')
								LEFT JOIN laser_procedure_patient_table lp ON (lp.confirmation_id = st.patient_confirmation_id AND pr.patient_confirmation_id !='0')
								LEFT JOIN procedures pcr ON (pc.patient_primary_procedure_id = pcr.procedureId ) 
								WHERE st.dos = '".$selected_date."' 
											AND st.patient_status != 'Canceled' 
											AND pc.prim_proc_is_misc = '' 
											AND 
											(
												(pr.signSurgeon1Id > 0 AND ps.signSurgeon1Id > 0 AND pr.form_status <> '' AND ps.form_status <> '') 
												 OR
												(lp.signSurgeon1Id > 0 AND lp.form_status <> '' AND pcr.catId = '2' )
											) 
											$fac_con
								GROUP BY st.stub_id 
								ORDER BY st.surgery_time, st.surgeon_fname";
		//echo $stubQry; 
	
		$stubRes = imw_query($stubQry) or die(imw_error());
		$stubNumRow = imw_num_rows($stubRes);
		
		if( $stubNumRow > 0 ) 
		{ 
			while($stubRow=imw_fetch_array($stubRes)) 
			{
				$preOpPhySignSurgeonId 			= $stubRow["preOpPhySignSurgeonId"];
				$postOpPhySignSurgeonId			= $stubRow["postOpPhySignSurgeonId"];
				$laserProcSignSurgeonId			= $stubRow["laserProcedureSignSurgeonId"];
				$sign_all_pre_op_order_status	= $stubRow["sign_all_pre_op_order_status"];
				$sign_all_post_op_order_status	= $stubRow["sign_all_post_op_order_status"];
				
				$preOpPhySignNurse1Id 			= $stubRow["preOpPhySignNurse1Id"];
				$postOpPhySignNurse1Id			= $stubRow["postOpPhySignNurse1Id"];
				$laserProcSignNurseId			= $stubRow["laserProcedureSignNurseId"];
				
				$procedureCatId					= $stubRow["procedureCatId"];
				$preOpOrdersNoted				= $stubRow['preOpOrdersNoted'];
				$postOpOrdersNoted				= $stubRow['postOpOrdersNoted'];
				$preOpPhysicianVersionNum		= $stubRow["preOpPhysicianVersionNum"];
				$postOpPhysicianVersionNum		= $stubRow["postOpPhysicianVersionNum"];
				
				
				if(($preOpPhySignSurgeonId > 0 && $postOpPhySignSurgeonId > 0) || ($procedureCatId == '2' && $laserProcSignSurgeonId > 0 && $sign_all_pre_op_order_status== '1' && $sign_all_post_op_order_status== '1'))
				{
					if( ($preOpPhySignNurse1Id == 0 || $preOpOrdersNoted == 0 || (($postOpPhySignNurse1Id == 0 || $postOpOrdersNoted == 0) && $postOpPhysicianVersionNum > 2) ) && $procedureCatId <> '2'   )
					{
						$pendingStubRowArr[] = $stubRow;
					}
					elseif( $laserProcSignNurseId == 0 && $procedureCatId == '2'   )
					{
						$pendingStubRowArr[] = $stubRow;
					}
					else
					{
						$stubRowArr[] = $stubRow;
					}
				
					
					if(!in_array($stubRow['patient_confirmation_id'],$patient_confirmation_id_arr) && $stubRow['patient_confirmation_id']!='0') 
					{
						$patient_confirmation_id_arr[] = $stubRow['patient_confirmation_id'];
						if($procedureCatId=='2') {
							$patient_laser_confirmation_id_arr[] = $stubRow['patient_confirmation_id'];	
						}
					}
				
				}
				
			}
			
			if(count($patient_confirmation_id_arr)>0) {
				$patient_confirmation_id_implode = implode(",",$patient_confirmation_id_arr);
			}
			if(count($patient_laser_confirmation_id_arr)>0) {
				$patient_laser_confirmation_id_implode = implode(",",$patient_laser_confirmation_id_arr);
			}
			
		}
		
		$medicationNameArr = $strengthArr = $directionArr = $postMedicationNameArr =  array();
		if($patient_confirmation_id_implode!=0) 
		{
				
				// Start Getting Pre Op Medications
				$medicationQry = "SELECT medicationName, strength, direction, patient_confirmation_id, sourcePage FROM patientpreopmedication_tbl WHERE patient_confirmation_id IN(".$patient_confirmation_id_implode.")";	
		
				$medicationRes = imw_query($medicationQry) or die(imw_error());
			
				if(imw_num_rows($medicationRes)>0) 
				{
					while($medicationRow = imw_fetch_array($medicationRes)) 
					{
						$pConfIdMed	=	$medicationRow["patient_confirmation_id"];
						$source			=	$medicationRow["sourcePage"];
						$medicationNameArr[$pConfIdMed.'_S'.$source][]  =	$medicationRow["medicationName"];
						$strengthArr[$pConfIdMed.'_S'.$source][] 				=	$medicationRow["strength"];
						$directionArr[$pConfIdMed.'_S'.$source][] 			= $medicationRow["direction"];
					}
				}
				
				// Start Getting Post Op Medications
				
				$medicationQry = "SELECT physician_order_name, confirmation_id, physician_order_type, physician_order_time FROM patient_physician_orders WHERE confirmation_id IN(".$patient_confirmation_id_implode.") AND confirmation_id NOT IN(".$patient_laser_confirmation_id_implode.") AND  chartName = 'post_op_physician_order_form' ";	
		
				$medicationRes = imw_query($medicationQry) or die($medicationQry.imw_error());
			
				if(imw_num_rows($medicationRes)>0) 
				{
					while($medicationRow = imw_fetch_array($medicationRes)) 
					{
						$pConfIdMed 							= 	$medicationRow["confirmation_id"];
						$physician_order_type					= 	$medicationRow["physician_order_type"];
						$physician_order_time					= 	$medicationRow["physician_order_time"];
						if(strtolower($physician_order_type) == "medication" || (trim($physician_order_type)=="" && $physician_order_time != "00:00:00")) { 
							$postMedicationNameArr[$pConfIdMed][] 	= 	$medicationRow["physician_order_name"];
						}
						
					}
				}
				$medicationLaserPostOpQry = "SELECT laser_post_progress, form_status, sign_all_post_op_order_status, confirmation_id FROM laser_procedure_patient_table WHERE confirmation_id IN(".$patient_laser_confirmation_id_implode.")";	
				$medicationLaserPostOpRes = imw_query($medicationLaserPostOpQry) or die(imw_error());
				if(imw_num_rows($medicationLaserPostOpRes)>0)  {
					while($medicationLaserPostOpRow = imw_fetch_assoc($medicationLaserPostOpRes) ) {
						$pConfIdLaserPostOp			= $medicationLaserPostOpRow['confirmation_id'];
						if(trim($medicationLaserPostOpRow['sign_all_post_op_order_status'])<>'0') {
							if(trim($medicationLaserPostOpRow["laser_post_progress"])) {
								$postMedicationNameArr[$pConfIdLaserPostOp][] = 	$medicationLaserPostOpRow["laser_post_progress"];
							}
						}
					}
				}				
				
		}
		
}
//END CODE TO GET RECORDS PRE_OP_PHYSICIAN_ORDER
?>
	
<?php if($response) echo '<script>alert_msg(\'update\')</script>'; ?>		
    
<form action="<?=basename($_SERVER['PHP_SELF'])?>" name="frm_sign_all_pre_post_op_order_noted" id="frm_sign_all_pre_post_op_order_noted" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;">
    <input type="hidden" name="submitMe" id="submitMe" value="true">
    <input type="hidden" name="dos" id="dos" value="<?=$dos;?>">

    <div class="full_width" id="height-adj-pre-op">
    	
        <div class="table_head_sch bg-adj-or">
            <h3 class="rob"> Nurse   &nbsp;<b> <?php echo $loggedInUserName;?></b> </h3>
        </div> 
        
        <div class="full_width" id="dataContainer">
            <div class="pre-table-fixed rob table-bordered">
              	<div class="pad-adj-height-scrollable full_width">	
                	<div class="table-row-displ " id="captionHeader"> 
                    	<div class="col">
                            <table class="left-part-pre table-bordered">
                                <tr class="">
                                    <td class="sign-adj"> <label> &nbsp; <input type="checkbox" name="objMain" onClick="javascript:selAll(this,'<?php echo $stubNumRow?>');"> &nbsp;</label></td>													
                                    <td class="p_name"><label>Patient Name (DOB)</label>  </td>
                                    <td class="dos-field"><label>DOS</label></td>	
                                    <td class="proc" ><label>Procedure</label></td>
                                    <td class="pad-adj-pre-in">
                                        <table class="right-meds-displ table-bordered">
                                            <caption class="head-pre text-center">
                                                <label>Pre-Op Orders</label>
                                            </caption>
                                            <tr class="in_row_dis">
                                                <td><label>Medication </label></td>
                                                <td><label>Strength</label></td>
                                                <td> <label>Direction</label> </td>
                                            </tr>	
                                        </table>
                                    </td>
                                    <td class="pad-adj-pre-in" ><table class="right-meds-displ table-bordered">
                                    	<caption class="head-pre text-center">
                                     		<label>Post-Op Orders</label>
                                     	</caption>
                                      <tr class="in_row_dis">
                                      	<td><label>Medication </label></td>
                                     	</tr>   
                                  	</table></td>
                                </tr>
                            </table>
                    	</div>
                	</div>
                	<div class="table-row-displ" id="top"> 
                    	<div class="col">
                        	<table class="left-part-pre table-bordered">
                            		<tr height="40"><td style="vertical-align:middle; text-align:center; background-color:#333; color:#fff; font-size:18px;" ><b>Pending Pre-Op/Post-Op Orders Noted By Nurse </b></td></tr>
                         	</table>
                        </div>
                     	<div class="col" id="tData">
                      	<table class="left-part-pre table-bordered">	
                        	<?php
														$counter = 0;
														$ctrColor=0;
														if( count($pendingStubRowArr) > 0 ) 
														{
															foreach($pendingStubRowArr as $stubRowNew)
															{
																$ctrColor++;
																$preOpPhySignSurgeonId 	= $stubRowNew["preOpPhySignSurgeonId"];
																$postOpPhySignSurgeonId = $stubRowNew["postOpPhySignSurgeonId"];
																$patient_confirmation_id= $stubRowNew["patient_confirmation_id"];
																$patient_name 					= $stubRowNew["patient_name"];
																$dob 										= $stubRowNew["dob"];
																$patient_name_dob 			= $patient_name;
																if($dob) {
																	$patient_name_dob 		= $patient_name." (".$dob.")";
																}
																$procedureCatId					=	$stubRowNew["procedureCatId"];
																$preMedicationSource		=	($procedureCatId == '2') ? '1' : '0';	
																$patient_primary_procedure=	stripslashes($stubRowNew["patient_primary_procedure"]);
																$postOpDropExist				=	'';
																
																$counter++;
													?>
                          			<input type="hidden" name="patientConfirmationIdArr[]" id="patientConfirmationId<?php echo $counter;?>" value="<?php echo $patient_confirmation_id;?>" disabled />
                                
                                
                                <tr style="background-color:<?php if(($ctrColor%2)!=0) echo '#eee'; else echo '#fff';?>;">
                                	<td class="sign-adj">
                                  	<label>&nbsp;<input type="checkbox" name="chkbxMed[]" id="chkbxMed<?php echo $counter;?>" onClick="isChecked(this,'<?php echo $counter;?>')"  /> &nbsp;</label>
                                 	</td>
                                  <td class="p_name"><?php echo $patient_name_dob;?>  </td>
                                  <td class="dos-field"><?php echo $dos;?></td>	
                                  <td class="proc" ><?php echo $patient_primary_procedure;?></td>
                                  <td class="pad-adj-pre-in">
                                  	<table class="right-meds-displ table-bordered">
                                    <?php
																			$key	=	$patient_confirmation_id.'_S'.$preMedicationSource;
																			foreach($medicationNameArr[$key] as $medicationNameKey => $medicationNameVal) {
																					$medicationName = $medicationNameArr[$key][$medicationNameKey];
																					$strength 		= $strengthArr[$key][$medicationNameKey];
																					$direction 		= $directionArr[$key][$medicationNameKey];
																		?>
                                    		<tr class="in_row_dis">
                                        	<td ><?php echo $medicationName;?></td>
                                          <td><?php echo $strength;?></td>
                                          <td ><?php echo $direction;?></td>
                                       	</tr>	
                                   	<?php
																			}
																		?>
                                  	</table>
                                	</td>
                                  <td class="pad-adj-pre-in">
                                  	<table class="right-meds-displ table-bordered">
                                    <?php
																			foreach($postMedicationNameArr[$patient_confirmation_id] as $medicationNameKey => $medicationNameVal) {
																				if($medicationNameVal) $postOpDropExist	=	'Yes'
																		?>
                                    		<tr class="in_row_dis">
                                        	<td ><?php echo $medicationNameVal;?></td>
                                       	</tr>
                                   	<?php
																			}
																		?>
                                    </table>
                                	</td>
                                  <input type="hidden" name="postOpDropExist_<?=$patient_confirmation_id?>" id="postOpDropExist<?php echo $counter;?>" value="<?=$postOpDropExist?>" />
                               	</tr>
                        	<?php
															}
														}
														else
														{
															echo '<tr height="60"><td style="text-align:center; vertical-align:middle"  ><b>No Pre-Op/Post-Op Order Pending To Be Noted By Nurse</b></td></tr>';	
														}
													?>
                       	</table>
                     	</div>
                	</div>
                  
                  <div class="table-row-displ" id="bottom">
                  	<div class="col">
                    	<table class="left-part-pre table-bordered">
                      	<tr height="40"><td style="vertical-align:middle; text-align:center; background-color:#333; color:#fff; font-size:18px;" ><b>Pre-Op/Post-Op Orders Noted By Nurse</b></td></tr>
                    	</table>
                   	</div>
                    <div class="col" id="bData">         
                    	<table class="left-part-pre table-bordered">
                      <?php
												$counter = 0;
												$ctrColor=0;
												if( count($stubRowArr) > 0 )
												{
													foreach($stubRowArr as $stubRowNew)
													{
														$ctrColor++;
														$preOpPhySignSurgeonId 	= $stubRowNew["preOpPhySignSurgeonId"];
														$postOpPhySignSurgeonId = $stubRowNew["postOpPhySignSurgeonId"];
														
														$patient_confirmation_id= $stubRowNew["patient_confirmation_id"];
														$patient_name 					= $stubRowNew["patient_name"];
														$dob 										= $stubRowNew["dob"];
														$patient_name_dob 			= $patient_name;
														if($dob) {
															$patient_name_dob 		= $patient_name." (".$dob.")";
														}
														$preOpPhysicianOrdersId	=	$stubRowNew['preOpPhysicianOrdersId'];
														$postOpPhysicianOrdersId=	$stubRowNew['postOpPhysicianOrdersId'];
														$procedureCatId					=	$stubRowNew["procedureCatId"];
														$preMedicationSource		=	($procedureCatId == '2') ? '1' : '0';	
														$patient_primary_procedure 	=	stripslashes($stubRowNew["patient_primary_procedure"]);
														$preOpPhyFormStatus 		=	$stubRowNew["pre_op_phy_form_status"];
														$postOpPhyFormStatus 		=	$stubRowNew["post_op_phy_form_status"];
														$counter++;
											?>
                      			<tr style="background-color:<?php if(($ctrColor%2)!=0) echo '#eee'; else echo '#fff';?>;">
                            	<td class="sign-adj"><label class="check">&#x2714;</label></td>
                              <td class="p_name" ><?php echo $patient_name_dob;?>  </td>
                              <td class="dos-field"><?php echo $dos;?></td>	
                              <td class="proc" ><?php echo $patient_primary_procedure;?></td>
                              <td class="pad-adj-pre-in">
                              	<table class="right-meds-displ table-bordered">
                                <?php
																	$key	=	$patient_confirmation_id.'_S'.$preMedicationSource;
																	foreach($medicationNameArr[$key] as $medicationNameKey => $medicationNameVal) {
																		$medicationName = $medicationNameArr[$key][$medicationNameKey];
																		$strength 		= $strengthArr[$key][$medicationNameKey];
																		$direction 		= $directionArr[$key][$medicationNameKey];
																?>
                                		<tr class="in_row_dis">
                                    	<td ><?php echo $medicationName;?></td>
                                      <td><?php echo $strength;?></td>
                                      <td ><?php echo $direction;?></td>
                                   	</tr>	
                               	<?php
																	}
																?>
                             		</table>
                            	</td>
                              <td class="pad-adj-pre-in">
                                <table class="right-meds-displ table-bordered">
                                <?php
                                  foreach($postMedicationNameArr[$patient_confirmation_id] as $medicationNameKey => $medicationNameVal) {
                               	?>
                                    <tr class="in_row_dis">
                                      <td ><?php echo $medicationNameVal;?></td>
                                    </tr>
                                <?php
                                  }
                                ?>
                                </table>
                              </td>
                          	</tr>
                     	<?php
													}
												}
												else
												{
													echo '<tr height="60"><td style="text-align:center; vertical-align:middle"  ><b>No Pre-Op/Post-Op Order Noted By Nurse</b></td></tr>';	
												}
											?>
                   		</table>
                 		</div>
                	</div>
                  
            	</div>
        	</div>
        </div>
        
        
        <div class="btn-footer-slider" style="border-top: 1px solid #ddd;">
        	<?php  if(count($pendingStubRowArr) > 0 ) : ?>
            <a href="javascript:void(0)" class="btn btn-success" onclick="buttonClick();">
            	<b class="fa fa-th"></b>&nbsp;Noted
          	</a>
            <?php endif; ?>
            <a href="javascript:void(0)" class="btn btn-danger" onclick="javascript:window.close();">Close</a>
  		</div>
        
	</div>
</form>

<div id="myModal" class="modal fade in" style="top:20%"> <!--Common Alert Container-->
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="padding:6px 12px;">
				<button style="color:#FFFFFF;opacity:0.9" ype="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 style="color:#FFFFFF;" class="modal-title">Pre-Op/Post-Op Orders Noted By Nurse</h4>
			</div>
			<div class="modal-body" style="min-height:auto;">
				<p style="padding: 10px;" class="text-center"></p>
			</div>
			<div class="modal-footer" style="text-align:center;margin-top:0;padding:4px;">
				<button id="cancel_yes" class="btn btn-primary hidden" onclick="this.disabled=true;document.getElementById('frm_sign_all_pre_post_op_order_noted').submit();">Yes</button>
				<button id="cancel_no" class="btn btn-danger hidden" data-dismiss="modal">No</button>
				<button style="margin-left:0;" id="missing_feilds" class="btn btn-primary hidden" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>
</body>

</html>