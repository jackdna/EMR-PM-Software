<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

include_once("insurance_default_case.php"); //$defaultCaseName IS SET FROM imwemr IN THIS FILE
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php"); 
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<script src="js/webtoolkit.aim.js"></script>
<script type="text/javascript" src="js/actb.js"></script>
<script type="text/javascript" src="js/common.js"></script>	
<?php
$spec= "
</head>
<body>";
include("common/link_new_file.php");
include_once("common/functions.php");
include("common/iOLinkCommonFunction.php");
 ?>

<?php

	$patient_id = $_REQUEST['pid'];
	$waiting_id = $_REQUEST['wid'];
	$insCaseId 	= $_REQUEST['insCaseIdList'];
	$type		= $_REQUEST['type'];
	$obj = new manageData;
	
	$relats = array('self','Father','Mother','Son','Daughter','Spouse','Guardian','POA','Employee','Other');	
	$arrayInsDataRecord = array();
	
//	print_r($row);
	$patientDetailQry = "SELECT pdt.* , DATE_FORMAT(date_of_birth,'%m-%d-%Y') AS dobShow FROM patient_data_tbl pdt WHERE  patient_id = '".$patient_id."' AND patient_id!='0'";
	$patientDetailRes = imw_query($patientDetailQry) or die(imw_error());
	$patientDetail = array();
	if(imw_num_rows($patientDetailRes)>0) {
		$patientDetail = imw_fetch_array($patientDetailRes);
	}
	
	
	if($_REQUEST['expPreviousPri']<>"") {
	
	$chkQry1 = "select * from insurance_data where  patient_id = '".$patient_id."' AND ins_caseid='".$insCaseId."' and type = '".$type."' and actInsComp='1' order by id";
	$chkRes1 = imw_query($chkQry1);
	$chkRow1 = @imw_fetch_array($chkRes1);
	$insID = $chkRow1['id']; 
	
	$active_date = $_POST["active_date"];
	$expiry_Date = $_POST["expiry_date"];
	$patient_dob = $_POST["dob"];
	
	if($active_date){
		$active_date_split = explode("-",$active_date);
		$active_date = $active_date_split[2]."-".$active_date_split[0]."-".$active_date_split[1];	
	}
	
	if($expiry_Date){
		$expiry_Date_split = explode("-",$expiry_Date);
		$expiry_Date = $expiry_Date_split[2]."-".$expiry_Date_split[0]."-".$expiry_Date_split[1]; 
	}
	
	if($patient_dob) {
		$patient_dob_split = explode("-",$patient_dob);
		$dob = $patient_dob_split[2]."-".$patient_dob_split[0]."-".$patient_dob_split[1];	
	}
	
	
	unset($arrayInsDataRecord);
	$arrayInsDataRecord['ins_provider'] = trim(addslashes($_POST["insprovider"]));
	$arrayInsDataRecord['ins_in_house_code'] = trim(addslashes($_POST["ins_in_house_code"]));
	$arrayInsDataRecord['policy'] = addslashes($_POST["policy"]);
	$arrayInsDataRecord['group_name'] = addslashes($_POST["group"]);
	$arrayInsDataRecord['plan_name'] = addslashes($_POST["plan_name"]);
	$arrayInsDataRecord['copay'] = addslashes($_POST["copay"]);
	$arrayInsDataRecord['refer_req'] = addslashes($_POST["refer_req"]);
	$arrayInsDataRecord['authorization_number'] = addslashes($_POST["authorization_number"]);
	$arrayInsDataRecord['active_date'] = $active_date;
	$arrayInsDataRecord['expiry_Date'] = $expiry_Date;
	$arrayInsDataRecord['fname'] = addslashes($_POST["fname"]);
	$arrayInsDataRecord['mname'] = addslashes($_POST["mname"]);
	$arrayInsDataRecord['lname'] = addslashes($_POST["lname"]);
	$arrayInsDataRecord['sub_relation'] = addslashes($_POST["sub_relation"]);
	$arrayInsDataRecord['ssn'] = addslashes($_POST["ssn"]);
	$arrayInsDataRecord['dob'] = $dob;
	$arrayInsDataRecord['gender'] = $_POST["gender"];
	$arrayInsDataRecord['payment_auth'] = addslashes($_POST["payment_auth"]);
	$arrayInsDataRecord['sign_on_file'] = addslashes($_POST["sign_on_file"]);
	
	$address1Post = $_POST["address1"];
	$address1Post=str_ireplace(', ',',',$address1Post);
	$address1Post=str_ireplace(',',', ',$address1Post);
	
	$address2Post = $_POST["address2"];
	$address2Post=str_ireplace(', ',',',$address2Post);
	$address2Post=str_ireplace(',',', ',$address2Post);
	
	$arrayInsDataRecord['address1'] = addslashes($address1Post);
	$arrayInsDataRecord['address2'] = addslashes($address2Post);
	$arrayInsDataRecord['zip_code'] = $_POST["zip_code"];
	$arrayInsDataRecord['city'] = addslashes($_POST["city"]);
	$arrayInsDataRecord['state'] = addslashes($_POST["state"]);
	$arrayInsDataRecord['home_phone'] = addslashes($_POST["home_phone"]);
	$arrayInsDataRecord['work_phone'] = addslashes($_POST["work_phone"]);
	$arrayInsDataRecord['mbl_phone'] = addslashes($_POST["mbl_phone"]);
	$arrayInsDataRecord['type'] = addslashes($_POST["type"]);
	$arrayInsDataRecord['ins_caseid'] = $insCaseId;
	
	$arrayInsDataRecord['waiting_id'] = $waiting_id;
	$arrayInsDataRecord['patient_id'] = $patient_id;
	
	$chkScnDocQry = "select scan_documents_id,scan_card,scan_card2 from iolink_insurance_scan_documents 
			where type = '".$type."' and ins_caseid = '".$insCaseId."'
			and patient_id = '".$patient_id."' and document_status = '0'";
	$chkScnDocRes = imw_query($chkScnDocQry) or die(imw_error());
	$chkScnDocNmRw = imw_num_rows($chkScnDocRes);
	$scan_cardScnDoc='';
	$scan_card2ScnDoc='';
	if($chkScnDocNmRw>0){
		$chkScnDocRw = imw_fetch_array($chkScnDocRes);
		$scanDocumentsIdScnDoc = $chkScnDocRw['scan_documents_id'];
		$scan_cardScnDoc = $chkScnDocRw['scan_card'];
		$scan_card2ScnDoc = $chkScnDocRw['scan_card2'];
	}
	if($scan_cardScnDoc) {
		$arrayInsDataRecord['scan_card'] = $scan_cardScnDoc;
	}
	if($scan_card2ScnDoc) {
		$arrayInsDataRecord['scan_card2'] = $scan_card2ScnDoc;
	}
	
	if(@imw_num_rows($chkRes1) <= 0){
		$arrayInsDataRecord['actInsComp'] = '1';
		$obj->addRecords($arrayInsDataRecord, 'insurance_data');
		
		$updtScnDocQry="UPDATE iolink_insurance_scan_documents 
						SET document_status='1' 
						WHERE type = '".$type."' AND ins_caseid = '".$insCaseId."' AND ins_caseid != '0'
						AND patient_id = '".$patient_id."' AND document_status = '0'";
		$updtScnDocRes = imw_query($updtScnDocQry) or die(imw_error());			
	}else{
		$obj->updateRecords($arrayInsDataRecord, 'insurance_data','id',$insID);
	}
	setReSyncroStatus($waiting_id,'insData');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
	if($waiting_id) {
		
		echo "<script>
				if(opener.waitingPatient_info) {
					opener.waitingPatient_info('$patient_id','$waiting_id');
				}
				if(opener.top.iframeHome) {	
					if(opener.top.iframeHome.iOLinkBookSheetFrameId) {	
						opener.top.iframeHome.iOLinkBookSheetFrameId.location.reload();
					}
				}
			  </script>";
		
	}
	?>
	<script>
		alert('Record Saved');
		window.close();
	</script>
	<?php
}
if($patient_id) {
	$getInsCaseQry 	= "SELECT * FROM iolink_insurance_case WHERE patient_id='".$patient_id."' AND patient_id!=''";
	$getInsCaseRes 	= imw_query($getInsCaseQry);
	$getInsCaseNumRow 	= imw_num_rows($getInsCaseRes);
	$getInsCaseNameArr 	= array();
	if($getInsCaseNumRow>0) {
		while($getInsCaseRow = @imw_fetch_array($getInsCaseRes)) {
			$getInsCaseid=$getInsCaseRow['ins_caseid'];
			if(!$insDataCaseId) {
				$insDataCaseId=$getInsCaseRow['ins_caseid'];
			}
			$getInsCaseNameArr[$getInsCaseid]=$getInsCaseRow['case_name'];
		}
	}else {
		$insrtQry ="INSERT INTO iolink_insurance_case SET  
					patient_id='".$patient_id."',
					ins_case_name='".$defaultCaseName."',
					case_name='".$defaultCaseName."',
					start_date=NOW(),
					case_status='Open',
					waiting_id='".$waiting_id."'
					";
		$insrtRes = imw_query($insrtQry);	
		if(!$insDataCaseId) {
			$insDataCaseId = imw_insert_id();
			$getInsCaseNameArr[$insDataCaseId]=$defaultCaseName;
		}		
	}	
}
if(!trim($insCaseId)) { $insCaseId = $insDataCaseId;}
if($insCaseId) {  
	$AndCaseIdQry = ' AND ins_caseid="'.$insCaseId.'" ';
	$updtInsDataCaseQry="UPDATE insurance_data SET ins_caseid='".$insCaseId."' WHERE  patient_id = '".$patient_id."' AND  type = '".$type."' AND actInsComp='1' ";
	$updtInsDataCaseRes = imw_query($updtInsDataCaseQry);	
}
//$chkQry = "select * from insurance_data where  waiting_id = '$waiting_id' and  type = '".$type."'";
$chkQry = "SELECT * from insurance_data WHERE  patient_id = '".$patient_id."' AND  type = '".$type."' AND actInsComp='1' $AndCaseIdQry order by id";
$chkRes = imw_query($chkQry);
$row = @imw_fetch_array($chkRes);
$insuranceId 	= $row['id'];
$insScan1Upload = $row['insScan1Upload'];
$insScan1Status = $row['insScan1Status'];
$insScan2Upload = $row['insScan2Upload'];
$insScan2Status = $row['insScan2Status'];
$insDataCaseId 	= $row['ins_caseid'];

$priScan_card 	= $row['scan_card'];
$priScan_card2 	= $row['scan_card2'];


//--- Resize Primary/Secondary/Tertiary Insurance Scanned first document Image --------
if($priScan_card){
	$priImgPath = realpath('imedic_uploaddir'.$priScan_card);
	if(file_exists($priImgPath) && is_dir($priImgPath) == '')
	{
		$priImageSize = getimagesize($priImgPath);
		$priImgPathInfo = pathinfo($priImgPath);
		$ext = $priImgPathInfo["extension"];
		if(trim(strtolower($ext))=='pdf') {
			$priImage = '<img style="cursor:pointer;" onClick="show_scanned(\''.$insuranceId.'\',1)" src="images/pdf_icon_medium.png" title="'.$type.' Uploaded Document">';
		}else if($priImageSize[0]>20){
			$newSize = $obj->imageResize($priImageSize[0],$priImageSize[1],25);
			$priImage = '<img style="cursor:pointer;" onClick="show_scanned(\''.$insuranceId.'\',1)" src="imedic_uploaddir'.$priScan_card.'" title="'.$type.' Scanned Document" '.$newSize.'>';
		}
		else{
			$priImage = '<img style="cursor:pointer;" onClick="show_scanned(\''.$insuranceId.'\',1)" src="imedic_uploaddir'.$priScan_card.'" title="'.$type.' Scanned Document">';
		}
	}
}
//--- Resize Primary/Secondary/Tertiary Insurance Scanned second document Image --------
if($priScan_card2){
	$priImgPath2 = realpath('imedic_uploaddir'.$priScan_card2);
	if(file_exists($priImgPath2) && is_dir($priImgPath2) == ''){		
		$priImageSize2 = getimagesize($priImgPath2);
		$priImgPath2Info = pathinfo($priImgPath2);
		$ext2 = $priImgPath2Info["extension"];
		if(trim(strtolower($ext2))=='pdf') {
			$priImage2 = '<img style="cursor:pointer;" onClick="show_scanned(\''.$insuranceId.'\',2)" src="images/pdf_icon_medium.png" title="'.$type.' Uploaded Document">';			
		}else if($priImageSize2[0]>20){
			$newSize2 = $obj->imageResize($priImageSize2[0],$priImageSize2[1],25);
			$priImage2 = '<img style="cursor:pointer;" onClick="show_scanned(\''.$insuranceId.'\',2)" src="imedic_uploaddir'.$priScan_card2.'" title="'.$type.' Scanned Document" '.$newSize2.'>';
		}
		else{
			$priImage2 = '<img style="cursor:pointer;" onClick="show_scanned(\''.$insuranceId.'\',2)" src="imedic_uploaddir'.$priScan_card2.'" title="'.$type.' Scanned Document">';
		}
	}
}

if($insuranceId == ''){
	//---- get Document from scan_table without Primary/Secondary/Tertiary insurance company -------
	$qry = "select scan_documents_id,scan_card,scan_card2 from iolink_insurance_scan_documents 
			where type = '".$type."' and ins_caseid = '$insDataCaseId'
			and patient_id = '$patient_id' and document_status = '0'";
	//$priInsDetails = ManageData::getQryRes($qry);
	$priInsRes = imw_query($qry) or die(imw_error());
	$priInsNumRw = imw_num_rows($priInsRes);
	if($priInsNumRw>0){
		$priInsDetails = imw_fetch_array($priInsRes);
		$priScan_card = $priInsDetails['scan_card'];
		$scan_documents_id = $priInsDetails['scan_documents_id'];
		$pri_scan_documents_id1 = $priInsDetails['scan_documents_id'];
		$priImgPath = realpath('imedic_uploaddir'.$priScan_card);	
		if(file_exists($priImgPath) && is_dir($priImgPath) == ''){		
			$priImageSize = getimagesize($priImgPath);
			$priImgPathInfo = pathinfo($priImgPath);
			$ext = $priImgPathInfo["extension"];
			if(trim(strtolower($ext))=='pdf') {
				$priImage = '<img style="cursor:pointer;" onClick="show_scanned(\''.$insuranceId.'\',1)" src="images/pdf_icon_medium.png" title="'.$type.' Uploaded Document">';
			}else if($priImageSize[0]>20){
				$newSize = $obj->imageResize($priImageSize[0],$priImageSize[1],25);
				$priImage = '<img style="cursor:pointer;" onClick="show_scanned(\''.$scan_documents_id.'\',1,\'scan_card\')" src="imedic_uploaddir'.$priScan_card.'" title="'.$type.' Scanned Document" '.$newSize.'>';
			}
			else{
				$priImage = '<img style="cursor:pointer;" onClick="show_scanned(\''.$scan_documents_id.'\',1,\'scan_card\')" src="imedic_uploaddir'.$priScan_card.'" title="'.$type.' Scanned Document">';
			}
		}
		$priScan_card2 = $priInsDetails['scan_card2'];
		$priImgPath2 = realpath('imedic_uploaddir'.$priScan_card2);
		if(file_exists($priImgPath2) && is_dir($priImgPath2) == ''){		
			$priImageSize2 = getimagesize($priImgPath2);
			$priImgPath2Info = pathinfo($priImgPath2);
			$ext2 = $priImgPath2Info["extension"];
			if(trim(strtolower($ext2))=='pdf') {
				$priImage2 = '<img style="cursor:pointer;" onClick="show_scanned(\''.$insuranceId.'\',2)" src="images/pdf_icon_medium.png" title="'.$type.' Uploaded Document">';			
			}else if($priImageSize2[0]>20){
				$newSize2 = $obj->imageResize($priImageSize2[0],$priImageSize2[1],25);
				$priImage2 = '<img style="cursor:pointer;" onClick="show_scanned(\''.$scan_documents_id.'\',2,\'scan_card\')" src="imedic_uploaddir'.$priScan_card2.'" title="'.$type.' Scanned Document" '.$newSize2.'>';
			}
			else{
				$priImage2 = '<img style="cursor:pointer;" onClick="show_scanned(\''.$scan_documents_id.'\',2,\'scan_card\')" src="imedic_uploaddir'.$priScan_card2.'" title="'.$type.' Scanned Document">';
			}
		}
		
	}
}	

//START CODE TO GET INSURANCE-COMPANY NAME FROM iASC
include('connect_imwemr.php'); //imwemr connection
$iAscInscompNmeArr= $iAscInscompNmeValArr = array();
$iAscInscompNme = '';
$getCompQry 	= "SELECT id,name,in_house_code FROM insurance_companies WHERE ins_del_status='0' ORDER BY name,in_house_code";
$getCompRes 	= imw_query($getCompQry) or die(imw_error());	
$getCompNumRow 	= imw_num_rows($getCompRes);
if($getCompNumRow>0) { 
	while($getCompRow = imw_fetch_array($getCompRes)) {
		//$inscomp 					= str_ireplace("'","",$getCompRow['name']);
		//$in_house_code 			= str_ireplace("'","",$getCompRow['in_house_code']);
		$inscomp 					= addslashes($getCompRow['name']);
		$in_house_code 				= addslashes($getCompRow['in_house_code']);
		if($inscomp) {
			$iAscInscompNmeArr[]	= "'".$in_house_code." - ".$inscomp."'";
			$iAscInscompNmeValArr[]	= "'".$inscomp."'";
			$iAscInscompInHouseCodeArr[] = "'".$in_house_code."'";
		}
	}
}
imw_close($link_imwemr); //CLOSE imwemr connection

if(count($iAscInscompNmeArr)>0){
	$iAscInscompNme				=	implode(',',$iAscInscompNmeArr);
	$iAscInscompNmeVal			=	implode(',',$iAscInscompNmeValArr);
	$iAscInscompInHouseCode		=	implode(',',$iAscInscompInHouseCodeArr);
}

include("common/conDb.php");				
//END CODE TO GET INSURANCE-COMPANY NAME FROM iASC

?>

<script type="text/javascript">
	window.focus();
	
	var iAscInscompNme = "";
	var iAscInscompInHouseCode = "";
	<?php
		if($iAscInscompNme!=""){
		?>		
		iAscInscompNme = new Array(<?php echo fnLineBrk($iAscInscompNme); ?>);
		iAscInscompInHouseCode = new Array(<?php echo fnLineBrk($iAscInscompInHouseCode); ?>);
		<?php
		}	
	?>	
	
	var today = new Date();
	var day = today.getDate();
	var month = today.getMonth()
	var year = y2k(today.getYear());
	var mon=month+1;
	if(mon<=9){
		mon='0'+mon;
	}
	var todaydate=mon+'-'+day+'-'+year;
	function y2k(number){
		return (number < 1000)? number+1900 : number;
	}
	function newWindow(q){
		
		mywindow=open('mycal1.php?md='+q+'&rf=yes','','width=200,height=250,top=200,left=300');
		if(mywindow.opener == null)
			mywindow.opener = self;
	}
	function restart(q){
		fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
		if(q==8){
			if(fillDate > todaydate){
				//alert("Date Of Service can not be a future date")
				//return false;
			}
		}
		
		
		document.getElementById(q).value=fillDate;
		mywindow.close();
	}
function padout(number){
return (number < 10) ? '0' + number : number;
}


function doDateCheck(from, to) {
	if(chkdate(to) && chkdate(from) ){
	
	if (Date.parse(from.value) >= Date.parse(to.value)) {
	//alert("The dates are valid.");
	}
	else {
		if (from.value == "" || to.value == ""){ 
		//alert("Both dates must be entered.");
		}
		else{ 
		to.value="";
		alert("Date of birth can not be greater than current date.");
		   }
		}
	}
}

//START FUNCTION TO CHECK AND SUBMIT INSURANCE FORM
function frmSubmit(){
	var insType = '<?php echo ucfirst($type);?>';
	var msg = 'Please enter the following \n';
	var alertActive = false;
	if(document.getElementById('policy').value == ""){
		msg = msg + ' \xb7 '+insType+' Insurance Policy.\n'; 
		alertActive = true;
	}
	if(document.getElementById('active_date').value == ""){
		msg = msg + ' \xb7 Act. Date.\n'; 
		alertActive = true;
	}
	if(document.getElementById('i1subscriber_fname').value == ""){
		msg = msg + ' \xb7 '+insType+' Insurance First Name.\n'; 
		alertActive = true;
	}
	if(document.getElementById('lastName').value == ""){
		msg = msg + ' \xb7 '+insType+' Insurance Last Name.\n'; 
		alertActive = true;
	}
	if(document.getElementById('sub_relation').value == ""){
		msg = msg + ' \xb7 '+insType+' Insurance Sub Relation.\n'; 
		alertActive = true;
	}
	/*
	if(document.getElementById('address1').value == ""){
		msg = msg + ' \xb7 '+insType+' Insurance Address 1.\n'; 
		alertActive = true;
	}
	if(document.getElementById('zip').value == ""){
		msg = msg + ' \xb7 '+insType+' Insurance Zip.\n'; 
		alertActive = true;
	}
	if(document.getElementById('city').value == ""){
		msg = msg + ' \xb7 '+insType+' Insurance City.\n'; 
		alertActive = true;
	}
	if(document.getElementById('state').value == ""){
		msg = msg + ' \xb7 '+insType+' Insurance State.\n'; 
		alertActive = true;
	}
	*/
	if(document.getElementById('insprovider').value == ""){
		var providerMsg = insType+' insurance carrier is required.\n'; 
		alert(providerMsg);
		document.getElementById('insprovider').focus();
	}else if(alertActive == true){
		msg = msg + 'Do you want to continue.\n'; 
		if(confirm(msg)) {
			document.primary_ins.submit();
		}
	}
	else{
		document.primary_ins.submit();
	}		
}
//END FUNCTION TO CHECK AND SUBMIT INSURANCE FORM
	
//START FUNCTION TO SCAN/UPLOAD DISCHARGE SUMMARY SHEET
function startCallback() {	
	return true;
}
function completeCallback(response){
	setTimeout('getImage()', 1000);
}
function getImage(){
	document.frames['iframeIOL'].location.reload();
	var objFrm = document.frm_uploadInsPriImage;
	if(objFrm.hidd_delImage.value=='yes' || objFrm.hidd_delImage.value=='yes2') {
		document.frm_uploadInsPriImage.hidd_delImage.value='';
	}else {
		document.getElementById('iframeIOL').style.height = '100px';
		document.getElementById('iframeIOL').style.width = '400px';
		document.frm_uploadInsPriImage.hidd_delImage.value='';
	}	
}
function showImgDiv(){
	document.getElementById('imgDiv').style.display = 'block';
}

//END FUNCTION TO SCAN/UIPLOAD DISCHARGE SUMMARY SHEET

function changeCaseFun() {
	document.getElementById('expPreviousPri').value='';
	document.primary_ins.submit();
}	

function scan_card(piid,isRecordExist,patient_id,waiting_id,insDataCaseId)
{
	window.open("scan_card.php?type="+piid+"&isRecordExists="+isRecordExist+"&patient_id="+patient_id+"&waiting_id="+waiting_id+"&currentCaseid="+insDataCaseId,'lic','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=650,height=700,left=150,top=60');	
}
function show_scanned(id,val,type){
	window.open('show_scan_img.php?id='+id+'&val='+val+'&type='+type,'scan','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1');
}


function getvalue(obj,field){
	var lname1 = "";
	var fname1 = "";		
	
	var patientFname 		= "<?php print ucfirst($patientDetail['patient_fname']); ?>";
	var patientMname 		= "<?php print ucfirst($patientDetail['patient_mname']); ?>";
	var patientLname 		= "<?php print ucfirst($patientDetail['patient_lname']); ?>";
	var patientDOB 			= "<?php print $patientDetail['dobShow']; ?>";
	var patientSex 			= "<?php print $patientDetail['sex']; ?>";
	var patientStreet 		= "<?php print ucfirst($patientDetail['street1']); ?>";
	var patientStreet_2 	= "<?php print ucfirst($patientDetail['street2']); ?>";
	var patientPostal_code 	= "<?php print $patientDetail['zip']; ?>";
	var patientCity 		= "<?php print ucfirst($patientDetail['city']); ?>";
	var patientState 		= "<?php print ucfirst($patientDetail['state']); ?>";
	var patientPhone_home 	= "<?php print core_phone_format($patientDetail['homePhone']); ?>";
	var patientBiz_home 	= "<?php print core_phone_format($patientDetail['workPhone']); ?>";
	
	document.getElementById("dob").value 				= '';
	document.getElementById("i1subscriber_sex").value 	= '';
	document.getElementById("address1").value 			= '';
	document.getElementById("address2").value 			= '';
	document.getElementById("zip").value 				= '';
	document.getElementById("city").value 				= '';
	document.getElementById("state").value 				= '';
	document.getElementById("home_phone").value 		= '';
	document.getElementById("work_phone").value 		= '';
	
	if(obj.value == "self"){
		document.getElementById("i1subscriber_fname").value = patientFname;
		document.getElementById("mname").value 				= patientMname;
		document.getElementById("lastName").value 			= patientLname;
		document.getElementById("dob").value 				= patientDOB;
		document.getElementById("i1subscriber_sex").value 	= patientSex;
		document.getElementById("address1").value 			= patientStreet;
		document.getElementById("address2").value 			= patientStreet_2;
		document.getElementById("zip").value 				= patientPostal_code;
		document.getElementById("city").value 				= patientCity;
		document.getElementById("state").value 				= patientState;
		document.getElementById("home_phone").value 		= patientPhone_home;
		document.getElementById("work_phone").value 		= patientBiz_home;
	}
	else if(obj.value != ""){
		lname1 = document.getElementById("lastName").value;
		if(obj.value != "Spouse"){	
			fname1 = document.getElementById("i1subscriber_fname").value;
		}
		document.getElementById("i1subscriber_fname").value = '';
		document.getElementById("mname").value = '';
		document.getElementById("lastName").value = '';
		if(fname1 || lname1){
			//var height = parseInt(parent.document.body.clientHeight) - 50;				
			var height=575;
			window.open("insurance_search_patient.php?lastName="+lname1+"&firstName="+fname1,"PatientWindow","width=720,height="+height+",top=90,left=10,scrollbars=yes");
		}
		else{
			alert('Please enter First name or Last name to perform search');
		}
	}
	changeTxtGroupColor(1,'i1subscriber_fname');
	changeTxtGroupColor(1,'lastName');
	changeTxtGroupColor(1,'address1');
	changeTxtGroupColor(1,'zip');
	changeTxtGroupColor(1,'city');
	changeTxtGroupColor(1,'state');
	
}

function popUpRelationValue(pid,title,iterfname,itermname,iterlname,itersuffix,status,dob_format,sex,street,zip,city,state,ss,phone_home,phone_biz,phone_cell){
		document.getElementById("i1subscriber_fname").value=iterfname;
		document.getElementById("mname").value = itermname;
		document.getElementById("lastName").value=iterlname;	
		document.getElementById("dob").value = dob_format;
		document.getElementById("i1subscriber_sex").value = sex;		
		document.getElementById("address1").value = street;
		document.getElementById("zip").value = zip;
		document.getElementById("city").value = city;
		document.getElementById("state").value = state;
		document.getElementById("home_phone").value = phone_home;
		document.getElementById("work_phone").value = phone_biz;
		
		changeTxtGroupColor(1,'i1subscriber_fname');
		changeTxtGroupColor(1,'lastName');
		changeTxtGroupColor(1,'address1');
		changeTxtGroupColor(1,'zip');
		changeTxtGroupColor(1,'city');
		changeTxtGroupColor(1,'state');
		
		window.focus();
}
</script>
<form action="insurance_primary.php" method="post" name="primary_ins">
    <input type="hidden" name="waiting_id" id="waiting_id" value="<?php echo $waiting_id; ?>">
    <input type="hidden" name="wid" id="wid" value="<?php echo $waiting_id; ?>">
    <input type="hidden" name="pid" id="pid" value="<?php echo $patient_id; ?>">
    <input type="hidden" name="expPreviousPri" id="expPreviousPri" value="test" />
    <input type="hidden" name="actPreviousPri" id="actPreviousPri" value="<?php print $actPreviousPriDate; ?>" />
    <input type="hidden" name="type" id="type" value="<?php echo $type;?>" />

    <table class="text_10 alignLeft" style="width:100%; padding:2px; border:none; background-color:#ECF1EA;">
        <tr>
            <td>
                <table class="text_10 table_collapse alignCenter" style="border:none;" >
                    <tr>
                        <td class="text_10b" style="width:67px;">
                            <table class="alignCenter" style="border:none; padding:4px; width:100%;">
                                <tr>
                                    <td colspan="3"  class="text_10b"  style="padding-left:5px; background-image:url(<?php echo $bgHeadingImage;?>);" >
                                        <table class="alignCenter" style="border:none; padding:4px; width:100%;">
                                            <tr>
                
                                                <td  class="text_10b"  style="padding-left:5px;" >
                                                    <?php echo ucfirst($type);?> Ins. Case <?php if($getInsCaseNameArr) {echo $getInsCaseNameArr[$insDataCaseId].'-'.$insDataCaseId;}?>
                                                </td>
                                                <td  class="text_10b"  style="padding-left:5px;" ><?php print $priImage.'&nbsp;'.$priImage2; ?></td>
                                                <td  class="text_10b"  style="padding-left:5px;" >
                                                    Choose Case
                                                    <select class="field text1" style="width:100px; height:22px;" name="insCaseIdList" id="insCaseIdList" onChange="javascript:changeCaseFun();" >
                                                        <?php
                                                        if($getInsCaseNameArr) {
                                                            foreach($getInsCaseNameArr as $insCaseIdKey => $getInsCaseName) {
                                                                $caseSel='';//insDataCaseId
                                                                if($insCaseId==$insCaseIdKey) { $caseSel='selected'; }
                                                        ?>		<option value="<?php echo $insCaseIdKey;?>" <?php echo $caseSel;?>><?php echo $getInsCaseName;?></option>
                                                        <?php		
                                                            }
                                                        }
                                                        ?>
                                                    </select> 
                                                </td>
                                                <td  class="text_10b"  style="padding-left:5px;" >
                                                    <img style="cursor:pointer;border:none; " src="images/scanicon.png"  id="scanIcon" alt="Scan Insurance Card" onClick="scan_card('<?php echo $type;?>','<?php echo $insuranceId;?>','<?php echo $patient_id;?>','<?php echo $waiting_id;?>','<?php echo $insDataCaseId;?>');"/>
                                                </td>
                                            </tr>
                                        </table>		
                                    </td>
                                    
                                    
                                            
                                </tr>
                                <tr>
                                    <td class="valignTop" style="width:267px;">
                                        <table id="provider1_table" class="tblBg" style="width:235px; height:125px; padding:1px; border:none;">
                                            <tr>
                                                <td class="text_10b alignLeft" >
                                                    Provider :
                                                </td>
                                            </tr>
                                            <tr class="bgcolor">
                                                <td class="alignLeft nowrap" style="padding-left:10px;">
                                                    <table class="table_pad_bdr" style="border:none; width:35%; padding:0px;">
                                                        <tr>
                                                            <td class="alignLeft" style="width:105px;">
                                                                    <input type="text" name="insprovider" id="insprovider" class="field text1" onFocus="changeTxtGroupColor(1,'insprovider');" onKeyUp="changeTxtGroupColor(1,'insprovider');" style="width:120px; height:17px; <?php if(trim(!$row['ins_provider'])){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;" value="<?php echo $row['ins_provider']; ?>" /><div class="label1">Ins. Carrier</div>
                                                                    <input type="hidden" name="ins_in_house_code" id="ins_in_house_code" class="field text1"  value="<?php echo $row['ins_in_house_code']; ?>" />
                                                                    
                                                            </td>
                                                            <td class="alignLeft" style="padding-left:8px;">
                                                                <input type="text" name="policy" id="policy" class="field text1" onFocus="changeTxtGroupColor(1,'policy');" onKeyUp="changeTxtGroupColor(1,'policy');" style=" width:120px; height:17px; <?php if(trim(!$row['policy'])){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;" value="<?php echo $row['policy']; ?>" ><div class="label1">Policy</div>
                                                            </td>
                                                    	</tr>
                                                    
                                                        <tr class="bgcolor">
                                                            <td><input type="text" name="group" id="group" value="<?php echo $row['group_name']; ?>" class="field text1" style="width:120px; height:17px;"><div class="label1">Group#</div>
                                                            </td>
                                                            <td class="alignLeft" style="padding-left:8px;">
                                                                <input type="text" name="plan_name" id="plan_name" value="<?php echo $row['plan_name']; ?>" class="field text1" style="width:120px; height:17px;"><div class="label1">Plan Name</div>
                                                            </td>
                                                        </tr>
                                                        
                                                        <tr class="bgcolor">	
                                                            <td >
                                                                <input type="text" name="copay" id="copay" value="<?php echo $row['copay']; ?>" class="field text1" style="width:120px; height:17px;"><div class="label1">CoPay</div>
                                                            </td>
                                                            <td style="padding-left:8px;">
                                                                <select  class="field text1" style="width:120px; height:22px;" name="refer_req" >
                                                                    <option value="No" <?php if($row['refer_req'] == 'No'){ echo 'selected=selected'; } ?>>No</option>
                                                                    <option value="Yes" <?php if($row['refer_req'] == 'Yes'){ echo 'selected=selected'; } ?>>Yes</option>
                                                                </select><div class="label1">Refer&nbsp;Req</div>
                                                            </td>
                                                        </tr>
                                                        
                                                        <tr class="bgcolor">
                                                            <td>
                                                                <input type="text" name="authorization_number" id="authorization_number" value="<?php echo $row['authorization_number']; ?>" class="field text1" style="width:120px; height:17px;"><div class="label1">Authorization#</div>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        
                                                    </table>
                                                </td>
                                          </tr>
                                          <tr class="bgcolor">
                                                <td class="tr_bg alignLeft" style="padding-left:11px;" >
                                                    <table  class="table_collapse">
                                                        <tr>
                                                            <td class="valignTop">
                                                            <?php  if($row['active_date'] != '' &&  $row['active_date'] != '0000-00-00') { 
                                                                        $actDate = date('m-d-Y',strtotime($row['active_date']));
                                                                        //$hiddActDate = date('m-d-Y',strtotime($row['active_date']));
                                                                    }
                                                                    
                                                                    if($row['expiry_Date'] != '' &&  $row['expiry_Date'] != '0000-00-00') { 
                                                                        $expDate = date('m-d-Y',strtotime($row['expiry_Date']));
                                                                        //$hiddExpDate = date('m-d-Y',strtotime($row['expiry_Date']));
                                                                    }
                                                            ?>
                                                            
                                                                <input type="hidden" name="hidd_active_date" id="hidd_active_date" value="<?php echo $hiddActDate; ?>">
                                                                <input type="hidden" name="exitingExpirationDate" id="exitingExpirationDate"  value="" />
                                                                <input name="active_date" id="active_date" type="text" onBlur="checkdate(this);"  class="field text1" onFocus="changeTxtGroupColor(1,'active_date');" onKeyUp="changeTxtGroupColor(1,'active_date');if(event.keyCode=='13') {this.blur(); }" style=" <?php if(trim(!$actDate)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>; width:90px; height:17px;" value="<?php echo $actDate; ?>" maxlength="10"  title='mm-dd-yyyy' >
                                                                <img src="images/icon_cal.jpg" style="cursor:pointer; width:20px; height:17px; border:none;" alt="Act. Date" onClick="newWindow('active_date')"><div class="label1">Act. Date</div>
                                                            </td>
                                                            <td style="padding-left:8px;">
                                                                <input type="hidden" name="hidd_expiry_date" id="hidd_expiry_date" value="<?php echo $hiddExpDate; ?>">
                                                                <input name="priExpirationDate" id="priExpirationDate" value="<?php print $expirationDate; ?>" type="hidden" >
                                                                <input name="expiry_date" id="expiry_date" onBlur="checkdate(this);" value="<?php echo $expDate; ?>" onKeyUp="if(event.keyCode=='13') {this.blur(); }" type="text" class="field text1" title="mm-dd-yyyy" style="width:90px; height:17px;" >
                                                                <img src='images/icon_cal.jpg' style="cursor:pointer; width:20px; height:17px; border:none;" alt="Exp. Date" onClick="newWindow('expiry_date')"><div class="label1">Exp. Date</div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                      </table>
                                    </td>
                                    
                                    <td class="valignTop" style="width:267px; ">
                                        <table id="insPolicy1_table" class="tblBg table_pad_bdr" style="border:none; width:210px; height:125px;">
                                            <tr>
                                                <td colspan="3" class="text_10b alignLeft">
                                                    Ins. Policy
                                                </td>
                                            </tr>
                                            
                                            <tr class="bgcolor alignLeft">
                                                <td style="padding-left:10px;">
                                                    <input type="text"  name="fname" id="i1subscriber_fname" class="field text1" onFocus="changeTxtGroupColor(1,'i1subscriber_fname');" onKeyUp="changeTxtGroupColor(1,'i1subscriber_fname');" style=" width:100px; height:17px; <?php if(trim(!$row['fname'])){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;" value="<?php echo $row['fname']; ?>" ><div class="label1">First Name</div>
                                              </td>
                                                <?php if(trim($patientMname)=="" and $InsPriInsName==1){$class="mandatory";} else{$class="text_10";} ?>
                                                <td >
                                                    <input type="text" class="field text1" name="mname" id="mname" value="<?php echo $row['mname']; ?>" style="width:40px; height:17px;" ><div class="label1">Middle</div>
                                              </td>
                                                <?php if(trim($patientLname)=="" and $InsPriInsName==1){$class="mandatory";} else{$class="text_10";} ?>
                                                <td>
                                                    <input type="text" name="lname" id="lastName" class="field text1" onFocus="changeTxtGroupColor(1,'lastName');" onKeyUp="changeTxtGroupColor(1,'lastName');" style="width:120px; height:17px; <?php if(trim(!$row['lname'])){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;" value="<?php echo $row['lname']; ?>"  /><div class="label1">Last Name</div>
                                              </td>
                                            </tr>
                                            
                                            <tr class="bgcolor alignLeft">
                                                <td colspan="2"  class="text_10" style="padding-left:10px;">
                                                    <select name='sub_relation' id='sub_relation' class="field text1" onChange="changeTxtGroupColor(1,'sub_relation');getvalue(this,'');" style="width:140px; height:22px; <?php if(trim(!$row['sub_relation'])){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;">
                                                        <option value="">--Select--</option>
                                                        <?php
                                                            foreach($relats as $val){
                                                                 if($val == $row['sub_relation']){
                                                                    $sel = 'selected="selected"';
                                                                } 
                                                                else{
                                                                    $sel = '';
                                                                }
                                                                $selectOption .= '
                                                                    <option value="'.$val.'" '.$sel.'>'.ucfirst($val).'</option>
                                                                ';
                                                            }
                                                            print $selectOption;
                                                        ?>
                                                    </select><div class="label1">Sub.Relation</div>
                                                </td>
                                                <td colspan="1">
                                                    <input type="text" name="ssn"  id="ssn" value="<?php echo $row['ssn']; ?>" class="field text1" style="width:120px; height:17px;" ><div class="label1">S.S</div>
                                                </td>
                                            </tr>
                                            
                                            
                                            <tr class="bgcolor alignLeft" >
                                                <td colspan="2" style="padding-left:10px;">
                                                <?php
                                                    if($row['dob'] != '' &&  $row['dob'] != '0000-00-00') { 
                                                            $dob = date('m-d-Y',strtotime($row['dob']));
                                                            //$hiddDob = date('m-d-Y',strtotime($row['dob']));
                                                            
                                                        }
                                                ?>
                                                    <input type="hidden" name="from_date_subscriber1" id="from_date_subscriber1" value="<?php echo(date("m-d-Y"));?>" >
                                                    <input type="hidden" name="hidd_dob" id="hidd_dob" value="<?php echo $hiddDob; ?>">
                                                    <input type="text" id="dob" onBlur="checkdate(this);" onKeyUp="if(event.keyCode=='13') {this.blur(); }" maxlength="10" name='dob' value='<?php echo $dob; ?>' title='mm-dd-yyyy' class="field text1" style="width:75px; height:17px;" />
                                                    <img src='images/icon_cal.jpg' width="20" height="17" style=" cursor:pointer;border:none;" alt="DOB" onClick="newWindow('dob')"><div class="label1">DOB</div>
                                                </td>
                                                <td >
                                                    <select  name="gender" id="i1subscriber_sex" class="field text1" style="width:120px; height:22px;">				
                                                        <option value="m" <?php if($row['gender'] == 'm') print 'selected="selected"'; ?> >Male</option>
                                                        <option value="f" <?php if($row['gender'] == 'f') print 'selected="selected"'; ?> >Female</option>
                                                    </select><div class="label1">Gender</div>																					
                                                </td>
                                            </tr>
                                            
                                            <tr class="bgcolor">
                                                <td colspan="2" class="text_10 alignLeft" style="padding-left:10px;">
                                                    <input  name="payment_auth" id="payment_auth" value="1" type="checkbox" <?php if($row['payment_auth'] == '1') print 'checked="checked"'; ?>><div class="label1">Pymt. Auth</div>	
                                                </td>
                                                <td colspan="1" class="text_10 alignLeft" >
                                                    <input  name="sign_on_file" id="sign_on_file" value="1" type="checkbox" <?php if($row['sign_on_file'] == '1') print 'checked="checked"'; ?>><div class="label1">Sign. on File</div>	
                                                </td>
                                            </tr>
                                            
                                                    
                                        </table>
                                    </td>
                                    <td class="valignTop" style="width:348px;">
                                        <table id="Contacts1_table" class="tblBg" style="border:none; width:220px; height:125px; padding:1px;">
                                            <tr>
                                                <td colspan="3" class="text_10b alignLeft">Contacts
                                                </td>
                                            </tr>
                                            <tr class="bgcolor alignLeft">
                                                <td style="padding-left:10px;" colspan="3">
                                                    <input type="text" name="address1"  id="address1" class="field text1" style=" width:278px; height:17px;" value="<?php echo $row['address1']; ?>"  /><div class="label1">Address 1</div>
                                                </td>
                                            </tr>
                                            
                                            <tr class="bgcolor alignLeft">
                                                <td style="padding-left:10px;" colspan="3">
                                                    <?php 
                                                        $class="text_10";
                                                     ?>
                                                    <input type="text" class="field text1" name="address2" id="address2" value="<?php echo $row['address2']; ?>" style=" width:278px; height:17px;" /><div class="label1">Address 2</div>
                                                </td>
                                            </tr>
                                            
                                            <tr class="bgcolor alignLeft">
                                                <td class="text_10 alignLeft" style="padding-left:10px;">
                                                    <input type="text" id="zip" name="zip_code" class="field text1" style="width:65px; height:17px;" value="<?php if($row['zip_code']) {echo $row['zip_code'];} ?>"  onBlur="return getCityStateFn(this,document.getElementById('city'),document.getElementById('state'));"  /><div class="label1">Zip</div>
                                                </td>
                                                <td class="text_10 alignLeft" >
                                                    <input type="text" name="city" id="city" class="field text1" style="  width:171px; height:17px;" value="<?php echo $row['city']; ?>"/><div class="label1">City</div>
                                                </td>
                                                <td class="text_10 alignLeft">
                                                    <input type="text" name="state" id="state" class="field text1" style="width:25px; height:17px;" value="<?php echo $row['state']; ?>" /><div class="label1">State</div>
                                                </td>
                                            </tr>		
                                            
                                            <tr class="bgcolor alignLeft">
                                                <td colspan="3" >
                                                	<table class="table_collapse" style="border:none; width:270px;">
                                                    	<tr>
                                                            <td style="padding-left:10px;" >
                                                                <input type="text" maxlength="12" name="home_phone" id="home_phone" class="field text1" style="width:90px; height:17px;" value="<?php echo $row['home_phone']; ?>" onBlur="ValidatePhone(this);" /><div class="label1">Home Tel</div>
                                                            </td>
                                                            <td >
                                                                <?php 
                                                                $class="text_10";
                                                                 ?>
                                                                <input type="text" maxlength="12" name="work_phone" id="work_phone" class="field text1" style="width:90px; height:17px;" value="<?php echo $row['work_phone']; ?>" onBlur="ValidatePhone(this);" /><div class="label1">Work Tel</div>
                                                            </td>
                                                            <td >
                                                                <?php 
                                                                $class="text_10";
                                                                ?>
                                                                <input type="text" maxlength="12" name="mbl_phone" id="mbl_phone" class="field text1" style="width:90px; height:17px;" value="<?php echo $row['mbl_phone']; ?>" onBlur="ValidatePhone(this);" /><div class="label1">Mobile</div>
                                                            </td>                                                        </tr>
                                                        
                                                    </table>
                                                </td>

                                            </tr>
                                            
                                    </table>
                                </td>
                            </tr>
                        </table>
                        </td>
                    </tr>
                    <tr style="height:10px;"><td>&nbsp;</td></tr>
                    <tr>
                        <td class="text_10b alignCenter" style="height:8px;">
                            <div id="saveBtnDivId" style="position:absolute; left:350px; top:270px;"><!-- 390px -->
                                <a href="#" onClick="frmSubmit();" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveBtn','','images/save_hover1.jpg',1)"><img id="saveBtn" src="images/save.jpg" style="border:none;" alt="Save" /></a>
                            </div>
                        </td>
                    </tr>
            </table>
        </td>
    </tr>
    </table>
</form>


<script>
	function changeImgSize(){
			var target = 100;
			var imgHeight = top.iframeIOL.document.getElementById('imgInsuranceThumbNail2').height;
			var imgWidth = top.iframeIOL.document.getElementById('imgInsuranceThumbNail2').width;
			
			if((imgHeight>=200) || (imgWidth>=200)){
				if(imgWidth > imgHeight){ 
					percentage = (target/imgWidth); 
				}else{ 
					percentage = (target/imgHeight);
				} 
				widthNew = imgWidth*percentage; 
				heightNew = imgHeight*percentage; 	
				top.iframeIOL.document.getElementById('imgInsuranceThumbNail2').height = heightNew;
				top.iframeIOL.document.getElementById('imgInsuranceThumbNail2').width = widthNew;
			}
		}
		
		//START THIS FUNCTION FOR SECOND IMAGE
		function changeImgSize2(){
			var target2 = 100;
			var imgHeight2 = document.getElementById('imgInsuranceThumbNail2').height;
			var imgWidth2 = document.getElementById('imgInsuranceThumbNail2').width;
			if((imgHeight2>=200) || (imgWidth2>=200)){
				if(imgWidth2 > imgHeight2){ 
					percentage2 = (target2/imgWidth2); 
				}else{ 
					percentage2 = (target2/imgHeight2);
				} 
				widthNew2 = imgWidth2*percentage2; 
				heightNew2 = imgHeight2*percentage2; 	
				document.getElementById('imgInsuranceThumbNail2').height = heightNew2;
				document.getElementById('imgInsuranceThumbNail2').width = widthNew2;	
			}
			
			
		}
		//END THIS FUNCTION FOR SECOND IMAGE
		
	// IMAGE THUMBNAIL
	if(document.getElementById('imgInsuranceThumbNail2')){
		setTimeout('changeImgSize()', 100);
	}
	if(document.getElementById('imgInsuranceThumbNail22')){
		setTimeout('changeImgSize2()', 100);
	}
	
	new actb(document.getElementById('insprovider'),iAscInscompNme,'',true,document.getElementById('ins_in_house_code'),iAscInscompInHouseCode);	

</script>
</body>
</html>