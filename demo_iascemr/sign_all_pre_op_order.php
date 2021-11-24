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
	var objPreOpChk		=	document.getElementById('chbxPreOpOrder');
	var objPostOpChk	=	document.getElementById('chbxPostOpOrder');
	if(objPreOpChk.checked==false && objPostOpChk.checked==false) {
			$('#myModal .modal-body>p').removeClass('text-center');
			$('#myModal .modal-body>p').html('Please select Pre/Post-Op Order');
			$("#missing_feilds").removeClass('hidden');
			$("#cancel_yes").addClass('hidden');
			$("#cancel_no").addClass('hidden');
			$('#myModal').modal('show');
			objPreOpChk.focus();
			return;
	}
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
	<div class="alert alert-success alert-msg" id="alert_success"><strong>Record(s) Saved Successfully</strong></div>
	<div class="loader" style="top:40%;">
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


	
	//$query = "SELECT St.stub_id, Pc.patientConfirmationId FROM `stub_tbl` St  Left Join patientconfirmation Pc On St.patient_confirmation_id = Pc.patientConfirmationId Where St.dos='".$selected_date."' ";
	$query = "SELECT St.stub_id, Pc.patientConfirmationId,
				Pr.patient_confirmation_id AS pre_op_phy_conf_id,
				Po.patient_confirmation_id AS post_op_phy_conf_id,
				Lp.confirmation_id AS laser_proc_conf_id,
				Pn.confirmation_id AS pre_op_nurse_conf_id
				FROM  `stub_tbl` St
				LEFT JOIN patientconfirmation Pc ON St.patient_confirmation_id = Pc.patientConfirmationId
				LEFT JOIN preopphysicianorders Pr ON St.patient_confirmation_id = Pr.patient_confirmation_id
				LEFT JOIN postopphysicianorders Po ON St.patient_confirmation_id = Po.patient_confirmation_id
				LEFT JOIN laser_procedure_patient_table Lp ON St.patient_confirmation_id = Lp.confirmation_id
				LEFT JOIN preopnursingrecord Pn ON St.patient_confirmation_id = Pn.confirmation_id
				WHERE St.dos =  '".$selected_date."' GROUP BY St.stub_id ";
	$sql		=	imw_query($query) or die( 'Error found at line no. '.(__LINE__).': ' . imw_error());
	$updateCount = 0; 
	while( $row = imw_fetch_object($sql))
	{
			$pConfId	=	$row->patientConfirmationId ;
			$stub_id	= $row->stub_id ;
			$loginUserId = $loggedInUserId ;
			$pre_op_phy_conf_id		=	$row->pre_op_phy_conf_id ;
			$post_op_phy_conf_id	=	$row->post_op_phy_conf_id ;
			$laser_proc_conf_id		=	$row->laser_proc_conf_id ;
			$pre_op_nurse_conf_id	=	$row->pre_op_nurse_conf_id ;
			if(!$pConfId) 
			{
				$updateCount++ ;	
				include ('sign_all_patient_confirm.php') ;	
			}else {
				if(!$laser_proc_conf_id) {
					$blankInsertQry	= "insert into laser_procedure_patient_table set confirmation_id = '".$pConfId."', form_status= ''"; 
					$blankInsertRes	= imw_query($blankInsertQry) or die(imw_error());
				}
				if(!$pre_op_phy_conf_id) {
					$blankInsertQry = "insert into preopphysicianorders set patient_confirmation_id = '".$pConfId."', form_status= ''"; 
					$blankInsertRes = imw_query($blankInsertQry) or die(imw_error());
				}
				if(!$post_op_phy_conf_id) {
					$blankInsertQry = "insert into postopphysicianorders set patient_confirmation_id = '".$pConfId."', form_status= ''"; 
					$blankInsertRes = imw_query($blankInsertQry) or die(imw_error());
				}
				if(!$pre_op_nurse_conf_id) {
					$blankInsertQry = "insert into preopnursingrecord set confirmation_id = '".$pConfId."', form_status= ''"; 
					$blankInsertRes = imw_query($blankInsertQry) or die(imw_error());
				}
			}
	}
	if($updateCount > 0 )
		echo '<script>window.opener.location.reload();</script>';
	
	
	//GET CONFIRMATION DETAIL												
	$laserSignCatIdArr = array();
	$getConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE  dos='".$selected_date."'";
	$getConfirmationDetailRes = imw_query($getConfirmationDetailQry) or die($getConfirmationDetailQry.imw_error()); 
	$getConfirmationDetailNumRow = imw_num_rows($getConfirmationDetailRes);
	$primary_procedure_is_inj_misc_arr = $patient_primary_procedure_id_arr = array();
	if($getConfirmationDetailNumRow>0) {
		while($getConfirmationDetailRow = imw_fetch_array($getConfirmationDetailRes)) { 
			$ptConfId = $getConfirmationDetailRow["patientConfirmationId"];
			$primary_procedure_is_inj_misc_arr[$ptConfId] = $getConfirmationDetailRow["prim_proc_is_misc"];  
			$patient_primary_procedure_id = $getConfirmationDetailRow["patient_primary_procedure_id"]; 
			$patient_primary_procedure_id_arr[$ptConfId] = $patient_primary_procedure_id;
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
	$chbxPreOpOrder		=	$_REQUEST['chbxPreOpOrder'] ;
	$chbxPostOpOrder	=	$_REQUEST['chbxPostOpOrder'] ;
	
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
	
	if(!trim($laserConfId)) { $laserConfId 	= 0;	 }
	if(!trim($phyConfId)) {   $phyConfId 	= 0;	 }
	
	$SaveSignArr = array('preopphysicianorders','postopphysicianorders','laser_procedure_patient_table');
	$dis_sumry_filled = ''; //Initialize Discharge Summary filled Satus
	$affectedRows	=	0 ;
	foreach($SaveSignArr as $SaveSignArrTableName)
	{
		
		if($chbxPreOpOrder!="yes" && $SaveSignArrTableName == "preopphysicianorders") {
			continue; //DO NO SIGN PRE-OP ORDER IF ITS CHECKBOX IS UNCHECKED
		}
		if($chbxPostOpOrder!="yes" && $SaveSignArrTableName == "postopphysicianorders") {
			continue; //DO NO SIGN POST-OP ORDER IF ITS CHECKBOX IS UNCHECKED
		}
		$signUserId 					=	'signSurgeon1Id';
		$signUserFirstName 		=	'signSurgeon1FirstName'; 
		$signUserMiddleName		=	'signSurgeon1MiddleName';
		$signUserLastName			=	'signSurgeon1LastName'; 
		$signUserStatus				=	'signSurgeon1Status';
		$signUserDateTime			=	'signSurgeon1DateTime';
		$signUserconfirmation_id=	'confirmation_id';
		
		if($SaveSignArrTableName == "preopphysicianorders" || $SaveSignArrTableName == "postopphysicianorders" ) {
			$signUserconfirmation_id = 'patient_confirmation_id';
		}
		
		$inFields	=	$phyConfId ;
		if($SaveSignArrTableName=='laser_procedure_patient_table') {
			$inFields = $laserConfId;
		}
		//START CODE - DO NO SIGN PRE-OP ORDERS IF NOT EXIST
		$inFieldsExpl = explode(",",$inFields);
		foreach($inFieldsExpl as $inFieldsKey => $inFieldsVal) {
			if(($SaveSignArrTableName == 'preopphysicianorders' || $SaveSignArrTableName=='laser_procedure_patient_table') &&  !$_POST['medication_'.$inFieldsVal]) {
				unset($inFieldsExpl[$inFieldsKey]); //DO NO SIGN PRE-OP ORDERS IF NOT EXIST
			}
		}
		$inFields = implode(",",$inFieldsExpl);
		if(!$inFields) { $inFields = '0'; }
		//END CODE - DO NO SIGN PRE-OP ORDERS IF NOT EXIST

		$andQry = " AND $signUserconfirmation_id IN(".$inFields.")";
		$surgeonLaserVerifyFields='';
		if($SaveSignArrTableName=='laser_procedure_patient_table') {
			$laserSubFields = '';
			$laserSubFieldsArr=array();
			if($chbxPreOpOrder=='yes') {
				$laserSubFieldsArr[] = "sign_all_pre_op_order_status = '1' ";	
			}
			if($chbxPostOpOrder=='yes') {
				$laserSubFieldsArr[] = "sign_all_post_op_order_status = '1' ";	
			}
			if(count($laserSubFieldsArr)>0) {
				$laserSubFields = implode(', ',$laserSubFieldsArr);
				$laserSubSaveQry= "UPDATE $SaveSignArrTableName SET ".$laserSubFields." WHERE 1=1 ".$andQry;
				$laserSubSaveRes= imw_query($laserSubSaveQry) or die('Error occured at line no.'.(__LINE__).$laserSubSaveQry.imw_error());
			}
			$surgeonLaserVerifyFields=", verified_surgeon_Id = '".$loggedInUserId."',
											 verified_surgeon_Name = '".$loggedInUserName."',
											 chk_laser_patient_examined = 'Yes',
											 chk_laser_patient_evaluated = 'Yes' ";
		}
		$surgeonPreOpPhyFields='';
		if($SaveSignArrTableName=='preopphysicianorders') {
			$surgeonPreOpPhyFields=", evaluatedPatient = '1'";
		}
		$SaveSignQry = "update $SaveSignArrTableName set 
										$signUserId = '$loggedInUserId',
										$signUserFirstName = '$loggedInUserFirstName', 
										$signUserMiddleName = '$loggedInUserMiddleName',
										$signUserLastName = '$loggedInUserLastName', 
										$signUserStatus = '$signOnFileStatus',
										$signUserDateTime = '$signDateTime'
										$surgeonLaserVerifyFields
										$surgeonPreOpPhyFields
										WHERE ($signUserId='0' OR $signUserId='')  " . $andQry;
										//AND (form_status = 'not completed' OR form_status = 'completed')
			
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
					$primary_procedure_is_inj_misc = $primary_procedure_is_inj_misc_arr[$confirmationId];
					$pt_primary_procedure_id = $patient_primary_procedure_id_arr[$confirmationId];
					if(trim($primary_procedure_is_inj_misc) == '' && trim($pt_primary_procedure_id))
					{
						$primary_procedure_is_inj_misc		=	$objManageData->verifyProcIsInjMisc($pt_primary_procedure_id);
					}
					
					$preOpPhysicianOrdersId =	$_POST['preOpPhysicianOrderId_'.$confirmationId];
					
					$currentFormStatus=	$_POST['formStatus_'.$confirmationId];
					$medicationArray	=	$_POST['medication_'.$confirmationId];
					$strengthArray		=	$_POST['strength_'.$confirmationId];
					$directionArray		=	$_POST['direction_'.$confirmationId];
					$otherPreOpOrder	=	addslashes($_POST['other_pre_op_orders_'.$confirmationId]);
					$postopMedArray		=	$_POST['postop_medication_'.$confirmationId];
					
					//echo 'Confirmation Id :'.$confirmationId .'<br>Pre op Physician Order Id:'.$preOpPhysicianOrdersId.'<br>Form Status : '.$currentFormStatus.'<br>';
					
					//START PRE-FILL OTHER PRE-OP ORDERS
					if($SaveSignArrTableName == 'preopphysicianorders') {
						$updtOtherPreOpOrderQry = "UPDATE $SaveSignArrTableName SET preOpOrdersOther = IF(preOpOrdersOther!='', preOpOrdersOther,'".$otherPreOpOrder."') WHERE patient_confirmation_id = '".$confirmationId."'";
						$updtOtherPreOpOrderRes	=	imw_query($updtOtherPreOpOrderQry) or die(imw_error());	
					}
					//END PRE-FILL OTHER PRE-OP ORDERS
					
					if($SaveSignArrTableName == 'preopphysicianorders' || $SaveSignArrTableName == 'laser_procedure_patient_table' )
					{
						
						if(is_array($medicationArray) && count($medicationArray) > 0 )
						{
							//print_r($medicationArray); echo '<br>';
							//print_r($strengthArray); echo '<br>';
							//print_r($directionArray); echo '<br>';
							foreach($medicationArray as $key => $medicationName )
							{
								$strength		=	$strengthArray[$key];
								$direction	=	$directionArray[$key];
								$source			=	($laserSignCatIdArr[$confirmationId] == '2') ? 1 : 0;
								
								$chkPatientPreOpMediQry = "Select * From patientpreopmedication_tbl Where
																					patient_confirmation_id = '$confirmationId'  And
																					medicationName = '$medicationName'  And
																					strength = '$strength' And
																					direction = '$direction' And
																					sourcePage = '$source'
																				";
								
								$chkPatientPreOpMediSql	=	imw_query($chkPatientPreOpMediQry);
								$chkPatientPreOpMediNum=	imw_num_rows($chkPatientPreOpMediSql);
								
								if($chkPatientPreOpMediNum == 0)
								{
									
									$insPatientPreOpMediQry = "Insert Into patientpreopmedication_tbl Set
																						preOpPhyOrderId = '$preOpPhysicianOrdersId',
																						patient_confirmation_id = '$confirmationId',
																						medicationName = '$medicationName',
																						strength = '$strength',
																						direction = '$direction',
																						sourcePage = '$source'
																					"; 
									$insPatientPreOpMediRes = imw_query($insPatientPreOpMediQry) or die(imw_error()); 	
								}
							
							}
							
						}
						
						$prefilMedicationStatusSource = $saveFromChart =  1 ;
						if( $currentFormStatus == 'not completed' || $currentFormStatus == 'completed')	{
							$prefilMedicationStatusSource = $saveFromChart = 0 ;
						}
						$updatePrefilMedicationStatusQry = " Update $SaveSignArrTableName Set
																										prefilMedicationStatus = 'true', 
																										prefilMedicationStatusSource = $prefilMedicationStatusSource,
																										saveFromChart = $saveFromChart
																										WHERE $signUserconfirmation_id = '$confirmationId' ".$versionQry."
																					 ";
						$updatePrefilMedicationStatusRes = imw_query($updatePrefilMedicationStatusQry) or die(imw_error());
					
					
					}
					
					// Prefilling of Post Op Medications 
					if($SaveSignArrTableName == 'postopphysicianorders')
					{
						if(is_array($postopMedArray) && count($postopMedArray) > 0)
						{
							foreach($postopMedArray as $key => $medicationName)
							{
									$medicationName	=	trim($medicationName);
									if($medicationName)
									{
										$dataArray	=	array();
										$dataArray['confirmation_id']				=	$confirmationId ;
										$dataArray['chartName']							=	'post_op_physician_order_form';
										$dataArray['physician_order_name']	=	$medicationName;
										
										$chkRecords	=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$dataArray);
										if(!$chkRecords) 
										{
											$dataArray['physician_order_location']		= 'sign_all_pre_op_order';
											$dataArray['physician_order_date_time']		= date("Y-m-d H:i:s");
											$dataArray['physician_order_type']			= 'medication';
											$objManageData->addRecords($dataArray,'patient_physician_orders');	
										}
									}
							}
						}
					}
					if($SaveSignArrTableName == "laser_procedure_patient_table" && $laserSignCatIdArr[$confirmationId]=='2') {
						if(is_array($postopMedArray) && count($postopMedArray) > 0) {
							foreach($postopMedArray as $key => $medicationName) {
								$medicationName	=	trim($medicationName);
								if($medicationName) {
									$dataArray	=	array();
									$dataArray['confirmation_id']	=	$confirmationId ;
									$dataArray['form_status']		=	'' ;
									$chkRecords	=	$objManageData->getMultiChkArrayRecords('laser_procedure_patient_table',$dataArray);
									if($chkRecords) {
										$dataSaveArray	=	array();
										$dataSaveArray['confirmation_id']				=	$confirmationId ;
										$dataSaveArray['chk_laser_post_progress']		=	'on';
										$dataSaveArray['laser_post_progress']			=	$medicationName;
										$objManageData->updateRecords($dataSaveArray, 'laser_procedure_patient_table', 'confirmation_id', $confirmationId);
									}
									
								}
							}
						}
					}
					
					//if( $currentFormStatus == 'not completed' || $currentFormStatus == 'completed' )
					//update form status 
					$tableFormStatus	= 'completed';
					$laserSignCatId		=	$laserSignCatIdArr[$confirmationId];
					$leftNaviFieldName = "";
					if($SaveSignArrTableName == "preopphysicianorders" && $laserSignCatId!='2') 
					{		
							$leftNaviFieldName = "pre_op_physician_order_form";
		
							$chkPreOpPhysicianFormStatus 			= $tableFormStatusRow['form_status'];
							$chkPreOpPhysicianVersionNum 			= $tableFormStatusRow['version_num'];
							$chkPreOpPhysicianVersionDateTime = $tableFormStatusRow['version_date_time'];
							$version_num	=	$chkPreOpPhysicianVersionNum;
							
							// Check & update Form's Version if form not saved once.
							if(!$chkPreOpPhysicianVersionNum)
							{
								$version_date_time	=	$chkPreOpPhysicianVersionDateTime;
								if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
								{
									$version_date_time	=	date('Y-m-d H:i:s');
								}
										
								if($chkPreOpPhysicianFormStatus == 'completed' || $chkPreOpPhysicianFormStatus == 'not completed'){
									$version_num = 1;
								}else{
									$version_num	=	3;
								}
								$updtQry	=	"Update ".$SaveSignArrTableName." Set version_num = '".$version_num."', version_date_time = '".$version_date_time."' Where $signUserconfirmation_id ='".$confirmationId."' ";
								imw_query($updtQry);
							}
							//End Check & update Form's Version if form not saved once.
							
							
							$signNurseIdPreOpPhy = $tableFormStatusRow['signNurseId'];
							$signNurse1IdPreOpPhy = $tableFormStatusRow['signNurse1Id'];
							$signSurgeon1IdPreOpPhy = $tableFormStatusRow['signSurgeon1Id'];
							$notedByNurse = $tableFormStatusRow['notedByNurse'];
							
							if($signSurgeon1IdPreOpPhy=="0" || $signNurse1IdPreOpPhy == "0" || $notedByNurse==0){	
								$tableFormStatus = 'not completed';
							}
							
							// Start Validate chart if form is on version no. 2 or greater and surgeon signature not on chart
							if($version_num == 1 && $tableFormStatus == 'completed' && $signNurseIdPreOpPhy == "0")
							{
								$tableFormStatus = 'not completed';
							}
							// End Validate chart if form is on version no. 2 or greater and surgeon signature not on chart
					}
					else if($SaveSignArrTableName == "postopphysicianorders" && $laserSignCatId!='2') 
					{	
						$leftNaviFieldName = "post_op_physician_order_form";
						$chkPostOpPhysicianFormStatus 			= $tableFormStatusRow['form_status'];
						$chkPostOpPhysicianVersionNum 			= $tableFormStatusRow['version_num'];
						$chkPostOpPhysicianVersionDateTime 	= $tableFormStatusRow['version_date_time'];
						$version_num	=	$chkPostOpPhysicianVersionNum;
						
						// Check & update Form's Version if form not saved once.
						if(!$chkPostOpPhysicianVersionNum)
						{
							$version_date_time	=	$chkPostOpPhysicianVersionDateTime;
							if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
							{
								$version_date_time	=	date('Y-m-d H:i:s');
							}
									
							if($chkPostOpPhysicianFormStatus == 'completed' || $chkPostOpPhysicianFormStatus == 'not completed'){
								$version_num = 1;
							}else{
								$version_num	=	3;
							}
							$updtQry	=	"Update ".$SaveSignArrTableName." Set version_num = '".$version_num."', version_date_time = '".$version_date_time."' Where $signUserconfirmation_id='".$confirmationId."' ";
							imw_query($updtQry);
						}
						//End Check & update Form's Version if form not saved once.
		
						$postOpDropExist 	= "";
						$chkPostOpDropQry = "SELECT recordId,physician_order_time FROM patient_physician_orders 
																 WHERE confirmation_id = '".$confirmationId."' 
																 AND chartName = 'post_op_physician_order_form'";
						$chkPostOpDropRes = imw_query($chkPostOpDropQry); 
						if(imw_num_rows($chkPostOpDropRes)>0)
						{
							$postOpDropExist = "Yes";
						}
						
						$patientAssessed = $tableFormStatusRow['patientAssessed'];
						$vitalSignStable = $tableFormStatusRow['vitalSignStable'];
						$postOpEvalDone = $tableFormStatusRow['postOpEvalDone'];
						$postOpInstructionMethodWritten = $tableFormStatusRow['postOpInstructionMethodWritten'];
						$postOpInstructionMethodVerbal = $tableFormStatusRow['postOpInstructionMethodVerbal'];
						$patientAccompaniedSafely = $tableFormStatusRow['patientAccompaniedSafely'];
						$signNurseIdPostOpPhy = $tableFormStatusRow['signNurseId'];
						$signNurse1IdPostOpPhy = $tableFormStatusRow['signNurse1Id'];
						$signSurgeon1IdPostOpPhy = $tableFormStatusRow['signSurgeon1Id'];
						$postOpNotedByNurse	=	$tableFormStatusRow['notedByNurse'];
						
						if(($patientAssessed!='Yes') 
							||($vitalSignStable!='Yes') 
							||($postOpEvalDone!='Yes') 
							||($postOpInstructionMethodWritten!='Yes') 
							||($postOpInstructionMethodVerbal!='Yes') 
							||($patientAccompaniedSafely!='Yes') 
							||($signNurseIdPostOpPhy=="0") || ($signSurgeon1IdPostOpPhy=="0")
							||($signNurse1IdPostOpPhy == "0" && $version_num >2)
							||($postOpNotedByNurse == "0" && $version_num >2)
							||($postOpDropExist!='Yes')
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
						
						if($SaveSignArrTableName == "laser_procedure_patient_table" && $laserSignCatId!='2') {
							//DO NOT UPDATE FORM STATUS FOR Laser Chartnote if procedure is not laser
						}else if($laserSignCatId=='2' && ($SaveSignArrTableName == "preopphysicianorders" || $SaveSignArrTableName == "postopphysicianorders"  )) {
							//DO NOT UPDATE FORM STATUS FOR preopphysicianorders/postopphysicianorders Chartnote if procedure is laser
						}else if($laserSignCatId <> '2' && $primary_procedure_is_inj_misc && ($SaveSignArrTableName == "preopphysicianorders" || $SaveSignArrTableName == "postopphysicianorders")) {
							//DO NOT UPDATE FORM STATUS FOR preopphysicianorders AND postopphysicianorders Chartnote if procedure is injection
						}else{	
							$updateTableFormStatusQry = "update $SaveSignArrTableName SET form_status='$tableFormStatus' WHERE $signUserconfirmation_id='".$tableFormStatusRow[$signUserconfirmation_id]."' ";
							imw_query($updateTableFormStatusQry) or die('Error occured at line no. '.(__LINE__).': '.imw_error());
							
							//START SHIFTING LEFT LINK TO RIGHT SLIDER 
							if(trim($leftNaviFieldName)) {
								$updateLeftNavigationTableQry = "update `left_navigation_forms` set $leftNaviFieldName = 'false' WHERE confirmationId='".$tableFormStatusRow[$signUserconfirmation_id]."'";
								$updateLeftNavigationTableRes = imw_query($updateLeftNavigationTableQry) or die($updateLeftNavigationTableQry.imw_error());		
							}
							//END SHIFTING LEFT LINK TO RIGHT SLIDER
						
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
			
			//END SET FORM STATUS(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
		}
	
	}
	
	$response	=	$affectedRows." Records(s) Updated" ;
	
}
//END CODE TO MAKE SIGNATURES OF SURGEON IN PRE-OP ORDER CHART NOTES OF PATIENT(IN OPENED DOS)	




//START CODE TO GET RECORDS PRE_OP_PHYSICIAN_ORDER
$stubNumRow = 0;
$stubRowArr = $pendingStubRowArr = $patient_confirmation_id_arr = $patient_laser_confirmation_id_arr = $surgeon_arr = $primary_procedure_id_arr = $secondary_procedure_id = $tertiary_procedure_id = $defaultMedTable	=	$postOpFormStatus = $preOpPrefillStatus = $laserPrefillStatus = $patientProcCatId = array();
$patient_confirmation_id_implode =  0;
$patient_laser_confirmation_id_implode =  0;
if( $_SESSION['loginUserType'] == 'Surgeon' ) 
{
		$stubQry = "SELECT 
							TRIM(CONCAT(st.patient_last_name,', ',st.patient_first_name,' ',st.patient_middle_name)) as patient_name , 
							DATE_FORMAT(st.patient_dob,'%m/%d/%Y') as dob, st.patient_primary_procedure, st.patient_confirmation_id,  
							pr.form_status as pre_op_phy_form_status, lp.form_status as laser_procedure_form_status,pr.preOpPhysicianOrdersId,
							po.form_status as post_op_phy_form_status,
							pc.patient_primary_procedure_id, pc.patient_secondary_procedure_id, pc.patient_tertiary_procedure_id,
							pr.signSurgeon1Id AS preOpPhySignSurgeonId , lp.signSurgeon1Id AS laserProcSignSurgeonId, po.signSurgeon1Id AS postOpPhySignSurgeonId ,
							pcr.catId as procedureCatId , pc.surgeonId, 
							pr.version_num as preOpPhysicianVersionNum,
							pr.prefilMedicationStatus as pre_op_prefill_status, lp.prefilMedicationStatus as laser_prefill_status, 
							lp.sign_all_pre_op_order_status, lp.sign_all_post_op_order_status  
						FROM stub_tbl st
						LEFT JOIN patientconfirmation pc ON (pc.patientConfirmationId = st.patient_confirmation_id)
						LEFT JOIN preopphysicianorders pr ON (pr.patient_confirmation_id = st.patient_confirmation_id AND pr.patient_confirmation_id !='0')
						LEFT JOIN postopphysicianorders po ON (po.patient_confirmation_id = st.patient_confirmation_id AND po.patient_confirmation_id !='0')
						LEFT JOIN laser_procedure_patient_table lp ON (lp.confirmation_id = st.patient_confirmation_id AND lp.confirmation_id !='0')
						LEFT JOIN procedures pcr ON (pc.patient_primary_procedure_id = pcr.procedureId )
						WHERE st.dos = '".$selected_date."' 
							AND st.surgeon_fname = '".addslashes($loggedInUserFirstName)."'
							AND st.surgeon_lname = '".addslashes($loggedInUserLastName)."'
							AND st.patient_status != 'Canceled'
							AND pc.prim_proc_is_misc = ''
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
				$preOpPhySignSurgeonId 		= $stubRow["preOpPhySignSurgeonId"];
				$laserProcSignSurgeonId		= $stubRow["laserProcSignSurgeonId"];
				$preOpPhysicianVersionNum	= $stubRow["preOpPhysicianVersionNum"];
				$postOpPhySignSurgeonId		= $stubRow["postOpPhySignSurgeonId"];
				$procedureCatId				= $stubRow["procedureCatId"];
				$sign_all_pre_op_order_status = $stubRow["sign_all_pre_op_order_status"];
				$sign_all_post_op_order_status = $stubRow["sign_all_post_op_order_status"];
				$stubRow['MedTable']		= 'preopphysicianorders';
				
				if((($preOpPhySignSurgeonId == 0 || $postOpPhySignSurgeonId == 0) && $procedureCatId !='2') || (($laserProcSignSurgeonId == 0 || $sign_all_pre_op_order_status== '0' || $sign_all_post_op_order_status== '0')  && $procedureCatId =='2'))
				{
					$pendingStubRowArr[] = $stubRow;
				}
				else
				{
					$stubRowArr[] = $stubRow;
				}
				$surgeon_arr[$stubRow['patient_confirmation_id']] = $stubRow['surgeonId'];
				$primary_procedure_id_arr[$stubRow['patient_confirmation_id']] = $stubRow['patient_primary_procedure_id'];
				$secondary_procedure_id[$stubRow['patient_confirmation_id']] = $stubRow['patient_secondary_procedure_id'];
				$tertiary_procedure_id[$stubRow['patient_confirmation_id']] = $stubRow['patient_tertiary_procedure_id'];
				$defaultMedTable[$stubRow['patient_confirmation_id']] = $stubRow['MedTable'];
				$patientProcCatId[$stubRow['patient_confirmation_id']]	=	$procedureCatId;
				$preOpPrefillStatus[$stubRow['patient_confirmation_id']] = $stubRow['pre_op_prefill_status'];
				$laserPrefillStatus[$stubRow['patient_confirmation_id']] = $stubRow['laser_prefill_status'];
				$postOpFormStatus[$stubRow['patient_confirmation_id']] = $stubRow['post_op_phy_form_status'];
					if(!in_array($stubRow['patient_confirmation_id'],$patient_confirmation_id_arr) && $stubRow['patient_confirmation_id']!='0') 
					{
						$patient_confirmation_id_arr[] = $stubRow['patient_confirmation_id'];
						if($procedureCatId=='2') {
							$patient_laser_confirmation_id_arr[] = $stubRow['patient_confirmation_id'];	
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
		
		
		$patientMedicationConfId = $defaultMedicationConfId = $patientMedicationPostOpConfId = $defaultMedicationPostOpConfId = array() ;
		$patientMedicationConfId_implode = $defaultMedicationConfId_implode = $patientMedicationPostOpConfId_implode = $defaultMedicationPostOpConfId_implode = 0 ;
		
		if($patient_confirmation_id_implode!=0) 
		{
			/*$medicationCountQry = "SELECT Pc.patientConfirmationId, count(Pm.patient_confirmation_id) as recordsFound FROM patientconfirmation Pc Left Join patientpreopmedication_tbl Pm on Pc.patientConfirmationId = Pm.patient_confirmation_id  WHERE Pc.patientConfirmationId IN(".$patient_confirmation_id_implode.") GROUP BY Pc.patientConfirmationId ";	
			//echo $medicationCountQry ;
			$medicationCountRes = imw_query($medicationCountQry) or die(imw_error());
			if(imw_num_rows($medicationCountRes)>0) 
			{
				while($medicationCountRow = imw_fetch_assoc($medicationCountRes) )
				{		
						$pConfId				=	$medicationCountRow['patientConfirmationId'] ;
						$recordsFound		= $medicationCountRow['recordsFound'];
						$temp_proc_cat	=	$patientProcCatId[$pConfId];
						$temp_prefill		=	$preOpPrefillStatus[$pConfId];
						if($temp_proc_cat == '2')
						{
							$temp_prefill		=	$laserPrefillStatus[$pConfId];
						}
						
						if($recordsFound > 0 && $temp_prefill == 'true')
							array_push($patientMedicationConfId, $pConfId);	
						else
							array_push($defaultMedicationConfId, $pConfId);		
				}
			}*/
			
			foreach($patient_confirmation_id_arr as $pConfId)
			{
				$temp_proc_cat	=	$patientProcCatId[$pConfId];
				$temp_prefill		=	$preOpPrefillStatus[$pConfId];
				if($temp_proc_cat == '2')
				{
					$temp_prefill		=	$laserPrefillStatus[$pConfId];
				}
				if($temp_prefill == 'true')
					array_push($patientMedicationConfId, $pConfId);	
				else
					array_push($defaultMedicationConfId, $pConfId);
			
			}
			
			$patientMedicationConfId_implode = implode(",",$patientMedicationConfId) ;
			$defaultMedicationConfId_implode = implode(",",$defaultMedicationConfId) ;
			
		}
		
		$medicationNameArr = $strengthArr = $directionArr = $otherPreOpOrdersArr = array();
		
		if($patientMedicationConfId_implode != 0) 
		{
				$medicationQry = "SELECT medicationName, strength, direction, patient_confirmation_id FROM patientpreopmedication_tbl WHERE patient_confirmation_id IN(".$patientMedicationConfId_implode.")";	
		
				$medicationRes = imw_query($medicationQry) or die(imw_error());
			
				if(imw_num_rows($medicationRes)>0) 
				{
					while($medicationRow = imw_fetch_array($medicationRes)) 
					{
						$pConfIdMed 											= 	$medicationRow["patient_confirmation_id"];
						$medicationNameArr[$pConfIdMed][] = 	$medicationRow["medicationName"];
						$strengthArr[$pConfIdMed][] 			= 	$medicationRow["strength"];
						$directionArr[$pConfIdMed][] 			= 	$medicationRow["direction"];
					}
				}
				
				//START GET OTHER PRE-OP ORDERS
				$ptOtherPreOpOrderQry = "SELECT * FROM preopphysicianorders WHERE patient_confirmation_id IN(".$patientMedicationConfId_implode.") ";
				$ptOtherPreOpOrderRes = imw_query($ptOtherPreOpOrderQry) or die($ptOtherPreOpOrderQry.imw_error());
				if(imw_num_rows($ptOtherPreOpOrderRes)>0) {
					while($ptOtherPreOpOrderRow = imw_fetch_array($ptOtherPreOpOrderRes)) {
						$pConfIdOtherPreOpOrder		 					= $ptOtherPreOpOrderRow["patient_confirmation_id"];
						$otherPreOpOrdersArr[$pConfIdOtherPreOpOrder] 	= $ptOtherPreOpOrderRow["preOpOrdersOther"];						
					}
				}
				//START GET OTHER PRE-OP ORDERS
		}
		
		
		if($defaultMedicationConfId_implode != 0 ) 
		{ 	
			include_once 'signAllDefaultMedications.php';	
			foreach($defaultMedicationConfId as $pConfId)
			{	
				$defaultMedArr		= array();
				$medications		= array();
				$defaultMedArr 		= signAllDefautlMedications($pConfId, $laserSignCatIdArr[$pConfId], $surgeon_arr[$pConfId],$primary_procedure_id_arr[$pConfId],$secondary_procedure_id[$pConfId],$tertiary_procedure_id[$pConfId],$defaultMedTable[$pConfId]);
				$medications		= $defaultMedArr[0];
				$otherPreOpOrders	= $defaultMedArr[1];
				//print_r($medications); echo '<br><br>';
				if($otherPreOpOrders) {
					$otherPreOpOrdersArr[$pConfId] = $otherPreOpOrders;	
				}
				if(is_array($medications) && count($medications) > 0 )
				{
					foreach($medications as $medicationRow)
					{
						
						$medicationNameArr[$pConfId][] 	= 	$medicationRow["medicationName"];
						$strengthArr[$pConfId][] 				= 	$medicationRow["strength"];
						$directionArr[$pConfId][] 				= 	$medicationRow["direction"];
					}
				}
			}
		}
		
		//START CODE FOR POST OP PHYSICIAN ORDERS
		$medicationPostOpNameArr = array();
		if($patient_confirmation_id_implode!=0) {
			
			$medicationPostOpCountQry = "SELECT Pc.patientConfirmationId, count(Ppo.confirmation_id) as recordsFoundPostOp FROM patientconfirmation Pc Left Join patient_physician_orders Ppo on (Pc.patientConfirmationId = Ppo.confirmation_id AND Ppo.chartName = 'post_op_physician_order_form')  WHERE Pc.patientConfirmationId IN(".$patient_confirmation_id_implode.") AND Pc.patientConfirmationId NOT IN(".$patient_laser_confirmation_id_implode.") GROUP BY Pc.patientConfirmationId ";	
			//echo $medicationPostOpCountQry ;
			$medicationPostOpCountRes = imw_query($medicationPostOpCountQry) or die(imw_error());
			if(imw_num_rows($medicationPostOpCountRes)>0) 
			{
				while($medicationPostOpCountRow = imw_fetch_assoc($medicationPostOpCountRes) )
				{		
						$pConfIdPostOp				=	$medicationPostOpCountRow['patientConfirmationId'] ;
						$recordsFoundPostOp			= 	$medicationPostOpCountRow['recordsFoundPostOp'];
						if($recordsFoundPostOp > 0 || ($recordsFoundPostOp == 0 && ($postOpFormStatus[$pConfIdPostOp] == 'completed' || $postOpFormStatus[$pConfIdPostOp] == 'not completed' )))
							array_push($patientMedicationPostOpConfId, $pConfIdPostOp);	
						else
							array_push($defaultMedicationPostOpConfId, $pConfIdPostOp);		
				}
			}
			$medicationLaserPostOpQry = "SELECT Lp.laser_post_progress, Lp.form_status, Lp.sign_all_post_op_order_status, Pc.patientConfirmationId FROM patientconfirmation Pc Left Join laser_procedure_patient_table Lp on (Pc.patientConfirmationId = Lp.confirmation_id) WHERE Pc.patientConfirmationId IN(".$patient_laser_confirmation_id_implode.")";	
			$medicationLaserPostOpRes = imw_query($medicationLaserPostOpQry) or die(imw_error());
			if(imw_num_rows($medicationLaserPostOpRes)>0)  {
				while($medicationLaserPostOpRow = imw_fetch_assoc($medicationLaserPostOpRes) ) {
					$pConfIdLaserPostOp			= $medicationLaserPostOpRow['patientConfirmationId'];
					if(trim($medicationLaserPostOpRow['sign_all_post_op_order_status'])<>'0') {
						if(trim($medicationLaserPostOpRow["laser_post_progress"])) {
							$medicationPostOpNameArr[$pConfIdLaserPostOp][] = 	$medicationLaserPostOpRow["laser_post_progress"];
						}
					}else {
						array_push($defaultMedicationPostOpConfId, $pConfIdLaserPostOp);	
					}
				}
			}
			
			$patientMedicationPostOpConfId_implode = implode(",",$patientMedicationPostOpConfId) ;
			$defaultMedicationPostOpConfId_implode = implode(",",$defaultMedicationPostOpConfId) ;		
		}
		
		
		if($patientMedicationPostOpConfId_implode != 0) 
		{
				$medicationPostOpQry = "SELECT physician_order_name, confirmation_id FROM patient_physician_orders WHERE confirmation_id IN(".$patientMedicationPostOpConfId_implode.")";	
		
				$medicationPostOpRes = imw_query($medicationPostOpQry) or die(imw_error());
			
				if(imw_num_rows($medicationPostOpRes)>0) 
				{
					while($medicationPostOpRow = imw_fetch_array($medicationPostOpRes)) 
					{
						$pConfIdPostOpMed 							  = 	$medicationPostOpRow["confirmation_id"];
						$medicationPostOpNameArr[$pConfIdPostOpMed][] = 	$medicationPostOpRow["physician_order_name"];
					}
				}
		}
		
		if($defaultMedicationPostOpConfId_implode != 0 ) 
		{ 	
			include_once 'signAllDefaultMedications.php';	
			foreach($defaultMedicationPostOpConfId as $pConfIdPostOp)
			{	
				
				$medicationsPostOp	=	signAllDefaultPostOpMedications($pConfIdPostOp, $laserSignCatIdArr[$pConfIdPostOp], $surgeon_arr[$pConfIdPostOp],$primary_procedure_id_arr[$pConfIdPostOp],$secondary_procedure_id[$pConfIdPostOp],$tertiary_procedure_id[$pConfIdPostOp],$defaultMedTable[$pConfIdPostOp]);
				//print_r($medicationsPostOp); echo '<br><br>';
				if(is_array($medicationsPostOp) && count($medicationsPostOp) > 0 )
				{
					if($laserSignCatIdArr[$pConfIdPostOp]=='2') {
						$medicationPostOpNameArr[$pConfIdPostOp][] 	= 	implode(", ",$medicationsPostOp);
					}else {
						foreach($medicationsPostOp as $medicationPostOpName)
						{
							
							$medicationPostOpNameArr[$pConfIdPostOp][] 	= 	$medicationPostOpName;
						}
					}
				}
			}
		}
		//print'<pre>';print_r($medicationPostOpNameArr);
		//END CODE FOR POST OP PHYSICIAN ORDERS
		
}
//END CODE TO GET RECORDS PRE_OP_PHYSICIAN_ORDER
?>
	
<?php if($response) echo '<script>alert_msg(\'update\')</script>'; ?>		
    
<form action="sign_all_pre_op_order.php" name="frm_sign_all_pre_op_order" id="frm_sign_all_pre_op_order" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;">
    <input type="hidden" name="submitMe" id="submitMe" value="true">
    <input type="hidden" name="dos" id="dos" value="<?=$dos;?>">

    <div class="full_width" id="height-adj-pre-op">
    	
        <div class="table_head_sch bg-adj-or">
            <h3 class="rob"> Surgeon   &nbsp;<b>Dr. <?php echo $loggedInUserName;?></b> </h3>
        </div> 
        
        <div class="full_width" id="dataContainer">
            <div class="pre-table-fixed rob table-bordered">
              	<div class="pad-adj-height-scrollable full_width">	
                	<div class="table-row-displ " id="captionHeader"> 
                    	<div class="col">
                            <table class="left-part-pre table-bordered">
                                <tr class="">
                                    <td class="sign-adj"> <label> &nbsp; <input type="checkbox" name="objMain" onClick="javascript:selAll(this,'<?php echo $stubNumRow?>');"> &nbsp;</label></td>													
                                    <td class="p_name" colspan="2"><label>Patient Name (DOB)</label>  </td>
                                    <td class="dos-field"><label>DOS</label></td>	
                                    <td class="proc" style="width:200px !important;"><label>Procedure</label></td>
                                    <td colspan="4" class="pad-adj-pre-in">
                                        <table class="right-meds-displ table-bordered">
                                            <caption class="head-pre text-center">
                                                <label><input type="checkbox" name="chbxPreOpOrder" id="chbxPreOpOrder" value="yes" <?php if($_REQUEST['chbxPreOpOrder']=='yes' || (!$_REQUEST['chbxPreOpOrder'] && !$_REQUEST['chbxPostOpOrder'])) { echo 'checked'; } ?>>&nbsp;&nbsp;&nbsp;Pre-Op Orders</label>
                                            </caption>
                                            <tr class="in_row_dis">
                                                <td colspan="2"><label>Medication </label></td>
                                                <td ><label>Strength</label></td>
                                                <td colspan="2"> <label>Direction</label> </td>
                                            </tr>	
                                        </table>
                                    </td>
                                    <td colspan="2" class="pad-adj-pre-in">
                                        <table class="right-meds-displ table-bordered">
                                            <caption class="head-pre text-center">
                                                <label><input type="checkbox" name="chbxPostOpOrder" id="chbxPostOpOrder" value="yes" <?php if($_REQUEST['chbxPostOpOrder']=='yes') { echo 'checked'; }?> >&nbsp;&nbsp;&nbsp;Post-Op Orders</label>
                                            </caption>
                                            <tr class="in_row_dis">
                                                <td><label>Medication </label></td>
                                           	</tr>	
                                        </table>
                                    </td>
                                </tr>
                            </table>
                    	</div>
                	</div>
                	<div class="table-row-displ" id="top"> 
                    	<div class="col">
                        	<table class="left-part-pre table-bordered">
                            		<tr height="40"><td style="vertical-align:middle; text-align:center; background-color:#333; color:#fff; font-size:18px;" ><b>Pending Pre/Post-Op Orders </b></td></tr>
                         	</table>
                        </div>
                        <div class="col" id="tData">
                                <table class="left-part-pre table-bordered">	
                                <?php
                                $counter = 0;
								$ctrColor=0;
                                if( count($pendingStubRowArr) > 0 ) 
								{
								    foreach($pendingStubRowArr as $stubRowNew) {
                                        $ctrColor++;
										$preOpPhySignSurgeonId 	= $stubRowNew["preOpPhySignSurgeonId"];
                                        $laserProcSignSurgeonId		= $stubRowNew["laserProcSignSurgeonId"];
          								$patient_confirmation_id	= $stubRowNew["patient_confirmation_id"];
                                        $patient_name 				= $stubRowNew["patient_name"];
                                        $dob 								= $stubRowNew["dob"];
                                        $patient_name_dob 		= $patient_name;
                                        if($dob) {
                                              $patient_name_dob 	= $patient_name." (".$dob.")";
                                        }
										$preOpPhysicianOrdersId		=	$stubRowNew['preOpPhysicianOrdersId'];
                                        $patient_primary_procedure 	=	stripslashes($stubRowNew["patient_primary_procedure"]);
                                        $pre_op_phy_form_status 		=	$stubRowNew["pre_op_phy_form_status"];
                                        $laser_procedure_form_status	=	$stubRowNew["laser_procedure_form_status"];
										$formStatus		=	($pre_op_phy_form_status) 	?	$pre_op_phy_form_status : $laser_procedure_form_status ;
										
										$preOpCheckMark = $postOpCheckMark = '<label class="check" title="Signed">&#x2714;</label>';
										if(($stubRowNew['preOpPhySignSurgeonId'] == 0 && ($stubRowNew['laserProcSignSurgeonId'] == 0 || $stubRowNew['sign_all_pre_op_order_status']=='0')) || (count($medicationNameArr[$patient_confirmation_id])==0)) {
											$preOpCheckMark = '';	
										}
										if(($stubRowNew['postOpPhySignSurgeonId'] == 0 && ($stubRowNew['laserProcSignSurgeonId'] == 0 || $stubRowNew['sign_all_post_op_order_status']=='0')) || (count($medicationPostOpNameArr[$patient_confirmation_id])==0)) {
											$postOpCheckMark = '';	
										}
                                        $counter++;
									?>
                                        	 
                                            <input type="hidden" name="patientConfirmationIdArr[]" id="patientConfirmationId<?php echo $counter;?>" value="<?php echo $patient_confirmation_id;?>" disabled />
                                            <input type="hidden" name="formStatus_<?=$patient_confirmation_id?>" id="formStatus<?php echo $counter;?>" value="<?=$formStatus?>" />
                                            <input type="hidden" name="preOpPhysicianOrderId_<?=$patient_confirmation_id?>" id="preOpPhysicianOrderId<?php echo $counter;?>" value="<?=$preOpPhysicianOrdersId?>" />
                                            
                                          	<tr style="background-color:<?php if(($ctrColor%2)!=0) echo '#eee'; else echo '#fff';?>;">
                                                <td class="sign-adj">
                                                	<label>&nbsp;<input type="checkbox" name="chkbxMed[]" id="chkbxMed<?php echo $counter;?>" <?php echo $chbxEnblDsbl;?> onClick="isChecked(this,'<?php echo $counter;?>')"  /> &nbsp;</label></td>
                                                   
                                                <td class="p_name" colspan="2"><?php echo $patient_name_dob;?>  </td>
                                                <td class="dos-field"><?php echo $dos;?></td>	
                                                <td class="proc" style="width:200px !important;"><?php echo $patient_primary_procedure;?></td>
                                                <td class="pad-adj-pre-in" colspan="4"><?php echo $preOpCheckMark;?>
                                                	<table class="right-meds-displ table-bordered">
                                                  <?php 
																										foreach($medicationNameArr[$patient_confirmation_id] as $medicationNameKey => $medicationNameVal) {
                                                            $medicationName = $medicationNameArr[$patient_confirmation_id][$medicationNameKey];
                                                            $strength 		= $strengthArr[$patient_confirmation_id][$medicationNameKey];
                                                            $direction 		= $directionArr[$patient_confirmation_id][$medicationNameKey];
                                                        
                                                        ?>
                                                            <tr class="in_row_dis">
                                                            	<input type="hidden" readonly name="medication_<?=$patient_confirmation_id?>[]" value="<?=$medicationName?>" />
                                                                <input type="hidden" readonly name="strength_<?=$patient_confirmation_id?>[]" value="<?=$strength?>" />
                                                                <input type="hidden" readonly name="direction_<?=$patient_confirmation_id?>[]" value="<?=$direction?>" />
                                                                
                                                                <td colspan="2"><?php echo $medicationName;?></td>
                                                                <td><?php echo $strength;?></td>
                                                                <td colspan="2"><?php echo $direction;?></td>
                                                            </tr>	
                                                        <?php
                                                        }
                                                        ?>
                                                    </table>
													<input type="hidden" readonly name="other_pre_op_orders_<?php echo $patient_confirmation_id;?>" value="<?php echo $otherPreOpOrdersArr[$patient_confirmation_id];?>" />
                                                </td>
                                                
                                                <td colspan="2" class="pad-adj-pre-in"><?php echo $postOpCheckMark;?>
                                                    <table class="right-meds-displ table-bordered">
                                                    <?php
																											foreach($medicationPostOpNameArr[$patient_confirmation_id] as $medicationNameKey => $medicationNameVal) {
																												$medicationName = $medicationPostOpNameArr[$patient_confirmation_id][$medicationNameKey];
																										?>
                                                    		<tr class="in_row_dis">
                                                        	<input type="hidden" readonly name="postop_medication_<?=$patient_confirmation_id?>[]" value="<?=$medicationName?>" />
                                                          <td><?php echo $medicationName;?></td>
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
									
									echo '<tr height="60"><td style="text-align:center; vertical-align:middle"  ><b>No Pre/Post-Op Order Pending To Sign By Surgeon</b></td></tr>';	
									
								}
                                ?>
                                </table>
                       	</div>
                  	</div>
                   	<div class="table-row-displ" id="bottom">
                    	<div class="col">
                                <table class="left-part-pre table-bordered">
                                <tr height="40"><td style="vertical-align:middle; text-align:center; background-color:#333; color:#fff; font-size:18px;" ><b>Signed Pre/Post-Op Orders </b></td></tr>
                                </table>
                       	</div>
                        <div class="col" id="bData">         
                                <table class="left-part-pre table-bordered">
                                <?php
                                $counter = 0;
								$ctrColor=0;
                                if( count($stubRowArr) > 0 ) {
                                    foreach($stubRowArr as $stubRowNew) {
                                        $ctrColor++;
										$preOpPhySignSurgeonId 	= $stubRowNew["preOpPhySignSurgeonId"];
                                        $laserProcSignSurgeonId		= $stubRowNew["laserProcSignSurgeonId"];
          								$patient_confirmation_id		= $stubRowNew["patient_confirmation_id"];
                                        $patient_name 					= $stubRowNew["patient_name"];
                                        $dob 									= $stubRowNew["dob"];
                                        $patient_name_dob 			= $patient_name;
                                        if($dob) {
                                              $patient_name_dob 	= $patient_name." (".$dob.")";
                                        }
										$preOpPhysicianOrdersId		=	$stubRowNew['preOpPhysicianOrdersId'];
                                        $patient_primary_procedure 	=	stripslashes($stubRowNew["patient_primary_procedure"]);
                                        $pre_op_phy_form_status 		=	$stubRowNew["pre_op_phy_form_status"];
                                        $laser_procedure_form_status	=	$stubRowNew["laser_procedure_form_status"];
										$formStatus		=	($pre_op_phy_form_status) 	?	$pre_op_phy_form_status : $laser_procedure_form_status ;
											
                                        $counter++;
										
										
                                    ?>
                                        	 <tr style="background-color:<?php if(($ctrColor%2)!=0) echo '#eee'; else echo '#fff';?>;">
                                                <td class="sign-adj"><label class="check" title="Signed">&#x2714;</label></td>
                                                <td class="p_name" colspan="2"><?php echo $patient_name_dob;?>  </td>
                                                <td class="dos-field"><?php echo $dos;?></td>	
                                                <td class="proc" style="width:200px !important;"><?php echo $patient_primary_procedure;?></td>
                                                <td colspan="4" class="pad-adj-pre-in">
                                                    <table class="right-meds-displ table-bordered">
                                                        <?php
                                                        foreach($medicationNameArr[$patient_confirmation_id] as $medicationNameKey => $medicationNameVal) {
                                                            $medicationName = $medicationNameArr[$patient_confirmation_id][$medicationNameKey];
                                                            $strength 		= $strengthArr[$patient_confirmation_id][$medicationNameKey];
                                                            $direction 		= $directionArr[$patient_confirmation_id][$medicationNameKey];
                                                        
                                                        ?>
                                                            <tr class="in_row_dis">
                                                            	 <td colspan="2"><?php echo $medicationName;?></td>
                                                                <td><?php echo $strength;?></td>
                                                                <td colspan="2"><?php echo $direction;?></td>
                                                            </tr>	
                                                        <?php
                                                        }
                                                        ?>
                                                    </table>
                                                </td>
                                                <td colspan="2" class="pad-adj-pre-in">
                                                    <table class="right-meds-displ table-bordered">
                                                    <?php
																											foreach($medicationPostOpNameArr[$patient_confirmation_id] as $medicationNameKey => $medicationNameVal) {
																												$medicationName = $medicationPostOpNameArr[$patient_confirmation_id][$medicationNameKey];
																										?>
                                                    		<tr class="in_row_dis">
                                                        	<td><?php echo $medicationName;?></td>
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
									
									echo '<tr height="60"><td style="text-align:center; vertical-align:middle"  ><b>No Pre-Op Order Signed By Surgeon</b></td></tr>';	
									
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
            	<b class="fa fa-th"></b>&nbsp;Sign All Orders
          	</a>
            <?php endif; ?>
            <a href="javascript:void(0)" class="btn btn-danger" onclick="javascript:window.close();">Close</a>
  		</div>
        
	</div>
</form>

<div id="myModal" class="modal fade in" style="top:20%"> <!--Common Alert Container-->
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header" style="padding:6px 12px;">
				<button style="color:#FFFFFF;opacity:0.9" ype="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 style="color:#FFFFFF;" class="modal-title">Sign All Orders</h4>
			</div>
			<div class="modal-body" style="min-height:auto;">
				<p style="padding: 10px;" class="text-center"></p>
			</div>
			<div class="modal-footer" style="text-align:center;margin-top:0;padding:4px;">
				<button id="cancel_yes" class="btn btn-primary hidden" onclick="this.disabled=true;document.getElementById('frm_sign_all_pre_op_order').submit();">Yes</button>
				<button id="cancel_no" class="btn btn-danger hidden" data-dismiss="modal">No</button>
				<button style="margin-left:0;" id="missing_feilds" class="btn btn-primary hidden" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>
</body>

</html>