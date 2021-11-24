<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
set_time_limit(180);
include("common/conDb.php");
include('connect_imwemr.php');
include("common/conDb.php");

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<!--<style>
	td{	
		font-family:verdana,arial;font-size:11px;
	}
	textarea
	{
	resize:none;
	}

</style>-->
<?php
$spec= "
</head>
<body >";

include_once("common/commonFunctions.php");
include("common/link_new_file.php");
include_once("no_record.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$loginUser = $_SESSION['loginUserId'];
unset($privilegesArr);
$privileges = $_SESSION['userPrivileges'];
$privilegesArr = explode(', ', $privileges);

//START GET SCHEDULE STATUS ID FROM IMREMR
$imwSchStatusIdArr = array();
$imwSchStatusQry = "SELECT id, status_name, status FROM ".$imw_db_name.".schedule_status ORDER BY id";
$imwSchStatusRes = imw_query($imwSchStatusQry) or die($imwSchStatusQry.' Error found @ Line No. '.(__LINE__).': '.imw_error());				
if(imw_num_rows($imwSchStatusRes)>0) {
	while($imwSchStatusRow 	= imw_fetch_assoc($imwSchStatusRes)) {
		$imwSchStatusId 	= $imwSchStatusRow["id"];
		$imwSchStatusName 	= trim(ucwords(strtolower(stripslashes($imwSchStatusRow["status_name"]))));
		$imwSchActiveStatus	= stripslashes($imwSchStatusRow["status"]);
		$imwSchStatusIdArr[$imwSchStatusName] = $imwSchStatusId;	
	}
}
//END GET SCHEDULE STATUS ID FROM IMwEMR

#CREATE FACILITY ARRAY
$fac_query=imw_query("select fac_idoc_link_id,fac_name from facility_tbl order by fac_name") or die(imw_error());
while($fac_data=imw_fetch_object($fac_query))
{
	$facility_arr[$fac_data->fac_idoc_link_id]=$fac_data->fac_name;	
}
//$fac_query.close;
$sortSurgeonName= $_REQUEST['sortSurgeonName'];
$sortFieldName 	= $_REQUEST['sortFieldName'];
if($_REQUEST['showAllApptStatus'])
{
	//if($_REQUEST['showAllApptStatus']=='All')
	//$showAllApptStatus = '';
	//else
	$showAllApptStatus = $_REQUEST['showAllApptStatus'];
}
else
{$showAllApptStatus = 'Active';}
$fac_con="";
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" and stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}
$findBy 	= trim($_GET["findBy"]);

//FUNCTION TO DISPLAY TIME AT USER INTERFACE
	function getInterfaceTime($stub_tbl_all_time) {
		$time_split = explode(":",$stub_tbl_all_time);
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
		$stub_tbl_time = $time_split[0].":".$time_split[1]." ".$am_pm;
		return $stub_tbl_time;
	}
//END FUNCTION TO DISPLAY TIME AT USER INTERFACE

$comment = $_REQUEST['comment'];
$stubID = $_REQUEST['id'];
if($stubID) {
	$updtCommentQry = "UPDATE stub_tbl SET 
						comment ='".addslashes($comment)."', 
						comment_modified_status = '1', 
						comment_modified_datetime = '".date("Y-m-d H:i:s")."',
						comment_modified_by_operator = '".$_SESSION['loginUserId']."'
						WHERE stub_id='".$stubID."' ";
	$updtCommentRes = imw_query($updtCommentQry) or die($updtCommentQry.imw_error());
}
$sel_month_number = $_GET["selected_month_number"];
$sel_year_number = $_GET["sel_year_number"];
if($_GET["day"] <> "") {
	$sel_day_number = date("d",mktime(0,0,0,$sel_month_number,$_GET["day"],$sel_year_number));
}
if($sel_month_number == "") {
	$sel_month_number = date("m");
}
if($sel_year_number == "") {
	$sel_year_number = date("Y");
}
if($sel_day_number == "") {
	$sel_day_number = date("d");
}
$selected_date	= $sel_year_number."-".$sel_month_number."-".$sel_day_number;
$display_date	= $sel_month_number."/".$sel_day_number."/".$sel_year_number;

$arrAlertEpost=array();
$qryAlertEpost="SELECT epost_id ,patient_id,patient_conf_id FROM eposted Where table_name='alert'";
$resAlertEpost=imw_query($qryAlertEpost)or die(imw_error());
while($rowAlertEpost=imw_fetch_assoc($resAlertEpost)){
	$resPatientId	=	$rowAlertEpost['patient_id'];
	$resConfId		=	$rowAlertEpost['patient_conf_id'];
	$arrAlertEpost[$resPatientId][$resConfId]=$rowAlertEpost['epost_id'];	
}

// GETTING LOGIN USER FIRST NAME, MIDDLE NAME, LAST NAME, USERTYPE, PRACTICENAME.
	$userTypeQry = "SELECT fname, mname, lname, 
			user_type,coordinator_type, practiceName FROM users
			WHERE usersId = '$loginUser'";
	$userTypeRes = imw_query($userTypeQry);
	$userTypeRows = imw_fetch_array($userTypeRes);
	$surgeonLoggedFirstName = trim(stripslashes($userTypeRows['fname']));
	$surgeonLoggedMiddleName = trim(stripslashes($userTypeRows['mname']));
	$surgeonLoggedLastName = trim(stripslashes($userTypeRows['lname']));
	$loggedInSurgeonName = $surgeonLoggedFirstName.' '.$surgeonLoggedMiddleName.' '.$surgeonLoggedLastName;
	$loggedInUserName = trim($surgeonLoggedLastName.', '.$surgeonLoggedFirstName.' '.$surgeonLoggedMiddleName);
	$userType = $userTypeRows['user_type'];
	$coordinatorType = $userTypeRows['coordinator_type'];
	$practiceName = stripslashes($userTypeRows['practiceName']);
	$user_type = $userTypeRows['user_type'];
// GETTING LOGIN USER FIRST NAME, MIDDLE NAME, LAST NAME, USERTYPE, PRACTICENAME.


$changeStatusid = $_REQUEST['changeStatusid'];
if($changeStatusid){
	$status = $_REQUEST['status'];
	if($_REQUEST['newVal']==1)
	{
		$statusNow =$status;
	}
	else
	{
		//if($status=='Checked-In' || $status=='Scheduled') { 
		if($status=='Checked-In') {
			$statusNow = 'Checked-Out';
		}else if($status=='Checked-Out') {
			$statusNow = 'Checked-In';
		}else if($status=='Scheduled') {
			$statusNow = 'Canceled';
		}else if($status=='Canceled') {
			$statusNow = 'Scheduled';
		}else {
		$chkPtStubQry = "select patient_confirmation_id from stub_tbl where stub_id = '$changeStatusid'";
		$chkPtStubRes = imw_query($chkPtStubQry);
		$chkPtStubNumRow = imw_num_rows($chkPtStubRes);
		//CHECK IF PATIENT CONFIRMATION ID EXIST TABLE THEN SET STATUS FROM CANCEL TO CHECKED-IN ELSE SET STATUS CANCEL TO SCHEDULED
		if($chkPtStubNumRow){
			$chkPtStubRow = imw_fetch_array($chkPtStubRes);
			$chk_Patient_Confirmation_id = $chkPtStubRow['patient_confirmation_id'];
			if($chk_Patient_Confirmation_id<>'0') {
				$statusNow = 'Checked-In';
			}else {
				$statusNow = 'Scheduled';
			}
		}
		//END CHECK IF PATIENT CONFIRMATION ID EXIST TABLE THEN SET STATUS FROM CANCEL TO CHECKED-IN ELSE SET STATUS CANCEL TO SCHEDULED
		
	}	
	}
	unset($arrayRecord);	
	$arrayRecord['patient_status']		= $statusNow;
	if($status=='Checked-In') {
		$arrayRecord['checked_out_time']= date("h:i A");
		$arrayRecord['recentChartSaved']= '';
	}
	$objManageData->updateRecords($arrayRecord, 'stub_tbl', 'stub_id', $changeStatusid);
	
	//START CODE TO SET PATIENT APPOINTMENT STATUS IN imwemr
	if($statusNow=='Canceled' || $statusNow=='Scheduled' || $statusNow=='Checked-In' || $statusNow=='Checked-Out' || $statusNow=='No Show' || $statusNow=='Aborted Surgery') {
		$connectionFileName					= '';
		$closeConnectionFileName			= '';
		if($imwSwitchFile == 'sync_imwemr.php') {
			$connectionFileName 			= 'connect_imwemr.php';
			$closeConnectionFileName 		= $link_imwemr;
		}else if($imwSwitchFile == 'sync_imwemr_remote.php') {
			$connectionFileName 			= 'connect_imwemr_remote.php';
			$closeConnectionFileName 		= $link_imwemr_remote;
		}
		$selected_date_stub 				= $selected_date;
		if($connectionFileName) {
			$getStubImwApptIdQry 			= "SELECT * FROM stub_tbl WHERE stub_id = '".$changeStatusid."'";
			$getStubImwApptIdRes 			= imw_query($getStubImwApptIdQry) or die($getStubImwApptIdQry.imw_error());
			$getStubImwApptIdRow 			= imw_fetch_array($getStubImwApptIdRes);
			$stubImwApptId	 				= $getStubImwApptIdRow["appt_id"];
			$stubComment	 				= $getStubImwApptIdRow["comment"];
			$stubIolinkPatientInWaitingId 	= trim($getStubImwApptIdRow["iolink_patient_in_waiting_id"]);
			$selected_date_stub				= $getStubImwApptIdRow["dos"];
			
			$imwApptStatusId 	 = '202';							//INITIALIZE VARIABLE
			$imwApptComments 	 = 'Rescheduled by '.$loggedInUserName; 	//INITIALIZE VARIABLE
			if($statusNow=='Canceled') {
				$imwApptComments = 'Cancelled by '.$loggedInUserName;
				$imwApptStatusId = '18';
			}else if($statusNow=='No Show') {
				$imwApptComments = 'No Show by '.$loggedInUserName;
				$imwApptStatusId = '3';
			}else if($statusNow=='Aborted Surgery') {
				$imwApptComments = 'Aborted Surgery by '.$loggedInUserName;
				$imwApptStatusId = $imwSchStatusIdArr['Aborted Surgery'];
			}else if($statusNow=='Scheduled') {
				$imwApptComments = 'Rescheduled by '.$loggedInUserName;
				$imwApptStatusId = '202';
			}else if($statusNow=='Checked-In') {
				$imwApptComments = 'Checked-In by '.$loggedInUserName;
				$imwApptStatusId = '13';
			}else if($statusNow=='Checked-Out') {
				$imwApptComments = 'Checked-Out by '.$loggedInUserName;
				$imwApptStatusId = '11';
			}
			//include_once($connectionFileName); // imwemr connection
			//logApptChangedStatus($intApptId, $dtNewApptDate, $tmNewApptStartTime, $tmNewApptEndTime, $intNewApptStatusId='18', $intNewApptProviderId, $intNewApptFacilityId, $strNewApptOpUsername='surgercenter', $strNewApptComments='No Reason', $intNewApptProcedureId, $blUpdateNew = false,$connectionFileName,$closeConnectionFileName)
			$blUpdateNew = false;
			$stubComment = addslashes($stubComment);
			$imwComments = trim($stubComment.' '.$imwApptComments);
			logApptChangedStatus($stubImwApptId, $selected_date_stub, '', '', $imwApptStatusId, '', '', 'surgerycenter', $imwComments, '', $blUpdateNew,$connectionFileName,$closeConnectionFileName);
			include($connectionFileName); // imwemr connection
			if($statusNow=='Canceled') {//CALL FUNCTION TO RESTORE LABELS IN IDOC
				//restore_imw_appt_label('', '', $stubImwApptId, $constantImwSlotMinute,$connectionFileName,$closeConnectionFileName);
			}
			$updateImwApptStatusQry 		= "UPDATE schedule_appointments 
												SET sa_patient_app_status_id='".$imwApptStatusId."',
												sa_comments='".addslashes($imwComments)."' 
												WHERE sa_app_start_date='".$selected_date_stub."' 
												AND id='".$stubImwApptId."'";
			$updateImwApptStatusRes 		= imw_query($updateImwApptStatusQry) or die($updateImwApptStatusQry.imw_error());
			imw_close($connectionFileName); //CLOSE IMWEMR CONNECTION
			include("common/conDb.php");  //SURGERYCENTER CONNECTION
			//START UPDATE PATIENT STATUS IN IOLINK
			if(($statusNow=='Canceled' || $statusNow=='Scheduled') && $stubIolinkPatientInWaitingId && $stubIolinkPatientInWaitingId!='0') {
				$updtIolinkWaitingTblQry = "UPDATE patient_in_waiting_tbl SET patient_status='".$statusNow."',comment='".addslashes($imwComments)."'
											WHERE  patient_in_waiting_id = '".$stubIolinkPatientInWaitingId."' AND dos = '".$selected_date_stub."'";
				$updtIolinkWaitingTblRes = imw_query($updtIolinkWaitingTblQry) or die($updtIolinkWaitingTblQry.imw_error());
			}
			//END UPDATE PATIENT STATUS IN IOLINK
				
		}
	}		
	//END CODE TO SET PATIENT APPOINTMENT STATUS IN imwemr
}
//imw_close($GLOBALS['dbh']);
//include("common/conDb.php");  //SURGERYCENTER CONNECTION
//$adb = imw_query("SELECT DATABASE() as imw_dbname") or die('db not connected');$bdb = imw_fetch_assoc($adb);echo 'hlo '.$bdb['imw_dbname'];die;
//var_dump($GLOBALS['dbh']);echo'<br>';
$chkPtExist = $_REQUEST['chkPtExist'];
//START CODE TO REGISTER THE NON SCHEDULED PATIENT(FOR SCAN) IF PATIENT DOES NOT EXIST IN PATIENT DATA 
	if($chkPtExist=="yes") {
		$ptStubId = $_REQUEST["ptStubId"];
		$stub_dos = $_REQUEST['stub_dos'];
		$actionDenied	=	$_REQUEST['AD'];
		$getPtStubQry 			= "select * from stub_tbl where stub_id = '$ptStubId'";
		$getPtStubRes 			= imw_query($getPtStubQry);
		$getPtStubRow 			= imw_fetch_array($getPtStubRes);
		$getPtStubId 			= $getPtStubRow["stub_id"];
		$getPtStubFirstName 	= $getPtStubRow["patient_first_name"];
		$getPtStubMiddleName 	= $getPtStubRow["patient_middle_name"];
		$getPtStubLastName 		= $getPtStubRow["patient_last_name"];
		$getPtStubDob 			= $getPtStubRow["patient_dob"];
		$getPtStubZip 			= $getPtStubRow["patient_zip"];
		
		$patientIdStub 			= $getPtStubRow["patient_id_stub"];
		$stubImwPatientId 	= $getPtStubRow["imwPatientId"];
		
		$arrayPatientRecord['patient_fname'] 	= addslashes($getPtStubFirstName);
		$arrayPatientRecord['patient_mname'] 	= addslashes($getPtStubMiddleName);
		$arrayPatientRecord['patient_lname'] 	= addslashes($getPtStubLastName);
		$arrayPatientRecord['street1'] 			= addslashes($getPtStubRow["patient_street1"]);
		$arrayPatientRecord['street2'] 			= addslashes($getPtStubRow["patient_street2"]);
		$arrayPatientRecord['city'] 			= $getPtStubRow["patient_city"];
		$arrayPatientRecord['state'] 			= $getPtStubRow["patient_state"];
		$arrayPatientRecord['zip'] 				= $getPtStubRow["patient_zip"];
		$arrayPatientRecord['date_of_birth'] 	= $getPtStubDob;
		$arrayPatientRecord['sex'] 				= $getPtStubRow["patient_sex"];
		$arrayPatientRecord['homePhone'] 		= $getPtStubRow["patient_home_phone"];
		$arrayPatientRecord['workPhone'] 		= $getPtStubRow["patient_work_phone"];
		$arrayPatientRecord['imwPatientId'] 	= $getPtStubRow["imwPatientId"];
		
		if($patientIdStub) {
			$patientMatchStr = "SELECT patient_id FROM patient_data_tbl 
								WHERE patient_id = '".$patientIdStub."'
								";
		}else {
			$patientMatchStr = "SELECT patient_id FROM patient_data_tbl 
								WHERE patient_fname = '".addslashes($getPtStubFirstName)."'
								AND patient_lname 	= '".addslashes($getPtStubLastName)."'
								AND zip 			= '".addslashes($getPtStubZip)."'
								AND date_of_birth 	= '".addslashes($getPtStubDob)."'";
		}
		$patientMatchQry = imw_query($patientMatchStr) or die(imw_error());
		$patientMatchRows = imw_num_rows($patientMatchQry);
		if($patientMatchRows>0){
			$patientDataRow = imw_fetch_array($patientMatchQry);
			$objManageData->updateRecords($arrayPatientRecord, 'patient_data_tbl', 'patient_id', $patientDataRow["patient_id"]);
			$insertPatientDataId = $patientDataRow["patient_id"];
			
			if($_REQUEST['chkEpostPtExist']!='yes') {//IF CLICK ONLY ON SCAN BUTTON THEN
			?>
				<script>
					var ptStubId = '<?php echo $ptStubId;?>';
					var patientId = '<?php echo $insertPatientDataId;?>';
					var pConfirmId = '<?php echo $_REQUEST["pConfirmId"]; ?>';
					var dosScan = '<?php echo $selected_date; ?>';
					var actionDenied = '<?php echo $_REQUEST['AD']; ?>';
					
					var scanUrl	=	'admin/scanPopUp.php';
					scanUrl	+=	'?patient_id='+patientId;
					scanUrl	+=	'&pConfirmId='+pConfirmId;
					scanUrl	+=	'&ptStubId='+ptStubId;
					scanUrl	+=	'&dosScan='+dosScan;
					scanUrl	+=	(actionDenied == 'yes') ? '&AD='+actionDenied : '';
					window.open(scanUrl,'scanWin', 'width=775, height=650,location=yes,status=yes');
				</script>
				
			<?php
			}
		
		}else {
			$insertPatientDataId = $objManageData->addRecords($arrayPatientRecord, 'patient_data_tbl');
			//INSERT DEFAULT ENTRIES IN SCAN DOCUMENT TABLE 
				$formFolderArr = array('Pt. Info', 'Clinical', 'IOL');
				foreach($formFolderArr as $formFolder){
					
					$chk_insert_scan_document_qry = "select * from scan_documents where document_name = '".$formFolder."' AND patient_id = '".$insertPatientDataId."' AND confirmation_id = '0' AND dosOfScan = '".$selected_date."' AND stub_id = '".$_REQUEST["ptStubId"]."' ";
					$chk_insert_scan_document_res = imw_query($chk_insert_scan_document_qry) or die(imw_error());
					$chk_insert_scan_document_numrow = imw_num_rows($chk_insert_scan_document_res);
					
					if($chk_insert_scan_document_numrow<=0) {
						unset($arrayScanRecord);
						$arrayScanRecord['patient_id'] = $insertPatientDataId;
						$arrayScanRecord['document_name'] = $formFolder;
						$arrayScanRecord['dosOfScan'] = $selected_date;
						$arrayScanRecord['stub_id'] = $_REQUEST["ptStubId"];
						 
						$insertScanId = $objManageData->addRecords($arrayScanRecord, 'scan_documents');
				
					
						//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
						$document_encounter='';
						if($formFolder=='Pt. Info') {
							$document_encounter = 'pt_info_1';
						}else if($formFolder=='Clinical') {
							$document_encounter = 'clinical_1';
						}
						unset($arrayScanRecord);
						$arrayScanRecord['patient_id'] = $insertPatientDataId;
						$arrayScanRecord['document_name'] = $formFolder;
						$arrayScanRecord['document_id'] = $insertScanId;
						$arrayScanRecord['document_date_time'] = date('Y-m-d H:i:s');
						$arrayScanRecord['document_file_name'] = 'home_inner_front.php';
						$arrayScanRecord['document_encounter'] = $document_encounter;
						$inserIdScanLogTbl = $objManageData->addRecords($arrayScanRecord, 'scan_log_tbl');
						//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
					
					}
				}
			//END INSERT DEFAULT ENTRIES IN SCAN DOCUMENT TABLE
		}
		
		//START CODE TO UPDATE PATIENT-ID IN STUB-TABLE
			if($insertPatientDataId) {
				unset($arrayStubRecord);
				$arrayStubRecord['patient_id_stub']=$insertPatientDataId;
				$objManageData->updateRecords($arrayStubRecord, 'stub_tbl', 'stub_id', $getPtStubId);
			}
		//END CODE TO UPDATE PATIENT-ID IN STUB-TABLE
			

		if($_REQUEST['chkEpostPtExist']=='yes') {
		?>
			<script>
				var patientId = '<?php echo $insertPatientDataId;?>';
				var pConfirmId = '<?php echo $_REQUEST["pConfirmId"]; ?>';
				var spanPtAlertId = '<?php echo $_REQUEST["spanPtAlertId"]; ?>';
				var ptStubId = '<?php echo $_REQUEST["ptStubId"]; ?>';
				
				if(!pConfirmId) {
					pConfirmId = '0';
				}
				window.open('epost_new_patient.php?patient_id='+patientId+'&pConfirmId='+pConfirmId+'&stub_id='+ptStubId+'&spanPtAlertId='+spanPtAlertId,'EpostIt', 'width=900, height=430, resizable=yes');
			</script>
		
		<?php
		}else {
		
			//IF PATIENT ALEADY CONFIRMED THEN SHOW ALERT
			$chkPatientAlreadyConfirmedQry = "SELECT patientConfirmationId FROM patientconfirmation where patientId='$insertPatientDataId' and dos='$stub_dos'";
			$chkPatientAlreadyConfirmedRes = imw_query($chkPatientAlreadyConfirmedQry) or die(imw_error());
			$chkPatientAlreadyConfirmedNumRow = imw_num_rows($chkPatientAlreadyConfirmedRes);
			if($chkPatientAlreadyConfirmedNumRow>0) {
			}else {  // ELSE OPEN SCAN POPUP WINDOW

		?>
				<script>
					var ptStubId = '<?php echo $ptStubId;?>';
					var patientId = '<?php echo $insertPatientDataId;?>';
					var pConfirmId = '<?php echo $_REQUEST["pConfirmId"]; ?>';
					var dosScan = '<?php echo $selected_date; ?>';
					window.open('admin/scanPopUp.php?patient_id='+patientId+'&pConfirmId='+pConfirmId+'&ptStubId='+ptStubId+'&dosScan='+dosScan,'scanWin', 'width=775, height=650,location=yes,status=yes');
				</script>
	<?php
			}
		}
	}
//END CODE TO REGISTER THE NON SCHEDULED PATIENT(FOR SCAN) IF PATIENT DOES NOT EXIST IN PATIENT DATA

//DATE FOR PRINT REPORT
$report_display_date = $sel_month_number."-".$sel_day_number."-".$sel_year_number;
//END DATE FOR PRINT REPORT

?> 
<script>

function swap_cal_color(obj,cond){
	if(document.getElementById(obj)){
		if(cond=="Yes"){
			document.getElementById(obj).style.backgroundColor="#FBD78D";
		}else{
			document.getElementById(obj).style.backgroundColor="";
		}
	}
}
function tab_change(){
	if($("#cal")){$("#cal").show();}	
}
//START
function change_month(month_number,year_number) {
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	 }
	var url="cal_ajax.php"
	url=url+"?sel_month_number="+month_number
	url=url+"&year_now="+year_number
	
	xmlHttp.onreadystatechange=stateCalFun 
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}
function stateCalFun(){ 
	if(xmlHttp.readyState==1) {
		document.getElementById("cal_ajax_id").innerHTML='<center><img src="images/pdf_load_img.gif"></center>';
	}
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("cal_ajax_id").innerHTML=xmlHttp.responseText; 
	} 
}

//END
function today_sch(){
	parent.top.$(".loader").fadeIn('fast').show('fast'); 
	top.frames[0].location.href = 'home_inner_front.php?date_click=yes';
}
function refresh_sch(day_number,month_number,year_number) {
	parent.top.$(".loader").fadeIn('fast').show('fast'); 
	top.frames[0].location.href = 'home_inner_front.php?refresh_click=yes&day='+day_number+'&selected_month_number='+month_number+'&sel_year_number='+year_number;
}
function goToApptDate(day_number,month_number,year_number,showAllApptStatus) {
	parent.top.$(".loader").fadeIn('fast').show('fast'); 
	top.frames[0].location.href = 'home_inner_front.php?showAllApptStatus='+showAllApptStatus+'&day='+day_number+'&selected_month_number='+month_number+'&sel_year_number='+year_number;
}
function changeStatus(id, keyWord, status,showAllApptStatus,obj,findBy){
	var newVal=0;
	if(typeof(obj.value)!='undefined')
	{
		if(obj && obj.value=='Canceled') {
			if(!confirm('Are you sure you want to cancel this patient?')) {
				obj.value =	status;
				return;
			}
		}
		status=obj.value;
		newVal=1;
	}
	parent.top.$(".loader").fadeIn('fast').show('fast'); 
	var day = '<?php echo $_REQUEST["day"];?>';
	var selected_month_number = '<?php echo $_REQUEST["selected_month_number"];?>';
	var sel_year_number = '<?php echo $_REQUEST["sel_year_number"];?>';
	var sortSurgeonName = '<?php echo $_REQUEST["sortSurgeonName"];?>';
	var sortFieldName = '<?php echo $_REQUEST["sortFieldName"];?>';
	
	var sortPtAscDsc = '<?php echo $_REQUEST["sortPtAscDsc"];?>';
	var sortStAscDsc = '<?php echo $_REQUEST["sortStAscDsc"];?>';
	var sortAscDscNew='';
	if(sortPtAscDsc) {sortAscDscNew = '&sortPtAscDsc='+sortPtAscDsc;}
	if(sortStAscDsc) {sortAscDscNew = '&sortStAscDsc='+sortStAscDsc;}
	
	top.frames[0].location = 'home_inner_front.php?showAllApptStatus='+showAllApptStatus+'&txt_patient_search_id='+keyWord+'&changeStatusid='+id+'&status='+status+'&day='+day+'&selected_month_number='+selected_month_number+'&sel_year_number='+sel_year_number+'&sortSurgeonName='+sortSurgeonName+'&sortFieldName='+sortFieldName+sortAscDscNew+"&newVal="+newVal+'&findBy='+findBy;
}
function changeStatusCheckInCancel(id, keyWord, status, findBy, obj){
	var newVal=0;
	if(obj && typeof(obj)!='undefined')
	{
		if(typeof(obj.value)!='undefined' && obj.value=='Canceled') {
			if(!confirm('Are you sure you want to cancel this patient?')) {
				obj.value =	status;
				return;
			}
		}
		if(obj.value=='Canceled' || obj.value=='No Show' || obj.value=='Aborted Surgery' || obj.value=='Scheduled') {
			status=obj.value;
			newVal=1;	
		}
	
	}
	parent.top.$(".loader").fadeIn('fast').show('fast'); 
	var day = '<?php echo $_REQUEST["day"];?>';
	var selected_month_number = '<?php echo $_REQUEST["selected_month_number"];?>';
	var sel_year_number = '<?php echo $_REQUEST["sel_year_number"];?>';
	
	var sortSurgeonName = '<?php echo $_REQUEST["sortSurgeonName"];?>';
	var sortFieldName = '<?php echo $_REQUEST["sortFieldName"];?>';
	var sortPtAscDsc = '<?php echo $_REQUEST["sortPtAscDsc"];?>';
	var sortStAscDsc = '<?php echo $_REQUEST["sortStAscDsc"];?>';
	var sortAscDscNew='';
	if(sortPtAscDsc) {sortAscDscNew = '&sortPtAscDsc='+sortPtAscDsc;}
	if(sortStAscDsc) {sortAscDscNew = '&sortStAscDsc='+sortStAscDsc;}
	top.frames[0].location = 'home_inner_front.php?txt_patient_search_id='+keyWord+'&changeStatusid='+id+'&status='+status+'&day='+day+'&selected_month_number='+selected_month_number+'&sel_year_number='+sel_year_number+'&display_cal=none&display_patient_sch=none&display_patient_search=display&sortSurgeonName='+sortSurgeonName+'&sortFieldName='+sortFieldName+sortAscDscNew+"&newVal="+newVal+'&findBy='+findBy;
}

function saveComment(id, keyWord, status,commentTxt, display_patient_search, showAllApptStatus,findBy){
	parent.top.$(".loader").fadeIn('fast').show('fast'); 
	var comment = document.getElementById(commentTxt).value;
	var day = '<?php echo $_REQUEST["day"];?>';
	var selected_month_number = '<?php echo $_REQUEST["selected_month_number"];?>';
	var sel_year_number = '<?php echo $_REQUEST["sel_year_number"];?>';
	
	var sortSurgeonName = '<?php echo $_REQUEST["sortSurgeonName"];?>';
	var sortFieldName = '<?php echo $_REQUEST["sortFieldName"];?>';
	var sortPtAscDsc = '<?php echo $_REQUEST["sortPtAscDsc"];?>';
	var sortStAscDsc = '<?php echo $_REQUEST["sortStAscDsc"];?>';
	var sortAscDscNew='';
	if(sortPtAscDsc) {sortAscDscNew = '&sortPtAscDsc='+sortPtAscDsc;}
	if(sortStAscDsc) {sortAscDscNew = '&sortStAscDsc='+sortStAscDsc;}
	
	comment	=	encodeURIComponent(comment);
	//comment	=	escape(comment);
	if(display_patient_search) {
		top.frames[0].location = 'home_inner_front.php?showAllApptStatus='+showAllApptStatus+'&txt_patient_search_id='+keyWord+'&id='+id+'&comment='+comment+'&display_cal=none&display_patient_sch=none&display_patient_search=display&sortSurgeonName='+sortSurgeonName+'&sortFieldName='+sortFieldName+sortAscDscNew+'&findBy='+findBy;
	}else {
		top.frames[0].location = 'home_inner_front.php?showAllApptStatus='+showAllApptStatus+'&txt_patient_search_id='+keyWord+'&id='+id+'&comment='+comment+'&day='+day+'&selected_month_number='+selected_month_number+'&sel_year_number='+sel_year_number+'&sortSurgeonName='+sortSurgeonName+'&sortFieldName='+sortFieldName+sortAscDscNew+'&findBy='+findBy;
	}
}
var stub_dos='';
var imwPatientId='';
function chkPatientExist(id,keyWord,patientId,stub_dos,pConfirmId,imwPatientId,showAllApptStatus,actionDenied,findBy) {
	
	var day = '<?php echo $_REQUEST["day"];?>';
	var selected_month_number = '<?php echo $_REQUEST["selected_month_number"];?>';
	var sel_year_number = '<?php echo $_REQUEST["sel_year_number"];?>';
	var sortSurgeonName = '<?php echo $_REQUEST["sortSurgeonName"];?>';
	var sortFieldName = '<?php echo $_REQUEST["sortFieldName"];?>';
	var sortPtAscDsc = '<?php echo $_REQUEST["sortPtAscDsc"];?>';
	var sortStAscDsc = '<?php echo $_REQUEST["sortStAscDsc"];?>';
	var sortAscDscNew='';
	if(sortPtAscDsc) {sortAscDscNew = '&sortPtAscDsc='+sortPtAscDsc;}
	if(sortStAscDsc) {sortAscDscNew = '&sortStAscDsc='+sortStAscDsc;}
	var dosScan = '<?php echo $selected_date; ?>';
	
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	
	var	W	=	( SW > 1200 ) ?  1200	: SW ;
	var	H	=	W * 0.65
	
	var L	=	(SW - W ) / 2  ;
    var T	= 	(SH - H ) / 2 - 50 ; 
	//alert('Screen Width : ' + SW + '\n Screen Height : ' + SH + '\nWindow Width : ' + W + '\n Window Height : ' + H + '\n Left : ' + L + '\n Top : ' + T );
	
	if(patientId) {
		
		var scanUrl	=	'admin/scanPopUp.php';
		scanUrl	+=	'?patient_id='+patientId;		
		scanUrl	+=	'&pConfirmId='+pConfirmId;		
		scanUrl	+=	'&ptStubId='+id;
		scanUrl	+=	'&dosScan='+dosScan;		
		scanUrl	+=	(actionDenied == 'yes') ? '&AD='+actionDenied : '';		
		
		window.open(scanUrl,'scanWin', 'width='+W+', height='+ H+',location=no, status=no, resizable=no, scrollbars=no, resizable=no, top='+T+', left='+L);
	}else {
		parent.top.$(".loader").fadeIn('fast').show('fast'); 
		if(keyWord) {
			top.frames[0].location = 'home_inner_front.php?showAllApptStatus='+showAllApptStatus+'&txt_patient_search_id='+keyWord+'&ptStubId='+id+'&pConfirmId='+pConfirmId+'&day='+day+'&selected_month_number='+selected_month_number+'&sel_year_number='+sel_year_number+'&display_cal=none&display_patient_sch=none&display_patient_search=display&chkPtExist=yes&stub_dos='+stub_dos+'&sortSurgeonName='+sortSurgeonName+'&sortFieldName='+sortFieldName+sortAscDscNew+'&AD='+actionDenied+'&findBy='+findBy
		}else {
			top.frames[0].location = 'home_inner_front.php?showAllApptStatus='+showAllApptStatus+'&ptStubId='+id+'&pConfirmId='+pConfirmId+'&day='+day+'&selected_month_number='+selected_month_number+'&sel_year_number='+sel_year_number+'&chkPtExist=yes&stub_dos='+stub_dos+'&sortSurgeonName='+sortSurgeonName+'&sortFieldName='+sortFieldName+sortAscDscNew+'&AD='+actionDenied;
		}
	}	
}
function popupReport(date1,showAllApptStatus,dType)
{
	var win_name	=	'Day Report ' + date1 ;
 	window.open('day_reportpop.php?date12='+date1+'&showAllApptStatus='+showAllApptStatus+'&dType=' + dType,win_name );
 
}
function popuplabel(date1,showAllApptStatus)
{
  window.open('label_popup.php?showAllApptStatus='+showAllApptStatus+'&date12='+date1,'','width=450, height=368,resizable=1');
}
function popupNarcoticsLogSheet(date1){
  window.open('narcotics_log_sheet.php?dateOS='+date1,'winlogsheet','width=1250, height=800,resizable=1');
}
function popupPreOpOrder(date1,showAllApptStatus,request){
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	
	var	W	=	( SW > 1200 ) ?  1200	: SW ;
	var	H	=	W * 0.65
	
	var L	=	(SW - W ) / 2  ;
    var T	= 	(SH - H ) / 2 - 50 ; 
	//alert('Screen Width : ' + SW + '\n Screen Height : ' + SH + '\nWindow Width : ' + W + '\n Window Height : ' + H + '\n Left : ' + L + '\n Top : ' + T );
	if(request == 'Surgeon')
	{
		window.open('sign_all_pre_op_order.php?showAllApptStatus='+showAllApptStatus+'&dos='+date1,'signAllPreOpOrderWin', 'width='+W+', height='+ H+',location=no, status=no, resizable=yes, scrollbars=yes,  top='+T+', left='+L);
	}
	else if(request == 'Nurse')
	{
		window.open('sign_pre_post_order_noted.php?showAllApptStatus='+showAllApptStatus+'&dos='+date1,'signPrePostOpOrdersNotedWin', 'width='+W+', height='+ H+',location=no, status=no, resizable=yes, scrollbars=yes,  top='+T+', left='+L);
	}
	
}
//START FUNCTION TO SORT RECORD IN SCEDULER
var sortPtAscDsc='';
function sortPtInfo(surgeonNameWithoutSpace,sortFieldName,sortPtAscDsc,sortRequire) {
	parent.top.$(".loader").fadeIn('fast').show('fast'); 
	var sortSurgeonName = '<?php echo $_REQUEST["sortSurgeonName"];?>';
	var sortPtAscDscNew = 'asc';
	if(surgeonNameWithoutSpace==sortSurgeonName) {
		sortPtAscDscNew = sortPtAscDsc;
	}
	var currLocation=document.location.href; 
	currLocation = currLocation.replace('&chkPtExist=yes','');
	currLocation = currLocation.replace('&chkEpostPtExist=yes','');
	var currLocationNew=currLocation.split('&sortSurgeonName'); 
	var sortAscDscNew='';
	if(sortFieldName=='ptname') {
		sortAscDscNew = '&sortPtAscDsc='+sortPtAscDscNew;
	}else if(sortFieldName=='stime') {
		sortAscDscNew = '&sortStAscDsc='+sortPtAscDscNew;
	}
	
	var newLocation = currLocationNew[0]+'&sortSurgeonName='+surgeonNameWithoutSpace+'&sortFieldName='+sortFieldName+sortAscDscNew+'&sortRequire=yes';
	newLocation = newLocation.replace('home_inner_front.php&amp;','home_inner_front.php?');
	newLocation = newLocation.replace('refresh_click=yes&amp;','');
	newLocation = newLocation.replace('home_inner_front.php&;','home_inner_front.php?');
	newLocation = newLocation.replace('home_inner_front.php&','home_inner_front.php?');
	newLocation = newLocation.replace('refresh_click=yes&','');
	
	location.href=newLocation;
}
//END FUNCTION TO SORT RECORD IN SCEDULER
$(document).ready(function(e) {
   
     $("#printSelect,#a_allactivecancelled").click(function() {
		var $this	=	$(this) ;
		var Dd		=	$this.siblings().closest('.dropdown-menu') ;
		var Pt		=	$this.parent('.dropdown');
		
		Pt.children().siblings('.dropdown-menu', this).stop( true, true ).slideToggle("fast");
		Pt.siblings().removeClass('active');
		Pt.siblings().children('.dropdown-menu').hide();						
		Dd.addClass('active');
		
		Dd.children('li').on('click',function(){ 
			$(this).closest('.dropdown-menu').slideUp('fast');
		});
		
	});
	
	/*
		$(".dropdown-menu #a_allactivecancelled").click(function(){
			$("#allactivecancelled").hide();	
		});
	
		$(".dropdown-menu .radioFilter").prop('checked','true',function(){
			$("#allactivecancelled").hide();	
		});
	*/
	
	$(document).ready(function() {
		/* ** * * * ** CALENDAR *  ** * * * * *  * */
		$(' #cal_btn').on('click',function(){ 
							  
				$('#my_modal_cal').modal({
					show: true,
					backdrop: true,
					keyboard: true
				});
	  
		});
		$("#cal").ionCalendar({
								 // language
			sundayFirst: true,             // first week day
			years: "80",                    // years diapason
			format: "MM.DD.YYYY",           // date format
			onClick: function(datestr){        // click on day returns date
				var dtArr = datestr.split(".");
				var month_new=dtArr[0];
				var day_new=dtArr[1];
				var year_new=dtArr[2];
				parent.top.$(".loader").fadeIn('fast').show('fast'); 
				
				top.frames[0].location='home_inner_front.php?day='+day_new+'&selected_month_number='+month_new+'&sel_year_number='+year_new+'&date_click=yes';
				
			}
		});
		/********************************************** CALENDAR *********************************************/
	});	
	
});
</script>
<?php
if($_GET["display_patient_sch"]=="none") {
	$display_patient_schedule = "none";
}else {
	$display_patient_schedule = "display";
}


//GET SURGERY CENTER NAME
	$surgerycenter_name_qry = "select `name`,`peer_review` from surgerycenter where surgeryCenterId=1";
	$surgerycenter_name_res = imw_query($surgerycenter_name_qry) or die($surgerycenter_name_qry.imw_error());
	$surgerycenter_name_row = imw_fetch_array($surgerycenter_name_res);
	$surgerycenter_name = $surgerycenter_name_row["name"];
	$surgerycenter_peer_review = $surgerycenter_name_row["peer_review"];
//END GET SURGERY CENTER NAME

//START GET PRACTICE NAME OF ALL USERS
	$userPracQry = "SELECT usersId,practiceName,fname, mname, lname FROM users
					WHERE deleteStatus != 'Yes' AND practiceName !=''";
	$userPracRes = imw_query($userPracQry) or die($userPracQry.imw_error());
	if(imw_num_rows($userPracRes)>0) {
		while($userPracRow 			= imw_fetch_array($userPracRes)) {
			$pracUserId 			= $userPracRow["usersId"];
			$pracUserFname 			= trim($userPracRow["fname"]);
			$pracUserMname 			= trim($userPracRow["mname"]);
			$pracUserLname 			= trim($userPracRow["lname"]);
			$pracUserPracticeName 	= stripslashes($userPracRow["practiceName"]);
			$userPracNameArr["user_id"][$pracUserId] = $pracUserPracticeName;
			$userPracNameArr["user_name"][strtolower($pracUserFname)][strtolower($pracUserMname)][strtolower($pracUserLname)] = $pracUserPracticeName;
		}
	}

//END GET PRACTICE NAME OF ALL USERS

//COMMENT THIS CODE TEMPRARILY
	/*
	//GET COUNT OF UNREAD MESSAGES FROM msg_tbl
		
		
		$getMsgQry = "select msg_user_id from msg_tbl 
						   WHERE read_status=''
						   AND msg_delete_status != 'true'
						   AND  msg_user_id = '".$loginUser."'";
		$getMsgRes = imw_query($getMsgQry) or die(imw_error());
		$getMsgNumRow = imw_num_rows($getMsgRes);
		if($getMsgNumRow>0) {
			$getCountMsg = '('.$getMsgNumRow.')';
		}
		
	//END GET COUNT OF UNREAD MESSAGES FROM msg_tbl
	
	//GET COUNT OF ALL MESSAGES OF LOGGED IN USER
		$msgTblCountQry = "select COUNT(msg_user_id) AS msgTblCountNumRow from msg_tbl where msg_user_id = '".$loginUser."' AND msg_delete_status != 'true'";
		$msgTblCountRes = imw_query($msgTblCountQry) or die(imw_error());
		if(imw_num_rows($msgTblCountRes)>0) {
			$msgTblCountRow = imw_fetch_array($msgTblCountRes);
			$msgTblCountNumRow = $msgTblCountRow['msgTblCountNumRow'];
		}
	//GET COUNT OF ALL MESSAGES OF LOGGED IN USER
	*/
//END COMMENT THIS CODE TEMPRARILY	
	
	
//SET VARIABLE OF ACCESS DENIED
$accessDeniedCoordinator = 'Access denied to Coordinator';
$chartFinalizedAlert = 'Chart Finalized';
//END SET VARIABLE OF ACCESS DENIED

$showAllApptStatusQry = "";
if($showAllApptStatus=="Active") {
	$showAllApptStatusQry = " AND  patient_status NOT IN('Canceled','No Show','Aborted Surgery')";	
}else if($showAllApptStatus=="Canceled") {
	$showAllApptStatusQry = " AND  patient_status='Canceled'";	
}else if($showAllApptStatus=="No Show") {
	$showAllApptStatusQry = " AND  patient_status='No Show'";	
}else if($showAllApptStatus=="Aborted Surgery") {
	$showAllApptStatusQry = " AND  patient_status='Aborted Surgery'";	
}

if($_GET["display_patient_search"]=="display") {
	$display_patient_search_result = "display";
	$display_patient_label = "Patient Search Results";
}else {
	$display_patient_search_result = "none";
	$display_patient_label = "Patient Surgery Schedule ".$display_date;
}

?>
<!-- EPOST Start Here -->
<?php include 'epostHtml.php'; ?>
<!-- EPOST End Here -->

<!--START NEW DESIGN-->

        <div class="">
        	<div class="scheduler_table_Complete ">
            	<div class="subtracting-head">
                	<div class="buttons-sch new_buttonbar">
												<?php if($_SESSION['loginUserType']=='Nurse') { ?>
                        		<a class="btn btn-default" title="Pre/Post Op Orders Noted" href="javascript:popupPreOpOrder('<?php echo $report_display_date;?>','<?php echo $showAllApptStatus;?>','Nurse');"> <b class="fa fa-stethoscope"></b> Pre/Post Op Orders Noted</a>
                        <?php } ?>
												<?php if($_SESSION['loginUserType']=='Surgeon') { ?>
                        		<a class="btn btn-default" title="Pre-Op Orders" href="javascript:popupPreOpOrder('<?php echo $report_display_date;?>','<?php echo $showAllApptStatus;?>','Surgeon');"> <b class="fa fa-stethoscope"></b> Pre/Post Op Orders</a>
                        <?php } ?>
						<a class="btn btn-default" title="Name Tag" href="javascript:popuplabel('<?php echo $report_display_date;?>','<?php echo $showAllApptStatus;?>','Surgeon');"><b class="fa fa-print"></b> Name Tag</a>
						
                    	<a class="btn btn-default" title="Refresh" onClick="refresh_sch('<?php echo $sel_day_number?>','<?php echo $sel_month_number?>','<?php echo $sel_year_number?>');">
                        	<b class="fa fa-refresh"></b> Refresh
						</a>
                        
                        <a class="btn btn-default " title="Calendar" id="cal_btn">
                        	<b class="fa fa-calendar"></b>	   Calendar
						</a>
                        
                        <a class="btn btn-default " title="Today" onClick="today_sch();">Today</a>
                        
                        <div class="dropdown new_auto_Dropdown">
                        	
                            <a id="a_allactivecancelled" class="btn btn-info dropdown-toggle" data-target=".dropdown-menu" href="javascript:void(0)">
                               <b class="fa fa-filter"></b>	Filter  <i class="caret"></i> 
                             </a>
                             <ul class="dropdown-menu" id="allactivecancelled">
                                <li>
                                    <a href="javascript:void(0)"><label><input class="radioFilter" type="radio" name="showAllApptStatus" value="All" onClick="javascript:goToApptDate(<?php echo $sel_day_number;?>,<?php echo $sel_month_number;?>,<?php echo $sel_year_number;?>,this.value);" <?php if($showAllApptStatus=="") 		{ echo "checked"; }?> /> All   </label>  </a> 
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><label><input class="radioFilter" type="radio" name="showAllApptStatus" value="Active" 	onClick="javascript:goToApptDate(<?php echo $sel_day_number;?>,<?php echo $sel_month_number;?>,<?php echo $sel_year_number;?>,this.value);" <?php if($showAllApptStatus=="Active") 	{ echo "checked"; }?> /> Active   </label>  </a> 
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><label><input class="radioFilter" type="radio" name="showAllApptStatus" value="Canceled"   onClick="javascript:goToApptDate(<?php echo $sel_day_number;?>,<?php echo $sel_month_number;?>,<?php echo $sel_year_number;?>,this.value);" <?php if($showAllApptStatus=="Canceled"){ echo "checked"; }?>/> Cancelled </label>   </a> 
                                </li>
                                
                                <li>
                                    <a href="javascript:void(0)"><label><input class="radioFilter" type="radio" name="showAllApptStatus" value="No Show"   onClick="javascript:goToApptDate(<?php echo $sel_day_number;?>,<?php echo $sel_month_number;?>,<?php echo $sel_year_number;?>,this.value);" <?php if($showAllApptStatus=="No Show"){ echo "checked"; }?>/> No&nbsp;Show </label>   </a> 
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><label><input class="radioFilter" type="radio" name="showAllApptStatus" value="Aborted Surgery"   onClick="javascript:goToApptDate(<?php echo $sel_day_number;?>,<?php echo $sel_month_number;?>,<?php echo $sel_year_number;?>,this.value);" <?php if($showAllApptStatus=="Aborted Surgery"){ echo "checked"; }?>/> Aborted&nbsp;Surgery </label>   </a> 
                                </li>
                             </ul>
                        </div>   
                         
                        <div class="dropdown new_auto_Dropdown">
                        	<a id="printSelect" class="btn btn-primary dropdown-toggle" data-target="#printdropdown" href="javascript:void(0)">
                            	<b class="fa fa-filter"></b>	Print <i class="caret"></i> 
                           	</a>  
                            
                            <ul class="dropdown-menu" id="printdropdown" >
                            	<li><a href="javascript:void(0)" onClick="popupReport('<?php echo $report_display_date;?>','<?php echo $showAllApptStatus;?>','summary');"><label style="cursor:pointer">Summary</label></a></li>
                                <li><a href="javascript:void(0)" onClick="popupReport('<?php echo $report_display_date;?>','<?php echo $showAllApptStatus;?>','details');"><label style="cursor:pointer">Details</label></a> 
                                </li>
                       		</ul>
                        
                 		</div>            
                 	</div>	
                    <div class="table_head_sch text-left">
                        <span class="rob">
                            <?php echo $display_patient_label; ?>
                        </span>
                    </div>
                    
                    
                    </div>
                    
                    
                    
                    <div class="no-more-tables scrollable_yes " id="patient_info" style="display:<?php echo $display_patient_schedule;?>;">
                    	
                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf table-hover">
                        
                        <?php
							
							//START CODE FOR IMW
							if(!$imwSwitchFile) {
								$imwSwitchFile = "sync.php";
							}
							if($selected_date<=date('Y-m-d') && $_REQUEST['refresh_click']=='yes') { //CURRENT DATE	
								include($imwSwitchFile);
							}else if($selected_date>date('Y-m-d') && $_REQUEST['date_click'] == 'yes') { //FUTURE AND PAST DATE
								include($imwSwitchFile);
							}
							//END CODE FOR IMW
					
							//CODE FOR STUB DATABASE VALUE
							
							//START CODE TO SHOW RECORD RELATED TO SPECIFIC USER
							$surgeonSpecificQry="";
							
							if($userType=='Coordinator' && $coordinatorType!='Master')
							{ //IF USER TYPE IS Coordinator AND HE IS NOT MASTER THEN SHOW RECORD RELATED TO HIS PRACTICENAME
								$AndUserPracticeNameQry=getPracticeUser($practiceName,"AND","");  
								$practiceSurgeonQry = "select fname, mname, lname from users WHERE practiceName!='' ".$AndUserPracticeNameQry;
								$practiceSurgeonRes = imw_query($practiceSurgeonQry) or die(imw_error());
								$practiceSurgeonNumRow = imw_num_rows($practiceSurgeonRes);
								if($practiceSurgeonNumRow>0){
									$practiceSurgeonFname=array();
									$practiceSurgeonMname=array();
									$practiceSurgeonLname=array();
									while($practiceSurgeonRow = imw_fetch_array($practiceSurgeonRes)) {
										$practiceSurgeonFname[] = addslashes(stripslashes($practiceSurgeonRow['fname'])); // in case of string match with "IN" Query
										$practiceSurgeonMname[] = addslashes(stripslashes($practiceSurgeonRow['mname']));
										$practiceSurgeonLname[] = addslashes(stripslashes($practiceSurgeonRow['lname']));
									}
									$practiceSurgeonFnameImplode = implode("','",$practiceSurgeonFname);
									$practiceSurgeonMnameImplode = implode("','",$practiceSurgeonMname);
									$practiceSurgeonLnameImplode = implode("','",$practiceSurgeonLname);
									$surgeonSpecificQry = "AND surgeon_fname in('".$practiceSurgeonFnameImplode."') AND surgeon_lname in('".$practiceSurgeonLnameImplode."')";
								}
							}
							//END CODE TO SHOW RECORD RELATED TO SPECIFIC USER
							
							$stub_tbl_group_query = "select surgeon_fname, surgeon_mname, surgeon_lname from stub_tbl where dos = '$selected_date' $surgeonSpecificQry $fac_con $showAllApptStatusQry ORDER BY surgery_time, surgeon_fname";
							
							$stub_tbl_group_res = imw_query($stub_tbl_group_query) or die(imw_error());
							$stub_tbl_group_NumRow = imw_num_rows($stub_tbl_group_res);
							
							if($stub_tbl_group_NumRow>0){
								$stub_tbl_groupTemp = array();
								
								while($stub_tbl_group_row = imw_fetch_array($stub_tbl_group_res)) {
							
									$stub_surgeon_name = "";
									$stub_tbl_group_surgeon_name = "";
									$stub_tbl_group_surgeon_fname = trim(stripslashes($stub_tbl_group_row['surgeon_fname']));
									$stub_tbl_group_surgeon_mname = trim(stripslashes($stub_tbl_group_row['surgeon_mname']));
									$stub_tbl_group_surgeon_lname = trim(stripslashes($stub_tbl_group_row['surgeon_lname']));
									
									$stub_surgeon_name = trim(stripslashes($stub_tbl_group_row['surgeon_fname'])).' '.trim(stripslashes($stub_tbl_group_row['surgeon_mname'])).' '.trim(stripslashes($stub_tbl_group_row['surgeon_lname']));
									$surgeonNameWithoutSpace = trim(stripslashes($stub_tbl_group_row['surgeon_fname'])).trim(stripslashes($stub_tbl_group_row['surgeon_mname'])).trim(stripslashes($stub_tbl_group_row['surgeon_lname']));
									
									
									//START CODE TO SORT PATIENT-RECORD WITH IN SURGEON-NAME
									$sortSurgeonName= $_REQUEST['sortSurgeonName'];
									$sortFieldName 	= $_REQUEST['sortFieldName'];
							
									$sortPtClassName = 'link_ptInfo_new';
									$sortSrgTimeClassName = 'link_ptInfo_new';
									$orderByFieldNameStubQry = 'order by stub_tbl.iasc_facility_id, surgery_time';
							
									$sortPtAscDsc 	= $_REQUEST['sortPtAscDsc'];
									$sortStAscDsc 	= $_REQUEST['sortStAscDsc'];
									
									$sortRequire	= $_REQUEST['sortRequire'];
									
									$orderPtAscDesc = ($sortPtAscDsc=='asc')	?	'desc'	:	'asc';
									$orderStAscDesc = ($sortStAscDsc=='asc')	?	'desc'	:	'asc';
									
									
									if($surgeonNameWithoutSpace == $sortSurgeonName && $sortFieldName == 'ptname')
									{
										$orderByFieldNameStubQry = 'order by iasc_facility_id, patient_last_name '.$sortPtAscDsc.', patient_first_name '.$sortPtAscDsc;
										$sortPtClassName='link_ptInfo_newBack';
									}else if($surgeonNameWithoutSpace==$sortSurgeonName && $sortFieldName=='stime') {  
										$orderByFieldNameStubQry = 'order by iasc_facility_id, surgery_time '.$sortStAscDsc;
										$sortSrgTimeClassName='link_ptInfo_newBack';
									}
									//END CODE TO SORT PATIENT-RECORD WITH IN SURGEON-NAME
									
									
									if(!in_array($stub_surgeon_name,$stub_tbl_groupTemp)) 
									{
										
						?>
		                     		<thead class="cf">
                                        <tr class="caption"><td colspan="13"  class="text-center rob"> Surgeon   &nbsp;<b>Dr. <?php echo $stub_surgeon_name;?></b></td></tr>
                                
                                		<tr>
                                        	<th class="text-center" colspan="3">Signed</th>
                                            <th  class="text-left"><a href="#"  class="<?php echo $sortPtClassName;?>" onClick="javascript:sortPtInfo('<?php echo $surgeonNameWithoutSpace;?>','ptname','<?php echo $orderPtAscDesc;?>','yes');">Patient Name</a></th>
                                            <th class="text-left nowrap">Pick Up</th>
                                            <th class="text-left">Arrival</th>
                                            <th class="text-left"><a href="javascript:void(0);"  class="<?php echo $sortSrgTimeClassName;?>" onClick="javascript:sortPtInfo('<?php echo $surgeonNameWithoutSpace;?>','stime','<?php echo $orderStAscDesc;?>','yes');">Surgery</a></th>
                                            <th class="text-left">Procedure</th>
                                            <th class="text-left">Status</th>
                                            <th class="text-left">Comments</th>
                                            <th class="text-center">Scan</th>
                                            <th class="text-center">Edit</th>
                                            <th class="text-center">Epost</th>
                                		</tr>
                                
                            		</thead>
                            		<tbody>
                                    
						<?php 
									
									$stub_tbl_groupTemp[] = $stub_surgeon_name;
									
									$stub_tbl_query = "select *, DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format from stub_tbl where surgeon_fname = '".addslashes($stub_tbl_group_surgeon_fname)."' and surgeon_mname = '".addslashes($stub_tbl_group_surgeon_mname)."' and surgeon_lname = '".addslashes($stub_tbl_group_surgeon_lname)."' and dos = '$selected_date' $fac_con $showAllApptStatusQry $orderByFieldNameStubQry";
									$stub_tbl_res = imw_query($stub_tbl_query) or die($stub_tbl_query.imw_error());
									$incr_stub = 0;
									while($stub_tbl_row = imw_fetch_array($stub_tbl_res))
									{
										$incr_stub++;
										
										//CODE TO SET THE TIME
										$stub_tbl_surgery_time 	= $objManageData->getTmFormat($stub_tbl_row['surgery_time']);//getInterfaceTime($stub_tbl_row['surgery_time']);
										$stub_tbl_pickup_time 	= $objManageData->getTmFormat($stub_tbl_row['pickup_time']);//getInterfaceTime($stub_tbl_row['pickup_time']);
										$stub_tbl_arrival_time 	= $objManageData->getTmFormat($stub_tbl_row['arrival_time']);//getInterfaceTime($stub_tbl_row['arrival_time']);
										//CODE TO SET THE TIME								
								
										$stub_tbl_stub_id 						= $stub_tbl_row['stub_id'];
										$stub_tbl_stub_dos 						= $stub_tbl_row['dos'];
										$stub_tbl_patient_first_name 			= $stub_tbl_row['patient_first_name'];
										$stub_tbl_patient_middle_name 			= $stub_tbl_row['patient_middle_name'];
										$stub_tbl_patient_last_name 			= $stub_tbl_row['patient_last_name'];
										$stub_tbl_patient_status 				= $stub_tbl_row['patient_status'];
										$stub_tbl_patient_CheckedInTime 		= $objManageData->getTmFormat($stub_tbl_row['checked_in_time']);
										$stub_tbl_patient_CheckedOutTime 		= $objManageData->getTmFormat($stub_tbl_row['checked_out_time']);
										$stub_tbl_patient_dob 					= $stub_tbl_row['patient_dob'];
										$stub_tbl_patient_dob_format			= $stub_tbl_row['patient_dob_format'];
										
										$confirmation_id 						= $stub_tbl_row['patient_confirmation_id'];
										$confPatientId 							= $stub_tbl_row['patient_id_stub'];
								
										$stub_tbl_patient_name 					= $stub_tbl_patient_last_name.", ".$stub_tbl_patient_first_name;//." ".$stub_tbl_patient_middle_name;
										if(trim($stub_tbl_patient_middle_name)) {
											$stub_tbl_patient_name 				= $stub_tbl_patient_name." ".trim($stub_tbl_patient_middle_name);	
										}
										if(trim($stub_tbl_patient_dob_format)!="00/00/0000" && trim($stub_tbl_patient_dob_format)!=0) {
											$stub_tbl_patient_name 				= $stub_tbl_patient_name." (".trim($stub_tbl_patient_dob_format).")";	
										}
										$stub_tbl_patient_primary_procedure 	= $stub_tbl_row['patient_primary_procedure'];
										$stub_tbl_patient_secondary_procedure	= $stub_tbl_row['patient_secondary_procedure'];
										$stub_tbl_patient_tertiary_procedure	= $stub_tbl_row['patient_tertiary_procedure'];
										$stub_tbl_comment 						= stripslashes($stub_tbl_row['comment']);
										$stub_tbl_site 							= $stub_tbl_row['site'];
										$stub_tbl_secondary_site 				= $stub_tbl_row['stub_secondary_site'];
										$stub_tbl_tertiary_site 				= $stub_tbl_row['stub_tertiary_site'];
										$stub_tbl_chartSignedBySurgeon 			= $stub_tbl_row['chartSignedBySurgeon'];
										$stub_tbl_chartSignedByNurse 			= $stub_tbl_row['chartSignedByNurse'];
										$stub_tbl_chartSignedByAnes 			= $stub_tbl_row['chartSignedByAnes'];
										$stub_tbl_recentChartSaved 				= $stub_tbl_row['recentChartSaved'];
								
										$brTagSec=$brTagTer="";
										if(trim($stub_tbl_patient_primary_procedure) && trim($stub_tbl_patient_secondary_procedure)) {
											$brTagSec = "<br>";	
										}
										if(trim($stub_tbl_patient_primary_procedure) && trim($stub_tbl_patient_tertiary_procedure)) {
											$brTagTer = "<br>";	
										}
										
										// Start change site to OS/OD/OU
												$primarySite	=	$secondarySite	=	$tertiarySite	=	'';
												if($stub_tbl_site == 'left'){
													$primarySite	=	'OS';
												}else if($stub_tbl_site == 'right'){
													$primarySite	=	'OD';
												}else if($stub_tbl_site=='both'){
													$primarySite	=	'OU';
												}else if($stub_tbl_site=='left upper lid'){
													$primarySite	=	'LUL';
												}else if($stub_tbl_site=='left lower lid'){
													$primarySite	=	'LLL';
												}else if($stub_tbl_site=='right upper lid'){
													$primarySite	=	'RUL';
												}else if($stub_tbl_site=='right lower lid'){
													$primarySite	=	'RLL';
												}else if($stub_tbl_site=='bilateral upper lid'){
													$primarySite	=	'BUL';
												}else if($stub_tbl_site=='bilateral lower lid'){
													$primarySite	=	'BLL';
												}
												
												$stub_tbl_secondary_site	=	($stub_tbl_secondary_site)	?	$stub_tbl_secondary_site	:	$stub_tbl_site;
												if($stub_tbl_secondary_site == 'left'){
													$secondarySite	=	'OS';
												}else if($stub_tbl_secondary_site == 'right'){
													$secondarySite	=	'OD';
												}else if($stub_tbl_secondary_site == 'both'){
													$secondarySite	=	'OU';
												}else if($stub_tbl_secondary_site == 'left upper lid'){
													$secondarySite	=	'LUL';
												}else if($stub_tbl_secondary_site == 'left lower lid'){
													$secondarySite	=	'LLL';
												}else if($stub_tbl_secondary_site == 'right upper lid'){
													$secondarySite	=	'RUL';
												}else if($stub_tbl_secondary_site == 'right lower lid'){
													$secondarySite	=	'RLL';
												}else if($stub_tbl_secondary_site == 'bilateral upper lid'){
													$secondarySite	=	'BUL';
												}else if($stub_tbl_secondary_site == 'bilateral lower lid'){
													$secondarySite	=	'BLL';
												}
												
												$stub_tbl_tertiary_site	=	($stub_tbl_tertiary_site)	?	$stub_tbl_tertiary_site	:	$stub_tbl_site;
												if($stub_tbl_tertiary_site == 'left'){
													$tertiarySite	=	'OS';
												}else if($stub_tbl_tertiary_site == 'right'){
													$tertiarySite	=	'OD';
												}else if($stub_tbl_tertiary_site == 'both'){
													$tertiarySite	=	'OU';
												}else if($stub_tbl_tertiary_site == 'left upper lid'){
													$tertiarySite	=	'LUL';
												}else if($stub_tbl_tertiary_site == 'left lower lid'){
													$tertiarySite	=	'LLL';
												}else if($stub_tbl_tertiary_site == 'right upper lid'){
													$tertiarySite	=	'RUL';
												}else if($stub_tbl_tertiary_site == 'right lower lid'){
													$tertiarySite	=	'RLL';
												}else if($stub_tbl_tertiary_site == 'bilateral upper lid'){
													$tertiarySite	=	'BUL';
												}else if($stub_tbl_tertiary_site == 'bilateral lower lid'){
													$tertiarySite	=	'BLL';
												}
												// End change site to OS/OD/OU
												
												//START APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
												$stub_tbl_patient_secondary_procedure_eye = $stub_tbl_patient_tertiary_procedure_eye = '';
												$stub_tbl_patient_primary_procedure 			= $stub_tbl_patient_primary_procedure.' '.$primarySite;
												$stub_tbl_patient_secondary_procedure_eye	= $stub_tbl_patient_secondary_procedure.' '.$secondarySite;
												$stub_tbl_patient_tertiary_procedure_eye 	= $stub_tbl_patient_tertiary_procedure.' '.$tertiarySite;	
												
												$stub_tbl_patient_primary_procedure 	= trim($stub_tbl_patient_primary_procedure);
												$stub_tbl_patient_secondary_procedure	= trim($stub_tbl_patient_secondary_procedure);
												$stub_tbl_patient_tertiary_procedure	= trim($stub_tbl_patient_tertiary_procedure);
												
												if(!$stub_tbl_patient_secondary_procedure) {
													$stub_tbl_patient_secondary_procedure_eye 	= '';	
												}
												if(!$stub_tbl_patient_tertiary_procedure) {
													$stub_tbl_patient_tertiary_procedure_eye 	= '';	
												}
										//END APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
								
										
										
										//START CODE TO FIND PATIENT ID (IF EXIST) 
										$chartFinalizeStatus="";
										$getPtIdQry 	= "SELECT patientId,finalize_status  FROM patientconfirmation WHERE patientConfirmationId='$confirmation_id'";
										$getPtIdRes 	= imw_query($getPtIdQry) or die(imw_error());
										$getPtIdNumRow 	= imw_num_rows($getPtIdRes);
										
										if($getPtIdNumRow>0) {
											
											$getPtIdRow = imw_fetch_array($getPtIdRes);
											
											$chartFinalizeStatus = $getPtIdRow['finalize_status'];
										
											if($confirmation_id && !$confPatientId) {
												$confPatientId = $getPtIdRow['patientId'];
											}
										
										}
										
										
										if($confPatientId) {
											$patientMatchSearchStr = "SELECT patient_id,imwPatientId FROM patient_data_tbl 
																WHERE patient_id = '".$confPatientId."'
																";
										}else {//AND zip 			= '".addslashes($stub_tbl_row['patient_middle_name'])."'
											$patientMatchSearchStr = "SELECT patient_id,imwPatientId FROM patient_data_tbl 
																WHERE patient_fname = '".addslashes($stub_tbl_patient_first_name)."'
																AND patient_lname 	= '".addslashes($stub_tbl_patient_last_name)."'
																AND zip 			= '".addslashes($stub_tbl_row['patient_zip'])."'
																AND date_of_birth 	= '".$stub_tbl_patient_dob."'
																ORDER BY patient_id DESC
																";
									
										}
										$patientMatchSearchQry = imw_query($patientMatchSearchStr);
										$patientMatchSearchNumRows = imw_num_rows($patientMatchSearchQry);
										$patientDataSearchRow = imw_fetch_array($patientMatchSearchQry);

										if($patientMatchSearchNumRows>0){
											$patientPatientSearchDataId = $patientDataSearchRow["patient_id"];
											$patientPatientSearchImwPatientId = $patientDataSearchRow["imwPatientId"];
										}else {
											$patientPatientSearchDataId = "";
											$patientPatientSearchImwPatientId = "";
										}
									
										//END CODE TO FIND PATIENT ID (IF EXIST)
								
										
										//START CODE TO CHECK IF ANY ENTRY EXIST IN scan_upload_tbl (OR IN iolink_scan_consent table)
										$andDosScanUploadQry = $andEpostedQry='';
										if(!$confirmation_id) {
											$andDosScanUploadQry = " AND dosOfScan = '".$selected_date."' AND stub_id = '".$stub_tbl_stub_id."' ";
											$andEpostedQry = " AND stub_id = '".$stub_tbl_stub_id."' ";
										}
										$patientPatientSearchScanStr = "SELECT scan_upload_id FROM scan_upload_tbl 
														WHERE patient_id = '$patientPatientSearchDataId'
														 AND  patient_id != ''
														 AND  patient_id != '0'
														 $andDosScanUploadQry
														 AND  confirmation_id = '$confirmation_id'";
										$patientPatientSearchScanQry = imw_query($patientPatientSearchScanStr);
										$patientPatientSearchScanNumRows = imw_num_rows($patientPatientSearchScanQry);
										$scanSearch_class = '';
										if($patientPatientSearchScanNumRows <= 0) {
											$iolinkPatientSearchScanStr = "SELECT isc.scan_consent_id FROM iolink_scan_consent isc,patient_in_waiting_tbl piwt 
																		WHERE isc.patient_id = '$patientPatientSearchDataId'
																		 AND  isc.patient_id != ''
																		 AND  isc.patient_id != '0'
																		 AND  piwt.dos = '".$selected_date."'
																		 AND  piwt.patient_status != 'Canceled'
																		 AND  piwt.patient_status != 'No Show'
																		 AND  piwt.patient_status != 'Aborted Surgery'
																		 
																		 AND  isc.patient_in_waiting_id = piwt.patient_in_waiting_id
															 ";
											$iolinkPatientSearchScanQry = imw_query($iolinkPatientSearchScanStr);
											$iolinkPatientSearchScanNumRows = imw_num_rows($iolinkPatientSearchScanQry);
										}
										if($patientPatientSearchScanNumRows > 0) {
											$scanSearch_class = 'tab_bg';
										}else if($iolinkPatientSearchScanNumRows > 0) {
											$scanSearch_class = 'tab_bg_orange';
										}
									
										//END CODE TO CHECK IF ANY ENTRY EXIST IN scan_upload_tbl (OR IN iolink_scan_consent table)
								
								
								
										//START CODE TO CHECK IF ANY ENTRY OF PATIENT EXIST IN eposted TABLE
										$patientSearchEpostStr = "SELECT epost_id FROM eposted 
															WHERE patient_id = '$patientPatientSearchDataId'
															 AND  patient_id != ''
															 AND  patient_id != '0'
															 $andEpostedQry
															 AND  patient_conf_id = '$confirmation_id' AND table_name!='alert'";
										$patientSearchEpostQry = imw_query($patientSearchEpostStr);
										$patientSearchEpostNumRows = imw_num_rows($patientSearchEpostQry);
										if($patientSearchEpostNumRows > 0) {
											$epostSearch_class = 'alert-epost';
										}else {
											$epostSearch_class = '';
										}
										//END CODE TO CHECK IF ANY ENTRY OF PATIENT EXIST IN eposted TABLE
									
									
										if($stub_tbl_patient_status=='Canceled' || $stub_tbl_patient_status=='No Show' || $stub_tbl_patient_status=='Aborted Surgery'){
											$strikeStyle = 'background-image:url(images/strike_image.jpg); background-repeat:repeat-x; background-position:center;';
										}else {
											$strikeStyle = '';
										}
								
										
										$ptNameBgClass = '';
										if($stub_tbl_patient_status!='Checked-Out') {
											if($stub_tbl_recentChartSaved		== 'preopnursingrecord') 	{ $ptNameBgClass = 'tab_bg_pre_nurse';  
											}else if($stub_tbl_recentChartSaved	== 'postopnursingrecord') 	{ $ptNameBgClass = 'tab_bg_post_nurse';  
											}else if($stub_tbl_recentChartSaved	== 'operatingroomrecords') 	{ $ptNameBgClass = 'tab_bg_operating'; 
										} 
								}
								?>
                                
                                <tr style=" <?php echo $strikeStyle;?> ">
                                
                                <?php
									
									$displayChkMarkImg="";
									$displayChkMarkImg1="";
									$displayChkMarkImg2="";
									//CODE TO CHECK SURGEON SIGNATURE
										if($stub_tbl_chartSignedBySurgeon=='green') { $displayChkMarkImg = "<img src='images/surgeon_green.jpg' style=' border:none;' alt='Chartnote/Sign of surgeon are completed'>"; } else if($stub_tbl_chartSignedBySurgeon=='red')  { $displayChkMarkImg = "<img src='images/surgeon_red.jpg' style=' border:none;' alt='Chartnotes/Sign of Surgeon are not completed'>";}
									//END CODE TO CHECK SURGEON SIGNATURE
									
									//CODE TO CHECK ANES SIGNATURE
										if($stub_tbl_chartSignedByAnes=='green') { $displayChkMarkImg1 = "<img src='images/anes_green.jpg' style=' border:none;' alt='Chartnote/Sign of Anesthesiologist are completed'>"; } else if($stub_tbl_chartSignedByAnes=='red') { $displayChkMarkImg1 = "<img src='images/anes_red.jpg' style=' border:none;' alt='Chartnotes/Sign of Anesthesiologist are not completed'>"; }
									//END CODE TO CHECK ANES SIGNATURE
									
									//CODE TO CHECK NURSE SIGNATURE
										if($stub_tbl_chartSignedByNurse=='green') { $displayChkMarkImg2 = "<img src='images/nurse_green.jpg' style=' border:none;' alt='Chartnote/Sign of Nurse are completed'>"; } else if($stub_tbl_chartSignedByNurse=='red') { $displayChkMarkImg2 = "<img src='images/nurse_red.jpg' style=' border:none;' alt='Chartnotes/Sign of Nurse are not completed'>"; }
									//END CODE TO CHECK NURSE SIGNATURE
									$practiceNameMatch = '';
									if($userType=='Surgeon' && $surgerycenter_peer_review=='Y' && ($stub_surgeon_name <> $loggedInSurgeonName)) {
										$specSrgPracName = $userPracNameArr["user_name"][strtolower(trim($stub_tbl_group_surgeon_fname))][strtolower(trim($stub_tbl_group_surgeon_mname))][strtolower(trim($stub_tbl_group_surgeon_lname))];
										$practiceNameMatch = $objManageData->getPracMatch($practiceName,$specSrgPracName);
									}
									
								?>
                                    <td data-title="Signed" class="text-center " style="width:1%"> 
                                      <?php echo $displayChkMarkImg;?>
                                    </td>
                                    <td class="text-center"  style="width:1%"> <?php echo $displayChkMarkImg1; ?></td>
                                    <td  class="text-center "  style="width:1%"> <?php echo $displayChkMarkImg2;?></td>
                                    
                                    <td data-title="Patient Name" class="capitalize text-left <?php echo $ptNameBgClass;?>">
                                    	<?php 
										//condition to set patient name variable
										if($userType == 'Surgeon') {
											if(($practiceNameMatch == 'yes') || ((ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName)))) {
											}
											else {
												$stub_tbl_patient_name="";
												$stub_tbl_chartSignedBySurgeon="";
												$stub_tbl_chartSignedByNurse="";
												$stub_tbl_chartSignedByAnes="";
											}
										}else {
										}
										//end
										
										//if($stub_tbl_patient_status=='Canceled'){
											?>
											<!--<a href="#" class="link_home" style="cursor:pointer;" onClick="javascript:alert('Patient status is Canceled.')"><?php echo $stub_tbl_patient_name; ?></a>-->
											<?php
										//}else{ 
											//CODE TO REFRESH THIS FILE FOR SCHEDULED PATIENT	
												$javaFunReload='';
												
												//if(!$confirmation_id) { $javaFunReload='document.location.reload();';} else { $javaFunReload=''; }
												if(!$confirmation_id) {$javaFunReload="goToApptDate(".$sel_day_number.",".$sel_month_number.",".$sel_year_number.",'".$showAllApptStatus."');";} else { $javaFunReload=''; }
										  		//
											//END CODE TO REFRESH THIS FILE FOR SCHEDULED PATIENT
										  ?>
												<!-- <a  class="link_home" href="javascript:MM_openBrWindow('patient_without_confirm.php?stub_id=<?php //echo $stub_tbl_stub_id;?>&amp;pConfId=<?php //echo $confirmation_id; ?>&amp;multiwin=yes','ChartNote<?php //echo $stub_tbl_stub_id; ?>', 'top=10, width=1020, height=650, location=yes,status=yes');<?php //echo $javaFunReload;?>"> -->
												<a  class="link_home" href="javascript:top.setwin('ChartNote','<?php echo $stub_tbl_stub_id; ?>','patient_without_confirm.php?showAllApptStatus=<?php echo $showAllApptStatus;?>&stub_id=<?php echo $stub_tbl_stub_id;?>&pConfId=<?php echo $confirmation_id; ?>&multiwin=yes','ChartNote<?php echo $stub_tbl_stub_id; ?>');<?php echo $javaFunReload;?>">	
													<?php echo $stub_tbl_patient_name;?>
												</a>
										<?php  
										//}
											$cntIolinkSxAlerts=	0;
											$qryIolinkSxAlert	=	"SELECT count(*) as TotalSxAlerts FROM iolink_patient_alert_tbl WHERE patient_id = '".$patientDataSearchRow["patient_id"]."' AND iosync_status='Syncronized' AND alert_disabled!='yes'";
											$resIolinkSxAlert	=	imw_query($qryIolinkSxAlert)or die(imw_error());
											$rowIolinkSxAlert=	imw_fetch_assoc($resIolinkSxAlert) ;
											$cntIolinkSxAlerts=	$rowIolinkSxAlert['TotalSxAlerts'] ;

											$pt_alert_visibility	=	"hidden";
											if($arrAlertEpost[$patientPatientSearchDataId][$confirmation_id] || $cntIolinkSxAlerts > 0 )
											{
												$pt_alert_visibility="visible";
											}
										?>
                                        &nbsp;<a class="epost_trigger" style="padding:2px; text-decoration:none; visibility:<?php echo $pt_alert_visibility; ?>" id="alert_<?php echo $stub_tbl_stub_id; ?>" >
                                        			<b class="fa fa-exclamation-triangle "  data-privileges = "<?=$privileges?>"
                                                    										data-privileges-type = "<?=$coordinatorType?>"
                                                                                            data-access-denied = "<?=$accessDeniedCoordinator?>"
                                                                                            data-stub-id = "<?=$stub_tbl_stub_id?>"
                                                                                            data-patient-search = "<?=$txt_patient_search?>" 
                                                                                            data-patient-id = "<?=$patientPatientSearchDataId?>"
                                                                                            data-patient-confirmation-id = "<?=$confirmation_id?>"
                                                                                            data-imw-id = "<?=$patientPatientSearchImwPatientId?>"
                                                                                            data-app-status = "<?=$showAllApptStatus?>"
                                                                                            data-epost-id = "epost_<?=$stub_tbl_stub_id;?>"
                                                                                            data-epost-for = "alert"
                                                                                            data-action-denied="<?=$practiceNameMatch?>" 
                                                                                           >
													</b>
												</a>
                                                
									</td>
                                    <td data-title="Pick Up" class="text-left nowrap">
                                    
                                    <?php 
										//condition to set patient name variable
										if($userType=='Surgeon') {
											if(($practiceNameMatch == 'yes') || (ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))){
												//$stub_tbl_pickup_time = $stub_tbl_row['pickup_time'];//getInterfaceTime($stub_tbl_row['pickup_time']);
											}
											else {
												$stub_tbl_pickup_time="";
											}
										}
										else {
											//$stub_tbl_pickup_time = $stub_tbl_row['pickup_time'];//getInterfaceTime($stub_tbl_row['pickup_time']);
										}
										//end
										
										//if($stub_tbl_patient_status=='Canceled'){
											?>										
											<!--<a href="#" class="link_home" style="cursor:pointer;" onClick="javascript:alert('Patient status is Canceled.')"><?php echo $stub_tbl_pickup_time; ?></a>-->
											<?php
										//}else {  
											?>
											<a href="patient_without_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>&amp;pConfId=<?php echo $confirmation_id; ?>" target="_top" class="link_home">
												<?php echo $stub_tbl_pickup_time;?>
											</a>
										<?php 
										//} ?>
                                    
                                    </td>
                                    <td data-title="Arrival" class="text-left nowrap">
                                    	<?php 
										//condition to set patient name variable
										if($userType=='Surgeon') {
											if(($practiceNameMatch == 'yes') || (ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))){
												//$stub_tbl_arrival_time = $stub_tbl_row['arrival_time'];//getInterfaceTime($stub_tbl_row['arrival_time']);

											}
											else
											{
												$stub_tbl_arrival_time="";
											}
										}
										else
										{
											//$stub_tbl_arrival_time = $stub_tbl_row['arrival_time'];//getInterfaceTime($stub_tbl_row['arrival_time']);

										}
										//end
										
										
										//if($stub_tbl_patient_status=='Canceled'){
											?>
											<!--<a href="#" class="link_home" style="cursor:pointer;" onClick="javascript:alert('Patient status is Canceled.')"><?php echo $stub_tbl_arrival_time; ?></a>-->
											<?php
										//}else{ 
											?>
											<a href="patient_without_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>&amp;pConfId=<?php echo $confirmation_id; ?>" target="_top" class="link_home">
												<?php echo $stub_tbl_arrival_time;?>
											</a>
										<?php 
										//} ?>
                                    
                                    </td>
                                    <td data-title="Surgery" class="text-left nowrap">
                                    	<?php 
										
										//condition to set patient name variable
										if($userType=='Surgeon') {
											if(($practiceNameMatch == 'yes') || (ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))){
												//if($stub_tbl_patient_status=='Canceled'){
												?>
													<!--<a href="#" class="link_home" style="cursor:pointer;" onClick="javascript:alert('Patient status is Canceled.')"><?php echo $stub_tbl_surgery_time; ?></a>-->
												<?php
												//}else{ 
													?>
													<a href="patient_without_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>&amp;pConfId=<?php echo $confirmation_id; ?>" target="_top" class="link_home">
														<?php echo $stub_tbl_surgery_time;?>
													</a>
												<?php 
												//} 
											}
											else
											{
												echo $stub_tbl_surgery_time;
											}
										}
										else
										{
											//if($stub_tbl_patient_status=='Canceled'){
											?>
												<!--<a href="#" class="link_home" style="cursor:pointer;" onClick="javascript:alert('Patient status is Canceled.')"><?php echo $stub_tbl_surgery_time; ?></a>-->
											<?php
											//}else{ 
												?>
												<a href="patient_without_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>&amp;pConfId=<?php echo $confirmation_id; ?>" target="_top" class="link_home">
													<?php echo $stub_tbl_surgery_time;?>
												</a>
											<?php 
											//} 

										}
										?>
                                    </td>
                                    <td data-title="Procedure" class="text-left"  <?php if($stub_tbl_patient_status<>'No Show' && $stub_tbl_patient_status<>'Aborted Surgery' && $stub_tbl_patient_status<>'Scheduled' && $stub_tbl_patient_status<>'Canceled' && $stub_tbl_patient_status<>'Checked-Out'){?>style="padding-left:5px;font-weight:bold;color:rgb(0,128,0);" <?php  }else {?>style="padding-left:5px;"<?php }?>> 
                                    
                                   <?php		//condition to set patient name variable
										$stub_tbl_patient_primary_procedure = wordwrap($stub_tbl_patient_primary_procedure,26,"\n",1);
										if($userType=='Surgeon') {
											if(($practiceNameMatch == 'yes') || (ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))) {
												//if($stub_tbl_patient_status=='Canceled'){
													?>
													<!--<a href="#" class="link_home" style="cursor:pointer;" onClick="javascript:alert('Patient status is Canceled.')"><?php //echo $stub_tbl_patient_primary_procedure.$brTagSec.$stub_tbl_patient_secondary_procedure_eye.$brTagTer.$stub_tbl_patient_tertiary_procedure_eye; ?></a>-->
													<?php
												//}else{ 
													
													//CODE TO REFRESH THIS FILE FOR SCHEDULED PATIENT	
														$javaFunReload='';
														if(!$confirmation_id) {$javaFunReload="goToApptDate(".$sel_day_number.",".$sel_month_number.",".$sel_year_number.",'".$showAllApptStatus."');";} else { $javaFunReload=''; }
														 
													//END CODE TO REFRESH THIS FILE FOR SCHEDULED PATIENT
													?>
													
													<a  class="link_home" href="javascript:top.setwin('ChartNote','<?php echo $stub_tbl_stub_id; ?>','patient_without_confirm.php?showAllApptStatus=<?php echo $showAllApptStatus;?>&stub_id=<?php echo $stub_tbl_stub_id;?>&pConfId=<?php echo $confirmation_id; ?>&multiwin=yes','ChartNote<?php echo $stub_tbl_stub_id; ?>');<?php echo $javaFunReload;?>" <?php if($stub_tbl_patient_status<>'Canceled' && $stub_tbl_patient_status<>'No Show' && $stub_tbl_patient_status<>'Aborted Surgery' && $stub_tbl_patient_status<>'Scheduled' && $stub_tbl_patient_status<>'Canceled' && $stub_tbl_patient_status<>'Checked-Out'){?>style="font-weight:bold;color:rgb(0,128,0);" <?php }else if($stub_tbl_patient_status=='Checked-Out') {?>style="font-weight:bold;color:#0033CC;"  <?php } else {?>style="font-weight:normal;" <?php } ?>>		
														<?php echo $stub_tbl_patient_primary_procedure.$brTagSec.$stub_tbl_patient_secondary_procedure_eye.$brTagTer.$stub_tbl_patient_tertiary_procedure_eye;?>
													</a>
										<?php 	//}
											}else {
										?>			<span <?php if($stub_tbl_patient_status<>'No Show' && $stub_tbl_patient_status<>'Aborted Surgery' && $stub_tbl_patient_status<>'Scheduled' && $stub_tbl_patient_status<>'Canceled' && $stub_tbl_patient_status<>'Checked-Out'){?>style="font-weight:bold;color:rgb(0,128,0);" <?php }else if($stub_tbl_patient_status=='Checked-Out') {?>style="font-weight:bold;color:#0033CC;"  <?php } else {?>style="font-weight:normal;" <?php } ?> >	
										<?php			echo $stub_tbl_patient_primary_procedure.$brTagSec.$stub_tbl_patient_secondary_procedure_eye.$brTagTer.$stub_tbl_patient_tertiary_procedure_eye; //SHOW PROCEDURE WITHOUT LINK
										?>			</span>
										<?php		
											}
										}else {
											//if($stub_tbl_patient_status=='Canceled'){
												?>
												<!--<a href="#" class="link_home" style="cursor:pointer;" onClick="javascript:alert('Patient status is Canceled.')"><?php //echo $stub_tbl_patient_primary_procedure.$brTagSec.$stub_tbl_patient_secondary_procedure_eye.$brTagTer.$stub_tbl_patient_tertiary_procedure_eye; ?></a>-->
												<?php
										//	}else{ 
												
												//CODE TO REFRESH THIS FILE FOR SCHEDULED PATIENT	
													$javaFunReload='';
													
													if(!$confirmation_id) {$javaFunReload="goToApptDate(".$sel_day_number.",".$sel_month_number.",".$sel_year_number.",'".$showAllApptStatus."');";} else { $javaFunReload=''; }
													
												//END CODE TO REFRESH THIS FILE FOR SCHEDULED PATIENT
												?>
												
												<a  class="link_home" href="javascript:top.setwin('ChartNote','<?php echo $stub_tbl_stub_id; ?>','patient_without_confirm.php?showAllApptStatus=<?php echo $showAllApptStatus;?>&stub_id=<?php echo $stub_tbl_stub_id;?>&pConfId=<?php echo $confirmation_id; ?>&multiwin=yes','ChartNote<?php echo $stub_tbl_stub_id; ?>');<?php echo $javaFunReload;?>" <?php if($stub_tbl_patient_status<>'No Show' && $stub_tbl_patient_status<>'Aborted Surgery' && $stub_tbl_patient_status<>'Scheduled' && $stub_tbl_patient_status<>'Canceled' && $stub_tbl_patient_status<>'Checked-Out'){?>style="font-weight:bold;color:rgb(0,128,0);" <?php }else if($stub_tbl_patient_status=='Checked-Out') {?>style="font-weight:bold;color:#0033CC;"  <?php } else {?>style="font-weight:normal;" <?php } ?>>			
													<?php echo $stub_tbl_patient_primary_procedure.$brTagSec.$stub_tbl_patient_secondary_procedure_eye.$brTagTer.$stub_tbl_patient_tertiary_procedure_eye;?>
												</a>
										<?php //}
										}
								?>
                                    
                                    
                                    </td>
                                    <td data-title="Status" class="text-left "  <?php if($stub_tbl_patient_status<>'Scheduled' || $stub_tbl_patient_status<>'Canceled') {?> style="padding-left:5px;" <?php } ?>>
                                    
                                    <?php	if($userType=='Surgeon') {
												if(($stub_tbl_patient_status=='Checked-In') && ($confirmation_id<>'0')){ 
													
													if((ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))) {
											?>			<a href="javascript:<?php if($chartFinalizeStatus=='true') { ?>alert('<?php echo $chartFinalizedAlert;?>');<?php }else if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>alert('<?php echo $accessDeniedCoordinator;?>');<?php }else {?>changeStatus(<?php echo $stub_tbl_stub_id; ?>, '<?php echo $txt_patient_search; ?>', '<?php echo $stub_tbl_patient_status; ?>', '<?php echo $showAllApptStatus; ?>', this, '<?php echo $findBy; ?>');<?php } ?>" class="link_home" title="Change Status">
											<?php   }  
															echo '<i style="color:rgb(0,128,0);font-weight:bold;">CI</i>';if($stub_tbl_patient_CheckedInTime) { echo '<span style="color:rgb(0,128,0);font-weight:bold;"> - '.$stub_tbl_patient_CheckedInTime.'</span>'; } 
													if((ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))) {
											?>			</a>
											<?php	}
												}else if(($stub_tbl_patient_status=='No Show' || $stub_tbl_patient_status=='Aborted Surgery' || $stub_tbl_patient_status=='Scheduled' || $stub_tbl_patient_status=='Canceled')) {
													
													
													?>
													
													<select class="selectpicker" name="patient_status_list" 
														onChange="javascript:<?php 
																		if((ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))) {
																			if($chartFinalizeStatus=='true') { ?>
																				alert('<?php echo $chartFinalizedAlert;?>');
																			<?php
																			}else if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>
																				alert('<?php echo $accessDeniedCoordinator;?>');
																			<?php
																			}else {?>
																				if(this.value=='Checked-In') { 
																					top.location.href='patient_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>&pConfId=<?php echo $confirmation_id; ?>&reConfirmId=<?php echo $confirmation_id; ?>&patient_id=<?php echo $patientPatientSearchDataId; ?>';  
																				}else { 
																					changeStatus(<?php echo $stub_tbl_stub_id; ?>, '<?php echo $txt_patient_search; ?>', '<?php echo $stub_tbl_patient_status; ?>', '<?php echo $showAllApptStatus; ?>',this, '<?php echo $findBy; ?>'); 
																				}
																	 <?php  } 
																		}else {
																			if($stub_tbl_group_surgeon_fname) { 
																	?>			alert('<?php echo "This patient is registered to ".$stub_tbl_group_surgeon_fname." ".$stub_tbl_group_surgeon_lname; ?>');
																	<?php	}
																		}
																	 ?>
																">
														
															<option value="Scheduled" 		<?php if($stub_tbl_patient_status=='Scheduled') 		{ echo 'selected'; }?>>Scheduled</option>
															<option value="Canceled" 		<?php if($stub_tbl_patient_status=='Canceled') 			{ echo 'selected'; }?>>Cancelled</option>
															<option value="Checked-In" 		<?php if($stub_tbl_patient_status=='Checked-In') 		{ echo 'selected'; }?>>Checked-In</option>
                                                            <option value="No Show" 		<?php if($stub_tbl_patient_status=='No Show') 			{ echo 'selected'; }?>>No Show</option>
                                                            <option value="Aborted Surgery"	<?php if($stub_tbl_patient_status=='Aborted Surgery')	{ echo 'selected'; }?>>Aborted Surgery</option>
													</select>
													<?php
												}else if($stub_tbl_patient_status=='Checked-Out'){
													if((ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))) {
												?>		<a href="javascript:<?php if($chartFinalizeStatus=='true') { ?>alert('<?php echo $chartFinalizedAlert;?>');<?php }else if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>alert('<?php echo $accessDeniedCoordinator;?>');<?php }else {?>changeStatus(<?php echo $stub_tbl_stub_id; ?>, '<?php echo $txt_patient_search; ?>', '<?php echo $stub_tbl_patient_status; ?>', '<?php echo $showAllApptStatus; ?>',this, '<?php echo $findBy; ?>');<?php } ?>" class="link_home" title="Change Status">
											<?php	} 
															echo '<i style="color:#0033CC;font-weight:bold;">CO</i>';if($stub_tbl_patient_CheckedOutTime) {echo '<span style="color:#0033CC;font-weight:bold;"> - '.$stub_tbl_patient_CheckedOutTime.'</span>'; }
													if((ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))) {		
												?>		</a>
											<?php	}
												
												}else{
											?>
													<a href="patient_without_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>&amp;pConfId=<?php echo $confirmation_id; ?>" target="_top" class="link_home">
														<?php echo $stub_tbl_patient_status;?>
													</a>
											<?php	
												}
												
											} else {
													if(($stub_tbl_patient_status=='Checked-In') && ($confirmation_id<>'0')){ 
													?>
													
													<a href="javascript:<?php if($chartFinalizeStatus=='true') { ?>alert('<?php echo $chartFinalizedAlert;?>');<?php }else if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>alert('<?php echo $accessDeniedCoordinator;?>');<?php }else {?>changeStatus(<?php echo $stub_tbl_stub_id; ?>, '<?php echo $txt_patient_search; ?>', '<?php echo $stub_tbl_patient_status; ?>', '<?php echo $showAllApptStatus; ?>','', '<?php echo $findBy; ?>');<?php } ?>" class="link_home" title="Change Status">
														<?php echo '<i style="color:rgb(0,128,0);font-weight:bold;">CI</i>';if($stub_tbl_patient_CheckedInTime) { echo '<span style="color:rgb(0,128,0);font-weight:bold;"> - '.$stub_tbl_patient_CheckedInTime.'</span>'; } ?>
													</a>
													<?php
												
													}else if(($stub_tbl_patient_status=='No Show' || $stub_tbl_patient_status=='Aborted Surgery' || $stub_tbl_patient_status=='Scheduled' || $stub_tbl_patient_status=='Canceled')) {
														?>
														
														<select class="selectpicker" name="patient_status_list" 
															  onChange="javascript:<?php if($chartFinalizeStatus=='true') { ?>
																				alert('<?php echo $chartFinalizedAlert;?>');
																			<?php
																			}else if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>
																				alert('<?php echo $accessDeniedCoordinator;?>');
																			<?php
																			}else { ?>
																				if(this.value=='Checked-In') { 
																					top.location.href='patient_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>&pConfId=<?php echo $confirmation_id; ?>&reConfirmId=<?php echo $confirmation_id; ?>&patient_id=<?php echo $patientPatientSearchDataId; ?>';  
																				}else { 
																					changeStatus(<?php echo $stub_tbl_stub_id; ?>, '<?php echo $txt_patient_search; ?>', '<?php echo $stub_tbl_patient_status; ?>', '<?php echo $showAllApptStatus; ?>',this, '<?php echo $findBy; ?>'); 
																				}
																	 <?php  } ?>
																						
																	">
															
																<option value="Scheduled"  		<?php if($stub_tbl_patient_status=='Scheduled') 		{ echo 'selected'; }?>>Scheduled</option>
                                                                <option value="Canceled"   		<?php if($stub_tbl_patient_status=='Canceled') 			{ echo 'selected'; }?>>Cancelled</option>
																<option value="Checked-In" 		<?php if($stub_tbl_patient_status=='Checked-In') 		{ echo 'selected'; }?>>Checked-In</option>
																<option value="No Show"   		<?php if($stub_tbl_patient_status=='No Show') 			{ echo 'selected'; }?>>No Show</option>
                                                                <option value="Aborted Surgery" <?php if($stub_tbl_patient_status=='Aborted Surgery') 	{ echo 'selected'; }?>>Aborted Surgery</option>
														</select>
														<?php
													}else if($stub_tbl_patient_status=='Checked-Out'){
														?>
														
														<a href="javascript:<?php if($chartFinalizeStatus=='true') { ?>alert('<?php echo $chartFinalizedAlert;?>');<?php }else if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>alert('<?php echo $accessDeniedCoordinator;?>');<?php }else {?>changeStatus(<?php echo $stub_tbl_stub_id; ?>, '<?php echo $txt_patient_search; ?>', '<?php echo $stub_tbl_patient_status; ?>', '<?php echo $showAllApptStatus; ?>','', '<?php echo $findBy; ?>');<?php } ?>" class="link_home" title="Change Status">
															<?php echo '<i style="color:#0033CC;font-weight:bold;">CO</i>';if($stub_tbl_patient_CheckedOutTime) {echo '<span style="color:#0033CC;font-weight:bold;"> - '.$stub_tbl_patient_CheckedOutTime.'</span>'; }?>
														</a>
														<?php
			
													}else{
														?>
														<a href="patient_without_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>&amp;pConfId=<?php echo $confirmation_id; ?>" target="_top" class="link_home">
															<?php echo $stub_tbl_patient_status;?>
														</a>
														<?php 
													}
												}
												//end		
											 ?>
                                    
                                    </td>
                                    <td  data-title="Comments" class="text-left"> <div class="comment_box ">
                                       <textarea class="form-control" id="commentText<?php echo $stub_tbl_stub_id; ?>" style=" <?php echo $strikeStyle;?> height:40px !important; " title="<?php echo $stub_tbl_comment; ?>"><?php echo $stub_tbl_comment; ?></textarea>
                                       <?php
										if($userType=='Surgeon') {
											if((ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))) {
										?>		<a href="#" onClick="javascript:saveComment(<?php echo $stub_tbl_stub_id; ?>, '<?php echo $txt_patient_search; ?>', '<?php echo $stub_tbl_patient_status; ?>','commentText<?php echo $stub_tbl_stub_id; ?>','', '<?php echo $showAllApptStatus; ?>', '<?php echo $findBy; ?>');" class="link_home"><b>Save</b></a>
								<?php		}
										}else{
										?>	<a href="#" onClick="javascript:saveComment(<?php echo $stub_tbl_stub_id; ?>, '<?php echo $txt_patient_search; ?>', '<?php echo $stub_tbl_patient_status; ?>','commentText<?php echo $stub_tbl_stub_id; ?>','', '<?php echo $showAllApptStatus; ?>', '<?php echo $findBy; ?>');" class="link_home"><b>Save</b></a>
								<?php	}	?>	
                                	  </Div> 
                                               
                                                </td>
                                   
                                    <td data-title="Scan" valign="top"   class="text-center <?php echo $scanSearch_class;?>" id="scan_bgId<?php echo $stub_tbl_stub_id;?>"> 
                                  <!--  <a class="btn-xs btn btn-info"> <b class="glyphicon glyphicon-screenshot"></b> </a>-->
                                    	<?php
									//condition to set patient name variable
										if($userType=='Surgeon') {
											if(($practiceNameMatch == 'yes') || (ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))) {
												if($stub_tbl_patient_status=='Scheduled') {?>
													<img style="cursor:pointer; border:none; " src="images/scanicon.png" title="Scan" onClick="javascript:chkPatientExist('<?php echo $stub_tbl_stub_id;?>','<?php echo $txt_patient_search;?>','<?php echo $patientPatientSearchDataId;?>','<?php echo $stub_tbl_stub_dos;?>','<?php echo $confirmation_id; ?>','<?php echo $patientPatientSearchImwPatientId;?>','<?php echo $showAllApptStatus;?>','<?php echo $practiceNameMatch;?>','<?php echo $findBy;?>');"/>
										<?php   } ?>
									<?php	}
											else {
												$stub_tbl_stub_id="";
											}
										 }
										 else {
											if($stub_tbl_patient_status=='Scheduled') {?>
												<img style="cursor:pointer; border:none; " src="images/scanicon.png" title="Scan" onClick="javascript:chkPatientExist('<?php echo $stub_tbl_stub_id;?>','<?php echo $txt_patient_search;?>','<?php echo $patientPatientSearchDataId;?>','<?php echo $stub_tbl_stub_dos;?>','<?php echo $confirmation_id; ?>','<?php echo $patientPatientSearchImwPatientId;?>','<?php echo $showAllApptStatus;?>','<?php echo $practiceNameMatch;?>','<?php echo $findBy;?>');"/>
								<?php   	} 
										 }
										//end		
									 ?>
                                    </td>
                                    <td data-title="Edit" class="text-center">
                                    
                                   <?php
									
									//condition to set patient name variable
										if($userType=='Surgeon') {
											if(($practiceNameMatch == 'yes') || (ucfirst($stub_tbl_group_surgeon_fname)==ucfirst($surgeonLoggedFirstName))&&(ucfirst($stub_tbl_group_surgeon_lname)==ucfirst($surgeonLoggedLastName))) {
													if($stub_tbl_patient_status=='Checked-In' || $stub_tbl_patient_status=='Checked-Out') 
													{
									
									?>
                                    					<a onClick="javascript:<?php if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>alert('<?php echo $accessDeniedCoordinator;?>'); <?php } else {?>top.location.href='patient_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>&pConfId=<?php echo $confirmation_id; ?>&reConfirmId=<?php echo $confirmation_id; ?>&patient_id=<?php echo $patientPatientSearchDataId; ?>'<?php } ?>" target="_top" class="btn-xs btn btn-success edit_btn" title="Edit"><b class="fa fa-edit"></b>
														</a>
												<?php 	} ?>
									<?php	}
											else {
												$stub_tbl_stub_id="";
											}
										}
										else {
											if($stub_tbl_patient_status=='Checked-In' || $stub_tbl_patient_status=='Checked-Out'){?>
												<a onClick="javascript:<?php if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>alert('<?php echo $accessDeniedCoordinator;?>');<?php }
												else {?>top.location.href='patient_confirm.php?stub_id=<?php echo $stub_tbl_stub_id;?>&pConfId=<?php echo $confirmation_id; ?>&reConfirmId=<?php echo $confirmation_id; ?>&patient_id=<?php echo $patientPatientSearchDataId; ?>'<?php } ?>" target="_top" class="btn-xs btn btn-success edit_btn" title="Edit"><b class="fa fa-edit"></b>
												</a>
										<?php 	
											} ?>
	
								<?php	} 
										//end
										?>
                                    
                                    </td>
                                    <td data-title="Epost" class="text-center">
                                  	<?php
										
										//condition to set patient name variable
										if($userType == 'Surgeon') {
											
											if(($practiceNameMatch == 'yes') ||  (ucfirst($stub_tbl_group_surgeon_fname) == ucfirst($surgeonLoggedFirstName)) && (ucfirst($stub_tbl_group_surgeon_lname) == ucfirst($surgeonLoggedLastName) ) ) 
											{
												if($stub_tbl_patient_status <> 'Canceled') 
												{
									
									?>
                                    				<div id="epost_<?=$stub_tbl_stub_id?>" class="box epost_trigger <?PHP echo $epostSearch_class ?>"><a class="btn-xs btn btn-primary " title="ePostIt" >
                                                    	<b class="fa fa-comment" data-privileges="<?=$privileges?>"
                                                        						 data-privileges-type = "<?=$coordinatorType?>"
                                                                                 data-access-denied="<?=$accessDeniedCoordinator?>"
                                                                                 data-stub-id = "<?=$stub_tbl_stub_id?>"
                                                                                 data-patient-search = "<?=$txt_patient_search?>"
                                                                                 data-patient-id = "<?=$patientPatientSearchDataId?>" 
                                                                                 data-patient-confirmation-id = "<?=$confirmation_id?>"
                                                                                 data-imw-id =	"<?=$patientPatientSearchImwPatientId?>"
                                                                                 data-app-status = "<?=$showAllApptStatus?>"
                                                                                 data-epost-id = "epost_<?=$stub_tbl_stub_id;?>"
                                                                                 data-epost-for = "epost"
                                                                                 data-action-denied="<?=$practiceNameMatch?>" ></b>
                                                                                 
													</a></div>
									<?php 
												 
												 }
											}
											else {
												$stub_tbl_stub_id="";
											}
										}
										else 
										{
											 if($stub_tbl_patient_status<>'Canceled')
											 {
									//cd532f 9eca3b
									?>
													<div id="epost_<?=$stub_tbl_stub_id?>" class="box epost_trigger <?PHP echo $epostSearch_class ?> "><a class="btn-xs btn btn-primary" title="ePostIt">
                                                		<b class="fa fa-comment" data-privileges="<?=$privileges?>"
                                                        						 data-privileges-type = "<?=$coordinatorType?>"
                                                                                 data-access-denied="<?=$accessDeniedCoordinator?>"
                                                                                 data-stub-id = "<?=$stub_tbl_stub_id?>"
                                                                                 data-patient-search = "<?=$txt_patient_search?>"
                                                                                 data-patient-id = "<?=$patientPatientSearchDataId?>"
                                                                                 data-patient-confirmation-id = "<?=$confirmation_id?>"
                                                                                 data-imw-id = "<?=$patientPatientSearchImwPatientId?>"
                                                                                 data-app-status = "<?=$showAllApptStatus?>"
                                                                                 data-epost-id = "epost_<?=$stub_tbl_stub_id;?>"
                                                                                 data-epost-for = "epost"
                                                                                 data-action-denied="<?=$practiceNameMatch?>" ></b>
													</a></div>
									<?php 
											 
											 } 
										}?>
                                    </td>
                                </tr>
                                <?php 
							}
									
									}
					  }	
							
							}else{
					?>
								<tr style="background-color:#ECF1EA">
									<td class="text-center nowrap" colspan="13">	
										<b>No Record Found !</b>
									</td>
								</tr>
					<?php
							}
							//END CODE FOR STUB DATABASE VALUE
					?>
                    
                    		</tbody>
                    </table>
                    
                 </div>                
             
              </div>
              <!-- NEcessary PUSH     -->	 
              <!--<Div class="push"></Div>-->
              <!-- NEcessary PUSH     -->
        </div>
    

<!--END NEW DESIGN-->
<div style="clear:both;"></div>

<!--/////////////////////////////////////			CALENDER		//////////////////////////////////////////////-->
<div class="modal fade" id="my_modal_cal">
 <div class="modal-dialog modal-lg">
 	<div class="modal-content">
    	<div class="modal-header text-center">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title rob">Calendar  </h4>  
       </div>
        <div class="modal-body">
        	  <!-- Responsive calendar - START -->
      		<div class="myCalendar for_responsive_calendar" id="cal"></div>    
              <!-- Responsive calendar - END -->
        </div>
     
    </div>
 </div>
</div>
<!--/////////////////////////////////////			CALENDER		//////////////////////////////////////////////-->

<!-- START DISPLAY PATIENT SEARCH RESULTS-->			

<div id="patient_search_results_id" class="no-more-tables scrollable_yes " style=" display:<?php echo $display_patient_search_result;?>; ">	
    <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf table-hover" style="display:<?php echo $display_patient_search_result;?>;">
        <?php 	
        $txt_patient_search = trim($_GET["txt_patient_search_id"]);
        $findByQry  = "";
		if($txt_patient_search <>"") {
            if(!is_numeric($txt_patient_search) || ($findBy == "External_MRN")){
                $findByQry = " AND (patient_first_name LIKE '$txt_patient_search%' OR patient_last_name LIKE '$txt_patient_search%')";
				if($findBy == "External_MRN") {
					$findByQry = " AND imwPatientId = '".$txt_patient_search."' ";
				}
				$andSrgnQry = "";
                if($userType == 'Surgeon') {
										if($surgerycenter_peer_review == 'Y')
										{
											$practiceUsersArr	=	$objManageData->loadPracticeUser($practiceName);
											if(is_array($practiceUsersArr) && count($practiceUsersArr) > 0 )
											{
												$counter = 0;
												$andSrgnQry .= " AND ( ";
												foreach($practiceUsersArr as $v)
												{
													$counter++;
													$andSrgnQry .= " (surgeon_fname = '".addslashes($v['fname'])."' AND surgeon_lname = '".addslashes($v['lname'])."') ";		
													$andSrgnQry .=	($counter < count($practiceUsersArr)) ?	'OR' : '';
													 
												}
												$andSrgnQry .= " ) ";
											}
										}
										else
										{
											$andSrgnQry = " AND surgeon_fname = '".addslashes($surgeonLoggedFirstName)."' AND surgeon_lname = '".addslashes($surgeonLoggedLastName)."'  ";
										}
								}
								if(strpos($txt_patient_search, ',')){
                    $searchKeywordArr = explode(",", $txt_patient_search);
                    $patientLastName = trim($searchKeywordArr[0]);
                    $patientFirstName = trim($searchKeywordArr[1]);
                    $getPatientSearchQry = imw_query("select *, DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format from stub_tbl where 
                                            patient_first_name LIKE '$patientFirstName%' 
                                            AND patient_last_name LIKE '$patientLastName%' 
                                            ".$andSrgnQry."
                                           	order by iasc_facility_id,`patient_last_name`,`dos` DESC");// ".$fac_con."
                    
                
								}else{
                    $getPatientSearchQry = imw_query("select *, DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format from stub_tbl where 1=1 ".$andSrgnQry.$findByQry."
                                            order by iasc_facility_id, `patient_last_name`,`dos` DESC");// ".$fac_con."
				}
								$countRows = imw_num_rows($getPatientSearchQry);
                
                if($countRows>0){
				
                    while($getPatientSearchRows = imw_fetch_assoc($getPatientSearchQry)){
                        extract($getPatientSearchRows);
						
												#CHECK IS THAT A LOGGED IN FACILITY TO ALLOW MAKE CHANGES IN PATIENT DATA
												$allowChanges=0;
												$allowChanges=($getPatientSearchRows['iasc_facility_id']==$_SESSION['iasc_facility_id'])?1:0;
												#ALERT TO SHOW OTHER ASC PATIENTS
												$otherFacAlert='Please login to ASC - '.$facility_arr[$getPatientSearchRows['iasc_facility_id']].' to access this chart';
												#CHECKING FACILITY ID TO CREATE FACILITY WISE HEADER ON FIRST RECORD
												if(!in_array($getPatientSearchRows['iasc_facility_id'],$stub_tbl_iasc_groupTemp)) 
												{
													if(sizeof($stub_tbl_iasc_groupTemp)>=1)echo'</tbody>';
							?>
              							<thead class="cf">
                              <tr class="caption">
                                  <td  class="text-center rob" colspan="12">ASC - <strong><?php echo $facility_arr[$getPatientSearchRows['iasc_facility_id']];?></strong></td>
                              </tr>
                              <tr>
                                  <th class="text-center" colspan="3">Signed</th>
                                  <th  class="text-left">Patient Name</th>
                                  <th class="text-left">DOS</th>
                                  <th class="text-left">Surgery</th>
                                  <th class="text-left">Procedure</th>
                                  <th class="text-left">Status</th>
                                  <th class="text-left">Comments</th>
                                  <th class="text-center">Scan</th>
                                  <th class="text-center">Edit</th>
                                  <th class="text-center">Epost</th>
                  
                              </tr>
                          	</thead>
                            <tbody>
                            <?php
															$stub_tbl_iasc_groupTemp[]=$getPatientSearchRows['iasc_facility_id'];
												}
                        
												$stub_tbl_stub_id = $getPatientSearchRows['stub_id'];
                        $stub_tbl_dos = $getPatientSearchRows['dos'];
                        $stub_tbl_chartSignedBySurgeon 	= $getPatientSearchRows['chartSignedBySurgeon'];
                        $stub_tbl_chartSignedByNurse 	= $getPatientSearchRows['chartSignedByNurse'];
                        $stub_tbl_chartSignedByAnes 	= $getPatientSearchRows['chartSignedByAnes'];
                        $stub_tbl_recentChartSaved 		= $getPatientSearchRows['recentChartSaved'];
                        $stub_tbl_patient_dob_format 	= $getPatientSearchRows['patient_dob_format'];
                                                        
                        $stub_tbl_patient_id_stub = $getPatientSearchRows['patient_id_stub'];
                        //SET WINDOW NAME WHILE OPENING MUTIPLE WINDOW
                        //END SET WINDOW NAME WHILE OPENING MUTIPLE WINDOW

                        $stub_tbl_SearchComment = $getPatientSearchRows['comment'];
                        $CheckedInOutTime = "";
                        $display_patient_status = "";
                        
						if($allowChanges)
						{
                       // if($patient_status=='Canceled'){
                       //     $ahref_link_start = "<a href='#' class='link_home' style='cursor:pointer;' onClick=\"javascript:alert('Patient status is Canceled.')\">";
                       // }else{						
                            $ahref_link_start = "<a class='link_home' target='_top' style='cursor:pointer;' href='patient_without_confirm.php?stub_id=$stub_id&amp;pConfId=$patient_confirmation_id'>";
                       // }	
                        
                        if(($patient_status=='Checked-In')){ 
                            
                            $ahref_CheckInlink_start = "<a href=\"javascript:changeStatusCheckInCancel('".$stub_id."','".$txt_patient_search."','".$patient_status."','".$findBy."', '');\" class='link_home' title='Change Status'>";
                            $display_patient_status = '<i style="color:rgb(0,128,0);font-weight:bold;">CI</i>';
                            if($checked_in_time) {
                                $CheckedInOutTime = '<span style="color:rgb(0,128,0);font-weight:bold;"> - '.$objManageData->getTmFormat($checked_in_time).'</span>';
                            }
                        }else if($patient_status=='Checked-Out'){
                            $ahref_CheckInlink_start = "<a href=\"javascript:changeStatusCheckInCancel('".$stub_id."','".$txt_patient_search."','".$patient_status."','".$findBy."', '');\" class='link_home' title='Change Status'>";
                            $display_patient_status = '<i style="color:#0033CC;font-weight:bold;">CO</i>';
                            if($checked_out_time) {
                                $CheckedInOutTime = '<span style="color:#0033CC;font-weight:bold;"> - '.$objManageData->getTmFormat($checked_out_time).'</span>';
                            }
                            
                        }else{
                            $ahref_CheckInlink_start = "<a href='patient_without_confirm.php?stub_id=$stub_id&amp;pConfId=$patient_confirmation_id' target='_top' class='link_home'>";
                        }
                        
                        $ahref_link_end = "</a>";
						}
						else
						{
							 $ahref_CheckInlink_start = "<a href='patient_without_confirm.php?stub_id=$stub_id&amp;pConfId=$patient_confirmation_id' target='_top' class='link_home'>";
                        	$ahref_link_end = "</a>";	
						}
                         
                        
                        //START CODE TO FIND PATIENT ID (IF EXIST) 
                        $chartFinalizeStatus="";
                        if($patient_confirmation_id) {
                            $getPtIdQry = "SELECT patientId,finalize_status  FROM patientconfirmation WHERE patientConfirmationId='$patient_confirmation_id'";
                            $getPtIdRes = imw_query($getPtIdQry) or die(imw_error());
                            $getPtIdNumRow = imw_num_rows($getPtIdRes);
                            if($getPtIdNumRow>0) {
                                $getPtIdRow = imw_fetch_array($getPtIdRes);
                                $chartFinalizeStatus = $getPtIdRow['finalize_status'];											
                                if($patient_confirmation_id && !$stub_tbl_patient_id_stub) {
                                    $stub_tbl_patient_id_stub = $getPtIdRow['patientId'];
                                }
                            }
                        }
                        if($stub_tbl_patient_id_stub) {
                            $patientSearchMatchStr = "SELECT patient_id,imwPatientId FROM patient_data_tbl 
                                                    WHERE patient_id = '".$stub_tbl_patient_id_stub."'
                                                    ";
                        }else {
                            $patientSearchMatchStr = "SELECT patient_id,imwPatientId FROM patient_data_tbl 
                                                    WHERE patient_fname = '".addslashes($patient_first_name)."'
                                                    AND patient_lname 	= '".addslashes($patient_last_name)."'
                                                    AND zip 			= '".addslashes($patient_zip)."'
                                                    AND date_of_birth 	= '".$patient_dob."'";
                        }		
                        $patientSearchMatchQry = imw_query($patientSearchMatchStr);
                        $patientSearchMatchRows = imw_num_rows($patientSearchMatchQry);
                        $patientSearchDataRow = imw_fetch_array($patientSearchMatchQry);
                        if($patientSearchMatchRows>0){
                            $patientSearchPatientDataId = $patientSearchDataRow["patient_id"];
                            $patientSearchPatientImwPatientId = $patientSearchDataRow["imwPatientId"];
                        }else {
                            $patientSearchPatientDataId = "";
                            $patientSearchPatientImwPatientId = "";
                        }
                        //END CODE TO FIND PATIENT ID (IF EXIST)
                        
                        //CODE TO REFRESH THIS FILE FOR SCHEDULED PATIENT	
                            $javaFunReload='';
                            $andDosScanUploadQry='';
                            $andEpostedQry='';
                            if(!$patient_confirmation_id) { 
                                $javaFunReload="goToApptDate(".$sel_day_number.",".$sel_month_number.",".$sel_year_number.",'".$showAllApptStatus."');";
                                $andDosScanUploadQry = " AND dosOfScan = '".$selected_date."' AND stub_id = '".$stub_tbl_stub_id."' ";
                                $andEpostedQry = " AND stub_id = '".$stub_tbl_stub_id."' ";
                                
                            } else { 
                                $javaFunReload='';
                            }
                        //END CODE TO REFRESH THIS FILE FOR SCHEDULED PATIENT
                        
                        $multipleWindowOpenLink = "<a  class=\"link_home\" href=\"javascript:top.setwin('ChartNote','$stub_id','patient_without_confirm.php?showAllApptStatus=$showAllApptStatus&stub_id=$stub_id&pConfId=$patient_confirmation_id&multiwin=yes','ChartNote$stub_id');".$javaFunReload."\">";		
                        //if($patient_status=='Canceled'){
                           //$multipleWindowOpenLink = $ahref_link_start;
                        //}
                            
                        
                        
                        //START CODE TO CHECK IF ANY ENTRY EXIST IN scan_upload_tbl
                            $scan_class='';
                            $patientSearchScanStr = "SELECT scan_upload_id FROM scan_upload_tbl 
                                                WHERE patient_id = '$patientSearchPatientDataId'
                                                 AND  patient_id != ''
                                                 AND  patient_id != '0'
                                                 $andDosScanUploadQry
                                                 AND  confirmation_id = '$patient_confirmation_id'";
                            $patientSearchScanQry = imw_query($patientSearchScanStr);
                            $patientSearchScanNumRows = imw_num_rows($patientSearchScanQry);
                            
                            if($patientSearchScanNumRows <= 0) {
                                $iolinkPatientSearchScanStr = "SELECT isc.scan_consent_id FROM iolink_scan_consent isc,patient_in_waiting_tbl piwt 
                                                    WHERE isc.patient_id = '$patientPatientSearchDataId'
                                                     AND  isc.patient_id != ''
                                                     AND  isc.patient_id != '0'
                                                     AND  piwt.dos = '".$selected_date."'
                                                     AND  piwt.patient_status != 'Canceled'
                                                     AND  isc.patient_in_waiting_id = piwt.patient_in_waiting_id
                                                     ";
                                $iolinkPatientSearchScanQry = imw_query($iolinkPatientSearchScanStr);
                                $iolinkPatientSearchScanNumRows = imw_num_rows($iolinkPatientSearchScanQry);
                            }
                            if($patientSearchScanNumRows > 0) {
                                $scan_class = 'tab_bg';
                            }else if($iolinkPatientSearchScanNumRows > 0) {
                                $scan_class = 'tab_bg_orange';
                            }
                            
                        //END CODE TO CHECK IF ANY ENTRY EXIST IN scan_upload_tbl
                        
                        //START CODE TO CHECK IF ANY ENTRY OF PATIENT EXIST IN eposted TABLE
                            $patientSearchEpostStr = "SELECT epost_id FROM eposted 
                                                WHERE patient_id = '$patientSearchPatientDataId'
                                                 AND  patient_id != ''
                                                 AND  patient_id != '0'
                                                 $andEpostedQry
                                                 AND  patient_conf_id = '$patient_confirmation_id' AND table_name!='alert'";
                            $patientSearchEpostQry = imw_query($patientSearchEpostStr);
                            $patientSearchEpostNumRows = imw_num_rows($patientSearchEpostQry);
                            if($patientSearchEpostNumRows > 0) {
                                $epostSearch_class = 'alert-epost';
                            }else {
                                $epostSearch_class = '';
                            }
                        //END CODE TO CHECK IF ANY ENTRY OF PATIENT EXIST IN eposted TABLE
                        
												// Start change site to OS/OD/OU
												$primarySite	=	$secondarySite	=	$tertiarySite	=	'';
												if($site == 'left')
												{
													$primarySite	=	'OS';
												}
												else if($site == 'right')
												{
													$primarySite	=	'OD';
												}
												else if($site=='both') 
												{
													$primarySite	=	'OU';
												}else if($site == 'left upper lid'){
													$primarySite	=	'LUL';
												}else if($site == 'left lower lid'){
													$primarySite	=	'LLL';
												}else if($site == 'right upper lid'){
													$primarySite	=	'RUL';
												}else if($site == 'right lower lid'){
													$primarySite	=	'RLL';
												}else if($site == 'bilateral upper lid'){
													$primarySite	=	'BUL';
												}else if($site == 'bilateral lower lid'){
													$primarySite	=	'BLL';
												}
												
												$stub_secondary_site	=	($stub_secondary_site)	?	$stub_secondary_site	:	$site;
												if($stub_secondary_site == 'left'){
													$secondarySite	=	'OS';
												}else if($stub_secondary_site == 'right'){
													$secondarySite	=	'OD';
												}else if($stub_secondary_site=='both'){
													$secondarySite	=	'OU';
												}else if($stub_secondary_site == 'left upper lid'){
													$secondarySite	=	'LUL';
												}else if($stub_secondary_site == 'left lower lid'){
													$secondarySite	=	'LLL';
												}else if($stub_secondary_site == 'right upper lid'){
													$secondarySite	=	'RUL';
												}else if($stub_secondary_site == 'right lower lid'){
													$secondarySite	=	'RLL';
												}else if($stub_secondary_site == 'bilateral upper lid'){
													$secondarySite	=	'BUL';
												}else if($stub_secondary_site == 'bilateral lower lid'){
													$secondarySite	=	'BLL';
												}
												
												$stub_tertiary_site	=	($stub_tertiary_site)	?	$stub_tertiary_site	:	$site;
												if($stub_tertiary_site == 'left'){
													$tertiarySite	=	'OS';
												}else if($stub_tertiary_site == 'right'){
													$tertiarySite	=	'OD';
												}else if($stub_tertiary_site=='both'){
													$tertiarySite	=	'OU';
												}else if($stub_tertiary_site == 'left upper lid'){
													$tertiarySite	=	'LUL';
												}else if($stub_tertiary_site == 'left lower lid'){
													$tertiarySite	=	'LLL';
												}else if($stub_tertiary_site == 'right upper lid'){
													$tertiarySite	=	'RUL';
												}else if($stub_tertiary_site == 'right lower lid'){
													$tertiarySite	=	'RLL';
												}else if($stub_tertiary_site == 'bilateral upper lid'){
													$tertiarySite	=	'BUL';
												}else if($stub_tertiary_site == 'bilateral lower lid'){
													$tertiarySite	=	'BLL';
												}
												
												// End change site to OS/OD/OU
												
												//START APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
												$patient_primary_procedure 		.= ($patient_primary_procedure)		?	' '.$primarySite 		: '';
												$patient_secondary_procedure 	.= ($patient_secondary_procedure)	?	' '.$secondarySite 	: '';
												$patient_tertiary_procedure 	.= ($patient_tertiary_procedure)	?	' '.$tertiarySite 		:	'';	
												//END APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
                        
												$brTagSec = $brTagTer = '';
												if($patient_primary_procedure && $patient_secondary_procedure)
												{
													$brTagSec = '<br>';	
												}
												if($patient_primary_procedure && $patient_tertiary_procedure)
												{
													$brTagTer = '<br>';	
												}
												
                        ?>
                        <?php 
                        $setProcedureStyleStart = "";
                        $setProcedureStyleEnd = "";
                        if($patient_status=='Checked-In'){ $setProcedureStyleStart = '<span style="color:rgb(0,128,0);font-weight:bold;">';$setProcedureStyleEnd = '</span>';  }
                        if($patient_status=='Checked-Out'){ $setProcedureStyleStart = '<span style="color:#0033CC;font-weight:bold;">';$setProcedureStyleEnd = '</span>';  }
                        
                        if($patient_status=='Canceled' || $patient_status=='No Show' || $patient_status=='Aborted Surgery'){
                            $strikeStyle = 'background-image:url(images/strike_image.jpg); background-repeat:repeat-x; background-position:center;';
                        }else {
                            $strikeStyle = '';
                        }
                        
                        $stub_tbl_dosTemp = $getPatientSearchRows['dos'];
                        $stub_tbl_dos_split = explode("-",$stub_tbl_dosTemp);
                        $stub_tbl_dos_show = $stub_tbl_dos_split[1]."-".$stub_tbl_dos_split[2]."-".$stub_tbl_dos_split[0];
                                
                        $displayChkMarkImg="<b><img src='images/sign_blank.jpg' style=' border:none;' ></b>";
                        $displayChkMarkImg1="<b><img src='images/sign_blank.jpg' style=' border:none;'></b>";
                        $displayChkMarkImg2="<b><img src='images/sign_blank.jpg' style=' border:none;'></b>";
                        
                        //CODE TO CHECK SURGEON SIGNATURE
                            if($stub_tbl_chartSignedBySurgeon=='green') { $displayChkMarkImg = "<img src='images/surgeon_green.jpg' style=' border:none;' alt='Chartnote/Sign of surgeon are completed'>"; } else if($stub_tbl_chartSignedBySurgeon=='red')  { $displayChkMarkImg = "<img src='images/surgeon_red.jpg' style=' border:none;' alt='Chartnotes/Sign of Surgeon are not completed'>";}
                        //END CODE TO CHECK SURGEON SIGNATURE
                        
                        //CODE TO CHECK ANES SIGNATURE
                            if($stub_tbl_chartSignedByAnes=='green') { $displayChkMarkImg1 = "<img src='images/anes_green.jpg' style=' border:none;' alt='Chartnote/Sign of Anesthesiologist are completed'>"; } else if($stub_tbl_chartSignedByAnes=='red') { $displayChkMarkImg1 = "<img src='images/anes_red.jpg' style=' border:none;' alt='Chartnotes/Sign of Anesthesiologist are not completed'>"; }
                        //END CODE TO CHECK ANES SIGNATURE
                        
                        //CODE TO CHECK NURSE SIGNATURE
                            if($stub_tbl_chartSignedByNurse=='green') { $displayChkMarkImg2 = "<img src='images/nurse_green.jpg' style=' border:none;' alt='Chartnote/Sign of Nurse are completed'>"; } else if($stub_tbl_chartSignedByNurse=='red') { $displayChkMarkImg2 = "<img src='images/nurse_red.jpg' style=' border:none;' alt='Chartnotes/Sign of Nurse are not completed'>"; }
                        //END CODE TO CHECK NURSE SIGNATURE
                        
                        
                        $ptNameBgClass = '';
                        if($patient_status!='Checked-Out') {
                            if($stub_tbl_recentChartSaved		== 'preopnursingrecord') 	{ $ptNameBgClass = 'tab_bg_pre_nurse';  
                            }else if($stub_tbl_recentChartSaved	== 'postopnursingrecord') 	{ $ptNameBgClass = 'tab_bg_post_nurse';  
                            }else if($stub_tbl_recentChartSaved	== 'operatingroomrecords') 	{ $ptNameBgClass = 'tab_bg_operating'; 
                            }
                        }
                        
                        $stub_pt_name = $patient_last_name.', '.$patient_first_name;
                        if(trim($patient_middle_name)) {
                            $stub_pt_name = $stub_pt_name." ".trim($patient_middle_name);
                        }
                        if(trim($stub_tbl_patient_dob_format)!="00/00/0000" && trim($stub_tbl_patient_dob_format)!=0) {
                            $stub_pt_name = $stub_pt_name." (".trim($stub_tbl_patient_dob_format).")";	
                        }
						
												$cntIolinkSxAlerts=	0;
												$qryIolinkSxAlert	=	"SELECT count(*) as TotalSxAlerts FROM iolink_patient_alert_tbl WHERE patient_id = '".$patientSearchPatientDataId."' AND iosync_status='Syncronized' AND alert_disabled!='yes'";
												$resIolinkSxAlert	=	imw_query($qryIolinkSxAlert)or die(imw_error());
												$rowIolinkSxAlert=	imw_fetch_assoc($resIolinkSxAlert) ;
												$cntIolinkSxAlerts=	$rowIolinkSxAlert['TotalSxAlerts'] ;
												
												$stub_surgeon_name = "";
												$stub_tbl_group_surgeon_fname = trim(stripslashes($surgeon_fname));
												$stub_tbl_group_surgeon_mname = trim(stripslashes($surgeon_mname));
												$stub_tbl_group_surgeon_lname = trim(stripslashes($surgeon_lname));
												$stub_surgeon_name = $stub_tbl_group_surgeon_fname.' '.$stub_tbl_group_surgeon_mname.' '.$stub_tbl_group_surgeon_lname;
												
												
												$practiceNameMatch = '';
												if($userType=='Surgeon' && $surgerycenter_peer_review=='Y' && ($stub_surgeon_name <> $loggedInSurgeonName)) {
													$specSrgPracName = $userPracNameArr["user_name"][strtolower(trim($stub_tbl_group_surgeon_fname))][strtolower(trim($stub_tbl_group_surgeon_mname))][strtolower(trim($stub_tbl_group_surgeon_lname))];
													$practiceNameMatch = $objManageData->getPracMatch($practiceName,$specSrgPracName);
												}

												?>
                        
                        <tr style=" background-color:#FFFFFF; <?php echo $strikeStyle;?> ">
                            <td data-title="Signed" class="text-center " style=" width:1%"><?php echo $displayChkMarkImg;?></td>
                            <td class="text-center" style=" width:1%"><?php echo $displayChkMarkImg1;?></td>
                            <td class="text-center" style=" width:1%"><?php echo $displayChkMarkImg2;?></td>
                            
                            <td data-title="Patient Name" class="capitalize text-left  <?php echo $ptNameBgClass;?>"><?php 
							if($allowChanges)
							{
								echo $multipleWindowOpenLink.$stub_pt_name.$ahref_link_end;
								$pt_alert_visibility="hidden";
								if($arrAlertEpost[$patientSearchPatientDataId][$patient_confirmation_id] || $cntIolinkSxAlerts > 0 ){
									$pt_alert_visibility="visible";
								}?>&nbsp;<a class="epost_trigger" style="padding:2px; text-decoration:none; visibility:<?php echo $pt_alert_visibility; ?>" id="alert_<?php echo $stub_id; ?>" >
								
									<b class="fa fa-exclamation-triangle" data-privileges="<?=$privileges?>" 
																		   data-privileges-type = "<?=$coordinatorType?>" 
																		   data-access-denied="<?=$accessDeniedCoordinator?>"
																		   data-stub-id = "<?=$stub_id?>"
																		   data-patient-search = "<?=$txt_patient_search?>"
																		   data-patient-id = "<?=$patientSearchPatientDataId?>"
																		   data-patient-confirmation-id = "<?=$patient_confirmation_id?>"
																		   data-imw-id	=	"<?=$patientSearchPatientImwPatientId?>"
																		   data-app-status = "<?=$showAllApptStatus?>"
																		   data-epost-id = "span_pt_alert<?=$stub_id;?>"
																		   data-epost-for = "alert"
                                       data-action-denied="<?=$practiceNameMatch?>" 
									></b></a>
                            <?php
							}
							else
							{
								echo"<a href=\"javascript:modalAlert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$stub_pt_name.$ahref_link_end; 	
							}
							?>    
                            </td>
                            <td data-title="DOS" class="text-left nowrap"><?php 
							if($allowChanges){
							echo $ahref_link_start.$stub_tbl_dos_show.$ahref_link_end; 
							}
							else
							{
								echo"<a href=\"javascript:modalAlert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$stub_tbl_dos_show.$ahref_link_end; 	
							}
							?></td>
                            <td data-title="Surgery" class="text-left nowrap" >
							<?php 
							if($allowChanges)
							{
								echo $ahref_link_start.$objManageData->getTmFormat($surgery_time).$ahref_link_end; 
							}
							else
							{
								echo"<a href=\"javascript:modalAlert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$objManageData->getTmFormat($surgery_time).$ahref_link_end; 
							}
							?></td>
                            <td data-title="Procedure" class="text-left">
                                <?php 
								if($allowChanges)
								{
									$patient_primary_procedure = wordwrap($patient_primary_procedure,26,"\n",1);
									$patient_secondary_procedure = wordwrap($patient_secondary_procedure,26,"\n",1);
									$patient_tertiary_procedure = wordwrap($patient_tertiary_procedure,26,"\n",1);
									echo $multipleWindowOpenLink.$setProcedureStyleStart.$patient_primary_procedure.$brTagSec.$patient_secondary_procedure.$brTagTer.$patient_tertiary_procedure.$setProcedureStyleEnd.$ahref_link_end; 
								}
								else
								{
									echo"<a href=\"javascript:modalAlert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$setProcedureStyleStart.$patient_primary_procedure.$setProcedureStyleEnd.$ahref_link_end;	
								}
								
								?>
                            </td>
                            <td data-title="Status" class="text-left">
                                <?php 
								
									if($allowChanges){
										if($patient_status=='Scheduled' || $patient_status=='Canceled' || $patient_status=='No Show' || $patient_status=='Aborted Surgery') {
									?>
											<select name="patient_status_list" class="selectpicker" data-width="100%" 
												onChange="javascript:<?php if($chartFinalizeStatus=='true') { ?>
																				alert('<?php echo $chartFinalizedAlert;?>');
																			<?php
																			}else if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>
																				alert('<?php echo $accessDeniedCoordinator;?>');
																			<?php
																			}else {?>
																				if(this.value=='Checked-In') { 
																					top.location.href='patient_confirm.php?stub_id=<?php echo $stub_id;?>&pConfId=<?php echo $patient_confirmation_id; ?>&reConfirmId=<?php echo $patient_confirmation_id; ?>&patient_id=<?php echo $patientSearchPatientDataId; ?>';  
																				}else { 
																					changeStatusCheckInCancel(<?php echo $stub_id; ?>, '<?php echo $txt_patient_search; ?>', '<?php echo $patient_status; ?>', '<?php echo $findBy; ?>', this); 
																				}
																	 <?php  }?>
														  ">
													<option value="Scheduled" 		<?php if($patient_status=='Scheduled') 		{ echo 'selected'; }?>>Scheduled</option>
													<option value="Canceled" 		<?php if($patient_status=='Canceled') 		{ echo 'selected'; }?>>Cancelled</option>
													<option value="Checked-In" 		<?php if($patient_status=='Checked-In') 	{ echo 'selected'; }?>>Checked-In</option>
													<option value="No Show" 		<?php if($patient_status=='No Show') 		{ echo 'selected'; }?>>No Show</option>
                                                    <option value="Aborted Surgery" <?php if($patient_status=='Aborted Surgery'){ echo 'selected'; }?>>Aborted Surgery</option>
											</select>
									<?php
										}else {
											
											
											if($chartFinalizeStatus=='true') { $ahref_CheckInlink_start = "<a href=\"javascript:alert('".$chartFinalizedAlert."');\" class='link_home' title='Change Status'>";  
											}else if($privileges=='Coordinator' && $coordinatorType!='Master') { $ahref_CheckInlink_start = "<a href=\"javascript:alert('".$accessDeniedCoordinator."');\" class='link_home' title='Change Status'>";}
											echo '<img src="images/tpixel.gif" style=" width:5px; height:1px; border:none;">'.$ahref_CheckInlink_start.$display_patient_status.$CheckedInOutTime.$ahref_link_end; 
										}
									}
									else
									{
										$patient_status=($patient_status=='Canceled')?'Cancelled':$patient_status;
										echo"<a href=\"javascript:modalAlert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$patient_status.$ahref_link_end; 	
									}
								
                                ?>
                            </td>
                            <td data-title="Comments" class="text-left">
                                <div class="comment_box">
                                	<textarea class="form-control" id="commentTextNew<?php echo $stub_id; ?>" style=" height:40px !important; " title="<?php echo stripslashes($stub_tbl_SearchComment); ?>" ><?php echo stripslashes($stub_tbl_SearchComment); ?></textarea><?php if($allowChanges && $practiceNameMatch <> 'yes'){?><a href="#" onClick="javascript:saveComment('<?php echo $stub_id; ?>', '<?php echo $txt_patient_search; ?>', '<?php echo $patient_status; ?>','commentTextNew<?php echo $stub_id; ?>','display_patient_search', '<?php echo $showAllApptStatus; ?>', '<?php echo $findBy; ?>');" class="link_home"><b>Save</b></a><?php }?>
                            	</div>
                            </td>
                            <td data-title="Scan" valign="top"  class="text-center <?php echo ($allowChanges)?$scan_class:'';?>" id="scan_bgId<?php echo $stub_id;?>"><?php 
							if($allowChanges)
							{
								if($patient_status=='Scheduled') {?><img style="cursor:pointer; border:none; " src="images/scanicon.png"  title="Scan" onClick="javascript:chkPatientExist('<?php echo $stub_id;?>','<?php echo $txt_patient_search;?>','<?php echo $patientSearchPatientDataId;?>','<?php echo $stub_tbl_dos?>','<?php echo $patient_confirmation_id;?>','<?php echo $patientSearchPatientImwPatientId;?>','<?php echo $showAllApptStatus;?>','<?php echo $practiceNameMatch;?>','<?php echo $findBy;?>'); "/><?php } 
							}
							?></td>
                            <td data-title="Edit"  class="text-center"><?php 
							if($allowChanges)
							{
								if($patient_status=='Checked-In' || $patient_status=='Checked-Out') {?><a onClick="javascript:<?php if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>alert('<?php echo $accessDeniedCoordinator;?>');<?php }else {?>top.location.href='patient_confirm.php?stub_id=<?php echo $stub_id;?>&pConfId=<?php echo $patient_confirmation_id;?>&reConfirmId=<?php echo $patient_confirmation_id;?>&patient_id=<?php echo $patientSearchPatientDataId; ?>'<?php } ?>" target="_top" class="btn-xs btn btn-success edit_btn" title="Edit"><b class="fa fa-edit"></b></a><?php } 
							}
							?></td>
                            
                            <td data-title="Epost" class="text-center <?php echo $epost_class;?>" ><?php 
							if($allowChanges)
							{
								if($patient_status<>'Canceled') {?>
                                
                                	<div id="epost_<?=$stub_id?>" class="box epost_trigger <?PHP echo $epostSearch_class ?> "><a class="btn-xs btn btn-primary" title="ePostIt">
                            		<b class="fa fa-comment" data-privileges="<?=$privileges?>" 
                                									   data-privileges-type = "<?=$coordinatorType?>" 
                                                                       data-access-denied="<?=$accessDeniedCoordinator?>"
                                                                       data-stub-id = "<?=$stub_id?>"
                                                                       data-patient-search = "<?=$txt_patient_search?>"
                                                                       data-patient-id = "<?=$patientSearchPatientDataId?>"
                                                                       data-patient-confirmation-id = "<?=$patient_confirmation_id?>"
                                                                       data-imw-id	=	"<?=$patientSearchPatientImwPatientId?>"
                                                                       data-app-status = "<?=$showAllApptStatus?>"
                                                                       data-epost-id = "span_pt_alert<?=$stub_id;?>"
                                                                       data-epost-for =	"epost"
                                                                       data-action-denied="<?=$practiceNameMatch?>"
                                                                       ></b></a></div>
                            <?php } 
							}
							?>
                           	
                            </td>
                            
                        </tr>
                        <?php
                    }
                }
            }
        }
    
        //CODE TO DISPLAY PATIENT SEARCH RESULTS
        $chartFinalizeStatus="";
        $txt_patient_search = trim($_GET["txt_patient_search_id"]);
        if($txt_patient_search <>"" && ($findBy != "External_MRN" && $findBy != "Patient_Name")) {
            if(is_numeric($txt_patient_search)) { 
                $getComfirmationQry = imw_query("SELECT pc.patientId, pc.ascId, pc.finalize_status, stub_tbl.iasc_facility_id, stub_tbl.stub_id as searchStubId FROM patientconfirmation pc
												INNER JOIN patient_data_tbl pd ON pd.patient_id = pc.patientId
												INNER JOIN stub_tbl ON pc.patientConfirmationId=stub_tbl.patient_confirmation_id
                                                WHERE pc.ascId = '$txt_patient_search'
												AND stub_tbl.patient_id_stub!=''
                                                AND pc.patientStatus!='Scheduled' 
                                                AND pc.patientStatus!='' ORDER BY stub_tbl.iasc_facility_id,pc.dos LIMIT 0,1")or die(imw_error());//$fac_con
                if(imw_num_rows($getComfirmationQry)>0){
                    while($getComfirmationRows = imw_fetch_assoc($getComfirmationQry)){
                        extract($getComfirmationRows);
                        $getComfirmationAscId = $ascId;
                        $chartFinalizeStatus = $finalize_status;
						
                        $getPatientSearchQry = "select pd.*, DATE_FORMAT(date_of_birth,'%m/%d/%Y') as date_of_birth_format , stub_tbl.iasc_facility_id, stub_tbl.surgeon_fname,stub_tbl.surgeon_mname,stub_tbl.surgeon_lname
												from patient_data_tbl as pd 
												JOIN stub_tbl ON (pd.patient_id = stub_tbl.patient_id_stub && stub_tbl.stub_id = $searchStubId) 
												where pd.patient_id = '$patientId' GROUP BY pd.patient_id"; 
                    }
                }
                
            }
			else{
                if(strpos($txt_patient_search, ',')){
                    $searchKeywordArr = explode(",", $txt_patient_search);
                    $patientLastName = trim($searchKeywordArr[0]);
                    $patientFirstName = trim($searchKeywordArr[1]);
                    $getPatientSearchQry 	 = "select pd.*, DATE_FORMAT(pd.date_of_birth,'%m/%d/%Y') as date_of_birth_format, stub_tbl.iasc_facility_id 
												from patient_data_tbl as pd 
												LEFT JOIN stub_tbl ON pd.patient_id = stub_tbl.patient_id_stub 
												where pd.patient_fname LIKE '$patientFirstName%'
                                                AND pd.patient_lname LIKE '$patientLastName%' 
												AND stub_tbl.patient_id_stub!=''  
												ORDER BY stub_tbl.iasc_facility_id,pd.`patient_fname` ";//$fac_con
												
                }else{
                    $getPatientSearchQry 	 = "select pd.*, DATE_FORMAT(pd.date_of_birth,'%m/%d/%Y') as date_of_birth_format , stub_tbl.iasc_facility_id
												from patient_data_tbl as pd 
												LEFT JOIN stub_tbl ON pd.patient_id = stub_tbl.patient_id_stub 
												where (pd.patient_fname LIKE '$txt_patient_search%'
                                                OR pd.patient_lname LIKE '$txt_patient_search%')
												AND stub_tbl.patient_id_stub!=''  
												ORDER BY stub_tbl.iasc_facility_id,pd.`patient_fname`"; //$fac_con
												
                }
            }						
            if($getPatientSearchQry) {
                $getPatientSearchRes = imw_query($getPatientSearchQry) or die($getPatientSearchQry.'error1'.imw_error());
                $getPatientSearchNumRow = imw_num_rows($getPatientSearchRes);
				$countRows = $getPatientSearchNumRow;
            }
            
            
            
            
            if($getPatientSearchNumRow > 0 && is_numeric($txt_patient_search) ) {
           	
                while($getPatientSearchRow = imw_fetch_array($getPatientSearchRes)) {
					#CHECK IS THAT A LOGGED IN FACILITY TO ALLOW MAKE CHANGES IN PATIENT DATA
					$allowChanges=0;
					$allowChanges=($getPatientSearchRow['iasc_facility_id']==$_SESSION['iasc_facility_id'])?1:0;
					#ALERT TO SHOW OTHER ASC PATIENTS
					$otherFacAlert='Please login to ASC - '.$facility_arr[$getPatientSearchRow['iasc_facility_id']].' to access this chart';
					
					#CHECKING FACILITY ID TO CREATE FACILITY WISE HEADER ON FIRST RECORD
					if(!in_array($getPatientSearchRow['iasc_facility_id'],$stub_tbl_iasc_groupTemp)) 
						{
							?><thead class="cf">
                            <tr class="caption">
                                <td  class="text-center rob" colspan="13">ASC - <strong><?php echo $facility_arr[$getPatientSearchRow['iasc_facility_id']];?></strong></td>
                            </tr>
                            <tr >
                        	    <th class="text-center" colspan="3">Signed</th>
                                <th  class="text-left">Patient Name</th>
                                <th class="text-left">ASCID</th>
                                <th class="text-left">DOB</th>
                                <th class="text-left">Procedure</th>
                                <th class="text-left">DOS</th>
                                <th class="text-left">ID</th>
                                <th class="text-center">Comments</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Edit</th>
                                <th class="text-left">ePost</th>
                            </tr>
                            </thead>
                            <?php
							$stub_tbl_iasc_groupTemp[]=$getPatientSearchRow['iasc_facility_id'];
						}
						
                    $patientSearch_id = $getPatientSearchRow['patient_id'];
                    $patientSearch_fname = $getPatientSearchRow['patient_fname'];
                    $patientSearch_mname = $getPatientSearchRow['patient_mname'];
                    $patientSearch_lname = $getPatientSearchRow['patient_lname'];
                    $patientSearch_dob_format = $getPatientSearchRow['date_of_birth_format'];
                    $patientSearch_name = $patientSearch_lname.", ".$patientSearch_fname;
                    if(trim($patientSearch_mname)) {
                        $patientSearch_name = $patientSearch_name." ".trim($patientSearch_mname);
                    }
                    if(trim($patientSearch_dob_format)!="00/00/0000" && trim($patientSearch_dob_format)!=0) {
                        $patientSearch_name = $patientSearch_name." (".trim($patientSearch_dob_format).")";	
                    }
                    $patientSearchImwPatientId = $getPatientSearchRow['imwPatientId'];
    
                    $patientSearch_asc_id  = $getPatientSearchRow['asc_id'];
                    $patientSearch_dob_temp = $getPatientSearchRow['date_of_birth'];
                    $patientSearch_dob_split = explode("-",$patientSearch_dob_temp);
                    $patientSearch_dob = $patientSearch_dob_split[1]."-".$patientSearch_dob_split[2]."-".$patientSearch_dob_split[0];
    
                    $andGetPatientSearchConfirmQry="";
                    if($getComfirmationAscId) { $andGetPatientSearchConfirmQry = "AND ascId='".$getComfirmationAscId."'"; }
                    
                    $getPatientSearchConfirmQry = "select * from patientconfirmation where patientId = '$patientSearch_id' $andGetPatientSearchConfirmQry ORDER BY patientConfirmationId DESC"; 
                    $getPatientSearchConfirmRes = imw_query($getPatientSearchConfirmQry) or die(imw_error());
                    $getPatientSearchConfirmNumRow = imw_num_rows($getPatientSearchConfirmRes);
                    
                    if($getPatientSearchConfirmNumRow > 0) {
                        $getPatientSearchConfirmRow = imw_fetch_array($getPatientSearchConfirmRes);
                        $patientSearchConfirm_id = $getPatientSearchConfirmRow["patientConfirmationId"];
                        $patientSearchSurgeon_id = $getPatientSearchConfirmRow["surgeonId"];
                        $patientSearch_dos_temp = $getPatientSearchConfirmRow["dos"];
                        $patientSearch_dos_split = explode("-",$patientSearch_dos_temp);
                        $patientSearch_dos = $patientSearch_dos_split[1]."-".$patientSearch_dos_split[2]."-".$patientSearch_dos_split[0];
                        $patientSearch_prim_procedure = $getPatientSearchConfirmRow["patient_primary_procedure"];
                        $patientSearch_sec_procedure = $getPatientSearchConfirmRow["patient_secondary_procedure"];
                        $patientSearch_ter_procedure = $getPatientSearchConfirmRow["patient_tertiary_procedure"];
                        $patientSearch_site 		= $getPatientSearchConfirmRow["site"];
												$patientSearch_sec_site = $getPatientSearchConfirmRow["secondary_site"];
												$patientSearch_sec_site	=	($patientSearch_sec_site)	?	$patientSearch_sec_site	:	$patientSearch_site;
												$patientSearch_ter_site = $getPatientSearchConfirmRow["tertiary_site"];
												$patientSearch_ter_site	=	($patientSearch_ter_site)	?	$patientSearch_ter_site	:	$patientSearch_site;
												
												// Start change site to OS/OD/OU
												$primarySite	=	$secondarySite	=	$tertiarySite	=	'';
												if($patientSearch_site == '1'){
													$primarySite	=	'OS';
												}else if($patientSearch_site == '2'){
													$primarySite	=	'OD';
												}else if($patientSearch_site=='3'){
													$primarySite	=	'OU';
												}else if($patientSearch_site=='4'){
													$primarySite	=	'LUL';
												}else if($patientSearch_site=='5'){
													$primarySite	=	'LLL';
												}else if($patientSearch_site=='6'){
													$primarySite	=	'RUL';
												}else if($patientSearch_site=='7'){
													$primarySite	=	'RLL';
												}												
												
												if($patientSearch_sec_site == '1'){
													$secondarySite	=	'OS';
												}else if($patientSearch_sec_site == '2'){
													$secondarySite	=	'OD';
												}else if($patientSearch_sec_site=='3'){
													$secondarySite	=	'OU';
												}else if($patientSearch_sec_site=='4'){
													$secondarySite	=	'LUL';
												}else if($patientSearch_sec_site=='5'){
													$secondarySite	=	'LLL';
												}else if($patientSearch_sec_site=='6'){
													$secondarySite	=	'RUL';
												}else if($patientSearch_sec_site=='7'){
													$secondarySite	=	'RLL';
												}
												
												if($patientSearch_ter_site == '1'){
													$tertiarySite	=	'OS';
												}else if($patientSearch_ter_site == '2'){
													$tertiarySite	=	'OD';
												}else if($patientSearch_ter_site=='3'){
													$tertiarySite	=	'OU';
												}else if($patientSearch_ter_site=='4'){
													$tertiarySite	=	'LUL';
												}else if($patientSearch_ter_site=='5'){
													$tertiarySite	=	'LLL';
												}else if($patientSearch_ter_site=='6'){
													$tertiarySite	=	'RUL';
												}else if($patientSearch_ter_site=='7'){
													$tertiarySite	=	'RLL';
												}
												
												// End change site to OS/OD/OU
												
												//START APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
												$patientSearch_prim_procedure .= ($patientSearch_prim_procedure)?	' '.$primarySite 		: '';
												$patientSearch_sec_procedure 	.= ($patientSearch_sec_procedure)	?	' '.$secondarySite 	: '';
												$patientSearch_ter_procedure 	.= ($patientSearch_ter_procedure)	?	' '.$tertiarySite 		:	'';	
												
												$patientSearch_prim_procedure = trim($patientSearch_prim_procedure);
												$patientSearch_sec_procedure 	= trim($patientSearch_sec_procedure);
												$patientSearch_ter_procedure 	= trim($patientSearch_ter_procedure);	
												
												//END APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
                        
												$brTagSec = $brTagTer = '';
												if($patientSearch_prim_procedure && $patientSearch_sec_procedure)
												{
													$brTagSec = '<br>';	
												}
												if($patientSearch_prim_procedure && $patientSearch_ter_procedure)
												{
													$brTagTer = '<br>';	
												}
												
												/*
												if($patientSearch_prim_procedure<>"") {
                            $patientSearch_procedure = $patientSearch_prim_procedure;
                        }elseif($patientSearch_sec_procedure<>"") { 
                            $patientSearch_procedure = $patientSearch_sec_procedure;
                        }else {
                            $patientSearch_procedure = $patientSearch_ter_procedure;
                        }
                        
                        //START APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
                            if($patientSearch_site=='1') {  //LEFT
                                $patientSearch_procedure = $patientSearch_procedure.' OS';
                            }else if($patientSearch_site=='2') { //RIGHT
                                $patientSearch_procedure = $patientSearch_procedure.' OD';
                            }else if($patientSearch_site=='3') { //BOTH
                                $patientSearch_procedure = $patientSearch_procedure.' OU';
                            }
                        //END APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
                        */
                        $patientSearch_ascId = $getPatientSearchConfirmRow["ascId"];
                        
                        //START SEARCH PATIENT FROM STUB (IMW)
                            $getPatientSearchStubConfirmQry = "select * from stub_tbl where patient_confirmation_id = '$patientSearchConfirm_id' $fac_con"; 
                            $getPatientSearchStubConfirmRes = imw_query($getPatientSearchStubConfirmQry) or die(imw_error());
                            if(imw_num_rows($getPatientSearchStubConfirmRes)) {
                                $getPatientSearchStubConfirmRow = imw_fetch_array($getPatientSearchStubConfirmRes);
                                $getPatientSearchStubConfirmStatus = $getPatientSearchStubConfirmRow["patient_status"];
                                
                                $getPatientSearchStubCheckedInTime = $objManageData->getTmFormat($getPatientSearchStubConfirmRow["checked_in_time"]);
                                $getPatientSearchStubCheckedOutTime = $objManageData->getTmFormat($getPatientSearchStubConfirmRow["checked_out_time"]);
                                
                                $getPatientSearchStubConfirmStubId = $getPatientSearchStubConfirmRow["stub_id"];
                                $getPatientSearchStubConfirmStubComment = $getPatientSearchStubConfirmRow["comment"];
                                $stub_tbl_chartSignedBySurgeon 	= $getPatientSearchStubConfirmRow["chartSignedBySurgeon"];
                                $stub_tbl_chartSignedByNurse 	= $getPatientSearchStubConfirmRow["chartSignedByNurse"];
                                $stub_tbl_chartSignedByAnes 	= $getPatientSearchStubConfirmRow["chartSignedByAnes"];
                                $stub_tbl_recentChartSaved 		= $getPatientSearchStubConfirmRow["recentChartSaved"];
                                
                            
                            }
                            
                        //END SEARCH PATIENT FROM STUB (IMW)
                        
                            $CheckedInOutSearchTime = "";
                            $ahref_link_start = "<a class='link_home' target='_top' style='cursor:pointer;' href='mainpage.php?patient_id=$patientSearch_id&amp;pConfId=$patientSearchConfirm_id&amp;ascId=$patientSearch_asc_id'>";
                            $display_getPatientSearchStubConfirmStatus = "";
                            
                           // if($getPatientSearchStubConfirmStatus=="Canceled") {
                             //   $ahref_link_start = "<a href='#' class='link_home' style='cursor:pointer;' onClick=\"javascript:alert('Patient status is Canceled.')\">";
                            //}
                            if($getPatientSearchStubConfirmStatus=='Checked-In'){ 
                                $ahref_CheckInlink_start = "<a href=\"javascript:changeStatusCheckInCancel('".$getPatientSearchStubConfirmStubId."','".$txt_patient_search."','".$getPatientSearchStubConfirmStatus."','".$findBy."', '');\" class='link_home' title='Change Status'>";
                                $display_getPatientSearchStubConfirmStatus = '<i style="color:rgb(0,128,0);font-weight:bold;">CI</i>';
                                if($getPatientSearchStubCheckedInTime) {
                                    $CheckedInOutSearchTime = '<span style="color:rgb(0,128,0);font-weight:bold;"> - '.$getPatientSearchStubCheckedInTime.'</span';
                                }
                            }else if($getPatientSearchStubConfirmStatus=='Checked-Out'){ 
                                $ahref_CheckInlink_start = "<a href=\"javascript:changeStatusCheckInCancel('".$getPatientSearchStubConfirmStubId."','".$txt_patient_search."','".$getPatientSearchStubConfirmStatus."','".$findBy."', '');\" class='link_home' title='Change Status'>";
                                $display_getPatientSearchStubConfirmStatus = '<i style="color:#0033CC;font-weight:bold;">CO</i>';
                                if($getPatientSearchStubCheckedOutTime) {
                                    $CheckedInOutSearchTime = '<span style="color:#0033CC;font-weight:bold;"> - '.$getPatientSearchStubCheckedOutTime.'</span>';
                                }
                            }else{
                                $ahref_CheckInlink_start = $ahref_link_start;
                                //$display_getPatientSearchStubConfirmStatus = $getPatientSearchStubConfirmStatus;
                            }
                            $ahref_link_end = "</a>";
                            
                            $displayChkMarkImg='';
                            if($patientSearchConfirm_id<>'0') { 
                                $multipleWindowOpenLink = "<a  class=\"link_home\" href=\"javascript:top.setwin('ChartNote','$getPatientSearchStubConfirmStubId','patient_without_confirm.php?showAllApptStatus=$showAllApptStatus&stub_id=$getPatientSearchStubConfirmStubId&pConfId=$patientSearchConfirm_id&multiwin=yes','ChartNote$getPatientSearchStubConfirmStubId');\">";		
                            }else {
                                $multipleWindowOpenLink=$ahref_link_start;
                            }
                            $setProcedureStyleSearchStart = "";
                            $setProcedureStyleSearchEnd = "";
                            if($getPatientSearchStubConfirmStatus=='Checked-In' ){ $setProcedureStyleSearchStart = '<span style="color:rgb(0,128,0);font-weight:bold;">';$setProcedureStyleSearchEnd = '</span>'; }
                            if($getPatientSearchStubConfirmStatus=='Checked-Out'){ $setProcedureStyleSearchStart = '<span style="color:#0033CC;font-weight:bold;">';$setProcedureStyleSearchEnd = '</span>';  }
                            $showData='yes';
														
														$practiceNameMatch = '';
                            if($userType == 'Surgeon') {
															
																$patientSearchSurgeonNameArr = array();
																$patientSearchSurgeonNameArr = getUsrNm($patientSearchSurgeon_id);
																$patientSearchSurgeon_name = $patientSearchSurgeonNameArr[0];
																	
																if($patientSearchSurgeon_id!=$_SESSION['loginUserId']) {
																		
																		if($surgerycenter_peer_review == 'Y')
																		{
																			$stub_tbl_group_surgeon_fname = trim(stripslashes($getPatientSearchRow['surgeon_fname']));
																			$stub_tbl_group_surgeon_mname = trim(stripslashes($getPatientSearchRow['surgeon_mname']));
																			$stub_tbl_group_surgeon_lname = trim(stripslashes($getPatientSearchRow['surgeon_lname']));

																			$specSrgPracName = $userPracNameArr["user_name"][strtolower(trim($stub_tbl_group_surgeon_fname))][strtolower(trim($stub_tbl_group_surgeon_mname))][strtolower(trim($stub_tbl_group_surgeon_lname))];
																			$practiceNameMatch = $objManageData->getPracMatch($practiceName,$specSrgPracName);
																		}
																		
																		if($practiceNameMatch <> 'yes') 
																		{
																			$ahref_link_start = $multipleWindowOpenLink= $ahref_CheckInlink_start = "<a href='#' class='link_home' style='cursor:pointer;' onClick=\"javascript:alert('Patient is registered to Dr. $patientSearchSurgeon_name')\">";	
																			$showData='';
																		}
																}
																
																
                            }
                        
                            //START CODE TO CHECK IF ANY ENTRY OF PATIENT EXIST IN eposted TABLE
                                $patientSearchEpostStr = "SELECT epost_id FROM eposted 
                                                    WHERE patient_id = '$patientSearch_id'
                                                     AND  patient_id != ''
                                                     AND  patient_id != '0'
                                                     AND  patient_conf_id = '$patientSearchConfirm_id' AND table_name!='alert'";
                                $patientSearchEpostQry = imw_query($patientSearchEpostStr);
                                $patientSearchEpostNumRows = imw_num_rows($patientSearchEpostQry);
                                if($patientSearchEpostNumRows > 0) {
                                    $epostSearch_class = 'alert-epost';
                                }else {
                                    $epostSearch_class = '';
                                }
                          //END CODE TO CHECK IF ANY ENTRY OF PATIENT EXIST IN eposted TABLE									
                        
                          $displayChkMarkImg="<b><img src='images/sign_blank.jpg' style='border:none;'></b>";
                          $displayChkMarkImg1="<b><img src='images/sign_blank.jpg' style='border:none;'></b>";
                          $displayChkMarkImg2="<b><img src='images/sign_blank.jpg' style='border:none;'></b>";
                          //CODE TO CHECK SURGEON SIGNATURE
                            if($stub_tbl_chartSignedBySurgeon=='green') { $displayChkMarkImg = "<img src='images/surgeon_green.jpg' style=' border:none;' alt='Chartnote/Sign of surgeon are completed'>"; } else if($stub_tbl_chartSignedBySurgeon=='red')  { $displayChkMarkImg = "<img src='images/surgeon_red.jpg' style=' border:none;' alt='Chartnotes/Sign of Surgeon are not completed'>";}
                          //END CODE TO CHECK SURGEON SIGNATURE
                            
                          //CODE TO CHECK ANES SIGNATURE
                             if($stub_tbl_chartSignedByAnes=='green') { $displayChkMarkImg1 = "<img src='images/anes_green.jpg' style=' border:none;' alt='Chartnote/Sign of Anesthesiologist are completed'>"; } else if($stub_tbl_chartSignedByAnes=='red') { $displayChkMarkImg1 = "<img src='images/anes_red.jpg' style=' border:none;' alt='Chartnotes/Sign of Anesthesiologist are not completed'>"; }
                          //END CODE TO CHECK ANES SIGNATURE
                            
                          //CODE TO CHECK NURSE SIGNATURE
                            if($stub_tbl_chartSignedByNurse=='green') { $displayChkMarkImg2 = "<img src='images/nurse_green.jpg' style=' border:none;' alt='Chartnote/Sign of Nurse are completed'>"; } else if($stub_tbl_chartSignedByNurse=='red') { $displayChkMarkImg2 = "<img src='images/nurse_red.jpg' style=' border:none;' alt='Chartnotes/Sign of Nurse are not completed'>"; }
                          //END CODE TO CHECK NURSE SIGNATURE
                          
                          
                        $ptNameBgClass = '';
                        if($getPatientSearchStubConfirmStatus!='Checked-Out') {
                            if($stub_tbl_recentChartSaved		== 'preopnursingrecord') 	{ $ptNameBgClass = 'tab_bg_pre_nurse';  
                            }else if($stub_tbl_recentChartSaved	== 'postopnursingrecord') 	{ $ptNameBgClass = 'tab_bg_post_nurse';  
                            }else if($stub_tbl_recentChartSaved	== 'operatingroomrecords') 	{ $ptNameBgClass = 'tab_bg_operating'; 
                            }		
                        }
                        
						$cntIolinkSxAlerts=	0;
						$qryIolinkSxAlert	=	"SELECT count(*) as TotalSxAlerts FROM iolink_patient_alert_tbl WHERE patient_id = '".$patientSearch_id."' AND iosync_status='Syncronized' AND alert_disabled!='yes'";
						$resIolinkSxAlert	=	imw_query($qryIolinkSxAlert)or die(imw_error());
						$rowIolinkSxAlert=	imw_fetch_assoc($resIolinkSxAlert) ;
						$cntIolinkSxAlerts=	$rowIolinkSxAlert['TotalSxAlerts'] ;
                        ?>
                        <tr >
                            <td data-title="Signed" class="text-center " style="width:1%;"><?php echo $displayChkMarkImg;?></td>
                            <td class="text-center" style="width:1%;"><?php echo $displayChkMarkImg1;?></td>
                            <td class="text-center" style="width:1%;"><?php echo $displayChkMarkImg2;?></td>
                            
                            <td data-title="Patient Name" class="capitalize text-left <?php echo $ptNameBgClass;?>" ><?php 
							if($allowChanges)
							{
							echo $multipleWindowOpenLink.$patientSearch_name.$ahref_link_end;
                            $pt_alert_visibility="hidden";
                            if($arrAlertEpost[$patientSearch_id][$patientSearchConfirm_id] || $cntIolinkSxAlerts > 0 ){
                                $pt_alert_visibility="visible";
                            }?>&nbsp;<a class="epost_trigger" style="padding:2px; text-decoration:none; visibility:<?php echo $pt_alert_visibility; ?>" id="alert_<?php echo $getPatientSearchStubConfirmStubId; ?>" >
                            	<b class="fa fa-exclamation-triangle" data-privileges="<?=$privileges?>" 
                                									   data-privileges-type = "<?=$coordinatorType?>" 
                                                                       data-access-denied="<?=$accessDeniedCoordinator?>"
                                                                       data-stub-id = "<?=$getPatientSearchStubConfirmStubId?>"
                                                                       data-patient-search = "<?=$txt_patient_search?>"
                                                                       data-patient-id = "<?=$patientSearch_id?>"
                                                                       data-patient-confirmation-id = "<?=$patientSearchConfirm_id?>"
                                                                       data-imw-id	=	"<?=$patientSearchImwPatientId?>"
                                                                       data-app-status = "<?=$showAllApptStatus?>"
                                                                       data-epost-id = "span_pt_alert<?=$getPatientSearchStubConfirmStubId;?>"
                                                                       data-epost-for = "alert"
                                                                       data-action-denied="<?=$practiceNameMatch?>"
                            	></b></a>
                            
                           <?php
							}
							else
							{
								echo"<a href=\"javascript:modalAlert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$patientSearch_name.$ahref_link_end; 	
							}
							?>   </td>
                            <td data-title="ASCID" class="text-left "><?php 
							if($allowChanges)
							{
							echo $ahref_link_start.$patientSearch_ascId.$ahref_link_end;//echo $ahref_link_start.$patientSearch_asc_id.$ahref_link_end;
							}
							else
							{
								echo"<a href=\"javascript:modalAlert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$patientSearch_ascId.$ahref_link_end; 	
							}
							?></td>
                            <td data-title="DOB" class="text-left nowrap" ><?php 
							if($allowChanges)
							{
								echo $ahref_link_start.$patientSearch_dob.$ahref_link_end;
							}
							else
							{
								echo"<a href=\"javascript:modalAlert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$patientSearch_dob.$ahref_link_end; 	
							}
							?></td>
                            <td data-title="Procedure" class="text-left ">
                                <?php 
								if($allowChanges)
								{
									$patientSearch_procedure = wordwrap($patientSearch_procedure,26,"\n",1);
									echo $multipleWindowOpenLink.$setProcedureStyleSearchStart.$patientSearch_prim_procedure.$brTagSec.$patientSearch_sec_procedure.$brTagTer.$patientSearch_ter_procedure.$setProcedureStyleSearchEnd.$ahref_link_end;
								}
							else
							{
								echo"<a href=\"javascript:modalAlert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$setProcedureStyleSearchStart.$patientSearch_procedure.$setProcedureStyleSearchEnd.$ahref_link_end; 	
							}	
									?>
                            </td>
                            <td data-title="DOS" class="text-left nowrap"><?php 
							if($allowChanges)
							{
								echo $ahref_link_start.$patientSearch_dos.$ahref_link_end;
							}
							else
							{
								echo"<a href=\"javascript:modalAlert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$patientSearch_dos.$ahref_link_end; 	
							}
							?></td>
                            <td data-title="ID" class="text-left "><?php 
							if($allowChanges)
							{
								echo $ahref_link_start.$patientSearch_id.$ahref_link_end;
							}
							else
							{
								echo"<a href=\"javascript:alert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$patientSearch_id.$ahref_link_end; 	
							}
							?></td>
                            <td data-title="Comments" class="text-left">
                                <div class="comment_box">


                                    <textarea class="form-control" id="commentTextAnother<?php echo $getPatientSearchStubConfirmStubId; ?>" style=" height:40px !important; " title="<?php echo stripslashes($getPatientSearchStubConfirmStubComment); ?>"><?php echo stripslashes($getPatientSearchStubConfirmStubComment); ?></textarea><?php if($showData=='yes' && $allowChanges && $practiceNameMatch <> 'yes') {?><a href="#" onClick="javascript:saveComment('<?php echo $getPatientSearchStubConfirmStubId; ?>', '<?php echo $txt_patient_search; ?>', '<?php echo $getPatientSearchStubConfirmStatus; ?>','commentTextAnother<?php echo $getPatientSearchStubConfirmStubId; ?>','display_patient_search', '<?php echo $showAllApptStatus; ?>', '<?php echo $findBy; ?>');" class="link_home"><b>Save</b></a><?php } ?>
                                </div>
                            </td>
                            <td data-title="Status" class="text-left nowrap">
                                <?php 
								if($allowChanges)
								{
                                    if($getPatientSearchStubConfirmStatus=='Scheduled' || $getPatientSearchStubConfirmStatus=='Canceled')  {
                                ?>
                                        <select name="patient_status_list" class="selectpicker" 
                                            onChange="javascript:<?php if($chartFinalizeStatus=='true') { ?>
                                                                            alert('<?php echo $chartFinalizedAlert;?>');
                                                                 <?php }else if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>
                                                                            alert('<?php echo $accessDeniedCoordinator;?>');
                                                                 <?php }else {?>
                                                                            if(this.value=='Checked-In') { 
                                                                                top.location.href='patient_confirm.php?stub_id=<?php echo $getPatientSearchStubConfirmStubId;?>&pConfId=<?php echo $patientSearchConfirm_id; ?>&reConfirmId=<?php echo $patientSearchConfirm_id; ?>&patient_id=<?php echo $patientSearch_id; ?>';  
                                                                            }else { 
                                                                                changeStatusCheckInCancel(<?php echo $getPatientSearchStubConfirmStubId; ?>, '<?php echo $txt_patient_search; ?>', '<?php echo $getPatientSearchStubConfirmStatus; ?>', '<?php echo $findBy; ?>', this); 
                                                                            }
                                                                <?php  } ?>		
                                                    ">
                                                <option value="Scheduled"  		<?php if($getPatientSearchStubConfirmStatus=='Scheduled')  		{ echo 'selected'; }?>>Scheduled</option>
                                                <option value="Canceled"   		<?php if($getPatientSearchStubConfirmStatus=='Canceled')   		{ echo 'selected'; }?>>Cancelled</option>
                                                <option value="Checked-In" 		<?php if($getPatientSearchStubConfirmStatus=='Checked-In') 		{ echo 'selected'; }?>>Checked-In</option>
                                                <option value="No Show"   		<?php if($getPatientSearchStubConfirmStatus=='No Show')   		{ echo 'selected'; }?>>No Show</option>
                                                <option value="Aborted Surgery"	<?php if($getPatientSearchStubConfirmStatus=='Aborted Surgery')	{ echo 'selected'; }?>>Aborted Surgery</option>
                                        </select>
                                <?php
                                    }else{
                                        if($chartFinalizeStatus=='true') { $ahref_CheckInlink_start = "<a href=\"javascript:alert('".$chartFinalizedAlert."');\" class='link_home' title='Change Status'>";
                                        }else if($privileges=='Coordinator' && $coordinatorType!='Master') { $ahref_CheckInlink_start = "<a href=\"javascript:alert('".$accessDeniedCoordinator."');\" class='link_home' title='Change Status'>";}
                                        echo $ahref_CheckInlink_start.$display_getPatientSearchStubConfirmStatus.$CheckedInOutSearchTime.$ahref_link_end;
                                    }	
								}
								else
								{
									$getPatientSearchStubConfirmStatus=($getPatientSearchStubConfirmStatus=='Canceled')?'Cancelled':$getPatientSearchStubConfirmStatus;
									echo"<a href=\"javascript:modalAlert('".$otherFacAlert."');\" class='link_home' title='Change Status'>".$getPatientSearchStubConfirmStatus.$ahref_link_end; 			
								}
								?>
                            </td>
                            <td data-title="Edit"  class="text-center">
                                <?php 
								if($allowChanges)
								{
								if($patientSearchConfirm_id<>'0' && $showData=='yes') {?>
                                    <a onClick="javascript:<?php if($privileges=='Coordinator' && $coordinatorType!='Master') { ?>alert('<?php echo $accessDeniedCoordinator;?>');<?php }else {?>top.location.href='patient_confirm.php?stub_id=<?php echo $getPatientSearchStubConfirmStubId;?>&pConfId=<?php echo $patientSearchConfirm_id;?>&reConfirmId=<?php echo $patientSearchConfirm_id;?>&patient_id=<?php echo $patientSearch_id; ?>'<?php } ?>" target="_top" class="btn-xs btn btn-success edit_btn" title="Edit">
                                        <b class="fa fa-edit"></b>
                                    </a>
                                <?php } 
								}
								?>
                            </td>
                            <td data-title="Epost" class="text-center <?php echo $epostSearchConfirm_class;?>"><?php 
							if($allowChanges)
							{
							if($getPatientSearchStubConfirmStatus<>'Canceled' && $showData=='yes') {?>
                            <div id="epost_<?=$getPatientSearchStubConfirmStubId?>" class="box epost_trigger <?PHP echo $epostSearch_class ?> "><a class="btn-xs btn btn-primary" title="ePostIt">
                            
                            	<b class="fa fa-comment " data-privileges="<?=$privileges?>" 
                                									   data-privileges-type = "<?=$coordinatorType?>" 
                                                                       data-access-denied="<?=$accessDeniedCoordinator?>"
                                                                       data-stub-id = "<?=$getPatientSearchStubConfirmStubId?>"
                                                                       data-patient-search = "<?=$txt_patient_search?>"
                                                                       data-patient-id = "<?=$patientSearch_id?>"
                                                                       data-patient-confirmation-id = "<?=$patientSearchConfirm_id?>"
                                                                       data-imw-id	=	"<?=$patientSearchImwPatientId?>"
                                                                       data-app-status = "<?=$showAllApptStatus?>"
                                                                       data-epost-id = "span_pt_alert<?=$getPatientSearchStubConfirmStubId;?>"
                                                                       data-epost-for =	"epost"
                                                                       data-action-denied="<?=$practiceNameMatch?>"
                            	></b></a></div>
                                <?php } }?></td>
                        </tr>
                         
                    <?php 
                    }
                } // END WHILE LOOP
                
            }/*else{
                if($countRows<=0){
                    ?>
                        <tr>
                            <td class="text-center nowrap">
                                <b>No Record Found !</b>
                            </td>
                        </tr>
                    <?php
                }
            }*/
        }
			if($countRows<=0){
				?>
					<tr>
						<td class="text-center nowrap">
							<b>No Record Found !</b>
						</td>
					</tr>
				<?php
			}
		
        ?>
    </table>
</div>
</body>
</html>			
<!-- END DISPLAY PATIENT SEARCH RESULT -->