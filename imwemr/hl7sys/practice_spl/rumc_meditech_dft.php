<?php
set_time_limit(0);
$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
$argv[1] = 'rumc';
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}
require_once(dirname(__FILE__)."/../../config/globals.php");
function LogResponse($text){
	echo date('Y-m-d H:i:s').': '.$text.'<br>';
}
//include_once(dirname(__FILE__)."/../sender/commonFunctions.php");
//include_once(dirname(__FILE__)."/../hl7GP/hl7Create.php");
/*
error_reporting(-1);
ini_set("display_errors",-1);
die('good');
*/

$today = date('Y-m-d');
/***********GET THE SUPERBILL IDs FOR WHICH DFT ALREADY CREATED FOR MMRI********/
$base_res = imw_query("SELECT GROUP_CONCAT(DISTINCT(superbill_id)) as superbill_ids FROM hl7_sent WHERE superbill_id > 0 AND DATE_FORMAT(saved_on,'%Y-%m-%d') = '".$today."' AND (sent='1' OR sftp_sent='1')");
if(!$base_res || imw_num_rows($base_res)==0){
	LogResponse('MEDITECH INTERFACE: No superbill ID pending to send. '.imw_error().'. ');
	die;	
}else if($base_res && imw_num_rows($base_res)==1){
	$base_rs = imw_fetch_assoc($base_res);
	$superbill_ids = $base_rs['superbill_ids'];
	if(empty($superbill_ids)){
		LogResponse('MEDITECH INTERFACE: No superbill ID pending to send. '.imw_error().'. ');
		die;
	}
	LogResponse('MEDITECH INTERFACE: Superbill IDs pending to send are: '.$superbill_ids.'. ');
	if(!empty($superbill_ids) && constant('SUP_DFT_GENERATION')==true){
		require_once( dirname(__FILE__).'/../../hl7sys/old/CLS_makeHL7.php');
		$superbill_id_arr = explode(',',$superbill_ids);
		$main_msg_str = 'FHS|^~\&'.chr(10);
		$main_msg_str .= 'BHS|^~\&|||||||'.date('Ymd').'|'.chr(10);
		$dft_msg_text = '';
		foreach($superbill_id_arr as $superbill_id){
			$main_q = "SELECT idSuperBill, patientId, physicianId, dateOfService, sch_app_id FROM superbill WHERE del_status = '0' AND idSuperBill = '$superbill_id' LIMIT 0,1";
			$main_res = imw_query($main_q);
			if(!$main_res || imw_num_rows($main_res)==0){
				LogResponse('MEDITECH INTERFACE: No record found for superbill ID '.$superbill_id.'. ');
				die;
			}else{
				$makeHL7 = new makeHL7;
				$main_rs = imw_fetch_assoc($main_res);
				$superBillID = $main_rs['idSuperBill'];
				$makeHL7->patient_id = $main_rs['patientId'];	
				$makeHL7->segmentEnding	= chr(10);		
				$msg = $makeHL7->makeDFT4MediTech($superBillID);
				$dft_msg_text .= $msg;				
			}	   
		}
		$main_msg_str .= $dft_msg_text;	
		//echo '<pre>'.$main_msg_str;die;
		if(!empty($main_msg_str)){
			LogResponse('MEDITECH INTERFACE: HL7 message formed, creating file. ');
		}else{
			LogResponse('MEDITECH INTERFACE: HL7 message NOT formed. ');
			die;
		}
		
		$archiveDir 		= $webserver_root.'/data/'.PRACTICE_PATH.'/HL7_OUTBOUND/meditech';
		$new_file_name		= date('YmdHis').'.txt';
		if(!is_dir($archiveDir)){
			@mkdir($archiveDir,0777,true);
			chmod($archiveDir,777);
		}
		if(is_dir($archiveDir)){
			file_put_contents($archiveDir.'/'.$new_file_name,$main_msg_str);
			if(file_exists($archiveDir.'/'.$new_file_name)){
				LogResponse('MEDITECH INTERFACE: New file written with name: '.$new_file_name.'. ');
				$ftp_server = "172.22.100.99";
				$ftp_conn = ftp_connect($ftp_server) or LogResponse("Could not connect to $ftp_server");
				$login = ftp_login($ftp_conn, 'imbill', '1Richmond!');
				if($login){
					$switchDir = 'ImBill'; //'Imedicware_Billing'
					ftp_chdir($ftp_conn,$switchDir); // changing to directory after login.	
					$pwd = ftp_pwd($ftp_conn);
					if(stristr($pwd,$switchDir)){
						LogResponse('MEDITECH INTERFACE: Switched to directory "'.$switchDir.'". ');
						if(ftp_put($ftp_conn, $new_file_name, $archiveDir.'/'.$new_file_name, FTP_ASCII)){
							LogResponse('MEDITECH INTERFACE: File successfully uploaded to FTP. ');
						}else{
							LogResponse('MEDITECH INTERFACE: File upload failed. ');
						}
					}else{
						LogResponse('MEDITECH INTERFACE: Not able to switch to directory "'.$switchDir.'". ');
					}
				}else{
					LogResponse('MEDITECH INTERFACE: Login failed to FTP. ');
				}
				@ftp_close($ftp_conn);
			}
		}		
	}else{
		LogResponse('MEDITECH INTERFACE: SUP_DFT_GENERATION not enabled. ');
		die;
	}
	
}

die();


// connect and login to FTP server
$ftp_server = "172.22.100.99";
$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
$login = ftp_login($ftp_conn, 'imwemr', '1Richmond!');
var_dump($login);echo '<hr>';
$file = "imw2meditech_demo_01.txt";

// upload file
if (ftp_put($ftp_conn, "imw2meditech_demo_01.txt", $file, FTP_ASCII))
  {
  echo "Successfully uploaded $file.";
  }
else
  {
  echo "Error uploading $file.";
  }

// close connection
ftp_close($ftp_conn);
?>