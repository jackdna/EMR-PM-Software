<?php
include __DIR__ . "/sync_api_global.php";
require_once __DIR__ . '/../library/vendor_api/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPIOException;

$connection = new AMQPSSLConnection($inteHost, $intePort, $inteUser, $intePass, "/", $inteSSLOptions); //[host, port, user, pass, vHost, ssl_options]
$channel = $connection->channel();
/** Declare the queue if it does not exists */
//$channel->queue_declare($outboundQueue, false, $inteDurable, false, false);
$channel->exchange_declare($inteExchange, $inteExchangeType, false, $inteDurable, false); //[exchange, exchangeType, passive, durable, auto_delete]

include __DIR__ . '/../common/conDb.php';
$dt_frmt 	= date("Y_m_d");
$dtm_frmt 	= date('Y_m_d_H_i_s');
if(!trim($_SERVER['DOCUMENT_ROOT'])) {
	$_SERVER['DOCUMENT_ROOT'] = "/var/www/html";	
}
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
$pdf_dir = $rootServerPath."/".$iolinkDirectoryName."/admin/pdfFiles";	
$pdf_dir_ascemr_db = $rootServerPath."/".$surgeryCenterDirectoryName."/admin";
if(!is_dir($pdf_dir."/sync_api_log")) {
	mkdir($pdf_dir."/sync_api_log", 0775);
}
$logFolderPath = $pdf_dir."/sync_api_log/date_".$dt_frmt;
if(!is_dir($logFolderPath)) {
	mkdir($logFolderPath, 0775);
}	
if(!is_dir($pdf_dir."/sync_api_log/error_log")){
	mkdir($pdf_dir."/sync_api_log/error_log", 0775);
}
$error_log_dir = $pdf_dir."/sync_api_log/error_log";
//START GET ARRAY OF MODIFIERS
$modQry = "SELECT * FROM modifiers ORDER BY modifierId ASC";
$modRes = imw_query($modQry)  or $error_result_charges[] = ($modQry.imw_error());
if(imw_num_rows($modRes)>0) {
	while($modRow = imw_fetch_array($modRes)) {
		$adm_mod_prac_code = $modRow['practiceCode'];
		$adm_mod_description = $modRow['description'];
		$adm_mod_arr[$adm_mod_prac_code] = $adm_mod_description;
	}
}
//END GET ARRAY OF MODIFIERS

$cpt_group_arr = array("1"=>"Anesthesia", "2"=>"Practice", "3"=>"Facility");
$qry = "SELECT sb.superbill_id, sb.bill_user_type, sb.cpt_code, sb.quantity, sb.modifier1, sb.modifier2, sb.modifier3, sb.dxcode_icd10, 
		ds.dischargeSummarySheetId, ds.confirmation_id, ds.procedures_name, ds.procedures_code_name, ds.icd10_code, ds.icd10_name, ds.comment, 
		wt.patient_in_waiting_id, wt.idoc_sch_athena_id, pc.dos
		FROM superbill_tbl sb
		INNER JOIN dischargesummarysheet ds ON(ds.confirmation_id = sb.confirmation_id AND ds.form_status = 'completed' AND ds.cpt_inte_sync_status = '0' AND ds.cpt_inte_sync_flag = '1') 
		INNER JOIN patientconfirmation pc ON(pc.patientConfirmationId = sb.confirmation_id) 
		INNER JOIN stub_tbl st ON(st.patient_confirmation_id = pc.patientConfirmationId AND st.dos = pc.dos) 
		INNER JOIN patient_in_waiting_tbl wt ON(wt.patient_in_waiting_id = st.iolink_patient_in_waiting_id AND wt.idoc_sch_athena_id!='')
		WHERE sb.deleted = '0' AND sb.bill_user_type IN(2,3)
		GROUP BY sb.superbill_id
		ORDER BY pc.dos ASC, pc.patientConfirmationId ASC, sb.bill_user_type ASC";

$res = imw_query($qry)  or $error_result_charges[] = ($qry.imw_error());
$data = array();
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_array($res)) {
		$dischargeSummarySheetId 	= $row["dischargeSummarySheetId"];
		$confirmation_id 			= $row["confirmation_id"];
		$bill_user_type 			= $row["bill_user_type"];
		$cpt_code					= $row["cpt_code"];
		$dxcode_icd10				= $row["dxcode_icd10"];
		$dxcode_icd10_arr			= explode(",",$dxcode_icd10);
		$quantity 					= $row["quantity"];
		$modifier1 					= $row["modifier1"];
		$modifier2 					= $row["modifier2"];
		$modifier3 					= $row["modifier3"];
		$procedures_name 			= $row["procedures_name"];
		$procedures_name_arr 		= explode("!,!",$procedures_name);
		$procedures_code_name 		= $row["procedures_code_name"];
		$procedures_code_name_arr 	= explode("##",$procedures_code_name);
		$icd10_code 				= $row["icd10_code"];
		$icd10_code_arr 			= explode(",",$icd10_code);
		$icd10_name 				= $row["icd10_name"];
		$icd10_name_arr 			= explode("@@",$icd10_name);
		$comment 					= $row["comment"];
		$dos 						= $row["dos"];
		$inte_appt_id 				= $row["idoc_sch_athena_id"];
		$iasclink_appt_id			= $row["patient_in_waiting_id"];
		
		$pcn_arr = array();
		foreach($procedures_code_name_arr as $pcn_key => $pcn_val) {
			$pcn_arr[$pcn_val] = $procedures_name_arr[$pcn_key];
		}
		$icd_arr = array();
		foreach($icd10_code_arr as $icd_key => $icd_val) {
			$icd_arr[$icd_val] = $icd10_name_arr[$icd_key];
		}
		$cpt_code_name = $pcn_arr[$cpt_code];
		
		$dxVal = array();
		
		foreach($dxcode_icd10_arr as $dxicd_val) {
			$dxentry = array();
			$dxentry['DiagnosticType'] = 'ICD10';
			$dxentry['DiagnosticCode'] = $dxicd_val;
			$dxentry['DiagnosticDescription'] = $icd_arr[$dxicd_val];
			array_push($dxVal, $dxentry);
		}
		$cpt_group = $cpt_group_arr[$bill_user_type];
		
		if($tmp_confirmation_id != $confirmation_id) {
			
			$entry["Charges"] = array("CptCodes"=>array());
			
			$chargePointer = &$entry["Charges"]["CptCodes"];
			$chargePointer = array();

			$entry["ExternalId"]				= $inte_appt_id;
			$entry["DOS"]						= $dos;
			$entry["iasclink_appt_id"]			= $iasclink_appt_id;
			$entry["charges_confirmation_id"]	= $confirmation_id;
			$entry["charges_id_primary"]		= $dischargeSummarySheetId;
			$entry["Comments"]					= $comment;
			
			array_push($data, $entry);
			
			$updtChargesQry = "UPDATE dischargesummarysheet SET cpt_inte_sync_status = '1', cpt_inte_sync_date_time = '".date("Y-m-d H:i:s")."' WHERE confirmation_id = '".$confirmation_id."' ";
			$updtChargesRes = imw_query($updtChargesQry) or $error_result_charges[] = ($updtChargesQry.imw_error());
		}

		$modifier_arr = array();

		$dxentry = array();
		$dxentry['DiagnosticType'] = 'ICD10';
		$dxentry['DiagnosticCode'] = $dxicd_val;
		$dxentry['DiagnosticDescription'] = 'ICD10';
		$dxentry['DiagnosticType'] = $icd_arr[$dxicd_val];
		
		$modVal = array();
		if(trim($modifier1)) {
			$modentry =array();
			$modentry["modifier"] = $modifier1;
			$modentry["description"] = $adm_mod_arr[$modifier1];
			array_push($modVal, $modentry);
		}
		if(trim($modifier2)) {
			$modentry =array();
			$modentry["modifier"] = $modifier2;
			$modentry["description"] = $adm_mod_arr[$modifier2];
			array_push($modVal, $modentry);
		}
		if(trim($modifier3)) {
			$modentry =array();
			$modentry["modifier"] = $modifier3;
			$modentry["description"] = $adm_mod_arr[$modifier3];
			array_push($modVal, $modentry);
		}
		
		$chargeEntry = array();
		$chargeEntry['CptGroup'] = $cpt_group;
		$chargeEntry['CptCode'] = $cpt_code;
		$chargeEntry['Units'] = $quantity;
		$chargeEntry['modifiers'] = $modVal;
		$chargeEntry['DiagnosticCodes'] = $dxVal;
		
		array_push($chargePointer, $chargeEntry);
		
		$tmp_confirmation_id = $confirmation_id;
	}
}

$messageDataJSon = ""; 
$messageData = array();
if(count($data)>0) {
	foreach( $data as $messageData )
	{
		$messageData = array('api_charges_data'=>$messageData);
		$messageDataJSon = json_encode($messageData);
		file_put_contents($logFolderPath.'/api_charges_data_send_'.$dt_frmt.'.txt', date("Y-m-d H:i:s")." \n".$messageDataJSon, FILE_APPEND);	
		file_put_contents($logFolderPath.'/api_charges_data_send_'.$dt_frmt.'.txt', "\n============================\n", FILE_APPEND);

		$msg = new AMQPMessage($messageDataJSon);
		$channel->basic_publish($msg, $inteExchange,$inteOutboundRoutingKey); //[msg, exchange, routing_key]
		//$channel->basic_publish($msg, '', $outboundQueue); //[msg, exchange, routing_key]
		echo ' [x] Sent '.$showMsg, $messageDataJSon, "\n";

	}
}else {
	echo ' [x] Sent No Charges(CPT) Found', '', "\n";
}
if(count($error_result_charges)>0) {
	$errorOutputCharges = print_r($error_result_charges, true);
	if(!trim($error_log_dir)) { $error_log_dir = $pdf_dir; }
	file_put_contents($error_log_dir.'/api_charges_data_error'.$dt_frmt.'.txt', date("Y-m-d H:i:s")." \n".$errorOutputCharges, FILE_APPEND);
	file_put_contents($error_log_dir.'/api_charges_data_error'.$dt_frmt.'.txt', "\n============================\n", FILE_APPEND);
}
$channel->close();
$connection->close();

?>
