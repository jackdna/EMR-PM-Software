<?php
set_time_limit(300);
$_SESSION['callFrom']="medicationTab";
include_once('../../../config/globals.php'); 
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../../library/classes/common_function.php");
$pid = $_SESSION['patient'];
$save = new SaveFile($pid);
$upload_path = $save->upDir.$save->pDir.'/';

$page_title = "Export CSV Vital Sign";
$filename="vs_csv.csv";
$pfx=",";
$car_aux = "\r\n";

if(($recno=="") || ($recno==1)){
	$recno   =1;
	$csvtext ="";
	$csvtext.="Date".$pfx;
	$csvtext.="B/P - Systolic".$pfx;
	$csvtext.="B/P - Diastolic".$pfx;
	$csvtext.="Pulse".$pfx;
	$csvtext.="Respiration".$pfx;
	$csvtext.="O2Sat".$pfx;
	$csvtext.="Temp".$pfx;
	$csvtext.="Height".$pfx;
	$csvtext.="Weight".$pfx;
	$csvtext.="BMI".$pfx;
	$csvtext.="Inhale O2".$car_aux;
	$fp=fopen($upload_path.$filename,"w");
	fwrite($fp,$csvtext);
	fclose($fp);
}

$sql = "select date_vital,time_vital,id from vital_sign_master
  		where 
		patient_id='".$pid."' and status='0' order by date_vital desc ";
$rez =@imw_query($sql);				
$num_rows=@imw_num_rows($rez);				
while($row=imw_fetch_array($rez)){
	//$dat=explode('-',$row['date_vital']);
	//$dat_final=$dat[1].'-'.$dat[2].'-'.$dat[0];
	$dat_final = get_date_format($row['date_vital']);
	$tim=$row['time_vital'];
	$mas_id=$row['id'];
	
	$csvtext.='"'.$dat_final.'"';
	$csvtext.=$pfx;

		$pat_rec=imw_query("select range_vital,unit,range_status,vital_sign_id from vital_sign_patient 
								where 
							vital_master_id='$mas_id'");
		while($vt_rec=imw_fetch_array($pat_rec)){
			$unt="";
			if($vt_rec['range_vital']){
				$unt=$vt_rec['unit'] ? ' ('.html_entity_decode($vt_rec['unit']).')' : '';
			}
		$csvtext.='"'.$vt_rec['range_vital'].$unt.'"';
		if($vt_rec['vital_sign_id']!=9){
			$csvtext.=$pfx;
		}	
	}
	$csvtext.=$car_aux;
}
$fp=fopen($upload_path.$filename,"w+");
if(fwrite($fp,$csvtext)) {
	fclose($fp);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer");
	
	header("Content-Type: application/octet-stream;");
	
	header("Content-disposition:attachment; filename=\"".$filename."\"");
	
	header("Content-Length: ".@filesize($upload_path.$filename));
	
	@readfile($upload_path.$filename) or die("File not found.");
	unlink($upload_path.$filename);
	exit;
}	
//echo $dispMsg;
?>
