<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
if($_REQUEST['hidd_report_format']!='csv') {
	echo '<table id="loader_tbl" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">Please wait while data is retrieving from the server.</td>
			</tr>
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center"><img src="images/pdf_load_img.gif"></td> 
			</tr>
		</table>';
}
set_time_limit(500);
include("common/conDb.php");
include('connect_imwemr.php');
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include_once("admin/classObjectFunction.php");
global $objManageData;
$objManageData = new manageData;


$imwPatientIdSet = trim($_REQUEST['imwPatientId']);
if( substr($imwPatientIdSet,-1,1) == "," ){
	$imwPatientIdSet = substr($imwPatientIdSet,0,-1);
}
$imwPatientIdSet = ($imwPatientIdSet) ? $imwPatientIdSet : '0';

$ptCountyArr = array();
$imwQry = "SELECT pid, county FROM ".$imw_db_name.".patient_data WHERE pid in(".$imwPatientIdSet.") ORDER BY pid";
$imwRes = imw_query($imwQry) or die('Error found @ Line No. '.(__LINE__).': '.imw_error());				
if(imw_num_rows($imwRes)>0) {
	while($imwRow = imw_fetch_assoc($imwRes)) {
		$imwPtId = $imwRow["pid"];
		$county = $imwRow["county"];
		$ptCountyArr[$imwPtId] = $county;	
	}
}


//START GET MASTER PROCEDURE WITH SPECIFIC CATEGORY
$procQry = "SELECT procedureId FROM procedures prs 
			INNER JOIN procedurescategory pct ON (prs.catId = pct.proceduresCategoryId AND (pct.proceduresCategoryId IN(1,2) OR pct.isMisc='1' OR pct.isInj ='1') )
			WHERE prs.procedureId !='0'
			ORDER BY prs.procedureId";
$procRes = imw_query($procQry) or die('Error found @ Line No. '.(__LINE__).': '.imw_error());				
if(imw_num_rows($procRes)>0) {
	while($procRow = imw_fetch_assoc($procRes)) {
		$procedureIdArr[] = $procRow['procedureId'];
	}
}
//END GET MASTER PROCEDURE WITH SPECIFIC CATEGORY
			
$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes= imw_query($SurgeryQry) or die($SurgeryQry.imw_error());
while($SurgeryRecord=imw_fetch_array($SurgeryRes))
{
	$img = $SurgeryRecord['logoName'];
	$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
}

$bakImgResource = imagecreatefromstring($surgeryCenterLogo);
imagejpeg($bakImgResource,'new_html2pdf/white.jpg');
$size=getimagesize('new_html2pdf/white.jpg');
$hig=$size[1];
$wid=$size[0];
$higinc=$hig+10;
$filename='new_html2pdf/white.jpg';

function showThumbImages($fileName='white.jpg',$targetWidth=500,$targetHeight=70)
{ 
	if(file_exists($fileName))
	{ 
		$img_size=getimagesize('new_html2pdf/white.jpg');
		$width=$img_size[0];
		$height=$img_size[1];
		$filename;
		do
		{
			if($width > $targetWidth)
			{
				 $width=$targetWidth;
				 $percent=$img_size[0]/$width;
				 $height=$img_size[1]/$percent; 
			}
			if($height > $targetHeight)
			{
				$height=$targetHeight;
				$percent=$img_size[1]/$height;
				$width=$img_size[0]/$percent; 
			}

		}while($width > $targetWidth || $height > $targetHeight);

		$returnArr[] = "<img src='white.jpg' width='$width' height='$height'>";
		$returnArr[] = $width;
		$returnArr[] = $height;
		return $returnArr; 
	} 
	return "";
}
	 		
// end set surgerycenter detail  

function getConsumeTimeSlb($startTime, $endTime){
	$dt = date("Y-m-d");
	$docTime='';
	if($startTime && $endTime) {
		if(strtotime($startTime)>strtotime($endTime)) {
			$startTime = date('Y-m-d H:i:s', strtotime("$dt $startTime"));
			$edate = date('Y-m-d', strtotime($dt. ' + 1 day'));
			$endTime = date('Y-m-d H:i:s', strtotime("$edate $endTime"));
		}
	}
	$seconds = strtotime($endTime) - strtotime($startTime);
	if($seconds<60){
		$seconds= $seconds;
	}else{
		$minutes = floor($seconds/60);
		$seconds = $seconds%60;
		if($minutes>60) {
			$hour=floor($minutes/60);
			$minutes = $minutes%60;
		}else{
			$minutes= $minutes;
		}
	}
	if($hour>0 || $minutes>0 || $seconds>0){
		$hour= ($hour<10) ? '0'.$hour : $hour;
		$minutes= ($minutes<10) ? '0'.$minutes : $minutes;
		$seconds= ($seconds<10) ? '0'.$seconds : $seconds;

		$hour= ($hour==0) ? '00' : $hour;
		$minutes= ($minutes==0) ? '00' : $minutes;
		$seconds= ($seconds==0) ? '00' : $seconds;

		$docTime = $hour.':'.$minutes.':'.$seconds;
	}
	
	return $docTime;
}

$stubIdSet = trim($_REQUEST['stubId']);
if( substr($stubIdSet,-1,1) == "," ){
	$stubIdSet = substr($stubIdSet,0,-1);
}
$stubIdSet = ($stubIdSet) ? $stubIdSet : '0';

//date1
$selected_date	= $_REQUEST['startdate'];
$selected_date2	= $_REQUEST['enddate'];

$frm_date 	= date('m-d-Y',strtotime($selected_date));
$to_date 	= date('m-d-Y',strtotime($selected_date2));

$current_date=date("m-d-Y");
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+135;	

$t=0;
$table = $tableCounty = '';
$surgeonIdArr = $surgNameArr = array();
$physician_report = "";
$genderArr = array("m"=>"Male","f"=>"Female");

// Start Collecting Surgery Log Book Report Data
$physician_report = "SELECT st.stub_id, 
					IFNULL(st.patient_id_stub,'0') 				AS stub_patient_id_stub, 
					IFNULL(st.imwPatientId,'0') 				AS stub_imw_pt_data_id, 
					IFNULL(st.patient_first_name,'') 			AS stub_patient_first_name, 
					IFNULL(st.patient_middle_name,'') 			AS stub_patient_middle_name, 
					IFNULL(st.patient_last_name,'') 			AS stub_patient_last_name,
					IFNULL(st.patient_dob,'') 					AS stub_patient_dob,
					IFNULL(st.patient_sex,'') 					AS stub_patient_sex,
					IFNULL(st.patient_race,'') 					AS stub_patient_race,
					IFNULL(DATE_FORMAT(st.dos,'%m-%d-%Y'),'')	AS stub_dos_format,
					IFNULL(st.checked_in_time,'') 				AS checked_in_time,
					IFNULL(st.checked_out_time,'') 				AS checked_out_time,
					IFNULL(st.patient_status,'') 				AS patient_status,
					IFNULL(st.comment,'') 						AS comment,
					TRIM(CONCAT(st.surgeon_lname,', ',st.surgeon_fname,' ',st.surgeon_mname)) 	AS stub_surgeon_name,
					IFNULL(TRIM(CONCAT(u1.lname,', ',u1.fname,' ',u1.mname)),'') 				AS surgeon_name,
					IFNULL(TRIM(CONCAT(u2.lname,', ',u2.fname,' ',u2.mname)),'') 				AS crna,
					IFNULL(TRIM(CONCAT(u3.lname,', ',u3.fname,' ',u3.mname)),'') 				AS assistant_scrub_tec,
					IFNULL(TRIM(CONCAT(u4.lname,', ',u4.fname,' ',u4.mname)),'') 				AS circulating_nurse,
					IFNULL(pc.patientConfirmationId,'0') 		AS patientConfirmationId, 
					IFNULL(DATE_FORMAT(pc.dos,'%m-%d-%Y'),'')	AS dos_format,
					IFNULL(IF(ds.procedures_code !='',ds.procedures_code,''),'') AS discharge_procedures_ids,
					IFNULL(IF(ds.procedures_name !='',ds.procedures_name,''),'') AS discharge_procedures_name,
					IFNULL(IF(pc.patient_primary_procedure!='',pc.patient_primary_procedure,st.patient_primary_procedure),'') AS pt_patient_primary_procedure,
					IFNULL(ds.icd10_code,'') 					AS icd10_code, 
					IFNULL(GROUP_CONCAT(dx.icd10_desc),'') 		AS icd10_desc,
					IFNULL(pd.patient_id,'') 					AS pt_data_id, 
					IFNULL(pd.imwPatientId,'') 					AS imw_pt_data_id, 
					IFNULL(pd.patient_fname,'') 				AS patient_fname, 
					IFNULL(pd.patient_mname,'') 				AS patient_mname, 
					IFNULL(pd.patient_lname,'') 				AS patient_lname,
					IFNULL(pd.date_of_birth,'') 				AS date_of_birth, 
					IFNULL(pd.sex,'') 							AS sex, 
					IFNULL(pd.race,'') 							AS race,
					IFNULL(op.anesthesia_service,'') 			AS anesthesia_service, 
					IFNULL(op.TopicalBlock,'') 					AS TopicalBlock,
					IFNULL(op.complications,'') 				AS complications,
					IFNULL(tf.transfer_reason,'') 				AS transfer_reason,
					IFNULL(fc.fac_name,'') 						AS asc_name,
					IFNULL(tp.patient_id,'') 					AS tmp_pt_data_id
					FROM stub_tbl st 
					LEFT JOIN patientconfirmation pc on (pc.patientConfirmationId = st.patient_confirmation_id)
					LEFT JOIN patient_data_tbl pd ON (pd.patient_id = pc.patientId )
					LEFT JOIN operatingroomrecords op ON (op.confirmation_id = pc.patientConfirmationId )
					LEFT JOIN dischargesummarysheet ds ON (ds.confirmation_id = pc.patientConfirmationId )
					LEFT JOIN transfer_followups tf ON (tf.confirmation_id = pc.patientConfirmationId )
					LEFT JOIN icd10_data dx ON (find_in_set(dx.id, ds.icd10_id))
					LEFT JOIN users u1 ON (u1.usersId=pc.surgeonId)
					LEFT JOIN users u2 ON (u2.usersId=pc.anesthesiologist_id)
					LEFT JOIN users u3 ON (u3.usersId=op.scrubTechId1)
					LEFT JOIN users u4 ON (u4.usersId=op.nurseId)
					LEFT JOIN facility_tbl fc ON (fc.fac_idoc_link_id=st.iasc_facility_id AND fac_del_status = '0')
					LEFT JOIN patient_data_tbl tp ON (tp.imwPatientId = st.imwPatientId )
					WHERE  st.stub_id in(".$stubIdSet.")
					GROUP BY st.stub_id
					ORDER BY st.surgeon_lname, st.surgeon_fname, st.dos, st.surgery_time
					";//die($physician_report);

$physician=@imw_query($physician_report);
$rows=@imw_num_rows($physician);
$t=0;
$csv_content = '';
if($rows>0){
		while($rpt=imw_fetch_array($physician)) {
			$report_srgn[] = $rpt;
			if(!in_array($rpt['surgeon_name'],$physician_name_arr)) {
				$physician_name_arr[] = $rpt['surgeon_name'];
			}
		}
		$csv_content1 = $name;
		
		$a=1;
		$t++;
		if($t == '1') {
			$procedure_sel_csv	 = trim(str_ireplace("<br>","  ",$procedure));
			$user_name_csv 		 =	'"'.trim($user_name).'"';
			$csv_content1 		.= ','."".','.'"Surgery Log Book Report"'.','."".','."From ".$frm_date." To ".$to_date.','."".','.'Report Date '.$current_date.' ,'."\n";
		}
		
		$csv_content 			.= '"Seq"'.','.'"Date"'.','.'"Surgerycenter Patient Id"'.','.'"imwemr Patient Id"'.','.
									'"Patient First Name"'.','.'"Patient Middle Name"'.','.'"Patient Last Name"'.','.'"Age"'.','.'"Sex"'.','.
									'"Race"'.','.'"County"'.','.'"Surgeon"'.','.'"Assistant(ST)"'.','.'"Circulator(RN)"'.','.
									'"CRNA"'.','.'"Procedure"'.','.'"Diagnosis"'.','.'"Anesthesia"'.','.
									'"Time-In"'.','.'"Time-Out"'.','.'"Total"'.','.'"Surgery Cancellation Reason"'.','.'"Hospital Transfer"'.','.'"Complications"'.','.
									"\n";

		foreach($report_srgn as $report) {
			//START GET VALUES OF COLUMNS
			$patientConfirmationId		= $report['patientConfirmationId'];
			$dos_format 				= $report['dos_format'];
			$pt_data_id 				= $report['pt_data_id']; //SURGERYCENTER PATIENT ID
			$imw_pt_data_id 			= $report['imw_pt_data_id']; 
			$patient_fname				= trim(stripslashes($report['patient_fname']));
			$patient_mname				= trim(stripslashes($report['patient_mname']));
			$patient_lname				= trim(stripslashes($report['patient_lname']));
			$patient_dob				= trim(stripslashes($report['date_of_birth']));
			$patient_gender				= trim(stripslashes($report['sex']));
			$patient_race				= trim(stripslashes($report['race']));
			$surgeon_name				= trim(stripslashes($report['surgeon_name']));
			
			if($patientConfirmationId=='0') {
				$dos_format 			= $report['stub_dos_format'];
				$pt_data_id				= ($report['patient_id_stub']) ? $report['patient_id_stub'] : $report['tmp_pt_data_id'];
				$imw_pt_data_id			= $report['stub_imw_pt_data_id'];
				$patient_fname			= trim(stripslashes($report['stub_patient_first_name']));
				$patient_mname			= trim(stripslashes($report['stub_patient_middle_name']));
				$patient_lname			= trim(stripslashes($report['stub_patient_last_name']));
				$patient_dob			= trim(stripslashes($report['stub_patient_dob']));
				$patient_gender			= trim(stripslashes($report['stub_patient_sex']));
				$patient_race			= trim(stripslashes($report['stub_patient_race']));
				$surgeon_name			= trim(stripslashes($report['stub_surgeon_name']));
			}
			
			$patient_age				= $objManageData->dob_calc($patient_dob);
			$patient_gender_show		= $genderArr[strtolower($patient_gender)];
			$patient_race 				= str_ireplace(",",", ",$patient_race);
			$patient_county				= trim(stripslashes($ptCountyArr[$imw_pt_data_id]));
			
			
			$assistant_scrub_tec		= trim(stripslashes($report['assistant_scrub_tec']))== ',' ? '' : trim(stripslashes($report['assistant_scrub_tec']));
			$circulating_nurse			= trim(stripslashes($report['circulating_nurse'])) 	== ',' ? '' : trim(stripslashes($report['circulating_nurse']));
			$crna						= trim(stripslashes($report['crna'])) 				== ',' ? '' : trim(stripslashes($report['crna']));
			
			//START SELECT PROCEDURE ONLY RELATED TO CATEGORY OF PROCEDURE, LASER PROCEDURE, INJECTION AND MISCELLANEOUS PROCEDURE
			$pt_patient_primary_procedure= trim(stripslashes($report['pt_patient_primary_procedure']));
			$discharge_procedures_ids	= trim(stripslashes($report['discharge_procedures_ids']));
			$discharge_procedures_name	= trim(stripslashes($report['discharge_procedures_name']));
			$discharge_procedures_name_val 		= "";
			$discharge_procedures_main_arr 		= array();
			if($discharge_procedures_ids) {
				$discharge_procedures_ids_arr 	= explode(",",$discharge_procedures_ids);	
				$discharge_procedures_name_arr 	= explode("!,!",$discharge_procedures_name);
				foreach($discharge_procedures_ids_arr as $dp_ids_key =>$discharge_procedures_ids_val) {
					if(in_array($discharge_procedures_ids_val,$procedureIdArr))	{
						$discharge_procedures_main_arr[] = $discharge_procedures_name_arr[$dp_ids_key];	
					}
				}
				$discharge_procedures_name_val 	= implode(", ",$discharge_procedures_main_arr);
			}
			if(!$discharge_procedures_name_val) {
				$discharge_procedures_name_val 	= $pt_patient_primary_procedure;
			}
			//END SELECT PROCEDURE ONLY RELATED TO CATEGORY OF PROCEDURE, LASER PROCEDURE, INJECTION AND MISCELLANEOUS PROCEDURE
			
			//$discharge_procedures_name 	= str_ireplace("!,!",", ",$discharge_procedures_name);
			$icd10_desc					= trim(stripslashes($report['icd10_desc']));
			$icd10_desc 				= str_ireplace(",",", ",$icd10_desc);
			
			$anesthesia_service			= trim(stripslashes($report['anesthesia_service']));
			$TopicalBlock				= trim(stripslashes($report['TopicalBlock']));
			$anesthesia 				= ($TopicalBlock) ? $TopicalBlock : $anesthesia_service;
			$complications				= trim(stripslashes($report['complications']));
			
			$checked_in_time			= $objManageData->getTmFormat($report['checked_in_time']);
			$checked_out_time			= $objManageData->getTmFormat($report['checked_out_time']);
			$total_time 				= getConsumeTimeSlb($checked_in_time, $checked_out_time);
			
			$patient_status				= trim(stripslashes($report['patient_status']));
			$comment 					= ($patient_status	== 'Canceled' || $patient_status	== 'No Show' || $patient_status	== 'Aborted Surgery') ? trim(stripslashes($report['comment'])) : '';
			
			$transfer_reason			= trim(stripslashes($report['transfer_reason']));
			$hostpital_transfer			= ($transfer_reason) ? 'Yes' : '';
			
			$asc_name					= trim(stripslashes($report['asc_name']));
			//END GET VALUES OF COLUMNS

			//Set Nbsp in Seq & Name
			if($i<=$num){	
				//START CSV CODE
				$seq_csv 						=	'"'.trim($a).'"';
				$dos_format_csv 				=	'"'.trim($dos_format).'"';
				$dos_pt_data_id_csv				=	'"'.trim($pt_data_id).'"';
				$imw_pt_data_id_csv				=	'"'.trim($imw_pt_data_id).'"';
				$patient_fname_csv 				=	'"'.trim($patient_fname).'"';
				$patient_mname_csv 				=	'"'.trim($patient_mname).'"';
				$patient_lname_csv 				=	'"'.trim($patient_lname).'"';
				$patient_age_csv 				=	'"'.trim($patient_age).'"';
				$patient_gender_show_csv		=	'"'.trim($patient_gender_show).'"';
				$patient_race_csv 				=	'"'.trim($patient_race).'"';
				$patient_county_csv				=	'"'.trim($patient_county).'"';
				$surgeon_name_csv 				=	'"'.trim($surgeon_name).'"';
				$assistant_scrub_tec_csv		=	'"'.trim($assistant_scrub_tec).'"';
				$circulating_nurse_csv 			=	'"'.trim($circulating_nurse).'"';
				$crna_csv 						=	'"'.trim($crna).'"';
				
				$discharge_procedures_name_csv	=	'"'.trim($discharge_procedures_name_val).'"';
				$icd10_desc_csv 				=	'"'.trim($icd10_desc).'"';
				$anesthesia_csv 				=	'"'.trim($anesthesia).'"';
				$checked_in_time_csv 			=	'"'.trim($checked_in_time).'"';
				$checked_out_time_csv 			=	'"'.trim($checked_out_time).'"';
				$total_time_csv 				=	'"'.trim($total_time).'"';
				$comment_csv 					=	'"'.trim($comment).'"';
				$hostpital_transfer_csv 		=	'"'.trim($hostpital_transfer).'"';
				$complications_csv 				=	'"'.trim($complications).'"';
				
				$csv_content 				.= $seq_csv.','.$dos_format_csv.','.$dos_pt_data_id_csv.','.$imw_pt_data_id_csv.','.
											   $patient_fname_csv.','.$patient_mname_csv.','.$patient_lname_csv.','.$patient_age_csv.','.$patient_gender_show_csv.','.
											   $patient_race_csv.','.$patient_county_csv.','.$surgeon_name_csv.','.$assistant_scrub_tec_csv.','.$circulating_nurse_csv.','.
											   $crna_csv.','.$discharge_procedures_name_csv.','.$icd10_desc_csv.','.$anesthesia_csv.','.
											   $checked_in_time_csv.','.$checked_out_time_csv.','.$total_time_csv.','.$comment_csv.','.$hostpital_transfer_csv.','.$complications_csv.
											   "\n";		
				//END CSV CODE
			}
			$a++;
		}
	}
		
if($_REQUEST['hidd_report_format']=='csv' && imw_num_rows($physician)>0) {
	$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/surgery_log_book_print.csv';
	if(file_exists($file_name)) {
		@unlink($file_name);
	}
	$fpH1 = fopen($file_name,'w');
	fwrite($fpH1, $csv_content1."\n");
	fwrite($fpH1, $csv_content."\n\r");
	$objManageData->download_file($file_name);
	fclose($fpH1);
	exit;
}		


?>	
<!DOCTYPE html>
<html>
<head>
<title>Physician Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >   
<script language="javascript">
	window.focus();
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
if(@imw_num_rows($physician)<=0 && $_REQUEST['hidd_report_format']=='csv') {
?>
	<script type="text/javascript">
		location.href = "surgery_log_book.php?no_record=yes&date1=<?php echo $_REQUEST['startdate'];?>&date2=<?php echo $_REQUEST['enddate'];?>&procedure=<?php echo $_REQUEST['procedure'];?>&physician=<?php echo $physician_data_req;?>&status=<?php echo $_REQUEST['status'];?>&reportType=<?php echo $_REQUEST['reportType'];?>";
	</script>
<?php
}else if(@imw_num_rows($physician)>0){?>		
	<script type="text/javascript">
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
</html>

