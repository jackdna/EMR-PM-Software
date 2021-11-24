<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php

include_once("common/conDb.php");
include_once("common_functions.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

include_once("common/header_print_function.php");
include_once("imageSc/imgTimeInterval.php");
include_once("imageSc/imgGd.php");
require_once("new_html2pdf/html2pdf.class.php");
$_SERVER["DOCUMENT_ROOT"] = "/var/www/html";
$rootServerPath = $_SERVER["DOCUMENT_ROOT"];
if(!is_dir($rootServerPath."/".$surgeryCenterDirectoryName."/"."outbound")){		
	mkdir($rootServerPath."/".$surgeryCenterDirectoryName."/"."outbound");
}
$selected_date = $_REQUEST["selected_date"];
if(!$selected_date) {
	$selected_date = date("Y-m-d");	
}
$showDate = date("m-d-Y",strtotime($selected_date));
$dateFolderPath = "outbound/".$showDate;
$inboundFolderPath = "inbound/".$showDate;

$fac_con = "";
$fac_con_qry = "";
if($_REQUEST['iasc_facility_id'])
{
	$fac_con=" AND stub_tbl.iasc_facility_id='".$_REQUEST["iasc_facility_id"]."' "; 
	$fac_con_qry=" AND st.iasc_facility_id='".$_SESSION["iasc_facility_id"]."' "; 
}

//START GET ALL ANESTHESIOLOGIST FROM USERS
$anesTypeQry 			= "SELECT usersId, userTitle, fname, mname, lname FROM users WHERE user_type = 'Anesthesiologist' ORDER BY usersId";
$anesTypeRes 			= imw_query($anesTypeQry) or die($anesTypeQry.imw_error());
$arrAnesId 				= $arrAnesTitle = $arrAnesFname = $arrAnesMname = $arrAnesLname = array();
if(imw_num_rows($anesTypeRes)>0) {
	while($anesTypeRow 	= imw_fetch_array($anesTypeRes)) {
		$arrAnesId[] 	= $anesTypeRow['usersId'];
		$arrAnesTitle[] = trim(stripslashes($anesTypeRow['userTitle']));
		$arrAnesFname[] = trim(stripslashes($anesTypeRow['fname']));
		$arrAnesMname[] = trim(stripslashes($anesTypeRow['mname']));
		$arrAnesLname[] = trim(stripslashes($anesTypeRow['lname']));
	}
}
//END GET ALL ANESTHESIOLOGIST FROM USERS

$showAllApptStatusQry = " AND  patient_status!='Canceled' ";

$stub_tbl_group_query = "select surgeon_fname, surgeon_mname, surgeon_lname from stub_tbl where dos = '$selected_date' $fac_con $showAllApptStatusQry ORDER BY surgery_time, surgeon_fname"; 		
$stub_tbl_group_res = imw_query($stub_tbl_group_query) or die($stub_tbl_group_query.imw_error());
$stub_tbl_group_NumRow = imw_num_rows($stub_tbl_group_res);
$t=0;
$table='';
if($stub_tbl_group_NumRow>0){
	$stub_tbl_groupTemp = array();
	while($stub_tbl_group_row = imw_fetch_array($stub_tbl_group_res)) {
		$stub_surgeon_name = "";
		$stub_tbl_group_surgeon_name = "";
		$stub_surgeon_name = $stub_tbl_group_row['surgeon_fname'].' '.$stub_tbl_group_row['surgeon_mname'].' '.$stub_tbl_group_row['surgeon_lname'];
		
		if(!in_array($stub_surgeon_name,$stub_tbl_groupTemp)) {
			$stub_tbl_groupTemp[] = $stub_surgeon_name;
			$stub_tbl_group_surgeon_fname[] = $stub_tbl_group_row['surgeon_fname'];
			$stub_tbl_group_surgeon_mname[] = $stub_tbl_group_row['surgeon_mname'];
			$stub_tbl_group_surgeon_lname[] = $stub_tbl_group_row['surgeon_lname'];
		}
	}
	$tmpCnt = 1;
	$prev_sur_name='';
	
	for($s=0;$s<count($stub_tbl_groupTemp);$s++) { 
		$sur_name='';
		$stub_tbl_group_surgeon_lname[$s];
		$stub_tbl_group_surgeon_fname[$s];
		$stub_tbl_group_surgeon_mname[$s];
		$sur_name = $stub_tbl_groupTemp[$s];
		$t++;
		
		$csv_content1 = "".','."".','."".','."".','."".','."".',Surgery Center Day Report '.$showDate;
		//$csv_content2 = $address.','."".','."".','."".','."".','."".','."".','."";
		$csv_content2 = ""; 
		$csv_content .= "Phys.Name: Dr. ".$sur_name."\n";
		$csv_content .= 'Seq'.','.'"Patient Name"'.','.'"DOB"'.','
						.'"Street"'.','.'"City"'.','.'"State"'.','.'"Zip"'.','.'"Phone"'.','
						.'"PickUp"'.','.'"Arrival"'.','.'"Surgery"'.','.'"Procedure"'.','.'"Comments"'.','.'"Anesthesiologist"'."\n";
		
		
		$report="SELECT *, DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format from stub_tbl where surgeon_fname = '".$stub_tbl_group_surgeon_fname[$s]."' and surgeon_mname = '".$stub_tbl_group_surgeon_mname[$s]."' and surgeon_lname = '".$stub_tbl_group_surgeon_lname[$s]."' and dos = '$selected_date' $showAllApptStatusQry $fac_con order by surgery_time"	;	
		$day_rpt=imw_query($report) or die($report.imw_error());
		$n=@imw_num_rows($day_rpt);
		$a=0;
		$sq=0;
		while($rpt=imw_fetch_array($day_rpt)) {
			$sq++;
			$confi						= 	$rpt['patient_confirmation_id'];
			$patientfname				=	$rpt['patient_first_name'];
			$patientmname				=	$rpt['patient_middle_name'];
			$patientlname				=	$rpt['patient_last_name'];
			$patient_name				=	$patientlname.", ".$patientfname;
			if(trim($patientmname)) {
				$patient_name 			= 	$patient_name." ".trim($patientmname);	
			}
			$patientDOB					=	trim($rpt['patient_dob_format']);
			$stub_tbl_patient_status	=	$rpt['patient_status'];
			$from_time					=	$rpt['pickup_time'];
			$to_time					=	$rpt['arrival_time'];
			$surgeryTime				= 	$rpt['surgery_time'];
			$procedure_name				=	$rpt['patient_primary_procedure'];
			$sec_procedure_name			=	$rpt['patient_secondary_procedure'];
			$ter_procedure_name			=	$rpt['patient_tertiary_procedure'];
			$stub_tbl_site				=	$rpt['site'];
			$in_time					=	$rpt['checked_in_time'];
			$out_time					=	$rpt['checked_out_time'];
			$comment					= 	$rpt['comment']; 
			$patientStreet1 			= 	$rpt['patient_street1'];
			$patientCity 				= 	$rpt['patient_city'];
			$patientState 				= 	$rpt['patient_state'];
			$patientZip 				= 	$rpt['patient_zip'];
			$patientHomePhone 			= 	$rpt['patient_home_phone'];
			$patientHomePhone 			=   $objManageData->format_phone($patientHomePhone);
			
			$anesthesiologist_title 	= 	"";
			$anesthesiologist_fname 	= 	trim(stripslashes($rpt['anesthesiologist_fname']));
			$anesthesiologist_mname 	= 	trim(stripslashes($rpt['anesthesiologist_mname']));
			$anesthesiologist_lname 	= 	trim(stripslashes($rpt['anesthesiologist_lname']));
			for($p=0; $p<count($arrAnesId);$p++) {
				if(strtolower($anesthesiologist_fname) == strtolower($arrAnesFname[$p]) && strtolower($anesthesiologist_mname) == strtolower($arrAnesMname[$p]) && strtolower($anesthesiologist_lname) == strtolower($arrAnesLname[$p])) {
					$anesthesiologist_title = $arrAnesTitle[$p];
					$anesthesiologist_fname = $arrAnesFname[$p]; 
					$anesthesiologist_mname = $arrAnesMname[$p]; 
					$anesthesiologist_lname = $arrAnesLname[$p];
						
				}
			}
			
			
			//START CODE TO GET ASC-ID, anesthesiologist_id
			$getAscQry="SELECT ascId,patientConfirmationId,anesthesiologist_id
					FROM patientconfirmation
					WHERE patientConfirmationId='".$confi."'
					ORDER BY patientConfirmationId DESC LIMIT 0,1";
			$getAscRes=imw_query($getAscQry) or die($getAscQry.imw_error());
			$getAscRow=imw_fetch_array($getAscRes);
			$confirmAnesId = $getAscRow['anesthesiologist_id'];
			$confirmAscId = $getAscRow['ascId'];
			//END CODE TO GET ASC-ID
			
			//START GET ANESTHESIOLOGIST IF CONFIRMED
			for($q=0; $q<count($arrAnesId);$q++) {
				if($confirmAnesId == $arrAnesId[$q]) {
					$anesthesiologist_title = $arrAnesTitle[$q];
					$anesthesiologist_fname = $arrAnesFname[$q]; 
					$anesthesiologist_mname = $arrAnesMname[$q]; 
					$anesthesiologist_lname = $arrAnesLname[$q];
				}
			}
			$anesthesiologistName = $anesthesiologist_mname_new="";
			if($anesthesiologist_mname) { $anesthesiologist_mname_new = " ".$anesthesiologist_mname;}
			$anesthesiologistName = trim($anesthesiologist_title." ".$anesthesiologist_fname.$anesthesiologist_mname_new." ".$anesthesiologist_lname);
			//END GET ANESTHESIOLOGIST IF CONFIRMED
			
			//START CODE TO SET SURGERY-TIME
			$surgery_time='';
			if($surgeryTime!='00:00:00' && $surgeryTime!='0') {
				$surgery_time = date('h:i A',strtotime($surgeryTime));
			}
			//END CODE TO SET SURGERY-TIME
			
			//START APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE

				if($stub_tbl_site=='left') {
					$procedure_name = $procedure_name.' OS';
					if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' OS';}
					if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' OS';}
				}else if($stub_tbl_site=='right') {
					$procedure_name = $procedure_name.' OD';
					if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' OD';}
					if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' OD';}
				}else if($stub_tbl_site=='both') {
					$procedure_name = $procedure_name.' OU';
					if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' OU';}
					if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' OU';}
				}
			//END APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE 				
			$comment = wordwrap($comment,40,"<br>",1);
			if(strlen($comment)>200){ $comment = substr($comment,0,200);}
			
			$borderBottomFirstRow = $borderBottomSecondRow = 'bottomBorder';
			if($anesthesiologist_fname) { $borderBottomFirstRow = ''; }
			
			$patientDOBVal		=	"";
			if($patientDOB!="00/00/0000" && $patientDOB!=0) {
				$patientDOBVal 	= 	$patientDOB;	
			}
			
			//START CSV CODE
			$patient_name_csv 	=	'"'.trim($patient_name).'"';
			$comments_csv 		=	'"'.trim(str_ireplace("<br>","  ",$comment)).'"';
			$procedure_name_csv	=	'"'.str_ireplace("<br>","  ",$procedure_name).'"';
			$patientStreet1Csv	=	'"'.stripslashes(htmlentities($patientStreet1)).'"';
			$patientCityCsv 	=	'"'.trim($patientCity).'"';
			$patientStateCsv 	=	'"'.trim($patientState).'"';
			$patientZipCsv 		=	'"'.trim($patientZip).'"';
			$patientHomePhoneCsv=	'"'.stripslashes($patientHomePhone).'"';
			//END CSV CODE
			
			$csv_content .= $sq.','.$patient_name_csv.','.$patientDOBVal.','
							.$patientStreet1Csv.','.$patientCityCsv.','.$patientStateCsv.','.$patientZipCsv.','.$patientHomePhoneCsv.','
							.$from_time.','.$to_time.','.$surgery_time.','.$procedure_name_csv.','.$comments_csv.','.$anesthesiologistName."\n";		
			
		
			if(!is_dir($rootServerPath."/".$surgeryCenterDirectoryName."/".$dateFolderPath)){		
				mkdir($rootServerPath."/".$surgeryCenterDirectoryName."/".$dateFolderPath);
			}
		}
		
	}
	if(!is_dir($rootServerPath."/".$surgeryCenterDirectoryName."/".$dateFolderPath)){		
		mkdir($rootServerPath."/".$surgeryCenterDirectoryName."/".$dateFolderPath);
	}
	$file_name=$rootServerPath."/".$surgeryCenterDirectoryName."/".$dateFolderPath."/day_schedule_".$showDate.".csv";
	if(file_exists($file_name)) {
		@unlink($file_name);
	}
	$fpH1 = fopen($file_name,'w');
	fwrite($fpH1, $csv_content1."\n");
	fwrite($fpH1, $csv_content2."\n\r");
	fwrite($fpH1, $csv_content."\n\r");
	fclose($fpH1);
	
	
}

$loginMsg = "Log started Date Time ".date("m-d-Y h:i:s A");
$schApptMsg = "Total number of scheduled appointment for ".$showDate." is ".$stub_tbl_group_NumRow;

$txt_file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/outbound/log.txt';
$fpH2 = fopen($txt_file_name,'a+');
fwrite($fpH2, $loginMsg." \n");
fwrite($fpH2, $schApptMsg." \n\r");
$msgInfoArr = array();
$msgInfoArr[] = $loginMsg;
$msgInfoArr[] = $schApptMsg;

//START CODE TO SAVE PDF FOR MAC/REGIONAL ANESTHESIA CHART
$macQry="SELECT pc.ascId, pc.patientConfirmationId,pd.patient_fname, pd.patient_lname
		FROM patientconfirmation pc
		INNER JOIN patient_data_tbl pd ON (pd.patient_id = pc.patientId)
		INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId AND st.patient_status !='Canceled' ".$fac_con_qry." ) 
		INNER JOIN localanesthesiarecord l ON (l.confirmation_id = pc.patientConfirmationId AND l.form_status != '') 
		WHERE pc.dos = '".$selected_date."' AND pc.dos != '0000-00-00' AND pc.ascId !='0' AND pc.ascId !='' 
		ORDER BY pc.ascId,pc.patientConfirmationId";
$macRes=imw_query($macQry) or die($macQry.imw_error());
$macNumRow = imw_num_rows($macRes);
if($macNumRow > 0) {
	while($macRow			=	imw_fetch_array($macRes)) {
		$savePdfAscId 			= $macRow['ascId'];
		$savePdfConfirmationId 	= $macRow['patientConfirmationId'];
		$savePdfPatientFname 	= $macRow['patient_fname'];
		$savePdfPatientLname 	= $macRow['patient_lname'];
		$savePdfPatientName		= $savePdfPatientFname.'_'.$savePdfPatientLname;
		include("day_anesthesia_chart_report_print_export.php");
		
	}
}
$macRegMsg = "Total number of Mac/Regional PDF for ".$showDate." is ".$macNumRow;
fwrite($fpH2, $macRegMsg." \n\r");
$msgInfoArr[] = $macRegMsg;
//END CODE TO SAVE PDF FOR MAC/REGIONAL ANESTHESIA CHART

//START CODE TO SAVE PDF FOR FACESHEET
$facesheetQry = "SELECT st.appt_id, pc.ascId, pc.patientConfirmationId,pd.patient_fname, pd.patient_lname 
				 FROM patientconfirmation pc
				 INNER JOIN patient_data_tbl pd ON (pd.patient_id = pc.patientId)
				 INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId AND st.patient_status !='Canceled' ".$fac_con_qry.") 
				 WHERE pc.dos = '".$selected_date."' AND pc.dos != '0000-00-00' AND pc.ascId !='0' AND pc.ascId !='' 
				 ORDER BY pc.ascId,pc.patientConfirmationId";
$facesheetRes=imw_query($facesheetQry) or die($facesheetQry.imw_error());
$facesheetNumRow = imw_num_rows($facesheetRes);
$facesheetCnt = 0;
if($facesheetNumRow > 0) {
	while($facesheetRow	=	imw_fetch_array($facesheetRes)) {
		$schedule_id 				= $facesheetRow["appt_id"];
		$facesheetPdfAscId 			= $facesheetRow['ascId'];
		$facesheetPdfConfirmationId	= $facesheetRow['patientConfirmationId'];
		$facesheetPdfPatientFname 	= $facesheetRow['patient_fname'];
		$facesheetPdfPatientLname 	= $facesheetRow['patient_lname'];
		$facesheetPdfPatientName	= $facesheetPdfPatientFname.'_'.$facesheetPdfPatientLname;
		include("export_facesheet.php");
		$facesheetCnt++;
	}
}
$facesheetMsg = "Total number of Facesheet PDF for ".$showDate." is ".$facesheetCnt;
fwrite($fpH2, $facesheetMsg." \n\r");
$msgInfoArr[] = $facesheetMsg;

//END CODE TO SAVE PDF FOR FACESHEET


// Start Saving Discharge Summary Sheet 
$dischargeCnt = 0;
$dischargeQry = "SELECT st.appt_id, pc.ascId, pc.patientConfirmationId,pd.patient_fname, pd.patient_lname 
				 FROM patientconfirmation pc
				 INNER JOIN patient_data_tbl pd ON (pd.patient_id = pc.patientId)
				 INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId AND st.patient_status !='Canceled' ".$fac_con_qry.") 
				 INNER JOIN dischargesummarysheet d ON (d.confirmation_id = pc.patientConfirmationId AND d.form_status != '') 	
				 WHERE pc.dos = '".$selected_date."' AND pc.dos != '0000-00-00' AND pc.ascId !='0' AND pc.ascId !='' 
				 ORDER BY pc.ascId,pc.patientConfirmationId";
$dischargeRes=imw_query($dischargeQry) or die($dischargeQry.imw_error());
$dischargeNumRow = imw_num_rows($dischargeRes);
if($dischargeNumRow > 0) {
	while($dischargeRow	=	imw_fetch_array($dischargeRes)) {
		$dischargePdfAscId 				= $dischargeRow['ascId'];
		$dischargePdfConfirmationId= $dischargeRow['patientConfirmationId'];
		$dischargePdfPatientFname 	= $dischargeRow['patient_fname'];
		$dischargePdfPatientLname 	= $dischargeRow['patient_lname'];
		$dischargePdfPatientName		= $dischargePdfPatientFname.'_'.$dischargePdfPatientLname;
		include("discharge_detail_reportpop_export.php");
		$dischargeCnt++;
	}
}
$dischargeMsg = "Total number of Discharge Summary PDF for ".$showDate." is ".$dischargeCnt;
fwrite($fpH2, $dischargeMsg." \n\r");
$msgInfoArr[] = $dischargeMsg;

// End Saving Discharge Summary Sheet 


//START CODE FOR SFTP
$strServerIP = "ftp.medsuite.net";
$strServerPort = "22";
$strServerUsername = "serene.palisades";
$strServerPassword = "eAmi7hgp6XZ9pLHB";
$strTimeOut=1000;//seconds

set_include_path(get_include_path() . PATH_SEPARATOR . './phpseclib0.3.8');
include('Net/SFTP.php');
/* Change the following directory path to your specification */
$local_directory = $rootServerPath."/".$surgeryCenterDirectoryName."/".$dateFolderPath."/";
$remote_path = '/home/serene.palisades/';//remote path
$remote_directory = '/home/serene.palisades/'.$inboundFolderPath.'/';//providing physical(full) path

$fileLoginInfo = '';
/* Add the correct FTP credentials below */
$sftp = new Net_SFTP($strServerIP,$strServerPort,$strTimeOut);
if (!$sftp->login($strServerUsername,$strServerPassword)) 
{
    $msgInfoArr[] = $fileLoginInfo = ('SFTP Login Failed');
} 


if(!in_array('SFTP Login Failed', $msgInfoArr)) {
	/* We save all the filenames in the following array */
	$files_to_upload = array();
	
	/* Open the local directory form where you want to upload the files */
	if ($handle = opendir($local_directory)) 
	{
		/* This is the correct way to loop over the directory. */
		while (false !== ($file = readdir($handle))) 
		{
			if ($file != "." && $file != "..") 
			{
				$files_to_upload[] = $file;
			}
		}
	
		closedir($handle);
	}
	$finalCount = 0;
	if(!empty($files_to_upload))
	{
		if(!$sftp->file_exists($remote_directory))
		{
			//create directory
			//$sftp->mkdir($remote_directory);
		}	
		/* Now upload all the files to the remote server */
		foreach($files_to_upload as $file)
		{
			$remote_sub_directory = "";
			$directoryArr = array("day_schedule_"=>"Schedules","anes_"=>"AnesRecords","facesheet_"=>"Demographics","discharge_summary_"=>"Coding");
			foreach($directoryArr as $searchCriteria => $searchDirectoryName) {
				if(stristr($file,$searchCriteria)) {
					$remote_directory = $remote_path.$searchDirectoryName."/";
					$remote_sub_directory = $remote_directory.$showDate."/";
					if(!$sftp->file_exists($remote_directory)){
						//create directory
						$sftp->mkdir($remote_directory);
					}
					if($searchDirectoryName == "Schedules") {
						$remote_sub_directory = $remote_directory; //DO NOT CREATE DATE FOLDER IN CASE OF DAY SCHEDULE CSV FILE
					}
					if(!$sftp->file_exists($remote_sub_directory)){
						//create sub directory
						$sftp->mkdir($remote_sub_directory);
					}
				}
			}
			/* Upload the local file to the remote server 
			 put('remote file', 'local file');
			*/
			if(file_exists($local_directory.$file) && $remote_sub_directory) {
			  $success = $sftp->put($remote_sub_directory . $file, 
									$local_directory . $file, 
									 NET_SFTP_LOCAL_FILE);
				$finalCount++; 
			}
		}
	}
}

if($fileLoginInfo) {
	//ADD INFO IN LOG FILE
	fwrite($fpH2, $fileLoginInfo." \n\r");
}

$fileTransferInfo = "Total files transfer to inboud (SFTP) is ".$finalCount;

//ADD INFO IN LOG FILE
fwrite($fpH2, $fileTransferInfo." \n\r");
fclose($fpH2);

$msgInfoArr[] = $fileTransferInfo;	

/*
/home/serene.palisades/AnesRecords
/home/serene.palisades/Demographics
/home/serene.palisades/Schedules
*/
//END CODE FOR SFTP

if(count($msgInfoArr)>0) {
	echo implode("<br>",$msgInfoArr);	
}


?>
