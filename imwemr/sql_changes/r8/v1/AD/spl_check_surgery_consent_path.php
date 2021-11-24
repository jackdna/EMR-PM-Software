<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$updir=substr(data_path(), 0, -1);
$csvErrorLog=$updir."/iOLink/surgery_consent_data_not_exists.csv";
if(file_exists($csvErrorLog)) {
	unlink($csvErrorLog);
}
function dwnldFile($filename) {
	//$filename=$_REQUEST['fn'];
	$fileArr=explode('/',$filename);
	$fileSoloName=$fileArr[sizeof($fileArr)-1];
	$content_type = "application/force-download";
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer");
	header("Content-Type: ".$content_type."; charset=utf-8");
	header("Content-disposition:attachment; filename=\"".$fileSoloName."\"");
	header("Content-Length: ".@filesize($filename));
	@readfile($filename) or die("File not found.");
	exit;
}
function csv_error_log($arr_error_log,$reason,$csvErrorLog){
	$csv_file_content="";
	if(is_array($arr_error_log)){
		$arr_error_log = preg_replace('/'.$pattern.'/',$replace,$arr_error_log);
		$csv_file_content='"'.implode('","',$arr_error_log).'"';
		$cvsDataInsert=$csv_file_content.",".$reason."\n";
		$fp = fopen($csvErrorLog,"a");
		if($fp){
			fwrite($fp,$cvsDataInsert); 
			fclose($fp);
		}
	}
}



$qry = "SELECT scff.surgery_consent_id,scff.patient_id,scff.appt_id,sa.sa_app_start_date as DOS,scff.surgery_consent_name, scff.form_created_date as created_date,scff.iolink_pdf_path 
FROM schedule_appointments sa
INNER JOIN surgery_consent_filled_form scff ON ( scff.patient_id = sa.sa_patient_id
AND scff.appt_id = sa.id
AND scff.movedToTrash =  '0' ) 
WHERE sa.iolinkPatientWtId !=  '0' AND sa.sa_app_start_date >='2018-10-01' 
ORDER BY  scff.patient_id, `scff`.`form_created_date` ASC ";
$res=imw_query($qry);
//file_put_contents('/var/www/html/shoreline/data/shoreline/iOLink/PatientId_167716/test1.txt','ID,Patient_ID, Appointment_ID,DOS,surgery_consent_name,created_date,iolink_path \n');
$fieldArr = array("ID","Patient_ID","Appointment_ID","DOS","surgery_consent_name","created_date","iolink_path"); 
if(imw_num_rows($res)>0){
	$a=0;
	while($row =imw_fetch_assoc($res)) {
		$surgery_consent_id  	= $row["surgery_consent_id"];
		$patient_id  			= $row["patient_id"];
		$appt_id  				= $row["appt_id"];
		$DOS  					= $row["DOS"];
		$surgery_consent_name  	= $row["surgery_consent_name"];
		$created_date  			= $row["created_date"];
		$iolink_pdf_path  		= urldecode($row["iolink_pdf_path"]);
		if(!file_exists($iolink_pdf_path) && $iolink_pdf_path) {
			$a++;
			//file_put_contents('/var/www/html/shoreline/data/shoreline/iOLink/PatientId_167716/test1.txt','<br>'.$surgery_consent_id.','.$patient_id.','.$appt_id.','.$DOS.','.$surgery_consent_name.','.$created_date.','.$iolink_pdf_path.'\n',FILE_APPEND);
			if($a==1) { csv_error_log($fieldArr,"Reason",$csvErrorLog);}
			csv_error_log($row,"Path not exist",$csvErrorLog);
			
		}
	}
}

if(file_exists($csvErrorLog)) {
	dwnldFile($csvErrorLog);
	$msg_info[] = "<br><b>Release :<br> Update Success.</b>";
}else {
	$msg_info[] = "<br><b><br>Run Successfully. No Record Found.</b>";	
}



$color = "green";	

?>
<html>
<head>
<title>Update Surgery Consent Path Retreived</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>