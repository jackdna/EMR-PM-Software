<?php
set_time_limit(0);
$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
//$argv[1] = 'imwemr-Dev';
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}

//===========PRIMARY FILES===============================
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../sender/class.ClientSocket.php");
include_once(dirname(__FILE__)."/interface/chart_notes/chart_globals.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
include_once(dirname(__FILE__).'/../../interface/patient_info/complete_pt_rec/print_functions_class.php');
$library_path = $GLOBALS['webroot'].'/library';
error_reporting(0);
ini_set("display_errors",0);
//===========CONFIG DEFINE GLOBAL PARAMETERS=============
if((constant("PTVISIT_ORU_GENERATION")) && constant("PTVISIT_ORU_GENERATION_FILEPATH")!='') 
{ 
	// AND sent='1' 
	$sqlQry = "SELECT id, msg, patient_id, order_id FROM `hl7_sent` WHERE msg_type='PTVISIT_ORU' AND order_id != '' AND txt_file_created='0'";
	//echo $sqlQry; die;
	$exeQry = imw_query($sqlQry);
	if(imw_num_rows($exeQry)>0){
		while($resQry = imw_fetch_assoc($exeQry)){
			$hl7SentId 	= $resQry['id'];
			$HL7msg		= $resQry['msg']; 
			$pid 		= $resQry['patient_id'];
			$chartNoteId = $resQry['order_id'];  //PATIENT FORM OR CHART ID
		
			$cpr = New CmnFunc($pid);  			//CALLING FROM print_functions_class.php
			$pid = $cpr->patient_id;
			$_SESSION['patient'] = $pid;  		//PATIENT ID SET IN SESSION- SOME FILES ARE USING SESSION PATIENT ID
			$form_id = $chartNoteId;
			
			$_GET = array();
			$_REQUEST = array();
			$_REQUEST['chart_nopro'] = array('Chart Notes');  //SET VALUE TO PRINT THE CHART NOTE DATA
			$_REQUEST['formIdToPrint'] = array($chartNoteId);  
			
			$reportName="Visit Notes";
			$lenFIds=count($_REQUEST["formIdToPrint"]);

			$fdr_pat_img=$GLOBALS['fileroot']."/interface/patient_access/patient_photos/";

			ob_start();
			
			echo "<page backtop=\"5mm\" backbottom=\"5mm\">";

			include(dirname(__FILE__)."/../../interface/patient_info/complete_pt_rec/visionPrintWithNotes_1.php");
			//Set timeout
			set_time_limit(10);
 
			$zFormId=$chartNoteId;	
			$arrDosToPrint = $cpr->print_getDosfromId(array($zFormId));  //GET DOS BY FormIds 
			$strDosToPrint1 = "'".implode("', '", $arrDosToPrint)."'";		
			
			//========PATIENT CHART DATA FUNCTIONS CALLING FROM BELOW FILE===============		
			include($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/visionPrintWithNotes.php");
			
			//Add Empty Page ---
			if($key<$lenFIds-1){ //do not add at end
				echo "<div style=\"height:100%;border:0px solid red;\"></div>";				
			}
			//Add Empty Page ---

			include($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/other_print.php");
			
			echo "</page>";
			$patient_workprint_data = ob_get_contents();

			ob_end_clean();

			$headDataRR = $patient_workprint_data;
			$headDataRR =  str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$headDataRR);
			$fileName = write_html($headDataRR);   //WRITING HTML
			
			$ChartNoteImagesStringFinal= isset($ChartNoteImagesString) ? implode(",",$ChartNoteImagesString) : '';

			$pdfName = 'pt'.$pid.'frm'.$chartNoteId;  //PDF FILE NAME
			$pathInfo = pathinfo($fileName);
			$fileDir = (is_dir($pathInfo['dirname']) === true) ? constant("PTVISIT_ORU_GENERATION_FILEPATH").$pdfName.'.pdf' : '';

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
			curl_setopt($ch, CURLOPT_URL, 'https://neecimwapp.tuftsmedicalcenter.org/imwemr/library/html_to_pdf/createPdf.php?setIgnoreAuth=true');
			curl_setopt($ch, CURLOPT_POST, true);	/*RESET HTTP METHOD TO GET*/
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /*RETURN THE RESPONSE*/
			curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP); /*SET PROTOCOL TO HTTP IF DEFAULT CHANGED*/
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false); /*INCLUDE HEADER IN OUTPUT RESPONSE*/
			$data = curl_exec($ch); /*$DATA WILL HOLD DATA*/
			curl_close($ch); /*CURL CONNECTION CLOSE */
			
			//print($fileDir).'<br>'; //PRINT CREATED FILES NAME 
			
			if(file_exists($fileDir))
			{	
				$sqlQry2= "UPDATE `hl7_sent` SET txt_file_created='1', status_text=concat('PDF created: ".date('Y-m-d H:i:s').". ',status_text) WHERE id='".$hl7SentId."' AND txt_file_created='0'";
				$exeQry2 = imw_query($sqlQry2);
				/*try {
					if(isset($sc)){unset($sc);}
					$tcp_destination = $GLOBALS['HL7_SENDER_ARRAY']['PTVISITORU'];
					$sc = new ClientSocket();
					$sc->iReadTimeOut = $tcp_destination['ACK_WAIT'];
					$sc->open($tcp_destination['IP'],$tcp_destination['PORT']);
					$HL7msg     = chr(11).$HL7msg.chr(28).chr(13);
					$length 	= strlen($HL7msg);
					$sc->send($HL7msg);
					try {
					  $ack 		= $sc->recv();
					}
					catch(Exception $e) {
					  echo 'Message: ' .$e->getMessage();
					}
					
					if($ack!=''){
						imw_query("UPDATE `hl7_sent` SET sent=1, sent_on='".date('Y-m-d H:i:s')."', response='".addslashes($ack)."' WHERE id='".$hl7SentId."' AND sent='0'");
					}
				}catch (Exception $e){
					var_dump($e);die;
					echo ($e->getMessage());
				}*/
			}
			
					
		}	//WHILE CONDITION CLOSED.
		
	}  //hl7_sent NUM ROWS CONDITION CLOSED.
	
	$sqlQry1 = "SELECT id, msg FROM `hl7_sent` WHERE msg_type='PTVISIT_ORU' AND order_id != '' AND txt_file_created='1' AND sent!= '1' ORDER BY id";
	$exeQry1 = imw_query($sqlQry1);
	if(imw_num_rows($exeQry1)>0){

		if(isset($sc)){unset($sc);}
		$tcp_destination = $GLOBALS['HL7_SENDER_ARRAY']['PTVISITORU'];
		$sc = new ClientSocket();
		$sc->iReadTimeOut = $tcp_destination['ACK_WAIT'];
		$sc->open($tcp_destination['IP'],$tcp_destination['PORT']);

		while($resQry = imw_fetch_assoc($exeQry1)){
			$hl7SentId 	= $resQry['id'];
			$HL7msg		= $resQry['msg']; 
			try {
				$HL7msg     = chr(11).$HL7msg.chr(28).chr(13);
				$length 	= strlen($HL7msg);
				$sc->send($HL7msg);
				try {
				  $ack 		= $sc->recv();
				}
				catch(Exception $e) {
				  echo 'Message: ' .$e->getMessage();
				}
				
				if($ack!=''){
					imw_query("UPDATE `hl7_sent` SET sent=1, sent_on='".date('Y-m-d H:i:s')."', response='".addslashes($ack)."' WHERE id='".$hl7SentId."' AND sent='0'");
				}
			}catch (Exception $e){
				var_dump($e);die;
				echo ($e->getMessage());
			}
		}
	}
	
}
else
{
	die("ERROR:- PLEASE CHECK GLOBAL CONFIG PARAMETERS");
}
?>