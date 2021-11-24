<?php

$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
//$argv[1] = 'imwemr';

if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}

//===========PRIMARY FILES===============================
require_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../interface/chart_notes/chart_globals.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
include_once(dirname(__FILE__)."/../../interface/patient_info/complete_pt_rec/print_functions_class.php");
//include_once(dirname(__FILE__)."/../../library/classes/work_view/wv_functions.php");
$library_path = $GLOBALS['webroot'].'/library';

if(!constant("PTVISIT_ORU_GENERATION_FILEPATH")) die('Required configuration not done.');

//error_reporting(-1);
//ini_set("display_errors",-1);
set_time_limit(0);

if(empty($GLOBALS["LOCAL_SERVER"])) die('Global LOCAL_SERVER Name Not Defined.');


$today	= date('Y-m-d');

/*****SELECT INITIAL POPULATION OF PATIENT FROM TODAY'S APPEARED PATIENTS******/

$main_q = "SELECT csl.id, csl.patient_id, csl.form_id, 	csl.user_id, csl.logged_user_type, pd.External_MRN_2 FROM `chart_save_log` csl
			JOIN patient_data pd ON (pd.id = csl.patient_id) 
			WHERE pd.id <> 0 AND pd.pid <> 0
			AND csl.finalized=1 AND csl.pdf_exported=0"; 
			//echo $main_q; die;
$res = imw_query($main_q);echo imw_error();
if($res && imw_num_rows($res)>0){
	$UPLOAD_FILES = array();
	while($rs = imw_fetch_assoc($res)){
		
		$LogId		=	$rs['id'];
		$pid		=	$rs['patient_id'];
		$chartNoteId=	$rs['form_id'];
		$user_id	=	$rs['user_id'];
		$user_type	=	$rs['logged_user_type'];
		$MRNId		=	$rs['External_MRN_2'];
		
		if(!empty($pid) && !empty($chartNoteId) && !empty($LogId)){// if patient id and form id and primary id is available, only then proceed.
			
			$cpr = New CmnFunc($pid);  			//CALLING FROM print_functions_class.php
			$pid = $cpr->patient_id;
			$_SESSION['patient'] = $pid;  		//PATIENT ID SET IN SESSION- SOME FILES ARE USING SESSION PATIENT ID
			$form_id = $chartNoteId;
			
			$_GET = array();
			$_REQUEST = array();
			$_REQUEST['chart_nopro'] = array('Chart Notes','Medical History');  //SET VALUE TO PRINT THE CHART NOTE DATA
			$_REQUEST['formIdToPrint'] = array($form_id);  
			
			
			$reportName="Visit Notes";
			$lenFIds=count($_REQUEST["formIdToPrint"]);

			$fdr_pat_img=$GLOBALS['fileroot']."/interface/patient_access/patient_photos/";

			ob_start();
			
			echo "<page backtop=\"5mm\" backbottom=\"5mm\">";

			include(dirname(__FILE__)."/../../interface/patient_info/complete_pt_rec/visionPrintWithNotes_1.php");
			//Set timeout
			//set_time_limit(10);
 
			$zFormId=$chartNoteId;	
			$arrDosToPrint = $cpr->print_getDosfromId(array($zFormId));  //GET DOS BY FormIds 
			$strDosToPrint1 = "'".implode("', '", $arrDosToPrint)."'";		
			$dt_of_visit = str_ireplace("'","",$strDosToPrint1);
			$pdfNameDos =  str_ireplace("-","",$dt_of_visit);
			//print_r($strDosToPrint1); die;
			
			//GET ACCOUNT NO. FROM BELOW HL7 TABLE
			$acc_q ="SELECT account_num FROM `hl7_received_accno` 
					WHERE patient_id='".$pid."' AND dt_of_visit='".$dt_of_visit."' ORDER BY id DESC LIMIT 0,1";
			//echo $acc_q; die;
			$res_q = imw_query($acc_q);echo imw_error();
			if($res_q && imw_num_rows($res_q)>0){
				$rs_q = imw_fetch_assoc($res_q);
				$account = $rs_q['account_num'];
				
				//========PATIENT CHART DATA FUNCTIONS CALLING FROM BELOW FILE===============		
				include($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/visionPrintWithNotes.php");
				
				//Add Empty Page ---
				if($key<$lenFIds-1){ //do not add at end
					echo "<div style=\"height:100%;border:0px solid red;\"></div>";				
				}
				//Add Empty Page ---

				include($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/other_print.php");
				//Medical History & ROS Data
				//include($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/getMedicalHistoryPrint.php");
				echo "</page>";
				$patient_workprint_data = ob_get_contents();

				ob_end_clean();

				$headDataRR = $patient_workprint_data;
				//$headDataRR =  str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$headDataRR);
				$fileName = write_html($headDataRR);   //WRITING HTML
				
				$ChartNoteImagesStringFinal=implode(",",$ChartNoteImagesString);
				
				//==PDF WORK RE-STARTS FROM HERE================
				$pdfFileName = $MRNId.'_'.$account.'_Clinic Note Report_'.$pdfNameDos;  //PDF FILE NAME
				$pathInfo = pathinfo($fileName);
				$fileDir = (is_dir($pathInfo['dirname']) === true) ? constant("PTVISIT_ORU_GENERATION_FILEPATH").$pdfFileName.'.pdf' : '';

				$params = array(
					'page'=>'1.3', 
					'op'=>'P', 
					'font_size'=>'7.5', 
					'saveOption'=>'F', 
					'name'=>$fileName, 
					'file_location'=>$fileName, 
					'pdf_name'=>$fileDir, 
					//'htmlFileName'=>'pdffile', 
					'images'=>$ChartNoteImagesStringFinal 
				);
				
				//CURL WORK STARTS HERE
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php?setIgnoreAuth=true');
				curl_setopt($ch, CURLOPT_POST, true);	/*RESET HTTP METHOD TO GET*/
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /*RETURN THE RESPONSE*/
				curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP); /*SET PROTOCOL TO HTTP IF DEFAULT CHANGED*/
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_HEADER, false); /*INCLUDE HEADER IN OUTPUT RESPONSE*/
				$data = curl_exec($ch); /*$DATA WILL HOLD DATA*/
				curl_close($ch); /*CURL CONNECTION CLOSE */
				
				$UPLOAD_FILES[$LogId]['file'] = $fileDir;//).'<br>'; //PRINT CREATED FILES NAME 
				$UPLOAD_FILES[$LogId]['logId']= $LogId;
				
				
			}else{
				ob_end_clean();
				echo 'No ACCNO found.<br>';
			}
		}
		else
		{
			die('Patient Id and Form Id does not exist.');
		}
	}
	send_to_sftp($UPLOAD_FILES);
}
else
{
	die('No record exists.');
}

function send_to_sftp($UPLOAD_FILES){
	//pre($UPLOAD_FILES,1);
	/* Add the correct FTP credentials below */
	include('Net/SFTP.php');
	$sftp = new Net_SFTP('hpmcsftp.hpmedcenter.com',22,1000);
	if (!$sftp->login('imwemr-ftp@imwemr','!3Y3wicn@w'))
	{
		exit('Login Failed');
	}else{
		echo 'Login Success.<br>';
	}
	
  /* Upload the local file to the remote server 
	 put('remote file', 'local file');
   */
   
   foreach($UPLOAD_FILES as $UPLOAD_FILE){
	if(file_exists($UPLOAD_FILE['file'])){
			echo" Tring to upload(".end(explode('/',$UPLOAD_FILE['file'])).") file ".$UPLOAD_FILE['file'].'<br/>';
			$success =$sftp->put('/data/LIVE/'.end(explode('/',$UPLOAD_FILE['file'])), $UPLOAD_FILE['file'], NET_SFTP_LOCAL_FILE);
			echo "upload :".$success.'<br>';
			if($success){
				rename($UPLOAD_FILE['file'],$UPLOAD_FILE['file'].'.done');//move that file to archieve folder
				//CHART SAVE LOG UPDATE PDF EXPORT STATUS
					$updateQry = "UPDATE `chart_save_log` SET pdf_exported=1, export_date_time='".date('Y-m-d H:i:s')."' WHERE id='".$UPLOAD_FILE['logId']."'";
					$exeQry = imw_query($updateQry);
			}else{
				var_dump($success);
			} 
		}
   }	
}
?>