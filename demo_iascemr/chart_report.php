<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
set_time_limit(0);
if(!$_SERVER['DOCUMENT_ROOT']) {
	$_SERVER['DOCUMENT_ROOT'] = "/var/www/html";	
}
if($_SESSION["iasc_facility_id"]=="") {
	header('Location:./logout.php');	
}
include_once("common/conDb.php");
$get_http_path=$_REQUEST['get_http_path'];
include_once("admin/classObjectFunction.php");
global $objManageData;
$objManageData = new manageData;
$loginUser = $_SESSION['loginUserId'];
$fac_qry	=	" and st.iasc_facility_id='".$_SESSION["iasc_facility_id"]."' ";
$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' )	; 
$user_name="";
if($loginUser){
	$qry_user="SELECT fname,mname,lname from users where usersId='".$loginUser."'";
	$res_user=imw_query($qry_user);
	$row_user=imw_fetch_assoc($res_user);
	$user_name=trim($row_user['lname'].", ".$row_user['fname']." ".$row_user['mname']);
}

//get detail for logged in facility
$ascName = "";
if($_SESSION['iasc_facility_id'])
{
	$queryFac=imw_query("select fac_name,fac_address1, fac_address2, fac_city, fac_state from facility_tbl where fac_id='".$_SESSION['facility']."'")or die(imw_error());
	$dataFac=imw_fetch_object($queryFac);
	$ascName=stripslashes($dataFac->fac_name);
	$address=stripslashes($dataFac->fac_address1).' '.stripslashes($dataFac->fac_address2).' '.stripslashes($dataFac->fac_city).' '.stripslashes($dataFac->fac_state);
}
 //set surgerycenter detail 
$startdate	= trim($_REQUEST['startdate']);
$enddate	= trim($_REQUEST['enddate']);

if($startdate!="") {
	list($startYY,$startMM,$startDD) = explode("-",$startdate);
	if($startYY && $startMM && $startDD && strlen($startYY)=="4" && strlen($startMM)=="2" && strlen($startDD)=="2") {
		//DO NOTHING
	}else {
		$startdate 	= date("Y-m-d");	
	}
}else {
	$startdate 	= date("Y-m-d");	
}


$andDosQry 	= " AND pc.dos='".$startdate."' ";
if($enddate!="") {
	list($endYY,$endMM,$endDD) = explode("-",$enddate);
	if($endYY && $endMM && $endDD && strlen($endYY)=="4" && strlen($endMM)=="2" && strlen($endDD)=="2") {
		$andDosQry = " AND (pc.dos BETWEEN '".$startdate."' AND '".$enddate."') ";
	}
	//$enddate 	= date("Y-m-d");	
}


$current_date=date("m-d-Y");
$usrQry = "SELECT usersId,CONCAT(lname,', ',fname,' ',mname) AS uname FROM users ORDER BY usersId" ;
$usrRes = imw_query($usrQry) or die($usrQry.imw_error());
if(imw_num_rows($usrRes)>0) {
	while($usrRow 			= imw_fetch_array($usrRes)) {
		$userId 			= $usrRow['usersId'];
		$userName 			= trim($usrRow['uname']);
		$userIdArr[$userId] = $userName;
	}
}

$userGrpQry = "SELECT u.usersId,u.fname, u.mname, u.lname 
				FROM patientconfirmation pc
				INNER JOIN users u ON (u.usersId=pc.surgeonId)
				WHERE pc.surgeonId!='' 
				".$andDosQry."
				GROUP BY pc.surgeonId 
				ORDER BY pc.surgery_time
				";
$userGrpRes = imw_query($userGrpQry) or die($userGrpQry.imw_error());
$t=0;
$table='';
$surgeonIdArr = $surgNameArr = array();
$csv_content = '';
if(imw_num_rows($userGrpRes)>0) {
	while($userGrpRow 		= imw_fetch_array($userGrpRes)) {
		$uId				= $userGrpRow['usersId'];
		$surgeonIdArr[$uId] = $userGrpRow['usersId'];
		$surgNameArr[$uId] 	= $userGrpRow['fname'].' '.$userGrpRow['mname'].' '.$userGrpRow['lname'];	
	}
	$borderBottomFirstRow = $borderBottomSecondRow = 'bottomBorder';
	$recordCounter = 0; 
	foreach($surgeonIdArr as $surgeonId) {
		$t++;
		$sur_name = $surgNameArr[$surgeonId];
		$selQry = "SELECT pdt.patient_fname, pdt.patient_mname, pdt.patient_lname,
						pc.ascId,pc.surgeonId,pc.anesthesiologist_id,pc.nurseId,pc.patientId,DATE_FORMAT(pc.dos,'%m-%d-%Y') AS dosFormat,
						pc.patient_primary_procedure,pc.patient_secondary_procedure,pc.patient_tertiary_procedure,
						dis.procedures_code_name, dis.icd10_code, 
						opr.surgeryTimeIn, opr.surgeryStartTime, opr.surgeryEndTime, opr.surgeryTimeOut
						FROM patientconfirmation pc
						INNER JOIN patient_data_tbl pdt ON (pdt.patient_id=pc.patientId)
						INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId) 
						LEFT JOIN dischargesummarysheet dis ON (dis.confirmation_id = pc.patientConfirmationId) 
						LEFT JOIN operatingroomrecords opr ON (opr.confirmation_id=pc.patientConfirmationId)
						WHERE pc.surgeonId='".$surgeonId."' AND pc.ascId !='0'  
						".$andDosQry." 
						".$fac_con."
						ORDER BY pc.dos,pc.surgery_time";
						//die($selQry);
	
		$selRes = imw_query($selQry) or die($selQry.imw_error());
		$selRow = array();
		
		if(imw_num_rows($selRes)>0) {
			$csv_content 			.= "Physician Name: Dr. ".$sur_name."\n";
			//$csv_content 			.= '"S.No"'.','.'"ASC-ID"'.','.'"Patient Name"'.','.'"Surgeon Name"'.','.'"Anesthesiologist Name"'.','.'"Nurse Name"'.','.'"Location Name"'.','.'"Patient ID"'.','.'"Date of Service"'.','.'"Primary Procedure"'.','.'"Secondary Procedure"'.','.'"Tertiary Procedure"'.','.'"CPT Code"'.','.'"ICD-10 Code"'."\n";
			$csv_content 			.= '"Encounter #"'.','.'"Provider Name"'.','.'"Nurse Name"'.','.'"Location Name"'.','.'"Date of Service"'.','.'"Primary Procedure"'.','.'"Secondary Procedure"'.','.'"Tertiary Procedure"'.','.'"In Room Time"'.','.'"Surgery Start Time "'.','.'"Surgery End Time "'.','.'"Out Room Time"'.','.'"CPT Code"'.','.'"ICD-10 Code"'."\n";
			$sq=0;
			
			while($selRows 	= imw_fetch_array($selRes)) {
				$recordCounter++;
				
				$selRow[] = $selRows;
			}//print'<pre>';print_r($userIdArr);die;
			for($k=0; $k<count($selRow);$k++) {
				$sq++;
				$ascId 							= $selRow[$k]['ascId'];
				$patientFname 					= $selRow[$k]['patient_fname'];
				$patientMname 					= $selRow[$k]['patient_mname'];
				$patientLname 					= $selRow[$k]['patient_lname'];
				$patientName  					= $patientLname.", ".$patientFname;
				$surgeonName					= $userIdArr[$selRow[$k]['surgeonId']];
				$anesName						= $userIdArr[$selRow[$k]['anesthesiologist_id']];
				$nurseName						= $userIdArr[$selRow[$k]['nurseId']];
				$locationName					= $ascName;
				$patientId 						= $selRow[$k]['patientId'];
				$dosFormat 						= $selRow[$k]['dosFormat'];
				$patientPrimaryProcedure 		= $selRow[$k]['patient_primary_procedure'];
				$patientSecondaryProcedure		= $selRow[$k]['patient_secondary_procedure'];
				$patientTertiaryProcedure 		= $selRow[$k]['patient_tertiary_procedure'];
				
				$surgeryTimeIn 					= $selRow[$k]['surgeryTimeIn'];
				$surgeryTimeIn					= "";
				if($selRow[$k]['surgeryTimeIn']!= "") {
					$surgeryTimeIn 				= date('h:i A', strtotime(trim($selRow[$k]['surgeryTimeIn'])));
					if($surgeryTimeIn=="12:00 AM") {
						$surgeryTimeIn			= "";
					}
				}
				$surgeryStartTime				= "";
				if($selRow[$k]['surgeryStartTime'] !="00:00:00") {
					$surgeryStartTime 			= date('h:i A', strtotime($selRow[$k]['surgeryStartTime']));
				}
				$surgeryEndTime					= "";
				if($selRow[$k]['surgeryEndTime'] !="00:00:00") {
					$surgeryEndTime 			= date('h:i A', strtotime($selRow[$k]['surgeryEndTime']));
				}
				$surgeryTimeOut					= "";
				if($selRow[$k]['surgeryTimeOut']!= "") {
					$surgeryTimeOut 			= date('h:i A', strtotime(trim($selRow[$k]['surgeryTimeOut'])));
					if($surgeryTimeOut=="12:00 AM") {
						$surgeryTimeOut			= "";
					}
				}
				
				$proceduresCode					= trim(str_ireplace("##","; ",$selRow[$k]['procedures_code_name']));
				if($proceduresCode!="") {
					$proceduresCode 			= $proceduresCode.";";
				}
				
				$icd10Code						= trim(str_ireplace(",","; ",$selRow[$k]['icd10_code']));
				if($icd10Code!="") {
					$icd10Code 					= $icd10Code.";";
				}
				
				
				//START CSV CODE
				$seq_csv 						=	'"'.trim($sq).'"';
				$ascId_csv 						=	'"'.trim($ascId).'"';
				$patientName_csv 				=	'"'.trim($patientName).'"';
				$surgeonName_csv				=	'"'.trim($surgeonName).'"';
				$anesName_csv					=	'"'.trim($anesName).'"';
				$nurseName_csv					=	'"'.trim($nurseName).'"';
				$locationName_csv				=	'"'.trim($locationName).'"';
				$patientId_csv					=	'"'.trim($patientId).'"';
				$dosFormat_csv					=	'"'.trim($dosFormat).'"';
				$patientPrimaryProcedure_csv	=	'"'.trim($patientPrimaryProcedure).'"';
				$patientSecondaryProcedure_csv	=	'"'.trim($patientSecondaryProcedure).'"';
				$patientTertiaryProcedure_csv	=	'"'.trim($patientTertiaryProcedure).'"';
				$proceduresCode_csv				=	'"'.trim($proceduresCode).'"';
				$icd10Code_csv					=	'"'.trim($icd10Code).'"';
				
				$surgeryTimeIn_csv				=	'"'.trim($surgeryTimeIn).'"';
				$surgeryStartTime_csv			=	'"'.trim($surgeryStartTime).'"';
				$surgeryEndTime_csv				=	'"'.trim($surgeryEndTime).'"';
				$surgeryTimeOut_csv				=	'"'.trim($surgeryTimeOut).'"';
				
				
				$csv_content 				   .= $ascId_csv.','.$surgeonName_csv.','.$nurseName_csv.','.$locationName_csv.','.$dosFormat_csv.','.$patientPrimaryProcedure_csv.','.$patientSecondaryProcedure_csv.','.$patientTertiaryProcedure_csv.','.$surgeryTimeIn_csv.','.$surgeryStartTime_csv.','.$surgeryEndTime_csv.','.$surgeryTimeOut_csv.','.$proceduresCode_csv.','.$icd10Code_csv."\n";		
				
				//END CSV CODE
			}
		}
	}
	if($recordCounter > 0){
		$csv_content1 = $ascName.','."".','."".',Surgery Center Chart Report '.date("m-d-Y");
		$csv_content2 = $address.','."".','."".','."".','."".','."".','."".','."";
		
		$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/chart_report.csv';
		if(file_exists($file_name)) {
			@unlink($file_name);
		}
		$fpH1 = fopen($file_name,'w');
		fwrite($fpH1, $csv_content1."\n");
		fwrite($fpH1, $csv_content2."\n\r");
		fwrite($fpH1, $csv_content."\n\r");
		$objManageData->download_file($file_name);
		fclose($fpH1);
		exit;
	}

}
//echo $table;


		
?>	
<!DOCTYPE html>
<html>
<head>
<title>Chart Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >   

<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
</head>
<body>
<form name="printFrm" action="new_html2pdf/createPdf.php?op=l" method="post">

</form>
<?php 
if($recordCounter > 0){?>
	<script type="text/javascript">
        window.focus();
		submitfn();
    </script>
<?php 
}else {?>
	<script>
		if(document.getElementById("loader_tbl")) {
			document.getElementById("loader_tbl").style.display = "none";	
		}
	</script>	
	<table style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#EAF0F7; width:100%; height:100%;">
		<tr>
			<td class="alignCenter valignTop" style="width:100%;"><b>No Record Found</b></td> 
		</tr>
	</table>
<?php		
}?>
</body>

