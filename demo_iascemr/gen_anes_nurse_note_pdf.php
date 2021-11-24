<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include("common_functions.php");
include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
extract($_GET);
//include_once("header_print.php");
include_once("new_header_print.php");
$pConfId=$_SESSION['pConfId'];
$get_http_path=$_REQUEST['get_http_path'];

//GET CURRENTLY LOGGED IN NURSE ID 
	$currentLoggedinNurseQry = "select * from `patientconfirmation` where  patientConfirmationId = '".$_REQUEST["pConfId"]."'";
	$currentLoggedinNurseRes = imw_query($currentLoggedinNurseQry) or die(imw_error()); 
	$currentLoggedinNurseRow = imw_fetch_array($currentLoggedinNurseRes); 
	$currentLoggedinNurseId = $currentLoggedinNurseRow["nurseId"];
	
	//GET CURRENTLY LOGGED IN NURSE NAME
		$ViewNurseNameQry = "select * from `users` where  usersId = '".$currentLoggedinNurseId."'";
		$ViewNurseNameRes = imw_query($ViewNurseNameQry) or die(imw_error()); 
		$ViewNurseNameRow = imw_fetch_array($ViewNurseNameRes); 
		$currentLoggedinNurseName = $ViewNurseNameRow["lname"].", ".$ViewNurseNameRow["fname"]." ".$ViewNurseNameRow["mname"];
		$currentLoggedinNurseSignature = $ViewNurseNameRow["signature"];
	//END GET CURRENTLY LOGGED IN NURSE NAME

//END GET CURRENTLY LOGGED IN NURSE ID

//FUNCTION TO GET TIME FROM HIDDEN VALUE
	function get_timeValue($hidd_timeValue) {
		if($hidd_timeValue<>'00:00:00' && $hidd_timeValue<>'') {
			$time_split = explode(":",$hidd_timeValue);
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
			$mainTime = $time_split[0].":".$time_split[1]." ".$am_pm;
		
			return $mainTime;
		}	
	}
//END FUNCTION TO GET TIME FROM HIDDEN VALUE

//FUNCTION TO SAVE TIME IN DATABASE
	function SaveTime($txt_NurseTime) {
	   if($txt_NurseTime<>"") {
		   $time_splitNotes = explode(" ",$txt_NurseTime);
		   
			if($time_splitNotes[1]=="PM" || $time_splitNotes[1]=="pm") {
				
				$time_splitNotestime = explode(":",$time_splitNotes[0]);
				$txt_NurseTimeIncr=$time_splitNotestime[0]+12;
				$txt_NurseTime = $txt_NurseTimeIncr.":".$time_splitNotestime[1].":00";
				
			}elseif($time_splitNotes[1]=="AM" || $time_splitNotes[1]=="am") {
				$time_splitNotestime = explode(":",$time_splitNotes[0]);
				$txt_NurseTime=$time_splitNotestime[0].":".$time_splitNotestime[1].":00";
				
				if($time_splitNotestime[0]=="00" && $time_splitNotestime[1]=="00") {
					$txt_NurseTime=$time_splitNotestime[0].":".$time_splitNotestime[1].":01";
				}
			}
			return $txt_NurseTime;
		}	
	}	

//VIEW RECORD FROM DATABASE
		
		$genAnesNurseFormStatus = '';
		$ViewgenAnesNurseQry = "select *, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat from `genanesthesianursesnotes` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewgenAnesNurseRes = imw_query($ViewgenAnesNurseQry) or die(imw_error()); 
		$ViewgenAnesNurseNumRow = imw_num_rows($ViewgenAnesNurseRes);
		$ViewgenAnesNurseRow = imw_fetch_array($ViewgenAnesNurseRes); 
		
		$genAnesNurseFormStatus = $ViewgenAnesNurseRow["form_status"];
		$anesthesiaGeneral = $ViewgenAnesNurseRow["anesthesiaGeneral"];
		$anesthesiaRegional = $ViewgenAnesNurseRow["anesthesiaRegional"];
		$anesthesiaEpidural = $ViewgenAnesNurseRow["anesthesiaEpidural"];
		$anesthesiaMAC = $ViewgenAnesNurseRow["anesthesiaMAC"];
		$anesthesiaLocal = $ViewgenAnesNurseRow["anesthesiaLocal"];
		$anesthesiaSpinal = $ViewgenAnesNurseRow["anesthesiaSpinal"];
		$anesthesiaSensationAt = $ViewgenAnesNurseRow["anesthesiaSensationAt"];
		$anesthesiaSensationAtDesc = $ViewgenAnesNurseRow["anesthesiaSensationAtDesc"];
		
		$genNotesSignApplet = $ViewgenAnesNurseRow["genNotesSignApplet"];
		$temp = $ViewgenAnesNurseRow["temp"];
		$o2Sat = $ViewgenAnesNurseRow["o2Sat"];
		$painScale = $ViewgenAnesNurseRow["painScale"];
		
		$intake_site1 = $ViewgenAnesNurseRow["intake_site1"];
		$intake_site2 = $ViewgenAnesNurseRow["intake_site2"];
		$intake_site3 = $ViewgenAnesNurseRow["intake_site3"];
		$intake_site4 = $ViewgenAnesNurseRow["intake_site4"];
		$intake_site5 = $ViewgenAnesNurseRow["intake_site5"];
		$intake_site6 = $ViewgenAnesNurseRow["intake_site6"];
		$intake_site7 = $ViewgenAnesNurseRow["intake_site7"];
		
		$intake_solution1 = $ViewgenAnesNurseRow["intake_solution1"];
		$intake_solution2 = $ViewgenAnesNurseRow["intake_solution2"];
		$intake_solution3 = $ViewgenAnesNurseRow["intake_solution3"];
		$intake_solution4 = $ViewgenAnesNurseRow["intake_solution4"];
		$intake_solution5 = $ViewgenAnesNurseRow["intake_solution5"];
		$intake_solution6 = $ViewgenAnesNurseRow["intake_solution6"];
		$intake_solution7 = $ViewgenAnesNurseRow["intake_solution7"];
		
		$intake_credit1 = $ViewgenAnesNurseRow["intake_credit1"];
		$intake_credit2 = $ViewgenAnesNurseRow["intake_credit2"];
		$intake_credit3 = $ViewgenAnesNurseRow["intake_credit3"];
		$intake_credit4 = $ViewgenAnesNurseRow["intake_credit4"];
		$intake_credit5 = $ViewgenAnesNurseRow["intake_credit5"];
		$intake_credit6 = $ViewgenAnesNurseRow["intake_credit6"];
		$intake_credit7 = $ViewgenAnesNurseRow["intake_credit7"];
			
		
		$alterTissuePerfusionTime1 = $ViewgenAnesNurseRow["alterTissuePerfusionTime1"];
		$hidd_alterTissuePerfusionTime1 = $ViewgenAnesNurseRow["alterTissuePerfusionTime1"];
		
		$alterTissuePerfusionTime2 = $ViewgenAnesNurseRow["alterTissuePerfusionTime2"];
		$hidd_alterTissuePerfusionTime2 = $ViewgenAnesNurseRow["alterTissuePerfusionTime2"];
		
		$alterTissuePerfusionTime3 = $ViewgenAnesNurseRow["alterTissuePerfusionTime3"];
		$hidd_alterTissuePerfusionTime3 = $ViewgenAnesNurseRow["alterTissuePerfusionTime3"];
		
		$alterTissuePerfusionInitials1 = $ViewgenAnesNurseRow["alterTissuePerfusionInitials1"];
		$alterTissuePerfusionInitials2 = $ViewgenAnesNurseRow["alterTissuePerfusionInitials2"];
		$alterTissuePerfusionInitials3 = $ViewgenAnesNurseRow["alterTissuePerfusionInitials3"];
		
		$alterGasExchangeTime1 = $ViewgenAnesNurseRow["alterGasExchangeTime1"];
		$hidd_alterGasExchangeTime1 = $ViewgenAnesNurseRow["alterGasExchangeTime1"];
		
		$alterGasExchangeTime2 = $ViewgenAnesNurseRow["alterGasExchangeTime2"];
		$hidd_alterGasExchangeTime2 = $ViewgenAnesNurseRow["alterGasExchangeTime2"];
		
		$alterGasExchangeTime3 = $ViewgenAnesNurseRow["alterGasExchangeTime3"];
		$hidd_alterGasExchangeTime3 = $ViewgenAnesNurseRow["alterGasExchangeTime3"];
		
		
		$alterGasExchangeInitials1 = $ViewgenAnesNurseRow["alterGasExchangeInitials1"];
		$alterGasExchangeInitials2 = $ViewgenAnesNurseRow["alterGasExchangeInitials2"];
		$alterGasExchangeInitials3 = $ViewgenAnesNurseRow["alterGasExchangeInitials3"];
	
		
		$alterComfortTime1 = $ViewgenAnesNurseRow["alterComfortTime1"];
		//$hidd_alterComfortTime1 = $ViewgenAnesNurseRow["alterComfortTime1"];
		
		$alterComfortTime2 = $ViewgenAnesNurseRow["alterComfortTime2"];
		//$hidd_alterComfortTime2 = $ViewgenAnesNurseRow["alterComfortTime2"];
		
		$alterComfortTime3 = $ViewgenAnesNurseRow["alterComfortTime3"];
		//$hidd_alterComfortTime3 = $ViewgenAnesNurseRow["alterComfortTime3"];
		
		$alterComfortInitials1 = $ViewgenAnesNurseRow["alterComfortInitials1"];
		$alterComfortInitials2 = $ViewgenAnesNurseRow["alterComfortInitials2"];
		$alterComfortInitials3 = $ViewgenAnesNurseRow["alterComfortInitials3"];
	
		
		
		$monitorTissuePerfusion = $ViewgenAnesNurseRow["monitorTissuePerfusion"];
		$implementComplicationMeasure = $ViewgenAnesNurseRow["implementComplicationMeasure"];
		$assessPlus = $ViewgenAnesNurseRow["assessPlus"];
		$telemetry = $ViewgenAnesNurseRow["telemetry"];
		$otherTPNurseInter = $ViewgenAnesNurseRow["otherTPNurseInter"];
		$otherTPNurseInterDesc = $ViewgenAnesNurseRow["otherTPNurseInterDesc"];
		$assessRespiratoryStatus = $ViewgenAnesNurseRow["assessRespiratoryStatus"];
		$positionOptimalChestExcusion = $ViewgenAnesNurseRow["positionOptimalChestExcusion"];
		$monitorOxygenationGE = $ViewgenAnesNurseRow["monitorOxygenationGE"];
		$otherGENurseInter = $ViewgenAnesNurseRow["otherGENurseInter"];
		$otherGENurseInterDesc = $ViewgenAnesNurseRow["otherGENurseInterDesc"];
		$assessPain = $ViewgenAnesNurseRow["assessPain"];
		$usePharmacology = $ViewgenAnesNurseRow["usePharmacology"];
		$monitorOxygenationComfort = $ViewgenAnesNurseRow["monitorOxygenationComfort"];
		$otherComfortNurseInter = $ViewgenAnesNurseRow["otherComfortNurseInter"];
		$otherComfortNurseInterDesc = $ViewgenAnesNurseRow["otherComfortNurseInterDesc"]; 
		$goalTPAchieveTime1 = $ViewgenAnesNurseRow["goalTPAchieveTime1"]; 
		$hidd_goalTPAchieveTime1 = $ViewgenAnesNurseRow["goalTPAchieveTime1"]; 
		$goalTPArchieve1 = $ViewgenAnesNurseRow["goalTPArchieve1"]; 
		$goalTPAchieveInitial1  = $ViewgenAnesNurseRow["goalTPAchieveInitial1"]; 
		
		$goalTPAchieveTime2 = $ViewgenAnesNurseRow["goalTPAchieveTime2"]; 
		$hidd_goalTPAchieveTime2 = $ViewgenAnesNurseRow["goalTPAchieveTime2"]; 
		$goalTPAchieve2 = $ViewgenAnesNurseRow["goalTPAchieve2"]; 
		$goalTPAchieveInitial2  = $ViewgenAnesNurseRow["goalTPAchieveInitial2"]; 
		
		$goalGEAchieveTime1 = $ViewgenAnesNurseRow["goalGEAchieveTime1"]; 
		$hidd_goalGEAchieveTime1 = $ViewgenAnesNurseRow["goalGEAchieveTime1"]; 
		$goalGEAchieve1 = $ViewgenAnesNurseRow["goalGEAchieve1"]; 
		$goalGEAchieveInitial1  = $ViewgenAnesNurseRow["goalGEAchieveInitial1"]; 
	
		$goalGEAchieveTime2 = $ViewgenAnesNurseRow["goalGEAchieveTime2"]; 
		$hidd_goalGEAchieveTime2 = $ViewgenAnesNurseRow["goalGEAchieveTime2"]; 
		$goalGEAchieve2 = $ViewgenAnesNurseRow["goalGEAchieve2"]; 
		$goalGEAchieveInitial2  = $ViewgenAnesNurseRow["goalGEAchieveInitial2"]; 
		
		$goalCRPAchieveTime1 = $ViewgenAnesNurseRow["goalCRPAchieveTime1"]; 
		$hidd_goalCRPAchieveTime1 = $ViewgenAnesNurseRow["goalCRPAchieveTime1"]; 
		$goalCRPAchieve1 = $ViewgenAnesNurseRow["goalCRPAchieve1"]; 
		$goalCRPAchieveInitial1  = $ViewgenAnesNurseRow["goalCRPAchieveInitial1"]; 
		
		$goalCRPAchieveTime2 = $ViewgenAnesNurseRow["goalCRPAchieveTime2"]; 
		$hidd_goalCRPAchieveTime2 = $ViewgenAnesNurseRow["goalCRPAchieveTime2"]; 
		$goalCRPAchieve2 = $ViewgenAnesNurseRow["goalCRPAchieve2"]; 
		$goalCRPAchieveInitial2  = $ViewgenAnesNurseRow["goalCRPAchieveInitial2"]; 
		
		$dischargeSummaryTemp = $ViewgenAnesNurseRow["dischargeSummaryTemp"];
		$dischangeSummaryBp = $ViewgenAnesNurseRow["dischangeSummaryBp"];
		$dischargeSummaryP = $ViewgenAnesNurseRow["dischargeSummaryP"];
		$dischangeSummaryRR = $ViewgenAnesNurseRow["dischangeSummaryRR"];
		
		$alert = $ViewgenAnesNurseRow["alert"];
		$hasTakenNourishment = $ViewgenAnesNurseRow["hasTakenNourishment"];
		$nauseasVomiting = $ViewgenAnesNurseRow["nauseasVomiting"];
		$voidedQs = $ViewgenAnesNurseRow["voidedQs"];
		$panv = $ViewgenAnesNurseRow["panv"];
		$dressing = $ViewgenAnesNurseRow["dressing"];
		$dischargeSummaryOther = $ViewgenAnesNurseRow["dischargeSummaryOther"];
		$dischargeAt = $ViewgenAnesNurseRow["dischargeAt"];
		$comments = $ViewgenAnesNurseRow["comments"];
		$form_status = $ViewgenAnesNurseRow["form_status"];
			list($list_hour,$list_min,$secnd) = explode(":",$dischargeAt);
			//echo $list_hour.$list_min;
			if($list_hour>12) {
				$list_hour=$list_hour-12;
				$list_ampm="PM";
			}
		$dischargeAtTime=	$list_hour.':'.$list_min.''.$list_ampm;
		$nurseId = $ViewgenAnesNurseRow["nurseId"];
		$nurseSign = $ViewgenAnesNurseRow["nurseSign"];
		
		$whoUserType = $ViewgenAnesNurseRow["whoUserType"];
		$whoUserTypeLabel = ($whoUserType == 'Anesthesiologist') ? 'Anesthesia&nbsp;Provider' : $whoUserType ;
		$createdByUserId = $ViewgenAnesNurseRow["createdByUserId"];
		$relivedNurseId = $ViewgenAnesNurseRow["relivedNurseId"];
		
		
		$signNurseId =  $ViewgenAnesNurseRow["signNurseId"];
		$signNurseFirstName =  $ViewgenAnesNurseRow["signNurseFirstName"];
		$signNurseMiddleName =  $ViewgenAnesNurseRow["signNurseMiddleName"];
		$signNurseLastName =  $ViewgenAnesNurseRow["signNurseLastName"]; 
		$signNurseStatus =  $ViewgenAnesNurseRow["signNurseStatus"];
		
		$signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
		//$signNurseDateTime =  $ViewPreopnursingRow["signNurseDateTime"];
		
		
		$intake_new_fluids = array();
		$intake_new_amount_given = array();
		for($j=1;$j<=3;$j++) {
			$intake_new_fluids[$j] =  $ViewgenAnesNurseRow["intake_new_fluids$j"];
			$intake_new_amount_given[$j] =  $ViewgenAnesNurseRow["intake_new_amount_given$j"];
			
		}
		
		$ascId = $ViewgenAnesNurseRow["ascId"];
		$confirmation_id = $ViewgenAnesNurseRow["confirmation_id"];
		$patient_id = $ViewgenAnesNurseRow["patient_id"];
		
		
			
		//CODE TO SET gennurseNotes TISSUE TIME
			if($alterTissuePerfusionTime1=="00:00:00" || $alterTissuePerfusionTime1=="") {
				$hidd_alterTissuePerfusionTime1 = "";//date("H:i:s");
			}else {
				$hidd_alterTissuePerfusionTime1 = 	$alterTissuePerfusionTime1;
			}	
			
			if($alterTissuePerfusionTime2=="00:00:00" || $alterTissuePerfusionTime2=="") {
				$hidd_alterTissuePerfusionTime2 = "";//date("H:i:s");
			}else {
				$hidd_alterTissuePerfusionTime2 = 	$alterTissuePerfusionTime2;
			}
			
			if($alterTissuePerfusionTime3=="00:00:00" || $alterTissuePerfusionTime3=="") {
				$hidd_alterTissuePerfusionTime3 = "";//date("H:i:s");
			}else {
				$hidd_alterTissuePerfusionTime3 = 	$alterTissuePerfusionTime3;
			}
			
			$alterTissuePerfusionTime1 = get_timeValue($hidd_alterTissuePerfusionTime1);
			$alterTissuePerfusionTime2 = get_timeValue($hidd_alterTissuePerfusionTime2);
			$alterTissuePerfusionTime3 = get_timeValue($hidd_alterTissuePerfusionTime3);
		//END CODE TO SET gennurseNotes TISSUE TIME
		
		//CODE TO SET gennurseNotes GAS EXCHANGE TIME
			if($alterGasExchangeTime1=="00:00:00" || $alterGasExchangeTime1=="") {
				$hidd_alterGasExchangeTime1 = "";//date("H:i:s");
			}else {
				$hidd_alterGasExchangeTime1 = 	$alterGasExchangeTime1;
			}	
			
			if($alterGasExchangeTime2=="00:00:00" || $alterGasExchangeTime2=="") {
				$hidd_alterGasExchangeTime2 = "";//date("H:i:s");
			}else {
				$hidd_alterGasExchangeTime2 = 	$alterGasExchangeTime2;
			}
			
			if($alterGasExchangeTime3=="00:00:00" || $alterGasExchangeTime3=="") {
				$hidd_alterGasExchangeTime3 = "";//date("H:i:s");
			}else {
				$hidd_alterGasExchangeTime3 = 	$alterGasExchangeTime3;
			}
			
			$alterGasExchangeTime1 = get_timeValue($hidd_alterGasExchangeTime1);
			$alterGasExchangeTime2 = get_timeValue($hidd_alterGasExchangeTime2);
			$alterGasExchangeTime3 = get_timeValue($hidd_alterGasExchangeTime3);
		//END CODE TO SET gennurseNotes GAS EXCHANGE TIME
		
		//CODE TO SET gennurseNotes CONFORT TIME
			if($alterComfortTime1=="00:00:00" || $alterComfortTime1=="") {
				$hidd_alterComfortTime1 = "";//date("H:i:s");
			}else {
				$hidd_alterComfortTime1 = 	$alterComfortTime1;
			}	
			
			if($alterComfortTime2=="00:00:00" || $alterComfortTime2=="") {
				$hidd_alterComfortTime2 = "";//date("H:i:s");
			}else {
				$hidd_alterComfortTime2 = 	$alterComfortTime2;
			}
			
			if($alterComfortTime3=="00:00:00" || $alterComfortTime3=="") {
				$hidd_alterComfortTime3 = "";//date("H:i:s");
			}else {
				$hidd_alterComfortTime3 = 	$alterComfortTime3;
			}
			
			$alterComfortTime1 = get_timeValue($hidd_alterComfortTime1);
			$alterComfortTime2 = get_timeValue($hidd_alterComfortTime2);
			$alterComfortTime3 = get_timeValue($hidd_alterComfortTime3);
		//END CODE TO SET gennurseNotes CONFORT TIME
		
		//CODE TO SET gennurseNotes GOAL TP ACHIVE TIME
			if($goalTPAchieveTime1=="00:00:00" || $goalTPAchieveTime1=="") {
				$hidd_goalTPAchieveTime1 = "";//date("H:i:s");
			}else {
				$hidd_goalTPAchieveTime1 = 	$goalTPAchieveTime1;
			}	
			
			if($goalTPAchieveTime2=="00:00:00" || $goalTPAchieveTime2=="") {
				$hidd_goalTPAchieveTime2 = "";//date("H:i:s");
			}else {
				$hidd_goalTPAchieveTime2 = 	$goalTPAchieveTime2;
			}
			$goalTPAchieveTime1 = get_timeValue($hidd_goalTPAchieveTime1);
			$goalTPAchieveTime2 = get_timeValue($hidd_goalTPAchieveTime2);
		//END CODE TO SET gennurseNotes GOAL TP ACHIVE TIME
		
		//CODE TO SET gennurseNotes GOAL GE ACHIVE TIME
			if($goalGEAchieveTime1=="00:00:00" || $goalGEAchieveTime1=="") {
				$hidd_goalGEAchieveTime1 = "";//date("H:i:s");
			}else {
				$hidd_goalGEAchieveTime1 = 	$goalGEAchieveTime1;
			}	
			
			if($goalGEAchieveTime2=="00:00:00" || $goalGEAchieveTime2=="") {
				$hidd_goalGEAchieveTime2 = "";//date("H:i:s");
			}else {
				$hidd_goalGEAchieveTime2 = 	$goalGEAchieveTime2;
			}
			$goalGEAchieveTime1 = get_timeValue($hidd_goalGEAchieveTime1);
			$goalGEAchieveTime2 = get_timeValue($hidd_goalGEAchieveTime2);
		//END TO SET gennurseNotes GOAL GE ACHIVE TIME
		
		//CODE TO SET gennurseNotes GOAL CRP ACHIVE TIME
			if($goalCRPAchieveTime1=="00:00:00" || $goalCRPAchieveTime1=="") {
				$hidd_goalCRPAchieveTime1 = "";//date("H:i:s");
			}else {
				$hidd_goalCRPAchieveTime1 = 	$goalCRPAchieveTime1;
			}	
			
			if($goalCRPAchieveTime2=="00:00:00" || $goalCRPAchieveTime2=="") {
				$hidd_goalCRPAchieveTime2 = "";//date("H:i:s");
			}else {
				$hidd_goalCRPAchieveTime2 = 	$goalCRPAchieveTime2;
			}
			$goalCRPAchieveTime1 = get_timeValue($hidd_goalCRPAchieveTime1);
			$goalCRPAchieveTime2 = get_timeValue($hidd_goalCRPAchieveTime2);
		//END CODE TO SET gennurseNotes GOAL CRP ACHIVE TIME
	
//END VIEW RECORD FROM DATABASE
//start
$qryUser="select usersId,fname,mname,lname from users";
$qryRes=imw_query($qryUser) or die(imw_error());
$userNameArr=array();
if(imw_num_rows($qryRes)) {
	while($qryrow=imw_fetch_array($qryRes)) {
		$usersId = $qryrow["usersId"];
		$userNameArr[$usersId]=$qryrow['lname'].','.$qryrow['fname'].' '.$qryrow['mname'];
	}
}
if($genNotesSignApplet!='') {
	include("imageSc/imgGd.php");
	$imgName = 'bgGridNN.jpg';
	$pdfFolderPath='new_html2pdf/';
	drawOnImage2($genNotesSignApplet,$imgName,"new_html2pdf/tess_nurse_notes.jpg","",$pdfFolderPath);			
}

$table_pdf.='<table width="100%"><tr><td width="100%" align="center"><b>General Anesthesia Nurses Notes</b></td></tr></table><table width="100%">';
$allrgiesQry=("select allergy_name,reaction_name from patient_allergies_tbl where patient_confirmation_id=$pConfId");
$allergiesRes = imw_query($allrgiesQry);
$allergiesnum=@imw_num_rows($allergiesRes);
$i=0;

$newnotesQry = "select * from `genanesthesianursesnewnotes` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
$newnotesRes = imw_query($newnotesQry) or die(imw_error()); 
$newnotesNumRow = imw_num_rows($newnotesRes);

$table_pdf_new.=$head_table;
$table_pdf_new.="<style>.bdrbtm{vertical-align:middle;}</style>";
	$table_pdf_new.='
		<table style="width:700px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
         	<tr>
				<td colspan="2" style="width:700px;" class="fheader">General Anesthesia Nurses Notes</td>
			</tr>
			<tr>
				<td style="width:350px;" class="bdrbtm bgcolor bold pl5">Allergies</td>
				<td style="width:350px;" class="bdrbtm bgcolor bold pl5">Anesthesia</td>
			</tr>';
					
			$table_pdf_new.='
			<tr>
				<td style="width:350px;vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Name</td>
							<td style="width:145px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Reaction</td>
						</tr>';
						if($allergiesnum>0){
							while($detailsAllergy=@imw_fetch_array($allergiesRes)){
								$table_pdf_new.='
								<tr>
									<td style="width:200px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($detailsAllergy['allergy_name'])).'</td>
									<td style="width:145px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($detailsAllergy['reaction_name'])).'</td>
								</tr>';
							}				
						}else{
						$table_pdf_new.='
							<tr>
								<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
								<td style="width:150px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
							</tr>
							<tr>
								<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
								<td style="width:150px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
							</tr>
							<tr>
								<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
								<td style="width:150px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
							</tr>
							';	
						}
					$table_pdf_new.='	
					</table>
				</td>';
				$table_pdf_new.='
				<td style="width:350px;vertical-align:top;">
					<table style="width:350px;border-left:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="4" style="width:336px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">&nbsp;</td>
						</tr>';
						$table_pdf_new.='
							<tr>
								<td style="width:84px;" class="bdrbtm pl5">General&nbsp;';	if($anesthesiaGeneral)  	{$table_pdf_new	.= '<b>'.$anesthesiaGeneral.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
								<td style="width:90px;" class="bdrbtm pl5">Regional&nbsp;';	if($anesthesiaRegional) 	{$table_pdf_new	.= '<b>'.$anesthesiaRegional.'</b>';}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
								<td style="width:84px;" class="bdrbtm pl5">Epidural&nbsp;';	if($anesthesiaEpidural) 	{$table_pdf_new	.= '<b>'.$anesthesiaEpidural.'</b>';}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
								<td style="width:78px;" class="bdrbtm pl5">MAC&nbsp;';		if($anesthesiaMAC)			{$table_pdf_new	.= '<b>'.$anesthesiaMAC.'</b>';		}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
							</tr>
							<tr>
								<td style="width:84px;" class="bdrbtm pl5">Local&nbsp;';	if($anesthesiaLocal)  		{$table_pdf_new	.= '<b>'.$anesthesiaLocal.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
								<td style="width:90px;" class="bdrbtm pl5">Spinal&nbsp;';	if($anesthesiaSpinal) 		{$table_pdf_new	.= '<b>'.$anesthesiaSpinal.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
								<td colspan="2" style="width:162px;" class="bdrbtm pl5">Sensation At&nbsp;';if($anesthesiaSensationAtDesc) {$table_pdf_new	.= '<b>'.$anesthesiaSensationAtDesc.'</b>';}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
							</tr>
							';
				$table_pdf_new.='
					</table>
				</td>
			</tr>';
			if($genNotesSignApplet!='') {
				$table_pdf_new.='
				<tr>
					<td colspan="2"  valign="top" class="bdrbtm" ><b>Applet Data</b><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">&nbsp;&nbsp;&nbsp;<img src="../new_html2pdf/tess_nurse_notes.jpg" width="350" height="300"></font></td>
				</tr>
				';
			}
			
			
			$table_pdf_new.='
			<tr>
				<td colspan="2"  valign="top" >
					<table style="width:700px;" cellpadding="0" cellspacing="0">
						<tr>
							<td rowspan="5" style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Recovery Room Meds';if(trim($ViewgenAnesNurseRow["recovery_room_na"])=="Yes") {$table_pdf_new.=' - N/A';}$table_pdf_new.='</td>
							<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Medication</td>
							<td style="width:140px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Dose</td>
							<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Route</td>
							<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Time</td>
							<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Initial</td>
						</tr>';
					for($i=1;$i<=4;$i++) {
						$table_pdf_new.='
						<tr>
							<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($ViewgenAnesNurseRow["recovery_new_drugs".$i]).'</td>
							<td style="width:140px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($ViewgenAnesNurseRow["recovery_new_dose".$i]).'</td>
							<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if(trim($ViewgenAnesNurseRow["recovery_new_drugs".$i])) {$table_pdf_new.=$ViewgenAnesNurseRow["recovery_new_route".$i];}$table_pdf_new.='</td>
							<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["recovery_new_time".$i]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["recovery_new_time".$i]);}$table_pdf_new.='</td>
							<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["recovery_new_initial".$i]]).'</td>
						</tr>';
					}
						$table_pdf_new.='
						<tr>
							<td rowspan="4" style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Intake</td>
							<td colspan="2" style="width:320px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">IV fluids</td>
							<td colspan="2" style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Amount Given</td>
							<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">&nbsp;</td>
						</tr>';
					
					for($j=1;$j<=3;$j++) {
						$table_pdf_new.='
						<tr>
							<td colspan="2" style="width:320px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($ViewgenAnesNurseRow["intake_new_fluids".$j]).'</td>
							<td colspan="2" style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($ViewgenAnesNurseRow["intake_new_amount_given".$j]).'</td>
							<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
						</tr>';
					}
					
					if($newnotesNumRow>0) {
						$table_pdf_new.='
						<tr>
							<td style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Time</td>
							<td colspan="5" style="width:620px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Nurses Notes</td>
						</tr>';
					  while($newnotesRow = imw_fetch_array($newnotesRes)) {
						$table_pdf_new.='
						<tr>
							<td style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($newnotesRow["newnotes_time"]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($newnotesRow["newnotes_time"]);}$table_pdf_new.='</td>
							<td colspan="5" style="width:620px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($newnotesRow["newnotes_desc"]).'</td>
						</tr>';
						  
					  }
						
					}
						$table_pdf_new.='
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" >
					<table style="width:700px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bgcolor bold">NURSING DIAGNOSIS</td>
							<td style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bgcolor bold">NURSING INTERVENTION</td>
							<td style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bgcolor bold">GOAL/EVALUATION</td>
						</tr>
						<tr>
							<td style="width:240px;border-right:1px solid #C0C0C0;vertical-align:top;" class="bdrbtm">
								<table style="width:240px;" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="2" style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Alteration in tissue perfusion</td>
									</tr>
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($k=1;$k<=3;$k++) {
									$table_pdf_new.='
									
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["alterTissuePerfusionTime".$k]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["alterTissuePerfusionTime".$k]);}$table_pdf_new.='</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["alterTissuePerfusionInitials".$k]]).'</td>
									</tr>';
								}
									$table_pdf_new.='
									<tr>
										<td colspan="2" style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Alteration in gas exchange</td>
									</tr>
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($l=1;$l<=3;$l++) {
									$table_pdf_new.='
									
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["alterGasExchangeTime".$l]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["alterGasExchangeTime".$l]);}$table_pdf_new.='</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["alterGasExchangeInitials".$l]]).'</td>
									</tr>';
								}
									$table_pdf_new.='
									<tr>
										<td colspan="2" style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Alteration in comfort related pain</td>
									</tr>
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($m=1;$m<=3;$m++) {
									$table_pdf_new.='
									
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["alterComfortTime".$m]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["alterComfortTime".$m]);}$table_pdf_new.='</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["alterComfortInitials".$m]]).'</td>
									</tr>';
								}
									$table_pdf_new.='

								</table>
							</td>
							<td style="width:240px;border-right:1px solid #C0C0C0;vertical-align:top;" class="bdrbtm">
								<table style="width:240px;" cellpadding="0" cellspacing="0">
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Monitor the patient for changes in systemic and peripheral tissue perfusion</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($monitorTissuePerfusion) {$table_pdf_new	.= '<b>'.$monitorTissuePerfusion.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Implement measure to minimize complication and on dimished perfusion</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($implementComplicationMeasure) {$table_pdf_new	.= '<b>'.$implementComplicationMeasure.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Assess Pulse</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($assessPlus) {$table_pdf_new	.= '<b>'.$assessPlus.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Telemetry</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($telemetry) {$table_pdf_new	.= '<b>'.$telemetry.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Other&nbsp;';if(trim($otherTPNurseInterDesc)) {$table_pdf_new	.= '<b>'.stripslashes($otherTPNurseInterDesc).'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($otherTPNurseInter) {$table_pdf_new	.= '<b>'.$otherTPNurseInter.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Assess respiratory status</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($assessRespiratoryStatus) {$table_pdf_new	.= '<b>'.$assessRespiratoryStatus.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Position the patient for optimal chest excursion and its exchange</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($positionOptimalChestExcusion) {$table_pdf_new	.= '<b>'.$positionOptimalChestExcusion.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Monitor oxygenation</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($monitorOxygenationGE) {$table_pdf_new	.= '<b>'.$monitorOxygenationGE.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Other&nbsp;';if(trim($otherGENurseInterDesc)) {$table_pdf_new	.= '<b>'.stripslashes($otherGENurseInterDesc).'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($otherGENurseInter) {$table_pdf_new	.= '<b>'.$otherGENurseInter.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Assess pain</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($assessPain) {$table_pdf_new	.= '<b>'.$assessPain.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Use pharmacology interventions to relieve pain</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($usePharmacology) {$table_pdf_new	.= '<b>'.$usePharmacology.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Monitor oxygenation</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($monitorOxygenationComfort) {$table_pdf_new	.= '<b>'.$monitorOxygenationComfort.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Other&nbsp;';if(trim($otherComfortNurseInterDesc)) {$table_pdf_new	.= '<b>'.stripslashes($otherComfortNurseInterDesc).'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($otherComfortNurseInter) {$table_pdf_new	.= '<b>'.$otherComfortNurseInter.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
								</table>
							</td>
							<td style="width:240px;border-right:1px solid #C0C0C0;vertical-align:top;" class="bdrbtm">
								<table style="width:230px;" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="3" style="width:220px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Patient will show signs of adequate tissue perfusion</td>
									</tr>
									<tr>
										<td style="width:50px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Achieve</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:110px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($p=1;$p<=2;$p++) {
									$goalTPAchieve = $ViewgenAnesNurseRow["goalTPArchieve".$p];
									if($p==2) {$goalTPAchieve = $ViewgenAnesNurseRow["goalTPAchieve".$p];}
									$table_pdf_new.='
									
									<tr>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($goalTPAchieve) {$table_pdf_new	.= '<b>'.$goalTPAchieve.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["goalTPAchieveTime".$p]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["goalTPAchieveTime".$p]);}$table_pdf_new.='</td>
										<td style="width:120px;border-right:1px solid #C0C0C0;font-size:12px;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["goalTPAchieveInitial".$p]]).'</td>
									</tr>';
								}
									$table_pdf_new.='
									<tr>
										<td colspan="3" style="width:220px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Maintain patient airways with adequate exchange</td>
									</tr>
									<tr>
										<td style="width:50px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Achieve</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:110px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($q=1;$q<=2;$q++) {
									$table_pdf_new.='
									<tr>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["goalGEAchieve".$q]) {$table_pdf_new.= '<b>'.$ViewgenAnesNurseRow["goalGEAchieve".$q].'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["goalGEAchieveTime".$q]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["goalGEAchieveTime".$q]);}$table_pdf_new.='</td>
										<td style="width:120px;border-right:1px solid #C0C0C0;font-size:12px;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["goalGEAchieveInitial".$q]]).'</td>
									</tr>';
								}
									$table_pdf_new.='
									<tr>
										<td colspan="3" style="width:220px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Patient denies or shows no sign of excessive pain</td>
									</tr>
									<tr>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Achieve</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:120px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($r=1;$r<=2;$r++) {
									$table_pdf_new.='
									<tr>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["goalCRPAchieve".$r]) {$table_pdf_new.= '<b>'.$ViewgenAnesNurseRow["goalCRPAchieve".$r].'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["goalCRPAchieveTime".$r]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["goalCRPAchieveTime".$r]);}$table_pdf_new.='</td>
										<td style="width:120px;border-right:1px solid #C0C0C0;font-size:12px;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["goalCRPAchieveInitial".$r]]).'</td>
									</tr>';
								}
									$table_pdf_new.='
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="width:350px; border:1px solid #C0C0C0; vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="4" style="width:355px; border:1px solid #C0C0C0;" class="bold bdrbtm pl5 bgcolor">DISCHARGE SUMMARY</td>
						</tr>
						<tr>
							<td style="width:85px;" class="bdrbtm "><b>Temp</b>&nbsp;';
							if($dischargeSummaryTemp){
								$table_pdf_new.=$dischargeSummaryTemp;	
							}else{
								$table_pdf_new.="___";
							}
							$table_pdf_new.='
							</td>
							<td style="width:85px;" class="bdrbtm"><b>BP</b>&nbsp;';
							if($dischangeSummaryBp){
								$table_pdf_new.=$dischangeSummaryBp;	
							}else{
								$table_pdf_new.="___";
							}
							$table_pdf_new.='
							</td>
							<td style="width:85px;" class="bdrbtm"><b>P</b>&nbsp;';
							if($dischargeSummaryP){
								$table_pdf_new.=$dischargeSummaryP;	
							}else{
								$table_pdf_new.="___";
							}
							$table_pdf_new.='
							</td>
							<td style="width:85px;" class="bdrbtm"><b>BP</b>&nbsp;';
							if($dischangeSummaryRR){
								$table_pdf_new.=$dischangeSummaryRR;	
							}else{
								$table_pdf_new.="___";
							}
							$table_pdf_new.='
							</td>
						</tr>
						<tr>
							<td style="width:85px;" class="bdrbtm">Dressing</td>
							<td colspan="3" style="width:250px;" class="bdrbtm">';
							if($dressing){
								$table_pdf_new.=$dressing;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>
						<tr>
							<td style="width:85px;" class="bdrbtm">Other</td>
							<td colspan="3" style="width:250px;" class="bdrbtm">';
							if($dischargeSummaryOther){
								$table_pdf_new.=$dischargeSummaryOther;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>
						<tr>
							<td style="width:85px;" class="bdrbtm">Discharge At</td>
							<td colspan="3" style="width:250px;" class="bdrbtm">';
							if($dischargeAtTime){
								$table_pdf_new.=$objManageData->getTmFormat($dischargeAtTime);
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>
						
					</table>
				</td>
				<td style="width:350px; border:1px solid #C0C0C0;vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:250px;" class="bdrbtm bgcolor pl5"></td>
							<td style="width:100px;" class="bdrbtm cbold bgcolor pl5">Yes/No</td>
						</tr>
						<tr>
							<td style="width:250px;" class="bdrbtm pl5">Alert</td>
							<td style="width:100px;" class="bdrbtm cbold pl5">';
							if($alert){
								$table_pdf_new.=$alert;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>	
						<tr>
							<td style="width:250px;" class="bdrbtm pl5">Has taken nourishment</td>
							<td style="width:100px;" class="bdrbtm cbold  pl5">';
							if($hasTakenNourishment){
								$table_pdf_new.=$hasTakenNourishment;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>	
						<tr>
							<td style="width:250px;" class="bdrbtm pl5">Nausea Vomiting</td>
							<td style="width:100px;" class="bdrbtm pl5 cbold">';
							if($nauseasVomiting){
								$table_pdf_new.=$nauseasVomiting;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>	
						<tr>
							<td style="width:250px;" class="bdrbtm pl5">Voided Q.S.</td>
							<td style="width:100px;" class="bdrbtm pl5 cbold">';
							if($voidedQs){
								$table_pdf_new.=$voidedQs;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>	
						<tr>
							<td style="width:250px;" class="bdrbtm pl5">Pain</td>
							<td style="width:100px;" class="bdrbtm pl5 cbold">';
							if($panv){
								$table_pdf_new.=$panv;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>	
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="width:700px;">
					<table style="width:700px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:85px;" class="bdrbtm"><b>Comments:</b></td>
							<td style="width:200px;" class="bdrbtm">';
							if($comments){
								$table_pdf_new.=stripslashes($comments);
							}else{
								$table_pdf_new.="______";	
							}
							$table_pdf_new.='
							</td>
							<td style="width:200px;" class="bdrbtm"><b>User Type:</b>&nbsp;';
							if($whoUserType){
								$table_pdf_new.=$whoUserTypeLabel;
							}else{
								$table_pdf_new.="______";	
							}
							$table_pdf_new.='
							</td>
							<td style="width:250px;" class="bdrbtm"><b>Created By:&nbsp;</b>';
							if($createdByUserId && $userNameArr[$createdByUserId]){
								
								$table_pdf_new.=$userNameArr[$createdByUserId];
							}else{
								$table_pdf_new.="______";	
							}
							$table_pdf_new.='
							</td>
						</tr>
						<tr>
							<td colspan="3" class="bdrbtm" style="width:485px;">';
							if($signNurseStatus=="Yes"){
								$table_pdf_new.="<br><b>Nurse:&nbsp;</b>".$signNurseLastName.','.$signNurseFirstName;
								$table_pdf_new.="<br><b>Electronically Signed:&nbsp;</b>Yes";
								$table_pdf_new.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($ViewgenAnesNurseRow["signNurseDateTime"]);
							}else {
								$table_pdf_new.="<br><b>Nurse:&nbsp;</b>________";
								$table_pdf_new.="<br><b>Electronically Signed:&nbsp;</b>________";
								$table_pdf_new.="<br><b>Signature Date:&nbsp;</b>________";
							}
							$table_pdf_new.='
							</td>
							<td class="bdrbtm" style="width:250px;"><b>Relief Nurse:&nbsp;</b>';
							if($relivedNurseId && $userNameArr[$relivedNurseId]){
								$table_pdf_new.=$userNameArr[$relivedNurseId];
							}else{
								$table_pdf_new.="_____";
							}
							$table_pdf_new.='
							</td>
						</tr>
					</table>
				</td>
			</tr>			
			';
		$table_pdf_new.='	
		</table>';

$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$table_pdf_new);
fclose($fileOpen);

?>	
 <form name="printgen_anes_nurse_note" action="new_html2pdf/createPdf.php?op=p" method="post">
 </form>
<script language="javascript">
	function submitfn(){
		document.printgen_anes_nurse_note.submit();
	}
</script>
<?php 
if($genAnesNurseFormStatus=='completed' || $genAnesNurseFormStatus=='not completed') {
?>
<script type="text/javascript">
	submitfn();
</script>
<?php
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>	
